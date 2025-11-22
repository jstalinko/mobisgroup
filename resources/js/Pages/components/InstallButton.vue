<template>
  <transition name="fade">
    <div 
      v-if="show" 
      class="fixed inset-0 bg-black bg-opacity-40 flex items-center justify-center z-[9999]"
    >
      <div class="bg-zinc-900 text-white rounded-xl w-80 p-6 shadow-xl text-center relative animate-scale">
        
        <!-- Icon -->
        <img 
          src="/icons/web-app-manifest-192x192.png"
          alt="App Icon"
          class="w-20 h-20 mx-auto mb-4 rounded-xl shadow"
        />

        <!-- Title -->
        <h2 class="text-xl font-bold mb-2">Install Aplikasi</h2>
        <p class="text-gray-200 text-sm mb-6">
          Pasang aplikasi ini agar lebih cepat diakses dari layar utama.
        </p>

        <!-- Buttons -->
        <div class="flex gap-3 mt-2">
          <button 
            @click="close"
            class="flex-1 py-2 rounded-lg border border-gray-300 text-gray-100"
          >
            Nanti Saja
          </button>

          <button 
            @click="install"
            class="flex-1 py-2 bg-blue-600 text-white rounded-lg"
          >
            Install
          </button>
        </div>
      </div>
    </div>
  </transition>
</template>

<script setup>
import { ref, onMounted } from "vue"
import { Storage } from "../../utils/helpers"

const show = ref(false)
let deferredPrompt = null

onMounted(() => {
  window.addEventListener("pwa-install-ready", () => {
    deferredPrompt = window.deferredPWA
    if (!Storage.get("pwa-install-dismissed")) {
      show.value = true
    }
  })
})

const install = async () => {
  if (!deferredPrompt) return

  deferredPrompt.prompt()
  const result = await deferredPrompt.userChoice

  show.value = false
  deferredPrompt = null
}

const close = () => {
  show.value = false
  Storage.set("pwa-install-dismissed", "true");
}
</script>

<style scoped>
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.2s ease;
}
.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}
.animate-scale {
  animation: scaleIn .25s ease;
}
@keyframes scaleIn {
  from {
    transform: scale(0.8);
    opacity: 0;
  }
  to {
    transform: scale(1);
    opacity: 1;
  }
}
</style>
