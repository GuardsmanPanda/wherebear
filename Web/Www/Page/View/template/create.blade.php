<?php declare(strict_types=1); ?>
@php use Domain\User\Enum\BearPermissionEnum;use GuardsmanPanda\Larabear\Infrastructure\Auth\Service\BearAuthService; @endphp
<x-bear::dialog.create hx-post="/page/template">
  <x-bear::form.text id="name" required></x-bear::form.text>
  <x-bear::form.number id="number_of_rounds" required>7</x-bear::form.number>
  <fieldset class="flex gap-3 px-3 border border-gray-400 pb-3 mt-2">
    <legend class="px-1.5">Public Status</legend>
    <label class="font-bold">Public<input type="radio" name="game_public_status_enum" value="PUBLIC" class="ml-1" checked></label>
    @if(BearAuthService::hasPermission(permission: BearPermissionEnum::IS_BOB))
      <label class="font-bold">Google<input type="radio" name="game_public_status_enum" value="GOOGLE" class="ml-1"></label>
    @endif
    <label class="font-bold">Private<input type="radio" name="game_public_status_enum" value="PRIVATE" class="ml-1"></label>
  </fieldset>
  <fieldset class="flex gap-3 px-3 border border-gray-400 pb-3 mt-2">
    <legend class="px-1.5">Tag</legend>
    <label class="font-bold">None<input type="radio" name="panorama_tag_enum" value="" class="ml-1" checked></label>
    @if(BearAuthService::hasPermission(permission: BearPermissionEnum::IS_BOB))
      <label class="font-bold">Google<input type="radio" name="panorama_tag_enum" value="GOOGLE" class="ml-1"></label>
    @endif
  </fieldset>
</x-bear::dialog.create>
