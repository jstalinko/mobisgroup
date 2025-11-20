<template>
    <!-- Hero Swiper Section -->
    <div class="relative bg-gradient-to-b from-base-100 to-base-300">
      <div class="carousel w-full h-[450px] md:h-[600px] ">
        <div 
          v-for="(slide, index) in heroSlides" 
          :key="index"
          :id="`slide${index + 1}`" 
          class="carousel-item relative w-full"
          @click="router.visit('/detail/'+slide.id+'/'+slugify(slide.title))"
        >

        
          <!-- Background Image with Overlay -->
          <div class="absolute inset-0 overflow-hidden">
            <img 
              :src="slide.image" 
              class="w-full h-full object-cover blur-sm scale-110" 
              alt="Background"
            />
            <div class="absolute inset-0 bg-gradient-to-t from-base-300 via-base-300/80 to-base-300/40"></div>
          </div>

          <!-- Content -->
          <div class="relative w-full flex items-center justify-center px-4 md:px-8 ">
            <div class="container mx-auto">
              <div class="flex flex-col md:flex-row items-center justify-center gap-6 md:gap-12">
                <!-- Poster Image -->
                <div class="relative group cursor-pointer">
                  <img 
                    :src="slide.image" 
                    class="w-[200px] md:w-[280px] rounded-xl shadow-2xl transition-transform duration-300 group-hover:scale-105" 
                    :alt="slide.title"
                  />
                  <!-- Hover Play Button -->
                  <div class="absolute inset-0 bg-black/60 opacity-0 group-hover:opacity-100 transition-opacity duration-300 rounded-xl flex items-center justify-center">
                    <button class="btn btn-circle btn-lg btn-primary">
                      <span class="mdi mdi-play text-3xl"></span>
                    </button>
                  </div>
                </div>

                <!-- Info -->
                <div class="max-w-2xl text-center md:text-left " >
                  <h1 class="text-3xl md:text-5xl lg:text-6xl font-bold mb-4 text-white drop-shadow-lg">
                    {{ slide.title }}
                  </h1>
                  <div class="flex gap-2 my-4 flex-wrap justify-center md:justify-start">
                    
                    <div v-for="badge in slide.tags" :key="badge" class="badge badge-lg hidden md:block" >
                      {{ badge }}
                    </div>
                  </div>
                  <p class="text-base md:text-lg mb-6 text-gray-200 line-clamp-3 hidden md:block">
                    {{ slide.introduction }}
                  </p>
                  <div class="flex gap-3 justify-center md:justify-start hidden md:block">
                    <button class="btn btn-primary btn-sm md:btn-lg gap-2" @click="router.visit('/play/'+slide.id+'/1/'+slugify(slide.title))">
                      <span class="mdi mdi-play"></span>
                      Watch Now
                    </button>
                    &nbsp;&nbsp;
                    <button class="btn btn-outline btn-sm md:btn-lg gap-2" @click="router.visit('/detail/'+slide.id+'/'+slugify(slide.title))">
                      <span class="mdi mdi-information-outline"></span>
                      More Info
                    </button>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      
      <!-- Dots Indicator -->
      <div class="flex justify-center w-full py-4 gap-2 absolute bottom-4">
        <a 
          v-for="(slide, index) in heroSlides" 
          :key="`dot-${index}`" 
          :href="`#slide${index + 1}`" 
          class="w-2 h-2 md:w-3 md:h-3 rounded-full bg-white/50 hover:bg-white transition-all"
        ></a>
      </div>
    </div>
</template>

<script setup>
import { slugify } from '../../utils/helpers';
import { router } from '@inertiajs/vue3';


defineProps({heroSlides: Object});
</script>