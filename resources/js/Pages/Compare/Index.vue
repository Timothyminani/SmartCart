<template>
  <AppLayout>
    <div class="min-h-screen bg-gray-50 ">

      <div class="max-w-7xl mx-auto px-10 py-10 ">



        <!-- HEADER -->
 <div
  class="relative overflow-hidden
         bg-gradient-to-br from-white via-white to-blue-50/40
         shadow-xl rounded-[2rem]
         p-10 lg:p-9 mb-10
         ring-1 ring-blue-100"
>

  <!-- Decorative Background -->
  <div class="absolute top-0 right-0 w-64 h-64 bg-blue-50 rounded-full blur-3xl"></div>

  <div class="relative flex flex-col lg:flex-row lg:items-center lg:justify-between gap-8">

    <!-- Left -->
    <div>

      <div
        class="inline-flex items-center gap-2 px-4 py-2 bg-blue-50 text-blue-600 rounded-full text-sm font-medium"
      >
        <Sparkles class="w-4 h-4" />
        AI Product Comparison
      </div>

      <h1 class="text-4xl font-bold text-gray-900 mt-5">
        Compare Products
      </h1>

      <p class="text-gray-500 mt-3 max-w-2xl">
        Compare specifications, pricing, performance and AI-powered insights
        to confidently choose the best product.
      </p>

    </div>

    <!-- Right Stats -->
    <div class="flex gap-4">

      <div
        class="bg-blue-50 border border-blue-100 px-6 py-5 rounded-2xl min-w-[140px]"
      >
        <p class="text-sm text-gray-500">
          Comparing
        </p>

        <p class="text-3xl font-bold text-blue-600">
          {{ products.length }}
        </p>

        <p class="text-xs text-gray-500 mt-1">
          Products
        </p>
      </div>

    </div>

  </div>

</div>
<!-- END OF HEADER -->




        <!-- EMPTY STATE -->
        <div v-if="products.length < 2" class="bg-white p-16 text-center rounded-3xl border">

          <GitCompare class="w-12 h-12 mx-auto text-blue-600" />

          <h2 class="text-2xl font-bold mt-4">
            Not enough products to compare
          </h2>

          <p class="text-gray-500 mt-2">
            Select at least 2 products from the store.
          </p>

        </div>
<!-- END OF EMPTY STATE -->






        <!-- COMPARE CONTENT COMPARE CONTENT COMPARE CONTENT COMPARE CONTENT -->

<div v-else class="space-y-8 ">



          <!-- PRODUCTS COMPARE HIGHLIGHT-->

<div class="relative">

  <!-- VS BADGE -->
  <div
    class="hidden lg:flex absolute left-1/2 top-1/2 -translate-x-1/2 -translate-y-1/2 z-10"
  >
    <div
      class="w-16 h-16 rounded-full bg-white text-blue-600 flex items-center justify-center text-lg font-bold shadow-xl border-4 border-white"
    >
      <span class="text-2xl">V</span>S
    </div>
  </div>

  <!-- PRODUCTS -->
  <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

    <div
      v-for="product in products"
      :key="product.id"
      class="bg-white shadow-lg rounded-3xl p-6"
    >

      <div class="flex gap-4">

        <!-- Image -->
        <div class="flex-shrink-0">
          <img
            :src="product.images?.[0]
              ? `/storage/${product.images[0].image_path}`
              : '/placeholder.png'"
            class="w-28 h-28 object-cover rounded-2xl border"
          />
        </div>

        <!-- Content -->
        <div class="flex-1 flex flex-col">

          <!-- Brand Badge -->
          <div>
            <span
              class="inline-flex px-3 py-1 rounded-full bg-blue-50 text-blue-600 text-xs font-medium"
            >
              {{ product.brand?.name }}
            </span>
          </div>

          <!-- Product Name -->
          <h2 class="text-lg font-semibold mt-3 text-gray-900 line-clamp-2">
            {{ product.name }}
          </h2>

          <!-- Price -->
          <div class="mt-4">
            <p class="text-xs uppercase tracking-wide text-gray-500">
              Price
            </p>

            <p class="text-2xl font-bold text-blue-600">
              KES {{ format(product.sale_price) }}
            </p>
          </div>

        </div>

      </div>

    </div>

  </div>

