<script setup>
import {
    Heart,
    X,
    Trash2,
    ArrowRight,
    ShoppingCart,
    Plus,
    Minus,
    Loader2
} from 'lucide-vue-next'

import { Link } from '@inertiajs/vue3'
import { useWishlist } from '@/composables/useWishlist'
import { useCart } from '@/composables/useCart'
import { ref, watch } from 'vue'
import axios from 'axios'

const props = defineProps({
    open: {
        type: Boolean,
        default: false
    }
})

const emit = defineEmits(['close'])

const products = ref([])
const loading = ref(false)
const cartLoading = ref(null)
const errorMessage = ref('')

const {
    wishlist,
    removeFromWishlist
} = useWishlist()

const {
    addToCart,
    increaseQty,
    decreaseQty,
    getCartItem,
    removeItem
} = useCart()


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
| FORMAT PRICE
|--------------------------------------------------------------------------
*/

const formatPrice = (value) => {
    return new Intl.NumberFormat().format(value)
}


/*
|--------------------------------------------------------------------------
| FETCH WISHLIST PRODUCTS
|--------------------------------------------------------------------------
*/

const fetchWishlistProducts = async () => {

    if (!wishlist.value.length) {
        products.value = []
        return
    }

    loading.value = true
    errorMessage.value = ''

    try {

        const response = await axios.get('/wishlist/products', {
            params: {
                ids: wishlist.value
            },
            paramsSerializer: {
                indexes: true
            }
        })

        products.value = response.data

    } catch (error) {

        console.error('Failed to load wishlist products:', error)

        console.error('Status:', error.response?.status)
        console.error('Response:', error.response?.data)

        errorMessage.value =
            error.response?.data?.message ||
            'Unable to load your wishlist.'

    } finally {

        loading.value = false

    }
}


/*
|--------------------------------------------------------------------------
| REMOVE FROM WISHLIST
|--------------------------------------------------------------------------
*/

const removeProduct = (productId) => {

    removeFromWishlist(productId)

    // Remove immediately from drawer UI
    products.value = products.value.filter(
        product => product.id !== productId
    )
}


/*
|--------------------------------------------------------------------------
| GET CART ITEM
|--------------------------------------------------------------------------
*/

const getProductCartItem = async (product) => {

    try {

        return await getCartItem(product.id)

    } catch (error) {

        console.error(
            'Failed to get cart item:',
            error
        )

        return null
    }
}


/*
|--------------------------------------------------------------------------
| ADD TO CART
|--------------------------------------------------------------------------
*/

const handleAddToCart = async (product) => {

    cartLoading.value = product.id
    errorMessage.value = ''

    try {

        await addToCart(product.id)

        /*
        |--------------------------------------------------------------------------
        | Refresh product cart state
        |--------------------------------------------------------------------------
        */

        product.cartItem =
            await getProductCartItem(product)

    } catch (error) {

        console.error(
            'Failed to add product to cart:',
            error
        )

        errorMessage.value =
            error.response?.data?.message ||
            'Unable to add product to cart.'

    } finally {

        cartLoading.value = null

    }
}


/*
|--------------------------------------------------------------------------
| INCREASE CART QUANTITY
|--------------------------------------------------------------------------
*/

const handleIncrease = async (product) => {

    if (!product.cartItem) {
        return
    }

    cartLoading.value = product.id

    try {

        const success =
            await increaseQty(product.id)

        if (success) {

            product.cartItem =
                await getProductCartItem(product)

        }

    } catch (error) {

        console.error(
            'Failed to increase quantity:',
            error
        )

    } finally {

        cartLoading.value = null

    }
}


/*
|--------------------------------------------------------------------------
| DECREASE CART QUANTITY
|--------------------------------------------------------------------------
*/

const handleDecrease = async (product) => {

    if (!product.cartItem) {
        return
    }

    cartLoading.value = product.id

    try {

        if (product.cartItem.quantity > 1) {

            await decreaseQty(
                product.cartItem.id,
                product.cartItem.quantity
            )

            product.cartItem =
                await getProductCartItem(product)

        } else {

            await removeItem(
                product.cartItem.id
            )

            product.cartItem = null

        }

    } catch (error) {

        console.error(
            'Failed to decrease quantity:',
            error
        )

    } finally {

        cartLoading.value = null

    }
}


/*
|--------------------------------------------------------------------------
| LOAD CART STATE FOR WISHLIST PRODUCTS
|--------------------------------------------------------------------------
*/

