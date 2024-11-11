<?php

declare(strict_types=1); ?>

<div class="grid grid-cols-3 min-[456px]:grid-cols-4 min-[566px]:grid-cols-5 min-[672px]:grid-cols-6 gap-4 justify-between">
  <template x-for="player in players" >
    <div x-ref="player" class="flex flex-col items-center w-24 h-24 relative">
      <div 
        class="w-20 h-0 absolute bottom-8 left-1/2 transform -translate-x-1/2 bg-gradient-to-t from-pistachio-400 to-transparent"
        :class="{ 'h-16': player.is_ready }"
        style="transition: height 0.2s ease-in-out;"
      ></div>

      <img :src="player.map_marker_file_path" class="w-16 max-h-16 object-contain relative top-[2px]"/>
      <div 
        class="flex justify-center items-center w-24 h-5 transform z-10 px-1 rounded truncate border border-gray-700"
        :class="{ 'bg-pistachio-400': player.is_ready, 'bg-gray-100': !player.is_ready }"
        style="transition: height 0.2s ease-in-out;"
      >
        <span x-text="player.display_name" class="font-heading font-medium text-sm text-gray-800 truncate"></span>
      </div>
      <lit-level-emblem :level="player.level" size="xs" class="absolute bottom-[28px] left-[4px] z-20"></lit-level-emblem>
      <lit-flag :cca2="player.country_cca2" :filePath="player.flag_file_path" :description="player.flag_description" roundedClass="rounded-sm" class="absolute bottom-[28px] right-[4px] h-5 z-20" draggable="false"></lit-flag>
    </div>
  </template>
</div>

<script>
  /** Intended for development use only. */
  {{-- function playerListState() {
    return {
      getPlayers() {
        const players = [];
        for(i=0; i<10;i++) {
          players.push(this.player);
        }
        return players;
      },
      player: {
        id: 'id',
        display_name: 'GreenMonkeyBoy',
        is_ready: true,
        country_cca2: 'FR',
        flag_file_path: '/static/flag/svg/FR.svg',
        flag_description: 'desc',
        level: 8,
        map_marker_file_path: 'https://gmb.gman.bot/static/img/map-marker/chibi/greek-warrior.png'
      }
    } 
  } --}}
</script>