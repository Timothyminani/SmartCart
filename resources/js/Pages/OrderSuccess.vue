<template>
  <AppLayout>
    <div class="flex flex-col  max-w-3xl mx-auto px-6 py-16 space-y-6">

      <!-- ================= SUCCESS ================= -->
      <div class="bg-white rounded-2xl p-8 shadow-sm text-center space-y-4">

        <div class="w-16 h-16 mx-auto rounded-full bg-green-100 flex items-center justify-center">
          <CheckCircle class="w-8 h-8 text-green-600" />
        </div>

        <h1 class="text-xl font-semibold text-gray-800">
          Order Confirmed
        </h1>

        <p class="text-sm text-gray-500">
          Your order has been placed successfully
        </p>

        <p class="text-sm font-medium text-blue-600">
          Order #{{ order.id }}
        </p>

      </div>

      <!-- ================= ORDER SUMMARY ================= -->
      <div class="bg-white rounded-2xl p-6 shadow-sm space-y-4">

        <h2 class="text-sm font-semibold text-gray-700">
          Order Summary
        </h2>

        <div class="space-y-3">

          <div
            v-for="item in order.items"
            :key="item.id"
            class="flex items-center gap-3"
          >
            <!-- ✅ SAFE IMAGE FIX -->
          <img
            :src="getImage(item.product.images?.[0]?.image_path)"
            class="w-14 h-14 object-cover rounded-lg border"
          />

      

            <p class="text-sm text-gray-700 line-clamp-1">
              {{ item.product.name }}
            </p>
          </div>

        </div>

        <div class="border-t pt-4 flex justify-between text-sm font-medium">
          <span>Total</span>
          <span class="text-blue-600">
            KES {{ formatPrice(order.total_amount) }}
          </span>
        </div>

      </div>

      <!-- ================= DELIVERY ================= -->
      <div class="bg-white rounded-2xl p-6 shadow-sm space-y-3">

        <h2 class="text-sm font-semibold text-gray-700">
          Delivery Details
        </h2>

        <div class="text-sm text-gray-600 space-y-4">
          <p><span class="font-medium text-gray-800">Shipping to:</span> {{ order.address }}</p>
          <p><span class="font-medium text-gray-800">Phone:</span> {{ order.phone }}</p>
          <p class="capitalize">
            <span class="font-medium text-gray-800">Method:</span>
            {{ order.delivery_method }}
          </p>
          <p class="border border-blue-600 rounded-xl  p-3 bg-blue-600/10">
            <span class="font-medium text-gray-800">Estimated delivery:</span>
            {{ estimatedDelivery }}
          </p>
        </div>

      </div>

      <!-- ================= ORDER PROGRESS (VERTICAL) ================= -->
      <div class="bg-white rounded-2xl p-6 shadow-sm">

        <h2 class="text-sm font-semibold text-gray-700 mb-6">
          Order Progress
        </h2>

        <div class="space-y-6">

          <!-- STEP 1 -->
          <div class="flex gap-4 items-start">
            <div class="flex flex-col items-center">
              <div class="w-10 h-10 bg-green-500 rounded-full flex items-center justify-center text-white">
                <Check class="w-5 h-5" />
              </div>
              <div class="w-px h-10 bg-gray-200"></div>
            </div>

            <div>
              <p class="text-sm font-medium text-gray-800">Order Placed</p>
              <p class="text-xs text-gray-500">We received your order</p>
            </div>
          </div>

          <!-- STEP 2 -->
          <div class="flex gap-4 items-start">
            <div class="flex flex-col items-center">
              <div class="w-10 h-10 bg-blue-600 rounded-full flex items-center justify-center text-white">
                <Check class="w-5 h-5" />
              </div>
              <div class="w-px h-10 bg-gray-200"></div>
            </div>

            <div>
              <p class="text-sm font-medium text-gray-800">Processing</p>
              <p class="text-xs text-gray-500">Preparing your items</p>
            </div>
          </div>

          <!-- STEP 3 -->
          <div class="flex gap-4 items-start">
            <div class="flex flex-col items-center">
              <div class="w-10 h-10 bg-gray-100 border rounded-full flex items-center justify-center">
                <Truck class="w-5 h-5 text-gray-500" />
              </div>
              <div class="w-px h-10 bg-gray-200"></div>
            </div>

            <div>
              <p class="text-sm font-medium text-gray-500">Shipping</p>
              <p class="text-xs text-gray-400">On the way</p>
            </div>
          </div>

          <!-- STEP 4 -->
          <div class="flex gap-4 items-start">
            <div class="w-10 h-10 bg-gray-100 border rounded-full flex items-center justify-center">
              <Home class="w-5 h-5 text-gray-500" />
            </div>

            <div>
              <p class="text-sm font-medium text-gray-500">Delivered</p>
              <p class="text-xs text-gray-400">Final step</p>
            </div>
          </div>

        </div>

      </div>

      <!-- ================= ACTIONS ================= -->
      <div class="flex flex-col gap-3">

            <Link
              href="/orders"
              class="bg-blue-600 text-white py-3 rounded-xl text-center hover:bg-blue-700 transition"
            >
              View My Orders
            </Link>


        <Link
          href="/"
          class="border py-3 rounded-xl text-center hover:bg-gray-50 transition"
        >
          Continue Shopping
        </Link>

      </div>

    </div>
  </AppLayout>
</template>

<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'
import { computed } from 'vue'
import { Link } from '@inertiajs/vue3'
import {
  CheckCircle,
  Check,
  Truck,
  Home
} from 'lucide-vue-next'

const props = defineProps({
  order: Object
})

/* ✅ IMAGE FIX (ROBUST) */
const getImage = (image) => {
  if (!image) return '/placeholder.png'

  if (image.startsWith('http')) return image
  if (image.startsWith('storage')) return `/${image}`

  return `/storage/${image}`
}

/* DELIVERY */
const estimatedDelivery = computed(() => {
  const method = props.order?.delivery_method

  if (method === 'express') return 'Today'
  if (method === 'standard') return '2–3 days'
  if (method === 'pickup') return 'Ready for pickup today'

  return 'Processing...'
})

/* PRICE */
const formatPrice = (value) => {
  return new Intl.NumberFormat().format(value || 0)
}
</script>