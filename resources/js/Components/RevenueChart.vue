<template>
    <div class="h-72 w-full">
        <Line :data="chartData" :options="chartOptions" />
    </div>
</template>

<script setup>
import { ref } from 'vue'
import { Line } from 'vue-chartjs'
import { computed } from 'vue'

import {
    Chart as ChartJS,
    Title,
    Tooltip,
    Legend,
    LineElement,
    PointElement,
    CategoryScale,
    LinearScale,
    Filler
} from 'chart.js'

ChartJS.register(
    Title,
    Tooltip,
    Legend,
    LineElement,
    PointElement,
    CategoryScale,
    LinearScale,
    Filler
)

const props = defineProps({
    chart: Object
})





const chartData = computed(() => ({
    labels: props.chart.labels,

    datasets: [
        {
            label: 'Revenue (Ksh)',

            data: props.chart.data,

            borderColor: '#3B82F6',
            backgroundColor: 'rgba(59,130,246,0.12)',

            fill: true,
            tension: 0.4,

            pointRadius: 5,
            pointHoverRadius: 7,

            pointBackgroundColor: '#3B82F6',
            pointBorderColor: '#fff',
            pointBorderWidth: 2,
        }
    ]
}))



const chartOptions = {
    responsive: true,
    maintainAspectRatio: false,

    plugins: {
        legend: {
            display: false
        }
    },

    scales: {
        y: {
            beginAtZero: true,
            grid: {
                color: '#F1F5F9'
            }
        },

        x: {
            grid: {
                display: false
            }
        }
    }
}

</script>