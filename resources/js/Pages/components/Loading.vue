<template>
  <transition name="fade">
    <div
      v-if="show"
      class="fixed inset-0 z-[9999] flex items-center justify-center bg-black/40 backdrop-blur-sm"
    >
      <div class="flex flex-col items-center gap-4">
        <span class="mdi mdi-loading text-5xl animate-spin text-white"></span>
        <p class="text-white text-lg font-semibold">Loading...</p>
      </div>
    </div>
  </transition>
</template>

<script setup>
defineProps({
  show: {
    type: Boolean,
    default: false,
  },
});
</script>

<style scoped>
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.2s;
}
.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}
</style>
