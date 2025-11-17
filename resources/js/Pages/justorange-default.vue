<template>
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
          <div v-for="(yml,index) in theaters" :key="'trending-' + index" class="card bg-base-100 shadow-xl hover:shadow-2xl transition-all cursor-pointer group" @click="router.visit('/detail/'+yml.bookId+'/'+slugify(yml.bookName))">
            <figure class="relative overflow-hidden">
              <img :src="yml.coverWap" class="group-hover:scale-110 transition-transform duration-300" :alt="yml.bookName" />
              
              <!-- Top Badge -->
              <div class="absolute top-1 right-1 md:top-2 md:right-2 badge badge-error badge-xs md:badge-sm gap-1">
                <span class="mdi mdi-eye text-xs"></span>
                {{ yml.playCount }}
              </div>
              
              <!-- Hover Overlay -->
              <div class="absolute inset-0 bg-black/70 opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex flex-col items-center justify-center gap-2 p-3">
                <button class="btn btn-circle btn-primary btn-sm md:btn-md">
                  <span class="mdi mdi-play text-xl md:text-2xl"></span>
                </button>
                <div class="text-white text-xs md:text-sm font-semibold">Play Now</div>
              </div>

              <!-- Episode Count (Bottom Left) -->
              <div class="absolute bottom-2 left-2 bg-black/80 px-2 py-1 rounded text-xs md:text-sm text-white font-semibold opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex items-center gap-1">
                <span class="mdi mdi-television-play"></span>
                {{ yml.chapterCount }} Eps
              </div>
            </figure>
            <div class="card-body p-2 md:p-3">
              <h3 class="text-xs md:text-sm font-bold line-clamp-2">{{ yml.bookName }}</h3>
             
            </div>
          </div>
        </div>
      </section>

      <!-- Popular This Week -->
      <section class="mb-8 md:mb-12">
        <div class="flex items-center justify-between mb-4 md:mb-6">
          <h2 class="text-xl md:text-3xl font-bold flex items-center gap-2">
            <span class="mdi mdi-star text-yellow-500"></span>
            Akan rilis
          </h2>
          <!-- <a href="#" class="btn btn-ghost btn-xs md:btn-sm gap-1">
            View All
            <span class="mdi mdi-arrow-right"></span>
          </a> -->
        </div>
        <div class="grid grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-2 md:gap-4">
          <div v-for="(cs,index) in coming_soon" :key="'popular-' + index" class="card bg-base-100 shadow-xl hover:shadow-2xl transition-all cursor-pointer group">
            
            <figure class="relative overflow-hidden">
              <img :src="cs.coverWap" class="group-hover:scale-110 transition-transform duration-300" :alt="cs.bookName" />
              
             

              <!-- Hover Overlay -->
              <div class="absolute inset-0 bg-black/70 opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex flex-col items-center justify-center gap-2 p-3">
                <button class="btn btn-circle btn-primary btn-sm md:btn-md">
                  <span class="mdi mdi-play text-xl md:text-2xl"></span>
                </button>
                <div class="text-white text-xs md:text-sm font-semibold">Play Now</div>
              </div>

              <!-- Episode Count -->
              <div class="absolute bottom-2 left-2 bg-black/80 px-2 py-1 rounded text-xs md:text-sm text-white font-semibold opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex items-center gap-1">
                <span class="mdi mdi-television-play"></span>
                {{cs.chapterCount }} Eps
              </div>
            </figure>
           
          </div>
        </div>
      </section>

  <section class="mb-8 md:mb-12">
  <h2 class="text-xl md:text-3xl font-bold mb-4 md:mb-6 flex items-center gap-2">
    <span class="mdi mdi-shape text-primary"></span>
    Browse by Category
  </h2>

  <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">

    <!-- Loop dropdown -->

    <div v-for="(cat, idx) in categories.classifyList" :key="idx" class="form-control w-full">
    

      <select
        v-model="categorySelected"
        class="select select-bordered w-full"
      >
        <option disabled value="">Pilih kategori</option>

        <option
          v-for="opt in cat.showList"
          :key="opt.hitValue"
          :value="opt.hitValue"
        >
          {{ opt.display }}
        </option>
      </select>
    </div>

  </div>

    <div class="grid grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-2 md:gap-4 mt-5">
          <div v-for="(rec,index) in filteredBooks" :key="'xx-' + index" class="card bg-base-100 shadow-xl hover:shadow-2xl transition-all cursor-pointer group" @click="router.visit('/detail/'+rec.bookId+'/'+slugify(rec.bookName))"  >
            
            
            
            <figure class="relative overflow-hidden">
              <img :src="rec.coverWap" class="group-hover:scale-110 transition-transform duration-300" :alt="rec.bookName" />
              
              
              <!-- Top Badge -->
              <div class="absolute top-1 right-1 md:top-2 md:right-2 badge badge-success badge-xs md:badge-sm gap-1">
                <span class="mdi mdi-eye"></span>
                {{ rec.playCount }}
              </div>

              <!-- Hover Overlay -->
              <div class="absolute inset-0 bg-black/70 opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex flex-col items-center justify-center gap-2 p-3">
                <button class="btn btn-circle btn-primary btn-sm md:btn-md">
                  <span class="mdi mdi-play text-xl md:text-2xl"></span>
                </button>
                <div class="text-white text-xs md:text-sm font-semibold">Play Now</div>
              </div>

              <!-- Episode Count -->
              <div class="absolute bottom-2 left-2 bg-black/80 px-2 py-1 rounded text-xs md:text-sm text-white font-semibold opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex items-center gap-1">
                <span class="mdi mdi-television-play"></span>
                {{ rec.chapterCount }} Eps
              </div>
            </figure>
            <div class="card-body p-2 md:p-3">
              <h3 class="text-xs md:text-sm font-bold line-clamp-2">{{ rec.bookName }}</h3>
              
            </div>
          </div>
      
        </div>
