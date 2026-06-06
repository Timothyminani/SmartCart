<template>
  <AppLayout>
    <div class="max-w-6xl mx-auto px-6 py-10 grid md:grid-cols-2 gap-10 ">

      <!-- 🖼️ IMAGES -->
      <div class=" ">
  <!-- MAIN IMAGE -->
  <img
    :src="`/storage/${activeImage}`"
    class="w-full h-96 object-cover rounded-xl shadow"
  />

  <!-- THUMBNAILS -->
  <div class="flex gap-3 mt-4">
    <img
      v-for="img in product.images"
      :key="img.id"
      :src="`/storage/${img.image_path}`"
      @click="activeImage = img.image_path"
      class="w-20 h-20 object-cover rounded-lg cursor-pointer border hover:border-blue-500"
    />
  </div>
</div>




      <!-- 📦 PRODUCT INFO -->
<div class="bg-white rounded-2xl p-6 space-y-6">

  <!-- 🟢 TITLE -->
  <h1 class="text-2xl font-bold text-gray-900 leading-snug">
    {{ product.name }}
  </h1>

  <!-- 🔵 BRAND + CATEGORY -->
  <div class="text-sm text-gray-500 space-y-1 flex gap-2">
    <p>Brand: <span class="text-gray-700 font-medium">{{ product.brand?.name }}. Category: {{ product.category?.name }}</span></p>

  </div>

  <!-- 💰 PRICE -->
  <div class="flex items-center gap-3">
    <span class="text-3xl text-blue-600 font-bold">
      KES {{ Number(product.sale_price).toLocaleString() }} 
    </span>

    <span class="text-gray-400 line-through text-sm">
       KES {{ Number(product.price).toLocaleString() }}
    </span>

    <!-- DISCOUNT BADGE -->
    <span class="bg-green-100 text-green-600 text-xs px-2 py-1 rounded">
      Save
    </span>
  </div>

  <!-- 🚀 ACTION BUTTONS -->
  <div class="flex items-center gap-3">

    <!-- ADD TO CART -->
   <div class="mt-6 flex-1">

  <!-- 🟢 NOT IN CART -->
  <button
    v-if="!cartItem"
    @click="handleAdd"
    class="flex-1 w-full flex items-center justify-center gap-2 bg-gradient-to-r from-blue-600 to-blue-400 text-white py-3 rounded-xl shadow hover:shadow-md transition"
  >
    <ShoppingCart class="w-5 h-5" />
    Add to Cart
  </button>

  <!-- 🔵 IN CART -->
  <div
    v-else
    class="flex items-center justify-between w-full bg-gradient-to-r text-white  py-3 px-4 rounded-xl shadow"
  >

    <!-- MINUS -->
    <button
      @click="handleDecrease"
      class="py-3 px-3 bg-blue-600  rounded-lg transition"
    >
      <Minus class="w-4 h-4" />
    </button>

    <!-- QUANTITY -->
   
   <span class="text-sm font-semibold flex items-center justify-center">

  <!-- 🔄 LOADING -->
  <Loader2
    v-if="loading"
    class="w-6 h-6 text-blue-600 animate-spin"
  />

  <!-- 🔢 NORMAL -->
  <span v-else class="text-black">
    {{ cartItem?.quantity }}
  </span>

</span>


    <!-- PLUS -->
    <button
      @click="handleIncrease"
      class="py-3 px-3 bg-blue-600 rounded-lg transition"
    >
      <Plus class="w-4 h-4" />
    </button>

  </div>

</div>

    <!-- ❤️ WISHLIST (BOX STYLE) -->
    <button class="w-12 h-12 flex items-center justify-center border rounded-xl hover:bg-gray-100 transition">
      <Heart class="w-5 h-5 text-gray-600" />
    </button>

  </div>

  <!-- ⚖️ COMPARE -->
  <label class="flex items-center gap-3 text-sm cursor-pointer bg-gray-50 px-3 py-2 rounded-lg border hover:border-blue-400 transition">
    <input type="checkbox" class="accent-blue-600 w-4 h-4" />
    <span class="text-gray-700 font-medium">Add to Compare</span>
  </label>

  <!-- 🚚 DELIVERY INFO -->
  <div class="flex items-start gap-3 text-sm text-gray-600">
    <Truck class="w-5 h-5 text-blue-600 mt-0.5" />
    <div>
      <p class="font-medium text-gray-800">Estimated Delivery</p>
      <p>2 - 4 business days</p>
    </div>
  </div>

  <!-- 🛡️ WARRANTY -->
  <div class="flex items-start gap-3 text-sm text-gray-600">
    <ShieldCheck class="w-5 h-5 text-green-600 mt-0.5" />
    <div>
      <p class="font-medium text-gray-800">Warranty</p>
      <p>6 months warranty included</p>
    </div>
  </div>

