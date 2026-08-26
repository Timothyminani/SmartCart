<script setup>
import {
    CornerDownRight,
    Sparkles,
} from 'lucide-vue-next'


defineProps({
    suggestions: {
        type: Array,
        default: () => [],
    },

    loading: {
        type: Boolean,
        default: false,
    },
})


const emit = defineEmits([
    'select',
])


const selectSuggestion = (suggestion) => {
    emit(
        'select',
        suggestion
    )
}
</script>


<template>

    <section
        v-if="
            loading ||
            suggestions.length
        "
        class="
            mb-8
            flex
            justify-center
        "
    >

        <div
            class="
                w-full
                max-w-3xl
            "
        >

            <!-- HEADER -->

            <div
                class="
                    flex
                    items-center
                    gap-2
                    mb-3
                "
            >

                <div
                    class="
                        w-7
                        h-7
                        rounded-lg
                        bg-blue-50
                        flex
                        items-center
                        justify-center
                        shrink-0
                    "
                >

                    <Sparkles
                        class="
                            w-3.5
                            h-3.5
                            text-blue-600
                        "
                    />

                </div>


                <div>

                    <h3
                        class="
                            text-sm
                            sm:text-base
                            font-semibold
                            text-gray-900
                        "
                    >
                        You might also ask
                    </h3>

                    <p
                        class="
                            hidden
                            sm:block
                            text-xs
                            text-gray-500
                            mt-0.5
                        "
                    >
                        Refine your search with one of these suggestions.
                    </p>

                </div>

            </div>


            <!-- LOADING -->

            <div
                v-if="loading"
                class="
                    flex
                    flex-col
                    gap-2
                "
            >

                <div
                    v-for="n in 4"
                    :key="n"
                    class="
                        h-11
                        rounded-lg
                        bg-white
                        border
                        border-gray-100
                        animate-pulse
                    "
                ></div>

            </div>


            <!-- SUGGESTIONS -->

            <div
                v-else
                class="
                    flex
                    flex-col
                    gap-2
                "
            >

                <button
                    v-for="(suggestion, index) in suggestions"
                    :key="`${index}-${suggestion}`"
                    @click="selectSuggestion(suggestion)"
                    type="button"
                    class="
                        group
                        w-full

                        flex
                        items-center
                        gap-2.5

                        text-left

                        px-3
                        py-2.5

                        rounded-lg

                        border
                        border-gray-100

                        bg-white

                        hover:bg-blue-50
                        hover:border-blue-200

                        transition
                    "
                >

                    <!-- ICON -->

                    <CornerDownRight
                        class="
                            w-4
                            h-4
                            text-blue-500
                            shrink-0
                        "
                    />


                    <!-- QUESTION -->

                    <span
                        class="
                            text-sm
                            text-gray-700
                            group-hover:text-blue-700
                            leading-5
                            transition
                        "
                    >
                        {{ suggestion }}
                    </span>

                </button>

            </div>

        </div>

    </section>

</template>