</section>


      <!-- New Releases -->
      <section class="mb-8 md:mb-12">
        <div class="flex items-center justify-between mb-4 md:mb-6">
          <h2 class="text-xl md:text-3xl font-bold flex items-center gap-2">
            <span class="mdi mdi-new-box text-green-500"></span>
            Rekomendasi Populer
          </h2>
         
          <!-- <a href="#" class="btn btn-ghost btn-xs md:btn-sm gap-1">
            View All
            <span class="mdi mdi-arrow-right"></span>
          </a> -->
        </div>
        <div class="grid grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-2 md:gap-4">
          <div v-for="(rec,index) in recommends" :key="'new-' + index" class="card bg-base-100 shadow-xl hover:shadow-2xl transition-all cursor-pointer group" @click="router.visit('/detail/'+rec.bookId+'/'+slugify(rec.bookName))"  >
            
            
            
            <figure class="relative overflow-hidden">
              <img :src="rec.coverWap" class="group-hover:scale-110 transition-transform duration-300" :alt="rec.bookName" />
              
              
              <!-- Top Badge -->
              <div class="absolute top-1 right-1 md:top-2 md:right-2 badge badge-success badge-xs md:badge-sm gap-1">
                <span class="mdi mdi-eye"></span>
                {{ rec.playCount }}
              </div>

              <!-- Hover Overlay -->
              <div class="absolute inset-0 bg-black/70 opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex flex-col items-center justify-center gap-2 p-3">
                <button class="btn btn-circle btn-primary btn-sm md:btn-md">
                  <span class="mdi mdi-play text-xl md:text-2xl"></span>
                </button>
                <div class="text-white text-xs md:text-sm font-semibold">Play Now</div>
              </div>

              <!-- Episode Count -->
              <div class="absolute bottom-2 left-2 bg-black/80 px-2 py-1 rounded text-xs md:text-sm text-white font-semibold opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex items-center gap-1">
                <span class="mdi mdi-television-play"></span>
                {{ rec.chapterCount }} Eps
              </div>
            </figure>
            <div class="card-body p-2 md:p-3">
              <h3 class="text-xs md:text-sm font-bold line-clamp-2">{{ rec.bookName }}</h3>
              
            </div>
          </div>
      
        </div>

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
      </section>
    </div>

   <MobileNav/>

    <!-- Footer - Hidden on Mobile -->
    <footer class="footer footer-center p-6 md:p-10 bg-base-100 text-base-content hidden md:flex">
      <div>
        <p class="font-bold text-lg">
          <span class="text-primary">DramaDrama</span>Box
        </p> 
        <p class="text-sm">Your ultimate destination for drama series</p>
      </div> 
      <div>
        <div class="grid grid-flow-col gap-4 text-sm">
          <a class="link link-hover">About</a> 
          <a class="link link-hover">Contact</a> 
          <a class="link link-hover">Terms</a> 
          <a class="link link-hover">Privacy</a>
        </div>
      </div> 
      <div>
        <p class="text-xs">Copyright © 2024 - All right reserved</p>
      </div>
    </footer>
  </div>

  <Loading :show="isLoading"/>
</template>

<script setup>
import { ref ,nextTick , computed, onMounted} from 'vue';
import Navbar from './components/Navbar.vue';
import HeaderSlider from './components/HeaderSlider.vue';
import { slugify } from '../utils/helpers';
import { router } from '@inertiajs/vue3';
import Loading from './components/Loading.vue';
import { getTheater,getCategory,getRecommend } from '../utils/api';
import MobileNav from './components/MobileNav.vue';

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
const loadMore = async (page) => {
  if (isLoading.value) return;
  isLoading.value = true;

  try {
    // 🔥 Ganti fetch → getRecommend(page)
    const res = await getRecommend(page);

    if (res.success) {
      if (Array.isArray(res.data?.records)) {
        recommends.value.push(...res.data.records);
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
    isLoading.value = false;
  }
};



onMounted(async ()=>{
  isLoading.value = true;
  let te = await getTheater();
  let c = await getCategory();
  let r = await getRecommend();
  theaters.value = te.data.you_might_like;
  coming_soon.value= te.data.coming_soon;
  recommends.value = r.data.records;
  categories.value = c.data;

  isLoading.value=false;
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