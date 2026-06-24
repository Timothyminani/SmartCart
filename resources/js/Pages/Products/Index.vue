
<template>
<AppLayout>
 <div class="max-w-7xl mx-auto px-6 py-10 ">

  <!-- 🔷 PHASE 1 -->



  <!-- 🔷 PHASE 1: DYNAMIC TOP SECTION -->
<div class="mb-10">

  <!-- 🔵 AI MODE -->
  <div v-if="query" class="space-y-5">

    <!-- SEARCH BAR -->
    <div class="bg-white border rounded-xl p-3 flex items-center gap-3 shadow-sm">

      <!-- Icon -->
      <Search class="w-5 h-5 text-gray-400" />

      <!-- Input -->
      <input
        type="text"
        :value="query"
        placeholder="Search or refine your request..."
        class="flex-1 bg-transparent focus:outline-none text-sm"
      />

      <!-- Button -->
      <button class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm">
        Search
      </button>

    </div>

    <!-- AI SUMMARY -->
    <div class="bg-blue-50 border border-blue-100 rounded-xl p-4 flex items-start gap-3">

      <div class="bg-blue-100 p-2 rounded-full">
        🤖
      </div>

      <div>
        <p class="text-sm text-gray-500 mb-1">AI Insight</p>
        <p class="text-sm text-blue-700 font-medium">
          {{ summary }}
        </p>
      </div>

    </div>

    <!-- AI RECOMMENDATION -->
    <div
      v-if="recommendation"
      class="bg-white border rounded-xl p-4 shadow-sm flex items-center justify-between"
    >

      <div>
        <p class="text-xs text-gray-500 mb-1">Recommended</p>
        <h3 class="text-sm font-semibold text-gray-900">
          {{ recommendation.title }}
        </h3>
        <p class="text-xs text-gray-600">
          {{ recommendation.description }}
        </p>
      </div>

      <button class="text-blue-600 text-sm hover:underline">
        View
      </button>

    </div>

  </div>

  <!--  DEFAULT MODE (BANNER) -->
<div v-else class="relative rounded-xl overflow-hidden h-48 md:h-56">

  <!-- BACKGROUND IMAGE -->
  <img
    :src="BannerImage"
    alt="Banner"
    class="absolute inset-0 w-full h-full object-cover"
  />

  <!-- DARK OVERLAY -->
  <div class="absolute inset-0 bg-black/25"></div>

  <!-- CONTENT -->
  <div class="relative z-10 h-full flex items-center px-6">

    <div class="text-white max-w-md">

      <!-- BADGE -->
      <span class="bg-blue-600 text-white text-xs px-2 py-1 rounded mb-2 inline-block">
        LIMITED OFFER
      </span>

      <!-- TITLE -->
      <h2 class="text-2xl md:text-3xl font-bold mb-2">
        🔥 Smart Deals for You
      </h2>

      <!-- SUBTEXT -->
      <p class="text-sm text-gray-200 mb-4">
        AI finds the best products at the best prices
      </p>

      <!-- BUTTON -->
      <button class="bg-white text-blue-600 px-5 py-2 rounded-lg font-medium hover:bg-gray-100 transition">
        Shop Now
        <ShoppingCart class="w-4 h-4 inline-block ml-1" />
      </button>

    </div>

  </div>

</div>

</div>







  <!-- 🔷 PHASE 2 -->
  <div class="flex gap-6">


     <!-- 🔸 LEFT: FILTER SIDEBAR -->
  <aside class="w-64 hidden lg:block space-y-6">

    <!-- CATEGORIES -->
    <div class="bg-white p-4 rounded-xl border shadow-sm">
      <h3 class="text-sm font-semibold text-gray-900 mb-3">Categories</h3>

      <div class="space-y-2 text-sm text-gray-600">
      <label
  v-for="category in categories"
  :key="category.id"
  class="flex items-center gap-2 cursor-pointer"
>
  <input
    type="checkbox"
    class="accent-blue-600"
    :value="category.id"
    v-model="selectedCategories"
    @change="applyFilters"
  />
  {{ category.name }}
</label>
       
        
      </div>
    </div>

    <!-- BRANDS -->
    <div class="bg-white p-4 rounded-xl border shadow-sm">
      <h3 class="text-sm font-semibold text-gray-900 mb-3">Brands</h3>

      <div class="space-y-2 text-sm text-gray-600">
        <div class="space-y-3">

  <label
  v-for="brand in brands"
  :key="brand.id"
  class="flex items-center gap-3 cursor-pointer"
