<template>
    <AdminLayout>




        <!-- Page Title -->
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-gray-800">
                Dashboard Overview
            </h1>
            <p class="text-gray-500">
                Welcome back 👋 Here is what’s happening today.
            </p>
        </div>

        <!-- Overview Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">

                  <div class="bg-white p-6 rounded-xl shadow flex justify-between items-center">
        <div>
            <p class="text-gray-500">Total Products</p>
            <h2 class="text-3xl font-bold">0</h2>
        </div>
        <Package class="w-8 h-8 text-blue-500" />
    </div>

    <div class="bg-white p-6 rounded-xl shadow flex justify-between items-center">
        <div>
            <p class="text-gray-500">Total Orders</p>
            <h2 class="text-3xl font-bold">0</h2>
        </div>
        <ShoppingCart class="w-8 h-8 text-green-500" />
    </div>

    <div class="bg-white p-6 rounded-xl shadow flex justify-between items-center">
        <div>
            <p class="text-gray-500">Total Users</p>
            <h2 class="text-3xl font-bold">0</h2>
        </div>
        <Users class="w-8 h-8 text-purple-500" />
    </div>

    <div class="bg-white p-6 rounded-xl shadow flex justify-between items-center">
        <div>
            <p class="text-gray-500">Revenue</p>
            <h2 class="text-3xl font-bold">Ksh 0</h2>
        </div>
        <DollarSign class="w-8 h-8 text-yellow-500" />
    </div>

        </div>

        <!-- Graph Section -->
        <div class="bg-white p-6 rounded-xl shadow mb-8">
            <h3 class="text-lg font-semibold mb-4">
                Monthly Revenue
            </h3>

            <!-- Graph will go here later -->
            <div class="h-64 flex items-center justify-center text-gray-400">
                <RevenueChart />
            </div>
        </div>

        <!-- Recent Orders Table -->
        <div class="bg-white p-6 rounded-xl shadow">

            <h3 class="text-lg font-semibold mb-4">
                Recent Orders
            </h3>

            <div class="overflow-x-auto">
                <table class="w-full text-left">

                    <thead class="border-b">
                        <tr class="text-gray-600">
                            <th class="py-2">Order ID</th>
                            <th>Customer</th>
                            <th>Total</th>
                            <th>Status</th>
                            <th>Date</th>
                        </tr>
                    </thead>

                    <tbody>
                        <tr class="border-b">
                            <td class="py-2">#001</td>
                            <td>John Doe</td>
                            <td>Ksh 5,000</td>
                            <td>Pending</td>
                            <td>Today</td>
                        </tr>
                    </tbody>

                </table>
            </div>

        </div>




    </AdminLayout>


</template>

<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue'
import { ref } from 'vue'
import RevenueChart from '@/Components/RevenueChart.vue'

import {
   ShoppingCart,
    Users,
    BarChart2,
    DollarSign,
    Package
} from 'lucide-vue-next'
</script>