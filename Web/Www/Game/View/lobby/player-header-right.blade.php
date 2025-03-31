<div class="flex items-center text-lg font-bold">
  <div class="absolute top-0 right-[80px] z-10 w-8 h-full -skew-x-12 border-l border-gray-700 transition-colors duration-300 ease-in-out" :class="playerPanelHeaderReadyBgColor"></div>
  <div 
    class="absolute top-0 right-0 z-20 h-full transition-colors duration-300 ease-in-out" 
    :class="[playerPanelHeaderReadyBgColor, playerCount < 10 ? 'w-[84px]' : 'w-[108px]']">
  </div>
  <div class="flex justify-between items-center z-30" :class="playerCount < 10 ? 'w-[92px]' : 'w-[110px]'">
    <span>Ready:</span>
    <div class="flex gap-0.5">
      <span x-text="readyPlayerCount"></span>/<span x-text="playerCount"></span>
    </div>
  </div>
</div>