<?php declare(strict_types=1);

namespace Domain\Map\Model;

use Carbon\CarbonInterface;
use Closure;
use GuardsmanPanda\Larabear\Infrastructure\Database\Traits\BearLogDatabaseChanges;
use GuardsmanPanda\Larabear\Integration\ExternalApi\Model\BearExternalApi;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * AUTO GENERATED FILE DO NOT MODIFY
 *
 * @method static MapStyle|null find(string $id, array $columns = ['*'])
 * @method static MapStyle findOrFail(string $id, array $columns = ['*'])
 * @method static MapStyle sole(array $columns = ['*'])
 * @method static MapStyle|null first(array $columns = ['*'])
 * @method static MapStyle firstOrFail(array $columns = ['*'])
 * @method static MapStyle firstOrCreate(array $filter, array $values)
 * @method static MapStyle firstOrNew(array $filter, array $values)
 * @method static MapStyle|null firstWhere(string $column, string $operator, string|float|int|bool $value)
 * @method static Collection<int, MapStyle> all(array $columns = ['*'])
 * @method static Collection<int, MapStyle> get(array $columns = ['*'])
 * @method static Collection<int|string, MapStyle> pluck(string $column, string $key = null)
 * @method static Collection<int, MapStyle> fromQuery(string $query, array $bindings = [])
 * @method static MapStyle lockForUpdate()
 * @method static MapStyle select(array $columns = ['*'])
 * @method static MapStyle selectRaw(string $expression, array $bindings = [])
 * @method static MapStyle with(array $relations)
 * @method static MapStyle leftJoin(string $table, string $first, string $operator = null, string $second = null)
 * @method static MapStyle where(string $column, string $operator = null, string|float|int|bool $value = null)
 * @method static MapStyle whereIn(string $column, array $values)
 * @method static MapStyle whereNull(string|array $columns)
 * @method static MapStyle whereNotNull(string|array $columns)
 * @method static MapStyle whereYear(string $column, string $operator, CarbonInterface|string|int $value)
 * @method static MapStyle whereMonth(string $column, string $operator, CarbonInterface|string|int $value)
 * @method static MapStyle whereDate(string $column, string $operator, CarbonInterface|string $value)
 * @method static MapStyle whereExists(Closure $callback)
 * @method static MapStyle whereNotExists(Closure $callback)
 * @method static MapStyle whereHas(string $relation, Closure $callback = null, string $operator = '>=', int $count = 1)
 * @method static MapStyle withWhereHas(string $relation, Closure $callback = null, string $operator = '>=', int $count = 1)
 * @method static MapStyle whereDoesntHave(string $relation, Closure $callback = null)
 * @method static MapStyle whereRaw(string $sql, array $bindings = [])
 * @method static MapStyle groupBy(string $groupBy)
 * @method static MapStyle orderBy(string $column, string $direction = 'asc')
 * @method static MapStyle orderByDesc(string $column)
 * @method static MapStyle orderByRaw(string $sql, array $bindings = [])
 * @method static MapStyle limit(int $value)
 * @method static int count(array $columns = ['*'])
 * @method static mixed sum(string $column)
 * @method static bool exists()
 *
 * @property string $created_at
 * @property string $map_style_url
 * @property string $map_style_enum
 * @property string $map_style_name
 * @property string $external_api_id
 *
 * @property BearExternalApi $externalApi
 *
 * AUTO GENERATED FILE DO NOT MODIFY
 */
final class MapStyle extends Model {
    use BearLogDatabaseChanges;

    protected $connection = 'pgsql';
    protected $table = 'map_style';
    protected $primaryKey = 'map_style_enum';
    protected $keyType = 'string';
    protected $dateFormat = 'Y-m-d\TH:i:sP';
    public $timestamps = false;

    public function externalApi(): BelongsTo {
        return $this->belongsTo(related: BearExternalApi::class, foreignKey: 'external_api_id', ownerKey: 'id');
    }

    protected $guarded = ['map_style_enum', 'updated_at', 'created_at', 'deleted_at'];
}
