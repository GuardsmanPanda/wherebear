<div class="grid grid-cols-3 min-[400px]:grid-cols-4 min-[530px]:grid-cols-5 min-[630px]:grid-cols-6 md:grid-cols-6 gap-4 justify-between">
  @foreach($players as $player)
  <x-player-profile-small-lobby :isHost="$loop->first" :isReady="$player->is_ready" :level="$player->user_level_enum"
    :name="$player->display_name" :icon="$player->map_marker_file_name" :countryCode="$player->cca2" />
  @endforeach
</div>