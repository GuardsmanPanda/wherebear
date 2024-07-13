<?php declare(strict_types=1);

namespace Infrastructure\Database\Command;

use Domain\Game\Enum\GamePublicStatusEnum;
use Domain\Game\Enum\GameStateEnum;
use Domain\Map\Enum\MapStyleEnum;
use Domain\Panorama\Enum\TagEnum;
use Domain\User\Enum\BearPermissionEnum;
use Domain\User\Enum\BearRoleEnum;
use Domain\User\Service\WhereBearRolePermissionService;
use GuardsmanPanda\Larabear\Infrastructure\Console\Service\BearTransactionCommand;
use Infrastructure\App\Enum\BearExternalApiEnum;
use Infrastructure\Database\Initialize\DatabaseInitializeBearCountry;
use Infrastructure\Database\Initialize\DatabaseInitializeBearRolePermission;

final class DatabaseInitializeCommand extends BearTransactionCommand {
    protected $signature = 'database:initialize';
    protected $description = 'Initialize the database.';

    protected function handleInTransaction(): void {
        BearPermissionEnum::syncToDatabase();
        BearRoleEnum::syncToDatabase();
        DatabaseInitializeBearCountry::initialize();
        BearExternalApiEnum::syncToDatabase();
        GamePublicStatusEnum::syncToDatabase();
        GameStateEnum::syncToDatabase();
        TagEnum::syncToDatabase();

        MapStyleEnum::syncToDatabase(); // Requires BearExternalApiEnum.
        WhereBearRolePermissionService::syncRolePermissionsToDatabase(); // Requires Bear Role and Bear Permission.

        $this->call(command: 'map:marker-synchronize');
    }
}
