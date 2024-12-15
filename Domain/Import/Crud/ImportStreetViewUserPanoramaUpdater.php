<?php declare(strict_types=1);

namespace Domain\Import\Crud;

use Carbon\CarbonImmutable;
use Carbon\CarbonInterface;
use Domain\Import\Enum\ImportStatusEnum;
use Domain\Import\Model\ImportStreetViewUserPanorama;
use GuardsmanPanda\Larabear\Infrastructure\App\DataType\BearPoint;
use GuardsmanPanda\Larabear\Infrastructure\Database\Service\BearDatabaseService;
use Illuminate\Support\Facades\DB;

final readonly class ImportStreetViewUserPanoramaUpdater {
    public function __construct(private ImportStreetViewUserPanorama $model) {
        BearDatabaseService::mustBeInTransaction();
        BearDatabaseService::mustBeProperHttpMethod(verbs: ['POST', 'PATCH']);
    }

    public static function fromId(string $id): self {
        return new self(model: ImportStreetViewUserPanorama::findOrFail(id: $id));
    }

    public static function specialUpdate(
      string $id,
      float $lat,
      float $lng,
      CarbonImmutable $captured_date,
      ImportStatusEnum $import_status_enum,
    ): void {
      DB::update(query: "
        UPDATE import_street_view_user_panorama
        SET captured_date = :captured_date,
            country_cca2 = wherebear_country(:lng, :lat),
            country_subdivision_iso_3166 = wherebear_subdivision(:lng, :lat, wherebear_country(:lng, :lat)),
            location = ST_Point(:lng, :lat, 4326)::geography,
            import_status_enum = :import_status_enum,
            updated_at = CURRENT_TIMESTAMP
        WHERE id = :id
      ", bindings: [
        'id' => $id,
        'lat' => $lat,
        'lng' => $lng,
        'captured_date' => $captured_date,
        'import_status_enum' => $import_status_enum->value,
      ]);
    }


    public function setPanoramaId(string $panorama_id): self {
        $this->model->panorama_id = $panorama_id;
        return $this;
    }

    public function setImportStatusEnum(ImportStatusEnum $import_status_enum): self {
        $this->model->import_status_enum = $import_status_enum;
        return $this;
    }

    public function setCapturedDate(CarbonInterface|null $captured_date): self {
        if ($captured_date?->toDateString() === $this->model->captured_date?->toDateString()) {
            return $this;
        }
        $this->model->captured_date = $captured_date;
        return $this;
    }

    public function setLocation(BearPoint|null $location): self {
        $this->model->location = $location;
        return $this;
    }

    public function update(): ImportStreetViewUserPanorama {
        $this->model->save();
        return $this->model;
    }
}
