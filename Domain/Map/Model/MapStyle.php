<?php declare(strict_types=1);

namespace Domain\Map\Model;

use Carbon\CarbonInterface;
use Closure;
use Domain\User\Enum\UserLevelEnum;
use Domain\User\Model\UserLevel;
use GuardsmanPanda\Larabear\Infrastructure\Database\Traits\BearDatabaseChangeTrait;
use GuardsmanPanda\Larabear\Integration\ExternalApi\Model\BearExternalApi;
use Illuminate\Database\Eloquent\Builder;
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
 * @method static Collection<array-key, MapStyle> pluck(string $column, string $key = null)
 * @method static Collection<int, MapStyle> fromQuery(string $query, array $bindings = [])
 * @method static Builder<MapStyle> lockForUpdate()
 * @method static Builder<MapStyle> select(array $columns = ['*'])
 * @method static Builder<MapStyle> selectRaw(string $expression, array $bindings = [])
 * @method static Builder<MapStyle> with(array $relations)
 * @method static Builder<MapStyle> leftJoin(string $table, string $first, string $operator = null, string $second = null)
 * @method static Builder<MapStyle> where(string $column, string $operator = null, string|float|int|bool $value = null)
 * @method static Builder<MapStyle> whereIn(string $column, array $values)
 * @method static Builder<MapStyle> whereNull(string|array $columns)
 * @method static Builder<MapStyle> whereNotNull(string|array $columns)
 * @method static Builder<MapStyle> whereYear(string $column, string $operator, CarbonInterface|string|int $value)
 * @method static Builder<MapStyle> whereMonth(string $column, string $operator, CarbonInterface|string|int $value)
 * @method static Builder<MapStyle> whereDate(string $column, string $operator, CarbonInterface|string $value)
 * @method static Builder<MapStyle> whereExists(Closure $callback)
 * @method static Builder<MapStyle> whereNotExists(Closure $callback)
 * @method static Builder<MapStyle> whereHas(string $relation, Closure $callback = null, string $operator = '>=', int $count = 1)
 * @method static Builder<MapStyle> withWhereHas(string $relation, Closure $callback = null, string $operator = '>=', int $count = 1)
 * @method static Builder<MapStyle> whereDoesntHave(string $relation, Closure $callback = null)
 * @method static Builder<MapStyle> whereRaw(string $sql, array $bindings = [])
 * @method static Builder<MapStyle> groupBy(string $groupBy)
 * @method static Builder<MapStyle> orderBy(string $column, string $direction = 'asc')
 * @method static Builder<MapStyle> orderByDesc(string $column)
 * @method static Builder<MapStyle> orderByRaw(string $sql, array $bindings = [])
 * @method static Builder<MapStyle> limit(int $value)
 * @method static int count(array $columns = ['*'])
 * @method static bool exists()
 *
 * @property int $tile_size
 * @property int $zoom_offset
 * @property string $enum
 * @property string $name
 * @property string $full_uri
 * @property string $http_path
 * @property string $icon_path
 * @property string $created_at
 * @property string $external_api_enum
 * @property UserLevelEnum $user_level_enum
 *
 * @property BearExternalApi $externalApi
 * @property UserLevel $userLevel
 *
 * AUTO GENERATED FILE DO NOT MODIFY
 */
final class MapStyle extends Model {
    use BearDatabaseChangeTrait;

    protected $connection = 'pgsql';
    protected $table = 'map_style';
    protected $primaryKey = 'enum';
    protected $keyType = 'string';
    protected $dateFormat = 'Y-m-d\TH:i:sP';
    public $timestamps = false;

    /** @var array<string, string> $casts */
    protected $casts = [
        'user_level_enum' => UserLevelEnum::class,
    ];

    /** @return BelongsTo<BearExternalApi, self> */
    public function externalApi(): BelongsTo {
        return $this->belongsTo(related: BearExternalApi::class, foreignKey: 'external_api_enum', ownerKey: 'enum');
    }

    /** @return BelongsTo<UserLevel, self> */
    public function userLevel(): BelongsTo {
        return $this->belongsTo(related: UserLevel::class, foreignKey: 'user_level_enum', ownerKey: 'enum');
    }

    protected $guarded = ['enum', 'updated_at', 'created_at', 'deleted_at'];
}
