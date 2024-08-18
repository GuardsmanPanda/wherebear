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
 * @method static PanoramaTag|null find(string $id, array $columns = ['*'])
 * @method static PanoramaTag findOrFail(string $id, array $columns = ['*'])
 * @method static PanoramaTag sole(array $columns = ['*'])
 * @method static PanoramaTag|null first(array $columns = ['*'])
 * @method static PanoramaTag firstOrFail(array $columns = ['*'])
 * @method static PanoramaTag firstOrCreate(array $filter, array $values)
 * @method static PanoramaTag firstOrNew(array $filter, array $values)
 * @method static PanoramaTag|null firstWhere(string $column, string $operator, string|float|int|bool $value)
 * @method static Collection<int, PanoramaTag> all(array $columns = ['*'])
 * @method static Collection<int, PanoramaTag> get(array $columns = ['*'])
 * @method static Collection<array-key, PanoramaTag> pluck(string $column, string $key = null)
 * @method static Collection<int, PanoramaTag> fromQuery(string $query, array $bindings = [])
 * @method static Builder<PanoramaTag> lockForUpdate()
 * @method static Builder<PanoramaTag> select(array $columns = ['*'])
 * @method static Builder<PanoramaTag> selectRaw(string $expression, array $bindings = [])
 * @method static Builder<PanoramaTag> with(array $relations)
 * @method static Builder<PanoramaTag> leftJoin(string $table, string $first, string $operator = null, string $second = null)
 * @method static Builder<PanoramaTag> where(string $column, string $operator = null, string|float|int|bool $value = null)
 * @method static Builder<PanoramaTag> whereIn(string $column, array $values)
 * @method static Builder<PanoramaTag> whereNull(string|array $columns)
 * @method static Builder<PanoramaTag> whereNotNull(string|array $columns)
 * @method static Builder<PanoramaTag> whereYear(string $column, string $operator, CarbonInterface|string|int $value)
 * @method static Builder<PanoramaTag> whereMonth(string $column, string $operator, CarbonInterface|string|int $value)
 * @method static Builder<PanoramaTag> whereDate(string $column, string $operator, CarbonInterface|string $value)
 * @method static Builder<PanoramaTag> whereExists(Closure $callback)
 * @method static Builder<PanoramaTag> whereNotExists(Closure $callback)
 * @method static Builder<PanoramaTag> whereHas(string $relation, Closure $callback = null, string $operator = '>=', int $count = 1)
 * @method static Builder<PanoramaTag> withWhereHas(string $relation, Closure $callback = null, string $operator = '>=', int $count = 1)
 * @method static Builder<PanoramaTag> whereDoesntHave(string $relation, Closure $callback = null)
 * @method static Builder<PanoramaTag> whereRaw(string $sql, array $bindings = [])
 * @method static Builder<PanoramaTag> groupBy(string $groupBy)
 * @method static Builder<PanoramaTag> orderBy(string $column, string $direction = 'asc')
 * @method static Builder<PanoramaTag> orderByDesc(string $column)
 * @method static Builder<PanoramaTag> orderByRaw(string $sql, array $bindings = [])
 * @method static Builder<PanoramaTag> limit(int $value)
 * @method static int count(array $columns = ['*'])
 * @method static bool exists()
 *
 * @property string $enum
 * @property string $created_at
 * @property string $description
 *
 * AUTO GENERATED FILE DO NOT MODIFY
 */
final class PanoramaTag extends Model {
    use BearDatabaseChangeTrait;

    protected $connection = 'pgsql';
    protected $table = 'panorama_tag';
    protected $primaryKey = 'enum';
    protected $keyType = 'string';
    protected $dateFormat = 'Y-m-d\TH:i:sP';
    public $timestamps = false;

    protected $guarded = ['enum', 'updated_at', 'created_at', 'deleted_at'];
}
