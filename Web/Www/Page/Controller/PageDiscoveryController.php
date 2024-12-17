<?php declare(strict_types=1);

namespace Web\Www\Page\Controller;

use GuardsmanPanda\Larabear\Infrastructure\Auth\Service\BearAuthService;
use GuardsmanPanda\Larabear\Infrastructure\Http\Service\Resp;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

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
}
