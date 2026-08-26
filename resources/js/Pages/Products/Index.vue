<script setup>
import { ref, watch } from 'vue'
import { router } from '@inertiajs/vue3'

import AppLayout from '@/Layouts/AppLayout.vue'

import ProductListingBanner from '@/Components/Products/ProductListingBanner.vue'
import ProductFilters from '@/Components/Products/ProductFilters.vue'
import ProductListingToolbar from '@/Components/Products/ProductListingToolbar.vue'
import ProductGrid from '@/Components/Products/ProductGrid.vue'
import LoadMoreProducts from '@/Components/Products/LoadMoreProducts.vue'


/*
|--------------------------------------------------------------------------
| PROPS
|--------------------------------------------------------------------------
*/

const props = defineProps({
    query: {
        type: String,
        default: '',
    },

    products: {
        type: Object,
        default: () => ({
            data: [],
            current_page: 1,
            last_page: 1,
        }),
    },

    categories: {
        type: Array,
        default: () => [],
    },

    brands: {
        type: Array,
        default: () => [],
    },

    filters: {
        type: Object,
        default: () => ({
            categories: [],
            brands: [],
            sort: '',
        }),
    },
})


/*
|--------------------------------------------------------------------------
| FILTER STATE
|--------------------------------------------------------------------------
*/

const selectedCategories = ref(
    props.filters?.categories || []
)

const selectedBrands = ref(
    props.filters?.brands || []
)

const selectedSort = ref(
    props.filters?.sort || ''
)


/*
|--------------------------------------------------------------------------
| PRODUCT STATE
|--------------------------------------------------------------------------
*/

const productList = ref(
    props.products?.data || []
)

const currentPage = ref(
    props.products?.current_page || 1
)

const lastPage = ref(
    props.products?.last_page || 1
)

const loading = ref(false)


/*
|--------------------------------------------------------------------------
| APPLY FILTERS
|--------------------------------------------------------------------------
*/

const applyFilters = () => {

    loading.value = true

    router.get(
        route('products.index'),
        {
            page: 1,

            query: props.query,

            categories:
                selectedCategories.value,

            brands:
                selectedBrands.value,

            sort:
                selectedSort.value,
        },
        {
            preserveState: true,
            preserveScroll: true,
            replace: true,

            onSuccess: (page) => {

                productList.value =
                    page.props.products?.data || []

                currentPage.value =
                    page.props.products?.current_page || 1

                lastPage.value =
                    page.props.products?.last_page || 1
            },

            onFinish: () => {
                loading.value = false
            },
        }
    )
}


/*
|--------------------------------------------------------------------------
| LOAD MORE
|--------------------------------------------------------------------------
*/

const loadMore = () => {

    if (
        loading.value ||
        currentPage.value >= lastPage.value
    ) {
        return
    }

    loading.value = true

    const nextPage =
        Number(currentPage.value) + 1

    router.get(
        route('products.index'),
        {
            page: nextPage,

            query:
                props.query,

            categories:
                selectedCategories.value,

            brands:
                selectedBrands.value,

            sort:
                selectedSort.value,
        },
        {
            preserveState: true,
            preserveScroll: true,

            only: [
                'products',
            ],

            onSuccess: (page) => {

                const pagination =
                    page.props.products

                const newProducts =
                    pagination?.data || []

                console.log('LOAD MORE:', {
                    requestedPage: nextPage,
                    currentPage:
                        pagination?.current_page,
                    lastPage:
                        pagination?.last_page,
                    total:
                        pagination?.total,
                    received:
                        newProducts.length,
                })

                productList.value = [
                    ...productList.value,
                    ...newProducts,
                ]

                currentPage.value =
                    Number(
                        pagination?.current_page ||
                        currentPage.value
                    )

                lastPage.value =
                    Number(
                        pagination?.last_page ||
                        lastPage.value
                    )
            },

            onFinish: () => {
                loading.value = false
            },
        }
    )
}

/*
|--------------------------------------------------------------------------
| SYNC SERVER PRODUCTS
|--------------------------------------------------------------------------
*/

watch(
    () => props.products,
    (newProducts) => {

        if (!newProducts) {
            return
        }

        productList.value =
            newProducts.data || []

        currentPage.value =
            newProducts.current_page || 1

        lastPage.value =
            newProducts.last_page || 1
    }
)
</script>


<template>

    <AppLayout>

        <div
            class="
                max-w-7xl
                mx-auto

                px-4
                sm:px-6

                py-6
                sm:py-8
                lg:py-10
            "
        >

            <!-- BANNER -->

            <ProductListingBanner />


            <!-- PRODUCTS AREA -->

          <div
              class="
                  flex
                  flex-col

                  lg:flex-row

                  gap-4
                  lg:gap-6
              "
          >

                <!-- FILTERS -->

                <ProductFilters
                    :categories="categories"
                    :brands="brands"

                    v-model:selected-categories="selectedCategories"
                    v-model:selected-brands="selectedBrands"

                    @change="applyFilters"
                />


                <!-- PRODUCT CONTENT -->

                <main
                    class="
                        flex-1
                        min-w-0
                    "
                >

                    <ProductListingToolbar
                        :product-count="productList.length"

                        v-model:sort="selectedSort"

                        @change="applyFilters"
                    />


                    <ProductGrid
                        :products="productList"
                    />

                </main>

            </div>


            <!-- LOAD MORE -->

            <LoadMoreProducts
                :loading="loading"
                :has-more="
                    currentPage < lastPage
                "
                @load="loadMore"
            />

        </div>

    </AppLayout>

</template>