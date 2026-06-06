<template>
  <div class="min-h-screen flex flex-col bg-gray-50 ">

    <!-- NAVBAR -->
    <header ref="headerRef" class="fixed top-0 left-0 w-full z-50 bg-white shadow-sm ">

      <!-- Top Bar -->
    <transition name="slide-up">
  <div v-if="showTopBar" class="bg-blue-900 text-white text-xs">
    <div class="max-w-7xl mx-auto px-6 py-1 flex justify-between items-center">

      <!-- LEFT: Contact -->
      <div class="flex items-center space-x-2">
        <Phone class="w-3 h-3" />
        <span>+254 712 345 678</span>
      </div>



<!-- CENTER: Offer Message -->
      <div class="hidden md:flex items-center text-[11px] text-white">
        <span class="bg-indigo-500 text-white px-2 py-0.5 rounded mr-2 text-[10px]">
          NEW
        </span>
        <span>
          Free delivery on orders above KES 2,000
        </span>
      </div>





      <!-- RIGHT: Links + Socials -->
      <div class="flex items-center space-x-6">

        <!-- Small Links -->
        <div class="flex items-center space-x-4 text-[11px] text-white">
          <a href="#" class="hover:text-white transition">About</a>
          <a href="#" class="hover:text-white transition">Terms</a>
          <a href="#" class="hover:text-white transition">Privacy</a>
            <a href="#" class="hover:text-white transition">FAQs</a>
        </div>

        <!-- Divider -->
        <div class="h-3 w-px bg-gray-300"></div>

        <!-- Social Icons -->
        <div class="flex items-center space-x-4">

    <!-- WhatsApp -->
    <a href="#" class="hover:text-blue-400 transition" title="WhatsApp">
        <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24">
            <path d="M20.52 3.48A11.91 11.91 0 0012 0C5.37 0 .04 5.33.04 11.96c0 2.11.55 4.16 1.6 5.96L0 24l6.24-1.63a11.96 11.96 0 005.76 1.47h.01c6.63 0 11.96-5.33 11.96-11.96a11.9 11.9 0 00-3.45-8.4zM12 21.5a9.4 9.4 0 01-4.8-1.3l-.34-.2-3.7.97.99-3.61-.22-.37A9.4 9.4 0 1121.4 12 9.4 9.4 0 0112 21.5zm5.2-7.1c-.28-.14-1.66-.82-1.92-.91-.26-.1-.45-.14-.64.14-.19.28-.73.91-.9 1.1-.17.19-.33.21-.61.07-.28-.14-1.18-.43-2.25-1.37-.83-.74-1.39-1.66-1.55-1.94-.16-.28-.02-.43.12-.57.13-.13.28-.33.42-.5.14-.17.19-.28.28-.47.09-.19.05-.36-.02-.5-.07-.14-.64-1.55-.88-2.12-.23-.56-.47-.49-.64-.5h-.55c-.19 0-.5.07-.76.36-.26.28-1 1-.99 2.44.02 1.44 1.04 2.83 1.18 3.02.14.19 2.04 3.11 4.95 4.36.69.3 1.23.48 1.65.62.69.22 1.31.19 1.8.11.55-.08 1.66-.68 1.9-1.33.23-.64.23-1.2.16-1.33-.07-.13-.26-.21-.55-.36z"/>
        </svg>
    </a>

    <!-- Facebook -->
    <a href="#" class="hover:text-blue-500 transition" title="Facebook">
        <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24">
            <path d="M22 12a10 10 0 10-11.63 9.87v-6.99H7.9V12h2.47V9.8c0-2.44 1.45-3.79 3.67-3.79 1.06 0 2.17.19 2.17.19v2.39h-1.22c-1.2 0-1.57.75-1.57 1.51V12h2.67l-.43 2.88h-2.24v6.99A10 10 0 0022 12z"/>
        </svg>
    </a>

    <!-- Instagram -->
    <a href="#" class="hover:text-pink-500 transition" title="Instagram">
        <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24">
            <path d="M7.75 2C4.57 2 2 4.57 2 7.75v8.5C2 19.43 4.57 22 7.75 22h8.5c3.18 0 5.75-2.57 5.75-5.75v-8.5C22 4.57 19.43 2 16.25 2h-8.5zM12 7a5 5 0 110 10 5 5 0 010-10zm6.5-.25a1.25 1.25 0 11-2.5 0 1.25 1.25 0 012.5 0z"/>
        </svg>
    </a>

    <!-- TikTok -->
    <a href="#" class="hover:text-gray-100 transition" title="TikTok">
        <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24">
            <path d="M16 1c.4 2.3 2.3 4.2 4.6 4.6v3.1c-1.5 0-2.9-.4-4.1-1.2v7.4a6.6 6.6 0 11-6.6-6.6c.4 0 .8 0 1.2.1v3.2a3.4 3.4 0 00-1.2-.2 3.4 3.4 0 103.4 3.4V1h2.7z"/>
        </svg>
    </a>

