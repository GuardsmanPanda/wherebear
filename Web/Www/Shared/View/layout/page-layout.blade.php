<?php declare(strict_types=1); ?>
@php use Illuminate\Support\Facades\App; @endphp
        <!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="WhereBear">
    <title>{{$title ?? 'WhereBear'}}</title>
    @if(App::isLocal())
        <script src="https://cdn.tailwindcss.com?plugins=forms"></script>
    @endif
    <script src="/static/leaflet/leaflet.js"></script>
    <link rel="stylesheet" href="{!! config('bear.ui.app_css') !!}">
    <link rel="stylesheet" href="/static/leaflet/leaflet.css" />
    <link rel="stylesheet" href="/static/dist/app.css" />
</head>
<body class="min-h-screen bg-gray-950 text-gray-300" hx-target="#primary">
<div style="display: grid; grid-template-columns: 16rem auto;" class="min-h-screen">
    <div class="flex md:w-64 flex-col shadow bg-gray-800">
        <div class="flex flex-col flex-grow pt-2 overflow-y-auto">
            <div class="flex-grow flex flex-col">
                <nav class="flex-1 px-2 pb-4">
                    <x-bear::sidebar.link path="/" icon="home">Pages</x-bear::sidebar.link>
                    <x-bear::sidebar.divider color="gray-800">Contribute</x-bear::sidebar.divider>
                    <x-bear::sidebar.link path="/page/discovery" icon="key">Add Panorama</x-bear::sidebar.link>
                </nav>
            </div>
        </div>
    </div>
    <div id="primary" class="max-w-full min-w-full px-2 md:px-4 pt-2">{!! $content !!}</div>
</div>

<script src="{!! config('bear.ui.app_js') !!}" defer></script>
</body>
</html>
