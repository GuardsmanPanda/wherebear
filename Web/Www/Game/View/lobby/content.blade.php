<?php declare(strict_types=1); ?>
@php use Domain\Map\Enum\MapStyleEnum;use GuardsmanPanda\Larabear\Infrastructure\Auth\Service\BearAuthService; @endphp
<div class="grid place-items-center flex-1 overflow-auto">
    <div class="grid lg:grid-cols-3 grid-cols-1 gap-8">

        <div id="game-info"
             class="flex flex-col gap-2 rounded-md border border-gray-300 bg-gray-50 p-4 dark:border-gray-700 dark:bg-gray-900">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <x-bear::icon name="information-circle" size="6" class="opacity-50 mr-2"/>
                    <h2 class="text-xl font-medium text-gray-700 dark:text-gray-100">Game Settings</h2>
                </div>
                <div class="flex">
                    @if($user->is_ready)
                        <button class="flex items-center ml-4 text-gray-500 hover:text-gray-400 ring-1 ring-gray-500 font-medium pl-2 pr-3 py-0.5 my-0.5 rounded duration-75 hover:scale-110 transition-transform"
                                hx-patch="/game/{{$game->id}}/lobby/update-game-user" hx-vals='{"is_ready": false}'>
                            <x-bear::icon name="x-mark" size="4" class="opacity-70 mr-0.5"/>
                            Wait
                        </button>
                    @else
                        <div id="game-state-text" class="text-gray-500 font-medium text-xl animate-pulse">Click Ready
                        </div>
                        <button class="flex items-center ml-4 bg-yellow-400 text-yellow-800 hover:text-yellow-900 font-medium pl-2 pr-3 py-0.5 my-0.5 rounded duration-75 hover:scale-110 transition-transform"
                                hx-patch="/game/{{$game->id}}/lobby/update-game-user" hx-vals='{"is_ready": true}'>
                            <x-bear::icon name="check" size="4" class="opacity-70 mr-0.5"/>
                            Ready
                        </button>
                    @endif
                </div>
            </div>
            <hr class="border-solid border-gray-300 dark:border-gray-700"/>

            @if($game->created_by_user_id === BearAuthService::getUserId())
                <div class="flex">
                    <button class="flex items-center ml-4 bg-red-400 text-red-800 hover:text-red-900 text-xs font-medium pl-2 pr-3 py-0.5 my-0.5 rounded duration-75 hover:scale-110 transition-transform"
                            hx-delete="/game/{{$game->id}}" hx-confirm="DELETE the game?">
                        <x-bear::icon name="x-mark" size="4" class="opacity-70 mr-0.5"/>
                        Delete Game
                    </button>
                    <button class="flex items-center ml-4 bg-amber-400 text-amber-800 hover:text-amber-900 text-xs font-medium pl-2 pr-3 py-0.5 my-0.5 rounded duration-75 hover:scale-110 transition-transform"
                            hx-get="/game/{{$game->id}}/lobby/dialog/settings">
                        <x-bear::icon name="cog-6-tooth" size="4" class="opacity-70 mr-0.5"/>
                        Game Settings
                    </button>
                    <button class="flex items-center ml-4 bg-blue-400 text-blue-800 hover:text-blue-900 text-xs font-medium pl-2 pr-3 py-0.5 my-0.5 rounded duration-75 hover:scale-110 transition-transform"
                            hx-post="/game/{{$game->id}}/start" hx-confirm="Start The Game Now?">
                        <x-bear::icon name="play-circle" size="4" class="opacity-70 mr-0.5"/>
                        Force Start
                    </button>
                </div>
            @endif

            <div class="relative overflow-x-auto">
                <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                    <tr>
                        <th scope="col" class="px-6 py-3">Rounds</th>
                        <th scope="col" class="px-6 py-3">Guessing Time</th>
                        <th scope="col" class="px-6 py-3">Total Game Time</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                        <td class="px-6 py-4">{{$game->number_of_rounds}} rounds</td>
                        <td class="px-6 py-4">{{$game->round_duration_seconds}} seconds</td>
                        <td class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-gray-100">
                            ~{{ round(num: ($game->number_of_rounds * ($game->round_duration_seconds  + 23) + 90) / 60) }}
                            minutes
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="bg-gray-900 px-4 py-4 rounded-md">
            <div class="flex justify-between">
                <div class="flex items-center">
                    <x-bear::icon name="cog-6-tooth" size="6" class="opacity-50 mr-2"/>
                    <h2 class="text-xl font-bold">Player Settings</h2>
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
                                class="text-gray-300">{{$user->map_style_name}}</span></div>
                    <div class="flex justify-center">
                        <img class="h-24 w-96 object-none"
                             src="{{MapStyleEnum::from($user->map_style_enum)->mapTileUrl(z: 11, x: 1614, y: 1016)}}"
                             alt="Example map tile">
                    </div>
                    @if($user->map_style_enum === null)
                        <div class="text-lime-300 font-medium text-xs">Click to Change</div>
                    @endif
                </button>
            </div>
        </div>
    </div>
</div>