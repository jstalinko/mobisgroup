<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue'

const props = defineProps({
  videoData: {
    type: Object,
    default: () => ({
      title: "Anime Series Title",
      synopsis: "Lorem ipsum dolor sit amet...",
      tags: ["Action", "Adventure"],
      episodeCount: 10,
      currentEpisodeNumber: 1,
      views: "2.5",
      likes: "120",
      episodes: Array.from({ length: 10 }, (_, i) => ({
        number: i + 1,
        title: `Episode ${i + 1}`,
        synopsis: `Synopsis for episode ${i + 1}`
      })),
      videoUrl: "http://localhost/videos/example.mp4"
    })
  }
})

const currentEpisode = ref(props.videoData?.currentEpisodeNumber || 1)
const videoRef = ref(null)
const isPlaying = ref(false)
const showControls = ref(true)
const controlsTimeout = ref(null)
const isMobile = ref(false)
const showEpisodesInDrawer = ref(50)

const currentEpisodeDetail = computed(() => {
  return props.videoData?.episodes.find(ep => ep.number === currentEpisode.value)
})

const watchButtonText = computed(() => {
  return currentEpisode.value > 1 ? 'Lanjutkan Menonton' : 'Tonton Sekarang'
})

const checkMobile = () => {
  isMobile.value = window.innerWidth < 1024
}

onMounted(() => {
  checkMobile()
  window.addEventListener('resize', checkMobile)
})

onUnmounted(() => {
  window.removeEventListener('resize', checkMobile)
  if (controlsTimeout.value) clearTimeout(controlsTimeout.value)
})

const resetControlsTimeout = () => {
  if (controlsTimeout.value) clearTimeout(controlsTimeout.value)

  controlsTimeout.value = setTimeout(() => {
    if (isPlaying.value) showControls.value = false
  }, 3000)
}

const handleMouseMove = () => {
  showControls.value = true
  resetControlsTimeout()
}

const handleVideoClick = (e) => {
  if (e.target.tagName === 'BUTTON' || e.target.tagName === 'I') return
  togglePlay()
}

const togglePlay = async () => {
  if (!videoRef.value) return

  try {
    if (videoRef.value.paused) {
      await videoRef.value.play()
    } else {
      videoRef.value.pause()
    }
  } catch (err) {
    console.warn("Play blocked:", err)
  }

  showControls.value = true
  resetControlsTimeout()
}

const toggleFullscreen = () => {
  const vid = videoRef.value
  if (!vid) return

  if (vid.requestFullscreen) vid.requestFullscreen()
  else if (vid.webkitRequestFullscreen) vid.webkitRequestFullscreen()
  else if (vid.mozRequestFullScreen) vid.mozRequestFullScreen()
}

const playEpisode = (epNum) => {
  currentEpisode.value = epNum

  const drawer = document.getElementById('episode_drawer_player')
  if (drawer) drawer.close()

  if (!videoRef.value) return

  videoRef.value.pause()
  videoRef.value.load()

  setTimeout(async () => {
    try {
      await videoRef.value.play()
    } catch {}
  }, 150)
}

const openEpisodeDrawer = () => {
  const drawer = document.getElementById('episode_drawer_player')
  if (drawer) drawer.showModal()
}

const loadMoreEpisodesInDrawer = () => {
  showEpisodesInDrawer.value += 50
}
</script>


