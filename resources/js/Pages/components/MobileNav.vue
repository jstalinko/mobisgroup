<template>
  <!-- Mobile Bottom Navigation -->
  <div class="btm-nav md:hidden z-50 bg-base-100 border-t border-base-300">

    <!-- Home -->
    <button :class="{'active': activeMenu === 'home'}" @click="router.visit('/')">
      <span class="mdi mdi-home text-xl"></span>
      <span class="btm-nav-label text-xs">Home</span>
    </button>

    <!-- Search (open modal) -->
    <button :class="{'active': activeMenu === 'search'}" @click="openSearchModal">
      <span class="mdi mdi-magnify text-xl"></span>
      <span class="btm-nav-label text-xs">Cari</span>
    </button>

    <!-- PLATFORM (DROP-UP CENTER BUTTON) -->
    <div class="dropdown dropdown-top dropdown-center">
      <label tabindex="0" class="btn btn-circle btn-primary">
        <span class="mdi mdi-movie text-xl"></span>
      </label>
      <ul tabindex="0" class="dropdown-content menu bg-base-100 rounded-box w-40 shadow">
       <li>
  <button 
    @click="changePlatform('dramabox')" 
    :class="{ active: selectedPlatform === 'dramabox' }"
  >Dramabox</button>
</li>

<li>
  <button 
    @click="changePlatform('netshort')" 
    :class="{ active: selectedPlatform === 'netshort' }"
  >Netshort</button>
</li>

<li>
  <button 
    @click="changePlatform('dramave')" 
    :class="{ active: selectedPlatform === 'dramave' }"
  >Dramave</button>
</li>

<li>
  <button 
    @click="changePlatform('shortmax')" 
    :class="{ active: selectedPlatform === 'shortmax' }"
  >Shortmax</button>
</li>

      </ul>
    </div>

    <!-- Theme -->
    <button :class="{'active': activeMenu === 'theme'}" @click="handleMenuClick('theme')">
      <span class="mdi mdi-palette text-xl"></span>
      <span class="btm-nav-label text-xs">Theme</span>
    </button>

    <!-- Profile -->
    <button :class="{'active': activeMenu === 'profile'}" @click="handleMenuClick('profile')">
      <span class="mdi mdi-account text-xl"></span>
      <span class="btm-nav-label text-xs">Profile</span>
    </button>

  </div>

  <!-- SEARCH MODAL -->
  <input type="checkbox" id="search_modal" class="modal-toggle" v-model="showSearchModal" />
  <div class="modal" :class="{ 'modal-open': showSearchModal }">
    <div class="modal-box">
      <h3 class="font-bold text-lg mb-4">Pencarian</h3>

      <input 
        type="text" 
        placeholder="Cari mobil..."
        class="input input-bordered w-full"
        v-model="searchQuery"
        @keyup.enter="submitSearch"
      />

      <div class="modal-action">
        <button class="btn btn-primary" @click="submitSearch">Search</button>
        <button class="btn" @click="closeSearchModal">Close</button>
      </div>
    </div>
    <label class="modal-backdrop" @click="closeSearchModal"></label>
  </div>

  <!-- PROFILE MODAL -->
  <input type="checkbox" id="profile_modal" class="modal-toggle" v-model="showProfileModal" />
  <div class="modal" :class="{ 'modal-open': showProfileModal }">
    <div class="modal-box max-w-2xl">
      <h3 class="font-bold text-lg mb-4">Profile Information</h3>

      <div class="mb-6">
        <h4 class="font-semibold text-md mb-3 flex items-center gap-2">
          <span class="mdi mdi-account-circle"></span>
          User Details
        </h4>

        <div class="grid grid-cols-1 gap-3">
          <div>
            <span class="text-sm opacity-70">Username</span>
            <span class="font-medium">{{ userData?.username || '-' }}</span>
          </div>

          <div>
            <span class="text-sm opacity-70">Email</span>
            <span class="font-medium">{{ userData?.email || '-' }}</span>
          </div>

          <div>
            <span class="text-sm opacity-70">License Key</span>
            <span class="font-mono text-sm">{{ userData?.license_key || '-' }}</span>
          </div>
        </div>
      </div>

      <div class="divider"></div>

      <div class="mb-4">
        <h4 class="font-semibold text-md mb-3 flex items-center gap-2">
          <span class="mdi mdi-card-account-details"></span>
          Subscription Details
        </h4>

        <div class="grid grid-cols-1 gap-3">
          <div>
            <span class="text-sm opacity-70">Plan Name</span>
            <span class="font-medium">{{ subscriptionData?.plan_name || '-' }}</span>
          </div>

          <div>
            <span class="text-sm opacity-70">Subscription Code</span>
            <span class="font-mono text-sm">{{ subscriptionData?.subscription_code || '-' }}</span>
          </div>

          <div>
            <span class="text-sm opacity-70">Status</span>
            <span class="badge" :class="getStatusClass(subscriptionData?.status)">
              {{ subscriptionData?.status || '-' }}
            </span>
          </div>

          <div>
            <span class="text-sm opacity-70">Start Date</span>
            <span class="font-medium">{{ formatDate(subscriptionData?.start_at) }}</span>
          </div>

          <div>
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
import { ref } from 'vue';
import { router } from '@inertiajs/vue3';
import { Storage } from '../../utils/helpers';

const activeMenu = ref('home');
const showSearchModal = ref(false);
const searchQuery = ref('');
const showProfileModal = ref(false);
const userData = ref(null);
const subscriptionData = ref(null);
const selectedPlatform = ref(Storage.get('service_active_app_x') || 'dramabox');

const changePlatform = (platform) => {
  const allowed = ['dramabox', 'netshort'];

  selectedPlatform.value = platform;

  if (!allowed.includes(platform)) {
    // jika platform tidak valid → set ke dramabox
    selectedPlatform.value = 'dramabox';
    Storage.set('service_active_app_x', 'dramabox');
    router.visit(`/coming-soon/${platform}`);
  } else {
    // platform valid
    Storage.set('service_active_app_x', platform);
    window.location.reload();
  }
};

const openSearchModal = () => {
  showSearchModal.value = true;
  activeMenu.value = 'search';
};

const closeSearchModal = () => {
  showSearchModal.value = false;
  activeMenu.value = 'home';
  searchQuery.value = '';
};

// submit to router.visit
const submitSearch = () => {
  if (!searchQuery.value) return;
  router.visit(`/search?query=${encodeURIComponent(searchQuery.value)}`);
};

const handleMenuClick = (menu) => {
  activeMenu.value = menu;

  if (menu === 'profile') openProfileModal();
  if (menu === 'theme') randomizeTheme();
};


// PROFILE
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
  return date.toLocaleDateString('id-ID', { year: 'numeric', month: 'long', day: 'numeric' });
};

const getStatusClass = (status) => {
  const map = {
    active: 'badge-success',
    inactive: 'badge-error',
    pending: 'badge-warning',
    expired: 'badge-error'
  };
  return map[status?.toLowerCase()] || 'badge-ghost';
};

// THEME
const themes = ['light','dark','cupcake','emerald','retro','cyberpunk','night','coffee','winter'];
const randomizeTheme = () => {
  const theme = themes[Math.floor(Math.random() * themes.length)];
  document.documentElement.setAttribute('data-theme', theme);
  localStorage.setItem('theme', theme);
};
</script>
