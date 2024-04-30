<?php declare(strict_types=1);

namespace Infrastructure\Database\Command;

use GuardsmanPanda\Larabear\Infrastructure\Console\Service\BearTransactionCommand;
use Infrastructure\Database\Initialize\DatabaseInitializeExternalApi;

final class DatabaseInitializeCommand extends BearTransactionCommand {
    protected $signature = 'database:initialize';
    protected $description = 'Initialize the database.';

    protected function handleInTransaction(): void {
        DatabaseInitializeExternalApi::initialize();
    }
}
