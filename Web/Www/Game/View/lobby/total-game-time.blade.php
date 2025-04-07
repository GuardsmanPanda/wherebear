<?php declare(strict_types=1); ?>

<div class="flex justify-self-end justify-between items-center relative w-full h-8 sm:h-12 rounded-sm sm:rounded-none px-2 bg-iris-200 border sm:border-0 sm:border-t border-iris-700">
  <div class="w-40 h-full absolute right-0 bg-iris-500" style="clip-path: polygon(8px 0, 100% 0, 100% 100%, 0 100%)"></div>
  <span class="font-heading font-semibold text-lg text-gray-800">Total Game Time</span>
  <div class="flex items-center gap-2 z-10">
    <img src="/static/img/icon/chronometer.svg" class="h-6" draggable="false" />
    <span x-text="`~${game.total_game_time_mn} minute${game.total_game_time_mn > 1 ? 's' : ''}`" 
      class="font-heading font-semibold text-xl text-gray-0 text-stroke-2 text-stroke-iris-700">
    </span>
  </div>
</div>