<script setup>
import { ref, onMounted } from 'vue'

import Navbar from '@/Components/Navigation/Navbar.vue'
import Footer from '@/Components/Footer.vue'
import CategorySidebar from '@/Components/Navigation/CategorySidebar.vue'
import CartDrawer from '@/Components/CartDrawer.vue'
import WishlistDrawer from '@/Components/WishlistDrawer.vue'
import CompareTray from '@/Components/CompareTray.vue'

import { useCart } from '@/composables/useCart'
import { useCompare } from '@/composables/useCompare'

const showCartDrawer = ref(false)
const wishlistOpen = ref(false)
const showSidebar = ref(false)
const headerHeight = ref(0)

const { processPendingCart, cartMessage } = useCart()
const { compareMessage } = useCompare()

const updateHeaderHeight = (height) => {
  headerHeight.value = height
}

onMounted(async () => {
  await processPendingCart()
})
</script>

<template>
  <div class="min-h-screen flex flex-col bg-white">

    <!-- NAVBAR -->
    <Navbar
      @open-cart="showCartDrawer = true"
      @open-wishlist="wishlistOpen = true"
      @open-sidebar="showSidebar = true"
      @height-change="updateHeaderHeight"
    />

    <!-- Reserve space for the fixed navbar -->
    <div
      :style="{ height: `${headerHeight}px` }"
      class="shrink-0"
    />

    <!-- PAGE CONTENT -->
    <main class="flex-1 max-w-7xl mx-auto w-full">
      <slot />
    </main>

    <!-- FOOTER -->
    <Footer />

  </div>

  <!-- CART DRAWER -->
  <CartDrawer
    :open="showCartDrawer"
    @close="showCartDrawer = false"
  />

  <!-- CATEGORY SIDEBAR -->
  <CategorySidebar
    :open="showSidebar"
    @close="showSidebar = false"
  />

  <!-- WISHLIST DRAWER -->
  <WishlistDrawer
    :open="wishlistOpen"
    @close="wishlistOpen = false"
  />

  <!-- COMPARE TRAY -->
  <CompareTray />

  <!-- COMPARE MESSAGE -->
  <Transition name="fade">
    <div
      v-if="compareMessage"
      class="fixed bottom-28 left-1/2 -translate-x-1/2
             bg-gray-900 text-white text-sm px-4 py-2
             rounded-xl shadow-lg z-[100]"
    >
      {{ compareMessage }}
    </div>
  </Transition>

  <!-- CART MESSAGE -->
  <Transition
    enter-active-class="transition duration-300"
    enter-from-class="opacity-0 translate-y-3"
    enter-to-class="opacity-100 translate-y-0"
    leave-active-class="transition duration-300"
    leave-from-class="opacity-100 translate-y-0"
    leave-to-class="opacity-0 translate-y-3"
  >
    <div
      v-if="cartMessage"
      class="fixed bottom-6 left-1/2 -translate-x-1/2
             bg-red-500 text-white px-6 py-3
             rounded-xl shadow-lg z-50"
    >
      {{ cartMessage }}
    </div>
  </Transition>
</template>

<style scoped>
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