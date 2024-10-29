<?php declare(strict_types=1); ?>

<div x-data="state({{ $isDev }})" class="flex flex-col h-screen">
  <div class="relative flex-1 overflow-hidden">
    <div class="flex flex-col min-w-16 absolute top-2 left-2 z-10 rounded border border-gray-700">
      <div class="w-[6px] h-4 absolute -top-[10px] left-[8px] z-10 rounded-b-md border  border-gray-700 bg-gray-50"
        style="box-shadow: 0 2px 1px rgb(0 0 0 / 0.4)"></div>
      <div class="w-[6px] h-4 absolute -top-[10px] right-[8px] z-10 rounded-b-md border border-gray-700 bg-gray-50"
        style="box-shadow: 0 2px 1px rgb(0 0 0 / 0.4)"></div>
      <div class="flex justify-center items-center px-1 py-0 rounded-t border-b border-gray-700 bg-iris-500 font-heading text-sm font-medium text-white"
        style="box-shadow: inset 0 2px 1px rgb(255 255 255 / 0.4)">{{ $game->captured_year }}</div>
      <div class="flex justify-center items-center px-1 py-0.5 rounded-b bg-white font-heading text-sm font-medium text-gray-800"
        style="box-shadow: inset 0 -2px 1px rgb(0 0 0 / 0.4)">{{ $game->captured_month }}</div>
    </div>

    <div id="panorama"></div>

    <div>
      <div id="smallScreenMap" tabIndex="-1"
        class="block sm:hidden absolute top-0 w-full h-full z-10 border-r-2 border-gray-800 transition-all duration-300"
        :class="{ '-right-[2px]': screens.small.isVisible, 'right-full': !screens.small.isVisible }"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="translate-x-full"
        x-transition:enter-end="translate-x-0"
        x-transition:leave="transition ease-in duration-300"
        x-transition:leave-start="translate-x-0"
        x-transition:leave-end="translate-x-full"
        @keyup.esc="closeSmallMap()">
      </div>

      <div id="largeScreenMap"
        class="hidden sm:block aspect-square absolute top-0 right-0 z-10 rounded-bl opacity-75 hover:opacity-100 drop-shadow-xl transition-all duration-300 ease-in-out"
        x-data="{
          size: 0,
          calculateSize() {
            this.size = screens.large.fullMapWidthPx;
          }
        }"
        x-init="calculateSize()"
        :style="`width: ${size}px; height: ${size}px; clip-path: ${screens.large.clipStyle};`"
        x-on:resize.window="calculateSize()"
        x-on:mouseenter="screens.large.onMouseEnter();"
        x-on:mouseleave="screens.large.onMouseLeave();"
      >
      </div>
    </div>

    <lit-button-square label="GUESS" 
      imgPath="/static/img/icon/map-with-marker.svg"
      bgColorClass="bg-iris-400"
      class="block sm:hidden absolute top-1/2 right-0 mr-2"
      x-on:clicked="openSmallMap()"
    ></lit-button-square>

    <div class="flex justify-center items-center min-w-40 absolute bottom-2 right-0 z-10 -skew-x-12 mr-[10px] ml-24 px-3 py-1 rounded border border-gray-700 bg-gray-50">
       <div class="flex justify-center items-center h-4 absolute -top-[8px] right-2 skew-x-12 rounded pl-3 pr-1 border border-gray-800 bg-gray-700">
        <img src="/static/img/icon/marker-red.svg" width="28" height="28" class="absolute -top-[10px] left-0 transform -translate-x-1/2" />
        <span class="text-xs text-gray-50 font-medium">Your Guess</span>
      </div>
      <span x-text="guessedCountry" class="skew-x-12 text-lg text-gray-700 font-medium"></span>
      <span x-show="!guessedCountry" class="skew-x-12 text-lg text-gray-700 font-medium">...</span>
    </div>
  </div>

  <x-play-footer
    secondsRemaining="{{ $game->round_seconds_remaining }}"
    :rounds="$rounds"
    :totalRoundCount="$game->number_of_rounds"
    :currentRound="$game->current_round"
    :selectedRound="$game->current_round"
    page="play"
  />
</div>

