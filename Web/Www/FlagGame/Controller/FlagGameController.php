<?php declare(strict_types=1);

namespace Web\Www\FlagGame\Controller;

use GuardsmanPanda\Larabear\Infrastructure\Http\Service\Req;
use GuardsmanPanda\Larabear\Infrastructure\Http\Service\Resp;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

final class FlagGameController extends Controller {

  public function index(): View {
    return Resp::view(view: 'flag-game::index', data: [
      'countries' => DB::select(query: "SELECT cca2, name FROM bear_country ORDER BY name"),
      'map' => DB::selectOne(query: "SELECT zoom_offset,tile_size, full_uri FROM map_style WHERE enum = 'SATELLITE'"),
    ]);
  }

  public static function locationData(): JsonResponse {
    $lng = Req::getFloat(key: 'lng');
    $lat = Req::getFloat(key: 'lat');

    $country = DB::selectOne(query: "
      SELECT
        bc.cca2, bc.name
      FROM map_country_boundary mcb
      LEFT JOIN bear_country bc ON mcb.country_cca2 = bc.cca2
      WHERE ST_DWITHIN(mcb.polygon, st_point(?, ?, 4326)::geography, 0)
      ORDER BY mcb.osm_relation_sort_order
      LIMIT 1
    ", bindings: [$lng, $lat]);

    $subdivision = DB::selectOne(query: "
      SELECT
        bcs.iso_3166, bcs.name
      FROM map_country_subdivision_boundary mcsb
      LEFT JOIN bear_country_subdivision bcs ON mcsb.country_subdivision_iso_3166 = bcs.iso_3166
      WHERE ST_DWITHIN(mcsb.polygon, st_point(?, ?, 4326)::geography, 0)
      ORDER BY mcsb.country_subdivision_iso_3166
      LIMIT 1
    ", bindings: [$lng, $lat]);

    return Resp::json(data: [
      'country' => $country,
      'subdivision' => $subdivision,
    ]);
  }
}
