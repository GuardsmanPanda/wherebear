<?php declare(strict_types=1);

namespace Web\Www\Page\Controller;


use GuardsmanPanda\Larabear\Infrastructure\Auth\Service\BearAuthService;
use GuardsmanPanda\Larabear\Infrastructure\Http\Service\Json;
use GuardsmanPanda\Larabear\Infrastructure\Http\Service\Req;
use GuardsmanPanda\Larabear\Infrastructure\Http\Service\Resp;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\JsonResponse;

final class PageDiscoveryController extends Controller {
  public function index(): View {
    return Resp::view(view: 'page::discovery.index', data: [
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

  public function getPanoramas(): JsonResponse {
    return Json::fromSql(sql: "
      SELECT
        ST_Y(location::geometry) as latitude,
        ST_X(location::geometry) as longitude
      FROM panorama p
      WHERE 
        st_within(p.location::geometry, ST_MakeEnvelope(?, ?, ?, ?, 4326))
        AND p.retired_at IS NULL
    ", data: [Req::getFloat(key: 'west'), Req::getFloat(key: 'south'), Req::getFloat(key: 'east'), Req::getFloat(key: 'north')]);
  }
}
