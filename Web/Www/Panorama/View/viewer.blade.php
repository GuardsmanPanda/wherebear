<?php declare(strict_types=1); ?>
@extends('layout.blank-layout')

@section('content')
  <div id="panorama">
    <div class="absolute text-black z-10">test</div>
  </div>
  <script>
    pannellum.viewer('panorama', {
      panorama: "{{ $panorama_url }}",
      type: "equirectangular",
      showControls: false,
      autoLoad: true,
      yaw: {{ $heading }},
      pitch: {{ $pitch }},
      hfov: {{ $field_of_view }},
    });
  </script>
@endsection