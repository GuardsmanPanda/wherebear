<?php declare(strict_types=1);

namespace Domain\Panorama\Crud;

use Domain\Panorama\Enum\TagEnum;
use Domain\Panorama\Model\Tag;
use GuardsmanPanda\Larabear\Infrastructure\Database\Service\BearDatabaseService;

final class TagCrud {
    public static function syncToDatabase(TagEnum $tag_enum): Tag {
        BearDatabaseService::mustBeInTransaction();
        BearDatabaseService::mustBeProperHttpMethod(verbs: ['POST']);

        $model = Tag::find($tag_enum->value) ?? new Tag();
        $model->enum = $tag_enum->value;
        $model->description = $tag_enum->getDescription();

        $model->save();
        return $model;
    }
}
