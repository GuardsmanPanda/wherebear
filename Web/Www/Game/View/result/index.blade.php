<?php declare(strict_types=1); 
  use Web\Www\Game\Util\GameUtil;
?>

<div x-ref="gameResult" x-data="state({{ $is_dev ?? null }})" class="flex flex-col h-screen bg-iris-200 select-none">
  <!-- Header -->
  <div class="flex h-14 z-30 justify-between items-center px-2 border-b border-gray-700 bg-iris-500">
    <div class="flex flex-1 w-16">
      <lit-button 
        imgPath="/static/img/icon/cross.svg"
        size="md"
        bgColorClass="bg-gray-400"
        x-cloak
        x-show="isLargeScreen || currentPage === 'game'"
        x-on:click="navigateToHome">
      </lit-button>
      <lit-button
        imgPath="/static/img/icon/arrow-back.svg"
        size="md"
        bgColorClass="bg-gray-400"
        x-cloak
        x-show="isSmallScreen && currentPage !== 'game'"
        x-on:click="navigateToGame">
      </lit-button>
    </div>
    <div class="flex flex-grow justify-center items-center gap-4">
      <span class="px-2 font-heading text-xl sm:text-2xl font-bold text-white text-stroke-2 text-stroke-iris-900">{{ $game->name }}</span>
    </div>
    <div class="flex-1">
      <div class="hidden lg:flex justify-end gap-4">
        <lit-label label="{{ $game->type === 'template' ? 'TEMPLATE' : 'CLASSIC' }}" size="sm" type="dark" class="w-[88px]"></lit-label>
        <lit-label label="{{ $game->total_game_time_mn }} MN" iconPath="/static/img/icon/chronometer.svg" size="sm" type="dark" class="w-[88px]"></lit-label>
      </div>
    </div>
  </div>

  <template x-if="selectedRound.round">
    <lit-round-result-header x-show="currentPage === 'round'" class="block xl:hidden"
      :countryCca2="selectedRound.round?.country_cca2"
      :countryName="selectedRound.round?.country_name"
      :countrySubdivisionName="selectedRound.round?.country_subdivision_name"
      :userGuess="selectedRound.userGuess ? JSON.stringify({
        countryCca2: selectedRound.userGuess?.country_cca2,
        countryName: selectedRound.userGuess?.country_name,
        countryMatch: selectedRound.userGuess?.country_match,
        countrySubdivisionMatch: selectedRound.userGuess?.country_subdivision_match,
        detailedPoints: selectedRound.userGuess?.detailed_points,
        distanceMeters: selectedRound.userGuess?.distance_meters,
        flagFilePath: selectedRound.userGuess?.flag_file_path,
        roundedPoints: selectedRound.userGuess?.rounded_points,
        rank: selectedRound.userGuess?.rank
      }) : null">
    </lit-round-result-header>
  </template>


  <div class="flex flex-1 gap-2 min-h-0 xl:p-2">
    <!-- Left Column -->
    <div class="hidden xl:flex flex-col gap-2 flex-1 min-h-0 shrink-0">
      <lit-button label="FULLSCREEN" imgPath="/static/img/icon/fullscreen.svg" type="primary" size="lg" :isSelected="isFullScreen" x-on:click="toggleFullScreen"></lit-button>

      <div class="flex flex-col flex-1 w-[280px] min-h-0">
        <lit-panel label="ROUNDS" class="w-full min-h-0">
          <div slot="header-right" class="font-heading z-20 font-semibold text-lg text-gray-50">{{ $game->number_of_rounds }}</div>
          <div class="h-full overflow-y-auto z-10">
            @include('game::result.rounds-panel')
          </div>
        </lit-panel>
      </div>
    </div>

    <!-- Middle Column -->
    <div x-ref="middleColumn" class="hidden xl:flex w-full overflow-hidden rounded border border-gray-700 bg-iris-200">
      <div x-show="isFullScreen" x-cloak class="flex flex-col w-[280px] shrink-0 z-50 border-r border-gray-700">
      
        <lit-button label="FULLSCREEN" imgPath="/static/img/icon/fullscreen.svg" type="primary" size="lg" :isSelected="isFullScreen" x-on:click="toggleFullScreen" class="m-2"></lit-button>

        <lit-panel-header2 label="ROUNDS" noBorder noRounded class="border-y border-gray-700">
          <div slot="right" class="font-heading font-semibold text-lg text-gray-50">{{ $game->number_of_rounds }}</div>
        </lit-panel-header2>
        <div class="border-b border-gray-700">
          @include('game::result.rounds-panel')
        </div>
      </div>

      <div class="flex flex-col w-full z-50">
        <template x-if="selectedRound.round" class="z-50">
          <lit-round-result-header
            :countryCca2="selectedRound.round?.country_cca2"
            :countryName="selectedRound.round?.country_name"
            :countrySubdivisionName="selectedRound.round?.country_subdivision_name"
            :userGuess="selectedRound.userGuess ? JSON.stringify({
              countryCca2: selectedRound.userGuess?.country_cca2,
              countryName: selectedRound.userGuess?.country_name,
              countryMatch: selectedRound.userGuess?.country_match,
              countrySubdivisionMatch: selectedRound.userGuess?.country_subdivision_match,
              detailedPoints: selectedRound.userGuess?.detailed_points,
              distanceMeters: selectedRound.userGuess?.distance_meters,
              flagFilePath: selectedRound.userGuess?.flag_file_path,
              roundedPoints: selectedRound.userGuess?.rounded_points,
              rank: selectedRound.userGuess?.rank
            }) : null">
          </lit-round-result-header>
        </template>

        <div class="w-full h-full relative">
          <div id="panoramaLargeScreen" class="w-full h-full absolute transition-opacity duration-500 ease-in-out"
            :class="{ 
              'opacity-0': currentMode !== 'panorama',
              'z-10': currentMode === 'panorama'
            }"
          ></div>
         
          <lit-game-round-map
            x-ref="map"
            mapStyleEnum="{{ $user->map_style_enum }}"
            mapStyleTileSize="{{ $user->map_style_tile_size }}"
            mapStyleFullUri="{{ $user->map_style_full_uri }}"
            panoramaLocationMarkerAnchor="{{ $user->map_location_marker_anchor }}"
            panoramaLocationMarkerImgPath="{{ $user->map_location_marker_img_path }}"
            :guesses="JSON.stringify(selectedRound.guesses)"
            :panoramaLat="selectedRound.panorama.lat"
            :panoramaLng="selectedRound.panorama.lng"
            class="transition-opacity duration-500 ease-in-out"
            :class="{ 'opacity-0': currentMode !== 'map'}"
          ></lit-game-round-map>

          <lit-toggle 
            leftLabel="Panorama" 
            rightLabel="Map"
            size="sm"
            class="w-48 sm:w-64 absolute bottom-2 left-2 sm:left-1/2 sm:transform sm:-translate-x-1/2 z-20"
            :isSelected="currentMode === 'map'"
            x-on:clicked="switchMode($event)">
          </lit-toggle>

          <div class="flex flex-col justify-end items-end gap-4 absolute bottom-0 w-full p-2">
            <lit-round-result-ranking-dialog-button class="z-10"
              :guesses="JSON.stringify(selectedRound.guesses)"
              :userId="user.id"
            ></lit-round-result-ranking-dialog-button>
          </div>
        </div>
      </div>
    </div>

    <!-- Right Column -->
    <div class="flex flex-col flex-1 w-[440px] min-h-0">
      <!-- Stats Panel -->
      <lit-panel label="STATS" x-show="isStatsPanelVisible" class="mt-2 mx-2 xl:mt-0 xl:mx-0">
        <!-- Rank -->
        <div class="flex flex-none w-24 sm:w-32 h-[86px] absolute -top-1 -right-1 justify-center items-center">
          <div class="flex relative left-1 z-10 items-end">
            @if($user->is_player)
              <span class="font-heading text-5xl sm:text-6xl text-gray-50 font-bold text-stroke-2 text-stroke-gray-700">{{ $user->rank }}</span>
              <span class="relative bottom-1 font-heading text-xl sm:text-2xl text-gray-50 font-bold text-stroke-2 text-stroke-gray-700">{{ GameUtil::getOrdinalSuffix($user->rank) }}</span>
            @else
              <span class="font-heading text-5xl sm:text-6xl text-gray-50 font-bold text-stroke-2 text-stroke-gray-700">-</span>
            @endif
          </div>
          <svg class="absolute w-full h-full" viewBox="0 0 123 74" fill="none" xmlns="http://www.w3.org/2000/svg" preserveAspectRatio="none">
            <g filter="url(#filter0_i_1214_13529)">
              <path d="M16.6769 3.0332C17.1208 1.25091 18.7216 0 20.5583 0H119C121.209 0 123 1.79086 123 4V70C123 72.2091 121.209 74 119 74H4.11857C1.5177 74 -0.391467 71.5569 0.237167 69.0332L16.6769 3.0332Z" fill="{{ $user->is_player ? GameUtil::getHexaColorByRank($user->rank) : '#4F576C' }}"/>
            </g>
            <path d="M20.5583 0.5H119C120.933 0.5 122.5 2.067 122.5 4V70C122.5 71.933 120.933 73.5 119 73.5H4.11857C1.84281 73.5 0.172288 71.3623 0.722342 69.154L17.1621 3.15405C17.5505 1.59454 18.9511 0.5 20.5583 0.5Z" stroke="#333847"/>
            <defs>
              <filter id="filter0_i_1214_13529" x="0.116608" y="-2" width="122.883" height="76" filterUnits="userSpaceOnUse" color-interpolation-filters="sRGB">
                <feFlood flood-opacity="0" result="BackgroundImageFix"/>
                <feBlend mode="normal" in="SourceGraphic" in2="BackgroundImageFix" result="shape"/>
                <feColorMatrix in="SourceAlpha" type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0" result="hardAlpha"/>
                <feOffset dy="-4"/>
                <feGaussianBlur stdDeviation="1"/>
                <feComposite in2="hardAlpha" operator="arithmetic" k2="-1" k3="1"/>
                <feColorMatrix type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0.4 0"/>
                <feBlend mode="normal" in2="shape" result="effect1_innerShadow_1214_13529"/>
              </filter>
            </defs>
          </svg>
        </div>
        
        <div class="flex gap-2 m-2">
          <!-- Map Marker -->
          <div
            class="flex flex-none justify-center w-[72px] h-[72px]" 
            :class="{
              'items-end': '{{ $user->map_marker_map_anchor === 'bottom' }}',
              'items-center': '{{ $user->map_marker_map_anchor === 'center' }}'
            }"
            x-cloak
          >
            <img src="{{ $user->map_marker_file_path }}" class="max-w-full max-h-full object-contain" draggable="false" />
          </div>

          <div class="flex flex-col flex-grow mr-24 sm:mr-32 justify-between">
            <span class="leading-none font-heading font-semibold text-lg text-iris-800 truncate">{{ $user->display_name }}</span>
            <div class="flex gap-2">
              <div class="flex flex-none justify-center items-center relative">
                <span class="absolute text-white font-heading text-xl font-bold text-stroke-2 text-stroke-iris-900">{{ $user->level }}</span>
                <img class="w-8" src="/static/img/icon/emblem.svg" />
              </div>
              <div class="flex flex-col items-center w-full max-w-64">
                <span class="font-heading text-sm font-semibold text-gray-800">{{ $user->current_level_experience_points }}/{{ $user->next_level_experience_points_requirement }}</span>
                <lit-progress-bar class="w-full relative bottom-0.5" percentage="{{ $user->level_percentage }}" innerBgColorClass="bg-yellow-500" tippy="{{ $user->level_percentage }}%"></lit-progress-bar>
              </div>
            </div>
          </div>
        </div>

        <!-- Stats -->
        <div class="flex justify-around m-2 p-2 rounded border border-iris-500 bg-iris-100">
          <div class="flex flex-col items-center">
            <span class="font-heading text-base font-bold text-iris-800">Points</span>
            <div class="flex justify-center items-center w-32 h-6 relative rounded bg-iris-500">
              <img src="/static/img/icon/star-gold.svg" class="w-8 aspect-auto absolute -top-[6px] left-0 transform -translate-x-1/2" />
              <span class="font-heading text-lg font-bold text-white text-stroke-2 text-stroke-iris-900" tippy="{{ $user->detailed_points }}">{{ $user->rounded_points}}</span>
            </div>
          </div>
          <div class="flex flex-col items-center">
            <span class="font-heading text-base font-bold text-iris-800">Experience</span>
            <div class="flex justify-center items-center w-32 h-6 relative rounded bg-iris-500">
              <div class="flex justify-center items-center absolute left-0 transform -translate-x-1/2">
                <span class="absolute text-white font-heading text-xl font-bold text-stroke-2 text-stroke-iris-900">XP</span>
                <img class="w-8" src="/static/img/icon/emblem.svg" />
              </div>
              <span class="font-heading text-lg font-bold text-white text-stroke-2 text-stroke-iris-900">+{{ $game->experience_points }}</span>
            </div>
          </div>
        </div>
      </lit-panel>

      <!-- Expanded Bloc To Bottom -->
      <div x-ref="expandedBloc" class="flex flex-col flex-1 min-h-0">
        <lit-panel label="RANKING" x-show="isRankingPanelVisible" class="overflow-y-auto gap-2 mt-2 mx-2 xl:mx-0">
          <div slot="header-right" class="font-heading font-semibold text-lg text-gray-50">{{ count($players) }} players</div>
          <div class="flex flex-col gap-2 p-2">
            @foreach ($players as $player)
              <lit-player-result-item
                countryCCA2="{{ $player->country_cca2 }}"
                detailedPoints="{{ $player->detailed_points }}"
                flagDescription="{{ $player->flag_description }}"
                flagFilePath="{{ $player->flag_file_path }}"
                iconPath="{{ $player->map_marker_file_path }}"
                level="{{ $player->level }}"
                name="{{ $player->display_name }}"
                rank="{{ $player->rank }}"
                rankIconType="cup"
                rankSelected="{{ $player->rank === $user->rank ? $player->rank : '' }}"
                roundedPoints="{{ $player->rounded_points }}"
                userTitle="{{ $player->title }}">
              </lit-player-result-item>
            @endforeach
          </div>
        </lit-panel>
      
        <div x-data x-show="isPanoramaVisible"  class="w-full h-full relative bg-gray-600">
          <div 
            id="panoramaSmallScreen" 
            class="w-full h-full absolute transition-opacity duration-500 ease-in-out"
            :class="{ 
              'opacity-0': currentMode !== 'panorama',
              'z-10': currentMode === 'panorama'
            }">
          </div>
          
          <lit-game-round-map
            x-ref="map"
            mapStyleEnum="{{ $user->map_style_enum }}"
            mapStyleTileSize="{{ $user->map_style_tile_size }}"
            mapStyleFullUri="{{ $user->map_style_full_uri }}"
            panoramaLocationMarkerAnchor="{{ $user->map_location_marker_anchor }}"
            panoramaLocationMarkerImgPath="{{ $user->map_location_marker_img_path }}"
            :guesses="JSON.stringify(selectedRound.guesses)"
            :panoramaLat="selectedRound.panorama.lat"
            :panoramaLng="selectedRound.panorama.lng"
            class="transition-opacity duration-500 ease-in-out"
            :class="{ 'opacity-0': currentMode !== 'map'}">
          </lit-game-round-map>

          <lit-toggle 
            leftLabel="Panorama" 
            rightLabel="Map"
            size="sm"
            class="block xl:hidden w-48 sm:w-64 absolute bottom-2 left-2 sm:left-1/2 sm:transform sm:-translate-x-1/2 z-20"
            :isSelected="currentMode === 'map'"
            x-on:clicked="switchMode($event)">
          </lit-toggle>

          <div class="flex xl:hidden justify-end gap-2 absolute bottom-0 w-full p-2">
            <lit-round-result-ranking-dialog-button class="z-10"
              :guesses="JSON.stringify(selectedRound.guesses)"
              :userId="user.id"
            ></lit-round-result-ranking-dialog-button>
          </div>
        </div>
      </div>
    </div>
  </div>
  
  <lit-round-list 
    rounds="{{ json_encode($rounds) }}"
    totalRoundCount="{{ count($rounds) }}"
    :selectedRoundNumber="selectedRoundNumberSmallScreen"
    class="block xl:hidden border-t border-gray-700 bg-iris-500"
    roundClickable
    x-on:clicked="selectRound($event.detail.roundNumber)"
  ></lit-round-list>
