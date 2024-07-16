<?php declare(strict_types=1);

namespace Domain\User\Model;

use Carbon\CarbonInterface;
use Closure;
use GuardsmanPanda\Larabear\Infrastructure\Database\Traits\BearLogDatabaseChanges;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * AUTO GENERATED FILE DO NOT MODIFY
 *
 * @method static UserLevel|null find(int $id, array $columns = ['*'])
 * @method static UserLevel findOrFail(int $id, array $columns = ['*'])
 * @method static UserLevel sole(array $columns = ['*'])
 * @method static UserLevel|null first(array $columns = ['*'])
 * @method static UserLevel firstOrFail(array $columns = ['*'])
 * @method static UserLevel firstOrCreate(array $filter, array $values)
 * @method static UserLevel firstOrNew(array $filter, array $values)
 * @method static UserLevel|null firstWhere(string $column, string $operator, string|float|int|bool $value)
 * @method static Collection<int, UserLevel> all(array $columns = ['*'])
 * @method static Collection<int, UserLevel> get(array $columns = ['*'])
 * @method static Collection<int|string, UserLevel> pluck(string $column, string $key = null)
 * @method static Collection<int, UserLevel> fromQuery(string $query, array $bindings = [])
 * @method static UserLevel lockForUpdate()
 * @method static UserLevel select(array $columns = ['*'])
 * @method static UserLevel selectRaw(string $expression, array $bindings = [])
 * @method static UserLevel with(array $relations)
 * @method static UserLevel leftJoin(string $table, string $first, string $operator = null, string $second = null)
 * @method static UserLevel where(string $column, string $operator = null, string|float|int|bool $value = null)
 * @method static UserLevel whereIn(string $column, array $values)
 * @method static UserLevel whereNull(string|array $columns)
 * @method static UserLevel whereNotNull(string|array $columns)
 * @method static UserLevel whereYear(string $column, string $operator, CarbonInterface|string|int $value)
 * @method static UserLevel whereMonth(string $column, string $operator, CarbonInterface|string|int $value)
 * @method static UserLevel whereDate(string $column, string $operator, CarbonInterface|string $value)
 * @method static UserLevel whereExists(Closure $callback)
 * @method static UserLevel whereNotExists(Closure $callback)
 * @method static UserLevel whereHas(string $relation, Closure $callback = null, string $operator = '>=', int $count = 1)
 * @method static UserLevel withWhereHas(string $relation, Closure $callback = null, string $operator = '>=', int $count = 1)
 * @method static UserLevel whereDoesntHave(string $relation, Closure $callback = null)
 * @method static UserLevel whereRaw(string $sql, array $bindings = [])
 * @method static UserLevel groupBy(string $groupBy)
 * @method static UserLevel orderBy(string $column, string $direction = 'asc')
 * @method static UserLevel orderByDesc(string $column)
 * @method static UserLevel orderByRaw(string $sql, array $bindings = [])
 * @method static UserLevel limit(int $value)
 * @method static int count(array $columns = ['*'])
 * @method static mixed sum(string $column)
 * @method static bool exists()
 *
 * @property int $id
 * @property int $experience_requirement
 * @property string $created_at
 * @property string $updated_at
 * @property string|null $feature_unlock
 *
 * AUTO GENERATED FILE DO NOT MODIFY
 */
final class UserLevel extends Model {
    use BearLogDatabaseChanges;

    protected $connection = 'pgsql';
    protected $table = 'user_level';
    protected $dateFormat = 'Y-m-d\TH:i:sP';

    protected $guarded = ['id', 'updated_at', 'created_at', 'deleted_at'];
}
