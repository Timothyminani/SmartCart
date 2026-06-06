<template>
  <AppLayout>
 <section class="bg-gradient-to-br from-blue-50 via-white to-blue-100">

<section class=" bg-gradient-to-br from-blue-50 via-white to-blue-100">

  <div class="max-w-7xl mx-auto px-6 grid md:grid-cols-2 gap-10 items-center">

    <!-- LEFT -->
    <div class=" ">
      <h1 class="text-4xl md:text-5xl font-black tracking-tight text-gray-900 leading-tight">
        Shop smarter with
        <span class="text-blue-600">AI assistance</span>
      </h1>

      <p class="mt-4 text-gray-500">
        Describe what you want and we’ll find the best products for you instantly.
      </p>

      <div class="mt-10 flex justify-center">
  <div class="w-full max-w-2xl">

    <div class="relative bg-white border border-gray-200 rounded-2xl shadow-md focus-within:shadow-lg transition p-4">

      <!-- TEXTAREA (multi-line like AI prompt) -->
            <textarea
            v-model="prompt"
            rows="2"
            placeholder="Ask anything... e.g. 'affordable laptop for programming'"
            class="w-full resize-none bg-transparent text-lg placeholder-gray-400 pr-12 outline-none border-none focus:outline-none focus:ring-0 focus:border-none"
            ></textarea>

      <!-- LEFT BOTTOM (+ icon) -->
      <button class="absolute bottom-3 left-3 w-8 h-8 flex items-center justify-center rounded-full hover:bg-gray-100 transition">
        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
        </svg>
      </button>

      <!-- RIGHT BOTTOM (Arrow button) -->
      <button 
        class="absolute bottom-3 right-3 w-9 h-9 flex items-center justify-center rounded-full bg-gray-900 hover:bg-black transition"
        @click="submitPrompt"
      >
        <Loader2 v-if="loading" class="w-4 h-4 text-white animate-spin" />
        <ArrowRightIcon v-else class="w-4 h-4 text-white" />
      </button>

    </div>

  </div>
</div>

      <div class="mt-3 flex items-center gap-1 text-xs text-gray-500">
        <Sparkles class="w-3 h-3 text-blue-500" />
        <span>AI-powered smart search</span>
      </div>
    </div>



    <!-- RIGHT -->
   
 <div class="mt-6 rounded-xl overflow-hidden hover:shadow-xl transition-shadow duration-300">

  <Swiper
    :modules="[Autoplay, Pagination]"
    :slides-per-view="1"
    :loop="true"
    :autoplay="{ delay: 4000 }"
    :pagination="{ clickable: true }"
    class="overflow-hidden rounded-xl"
  >

    <SwiperSlide v-for="slide in slides" :key="slide.id">
      <div class="relative">

        <!-- Image with right-side fade -->
        <div class="w-full h-[400px] rounded-xl overflow-hidden relative">
          <img
            :src="slide.image"
            class="w-full h-full object-cover"
          />

          <!-- Right-side fade mask -->
  <div class="absolute inset-x-0 bottom-0 h-32 bg-gradient-to-t from-gray-900/70 to-transparent pointer-events-none"></div>
        </div>

        <!-- Floating Content -->
        <div class="absolute bottom-6 left-6 max-w-sm text-white">
          <h2 class="text-2xl font-bold mb-2">{{ slide.title }}</h2>
          <p class="text-sm mb-4">{{ slide.description }}</p>

          <button
            class="bg-white text-black px-5 py-2 rounded-full text-sm font-medium hover:bg-gray-200 transition"
            @click="handleClick(slide.category)"
          >
            Shop Now <span> <ShoppingCart class="w-5 h-5 inline mr-2 text-black" /> </span>
          </button>
        </div>

      </div>
    </SwiperSlide>

  </Swiper>

</div>
  
  </div>



</section>


 <!-- RECOMMENDED SECTION -->

