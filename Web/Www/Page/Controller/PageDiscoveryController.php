<?php declare(strict_types=1);

namespace Web\Www\Page\Controller;

use Domain\Map\Service\MapService;
use Domain\Panorama\Crud\PanoramaCreator;
use Domain\Panorama\Crud\PanoramaUpdater;
use Domain\Panorama\Enum\PanoramaTagEnum;
use Domain\Panorama\Model\Panorama;
use Domain\Panorama\Service\PanoramaService;
use GuardsmanPanda\Larabear\Infrastructure\App\Service\BearRegexService;
use GuardsmanPanda\Larabear\Infrastructure\Auth\Service\BearAuthService;
use GuardsmanPanda\Larabear\Infrastructure\Http\Service\Req;
use GuardsmanPanda\Larabear\Infrastructure\Http\Service\Resp;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\View\View;
use Integration\StreetView\Client\StreetViewClient;
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


  public function addFromStreetViewData(): JsonResponse {
    $data = StreetViewClient::fromUrl(url: Req::getString(key: 'street_view_url'));
    //$heading = BearRegexService::extractFirstFloat(regex: '/,([^,h]+)h,/', subject: Req::getString(key: 'street_view_url'));
    //$pitch = BearRegexService::extractFirstFloat(regex: '~,([^,t]+)t/~', subject: Req::getString(key: 'street_view_url')) - 90;
    //if ($heading > 180) {
    //  $heading -= 360;
    //}
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
    } else {
      $updater = new PanoramaUpdater(model: $panorama);
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
    }
    return new JsonResponse(data: [
      'country_cca2' => $panorama->country_cca2,
      'lat' => $data->lat,
      'lng' => $data->lng,
      'date' => $data->date,
      'exists' => $exists,
      'from_id' => $data->from_id,
      'panorama_id' => $panorama->id,
      'tags_added' => $tags_added,
      'tags_removed' => $tags_removed,
    ]);
  }
}
