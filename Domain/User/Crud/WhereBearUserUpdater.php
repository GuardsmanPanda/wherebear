<?php

declare(strict_types=1);

namespace Domain\User\Crud;

use Carbon\CarbonInterface;
use Domain\Game\Broadcast\GameBroadcast;
use Domain\Map\Enum\MapMarkerEnum;
use Domain\Map\Enum\MapStyleEnum;
use Domain\User\Enum\UserFlagEnum;
use Domain\User\Model\WhereBearUser;
use GuardsmanPanda\Larabear\Infrastructure\App\Service\BearShortCodeService;
use GuardsmanPanda\Larabear\Infrastructure\Auth\Service\BearAuthService;
use GuardsmanPanda\Larabear\Infrastructure\Database\Service\BearDatabaseService;
use GuardsmanPanda\Larabear\Infrastructure\Locale\Enum\BearCountryEnum;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

final readonly class WhereBearUserUpdater {
  public function __construct(private WhereBearUser $model) {
    BearDatabaseService::mustBeInTransaction();
  }

  public static function fromId(string $id): self {
    return new self(model: WhereBearUser::findOrFail(id: $id));
  }


  public function setDisplayName(string $display_name): self {
    $this->model->display_name = $display_name;
    return $this;
  }

  public function setMapMarkerEnum(MapMarkerEnum $map_marker_enum): self {
    if ($this->model->user_level_enum->value < $map_marker_enum->getUserLevelRequirement()->value) {
      throw new BadRequestHttpException(message: "User level too low for map marker: $map_marker_enum->value");
    }
    $this->model->map_marker_enum = $map_marker_enum;
    return $this;
  }

  public function setMapStyleEnum(MapStyleEnum $map_style_enum): self {
    if ($this->model->user_level_enum->value < $map_style_enum->getUserLevelRequirement()->value) {
      throw new BadRequestHttpException(message: "User level too low for map style: $map_style_enum->value");
    }
    $this->model->map_style_enum = $map_style_enum;
    return $this;
  }


  public function setCountryCca2(BearCountryEnum $country_cca2): self {
    $this->model->country_cca2 = $country_cca2;
    $this->model->user_flag_enum = null;
    return $this;
  }

  public function setUserFlag(UserFlagEnum $enum): self {
    $this->model->user_flag_enum = $enum;
    return $this;
  }

  public function setLastLoginAt(CarbonInterface|null $last_login_at): self {
    if ($last_login_at?->toIso8601String() === $this->model->last_login_at?->toIso8601String()) {
      return $this;
    }
    $this->model->last_login_at = $last_login_at;
    return $this;
  }


  public function makeAnonymous(): self {
    $this->model->user_flag_enum = UserFlagEnum::UNKNOWN;
    $this->model->display_name = 'Player-' . BearShortCodeService::generateNextCode();
    $map_marker = DB::selectOne(query: "
      SELECT enum
      FROM map_marker
      WHERE user_level_enum <= ?
      ORDER BY random()
      LIMIT 1
    ", bindings: [$this->model->user_level_enum->value]);
    $this->model->map_marker_enum = MapMarkerEnum::from(value: $map_marker->enum);
    return $this;
  }


  public function update(): WhereBearUser {
    $this->model->save();

    if ($this->model->wasChanged(['country_cca2', 'display_name', 'map_marker_enum', 'map_style_enum', 'user_flag_enum'])) {
      /** The active games where the user is a participant. */
      $activeGamesForUser = DB::select(query: <<<SQL
        SELECT
          g.id
        FROM game_user gu 
        LEFT JOIN game g ON gu.game_id = g.id
        LEFT JOIN game_state gs ON g.game_state_enum = gs.enum
        WHERE 
          gu.user_id = ?
          AND gs.is_multiplayer
          AND gs.is_lobby
      SQL, bindings: [BearAuthService::getUserId()]);

      foreach ($activeGamesForUser as $game) {
        GameBroadcast::gameUserUpdate(gameId: $game->id, userId: BearAuthService::getUserId());
      }
    }

    return $this->model;
  }
}
