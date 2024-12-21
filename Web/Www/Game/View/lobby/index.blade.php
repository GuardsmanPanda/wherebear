<?php declare(strict_types=1); ?>

<div x-data="state('{{ $user->id }}')" class="flex flex-col max-w-5xl h-screen mx-auto lg:border-x border-gray-700 select-none">
  <!-- Header -->
  <div x-ref="header" class="flex h-14 shrink-0 justify-between items-center px-2 border-b border-gray-700 bg-iris-500">
    <div class="flex w-16">
      @if($user->is_host)
        <lit-button imgPath="/static/img/icon/cross.svg" size="md" bgColorClass="bg-red-500" x-on:clicked="openConfirmDeleteGameDialog"></lit-button>
        <lit-confirm-dialog x-ref="confirmDeleteGameDialog" 
          label="Delete the game"
          message="Are you sure you want to delete the game?"
          cancelBtnText="No, Cancel"
          confirmBtnText="Yes, Delete"
          confirmBtnBgColorClass="bg-poppy-400"
          x-on:confirmed="deleteGame">
        </lit-confirm-dialog>
      @else
        <lit-button imgPath="/static/img/icon/arrow-back.svg" size="md" bgColorClass="bg-gray-400" x-on:click="leave"></lit-button>
      @endif
    </div>
    <div class="flex flex-col flex-1 items-center">
      <span x-text="gameStageText" class="font-heading text-lg text-gray-0 font-bold text-stroke-2 text-stroke-iris-800 leading-none"></span>
      <div class="font-heading text-base text-gray-0 font-medium text-stroke-1 text-stroke-iris-800">
        <span x-text="readyEntrantCount"></span>/<span x-text="gameUserCount"></span> players ready
      </div>
    </div>
    <div class="flex justify-end w-24">
      <div class="block md:hidden w-full">
        <lit-button label="Wait" size="md" bgColorClass="bg-gray-500" x-on:click="toggleIsReady(false)" x-show="user.is_ready"></lit-button>
        <lit-button label="Ready" size="md" bgColorClass="bg-pistachio-400" x-on:click="toggleIsReady(true)" x-show="!user.is_ready"></lit-button>
      </div>
    </div>
  </div>
  
  <!-- Main Content Area -->
  <div x-ref="layout" class="flex w-full h-full overflow-hidden">
    <!-- Left Column -->
    <div class="flex flex-col w-full h-full bg-iris-100">
      <!-- Scrollable Content -->
      <div class="flex flex-col w-full md:h-full overflow-y-auto">
        <lit-panel label="PROFILE" class="mt-2 sm:mt-4 mx-2 sm:mx-4">
          <div slot="header-right">
            @if($user->can_observe)
            <div slot="right" class="flex md:hidden items-center gap-1">
              <span class="font-heading font-semibold text-sm text-gray-0 whitespace-nowrap">Observer Mode</span>
              <lit-toggle size="xs" leftLabel="Off" rightLabel="On" :isSelected="user.is_observer" x-on:clicked="toggleIsObserver($event.detail.isSelected)" class="w-20"></lit-toggle>
            </div>
            @endif
          </div>
          <div class="py-2">
            <div class="flex gap-2 mx-2">
              <div class="flex flex-none justify-center w-[72px] h-[72px]" :class="{ 'items-end': user.map_marker_map_anchor === 'bottom', 'items-center': user.map_marker_map_anchor === 'center' }">
                <img :src="user.map_marker_file_path" class="max-w-full max-h-full object-contain cursor-pointer" draggable="false" x-on:click="openSelectMapMarkerDialog" />
              </div>

              <lit-select-map-marker-dialog x-ref="selectMapMarkerDialog"></lit-select-map-marker-dialog>

              <div class="flex flex-col justify-between w-full overflow-hidden">
                <div class="flex flex-col">
                  <div class="flex items-center gap-2">
                    <span x-text="user.display_name" class="leading-none font-heading font-semibold text-lg text-iris-800 truncate"></span>
                    <img src="/static/img/icon/edit-pen.svg" class="h-6 hover:brightness-90 cursor-pointer" draggable="false" x-on:click="openSelectUserProfileDialog" />
                    <lit-select-user-profile-dialog x-ref="selectUserProfileDialog" :displayName="user.display_name" :selectedCountryFlag="user.country_cca2"></lit-select-user-profile-dialog>
                  </div>
                  <span class="relative bottom-0.5 font-heading font-semibold text-sm text-gray-800">{{ $user->title }}</span>
                </div>
                <div class="flex">
                  @if(!$user->is_guest)
                    <lit-button 
                      :label="lowercaseMapStyleShortName"
                      size="xs"
                      :bgColorClass="user.map_style_enum === 'SATELLITE' ? 'bg-red-500' : 'bg-iris-500'"
                      contentAlignment="left"
                      imgPath="/static/img/icon/map.svg"
                      class="w-[100px]"
                      x-on:clicked="openSelectMapStyleDialog">
                    </lit-button>
                    <lit-select-map-style-dialog x-ref="selectMapStyleDialog" :selectedMapStyleEnum="user.map_style_enum" :userLevel="user.level"></lit-select-map-style-dialog>
                  @endif
                </div>
              </div>

              <lit-level-emblem level="{{ $user->level }}" size="lg" class="ml-4" /></lit-level-emblem>
            </div>

            <div class="flex items-center gap-1 mt-2 mx-2">
              <div class="w-full h-[1px] border-t border-iris-800"></div>
              <span class="font-heading font-bold text-sm text-iris-800">Experience</span>
              <div class="w-full h-[1px] border-t border-iris-800"></div>
            </div>

            <div class="flex mt-1 mx-2">
              <div class="flex flex-col gap-0.5 w-full">
                <div class="flex justify-between w-full">
                  <span class="font-heading font-bold text-sm text-iris-800">Next Level</span>
                  <span class="font-heading font-bold text-sm text-iris-800">{{ $user->display_level_percentage }}%</span>
                  <span class="font-heading font-bold text-sm text-iris-800">{{ $user->current_level_experience_points }}/{{ $user->next_level_experience_points_requirement }} XP</span>
                </div>
                <lit-progress-bar percentage="{{ $user->display_level_percentage }}" innerBgColorClass="bg-yellow-400" />
              </div>
            </div>
          </div>
        </lit-panel>

        <lit-panel label="GAME SETTINGS" class="my-2 sm:my-4 mx-2 sm:mx-4">
          @if($user->is_host)
            <lit-label slot="header-left" :label="game.is_public ? 'PUBLIC' : 'PRIVATE'" size="xs" :bgColorClass="game.is_public ? 'bg-pistachio-400' : 'bg-red-500'" widthClass="w-16"></lit-label>
            <div slot="header-right" class="flex items-center gap-1">
              <lit-button label="Edit" size="xs" bgColorClass="bg-gray-400" class="w-16" x-on:click="openEditGameSettingsDialog"></lit-button>
              <lit-edit-game-settings-dialog 
                x-ref="editGameSettingsDialog" 
                :gameId="game.id" 
                :gamePublicStatusEnum="game.game_public_status_enum" 
                :gameType="game.type"
                :roundCount="game.number_of_rounds" 
                :roundDurationSec="game.round_duration_seconds" 
                :roundResultDurationSec="game.round_result_duration_seconds" 
                :isBob="isBob">
              </lit-edit-game-settings-dialog>

              <lit-button label="Start" size="xs" bgColorClass="bg-iris-400" class="w-16" hx-post="/web-api/game/{{$game->id}}/force-start" hx-swap="none" hx-confirm="Confirm that you wish to START the game?"></lit-button>
            </div>
          @else
           <lit-label slot="header-right" :label="game.is_public ? 'PUBLIC' : 'PRIVATE'" size="xs" :bgColorClass="game.is_public ? 'bg-pistachio-400' : 'bg-red-500'" widthClass="w-16"></lit-label>
          @endif

          <div class="flex flex-col py-2">
            <div class="flex justify-between gap-2 w-full">
              <div class="flex flex-col w-full max-w-64 ml-2 overflow-hidden">
                <span class="font-heading font-bold text-sm text-iris-800">Name</span>
                <div class="flex items-center w-full h-8 px-2 rounded border border-iris-300 bg-iris-200">
                  <span class="text-xs text-gray-800 truncate select-all">{{ $game->name }}</span>
                </div>
              </div>
              <div class="flex flex-col w-full max-w-64 mr-2 overflow-hidden">
                <span class="font-heading font-bold text-sm text-iris-800">Invite Link</span>
                <div x-data="gameTinyUrlState" class="flex h-8">
                  <div class="flex items-center w-full h-full px-2 rounded-l border border-r-0 border-iris-300 bg-iris-200 truncate">
                    <span x-ref="url" class="text-xs text-gray-800 truncate select-all">{{ config(key: 'app.url') }}/g/{{ $game->short_code }}</span>
                  </div>
                  <div x-ref="clipboardIcon" class="w-8 shrink-0 rounded-r border border-iris-300 py-0.5 bg-iris-400 hover:bg-iris-500 cursor-pointer" x-on:click="copyUrlToClipboard()">
                    <img src="/static/img/icon/copy.svg" class="w-full h-full hover:brightness-90" />
                  </div>
                </div>
              </div>
            </div>

            <div class="flex items-center gap-1 mt-2 mx-2">
              <div class="w-full h-[1px] border-t border-iris-800"></div>
              <span class="font-heading font-bold text-sm text-iris-800">Duration</span>
              <div class="w-full h-[1px] border-t border-iris-800"></div>
            </div>

            <div class="flex justify-between gap-2 mx-2">
              <div class="flex flex-col w-full">
                <div class="flex justify-center items-center w-full h-6 rounded-t border border-gray-700 bg-gray-600">
                  <span class="font-heading font-medium text-sm text-gray-0">Rounds</span>
                </div>
                <div class="flex justify-center items-center w-full h-6 rounded-b border border-t-0 border-gray-700 bg-gray-50">
                  <span x-text="game.number_of_rounds" class="font-heading font-semibold text-base text-iris-950"></span>
                </div>
              </div>
              <div class="flex flex-col w-full">
                <div class="flex justify-center items-center w-full h-6 rounded-t border border-gray-700 bg-gray-600">
                  <span class="font-heading font-medium text-sm text-gray-0">Guessing Time</span>
                </div>
                <div class="flex justify-center items-center w-full h-6 rounded-b border border-t-0 border-gray-700 bg-gray-50">
                  <span x-text="`${game.round_duration_seconds}s`" class="font-heading font-semibold text-base text-iris-950"></span>
                </div>
              </div>
              <div class="flex flex-col w-full">
                <div class="flex justify-center items-center w-full h-6 rounded-t border border-gray-700 bg-gray-600">
                  <span class="font-heading font-medium text-sm text-gray-0">Result Time</span>
                </div>
                <div class="flex justify-center items-center w-full h-6 rounded-b border border-t-0 border-gray-700 bg-gray-50">
                  <span x-text="`${game.round_result_duration_seconds}s`" class="font-heading font-semibold text-base text-iris-950"></span>
                </div>
              </div>
            </div>

            <div class="block md:hidden mt-2 mx-2">
              @include('game::lobby.total-game-time')
            </div>
          </div>
        </lit-panel>
      </div>
      <!-- Expandable Panel -->
      <div x-ref="gameUsersSubstitute" class="hidden flex-1 min-h-[150px]"></div>
      <div x-ref="gameUsers" data-state="collapsed" class="flex flex-col md:hidden flex-1 min-h-[150px] z-10 px-2 bg-iris-500 border border-t-2 border-b-0 border-gray-700 rounded-t-2xl transition-[height] duration-700 ease-in-out">
        <div class="flex justify-between items-center py-2 border-b border-gray-50 cursor-pointer" x-on:click="togglePlayerListSize">
          <div></div>
          <div class="font-heading font-bold text-base text-gray-0 text-stroke-2 text-stroke-iris-800">PLAYERS</div>
          <div x-ref="gameUsersToggleSizeIcon" class="w-4 mr-4 transition-transform duration-1000 ease-in-out">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="#EFF1F4" class="size-6">
              <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 15.75 7.5-7.5 7.5 7.5" />
            </svg>
          </div>
        </div>
        <div x-ref="gameUserList" class="py-2">
          <div class="grid grid-cols-3 min-[420px]:grid-cols-4 min-[520px]:grid-cols-5 min-[622px]:grid-cols-6 gap-4">
            <template x-for="gameUser in gameUserList">
              <div class="flex flex-col w-20 justify-center items-center">
                <div class="flex w-full h-4 justify-center items-center px-0.5 rounded-t border border-b-0 border-gray-700 bg-gray-600" :tippy="gameUser.display_name">
                  <span x-text="gameUser.display_name" class="font-heading font-medium text-xs text-gray-0 truncate"></span>
                </div>
                <div class="flex flex-none justify-center w-20 h-16 relative border-x border-gray-700 bg-gradient-to-t"
                  :class="{ 
                    'items-center': gameUser.map_marker_map_anchor === 'center',
                    'items-end': gameUser.map_marker_map_anchor === 'bottom',
                    'from-gray-200 to-gray-300': !gameUser.is_ready,
                    'from-pistachio-300 to-pistachio-500': gameUser.is_ready
                  }">
                  
                  <img x-show="gameUser.is_observer" src="/static/img/icon/eye.svg" class="h-4 absolute -left-1 -top-0.5" />
                  <img src="/static/img/icon/check-green.svg" draggable="false" class="h-6 absolute -top-2 -right-2 z-10 transform-[opacity] duration-100" :class="{ 'opacity-100': gameUser.is_ready, 'opacity-0': !gameUser.is_ready }" />
                  <img :src="gameUser.map_marker_file_path" draggable="false" class="max-w-[72px]" :class="{
                    'max-h-14': gameUser.map_marker_map_anchor === 'center',
                    'max-h-16 relative top-[4px]': gameUser.map_marker_map_anchor === 'bottom'
                  }" />
                </div>
                <div class="flex flex-none w-full h-[10px] relative rounded-b border border-gray-700 bg-gray-600">
                  <lit-flag :cca2="gameUser.country_cca2" :filePath="gameUser.flag_file_path" :description="gameUser.flag_description" roundedClass="rounded-sm" class="absolute bottom-[2px] right-[3px] h-4" draggable="false"></lit-flag>
                  <lit-level-emblem :level="gameUser.level" size="xs" class="absolute -left-[4px] -bottom-[3px]"></lit-level-emblem>
                </div>
              </div>
            </template>
          </div>
        </div>
      </div> 
      <div class="hidden md:block">
        @include('game::lobby.total-game-time')
      </div>
    </div>
    <!-- Right Column -->
    <div class="hidden md:flex flex-col shrink-0 w-[320px] h-full overflow-hidden z-10 border-l border-gray-700"
      style="box-shadow: -2px 0 2px rgba(0, 0, 0, 0.25)">
      <lit-panel-header2 label="STATUS" noBorder noRounded class="border-b border-gray-700">
        <lit-label slot="right" :label="user.is_ready ? 'Ready' : 'Pending...'" size="sm" :bgColorClass="user.is_ready ? 'bg-pistachio-400' : 'bg-gray-500'" widthClass="w-24"></lit-label>
      </lit-panel-header2>
      <div class="flex justify-center">
        @if($user->can_observe)
          <div class="flex flex-col justify-between items-center w-[168px] h-full p-2 pt-1 border-r" :class="{ 'border-gray-300 bg-gray-100': !user.is_observer, 'border-iris-500 bg-iris-300': user.is_observer }">
            <span class="font-heading font-semibold text-sm" :class="{ 'text-iris-800': !user.is_observer, 'text-gray-900': user.is_observer }">Observer Mode</span>
            <lit-toggle size="sm" leftLabel="Off" rightLabel="On" :isSelected="user.is_observer" x-on:clicked="toggleIsObserver($event.detail.isSelected)" class="w-full"></lit-toggle>
        </div>
        @endif
        <div class="w-full bg-iris-300 p-2">
          <lit-button label="Wait" size="lg" bgColorClass="bg-gray-400" x-on:click="toggleIsReady(false)" x-show="user.is_ready" class="w-48"></lit-button>
          <lit-button label="Ready" size="lg" bgColorClass="bg-pistachio-500" x-on:click="toggleIsReady(true)" x-show="!user.is_ready" class="w-48"></lit-button>
        </div>
      </div>

      <lit-panel-header2 label="PLAYERS" noBorder noRounded class="border-y border-gray-700">
        <div slot="right" class="flex items-center gap-2">
          <lit-label :label="`${playerCount} player${playerCount > 1 ? 's' : ''}`" size="sm" type="primary" class="w-20"></lit-label>
          <lit-label x-show="observerCount > 0" :label="`${observerCount} observer${observerCount > 1 ? 's' : ''}`" size="sm" bgColorClass="bg-iris-300" class="w-24"></lit-label>
        </div>
      </lit-panel-header2>
      <div class="h-full overflow-y-auto bg-iris-300">
        <template x-for="gameUser in gameUserList">
          <div class="flex gap-2 relative overflow-hidden p-2 border-b border-gray-300 bg-gradient-to-t" :class="{ 
            'from-pistachio-400 to-pistachio-500': gameUser.is_ready, 
            'from-gray-50 to-gray-100': !gameUser.is_ready 
            }">
            <div x-show="gameUser.is_host" class="flex justify-center items-center w-16 h-4 absolute top-2 -left-4 border border-gray-700 -rotate-45 bg-gradient-to-t from-yellow-300 to-yellow-400">
              <span class="font-heading font-semibold text-xs text-gray-0 text-stroke-2 text-stroke-gray-700">HOST</span>
            </div>

            <div class="flex flex-none justify-center w-16 h-16" :class="{ 'items-end': gameUser.map_marker_map_anchor === 'bottom', 'items-center': gameUser.map_marker_map_anchor === 'center' }">
              <img :src="gameUser.map_marker_file_path" 
                class="max-w-full max-h-full object-contain"
                draggable="false" 
              />
            </div>
            <div class="flex flex-col gap-[10px] w-full overflow-hidden">
              <div class="flex flex-col w-full">
                <span x-text="gameUser.display_name" class="leading-none font-heading font-semibold text-lg truncate" 
                  :class="{ 'text-iris-800': !gameUser.is_ready, 'text-gray-0 text-stroke-2 text-stroke-pistachio-900': gameUser.is_ready }">
                </span>
                <span x-text="gameUser.title" class="leading-none font-heading font-medium text-sm text-gray-800 truncate"></span>
              </div>
              
              <div class="flex gap-2">
                <lit-label x-show="!gameUser.is_observer"
                  :label="gameUser.map_style_short_name"
                  size="xs"
                  :type="gameUser.map_style_enum === 'SATELLITE' ? 'error' : 'dark'"
                  iconPath="/static/img/icon/map.svg"
                  class="w-[80px] ml-1">
                </lit-label>

                 <lit-label x-show="gameUser.is_observer"
                  label="Observer"
                  size="xs"
                  bgColorClass="bg-iris-300"
                  iconPath="/static/img/icon/eye.svg"
                  class="ml-1">
                </lit-label>
              </div>
            </div>
            <div class="flex flex-col justify-between items-end">
              <div class="flex items-center w-8 h-6">
                <lit-flag :cca2="gameUser.country_cca2" :filePath="gameUser.flag_file_path" :description="gameUser.flag_description" roundedClass="rounded-sm" maxHeightClass="max-h-6" class="w-8" draggable="false"></lit-flag>
              </div>
              <lit-level-emblem :level="gameUser.level" size="sm"></lit-level-emblem>
            </div>
          </div>
        </template>
      </div>
    </div>
  </div>
