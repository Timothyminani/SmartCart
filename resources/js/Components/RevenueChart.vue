<template>
    <div class="h-64 w-full">
        <Bar :data="chartData" :options="chartOptions" />
    </div>
</template>

<script setup>
import { ref } from 'vue'
import { Bar } from 'vue-chartjs'

import {
    Chart as ChartJS,
    Title,
    Tooltip,
    Legend,
    BarElement,
    CategoryScale,
    LinearScale
} from 'chart.js'

ChartJS.register(
    Title,
    Tooltip,
    Legend,
    BarElement,
    CategoryScale,
    LinearScale
)

// Dummy data (later from backend)
const chartData = ref({
    labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
    datasets: [
        {
            label: 'Revenue (Ksh)',
            data: [12000, 19000, 15000, 22000, 30000, 27000],
             backgroundColor: [
                '#3B82F6',
                '#6366F1',
                '#8B5CF6',
                '#10B981',
                '#F59E0B',
                '#EF4444'
            ],
            borderWidth: 2,
        }
    ]
})

const chartOptions = {
    responsive: true,
    maintainAspectRatio: false,
}
</script>