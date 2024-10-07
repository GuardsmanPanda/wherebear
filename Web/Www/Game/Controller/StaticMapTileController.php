<?php declare(strict_types=1);

namespace Web\Www\Game\Controller;

use Domain\Map\Enum\MapStyleEnum;
use GuardsmanPanda\Larabear\Infrastructure\App\Service\BearStringService;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Storage;
use Integration\MapTile\MapTileClient;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

final class StaticMapTileController extends Controller {
  public function getMapTile(string $map_style_enum, string $z, string $x, string $file_name): BinaryFileResponse {
    $idx = BearStringService::getPosition(haystack: $file_name, needle: '.');
    $y = substr(string: $file_name, offset: 0, length: $idx);
    $data = MapTileClient::getMapTile(MapStyleEnum::from(value: $map_style_enum), $z, $x, $y);
    $rel_loc = "tile/$map_style_enum/$z/$x/$file_name";
    Storage::put(path: $rel_loc, contents: $data);
    return new BinaryFileResponse(storage_path('app/public/' . $rel_loc));
  }
}
