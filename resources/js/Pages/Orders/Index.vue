<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'
import { Link } from '@inertiajs/vue3'

import {
    Package,
    ChevronRight,
    CalendarDays,
} from 'lucide-vue-next'


/*
|--------------------------------------------------------------------------
| PROPS
|--------------------------------------------------------------------------
*/

const props = defineProps({
    orders: {
        type: Array,
        default: () => [],
    },
})


/*
|--------------------------------------------------------------------------
| PRICE
|--------------------------------------------------------------------------
*/

const formatPrice = (value) => {
    return new Intl.NumberFormat('en-KE').format(
        Number(value || 0)
    )
}


/*
|--------------------------------------------------------------------------
| DATE
|--------------------------------------------------------------------------
*/

const formatDate = (date) => {
    if (!date) return ''

    return new Intl.DateTimeFormat(
        'en-KE',
        {
            day: 'numeric',
            month: 'short',
            year: 'numeric',
        }
    ).format(new Date(date))
}


/*
|--------------------------------------------------------------------------
| IMAGE
|--------------------------------------------------------------------------
*/

const getImage = (image) => {
    if (!image) {
        return '/placeholder.png'
    }

    if (image.startsWith('http')) {
        return image
    }

    if (image.startsWith('storage')) {
        return `/${image}`
    }

    return `/storage/${image}`
}


/*
|--------------------------------------------------------------------------
| ITEM COUNT
|--------------------------------------------------------------------------
*/

const getItemCount = (order) => {
    return order.items?.reduce(
        (total, item) => {
            return total + Number(item.quantity || 0)
        },
        0
    ) || 0
}


/*
|--------------------------------------------------------------------------
| STATUS
|--------------------------------------------------------------------------
*/

const normalizeStatus = (status) => {
    if (status === 'shipping') {
        return 'shipped'
    }

    return status || 'pending'
}


const statusLabel = (status) => {
    switch (normalizeStatus(status)) {
        case 'pending':
            return 'Order placed'

        case 'processing':
            return 'Processing'

        case 'shipped':
            return 'Shipped'

        case 'delivered':
            return 'Delivered'

        case 'cancelled':
            return 'Cancelled'

        default:
            return 'Order placed'
    }
}


const statusClasses = (status) => {
    switch (normalizeStatus(status)) {
        case 'processing':
            return `
                bg-amber-50
                text-amber-700
                ring-amber-600/10
            `

        case 'shipped':
            return `
                bg-blue-50
                text-blue-700
                ring-blue-600/10
            `

        case 'delivered':
            return `
                bg-green-50
                text-green-700
                ring-green-600/10
            `

        case 'cancelled':
            return `
                bg-red-50
                text-red-700
                ring-red-600/10
            `

        default:
            return `
                bg-gray-100
                text-gray-700
                ring-gray-600/10
            `
    }
}


/*
|--------------------------------------------------------------------------
| PAYMENT
|--------------------------------------------------------------------------
*/

const paymentMethod = (order) => {
    return order.payment_method === 'mpesa'
        ? 'M-Pesa'
        : 'Cash on delivery'
}


const paymentStatus = (order) => {
    switch (order.payment_status) {
        case 'paid':
            return 'Paid'

        case 'pending':
            return 'Pending'

        case 'unpaid':
            return 'Pay on delivery'

        case 'failed':
            return 'Failed'

        default:
            return order.payment_status || 'Pending'
    }
}
</script>


