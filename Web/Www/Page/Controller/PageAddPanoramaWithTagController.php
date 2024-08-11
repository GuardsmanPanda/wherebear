<?php declare(strict_types=1);

namespace Web\Www\Page\Controller;

use GuardsmanPanda\Larabear\Infrastructure\App\Service\BearRegexService;
use GuardsmanPanda\Larabear\Infrastructure\Auth\Service\BearAuthService;
use GuardsmanPanda\Larabear\Infrastructure\Http\Service\Req;
use GuardsmanPanda\Larabear\Infrastructure\Http\Service\Resp;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\View\View;

final class PageAddPanoramaWithTagController extends Controller {
  public function index(): View {
    return Resp::view(view: 'page::add-panorama-with-tag.index', data: [
      'user' => DB::selectOne(query: "
        SELECT
            m.file_name as map_marker_file_name,
            u.map_style_enum
        FROM bear_user u
        LEFT JOIN map_marker m ON u.map_marker_enum = m.enum
        LEFT JOIN map_style s ON u.map_style_enum = s.enum
        WHERE u.id = ?
      ", bindings: [BearAuthService::getUserId()]),
    ]);
  }


  public function streetviewsOrgUrlTranslation(): string {
    $html = Http::get(url: Req::getString(key: 'url'))->body();
    return BearRegexService::extractFirst(regex: '/"panoid":"([^"]+)/', subject: $html);
  }
}
