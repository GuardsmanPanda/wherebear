<?php declare(strict_types=1); ?>
<div class="pb-4 mb-4 px-4 pt-2 shadow border-b border-gray-700">
  <div class="flex items-center">
    <div class="flex-auto">
      <h1 class="text-3xl font-semibold">StreetView Users</h1>
    </div>
    <form class="flex gap-4 items-end" hx-post="/page/curate/street-view-user">
      <x-bear::form.text id="id" required="" class="text-gray-700" autocomplete="off" placeholder="[0-9]+"></x-bear::form.text>
      <x-bear::form.text id="name" required="" class="text-gray-700" autocomplete="off"></x-bear::form.text>
      <x-bear::button.dark icon="plus" type="submit">Add User</x-bear::button.dark>
    </form>
  </div>
</div>
<table>
  <thead class="text-xs text-gray-400 uppercase bg-gray-700">
  <tr>
    <th scope="col" class="px-4 py-3">Id</th>
    <th scope="col" class="px-4 py-3">Name</th>
    <th scope="col" class="px-4 py-3">Latest Captured</th>
    <th scope="col" class="px-4 py-3">Total</th>
    <th scope="col" class="px-4 py-3">Ready</th>
    <th scope="col" class="px-4 py-3">Imported</th>
    <th scope="col" class="px-4 py-3">Rejected</th>
    <th scope="col" class="px-4 py-3">Last Sync At</th>
    <th scope="col" class="px-4 py-3">Action</th>
  </tr>
  </thead>
  @foreach($users as $user)
    <tr class="border-b bg-gray-800 border-gray-700">
      <td class="px-4 py-3 font-medium text-right w-8">{{$user->id}}</td>
      <td class="px-4 py-3">{{$user->name}}</td>
      <td class="px-4 py-3">{{$user->latest_captured_date}}</td>
      <td class="px-4 py-3 text-right">{{$user->panoramas_count}}</td>
      <td class="px-4 py-3 text-right">{{$user->location_added_count}}</td>
      <td class="px-4 py-3 text-right">{{$user->imported_count}}</td>
      <td class="px-4 py-3 text-right">{{$user->rejected_count}}</td>
      <td class="px-4 py-3">{{$user->last_sync_at}}</td>
      <td class="px-4 py-2">
        @if($user->location_added_count > 0)
          <button type="button"
                  class="text-blue-500 hover:text-white border border-blue-500 hover:bg-blue-500 font-medium rounded-md text-sm ml-1 px-2.5 py-0.5 hover:scale-110 transition-all duration-75 text-center"
                  hx-get="/page/curate/street-view-user/{{$user->id}}" hx-push-url="true">Curate Panoramas
          </button>
        @endif
      </td>
    </tr>
  @endforeach
</table>
