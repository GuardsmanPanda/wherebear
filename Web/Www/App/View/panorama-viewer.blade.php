<?php declare(strict_types=1); ?>
@extends('layout.blank-layout')

@section('content')
  <div id="panorama"></div>
  <script>
    pannellum.viewer('panorama', {
      type: "equirectangular", panorama: "{{ $panorama_url }}", autoLoad: true, showControls: false
    });
  </script>
@endsection