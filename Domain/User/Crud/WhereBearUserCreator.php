<?php declare(strict_types=1);

namespace Domain\User\Crud;

use Carbon\CarbonImmutable;
use Domain\Map\Enum\MapMarkerEnum;
use Domain\Map\Enum\MapStyleEnum;
use Domain\User\Enum\UserLevelEnum;
use Domain\User\Model\WhereBearUser;
use GuardsmanPanda\Larabear\Infrastructure\App\Service\BearRegexService;
use GuardsmanPanda\Larabear\Infrastructure\Database\Service\BearDatabaseService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

final class WhereBearUserCreator {
    public static function create(
        string        $display_name,
        int           $experience,
        UserLevelEnum $user_level_enum,
        string        $email = null,
        string        $country_cca2 = null,
    ): WhereBearUser {
        BearDatabaseService::mustBeInTransaction();
        BearDatabaseService::mustBeProperHttpMethod(verbs: ['GET', 'POST']);

        $model = new WhereBearUser();
        $model->id = Str::uuid()->toString();

        $model->display_name = trim($display_name);
        $model->experience = $experience;
        $model->user_level_enum = $user_level_enum;
        $model->email = $email !== null ? BearRegexService::superTrim($email) : null;
        $model->country_cca2 = $country_cca2;
        $model->last_login_at = CarbonImmutable::now();
        $model->map_style_enum = MapStyleEnum::DEFAULT;

        $map_marker = DB::selectOne(query: "
            SELECT enum
            FROM map_marker
            WHERE user_level_enum <= ?
            ORDER BY random()
            LIMIT 1
        ", bindings: [$user_level_enum->value]);
        $model->map_marker_enum = MapMarkerEnum::from(value: $map_marker->enum);

        $model->save();
        return $model;
    }
}
