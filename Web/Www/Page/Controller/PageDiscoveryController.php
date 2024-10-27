<?php declare(strict_types=1);

namespace Web\Www\Page\Controller;

use Domain\Map\Service\MapService;
use Domain\Panorama\Crud\PanoramaCreator;
use Domain\Panorama\Crud\PanoramaUpdater;
use Domain\Panorama\Enum\PanoramaTagEnum;
use Domain\Panorama\Model\Panorama;
use Domain\Panorama\Service\PanoramaService;
use GuardsmanPanda\Larabear\Infrastructure\Auth\Service\BearAuthService;
use GuardsmanPanda\Larabear\Infrastructure\Http\Service\Req;
use GuardsmanPanda\Larabear\Infrastructure\Http\Service\Resp;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Integration\StreetView\Client\StreetViewClient;
use Symfony\Component\HttpFoundation\JsonResponse;

final class PageDiscoveryController extends Controller {
  public function index(): View {
    return Resp::view(view: 'page::discovery.index', data: [
      'user_panoramas' => DB::select(query: "
        SELECT ST_Y(p.location::geometry) as lat, ST_X(p.location::geometry) as lng
        FROM panorama p
        WHERE p.added_by_user_id IS NOT NULL
      "),
      'other_panoramas' => DB::select(query: "
        SELECT ST_Y(p.location::geometry) as lat, ST_X(p.location::geometry) as lng
        FROM panorama p
        WHERE p.added_by_user_id IS NULL
      "),
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
    $data = StreetViewClient::fromPanoramaId(panoramaId: Req::getString(key: 'panorama_id'));
    $data ??= StreetViewClient::fromLocation(latitude: Req::getFloat(key: 'lat'), longitude: Req::getFloat(key: 'lng'));
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
      'tags_added' => $tags_added,
      'tags_removed' => $tags_removed,
    ]);
  }


  public function searchFromStreetViewLocation(): JsonResponse {
    $retries = Req::getInt(key: 'retries');
    $retries = min(max($retries, 0), 50);
    $lat = Req::getFloat(key: 'lat');
    $lng = Req::getFloat(key: 'lng');
    $results = [];
    for ($i = 0; $i <= $retries; $i++) {
      $newPos = MapService::offsetLatLng(lat: $lat, lng: $lng, meters: Req::getFloat(key: 'distance'));
      $data = StreetViewClient::fromLocation(latitude: $newPos->latitude, longitude: $newPos->longitude);
      if ($data === null) {
        $results[] = [
          'lat' => $newPos->latitude,
          'lng' => $newPos->longitude,
          'status' => 'failed',
        ];
        continue;
      }
      if (!PanoramaService::panoramaExists(id: $data->panoId)) {
        $panorama = PanoramaCreator::createFromStreetViewData(data: $data);
        $results[] = [
          'country_cca2' => $panorama->country_cca2,
          'lat' => $data->lat,
          'lng' => $data->lng,
          'date' => $data->date,
          'status' => 'new',
        ];
        break;
      } else {
        $results[] = ['statue' => 'exists'];
      }
    }
    return new JsonResponse(data: $results);
  }
}
