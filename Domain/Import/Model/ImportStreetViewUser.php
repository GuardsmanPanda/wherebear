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
 * @method static ImportStreetViewUser|null find(string $id, array $columns = ['*'])
 * @method static ImportStreetViewUser findOrFail(string $id, array $columns = ['*'])
 * @method static ImportStreetViewUser sole(array $columns = ['*'])
 * @method static ImportStreetViewUser|null first(array $columns = ['*'])
 * @method static ImportStreetViewUser firstOrFail(array $columns = ['*'])
 * @method static ImportStreetViewUser firstOrCreate(array $filter, array $values)
 * @method static ImportStreetViewUser firstOrNew(array $filter, array $values)
 * @method static ImportStreetViewUser|null firstWhere(string $column, string $operator, string|float|int|bool $value)
 * @method static Collection<int, ImportStreetViewUser> all(array $columns = ['*'])
 * @method static Collection<int, ImportStreetViewUser> get(array $columns = ['*'])
 * @method static Collection<int, ImportStreetViewUser> fromQuery(string $query, array $bindings = [])
 * @method static Builder<ImportStreetViewUser> lockForUpdate()
 * @method static Builder<ImportStreetViewUser> select(array $columns = ['*'])
 * @method static Builder<ImportStreetViewUser> selectRaw(string $expression, array $bindings = [])
 * @method static Builder<ImportStreetViewUser> with(array $relations)
 * @method static Builder<ImportStreetViewUser> leftJoin(string $table, string $first, string $operator = null, string $second = null)
 * @method static Builder<ImportStreetViewUser> where(string $column, string $operator = null, string|float|int|bool $value = null)
 * @method static Builder<ImportStreetViewUser> whereIn(string $column, array $values)
 * @method static Builder<ImportStreetViewUser> whereNull(string|array $columns)
 * @method static Builder<ImportStreetViewUser> whereNotNull(string|array $columns)
 * @method static Builder<ImportStreetViewUser> whereYear(string $column, string $operator, CarbonInterface|string|int $value)
 * @method static Builder<ImportStreetViewUser> whereMonth(string $column, string $operator, CarbonInterface|string|int $value)
 * @method static Builder<ImportStreetViewUser> whereDate(string $column, string $operator, CarbonInterface|string $value)
 * @method static Builder<ImportStreetViewUser> whereExists(Closure $callback)
 * @method static Builder<ImportStreetViewUser> whereNotExists(Closure $callback)
 * @method static Builder<ImportStreetViewUser> whereHas(string $relation, Closure $callback = null, string $operator = '>=', int $count = 1)
 * @method static Builder<ImportStreetViewUser> withWhereHas(string $relation, Closure $callback = null, string $operator = '>=', int $count = 1)
 * @method static Builder<ImportStreetViewUser> whereDoesntHave(string $relation, Closure $callback = null)
 * @method static Builder<ImportStreetViewUser> whereRaw(string $sql, array $bindings = [])
 * @method static Builder<ImportStreetViewUser> groupBy(string $groupBy)
 * @method static Builder<ImportStreetViewUser> orderBy(string $column, string $direction = 'asc')
 * @method static Builder<ImportStreetViewUser> orderByDesc(string $column)
 * @method static Builder<ImportStreetViewUser> orderByRaw(string $sql, array $bindings = [])
 * @method static Builder<ImportStreetViewUser> limit(int $value)
 * @method static int count(array $columns = ['*'])
 * @method static bool exists()
 *
 * @property string $id
 * @property string $name
 * @property string $created_at
 * @property string $updated_at
 * @property string|null $continue_token
 * @property CarbonInterface|null $last_sync_at
 *
 * AUTO GENERATED FILE DO NOT MODIFY
 */
final class ImportStreetViewUser extends Model {
    use BearDatabaseChangeTrait;

    protected $connection = 'pgsql';
    protected $table = 'import_street_view_user';
    protected $keyType = 'string';
    protected $dateFormat = 'Y-m-d\TH:i:sP';

    /** @var array<string, string> $casts */
    protected $casts = [
        'last_sync_at' => 'immutable_datetime',
    ];

    protected $guarded = ['id', 'updated_at', 'created_at', 'deleted_at'];
}
