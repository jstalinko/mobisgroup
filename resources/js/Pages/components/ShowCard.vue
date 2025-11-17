<script setup>
defineProps({
  show: {
    type: Object,
    required: true,
  },
});
</script>

<template>
  <div class="card card-compact w-36 md:w-48 bg-base-100 shadow-xl
              relative shrink-0 
              transition-transform duration-300 hover:scale-105 cursor-pointer
              group">
    
    <figure>
      <img
        :src="show.imageUrl"
        :alt="show.title"
        class="w-full aspect-[2/3] object-cover"
      />
    </figure>

    <div 
      v-if="show.badge" 
      class="badge badge-error badge-sm absolute top-2 left-2 font-semibold z-10"
    >
      {{ show.badge }}
    </div>

    <div class="absolute bottom-0 left-0 w-full p-2 
                bg-gradient-to-t from-black/70 to-transparent
                opacity-0 group-hover:opacity-100 transition-opacity">
      <h3 class="text-white text-sm font-bold truncate">{{ show.title }}</h3>
    </div>
  </div>
</template>