<?php declare(strict_types=1); ?>

<div x-data="state" class="flex flex-col h-screen select-none">
  <div class="relative flex-1 overflow-hidden">
    <lit-round-result-header
      countryCca2="{{ strtolower($game->country_cca2) }}"
      countryName="{{ $game->country_name }}"
      countrySubdivisionName="{{ $game->country_subdivision_name }}"
      userGuess='{
        "detailedPoints": {{ $user_guess->detailed_points }},
        "distanceMeters": {{ $user_guess->distance_meters }},
        "flagFilePath": "{{ $user_guess->flag_file_path }}",
        "rank": {{ $user_guess->rank }},
        "roundedPoints": {{ $user_guess->rounded_points }},
        "countryMatch": {{ $user_guess->country_match ? 'true' : 'false' }},
        "countrySubdivisionMatch": {{ $user_guess->country_subdivision_match ? 'true' : 'false' }}
      }'>
    </lit-round-result-header>



    <div class="flex justify-end gap-2 absolute bottom-0 w-full p-2">
      <lit-round-result-ranking-dialog-button class="z-10"
        :guesses="JSON.stringify(guesses)"
      ></lit-round-result-ranking-dialog-button>
    </div>

    <lit-game-round-map
      x-ref="map"
      mapStyleTileSize="{{ $user->map_style_tile_size }}"
      mapStyleFullUri="{{ $user->map_style_full_uri }}"
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

<script>
document.addEventListener('alpine:init', () => {
  Alpine.data('state', () => ({
    guesses: @json($guesses)
  }));
})
</script>