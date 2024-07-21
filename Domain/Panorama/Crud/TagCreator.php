<?php declare(strict_types=1);

namespace Domain\Panorama\Crud;

use Domain\Panorama\Enum\TagEnum;
use Domain\Panorama\Model\Tag;
use GuardsmanPanda\Larabear\Infrastructure\Database\Service\BearDatabaseService;

final class TagCreator {
    public static function create(TagEnum $tag_enum,): Tag {
        BearDatabaseService::mustBeInTransaction();
        BearDatabaseService::mustBeProperHttpMethod(verbs: ['POST']);

        $model = new Tag();

        $model->enum = $tag_enum->value;
        $model->description = $tag_enum->getDescription();

        $model->save();
        return $model;
    }
}
