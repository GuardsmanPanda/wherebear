<?php declare(strict_types=1);

namespace Domain\Game\Action;

use Domain\Game\Broadcast\GameBroadcast;
use Domain\Game\Crud\GameRoundCreator;
use Domain\Game\Model\Game;
use GuardsmanPanda\Larabear\Infrastructure\Error\Crud\BearErrorCreator;
use Illuminate\Support\Facades\DB;

final class GameRoundCreatorAction {
    const string SAFETY_PANORAMA_ID = 'CAoSLEFGMVFpcE9pZ3JUek55Um80bkhsR2VoWHZkX2tTaDFlNTBLYmpBVVRNTUFx';
    const float MIN_DELAY_PER_ROUND_MS = 1000;
    const int TIER1_COUNTRY_CHANCE = 10;
    const int TIER2_COUNTRY_CHANCE = 10;
    CONST int FILLER_COUNTRY_CHANCE = 40;
    CONST int RANDOM_COUNTRY_CHANCE = 30;

    private array $CL1 = ['AT', 'BE', 'CH', 'DE', 'DK', 'EE', 'ES', 'FI', 'FR', 'GB-ENG', 'GB-SCT', 'GB-WLS', 'GR', 'IE', 'IT', 'LT', 'LV', 'NL', 'NO', 'PL', 'PT', 'SE', 'US'];
    private array $CL2 = ['AL', 'AU', 'BA', 'BG', 'BY', 'CA', 'CN', 'CZ', 'GB-NIR', 'GE', 'HR', 'JP', 'KR', 'LU', 'NZ', 'RS', 'RU', 'SI', 'SK', 'UA', 'VA', 'XK'];
    private array $filler = [
        'CA', 'US', 'MX', 'CU',
        'AR', 'CL', 'UY', 'BR', 'PY', 'PE', 'BO', 'EC', 'CO', 'VE',
        'AU', 'NZ', 'ID', 'MY', 'BN', 'SG', 'PH',
        'ZA', 'BW', 'KE', 'UG', 'NG', 'GH', 'SN', 'MA', 'TN', 'EG', 'ZM', 'ZW',
        'RU', 'IN', 'MN', 'CN', 'JP', 'KR', 'TW', 'VN', 'LA', 'NP', 'BT', 'KH', 'TH', 'MM', 'BD', 'MO', 'HK',
        'PK', 'KZ', 'IR', 'IQ', 'AE', 'KW', 'BH', 'QA', 'SA', 'JO', 'PS', 'IL', 'LB', 'TR', 'GE', 'AZ', 'AM',
        'GL', 'IS', 'NO', 'SE', 'FI', 'FO', 'AX', 'ET', 'LV', 'EE', 'DK', 'GB-NIR', 'GB-ENG', 'GB-SCT', 'GB-WLS', 'IE', 'PT', 'ES', 'FR', 'LU', 'BE', 'NL', 'AD', 'MC', 'DE', 'CH', 'AT', 'CZ', 'SK', 'PL', 'LI', 'VA', 'BY', 'UA', 'MD', 'HU', 'RO', 'SI', 'HR', 'BA', 'ME', 'RS', 'BG', 'GR', 'AL', 'XK', 'MK',
    ];

    private array $countries_used = ['XX'];
    private array $all_countries = [];


