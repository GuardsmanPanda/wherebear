<lit-panel label="RANKING">
  <div slot="header-right" class="font-heading font-semibold text-lg text-gray-50">{{ count($players) }} players</div>
  <div class="flex flex-col gap-2 p-2">
    @foreach ($players as $player)
      <lit-player-result-item
        countryCCA2="{{ $player->country_cca2 }}"
        detailedPoints="{{ $player->detailed_points }}"
        flagDescription="{{ $player->flag_description }}"
        flagFilePath="{{ $player->flag_file_path }}"
        iconPath="{{ $player->map_marker_file_path }}"
        level="{{ $player->level }}"
        name="{{ $player->display_name }}"
        rank="{{ $player->rank }}"
        rankIconType="cup"
        rankSelected="{{ $player->rank === $user->rank ? $player->rank : '' }}"
        roundedPoints="{{ $player->rounded_points }}"
        userTitle="{{ $player->title }}">
      </lit-player-result-item>
    @endforeach
  </div>
</lit-panel>