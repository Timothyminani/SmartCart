<script setup>
import { computed, onMounted, onBeforeUnmount, ref, watch } from 'vue'
import { router } from '@inertiajs/vue3'
import {
    ArrowLeft,
    Minus,
    Plus,
    Sparkles,
    Loader2,
    ShoppingCart,
} from 'lucide-vue-next'

import { useCart } from '@/composables/useCart'


/*
|--------------------------------------------------------------------------
| PROPS / EMITS
|--------------------------------------------------------------------------
*/

const props = defineProps({
    open: {
        type: Boolean,
        default: false,
    },
})

const emit = defineEmits([
    'close',
])


/*
|--------------------------------------------------------------------------
| CART
|--------------------------------------------------------------------------
*/

const {
    addToCart,
    getCart,
    increaseQty,
    decreaseQty,
    removeItem,
    getCartRecommendations,
    cartMessage,
} = useCart()


/*
|--------------------------------------------------------------------------
| STATE
|--------------------------------------------------------------------------
*/

const cartItems = ref([])
const relatedProducts = ref([])

const loading = ref(false)
const loadingRecommendations = ref(false)

const loadingItems = ref([])


/*
|--------------------------------------------------------------------------
| CLOSE DRAWER
|--------------------------------------------------------------------------
*/

const closeDrawer = () => {
    emit('close')
}


/*
|--------------------------------------------------------------------------
| LOAD CART
|--------------------------------------------------------------------------
*/

const loadCart = async () => {

    loading.value = true

    try {

        cartItems.value = await getCart()

    } catch (error) {

        console.error(
            'Failed to load cart:',
            error
        )

    } finally {

        loading.value = false

    }

}


/*
|--------------------------------------------------------------------------
| LOAD RECOMMENDATIONS
|--------------------------------------------------------------------------
*/

const loadRecommendations = async () => {

    loadingRecommendations.value = true

    try {

        relatedProducts.value =
            await getCartRecommendations()

    } catch (error) {

        console.error(
            'Failed to load cart recommendations:',
            error
        )

        relatedProducts.value = []

    } finally {

        loadingRecommendations.value = false

    }

}


/*
|--------------------------------------------------------------------------
| SYNC CART
|--------------------------------------------------------------------------
*/

const syncCart = async () => {

    await loadCart()

    await loadRecommendations()

}


/*
|--------------------------------------------------------------------------
| TOTALS
|--------------------------------------------------------------------------
*/

const cartTotal = computed(() => {

    return cartItems.value.reduce(
        (sum, item) => {
            return sum +
                Number(item.price) *
                Number(item.quantity)
        },
        0
    )

})


const deliveryFee = computed(() => {

    return cartTotal.value >= 2000
        ? 0
        : 250

})


const remainingForFreeDelivery = computed(() => {

    return Math.max(
        2000 - cartTotal.value,
        0
    )

})


const grandTotal = computed(() => {

    return cartTotal.value +
        deliveryFee.value

})


/*
|--------------------------------------------------------------------------
| FORMAT PRICE
|--------------------------------------------------------------------------
*/

const formatPrice = (value) => {

    return new Intl.NumberFormat(
        'en-KE'
    ).format(
        Number(value || 0)
    )

}


/*
|--------------------------------------------------------------------------
| LOADING HELPERS
|--------------------------------------------------------------------------
*/

const setItemLoading = (
    itemId,
    state
) => {

    if (state) {

        if (
            !loadingItems.value.includes(
                itemId
            )
        ) {
            loadingItems.value.push(
                itemId
            )
        }

        return
    }

    loadingItems.value =
        loadingItems.value.filter(
            id => id !== itemId
        )

}


const isItemLoading = (itemId) => {

    return loadingItems.value.includes(
        itemId
    )

}


/*
|--------------------------------------------------------------------------
| INCREASE
|--------------------------------------------------------------------------
*/

const handleIncrease = async (item) => {

    if (isItemLoading(item.id)) {
        return
    }

    setItemLoading(
        item.id,
        true
    )

    /*
    |--------------------------------------------------------------------------
    | OPTIMISTIC UI
    |--------------------------------------------------------------------------
    */

    item.quantity++

    try {

        const success =
            await increaseQty(
                item.product_id
            )

        if (!success) {

            item.quantity--

        }

    } catch (error) {

        item.quantity--

        console.error(
            'Failed to increase quantity:',
            error
        )

    } finally {

        setItemLoading(
            item.id,
            false
        )

    }

}


