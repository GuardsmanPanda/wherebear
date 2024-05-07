<?php declare(strict_types=1);

namespace Domain\User\Model;

use Carbon\CarbonInterface;
use Closure;
use Domain\Map\Model\MapMarker;
use Domain\Map\Model\MapStyle;
use GuardsmanPanda\Larabear\Infrastructure\Database\Traits\BearLogDatabaseChanges;
use GuardsmanPanda\Larabear\Infrastructure\Locale\Model\BearCountry;
use GuardsmanPanda\Larabear\Infrastructure\Locale\Model\BearLanguage;
use Illuminate\Database\Eloquent\Casts\ArrayObject;
use Illuminate\Database\Eloquent\Casts\AsArrayObject;
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
 * @method static Collection<int|string, WhereBearUser> pluck(string $column, string $key = null)
 * @method static Collection<int, WhereBearUser> fromQuery(string $query, array $bindings = [])
 * @method static WhereBearUser lockForUpdate()
 * @method static WhereBearUser select(array $columns = ['*'])
 * @method static WhereBearUser selectRaw(string $expression, array $bindings = [])
 * @method static WhereBearUser with(array $relations)
 * @method static WhereBearUser leftJoin(string $table, string $first, string $operator = null, string $second = null)
 * @method static WhereBearUser where(string $column, string $operator = null, string|float|int|bool $value = null)
 * @method static WhereBearUser whereIn(string $column, array $values)
 * @method static WhereBearUser whereNull(string|array $columns)
 * @method static WhereBearUser whereNotNull(string|array $columns)
 * @method static WhereBearUser whereYear(string $column, string $operator, CarbonInterface|string|int $value)
 * @method static WhereBearUser whereMonth(string $column, string $operator, CarbonInterface|string|int $value)
 * @method static WhereBearUser whereDate(string $column, string $operator, CarbonInterface|string $value)
 * @method static WhereBearUser whereExists(Closure $callback)
 * @method static WhereBearUser whereNotExists(Closure $callback)
 * @method static WhereBearUser whereHas(string $relation, Closure $callback = null, string $operator = '>=', int $count = 1)
 * @method static WhereBearUser withWhereHas(string $relation, Closure $callback = null, string $operator = '>=', int $count = 1)
 * @method static WhereBearUser whereDoesntHave(string $relation, Closure $callback = null)
 * @method static WhereBearUser whereRaw(string $sql, array $bindings = [])
 * @method static WhereBearUser groupBy(string $groupBy)
 * @method static WhereBearUser orderBy(string $column, string $direction = 'asc')
 * @method static WhereBearUser orderByDesc(string $column)
 * @method static WhereBearUser orderByRaw(string $sql, array $bindings = [])
 * @method static WhereBearUser limit(int $value)
 * @method static int count(array $columns = ['*'])
 * @method static mixed sum(string $column)
 * @method static bool exists()
 *
 * @property bool $is_user_activated
 * @property string $id
 * @property string $created_at
 * @property string $updated_at
 * @property string $user_display_name
 * @property string $map_marker_file_name
 * @property string|null $password
 * @property string|null $user_city
 * @property string|null $user_email
 * @property string|null $map_style_enum
 * @property string|null $remember_token
 * @property string|null $user_last_name
 * @property string|null $user_first_name
 * @property string|null $user_profile_image
 * @property string|null $password_reset_token
 * @property string|null $user_country_iso2_code
 * @property string|null $user_language_iso2_code
 * @property ArrayObject $user_data_json
 * @property CarbonInterface|null $last_login_at
 * @property CarbonInterface|null $email_verified_at
 * @property CarbonInterface|null $password_reset_expires_at
 *
 * @property BearLanguage|null $userLanguageIso2Code
 * @property BearCountry|null $userCountryIso2Code
 * @property MapMarker $mapMarkerFileName
 * @property MapStyle|null $mapStyleEnum
 *
 * AUTO GENERATED FILE DO NOT MODIFY
 */
final class WhereBearUser extends Model {
    use BearLogDatabaseChanges;

    protected $connection = 'pgsql';
    protected $table = 'bear_user';
    protected $keyType = 'string';
    protected $dateFormat = 'Y-m-d\TH:i:sP';

    /** @var array<string, string> $casts */
    protected $casts = [
        'email_verified_at' => 'immutable_datetime',
        'last_login_at' => 'immutable_datetime',
        'password_reset_expires_at' => 'immutable_datetime',
        'user_data_json' => AsArrayObject::class,
    ];

    public function userLanguageIso2Code(): BelongsTo|null {
        return $this->belongsTo(related: BearLanguage::class, foreignKey: 'user_language_iso2_code', ownerKey: 'language_iso2_code');
    }

    public function userCountryIso2Code(): BelongsTo|null {
        return $this->belongsTo(related: BearCountry::class, foreignKey: 'user_country_iso2_code', ownerKey: 'country_iso2_code');
    }

    public function mapMarkerFileName(): BelongsTo {
        return $this->belongsTo(related: MapMarker::class, foreignKey: 'map_marker_file_name', ownerKey: 'file_name');
    }

    public function mapStyleEnum(): BelongsTo|null {
        return $this->belongsTo(related: MapStyle::class, foreignKey: 'map_style_enum', ownerKey: 'map_style_enum');
    }

    protected $guarded = ['id', 'updated_at', 'created_at', 'deleted_at'];
}
