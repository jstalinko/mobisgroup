<template>
  <!-- Mobile Bottom Navigation - iOS Style Floating -->
  <div class="fixed bottom-4 left-4 right-4 md:hidden z-50">
    <div class="
      backdrop-blur-xl bg-base-100/70 
      border border-base-300/50
      rounded-[20px] 
      shadow-2xl shadow-black/10
      px-2 py-1.5
      flex items-center justify-around
    ">

      <!-- Home -->
      <button :class="{ 'text-primary scale-110': activeMenu === 'home' }" @click="router.visit('/')"
        class="flex flex-col items-center gap-0.5 transition-all duration-200 hover:scale-105 active:scale-95 px-2 py-1">
        <span class="mdi mdi-home text-xl"></span>
        <span class="text-[9px] font-medium">Home</span>
      </button>

      <!-- Search -->
      <button :class="{ 'text-primary scale-110': activeMenu === 'search' }" @click="openSearchModal"
        class="flex flex-col items-center gap-0.5 transition-all duration-200 hover:scale-105 active:scale-95 px-2 py-1">
        <span class="mdi mdi-magnify text-xl"></span>
        <span class="text-[9px] font-medium">Cari</span>
      </button>

      <!-- PLATFORM (CENTER BUTTON) -->
      <div class="relative -mt-6">
        <div class="dropdown dropdown-top">
          <label tabindex="0" class="
              btn btn-circle
              bg-gradient-to-br from-primary to-secondary
              border-4 border-base-100
              shadow-xl shadow-primary/30
              hover:scale-105 active:scale-95
              transition-all duration-200
              w-14 h-14
            ">
            <span class="mdi mdi-movie text-xl text-white"></span>
          </label>
          <ul tabindex="0" class="
              dropdown-content menu
              backdrop-blur-xl bg-base-100/90
              rounded-[20px]
              w-44 p-2
              shadow-2xl shadow-black/20
              border border-base-300/50
              mb-3
            ">
            <li>
              <button @click="changePlatform('dramabox')"
                :class="{ 'bg-primary/20 text-primary': selectedPlatform === 'dramabox' }"
                class="rounded-[14px] transition-all duration-200 hover:scale-105">
                <span class="mdi mdi-drama-masks"></span>
                Dramabox
              </button>
            </li>

            <li>
              <button @click="changePlatform('netshort')"
                :class="{ 'bg-primary/20 text-primary': selectedPlatform === 'netshort' }"
                class="rounded-[14px] transition-all duration-200 hover:scale-105">
                <span class="mdi mdi-video-vintage"></span>
                Netshort
              </button>
            </li>

            <li>
              <button @click="changePlatform('dramave')"
                :class="{ 'bg-primary/20 text-primary': selectedPlatform === 'dramave' }"
                class="rounded-[14px] transition-all duration-200 hover:scale-105">
                <span class="mdi mdi-television-classic"></span>
                Dramave
              </button>
            </li>

            <li>
              <button @click="changePlatform('shortmax')"
                :class="{ 'bg-primary/20 text-primary': selectedPlatform === 'shortmax' }"
                class="rounded-[14px] transition-all duration-200 hover:scale-105">
                <span class="mdi mdi-filmstrip"></span>
                Shortmax
              </button>
            </li>
          </ul>
        </div>
      </div>

      <!-- Theme -->
      <button :class="{ 'text-primary scale-110': activeMenu === 'theme' }" @click="handleMenuClick('theme')"
        class="flex flex-col items-center gap-0.5 transition-all duration-200 hover:scale-105 active:scale-95 px-2 py-1">
        <span class="mdi mdi-palette text-xl"></span>
        <span class="text-[9px] font-medium">Theme</span>
      </button>

      <!-- Account Menu -->
      <button :class="{ 'text-primary scale-110': activeMenu == 'account' }"
        class="flex flex-col items-center gap-0.5 transition-all duration-200 hover:scale-105 active:scale-95 px-2 py-1"
        @click="router.visit('/login')"
        v-if="userData == null && subscriptionData == null">
        <span class="mdi mdi-login text-xl"></span>
        <span class="text-[9px] font-medium">LogIn</span>
      </button>
      <button v-else
        :class="{ 'text-primary scale-110': activeMenu === 'account' }" 
        @click="openAccountDrawer"
        class="flex flex-col items-center gap-0.5 transition-all duration-200 hover:scale-105 active:scale-95 px-2 py-1">
        <span class="mdi mdi-account text-xl"></span>
        <span class="text-[9px] font-medium">Akun</span>
      </button>

    </div>
  </div>

  <!-- SEARCH MODAL -->
  <input type="checkbox" id="search_modal" class="modal-toggle" v-model="showSearchModal" />
  <div class="modal" :class="{ 'modal-open': showSearchModal }">
    <div class="modal-box backdrop-blur-xl bg-base-100/80 rounded-[24px] border border-base-300/50">
      <h3 class="font-bold text-lg mb-4">Pencarian</h3>

      <input type="text" placeholder="Cari drama..." class="input input-bordered w-full rounded-[16px]"
        v-model="searchQuery" @keyup.enter="submitSearch" />

      <div class="modal-action">
        <button class="btn btn-primary rounded-[14px]" @click="submitSearch">Search</button>
        <button class="btn rounded-[14px]" @click="closeSearchModal">Close</button>
      </div>
    </div>
    <label class="modal-backdrop backdrop-blur-sm" @click="closeSearchModal"></label>
  </div>

  <!-- ACCOUNT DRAWER -->
  <div class="drawer drawer-end z-[60]">
    <input id="account_drawer" type="checkbox" class="drawer-toggle" v-model="showAccountDrawer" />
    <div class="drawer-side">
      <label for="account_drawer" class="drawer-overlay bg-black/30"></label>
      <div class="
        min-h-full w-80 
        backdrop-blur-xl bg-base-100/80
        border-l border-base-300/50
        shadow-2xl
        p-6
      ">
        <!-- Header -->
        <div class="flex items-center justify-between mb-8">
          <h2 class="text-2xl font-bold flex items-center gap-2">
            <span class="mdi mdi-account-circle text-primary"></span>
            Akun
          </h2>
          <button @click="closeAccountDrawer" class="btn btn-circle btn-ghost btn-sm">
            <span class="mdi mdi-close text-xl"></span>
          </button>
        </div>

        <!-- User Info -->
        <div class="mb-6 p-4 bg-base-200/50 rounded-[20px]">
          <div class="flex items-center gap-3">
            <div class="avatar placeholder">
              <div class="bg-primary text-primary-content rounded-full w-12">
                <span class="text-xl">{{ getUserInitial() }}</span>
              </div>
            </div>
            <div>
              <div class="font-semibold">{{ userData?.name || 'User' }}</div>
              <div class="text-xs opacity-70">{{ userData?.email || '-' }}</div>
            </div>
          </div>
        </div>

        <!-- Menu Items -->
        <ul class="menu p-0 gap-2">
          <li>
            <button @click="openProfileModal" 
              class="rounded-[16px] flex items-center gap-3 hover:bg-primary/10 transition-all duration-200">
              <span class="mdi mdi-account-details text-xl text-primary"></span>
              <span>Profile</span>
            </button>
          </li>
          
          <li>
            <button @click="goToReferral"
              class="rounded-[16px] flex items-center gap-3 hover:bg-primary/10 transition-all duration-200">
              <span class="mdi mdi-share-variant text-xl text-secondary"></span>
              <span>Referral</span>
            </button>
          </li>
          
          <li>
            <button @click="openContactModal"
              class="rounded-[16px] flex items-center gap-3 hover:bg-primary/10 transition-all duration-200">
              <span class="mdi mdi-phone text-xl text-accent"></span>
              <span>Hubungi Kami</span>
            </button>
          </li>
        </ul>

        <div class="divider"></div>

        <!-- Logout Button -->
        <button @click="logoutAction" 
          class="btn btn-error btn-block rounded-[16px] gap-2">
          <span class="mdi mdi-logout"></span>
          Logout
        </button>
      </div>
    </div>
  </div>

  <!-- PROFILE MODAL -->
  <input type="checkbox" id="profile_modal" class="modal-toggle" v-model="showProfileModal" />
  <div class="modal" :class="{ 'modal-open': showProfileModal }">
    <div class="modal-box max-w-2xl backdrop-blur-xl bg-base-100/80 rounded-[24px] border border-base-300/50">
      <h3 class="font-bold text-lg mb-4">Profile Information</h3>

      <div class="mb-6">
        <h4 class="font-semibold text-md mb-3 flex items-center gap-2">
          <span class="mdi mdi-account-circle"></span>
          User Details
        </h4>

        <div class="grid grid-cols-1 gap-3">
          <div class="bg-base-200/50 rounded-[16px] p-3 backdrop-blur-sm">
            <div class="text-sm opacity-70">License Key</div>
            <div class="font-mono text-sm">{{ userData?.license_key || '-' }}</div>
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
          <div class="bg-base-200/50 rounded-[16px] p-3 backdrop-blur-sm">
            <div class="text-sm opacity-70">Plan Name</div>
            <div class="font-medium">{{ subscriptionData?.plan_name || '-' }}</div>
          </div>

          <div class="bg-base-200/50 rounded-[16px] p-3 backdrop-blur-sm">
            <div class="text-sm opacity-70">Subscription Code</div>
            <div class="font-mono text-sm">{{ subscriptionData?.subscription_code || '-' }}</div>
          </div>

          <div class="bg-base-200/50 rounded-[16px] p-3 backdrop-blur-sm">
            <div class="text-sm opacity-70">Status</div>
            <span class="badge" :class="getStatusClass(subscriptionData?.status)">
              {{ subscriptionData?.status || '-' }}
            </span>
          </div>

          <div class="bg-base-200/50 rounded-[16px] p-3 backdrop-blur-sm">
            <div class="text-sm opacity-70">Start Date</div>
            <div class="font-medium">{{ formatDate(subscriptionData?.start_at) }}</div>
          </div>

          <div class="bg-base-200/50 rounded-[16px] p-3 backdrop-blur-sm">
            <div class="text-sm opacity-70">End Date</div>
            <div class="font-medium">{{ formatDate(subscriptionData?.end_at) }}</div>
          </div>
        </div>
      </div>

      <div class="modal-action">
        <button class="btn rounded-[14px]" @click="closeProfileModal">Close</button>
      </div>
    </div>
    <label class="modal-backdrop backdrop-blur-sm" @click="closeProfileModal"></label>
  </div>

  <!-- CONTACT MODAL -->
  <input type="checkbox" id="contact_modal" class="modal-toggle" v-model="showContactModal" />
  <div class="modal" :class="{ 'modal-open': showContactModal }">
    <div class="modal-box backdrop-blur-xl bg-base-100/95 rounded-[24px] border border-base-300/50">
      <h3 class="font-bold text-lg mb-4 flex items-center gap-2">
        <span class="mdi mdi-phone text-primary"></span>
        Hubungi Kami
      </h3>

      <div class="space-y-3">
        <a :href="'https://wa.me/'+noWhatsapp" target="_blank"
          class="flex items-center gap-3 p-4 bg-base-200/50 rounded-[16px] hover:bg-primary/10 transition-all duration-200">
          <span class="mdi mdi-whatsapp text-2xl text-success"></span>
          <div>
            <div class="font-semibold">WhatsApp</div>
            <div class="text-sm opacity-70">Chat dengan kami</div>
          </div>
        </a>

      </div>

      <div class="modal-action">
        <button class="btn rounded-[14px]" @click="closeContactModal">Close</button>
      </div>
    </div>
    <label class="modal-backdrop backdrop-blur-sm" @click="closeContactModal"></label>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { router } from '@inertiajs/vue3';
