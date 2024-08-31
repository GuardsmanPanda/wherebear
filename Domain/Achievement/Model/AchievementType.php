<?php declare(strict_types=1);

namespace Domain\Achievement\Model;

use Carbon\CarbonInterface;
use Closure;
use GuardsmanPanda\Larabear\Infrastructure\Database\Traits\BearDatabaseChangeTrait;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * AUTO GENERATED FILE DO NOT MODIFY
 *
 * @method static AchievementType|null find(string $id, array $columns = ['*'])
 * @method static AchievementType findOrFail(string $id, array $columns = ['*'])
 * @method static AchievementType sole(array $columns = ['*'])
 * @method static AchievementType|null first(array $columns = ['*'])
 * @method static AchievementType firstOrFail(array $columns = ['*'])
 * @method static AchievementType firstOrCreate(array $filter, array $values)
 * @method static AchievementType firstOrNew(array $filter, array $values)
 * @method static AchievementType|null firstWhere(string $column, string $operator, string|float|int|bool $value)
 * @method static Collection<int, AchievementType> all(array $columns = ['*'])
 * @method static Collection<int, AchievementType> get(array $columns = ['*'])
 * @method static Collection<int, AchievementType> fromQuery(string $query, array $bindings = [])
 * @method static Builder<AchievementType> lockForUpdate()
 * @method static Builder<AchievementType> select(array $columns = ['*'])
 * @method static Builder<AchievementType> selectRaw(string $expression, array $bindings = [])
 * @method static Builder<AchievementType> with(array $relations)
 * @method static Builder<AchievementType> leftJoin(string $table, string $first, string $operator = null, string $second = null)
 * @method static Builder<AchievementType> where(string $column, string $operator = null, string|float|int|bool $value = null)
 * @method static Builder<AchievementType> whereIn(string $column, array $values)
 * @method static Builder<AchievementType> whereNull(string|array $columns)
 * @method static Builder<AchievementType> whereNotNull(string|array $columns)
 * @method static Builder<AchievementType> whereYear(string $column, string $operator, CarbonInterface|string|int $value)
 * @method static Builder<AchievementType> whereMonth(string $column, string $operator, CarbonInterface|string|int $value)
 * @method static Builder<AchievementType> whereDate(string $column, string $operator, CarbonInterface|string $value)
 * @method static Builder<AchievementType> whereExists(Closure $callback)
 * @method static Builder<AchievementType> whereNotExists(Closure $callback)
 * @method static Builder<AchievementType> whereHas(string $relation, Closure $callback = null, string $operator = '>=', int $count = 1)
 * @method static Builder<AchievementType> withWhereHas(string $relation, Closure $callback = null, string $operator = '>=', int $count = 1)
 * @method static Builder<AchievementType> whereDoesntHave(string $relation, Closure $callback = null)
 * @method static Builder<AchievementType> whereRaw(string $sql, array $bindings = [])
 * @method static Builder<AchievementType> groupBy(string $groupBy)
 * @method static Builder<AchievementType> orderBy(string $column, string $direction = 'asc')
 * @method static Builder<AchievementType> orderByDesc(string $column)
 * @method static Builder<AchievementType> orderByRaw(string $sql, array $bindings = [])
 * @method static Builder<AchievementType> limit(int $value)
 * @method static int count(array $columns = ['*'])
 * @method static bool exists()
 *
 * @property string $enum
 * @property string $created_at
 * @property string $description
 *
 * AUTO GENERATED FILE DO NOT MODIFY
 */
final class AchievementType extends Model {
    use BearDatabaseChangeTrait;

    protected $connection = 'pgsql';
    protected $table = 'achievement_type';
    protected $primaryKey = 'enum';
    protected $keyType = 'string';
    protected $dateFormat = 'Y-m-d\TH:i:sP';
    public $timestamps = false;

    protected $guarded = ['enum', 'updated_at', 'created_at', 'deleted_at'];
}
