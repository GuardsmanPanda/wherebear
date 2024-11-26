<?php declare(strict_types=1); ?>
@extends('layout.blank-layout')

@section('content')
  <div id="panorama">
    <button class="absolute text-black z-10 button" onclick="saveViewport()">Save Viewport</button>
  </div>
  <script>
    const viewer = pannellum.viewer('panorama', {
      panorama: "{{ $panorama_url }}",
      type: "equirectangular",
      showControls: false,
      autoLoad: true,
      yaw: {{ $heading }},
      pitch: {{ $pitch }},
      hfov: {{ $field_of_view }},
      minHfov: window.innerWidth < 1000 ? 30 : 50,
    });

    function saveViewport() {
      const pitch = viewer.getPitch();
      const heading = viewer.getYaw();
      const field_of_view = viewer.getHfov();
      console.log({ pitch, heading, field_of_view });
      fetch('/panorama/{{$panorama_id}}/viewport', {
        method: 'PATCH',
        headers: {
          'Content-Type': 'application/json',
        },
        body: JSON.stringify({ pitch, heading, field_of_view }),
      }).then(response => {
        if (response.ok) {
          window.notify.success('Viewport saved');
        } else {
          window.notify.error('Failed to save viewport');
        }
      });
    }
  </script>
@endsection
