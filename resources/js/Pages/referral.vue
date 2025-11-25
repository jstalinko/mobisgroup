<template>
  <Navbar />

  <div class="min-h-screen bg-base-200 py-8 px-4 md:px-8 pb-24 md:pb-8">
    <div class="max-w-4xl mx-auto">

      <!-- Header Section -->
      <div class="text-center mb-6 md:mb-8">
        <h1 class="text-2xl md:text-4xl font-bold mb-2 flex items-center justify-center gap-2 md:gap-3">
          <span class="mdi mdi-share-variant text-primary text-2xl md:text-4xl"></span>
          Program Referral
        </h1>
        <p class="text-sm md:text-base text-base-content/70">Ajak teman dan dapatkan benefit menarik!</p>
      </div>

      <!-- Referral Card -->
      <div class="card bg-base-100 shadow-xl mb-4 md:mb-6">
        <div class="card-body p-4 md:p-6">
          <h2 class="card-title text-base md:text-lg flex items-center gap-2">
            <span class="mdi mdi-link-variant text-primary"></span>
            Link Referral Anda
          </h2>

          <div class="bg-base-200 rounded-[16px] p-3 md:p-4 mb-4">
            <div class="text-xs md:text-sm opacity-70 mb-2">Link Referral</div>
            <div class="flex items-center gap-2">
              <input type="text" :value="props.prop?.referralLink || 'https://example.com/ref/YOUR_CODE'" readonly
                class="input input-bordered input-sm md:input-md flex-1 font-mono text-xs md:text-sm" />
              <button @click="copyLink" class="btn btn-primary btn-sm md:btn-md btn-square"
                :class="{ 'btn-success': linkCopied }">
                <span class="mdi text-lg" :class="linkCopied ? 'mdi-check' : 'mdi-content-copy'"></span>
              </button>
            </div>
          </div>
          <div class="divider my-2 md:my-4"></div>

          <!-- Share Buttons -->
          <h3 class="font-semibold text-sm md:text-base mb-2 md:mb-3">Bagikan Melalui:</h3>
          <div class="grid grid-cols-2 md:grid-cols-4 gap-2 md:gap-3">
            <button @click="shareWhatsApp" class="btn btn-success btn-sm md:btn-md gap-1 md:gap-2">
              <span class="mdi mdi-whatsapp text-lg md:text-xl"></span>
              <span class="text-xs md:text-sm">WhatsApp</span>
            </button>

            <button @click="shareTelegram" class="btn btn-info btn-sm md:btn-md gap-1 md:gap-2">
              <span class="mdi mdi-send text-lg md:text-xl"></span>
              <span class="text-xs md:text-sm">Telegram</span>
            </button>

            <button @click="shareTwitter" class="btn btn-sm md:btn-md gap-1 md:gap-2">
              <span class="mdi mdi-twitter text-lg md:text-xl"></span>
              <span class="text-xs md:text-sm">Twitter</span>
            </button>

            <button @click="shareFacebook" class="btn btn-primary btn-sm md:btn-md gap-1 md:gap-2">
              <span class="mdi mdi-facebook text-lg md:text-xl"></span>
              <span class="text-xs md:text-sm">Facebook</span>
            </button>
          </div>
        </div>
      </div>

      <!-- Statistics Section - Mobile Friendly -->
      <div class="grid grid-cols-2 md:grid-cols-4 gap-3 md:gap-4 mb-4 md:mb-6">
        <!-- Total Referral -->
        <div class="bg-base-100 rounded-[16px] md:rounded-[20px] shadow-lg p-3 md:p-4">
          <div class="flex flex-col items-center text-center">
            <span class="mdi mdi-account-multiple text-primary text-2xl md:text-4xl mb-1 md:mb-2"></span>
            <div class="text-xs md:text-sm opacity-70">Total Referral</div>
            <div class="text-xl md:text-3xl font-bold text-primary">{{ referral.totalReferrals }}</div>
          </div>
        </div>

        <!-- Total Komisi -->
        <div class="bg-base-100 rounded-[16px] md:rounded-[20px] shadow-lg p-3 md:p-4">
          <div class="flex flex-col items-center text-center">
            <span class="mdi mdi-cash-multiple text-secondary text-2xl md:text-4xl mb-1 md:mb-2"></span>
            <div class="text-xs md:text-sm opacity-70">Total Komisi</div>
            <div class="text-base md:text-2xl font-bold text-secondary">
              <span class="text-xs md:text-sm">Rp</span> {{ formatNumber(referral.totalCommission) }}
            </div>
          </div>
        </div>

        <!-- Saldo Komisi -->
        <div class="bg-base-100 rounded-[16px] md:rounded-[20px] shadow-lg p-3 md:p-4">
          <div class="flex flex-col items-center text-center">
            <span class="mdi mdi-wallet text-accent text-2xl md:text-4xl mb-1 md:mb-2"></span>
            <div class="text-xs md:text-sm opacity-70">Saldo Komisi</div>
            <div class="text-base md:text-2xl font-bold text-accent">
              <span class="text-xs md:text-sm">Rp</span> {{ formatNumber(referral.availableCommission) }}
            </div>
          </div>
        </div>

        <!-- Saldo Dicairkan -->
        <div class="bg-base-100 rounded-[16px] md:rounded-[20px] shadow-lg p-3 md:p-4">
          <div class="flex flex-col items-center text-center">
            <span class="mdi mdi-cash-check text-success text-2xl md:text-4xl mb-1 md:mb-2"></span>
            <div class="text-xs md:text-sm opacity-70">Dicairkan</div>
            <div class="text-base md:text-2xl font-bold text-success">
              <span class="text-xs md:text-sm">Rp</span> {{ formatNumber(referral.withdrawnCommission) }}
            </div>
          </div>
        </div>
      </div>

      <!-- Referral Users List -->
      <!-- Tabs Navigation -->
      <div class="card bg-base-100 shadow-xl mb-4 md:mb-6">
        <div role="tablist" class="tabs tabs-boxed tabs-lg">
          <a role="tab" class="tab text-xs md:text-sm" :class="{ 'tab-active': activeTab === 'users' }"
            @click="activeTab = 'users'">
            <span class="mdi mdi-account-group mr-1 md:mr-2"></span>
            Referral User
          </a>
          <a role="tab" class="tab text-xs md:text-sm" :class="{ 'tab-active': activeTab === 'withdraw' }"
            @click="activeTab = 'withdraw'">
            <span class="mdi mdi-bank-transfer mr-1 md:mr-2"></span>
            Tarik Komisi
          </a>
        </div>
      </div>

      <!-- Tab Content: Referral Users List -->
      <div v-show="activeTab === 'users'" class="card bg-base-100 shadow-xl mb-4 md:mb-6">
        <div class="card-body p-4 md:p-6">
          <h2 class="card-title text-base md:text-lg flex items-center gap-2 mb-3 md:mb-4">
            <span class="mdi mdi-account-group text-primary"></span>
            Daftar Pengguna Referral
          </h2>


          <div class="overflow-x-auto">
            <table class="table table-xs md:table-sm">
              <thead>
                <tr>
                  <th class="text-xs md:text-sm">No</th>
                  <th class="text-xs md:text-sm">Username</th>
                  <th class="text-xs md:text-sm">Status</th>
                  <th class="text-xs md:text-sm">Komisi</th>
                  <th class="text-xs md:text-sm hidden md:table-cell">Tanggal</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="(ref, index) in prop.referrals" :key="ref.id" class="hover">
                  <td class="text-xs md:text-sm">{{ index + 1 }}</td>
                  <td>
                    <div class="flex items-center gap-2">
                      <div class="avatar placeholder">
                        <div class="bg-primary text-primary-content rounded-full w-6 h-6 md:w-8 md:h-8">
                          <span class="text-xs">{{ ref.referred_user.name }}</span>
                        </div>
                      </div>
                      <div class="text-xs md:text-sm font-medium">{{ ref.referred_user?.name }}</div>
                    </div>
                  </td>
                  <td>
                    <span class="badge badge-xs md:badge-sm" :class="getStatusBadge(ref.status)">
                      {{ ref.status }}
                    </span>
                  </td>
                  <td class="text-xs md:text-sm font-semibold">
                    Rp {{ formatNumber(ref.bonus_amount) }}
                  </td>
                  <td class="text-xs opacity-70 hidden md:table-cell">{{ formatDate(ref.created_at) }}</td>
                </tr>
              </tbody>
            </table>
          </div>

          <!-- Empty State -->
          <div v-if="prop.referrals.length === 0" class="text-center py-8">
            <span class="mdi mdi-account-off text-5xl opacity-30"></span>
            <p class="text-sm opacity-70 mt-2">Belum ada pengguna yang menggunakan referral Anda</p>
          </div>
        </div>
      </div>

     
      <!-- Tab Content: Tarik Komisi -->
      <div v-show="activeTab === 'withdraw'" class="card bg-base-100 shadow-xl mb-4 md:mb-6">
        <div class="card-body p-4 md:p-6">
          <h2 class="card-title text-base md:text-lg flex items-center gap-2 mb-3 md:mb-4">
            <span class="mdi mdi-bank-transfer text-primary"></span>
            Penarikan Komisi
          </h2>
           <div>
               <h3 class="font-semibold text-sm md:text-base mb-2">Saldo Komisi Tersedia</h3>
                <div class="text-2xl md:text-3xl font-bold text-accent">
                  <span class="text-sm md:text-base">Rp</span> {{ formatNumber(referral.availableCommission) }}
                  </div>
              </div>
          <div>
         <div>
    <form @submit.prevent="submitWithdrawal" class="space-y-4">


      <!-- Nama Bank / E-Wallet -->
      <div>
        <label class="label">
          <span class="label-text text-xs md:text-sm">Nama Bank / E-Wallet</span>
        </label>
        <input 
          v-model="withdrawalForm.bankName"
          type="text" 
          class="input input-bordered w-full input-sm md:input-md text-xs md:text-sm"
          placeholder="Masukkan Nama bank ( Cth: BCA, OVO, DANA )" 
          :disabled="referral.availableCommission < minimalWithdrawal" 
        />
      </div>

      <!-- No. Rekening / HP -->
      <div>
        <label class="label">
          <span class="label-text text-xs md:text-sm">No. Rekening / HP</span>
        </label>
        <input 
          v-model="withdrawalForm.accountNumber"
          type="tel" 
          class="input input-bordered w-full input-sm md:input-md text-xs md:text-sm"
          placeholder="Masukkan No. Rekening / No. HP" 
          :disabled="referral.availableCommission < minimalWithdrawal"
        />
      </div>

      <!-- Atas Nama -->
      <div>
        <label class="label">
          <span class="label-text text-xs md:text-sm">Atas Nama</span>
        </label>
        <input 
          v-model="withdrawalForm.accountName"
          type="text" 
          class="input input-bordered w-full input-sm md:input-md text-xs md:text-sm"
          placeholder="Masukkan atas nama pemilik rekening/e-wallet" 
          :disabled="referral.availableCommission < minimalWithdrawal"
        />
      </div>

      <!-- No. Whatsapp -->
      <div>
        <label class="label">
          <span class="label-text text-xs md:text-sm">No. Whatsapp</span>
        </label>
        <input 
          v-model="withdrawalForm.whatsapp"
          type="tel" 
          class="input input-bordered w-full input-sm md:input-md text-xs md:text-sm"
          placeholder="Masukkan nomer whatsapp aktif" 
          :disabled="referral.availableCommission < minimalWithdrawal"
        />
      </div>

      <!-- Alert jika saldo kurang -->
      <div v-if="referral.availableCommission < minimalWithdrawal" class="alert alert-warning">
        <span class="mdi mdi-alert"></span>
        <span class="text-xs md:text-sm">Saldo komisi minimal  {{ formatCurrency(minimalWithdrawal) }} untuk melakukan penarikan</span>
      </div>

      <!-- Submit Button -->
      <div>
        <button 
          type="submit" 
          class="btn btn-primary w-full btn-sm md:btn-md mt-2"
          :disabled="referral.availableCommission < minimalWithdrawal"
        >
          Ajukan Penarikan
        </button>
      </div>
    </form>
  </div>
          </div>
        </div>
      </div>
      <!-- How It Works -->
      <div class="card bg-base-100 shadow-xl">
        <div class="card-body p-4 md:p-6">
          <h2 class="card-title text-base md:text-lg flex items-center gap-2">
            <span class="mdi mdi-help-circle text-primary"></span>
            Cara Kerja
          </h2>

          <div class="space-y-3 md:space-y-4 mt-3 md:mt-4">
            <div class="flex items-start gap-3 md:gap-4">
              <div class="badge badge-primary badge-sm md:badge-lg shrink-0">1</div>
              <div>
                <div class="font-semibold text-sm md:text-base">Bagikan Link Referral</div>
                <div class="text-xs md:text-sm opacity-70">Kirim link atau kode referral Anda ke teman dan keluarga
                </div>
              </div>
            </div>

            <div class="flex items-start gap-3 md:gap-4">
              <div class="badge badge-primary badge-sm md:badge-lg shrink-0">2</div>
              <div>
                <div class="font-semibold text-sm md:text-base">Teman Mendaftar</div>
                <div class="text-xs md:text-sm opacity-70">Teman Anda mendaftar menggunakan link atau kode referral Anda
                </div>
              </div>
            </div>

            <div class="flex items-start gap-3 md:gap-4">
              <div class="badge badge-primary badge-sm md:badge-lg shrink-0">3</div>
              <div>
                <div class="font-semibold text-sm md:text-base">Dapatkan Komisi</div>
                <div class="text-xs md:text-sm opacity-70">Anda mendapat komisi setiap kali teman Anda melakukan
                  transaksi</div>
              </div>
            </div>

            <div class="flex items-start gap-3 md:gap-4">
              <div class="badge badge-primary badge-sm md:badge-lg shrink-0">4</div>
              <div>
                <div class="font-semibold text-sm md:text-base">Withdraw Kapan Saja</div>
                <div class="text-xs md:text-sm opacity-70">Cairkan komisi Anda dengan minimal penarikan Rp 50.000</div>
              </div>
            </div>
          </div>
        </div>
      </div>

    </div>
  </div>

  <Footer />
  <MobileNav />
  <Loading :show="isLoading"/>
