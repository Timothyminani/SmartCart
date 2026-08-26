<script setup>
defineProps({
    productCount: {
        type: Number,
        default: 0,
    },
})
</script>


<template>

    <section
        class="
            mb-8
            sm:mb-10
            pb-5
            sm:pb-6
            border-b
            border-gray-200
        "
    >

        <div
            class="
                flex
                flex-col
                sm:flex-row
                sm:items-end
                sm:justify-between
                gap-4
            "
        >

            <!-- PAGE INTRO -->

            <div class="max-w-2xl">

                <h1
                    class="
                        text-2xl
                        sm:text-3xl
                        lg:text-4xl
                        font-bold
                        text-gray-900
                        leading-tight
                    "
                >
                    Compare Products
                </h1>

                <p
                    class="
                        mt-2
                        text-sm
                        sm:text-base
                        text-gray-500
                        leading-6
                    "
                >
                    Review specifications, pricing and key
                    differences side by side.
                </p>

            </div>


            <!-- COUNT -->

            <p
                class="
                    text-sm
                    text-gray-500
                    shrink-0
                "
            >
                Comparing

                <span class="font-semibold text-gray-900">
                    {{ productCount }}
                </span>

                {{
                    productCount === 1
                        ? 'product'
                        : 'products'
                }}
            </p>

        </div>

    </section>

</template>