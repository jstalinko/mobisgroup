<template>
  <div class="video-player-container">
    <!-- Desktop View -->
    <div class="hidden lg:block min-h-screen bg-base-200" v-if="!isMobile">
      <div class="container mx-auto p-4">
     <!-- Video Area -->
        <div class="bg-black rounded-lg overflow-hidden mb-4">
          <div class="relative aspect-video">
            <video
              ref="videoPlayer"
              :src="nginxCacheVideo(currentEpisode?.playUrl,dramaDetail?.id , currentIndex+1 ,slugify(dramaDetail?.title))"
              :poster="currentEpisode?.cover"
              class="w-full h-full object-contain"
              controls
              @ended="nextEpisode"
            ></video>
            <!-- Floating Watermark Desktop -->
            <div class="absolute top-4 right-4 text-white font-bold text-lg opacity-70 pointer-events-none z-10 bg-black/30 px-3 py-1 rounded-lg backdrop-blur-sm">
              {{ setting.site_name }}
            </div>
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
    <Loading :show="isLoading && !isMobile"/>
    <div v-if="showAdModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/80 backdrop-blur-sm">
      <div class="bg-base-100 rounded-2xl shadow-2xl max-w-md w-full mx-4 overflow-hidden">
        <div class="relative bg-gradient-to-br from-primary/20 to-secondary/20 p-8">
          <div class="text-center">
            <div class="mb-6">
              <span class="mdi mdi-play-circle-outline text-7xl text-primary"></span>
            </div>
            <h3 class="text-3xl font-bold mb-3">Iklan</h3>
            <p class="text-base-content/70 text-lg mb-2">Untuk melanjutkan menonton,</p>
            <p class="text-base-content/70 text-lg">silakan klik tombol di bawah</p>
          </div>
        </div>
        
        <div class="p-6">
          <button 
            @click="closeAdAndContinue"
            class="btn btn-primary btn-block btn-lg gap-2 shadow-lg hover:scale-105 transition-transform"
          >
            <span class="mdi mdi-play text-xl"></span>
            Klik disini untuk melanjutkan
          </button>
        </div>
      </div>
    </div>

    <!-- Mobile View -->
    <div class="lg:hidden relative h-screen overflow-hidden bg-black" v-if="isMobile">
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

          
          <div class="absolute top-4 right-4 text-white font-bold text-base opacity-70 pointer-events-none z-20 bg-black/30 px-3 py-1 rounded-lg backdrop-blur-sm">
            {{setting.site_name}}
          </div>
          <video
            v-if="shouldLoadVideo(index)"
            :ref="el => { if (el) videoRefs[index] = el }"
            :src="nginxCacheVideo(episode.playUrl , dramaDetail?.id , currentIndex+1 ,slugify(dramaDetail?.title))"
            :poster="episode.cover"
            class="w-full h-full object-cover"
            controls
            playsinline
            :preload="index === currentIndex ? 'auto' : 'none'"
            @loadstart="handleVideoLoading(index)"
            @loadeddata="handleVideoCanPlay(index)"
            @canplay="handleVideoPlay(index)"
            @ended="handleVideoEnded(index)"
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
        <button @click="router.visit('/')" class="btn btn-circle shadow-lg bg-white/10 backdrop-blur-md border border-white/20 hover:bg-white/20">
  <span class="mdi mdi-home text-white text-2xl"></span>
</button>
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

          <div class="w-full h-auto bg-white/10 backdrop-blur-xl text-base-content rounded-3xl shadow-xl">
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
import { nginxCacheVideo, siteSetting } from '../../utils/helpers';
import Loading from './Loading.vue';
import { router } from '@inertiajs/vue3';
const props = defineProps({
  bookId: String,
  episode: String
});
const setting = siteSetting();
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
const loadRange = ref(2);
const isMobile = ref(false); 
const showAdModal = ref(false);
const adShownForEpisode = ref(new Set());
const adTimeoutId = ref(null);

const currentEpisode = computed(() => episodes.value[currentIndex.value]);

const updateURL = (episodeNum) => {
  const pathParts = window.location.pathname.split('/');
  const bookIdIndex = pathParts.findIndex(part => part === props.bookId);
  if (bookIdIndex !== -1 && pathParts[bookIdIndex + 1]) {
    pathParts[bookIdIndex + 1] = episodeNum.toString();
    const newPath = pathParts.join('/');
    window.history.pushState({}, '', newPath);
  }
};

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
  updateURL(episodes.value[index].episode);
  
  // Setup ad for desktop
  if (!isMobile.value) {
    setTimeout(() => {
      setupDesktopAdListener();
    }, 500);
  }
};

const selectEpisodeMobile = (index) => {
  pauseAllVideosExcept(-1);
  
  currentIndex.value = index;
  isEpisodeDrawerOpen.value = false;
  
  updateURL(episodes.value[index].episode);
  
  const container = mobileContainer.value;
  if (container) {
    container.scrollTo({
      top: index * window.innerHeight,
      behavior: 'smooth'
    });
  }
  
  setTimeout(() => {
    pauseAllVideosExcept(index);
    const currentVideo = videoRefs.value[index];
    if (currentVideo) {
      currentVideo.play().catch(err => console.log('Play error:', err));
    }
  }, 600);
};

