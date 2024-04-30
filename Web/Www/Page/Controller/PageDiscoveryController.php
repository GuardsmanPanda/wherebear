<?php declare(strict_types=1);

namespace Web\Www\Page\Controller;

use Domain\Panorama\Crud\PanoramaCreator;
use Domain\Panorama\Service\PanoramaService;
use GuardsmanPanda\Larabear\Infrastructure\Http\Service\Req;
use GuardsmanPanda\Larabear\Infrastructure\Http\Service\Resp;
use Illuminate\Routing\Controller;
use Illuminate\View\View;
use Integration\StreetView\StreetViewClient;

final class PageDiscoveryController extends Controller {
    public function index(): View {
        return Resp::view(view: 'page::discovery.index');
    }

    public function addFromStreetViewLocation(): array {
        $lat = Req::getFloat(key: 'lat');
        $lng = Req::getFloat(key: 'lng');
        $data = StreetViewClient::findByLocation(latitude: $lat, longitude: $lng);
        $exists = PanoramaService::panoramaExists(id: $data['pano_id']);
        if (!$exists) {
            $panorama = PanoramaCreator::createFromStreetViewData(data: $data);
        }
        return [
            'exists' => $exists,
        ];
    }
}
