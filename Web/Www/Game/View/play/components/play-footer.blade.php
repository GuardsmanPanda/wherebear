<?php

declare(strict_types=1); ?>

@props(['secondsRemaining', 'rounds', 'currentRound', 'selectedRound', 'totalRoundCount', 'page' ])

<div class="flex flex-col">
  <div class="relative">
    <img src="/static/img/pengu-sign.png" class="absolute left-0 bottom-[10px] h-20 z-20" alt="Cutest pengu around">
    <div class="flex justify-start items-center w-12 h-8 absolute bottom-[60px] left-[17px]">
      <span x-data="countdownState({{ $secondsRemaining - 2 }})"
        :class="{
            'ml-[19px]': timeRemainingSec.toString().length === 1,
            'ml-[14px]': timeRemainingSec.toString().length === 2,
            'ml-[9px]': timeRemainingSec.toString().length === 3
        }" 
        x-text="timeRemainingSec" 
        class="font-heading text-xl text-gray-900 select-none z-20">
      </span>
    </div>

    <lit-progress-bar 
      x-data="progressBarState({{ $secondsRemaining - 2 }})"
      sideFlated sideUnbordered 
      :percentage="percentage"
      @if($page === 'play')
        :innerBgColor="innerBgColor"
      @else
        innerBgColorClass="bg-yellow-300"
      @endif
    ></lit-progress-bar>
  </div>

  <lit-round-list 
    rounds="{{ json_encode($rounds) }}"
    currentRound="{{ $currentRound }}"
    totalRoundCount="{{ $totalRoundCount }}"
    @if($page === 'play')
    selectedRound="{{ $currentRound }}"
    @endif
    class="bg-iris-400"
  ></lit-round-list>
</div>

<script>
  function countdownState(durationSec) {
    return {
      targetTime: new Date(new Date().getTime() + durationSec * 1000),
      timeRemainingSec: durationSec,
      timerInterval: null,
      init() {
        const tick = () => {
          const now = new Date();
          let timeDiff = this.targetTime - now;
          if (timeDiff < -4000) {
            window.location.reload();
          }
          this.timeRemainingSec = Math.max(0, Math.ceil(timeDiff / 1000));
        };
        this.timerInterval = setInterval(() => {
          tick();
        }, 100);
      },
      destroy() {
        clearInterval(this.timerInterval);
      },
    }
  }

  function progressBarState(durationSec) {
    return {
      percentage: 100,
      durationSec: durationSec,
      targetTime: new Date(new Date().getTime() + durationSec * 1000),
      timerInterval: null,
      innerBgColor() {
        return `hsl(${123 * this.percentage / 100}, 69%, 58%)`;
      },
      init() {
        const tick = () => {
          const now = new Date();
          let timeDiff = this.targetTime - now - 1000;
          this.percentage = Math.max(0, Math.min(100, (timeDiff / (this.durationSec * 1000)) * 100));
        };
        this.timerInterval = setInterval(() => {
          tick();
        }, 1000);
      },
      destroy() {
        clearInterval(this.timerInterval);
      },
    }
  }
</script>