<?php declare(strict_types=1);

namespace Domain\Panorama\Crud;

use Domain\Panorama\Model\Tag;
use GuardsmanPanda\Larabear\Infrastructure\Database\Service\BearDatabaseService;

final class TagCreator {
    public static function create(
        string $tag_enum,
        string $tag_description
    ): Tag {
        BearDatabaseService::mustBeInTransaction();
        BearDatabaseService::mustBeProperHttpMethod(verbs: ['POST']);

        $model = new Tag();

        $model->tag_enum = $tag_enum;
        $model->tag_description = $tag_description;

        $model->save();
        return $model;
    }
}
