<script setup>
defineProps({
  shows: Array,
});
</script>

<template>
  <div class="carousel w-full rounded-box shadow-2xl">
    
    <div
      v-for="(show, index) in shows"
      :key="show.id"
      :id="'slide' + index"
      class="carousel-item relative w-full"
    >
      <img :src="show.imageUrl" class="w-full object-cover aspect-video" />
      
      <div class="absolute bottom-0 left-0 w-full h-1/2 p-6 md:p-10
                  flex flex-col justify-end
                  bg-gradient-to-t from-black/80 to-transparent">
        <h2 class="card-title text-white text-2xl md:text-4xl font-bold">
          {{ show.title }}
        </h2>
      </div>

      <div class="absolute flex justify-between transform -translate-y-1/2 left-5 right-5 top-1/2">
        <a 
          :href="'#slide' + (index === 0 ? shows.length - 1 : index - 1)" 
          class="btn btn-circle btn-ghost text-white"
        >
          ❮
        </a> 
        <a 
          :href="'#slide' + (index === shows.length - 1 ? 0 : index + 1)" 
          class="btn btn-circle btn-ghost text-white"
        >
          ❯
        </a>
      </div>
    </div>
    
  </div>
</template>