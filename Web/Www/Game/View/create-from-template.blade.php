<?php declare(strict_types=1); ?>
@php use Domain\User\Enum\BearPermissionEnum;use GuardsmanPanda\Larabear\Infrastructure\Auth\Service\BearAuthService; @endphp
<x-bear::dialog.basic :includeButton="false">
  <div
    hx-include="[name='name'], [name='round_duration_seconds'], [name='round_result_duration_seconds'], [name='game_public_status_enum'], [name='is_observer']">
    <x-bear::form.text id="name" required>{{$display_name}}'s Game</x-bear::form.text>
    <div class="flex gap-4">
      <fieldset class="flex gap-3 px-3 border border-gray-400 pb-3 mt-2">
        <legend class="px-1.5">Panorama - Seconds</legend>
        <label class="font-bold">35<input type="radio" name="round_duration_seconds" value="35" class="ml-1"></label>
        <label class="font-bold">40<input type="radio" name="round_duration_seconds" value="40" class="ml-1" checked></label>
        <label class="font-bold">50<input type="radio" name="round_duration_seconds" value="50" class="ml-1"></label>
      </fieldset>
      <fieldset class="flex gap-3 px-3 border border-gray-400 pb-3 mt-2">
        <legend class="px-1.5">Result - Seconds</legend>
        <label class="font-bold">18<input type="radio" name="round_result_duration_seconds" value="18" class="ml-1"></label>
        <label class="font-bold">20<input type="radio" name="round_result_duration_seconds" value="20" class="ml-1" checked></label>
        <label class="font-bold">24<input type="radio" name="round_result_duration_seconds" value="24" class="ml-1"></label>
      </fieldset>
    </div>
    <div class="flex gap-4">
      <fieldset class="flex gap-3 px-3 border border-gray-400 pb-3 mt-2">
        <legend class="px-1.5">Public Status</legend>
        <label class="font-bold">Public<input type="radio" name="game_public_status_enum" value="PUBLIC" class="ml-1" checked></label>
        @if(BearAuthService::hasPermission(permission: BearPermissionEnum::IS_BOB))
          <label class="font-bold">Google<input type="radio" name="game_public_status_enum" value="GOOGLE" class="ml-1"></label>
        @endif
        <label class="font-bold">Private<input type="radio" name="game_public_status_enum" value="PRIVATE" class="ml-1"></label>
      </fieldset>
      <fieldset class="flex gap-3 px-3 border border-gray-400 pb-3 mt-2">
        <legend class="px-1.5">Join As</legend>
        <label class="font-bold">Player<input type="radio" name="is_observer" value="false" class="ml-1" checked></label>
        <label class="font-bold">Observer<input type="radio" name="is_observer" value="true" class="ml-1"></label>
      </fieldset>
    </div>
    <div>
      @foreach($templates as $template)
        <button class="block w-full bg-blue-200 rounded shadow py-1 mt-2 hover:scale-105 duration-75"
                hx-post="/game/create-from-template/{{$template->id}}">
          <div class="font-bold opacity-70">{{$template->name}}</div>
          <div class="text-sm -mt-1 opacity-50">
            <span>Creator: {{$template->display_name}},</span>
            <span>Rounds: {{$template->number_of_rounds}}</span>
          </div>
        </button>
      @endforeach
    </div>
  </div>
</x-bear::dialog.basic>
