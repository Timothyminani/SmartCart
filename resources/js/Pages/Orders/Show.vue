<script setup>
import { computed } from 'vue'

import AppLayout from '@/Layouts/AppLayout.vue'

import {
    Check,
    Package,
    Truck,
    House,
    Download,
    MapPin,
    Phone,
    CreditCard,
    CalendarDays,
    ChevronLeft,
    CircleCheck,
} from 'lucide-vue-next'


/*
|--------------------------------------------------------------------------
| PROPS
|--------------------------------------------------------------------------
*/

const props = defineProps({
    order: {
        type: Object,
        required: true,
    },
})


/*
|--------------------------------------------------------------------------
| HELPERS
|--------------------------------------------------------------------------
*/

const formatPrice = (value) => {
    return new Intl.NumberFormat('en-KE').format(
        Number(value || 0)
    )
}


const formatDate = (date) => {
    if (!date) return ''

    return new Intl.DateTimeFormat(
        'en-KE',
        {
            day: 'numeric',
            month: 'long',
            year: 'numeric',
        }
    ).format(new Date(date))
}


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
| ORDER ITEMS
|--------------------------------------------------------------------------
*/

const itemCount = computed(() => {
    return props.order.items?.reduce(
        (total, item) => {
            return total + Number(item.quantity || 0)
        },
        0
    ) || 0
})


/*
|--------------------------------------------------------------------------
| ORDER STATUS
|--------------------------------------------------------------------------
|
| IMPORTANT:
|
| We normalize "shipping" -> "shipped" so the UI remains compatible
| if some older orders currently contain "shipping".
|
*/

const normalizedStatus = computed(() => {

    const status =
        props.order?.status?.toLowerCase() || 'pending'


    if (status === 'shipping') {
        return 'shipped'
    }


    return status
})