import { siteSetting, Storage } from '../../utils/helpers';

const activeMenu = ref('home');
const showSearchModal = ref(false);
const searchQuery = ref('');
const showAccountDrawer = ref(false);
const showProfileModal = ref(false);
const showContactModal = ref(false);
const userData = ref(null);
const subscriptionData = ref(null);
const selectedPlatform = ref(Storage.get('service_active_app_x') || 'dramabox');
const setting = siteSetting();
const noWhatsapp =  function (number) {
  // Hapus karakter non-digit
  let cleaned = ('' + number).replace(/\D/g, '');

  // Tambahkan kode negara jika belum ada (misal: +62 untuk Indonesia)
  if (cleaned.startsWith('0')) {
    cleaned = '62' + cleaned.substring(1);
  } else if (!cleaned.startsWith('62')) {
    cleaned = '62' + cleaned;
  }

  return cleaned;
}(setting.no_whatsapp_admin || '081234567890');


const changePlatform = (platform) => {
  const allowed = ['dramabox', 'netshort'];

  selectedPlatform.value = platform;

  // Simpan ke localStorage (Storage.set)
  Storage.set('service_active_app_x', platform);

  // Simpan ke cookie (agar dibaca PHP)
  document.cookie = "service_active_app_x=" + platform + "; path=/; max-age=31536000";

  if (!allowed.includes(platform)) {
    selectedPlatform.value = 'dramabox';

    // Save default fallback ke localStorage + cookie
    Storage.set('service_active_app_x', 'dramabox');
    document.cookie = "service_active_app_x=dramabox; path=/; max-age=31536000";

    router.visit(`/coming-soon/${platform}`);
  } else {
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

const submitSearch = () => {
  if (!searchQuery.value) return;
  router.visit(`/search?query=${encodeURIComponent(searchQuery.value)}`);
};

const handleMenuClick = (menu) => {
  activeMenu.value = menu;

  if (menu === 'theme') randomizeTheme();
};

const openAccountDrawer = () => {
  userData.value = Storage.get('mobis_user');
  subscriptionData.value = Storage.get('mobis_sub');
  showAccountDrawer.value = true;
  activeMenu.value = 'account';
};

const closeAccountDrawer = () => {
  showAccountDrawer.value = false;
  activeMenu.value = 'home';
};

const getUserInitial = () => {
  const username = userData.value?.username || 'U';
  return username.charAt(0).toUpperCase();
};

const openProfileModal = () => {
  showAccountDrawer.value = false;
  showProfileModal.value = true;
};

const closeProfileModal = () => {
  showProfileModal.value = false;
};

const goToReferral = () => {
  router.visit('/referral');
};

const openContactModal = () => {
  showAccountDrawer.value = false;
  showContactModal.value = true;
};

const closeContactModal = () => {
  showContactModal.value = false;
};

const logoutAction = () => {
  showAccountDrawer.value = false;
  showProfileModal.value = false;
  showContactModal.value = false;
  userData.value = null;
  subscriptionData.value = null;
  Storage.delete('mobis_user');
  Storage.delete('mobis_sub');
  Storage.delete('device_id');
  document.cookie = 'device_id=; path=/; max-age=0';
  Storage.delete('pwa-install-dismissed');
  router.visit('/logout');
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

const themes = ['light', 'dark', 'cupcake', 'emerald', 'retro', 'cyberpunk', 'night', 'coffee', 'winter'];
const randomizeTheme = () => {
  const theme = themes[Math.floor(Math.random() * themes.length)];
  document.documentElement.setAttribute('data-theme', theme);
  localStorage.setItem('theme', theme);
};

onMounted(() => {
  // Load saved theme
  const savedTheme = localStorage.getItem('theme');
  if (savedTheme) {
    document.documentElement.setAttribute('data-theme', savedTheme);
  }

  // Load user data if available
  userData.value = Storage.get('mobis_user') || null;
  subscriptionData.value = Storage.get('mobis_sub') || null;
});
</script>