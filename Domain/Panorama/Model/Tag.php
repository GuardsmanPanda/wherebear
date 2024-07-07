<?php declare(strict_types=1);

namespace Domain\Panorama\Model;

use Carbon\CarbonInterface;
use Closure;
use GuardsmanPanda\Larabear\Infrastructure\Database\Traits\BearLogDatabaseChanges;
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
 * @method static Tag lockForUpdate()
 * @method static Tag select(array $columns = ['*'])
 * @method static Tag selectRaw(string $expression, array $bindings = [])
 * @method static Tag with(array $relations)
 * @method static Tag leftJoin(string $table, string $first, string $operator = null, string $second = null)
 * @method static Tag where(string $column, string $operator = null, string|float|int|bool $value = null)
 * @method static Tag whereIn(string $column, array $values)
 * @method static Tag whereNull(string|array $columns)
 * @method static Tag whereNotNull(string|array $columns)
 * @method static Tag whereYear(string $column, string $operator, CarbonInterface|string|int $value)
 * @method static Tag whereMonth(string $column, string $operator, CarbonInterface|string|int $value)
 * @method static Tag whereDate(string $column, string $operator, CarbonInterface|string $value)
 * @method static Tag whereExists(Closure $callback)
 * @method static Tag whereNotExists(Closure $callback)
 * @method static Tag whereHas(string $relation, Closure $callback = null, string $operator = '>=', int $count = 1)
 * @method static Tag withWhereHas(string $relation, Closure $callback = null, string $operator = '>=', int $count = 1)
 * @method static Tag whereDoesntHave(string $relation, Closure $callback = null)
 * @method static Tag whereRaw(string $sql, array $bindings = [])
 * @method static Tag groupBy(string $groupBy)
 * @method static Tag orderBy(string $column, string $direction = 'asc')
 * @method static Tag orderByDesc(string $column)
 * @method static Tag orderByRaw(string $sql, array $bindings = [])
 * @method static Tag limit(int $value)
 * @method static int count(array $columns = ['*'])
 * @method static mixed sum(string $column)
 * @method static bool exists()
 *
 * @property string $tag_enum
 * @property string $created_at
 * @property string $tag_description
 *
 * AUTO GENERATED FILE DO NOT MODIFY
 */
final class Tag extends Model {
    use BearLogDatabaseChanges;

    protected $connection = 'pgsql';
    protected $table = 'tag';
    protected $primaryKey = 'tag_enum';
    protected $keyType = 'string';
    protected $dateFormat = 'Y-m-d\TH:i:sP';
    public $timestamps = false;

    protected $guarded = ['tag_enum', 'updated_at', 'created_at', 'deleted_at'];
}
