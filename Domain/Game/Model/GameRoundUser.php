<?php declare(strict_types=1);

namespace Domain\Game\Model;

use Carbon\CarbonInterface;
use Closure;
use GuardsmanPanda\Larabear\Infrastructure\Database\Traits\BearLogDatabaseChanges;
use GuardsmanPanda\Larabear\Infrastructure\Locale\Model\BearCountry;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Eloquent\Casts\ArrayObject;
use Illuminate\Database\Eloquent\Casts\AsArrayObject;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use RuntimeException;

/**
 * AUTO GENERATED FILE DO NOT MODIFY
 *
 * @method static GameRoundUser sole(array $columns = ['*'])
 * @method static GameRoundUser|null first(array $columns = ['*'])
 * @method static GameRoundUser firstOrFail(array $columns = ['*'])
 * @method static GameRoundUser firstOrCreate(array $filter, array $values)
 * @method static GameRoundUser firstOrNew(array $filter, array $values)
 * @method static GameRoundUser|null firstWhere(string $column, string $operator, string|float|int|bool $value)
 * @method static Collection<int, GameRoundUser> all(array $columns = ['*'])
 * @method static Collection<int, GameRoundUser> get(array $columns = ['*'])
 * @method static Collection<int|string, GameRoundUser> pluck(string $column, string $key = null)
 * @method static Collection<int, GameRoundUser> fromQuery(string $query, array $bindings = [])
 * @method static GameRoundUser lockForUpdate()
 * @method static GameRoundUser select(array $columns = ['*'])
 * @method static GameRoundUser selectRaw(string $expression, array $bindings = [])
 * @method static GameRoundUser with(array $relations)
 * @method static GameRoundUser leftJoin(string $table, string $first, string $operator = null, string $second = null)
 * @method static GameRoundUser where(string $column, string $operator = null, string|float|int|bool $value = null)
 * @method static GameRoundUser whereIn(string $column, array $values)
 * @method static GameRoundUser whereNull(string|array $columns)
 * @method static GameRoundUser whereNotNull(string|array $columns)
 * @method static GameRoundUser whereYear(string $column, string $operator, CarbonInterface|string|int $value)
 * @method static GameRoundUser whereMonth(string $column, string $operator, CarbonInterface|string|int $value)
 * @method static GameRoundUser whereDate(string $column, string $operator, CarbonInterface|string $value)
 * @method static GameRoundUser whereExists(Closure $callback)
 * @method static GameRoundUser whereNotExists(Closure $callback)
 * @method static GameRoundUser whereHas(string $relation, Closure $callback = null, string $operator = '>=', int $count = 1)
 * @method static GameRoundUser withWhereHas(string $relation, Closure $callback = null, string $operator = '>=', int $count = 1)
 * @method static GameRoundUser whereDoesntHave(string $relation, Closure $callback = null)
 * @method static GameRoundUser whereRaw(string $sql, array $bindings = [])
 * @method static GameRoundUser groupBy(string $groupBy)
 * @method static GameRoundUser orderBy(string $column, string $direction = 'asc')
 * @method static GameRoundUser orderByDesc(string $column)
 * @method static GameRoundUser orderByRaw(string $sql, array $bindings = [])
 * @method static GameRoundUser limit(int $value)
 * @method static int count(array $columns = ['*'])
 * @method static mixed sum(string $column)
 * @method static bool exists()
 *
 * @property int $round_number
 * @property int|null $round_rank
 * @property float|null $round_points
 * @property float|null $distance_meters
 * @property float|null $approximate_country_distance_meters
 * @property string $game_id
 * @property string $user_id
 * @property string $location
 * @property string $created_at
 * @property string $updated_at
 * @property string|null $correct_country_iso_2_code
 * @property string|null $approximate_country_iso_2_code
 * @property ArrayObject|null $nominatim_json
 *
 * @property GameUser $game
 * @property GameUser $user
 * @property BearCountry|null $approximateCountryIso2Code
 * @property BearCountry|null $correctCountryIso2Code
 * @property GameRound $roundNumber
 *
 * AUTO GENERATED FILE DO NOT MODIFY
 */
final class GameRoundUser extends Model {
    use BearLogDatabaseChanges;

    protected $connection = 'pgsql';
    protected $table = 'game_round_user';
    /** @var array<string> primaryKeyArray */
    private array $primaryKeyArray = ['game_id', 'round_number', 'user_id'];
    protected $keyType = 'array';
    public $incrementing = false;
    protected $dateFormat = 'Y-m-d\TH:i:sP';

    /** @var array<string, string> $casts */
    protected $casts = [
        'nominatim_json' => AsArrayObject::class,
    ];

    public function game(): BelongsTo {
        return $this->belongsTo(related: GameUser::class, foreignKey: 'game_id', ownerKey: 'user_id');
    }

    public function user(): BelongsTo {
        return $this->belongsTo(related: GameUser::class, foreignKey: 'user_id', ownerKey: 'game_id');
    }

    public function approximateCountryIso2Code(): BelongsTo|null {
        return $this->belongsTo(related: BearCountry::class, foreignKey: 'approximate_country_iso_2_code', ownerKey: 'country_iso2_code');
    }

    public function correctCountryIso2Code(): BelongsTo|null {
        return $this->belongsTo(related: BearCountry::class, foreignKey: 'correct_country_iso_2_code', ownerKey: 'country_iso2_code');
    }

    public function roundNumber(): BelongsTo {
        return $this->belongsTo(related: GameRound::class, foreignKey: 'round_number', ownerKey: 'round_number');
    }

    protected $guarded = ['game_id', 'round_number', 'user_id', 'updated_at', 'created_at', 'deleted_at'];


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
     * @return GameRoundUser|null
     */
    public static function find(array $ids, array $columns = ['*']): GameRoundUser|null {
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
     * @return GameRoundUser
     */
    public static function findOrFail(array $ids, array $columns = ['*']): GameRoundUser {
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
