<?php

declare(strict_types=1); ?>
@php use Illuminate\Support\Facades\App; @endphp
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="description" content="WhereBear">
  <title>{{$title ?? 'WhereBear'}}</title>
  <script src="{!! config('bear.ui.app_js') !!}"></script>
  @foreach (config('bear.ui.lit_components') as $litComponentFile)
    <script src="{{ $litComponentFile }}"></script>
  @endforeach
  @if(App::isLocal())
  @endif
  <link rel="stylesheet" href="{!! config('bear.ui.app_css') !!}">
  <link rel="stylesheet" href="{!! config('bear.ui.tailwind_css') !!}">
  <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Outfit" />
  <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
  <script>
    let targetTime = null;
    let countdownInterval;
    let reloading = false
    countdownStart = function(seconds) {
      clearInterval(countdownInterval);
      targetTime = new Date(new Date().getTime() + seconds * 1000);
      document.getElementById("countdown").setAttribute("style", "display: block;");
      countdownInterval = setInterval(countdownUpdate, 100);
    }

    function countdownUpdate() {
      let value = Math.round((targetTime - new Date()) / 1000);
      document.getElementById("countdown").innerText = '' + Math.max(value, 0);
      if (value <= 0) {
        document.getElementById("countdown").setAttribute("style", "display: none;");
      }
      if (value < -4 && !reloading) {
        reloading = true;
        location.reload();
      }
    }
  </script>
</head>

<body>
  <div id="primary" hx-target="#primary" class="bg-gray-50 font-body text-shade-text-body">
    {!! $content !!}
  </div>
  @if(App::isLocal())
  <script>
    const pHeader = new Pusher('6csm0edgczin2onq92lm', window.pusher_data);
    const pChannel = pHeader.subscribe('dev');
    pChannel.bind('reload', function(data) {
      if (data.hostname === window.location.hostname) {
        location.reload();
      }
    });
  </script>
  @endif
</body>

</html>