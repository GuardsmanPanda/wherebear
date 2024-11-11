<?php

declare(strict_types=1);

namespace Domain\Game\Action;

use Domain\Game\Broadcast\GameBroadcast;
use Domain\Game\Crud\GameRoundCreator;
use Domain\Game\Model\Game;
use GuardsmanPanda\Larabear\Infrastructure\App\Service\BearArrayService;
use GuardsmanPanda\Larabear\Infrastructure\App\Service\BearRandomService;
use GuardsmanPanda\Larabear\Infrastructure\Error\Crud\BearErrorCreator;
use Illuminate\Support\Facades\DB;
use Ramsey\Collection\Set;
use stdClass;

final class GameRoundCreatorAction {
  const string SAFETY_PANORAMA_ID = 'CAoSLEFGMVFpcE02ZEZUck9MZ0x5OS1XaElySWpTN1U5QlVabUJ5Z3J6RFV4S0R1';
  const int DELAY_PER_ROUND_MS = 1000;

  const int TIER1_COUNTRY_CHANCE = 10;
  const int TIER2_COUNTRY_CHANCE = 10;
  const int FILLER_COUNTRY_CHANCE = 30;
  const int RANDOM_COUNTRY_CHANCE = 30;

  const int CONTRIBUTOR_PRIORITY_CHANCE = 20;
  const int USER_UPLOAD_PRIORITY_CHANCE = 50;

  /** @var array<string> $CL1 */
  private array $CL1 = ['AT', 'BE', 'CH', 'DE', 'DK', 'EE', 'ES', 'FI', 'FR', 'GB', 'GR', 'IE', 'IT', 'LT', 'LV', 'NL', 'NO', 'PL', 'PT', 'SE', 'US'];
  /** @var array<string> $CL2 */
  private array $CL2 = ['AL', 'AU', 'BA', 'BG', 'BY', 'CA', 'CN', 'CZ', 'GE', 'HR', 'JP', 'KR', 'LU', 'NZ', 'RS', 'RU', 'SI', 'SK', 'UA', 'VA', 'XK'];
  /** @var array<string> $filler */
  private array $filler = [
    // North America
    'CA',
    'US',
    'MX',
    'CU',
    // South America
    'AR',
    'CL',
    'UY',
    'BR',
    'PY',
    'PE',
    'BO',
    'EC',
    'CO',
    'VE',
    // Oceania
    'AU',
    'NZ',
    'ID',
    'MY',
    'BN',
    'SG',
    'PH',
    // Africa
    'ZA',
    'BW',
    'KE',
    'UG',
    'NG',
    'GH',
    'SN',
    'MA',
    'TN',
    'EG',
    'ZM',
    'ZW',
    // East Asia & Southeast Asia
    'RU',
    'IN',
    'MN',
    'CN',
    'JP',
    'KR',
    'TW',
    'VN',
    'LA',
    'NP',
    'BT',
    'KH',
    'TH',
    'MM',
    'BD',
    'MO',
    'HK',
    // Middle East & Central Asia
    'PK',
    'KZ',
    'IR',
    'IQ',
    'AE',
    'KW',
    'BH',
    'QA',
    'SA',
    'JO',
    'PS',
    'IL',
    'LB',
    'TR',
    'GE',
    'AZ',
    'AM',
    // Europe
    'GL',
    'IS',
    'NO',
    'SE',
    'FI',
    'FO',
    'AX',
    'ET',
    'LV',
    'EE',
    'DK',
    'GB',
    'IE',
    'PT',
    'ES',
    'FR',
    'LU',
    'BE',
    'NL',
    'AD',
    'MC',
    'DE',
    'CH',
    'AT',
    'CZ',
    'SK',
    'PL',
    'LI',
    'VA',
    'BY',
    'UA',
    'MD',
    'HU',
    'RO',
    'SI',
    'HR',
    'BA',
    'ME',
    'RS',
    'BG',
    'GR',
    'AL',
    'XK',
    'MK',
  ];