/*
|--------------------------------------------------------------------------
| DECREASE
|--------------------------------------------------------------------------
*/

const handleDecrease = async (item) => {

    if (isItemLoading(item.id)) {
        return
    }

    setItemLoading(
        item.id,
        true
    )

    try {

        if (item.quantity > 1) {

            const previousQuantity =
                item.quantity

            /*
            |--------------------------------------------------------------------------
            | OPTIMISTIC UI
            |--------------------------------------------------------------------------
            */

            item.quantity--

            try {

                await decreaseQty(
                    item.id,
                    previousQuantity
                )

            } catch (error) {

                item.quantity =
                    previousQuantity

                throw error

            }

        } else {

            const index =
                cartItems.value.findIndex(
                    cartItem =>
                        cartItem.id === item.id
                )

            const removedItem =
                cartItems.value[index]

            /*
            |--------------------------------------------------------------------------
            | OPTIMISTIC REMOVE
            |--------------------------------------------------------------------------
            */

            cartItems.value.splice(
                index,
                1
            )

            try {

                await removeItem(
                    item.id
                )

            } catch (error) {

                cartItems.value.splice(
                    index,
                    0,
                    removedItem
                )

                throw error

            }

        }

    } catch (error) {

        console.error(
            'Failed to decrease quantity:',
            error
        )

    } finally {

        setItemLoading(
            item.id,
            false
        )

    }

}


/*
|--------------------------------------------------------------------------
| REMOVE
|--------------------------------------------------------------------------
*/

const handleRemove = async (item) => {

    if (isItemLoading(item.id)) {
        return
    }

    setItemLoading(
        item.id,
        true
    )

    const index =
        cartItems.value.findIndex(
            cartItem =>
                cartItem.id === item.id
        )

    if (index === -1) {

        setItemLoading(
            item.id,
            false
        )

        return

    }

    const removedItem =
        cartItems.value[index]

    /*
    |--------------------------------------------------------------------------
    | OPTIMISTIC REMOVE
    |--------------------------------------------------------------------------
    */

    cartItems.value.splice(
        index,
        1
    )

    try {

        await removeItem(
            item.id
        )

    } catch (error) {

        cartItems.value.splice(
            index,
            0,
            removedItem
        )

        console.error(
            'Failed to remove item:',
            error
        )

    } finally {

        setItemLoading(
            item.id,
            false
        )

    }

}


/*
|--------------------------------------------------------------------------
| ADD RECOMMENDATION
|--------------------------------------------------------------------------
*/

const handleAddRecommendation = async (
    product
) => {

    if (
        isItemLoading(
            `recommendation-${product.id}`
        )
    ) {
        return
    }

    const loadingKey =
        `recommendation-${product.id}`

    setItemLoading(
        loadingKey,
        true
    )

    try {

        const success =
            await addToCart(
                product.id
            )

        if (success) {

            await loadCart()

            await loadRecommendations()

        }

    } catch (error) {

        console.error(
            'Failed to add recommendation:',
            error
        )

    } finally {

        setItemLoading(
            loadingKey,
            false
        )

    }

}


/*
|--------------------------------------------------------------------------
| CHECKOUT
|--------------------------------------------------------------------------
*/

const goToCheckout = () => {

    closeDrawer()

    router.visit(
        route('checkout')
    )

}


/*
|--------------------------------------------------------------------------
| WATCH OPEN
|--------------------------------------------------------------------------
*/

watch(
    () => props.open,
    async (isOpen) => {

        if (!isOpen) {
            return
        }

        await loadCart()

        await loadRecommendations()

    }
)


/*
|--------------------------------------------------------------------------
| CART EVENT
|--------------------------------------------------------------------------
*/

onMounted(() => {

    window.addEventListener(
        'cartUpdated',
        syncCart
    )

})


onBeforeUnmount(() => {

    window.removeEventListener(
        'cartUpdated',
        syncCart
    )

})
</script>


