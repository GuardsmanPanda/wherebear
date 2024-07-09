<?php declare(strict_types=1);

namespace Domain\Panorama\Model;

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
 * @method static PanoramaUserRating sole(array $columns = ['*'])
 * @method static PanoramaUserRating|null first(array $columns = ['*'])
 * @method static PanoramaUserRating firstOrFail(array $columns = ['*'])
 * @method static PanoramaUserRating firstOrCreate(array $filter, array $values)
 * @method static PanoramaUserRating firstOrNew(array $filter, array $values)
 * @method static PanoramaUserRating|null firstWhere(string $column, string $operator, string|float|int|bool $value)
 * @method static Collection<int, PanoramaUserRating> all(array $columns = ['*'])
 * @method static Collection<int, PanoramaUserRating> get(array $columns = ['*'])
 * @method static Collection<int|string, PanoramaUserRating> pluck(string $column, string $key = null)
 * @method static Collection<int, PanoramaUserRating> fromQuery(string $query, array $bindings = [])
 * @method static PanoramaUserRating lockForUpdate()
 * @method static PanoramaUserRating select(array $columns = ['*'])
 * @method static PanoramaUserRating selectRaw(string $expression, array $bindings = [])
 * @method static PanoramaUserRating with(array $relations)
 * @method static PanoramaUserRating leftJoin(string $table, string $first, string $operator = null, string $second = null)
 * @method static PanoramaUserRating where(string $column, string $operator = null, string|float|int|bool $value = null)
 * @method static PanoramaUserRating whereIn(string $column, array $values)
 * @method static PanoramaUserRating whereNull(string|array $columns)
 * @method static PanoramaUserRating whereNotNull(string|array $columns)
 * @method static PanoramaUserRating whereYear(string $column, string $operator, CarbonInterface|string|int $value)
 * @method static PanoramaUserRating whereMonth(string $column, string $operator, CarbonInterface|string|int $value)
 * @method static PanoramaUserRating whereDate(string $column, string $operator, CarbonInterface|string $value)
 * @method static PanoramaUserRating whereExists(Closure $callback)
 * @method static PanoramaUserRating whereNotExists(Closure $callback)
 * @method static PanoramaUserRating whereHas(string $relation, Closure $callback = null, string $operator = '>=', int $count = 1)
 * @method static PanoramaUserRating withWhereHas(string $relation, Closure $callback = null, string $operator = '>=', int $count = 1)
 * @method static PanoramaUserRating whereDoesntHave(string $relation, Closure $callback = null)
 * @method static PanoramaUserRating whereRaw(string $sql, array $bindings = [])
 * @method static PanoramaUserRating groupBy(string $groupBy)
 * @method static PanoramaUserRating orderBy(string $column, string $direction = 'asc')
 * @method static PanoramaUserRating orderByDesc(string $column)
 * @method static PanoramaUserRating orderByRaw(string $sql, array $bindings = [])
 * @method static PanoramaUserRating limit(int $value)
 * @method static int count(array $columns = ['*'])
 * @method static mixed sum(string $column)
 * @method static bool exists()
 *
 * @property int $rating
 * @property string $user_id
 * @property string $created_at
 * @property string $updated_at
 * @property string $panorama_id
 *
 * @property Panorama $panorama
 * @property BearUser $user
 *
 * AUTO GENERATED FILE DO NOT MODIFY
 */
final class PanoramaUserRating extends Model {
    use BearLogDatabaseChanges;

    protected $connection = 'pgsql';
    protected $table = 'panorama_user_rating';
    /** @var array<string> primaryKeyArray */
    private array $primaryKeyArray = ['panorama_id', 'user_id'];
    protected $keyType = 'array';
    public $incrementing = false;
    protected $dateFormat = 'Y-m-d\TH:i:sP';

    public function panorama(): BelongsTo {
        return $this->belongsTo(related: Panorama::class, foreignKey: 'panorama_id', ownerKey: 'id');
    }

    public function user(): BelongsTo {
        return $this->belongsTo(related: BearUser::class, foreignKey: 'user_id', ownerKey: 'id');
    }

    protected $guarded = ['panorama_id', 'user_id', 'updated_at', 'created_at', 'deleted_at'];


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
     * @return PanoramaUserRating|null
     */
    public static function find(array $ids, array $columns = ['*']): PanoramaUserRating|null {
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
     * @return PanoramaUserRating
     */
    public static function findOrFail(array $ids, array $columns = ['*']): PanoramaUserRating {
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
