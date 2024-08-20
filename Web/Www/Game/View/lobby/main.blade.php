<?php

declare(strict_types=1); ?>
@php
use GuardsmanPanda\Larabear\Infrastructure\Auth\Service\BearAuthService;
use Web\Www\Shared\Enum\ButtonSize;
use Web\Www\Shared\Enum\ButtonStyle;
use Web\Www\Shared\Enum\ButtonType;
use Web\Www\Shared\Enum\RewardType;
use Web\Www\Shared\Enum\TooltipPosition;
use Web\Www\Shared\Enum\UserLevelBadgeSize;

$isPlayerHost = $game->created_by_user_id === BearAuthService::getUserId();
$isPlayerGuest = $user->user_level_enum === 0;
@endphp

<x-panel class="m-2">
  <div class="flex flex-col w-full">
    <div class="flex gap-2">
      @if(!$isPlayerGuest)
      <x-user-level-badge :size="UserLevelBadgeSize::LG" :level="$user->user_level_enum" />
      @endif
      <div class="flex flex-col">
        <div class="flex gap-1 items-center">
          <span class="text-lg text-shade-text-title">{{ $user->display_name }}</span>
          <x-icon icon="pencil-square" :isButton=true size=5 hx-get="/game/{{$game->id}}/lobby/dialog/name-flag" />
        </div>
        <span class="text-sm text-shade-text-subtitle">Enthusiast Traveler</span>
      </div>
      <img class="h-8 ml-auto rounded border border-shade-border-default shadow"
        src="/static/flag/svg/{{ $user->cca2 }}.svg"
        alt="{{ $user->country_name }}" title="{{ $user->country_name }}" />
    </div>
    @if(!$isPlayerGuest)
    <div class="flex flex-col">
      <div class="flex gap-1 text-sm text-shade-text-title">
        <span>Next Level:</span>
        <span class="font-medium">{{ $user->current_level_experience }}/{{ $user->next_level_experience }} XP</span>
      </div>
      <x-progress-bar :percentage="$user->current_level_experience * 100 / $user->next_level_experience" class="w-full" />
      <div class="flex justify-end items-start">
        @php
        $nextRewards = [
        (object) ['type' => RewardType::MAP_MARKER, 'iconFilename' => 'cat.png'],
        (object) ['type' => RewardType::MAP, 'iconFilename' => 'satellite-xs.png'],
        ];
        @endphp
        <x-next-reward class="mt-4" :level="$user->user_level_enum + 1" :rewards="$nextRewards" />
      </div>
    </div>
    @endif
  </div>
</x-panel>

<x-panel class="m-2">
  <x-slot:heading>
    <x-heading label="Player Settings">
      <div class="flex items-center gap-2">
        <div
          class="text-sm font-medium {{ $user->is_ready ? 'text-success-text' : 'text-error-text' }}">{{ $user->is_ready ? 'Ready' : 'Not Ready' }}</div>
        <div
          class=" flex w-4 h-4 rounded-full border {{ $user->is_ready ? 'bg-success-surface-default border-success-border-default' : 'bg-error-surface-default border-error-border-default' }}"></div>
      </div>
    </x-heading>
    </x-slot>

    <div class="flex w-full gap-2">
      <div class="flex justify-between gap-2">
        <x-button-selector label="Marker" imageUrl='/static/img/map-marker/{{ $user->map_marker_file_name }}'
          hx-get="/game/{{$game->id}}/lobby/dialog/map-marker" />
        @if(!$isPlayerGuest)
        <x-button-selector label="Map" :imageUrl="str_replace(search: ['{x}', '{y}', '{z}'], replace: [1614, 1016, 11], subject: $user->map_style_full_uri)"
          hx-get="/game/{{$game->id}}/lobby/dialog/map-style" />
        @endif
      </div>
      <div class="flex w-full justify-end items-end">

        @if($user->is_ready)
        <div hx-patch="/game/{{$game->id}}/lobby/update-game-user" hx-vals='{"is_ready": false}'>
          <x-button label="Cancel" :style="ButtonStyle::ERROR" :size="ButtonSize::LG" icon="x-circle" class="w-[160px]" />
        </div>
        @else
        <div hx-patch="/game/{{$game->id}}/lobby/update-game-user" hx-vals='{"is_ready": true}'>
          <x-button label="Ready" :style="ButtonStyle::PRIMARY" :size="ButtonSize::LG" icon="play-circle" class="w-[160px]" />
        </div>
        @endif
      </div>
    </div>
</x-panel>