</div>
      </div>

    </div>
  </div>
</transition>



      <!-- Main Navbar -->


      
      <div  class="border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-6 py-3 flex items-center justify-between gap-6">
          <!-- Logo -->
<div class="text-2xl font-black tracking-tight leading-none cursor-pointer select-none font-[Poppins]">
  <span class="text-blue-600">Smart</span>
  <span class="text-blue-600">Cart</span>
</div>


          <!-- Search -->
<div class="flex-1 max-w-lg">

  <div class="flex items-center w-full  rounded-full overflow-hidden bg-blue-100/50 focus-within:ring-2 focus-within:ring-indigo-500 focus-within:bg-white transition ">

    <div>
        <Sparkles class="w-5 h-5 text-blue-600 mx-3" />
    </div>

    <!-- INPUT -->
    <input
      type="text"
      placeholder="Search or describe what you need..."
      class="flex-1 px-4 py-2 bg-transparent focus:outline-none border-none outline-none text-sm"
    />

    <!-- RIGHT: Search Button -->
    <button
      class="bg-blue-600  hover:bg-blue-700 px-3 py-2 text-white flex items-center justify-center rounded-full transition"
    >
      <Search class="w-6 h-6"  />
    </button>

  </div>

</div>



          <!-- Icons -->
          <div class="flex items-center space-x-5 text-gray-600">
            <button class="hover:text-blue-600 transition p-1  rounded-full flex items-center justify-center ">
              <Heart class="w-7 h-7" />
            </button>
            <button 
            @click="showCartDrawer = true"
            class="hover:text-blue-600 transition relative rounded-full flex items-center justify-center  p-1">
              <ShoppingCart class="w-7 h-7" />
 <span
  class="absolute -top-2 -right-2 text-xs bg-blue-600 text-white px-1.5 rounded-full transition"
  :class="{ 'animate-bounce': cartAnimate }"
> 
                {{ cartCount }}</span>
            </button>
          <div class="relative">

  <!-- USER ICON BUTTON -->
  <button 
    @click="showDropdown = !showDropdown"
    class="hover:text-blue-600 transition rounded-full flex items-center justify-center p-1"
  >
    <User class="w-7 h-7" />
  </button>

  <!-- DROPDOWN -->
  <div 
    v-if="showDropdown"
   class="absolute right-0 mt-2 w-48 bg-white rounded-xl shadow-lg border z-50 animate-fadeIn"
  >

    <!-- NOT LOGGED IN -->
    <div v-if="!$page.props.auth.user" class="flex flex-col text-sm">
      
      <a href="/login" class="px-4 py-2 hover:bg-gray-100">
        Login
      </a>

      <a href="/register" class="px-4 py-2 hover:bg-gray-100">
        Register
      </a>

    </div>

    <!-- LOGGED IN -->
    <div v-else class="flex flex-col text-sm">

      <a href="/profile" class="px-4 py-2 hover:bg-gray-100">
        Profile
      </a>

      <!-- LOGOUT -->
    <button 
        @click="logout"
        class="w-full text-left px-4 py-2 hover:bg-gray-100 text-red-500"
      >
        Logout
    </button>

    </div>

  </div>

