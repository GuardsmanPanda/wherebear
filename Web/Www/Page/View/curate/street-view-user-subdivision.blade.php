<?php declare(strict_types=1); ?>
@php use Domain\User\Enum\BearPermissionEnum;use GuardsmanPanda\Larabear\Infrastructure\Auth\Service\BearAuthService; @endphp
<x-bear::form.text id="map-url" required="" class="text-gray-700" autocomplete="off"></x-bear::form.text>
<div class="flex">
  <div class="mt-1">
    <h3 class="font-bold text-amber-600">Viewport</h3>
    <div class="flex gap-4 ml-2 items-center">
      <div class="flex items-center">
        <label class="mr-2 font-medium text-amber-400" for="street_view_viewport">StreetView Viewport</label>
        <input id="street_view_viewport" type="checkbox" name="tag" value="true" checked>
      </div>
    </div>
  </div>
  <div id="tags" class="mt-1 ml-12">
    <h3 class="font-bold text-teal-500">Tags</h3>
    <div class="flex gap-4 ml-2 items-center">
      @if(BearAuthService::hasPermission(permission: BearPermissionEnum::PANORAMA_TAG_DAILY))
        <div class="flex items-center">
          <label class="mr-2 font-medium text-gray-400" for="DAILY">DAILY</label>
          <input id="DAILY" type="checkbox" name="tag" value="DAILY">
        </div>
      @endif
      <div class="flex items-center">
        <label class="mr-2 font-medium text-gray-400" for="ANIMAL">ANIMAL</label>
        <input id="ANIMAL" type="checkbox" name="tag" value="ANIMAL">
      </div>
    </div>
  </div>
</div>

<hr class="mt-2 mb-4  border-gray-600 border-2">

<table>
  <thead class="text-xs text-gray-400 uppercase bg-gray-700">
  <tr>
    <th scope="col" class="px-4 py-3">Country Name</th>
    <th scope="col" class="px-4 py-3">Subdivision Name</th>
    <th scope="col" class="px-4 py-3">Panoramas</th>
    <th scope="col" class="px-4 py-3">Action</th>
  </tr>
  </thead>
  @foreach($subdivisions as $subdivision)
    <tr class="border-b bg-gray-800 border-gray-700">
      <td class="px-4 py-3">{{$subdivision->country_name}}  ({{$subdivision->country_panoramas_count}})</td>
      <td class="px-4 py-3">{{$subdivision->subdivision_name}} ({{$subdivision->country_subdivision_panoramas_count}})</td>
      <td class="px-4 py-3">{{$subdivision->panorama_count}}</td>
      <td class="px-4 py-2">
        <button type="button"
                class="text-blue-500 hover:text-white border border-blue-500 hover:bg-blue-500 font-medium rounded-md text-sm ml-1 px-2.5 py-0.5 hover:scale-110 transition-all duration-75 text-center"
                hx-get="/page/curate/street-view-user/{{$userId}}/table?cca2={{$subdivision->cca2}}&iso-3166={{$subdivision->iso_3166}}" hx-push-url="true">Curate Panoramas
        </button>
      </td>
    </tr>
  @endforeach
</table>