const loadCartStates = async () => {

    for (const product of products.value) {

        product.cartItem =
            await getProductCartItem(product)

    }

}


/*
|--------------------------------------------------------------------------
| WATCH DRAWER OPEN
|--------------------------------------------------------------------------
*/

watch(
    () => props.open,
    async (isOpen) => {

        if (isOpen) {

            await fetchWishlistProducts()

            await loadCartStates()

        }

    },
    {
        immediate: true
    }
)


/*
|--------------------------------------------------------------------------
| WATCH WISHLIST CHANGES
|--------------------------------------------------------------------------
*/

watch(
    wishlist,
    async () => {

        if (!props.open) {
            return
        }

        await fetchWishlistProducts()

        await loadCartStates()

    },
    {
        deep: true
    }
)

</script>


<template>

    <!-- BACKDROP -->

    <Transition name="fade">

        <div
            v-if="open"
            class="fixed inset-0 bg-black/40 z-40"
            @click="closeDrawer"
        ></div>

    </Transition>


    <!-- DRAWER -->

    <Transition name="slide">

        <aside
            v-if="open"
            class="fixed top-0 right-0 h-full w-full sm:w-[440px]
                   bg-white z-50 shadow-2xl flex flex-col"
        >


            <!-- HEADER -->

            <div
                class="flex items-center justify-between
                       px-6 py-5 border-b border-gray-100"
            >

                <div>

                    <div class="flex items-center gap-2">

                        <Heart
                            class="w-5 h-5 text-red-500"
                            fill="currentColor"
                        />

                        <h2
                            class="text-xl font-bold text-gray-900"
                        >
                            Wishlist
                        </h2>

                    </div>

                    <p
                        class="text-sm text-gray-500 mt-1"
                    >
                        {{ wishlist.length }}
                        {{ wishlist.length === 1 ? 'item' : 'items' }}
                    </p>

                </div>


                <button
                    @click="closeDrawer"
                    class="w-9 h-9 rounded-full
                           flex items-center justify-center
                           text-gray-500
                           hover:bg-gray-100
                           hover:text-gray-900
                           transition"
                >

                    <X class="w-5 h-5" />

                </button>

            </div>


            <!-- CONTENT -->

            <div
                class="flex-1 overflow-y-auto"
            >


                <!-- LOADING -->

                <div
                    v-if="loading"
                    class="h-full flex items-center
                           justify-center"
                >

                    <Loader2
                        class="w-7 h-7 text-blue-600
                               animate-spin"
                    />

                </div>


                <!-- ERROR -->

                <div
                    v-else-if="errorMessage"
                    class="p-6 text-center"
                >

                    <p
                        class="text-sm text-red-500"
                    >
                        {{ errorMessage }}
                    </p>

                </div>


                <!-- EMPTY -->

                <div
                    v-else-if="products.length === 0"
                    class="h-full flex flex-col items-center
                           justify-center px-8 text-center"
                >

                    <div
                        class="w-16 h-16 rounded-full
                               bg-red-50
                               flex items-center
                               justify-center mb-5"
                    >

                        <Heart
                            class="w-8 h-8 text-red-400"
                        />

                    </div>


                    <h3
                        class="text-lg font-semibold
                               text-gray-900"
                    >
                        Your wishlist is empty
                    </h3>


                    <p
                        class="text-sm text-gray-500
                               mt-2 max-w-xs"
                    >
                        Save products you love and
                        come back to them later.
                    </p>


                    <button
                        @click="closeDrawer"
                        class="mt-6 px-5 py-2.5
                               bg-blue-600
                               hover:bg-blue-700
                               text-white
                               rounded-lg
                               text-sm font-medium
                               transition"
                    >
                        Continue Shopping
                    </button>

                </div>


                <!-- PRODUCTS -->

                <div
                    v-else
                    class="p-5 space-y-4"
                >

                    <div
                        v-for="product in products"
                        :key="product.id"
                        class="p-3 rounded-xl
                               border border-gray-100
                               hover:border-gray-200
                               hover:shadow-sm
                               transition"
                    >


                        <!-- PRODUCT TOP -->

                        <div class="flex gap-4">


                            <!-- IMAGE -->

                            <Link
                                :href="route('products.show', {
                                    product: product.slug
                                })"
                                @click="closeDrawer"
                                class="w-20 h-20 shrink-0
                                       rounded-lg overflow-hidden
                                       bg-gray-100"
                            >

                                <img
                                    :src="product.images?.length
                                        ? `/storage/${product.images[0].image_path}`
                                        : '/placeholder.png'"
                                    :alt="product.name"
                                    class="w-full h-full
                                           object-cover"
                                />

                            </Link>


                            <!-- PRODUCT INFO -->

                            <div
                                class="flex-1 min-w-0"
                            >

                                <Link
                                    :href="route('products.show', {
                                        product: product.slug
                                    })"
                                    @click="closeDrawer"
                                >

                                    <h3
                                        class="text-sm font-semibold
                                               text-gray-800
                                               line-clamp-2
                                               hover:text-blue-600
                                               transition"
                                    >
                                        {{ product.name }}
                                    </h3>

                                </Link>


                                <p
                                    class="mt-2 text-sm
                                           font-semibold
                                           text-blue-600"
                                >

                                    KES
                                    {{ formatPrice(product.sale_price) }}

                                </p>

                            </div>


                            <!-- DELETE -->

                            <button
                                @click="removeProduct(product.id)"
                                class="self-start
                                       w-8 h-8
                                       flex items-center
                                       justify-center
                                       rounded-full
                                       text-gray-400
                                       hover:bg-red-50
                                       hover:text-red-500
                                       transition"
                                title="Remove from wishlist"
                            >

                                <Trash2
                                    class="w-4 h-4"
                                />

                            </button>

                        </div>


                        <!-- CART ACTION -->

                        <div class="mt-4">


                            <!-- ADD TO CART -->

                            <button
                                v-if="!product.cartItem"
                                @click="handleAddToCart(product)"
                                :disabled="cartLoading === product.id"
                                class="w-full flex items-center
                                       justify-center gap-2
                                       bg-blue-600
                                       hover:bg-blue-700
                                       disabled:opacity-60
                                       text-white
                                       py-2 rounded-lg
                                       text-sm font-medium
                                       transition"
                            >

                                <Loader2
                                    v-if="cartLoading === product.id"
                                    class="w-4 h-4 animate-spin"
                                />

                                <ShoppingCart
                                    v-else
                                    class="w-4 h-4"
                                />

                                {{
                                    cartLoading === product.id
                                        ? 'Adding...'
                                        : 'Add to Cart'
                                }}

                            </button>


                            <!-- CART QUANTITY -->

                            <div
                                v-else
                                class="flex items-center
                                       justify-between
                                       border border-gray-200
                                       rounded-lg
                                       overflow-hidden"
                            >

                                <button
                                    @click="handleDecrease(product)"
                                    :disabled="cartLoading === product.id"
                                    class="w-10 h-9
                                           flex items-center
                                           justify-center
                                           bg-blue-600
                                           hover:bg-blue-700
                                           text-white
                                           disabled:opacity-60"
                                >

                                    <Minus
                                        class="w-4 h-4"
                                    />

                                </button>


                                <div
                                    class="flex-1 h-9
                                           flex items-center
                                           justify-center
                                           text-sm font-semibold"
                                >

                                    <Loader2
                                        v-if="cartLoading === product.id"
                                        class="w-4 h-4
                                               animate-spin
                                               text-blue-600"
                                    />

                                    <span v-else>
                                        {{ product.cartItem.quantity }}
                                    </span>

                                </div>


                                <button
                                    @click="handleIncrease(product)"
                                    :disabled="cartLoading === product.id"
                                    class="w-10 h-9
                                           flex items-center
                                           justify-center
                                           bg-blue-600
                                           hover:bg-blue-700
                                           text-white
                                           disabled:opacity-60"
                                >

                                    <Plus
                                        class="w-4 h-4"
                                    />

                                </button>

                            </div>

                        </div>

                    </div>

                </div>

            </div>


            <!-- FOOTER -->

            <div
                v-if="products.length > 0"
                class="border-t border-gray-100
                       p-5"
            >

                <Link
                    href="/productListing"
                    @click="closeDrawer"
                    class="w-full flex items-center
                           justify-center gap-2
                           bg-blue-600
                           hover:bg-blue-700
                           text-white py-3
                           rounded-xl
                           font-medium
                           transition"
                >

                    Continue Shopping

                    <ArrowRight
                        class="w-4 h-4"
                    />

                </Link>

            </div>

        </aside>

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


.slide-enter-active,
.slide-leave-active {
    transition: transform 0.3s ease;
}

.slide-enter-from,
.slide-leave-to {
    transform: translateX(100%);
}

</style>