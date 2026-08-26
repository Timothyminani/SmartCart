<script setup>
import { computed } from 'vue'
import { Sparkles } from 'lucide-vue-next'

const props = defineProps({
    intent: {
        type: Object,
        default: null,
    },

    loading: {
        type: Boolean,
        default: false,
    },
})


/*
|--------------------------------------------------------------------------
| INTENT VALUES
|--------------------------------------------------------------------------
*/

const category = computed(() => {
    return props.intent?.category?.primary || null
})

const brand = computed(() => {
    return props.intent?.brand || null
})

const useCase = computed(() => {
    return props.intent?.use_case || null
})

const budget = computed(() => {

    if (!props.intent?.budget_max) {
        return null
    }

    return Number(
        props.intent.budget_max
    ).toLocaleString()
})


/*
|--------------------------------------------------------------------------
| SUMMARY ITEMS
|--------------------------------------------------------------------------
*/

const summaryItems = computed(() => {

    const items = []

    if (category.value) {
        items.push(category.value)
    }

    if (brand.value) {
        items.push(brand.value)
    }

    if (useCase.value) {
        items.push(useCase.value)
    }

    if (budget.value) {
        items.push(`Up to KES ${budget.value}`)
    }

    return items
})


/*
|--------------------------------------------------------------------------
| HAS SUMMARY
|--------------------------------------------------------------------------
*/

const hasSummary = computed(() => {
    return summaryItems.value.length > 0
})
</script>


<template>

    <section
        v-if="loading || hasSummary"
        class="mb-5"
    >

        <!-- ========================================================= -->
        <!-- LOADING -->
        <!-- ========================================================= -->

        <div
            v-if="loading"
            class="
                flex
                items-center
                gap-3

                min-h-[46px]

                px-4
                py-2.5

                rounded-xl

                bg-white

                border
                border-gray-100
            "
        >

            <div
                class="
                    w-4
                    h-4
                    rounded-full
                    bg-gray-200
                    animate-pulse
                    shrink-0
                "
            ></div>


            <div
                class="
                    h-3
                    w-28
                    rounded
                    bg-gray-200
                    animate-pulse
                    shrink-0
                "
            ></div>


            <div
                class="
                    flex
                    items-center
                    gap-2
                    overflow-hidden
                "
            >

                <div
                    v-for="n in 3"
                    :key="n"
                    class="
                        h-7
                        w-24
                        rounded-full
                        bg-gray-100
                        animate-pulse
                        shrink-0
                    "
                ></div>

            </div>

        </div>


        <!-- ========================================================= -->
        <!-- SUMMARY -->
        <!-- ========================================================= -->

        <div
            v-else
            class="
                flex
                items-center
                gap-3

                min-h-[46px]

                px-3
                sm:px-4

                py-2

                rounded-xl

                bg-white

                border
                border-gray-100

                overflow-hidden
            "
        >

            <!-- LABEL -->

            <div
                class="
                    flex
                    items-center
                    gap-2
                    shrink-0
                "
            >

                <Sparkles
                    class="
                        w-4
                        h-4
                        text-blue-600
                    "
                />

                <span
                    class="
                        hidden
                        sm:inline

                        text-xs
                        font-semibold
                        text-gray-600
                        whitespace-nowrap
                    "
                >
                    SmartCart understood
                </span>

            </div>


            <!-- DIVIDER -->

            <div
                class="
                    hidden
                    sm:block

                    w-px
                    h-5
                    bg-gray-200
                    shrink-0
                "
            ></div>


            <!-- ITEMS -->

            <div
                class="
                    flex
                    items-center
                    gap-2

                    overflow-x-auto

                    min-w-0

                    [scrollbar-width:none]
                    [&::-webkit-scrollbar]:hidden
                "
            >

                <span
                    v-for="item in summaryItems"
                    :key="item"
                    class="
                        shrink-0

                        px-3
                        py-1.5

                        rounded-full

                        bg-gray-50

                        border
                        border-gray-100

                        text-xs
                        font-medium
                        text-gray-700

                        whitespace-nowrap
                    "
                >
                    {{ item }}
                </span>

            </div>

        </div>

    </section>

</template>