<?php declare(strict_types=1);

namespace Infrastructure\App\Enum;

use GuardsmanPanda\Larabear\Integration\ExternalApi\Crud\BearExternalApiCreator;
use GuardsmanPanda\Larabear\Integration\ExternalApi\Crud\BearExternalApiCrud;
use GuardsmanPanda\Larabear\Integration\ExternalApi\Enum\BearExternalApiAuthEnum;
use GuardsmanPanda\Larabear\Integration\ExternalApi\Interface\BearExternalApiEnumInterface;
use GuardsmanPanda\Larabear\Integration\ExternalApi\Model\BearExternalApi;
use Illuminate\Database\Eloquent\Casts\ArrayObject;

enum BearExternalApiEnum: string implements BearExternalApiEnumInterface {
    case OPENSTREETMAP = 'OPENSTREETMAP';
    case NOMINATIM = 'NOMINATIM';
    case GOOGLE_MAP_TILES_API = 'GOOGLE_MAP_TILES_API';
    case GOOGLE_STREET_VIEW_STATIC_API = 'GOOGLE_STREET_VIEW_STATIC_API';
    case MAPBOX = 'MAPBOX';

    public function getValue(): string {
        return $this->value;
    }


    public function description(): string {
        return match ($this) {
            self::OPENSTREETMAP => 'OpenStreetMap used for the default map tiles',
            self::NOMINATIM => 'Nominatim used for reverse location lookup',
            self::GOOGLE_MAP_TILES_API => 'Google Maps used for map tiles',
            self::GOOGLE_STREET_VIEW_STATIC_API => 'Google Street View used for street view images',
            self::MAPBOX => 'Mapbox used for map tiles',
        };
    }


    public function baseUrl(): string {
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
    public function baseHeadersJson(): ArrayObject {
        return match ($this) {
            self::NOMINATIM => new ArrayObject([
                'User-Agent' => 'WhereBear (guardsmanpanda@gmail.com)'
            ]),
            default => new ArrayObject([]),
        };
    }


    public function externalApiAuth(): BearExternalApiAuthEnum {
        return match ($this) {
            self::OPENSTREETMAP, self::NOMINATIM => BearExternalApiAuthEnum::NO_AUTH,
            self::GOOGLE_MAP_TILES_API, self::GOOGLE_STREET_VIEW_STATIC_API => BearExternalApiAuthEnum::QUERY_KEY,
            self::MAPBOX => BearExternalApiAuthEnum::QUERY_ACCESS_TOKEN,
        };
    }


    public function oauth2ClientId(): string|null {
        return null;
    }

    public function metadataJson(): ArrayObject|null {
        return null;
    }


    public function getModel(): BearExternalApi {
        return BearExternalApi::findOrFail(id: $this->value);
    }


    public static function syncToDatabase(): void {
        foreach (BearExternalApiEnum::cases() as $enum) {
            BearExternalApiCrud::syncToDatabase(enum: $enum);
        }
    }
}
