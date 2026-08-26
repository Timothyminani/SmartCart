
<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'
import { Sparkles, GitCompare } from 'lucide-vue-next'
import { computed, ref, onUnmounted } from 'vue'
import { router } from '@inertiajs/vue3'
import axios from 'axios'
import MarkdownIt from 'markdown-it'
import ProductCard from '@/Components/ProductCard.vue'
import CompareHeader from '@/Components/Compare/CompareHeader.vue'
import CompareProducts from '@/Components/Compare/CompareProducts.vue'
import ComparisonTable from '@/Components/Compare/ComparisonTable.vue'
import AiComparison from '@/Components/Compare/AiComparison.vue'
import CompareDecision from '@/Components/Compare/CompareDecision.vue'

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







<template>
  <AppLayout>
    <div class="min-h-screen bg-gray-50 ">

      <div class="max-w-7xl mx-auto px-2 lg:px-10 py-10 ">



        <!-- HEADER -->

 <CompareHeader
    :product-count="products.length"
/>

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

<CompareProducts
    :products="products"
/>

<!-- END OF PRODUCTS COMPARE HIGHLIGHT-->





          <!-- ================= SPEC TABLE ================= -->
 <ComparisonTable
    :products="products"
/>
<!-- ================= END OF SPEC TABLE ================= -->






<!-- ================= AI WORKSPACE ================= -->

<AiComparison
    :products="products"
/>

<!-- ================= END AI WORKSPACE ================= -->



<!-- FINAL DECISION SECTION -->


<CompareDecision
    :products="products"
/>





        </div>

      </div>

    </div>
  </AppLayout>
</template>

