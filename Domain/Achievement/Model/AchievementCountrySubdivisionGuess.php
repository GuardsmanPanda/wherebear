<?php declare(strict_types=1);

namespace Domain\Achievement\Model;

use Carbon\CarbonInterface;
use Closure;
use GuardsmanPanda\Larabear\Infrastructure\Auth\Model\BearUser;
use GuardsmanPanda\Larabear\Infrastructure\Database\Traits\BearDatabaseChangeTrait;
use GuardsmanPanda\Larabear\Infrastructure\Locale\Enum\BearCountrySubdivisionEnum;
use GuardsmanPanda\Larabear\Infrastructure\Locale\Model\BearCountrySubdivision;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use RuntimeException;

/**
 * AUTO GENERATED FILE DO NOT MODIFY
 *
 * @method static AchievementCountrySubdivisionGuess sole(array $columns = ['*'])
 * @method static AchievementCountrySubdivisionGuess|null first(array $columns = ['*'])
 * @method static AchievementCountrySubdivisionGuess firstOrFail(array $columns = ['*'])
 * @method static AchievementCountrySubdivisionGuess firstOrCreate(array $filter, array $values)
 * @method static AchievementCountrySubdivisionGuess firstOrNew(array $filter, array $values)
 * @method static AchievementCountrySubdivisionGuess|null firstWhere(string $column, string $operator, string|float|int|bool $value)
 * @method static Collection<int, AchievementCountrySubdivisionGuess> all(array $columns = ['*'])
 * @method static Collection<int, AchievementCountrySubdivisionGuess> get(array $columns = ['*'])
 * @method static Collection<int, AchievementCountrySubdivisionGuess> fromQuery(string $query, array $bindings = [])
 * @method static Builder<AchievementCountrySubdivisionGuess> lockForUpdate()
 * @method static Builder<AchievementCountrySubdivisionGuess> select(array $columns = ['*'])
 * @method static Builder<AchievementCountrySubdivisionGuess> selectRaw(string $expression, array $bindings = [])
 * @method static Builder<AchievementCountrySubdivisionGuess> with(array $relations)
 * @method static Builder<AchievementCountrySubdivisionGuess> leftJoin(string $table, string $first, string $operator = null, string $second = null)
 * @method static Builder<AchievementCountrySubdivisionGuess> where(string $column, string $operator = null, string|float|int|bool $value = null)
 * @method static Builder<AchievementCountrySubdivisionGuess> whereIn(string $column, array $values)
 * @method static Builder<AchievementCountrySubdivisionGuess> whereNull(string|array $columns)
 * @method static Builder<AchievementCountrySubdivisionGuess> whereNotNull(string|array $columns)
 * @method static Builder<AchievementCountrySubdivisionGuess> whereYear(string $column, string $operator, CarbonInterface|string|int $value)
 * @method static Builder<AchievementCountrySubdivisionGuess> whereMonth(string $column, string $operator, CarbonInterface|string|int $value)
 * @method static Builder<AchievementCountrySubdivisionGuess> whereDate(string $column, string $operator, CarbonInterface|string $value)
 * @method static Builder<AchievementCountrySubdivisionGuess> whereExists(Closure $callback)
 * @method static Builder<AchievementCountrySubdivisionGuess> whereNotExists(Closure $callback)
 * @method static Builder<AchievementCountrySubdivisionGuess> whereHas(string $relation, Closure $callback = null, string $operator = '>=', int $count = 1)
 * @method static Builder<AchievementCountrySubdivisionGuess> withWhereHas(string $relation, Closure $callback = null, string $operator = '>=', int $count = 1)
 * @method static Builder<AchievementCountrySubdivisionGuess> whereDoesntHave(string $relation, Closure $callback = null)
 * @method static Builder<AchievementCountrySubdivisionGuess> whereRaw(string $sql, array $bindings = [])
 * @method static Builder<AchievementCountrySubdivisionGuess> groupBy(string $groupBy)
 * @method static Builder<AchievementCountrySubdivisionGuess> orderBy(string $column, string $direction = 'asc')
 * @method static Builder<AchievementCountrySubdivisionGuess> orderByDesc(string $column)
 * @method static Builder<AchievementCountrySubdivisionGuess> orderByRaw(string $sql, array $bindings = [])
 * @method static Builder<AchievementCountrySubdivisionGuess> limit(int $value)
 * @method static int count(array $columns = ['*'])
 * @method static bool exists()
 *
 * @property int $count
 * @property string $user_id
 * @property string $created_at
 * @property string $updated_at
 * @property BearCountrySubdivisionEnum $country_subdivision_iso_3166
 *
 * @property BearCountrySubdivision $countrySubdivisionIso3166
 * @property BearUser $user
 *
 * AUTO GENERATED FILE DO NOT MODIFY
 */
