<?php declare(strict_types=1);

namespace Domain\Panorama\Command;

use Domain\Panorama\Crud\PanoramaUpdater;
use Domain\Panorama\Model\Panorama;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use RuntimeException;
use Throwable;

final class PanoramaImportCommand extends Command {
    protected $signature = 'panorama:import';
    protected $description = 'Import all Panorama files.';

    public function handle(): void {
        foreach (Storage::allFiles(directory: 'panorama-imports') as $file) {
            $id = pathinfo($file)['filename'];
            $panorama = Panorama::find(id: $id);
            if ($panorama !== null) {
                $this->importPanorama(fileName: $file, id: $id, panorama: $panorama);
            } else {
                throw new RuntimeException(message: "Panorama with ID $id not found.");
            }
        }
    }


    private function importPanorama(string $fileName, string $id, Panorama $panorama): void {
        $content = Storage::get(path: $fileName);
        if ($content === null) {
            throw new RuntimeException(message: "Failed to read panorama file $fileName.");
        }
        try {
            DB::beginTransaction();
            $yearFolder = $panorama->captured_date->format(format: 'Y');
            $monthFolder = $panorama->captured_date->format(format: 'm');
            $newFileName = "$yearFolder/$monthFolder/" . Str::random(length: 32) . '.jpg';
            PanoramaUpdater::fromId(id: $id)
                ->setJpgPath(jpg_path: $newFileName)
                ->update();
            Storage::put(path: 'panorama/' . $newFileName, contents: $content);
            Storage::delete(paths: $fileName);
            DB::commit();
        } catch (Throwable $e) {
            DB::rollBack();
            throw new RuntimeException(message: "Failed to import panorama with ID $id. [{$e->getMessage()}]", previous: $e);
        }
    }
}
