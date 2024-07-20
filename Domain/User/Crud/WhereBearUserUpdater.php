<?php declare(strict_types=1);

namespace Domain\User\Crud;

use Carbon\CarbonInterface;
use Domain\User\Model\WhereBearUser;
use GuardsmanPanda\Larabear\Infrastructure\Database\Service\BearDatabaseService;
use Illuminate\Database\Eloquent\Casts\ArrayObject;

final readonly class WhereBearUserUpdater {
    public function __construct(private WhereBearUser $model) {
        BearDatabaseService::mustBeInTransaction();
        BearDatabaseService::mustBeProperHttpMethod(verbs: ['POST', 'PATCH']);
    }

    public static function fromId(string $id): self {
        return new self(model: WhereBearUser::findOrFail(id: $id));
    }


    public function setUserDisplayName(string $user_display_name): self {
        $this->model->user_display_name = $user_display_name;
        return $this;
    }

    public function setIsUserActivated(bool $is_user_activated): self {
        $this->model->is_user_activated = $is_user_activated;
        return $this;
    }

    public function setUserDataJson(ArrayObject $user_data_json): self {
        $this->model->user_data_json = $user_data_json;
        return $this;
    }

    public function setMapMarkerEnum(string $map_marker_enum): self {
        $this->model->map_marker_enum = $map_marker_enum;
        return $this;
    }

    public function setMapStyleEnum(string $map_style_enum): self {
        $this->model->map_style_enum = $map_style_enum;
        return $this;
    }

    public function setUserEmail(string|null $user_email): self {
        $this->model->user_email = $user_email;
        return $this;
    }

    public function setUserCountryIso2Code(string|null $user_country_iso2_code): self {
        $this->model->user_country_iso2_code = $user_country_iso2_code;
        return $this;
    }

    public function setUserLanguageIso2Code(string|null $user_language_iso2_code): self {
        $this->model->user_language_iso2_code = $user_language_iso2_code;
        return $this;
    }

    public function setLastLoginAt(CarbonInterface|null $last_login_at): self {
        if ($last_login_at?->toIso8601String() === $this->model->last_login_at?->toIso8601String()) {
            return $this;
        }
        $this->model->last_login_at = $last_login_at;
        return $this;
    }

    public function setUserFirstName(string|null $user_first_name): self {
        $this->model->user_first_name = $user_first_name;
        return $this;
    }

    public function setUserLastName(string|null $user_last_name): self {
        $this->model->user_last_name = $user_last_name;
        return $this;
    }

    public function setUserCity(string|null $user_city): self {
        $this->model->user_city = $user_city;
        return $this;
    }

    public function setUserProfileImage(string|null $user_profile_image): self {
        $this->model->user_profile_image = $user_profile_image;
        return $this;
    }

    public function update(): WhereBearUser {
        $this->model->save();
        return $this->model;
    }
}
