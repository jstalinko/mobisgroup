<template>
  <div>
    <!-- Card -->
    <div class="card bg-base-100 shadow-md hover:shadow-lg border transition rounded-xl">
      <div class="card-body">
        <!-- Header -->
        <div class="flex justify-between items-center mb-3">
          <h2 class="card-title text-lg font-bold">
            {{ items.name }}
          </h2>

          <div 
            class="badge px-3 py-2 text-xs"
            :class="items.active == 1 ? 'badge-success' : 'badge-error'"
          >
            {{ items.active == 1 ? 'Ready' : 'Sold' }}
          </div>
        </div>

        <!-- Price -->
        <p class="text-3xl font-extrabold text-primary mb-2">
          Rp {{ formatNumber(items.price) }}
          <span class="text-sm font-medium text-gray-100">
            / {{ items.duration === 'year' ? 'tahun' : 'bulan' }}
          </span>
        </p>

        <!-- Description -->
        <p v-if="items.description" class="text-sm mb-4">
          {{ items.description }}
        </p>

        <!-- Features -->
        <div class="space-y-2 mb-4">
          <FeatureLine 
            title="Dramabox Access"
            :active="items.feature_dramabox"
          />

          <FeatureLine 
            title="Netshort Access"
            :active="items.feature_netshort"
          />
          <FeatureLine 
            :title="items.max_devices + ' Max Devices'"
            :active="true"
          />
          <FeatureLine 
            :title="'Masa aktif satu ' + (items.duration === 'year' ? 'tahun' : 'bulan')"
            :active="true"
          />
          <FeatureLine 
            title="Full Support 7/24"
            :active="true"
          />
        </div>

        <!-- Button -->
        <div class="card-actions">
          <button class="btn btn-primary w-full" @click="showModal = true">
            Pilih Paket
          </button>
        </div>
      </div>
    </div>

    <!-- Payment Method Modal -->
    <div v-if="showModal" class="modal modal-open">
      <div class="modal-box max-w-md">
        <h3 class="font-bold text-lg mb-4">Pilih Metode Pembayaran</h3>
        
        <div class="space-y-3">
          <!-- Loop through payment methods -->
          <div 
            v-for="(methods, type) in paymentMethod" 
            :key="type"
            class="border rounded-lg p-4 hover:border-primary cursor-pointer transition"
            @click="selectPaymentMethod(type, methods[0])"
          >
            <div class="flex items-center justify-between">
              <div class="flex items-center gap-3">
                <!-- Icon based on payment type -->
                <div class="w-10 h-10 rounded-full bg-primary/10 flex items-center justify-center">
                  <svg v-if="type === 'qris'" class="w-6 h-6 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z" />
                  </svg>
                  <svg v-else-if="type === 'bank'" class="w-6 h-6 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                  </svg>
                  <svg v-else class="w-6 h-6 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                  </svg>
                </div>
                
                <div>
                  <p class="font-semibold capitalize">{{ formatPaymentType(type) }}</p>
                  <p class="text-xs text-gray-500">{{ methods.length }} metode tersedia</p>
                </div>
              </div>

              <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
              </svg>
            </div>
          </div>
        </div>

        <div class="modal-action">
          <button class="btn btn-ghost" @click="showModal = false">Batal</button>
        </div>
      </div>
      <div class="modal-backdrop" @click="showModal = false"></div>
    </div>
  </div>
  <Loading :show="isLoading"/>
</template>

<script setup>
import { ref } from 'vue';
import Loading from '../components/Loading.vue';
import FeatureLine from './PlanFeatureLine.vue';
import { http } from '../../utils/api';
const isLoading = ref(false);
const props = defineProps({
  items: Object,
  checkout_url: String,
  paymentMethod: Object
});

const showModal = ref(false);

const formatNumber = (number) => {
  return new Intl.NumberFormat('id-ID').format(number);
};

const formatPaymentType = (type) => {
  const typeMap = {
    'qris': 'QRIS',
    'bank': 'Bank Transfer',
    'ewallet': 'E-Wallet',
    'e-wallet': 'E-Wallet'
  };
  return typeMap[type.toLowerCase()] || type;
};

const selectPaymentMethod = async (type, method) => {
  
  isLoading.value = true;
  showModal.value = false;
  
  // Call checkout API with payment method
  const response = await http('/api/checkout',{
    method: 'POST',
    body:{
    plan_id: props.items.id,
    payment_type: type,
    payment_method: type
    }
  });
  if(response.success)
  {
    window.location.href = response.redirect;
  }else{
    alert('Gagal memproses pembayaran. Silakan coba lagi.');
  }
  isLoading.value = false;
};
</script>