<?php

declare(strict_types=1);

?>

<div x-data="pageHandler" class="flex flex-col h-screen bg-green-200">
  <div class="relative flex-1 overflow-hidden">
    <div class="flex flex-col min-w-16 absolute top-0 left-0 z-10 rounded-br border-r border-b border-gray-700">
      <div class="flex justify-center items-center px-1 py-0.5 bg-blue-500 font-heading text-sm font-medium text-white">2014</div>
      <div class="flex justify-center items-center px-1 py-0.5 rounded-br-md bg-white font-heading text-sm font-medium text-gray-800">May</div>
    </div>
    <div id="panorama"></div>

    <div>
      <div id="smallScreenMap"
        class="block sm:hidden absolute top-0 w-full h-full z-10 border-r-2 border-gray-800 transition-all duration-300"
        :class="{ '-right-[2px]': maps.screens.small.isVisible, 'right-full': !maps.screens.small.isVisible }"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="translate-x-full"
        x-transition:enter-end="translate-x-0"
        x-transition:leave="transition ease-in duration-300"
        x-transition:leave-start="translate-x-0"
        x-transition:leave-end="translate-x-full">
      </div>

      <div id="largeScreenMap" x-data="{ clipStyle: 'polygon(0 0, 100% 0, 100% 100%, 55% 100%)'}"
        class="hidden sm:block w-4/12 hover:w-2/3 h-1/3 hover:h-2/3 absolute top-0 right-0 rounded-bl opacity-75 hover:opacity-100 drop-shadow-xl transition-all duration-[100ms]"
        x-on:mouseenter="clipStyle = 'none'; maps.screens.large.onMouseEnter();"
        x-on:mouseleave="clipStyle = 'polygon(0 0, 100% 0, 100% 100%, 55% 100%)'; maps.screens.large.onMouseLeave();"
        :style="`clip-path: ${clipStyle}`">
      </div>
    </div>

    <x-button-guess x-data @clicked="maps.screens.small.isVisible = $event.detail" class="block sm:hidden absolute top-1/2 right-0 z-10 transform -translate-y-1/2 mr-1" />
  </div>
  <div x-data="countdown({{ round((float) $game->round_seconds_remaining) }})" class="flex flex-col">
    <div id="guessing-time-progress-bar" class="relative">
      <img src="/static/img/pengu-sign.png" class="absolute -left-1 bottom-[10px] h-20 z-20" alt="Cutest pengu around">
      <div class="flex justify-center items-center w-12 h-8 absolute bottom-[62px] left-[13px]">
        <span x-text="timeRemainingSec" class="font-heading text-xl font-medium text-gray-900 z-30"></span>
      </div>
      <div class="flex w-full h-4 bg-gray-700 border-y border-gray-900" style="box-shadow: inset 0 4px 1px rgb(0 0 0 / 0.3);">
        <div class="rounded-r" x-bind:style="{ 
        width: percentage + '%',
        transition: applyTransition ? 'width ' + intervalDurationMs + 'ms linear, background-color ' + intervalDurationMs + 'ms linear' : 'width ' + intervalDurationMs + 'ms linear',   
        boxShadow: 'inset 0 -6px 1px rgba(0, 0, 0, 0.3)',
        backgroundColor: innerBarHsl
      }"></div>
      </div>
    </div>
    @include('game::play.countries-used')
  </div>
</div>

<script>
  function countdown(durationSec) {
    return {
      percentage: 100,
      timeRemainingSec: durationSec,
      timerInterval: null,
      intervalDurationMs: 1000,
      applyTransition: false,
      get innerBarHsl() {
        return `hsl(${130 * this.percentage / 100}, 80%, 50%)`;
      },
      init() {
        const totalStepCount = (durationSec * 1000) / this.intervalDurationMs;
        const percentageStep = (100 / (totalStepCount));
        let firstCycle = true

        const timerInterval = setInterval(() => {
          if (this.percentage <= 0) {
            clearInterval(timerInterval);
            this.percentage = 0;
            this.timeRemainingSec = 0;
          } else {
            this.percentage = Math.max(this.percentage - percentageStep, 0);

            if (firstCycle) {
              this.applyTransition = true;
            } else {
              this.timeRemainingSec--;
            }
          }
          firstCycle = false;
        }, this.intervalDurationMs);
      }
    }
  }

  pannellum.viewer('panorama', {
    type: "equirectangular",
    panorama: "{{ $panorama_url }}",
    autoLoad: true,
    showControls: false
  });

  function pageHandler() {
    return {
      maps: {
        mapIcon: null,
        firstGuessMade: false,
        requestThrottleTimeout: null,
        screens: {
          small: {
            divId: 'smallScreenMap',
            map: null,
            marker: null,
            isVisible: false
          },
          large: {
            divId: 'largeScreenMap',
            map: null,
            marker: null,
            onMouseEnter() {
              this.map.zoomIn(1);
              this.map.invalidateSize();
            },
            onMouseLeave() {
              this.map.zoomOut(1);
              this.map.invalidateSize();
            },
          },
        },
        handleMarkerPlacement(latlng) {
          this.placeMarkerOnMaps(latlng);

          if (!"{{ $isDev }}") {
            clearTimeout(this.requestThrottleTimeout);

            this.requestThrottleTimeout = setTimeout(() => {
              fetch('/game/{{ $game->id }}/play/guess', {
                  method: 'PUT',
                  headers: {
                    'Content-Type': 'application/json'
                  },
                  body: JSON.stringify(latlng),
                })
                .then(resp => {
                  if (!resp.ok) {
                    return Promise.reject(resp);
                  }
                })
                .catch(error => console.error(error.statusText || error));
            }, 200);
          }
        },
        placeMarkerOnMaps(latlng) {
          for (const screen in this.screens) {
            const currentScreen = this.screens[screen];
            if (!this.firstGuessMade) {
              currentScreen.marker = L.marker(latlng, {
                icon: this.mapIcon
              }).addTo(currentScreen.map);
            } else {
              currentScreen.marker.setLatLng(latlng);
            }
          }
          this.firstGuessMade = true;
        },
        setupMap(screen) {
          const mapInstance = L.map(screen.divId, {
            center: [25, 0],
            worldCopyJump: true,
            zoom: 1,
            zoomControl: false,
          });

          L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxNativeZoom: 17,
            minZoom: 1,
            tileSize: parseInt("{{ $user->map_style_tile_size }}", 10),
            zoomOffset: parseInt("{{ $user->map_style_zoom_offset }}", 10),
            attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>'
          }).addTo(mapInstance);

          mapInstance.on('click', e => {
            this.handleMarkerPlacement(e.latlng);
          });

          screen.map = mapInstance;
        },
      },
      init() {
        this.maps.mapIcon = L.icon({
          iconUrl: '{{ $user->map_marker_file_path }}',
          iconSize: [48, 48],
          iconAnchor: [24, 48],
          tooltipAnchor: [0, -48],
        });

        for (const screen in this.maps.screens) {
          this.maps.setupMap(this.maps.screens[screen]);
        }
      }
    }
  }
</script>