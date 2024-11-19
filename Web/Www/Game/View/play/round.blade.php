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

    @if($user->is_player)
    <div>
      <div id="smallScreenMap" tabIndex="-1"
        class="block md:hidden absolute top-0 w-full h-full z-10 border-r-2 border-gray-800 transition-all duration-300"
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
        class="hidden w-full h-full md:block absolute top-0 right-0 z-10 transition-all duration-300 ease-in-out rounded-bl drop-shadow-xl"
        :class="{ 'opacity-100': screens.large.isHovered, 'opacity-75': !screens.large.isHovered }"
        x-init="screens.large.calculateMapSize()"
        :style="screens.large.mapStyle" 
        x-on:resize.window="screens.large.calculateMapSize()"
        x-on:mouseenter="screens.large.onMouseEnter(); hovered = true;"
        x-on:mouseleave="screens.large.onMouseLeave(); hovered = false">
      </div>

    </div>
    @endif

    @if($user->is_player)
    <lit-button-square label="GUESS" 
      imgPath="/static/img/icon/map-with-marker.svg"
      bgColorClass="bg-iris-400"
      class="block md:hidden absolute top-1/2 right-0 mr-2"
      x-on:clicked="openSmallMap()"
    ></lit-button-square>
    @endif

    @if($user->is_player && $user->is_guess_indicator_allowed)
    <div class="flex justify-center items-center min-w-40 absolute bottom-2 right-0 z-10 -skew-x-12 mr-[10px] ml-24 px-3 py-1 rounded border border-gray-700 bg-gray-50">
       <div class="flex justify-center items-center h-4 absolute -top-[8px] right-2 skew-x-12 rounded pl-3 pr-1 border border-gray-800 bg-gray-700">
        <img src="/static/img/icon/marker-red.svg" width="28" height="28" class="absolute -top-[10px] left-0 transform -translate-x-1/2" />
        <span class="text-xs text-gray-50 font-medium">Your Guess</span>
      </div>
      <span x-text="guessedCountry" class="skew-x-12 text-lg text-gray-700 font-medium"></span>
      <span x-show="!guessedCountry" class="skew-x-12 text-lg text-gray-700 font-medium">...</span>
    </div>
    @endif
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
          canMinifiedMap: false,
          /** The percentage of the mini-map's width relative to the panorama's width. */
          miniMapWidthPct: 50,
          /** The percentage of the mini-map's height relative to the panorama's width. */
          miniMapHeightPct: 50,
          /** The percentage of the map's width relative to the panoramas's width. */
          mapWidthPct: 0,
          /** The percentage of the map's height relative to the panoramas's height. */
          mapHeightPct: 100,
          panoramaWidthPx: 0,
          panoramaHeightPx: 0,
          calculateMapSize() {
            const panoramaEl = document.getElementById('panorama');
            this.panoramaWidthPx = panoramaEl.clientWidth;
            this.panoramaHeightPx = panoramaEl.clientHeight;

            if (this.panoramaWidthPx < 1280) {
              this.mapWidthPct = 50;
              this.miniMapWidthPct = 25;
              this.miniMapHeightPct = 35;
            } else {
              this.mapWidthPct = 40;
              this.miniMapWidthPct = 25;
              this.miniMapHeightPct = 30;
            }
          },
          get mapStyle() {            
            const clippedWidth = 100 - (100/(this.mapWidthPct/this.miniMapWidthPct));
            return {
              'width': `${this.mapWidthPct}%`,
              'height': `${this.mapHeightPct}%`,
              'clip-path': this.isHovered 
                ? `polygon(0 0, 100% 0, 100% 100%, 0 100%)` // Original size instead of 'none' to make the transition animation works
                : `polygon(
                  ${clippedWidth}% 0, 
                  100% 0, 
                  100% ${this.miniMapHeightPct}%, 
                  ${clippedWidth}% ${this.miniMapHeightPct}%
                )`
            }
          },
          onMouseEnter() {
            this.canMinifiedMap = false;
    
            if (!this.isHovered) {
              if (this.marker) {
                this.map.flyTo({ 
                  center: this.marker.getLngLat(),
                  zoom: this.map.getZoom() + 1
                });
              }
            }
            this.isHovered = true;
          },
          onMouseLeave() {
            // Schedule to switch isHovered to false if the mouse has not entered meanwhile
            this.canMinifiedMap = true;
            setTimeout(() => {
              if (this.canMinifiedMap) {
                this.isHovered = false;

                if (this.marker) {              
                  this.map.flyTo({
                    center: this.marker.getLngLat(),
                    zoom: this.map.getZoom() - 1,
                    offset: [
                      this.panoramaWidthPx * ((this.mapWidthPct - this.miniMapWidthPct) * 0.5 / 100),
                      -this.panoramaHeightPx * ((this.mapHeightPct - this.miniMapHeightPct) * 0.5 / 100)
                    ]
                  });
                }
              }
            }, 300);
          }
        }
      },
      user: @json($user),
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
              },
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

        if (this.user.is_player) {
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
              this.guessedCountry = data.country_name;
            });
          });  
        }
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