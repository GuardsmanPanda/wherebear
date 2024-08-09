<?php

declare(strict_types=1); ?>

<div class="flex flex-col h-full grow p-2 bg-primary-surface-default border border-b-0 border-primary-border-dark rounded-t-2xl">
  <div class="flex justify-center items-center pb-2 border-b border-primary-border-dark">
    <div class="font-heading text-base text-shade-text-title font-medium uppercase">Players</div>
  </div>

  <div class="grid grid-cols-3 min-[400px]:grid-cols-4 min-[530px]:grid-cols-5 min-[630px]:grid-cols-6 md:grid-cols-6 gap-4 py-2 justify-between">
    @foreach($players as $player)
    <x-player-profile-small-lobby :isHost="$loop->first" :isReady="$player->is_ready" :level="$player->user_level_enum" :name="$player->display_name" :icon="$player->map_marker_file_name" :countryCode="$player->cca2" />
    @endforeach
  </div>
</div>