</div>
          </div>
        </div>
      </div>





      <!-- Bottom Navigation -->
       <transition name="slide-up">
      <div v-if="showTopBar && !isProductsPage && !isProductPage && !isCheckoutPage && !isOrderSuccess && !isIndexOrder && !isShowOrder && !isPayment && !isComparePage && !isAiSearchPage" class="border-b border-gray-200 bg-white">
        <div class="max-w-7xl mx-auto px-6 py-2 flex items-center space-x-8 text-sm  text-gray-600">

<!-- LEFT: Categories -->
    <button
      @click="showSidebar = true"
      class="flex items-center gap-2 px-3 py-2  border-r text-sm text-blue-600 hover:bg-gray-200 transition"
    >
            <div class="flex flex-col justify-center items-start gap-[4px]">
            <span class="block w-5 h-[2px] bg-blue-600"></span>
            <span class="block w-3 h-[2px] bg-blue-600"></span>
            <span class="block w-5 h-[2px] bg-blue-600"></span>
            </div>
      <span class="hidden md:inline font-semibold">All Category</span>
    </button>

          <a href="#" class="hover:text-blue-600 transition">Home</a>
          <a href="#" class="hover:text-blue-600 transition">Shop</a>
          <a href="#" class="hover:text-blue-600 transition">Deals</a>
           <a href="#" class="hover:text-blue-600 transition">Phones</a>
          <a href="#" class="hover:text-blue-600 transition">Accessories</a>
          <a href="#" class="hover:text-blue-600 transition">Electronics</a>
           <a href="#" class="hover:text-blue-600 transition">Computers</a>
          <a href="#" class="hover:text-blue-600 transition">Cables</a>
          <a href="#" class="hover:text-blue-600 transition">TV</a>
    
        </div>
      </div>
  </transition>

    </header>

    <!-- PAGE CONTENT -->
    <main
         :style="{ paddingTop: headerHeight + 'px' }"
      class="flex-1 overflow-y-auto   max-w-7xl mx-auto w-full"
    >
      <slot />
    </main>

    <!-- FOOTER -->
    <footer class="bg-blue-900 border-t mt-10">
      <div class="max-w-7xl mx-auto px-6 py-8 grid grid-cols-2 md:grid-cols-4 gap-6 text-sm text-white">
        <div>
          <h3 class="font-semibold text-white mb-2">Shop</h3>
          <ul class="space-y-1">
            <li>Products</li>
            <li>Categories</li>
            <li>Deals</li>
          </ul>
        </div>
        <div>
          <h3 class="font-semibold text-white mb-2">Company</h3>
          <ul class="space-y-1">
            <li>About</li>
            <li>Contact</li>
          </ul>
        </div>
        <div>
          <h3 class="font-semibold text-white mb-2">Support</h3>
          <ul class="space-y-1">
            <li>Help Center</li>
            <li>Returns</li>
          </ul>
        </div>
        <div>
          <h3 class="font-semibold text-white mb-2">Legal</h3>
          <ul class="space-y-1">
            <li>Privacy Policy</li>
            <li>Terms</li>
          </ul>
        </div>
      </div>
      <div class="text-center text-xs text-white pb-4">
        © 2026 MyStore. All rights reserved.
      </div>
    </footer>

  </div>













<!-- OVERLAY -->


<div
  v-if="showCartDrawer"
  class="fixed inset-0 bg-black/40 backdrop-blur-sm z-50"
  @click="showCartDrawer = false"
></div>



<!-- DRAWER -->
<div
  class="fixed top-0 right-0 h-full w-full md:w-[85%] lg:w-[75%] bg-white shadow-2xl z-50 transform transition-transform duration-300 flex"
  :class="showCartDrawer ? 'translate-x-0' : 'translate-x-full'"
>





  <!-- LEFT: CART ITEMS -->
