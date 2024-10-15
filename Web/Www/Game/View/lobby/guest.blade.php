<?php declare(strict_types=1); ?>
<div class="grid-cols-1 grid container mx-auto w-96 mt-2">
  <h1 class="text-center font-bold text-xl">{{$game->name}}</h1>
  <div class="text-center text-sm text-gray-400 -mt-1">{{$game->number_of_rounds}} rounds, {{count($players)}} players.</div>
  <hr class="mx-5 mt-3 mb-3 pb-0.5 border-gray-700 border-dashed border-b-2">
  <div class="mx-auto text-orange-600 font-medium text-xl">
    Please log in or join as a guest to play.
  </div>
  <div>
    <div class="text-sm">Optional - Name Yourself.</div>
    <input id="display_name" name="display_name" class="w-48 mt-2 px-2 py-1 border-2 border-gray-700 rounded" type="text">
  </div>
  <button class="mx-auto ring-1 font-medium shadow shadow-gray-700 ring-gray-700 px-4 py-1 mt-2 hover:scale-110 rounded duration-75 transition-all"
          hx-post="/auth/guest" hx-vals='{"game_id": "{{$game->id}}"}' hx-include="#display_name">
    Continue as Guest
  </button>
  <hr class="mx-5 mt-4 mb-2 pb-0.5 border-gray-700 border-dashed border-b-2">
  <div class="mx-auto text-gray-600 font-medium text-xl">Players</div>
  <ul id="player-list" class="mt-2">
    @foreach($players as $player)
      @if(!$loop->first)
        <hr class="mx-5 mt-1 mb-1 pb-0.5 border-gray-700 border-dashed">
      @endif
      <li class="flex items-center">
        <img class="h-8 w-8 mr-2" src="{{$player->map_marker_file_path}}" alt="{{$player->map_marker_file_path}}">
        <img class="w-8 mr-2" src="/static/flag/svg/{{$player->country_cca2}}.svg" alt="{{$player->country_name}}"
             tippy="{{$player->country_name}}">
        <div>
          <p class="font-semibold">
            {{$player->display_name}}
          </p>
          @if($player->is_ready)
            <p class="text-green-400 text-xs -mt-1 font-medium">ready</p>
          @else
            <p class="text-gray-400 text-xs -mt-1">waiting</p>
          @endif
        </div>
      </li>
    @endforeach
  </ul>
</div>
