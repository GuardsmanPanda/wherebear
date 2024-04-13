<?php declare(strict_types=1); ?>
<x-bear::dialog.create hx-post="/game">
    <x-bear::form.number id="number_of_rounds" required>7</x-bear::form.number>
    <fieldset class="flex gap-3 px-3 border border-gray-400 pb-3 mt-2">
        <legend class="px-1.5">Round Duration - Seconds</legend>
        <label class="font-bold">35<input type="radio" name="round_duration" value="35" class="ml-1"></label>
        <label class="font-bold">45<input type="radio" name="round_duration" value="45" class="ml-1" checked></label>
        <label class="font-bold">65<input type="radio" name="round_duration" value="65" class="ml-1"></label>
        <label class="font-bold">85<input type="radio" name="round_duration" value="85" class="ml-1"></label>
    </fieldset>
</x-bear::dialog.create>
