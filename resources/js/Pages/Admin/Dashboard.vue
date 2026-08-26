<template>
    <AdminLayout
    title="Dashboard"
    :showBackButton="false"
     :subtitle=" `Welcome back ${$page.props.auth.user.name}!👋 Here is what's happening today.`"
    >




        <!-- Page Title -->
        <div class="mb-7">
            <h1 class="text-xl font-bold text-gray-600">
                Sales Overview
            </h1>

           

        </div>

        <!-- Overview Cards -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-10 mb-8 ">

    <StatCard
        title="Total Products"
        :value="stats.products"
        :icon="Package"
         href="/admin/products"
        color="blue"
    />

    <StatCard
        title="Total Orders"
        :value="stats.orders"
        :icon="ShoppingCart"
        href="/admin/orders"
        color="green"
    />

    <StatCard
        title="Total Users"
        :value="stats.users"
        :icon="Users"
        href="/admin/users"
        color="purple"
    />

    <StatCard
        title="Revenue"
        :value="formattedRevenue"
        :icon="DollarSign"
        href="/admin/orders"
        color="yellow"
    />

    <StatCard
        title="Out of Stock"
        :value="stats.outOfStock"
        :icon="PackageX"
        href="/admin/products?stock=out"
        color="red"
    />
  
  <StatCard
        title="Low Stock"
        :value="stats.lowStock"
        :icon="TriangleAlert"
        href="/admin/products?stock=low"
        color="orange"
    />

    <StatCard
        title="Pending Orders"
        :value="stats.pendingOrders"
        :icon="Clock3"
        href="/admin/orders?status=pending"
        color="indigo"
    />

    <StatCard
        title="Completed Orders"
        :value="stats.completedOrders"
        :icon="CircleCheck"
         href="/admin/orders?status=delivered"
        color="emerald"
    />

</div>


        <!-- Graph Section -->

        <div class=" p-6 rounded-xl shadow mb-8 bg-white/50 backdrop-blur-lg">
            <h3 class="text-lg font-semibold mb-4">
                Monthly Revenue
            </h3>

            <!-- Graph will go here later -->
            <div class="h-64 flex items-center justify-center text-gray-400">
               <RevenueChart :chart="revenueChart" />
            </div>
        </div>





        <!-- Charts -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">

        <div class="bg-white/50 backdrop-blur-lg rounded-xl shadow p-6">

                        <h3 class="text-lg font-semibold mb-4">
                            Order Status
                        </h3>

                        <OrderStatusChart :chart="orderStatusChart"/>

                    </div>







            <div class="bg-white/50 backdrop-blur-lg rounded-xl shadow p-6">

                <h3 class="text-lg font-semibold mb-4">
                    Products by Category
                </h3>

                <CategoryChart  :chart="categoryChart" />

            </div>

            

        </div>



        <!-- Recent Orders -->
<div class="bg-white/50 backdrop-blur-lg rounded-2xl shadow-sm border border-gray-100 p-6">

    <!-- Header -->
    <div class="flex items-center justify-between mb-6">

        <div>
            <h3 class="text-xl font-semibold text-gray-800">
                Recent Orders
            </h3>

            <p class="text-sm text-gray-500">
                Latest customer purchases
            </p>
        </div>

        <Link
            href="/admin/orders"
            class="text-sm font-semibold text-blue-600 hover:text-blue-700 flex items-center gap-1"
        >
            View All
            <ArrowRight class="w-4 h-4" />
        </Link>

    </div>

    <!-- Table -->
    <div class="overflow-x-auto">

        <table class="w-full">

            <thead>
                <tr class="border-b text-sm text-gray-500">

                    <th class="text-left py-3 font-medium">
                        Order
                    </th>

                    <th class="text-left font-medium">
                        Customer
                    </th>

                    <th class="text-right font-medium">
                        Total
                    </th>

                    <th class="text-center font-medium">
                        Status
                    </th>

                    <th class="text-center font-medium">
                        Date
                    </th>

                    <th class="text-right font-medium">
                        Action
                    </th>

                </tr>
            </thead>

            <tbody>

                <tr
                    v-for="order in recentOrders"
                    :key="order.id"
                    class="border-b last:border-0 hover:bg-gray-50 transition"
                >

                    <!-- Order ID -->
                    <td class="py-4 font-semibold text-gray-700">
                        #{{ order.id }}
                    </td>

                    <!-- Customer -->
                    <td>

                        <div class="flex items-center gap-3">

                            

                            <div>

                                <p class="font-medium text-gray-800">
                                    {{ order.customer }}
                                </p>

                            </div>

                        </div>

                    </td>

                    <!-- Total -->
                    <td class="text-right font-semibold text-gray-800">
                        Ksh {{ Number(order.total).toLocaleString() }}
                    </td>

                    <!-- Status -->
                    <td class="text-center">

                        <span
                            class="px-3 py-1 rounded-full text-xs font-semibold"
                            :class="{
                                'bg-yellow-100 text-yellow-700': order.status === 'pending',
                                'bg-green-100 text-green-700': order.status === 'delivered',
                                'bg-red-100 text-red-700': order.status === 'cancelled',
                                'bg-blue-100 text-blue-700': order.status === 'processing'
                            }"
                        >
                            {{ order.status }}
                        </span>

                    </td>

                    <!-- Date -->
                    <td class="text-center text-gray-500">
                        {{ order.date }}
                    </td>

                    <!-- Action -->
                    <td class="text-right">

                       <Link
    :href="`/admin/orders/${order.id}`"
    class="inline-flex items-center justify-center
           w-10 h-10
           rounded-xl
           border border-gray-200
           bg-white
           text-blue-600
           hover:bg-blue-50
           hover:border-blue-300
           hover:text-blue-700
           transition-all duration-200"
>
    <Eye class="w-5 h-5" />
</Link>
                    </td>

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
import StatCard from '@/Components/Dashboard/StatCard.vue'
import { computed } from 'vue'
import CategoryChart from '@/Components/Dashboard/CategoryChart.vue'
import OrderStatusChart from '@/Components/Dashboard/OrderStatusChart.vue'
import { Link } from '@inertiajs/vue3'

import {
    ShoppingCart,
    Users,
    DollarSign,
    Package,
    PackageX,
    TriangleAlert,
    Clock3,
    CircleCheck,
    ArrowRight,
    Eye,
} from 'lucide-vue-next'

const props = defineProps({
    stats: Object,
    revenueChart: Object,
    orderStatusChart: Object,
    categoryChart: Object,
    recentOrders: Array,
})


const formattedRevenue = computed(() =>
    `Ksh ${Number(props.stats.revenue).toLocaleString()}`
)


const applyFilters = () => {
    router.get(route('admin.products.index'), {
        search: search.value,
        category: category.value,
        brand: brand.value,
        stock: stock.value,
    }, {
        preserveState: true,
        replace: true,
    })
}


</script>