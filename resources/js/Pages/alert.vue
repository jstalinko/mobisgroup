<template>
    <Navbar />
    <div class="min-h-screen bg-base-100 flex flex-col justify-between">
        <div class="flex-1 flex items-center justify-center px-6 py-10">
            <div class="shadow-md rounded-2xl p-8 max-w-md w-full text-center border">
                <div class="text-yellow-500 text-5xl mb-4">
                    <span class="mdi mdi-alert-circle-outline mx-auto text-4xl"></span>
                </div>
                <h1 class="text-2xl font-bold text-gray-100 mb-2">
                    STOP!
                </h1>
                <p class="text-gray-200 mb-6">
                    {{ message }}
                </p>
                <button
                    @click="goBack"
                    class="w-full bg-yellow-500 hover:bg-yellow-600 text-white py-3 px-5 rounded-xl 
                           font-semibold shadow transition-colors"
                >
                    Kembali
                </button>
            </div>
        </div>
        <MobileNav />
        <Footer />
    </div>
</template>
<script setup>
import MobileNav from './components/MobileNav.vue';
import Footer from './components/Footer.vue';
import Navbar from './components/Navbar.vue';
import { router } from '@inertiajs/vue3';
defineProps({message:String});
const goBack = () => {
    window.location.href = document.referrer || '/';
};
</script>