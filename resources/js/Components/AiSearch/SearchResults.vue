<script setup>
import ProductCard from '@/Components/ProductCard.vue'

defineProps({
    products: {
        type: Array,
        default: () => [],
    },

    loading: {
        type: Boolean,
        default: false,
    },
})
</script>


<template>

    <section class="mb-10">

        <!-- HEADER -->
        <div
            class="
                flex
                items-start
                sm:items-center
                justify-between
                gap-4
                mb-5
            "
        >

            <div>

                <h2
                    class="
                        text-lg
                        sm:text-xl
                        font-semibold
                        text-gray-900
                    "
                >
                    Recommended Products
                </h2>

                <p
                    v-if="loading"
                    class="text-sm text-gray-500 mt-1"
                >
                    Finding the best matches for you...
                </p>

                <p
                    v-else
                    class="text-sm text-gray-500 mt-1"
                >
                    We found
                    {{ products.length }}
                    {{ products.length === 1 ? 'product' : 'products' }}
                    matching your needs
                </p>

            </div>

        </div>


        <!-- ===================================================== -->
        <!-- LOADING SKELETONS -->
        <!-- ===================================================== -->

        <div
            v-if="loading"
            class="
                grid
                grid-cols-1
                sm:grid-cols-2
                md:grid-cols-3
                lg:grid-cols-4
                gap-4
                sm:gap-5
                lg:gap-6
            "
        >

            <div
                v-for="n in 4"
                :key="n"
                class="
                    bg-white
                    rounded-2xl
                    border
                    border-gray-100
                    p-4
                    animate-pulse
                "
            >

                <!-- IMAGE -->
                <div
                    class="
                        aspect-square
                        bg-gray-200
                        rounded-xl
                        mb-4
                    "
                ></div>


                <!-- PRODUCT NAME -->
                <div
                    class="
                        h-3
                        bg-gray-200
                        rounded
                        w-full
                        mb-2
                    "
                ></div>

                <div
                    class="
                        h-3
                        bg-gray-200
                        rounded
                        w-2/3
                        mb-4
                    "
                ></div>


                <!-- PRICE -->
                <div
                    class="
                        h-4
                        bg-gray-200
                        rounded
                        w-1/2
                        mb-4
                    "
                ></div>


                <!-- BUTTON -->
                <div
                    class="
                        h-10
                        bg-gray-200
                        rounded-xl
                    "
                ></div>

            </div>

        </div>


        <!-- ===================================================== -->
        <!-- PRODUCTS -->
        <!-- ===================================================== -->

        <div
            v-else-if="products.length"
            class="
                grid
                grid-cols-1
                sm:grid-cols-2
                md:grid-cols-3
                lg:grid-cols-4
                gap-4
                sm:gap-5
                lg:gap-6
            "
        >

            <ProductCard
                v-for="product in products"
                :key="product.id"
                :product="product"
            />

        </div>


        <!-- ===================================================== -->
        <!-- NO RESULTS -->
        <!-- ===================================================== -->

        <div
            v-else
            class="
                bg-white
                border
                border-gray-100
                rounded-2xl
                px-6
                py-12
                text-center
            "
        >

            <div
                class="
                    w-12
                    h-12
                    mx-auto
                    mb-4
                    rounded-full
                    bg-gray-100
                    flex
                    items-center
                    justify-center
                    text-xl
                "
            >
                🔍
            </div>

            <h3
                class="
                    font-semibold
                    text-gray-900
                "
            >
                No matching products found
            </h3>

            <p
                class="
                    text-sm
                    text-gray-500
                    mt-2
                    max-w-md
                    mx-auto
                "
            >
                Try adjusting your budget, brand,
                specifications, or what you plan to use
                the product for.
            </p>

        </div>

    </section>

</template>