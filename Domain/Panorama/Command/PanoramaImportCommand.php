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
        foreach (Storage::allFiles(directory: 'panorama') as $file) {
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
        $newFileName = Str::random(length: 32) . '.jpg';
        try {
            DB::beginTransaction();
            PanoramaUpdater::fromId(id: $id)
                ->setJpgName(jpg_name: $newFileName)
                ->update();
            Storage::move(from: $fileName, to: 'panorama-jpg/' . $newFileName);
            DB::commit();
        } catch (Throwable $e) {
            DB::rollBack();
            throw new RuntimeException(message: "Failed to import panorama with ID $id. [{$e->getMessage()}]", previous: $e);
        }
    }
}
