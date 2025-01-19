<?php declare(strict_types=1);

namespace Web\Www\WebApi\Controller;

use Domain\Panorama\Crud\PanoramaCreator;
use Domain\Panorama\Crud\PanoramaUpdater;
use Domain\Panorama\Enum\PanoramaTagEnum;
use Domain\Panorama\Model\Panorama;
use GuardsmanPanda\Larabear\Infrastructure\App\Service\BearRegexService;
use GuardsmanPanda\Larabear\Infrastructure\Auth\Service\BearAuthService;
use GuardsmanPanda\Larabear\Infrastructure\Http\Service\Htmx;
use GuardsmanPanda\Larabear\Infrastructure\Http\Service\Json;
use GuardsmanPanda\Larabear\Infrastructure\Http\Service\Req;
use Illuminate\Routing\Controller;
use Integration\StreetView\Client\StreetViewClient;
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
    if (Req::has(key: 'retired')) {
      $updater->setRetiredStatus(retired: Req::getBool(key: 'retired'), retired_reason: Req::getStringOrNull(key: 'retired_reason'));
    }
    Htmx::refresh();
    return Json::fromModel(model: $updater->update());
  }


  public function streetViewUrl(): JsonResponse {
    $data = StreetViewClient::fromUrl(url: Req::getString(key: 'street_view_url'));
    if ($data === null) {
      return new JsonResponse(data: ['status' => 'failed']);
    }
    $panorama = Panorama::find(id: $data->panoId);
    $exists = $panorama !== null;
    $tags_added = [];
    $tags_removed = [];

    if ($panorama === null) {
      $panorama = PanoramaCreator::createFromStreetViewData(
        data: $data,
        panorama_tag_array: Req::getArray(key: 'tags_checked'),
        added_by_user_id: BearAuthService::getUserId()
      );
      $tags_added = $panorama->panorama_tag_array->getArrayCopy();
    }

    $updater = new PanoramaUpdater(model: $panorama);
    if (Req::getBool(key: 'street_view_viewport') && $updater->hasDefaultFieldOfView()) {
      $heading = BearRegexService::extractFirstFloatOrNull(regex: '/,([^,h]+)h,/', subject: Req::getString(key: 'street_view_url'));
      $pitch = BearRegexService::extractFirstFloat(regex: '~,([^,t]+)t[,/]~', subject: Req::getString(key: 'street_view_url')) - 90;
      $updater->setStreetViewViewport(heading: $heading ?? 0, pitch: $pitch);
    }
    foreach (Req::getArray(key: 'tags_checked') as $tag) {
      if ($updater->addPanoramaTag(tag: PanoramaTagEnum::from(value: $tag))) {
        $tags_added[] = $tag;
      }
    }
    foreach (Req::getArray(key: 'tags_unchecked') as $tag) {
      if ($updater->removePanoramaTag(tag: PanoramaTagEnum::from(value: $tag))) {
        $tags_removed[] = $tag;
      }
    }
    $updater->update();

    return new JsonResponse(data: [
      'exists' => $exists,
      'from_id' => $data->from_id,
      'panorama' => $panorama,
      'tags_added' => $tags_added,
      'tags_removed' => $tags_removed,
    ]);
  }
}
