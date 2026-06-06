<template>
  <AppLayout>
    <div class="max-w-7xl mx-auto px-6 py-12 grid lg:grid-cols-2 gap-12">

      <!-- 📝 LEFT -->
      <div class="space-y-8">

        <!-- DELIVERY CARD -->
        <div class="bg-white p-6 rounded-2xl shadow-sm border space-y-6">

          <h2 class="text-xl font-semibold text-gray-800">
            Delivery Information
          </h2>

          <!-- PHONE -->
          <div>
            <label class="text-sm text-gray-600">Phone Number</label>
            <input
              v-model="form.phone"
              type="text"
              placeholder="07XXXXXXXX"
              class="w-full border rounded-lg px-3 py-2 mt-1 focus:ring-2 focus:ring-blue-500 outline-none"
            />
          </div>

          <!-- ADDRESS -->
          <div>
            <label class="text-sm text-gray-600">Delivery Address</label>
            <textarea
              v-model="form.address"
              rows="3"
              placeholder="e.g. Nairobi, Westlands, near ABC Place..."
              class="w-full border rounded-lg px-3 py-2 mt-1 focus:ring-2 focus:ring-blue-500 outline-none"
            ></textarea>
          </div>
        </div>

        <!-- DELIVERY OPTIONS -->
        <div class="bg-white p-6 rounded-2xl shadow-sm border space-y-4">

          <h2 class="text-lg font-semibold text-gray-800">
            Delivery Options
          </h2>

          <div class="space-y-3">

            <label
              class="flex items-center justify-between border rounded-xl px-4 py-3 cursor-pointer hover:border-blue-500 transition"
              :class="form.delivery === 'standard' && 'border-blue-600 bg-blue-50'"
            >
              <div class="flex items-center gap-3">
                <input type="radio" value="standard" v-model="form.delivery" />
                <div>
                  <p class="text-sm font-medium">Standard Delivery</p>
                  <p class="text-xs text-gray-500">2-4 business days</p>
                </div>
              </div>
              <span class="text-sm">KES 250</span>
            </label>

            <label
              class="flex items-center justify-between border rounded-xl px-4 py-3 cursor-pointer hover:border-blue-500 transition"
              :class="form.delivery === 'express' && 'border-blue-600 bg-blue-50'"
            >
              <div class="flex items-center gap-3">
                <input type="radio" value="express" v-model="form.delivery" />
                <div>
                  <p class="text-sm font-medium">Express Delivery</p>
                  <p class="text-xs text-gray-500">Same day delivery</p>
                </div>
              </div>
              <span class="text-sm">KES 500</span>
            </label>

            <label
              class="flex items-center justify-between border rounded-xl px-4 py-3 cursor-pointer hover:border-blue-500 transition"
              :class="form.delivery === 'pickup' && 'border-blue-600 bg-blue-50'"
            >
              <div class="flex items-center gap-3">
                <input type="radio" value="pickup" v-model="form.delivery" />
                <div>
                  <p class="text-sm font-medium">Pickup Station</p>
                  <p class="text-xs text-gray-500">Collect from our store</p>
                </div>
              </div>
              <span class="text-sm text-green-600 font-medium">FREE</span>
            </label>

          </div>

        </div>



<!-- PAYMENT METHODS -->
<div class="bg-white p-6 rounded-2xl shadow-sm border space-y-4">

  <h2 class="text-lg font-semibold text-gray-800">
    Payment Method
  </h2>

  <div class="space-y-3">

    <!-- COD -->
    <label
      class="flex items-start justify-between border rounded-xl px-4 py-4 cursor-pointer transition"
      :class="form.payment_method === 'cod'
        ? 'border-blue-600 bg-blue-50'
        : 'hover:border-blue-400'"
    >

      <div class="flex gap-3">

        <input
          type="radio"
          value="cod"
          v-model="form.payment_method"
          class="mt-1"
        />

        <div>
          <p class="text-sm font-medium text-gray-800">
            Cash on Delivery
          </p>

          <p class="text-xs text-gray-500 mt-1">
            Pay when your order arrives
          </p>
        </div>

      </div>

      <Truck class="w-5 h-5 text-blue-600" />

    </label>

    <!-- Online Payment -->
    <label
      class="flex items-start justify-between border rounded-xl px-4 py-4 cursor-pointer transition"
      :class="form.payment_method === 'Online Payment'
        ? 'border-blue-600 bg-blue-50'
        : 'hover:border-blue-400'"
    >

      <div class="flex gap-3">

        <input
          type="radio"
          value="Online Payment"
          v-model="form.payment_method"
          class="mt-1"
        />

        <div>
          <p class="text-sm font-medium text-gray-800">
            Online Payment (MPESA)
          </p>

          <p class="text-xs text-gray-500 mt-1">
            1. After placing your order, you will be redirected to the payment page
          </p>
        </div>

      </div>

       <ShieldCheck class="w-5 h-5 text-green-600" />
    </label>

  </div>

