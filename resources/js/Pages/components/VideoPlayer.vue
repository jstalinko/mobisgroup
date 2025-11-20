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
              :src="currentVideoUrl"
              class="w-full h-full object-contain"
              controls
              @ended="nextEpisode"
            ></video>
            
            <!-- Quality Selector -->
            <div class="absolute top-4 right-4">
              <div class="dropdown dropdown-end">
                <label tabindex="0" class="btn btn-sm btn-circle btn-ghost bg-black/50 text-white">
                  <span class="mdi mdi-cog text-xl"></span>
                </label>
                <ul tabindex="0" class="dropdown-content menu p-2 shadow bg-base-100 rounded-box w-32 mt-2">
                  <li v-for="url in currentEpisode?.video_urls" :key="url.quality">
                    <a @click="changeQuality(url.url)" :class="{ 'active': currentVideoUrl === url.url }">
                      {{ url.quality }}p
                    </a>
                  </li>
                </ul>
              </div>
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
          <h1 class="text-2xl font-bold mb-2">{{ currentEpisode?.title }}</h1>
          <p class="text-base-content/70 mb-4">{{ currentEpisode?.description }}</p>
          
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
              class="btn btn-sm min-w-[44px] h-11 font-semibold"
              :class="{ 'btn-primary': currentIndex === index, 'btn-outline btn-ghost': currentIndex !== index }"
            >
              {{ index + 1 }}
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Mobile View -->
    <div class="lg:hidden relative h-screen overflow-hidden bg-black">
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
          <video
            :ref="el => { if (el) videoRefs[index] = el }"
            :src="getDefaultVideoUrl(episode)"
            class="w-full h-full object-cover"
            :controls="currentIndex === index"
            :autoplay="currentIndex === index"
            loop
            playsinline
          ></video>

          <!-- Episode Info Overlay -->
          <div class="absolute bottom-20 left-4 right-4 text-white">
            <h2 class="text-xl font-bold mb-1">{{dramaDetail.title}} - {{ episode.title }}</h2>
            
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

  <!-- Quality Button -->
  <button 
    @click="openQualityDrawer"
    class="btn btn-circle shadow-lg bg-white/10 backdrop-blur-md border border-white/20 hover:bg-white/20"
  >
    <span class="mdi mdi-cog text-white text-2xl"></span>
  </button>

</div>


      <!-- Episode Drawer -->
    <div class="drawer drawer-end">

  <!-- Drawer toggle -->
  <input id="episode-drawer" type="checkbox" class="drawer-toggle" v-model="isEpisodeDrawerOpen" />

  <div class="drawer-side z-20">
    <label for="episode-drawer" class="drawer-overlay"></label>

    <div class="w-full h-auto bg-white/10 backdrop-blur-xl text-base-content rounded-t-3xl shadow-xl">

      <!-- Header -->
      <div class="flex items-center justify-between p-4 border-b border-white/20">
        <!-- Close Button -->
        <label 
          for="episode-drawer"
          class="btn btn-sm btn-circle bg-white/20 backdrop-blur border border-white/30 hover:bg-white/30"
        >
          <span class="mdi mdi-close text-white text-xl"></span>
        </label>

        <h3 class="text-lg font-bold text-white">Pilih Episode</h3>

        <!-- Dummy element biar title center -->
        <div class="w-8"></div>
      </div>

      <!-- Episode List -->
      <div class="menu p-4">
        <div class="flex flex-wrap gap-1.5">
          <button
            v-for="(ep, index) in episodes"
            :key="ep.id"
            @click="selectEpisodeMobile(index)"
            class="btn btn-sm min-w-[44px] h-11 font-semibold"
            :class="{ 
              'btn-primary': currentIndex === index, 
              'btn-outline btn-ghost text-white border-white/30': currentIndex !== index 
            }"
          >
            {{ index + 1 }}
          </button>
        </div>
      </div>

    </div>
  </div>
</div>


     <!-- Quality Drawer -->
