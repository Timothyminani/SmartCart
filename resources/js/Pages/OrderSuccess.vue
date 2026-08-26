<script setup>
import { computed } from 'vue'
import { Link } from '@inertiajs/vue3'
import {
    Check,
    CheckCircle2,
    ChevronRight,
    MapPin,
    Phone,
    Truck,
} from 'lucide-vue-next'

import AppLayout from '@/Layouts/AppLayout.vue'


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
| ORDER ITEMS
|--------------------------------------------------------------------------
*/

const itemCount = computed(() => {
    return props.order.items?.reduce(
        (total, item) =>
            total + Number(item.quantity || 0),
        0
    ) || 0
})


/*
|--------------------------------------------------------------------------
| DELIVERY
|--------------------------------------------------------------------------
*/

const deliveryMethod = computed(() => {
    switch (props.order.delivery_method) {
        case 'express':
            return 'Express delivery'

        case 'pickup':
            return 'Store pickup'

        case 'standard':
            return 'Standard delivery'

        default:
            return props.order.delivery_method || 'Delivery'
    }
})


const estimatedDelivery = computed(() => {
    switch (props.order.delivery_method) {
        case 'express':
            return 'Expected today'

        case 'standard':
            return 'Expected within 2–3 business days'

        case 'pickup':
            return 'Ready for pickup today'

        default:
            return 'Delivery estimate will be updated soon'
    }
})


/*
|--------------------------------------------------------------------------
| PAYMENT
|--------------------------------------------------------------------------
*/

const paymentMethod = computed(() => {
    return props.order.payment_method === 'mpesa'
        ? 'M-Pesa'
        : 'Cash on delivery'
})


const paymentStatus = computed(() => {
    switch (props.order.payment_status) {
        case 'paid':
            return 'Paid'

        case 'pending':
            return 'Payment pending'

        case 'unpaid':
            return 'Pay on delivery'

        default:
            return props.order.payment_status || 'Pending'
    }
})


/*
|--------------------------------------------------------------------------
| ORDER STATUS
|--------------------------------------------------------------------------
*/

