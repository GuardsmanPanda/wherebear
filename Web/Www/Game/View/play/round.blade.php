<?php

declare(strict_types=1);

?>

<div x-data="pageHandler" class="flex flex-col h-screen bg-green-200">
  <div class="relative flex-1 overflow-hidden">
    <div class="flex flex-col min-w-16 absolute top-0 left-0 z-10 rounded-br border-r border-b border-gray-700">
      <div class="flex justify-center items-center px-1 py-0.5 bg-blue-500 font-heading text-sm font-medium text-white select-none">{{$game->captured_year}}</div>
      <div class="flex justify-center items-center px-1 py-0.5 rounded-br-md bg-white font-heading text-sm font-medium text-gray-800 select-none">{{$game->captured_month}}</div>
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
        class="hidden sm:block w-4/12  h-1/3  absolute top-0 right-0 rounded-bl opacity-75 hover:opacity-100 drop-shadow-xl transition-all duration-[100ms]"
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
      <div class="flex justify-start items-center w-12 h-8 absolute bottom-[60px] left-[13px]">
        <span x-data
          :class="{
              'ml-[19px]': timeRemainingSec.toString().length === 1,
              'ml-[14px]': timeRemainingSec.toString().length === 2,
              'ml-[9px]': timeRemainingSec.toString().length === 3
          }" 
          x-text="timeRemainingSec" 
          class="font-heading text-xl font-medium text-gray-900 select-none z-20">
        </span>
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
              console.log("map center in before: ", this.map.getCenter());
              this.map.zoomIn(1, {animate: false});
              console.log("map center in after: ", this.map.getCenter());
            },
            onMouseLeave() {
              console.log("map center out before: ", this.map.getCenter());
              this.map.zoomOut(1, {animate: false});
              console.log("map center out after: ", this.map.getCenter());
            },
          },
        },
        mapClickHandler(latlng) {
          fetch('/game/{{ $game->id }}/play/guess', {
            method: 'PUT',
            headers: {
              'Content-Type': 'application/json'
            },
            body: JSON.stringify(latlng),
          }).then(resp => {
              if (!resp.ok) {
                return Promise.reject(resp);
              }
            this.placeMarkerOnMaps(latlng);
          }).catch(error => console.error(error.statusText || error));
        },
        placeMarkerOnMaps(latlng) {
          for (const screen in this.screens) {
            const currentScreen = this.screens[screen];
            if (!this.firstGuessMade) {
              currentScreen.marker = new window.maplibregl.Marker({element: this.mapIcon, anchor: 'bottom'})
                .setLngLat([latlng.lng, latlng.lat])
                .addTo(currentScreen.map);
            } else {
              currentScreen.marker.setLngLat([latlng.lng, latlng.lat]);
            }
          }
          this.firstGuessMade = true;
        },
        setupMap(screen) {
          const mapInstance = new window.maplibregl.Map({
            container: screen.divId, style: {
              'version': 8, 'sources': {
                'raster-tiles': {
                  'type': 'raster', 'tiles': ['{{$user->map_style_full_uri}}'], 'tileSize': {{$user->map_style_tile_size}},
                }
              }, 'layers': [{'id': 'simple-tiles', 'type': 'raster', 'source': 'raster-tiles'}]
            }, center: [0, 25], dragRotate: false, keyboard: false, minZoom: 1, maxZoom: 18, zoom: 1
          });
          mapInstance.scrollZoom.setWheelZoomRate(1 / 75);
          mapInstance.scrollZoom.setZoomRate(1 / 75);
          mapInstance.touchZoomRotate.disableRotation();

          mapInstance.on('click', e => {
            this.mapClickHandler(e.lngLat);
          });
          screen.map = mapInstance;
        }
      },
      init() {
        const elem = document.createElement('img');
        elem.src = '{{ $user->map_marker_file_path }}';
        elem.style.height = '48px';
        elem.classList.add('drop-shadow');
        this.maps.mapIcon = elem;

        for (const screen in this.maps.screens) {
          this.maps.setupMap(this.maps.screens[screen]);
        }
      }
    }
  }
</script>