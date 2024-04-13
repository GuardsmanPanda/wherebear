<?php declare(strict_types=1);

namespace Domain\Map\Crud;

use Domain\Map\Model\MapStyle;
use GuardsmanPanda\Larabear\Infrastructure\Database\Service\BearDatabaseService;

final class MapStyleCreator {
    public static function create(
        string $map_style_enum,
        string $map_style_name,
        string $map_style_url,
        string $external_api_id
    ): MapStyle {
        BearDatabaseService::mustBeInTransaction();
        BearDatabaseService::mustBeProperHttpMethod(verbs: ['POST']);

        $model = new MapStyle();

        $model->map_style_enum = $map_style_enum;
        $model->map_style_name = $map_style_name;
        $model->map_style_url = $map_style_url;
        $model->external_api_id = $external_api_id;

        $model->save();
        return $model;
    }
}