const statusLabel = computed(() => {
    switch (props.order.status) {
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


const statusClass = computed(() => {
    switch (props.order.status) {
        case 'delivered':
            return 'bg-green-50 text-green-700 ring-green-600/10'

        case 'shipped':
            return 'bg-blue-50 text-blue-700 ring-blue-600/10'

        case 'processing':
            return 'bg-amber-50 text-amber-700 ring-amber-600/10'

        case 'cancelled':
            return 'bg-red-50 text-red-700 ring-red-600/10'

        default:
            return 'bg-gray-100 text-gray-700 ring-gray-600/10'
    }
})


/*
|--------------------------------------------------------------------------
| ORDER PROGRESS
|--------------------------------------------------------------------------
*/

const progressSteps = computed(() => {
    const statuses = [
        'pending',
        'processing',
        'shipped',
        'delivered',
    ]

    const currentStatus =
        props.order.status || 'pending'

    const currentIndex =
        statuses.indexOf(currentStatus)

    const steps = [
        {
            key: 'pending',
            title: 'Order placed',
        },
        {
            key: 'processing',
            title: 'Processing',
        },
        {
            key: 'shipped',
            title: 'Shipped',
        },
        {
            key: 'delivered',
            title: 'Delivered',
        },
    ]

    return steps.map((step, index) => ({
        ...step,

        completed:
            currentIndex >= 0 &&
            index <= currentIndex,

        active:
            currentIndex === index,
    }))
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

                py-8
                sm:py-10
                lg:py-12
            "
        >

            <!-- ===================================================== -->
            <!-- CONFIRMATION -->
            <!-- ===================================================== -->

            <header
                class="
                    max-w-2xl
                    mx-auto

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

                        bg-green-50
                        text-green-600
                    "
                >
                    <CheckCircle2
                        class="
                            w-6
                            h-6
                        "
                    />
                </div>


                <p
                    class="
                        mt-5

                        text-xs
                        sm:text-sm

                        font-semibold
                        tracking-wide
                        uppercase

                        text-green-700
                    "
                >
                    Order #{{ order.id }}
                </p>


                <h1
                    class="
                        mt-2

                        text-2xl
                        sm:text-3xl

                        font-semibold
                        tracking-tight
                        text-gray-950
                    "
                >
                    Thanks, your order is confirmed.
                </h1>


                <p
                    class="
                        mt-3

                        max-w-xl
                        mx-auto

                        text-sm
                        sm:text-base

                        leading-6
                        text-gray-500
                    "
                >
                    We've received your order and will keep
                    you updated as it moves through delivery.
                </p>


                <div
                    class="
                        mt-5

                        flex
                        flex-wrap
                        items-center
                        justify-center

                        gap-2
                    "
                >

                    <span
                        class="
                            inline-flex
                            items-center

                            rounded-full

                            px-3
                            py-1.5

                            text-xs
                            font-medium

                            ring-1
                            ring-inset
                        "
                        :class="statusClass"
                    >
                        {{ statusLabel }}
                    </span>


                    <span
                        class="
                            inline-flex
                            items-center

                            rounded-full

                            bg-gray-50

                            px-3
                            py-1.5

                            text-xs
                            font-medium
                            text-gray-600

                            ring-1
                            ring-inset
                            ring-gray-200
                        "
                    >
                        {{ paymentMethod }}
                    </span>

                </div>

            </header>


            <!-- ===================================================== -->
            <!-- CONTENT -->
            <!-- ===================================================== -->

            <div
                class="
                    mt-10
                    sm:mt-12

                    grid

                    lg:grid-cols-[minmax(0,1fr)_340px]

                    gap-6
                    lg:gap-8

                    items-start
                "
            >

                <!-- ================================================= -->
                <!-- LEFT -->
                <!-- ================================================= -->

                <div
                    class="
                        min-w-0
                        space-y-6
                    "
                >

                    <!-- ============================================= -->
                    <!-- ORDER SUMMARY -->
                    <!-- ============================================= -->

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
                                flex
                                items-center
                                justify-between

                                gap-4

                                px-5
                                sm:px-6

                                py-5

                                border-b
                                border-gray-100
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
                                    Order summary
                                </h2>


                                <p
                                    class="
                                        mt-1

                                        text-xs
                                        sm:text-sm

                                        text-gray-500
                                    "
                                >
                                    {{ itemCount }}
                                    {{
                                        itemCount === 1
                                            ? 'item'
                                            : 'items'
                                    }}
                                    in this order
                                </p>

                            </div>


                            <span
                                class="
                                    shrink-0

                                    text-xs
                                    font-medium
                                    text-gray-400
                                "
                            >
                                #{{ order.id }}
                            </span>

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


                                <!-- PRODUCT INFO -->

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
                                                {{
                                                    item.product
                                                        ?.name
                                                }}
                                            </p>


                                            <p
                                                class="
                                                    mt-1.5

                                                    text-xs
                                                    text-gray-500
                                                "
                                            >
                                                Qty
                                                {{ item.quantity }}
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
                                bg-gray-50/70

                                px-5
                                sm:px-6

                                py-5

                                border-t
                                border-gray-100
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


                    <!-- ============================================= -->
                    <!-- PROGRESS -->
                    <!-- ============================================= -->

                    <section
                        v-if="order.status !== 'cancelled'"
                        class="
                            rounded-2xl

                            border
                            border-gray-200

                            bg-white

                            px-5
                            sm:px-6

                            py-6
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
                                Order progress
                            </h2>


                            <p
                                class="
                                    mt-1

                                    text-sm
                                    text-gray-500
                                "
                            >
                                We'll update this as your
                                order moves forward.
                            </p>

                        </div>


                        <!-- DESKTOP -->

                        <div
                            class="
                                hidden
                                sm:grid

                                grid-cols-4

                                mt-8
                            "
                        >

                            <div
                                v-for="(step, index) in progressSteps"
                                :key="step.key"
                                class="
                                    relative
                                    text-center
                                "
                            >

                                <!-- LEFT LINE -->

                                <div
                                    v-if="index > 0"
                                    class="
                                        absolute

                                        top-4
                                        right-1/2

                                        h-px
                                        w-full

                                        -z-0
                                    "
                                    :class="
                                        step.completed
                                            ? 'bg-blue-600'
                                            : 'bg-gray-200'
                                    "
                                ></div>


                                <!-- CIRCLE -->

                                <div
                                    class="
                                        relative
                                        z-10

                                        w-8
                                        h-8

                                        mx-auto

                                        rounded-full

                                        flex
                                        items-center
                                        justify-center

                                        border-2
                                    "
                                    :class="
                                        step.completed
                                            ? 'border-blue-600 bg-blue-600 text-white'
                                            : 'border-gray-200 bg-white text-gray-400'
                                    "
                                >

                                    <Check
                                        v-if="step.completed"
                                        class="
                                            w-4
                                            h-4
                                        "
                                    />


                                    <span
                                        v-else
                                        class="
                                            w-2
                                            h-2

                                            rounded-full

                                            bg-gray-300
                                        "
                                    ></span>

                                </div>


                                <p
                                    class="
                                        mt-3

                                        text-xs
                                        font-medium
                                    "
                                    :class="
                                        step.completed
                                            ? 'text-gray-900'
                                            : 'text-gray-400'
                                    "
                                >
                                    {{ step.title }}
                                </p>

                            </div>

                        </div>


                        <!-- MOBILE -->

                        <div
                            class="
                                sm:hidden

                                mt-6

                                space-y-0
                            "
                        >

                            <div
                                v-for="(step, index) in progressSteps"
                                :key="step.key"
                                class="
                                    flex
                                    gap-3
                                "
                            >

                                <div
                                    class="
                                        flex
                                        flex-col
                                        items-center
                                    "
                                >

                                    <div
                                        class="
                                            w-8
                                            h-8

                                            rounded-full

                                            flex
                                            items-center
                                            justify-center

                                            border-2
                                        "
                                        :class="
                                            step.completed
                                                ? 'border-blue-600 bg-blue-600 text-white'
                                                : 'border-gray-200 bg-white text-gray-400'
                                        "
                                    >

                                        <Check
                                            v-if="
                                                step.completed
                                            "
                                            class="
                                                w-4
                                                h-4
                                            "
                                        />


                                        <span
                                            v-else
                                            class="
                                                w-2
                                                h-2

                                                rounded-full

                                                bg-gray-300
                                            "
                                        ></span>

                                    </div>


                                    <div
                                        v-if="
                                            index <
                                            progressSteps.length - 1
                                        "
                                        class="
                                            w-px
                                            h-9
                                        "
                                        :class="
                                            progressSteps[
                                                index + 1
                                            ]?.completed
                                                ? 'bg-blue-600'
                                                : 'bg-gray-200'
                                        "
                                    ></div>

                                </div>


                                <p
                                    class="
                                        pt-1.5

                                        text-sm
                                        font-medium
                                    "
                                    :class="
                                        step.completed
                                            ? 'text-gray-900'
                                            : 'text-gray-400'
                                    "
                                >
                                    {{ step.title }}
                                </p>

                            </div>

                        </div>

                    </section>


                    <!-- CANCELLED -->

                    <section
                        v-else
                        class="
                            rounded-2xl

                            border
                            border-red-100

                            bg-red-50

                            px-5
                            sm:px-6

                            py-5
                        "
                    >

                        <p
                            class="
                                text-sm
                                font-semibold
                                text-red-800
                            "
                        >
                            This order was cancelled.
                        </p>


                        <p
                            class="
                                mt-1

                                text-sm
                                text-red-600
                            "
                        >
                            It will no longer continue
                            through the delivery process.
                        </p>

                    </section>

                </div>


                <!-- ================================================= -->
                <!-- RIGHT -->
                <!-- ================================================= -->

                <aside
                    class="
                        lg:sticky
                        lg:top-28

                        space-y-6
                    "
                >

                    <!-- ============================================= -->
                    <!-- DELIVERY DETAILS -->
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
                                justify-between

                                gap-4
                            "
                        >

                            <h2
                                class="
                                    text-base
                                    font-semibold
                                    text-gray-950
                                "
                            >
                                Delivery
                            </h2>


                            <Truck
                                class="
                                    w-4
                                    h-4

                                    text-gray-400
                                "
                            />

                        </div>


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


                                <div>

                                    <p
                                        class="
                                            text-xs
                                            text-gray-400
                                        "
                                    >
                                        Delivering to
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
                                        Contact
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

                        </div>


                        <!-- DELIVERY ESTIMATE -->

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
                                    font-medium
                                    text-gray-400
                                "
                            >
                                {{ deliveryMethod }}
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

                        <h2
                            class="
                                text-base
                                font-semibold
                                text-gray-950
                            "
                        >
                            Payment
                        </h2>


                        <div
                            class="
                                mt-4

                                flex
                                items-center
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
                                        text-gray-400
                                    "
                                >
                                    {{ paymentStatus }}
                                </p>

                            </div>


                            <span
                                class="
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
                            </span>

                        </div>

                    </section>


                    <!-- ============================================= -->
                    <!-- ACTIONS -->
                    <!-- ============================================= -->

                    <div
                        class="
                            space-y-3
                        "
                    >

                        <Link
                            :href="`/orders/${order.id}`"
                            class="
                                w-full

                                inline-flex
                                items-center
                                justify-between

                                rounded-xl

                                bg-gray-950

                                px-4
                                py-3

                                text-sm
                                font-semibold
                                text-white

                                transition

                                hover:bg-black
                            "
                        >

                            <span>
                                View order details
                            </span>


                            <ChevronRight
                                class="
                                    w-4
                                    h-4
                                "
                            />

                        </Link>


                        <Link
                            href="/productListing"
                            class="
                                w-full

                                inline-flex
                                items-center
                                justify-center

                                rounded-xl

                                border
                                border-gray-200

                                bg-white

                                px-4
                                py-3

                                text-sm
                                font-semibold
                                text-gray-700

                                transition

                                hover:bg-gray-50
                                hover:border-gray-300
                            "
                        >
                            Continue shopping
                        </Link>


                        <Link
                            href="/orders"
                            class="
                                block

                                py-1

                                text-center

                                text-sm
                                font-medium
                                text-blue-600

                                transition

                                hover:text-blue-700
                                hover:underline
                            "
                        >
                            View all orders
                        </Link>

                    </div>

                </aside>

            </div>

        </main>

    </AppLayout>
</template>