<?php declare(strict_types=1); ?>
<div class="h-full w-full flex flex-col">
  <x-bear::form.text id="map-url" required="" class="text-gray-700" autocomplete="off"></x-bear::form.text>
  <div id="tags" class="mt-1">
    <h3 class="font-bold text-teal-500">Tags</h3>
    <div class="flex gap-6 ml-2 items-center">
      <div class="flex items-center">
        <label class="mr-2 font-medium text-gray-400" for="GREAT">GREAT</label>
        <input id="GREAT" type="checkbox" name="tag" value="GREAT">
      </div>
      <div class="flex items-center">
        <label class="mr-2 font-medium text-gray-400" for="LANDSCAPE">LANDSCAPE</label>
        <input id="LANDSCAPE" type="checkbox" name="tag" value="LANDSCAPE">
      </div>
      <div class="flex items-center">
        <label class="mr-2 font-medium text-gray-400" for="ANIMAL">ANIMAL</label>
        <input id="ANIMAL" type="checkbox" name="tag" value="ANIMAL">
      </div>
      <div class="flex items-center">
        <label class="mr-2 font-medium text-amber-400" for="DIFFICULT">DIFFICULT</label>
        <input id="DIFFICULT" type="checkbox" name="tag" value="DIFFICULT">
      </div>
    </div>
  </div>
  <hr class="mt-2 mb-1  border-gray-600 border-2">
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
  const map = new window.maplibregl.Map({
    container: 'map', style: {
      'version': 8, 'sources': {
        'raster-tiles': {
          'type': 'raster', 'tiles': ['{{$user->map_style_full_uri}}'], 'tileSize': {{$user->map_style_tile_size}},
        }
      }, 'layers': [{'id': 'simple-tiles', 'type': 'raster', 'source': 'raster-tiles'}]
    }, center: [0, 25], dragRotate: false, keyboard: false, minZoom: 1, maxZoom: 18, zoom: 2
  });
  map.scrollZoom.setWheelZoomRate(1 / 70);
  map.scrollZoom.setZoomRate(1 / 70);
  map.touchZoomRotate.disableRotation();


  map.on('click', function (e) {
    const lat = e.lngLat.lat;
    const lng = e.lngLat.lng;
    if (!document.getElementById('enable-click-search').checked) {
      window.open('https://google.com/maps/@' + lat + ',' + lng + ',' + map.getZoom() + 'z', '', 'width=1300,height=800');
      return;
    }

    fetch('/page/discovery/street-view-location-search', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json'
      }, body: JSON.stringify({
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
              new window.maplibregl.Marker({scale: 0.4, color: 'gray'})
                .setLngLat([cur.lng, cur.lat])
                .addTo(map)
            }
            if (cur.status === 'new') {
              const icon = document.createElement('img');
              icon.src = '{{$user->map_marker_file_path}}'
              icon.classList.add('drop-shadow');
              icon.style.height = '32px';
              new window.maplibregl.Marker({element: icon})
                .setLngLat([cur.lng, cur.lat])
                .addTo(map);
            }
          }
          //addGuesses(json)
        });
      }
    });
  });


  const addGuesses = function (guesses) {
    guesses.forEach(val => {
      if (val.result) L.marker([val.lat, val.lng], {icon: map_icon}).addTo(map); else L.marker([val.lat, val.lng], {icon: small_icon}).addTo(map);
    });
  }


  const add_panorama = function () {
    let panorama_id = "";
    let text = "";
    try {
      // Regex for extracting panorama id, which is the string aster !1s
      const re = "!1s([^!]+)!2e";
      panorama_id = document.getElementById('map-url').value.match(re)[1];
      text = document.getElementById('map-url').value.split('@')[1].split(',');
    } catch (e) {
      window.notify.error("Failed to parse URL, is this a valid Street View URL?");
      return;
    }
    let tag_elements = document.getElementById('tags').querySelectorAll('input[type="checkbox"]');
    let tags_unchecked = [];
    let tags_checked = [];
    tag_elements.forEach(val => {
      if (val.checked) tags_checked.push(val.value); else tags_unchecked.push(val.value);
    });

    fetch('/page/discovery/street-view', {
      method: 'POST', headers: {
        'Content-Type': 'application/json'
      }, body: JSON.stringify({
        panorama_id: panorama_id,
        lat: text[0],
        lng: text[1],
        tags_checked: tags_checked,
        tags_unchecked: tags_unchecked,
      }),
    }).then(resp => resp.json()).then(json => {
        console.log(json);
      if (json['status'] === 'failed') {
        window.notify.error("Failed to add location to the game, Panorama API error.");
        console.error(json['error']);
      } else if (json['exists']) {
        window.notify.open({
          type: "warning", message: "This location has already been discovered!", duration: 10000,
        });
        if (json['tags_added'].length > 0) {
          window.notify.open({
            type: "success", message: "Tags added<br>" +  json['tags_added'], duration: 10000,
          });
        }
        if (json['tags_removed'].length > 0) {
          window.notify.open({
            type: "error", message: "Tags removed<br>" +  json['tags_removed'], duration: 10000,
          });
        }
      } else {
        window.notify.open({
          type: "success", message: "Location added to the game!", duration: 10000,
        });
        if (json['tags_added'].length > 0) {
          window.notify.open({
            type: "success", message: "Tags added<br>" +  json['tags_added'], duration: 10000,
          });
        }
        document.getElementById('map-url').value = '';
        const icon = document.createElement('img');
        icon.src = '{{$user->map_marker_file_path}}'
        icon.classList.add('drop-shadow');
        icon.style.height = '32px';
        new window.maplibregl.Marker({element: icon})
          .setLngLat([json['lng'], json['lat']])
          .addTo(map);
        map.panTo([json['lng'], json['lat']]);
      }
    }).catch(err => {
      window.notify.error("Failed to add location to the game, see console for error.");
      console.error(err);
    });
  }

  document.getElementById("map-url").addEventListener("keyup", function (event) {
    if (event.key === 'Enter') {
      add_panorama();
    }
  });

  const user_panoramas = @json($user_panoramas);
  const other_panoramas = @json($other_panoramas);
  user_panoramas.forEach(val => {
    new window.maplibregl.Marker({scale: 0.5, color: 'orange'})
      .setLngLat([val.lng, val.lat])
      .addTo(map)
  });
  other_panoramas.forEach(val => {
    new window.maplibregl.Marker({scale: 0.5})
      .setLngLat([val.lng, val.lat])
      .addTo(map)
  });
</script>
