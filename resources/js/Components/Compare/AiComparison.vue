<script setup>
import {
    onUnmounted,
    ref,
} from 'vue'

import axios from 'axios'
import MarkdownIt from 'markdown-it'

import {
    Loader2,
    Sparkles,
} from 'lucide-vue-next'


/*
|--------------------------------------------------------------------------
| PROPS
|--------------------------------------------------------------------------
*/

const props = defineProps({
    products: {
        type: Array,
        default: () => [],
    },
})


/*
|--------------------------------------------------------------------------
| STATE
|--------------------------------------------------------------------------
*/

const loading = ref(false)

const aiResult = ref('')
const displayedResult = ref('')

const comparisonId = ref(null)

let pollInterval = null
let typingInterval = null


/*
|--------------------------------------------------------------------------
| MARKDOWN
|--------------------------------------------------------------------------
*/

const markdown = new MarkdownIt({
    html: true,
    linkify: true,
    breaks: true,
})


const renderMarkdown = (text) => {

    if (!text) {
        return ''
    }

    return markdown.render(text)
}


/*
|--------------------------------------------------------------------------
| GENERATE COMPARISON
|--------------------------------------------------------------------------
*/

const generateAI = async () => {

    if (
        loading.value ||
        props.products.length < 2
    ) {
        return
    }


    loading.value = true

    aiResult.value = ''
    displayedResult.value = ''

    stopPolling()
    stopTyping()


    try {

        const response = await axios.post(
            '/compare/ai',
            {
                products:
                    props.products.map(
                        product => product.id
                    ),
            }
        )


        comparisonId.value =
            response.data.comparison_id


        startPolling()

    } catch (error) {

        console.error(
            'Failed to start AI comparison:',
            error
        )

        loading.value = false
    }
}


/*
|--------------------------------------------------------------------------
| POLLING
|--------------------------------------------------------------------------
*/

const startPolling = () => {

    if (!comparisonId.value) {
        return
    }


    stopPolling()


    pollInterval = setInterval(
        async () => {

            try {

                const response =
                    await axios.get(
                        `/compare/ai/${comparisonId.value}`
                    )


                /*
                |--------------------------------------------------------------------------
                | COMPLETED
                |--------------------------------------------------------------------------
                */

                if (
                    response.data.status ===
                    'completed'
                ) {

                    const result =
                        response.data.result || ''

                    aiResult.value = result

                    typeResult(result)

                    loading.value = false

                    stopPolling()

                    return
                }


                /*
                |--------------------------------------------------------------------------
                | FAILED
                |--------------------------------------------------------------------------
                */

                if (
                    response.data.status ===
                    'failed'
                ) {

                    loading.value = false

                    stopPolling()
                }

            } catch (error) {

                console.error(
                    'AI comparison polling failed:',
                    error
                )

                loading.value = false

                stopPolling()
            }

        },
        2000
    )
}


const stopPolling = () => {

    if (!pollInterval) {
        return
    }

    clearInterval(
        pollInterval
    )

    pollInterval = null
}


/*
|--------------------------------------------------------------------------
| TYPING EFFECT
|--------------------------------------------------------------------------
*/

const typeResult = (text) => {

    stopTyping()

    displayedResult.value = ''

    if (!text) {
        return
    }


    let index = 0

    const speed = 8


    typingInterval = setInterval(
        () => {

            displayedResult.value +=
                text.charAt(index)

            index++


            if (
                index >= text.length
            ) {

                stopTyping()
            }

        },
        speed
    )
}


const stopTyping = () => {

    if (!typingInterval) {
        return
    }

    clearInterval(
        typingInterval
    )

    typingInterval = null
}


/*
|--------------------------------------------------------------------------
| CLEANUP
|--------------------------------------------------------------------------
*/

onUnmounted(() => {
    stopPolling()
    stopTyping()
})
</script>


<template>

    <section
        class="
            relative
            overflow-hidden

            mt-8

            bg-gradient-to-br
            from-white
            via-white
            to-blue-50/30

            rounded-2xl
            sm:rounded-3xl

            border
            border-blue-100

            shadow-lg
        "
    >

        <!-- ========================================================= -->
        <!-- DECORATIVE BACKGROUND -->
        <!-- ========================================================= -->

        <div
            class="
                absolute
                top-0
                right-0

                w-48
                h-48

                sm:w-72
                sm:h-72

                bg-blue-100/30

                blur-3xl

                rounded-full

                pointer-events-none
            "
        ></div>


        <!-- ========================================================= -->
        <!-- CONTENT -->
        <!-- ========================================================= -->

        <div
            class="
                relative

                p-5
                sm:p-6
                lg:p-8
            "
        >

            <!-- ===================================================== -->
            <!-- HEADER -->
            <!-- ===================================================== -->

            <!-- HEADER -->

<div
    class="
        flex
        flex-col

        md:flex-row
        md:items-center
        md:justify-between

        gap-4
    "
