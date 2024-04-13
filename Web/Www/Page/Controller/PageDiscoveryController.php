<?php declare(strict_types=1);

namespace Web\Www\Page\Controller;

use GuardsmanPanda\Larabear\Infrastructure\Http\Service\Resp;
use Illuminate\Routing\Controller;
use Illuminate\View\View;

final class PageDiscoveryController extends Controller {
    public function index(): View {
        return Resp::view(view: 'page::discovery.index');
    }
}
