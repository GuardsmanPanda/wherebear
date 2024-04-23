<?php declare(strict_types=1);

namespace Domain\Panorama\Crud;

use Carbon\CarbonInterface;
use Domain\Panorama\Model\Panorama;
use GuardsmanPanda\Larabear\Infrastructure\Database\Service\BearDatabaseService;

final class PanoramaUpdater {
    public function __construct(private readonly Panorama $model) {
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

    public function setIsRetired(bool $is_retired): self {
        $this->model->is_retired = $is_retired;
        return $this;
    }

    public function setCountryIso2Code(string|null $country_iso_2_code): self {
        $this->model->country_iso_2_code = $country_iso_2_code;
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

    public function setRegionName(string|null $region_name): self {
        $this->model->region_name = $region_name;
        return $this;
    }

    public function setStateDistrictName(string|null $state_district_name): self {
        $this->model->state_district_name = $state_district_name;
        return $this;
    }

    public function setCountyName(string|null $county_name): self {
        $this->model->county_name = $county_name;
        return $this;
    }

    public function setJpgName(string|null $jpg_name): self {
        $this->model->jpg_name = $jpg_name;
        return $this;
    }

    public function update(): Panorama {
        $this->model->save();
        return $this->model;
    }
}