    public function __construct(private readonly Game $game) {
        $countries = DB::select(query: "
            SELECT DISTINCT country_iso_2_code
            FROM panorama
            WHERE retired_at IS NULL
        ");
        $this->all_countries = array_map(fn($c) => $c->country_iso_2_code, $countries);

        $recent_countries = DB::select(query: "
            SELECT DISTINCT latest_countries.country_iso_2_code
            FROM (
                SELECT p.country_iso_2_code
                FROM game_round gr
                LEFT JOIN panorama p ON gr.panorama_id = p.id
                ORDER BY gr.created_at DESC
                LIMIT 100
            ) as latest_countries
        ");
        $this->filler = array_filter($this->filler, static function ($ele) use ($recent_countries) {return !in_array($ele, $recent_countries, true);});

        shuffle($this->all_countries);
        shuffle($this->CL1);
        shuffle($this->CL2);
        shuffle($this->filler);
    }


    public function createAllRounds(): void {
        $rounds = $this->game->number_of_rounds;
        for ($i = 1; $i <= $rounds; $i++) {
            $startTimestamp = microtime(as_float: true);
            $strategy = 'UNSPECIFIED';
            $id = null;

            if (random_int(0, 99) < self::TIER1_COUNTRY_CHANCE) {
                $country = $this->selectCountry($this->CL1);
                if ($country !== null) {
                    $id = $this->selectPanoramaId(countryCode: $country);
                    $strategy = 'Tier 1 Country';
                }
            }
            if ($id === null && random_int(0, 99) < self::TIER2_COUNTRY_CHANCE) {
                $country = $this->selectCountry($this->CL2);
                if ($country !== null) {
                    $id = $this->selectPanoramaId(countryCode: $country);
                    $strategy = 'Tier 2 Country';
                }
            }
            if ($id === null && random_int(0, 99) < self::FILLER_COUNTRY_CHANCE) {
                $country = $this->selectCountry($this->filler);
                if ($country !== null) {
                    $id = $this->selectPanoramaId(countryCode: $country);
                    $strategy = 'Filler Country';
                }
            }
            if ($id === null && random_int(0, 99) < self::RANDOM_COUNTRY_CHANCE) {
                $country = $this->selectCountry($this->all_countries);
                if ($country !== null) {
                    $id = $this->selectPanoramaId(countryCode: $country);
                    $strategy = 'Random Country';
                }
            }

            if ($id === null) {
                $id = $this->selectPanoramaId();
                $strategy = 'Random Panorama';
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

            $endTimestamp = microtime(as_float: true);
            $delay = self::MIN_DELAY_PER_ROUND_MS - ($endTimestamp - $startTimestamp) * 1000;
            if ($delay > 0) {
                usleep(microseconds: (int)($delay * 1000));
            }
            GameBroadcast::prep(gameId: $this->game->id, message: 'Round ' . $i . ' of ' . $rounds . ' selected', stage: 2 + $i);
        }
    }


    private function selectPanoramaId(string $countryCode = null): string|null {
        $bindings = [$this->game->id, $this->game->id];
        $where = '';
        if ($countryCode !== null) {
            $where = 'AND p.country_iso_2_code = ?';
            $bindings[] = $countryCode;
        }
        $result = DB::selectOne(query: "
            SELECT p.id, p.country_iso_2_code
            FROM panorama p
            LEFT JOIN (
                SELECT p2.country_iso_2_code
                FROM game_round gr2
                LEFT JOIN panorama p2 ON gr2.panorama_id = p2.id
                WHERE gr2.game_id = ?
            ) as used_countries ON p.country_iso_2_code = used_countries.country_iso_2_code
            LEFT JOIN (
                SELECT gr2.panorama_id
                FROM game_user g2
                LEFT JOIN game_round_user gru2 ON g2.user_id = gru2.user_id
                LEFT JOIN game_round gr2 ON gru2.game_id = gr2.game_id
                WHERE g2.game_id = ? AND gr2.created_at > NOW() - INTERVAL '6 MONTH'
            ) as used_panoramas ON p.id = used_panoramas.panorama_id
            WHERE
                p.retired_at IS NULL
                AND used_countries.country_iso_2_code IS NULL -- Country not seen in this game
                AND used_panoramas.panorama_id IS NULL        -- Panorama not seen by any user in this game
                AND p.jpg_path IS NOT NULL                    -- Panorama has a jpg
                $where
            ORDER BY RANDOM() -- Slow, problematic with a high number of panoramas
            LIMIT 1
        ", bindings: $bindings);
        if ($result !== null) {
            $this->countries_used[] = $result->country_iso_2_code;
        }
        return $result?->id;
    }


    private function selectCountry(array &$countryArray): string|null {
        $country = null;
        while ($country === null && count($countryArray) > 0) {
            $country = array_shift($countryArray);
            if (in_array($country, $this->countries_used)) {
                $country = null;
            }
        }
        return $country;
    }
}