<div class="flex-1 flex flex-col border-r">

  <!-- HEADER (always at top, NOT scrolling) -->
  <div class="flex items-center gap-3 p-6 border-b bg-white">

    <!-- BACK BUTTON -->
    <button
      @click="showCartDrawer = false"
      class="w-9 h-9 flex items-center justify-center rounded-full hover:bg-gray-100 transition"
    >
      <ArrowLeft class="w-5 h-5 text-gray-700" />
    </button>

    <!-- TITLE -->
    <div>
      <h2 class="text-xl font-bold">Your Cart</h2>
      <p class="text-sm text-gray-500">
        {{ cartItems.length }} item(s)
      </p>
    </div>

  </div>

  <!-- SCROLLABLE CONTENT -->
  <div class="flex-1 overflow-y-auto p-6">

    <!-- EMPTY STATE -->
    <div v-if="!cartItems.length" class="text-center mt-20 text-gray-500">
      Your cart is empty
    </div>

    <!-- ITEMS -->
    <div v-else class="space-y-5">

      <div
        v-for="item in cartItems"
        :key="item.id"
        class="flex gap-4 border-b pb-5"
      >

        <!-- IMAGE -->
        <img
          :src="`/storage/${item.image}`"
          class="w-24 h-24 object-cover rounded-xl"
        />

        <!-- DETAILS -->
        <div class="flex-1">

          <p class="font-semibold text-gray-800">
            {{ item.name }}
          </p>

          <p class="text-sm text-gray-500">
            KES {{ formatPrice(item.price) }}
          </p>

          <!-- QUANTITY -->
          <div class="flex items-center gap-3 mt-3">

            <button @click="handleDecrease(item)" class="w-8 h-8 bg-gray-100 rounded">
              <Minus class="w-4 h-4 mx-auto" />
            </button>

            <span class="font-semibold">{{ item.quantity }}</span>

            <button @click="handleIncrease(item)" class="w-8 h-8 bg-gray-100 rounded">
              <Plus class="w-4 h-4 mx-auto" />
            </button>

          </div>

        </div>

        <!-- PRICE + REMOVE -->
        <div class="text-right">

          <p class="font-bold text-blue-600">
            KES {{ formatPrice(item.price * item.quantity) }}
          </p>

          <button
            @click="handleRemove(item)"
            class="text-xs text-red-500 mt-2"
          >
            Remove
          </button>

        </div>

      </div>

    </div>

  </div>

</div>







  <!-- RIGHT: SUMMARY -->
  <div class="w-[320px] p-6 bg-gray-50 flex flex-col h-full">



<!-- RECOMMENDATIONS -->
<div class="mb-6">

  <!-- HEADER -->
  <div class="flex items-center justify-between mb-4">

    <div>
      <h3 class="text-sm font-bold text-gray-800">
        Recommended for you
      </h3>

      <p class="text-[11px] text-gray-500">
        Based on your cart items
      </p>
    </div>

    <Sparkles class="w-4 h-4 text-blue-600" />

  </div>

  <!-- PRODUCTS -->
  <div class="space-y-3 max-h-[180px] overflow-y-auto pr-1">

    <div
      v-for="item in relatedProducts"
      :key="item.id"
      class="group flex gap-3 bg-white p-3 rounded-xl border border-gray-100 hover:border-blue-200 hover:shadow-md transition-all duration-300"
    >

      <!-- IMAGE -->
      <div class="overflow-hidden rounded-lg">

        <img
          :src="`/storage/${item.images[0]?.image_path}`"
          class="w-16 h-16 object-cover rounded-lg group-hover:scale-105 transition duration-300"
        />

      </div>

      <!-- INFO -->
      <div class="flex-1 min-w-0">

        <p class="text-sm font-medium text-gray-800 line-clamp-2">
          {{ item.name }}
        </p>

        <p class="text-sm text-blue-600 font-bold mt-1">
          KES {{ Number(item.sale_price).toLocaleString() }}
        </p>

      

      </div>

      <!-- ACTION -->
      <button
        @click="addToCart(item.id)"
        class="self-center bg-blue-600 hover:bg-blue-700 text-white text-xs px-3 py-2 rounded-lg transition flex items-center gap-1"
      >
        <Plus class="w-3 h-3" />
        Add
      </button>

    </div>

  </div>

