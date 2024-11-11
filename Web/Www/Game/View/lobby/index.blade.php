<div x-data="state('{{ $game->id }}', '{{ $user->id }}')" class="flex flex-col max-w-5xl h-screen mx-auto lg:border-x border-gray-700 select-none">
  <!-- Header -->
  <div x-ref="header" class="flex h-14 shrink-0 justify-between items-center px-2 border-b border-gray-700 bg-iris-500">
    <div class="flex w-16">
      @if($user->isHost)
        <lit-button imgPath="/static/img/icon/cross.svg" size="md" bgColorClass="bg-red-500" hx-delete="/game/{{$game->id}}" hx-swap="none"></lit-button>
      @else
        <lit-button imgPath="/static/img/icon/arrow-back.svg" size="md" bgColorClass="bg-red-500" hx-delete="/game/{{$game->id}}/lobby/leave" hx-swap="none"></lit-button>
      @endif
    </div>
    <div class="flex flex-col flex-1 items-center">
      <span x-text="gameStageText" class="font-heading text-lg text-gray-0 font-bold text-stroke-2 text-stroke-iris-800 leading-none"></span>
      <div class="font-heading text-base text-gray-0 font-medium text-stroke-1 text-stroke-iris-800">
        <span x-text="readyPlayerCount"></span>/<span x-text="playerCount"></span> players ready
      </div>
    </div>
    <div class="flex justify-end w-24">
      <div class="block sm:hidden w-full">
        <lit-button label="Wait" size="md" bgColorClass="bg-gray-500" hx-patch="/game/{{$game->id}}/lobby/update-game-user" hx-vals='{"is_ready": false}' hx-swap="none" x-show="user.is_ready"></lit-button>
        <lit-button label="Ready" size="md" bgColorClass="bg-pistachio-400" hx-patch="/game/{{$game->id}}/lobby/update-game-user" hx-vals='{"is_ready": true}' hx-swap="none" x-show="!user.is_ready"></lit-button>
      </div>
    </div>
  </div>
  
  <!-- Main Content Area -->
  <div x-ref="layout" class="flex w-full h-full overflow-hidden">
    <!-- Left Column -->
    <div class="flex flex-col w-full h-full">
      <!-- Scrollable Content -->
      <div class="flex flex-col w-full sm:h-full overflow-y-auto">
        <lit-panel-header label="PROFILE"></lit-panel-header>
        <div class="py-2">
          <div class="flex gap-2 mx-2">
            <div class="flex flex-none justify-start items-end w-[72px] h-[72px]">
              <img :src="user.map_marker_file_path" class="max-w-full max-h-full self-end object-contain cursor-pointer" draggable="false" hx-get="/game/{{$game->id}}/lobby/dialog/map-marker" />
            </div>

            <div class="flex flex-col justify-between w-full overflow-hidden">
              <div class="flex flex-col">
                <div class="flex items-center gap-2">
                  <span x-text="user.display_name" class="leading-none font-heading font-semibold text-lg text-iris-800 truncate"></span>
                  <img src="/static/img/icon/edit-pen.svg" class="h-6 hover:brightness-90 cursor-pointer" draggable="false" hx-get="/game/{{$game->id}}/lobby/dialog/name-flag" />
                </div>
                <span class="relative bottom-0.5 font-heading font-semibold text-sm text-gray-800">{{ $user->title }}</span>
              </div>
              <div class="flex">
                @if(!$user->isGuest)
                  <lit-button :label="userMapStyleShortName" size="xs" contentAlignment="left" imgPath="/static/img/icon/map.svg" class="w-24" hx-get="/game/{{$game->id}}/lobby/dialog/map-style"></lit-button>
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

        <lit-panel-header label="GAME SETTINGS">
          <lit-label slot="left" :label="game.is_public ? 'PUBLIC' : 'PRIVATE'" size="xs" :bgColorClass="game.is_public ? 'bg-pistachio-400' : 'bg-red-500'" isPill widthClass="w-16"></lit-label>
          @if($user->isHost)
          <div slot="right" class="flex items-center gap-2">
            <lit-button label="Edit" size="xs" bgColorClass="bg-orange-400" class="w-16" hx-get="/game/{{$game->id}}/lobby/dialog/settings"></lit-button>
            <lit-button label="Start" size="xs" bgColorClass="bg-iris-400" class="w-16" hx-post="/game/{{$game->id}}/start" hx-swap="none"></lit-button>
          </div>
          @endif
        </lit-panel-header>
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
                  <span x-ref="url" class="text-xs text-gray-800 max-w-[132px] min-[680px]:max-w-none truncate select-all">{{ config(key: 'app.url') }}/g/{{ $game->short_code }}</span>
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

          <div class="block sm:hidden mt-2 mx-2">
            @include('game::lobby.total-game-time')
          </div>
        </div>
      </div>
      <!-- Expandable Panel -->
      <div x-ref="playersSubstitute" class="hidden flex-1 min-h-[142px]"></div>
      <div x-ref="players" data-state="collapsed" class="flex flex-col sm:hidden flex-1 min-h-[142px] z-10 px-2 bg-iris-500 border border-t-2 border-b-0 border-gray-700 rounded-t-2xl transition-[height] duration-700 ease-in-out">
        <div class="flex justify-between items-center py-2 border-b border-gray-50 cursor-pointer" x-on:click="togglePlayerListSize">
          <div></div>
          <div class="font-heading font-bold text-base text-gray-0 text-stroke-2 text-stroke-iris-800">PLAYERS</div>
          <div x-ref="playersToggleSizeIcon" class="w-4 mr-4 transition-transform duration-1000 ease-in-out">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="#EFF1F4" class="size-6">
              <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 15.75 7.5-7.5 7.5 7.5" />
            </svg>
          </div>
        </div>
        <div x-ref="playerList" class="py-2">
          @include('game::lobby.player-list')
        </div>
      </div> 
      
      {{-- <div x-ref="players" data-state="collapsed" style="height: 148px" class="grow z-10 bg-iris-500 transition-[height] duration-700 ease-in-out">
        <div class="flex justify-between items-center py-2 border-b border-gray-50 cursor-pointer" x-on:click="togglePlayerListSize">
          <div></div>
          <div class="font-heading font-bold text-base text-gray-0 text-stroke-2 text-stroke-iris-800">PLAYERS</div>
          <div x-ref="playersToggleSizeIcon" class="w-4 mr-4 transition-transform duration-1000 ease-in-out">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="#EFF1F4" class="size-6">
              <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 15.75 7.5-7.5 7.5 7.5" />
            </svg>
          </div>
        </div>
        <div x-ref="playerList" class="overflow-y-auto">
          @include('game::lobby.player-list')
          player list<br>player list<br>player list<br>player list<br>player list<br>player list<br>player list<br>player list<br>LAST<br>
        </div>
      </div> --}}

      <div class="hidden sm:block">
        @include('game::lobby.total-game-time')
      </div>
    </div>
    <!-- Right Column -->
    <div class="hidden sm:flex flex-col shrink-0 w-[280px] h-full border-l border-gray-700">
      <lit-panel-header label="STATUS">
        <lit-label slot="right" :label="user.is_ready ? 'Ready' : 'Pending...'" size="sm" :bgColorClass="user.is_ready ? 'bg-pistachio-400' : 'bg-gray-500'" widthClass="w-24"></lit-label>
      </lit-panel-header>
      <div class="flex justify-center p-2">
        <lit-button label="Wait" size="md" bgColorClass="bg-gray-400" hx-patch="/game/{{$game->id}}/lobby/update-game-user" hx-vals='{"is_ready": false}' hx-swap="none" x-show="user.is_ready" class="w-48"></lit-button>
        <lit-button label="Ready" size="md" bgColorClass="bg-pistachio-500" hx-patch="/game/{{$game->id}}/lobby/update-game-user" hx-vals='{"is_ready": true}' hx-swap="none" x-show="!user.is_ready" class="w-48"></lit-button>
      </div>

      <lit-panel-header label="PLAYERS"></lit-panel-header>
      <div class="overflow-y-auto">
        <template x-for="player in players">
          <div class="flex gap-2 p-2 border-b border-gray-300 bg-gradient-to-t" :class="{ 
            'from-pistachio-400 to-pistachio-500': player.is_ready, 
            'from-gray-50 to-gray-100': !player.is_ready 
            }">
            <div class="flex flex-none justify-start items-end w-12 h-12">
              <img :src="player.map_marker_file_path" class="max-w-full max-h-full self-end object-contain" draggable="false" />
            </div>
            <div class="flex flex-col gap-0 w-full overflow-hidden">
              <span x-text="player.display_name" class="leading-none font-heading font-semibold text-base truncate"  :class="{ 'text-iris-800': !player.is_ready, 'text-gray-0 text-stroke-2 text-stroke-pistachio-900': player.is_ready }"></span>
              <span x-text="player.title" class="font-heading font-semibold text-sm text-gray-800 truncate"></span>
            </div>
            <div class="flex flex-col gap-1">
              <div class="flex justify-center items-center w-6 h-5">
                <lit-flag :cca2="player.country_cca2" :filePath="player.flag_file_path" :description="player.flag_description" roundedClass="rounded-sm" maxHeightClass="max-h-5" class="w-6" draggable="false"></lit-flag>
              </div>
              <lit-level-emblem :level="player.level" size="xs"></lit-level-emblem>
            </div>
          </div>
        </template>
      </div>
    </div>
  </div>
