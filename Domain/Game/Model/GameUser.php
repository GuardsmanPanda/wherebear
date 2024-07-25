<?php declare(strict_types=1);

namespace Domain\Game\Model;

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
 * @method static Builder<GameUser> lockForUpdate()
 * @method static Builder<GameUser> select(array $columns = ['*'])
 * @method static Builder<GameUser> selectRaw(string $expression, array $bindings = [])
 * @method static Builder<GameUser> with(array $relations)
 * @method static Builder<GameUser> leftJoin(string $table, string $first, string $operator = null, string $second = null)
 * @method static Builder<GameUser> where(string $column, string $operator = null, string|float|int|bool $value = null)
 * @method static Builder<GameUser> whereIn(string $column, array $values)
 * @method static Builder<GameUser> whereNull(string|array $columns)
 * @method static Builder<GameUser> whereNotNull(string|array $columns)
 * @method static Builder<GameUser> whereYear(string $column, string $operator, CarbonInterface|string|int $value)
 * @method static Builder<GameUser> whereMonth(string $column, string $operator, CarbonInterface|string|int $value)
 * @method static Builder<GameUser> whereDate(string $column, string $operator, CarbonInterface|string $value)
 * @method static Builder<GameUser> whereExists(Closure $callback)
 * @method static Builder<GameUser> whereNotExists(Closure $callback)
 * @method static Builder<GameUser> whereHas(string $relation, Closure $callback = null, string $operator = '>=', int $count = 1)
 * @method static Builder<GameUser> withWhereHas(string $relation, Closure $callback = null, string $operator = '>=', int $count = 1)
 * @method static Builder<GameUser> whereDoesntHave(string $relation, Closure $callback = null)
 * @method static Builder<GameUser> whereRaw(string $sql, array $bindings = [])
 * @method static Builder<GameUser> groupBy(string $groupBy)
 * @method static Builder<GameUser> orderBy(string $column, string $direction = 'asc')
 * @method static Builder<GameUser> orderByDesc(string $column)
 * @method static Builder<GameUser> orderByRaw(string $sql, array $bindings = [])
 * @method static Builder<GameUser> limit(int $value)
 * @method static int count(array $columns = ['*'])
 * @method static bool exists()
 *
 * @property bool $is_ready
 * @property float $points
 * @property string $game_id
 * @property string $user_id
 * @property string $created_at
 * @property string $updated_at
 *
 * @property Game $game
 * @property BearUser $user
 *
 * AUTO GENERATED FILE DO NOT MODIFY
 */
final class GameUser extends Model {
    use BearDatabaseChangeTrait;

    protected $connection = 'pgsql';
    protected $table = 'game_user';
    /** @var array<string> primaryKeyArray */
    private array $primaryKeyArray = ['game_id', 'user_id'];
    protected $keyType = 'array';
    public $incrementing = false;
    protected $dateFormat = 'Y-m-d\TH:i:sP';
    /** @var array<string> $log_exclude_columns */
    public array $log_exclude_columns = ['is_ready'];

    /** @return BelongsTo<Game, self> */
    public function game(): BelongsTo {
        return $this->belongsTo(related: Game::class, foreignKey: 'game_id', ownerKey: 'id');
    }

    /** @return BelongsTo<BearUser, self> */
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
     * @param array<string, string|int> $ids # Ids in the form ['key1' => 'value1', 'key2' => 'value2']
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
     * @param array<string, string|int> $ids # Ids in the form ['key1' => 'value1', 'key2' => 'value2']
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
