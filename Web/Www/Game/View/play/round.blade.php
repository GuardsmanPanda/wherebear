<?php declare(strict_types=1); ?>
@php use Domain\Map\Enum\MapStyleEnum; @endphp
<div class="z-30 filter drop-shadow-xl absolute h-64 hover:h-2/3 hover:opacity-100 hover:w-2/3 opacity-75 right-0 rounded-bl overflow-hidden w-96">
    <div id="map-container" style="clip-path: polygon(0 0, 100% 0, 100% 100%, 55% 100%);" class="w-full h-full">
        <div id="map" class="h-full w-full"></div>
    </div>
</div>


<div class="absolute bg-black bg-opacity-70 -top-1 font-bold left-9 px-3 py-0.5 rounded-b-md text-gray-400 text-2xl z-20 shadow-lg"
     tippy="Photographed On" style="font-family: 'Inkwell Sans', system-ui, sans-serif;">
    {{$game->captured_month}}
</div>

<div id="pengu" class="z-40 absolute font-bold -right-3 top-64 drop-shadow-lg filter pointer-events-none"
     style="font-family: 'Inkwell Sans', system-ui, sans-serif;">
    <div class="relative text-gray-800">
        <img src="/static/img/pengu-sign.png" class="h-52" style="transform: scaleX(-1)" alt="Cutest pengu around">
        <div class="absolute capitalize opacity-70 rotate-1 text-xl text-center top-1 transform w-full">Round Ends In
        </div>
        <div id="countdown" class=" text-3xl leading-7 tabular-nums absolute top-6 w-full text-center"> </div>
    </div>
</div>

<div class="invisible md:visible absolute bottom-7 drop-shadow-lg filter font-bold origin-bottom-left pointer-events-none scale-75 transform" style="z-index: 520;">
    <div class="relative text-gray-800" style="font-family: 'Inkwell Sans', system-ui, sans-serif;">
        <img src="/static/img/pengu-sign.png" class="h-52" alt="Cutest pengu around">
        <div class="absolute opacity-70 rotate-1 text-xl text-center top-1 transform w-full">Game Round</div>
        <div class="text-3xl leading-7 tabular-nums absolute top-6 w-full text-center">
            {{$game->current_round}}<span class="opacity-50 px-0.5">/</span>{{$game->number_of_rounds}}
        </div>
    </div>
</div>
@include('game::play.countries-used')
<script>
    countdownStart({{$game->round_seconds_remaining}});
    //Make pengu bouncy if no guess made in time
    let guessMade = false;
    setTimeout(function () {
        if (!guessMade) {
            document.getElementById('pengu').classList.add('animate-bounce');
        }
    }, {{($game->round_seconds_remaining - 11) * 1000}});

    pannellum.viewer('play', {
        "type": "equirectangular",
        "panorama": "https://panorama.gman.bot/{{$game->jpg_path}}",
        "autoLoad": true,
    });

    const map = L.map('map', {
        center: [25, 0],
        zoom: 1,
        worldCopyJump: true
    });
    const map_icon = L.icon({
        iconUrl: '/static/img/map-marker/{{ $user->map_marker_file_name }}',
        iconSize: [48, 48],
        iconAnchor: [24, 48],
        tooltipAnchor: [0, -48],
    });
    L.tileLayer('{{ MapStyleEnum::from($user->map_style_enum)->mapTileUrl() }}', {
        maxNativeZoom: 17,
        minZoom: 1,
    }).addTo(map);

    const map_ele = document.getElementById('map-container');
    map_ele.addEventListener('mouseenter', _ => {
        map_ele.setAttribute('style', 'clip-path: none;');
        map.zoomIn(1, {animate: false});
        map.invalidateSize();
    });
    map_ele.addEventListener('mouseleave', _ => {
        map_ele.setAttribute('style', 'clip-path: polygon(0 0, 100% 0, 100% 100%, 55% 100%);');
        map.zoomOut(1, {animate: false});
        map.invalidateSize();
    });

    let marker = null;
    map.on('click', function (e) {
        fetch('/game/{{$game->id}}/play/guess', {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(e.latlng),
        }).then(resp => {
            if (!resp.ok) {
                console.log('Error:', resp.text());
            } else {
                guessMade = true;
                document.getElementById('pengu').classList.remove('animate-bounce');
                if (marker === null) {
                    marker = L.marker(e.latlng, {icon: map_icon}).addTo(map);
                } else {
                    marker.setLatLng(e.latlng);
                }
            }
        });
    });
</script>