<?php declare(strict_types=1);

namespace Web\Www\Page\Controller;

use Domain\Map\Service\MapService;
use Domain\Panorama\Crud\PanoramaCreator;
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
      'markers' => DB::select(query: "
        SELECT ST_Y(p.location::geometry) as lat, ST_X(p.location::geometry) as lng
        FROM panorama p
        WHERE p.location IS NOT NULL
      "),
      'user' => DB::selectOne(query: "
        SELECT
            m.file_name as map_marker_file_name,
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


  public function addFromStreetViewLocation(): JsonResponse {
    $lat = Req::getFloat(key: 'lat');
    $lng = Req::getFloat(key: 'lng');
    $data = StreetViewClient::findByLocation(latitude: $lat, longitude: $lng);
    if ($data === null) {
      return new JsonResponse(data: ['status' => 'failed']);
    }
    if (!PanoramaService::panoramaExists(id: $data->panoId)) {
      $panorama = PanoramaCreator::createFromStreetViewData(data: $data, added_by_user_id: BearAuthService::getUserId());
      return new JsonResponse(data: [
        'country_cca2' => $panorama->country_cca2,
        'state_name' => $panorama->state_name,
        'city_name' => $panorama->city_name,
        'lat' => $data->lat,
        'lng' => $data->lng,
        'date' => $data->date,
        'exists' => false,
      ]);
    }
    return new JsonResponse(data: ['exists' => true]);
  }


  public function searchFromStreetViewLocation(): JsonResponse {
    $retries = Req::getInt(key: 'retries');
    $retries = min(max($retries, 0), 50);
    $lat = Req::getFloat(key: 'lat');
    $lng = Req::getFloat(key: 'lng');
    $results = [];
    for ($i = 0; $i <= $retries; $i++) {
      $newPos = MapService::offsetLatLng(lat: $lat, lng: $lng, meters: Req::getFloat(key: 'distance'));
      $data = StreetViewClient::findByLocation(latitude: $newPos->lat, longitude: $newPos->lng);
      if ($data === null) {
        $results[] = [
          'lat' => $newPos->lat,
          'lng' => $newPos->lng,
          'status' => 'failed',
        ];
        continue;
      }
      if (!PanoramaService::panoramaExists(id: $data->panoId)) {
        $panorama = PanoramaCreator::createFromStreetViewData(data: $data);
        $results[] = [
          'country_cca2' => $panorama->country_cca2,
          'state_name' => $panorama->state_name,
          'city_name' => $panorama->city_name,
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
