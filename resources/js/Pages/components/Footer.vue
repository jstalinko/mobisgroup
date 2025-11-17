<template>
  <footer class="bg-base-200 border-t border-base-300 hidden md:block">
    <div class="container mx-auto px-4 py-8">
      <!-- Main Content -->
      <div class="flex flex-col md:flex-row justify-between items-center gap-6">
        <!-- Brand -->
        <div class="text-center md:text-left">
          <h3 class="font-bold text-lg text-primary mb-1">
            {{ setting.site_name }}
          </h3>
          <p class="text-sm text-base-content/70">
            {{ setting.site_description }}
          </p>
        </div>

        <!-- Links -->
        <nav class="flex gap-6 text-sm">
          <a href="#" class="link link-hover">About</a>
          <a href="#" class="link link-hover">Contact</a>
          <a href="#" class="link link-hover">Terms</a>
          <a href="#" class="link link-hover">Privacy</a>
        </nav>
      </div>

      <!-- Copyright -->
      <div class="text-center mt-6 pt-6 border-t border-base-300">
        <p class="text-xs text-base-content/60">
          © {{new Date().getFullYear()}} {{ setting.site_name }}. All rights reserved.
        </p>
      </div>
    </div>
  </footer>
</template>

<script setup>
import { siteSetting } from '../../utils/helpers';

let setting = siteSetting();
</script>