<x-panel class="flex flex-col m-2">
  <x-slot:heading>
    <x-heading label="Game Settings" />
  </x-slot:heading>

  <div class="flex flex-col w-full gap-4">
    <div class="flex h-8 justify-between items-center">
      <div class="flex h-full items-center border border-shade-border-light rounded">
        <div class="flex h-full items-center px-1 bg-tertiary-surface-light rounded-l text-xs text-shade-text-subtitle">ACCESS</div>
        <div
          class="flex h-full items-center px-1 rounded-r text-xs text-shade-text-negative font-medium {{ $game->is_public ? 'bg-info-surface-default' : 'bg-error-surface-default' }}">
          {{ $game->game_public_status_enum }}
        </div>
      </div>

      <div class="flex h-full bg-tertiary-surface-light border border-shade-border-light rounded">
        <div id="game-join-link" class="flex h-full items-center truncate px-2 py-1 border-r border-shade-border-default text-xs text-shade-text-body">
          <span class="truncate">https://wherebear.xyz/123456</span>
        </div>
        <div id="clipboard-icon">
          <x-tooltip label="Copy url to clipboard" :position="TooltipPosition::LEFT" class="flex justify-center items-center p-1">
            <x-icon icon="clipboard" :isButton=true onclick="copyGameJoinLinkToClipboard()" />
          </x-tooltip>
        </div>
      </div>
    </div>

    @if ($isPlayerHost)
    <div class="flex items-end gap-2">
      <x-button label="Delete" :type="ButtonType::SECONDARY" :style="ButtonStyle::ERROR" :size="ButtonSize::SM" hx-delete="/game/{{$game->id}}"
        class="w-full" />
      <x-button label="Configure" :type="ButtonType::SECONDARY" :style="ButtonStyle::WARNING" :size="ButtonSize::SM"
        hx-get="/game/{{$game->id}}/lobby/dialog/settings"
        class="w-full" />
      <x-button label="Start" :type="ButtonType::SECONDARY" :style="ButtonStyle::INFO" :size="ButtonSize::SM" hx-post="/game/{{$game->id}}/start"
        class="w-full" />
    </div>
    @endif

    <div class="flex flex-col gap-2">
      <div class="flex justify-between items-center gap-2">
        @php
        $gameSettings = [
        ['title' => 'Rounds', 'value' => $game->number_of_rounds],
        ['title' => 'Guessing Time', 'value' => $game->round_duration_seconds, 'suffix' => 's'],
        ['title' => 'Result Time', 'value' => $game->round_result_duration_seconds, 'suffix' => 's']
        ];
        @endphp
        @foreach ($gameSettings as $gameSetting)
        <div class="flex flex-col items-center w-full h-full rounded border border-shade-border-light">
          <div class="flex w-full h-full justify-center items-center p-1 rounded bg-tertiary-surface-dark">
            <span class="font-heading text-xs text-shade-text-body uppercase">{{ $gameSetting['title'] }}</span>
          </div>
          <div class="flex w-full h-full justify-center items-center bg-tertiary-surface-light">
            <span class="text-shade-text-title">{{ $gameSetting['value'] }}{{ $gameSetting['suffix'] ?? '' }}</span>
          </div>
        </div>
        @endforeach
      </div>

      <div class="flex justify-between items-center p-2 pb-0 bg-tertiary-surface-light border-t border-shade-border-light rounded-b">
        <span class="font-heading text-shade-text-title">Total Game Time</span>
        <span class="text-shade-text-title font-medium">~{{ round(num: ($game->number_of_rounds * ($game->round_duration_seconds + $game->round_result_duration_seconds + 1) + 90) / 60) }}
          minutes</span>
      </div>
    </div>
  </div>
</x-panel>

<script>
  let clipboardIconState = 'default';

  function switchClipboardIcon() {
    const clipboardIconEl = document.getElementById('clipboard-icon');

    if (clipboardIconState === 'default') {
      const defaultClipboardIconEl = clipboardIconEl.innerHTML;

      // This approach avoids placing the second icon and its tooltip directly in the HTML and toggling 
      // their 'hidden' and 'block' styles. Doing so would cause the 'copied' tooltip to appear below 
      // the original tooltip, as both tooltips would exist in the DOM at the same time and use absolute position.
      clipboardIconEl.innerHTML = `
        <x-tooltip label="Copied!" :position="TooltipPosition::LEFT" class="flex justify-center items-center p-1">
          <x-icon id="clipboard-icon" icon="clipboard-document-check" :isButton=true />
        </x-tooltip>
      `
      clipboardIconState = 'copied';

      setTimeout(() => {
        clipboardIconEl.innerHTML = defaultClipboardIconEl;
        clipboardIconState = 'default';
      }, 2000);
    }
  }

  function copyGameJoinLinkToClipboard() {
    const gameJoinLinkEl = document.getElementById("game-join-link");
    navigator.clipboard.writeText(gameJoinLinkEl.innerText);

    switchClipboardIcon();
  }
</script>