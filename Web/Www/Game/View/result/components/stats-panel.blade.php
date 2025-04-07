<?php declare(strict_types=1); 
  use Web\Www\Game\Util\GameUtil;
?>

<lit-panel label="STATS">
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
  <div class="flex justify-around m-2 p-2 rounded-sm border border-iris-500 bg-iris-100">
    <div class="flex flex-col items-center">
      <span class="font-heading text-base font-bold text-iris-800">Points</span>
      <div class="flex justify-center items-center w-32 h-6 relative rounded-sm bg-iris-500">
        <img src="/static/img/icon/star-gold.svg" class="w-8 aspect-auto absolute -top-[6px] left-0 transform -translate-x-1/2" />
        <span class="font-heading text-lg font-bold text-white text-stroke-2 text-stroke-iris-900" tippy="{{ $user->detailed_points }}">{{ $user->rounded_points}}</span>
      </div>
    </div>
    <div class="flex flex-col items-center">
      <span class="font-heading text-base font-bold text-iris-800">Experience</span>
      <div class="flex justify-center items-center w-32 h-6 relative rounded-sm bg-iris-500">
        <div class="flex justify-center items-center absolute left-0 transform -translate-x-1/2">
          <span class="absolute text-white font-heading text-xl font-bold text-stroke-2 text-stroke-iris-900">XP</span>
          <img class="w-8" src="/static/img/icon/emblem.svg" />
        </div>
        <span class="font-heading text-lg font-bold text-white text-stroke-2 text-stroke-iris-900">+{{ $game->experience_points }}</span>
      </div>
    </div>
  </div>
</lit-panel>