final class AchievementCountrySubdivisionGuess extends Model {
    use BearDatabaseChangeTrait;

    protected $connection = 'pgsql';
    protected $table = 'achievement_country_subdivision_guess';
    /** @var array<string> primaryKeyArray */
    private array $primaryKeyArray = ['user_id', 'country_subdivision_iso_3166'];
    protected $keyType = 'array';
    public $incrementing = false;
    protected $dateFormat = 'Y-m-d\TH:i:sP';

    /** @var array<string, string> $casts */
    protected $casts = [
        'country_subdivision_iso_3166' => BearCountrySubdivisionEnum::class,
    ];

    /** @return BelongsTo<BearCountrySubdivision, $this> */
    public function countrySubdivisionIso3166(): BelongsTo {
        return $this->belongsTo(related: BearCountrySubdivision::class, foreignKey: 'country_subdivision_iso_3166', ownerKey: 'iso_3166');
    }

    /** @return BelongsTo<BearUser, $this> */
    public function user(): BelongsTo {
        return $this->belongsTo(related: BearUser::class, foreignKey: 'user_id', ownerKey: 'id');
    }

    protected $guarded = ['user_id', 'country_subdivision_iso_3166', 'updated_at', 'created_at', 'deleted_at'];


    /** @return Mixed[] */
    public function getKey(): array {
        $attributes = [];
        foreach ($this->primaryKeyArray as $key) {
            $attributes[$key] = $this->getAttribute($key);
        }
        return $attributes;
    }

    /**
     * @param array<string, string|int> $ids # Ids in the form ['key1' => 'value1', 'key2' => 'value2']
     * @param array<string> $columns
     * @return AchievementCountrySubdivisionGuess|null
     */
    public static function find(array $ids, array $columns = ['*']): AchievementCountrySubdivisionGuess|null {
        $me = new self;
        $query = $me->newQuery();
        foreach ($me->primaryKeyArray as $key) {
            $query->where(column: $key, operator: '=', value: $ids[$key]);
        }
        $result = $query->first($columns);
        return $result instanceof self ? $result : null;
    }

    /**
     * @param array<string, string|int> $ids # Ids in the form ['key1' => 'value1', 'key2' => 'value2']
     * @param array<string> $columns
     * @return AchievementCountrySubdivisionGuess
     */
    public static function findOrFail(array $ids, array $columns = ['*']): AchievementCountrySubdivisionGuess {
        $result = self::find(ids: $ids, columns: $columns);
        return $result ?? throw new RuntimeException(message: "No result found for " . self::class . " with ids " . json_encode($ids, JSON_THROW_ON_ERROR));
    }

    protected function setKeysForSaveQuery($query): EloquentBuilder { 
        foreach ($this->primaryKeyArray as $key) {
            $query->where(column: $key, operator: "=", value: $this->$key ?? throw new RuntimeException(message: "Missing primary key value for $key"));
        }
        return $query;
    }
    protected function setKeysForSelectQuery($query): EloquentBuilder { 
        foreach ($this->primaryKeyArray as $key) {
            $query->where(column: $key, operator: "=", value: $this->$key ?? throw new RuntimeException(message: "Missing primary key value for $key"));
        }
        return $query;
    }
}
