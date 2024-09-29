<?php declare(strict_types=1);

namespace Domain\Achievement\Model;

use Carbon\CarbonInterface;
use Closure;
use GuardsmanPanda\Larabear\Infrastructure\Auth\Model\BearUser;
use GuardsmanPanda\Larabear\Infrastructure\Database\Traits\BearDatabaseChangeTrait;
use GuardsmanPanda\Larabear\Infrastructure\Locale\Enum\BearCountryEnum;
use GuardsmanPanda\Larabear\Infrastructure\Locale\Model\BearCountry;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use RuntimeException;

/**
 * AUTO GENERATED FILE DO NOT MODIFY
 *
 * @method static AchievementCountryGuess sole(array $columns = ['*'])
 * @method static AchievementCountryGuess|null first(array $columns = ['*'])
 * @method static AchievementCountryGuess firstOrFail(array $columns = ['*'])
 * @method static AchievementCountryGuess firstOrCreate(array $filter, array $values)
 * @method static AchievementCountryGuess firstOrNew(array $filter, array $values)
 * @method static AchievementCountryGuess|null firstWhere(string $column, string $operator, string|float|int|bool $value)
 * @method static Collection<int, AchievementCountryGuess> all(array $columns = ['*'])
 * @method static Collection<int, AchievementCountryGuess> get(array $columns = ['*'])
 * @method static Collection<int, AchievementCountryGuess> fromQuery(string $query, array $bindings = [])
 * @method static Builder<AchievementCountryGuess> lockForUpdate()
 * @method static Builder<AchievementCountryGuess> select(array $columns = ['*'])
 * @method static Builder<AchievementCountryGuess> selectRaw(string $expression, array $bindings = [])
 * @method static Builder<AchievementCountryGuess> with(array $relations)
 * @method static Builder<AchievementCountryGuess> leftJoin(string $table, string $first, string $operator = null, string $second = null)
 * @method static Builder<AchievementCountryGuess> where(string $column, string $operator = null, string|float|int|bool $value = null)
 * @method static Builder<AchievementCountryGuess> whereIn(string $column, array $values)
 * @method static Builder<AchievementCountryGuess> whereNull(string|array $columns)
 * @method static Builder<AchievementCountryGuess> whereNotNull(string|array $columns)
 * @method static Builder<AchievementCountryGuess> whereYear(string $column, string $operator, CarbonInterface|string|int $value)
 * @method static Builder<AchievementCountryGuess> whereMonth(string $column, string $operator, CarbonInterface|string|int $value)
 * @method static Builder<AchievementCountryGuess> whereDate(string $column, string $operator, CarbonInterface|string $value)
 * @method static Builder<AchievementCountryGuess> whereExists(Closure $callback)
 * @method static Builder<AchievementCountryGuess> whereNotExists(Closure $callback)
 * @method static Builder<AchievementCountryGuess> whereHas(string $relation, Closure $callback = null, string $operator = '>=', int $count = 1)
 * @method static Builder<AchievementCountryGuess> withWhereHas(string $relation, Closure $callback = null, string $operator = '>=', int $count = 1)
 * @method static Builder<AchievementCountryGuess> whereDoesntHave(string $relation, Closure $callback = null)
 * @method static Builder<AchievementCountryGuess> whereRaw(string $sql, array $bindings = [])
 * @method static Builder<AchievementCountryGuess> groupBy(string $groupBy)
 * @method static Builder<AchievementCountryGuess> orderBy(string $column, string $direction = 'asc')
 * @method static Builder<AchievementCountryGuess> orderByDesc(string $column)
 * @method static Builder<AchievementCountryGuess> orderByRaw(string $sql, array $bindings = [])
 * @method static Builder<AchievementCountryGuess> limit(int $value)
 * @method static int count(array $columns = ['*'])
 * @method static bool exists()
 *
 * @property int $count
 * @property string $user_id
 * @property string $created_at
 * @property string $updated_at
 * @property BearCountryEnum $country_cca2
 *
 * @property BearCountry $countryCca2
 * @property BearUser $user
 *
 * AUTO GENERATED FILE DO NOT MODIFY
 */
final class AchievementCountryGuess extends Model {
    use BearDatabaseChangeTrait;

    protected $connection = 'pgsql';
    protected $table = 'achievement_country_guess';
    /** @var array<string> primaryKeyArray */
    private array $primaryKeyArray = ['user_id', 'country_cca2'];
    protected $keyType = 'array';
    public $incrementing = false;
    protected $dateFormat = 'Y-m-d\TH:i:sP';

    /** @var array<string, string> $casts */
    protected $casts = [
        'country_cca2' => BearCountryEnum::class,
    ];

    /** @return BelongsTo<BearCountry, self> */
    public function countryCca2(): BelongsTo {
        return $this->belongsTo(related: BearCountry::class, foreignKey: 'country_cca2', ownerKey: 'cca2');
    }

    /** @return BelongsTo<BearUser, self> */
    public function user(): BelongsTo {
        return $this->belongsTo(related: BearUser::class, foreignKey: 'user_id', ownerKey: 'id');
    }

    protected $guarded = ['user_id', 'country_cca2', 'updated_at', 'created_at', 'deleted_at'];


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
     * @return AchievementCountryGuess|null
     */
    public static function find(array $ids, array $columns = ['*']): AchievementCountryGuess|null {
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
     * @return AchievementCountryGuess
     */
    public static function findOrFail(array $ids, array $columns = ['*']): AchievementCountryGuess {
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
