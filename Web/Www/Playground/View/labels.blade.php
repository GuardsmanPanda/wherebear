<div x-data="state" class="flex flex-col gap-8 w-min h-full p-4">
  <!-- Default -->
  <div class="flex flex-col gap-4">
    <span class="font-medium self-center">Default</span>
    <template x-for="size in sizes" :key="size">
      <div class="flex gap-8">
        <span class="w-8" x-text="size.toUpperCase()"></span>
        <div class="flex w-full gap-10">
          <template x-for="color in colors" :key="`${size}-${color}`">
            <div class="w-full">
              <lit-label
                label="LABEL"
                :size="size"
                :color="color"
                class="w-24"
              ></lit-label>
            </div>
          </template>
        </div>
      </div>
    </template>
  </div>

  <!-- Pill -->
  <div class="flex flex-col gap-4">
    <span class="font-medium self-center">Pill</span>
    <template x-for="size in sizes" :key="size">
      <div class="flex gap-8">
        <span class="w-8" x-text="size.toUpperCase()"></span>
        <div class="flex w-full gap-10">
          <template x-for="color in colors" :key="`${size}-${color}`">
            <div class="w-full">
              <lit-label
                label="LABEL"
                :size="size"
                :color="color"
                pill
                class="w-24"
              ></lit-label>
            </div>
          </template>
        </div>
      </div>
    </template>
  </div>

  <!-- Icon -->
  <div class="flex flex-col gap-4">
    <span class="font-medium self-center">Icon</span>
    <template x-for="size in sizes" :key="size">
      <div class="flex gap-8">
        <span class="w-8" x-text="size.toUpperCase()"></span>
        <div class="flex w-full gap-10">
          <template x-for="color in colors" :key="`${size}-${color}`">
            <div class="w-full">
              <lit-label
                label="LABEL"
                :size="size"
                :color="color"
                icon="chronometer"
                class="w-24"
              ></lit-label>
            </div>
          </template>
        </div>
      </div>
    </template>
  </div>

  <!-- Icon + Pill -->
  <div class="flex flex-col gap-4">
    <span class="font-medium self-center">Icon + Pill</span>
    <template x-for="size in sizes" :key="size">
      <div class="flex gap-8">
        <span class="w-8" x-text="size.toUpperCase()"></span>
        <div class="flex w-full gap-10">
          <template x-for="color in colors" :key="`${size}-${color}`">
            <div class="w-full">
              <lit-label
                label="LABEL"
                :size="size"
                :color="color"
                icon="chronometer"
                pill
                class="w-24"
              ></lit-label>
            </div>
          </template>
        </div>
      </div>
    </template>
  </div>
</div>

<script>
  document.addEventListener('alpine:init', () => {
    Alpine.data('state', () => ({
      sizes: ['xs', 'sm', 'md'],
      colors: ['blue', 'green', 'yellow', 'orange', 'red', 'gray'],
    }))
  })
</script>
