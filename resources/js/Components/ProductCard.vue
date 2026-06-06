
<template>
  <div class="bg-white rounded-xl border border-gray-100 hover:shadow-md transition overflow-hidden group h-[340px] flex flex-col">

    <!-- IMAGE -->
    <div class="relative h-40 bg-gray-100 overflow-hidden">
<Link
 v-if="product?.slug"
 :href="route('products.show', { product: product.slug })">
      <img
         :src="product.images?.length 
    ? `/storage/${product.images[0].image_path}` 
    : '/placeholder.png'"
        class="w-full h-full object-cover group-hover:scale-105 transition duration-300"
      />
</Link>
      <!-- AI BADGE -->
      <span
        v-if="showAiBadge"
        class="absolute top-2 left-2 flex items-center gap-1 text-[10px] bg-blue-100 text-blue-600 px-2 py-0.5 rounded-full"
      >
        <Sparkles class="w-3 h-3" />
        AI Pick
      </span>

      <!-- Wishlist -->
      <button class="absolute top-2 right-2 bg-white p-1.5 rounded-full shadow hover:text-red-500 transition">
        <Heart class="w-4 h-4" />
      </button>

    </div>

    <!-- CONTENT -->
    <div class="p-3 flex flex-col flex-1">

      <!-- NAME -->

      <Link 
      v-if="product?.slug"
      :href="route('products.show', { product: product.slug })"
      class="hover:text-blue-600 transition"
      >
      <h3 
       
      class=" font-medium text-gray-800 line-clamp-2 leading-tight">
        {{ product.name }}
      </h3>
    </Link>
      <!-- PRICE -->
      <p class="mt-1 text-sm text-blue-600 font-semibold">
        KES {{ formatPrice(product.sale_price) }}
      </p>

      <!-- ⭐ RATING -->
      <div class="flex items-center gap-1 mt-1 text-yellow-400">
        <Star class="w-3 h-3 fill-yellow-400" />
        <Star class="w-3 h-3 fill-yellow-400" />
        <Star class="w-3 h-3 fill-yellow-400" />
        <Star class="w-3 h-3 fill-yellow-400" />
        <Star class="w-3 h-3 text-gray-300" />
        <span class="text-[10px] text-gray-500 ml-1">(24)</span>
      </div>

      <!-- PUSH ACTIONS DOWN -->
      <div class="mt-auto pt-2 space-y-2">

        <!-- Compare -->
        <label class="flex items-center gap-1 text-[11px] text-gray-600 cursor-pointer">
  <input
  type="checkbox"
  class="accent-blue-600"
  :checked="isInCompare(product.id)"
  @change="handleCompare"
  />
          Compare
        </label>

        <!-- Add to Cart -->
    <!-- 🛒 CART SECTION -->
<div>

  <!-- 🟢 NOT IN CART -->
  <button
    v-if="!cartItem"
    @click="handleAdd"
    class="w-full flex items-center justify-center gap-1 bg-blue-600 hover:bg-blue-700 text-white px-2 py-2 rounded-lg transition"
  >
    <ShoppingCart class="w-4 h-4" />
    Add to Cart
  </button>

  <!-- 🔵 IN CART -->
  <div
    v-else
    class="w-full flex items-center justify-between text-white px-2 py-2 rounded-lg"
  >

    <!-- MINUS -->
    <button
      @click="handleDecrease"
       :disabled="loading"
      class="px-2 py-2 bg-blue-600 hover:bg-blue-700 rounded transition"
    >
      <Minus class="w-4 h-4" />
    </button>

    <!-- QUANTITY -->
   <span class="text-sm font-semibold flex items-center justify-center">

  <!-- 🔄 LOADING -->
  <Loader2
    v-if="loading"
    class="w-4 h-4 text-blue-600 animate-spin"
  />

  <!-- 🔢 NORMAL -->
  <span v-else class="text-black">
    {{ cartItem?.quantity }}
  </span>

</span>

    <!-- PLUS -->
    <button
      @click="handleIncrease"
       :disabled="loading"
      class="px-2 py-2 bg-blue-600 hover:bg-blue-700 rounded transition"
    >
      <Plus class="w-4 h-4" />
    </button>

  </div>

</div>

      </div>

    </div>

  </div>
</template>




<script setup>
import { Sparkles, Heart, Star, ShoppingCart } from 'lucide-vue-next'
import { computed, ref } from 'vue'
import { Link } from '@inertiajs/vue3'
import { Plus, Minus } from 'lucide-vue-next'
import { Loader2 } from 'lucide-vue-next'
import { onMounted } from 'vue'
import { onBeforeUnmount } from 'vue'
import { useCart } from '@/composables/useCart'
import { useCompare } from '@/composables/useCompare'

const loading = ref(false)
// ✅ 1. define props FIRST
const props = defineProps({
  product: Object,
  showAiBadge: Boolean
})

// ✅ 2. useCart AFTER props
const { addToCart, increaseQty, decreaseQty, getCartItem, removeItem } = useCart()
const { toggleCompare, isInCompare } = useCompare()
// ✅ 3. reactivity helper
const refresh = ref(0)

// ✅ 4. computed AFTER everything is defined

const cartItem = ref(null)





// ✅ 5. handlers
const handleAdd = async () => {
  loading.value = true

  await addToCart(props.product.id)

  // ⚡ instant UI update
cartItem.value = await getCartItem(props.product.id)

  loading.value = false
}



const handleIncrease = async () => {
  loading.value = true

  await increaseQty(props.product.id)

  // ⚡ instant update
  cartItem.value.quantity++

  loading.value = false
}




const handleDecrease = async () => {
  loading.value = true

  if (cartItem.value.quantity > 1) {
    await decreaseQty(cartItem.value.id, cartItem.value.quantity)
    cartItem.value.quantity--
  } else {
    await removeItem(cartItem.value.id)
    cartItem.value = null
  }

  loading.value = false
}


const formatPrice = (value) => {
  return new Intl.NumberFormat().format(value)
}








onMounted(async () => {
  cartItem.value = await getCartItem(props.product.id)
})




// Compare handlers

const handleCompare = () => {

  toggleCompare({
    id: props.product.id,
    name: props.product.name,
    slug: props.product.slug,
    sale_price: props.product.sale_price,
    category_id: props.product.category_id,
    image: props.product.images?.[0]?.image_path
  })

}






</script>