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

      <h2 class="text-2xl font-bold mb-4">Perangkat Aktif</h2>

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
            class="px-3 py-1 text-sm bg-green-500 text-white rounded-lg hover:bg-red-700"
          >
            Aktif
          </button>

          <span v-else class="text-xs text-red-500 font-medium">Tidak Aktif</span>
        </div>

      </div>

    </div>

    <Footer />

  </div>
</template>

<script setup>
import Navbar from './components/Navbar.vue'
import Footer from './components/Footer.vue'
import { router } from '@inertiajs/vue3'

const props = defineProps({
  devices: Array,
  maxDevices: Number,
  limitReached: Boolean
})

const logoutDevice = (id) => {
  router.post(`/devices/logout/${id}`, {}, {
    preserveScroll: true,
    onSuccess: () => console.log('Device logged out')
  })
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
