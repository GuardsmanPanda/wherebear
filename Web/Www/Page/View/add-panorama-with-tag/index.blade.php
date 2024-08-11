<?php declare(strict_types=1); ?>
<div class="h-full w-full flex flex-col">
  <x-bear::form.text id="map-url" required="" class="text-gray-700" autocomplete="off"></x-bear::form.text>
  <div class="mt-1">
    <h3 class="font-bold text-teal-500">Tags</h3>
    <div class="flex gap-8 ml-2 items-center">
      <div class="flex items-center">
        <label class="mr-2 font-medium text-gray-400" for="GREAT">GREAT</label>
        <input id="GREAT" type="checkbox" name="tag" value="GREAT">
      </div>
      <div class="flex items-center">
        <label class="mr-2 font-medium text-gray-400" for="LANDSCAPE">LANDSCAPE</label>
        <input id="LANDSCAPE" type="checkbox" name="tag" value="LANDSCAPE">
      </div>
      <div class="flex items-center">
        <label class="mr-2 font-medium text-gray-400" for="DRONE">DRONE</label>
        <input id="DRONE" type="checkbox" name="tag" value="DRONE">
      </div>
    </div>
  </div>
  <x-bear::form.text id="streetviews-org-url" class="mt-6 text-gray-700 w-80" autocomplete="off">
  </x-bear::form.text>
  <div id="map" class="w-full flex-grow my-4"></div>
</div>
<script>
  document.getElementById("map-url").addEventListener("keyup", function (event) {
    if (event.key === 'Enter') {
      //add_panorama();
    }
  });
  document.getElementById("streetviews-org-url").addEventListener("keyup", function (event) {
    if (event.key === 'Enter') {
      const url = document.getElementById("streetviews-org-url").value;
      fetch("/page/add-panorama-with-tag/streetviews-org-url", {
        method: "POST", headers: {
          "Content-Type": "application/json",
        }, body: JSON.stringify({url: url}),
      }).then(resp => {
        return resp.text();

      }).then(text => {
        console.log("https://www.google.com/maps/@?api=1&map_action=pano&pano=" + text)
        window.open("https://www.google.com/maps/@?api=1&map_action=pano&pano=" + text)
      });
    }
  });
</script>