<template>
  <AppLayout>

    <div class="max-w-4xl mx-auto px-6 py-12 space-y-8">

    <!-- ================= HEADER ================= -->
<div class="bg-white rounded-2xl p-6 border shadow-sm">

    <div class="flex items-start justify-between">

        <div>
            <h1 class="text-xl font-semibold text-gray-800">
                Order #{{ order.id }}
            </h1>

            <p class="text-sm text-gray-500 mt-1">
                {{ formatDate(order.created_at) }}
            </p>
        </div>

        <span
            class="px-3 py-1 rounded-full text-xs font-medium capitalize"
            :class="statusClasses(order.status)"
        >
            {{ order.status || 'processing' }}
        </span>

    </div>

    <!-- ACTIONS -->
    <div class="mt-6 flex flex-wrap gap-3">

        <!-- Download Invoice -->
        <a
            :href="`/orders/${order.id}/invoice`"
            target="_blank"
            class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl
                   bg-blue-600 text-white text-sm font-medium
                   hover:bg-blue-700 transition"
        >
            <Download class="w-4 h-4" />

            Download Invoice
        </a>

    </div>

</div>

     <!-- ================= DYNAMIC TRACKING ================= -->
<div class="bg-white rounded-2xl p-6 border shadow-sm">

  <h2 class="text-sm font-semibold text-gray-700 mb-6">
    Order Tracking
  </h2>

  <div class="relative space-y-8">

    <!-- vertical line -->
    <div class="absolute left-5 top-0 bottom-0 w-px bg-gray-200"></div>

    <div
      v-for="(step, index) in steps"
      :key="step.key"
      class="flex gap-4 relative"
    >

      <!-- ICON -->
      <div
        class="z-10 w-10 h-10 rounded-full flex items-center justify-center transition"
        :class="index <= currentIndex
          ? 'bg-green-500 text-white'
          : 'bg-gray-100 border text-gray-400'"
      >

        <!-- COMPLETED STEP -->
        <Check v-if="index < currentIndex" class="w-5 h-5" />

        <!-- CURRENT / FUTURE STEP ICON -->
        <component
          v-else
          :is="step.icon"
          class="w-5 h-5"
        />

      </div>

      <!-- TEXT -->
      <div>
        <p
          class="text-sm font-medium"
          :class="index <= currentIndex ? 'text-gray-800' : 'text-gray-400'"
        >
          {{ step.label }}
        </p>

        <p class="text-xs text-gray-500">
          {{ step.desc }}
        </p>
      </div>

    </div>

  </div>

</div>

      <!-- ================= DELIVERY ================= -->
      <div class="bg-white rounded-2xl p-6 border shadow-sm space-y-2">

        <h2 class="text-sm font-semibold text-gray-700 mb-3">
          Delivery Info
        </h2>

        <div class="text-sm text-gray-600 space-y-2">
          <p><span class="font-medium text-gray-800">Address:</span> {{ order.address }}</p>
          <p><span class="font-medium text-gray-800">Phone:</span> {{ order.phone }}</p>
          <p class="capitalize">
            <span class="font-medium text-gray-800">Method:</span>
            {{ order.delivery_method }}
          </p>

          <p class="text-blue-600 font-medium pt-2 border border-blue-600  bg-blue-600/10 p-2 rounded ">
            {{ estimatedDelivery }}
          </p>
        </div>

      </div>

    </div>

  </AppLayout>
</template>

<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'
import { computed } from 'vue'
import {
  Check,
  Package,
  Truck,
  Home,
  CheckCircle,
  Download
} from 'lucide-vue-next'

const props = defineProps({
  order: Object
})

const formatDate = (date) => {
  return new Date(date).toDateString()
}

const getImage = (image) => {
  if (!image) return '/placeholder.png'
  if (image.startsWith('http')) return image
  return `/storage/${image}`
}

const statusClasses = (status) => {
  switch (status) {
    case 'delivered':
      return 'bg-green-100 text-green-700'
    case 'shipping':
      return 'bg-purple-100 text-purple-700'
    case 'processing':
      return 'bg-blue-100 text-blue-700'
    default:
      return 'bg-gray-100 text-gray-700'
  }
}

const estimatedDelivery = computed(() => {
  const method = props.order?.delivery_method

  if (method === 'express') return 'Delivery: Today'
  if (method === 'standard') return 'Delivery: 2–3 days'
  if (method === 'pickup') return 'Ready for pickup today'

  return 'Processing...'
})




const steps = computed(() => [
  {
    key: 'pending',
    label: 'Order Placed',
    desc: 'We received your order',
    icon: CheckCircle
  },
  {
    key: 'processing',
    label: 'Processing',
    desc: 'Preparing your items',
    icon: Package
  },
  {
    key: 'shipping',
    label: 'Shipping',
    desc: 'On the way',
    icon: Truck
  },
  {
    key: 'delivered',
    label: 'Delivered',
    desc: 'Order completed',
    icon: Home
  }
])

const currentIndex = computed(() => {
  return steps.value.findIndex(s => s.key === props.order?.status)
})




</script>