<template>
  <InstallButton/>
  <div class="min-h-screen bg-base-300 pb-20 md:pb-0">
  <Navbar/>

  
<HeaderSlider :hero-slides="theaters"/>

    <!-- Main Content -->
    <div class="container mx-auto px-3 md:px-4 py-6 md:py-8">
      <!-- Trending Section -->
      <section class="mb-8 md:mb-12">
        <div class="flex items-center justify-between mb-4 md:mb-6">
          <h2 class="text-xl md:text-3xl font-bold flex items-center gap-2">
            <span class="mdi mdi-fire text-orange-500"></span>
            Mungkin anda suka
          </h2>
          
        </div>
        
        
        <div class="grid grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-2 md:gap-4">
          <template v-if="isLoading">
            <MovieCardSkeleton v-for="i in 10"/>
          </template>
          <template v-else>
            <MovieCard v-for="(item,index) in theaters" :key="'theater-'+index" :item="item"/>
          </template>
        </div>
      </section>

      <!-- Popular This Week -->
      <section class="mb-8 md:mb-12">
        <div class="flex items-center justify-between mb-4 md:mb-6">
          <h2 class="text-xl md:text-3xl font-bold flex items-center gap-2">
            <span class="mdi mdi-star text-yellow-500"></span>
            Akan rilis
          </h2>
        </div>
        <div class="grid grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-2 md:gap-4">
          <template v-if="isLoading">
            <MovieCardSkeleton v-for="i in 5"/>
          </template>
          <template v-else>
          <MovieCard2 v-for="(item,index) in coming_soon" :key="'coming_soon_'+index"  :item="item"/>
          </template>
        </div>
      </section>




      <!-- New Releases -->
      <section class="mb-8 md:mb-12">
        <div class="flex items-center justify-between mb-4 md:mb-6">
          <h2 class="text-xl md:text-3xl font-bold flex items-center gap-2">
            <span class="mdi mdi-new-box text-green-500"></span>
            Rekomendasi Populer
          </h2>
        </div>
        <div class="grid grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-2 md:gap-4">
         <template v-if="isLoading2">
          <MovieCardSkeleton v-for="i in 9"/>
         </template>
         <template v-else>

          <MovieCard v-for="(item,index) in recommends" :key="'recommends-'+index" :item="item"/>
      </template>
        </div>
<!-- Menggunakan flex justify-center dengan padding -->
<div class="flex justify-center my-8">
  <button 
    class="btn btn-primary flex items-center gap-2"
    @click="loadMore(currentPage + 1)"
    :disabled="isLoading"
  >
    <span v-if="!isLoading">Tampilkan lainnya</span>

    <span v-else class="flex items-center gap-1">
      <span class="mdi mdi-loading animate-spin text-xl"></span>
      Memuat...
    </span>
  </button>
</div>
      </section>
    </div>

   <MobileNav/>

<Footer/>
  </div>

 
</template>

<script setup>
import { ref ,nextTick , computed, onMounted} from 'vue';
import Navbar from './components/Navbar.vue';
import HeaderSlider from './components/HeaderSlider.vue';
import InstallButton from './components/InstallButton.vue';
import Footer from './components/Footer.vue';
import { getTheater,getCategory,getRecommend } from '../utils/api';
import MobileNav from './components/MobileNav.vue';
import MovieCard2 from './components/MovieCard2.vue';
import MovieCard from './components/MovieCard.vue';
import MovieCardSkeleton from './components/MovieCardSkeleton.vue';

const activeMenu = ref('home');


const props = defineProps({prop: Object});
const currentPage = ref(1);
const theaters = ref( []);
const coming_soon = ref( []);
const recommends = ref( []);
const categories = ref([]);
const categorySelected = ref("-1");
const filteredBooks = computed(() => {
  // Jika "Semua", return semua
  let all = categories.value.classifyBookList?.records;
  if (categorySelected.value === "-1") {
    return all;
  }
  console.log(categorySelected.value)

  // Filter berdasarkan tagName
  return all.filter(book =>
    book.tagV3s?.some(tag => tag.tagEnName === categorySelected.value)
  );
});
const isLoading = ref(false);
const isLoading2 = ref(false);
const loadMore = async (page) => {
  if (isLoading.value) return;
  isLoading2.value = true;

  try {
    // 🔥 Ganti fetch → getRecommend(page)
    const res = await getRecommend(page);

    if (res.success) {
      if (Array.isArray(res.data)) {
        recommends.value.push(...res.data);
      }

      currentPage.value = page;

      // Tunggu DOM update lalu scroll
      await nextTick();
      window.scrollTo({
        top: document.body.scrollHeight,
        behavior: "smooth"
      });
    }

  } catch (err) {
    console.error("loadMore error:", err);
  } finally {
    isLoading2.value = false;
  }
};



onMounted(async ()=>{
  isLoading.value = true;
  isLoading2.value = true;
  let te = await getTheater();
  theaters.value = te.data.you_might_like;
  coming_soon.value= te.data.coming_soon;
  isLoading.value = false;

  let c = await getCategory();
  let r = await getRecommend();
  recommends.value = r.data;
  categories.value = c.data;
  isLoading2.value=false;
});

</script>

<style scoped>
.line-clamp-2 {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

.line-clamp-3 {
  display: -webkit-box;
  -webkit-line-clamp: 3;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

.btm-nav > button.active {
  border-top: 2px solid oklch(var(--p));
  background-color: oklch(var(--b2));
}
</style>