<?php declare(strict_types=1);

namespace Infrastructure\Database\Command;

use Domain\Game\Enum\GamePublicStatusEnum;
use Domain\Game\Enum\GameStateEnum;
use Domain\Map\Enum\MapStyleEnum;
use Domain\Panorama\Enum\TagEnum;
use Domain\User\Enum\BearPermissionEnum;
use Domain\User\Enum\BearRoleEnum;
use Domain\User\Enum\UserLevelEnum;
use Domain\User\Service\WhereBearRolePermissionService;
use GuardsmanPanda\Larabear\Infrastructure\Console\Service\BearTransactionCommand;
use Infrastructure\App\Enum\BearExternalApiEnum;
use Infrastructure\App\Enum\BearOauth2ClientEnum;
use Infrastructure\Database\Initialize\DatabaseInitializeBearCountry;

final class DatabaseInitializeCommand extends BearTransactionCommand {
    protected $signature = 'database:initialize';
    protected $description = 'Initialize the database.';

    protected function handleInTransaction(): void {
        BearOauth2ClientEnum::syncToDatabase();
        BearPermissionEnum::syncToDatabase();
        BearRoleEnum::syncToDatabase();
        DatabaseInitializeBearCountry::initialize();
        BearExternalApiEnum::syncToDatabase();
        TagEnum::syncToDatabase();
        UserLevelEnum::syncToDatabase();

        GamePublicStatusEnum::syncToDatabase(); // Requires UserLevelEnum.
        GameStateEnum::syncToDatabase(); // Requires UserLevelEnum.

        MapStyleEnum::syncToDatabase(); // Requires BearExternalApiEnum.
        WhereBearRolePermissionService::syncRolePermissionsToDatabase(); // Requires Bear Role and Bear Permission.
    }
}
