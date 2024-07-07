<?php declare(strict_types=1);

namespace Domain\Panorama\Crud;

use Domain\Panorama\Model\Tag;
use GuardsmanPanda\Larabear\Infrastructure\Database\Service\BearDatabaseService;

final class TagDeleter {
    public static function delete(Tag $model): void {
        BearDatabaseService::mustBeInTransaction();
        BearDatabaseService::mustBeProperHttpMethod(verbs: ['DELETE']);
        $model->delete();
    }

    public static function deleteFromTagEnum(string $tag_enum): void {
        self::delete(model: Tag::findOrFail(id: $tag_enum));
    }
}
