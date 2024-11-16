<?php declare(strict_types=1);

namespace Web\Www\App\Controller;

use GuardsmanPanda\Larabear\Infrastructure\Http\Service\Resp;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

final class AppPanoramaViewerController extends Controller {
  public function view(string $panoramaId): View {
    $jpg_path = DB::selectOne(query: "SELECT jpg_path FROM panorama WHERE id = ?", bindings: [$panoramaId])->jpg_path;
    return Resp::view(view: 'app::panorama-viewer', data: [
      'panorama_url' => App::isProduction() ? "https://panorama.wherebear.fun/$jpg_path" : "https://panorama.gman.bot/$jpg_path",
    ]);
  }
}
