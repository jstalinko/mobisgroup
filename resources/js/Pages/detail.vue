<template>
  <div class="min-h-screen bg-base-300">
    <!-- Navbar Component -->
    <Navbar />

    <div class="container mx-auto px-3 md:px-6 py-6 ">
      <!-- Breadcrumb -->
      <div class="text-sm breadcrumbs mb-6">
        <ul>
          <li>
            <a href="/" class="flex items-center gap-1">
              <span class="mdi mdi-home"></span>
              Home
            </a>
          </li>
          <li>
            <a href="/romance" class="flex items-center gap-1">
              <span class="mdi mdi-heart"></span>
              Romance
            </a>
          </li>
          <li>{{ detail.bookName }}</li>
        </ul>
      </div>

      <!-- Main Content -->
      <div class="card bg-base-100 shadow-xl mb-8">
        <div class="card-body p-4 md:p-6">
          <div class="flex flex-col md:flex-row gap-4 md:gap-6">
            <!-- Poster - Mobile Friendly -->
            <div class="w-full md:w-48 lg:w-56 flex-shrink-0">
              <figure class="relative group">
                <img 
                  :src="detail.coverWap" 
                  :alt="detail.bookName"
                  class="w-full rounded-lg aspect-[2/3] object-cover"
                />
                <!-- Play Overlay -->
                <div class="absolute inset-0 bg-black/60 opacity-0 group-hover:opacity-100 transition-opacity duration-300 rounded-lg flex items-center justify-center">
                  <button class="btn btn-circle btn-primary btn-lg">
                    <span class="mdi mdi-play text-3xl"></span>
                  </button>
                </div>
              </figure>
              <button class="btn btn-primary w-full gap-2 mt-4" @click="router.visit('/play/'+detail.bookId+'/1/'+slugify(detail.bookName))">
                <span class="mdi mdi-play"></span>
                Play Now
              </button>
            </div>

            <!-- Info -->
            <div class="flex-1">
              <h1 class="text-2xl md:text-3xl lg:text-4xl font-bold mb-3">{{ detail.bookName }}</h1>
              
              <div class="flex items-center gap-2 mb-4">
                <span class="badge badge-lg">{{ detail.chapterCount }} Episodes</span>
              </div>

              <p class="text-base-content/80 mb-4 leading-relaxed text-sm md:text-base">
                {{ detail.introduction }}
              </p>

              <!-- Tags -->
              <div class="flex flex-wrap gap-2 mb-4">
                <div 
                  v-for="tag in detail.tags" 
                  :key="tag"
                  class="badge badge-outline badge-sm"
                >
                  {{ tag }}
                </div>
              </div>

              <!-- Share -->
              <div class="flex items-center gap-3">
                <span class="font-semibold text-sm">Share:</span>
                <button class="btn btn-circle btn-sm" style="background-color: #1877F2; border: none;">
                  <span class="mdi mdi-facebook text-white"></span>
                </button>
                <button class="btn btn-circle btn-sm" style="background-color: #000000; border: none;">
                  <span class="mdi mdi-twitter text-white"></span>
                </button>
                <button class="btn btn-circle btn-sm" style="background: linear-gradient(45deg, #F58529, #DD2A7B, #8134AF); border: none;">
                  <span class="mdi mdi-instagram text-white"></span>
                </button>
                <button class="btn btn-circle btn-sm btn-neutral">
                  <span class="mdi mdi-link-variant"></span>
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Episode List -->
      <section class="mb-8">
        <div class="flex items-center justify-between mb-6">
          <h2 class="text-2xl md:text-3xl font-bold flex items-center gap-2">
            <span class="mdi mdi-playlist-play"></span>
            Episode List
            <span class="badge badge-lg badge-primary">{{ episodes.length }} Episodes</span>
          </h2>
        </div>

        <div class="grid grid-cols-3 md:grid-cols-4 lg:grid-cols-6 xl:grid-cols-8 gap-2 md:gap-3">
          <div 
            v-for="episode in displayedEpisodes" 
            :key="episode.chapterIndex"
            class="card bg-base-100 shadow hover:shadow-lg transition-all cursor-pointer group"
            @click="router.visit('/play/'+detail.bookId+'/'+(episode.chapterIndex+1)+'/'+slugify(detail.bookName))"
          >
            <div class="card-body p-2 md:p-3 text-center">
              <div class="relative mb-2">
                <span class="mdi mdi-play-circle text-3xl md:text-4xl text-primary group-hover:text-primary-focus transition-colors"></span>
                <!-- Lock Icon for Locked Episodes -->
               
                <!-- Premium Badge -->
                <span 
                  v-if="episode.isCharge"
                  class="mdi mdi-crown absolute top-0 left-0 text-lg text-warning"
                ></span>
              </div>
              <h3 class="font-bold text-xs md:text-sm">{{ detail.bookName }}</h3>
              <p class="text-xs text-base-content/70">Episode {{ (episode.chapterIndex+1) }}</p>
            </div>
          </div>
        </div>

        <!-- View More Button -->
        <div class="text-center mt-6" v-if="hasMoreEpisodes">
          <button class="btn btn-outline btn-wide gap-2" @click="loadMore">
            Tampilkan lainnya
            <span class="mdi mdi-chevron-down"></span>
          </button>
        </div>
      </section>


      <!-- Recommended for you -->
      <section class="mb-8">
        <h2 class="text-2xl md:text-3xl font-bold mb-6 flex items-center gap-2">
          <span class="mdi mdi-thumb-up"></span>
          Yang mungkin anda suka 
        </h2>
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-6 gap-3 md:gap-4">
          <div 
            v-for="item in recommended" 
            :key="item.bookId"
            class="card bg-base-100 shadow-xl hover:shadow-2xl transition-all cursor-pointer group"
            @click="router.visit('/detail/'+item.bookId+'/'+slugify(item.bookName))"
          >
            <figure class="relative overflow-hidden">
              <img 
                :src="item.coverWap" 
                :alt="item.bookName"
                class="w-full aspect-[3/4] object-cover group-hover:scale-110 transition-transform duration-300"
              />
              <!-- Hover Overlay -->
              <div class="absolute inset-0 bg-black/60 opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex items-center justify-center">
                <button class="btn btn-circle btn-primary">
                  <span class="mdi mdi-play text-xl"></span>
                </button>
              </div>
            </figure>
            <div class="card-body p-3">
              <h3 class="font-bold text-sm line-clamp-2">{{ item.bookName }}</h3>
              <div class="flex items-center gap-1 text-xs">
                <span class="mdi mdi-eye text-orange-400"></span>
                <span>{{ item.playCount }}</span>
              </div>
            </div>
          </div>
        </div>
      </section>
    </div>

   <MobileNav />

   <Footer/>
  </div>
  <Loading :show="isLoading"  />
