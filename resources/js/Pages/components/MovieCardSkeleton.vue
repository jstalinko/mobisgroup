<template>
  <div class="card bg-base-100 shadow-xl">
    <figure class="relative overflow-hidden bg-base-300 aspect-video">
      <!-- Skeleton Image -->
      <div class="skeleton w-full h-full"></div>
      
      <!-- Skeleton Badge -->
      <div class="absolute top-1 right-1 md:top-2 md:right-2">
        <div class="skeleton w-12 h-4 md:w-16 md:h-5"></div>
      </div>
    </figure>

    <div class="card-body p-2 md:p-3">
      <!-- Skeleton Title -->
      <div class="skeleton h-3 md:h-4 w-full mb-2"></div>
      <div class="skeleton h-3 md:h-4 w-3/4"></div>
    </div>
  </div>
</template>