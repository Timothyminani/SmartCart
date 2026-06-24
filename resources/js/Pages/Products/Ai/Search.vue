<template>
  <AppLayout>

    <div class="h-[calc(100vh-64px)] flex bg-gray-50">

      
      
<!-- LEFT SIDEBAR -->


<aside
  :class="[
    'bg-white border-r border-gray-200 flex flex-col transition-all duration-300 shrink-0',
    sidebarOpen ? 'w-72' : 'w-20'
  ]"
>

  <!-- TOP -->
  <div class="p-4 border-b border-gray-100">

    <!-- HEADER -->
    <div
      :class="[
        'flex mb-4',
        sidebarOpen ? 'items-start justify-between' : 'justify-center'
      ]"
    >

      <!-- LOGO / TITLE -->
      <div v-if="sidebarOpen">

        <h2 class="font-bold text-gray-900 text-lg">
          SmartCart AI
        </h2>

        <p class="text-xs text-gray-500">
          Your shopping assistant
        </p>

      </div>

      <!-- TOGGLE -->
      <button
        @click="sidebarOpen = !sidebarOpen"
        class="w-10 h-10 rounded-xl hover:bg-gray-100 flex items-center justify-center transition"
      >
        <PanelLeft class="w-5 h-5 text-gray-600" />
      </button>

    </div>

    <!-- NEW SEARCH -->
    <button
      v-if="sidebarOpen"
      class="w-full bg-blue-600 hover:bg-blue-700 transition text-white py-3 rounded-2xl font-medium shadow-sm"
    >
      + New Search
    </button>

    <!-- COLLAPSED NEW SEARCH -->
    <button
      v-else
      class="w-10 h-10 mx-auto bg-blue-600 hover:bg-blue-700 transition text-white rounded-xl flex items-center justify-center"
    >
      +
    </button>

  </div>

  <!-- HISTORY -->
  <div class="flex-1 overflow-y-auto px-3 py-4">

    <!-- TITLE -->
    <div
      v-if="sidebarOpen"
      class="flex items-center justify-between mb-3 px-2"
    >

      <h3 class="text-xs font-semibold uppercase tracking-wide text-gray-400">
        Recent Searches
      </h3>

      <span class="text-xs text-gray-400">
        {{ recentSearches.length }}
      </span>

    </div>

    <!-- SEARCHES -->
    <div class="space-y-1">

      <button
        v-for="search in recentSearches"
        :key="search.id"
        @click="restoreSearch(search)"
        class="group w-full rounded-2xl hover:bg-gray-100 transition"
      >

        <!-- EXPANDED -->
        <div
          v-if="sidebarOpen"
          class="flex items-start gap-3 px-3 py-3 text-left"
        >

          <div
            class="w-9 h-9 rounded-xl bg-gray-100 group-hover:bg-white flex items-center justify-center shrink-0"
          >
            <RotateCcw class="w-4 h-4 text-gray-500" />
          </div>

          <div class="flex-1 min-w-0">

            <p class="text-sm text-gray-700 line-clamp-2 group-hover:text-black transition">
              {{ search.query }}
            </p>

            <p class="text-xs text-gray-400 mt-1">
               {{ formatTime(search.created_at) }}
            </p>

          </div>

        </div>

        <!-- COLLAPSED -->
        <div
          v-else
          class="flex justify-center py-3"
        >

          <div
            class="w-10 h-10 rounded-xl bg-gray-100 group-hover:bg-white flex items-center justify-center"
          >
            <RotateCcw class="w-4 h-4 text-gray-500" />
          </div>

        </div>

      </button>

    </div>

  </div>

