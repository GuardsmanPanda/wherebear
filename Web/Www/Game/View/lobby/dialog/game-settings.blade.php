<?php declare(strict_types=1); ?>
@php use GuardsmanPanda\Larabear\Infrastructure\Auth\Service\BearAuthService; @endphp
<x-bear::dialog.update hx-patch="/game/{{$game->id}}/lobby/settings" hx-target="#lobby">
    <x-bear::form.number id="number_of_rounds" required>{{$game->number_of_rounds}}</x-bear::form.number>
    <x-bear::form.number id="round_duration_seconds" required>{{$game->round_duration_seconds}}</x-bear::form.number>
    <fieldset class="flex gap-3 px-3 border border-gray-400 pb-3 mt-2">
        <legend class="px-1.5">Public Status</legend>
        <label class="font-bold">Public<input type="radio" name="game_public_status" value="PUBLIC" class="ml-1"
                                              @if($game->game_public_status_enum === 'PUBLIC') checked @endif>
        </label>
        @if(BearAuthService::hasPermission(permission: 'is-bob'))
            <label class="font-bold">Google<input type="radio" name="game_public_status" value="GOOGLE" class="ml-1"
                                                   @if($game->game_public_status_enum === 'GOOGLE') checked @endif>
            </label>
        @endif
        <label class="font-bold">Private<input type="radio" name="game_public_status" value="PRIVATE" class="ml-1"
                                               @if($game->game_public_status_enum === 'PRIVATE') checked @endif>
        </label>
    </fieldset>
</x-bear::dialog.update>
