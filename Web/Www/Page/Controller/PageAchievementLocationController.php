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

    $polygon = DB::selectOne(query: "
      SELECT
        ST_AsGeoJSON(ST_Buffer(ST_MakePoint(?, ?)::geography, ?)) as polygon
    ", bindings: [$lng, $lat, $radius]);

    $panoramas = DB::select(query: "
      SELECT
        p.id, p.country_cca2, p.location, p.city_name, p.state_name
      FROM panorama p
      WHERE ST_DWithin(p.location, ST_SetSRID(ST_MakePoint(?, ?), 4326), ?)
    ", bindings: [$lng, $lat, $radius]);

    //$data = NominatimClient::reverseLookup(latitude: $lat, longitude: $lng);
    return new JsonResponse(data: [
      'polygon' => json_decode(json: $polygon->polygon),
      'panoramas' => $panoramas,
      //'location' => $data,
    ]);

  }
}