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
use Integration\StreetView\StreetViewClient;

final class PageDiscoveryController extends Controller {
    public function index(): View {
        return Resp::view(view: 'page::discovery.index', data: [
            'markers' => DB::select(query: "
                SELECT public.ST_Y(p.panorama_location::public.geometry) as lat, public.ST_X(p.panorama_location::public.geometry) as lng
                FROM panorama p
                WHERE p.panorama_location IS NOT NULL
            "),
            'user' => DB::selectOne(query: "
                SELECT
                    u.map_marker_file_name,
                    COALESCE(u.map_style_enum, 'OSM') as map_style_enum
                FROM bear_user u
                WHERE u.id = ?
            ", bindings: [BearAuthService::getUserId()]),
        ]);
    }


    public function addFromStreetViewLocation(): array {
        $lat = Req::getFloatOrDefault(key: 'lat');
        $lng = Req::getFloatOrDefault(key: 'lng');
        $data = StreetViewClient::findByLocation(latitude: $lat, longitude: $lng);
        if (!PanoramaService::panoramaExists(id: $data['pano_id'])) {
            $panorama = PanoramaCreator::createFromStreetViewData(data: $data, added_by_user_id: BearAuthService::getUserId());
            return [
                'country_iso2_code' => $panorama->country_iso_2_code,
                'state_name' => $panorama->state_name,
                'city_name' => $panorama->city_name,
                'lat' => $data['location']['lat'],
                'lng' => $data['location']['lng'],
                'date' => $data['date'],
                'exists' => false,
            ];
        }
        return [
            'exists' => true,
        ];
    }


    public function searchFromStreetViewLocation(): array {
        $retries = Req::getIntOrDefault(key: 'retries');
        $retries = min(max($retries, 0), 50);
        $lat = Req::getFloatOrDefault(key: 'lat');
        $lng = Req::getFloatOrDefault(key: 'lng');
        $results = [];
        for ($i = 0; $i <= $retries; $i++) {
            $newPos = MapService::offsetLatLng(lat: $lat, lng: $lng, meters: Req::getFloatOrDefault(key: 'distance'));
            $data = StreetViewClient::findByLocation(latitude: $newPos->lat, longitude: $newPos->lng);
            if ($data === null) {
                $results[] = [
                    'lat' => $newPos->lat,
                    'lng' => $newPos->lng,
                    'status' => 'failed',
                ];
                continue;
            }
            if (!PanoramaService::panoramaExists(id: $data['pano_id'])) {
                $panorama = PanoramaCreator::createFromStreetViewData(data: $data);
                $results[] = [
                    'country_iso2_code' => $panorama->country_iso_2_code,
                    'state_name' => $panorama->state_name,
                    'city_name' => $panorama->city_name,
                    'lat' => $data['location']['lat'],
                    'lng' => $data['location']['lng'],
                    'date' => $data['date'],
                    'status' => 'new',
                ];
                break;
            } else {
                $results[] = [
                    'statue' => 'exists',
                ];
            }
        }
        return $results;

    }
}
