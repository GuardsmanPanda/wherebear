<?php declare(strict_types=1); ?>
@php use GuardsmanPanda\Larabear\Infrastructure\Auth\Service\BearAuthService; @endphp
<div class="flex justify-center h-9 bg-gray-900 items-center shadow z-10">
    @if($user->is_ready)
        <div class=" text-gray-500 font-medium text-xl">Waiting For Players..</div>
        <button class="flex items-center ml-4 text-gray-500 hover:text-gray-400 ring-1 ring-gray-500 font-medium pl-2 pr-3 py-0.5 my-0.5 rounded duration-75 hover:scale-110 transition-transform"
                hx-patch="/game/{{$game->id}}/lobby/update-game-user" hx-vals='{"is_ready": false}'>
            <x-bear::icon name="x-mark" size="4" class="opacity-70 mr-0.5"/> Wait
        </button>
    @else
        <div class=" text-gray-500 font-medium text-xl animate-pulse">Click When Ready..</div>
        <button class="flex items-center ml-4 bg-yellow-400 text-yellow-800 hover:text-yellow-900 font-medium pl-2 pr-3 py-0.5 my-0.5 rounded duration-75 hover:scale-110 transition-transform"
                hx-patch="/game/{{$game->id}}/lobby/update-game-user" hx-vals='{"is_ready": true}'>
            <x-bear::icon name="check" size="4" class="opacity-70 mr-0.5"/> Ready
        </button>
    @endif
</div>
@if($game->created_by_user_id === BearAuthService::getUserId())
    <div class="flex justify-center h-7 bg-gray-800 items-center">
        <button class="flex items-center ml-4 bg-red-400 text-red-800 hover:text-red-900 text-xs font-medium pl-2 pr-3 py-0.5 my-0.5 rounded duration-75 hover:scale-110 transition-transform"
                hx-delete="/game/{{$game->id}}" hx-confirm="DELETE the game?">
            <x-bear::icon name="x-mark" size="4" class="opacity-70 mr-0.5"/>Delete Game
        </button>
        <button class="flex items-center ml-4 bg-blue-400 text-blue-800 hover:text-blue-900 text-xs font-medium pl-2 pr-3 py-0.5 my-0.5 rounded duration-75 hover:scale-110 transition-transform"
                hx-post="/game/{{$game->id}}/start" hx-confirm="Start The Game Now?">
            <x-bear::icon name="play-circle" size="4" class="opacity-70 mr-0.5"/> Force Start
        </button>
    </div>
    @else
    <div class="h-7"></div>
@endif
<div class="grid place-items-center flex-1 overflow-auto">
    <div class="grid lg:grid-cols-3 grid-cols-1 gap-8">
        <div class="flex flex-col gap-4">
            <button class="hover:scale-110 hover:-rotate-3 transition-all duration-75" hx-get="/game/{{$game->id}}/lobby/dialog/name-flag">
                <div class="flex justify-center items-center">
                    <img class="h-6 mr-2 ring-1 ring-gray-800" src="/static/flag/svg/{{$user->country_iso2_code}}.svg" alt="{{$user->country_name}}">
                    <div class="text-gray-200 text-3xl font-bold">{{$user->user_display_name}}</div>
                </div>
                @if(str_starts_with(haystack: $user->user_display_name, needle: 'Guest-'))
                    <div class="text-lime-300 font-medium text-xs">Click to Change</div>
                @endif
            </button>
            <button class="hover:scale-110 hover:rotate-3 transition-all duration-75" hx-get="/game/{{$game->id}}/lobby/dialog/map-marker">
                <div class="text-gray-400 text-xl font-bold">Marker: <span class="text-gray-300">{{$user->map_marker_name}}</span></div>
                <div class="flex justify-center mt-1">
                    <img class="h-12 w-12" src="/static/img/map-marker/{{$user->map_marker_file_name}}" alt="{{$user->map_marker_name}}">
                    <img class="h-12 w-12" src="/static/img/map-marker/{{$user->map_marker_file_name}}" alt="{{$user->map_marker_name}}">
                    <img class="h-12 w-12" src="/static/img/map-marker/{{$user->map_marker_file_name}}" alt="{{$user->map_marker_name}}">
                </div>
                @if($user->map_marker_file_name === 'default.png')
                    <div class="text-lime-300 font-medium text-xs">Click to Change</div>
                @endif
            </button>
            <button class="hover:scale-110 hover:-rotate-3 transition-all duration-75">
                <div class="text-gray-400 text-xl font-bold">Map: <span class="text-gray-300">{{$user->map_style_name}}</span></div>
                <div class="flex justify-center">
                    <img class="h-24 w-96 object-none" src="/static/files/tile/0/11/1614/1016.png" alt="Example map tile">
                </div>
            </button>
        </div>
        <div class="w-80">
            <ul id="player-list">
                @foreach($players as $player)
                    @if(!$loop->first)
                        <hr class="mx-5 mt-1 mb-1 pb-0.5 border-gray-700 border-dashed"></hr>
                    @endif
                    <li class="flex items-center">
                        <img class="h-8 w-8 mr-2" src="/static/img/map-marker/{{$player->map_marker_file_name}}" alt="{{$player->map_marker_file_name}}" >
                        <img class="w-8 mr-2 ring-1 ring-gray-800" src="/static/flag/svg/{{$player->user_country_iso2_code}}.svg" alt="{{$player->country_name}}" tippy="{{$player->country_name}}" >
                        <div>
                            <p class="font-semibold">
                                {{$player->user_display_name}}
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
        <div>information</div>
    </div>
</div>