</div>






        <!-- BUTTON -->
        <button
          @click="placeOrder"
          :disabled="loading || !cartItems.length"
          class="w-full bg-gradient-to-r from-blue-600 to-blue-500 text-white py-3 rounded-xl hover:shadow-lg transition flex items-center justify-center gap-2 disabled:opacity-50"
        >
          <Loader2 v-if="loading" class="w-5 h-5 animate-spin" />
          <span>
  {{
    loading
      ? 'Processing...'
      : form.payment_method === 'Online Payment'
        ? 'Continue to Payment'
        : 'Place Order'
  }}
</span>
        </button>

        <!-- TRUST -->
    <!-- TRUST / ASSURANCE -->
<div class="bg-gray-50 border rounded-xl p-4 space-y-4">

  <h3 class="text-sm font-semibold text-gray-800">
    Why shop with us?
  </h3>

  <div class="space-y-3">

    <!-- SECURE -->
    <div class="flex items-start gap-3">
      <ShieldCheck class="w-5 h-5 text-green-600 mt-0.5" />
      <div>
        <p class="text-sm font-medium text-gray-800">Secure Checkout</p>
        <p class="text-xs text-gray-500">
          Your information is protected and encrypted
        </p>
      </div>
    </div>

    <!-- COD -->
    <div class="flex items-start gap-3">
      <Truck class="w-5 h-5 text-blue-600 mt-0.5" />
      <div>
        <p class="text-sm font-medium text-gray-800">Cash on Delivery</p>
        <p class="text-xs text-gray-500">
          Pay only when your order arrives
        </p>
      </div>
    </div>

    <!-- RETURNS -->
    <div class="flex items-start gap-3">
      <RotateCcw class="w-5 h-5 text-orange-500 mt-0.5" />
      <div>
        <p class="text-sm font-medium text-gray-800">Easy Returns</p>
        <p class="text-xs text-gray-500">
          7-day hassle-free return policy
        </p>
      </div>
    </div>

  </div>

</div>

      </div>

      <!-- 🛒 RIGHT -->
      <div class="bg-white p-6 rounded-2xl shadow-sm border space-y-6">

        <h2 class="text-xl font-semibold text-gray-800">
          Order Summary
        </h2>

        <!-- ITEMS -->
        <div v-if="cartItems.length" class="space-y-5">

          <div
            v-for="item in cartItems"
            :key="item.id"
            class="flex gap-4 border-b pb-4"
          >
            <!-- IMAGE -->
            <img
              :src="`/storage/${item.image}`"
              class="w-20 h-20 object-cover rounded-xl"
            />

            <!-- DETAILS -->
            <div class="flex-1">

              <p class="text-sm font-medium text-gray-800 line-clamp-2">
                {{ item.name }}
              </p>

              <p class="text-xs text-gray-500 mt-1">
                KES {{ formatPrice(item.price) }}
              </p>

              <!-- CONTROLS -->
              <div class="flex items-center gap-2 mt-3">

                <button
                  @click="handleDecrease(item)"
                  :disabled="loadingItems.includes(item.id)"
                  class="w-8 h-8 flex items-center justify-center rounded-full bg-gray-100 hover:bg-red-100"
                >
                  <Minus class="w-4 h-4" />
                </button>

                <span class="w-6 text-center text-sm font-semibold">
                  <Loader2
                    v-if="loadingItems.includes(item.id)"
                    class="w-4 h-4 animate-spin mx-auto"
                  />
                  <span v-else>{{ item.quantity }}</span>
                </span>

                <button
                  @click="handleIncrease(item)"
                  :disabled="loadingItems.includes(item.id)"
                  class="w-8 h-8 flex items-center justify-center rounded-full bg-gray-100 hover:bg-green-100"
                >
                  <Plus class="w-4 h-4" />
                </button>

                <button
                  @click="handleRemove(item)"
                  class="text-xs text-red-500 ml-3 hover:underline"
                >
                  Remove
                </button>

              </div>
            </div>

            <!-- PRICE -->
            <p class="text-sm font-semibold text-blue-600">
              KES {{ formatPrice(item.price * item.quantity) }}
            </p>
          </div>

          <!-- TOTAL -->
          <div class="space-y-2 text-sm pt-4">

            <div class="flex justify-between">
              <span class="text-gray-600">Subtotal</span>
              <span>KES {{ formatPrice(total) }}</span>
            </div>

            <div class="flex justify-between">
              <span class="text-gray-600">Delivery</span>
              <span>
                KES {{ formatPrice(deliveryFee) }}
              </span>
            </div>

            <div class="flex justify-between text-lg font-bold border-t pt-3">
              <span>Total</span>
              <span class="text-blue-600">
                KES {{ formatPrice(grandTotal) }}
              </span>
            </div>

          </div>

        </div>

        <!-- EMPTY -->
        <div v-else class="text-gray-500 text-sm">
          Your cart is empty
        </div>

      </div>

    </div>
  </AppLayout>