<script>
  class CloseMapButtonControl {
    onAdd(map) {
      this._map = map;
      this._container = document.createElement('lit-button');
      this._container.setAttribute('imgPath', '/static/img/icon/cross.svg');
      this._container.setAttribute('size', 'sm');
      this._container.setAttribute('bgColorClass', 'bg-gray-400');
      this._container.setAttribute('x-on:click', 'closeSmallMap()');
      
      this._container.className = 'maplibregl-ctrl';

      return this._container;
    }

    onRemove() {
      this._container.parentNode.removeChild(this._container);
      this._map = undefined;
    }
  }

  function state(isDev) {
    return {
      closeSmallScreenVisibilityTimeout: null,
      firstGuessMade: false,
      guessedCountry: null,
      mapIcon: null,
      marker: null,
      markerLngLat: null,
      requestThrottleTimeout: null,
      screens: {
        small: {
          divId: 'smallScreenMap',
          map: null,
          mapElement: null,
          mapIcon: null,
          marker: null,
          isVisible: false,
        },
        large: {
          divId: 'largeScreenMap',
          isHovered: false,
          map: null,
          mapIcon: null,
          marker: null,
          hasClicked: false,
          hasZoomedAfterLastLeft: false,
          minimifiedMapMinSidePercentage: 0.25,
          mapMinSidePercentage: 0.75,
          get width() {
            return `${this.fullMapWidthPx}px`;
          },
          get height() {
            return `${this.fullMapHeightPx}px`;
          },
          get panoramaMinSidePx() {
             const panoramaEl = document.getElementById('panorama'); 
             return Math.min(panoramaEl.clientWidth, panoramaEl.clientHeight);
          },
          get minimifiedMapWidthPx() {
            return this.panoramaMinSidePx * this.minimifiedMapMinSidePercentage;
          },
          get minimifiedMapHeightPx() {
            return this.panoramaMinSidePx * this.minimifiedMapMinSidePercentage;
          },
          get fullMapWidthPx() {
            return this.panoramaMinSidePx * this.mapMinSidePercentage;
          },
          get fullMapHeightPx() {
            return this.panoramaMinSidePx * this.mapMinSidePercentage;
          },
          get clipStyle() {
            const panoramaEl = document.getElementById('panorama');
            const minSidePx = Math.min(panoramaEl.clientWidth, panoramaEl.clientHeight);

            if (this.isHovered) {
              return `ellipse(${minSidePx * this.mapMinSidePercentage}px ${minSidePx * this.mapMinSidePercentage}px at 100% 0)`;
            } else {
              return `ellipse(${this.minimifiedMapWidthPx}px ${this.minimifiedMapHeightPx}px at 100% 0)`;
            }
          },
          onMouseEnter() {
            this.isHovered = true;
            this.hasClicked = false;
            this.hasDragged = false;
    
            if (this.marker && this.hasZoomedAfterLastLeft) {
              this.map.flyTo({ 
                center: this.marker.getLngLat(),
                zoom: this.map.getZoom() + 1
              });
            }
          },
          onMouseLeave() {
            this.isHovered = false;

            // If the map must be centered on the maker
            if (this.marker && (this.hasClicked || this.hasZoomedAfterLastLeft) && !(this.hasDragged && !this.hasClicked)) {
              const mapEl = document.getElementById('largeScreenMap');
              const mapWidthPx = mapEl.clientWidth;
              const mapHeightPx = mapEl.clientHeight;
              const minMapSidePx = Math.min(mapWidthPx, mapHeightPx);

              const getLatOffset = (markerLat, zoom) => {
                if (markerLat > 75) {
                  if (zoom > 1) {
                    // TODO: implement the logic when the map is zoomed
                    return mapHeightPx * 0.1;
                  }
                }
                return -mapHeightPx * this.minimifiedMapMinSidePercentage;
              }
 
              this.map.flyTo({
                center: this.marker.getLngLat(),
                zoom: this.map.getZoom() - 1,
                offset: [this.fullMapWidthPx * 0.345, getLatOffset(this.marker.getLngLat().lat, this.map.getZoom())]
              });

              this.hasZoomedAfterLastLeft = true;
            } else {
              this.map.zoomOut();
              this.hasZoomedAfterLastLeft = false;
            }
          }
        }
      },
      closeSmallMap() {
        this.screens.small.isVisible = false;
      },
      openSmallMap() {
        this.screens.small.isVisible = true;
        this.screens.small.mapElement.focus();
      },
      createMap(divId) {
        const map = new maplibregl.Map({
          container: divId, 
          style: {
            'version': 8, 
            'sources': {
              'raster-tiles': {
                'type': 'raster', 
                'tiles': ['{{$user->map_style_full_uri}}'], 
                'tileSize': {{$user->map_style_tile_size}},
              }
            }, 
            'layers': [{'id': 'simple-tiles', 'type': 'raster', 'source': 'raster-tiles'}]
          }, 
          center: [0, 25], 
          dragRotate: false,
          keyboard: false, 
          minZoom: 1, 
          maxZoom: 18, 
          zoom: 1,
          attributionControl: false
        });

        map.scrollZoom.setWheelZoomRate(1 / 75);
        map.scrollZoom.setZoomRate(1 / 75);
        map.touchZoomRotate.disableRotation();
        
        return map;
      },
      placeMarkerOnMaps(latlng) {
        for (const screen in this.screens) {
          const currentScreen = this.screens[screen];
          
          if (!this.firstGuessMade) {
            currentScreen.marker = new maplibregl.Marker({element: currentScreen.mapIcon, anchor: 'bottom'})
              .setLngLat([latlng.lng, latlng.lat])
              .addTo(currentScreen.map);
          } else {
            currentScreen.marker.setLngLat([latlng.lng, latlng.lat]);
          }
        }
        this.firstGuessMade = true;
      },
      saveMarkerLocation(latlng, callback) {
        if (isDev) {
          callback({ country_cca2: 'FR', country_name: 'France' });
        } else {
          fetch('/game/{{ $game->id }}/play/guess', {
            method: 'PUT',
            headers: {
              'Content-Type': 'application/json',
            },
            body: JSON.stringify(latlng),
          })
            .then((resp) => {
              if (!resp.ok) {
                return Promise.reject(resp);
              }
              return resp.json();
            })
            .then((data) => {
              callback(data);
            })
            .catch((error) => console.error(error.statusText || error));
        }
      },
      scheduleSmallScreenClose() {
        if (this.closeSmallScreenVisibilityTimeout) {
          clearTimeout(this.closeSmallScreenVisibilityTimeout);
        }

        this.closeSmallScreenVisibilityTimeout = setTimeout(() => {
          this.screens.small.isVisible = false;
        }, 1600);
      },
      init() {
        const mapMarker = `<lit-map-marker iconFilePath="{{ $user->map_marker_file_path }}"></lit-map-marker>`;

        // Initialize the small screen map
        let smallMapIcon = document.createElement('div');
        smallMapIcon.innerHTML = mapMarker;
        this.screens.small.mapIcon = smallMapIcon;
        this.screens.small.map = this.createMap(this.screens.small.divId);

        this.screens.small.map.addControl(new CloseMapButtonControl());

        this.screens.small.map.on('click', e => {
          // data: {country_cca2: 'FR', country_name: 'France'}
          this.saveMarkerLocation(e.lngLat, (data) => {
            this.placeMarkerOnMaps(e.lngLat);
            this.scheduleSmallScreenClose();
            this.guessedCountry = data.country_name;
          });
        });

        this.screens.small.mapElement = document.getElementById(this.screens.small.divId);

        // Initialize the large screen map
        let largeMapIcon = document.createElement('div');
        largeMapIcon.innerHTML = mapMarker;
        this.screens.large.mapIcon = largeMapIcon;
        this.screens.large.map = this.createMap(this.screens.large.divId);

        this.screens.large.map.on('click', e => {
          // data: {country_cca2: 'FR', country_name: 'France'}
          this.saveMarkerLocation(e.lngLat, (data) => {
            this.placeMarkerOnMaps(e.lngLat);
            this.screens.large.hasClicked = true;
            this.guessedCountry = data.country_name;
          });
        });  

        this.screens.large.map.on('drag', e => {
          // In case the user click then drag
          this.screens.large.hasDragged = true;
          this.screens.large.hasClicked = false;
        });
      },
    }
  }

  pannellum.viewer('panorama', {
    type: "equirectangular",
    panorama: "{{ $panorama_url }}",
    autoLoad: true,
    showControls: false
  });
</script>