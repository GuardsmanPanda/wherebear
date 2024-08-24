<?php

declare(strict_types=1); ?>

<div class="grid grid-cols-3 min-[400px]:grid-cols-4 min-[530px]:grid-cols-5 min-[630px]:grid-cols-6 md:grid-cols-6 gap-4 justify-between sm:hidden">
  @foreach($players as $player)
  <x-player-profile-small-lobby :isHost="$loop->first" :isReady="$player->is_ready" :level="$player->user_level_enum"
    :name="$player->display_name" :icon="$player->map_marker_file_name" :flagFilePath="$player->flag_file_path" :flagDescription="$player->flag_description" />
  @endforeach
</div>

<div class="hidden sm:grid lg:hidden grid-cols-2 gap-2">
  @foreach($players as $player)
  <x-player-profile-large-lobby :isHost="$loop->first" :isReady="$player->is_ready" :level="$player->user_level_enum"
    :name="$player->display_name" :isActive="random_int(0, 1) === 1" title="Enthusiast Traveler" :icon="$player->map_marker_file_name" :flagFilePath="$player->flag_file_path" :flagDescription="$player->flag_description" class="hidden sm:flex p-2 rounded-md bg-primary-surface-subtle border border-primary-border-default" />
  @endforeach
</div>


<div class="hidden lg:flex flex-col gap-2">
  @foreach($players as $player)
  <x-player-profile-large-lobby :isHost="$loop->first" :isReady="$player->is_ready" :level="$player->user_level_enum"
    :name="$player->display_name" :isActive="random_int(0, 1) === 1" title="Enthusiast Traveler" :icon="$player->map_marker_file_name" :flagFilePath="$player->flag_file_path" :flagDescription="$player->flag_description" class="hidden sm:flex p-2 rounded-md bg-white border border-shade-border-light" />
  @endforeach
</div>