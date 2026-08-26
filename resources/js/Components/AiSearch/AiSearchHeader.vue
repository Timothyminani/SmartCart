<script setup>
import {
    nextTick,
    onMounted,
    ref,
    watch,
} from 'vue'

import {
    RotateCcw,
    Search,
} from 'lucide-vue-next'


const props = defineProps({
    modelValue: {
        type: String,
        default: '',
    },

    loading: {
        type: Boolean,
        default: false,
    },
})


const emit = defineEmits([
    'update:modelValue',
    'search',
])


const queryTextarea = ref(null)


const updateQuery = (event) => {
    emit(
        'update:modelValue',
        event.target.value
    )
}


const autoResize = async () => {

    await nextTick()

    const textarea =
        queryTextarea.value

    if (!textarea) {
        return
    }

    textarea.style.height = '0px'

    textarea.style.height =
        `${textarea.scrollHeight}px`
}


const submitSearch = () => {

    if (
        props.loading ||
        !props.modelValue.trim()
    ) {
        return
    }

    emit('search')
}


const handleKeydown = (event) => {

    if (
        event.key === 'Enter' &&
        !event.shiftKey
    ) {

        event.preventDefault()

        submitSearch()
    }
}


onMounted(() => {
    autoResize()
})


watch(
    () => props.modelValue,
    () => {
        autoResize()
    }
)
</script>


<template>

    <section class="mb-6">

        <!-- TITLE -->

        <div class="mb-3">

            <h1
                class="
                    text-lg
                    sm:text-xl
                    font-semibold
                    text-gray-900
                "
            >
                Refine your search
            </h1>

        </div>


        <!-- SEARCH -->

        <div
            class="
                bg-white
                border
                border-gray-200
                rounded-2xl
                shadow-sm

                p-3
            "
        >

            <div
                class="
                    flex
                    flex-col
                    sm:flex-row
                    sm:items-end
                    gap-3
                "
            >

                <!-- INPUT -->

                <div
                    class="
                        flex-1
                        min-w-0
                    "
                >

                    <div
                        class="
                            flex
                            items-start
                            gap-3
                        "
                    >

                        <div
                            class="
                                w-9
                                h-9
                                rounded-xl
                                bg-blue-50
                                flex
                                items-center
                                justify-center
                                shrink-0
                                mt-1
                            "
                        >

                            <Search
                                class="
                                    w-4
                                    h-4
                                    text-blue-600
                                "
                            />

                        </div>


                        <textarea
                            ref="queryTextarea"
                            :value="modelValue"
                            rows="1"
                            @input="updateQuery"
                            @keydown="handleKeydown"
                            placeholder="Describe what you want to find..."
                            class="
                                w-full

                                min-h-[44px]
                                max-h-[160px]

                                bg-transparent

                                text-gray-900
                                text-sm
                                sm:text-base

                                leading-6

                                px-1
                                py-2

                                resize-none
                                overflow-y-auto

                                border-0
                                outline-none

                                focus:outline-none
                                focus:ring-0

                                placeholder:text-gray-400
                            "
                        ></textarea>

                    </div>


                    <!-- DESKTOP HELPER -->

                    <p
                        class="
                            hidden
                            sm:block

                            text-xs
                            text-gray-400

                            mt-1
                            pl-12
                        "
                    >
                        Enter to update · Shift + Enter for a new line
                    </p>

                </div>


                <!-- ACTION -->

                <button
                    @click="submitSearch"
                    :disabled="
                        loading ||
                        !modelValue.trim()
                    "
                    type="button"
                    class="
                        w-full
                        sm:w-auto

                        shrink-0

                        inline-flex
                        items-center
                        justify-center
                        gap-2

                        px-5
                        py-2.5

                        bg-blue-600
                        hover:bg-blue-700

                        disabled:bg-gray-300
                        disabled:cursor-not-allowed

                        text-white
                        text-sm
                        font-semibold

                        rounded-xl

                        transition
                    "
                >

                    <RotateCcw
                        class="w-4 h-4"
                        :class="{
                            'animate-spin': loading
                        }"
                    />

                    <span>
                        {{
                            loading
                                ? 'Updating...'
                                : 'Update Results'
                        }}
                    </span>

                </button>

            </div>

        </div>

    </section>

</template>