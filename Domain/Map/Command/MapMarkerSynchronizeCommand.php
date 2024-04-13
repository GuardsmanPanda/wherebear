<?php declare(strict_types=1);

namespace Domain\Map\Command;

use Domain\Map\Crud\MapMarkerCreator;
use Domain\Map\Model\MapMarker;
use GuardsmanPanda\Larabear\Infrastructure\Console\Service\BearTransactionCommand;
use Illuminate\Support\Str;

final class MapMarkerSynchronizeCommand extends BearTransactionCommand {
    protected $signature = 'map:marker-synchronize';
    protected $description = 'Synchronize map markers with the database';

    public function handleInTransaction(): void {
        $this->info(string: 'Synchronizing map markers.');
        $count = 0;
        foreach(scandir(directory: 'public/static/img/map-marker') as $file) {
            if ($file === '.' || $file === '..') {
                continue;
            }
            $count++;
            $marker = MapMarker::find(id: $file);
            if ($marker === null) {
                $this->info(string: "Creating marker $file.");
                $name = str_replace(search: ['.png', '.webp'], replace: '', subject: $file);
                MapMarkerCreator::create(
                    file_name: $file,
                    map_marker_name: Str::studly(value: $name),
                    map_marker_group: 'Miscellaneous',
                    height_rem: 4,
                    width_rem: 4
                );
            }
        }
        $this->info(string: "Synchronized $count markers.");
    }
}
