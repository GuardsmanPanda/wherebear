<?php declare(strict_types=1);

namespace Domain\User\Crud;

use Carbon\CarbonInterface;
use Domain\Map\Enum\MapMarkerEnum;
use Domain\Map\Enum\MapStyleEnum;
use Domain\User\Enum\UserFlagEnum;
use Domain\User\Model\WhereBearUser;
use GuardsmanPanda\Larabear\Infrastructure\Database\Service\BearDatabaseService;

final readonly class WhereBearUserUpdater {
    public function __construct(private WhereBearUser $model) {
        BearDatabaseService::mustBeInTransaction();
        BearDatabaseService::mustBeProperHttpMethod(verbs: ['POST', 'PATCH']);
    }

    public static function fromId(string $id): self {
        return new self(model: WhereBearUser::findOrFail(id: $id));
    }


    public function setDisplayName(string $display_name): self {
        $this->model->display_name = $display_name;
        return $this;
    }

    public function setMapMarkerEnum(MapMarkerEnum $map_marker_enum): self {
        $this->model->map_marker_enum = $map_marker_enum;
        return $this;
    }

    public function setMapStyleEnum(MapStyleEnum $map_style_enum): self {
        $this->model->map_style_enum = $map_style_enum;
        return $this;
    }


    public function setCountryCca2(string $country_cca2): self {
        $this->model->country_cca2 = $country_cca2;
        $this->model->user_flag_enum = null;
        return $this;
    }

    public function setUserFlag(UserFlagEnum $enum): self {
        $this->model->user_flag_enum = $enum;
        return $this;
    }

    public function setLastLoginAt(CarbonInterface|null $last_login_at): self {
        if ($last_login_at?->toIso8601String() === $this->model->last_login_at?->toIso8601String()) {
            return $this;
        }
        $this->model->last_login_at = $last_login_at;
        return $this;
    }


    public function update(): WhereBearUser {
        $this->model->save();
        return $this->model;
    }
}
