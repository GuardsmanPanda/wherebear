<?php declare(strict_types=1); ?>
@php use GuardsmanPanda\Larabear\Infrastructure\Auth\Service\BearAuthService; @endphp
<x-bear::dialog.basic>
    <form class="flex gap-4 items-end" hx-patch="/game/{{$game_id}}/lobby/update-user" autocomplete="off" hx-target="#primary">
        <x-bear::form.text id="user_display_name" maxlength="32">{{BearAuthService::getUser()->user_display_name}}</x-bear::form.text>
        <x-bear::button.dark type="submit">Update</x-bear::button.dark>
    </form>

    <form class="mt-6" hx-target="#primary">
        <x-bear::form.select id="user_country_iso2_code" label="Country" hx-patch="/game/{{$game_id}}/lobby/update-user">
            @foreach($countries as $country)
                <option value="{{$country->country_iso2_code}}" @if($country->country_iso2_code === BearAuthService::getUser()->user_country_iso2_code) selected @endif>{{$country->country_name}}</option>
            @endforeach
        </x-bear::form.select>
    </form>
</x-bear::dialog.basic>