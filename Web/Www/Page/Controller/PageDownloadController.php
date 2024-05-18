<?php declare(strict_types=1);

namespace Web\Www\Page\Controller;

use GuardsmanPanda\Larabear\Infrastructure\Http\Service\Resp;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

final class PageDownloadController extends Controller {
    public function index(): View {
        return Resp::view(view: 'page::download.index', data: [
            'panoramas' => DB::select(query: "
                SELECT 
                    p.id
                FROM panorama p
                WHERE p.jpg_name IS NULL
                ORDER BY p.added_by_user_id DESC
            "),
        ]);
    }
}
