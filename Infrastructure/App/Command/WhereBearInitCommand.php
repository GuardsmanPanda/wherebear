<?php declare(strict_types=1);

namespace Infrastructure\App\Command;

use Domain\Achievement\Enum\AchievementEnum;
use Domain\Achievement\Enum\AchievementTypeEnum;
use Domain\Game\Enum\GamePublicStatusEnum;
use Domain\Game\Enum\GameStateEnum;
use Domain\Map\Crud\MapCountryBoundaryCrud;
use Domain\Map\Crud\MapCountrySubdivisionBoundaryCrud;
use Domain\Map\Enum\MapMarkerEnum;
use Domain\Map\Enum\MapStyleEnum;
use Domain\Panorama\Enum\PanoramaTagEnum;
use Domain\User\Enum\BearPermissionEnum;
use Domain\User\Enum\BearRoleEnum;
use Domain\User\Enum\UserFlagEnum;
use Domain\User\Enum\UserLevelEnum;
use Domain\User\Service\WhereBearRolePermissionService;
use GuardsmanPanda\Larabear\Infrastructure\Console\Service\BearTransactionCommand;
use Infrastructure\App\Enum\BearConfigEnum;
use Infrastructure\App\Enum\BearExternalApiEnum;
use Infrastructure\App\Enum\BearOauth2ClientEnum;

final class WhereBearInitCommand extends BearTransactionCommand {
  protected $signature = 'wherebear:init';
  protected $description = 'Initialize the database.';

  protected function handleInTransaction(): void {
    AchievementTypeEnum::syncToDatabase();
    BearConfigEnum::syncToDatabase();
    BearOauth2ClientEnum::syncToDatabase();
    BearPermissionEnum::syncToDatabase();
    BearRoleEnum::syncToDatabase();
    BearExternalApiEnum::syncToDatabase();
    GamePublicStatusEnum::syncToDatabase();
    PanoramaTagEnum::syncToDatabase();
    UserFlagEnum::syncToDatabase();
    UserLevelEnum::syncToDatabase();

    WhereBearRolePermissionService::syncRolePermissionsToDatabase(); // Requires Bear Role and Bear Permission.
    AchievementEnum::syncToDatabase(); // Requires AchievementTypeEnum.
    GameStateEnum::syncToDatabase(); // Requires UserLevelEnum.
    MapMarkerEnum::syncToDatabase(); // Requires UserLevelEnum.
    MapStyleEnum::syncToDatabase(); // Requires BearExternalApiEnum && UserLevelEnum.

    MapCountryBoundaryCrud::syncCountriesBoundariesToDatabase(); // Requires BearCountryEnum.
    MapCountrySubdivisionBoundaryCrud::syncCountriesSubdivisionBoundariesToDatabase(); // Requires BearCountrySubdivisionEnum.
  }
}
