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
 * @method static UserFlag|null find(string $id, array $columns = ['*'])
 * @method static UserFlag findOrFail(string $id, array $columns = ['*'])
 * @method static UserFlag sole(array $columns = ['*'])
 * @method static UserFlag|null first(array $columns = ['*'])
 * @method static UserFlag firstOrFail(array $columns = ['*'])
 * @method static UserFlag firstOrCreate(array $filter, array $values)
 * @method static UserFlag firstOrNew(array $filter, array $values)
 * @method static UserFlag|null firstWhere(string $column, string $operator, string|float|int|bool $value)
 * @method static Collection<int, UserFlag> all(array $columns = ['*'])
 * @method static Collection<int, UserFlag> get(array $columns = ['*'])
 * @method static Collection<int, UserFlag> fromQuery(string $query, array $bindings = [])
 * @method static Builder<UserFlag> lockForUpdate()
 * @method static Builder<UserFlag> select(array $columns = ['*'])
 * @method static Builder<UserFlag> selectRaw(string $expression, array $bindings = [])
 * @method static Builder<UserFlag> with(array $relations)
 * @method static Builder<UserFlag> leftJoin(string $table, string $first, string $operator = null, string $second = null)
 * @method static Builder<UserFlag> where(string $column, string $operator = null, string|float|int|bool $value = null)
 * @method static Builder<UserFlag> whereIn(string $column, array $values)
 * @method static Builder<UserFlag> whereNull(string|array $columns)
 * @method static Builder<UserFlag> whereNotNull(string|array $columns)
 * @method static Builder<UserFlag> whereYear(string $column, string $operator, CarbonInterface|string|int $value)
 * @method static Builder<UserFlag> whereMonth(string $column, string $operator, CarbonInterface|string|int $value)
 * @method static Builder<UserFlag> whereDate(string $column, string $operator, CarbonInterface|string $value)
 * @method static Builder<UserFlag> whereExists(Closure $callback)
 * @method static Builder<UserFlag> whereNotExists(Closure $callback)
 * @method static Builder<UserFlag> whereHas(string $relation, Closure $callback = null, string $operator = '>=', int $count = 1)
 * @method static Builder<UserFlag> withWhereHas(string $relation, Closure $callback = null, string $operator = '>=', int $count = 1)
 * @method static Builder<UserFlag> whereDoesntHave(string $relation, Closure $callback = null)
 * @method static Builder<UserFlag> whereRaw(string $sql, array $bindings = [])
 * @method static Builder<UserFlag> groupBy(string $groupBy)
 * @method static Builder<UserFlag> orderBy(string $column, string $direction = 'asc')
 * @method static Builder<UserFlag> orderByDesc(string $column)
 * @method static Builder<UserFlag> orderByRaw(string $sql, array $bindings = [])
 * @method static Builder<UserFlag> limit(int $value)
 * @method static int count(array $columns = ['*'])
 * @method static bool exists()
 *
 * @property string $enum
 * @property string $file_path
 * @property string $created_at
 * @property string $description
 *
 * AUTO GENERATED FILE DO NOT MODIFY
 */
final class UserFlag extends Model {
    use BearDatabaseChangeTrait;

    protected $connection = 'pgsql';
    protected $table = 'user_flag';
    protected $primaryKey = 'enum';
    protected $keyType = 'string';
    protected $dateFormat = 'Y-m-d\TH:i:sP';
    public $timestamps = false;

    protected $guarded = ['enum', 'updated_at', 'created_at', 'deleted_at'];
}