</aside>





      <!-- MAIN CONTENT -->
      <main class="flex-1 flex flex-col">

        <!-- SCROLLABLE CONTENT -->
        <div 
        ref="resultsContainer"
        class="flex-1 overflow-y-auto">

          <!-- EMPTY STATE -->
          <div
            v-if="!query && !loading"
            class="h-full flex flex-col items-center justify-center px-6"
          >

            <div class="text-center">

              <h1 class="text-4xl font-bold mb-3">
                🤖 SmartCart AI
              </h1>

              <p class="text-gray-500 mb-8">
                What can I help you find today?
              </p>

              <div class="flex flex-wrap gap-3 justify-center">

                <button class="bg-white border px-4 py-2 rounded-xl">
                  Laptop for programming
                </button>

                <button class="bg-white border px-4 py-2 rounded-xl">
                  Gaming phone
                </button>

                <button class="bg-white border px-4 py-2 rounded-xl">
                  Wireless headphones
                </button>

              </div>

            </div>

          </div>

         
                            <!-- RESULTS -->
                    <div
                    v-if="query"
                    class="max-w-5xl mx-auto px-6 py-10"
                    >

                    <!-- USER MESSAGE -->
                    
                <div class="flex justify-end mb-8 flex-col items-end gap-3">

                <!-- TEXTAREA -->
            <textarea
            ref="queryTextarea"
            v-model="query"
            rows="1"
            @input="autoResize"
            class="
                w-full
                max-w-lg

                bg-blue-50
                text-blue-900

                px-4
                py-3

                rounded-2xl
                border
                border-blue-200

                resize-none
                overflow-hidden

                focus:outline-none
                focus:ring-2
                focus:ring-blue-300
            "
            style="height: auto;"
            ></textarea>

                <!-- BUTTON -->
            <button
            @click="runSearchFromQuery"
            class="flex items-center gap-2 px-5 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-xl transition"
            >
            <RotateCcw class="w-4 h-4" />
            <span>Update Results</span>
            </button>
                            </div>



                    
                    
                      <!-- AI RESPONSE -->
<div class="mb-10">

  <div class="bg-white border rounded-2xl p-6 shadow-sm">

    <!-- HEADER -->
    <div class="flex items-center gap-2 mb-5">
      <div class="w-9 h-9 rounded-full bg-blue-100 flex items-center justify-center">
        🤖
      </div>

      <div>
        <h3 class="font-semibold text-gray-900">
          SmartCart AI
        </h3>

        <p class="text-xs text-gray-500">
          AI-powered shopping assistant
        </p>
      </div>
    </div>

                    <!-- LOADING STATE -->
                    <div v-if="loading">

                    <!-- animated status -->
                    <div class="flex items-center gap-2 mb-6">

                        <div class="flex gap-1">
                        <div class="w-2 h-2 bg-blue-500 rounded-full animate-bounce"></div>
                        <div class="w-2 h-2 bg-blue-500 rounded-full animate-bounce [animation-delay:0.2s]"></div>
                        <div class="w-2 h-2 bg-blue-500 rounded-full animate-bounce [animation-delay:0.4s]"></div>
                        </div>

                        <p class="text-sm text-gray-500">
                        SmartCart AI is analyzing products and comparing specifications...
                        </p>

                    </div>

                    <!-- SECTION SKELETONS -->
                    <div class="space-y-6 animate-pulse">

                        <!-- overview -->
                        <div>
                        <div class="h-4 bg-gray-200  rounded w-48 mb-3"></div>

                        <div class="space-y-2">
                            <div class="h-3 bg-gray-200 rounded w-full"></div>
                            <div class="h-3 bg-gray-200 rounded w-5/6"></div>
                            <div class="h-3 bg-gray-200 rounded w-4/6"></div>
                        </div>
                        </div>

                        <!-- recommendations -->
                        <div>
                        <div class="h-4 bg-gray-200 rounded w-40 mb-4"></div>

                        <div class="space-y-3">
                            <div class="h-16 bg-gray-100 rounded-xl"></div>
                            <div class="h-16 bg-gray-100 rounded-xl"></div>
                        </div>
                        </div>

                        <!-- buying advice -->
                        <div>
                        <div class="h-4 bg-gray-200 rounded w-36 mb-3"></div>

                        <div class="space-y-2">
                            <div class="h-3 bg-gray-200 rounded w-full"></div>
                            <div class="h-3 bg-gray-200 rounded w-3/4"></div>
                        </div>
                        </div>

                    </div>

                    </div>

                    <!-- REAL AI RESPONSE -->
                