<section class="py-10">

  <div class="max-w-7xl mx-auto px-6">

    <!-- WHITE CONTAINER -->
    <div class="bg-white rounded-2xl shadow-sm p-10">

      <!-- HEADER -->
      <div class="flex items-center justify-between mb-5">

        <h2 class="text-lg md:text-xl font-semibold text-gray-900 flex items-center gap-2">
          <Sparkles class="w-5 h-5 text-blue-500" />
          Recommended for you
        </h2>

        <button class="text-sm text-blue-600 hover:underline flex items-center gap-1">
          See more
          <ArrowRightIcon class="w-4 h-4" />
        </button>

      </div>

      <!-- SWIPER -->
      <Swiper
        :modules="[Navigation]"
        navigation
        grab-cursor
        :space-between="16"
        :slides-per-view="2"
        :breakpoints="{
          640: { slidesPerView: 2 },
          768: { slidesPerView: 3 },
          1024: { slidesPerView: 4 }
        }"
        class="!px-1"
      >

        <SwiperSlide
          v-for="product in recommendedProducts"
          :key="product.id"
        >
          <ProductCard
            :product="product"
            :show-ai-badge="true"
          />
        </SwiperSlide>

      </Swiper>

    </div>

  </div>

</section>




 <!-- CATEGORIES SECTION -->

<section class="py-10">

  <div class="max-w-7xl mx-auto px-6">

    <!-- WHITE CONTAINER -->
    <div class="bg-white rounded-2xl shadow-sm p-10">

      <!-- HEADER -->
      <div class="flex items-center justify-between mb-5">

        <h2 class="text-lg md:text-xl font-semibold text-gray-900">
          Shop by Category
        </h2>

          <button class="text-sm text-blue-600 hover:underline flex items-center gap-1">
          See more
          <ArrowRightIcon class="w-4 h-4" />
        </button>

      </div>

      <!-- GRID -->
      <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">

        <CategoryCard
          v-for="category in categories"
          :key="category.id"
          :category="category"
        />

      </div>

    </div>

  </div>

</section>







<!-- FEATURED PRODUCTS SECTION -->

<section class="py-10">

  <div class="max-w-7xl mx-auto px-6">

    <!-- WHITE CONTAINER -->
    <div class="bg-white rounded-2xl shadow-sm p-10">

      <!-- HEADER -->
      <div class="flex items-center justify-between mb-5">

        <h2 class="text-lg md:text-xl font-semibold text-gray-900 flex items-center gap-2">
          <Sparkles class="w-5 h-5 text-blue-500" />
          Featured Products
        </h2>

        <button class="text-sm text-blue-600 hover:underline flex items-center gap-1">
          See more
          <ArrowRightIcon class="w-4 h-4" />
        </button>

      </div>

      <!-- SWIPER -->
      <Swiper
        :modules="[Navigation]"
        navigation
        :slides-per-view="2"
        :space-between="16"
        :breakpoints="{
          640: { slidesPerView: 2 },
          768: { slidesPerView: 3 },
          1024: { slidesPerView: 4 }
        }"
      >
        <SwiperSlide
          v-for="product in featuredProducts"
          :key="product.id"
        >
          <ProductCard
            :product="product"
            :show-ai-badge="true"
          />
        </SwiperSlide>
      </Swiper>

    </div>

  </div>

</section>




<!-- AI FEATURED PROMO SECTION -->
<section class="py-12">
  <div class="max-w-7xl mx-auto px-6">

    <!-- Container with Rounded Box -->
    <div class="relative rounded-2xl overflow-hidden shadow-lg">

      <!-- Background Image -->
      <img
        :src="headphonesImg"
        alt="AI Featured"
        class="w-full h-80 md:h-96 object-cover"
      />

      <!-- Gradient Overlay (curved effect) -->
      <div class="absolute inset-0 bg-gradient-to-r from-black/40 via-black/20 to-black/40"></div>

      <!-- Floating Content -->
      <div class="absolute inset-0 flex flex-col justify-center md:justify-center md:items-start md:pl-12 gap-4 text-white">

        <!-- AI Badge -->
        <span class="bg-blue-600/80 text-white text-xs font-semibold px-3 py-1 rounded-full inline-flex items-center gap-1">
          <Sparkles class="w-4 h-4" />
          AI Highlight
        </span>

        <!-- Heading -->
        <h2 class="text-2xl md:text-4xl font-extrabold leading-tight text-right">
          Discover Smart Picks for You
        </h2>

        <!-- Description -->
        <p class="max-w-sm md:max-w-md text-sm md:text-base text-right">
          Our AI recommends products tailored to your preferences. Explore trending picks and personalized suggestions, all curated for you!
        </p>

        <!-- CTA Button -->
        <button class="bg-blue-600 hover:bg-blue-700 px-6 py-3 rounded-full text-sm md:text-base font-medium transition">
          Explore Now
          <ArrowRightIcon class="w-4 h-4 inline ml-2" />
        </button>

      </div>

    </div>

  </div>