const statusLabel = computed(() => {

    switch (normalizedStatus.value) {

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
})


const statusClasses = computed(() => {

    switch (normalizedStatus.value) {

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
})


/*
|--------------------------------------------------------------------------
| TRACKING
|--------------------------------------------------------------------------
*/

const steps = [
    {
        key: 'pending',
        label: 'Order placed',
        description: 'We received your order.',
        icon: CircleCheck,
    },

    {
        key: 'processing',
        label: 'Processing',
        description: 'Your items are being prepared.',
        icon: Package,
    },

    {
        key: 'shipped',
        label: 'Shipped',
        description: 'Your order is on the way.',
        icon: Truck,
    },

    {
        key: 'delivered',
        label: 'Delivered',
        description: 'Your order has arrived.',
        icon: House,
    },
]


const currentIndex = computed(() => {

    return steps.findIndex(
        step =>
            step.key === normalizedStatus.value
    )
})


const stepState = (index) => {

    if (currentIndex.value === -1) {
        return 'future'
    }

    if (index < currentIndex.value) {
        return 'completed'
    }

    if (index === currentIndex.value) {
        return 'current'
    }

    return 'future'
}


/*
|--------------------------------------------------------------------------
| DELIVERY
|--------------------------------------------------------------------------
*/

const deliveryMethod = computed(() => {

    switch (props.order?.delivery_method) {

        case 'express':
            return 'Express delivery'

        case 'standard':
            return 'Standard delivery'

        case 'pickup':
            return 'Store pickup'

        default:
            return props.order?.delivery_method || 'Delivery'
    }
})


const estimatedDelivery = computed(() => {

    if (normalizedStatus.value === 'delivered') {
        return 'Delivered'
    }


    if (normalizedStatus.value === 'cancelled') {
        return 'Delivery cancelled'
    }


    switch (props.order?.delivery_method) {

        case 'express':
            return 'Expected today'

        case 'standard':
            return 'Expected within 2–3 business days'

        case 'pickup':
            return 'Ready for pickup today'

        default:
            return 'Delivery estimate pending'
    }
})


/*
|--------------------------------------------------------------------------
| PAYMENT
|--------------------------------------------------------------------------
*/

const paymentMethod = computed(() => {

    switch (props.order?.payment_method) {

        case 'mpesa':
            return 'M-Pesa'

        case 'cod':
            return 'Cash on delivery'

        default:
            return props.order?.payment_method || 'Payment'
    }
})


const paymentStatus = computed(() => {

    switch (props.order?.payment_status) {

        case 'paid':
            return 'Paid'

        case 'pending':
            return 'Payment pending'

        case 'unpaid':
            return 'Pay on delivery'

        case 'failed':
            return 'Payment failed'

        default:
            return props.order?.payment_status || 'Pending'
    }
})


const paymentStatusClasses = computed(() => {

    switch (props.order?.payment_status) {

        case 'paid':
            return 'text-green-700'

        case 'failed':
            return 'text-red-600'

        case 'pending':
            return 'text-amber-600'

        default:
            return 'text-gray-500'
    }
})
</script>


<template>

    <AppLayout>

        <main
            class="
                max-w-6xl
                mx-auto

                px-4
                sm:px-6

                py-7
                sm:py-9
                lg:py-12
            "
        >

            <!-- ===================================================== -->
            <!-- BACK -->
            <!-- ===================================================== -->

            <a
                href="/orders"
                class="
                    inline-flex
                    items-center
                    gap-1.5

                    text-sm
                    font-medium
                    text-gray-500

                    transition

                    hover:text-gray-900
                "
            >

                <ChevronLeft
                    class="
                        w-4
                        h-4
                    "
                />

                My orders

            </a>


            <!-- ===================================================== -->
            <!-- HEADER -->
            <!-- ===================================================== -->

            <header
                class="
                    mt-5

                    flex
                    flex-col

                    sm:flex-row
                    sm:items-end
                    sm:justify-between

                    gap-5

                    pb-7

                    border-b
                    border-gray-200
                "
            >

                <div>

                    <div
                        class="
                            flex
                            flex-wrap
                            items-center

                            gap-3
                        "
                    >

                        <h1
                            class="
                                text-2xl
                                sm:text-3xl

                                font-semibold
                                tracking-tight
                                text-gray-950
                            "
                        >
                            Order #{{ order.id }}
                        </h1>


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
                            :class="statusClasses"
                        >
                            {{ statusLabel }}
                        </span>

                    </div>


                    <div
                        class="
                            mt-3

                            flex
                            flex-wrap
                            items-center

                            gap-x-4
                            gap-y-2

                            text-sm
                            text-gray-500
                        "
                    >

                        <span
                            class="
                                inline-flex
                                items-center
                                gap-1.5
                            "
                        >

                            <CalendarDays
                                class="
                                    w-4
                                    h-4
                                "
                            />

                            Placed {{ formatDate(order.created_at) }}

                        </span>


                        <span>
                            {{ itemCount }}
                            {{
                                itemCount === 1
                                    ? 'item'
                                    : 'items'
                            }}
                        </span>

                    </div>

                </div>


                <!-- INVOICE -->

                <a
                    :href="`/orders/${order.id}/invoice`"
                    target="_blank"
                    class="
                        inline-flex
                        items-center
                        justify-center

                        gap-2

                        shrink-0

                        rounded-xl

                        border
                        border-gray-300

                        bg-white

                        px-4
                        py-2.5

                        text-sm
                        font-semibold
                        text-gray-700

                        transition

                        hover:bg-gray-50
                        hover:border-gray-400
                    "
                >

                    <Download
                        class="
                            w-4
                            h-4
                        "
                    />

                    Download invoice

                </a>

            </header>


            <!-- ===================================================== -->
            <!-- CANCELLED NOTICE -->
            <!-- ===================================================== -->

            <div
                v-if="normalizedStatus === 'cancelled'"
                class="
                    mt-6

                    rounded-xl

                    border
                    border-red-200

                    bg-red-50

                    px-4
                    py-3
                "
            >

                <p
                    class="
                        text-sm
                        font-medium
                        text-red-800
                    "
                >
                    This order has been cancelled.
                </p>


                <p
                    class="
                        mt-1

                        text-xs
                        text-red-600
                    "
                >
                    It will no longer continue through
                    the delivery process.
                </p>

            </div>


            <!-- ===================================================== -->
            <!-- TRACKING -->
            <!-- ===================================================== -->

            <section
                v-if="normalizedStatus !== 'cancelled'"
                class="
                    mt-8

                    rounded-2xl

                    border
                    border-gray-200

                    bg-white

                    px-5
                    sm:px-7

                    py-6
                    sm:py-7
                "
            >

                <div>

                    <h2
                        class="
                            text-base
                            font-semibold
                            text-gray-950
                        "
                    >
                        Order tracking
                    </h2>


                    <p
                        class="
                            mt-1

                            text-sm
                            text-gray-500
                        "
                    >
                        Follow your order from confirmation
                        to delivery.
                    </p>

                </div>


                <!-- ============================================= -->
                <!-- DESKTOP TRACKING -->
                <!-- ============================================= -->

                <div
                    class="
                        hidden
                        sm:grid

                        grid-cols-4

                        mt-9
                    "
                >

                    <div
                        v-for="(step, index) in steps"
                        :key="step.key"
                        class="
                            relative
                            text-center
                        "
                    >

                        <!-- CONNECTING LINE -->

                        <div
                            v-if="index > 0"
                            class="
                                absolute

                                top-5
                                right-1/2

                                w-full
                                h-0.5
                            "
                            :class="
                                stepState(index) === 'completed' ||
                                stepState(index) === 'current'
                                    ? 'bg-blue-600'
                                    : 'bg-gray-200'
                            "
                        ></div>


                        <!-- STEP ICON -->

                        <div
                            class="
                                relative
                                z-10

                                mx-auto

                                w-10
                                h-10

                                rounded-full

                                flex
                                items-center
                                justify-center

                                border-2

                                transition
                            "
                            :class="{
                                'border-blue-600 bg-blue-600 text-white':
                                    stepState(index) === 'completed',

                                'border-blue-600 bg-white text-blue-600 ring-4 ring-blue-50':
                                    stepState(index) === 'current',

                                'border-gray-200 bg-white text-gray-400':
                                    stepState(index) === 'future',
                            }"
                        >

                            <Check
                                v-if="
                                    stepState(index) ===
                                    'completed'
                                "
                                class="
                                    w-5
                                    h-5
                                "
                            />


                            <component
                                v-else
                                :is="step.icon"
                                class="
                                    w-4
                                    h-4
                                "
                            />

                        </div>


                        <!-- LABEL -->

                        <p
                            class="
                                mt-3

                                text-sm
                                font-medium
                            "
                            :class="
                                stepState(index) === 'future'
                                    ? 'text-gray-400'
                                    : 'text-gray-900'
                            "
                        >
                            {{ step.label }}
                        </p>


                        <p
                            class="
                                mt-1

                                px-2

                                text-xs
                                leading-5
                                text-gray-400
                            "
                        >
                            {{ step.description }}
                        </p>

                    </div>

                </div>


                <!-- ============================================= -->
                <!-- MOBILE TRACKING -->
                <!-- ============================================= -->

                <div
                    class="
                        sm:hidden

                        mt-7
                    "
                >

                    <div
                        v-for="(step, index) in steps"
                        :key="step.key"
                        class="
                            flex
                            gap-4
                        "
                    >

                        <!-- STEP -->

                        <div
                            class="
                                flex
                                flex-col
                                items-center
                            "
                        >

                            <div
                                class="
                                    w-9
                                    h-9

                                    shrink-0

                                    rounded-full

                                    flex
                                    items-center
                                    justify-center

                                    border-2
                                "
                                :class="{
                                    'border-blue-600 bg-blue-600 text-white':
                                        stepState(index) === 'completed',

                                    'border-blue-600 bg-white text-blue-600 ring-4 ring-blue-50':
                                        stepState(index) === 'current',

                                    'border-gray-200 bg-white text-gray-400':
                                        stepState(index) === 'future',
                                }"
                            >

                                <Check
                                    v-if="
                                        stepState(index) ===
                                        'completed'
                                    "
                                    class="
                                        w-4
                                        h-4
                                    "
                                />


                                <component
                                    v-else
                                    :is="step.icon"
                                    class="
                                        w-4
                                        h-4
                                    "
                                />

                            </div>


                            <!-- LINE -->

                            <div
                                v-if="
                                    index <
                                    steps.length - 1
                                "
                                class="
                                    w-0.5
                                    h-12
                                "
                                :class="
                                    stepState(index + 1) ===
                                    'completed' ||
                                    stepState(index + 1) ===
                                    'current'
                                        ? 'bg-blue-600'
                                        : 'bg-gray-200'
                                "
                            ></div>

                        </div>


                        <!-- TEXT -->

                        <div
                            class="
                                pt-1
                            "
                        >

                            <p
                                class="
                                    text-sm
                                    font-medium
                                "
                                :class="
                                    stepState(index) ===
                                    'future'
                                        ? 'text-gray-400'
                                        : 'text-gray-900'
                                "
                            >
                                {{ step.label }}
                            </p>


                            <p
                                class="
                                    mt-1

                                    text-xs
                                    text-gray-400
                                "
                            >
                                {{ step.description }}
                            </p>

                        </div>

                    </div>

                </div>

            </section>


            <!-- ===================================================== -->
            <!-- MAIN CONTENT -->
            <!-- ===================================================== -->

            <div
                class="
                    mt-6

                    grid

                    lg:grid-cols-[minmax(0,1fr)_340px]

                    gap-6
                    lg:gap-8

                    items-start
                "
            >

                <!-- ================================================= -->
                <!-- ORDER ITEMS -->
                <!-- ================================================= -->

                <section
                    class="
                        overflow-hidden

                        rounded-2xl

                        border
                        border-gray-200

                        bg-white
                    "
                >

                    <!-- HEADER -->

                    <div
                        class="
                            px-5
                            sm:px-6

                            py-5

                            border-b
                            border-gray-100
                        "
                    >

                        <h2
                            class="
                                text-base
                                font-semibold
                                text-gray-950
                            "
                        >
                            Items in your order
                        </h2>


                        <p
                            class="
                                mt-1

                                text-sm
                                text-gray-500
                            "
                        >
                            {{ itemCount }}
                            {{
                                itemCount === 1
                                    ? 'item'
                                    : 'items'
                            }}
                        </p>

                    </div>


                    <!-- PRODUCTS -->

                    <div
                        class="
                            px-5
                            sm:px-6
                        "
                    >

                        <article
                            v-for="item in order.items"
                            :key="item.id"
                            class="
                                flex
                                gap-4

                                py-5

                                border-b
                                border-gray-100

                                last:border-b-0
                            "
                        >

                            <!-- IMAGE -->

                            <div
                                class="
                                    w-16
                                    h-16

                                    sm:w-20
                                    sm:h-20

                                    shrink-0

                                    overflow-hidden

                                    rounded-xl

                                    border
                                    border-gray-100

                                    bg-gray-50
                                "
                            >

                                <img
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
                                        w-full
                                        h-full

                                        object-contain

                                        p-1
                                    "
                                />

                            </div>


                            <!-- INFO -->

                            <div
                                class="
                                    flex-1
                                    min-w-0
                                "
                            >

                                <div
                                    class="
                                        flex
                                        flex-col

                                        sm:flex-row
                                        sm:items-start
                                        sm:justify-between

                                        gap-2
                                        sm:gap-4
                                    "
                                >

                                    <div
                                        class="
                                            min-w-0
                                        "
                                    >

                                        <p
                                            class="
                                                text-sm
                                                font-medium
                                                leading-5
                                                text-gray-900

                                                line-clamp-2
                                            "
                                        >
                                            {{ item.product?.name }}
                                        </p>


                                        <p
                                            class="
                                                mt-1.5

                                                text-xs
                                                text-gray-500
                                            "
                                        >
                                            Qty {{ item.quantity }}
                                            ·
                                            KES
                                            {{
                                                formatPrice(
                                                    item.price
                                                )
                                            }}
                                            each
                                        </p>

                                    </div>


                                    <p
                                        class="
                                            shrink-0

                                            text-sm
                                            font-semibold
                                            text-gray-950
                                        "
                                    >
                                        KES
                                        {{
                                            formatPrice(
                                                item.price *
                                                item.quantity
                                            )
                                        }}
                                    </p>

                                </div>

                            </div>

                        </article>

                    </div>


                    <!-- TOTAL -->

                    <div
                        class="
                            border-t
                            border-gray-100

                            bg-gray-50/70

                            px-5
                            sm:px-6

                            py-5
                        "
                    >

                        <div
                            class="
                                flex
                                items-end
                                justify-between

                                gap-4
                            "
                        >

                            <div>

                                <p
                                    class="
                                        text-sm
                                        font-medium
                                        text-gray-700
                                    "
                                >
                                    Order total
                                </p>


                                <p
                                    class="
                                        mt-0.5

                                        text-xs
                                        text-gray-400
                                    "
                                >
                                    Including delivery
                                </p>

                            </div>


                            <p
                                class="
                                    text-lg
                                    sm:text-xl

                                    font-semibold
                                    tracking-tight
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

                    </div>

                </section>


                <!-- ================================================= -->
                <!-- SIDEBAR -->
                <!-- ================================================= -->

                <aside
                    class="
                        space-y-5

                        lg:sticky
                        lg:top-28
                    "
                >

                    <!-- ============================================= -->
                    <!-- DELIVERY -->
                    <!-- ============================================= -->

                    <section
                        class="
                            rounded-2xl

                            border
                            border-gray-200

                            bg-white

                            p-5
                        "
                    >

                        <h2
                            class="
                                text-base
                                font-semibold
                                text-gray-950
                            "
                        >
                            Delivery details
                        </h2>


                        <div
                            class="
                                mt-5
                                space-y-5
                            "
                        >

                            <!-- ADDRESS -->

                            <div
                                class="
                                    flex
                                    gap-3
                                "
                            >

                                <MapPin
                                    class="
                                        w-4
                                        h-4

                                        mt-0.5

                                        shrink-0

                                        text-gray-400
                                    "
                                />


                                <div
                                    class="
                                        min-w-0
                                    "
                                >

                                    <p
                                        class="
                                            text-xs
                                            text-gray-400
                                        "
                                    >
                                        Delivery address
                                    </p>


                                    <p
                                        class="
                                            mt-1

                                            text-sm
                                            leading-5
                                            text-gray-800
                                        "
                                    >
                                        {{ order.address }}
                                    </p>

                                </div>

                            </div>


                            <!-- PHONE -->

                            <div
                                class="
                                    flex
                                    gap-3
                                "
                            >

                                <Phone
                                    class="
                                        w-4
                                        h-4

                                        mt-0.5

                                        shrink-0

                                        text-gray-400
                                    "
                                />


                                <div>

                                    <p
                                        class="
                                            text-xs
                                            text-gray-400
                                        "
                                    >
                                        Contact number
                                    </p>


                                    <p
                                        class="
                                            mt-1

                                            text-sm
                                            text-gray-800
                                        "
                                    >
                                        {{ order.phone }}
                                    </p>

                                </div>

                            </div>


                            <!-- METHOD -->

                            <div
                                class="
                                    flex
                                    gap-3
                                "
                            >

                                <Truck
                                    class="
                                        w-4
                                        h-4

                                        mt-0.5

                                        shrink-0

                                        text-gray-400
                                    "
                                />


                                <div>

                                    <p
                                        class="
                                            text-xs
                                            text-gray-400
                                        "
                                    >
                                        Delivery method
                                    </p>


                                    <p
                                        class="
                                            mt-1

                                            text-sm
                                            text-gray-800
                                        "
                                    >
                                        {{ deliveryMethod }}
                                    </p>

                                </div>

                            </div>

                        </div>


                        <!-- ESTIMATE -->

                        <div
                            class="
                                mt-5
                                pt-5

                                border-t
                                border-gray-100
                            "
                        >

                            <p
                                class="
                                    text-xs
                                    text-gray-400
                                "
                            >
                                Delivery estimate
                            </p>


                            <p
                                class="
                                    mt-1

                                    text-sm
                                    font-semibold
                                    text-gray-900
                                "
                            >
                                {{ estimatedDelivery }}
                            </p>

                        </div>

                    </section>


                    <!-- ============================================= -->
                    <!-- PAYMENT -->
                    <!-- ============================================= -->

                    <section
                        class="
                            rounded-2xl

                            border
                            border-gray-200

                            bg-white

                            p-5
                        "
                    >

                        <div
                            class="
                                flex
                                items-center
                                gap-2
                            "
                        >

                            <CreditCard
                                class="
                                    w-4
                                    h-4

                                    text-gray-400
                                "
                            />


                            <h2
                                class="
                                    text-base
                                    font-semibold
                                    text-gray-950
                                "
                            >
                                Payment
                            </h2>

                        </div>


                        <div
                            class="
                                mt-5

                                flex
                                items-start
                                justify-between

                                gap-4
                            "
                        >

                            <div>

                                <p
                                    class="
                                        text-sm
                                        font-medium
                                        text-gray-800
                                    "
                                >
                                    {{ paymentMethod }}
                                </p>


                                <p
                                    class="
                                        mt-1

                                        text-xs
                                        font-medium
                                    "
                                    :class="
                                        paymentStatusClasses
                                    "
                                >
                                    {{ paymentStatus }}
                                </p>

                            </div>


                            <p
                                class="
                                    shrink-0

                                    text-sm
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

                    </section>


                    <!-- ============================================= -->
                    <!-- INVOICE -->
                    <!-- ============================================= -->

                    <section
                        class="
                            rounded-2xl

                            border
                            border-gray-200

                            bg-white

                            p-5
                        "
                    >

                        <h2
                            class="
                                text-sm
                                font-semibold
                                text-gray-900
                            "
                        >
                            Invoice
                        </h2>


                        <p
                            class="
                                mt-1.5

                                text-xs
                                leading-5
                                text-gray-500
                            "
                        >
                            Download a printable copy of
                            this order for your records.
                        </p>


                        <a
                            :href="
                                `/orders/${order.id}/invoice`
                            "
                            target="_blank"
                            class="
                                mt-4

                                inline-flex
                                items-center

                                gap-1.5

                                text-sm
                                font-semibold
                                text-blue-600

                                transition

                                hover:text-blue-700
                                hover:underline
                            "
                        >

                            <Download
                                class="
                                    w-4
                                    h-4
                                "
                            />

                            Download invoice

                        </a>

                    </section>

                </aside>

            </div>

        </main>

    </AppLayout>

</template>