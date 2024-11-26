<?php declare(strict_types=1);

namespace Web\Www\Panorama\Controller;

use Domain\Panorama\Crud\PanoramaUpdater;
use GuardsmanPanda\Larabear\Infrastructure\Http\Service\Json;
use GuardsmanPanda\Larabear\Infrastructure\Http\Service\Req;
use GuardsmanPanda\Larabear\Infrastructure\Http\Service\Resp;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\JsonResponse;

final class PanoramaViewerController extends Controller {
  public function view(string $panoramaId): View {
    $data = DB::selectOne(query: "
      SELECT 
        jpg_path, heading, pitch, field_of_view
      FROM panorama
      WHERE id = ?
    ", bindings: [$panoramaId]);
    return Resp::view(view: 'panorama::viewer', data: [
      'panorama_id' => $panoramaId,
      'panorama_url' => App::isProduction() ? "https://panorama.wherebear.fun/$data->jpg_path" : "https://panorama.gman.bot/$data->jpg_path",
      'heading' => $data->heading,
      'pitch' => $data->pitch,
      'field_of_view' => $data->field_of_view
    ]);
  }

  public function updateViewport(string $panoramaId): JsonResponse {
    PanoramaUpdater::fromId(id: $panoramaId)->setViewport(
      heading: Req::getFloat(key: 'heading'),
      pitch: Req::getFloat(key: 'pitch'),
      field_of_view: Req::getFloat(key: 'field_of_view')
    )->update();
    return Json::empty();
  }
}