</section>




<!-- WHY CHOOSE US SECTION -->


<section class="py-12 px-6">
  <div class="max-w-7xl mx-auto p-6  bg-white rounded-2xl shadow-lg">

    <!-- HEADER -->
    <div class="flex items-center justify-between mb-8">
      <h2 class="text-xl md:text-2xl font-bold text-gray-900">
        Why Choose SmartCart
      </h2>
      <button class="text-sm text-blue-600 hover:underline">
        Learn More
        <ArrowRightIcon class="w-4 h-4 inline ml-1" />
      </button>
    </div>

    <!-- FEATURE GRID -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">

      <!-- FEATURE CARD -->
      <div class="flex flex-col items-center text-center p-6 bg-gray-50 rounded-xl hover:shadow-md transition">
        <Heart class="w-8 h-8 text-blue-600 mb-3" />
        <h3 class="font-semibold text-gray-900 mb-2">AI Recommendations</h3>
        <p class="text-sm text-gray-600">Get products tailored to your taste with smart AI suggestions.</p>
      </div>

      <div class="flex flex-col items-center text-center p-6 bg-gray-50 rounded-xl hover:shadow-md transition">
        <ShoppingCart class="w-8 h-8 text-blue-600 mb-3" />
        <h3 class="font-semibold text-gray-900 mb-2">Easy Shopping</h3>
        <p class="text-sm text-gray-600">Add items to your cart effortlessly and checkout in seconds.</p>
      </div>

      <div class="flex flex-col items-center text-center p-6 bg-gray-50 rounded-xl hover:shadow-md transition">
        <Truck class="w-8 h-8 text-blue-600 mb-3" />
        <h3 class="font-semibold text-gray-900 mb-2">Fast Delivery</h3>
        <p class="text-sm text-gray-600">Receive your products quickly with our reliable delivery service.</p>
      </div>

      <div class="flex flex-col items-center text-center p-6 bg-gray-50 rounded-xl hover:shadow-md transition">
        <Shield class="w-8 h-8 text-blue-600 mb-3" />
        <h3 class="font-semibold text-gray-900 mb-2">Secure Payments</h3>
        <p class="text-sm text-gray-600">Shop with peace of mind with our secure and encrypted payment options.</p>
      </div>

    </div>

  </div>
</section>



<!-- BRANDS SECTION -->
<section class="py-12  px-6">
  <div class="max-w-7xl mx-auto p-6 bg-white rounded-2xl shadow-lg">

    <!-- HEADER -->
    <div class="flex items-center justify-between mb-8">
      <h2 class="text-xl md:text-2xl font-bold text-gray-900">
        Our Brands
      </h2>
      <button class="text-sm text-blue-600 hover:underline flex items-center gap-1">
        View All
        <ArrowRightIcon class="w-4 h-4" />
      </button>
    </div>

    <!-- BRANDS SWIPER -->
    <Swiper
      :modules="[Navigation]"
      navigation
      :slides-per-view="2"
      :space-between="16"
      :breakpoints="{
        640: { slidesPerView: 3 },
        768: { slidesPerView: 4 },
        1024: { slidesPerView: 6 }
      }"
    >
      <SwiperSlide
        v-for="brand in brands"
        :key="brand.id"
      >
        <div class="flex items-center justify-center p-4 bg-gray-50 rounded-xl hover:shadow-md transition cursor-pointer">
          <img
            :src="brand.logo"
            :alt="brand.name"
            class="h-12 object-contain"
          />
        </div>
      </SwiperSlide>
    </Swiper>

  </div>
