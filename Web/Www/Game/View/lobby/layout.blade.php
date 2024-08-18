<?php

declare(strict_types=1); ?>
@php
use GuardsmanPanda\Larabear\Infrastructure\Auth\Service\BearAuthService;

$isPlayerHost = $game->created_by_user_id === BearAuthService::getUserId();
@endphp

<div id="layout" class="flex flex-col h-screen overflow-hidden">
  <div id="game-status" class="flex justify-between gap-2 bg-primary-surface-default border-b-2 border-primary-border-dark p-2">
    <div class="w-16">
      @if(!$isPlayerHost)
      <x-icon icon="chevron-left" color="text-shade-text-subtitle hover:cursor-pointer hover:text-shade-text-title" size="12"
        hx-delete="/game/{{$game->id}}/lobby/leave" />
      @endif
    </div>

    <div class="flex flex-col w-full justify-between {{ $isPlayerHost ? 'items-center' : 'items-end' }}">
      <span id="game-state-text" class="text-md text-shade-text-title font-medium text-center">Waiting for players...</span>
      <span class="text-md text-shade-text-title">7/10 ready</span>
    </div>

    <div class="w-16"></div>
  </div>

  <div id="lobby" hx-target="#lobby" class="overflow-y-auto">
    @include('game::lobby.main')
  </div>

  <div id="playersSubstitute" class="flex-1 min-h-[136px] hidden"></div>
  <div id="players" data-state="collapsed" class="flex flex-col min-h-[136px] flex-1 px-2 bg-primary-surface-default border border-b-0 border-primary-border-dark rounded-t-2xl transition-[height] duration-700 ease-in-out">
    <div class="flex justify-between items-center py-2 border-b border-primary-border-dark cursor-pointer" onclick="togglePlayerListSize()">
      <div></div>
      <div class="font-heading text-base text-shade-text-title font-medium uppercase">Players</div>

      <x-icon id="players-expand-icon" class="transition-transform duration-1000 ease-in-out" icon="chevron-up" :isButton=true color="text-shade-text-title" />
    </div>

    <div id="player-list" class="py-2" hx-get="/game/{{$game->id}}/lobby/player-list" hx-trigger="load" hx-target="this"></div>
  </div>
</div>

<script>
  const layoutEl = document.getElementById('layout');
  const gameStatusEl = document.getElementById('game-status');
  const playersSubstituteEl = document.getElementById('playersSubstitute');
  const playersEl = document.getElementById('players');
  const expandIconEl = document.getElementById('players-expand-icon');
  const playerListEl = document.getElementById('player-list');

  const animationDurationMs = 700;
  const playerListMarginTopPx = 8;

  window.addEventListener('resize', function() {
    const playersState = playersEl.getAttribute('data-state');

    if (playersState === 'expanded') {
      const playersExpandedHeightpx = window.innerHeight - gameStatusEl.offsetHeight - playerListMarginTopPx;

      playersEl.style.width = `${layoutEl.offsetWidth}px`;
      playersEl.style.height = `${playersExpandedHeightpx}px`;
    }
  });

  function togglePlayerListSize() {
    const state = playersEl.getAttribute('data-state');

    const playersExpandedHeightpx = window.innerHeight - gameStatusEl.offsetHeight - playerListMarginTopPx;

    if (state === 'collapsed') {
      playersEl.style.width = `${layoutEl.offsetWidth}px`;
      playersEl.style.height = `${playersEl.offsetHeight}px`;

      expandIconEl.classList.add('rotate-180');

      // If there is no timeout, the animation doesn't occur. It's as if the second value is applied immediately, bypassing the first value altogether.
      // One millisecond is enough in dev, put 5 for security.
      setTimeout(() => {
        playersEl.classList.add('fixed', 'bottom-0', 'transition-[height]', `duration-${animationDurationMs}`);

        setTimeout(() => {
          playersSubstituteEl.classList.remove('hidden');
          playersSubstituteEl.classList.add('block');

          playersEl.style.height = `${playersExpandedHeightpx}px`;
        }, 5);
      }, 5);

      setTimeout(() => {
        playersEl.classList.remove('transition-[height]', `duration-${animationDurationMs}`);
        playerListEl.classList.add('overflow-y-auto');
        playersEl.setAttribute('data-state', 'expanded');
      }, (animationDurationMs));
    } else {
      playersEl.classList.add('transition-[height]', `duration-${animationDurationMs}`);
      playersEl.style.height = `${playersSubstituteEl.offsetHeight}px`;

      expandIconEl.classList.remove('rotate-180');

      playerListEl.classList.remove('overflow-y-auto');

      setTimeout(() => {
        playersEl.style.removeProperty('width');
        playersEl.classList.remove('fixed', 'bottom-0');
        playersEl.classList.add('flex', 'flex-1', `min-h-[136px]`);
        playersEl.classList.remove('transition-[height]', `duration-${animationDurationMs}`);

        playersSubstituteEl.classList.remove('block');
        playersSubstituteEl.classList.add('hidden');

        playersEl.setAttribute('data-state', 'collapsed');
      }, animationDurationMs);

    }
  }

  const pusher = new Pusher('6csm0edgczin2onq92lm', window.pusher_data);
  const channel = pusher.subscribe('game.{{$game->id}}');
  let lastUpdate = new Date(0);
  let pendingUpdate = false;

  channel.bind('player.update', function(data) {
    const currentTime = new Date();
    if (currentTime - lastUpdate < 2500) {
      pendingUpdate = true;
      return;
    }
    window.htmx.ajax('GET', '/game/{{$game->id}}/lobby/player-list', '#player-list');
    lastUpdate = currentTime;
  });

  setInterval(function() {
    const currentTime = new Date();
    if (pendingUpdate && currentTime - lastUpdate > 2500) {
      window.htmx.ajax('GET', '/game/{{$game->id}}/lobby/player-list', '#player-list');
      lastUpdate = new Date();
      pendingUpdate = false;
    } else if (currentTime - lastUpdate > 20000) {
      window.htmx.ajax('GET', '/game/{{$game->id}}/lobby/player-list', '#player-list');
      lastUpdate = new Date();
    }
  }, 300 + Math.floor(Math.random() * 120));

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