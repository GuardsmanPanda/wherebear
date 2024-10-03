<?php declare(strict_types=1); ?>
<div class="h-full w-full flex gap-2">
  <div id="map" class="w-full flex-grow my-4">
  </div>
  <div id="controls" class="w-80 mt-2">
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
  const map = new window.maplibregl.Map({
    container: 'map', style: {
      'version': 8, 'sources': {
        'raster-tiles': {
          'type': 'raster', 'tiles': ['{{$user->map_style_full_uri}}'], 'tileSize': {{$user->map_style_tile_size}},
        }
      }, 'layers': [{'id': 'simple-tiles', 'type': 'raster', 'source': 'raster-tiles'}]
    }, center: [0, 25], dragRotate: false, keyboard: false, minZoom: 1, maxZoom: 17, zoom: 2
  });
  map.scrollZoom.setWheelZoomRate(1 / 70);
  map.scrollZoom.setZoomRate(1 / 70);
  map.touchZoomRotate.disableRotation();

  let lat = 0;
  let lng = 0;
  let radius = 4000;
  let circle = null;
  let markers = [];


  const drawCircle = function () {
    fetch('/page/achievement-location/data?' + new URLSearchParams({lat: lat, lng: lng, radius: radius}).toString())
      .then(resp => resp.json())
      .then(json => {
        markers.forEach(marker => marker.remove());

        let panoramas = 0;
        json.panoramas.forEach(panorama => {
          markers.push(
            new window.maplibregl.Marker({scale: 0.5, color: panorama.within ? 'lightgreen' : 'red'})
              .setLngLat([panorama.lng, panorama.lat])
              .addTo(map)
          );
          panoramas += panorama.within ? 1 : 0;
        });
        document.getElementById('lat').innerText = lat;
        document.getElementById('lng').innerText = lng;
        document.getElementById('radius').innerText = radius;
        document.getElementById('panoramas').innerText = panoramas;

        if (circle !== null) {
          map.removeLayer('main');
          map.removeSource('main');
        }

        map.addSource('main', {
          'type': 'geojson', 'data': {
            'type': 'Feature', 'geometry': json.polygon
          }
        });

        circle = map.addLayer({
          'id': 'main', 'type': 'fill', 'layout': {}, 'paint': {
            'fill-color': 'lightgreen', 'fill-opacity': 0.3
          }, source: 'main'
        });
      });
  };

  document.getElementById('controls').addEventListener('wheel', function (ev) {
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

  map.on('click', function (e) {
    lat = Math.round(e.lngLat.lat * 10000000) / 10000000;
    lng = Math.round(e.lngLat.lng * 10000000) / 10000000;
    drawCircle();
  });

  window.addEventListener('keydown', function (ev) {
    if (ev.key === 'x') {
      radius *= 1.1;
      radius = Math.round(radius);
      drawCircle();
    }
    if (ev.key === 'z') {
      radius /= 1.1;
      radius = Math.round(radius);
      drawCircle();
    }
  });
</script>