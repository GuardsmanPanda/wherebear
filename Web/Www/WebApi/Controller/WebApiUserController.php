<?php

declare(strict_types=1);

namespace Web\Www\WebApi\Controller;

use Domain\Map\Enum\MapMarkerEnum;
use Domain\Map\Enum\MapStyleEnum;
use Domain\User\Crud\WhereBearUserUpdater;
use Domain\User\Enum\UserFlagEnum;
use GuardsmanPanda\Larabear\Infrastructure\Auth\Service\BearAuthService;
use GuardsmanPanda\Larabear\Infrastructure\Http\Service\Req;
use GuardsmanPanda\Larabear\Infrastructure\Http\Service\Resp;
use GuardsmanPanda\Larabear\Infrastructure\Locale\Enum\BearCountryEnum;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\JsonResponse;

final class WebApiUserController extends Controller {
  public function getMapMarkers(): JsonResponse {
    $mapMarkers = DB::select(query: <<<SQL
      SELECT mm.enum, mm.file_path, mm.grouping, mm.map_anchor
      FROM map_marker mm
      WHERE user_level_enum <= (SELECT user_level_enum FROM bear_user WHERE id = ?)
      ORDER BY mm.grouping = 'Miscellaneous',  mm.grouping, mm.file_path
    SQL, bindings: [BearAuthService::getUserId()]);

    return Resp::json($mapMarkers);
  }

  public function getMapStyles(): JsonResponse {
    $mapStyles = DB::select(query: <<<SQL
      SELECT ms.enum, ms.name, ms.full_uri, ms.user_level_enum, ms.short_name
      FROM map_style ms
      WHERE ms.enum != 'DEFAULT'
      ORDER BY ms.user_level_enum, ms.name
    SQL, bindings: []);

    return Resp::json($mapStyles);
  }

  public function getFlags(): JsonResponse {
    $countries = DB::select(query: <<<SQL
      SELECT name, cca2
      FROM bear_country
      ORDER BY name
    SQL);

    $noveltyFlags = DB::select(query: <<<SQL
      SELECT enum, description, file_path
      FROM user_flag
      ORDER BY enum
    SQL);

    return Resp::json([
      'countries' => $countries,
      'novelty_flags' =>  $noveltyFlags,
    ]);
  }

  public function patch(): JsonResponse {
    $updater = WhereBearUserUpdater::fromId(id: BearAuthService::getUserId());

    if (Req::has(key: 'country_cca2')) {
      $updater->setCountryCca2(country_cca2: BearCountryEnum::from(value: Req::getString(key: 'country_cca2')));
    }
    if (Req::has(key: 'display_name')) {
      $updater->setDisplayName(display_name: Req::getString(key: 'display_name'));
    }
    if (Req::has(key: 'map_marker_enum')) {
      $updater->setMapMarkerEnum(map_marker_enum: MapMarkerEnum::fromRequest());
    }
    if (Req::has(key: 'map_style_enum')) {
      $updater->setMapStyleEnum(map_style_enum: MapStyleEnum::fromRequest());
    }
    if (Req::has(key: 'user_flag_enum')) {
      $updater->setUserFlag(enum: UserFlagEnum::fromRequest());
    }

    $updater->update();
    return Resp::json([]);
  }
}
