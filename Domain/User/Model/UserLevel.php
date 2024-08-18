<?php declare(strict_types=1);

namespace Domain\User\Model;

use Carbon\CarbonInterface;
use Closure;
use GuardsmanPanda\Larabear\Infrastructure\Database\Traits\BearDatabaseChangeTrait;
use Illuminate\Database\Eloquent\Builder;
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
 * @method static Collection<array-key, UserLevel> pluck(string $column, string $key = null)
 * @method static Collection<int, UserLevel> fromQuery(string $query, array $bindings = [])
 * @method static Builder<UserLevel> lockForUpdate()
 * @method static Builder<UserLevel> select(array $columns = ['*'])
 * @method static Builder<UserLevel> selectRaw(string $expression, array $bindings = [])
 * @method static Builder<UserLevel> with(array $relations)
 * @method static Builder<UserLevel> leftJoin(string $table, string $first, string $operator = null, string $second = null)
 * @method static Builder<UserLevel> where(string $column, string $operator = null, string|float|int|bool $value = null)
 * @method static Builder<UserLevel> whereIn(string $column, array $values)
 * @method static Builder<UserLevel> whereNull(string|array $columns)
 * @method static Builder<UserLevel> whereNotNull(string|array $columns)
 * @method static Builder<UserLevel> whereYear(string $column, string $operator, CarbonInterface|string|int $value)
 * @method static Builder<UserLevel> whereMonth(string $column, string $operator, CarbonInterface|string|int $value)
 * @method static Builder<UserLevel> whereDate(string $column, string $operator, CarbonInterface|string $value)
 * @method static Builder<UserLevel> whereExists(Closure $callback)
 * @method static Builder<UserLevel> whereNotExists(Closure $callback)
 * @method static Builder<UserLevel> whereHas(string $relation, Closure $callback = null, string $operator = '>=', int $count = 1)
 * @method static Builder<UserLevel> withWhereHas(string $relation, Closure $callback = null, string $operator = '>=', int $count = 1)
 * @method static Builder<UserLevel> whereDoesntHave(string $relation, Closure $callback = null)
 * @method static Builder<UserLevel> whereRaw(string $sql, array $bindings = [])
 * @method static Builder<UserLevel> groupBy(string $groupBy)
 * @method static Builder<UserLevel> orderBy(string $column, string $direction = 'asc')
 * @method static Builder<UserLevel> orderByDesc(string $column)
 * @method static Builder<UserLevel> orderByRaw(string $sql, array $bindings = [])
 * @method static Builder<UserLevel> limit(int $value)
 * @method static int count(array $columns = ['*'])
 * @method static bool exists()
 *
 * @property int $enum
 * @property int $experience_requirement
 * @property string $created_at
 * @property string $updated_at
 * @property string|null $feature_unlock
 *
 * AUTO GENERATED FILE DO NOT MODIFY
 */
final class UserLevel extends Model {
    use BearDatabaseChangeTrait;

    protected $connection = 'pgsql';
    protected $table = 'user_level';
    protected $primaryKey = 'enum';
    protected $dateFormat = 'Y-m-d\TH:i:sP';

    protected $guarded = ['enum', 'updated_at', 'created_at', 'deleted_at'];
}
