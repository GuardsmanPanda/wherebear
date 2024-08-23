<?php declare(strict_types=1);

namespace Domain\Game\Model;

use Carbon\CarbonInterface;
use Closure;
use Domain\Game\Enum\GamePublicStatusEnum;
use Domain\Game\Enum\GameStateEnum;
use Domain\Panorama\Enum\PanoramaTagEnum;
use Domain\Panorama\Model\PanoramaTag;
use GuardsmanPanda\Larabear\Infrastructure\Auth\Model\BearUser;
use GuardsmanPanda\Larabear\Infrastructure\Database\Traits\BearDatabaseChangeTrait;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * AUTO GENERATED FILE DO NOT MODIFY
 *
 * @method static Game|null find(string $id, array $columns = ['*'])
 * @method static Game findOrFail(string $id, array $columns = ['*'])
 * @method static Game sole(array $columns = ['*'])
 * @method static Game|null first(array $columns = ['*'])
 * @method static Game firstOrFail(array $columns = ['*'])
 * @method static Game firstOrCreate(array $filter, array $values)
 * @method static Game firstOrNew(array $filter, array $values)
 * @method static Game|null firstWhere(string $column, string $operator, string|float|int|bool $value)
 * @method static Collection<int, Game> all(array $columns = ['*'])
 * @method static Collection<int, Game> get(array $columns = ['*'])
 * @method static Collection<int, Game> fromQuery(string $query, array $bindings = [])
 * @method static Builder<Game> lockForUpdate()
 * @method static Builder<Game> select(array $columns = ['*'])
 * @method static Builder<Game> selectRaw(string $expression, array $bindings = [])
 * @method static Builder<Game> with(array $relations)
 * @method static Builder<Game> leftJoin(string $table, string $first, string $operator = null, string $second = null)
 * @method static Builder<Game> where(string $column, string $operator = null, string|float|int|bool $value = null)
 * @method static Builder<Game> whereIn(string $column, array $values)
 * @method static Builder<Game> whereNull(string|array $columns)
 * @method static Builder<Game> whereNotNull(string|array $columns)
 * @method static Builder<Game> whereYear(string $column, string $operator, CarbonInterface|string|int $value)
 * @method static Builder<Game> whereMonth(string $column, string $operator, CarbonInterface|string|int $value)
 * @method static Builder<Game> whereDate(string $column, string $operator, CarbonInterface|string $value)
 * @method static Builder<Game> whereExists(Closure $callback)
 * @method static Builder<Game> whereNotExists(Closure $callback)
 * @method static Builder<Game> whereHas(string $relation, Closure $callback = null, string $operator = '>=', int $count = 1)
 * @method static Builder<Game> withWhereHas(string $relation, Closure $callback = null, string $operator = '>=', int $count = 1)
 * @method static Builder<Game> whereDoesntHave(string $relation, Closure $callback = null)
 * @method static Builder<Game> whereRaw(string $sql, array $bindings = [])
 * @method static Builder<Game> groupBy(string $groupBy)
 * @method static Builder<Game> orderBy(string $column, string $direction = 'asc')
 * @method static Builder<Game> orderByDesc(string $column)
 * @method static Builder<Game> orderByRaw(string $sql, array $bindings = [])
 * @method static Builder<Game> limit(int $value)
 * @method static int count(array $columns = ['*'])
 * @method static bool exists()
 *
 * @property int $current_round
 * @property int $number_of_rounds
 * @property int $round_duration_seconds
 * @property int $round_result_duration_seconds
 * @property bool $is_forced_start
 * @property bool $is_country_restricted
 * @property string $id
 * @property string $created_at
 * @property string $updated_at
 * @property string $created_by_user_id
 * @property string|null $name
 * @property CarbonInterface|null $next_round_at
 * @property CarbonInterface|null $round_ends_at
 * @property GameStateEnum $game_state_enum
 * @property PanoramaTagEnum|null $panorama_tag_enum
 * @property GamePublicStatusEnum $game_public_status_enum
 *
 * @property BearUser $createdByUser
 * @property GameState $gameState
 * @property PanoramaTag|null $panoramaTag
 * @property GamePublicStatus $gamePublicStatus
 *
 * AUTO GENERATED FILE DO NOT MODIFY
 */
final class Game extends Model {
    use BearDatabaseChangeTrait;

    protected $connection = 'pgsql';
    protected $table = 'game';
    protected $keyType = 'string';
    protected $dateFormat = 'Y-m-d\TH:i:sP';

    /** @var array<string, string> $casts */
    protected $casts = [
        'game_public_status_enum' => GamePublicStatusEnum::class,
        'game_state_enum' => GameStateEnum::class,
        'next_round_at' => 'immutable_datetime',
        'panorama_tag_enum' => PanoramaTagEnum::class,
        'round_ends_at' => 'immutable_datetime',
    ];

    /** @return BelongsTo<BearUser, self> */
    public function createdByUser(): BelongsTo {
        return $this->belongsTo(related: BearUser::class, foreignKey: 'created_by_user_id', ownerKey: 'id');
    }

    /** @return BelongsTo<GameState, self> */
    public function gameState(): BelongsTo {
        return $this->belongsTo(related: GameState::class, foreignKey: 'game_state_enum', ownerKey: 'enum');
    }

    /** @return BelongsTo<PanoramaTag, self>|null */
    public function panoramaTag(): BelongsTo|null {
        return $this->belongsTo(related: PanoramaTag::class, foreignKey: 'panorama_tag_enum', ownerKey: 'enum');
    }

    /** @return BelongsTo<GamePublicStatus, self> */
    public function gamePublicStatus(): BelongsTo {
        return $this->belongsTo(related: GamePublicStatus::class, foreignKey: 'game_public_status_enum', ownerKey: 'enum');
    }

    protected $guarded = ['id', 'updated_at', 'created_at', 'deleted_at'];
}
