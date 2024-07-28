<?php declare(strict_types=1); ?>
@php use Illuminate\Support\Facades\App; @endphp
        <!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="WhereBear">
    <title>{{$title ?? 'WhereBear'}}</title>
    <script src="{!! config('bear.ui.app_js') !!}"></script>
    @if(App::isLocal())
        <script src="https://cdn.tailwindcss.com?plugins=forms"></script>
    @endif
    <link rel="stylesheet" href="{!! config('bear.ui.app_css') !!}">
    @yield('styles')
</head>
<body class="min-h-screen grid place-items-center bg-gray-900 text-gray-300">
@yield('content')
@if(App::isLocal())
    <script>
        const pHeader = new Pusher('6csm0edgczin2onq92lm', window.pusher_data);
        const pChannel = pHeader.subscribe('dev');
        pChannel.bind('reload', function (data) {
            if (data.hostname === window.location.hostname) {
                location.reload();
            }
        });
    </script>
@endif
</body>
</html>