</div>

<script>
  function state(userId) {
    return {
      animationDurationMs: 700,
      gameUserListMarginTopPx: 8,
      game: @json($game),
      gameStageText: 'Waiting for players...',
      gameUsers: @json($game_users),
      handleGameStatusInterval: null,
      isBob: @json($user).is_bob,
      get gameUserCount() {
        return this.gameUsers.length;
      },
      get observerCount() {
        return this.gameUsers.filter(n => n.is_observer).length;
      },
      get playerCount() {
        return this.gameUsers.filter(n => !n.is_observer).length;
      },
      get gameUserList() {
        const hostPlayer = this.gameUsers.find(n => n.is_host);
        const noHostPlayers = this.gameUsers
          .filter(n => n.is_host ? false : true)
          .sort((a,b) => a.created_at < b.created_at ? -1 : a.created_at > b.created_at ? 1 : 0);
        return [hostPlayer, ...noHostPlayers];
      },
      /** Returns a list of game users for dev purpose only. */
      get gameUserListDev() {
        const gameUsers = [];
        for(i=0; i<10;i++) {
          gameUsers.push( {
            id: 'id',
            display_name: 'GreenMonkeyBoy',
            is_ready: true,
            country_cca2: 'FR',
            flag_file_path: '/static/flag/svg/FR.svg',
            flag_description: 'desc',
            level: 8,
            map_marker_file_path: 'https://gmb.gman.bot/static/img/map-marker/chibi/greek-warrior.png',
            map_marker_map_anchor: 'bottom'
          });
        }
        return gameUsers;
      },
      get lowercaseMapStyleShortName() {
        const name = this.user.map_style_short_name.toLowerCase();
        return name.charAt(0).toUpperCase() + name.slice(1);
      },
      get readyEntrantCount() {
        return this.gameUsers.filter(n => n.is_ready).length;
      },
      get user() {
        return this.gameUsers.find(n => n.id === userId);
      },
      deleteGame() {
        fetch(`/web-api/game/${this.game.id}`, {
          method: 'DELETE',
          headers: {
            'Content-Type': 'application/json',
          },
        }).then(() => {
          window.location.href = '/';
        })
      },
      leave() {
        fetch(`/web-api/game/${this.game.id}/leave`, {
          method: 'DELETE',
          headers: {
            'Content-Type': 'application/json',
          },
        }).then(() => {
          window.location.href = '/';
        })
      },
      openConfirmDeleteGameDialog() {
        this.$refs.confirmDeleteGameDialog.open();
      },
      openEditGameSettingsDialog() {
        this.$refs.editGameSettingsDialog.open();
      },
      openSelectMapMarkerDialog() {
        this.$refs.selectMapMarkerDialog.open();
      },
      openSelectMapStyleDialog() {
        this.$refs.selectMapStyleDialog.open();
      },
      openSelectUserProfileDialog() {
        this.$refs.selectUserProfileDialog.open();
      },
      toggleIsObserver(isObserver) {
        fetch(`/web-api/game-user/${this.game.id}`, {
            method: 'PATCH',
            headers: {
              'Content-Type': 'application/json',
            },
            body: JSON.stringify({
              is_observer: isObserver
            })
        })
        .then(response => {
            if (!response.ok) {
              console.error(`Failed to update observer status: ${response.statusText}`);
              return;
            }
        })
        .catch(error => {
            console.error('Error updating observer status:', error);
        });
      },
      toggleIsReady(isReady) {
        fetch(`/web-api/game-user/${this.game.id}`, {
            method: 'PATCH',
            headers: {
              'Content-Type': 'application/json',
            },
            body: JSON.stringify({
              is_ready: isReady
            })
        })
        .then(response => {
            if (!response.ok) {
              console.error(`Failed to update ready status: ${response.statusText}`);
              return;
            }
        })
        .catch(error => {
            console.error('Error updating ready status:', error);
        });
      },
      togglePlayerListSize() {
        const state = this.$refs.gameUsers.getAttribute('data-state');
        const gameUsersExpandedHeightPx = window.innerHeight - this.$refs.header.offsetHeight - this.gameUserListMarginTopPx;

        if (state === 'collapsed') {
          this.$refs.gameUsers.style.width = `${this.$refs.layout.offsetWidth}px`;
          this.$refs.gameUsers.style.height = `${this.$refs.gameUsers.offsetHeight}px`;
          this.$refs.gameUsersToggleSizeIcon.classList.add('rotate-180');

          // If there is no timeout, the animation doesn't occur. It's as if the second value is applied immediately, bypassing the first value altogether.
          // One millisecond is enough in dev, put 5 for security.
          setTimeout(() => {
            this.$refs.gameUsers.classList.add('fixed', 'bottom-0', 'transition-[height]', `duration-${this.animationDurationMs}`);

            setTimeout(() => {
              this.$refs.gameUsersSubstitute.classList.remove('hidden');
              this.$refs.gameUsersSubstitute.classList.add('block');

              this.$refs.gameUsers.style.height = `${gameUsersExpandedHeightPx}px`;
            }, 5);
          }, 5);

          setTimeout(() => {
            this.$refs.gameUsers.classList.remove('transition-[height]', `duration-${this.animationDurationMs}`);
            this.$refs.gameUserList.classList.add('overflow-y-auto');
            this.$refs.gameUsers.setAttribute('data-state', 'expanded');
          }, (this.animationDurationMs));
        } else {
          this.$refs.gameUsers.classList.add('transition-[height]', `duration-${this.animationDurationMs}`);
          this.$refs.gameUsers.style.height = `${this.$refs.gameUsersSubstitute.offsetHeight}px`;
          this.$refs.gameUsersToggleSizeIcon.classList.remove('rotate-180');
          this.$refs.gameUserList.classList.remove('overflow-y-auto');

          setTimeout(() => {
            this.$refs.gameUsers.style.removeProperty('width');
            this.$refs.gameUsers.classList.remove('fixed', 'bottom-0');
            this.$refs.gameUsers.classList.add('flex', 'flex-1', 'min-h-[150px]');
            this.$refs.gameUsers.classList.remove('transition-[height]', `duration-${this.animationDurationMs}`);

            this.$refs.gameUsersSubstitute.classList.remove('block');
            this.$refs.gameUsersSubstitute.classList.add('hidden'); 

            this.$refs.gameUsers.setAttribute('data-state', 'collapsed');
          }, this.animationDurationMs);
        }
      },
      updatePlayer(gameUser) {
        for (let i = 0; i < this.gameUsers.length; i++) {
          if (this.gameUsers[i].id === gameUser.id) {
            this.gameUsers[i] = gameUser;
            break;
          }
        }   
      },
      init() {
        window.addEventListener('resize', () => {
          const gameUsersState = this.$refs.gameUsers.getAttribute('data-state');

          if (gameUsersState === 'expanded') {
            const gameUsersExpandedHeightPx = window.innerHeight - this.$refs.header.offsetHeight - this.gameUserListMarginTopPx;

            this.$refs.gameUsers.style.width = `${this.$refs.layout.offsetWidth}px`;
            this.$refs.gameUsers.style.height = `${gameUsersExpandedHeightPx}px`;
          }
        });    

        // Websockets
        const webSocketClient = WebSocketClient.init();
        const channel = webSocketClient.subscribeToChannel(`game.${this.game.id}`);
        
        channel.bind('game.delete', () => {
          window.location.href = '/';
        });
        channel.bind('game.update', ({ game }) => {
          this.game = game;
        });
        channel.bind('game.stage.update', ({ message, stage }) => {
          this.gameStageText = message;
        });
        channel.bind('game.round.update', ({ roundNumber, gameStateEnum }) => {
          window.location.href = `/game/${this.game.id}/play`;
        });
        channel.bind('game-user.join', ({ gameUser }) => {
          if (!this.gameUsers.find(n => n.id === gameUser.id)) {
            this.gameUsers.push(gameUser);
          }
        });
        channel.bind('game-user.update', ({ gameUser }) => {
          this.updatePlayer(gameUser);    
        });
        channel.bind('game-user.leave', ({ userId }) => {
          // Because leaving the game is a request, it takes time. If I remove the user in the player list,
          // it creates bugs on the page because the user not exist anymore.
          if (this.user.id !== userId) {
            this.gameUsers = this.gameUsers.filter(n => n.id !== userId);
          }
        }); 

        this.handleGameStatusInterval = setInterval(() => {
          fetch(`/web-api/game/${this.game.id}/status`, {
            method: 'GET',
            headers: {
              'Content-Type': 'application/json',
            },
          }).then(response => {
            if (!response.ok) {
              throw new Error(`Response not ok: ${response.statusText}`);
            }
            return response.json();
          }).then(data => {
            if (data.status !== 'OK') {
              window.location.href = '/';
            }
            if (data.in_progress === true) {
              window.location.href = `/game/${this.game.id}/play`;
            }
            if (data.finished === true) {
              window.location.href = `/game/${this.game.id}/result`;
            }
          }).catch(error => {
            console.error('Error fetching game data:', error);
          });
        }, 20000);
      },
      destroy() {
        clearInterval(this.handleGameStatusInterval);
      }
    }
  }

  function gameTinyUrlState() {
    return {
      iconTooltipText: 'Copy to clipboard',
      copyUrlToClipboard() {
        const gameInvitationText = this.$refs.url.innerText;
        navigator.clipboard.writeText(gameInvitationText);
        this.iconTooltipText = 'Copied';
        this.refreshTooltip();
      },
      initTippy() {
        tippy(this.$refs.clipboardIcon, {
          content: this.iconTooltipText,
          hideOnClick: false,
          onHidden: (instance) => {
            instance.setContent(this.iconTooltipText);
            this.$refs.clipboardIcon._tippy.setContent('Copy to clipboard');
          }
        });
      },
      refreshTooltip() {
        this.$refs.clipboardIcon._tippy.setContent(this.iconTooltipText);
      },
       init() {
        this.initTippy();
      },

    }
  }
</script>