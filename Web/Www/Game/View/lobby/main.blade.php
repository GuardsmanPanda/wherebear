<?php

declare(strict_types=1); ?>
@php
  use Domain\Map\Enum\MapStyleEnum;use GuardsmanPanda\Larabear\Infrastructure\Auth\Service\BearAuthService;use Web\Www\Shared\Enum\ButtonSize;use Web\Www\Shared\Enum\ButtonStyle;use Web\Www\Shared\Enum\ButtonType;use Web\Www\Shared\Enum\RewardType;use Web\Www\Shared\Enum\UserLevelBadgeSize;

  $isPlayerHost = $game->created_by_user_id === BearAuthService::getUserId();
@endphp

<x-panel class="m-2">
  <div class="flex flex-col w-full">
    <div class="flex gap-2">
      <x-user-level-badge :size="UserLevelBadgeSize::LG" :level="$user->user_level_enum"/>
      <div class="flex flex-col">
        <div class="flex gap-1 items-center">
          <span class="text-lg text-shade-text-title">{{ $user->display_name }}</span>
          <x-icon icon="pencil-square" size=6 class="cursor-pointer hover:text-shade-text-title" hx-get="/game/{{$game->id}}/lobby/dialog/name-flag"/>
        </div>
        <span class="text-sm text-shade-text-subtitle">Enthusiast Traveler</span>
      </div>
      <img class="h-8 ml-auto rounded border border-shade-border-default shadow"
           src="https://devfuntime.gman.bot/static/flag/svg/{{ $user->cca2 }}.svg"
           alt="{{ $user->country_name }}" title="{{ $user->country_name }}"/>
    </div>
    <div class="flex flex-col">
      <x-progress-bar percentage=45 class="w-full" label="120/140"/>
      <div class="flex justify-between items-start">
        <div class="flex flex-wrap items-baseline gap-1">
          <span class="font-heading text-sm text-shade-text-subtitle">Next Level:</span>
          <span class="text-md font-medium text-primary-text">{{$user->current_level_experience}}/{{$user->next_level_experience}}</span>
        </div>
        <x-next-reward class="mt-[2px]" :type="RewardType::ICON" name="Kitty Cat" icon-url="/static/img/map-marker/cat.png"/>
      </div>
    </div>
  </div>
</x-panel>

<x-panel class="m-2">
  <x-slot:heading>
    <x-heading label="Player Settings">
      <div class="flex items-center gap-2">
        <div
          class="text-md {{ $user->is_ready ? 'text-primary-text' : 'text-error-text' }} font-medium">{{ $user->is_ready ? 'Ready' : 'Not Ready' }}</div>
        <div
          class=" flex w-6 h-6 rounded-full border {{ $user->is_ready ? 'bg-primary-surface-default border-primary-border-default' : 'bg-error-surface-default border-error-border-default' }}"></div>
      </div>
    </x-heading>
  </x-slot>

  <div class="flex w-full gap-2">
    <div class="flex justify-between gap-2">
      <x-button-selector label="Marker" imageUrl='/static/img/map-marker/{{ $user->map_marker_file_name }}'
                         hx-get="/game/{{$game->id}}/lobby/dialog/map-marker"/>
      <x-button-selector label="Map" :imageUrl="MapStyleEnum::from($user->map_style_enum)->mapTileUrl(z: 11, x: 1614, y: 1016)"
                         hx-get="/game/{{$game->id}}/lobby/dialog/map-style"/>
    </div>
    <div class="flex w-full justify-end items-end">
      @if($user->is_ready)
        <div hx-patch="/game/{{$game->id}}/lobby/update-game-user" hx-vals='{"is_ready": false}'>
          <x-button label="Cancel" :style="ButtonStyle::ERROR" :size="ButtonSize::LG" icon="x-circle" class="w-[160px]"/>
        </div>
      @else
        <div hx-patch="/game/{{$game->id}}/lobby/update-game-user" hx-vals='{"is_ready": true}'>
          <x-button label="Ready" :style="ButtonStyle::PRIMARY" :size="ButtonSize::LG" icon="play-circle" class="w-[160px]"/>
        </div>
      @endif
    </div>
  </div>
