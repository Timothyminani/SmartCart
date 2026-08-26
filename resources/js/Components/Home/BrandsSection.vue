<script setup>
import { computed } from 'vue'
import { usePage, router } from '@inertiajs/vue3'

import { Swiper, SwiperSlide } from 'swiper/vue'
import { Navigation } from 'swiper/modules'

import { ArrowRight } from 'lucide-vue-next'

import 'swiper/css'
import 'swiper/css/navigation'


const page = usePage()

const brands = computed(() => {
  return page.props.navigation?.brands ?? []
})


const viewBrand = (brand) => {
  router.visit(
    `/productListing?brand=${encodeURIComponent(brand.slug)}`
  )
}
</script>

<template>
  <section class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-14">

    <!-- HEADER -->
    <div class="mb-7 flex items-end justify-between gap-4">
      <div>
        <h2 class="text-2xl font-bold text-gray-900 sm:text-3xl">
          Our Brands
        </h2>

        <p class="mt-1 text-sm text-gray-500">
          Shop products from trusted brands
        </p>
      </div>

      <button
        type="button"
        class="
          flex
          shrink-0
          items-center
          gap-1
          text-sm
          font-semibold
          text-blue-600
          transition
          hover:text-blue-700
          hover:underline
        "
        @click="router.visit('/productListing')"
      >
        View All

        <ArrowRight class="h-4 w-4" />
      </button>
    </div>


    <!-- BRANDS -->
    <Swiper
      v-if="brands.length"
      :modules="[Navigation]"
      navigation
      :slides-per-view="2"
      :space-between="12"
      :breakpoints="{
        640: {
          slidesPerView: 3,
          spaceBetween: 14,
        },

        768: {
          slidesPerView: 4,
          spaceBetween: 16,
        },

        1024: {
          slidesPerView: 5,
          spaceBetween: 16,
        },

        1280: {
          slidesPerView: 6,
          spaceBetween: 18,
        },
      }"
      class="brands-swiper"
    >
      <SwiperSlide
        v-for="brand in brands"
        :key="brand.id"
      >
        <button
          type="button"
          class="
            group
            flex
            h-24
            w-full
            items-center
            justify-center
            rounded-2xl
            border
            border-gray-100
            bg-gray-50
            p-4
            transition
            duration-300
            hover:-translate-y-1
            hover:border-blue-100
            hover:bg-white
            hover:shadow-md
          "
          @click="viewBrand(brand)"
        >
            <img
            v-if="brand.logo"
            :src="`/storage/${brand.logo}`"
            :alt="brand.name"
            class="
                max-h-12
                max-w-[110px]
                object-contain
                transition
                duration-300
                group-hover:scale-105
            "
            />

          <span
            v-else
            class="text-sm font-semibold text-gray-700"
          >
            {{ brand.name }}
          </span>
        </button>
      </SwiperSlide>
    </Swiper>


    <!-- EMPTY STATE -->
    <div
      v-else
      class="
        rounded-2xl
        border
        border-dashed
        border-gray-200
        py-10
        text-center
        text-sm
        text-gray-500
      "
    >
      No brands available.
    </div>

  </section>
</template>

<style scoped>
:deep(.swiper-button-next),
:deep(.swiper-button-prev) {
  width: 34px;
  height: 34px;
  border-radius: 9999px;
  background: white;
  box-shadow: 0 2px 8px rgb(0 0 0 / 0.08);
}

:deep(.swiper-button-next::after),
:deep(.swiper-button-prev::after) {
  font-size: 13px;
  font-weight: 700;
}
</style>