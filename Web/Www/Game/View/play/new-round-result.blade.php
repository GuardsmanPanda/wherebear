<?php declare(strict_types=1); ?>
@php use Web\Www\Game\Util\GameUtil; @endphp

<div x-data="state({{ $game->round_result_seconds_remaining }})" class="flex flex-col h-screen">
  <div x-show="isRankingDialogOpen" class="dialog-overlay" x-cloak></div>
  <div class="relative flex-1 overflow-hidden">
    <div class="flex justify-between items-start bg-blue-400 border-b border-gray-800">
      <div class="flex gap-2 w-full relative mr-[122px]">
        <img class="h-16 absolute top-1 left-1 z-10 drop-shadow" src="/static/flag/wavy/{{ strtolower($game->cca2) }}.png" alt="Flag of {{ $game->country_name }}" />
        <div class="flex flex-col gap-1 mt-0 py-1 pl-[92px]">
          <div class="text-3xl text-gray-100 font-medium text-stroke-2 leading-none">France</div>
          <div class="text-lg text-gray-200 font-medium text-stroke-2 leading-none">{{ $game->state_name }}</div>
        </div>
      </div>

      <div class="absolute top-0 right-1 z-10">
        <img class="relative bottom-[8px]" src="/static/img/ui/ribbon-emblem.png" />

        <div class="flex flex-col items-center w-[114px] absolute top-[8px]">
          @php
            $rank = (int)$player_guess->rank;
            $gapClass = ($rank === 1) ? 'gap-0' : (($rank === 2) ? 'gap-1' : (($rank === 3) ? 'gap-0.5' : 'gap-1'));
          @endphp

          <div class="flex items-end {{ $gapClass }}">
            <span class="text-5xl font-bold text-white text-stroke-2 z-10">{{ $player_guess->rank }}</span>
            <span class="relative bottom-[1px] text-xl font-medium text-white text-stroke-2 {{ $player_guess->rank === '1' ? 'relative right-1' : '' }}">{{ GameUtil::getOrdinalSuffix($player_guess->rank) }}</span>
          </div>

          <div class="relative left-1.5 mt-1">
            <div class="flex justify-center w-[72px] relative rounded border border-gray-800 bg-blue-500">
              <div class="w-6 aspect-auto absolute -top-[4px] left-0 transform -translate-x-1/2">
                <x-custom-icon icon="star" />
              </div>
              <span class="text-xs text-white">{{ $player_guess->points }}</span>
            </div>
      
            <div class="flex justify-center items-center {{ $player_guess->country_cca2 === 'NP' ? 'w-[74px] relative right-[2px]' : 'w-[72px] relative' }} mt-3 rounded border border-gray-800 bg-blue-500">
              <img class="h-5 absolute {{ $player_guess->country_cca2 === 'NP' ? '-left-[2px]' : 'left-0 transform -translate-x-1/2 border border-black rounded' }}" 
              src="/static/flag/svg/{{ $player_guess->country_cca2 }}.svg">
              @php
              $distanceAndUnit = GameUtil::getDistanceAndUnit(distanceMeters: $player_guess->distance_meters);
              $mlClass = 'ml-0';
              if ($distanceAndUnit['unit'] === 'km') {
                $distanceCharactersCount = strlen((string)$distanceAndUnit['value']);
                if ($distanceCharactersCount > 3) {
                  $mlClass = 'ml-3';
                } else if ($distanceCharactersCount > 2) {
                  $mlClass = 'ml-2';
                }                    
              } 
              @endphp
              <span class="text-xs text-white {{ $mlClass }}">{{ $distanceAndUnit['value'] }}{{ $distanceAndUnit['unit'] }}</span>
            </div>
         </div>
        </div>
      </div>

      <dialog id="rankingDialog" class="dialog absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 z-50">
        <lit-dialog iconPath="/static/img/icon/podium.svg" label="Ranking" x-on:closed="closeRankingDialog()">
          <div class="flex flex-col gap-1 p-1">
          @foreach ($guesses as $guess)
          <div class="p-0">
            <lit-player-result 
              distanceMeters="{{ $guess->distance_meters }}"
              iconPath="{{ $guess->map_marker_file_path }}"
              name="{{ $guess->display_name }}"
              rank="{{ $guess->rank }}"
              points="{{ $guess->points }}"
              title="{{ $guess->title }}">
            </lit-player-result>    
          </div>
          @endforeach
          </div>
        </lit-dialog>
      </dialog>
    </div>

    <div class="flex justify-end md:justify-center gap-2 absolute bottom-0 w-full p-1">
      <lit-button-square imgPath="/static/img/icon/podium.svg" label="Ranking" size="xl" class="z-10" x-on:clicked="onSwitchRankingButtonClicked($event);" ></lit-button-square>
      {{-- <button @click="switchIsSelected()" class="z-10">switch</button> --}}
    </div>
    <div id="map" class="flex w-full h-full"></div>
  </div>
  <div class="flex flex-col">
    <div class="relative">
      <img src="/static/img/pengu-sign.png" class="absolute -left-1 bottom-[10px] h-20 z-20" alt="Cutest pengu around">
      <div class="flex justify-center items-center w-12 h-8 absolute bottom-[62px] left-[13px]">
        <span x-text="timeRemainingSec" class="font-heading text-xl font-medium text-gray-900 select-none z-30"></span>
      </div>
      <lit-progress-bar sideFlated sideUnbordered :innerBgColor="innerBgColor" :percentage="percentage" :transitionDurationMs="transitionDurationMs"></lit-progress-bar>
    </div>
    <x-country-used-list :countries="$countries_used" :currentRoundNumber="$game->current_round" :totalRounds="$game->number_of_rounds" :selectedRound="5" />
  </div>
