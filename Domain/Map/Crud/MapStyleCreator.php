<?php declare(strict_types=1);

namespace Domain\Map\Crud;

use Domain\Map\Enum\MapStyleEnum;
use Domain\Map\Model\MapStyle;
use GuardsmanPanda\Larabear\Infrastructure\Database\Service\BearDatabaseService;

final class MapStyleCreator {
    public static function create(MapStyleEnum $enum): MapStyle {
        BearDatabaseService::mustBeInTransaction();
        BearDatabaseService::mustBeProperHttpMethod(verbs: ['POST']);

        $model = new MapStyle();

        $model->map_style_enum = $enum->value;
        $model->map_style_name = $enum->getMapStyleName();
        $model->map_style_url = $enum->getRemoteSystemPath();
        $model->external_api_id = $enum->getExternalApi()->getId();

        $model->save();
        return $model;
    }
}
