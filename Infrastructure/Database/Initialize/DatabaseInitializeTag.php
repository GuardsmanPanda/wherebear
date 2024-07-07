<?php declare(strict_types=1);

namespace Infrastructure\Database\Initialize;

use Domain\Panorama\Crud\TagCreator;
use Domain\Panorama\Enum\TagEnum;
use Domain\Panorama\Service\TagService;

final class DatabaseInitializeTag {
    public static function initialize(): void {
        foreach (TagEnum::cases() as $enum) {
            if (TagService::tagExists(tag_enum: $enum->value)) {
                continue;
            }
            TagCreator::create(
                tag_enum: $enum->value,
                tag_description: $enum->description()
            );
        }
    }
}
