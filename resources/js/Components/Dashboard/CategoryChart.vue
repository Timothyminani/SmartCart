<template>
    <div class="h-64">
        <Bar
            :data="chartData"
            :options="chartOptions"
        />
    </div>
</template>

<script setup>
import { ref } from 'vue'
import { Bar } from 'vue-chartjs'
import { computed } from 'vue'

import {
    Chart as ChartJS,
    CategoryScale,
    LinearScale,
    BarElement,
    Tooltip,
    Legend
} from 'chart.js'

const props = defineProps({
    chart: Object
})

ChartJS.register(
    CategoryScale,
    LinearScale,
    BarElement,
    Tooltip,
    Legend
)

const chartData = computed(() => ({
    labels: props.chart.labels,

    datasets: [
        {
            label: 'Products',

            data: props.chart.data,

            backgroundColor: [
                '#3B82F6',
                '#10B981',
                '#F59E0B',
                '#8B5CF6',
                '#EC4899'
            ],

            borderRadius: 8,
            borderSkipped: false
        }
    ]
}))

const chartOptions = {
    responsive: true,
    maintainAspectRatio: false,

    indexAxis: 'y', // 👈 Makes it horizontal

    plugins: {
        legend: {
            display: false
        }
    },

    scales: {
        x: {
            beginAtZero: true,
            grid: {
                color: '#f1f5f9'
            }
        },

        y: {
            grid: {
                display: false
            }
        }
    }
}
</script>