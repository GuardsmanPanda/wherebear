<?php declare(strict_types=1);

namespace Infrastructure\Database\Command;

use GuardsmanPanda\Larabear\Infrastructure\Console\Service\BearTransactionCommand;
use Infrastructure\Database\Initialize\DatabaseInitializeBearCountry;
use Infrastructure\Database\Initialize\DatabaseInitializeExternalApi;
use Infrastructure\Database\Initialize\DatabaseInitializeMapStyle;

final class DatabaseInitializeCommand extends BearTransactionCommand {
    protected $signature = 'database:initialize';
    protected $description = 'Initialize the database.';

    protected function handleInTransaction(): void {
        DatabaseInitializeBearCountry::initialize();
        DatabaseInitializeExternalApi::initialize();

        DatabaseInitializeMapStyle::initialize(); // Requires External API.

        $this->call(command: 'map:marker-synchronize');
    }
}
