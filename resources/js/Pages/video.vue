<script setup>
import Navbar from './components/Navbar.vue';
import Footer from './components/Footer.vue';
import VideoPlayer from './components/VideoPlayer.vue';
import { getService } from '../utils/api';
import { ref } from 'vue';
import NetshortVideoPlayer from './components/NetshortVideoPlayer.vue';
defineProps({prop:Object});

const service = ref(getService());
console.log(service.value);

</script>

<template>
  <div class="min-h-screen bg-base-100">
    <Navbar />
    <div v-if="service === 'netshort'">
      netshort bos
      <NetshortVideoPlayer :bookId="prop.bookId" :episode="prop.episode"/>
    </div>
    <div v-else>
    <VideoPlayer   :bookId="prop.bookId" :episode="prop.episode"/>
    </div>
    <Footer />
  </div>
</template>