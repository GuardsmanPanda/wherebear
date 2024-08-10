<?php declare(strict_types=1);

namespace Integration\Nominatim\Client;

use GuardsmanPanda\Larabear\Integration\ExternalApi\Client\BearExternalApiClient;
use Infrastructure\App\Enum\BearExternalApiEnum;
use Integration\Nominatim\Data\NominatimLocationData;

final class NominatimClient {
    public static function reverseLookup(float $latitude, float $longitude): NominatimLocationData {
        $client = BearExternalApiClient::fromEnum(enum: BearExternalApiEnum::NOMINATIM);
        $response = $client->request(path: "reverse", query: [
            'format' => 'jsonv2',
            'lat' => sprintf("%.15f", $latitude),
            'lon' => sprintf("%.15f", $longitude),
        ]);
        return NominatimLocationData::fromResponse(response: $response);
    }
}
