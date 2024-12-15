<?php declare(strict_types=1);

namespace Domain\Import\Model;

use Carbon\CarbonInterface;
use Closure;
use Domain\Import\Enum\ImportStatusEnum;
use GuardsmanPanda\Larabear\Infrastructure\App\DataType\BearPoint;
use GuardsmanPanda\Larabear\Infrastructure\Database\Cast\BearDatabasePointCast;
use GuardsmanPanda\Larabear\Infrastructure\Database\Traits\BearDatabaseChangeTrait;
use GuardsmanPanda\Larabear\Infrastructure\Locale\Enum\BearCountryEnum;
use GuardsmanPanda\Larabear\Infrastructure\Locale\Enum\BearCountrySubdivisionEnum;
use GuardsmanPanda\Larabear\Infrastructure\Locale\Model\BearCountry;
use GuardsmanPanda\Larabear\Infrastructure\Locale\Model\BearCountrySubdivision;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * AUTO GENERATED FILE DO NOT MODIFY
 *
 * @method static ImportStreetViewUserPanorama|null find(string $id, array $columns = ['*'])
 * @method static ImportStreetViewUserPanorama findOrFail(string $id, array $columns = ['*'])
 * @method static ImportStreetViewUserPanorama sole(array $columns = ['*'])
 * @method static ImportStreetViewUserPanorama|null first(array $columns = ['*'])
 * @method static ImportStreetViewUserPanorama firstOrFail(array $columns = ['*'])
 * @method static ImportStreetViewUserPanorama firstOrCreate(array $filter, array $values)
 * @method static ImportStreetViewUserPanorama firstOrNew(array $filter, array $values)
 * @method static ImportStreetViewUserPanorama|null firstWhere(string $column, string $operator, string|float|int|bool $value)
 * @method static Collection<int, ImportStreetViewUserPanorama> all(array $columns = ['*'])
 * @method static Collection<int, ImportStreetViewUserPanorama> get(array $columns = ['*'])
 * @method static Collection<int, ImportStreetViewUserPanorama> fromQuery(string $query, array $bindings = [])
 * @method static Builder<ImportStreetViewUserPanorama> lockForUpdate()
 * @method static Builder<ImportStreetViewUserPanorama> select(array $columns = ['*'])
 * @method static Builder<ImportStreetViewUserPanorama> selectRaw(string $expression, array $bindings = [])
 * @method static Builder<ImportStreetViewUserPanorama> with(array $relations)
 * @method static Builder<ImportStreetViewUserPanorama> leftJoin(string $table, string $first, string $operator = null, string $second = null)
 * @method static Builder<ImportStreetViewUserPanorama> where(string $column, string $operator = null, string|float|int|bool $value = null)
 * @method static Builder<ImportStreetViewUserPanorama> whereIn(string $column, array $values)
 * @method static Builder<ImportStreetViewUserPanorama> whereNull(string|array $columns)
 * @method static Builder<ImportStreetViewUserPanorama> whereNotNull(string|array $columns)
 * @method static Builder<ImportStreetViewUserPanorama> whereYear(string $column, string $operator, CarbonInterface|string|int $value)
 * @method static Builder<ImportStreetViewUserPanorama> whereMonth(string $column, string $operator, CarbonInterface|string|int $value)
 * @method static Builder<ImportStreetViewUserPanorama> whereDate(string $column, string $operator, CarbonInterface|string $value)
 * @method static Builder<ImportStreetViewUserPanorama> whereExists(Closure $callback)
 * @method static Builder<ImportStreetViewUserPanorama> whereNotExists(Closure $callback)
 * @method static Builder<ImportStreetViewUserPanorama> whereHas(string $relation, Closure $callback = null, string $operator = '>=', int $count = 1)
 * @method static Builder<ImportStreetViewUserPanorama> withWhereHas(string $relation, Closure $callback = null, string $operator = '>=', int $count = 1)
 * @method static Builder<ImportStreetViewUserPanorama> whereDoesntHave(string $relation, Closure $callback = null)
 * @method static Builder<ImportStreetViewUserPanorama> whereRaw(string $sql, array $bindings = [])
 * @method static Builder<ImportStreetViewUserPanorama> groupBy(string $groupBy)
 * @method static Builder<ImportStreetViewUserPanorama> orderBy(string $column, string $direction = 'asc')
 * @method static Builder<ImportStreetViewUserPanorama> orderByDesc(string $column)
 * @method static Builder<ImportStreetViewUserPanorama> orderByRaw(string $sql, array $bindings = [])
 * @method static Builder<ImportStreetViewUserPanorama> limit(int $value)
 * @method static int count(array $columns = ['*'])
 * @method static bool exists()
 *
 * @property string $id
 * @property string $created_at
 * @property string $updated_at
 * @property string $panorama_id
 * @property string $import_street_view_user_id
 * @property BearPoint|null $location
 * @property CarbonInterface|null $captured_date
 * @property BearCountryEnum|null $country_cca2
 * @property ImportStatusEnum $import_status_enum
 * @property BearCountrySubdivisionEnum|null $country_subdivision_iso_3166
 *
 * @property BearCountry|null $countryCca2
 * @property BearCountrySubdivision|null $countrySubdivisionIso3166
 * @property ImportStreetViewUser $importStreetViewUser
 * @property ImportStatus $importStatus
 *
 * AUTO GENERATED FILE DO NOT MODIFY
 */
final class ImportStreetViewUserPanorama extends Model {
    use BearDatabaseChangeTrait;

    protected $connection = 'pgsql';
    protected $table = 'import_street_view_user_panorama';
    protected $keyType = 'string';
    protected $dateFormat = 'Y-m-d\TH:i:sP';

    /** @var array<string, string> $casts */
    protected $casts = [
        'captured_date' => 'immutable_date',
        'country_cca2' => BearCountryEnum::class,
        'country_subdivision_iso_3166' => BearCountrySubdivisionEnum::class,
        'import_status_enum' => ImportStatusEnum::class,
        'location' => BearDatabasePointCast::class,
    ];

    /** @return BelongsTo<BearCountry, $this> */
    public function countryCca2(): BelongsTo {
        return $this->belongsTo(related: BearCountry::class, foreignKey: 'country_cca2', ownerKey: 'cca2');
    }

    /** @return BelongsTo<BearCountrySubdivision, $this> */
    public function countrySubdivisionIso3166(): BelongsTo {
        return $this->belongsTo(related: BearCountrySubdivision::class, foreignKey: 'country_subdivision_iso_3166', ownerKey: 'iso_3166');
    }

    /** @return BelongsTo<ImportStreetViewUser, $this> */
    public function importStreetViewUser(): BelongsTo {
        return $this->belongsTo(related: ImportStreetViewUser::class, foreignKey: 'import_street_view_user_id', ownerKey: 'id');
    }

    /** @return BelongsTo<ImportStatus, $this> */
    public function importStatus(): BelongsTo {
        return $this->belongsTo(related: ImportStatus::class, foreignKey: 'import_status_enum', ownerKey: 'enum');
    }

    protected $guarded = ['id', 'updated_at', 'created_at', 'deleted_at'];
}
