<?php declare(strict_types=1); ?>
@php use Domain\User\Enum\BearPermissionEnum;use GuardsmanPanda\Larabear\Infrastructure\Auth\Service\BearAuthService; @endphp
<div class="pb-4 mb-4 px-4 pt-2 shadow border-b border-gray-700">
  <div class="flex items-center">
    <div class="flex-auto">
      <h1 class="text-3xl font-semibold">Game Templates > {{$template->name}}</h1>
    </div>
  </div>
</div>
<table class="w-full text-sm text-left text-gray-400">
  <thead class="text-xs text-gray-400 uppercase bg-gray-700">
  <tr>
    <th scope="col" class="px-4 py-3">Round Number</th>
    <th scope="col" class="px-4 py-3">Panorama ID</th>
    <th scope="col" class="px-4 py-3">Country</th>
    <th scope="col" class="px-4 py-3">Subdivision</th>
    <th scope="col" class="px-4 py-3">Tags</th>
    <th scope="col" class="px-4 py-3">Panorama Date</th>
    <th scope="col" class="px-4 py-3">Action</th>
  </tr>
  </thead>
  <tbody>
  @foreach($rounds as $round)
    <tr class="border-b bg-gray-800 border-gray-700">
      <td class="px-4 py-3 font-medium text-right w-8">{{$round->r_number}}</td>
      <td class="px-4 py-3">{{substr($round->panorama_id ?? '', 0, 10)}}</td>
      <td class="px-4 py-3">{{$round->country_name}}</td>
      <td class="px-4 py-3">{{$round->country_subdivision_name}}</td>
      <td class="px-4 py-3">{{$round->panorama_tag_array}}</td>
      <td class="px-4 py-3">{{$round->captured_date}}</td>
      <td class="px-4 py-2">
        @if($round->panorama_id !== null)
          <button type="button"
                  class="text-blue-500 hover:text-white border border-blue-500 hover:bg-blue-500 font-medium rounded-md text-sm ml-1 px-2.5 py-0.5 hover:scale-110 transition-all duration-75 text-center"
                  onclick="window.open('/panorama/{{$round->panorama_id}}/view', 'pano-round', 'popup')">View Panorama
          </button>
          @if(BearAuthService::hasPermission(BearPermissionEnum::TEMPLATE_ROUND_DELETE))
            <button type="button"
                    class="text-red-500 hover:text-white border border-red-500 hover:bg-red-600 font-medium rounded-md text-sm ml-1 px-2.5 py-0.5 hover:scale-110 transition-all duration-75 text-center"
                    hx-delete="/page/template/{{$template->id}}/panorama/{{$round->r_number}}" hx-confirm="Are you sure?">Remove
            </button>
          @endif
        @else
          <button type="button"
                  class="text-blue-500 hover:text-white border border-blue-500 hover:bg-blue-500 font-medium rounded-md text-sm ml-1 px-2.5 py-0.5 hover:scale-110 transition-all duration-75 text-center"
                  hx-get="/page/template/{{$template->id}}/panorama/{{$round->r_number}}" hx-target="#panorama-select">Select
          </button>
        @endif
      </td>
    </tr>
  @endforeach
  </tbody>
</table>
<div id="panorama-select" class="mt-4"></div>