// ✅ OPTIMASI: Desktop load 1 video, Mobile load current + adjacent
const shouldLoadVideo = (index) => {
  if (!isMobile.value) {
    return index === currentIndex.value; // Desktop: hanya current
  }
  const distance = Math.abs(index - currentIndex.value);
  return distance <= loadRange.value; // Mobile: current + adjacent
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
      pauseAllVideosExcept(-1);
      
      currentIndex.value = newIndex;
      
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
  if (index === 0 && isMobile.value) {
    isLoading.value = false;
  }
  
  // Schedule ad for mobile
  if (isMobile.value && index === currentIndex.value) {
    const video = videoRefs.value[index];
    if (video) {
      scheduleAdForVideo(video, index);
    }
  }
};

const handleVideoEnded = (index) => {
  if (isMobile.value && index < episodes.value.length - 1) { // ✅ GUNAKAN isMobile.value
    setTimeout(() => {
      const container = mobileContainer.value;
      if (container) {
        container.scrollTo({
          top: (index + 1) * window.innerHeight,
          behavior: 'smooth'
        });
      }
      updateURL(currentIndex.value+1);
    }, 500);
  }
};

const handleVideoPlay = (index) => {
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
const scheduleAdForVideo = (video, episodeIndex) => {
  if (!adSetting?.ads?.active || adShownForEpisode.value.has(episodeIndex)) {
    return;
  }

  if (adTimeoutId.value) {
    clearTimeout(adTimeoutId.value);
  }

  const minTime = 1;
  const maxTime = 30;
  const videoDuration = video.duration || 120;
  const randomTime = Math.min(
    Math.random() * (maxTime - minTime) + minTime,
    videoDuration * 0.7
  );

  adTimeoutId.value = setTimeout(() => {
    if (video && !video.paused) {
      showAd(video, episodeIndex);
    }
  }, randomTime * 1000);
};

const showAd = (video, episodeIndex) => {
  video.pause();
  showAdModal.value = true;
  adShownForEpisode.value.add(episodeIndex);
};

const closeAdAndContinue = () => {
  // Open ad URL in new tab
  if (adSetting?.ads?.ad_url) {
    window.open(adSetting.ads.ad_url, '_blank');
  }
  
  showAdModal.value = false;
  
  const currentVideo = isMobile.value 
    ? videoRefs.value[currentIndex.value]
    : videoPlayer.value;
    
  if (currentVideo) {
    currentVideo.play().catch(err => console.log('Play error:', err));
  }
};

const setupDesktopAdListener = () => {
  if (!videoPlayer.value || !adSetting?.ads?.active) return;
  
  const video = videoPlayer.value;
  let adScheduled = false;

  const checkAdTiming = () => {
    if (adScheduled || adShownForEpisode.value.has(currentIndex.value)) return;
    
    const currentTime = video.currentTime;
    const duration = video.duration || 120;
    
    const randomTime = Math.min(
      Math.random() * 30 + 30,
      duration * 0.7
    );
    
    if (currentTime >= randomTime) {
      adScheduled = true;
      showAd(video, currentIndex.value);
      video.removeEventListener('timeupdate', checkAdTiming);
    }
  };

  video.addEventListener('timeupdate', checkAdTiming);
};
watch(currentIndex, (newIndex) => {
  pauseAllVideosExcept(newIndex);
});

onMounted(async () => {
  // ✅ DETEKSI DEVICE TYPE
  isMobile.value = window.innerWidth < 1024;
  
  const handleResize = () => {
    isMobile.value = window.innerWidth < 1024;
  };
  window.addEventListener('resize', handleResize);
  
  try {
    isLoading.value = true;
    const response = await getChapterDetail(props.bookId);
    episodes.value = response?.data?.episodes || [];
    const detailResponse = await getTheaterDetail(props.bookId);
    dramaDetail.value = detailResponse?.data || null;
    
    if (episodes.value.length > 0) {
      const episodeNum = parseInt(props.episode) || 1;
      const episodeIndex = episodes.value.findIndex(ep => ep.episode === episodeNum);
      
      if (episodeIndex !== -1) {
        currentIndex.value = episodeIndex;
      } else {
        currentIndex.value = 0;
      }
      
      if (isMobile.value && mobileContainer.value) {
        setTimeout(() => {
          mobileContainer.value.scrollTo({
            top: currentIndex.value * window.innerHeight,
            behavior: 'auto'
          });
        }, 100);
      }
      
      // Setup ad for desktop initial load
      if (!isMobile.value && videoPlayer.value) {
        setTimeout(() => {
          setupDesktopAdListener();
        }, 1000);
      }
    
    }
  } catch (error) {
    console.error('Error loading episodes:', error);
  }
  isLoading.value=false;
  

});
onUnmounted(() => {
  if (scrollTimeout.value) {
    clearTimeout(scrollTimeout.value);
  }
  if (adTimeoutId.value) {
    clearTimeout(adTimeoutId.value);
  }
  pauseAllVideosExcept(-1);
});
  
watch(() => props.episode, (newEpisode) => {
  if (newEpisode && episodes.value.length > 0) {
    const episodeNum = parseInt(newEpisode);
    const episodeIndex = episodes.value.findIndex(ep => ep.episode === episodeNum);
    
    if (episodeIndex !== -1 && episodeIndex !== currentIndex.value) {
      currentIndex.value = episodeIndex;
      
      if (mobileContainer.value) {
        mobileContainer.value.scrollTo({
          top: episodeIndex * window.innerHeight,
          behavior: 'smooth'
        });
      }
      
      if (videoPlayer.value) {
        videoPlayer.value.load();
      }
    }
  }
});

onUnmounted(() => {
  if (scrollTimeout.value) {
    clearTimeout(scrollTimeout.value);
  }
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