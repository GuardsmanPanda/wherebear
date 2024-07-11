<?php declare(strict_types=1);

namespace Infrastructure\Database\Initialize;

use GuardsmanPanda\Larabear\Integration\ExternalApi\Crud\BearExternalApiCreator;
use GuardsmanPanda\Larabear\Integration\ExternalApi\Enum\BearExternalApiTypeEnum;
use GuardsmanPanda\Larabear\Integration\ExternalApi\Service\BearExternalApiService;
use Illuminate\Database\Eloquent\Casts\ArrayObject;

final class DatabaseInitializeExternalApi  {
    public static function initialize(): void {
        $external_api = [
            [
                'id' => 'e9f8e665-ca90-4f3d-b7f4-d9a811eb4754',
                'external_api_slug' => 'openstreetmap',
                'external_api_description' => 'OpenStreetMap used for the default map tiles',
                'external_api_type' => BearExternalApiTypeEnum::NO_AUTH,
                'external_api_base_url' => 'https://tile.openstreetmap.org/'
            ],
            [
                'id' => '384be8aa-a197-425f-80c3-e63f398525d6',
                'external_api_slug' => 'nominatim',
                'external_api_description' => 'Nominatim used for reverse location lookup',
                'external_api_type' => BearExternalApiTypeEnum::NO_AUTH,
                'external_api_base_url' => 'https://nominatim.openstreetmap.org/',
                'external_api_base_headers_json' => new ArrayObject([
                    'User-Agent' => 'WhereBear (guardsmanpanda@gmail.com)'
                ])
            ],
            [
                'id' => 'ce41bedb-0879-4e03-b5ce-c9473515524e',
                'external_api_slug' => 'google-map-tiles-api',
                'external_api_description' => 'Google Maps used for map tiles',
                'external_api_type' => BearExternalApiTypeEnum::X_GOOG_API_KEY,
                'external_api_base_url' => 'https://tile.googleapis.com/'
            ],
            [
                'id' => '29494542-70bf-49c2-885b-e405ce8ca492',
                'external_api_slug' => 'google-street-view-static-api',
                'external_api_description' => 'Google Street View used for street view images',
                'external_api_type' => BearExternalApiTypeEnum::X_GOOG_API_KEY,
                'external_api_base_url' => 'https://maps.googleapis.com/maps/api/streetview/'
            ],
        ];

        foreach ($external_api as $api) {
            if (BearExternalApiService::externalApiExists(id: $api['id'])) {
                continue;
            }
            BearExternalApiCreator::create(
                external_api_slug: $api['external_api_slug'],
                external_api_description: $api['external_api_description'],
                external_api_type: $api['external_api_type'],
                id: $api['id'],
                external_api_base_url: $api['external_api_base_url'],
                external_api_base_headers_json: $api['external_api_base_headers_json'] ?? new ArrayObject([]),
            );
        }
    }
}
