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
      intervalDurationMs: 1000,
      timeRemainingSec: durationSec,
      timerInterval: null,
      start() {
        const tick = () => {
          if (this.timeRemainingSec <= 0) {
            clearInterval(this.timerInterval);
            this.timeRemainingSec = 0;
          } else {
            this.timeRemainingSec--;
          }
        };

        tick();
        this.timerInterval = setInterval(() => {
          tick();
        }, this.intervalDurationMs);
      },
      init() {
        setTimeout(() => {
          this.start();
        }, 1100);
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
      timerInterval: null,
      intervalDurationMs: 1000,
      innerBgColor() {
        return `hsl(${123 * this.percentage / 100}, 69%, 58%)`;
      },
      start() {
        const totalStepCount = (durationSec * 1000) / this.intervalDurationMs;
        const percentageStep = (100 / (totalStepCount));

        const tick = () => {
          if (this.percentage <= 0) {
            clearInterval(this.timerInterval);
            this.percentage = 0;
          } else {
            this.percentage = Math.max(this.percentage - percentageStep, 0);
          }
        };

        tick();
        this.timerInterval = setInterval(() => {
          tick();
        }, this.intervalDurationMs);
      },
      init() {
        setTimeout(() => {
          this.start();
        }, 100);
      },
      destroy() {
        clearInterval(this.timerInterval);
      },
    }
  }
</script>