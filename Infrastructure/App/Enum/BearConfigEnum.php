<?php declare(strict_types=1);

namespace Infrastructure\App\Enum;

use Carbon\CarbonInterface;
use GuardsmanPanda\Larabear\Infrastructure\Config\Crud\BearConfigCreator;
use GuardsmanPanda\Larabear\Infrastructure\Config\Interface\BearConfigEnumInterface;
use GuardsmanPanda\Larabear\Infrastructure\Config\Model\BearConfig;

enum BearConfigEnum: string implements BearConfigEnumInterface {
    case MAP_BOX_API_REQUESTS = 'MAP_BOX_API_REQUESTS';

    public function getValue(): string {
        return $this->value;
    }

    public function getDescription(): string {
        return match ($this) {
            self::MAP_BOX_API_REQUESTS => 'The number of requests made to the MapBox API',
        };
    }

    public function getDefaultConfigString(): string|null {
        return null;
    }

    public function getDefaultConfigInteger(): int|null {
        return match ($this) {
            self::MAP_BOX_API_REQUESTS => 0,
        };
    }

    public function getDefaultConfigBoolean(): bool|null {
        return null;
    }

    public function getDefaultConfigDate(): CarbonInterface|null {
        return null;
    }

    public function getDefaultConfigTimestamp(): CarbonInterface|null {
        return null;
    }


    public function getModel(): BearConfig {
        return BearConfig::findOrFail($this->value);
    }

    public static function syncToDatabase(): void {
        foreach (self::cases() as $enum) {
            if (BearConfig::find(id: $enum->value) === null) {
                BearConfigCreator::create(enum: $enum);
            }
        }
    }
}