</div>

</div>




<div class="mt-16">

  <!-- TAB HEADERS (CENTERED) -->
  <div class="flex justify-center border-b">

    <div class="flex gap-10 text-sm font-medium">

      <button
        @click="activeTab = 'description'"
        :class="[
          'pb-3 transition',
          activeTab === 'description'
            ? 'border-b-2 border-blue-600 text-blue-600'
            : 'text-gray-500 hover:text-blue-600'
        ]"
      >
        Description
      </button>

      <button
        @click="activeTab = 'specs'"
        :class="[
          'pb-3 transition',
          activeTab === 'specs'
            ? 'border-b-2 border-blue-600 text-blue-600'
            : 'text-gray-500 hover:text-blue-600'
        ]"
      >
        Specifications
      </button>

      <button
        @click="activeTab = 'reviews'"
        :class="[
          'pb-3 transition',
          activeTab === 'reviews'
            ? 'border-b-2 border-blue-600 text-blue-600'
            : 'text-gray-500 hover:text-blue-600'
        ]"
      >
        Reviews
      </button>

    </div>

  </div>

  <!-- TAB CONTENT -->
  <div class="mt-10 max-w-4xl mx-auto px-4">

    <!-- DESCRIPTION -->
    <div v-if="activeTab === 'description'" class="text-gray-700 text-sm leading-relaxed">
      {{ product.description }}
    </div>

    <!-- SPECS -->
    <div v-if="activeTab === 'specs'">

      <div v-if="product.attributes && product.attributes.length" class="bg-white border rounded-xl p-6 divide-y">

        <div
          v-for="attr in product.attributes"
          :key="attr.id"
          class="flex justify-between py-3 text-sm"
        >
          <span class="text-gray-500">
            {{ attr.attribute_name }}
          </span>

          <span class="font-medium text-gray-900">
            {{ attr.attribute_value }}
          </span>
        </div>

      </div>

      <p v-else class="text-gray-500 text-sm">
        No specifications available.
      </p>

    </div>

    <!-- REVIEWS -->
    <div v-if="activeTab === 'reviews'" class="text-gray-500 text-sm">
      No reviews yet.
    </div>

  </div>

</div>



<!-- RELATED PRODUCTS -->
<div
  v-if="relatedProducts.length"
  class="max-w-7xl mx-auto px-6 mt-20 pb-16"
>

  <!-- HEADER -->
  <div class="mb-8">

    <h2 class="text-2xl font-bold text-gray-900">
      You May Also Like
    </h2>

    <p class="text-sm text-gray-500 mt-1">
      Similar products you might like
    </p>

  </div>

  <!-- SWIPER -->
  <Swiper
  :modules="[Navigation, Pagination]"
  navigation
  pagination
  :slides-per-view="2"
  :space-between="15"
  :breakpoints="{
    640: { slidesPerView: 2 },
    768: { slidesPerView: 3 },
    1024: { slidesPerView: 4 }
  }"
  class="w-full pb-20"
>

   <!-- SWIPER SLIDE -->
<SwiperSlide
  v-for="item in relatedProducts"
  :key="item.id"
