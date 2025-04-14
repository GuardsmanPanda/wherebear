<div x-data="state" class="flex flex-col gap-8 w-min h-full p-4">
  <!-- Default -->
  <div class="flex flex-col gap-4">
    <span class="font-medium self-center">Default</span>
    <template x-for="size in sizes" :key="size">
      <div class="flex gap-4">
        <span class="w-8" x-text="size.toUpperCase()"></span>
        <div class="flex gap-4">
          <template x-for="color in colors" :key="`${size}-${color}`">
            <lit-button
              label="BUTTON"
              :size="size"
              :color="color"
              class="w-48"
            ></lit-button>
          </template>
        </div>
      </div>
    </template>
  </div>

  <!-- Default -->
  <div class="flex flex-col gap-4">
    <span class="font-medium self-center">Selectable</span>
    <template x-for="size in sizes" :key="size">
      <div class="flex gap-4">
        <span class="w-8" x-text="size.toUpperCase()"></span>
        <div class="flex gap-4">
          <template x-for="color in colors" :key="`${size}-${color}`">
            <lit-button
              label="BUTTON"
              :size="size"
              :color="color"
              isSelectable
              class="w-48"
            ></lit-button>
          </template>
        </div>
      </div>
    </template>
  </div>

  <!-- Content Alignment Left -->
  <div class="flex flex-col gap-4">
    <span class="font-medium self-center">Content Alignment Left</span>
    <template x-for="size in sizes" :key="size">
      <div class="flex gap-4">
        <span class="w-8" x-text="size.toUpperCase()"></span>
        <div class="flex gap-4">
          <template x-for="color in colors" :key="`${size}-${color}`">
            <lit-button
              label="BUTTON"
              :size="size"
              :color="color"
              contentAlignment="left"
              class="w-48"
            ></lit-button>
          </template>
        </div>
      </div>
    </template>
  </div>

  <!-- With Icon -->
  <div class="flex flex-col gap-4">
    <span class="font-medium self-center">Left Icon</span>
    <template x-for="size in sizes" :key="size + '-icon'">
      <div class="flex gap-4">
        <span class="w-8" x-text="size.toUpperCase()"></span>
        <div class="flex gap-4">
          <template x-for="color in colors" :key="`${size}-${color}-icon`">
            <lit-button
              label="BUTTON"
              :size="size"
              :color="color"
              icon="cross"
              class="w-48"
            ></lit-button>
          </template>
        </div>
      </div>
    </template>
  </div>

  <!-- Lef Icon And Left Alignment -->
  <div class="flex flex-col gap-4">
    <span class="font-medium self-center">Left Icon + Content Alignment Left</span>
    <template x-for="size in sizes" :key="size + '-icon'">
      <div class="flex gap-4">
        <span class="w-8" x-text="size.toUpperCase()"></span>
        <div class="flex gap-4">
          <template x-for="color in colors" :key="`${size}-${color}-icon`">
            <lit-button
              label="BUTTON"
              :size="size"
              :color="color"
              icon="cross"
              contentAlignment="left"
              class="w-48"
            ></lit-button>
          </template>
        </div>
      </div>
    </template>
  </div>

<!-- Icon Only -->
  <div class="flex flex-col gap-4">
    <span class="font-medium self-center">Icon Only</span>
    <template x-for="size in sizes" :key="size + '-icon'">
      <div class="flex gap-4">
        <span class="w-8" x-text="size.toUpperCase()"></span>
        <div class="flex gap-4">
          <template x-for="color in colors" :key="`${size}-${color}-icon`">
            <lit-button
              :size="size"
              :color="color"
              icon="cross"
              class="w-48"
            ></lit-button>
          </template>
        </div>
      </div>
    </template>
  </div>

<!-- Custom Background Color -->
  <div class="flex flex-col gap-4">
    <span class="font-medium self-center">Custom Background Color</span>
    <template x-for="size in sizes" :key="size + '-icon'">
      <div class="flex gap-4">
        <span class="w-8" x-text="size.toUpperCase()"></span>
        <div class="flex gap-4">
          <template x-for="color in colors" :key="`${size}-${color}-icon`">
            <lit-button
              label="BUTTON"
              :size="size"
              bgColorClass="bg-sky-500"
              class="w-48"
            ></lit-button>
          </template>
        </div>
      </div>
    </template>
  </div>

  <!-- Pill -->
  <div class="flex flex-col gap-4">
    <span class="font-medium self-center">Pill</span>
    <template x-for="size in sizes" :key="size + '-icon'">
      <div class="flex gap-4">
        <span class="w-8" x-text="size.toUpperCase()"></span>
        <div class="flex gap-4">
          <template x-for="color in colors" :key="`${size}-${color}-icon`">
            <lit-button
              label="BUTTON"
              :size="size"
              :color="color"
              pill
              class="w-48"
            ></lit-button>
          </template>
        </div>
      </div>
    </template>
  </div>

  <!-- Ping -->
  <div class="flex flex-col gap-4">
    <span class="font-medium self-center">Ping</span>
    <template x-for="size in sizes" :key="size + '-icon'">
      <div class="flex gap-4">
        <span class="w-8" x-text="size.toUpperCase()"></span>
        <div class="flex gap-4">
          <template x-for="color in colors" :key="`${size}-${color}-icon`">
            <lit-button
              label="BUTTON"
              :size="size"
              :color="color"
              ping
              class="w-48"
            ></lit-button>
          </template>
        </div>
      </div>
    </template>
  </div>

  <!-- Disabled -->
  <div class="flex flex-col gap-4">
    <span class="font-medium self-center">Disabled</span>
    <template x-for="size in sizes" :key="size + '-icon'">
      <div class="flex gap-4">
        <span class="w-8" x-text="size.toUpperCase()"></span>
        <div class="flex gap-4">
          <template x-for="color in colors" :key="`${size}-${color}-icon`">
            <lit-button
              label="BUTTON"
              :size="size"
              :color="color"
              disabled
              class="w-48"
            ></lit-button>
          </template>
        </div>
      </div>
    </template>
  </div>
</div>

<script>
  document.addEventListener('alpine:init', () => {
    Alpine.data('state', () => ({
      sizes: ['xs', 'sm', 'md', 'lg', 'xl'],
      colors: ['blue', 'green', 'red', 'yellow', 'gray'],
    }))
  })
</script>
