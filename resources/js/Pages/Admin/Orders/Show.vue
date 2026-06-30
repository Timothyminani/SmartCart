<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue'
import { Head } from '@inertiajs/vue3'
import { useForm } from '@inertiajs/vue3'
import {
    Download
} from 'lucide-vue-next'

const props = defineProps({
    order: Object
})

const formatCurrency = (amount) => {
    return new Intl.NumberFormat('en-KE', {
        style: 'currency',
        currency: 'KES',
        minimumFractionDigits: 0
    }).format(amount)
}

const getImage = (image) => {
    if (!image) return '/placeholder.png'

    if (image.startsWith('http')) return image

    return `/storage/${image}`
}


const form = useForm({
    status: props.order.status,
    payment_status: props.order.payment_status
})

const updateStatuses = () => {
    form.patch(`/admin/orders/${props.order.id}/status`)
}


const orderStatusClass = (status) => {
    switch (status) {
        case 'pending':
            return 'bg-yellow-100 text-yellow-700'

        case 'processing':
            return 'bg-blue-100 text-blue-700'

        case 'shipped':
            return 'bg-purple-100 text-purple-700'

        case 'delivered':
            return 'bg-green-100 text-green-700'

        case 'cancelled':
            return 'bg-red-100 text-red-700'

        default:
            return 'bg-gray-100 text-gray-700'
    }
}


const paymentStatusClass = (status) => {
    switch (status) {
        case 'paid':
            return 'bg-green-100 text-green-700'

        case 'pending':
            return 'bg-yellow-100 text-yellow-700'

        case 'failed':
            return 'bg-red-100 text-red-700'

        case 'refunded':
            return 'bg-purple-100 text-purple-700'

        default:
            return 'bg-gray-100 text-gray-700'
    }

    
}

</script>

<template>
    <Head :title="`Order #${order.id}`" />

    <AdminLayout>

        <!-- Header -->
        <div class="mb-8">

            <h1 class="text-3xl font-bold text-gray-900">
                Order #{{ order.id }}
            </h1>

            <p class="text-gray-600 mt-2">
                View complete order information.
            </p>

        </div>

        
  <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">

    <!-- LEFT -->
    <div class="lg:col-span-2 space-y-6">

        <!-- Customer Card -->
        <div class="bg-white rounded-2xl shadow-sm border p-6">

            <h2 class="text-xl font-semibold mb-6">
                Customer Information
            </h2>

            <div class="grid md:grid-cols-2 gap-6">

                <div>
                    <p class="text-sm text-gray-500">Customer Name</p>
                    <p class="font-medium">{{ order.user?.name }}</p>
                </div>

                <div>
                    <p class="text-sm text-gray-500">Email</p>
                    <p class="font-medium">{{ order.user?.email }}</p>
                </div>

                <div>
                    <p class="text-sm text-gray-500">Phone</p>
                    <p class="font-medium">{{ order.phone }}</p>
                </div>

                <div>
                    <p class="text-sm text-gray-500">Address</p>
                    <p class="font-medium">{{ order.address }}</p>
                </div>

            </div>

        </div>

        <!-- Order Card -->
        <div class="bg-white rounded-2xl shadow-sm border p-6">

            <h2 class="text-xl font-semibold mb-6">
                Order Summary
            </h2>

            <div class="grid md:grid-cols-2 gap-6">

                <div>
                    <p class="text-sm text-gray-500">
                        Delivery Method
                    </p>

                    <p class="font-medium capitalize">
                        {{ order.delivery_method }}
                    </p>
                </div>

                <div>
                    <p class="text-sm text-gray-500">
                        Payment Method
                    </p>

                    <p class="font-medium uppercase">
                        {{ order.payment_method }}
                    </p>
                </div>

                <div>
                    <p class="text-sm text-gray-500">
                        Order Date
                    </p>

                    <p class="font-medium">
                        {{ new Date(order.created_at).toLocaleString() }}
                    </p>
                </div>

                <div>
                    <p class="text-sm text-gray-500">
                        Total Amount
                    </p>

                    <p class="font-bold text-lg text-blue-600">
                        {{ formatCurrency(order.total_amount) }}
                    </p>
                </div>

            </div>

        </div>

    </div>

    <!-- RIGHT -->
    <div class="bg-white rounded-2xl shadow-sm border p-6 h-fit">

<!-- Download Invoice -->
<a
    :href="route('admin.orders.invoice', order.id)"
    target="_blank"
    class="w-full flex items-center justify-center gap-2
           bg-green-600 text-white py-3 rounded-xl
           hover:bg-green-700 transition mb-6"
>
    <Download class="w-4 h-4" />

    Download Invoice
</a>

        <h2 class="text-xl font-semibold mb-6">
            Admin Actions
        </h2>

        <form
            @submit.prevent="updateStatuses"
            class="space-y-6"
        >

            <div>

                <label class="block text-sm text-gray-500 mb-2">
                    Order Status
                </label>

                <span
                    class="inline-block px-3 py-1 rounded-full text-xs font-medium mb-3"
                    :class="orderStatusClass(form.status)"
                >
                    {{ form.status }}
                </span>

                <select
                    v-model="form.status"
                    class="w-full rounded-xl border-gray-300"
                >
                    <option value="pending">Pending</option>
                    <option value="processing">Processing</option>
                    <option value="shipped">Shipped</option>
                    <option value="delivered">Delivered</option>
                    <option value="cancelled">Cancelled</option>
                </select>

            </div>

            <div>

                <label class="block text-sm text-gray-500 mb-2">
                    Payment Status
                </label>

                <span
                    class="inline-block px-3 py-1 rounded-full text-xs font-medium mb-3"
                    :class="paymentStatusClass(form.payment_status)"
                >
                    {{ form.payment_status }}
                </span>

                <select
                    v-model="form.payment_status"
                    class="w-full rounded-xl border-gray-300"
                >
                    <option value="pending">Pending</option>
                    <option value="paid">Paid</option>
                    <option value="failed">Failed</option>
                    <option value="refunded">Refunded</option>
                </select>

            </div>

            <button
                class="w-full bg-blue-600 text-white py-3 rounded-xl hover:bg-blue-700"
                :disabled="form.processing"
            >
                Save Changes
            </button>

        </form>

    </div>

</div>

        <!-- Ordered Products -->
        <div class="bg-white rounded-2xl shadow-sm border overflow-hidden">

            <div class="p-6 border-b">
                <h2 class="text-xl font-semibold">
                    Ordered Products
                </h2>
            </div>

            <div
                v-for="item in order.items"
                :key="item.id"
                class="flex items-center gap-4 p-6 border-b last:border-b-0"
            >

                <img
                    :src="getImage(item.product.images?.[0]?.image_path)"
                    class="w-20 h-20 rounded-xl object-cover"
                >

                <div class="flex-1">

                    <h3 class="font-semibold">
                        {{ item.product.name }}
                    </h3>

                    <p class="text-sm text-gray-500">
                        Quantity: {{ item.quantity }}
                    </p>

                </div>

                <div class="font-semibold">
                    {{ formatCurrency(item.price) }}
                </div>

            </div>

            <div class="p-6 bg-gray-50">

                <div class="flex justify-between text-lg font-bold">

                    <span>Total Amount</span>

                    <span>
                        {{ formatCurrency(order.total_amount) }}
                    </span>

                </div>

            </div>

        </div>

    </AdminLayout>
</template>