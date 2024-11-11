<?php declare(strict_types=1); ?>
@php use Domain\User\Enum\BearPermissionEnum;use GuardsmanPanda\Larabear\Infrastructure\Auth\Service\BearAuthService; @endphp
<div class="h-full w-full flex flex-col">
  <x-bear::form.text id="map-url" required="" class="text-gray-700" autocomplete="off"></x-bear::form.text>
  <div id="tags" class="mt-1">
    <h3 class="font-bold text-teal-500">Tags</h3>
    <div class="flex gap-4 ml-2 items-center">
      @if(BearAuthService::hasPermission(permission: BearPermissionEnum::PANORAMA_TAG_DAILY))
        <div class="flex items-center">
          <label class="mr-2 font-medium text-gray-400" for="DAILY">DAILY</label>
          <input id="DAILY" type="checkbox" name="tag" value="DAILY">
        </div>
      @endif
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
      @if(BearAuthService::hasPermission(permission: BearPermissionEnum::IS_BOB))
        <div class="flex items-center">
          <label class="mr-2 font-medium text-blue-400" for="GOOGLE">GOOGLE</label>
          <input id="GOOGLE" type="checkbox" name="tag" value="GOOGLE">
        </div>
        <div class="flex items-center">
          <label class="mr-2 font-medium text-blue-400" for="HIDDEN">HIDDEN</label>
          <input id="HIDDEN" type="checkbox" name="tag" value="HIDDEN">
        </div>
      @endif
    </div>
  </div>
  <hr class="mt-2 mb-1  border-gray-600 border-2">
  <div class="mt-1">
    <h3 class="font-bold text-teal-500">Map Options</h3>
    <div class="flex ml-2 items-center">
      Country Dropdown
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
    window.open('https://google.com/maps/@' + e.lngLat.lat + ',' + e.lngLat.lng + ',' + (map.getZoom() + 1) + 'z', '', 'width=1300,height=800');
  });

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
        panorama_id: panorama_id, lat: text[0], lng: text[1], tags_checked: tags_checked, tags_unchecked: tags_unchecked,
      }),
    }).then(resp => resp.json()).then(json => {
      console.log(json);
      if (json['status'] === 'failed') {
        window.notify.error("Failed to add location to the game, Panorama API error.");
        console.error(json['error']);
      } else if (json['exists']) {
        window.notify.open({
          type: "warning", message: "This location has already been discovered!", duration: 14000,
        });
        if (json['tags_added'].length > 0) {
          window.notify.open({
            type: "success", message: "Tags added<br>" + json['tags_added'], duration: 14000,
          });
        }
        if (json['tags_removed'].length > 0) {
          window.notify.open({
            type: "error", message: "Tags removed<br>" + json['tags_removed'], duration: 14000,
          });
        }
      } else {
        window.notify.open({
          type: "success", message: "Location added to the game!", duration: 10000,
        });
        if (json['tags_added'].length > 0) {
          window.notify.open({
            type: "success", message: "Tags added<br>" + json['tags_added'], duration: 10000,
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
</script>
