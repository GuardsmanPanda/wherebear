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


    public function setDisplayName(string $display_name): self {
        $this->model->display_name = $display_name;
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

    public function setEmail(string|null $email): self {
        $this->model->email = $email;
        return $this;
    }

    public function setCountryCca2(string $country_cca2): self {
        $this->model->country_cca2 = $country_cca2;
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
