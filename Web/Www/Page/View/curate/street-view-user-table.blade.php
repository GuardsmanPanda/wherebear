<?php declare(strict_types=1); ?>
@php use Domain\User\Enum\BearPermissionEnum;use GuardsmanPanda\Larabear\Infrastructure\Auth\Service\BearAuthService; @endphp
<x-bear::form.text id="map-url" required="" class="text-gray-700" autocomplete="off"></x-bear::form.text>
<div class="flex">
  <div class="mt-1">
    <h3 class="font-bold text-amber-600">Viewport</h3>
    <div class="flex gap-4 ml-2 items-center">
      <div class="flex items-center">
        <label class="mr-2 font-medium text-amber-400" for="street_view_viewport">StreetView Viewport</label>
        <input id="street_view_viewport" type="checkbox" name="tag" value="true" checked>
      </div>
    </div>
  </div>
  <div id="tags" class="mt-1 ml-12">
    <h3 class="font-bold text-teal-500">Tags</h3>
    <div class="flex gap-4 ml-2 items-center">
      @if(BearAuthService::hasPermission(permission: BearPermissionEnum::PANORAMA_TAG_DAILY))
        <div class="flex items-center">
          <label class="mr-2 font-medium text-gray-400" for="DAILY">DAILY</label>
          <input id="DAILY" type="checkbox" name="tag" value="DAILY">
        </div>
      @endif
      <div class="flex items-center">
        <label class="mr-2 font-medium text-gray-400" for="ANIMAL">ANIMAL</label>
        <input id="ANIMAL" type="checkbox" name="tag" value="ANIMAL">
      </div>
    </div>
  </div>
</div>

<hr class="mt-2 mb-4  border-gray-600 border-2">

<table>
  <thead class="text-xs text-gray-400 uppercase bg-gray-700">
  <tr>
    <th scope="col" class="px-4 py-3">Id</th>
    <th scope="col" class="px-4 py-3">Captured Date</th>
    <th scope="col" class="px-4 py-3">Country Name</th>
    <th scope="col" class="px-4 py-3">Subdivision Name</th>
    <th scope="col" class="px-4 py-3">Closest Distance (M)</th>
    <th scope="col" class="px-4 py-3">Action</th>
  </tr>
  </thead>
  @foreach($panoramas as $panorama)
    <tr class="border-b bg-gray-800 border-gray-700">
      <td class="px-4 py-3 font-medium w-8">{{substr(string: $panorama->id, offset: 0, length: 12)}}</td>
      <td class="px-4 py-3">{{$panorama->captured_date}}</td>
      <td class="px-4 py-3">{{$panorama->country_name}} ({{$panorama->country_panoramas_count}})</td>
      <td class="px-4 py-3">{{$panorama->country_subdivision_name}} ({{$panorama->country_subdivision_panoramas_count}})</td>
      <td class="px-4 py-3 text-right">{{$panorama->distance_to_closest}}</td>
      <td class="px-4 py-2">
        <button type="button"
                class="text-blue-500 hover:text-white border border-blue-500 hover:bg-blue-500 font-medium rounded-md text-sm ml-1 px-2.5 py-0.5 hover:scale-110 transition-all duration-75 text-center"
                onclick="window.open('/panorama/{{$panorama->panorama_id}}/view', 'pano', 'popup')">View Panorama
        </button>
        <button type="button"
                class="text-blue-500 hover:text-white border border-blue-500 hover:bg-blue-500 font-medium rounded-md text-sm ml-1 px-2.5 py-0.5 hover:scale-110 transition-all duration-75 text-center"
                onclick="window.open('/panorama/{{$panorama->closest_panorama_id}}/view', 'pano', 'popup')">Closest
        </button>
        <button type="button"
                class="text-red-500 hover:text-white border border-red-500 hover:bg-red-500 font-medium rounded-md text-sm ml-4 px-2.5 py-0.5 hover:scale-110 transition-all duration-75 text-center"
                hx-post="/page/curate/street-view-user/{{$userId}}/panorama/{{$panorama->id}}/reject">Reject
        </button>
        <button type="button"
                class="text-green-500 hover:text-white border border-green-500 hover:bg-green-500 font-medium rounded-md text-sm ml-1 px-2.5 py-0.5 hover:scale-110 transition-all duration-75 text-center"
                hx-post="/page/curate/street-view-user/{{$userId}}/panorama/{{$panorama->id}}/accept">Accept
        </button>
      </td>
    </tr>
  @endforeach
</table>
<script>
  var add_panorama = function () {
    let tag_elements = document.getElementById('tags').querySelectorAll('input[type="checkbox"]');
    let tags_unchecked = [];
    let tags_checked = [];
    tag_elements.forEach(val => {
      if (val.checked) tags_checked.push(val.value); else tags_unchecked.push(val.value);
    });
    fetch('/web-api/panorama/street-view-url', {
      method: 'POST', headers: {
        'Content-Type': 'application/json'
      }, body: JSON.stringify({
        street_view_url: document.getElementById('map-url').value,
        tags_checked: tags_checked,
        tags_unchecked: tags_unchecked,
        street_view_viewport: document.getElementById('street_view_viewport').checked,
      }),
    }).then(resp => resp.json()).then(json => {
      if (json['status'] === 'failed') {
        window.notify.error("Failed to add location to the game, Panorama API error.");
      } else if (json['exists']) {
        let message = "This location has already been discovered!";
        window.notify.open({
          type: "warning", message: message, duration: 14000,
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
        if (!json['from_id']) {
          window.notify.open({
            type: "info", message: "*** From: Location ***", duration: 14000,
          });
        }
        document.getElementById('map-url').value = '';
      } else {
        location.reload();
      }
    }).catch(err => {
      window.notify.error("Failed to add location to the game, please report this url on discord.");
      console.error(err);
    });
  }

  document.getElementById("map-url").addEventListener("keyup", function (event) {
    if (event.key === 'Enter') {
      add_panorama();
    }
  });
</script>