</div>

<script>
 

  function state(durationSec) {
    return {
      isSelected: false,
      isDialogVisible: true,
      rankingDialog: document.getElementById('rankingDialog'),
      isRankingDialogOpen: false,
      openRankingDialog() {
        this.rankingDialog.showModal();
        this.isRankingDialogOpen = true;
      },
      closeRankingDialog() {
        this.rankingDialog.close();
        this.isRankingDialogOpen = false;
      },
      switchRankingDialog() {
        (this.isRankingDialogOpen) ? this.closeRankingDialog() : this.openRankingDialog();
      },
      switchIsSelected() {
        this.isSelected = !this.isSelected;
      },
      onSwitchRankingButtonClicked(e) {
        this.isSelected = !this.isSelected;
        this.switchRankingDialog();
      },
      intervalDurationMs: 1000,
      percentage: 100,
      timeRemainingSec: durationSec,
      timerInterval: null,
      transitionDurationMs: 0,
      guessButtonState: "abc",
      get innerBgColor() {
        return `hsl(${123 * this.percentage / 100}, 69%, 58%)`;
      },
      start() {
        const totalStepCount = (durationSec * 1000) / this.intervalDurationMs;
        const percentageStep = (100 / (totalStepCount));
        let firstCycle = true

        const tick = () => {
          if (this.percentage <= 0) {
            clearInterval(this.timerInterval);
            this.percentage = 0;
            this.timeRemainingSec = 0;
          } else {
            this.percentage = Math.max(this.percentage - percentageStep, 0);

            if (!firstCycle) {
              this.timeRemainingSec--;
            }
            firstCycle = false;
          }
        };

        tick();
        this.timerInterval = setInterval(() => {
          tick();
        }, this.intervalDurationMs);
      },
      reset() {
        this.percentage = 100;
        this.timeRemainingSec = durationSec;
      },
      init() {
        this.openRankingDialog();    
      },
      destroy() {
        clearInterval(this.timerInterval);
      },
      onGuessButtonClicked() {
        console.log('guess button clicked');
        this.guessButtonState = "new state";
      }
    }
  }

  const map = new window.maplibregl.Map({
    container: 'map', 
    style: {
      'version': 8, 
      'sources': {
      'raster-tiles': {
        'type': 'raster', 
          'tiles': ['{{$map->full_uri}}'], 
          'tileSize': {{$map->tile_size}},
        }
      }, 
      'layers': [{'id': 'simple-tiles', 'type': 'raster', 'source': 'raster-tiles'}]
    }, 
    center: [{{ $game->panorama_lng }}, {{ $game->panorama_lat}}], 
    dragRotate: false, 
    keyboard: false, 
    minZoom: 1, 
    maxZoom: 18, 
    zoom: 3,
    attributionControl: false
  })


  map.scrollZoom.setWheelZoomRate(1 / 75);
  map.scrollZoom.setZoomRate(1 / 75);
  map.touchZoomRotate.disableRotation();


  const playerGuesses = @json($guesses);
  playerGuesses.forEach(guess => {
    const mapPlayerMarkerElement = document.createElement('div');
    mapPlayerMarkerElement.innerHTML = `
      <lit-map-marker distanceMeters="${guess.distance_meters}" mapMarkerFilePath="${guess.map_marker_file_path}" playerName="${guess.display_name}" rank="${guess.rank}"></lit-map-marker>
    `;

    new window.maplibregl
      .Marker({element: mapPlayerMarkerElement, anchor: 'bottom'})
      .setLngLat([guess.lng, guess.lat])
      .addTo(map);
  });

    const mapPanoramaMarkerElement = document.createElement('div');
    mapPanoramaMarkerElement.innerHTML = `
      <img src="/static/img/map-extra/marker-win3.png" class="w-16" />
    `;

    new window.maplibregl
      .Marker({element: mapPanoramaMarkerElement, anchor: 'center'})
      .setLngLat([{{ $game->panorama_lng }}, {{ $game->panorama_lat }}])
      .addTo(map);
</script>