</template>

<script setup>
import { onMounted, ref ,computed} from 'vue';
import Navbar from './components/Navbar.vue';
import Loading from './components/Loading.vue';
import MobileNav from './components/MobileNav.vue';
import { getChapterDetail , getTheaterDetail } from '../utils/api';
import { slugify } from '../utils/helpers';
import Footer from './components/Footer.vue';
import { router } from '@inertiajs/vue3';
const props = defineProps({prop:Object});
const detail = ref([]);
const chapters = ref([]);
const episodes = ref([]);
const activeMenu = ref('home');
const isLoading =ref(false);
const recommended = ref([]);

const visibleCount = ref(8)

// Computed
const displayedEpisodes = computed(() => {
  return episodes.value.slice(0, visibleCount.value)
})

const hasMoreEpisodes = computed(() => {
  return visibleCount.value < episodes.value.length
})

// Methods
const loadMore = () => {
  visibleCount.value += 8
}

onMounted(async ()=>{
isLoading.value = true;
let d = await getTheaterDetail(props.prop.bookId);
let c = await getChapterDetail(props.prop.bookId);
detail.value = d?.data;
chapters.value = c?.data;
episodes.value = c?.data?.list;
recommended.value = c?.data?.recommendList;
isLoading.value =false;
});



const reviews = ref([
  { id: 1, title: 'Move to Countryside, Marry a Billionaire! full movie', url: '#' },
  { id: 2, title: 'Move to Countryside, Marry a Billionaire! Dailymotion: Secret Billionaires, Small Town Chaos, and Romance You Can\'t Resist', url: '#' }
]);


</script>

<style scoped>
.line-clamp-2 {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

.btm-nav > button.active {
  border-top: 2px solid oklch(var(--p));
  background-color: oklch(var(--b2));
}
</style>