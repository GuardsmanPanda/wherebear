<?php declare(strict_types=1); ?>
@php use GuardsmanPanda\Larabear\Infrastructure\Auth\Service\BearAuthService; @endphp
<div class="flex justify-center h-9 bg-gray-900 items-center shadow z-10">
    <div id="game-state-text" class="text-gray-500 font-medium text-xl">Waiting For Players..</div>
</div>
@if($game->created_by_user_id === BearAuthService::getUserId())
    <div class="flex justify-center h-7 bg-gray-800 items-center">
        <button class="flex items-center ml-4 bg-red-400 text-red-800 hover:text-red-900 text-xs font-medium pl-2 pr-3 py-0.5 my-0.5 rounded duration-75 hover:scale-110 transition-transform"
                hx-delete="/game/{{$game->id}}" hx-confirm="DELETE the game?">
            <x-bear::icon name="x-mark" size="4" class="opacity-70 mr-0.5"/>
            Delete Game
        </button>
        <button class="flex items-center ml-4 bg-blue-400 text-blue-800 hover:text-blue-900 text-xs font-medium pl-2 pr-3 py-0.5 my-0.5 rounded duration-75 hover:scale-110 transition-transform"
                hx-post="/game/{{$game->id}}/start" hx-confirm="Start The Game Now?">
            <x-bear::icon name="play-circle" size="4" class="opacity-70 mr-0.5"/>
            Force Start
        </button>
    </div>
@else
    <div class="h-7"></div>
@endif
<div id="lobby" hx-target="#lobby" class="min-h-screen flex flex-col">
    @include('game::lobby.content')
</div>
<script>
    pusher = new Pusher('6csm0edgczin2onq92lm', {
        cluster: 'eu',
        wsHost: 'ws.gman.bot',
        wsPort: 80,
    });
    channel = pusher.subscribe('game.{{$game->id}}');
    channel.bind('player.update', function (data) {
        if (data.playerId !== '{{$user->id}}') {
            window.htmx.ajax('GET', '/game/{{$game->id}}/lobby', '#lobby');
        }
    });
    channel.bind('prep', function (data) {
        document.getElementById('game-state-text').innerText = data.message;
        if (data.stage === -2) { // game reset
        } else {

        }
    });
</script>
