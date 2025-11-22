<template>
  <div class="card bg-base-100 shadow-md hover:shadow-lg border transition rounded-xl">

    <div class="card-body">

      <!-- Header -->
      <div class="flex justify-between items-center mb-3">
        <h2 class="card-title text-lg font-bold">
          {{ items.name }}
        </h2>

        <div 
          class="badge px-3 py-2 text-xs"
          :class="items.active ==1 ? 'badge-success' : 'badge-error'"
        >
          {{ items.active==1 ? 'Ready' : 'Sold' }}
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
      <p v-if="items.description" class=" text-sm mb-4">
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
          :title="'Masa aktif satu '+(items.duration === 'year' ? 'tahun' : 'bulan')"
          :active="true"
        />
          <FeatureLine 
          title="Full Support 7/24"
          :active="true"
        />
      </div>

      <!-- Button -->
      <div class="card-actions">
        <button class="btn btn-primary w-full" @click="redirect(checkout_url+'%20%20: '+items.name)">
          Pilih Paket
        </button>
      </div>

    </div>

  </div>
</template>

<script setup>
import FeatureLine from './PlanFeatureLine.vue';


defineProps({
  items: Object,
  checkout_url: String
})

const redirect = (url) => {
  window.location.href = url;
};

 const formatNumber = (number) => {
  return new Intl.NumberFormat('id-ID').format(number)
}
</script>