  /** @var array<string> $countries_used */
  private array $countries_used = ['XX'];
  /** @var array<string> $all_countries */
  private array $all_countries;
  /** @var array<int, stdClass> $panoramas */
  private array $panoramas;
  /** @var array<string, array<int, mixed>> $panorama_country */
  private array $panorama_country;
  private int $panoramaIndex = 0;
  private int $panoramaContributorIndex = 0;
  private int $panoramaUserUploadIndex = 0;


  public function __construct(private readonly Game $game) {
    $this->panoramas = DB::select(query: "
      SELECT 
        p.id,
        p.country_cca2,
        p.added_by_user_id IS NOT NULL as is_contributor,
        p.id LIKE 'CAoS%' as is_user_upload
      FROM panorama p
      LEFT JOIN (
        SELECT p2.id
        FROM panorama p2
        LEFT JOIN game_round gr2 ON p2.id = gr2.panorama_id AND gr2.created_at > NOW() - INTERVAL '6 MONTH'
        LEFT JOIN game_user gu2 ON gr2.game_id = gu2.game_id
        LEFT JOIN game_user gu3 ON gu2.user_id = gu3.user_id AND gu3.game_id = :game_id
        WHERE gr2.game_id IS NOT NULL AND gu3.user_id IS NOT NULL
        GROUP BY p2.id
      ) as used_panoramas ON p.id = used_panoramas.id
      LEFT JOIN (
        SELECT p3.id
        FROM panorama p2
        LEFT JOIN game_round gr2 ON p2.id = gr2.panorama_id AND gr2.created_at > NOW() - INTERVAL '22 DAY'
        LEFT JOIN game_user gu2 ON gr2.game_id = gu2.game_id
        LEFT JOIN game_user gu3 ON gu2.user_id = gu3.user_id AND gu3.game_id = :game_id
        LEFT JOIN panorama p3 ON st_distance(p2.location, p3.location) < 5000
        WHERE gr2.game_id IS NOT NULL AND gu3.user_id IS NOT NULL
        GROUP BY p3.id
      ) as used_panorama_area ON p.id = used_panorama_area.id
      WHERE 
        p.retired_at IS NULL
        AND p.jpg_path IS NOT NULL
        AND used_panoramas.id IS NULL
        AND used_panorama_area.id IS NULL
        AND NOT p.panorama_tag_array @> ARRAY['HIDDEN']
    ", bindings: ['game_id' => $this->game->id]);

    $countrySet = new Set(setType: 'string');
    foreach ($this->panoramas as $panorama) {
      $countrySet->add($panorama->country_cca2);
    }
    $this->all_countries = $countrySet->toArray();

    shuffle(array: $this->all_countries);
    shuffle(array: $this->CL1);
    shuffle(array: $this->CL2);
    shuffle(array: $this->filler);
    shuffle(array: $this->panoramas);
    $this->panorama_country = BearArrayService::groupArrayBy(array: $this->panoramas, key: 'country_cca2');
  }


  public function createAllRounds(): void {
    $rounds = $this->game->number_of_rounds;

    for ($i = 1; $i <= $rounds; $i++) {
      $contributorPriority = BearRandomService::percentChance(chance: self::CONTRIBUTOR_PRIORITY_CHANCE);
      $userUploadPriority = BearRandomService::percentChance(chance: self::USER_UPLOAD_PRIORITY_CHANCE);
      $strategy = 'UNSPECIFIED';
      $id = null;

      if (BearRandomService::percentChance(chance: self::TIER1_COUNTRY_CHANCE)) {
        $id = $this->selectFromCountryArray(countryArray: $this->CL1, contributorPriority: $contributorPriority, userUploadPriority: $userUploadPriority);
        if ($id !== null) {
          $strategy = 'Tier 1 Country';
        }
      }
      if ($id === null && BearRandomService::percentChance(chance: self::TIER2_COUNTRY_CHANCE)) {
        $id = $this->selectFromCountryArray(countryArray: $this->CL2, contributorPriority: $contributorPriority, userUploadPriority: $userUploadPriority);
        if ($id !== null) {
          $strategy = 'Tier 2 Country';
        }
      }
      if ($id === null && BearRandomService::percentChance(chance: self::FILLER_COUNTRY_CHANCE)) {
        $id = $this->selectFromCountryArray(countryArray: $this->filler, contributorPriority: $contributorPriority, userUploadPriority: $userUploadPriority);
        if ($id !== null) {
          $strategy = 'Filler Country';
        }
      }
      if ($id === null && BearRandomService::percentChance(chance: self::RANDOM_COUNTRY_CHANCE)) {
        $id = $this->selectFromCountryArray(countryArray: $this->all_countries, contributorPriority: $contributorPriority, userUploadPriority: $userUploadPriority);
        if ($id !== null) {
          $strategy = 'Random Country';
        }
      }

      while ($id === null && $contributorPriority &&  $this->panoramaContributorIndex < count($this->panoramas)) {
        $candidate = $this->panoramas[$this->panoramaContributorIndex];
        if (!in_array(needle: $candidate->country_cca2, haystack: $this->countries_used, strict: true) && $candidate->is_contributor) {
          $this->countries_used[] = $candidate->country_cca2;
          $strategy = 'Random Contributor';
          $id = $candidate->id;
        }
        $this->panoramaContributorIndex++;
      }
      while ($id === null && $userUploadPriority &&  $this->panoramaUserUploadIndex < count($this->panoramas)) {
        $candidate = $this->panoramas[$this->panoramaUserUploadIndex];
        if (!in_array(needle: $candidate->country_cca2, haystack: $this->countries_used, strict: true) && $candidate->is_user_upload) {
          $this->countries_used[] = $candidate->country_cca2;
          $strategy = 'Random User Upload';
          $id = $candidate->id;
        }
        $this->panoramaUserUploadIndex++;
      }
      while ($id === null && $this->panoramaIndex < count($this->panoramas)) {
        $candidate = $this->panoramas[$this->panoramaIndex];
        if (!in_array(needle: $candidate->country_cca2, haystack: $this->countries_used, strict: true)) {
          $this->countries_used[] = $candidate->country_cca2;
          $strategy = 'Random Panorama';
          $id = $candidate->id;
        }
        $this->panoramaIndex++;
      }

      if ($id === null) { // This should never happen.
        BearErrorCreator::create(message: 'No panorama found for round, game_id: ' . $this->game->id);
        $id = self::SAFETY_PANORAMA_ID;
        $strategy = 'Safety Panorama';
      }

      GameRoundCreator::createWithTransaction(
        game_id: $this->game->id,
        round_number: $i,
        panorama_pick_strategy: $strategy,
        panorama_id: $id
      );

      GameBroadcast::gameStageUpdate(gameId: $this->game->id, message: "Round $i of $rounds selected", stage: 2 + $i);
      usleep(microseconds: self::DELAY_PER_ROUND_MS * 1000);
    }
  }


  /**
   * @param array<string> $countryArray
   */
  private function selectFromCountryArray(array &$countryArray, bool $contributorPriority, bool $userUploadPriority): string|null {
    while (count($countryArray) > 0) {
      $country = array_shift(array: $countryArray);
      if (in_array(needle: $country, haystack: $this->countries_used, strict: true) || !array_key_exists(key: $country, array: $this->panorama_country)) {
        continue;
      }
      $this->countries_used[] = $country;
      $panoramas = $this->panorama_country[$country];
      if ($contributorPriority) {
        foreach ($panoramas as $panorama) {
          if ($panorama->is_contributor) {
            return $panorama->id;
          }
        }
      }
      if ($userUploadPriority) {
        foreach ($panoramas as $panorama) {
          if ($panorama->is_user_upload) {
            return $panorama->id;
          }
        }
      }
      return $panoramas[0]->id;
    }
    return null;
  }
}
