<?php

declare(strict_types=1);

namespace Web\Www\Playground\Controller;

use GuardsmanPanda\Larabear\Infrastructure\Http\Service\Resp;
use Illuminate\Routing\Controller;
use Illuminate\View\View;

final class PlaygroundController extends Controller {
  public function buttons(): View {
    return Resp::view(view: 'playground::buttons');
  }
}
