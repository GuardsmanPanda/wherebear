<?php declare(strict_types=1);

namespace Web\Www\Game\Controller;

use Domain\Map\Enum\MapStyleEnum;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Storage;
use Integration\MapTile\MapTileClient;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

final class StaticMapTileController extends Controller {
    public function getMapTile(int $map_style, string $z, string $x, string $file_name): BinaryFileResponse {
        $y =  str_replace('.png', '', $file_name);
        $data = MapTileClient::getMapTile(MapStyleEnum::fromInt($map_style), $z, $x, $y);
        $rel_loc = "tile/$map_style/$z/$x/$file_name";
        Storage::disk(name: 'public')->put(path: $rel_loc, contents: $data);
        return new BinaryFileResponse(storage_path('app/public/' . $rel_loc));
    }
}
