<?php declare(strict_types=1);

namespace Domain\Panorama\Command;

use Domain\Panorama\Model\Panorama;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

final class PanoramaImportCommand extends Command {
    protected $signature = 'panorama:import';
    protected $description = 'Import all Panorama files.';

    public function handle(): void {
        foreach (Storage::allFiles(directory: 'panorama') as $file) {
            $id = pathinfo($file)['filename'];
            $panorama = Panorama::findOrFail(id: $id);
            dd($file, $id, $panorama);
        }
    }
}