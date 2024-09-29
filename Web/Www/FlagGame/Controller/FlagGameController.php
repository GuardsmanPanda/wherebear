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
      'countries' => DB::select(query: "SELECT cca2, name FROM bear_country WHERE cca2 != 'XX'"),
      'map' => DB::selectOne(query: "SELECT zoom_offset,tile_size, full_uri FROM map_style WHERE enum = 'SATELLITE'"),
    ]);
  }


  public function old(): View {
    return Resp::view(view: 'flag-game::index', data: [
      'map' => DB::selectOne(query: "SELECT zoom_offset,tile_size, full_uri FROM map_style WHERE enum = 'SATELLITE'"),
    ]);
  }

  public static function locationData(): JsonResponse {
    $lng = Req::getFloat(key: 'lng');
    $lat = Req::getFloat(key: 'lat');

    $country = DB::selectOne(query: <<<SQL
      WITH country as (SELECT wherebear_country(:lng, :lat) as cca2)
      SELECT
        bc.cca2, bc.name
      FROM bear_country bc
      WHERE bc.cca2 = (SELECT cca2 FROM country)
      LIMIT 1
    SQL, bindings: ['lng' => $lng, 'lat' => $lat]);

    $data = DB::selectOne(query: <<<SQL
      WITH 
        country as (SELECT wherebear_country(:lng, :lat) as cca2),
        subdivision as (SELECT wherebear_subdivision(:lng, :lat, (SELECT cca2 FROM country)) as cca3)
      SELECT
        bc.cca2, bc.name as country_name, bcs.iso_3166, bcs.name as subdivision_name
      FROM bear_country bc
      LEFT JOIN bear_country_subdivision bcs ON bc.cca2 = bcs.country_cca2 AND bcs.iso_3166 = (SELECT cca3 FROM subdivision)
      WHERE bc.cca2 = (SELECT cca2 FROM country)
    SQL, bindings: ['lng' => $lng, 'lat' => $lat]);

    return Resp::json(data: [
      'country' => $country,
      'data' => $data,
    ]);
  }
}
