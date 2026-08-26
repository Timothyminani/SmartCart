<script setup>
import { computed, ref, watch } from 'vue'
import { marked } from 'marked'

import {
    Bot,
    ChevronDown,
    ChevronUp,
    Sparkles,
} from 'lucide-vue-next'


const props = defineProps({
    explanation: {
        type: String,
        default: '',
    },

    loading: {
        type: Boolean,
        default: false,
    },
})


const showFullExplanation = ref(false)


const formattedExplanation = computed(() => {

    if (!props.explanation) {
        return ''
    }

    return marked(
        props.explanation
    )
})


watch(
    () => props.explanation,
    () => {
        showFullExplanation.value = false
    }
)
</script>


<template>

    <section class="mb-8">

        <div
            class="
                bg-white
                border
                border-gray-100
                rounded-2xl
                p-4
                sm:p-5
            "
        >

            <!-- ===================================================== -->
            <!-- HEADER -->
            <!-- ===================================================== -->

            <div
                class="
                    flex
                    items-center
                    gap-3
                    mb-4
                "
            >

                <div
                    class="
                        w-9
                        h-9
                        sm:w-10
                        sm:h-10

                        rounded-xl

                        bg-blue-50

                        flex
                        items-center
                        justify-center

                        shrink-0
                    "
                >

                    <Bot
                        class="
                            w-4
                            h-4
                            sm:w-5
                            sm:h-5

                            text-blue-600
                        "
                    />

                </div>


                <div class="min-w-0">

                    <div
                        class="
                            flex
                            items-center
                            gap-2
                        "
                    >

                        <h3
                            class="
                                text-sm
                                sm:text-base

                                font-semibold
                                text-gray-900

                                truncate
                            "
                        >
                            SmartCart Recommendation
                        </h3>


                        <Sparkles
                            class="
                                w-3.5
                                h-3.5
                                sm:w-4
                                sm:h-4

                                text-blue-500

                                shrink-0
                            "
                        />

                    </div>


                    <p
                        class="
                            text-[11px]
                            sm:text-xs

                            text-gray-400

                            mt-0.5
                        "
                    >
                        Buying advice based on your search
                    </p>

                </div>

            </div>


            <!-- ===================================================== -->
            <!-- LOADING -->
            <!-- ===================================================== -->

            <div v-if="loading">

                <!-- STATUS -->

                <div
                    class="
                        flex
                        items-start
                        sm:items-center

                        gap-3

                        mb-5
                    "
                >

                    <div
                        class="
                            flex
                            gap-1

                            mt-1
                            sm:mt-0

                            shrink-0
                        "
                    >

                        <span
                            class="
                                w-2
                                h-2
                                bg-blue-500
                                rounded-full
                                animate-bounce
                            "
                        ></span>

                        <span
                            class="
                                w-2
                                h-2
                                bg-blue-500
                                rounded-full
                                animate-bounce
                                [animation-delay:0.2s]
                            "
                        ></span>

                        <span
                            class="
                                w-2
                                h-2
                                bg-blue-500
                                rounded-full
                                animate-bounce
                                [animation-delay:0.4s]
                            "
                        ></span>

                    </div>


                    <p
                        class="
                            text-xs
                            sm:text-sm

                            text-gray-500

                            leading-5
                            sm:leading-6
                        "
                    >
                        SmartCart AI is comparing the strongest matches...
                    </p>

                </div>


                <!-- SKELETON -->

                <div
                    class="
                        space-y-5
                        animate-pulse
                    "
                >

                    <div>

                        <div
                            class="
                                h-3.5
                                bg-gray-200
                                rounded
                                w-36
                                mb-3
                            "
                        ></div>

                        <div class="space-y-2">

                            <div
                                class="
                                    h-3
                                    bg-gray-200
                                    rounded
                                    w-full
                                "
                            ></div>

                            <div
                                class="
                                    h-3
                                    bg-gray-200
                                    rounded
                                    w-5/6
                                "
                            ></div>

                            <div
                                class="
                                    h-3
                                    bg-gray-200
                                    rounded
                                    w-2/3
                                "
                            ></div>

                        </div>

                    </div>


                    <div class="space-y-2">

                        <div
                            class="
                                h-12
                                sm:h-14

                                bg-gray-100
                                rounded-xl
                            "
                        ></div>

                        <div
                            class="
                                h-12
                                sm:h-14

                                bg-gray-100
                                rounded-xl
                            "
                        ></div>

                    </div>


                    <div class="space-y-2">

                        <div
                            class="
                                h-3
                                bg-gray-200
                                rounded
                                w-full
                            "
                        ></div>

                        <div
                            class="
                                h-3
                                bg-gray-200
                                rounded
                                w-3/4
                            "
                        ></div>

                    </div>

                </div>

            </div>


            <!-- ===================================================== -->
            <!-- RESPONSE -->
            <!-- ===================================================== -->

            <div
                v-else-if="explanation"
            >

                <div
                    :class="[
                        `
                            relative
                            overflow-hidden
                            transition-all
                            duration-500
                        `,

                        showFullExplanation
                            ? 'max-h-[5000px]'
                            : 'max-h-[220px] sm:max-h-[280px]'
                    ]"
                >

                    <!-- FADE -->

                    <div
                        v-if="!showFullExplanation"
                        class="
                            absolute
                            bottom-0
                            left-0
                            right-0

                            h-20
                            sm:h-24

                            bg-gradient-to-t
                            from-white
                            via-white/90
                            to-transparent

                            z-10

                            pointer-events-none
                        "
                    ></div>


                    <!-- MARKDOWN -->

                    <div
                        class="
                            prose
                            prose-sm

                            max-w-none

                            prose-headings:font-bold
                            prose-headings:text-gray-900

                            prose-h2:text-lg
                            sm:prose-h2:text-xl
                            prose-h2:mt-5
                            prose-h2:mb-2

                            prose-h3:text-base
                            prose-h3:font-semibold

                            prose-p:text-gray-700
                            prose-p:leading-6
                            sm:prose-p:leading-7
                            prose-p:mb-3

                            prose-ul:my-3

                            prose-li:my-1
                            prose-li:text-gray-700

                            prose-strong:text-gray-900
                            prose-strong:font-semibold

                            prose-li:marker:text-gray-400
                        "
                        v-html="formattedExplanation"
                    ></div>

                </div>


                <!-- TOGGLE -->

                <div
                    class="
                        mt-4
                        flex
                        justify-center
                    "
                >

                    <button
                        @click="
                            showFullExplanation =
                                !showFullExplanation
                        "
                        type="button"
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

                            hover:bg-gray-50

                            text-sm
                            font-medium
                            text-gray-700

                            transition
                        "
                    >

                        <span>
                            {{
                                showFullExplanation
                                    ? 'Show Less'
                                    : 'Read Full Advice'
                            }}
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


            <!-- ===================================================== -->
            <!-- EMPTY -->
            <!-- ===================================================== -->

            <div
                v-else
                class="
                    py-6
                    sm:py-8
                    text-center
                "
            >

                <Bot
                    class="
                        w-7
                        h-7
                        mx-auto
                        text-gray-300
                    "
                />

                <p
                    class="
                        text-xs
                        sm:text-sm
                        text-gray-500
                        mt-3
                    "
                >
                    No buying advice is available for this search.
                </p>

            </div>

        </div>

    </section>

</template>