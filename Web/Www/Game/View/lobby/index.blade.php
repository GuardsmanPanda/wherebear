<?php declare(strict_types=1); ?>

<div x-data="state" class="flex flex-col w-full max-w-5xl h-screen mx-auto lg:border-x border-gray-700 select-none toast-container top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 overflow-hidden">
  <!-- Dialogs -->
  <lit-login-dialog x-ref="loginDialog"></lit-login-dialog>

  <lit-edit-game-settings-dialog
    x-ref="editGameSettingsDialog"
    :gameId="game.id"
    :gamePublicStatusEnum="game.game_public_status_enum"
    :gameType="game.type"
    :roundCount="game.number_of_rounds"
    :roundDurationSec="game.round_duration_seconds"
    :roundResultDurationSec="game.round_result_duration_seconds"
    :isBob="user.isBob">
  </lit-edit-game-settings-dialog>

  <lit-confirm-dialog id="abc" x-ref="confirmDeleteGameDialog" 
    label="Delete the game"
    message="Are you sure to delete the game?"
    cancelBtnText="No, Cancel"
    confirmBtnText="Yes, Delete"
    confirmBtnBgColorClass="bg-poppy-400"
    x-on:confirmed="deleteGame">
  </lit-confirm-dialog>

  <lit-confirm-dialog x-ref="confirmStartGameDialog" 
    label="Start the game"
    message="Are you sure to start the game?"
    cancelBtnText="No, Cancel"
    confirmBtnText="Yes, Start"
    confirmBtnBgColorClass="bg-pistachio-400"
    x-on:confirmed="startGame">
  </lit-confirm-dialog>

  <!-- Header -->
  <div class="flex flex-col h-24 bg-[url('/static/img/ui/mountain-cartoon.png')] bg-cover bg-center relative">
    <div class="flex w-full relative">
      <div class="flex w-full overflow-hidden">
        <div class="flex justify-center items-center w-full sm:w-min h-8 px-2 sm:rounded-br-sm bg-gradient-to-t from-iris-400 to-iris-500 sm:border-r border-b border-gray-700 shadow-lg overflow-hidden">
          <span x-text="game.name" class="font-heading font-semibold text-base text-gray-0 text-stroke-2 text-stroke-gray-700 truncate"></span>
        </div>
      </div>

      <div class="flex sm:hidden gap-2 absolute -bottom-2 right-2">
        <lit-label x-show="game.type === 'template'" label="Template" size="xs" color="gray" class="w-16" data-tippy-content="Game type: Fixed panoramas"></lit-label>
        <lit-label x-show="game.type === 'normal'" label="Normal" size="xs" color="gray" class="w-16"  data-tippy-content="Game type: Randomized panoramas"></lit-label>
        <lit-label
          :label="game.is_public ? 'Public' : 'Private'"  
          size="xs"  
          :color="game.is_public ? 'gray' : 'red'"  
          class="w-16"  
          x-tippy="game.is_public ? 'Game access: Everyone can join' : 'Game access: Invite-only'"  
        ></lit-label>  
      </div>

      <div class="hidden sm:flex gap-2 p-2">
        <div x-data="gameTinyUrlState" x-ref="clipboardIcon" x-on:click="copyUrlToClipboard()" class="hidden sm:flex h-6 hover:cursor-pointer group">
          <div class="flex items-center w-full h-full px-2 rounded-l-sm border border-r-0 border-iris-800 bg-iris-200 group-hover:bg-iris-300">
            <span x-text="getGameLink()" class="text-xs text-gray-800 text-nowrap"></span>
          </div>
          <div  class="w-8 shrink-0 rounded-r-sm border border-iris-800 py-0.5 bg-iris-400 group-hover:bg-iris-500">
            <img src="/static/img/icon/copy.svg" class="w-full h-full"/>
          </div>
        </div>
        <lit-label x-show="game.type === 'template'" label="Template" size="sm" color="gray" class="w-16" data-tippy-content="Game type: Fixed panoramas"></lit-label>
        <lit-label x-show="game.type === 'normal'" label="Normal" size="sm" color="gray" class="w-16"  data-tippy-content="Game type: Randomized panoramas"></lit-label>
        <lit-label  
          :label="game.is_public ? 'Public' : 'Private'"  
          size="sm"  
          :color="game.is_public ? 'gray' : 'red'"  
          class="w-16"  
          x-tippy="game.is_public ? 'Game access: Everyone can join' : 'Game access: Invite-only'"  
        ></lit-label> 
      </div>
    </div>


    <div class="absolute -bottom-[2px] left-2 sm:left-1/2 sm:-translate-x-1/2">
      <img src="/static/img/ui/road-sign.svg" class="w-[184px]" />
      <span x-text="gameStartText" class="absolute top-[4px] left-1/2 -translate-x-1/2 whitespace-nowrap font-heading font-semibold text-center text-orange-400 text-base text-stroke-1 text-stroke-gray-800"></span>
    </div>
  </div>

  <!-- Road -->
  <div class="w-full h-[42px] bg-[url('/static/img/ui/road.png')] bg-repeat-x"></div>

  
  <!-- Sub Header -->
  <div class="flex justify-center gap-2 w-full p-1 bg-gray-600 border-b border-gray-700">
    <lit-label-with-value label="Rounds" :value="game.number_of_rounds" widthClass="w-full" class="w-full max-w-32" data-tippy-content="Total number of rounds in the game"></lit-label-with-value>
    <lit-label-with-value label="Guess" :value="`${game.round_duration_seconds}s`" widthClass="w-full" class="block sm:hidden w-full max-w-32" data-tippy-content="Time limit to make a guess per round"></lit-label-with-value>
    <lit-label-with-value label="Guessing Time" :value="`${game.round_duration_seconds}s`" widthClass="w-full" class="hidden sm:block w-full max-w-32" data-tippy-content="Time limit to make a guess per round"></lit-label-with-value>
    <lit-label-with-value label="Result" :value="`${game.round_result_duration_seconds}s`" widthClass="w-full" class="block sm:hidden w-full max-w-32" data-tippy-content="Duration of result display after each round"></lit-label-with-value>
    <lit-label-with-value label="Result Time" :value="`${game.round_result_duration_seconds}s`" widthClass="w-full" class="hidden sm:block w-full max-w-32" data-tippy-content="Duration of result display after each round"></lit-label-with-value>
    <lit-label :label="`${game.total_game_time_mn} min`" color="orange" iconPath="/static/img/icon/chronometer.svg" class="flex sm:hidden shrink-0" data-tippy-content="Estimated total game duration"></lit-label>
    <lit-label :label="`${game.total_game_time_mn} minute${game.total_game_time_mn > 1 ? 's' : ''}`" color="orange" iconPath="/static/img/icon/chronometer.svg" class="hidden sm:flex" data-tippy-content="Estimated total game duration"></lit-label>
  </div>

  <!-- Main -->
  <div class="flex flex-1 overflow-hidden bg-iris-200">
    <!-- Left Column -->
    <div class="flex flex-col w-full overflow-hidden">
      <!-- Not Logged In Warning -->
      <div class="w-full h-12 sm:h-14 border-b border-gray-700 bg-gradient-to-t from-yellow-300 to-yellow-200" :class="[user.is_guest ? 'flex' : 'hidden']">
        <div class="flex shrink-0 justify-center items-center w-12 sm:w-16 bg-yellow-400">
          <img src="/static/img/icon/warning-sign-yellow.svg" class="w-10 sm:w-12 relative top-1"/>
        </div>
        <div class="flex flex-col justify-center items-start sm:items-center w-full p-2">
          <div class="flex gap-1">
            <span class="font-heading font-bold text-sm sm:text-base text-gray-700">Oops, you're not logged in</span>
          </div>
          <span class="text-xs sm:text-sm text-gray-700">Log in to earn XP and rewards.</span>
        </div>
        <div class="flex justify-center items-center p-2">
          <lit-button label="Log in" size="sm" bgColorClass="bg-yellow-400" class="block sm:hidden w-20" x-on:clicked="openLoginDialog"></lit-button>
          <lit-button label="Log in" size="md" bgColorClass="bg-yellow-400" class="hidden sm:block w-24" x-on:clicked="openLoginDialog"></lit-button>
        </div>
      </div>

      <div class="flex flex-col flex-1 overflow-x-hidden overflow-y-auto">
        <lit-panel label="PROFILE" class="my-2 mx-2">
          <div class="py-2">
            <div class="flex gap-2 mx-2">
              <!-- Map Marker -->
              <div data-tippy-content="Select Map Marker" class="flex flex-none justify-center w-[72px] h-[72px]" :class="{ 'items-end': gameUser.map_marker_map_anchor === 'bottom', 'items-center': gameUser.map_marker_map_anchor === 'center' }">
                <img :src="gameUser.map_marker_file_path" class="max-w-full max-h-full object-contain cursor-pointer" draggable="false" x-on:click="openSelectMapMarkerDialog" />
              </div>

              <lit-select-map-marker-dialog x-ref="selectMapMarkerDialog"></lit-select-map-marker-dialog>

              <div class="flex flex-col justify-between w-full overflow-hidden">
                <div class="flex flex-col">
                  <!-- Name -->
                  <div class="flex items-center gap-2">
                    <span x-text="gameUser.display_name" class="block leading-none font-heading font-semibold text-lg text-iris-800 truncate"></span>
                    <img src="/static/img/icon/edit-pen.svg" class="h-6 hover:brightness-90 cursor-pointer" draggable="false" data-tippy-content="Edit profile" x-on:click="openSelectUserProfileDialog" />
                    <lit-select-user-profile-dialog x-ref="selectUserProfileDialog" :displayName="gameUser.display_name" :selectedCountryFlag="gameUser.country_cca2"></lit-select-user-profile-dialog>
                  </div>
                  <!-- Title -->
                  <span class="relative bottom-0.5 font-heading font-semibold text-sm text-gray-800">{{ $user->title }}</span>
                </div>

                <!-- Buttons Row -->
                <div class="flex gap-2">
                  @if(!$user->is_guest)
                    <span data-tippy-content="Select Map Style">
                      <lit-button 
                        size="sm"
                        contentAlignment="left"
                        imgPath="/static/img/icon/map.svg"
                        class="block sm:hidden w-8"
                        x-on:clicked="openSelectMapStyleDialog">
                      </lit-button>
                      <lit-button 
                        :label="lowercaseMapStyleShortName"
                        size="sm"
                        contentAlignment="left"
                        imgPath="/static/img/icon/map.svg"
                        class="hidden sm:block w-[112px]"
                        x-on:clicked="openSelectMapStyleDialog">
                      </lit-button>
                    </span>
                  @endif
                </div>
              </div>

              <lit-level-emblem level="{{ $user->level }}" size="lg" class="ml-4" /></lit-level-emblem>
            </div>

            <!-- Experience -->
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

        <lit-panel label="PLAYERS" x-show="playerCount > 0" class="flex md:hidden my-2 mx-2">
          <div slot="header-right">
            @include('game::lobby.player-header-right')
          </div>
          <div  class="flex p-2">
            <div class="grid grid-cols-3 min-[420px]:grid-cols-4 min-[520px]:grid-cols-5 min-[622px]:grid-cols-6 gap-4">
              <template x-for="gameUser in playerList">
                <div class="flex flex-col w-20 justify-center items-center">
                  <div class="flex w-full h-4 justify-center items-center px-0.5 rounded-t-sm border border-b-0 border-gray-700 bg-gray-600" :data-tippy-content="gameUser.display_name">
                    <span x-text="gameUser.display_name" class="font-heading font-medium text-xs text-gray-0 truncate"></span>
                  </div>
                  <div class="flex flex-none justify-center w-20 h-16 relative border-x border-gray-700 bg-gradient-to-t"
                    :class="{ 
                      'items-center': gameUser.map_marker_map_anchor === 'center',
                      'items-end': gameUser.map_marker_map_anchor === 'bottom',
                      'from-gray-100 to-gray-200': !gameUser.is_ready,
                      'from-pistachio-300 to-pistachio-500': gameUser.is_ready
                    }">
                    
                    <img :src="gameUser.map_marker_file_path" draggable="false" class="max-w-[72px]" :class="{
                      'max-h-14': gameUser.map_marker_map_anchor === 'center',
                      'max-h-16 relative top-[4px]': gameUser.map_marker_map_anchor === 'bottom'
                    }" />
                  </div>
                  <div class="flex flex-none w-full h-[10px] relative rounded-b-sm border border-gray-700 bg-gray-600">
                    <lit-flag :cca2="gameUser.country_cca2" :filePath="gameUser.flag_file_path" :description="gameUser.flag_description" roundedClass="rounded-xs" class="absolute bottom-[2px] right-[3px] h-4" draggable="false"></lit-flag>
                    <lit-level-emblem :level="gameUser.level" size="xs" class="absolute left-0.5 bottom-0.5"></lit-level-emblem>
                  </div>
                </div>
              </template>
            </div>
          </div>
        </lit-panel>

        <lit-panel label="SPECTATORS" x-show="observerCount > 0" class="flex md:hidden my-2 mx-2">
          <div slot="header-right">
            <span x-text="observerCount" class="font-heading text-base text-gray-0"></span>
          </div>
          <div class="flex p-2">
            <div class="grid grid-cols-3 min-[420px]:grid-cols-4 min-[520px]:grid-cols-5 min-[622px]:grid-cols-6 gap-4">
              <template x-for="gameUser in observerList">
                <div class="flex flex-col w-20 justify-center items-center">
                  <div class="flex w-full h-4 justify-center items-center px-0.5 rounded-t-sm border border-b-0 border-gray-700 bg-gray-600" :data-tippy-content="gameUser.display_name">
                    <span x-text="gameUser.display_name" class="font-heading font-medium text-xs text-gray-0 truncate"></span>
                  </div>
                  <div class="flex flex-none justify-center w-20 h-16 relative border-x border-gray-700 bg-gradient-to-t from-gray-100 to-gray-200"
                    :class="{ 
                      'items-center': gameUser.map_marker_map_anchor === 'center',
                      'items-end': gameUser.map_marker_map_anchor === 'bottom'
                    }">
                    
                    <img :src="gameUser.map_marker_file_path" draggable="false" class="max-w-[72px]" :class="{
                      'max-h-14': gameUser.map_marker_map_anchor === 'center',
                      'max-h-16 relative top-[4px]': gameUser.map_marker_map_anchor === 'bottom'
                    }" />
                  </div>
                  <div class="flex flex-none w-full h-[10px] relative rounded-b-sm border border-gray-700 bg-gray-600">
                    <lit-flag :cca2="gameUser.country_cca2" :filePath="gameUser.flag_file_path" :description="gameUser.flag_description" roundedClass="rounded-xs" class="absolute bottom-[2px] right-[3px] h-4" draggable="false"></lit-flag>
                    <lit-level-emblem :level="gameUser.level" size="xs" class="absolute left-0.5 bottom-0.5"></lit-level-emblem>
                  </div>
                </div>
              </template>
            </div>
          </div>
        </lit-panel>
      </div>
    </div>

    <!-- Right Column -->
    <div class="hidden md:flex flex-col shrink-0 w-[320px] h-full overflow-y-auto z-10 border-l border-gray-700 bg-iris-400" style="box-shadow: -2px 0 2px rgba(0, 0, 0, 0.25)">
      <!-- Player List -->
      <lit-panel-header2 label="PLAYERS" noBorder noRounded class="mt-2 border-y border-gray-700">
        <div slot="right">
          @include('game::lobby.player-header-right')
        </div>
      </lit-panel-header2>
      <div>
        <template x-for="gameUser in playerList">
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
                  color="gray"
                  iconPath="/static/img/icon/map.svg"
                  class="w-[80px] ml-1">
                </lit-label>

                 <lit-label x-show="gameUser.is_observer"
                  label="Observer"
                  size="xs"
                  color="blue"
                  iconPath="/static/img/icon/eye.svg"
                  class="ml-1">
                </lit-label>
              </div>
            </div>
            <div class="flex flex-col justify-between items-end">
              <div class="flex items-center w-8 h-6">
                <lit-flag :cca2="gameUser.country_cca2" :filePath="gameUser.flag_file_path" :description="gameUser.flag_description" roundedClass="rounded-xs" maxHeightClass="max-h-6" class="w-8" draggable="false"></lit-flag>
              </div>
              <lit-level-emblem :level="gameUser.level" size="sm"></lit-level-emblem>
            </div>
          </div>
        </template>
      </div>

      <!-- Spectator List -->
      <lit-panel-header2 label="SPECTATORS" x-show="observerCount > 0" noBorder noRounded class="border-y border-gray-700">
        <div slot="right" class="flex items-center font-heading font-semibold text-base text-gray-0">
          <span x-text="observerCount"></span>
        </div>
      </lit-panel-header2>
      <div>
        <template x-for="gameUser in observerList">
          <div class="flex gap-2 relative overflow-hidden p-2 border-b border-gray-300 bg-gradient-to-t from-gray-50 to-gray-100">
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
                <span x-text="gameUser.display_name" class="leading-none font-heading font-semibold text-lg text-iris-800 truncate"></span>
                <span x-text="gameUser.title" class="leading-none font-heading font-medium text-sm text-gray-800 truncate"></span>
              </div>
              
              <div class="flex gap-2">
                <lit-label
                  :label="gameUser.map_style_short_name"
                  size="xs"
                  color="orange"
                  iconPath="/static/img/icon/map.svg"
                  class="w-[80px] ml-1">
                </lit-label>
              </div>
            </div>
            <div class="flex flex-col justify-between items-end">
              <div class="flex items-center w-8 h-6">
                <lit-flag :cca2="gameUser.country_cca2" :filePath="gameUser.flag_file_path" :description="gameUser.flag_description" roundedClass="rounded-xs" maxHeightClass="max-h-6" class="w-8" draggable="false"></lit-flag>
              </div>
              <lit-level-emblem :level="gameUser.level" size="sm"></lit-level-emblem>
            </div>
          </div>
        </template>
      </div>
    </div>
  </div>

  <!-- Host Control Bar -->
  @if ($user->is_host)
    <div class="flex sm:hidden justify-center items-center gap-2 absolute bottom-14 left-1/2 -translate-x-1/2 w-56 h-12 border-t border-gray-700 bg-gray-600">
      <div class="absolute top-0 -left-2 z-10 w-6 h-12 -skew-x-12 border-l border-gray-700 rounded-tl-sm bg-gray-600"></div>

      <span data-tippy-content="Edit game settings" class="z-20">
        <lit-button label="EDIT" size="md" imgPath="/static/img/icon/gear.svg" x-on:clicked="openEditGameSettingsDialog"></lit-button>
      </span>
      <span data-tippy-content="Force the game to start" class="z-20">
        <lit-button label="START" size="md" imgPath="/static/img/icon/arrow-right.svg" x-on:clicked="openConfirmStartGameDialog"></lit-button>
      </span>

      <div class="absolute -top-px -right-2 z-10 w-6 h-12 skew-x-12 border-t border-r border-gray-700 rounded-tr-sm bg-gray-600"></div>
    </div>
  @endif

  <!-- Footer -->
  <div class="flex justify-between items-center w-full h-14 px-2 relative border-t border-gray-700 bg-iris-500">
    <!-- Back/Delete Game button -->
    <div class="flex items-center gap-2">
      @if($user->is_host)
        <span data-tippy-content="Delete the game">
          <lit-button imgPath="/static/img/icon/cross.svg" size="md" bgColorClass="bg-poppy-500" class="flex sm:hidden w-10" x-on:clicked="openConfirmDeleteGameDialog"></lit-button>
          <lit-button label="DELETE" size="md" bgColorClass="bg-poppy-500" class="hidden sm:flex md:hidden w-20" x-on:clicked="openConfirmDeleteGameDialog"></lit-button>
          <lit-button label="DELETE" imgPath="/static/img/icon/cross.svg" size="md" bgColorClass="bg-poppy-500" class="hidden md:flex w-32" x-on:clicked="openConfirmDeleteGameDialog"></lit-button>
        </span>
      @else
        <lit-button x-on:click="leave" imgPath="/static/img/icon/arrow-back.svg" size="md" bgColorClass="bg-gray-500" data-tippy-content="Leave the game" class="flex sm:hidden w-10"></lit-button>
        <lit-button label="LEAVE" x-on:click="leave" size="md" bgColorClass="bg-gray-500" data-tippy-content="Leave the game" class="hidden sm:flex md:hidden w-20"></lit-button>
        <lit-button label="LEAVE" x-on:click="leave" imgPath="/static/img/icon/arrow-back.svg" size="md" bgColorClass="bg-gray-500" data-tippy-content="Leave the game" class="hidden md:flex w-32"></lit-button>
      @endif
    </div>

    <div class="flex sm:hidden justify-center items-center">
      <!-- Spectate button -->
      @if($user->can_observe)
        <span x-tippy="gameUser.is_observer ? 'Do not spectate the game' : 'Spectate the game'">
          <div x-show="user.is_host" class="flex relative left-2 cursor-pointer group" x-on:click="toggleUserObserverStatus">
            <div 
              class="absolute top-[2px] left-[79px] w-[4px] h-[36px] z-40"
              :class="{ 
                'bg-gray-500 group-hover:bg-gray-600': !gameUser.is_observer, 
                'bg-gray-600 group-hover:bg-gray-500': gameUser.is_observer 
              }"></div>

            <div
              class="flex justify-center items-center w-20 h-10 z-20 border-y-2 border-l-2 border-gray-0 rounded-l-sm"
              :class="{ 
                'bg-gray-500 group-hover:bg-gray-600': !gameUser.is_observer, 
                'bg-gray-600 group-hover:bg-gray-500': gameUser.is_observer 
              }"
              style="box-shadow: 0 4px 0 0 rgba(0, 0, 0, 0.5)">
              <div class="flex flex-col justify-center items-center relative left-1 group-active:top-[2px] z-50">
                <span class="font-heading font-semibold text-lg text-gray-0 text-stroke-2 text-stroke-gray-700 leading-none">Spectate</span>
              </div>
            </div>

            <div
              class="z-10 w-4 h-10 -ml-1 -skew-x-12 border-2 border-gray-0 rounded-r-sm"
              :class="{ 
                'bg-gray-500 group-hover:bg-gray-600': !gameUser.is_observer, 
                'bg-gray-600 group-hover:bg-gray-500': gameUser.is_observer 
              }"
              style="box-shadow: 0 4px 0 0 rgba(0, 0, 0, 0.5)">
            </div>
          </div>
        </span>
      @endif

      <!-- Ready Button Small -->
      <div class="flex pl-1 relative left-4 group" :class="{ 'cursor-pointer': !gameUser.is_observer }" x-tippy="gameUser.is_observer ? '' : gameUser.is_ready ? 'Cancel readiness' : 'Join the game when ready'" x-on:click="toggleUserReadyStatus()">
        <div
          class="z-10 w-6 h-12 -mr-1.5 -skew-x-12 border-2 border-gray-0 rounded-l-sm transition-colors duration-300 ease-in-out"
          :class="[playButtonBgColor]"
          style="box-shadow: 0 4px 0 0 rgba(0, 0, 0, 0.5)">
        </div>

        <div class="absolute top-[2px] left-[32px] w-[2px] h-[44px] z-40 transition-colors duration-300 ease-in-out" :class="[playButtonBgColor]"></div>

        <div
          class="flex justify-center items-center w-[196px] h-12 z-20 pr-2 border-2 border-l-0 border-gray-0 rounded-r-sm transition-colors duration-300 ease-in-out"
          :class="[playButtonBgColor]"
          style="box-shadow: 0 4px 0 0 rgba(0, 0, 0, 0.5)">

          <div class="flex flex-col justify-center items-center relative z-50" :class="[gameUser.is_observer ? '' : 'group-active:top-[2px]']">
            <span
              x-text="gameUser.is_observer ? 'READY TO SPECTATE' : gameUser.is_ready ? 'CANCEL' : 'READY!'"
              class="font-heading font-bold text-gray-0 text-stroke-2 text-stroke-gray-700 leading-none"
              :class="[gameUser.is_observer ? 'text-lg' : gameUser.is_ready ? 'text-2xl' : 'text-4xl']">
            </span>
            <div class="items-center gap-1" :class="[gameUser.is_observer || gameUser.is_ready ? 'flex' : 'hidden']">
              <span class="font-medium text-xs text-gray-0 text-stroke-2 text-stroke-gray-700">Waiting for game to start...</span>
              <div class="text-white">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                  <style>.spinner_ajPY{transform-origin:center;animation:spinner_AtaB 1s infinite linear}@keyframes spinner_AtaB{100%{transform:rotate(360deg)}}</style>
                  <path d="M12,1A11,11,0,1,0,23,12,11,11,0,0,0,12,1Zm0,19a8,8,0,1,1,8-8A8,8,0,0,1,12,20Z" opacity=".25"/>
                  <path d="M10.14,1.16a11,11,0,0,0-9,8.92A1.59,1.59,0,0,0,2.46,12,1.52,1.52,0,0,0,4.11,10.7a8,8,0,0,1,6.66-6.61A1.42,1.42,0,0,0,12,2.69h0A1.57,1.57,0,0,0,10.14,1.16Z" class="spinner_ajPY"/>
                </svg>
              </div>          
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Centered Buttons -->
    <div class="hidden sm:flex items-end gap-2 absolute -top-2 left-1/2 -translate-x-1/2 z-10">
      <!-- Edit Button -->
      <span data-tippy-content="Edit game settings">
        <div x-show="user.is_host" class="flex relative cursor-pointer group" x-on:click="openEditGameSettingsDialog">
          <div class="absolute top-[2px] left-[79px] w-[4px] h-[36px] z-40 bg-iris-600 group-hover:bg-iris-700 transition-colors duration-300 ease-in-out"></div>

          <div
            class="flex justify-center items-center w-20 h-10 z-20 border-y-2 border-l-2 border-gray-0 rounded-l-sm bg-iris-600 group-hover:bg-iris-700 transition-colors duration-300 ease-in-out"
            style="box-shadow: 0 4px 0 0 rgba(0, 0, 0, 0.5)">
            <div class="flex flex-col justify-center items-center relative left-1 group-active:top-[2px] z-50">
              <span class="font-heading font-semibold text-xl text-gray-0 text-stroke-2 text-stroke-gray-700 leading-none">EDIT</span>
            </div>
          </div>

          <div
            class="z-10 w-4 h-10 -ml-1 -skew-x-12 border-2 border-gray-0 rounded-r-sm bg-iris-600 group-hover:bg-iris-700 transition-colors duration-300 ease-in-out"
            style="box-shadow: 0 4px 0 0 rgba(0, 0, 0, 0.5)">
          </div>
        </div>
      </span>

      <!-- Ready Button -->
      <div class="flex relative group" :class="{ 'cursor-pointer': !gameUser.is_observer }" x-tippy="gameUser.is_observer ? '' : gameUser.is_ready ? 'Cancel readiness' : 'Join the game when ready'" x-on:click="toggleUserReadyStatus()">
        <div
          class="z-10 w-6 h-14 -mr-1.5 -skew-x-12 border-2 border-gray-0 rounded-l-sm transition-colors duration-300 ease-in-out"
          :class="[playButtonBgColor]"
          style="box-shadow: 0 4px 0 0 rgba(0, 0, 0, 0.5)">
        </div>

        <div class="absolute top-[2px] left-[16px] w-[4px] h-[52px] z-40 transition-colors duration-300 ease-in-out" :class="[playButtonBgColor]"></div>
        <div class="absolute top-[2px] right-[16px] w-[4px] h-[52px] z-40 transition-colors duration-300 ease-in-out" :class="[playButtonBgColor]"></div>

        <div
          class="flex justify-center items-center w-40 min-[400px]:w-[196px] h-14 z-20 border-y-2 border-gray-0 transition-colors duration-300 ease-in-out"
          :class="[playButtonBgColor]"
          style="box-shadow: 0 4px 0 0 rgba(0, 0, 0, 0.5)">
          <div class="flex flex-col justify-center items-center relative z-50" :class="[gameUser.is_observer ? '' : 'group-active:top-[2px]']">
            <span
              x-text="gameUser.is_observer ? 'READY TO SPECTATE' : gameUser.is_ready ? 'CANCEL' : 'READY!'"
              class="font-heading font-bold text-gray-0 text-stroke-2 text-stroke-gray-700 leading-none"
              :class="[gameUser.is_observer ? 'text-xl' : gameUser.is_ready ? 'text-2xl' : 'text-4xl']">
            </span>
            <div class="items-center gap-2" :class="[gameUser.is_observer || gameUser.is_ready ? 'flex' : 'hidden']">
              <span class="font-medium text-sm text-gray-0 text-stroke-2 text-stroke-gray-700">Waiting for game to start...</span>
              <div class="text-white">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                  <style>.spinner_ajPY{transform-origin:center;animation:spinner_AtaB 1s infinite linear}@keyframes spinner_AtaB{100%{transform:rotate(360deg)}}</style>
                  <path d="M12,1A11,11,0,1,0,23,12,11,11,0,0,0,12,1Zm0,19a8,8,0,1,1,8-8A8,8,0,0,1,12,20Z" opacity=".25"/>
                  <path d="M10.14,1.16a11,11,0,0,0-9,8.92A1.59,1.59,0,0,0,2.46,12,1.52,1.52,0,0,0,4.11,10.7a8,8,0,0,1,6.66-6.61A1.42,1.42,0,0,0,12,2.69h0A1.57,1.57,0,0,0,10.14,1.16Z" class="spinner_ajPY"/>
                </svg>
              </div>          
            </div>
          </div>
        </div>

        <div
          class="z-10 w-6 h-14 -ml-1.5 skew-x-12 border-2 border-gray-0 rounded-r-sm transition-colors duration-300 ease-in-out"
          :class="[playButtonBgColor]"
          style="box-shadow: 0 4px 0 0 rgba(0, 0, 0, 0.5)">
        </div>
      </div>

      <!-- Start Button -->
      <span data-tippy-content="Force the game to start">
        <div x-show="user.is_host" class="flex relative cursor-pointer group" x-on:click="openConfirmStartGameDialog">
          <div class="absolute top-[2px] right-[79px] w-[4px] h-[36px] z-40 bg-iris-600 group-hover:bg-iris-700 transition-colors duration-300 ease-in-out"></div>

          <div
            class="z-10 w-4 h-10 -mr-1 skew-x-12 border-2 border-gray-0 rounded-l-sm bg-iris-600 group-hover:bg-iris-700 transition-colors duration-300 ease-in-out"
            style="box-shadow: 0 4px 0 0 rgba(0, 0, 0, 0.5)">
          </div>

          <div
            class="flex justify-center items-center w-20 h-10 z-20 border-y-2 border-r-2 border-gray-0 rounded-r-sm bg-iris-600 group-hover:bg-iris-700 transition-colors duration-300 ease-in-out"
            style="box-shadow: 0 4px 0 0 rgba(0, 0, 0, 0.5)">
            <div class="flex flex-col justify-center items-center relative right-1 group-active:top-[2px] z-50">
              <span class="font-heading font-semibold text-xl text-gray-0 text-stroke-2 text-stroke-gray-700 leading-none">START</span>
            </div>
          </div>
        </div>
      </span>
    </div>

    <!-- Spectate Button -->
    @if($user->can_observe)
      <span x-tippy="gameUser.is_observer ? 'Do not spectate the game' : 'Spectate the game'" class="hidden sm:flex">
        <lit-button label="SPECTATE" isSelectable :isSelected="gameUser.is_observer" size="md" bgColorClass="bg-gray-500" class="flex md:hidden w-20" x-on:click="toggleUserObserverStatus"></lit-button>
        <lit-button label="SPECTATE" imgPath="/static/img/icon/eye.svg" isSelectable :isSelected="gameUser.is_observer" size="md" bgColorClass="bg-gray-500" class="hidden md:flex w-32" x-on:click="toggleUserObserverStatus"></lit-button>
      </span>
    @endif
  </div>
