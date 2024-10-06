<?php declare(strict_types=1); ?>
@php use Web\Www\Game\Util\GameUtil; @endphp

<div x-data="state({{ $game->round_result_seconds_remaining }})" @keyup.right="nextTexture()" @keyup.left="previousTexture()" class="flex flex-col h-screen">
  <div x-show="isRankingDialogOpen" class="dialog-overlay" x-cloak></div>
  <div class="relative flex-1 overflow-hidden">
    <div class="flex justify-between items-start bg-blue-400 texture border-b border-gray-800" :style="{ 'background-image': `url(https://www.transparenttextures.com/patterns/${texture}.png)` }">
      <div class="flex gap-2 w-full relative pr-[122px]">
        <img class="h-16 absolute top-1 left-1 z-10 drop-shadow" src="/static/flag/wavy/{{ strtolower($game->cca2) }}.png" alt="Flag of {{ $game->country_name }}" />
        <div class="flex flex-col gap-1 mt-0 py-1 pl-[92px]">
          <div class="text-3xl text-gray-100 font-medium text-stroke-2 leading-none">{{ $game->country_name }}</div>
          <div class="text-lg text-gray-200 font-medium text-stroke-2 leading-none">Centre-Val de Loire</div>
        </div>
      </div>

      <div class="absolute top-0 right-1 z-10">
        <img class="relative bottom-[8px]" src="/static/img/ui/ribbon-emblem.png" />

        <div class="flex flex-col items-center w-[114px] absolute top-[8px]">
          @php
            $gapClass = ($user_guess->rank === 1) ? 'gap-0' : (($user_guess->rank === 2) ? 'gap-1' : (($user_guess->rank === 3) ? 'gap-0.5' : 'gap-1'));
          @endphp

          <div class="flex items-end {{ $gapClass }}">
            <span class="text-5xl font-bold text-white text-stroke-2 z-10">{{ $user_guess->rank }}</span>
            <span class="relative bottom-[1px] text-xl font-medium text-white text-stroke-2 {{ $user_guess->rank === 1 ? 'relative right-1' : '' }}">{{ GameUtil::getOrdinalSuffix($user_guess->rank) }}</span>
          </div>

          <div class="relative left-1.5 mt-1">
            <div class="flex justify-center w-[72px] relative rounded border border-gray-800 bg-blue-500">
              <div class="w-6 aspect-auto absolute -top-[4px] left-0 transform -translate-x-1/2">
                <x-custom-icon icon="star" />
              </div>
              <span class="text-xs text-white">{{ $user_guess->points }}</span>
            </div>
      
            <div class="flex justify-center items-center {{ $user_guess->country_cca2 === 'NP' ? 'w-[74px] relative right-[2px]' : 'w-[72px] relative' }} mt-3 rounded border border-gray-800 bg-blue-500">
              <img class="h-5 absolute {{ $user_guess->country_cca2 === 'NP' ? '-left-[2px]' : 'left-0 transform -translate-x-1/2 border border-black rounded' }}" 
              src="/static/flag/svg/{{ $user_guess->country_cca2 }}.svg">
              @php
              $distanceAndUnit = GameUtil::getDistanceAndUnit(distanceMeters: $user_guess->distance_meters);
              $mlClass = 'ml-0';
              if ($distanceAndUnit['unit'] === 'km') {
                $distanceCharactersCount = strlen((string)$distanceAndUnit['value']);
                if ($distanceCharactersCount > 3) {
                  $mlClass = 'ml-3';
                } else if ($distanceCharactersCount > 2) {
                  $mlClass = 'ml-2';
                }                    
              } 
              @endphp
              <span class="text-xs text-white {{ $mlClass }}">{{ $distanceAndUnit['value'] }}{{ $distanceAndUnit['unit'] }}</span>
            </div>
         </div>
        </div>
      </div>

      <dialog id="rankingDialog" class="dialog absolute top-1/2 left-1/2 transform -translate-x-full -translate-y-1/2">
        <lit-dialog iconPath="/static/img/icon/podium.svg" label="Ranking" x-on:closed="closeRankingDialog()">
          <div class="flex flex-col gap-1 p-1">
          @foreach ($guesses as $guess)
          <div class="p-0">
            <lit-player-result 
              distanceMeters="{{ $guess->distance_meters }}"
              iconPath="{{ $guess->map_marker_file_path }}"
              name="{{ $guess->user_display_name }}"
              rank="{{ $guess->rank }}"
              points="{{ $guess->points }}"
              title="Enthusiast Traveler">
            </lit-player-result>    
          </div>
          @endforeach
          </div>
        </lit-dialog>
      </dialog>
    </div>

    <div class="flex justify-end md:justify-center gap-2 absolute bottom-0 w-full p-1">
      <lit-button-square imgPath="/static/img/icon/podium.svg" label="Ranking" size="xl" class="z-10" x-on:clicked="onSwitchRankingButtonClicked($event);" ></lit-button-square>
    </div>
    <div id="map" class="flex w-full h-full"></div>
  </div>
  <div class="flex flex-col">
    <div class="relative">
      <img src="/static/img/pengu-sign.png" class="absolute -left-1 bottom-[10px] h-20 z-20" alt="Cutest pengu around">
      <div class="flex justify-center items-center w-12 h-8 absolute bottom-[62px] left-[13px]">
        <span x-text="timeRemainingSec" class="font-heading text-xl font-medium text-gray-900 select-none z-30"></span>
      </div>
      <lit-progress-bar sideFlated sideUnbordered :innerBgColor="innerBgColor" :percentage="percentage" :transitionDurationMs="transitionDurationMs"></lit-progress-bar>
    </div>
    <div class="bg-blue-500" :style="{ 'background-image': `url(https://www.transparenttextures.com/patterns/${texture}.png)` }">
    <lit-country-used-list countries="{{ json_encode($countries_used) }}" currentRoundNumber="{{ $game->current_round }}" totalRounds="{{ $game->number_of_rounds }}" selectedRound="{{ $game->current_round }}"></lit-country-used-list>
    </div>
  </div>
