<script setup>
import { ref } from 'vue'
import { router } from '@inertiajs/vue3'

import { Swiper, SwiperSlide } from 'swiper/vue'
import { Autoplay, Pagination } from 'swiper/modules'
import { ShoppingCart } from 'lucide-vue-next'

import AiPromptBox from '@/Components/Home/AiPromptBox.vue'

import phoneImg from '@/assets/images/b-3.jpg'
import computersImg from '@/assets/images/b-2.jpg'
import headphonesImg from '@/assets/images/headphones.jpg'
import accessoriesImg from '@/assets/images/accessories.jpg'

import 'swiper/css'
import 'swiper/css/pagination'


const prompt = ref('')
const loading = ref(false)

const submitPrompt = (value) => {
  const query = value.trim()

  if (!query || loading.value) return

  loading.value = true

  router.visit(
    `/ai-search?q=${encodeURIComponent(query)}`,
    {
      onFinish: () => {
        loading.value = false
      },
    }
  )
}


const slides = [
  {
    id: 1,
    title: 'Smartphones & Gadgets',
    description: 'Latest phones and smart gadgets at amazing prices.',
    image: phoneImg,
    category: 'phones',
  },
  {
    id: 2,
    title: 'Computers & Laptops',
    description: 'Powerful computers for work, study and gaming.',
    image: computersImg,
    category: 'computers',
  },
  {
    id: 3,
    title: 'Wearable Tech',
    description: 'Smart watches and wearable technology.',
    image: headphonesImg,
    category: 'watches',
  },
  {
    id: 4,
    title: 'Audio Deals',
    description: 'Great audio products for better listening.',
    image: accessoriesImg,
    category: 'speakers',
  },
]

const browseCategory = (category) => {
  router.visit(
    `/productListing?category=${encodeURIComponent(category)}`
  )
}
</script>

<template>
  <section class="overflow-hidden rounded-2xl bg-blue-50">
    <div
      class="
        mx-auto
        grid
        max-w-7xl
        grid-cols-1
        items-center
        gap-6
        px-6
        py-8
        md:grid-cols-2
        md:gap-8
        lg:px-10
      "
    >

      <!-- LEFT -->
      <div>
        <h1
          class="
            max-w-xl
            text-3xl
            font-black
            leading-tight
            tracking-tight
            text-gray-900
            sm:text-4xl
            lg:text-5xl
          "
        >
          Tell us what you need.

          <span class="text-blue-600">
            We’ll find the right product.
          </span>
        </h1>

        <p
          class="
            mt-3
            max-w-lg
            text-sm
            leading-6
            text-gray-600
            sm:text-base
          "
        >
          Describe what you're looking for and our AI will find
          the best products for your needs and budget.
        </p>

        <div class="mt-5 max-w-xl">
          <AiPromptBox
            v-model="prompt"
            :loading="loading"
            placeholder="Try: laptop for programming under KSh 80,000"
            @submit="submitPrompt"
          />
        </div>
      </div>


      <!-- RIGHT -->
      <div
        class="
          min-w-0
          overflow-hidden
          rounded-xl
          bg-white
          p-1.5
          shadow-sm
        "
      >
        <Swiper
          :modules="[Autoplay, Pagination]"
          :slides-per-view="1"
          :loop="true"
          :autoplay="{
            delay: 4500,
            disableOnInteraction: false,
          }"
          :pagination="{ clickable: true }"
          class="hero-swiper overflow-hidden rounded-lg"
        >
          <SwiperSlide
            v-for="slide in slides"
            :key="slide.id"
          >
            <div class="relative">
              <div
                class="
                  relative
                  h-[240px]
                  overflow-hidden
                  rounded-lg
                  sm:h-[270px]
                  lg:h-[300px]
                "
              >
                <img
                  :src="slide.image"
                  :alt="slide.title"
                  class="h-full w-full object-cover"
                />

                <div
                  class="
                    pointer-events-none
                    absolute
                    inset-0
                    bg-gradient-to-t
                    from-black/75
                    via-black/20
                    to-transparent
                  "
                />
              </div>

              <div
                class="
                  absolute
                  bottom-5
                  left-5
                  right-5
                  text-white
                "
              >
                <h2 class="text-xl font-bold">
                  {{ slide.title }}
                </h2>

                <p
                  class="
                    mt-1
                    max-w-sm
                    text-sm
                    text-white/80
                  "
                >
                  {{ slide.description }}
                </p>

                <button
                  type="button"
                  class="
                    mt-3
                    inline-flex
                    items-center
                    gap-2
                    rounded-full
                    bg-white
                    px-4
                    py-2
                    text-xs
                    font-semibold
                    text-gray-900
                    transition
                    hover:bg-gray-100
                  "
                  @click="browseCategory(slide.category)"
                >
                  Shop now

                  <ShoppingCart class="h-4 w-4" />
                </button>
              </div>
            </div>
          </SwiperSlide>
        </Swiper>
      </div>

    </div>
  </section>
</template>

<style scoped>
:deep(.swiper-pagination-bullet) {
  background: white;
  opacity: 0.55;
}

:deep(.swiper-pagination-bullet-active) {
  opacity: 1;
}
</style>