</template>

<script setup>
import { ref } from 'vue';
import MobileNav from './components/MobileNav.vue';
import Navbar from './components/Navbar.vue';
import Footer from './components/Footer.vue';
import { http } from '../utils/api';
import Loading from './components/Loading.vue';
import { formatCurrency } from '../utils/helpers';

const props = defineProps({ prop: Object });
const isLoading = ref(false);
const linkCopied = ref(false);
const codeCopied = ref(false);
const referral = ref(props.prop || {});
const activeTab = ref('users');
const minimalWithdrawal = 20000;

const copyLink = () => {
  const link = props.prop?.referralLink || 'https://example.com/ref/YOUR_CODE';
  navigator.clipboard.writeText(link);
  linkCopied.value = true;
  setTimeout(() => linkCopied.value = false, 2000);
};
// Withdrawal form
const withdrawalForm = ref({
  amount: null,
  bankName: '',
  accountNumber: '',
  accountName: '',
  whatsapp: ''
});
const submitWithdrawal =async () => {
  if (referral.availableCommission < minimalWithdrawal) return
  isLoading.value = true;
  // Handle withdrawal submission
  const response = await http('/api/request-withdrawal' , {
    method:'POST',
    body:{
      phone: withdrawalForm.value.whatsapp,
      bank_name: withdrawalForm.value.bankName,
      account_number: withdrawalForm.value.accountNumber,
      holder_name: withdrawalForm.value.accountName,
      user_id: props.prop.user_id
    }
  });
if(response.success){  
  // Reset form after submission
  withdrawalForm.value = {
    amount: null,
    bankName: '',
    accountNumber: '',
    accountName: '',
    whatsapp: ''
  }
  
  // Show success message
  alert('Pengajuan penarikan berhasil diajukan!');
  window.location.reload();
}else{
  alert('Pengajuan tidak dapat di proses,silakan ulangi lagi');
  window.location.reload;
}
  isLoading.value = false;
}
const copyCode = () => {
  const code = props.prop?.referralCode || 'YOUR_CODE';
  navigator.clipboard.writeText(code);
  codeCopied.value = true;
  setTimeout(() => codeCopied.value = false, 2000);
};

