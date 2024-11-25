<?php declare(strict_types=1);

namespace Web\Www\Panorama\Controller;

use GuardsmanPanda\Larabear\Infrastructure\Http\Service\Resp;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

final class PanoramaViewerController extends Controller {
  public function view(string $panoramaId): View {
    $data = DB::selectOne(query: "
      SELECT 
        jpg_path, heading, pitch, field_of_view
      FROM panorama
      WHERE id = ?
    ", bindings: [$panoramaId]);
    return Resp::view(view: 'panorama::viewer', data: [
      'panorama_url' => App::isProduction() ? "https://panorama.wherebear.fun/$data->jpg_path" : "https://panorama.gman.bot/$data->jpg_path",
      'heading' => $data->heading,
      'pitch' => $data->pitch,
      'field_of_view' => $data->field_of_view
    ]);
  }
}
