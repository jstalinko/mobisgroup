<template>
  <div class="video-player-container">
    <!-- Desktop View -->
    <div class="hidden lg:block min-h-screen bg-base-200">
      <div class="container mx-auto p-4">
        <!-- Video Area -->
        <div class="bg-black rounded-lg overflow-hidden mb-4">
          <div class="relative aspect-video">
            <video
              ref="videoPlayer"
              :src="currentEpisode?.playUrl"
              :poster="currentEpisode?.cover"
              class="w-full h-full object-contain"
              controls
              @ended="nextEpisode"
            ></video>
          </div>
        </div>

        <!-- Navigation Buttons -->
        <div class="flex gap-2 mb-4">
          <button 
            @click="previousEpisode" 
            :disabled="currentIndex === 0"
            class="btn btn-primary"
          >
            <span class="mdi mdi-chevron-left text-xl"></span>
            Sebelumnya
          </button>
          <button 
            @click="nextEpisode" 
            :disabled="currentIndex === episodes.length - 1"
            class="btn btn-primary"
          >
            Selanjutnya
            <span class="mdi mdi-chevron-right text-xl"></span>
          </button>
        </div>

        <!-- Video Info -->
        <div class="bg-base-100 rounded-lg p-6 mb-4">
          <h1 class="text-2xl font-bold mb-2">Episode {{ currentEpisode?.episode }} | {{ dramaDetail?.title }}</h1>
          <p>{{ dramaDetail?.introduction }}</p>
          <br>
          <div class="flex gap-4 text-sm text-base-content/70 mb-4">
            <span class="flex items-center gap-1">
              <span class="mdi mdi-heart"></span>
              {{ currentEpisode?.likes }}
            </span>
            <span class="flex items-center gap-1">
              <span class="mdi mdi-play-circle"></span>
              {{ currentEpisode?.chase }}
            </span>
            <span v-if="currentEpisode?.isVip" class="badge badge-warning">👑 VIP</span>
            <span v-if="currentEpisode?.isLocked" class="badge badge-error">🔒 Locked</span>
          </div>
          
          <!-- Share Buttons -->
          <div class="flex gap-2">
            <button @click="shareToWhatsApp" class="btn btn-sm btn-success gap-2">
              <span class="mdi mdi-whatsapp text-lg"></span>
              WhatsApp
            </button>
            <button @click="shareToFacebook" class="btn btn-sm btn-info gap-2">
              <span class="mdi mdi-facebook text-lg"></span>
              Facebook
            </button>
            <button @click="shareToTwitter" class="btn btn-sm gap-2">
              <span class="mdi mdi-twitter text-lg"></span>
              Twitter
            </button>
            <button @click="copyLink" class="btn btn-sm btn-ghost gap-2">
              <span class="mdi mdi-content-copy text-lg"></span>
              Copy Link
            </button>
          </div>
        </div>

        <!-- Episodes Grid -->
        <div class="bg-base-100 rounded-lg p-6">
          <h2 class="text-xl font-bold mb-4">Episode List</h2>
          <div class="flex flex-wrap gap-1.5">
            <button
              v-for="(ep, index) in episodes"
              :key="ep.id"
              @click="selectEpisode(index)"
              class="btn btn-sm min-w-[44px] h-11 font-semibold relative"
              :class="{ 
                'btn-primary': currentIndex === index, 
                'btn-outline btn-ghost': currentIndex !== index
              }"
            >
              {{ ep.episode }}
              <span v-if="ep.isVip" class="absolute -top-1 -right-1 text-xs">👑</span>
              <span v-if="ep.isLocked" class="absolute -top-1 -right-1 text-xs">🔒</span>
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Mobile View -->
    <div class="lg:hidden relative h-screen overflow-hidden bg-black">
      <div v-if="isLoading" class="absolute inset-0 flex items-center justify-center bg-black z-30">
        <div class="text-center">
          <span class="loading loading-spinner loading-lg text-primary"></span>
          <p class="text-white mt-4">Memuat video...</p>
        </div>
      </div>
      
      <div 
        ref="mobileContainer"
        class="snap-y snap-mandatory h-full overflow-y-scroll"
        @scroll="handleMobileScroll"
      >
        <div
          v-for="(episode, index) in episodes"
          :key="episode.id"
          class="snap-start h-screen flex items-center justify-center relative"
        >
          <div 
            v-if="loadingIndex.has(index)" 
            class="absolute inset-0 flex items-center justify-center bg-black/80 z-10"
          >
            <span class="loading loading-spinner loading-lg text-primary"></span>
          </div>

        <!-- Only load video if it's in the loadable range -->
