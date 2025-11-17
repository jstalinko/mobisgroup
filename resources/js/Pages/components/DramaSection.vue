<script setup>
import { ref } from 'vue'

// Props untuk menerima judul section (misal: "Drama Populer") dan datanya
const props = defineProps({
  title: String,
  items: Array, // Diharapkan array berisi objek { image, title, description, episodeCount, viewCount }
})

// State untuk melacak jumlah item yang ditampilkan
const show = ref(6) // Menampilkan 6 item pertama

// Fungsi untuk menambah 12 item lagi saat tombol diklik
const loadMore = () => {
  show.value += 12
}
</script>

<template>
  <section class="mt-10">
    <h2 class="text-lg font-bold mb-4">{{ title }}</h2>
    
    <div class="grid grid-cols-3 md:grid-cols-4 lg:grid-cols-4 gap-4">
      
      <div 
        v-for="(item, i) in items.slice(0, show)" 
        :key="i" 
        class="card bg-base-100 shadow-xl group transition-all duration-300 hover:shadow-2xl"
      >
        <figure class="overflow-hidden relative">
          
        
          <img 
            :src="item.image" 
            :alt="item.title" 
            class="w-full h-full object-cover transition-transform duration-500 ease-in-out group-hover:scale-110" 
          />
          <div class="absolute bottom-0 left-0 right-0 z-10 
                      flex justify-between items-center 
                      bg-gradient-to-b from-black/60 to-transparent 
                      p-2 text-white text-xs font-medium">
            
            <span class="flex items-center gap-1">
              <i class="mdi mdi-play-box-outline text-lg"></i>
              <span>{{ item.episodeCount }} Eps</span>
            </span>
            
            <span class="flex items-center gap-1">
              <i class="mdi mdi-eye text-lg"></i>
              <span>{{ item.viewCount }}</span>
            </span>
          </div>
        </figure>
        
        <div class="card-body p-4 ">
          <h3 class="card-title text-sm sm:text-base truncate">
            {{ item.title }}
          </h3>
          
          <p class="text-xs opacity-70 mt-1 hidden sm:block">
            {{ item.description }}
          </p>
        </div>
      </div>
    </div>

    <div v-if="show < items.length" class="text-center mt-4">
      <button @click="loadMore" class="btn btn-outline">Tampilkan lainnya</button>
    </div>
  </section>
</template>