<?php declare(strict_types=1);

namespace Domain\Panorama\Crud;

use Carbon\CarbonInterface;
use Domain\Panorama\Enum\PanoramaTagEnum;
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

  public function setCountryCca2(string|null $country_cca2): self {
    $this->model->country_cca2 = $country_cca2;
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

  public function setLocation(string|null $location): self {
    $this->model->location = $location;
    return $this;
  }

  public function setJpgPath(string|null $jpg_path): self {
    $this->model->jpg_path = $jpg_path;
    return $this;
  }

  public function addPanoramaTag(PanoramaTagEnum $tag): bool {
    foreach ($this->model->panorama_tag_array as $key => $value) {
      if ($value === $tag->value) {
        return false;
      }
    }
    $this->model->panorama_tag_array[] = $tag->value;
    return true;
  }

  public function removePanoramaTag(PanoramaTagEnum $tag): bool {
    foreach ($this->model->panorama_tag_array as $key => $value) {
      if ($value === $tag->value) {
        unset($this->model->panorama_tag_array[$key]);
        return true;
      }
    }
    return false;
  }

  public function update(): Panorama {
    $this->model->save();
    return $this->model;
  }
}