</div>

<script>
  document.addEventListener('alpine:init', () => {
    Alpine.data('state', (isDev) => ({
      // Constants
      MIN_WIDTH_LARGE_SCREEN_PX: 1280,

      // Data Properties
      game: @json($game),
      rounds: @json($rounds),
      user: @json($user),
      gameResultElWidthPx: 0,
      isFullScreen: false,
      panoramaLng: null,
      panoramaLat: null,
      currentPage: 'game',
      currentMode: 'panorama',
      viewerSmallScreen: null,
      viewerLargeScreen: null,

      // Selected Round Data
      selectedRound: {
        guesses: [],
        number: null,
        panorama: {
          lat: null,
          lng: null
        },
        round: null,
        user: @json($user),
        get userGuess() {
          return this.guesses.find(guess => guess.user_id === this.user.id);
        }
      },
      selectedRoundNumberSmallScreen: null,
      selectedRoundNumberLargeScreen: null,

      // Computed Properties
      get isSmallScreen() {
        return this.gameResultWidthPx < this.MIN_WIDTH_LARGE_SCREEN_PX;
      },
      get isLargeScreen() {
        return this.gameResultWidthPx >= this.MIN_WIDTH_LARGE_SCREEN_PX;
      },
      get isPanoramaVisible() {
        return this.isSmallScreen && this.selectedRoundNumberSmallScreen !== null;
      },
      get isRankingPanelVisible() {
        return !(this.pageSize === 'small' && this.currentPage === 'round');
      },
      get isStatsPanelVisible() {
        return !(this.pageSize === 'small' && this.currentPage === 'round');
      },
      get pageSize() {
        return this.isSmallScreen ? 'small' : 'large';
      },

      // Navigation Methods
      navigateToHome() {
        window.location.href = '/';
      },
      navigateToGame() {
        this.currentPage = 'game';
        this.selectedRoundNumberSmallScreen = null;
      },

      // Fullscreen Methods
      enterFullScreen() {
        this.isFullScreen = true;
        this.$refs.middleColumn.requestFullscreen();
      },
      exitFullScreen() {
        this.isFullScreen = false;
        document.exitFullscreen();
      },
      toggleFullScreen() {
        this.isFullScreen ? this.exitFullScreen() : this.enterFullScreen();
      },

      // Mode Switch Method
      switchMode(event) {
        this.currentMode = event.detail.isSelected ? 'map' : 'panorama';
      },

      // Data Fetching Methods
      async fetchRoundData(gameId, roundNumber) {
        if (isDev) {
          return {
            guesses: [],
            panorama: {
              url: 'https://pannellum.org/images/alma.jpg',
              heading: 0,
              pitch: 0,
              field_of_view: 100,
            }
          };
        }
        const response = await fetch(`/web-api/game/${gameId}/round/${roundNumber}`, {
          method: 'GET',
          headers: {
            'Content-Type': 'application/json',
          },
        });
        return response.json();
      },
   
      // Round Selection Methods
      async selectRoundForViewer(roundNumber, viewer, screenType) {
        if (screenType === 'small') {
            this.selectedRoundNumberSmallScreen = roundNumber;
        } else if (screenType === 'large') {
            this.selectedRoundNumberLargeScreen = roundNumber;
        }

        const selectedRoundData = await this.fetchRoundData(this.game.id, roundNumber);

        this.selectedRound.guesses = selectedRoundData.guesses;
        this.selectedRound.panorama.lat = selectedRoundData.panorama.lat;
        this.selectedRound.panorama.lng = selectedRoundData.panorama.lng;
        this.selectedRound.round = selectedRoundData.round;

        this.updateViewerScene(viewer, roundNumber, selectedRoundData);
      },
      {{-- async selectRoundForSmallScreen(roundNumber) {
        this.currentPage = 'round';
        await this.selectRoundForViewer(roundNumber, this.viewerSmallScreen, 'small');
      }, --}}
      async selectRoundForLargeScreen(roundNumber) {
        await this.selectRoundForViewer(roundNumber, this.viewerLargeScreen, 'large');
      },
      async selectRound(roundNumber) {
        this.currentPage = 'round';
        const selectedRoundData = await this.fetchRoundData(this.game.id, roundNumber);

        this.selectedRound.guesses = selectedRoundData.guesses;
        this.selectedRoundNumberSmallScreen = roundNumber;
        this.selectedRoundNumberLargeScreen = roundNumber;
        this.selectedRound.panorama.lat = selectedRoundData.panorama.lat;
        this.selectedRound.panorama.lng = selectedRoundData.panorama.lng;
        this.selectedRound.round = selectedRoundData.round;

        [this.viewerSmallScreen, this.viewerLargeScreen].forEach(viewer => {
          if (viewer) {
            this.updateViewerScene(viewer, roundNumber, selectedRoundData);
          }
        });
      },

      // Viewer Methods
      updateViewerScene(viewer, roundNumber, roundData) {
        const currentSceneId = viewer.getScene();
        viewer.addScene(roundNumber, {
          panorama: roundData.panorama.url,
          yaw: roundData.panorama.heading,
          pitch: roundData.panorama.pitch,
          hfov: roundData.panorama.field_of_view
        });
        viewer.loadScene(roundNumber);
        if (currentSceneId) {
          viewer.removeScene(currentSceneId);
        }
      },

      init() {
        if (this.userRank === 1) {
          setTimeout(() => {
            window.confetti({ particleCount: 150 });
          }, 500);
        }

        this.viewerSmallScreen = pannellum.viewer('panoramaSmallScreen', {
          type: "equirectangular",
          autoLoad: true,
          showControls: false,
          minHfov: window.innerWidth < 1000 ? 30 : 50,
          scenes: {}
        });
        this.viewerLargeScreen = pannellum.viewer('panoramaLargeScreen', {
          type: "equirectangular",
          autoLoad: true,
          showControls: false,
          minHfov: window.innerWidth < 1000 ? 30 : 50,
          scenes: {}
        });

        this.selectRoundForLargeScreen(1).catch(err => console.error(err));

        const gameResultElement = this.$refs.gameResult;
        this.gameResultWidthPx = gameResultElement.offsetWidth;
        window.addEventListener('resize', () => {
          this.gameResultWidthPx = gameResultElement.offsetWidth;
        });

        document.addEventListener('fullscreenchange', () => {
          if (!document.fullscreenElement) {
            this.isFullScreen = false;
          }
        });
      }
    }));
  });
</script>