<template>
  <div class="bg-base-100 min-h-screen">

    <!-- Mobile Header -->
    <div 
      v-if="isMobile" 
      v-show="showControls || !isPlaying"
      class="bg-black/30 backdrop-blur-md text-white p-4 flex items-center justify-between fixed top-0 left-0 right-0 z-30"
      :class="{ 'opacity-0': !showControls && isPlaying }"
    >
      <a href="#" class="btn btn-ghost btn-circle btn-sm">
        <i class="mdi mdi-arrow-left text-xl"></i>
      </a>
      <span class="font-bold truncate max-w-[60%]">{{ videoData.title }}</span>
      <span>EP.{{ currentEpisode }}</span>
    </div>

    <div class="max-w-7xl mx-auto lg:p-4">
      <div class="flex flex-col lg:flex-row lg:gap-4">

        <!-- Video Player -->
        <div class="flex-grow lg:w-3/4">
          <div 
            :class="[
              'relative bg-black',
              isMobile ? 'h-screen w-screen fixed top-0 left-0 z-20' : 'aspect-video'
            ]"
            @click="handleVideoClick"
            @mousemove="handleMouseMove"
            @touchstart="handleMouseMove"
          >

            <!-- VIDEO ELEMENT -->
            <video
              ref="videoRef"
              class="w-full h-full object-contain"
              @play="isPlaying = true"
              @pause="isPlaying = false"
              preload="metadata"
              playsinline
              webkit-playsinline
              x5-playsinline
            >
              <source :src="videoData.videoUrl" type="video/mp4">
              Browser Anda tidak mendukung video tag.
            </video>

            <!-- PLAY BUTTON -->
            <div 
              v-show="showControls || !isPlaying"
              class="absolute inset-0 flex items-center justify-center pointer-events-none"
              :class="{ 'opacity-0': !showControls && isPlaying }"
            >
              <button 
                @click="togglePlay"
                class="btn btn-circle btn-ghost text-white text-6xl opacity-80 pointer-events-auto"
              >
                <i :class="['mdi', isPlaying ? 'mdi-pause-circle-outline' : 'mdi-play-circle-outline']"></i>
              </button>
            </div>

            <!-- MOBILE CONTROLS -->
            <div 
              v-if="isMobile"
              v-show="showControls || !isPlaying"
              class="absolute right-4 bottom-20 flex flex-col gap-3 z-10"
              :class="{ 'opacity-0': !showControls && isPlaying }"
            >
              <button @click="toggleFullscreen" class="btn btn-circle btn-sm bg-black/70 text-white">
                <i class="mdi mdi-fullscreen"></i>
              </button>

              <button @click="openEpisodeDrawer" class="btn btn-circle btn-sm bg-black/70 text-white">
                <i class="mdi mdi-format-list-bulleted"></i>
              </button>
            </div>

            <!-- DESKTOP CONTROLS -->
            <div 
              v-if="!isMobile"
              v-show="showControls || !isPlaying"
              class="absolute right-4 bottom-4 flex gap-2 z-10"
              :class="{ 'opacity-0': !showControls && isPlaying }"
            >
              <button @click="toggleFullscreen" class="btn btn-circle btn-sm bg-black/70 text-white">
                <i class="mdi mdi-fullscreen"></i>
              </button>
            </div>

          </div>

          <!-- EPISODE INFO DESKTOP -->
          <div v-if="!isMobile" class="p-4 bg-base-300">
            <h2 class="text-xl font-bold mt-2 truncate">
              {{ videoData.title }} - Episode {{ currentEpisode }}
            </h2>

            <div class="flex items-center gap-4 text-sm opacity-80 mt-2">
              <span class="flex items-center gap-1">
                <i class="mdi mdi-thumb-up"></i> {{ videoData.likes }}K
              </span>
              <span class="flex items-center gap-1">
                <i class="mdi mdi-eye"></i> {{ videoData.views }}M
              </span>
            </div>

            <p class="mt-4 text-sm">{{ currentEpisodeDetail?.synopsis }}</p>

            <div class="flex flex-wrap gap-2 mt-4">
              <div v-for="tag in videoData.tags" :key="tag" class="badge badge-outline">
                {{ tag }}
              </div>
            </div>

            <div class="flex items-center gap-4 mt-6">
              <button @click="togglePlay" class="btn btn-primary btn-lg">
                <i class="mdi mdi-play"></i>
                {{ watchButtonText }}
              </button>
            </div>
          </div>

        </div>

        <!-- SIDEBAR DESKTOP -->
        <div class="hidden lg:block w-1/4 bg-base-300 p-4 rounded-lg overflow-y-auto max-h-[calc(100vh-8rem)] custom-scrollbar">
          <h3 class="font-bold text-lg mb-4 sticky top-0 bg-base-300 z-10 pb-2">
            Episode ({{ currentEpisode }}/{{ videoData.episodeCount }})
          </h3>

          <div class="grid grid-cols-4 gap-2">
            <button 
              v-for="ep in videoData.episodes" 
              :key="ep.number"
              @click="playEpisode(ep.number)"
              :class="['btn btn-outline', { 'btn-primary': ep.number === currentEpisode }]"
            >
              {{ ep.number }}
            </button>
          </div>
        </div>

      </div>
    </div>

    <!-- MOBILE DRAWER EPISODE -->
    <dialog id="episode_drawer_player" class="modal modal-bottom">
      <div class="modal-box max-h-[80vh] bg-base-300 rounded-t-2xl">
        <div class="flex justify-between items-center pb-4 border-b border-base-content/10">
          <h3 class="font-bold text-lg">
            Episode ({{ currentEpisode }}/{{ videoData.episodeCount }})
          </h3>

          <form method="dialog">
            <button class="btn btn-sm btn-circle btn-ghost">
              <i class="mdi mdi-close text-xl"></i>
            </button>
          </form>
        </div>

        <div class="overflow-y-auto mt-4">
          <div class="grid grid-cols-5 gap-2">
            <button 
              v-for="ep in videoData.episodes.slice(0, showEpisodesInDrawer)" 
              :key="ep.number"
              @click="playEpisode(ep.number)"
              :class="['btn btn-outline', { 'btn-primary': ep.number === currentEpisode }]"
            >
              {{ ep.number }}
            </button>
          </div>

          <div v-if="showEpisodesInDrawer < videoData.episodes.length" class="text-center mt-4">
            <button @click="loadMoreEpisodesInDrawer" class="btn btn-ghost text-primary w-full">
              Lihat lebih banyak
            </button>
          </div>
        </div>

      </div>

      <form method="dialog" class="modal-backdrop">
        <button>close</button>
      </form>
    </dialog>

  </div>
</template>


<style scoped>
.custom-scrollbar::-webkit-scrollbar {
  width: 8px;
}
.custom-scrollbar::-webkit-scrollbar-thumb {
  background-color: #555;
  border-radius: 20px;
}
video {
  transition: all 0.3s ease;
}
</style>
