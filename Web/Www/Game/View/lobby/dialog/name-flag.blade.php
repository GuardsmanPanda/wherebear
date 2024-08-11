<?php declare(strict_types=1); ?>
@php use GuardsmanPanda\Larabear\Infrastructure\Auth\Service\BearAuthService; @endphp
<x-bear::dialog.basic>
  <form class="flex gap-4 items-end" hx-patch="/game/{{$game_id}}/lobby/update-user" autocomplete="off"
        hx-target="#lobby">
    <x-bear::form.text id="display_name"
                       maxlength="32">{{BearAuthService::getUser()->display_name}}</x-bear::form.text>
    <x-bear::button.dark type="submit">Update</x-bear::button.dark>
  </form>

  <h2 class="mt-4 font-bold text-xl">Flag Choice</h2>
  <div class="ml-1">
    <form class="" hx-target="#lobby">
      <x-bear::form.select id="country_cca2" label="Country"
                           hx-patch="/game/{{$game_id}}/lobby/update-user">
        <option value="" disabled selected></option>
        @foreach($countries as $country)
          <option value="{{$country->cca2}}"
                  @if($country->cca2 === BearAuthService::getUser()->country_cca2) selected @endif>{{$country->name}}</option>
        @endforeach
      </x-bear::form.select>
    </form>

    <h3 class="mt-2 font-medium">Other Flags</h3>
    <div class="flex" hx-target="#lobby">
      @foreach($novelty_flags as $flag)
        <button class="px-1 hover:scale-110 transition-transform duration-75" hx-dialog-close
                hx-patch="/game/{{$game_id}}/lobby/update-user"
                hx-vals='{"user_country_iso2_code": "{{$flag->country_iso2_code}}"}'
                tippy="{{$flag->country_name}}">
          <img class="h-8" src="/static/flag/svg/{{$flag->country_iso2_code}}.svg?"
               alt="{{$flag->country_name}}">
        </button>
      @endforeach
    </div>
  </div>

</x-bear::dialog.basic>