<div v-else>

  <!-- COLLAPSIBLE WRAPPER -->
  <div
    :class="[
      'relative overflow-hidden transition-all duration-500',
      showFullExplanation ? 'max-h-[5000px]' : 'max-h-[350px]'
    ]"
  >

    <!-- FADE EFFECT -->
    <div
      v-if="!showFullExplanation"
      class="absolute bottom-0 left-0 right-0 h-32 bg-gradient-to-t from-white to-transparent z-10"
    ></div>

    <!-- AI CONTENT -->
    <div
      class="
    prose
    prose-lg
    max-w-none

    prose-headings:font-bold
    prose-headings:text-gray-900

    prose-h2:text-2xl
    prose-h2:mb-2
    prose-h2:mt-6

    prose-h3:text-lg
    prose-h3:font-semibold

    prose-p:text-gray-700
    prose-p:leading-8
    prose-p:mb-3

    prose-ul:my-3
    prose-li:my-2
    prose-li:text-gray-700

    prose-strong:text-gray-900
    prose-strong:font-semibold

    prose-li:marker:text-black
    prose-li:marker:font-bold
  " 
      v-html="formattedExplanation"
    ></div>

  </div>

  <!-- TOGGLE BUTTON -->
  <div class="mt-4 flex justify-center">

    
<button
  @click="showFullExplanation = !showFullExplanation"
  class="px-5 py-2 rounded-xl border border-gray-200 hover:bg-gray-50 text-sm font-medium transition flex items-center gap-2"
>

  <span>
    {{ showFullExplanation ? 'Show Less' : 'Show More' }}
  </span>

  <ChevronUp
    v-if="showFullExplanation"
    class="w-4 h-4"
  />

  <ChevronDown
    v-else
    class="w-4 h-4"
  />

</button>



  </div>

</div>



                </div>

                </div>
                        



                    <!-- PRODUCTS -->
                    <div>

            <div class="flex items-center justify-between mb-4">

            <div>

                <h2 class="font-semibold text-lg text-gray-900">
                Recommended Products
                </h2>

                <p class="text-sm text-gray-500 mt-1">
                We found {{ products.length }} products matching your needs
                </p>

            </div>

            </div>

                        <!-- PRODUCT SKELETONS -->
                        <div
                        v-if="loading"
                        class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6"
                        >

                        <div
                            v-for="n in 4"
                            :key="n"
                            class="bg-white rounded-2xl border p-4 animate-pulse"
                        >

                            <div class="h-32 bg-gray-200 rounded-xl mb-4"></div>

                            <div class="h-3 bg-gray-200 rounded w-full mb-2"></div>

                            <div class="h-3 bg-gray-200 rounded w-2/3 mb-4"></div>

                            <div class="h-8 bg-gray-200 rounded-lg"></div>

                        </div>

                        </div>

                        <!-- REAL PRODUCTS -->
                        <div
                        v-else
                        class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6"
                        >

                        <ProductCard
                            v-for="product in products"
                            :key="product.id"
                            :product="product"
                        />

                        </div>

                    </div>

                    </div>

