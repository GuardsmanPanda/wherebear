<div x-data="state" class="flex flex-wrap gap-8 h-full p-4">
  <!-- Default -->
  <div class="flex flex-col gap-4">
    <span class="font-medium self-center">Default</span>
    <div class="flex flex-col gap-2">
      <template x-for="name in names" :key="name">
        <div class="flex gap-2">
          <div x-text="name" class="w-24"></div>
          <template x-for="size in sizes" :key="`${size}-${name}`">
            <div class="flex" :class="{ 'w-6': size === 'xs', 'w-8': size === 'sm', 'w-12': size === 'md', 'w-14': size === 'lg', 'w-16': size === 'xl', 'w-18': size === '2xl'}">
              <lit-icon :name="name" class="flex" :class="{ 'h-4': size === 'xs', 'h-6': size === 'sm', 'h-8': size === 'md', 'h-10': size === 'lg', 'h-12': size === 'xl', 'h-14': size === '2xl' }"></lit-icon>
            </div>
          </template>
        </div>
      </template>
    </div>
  </div>

  <!-- With Solid Shadow SM -->
  <div class="flex flex-col gap-4">
    <span class="font-medium self-center">With Solid Shadow SM</span>
    <div class="flex flex-col gap-2">
      <template x-for="name in names" :key="name">
        <div class="flex gap-2">
          <div x-text="name" class="w-24"></div>
          <template x-for="size in sizes" :key="`${size}-${name}`">
            <div class="flex" :class="{ 'w-6': size === 'xs', 'w-8': size === 'sm', 'w-12': size === 'md', 'w-14': size === 'lg', 'w-16': size === 'xl', 'w-18': size === '2xl'}">
              <lit-icon :name="name" class="flex solid-shadow-sm" :class="{ 'h-4': size === 'xs', 'h-6': size === 'sm', 'h-8': size === 'md', 'h-10': size === 'lg', 'h-12': size === 'xl', 'h-14': size === '2xl' }"></lit-icon>
            </div>
          </template>
        </div>
      </template>
    </div>
  </div>
</div>

<script>
  document.addEventListener('alpine:init', () => {
    Alpine.data('state', () => ({
      sizes: ['xs', 'sm', 'md', 'lg', 'xl', '2xl'],
      names: ['arrow_right', 'arrow_back', 'check_green', 'chronometer', 'copy', 'cross', 'edit', 'eye', 'fullscreen', 'gear', 'info', 'map', 'person'],
    }))
  })
</script>
