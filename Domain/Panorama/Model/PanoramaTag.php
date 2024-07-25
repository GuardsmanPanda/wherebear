<?php declare(strict_types=1);

namespace Domain\Panorama\Model;

use Carbon\CarbonInterface;
use Closure;
use Domain\Panorama\Enum\TagEnum;
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
 * @method static PanoramaTag sole(array $columns = ['*'])
 * @method static PanoramaTag|null first(array $columns = ['*'])
 * @method static PanoramaTag firstOrFail(array $columns = ['*'])
 * @method static PanoramaTag firstOrCreate(array $filter, array $values)
 * @method static PanoramaTag firstOrNew(array $filter, array $values)
 * @method static PanoramaTag|null firstWhere(string $column, string $operator, string|float|int|bool $value)
 * @method static Collection<int, PanoramaTag> all(array $columns = ['*'])
 * @method static Collection<int, PanoramaTag> get(array $columns = ['*'])
 * @method static Collection<int|string, PanoramaTag> pluck(string $column, string $key = null)
 * @method static Collection<int, PanoramaTag> fromQuery(string $query, array $bindings = [])
 * @method static Builder<PanoramaTag> lockForUpdate()
 * @method static Builder<PanoramaTag> select(array $columns = ['*'])
 * @method static Builder<PanoramaTag> selectRaw(string $expression, array $bindings = [])
 * @method static Builder<PanoramaTag> with(array $relations)
 * @method static Builder<PanoramaTag> leftJoin(string $table, string $first, string $operator = null, string $second = null)
 * @method static Builder<PanoramaTag> where(string $column, string $operator = null, string|float|int|bool $value = null)
 * @method static Builder<PanoramaTag> whereIn(string $column, array $values)
 * @method static Builder<PanoramaTag> whereNull(string|array $columns)
 * @method static Builder<PanoramaTag> whereNotNull(string|array $columns)
 * @method static Builder<PanoramaTag> whereYear(string $column, string $operator, CarbonInterface|string|int $value)
 * @method static Builder<PanoramaTag> whereMonth(string $column, string $operator, CarbonInterface|string|int $value)
 * @method static Builder<PanoramaTag> whereDate(string $column, string $operator, CarbonInterface|string $value)
 * @method static Builder<PanoramaTag> whereExists(Closure $callback)
 * @method static Builder<PanoramaTag> whereNotExists(Closure $callback)
 * @method static Builder<PanoramaTag> whereHas(string $relation, Closure $callback = null, string $operator = '>=', int $count = 1)
 * @method static Builder<PanoramaTag> withWhereHas(string $relation, Closure $callback = null, string $operator = '>=', int $count = 1)
 * @method static Builder<PanoramaTag> whereDoesntHave(string $relation, Closure $callback = null)
 * @method static Builder<PanoramaTag> whereRaw(string $sql, array $bindings = [])
 * @method static Builder<PanoramaTag> groupBy(string $groupBy)
 * @method static Builder<PanoramaTag> orderBy(string $column, string $direction = 'asc')
 * @method static Builder<PanoramaTag> orderByDesc(string $column)
 * @method static Builder<PanoramaTag> orderByRaw(string $sql, array $bindings = [])
 * @method static Builder<PanoramaTag> limit(int $value)
 * @method static int count(array $columns = ['*'])
 * @method static bool exists()
 *
 * @property string $created_at
 * @property string $panorama_id
 * @property string $created_by_user_id
 * @property TagEnum $tag_enum
 *
 * @property Panorama $panorama
 * @property BearUser $createdByUser
 * @property Tag $tag
 *
 * AUTO GENERATED FILE DO NOT MODIFY
 */
final class PanoramaTag extends Model {
    use BearDatabaseChangeTrait;

    protected $connection = 'pgsql';
    protected $table = 'panorama_tag';
    /** @var array<string> primaryKeyArray */
    private array $primaryKeyArray = ['panorama_id', 'tag_enum'];
    protected $keyType = 'array';
    public $incrementing = false;
    protected $dateFormat = 'Y-m-d\TH:i:sP';
    public $timestamps = false;

    /** @var array<string, string> $casts */
    protected $casts = [
        'tag_enum' => TagEnum::class,
    ];

    /** @return BelongsTo<Panorama, self> */
    public function panorama(): BelongsTo {
        return $this->belongsTo(related: Panorama::class, foreignKey: 'panorama_id', ownerKey: 'id');
    }

    /** @return BelongsTo<BearUser, self> */
    public function createdByUser(): BelongsTo {
        return $this->belongsTo(related: BearUser::class, foreignKey: 'created_by_user_id', ownerKey: 'id');
    }

    /** @return BelongsTo<Tag, self> */
    public function tag(): BelongsTo {
        return $this->belongsTo(related: Tag::class, foreignKey: 'tag_enum', ownerKey: 'enum');
    }

    protected $guarded = ['panorama_id', 'tag_enum', 'updated_at', 'created_at', 'deleted_at'];


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
     * @return PanoramaTag|null
     */
    public static function find(array $ids, array $columns = ['*']): PanoramaTag|null {
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
     * @return PanoramaTag
     */
    public static function findOrFail(array $ids, array $columns = ['*']): PanoramaTag {
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
