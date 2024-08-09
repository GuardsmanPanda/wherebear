<?php

declare(strict_types=1); ?>
@php
use GuardsmanPanda\Larabear\Infrastructure\Auth\Service\BearAuthService;
use Infrastructure\View\Enum\ButtonSize;

$isPlayerHost = $game->created_by_user_id === BearAuthService::getUserId();
@endphp

<div class="flex flex-col h-full">
  <div class="flex justify-between gap-2 bg-primary-surface-default border-b-2 border-primary-border-dark p-2">
    <div class="w-16">
      @if(!$isPlayerHost)
      <x-icon icon="chevron-left" color="text-shade-text-subtitle hover:cursor-pointer hover:text-shade-text-title" size="12" hx-delete="/game/{{$game->id}}/lobby/leave" />
      @endif
    </div>

    <div class="flex flex-col w-full justify-between {{ $isPlayerHost ? 'items-center' : 'items-end' }}">
      <span id="game-state-text" class="text-md text-shade-text-title font-medium text-center">Waiting for players...</span>
      <span class="text-md text-shade-text-title">7/10 ready</span>
    </div>

    <div class="w-16"></div>
  </div>

  <div id="lobby" hx-target="#lobby" class="">
    @include('game::lobby.main')
  </div>

  <div id="player-list" class="h-full" hx-get="/game/{{$game->id}}/lobby/player-list" hx-trigger="load" hx-target="this"></div>
</div>

<script>
  pusher = new Pusher('6csm0edgczin2onq92lm', window.pusher_data);
  channel = pusher.subscribe('game.{{$game->id}}');
  channel.bind('player.update', function(data) {
    window.htmx.ajax('GET', '/game/{{$game->id}}/lobby/player-list', '#player-list');
  });
  channel.bind('prep', function(data) {
    document.getElementById('game-state-text').innerText = data.message;
    if (data.stage === -2) { // game reset
    } else {

    }
  });
  channel.bind('round.event', function(data) {
    window.location.href = '/game/{{$game->id}}/play';
  });
  pusher.bind('error', function(error) {
    console.error('Pusher error:', error);
  });
  pusher.bind('disconnected', function(error) {
    console.error('Pusher error:', error);
  });
</script>