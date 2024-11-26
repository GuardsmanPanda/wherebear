<?php declare(strict_types=1);

namespace Web\Www\WebApi\Controller;

use Domain\Panorama\Crud\PanoramaUpdater;
use GuardsmanPanda\Larabear\Infrastructure\Http\Service\Json;
use GuardsmanPanda\Larabear\Infrastructure\Http\Service\Req;
use Illuminate\Routing\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;

final class WebApiPanoramaController extends Controller {
  public function patchPanorama(string $panoramaId): JsonResponse {
    $updater = PanoramaUpdater::fromId(id: $panoramaId);
    if (Req::hasAny(keys: ['heading', 'pitch', 'field_of_view'])) {
      $updater->setViewport(
        heading: Req::getFloat(key: 'heading'),
        pitch: Req::getFloat(key: 'pitch'),
        field_of_view: Req::getFloat(key: 'field_of_view')
      );
    }
    return Json::fromModel(model: $updater->update());
  }
}
