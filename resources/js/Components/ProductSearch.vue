<script setup>
import { ref, watch, onBeforeUnmount } from 'vue'
import { router } from '@inertiajs/vue3'
import { Search, Loader2, X } from 'lucide-vue-next'
import axios from 'axios'


const suggestions = ref([])
const loading = ref(false)
const showDropdown = ref(false)
const params = new URLSearchParams(window.location.search)
const search = ref(params.get('query') || '')

let debounceTimer = null

const formatPrice = (value) => {
    return new Intl.NumberFormat().format(value || 0)
}

const submitSearch = () => {

    const query = search.value.trim()

    if (!query) {
        return
    }

    showDropdown.value = false

    router.get(
        route('products.index'),
        {
            query
        }
    )
}

const fetchSuggestions = async () => {

    const query = search.value.trim()

    if (query.length < 2) {
        suggestions.value = []
        showDropdown.value = false
        return
    }

    loading.value = true

    try {

        const response = await axios.get(
            route('search.suggestions'),
            {
                params: {
                    q: query
                }
            }
        )

        suggestions.value =
            response.data.products || []

        showDropdown.value = true

    } catch (error) {

        console.error(
            'Failed to load search suggestions:',
            error
        )

        suggestions.value = []

    } finally {

        loading.value = false

    }
}

watch(search, () => {

    clearTimeout(debounceTimer)

    const query = search.value.trim()

    if (query.length < 2) {
        suggestions.value = []
        showDropdown.value = false
        return
    }

    debounceTimer = setTimeout(() => {
        fetchSuggestions()
    }, 300)
})

const openProduct = (product) => {

    showDropdown.value = false

    router.visit(
        route('products.show', {
            product: product.slug
        })
    )
}

const clearSearch = () => {

    search.value = ''
    suggestions.value = []
    showDropdown.value = false
}

const handleClickOutside = (event) => {

    if (!event.target.closest('.product-search')) {
        showDropdown.value = false
    }
}

window.addEventListener(
    'click',
    handleClickOutside
)

onBeforeUnmount(() => {

    clearTimeout(debounceTimer)

    window.removeEventListener(
        'click',
        handleClickOutside
    )
})

</script>


<template>

<div class="flex-1 max-w-xl relative product-search">

    <div
        class="flex items-center bg-white rounded-2xl border border-gray-200
               shadow-sm transition-all
               focus-within:border-blue-500"
    >

        <div class="pl-4">
            <Search class="w-5 h-5 text-gray-400" />
        </div>

        <input
            v-model="search"
            @keyup.enter="submitSearch"
            @focus="
                search.trim().length >= 2 &&
                (showDropdown = true)
            "
            type="text"
            placeholder="Search products..."
            class="flex-1 px-3 py-2.5
                   bg-transparent
                   text-gray-700
                   placeholder:text-gray-400
                   border-0
                   outline-none
                   focus:outline-none
                   focus:ring-0
                   focus:border-transparent
                   shadow-none"
        />

        <button
            v-if="search"
            @click="clearSearch"
            type="button"
            class="w-8 h-8 flex items-center
                   justify-center
                   text-gray-400
                   hover:text-gray-700"
        >
            <X class="w-4 h-4" />
        </button>

        <button
            @click="submitSearch"
            type="button"
            class="m-1 w-10 h-10 rounded-xl
                   bg-blue-600 hover:bg-blue-700
                   text-white
                   flex items-center justify-center
                   transition"
        >
            <Loader2
                v-if="loading"
                class="w-5 h-5 animate-spin"
            />

            <Search
                v-else
                class="w-5 h-5"
            />
        </button>

    </div>


    <!-- SUGGESTIONS -->
    <div
        v-if="showDropdown"
        class="absolute top-full left-0 right-0
               mt-2 bg-white
               border border-gray-100
               rounded-2xl
               shadow-xl
               overflow-hidden
               z-[100]"
    >

        <!-- PRODUCTS -->
        <div
            v-if="suggestions.length"
            class="p-2"
        >

            <p
                class="px-3 py-2
                       text-[11px]
                       font-semibold
                       uppercase
                       tracking-wide
                       text-gray-400"
            >
                Products
            </p>

            <button
                v-for="product in suggestions"
                :key="product.id"
                @click="openProduct(product)"
                class="w-full
                       flex items-center gap-3
                       p-3
                       rounded-xl
                       hover:bg-gray-50
                       transition
                       text-left"
            >

                <div
                    class="w-12 h-12
                           rounded-lg
                           overflow-hidden
                           bg-gray-100
                           shrink-0"
                >

                    <img
                        v-if="product.image"
                        :src="`/storage/${product.image}`"
                        :alt="product.name"
                        class="w-full h-full object-cover"
                    />

                </div>


                <div class="flex-1 min-w-0">

                    <p
                        class="text-sm
                               font-medium
                               text-gray-800
                               line-clamp-1"
                    >
                        {{ product.name }}
                    </p>

                    <div
                        class="flex items-center
                               gap-2 mt-1"
                    >

                        <span
                            v-if="product.brand"
                            class="text-[10px]
                                   text-gray-400"
                        >
                            {{ product.brand }}
                        </span>

                        <span
                            v-if="product.category"
                            class="text-[10px]
                                   text-gray-400"
                        >
                            · {{ product.category }}
                        </span>

                    </div>

                </div>


                <div
                    class="text-sm
                           font-semibold
                           text-blue-600
                           shrink-0"
                >
                    KES
                    {{
                        formatPrice(
                            product.sale_price ||
                            product.price
                        )
                    }}
                </div>

            </button>

        </div>


        <!-- NO RESULTS -->
        <div
            v-else-if="!loading"
            class="px-5 py-6 text-center"
        >

            <p
                class="text-sm
                       font-medium
                       text-gray-700"
            >
                No products found
            </p>

            <p
                class="text-xs
                       text-gray-400
                       mt-1"
            >
                Try a different search term
            </p>

        </div>


        <!-- VIEW ALL -->
        <button
            v-if="search.trim().length >= 2"
            @click="submitSearch"
            class="w-full
                   border-t
                   border-gray-100
                   px-4 py-3
                   text-sm
                   font-medium
                   text-blue-600
                   hover:bg-blue-50
                   transition"
        >
            View all results for "{{ search }}"
        </button>

    </div>

</div>

</template>