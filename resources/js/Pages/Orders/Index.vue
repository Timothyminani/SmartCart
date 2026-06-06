<template>
  <AppLayout>

    <div class="max-w-6xl mx-auto px-6 py-12">

      <!-- PAGE TITLE -->
      <div class="mb-8">
        <h1 class="text-3xl font-semibold text-gray-800">
          My Orders
        </h1>

        <p class="text-sm text-gray-500 mt-2">
          Track and manage your orders
        </p>
      </div>

      <!-- EMPTY STATE -->
      <div
        v-if="orders.length === 0"
        class="bg-white rounded-3xl p-12 text-center shadow-sm border"
      >
        <Package class="w-16 h-16 text-gray-300 mx-auto mb-4" />

        <h2 class="text-xl font-medium text-gray-700">
          No orders yet
        </h2>

        <p class="text-sm text-gray-500 mt-2">
          Your placed orders will appear here
        </p>

        <Link
          href="/"
          class="inline-flex items-center justify-center mt-6 bg-blue-600 text-white px-6 py-3 rounded-xl hover:bg-blue-700 transition"
        >
          Start Shopping
        </Link>
      </div>

      <!-- ORDERS TABLE -->
      <div
        v-else
        class="bg-white rounded-3xl shadow-sm border overflow-hidden"
      >

        <div class="overflow-x-auto">

          <table class="w-full min-w-[850px]">

            <!-- TABLE HEAD -->
            <thead class="bg-gray-50 border-b">

              <tr class="text-left">

                <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wide">
                  Order
                </th>

                <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wide">
                  Date
                </th>

                <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wide">
                  Products
                </th>

                <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wide">
                  Total
                </th>

                <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wide">
                  Status
                </th>

                <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wide text-right">
                  Action
                </th>

              </tr>

            </thead>

            <!-- TABLE BODY -->
            <tbody>

              <tr
                v-for="order in orders"
                :key="order.id"
                class="border-b last:border-b-0 hover:bg-gray-50 transition"
              >

                <!-- ORDER ID -->
                <td class="px-6 py-5">

                  <div>
                    <p class="font-semibold text-gray-800">
                      #{{ order.id }}
                    </p>

                    <p class="text-xs text-gray-400 mt-1">
                      {{ order.items.length }} items
                    </p>
                  </div>

                </td>

                <!-- DATE -->
                <td class="px-6 py-5">

                  <p class="text-sm text-gray-600">
                    {{ formatDate(order.created_at) }}
                  </p>

                </td>

                <!-- PRODUCTS -->
                <td class="px-6 py-5">

                  <div class="flex items-center -space-x-3">

                    <img
                      v-for="item in order.items.slice(0, 4)"
                      :key="item.id"
                      :src="getImage(item.product.images?.[0]?.image_path)"
                      class="w-12 h-12 rounded-full border-2 border-white object-cover bg-gray-100"
                    />

                  </div>

                </td>

                <!-- TOTAL -->
                <td class="px-6 py-5">

                  <p class="font-semibold text-blue-600">
                    KES {{ formatPrice(order.total_amount) }}
                  </p>

                </td>

                <!-- STATUS -->
                <td class="px-6 py-5">

                  <span
                    class="px-3 py-1 rounded-full text-xs font-medium capitalize"
                    :class="statusClasses(order.status)"
                  >
                    {{ order.status || 'processing' }}
                  </span>

                </td>

                <!-- ACTION -->
                <td class="px-6 py-5 text-right">

                  <Link
                    :href="`/orders/${order.id}`"
                    class="inline-flex items-center justify-center px-4 py-2 rounded-xl border text-sm hover:bg-gray-50 transition"
                  >
                    View Details
                  </Link>

                </td>

              </tr>

            </tbody>

          </table>

        </div>

      </div>

    </div>

  </AppLayout>
</template>

<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'
import { Link } from '@inertiajs/vue3'
import { Package } from 'lucide-vue-next'

const props = defineProps({
  orders: Array
})

/* FORMAT PRICE */
const formatPrice = (value) => {
  return new Intl.NumberFormat().format(value || 0)
}

/* FORMAT DATE */
const formatDate = (date) => {
  return new Date(date).toDateString()
}

/* IMAGE FIX */
const getImage = (image) => {
  if (!image) return '/placeholder.png'

  if (image.startsWith('http')) return image
  if (image.startsWith('storage')) return `/${image}`

  return `/storage/${image}`
}

/* STATUS COLORS */
const statusClasses = (status) => {
  switch (status) {

    case 'delivered':
      return 'bg-green-100 text-green-700'

    case 'shipping':
      return 'bg-purple-100 text-purple-700'

    case 'processing':
      return 'bg-blue-100 text-blue-700'

    case 'cancelled':
      return 'bg-red-100 text-red-700'

    default:
      return 'bg-gray-100 text-gray-700'
  }
}
</script>