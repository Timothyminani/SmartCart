<script setup>
import { ref, computed } from 'vue'
import { Link } from '@inertiajs/vue3'
import { ArrowRight } from 'lucide-vue-next'

import ProductCard from '@/Components/ProductCard.vue'

const props = defineProps({
  products: {
    type: Array,
    default: () => [],
  },

  categories: {
    type: Array,
    default: () => [],
  },
})

const selectedCategory = ref(null)

const filteredProducts = computed(() => {
  if (!selectedCategory.value) {
    return props.products
  }

  return props.products.filter(
    product => product.category?.slug === selectedCategory.value
  )
})
</script>

<template>
  <section class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-14">

    <!-- HEADER -->
    <div class="mb-7 flex items-end justify-between gap-4">
      <div>
        <h2 class="text-2xl font-bold text-gray-900 sm:text-3xl">
          Featured Products
        </h2>

        <p class="mt-1 text-sm text-gray-500">
          Explore some of our recommended products
        </p>
      </div>

      <Link
        href="/productListing"
        class="
          shrink-0
          text-sm
          font-semibold
          text-blue-600
          transition
          hover:text-blue-700
          hover:underline
        "
      >
        View All →
      </Link>
    </div>


    <!-- CATEGORY FILTERS -->
    <div
      class="
        mb-8
        flex
        gap-2
        overflow-x-auto
        pb-2
      "
    >
      <button
        type="button"
        @click="selectedCategory = null"
        :class="[
          'shrink-0 rounded-full px-4 py-2 text-sm font-medium transition',
          selectedCategory === null
            ? 'bg-blue-600 text-white'
            : 'border border-gray-200 bg-white text-gray-700 hover:border-blue-300 hover:bg-blue-50 hover:text-blue-600'
        ]"
      >
        All
      </button>

      <button
        v-for="category in categories"
        :key="category.id"
        type="button"
        @click="selectedCategory = category.slug"
        :class="[
          'shrink-0 rounded-full px-4 py-2 text-sm font-medium transition',
          selectedCategory === category.slug
            ? 'bg-blue-600 text-white'
            : 'border border-gray-200 bg-white text-gray-700 hover:border-blue-300 hover:bg-blue-50 hover:text-blue-600'
        ]"
      >
        {{ category.name }}
      </button>
    </div>


    <!-- PRODUCTS -->
    <div
      v-if="filteredProducts.length"
      class="
        grid
        grid-cols-2
        gap-4
        md:grid-cols-3
        lg:grid-cols-4
        lg:gap-6
      "
    >
      <ProductCard
        v-for="product in filteredProducts"
        :key="product.id"
        :product="product"
      />
    </div>


    <!-- EMPTY STATE -->
    <div
      v-else
      class="
        rounded-2xl
        border
        border-dashed
        border-gray-200
        py-12
        text-center
        text-sm
        text-gray-500
      "
    >
      No featured products in this category.
    </div>


    <!-- BROWSE ALL -->
    <div class="mt-10 flex justify-center">
      <Link
        href="/productListing"
        class="
          inline-flex
          items-center
          gap-2
          rounded-xl
          bg-blue-600
          px-5
          py-2.5
          text-sm
          font-semibold
          text-white
          shadow-sm
          transition
          hover:bg-blue-700
          hover:shadow-md
        "
      >
        Browse All Products

        <ArrowRight class="h-4 w-4" />
      </Link>
    </div>

  </section>
</template>