</x-panel>

<x-panel class="flex flex-col m-2">
  <x-slot:heading>
    <x-heading label="Game Settings"/>
  </x-slot>

  <div class="flex flex-col w-full gap-4">
    <div class="flex justify-between gap-4 border-b-0 border-shade-border-light">
      <div class="flex items-center h-8  border border-shade-border-default rounded">
        <div class="flex h-full items-center px-1 bg-tertiary-surface-subtle rounded-l text-xs text-shade-text-caption">ACCESS</div>
        <div
          class="flex h-full items-center px-1 rounded-r text-xs text-white font-medium {{ $game->is_public ? 'bg-info-surface-default' : 'bg-error-surface-default' }}">
          {{ $game->game_public_status_enum }}
        </div>
      </div>
      <div class="flex h-8 gap-1 bg-tertiary-surface-subtle border border-shade-border-default rounded overflow-hidden">
        <div id="game-join-link" class="flex items-center truncate px-2 py-1 border-r border-shade-border-default text-xs text-shade-text-body">
          <span class="truncate">https://wherebear.game/123456</span>
        </div>
        <div class="cursor-pointer p-1" onclick="copyGameJoinLinkToClipboard()">
          <x-icon icon="clipboard"/>
        </div>
      </div>
    </div>

    @if ($isPlayerHost)
      <div class="flex items-end gap-4 pb-4 border-b border-shade-border-light">
        <x-button label="Delete" :type="ButtonType::SECONDARY" :style="ButtonStyle::ERROR" :size="ButtonSize::SM" hx-delete="/game/{{$game->id}}"
                  class="w-full"/>
        <x-button label="Configure" :type="ButtonType::SECONDARY" :style="ButtonStyle::WARNING" :size="ButtonSize::SM"
                  hx-get="/game/{{$game->id}}/lobby/dialog/settings"
                  class="w-full"/>
        <x-button label="Start" :type="ButtonType::SECONDARY" :style="ButtonStyle::INFO" :size="ButtonSize::SM" hx-post="/game/{{$game->id}}/start"
                  class="w-full"/>
      </div>
    @endif

    <div class="flex justify-between items-center gap-2">
      @php
        $gameSettings = [
        ['title' => 'Rounds', 'value' => $game->number_of_rounds],
        ['title' => 'Guessing Time', 'value' => $game->round_duration_seconds, 'suffix' => 's'],
        ['title' => 'Result Time', 'value' => $game->round_result_duration_seconds, 'suffix' => 's']
        ];
      @endphp
      @foreach ($gameSettings as $gameSetting)
        <div class="flex flex-col items-center w-full h-full gap-2 @if(!$loop->last) pr-2 border-r border-shade-border-light @endif">
                    <span
                      class="flex w-full h-full justify-center items-center font-heading text-sm text-shade-text-subtitle bg-tertiary-surface-default rounded">{{ $gameSetting['title'] }}</span>
          <span class="text-lg text-shade-text-title">{{ $gameSetting['value'] }}{{ $gameSetting['suffix'] ?? '' }}</span>
        </div>
      @endforeach
    </div>

    <div class="flex justify-between items-center p-2 pb-0 border-t border-shade-border-default rounded-b">
      <span class="font-heading text-lg text-shade-text-title">Total Game Time</span>
      <span class="text-lg text-shade-text-title font-medium">~{{ round(num: ($game->number_of_rounds * ($game->round_duration_seconds + $game->round_result_duration_seconds + 1) + 90) / 60) }}
          minutes</span>
    </div>
  </div>
</x-panel>

<script>
  function copyGameJoinLinkToClipboard() {
    var gameJoinLink = document.getElementById("game-join-link");
    navigator.clipboard.writeText(gameJoinLink.innerText);
  }
</script>