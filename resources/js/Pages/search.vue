<template>
    <Navbar/>
    <div class="container mx-auto mt-10 px-4 mb-10" >

        <!-- Search input form -->
     <div class="mb-8">
    <div class="max-w-2xl mx-auto">
        <div class="relative">
            <input 
                v-model="searchQuery"
                @keyup.enter="handleSearch"
                type="text" 
                placeholder="Search movies, series, actors..." 
                class="input input-bordered w-full pl-12 pr-16 input-lg shadow-lg focus:shadow-xl transition-shadow duration-300"
            />
            <svg 
                xmlns="http://www.w3.org/2000/svg"
                viewBox="0 0 24 24"
                class="absolute left-4 top-1/2 transform -translate-y-1/2 h-5 w-5 text-base-content/40"
                fill="currentColor"
            >
                <path d="M9.5,3A6.5,6.5 0 0,1 16,9.5C16,11.11 15.41,12.59 14.44,13.73L14.71,14H15.5L20.5,19L19,20.5L14,15.5V14.71L13.73,14.44C12.59,15.41 11.11,16 9.5,16A6.5,6.5 0 0,1 3,9.5A6.5,6.5 0 0,1 9.5,3M9.5,5C7,5 5,7 5,9.5C5,12 7,14 9.5,14C12,14 14,12 14,9.5C14,7 12,5 9.5,5Z" />
            </svg>
            <button 
                @click="handleSearch"
                class="btn btn-ghost btn-sm btn-circle absolute right-2 top-1/2 transform -translate-y-1/2"
            >
                <svg 
                    xmlns="http://www.w3.org/2000/svg"
                    viewBox="0 0 24 24"
                    class="h-5 w-5"
                    fill="currentColor"
                >
                    <path d="M9.5,3A6.5,6.5 0 0,1 16,9.5C16,11.11 15.41,12.59 14.44,13.73L14.71,14H15.5L20.5,19L19,20.5L14,15.5V14.71L13.73,14.44C12.59,15.41 11.11,16 9.5,16A6.5,6.5 0 0,1 3,9.5A6.5,6.5 0 0,1 9.5,3M9.5,5C7,5 5,7 5,9.5C5,12 7,14 9.5,14C12,14 14,12 14,9.5C14,7 12,5 9.5,5Z" />
                </svg>
            </button>
        </div>
    </div>
</div>

        <div class="grid grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-2 md:gap-4">
            <template v-if="isLoading">
                <MovieCardSkeleton v-for="x in 10" :key="x"/>
            </template>
            <template v-else>
                <MovieCard2 v-for="(item,index) in searchData" :key="'search-'+index" :item="item"/>
            </template>
        </div>
    </div>
    <Footer/>
    <MobileNav/>
</template>

<script setup>
import Footer from './components/Footer.vue';
import Navbar from './components/Navbar.vue';
import MobileNav from './components/MobileNav.vue';
import { onMounted, ref } from 'vue';
import MovieCard2 from './components/MovieCard2.vue';
import MovieCardSkeleton from './components/MovieCardSkeleton.vue';
import { getSearch } from '../utils/api';
const isLoading = ref(false);
const props = defineProps({prop:Object});
const searchData = ref([]);

const searchQuery = ref(props.prop.query);

const handleSearch = async () => {
    isLoading.value = true;
    let result = await getSearch(searchQuery.value);
    searchData.value = result?.data;
    isLoading.value = false;
};

onMounted(async()=>{
    isLoading.value = true;
    let searchResult = await getSearch(props.prop.query);
    searchData.value = searchResult?.data;
    isLoading.value=false;
});

</script>