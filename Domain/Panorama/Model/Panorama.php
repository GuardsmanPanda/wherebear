<?php declare(strict_types=1);

namespace Domain\Panorama\Model;

use Carbon\CarbonInterface;
use Closure;
use GuardsmanPanda\Larabear\Infrastructure\Auth\Model\BearUser;
use GuardsmanPanda\Larabear\Infrastructure\Database\Traits\BearDatabaseChangeTrait;
use GuardsmanPanda\Larabear\Infrastructure\Locale\Model\BearCountry;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\ArrayObject;
use Illuminate\Database\Eloquent\Casts\AsArrayObject;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * AUTO GENERATED FILE DO NOT MODIFY
 *
 * @method static Panorama|null find(string $id, array $columns = ['*'])
 * @method static Panorama findOrFail(string $id, array $columns = ['*'])
 * @method static Panorama sole(array $columns = ['*'])
 * @method static Panorama|null first(array $columns = ['*'])
 * @method static Panorama firstOrFail(array $columns = ['*'])
 * @method static Panorama firstOrCreate(array $filter, array $values)
 * @method static Panorama firstOrNew(array $filter, array $values)
 * @method static Panorama|null firstWhere(string $column, string $operator, string|float|int|bool $value)
 * @method static Collection<int, Panorama> all(array $columns = ['*'])
 * @method static Collection<int, Panorama> get(array $columns = ['*'])
 * @method static Collection<int|string, Panorama> pluck(string $column, string $key = null)
 * @method static Collection<int, Panorama> fromQuery(string $query, array $bindings = [])
 * @method static Builder<Panorama> lockForUpdate()
 * @method static Builder<Panorama> select(array $columns = ['*'])
 * @method static Builder<Panorama> selectRaw(string $expression, array $bindings = [])
 * @method static Builder<Panorama> with(array $relations)
 * @method static Builder<Panorama> leftJoin(string $table, string $first, string $operator = null, string $second = null)
 * @method static Builder<Panorama> where(string $column, string $operator = null, string|float|int|bool $value = null)
 * @method static Builder<Panorama> whereIn(string $column, array $values)
 * @method static Builder<Panorama> whereNull(string|array $columns)
 * @method static Builder<Panorama> whereNotNull(string|array $columns)
 * @method static Builder<Panorama> whereYear(string $column, string $operator, CarbonInterface|string|int $value)
 * @method static Builder<Panorama> whereMonth(string $column, string $operator, CarbonInterface|string|int $value)
 * @method static Builder<Panorama> whereDate(string $column, string $operator, CarbonInterface|string $value)
 * @method static Builder<Panorama> whereExists(Closure $callback)
 * @method static Builder<Panorama> whereNotExists(Closure $callback)
 * @method static Builder<Panorama> whereHas(string $relation, Closure $callback = null, string $operator = '>=', int $count = 1)
 * @method static Builder<Panorama> withWhereHas(string $relation, Closure $callback = null, string $operator = '>=', int $count = 1)
 * @method static Builder<Panorama> whereDoesntHave(string $relation, Closure $callback = null)
 * @method static Builder<Panorama> whereRaw(string $sql, array $bindings = [])
 * @method static Builder<Panorama> groupBy(string $groupBy)
 * @method static Builder<Panorama> orderBy(string $column, string $direction = 'asc')
 * @method static Builder<Panorama> orderByDesc(string $column)
 * @method static Builder<Panorama> orderByRaw(string $sql, array $bindings = [])
 * @method static Builder<Panorama> limit(int $value)
 * @method static int count(array $columns = ['*'])
 * @method static bool exists()
 *
 * @property int|null $location_box_hash
 * @property string $id
 * @property string $created_at
 * @property string $updated_at
 * @property string|null $jpg_path
 * @property string|null $location
 * @property string|null $avif_path
 * @property string|null $city_name
 * @property string|null $state_name
 * @property string|null $country_cca2
 * @property string|null $retired_reason
 * @property string|null $added_by_user_id
 * @property ArrayObject|null $nominatim_json
 * @property CarbonInterface $captured_date
 * @property CarbonInterface|null $retired_at
 *
 * @property BearCountry|null $countryCca2
 * @property BearUser|null $addedByUser
 *
 * AUTO GENERATED FILE DO NOT MODIFY
 */
final class Panorama extends Model {
    use BearDatabaseChangeTrait;

    protected $connection = 'pgsql';
    protected $table = 'panorama';
    protected $keyType = 'string';
    protected $dateFormat = 'Y-m-d\TH:i:sP';

    /** @var array<string, string> $casts */
    protected $casts = [
        'captured_date' => 'immutable_date',
        'nominatim_json' => AsArrayObject::class,
        'retired_at' => 'immutable_datetime',
    ];

    /** @return BelongsTo<BearCountry, self>|null */
    public function countryCca2(): BelongsTo|null {
        return $this->belongsTo(related: BearCountry::class, foreignKey: 'country_cca2', ownerKey: 'cca2');
    }

    /** @return BelongsTo<BearUser, self>|null */
    public function addedByUser(): BelongsTo|null {
        return $this->belongsTo(related: BearUser::class, foreignKey: 'added_by_user_id', ownerKey: 'id');
    }

    protected $guarded = ['id', 'updated_at', 'created_at', 'deleted_at'];
}
