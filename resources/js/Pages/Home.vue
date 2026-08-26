<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'

import HeroSection from '@/Components/Home/HeroSection.vue'
import PopularCategories from '@/Components/Home/PopularCategories.vue'
import FeaturedProducts from '@/Components/Home/FeaturedProducts.vue'
import WhyChooseUs from '@/Components/Home/WhyChooseUs.vue'
import BrandsSection from '@/Components/Home/BrandsSection.vue'
import FaqSection from '@/Components/Home/FaqSection.vue'

defineProps({
  popularCategories: {
    type: Array,
    default: () => [],
  },

  featuredProducts: {
    type: Array,
    default: () => [],
  },
})
</script>

<template>
  <AppLayout>
    <section class="mt-4 bg-white px-2 lg:px-10">

      <HeroSection />

      <PopularCategories
        :categories="popularCategories"
      />

      <FeaturedProducts
        :products="featuredProducts"
        :categories="popularCategories"
      />

      <WhyChooseUs />

      <BrandsSection />

      <FaqSection />

    </section>
  </AppLayout>
</template>