<template>

    <AppLayout>

        <main
            class="
                max-w-6xl
                mx-auto

                px-4
                sm:px-6

                py-8
                sm:py-10
                lg:py-12
            "
        >

            <!-- ===================================================== -->
            <!-- HEADER -->
            <!-- ===================================================== -->

            <header
                class="
                    flex
                    flex-col

                    sm:flex-row
                    sm:items-end
                    sm:justify-between

                    gap-4

                    mb-8
                "
            >

                <div>

                    <h1
                        class="
                            text-2xl
                            sm:text-3xl

                            font-semibold
                            tracking-tight
                            text-gray-950
                        "
                    >
                        My orders
                    </h1>


                    <p
                        class="
                            mt-2

                            text-sm
                            text-gray-500
                        "
                    >
                        Review your purchases and track their progress.
                    </p>

                </div>


                <p
                    v-if="orders.length"
                    class="
                        text-sm
                        text-gray-400
                    "
                >
                    {{ orders.length }}
                    {{
                        orders.length === 1
                            ? 'order'
                            : 'orders'
                    }}
                </p>

            </header>


            <!-- ===================================================== -->
            <!-- EMPTY -->
            <!-- ===================================================== -->

            <section
                v-if="orders.length === 0"
                class="
                    max-w-xl
                    mx-auto

                    py-16
                    sm:py-20

                    text-center
                "
            >

                <div
                    class="
                        w-12
                        h-12

                        mx-auto

                        flex
                        items-center
                        justify-center

                        rounded-full

                        bg-gray-100
                        text-gray-400
                    "
                >
                    <Package class="w-5 h-5" />
                </div>


                <h2
                    class="
                        mt-5

                        text-lg
                        font-semibold
                        text-gray-900
                    "
                >
                    No orders yet
                </h2>


                <p
                    class="
                        mt-2

                        text-sm
                        leading-6
                        text-gray-500
                    "
                >
                    Orders you place will appear here
                    so you can track them anytime.
                </p>


                <Link
                    href="/productListing"
                    class="
                        mt-6

                        inline-flex
                        items-center
                        justify-center

                        rounded-xl

                        bg-gray-950

                        px-5
                        py-2.5

                        text-sm
                        font-semibold
                        text-white

                        transition

                        hover:bg-black
                    "
                >
                    Browse products
                </Link>

            </section>


            <!-- ===================================================== -->
            <!-- ORDERS -->
            <!-- ===================================================== -->

            <template v-else>

                <!-- ================================================= -->
                <!-- DESKTOP TABLE -->
                <!-- ================================================= -->

                <section
                    class="
                        hidden
                        md:block

                        overflow-hidden

                        rounded-2xl

                        border
                        border-gray-200

                        bg-white
                    "
                >

                    <div class="overflow-x-auto">

                        <table
                            class="
                                w-full
                                min-w-[900px]
                            "
                        >

                            <!-- HEADER -->

                            <thead
                                class="
                                    border-b
                                    border-gray-100

                                    bg-gray-50/70
                                "
                            >

                                <tr>

                                    <th
                                        class="
                                            px-5
                                            py-3.5

                                            text-left

                                            text-xs
                                            font-medium
                                            text-gray-400
                                        "
                                    >
                                        Order
                                    </th>


                                    <th
                                        class="
                                            px-5
                                            py-3.5

                                            text-left

                                            text-xs
                                            font-medium
                                            text-gray-400
                                        "
                                    >
                                        Items
                                    </th>


                                    <th
                                        class="
                                            px-5
                                            py-3.5

                                            text-left

                                            text-xs
                                            font-medium
                                            text-gray-400
                                        "
                                    >
                                        Payment
                                    </th>


                                    <th
                                        class="
                                            px-5
                                            py-3.5

                                            text-left

                                            text-xs
                                            font-medium
                                            text-gray-400
                                        "
                                    >
                                        Total
                                    </th>


                                    <th
                                        class="
                                            px-5
                                            py-3.5

                                            text-left

                                            text-xs
                                            font-medium
                                            text-gray-400
                                        "
                                    >
                                        Status
                                    </th>


                                    <th
                                        class="
                                            px-5
                                            py-3.5
                                        "
                                    ></th>

                                </tr>

                            </thead>


                            <!-- BODY -->

                            <tbody
                                class="
                                    divide-y
                                    divide-gray-100
                                "
                            >

                                <tr
                                    v-for="order in orders"
                                    :key="order.id"
                                    class="
                                        group

                                        transition

                                        hover:bg-gray-50/70
                                    "
                                >

                                    <!-- ORDER -->

                                    <td
                                        class="
                                            px-5
                                            py-5
                                        "
                                    >

                                        <p
                                            class="
                                                text-sm
                                                font-semibold
                                                text-gray-900
                                            "
                                        >
                                            #{{ order.id }}
                                        </p>


                                        <div
                                            class="
                                                mt-1.5

                                                flex
                                                items-center
                                                gap-1.5

                                                text-xs
                                                text-gray-400
                                            "
                                        >

                                            <CalendarDays
                                                class="
                                                    w-3.5
                                                    h-3.5
                                                "
                                            />

                                            {{
                                                formatDate(
                                                    order.created_at
                                                )
                                            }}

                                        </div>

                                    </td>


                                    <!-- ITEMS -->

                                    <td
                                        class="
                                            px-5
                                            py-5
                                        "
                                    >

                                        <div
                                            class="
                                                flex
                                                items-center
                                                gap-3
                                            "
                                        >

                                            <!-- IMAGES -->

                                            <div
                                                class="
                                                    flex
                                                    items-center

                                                    -space-x-2
                                                "
                                            >

                                                <img
                                                    v-for="item in order.items.slice(0, 3)"
                                                    :key="item.id"
                                                    :src="
                                                        getImage(
                                                            item.product
                                                                ?.images?.[0]
                                                                ?.image_path
                                                        )
                                                    "
                                                    :alt="
                                                        item.product?.name ||
                                                        'Product'
                                                    "
                                                    class="
                                                        w-9
                                                        h-9

                                                        rounded-full

                                                        border-2
                                                        border-white

                                                        bg-gray-100

                                                        object-cover
                                                    "
                                                />


                                                <div
                                                    v-if="
                                                        order.items.length > 3
                                                    "
                                                    class="
                                                        w-9
                                                        h-9

                                                        rounded-full

                                                        border-2
                                                        border-white

                                                        bg-gray-100

                                                        flex
                                                        items-center
                                                        justify-center

                                                        text-[10px]
                                                        font-semibold
                                                        text-gray-500
                                                    "
                                                >
                                                    +{{
                                                        order.items.length - 3
                                                    }}
                                                </div>

                                            </div>


                                            <span
                                                class="
                                                    text-xs
                                                    text-gray-500
                                                "
                                            >
                                                {{ getItemCount(order) }}
                                                {{
                                                    getItemCount(order) === 1
                                                        ? 'item'
                                                        : 'items'
                                                }}
                                            </span>

                                        </div>

                                    </td>


                                    <!-- PAYMENT -->

                                    <td
                                        class="
                                            px-5
                                            py-5
                                        "
                                    >

                                        <p
                                            class="
                                                text-sm
                                                font-medium
                                                text-gray-800
                                            "
                                        >
                                            {{ paymentMethod(order) }}
                                        </p>


                                        <p
                                            class="
                                                mt-1

                                                text-xs
                                                text-gray-400
                                            "
                                        >
                                            {{ paymentStatus(order) }}
                                        </p>

                                    </td>


                                    <!-- TOTAL -->

                                    <td
                                        class="
                                            px-5
                                            py-5
                                        "
                                    >

                                        <p
                                            class="
                                                text-sm
                                                font-semibold
                                                text-gray-900
                                            "
                                        >
                                            KES
                                            {{
                                                formatPrice(
                                                    order.total_amount
                                                )
                                            }}
                                        </p>

                                    </td>


                                    <!-- STATUS -->

                                    <td
                                        class="
                                            px-5
                                            py-5
                                        "
                                    >

                                        <span
                                            class="
                                                inline-flex
                                                items-center

                                                rounded-full

                                                px-2.5
                                                py-1

                                                text-xs
                                                font-medium

                                                ring-1
                                                ring-inset
                                            "
                                            :class="
                                                statusClasses(
                                                    order.status
                                                )
                                            "
                                        >
                                            {{
                                                statusLabel(
                                                    order.status
                                                )
                                            }}
                                        </span>

                                    </td>


                                    <!-- ACTION -->

                                    <td
                                        class="
                                            px-5
                                            py-5

                                            text-right
                                        "
                                    >

                                        <Link
                                            :href="
                                                `/orders/${order.id}`
                                            "
                                            class="
                                                inline-flex
                                                items-center

                                                gap-1

                                                text-sm
                                                font-medium
                                                text-gray-500

                                                transition

                                                group-hover:text-blue-600
                                            "
                                        >
                                            Details

                                            <ChevronRight
                                                class="
                                                    w-4
                                                    h-4
                                                "
                                            />

                                        </Link>

                                    </td>

                                </tr>

                            </tbody>

                        </table>

                    </div>

                </section>


                <!-- ================================================= -->
                <!-- MOBILE -->
                <!-- ================================================= -->

                <div
                    class="
                        md:hidden

                        space-y-3
                    "
                >

                    <Link
                        v-for="order in orders"
                        :key="order.id"

                        :href="
                            `/orders/${order.id}`
                        "

                        class="
                            block

                            rounded-2xl

                            border
                            border-gray-200

                            bg-white

                            p-4

                            transition

                            active:bg-gray-50
                        "
                    >

                        <!-- TOP -->

                        <div
                            class="
                                flex
                                items-start
                                justify-between

                                gap-3
                            "
                        >

                            <div>

                                <p
                                    class="
                                        text-sm
                                        font-semibold
                                        text-gray-900
                                    "
                                >
                                    Order #{{ order.id }}
                                </p>


                                <p
                                    class="
                                        mt-1
                                        text-xs
                                        text-gray-400
                                    "
                                >
                                    {{
                                        formatDate(
                                            order.created_at
                                        )
                                    }}
                                </p>

                            </div>


                            <span
                                class="
                                    inline-flex
                                    items-center

                                    rounded-full

                                    px-2.5
                                    py-1

                                    text-[11px]
                                    font-medium

                                    ring-1
                                    ring-inset
                                "
                                :class="
                                    statusClasses(
                                        order.status
                                    )
                                "
                            >
                                {{
                                    statusLabel(
                                        order.status
                                    )
                                }}
                            </span>

                        </div>


                        <!-- PRODUCTS -->

                        <div
                            class="
                                mt-4

                                flex
                                items-center
                                justify-between

                                gap-4
                            "
                        >

                            <div
                                class="
                                    flex
                                    items-center

                                    -space-x-2
                                "
                            >

                                <img
                                    v-for="item in order.items.slice(0, 3)"
                                    :key="item.id"
                                    :src="
                                        getImage(
                                            item.product
                                                ?.images?.[0]
                                                ?.image_path
                                        )
                                    "
                                    :alt="
                                        item.product?.name ||
                                        'Product'
                                    "
                                    class="
                                        w-10
                                        h-10

                                        rounded-full

                                        border-2
                                        border-white

                                        object-cover

                                        bg-gray-100
                                    "
                                />


                                <div
                                    v-if="
                                        order.items.length > 3
                                    "
                                    class="
                                        w-10
                                        h-10

                                        rounded-full

                                        border-2
                                        border-white

                                        bg-gray-100

                                        flex
                                        items-center
                                        justify-center

                                        text-[10px]
                                        font-semibold
                                        text-gray-500
                                    "
                                >
                                    +{{ order.items.length - 3 }}
                                </div>

                            </div>


                            <span
                                class="
                                    text-xs
                                    text-gray-500
                                "
                            >
                                {{ getItemCount(order) }}
                                {{
                                    getItemCount(order) === 1
                                        ? 'item'
                                        : 'items'
                                }}
                            </span>

                        </div>


                        <!-- BOTTOM -->

                        <div
                            class="
                                mt-4

                                pt-4

                                border-t
                                border-gray-100

                                flex
                                items-end
                                justify-between

                                gap-4
                            "
                        >

                            <div>

                                <p
                                    class="
                                        text-xs
                                        text-gray-400
                                    "
                                >
                                    {{ paymentMethod(order) }}
                                    ·
                                    {{ paymentStatus(order) }}
                                </p>


                                <p
                                    class="
                                        mt-1

                                        text-base
                                        font-semibold
                                        text-gray-950
                                    "
                                >
                                    KES
                                    {{
                                        formatPrice(
                                            order.total_amount
                                        )
                                    }}
                                </p>

                            </div>


                            <ChevronRight
                                class="
                                    w-5
                                    h-5
                                    text-gray-400
                                "
                            />

                        </div>

                    </Link>

                </div>

            </template>

        </main>

    </AppLayout>

</template>