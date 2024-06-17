<?php declare(strict_types=1); ?>
<div class="h-full w-full flex flex-col">
    <x-bear::form.text id="map-url" required="" class="text-gray-700"></x-bear::form.text>
    <div class="mt-1">
        <h3 class="font-bold text-teal-500">Map Options</h3>
        <div class="flex ml-2 items-center">
            <div class="flex items-center">
                <label class="mr-2 font-medium text-gray-400" for="enable-click-search">Click to Search</label>
                <input type="checkbox" id="enable-click-search" checked>
            </div>
            <div class="flex items-center ml-4">
                <label class="mr-2 font-medium text-gray-400" for="retries">Retries</label>
                <input type="number" id="retries" min="1" max="50" value="10"
                       class="rounded py-0.5 px-1 text-sm text-gray-600">
            </div>
            <div class="flex items-center ml-4">
                <label class="mr-2 font-medium text-gray-400" for="distance">Distance</label>
                <input type="range" id="distance" min="20" max="100000" value="1000">
            </div>
            <div class="flex items-center ml-4">
                <label class="mr-1 font-medium text-gray-400" for="year">From Year</label>
                <select id="year" class="rounded h-6 text-sm text-gray-600 py-0.5 mx-1">
                    <option value="2020">2010</option>
                    <option value="2020">2015</option>
                    <option value="2020" selected>2020</option>
                    <option value="2021">2021</option>
                    <option value="2022">2022</option>
                    <option value="2023">2023</option>
                    <option value="2024">2024</option>
                </select>
            </div>
        </div>
    </div>
    <div id="map" class="w-full flex-grow my-4"></div>
</div>


<script>
    const map = L.map('map', {
        center: [25, 0],
        zoom: 3,
        worldCopyJump: true
    });

    const map_icon = L.icon({
        iconUrl: '/static/img/map-marker/bobdino.png',
        iconSize: [48, 48],
        iconAnchor: [24, 48],
        tooltipAnchor: [0, -48],
    });
    const small_icon = L.icon({
        iconUrl: '/static/img/map-marker/default.png',
        iconSize: [24, 24],
        iconAnchor: [12, 24],
    });

    L.tileLayer('https://tile.gman.bot/OSM/{z}/{x}/{y}.png', {
        maxNativeZoom: 17,
        minZoom: 1,
    }).addTo(map);

    map.on('click', function (e) {
        if (!document.getElementById('enable-click-search').checked) {
            window.open('https://google.com/maps/@' + e.latlng.lat + ',' + e.latlng.lng + ',' + map.getZoom() + 'z', '', 'width=1300,height=800');
            return;
        }

        let lat = e.latlng.lat;
        let lng = e.latlng.lng;
        fetch('/page/discovery/street-view-location-search', {
            method: 'POST',

            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                lat: lat,
                lng: lng,
                distance: document.getElementById('distance').value,
                retries: document.getElementById('retries').value,
                year: document.getElementById('year').value,
            }),
        }).then(resp => {
            if (!resp.ok) {
                resp.text().then(text => {
                    window.notify.error(text);
                })
            } else {
                //document.getElementById('map-url').value = '';
                resp.json().then(json => {
                    for (let i = 0; i < json.length; i++) {
                        let cur = json[i];
                        if (cur.status === 'failed') {
                            L.marker([cur.lat, cur.lng], {icon: small_icon}).addTo(map);
                        }
                        if (cur.status === 'new') {
                            L.marker([cur.lat, cur.lng], {icon: map_icon}).addTo(map);
                        }
                    }
                    //addGuesses(json)
                });
            }
        });
    });


    const addGuesses = function (guesses) {
        guesses.forEach(val => {
            if (val.result) L.marker([val.lat, val.lng], {icon: map_icon}).addTo(map);
            else L.marker([val.lat, val.lng], {icon: small_icon}).addTo(map);
        });
    }


    const add_panorama = function () {
        let text = "";
        try {
            text = document.getElementById('map-url').value.split('@')[1].split(',');
        } catch (e) {
            window.notify.error("Failed to parse URL, is this a valid Street View URL?");
        }
        fetch('/page/discovery/street-view-location', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({lat: text[0], lng: text[1]}),
        }).then(resp => {
            map.panTo([text[0], text[1]]);
            if (!resp.ok) {
                resp.text().then(text => {
                    window.notify.error(text);
                })
            } else {
                //document.getElementById('map-url').value = '';
                resp.json().then(json => {
                    if (json['exists']) {
                        window.notify.open({
                            type: "warning",
                            message: "This location has already been discovered!",
                        });
                    } else {
                        window.notify.open({
                            type: "success",
                            message: "Location added to the game!",
                        });
                        document.getElementById('map-url').value = '';
                    }
                    //addGuesses(json)
                });
            }
        });
    }

    document.getElementById("map-url").addEventListener("keyup", function (event) {
        if (event.key === 'Enter') {
            event.preventDefault();
            add_panorama();
        }
    });


    const markLayer = L.markerClusterGroup({maxClusterRadius: 35});
    const markers = [];
    @foreach($markers as $marker)
    markers.push(L.marker([{{$marker->lat}}, {{$marker->lng}}], {icon: small_icon}));
    @endforeach
    markLayer.addLayers(markers);
    map.addLayer(markLayer);
</script>
