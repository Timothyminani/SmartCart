<script setup>
import { onMounted, ref } from 'vue'
import { router } from '@inertiajs/vue3'
import axios from 'axios'

import AppLayout from '@/Layouts/AppLayout.vue'

import SearchHistorySidebar from '@/Components/AiSearch/SearchHistorySidebar.vue'
import AiSearchHeader from '@/Components/AiSearch/AiSearchHeader.vue'
import IntentSummary from '@/Components/AiSearch/IntentSummary.vue'
import SearchResults from '@/Components/AiSearch/SearchResults.vue'
import AiExplanation from '@/Components/AiSearch/AiExplanation.vue'
import RefinementSuggestions from '@/Components/AiSearch/RefinementSuggestions.vue'
import CatalogBanner from '@/Components/AiSearch/CatalogBanner.vue'
import AiPromptBox from '@/Components/Home/AiPromptBox.vue'

/*
|--------------------------------------------------------------------------
| STATE
|--------------------------------------------------------------------------
*/

const query = ref(
    new URLSearchParams(
        window.location.search
    ).get('q') || ''
)

const loading = ref(false)

const intent = ref(null)
const products = ref([])

const aiExplanation = ref('')
const displayedExplanation = ref('')

const refinementSuggestions = ref([])
const recentSearches = ref([])

const resultsContainer = ref(null)

const typingSpeed = 10


/*
|--------------------------------------------------------------------------
| SEARCH
|--------------------------------------------------------------------------
*/

const performSearch = async ({
    scrollToTop = false,
} = {}) => {

    const searchQuery =
        query.value.trim()

    if (!searchQuery) {
        return
    }


    if (scrollToTop) {

        resultsContainer.value?.scrollTo({
            top: 0,
            behavior: 'smooth',
        })

    }


    loading.value = true

    /*
    |--------------------------------------------------------------------------
    | CLEAR PREVIOUS RESULT
    |--------------------------------------------------------------------------
    */

    intent.value = null
    products.value = []

    aiExplanation.value = ''
    displayedExplanation.value = ''

    refinementSuggestions.value = []


    try {

        const response = await axios.post(
            '/api/ai-search',
            {
                query: searchQuery,
            }
        )


        /*
        |--------------------------------------------------------------------------
        | STORE RESPONSE
        |--------------------------------------------------------------------------
        */

        intent.value =
            response.data.intent || null

        products.value =
            response.data.products || []

        aiExplanation.value =
            response.data.ai_explanation || ''

        refinementSuggestions.value =
            response.data.refinement_suggestions || []


        /*
        |--------------------------------------------------------------------------
        | DISPLAY AI EXPLANATION
        |--------------------------------------------------------------------------
        */

        typeExplanation(
            aiExplanation.value
        )


        /*
        |--------------------------------------------------------------------------
        | SAVE HISTORY
        |--------------------------------------------------------------------------
        */

        saveSearchToHistory()

    } catch (error) {

        console.error(
            'AI search failed:',
            error
        )

    } finally {

        loading.value = false

    }
}


/*
|--------------------------------------------------------------------------
| INITIAL SEARCH
|--------------------------------------------------------------------------
*/

const fetchAIResults = () => {

    return performSearch()

}


/*
|--------------------------------------------------------------------------
| UPDATE SEARCH
|--------------------------------------------------------------------------
*/

const runSearchFromQuery = () => {

    return performSearch({
        scrollToTop: true,
    })

}


/*
|--------------------------------------------------------------------------
| REFINEMENT SEARCH
|--------------------------------------------------------------------------
*/

const handleRefinement = async (suggestion) => {

    query.value = suggestion

    await runSearchFromQuery()

}


/*
|--------------------------------------------------------------------------
| EXAMPLE SEARCH
|--------------------------------------------------------------------------
*/

const runExampleSearch = async (example) => {

    query.value = example

    await runSearchFromQuery()

}


/*
|--------------------------------------------------------------------------
| NEW SEARCH
|--------------------------------------------------------------------------
*/

const startNewSearch = () => {

    query.value = ''

    intent.value = null

    products.value = []

    aiExplanation.value = ''
    displayedExplanation.value = ''

    refinementSuggestions.value = []

    /*
    |--------------------------------------------------------------------------
    | REMOVE OLD QUERY FROM URL
    |--------------------------------------------------------------------------
    */

    router.visit(
        '/ai-search',
        {
            preserveScroll: false,
            preserveState: true,
        }
    )
}


