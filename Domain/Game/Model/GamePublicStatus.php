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
 * @method static GamePublicStatus|null find(string $id, array $columns = ['*'])
 * @method static GamePublicStatus findOrFail(string $id, array $columns = ['*'])
 * @method static GamePublicStatus sole(array $columns = ['*'])
 * @method static GamePublicStatus|null first(array $columns = ['*'])
 * @method static GamePublicStatus firstOrFail(array $columns = ['*'])
 * @method static GamePublicStatus firstOrCreate(array $filter, array $values)
 * @method static GamePublicStatus firstOrNew(array $filter, array $values)
 * @method static GamePublicStatus|null firstWhere(string $column, string $operator, string|float|int|bool $value)
 * @method static Collection<int, GamePublicStatus> all(array $columns = ['*'])
 * @method static Collection<int, GamePublicStatus> get(array $columns = ['*'])
 * @method static Collection<int|string, GamePublicStatus> pluck(string $column, string $key = null)
 * @method static Collection<int, GamePublicStatus> fromQuery(string $query, array $bindings = [])
 * @method static Builder<GamePublicStatus> lockForUpdate()
 * @method static Builder<GamePublicStatus> select(array $columns = ['*'])
 * @method static Builder<GamePublicStatus> selectRaw(string $expression, array $bindings = [])
 * @method static Builder<GamePublicStatus> with(array $relations)
 * @method static Builder<GamePublicStatus> leftJoin(string $table, string $first, string $operator = null, string $second = null)
 * @method static Builder<GamePublicStatus> where(string $column, string $operator = null, string|float|int|bool $value = null)
 * @method static Builder<GamePublicStatus> whereIn(string $column, array $values)
 * @method static Builder<GamePublicStatus> whereNull(string|array $columns)
 * @method static Builder<GamePublicStatus> whereNotNull(string|array $columns)
 * @method static Builder<GamePublicStatus> whereYear(string $column, string $operator, CarbonInterface|string|int $value)
 * @method static Builder<GamePublicStatus> whereMonth(string $column, string $operator, CarbonInterface|string|int $value)
 * @method static Builder<GamePublicStatus> whereDate(string $column, string $operator, CarbonInterface|string $value)
 * @method static Builder<GamePublicStatus> whereExists(Closure $callback)
 * @method static Builder<GamePublicStatus> whereNotExists(Closure $callback)
 * @method static Builder<GamePublicStatus> whereHas(string $relation, Closure $callback = null, string $operator = '>=', int $count = 1)
 * @method static Builder<GamePublicStatus> withWhereHas(string $relation, Closure $callback = null, string $operator = '>=', int $count = 1)
 * @method static Builder<GamePublicStatus> whereDoesntHave(string $relation, Closure $callback = null)
 * @method static Builder<GamePublicStatus> whereRaw(string $sql, array $bindings = [])
 * @method static Builder<GamePublicStatus> groupBy(string $groupBy)
 * @method static Builder<GamePublicStatus> orderBy(string $column, string $direction = 'asc')
 * @method static Builder<GamePublicStatus> orderByDesc(string $column)
 * @method static Builder<GamePublicStatus> orderByRaw(string $sql, array $bindings = [])
 * @method static Builder<GamePublicStatus> limit(int $value)
 * @method static int count(array $columns = ['*'])
 * @method static bool exists()
 *
 * @property string $enum
 * @property string $created_at
 * @property string $description
 *
 * AUTO GENERATED FILE DO NOT MODIFY
 */
final class GamePublicStatus extends Model {
    use BearDatabaseChangeTrait;

    protected $connection = 'pgsql';
    protected $table = 'game_public_status';
    protected $primaryKey = 'enum';
    protected $keyType = 'string';
    protected $dateFormat = 'Y-m-d\TH:i:sP';
    public $timestamps = false;

    protected $guarded = ['enum', 'updated_at', 'created_at', 'deleted_at'];
}
