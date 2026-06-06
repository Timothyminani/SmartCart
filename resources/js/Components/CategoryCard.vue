<script setup>
import { Sparkles, ShoppingCart, ArrowRightIcon } from 'lucide-vue-next'



defineProps({
  category: Object
})

</script>

<template>
  <div class="bg-white rounded-xl overflow-hidden hover:shadow-md hover:-translate-y-1 transition cursor-pointer group">

    <!-- IMAGE CONTAINER -->
    <div class="w-full h-28 bg-gray-100 flex items-center justify-center overflow-hidden">

      <img
        :src="category.image"
        alt=""
        class="h-full w-full object-cover group-hover:scale-105 transition duration-300"
      />

    </div>

    <!-- NAME -->
    <div class="p-3 text-center">
      <p class="text-sm font-medium text-gray-700 group-hover:text-blue-600 transition">
        {{ category.name }} <ArrowRightIcon class="w-4 h-4 inline ml-1" />
      </p>
    </div>

  </div>
</template>