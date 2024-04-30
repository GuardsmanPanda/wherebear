<?php declare(strict_types=1);

use Domain\Map\Crud\MapStyleCreator;
use GuardsmanPanda\Larabear\Integration\ExternalApi\Crud\BearExternalApiCreator;
use GuardsmanPanda\Larabear\Integration\ExternalApi\Enum\BearExternalApiTypeEnum;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::dropIfExists(table: 'map_style');
        Schema::create(table: 'map_style', callback: static function (Blueprint $table): void {
            $table->text(column: 'map_style_enum')->primary();
            $table->text(column: 'map_style_name');
            $table->text(column: 'map_style_url');
            $table->uuid(column: 'external_api_id')->unique();
            $table->timestampTz(column: 'created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->foreign(columns: 'external_api_id')->references('id')->on('bear_external_api');
        });
        $external = BearExternalApiCreator::create(
            external_api_slug: 'openstreetmap',
            external_api_description: 'OpenStreetMap used for the default map tiles',
            external_api_type: BearExternalApiTypeEnum::NO_AUTH,
            id: 'e9f8e665-ca90-4f3d-b7f4-d9a811eb4754',
            external_api_base_url: 'https://c.tile.openstreetmap.org/'
        );
        MapStyleCreator::create(
            map_style_enum: 'OSM',
            map_style_name: 'OpenStreetMap',
            map_style_url: '{z}/{x}/{y}.png',
            external_api_id: $external->id
        );
    }

    public function down(): void {
        Schema::dropIfExists(table: 'map_style');
    }
};
