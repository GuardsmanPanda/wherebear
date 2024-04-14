<?php declare(strict_types=1);

namespace Domain\Game\Action;

use Domain\Game\Model\Game;

final class GamePanoramaSelectorAction {
    private static array $CL1 = ['AT', 'BE', 'CH', 'DE', 'DK', 'EE', 'ES', 'FI', 'FR', 'GB-ENG', 'GB-SCT', 'GB-WLS', 'GR', 'IE', 'IT', 'LT', 'LV', 'NL', 'NO', 'PL', 'PT', 'SE', 'US'];
    private static array $CL2 = ['AL', 'AU', 'BA', 'BG', 'BY', 'CA', 'CN', 'CZ', 'GB-NIR', 'GE', 'HR', 'JP', 'KR', 'LU', 'NZ', 'RS', 'RU', 'SI', 'SK', 'UA', 'VA', 'XK'];
    private static array $CL3 = [
        'CA', 'US', 'MX', 'CU',
        'AR', 'CL', 'UY', 'BR', 'PY', 'PE', 'BO', 'EC', 'CO', 'VE',
        'AU', 'NZ', 'ID', 'MY', 'BN', 'SG', 'PH',
        'ZA', 'BW', 'KE', 'UG', 'NG', 'GH', 'SN', 'MA', 'TN', 'EG', 'ZM', 'ZW',
        'RU', 'IN', 'MN', 'CN', 'JP', 'KR', 'TW', 'VN', 'LA', 'NP', 'BT', 'KH', 'TH', 'MM', 'BD', 'MO', 'HK',
        'PK', 'KZ', 'IR', 'IQ', 'AE', 'KW', 'BH', 'QA', 'SA', 'JO', 'PS', 'IL', 'LB', 'TR', 'GE', 'AZ', 'AM',
        'GL', 'IS', 'NO', 'SE', 'FI', 'FO', 'AX', 'ET', 'LV', 'EE', 'DK', 'GB-NIR', 'GB-ENG', 'GB-SCT', 'GB-WLS', 'IE', 'PT', 'ES', 'FR', 'LU', 'BE', 'NL', 'AD', 'MC', 'DE', 'CH', 'AT', 'CZ', 'SK', 'PL', 'LI', 'VA', 'BY', 'UA', 'MD', 'HU', 'RO', 'SI', 'HR', 'BA', 'ME', 'RS', 'BG', 'GR', 'AL', 'XK', 'MK',
    ];

    public static function selectPanoramas(Game $game) {

    }
}
