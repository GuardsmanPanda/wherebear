<?php declare(strict_types=1);

namespace Domain\Import\Command;

use Domain\Import\Crud\ImportStreetviewsOrgCrud;
use GuardsmanPanda\Larabear\Infrastructure\Console\Service\BearTransactionCommand;
use Illuminate\Support\Facades\Http;

final class ImportStreetviewsOrgCommand extends BearTransactionCommand {
  protected $signature = 'import:streetviews-org';
  protected $description = 'Import data from streetviews.org';

  protected function handleInTransaction(): void {
    $offset = 0;
    $data = Http::get(url: "https://streetviews.org/_i/?offset=$offset")->json();
    while ($data['count'] > 0) {
      ImportStreetviewsOrgCrud::createOrUpdateFromData(data: $data['views']);
      $offset += 5;
      $data = Http::get(url: "https://streetviews.org/_i/?offset=$offset")->json();
      dump($offset);
    }
  }
}
