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
        $model->name = $enum->getName();
        $model->external_api_id = $enum->getExternalApi()->getId();
        $model->external_path = $enum->getExternalPath();
        $model->user_level_requirement = $enum->getUserLevelRequirement();

        $model->save();
        return $model;
    }
}
