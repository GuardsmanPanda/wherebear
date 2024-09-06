<?php

declare(strict_types=1);

?>


@props(['size' => 'sm', 'heightPx' => null, 'bgColorCls', 'bgColorHoverCls', 'bgColorSelectedCls', 'roundedCls' => 'rounded-none'])

@php
$sizeToHeightPx = match($size) {
'sm' => 32,
'md' => 40,
'lg' => 48,
'xl' => 54,
default => 32,
};

$finalHeightPx = $heightPx ?? $sizeToHeightPx;
@endphp

<button
  x-data="{ isSelected: false, hasMouseLeftAfterClick: true }"
  x-on:click="isSelected = !isSelected; hasMouseLeftAfterClick = false; $dispatch('clicked', isSelected)"
  x-on:mouseleave="hasMouseLeftAfterClick = true"
  {{ $attributes->class(['flex items-end h-['.$heightPx.'px]']) }}>
  <div
    class="w-full h-[{{ $finalHeightPx }}px] active:h-[{{ $finalHeightPx - 4 }}px] {{ $roundedCls }} active:{{ $bgColorSelectedCls }}"
    :class="{
      '{{ $bgColorCls }}': !isSelected && !hasMouseLeftAfterClick,
      '{{ $bgColorCls }} hover:{{ $bgColorHoverCls }}': !isSelected && hasMouseLeftAfterClick,
      '{{ $bgColorSelectedCls }} active:{{ $bgColorCls }}': isSelected,
      'hover:{{ $bgColorHoverCls }}': isSelected && hasMouseLeftAfterClick
    }">
    {{ $slot }}
  </div>
</button>