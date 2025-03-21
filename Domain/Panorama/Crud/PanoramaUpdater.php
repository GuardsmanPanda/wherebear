<?php declare(strict_types=1);

namespace Domain\Panorama\Crud;

use Domain\Panorama\Enum\PanoramaTagEnum;
use Domain\Panorama\Model\Panorama;
use GuardsmanPanda\Larabear\Infrastructure\Database\Service\BearDatabaseService;
use GuardsmanPanda\Larabear\Infrastructure\Locale\Enum\BearCountryEnum;
use GuardsmanPanda\Larabear\Infrastructure\Locale\Enum\BearCountrySubdivisionEnum;
use RuntimeException;

final readonly class PanoramaUpdater {
  public function __construct(private Panorama $model) {
    BearDatabaseService::mustBeInTransaction();
    BearDatabaseService::mustBeProperHttpMethod(verbs: ['POST', 'PATCH']);
  }

  public static function fromId(string $id): self {
    return new self(model: Panorama::findOrFail(id: $id));
  }


  public function hasDefaultFieldOfView(): bool {
    return $this->model->field_of_view === 100.0;
  }


  public function setCountryCca2(string $country_cca2): self {
    $this->model->country_cca2 = BearCountryEnum::from(value: $country_cca2);
    return $this;
  }

  public function setCountrySubdivisionIso3166(string|null $country_subdivision_iso_3166): self {
    if ($country_subdivision_iso_3166 !== null) {
      $this->model->country_subdivision_iso_3166 = BearCountrySubdivisionEnum::from(value: $country_subdivision_iso_3166);
    } else {
      $this->model->country_subdivision_iso_3166 = null;
    }
    return $this;
  }

  public function setJpgPath(string $jpg_path): self {
    $this->model->jpg_path = $jpg_path;
    return $this;
  }

  public function addPanoramaTag(PanoramaTagEnum $tag): bool {
    if (array_any($this->model->panorama_tag_array->getArrayCopy(), fn($value) => $value === $tag->value)) {
      return false;
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

  public function setNorthRotationDegrees(int $north_rotation_degrees): self {
    if ($this->model->north_rotation_degrees !== null) {
      throw new RuntimeException(message: 'North rotation degrees should not be set more than once.');
    }
    $this->model->heading = $this->calculatedNewHeading(heading: $this->model->heading, north_rotation_degrees: $north_rotation_degrees);
    $this->model->north_rotation_degrees = $north_rotation_degrees;
    return $this;
  }


  public function setViewport(float $heading, float $pitch, float $field_of_view): self {
    $this->model->heading = $heading;
    $this->model->pitch = $pitch;
    $this->model->field_of_view = $field_of_view;
    return $this;
  }


  public function setStreetViewViewport(float $heading, float $pitch): self {
    if ($this->model->north_rotation_degrees === null) {
      $this->model->heading = $heading;
    } else {
      $this->model->heading = $this->calculatedNewHeading(heading: $heading, north_rotation_degrees: $this->model->north_rotation_degrees);
    }
    $this->model->pitch = $pitch;
    return $this;
  }


  public function setRetiredStatus(bool $retired, ?string $retired_reason): self {
    if ($retired) {
      $this->model->retired_at = now();
      if ($retired_reason === null) {
        throw new RuntimeException(message: 'Retired panoramas must have a reason.');
      }
    } else {
      $this->model->retired_at = null;

      if ($retired_reason !== null) {
        throw new RuntimeException(message: 'Unretired panoramas must not have a reason.');
      }
    }
    $this->model->retired_reason = $retired_reason;
    return $this;
  }


  private function calculatedNewHeading(float $heading, int $north_rotation_degrees): float {
    $heading += $north_rotation_degrees - 180;
    while ($heading < -180) {
      $heading += 360;
    }
    while ($heading > 180) {
      $heading -= 360;
    }
    return $heading;
  }

  public function update(): Panorama {
    $this->model->save();
    return $this->model;
  }
}
