<template>

<Link
        :href="href"
        class="block"
    >
    <div
        class="relative bg-white/50 backdrop-blur-lg rounded-2xl shadow-sm border border-gray-100 p-5 pt-6 overflow-visible  "
    >
        <!-- Top Colored Border -->
        <div
            :class="topBorderClass"
            class="absolute top-0 left-0 w-full h-1 rounded-t-2xl"
        ></div>

        <!-- Floating Icon -->
        <div
            :class="iconBoxClass"
            class="absolute -top-6 right-2 w-14 h-14 rounded-2xl flex items-center justify-center shadow-lg"
        >
            <component
                :is="icon"
                class="w-8 h-8 text-white"
            />
        </div>

        <!-- Title -->
        <p class="text-gray-800 ">
            {{ title }}
        </p>

        <!-- Value -->
        <h2  class="text-3xl font-bold text-gray-800 mt-3 whitespace-nowrap">
            {{ value }}
        </h2>

        <div class="mt-1 flex justify-end">
            <span
                class="flex items-center text-sm font-medium text-gray-500 group-hover:text-blue-600  transition"
            >
                view details
                <ArrowUpRight   class="w-4 h-4" />
            </span>
        </div>

    </div>

    </Link>
</template>

<script setup>
import { computed } from 'vue'
import { Link } from '@inertiajs/vue3'
import { ArrowUpRight } from 'lucide-vue-next'

const props = defineProps({
    title: String,
    value: [String, Number],
    icon: Object,
    color: {
        type: String,
        default: 'blue'
    },

     href: String
})

const colors = {
    blue: {
        top: 'bg-blue-500',
        box: 'bg-blue-500'
    },
    green: {
        top: 'bg-green-500',
        box: 'bg-green-500'
    },
    purple: {
        top: 'bg-purple-500',
        box: 'bg-purple-500'
    },
    yellow: {
        top: 'bg-yellow-500',
        box: 'bg-yellow-500'
    },
    red: {
        top: 'bg-red-500',
        box: 'bg-red-500'
    },
    orange: {
        top: 'bg-orange-500',
        box: 'bg-orange-500'
    },
    indigo: {
        top: 'bg-indigo-500',
        box: 'bg-indigo-500'
    },
    emerald: {
        top: 'bg-emerald-500',
        box: 'bg-emerald-500'
    }
}
const topBorderClass = computed(() => colors[props.color].top)

const iconBoxClass = computed(() => colors[props.color].box)
</script>