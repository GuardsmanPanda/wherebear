<?php declare(strict_types=1);

namespace Infrastructure\Database\Initialize;

use Domain\Map\Crud\MapStyleCreator;
use Domain\Map\Service\MapStyleService;

final class DatabaseInitializeMapStyle {

    public static function initialize(): void {
        $map_styles = [
            [
                'map_style_enum' => 'OSM',
                'map_style_name' => 'OpenStreetMap',
                'map_style_url' => '{z}/{x}/{y}.png',
                'external_api_id' => 'e9f8e665-ca90-4f3d-b7f4-d9a811eb4754'
            ],
        ];

        foreach ($map_styles as $style) {
            if (MapStyleService::mapStyleExists(mapStyleEnum: $style['map_style_enum'])) {
                continue;
            }

            MapStyleCreator::create(
                map_style_enum: $style['map_style_enum'],
                map_style_name: $style['map_style_name'],
                map_style_url: $style['map_style_url'],
                external_api_id: $style['external_api_id']
            );
        }
    }
}