>
  <input
    type="checkbox"
    class="accent-blue-600"
    :value="brand.id"
    v-model="selectedBrands"
    @change="applyFilters"
  />

  <img
    :src="`/storage/${brand.logo}`"
    class="w-6 h-6 object-contain"
  />

  <span class="text-sm text-gray-700">
    {{ brand.name }}
  </span>
</label>

</div>
        
        
      </div>
    </div>

    <!-- PRICE FILTER -->
    <div class="bg-white p-4 rounded-xl border shadow-sm">
      <h3 class="text-sm font-semibold text-gray-900 mb-3">Price</h3>

      <input
        type="range"
        min="0"
        max="100000"
        class="w-full accent-blue-600"
      />

      <div class="flex justify-between text-xs text-gray-500 mt-2">
        <span>KES 0</span>
        <span>KES 100K</span>
      </div>
    </div>

  </aside>

 
   



    <!-- RIGHT: PRODUCTS -->
    <div class="flex-1">

     
 <!-- TOP BAR -->
    <div class="flex items-center justify-between mb-4">

      <p class="text-sm text-gray-600">
        Showing {{ productList.length }} products
      </p>

<select
  v-model="selectedSort"
  @change="applyFilters"
  class="border rounded-lg text-sm px-3 py-2 focus:outline-none"
>
  <option value="">Sort by</option>
  <option value="price_asc">Price: Low to High</option>
  <option value="price_desc">Price: High to Low</option>
  <option value="newest">Newest</option>
</select>

    </div>

    <!-- PRODUCT GRID -->
    <div
     v-if="productList.length"
      class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6"
    >
        <ProductCard
          v-for="product in productList"
          :key="product.id"
          :product="product"
        />
    </div>

    <!-- EMPTY STATE -->
    <div v-else class="text-center text-gray-500 py-10">
      No products found.
    </div>

 
      

    </div>



  </div>


<div class="flex justify-center mt-8">

  <button
  v-if="currentPage < lastPage"
  @click="loadMore"
  class="flex items-center gap-2 bg-gradient-to-r hover:bg-gradient-to-l  border-l-white from-blue-600/40 via-blue-500/70 to-blue-100 text-white px-6 py-2 rounded-xl text-sm transition-all duration-200 shadow-md hover:shadow-xl hover:scale-105 active:scale-95"
>
  <template v-if="loading">
    <Loader2 class="w-4 h-4 animate-spin" />
    Loading...
  </template>

  <template v-else>
    <ArrowDown class="w-4 h-4" />
    Load More Products
  </template>
</button>

  <p v-else class="text-gray-500 text-sm">
    No more products
  </p>

</div>



</div>



</AppLayout>
</template>



<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'
import ProductCard from '@/Components/ProductCard.vue'
import { Search, ShoppingCart, LayoutDashboard,  ArrowDown, Loader2 } from 'lucide-vue-next'
import BannerImage from '@/assets/images/banner4.jpg'
import { router, Link } from '@inertiajs/vue3'
import { ref } from 'vue'
import { watch } from 'vue'


const props = defineProps({
  query: String,
  summary: String,
  recommendation: Object,
  products: Object,
  categories: Array,
  brands: Array,
  filters: Object
})


const selectedCategories = ref(props.filters?.categories || [])
const selectedBrands = ref(props.filters?.brands || [])

const productList = ref(props.products?.data || [])
const currentPage = ref(props.products?.current_page || 1)
const lastPage = ref(props.products?.last_page || 1)
const loading = ref(false)
const selectedSort = ref('')

const applyFilters = () => {
  router.get(route('products.index'), {
     page: 1, 
    categories: selectedCategories.value,
    brands: selectedBrands.value,
     sort: selectedSort.value
  }, {
    preserveState: true,
    replace: true,
    preserveScroll: true,
   
   onSuccess: (page) => {
    // REPLACE, don't append
    productList.value = page.props.products.data
   currentPage.value = page.props.products.current_page
   lastPage.value = page.props.products.last_page
},
    onFinish: () => {
        loading.value = false
    }
  })
}



const loadMore = () => {
  if (currentPage.value >= lastPage.value) return

  loading.value = true

  router.get(route('products.index'), {
    page: currentPage.value + 1,
    categories: selectedCategories.value,
    brands: selectedBrands.value,
    sort: selectedSort.value 
  }, {
    preserveState: true,
    preserveScroll: true,
    only: ['products'],
    onSuccess: (page) => {
    productList.value = page.props.products.data
                currentPage.value = page.props.products.current_page
                lastPage.value = page.props.products.last_page
    },
    onFinish: () => {
      loading.value = false
    }
  })
}


watch(
  () => props.products,
  (newProducts) => {
    productList.value = newProducts.data
    currentPage.value = newProducts.current_page
    lastPage.value = newProducts.last_page
  }
)



</script>