<!-- REFIMENT SUGGESTIONS -->
<div v-if="refinementSuggestions.length" class="mb-8 flex justify-center">
  <div class="w-full max-w-2xl">

  <h3 class="font-semibold text-gray-700 mb-4 flex items-center gap-2">
    
    You might also ask
  </h3>

  <div class="flex flex-col gap-2">

    <button
      v-for="(q, index) in refinementSuggestions"
      :key="index"
      @click="query = q; runSearchFromQuery()"
      class="
        group
        flex items-start gap-3
        text-left
        px-4 py-3
        rounded-xl
        border border-gray-100
        bg-white
        hover:bg-blue-50
        hover:border-blue-200
        transition
      "
    >

      <!-- Reply-style icon -->
      <div class="mt-0.5 text-blue-500">
        <CornerDownRight class="w-4 h-4" />
      </div>

      <!-- Question text -->
      <span class="text-sm text-gray-700 group-hover:text-blue-700 leading-relaxed">
        {{ q }}
      </span>

    </button>
</div>
  </div>

</div>

 <!-- Browse Catalog Bunner -->

<div class="mb-12 flex justify-center">

  <div
 class="
    relative
    overflow-hidden
    rounded-3xl
    w-full
    max-w-4xl
    min-h-[260px]

    bg-cover
    bg-center

    p-8
    text-white
    shadow-xl
  "
  :style="{
    backgroundImage: `url(${bannerImage})`
  }"
>

    <!-- DARK OVERLAY -->
    <div class="absolute inset-0 bg-black/55"></div>

    <!-- CONTENT -->
    <div
      class="
        relative
        z-10
        h-full

        flex
        flex-col
        md:flex-row

        items-start
        md:items-center

        justify-between
        gap-6
      "
    >

      <!-- TEXT -->
      <div class="max-w-xl">

        <p class="text-sm uppercase tracking-wider text-blue-200 mb-2">
          Explore More
        </p>

        <h2 class="text-3xl font-bold mb-3 leading-tight">
          Browse Our Full Product Catalog
        </h2>

        <p class="text-gray-200 leading-7">
          Discover trending products, explore categories,
          and shop with powerful filters beyond AI search.
        </p>

      </div>

      <!-- BUTTON -->
      <a
        href="/productListing"
        class="
          shrink-0
          px-6
          py-3
          rounded-2xl

          bg-white
          text-black
          font-semibold

          hover:bg-gray-100
          transition
        "
      >
        Browse Catalog
      </a>

    </div>

  </div>

</div>







</div>
                    




    
      </main>

    </div>

  </AppLayout>
</template>

<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'
import ProductCard from '@/Components/ProductCard.vue'
import { onMounted, ref } from 'vue'
import { usePage, router } from '@inertiajs/vue3'
import axios from 'axios'
import { marked } from 'marked'
import { computed } from 'vue'
import { PanelLeft, Search, ChevronDown, ChevronUp, RotateCcw,  CornerDownRight } from 'lucide-vue-next'
import { nextTick } from 'vue'
import { watch } from 'vue'
import bannerImage from '@/assets/images/banner4.jpg'

const page = usePage()
const query = ref('')
query.value = new URLSearchParams(window.location.search).get('q') || ''
const loading = ref(false)
const ai_explanation = ref('')
const products = ref([])
const sidebarOpen = ref(true)
const showFullExplanation = ref(false)
const recentSearches = ref([])
const refinementSuggestions = ref([])
const resultsContainer = ref(null)
const currentPage = ref(1)
const lastPage = ref(1)
const totalProducts = ref(0)
const loadingMore = ref(false)
const displayedExplanation = ref('')
const typingSpeed = 10



onMounted(() => {

  query.value =
    new URLSearchParams(window.location.search).get('q') || ''

  loadSearchHistory()

  // try restore existing search first
  const existingSearch = recentSearches.value.find(
    item => item.query === query.value
  )

  if (existingSearch) {

    ai_explanation.value = existingSearch.ai_explanation

  displayedExplanation.value = existingSearch.ai_explanation

    refinementSuggestions.value =
      existingSearch.refinement_suggestions || []

    products.value = existingSearch.products

    showFullExplanation.value = false

    nextTick(() => {
      autoResize()
    })

  } else {

    // only fetch if not cached
    fetchAIResults()

  }

})




