<template>
  <div class="navbar bg-base-100 shadow-lg sticky top-0 z-50 hidden md:block">
    <div class="container mx-auto px-2">
      <div class="flex-1">
        <Link class="btn btn-ghost normal-case text-lg md:text-xl font-bold" href="/">
          <span class="text-primary">{{ setting.site_name }}</span>
        </Link>
      </div>
      <div class="flex-none gap-2">
        <!-- Search Button -->
        <button class="btn btn-ghost btn-circle">
          <span class="mdi mdi-magnify text-xl"></span>
        </button>

        <!-- Theme Randomizer -->
        <div class="dropdown dropdown-end">
          <label tabindex="0" class="btn btn-ghost btn-circle">
            <span class="mdi mdi-palette text-xl"></span>
          </label>
          <ul tabindex="0"
            class="dropdown-content z-[1] menu p-2 shadow-lg bg-base-100 rounded-box w-64 max-h-96 overflow-y-auto">
            <li class="menu-title">
              <span class="flex items-center gap-2">
                <span class="mdi mdi-theme-light-dark"></span>
                Choose Theme
              </span>
            </li>
            <li>
              <button @click="randomTheme" class="gap-2 justify-between">
                <span class="flex items-center gap-2">
                  <span class="mdi mdi-shuffle-variant"></span>
                  Random Theme
                </span>
                <span class="badge badge-primary badge-sm">Click me!</span>
              </button>
            </li>
            <div class="divider my-1"></div>
            <li v-for="theme in themes" :key="theme.value">
              <button @click="setTheme(theme.value)" :class="{ 'active': currentTheme === theme.value }"
                class="gap-2 justify-between">
                <span class="flex items-center gap-2">
                  <span :class="theme.icon"></span>
                  {{ theme.label }}
                </span>
                <span v-if="currentTheme === theme.value" class="mdi mdi-check text-success"></span>
              </button>
            </li>
          </ul>
        </div>

        <!-- User Avatar -->
        <div class="dropdown dropdown-end">
    <div tabindex="0" role="button" class="btn btn-ghost gap-2">
      <span class="mdi mdi-movie-open text-xl"></span>
      <span>{{ selectedPlatform }}</span>
      <span class="mdi mdi-chevron-down"></span>
    </div>
    <ul tabindex="0" class="dropdown-content menu bg-base-100 rounded-box z-[1] w-52 p-2 shadow">
      <li>
        <a 
          @click="changePlatform('DramaBox')" 
          :class="{ 'active': selectedPlatform === 'DramaBox' }"
        >
          <span class="mdi mdi-television-play"></span>
          DramaBox
        </a>
      </li>
      <li>
        <a 
          @click="changePlatform('NetShort')" 
          :class="{ 'active': selectedPlatform === 'NetShort' }"
        >
          <span class="mdi mdi-netflix"></span>
          NetShort
        </a>
      </li>
      <li>
        <a 
          @click="changePlatform('DramaWave')" 
          :class="{ 'active': selectedPlatform === 'DramaWave' }"
        >
          <span class="mdi mdi-waves"></span>
          DramaWave
        </a>
      </li>
      <li>
        <a 
          @click="changePlatform('ShortMax')" 
          :class="{ 'active': selectedPlatform === 'ShortMax' }"
        >
          <span class="mdi mdi-play-box-multiple"></span>
          ShortMax
        </a>
      </li>
    </ul>
  </div>

          <div class="dropdown dropdown-end">
    <div tabindex="0" role="button" class="btn btn-ghost gap-2">
      <span class="mdi mdi-translate text-xl"></span>
      <span>{{ selectedLanguage }}</span>
      <span class="mdi mdi-chevron-down"></span>
    </div>
    <ul tabindex="0" class="dropdown-content menu bg-base-100 rounded-box z-[1] w-52 p-2 shadow">
      <li>
        <a 
          @click="changeLanguage('ID')" 
          :class="{ 'active': selectedLanguage === 'ID' }"
        >
          <span class="mdi mdi-flag"></span>
          Indonesia
        </a>
      </li>
      <li>
        <a 
          @click="changeLanguage('EN')" 
          :class="{ 'active': selectedLanguage === 'EN' }"
        >
          <span class="mdi mdi-flag-variant"></span>
          English
        </a>
      </li>
    </ul>
  </div>
        
          <div class="dropdown dropdown-end">
            <label tabindex="0" class="btn btn-ghost btn-circle">
              <div class="w-8 md:w-10 rounded-full flex items-center justify-center">
                <span class="mdi mdi-account text-lg"></span>
              </div>
            </label>
            <ul tabindex="0" class="dropdown-content z-[1] menu p-2 shadow-lg bg-base-100 rounded-box w-52">
              <li>
                <a class="gap-2" @click="openProfileModal">
                  <span class="mdi mdi-account-circle"></span>
                  Profile
                </a>
              </li>
              <div class="divider my-1"></div>
              <li>
                <a class="gap-2 text-error">
                  <span class="mdi mdi-logout"></span>
                  Logout
                </a>
              </li>
            </ul>
          </div>

          <!-- Profile Modal -->
          <input type="checkbox" id="profile_modal" class="modal-toggle" v-model="showModal" />
          <div class="modal" :class="{ 'modal-open': showModal }">
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
                    <span class="font-medium">{{ userData?.name || '-' }}</span>
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
        
      </div>
    </div>
  </div>
  
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { siteSetting, Storage } from '../../utils/helpers';
import { Link } from '@inertiajs/vue3';
import { router } from '@inertiajs/vue3';

