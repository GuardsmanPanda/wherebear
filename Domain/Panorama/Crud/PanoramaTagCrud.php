<?php declare(strict_types=1);

namespace Domain\Panorama\Crud;

use Domain\Panorama\Enum\PanoramaTagEnum;
use Domain\Panorama\Model\Tag;
use GuardsmanPanda\Larabear\Infrastructure\Database\Service\BearDatabaseService;

final class PanoramaTagCrud {
    public static function syncToDatabase(PanoramaTagEnum $tag_enum): Tag {
        BearDatabaseService::mustBeInTransaction();
        BearDatabaseService::mustBeProperHttpMethod(verbs: ['POST']);

        $model = Tag::find($tag_enum->value) ?? new Tag();
        $model->enum = $tag_enum->value;
        $model->description = $tag_enum->getDescription();

        $model->save();
        return $model;
    }
}
