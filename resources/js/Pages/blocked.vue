<template>
  <div class="min-h-screen flex flex-col ">

    <Navbar />

    <!-- CONTENT -->
    <div class="flex-1 flex items-center justify-center px-6 py-10">

      <div class=" shadow-md rounded-2xl p-8 max-w-md w-full text-center border">

        <div class="text-red-600 text-5xl mb-4">
          <span class="mdi mdi-lock-open-alert mx-auto text-4xl text-yellow-500"></span>
        </div>

        <h1 class="text-2xl font-bold text-gray-100 mb-2">
          Perangkat Terblokir
        </h1>

        <p class="text-gray-200 mb-6">
          Perangkat telah kadaluwarsa / ter-blokir.  
          Silakan login ulang untuk melanjutkan.
        </p>

        <button
          @click="logoutAction"
          class="w-full bg-red-600 hover:bg-red-700 text-white py-3 px-5 rounded-xl 
                 font-semibold shadow transition-colors"
        >
          Logout & Bersihkan Device
        </button>

      </div>

    </div>

    <MobileNav />

    <Footer />

  </div>
</template>

<script setup>
import Navbar from './components/Navbar.vue'
import MobileNav from './components/MobileNav.vue'
import Footer from './components/Footer.vue'

import { router } from '@inertiajs/vue3'
import { ref } from 'vue'
import { Storage } from '../utils/helpers'

const userData = ref(null)
const subscriptionData = ref(null)

const logoutAction = () => {
  userData.value = null
  subscriptionData.value = null

  Storage.delete('mobis_user')
  Storage.delete('mobis_sub')
  Storage.delete('device_id')
  Storage.delete('pwa-install-dismissed')

  document.cookie = 'device_id=; path=/; max-age=0'

  router.visit('/logout')
}
</script>