<video
  v-if="shouldLoadVideo(index)"
  :ref="el => { if (el) videoRefs[index] = el }"
  :src="episode.playUrl"
  :poster="episode.cover"
  class="w-full h-full object-cover"
  controls
  loop
  playsinline
  :preload="index === currentIndex ? 'auto' : 'metadata'"
  @loadstart="handleVideoLoading(index)"
  @loadeddata="handleVideoCanPlay(index)"
  @play="handleVideoPlay(index)"
></video>

<!-- Placeholder for videos that haven't loaded yet -->
<div 
  v-else 
  class="w-full h-full flex items-center justify-center bg-gray-900"
  :style="{ backgroundImage: episode.cover ? `url(${episode.cover})` : 'none', backgroundSize: 'cover', backgroundPosition: 'center' }"
>
  <div class="absolute inset-0 bg-black/60 flex items-center justify-center">
    <div class="text-center text-white">
      <span class="mdi mdi-play-circle-outline text-6xl mb-2"></span>
      <p class="text-lg font-bold">Episode {{ episode.episode }} | {{ dramaDetail?.title }}</p>
      <p v-if="episode.isVip" class="text-sm mt-1">👑 VIP</p>
      <p v-if="episode.isLocked" class="text-sm mt-1">🔒 Locked</p>
    </div>
  </div>
</div>

          <!-- Episode Info Overlay -->
          <div class="absolute bottom-20 left-4 right-4 text-white">
            <h2 class="text-xl font-bold mb-2">Episode {{ episode.episode }} | {{ dramaDetail?.title }}</h2>
            <div class="flex gap-3 text-sm">
              <span class="flex items-center gap-1 bg-black/50 px-2 py-1 rounded">
                <span class="mdi mdi-heart"></span>
                {{ episode.likes }}
              </span>
              <span class="flex items-center gap-1 bg-black/50 px-2 py-1 rounded">
                <span class="mdi mdi-play-circle"></span>
                {{ episode.chase }}
              </span>
              <span v-if="episode.isVip" class="bg-yellow-500 px-2 py-1 rounded text-xs font-bold">👑 VIP</span>
              <span v-if="episode.isLocked" class="bg-red-500 px-2 py-1 rounded text-xs font-bold">🔒 Locked</span>
            </div>
          </div>
        </div>
      </div>

      <!-- Floating Buttons -->
      <div class="fixed right-4 bottom-32 flex flex-col gap-3 z-10">
        <!-- Episode List Button -->
        <button 
          @click="openEpisodeDrawer"
          class="btn btn-circle shadow-lg bg-white/10 backdrop-blur-md border border-white/20 hover:bg-white/20"
        >
          <span class="mdi mdi-format-list-numbered text-white text-2xl"></span>
        </button>
      </div>

      <!-- Episode Drawer -->
      <div class="drawer drawer-end">
        <input id="episode-drawer" type="checkbox" class="drawer-toggle" v-model="isEpisodeDrawerOpen" />

        <div class="drawer-side z-20">
          <label for="episode-drawer" class="drawer-overlay"></label>

          <div class="w-full h-auto bg-white/10 backdrop-blur-xl text-base-content rounded-t-3xl shadow-xl">
            <!-- Header -->
            <div class="flex items-center justify-between p-4 border-b border-white/20">
              <label 
                for="episode-drawer"
                class="btn btn-sm btn-circle bg-white/20 backdrop-blur border border-white/30 hover:bg-white/30"
              >
                <span class="mdi mdi-close text-white text-xl"></span>
              </label>

              <h3 class="text-lg font-bold text-white">Pilih Episode</h3>

              <div class="w-8"></div>
            </div>

            <!-- Episode List -->
            <div class="menu p-4">
              <div class="flex flex-wrap gap-1.5">
                <button
                  v-for="(ep, index) in episodes"
                  :key="ep.id"
                  @click="selectEpisodeMobile(index)"
                  class="btn btn-sm min-w-[44px] h-11 font-semibold relative"
                  :class="{ 
                    'btn-primary': currentIndex === index, 
                    'btn-outline btn-ghost text-white border-white/30': currentIndex !== index
                  }"
                >
                  {{ ep.episode }}
                  <span v-if="ep.isVip" class="absolute -top-1 -right-1 text-xs">👑</span>
                  <span v-if="ep.isLocked" class="absolute -top-1 -right-1 text-xs">🔒</span>
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted, watch } from 'vue';
import { getChapterDetail, getTheaterDetail } from '../../utils/api';

const props = defineProps({
  bookId: String
});

const dramaDetail = ref(null);
const episodes = ref([]);
const currentIndex = ref(0);
const videoPlayer = ref(null);
const mobileContainer = ref(null);
const videoRefs = ref([]);
const isEpisodeDrawerOpen = ref(false);
const scrollTimeout = ref(null);
const isLoading = ref(true);
const loadingIndex = ref(new Set());
const loadRange = ref(2)

const currentEpisode = computed(() => episodes.value[currentIndex.value]);

