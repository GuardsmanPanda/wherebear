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
      SELECT
        bc.name, bc.cca2
      FROM map_country_boundary mcb
      LEFT JOIN bear_country bc ON mcb.country_cca2 = bc.cca2
      WHERE ST_DWithin(mcb.polygon, ST_Point(:lng, :lat, 4326)::geography, 0)
      ORDER BY mcb.osm_relation_sort_order
      LIMIT 1
    ", bindings: ['lng' => $lng, 'lat' => $lat]);

    $loc2 = DB::selectOne(query: "
      SELECT
        bcs.name as subdivision_name
      FROM wherebear.map_country_subdivision_boundary mcsb
      LEFT JOIN bear_country_subdivision bcs ON mcsb.country_subdivision_iso_3166 = bcs.iso_3166
      WHERE ST_DWithin(mcsb.polygon, ST_Point(:lng, :lat, 4326)::geography, 0)
      ORDER BY bcs.name, mcsb.country_subdivision_iso_3166
      LIMIT 1
    ", bindings: ['lng' => $lng, 'lat' => $lat])?->subdivision_name;

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