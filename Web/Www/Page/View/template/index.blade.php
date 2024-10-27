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
  <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
    <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
    <tr>
      <th scope="col" class="px-4 py-3">Template Name</th>
      <th scope="col" class="px-4 py-3">Creator Name</th>
      <th scope="col" class="px-4 py-3">Status</th>
      <th scope="col" class="px-4 py-3">Rounds</th>
      <th scope="col" class="px-4 py-3">Tag</th>
      <th scope="col" class="px-4 py-3">Created At</th>
      <th scope="col" class="px-4 py-3">Action</th>
    </tr>
    </thead>
    <tbody>
    @foreach($templates as $template)
      <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
        <th class="px-4 py-3 font-medium whitespace-nowrap" scope="row">
          {{$template->name}}
        </th>
        <td class="px-4 py-3">{{$template->created_by_user_display_name}}</td>
        <td class="px-4 py-3">{{$template->game_public_status_enum}}</td>
        <td class="px-4 py-3 text-right">{{$template->number_of_rounds}}</td>
        <td class="px-4 py-3">{{$template->panorama_tag_enum}}</td>
        <td class="px-4 py-3">{{$template->created_at}}</td>
        <td class="px-4 py-2">
          <button type="button"
                  class="text-blue-700 hover:text-white border border-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm ml-1 px-2.5 py-0.5 hover:scale-110 transition-all duration-75 text-center dark:border-blue-500 dark:text-blue-500 dark:hover:text-white dark:hover:bg-blue-500 dark:focus:ring-blue-800"
                  hx-get="/page/template/{{$template->id}}/panorama" hx-push-url="true">Panoramas
          </button>
          <button type="button"
                  class="text-red-700 hover:text-white border border-red-700 hover:bg-red-800 focus:ring-4 focus:outline-none focus:ring-red-300 font-medium rounded-lg text-sm ml-1 px-2.5 py-0.5 hover:scale-110 transition-all duration-75 text-center dark:border-red-500 dark:text-red-500 dark:hover:text-white dark:hover:bg-red-600 dark:focus:ring-red-900"
                  hx-delete="/page/template/{{$template->id}}">Delete
          </button>
        </td>
      </tr>
    @endforeach
    </tbody>
  </table>
</div>
