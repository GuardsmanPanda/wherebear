<?php declare(strict_types=1);

namespace Domain\Map\Enum;

enum MapStyleEnum: string {
    case OSM = 'OSM';
    //case STREETS = 'STREETS';
    //case SATELLITE = 'SATELLITE';
    //case LIGHT = 'LIGHT';
    //case DARK = 'DARK';
    //case OUTDOORS = 'OUTDOORS';

    public function mapTileUrl(int $z = null, int $x = null, int $y = null): String {
        if ($z === null || $x === null || $y === null) {
            return "https://tile.gman.bot/$this->value/{z}/{x}/{y}.png";
        }
        return "https://tile.gman.bot/$this->value/$z/$x/$y.png";
    }

    public function getMapStyleName(): String {
        return match ($this) {
            self::OSM => 'OpenStreetMap',
            //self::STREETS => 'Streets',
            //self::SATELLITE => 'Satellite',
            //self::LIGHT => 'Light',
            //self::DARK => 'Dark',
            //self::OUTDOORS => 'Outdoors',
        };
    }
}