</div>

<script>
 

  function state(durationSec) {
    return {
      textureIndex: 0,
      texture: '',
      textures: [        
        "3px-tile",
        "45-degree-fabric-dark",
        "45-degree-fabric-light",
        "60-lines",
        "absurdity",
        "ag-square",
        "always-grey",
        "arabesque",
        "arches",
        "argyle",
        "asfalt-dark",
        "asfalt-light",
        "assault",
        "axiom-pattern",
        "az-subtle",
        "back-pattern",
        "basketball",
        "batthern",
        "bedge-grunge",
        "beige-paper",
        "billie-holiday",
        "binding-dark",
        "binding-light",
        "black-felt",
        "black-linen",
        "black-linen-2",
        "black-lozenge",
        "black-mamba",
        "black-orchid",
        "black-paper",
        "black-scales",
        "black-thread",
        "black-thread-light",
        "black-twill",
        "blizzard",
        "blu-stripes",
        "bo-play",
        "brick-wall",
        "brick-wall-dark",
        "bright-squares",
        "brilliant",
        "broken-noise",
        "brushed-alum",
        "brushed-alum-dark",
        "buried",
        "candyhole",
        "carbon-fibre",
        "carbon-fibre-big",
        "carbon-fibre-v2",
        "cardboard",
        "cardboard-flat",
        "cartographer",
        "checkered-light-emboss",
        "checkered-pattern",
        "church",
        "circles",
        "classy-fabric",
        "clean-gray-paper",
        "clean-textile",
        "climpek",
        "cloth-alike",
        "concrete-wall",
        "concrete-wall-2",
        "concrete-wall-3",
        "connected",
        "corrugation",
        "cream-dust",
        "cream-paper",
        "cream-pixels",
        "crisp-paper-ruffles",
        "crissxcross",
        "cross-scratches",
        "cross-stripes",
        "crossword",
        "cubes",
        "cutcube",
        "dark-brick-wall",
        "dark-circles",
        "dark-denim",
        "dark-denim-3",
        "dark-dot",
        "dark-dotted-2",
        "dark-exa",
        "dark-fish-skin",
        "dark-geometric",
        "dark-leather",
        "dark-matter",
        "dark-mosaic",
        "dark-stripes",
        "dark-stripes-light",
        "dark-tire",
        "dark-wall",
        "dark-wood",
        "darth-stripe",
        "debut-dark",
        "debut-light",
        "diagmonds",
        "diagmonds-light",
        "diagonal-noise",
        "diagonal-striped-brick",
        "diagonal-waves",
        "diagonales-decalees",
        "diamond-eyes",
        "diamond-upholstery",
        "diamonds-are-forever",
        "dimension",
        "dirty-old-black-shirt",
        "dotnoise-light-grey",
        "double-lined",
        "dust",
        "ecailles",
        "egg-shell",
        "elastoplast",
        "elegant-grid",
        "embossed-paper",
        "escheresque",
        "escheresque-dark",
        "exclusive-paper",
        "fabric-plaid",
        "fabric-1-dark",
        "fabric-1-light",
        "fabric-of-squares",
        "fake-brick",
        "fake-luxury",
        "fancy-deboss",
        "farmer",
        "felt",
        "first-aid-kit",
        "flower-trail",
        "flowers",
        "foggy-birds",
        "food",
        "football-no-lines",
        "french-stucco",
        "fresh-snow",
        "gold-scale",
        "gplay",
        "gradient-squares",
        "graphcoders-lil-fiber",
        "graphy-dark",
        "graphy",
        "gravel",
        "gray-floral",
        "gray-lines",
        "gray-sand",
        "green-cup",
        "green-dust-and-scratches",
        "green-fibers",
        "green-gobbler",
        "grey-jean",
        "grey-sandbag",
        "grey-washed-wall",
        "greyzz",
        "grid",
        "grid-me",
        "grid-noise",
        "grilled-noise",
        "groovepaper",
        "grunge-wall",
        "gun-metal",
        "handmade-paper",
        "hexabump",
        "hexellence",
        "hixs-evolution",
        "hoffman",
        "honey-im-subtle",
        "ice-age",
        "inflicted",
        "inspiration-geometry",
        "iron-grip",
        "kinda-jean",
        "knitted-netting",
        "knitted-sweater",
        "kuji",
        "large-leather",
        "leather",
        "light-aluminum",
        "light-gray",
        "light-grey-floral-motif",
        "light-honeycomb",
        "light-honeycomb-dark",
        "light-mesh",
        "light-paper-fibers",
        "light-sketch",
        "light-toast",
        "light-wool",
        "lined-paper",
        "lined-paper-2",
        "little-knobs",
        "little-pluses",
        "little-triangles",
        "low-contrast-linen",
        "lyonnette",
        "maze-black",
        "maze-white",
        "mbossed",
        "medic-packaging-foil",
        "merely-cubed",
        "micro-carbon",
        "mirrored-squares",
        "mocha-grunge",
        "mooning",
        "moulin",
        "my-little-plaid-dark",
        "my-little-plaid",
        "nami",
        "nasty-fabric",
        "natural-paper",
        "navy",
        "nice-snow",
        "nistri",
        "noise-lines",
        "noise-pattern-with-subtle-cross-lines",
        "noisy",
        "noisy-grid",
        "noisy-net",
        "norwegian-rose",
        "notebook",
        "notebook-dark",
        "office",
        "old-husks",
        "old-map",
        "old-mathematics",
        "old-moon",
        "old-wall",
        "otis-redding",
        "outlets",
        "p1",
        "p2",
        "p4",
        "p5",
        "p6",
        "padded",
        "padded-light",
        "paper",
        "paper-1",
        "paper-2",
        "paper-3",
        "paper-fibers",
        "paven",
        "perforated-white-leather",
        "pineapple-cut",
        "pinstripe-dark",
        "pinstripe-light",
        "pinstriped-suit",
        "pixel-weave",
        "polaroid",
        "polonez-pattern",
        "polyester-lite",
        "pool-table",
        "project-paper",
        "ps-neutral",
        "psychedelic",
        "purty-wood",
        "pw-pattern",
        "pyramid",
        "quilt",
        "random-grey-variations",
        "ravenna",
        "real-carbon-fibre",
        "rebel",
        "redox-01",
        "redox-02",
        "reticular-tissue",
        "retina-dust",
        "retina-wood",
        "retro-intro",
        "rice-paper",
        "rice-paper-2",
        "rice-paper-3",
        "robots",
        "rocky-wall",
        "rough-cloth",
        "rough-cloth-light",
        "rough-diagonal",
        "rubber-grip",
        "sandpaper",
        "satin-weave",
        "scribble-light",
        "shattered",
        "shattered-dark",
        "shine-caro",
        "shine-dotted",
        "shley-tree-1",
        "shley-tree-2",
        "silver-scales",
        "simple-dashed",
        "simple-horizontal-light",
        "skeletal-weave",
        "skewed-print",
        "skin-side-up",
        "skulls",
        "slash-it",
        "small-crackle-bright",
        "small-crosses",
        "smooth-wall-dark",
        "smooth-wall-light",
        "sneaker-mesh-fabric",
        "snow",
        "soft-circle-scales",
        "soft-kill",
        "soft-pad",
        "soft-wallpaper",
        "solid",
        "sos",
        "sprinkles",
        "squairy",
        "squared-metal",
        "squares",
        "stacked-circles",
        "stardust",
        "starring",
        "stitched-wool",
        "strange-bullseyes",
        "straws",
        "stressed-linen",
        "stucco",
        "subtle-carbon",
        "subtle-dark-vertical",
        "subtle-dots",
        "subtle-freckles",
        "subtle-grey",
        "subtle-grunge",
        "subtle-stripes",
        "subtle-surface",
        "subtle-white-feathers",
        "subtle-zebra-3d",
        "subtlenet",
        "swirl",
        "tactile-noise-dark",
        "tactile-noise-light",
        "tapestry",
        "tasky",
        "tex2res1",
        "tex2res2",
        "tex2res3",
        "tex2res4",
        "tex2res5",
        "textured-paper",
        "textured-stripes",
        "texturetastic-gray",
        "ticks",
        "tileable-wood",
        "tileable-wood-colored",
        "tiny-grid",
        "translucent-fibres",
        "transparent-square-tiles",
        "tree-bark",
        "triangles",
        "triangles-2",
        "triangular",
        "tweed",
        "twinkle-twinkle",
        "txture",
        "type",
        "use-your-illusion",
        "vaio",
        "vertical-cloth",
        "vichy",
        "vintage-speckles",
        "wall-4-light",
        "washi",
        "wave-grid",
        "wavecut",
        "weave",
        "wet-snow",
        "white-bed-sheet",
        "white-brick-wall",
        "white-brushed",
        "white-carbon",
        "white-carbonfiber",
        "white-diamond",
        "white-diamond-dark",
        "white-leather",
        "white-linen",
        "white-paperboard",
        "white-plaster",
        "white-sand",
        "white-texture",
        "white-tiles",
        "white-wall",
        "white-wall-2",
        "white-wall-3",
        "white-wall-3-2",
        "white-wave",
        "whitey",
        "wide-rectangles",
        "wild-flowers",
        "wild-oliva",
        "wine-cork",
        "wood",
        "wood-pattern",
        "worn-dots",
        "woven",
        "woven-light",
        "xv",
        "zig-zag",
      ],
      nextTexture() {
        if (this.textureIndex === this.textures.length - 1) {
          this.textureIndex = 0;
        }else {
          this.textureIndex++;
        }
        this.texture = this.textures[this.textureIndex];
        console.log(this.texture);
      },
      previousTexture() {
        if (this.textureIndex === 0) {
          this.textureIndex = this.textures.length - 1;
        }else {
          this.textureIndex--; 
        }
        this.texture = this.textures[this.textureIndex];
        console.log(this.texture);
      },
      isSelected: false,
      rankingDialog: document.getElementById('rankingDialog'),
      isRankingDialogOpen: false,
      openRankingDialog() {
        this.rankingDialog.showModal();
        this.isRankingDialogOpen = true;
      },
      closeRankingDialog() {
        this.rankingDialog.close();
        this.isRankingDialogOpen = false;
      },
      switchRankingDialog() {
        (this.isRankingDialogOpen) ? this.closeRankingDialog() : this.openRankingDialog();
      },
      switchIsSelected() {
        this.isSelected = !this.isSelected;
      },
      onSwitchRankingButtonClicked(e) {
        this.isSelected = !this.isSelected;
        this.switchRankingDialog();
      },
      intervalDurationMs: 1000,
      percentage: 100,
      timeRemainingSec: durationSec,
      timerInterval: null,
      transitionDurationMs: 0,
      guessButtonState: "abc",
      get innerBgColor() {
        return `hsl(${123 * this.percentage / 100}, 69%, 58%)`;
      },
      start() {
        const totalStepCount = (durationSec * 1000) / this.intervalDurationMs;
        const percentageStep = (100 / (totalStepCount));
        let firstCycle = true

        const tick = () => {
          if (this.percentage <= 0) {
            clearInterval(this.timerInterval);
            this.percentage = 0;
            this.timeRemainingSec = 0;
          } else {
            this.percentage = Math.max(this.percentage - percentageStep, 0);

            if (!firstCycle) {
              this.timeRemainingSec--;
            }
            firstCycle = false;
          }
        };

        tick();
        this.timerInterval = setInterval(() => {
          tick();
        }, this.intervalDurationMs);
      },
      reset() {
        this.percentage = 100;
        this.timeRemainingSec = durationSec;
      },
      init() {
        
      },
      destroy() {
        clearInterval(this.timerInterval);
      },
      onGuessButtonClicked() {
        console.log('guess button clicked');
        this.guessButtonState = "new state";
      }
    }
  }

  const map = new window.maplibregl.Map({
    container: 'map', 
    style: {
      'version': 8, 
      'sources': {
      'raster-tiles': {
        'type': 'raster', 
          'tiles': ['{{ $user->map_style_full_uri }}'], 
          'tileSize': {{ $user->map_style_tile_size }}
        }
      }, 
      'layers': [{
        'id': 'simple-tiles',
        'type': 'raster',
        'source': 'raster-tiles'
      }]
    }, 
    center: [{{ $game->panorama_lng }}, {{ $game->panorama_lat}}], 
    dragRotate: false, 
    keyboard: false, 
    minZoom: 1, 
    maxZoom: 18, 
    zoom: 3,
    attributionControl: false
  })


  map.scrollZoom.setWheelZoomRate(1 / 75);
  map.scrollZoom.setZoomRate(1 / 75);
  map.touchZoomRotate.disableRotation();


  const playerGuesses = @json($guesses);
  playerGuesses.forEach(guess => {
    const mapPlayerMarkerElement = document.createElement('div');
    mapPlayerMarkerElement.innerHTML = `
      <lit-map-marker distanceMeters="${guess.distance_meters}" mapMarkerFilePath="${guess.map_marker_file_path}" playerName="${guess.user_display_name}" rank="${guess.rank}"></lit-map-marker>
    `;

    new window.maplibregl
      .Marker({element: mapPlayerMarkerElement, anchor: 'bottom'})
      .setLngLat([guess.lng, guess.lat])
      .addTo(map);
  });

    const mapPanoramaMarkerElement = document.createElement('div');
    mapPanoramaMarkerElement.innerHTML = `
      <img src="/static/img/map-extra/marker-win3.png" class="w-16" />
    `;

    new window.maplibregl
      .Marker({element: mapPanoramaMarkerElement, anchor: 'center'})
      .setLngLat([{{ $game->panorama_lng }}, {{ $game->panorama_lat }}])
      .addTo(map);
</script>