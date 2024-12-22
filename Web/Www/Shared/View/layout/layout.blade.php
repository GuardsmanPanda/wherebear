<?php

declare(strict_types=1); 

use GuardsmanPanda\Larabear\Infrastructure\Auth\Service\BearAuthService;
use Illuminate\Support\Facades\App; 
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="description" content="WhereBear">
  <title>{{$title ?? 'WhereBear'}}</title>
  <script src="{!! config('bear.ui.app_js') !!}"></script>
  <script src="{!! config('bear.ui.achievement_toast_js') !!}"></script>
  <script src="{!! config('bear.ui.achievement_toast_service_js') !!}"></script>
  <script src="{!! config('bear.ui.toast_container_js') !!}"></script>
  <script src="{!! config('bear.ui.websocket_service_js') !!}"></script>
  @foreach (config('bear.ui.lit_components') as $litComponentFile)
    <script src="{{ $litComponentFile }}"></script>
  @endforeach
  <link rel="stylesheet" href="{!! config('bear.ui.app_css') !!}">
  <link rel="stylesheet" href="{!! config('bear.ui.tailwind_css') !!}">
  <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Outfit" />
  <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>

<body>
  <div id="primary" x-data="layoutState" hx-target="#primary" class="w-full bg-gray-50 font-body text-shade-text-body">
    {!! $content !!}
  </div>

  <script>
    document.addEventListener('alpine:init', () => {
      Alpine.data('layoutState', () => ({
        isDev: {{ App::isLocal() }},
        user: @json(BearAuthService::getUser()),
        initAchievementToastNotifications() {
          const webSocketClient = WebSocketClient.init();
          const achievementToastContainer = new ToastContainer(
            document.querySelector('.toast-container') || document.querySelector('#primary'),
            {
              anchor: 'bottom-right',
              toastClasses: ['w-full', 'sm:w-[500px]']
            }
          );
          AchievementToastService.init(webSocketClient, this.user.id, achievementToastContainer);
        },
        initPageReloadOnSave() {
          const webSocketClient = WebSocketClient.init();
          const channel = webSocketClient.subscribeToChannel('dev');
          channel.bind('reload', (data) => {
            if (data.hostname === window.location.hostname) {
              location.reload();
            }
          });
        },
        init() {
          this.initAchievementToastNotifications();

          if (this.isDev) {
            this.initPageReloadOnSave();
          }
        }
      }));
    });
  </script>
</body>
</html>