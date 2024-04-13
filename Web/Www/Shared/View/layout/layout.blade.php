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
    <link rel="stylesheet" href="{!! config('bear.ui.app_css') !!}">
</head>
<body>
<div id="primary" hx-target="#primary" class="min-h-screen bg-gray-950 text-gray-300 flex flex-col">
    {!! $content !!}
</div>
<script src="{!! config('bear.ui.app_js') !!}" defer></script>
</body>
</html>
