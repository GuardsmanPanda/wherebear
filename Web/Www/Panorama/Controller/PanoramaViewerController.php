<?php declare(strict_types=1);

namespace Web\Www\Panorama\Controller;

use GuardsmanPanda\Larabear\Infrastructure\Http\Service\Resp;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\RedirectResponse;

final class PanoramaViewerController extends Controller {
  public function view(string $panoramaId): View|RedirectResponse {
    $data = DB::selectOne(query: "
      SELECT 
        heading, pitch, field_of_view,
        ST_Y(location::geometry) as lat, ST_X(location::geometry) as lng,
        jpg_path,
        retired_at
      FROM panorama
      WHERE id = ?
    ", bindings: [$panoramaId]);
    if ($data === null || $data->jpg_path === null) {
      $userData = DB::selectOne(query: "
        SELECT 
          id, ST_Y(location::geometry) as lat, ST_X(location::geometry) as lng
        FROM import_street_view_user_panorama
        WHERE panorama_id = ?
      ", bindings: [$panoramaId]);
      if ($userData !== null) {
        return Resp::redirect(url: "https://www.google.com/maps/@$userData->lat,$userData->lng,0a,73.7y,90t/data=!3m4!1e1!3m2!1s$userData->id!2e10");
      }
    }

    $sv_url = null;
    if (str_starts_with(haystack: $panoramaId, needle: 'CAoSL')) {
      $id = ""; // Should probably calculate to proper image id here, but in most cases this will work as is.
      $sv_url = "https://www.google.com/maps/@$data->lat,$data->lng,0a,73.7y,90t/data=!3m4!1e1!3m2!1s$id!2e10";
    }

    return Resp::view(view: 'panorama::viewer', data: [
      'panorama_id' => $panoramaId,
      'panorama_url' => App::isProduction() ? "https://panorama.wherebear.fun/$data->jpg_path" : "https://panorama.gman.bot/$data->jpg_path",
      'heading' => $data->heading,
      'pitch' => $data->pitch,
      'field_of_view' => $data->field_of_view,
      'retired' => $data->retired_at !== null,
      'sv_url' => $sv_url,
    ]);
  }
}
