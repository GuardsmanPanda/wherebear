<?php declare(strict_types=1); ?>
<thead class="text-xs text-gray-400 uppercase bg-gray-700">
<tr>
  <th scope="col" class="px-4 py-3">No</th>
  <th scope="col" class="px-4 py-3">Panorama ID</th>
  <th scope="col" class="px-4 py-3">Country</th>
  <th scope="col" class="px-4 py-3">Subdivision</th>
  <th scope="col" class="px-4 py-3">Tags</th>
  <th scope="col" class="px-4 py-3">Panorama Date</th>
  <th scope="col" class="px-4 py-3">Added Date</th>
  <th scope="col" class="px-4 py-3">Action</th>
</tr>
</thead>
@foreach($panoramas as $panorama)
  <tr class="border-b bg-gray-800 border-gray-700">
    <td class="px-4 py-3 font-medium text-right w-8">{{$panorama->round_number}}</td>
    <td class="px-4 py-3">{{substr($panorama->id, 0, 10)}}</td>
    <td class="px-4 py-3">{{$panorama->country_name}}</td>
    <td class="px-4 py-3">{{$panorama->country_subdivision_name}}</td>
    <td class="px-4 py-3">
      {{$panorama->panorama_tag_array}}
      @if($panorama->panorama_retired_at !== null)
        <span class="text-red-500">*Retired*</span>
      @endif
      @if($panorama->import_street_view_user_panorama_id !== null)
        <span class="text-yellow-500">*User Imported*</span>
      @endif
    </td>
    <td class="px-4 py-3">{{$panorama->captured_date}}</td>
    <td class="px-4 py-3">{{$panorama->panorama_created_at}}</td>
    <td class="px-4 py-2">
      <button type="button"
              class="text-blue-500 hover:text-white border border-blue-500 hover:bg-blue-500 font-medium rounded-md text-sm ml-1 px-2.5 py-0.5 hover:scale-110 transition-all duration-75 text-center"
              onclick="window.open('/panorama/{{$panorama->id}}/view', 'pano', 'popup')">View Panorama
      </button>
    </td>
  </tr>
@endforeach