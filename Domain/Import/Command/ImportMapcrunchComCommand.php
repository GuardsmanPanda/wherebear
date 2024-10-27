<?php declare(strict_types=1);

namespace Domain\Import\Command;

use Domain\Import\Crud\ImportMapcrunchComCrud;
use GuardsmanPanda\Larabear\Infrastructure\Console\Service\BearTransactionCommand;
use Illuminate\Support\Facades\Http;
use JsonException;

final class ImportMapcrunchComCommand extends BearTransactionCommand {
  protected $signature = "import:mapcrunch-com";
  protected $description = "Import data from mapcrunch.com";

  protected function handleInTransaction(): void {
    $offset = 0;
    $data = $this->getData(offset: $offset);
    while (count(value: $data) > 0 && $offset < 200_000) {
      if (count(value: $data) !== 20) {
        $this->warn(string: "Data count is not 20, for offset $offset");
      }
      ImportMapcrunchComCrud::createOrUpdateFromData(data: $data);
      $offset += 20;
      try {
        $data = $this->getData(offset: $offset);
      } catch (JsonException $e) {
        $this->error(string: "Failed to parse JSON: {$e->getMessage()}, for offset $offset");
      }
      if ($offset % 200 === 0) {
        $this->info(string: "Offset: $offset");
      }
    }
  }


  /**
   * @param int $offset
   * @return array<array-key, array<string, string|null>>
   */
  private function getData(int $offset): array {
    $data = Http::get(url: "https://www.mapcrunch.com/gallery?offset=$offset")->body();
    // For each line
    $lines = explode(separator: "\n", string: $data);

    foreach ($lines as $line) {
      // If the line contains the string "data-panoid"
      if (str_starts_with(haystack: $line, needle: "   var pics = '")) {
        $jsonText = substr(string: $line, offset: 15, length: str_ends_with(haystack: $line, needle: "';") ? -2 : null);
        return json_decode(json: $jsonText, associative: true, flags: JSON_THROW_ON_ERROR);
      }
    }
    $this->error(string: "Could not find data-panoid in the response, for offset $offset");
    return [];
  }
}
