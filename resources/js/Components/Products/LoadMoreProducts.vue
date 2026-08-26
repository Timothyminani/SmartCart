<script setup>
import {
    ArrowDown,
    Loader2,
} from 'lucide-vue-next'


defineProps({
    loading: {
        type: Boolean,
        default: false,
    },

    hasMore: {
        type: Boolean,
        default: false,
    },
})


const emit = defineEmits([
    'load',
])
</script>


<template>

    <section
        class="
            flex
            justify-center
            mt-8
            sm:mt-10
        "
    >

        <!-- LOAD MORE -->

        <button
            v-if="hasMore"
            @click="emit('load')"
            :disabled="loading"
            type="button"
            class="
                inline-flex
                items-center
                justify-center
                gap-2

                px-4
                py-2

                rounded-lg

                text-sm
                font-semibold

                text-blue-600

                hover:text-blue-700
                hover:bg-blue-50

                disabled:text-gray-400
                disabled:bg-transparent
                disabled:cursor-not-allowed

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

            <ArrowDown
                v-else
                class="
                    w-4
                    h-4
                "
            />


            <span>
                {{
                    loading
                        ? 'Loading...'
                        : 'Load More Products'
                }}
            </span>

        </button>


        <!-- END -->

        <p
            v-else
            class="
                text-xs
                sm:text-sm
                text-gray-400
            "
        >
            You've reached the end.
        </p>

    </section>

</template>