<?php declare(strict_types=1); ?>
@extends('layout.blank-layout')
@section('content')
  <div id="map" class="w-full h-full">
    <div class="font-bold text-xl absolute z-10">test</div>
  </div>
  <script>
    let countries = @json($countries);

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


    map.on('click', function (e) {
      // Rounded to nearest meter-ish (5 decimal places)
      const lng = e.lngLat.lng;
      const lat = e.lngLat.lat;
      const resp = fetch('/flag-game/location-data?' + new URLSearchParams({lat: lat, lng: lng}).toString())
        .then(resp => resp.json())
        .then(json => {
          if (json.country) {
            // create flag element
            const flag = document.createElement('img');
            const text = json.country.name + (json.subdivision ? " - " + json.subdivision.name : "");

            const element = `
              <div style='text-align: center; font-weight: bold; font-size: large; margin: -1px'>${json.country.name}</div>
              ${json.data.iso_3166 ? "<div style='text-align: center; font-size: small'>" + json.data.subdivision_name + "</div>" : ""}
            `

            flag.src = '/static/flag/svg/' + json.country.cca2  + '.svg'
            flag.classList.add('shadow');
            flag.classList.add('border');
            flag.classList.add('border-black');
            flag.style.height = '26px';
            flag.setAttribute('tippy', text);
            //window.tippyFunction(flag);
            window.tippy(flag, {
              allowHTML: true,
              content: element,
              hideOnClick: false,
              sticky: 'reference',
            });

            new window.maplibregl.Marker({element: flag})
              .setLngLat([lng, lat])
              .addTo(map);
            //trigger move the element 1px to the right
          } else {
            window.notify.error("No location found");
          }
        });
    });


  </script>
@endsection
