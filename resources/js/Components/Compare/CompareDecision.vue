<script setup>
import ProductCard from '@/Components/ProductCard.vue'
import { Link } from '@inertiajs/vue3'
import { ArrowRight } from 'lucide-vue-next'


defineProps({
    products: {
        type: Array,
        default: () => [],
    },
})
</script>


<template>

    <section class="mt-8 sm:mt-10">

        <!-- HEADER -->

        <div
            class="
                mb-5
                sm:mb-6

                text-center
                sm:text-left
            "
        >

            <h2
                class="
                    text-xl
                    sm:text-2xl

                    font-bold
                    text-gray-900
                "
            >
                Ready to Choose?
            </h2>


            <p
                class="
                    text-sm
                    text-gray-500

                    mt-1.5
                "
            >
                Add your preferred product to cart
                or continue exploring.
            </p>

        </div>


        <!-- PRODUCTS -->

        <div
            class="
                grid
                grid-cols-1
                sm:grid-cols-2

                gap-4
                sm:gap-5
                lg:gap-6

                max-w-4xl
            "
        >

            <div
                v-for="product in products"
                :key="product.id"
                class="
                    flex
                    justify-center

                    sm:justify-start
                "
            >

                <ProductCard
                    :product="product"
                    compact
                />

            </div>

        </div>


        <!-- BROWSE MORE -->

        <div
            class="
                mt-7
                pt-6

                border-t
                border-gray-100

                flex
                flex-col
                sm:flex-row

                items-center
                justify-between

                gap-4
            "
        >

            <p
                class="
                    text-sm
                    text-gray-500
                    text-center
                    sm:text-left
                "
            >
                Still looking for something else?
            </p>


            <Link
                href="/productListing"
                class="
                    w-full
                    sm:w-auto

                    inline-flex
                    items-center
                    justify-center
                    gap-2

                    px-5
                    py-2.5

                    rounded-xl

                    border
                    border-gray-200

                    bg-white

                    text-sm
                    font-semibold
                    text-gray-700

                    hover:border-blue-200
                    hover:bg-blue-50
                    hover:text-blue-600

                    transition
                "
            >

                Browse More Products

                <ArrowRight
                    class="
                        w-4
                        h-4
                    "
                />

            </Link>

        </div>

    </section>

</template>