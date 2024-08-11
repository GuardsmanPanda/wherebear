<?php declare(strict_types=1);

namespace Web\Www\App\Controller;

use GuardsmanPanda\Larabear\Infrastructure\App\Service\BearBroadcastService;
use GuardsmanPanda\Larabear\Infrastructure\Http\Service\Req;
use GuardsmanPanda\Larabear\Infrastructure\Http\Service\Resp;
use Illuminate\Routing\Controller;
use Symfony\Component\HttpFoundation\Response;

final class AppSystemController extends Controller {
  public function reload(): Response {
    BearBroadcastService::broadcastNow(
      channel: 'dev',
      event: 'reload',
      data: ['hostname' => Req::hostname()]
    );
    return Resp::noContent();
  }
}
