<script setup>
import {
  Sparkles,
  ShoppingCart,
  Truck,
  ShieldCheck,
} from 'lucide-vue-next'

const features = [
  {
    title: 'AI Recommendations',
    description:
      'Get smarter product suggestions based on what you are looking for.',
    icon: Sparkles,
  },
  {
    title: 'Easy Shopping',
    description:
      'Browse, compare and add products to your cart with a simple shopping flow.',
    icon: ShoppingCart,
  },
  {
    title: 'Fast Delivery',
    description:
      'Get your products delivered quickly with reliable delivery options.',
    icon: Truck,
  },
  {
    title: 'Secure Payments',
    description:
      'Shop confidently with secure and trusted payment methods.',
    icon: ShieldCheck,
  },
]
</script>

<template>
  <section class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-14">

    <!-- HEADER -->
    <div class="mb-7">
      <h2 class="text-2xl font-bold text-gray-900 sm:text-3xl">
        Why Choose SmartCart
      </h2>

      <p class="mt-1 text-sm text-gray-500">
        A smarter and simpler way to shop
      </p>
    </div>

    <!-- FEATURES -->
    <div
      class="
        grid
        grid-cols-1
        gap-3
        md:grid-cols-4
        md:gap-4
      "
    >
      <div
        v-for="feature in features"
        :key="feature.title"
        class="
          rounded-2xl
          border
          border-gray-100
          bg-gray-50
          p-5
          transition
          duration-300
          hover:-translate-y-1
          hover:border-blue-100
          hover:bg-white
          hover:shadow-md
        "
      >
        <!-- ICON -->
        <div
          class="
            mb-4
            flex
            h-10
            w-10
            items-center
            justify-center
            rounded-xl
            bg-blue-100
            text-blue-600
          "
        >
          <component
            :is="feature.icon"
            class="h-5 w-5"
          />
        </div>

        <!-- CONTENT -->
        <h3 class="text-sm font-semibold text-gray-900 sm:text-base">
          {{ feature.title }}
        </h3>

        <p
          class="
            mt-2
            text-xs
            leading-5
            text-gray-500
            sm:text-sm
          "
        >
          {{ feature.description }}
        </p>
      </div>
    </div>

  </section>
</template>