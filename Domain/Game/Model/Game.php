<?php declare(strict_types=1);

namespace Domain\Game\Model;

use Carbon\CarbonInterface;
use Closure;
use GuardsmanPanda\Larabear\Infrastructure\Auth\Model\BearUser;
use GuardsmanPanda\Larabear\Infrastructure\Database\Traits\BearLogDatabaseChanges;
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
 * @method static Collection<int|string, Game> pluck(string $column, string $key = null)
 * @method static Collection<int, Game> fromQuery(string $query, array $bindings = [])
 * @method static Game lockForUpdate()
 * @method static Game select(array $columns = ['*'])
 * @method static Game selectRaw(string $expression, array $bindings = [])
 * @method static Game with(array $relations)
 * @method static Game leftJoin(string $table, string $first, string $operator = null, string $second = null)
 * @method static Game where(string $column, string $operator = null, string|float|int|bool $value = null)
 * @method static Game whereIn(string $column, array $values)
 * @method static Game whereNull(string|array $columns)
 * @method static Game whereNotNull(string|array $columns)
 * @method static Game whereYear(string $column, string $operator, CarbonInterface|string|int $value)
 * @method static Game whereMonth(string $column, string $operator, CarbonInterface|string|int $value)
 * @method static Game whereDate(string $column, string $operator, CarbonInterface|string $value)
 * @method static Game whereExists(Closure $callback)
 * @method static Game whereNotExists(Closure $callback)
 * @method static Game whereHas(string $relation, Closure $callback = null, string $operator = '>=', int $count = 1)
 * @method static Game withWhereHas(string $relation, Closure $callback = null, string $operator = '>=', int $count = 1)
 * @method static Game whereDoesntHave(string $relation, Closure $callback = null)
 * @method static Game whereRaw(string $sql, array $bindings = [])
 * @method static Game groupBy(string $groupBy)
 * @method static Game orderBy(string $column, string $direction = 'asc')
 * @method static Game orderByDesc(string $column)
 * @method static Game orderByRaw(string $sql, array $bindings = [])
 * @method static Game limit(int $value)
 * @method static int count(array $columns = ['*'])
 * @method static mixed sum(string $column)
 * @method static bool exists()
 *
 * @property int $round_duration
 * @property int $number_of_rounds
 * @property int|null $current_round
 * @property bool $is_public
 * @property bool $is_forced_start
 * @property string $id
 * @property string $created_at
 * @property string $updated_at
 * @property string $game_state_enum
 * @property string $created_by_user_id
 * @property CarbonInterface|null $next_round_at
 * @property CarbonInterface|null $round_ends_at
 *
 * @property BearUser $createdByUser
 * @property GameState $gameStateEnum
 *
 * AUTO GENERATED FILE DO NOT MODIFY
 */
final class Game extends Model {
    use BearLogDatabaseChanges;

    protected $connection = 'pgsql';
    protected $table = 'game';
    protected $keyType = 'string';
    protected $dateFormat = 'Y-m-d\TH:i:sP';

    /** @var array<string, string> $casts */
    protected $casts = [
        'next_round_at' => 'immutable_datetime',
        'round_ends_at' => 'immutable_datetime',
    ];

    public function createdByUser(): BelongsTo {
        return $this->belongsTo(related: BearUser::class, foreignKey: 'created_by_user_id', ownerKey: 'id');
    }

    public function gameStateEnum(): BelongsTo {
        return $this->belongsTo(related: GameState::class, foreignKey: 'game_state_enum', ownerKey: 'game_state_enum');
    }

    protected $guarded = ['id', 'updated_at', 'created_at', 'deleted_at'];
}
