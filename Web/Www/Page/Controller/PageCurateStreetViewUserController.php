<?php declare(strict_types=1);

namespace Web\Www\Page\Controller;

use Domain\Import\Crud\ImportStreetViewUserCreator;
use Domain\Import\Crud\ImportStreetViewUserPanoramaUpdater;
use Domain\Import\Enum\ImportStatusEnum;
use GuardsmanPanda\Larabear\Infrastructure\Http\Service\Req;
use GuardsmanPanda\Larabear\Infrastructure\Http\Service\Resp;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

final class PageCurateStreetViewUserController extends Controller {
  public function index(): View {
    return Resp::view(view: 'page::curate.street-view-user', data: [
      'users' => DB::select(query: <<<SQL
        SELECT
          u.id, u.name, u.last_sync_at,
          COUNT(up.id) as panoramas_count,
          MAX(up.captured_date) as latest_captured_date,
          COUNT(up.id) FILTER (WHERE up.import_status_enum = 'LOCATION_ADDED') as location_added_count,
          COUNT(up.id) FILTER (WHERE up.import_status_enum = 'IMPORTED_PANORAMA') as imported_count,
          COUNT(up.id) FILTER (WHERE up.import_status_enum = 'REJECTED_PANORAMA') as rejected_count
        FROM import_street_view_user u
        LEFT JOIN import_street_view_user_panorama up ON u.id = up.import_street_view_user_id
        GROUP BY u.id, u.name
        ORDER BY location_added_count DESC, u.name
      SQL),
    ]);
  }

  public function create(): View {
    ImportStreetViewUserCreator::create(id: Req::getString(key: 'id'), name: Req::getString(key: 'name'));
    return $this->index();
  }

  public function table(string $userId): View {
    return Resp::view(view: 'page::curate.street-view-user-table', data: [
      'panoramas' => DB::select(query: <<<SQL
        SELECT
          up.id, 
          up.panorama_id,
          up.captured_date, 
          c.name as country_name,
          (SELECT COUNT(*) FROM panorama p2 WHERE p2.country_cca2 = up.country_cca2) as country_panoramas_count, 
          cs.name as country_subdivision_name,
          (SELECT COUNT(*) FROM panorama p2 WHERE p2.country_subdivision_iso_3166 = up.country_subdivision_iso_3166) as country_subdivision_panoramas_count,
          ROUND(ST_Distance(up.location, p_closest.location)) as distance_to_closest,
          p_closest.id as closest_panorama_id
        FROM import_street_view_user_panorama up
        LEFT JOIN bear_country c ON up.country_cca2 = c.cca2
        LEFT JOIN bear_country_subdivision cs ON cs.iso_3166 = up.country_subdivision_iso_3166
        LEFT JOIN panorama p_closest ON p_closest.id = (SELECT p2.id FROM panorama p2 ORDER BY p2.location <-> up.location LIMIT 1)
        WHERE up.import_street_view_user_id = ? AND up.import_status_enum = 'LOCATION_ADDED'
        ORDER BY up.captured_date DESC, up.id
        LIMIT 10
      SQL, bindings: [$userId]),
      'userId' => $userId,
    ]);
  }


  public function reject(string $userId, string $id): View {
    ImportStreetViewUserPanoramaUpdater::fromId(id: $id)
      ->setImportStatusEnum(import_status_enum: ImportStatusEnum::REJECTED_PANORAMA)
      ->update();
    return $this->table(userId: $userId);

  }
}
