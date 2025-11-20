<template>
  <div
    class="card bg-base-100 shadow-xl hover:shadow-2xl transition-all cursor-pointer group"
    @click="goToDetail"
  >
    <figure class="relative overflow-hidden rounded">
      <img
        :src="item.image"
        :alt="item.title"
        class="group-hover:scale-110 transition-transform duration-300"
      />

      <!-- Hover Overlay -->
      <div
        class="absolute inset-0 bg-black/70 opacity-0 group-hover:opacity-100
               transition-opacity duration-300 flex flex-col items-center justify-center gap-2 p-3"
      >
        <button class="btn btn-circle btn-primary btn-sm md:btn-md">
          <span class="mdi mdi-play text-xl md:text-2xl"></span>
        </button>
        <div class="text-white text-xs md:text-sm font-semibold">Play Now</div>
      </div>

      <!-- Episode Count -->
      <div
        class="absolute bottom-2 left-2 bg-black/80 px-2 py-1 rounded text-xs md:text-sm
               text-white font-semibold opacity-0 group-hover:opacity-100
               transition-opacity duration-300 flex items-center gap-1"
      >
        <span class="mdi mdi-television-play" v-if="item.episode !== 0"></span>
       <span v-if="item.episode !== 0"> {{ item.episode }} Eps </span> <span v-else>{{ item.title }}</span>
      </div>
    </figure>
  </div>
</template>

<script setup>
import { router } from '@inertiajs/vue3';
import { slugify } from '../../utils/helpers';
const props = defineProps({
  item: {
    type: Object,
    required: true,
  },
});
const goToDetail = () => {
  router.visit(`/detail/${props.item.id}/${slugify(props.item.title)}`);
};
</script>
