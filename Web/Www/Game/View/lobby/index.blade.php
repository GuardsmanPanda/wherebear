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

  <lit-confirm-dialog x-ref="confirmDeleteGameDialog" 
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

  <!-- Game Starting Dialog -->
  <dialog x-ref="gameStartingDialog" class="hidden fixed inset-0 w-full h-full pointer-events-none transition-opacity duration-300 p-4 bg-gray-900/50 items-center justify-center z-50">
    <div class="@container flex flex-col w-full relative max-w-2xl overflow-hidden animate-pop rounded border-2 border-gray-700 bg-gray-700">
      <!-- Header SM -->
      <div class="flex @xl:hidden justify-end items-center w-full h-14 z-10 px-2 rounded-t border-b border-gray-700 bg-gradient-to-r from-iris-400 to-iris-300 shadow-sm/60"></div>

      <!-- Top Left Corner LG -->
      <div class="hidden @xl:block">
        <img src="/static/img/ui/game-starting-dialog/top-left-corner.png" class="absolute -top-px -left-px drop-shadow-sm/60" />
      </div>

      <!-- Text -->
      <div class="flex flex-col justify-center gap-1 absolute top-1.5 left-2">
        <span class="heading-2xl text-gray-0 uppercase z-10 leading-none">STARTING GAME...</span>
        <span x-text="gameStartingText" class="font-heading font-regular text-base text-gray-800 z-10 leading-none"></span>
      </div>

      <!-- Background Image -->
      <div x-ref="gameStartingDialogBackgroundImage" class="w-full h-32 bg-[url('/static/img/ui/game-starting-dialog/background-mountain.png')] bg-center animate-[backgroundScrollX_48s_linear_1_forwards]"></div>

      <!-- Labels -->
      <div class="flex justify-end @xl:justify-center items-center gap-4 absolute top-14 @xl:top-0 @xl:left-[256px] right-0 @xl:right-[64px] p-2">
        <lit-label :label="`${playerCount} player${playerCount === 1 ? '' : 's'}`" size="sm" color="gray" icon="person" class="w-[114px] drop-shadow-sm/60"></lit-label>
        <lit-label :label="`${game.total_game_time_mn} min`" size="sm" color="gray" icon="chronometer" class="w-28 drop-shadow-sm/60"></lit-label>
      </div>

      <!-- Top Right Circle LG -->
      <div class="hidden @xl:flex size-[112px] absolute top-0 translate-x-[calc(50%-10px)] right-0 -translate-y-1/2 z-10 rounded-full border border-gray-700 bg-radial from-iris-400 to-iris-300 shadow-sm/60"></div>

      <!-- Top Right Countdown -->
      <div class="flex w-10 absolute top-2 @xl:top-1 z-20" :class="{ '-right-0.5': gameStartingDialogCountdownSec <= 9, 'right-1': gameStartingDialogCountdownSec >= 10 }">
        <span x-ref="gameStartingDialogCountdown" x-text="gameStartingDialogCountdownSec" class="heading-4xl text-gray-0" :class="animateCountdownPop ? 'animate-countdown-pop' : ''"></span>
      </div>

      <!-- Road -->
      <div x-ref="gameStartingDialogRoad" class="w-[728px] h-[42px] relative z-10 bg-[url('/static/img/ui/game-starting-dialog/road.png')] bg-repeat-x animate-[backgroundScrollX_4s_linear_infinite_reverse]"></div>

      <!-- Car -->
      <img x-ref="gameStartingDialogVehicle" src="/static/img/ui/game-starting-dialog/van.png" class="absolute bottom-[54px] @xl:bottom-[46px] -left-24 z-20" />

      <!-- Start Road Sign -->
      <img x-ref="gameStartingDialogRoadSignStart" src="/static/img/ui/game-starting-dialog/road-sign-start.png" class="absolute top-[154px] @xl:top-[98px] -right-[160px] z-10" />

      <!-- Tip -->
      <div class="flex h-12 @xl:h-10 relative bg-gray-600 border-t border-gray-700" style="box-shadow: inset 0px 2px 2px rgba(0, 0, 0, 0.25)">
        <img src="/static/img/ui/game-starting-dialog/bottom-left-corner-lg.png" class="block @xl:hidden absolute -bottom-[2px] -left-[2px] z-10" />
        <img src="/static/img/ui/game-starting-dialog/bottom-left-corner-sm.png" class="hidden @xl:block absolute -bottom-[2px] -left-[2px] z-10" />
        
        <div class="flex w-20 h-full justify-center items-center gap-1.5 absolute bottom-[3px] left-[3px] z-20">
          <lit-icon name="info" class="flex h-8 drop-shadow-[0_1px_0px_rgba(25_28_37_/_1)]"></lit-icon>
          <span class="heading-2xl text-gray-0">TIP</span>
        </div>
        <div class="flex items-center ml-[104px] px-2 leading-none">
          <span class="font-heading font-regular text-sm/3 @xl:text-sm text-gray-0">Pay attention to which side of the road the cars are driving on — it might just point you in the right direction!</span>
        </div>
      </div>
    </div>
  </dialog>


  <!-- Header -->
  <div class="flex flex-col h-24 bg-iris-500 relative">
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
        <lit-label x-show="game.type === 'template'" label="Template" size="sm" color="gray" class="w-20" data-tippy-content="Game type: Fixed panoramas"></lit-label>
        <lit-label x-show="game.type === 'normal'" label="Normal" size="sm" color="gray" class="w-20"  data-tippy-content="Game type: Randomized panoramas"></lit-label>
        <lit-label  
          :label="game.is_public ? 'Public' : 'Private'"  
          size="sm"  
          :color="game.is_public ? 'gray' : 'red'"  
          class="w-20"  
          x-tippy="game.is_public ? 'Game access: Everyone can join' : 'Game access: Invite-only'"  
        ></lit-label> 
      </div>
    </div>
  </div>
  
  <!-- Sub Header -->
  <div class="flex justify-center gap-2 w-full p-1 bg-gray-600 border-b border-gray-700">
    <lit-label-with-value label="Rounds" :value="game.number_of_rounds" widthClass="w-full" class="w-full max-w-32" data-tippy-content="Total number of rounds in the game"></lit-label-with-value>
    <lit-label-with-value label="Guess" :value="`${game.round_duration_seconds}s`" widthClass="w-full" class="block sm:hidden w-full max-w-32" data-tippy-content="Time limit to make a guess per round"></lit-label-with-value>
    <lit-label-with-value label="Guessing Time" :value="`${game.round_duration_seconds}s`" widthClass="w-full" class="hidden sm:block w-full max-w-32" data-tippy-content="Time limit to make a guess per round"></lit-label-with-value>
    <lit-label-with-value label="Result" :value="`${game.round_result_duration_seconds}s`" widthClass="w-full" class="block sm:hidden w-full max-w-32" data-tippy-content="Duration of result display after each round"></lit-label-with-value>
    <lit-label-with-value label="Result Time" :value="`${game.round_result_duration_seconds}s`" widthClass="w-full" class="hidden sm:block w-full max-w-32" data-tippy-content="Duration of result display after each round"></lit-label-with-value>
    <lit-label :label="`${game.total_game_time_mn} min`" color="orange" class="flex min-w-16 sm:hidden" data-tippy-content="Estimated total game duration"></lit-label>
    <lit-label :label="`${game.total_game_time_mn} minute${game.total_game_time_mn > 1 ? 's' : ''}`" size="sm" color="orange" icon="chronometer" class="hidden sm:flex" data-tippy-content="Estimated total game duration"></lit-label>
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
          <lit-button label="Log in" size="sm" color="yellow" class="block sm:hidden w-20" x-on:clicked="openLoginDialog"></lit-button>
          <lit-button label="Log in" size="md" color="yellow" class="hidden sm:block w-24" x-on:clicked="openLoginDialog"></lit-button>
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
                        :label="lowercaseMapStyleShortName"
                        size="sm"
                        icon="map"
                        contentAlignment="left"
                        class="flex w-28"
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
    <div class="hidden md:flex flex-col shrink-0 w-[320px] h-full z-10 border-l border-gray-700 bg-iris-400" style="box-shadow: -2px 0 2px rgba(0, 0, 0, 0.25)">
      <!-- Player List -->
      <lit-panel-header2 label="PLAYERS" noBorder noRounded class="mt-2 border-y border-gray-700">
        <div slot="right">
          @include('game::lobby.player-header-right')
        </div>
      </lit-panel-header2>
      <div class="min-h-[81px] overflow-y-auto mb-auto">
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
                  class="w-[80px] ml-1">
                </lit-label>

                 <lit-label x-show="gameUser.is_observer"
                  label="Observer"
                  size="xs"
                  color="blue"
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
      <div x-show="observerCount > 0" class="min-h-[81px] overflow-y-auto">
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
                  color="gray"
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

      <lit-panel-header2 label="ACTIVITY FEED" noBorder noRounded class="border-y border-gray-700" :class="{}"></lit-panel-header2>
      <div x-ref="activityFeed" class="flex flex-col flex-1 min-h-[160px] max-h-[240px] shrink-0 overflow-y-auto bg-gray-50">
        <div class="flex flex-col justify-end grow">
          <template x-for="message in activityFeedMessages">
            <div class="flex flex-col p-2 odd:bg-iris-50 even:bg-iris-100">
              <div class="flex justify-between">
                <lit-label
                  :label="activityFeedLabelMap[message.type] || 'UNKNOWN'"
                  size="xs"
                  class="w-[108px]"
                  :color="activityFeedLabelColorMap[message.type]">
                </lit-label>
                <span x-text="message.date.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });" class="font-heading font-medium text-sm text-gray-500"></span>
              </div>
              <span x-text="message.text" class="text-sm text-gray-800 select-text"></span>
            </div>
          </template>
        </div>
        <div x-ref="endOfFeed"></div>
      </div>
    </div>
  </div>

  <!-- Host Control Bar -->
  @if ($user->is_host)
    <div class="flex sm:hidden justify-center items-center gap-2 absolute bottom-14 left-1/2 -translate-x-1/2 w-56 h-12 border-t border-gray-700 bg-gray-600">
      <div class="absolute top-0 -left-2 z-10 w-6 h-12 -skew-x-12 border-l border-gray-700 rounded-tl-sm bg-gray-600"></div>

      <span data-tippy-content="Edit game settings" class="z-20">
        <lit-button label="EDIT" size="md" icon="gear" class="flex w-24" x-on:clicked="openEditGameSettingsDialog"></lit-button>
      </span>
      <span data-tippy-content="Force the game to start" class="z-20">
        <lit-button label="START" size="md" icon="arrow_right" class="flex w-24" x-on:clicked="openConfirmStartGameDialog"></lit-button>
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
          <lit-button icon="cross" size="md" color="red" class="flex md:hidden w-10" x-on:clicked="openConfirmDeleteGameDialog"></lit-button>
          <lit-button label="DELETE" size="md" color="red" class="hidden md:flex w-32" x-on:clicked="openConfirmDeleteGameDialog"></lit-button>
        </span>
      @else
        <lit-button x-on:click="leave" icon="arrow_back" size="md" color="gray" data-tippy-content="Leave the game" class="flex sm:hidden w-10"></lit-button>
        <lit-button label="LEAVE" x-on:click="leave" size="md"color="gray" data-tippy-content="Leave the game" class="hidden sm:flex md:hidden w-20"></lit-button>
        <lit-button label="LEAVE" x-on:click="leave" icon="arrow_back" size="md" color="gray" data-tippy-content="Leave the game" class="hidden md:flex w-32"></lit-button>
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
        <div x-show="user.can_edit" class="flex relative cursor-pointer group" x-on:click="openEditGameSettingsDialog">
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
        <lit-button label="SPECTATE" isSelectable :isSelected="gameUser.is_observer" size="md" color="gray" class="hidden md:flex w-32" x-on:click="toggleUserObserverStatus"></lit-button>
      </span>
    @endif
  </div>