</div>
<!-- END OF PRODUCTS COMPARE HIGHLIGHT-->





          <!-- ================= SPEC TABLE ================= -->
 <div class="overflow-x-auto rounded-3xl shadow-lg border border-gray-200 bg-white">

  <table class="w-full  bg-white">

    <thead>

      <tr class="bg-blue-50">

        <th
          class="border border-gray-200 p-4 text-left w-56 font-semibold "
        >
          Specification
        </th>

        <th
          v-for="p in products"
          :key="p.id"
          class="border border-gray-200 p-4 text-left min-w-[100px]"
        >

          <div class="flex items-center gap-3">

            <img
              :src="p.images?.[0]
                ? `/storage/${p.images[0].image_path}`
                : '/placeholder.png'"
              class="w-10 h-10 rounded-lg object-cover border"
            />

            <div>
              <p class="font-semibold text-sm text-gray-900">
                {{ p.name }}
              </p>

              <p class="text-xs text-gray-500">
                {{ p.brand?.name }}
              </p>
            </div>

          </div>

        </th>

      </tr>

    </thead>

    <tbody>

      <tr
        v-for="attr in mergedAttributes"
        :key="attr"
      >

        <td
          class="border border-gray-200 p-4 font-medium text-gray-800 bg-white capitalize"
        >
          {{ attr }}
        </td>

        <td
          v-for="p in products"
          :key="p.id"
          class="border border-gray-200 p-4 text-gray-600"
        >
          {{ getValue(p, attr) }}
        </td>

      </tr>

    </tbody>

  </table>

</div>
<!-- ================= END OF SPEC TABLE ================= -->






    <!-- ================= AI WORKSPACE ================= -->

<div
  class="relative overflow-hidden
         bg-gradient-to-br from-white via-white to-blue-50/30
         rounded-[2rem]
         border border-blue-100
         shadow-xl
         mt-8"
>

  <!-- Decorative Background -->
  <div class="absolute top-0 right-0 w-72 h-72 bg-blue-100/30 blur-3xl rounded-full"></div>

  <div class="relative p-8 lg:p-10">

    <!-- HEADER -->
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">

      <!-- LEFT -->
      <div>

        <div
          class="inline-flex items-center gap-2 px-4 py-2 rounded-full
                 bg-gradient-to-r from-blue-600 to-indigo-600
                 text-white text-sm font-medium shadow-md"
        >
          <Sparkles class="w-4 h-4" />
          AI Product Intelligence
        </div>

        <h2 class="text-3xl font-bold text-gray-900 mt-5">
          Smart Product Analysis
        </h2>

        <p class="text-gray-500 mt-3 max-w-xl">
          Generate AI-powered comparison insights, strengths,
          weaknesses, buying advice and real-world recommendations.
        </p>

      </div>

      <!-- RIGHT -->
      <div>

        <button
          @click="generateAI"
          :disabled="aiLoading"
          class="bg-gradient-to-r from-blue-600 to-indigo-600
                 hover:from-blue-700 hover:to-indigo-700
                 text-white px-6 py-4 rounded-2xl
                 font-medium shadow-lg shadow-blue-200
                 transition-all duration-300
                 disabled:opacity-60 disabled:cursor-not-allowed"
        >

          <span
            v-if="!aiLoading"
            class="flex items-center gap-2"
          >
            <Sparkles class="w-5 h-5" />
            Generate AI Insight
          </span>

          <span
            v-else
            class="flex items-center gap-2"
          >

            <svg
              class="animate-spin h-5 w-5 text-white"
              xmlns="http://www.w3.org/2000/svg"
              fill="none"
              viewBox="0 0 24 24"
            >
              <circle
                class="opacity-25"
                cx="12"
                cy="12"
                r="10"
                stroke="currentColor"
                stroke-width="4"
              />

              <path
                class="opacity-75"
                fill="currentColor"
                d="M4 12a8 8 0 018-8v4l3-3-3-3v4A10 10 0 002 12h2z"
              />
            </svg>

            Analyzing...
          </span>

        </button>

      </div>

    </div>

    <!-- LOADING STATE -->
    <div
  v-if="aiLoading"
  class="mt-10 border-t border-gray-100 pt-8"