// Fungsi untuk pause semua video kecuali yang aktif
const pauseAllVideosExcept = (activeIndex) => {
  videoRefs.value.forEach((video, index) => {
    if (video && index !== activeIndex && !video.paused) {
      video.pause();
    }
  });
};

const selectEpisode = (index) => {
  currentIndex.value = index;
  if (videoPlayer.value) {
    videoPlayer.value.load();
  }
};

const selectEpisodeMobile = (index) => {
  // Pause semua video terlebih dahulu
  pauseAllVideosExcept(-1);
  
  currentIndex.value = index;
  isEpisodeDrawerOpen.value = false;
  
  // Scroll ke episode
  const container = mobileContainer.value;
  if (container) {
    container.scrollTo({
      top: index * window.innerHeight,
      behavior: 'smooth'
    });
  }
  
  // Auto play setelah scroll selesai
  setTimeout(() => {
    pauseAllVideosExcept(index);
    const currentVideo = videoRefs.value[index];
    if (currentVideo) {
      currentVideo.play().catch(err => console.log('Play error:', err));
    }
  }, 600  );
};

const shouldLoadVideo = (index) => {
  const distance = Math.abs(index - currentIndex.value);
  return distance <= loadRange.value;
};

const nextEpisode = () => {
  if (currentIndex.value < episodes.value.length - 1) {
    selectEpisode(currentIndex.value + 1);
  }
};

const previousEpisode = () => {
  if (currentIndex.value > 0) {
    selectEpisode(currentIndex.value - 1);
  }
};

const handleMobileScroll = (e) => {
  if (scrollTimeout.value) {
    clearTimeout(scrollTimeout.value);
  }

  scrollTimeout.value = setTimeout(() => {
    const container = e.target;
    const scrollTop = container.scrollTop;
    const newIndex = Math.round(scrollTop / window.innerHeight);
    
    if (newIndex !== currentIndex.value && newIndex >= 0 && newIndex < episodes.value.length) {
      // Pause semua video
      pauseAllVideosExcept(-1);
      
      currentIndex.value = newIndex;
      
      // Auto play video saat ini
      setTimeout(() => {
        const currentVideo = videoRefs.value[newIndex];
        if (currentVideo) {
          currentVideo.play().catch(err => console.log('Play error:', err));
        }
      }, 200);
    }
  }, 150);
};

const handleVideoLoading = (index) => {
  loadingIndex.value.add(index);
};

const handleVideoCanPlay = (index) => {
  loadingIndex.value.delete(index);
  if (index === 0) {
    isLoading.value = false;
  }
};

const handleVideoPlay = (index) => {
  // Ketika video mulai diputar, pause semua video lainnya
  pauseAllVideosExcept(index);
};

const openEpisodeDrawer = () => {
  isEpisodeDrawerOpen.value = true;
};

const shareToWhatsApp = () => {
  const text = `Episode ${currentEpisode.value.episode} | ${dramaDetail.value?.title}`;
  const url = window.location.href;
  window.open(`https://wa.me/?text=${encodeURIComponent(text + ' ' + url)}`, '_blank');
};

const shareToFacebook = () => {
  const url = window.location.href;
  window.open(`https://www.facebook.com/sharer/sharer.php?u=${encodeURIComponent(url)}`, '_blank');
};

const shareToTwitter = () => {
  const text = `Episode ${currentEpisode.value.episode} | ${dramaDetail.value?.title}`;
  const url = window.location.href;
  window.open(`https://twitter.com/intent/tweet?text=${encodeURIComponent(text)}&url=${encodeURIComponent(url)}`, '_blank');
};

const copyLink = () => {
  navigator.clipboard.writeText(window.location.href).then(() => {
    alert('Link berhasil disalin!');
  });
};

// Watch currentIndex untuk memastikan hanya satu video yang diputar
watch(currentIndex, (newIndex) => {
  pauseAllVideosExcept(newIndex);
});

onMounted(async () => {
  try {
    const response = await getChapterDetail(props.bookId);
    episodes.value = response?.data?.episodes || [];
    const detailResponse = await getTheaterDetail(props.bookId);
    dramaDetail.value = detailResponse?.data || null;
  } catch (error) {
    console.error('Error loading episodes:', error);
  }
});

onUnmounted(() => {
  if (scrollTimeout.value) {
    clearTimeout(scrollTimeout.value);
  }
  // Pause semua video saat component di-unmount
  pauseAllVideosExcept(-1);
});
</script>

<style scoped>
.video-player-container {
  font-family: 'Inter', system-ui, -apple-system, sans-serif;
}

/* Hide scrollbar for mobile snap scroll */
.snap-y::-webkit-scrollbar {
  display: none;
}

.snap-y {
  -ms-overflow-style: none;
  scrollbar-width: none;
}

/* Drawer animation */
.drawer-side > * {
  transition: transform 0.3s ease-in-out;
}
</style>