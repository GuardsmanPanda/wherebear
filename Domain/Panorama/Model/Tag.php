<?php declare(strict_types=1);

namespace Domain\Panorama\Model;

use Carbon\CarbonInterface;
use Closure;
use GuardsmanPanda\Larabear\Infrastructure\Database\Traits\BearDatabaseChangeTrait;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * AUTO GENERATED FILE DO NOT MODIFY
 *
 * @method static Tag|null find(string $id, array $columns = ['*'])
 * @method static Tag findOrFail(string $id, array $columns = ['*'])
 * @method static Tag sole(array $columns = ['*'])
 * @method static Tag|null first(array $columns = ['*'])
 * @method static Tag firstOrFail(array $columns = ['*'])
 * @method static Tag firstOrCreate(array $filter, array $values)
 * @method static Tag firstOrNew(array $filter, array $values)
 * @method static Tag|null firstWhere(string $column, string $operator, string|float|int|bool $value)
 * @method static Collection<int, Tag> all(array $columns = ['*'])
 * @method static Collection<int, Tag> get(array $columns = ['*'])
 * @method static Collection<int|string, Tag> pluck(string $column, string $key = null)
 * @method static Collection<int, Tag> fromQuery(string $query, array $bindings = [])
 * @method static Builder<Tag> lockForUpdate()
 * @method static Builder<Tag> select(array $columns = ['*'])
 * @method static Builder<Tag> selectRaw(string $expression, array $bindings = [])
 * @method static Builder<Tag> with(array $relations)
 * @method static Builder<Tag> leftJoin(string $table, string $first, string $operator = null, string $second = null)
 * @method static Builder<Tag> where(string $column, string $operator = null, string|float|int|bool $value = null)
 * @method static Builder<Tag> whereIn(string $column, array $values)
 * @method static Builder<Tag> whereNull(string|array $columns)
 * @method static Builder<Tag> whereNotNull(string|array $columns)
 * @method static Builder<Tag> whereYear(string $column, string $operator, CarbonInterface|string|int $value)
 * @method static Builder<Tag> whereMonth(string $column, string $operator, CarbonInterface|string|int $value)
 * @method static Builder<Tag> whereDate(string $column, string $operator, CarbonInterface|string $value)
 * @method static Builder<Tag> whereExists(Closure $callback)
 * @method static Builder<Tag> whereNotExists(Closure $callback)
 * @method static Builder<Tag> whereHas(string $relation, Closure $callback = null, string $operator = '>=', int $count = 1)
 * @method static Builder<Tag> withWhereHas(string $relation, Closure $callback = null, string $operator = '>=', int $count = 1)
 * @method static Builder<Tag> whereDoesntHave(string $relation, Closure $callback = null)
 * @method static Builder<Tag> whereRaw(string $sql, array $bindings = [])
 * @method static Builder<Tag> groupBy(string $groupBy)
 * @method static Builder<Tag> orderBy(string $column, string $direction = 'asc')
 * @method static Builder<Tag> orderByDesc(string $column)
 * @method static Builder<Tag> orderByRaw(string $sql, array $bindings = [])
 * @method static Builder<Tag> limit(int $value)
 * @method static int count(array $columns = ['*'])
 * @method static bool exists()
 *
 * @property string $enum
 * @property string $created_at
 * @property string $description
 *
 * AUTO GENERATED FILE DO NOT MODIFY
 */
final class Tag extends Model {
    use BearDatabaseChangeTrait;

    protected $connection = 'pgsql';
    protected $table = 'tag';
    protected $primaryKey = 'enum';
    protected $keyType = 'string';
    protected $dateFormat = 'Y-m-d\TH:i:sP';
    public $timestamps = false;

    protected $guarded = ['enum', 'updated_at', 'created_at', 'deleted_at'];
}
