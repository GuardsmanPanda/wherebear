<?php declare(strict_types=1);

namespace Domain\User\Crud;

use Carbon\CarbonImmutable;
use Domain\Map\Enum\MapMarkerEnum;
use Domain\Map\Enum\MapStyleEnum;
use Domain\User\Model\WhereBearUser;
use GuardsmanPanda\Larabear\Infrastructure\App\Service\BearRegexService;
use GuardsmanPanda\Larabear\Infrastructure\Database\Service\BearDatabaseService;
use Illuminate\Support\Str;

final class WhereBearUserCreator {
    public static function create(
        string $user_display_name,
        int $user_experience,
        int $user_level_id,
        string $user_email = null,
        string $user_country_iso2_code = null,
        MapMarkerEnum $map_marker_enum = MapMarkerEnum::DEFAULT,
        MapStyleEnum $map_style_enum = MapStyleEnum::DEFAULT,
    ): WhereBearUser {
        BearDatabaseService::mustBeInTransaction();
        BearDatabaseService::mustBeProperHttpMethod(verbs: ['GET', 'POST']);

        $model = new WhereBearUser();
        $model->id = Str::uuid()->toString();

        $model->user_display_name = trim($user_display_name);
        $model->is_user_activated = true;
        $model->user_experience = $user_experience;
        $model->user_level_id = $user_level_id;
        $model->user_email = $user_email !== null ? BearRegexService::superTrim($user_email) : null;
        $model->user_country_iso2_code = $user_country_iso2_code;
        $model->user_language_iso2_code = 'en';
        $model->last_login_at = CarbonImmutable::now();
        $model->map_marker_enum = $map_marker_enum->value;
        $model->map_style_enum = $map_style_enum->value;

        $model->save();
        return $model;
    }
}