</div>

<script>
  const GameStartStatus = Object.freeze({
    WAITING_FOR_PLAYERS: 0,
    QUEUED: 1,
    SELECTING_PANORAMAS: 2
  });

  function state() {
    return {
      animationDurationMs: 700,
      gameUserListMarginTopPx: 8,
      game: @json($game),
      gameUsers: @json($game_users),
      handleGameStatusInterval: null,
      gameStartStatus: GameStartStatus.WAITING_FOR_PLAYERS,
      gameStartText: 'Waiting for players...',
      user: @json($user),
      get playButtonBgColor() {
        return this.gameUser.is_observer ? 'bg-gray-600' : this.gameUser.is_ready ? 'bg-gray-500 group-hover:bg-gray-600' : 'bg-pistachio-500 group-hover:bg-pistachio-600'
      },
      get playerPanelHeaderReadyBgColor() {
        const readyPlayerPercentage = Math.floor(this.readyPlayerCount * 100 / this.playerCount);

        if (this.playerCount === 0 || readyPlayerPercentage < 50) {
          return 'bg-poppy-500'
        } else if (readyPlayerPercentage < 100) {
          return 'bg-yellow-400'
        }
        return 'bg-pistachio-500'
      },
      get gameUserCount() {
        return this.gameUsers.length;
      },
      get gameUser() {
        return this.gameUsers.find(n => n.id === this.user.id);
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
        const name = this.gameUser.map_style_short_name.toLowerCase();
        return name.charAt(0).toUpperCase() + name.slice(1);
      },
      get observerCount() {
        return this.gameUsers.filter(n => n.is_observer).length;
      },
      get observerList() {
        return this.gameUsers
          .filter(n => n.is_observer)
          .sort((a, b) => (b.is_host ? 1 : 0) - (a.is_host ? 1 : 0));
      },
      get playerCount() {
        return this.gameUsers.filter(n => !n.is_observer).length;
      },
      get playerList() {
        return this.gameUsers
          .filter(n => !n.is_observer)
          .sort((a, b) => (b.is_host ? 1 : 0) - (a.is_host ? 1 : 0));
      },
      get readyPlayerCount() {
        return this.playerList.filter(n => n.is_ready).length;
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
      startGame() {
        fetch(`/web-api/game/${this.game.id}/force-start`, {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
          },
        });
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
      openConfirmStartGameDialog() {
        this.$refs.confirmStartGameDialog.open();
      },
      openEditGameSettingsDialog() {
        this.$refs.editGameSettingsDialog.open();
      },
      openLoginDialog() {
        this.$refs.loginDialog.open()
      },
      openSelectMapMarkerDialog() {
        this.$refs.selectMapMarkerDialog.open();
      },
      openSelectMapStyleDialog() {
        let dialog = document.querySelector('lit-select-map-style-dialog');
        if (!dialog) {
          dialog = document.createElement('lit-select-map-style-dialog');
          dialog.setAttribute('selectedLocationMarkerEnum', `${this.user.map_location_marker_enum}`);
          dialog.setAttribute('selectedMapStyleEnum', `${this.gameUser.map_style_enum}`);
          dialog.setAttribute('userLevel', `${this.gameUser.level}`);
          document.body.appendChild(dialog);
        }
        dialog.addEventListener('canceled', () => {
          dialog.remove()
        })
        dialog.addEventListener('submitted', (data) => {
          this.user = { ...this.user, map_location_marker_enum: data.detail.mapLocationMarkerEnum }
          dialog.remove()
        })
        dialog.addEventListener('closed', (data) => {
          dialog.remove()
        })
        dialog.open();
      },
      openSelectUserProfileDialog() {
        this.$refs.selectUserProfileDialog.open();
      },
      toggleUserObserverStatus() {    
        const isObserver = !this.gameUser.is_observer
     
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
        .then(() =>  this.setUserReadyStatus(isObserver))
        .catch(error => {
            console.error('Error updating observer status:', error);
        });
      },
      setUserReadyStatus(isReady) {
        return fetch(`/web-api/game-user/${this.game.id}`, {
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
      toggleUserReadyStatus() {
        if (this.gameUser.is_observer) return
        this.setUserReadyStatus(!this.gameUser.is_ready);
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
        // Websockets
        const webSocketClient = WebSocketClient.init();
        const channel = webSocketClient.subscribeToChannel(`game.${this.game.id}`);
        
        channel.bind('game.deleted', () => {
          window.location.href = '/';
        });
        channel.bind('game.updated', ({ game }) => {
          this.game = game;
        });
        channel.bind('game.stage.updated', ({ message, stage }) => {
          if (stage === -1) {
            this.gameStartStatus = GameStartStatus.WAITING_FOR_PLAYERS
            this.gameStartText = 'Waiting for players...'
          } else if (stage === 1) {
            this.gameStartStatus = GameStartStatus.QUEUED
            this.gameStartText = 'The game is queued!'
          } else if (stage === 2) {
            this.gameStartStatus = GameStartStatus.SELECTING_PANORAMAS
          } else if (stage === 3) {
            // Selecting panorama one by one
            this.gameStartStatus = GameStartStatus.SELECTING_PANORAMAS
            this.gameStartText = message
          }
        });
        channel.bind('game.round.updated', ({ roundNumber, gameStateEnum }) => {
          window.location.href = `/game/${this.game.id}/play`;
        });
        channel.bind('game-user.joined', ({ gameUser }) => {
          if (!this.gameUsers.find(n => n.id === gameUser.id)) {
            this.gameUsers.push(gameUser);
          }
        });
        channel.bind('game-user.updated', ({ gameUser }) => {
          this.updatePlayer(gameUser);    
        });
        channel.bind('game-user.left', ({ userId }) => {
          // Because leaving the game is a request, it takes time. If I remove the user in the player list,
          // it creates bugs on the page because the user not exist anymore.
          if (this.gameUser.id !== userId) {
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
      game: @json($game),
      iconTooltipText: 'Copy game invite link',
      getGameLink() {
        return `{{ config('app.url') }}/g/${this.game.short_code}`.replace(/^https?:\/\//, '');
      },
      copyUrlToClipboard() {
        navigator.clipboard.writeText(`https://${this.getGameLink()}`);
        this.iconTooltipText = 'Copied';
        this.refreshTooltip();
      },
      initTippy() {
        tippy(this.$refs.clipboardIcon, {
          content: this.iconTooltipText,
          hideOnClick: false,
          onHidden: (instance) => {
            instance.setContent(this.iconTooltipText);
            this.$refs.clipboardIcon._tippy.setContent('Copy game invite link');
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