<template>
  <AppLayout>
    <div class="min-h-screen bg-gray-50 border border-3 border-gray-800">

      <div class="max-w-7xl mx-auto px-4 py-10 ">

        <!-- HEADER -->
 <div
  class="relative overflow-hidden bg-white border border-blue-100 rounded-3xl p-8 mb-8"
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

        <!-- COMPARE CONTENT -->
        <div v-else class="space-y-8  ">

          <!-- PRODUCTS -->
         <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

  <div
    v-for="product in products"
    :key="product.id"
    class="bg-white border rounded-3xl p-6"
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

        <!-- Footer -->
        <div class="mt-auto pt-4">
          <button
            @click="remove(product.id)"
            class="text-sm text-gray-500 hover:text-red-500 transition"
          >
            Remove from comparison
          </button>
        </div>

      </div>

    </div>

  </div>

</div>


          <!-- ================= SPEC TABLE ================= -->
          <div class="overflow-x-auto">

  <table class="w-full border border-gray-200">

    <thead>

      <tr class="bg-blue-50">

        <th
          class="border border-gray-200 p-4 text-left w-56 font-semibold"
        >
          Specification
        </th>

        <th
          v-for="p in products"
          :key="p.id"
          class="border border-gray-200 p-4 text-left min-w-[280px]"
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
          class="border border-gray-200 p-4 font-medium text-gray-800 bg-gray-50 capitalize"
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







          <!-- ================= AI SECTION (BELOW TABLE) ================= -->
          
<!-- AI ACTION -->
<div class="bg-white rounded-3xl border p-6 mt-6">

  <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">

    <div>
      <h2 class="text-xl font-semibold flex items-center gap-2">
        <Sparkles class="w-5 h-5 text-blue-600" />
        AI Insight
      </h2>

      <p class="text-sm text-gray-500 mt-1">
        Get smart AI comparison + recommendation
      </p>
    </div>

    <button
      @click="generateAI"
      :disabled="aiLoading"
      class="bg-blue-600 hover:bg-blue-700 transition text-white px-5 py-3 rounded-2xl text-sm font-medium disabled:opacity-60 disabled:cursor-not-allowed"
    >
      <span v-if="!aiLoading">
        Generate AI Insight
      </span>

      <span v-else class="flex items-center gap-2">
        <svg
          class="animate-spin h-4 w-4 text-white"
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

        Analyzing Products...
      </span>
    </button>

  </div>

</div>

<!-- AI LOADING STATE -->
<div
  v-if="aiLoading"
  class="bg-white rounded-3xl border p-6 mt-6 animate-pulse"
>

  <div class="flex items-center gap-3 mb-6">
    <div class="w-10 h-10 rounded-full bg-blue-100"></div>

    <div>
      <div class="h-4 w-40 bg-gray-200 rounded mb-2"></div>
      <div class="h-3 w-64 bg-gray-100 rounded"></div>
    </div>
  </div>

  <!-- fake cards -->
  <div class="space-y-4">

    <div class="border rounded-2xl p-4">
      <div class="h-4 w-32 bg-gray-200 rounded mb-3"></div>
      <div class="h-3 w-full bg-gray-100 rounded mb-2"></div>
      <div class="h-3 w-5/6 bg-gray-100 rounded"></div>
    </div>

    <div class="border rounded-2xl p-4">
      <div class="h-4 w-40 bg-gray-200 rounded mb-3"></div>
      <div class="h-3 w-full bg-gray-100 rounded mb-2"></div>
      <div class="h-3 w-4/6 bg-gray-100 rounded"></div>
    </div>

    <div class="border rounded-2xl p-4">
      <div class="h-4 w-24 bg-gray-200 rounded mb-3"></div>
      <div class="h-3 w-full bg-gray-100 rounded mb-2"></div>
      <div class="h-3 w-3/6 bg-gray-100 rounded"></div>
    </div>

  </div>

  <!-- AI Thinking -->
  <div class="mt-6 flex items-center gap-2 text-sm text-gray-500">
    <div class="w-2 h-2 bg-blue-500 rounded-full animate-bounce"></div>
    <div class="w-2 h-2 bg-blue-500 rounded-full animate-bounce delay-100"></div>
    <div class="w-2 h-2 bg-blue-500 rounded-full animate-bounce delay-200"></div>

    <span class="ml-2">
      AI is comparing specifications and analyzing value...
    </span>
  </div>

</div>






