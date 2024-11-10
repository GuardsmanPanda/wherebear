<?php declare(strict_types=1);

namespace Domain\User\Model;

use Carbon\CarbonInterface;
use Closure;
use Domain\Map\Enum\MapMarkerEnum;
use Domain\Map\Enum\MapStyleEnum;
use Domain\Map\Model\MapMarker;
use Domain\Map\Model\MapStyle;
use Domain\User\Enum\UserFlagEnum;
use Domain\User\Enum\UserLevelEnum;
use GuardsmanPanda\Larabear\Infrastructure\Database\Traits\BearDatabaseChangeTrait;
use GuardsmanPanda\Larabear\Infrastructure\Locale\Enum\BearCountryEnum;
use GuardsmanPanda\Larabear\Infrastructure\Locale\Model\BearCountry;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * AUTO GENERATED FILE DO NOT MODIFY
 *
 * @method static WhereBearUser|null find(string $id, array $columns = ['*'])
 * @method static WhereBearUser findOrFail(string $id, array $columns = ['*'])
 * @method static WhereBearUser sole(array $columns = ['*'])
 * @method static WhereBearUser|null first(array $columns = ['*'])
 * @method static WhereBearUser firstOrFail(array $columns = ['*'])
 * @method static WhereBearUser firstOrCreate(array $filter, array $values)
 * @method static WhereBearUser firstOrNew(array $filter, array $values)
 * @method static WhereBearUser|null firstWhere(string $column, string $operator, string|float|int|bool $value)
 * @method static Collection<int, WhereBearUser> all(array $columns = ['*'])
 * @method static Collection<int, WhereBearUser> get(array $columns = ['*'])
 * @method static Collection<int, WhereBearUser> fromQuery(string $query, array $bindings = [])
 * @method static Builder<WhereBearUser> lockForUpdate()
 * @method static Builder<WhereBearUser> select(array $columns = ['*'])
 * @method static Builder<WhereBearUser> selectRaw(string $expression, array $bindings = [])
 * @method static Builder<WhereBearUser> with(array $relations)
 * @method static Builder<WhereBearUser> leftJoin(string $table, string $first, string $operator = null, string $second = null)
 * @method static Builder<WhereBearUser> where(string $column, string $operator = null, string|float|int|bool $value = null)
 * @method static Builder<WhereBearUser> whereIn(string $column, array $values)
 * @method static Builder<WhereBearUser> whereNull(string|array $columns)
 * @method static Builder<WhereBearUser> whereNotNull(string|array $columns)
 * @method static Builder<WhereBearUser> whereYear(string $column, string $operator, CarbonInterface|string|int $value)
 * @method static Builder<WhereBearUser> whereMonth(string $column, string $operator, CarbonInterface|string|int $value)
 * @method static Builder<WhereBearUser> whereDate(string $column, string $operator, CarbonInterface|string $value)
 * @method static Builder<WhereBearUser> whereExists(Closure $callback)
 * @method static Builder<WhereBearUser> whereNotExists(Closure $callback)
 * @method static Builder<WhereBearUser> whereHas(string $relation, Closure $callback = null, string $operator = '>=', int $count = 1)
 * @method static Builder<WhereBearUser> withWhereHas(string $relation, Closure $callback = null, string $operator = '>=', int $count = 1)
 * @method static Builder<WhereBearUser> whereDoesntHave(string $relation, Closure $callback = null)
 * @method static Builder<WhereBearUser> whereRaw(string $sql, array $bindings = [])
 * @method static Builder<WhereBearUser> groupBy(string $groupBy)
 * @method static Builder<WhereBearUser> orderBy(string $column, string $direction = 'asc')
 * @method static Builder<WhereBearUser> orderByDesc(string $column)
 * @method static Builder<WhereBearUser> orderByRaw(string $sql, array $bindings = [])
 * @method static Builder<WhereBearUser> limit(int $value)
 * @method static int count(array $columns = ['*'])
 * @method static bool exists()
 *
 * @property int $experience
 * @property string $id
 * @property string $created_at
 * @property string $updated_at
 * @property string $display_name
 * @property string|null $email
 * @property string|null $password
 * @property string|null $remember_token
 * @property CarbonInterface|null $last_login_at
 * @property BearCountryEnum|null $country_cca2
 * @property MapStyleEnum $map_style_enum
 * @property UserFlagEnum|null $user_flag_enum
 * @property MapMarkerEnum $map_marker_enum
 * @property UserLevelEnum $user_level_enum
 *
 * @property BearCountry|null $countryCca2
 * @property MapStyle $mapStyle
 * @property UserFlag|null $userFlag
 * @property UserLevel $userLevel
 * @property MapMarker $mapMarker
 *
 * AUTO GENERATED FILE DO NOT MODIFY
 */
final class WhereBearUser extends Model {
    use BearDatabaseChangeTrait;

    protected $connection = 'pgsql';
    protected $table = 'bear_user';
    protected $keyType = 'string';
    protected $dateFormat = 'Y-m-d\TH:i:sP';

    /** @var array<string, string> $casts */
    protected $casts = [
        'country_cca2' => BearCountryEnum::class,
        'last_login_at' => 'immutable_datetime',
        'map_marker_enum' => MapMarkerEnum::class,
        'map_style_enum' => MapStyleEnum::class,
        'user_flag_enum' => UserFlagEnum::class,
        'user_level_enum' => UserLevelEnum::class,
    ];

    /** @return BelongsTo<BearCountry, $this>|null */
    public function countryCca2(): BelongsTo|null {
        return $this->belongsTo(related: BearCountry::class, foreignKey: 'country_cca2', ownerKey: 'cca2');
    }

    /** @return BelongsTo<MapStyle, $this> */
    public function mapStyle(): BelongsTo {
        return $this->belongsTo(related: MapStyle::class, foreignKey: 'map_style_enum', ownerKey: 'enum');
    }

    /** @return BelongsTo<UserFlag, $this>|null */
    public function userFlag(): BelongsTo|null {
        return $this->belongsTo(related: UserFlag::class, foreignKey: 'user_flag_enum', ownerKey: 'enum');
    }

    /** @return BelongsTo<UserLevel, $this> */
    public function userLevel(): BelongsTo {
        return $this->belongsTo(related: UserLevel::class, foreignKey: 'user_level_enum', ownerKey: 'enum');
    }

    /** @return BelongsTo<MapMarker, $this> */
    public function mapMarker(): BelongsTo {
        return $this->belongsTo(related: MapMarker::class, foreignKey: 'map_marker_enum', ownerKey: 'enum');
    }

    protected $guarded = ['id', 'updated_at', 'created_at', 'deleted_at'];
}
