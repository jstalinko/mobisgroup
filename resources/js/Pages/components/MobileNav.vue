<template>
  <!-- Mobile Bottom Navigation -->
  <div class="btm-nav btm-nav md:hidden z-50 bg-base-100 border-t border-base-300">
    <button :class="{'active': activeMenu === 'home'}" @click="handleMenuClick('home')">
      <span class="mdi mdi-home text-xl"></span>
      <span class="btm-nav-label text-xs">Home</span>
    </button>
    
    <button :class="{'active': activeMenu === 'search'}" @click="handleMenuClick('search')">
      <span class="mdi mdi-magnify text-xl"></span>
      <span class="btm-nav-label text-xs">Cari</span>
    </button>
    
    <button :class="{'active': activeMenu === 'theme'}" @click="handleMenuClick('theme')">
      <span class="mdi mdi-palette text-xl"></span>
      <span class="btm-nav-label text-xs">Theme</span>
    </button>
    
    <button :class="{'active': activeMenu === 'profile'}" @click="handleMenuClick('profile')">
      <span class="mdi mdi-account text-xl"></span>
      <span class="btm-nav-label text-xs">Profile</span>
    </button>
  </div>

  <!-- Full Screen Search -->
  <div v-if="showSearch" class="fixed inset-0 z-[100] bg-base-100 flex flex-col">
    <!-- Search Header -->
    <div class="navbar bg-base-100 border-b border-base-300">
      <div class="flex-1">
        <button class="btn btn-ghost btn-circle" @click="closeSearch">
          <span class="mdi mdi-arrow-left text-xl"></span>
        </button>
      </div>
      <div class="flex-none">
        <h2 class="text-lg font-semibold">Pencarian</h2>
      </div>
      <div class="flex-1"></div>
    </div>

    <!-- Search Form -->
    <div class="p-4">
      <div class="form-control">
        <div class="input-group">
          <input 
            type="text" 
            placeholder="Cari mobil..." 
            class="input input-bordered w-full"
            v-model="searchQuery"
            @keyup.enter="performSearch"
            ref="searchInput"
          />
          
        </div>
      </div>
    </div>

    <!-- Search Results -->
    <div class="flex-1 overflow-y-auto p-4">
      <div v-if="searchQuery && searchResults.length > 0" class="space-y-2">
        <div v-for="result in searchResults" :key="result.id" class="card bg-base-200 shadow-sm">
          <div class="card-body p-4">
            <h3 class="card-title text-sm">{{ result.title }}</h3>
            <p class="text-xs opacity-70">{{ result.description }}</p>
          </div>
        </div>
      </div>
      <div v-else-if="searchQuery" class="text-center py-8 opacity-50">
        <span class="mdi mdi-magnify text-4xl block mb-2"></span>
        <p>Tidak ada hasil ditemukan</p>
      </div>
      <div v-else class="text-center py-8 opacity-50">
        <span class="mdi mdi-magnify text-4xl block mb-2"></span>
        <p>Ketik untuk mencari...</p>
      </div>
    </div>
  </div>

  <!-- Profile Modal -->
  <input type="checkbox" id="profile_modal" class="modal-toggle" v-model="showProfileModal" />
  <div class="modal" :class="{ 'modal-open': showProfileModal }">
    <div class="modal-box max-w-2xl">
      <h3 class="font-bold text-lg mb-4">Profile Information</h3>
      
      <!-- User Information -->
      <div class="mb-6">
        <h4 class="font-semibold text-md mb-3 flex items-center gap-2">
          <span class="mdi mdi-account-circle"></span>
          User Details
        </h4>
        <div class="grid grid-cols-1 gap-3">
          <div class="flex flex-col">
            <span class="text-sm opacity-70">Username</span>
            <span class="font-medium">{{ userData?.username || '-' }}</span>
          </div>
          <div class="flex flex-col">
            <span class="text-sm opacity-70">Email</span>
            <span class="font-medium">{{ userData?.email || '-' }}</span>
          </div>
          <div class="flex flex-col">
            <span class="text-sm opacity-70">License Key</span>
            <span class="font-mono text-sm">{{ userData?.license_key || '-' }}</span>
          </div>
        </div>
      </div>

      <div class="divider"></div>

      <!-- Subscription Information -->
      <div class="mb-4">
        <h4 class="font-semibold text-md mb-3 flex items-center gap-2">
          <span class="mdi mdi-card-account-details"></span>
          Subscription Details
        </h4>
        <div class="grid grid-cols-1 gap-3">
          <div class="flex flex-col">
            <span class="text-sm opacity-70">Plan Name</span>
            <span class="font-medium">{{ subscriptionData?.plan_name || '-' }}</span>
          </div>
          <div class="flex flex-col">
            <span class="text-sm opacity-70">Subscription Code</span>
            <span class="font-mono text-sm">{{ subscriptionData?.subscription_code || '-' }}</span>
          </div>
          <div class="flex flex-col">
            <span class="text-sm opacity-70">Status</span>
            <span class="badge" :class="getStatusClass(subscriptionData?.status)">
              {{ subscriptionData?.status || '-' }}
            </span>
          </div>
          <div class="flex flex-col">
            <span class="text-sm opacity-70">Start Date</span>
            <span class="font-medium">{{ formatDate(subscriptionData?.start_at) }}</span>
          </div>
          <div class="flex flex-col">
            <span class="text-sm opacity-70">End Date</span>
            <span class="font-medium">{{ formatDate(subscriptionData?.end_at) }}</span>
          </div>
        </div>
      </div>

      <div class="modal-action">
        <button class="btn" @click="closeProfileModal">Close</button>
      </div>
    </div>
    <label class="modal-backdrop" @click="closeProfileModal"></label>
  </div>