>

  <!-- TOP LOADING -->
  <div class="flex items-center gap-3 mb-8">

    <div class="flex gap-1">

      <div class="w-2 h-2 bg-blue-500 rounded-full animate-bounce"></div>
      <div class="w-2 h-2 bg-blue-500 rounded-full animate-bounce delay-100"></div>
      <div class="w-2 h-2 bg-blue-500 rounded-full animate-bounce delay-200"></div>

    </div>

    <p class="text-sm text-gray-500">
      AI is generating comparison insights...
    </p>

  </div>

  <!-- SKELETON CONTENT -->
  <div class="space-y-5 animate-pulse">

    <!-- Skeleton Block -->
    <div class="bg-white/70 rounded-3xl border border-gray-100 p-6">

      <div class="h-5 w-48 bg-gray-200 rounded mb-5"></div>

      <div class="space-y-3">

        <div class="h-3 w-full bg-gray-100 rounded"></div>
        <div class="h-3 w-11/12 bg-gray-100 rounded"></div>
        <div class="h-3 w-9/12 bg-gray-100 rounded"></div>

      </div>

    </div>

    <!-- Skeleton Block -->
    <div class="bg-white/70 rounded-3xl border border-gray-100 p-6">

      <div class="h-5 w-36 bg-gray-200 rounded mb-5"></div>

      <div class="space-y-3">

        <div class="h-3 w-full bg-gray-100 rounded"></div>
        <div class="h-3 w-10/12 bg-gray-100 rounded"></div>
        <div class="h-3 w-7/12 bg-gray-100 rounded"></div>

      </div>

    </div>

  </div>

</div>



    <!-- AI RESULT -->
    <div
      v-if="aiResult"
      class="mt-10 border-t border-gray-100 pt-10"
    >

      <div
        class="max-w-none text-gray-800
               prose prose-lg
               prose-h1:text-3xl
               prose-h2:text-2xl
               prose-h3:text-xl
               prose-headings:font-semibold
               prose-headings:text-gray-900
               prose-headings:mb-4
               prose-headings:mt-8
               prose-p:leading-8
               prose-p:my-3
               prose-ul:my-3
               prose-li:my-2
               prose-li:marker:text-black
               prose-table:my-6
               prose-th:bg-blue-50
               prose-th:p-3
               prose-td:p-3
               prose-hr:my-8"
        v-html="renderMarkdown(displayedAiResult)"
      ></div>

<div
  v-if="displayedAiResult.length < aiResult.length"
  class="mt-2"
>
  <span class="inline-block w-2 h-5 bg-blue-600 animate-pulse rounded-sm"></span>
</div>



    </div>

  </div>

</div>

<!-- ================= END AI WORKSPACE ================= -->



<!-- FINAL DECISION SECTION -->
<div
 
  class="mt-10"
 >

  <!-- HEADER -->
  <div class="flex items-center justify-between mb-6">

    <div>

      <h2 class="text-2xl font-bold text-gray-900">
        Ready to Choose?
      </h2>

      <p class="text-gray-500 mt-2">
        Add your preferred product to cart or continue exploring.
      </p>

    </div>

  </div>

  <!-- PRODUCTS -->
  <div class="flex flex-col md:flex-row justify-center gap-10">

    <ProductCard
      v-for="product in products"
      :key="product.id"
      :product="product"
       compact
    />

  </div>

