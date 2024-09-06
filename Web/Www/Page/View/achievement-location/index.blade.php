<?php declare(strict_types=1); ?>
<div class="h-full w-full flex gap-2">
  <div id="map" class="w-full flex-grow my-4">
  </div>
  <div class="w-80 mt-2">
    <div>
      Controls
    </div>
    <div>
      <h2>Selected Location</h2>
      <div class="flex gap-4">
        <div>Latitude: <span id="lat">0</span></div>
        <div>Longitude: <span id="lng">0</span></div>
      </div>
      <div>Radius: <span id="radius">2000</span></div>
      <div>Panoramas: <span id="panoramas">2000</span></div>


    </div>
  </div>
</div>
<script>
  const map = L.map('map', {
    center: [25, 0], zoom: 3, worldCopyJump: true, scrollWheelZoom: true
  });

  const markerLayer = L.layerGroup().addTo(map);

  const small_icon_green = L.icon({
    iconUrl: '/static/img/map-util/map-marker-green.svg', iconSize: [24, 24], iconAnchor: [12, 24],
  });
  const small_icon_red = L.icon({
    iconUrl: '/static/img/map-util/map-marker-red.svg', iconSize: [24, 24], iconAnchor: [12, 24],
  });


  L.tileLayer('{{$user->map_style_full_uri}}', {
    maxNativeZoom: 17, minZoom: 1, tileSize: {{$user->map_style_tile_size}}, zoomOffset: {{$user->map_style_zoom_offset}},
  }).addTo(map);


  let lat = 0;
  let lng = 0;
  let radius = 2000;
  let circle = null;

  const myStyle = {
    "color": "#ff7800", "weight": 5, "opacity": 0.3
  };

  const drawCircle = function () {
    if (circle !== null) {
      map.removeLayer(circle);
    }
    const resp = fetch('/page/achievement-location/data?' + new URLSearchParams({lat: lat, lng: lng, radius: radius}).toString())
      .then(resp => resp.json())
      .then(json => {
        //clear the layer
        markerLayer.clearLayers();

        circle = L.geoJSON(json.polygon, {style: myStyle});
        circle.addTo(markerLayer);

        let panoramas = 0;
        json.panoramas.forEach(panorama => {
          const marker = L.marker([panorama.lat, panorama.lng], {icon: panorama.within ? small_icon_green : small_icon_red});
          marker.addTo(markerLayer);
          marker.bindPopup(`<a href="/page/achievement-location/view/${panorama.id}">${panorama.id}</a>`);
          if (panorama.within) {
            panoramas++;
          }
        });
        document.getElementById('lat').innerText = lat;
        document.getElementById('lng').innerText = lng;
        document.getElementById('radius').innerText = radius;
        document.getElementById('panoramas').innerText = panoramas;
      });
  };

  map.on('click', function (e) {
    // Rounded to nearest meter-ish (5 decimal places)
    lat = Math.round(e.latlng.lat * 100000) / 100000;
    lng = Math.round(e.latlng.lng * 100000) / 100000;
    drawCircle();
  });

  window.addEventListener('wheel', function (ev) {
    if (circle !== null) {
      const delta = ev.shiftKey ? 2 : 1.1;
      if (ev.deltaY < 0) {
        radius *= delta;
      } else {
        radius /= delta;
      }
      radius = Math.round(radius);
      drawCircle();
    }
  });

</script>