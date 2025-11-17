<script setup>
import { ref } from 'vue'
import DramaSection from './DramaSection.vue' 

const props = defineProps({
  movie: Object,
})

const showEpisodes = ref(12) 

const loadMoreEpisodes = () => {
  // Kita buat load more lebih banyak di modal
  showEpisodes.value += 24 
}
</script>

<template>
  <div class="max-w-7xl mx-auto p-4 md:p-6">
    
    <section class="flex flex-col md:flex-row gap-6">
      <div class="flex-shrink-0 w-48 md:w-56 lg:w-64 mx-auto md:mx-0">
        <img :src="movie.posterImage" :alt="movie.title" class="w-full h-auto object-cover rounded-lg shadow-lg">
      </div>
      <div class="flex-grow">
        <h1 class="text-3xl font-bold">{{ movie.title }}</h1>
        <p class="text-sm opacity-80 mt-1">{{ movie.episodeCount }} Episode</p>
        <p class="mt-4 text-sm leading-relaxed">
          {{ movie.synopsis }}
        </p>
        <div class="flex flex-wrap gap-2 mt-4">
          <span v-for="tag in movie.tags" :key="tag" class="badge badge-outline">
            {{ tag }}
          </span>
        </div>
        <button class="btn btn-primary btn-lg mt-6">
          <i class="mdi mdi-play text-xl"></i>
          Tonton Sekarang
        </button>
      </div>
    </section>

    <div class="divider"></div>

    <section class="mt-8 lg:hidden">
      <div class="flex justify-between items-center mb-4">
        <h2 class="text-xl font-bold">Daftar Episode</h2>
        <span class="text-sm opacity-70">({{ movie.episodeCount }} Episode)</span>
      </div>
      <a 
        href="#episode_drawer" 
        class="flex justify-between items-center bg-base-200 p-4 rounded-lg hover:bg-base-300 transition-colors"
      >
        <div class="flex flex-col">
          <span>Pilih Episode</span>
          <span class="text-sm opacity-70">Episode 1 - {{ movie.episodeCount }}</span>
        </div>
        <i class="mdi mdi-chevron-right text-xl"></i>
      </a>
    </section>

    <section class="mt-8 hidden lg:block">
      <div class="flex justify-between items-center mb-4">
        <h2 class="text-xl font-bold">Daftar Episode</h2>
        <span class="text-sm opacity-70">({{ movie.episodeCount }} Episode)</span>
      </div>
      <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-3">
        <a 
          v-for="(episode, i) in movie.episodes.slice(0, showEpisodes)" 
          :key="i"
          href="#" 
          class="bg-base-200 p-4 rounded-lg text-sm hover:bg-base-300 transition-colors"
        >
          <div class="flex flex-col truncate">
            <span class="truncate opacity-80">{{ episode.title }}</span>
            <span class="font-semibold truncate">Episode {{ episode.number }}</span>
          </div>
        </a>
      </div>
      <div v-if="showEpisodes < movie.episodes.length" class="text-center mt-6">
        <button @click="loadMoreEpisodes" class="btn btn-ghost text-primary">
          Lihat lebih banyak
          <i class="mdi mdi-chevron-down"></i>
        </button>
      </div>
    </section>
    
    <div class="divider"></div>

    <DramaSection 
      title="Rekomendasi"
      :items="movie.recommendations" 
    />

  </div>

  <dialog id="episode_drawer" class="modal modal-bottom">
    <div class="modal-box max-h-[80vh] flex flex-col">
      
      <div class="flex justify-between items-center pb-4">
        <h3 class="font-bold text-lg">{{ movie.title }}</h3>
        <form method="dialog">
          <button class="btn btn-sm btn-circle btn-ghost">✕</button>
        </form>
      </div>

      <div class="flex-grow overflow-y-auto">
        <div class="grid grid-cols-5 gap-2">
          <a 
            v-for="(episode, i) in movie.episodes.slice(0, showEpisodes)" 
            :key="i"
            href="#" 
            class="btn btn-outline h-auto py-3"
          >
            <span class="text-lg font-bold">{{ episode.number }}</span>
          </a>
        </div>
        
        <div v...if="showEpisodes < movie.episodes.length" class="text-center mt-4">
          <button @click="loadMoreEpisodes" class="btn btn-ghost text-primary w-full">
            Lihat lebih banyak
          </button>
        </div>
      </div>

    </div>
    
    <form method="dialog" class="modal-backdrop">
      <button>close</button>
    </form>
  </dialog>
  </template>