</template>

<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'
import { ref, onMounted, computed } from 'vue'
import axios from 'axios'
import { useCart } from '@/composables/useCart'
import { Loader2, Plus, Minus, ShieldCheck, Truck, RotateCcw } from 'lucide-vue-next'
import mpesaLogo from '@/assets/images/mpesa-logo.jpg'

const { getCart, increaseQty, decreaseQty, removeItem } = useCart()

// STATE
const cartItems = ref([])
const loading = ref(false)
const loadingItems = ref([])

// FORM
const form = ref({
  phone: '',
  address: '',
  delivery: 'standard',
  payment_method: 'cod'
})

// LOAD CART
onMounted(async () => {
  try {
    cartItems.value = await getCart()

    if (!cartItems.value.length) {
      window.location.href = '/'
    }
  } catch (error) {
    console.error('Error loading cart:', error)
  }
})

// =========================
// CART HANDLERS
// =========================
const handleIncrease = async (item) => {
  if (loadingItems.value.includes(item.id)) return

  loadingItems.value.push(item.id)

  try {
    item.quantity++
    await increaseQty(item.product_id)
  } catch (error) {
    console.error('Increase error:', error)
    item.quantity-- // rollback
  }

  loadingItems.value = loadingItems.value.filter(id => id !== item.id)
}

const handleDecrease = async (item) => {
  if (loadingItems.value.includes(item.id)) return

  loadingItems.value.push(item.id)

  try {
    if (item.quantity > 1) {
      item.quantity--
      await decreaseQty(item.id, item.quantity + 1)
    } else {
      await removeItem(item.id)
      cartItems.value = cartItems.value.filter(i => i.id !== item.id)
    }
  } catch (error) {
    console.error('Decrease error:', error)
  }

  loadingItems.value = loadingItems.value.filter(id => id !== item.id)
}

const handleRemove = async (item) => {
  if (loadingItems.value.includes(item.id)) return

  loadingItems.value.push(item.id)

  try {
    await removeItem(item.id)
    cartItems.value = cartItems.value.filter(i => i.id !== item.id)
  } catch (error) {
    console.error('Remove error:', error)
  }

  loadingItems.value = loadingItems.value.filter(id => id !== item.id)
}

// =========================
// TOTALS
// =========================
const total = computed(() => {
  return cartItems.value.reduce((sum, item) => {
    return sum + (item.price * item.quantity)
  }, 0)
})

const deliveryFee = computed(() => {
  switch (form.value.delivery) {
    case 'express':
      return 500
    case 'standard':
      return 250
    case 'pickup':
      return 0
    default:
      return total.value >= 2000 ? 0 : 250
  }
})

const grandTotal = computed(() => {
  return total.value + deliveryFee.value
})

const formatPrice = (value) => {
  return new Intl.NumberFormat().format(value)
}

// =========================
// PLACE ORDER
// =========================
const placeOrder = async () => {
  if (!form.value.phone || !form.value.address) {
    alert('Please fill all fields')
    return
  }

  loading.value = true

  try {
    const response = await axios.post('/checkout', {
      phone: form.value.phone,
      address: form.value.address,
      delivery: form.value.delivery,
      payment_method: form.value.payment_method
    })

   if (form.value.payment_method === 'cod') {

  window.location.href =
    `/order-success/${response.data.order_id}`

} else {

  window.location.href =
    `/payment/${response.data.payment_id}`

}


  } catch (e) {
    console.log(e.response.data)
  }

  loading.value = false
}
</script>