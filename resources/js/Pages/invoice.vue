<template>
  <div>
    <Navbar />
    <MobileNav />

    <div class="container mx-auto px-4 py-8 max-w-4xl">
      <!-- Invoice Card -->
      <div class="card bg-base-100 shadow-xl">
        <div class="card-body">
          
          <!-- Header -->
          <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-6 pb-6 border-b">
            <div>
              <h1 class="text-3xl font-bold">Invoice</h1>
              <p class="text-gray-500 mt-1">{{ order?.invoice || 'xx' }}</p>
            </div>
            
            <div 
              class="badge badge-lg px-4 py-3 text-sm font-semibold"
              :class="{
                'badge-warning': order.status === 'pending',
                'badge-success': order.status === 'paid',
                'badge-error': order.status === 'cancelled'
              }"
            >
              {{ formatStatus(order.status) }}
            </div>
          </div>

          <!-- Order Info -->
          <div class="grid md:grid-cols-2 gap-6 mb-6">
            <div>
              <h3 class="font-semibold text-gray-500 text-sm mb-2">Informasi Paket</h3>
              <p class="text-lg font-bold">{{ order.plan?.name || 'Paket Premium' }}</p>
              <p class="text-sm text-gray-600 mt-1">{{ order.plan?.description || '' }}</p>
            </div>

            <div>
              <h3 class="font-semibold text-gray-500 text-sm mb-2">Tanggal Pemesanan</h3>
              <p class="text-lg">{{ formatDate(order.created_at) }}</p>
            </div>
          </div>

          <!-- Payment Details -->
          <div class="bg-base-200 rounded-lg p-6 mb-6">
            <h3 class="font-semibold text-lg mb-4">Detail Pembayaran</h3>
            
            <div class="space-y-3">
              <div class="flex justify-between">
                <span class="text-gray-600">Harga Paket</span>
                <span class="font-semibold">Rp {{ formatNumber(order.price) }}</span>
              </div>

              <div class="flex justify-between">
                <span class="text-gray-600">Kode Unik</span>
                <span class="font-semibold text-primary">{{ order.kode_unik }}</span>
              </div>

              <div class="divider my-2"></div>

              <div class="flex justify-between text-lg">
                <span class="font-bold">Total Pembayaran</span>
                <span class="font-bold text-primary">Rp {{ formatNumber(order.total_amount) }}</span>
              </div>
            </div>
          </div>

          <!-- Payment Method -->
          <div v-if="rekening" class="mb-6">
            <h3 class="font-semibold text-lg mb-3">Metode Pembayaran</h3>
            
            <!-- QRIS Payment -->
            <div v-if="order.payment_method === 'qris' && rekening[0].qrcode_image" class="bg-base-200 rounded-lg p-6">
              <div class="flex flex-col items-center">
                <div class="bg-white p-4 rounded-lg shadow-md mb-4">
                  <img 
                    :src="`/storage/${rekening[0].qrcode_image}`" 
                    :alt="`QRIS ${rekening[0].account_name || 'Payment'}`"
                    class="w-64 h-64 object-contain"
                  />
                </div>
                <p class="font-semibold text-lg">Scan QRIS</p>
                <p class="text-sm text-gray-500 text-center mt-1">
                  Scan kode QR di atas menggunakan aplikasi pembayaran Anda
                </p>
                <div v-if="rekening[0].account_name" class="mt-3 badge badge-outline">
                  {{ rekening[0].account_name }}
                </div>
              </div>
            </div>

            <!-- Bank Transfer -->
            <div v-else-if="order.payment_method === 'bank'" class="bg-base-200 rounded-lg p-6">
              <div class="space-y-4 mb-5" v-for="(rex,index) in rekening" :key="'rekening-'+index">
                <div class="flex items-center gap-3 mb-4">
                  <div class="w-12 h-12 rounded-full bg-primary/10 flex items-center justify-center">
                    <svg class="w-6 h-6 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                    </svg>
                  </div>
                  <div>
                    <p class="font-semibold text-lg">{{ rex.bank_name || 'Bank Transfer' }}</p>
                    <p class="text-sm text-gray-500">Transfer ke rekening berikut</p>
                  </div>
                </div>

                <div class="divider my-2"></div>

                <div class="space-y-3">
                  <div>
                    <p class="text-sm text-gray-500 mb-1">Nomor Rekening</p>
                    <div class="flex items-center justify-between bg-base-100 rounded-lg p-3">
                      <p class="font-mono font-bold text-lg">{{ rex.number }}</p>
                      <button 
                        class="btn btn-sm btn-ghost"
                        @click="copyToClipboard(rex.number)"
                      >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                        </svg>
                      </button>
                    </div>
                  </div>

                  <div>
                    <p class="text-sm text-gray-500 mb-1">Atas Nama</p>
                    <div class="bg-base-100 rounded-lg p-3">
                      <p class="font-semibold">{{ rex.account_name }}</p>
                    </div>
                  </div>

                 
                </div>

                
              </div>
              <div class="alert alert-warning mt-4">
                  <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-5 w-5" fill="none" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                  </svg>
                  <span class="text-xs">Transfer sesuai jumlah termasuk kode unik untuk verifikasi otomatis</span>
                </div>
            </div>

            <!-- E-Wallet -->
            <div v-else-if="order.payment_method === 'ewallet'" class="bg-base-200 rounded-lg p-6">
              <div class="space-y-4 mb-5" v-for="(ewe,index) in rekening" :key="'ewallet-'+index">
                <div class="flex items-center gap-3 mb-4">
                  <div class="w-12 h-12 rounded-full bg-primary/10 flex items-center justify-center">
                    <svg class="w-6 h-6 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                  </div>
                  <div>
                    <p class="font-semibold text-lg">{{ ewe.bank_name || 'E-Wallet' }}</p>
                    <p class="text-sm text-gray-500">Transfer ke nomor berikut</p>
                  </div>
                </div>

                <div class="divider my-2"></div>

                <div class="space-y-3">
                  <div>
                    <p class="text-sm text-gray-500 mb-1">Nomor E-Wallet</p>
                    <div class="flex items-center justify-between bg-base-100 rounded-lg p-3">
                      <p class="font-mono font-bold text-lg">{{ ewe.number }}</p>
                      <button 
                        class="btn btn-sm btn-ghost"
                        @click="copyToClipboard(ewe.number)"
                      >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                        </svg>
                      </button>
                    </div>
                  </div>

                  <div>
                    <p class="text-sm text-gray-500 mb-1">Atas Nama</p>
                    <div class="bg-base-100 rounded-lg p-3">
                      <p class="font-semibold">{{ ewe.account_name }}</p>
                    </div>
                  </div>
                </div>
              </div>
            </div>

          </div>

          <!-- Payment Instructions (if pending) -->
          <div v-if="order.status === 'pending'" class="alert alert-info mb-6">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" class="stroke-current shrink-0 w-6 h-6">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <div>
              <h3 class="font-bold">Menunggu Pembayaran</h3>
              <div class="text-xs">Silakan lakukan pembayaran sejumlah <strong>Rp {{ formatNumber(order.total_amount) }}</strong> untuk menyelesaikan pesanan.</div>
            </div>
          </div>

          <!-- Success Message (if paid) -->
          <div v-if="order.status === 'paid'" class="alert alert-success mb-6">
            <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <div>
              <h3 class="font-bold">Pembayaran Berhasil!</h3>
              <div class="text-xs">Terima kasih, pembayaran Anda telah dikonfirmasi.</div>
            </div>
          </div>

          <!-- Action Buttons -->
          <div class="flex flex-col sm:flex-row gap-3 mt-6">
            <button 
              v-if="order.status === 'pending'"
              class="btn btn-primary flex-1"
              @click="confirmPayment"
            >
              Konfirmasi Pembayaran
            </button>
            
            <button 
              class="btn btn-outline flex-1"
              @click="downloadInvoice"
            >
              <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
              </svg>
              Download Invoice
            </button>

            <button 
              v-if="order.status === 'pending'"
              class="btn btn-ghost"
              @click="cancelOrder"
            >
              Batalkan Pesanan
            </button>
          </div>

        </div>
      </div>

      <!-- Help Section -->
      <div class="text-center mt-8 text-gray-600">
        <p class="text-sm">Butuh bantuan? Hubungi <a href="#" class="text-primary font-semibold">Customer Support</a></p>
      </div>
    </div>

    <Footer />
  </div>
