<?php declare(strict_types=1); ?>
<div class="pb-4 mb-4 px-4 pt-2 shadow border-b border-gray-700">
  <div class="flex items-center">
    <div class="flex-auto">
      <h1 class="text-3xl font-semibold">Game Templates</h1>
    </div>
    <x-bear::button.dark icon="plus" hx-get="/page/template/create">New Template</x-bear::button.dark>
  </div>
</div>
<div class="relative overflow-x-auto shadow-md sm:rounded-md">
  <table class="w-full text-sm text-left text-gray-400">
    <thead class="text-xs text-gray-400 uppercase bg-gray-700">
    <tr>
      <th scope="col" class="px-4 py-3">Template Name</th>
      <th scope="col" class="px-4 py-3">Rounds</th>
      <th scope="col" class="px-4 py-3">Creator Name</th>
      <th scope="col" class="px-4 py-3">Games</th>
      <th scope="col" class="px-4 py-3">Status</th>
      <th scope="col" class="px-4 py-3">Tag</th>
      <th scope="col" class="px-4 py-3">Ready</th>
      <th scope="col" class="px-4 py-3">Created At</th>
      <th scope="col" class="px-4 py-3">Action</th>
    </tr>
    </thead>
    <tbody>
    @foreach($templates as $template)
      <tr class="border-b bg-gray-800 border-gray-700">
        <td class="px-4 py-3 font-medium whitespace-nowrap">{{$template->name}}</td>
        <td class="px-4 py-3 w-8 text-right">{{$template->number_of_rounds}}</td>
        <td class="px-4 py-3">{{$template->created_by_user_display_name}}</td>
        <td class="px-4 py-3">{{$template->number_of_games_templated}}</td>
        <td class="px-4 py-3">{{$template->game_public_status_enum}}</td>
        <td class="px-4 py-3">{{$template->panorama_tag_enum}}</td>
        <td class="px-4 py-3">{{$template->all_rounds_have_panorama ? "✅" : "-"}}</td>
        <td class="px-4 py-3">{{$template->created_at}}</td>
        <td class="px-4 py-2">
          <button type="button"
                  class="text-blue-500 hover:text-white border border-blue-500 hover:bg-blue-500 font-medium rounded-lg text-sm ml-1 px-2.5 py-0.5 hover:scale-110 transition-all duration-75 text-center"
                  hx-get="/page/template/{{$template->id}}/panorama" hx-push-url="true">Panoramas
          </button>
          <button type="button"
                  class="text-red-500 hover:text-white border border-red-500 hover:bg-red-600 font-medium rounded-lg text-sm ml-1 px-2.5 py-0.5 hover:scale-110 transition-all duration-75 text-center"
                  hx-delete="/page/template/{{$template->id}}">Delete
          </button>
        </td>
      </tr>
    @endforeach
    </tbody>
  </table>
</div>
