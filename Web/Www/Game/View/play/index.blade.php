<?php declare(strict_types=1); ?>
@php use Domain\Game\Enum\GameStateEnum; @endphp
<div id="play" class="min-h-screen flex flex-col">
    @include($template)
</div>
<script>
    const currentRound = {{ $game->current_round }};
    pusher = new Pusher('6csm0edgczin2onq92lm', {
        cluster: 'eu',
        wsHost: 'socket.gman.bot',
        wsPort: 443,
    });
    channel = pusher.subscribe('game.{{$game->id}}');
    channel.bind('round.event', function (data) {
        if (data.GameStateEnum === '{{GameStateEnum::IN_PROGRESS_CALCULATING->value}}') {
            // Todo: Hide the map to guesses can't be made.
        } else {
            location.reload();
        }
    });
</script>