</template>

<script setup>
import { ref } from 'vue';
import MobileNav from './components/MobileNav.vue';
import Navbar from './components/Navbar.vue';
import Footer from './components/Footer.vue';

const props = defineProps({prop:Object});
const order =ref(props.prop.order || {});
const rekening = ref(props.prop.rekening || null);
const formatNumber = (number) => {
  return new Intl.NumberFormat('id-ID').format(number);
};

console.log(rekening.value);
const formatStatus = (status) => {
  const statusMap = {
    'pending': 'Menunggu Pembayaran',
    'paid': 'Lunas',
    'cancelled': 'Dibatalkan'
  };
  return statusMap[status] || status;
};

const formatDate = (dateString) => {
  if (!dateString) return '-';
  const date = new Date(dateString);
  return new Intl.DateTimeFormat('id-ID', {
    day: 'numeric',
    month: 'long',
    year: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  }).format(date);
};

const confirmPayment = () => {
  // Handle payment confirmation
  console.log('Confirm payment for order:', props.order.invoice);
  // You can add API call here to confirm payment
};

const downloadInvoice = () => {
  // Handle invoice download
  console.log('Download invoice:', props.order.invoice);
  // You can add API call here to generate PDF
  window.print();
};

const cancelOrder = () => {
  // Handle order cancellation
  if (confirm('Apakah Anda yakin ingin membatalkan pesanan ini?')) {
    console.log('Cancel order:', props.order.invoice);
    // You can add API call here to cancel order
  }
};

const copyToClipboard = async (text) => {
  try {
    await navigator.clipboard.writeText(text.toString());
    alert('Berhasil disalin ke clipboard!');
  } catch (err) {
    console.error('Failed to copy:', err);
  }
};
</script>

<style scoped>
@media print {
  .btn, .alert {
    display: none;
  }
}
</style>