<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { reactive, watch } from 'vue'
import { router } from '@inertiajs/vue3'
import { Search, ChevronLeft, ChevronRight } from 'lucide-vue-next'

const props = defineProps({
    orders: Object,
    filters: Object
})

const filters = reactive({
    search: props.filters.search || '',
    status: props.filters.status || '',
    payment_status: props.filters.payment_status || '',
    date: props.filters.date || ''
})


watch(filters, () => {

    router.get(
        route('admin.orders.index'),
        filters,
        {
            preserveState: true,
            replace: true
        }
    )

})


const formatCurrency = (amount) => {
    return new Intl.NumberFormat('en-KE', {
        style: 'currency',
        currency: 'KES',
        minimumFractionDigits: 0,
    }).format(amount);
};
</script>

<template>
    <Head title="Orders" />

    <AdminLayout>

        <!-- Page Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">
                Orders Management
            </h1>

            <p class="mt-2 text-gray-600">
                Manage customer orders and deliveries.
            </p>
        </div>


<!-- Filters -->
<div class="bg-white rounded-2xl shadow-sm border p-4 mb-6">

    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">

        <!-- Search -->
<div class="relative">

    <!-- Search Icon -->
    <Search
        class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400"
    />

    <input
        v-model="filters.search"
        type="text"
        placeholder="Search order, customer..."
        class="w-full rounded-xl border-gray-300 pl-10
               focus:border-blue-500 focus:ring-blue-500"
    />

</div>

        <!-- Order Status -->
                <div>
                    <select
                        v-model="filters.status"
                        class="w-full rounded-xl border-gray-300 focus:border-blue-500 focus:ring-blue-500"
                    >
                        <option value="">All Statuses</option>
                        <option value="pending">Pending</option>
                        <option value="processing">Processing</option>
                        <option value="shipped">Shipped</option>
                        <option value="delivered">Delivered</option>
                        <option value="cancelled">Cancelled</option>
                    </select>
                </div>

                <!-- Payment Status -->
                <div>
                    <select
                        v-model="filters.payment_status"
                        class="w-full rounded-xl border-gray-300 focus:border-blue-500 focus:ring-blue-500"
                    >
                        <option value="">All Payments</option>
                        <option value="pending">Pending</option>
                        <option value="paid">Paid</option>
                        <option value="unpaid">Unpaid</option>
                    </select>
                </div>

                <!-- Date -->
                <div>
                    <select
                        v-model="filters.date"
                        class="w-full rounded-xl border-gray-300 focus:border-blue-500 focus:ring-blue-500"
                    >
                        <option value="">All Dates</option>
                        <option value="today">Today</option>
                        <option value="week">Last 7 Days</option>
                        <option value="month">This Month</option>
                        <option value="year">This Year</option>
                    </select>
                </div>

            </div>

        </div>




        <!-- Table Card -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">

            <div class="overflow-x-auto">

                <table class="w-full">

                    <thead class="bg-gray-50 border-b border-gray-200">

                        <tr>

                            <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                                Order #
                            </th>

                            <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                                Customer
                            </th>

                            <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                                Phone
                            </th>

                            <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                                Total
                            </th>

                            <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                                Payment
                            </th>

                            <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                                Status
                            </th>

                            <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                                Date
                            </th>

                            <th class="px-6 py-4 text-center text-xs font-semibold uppercase tracking-wider text-gray-500">
                                Actions
                            </th>

                        </tr>

                    </thead>

                    <tbody class="divide-y divide-gray-100">

                        <tr
                            v-for="order in orders.data"
                            :key="order.id"
                            class="hover:bg-gray-50 transition"
                        >

                            <!-- Order ID -->
                            <td class="px-6 py-4 font-semibold text-gray-900">
                                #{{ order.id }}
                            </td>

                            <!-- Customer -->
                            <td class="px-6 py-4">
                                {{ order.user?.name }}
                            </td>

                            <!-- Phone -->
                            <td class="px-6 py-4 text-gray-600">
                                {{ order.phone }}
                            </td>

                            <!-- Total -->
                            <td class="px-6 py-4 font-medium">
                                {{ formatCurrency(order.total_amount) }}
                            </td>

                            <!-- Payment Status -->
                            <td class="px-6 py-4">

                                <span
                                    class="px-3 py-1 rounded-full text-xs font-medium"
                                    :class="{
                                        'bg-green-100 text-green-700':
                                            order.payment_status === 'paid',

                                        'bg-yellow-100 text-yellow-700':
                                            order.payment_status === 'pending',

                                        'bg-red-100 text-red-700':
                                            order.payment_status === 'unpaid'
                                    }"
                                >
                                    {{ order.payment_status }}
                                </span>

                            </td>

                            <!-- Order Status -->
                            <td class="px-6 py-4">

                                <span
                                    class="px-3 py-1 rounded-full text-xs font-medium"
                                    :class="{
                                        'bg-yellow-100 text-yellow-700':
                                            order.status === 'pending',

                                        'bg-blue-100 text-blue-700':
                                            order.status === 'processing',

                                        'bg-purple-100 text-purple-700':
                                            order.status === 'shipped',

                                        'bg-green-100 text-green-700':
                                            order.status === 'delivered',

                                        'bg-red-100 text-red-700':
                                            order.status === 'cancelled'
                                    }"
                                >
                                    {{ order.status }}
                                </span>

                            </td>

                            <!-- Date -->
                            <td class="px-6 py-4 text-gray-600">
                                {{ new Date(order.created_at).toLocaleDateString() }}
                            </td>

                            <!-- Actions -->
                            <td class="px-6 py-4 text-center">

                                <Link
                                    :href="route('admin.orders.show', order.id)"
                                    class="inline-flex items-center px-4 py-2 rounded-lg bg-indigo-600 text-white text-sm hover:bg-indigo-700"
                                >
                                    View
                                </Link>

                            </td>

                        </tr>

                    </tbody>

                </table>

            </div>

        </div>


<!-- Pagination -->
<div
    v-if="orders.links.length > 3"
    class="mt-6 flex items-center justify-center gap-6"
>
    <template
        v-for="(link, index) in orders.links"
        :key="index"
    >

        <!-- Previous / Next Disabled -->
        <span
            v-if="!link.url"
            class="w-10 h-10 flex items-center justify-center rounded-xl bg-gray-100 text-gray-400 cursor-not-allowed"
        >
            <ChevronLeft
                v-if="index === 0"
                class="w-6 h-6"
            />

            <ChevronRight
                v-else-if="index === orders.links.length - 1"
                class="w-6 h-6"
            />
        </span>

        <!-- Active Page -->
        <span
            v-else-if="link.active"
            class="w-10 h-10 flex items-center justify-center rounded-xl border-2 border-blue-600 text-black font-semibold"
        >
            {{ link.label }}
        </span>

        <!-- Previous / Next Clickable -->
        <Link
            v-else-if="index === 0 || index === orders.links.length - 1"
            :href="link.url"
            preserve-scroll
            class="w-10 h-10 flex items-center justify-center rounded-xl border hover:bg-blue-50 transition"
        >
            <ChevronLeft
                v-if="index === 0"
                class="w-6 h-6"
            />

            <ChevronRight
                v-else
                class="w-6 h-6"
            />
        </Link>

        <!-- Normal Page Numbers -->
        <Link
            v-else
            :href="link.url"
            preserve-scroll
            class="w-10 h-10 flex items-center justify-center rounded-xl border hover:bg-gray-50 transition"
        >
            {{ link.label }}
        </Link>

    </template>
</div>

    </AdminLayout>
</template>