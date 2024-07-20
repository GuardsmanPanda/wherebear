<?php declare(strict_types=1); ?>
@php use Domain\Map\Enum\MapStyleEnum;use Web\Www\Game\Render\GameRoundResultRender; @endphp
<div class="flex min-h-screen">
    <div class="flex-grow flex-shrink relative">
        @include('game::play.countries-used')

        <div class="absolute z-50 bg-black bg-opacity-90 font-bold pl-4 pr-4 pt-1 pb-3 rounded-br-md text-emerald-500 text-lg shadow-lg grid"
             style="font-family: 'Inkwell Sans',Verdana,sans-serif; z-index:5000;max-width: 19rem;">
            <div class="text-center text-3xl text-blue-500">{{$game->country_name}}</div>
            <div class="text-center text-lg text-cyan-400 leading-4">{{$game->state_name}}</div>
            <div class="text-center text-lg text-cyan-500 leading-6">{{$game->city_name}}</div>

            <div class="flex gap-3 justify-center py-2">
                <img src="/static/flag/wavy/{{strtolower($game->country_iso2_code)}}.png" width="140" alt="Wavy flag">
                <div class="grid">

                    <div class="flex gap-2 items-center">
                        <x-bear::icon name="globe-europe-africa" class="text-gray-500"></x-bear::icon>
                        <span>{{$game->country_iso2_code}}/{{$game->country_iso3_code}}</span>
                    </div>
                    <div class="flex gap-2 items-center">
                        <x-bear::icon name="globe-alt" class="text-gray-500"></x-bear::icon>
                        <span>{{$game->country_tld}}</span>
                    </div>
                    <div class="flex gap-2 items-center">
                        <x-bear::icon name="banknotes" class="text-gray-500"></x-bear::icon>
                        <span>{{$game->country_currency_code}}</span>
                    </div>
                    <div class="flex gap-2 items-center">
                        <x-bear::icon name="phone" class="text-gray-500"></x-bear::icon>
                        <span>+{{$game->country_calling_code}}</span>
                    </div>
                </div>
            </div>
            @if($game->is_country_independent !== true)
                <div><span class="text-gray-500">Status: </span>{{$game->country_dependency_status}} </div>
            @endif
        </div>

        <div class="invisible md:visible absolute bottom-7 drop-shadow-lg filter font-bold origin-bottom-left pointer-events-none scale-75 transform" style="z-index: 520;">
            <div class="relative text-gray-800" style="font-family: 'Inkwell Sans', system-ui, sans-serif;">
                <img src="/static/img/pengu-sign.png" class="h-52" alt="Cutest pengu around">
                <div class="absolute opacity-70 rotate-1 text-xl text-center top-1 transform w-full">
                    {{ $game->current_round === $game->number_of_rounds ? 'Game Ends In' : 'Next Round In' }}
                </div>
                <div id="countdown" class="text-3xl leading-7 tabular-nums absolute top-6 w-full text-center"></div>
            </div>
        </div>

        <div id="map-container" class="w-full h-full">
            <div id="map" class="h-full w-full"></div>
        </div>
    </div>
    <div>
        <div class="w-[23.5rem] grid gap-3 py-2 px-4 overflow-y-auto max-h-full">
            @foreach($guesses as $guess)
                <div class="flex items-center px-4 py-2 rounded-md shadow-xl bg-gray-800">
                    <div class="text-center font-medium text-gray-500 text-2xl">{{$guess->round_rank}}</div>
                    <img class="h-12 ml-1" src="/static/img/map-marker/{{$guess->file_name}}"
                         alt="Map Marker">
                    <img class="w-12 shadow-md mx-1" src="/static/flag/svg/{{$guess->user_country_iso2_code}}.svg"
                         alt="Country Flag" tippy="{{$guess->country_name}}">
                    <div class="text-gray-300 ml-2 flex-grow">
                        <div class="font-bold text-lg">
                            {{$guess->user_display_name}}
                        </div>
                        <div class="font-medium text-gray-400 flex justify-between items-center">
                            {!! GameRoundResultRender::renderPoints(points: $guess->round_points) !!}
                            <div class="flex items-center">
                                @if($guess->country_match)
                                    <img src="/static/flag/svg/{{$game->country_iso2_code}}.svg"
                                         alt="Country flag" @class(['mx-1.5 h-4 shadow-md','animate-pulse' => $guess->round_points === null])>
                                @endif
                                {!! GameRoundResultRender::renderDistance(distanceMeters: $guess->distance_meters) !!}
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>
<script>
    countdownStart({{$game->round_result_seconds_remaining}});

    const map = L.map('map', {
        center: [{{$game->panorama_lat}}, {{$game->panorama_lng}}],
        zoom: 5,
        worldCopyJump: true
    });
    const marker_win = L.icon({
        iconUrl: '/static/img/map-extra/marker-win2.png',
        iconSize: [64, 64],
        iconAnchor: [32, 32],
    });
    L.tileLayer('{{ MapStyleEnum::from($user->map_style_enum)->mapTileUrl() }}', {
        maxNativeZoom: 17,
        minZoom: 1,
    }).addTo(map);

    L.marker(
        [{{$game->panorama_lat}}, {{$game->panorama_lng}}],
        {
            icon: L.icon({
                iconUrl: '/static/img/map-extra/marker-win2.png',
                iconSize: [64, 64],
                iconAnchor: [32, 32],
            }), zIndexOffset: 500
        }
    ).addTo(map);

    const map_guesses = [
            @foreach($guesses as $guess)
        {
            lat: {{$guess->lat}},
            lng: {{$guess->lng}},
            file: '{{$guess->file_name}}',
            name: '{{$guess->user_display_name}}'
        },
        @endforeach
    ];
    map_guesses.reverse().forEach(function (item) {
        L.marker([item.lat, item.lng], {
            icon: L.icon({
                iconUrl: '/static/img/map-marker/' + item.file,
                iconSize: [48, 48],
                iconAnchor: [24, 48],
                tooltipAnchor: [0, -48],
            })
        }).addTo(map).bindTooltip(item.name, {direction: 'top', permanent: true, opacity: 0.9})
            .openTooltip();
    });
</script>