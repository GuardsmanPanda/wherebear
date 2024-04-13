<?php declare(strict_types=1); ?>

<div id="map" class="h-full w-full"></div>

<script>
    const map = L.map('map', {
        center: [25, 0],
        zoom: 3,
        worldCopyJump: true
    });

    L.tileLayer('/static/files/tile/0/{z}/{x}/{y}.png', {
        maxNativeZoom: 17,
        minZoom: 1,
    }).addTo(map);

    map.on('click', function (e) {
        window.open('https://google.com/maps/@' + e.latlng.lat+','+e.latlng.lng+',' + map.getZoom()+'z', '', 'width=1300,height=800');
    });
</script>
