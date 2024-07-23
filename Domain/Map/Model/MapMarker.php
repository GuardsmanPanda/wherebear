<?php declare(strict_types=1);

namespace Domain\Map\Model;

use Carbon\CarbonInterface;
use Closure;
use Domain\User\Enum\UserLevelEnum;
use Domain\User\Model\UserLevel;
use GuardsmanPanda\Larabear\Infrastructure\Auth\Enum\LarabearPermissionEnum;
use GuardsmanPanda\Larabear\Infrastructure\Auth\Model\BearPermission;
use GuardsmanPanda\Larabear\Infrastructure\Database\Traits\BearDatabaseChangeTrait;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * AUTO GENERATED FILE DO NOT MODIFY
 *
 * @method static MapMarker|null find(string $id, array $columns = ['*'])
 * @method static MapMarker findOrFail(string $id, array $columns = ['*'])
 * @method static MapMarker sole(array $columns = ['*'])
 * @method static MapMarker|null first(array $columns = ['*'])
 * @method static MapMarker firstOrFail(array $columns = ['*'])
 * @method static MapMarker firstOrCreate(array $filter, array $values)
 * @method static MapMarker firstOrNew(array $filter, array $values)
 * @method static MapMarker|null firstWhere(string $column, string $operator, string|float|int|bool $value)
 * @method static Collection<int, MapMarker> all(array $columns = ['*'])
 * @method static Collection<int, MapMarker> get(array $columns = ['*'])
 * @method static Collection<int|string, MapMarker> pluck(string $column, string $key = null)
 * @method static Collection<int, MapMarker> fromQuery(string $query, array $bindings = [])
 * @method static MapMarker lockForUpdate()
 * @method static MapMarker select(array $columns = ['*'])
 * @method static MapMarker selectRaw(string $expression, array $bindings = [])
 * @method static MapMarker with(array $relations)
 * @method static MapMarker leftJoin(string $table, string $first, string $operator = null, string $second = null)
 * @method static MapMarker where(string $column, string $operator = null, string|float|int|bool $value = null)
 * @method static MapMarker whereIn(string $column, array $values)
 * @method static MapMarker whereNull(string|array $columns)
 * @method static MapMarker whereNotNull(string|array $columns)
 * @method static MapMarker whereYear(string $column, string $operator, CarbonInterface|string|int $value)
 * @method static MapMarker whereMonth(string $column, string $operator, CarbonInterface|string|int $value)
 * @method static MapMarker whereDate(string $column, string $operator, CarbonInterface|string $value)
 * @method static MapMarker whereExists(Closure $callback)
 * @method static MapMarker whereNotExists(Closure $callback)
 * @method static MapMarker whereHas(string $relation, Closure $callback = null, string $operator = '>=', int $count = 1)
 * @method static MapMarker withWhereHas(string $relation, Closure $callback = null, string $operator = '>=', int $count = 1)
 * @method static MapMarker whereDoesntHave(string $relation, Closure $callback = null)
 * @method static MapMarker whereRaw(string $sql, array $bindings = [])
 * @method static MapMarker groupBy(string $groupBy)
 * @method static MapMarker orderBy(string $column, string $direction = 'asc')
 * @method static MapMarker orderByDesc(string $column)
 * @method static MapMarker orderByRaw(string $sql, array $bindings = [])
 * @method static MapMarker limit(int $value)
 * @method static int count(array $columns = ['*'])
 * @method static mixed sum(string $column)
 * @method static bool exists()
 *
 * @property int $width_rem
 * @property int $height_rem
 * @property string $enum
 * @property string $name
 * @property string $grouping
 * @property string $file_name
 * @property string $created_at
 * @property LarabearPermissionEnum|null $permission_enum
 * @property UserLevelEnum $user_level_enum
 *
 * @property UserLevel $userLevel
 * @property BearPermission|null $permission
 *
 * AUTO GENERATED FILE DO NOT MODIFY
 */
final class MapMarker extends Model {
    use BearDatabaseChangeTrait;

    protected $connection = 'pgsql';
    protected $table = 'map_marker';
    protected $primaryKey = 'enum';
    protected $keyType = 'string';
    protected $dateFormat = 'Y-m-d\TH:i:sP';
    public $timestamps = false;

    /** @var array<string, string> $casts */
    protected $casts = [
        'permission_enum' => LarabearPermissionEnum::class,
        'user_level_enum' => UserLevelEnum::class,
    ];

    /** @return BelongsTo<UserLevel, self> */
    public function userLevel(): BelongsTo {
        return $this->belongsTo(related: UserLevel::class, foreignKey: 'user_level_enum', ownerKey: 'enum');
    }

    /** @return BelongsTo<BearPermission, self>|null */
    public function permission(): BelongsTo|null {
        return $this->belongsTo(related: BearPermission::class, foreignKey: 'permission_enum', ownerKey: 'enum');
    }

    protected $guarded = ['enum', 'updated_at', 'created_at', 'deleted_at'];
}
