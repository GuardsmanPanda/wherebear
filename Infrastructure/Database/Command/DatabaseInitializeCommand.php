<?php declare(strict_types=1);

namespace Infrastructure\Database\Command;

use GuardsmanPanda\Larabear\Infrastructure\Console\Service\BearTransactionCommand;
use Infrastructure\Database\Initialize\DatabaseInitializeBearCountry;
use Infrastructure\Database\Initialize\DatabaseInitializeBearPermission;
use Infrastructure\Database\Initialize\DatabaseInitializeBearRole;
use Infrastructure\Database\Initialize\DatabaseInitializeBearRolePermission;
use Infrastructure\Database\Initialize\DatabaseInitializeExternalApi;
use Infrastructure\Database\Initialize\DatabaseInitializeGameState;
use Infrastructure\Database\Initialize\DatabaseInitializeMapStyle;

final class DatabaseInitializeCommand extends BearTransactionCommand {
    protected $signature = 'database:initialize';
    protected $description = 'Initialize the database.';

    protected function handleInTransaction(): void {
        DatabaseInitializeBearPermission::initialize();
        DatabaseInitializeBearRole::initialize();
        DatabaseInitializeBearCountry::initialize();
        DatabaseInitializeExternalApi::initialize();
        DatabaseInitializeGameState::initialize();

        DatabaseInitializeMapStyle::initialize(); // Requires External API.
        DatabaseInitializeBearRolePermission::initialize(); // Requires Bear Role and Bear Permission.

        $this->call(command: 'map:marker-synchronize');
    }
}
