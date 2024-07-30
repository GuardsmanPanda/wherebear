<?php declare(strict_types=1);

namespace Integration\MapTile;

use Domain\Map\Enum\MapStyleEnum;
use Domain\Map\Model\MapStyle;
use GuardsmanPanda\Larabear\Integration\ExternalApi\Client\BearExternalApiClient;
use Illuminate\Support\Facades\DB;

final class MapTileClient {
    public static function getMapTile(MapStyleEnum $mapStyle, String $z, String $x, String $y): string {
        DB::update(query: "UPDATE bear_config SET config_integer = config_integer + 1 WHERE enum = 'MAP_BOX_API_REQUESTS';");

        $mapStyle = MapStyle::with(['externalApi'])->findOrFail($mapStyle->value);
        $url = str_replace(search: array('{z}', '{x}', '{y}'), replace:  array($z, $x, $y), subject: $mapStyle->http_path);
        $client = BearExternalApiClient::fromExternalApi($mapStyle->externalApi);
        return $client->request($url)->body();
    }
}
