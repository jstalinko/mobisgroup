<template>
  <div class="container mx-auto px-4 py-8">
    <!-- Hero Section -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 mb-12">
      <!-- Poster Skeleton -->
      <div class="lg:col-span-3">
        <div class="skeleton w-full aspect-[2/3] rounded-lg"></div>
        <div class="skeleton btn btn-block mt-4 h-12"></div>
      </div>

      <!-- Info Section Skeleton -->
      <div class="lg:col-span-9">
        <!-- Title -->
        <div class="skeleton h-10 w-3/4 mb-4"></div>
        
        <!-- Episodes Count -->
        <div class="skeleton h-6 w-32 mb-6"></div>

        <!-- Description -->
        <div class="space-y-3 mb-6">
          <div class="skeleton h-4 w-full"></div>
          <div class="skeleton h-4 w-full"></div>
          <div class="skeleton h-4 w-full"></div>
          <div class="skeleton h-4 w-2/3"></div>
        </div>

        <!-- Tags -->
        <div class="flex flex-wrap gap-2 mb-6">
          <div class="skeleton h-8 w-24 rounded-full"></div>
          <div class="skeleton h-8 w-20 rounded-full"></div>
          <div class="skeleton h-8 w-28 rounded-full"></div>
          <div class="skeleton h-8 w-24 rounded-full"></div>
          <div class="skeleton h-8 w-20 rounded-full"></div>
        </div>

        <!-- Share Buttons -->
        <div class="flex items-center gap-4">
          <div class="skeleton h-5 w-16"></div>
          <div class="flex gap-2">
            <div class="skeleton w-10 h-10 rounded-full"></div>
            <div class="skeleton w-10 h-10 rounded-full"></div>
            <div class="skeleton w-10 h-10 rounded-full"></div>
            <div class="skeleton w-10 h-10 rounded-full"></div>
          </div>
        </div>
      </div>
    </div>

    <!-- Episode List Section -->
    <div class="mb-8">
      <!-- Section Header -->
      <div class="flex items-center gap-4 mb-6">
        <div class="skeleton h-8 w-8"></div>
        <div class="skeleton h-8 w-48"></div>
        <div class="skeleton h-8 w-24 rounded-full"></div>
      </div>

      <!-- Episode Grid -->
      <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 xl:grid-cols-8 gap-4">
        <div v-for="n in 8" :key="n" class="space-y-2">
          <!-- Episode Thumbnail -->
          <div class="relative">
            <div class="skeleton w-full aspect-video rounded-lg"></div>
            <div class="absolute inset-0 flex items-center justify-center">
              <div class="skeleton w-12 h-12 rounded-full"></div>
            </div>
          </div>
          <!-- Episode Title -->
          <div class="skeleton h-4 w-full"></div>
          <div class="skeleton h-3 w-2/3"></div>
        </div>
      </div>

      <!-- Load More Button -->
      <div class="flex justify-center mt-8">
        <div class="skeleton btn h-12 w-48"></div>
      </div>
    </div>
  </div>
</template>

<script setup>
// No props needed for skeleton
</script>