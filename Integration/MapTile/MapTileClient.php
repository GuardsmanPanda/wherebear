<?php declare(strict_types=1);

namespace Integration\MapTile;

use Domain\Map\Enum\MapStyleEnum;
use Domain\Map\Model\MapStyle;
use GuardsmanPanda\Larabear\Integration\ExternalApi\Client\BearExternalApiClient;

final class MapTileClient {
    public static function getMapTile(MapStyleEnum $mapStyle, String $z, String $x, String $y): string {
        $mapStyle = MapStyle::with(['externalApi'])->findOrfail($mapStyle->value);
        $url = str_replace(search: array('{z}', '{x}', '{y}'), replace:  array($z, $x, $y), subject: $mapStyle->map_style_url);
        $client = BearExternalApiClient::fromExternalApi($mapStyle->externalApi);
        return $client->request($url)->body();
    }
}
