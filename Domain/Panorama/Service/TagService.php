<?php declare(strict_types=1);

namespace Domain\Panorama\Service;

use Domain\Panorama\Model\Tag;

final class TagService {
    public static function tagExists(string $tag_enum): bool {
        return Tag::find(id: $tag_enum, columns: ['tag_enum']) !== null;
    }
}
