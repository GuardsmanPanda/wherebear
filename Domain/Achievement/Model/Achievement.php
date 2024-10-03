<?php declare(strict_types=1);

namespace Domain\Achievement\Model;

use Carbon\CarbonInterface;
use Closure;
use Domain\Achievement\Enum\AchievementTypeEnum;
use GuardsmanPanda\Larabear\Infrastructure\Database\Cast\BearDatabaseArrayCast;
use GuardsmanPanda\Larabear\Infrastructure\Database\Traits\BearDatabaseChangeTrait;
use GuardsmanPanda\Larabear\Infrastructure\Locale\Enum\BearCountryEnum;
use GuardsmanPanda\Larabear\Infrastructure\Locale\Enum\BearCountrySubdivisionEnum;
use GuardsmanPanda\Larabear\Infrastructure\Locale\Model\BearCountry;
use GuardsmanPanda\Larabear\Infrastructure\Locale\Model\BearCountrySubdivision;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\ArrayObject;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * AUTO GENERATED FILE DO NOT MODIFY
 *
 * @method static Achievement|null find(string $id, array $columns = ['*'])
 * @method static Achievement findOrFail(string $id, array $columns = ['*'])
 * @method static Achievement sole(array $columns = ['*'])
 * @method static Achievement|null first(array $columns = ['*'])
 * @method static Achievement firstOrFail(array $columns = ['*'])
 * @method static Achievement firstOrCreate(array $filter, array $values)
 * @method static Achievement firstOrNew(array $filter, array $values)
 * @method static Achievement|null firstWhere(string $column, string $operator, string|float|int|bool $value)
 * @method static Collection<int, Achievement> all(array $columns = ['*'])
 * @method static Collection<int, Achievement> get(array $columns = ['*'])
 * @method static Collection<int, Achievement> fromQuery(string $query, array $bindings = [])
 * @method static Builder<Achievement> lockForUpdate()
 * @method static Builder<Achievement> select(array $columns = ['*'])
 * @method static Builder<Achievement> selectRaw(string $expression, array $bindings = [])
 * @method static Builder<Achievement> with(array $relations)
 * @method static Builder<Achievement> leftJoin(string $table, string $first, string $operator = null, string $second = null)
 * @method static Builder<Achievement> where(string $column, string $operator = null, string|float|int|bool $value = null)
 * @method static Builder<Achievement> whereIn(string $column, array $values)
 * @method static Builder<Achievement> whereNull(string|array $columns)
 * @method static Builder<Achievement> whereNotNull(string|array $columns)
 * @method static Builder<Achievement> whereYear(string $column, string $operator, CarbonInterface|string|int $value)
 * @method static Builder<Achievement> whereMonth(string $column, string $operator, CarbonInterface|string|int $value)
 * @method static Builder<Achievement> whereDate(string $column, string $operator, CarbonInterface|string $value)
 * @method static Builder<Achievement> whereExists(Closure $callback)
 * @method static Builder<Achievement> whereNotExists(Closure $callback)
 * @method static Builder<Achievement> whereHas(string $relation, Closure $callback = null, string $operator = '>=', int $count = 1)
 * @method static Builder<Achievement> withWhereHas(string $relation, Closure $callback = null, string $operator = '>=', int $count = 1)
 * @method static Builder<Achievement> whereDoesntHave(string $relation, Closure $callback = null)
 * @method static Builder<Achievement> whereRaw(string $sql, array $bindings = [])
 * @method static Builder<Achievement> groupBy(string $groupBy)
 * @method static Builder<Achievement> orderBy(string $column, string $direction = 'asc')
 * @method static Builder<Achievement> orderByDesc(string $column)
 * @method static Builder<Achievement> orderByRaw(string $sql, array $bindings = [])
 * @method static Builder<Achievement> limit(int $value)
 * @method static int count(array $columns = ['*'])
 * @method static bool exists()
 *
 * @property int $required_points
 * @property int|null $location_radius_meters
 * @property string $enum
 * @property string $name
 * @property string $title
 * @property string $created_at
 * @property string $updated_at
 * @property string|null $location
 * @property string|null $geographic_area
 * @property ArrayObject<int,string> $country_cca2_array
 * @property ArrayObject<int,string> $country_subdivision_iso_3166_array
 * @property BearCountryEnum|null $country_cca2
 * @property AchievementTypeEnum $achievement_type_enum
 * @property BearCountrySubdivisionEnum|null $country_subdivision_iso_3166
 *
 * @property BearCountry|null $countryCca2
 * @property BearCountrySubdivision|null $countrySubdivisionIso3166
 * @property AchievementType $achievementType
 *
 * AUTO GENERATED FILE DO NOT MODIFY
 */
final class Achievement extends Model {
    use BearDatabaseChangeTrait;

    protected $connection = 'pgsql';
    protected $table = 'achievement';
    protected $primaryKey = 'enum';
    protected $keyType = 'string';
    protected $dateFormat = 'Y-m-d\TH:i:sP';

    /** @var array<string, string> $casts */
    protected $casts = [
        'achievement_type_enum' => AchievementTypeEnum::class,
        'country_cca2' => BearCountryEnum::class,
        'country_cca2_array' => BearDatabaseArrayCast::class,
        'country_subdivision_iso_3166' => BearCountrySubdivisionEnum::class,
        'country_subdivision_iso_3166_array' => BearDatabaseArrayCast::class,
    ];

    /** @return BelongsTo<BearCountry, self>|null */
    public function countryCca2(): BelongsTo|null {
        return $this->belongsTo(related: BearCountry::class, foreignKey: 'country_cca2', ownerKey: 'cca2');
    }

    /** @return BelongsTo<BearCountrySubdivision, self>|null */
    public function countrySubdivisionIso3166(): BelongsTo|null {
        return $this->belongsTo(related: BearCountrySubdivision::class, foreignKey: 'country_subdivision_iso_3166', ownerKey: 'iso_3166');
    }

    /** @return BelongsTo<AchievementType, self> */
    public function achievementType(): BelongsTo {
        return $this->belongsTo(related: AchievementType::class, foreignKey: 'achievement_type_enum', ownerKey: 'enum');
    }

    protected $guarded = ['enum', 'updated_at', 'created_at', 'deleted_at'];
}