<div class="drawer drawer-end">
  <input id="quality-drawer" type="checkbox" class="drawer-toggle" v-model="isQualityDrawerOpen" />

  <div class="drawer-side z-20">
    <label for="quality-drawer" class="drawer-overlay"></label>

    <div class="w-80 h-auto bg-white/10 backdrop-blur-xl text-base-content rounded-t-3xl shadow-xl">

      <!-- Header -->
      <div class="flex items-center justify-between p-4 border-b border-white/20">
        <label 
          for="quality-drawer"
          class="btn btn-sm btn-circle bg-white/20 backdrop-blur border border-white/30 hover:bg-white/30"
        >
          <span class="mdi mdi-close text-white text-xl"></span>
        </label>

        <h3 class="text-lg font-bold text-white">Pilih Kualitas Video</h3>

        <div class="w-8"></div>
      </div>

      <!-- Content -->
      <div class="menu p-4">
        <div class="flex flex-col gap-2">
          <button
            v-for="url in currentEpisode?.video_urls"
            :key="url.quality"
            @click="changeQualityMobile(url.url)"
            class="btn btn-outline text-white border-white/40 hover:bg-white/20"
            :class="{ 
              'btn-active bg-white/30 text-black border-white': currentVideoUrl === url.url 
            }"
          >
            {{ url.quality }}p
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
import { ref, computed, onMounted, onUnmounted } from 'vue';
import { getChapterDetail,getTheaterDetail,getPlayerVideo } from '../../utils/api';

const props = defineProps({bookId:String,episode:String});
const episodes = ref([]);
const dramaDetail = ref([]);
const currentIndex = ref(0);
const currentVideoUrl = ref('');
const videoPlayer = ref(null);
const mobileContainer = ref(null);
const videoRefs = ref([]);
const isEpisodeDrawerOpen = ref(false);
const isQualityDrawerOpen = ref(false);
const scrollTimeout = ref(null);

const currentEpisode = computed(() => episodes.value[currentIndex.value]);

const getDefaultVideoUrl = (episode) => {
  const defaultVideo = episode?.video_urls?.find(v => v.is_default);
  return defaultVideo ? defaultVideo.url : episode?.video_urls[0].url;
};

const selectEpisode = (index) => {
  currentIndex.value = index;
  currentVideoUrl.value = getDefaultVideoUrl(currentEpisode.value);
};

const selectEpisodeMobile = (index) => {
  currentIndex.value = index;
  currentVideoUrl.value = getDefaultVideoUrl(currentEpisode.value);
  isEpisodeDrawerOpen.value = false;
  
  // Scroll to episode
  const container = mobileContainer.value;
  if (container) {
    container.scrollTo({
      top: index * window.innerHeight,
      behavior: 'smooth'
    });
  }
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

const changeQuality = (url) => {
  const currentTime = videoPlayer.value?.currentTime || 0;
  currentVideoUrl.value = url;
  setTimeout(() => {
    if (videoPlayer.value) {
      videoPlayer.value.currentTime = currentTime;
      videoPlayer.value.play();
    }
  }, 100);
};

const changeQualityMobile = (url) => {
  const currentVideo = videoRefs.value[currentIndex.value];
  const currentTime = currentVideo?.currentTime || 0;
  currentVideoUrl.value = url;
  isQualityDrawerOpen.value = false;
  
  setTimeout(() => {
    if (currentVideo) {
      currentVideo.src = url;
      currentVideo.currentTime = currentTime;
      currentVideo.play();
    }
  }, 100);
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
      currentIndex.value = newIndex;
      currentVideoUrl.value = getDefaultVideoUrl(currentEpisode.value);
      
      // Play current video, pause others
      videoRefs.value.forEach((video, idx) => {
        if (video) {
          if (idx === newIndex) {
            video.play();
          } else {
            video.pause();
          }
        }
      });
    }
  }, 150);
};

const openEpisodeDrawer = () => {
  isEpisodeDrawerOpen.value = true;
};

const openQualityDrawer = () => {
  isQualityDrawerOpen.value = true;
};

const shareToWhatsApp = () => {
  const text = `${currentEpisode.value.title} - ${currentEpisode.value.description}`;
  const url = window.location.href;
  window.open(`https://wa.me/?text=${encodeURIComponent(text + ' ' + url)}`, '_blank');
};

const shareToFacebook = () => {
  const url = window.location.href;
  window.open(`https://www.facebook.com/sharer/sharer.php?u=${encodeURIComponent(url)}`, '_blank');
};

const shareToTwitter = () => {
  const text = `${currentEpisode.value.title} - ${currentEpisode.value.description}`;
  const url = window.location.href;
  window.open(`https://twitter.com/intent/tweet?text=${encodeURIComponent(text)}&url=${encodeURIComponent(url)}`, '_blank');
};

const copyLink = () => {
  navigator.clipboard.writeText(window.location.href).then(() => {
    alert('Link berhasil disalin!');
  });
};



onMounted(async () => {
  let r = await getPlayerVideo(props.bookId,props.episode);
  let x = await getTheaterDetail(props.bookId);
  currentVideoUrl.value = getDefaultVideoUrl(currentEpisode.value);
  episodes.value = r?.data;
  dramaDetail.value = x?.data;
});

onUnmounted(() => {
  if (scrollTimeout.value) {
    clearTimeout(scrollTimeout.value);
  }
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