/*
|--------------------------------------------------------------------------
| SEARCH HISTORY
|--------------------------------------------------------------------------
*/

const loadSearchHistory = () => {

    try {

        recentSearches.value =
            JSON.parse(
                localStorage.getItem(
                    'smartcart_searches'
                )
            ) || []

    } catch (error) {

        console.error(
            'Failed to load AI search history:',
            error
        )

        recentSearches.value = []

    }
}


const saveSearchToHistory = () => {

    let existingSearches = []

    try {

        existingSearches =
            JSON.parse(
                localStorage.getItem(
                    'smartcart_searches'
                )
            ) || []

    } catch {

        existingSearches = []

    }


    /*
    |--------------------------------------------------------------------------
    | REMOVE DUPLICATE
    |--------------------------------------------------------------------------
    */

    const filtered =
        existingSearches.filter(
            item =>
                item.query !== query.value
        )


    /*
    |--------------------------------------------------------------------------
    | CREATE HISTORY ITEM
    |--------------------------------------------------------------------------
    */

    const newSearch = {

        id: Date.now(),

        query:
            query.value,

        intent:
            intent.value,

        products:
            products.value,

        ai_explanation:
            aiExplanation.value,

        refinement_suggestions:
            refinementSuggestions.value,

        created_at:
            new Date().toISOString(),

    }


    /*
    |--------------------------------------------------------------------------
    | KEEP LATEST 10
    |--------------------------------------------------------------------------
    */

    filtered.unshift(
        newSearch
    )

    const limited =
        filtered.slice(0, 10)


    localStorage.setItem(
        'smartcart_searches',
        JSON.stringify(limited)
    )


    recentSearches.value =
        limited
}


/*
|--------------------------------------------------------------------------
| RESTORE SEARCH
|--------------------------------------------------------------------------
*/

const restoreSearch = (search) => {

    query.value =
        search.query || ''

    intent.value =
        search.intent || null

    products.value =
        search.products || []

    aiExplanation.value =
        search.ai_explanation || ''

    displayedExplanation.value =
        search.ai_explanation || ''

    refinementSuggestions.value =
        search.refinement_suggestions || []


    /*
    |--------------------------------------------------------------------------
    | RETURN TO TOP
    |--------------------------------------------------------------------------
    */

    resultsContainer.value?.scrollTo({
        top: 0,
        behavior: 'smooth',
    })
}


/*
|--------------------------------------------------------------------------
| AI EXPLANATION TYPING
|--------------------------------------------------------------------------
*/

const typeExplanation = async (text) => {

    displayedExplanation.value = ''

    if (!text) {
        return
    }

    for (
        let index = 0;
        index < text.length;
        index++
    ) {

        displayedExplanation.value +=
            text.charAt(index)

        await new Promise(resolve => {

            setTimeout(
                resolve,
                typingSpeed
            )

        })

    }
}


/*
|--------------------------------------------------------------------------
| INITIALIZE
|--------------------------------------------------------------------------
*/

onMounted(() => {

    loadSearchHistory()


    /*
    |--------------------------------------------------------------------------
    | NOTHING TO SEARCH
    |--------------------------------------------------------------------------
    */

    if (!query.value) {
        return
    }


    /*
    |--------------------------------------------------------------------------
    | CHECK CACHE FIRST
    |--------------------------------------------------------------------------
    */

    const existingSearch =
        recentSearches.value.find(
            item =>
                item.query === query.value
        )


    if (existingSearch) {

        restoreSearch(
            existingSearch
        )

        return
    }


    /*
    |--------------------------------------------------------------------------
    | FETCH FRESH RESULTS
    |--------------------------------------------------------------------------
    */

    fetchAIResults()
})
</script>


<template>

    <AppLayout>

        <div
    class="
        h-[calc(100vh-64px)]
        flex
        bg-gray-50
        overflow-hidden
    "
>

            <!-- ===================================================== -->
            <!-- SEARCH HISTORY -->
            <!-- ===================================================== -->

            <SearchHistorySidebar
                :searches="recentSearches"
                @restore="restoreSearch"
                @new-search="startNewSearch"
            />


            <!-- ===================================================== -->
            <!-- MAIN -->
            <!-- ===================================================== -->

            <main
                class="flex-1
                       flex
                       flex-col
                       min-w-0"
            >

                <div
                    ref="resultsContainer"
                    class="flex-1
                           overflow-y-auto"
                >

                    <!-- ================================================= -->
                    <!-- EMPTY STATE -->
                    <!-- ================================================= -->

                  <!-- ================================================= -->
