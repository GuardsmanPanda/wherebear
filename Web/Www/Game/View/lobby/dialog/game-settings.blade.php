<?php declare(strict_types=1); ?>
@php use Domain\User\Enum\BearPermissionEnum;use GuardsmanPanda\Larabear\Infrastructure\Auth\Service\BearAuthService; @endphp
<x-bear::dialog.update hx-patch="/game/{{$game->id}}/lobby/settings">
  @if($game->templated_by_game_id !== null)
    <x-bear::form.number id="number_of_rounds" required min="1" max="40" class="hidden">{{$game->number_of_rounds}}</x-bear::form.number>
  @else
    <x-bear::form.number id="number_of_rounds" required min="1" max="40" class="">{{$game->number_of_rounds}}</x-bear::form.number>
  @endif
  <x-bear::form.number id="round_duration_seconds" required min="20">{{$game->round_duration_seconds}}</x-bear::form.number>
  <x-bear::form.number id="round_result_duration_seconds" required min="10">{{$game->round_result_duration_seconds}}</x-bear::form.number>
  <fieldset class="flex gap-3 px-3 border border-gray-400 pb-3 mt-2">
    <legend class="px-1.5">Public Status</legend>
    <label class="font-bold">Public<input type="radio" name="game_public_status_enum" value="PUBLIC" class="ml-1"
                                          @if($game->game_public_status_enum === 'PUBLIC') checked @endif>
    </label>
    @if(BearAuthService::hasPermission(permission: BearPermissionEnum::IS_BOB))
      <label class="font-bold">Google<input type="radio" name="game_public_status_enum" value="GOOGLE" class="ml-1"
                                            @if($game->game_public_status_enum === 'GOOGLE') checked @endif>
      </label>
    @endif
    <label class="font-bold">Private<input type="radio" name="game_public_status_enum" value="PRIVATE" class="ml-1"
                                           @if($game->game_public_status_enum === 'PRIVATE') checked @endif>
    </label>
  </fieldset>
</x-bear::dialog.update>