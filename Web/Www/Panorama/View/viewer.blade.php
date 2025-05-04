<?php declare(strict_types=1); ?>
@extends('layout.blank-layout')

@section('content')
  <div id="panorama">
    <div class="absolute z-10 top-1 left-1">
      <button class="text-black button" onclick="saveViewport()">Save Viewport</button>
      @if($sv_url !== null)
        <button class="text-black button" onclick="window.open('{{$sv_url}}', 'sv', 'popup')">Open StreetView</button>
      @endif
    </div>
    <div class="absolute left-5 bottom-4 z-10" hx-patch="/web-api/panorama/{{$panorama_id}}"
         hx-vals='{"retired":"{{$retired ? 'false' : 'true'}}", "retired_reason": "{{$retired ? '' : 'Manual Retirement'}}"}'>
      @if($retired)
        <button type="button" class="text-white bg-red-600 hover:bg-red-800 font-bold rounded-md text-lg px-3 py-1.5 border-2 border-red-100">Retired
          !!
        </button>
      @else
        <button type="button"
                class="py-1 px-2 text-sm font-bold text-gray-900 bg-gray-50 rounded-md border border-red-200 hover:bg-red-100 hover:text-red-600">
          Retire
        </button>
      @endif
    </div>
    <div class="absolute right-5 bottom-4 z-10 grid gap-2">
      @if(in_array(needle: 'LANDMARK', haystack: $tags))
        <button type="button"
                class="py-1 px-2 text-sm font-bold text-blue-900 bg-blue-500 rounded-md border border-blue-800 hover:bg-blue-600 hover:text-blue-900"
                hx-patch="/web-api/panorama/{{$panorama_id}}"
                hx-vals='{"tag_remove": ["LANDMARK"]}'>Remove Landmark
        </button>
      @else
        <button type="button"
                class="py-1 px-2 text-sm font-bold text-gray-900 bg-gray-50 rounded-md border border-blue-200 hover:bg-blue-100 hover:text-blue-600"
                hx-patch="/web-api/panorama/{{$panorama_id}}"
                hx-vals='{"tag_add": ["LANDMARK"]}'>Add Landmark
        </button>
      @endif
      @if(in_array(needle: 'DAILY', haystack: $tags))
        <button type="button"
                class="py-1 px-2 text-sm font-bold text-yellow-900 bg-yellow-500 rounded-md border border-yellow-800 hover:bg-yellow-600 hover:text-yellow-900"
                hx-patch="/web-api/panorama/{{$panorama_id}}"
                hx-vals='{"tag_remove": ["DAILY"]}'>Remove DAILY
        </button>
      @else
        <button type="button"
                class="py-1 px-2 text-sm font-bold text-gray-900 bg-gray-50 rounded-md border border-red-200 hover:bg-red-100 hover:text-red-600"
                hx-patch="/web-api/panorama/{{$panorama_id}}"
                hx-vals='{"tag_add": ["DAILY"]}'>Add DAILY
        </button>
      @endif
    </div>
  </div>
  <script>
    const viewer = pannellum.viewer('panorama', {
      panorama: "{{ $panorama_url }}",
      type: "equirectangular",
      showControls: false,
      autoLoad: true,
      yaw: {{ $heading }},
      pitch: {{ $pitch }},
      hfov: 120,
      minHfov: window.innerWidth < 1000 ? 30 : 50,
    });

    function saveViewport() {
      const pitch = viewer.getPitch();
      const heading = viewer.getYaw();
      const field_of_view = viewer.getHfov();
      console.log({pitch, heading, field_of_view});
      fetch('/web-api/panorama/{{$panorama_id}}', {
        method: 'PATCH',
        headers: {
          'Content-Type': 'application/json',
        },
        body: JSON.stringify({pitch, heading, field_of_view}),
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
