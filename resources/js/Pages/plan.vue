<template>
    <Navbar />
    <div class="container max-w-7xl mx-auto py-4 px-4 min-h-screen">
        
        <h1 class="text-3xl font-bold mb-6">Paket Langganan</h1>
        <p class="mb-8 ">
            Pilih paket langganan yang sesuai dengan kebutuhan Anda untuk mengakses konten premium kami.
        </p>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <PlanCard v-for="plan in prop.plans" :key="plan.id" :items="plan" :checkout_url="prop.checkout_url"/>
        </div>
    </div>
    <MobileNav/>
    <Footer />
</template>
<script setup>
import MobileNav from './components/MobileNav.vue';
import PlanCard from './components/PlanCard.vue';
import Navbar from './components/Navbar.vue';
import Footer from './components/Footer.vue';
const props = defineProps({
    prop: Object
});
</script>