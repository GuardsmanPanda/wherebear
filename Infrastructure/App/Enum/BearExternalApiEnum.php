<?php declare(strict_types=1);

namespace Infrastructure\App\Enum;

use GuardsmanPanda\Larabear\Integration\ExternalApi\Crud\BearExternalApiCreator;
use GuardsmanPanda\Larabear\Integration\ExternalApi\Enum\BearExternalApiTypeEnum;
use GuardsmanPanda\Larabear\Integration\ExternalApi\Service\BearExternalApiService;
use Illuminate\Database\Eloquent\Casts\ArrayObject;

enum BearExternalApiEnum: string {
    case OPENSTREETMAP = 'openstreetmap';
    case NOMINATIM = 'nominatim';
    case GOOGLE_MAP_TILES_API = 'google-map-tiles-api';
    case GOOGLE_STREET_VIEW_STATIC_API = 'google-street-view-static-api';
    case MAPBOX = 'mapbox';

    public function getId(): string {
        return match ($this) {
            self::OPENSTREETMAP => 'e9f8e665-ca90-4f3d-b7f4-d9a811eb4754',
            self::NOMINATIM => '384be8aa-a197-425f-80c3-e63f398525d6',
            self::GOOGLE_MAP_TILES_API => 'ce41bedb-0879-4e03-b5ce-c9473515524e',
            self::GOOGLE_STREET_VIEW_STATIC_API => '29494542-70bf-49c2-885b-e405ce8ca492',
            self::MAPBOX => '9b73e5be-0c0a-4078-8ce6-9478c909113c',
        };
    }

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
            self::GOOGLE_MAP_TILES_API, self::GOOGLE_STREET_VIEW_STATIC_API => BearExternalApiTypeEnum::X_GOOG_API_KEY,
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

    public function getHeaders(): ArrayObject {
        return match ($this) {
            self::NOMINATIM => new ArrayObject([
                'User-Agent' => 'WhereBear (guardsmanpanda@gmail.com)'
            ]),
            default => new ArrayObject([]),
        };
    }


    public static function syncToDatabase(): void {
        foreach (BearExternalApiEnum::cases() as $api) {
            if (BearExternalApiService::externalApiExists(id: $api->getId())) {
                continue;
            }
            BearExternalApiCreator::create(
                external_api_slug: $api->value,
                external_api_description: $api->getDescription(),
                external_api_type: $api->getType(),
                id: $api->getId(),
                external_api_base_url: $api->getBaseUrl(),
                external_api_base_headers_json: $api->getHeaders(),
            );
        }
    }
}
