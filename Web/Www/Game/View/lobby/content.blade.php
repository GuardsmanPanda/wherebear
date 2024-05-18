<?php declare(strict_types=1); ?>
@php use GuardsmanPanda\Larabear\Infrastructure\Auth\Service\BearAuthService; @endphp
<div class="grid place-items-center flex-1 overflow-auto">
    <div class="grid lg:grid-cols-3 grid-cols-1 gap-8">
        <div class="bg-gray-900 px-4 py-4 rounded-md">
            <div class="flex justify-between">
                <div class="flex items-center">
                    <x-bear::icon name="cog-6-tooth" size="6" class=" opacity-50 mr-2"/>
                    <h2 class="text-xl font-bold">Settings</h2>
                </div>
                <div class="flex">
                    @if($user->user_email === null)
                        <button class="flex items-center ml-2 bg-blue-700 text-blue-100 hover:text-blue-50 hover:-rotate-2 font-medium pl-2 pr-3 py-0.5 my-0.5 rounded duration-75 hover:scale-110 transition-transform"
                                hx-get="/auth/dialog?redirect_path=/game/{{$game->id}}/lobby">
                            <x-bear::icon name="user" size="4" class="opacity-70 mr-0.5"/>
                            Login
                        </button>
                    @endif
                    @if($game->created_by_user_id !== BearAuthService::getUserId())
                        <button class="flex items-center ml-2 text-gray-500 hover:text-gray-400 ring-1 ring-gray-500 hover:rotate-2 font-medium pl-2 pr-3 py-0.5 my-0.5 rounded duration-75 hover:scale-110 transition-transform"
                                hx-delete="/game/{{$game->id}}/lobby/leave"
                                hx-confirm="Are you sure you wish to leave the game?">
                            <x-bear::icon name="x-mark" size="4" class="opacity-70 mr-0.5"/>
                            Leave
                        </button>
                    @endif
                </div>

            </div>
            <hr class="mx-1 mt-1 mb-3 pb-0.5 border-gray-700 border-b-2 border-dashed">
            <div class="flex flex-col gap-4">
                <button class="hover:scale-110 hover:-rotate-3 transition-all duration-75"
                        hx-get="/game/{{$game->id}}/lobby/dialog/name-flag">
                    <div class="flex justify-center items-center">
                        <img class="h-6 mr-2"
                             src="/static/flag/svg/{{$user->country_iso2_code}}.svg" alt="{{$user->country_name}}">
                        <div class="text-gray-200 text-3xl font-bold">{{$user->user_display_name}}</div>
                    </div>
                    @if(str_starts_with(haystack: $user->user_display_name, needle: 'Guest-'))
                        <div class="text-lime-300 font-medium text-xs">Click to Change</div>
                    @endif
                </button>
                <button class="hover:scale-110 hover:rotate-3 transition-all duration-75"
                        hx-get="/game/{{$game->id}}/lobby/dialog/map-marker">
                    <div class="text-gray-400 text-xl font-bold">Marker: <span
                                class="text-gray-300">{{$user->map_marker_name}}</span></div>
                    <div class="flex justify-center mt-1">
                        <img class="h-12 w-12" src="/static/img/map-marker/{{$user->map_marker_file_name}}"
                             alt="{{$user->map_marker_name}}">
                        <img class="h-12 w-12" src="/static/img/map-marker/{{$user->map_marker_file_name}}"
                             alt="{{$user->map_marker_name}}">
                        <img class="h-12 w-12" src="/static/img/map-marker/{{$user->map_marker_file_name}}"
                             alt="{{$user->map_marker_name}}">
                    </div>
                    @if($user->map_marker_file_name === 'default.png')
                        <div class="text-lime-300 font-medium text-xs">Click to Change</div>
                    @endif
                </button>
                <button class="hover:scale-110 hover:-rotate-3 transition-all duration-75"
                        hx-get="/game/{{$game->id}}/lobby/dialog/map-style">
                    <div class="text-gray-400 text-xl font-bold">Map: <span
                                class="text-gray-300">{{$user->map_style_name ?? 'OpenStreetMap'}}</span></div>
                    <div class="flex justify-center">
                        <img class="h-24 w-96 object-none"
                             src="/static/files/tile/{{$user->map_style_enum ?? 'OSM'}}/11/1614/1016.png"
                             alt="Example map tile">
                    </div>
                    @if($user->map_style_enum === null)
                        <div class="text-lime-300 font-medium text-xs">Click to Change</div>
                    @endif
                </button>
            </div>
        </div>
        <div class="w-96 bg-gray-900 px-4 py-4 rounded-md">
            <div class="flex justify-between items-center">
                <div class="flex items-center">
                    <x-bear::icon name="users" size="6" class=" opacity-50 mr-2"/>
                    <h2 class="text-xl font-bold">Players</h2>
                </div>
                <div class="flex">
                    @if($user->is_ready)
                        <button class="flex items-center ml-4 text-gray-500 hover:text-gray-400 ring-1 ring-gray-500 font-medium pl-2 pr-3 py-0.5 my-0.5 rounded duration-75 hover:scale-110 transition-transform"
                                hx-patch="/game/{{$game->id}}/lobby/update-game-user" hx-vals='{"is_ready": false}'>
                            <x-bear::icon name="x-mark" size="4" class="opacity-70 mr-0.5"/>
                            Wait
                        </button>
                    @else
                        <div id="game-state-text" class="text-gray-500 font-medium text-xl animate-pulse">Click Ready</div>
                        <button class="flex items-center ml-4 bg-yellow-400 text-yellow-800 hover:text-yellow-900 font-medium pl-2 pr-3 py-0.5 my-0.5 rounded duration-75 hover:scale-110 transition-transform"
                                hx-patch="/game/{{$game->id}}/lobby/update-game-user" hx-vals='{"is_ready": true}'>
                            <x-bear::icon name="check" size="4" class="opacity-70 mr-0.5"/>
                            Ready
                        </button>
                    @endif
                </div>
            </div>
            <hr class="mx-1 mt-1 mb-3 pb-0.5 border-gray-700 border-b-2 border-dashed">
            <ul id="player-list">
                @foreach($players as $player)
                    @if(!$loop->first)
                        <hr class="mx-5 mt-1 mb-1 pb-0.5 border-gray-700 border-dashed">
                    @endif
                    <li class="flex items-center">
                        <img class="h-8 w-8 mr-2" src="/static/img/map-marker/{{$player->map_marker_file_name}}"
                             alt="{{$player->map_marker_file_name}}">
                        <img class="w-8 mr-2"
                             src="/static/flag/svg/{{$player->user_country_iso2_code}}.svg"
                             alt="{{$player->country_name}}" tippy="{{$player->country_name}}">
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