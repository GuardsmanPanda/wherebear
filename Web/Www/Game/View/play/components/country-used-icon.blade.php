<?php

declare(strict_types=1); ?>

@props(['cca2', 'name', 'backgroundColor' => 'bg-gray-200', 'borderColor' => 'border-gray-900', 'isPlaceholder' => false])

@php
$placeholderTippys = [
"Mystery box!",
"Guess who?",
"Shhh...",
"Top Secret!"
];
@endphp

<div
  class="flex flex-col w-[40px] h-[28px] rounded {{ $backgroundColor }} {{ $borderColor }} border relative cursor-default"
  @if(!$isPlaceholder)
  style="background-image: url('/static/flag/svg/{{ $cca2 }}.svg'); background-size: cover; background-position: center; box-shadow: inset 0 -4px 1px rgb(0 0 0 / 0.2);"
  @else
  style="box-shadow: inset 0 -4px 1px rgb(0 0 0 / 0.2);"
  tippy="{{ $placeholderTippys[array_rand($placeholderTippys)] }}"
  @endif>
  @if($isPlaceholder)
  <div class="flex justify-center items-center h-[24px] ">
    <span class="text-xl font-medium text-gray-600">?</span>
  </div>
  @endif
  {{ $slot }}
</div>