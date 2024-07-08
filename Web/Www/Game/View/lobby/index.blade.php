<?php declare(strict_types=1); ?>
@php use GuardsmanPanda\Larabear\Infrastructure\Auth\Service\BearAuthService; @endphp
<div class="md:flex min-h-screen">
    <div class="flex-grow">
        <div class="flex justify-center h-9 bg-gray-900 items-center shadow z-10">
            <div id="game-state-text" class="text-gray-500 font-medium text-xl">Waiting For Players..</div>
        </div>
        <div id="game-loading-progress" class="h-7"></div>
        <div id="lobby" hx-target="#lobby" class="min-h-screen flex flex-col">
            @include('game::lobby.content')
        </div>
    </div>
    <div id="player-list" class="w-96 md:w-72 bg-gray-900" hx-get="/game/{{$game->id}}/lobby/player-list"
         hx-trigger="load" hx-target="this"></div>
</div>
<script>
    pusher = new Pusher('6csm0edgczin2onq92lm', {
        cluster: 'eu',
        wsHost: 'socket.gman.bot',
        wssPort: 443,
    });
    channel = pusher.subscribe('game.{{$game->id}}');
    channel.bind('player.update', function (data) {
        window.htmx.ajax('GET', '/game/{{$game->id}}/lobby/player-list', '#player-list');
    });
    channel.bind('prep', function (data) {
        document.getElementById('game-state-text').innerText = data.message;
        if (data.stage === -2) { // game reset
        } else {

        }
    });
    channel.bind('round.event', function (data) {
        window.location.href = '/game/{{$game->id}}/play';
    });
    pusher.bind('error', function (error) {
        console.error('Pusher error:', error);
    });
    pusher.bind('disconnected', function (error) {
        console.error('Pusher error:', error);
    });
</script>
