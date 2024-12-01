<?php declare(strict_types=1);

namespace Domain\Game\Model;

use Carbon\CarbonInterface;
use Closure;
use GuardsmanPanda\Larabear\Infrastructure\Database\Traits\BearDatabaseChangeTrait;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * AUTO GENERATED FILE DO NOT MODIFY
 *
 * @method static GameState|null find(string $id, array $columns = ['*'])
 * @method static GameState findOrFail(string $id, array $columns = ['*'])
 * @method static GameState sole(array $columns = ['*'])
 * @method static GameState|null first(array $columns = ['*'])
 * @method static GameState firstOrFail(array $columns = ['*'])
 * @method static GameState firstOrCreate(array $filter, array $values)
 * @method static GameState firstOrNew(array $filter, array $values)
 * @method static GameState|null firstWhere(string $column, string $operator, string|float|int|bool $value)
 * @method static Collection<int, GameState> all(array $columns = ['*'])
 * @method static Collection<int, GameState> get(array $columns = ['*'])
 * @method static Collection<int, GameState> fromQuery(string $query, array $bindings = [])
 * @method static Builder<GameState> lockForUpdate()
 * @method static Builder<GameState> select(array $columns = ['*'])
 * @method static Builder<GameState> selectRaw(string $expression, array $bindings = [])
 * @method static Builder<GameState> with(array $relations)
 * @method static Builder<GameState> leftJoin(string $table, string $first, string $operator = null, string $second = null)
 * @method static Builder<GameState> where(string $column, string $operator = null, string|float|int|bool $value = null)
 * @method static Builder<GameState> whereIn(string $column, array $values)
 * @method static Builder<GameState> whereNull(string|array $columns)
 * @method static Builder<GameState> whereNotNull(string|array $columns)
 * @method static Builder<GameState> whereYear(string $column, string $operator, CarbonInterface|string|int $value)
 * @method static Builder<GameState> whereMonth(string $column, string $operator, CarbonInterface|string|int $value)
 * @method static Builder<GameState> whereDate(string $column, string $operator, CarbonInterface|string $value)
 * @method static Builder<GameState> whereExists(Closure $callback)
 * @method static Builder<GameState> whereNotExists(Closure $callback)
 * @method static Builder<GameState> whereHas(string $relation, Closure $callback = null, string $operator = '>=', int $count = 1)
 * @method static Builder<GameState> withWhereHas(string $relation, Closure $callback = null, string $operator = '>=', int $count = 1)
 * @method static Builder<GameState> whereDoesntHave(string $relation, Closure $callback = null)
 * @method static Builder<GameState> whereRaw(string $sql, array $bindings = [])
 * @method static Builder<GameState> groupBy(string $groupBy)
 * @method static Builder<GameState> orderBy(string $column, string $direction = 'asc')
 * @method static Builder<GameState> orderByDesc(string $column)
 * @method static Builder<GameState> orderByRaw(string $sql, array $bindings = [])
 * @method static Builder<GameState> limit(int $value)
 * @method static int count(array $columns = ['*'])
 * @method static bool exists()
 *
 * @property bool|null $is_lobby
 * @property bool|null $is_playing
 * @property bool|null $is_finished
 * @property bool|null $is_multiplayer
 * @property string $enum
 * @property string $created_at
 * @property string $description
 *
 * AUTO GENERATED FILE DO NOT MODIFY
 */
final class GameState extends Model {
    use BearDatabaseChangeTrait;

    protected $connection = 'pgsql';
    protected $table = 'game_state';
    protected $primaryKey = 'enum';
    protected $keyType = 'string';
    protected $dateFormat = 'Y-m-d\TH:i:sP';
    public $timestamps = false;

    protected $guarded = ['enum', 'updated_at', 'created_at', 'deleted_at'];
}
