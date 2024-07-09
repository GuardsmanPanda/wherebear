<?php declare(strict_types=1);

namespace Domain\Panorama\Crud;

use GuardsmanPanda\Larabear\Infrastructure\Database\Service\BearDatabaseService;
use Illuminate\Support\Facades\DB;

final class PanoramaUserRatingCrud {
    public static function createOrUpdate(
        string $panorama_id,
        string $user_id,
        int $rating
    ): void {
        BearDatabaseService::mustBeProperHttpMethod(verbs: ['PUT']);
        BearDatabaseService::mustBeInTransaction();
        DB::insert(query: "
            INSERT INTO panorama_user_rating (panorama_id, user_id, rating)
            VALUES (?, ?, ?)
            ON CONFLICT (panorama_id, user_id) DO UPDATE
            SET rating = excluded.rating, updated_at = CURRENT_TIMESTAMP
        ", bindings: [$panorama_id, $user_id, $rating]);
    }
}
