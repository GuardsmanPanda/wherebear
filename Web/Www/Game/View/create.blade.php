<?php declare(strict_types=1); ?>
@php use GuardsmanPanda\Larabear\Infrastructure\Auth\Service\BearAuthService; @endphp
<x-bear::dialog.create hx-post="/game">
    <x-bear::form.number id="number_of_rounds" required>7</x-bear::form.number>
    <fieldset class="flex gap-3 px-3 border border-gray-400 pb-3 mt-2">
        <legend class="px-1.5">Round Duration - Seconds</legend>
        <label class="font-bold">35<input type="radio" name="round_duration_seconds" value="35" class="ml-1"></label>
        <label class="font-bold">45<input type="radio" name="round_duration_seconds" value="45" class="ml-1" checked></label>
        <label class="font-bold">55<input type="radio" name="round_duration_seconds" value="55" class="ml-1"></label>
        <label class="font-bold">65<input type="radio" name="round_duration_seconds" value="65" class="ml-1"></label>
        <label class="font-bold">85<input type="radio" name="round_duration_seconds" value="85" class="ml-1"></label>
    </fieldset>
    <fieldset class="flex gap-3 px-3 border border-gray-400 pb-3 mt-2">
        <legend class="px-1.5">Round Result Duration - Seconds</legend>
        <label class="font-bold">18<input type="radio" name="round_result_duration_seconds" value="18" class="ml-1"></label>
        <label class="font-bold">22<input type="radio" name="round_result_duration_seconds" value="22" class="ml-1" checked></label>
        <label class="font-bold">26<input type="radio" name="round_result_duration_seconds" value="26" class="ml-1"></label>
        <label class="font-bold">30<input type="radio" name="round_result_duration_seconds" value="30" class="ml-1"></label>
        <label class="font-bold">40<input type="radio" name="round_result_duration_seconds" value="30" class="ml-1"></label>
    </fieldset>
    <fieldset class="flex gap-3 px-3 border border-gray-400 pb-3 mt-2">
        <legend class="px-1.5">Public Status</legend>
        <label class="font-bold">Public<input type="radio" name="game_public_status" value="PUBLIC" class="ml-1" checked></label>
        @if(BearAuthService::hasPermission(permission: 'is-bob'))
            <label class="font-bold">Google<input type="radio" name="game_public_status" value="GOOGLE" class="ml-1"></label>
        @endif
        <label class="font-bold">Private<input type="radio" name="game_public_status" value="PRIVATE" class="ml-1"></label>
    </fieldset>
</x-bear::dialog.create>
