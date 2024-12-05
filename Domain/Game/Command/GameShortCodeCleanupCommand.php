<?php declare(strict_types=1);

namespace Domain\Game\Command;

use Domain\Game\Crud\GameUpdater;
use GuardsmanPanda\Larabear\Infrastructure\Console\Service\BearTransactionCommand;
use Illuminate\Support\Facades\DB;

final class GameShortCodeCleanupCommand extends BearTransactionCommand {

  protected function handleInTransaction(): void {
    $games = DB::select(query: "
      SELECT g.id
      FROM game g
      WHERE g.short_code IS NOT NULL AND g.created_at < NOW() - INTERVAL '10 day'
    ");
    foreach ($games as $game) {
      GameUpdater::fromId(id: $game->id)->setShortCode(short_code: null)->update();
    }
  }
}
