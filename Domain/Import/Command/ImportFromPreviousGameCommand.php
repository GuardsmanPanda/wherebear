<?php declare(strict_types=1);

namespace Domain\Import\Command;

use Carbon\CarbonImmutable;
use Domain\Panorama\Crud\PanoramaCreator;
use Domain\User\Crud\WhereBearUserCreator;
use Domain\User\Enum\UserLevelEnum;
use GuardsmanPanda\Larabear\Infrastructure\Console\Service\BearTransactionCommand;
use GuardsmanPanda\Larabear\Infrastructure\Locale\Enum\BearCountryEnum;
use Illuminate\Support\Facades\DB;
use Integration\StreetView\Client\StreetViewClient;

final class ImportFromPreviousGameCommand extends BearTransactionCommand {
  protected $signature = 'import:from-previous-game';
  protected $description = 'Import the old Panorama database from the previous game';

  protected function handleInTransaction(): void {
    $this->info(string: 'Importing old Panorama database...');
    $panoramas = DB::connection(name: 'previous')->select(query: "
      SELECT p.panorama_id, AVG(pr.rating) as average_rating, u.email, u.display_name, u.country_code
      FROM panorama p
      LEFT JOIN panorama_rating pr ON pr.panorama_id = p.panorama_id
      LEFT JOIN users u on u.id = p.added_by_user_id
      GROUP BY p.panorama_id, u.email, u.display_name, u.country_code
      HAVING (p.added_by_user_id IS NOT NULL OR AVG(pr.rating) > 4) 
      ORDER BY random() DESC NULLS LAST
    ");
    $this->info(string: 'Found ' . count($panoramas) . ' panoramas');
    $total = 0;
    foreach ($panoramas as $panorama) {
      $exists = DB::selectOne(query: "SELECT id FROM panorama WHERE id = ?", bindings: [$panorama->panorama_id]);
      if ($exists !== null) {
        continue;
      }
      $id_substring = substr(string: $panorama->panorama_id, offset: 0, length: 10);
      $this->info(string: "Importing panorama with ID $id_substring, average rating: $panorama->average_rating");
      $data = StreetViewClient::fromPanoramaId(panoramaId: $panorama->panorama_id);
      if ($data === null) {
        $this->error(string: " *Failed to fetch panorama data");
      } else {
        $this->info(string: " Exists: $panorama->panorama_id, User: $panorama->email, Rating: $panorama->average_rating, Country: $panorama->country_code");
        $userId = DB::selectOne(query: "SELECT id FROM bear_user WHERE email = ?", bindings: [$panorama->email])?->id;
        if ($userId === null && $panorama->email !== null) {
          $this->error(string: " *Failed to find user with email $panorama->email");
          $user = WhereBearUserCreator::create(
            display_name: $panorama->display_name,
            experience: 1,
            user_level_enum: UserLevelEnum::L1,
            email: $panorama->email,
            country_cca2: BearCountryEnum::from(value: $panorama->country_code),
          );
          $userId = $user->id;
        }
        PanoramaCreator::createFromStreetViewData(
          data: $data,
          added_by_user_id: $userId,
          created_at: CarbonImmutable::createFromDate(year: 2021, month: 1, day: 1),
        );
        $total++;
        if ($total > 500) {
          break;
        }
      }
    }
    $this->info(string: "Imported $total panoramas");
  }
}
