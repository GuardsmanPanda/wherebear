<?php declare(strict_types=1); ?>
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
</script>
