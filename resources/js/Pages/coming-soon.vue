<script setup>
import { ref, onMounted } from 'vue'
import { Head, Link } from '@inertiajs/vue3'

const props = defineProps({
  service: {
    type: String,
    required: true
  }
})

// Platform info
const platformInfo = {
  'netshort': {
    name: 'NetShort',
    icon: 'mdi-netflix',
    color: 'text-red-500',
    gradient: 'from-red-500 to-pink-500'
  },
  'dramawave': {
    name: 'DramaWave',
    icon: 'mdi-waves',
    color: 'text-blue-500',
    gradient: 'from-blue-500 to-cyan-500'
  },
  'shortmax': {
    name: 'ShortMax',
    icon: 'mdi-play-box-multiple',
    color: 'text-purple-500',
    gradient: 'from-purple-500 to-pink-500'
  }
}
console.log(props)
const currentPlatform = platformInfo[props.service.toLowerCase()] || {
  name: props.service,
  icon: 'mdi-movie-open',
  color: 'text-primary',
  gradient: 'from-primary to-secondary'
}

// Animation
const show = ref(false)

onMounted(() => {
  setTimeout(() => {
    show.value = true
  }, 100)
})
</script>

<template>
  <Head :title="`${currentPlatform.name} - Coming Soon`" />
  
  <div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-base-200 to-base-300 p-4">
    <div 
      class="max-w-2xl w-full text-center transition-all duration-1000 transform"
      :class="show ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-10'"
    >
      <!-- Icon -->
      <div class="mb-8 animate-bounce">
        <span 
          :class="['mdi', currentPlatform.icon, currentPlatform.color]"
          class="text-9xl drop-shadow-lg"
        ></span>
      </div>

      <!-- Title -->
      <h1 
        class="text-5xl md:text-7xl font-bold mb-4 bg-gradient-to-r bg-clip-text text-transparent"
        :class="currentPlatform.gradient"
      >
        {{ currentPlatform.name }}
      </h1>

      <!-- Coming Soon Text -->
      <div class="mb-8">
        <h2 class="text-3xl md:text-4xl font-bold mb-4">
          Coming Soon
        </h2>
        <p class="text-lg text-base-content/70 mb-2">
          We're working hard to bring you the best experience
        </p>
        <p class="text-base text-base-content/60">
          Stay tuned for exciting updates!
        </p>
      </div>

      <!-- Features Preview -->
      <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
        <div class="card bg-base-100 shadow-lg">
          <div class="card-body items-center text-center p-6">
            <span class="mdi mdi-video-box text-4xl text-primary mb-2"></span>
            <h3 class="font-bold">HD Quality</h3>
            <p class="text-sm text-base-content/70">Crystal clear streaming</p>
          </div>
        </div>
        
        <div class="card bg-base-100 shadow-lg">
          <div class="card-body items-center text-center p-6">
            <span class="mdi mdi-lightning-bolt text-4xl text-warning mb-2"></span>
            <h3 class="font-bold">Fast Loading</h3>
            <p class="text-sm text-base-content/70">Instant playback</p>
          </div>
        </div>
        
        <div class="card bg-base-100 shadow-lg">
          <div class="card-body items-center text-center p-6">
            <span class="mdi mdi-star text-4xl text-success mb-2"></span>
            <h3 class="font-bold">Premium Content</h3>
            <p class="text-sm text-base-content/70">Exclusive shows</p>
          </div>
        </div>
      </div>
      <!-- Back Button -->
      <Link 
        href="/" 
        class="btn btn-outline btn-lg gap-2"
      >
        <span class="mdi mdi-arrow-left"></span>
        Back to DramaBox
      </Link>

      <!-- Social Media -->
      <div class="mt-8 flex justify-center gap-4">
        <a href="#" class="btn btn-circle btn-ghost">
          <span class="mdi mdi-facebook text-2xl"></span>
        </a>
        <a href="#" class="btn btn-circle btn-ghost">
          <span class="mdi mdi-twitter text-2xl"></span>
        </a>
        <a href="#" class="btn btn-circle btn-ghost">
          <span class="mdi mdi-instagram text-2xl"></span>
        </a>
        <a href="#" class="btn btn-circle btn-ghost">
          <span class="mdi mdi-youtube text-2xl"></span>
        </a>
      </div>
    </div>
  </div>
</template>

<style scoped>
@keyframes bounce {
  0%, 100% {
    transform: translateY(0);
  }
  50% {
    transform: translateY(-20px);
  }
}

.animate-bounce {
  animation: bounce 2s infinite;
}
</style>