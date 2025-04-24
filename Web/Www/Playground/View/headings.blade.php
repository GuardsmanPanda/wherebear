<div x-data="state" class="flex flex-col">
  <div class="flex flex-col gap-6 p-4">
    <template x-for="size in sizes" :key="size">
      <div class="flex flex-wrap gap-8 w-full">
        <span x-text="size"></span>
        <div 
          class="flex gap-4 text-gray-0"
          :class="`heading-${size}`"
        >
          <span>Wherebear is fun</span>
          <span class="uppercase">Wherebear is fun</span>
        </div>
      </div>
    </template>
  </div>
</div>

<script>
  document.addEventListener('alpine:init', () => {
    Alpine.data('state', () => ({
      sizes: ['xs', 'sm', 'md', 'lg', 'xl', '2xl', '3xl', '4xl']
    }))
  })
</script>
