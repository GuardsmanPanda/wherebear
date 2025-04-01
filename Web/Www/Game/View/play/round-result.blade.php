<?php declare(strict_types=1); ?>

<div x-data="roundResultState" class="flex h-screen select-none">
  <div class="flex flex-col w-full">
    <div class="relative flex-1 overflow-hidden">
      <lit-round-result-header
        countryCca2="{{ strtolower($game->country_cca2) }}"
        countryName="{{ $game->country_name }}"
        countrySubdivisionName="{{ $game->country_subdivision_name }}"
        @if ($user_guess)
          userGuess='{
            "countryMatch": {{ $user_guess->country_match ? 'true' : 'false' }},
            "countryName": "{{ $user_guess->country_name }}",
            "countrySubdivisionMatch": {{ $user_guess->country_subdivision_match ? 'true' : 'false' }},
            "detailedPoints": {{ $user_guess->detailed_points }},
            "distanceMeters": {{ $user_guess->distance_meters }},
            "flagFilePath": "{{ $user_guess->flag_file_path }}",
            "rank": {{ $user_guess->rank }},
            "roundedPoints": {{ $user_guess->rounded_points }}
          }'
        @endif
        >
      </lit-round-result-header>

      <div class="flex justify-end gap-2 absolute bottom-0 w-full p-2">
        <lit-round-result-ranking-dialog-button class="z-10 xl:hidden"
          :guesses="JSON.stringify(guesses)"
          :userId="user.id"
        ></lit-round-result-ranking-dialog-button>
      </div>

      <lit-game-round-map
        x-ref="map"
        mapStyleEnum="{{ $user->map_style_enum }}"
        mapStyleTileSize="{{ $user->map_style_tile_size }}"
        mapStyleFullUri="{{ $user->map_style_full_uri }}"
        panoramaLocationMarkerAnchor="{{ $user->map_location_marker_anchor }}"
        panoramaLocationMarkerImgPath="{{ $user->map_location_marker_img_path }}"
        :guesses="JSON.stringify(guesses)"
        :panoramaLng="{{ $game->panorama_lng }}"
        :panoramaLat="{{ $game->panorama_lat }}"
        class="w-full h-full">
      </lit-game-round-map>
    </div>

    <x-play-footer
      page="round-result"
      :rounds="$rounds"
      secondsRemaining="{{ $game->round_result_seconds_remaining }}"
      :selectedRoundNumber="$game->current_round"
      :totalRoundCount="$game->number_of_rounds"
    />
  </div>

  <div class="hidden xl:flex flex-col border-l border-gray-700 bg-iris-200">
    <lit-panel-header2 label="RANKING" noBorder noRounded class="w-full">
      <div slot="right" class="font-heading font-semibold text-lg text-gray-50">
        {{ count($guesses) }} {{ count($guesses) === 1 ? 'player' : 'players' }}
      </div>
    </lit-panel-header2>

    <div class="flex flex-col gap-2 min-w-64 overflow-y-auto border-t border-gray-700 p-2">
      <template x-for="guess in guesses" :key="guess.user_id">
        <lit-player-result-item
          :countryCCA2="guess.user_country_cca2"
          :detailedPoints="guess.detailed_points"
          :distanceMeters="guess.distance_meters"
          :flagFilePath="guess.user_flag_file_path"
          :flagDescription="guess.user_flag_description"
          :iconPath="guess.map_marker_file_path"
          :level="guess.user_level"
          :name="guess.user_display_name"
          :rank="guess.rank"
          rankIconType="medal"
          :rankSelected="guess.user_id === user.id ? guess.rank : ''"
          :roundedPoints="guess.rounded_points"
          :userTitle="guess.title">
        </lit-player-result-item>
      </template>
    </div>
  </div>
</div>

<script>
  document.addEventListener('alpine:init', () => {
    Alpine.data('roundResultState', () => ({
      guesses: @json($guesses),
      user: @json($user)
    }));
  })
</script>