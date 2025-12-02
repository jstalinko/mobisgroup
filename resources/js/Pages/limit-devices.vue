<template>
  <div class="min-h-screen flex flex-col ">

    <Navbar />

    <div class="max-w-4xl mx-auto w-full px-4 py-8 flex-1">

      <!-- Alert -->
      <div 
        v-if="limitReached"
        class="mb-5 bg-red-100 text-red-700 px-4 py-3 rounded-lg border border-red-300"
      >
        Anda telah mencapai batas maksimal perangkat ({{ maxDevices }}).
        Silakan logout perangkat lain untuk melanjutkan.
      </div>

      <!-- Header with Logout All Button -->
      <div class="flex justify-between items-center mb-4">
        <h2 class="text-2xl font-bold">Perangkat Aktif</h2>
        
        <button
          v-if="devices.length > 0"
          @click="logoutAllDevices"
          class="px-4 py-2 text-sm bg-red-500 text-white rounded-lg hover:bg-red-600 transition"
        >
          Logout Semua
        </button>
      </div>

      <!-- Devices List -->
      <div class="space-y-3">

        <div 
          v-for="device in devices"
          :key="device.id"
          class=" rounded-lg shadow-sm p-4 border flex justify-between items-center"
        >
          <div>
            <p class="font-semibold">{{ device.device_name || 'Unknown Device' }}</p>
            <p class="text-xs text-gray-100">{{ device.user_agent }}</p>
            <p class="text-xs text-gray-100">
              Last active: {{ formatDate(device.last_active) }}
            </p>
          </div>

          <button
            v-if="!device.revoked"
            @click="logoutDevice(device.device_id)"
            class="px-3 py-1 text-sm bg-green-500 text-white rounded-lg hover:bg-red-700 transition"
          >
            Aktif
          </button>

          <span v-else class="text-xs text-red-500 font-medium">Tidak Aktif</span>
        </div>

      </div>

    </div>
    <MobileNav/>
    <Footer />

  </div>
</template>

<script setup>
import Navbar from './components/Navbar.vue'
import Footer from './components/Footer.vue'
import { router } from '@inertiajs/vue3'
import MobileNav from './components/MobileNav.vue'
import { logoutAllDevice } from '../utils/api'

const props = defineProps({
  devices: Array,
  maxDevices: Number,
  limitReached: Boolean
})

const logoutDevice = async (id) => {
  const resp = await logoutDevice(id);
  window.location.href ='/login';
}

const logoutAllDevices =async () => {
  if (confirm('Apakah Anda yakin ingin logout dari semua perangkat?')) {
     const resp = await logoutAllDevice();
  window.location.href ='/login';
  }
}

const formatDate = (date) => {
  return new Date(date).toLocaleString('id-ID', {
    day: '2-digit',
    month: 'short',
    year: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  })
}
</script>