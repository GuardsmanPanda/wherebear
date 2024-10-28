<?php declare(strict_types=1);

namespace Domain\Import\Model;

use Carbon\CarbonInterface;
use Closure;
use GuardsmanPanda\Larabear\Infrastructure\Database\Traits\BearDatabaseChangeTrait;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * AUTO GENERATED FILE DO NOT MODIFY
 *
 * @method static ImportStatus|null find(string $id, array $columns = ['*'])
 * @method static ImportStatus findOrFail(string $id, array $columns = ['*'])
 * @method static ImportStatus sole(array $columns = ['*'])
 * @method static ImportStatus|null first(array $columns = ['*'])
 * @method static ImportStatus firstOrFail(array $columns = ['*'])
 * @method static ImportStatus firstOrCreate(array $filter, array $values)
 * @method static ImportStatus firstOrNew(array $filter, array $values)
 * @method static ImportStatus|null firstWhere(string $column, string $operator, string|float|int|bool $value)
 * @method static Collection<int, ImportStatus> all(array $columns = ['*'])
 * @method static Collection<int, ImportStatus> get(array $columns = ['*'])
 * @method static Collection<int, ImportStatus> fromQuery(string $query, array $bindings = [])
 * @method static Builder<ImportStatus> lockForUpdate()
 * @method static Builder<ImportStatus> select(array $columns = ['*'])
 * @method static Builder<ImportStatus> selectRaw(string $expression, array $bindings = [])
 * @method static Builder<ImportStatus> with(array $relations)
 * @method static Builder<ImportStatus> leftJoin(string $table, string $first, string $operator = null, string $second = null)
 * @method static Builder<ImportStatus> where(string $column, string $operator = null, string|float|int|bool $value = null)
 * @method static Builder<ImportStatus> whereIn(string $column, array $values)
 * @method static Builder<ImportStatus> whereNull(string|array $columns)
 * @method static Builder<ImportStatus> whereNotNull(string|array $columns)
 * @method static Builder<ImportStatus> whereYear(string $column, string $operator, CarbonInterface|string|int $value)
 * @method static Builder<ImportStatus> whereMonth(string $column, string $operator, CarbonInterface|string|int $value)
 * @method static Builder<ImportStatus> whereDate(string $column, string $operator, CarbonInterface|string $value)
 * @method static Builder<ImportStatus> whereExists(Closure $callback)
 * @method static Builder<ImportStatus> whereNotExists(Closure $callback)
 * @method static Builder<ImportStatus> whereHas(string $relation, Closure $callback = null, string $operator = '>=', int $count = 1)
 * @method static Builder<ImportStatus> withWhereHas(string $relation, Closure $callback = null, string $operator = '>=', int $count = 1)
 * @method static Builder<ImportStatus> whereDoesntHave(string $relation, Closure $callback = null)
 * @method static Builder<ImportStatus> whereRaw(string $sql, array $bindings = [])
 * @method static Builder<ImportStatus> groupBy(string $groupBy)
 * @method static Builder<ImportStatus> orderBy(string $column, string $direction = 'asc')
 * @method static Builder<ImportStatus> orderByDesc(string $column)
 * @method static Builder<ImportStatus> orderByRaw(string $sql, array $bindings = [])
 * @method static Builder<ImportStatus> limit(int $value)
 * @method static int count(array $columns = ['*'])
 * @method static bool exists()
 *
 * @property string $enum
 * @property string $created_at
 * @property string $description
 *
 * AUTO GENERATED FILE DO NOT MODIFY
 */
final class ImportStatus extends Model {
    use BearDatabaseChangeTrait;

    protected $connection = 'pgsql';
    protected $table = 'import_status';
    protected $primaryKey = 'enum';
    protected $keyType = 'string';
    protected $dateFormat = 'Y-m-d\TH:i:sP';
    public $timestamps = false;

    protected $guarded = ['enum', 'updated_at', 'created_at', 'deleted_at'];
}