</div>

<script>
  function state() {
    return {
      activityFeedMessages: [],
      activityFeedLabelMap: {
        'game-update': 'GAME UPDATE',
        'player-join': 'PLAYER JOIN',
        'player-left': 'PLAYER LEFT',
        'player-update': 'PLAYER UPDATE',
      },
      activityFeedLabelColorMap: {
        'game-update': 'orange',
        'player-join': 'green',
        'player-left': 'gray',
        'player-update': 'blue',   
      },
      animateCountdownPop: false,
      animationDurationMs: 700,
      gameUserListMarginTopPx: 8,
      game: @json($game),
      gameUsers: @json($game_users),
      handleGameStatusInterval: null,
      gameStartingText: 'The game is queued',
      gameStartingDialogCountdownSec: 0,
      user: @json($user),
      get playButtonBgColor() {
        return this.gameUser.is_observer ? 'bg-gray-600' : this.gameUser.is_ready ? 'bg-gray-500 group-hover:bg-gray-600' : 'bg-pistachio-500 group-hover:bg-pistachio-600'
      },
      get playerPanelHeaderReadyBgColor() {
        const { playerCount, readyPlayerCount, gameUsers } = this;

        if (playerCount === 0) {
          return 'bg-poppy-500';
        }

        const readyPercentage = Math.floor(readyPlayerCount * 100 / playerCount);
        const allPlayersReady = readyPlayerCount === playerCount;
        const oneNotReady = readyPlayerCount === playerCount - 1;
        const host = gameUsers.find(n => n.is_host);
        const hostIsLastNotReady = oneNotReady && host && !host.is_ready;

        if (allPlayersReady || hostIsLastNotReady) {
          return 'bg-pistachio-500';
        }

        if (readyPercentage < 50) {
          return 'bg-poppy-500';
        }

        if (readyPercentage < 100) {
          return 'bg-yellow-400';
        }

        // Default fallback, shouldn't really hit this
        return 'bg-pistachio-500';
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
      openGameStartingDialog(durationSec) {
        const dialog = this.$refs.gameStartingDialog
        dialog.classList.remove('hidden')
        dialog.classList.add('flex')
        dialog.classList.remove('pointer-events-none')
        dialog.classList.add('pointer-events-auto')

        this.startGameStartingDialogVehicleEnterAnimation()
        this.startGameStartingDialogCountdown(durationSec)
      },
      startGameStartingDialogCountdown(durationSec) {
        this.gameStartingDialogCountdownSec = durationSec;

        const interval = setInterval(() => {
          this.gameStartingDialogCountdownSec--;

          // Trigger pulse animation for last 5 seconds
          if (this.gameStartingDialogCountdownSec > 0 && this.gameStartingDialogCountdownSec <= 5) {
            this.animateCountdownPop = true;
          }

          if (this.gameStartingDialogCountdownSec === 3) {
            this.startGameStartingDialogRoadSignStartAnimation()
          }

          if (this.gameStartingDialogCountdownSec === 1) {
            this.startGameStartingDialogVehicleExitAnimation()
            this.$refs.gameStartingDialogRoad.style.animationPlayState = 'paused';
            this.$refs.gameStartingDialogBackgroundImage.style.animationPlayState = 'paused'
          }

          // Countdown reached 0, stop everything
          if (this.gameStartingDialogCountdownSec <= 0) {
            clearInterval(interval);

            // The timeout is required to stop the animation, I don't know why
            setTimeout(() => {
              this.animateCountdownPop = false;
            }, 500);
          }
        }, 1000);
      },
      startGameStartingDialogRoadSignStartAnimation() {
        const roadSignStart = this.$refs.gameStartingDialogRoadSignStart
        roadSignStart.style.transition = 'transform 2000ms linear';
        roadSignStart.style.transform = 'translateX(-280px)';
      },
      startGameStartingDialogVehicleEnterAnimation() {
        const car = this.$refs.gameStartingDialogVehicle
        car.classList.add('animate-vehicle-enter')
      },
      startGameStartingDialogVehicleExitAnimation() {
        const car = (this.$refs.gameStartingDialogVehicle)
        car.classList.remove('animate-vehicle-enter')
        car.classList.add('animate-vehicle-exit')
      },
      init() {
        // Auto scroll to the bottom of the Activity Feed panel
        this.$watch('activityFeedMessages', () => {
          this.$nextTick(() => {
            const container = this.$refs.activityFeed
            if (container) {
              container.scrollTo({ top: container.scrollHeight, behavior: 'smooth' })
            }
          })
        })

        // Websockets
        const webSocketClient = WebSocketClient.init();
        const channel = webSocketClient.subscribeToChannel(`game.${this.game.id}`);
        
        channel.bind('game.deleted', () => {
          window.location.href = '/';
        });
        channel.bind('game.updated', ({ game }) => {
          const currentGame = this.game

          if (currentGame.number_of_rounds !== game.number_of_rounds) {
            this.activityFeedMessages.push({
              type: 'game-update',
              date: new Date(),
              text: `Round count set to ${game.number_of_rounds}.`
            })
          }
          if (currentGame.round_duration_seconds !== game.round_duration_seconds){
            this.activityFeedMessages.push({
              type: 'game-update',
              date: new Date(),
              text: `Round duration set to ${game.round_duration_seconds} seconds.`
            })
          }
          if (currentGame.round_result_duration_seconds !== game.round_result_duration_seconds){
            this.activityFeedMessages.push({
              type: 'game-update',
              date: new Date(),
              text: `Round result duration set to ${game.round_result_duration_seconds} seconds.`
            })
          }
          if (currentGame.is_public !== game.is_public) {
            this.activityFeedMessages.push({
              type: 'game-update',
              date: new Date(),
              text: `Game access set to ${game.is_public ? 'Public' : 'Private' }.`
            })
          }

          this.game = game;
        });
        channel.bind('game.stage.updated', ({ message, stage, meta }) => {
          /** The delay in seconds from when the state was marked complete. */
          const postCompletionDelaySec = 3

          // QUEUE stage
          if (stage === 0) {
            this.gameStartingText = 'The game is queued'
            countdownSec = meta?.countdownSec || 100
            this.openGameStartingDialog(countdownSec) 

            setTimeout(() => {
              this.gameStartingText = `Preparation complete`
            }, (countdownSec - postCompletionDelaySec) * 1000)
          } 
          // SELECTING stage
          else if (stage === 1) {
            let round = 1
            this.gameStartingText = `Selecting Panorama...${round}/${this.game.number_of_rounds}`

            setInterval(() => {
              if (round < this.game.number_of_rounds) {
                round++;
                this.gameStartingText = `Selecting Panorama...${round}/${this.game.number_of_rounds}`
              }
            }, ((this.gameStartingDialogCountdownSec - postCompletionDelaySec - 0.5) * 1000) / this.game.number_of_rounds)
           
          }
          // START STAGE
          else if (stage === 2) {
            this.startGameStartingDialogVehicleExitAnimation()
          } 
        });
        channel.bind('game.round.updated', ({ roundNumber, gameStateEnum }) => {
          window.location.href = `/game/${this.game.id}/play`;
        });
        channel.bind('game-user.joined', ({ gameUser }) => {
          if (!this.gameUsers.find(n => n.id === gameUser.id)) {
            this.gameUsers.push(gameUser);
            
            this.activityFeedMessages.push({
              type: 'player-join',
              date: new Date(),
              text: `${gameUser.display_name} joined the game.`
            })
          }
        });
        channel.bind('game-user.updated', ({ gameUser }) => {
          const currentGameUser = this.gameUsers.find(n => n.id === gameUser.id)

          if (currentGameUser.display_name !== gameUser.display_name) {
            this.activityFeedMessages.push({
              type: 'player-update',
              date: new Date(),
              text: `${currentGameUser.display_name} renamed themselves to ${gameUser.display_name}.`
            })  
          }

          this.updatePlayer(gameUser); 
        });
        channel.bind('game-user.left', ({ userId }) => {
          // Because leaving the game is a request, it takes time. If I remove the user in the player list,
          // it creates bugs on the page because the user not exist anymore.
          if (this.gameUser.id !== userId) {
            const deletedGameUser = this.gameUsers.find(n => n.id === userId);

            this.gameUsers = this.gameUsers.filter(n => n.id !== userId);

            this.activityFeedMessages.push({
              type: 'player-left',
              date: new Date(),
              text: `${deletedGameUser.display_name} left the game.`
            }) 
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