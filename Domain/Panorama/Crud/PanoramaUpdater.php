<?php declare(strict_types=1);

namespace Domain\Panorama\Crud;

use Carbon\CarbonInterface;
use Domain\Panorama\Model\Panorama;
use GuardsmanPanda\Larabear\Infrastructure\Database\Service\BearDatabaseService;

final readonly class PanoramaUpdater {
    public function __construct(private Panorama $model) {
        BearDatabaseService::mustBeInTransaction();
        BearDatabaseService::mustBeProperHttpMethod(verbs: ['POST', 'PATCH']);
    }

    public static function fromId(string $id): self {
        return new self(model: Panorama::findOrFail(id: $id));
    }


    public function setCapturedDate(CarbonInterface $captured_date): self {
        if ($captured_date->toDateString() === $this->model->captured_date->toDateString()) {
            return $this;
        }
        $this->model->captured_date = $captured_date;
        return $this;
    }

    public function setCountryIso2Code(string|null $country_iso2_code): self {
        $this->model->country_iso2_code = $country_iso2_code;
        return $this;
    }

    public function setStateName(string|null $state_name): self {
        $this->model->state_name = $state_name;
        return $this;
    }

    public function setCityName(string|null $city_name): self {
        $this->model->city_name = $city_name;
        return $this;
    }

    public function setAddedByUserId(string|null $added_by_user_id): self {
        $this->model->added_by_user_id = $added_by_user_id;
        return $this;
    }

    public function setPanoramaLocation(string|null $panorama_location): self {
        $this->model->panorama_location = $panorama_location;
        return $this;
    }

    public function setJpgPath(string|null $jpg_path): self {
        $this->model->jpg_path = $jpg_path;
        return $this;
    }

    public function update(): Panorama {
        $this->model->save();
        return $this->model;
    }
}