</div>





        </div>

      </div>

    </div>
  </AppLayout>
</template>

<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'
import { Sparkles, GitCompare } from 'lucide-vue-next'
import { computed, ref, onUnmounted } from 'vue'
import { router } from '@inertiajs/vue3'
import axios from 'axios'
import MarkdownIt from 'markdown-it'
import ProductCard from '@/Components/ProductCard.vue'


const props = defineProps({
  products: Array
})

const products = computed(() => props.products || [])
const displayedAiResult = ref('')

/*
|--------------------------------------------------------------------------
| AI STATE
|--------------------------------------------------------------------------
*/

const aiLoading = ref(false)
const aiResult = ref(null)
const comparisonId = ref(null)
const pollInterval = ref(null)

/*
|--------------------------------------------------------------------------
| MARKDOWN
|--------------------------------------------------------------------------
*/

const md = new MarkdownIt({
  html: true,
  linkify: true,
  breaks: true
})

const renderMarkdown = (text) => {
  if (!text) return ''
  return md.render(text)
}

/*
|--------------------------------------------------------------------------
| FORMATTERS
|--------------------------------------------------------------------------
*/

const format = (v) => new Intl.NumberFormat().format(v)

/*
|--------------------------------------------------------------------------
| REMOVE PRODUCT
|--------------------------------------------------------------------------
*/

const remove = (id) => {

  const updated = products.value.filter(p => p.id !== id)

  router.get(route('compare.index'), {
    products: updated.map(p => p.id)
  })

}

/*
|--------------------------------------------------------------------------
| GENERATE AI
|--------------------------------------------------------------------------
*/

const generateAI = async () => {

  aiLoading.value = true
  aiResult.value = null

  try {

    const res = await axios.post('/compare/ai', {
      products: products.value.map(p => p.id)
    })

    comparisonId.value = res.data.comparison_id

    startPolling()

  } catch (err) {

    console.error(err)

    aiLoading.value = false

  }

}

/*
|--------------------------------------------------------------------------
| POLLING
|--------------------------------------------------------------------------
*/

const startPolling = () => {

  pollInterval.value = setInterval(async () => {

    try {

      const res = await axios.get(
        `/compare/ai/${comparisonId.value}`
      )

      if (res.data.status === 'completed') {

        aiResult.value = res.data.result
        typeAiResult(res.data.result)

        aiLoading.value = false

        stopPolling()

        console.log('AI RESULT:', res.data.result)

      }

      if (res.data.status === 'failed') {

        aiLoading.value = false

        stopPolling()

      }

    } catch (err) {

      console.error(err)

      aiLoading.value = false

      stopPolling()

    }

  }, 2000)

}

const stopPolling = () => {

  if (pollInterval.value) {

    clearInterval(pollInterval.value)

    pollInterval.value = null

  }

}

onUnmounted(() => {
  stopPolling()
})

/*
|--------------------------------------------------------------------------
| SPEC TABLE HELPERS
|--------------------------------------------------------------------------
*/

const mergedAttributes = computed(() => {

  const attributes = []

  products.value.forEach(product => {

    product.attributes?.forEach(attr => {

      if (!attributes.includes(attr.attribute_name)) {

        attributes.push(attr.attribute_name)

      }

    })

  })

  return attributes

})

const getValue = (product, attr) => {

  const found = product.attributes?.find(
    a => a.attribute_name === attr
  )

  return found?.attribute_value || '—'

}






// Type out the AI result character by character

const typeAiResult = (text) => {

  displayedAiResult.value = ''

  let index = 0

  const speed = 8

  const interval = setInterval(() => {

    displayedAiResult.value += text.charAt(index)

    index++

    if (index >= text.length) {
      clearInterval(interval)
    }

  }, speed)

}


</script>