</div>





    <div>

      <h3 class="text-lg font-bold mb-4">Summary</h3>

      <div class="space-y-3 text-sm">

        <div class="flex justify-between">
          <span>Subtotal</span>
          <span>KES {{ formatPrice(cartTotal) }}</span>
        </div>

        <div class="flex justify-between">
          <span>Delivery</span>
          <span>
            {{ deliveryFee === 0 ? 'FREE' : 'KES ' + formatPrice(deliveryFee) }}
          </span>
        </div>

        <div class="border-t pt-3 flex justify-between font-bold text-lg">
          <span>Total</span>
          <span class="text-blue-600">
            KES {{ formatPrice(grandTotal) }}
          </span>
        </div>

      </div>

      <p v-if="remainingForFreeDelivery > 0" class="text-xs text-blue-600 mt-3">
        Add KES {{ formatPrice(remainingForFreeDelivery) }} for FREE delivery
      </p>

    </div>

    <!-- ACTIONS -->
    <div class="space-y-2 mt-6">

      <button
        @click="showCartDrawer = false"
        class="w-full border py-2 rounded-lg"
      >
        Continue Shopping
      </button>

      <button
        @click="goToCheckout"
        class="w-full bg-blue-600 text-white py-3 rounded-lg"
      >
        Checkout
      </button>

    </div>

  </div>

</div>
















<!-- Overlay -->

<div
  v-if="showSidebar"
  @click="showSidebar = false"
  class="fixed inset-0 bg-black/40 z-50"
></div>

<!-- Sidebar -->
<div
  :class="[
    'fixed top-0 left-0 h-full w-72 bg-white overflow-y-auto shadow-xl z-50 transform transition-transform duration-300',
    showSidebar ? 'translate-x-0' : '-translate-x-full'
  ]"
>

  <!-- Header -->
  <div class="flex items-center justify-between p-4 border-b">
    <div class="flex items-center gap-2">
      <LayoutGrid class="w-5 h-5" />
      <h2 class="font-semibold text-lg">Categories</h2>
    </div>
    <button @click="showSidebar = false">✕</button>
  </div>

  <!-- Categories -->
  <div class="p-4 space-y-2">
    <div class="hover:bg-gray-100 p-2 rounded cursor-pointer">Electronics</div>
    <div class="hover:bg-gray-100 p-2 rounded cursor-pointer">Computers</div>
    <div class="hover:bg-gray-100 p-2 rounded cursor-pointer">Phones</div>
    <div class="hover:bg-gray-100 p-2 rounded cursor-pointer">Accessories</div>
  </div>

  <!-- Brands Section -->
  <div class="px-4 pb-4">

    <!-- Title -->
    <div class="flex items-center gap-2 mb-3 border-b pb-2">
      <Tag class="w-5 h-5 text-gray-600" />
      <h2 class="text-lg font-semibold text-gray-800">Brands</h2>
    </div>

    <!-- Brands Grid -->
    <div class="grid grid-cols-1 gap-3">

      <!-- Brand Item -->
      <div class="flex items-center gap-2 p-2 rounded hover:bg-gray-100 cursor-pointer">
        <img :src="AppleLogo" class="w-8 h-8 object-contain border rounded-full p-1" />
        <span class="text-sm">Apple</span>
      </div>
 
      <div class="flex items-center gap-2 p-2 rounded hover:bg-gray-100 cursor-pointer">
        <img :src="LenovoLogo" class="w-8 h-8 object-contain border rounded-full p-1" />
        <span class="text-sm">Lenovo</span>
      </div>

      <div class="flex items-center gap-2 p-2 rounded hover:bg-gray-100 cursor-pointer">
        <img :src="InfinixLogo" class="w-8 h-8 object-contain border rounded-full p-1" />
        <span class="text-sm">Infinix</span>
      </div>

      <div class="flex items-center  gap-2 p-2 rounded hover:bg-gray-100 cursor-pointer">
        <img :src="HPLogo" class="w-8 h-8 object-contain border rounded-full p-1" />
        <span class="text-sm">HP</span>
      </div>

    </div>

  </div>

</div>



