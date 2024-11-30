<?php declare(strict_types=1);

namespace Domain\Import\Command;

use Domain\Panorama\Crud\PanoramaUpdater;
use Domain\Panorama\Model\Panorama;
use GuardsmanPanda\Larabear\Infrastructure\Integrity\Service\ValidateAndParseValue;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use RuntimeException;
use Throwable;

final class ImportPanoramaJpgCommand extends Command {
  protected $signature = 'import:panorama-jpg';
  protected $description = 'Import all Panorama files.';

  public function handle(): void {
    foreach (Storage::allFiles(directory: 'panorama-imports') as $file) {
      $info = explode(separator: 'Ø', string: pathinfo($file)['filename']);
      $id = $info[0];
      $north_rotation_degrees = ValidateAndParseValue::parseInt(value: $info[1]);
      $panorama = Panorama::findOrFail(id: $id);
      if ($panorama->jpg_path !== null) {
        $this->setNorthRotationDegrees(panorama: $panorama, north_rotation_degrees: $north_rotation_degrees);
      } else {
        $this->importPanorama(fileName: $file, panorama: $panorama, north_rotation_degrees: $north_rotation_degrees);
      }
      Storage::delete(paths: $file);
    }
  }


  private function importPanorama(string $fileName, Panorama $panorama, int $north_rotation_degrees): void {
    $content = Storage::get(path: $fileName);
    if ($content === null) {
      throw new RuntimeException(message: "Failed to read panorama file $fileName.");
    }
    try {
      DB::beginTransaction();
      $yearFolder = $panorama->captured_date->format(format: 'Y');
      $monthFolder = $panorama->captured_date->format(format: 'm');
      $newFileName = "$yearFolder/$monthFolder/" . Str::random(length: 32) . '.jpg';
      new PanoramaUpdater(model: $panorama)
        ->setNorthRotationDegrees(north_rotation_degrees: $north_rotation_degrees)
        ->setJpgPath(jpg_path: $newFileName)
        ->update();
      Storage::put(path: 'panorama/' . $newFileName, contents: $content);
      DB::commit();
    } catch (Throwable $e) {
      DB::rollBack();
      throw new RuntimeException(message: "Failed to import panorama with ID $panorama->id. [{$e->getMessage()}]", previous: $e);
    }
  }


  private function setNorthRotationDegrees(Panorama $panorama, int $north_rotation_degrees): void {
    try {
      DB::beginTransaction();
      new PanoramaUpdater(model: $panorama)
        ->setNorthRotationDegrees(north_rotation_degrees: $north_rotation_degrees)
        ->update();
      DB::commit();
    } catch (Throwable $e) {
      DB::rollBack();
      throw new RuntimeException(message: "Failed to set north rotation degrees for panorama with ID $panorama->id. [{$e->getMessage()}]", previous: $e);
    }
  }
}