>

    <!-- LEFT -->

    <div class="min-w-0 max-w-2xl">

        <div
            class="
                flex
                items-center
                gap-2
            "
        >

            <Sparkles
                class="
                    w-4
                    h-4
                    text-blue-600
                "
            />

            <h2
                class="
                    text-lg
                    sm:text-xl

                    font-semibold
                    text-gray-900
                "
            >
                AI Comparison
            </h2>

        </div>


        <p
            class="
                text-sm
                text-gray-500
                mt-1.5
                leading-6
            "
        >
            Get a second opinion on the key differences above.
        </p>

    </div>


    <!-- ACTION -->

    <div
        class="
            w-full
            md:w-auto
            shrink-0
        "
    >

        <button
            @click="generateAI"
            :disabled="
                loading ||
                products.length < 2
            "
            type="button"
            class="
                w-full
                md:w-auto

                inline-flex
                items-center
                justify-center
                gap-2

                px-5
                py-2.5

                rounded-xl

                bg-blue-600
                hover:bg-blue-700

                disabled:bg-gray-300
                disabled:cursor-not-allowed

                text-white
                text-sm
                font-semibold

                transition
            "
        >

            <Loader2
                v-if="loading"
                class="
                    w-4
                    h-4
                    animate-spin
                "
            />

            <Sparkles
                v-else
                class="
                    w-4
                    h-4
                "
            />

            <span>
                {{
                    loading
                        ? 'Analyzing...'
                        : 'Generate AI Insights'
                }}
            </span>

        </button>

    </div>

</div>

            <!-- ===================================================== -->
            <!-- LOADING -->
            <!-- ===================================================== -->

            <div
                v-if="loading"
                class="
                    mt-7

                    border-t
                    border-gray-100

                    pt-6
                "
            >

                <!-- STATUS -->

                <div
                    class="
                        flex
                        items-start
                        sm:items-center

                        gap-3

                        mb-6
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

                                [animation-delay:0.15s]
                            "
                        ></span>


                        <span
                            class="
                                w-2
                                h-2

                                bg-blue-500

                                rounded-full

                                animate-bounce

                                [animation-delay:0.3s]
                            "
                        ></span>

                    </div>


                    <p
                        class="
                            text-xs
                            sm:text-sm

                            text-gray-500

                            leading-5
                        "
                    >
                        AI is generating comparison insights...
                    </p>

                </div>


                <!-- SKELETON -->

                <div
                    class="
                        space-y-4
                        animate-pulse
                    "
                >

                    <div
                        class="
                            bg-white/70

                            rounded-2xl

                            border
                            border-gray-100

                            p-4
                            sm:p-5
                        "
                    >

                        <div
                            class="
                                h-4
                                w-40

                                bg-gray-200

                                rounded

                                mb-4
                            "
                        ></div>


                        <div class="space-y-2.5">

                            <div
                                class="
                                    h-3
                                    w-full

                                    bg-gray-100

                                    rounded
                                "
                            ></div>

                            <div
                                class="
                                    h-3
                                    w-11/12

                                    bg-gray-100

                                    rounded
                                "
                            ></div>

                            <div
                                class="
                                    h-3
                                    w-3/4

                                    bg-gray-100

                                    rounded
                                "
                            ></div>

                        </div>

                    </div>


                    <div
                        class="
                            bg-white/70

                            rounded-2xl

                            border
                            border-gray-100

                            p-4
                            sm:p-5
                        "
                    >

                        <div
                            class="
                                h-4
                                w-32

                                bg-gray-200

                                rounded

                                mb-4
                            "
                        ></div>


                        <div class="space-y-2.5">

                            <div
                                class="
                                    h-3
                                    w-full

                                    bg-gray-100

                                    rounded
                                "
                            ></div>

                            <div
                                class="
                                    h-3
                                    w-5/6

                                    bg-gray-100

                                    rounded
                                "
                            ></div>

                            <div
                                class="
                                    h-3
                                    w-2/3

                                    bg-gray-100

                                    rounded
                                "
                            ></div>

                        </div>

                    </div>

                </div>

            </div>


            <!-- ===================================================== -->
            <!-- AI RESULT -->
            <!-- ===================================================== -->

            <div
                v-if="aiResult && !loading"
                class="
                    mt-7

                    border-t
                    border-gray-100

                    pt-6
                "
            >

                <div
                    class="
                        max-w-none

                        text-gray-800

                        prose
                        prose-sm
                        sm:prose-base

                        prose-headings:font-semibold
                        prose-headings:text-gray-900

                        prose-h1:text-xl
                        sm:prose-h1:text-2xl

                        prose-h2:text-lg
                        sm:prose-h2:text-xl

                        prose-h3:text-base
                        sm:prose-h3:text-lg

                        prose-headings:mt-6
                        prose-headings:mb-3

                        prose-p:leading-6
                        sm:prose-p:leading-7

                        prose-p:my-2

                        prose-ul:my-3

                        prose-li:my-1.5

                        prose-li:marker:text-gray-500

                        prose-table:my-5

                        prose-th:bg-blue-50
                        prose-th:p-2
                        sm:prose-th:p-3

                        prose-td:p-2
                        sm:prose-td:p-3

                        prose-hr:my-6
                    "
                    v-html="
                        renderMarkdown(
                            displayedResult
                        )
                    "
                ></div>


                <!-- TYPING CURSOR -->

                <div
                    v-if="
                        displayedResult.length <
                        aiResult.length
                    "
                    class="mt-1"
                >

                    <span
                        class="
                            inline-block

                            w-1.5
                            h-5

                            bg-blue-600

                            animate-pulse

                            rounded-sm
                        "
                    ></span>

                </div>

            </div>

        </div>

    </section>

</template>