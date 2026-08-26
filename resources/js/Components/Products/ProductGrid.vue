<script setup>
import ProductCard from '@/Components/ProductCard.vue'


defineProps({
    products: {
        type: Array,
        default: () => [],
    },
})
</script>


<template>

    <section>

        <!-- PRODUCT GRID -->

        <div
            v-if="products.length"
            class="
                grid
                grid-cols-2
                md:grid-cols-3
                lg:grid-cols-4

                gap-3
                sm:gap-4
                lg:gap-6
            "
        >

            <div
                v-for="product in products"
                :key="product.id"
                class="
                    w-full
                    min-w-0
                "
            >

                <ProductCard
                    :product="product"
                />

            </div>

        </div>


        <!-- EMPTY STATE -->

        <div
            v-else
            class="
                bg-white

                border
                border-gray-100

                rounded-2xl

                px-5
                sm:px-6

                py-9
                sm:py-12

                text-center
            "
        >

            <div
                class="
                    w-11
                    h-11

                    mx-auto

                    rounded-xl

                    bg-gray-100

                    flex
                    items-center
                    justify-center

                    text-lg
                "
            >
                🔍
            </div>


            <h3
                class="
                    text-sm
                    sm:text-base

                    font-semibold
                    text-gray-900

                    mt-4
                "
            >
                No products found
            </h3>


            <p
                class="
                    text-xs
                    sm:text-sm

                    text-gray-500

                    mt-2

                    max-w-sm
                    mx-auto

                    leading-5
                    sm:leading-6
                "
            >
                Try adjusting your filters or search
                to find more products.
            </p>

        </div>

    </section>

</template>