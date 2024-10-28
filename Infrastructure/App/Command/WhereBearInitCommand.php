<?php declare(strict_types=1);

namespace Infrastructure\App\Command;

use Domain\Achievement\Enum\AchievementEnum;
use Domain\Achievement\Enum\AchievementTypeEnum;
use Domain\Game\Enum\GamePublicStatusEnum;
use Domain\Game\Enum\GameStateEnum;
use Domain\Import\Enum\ImportStatusEnum;
use Domain\Map\Crud\MapCountryBoundaryCrud;
use Domain\Map\Crud\MapCountrySubdivisionBoundaryCrud;
use Domain\Map\Enum\MapMarkerEnum;
use Domain\Map\Enum\MapStyleEnum;
use Domain\Panorama\Enum\PanoramaTagEnum;
use Domain\User\Enum\BearPermissionEnum;
use Domain\User\Enum\BearRoleEnum;
use Domain\User\Enum\UserFlagEnum;
use Domain\User\Enum\UserLevelEnum;
use GuardsmanPanda\Larabear\Infrastructure\Console\Service\BearTransactionCommand;
use Infrastructure\App\Enum\BearConfigEnum;
use Infrastructure\App\Enum\BearExternalApiEnum;
use Infrastructure\App\Enum\BearOauth2ClientEnum;

final class WhereBearInitCommand extends BearTransactionCommand {
  protected $signature = 'wherebear:init {--bootstrap}';
  protected $description = 'Initialize the database.';

  protected function handleInTransaction(): void {
    AchievementTypeEnum::syncToDatabase();
    BearConfigEnum::syncToDatabase();
    BearOauth2ClientEnum::syncToDatabase();
    BearPermissionEnum::syncToDatabase();
    BearExternalApiEnum::syncToDatabase();
    GamePublicStatusEnum::syncToDatabase();
    ImportStatusEnum::syncToDatabase();
    PanoramaTagEnum::syncToDatabase();
    UserFlagEnum::syncToDatabase();
    UserLevelEnum::syncToDatabase();

    BearRoleEnum::syncToDatabase(); // Requires BearPermissionEnum.
    AchievementEnum::syncToDatabase(); // Requires AchievementTypeEnum.
    GameStateEnum::syncToDatabase(); // Requires UserLevelEnum.
    MapMarkerEnum::syncToDatabase(); // Requires UserLevelEnum.
    MapStyleEnum::syncToDatabase(); // Requires BearExternalApiEnum && UserLevelEnum.

    if ($this->option(key: 'bootstrap')) { // Skip the rest of the initialization if we're just bootstrapping.
      return;
    }

    MapCountryBoundaryCrud::syncCountriesBoundariesToDatabase(); // Requires BearCountryEnum && valid BearExternalApiEnum.
    MapCountrySubdivisionBoundaryCrud::syncCountriesSubdivisionBoundariesToDatabase(haltOnError: false); // Same as above.
  }
}
