<?php declare(strict_types=1); ?>
<div class="h-full w-full flex flex-col">
    <x-bear::form.text id="map-url" required="" class="text-gray-700"></x-bear::form.text>
    <div id="map" class="w-full flex-grow my-4"></div>
</div>


<script>
    const map = L.map('map', {
        center: [25, 0],
        zoom: 3,
        worldCopyJump: true
    });

    const map_icon = L.icon({
        iconUrl: '/static/img/markers/bobdino.png',
        iconSize: [48, 48],
        iconAnchor: [24, 48],
        tooltipAnchor: [0, -48],
    });
    const small_icon = L.icon({
        iconUrl: '/static/img/markers/standard.png',
        iconSize: [24, 24],
        iconAnchor: [12, 24],
    });

    L.tileLayer('/static/files/tile/OSM/{z}/{x}/{y}.png', {
        maxNativeZoom: 17,
        minZoom: 1,
    }).addTo(map);

    map.on('click', function (e) {
        window.open('https://google.com/maps/@' + e.latlng.lat + ',' + e.latlng.lng + ',' + map.getZoom() + 'z', '', 'width=1300,height=800');
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
            body: JSON.stringify({lat: text[0], lng: text[1], curated: true}),
        })
            .then(resp => {
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
</script>
