<?php declare(strict_types=1);

namespace Domain\Achievement\Command;

use GuardsmanPanda\Larabear\Infrastructure\Console\Service\BearTransactionCommand;

final class AchievementAssignRetroactiveCommand extends BearTransactionCommand {
  protected $signature = 'achievement:assign-retroactive';
  protected $description = 'Assign a retroactive achievement to all users';

  protected function handleInTransaction(): void {

  }
}