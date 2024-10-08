<?php declare(strict_types=1);

namespace Web\Www\Page\Controller;

use GuardsmanPanda\Larabear\Infrastructure\Auth\Service\BearAuthService;
use GuardsmanPanda\Larabear\Infrastructure\Http\Service\Json;
use GuardsmanPanda\Larabear\Infrastructure\Http\Service\Req;
use GuardsmanPanda\Larabear\Infrastructure\Http\Service\Resp;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Integration\Nominatim\Client\NominatimClient;
use Symfony\Component\HttpFoundation\JsonResponse;

final class PageAchievementLocationController extends Controller {
  public function index(): View {
    return Resp::view(view: 'page::achievement-location.index', data: [
      'user' => DB::selectOne(query: "
        SELECT
            m.file_path as map_marker_file_path,
            s.tile_size as map_style_tile_size,
            s.zoom_offset as map_style_zoom_offset,
            s.full_uri as map_style_full_uri 
        FROM bear_user u
        LEFT JOIN map_marker m ON u.map_marker_enum = m.enum
        LEFT JOIN map_style s ON u.map_style_enum = s.enum
        WHERE u.id = ?
      ", bindings: [BearAuthService::getUserId()]),
    ]);
  }

  public function getData(): JsonResponse {
    $lat = Req::getFloat(key: 'lat');
    $lng = Req::getFloat(key: 'lng');
    $radius = Req::getFloat(key: 'radius');

    $loc = DB::selectOne(query: "
      SELECT bc.name, bc.cca2
      FROM bear_country bc
      where bc.cca2 = wherebear_country(:lng, :lat)
    ", bindings: ['lng' => $lng, 'lat' => $lat]);

    $loc2 = DB::selectOne(query: "
      SELECT bcs.name, bcs.iso_3166
      FROM bear_country_subdivision bcs
      WHERE bcs.iso_3166 = wherebear_subdivision(:lng, :lat, :cca2)
    ", bindings: ['lng' => $lng, 'lat' => $lat, 'cca2' => $loc->cca2]);

    $polygon = DB::selectOne(query: "
      SELECT
        ST_AsGeoJSON(ST_Buffer(ST_Point(?, ?, 4326)::geography, ?)) as polygon
    ", bindings: [$lng, $lat, $radius]);

    $panoramas = DB::select(query: "
      SELECT
        p.id, p.country_cca2,
        ST_Y(p.location::geometry) as lat, ST_X(p.location::geometry) as lng,
        st_dwithin(p.location, ST_Point(:lng, :lat, 4326)::geography, :radius) as within
      FROM panorama p
      WHERE ST_DWithin(p.location, ST_Point(:lng, :lat, 4326)::geography, :radius * 2)
    ", bindings: ['lng' => $lng, 'lat' => $lat, 'radius' => $radius]);


    //$data = NominatimClient::reverseLookup(latitude: $lat, longitude: $lng);
    return new JsonResponse(data: [
      'polygon' => json_decode(json: $polygon->polygon),
      'panoramas' => $panoramas,
      'location' => $loc,
      'subdivision' => $loc2,
    ]);

  }
}