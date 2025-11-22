<template>
  <div class="flex items-center gap-2">
    <span v-if="active" class="text-green-400">
      <span class="mdi mdi-check-circle text-xl font-bold"></span>
    </span>
    <span v-else class="text-red-500">
      <span class="mdi mdi-close-circle text-xl font-bold"></span>
    </span>

    <p :class="active ? 'text-gray-100 font-bold' : 'text-gray-200'" class="text-sm">
      {{ title }}
    </p>
  </div>
</template>

<script setup>
const props = defineProps({
  title: String,
  active: Boolean
})
</script>