</section>




<!-- FAQ SECTION -->

<section class="py-16 bg-gray-50">



<!-- BADGE -->
  <div class="flex justify-center mb-3">
    <span class="inline-flex items-center gap-1 border border-blue-600 text-blue-600  font-semibold p-2 rounded-full">
      <Sparkles class="w-5 h-5" />
      FAQ
    </span>
  </div>








  <div class="max-w-4xl mx-auto px-6">

    <!-- TITLE -->
    <div class="text-center mb-10">
      <h2 class="text-2xl md:text-3xl font-bold text-gray-900">
        Frequently Asked Questions
      </h2>
      <p class="text-gray-600 mt-2 text-sm">
        Everything you need to know about shopping on SmartCart
      </p>
    </div>

    <!-- FAQ LIST -->
    <div class="space-y-4">

      <!-- ITEM -->
      <div
        v-for="(faq, index) in faqs"
        :key="index"
        class="bg-white rounded-xl border border-blue-600 overflow-hidden"
      >
        <!-- QUESTION -->
        <button
          @click="toggle(index)"
          class="w-full flex items-center justify-between px-5 py-4 text-left"
        >
          <span class="font-medium text-gray-900">
            {{ faq.question }}
          </span>

          <ChevronDown
            :class="{ 'rotate-180': openIndex === index }"
            class="w-5 h-5 text-gray-500 transition"
          />
        </button>

        <!-- ANSWER -->
        <div
          v-if="openIndex === index"
          class="px-5 pb-4 text-sm text-gray-600"
        >
          {{ faq.answer }}
        </div>
      </div>

    </div>

  </div>

</section>




</section>
</AppLayout>
</template>

<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'
import { Swiper, SwiperSlide} from 'swiper/vue'
import { Autoplay, Pagination, Navigation } from 'swiper/modules'

// Import Swiper styles
import 'swiper/css'
import 'swiper/css/pagination'
import 'swiper/css/navigation'

// Import your local images
import phoneImg from '@/assets/images/phones.jpg'
import computersImg from '@/assets/images/computers.jpg'
import headphonesImg from '@/assets/images/headphones.jpg'
import accessoriesImg from '@/assets/images/accessories.jpg'
import { Sparkles, ShoppingCart, ArrowRightIcon, Heart, Truck, Shield } from 'lucide-vue-next'
import ProductCard from '@/Components/ProductCard.vue'
import CategoryCard from '@/Components/CategoryCard.vue'
import HPLogo from '@/assets/images/HP_logo.png'
import LenovoLogo from '@/assets/images/lenovo.png'
import InfinixLogo from '@/assets/images/infinix.png'
import AppleLogo from '@/assets/images/Apple-Logo-3.png'
import { ref } from 'vue'
import { ChevronDown } from 'lucide-vue-next'
import { router } from '@inertiajs/vue3'

const prompt = ref('')
const loading = ref(false)
const openIndex = ref(null)

const toggle = (index) => {
  openIndex.value = openIndex.value === index ? null : index
}




const submitPrompt = () => {
if (!prompt.value.trim()) return

router.visit(
`/ai-search?q=${encodeURIComponent(prompt.value)}`
)
}









const faqs = [
  {
    question: "How does AI recommendation work?",
    answer: "Our system analyzes your browsing and purchase behavior to suggest products tailored to your preferences."
  },
  {
    question: "Can I compare products?",
    answer: "Yes! You can select products and use our AI-powered comparison feature to find the best option."
  },
  {
    question: "What payment methods are available?",
    answer: "We support mobile money, cards, and other secure payment options."
  },
  {
    question: "How long does delivery take?",
    answer: "Delivery usually takes 1–3 business days depending on your location."
  }
]








