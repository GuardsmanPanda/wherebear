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
      <div x-data="slidePanelState" x-ref="slidePanel" id="slidePanel" tabIndex="-1"
        class="block lg:hidden absolute top-0 w-full h-full z-10 transition-all duration-300"
        :class="{ 'right-0': isOpened, 'right-full': !isOpened }"
        x-on:slide-panel-opened.window="open"
        @keyup.esc="close">
      </div>

      <div x-data="mapState" x-ref="map" class="hidden lg:block">
        <div x-ref="mapBg" 
          class="absolute top-0 right-0 z-30 rounded-bl-md border-b-4 border-l-4 transition-all duration-300 ease-in-out"
          :class="{ 'border-gray-100': !isExpanded, 'border-gray-50': isExpanded }"
          :style="mapBackgroundStyle"
          style="pointer-events: none;">

          @if($user->is_guess_indicator_allowed)
          <div class="absolute -bottom-px left-0 max-w-full h-8">
            <div class="flex justify-center items-center h-4 absolute -top-[8px] left-[10px] z-10 rounded pl-3 pr-1 border border-gray-800 bg-gray-700">
              <img src="/static/img/icon/marker-red.svg" width="28" height="28" class="absolute -top-[10px] left-0 transform -translate-x-1/2" />
              <span class="text-xs text-gray-50 font-medium">Your Guess</span>
            </div>
            <div
              class="min-w-32 max-w-full h-full flex justify-center pt-1.5 pr-4 pl-1 relative right-px"
              :class="{ 'bg-gray-100': !isExpanded, 'bg-gray-50': isExpanded }"
              style="clip-path: polygon(0 0, calc(100% - 16px) 0, 100% 100%, 0 100%);">
              <span x-text="guessedCountry" class="text-lg text-gray-700 font-medium truncate"></span>
              <span x-show="!guessedCountry" class="text-lg text-gray-700 font-medium">...</span>
            </div>
          </div>
          @endif
        </div>

        <div id="map"
          class="hidden lg:block absolute top-0 right-0 z-20 transition-all duration-300 ease-in-out"
          :style="mapStyle" 
          x-on:mouseenter="expandMap"
          x-on:mouseleave="minifyMap">
        </div>
      </div>
    </div>
    @endif

    @if($user->is_player)
    <lit-button-square label="GUESS" 
      imgPath="/static/img/icon/map-with-marker.svg"
      bgColorClass="bg-iris-400"
      class="block lg:hidden absolute top-1/2 right-0 mr-2"
      x-on:clicked="openSlidePanel()"
    ></lit-button-square>
    @endif

    @if($user->is_player && $user->is_guess_indicator_allowed)
    
    <div class="lg:hidden absolute bottom-2 right-0 z-10 mr-[10px] min-w-40 px-3 py-1 flex justify-center items-center
      before:absolute before:-z-10 before:-skew-x-12 before:w-full before:h-full before:rounded before:border before:border-gray-700 before:bg-gray-50">
        <div class="flex justify-center items-center h-4 absolute -top-[8px] right-2 rounded pl-3 pr-1 border border-gray-800 bg-gray-700">
          <img src="/static/img/icon/marker-red.svg" width="28" height="28" class="absolute -top-[10px] left-0 transform -translate-x-1/2" />
          <span class="text-xs text-gray-50 font-medium">Your Guess</span>
        </div>
        <span x-text="guessedCountry" class="text-lg text-gray-700 font-medium"></span>
        <span x-show="!guessedCountry" class="text-lg text-gray-700 font-medium">...</span>
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
  class MapLibreCloseButtonControl {
    onAdd(map) {
      this._map = map;
      this._container = document.createElement('lit-button');
      this._container.setAttribute('imgPath', '/static/img/icon/cross.svg');
      this._container.setAttribute('size', 'sm');
      this._container.setAttribute('bgColorClass', 'bg-gray-400');
      this._container.setAttribute('x-on:click', 'close()');
      
      this._container.className = 'maplibregl-ctrl';

      return this._container;
    }

    onRemove() {
      this._container.parentNode.removeChild(this._container);
      this._map = undefined;
    }
  }

  pannellum.viewer('panorama', {
    type: "equirectangular",
    panorama: "{{ $panorama_url }}",
    autoLoad: true,
    showControls: false,
    minHfov: window.innerWidth < 1000 ? 30 : 50,
  });

  document.addEventListener('alpine:init', () => {
    // The global state of the page
    Alpine.data('state', (isDev) => ({
      guessedCountry: null,
      user: @json($user),
      createMapLibreMap(divId) {
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
      createMapMarkerElement(mapMarkerFilePath) {
        const mapMarker = `<lit-map-marker iconFilePath="${mapMarkerFilePath}"></lit-map-marker>`;
        let mapMarkerElement = document.createElement('div');
        mapMarkerElement.innerHTML = mapMarker;
        return mapMarkerElement;
      },
      dispatchMapMarkerPlacedEvent(lngLat) {
        document.dispatchEvent(new CustomEvent('map-marker-placed', {
          detail: { lngLat: lngLat }
        }));
      },
      openSlidePanel() {
         window.dispatchEvent(new CustomEvent('slide-panel-opened'));
      },
      saveMapMarkerLocation(lngLat, callback) {
        if (isDev) {
          callback({ country_cca2: 'FR', country_name: 'Saint Vincent and the Grenadines' });
        } else {
          fetch('/game/{{ $game->id }}/play/guess', {
            method: 'PUT',
            headers: {
              'Content-Type': 'application/json',
            },
            body: JSON.stringify(lngLat),
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
      }
    }));

    /** The state of the slide panel containing the map for small screens. */
    Alpine.data('slidePanelState', (isDev) => ({
      closeTimeout: null,
      isDraging: false,
      isFirstGuessMade: false,
      isOpened: false,
      mapLibreMap: null,
      mapLibreMarker: null,
      mapMarkerElement: null,
      close() {
        this.isOpened = false;
      },
      open() {
        this.isOpened = true;
        this.$refs.slidePanel.focus();
      },
      placeMapMarker(lngLat) {
        if (!this.isFirstGuessMade) {
          this.mapLibreMarker = new maplibregl.Marker({element: this.mapMarkerElement, anchor: 'bottom'})
            .setLngLat([lngLat.lng, lngLat.lat])
            .addTo(this.mapLibreMap);
        } else {
          this.mapLibreMarker.setLngLat([lngLat.lng, lngLat.lat]);
        }
        this.isFirstGuessMade = true;
      },
      interruptScheduledClose() {
        if (this.closeTimeout){
          clearTimeout(this.closeTimeout);
        }
      },
      scheduleClose() {
        this.interruptScheduledClose();
        this.closeTimeout = setTimeout(() => {
          this.isOpened = false;
        }, 1600);
      },
      init() {
        this.mapMarkerElement = this.createMapMarkerElement(this.user.map_marker_file_path);
        this.mapLibreMap = this.createMapLibreMap('slidePanel');
        this.mapLibreMap.addControl(new MapLibreCloseButtonControl());
        this.mapLibreMap.on('click', e => {
          // data: {country_cca2: 'FR', country_name: 'France'}
          this.saveMapMarkerLocation(e.lngLat, (data) => {
            this.scheduleClose();
            this.guessedCountry = data.country_name;
            this.dispatchMapMarkerPlacedEvent(e.lngLat);
          });
        });
        this.mapLibreMap.on('drag', e => {
          this.interruptScheduledClose();
        });

        document.addEventListener('map-marker-placed', (e) => {
          this.placeMapMarker(e.detail.lngLat)
        });
      }
    }));

    /** The state of the map and its minified version for large screens. */
    Alpine.data('mapState', (isDev) => ({
      isFirstGuessMade: false,
      isExpanded: false,
      isMapMinifyScheduled: false,
      mapLibreMap: null,
      mapLibreMarker: null,
      mapMarkerElement: null,
      mapWidthPx: 0,
      mapHeightPx: 0,
      miniMapWidthPx: 0,
      miniMapHeightPx: 0,
      resizeObserver: null,
      get panoramaElement() {
        return document.getElementById('panorama');
      },
      get mapStyle() {
        const miniMapClippedWidthPx = this.mapWidthPx - this.miniMapWidthPx;
        return {
          'width': `${this.mapWidthPx}px`,
          'height': `${this.mapHeightPx}px`,
          'opacity': `${this.isExpanded ? 100 : 75}%`,
          'clip-path': this.isExpanded 
            // Original size instead of 'none' to make the transition animation works
            ? `polygon(0 0, 100% 0, 100% 100%, 0 100%)`
            // The minimified map
            : `polygon(
              ${miniMapClippedWidthPx}px 0, 
              100% 0, 
              100% ${this.miniMapHeightPx}px, 
              ${miniMapClippedWidthPx}px ${this.miniMapHeightPx}px
            )`
        }
      },
      get mapBackgroundStyle() {
        const borderPx = 4;              
        return {
          width: this.isExpanded 
            ? `${this.mapWidthPx + borderPx}px`
            : `${this.miniMapWidthPx + borderPx}px`,
          height: this.isExpanded 
            ? `${this.mapHeightPx + borderPx}px`
            : `${this.miniMapHeightPx + borderPx}px`,
        };
      },
      expandMap() {
        this.isMapMinifyScheduled = false;
        if (!this.isExpanded) {
          if (this.mapLibreMarker) {
            this.mapLibreMap.flyTo({ 
              center: this.mapLibreMarker.getLngLat(),
              zoom: this.mapLibreMap.getZoom() + 1
            });
          }
          this.isExpanded = true;
        }
      },
      minifyMap() {
        this.isMapMinifyScheduled = true;
        setTimeout(() => {
          if (this.isMapMinifyScheduled) {
            if (this.mapLibreMarker) {
              this.mapLibreMap.flyTo({
                center: this.mapLibreMarker.getLngLat(),
                duration: 300,
                offset: [
                  (this.mapWidthPx - this.miniMapWidthPx) / 2,
                  -(this.mapHeightPx - this.miniMapHeightPx) / 2
                ],
                zoom: this.mapLibreMap.getZoom() - 1,
              });
            }
            this.isExpanded = false;
          }
        }, 300);
      },
      placeMapMarker(lngLat) {
        if (!this.isFirstGuessMade) {
          this.mapLibreMarker = new maplibregl.Marker({element: this.mapMarkerElement, anchor: 'bottom'})
            .setLngLat([lngLat.lng, lngLat.lat])
            .addTo(this.mapLibreMap);
        } else {
          this.mapLibreMarker.setLngLat([lngLat.lng, lngLat.lat]);
        }
        this.isFirstGuessMade = true;
      },
      setMapSizes(panoramaWidthPx, panoramaHeightPx) {
        // console.log('panorama size px', panoramaWidthPx, panoramaHeightPx);
        const mapBorderPx = 4;

        /** Calculates the pixel width corresponding to a given percentage of the panorama's total width. */
        const widthPercentage = (percentage) => {
          return panoramaWidthPx * percentage / 100;
        };
        /** Calculates the pixel height corresponding to a given percentage of the panorama's total height. */
        const heightPercentage = (percentage) => {
          return panoramaHeightPx * percentage / 100;
        }

        // Set mini map sizes: 30% of the smaller dimension while maintaining a 16:9 aspect ratio
        if (panoramaWidthPx < panoramaHeightPx) {
          this.miniMapWidthPx = widthPercentage(30);
          this.miniMapHeightPx = this.miniMapWidthPx * 9 / 16;

          this.mapWidthPx = widthPercentage(100) - mapBorderPx;
          this.mapHeightPx = heightPercentage(50);
        } else {
          this.miniMapHeightPx = heightPercentage(30);
          this.miniMapWidthPx = Math.min(this.miniMapHeightPx * 16 / 9, widthPercentage(50));

          this.mapWidthPx = widthPercentage(50);
          this.mapHeightPx = heightPercentage(100) - mapBorderPx;
        }
      },
      init() {
        this.mapMarkerElement = this.createMapMarkerElement(this.user.map_marker_file_path);
        this.mapLibreMap = this.createMapLibreMap('map');
        this.mapLibreMap.on('click', e => {
          // data: {country_cca2: 'FR', country_name: 'France'}
          this.saveMapMarkerLocation(e.lngLat, (data) => {
            this.guessedCountry = data.country_name;
            this.dispatchMapMarkerPlacedEvent(e.lngLat);
          });
        });

        document.addEventListener('map-marker-placed', (e) => {
          this.placeMapMarker(e.detail.lngLat)
        });
        
        this.resizeObserver = new ResizeObserver(entries => {
          for (let entry of entries) {
            this.isPortrait = entry.contentRect.width < entry.contentRect.height;
            this.setMapSizes(
              entry.contentRect.width,
              entry.contentRect.height
            );
          }
        });
        this.resizeObserver.observe(this.panoramaElement);
      },
      destroy() {
        this.resizeObserver.disconnect();
      }
    }));
  });
</script>