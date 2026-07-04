<template>
    <div class="h-64 w-full">
        <Doughnut
            :data="chartData"
            :options="chartOptions"
        />
    </div>
</template>

<script setup>
import { computed } from 'vue'
import { Doughnut } from 'vue-chartjs'

import {
    Chart as ChartJS,
    ArcElement,
    Tooltip,
    Legend
} from 'chart.js'

ChartJS.register(
    ArcElement,
    Tooltip,
    Legend
)

const props = defineProps({
    chart: Object
})

const chartData = computed(() => ({
    labels: props.chart.labels,

    datasets: [
        {
            data: props.chart.data,

            backgroundColor: [
                '#F59E0B', // Pending
                '#10B981', // Completed
                '#EF4444'  // Cancelled
            ],

            borderWidth: 0,
            hoverOffset: 8
        }
    ]
}))

const chartOptions = {
    responsive: true,
    maintainAspectRatio: false,

    plugins: {
        legend: {
            position: 'bottom',

            labels: {
                usePointStyle: true,
                pointStyle: 'circle',
                padding: 20
            }
        }
    },

    cutout: '70%'
}
</script>