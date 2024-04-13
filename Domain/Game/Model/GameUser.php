<?php declare(strict_types=1);

namespace Domain\Game\Model;

use Carbon\CarbonInterface;
use Closure;
use GuardsmanPanda\Larabear\Infrastructure\Auth\Model\BearUser;
use GuardsmanPanda\Larabear\Infrastructure\Database\Traits\BearLogDatabaseChanges;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use RuntimeException;

/**
 * AUTO GENERATED FILE DO NOT MODIFY
 *
 * @method static GameUser sole(array $columns = ['*'])
 * @method static GameUser|null first(array $columns = ['*'])
 * @method static GameUser firstOrFail(array $columns = ['*'])
 * @method static GameUser firstOrCreate(array $filter, array $values)
 * @method static GameUser firstOrNew(array $filter, array $values)
 * @method static GameUser|null firstWhere(string $column, string $operator, string|float|int|bool $value)
 * @method static Collection<int, GameUser> all(array $columns = ['*'])
 * @method static Collection<int, GameUser> get(array $columns = ['*'])
 * @method static Collection<int|string, GameUser> pluck(string $column, string $key = null)
 * @method static Collection<int, GameUser> fromQuery(string $query, array $bindings = [])
 * @method static GameUser lockForUpdate()
 * @method static GameUser select(array $columns = ['*'])
 * @method static GameUser selectRaw(string $expression, array $bindings = [])
 * @method static GameUser with(array $relations)
 * @method static GameUser leftJoin(string $table, string $first, string $operator = null, string $second = null)
 * @method static GameUser where(string $column, string $operator = null, string|float|int|bool $value = null)
 * @method static GameUser whereIn(string $column, array $values)
 * @method static GameUser whereNull(string|array $columns)
 * @method static GameUser whereNotNull(string|array $columns)
 * @method static GameUser whereYear(string $column, string $operator, CarbonInterface|string|int $value)
 * @method static GameUser whereMonth(string $column, string $operator, CarbonInterface|string|int $value)
 * @method static GameUser whereDate(string $column, string $operator, CarbonInterface|string $value)
 * @method static GameUser whereExists(Closure $callback)
 * @method static GameUser whereNotExists(Closure $callback)
 * @method static GameUser whereHas(string $relation, Closure $callback = null, string $operator = '>=', int $count = 1)
 * @method static GameUser withWhereHas(string $relation, Closure $callback = null, string $operator = '>=', int $count = 1)
 * @method static GameUser whereDoesntHave(string $relation, Closure $callback = null)
 * @method static GameUser whereRaw(string $sql, array $bindings = [])
 * @method static GameUser groupBy(string $groupBy)
 * @method static GameUser orderBy(string $column, string $direction = 'asc')
 * @method static GameUser orderByDesc(string $column)
 * @method static GameUser orderByRaw(string $sql, array $bindings = [])
 * @method static GameUser limit(int $value)
 * @method static int count(array $columns = ['*'])
 * @method static mixed sum(string $column)
 * @method static bool exists()
 *
 * @property bool $is_ready
 * @property float $game_points
 * @property string $game_id
 * @property string $user_id
 *
 * @property Game $game
 * @property BearUser $user
 *
 * AUTO GENERATED FILE DO NOT MODIFY
 */
final class GameUser extends Model {
    use BearLogDatabaseChanges;

    protected $connection = 'pgsql';
    protected $table = 'game_user';
    /** @var array<string> primaryKeyArray */
    private array $primaryKeyArray = ['game_id', 'user_id'];
    protected $keyType = 'array';
    public $incrementing = false;
    protected $dateFormat = 'Y-m-d\TH:i:sP';
    public $timestamps = false;

    public function game(): BelongsTo {
        return $this->belongsTo(related: Game::class, foreignKey: 'game_id', ownerKey: 'id');
    }

    public function user(): BelongsTo {
        return $this->belongsTo(related: BearUser::class, foreignKey: 'user_id', ownerKey: 'id');
    }

    protected $guarded = ['game_id', 'user_id', 'updated_at', 'created_at', 'deleted_at'];


    /** @return Mixed[] */
    public function getKey(): array {
        $attributes = [];
        foreach ($this->primaryKeyArray as $key) {
            $attributes[$key] = $this->getAttribute($key);
        }
        return $attributes;
    }

    /**
     * @param array<string, string> $ids # Ids in the form ['key1' => 'value1', 'key2' => 'value2']
     * @param array<string> $columns
     * @return GameUser|null
     */
    public static function find(array $ids, array $columns = ['*']): GameUser|null {
        $me = new self;
        $query = $me->newQuery();
        foreach ($me->primaryKeyArray as $key) {
            $query->where(column: $key, operator: '=', value: $ids[$key]);
        }
        $result = $query->first($columns);
        return $result instanceof self ? $result : null;
    }

    /**
     * @param array<string, string> $ids # Ids in the form ['key1' => 'value1', 'key2' => 'value2']
     * @param array<string> $columns
     * @return GameUser
     */
    public static function findOrFail(array $ids, array $columns = ['*']): GameUser {
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
