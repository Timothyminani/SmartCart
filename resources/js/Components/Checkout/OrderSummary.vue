<script setup>
import {
    Minus,
    Plus,
    Loader2,
    LockKeyhole,
    RotateCcw,
    Truck,
    ArrowRight,
    Trash2,
} from 'lucide-vue-next'


defineProps({
    items: {
        type: Array,
        default: () => [],
    },

    subtotal: {
        type: Number,
        default: 0,
    },

    deliveryFee: {
        type: Number,
        default: 0,
    },

    total: {
        type: Number,
        default: 0,
    },

    paymentMethod: {
        type: String,
        default: 'cod',
    },

    loading: {
        type: Boolean,
        default: false,
    },

    loadingItems: {
        type: Array,
        default: () => [],
    },
})


const emit = defineEmits([
    'increase',
    'decrease',
    'remove',
    'submit',
])


const formatPrice = (value) => {
    return new Intl.NumberFormat().format(
        Number(value || 0)
    )
}
</script>


<template>

    <aside
        class="
            lg:sticky
            lg:top-28

            self-start
        "
    >

        <div
            class="
                rounded-2xl

                border
                border-gray-200

                bg-white

                p-5
                sm:p-6

                shadow-sm
            "
        >

            <!-- HEADER -->

            <div
                class="
                    flex
                    items-center
                    justify-between

                    mb-5
                "
            >

                <h2
                    class="
                        text-lg
                        font-semibold
                        text-gray-900
                    "
                >
                    Order summary
                </h2>


                <span
                    class="
                        text-xs
                        font-medium
                        text-gray-500
                    "
                >
                    {{ items.length }}
                    {{
                        items.length === 1
                            ? 'item'
                            : 'items'
                    }}
                </span>

            </div>


            <!-- PRODUCTS -->

            <div
                v-if="items.length"
                class="
                    divide-y
                    divide-gray-100
                "
            >

                <article
                    v-for="item in items"
                    :key="item.id"
                    class="
                        py-4
                        first:pt-0
                    "
                >

                    <div
                        class="
                            flex
                            gap-3
                        "
                    >

                        <!-- IMAGE -->

                        <img
                            :src="
                                `/storage/${item.image}`
                            "
                            :alt="item.name"
                            class="
                                w-16
                                h-16

                                sm:w-18
                                sm:h-18

                                shrink-0

                                rounded-xl

                                object-cover

                                border
                                border-gray-100
                            "
                        />


                        <!-- DETAILS -->

                        <div
                            class="
                                min-w-0
                                flex-1
                            "
                        >

                            <div
                                class="
                                    flex
                                    justify-between
                                    gap-3
                                "
                            >

                                <p
                                    class="
                                        text-sm
                                        font-medium
                                        text-gray-900

                                        line-clamp-2
                                    "
                                >
                                    {{ item.name }}
                                </p>


                                <p
                                    class="
                                        shrink-0

                                        text-sm
                                        font-semibold
                                        text-gray-900
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


                            <p
                                class="
                                    mt-1
                                    text-xs
                                    text-gray-400
                                "
                            >
                                KES
                                {{
                                    formatPrice(
                                        item.price
                                    )
                                }}
                                each
                            </p>


                            <!-- CONTROLS -->

                            <div
                                class="
                                    mt-3

                                    flex
                                    items-center
                                    justify-between

                                    gap-3
                                "
                            >

                                <div
                                    class="
                                        inline-flex
                                        items-center

                                        rounded-lg

                                        border
                                        border-gray-200

                                        overflow-hidden
                                    "
                                >

                                    <button
                                        type="button"
                                        :disabled="
                                            loadingItems.includes(
                                                item.id
                                            )
                                        "
                                        @click="
                                            emit(
                                                'decrease',
                                                item
                                            )
                                        "
                                        class="
                                            flex
                                            h-8
                                            w-8

                                            items-center
                                            justify-center

                                            text-gray-500

                                            hover:bg-gray-50
                                            hover:text-gray-900

                                            disabled:opacity-50
                                        "
                                    >
                                        <Minus
                                            class="w-3.5 h-3.5"
                                        />
                                    </button>


                                    <span
                                        class="
                                            flex
                                            h-8
                                            min-w-[32px]

                                            items-center
                                            justify-center

                                            border-x
                                            border-gray-200

                                            text-xs
                                            font-medium
                                            text-gray-900
                                        "
                                    >

                                        <Loader2
                                            v-if="
                                                loadingItems.includes(
                                                    item.id
                                                )
                                            "
                                            class="
                                                w-3.5
                                                h-3.5
                                                animate-spin
                                            "
                                        />

                                        <template v-else>
                                            {{ item.quantity }}
                                        </template>

                                    </span>


                                    <button
                                        type="button"
                                        :disabled="
                                            loadingItems.includes(
                                                item.id
                                            )
                                        "
                                        @click="
                                            emit(
                                                'increase',
                                                item
                                            )
                                        "
                                        class="
                                            flex
                                            h-8
                                            w-8

                                            items-center
                                            justify-center

                                            text-gray-500

                                            hover:bg-gray-50
                                            hover:text-gray-900

                                            disabled:opacity-50
                                        "
                                    >
                                        <Plus
                                            class="w-3.5 h-3.5"
                                        />
                                    </button>

                                </div>


                                <button
                                    type="button"
                                    :disabled="
                                        loadingItems.includes(
                                            item.id
                                        )
                                    "
                                    @click="
                                        emit(
                                            'remove',
                                            item
                                        )
                                    "
                                    class="
                                        inline-flex
                                        items-center
                                        gap-1

                                        text-xs
                                        text-gray-400

                                        transition

                                        hover:text-red-500

                                        disabled:opacity-50
                                    "
                                >

                                    <Trash2
                                        class="w-3.5 h-3.5"
                                    />

                                    <span
                                        class="
                                            hidden
                                            sm:inline
                                        "
                                    >
                                        Remove
                                    </span>

                                </button>

                            </div>

                        </div>

                    </div>

                </article>

            </div>


            <!-- TOTALS -->

            <div
                class="
                    mt-5

                    border-t
                    border-gray-200

                    pt-5

                    space-y-3
                "
            >

                <div
                    class="
                        flex
                        justify-between

                        text-sm
                    "
                >
                    <span class="text-gray-500">
                        Subtotal
                    </span>

                    <span
                        class="
                            font-medium
                            text-gray-900
                        "
                    >
                        KES
                        {{ formatPrice(subtotal) }}
                    </span>
                </div>


                <div
                    class="
                        flex
                        justify-between

                        text-sm
                    "
                >
                    <span class="text-gray-500">
                        Delivery
                    </span>

                    <span
                        v-if="deliveryFee > 0"
                        class="
                            font-medium
                            text-gray-900
                        "
                    >
                        KES
                        {{ formatPrice(deliveryFee) }}
                    </span>

                    <span
                        v-else
                        class="
                            font-medium
                            text-green-600
                        "
                    >
                        Free
                    </span>
                </div>


                <div
                    class="
                        flex
                        items-end
                        justify-between

                        border-t
                        border-gray-100

                        pt-4
                        mt-4
                    "
                >

                    <div>

                        <p
                            class="
                                text-sm
                                font-medium
                                text-gray-900
                            "
                        >
                            Total
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
                            text-xl
                            font-bold
                            text-gray-900
                        "
                    >
                        KES
                        {{ formatPrice(total) }}
                    </p>

                </div>

            </div>


            <!-- ACTION -->

            <button
                type="button"
                :disabled="
                    loading ||
                    !items.length
                "
                @click="emit('submit')"
                class="
                    mt-6

                    w-full

                    inline-flex
                    items-center
                    justify-center
                    gap-2

                    rounded-xl

                    bg-blue-600

                    px-5
                    py-3

                    text-sm
                    font-semibold
                    text-white

                    transition

                    hover:bg-blue-700

                    disabled:cursor-not-allowed
                    disabled:opacity-50
                "
            >

                <Loader2
                    v-if="loading"
                    class="
                        w-4
                        h-4
                        animate-spin
                    "
                />


                <template v-if="loading">
                    Processing...
                </template>


                <template v-else>

                    {{
                        paymentMethod === 'mpesa'
                            ? 'Continue with M-Pesa'
                            : 'Place order'
                    }}

                    <span
                        class="
                            hidden
                            sm:inline
                        "
                    >
                        · KES {{ formatPrice(total) }}
                    </span>


                    <ArrowRight
                        class="w-4 h-4"
                    />

                </template>

            </button>


            <!-- TRUST -->

            <div
                class="
                    mt-4

                    flex
                    flex-wrap
                    items-center
                    justify-center

                    gap-x-4
                    gap-y-2

                    text-[11px]
                    text-gray-400
                "
            >

                <span
                    class="
                        inline-flex
                        items-center
                        gap-1
                    "
                >
                    <LockKeyhole
                        class="w-3.5 h-3.5"
                    />

                    Secure checkout
                </span>


                <span
                    class="
                        inline-flex
                        items-center
                        gap-1
                    "
                >
                    <RotateCcw
                        class="w-3.5 h-3.5"
                    />

                    7-day returns
                </span>


                <span
                    class="
                        inline-flex
                        items-center
                        gap-1
                    "
                >
                    <Truck
                        class="w-3.5 h-3.5"
                    />

                    Reliable delivery
                </span>

            </div>

        </div>

    </aside>

</template>