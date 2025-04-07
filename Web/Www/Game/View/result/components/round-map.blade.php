<lit-game-round-map
  x-ref="map"
  mapStyleEnum="{{ $user->map_style_enum }}"
  mapStyleTileSize="{{ $user->map_style_tile_size }}"
  mapStyleFullUri="{{ $user->map_style_full_uri }}"
  panoramaLocationMarkerAnchor="{{ $user->map_location_marker_anchor }}"
  panoramaLocationMarkerImgPath="{{ $user->map_location_marker_img_path }}"
  :guesses="JSON.stringify(selectedRound.guesses)"
  :panoramaLat="selectedRound.panorama.lat"
  :panoramaLng="selectedRound.panorama.lng"
  class="absolute top-0 left-0 w-full h-full transition-opacity duration-300 ease-in-out" 
  :class="{
    'opacity-100 pointer-events-auto': currentMode === 'map',
    'opacity-0 pointer-events-none': currentMode !== 'map'
  }">
</lit-game-round-map> 