<!-- COMPARE TRAY -->
<transition name="slide-up">

  <div
    v-if="compareItems.length && showCompareTray && !isComparePage"
    class="fixed bottom-4 left-1/2 -translate-x-1/2 z-50 bg-white border shadow-2xl rounded-2xl px-4 py-3 w-[95%] max-w-2xl"
  >

    <div class="flex items-center justify-between gap-4">

      <!-- PRODUCTS WRAPPER -->
      <div class="flex items-center gap-2 overflow-x-auto">

        <template v-for="(item, index) in compareItems" :key="item.id">

          <!-- PRODUCT CARD -->
          <div class="flex items-center gap-2 bg-gray-50 rounded-xl px-2 py-2 border shrink-0">

            <!-- IMAGE -->
            <img
              :src="`/storage/${item.image}`"
              class="w-12 h-12 rounded-lg object-cover"
            />

            <!-- INFO -->
            <div class="hidden sm:block max-w-[120px]">
              <p class="text-xs font-medium line-clamp-1">
                {{ item.name }}
              </p>

              <p class="text-xs text-blue-600 font-bold">
                KES {{ Number(item.sale_price).toLocaleString() }}
              </p>
            </div>

            <!-- REMOVE -->
            <button
              @click="removeFromCompare(item.id)"
              class="text-gray-400 hover:text-red-500 text-xs ml-1"
            >
              ✕
            </button>

          </div>

          <!-- VS SEPARATOR -->
          <div
            v-if="index < compareItems.length - 1"
            class="text-xs font-bold text-gray-400 px-1"
          >
            VS
          </div>

        </template>

      </div>



<!-- ACTION AREA -->
<div class="flex flex-col items-end">

  <!-- MESSAGE -->
  <p
    v-if="compareItems.length < 2"
    class="text-[11px] text-gray-500 mb-2"
  >
    Select one more product to compare
  </p>

  <!-- BUTTON -->
  <button
    @click="goToCompare"
    :disabled="compareItems.length < 2"
    class="px-5 py-3 rounded-xl text-sm font-medium transition whitespace-nowrap"
    :class="
      compareItems.length < 2
        ? 'bg-gray-200 text-gray-400 cursor-not-allowed'
        : 'bg-blue-600 hover:bg-blue-700 text-white'
    "
  >
    Compare
  </button>

</div>



    </div>

  </div>

</transition>



<!-- Compare Message -->

<!-- COMPARE MESSAGE -->
<transition name="fade">

  <div
    v-if="compareMessage"
    class="fixed bottom-28 left-1/2 -translate-x-1/2 bg-gray-900 text-white text-sm px-4 py-2 rounded-xl shadow-lg z-[100]"
  >
    {{ compareMessage }}
  </div>

</transition>


</template>

<script setup>
import { ref, onMounted, onUnmounted, computed } from 'vue'
import { Search, ShoppingCart, User, Heart, Phone, Menu, ArrowLeft } from 'lucide-vue-next'
import logo from '../assets/images/vidlix.jpeg'
import { Sparkles, LayoutGrid, Tag } from 'lucide-vue-next'
import HPLogo from '@/assets/images/HP_logo.png'
import LenovoLogo from '@/assets/images/lenovo.png'
import InfinixLogo from '@/assets/images/infinix.png'
import AppleLogo from '@/assets/images/Apple-Logo-3.png'
import { usePage } from '@inertiajs/vue3'
import {onBeforeUnmount } from 'vue'
import { router } from '@inertiajs/vue3'
import { useCart } from '@/composables/useCart'
import { Plus, Minus } from 'lucide-vue-next'
import { Loader2 } from 'lucide-vue-next'
import { useCompare } from '@/composables/useCompare'


const page = usePage()
const cartAnimate = ref(false)
const isProductsPage = page.url.startsWith('/productListing')
const isProductPage = page.url.startsWith('/products')
const isCheckoutPage = page.url.startsWith('/checkout')
const isOrderSuccess = page.url.startsWith('/order-success')
const isIndexOrder = page.url.startsWith('/orders')
const isShowOrder = page.url.startsWith('/orders/')
const isPayment = page.url.startsWith('/payment/')
const isComparePage = page.url.startsWith('/compare')
const isAiSearchPage = page.url.startsWith('/ai-search')
const showTopBar = ref(true)
const headerRef = ref(null)
const headerHeight = ref(0)
const showSidebar = ref(false)
const showDropdown = ref(false)
const showCartDrawer = ref(false)
const cartItems = ref([])
const loadingItems = ref([])
const relatedProducts = ref([])

const handleScroll = () => {
  showTopBar.value = window.scrollY <= 50
}

onMounted(() => {
  window.addEventListener('scroll', handleScroll)
  if (headerRef.value) {
    headerHeight.value = headerRef.value.offsetHeight
  }
})

