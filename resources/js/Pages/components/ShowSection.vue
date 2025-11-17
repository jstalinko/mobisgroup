<script setup>
import { ref } from 'vue';
import ShowCard from './ShowCard.vue';

// Terima data awal melalui props
const props = defineProps({
  title: String,
  initialShows: Array,
});

// Buat state lokal untuk daftar shows, diisi dengan data awal
const shows = ref([...props.initialShows]);
const isLoading = ref(false);
const scrollContainer = ref(null); // Ref untuk div yang di-scroll

// Fungsi untuk memuat data baru (simulasi API)
function loadMoreShows() {
  if (isLoading.value) return; // Jangan muat jika sedang memuat
  
  console.log(`Memuat lebih banyak untuk: ${props.title}`);
  isLoading.value = true;

  // Simulasi Panggilan API (delay 2 detik)
  setTimeout(() => {
    // Buat 5 data palsu baru
    const newShows = [];
    const currentMaxId = Math.max(...shows.value.map(s => s.id));
    for (let i = 1; i <= 5; i++) {
      newShows.push({
        id: currentMaxId + i,
        title: `Item Baru ${currentMaxId + i}`,
        imageUrl: `https://placeimg.com/200/300/any?${currentMaxId + i}`,
        badge: Math.random() > 0.5 ? 'Baru' : null,
      });
    }

    // Tambahkan data baru ke state
    shows.value.push(...newShows);
    isLoading.value = false;
  }, 1500);
}

// Event handler untuk mendeteksi scroll horizontal
function handleScroll(event) {
  const el = event.target;
  // Cek jika scroll sudah mendekati akhir (misal, 300px sebelum akhir)
  const isNearEnd = el.scrollLeft + el.clientWidth >= el.scrollWidth - 300;

  if (isNearEnd && !isLoading.value) {
    loadMoreShows();
  }
}
</script>

<template>
  <section>
    <h2 class="text-2xl font-bold mb-4">{{ title }}</h2>
    
    <div 
      ref="scrollContainer"
      @scroll="handleScroll"
      class="flex overflow-x-auto space-x-4 pb-4
             scrollbar-thin scrollbar-thumb-primary/50 scrollbar-track-base-200"
    >
      <ShowCard
        v-for="show in shows"
        :key="show.id"
        :show="show"
      />

      <div 
        v-if="isLoading" 
        class="flex-shrink-0 w-36 h-[216px] md:w-48 md:h-[288px] 
               flex flex-col items-center justify-center 
               bg-base-200 rounded-lg"
      >
        <span class="loading loading-spinner loading-md text-primary"></span>
        <span class="mt-2 text-sm font-semibold">Memuat...</span>
      </div>
    </div>
  </section>
</template>