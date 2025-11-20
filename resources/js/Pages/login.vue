<template>
  <div class="min-h-screen bg-base-200 flex flex-col items-center justify-center">
    <!-- Logo / Brand -->
    <div class="text-center mb-8">
      <h1 class="text-3xl font-bold text-primary">MOBISGROUP.ID</h1>
      <p class="text-sm text-base-content/70 mt-2">
        Masukkan lisensi Anda untuk melanjutkan
      </p>
    </div>

    <!-- Card -->
    <div class="card w-full max-w-md shadow-xl bg-base-100">
      <div class="card-body">
        <h2 class="card-title justify-center">Masukan Lisensi Anda</h2>
        <p class="text-center text-muted">
          Masukan lisensi anda yang telah dibeli untuk mengakses konten premium kami.
        </p>

        <form @submit.prevent="submitLicense" class="mt-4 space-y-4">
          <div>
            <label class="label">
              <span class="label-text">Kode Lisensi:</span>
            </label>
            <input v-model="license" type="text" placeholder="contoh: MOBIS-XXXX-YYYY-ZZZZ"
              class="input input-bordered w-full" required />
          </div>

          <button type="submit" class="btn btn-primary w-full" :disabled="loading">
            <span v-if="!loading">Verifikasi Lisensi</span>
            <span v-else class="loading loading-spinner"></span>
          </button>

          <p>
            Belum punya lisensi? <a href="/plan" class="text-primary underline">Beli disini</a>
          </p>
        </form>

        <!-- Optional Error Message -->
        <div v-if="errorMessage" class="alert alert-error mt-4">
          <span>{{ errorMessage }}</span>
        </div>
      </div>
    </div>

    <!-- Footer -->
    <footer class="mt-8 text-sm text-base-content/60">
      © {{ new Date().getFullYear() }} MOBISGROUP.ID — All rights reserved
    </footer>
  </div>
</template>

<script setup>
import { ref } from "vue"
import { http } from "../utils/api"
import { Storage } from "../utils/helpers"
const license = ref("")
const loading = ref(false)
const errorMessage = ref("")
function getCookie(name) {
  const v = `; ${document.cookie}`;
  const parts = v.split(`; ${name}=`);
  if (parts.length === 2) return parts.pop().split(';').shift();
}

function getXsrfToken() {
    const match = document.cookie.match(/XSRF-TOKEN=([^;]+)/);
    return match ? decodeURIComponent(match[1]) : "";
}
const submitLicense = async () => {
  loading.value = true
  errorMessage.value = ""
  const csrfToken = document
    .querySelector('meta[name="csrf-token"]')
    ?.getAttribute('content');
  try {
    const res = await http("/api/check-license", {
      method: "POST",
       credentials: "include",
      headers: {
        "X-XSRF-TOKEN": getXsrfToken(),
        'Content-Type': 'application/json'
      },
      body: {
        csrf_token: csrfToken,
        license_key: license.value, // kirim sesuai controller
      }
    })


    // Jika API mengembalikan status false
    if (!res.status) {
      errorMessage.value = res.message || "Lisensi tidak valid."
      return
    }

    Storage.set('mobis_token', res.token);
    Storage.set('mobis_license_key', res.data.user.license_key);

    Storage.set('mobis_user', res.data.user);
    Storage.set('mobis_sub', res.data.subscription);

    // Jika license valid → redirect ke dashboard atau halaman premium
    window.location.href = "/";

  } catch (error) {
    errorMessage.value = "Terjadi kesalahan saat memverifikasi lisensi. "+error
  } finally {
    loading.value = false
  }
}
</script>
