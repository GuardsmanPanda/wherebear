<?php declare(strict_types=1);

namespace Domain\Game\Model;

use Carbon\CarbonInterface;
use Closure;
use Domain\Panorama\Model\Panorama;
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
 * @method static GameRound sole(array $columns = ['*'])
 * @method static GameRound|null first(array $columns = ['*'])
 * @method static GameRound firstOrFail(array $columns = ['*'])
 * @method static GameRound firstOrCreate(array $filter, array $values)
 * @method static GameRound firstOrNew(array $filter, array $values)
 * @method static GameRound|null firstWhere(string $column, string $operator, string|float|int|bool $value)
 * @method static Collection<int, GameRound> all(array $columns = ['*'])
 * @method static Collection<int, GameRound> get(array $columns = ['*'])
 * @method static Collection<int, GameRound> fromQuery(string $query, array $bindings = [])
 * @method static Builder<GameRound> lockForUpdate()
 * @method static Builder<GameRound> select(array $columns = ['*'])
 * @method static Builder<GameRound> selectRaw(string $expression, array $bindings = [])
 * @method static Builder<GameRound> with(array $relations)
 * @method static Builder<GameRound> leftJoin(string $table, string $first, string $operator = null, string $second = null)
 * @method static Builder<GameRound> where(string $column, string $operator = null, string|float|int|bool $value = null)
 * @method static Builder<GameRound> whereIn(string $column, array $values)
 * @method static Builder<GameRound> whereNull(string|array $columns)
 * @method static Builder<GameRound> whereNotNull(string|array $columns)
 * @method static Builder<GameRound> whereYear(string $column, string $operator, CarbonInterface|string|int $value)
 * @method static Builder<GameRound> whereMonth(string $column, string $operator, CarbonInterface|string|int $value)
 * @method static Builder<GameRound> whereDate(string $column, string $operator, CarbonInterface|string $value)
 * @method static Builder<GameRound> whereExists(Closure $callback)
 * @method static Builder<GameRound> whereNotExists(Closure $callback)
 * @method static Builder<GameRound> whereHas(string $relation, Closure $callback = null, string $operator = '>=', int $count = 1)
 * @method static Builder<GameRound> withWhereHas(string $relation, Closure $callback = null, string $operator = '>=', int $count = 1)
 * @method static Builder<GameRound> whereDoesntHave(string $relation, Closure $callback = null)
 * @method static Builder<GameRound> whereRaw(string $sql, array $bindings = [])
 * @method static Builder<GameRound> groupBy(string $groupBy)
 * @method static Builder<GameRound> orderBy(string $column, string $direction = 'asc')
 * @method static Builder<GameRound> orderByDesc(string $column)
 * @method static Builder<GameRound> orderByRaw(string $sql, array $bindings = [])
 * @method static Builder<GameRound> limit(int $value)
 * @method static int count(array $columns = ['*'])
 * @method static bool exists()
 *
 * @property int $round_number
 * @property string $game_id
 * @property string $created_at
 * @property string $updated_at
 * @property string $panorama_pick_strategy
 * @property string|null $panorama_id
 *
 * @property Panorama|null $panorama
 * @property Game $game
 *
 * AUTO GENERATED FILE DO NOT MODIFY
 */
final class GameRound extends Model {
    use BearDatabaseChangeTrait;

    protected $connection = 'pgsql';
    protected $table = 'game_round';
    /** @var array<string> primaryKeyArray */
    private array $primaryKeyArray = ['game_id', 'round_number'];
    protected $keyType = 'array';
    public $incrementing = false;
    protected $dateFormat = 'Y-m-d\TH:i:sP';

    /** @return BelongsTo<Panorama, self>|null */
    public function panorama(): BelongsTo|null {
        return $this->belongsTo(related: Panorama::class, foreignKey: 'panorama_id', ownerKey: 'id');
    }

    /** @return BelongsTo<Game, self> */
    public function game(): BelongsTo {
        return $this->belongsTo(related: Game::class, foreignKey: 'game_id', ownerKey: 'id');
    }

    protected $guarded = ['game_id', 'round_number', 'updated_at', 'created_at', 'deleted_at'];


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
     * @return GameRound|null
     */
    public static function find(array $ids, array $columns = ['*']): GameRound|null {
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
     * @return GameRound
     */
    public static function findOrFail(array $ids, array $columns = ['*']): GameRound {
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