const shareWhatsApp = () => {
  const text = `Yuk join pakai link referral aku! ${props.prop?.referralLink || 'https://example.com/ref/YOUR_CODE'}`;
  window.open(`https://wa.me/?text=${encodeURIComponent(text)}`, '_blank');
};

const shareTelegram = () => {
  const text = `Yuk join pakai link referral aku! ${props.prop?.referralLink || 'https://example.com/ref/YOUR_CODE'}`;
  window.open(`https://t.me/share/url?url=${encodeURIComponent(props.prop?.referralLink || 'https://example.com/ref/YOUR_CODE')}&text=${encodeURIComponent(text)}`, '_blank');
};

const shareTwitter = () => {
  const text = `Yuk join pakai link referral aku!`;
  window.open(`https://twitter.com/intent/tweet?text=${encodeURIComponent(text)}&url=${encodeURIComponent(props.prop?.referralLink || 'https://example.com/ref/YOUR_CODE')}`, '_blank');
};

const shareFacebook = () => {
  window.open(`https://www.facebook.com/sharer/sharer.php?u=${encodeURIComponent(props.prop?.referralLink || 'https://example.com/ref/YOUR_CODE')}`, '_blank');
};

const formatNumber = (num) => {
  return new Intl.NumberFormat('id-ID').format(num);
};

const formatDate = (dateString) => {
  const date = new Date(dateString);
  return date.toLocaleDateString('id-ID', { year: 'numeric', month: 'short', day: 'numeric' });
};

const getStatusBadge = (status) => {
  const badges = {
    'withdrawn': 'badge-success',
    'completed': 'badge-info',
    'pending': 'badge-warning'
  };
  return badges[status] || 'badge-ghost';
};
</script>