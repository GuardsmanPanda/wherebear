<?php declare(strict_types=1);

namespace Domain\Panorama\Crud;

use Carbon\Carbon;
use Carbon\CarbonInterface;
use Domain\Panorama\Model\Panorama;
use GuardsmanPanda\Larabear\Infrastructure\Auth\Service\BearAuthService;
use GuardsmanPanda\Larabear\Infrastructure\Database\Service\BearDatabaseService;
use Illuminate\Support\Facades\DB;
use Integration\Nominatim\Client\NominatimClient;

final class PanoramaCreator {
    public static function create(
        string $id,
        float $latitude,
        float $longitude,
        CarbonInterface $captured_date,
        string $added_by_user_id = null,
        string $jpg_name = null,
    ): Panorama {
        BearDatabaseService::mustBeInTransaction();
        BearDatabaseService::mustBeProperHttpMethod(verbs: ['POST']);

        $data = NominatimClient::reverseLookup(latitude: $latitude, longitude: $longitude);

        DB::insert(query: "
            INSERT INTO panorama (
                id, captured_date, country_iso_2_code, state_name, city_name, added_by_user_id,
                panorama_location, jpg_name, nominatim_json, created_at, updated_at
            ) VALUES (?, ?, ?, ?, ?, ?, public.ST_MakePoint(?::double precision, ?::double precision), ?, ?, NOW(), NOW())
        ", bindings: [
            $id,
            $captured_date,
            $data->country_iso2_code,
            $data->state_name,
            $data->city_name,
            $added_by_user_id,
            $longitude, $latitude,
            $jpg_name,
            json_encode($data->nominatim_json)
        ]);
        return Panorama::findOrFail(id: $id);
    }

    public static function createFromStreetViewData(array $data, string $added_by_user_id = null): Panorama {
        return PanoramaCreator::create(
            id: $data['pano_id'],
            latitude: $data['location']['lat'],
            longitude: $data['location']['lng'],
            captured_date: Carbon::parse($data['date'] . "-01"),
            added_by_user_id: $added_by_user_id,
        );
    }
}
