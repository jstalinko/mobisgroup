<script setup>
import Navbar from './components/Navbar.vue';
import Footer from './components/Footer.vue';
import VideoPlayer from './components/VideoPlayer.vue'
import { ref } from 'vue'

const dummyVideoData = ref({
  title: 'Rindu yang Terlambat',
  episodeCount: 74,
  currentEpisodeNumber: 1, // Episode yang sedang dimainkan
  synopsis: 'Rindu yang Terlambat Episode 1... Mina menderita sakit parah dan hidupnya tidak lama lagi. Yuda, yang selalu menjadi pahlawan di hati Mina, lebih memilih menghabiskan waktu dengan Nina dan putranya, Dasa. Rita, ibu Mina, ingin menemui Yuda untuk membatalkan rencana selingkuh dengan Nina. Akhirnya, Mina meninggal dalam kekecewaan. Yuda, awalnya tidak percaya, terpengaruh oleh Nina dan salah paham terhadap Rita. Saat ia ingin menebus kesalahannya, semuanya sudah terlambat.',
  tags: ['Perselingkuhan', 'Anak Keluarga', 'Pernikahan', 'Penyesalan', 'Menderita'],
  likes: 2.4, // dalam ribuan
  views: 3.5, // dalam jutaan
  episodes: Array.from({ length: 74 }, (_, i) => ({
    number: i + 1,
    title: `Rindu yang Terlambat`,
    // Anda bisa tambahkan synopsis per episode jika ada
    synopsis: `Ini adalah sinopsis untuk episode ${i + 1}. Detail cerita akan berbeda di setiap episode.`
  })),
})
</script>

<template>
  <div class="min-h-screen bg-base-100">
    <Navbar/>
    <VideoPlayer />

    <Footer/>
  </div>
</template>