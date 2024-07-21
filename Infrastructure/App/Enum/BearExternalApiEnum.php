<?php declare(strict_types=1);

namespace Infrastructure\App\Enum;

use GuardsmanPanda\Larabear\Integration\ExternalApi\Crud\BearExternalApiCreator;
use GuardsmanPanda\Larabear\Integration\ExternalApi\Enum\BearExternalApiTypeEnum;
use GuardsmanPanda\Larabear\Integration\ExternalApi\Model\BearExternalApi;
use Illuminate\Database\Eloquent\Casts\ArrayObject;

enum BearExternalApiEnum: string {
    case OPENSTREETMAP = 'OPENSTREETMAP';
    case NOMINATIM = 'NOMINATIM';
    case GOOGLE_MAP_TILES_API = 'GOOGLE_MAP_TILES_API';
    case GOOGLE_STREET_VIEW_STATIC_API = 'GOOGLE_STREET_VIEW_STATIC_API';
    case MAPBOX = 'MAPBOX';

    public function getDescription(): string {
        return match ($this) {
            self::OPENSTREETMAP => 'OpenStreetMap used for the default map tiles',
            self::NOMINATIM => 'Nominatim used for reverse location lookup',
            self::GOOGLE_MAP_TILES_API => 'Google Maps used for map tiles',
            self::GOOGLE_STREET_VIEW_STATIC_API => 'Google Street View used for street view images',
            self::MAPBOX => 'Mapbox used for map tiles',
        };
    }

    public function getType(): BearExternalApiTypeEnum {
        return match ($this) {
            self::OPENSTREETMAP, self::NOMINATIM => BearExternalApiTypeEnum::NO_AUTH,
            self::GOOGLE_MAP_TILES_API, self::GOOGLE_STREET_VIEW_STATIC_API => BearExternalApiTypeEnum::KEY_QUERY,
            self::MAPBOX => BearExternalApiTypeEnum::ACCESS_TOKEN_QUERY,
        };
    }

    public function getBaseUrl(): string {
        return match ($this) {
            self::OPENSTREETMAP => 'https://tile.openstreetmap.org/',
            self::NOMINATIM => 'https://nominatim.openstreetmap.org/',
            self::GOOGLE_MAP_TILES_API => 'https://tile.googleapis.com/',
            self::GOOGLE_STREET_VIEW_STATIC_API => 'https://maps.googleapis.com/maps/api/streetview/',
            self::MAPBOX => 'https://api.mapbox.com/',
        };
    }

    /**
     * @return ArrayObject<string, string>
     */
    public function getBaseHeaders(): ArrayObject {
        return match ($this) {
            self::NOMINATIM => new ArrayObject([
                'User-Agent' => 'WhereBear (guardsmanpanda@gmail.com)'
            ]),
            default => new ArrayObject([]),
        };
    }


    public static function syncToDatabase(): void {
        foreach (BearExternalApiEnum::cases() as $api) {
            if (BearExternalApi::find(id: $api->value) === null) {
                BearExternalApiCreator::create(
                    enum: $api->value,
                    description: $api->getDescription(),
                    external_api_type: $api->getType(),
                    base_url: $api->getBaseUrl(),
                    base_headers_json: $api->getBaseHeaders(),
                );
            }
        }
    }
}