const setting = siteSetting();

const currentTheme = ref('synthwave');
const selectedPlatform = ref('DramaBox')

const changePlatform = (platform) => {
  selectedPlatform.value = platform
  
  // Redirect jika bukan DramaBox
  if (platform !== 'DramaBox') {
    selectedPlatform.value = 'DramaBox';
    router.visit(`/coming-soon/${platform}`)
  }
}

const selectedLanguage = ref('ID')

const changeLanguage = (lang) => {
  selectedLanguage.value = lang
  // Tambahkan logic untuk mengubah bahasa aplikasi
  console.log('Language changed to:', lang)
}
const themes = ref([
  { label: 'Light', value: 'light', icon: 'mdi mdi-white-balance-sunny' },
  { label: 'Dark', value: 'dark', icon: 'mdi mdi-moon-waning-crescent' },
  { label: 'Cupcake', value: 'cupcake', icon: 'mdi mdi-cupcake' },
  { label: 'Bumblebee', value: 'bumblebee', icon: 'mdi mdi-bee' },
  { label: 'Emerald', value: 'emerald', icon: 'mdi mdi-diamond-stone' },
  { label: 'Corporate', value: 'corporate', icon: 'mdi mdi-office-building' },
  { label: 'Synthwave', value: 'synthwave', icon: 'mdi mdi-sunglasses' },
  { label: 'Retro', value: 'retro', icon: 'mdi mdi-cassette' },
  { label: 'Cyberpunk', value: 'cyberpunk', icon: 'mdi mdi-robot' },
  { label: 'Valentine', value: 'valentine', icon: 'mdi mdi-heart' },
  { label: 'Halloween', value: 'halloween', icon: 'mdi mdi-ghost' },
  { label: 'Garden', value: 'garden', icon: 'mdi mdi-flower' },
  { label: 'Forest', value: 'forest', icon: 'mdi mdi-tree' },
  { label: 'Aqua', value: 'aqua', icon: 'mdi mdi-water' },
  { label: 'Lofi', value: 'lofi', icon: 'mdi mdi-music' },
  { label: 'Pastel', value: 'pastel', icon: 'mdi mdi-palette' },
  { label: 'Fantasy', value: 'fantasy', icon: 'mdi mdi-castle' },
  { label: 'Wireframe', value: 'wireframe', icon: 'mdi mdi-vector-square' },
  { label: 'Black', value: 'black', icon: 'mdi mdi-circle' },
  { label: 'Luxury', value: 'luxury', icon: 'mdi mdi-crown' },
  { label: 'Dracula', value: 'dracula', icon: 'mdi mdi-vampire' },
  { label: 'CMYK', value: 'cmyk', icon: 'mdi mdi-printer' },
  { label: 'Autumn', value: 'autumn', icon: 'mdi mdi-leaf' },
  { label: 'Business', value: 'business', icon: 'mdi mdi-briefcase' },
  { label: 'Acid', value: 'acid', icon: 'mdi mdi-flask' },
  { label: 'Lemonade', value: 'lemonade', icon: 'mdi mdi-glass-cocktail' },
  { label: 'Night', value: 'night', icon: 'mdi mdi-weather-night' },
  { label: 'Coffee', value: 'coffee', icon: 'mdi mdi-coffee' },
  { label: 'Winter', value: 'winter', icon: 'mdi mdi-snowflake' },
  { label: 'Dim', value: 'dim', icon: 'mdi mdi-brightness-6' },
  { label: 'Nord', value: 'nord', icon: 'mdi mdi-compass' },
  { label: 'Sunset', value: 'sunset', icon: 'mdi mdi-weather-sunset' },
]);

const setTheme = (theme) => {
  currentTheme.value = theme;
  document.documentElement.setAttribute('data-theme', theme);
  localStorage.setItem('theme', theme);
};

const randomTheme = () => {
  const randomIndex = Math.floor(Math.random() * themes.value.length);
  const theme = themes.value[randomIndex].value;
  setTheme(theme);

  // Tampilkan toast notification (optional)
  // Anda bisa tambahkan toast library atau custom notification
  console.log(`Theme changed to: ${themes.value[randomIndex].label}`);
};

// Load saved theme on mount
onMounted(() => {
  const savedTheme = localStorage.getItem('theme');
  if (savedTheme) {
    currentTheme.value = savedTheme;
    document.documentElement.setAttribute('data-theme', savedTheme);
  } else {
    // Set default theme
    setTheme('dark');
  }
});


const showModal = ref(false);
const userData = ref(null);
const subscriptionData = ref(null);

const openProfileModal = () => {
  // Ambil data dari Storage
  userData.value = Storage.get('mobis_user');
  subscriptionData.value = Storage.get('mobis_sub');
  showModal.value = true;
};

const closeProfileModal = () => {
  showModal.value = false;
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
</script>

<style scoped>
/* Custom scrollbar untuk dropdown */
.dropdown-content::-webkit-scrollbar {
  width: 6px;
}

.dropdown-content::-webkit-scrollbar-track {
  background: transparent;
}

.dropdown-content::-webkit-scrollbar-thumb {
  background: oklch(var(--bc) / 0.3);
  border-radius: 3px;
}

.dropdown-content::-webkit-scrollbar-thumb:hover {
  background: oklch(var(--bc) / 0.5);
}

/* Active menu item styling */
.menu li>button.active {
  background-color: oklch(var(--p) / 0.2);
  color: oklch(var(--p));
}
</style>