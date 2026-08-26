<template>
    <AdminLayout
        title="Customer Details"
        subtitle="View customer profile and purchase history"
    >

        <!-- Profile -->
        <div class="bg-white rounded-2xl shadow p-6 mb-6">

            <div class="flex items-center gap-6">

                <div
                    class="w-24 h-24 rounded-full bg-blue-100 flex items-center justify-center"
                >
                    <UserCircle class="w-20 h-20 text-blue-600" />
                </div>

                <div class="flex-1">

                    <h2 class="text-2xl font-bold text-gray-800">
                        {{ customer.name }}
                    </h2>

                    <p class="text-gray-500">
                        {{ customer.email }}
                    </p>

                    <div class="flex gap-6 mt-4 text-sm text-gray-600">

                        <div>
                            <span class="font-semibold">Phone:</span>
                            {{ customer.phone || '-' }}
                        </div>

                        <div>
                            <span class="font-semibold">Role:</span>
                            {{ customer.role }}
                        </div>

                    </div>

                    <div class="mt-2 text-sm text-gray-600">
                        <span class="font-semibold">Address:</span>
                        {{ customer.address || '-' }}
                    </div>

                </div>

            </div>

        </div>

        <!-- Statistics -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">

            <div class="bg-white rounded-xl shadow p-5">

                <p class="text-gray-500 text-sm">
                    Total Orders
                </p>

                <h2 class="text-3xl font-bold mt-2">
                    {{ customer.orders_count }}
                </h2>

            </div>

            <div class="bg-white rounded-xl shadow p-5">

                <p class="text-gray-500 text-sm">
                    Total Spent
                </p>

                <h2 class="text-3xl font-bold mt-2 text-green-600">
                    Ksh {{ Number(customer.total_spent).toLocaleString() }}
                </h2>

            </div>

            <div class="bg-white rounded-xl shadow p-5">

                <p class="text-gray-500 text-sm">
                    Joined
                </p>

                <h2 class="text-xl font-bold mt-2">
                    {{ formatDate(customer.created_at) }}
                </h2>

            </div>

        </div>

        <!-- Orders -->
        <div class="bg-white rounded-2xl shadow p-6">

            <h2 class="text-xl font-semibold mb-6">
                Recent Orders
            </h2>

            <table class="w-full">

                <thead>

                    <tr class="border-b text-gray-500">

                        <th class="text-left py-3">Order</th>
                        <th class="text-left">Date</th>
                        <th class="text-left">Status</th>
                        <th class="text-right">Total</th>

                    </tr>

                </thead>

                <tbody>

                    <tr
                        v-for="order in customer.orders"
                        :key="order.id"
                        class="border-b last:border-0"
                    >

                        <td class="py-4 font-semibold">
                            #{{ order.id }}
                        </td>

                        <td>
                            {{ formatDate(order.created_at) }}
                        </td>

                        <td>

                            <span
                                class="px-3 py-1 rounded-full text-xs"
                                :class="statusClass(order.status)"
                            >
                                {{ order.status }}
                            </span>

                        </td>

                        <td class="text-right font-semibold">
                            Ksh {{ Number(order.total).toLocaleString() }}
                        </td>

                    </tr>

                </tbody>

            </table>

            <div
                v-if="customer.orders.length === 0"
                class="text-center py-10 text-gray-400"
            >
                No orders found.
            </div>

        </div>

    </AdminLayout>
</template>

<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue'
import { UserCircle } from 'lucide-vue-next'

const props = defineProps({
    customer: Object
})

const formatDate = (date) => {
    return new Date(date).toLocaleDateString('en-GB', {
        day: 'numeric',
        month: 'short',
        year: 'numeric'
    })
}

const statusClass = (status) => {

    switch (status) {

        case 'pending':
            return 'bg-yellow-100 text-yellow-700'

        case 'processing':
            return 'bg-blue-100 text-blue-700'

        case 'delivered':
            return 'bg-green-100 text-green-700'

        case 'cancelled':
            return 'bg-red-100 text-red-700'

        default:
            return 'bg-gray-100 text-gray-700'
    }

}
</script>