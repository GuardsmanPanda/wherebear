<?php declare(strict_types=1);

namespace Domain\Achievement\Model;

use Carbon\CarbonInterface;
use Closure;
use GuardsmanPanda\Larabear\Infrastructure\Auth\Model\BearUser;
use GuardsmanPanda\Larabear\Infrastructure\Database\Traits\BearDatabaseChangeTrait;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use RuntimeException;

/**
 * AUTO GENERATED FILE DO NOT MODIFY
 *
 * @method static AchievementUser sole(array $columns = ['*'])
 * @method static AchievementUser|null first(array $columns = ['*'])
 * @method static AchievementUser firstOrFail(array $columns = ['*'])
 * @method static AchievementUser firstOrCreate(array $filter, array $values)
 * @method static AchievementUser firstOrNew(array $filter, array $values)
 * @method static AchievementUser|null firstWhere(string $column, string $operator, string|float|int|bool $value)
 * @method static Collection<int, AchievementUser> all(array $columns = ['*'])
 * @method static Collection<int, AchievementUser> get(array $columns = ['*'])
 * @method static Collection<int, AchievementUser> fromQuery(string $query, array $bindings = [])
 * @method static Builder<AchievementUser> lockForUpdate()
 * @method static Builder<AchievementUser> select(array $columns = ['*'])
 * @method static Builder<AchievementUser> selectRaw(string $expression, array $bindings = [])
 * @method static Builder<AchievementUser> with(array $relations)
 * @method static Builder<AchievementUser> leftJoin(string $table, string $first, string $operator = null, string $second = null)
 * @method static Builder<AchievementUser> where(string $column, string $operator = null, string|float|int|bool $value = null)
 * @method static Builder<AchievementUser> whereIn(string $column, array $values)
 * @method static Builder<AchievementUser> whereNull(string|array $columns)
 * @method static Builder<AchievementUser> whereNotNull(string|array $columns)
 * @method static Builder<AchievementUser> whereYear(string $column, string $operator, CarbonInterface|string|int $value)
 * @method static Builder<AchievementUser> whereMonth(string $column, string $operator, CarbonInterface|string|int $value)
 * @method static Builder<AchievementUser> whereDate(string $column, string $operator, CarbonInterface|string $value)
 * @method static Builder<AchievementUser> whereExists(Closure $callback)
 * @method static Builder<AchievementUser> whereNotExists(Closure $callback)
 * @method static Builder<AchievementUser> whereHas(string $relation, Closure $callback = null, string $operator = '>=', int $count = 1)
 * @method static Builder<AchievementUser> withWhereHas(string $relation, Closure $callback = null, string $operator = '>=', int $count = 1)
 * @method static Builder<AchievementUser> whereDoesntHave(string $relation, Closure $callback = null)
 * @method static Builder<AchievementUser> whereRaw(string $sql, array $bindings = [])
 * @method static Builder<AchievementUser> groupBy(string $groupBy)
 * @method static Builder<AchievementUser> orderBy(string $column, string $direction = 'asc')
 * @method static Builder<AchievementUser> orderByDesc(string $column)
 * @method static Builder<AchievementUser> orderByRaw(string $sql, array $bindings = [])
 * @method static Builder<AchievementUser> limit(int $value)
 * @method static int count(array $columns = ['*'])
 * @method static bool exists()
 *
 * @property string $user_id
 * @property string $created_at
 * @property string $updated_at
 * @property string $achievement_enum
 *
 * @property BearUser $user
 * @property Achievement $achievement
 *
 * AUTO GENERATED FILE DO NOT MODIFY
 */
final class AchievementUser extends Model {
    use BearDatabaseChangeTrait;

    protected $connection = 'pgsql';
    protected $table = 'achievement_user';
    /** @var array<string> primaryKeyArray */
    private array $primaryKeyArray = ['achievement_enum', 'user_id'];
    protected $keyType = 'array';
    public $incrementing = false;
    protected $dateFormat = 'Y-m-d\TH:i:sP';

    /** @return BelongsTo<BearUser, self> */
    public function user(): BelongsTo {
        return $this->belongsTo(related: BearUser::class, foreignKey: 'user_id', ownerKey: 'id');
    }

    /** @return BelongsTo<Achievement, self> */
    public function achievement(): BelongsTo {
        return $this->belongsTo(related: Achievement::class, foreignKey: 'achievement_enum', ownerKey: 'enum');
    }

    protected $guarded = ['achievement_enum', 'user_id', 'updated_at', 'created_at', 'deleted_at'];


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
     * @return AchievementUser|null
     */
    public static function find(array $ids, array $columns = ['*']): AchievementUser|null {
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
     * @return AchievementUser
     */
    public static function findOrFail(array $ids, array $columns = ['*']): AchievementUser {
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
