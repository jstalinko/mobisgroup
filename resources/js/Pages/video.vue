<script setup>
import Navbar from './components/Navbar.vue';
import Footer from './components/Footer.vue';
import Loading from './components/Loading.vue';
import VideoPlayer from './components/VideoPlayer.vue'
 defineProps({prop:Object});
</script>

<template>
  <div class="min-h-screen bg-base-100">
    <Navbar />

    <VideoPlayer   :bookId="prop.bookId" :episode="prop.episode"/>

    <Footer />
  </div>
</template>