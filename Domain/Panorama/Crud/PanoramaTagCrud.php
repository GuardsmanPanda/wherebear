<?php declare(strict_types=1);

namespace Domain\Panorama\Crud;

use Domain\Panorama\Enum\PanoramaTagEnum;
use Domain\Panorama\Model\PanoramaTag;
use GuardsmanPanda\Larabear\Infrastructure\Database\Service\BearDatabaseService;

final class PanoramaTagCrud {
    public static function syncToDatabase(PanoramaTagEnum $tag_enum): PanoramaTag {
        BearDatabaseService::mustBeInTransaction();
        BearDatabaseService::mustBeProperHttpMethod(verbs: ['POST']);

        $model = PanoramaTag::find($tag_enum->value) ?? new PanoramaTag();
        $model->enum = $tag_enum->value;
        $model->description = $tag_enum->getDescription();

        $model->save();
        return $model;
    }
}
