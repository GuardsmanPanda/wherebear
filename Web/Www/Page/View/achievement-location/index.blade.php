<?php declare(strict_types=1); ?>
<div class="h-full w-full flex gap-2">
  <div id="map" class="w-full flex-grow my-4">
  </div>
  <div class="w-80 mt-2">
    <div>
      Controls
    </div>
    <div>
      e
    </div>
  </div>
</div>
<script>
  const map = L.map('map', {
    center: [25, 0], zoom: 3, worldCopyJump: true, scrollWheelZoom: true
  });

  const small_icon = L.icon({
    iconUrl: '/static/img/map-marker/default.png', iconSize: [24, 24], iconAnchor: [12, 24],
  });

  L.tileLayer('{{$user->map_style_full_uri}}', {
    maxNativeZoom: 17,
    minZoom: 1,
    tileSize: {{$user->map_style_tile_size}},
    zoomOffset: {{$user->map_style_zoom_offset}},
  }).addTo(map);


  let lat = 0;
  let lng = 0;
  let radius = 2000;
  let circle = null;

  const myStyle = {
    "color": "#ff7800",
    "weight": 5,
    "opacity": 0.3
  };

  const drawCircle = function () {
    if (circle !== null) {
      map.removeLayer(circle);
    }
    const resp = fetch('/page/achievement-location/data?' + new URLSearchParams({lat: lat, lng: lng, radius: radius}).toString())
      .then(resp => resp.json())

    // wait for the response
    const json = resp;

    console.dir(resp);
    circle = L.geoJSON(json.polygon, {style: myStyle});
    circle.addTo(map);
  };

  map.on('click', function (e) {
    lat = e.latlng.lat;
    lng = e.latlng.lng;
    drawCircle();
  });

  window.addEventListener('wheel', function (ev) {
    console.dir(ev);
    if (circle !== null) {
      const delta = ev.shiftKey ? 2 : 1.1;
      if (ev.deltaY < 0) {
        radius *= delta;
      } else {
        radius /= delta;
      }
      drawCircle();
    }
    console.log('radius', radius);
  });

</script>