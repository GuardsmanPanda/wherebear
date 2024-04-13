<?php declare(strict_types=1);

namespace Domain\Map\Crud;

use Domain\Map\Model\MapMarker;
use GuardsmanPanda\Larabear\Infrastructure\Database\Service\BearDatabaseService;

final class MapMarkerCreator {
    public static function create(
        string $file_name,
        string $map_marker_name,
        string $map_marker_group,
        int $height_rem,
        int $width_rem
    ): MapMarker {
        BearDatabaseService::mustBeInTransaction();
        BearDatabaseService::mustBeProperHttpMethod(verbs: ['POST']);

        $model = new MapMarker();

        $model->file_name = $file_name;
        $model->map_marker_name = $map_marker_name;
        $model->map_marker_group = $map_marker_group;
        $model->height_rem = $height_rem;
        $model->width_rem = $width_rem;

        $model->save();
        return $model;
    }
}
