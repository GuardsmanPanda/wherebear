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
  <div  x-data="layoutState" class="flex bg-iris-200">
    {!! $content !!}
  </div>

  <script>
    document.addEventListener('alpine:init', () => {
      Alpine.data('layoutState', () => ({
        isDev: {{ App::isLocal() ? 'true' : 'false' }},
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