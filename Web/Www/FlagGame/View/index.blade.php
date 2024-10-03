<?php declare(strict_types=1); ?>
@extends('layout.blank-layout')
@section('content')
  <div id="map" class="w-full h-full">
    <div class="absolute z-10 bg-black opacity-90 py-1 px-4" style="left: 50%;transform: translate(-50%, 0);">
      <div class="font-bold">Click on of these countries!</div>
      <div id="flagSelector" class="flex gap-2 py-1"></div>
    </div>
    <div class=" text-xl absolute z-10 bg-black opacity-90 py-1 px-4 grid grid-cols-3 auto-cols-auto text-right">
      <div class="font-bold text-green-500 col-span-2">Correct</div>
      <div id="correct" class="font-bold">0</div>
      <div class="font-bold text-red-500 col-span-2">Mistakes</div>
      <div id="mistake" class="font-bold">0</div>
      <div class="font-bold col-span-2">Duplicates</div>
      <div id="duplicate" class="font-bold">0</div>
    </div>
  </div>
  <script>
    let countries = @json($countries);
    let flagsToShow = 7;
    let correct = 0;
    let mistake = 0;
    let duplicate = 0;

    const map = new window.maplibregl.Map({
      container: 'map', style: {
        'version': 8, 'sources': {
          'raster-tiles': {
            'type': 'raster', 'tiles': ['{{$map->full_uri}}'], 'tileSize': {{$map->tile_size}},
          }
        }, 'layers': [{'id': 'simple-tiles', 'type': 'raster', 'source': 'raster-tiles'}]
      }, center: [0, 25], dragRotate: false, keyboard: false, minZoom: 1, maxZoom: 18, zoom: 2
    });
    map.scrollZoom.setWheelZoomRate(1 / 75);
    map.scrollZoom.setZoomRate(1 / 75);
    map.touchZoomRotate.disableRotation();

    const flagShuffle = function () {
      for (let i = countries.length - 1; i >= 0; i--) {
        let j = Math.floor(Math.random() * (i + 1));
        let temp = countries[i];
        countries[i] = countries[j];
        countries[j] = temp;
      }
      const selector = document.getElementById('flagSelector');
      selector.innerHTML = '';
      for (let i = 0; i < flagsToShow; i++) {
        const imgNode = document.createElement('img');
        imgNode.src = '/static/flag/svg/' + countries[i].cca2 + '.svg';
        imgNode.classList.add('shadow');
        imgNode.classList.add('h-12');
        document.getElementById('flagSelector').appendChild(imgNode);
      }
    }

    const updateGuess = function (country) {
      const index = countries.findIndex(c => c.cca2 === country);
      console.log(index, country);
      if (index < flagsToShow && index >= 0) {
        correct++;
        document.getElementById('correct').innerHTML = "" + correct;
        // remove the country from the list
        countries.splice(index, 1);
        flagShuffle();
      } else if (index === -1) {
        duplicate++;
        document.getElementById('duplicate').innerHTML = "" + duplicate;

      } else {
        mistake++;
        document.getElementById('mistake').innerHTML = "" + mistake;
        countries.splice(index, 1);
      }
    }
    flagShuffle();


    map.on('click', function (e) {
      // Rounded to nearest meter-ish (5 decimal places)
      const lng = e.lngLat.lng;
      const lat = e.lngLat.lat;
      const resp = fetch('/flag-game/location-data?' + new URLSearchParams({lat: lat, lng: lng}).toString())
        .then(resp => resp.json())
        .then(json => {
          // create flag element
          const flag = document.createElement('img');
          const text = json.country.name + (json.subdivision ? " - " + json.subdivision.name : "");

          const element = `
              <div style='text-align: center; font-weight: bold; font-size: large; margin: -1px'>${json.country.name}</div>
              ${json.data.iso_3166 ? "<div style='text-align: center; font-size: small'>" + json.data.subdivision_name + "</div>" : ""}
            `

          flag.src = '/static/flag/svg/' + json.country.cca2 + '.svg'
          flag.classList.add('shadow');
          flag.classList.add('border');
          flag.classList.add('border-black');
          flag.style.height = '26px';
          flag.setAttribute('tippy', text);
          //window.tippyFunction(flag);
          {{-- window.tippy(flag, {
            allowHTML: true, content: element, hideOnClick: false, sticky: 'reference',
          }); --}}

          const el = document.createElement('div');
          el.innerHTML = `
            <div class="flex flex-col items-center gap-1 relative bottom-8">
              <div class="flex flex-col items-center p-1 rounded bg-gray-800">
                <span class="text-xl font-bold">${json.country.name}</span>
                ${json.data.iso_3166 ? `<span class="text-xs">${json.data.subdivision_name}</span>` : ``} 
              </div>
              <img class="w-min h-[26px] rounded border border-gray-900" src="/static/flag/svg/${json.country.cca2}.svg" />
            </div>
          `;
          

          new window.maplibregl.Marker({element: el})
            .setLngLat([lng, lat])
            .addTo(map);

          updateGuess(json.country.cca2);
        });
    });
  </script>
@endsection
