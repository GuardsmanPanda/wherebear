<?php declare(strict_types=1);

namespace Web\Www\Game\Controller;

use Domain\Map\Enum\MapStyleEnum;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Storage;
use Integration\MapTile\MapTileClient;
use RuntimeException;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

final class StaticMapTileController extends Controller {
  public function getMapTile(string $map_style_enum, string $z, string $x, string $file_name): BinaryFileResponse {
    $idx = strpos(haystack: $file_name, needle: '.');
    if ($idx === false) {
      throw new RuntimeException(message: 'Invalid map tile file name');
    }
    $y = substr(string: $file_name, offset: 0, length: $idx);
    $data = MapTileClient::getMapTile(MapStyleEnum::from(value: $map_style_enum), $z, $x, $y);
    $rel_loc = "tile/$map_style_enum/$z/$x/$file_name";
    Storage::put(path: $rel_loc, contents: $data);
    return new BinaryFileResponse(storage_path('app/public/' . $rel_loc));
  }
}