<!-- EMPTY STATE -->
<!-- ================================================= -->

<div
    v-if="!query && !loading"
    class="
        min-h-full

        flex
        items-center
        justify-center

        px-4
        sm:px-6

        py-10
        sm:py-14
    "
>

    <div
        class="
            w-full
            max-w-2xl
        "
    >

        <!-- INTRO -->

        <div class="text-center">

            <h1
                class="
                    text-2xl
                    sm:text-3xl
                    lg:text-4xl

                    font-bold
                    tracking-tight
                    text-gray-900
                "
            >
                What are you looking for?
            </h1>


            <p
                class="
                    max-w-lg
                    mx-auto

                    mt-3

                    text-sm
                    sm:text-base

                    leading-6
                    text-gray-500
                "
            >
                Describe what you need, your budget,
                and what matters most to you.
            </p>

        </div>


        <!-- AI PROMPT -->

        <div
            class="
                mt-7
                sm:mt-8
            "
        >

            <AiPromptBox
                v-model="query"
                :loading="loading"
                placeholder="e.g. A good laptop for programming under KES 60,000"
                @submit="runSearchFromQuery"
            />

        </div>


        <!-- EXAMPLES -->

        <div
            class="
                mt-6
                sm:mt-7
            "
        >

            <p
                class="
                    mb-3

                    text-center
                    text-xs
                    font-medium
                    text-gray-400
                "
            >
                Or try one of these
            </p>


            <div
                class="
                    flex
                    flex-wrap
                    justify-center

                    gap-2
                "
            >

                <button
                    @click="
                        runExampleSearch(
                            'Laptop for programming'
                        )
                    "
                    type="button"
                    class="
                        px-3
                        py-2

                        rounded-lg

                        border
                        border-gray-200

                        bg-white

                        text-xs
                        sm:text-sm
                        text-gray-600

                        hover:border-blue-200
                        hover:bg-blue-50
                        hover:text-blue-700

                        transition
                    "
                >
                    Laptop for programming
                </button>


                <button
                    @click="
                        runExampleSearch(
                            'Gaming phone'
                        )
                    "
                    type="button"
                    class="
                        px-3
                        py-2

                        rounded-lg

                        border
                        border-gray-200

                        bg-white

                        text-xs
                        sm:text-sm
                        text-gray-600

                        hover:border-blue-200
                        hover:bg-blue-50
                        hover:text-blue-700

                        transition
                    "
                >
                    Gaming phone
                </button>


                <button
                    @click="
                        runExampleSearch(
                            'Wireless headphones'
                        )
                    "
                    type="button"
                    class="
                        px-3
                        py-2

                        rounded-lg

                        border
                        border-gray-200

                        bg-white

                        text-xs
                        sm:text-sm
                        text-gray-600

                        hover:border-blue-200
                        hover:bg-blue-50
                        hover:text-blue-700

                        transition
                    "
                >
                    Wireless headphones
                </button>

            </div>

        </div>

    </div>

</div>
                    <!-- ================================================= -->
                    <!-- RESULTS -->
                    <!-- ================================================= -->

                    <div
                        v-else-if="query"
                        class="max-w-5xl
                               mx-auto
                               px-4
                               sm:px-6
                               py-6
                               sm:py-10"
                    >

                        <!-- SEARCH -->

                        <AiSearchHeader
                            v-model="query"
                            :loading="loading"
                            @search="runSearchFromQuery"
                        />


                        <!-- WHAT AI UNDERSTOOD -->
                      
                        <!-- <IntentSummary
                            :intent="intent"
                            :loading="loading"
                        /> ---->
                      


                        <!-- AI BUYING ADVICE -->

                        <AiExplanation
                            :explanation="displayedExplanation"
                            :loading="loading"
                        />


                         <!-- PRODUCTS -->

                        <SearchResults
                            :products="products"
                            :loading="loading"
                        />


                        <!-- FOLLOW-UP SEARCHES -->

                        <RefinementSuggestions
                            :suggestions="refinementSuggestions"
                            :loading="loading"
                            @select="handleRefinement"
                        />


                        <!-- CATALOG -->

                        <CatalogBanner />

                    </div>

                </div>

            </main>

        </div>

    </AppLayout>

</template>