>

  <Link
    :href="`/products/${item.slug}`"
    class="group block bg-white border rounded-xl overflow-hidden hover:shadow-md transition h-full"
  >

    <!-- IMAGE (SMALLER + FIXED HEIGHT) -->
    <div class="overflow-hidden bg-gray-50">

      <img
        :src="`/storage/${item.images[0]?.image_path}`"
        class="w-full h-40 object-cover group-hover:scale-105 transition duration-300"
      />

    </div>

    <!-- CONTENT (COMPACT) -->
    <div class="p-3 space-y-2">

      <!-- BRAND -->
      <p class="text-[10px] text-gray-400 uppercase tracking-wide">
        {{ item.brand?.name }}
      </p>

      <!-- NAME -->
      <h3
        class="text-xs font-medium text-gray-800 line-clamp-2 min-h-[32px]"
      >
        {{ item.name }}
      </h3>

      <!-- PRICE -->
      <div class="flex items-center gap-2">

        <span class="text-sm font-bold text-blue-600">
          KES {{ Number(item.sale_price).toLocaleString() }}
        </span>

        <span
          v-if="item.price"
          class="text-[10px] text-gray-400 line-through"
        >
          {{ Number(item.price).toLocaleString() }}
        </span>

      </div>

    </div>

  </Link>

</SwiperSlide>

  </Swiper>

</div>


  </AppLayout>
</template>


<script setup>
import { useHead } from '@vueuse/head'
import { ref } from 'vue'
import AppLayout from '@/Layouts/AppLayout.vue'
import { Search, ShoppingCart, LayoutDashboard,  ArrowDown, Loader2 } from 'lucide-vue-next'
import {  Heart, Truck, ShieldCheck } from 'lucide-vue-next'
import { useCart } from '@/composables/useCart'
import { computed } from 'vue'
import { Link } from '@inertiajs/vue3'
import { Plus, Minus } from 'lucide-vue-next'
import { Swiper, SwiperSlide } from 'swiper/vue'
import 'swiper/css'
import { Navigation, Pagination } from 'swiper/modules'

import 'swiper/css'
import 'swiper/css/navigation'
import 'swiper/css/pagination'


const loading = ref(false)
const props = defineProps({
  product: Object,
   relatedProducts: Array
})

const activeImage = ref(props.product.images[0]?.image_path)
const activeTab = ref('description')


useHead({
  title: `${props.product.name} | Buy Online`,
  meta: [
    {
      name: 'description',
      content: props.product.description?.slice(0, 150)
    }
  ]
})




useHead({
  script: [
    {
      type: 'application/ld+json',
      children: JSON.stringify({
        "@context": "https://schema.org/",
        "@type": "Product",
        "name": props.product.name,
        "image": `/storage/${props.product.images[0]?.image_path}`,
        "description": props.product.description,
        "brand": {
          "@type": "Brand",
          "name": props.product.brand?.name
        },
        "offers": {
          "@type": "Offer",
          "priceCurrency": "KES",
          "price": props.product.price,
          "availability": "https://schema.org/InStock"
        }
      })
    }
  ]
})



const { addToCart, increaseQty, decreaseQty, getCartItem, removeItem } = useCart()

const cartItem = ref(null)

// ---------------- LOAD ITEM ----------------
const loadCartItem = async () => {
  cartItem.value = await getCartItem(props.product.id)
}

// ---------------- ADD ----------------
const handleAdd = async () => {
  loading.value = true

  await addToCart(props.product.id)

  // ⚡ instant UI update
  cartItem.value = {
    id: null,
    product_id: props.product.id,
    quantity: 1
  }

  loading.value = false
}

// ---------------- INCREASE ----------------
const handleIncrease = async () => {
  loading.value = true

  await increaseQty(props.product.id)

  // ⚡ instant UI update
  cartItem.value.quantity++

  loading.value = false
}

// ---------------- DECREASE ----------------
const handleDecrease = async () => {
  loading.value = true

  if (cartItem.value.quantity > 1) {
    await decreaseQty(cartItem.value.id, cartItem.value.quantity)

    // ⚡ instant UI update
    cartItem.value.quantity--
  } else {
    await removeItem(cartItem.value.id)

    // ⚡ instant UI update
    cartItem.value = null
  }

  loading.value = false
}

// ---------------- LIFECYCLE ----------------
import { onMounted, onBeforeUnmount } from 'vue'

onMounted(async () => {
  await loadCartItem()
  window.addEventListener('cartUpdated', loadCartItem)
})

onBeforeUnmount(() => {
  window.removeEventListener('cartUpdated', loadCartItem)
})

</script>


<style scoped>
.swiper {
  padding-bottom: 40px;
}

.swiper-pagination {
  bottom: 0 !important;
}
</style>