</template>

<script setup>
import { ref, nextTick } from 'vue';
import { Storage } from '../../utils/helpers';

const activeMenu = ref('home');
const showSearch = ref(false);
const showProfileModal = ref(false);
const searchQuery = ref('');
const searchResults = ref([]);
const searchInput = ref(null);
const userData = ref(null);
const subscriptionData = ref(null);

// Daftar tema DaisyUI
const themes = [
  'light', 'dark', 'cupcake', 'bumblebee', 'emerald', 'corporate',
  'synthwave', 'retro', 'cyberpunk', 'valentine', 'halloween', 'garden',
  'forest', 'aqua', 'lofi', 'pastel', 'fantasy', 'wireframe', 'black',
  'luxury', 'dracula', 'cmyk', 'autumn', 'business', 'acid', 'lemonade',
  'night', 'coffee', 'winter', 'dim', 'nord', 'sunset'
];

const handleMenuClick = (menu) => {
  activeMenu.value = menu;
  
  if (menu === 'search') {
    openSearch();
  } else if (menu === 'profile') {
    openProfileModal();
  } else if (menu === 'theme') {
    randomizeTheme();
  }
};

// Search Functions
const openSearch = () => {
  showSearch.value = true;
  nextTick(() => {
    searchInput.value?.focus();
  });
};

const closeSearch = () => {
  showSearch.value = false;
  searchQuery.value = '';
  searchResults.value = [];
  activeMenu.value = 'home';
};

const performSearch = () => {
  // Implementasi pencarian Anda di sini
  // Contoh dummy data:
  if (searchQuery.value) {
    searchResults.value = [
      { id: 1, title: 'Hasil 1', description: 'Deskripsi hasil pencarian 1' },
      { id: 2, title: 'Hasil 2', description: 'Deskripsi hasil pencarian 2' },
    ];
  } else {
    searchResults.value = [];
  }
};

// Profile Modal Functions
const openProfileModal = () => {
  userData.value = Storage.get('mobis_user');
  subscriptionData.value = Storage.get('mobis_sub');
  showProfileModal.value = true;
};

const closeProfileModal = () => {
  showProfileModal.value = false;
  activeMenu.value = 'home';
};

const formatDate = (dateString) => {
  if (!dateString) return '-';
  const date = new Date(dateString);
  return date.toLocaleDateString('id-ID', {
    year: 'numeric',
    month: 'long',
    day: 'numeric'
  });
};

const getStatusClass = (status) => {
  const statusMap = {
    'active': 'badge-success',
    'inactive': 'badge-error',
    'pending': 'badge-warning',
    'expired': 'badge-error'
  };
  return statusMap[status?.toLowerCase()] || 'badge-ghost';
};

// Theme Functions
const randomizeTheme = () => {
  const randomTheme = themes[Math.floor(Math.random() * themes.length)];
  document.documentElement.setAttribute('data-theme', randomTheme);
  localStorage.setItem('theme', randomTheme);
  activeMenu.value = 'home';
};
</script>