<template>

    <!-- ========================================================= -->
    <!-- OVERLAY -->
    <!-- ========================================================= -->

    <Transition name="fade">

        <div
            v-if="open"
            class="fixed inset-0
                   bg-black/40
                   backdrop-blur-sm
                   z-50"
            @click="closeDrawer"
        ></div>

    </Transition>


    <!-- ========================================================= -->
    <!-- DRAWER -->
    <!-- ========================================================= -->

    <div
        class="fixed top-0 right-0
               h-full
               w-full
               md:w-[85%]
               lg:w-[75%]
               bg-white
               shadow-2xl
               z-[60]
               transform
               transition-transform
               duration-300
               flex"
        :class="
            open
                ? 'translate-x-0'
                : 'translate-x-full'
        "
    >

        <!-- ===================================================== -->
        <!-- LEFT SIDE -->
        <!-- ===================================================== -->

        <div
            class="flex-1
                   flex flex-col
                   border-r
                   min-w-0"
        >

            <!-- HEADER -->

            <div
                class="flex
                       items-center
                       gap-3
                       p-6
                       border-b
                       bg-white"
            >

                <button
                    @click="closeDrawer"
                    class="w-9 h-9
                           flex items-center
                           justify-center
                           rounded-full
                           hover:bg-gray-100
                           transition"
                >

                    <ArrowLeft
                        class="w-5 h-5
                               text-gray-700"
                    />

                </button>


                <div>

                    <h2
                        class="text-xl
                               font-bold"
                    >
                        Your Cart
                    </h2>

                    <p
                        class="text-sm
                               text-gray-500"
                    >
                        {{ cartItems.length }}
                        {{
                            cartItems.length === 1
                                ? 'item'
                                : 'items'
                        }}
                    </p>

                </div>

            </div>


            <!-- CONTENT -->

            <div
                class="flex-1
                       overflow-y-auto
                       p-6"
            >

                <!-- LOADING -->

                <div
                    v-if="loading"
                    class="h-full
                           flex
                           items-center
                           justify-center"
                >

                    <Loader2
                        class="w-7 h-7
                               text-blue-600
                               animate-spin"
                    />

                </div>


                <!-- EMPTY -->

                <div
                    v-else-if="!cartItems.length"
                    class="h-full
                           flex flex-col
                           items-center
                           justify-center
                           text-center
                           px-6"
                >

                    <div
                        class="w-16 h-16
                               rounded-full
                               bg-blue-50
                               flex items-center
                               justify-center"
                    >

                        <ShoppingCart
                            class="w-8 h-8
                                   text-blue-500"
                        />

                    </div>


                    <h3
                        class="text-lg
                               font-semibold
                               text-gray-900
                               mt-4"
                    >
                        Your cart is empty
                    </h3>


                    <p
                        class="text-sm
                               text-gray-500
                               mt-1"
                    >
                        Add something you like
                        and it will appear here.
                    </p>


                    <button
                        @click="closeDrawer"
                        class="mt-6
                               px-5 py-2.5
                               rounded-xl
                               bg-blue-600
                               hover:bg-blue-700
                               text-white
                               text-sm
                               font-medium
                               transition"
                    >
                        Continue Shopping
                    </button>

                </div>


                <!-- ITEMS -->

                <div
                    v-else
                    class="space-y-5"
                >

                    <div
                        v-for="item in cartItems"
                        :key="item.id"
                        class="flex
                               gap-4
                               border-b
                               border-gray-100
                               pb-5"
                    >

                        <!-- IMAGE -->

                        <div
                            class="w-24 h-24
                                   shrink-0
                                   rounded-xl
                                   overflow-hidden
                                   bg-gray-100"
                        >

                            <img
                                v-if="item.image"
                                :src="`/storage/${item.image}`"
                                :alt="item.name"
                                class="w-full h-full
                                       object-cover"
                            />

                        </div>


                        <!-- DETAILS -->

                        <div
                            class="flex-1
                                   min-w-0"
                        >

                            <p
                                class="font-semibold
                                       text-gray-800
                                       line-clamp-2"
                            >
                                {{ item.name }}
                            </p>


                            <p
                                class="text-sm
                                       text-gray-500
                                       mt-1"
                            >
                                KES
                                {{ formatPrice(item.price) }}
                            </p>


                            <!-- QUANTITY -->

                            <div
                                class="flex
                                       items-center
                                       gap-3
                                       mt-3"
                            >

                                <button
                                    @click="handleDecrease(item)"
                                    :disabled="
                                        isItemLoading(item.id)
                                    "
                                    class="w-8 h-8
                                           bg-gray-100
                                           hover:bg-gray-200
                                           rounded-lg
                                           flex items-center
                                           justify-center
                                           disabled:opacity-50
                                           transition"
                                >

                                    <Minus
                                        class="w-4 h-4"
                                    />

                                </button>


                                <div
                                    class="min-w-8
                                           text-center
                                           font-semibold"
                                >

                                    <Loader2
                                        v-if="
                                            isItemLoading(
                                                item.id
                                            )
                                        "
                                        class="w-4 h-4
                                               mx-auto
                                               animate-spin
                                               text-blue-600"
                                    />

                                    <span v-else>
                                        {{ item.quantity }}
                                    </span>

                                </div>


                                <button
                                    @click="handleIncrease(item)"
                                    :disabled="
                                        isItemLoading(item.id)
                                    "
                                    class="w-8 h-8
                                           bg-gray-100
                                           hover:bg-gray-200
                                           rounded-lg
                                           flex items-center
                                           justify-center
                                           disabled:opacity-50
                                           transition"
                                >

                                    <Plus
                                        class="w-4 h-4"
                                    />

                                </button>

                            </div>

                        </div>


                        <!-- PRICE / REMOVE -->

                        <div
                            class="text-right
                                   shrink-0"
                        >

                            <p
                                class="font-bold
                                       text-blue-600"
                            >
                                KES
                                {{
                                    formatPrice(
                                        item.price *
                                        item.quantity
                                    )
                                }}
                            </p>


                            <button
                                @click="handleRemove(item)"
                                :disabled="
                                    isItemLoading(item.id)
                                "
                                class="text-xs
                                       text-red-500
                                       hover:text-red-600
                                       mt-2
                                       disabled:opacity-50"
                            >
                                Remove
                            </button>

                        </div>

                    </div>

                </div>

            </div>

        </div>


        <!-- ===================================================== -->
        <!-- RIGHT SIDE -->
        <!-- ===================================================== -->

        <div
         v-if="cartItems.length"
            class="hidden md:flex
                   w-[320px]
                   p-6
                   bg-gray-50
                   flex-col
                   h-full
                   shrink-0"
        >

            <!-- ================================================= -->
            <!-- RECOMMENDATIONS -->
            <!-- ================================================= -->

            <div
                v-if="cartItems.length"
                class="mb-6"
            >

                <div
                    class="flex
                           items-center
                           justify-between
                           mb-4"
                >

                    <div>

                        <h3
                            class="text-sm
                                   font-bold
                                   text-gray-800"
                        >
                            Recommended for you
                        </h3>


                        <p
                            class="text-[11px]
                                   text-gray-500"
                        >
                            Based on your cart items
                        </p>

                    </div>


                    <Sparkles
                        class="w-4 h-4
                               text-blue-600"
                    />

                </div>


                <!-- LOADING -->

                <div
                    v-if="loadingRecommendations"
                    class="h-20
                           flex items-center
                           justify-center"
                >

                    <Loader2
                        class="w-5 h-5
                               text-blue-600
                               animate-spin"
                    />

                </div>


                <!-- PRODUCTS -->

                <div
                    v-else
                    class="space-y-3
                           max-h-[210px]
                           overflow-y-auto
                           pr-1"
                >

                    <div
                        v-for="item in relatedProducts"
                        :key="item.id"
                        class="group
                               flex gap-3
                               bg-white
                               p-3
                               rounded-xl
                               border
                               border-gray-100
                               hover:border-blue-200
                               hover:shadow-md
                               transition-all
                               duration-300"
                    >

                        <!-- IMAGE -->

                        <div
                            class="w-16 h-16
                                   shrink-0
                                   overflow-hidden
                                   rounded-lg
                                   bg-gray-100"
                        >

                            <img
                                v-if="item.images?.[0]"
                                :src="`/storage/${item.images[0].image_path}`"
                                :alt="item.name"
                                class="w-full h-full
                                       object-cover
                                       group-hover:scale-105
                                       transition
                                       duration-300"
                            />

                        </div>


                        <!-- INFO -->

                        <div
                            class="flex-1
                                   min-w-0"
                        >

                            <p
                                class="text-sm
                                       font-medium
                                       text-gray-800
                                       line-clamp-2"
                            >
                                {{ item.name }}
                            </p>


                            <p
                                class="text-sm
                                       text-blue-600
                                       font-bold
                                       mt-1"
                            >
                                KES
                                {{
                                    formatPrice(
                                        item.sale_price ||
                                        item.price
                                    )
                                }}
                            </p>

                        </div>


                        <!-- ADD -->

                        <button
                            @click="
                                handleAddRecommendation(
                                    item
                                )
                            "
                            :disabled="
                                isItemLoading(
                                    `recommendation-${item.id}`
                                )
                            "
                            class="self-center
                                   bg-blue-600
                                   hover:bg-blue-700
                                   disabled:opacity-60
                                   text-white
                                   text-xs
                                   px-3 py-2
                                   rounded-lg
                                   transition
                                   flex
                                   items-center
                                   gap-1"
                        >

                            <Loader2
                                v-if="
                                    isItemLoading(
                                        `recommendation-${item.id}`
                                    )
                                "
                                class="w-3 h-3
                                       animate-spin"
                            />

                            <Plus
                                v-else
                                class="w-3 h-3"
                            />

                            Add

                        </button>

                    </div>

                </div>

            </div>


            <!-- ================================================= -->
            <!-- SUMMARY -->
            <!-- ================================================= -->

            <div
                v-if="cartItems.length"
            >

                <h3
                    class="text-lg
                           font-bold
                           mb-4"
                >
                    Summary
                </h3>


                <div
                    class="space-y-3
                           text-sm"
                >

                    <div
                        class="flex
                               justify-between"
                    >
                        <span>
                            Subtotal
                        </span>

                        <span>
                            KES
                            {{ formatPrice(cartTotal) }}
                        </span>
                    </div>


                    <div
                        class="flex
                               justify-between"
                    >
                        <span>
                            Delivery
                        </span>

                        <span>
                            {{
                                deliveryFee === 0
                                    ? 'FREE'
                                    : 'KES ' +
                                      formatPrice(
                                          deliveryFee
                                      )
                            }}
                        </span>
                    </div>


                    <div
                        class="border-t
                               pt-3
                               flex
                               justify-between
                               font-bold
                               text-lg"
                    >

                        <span>
                            Total
                        </span>

                        <span
                            class="text-blue-600"
                        >
                            KES
                            {{ formatPrice(grandTotal) }}
                        </span>

                    </div>

                </div>


                <p
                    v-if="
                        remainingForFreeDelivery > 0
                    "
                    class="text-xs
                           text-blue-600
                           mt-3"
                >
                    Add KES
                    {{
                        formatPrice(
                            remainingForFreeDelivery
                        )
                    }}
                    for FREE delivery
                </p>

            </div>


            <!-- ================================================= -->
            <!-- ACTIONS -->
            <!-- ================================================= -->

            <div
                v-if="cartItems.length"
                class="space-y-2
                       mt-auto
                       pt-6"
            >

                <button
                    @click="closeDrawer"
                    class="w-full
                           border
                           border-gray-200
                           hover:bg-white
                           py-2.5
                           rounded-lg
                           transition"
                >
                    Continue Shopping
                </button>


                <button
                    @click="goToCheckout"
                    class="w-full
                           bg-blue-600
                           hover:bg-blue-700
                           text-white
                           py-3
                           rounded-lg
                           font-medium
                           transition"
                >
                    Checkout
                </button>

            </div>

        </div>


        <!-- ===================================================== -->
        <!-- MOBILE FOOTER -->
        <!-- ===================================================== -->

        <div
            v-if="cartItems.length"
            class="md:hidden
                   absolute
                   left-0 right-0 bottom-0
                   bg-white
                   border-t
                   p-4"
        >

            <div
                class="flex
                       justify-between
                       mb-3"
            >

                <span
                    class="text-sm
                           text-gray-500"
                >
                    Total
                </span>

                <span
                    class="font-bold
                           text-blue-600"
                >
                    KES
                    {{ formatPrice(grandTotal) }}
                </span>

            </div>


            <button
                @click="goToCheckout"
                class="w-full
                       bg-blue-600
                       hover:bg-blue-700
                       text-white
                       py-3
                       rounded-xl
                       font-medium
                       transition"
            >
                Checkout
            </button>

        </div>

    </div>


    <!-- ========================================================= -->
    <!-- CART MESSAGE -->
    <!-- ========================================================= -->

    <Transition
        enter-active-class="transition duration-300"
        enter-from-class="opacity-0 translate-y-3"
        enter-to-class="opacity-100 translate-y-0"
        leave-active-class="transition duration-300"
        leave-from-class="opacity-100 translate-y-0"
        leave-to-class="opacity-0 translate-y-3"
    >

        <div
            v-if="cartMessage"
            class="fixed bottom-6
                   left-1/2
                   -translate-x-1/2
                   bg-red-500
                   text-white
                   px-6 py-3
                   rounded-xl
                   shadow-lg
                   z-[100]"
        >
            {{ cartMessage }}
        </div>

    </Transition>

</template>


<style scoped>

.fade-enter-active,
.fade-leave-active {
    transition: opacity 0.25s ease;
}

.fade-enter-from,
.fade-leave-to {
    opacity: 0;
}

</style>