<?php declare(strict_types=1); 
  use Web\Www\Game\Util\GameUtil;
?>

<div x-data="state" class="flex flex-col h-screen bg-iris-200">
  <div class="flex justify-between items-center h-12 relative border-b-2 border-gray-700 bg-iris-500 shadow-lg">
    <div class="flex flex-1 pl-2">
      <lit-button imgPath="/static/img/icon/cross.svg" size="md" bgColorClass="bg-red-500" x-on:clicked="navigateHome()"></lit-button>
    </div>
    {{-- <div class="flex h-14 absolute top-0 left-0">
      <div class="flex justify-center items-center w-12 relative z-20 border-b border-gray-700 bg-[#E83D2D] inner-shadow">
        <img class="w-8 relative left-[6px] z-20" src="/static/img/icon/cross.svg" />
        <div class="w-6 h-14 absolute top-0 -right-[10px] z-10 -skew-x-12 rounded-br border-r border-b border-gray-700 bg-[#E83D2D] inner-shadow"></div>
      </div>
    </div> --}}
    <div class="flex justify-center flex-grow">
      <span class="font-heading text-2xl font-bold text-white text-stroke-2 text-stroke-iris-900">GAME RESULT</span>
    </div>
    <div class="flex-1"></div>
  </div>

  <lit-panel label="Stats" class="relative mx-2 mt-5">
    <div class="flex flex-col gap-4 px-2 pt-4 pb-2">
      <div class="flex h-16 gap-2">
        <div class="flex shrink-0 justify-center items-end">
          <img src="{{ $user->map_marker_file_path }}" class="max-w-full max-h-full" />
        </div>

        <div class="flex flex-col flex-grow mr-24 sm:mr-32 justify-between">
          <span class="font-heading text-xl font-bold text-white text-stroke-2 text-stroke-iris-900">{{ $user->display_name }}</span>
          <div class="flex gap-2">
            <div class="flex flex-none justify-center items-center relative">
              <span class="absolute text-white font-heading text-xl font-bold text-stroke-2 text-stroke-iris-900">{{ $user->level }}</span>
              <img class="w-8" src="/static/img/icon/emblem.svg" />
            </div>
            <div class="flex flex-col items-center w-full max-w-64">
              <span class="font-heading text-sm font-semibold text-white text-stroke-2 text-stroke-iris-900">{{ $user->current_level_experience_points }}/{{ $user->next_level_experience_points_requirement }}</span>
              <lit-progress-bar class="w-full relative bottom-0.5" percentage="{{ $levelPercentage }}" innerBgColorClass="bg-yellow-500" tippy="{{ $levelPercentage }}%"></lit-progress-bar>
            </div>
          </div>
        </div>
        <div class="flex flex-none w-24 sm:w-32 h-[86px] absolute -top-[8px] -right-1 justify-center items-center">
          <div class="flex relative left-1 z-10 items-end">
            <span class="font-heading text-5xl sm:text-6xl text-gray-50 font-bold text-stroke-2 text-stroke-gray-700">{{ $user->rank }}</span>
            <span class="relative bottom-1 font-heading text-xl sm:text-2xl text-gray-50 font-bold text-stroke-2 text-stroke-gray-700">{{ GameUtil::getOrdinalSuffix($user->rank) }}</span>
          </div>
          <svg class="absolute w-full h-full" viewBox="0 0 123 74" fill="none" xmlns="http://www.w3.org/2000/svg" preserveAspectRatio="none">
            <g filter="url(#filter0_i_1214_13529)">
              <path d="M16.6769 3.0332C17.1208 1.25091 18.7216 0 20.5583 0H119C121.209 0 123 1.79086 123 4V70C123 72.2091 121.209 74 119 74H4.11857C1.5177 74 -0.391467 71.5569 0.237167 69.0332L16.6769 3.0332Z" fill="{{ GameUtil::getHexaColorByRank($user->rank) }}"/>
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
      </div>
      <div class="flex justify-around p-2 rounded border border-iris-500 bg-iris-300">
        <div class="flex flex-col items-center">
          <span class="font-heading text-base font-bold text-white text-stroke-2 text-stroke-iris-900">Points</span>
          <div class="flex justify-center items-center w-32 h-6 relative rounded bg-iris-500">
            <img src="/static/img/icon/star-gold.svg" class="w-8 aspect-auto absolute -top-[6px] left-0 transform -translate-x-1/2" />
            <span class="font-heading text-lg font-bold text-white text-stroke-2 text-stroke-iris-900">{{ $user->points }}</span>
          </div>
        </div>
        <div class="flex flex-col items-center">
          <span class="font-heading text-base font-bold text-white text-stroke-2 text-stroke-iris-900">Experience</span>
          <div class="flex justify-center items-center w-32 h-6 relative rounded bg-iris-500">
            <div class="flex justify-center items-center absolute left-0 transform -translate-x-1/2">
              <span class="absolute text-white font-heading text-xl font-bold text-stroke-2 text-stroke-iris-900">XP</span>
              <img class="w-8" src="/static/img/icon/emblem.svg" />
            </div>
            <span class="font-heading text-lg font-bold text-white text-stroke-2 text-stroke-iris-900">+{{ $game->experience_points }}</span>
          </div>
        </div>
      </div>
    </div>
  </lit-panel>

  <div class="flex flex-col flex-1 min-h-0">
    <lit-panel label="Ranking" class="mx-2 mt-5 mb-3 min-h-0">
      <div class="flex flex-col flex-1 overflow-y-auto gap-2 px-2 py-4">
        @foreach ($players as $player)
          <lit-player-result-item
            flagFilePath="{{ $player->flag_file_path }}"
            flagDescription="{{ $player->flag_description }}"
            rank="{{ $player->rank }}"
            rankSelected="{{ $player->rank === $user->rank ? $player->rank : '' }}"
            honorificTitle="Digital Guinea Pig"
            name="{{ $player->display_name }}"
            iconPath="{{ $player->map_marker_file_path }}"
            points="{{ $player->points }}"
            countryCCA2="{{ $player->country_cca2 }}"
            level="{{ $player->level }}">
          </lit-player-result-item>
        @endforeach
      </div>
    </lit-panel>
  </div>
  
  <lit-round-list 
    rounds="{{ json_encode($rounds) }}"
    totalRoundCount="{{ count($rounds) }}"
    class="border-t border-gray-700 bg-iris-500"
    x-on:clicked="selectCountry($event)"
  ></lit-round-list>
</div>

<script>
  function state() {
    return {
      navigateHome() {
        window.location.href = '/';
      },
      selectCountry(e) {
        console.log(e.detail.countryCCA2);
      },
      init() {
        const userRank = parseInt('{{ $user->rank }}');
        if (userRank === 1) {
          setTimeout(() => {
            window.confetti({
              particleCount: 150
            });
          }, 500);
        }
      }
    }
  }
</script>