onUnmounted(() => window.removeEventListener('scroll', handleScroll))






const handleClickOutside = (e) => {
  if (!e.target.closest('.relative')) {
    showDropdown.value = false
  }
}

onMounted(() => {
  window.addEventListener('click', handleClickOutside)
})

onBeforeUnmount(() => {
  window.removeEventListener('click', handleClickOutside)
})


const logout = () => {
  router.post('/logout')
}



const { 
  getCartCount,
  getCart,
  increaseQty,
  decreaseQty,
  removeItem,
  getCartRecommendations,
} = useCart()

const loadRecommendations = async () => {
  relatedProducts.value = await getCartRecommendations()
}







const cartCount = ref(0)

const updateCartCount = async () => {
  cartCount.value = await getCartCount()

  // 🔥 trigger animation
  cartAnimate.value = true

  // reset after animation ends
  setTimeout(() => {
    cartAnimate.value = false
  }, 300)
}

onMounted(() => {
  updateCartCount()
  loadCart()
  loadRecommendations()
  window.addEventListener('cartUpdated', updateCartCount)
  window.addEventListener('cartUpdated', syncCart)
})



onBeforeUnmount(() => {
  window.removeEventListener('cartUpdated', updateCartCount)
  window.removeEventListener('cartUpdated', syncCart)
})



const loadCart = async () => {
 cartItems.value = await getCart()
}


const syncCart = () => {
  loadCart()
}

onMounted(() => {
  loadCart()
  window.addEventListener('cartUpdated', syncCart)
})

onBeforeUnmount(() => {
  window.removeEventListener('cartUpdated', syncCart)
})


const cartTotal = computed(() => {
  return cartItems.value.reduce((sum, item) => {
    return sum + item.price * item.quantity
  }, 0)
})


const formatPrice = (value) => {
  return new Intl.NumberFormat().format(value)
}


const deliveryFee = computed(() => {
  return cartTotal.value >= 2000 ? 0 : 250
})

const remainingForFreeDelivery = computed(() => {
  return Math.max(2000 - cartTotal.value, 0)
})


const grandTotal = computed(() => {
  return cartTotal.value + deliveryFee.value
})


// CART HANDLERS
const handleIncrease = async (item) => {
  // ⚡ instant UI update
  item.quantity++

  try {
    await increaseQty(item.product_id)
  } catch (error) {
    
    item.quantity--
  }
}

const handleDecrease = async (item) => {
  if (item.quantity > 1) {
    // ⚡ instant update
    item.quantity--

    try {
      await decreaseQty(item.id, item.quantity + 1)
    } catch (error) {
      item.quantity++ // rollback
    }
  } else {
    // ⚡ remove instantly
    const index = cartItems.value.findIndex(i => i.id === item.id)
    const removed = cartItems.value[index]

    cartItems.value.splice(index, 1)

    try {
      await removeItem(item.id)
    } catch (error) {
      // rollback
      cartItems.value.splice(index, 0, removed)
    }
  }
}


const handleRemove = async (item) => {
  const index = cartItems.value.findIndex(i => i.id === item.id)
  const removed = cartItems.value[index]

  // ⚡ instant remove
  cartItems.value.splice(index, 1)

  try {
    await removeItem(item.id)
  } catch (error) {
    // rollback
    cartItems.value.splice(index, 0, removed)
  }
}


const goToCheckout = () => {
  router.visit(route('checkout'))
}


//COMPARE Hendlers 

const {
  compareItems,
  showCompareTray,
  removeFromCompare,
  openCompareTray,
  compareMessage,
} = useCompare()


const goToCompare = () => {

  router.get(
    route('compare.index'),
    {
      products: compareItems.value.map(
        item => item.id
      )
    }
  )

}


</script>

<style scoped>
.slide-up-enter-active,
.slide-up-leave-active {
  transition: all 0.3s ease;
}
.slide-up-enter-from,
.slide-up-leave-to {
  opacity: 0;
  transform: translateY(-100%);
}



.fade-enter-active,
.fade-leave-active {
  transition: all 0.3s ease;
}

.fade-enter-from,
.fade-leave-to {
  opacity: 0;
  transform: translate(-50%, 10px);
}




</style>