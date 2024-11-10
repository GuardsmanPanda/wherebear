<?php declare(strict_types=1);

namespace Domain\Game\Crud;

use Domain\Game\Enum\GamePublicStatusEnum;
use Domain\Game\Enum\GameStateEnum;
use Domain\Game\Model\Game;
use Domain\Panorama\Enum\PanoramaTagEnum;
use GuardsmanPanda\Larabear\Infrastructure\App\Enum\BearSeverityEnum;
use GuardsmanPanda\Larabear\Infrastructure\App\Service\BearShortCodeService;
use GuardsmanPanda\Larabear\Infrastructure\Auth\Service\BearAuthService;
use GuardsmanPanda\Larabear\Infrastructure\Database\Service\BearDatabaseService;
use GuardsmanPanda\Larabear\Infrastructure\Error\Crud\BearErrorCreator;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Support\Str;
use RuntimeException;

final readonly class GameCreator {
  public static function create(
    string               $name,
    int                  $round_duration_seconds,
    int                  $round_result_duration_seconds,
    GamePublicStatusEnum $game_public_status,
    int|null             $number_of_rounds = null,
    GameStateEnum        $game_state_enum = GameStateEnum::WAITING_FOR_PLAYERS,
    PanoramaTagEnum|null $panorama_tag_enum = null,
    Game|null            $templated_by_game = null
  ): Game {
    BearDatabaseService::mustBeInTransaction();
    BearDatabaseService::mustBeProperHttpMethod(verbs: ['POST']);

    if ($number_of_rounds === null) {
      if ($templated_by_game === null) {
        throw new RuntimeException("Number of rounds must be provided if not templated by a game.");
      }
      $number_of_rounds = $templated_by_game->number_of_rounds;
    }

    $model = new Game();
    $model->id = Str::uuid()->toString();

    $model->name = $name;
    $model->game_state_enum = $game_state_enum;
    $model->number_of_rounds = $number_of_rounds;
    $model->round_duration_seconds = $round_duration_seconds;
    $model->round_result_duration_seconds = $round_result_duration_seconds;
    $model->created_by_user_id = BearAuthService::getUser()->id;
    $model->game_public_status_enum = $game_public_status;
    $model->panorama_tag_enum = $panorama_tag_enum;
    $model->is_forced_start = false;
    $model->current_round = 0;

    if ($templated_by_game !== null) {
      $model->templated_by_game_id = $templated_by_game->id;
    }

    $model->experience_points = match ($game_state_enum) {
      GameStateEnum::TEMPLATE => 0,
      default => $number_of_rounds + 3,
    };

    $model->save();

    // Only generate short codes for games that are waiting for players, not daily games or templates.
    while ($model->short_code === null && $game_state_enum === GameStateEnum::WAITING_FOR_PLAYERS) {
      try {
        $model = GameUpdater::fromId($model->id)
          ->setShortCode(short_code: BearShortCodeService::getRandomShortCode(length: 5))
          ->update();
      } catch (UniqueConstraintViolationException $e) {
        BearErrorCreator::create(
          message: "Failed to set short code on game with id: $model->id",
          severity: BearSeverityEnum::ERROR,
          exception: $e
        );
      }
    }

    // Create All the rounds for template games
    if ($templated_by_game !== null) {
      GameRoundCreator::createFromTemplate(game: $model, template: $templated_by_game);
    }

    return $model;
  }
}
