<?php declare(strict_types=1); ?>
@php use Domain\Game\Enum\GameStateEnum; @endphp
<div id="play" class="min-h-screen flex flex-col">
  @include($template)
</div>
<script>
  const currentRound = {{ $game->current_round }};
  // The suffix 2 is to avoid a conflict name with the 'webSocketClient' in Web/Www/Shared/View/layout/layout.blade.php
  const webSocketClient2 = WebSocketClient.init();
  const channel = webSocketClient2.subscribeToChannel('game.{{$game->id}}');

  channel.bind('game.round.updated', function (data) {
    if (data.gameStateEnum === '{{GameStateEnum::IN_PROGRESS_CALCULATING->value}}') {
      // Todo: Hide the map to guesses can't be made.
    } else {
      location.reload();
    }
  });
</script>