<!-- AI RESULT -->
<!-- AI RESULT -->
<div v-if="aiResult" class="bg-white rounded-3xl border p-6 mt-6 space-y-8">


<div class="bg-blue-50 border border-blue-100 p-4 rounded-2xl">

  <h3 class="font-semibold text-blue-700 mb-2">
    🧠 Summary
  </h3>

  <p class="text-gray-700">
    {{ aiResult?.summary }}
  </p>

</div>






  <!-- ================= TOP VERDICT ================= -->

  <div class="text-center border-b pb-6">

  <div class="inline-flex items-center gap-2 px-4 py-2 bg-green-50 text-green-700 rounded-full text-sm mb-4">
    🏆 AI Recommendation
  </div>

  <h2 class="text-2xl font-bold text-gray-900">
    {{ aiResult?.winner?.product_name }}
  </h2>

  <p class="text-gray-600 mt-3">
    Overall Score: {{ aiResult?.winner?.overall_score }}/100
  </p>

  <p class="text-gray-700 mt-2 max-w-2xl mx-auto">
    {{ aiResult?.winner?.reason }}
  </p>

</div>


  <!-- ================= QUICK COMPARISON ================= -->
  <div>

    <h3 class="font-semibold text-lg mb-4">⚖️ Quick Comparison</h3>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

      <div
        v-for="(item, key) in aiResult.comparison || {}"
        :key="key"
        class="border rounded-2xl p-4 bg-gray-50"
      >

        <div class="flex justify-between items-center mb-2">

          <p class="font-medium capitalize">
            {{ key.replace(/_/g, ' ') }}
          </p>

          <span class="text-xs px-3 py-1 bg-white border rounded-full">
            Winner:
            {{ item?.winner?.product_name || item?.winner }}
          </span>

        </div>

        <p class="text-sm text-gray-600">
          {{ item?.reason }}
        </p>

      </div>

    </div>

  </div>


<!-- ================= Overall Scores ================= -->

<div class="mt-6">

  <h3 class="font-semibold text-lg mb-4">📊 Overall Scores</h3>

  <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

    <div
      v-for="(data, name) in aiResult.scores"
      :key="name"
      class="border rounded-2xl p-4 bg-gray-50"
    >

      <p class="font-semibold mb-2">
        {{ name }}
      </p>

      <div class="w-full bg-gray-200 rounded-full h-3">
        <div
          class="bg-blue-600 h-3 rounded-full"
          :style="`width: ${data.overall}%`"
        ></div>
      </div>

      <p class="text-sm text-gray-600 mt-2">
        {{ data.overall }}/100
      </p>

    </div>

  </div>

</div>

  <!-- ================= BEST FOR ================= -->
<div>

  <h3 class="font-semibold text-lg mb-4">🎯 Best For You</h3>

  <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

    <div
      v-for="(item, key) in aiResult.best_for || {}"
      :key="key"
      class="border rounded-2xl p-4 bg-gray-50"
    >

      <div class="flex justify-between items-center mb-2">

        <p class="font-semibold capitalize">
          {{ key.replace(/_/g, ' ') }}
        </p>

      </div>

      <p class="text-sm text-gray-900 font-medium">
        {{ item.product_name }}
      </p>

      <p class="text-sm text-gray-600 mt-1">
        {{ item.reason }}
      </p>

    </div>

  </div>

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
import { computed, ref } from 'vue'
import { router } from '@inertiajs/vue3'
import axios from 'axios'
import { onUnmounted } from 'vue'

const props = defineProps({
  products: Array
})

const products = computed(() => props.products || [])

const aiSummary = ref('')
const loading = ref(false)
const aiLoading = ref(false)
const aiResult = ref(null)
const comparisonId = ref(null)
const pollInterval = ref(null)



const format = (v) => new Intl.NumberFormat().format(v)

const remove = (id) => {
  const updated = products.value.filter(p => p.id !== id)

  router.get(route('compare.index'), {
    products: updated.map(p => p.id)
  })
}

// AI SIMULATION (later we replace with backend + replicate)
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

  }

}




const startPolling = () => {

  pollInterval.value = setInterval(async () => {

    try {

      const res = await axios.get(`/compare/ai/${comparisonId.value}`)

      if (res.data.status === 'completed') {

        aiResult.value = res.data.result

        stopPolling()

        aiLoading.value = false
console.log("AI RESULT SET:", res.data.result)
      }

      if (res.data.status === 'failed') {

        stopPolling()

        aiLoading.value = false

      }

    } catch (err) {

      console.error(err)

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
</script>