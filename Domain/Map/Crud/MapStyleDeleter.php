<?php declare(strict_types=1);

namespace Domain\Map\Crud;

use Domain\Map\Model\MapStyle;
use GuardsmanPanda\Larabear\Infrastructure\Database\Service\BearDatabaseService;

final class MapStyleDeleter {
    public static function delete(MapStyle $model): void {
        BearDatabaseService::mustBeInTransaction();
        BearDatabaseService::mustBeProperHttpMethod(verbs: ['DELETE']);
        $model->delete();
    }

    public static function deleteFromMapStyleEnum(string $map_style_enum): void {
        self::delete(model: MapStyle::findOrFail(id: $map_style_enum));
    }
}