const categories = [
  { id: 1, name: 'Phones', image: phoneImg },
  { id: 2, name: 'Laptops', image: computersImg },
  { id: 3, name: 'Fashion', image: headphonesImg },
  { id: 4, name: 'Home', image: accessoriesImg },
  { id: 5, name: 'Gaming', image: headphonesImg },
  { id: 6, name: 'Accessories', image: accessoriesImg },
  { id: 1, name: 'Phones', image: phoneImg },
  { id: 2, name: 'Laptops', image: computersImg },
  { id: 3, name: 'Fashion', image: headphonesImg },
  { id: 4, name: 'Home', image: accessoriesImg },
  { id: 5, name: 'Gaming', image: headphonesImg },
  { id: 6, name: 'Accessories', image: accessoriesImg }
]



const slides = [
  {
    id: 1,
    title: "Smartphones & Gadgets",
    description: "Latest phones and smart gadgets at amazing prices",
    image: phoneImg,
    category: "tech"
  },
  {
    id: 2,
    title: "Computers & Laptops",
    description: "High-performance computers for work and gaming",
    image: computersImg,
    category: "home"
  },
  {
    id: 3,
    title: "Wearable Tech",
    description: "Smart watches and fitness trackers for your lifestyle",
    image: headphonesImg,
    category: "wearables"
  },
  {
    id: 4,
    title: "Audio Deals",
    description: "Headphones and speakers to elevate your sound experience",
    image: accessoriesImg,
    category: "audio"
  }
]



const recommendedProducts = [
  {
    id: 1,
    name: 'Laptop Pro 2024 Edition with Extended Name to Test Line Clamp',
    price: 90000,
    image: computersImg
  },
  {
    id: 2,
    name: 'Smartphone X with Extra Long Name to Test Line Clamp',
    price: 45000,
    image: phoneImg
  },

   {
    id: 1,
    name: 'Laptop Pro 2024 Edition with Extended Name to Test Line Clamp',
    price: 90000,
    image: computersImg
  },
  {
    id: 2,
    name: 'Smartphone X with Extra Long Name to Test Line Clamp',
    price: 45000,
    image: phoneImg
  },

   {
    id: 1,
    name: 'Laptop Pro 2024 Edition with Extended Name to Test Line Clamp ',
    price: 90000,
    image: computersImg
  },
  {
    id: 2,
    name: 'Smartphone X with Extra Long Name to Test Line Clamp',
    price: 45000,
    image: phoneImg
  }
]



const featuredProducts = [
   {
    id: 1,
    name: 'Laptop Pro 2024 Edition with Extended Name to Test Line Clamp',
    price: 90000,
    image: computersImg
  },
  {
    id: 2,
    name: 'Smartphone X with Extra Long Name to Test Line Clamp',
    price: 45000,
    image: phoneImg
  },

   {
    id: 1,
    name: 'Laptop Pro 2024 Edition with Extended Name to Test Line Clamp',
    price: 90000,
    image: computersImg
  },
  {
    id: 2,
    name: 'Smartphone X with Extra Long Name to Test Line Clamp',
    price: 45000,
    image: phoneImg
  },

   {
    id: 1,
    name: 'Laptop Pro 2024 Edition with Extended Name to Test Line Clamp ',
    price: 90000,
    image: computersImg
  },
  {
    id: 2,
    name: 'Smartphone X with Extra Long Name to Test Line Clamp',
    price: 45000,
    image: phoneImg
  }
]

const brands = [
  { id: 1, name: 'Apple', logo: AppleLogo },
  { id: 2, name: 'HP', logo: HPLogo },
  { id: 3, name: 'Lenovo', logo: LenovoLogo },
  { id: 4, name: 'Infinix', logo: InfinixLogo },
  { id: 5, name: 'Apple', logo: AppleLogo },
  { id: 6, name: 'HP', logo: HPLogo },
  { id: 7, name: 'Lenovo', logo: LenovoLogo },
  { id: 8, name: 'Infinix', logo: InfinixLogo }
]



</script>