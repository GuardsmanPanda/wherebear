<?php declare(strict_types=1);

namespace Domain\Panorama\Model;

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
 * @method static PanoramaUserRating sole(array $columns = ['*'])
 * @method static PanoramaUserRating|null first(array $columns = ['*'])
 * @method static PanoramaUserRating firstOrFail(array $columns = ['*'])
 * @method static PanoramaUserRating firstOrCreate(array $filter, array $values)
 * @method static PanoramaUserRating firstOrNew(array $filter, array $values)
 * @method static PanoramaUserRating|null firstWhere(string $column, string $operator, string|float|int|bool $value)
 * @method static Collection<int, PanoramaUserRating> all(array $columns = ['*'])
 * @method static Collection<int, PanoramaUserRating> get(array $columns = ['*'])
 * @method static Collection<array-key, PanoramaUserRating> pluck(string $column, string $key = null)
 * @method static Collection<int, PanoramaUserRating> fromQuery(string $query, array $bindings = [])
 * @method static Builder<PanoramaUserRating> lockForUpdate()
 * @method static Builder<PanoramaUserRating> select(array $columns = ['*'])
 * @method static Builder<PanoramaUserRating> selectRaw(string $expression, array $bindings = [])
 * @method static Builder<PanoramaUserRating> with(array $relations)
 * @method static Builder<PanoramaUserRating> leftJoin(string $table, string $first, string $operator = null, string $second = null)
 * @method static Builder<PanoramaUserRating> where(string $column, string $operator = null, string|float|int|bool $value = null)
 * @method static Builder<PanoramaUserRating> whereIn(string $column, array $values)
 * @method static Builder<PanoramaUserRating> whereNull(string|array $columns)
 * @method static Builder<PanoramaUserRating> whereNotNull(string|array $columns)
 * @method static Builder<PanoramaUserRating> whereYear(string $column, string $operator, CarbonInterface|string|int $value)
 * @method static Builder<PanoramaUserRating> whereMonth(string $column, string $operator, CarbonInterface|string|int $value)
 * @method static Builder<PanoramaUserRating> whereDate(string $column, string $operator, CarbonInterface|string $value)
 * @method static Builder<PanoramaUserRating> whereExists(Closure $callback)
 * @method static Builder<PanoramaUserRating> whereNotExists(Closure $callback)
 * @method static Builder<PanoramaUserRating> whereHas(string $relation, Closure $callback = null, string $operator = '>=', int $count = 1)
 * @method static Builder<PanoramaUserRating> withWhereHas(string $relation, Closure $callback = null, string $operator = '>=', int $count = 1)
 * @method static Builder<PanoramaUserRating> whereDoesntHave(string $relation, Closure $callback = null)
 * @method static Builder<PanoramaUserRating> whereRaw(string $sql, array $bindings = [])
 * @method static Builder<PanoramaUserRating> groupBy(string $groupBy)
 * @method static Builder<PanoramaUserRating> orderBy(string $column, string $direction = 'asc')
 * @method static Builder<PanoramaUserRating> orderByDesc(string $column)
 * @method static Builder<PanoramaUserRating> orderByRaw(string $sql, array $bindings = [])
 * @method static Builder<PanoramaUserRating> limit(int $value)
 * @method static int count(array $columns = ['*'])
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
    use BearDatabaseChangeTrait;

    protected $connection = 'pgsql';
    protected $table = 'panorama_user_rating';
    /** @var array<string> primaryKeyArray */
    private array $primaryKeyArray = ['panorama_id', 'user_id'];
    protected $keyType = 'array';
    public $incrementing = false;
    protected $dateFormat = 'Y-m-d\TH:i:sP';

    /** @return BelongsTo<Panorama, self> */
    public function panorama(): BelongsTo {
        return $this->belongsTo(related: Panorama::class, foreignKey: 'panorama_id', ownerKey: 'id');
    }

    /** @return BelongsTo<BearUser, self> */
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
     * @param array<string, string|int> $ids # Ids in the form ['key1' => 'value1', 'key2' => 'value2']
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
     * @param array<string, string|int> $ids # Ids in the form ['key1' => 'value1', 'key2' => 'value2']
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
