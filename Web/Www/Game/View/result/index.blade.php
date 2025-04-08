<?php declare(strict_types=1); 
  use Web\Www\Game\Util\GameUtil;
?>

<div x-ref="gameResult" x-data="state({{ $is_dev ?? null }})" class="flex flex-col h-screen select-none">
  <!-- Page Header -->
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
        <lit-label label="{{ $game->type === 'template' ? 'TEMPLATE' : 'CLASSIC' }}" size="sm" color="gray" class="w-[88px]"></lit-label>
        <lit-label label="{{ $game->total_game_time_mn }} MN" iconPath="/static/img/icon/chronometer.svg" size="sm" color="gray" class="w-[88px]"></lit-label>
      </div>
    </div>
  </div>

  <!-- Main -->
  <div class="flex flex-col flex-1 relative min-h-0 bg-blue-200">
    <!-- Small Layout -->
    <div class="flex flex-col w-full h-full xl:hidden min-h-0">
      <!-- Game Page -->
      <div x-show="currentPage === 'game'" class="flex flex-col flex-1 min-h-0">
        <!-- Stats Panel -->
        <div class="mt-2 mx-2">
          @include('game::result.components.stats-panel')
        </div>

        <div x-ref="expandedBloc" class="flex flex-col flex-1 min-h-0">
          <!-- Ranking Panel -->
          <div class="overflow-y-auto gap-2 my-2 mx-2">
            @include('game::result.components.ranking-panel')
          </div>
        </div>
      </div>

      <!-- Round Page -->
      <div x-show="currentPage === 'round'" class="w-full h-full">
        <div class="flex w-full h-full relative overflow-hidden bg-iris-200">
          <!-- Header -->
          <template x-if="selectedRound.round">
            @include('game::result.components.round-header')
          </template>

          <!-- Panorama -->
          <div class="absolute top-0 left-0 w-full h-full transition-opacity duration-300 ease-in-out" :class="{
            'opacity-100 pointer-events-auto': currentMode === 'panorama',
            'opacity-0 pointer-events-none': currentMode !== 'panorama'
          }">
            <div id="panoramaSmallScreen"></div>
          </div>

          <!-- Map -->
          @include('game::result.components.round-map')
          
          <!-- Panorama/Map Toggle -->
          <lit-toggle 
            leftLabel="Panorama" 
            rightLabel="Map"
            size="sm"
            class="w-48 sm:w-64 absolute bottom-2 left-2 sm:left-1/2 sm:transform sm:-translate-x-1/2 z-20"
            :isSelected="currentMode === 'map'"
            x-on:clicked="switchMode($event)">
          </lit-toggle>
        </div>
      </div>
    </div>

    <!-- Large Layout -->
    <div class="hidden xl:flex w-full h-full gap-2 p-2">
      <!-- Left And Middle Columns -->
      <div x-ref="largeScreenLeftAndMiddleColumn" class="flex w-full gap-2 bg-iris-200" :class="{ 'p-2': isFullScreen }">
        <!-- Left Column -->
        <div class="flex flex-col shrink-0 gap-2">
          <lit-button label="FULLSCREEN" imgPath="/static/img/icon/fullscreen.svg" type="primary" size="lg" :isSelected="isFullScreen" x-on:click="toggleFullScreen"></lit-button>

          <div class="flex flex-col flex-1 w-[280px] min-h-0">
            <lit-panel label="ROUNDS" class="w-full min-h-0">
              <div slot="header-right" class="font-heading z-20 font-semibold text-lg text-gray-50">{{ $game->number_of_rounds }}</div>
              <div class="h-full overflow-y-auto z-10">
                <div class="bg-gray-50 select-none z-10">
                  @foreach ($rounds as $round)
                    <div class="flex flex-col p-2 bg-gray-50 relative" :class="{ 
                      'bg-iris-300': '{{ $round->number }}' == selectedRoundNumberLargeScreen,
                      'hover:bg-iris-100 cursor-pointer': '{{ $round->number }}' != selectedRoundNumberLargeScreen
                      }" x-on:click="selectedRoundNumberLargeScreen !== {{ $round->number }} && selectRound({{ $round->number }})">
                      <div class="flex justify-between items-center relative">
                        <div class="flex gap-1">
                          <span class="font-heading font-medium text-lg text-gray-500">{{ $round->number }}.</span>
                          <span class="font-heading font-medium text-lg text-gray-800">{{ $round->country_name }}</span>
                        </div>
                        <div class="flex relative items-center place-self-start">
                          <div class="relative">
                            <img 
                              src="/static/img/ui/ribbon-rank-{{ 
                                $round->user_rank == 1 ? 'gold' : 
                                ($round->user_rank == 2 ? 'silver' : 
                                ($round->user_rank == 3 ? 'bronze' : 'default')) 
                              }}.svg" 
                              class="h-6" 
                              alt="Rank Ribbon"
                            />
                            <div class="absolute inset-0 top-px flex gap-px justify-center items-center">
                              <span class="z-10 font-heading font-semibold text-2xl text-gray-0 text-stroke-2 text-stroke-gray-700">{{ $round->user_rank }}</span>
                              <span class="relative top-0.5 z-10 font-heading font-semibold text-sm text-gray-0 text-stroke-2 text-stroke-gray-700">
                                {{ $round->user_rank === null ? '-' : GameUtil::getOrdinalSuffix($round->user_rank) }}
                              </span>
                            </div>
                          </div>
                        </div>
                      </div>
                      <div class="flex gap-2">
                        <img src="/static/flag/wavy/{{ strtolower($round->country_cca2) }}.png" alt="Flag of {{ $round->country_name }}" draggable="false" class="h-16" />
                        <div class="flex flex-col gap-1">
                          <div class="flex items-center gap-1 w-[148px]">
                            <img src="/static/img/icon/medal-gray-gold.svg" alt="Gold Cup" class="h-5"/>
                            <span class="text-sm text-gray-700 truncate {{ $round->rank1_player_display_name === $user->display_name ? 'font-bold' : ''}}">{{ $round->rank1_player_display_name }}</span>
                          </div>
                          <div class="flex items-center gap-1 w-[148px]">
                            <img src="/static/img/icon/medal-gray-silver.svg" alt="Silver Cup" class="h-5"/>
                            <span class="text-sm text-gray-700 truncate {{ $round->rank2_player_display_name === $user->display_name ? 'font-bold' : ''}}">{{ $round->rank2_player_display_name }} </span>
                          </div>
                          <div class="flex items-center gap-1 w-[148px]">
                            <img src="/static/img/icon/medal-gray-bronze.svg" alt="Bronze Cup" class="h-5"/>
                            <span class="text-sm text-gray-700 truncate {{ $round->rank2_player_display_name === $user->display_name ? 'font-bold' : ''}}">{{ $round->rank2_player_display_name }}</span>
                          </div>
                        </div>
                      </div>
                    </div>
                    @if (!$loop->last)
                      <div class="w-full h-px bg-gray-100"></div>
                    @endif
                  @endforeach
                </div>
              </div>
            </lit-panel>
          </div>
        </div>

        <!-- Middle Column -->
        <div class="flex w-full">
          <div class="flex w-full relative overflow-hidden rounded-sm border border-gray-700 bg-iris-200">
            <!-- Header -->
            <template x-if="selectedRound.round">
              @include('game::result.components.round-header')
            </template>

            <!-- Panorama -->
            <div class="absolute top-0 left-0 w-full h-full transition-opacity duration-300 ease-in-out" :class="{
              'opacity-100 pointer-events-auto': currentMode === 'panorama',
              'opacity-0 pointer-events-none': currentMode !== 'panorama'
            }">
              <div id="panoramaLargeScreen"></div>
            </div>

            <!-- Map -->
            @include('game::result.components.round-map')
            
            <!-- Panorama/Map Toggle -->
            <lit-toggle 
              leftLabel="Panorama" 
              rightLabel="Map"
              size="sm"
              class="w-48 sm:w-64 absolute bottom-2 left-2 sm:left-1/2 sm:transform sm:-translate-x-1/2 z-20"
              :isSelected="currentMode === 'map'"
              x-on:clicked="switchMode($event)">
            </lit-toggle>
          </div>
        </div>
      </div>

      <!-- Right Column -->
      <div class="flex flex-col w-[440px]">
        <!-- Stats Panel -->
        @include('game::result.components.stats-panel')

        <div x-ref="expandedBloc" class="flex flex-col flex-1 min-h-0">
          <!-- Ranking Panel -->
          <div class="overflow-y-auto mt-2">
            @include('game::result.components.ranking-panel')
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Footer -->
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
        this.$refs.largeScreenLeftAndMiddleColumn.requestFullscreen();
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