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
  //const map = L.map('map', {
  //  center: [25, 0], zoom: 3, worldCopyJump: true, scrollWheelZoom: true
  //});

  const map = new window.maplibregl.Map({
    container: 'map', style: {
      'version': 8, 'sources': {
        'raster-tiles': {
          'type': 'raster', 'tiles': ['{{$user->map_style_full_uri}}'], 'tileSize': {{$user->map_style_tile_size}},
        }
      }, 'layers': [{'id': 'simple-tiles', 'type': 'raster', 'source': 'raster-tiles'}]
    }, center: [0, 25], dragRotate: false, keyboard: false, minZoom: 1, maxZoom: 17, zoom: 2
  });
  map.scrollZoom.setWheelZoomRate(1 / 150);
  map.scrollZoom.setZoomRate(1 / 75);
  map.touchZoomRotate.disableRotation();


  map.on('click', function (e) {
    // Rounded to nearest meter-ish (5 decimal places)
    const lat = Math.round(e.lngLat.lat * 100000) / 100000;
    const lng = Math.round(e.lngLat.lng * 100000) / 100000;

    const resp = fetch('/page/achievement-location/data?' + new URLSearchParams({lat: lat, lng: lng, radius: radius}).toString())
      .then(resp => resp.json())
      .then(json => {
        if (json.location) {
          // create flag element
          const flag = document.createElement('img');
          flag.src = '/static/flag/svg/' + json.location.cca2  + '.svg'
          flag.classList.add('shadow');
          flag.classList.add('border');
          flag.classList.add('border-black');
          flag.setAttribute('tippy', json.location.name + (json.subdivision ? " - " + json.subdivision : ""));
          flag.style.height = '26px';
          window.tippyFunction(flag);
          new window.maplibregl.Marker({element: flag})
            .setLngLat([lng, lat])
            .addTo(map);
          //move mouse 1 px to trigger tooltip
          const mouseEvent = new MouseEvent('mousemove', {bubbles: true, cancelable: true, view: window});
          document.dispatchEvent(mouseEvent);

        } else {
          window.notify.error("No location found");
        }
      });
  });

  //const markerLayer = L.layerGroup().addTo(map);

  const small_icon_green = L.icon({
    iconUrl: '/static/img/map-util/map-marker-green.svg', iconSize: [24, 24], iconAnchor: [12, 24],
  });
  const small_icon_red = L.icon({
    iconUrl: '/static/img/map-util/map-marker-red.svg', iconSize: [24, 24], iconAnchor: [12, 24],
  });


  //L.tileLayer('{{$user->map_style_full_uri}}', {
  //  maxNativeZoom: 17, minZoom: 1, tileSize: {{$user->map_style_tile_size}}, zoomOffset: {{$user->map_style_zoom_offset}},
  //}).addTo(map);


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
        if (json.location) {
          let text = "Found location: " + json.location + (json.subdivision ? " - " + json.subdivision : "");
          window.notify.success(text);
        } else {
          window.notify.error("No location found");
        }
      });
  };

  //map.on('click', function (e) {
  //  // Rounded to nearest meter-ish (5 decimal places)
  //  lat = Math.round(e.latlng.lat * 100000) / 100000;
  //  lng = Math.round(e.latlng.lng * 100000) / 100000;
  //  drawCircle();
  //});

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