const fetchAIResults = async () => {
  if (!query.value) return

  loading.value = true

  try {
    const res = await axios.post('/api/ai-search', {
      query: query.value
    })

    console.log("AI RESPONSE:", res.data)
    ai_explanation.value = res.data.ai_explanation
    typeExplanation(ai_explanation.value)
    refinementSuggestions.value = res.data.refinement_suggestions || []
    products.value = res.data.products
    saveSearchToHistory()

  } catch (error) {
    console.error(error)
  } finally {
    loading.value = false
  }
}







const runSearchFromQuery = async () => {
  if (!query.value.trim()) return

  resultsContainer.value?.scrollTo({
  top: 0,
  behavior: 'smooth'
})

  loading.value = true
  products.value = []
  ai_explanation.value = ''

  try {
    const res = await axios.post('/api/ai-search', {
      query: query.value
    })

    ai_explanation.value = res.data.ai_explanation
    typeExplanation(ai_explanation.value)
    refinementSuggestions.value = res.data.refinement_suggestions || []
    products.value = res.data.products
    saveSearchToHistory()

  } catch (error) {
    console.error(error)
  } finally {
    loading.value = false
  }
}






const queryTextarea = ref(null)

const autoResize = async () => {
  await nextTick()

  const textarea = queryTextarea.value

  if (!textarea) return

  textarea.style.height = '0px'
  textarea.style.height = textarea.scrollHeight + 'px'
}

onMounted(() => {
  autoResize()
})

watch(query, () => {
  autoResize()
})




const saveSearchToHistory = () => {

  const existingSearches = JSON.parse(
    localStorage.getItem('smartcart_searches')
  ) || []

  // remove duplicate query if it already exists
  const filtered = existingSearches.filter(
    item => item.query !== query.value
  )

  // new search object
  const newSearch = {
    id: Date.now(),
    query: query.value,
    ai_explanation: ai_explanation.value,
    refinement_suggestions: refinementSuggestions.value,
    products: products.value,
    created_at: new Date().toISOString()
  }

  // add newest at top
  filtered.unshift(newSearch)

  // limit to 10 searches
  const limited = filtered.slice(0, 10)

  localStorage.setItem(
    'smartcart_searches',
    JSON.stringify(limited)
  )

loadSearchHistory()


}

const loadSearchHistory = () => {

  recentSearches.value = JSON.parse(
    localStorage.getItem('smartcart_searches')
  ) || []

}


const restoreSearch = (search) => {

  query.value = search.query

  ai_explanation.value = search.ai_explanation

 displayedExplanation.value = search.ai_explanation

  refinementSuggestions.value = search.refinement_suggestions || []

  products.value = search.products

  showFullExplanation.value = false

 nextTick(() => {
    autoResize()
  })

}



const typeExplanation = async (text) => {

  displayedExplanation.value = ''

  for (let i = 0; i < text.length; i++) {

    displayedExplanation.value += text.charAt(i)

    await new Promise(resolve =>
      setTimeout(resolve, typingSpeed)
    )

  }

}


const formattedExplanation = computed(() => {
  return marked(displayedExplanation.value || '')
})




const formatTime = (date) => {

  const now = new Date()
  const created = new Date(date)

  const diffInSeconds =
    Math.floor((now - created) / 1000)

  if (diffInSeconds < 60) {
    return 'Just now'
  }

  const diffInMinutes =
    Math.floor(diffInSeconds / 60)

  if (diffInMinutes < 60) {
    return `${diffInMinutes} min ago`
  }

  const diffInHours =
    Math.floor(diffInMinutes / 60)

  if (diffInHours < 24) {
    return `${diffInHours} hr ago`
  }

  const diffInDays =
    Math.floor(diffInHours / 24)

  if (diffInDays < 7) {
    return `${diffInDays} day ago`
  }

  return created.toLocaleDateString()

}

</script>