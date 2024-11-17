<?php declare(strict_types=1); ?>
<div class="grid-cols-1 grid container mx-auto w-96 mt-2">
  <h1 class="text-center font-bold text-xl">{{$game->name}}</h1>
  <div class="text-center text-sm text-gray-400 -mt-1">{{$game->number_of_rounds}} rounds, {{count($players)}} players.</div>
  <hr class="mx-5 mt-3 mb-3 pb-0.5 border-gray-700 border-dashed border-b-2">
  <div class="mx-auto text-orange-600 font-medium text-xl">
    Please log in or join as a guest to play.
  </div>
  <div class="flex items-center">
    <label class="mr-2 font-medium text-gray-400" for="anonymous">Join Anonymously</label>
    <input id="anonymous" type="checkbox" name="anonymous" value="true">
  </div>
  <button
    class="whitespace-nowrap font-medium inline-flex rounded transition-all hover:scale-105 items-center shadow hover:shadow-md duration-75 focus:outline-none focus:ring-2 focus:ring-offset-2 border px-5 h-9 text-indigo-50 bg-indigo-600 border-indigo-600 shadow-indigo-600/20 focus:ring-indigo-700 mx-auto mt-2"
    hx-post="/auth/social-redirect" hx-include="#anonymous" hx-vals='{"oauth2_client": "GOOGLE", "game_id": "{{$game->id}}"}'>
    Sign In With Google
  </button>
  <button
    class="whitespace-nowrap font-medium inline-flex rounded transition-all hover:scale-105 items-center shadow hover:shadow-md duration-75 focus:outline-none focus:ring-2 focus:ring-offset-2 border px-5 h-9 text-indigo-50 bg-indigo-600 border-indigo-600 shadow-indigo-600/20 focus:ring-indigo-700 mx-auto mt-2"
    hx-post="/auth/social-redirect" hx-include="#anonymous" hx-vals='{"oauth2_client": "TWITCH", "game_id": "{{$game->id}}"}'>
    Sign In With Twitch
  </button>
  <button class="mx-auto ring-1 font-medium shadow shadow-gray-700 ring-gray-700 px-4 py-1 mt-2 hover:scale-110 rounded duration-75 transition-all"
          hx-post="/auth/guest" hx-vals='{"game_id": "{{$game->id}}"}' hx-include="#anonymous">
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