</div>

<script>
  function state(gameId, userId) {
    return {
      animationDurationMs: 700,
      playerListMarginTopPx: 8,
      game: @json($game),
      gameStageText: 'Waiting for players...',
      players: @json($players),
      get user() {
        return this.players.find(n => n.id === userId);
      },
      get userMapStyleShortName() {
        switch(this.user.map_style_enum){
          case 'OSM': return 'OSM';
          case 'NIGHT': return 'NIGHT';
          case 'SATELLITE_STREETS': return 'SAT STR';
          case 'DARK': return 'DARK';
          case 'DEFAULT': return 'DEFLT';
          case 'STREETS': return 'PLE STR';
          case 'SATELLITE': return 'SAT';
          default: return 'MAP';
        };
      },
      get readyPlayerCount() {
        return this.players.filter(n => n.is_ready).length;
      },
      get playerCount() {
        return this.players.length;
      },
      togglePlayerListSize() {
        const state = this.$refs.players.getAttribute('data-state');
        const playersExpandedHeightpx = window.innerHeight - this.$refs.header.offsetHeight - this.playerListMarginTopPx;

        if (state === 'collapsed') {
          this.$refs.players.style.width = `${this.$refs.layout.offsetWidth}px`;
          this.$refs.players.style.height = `${this.$refs.players.offsetHeight}px`;
          this.$refs.playersToggleSizeIcon.classList.add('rotate-180');

          // If there is no timeout, the animation doesn't occur. It's as if the second value is applied immediately, bypassing the first value altogether.
          // One millisecond is enough in dev, put 5 for security.
          setTimeout(() => {
            this.$refs.players.classList.add('fixed', 'bottom-0', 'transition-[height]', `duration-${this.animationDurationMs}`);

            setTimeout(() => {
              this.$refs.playersSubstitute.classList.remove('hidden');
              this.$refs.playersSubstitute.classList.add('block');

              this.$refs.players.style.height = `${playersExpandedHeightpx}px`;
            }, 5);
          }, 5);

          setTimeout(() => {
            this.$refs.players.classList.remove('transition-[height]', `duration-${this.animationDurationMs}`);
            this.$refs.playerList.classList.add('overflow-y-auto');
            this.$refs.players.setAttribute('data-state', 'expanded');
          }, (this.animationDurationMs));
        } else {
          this.$refs.players.classList.add('transition-[height]', `duration-${this.animationDurationMs}`);
          this.$refs.players.style.height = `${this.$refs.playersSubstitute.offsetHeight}px`;
          this.$refs.playersToggleSizeIcon.classList.remove('rotate-180');
          this.$refs.playerList.classList.remove('overflow-y-auto');

          setTimeout(() => {
            this.$refs.players.style.removeProperty('width');
            this.$refs.players.classList.remove('fixed', 'bottom-0');
            this.$refs.players.classList.add('flex', 'flex-1', 'min-h-[142px]');
            this.$refs.players.classList.remove('transition-[height]', `duration-${this.animationDurationMs}`);

            this.$refs.playersSubstitute.classList.remove('block');
            this.$refs.playersSubstitute.classList.add('hidden'); 

            this.$refs.players.setAttribute('data-state', 'collapsed');
          }, this.animationDurationMs);
        }
      },
      updatePlayer(player) {
        for (let i = 0; i < this.players.length; i++) {
          if (this.players[i].id === player.id) {
            this.players[i] = player;
            break;
          }
        }   
      },
      init() {
        window.addEventListener('resize', () => {
          const playersState = this.$refs.players.getAttribute('data-state');

          if (playersState === 'expanded') {
            const playersExpandedHeightpx = window.innerHeight - this.$refs.header.offsetHeight - this.playerListMarginTopPx;

            this.$refs.players.style.width = `${this.$refs.layout.offsetWidth}px`;
            this.$refs.players.style.height = `${playersExpandedHeightpx}px`;
          }
        });    

        // Websockets
        const pusher = new Pusher('6csm0edgczin2onq92lm', window.pusher_data);
        pusher.bind('error', function(error) {
          console.error('Pusher error:', error);
        });
        pusher.bind('disconnected', function(error) {
          console.error('Pusher error:', error);
        });

        const channel = pusher.subscribe(`game.${gameId}`);
        
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
          window.location.href = `/game/${gameId}/play`;
        });
        channel.bind('player.join', ({ player }) => {
          if (!this.players.find(n => n.id === player.id)) {
            this.players.push(player);
          }
        });
        channel.bind('player.update', ({ player }) => {
          this.updatePlayer(player);    
        });
        channel.bind('player.leave', ({ playerId }) => {
          // Because leaving the game is a request, it takes time. If I remove the user in the player list,
          // it creates bugs on the page because the user not exist anymore.
          if (this.user.id !== playerId) {
            this.players = this.players.filter(n => n.id !== playerId);
          }
        }); 
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