<script setup>
defineProps({
    products: {
        type: Array,
        default: () => [],
    },
})


const formatPrice = (value) => {
    return new Intl.NumberFormat().format(
        Number(value || 0)
    )
}
</script>


<template>

    <section class="relative">

        <!-- PRODUCTS -->

        <div
            class="
                grid
                grid-cols-1
                lg:grid-cols-2

                gap-4
                lg:gap-6

                relative
            "
        >

            <article
                v-for="product in products"
                :key="product.id"
                class="
                    bg-white

                    border
                    border-gray-200

                    rounded-2xl

                    p-4
                    sm:p-5

                    transition
                "
            >

                <div
                    class="
                        flex
                        items-start

                        gap-4
                    "
                >

                    <!-- IMAGE -->

                    <div
                        class="
                            shrink-0
                        "
                    >

                        <img
                            :src="
                                product.images?.[0]
                                    ? `/storage/${product.images[0].image_path}`
                                    : '/placeholder.png'
                            "
                            :alt="product.name"
                            class="
                                w-24
                                h-24

                                sm:w-28
                                sm:h-28

                                object-cover

                                rounded-xl

                                bg-gray-50

                                border
                                border-gray-100
                            "
                        />

                    </div>


                    <!-- CONTENT -->

                    <div
                        class="
                            flex-1
                            min-w-0
                        "
                    >

                        <!-- BRAND -->

                        <p
                            v-if="product.brand?.name"
                            class="
                                text-xs
                                font-semibold

                                uppercase
                                tracking-wide

                                text-blue-600
                            "
                        >
                            {{ product.brand.name }}
                        </p>


                        <!-- NAME -->

                        <h2
                            class="
                                text-base
                                sm:text-lg

                                font-semibold

                                text-gray-900

                                mt-1

                                leading-snug

                                line-clamp-2
                            "
                        >
                            {{ product.name }}
                        </h2>


                        <!-- PRICE -->

                        <div
                            class="
                                mt-4
                            "
                        >

                            <p
                                class="
                                    text-xs
                                    text-gray-400
                                "
                            >
                                Price
                            </p>


                            <p
                                class="
                                    text-xl
                                    sm:text-2xl

                                    font-bold

                                    text-gray-900
                                "
                            >
                                KES
                                {{
                                    formatPrice(
                                        product.sale_price ||
                                        product.price
                                    )
                                }}
                            </p>

                        </div>

                    </div>

                </div>

            </article>


            <!-- VS -->

            <div
                v-if="products.length === 2"
                class="
                    hidden
                    lg:flex

                    absolute

                    left-1/2
                    top-1/2

                    -translate-x-1/2
                    -translate-y-1/2

                    z-10

                    w-10
                    h-10

                    rounded-full

                    bg-gray-900
                    text-white

                    items-center
                    justify-center

                    text-xs
                    font-bold

                    border-4
                    border-gray-50
                "
            >
                VS
            </div>

        </div>

    </section>

</template>