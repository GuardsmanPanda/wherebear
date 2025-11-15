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
  <script defer src="https://unpkg.com/alpinejs"></script>
</head>

<body>
  <div id="primary" x-data="layoutState" hx-target="#primary" class="w-full bg-gray-50 font-body text-shade-text-body">
    {!! $content !!}
  </div>

  <script>
    document.addEventListener('alpine:init', () => {
      Alpine.data('layoutState', () => ({
        isDev: {{ App::isLocal() ? 'true' : 'false' }},
        userId: @json(BearAuthService::getUserIdOrNull()),
        initAchievementToastNotifications(userId) {
          const webSocketClient = WebSocketClient.init();
          const achievementToastContainer = new ToastContainer(
            document.querySelector('.toast-container') || document.querySelector('#primary'),
            {
              anchor: 'bottom-right',
              toastClasses: ['w-full', 'sm:w-[500px]']
            }
          );
          AchievementToastService.init(webSocketClient, userId, achievementToastContainer);
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
          if (this.userId) {
            this.initAchievementToastNotifications(this.userId);
          }

          if (this.isDev) {
            this.initPageReloadOnSave();
          }
        }
      }));

      /**
       * Custom directive for dynamically updating Tippy tooltips.
       *
       * This directive allows tooltips to automatically update their content 
       * based on reactive Alpine.js state changes.
       *
       * Usage:
       *   <span x-tippy="gameUser.is_observer ? 'Disable spectator mode' : 'Enable spectator mode'">
       *       Hover me
       *   </span>
       */
      Alpine.directive("tippy", (el, { expression }, { evaluateLater, effect }) => {
        let getContent = evaluateLater(expression);
        let instance = tippy(el, { content: "", trigger: "mouseenter focus" });

        effect(() => {
            getContent((value) => {
                if (value.trim() === "") {
                    instance.disable(); // Disable tooltip when content is empty
                } else {
                    instance.setContent(value);
                    instance.enable(); // Enable tooltip when content is valid
                }
            });
        });
      });
    });
  </script>
</body>
</html>
