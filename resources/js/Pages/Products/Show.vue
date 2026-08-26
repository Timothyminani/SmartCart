
<script setup>
import { computed, onBeforeUnmount, onMounted, ref } from 'vue'
import { useHead } from '@vueuse/head'
import { Link, useForm } from '@inertiajs/vue3'

import AppLayout from '@/Layouts/AppLayout.vue'
import { useCart } from '@/composables/useCart'
import { useWishlist } from '@/composables/useWishlist'

import {
    Heart,
    ShoppingCart,
    Plus,
    Minus,
    Loader2,
    Truck,
    ShieldCheck,
    Package,
    Star,
    Tag,
} from 'lucide-vue-next'

import { Swiper, SwiperSlide } from 'swiper/vue'
import { Navigation, Pagination } from 'swiper/modules'

import 'swiper/css'
import 'swiper/css/navigation'
import 'swiper/css/pagination'


/*
|--------------------------------------------------------------------------
| PROPS
|--------------------------------------------------------------------------
*/

const props = defineProps({
    product: {
        type: Object,
        required: true,
    },

    relatedProducts: {
        type: Array,
        default: () => [],
    },

    // Optional authenticated user.
    // If your AppLayout already exposes authentication differently,
    // you can remove this prop and adjust the form visibility.
    auth: {
        type: Object,
        default: () => ({
            user: null,
        }),
    },
})


/*
|--------------------------------------------------------------------------
| STATE
|--------------------------------------------------------------------------
*/

const loading = ref(false)
const wishlistLoading = ref(false)

const cartItem = ref(null)

const activeImage = ref(
    props.product.images?.[0]?.image_path || null
)

const activeTab = ref('description')

/*
|--------------------------------------------------------------------------
| REVIEW STATE
|--------------------------------------------------------------------------
*/

const selectedRating = ref(0)

const reviewForm = useForm({
    product_id: props.product.id,
    rating: 0,
    review: '',
})


/*
|--------------------------------------------------------------------------
| CART
|--------------------------------------------------------------------------
*/

const {
    addToCart,
    increaseQty,
    decreaseQty,
    getCartItem,
    removeItem,
} = useCart()


/*
|--------------------------------------------------------------------------
| WISHLIST
|--------------------------------------------------------------------------
*/

const {
    isInWishlist,
    addToWishlist,
    removeFromWishlist,
} = useWishlist()


const wishlistActive = computed(() => {
    return isInWishlist(props.product.id)
})


const handleWishlist = async () => {

    if (wishlistLoading.value) {
        return
    }

    wishlistLoading.value = true

    try {

        if (wishlistActive.value) {

            removeFromWishlist(props.product.id)

        } else {

            addToWishlist(props.product.id)

        }

    } finally {

        wishlistLoading.value = false
    }
}


/*
|--------------------------------------------------------------------------
| IMAGE
|--------------------------------------------------------------------------
*/

const setActiveImage = (image) => {
    activeImage.value = image.image_path
}


/*
|--------------------------------------------------------------------------
| PRICE
|--------------------------------------------------------------------------
*/

const currentPrice = computed(() => {

    return Number(
        props.product.sale_price ||
        props.product.price ||
        0
    )

})


const originalPrice = computed(() => {

    return Number(
        props.product.price || 0
    )

})


const hasDiscount = computed(() => {

    return (
        originalPrice.value > 0 &&
        currentPrice.value < originalPrice.value
    )

})


const discountPercentage = computed(() => {

    if (!hasDiscount.value) {
        return 0
    }

    return Math.round(
        (
            (
                originalPrice.value -
                currentPrice.value
            ) /
            originalPrice.value
        ) * 100
    )

})


/*
|--------------------------------------------------------------------------
| STOCK
|--------------------------------------------------------------------------
*/

const stockQuantity = computed(() => {

    return Number(
        props.product.stock_quantity || 0
    )

})


const isInStock = computed(() => {

    return stockQuantity.value > 0
})


/*
|--------------------------------------------------------------------------
| REVIEWS
|--------------------------------------------------------------------------
*/

const reviews = computed(() => {

    return props.product.reviews || []

})


const averageRating = computed(() => {

    if (!reviews.value.length) {
        return 0
    }

    const total = reviews.value.reduce(
        (sum, review) =>
            sum + Number(review.rating || 0),
        0
    )

    return total / reviews.value.length

})


const formattedAverageRating = computed(() => {

    return averageRating.value
        ? averageRating.value.toFixed(1)
        : '0.0'

})


const ratingCount = (rating) => {

    return reviews.value.filter(
        review =>
            Number(review.rating) === rating
    ).length

}


const ratingPercentage = (rating) => {

    if (!reviews.value.length) {
        return 0
    }

    return (
        ratingCount(rating) /
        reviews.value.length
    ) * 100

}


const formatReviewDate = (date) => {

    if (!date) {
        return ''
    }

    return new Date(date).toLocaleDateString(
        'en-KE',
        {
            year: 'numeric',
            month: 'short',
            day: 'numeric',
        }
    )

}


/*
|--------------------------------------------------------------------------
| REVIEW FORM
|--------------------------------------------------------------------------
*/

const selectRating = (rating) => {

    selectedRating.value = rating

    reviewForm.rating = rating

}


const submitReview = () => {

    if (!selectedRating.value) {
        return
    }

    reviewForm.product_id = props.product.id
    reviewForm.rating = selectedRating.value

    console.log('Submitting review:', reviewForm.data())

    reviewForm.post(
        route('reviews.store', {
            product: props.product.id,
        }),
        {
            preserveScroll: true,

            onSuccess: () => {

                console.log('Review submitted successfully')

                selectedRating.value = 0

                reviewForm.reset(
                    'rating',
                    'review',
                    'product_id'
                )

            },

            onError: (errors) => {

                console.error(
                    'Review submission failed:',
                    errors
                )

                console.log(
                    'Form data:',
                    reviewForm.data()
                )
            },

            onFinish: () => {

                console.log(
                    'Review request finished'
                )
            },
        }
    )
}
/*
|--------------------------------------------------------------------------
| CART
|--------------------------------------------------------------------------
*/

const loadCartItem = async () => {

    try {

        cartItem.value =
            await getCartItem(
                props.product.id
            )

    } catch (error) {

        console.error(
            'Failed to load cart item:',
            error
        )

    }

}


const handleAdd = async () => {

    if (!isInStock.value || loading.value) {
        return
    }

    loading.value = true

    try {

        await addToCart(
            props.product.id
        )

        cartItem.value = {
            id: null,
            product_id: props.product.id,
            quantity: 1,
        }

    } catch (error) {

        console.error(
            'Failed to add product to cart:',
            error
        )

    } finally {

        loading.value = false

    }

}


const handleIncrease = async () => {

    if (
        !cartItem.value ||
        loading.value
    ) {
        return
    }

    loading.value = true

    try {

        await increaseQty(
            props.product.id
        )

        cartItem.value.quantity++

    } catch (error) {

        console.error(
            'Failed to increase quantity:',
            error
        )

    } finally {

        loading.value = false

    }

}


const handleDecrease = async () => {

    if (
        !cartItem.value ||
        loading.value
    ) {
        return
    }

    loading.value = true

    try {

        if (
            cartItem.value.quantity > 1
        ) {

            await decreaseQty(
                cartItem.value.id,
                cartItem.value.quantity
            )

            cartItem.value.quantity--

        } else {

            if (cartItem.value.id) {

                await removeItem(
                    cartItem.value.id
                )

            }

            cartItem.value = null

        }

    } catch (error) {

        console.error(
            'Failed to decrease quantity:',
            error
        )

    } finally {

        loading.value = false

    }

}


/*
|--------------------------------------------------------------------------
| LIFECYCLE
|--------------------------------------------------------------------------
*/

onMounted(async () => {

    await loadCartItem()

    window.addEventListener(
        'cartUpdated',
        loadCartItem
    )

})


onBeforeUnmount(() => {

    window.removeEventListener(
        'cartUpdated',
        loadCartItem
    )

})


/*
|--------------------------------------------------------------------------
| SEO
|--------------------------------------------------------------------------
*/

useHead({

    title:
        `${props.product.name} | Buy Online`,

    meta: [
        {
            name: 'description',
            content:
                props.product.description
                    ?.slice(0, 150),
        },
    ],

})


useHead({

    script: [

        {
            type: 'application/ld+json',

            children: JSON.stringify({

                '@context':
                    'https://schema.org/',

                '@type':
                    'Product',

                name:
                    props.product.name,

                image:
                    props.product.images?.map(
                        image =>
                            `/storage/${image.image_path}`
                    ),

                description:
                    props.product.description,

                brand: {
                    '@type': 'Brand',
                    name:
                        props.product.brand?.name,
                },

                aggregateRating:
                    reviews.value.length
                        ? {
                            '@type':
                                'AggregateRating',

                            ratingValue:
                                averageRating.value
                                    .toFixed(1),

                            reviewCount:
                                reviews.value.length,

                            bestRating: '5',

                            worstRating: '1',
                        }
                        : undefined,

                offers: {

                    '@type': 'Offer',

                    priceCurrency: 'KES',

                    price:
                        currentPrice.value,

                    availability:
                        isInStock.value
                            ? 'https://schema.org/InStock'
                            : 'https://schema.org/OutOfStock',

                },

            }),

        },

    ],

})
</script>


<template>

    <AppLayout>

        <!-- ========================================================= -->
        <!-- PRODUCT SECTION -->
        <!-- ========================================================= -->

        <section
            class="max-w-7xl mx-auto
                   px-4 sm:px-6 lg:px-8
                   py-8 lg:py-12"
        >

            <div
                class="grid lg:grid-cols-2
                       gap-8 lg:gap-14"
            >

                <!-- ================================================= -->
                <!-- IMAGES -->
                <!-- ================================================= -->

                <div>

                    <div
                        class="relative bg-white
                               rounded-2xl
                               border border-gray-100
                               overflow-hidden"
                    >

                        <img
                            v-if="activeImage"
                            :src="`/storage/${activeImage}`"
                            :alt="product.name"
                            class="w-full
                                   h-[400px]
                                   sm:h-[500px]
                                   object-cover"
                        />


                        <div
                            v-else
                            class="h-[400px]
                                   sm:h-[500px]
                                   bg-gray-100
                                   flex items-center
                                   justify-center"
                        >

                            <Package
                                class="w-14 h-14
                                       text-gray-300"
                            />

                        </div>


                        <!-- DISCOUNT -->

                        <span
                            v-if="hasDiscount"
                            class="absolute
                                   top-4 left-4
                                   bg-red-500
                                   text-white
                                   text-xs
                                   font-semibold
                                   px-3 py-1.5
                                   rounded-full"
                        >
                            -{{ discountPercentage }}%
                        </span>


                        <!-- WISHLIST -->

                        <button
                            @click="handleWishlist"
                            :disabled="wishlistLoading"
                            class="absolute
                                   top-4 right-4
                                   w-11 h-11
                                   rounded-full
                                   bg-white/95
                                   shadow
                                   flex items-center
                                   justify-center
                                   hover:scale-105
                                   transition"
                        >

                            <Loader2
                                v-if="wishlistLoading"
                                class="w-5 h-5
                                       animate-spin
                                       text-gray-500"
                            />

                            <Heart
                                v-else
                                class="w-5 h-5"
                                :class="
                                    wishlistActive
                                        ? 'text-red-500 fill-red-500'
                                        : 'text-gray-600'
                                "
                            />

                        </button>

                    </div>


                    <!-- THUMBNAILS -->

                    <div
                        v-if="product.images?.length"
                        class="flex gap-3
                               mt-4
                               overflow-x-auto
                               pb-2"
                    >

                        <button
                            v-for="image in product.images"
                            :key="image.id"
                            @click="setActiveImage(image)"
                            class="shrink-0
                                   rounded-xl
                                   overflow-hidden
                                   border-2
                                   transition"
                            :class="
                                activeImage === image.image_path
                                    ? 'border-blue-600'
                                    : 'border-transparent hover:border-gray-300'
                            "
                        >

                            <img
                                :src="`/storage/${image.image_path}`"
                                :alt="product.name"
                                class="w-20 h-20
                                       object-cover"
                            />

                        </button>

                    </div>

                </div>


                <!-- ================================================= -->
                <!-- PRODUCT INFORMATION -->
                <!-- ================================================= -->

                <div
                    class="flex flex-col
                           justify-center"
                >

                    <!-- BRAND / CATEGORY -->

                    <div
                        class="flex flex-wrap
                               items-center gap-2
                               mb-4"
                    >

                        <span
                            v-if="product.brand?.name"
                            class="inline-flex
                                   items-center gap-1.5
                                   text-xs
                                   font-medium
                                   text-gray-600
                                   bg-gray-100
                                   px-3 py-1.5
                                   rounded-full"
                        >

                            <Tag class="w-3.5 h-3.5" />

                            {{ product.brand.name }}

                        </span>


                        <span
                            v-if="product.category?.name"
                            class="text-xs
                                   font-medium
                                   text-gray-600
                                   bg-gray-100
                                   px-3 py-1.5
                                   rounded-full"
                        >
                            {{ product.category.name }}
                        </span>

                    </div>


                    <!-- TITLE -->

                    <h1
                        class="text-2xl sm:text-3xl
                               lg:text-4xl
                               font-bold
                               text-gray-900
                               leading-tight"
                    >
                        {{ product.name }}
                    </h1>


                    <!-- RATING -->

                    <div
                        class="flex items-center
                               gap-2 mt-4"
                    >

                        <div class="flex gap-0.5">

                            <Star
                                v-for="star in 5"
                                :key="star"
                                class="w-4 h-4"
                                :class="
                                    star <= Math.round(averageRating)
                                        ? 'text-yellow-400 fill-yellow-400'
                                        : 'text-gray-300'
                                "
                            />

                        </div>


                        <span
                            v-if="reviews.length"
                            class="text-sm
                                   text-gray-600"
                        >
                            {{ formattedAverageRating }}
                            ·
                            {{ reviews.length }}
                            {{
                                reviews.length === 1
                                    ? 'review'
                                    : 'reviews'
                            }}
                        </span>


                        <span
                            v-else
                            class="text-sm
                                   text-gray-400"
                        >
                            No reviews yet
                        </span>

                    </div>


                    <!-- PRICE -->

                    <div
                        class="flex flex-wrap
                               items-center gap-3
                               mt-6"
                    >

                        <span
                            class="text-3xl
                                   font-bold
                                   text-blue-600"
                        >
                            KES
                            {{ currentPrice.toLocaleString() }}
                        </span>


                        <span
                            v-if="hasDiscount"
                            class="text-sm
                                   text-gray-400
                                   line-through"
                        >
                            KES
                            {{ originalPrice.toLocaleString() }}
                        </span>


                        <span
                            v-if="hasDiscount"
                            class="text-xs
                                   font-semibold
                                   text-green-700
                                   bg-green-100
                                   px-2 py-1
                                   rounded"
                        >
                            Save {{ discountPercentage }}%
                        </span>

                    </div>


                    <!-- STOCK -->

                    <div class="mt-5">

                        <div
                            v-if="isInStock"
                            class="inline-flex
                                   items-center gap-2
                                   text-sm
                                   text-green-600
                                   font-medium"
                        >

                            <span
                                class="w-2 h-2
                                       rounded-full
                                       bg-green-500"
                            ></span>

                            In stock

                            <span
                                v-if="stockQuantity"
                                class="text-gray-400
                                       font-normal"
                            >
                                ({{ stockQuantity }} available)
                            </span>

                        </div>


                        <div
                            v-else
                            class="inline-flex
                                   items-center gap-2
                                   text-sm
                                   text-red-500
                                   font-medium"
                        >

                            <span
                                class="w-2 h-2
                                       rounded-full
                                       bg-red-500"
                            ></span>

                            Out of stock

                        </div>

                    </div>


                    <!-- CART + WISHLIST -->

                    <div
                        class="flex items-center
                               gap-3 mt-6"
                    >

                        <div class="flex-1">

                            <button
                                v-if="!cartItem"
                                @click="handleAdd"
                                :disabled="
                                    !isInStock ||
                                    loading
                                "
                                class="w-full
                                       flex items-center
                                       justify-center
                                       gap-2
                                       bg-blue-600
                                       hover:bg-blue-700
                                       disabled:bg-gray-300
                                       disabled:cursor-not-allowed
                                       text-white
                                       py-3.5
                                       rounded-xl
                                       font-semibold
                                       transition"
                            >

                                <Loader2
                                    v-if="loading"
                                    class="w-5 h-5
                                           animate-spin"
                                />

                                <ShoppingCart
                                    v-else
                                    class="w-5 h-5"
                                />

                                {{
                                    loading
                                        ? 'Adding...'
                                        : isInStock
                                            ? 'Add to Cart'
                                            : 'Out of Stock'
                                }}

                            </button>


                            <div
                                v-else
                                class="w-full
                                       flex items-center
                                       justify-between
                                       border
                                       border-gray-200
                                       rounded-xl
                                       overflow-hidden"
                            >

                                <button
                                    @click="handleDecrease"
                                    :disabled="loading"
                                    class="w-14 h-12
                                           flex items-center
                                           justify-center
                                           bg-blue-600
                                           hover:bg-blue-700
                                           text-white
                                           disabled:opacity-50"
                                >

                                    <Minus
                                        class="w-5 h-5"
                                    />

                                </button>


                                <div
                                    class="flex-1 h-12
                                           flex items-center
                                           justify-center
                                           font-semibold"
                                >

                                    <Loader2
                                        v-if="loading"
                                        class="w-5 h-5
                                               animate-spin
                                               text-blue-600"
                                    />

                                    <span v-else>
                                        {{ cartItem.quantity }}
                                    </span>

                                </div>


                                <button
                                    @click="handleIncrease"
                                    :disabled="loading"
                                    class="w-14 h-12
                                           flex items-center
                                           justify-center
                                           bg-blue-600
                                           hover:bg-blue-700
                                           text-white
                                           disabled:opacity-50"
                                >

                                    <Plus
                                        class="w-5 h-5"
                                    />

                                </button>

                            </div>

                        </div>


                        <button
                            @click="handleWishlist"
                            class="w-14 h-12
                                   shrink-0
                                   rounded-xl
                                   border
                                   flex items-center
                                   justify-center
                                   transition"
                            :class="
                                wishlistActive
                                    ? 'border-red-200 bg-red-50'
                                    : 'border-gray-200 hover:bg-gray-50'
                            "
                        >

                            <Heart
                                class="w-5 h-5"
                                :class="
                                    wishlistActive
                                        ? 'text-red-500 fill-red-500'
                                        : 'text-gray-600'
                                "
                            />

                        </button>

                    </div>


                    <!-- DELIVERY / WARRANTY -->

                    <div
                        class="grid sm:grid-cols-2
                               gap-4 mt-8
                               pt-6
                               border-t
                               border-gray-100"
                    >

                        <div
                            class="flex items-start gap-3"
                        >

                            <div
                                class="w-10 h-10
                                       rounded-lg
                                       bg-blue-50
                                       flex items-center
                                       justify-center
                                       shrink-0"
                            >

                                <Truck
                                    class="w-5 h-5
                                           text-blue-600"
                                />

                            </div>


                            <div>

                                <p
                                    class="text-sm
                                           font-semibold
                                           text-gray-800"
                                >
                                    Fast Delivery
                                </p>

                                <p
                                    class="text-xs
                                           text-gray-500
                                           mt-1"
                                >
                                    Estimated 2–4
                                    business days
                                </p>

                            </div>

                        </div>


                        <div
                            class="flex items-start gap-3"
                        >

                            <div
                                class="w-10 h-10
                                       rounded-lg
                                       bg-green-50
                                       flex items-center
                                       justify-center
                                       shrink-0"
                            >

                                <ShieldCheck
                                    class="w-5 h-5
                                           text-green-600"
                                />

                            </div>


                            <div>

                                <p
                                    class="text-sm
                                           font-semibold
                                           text-gray-800"
                                >
                                    Warranty
                                </p>

                                <p
                                    class="text-xs
                                           text-gray-500
                                           mt-1"
                                >
                                    6 months warranty included
                                </p>

                            </div>

                        </div>

                    </div>

                </div>

            </div>


            <!-- ===================================================== -->
            <!-- DETAILS TABS -->
            <!-- ===================================================== -->

            <div
                class="mt-16
                       border-t
                       border-gray-100"
            >

                <!-- TAB NAVIGATION -->

                <div
                    class="flex
                           overflow-x-auto
                           border-b"
                >

                    <button
                        @click="activeTab = 'description'"
                        class="px-6 py-4
                               text-sm
                               font-medium
                               whitespace-nowrap
                               border-b-2
                               transition"
                        :class="
                            activeTab === 'description'
                                ? 'border-blue-600 text-blue-600'
                                : 'border-transparent text-gray-500 hover:text-gray-800'
                        "
                    >
                        Description
                    </button>


                    <button
                        @click="activeTab = 'specs'"
                        class="px-6 py-4
                               text-sm
                               font-medium
                               whitespace-nowrap
                               border-b-2
                               transition"
                        :class="
                            activeTab === 'specs'
                                ? 'border-blue-600 text-blue-600'
                                : 'border-transparent text-gray-500 hover:text-gray-800'
                        "
                    >
                        Specifications
                    </button>


                    <button
                        @click="activeTab = 'reviews'"
                        class="px-6 py-4
                               text-sm
                               font-medium
                               whitespace-nowrap
                               border-b-2
                               transition"
                        :class="
                            activeTab === 'reviews'
                                ? 'border-blue-600 text-blue-600'
                                : 'border-transparent text-gray-500 hover:text-gray-800'
                        "
                    >
                        Reviews

                        <span
                            v-if="reviews.length"
                            class="ml-1 text-xs
                                   text-gray-400"
                        >
                            ({{ reviews.length }})
                        </span>

                    </button>

                </div>


                <!-- TAB CONTENT -->

                <div
                    class="max-w-4xl
                           mx-auto
                           px-4 py-10"
                >

                    <!-- ================================================= -->
                    <!-- DESCRIPTION -->
                    <!-- ================================================= -->

                    <div
                        v-if="activeTab === 'description'"
                    >

                        <h2
                            class="text-lg
                                   font-semibold
                                   text-gray-900
                                   mb-4"
                        >
                            Product Description
                        </h2>

                        <p
                            class="text-sm
                                   text-gray-600
                                   leading-7
                                   whitespace-pre-line"
                        >
                            {{
                                product.description ||
                                'No description available.'
                            }}
                        </p>

                    </div>


                    <!-- ================================================= -->
                    <!-- SPECIFICATIONS -->
                    <!-- ================================================= -->

                    <div
                        v-else-if="activeTab === 'specs'"
                    >

                        <div
                            v-if="product.attributes?.length"
                            class="border
                                   rounded-xl
                                   divide-y"
                        >

                            <div
                                v-for="attr in product.attributes"
                                :key="attr.id"
                                class="flex flex-col
                                       sm:flex-row
                                       sm:justify-between
                                       gap-2
                                       py-4 px-5
                                       text-sm"
                            >

                                <span
                                    class="text-gray-500"
                                >
                                    {{ attr.attribute_name }}
                                </span>

                                <span
                                    class="font-medium
                                           text-gray-900"
                                >
                                    {{ attr.attribute_value }}
                                </span>

                            </div>

                        </div>


                        <div
                            v-else
                            class="py-12
                                   text-center"
                        >

                            <Package
                                class="w-8 h-8
                                       mx-auto
                                       text-gray-300"
                            />

                            <p
                                class="text-sm
                                       text-gray-500
                                       mt-3"
                            >
                                No specifications available.
                            </p>

                        </div>

                    </div>


                    <!-- ================================================= -->
                    <!-- REVIEWS -->
                    <!-- ================================================= -->

                    <div
                        v-else-if="activeTab === 'reviews'"
                        class="space-y-8"
                    >

                        <!-- ================================================= -->
                        <!-- WRITE REVIEW -->
                        <!-- ================================================= -->

                        <div
                            class="border
                                   border-gray-100
                                   rounded-2xl
                                   p-6
                                   bg-white"
                        >

                            <div class="mb-6">

                                <h2
                                    class="text-xl
                                           font-bold
                                           text-gray-900"
                                >
                                    Write a Review
                                </h2>

                                <p
                                    class="text-sm
                                           text-gray-500
                                           mt-1"
                                >
                                    Share your experience with this product.
                                </p>

                            </div>


                            <!-- RATING -->

                            <div class="mb-6">

                                <label
                                    class="block
                                           text-sm
                                           font-medium
                                           text-gray-700
                                           mb-3"
                                >
                                    Your Rating
                                </label>


                                <div
                                    class="flex
                                           items-center
                                           gap-2"
                                >

                                    <button
                                        v-for="star in 5"
                                        :key="star"
                                        type="button"
                                        @click="selectRating(star)"
                                        class="transition
                                               hover:scale-110
                                               focus:outline-none"
                                        :aria-label="`Rate ${star} out of 5 stars`"
                                    >

                                        <Star
                                            class="w-8 h-8"
                                            :class="
                                                star <= selectedRating
                                                    ? 'text-yellow-400 fill-yellow-400'
                                                    : 'text-gray-300 hover:text-yellow-300'
                                            "
                                        />

                                    </button>


                                    <span
                                        v-if="selectedRating"
                                        class="ml-2
                                               text-sm
                                               font-medium
                                               text-gray-600"
                                    >
                                        {{ selectedRating }}/5
                                    </span>

                                </div>


                                <p
                                    v-if="reviewForm.errors.rating"
                                    class="text-sm
                                           text-red-500
                                           mt-2"
                                >
                                    {{ reviewForm.errors.rating }}
                                </p>

                            </div>


                            <!-- REVIEW TEXT -->

                            <div class="mb-6">

                                <label
                                    for="review"
                                    class="block
                                           text-sm
                                           font-medium
                                           text-gray-700
                                           mb-2"
                                >
                                    Your Review
                                </label>


                                <textarea
                                    id="review"
                                    v-model="reviewForm.review"
                                    rows="5"
                                    maxlength="1000"
                                    placeholder="Tell other customers what you think about this product..."
                                    class="w-full
                                           rounded-xl
                                           border
                                           border-gray-200
                                           px-4 py-3
                                           text-sm
                                           text-gray-800
                                           placeholder-gray-400
                                           focus:border-blue-500
                                           focus:ring-2
                                           focus:ring-blue-100
                                           resize-none"
                                ></textarea>


                                <div
                                    class="flex
                                           items-center
                                           justify-between
                                           mt-2"
                                >

                                    <p
                                        v-if="reviewForm.errors.review"
                                        class="text-sm
                                               text-red-500"
                                    >
                                        {{ reviewForm.errors.review }}
                                    </p>

                                    <span
                                        class="text-xs
                                               text-gray-400
                                               ml-auto"
                                    >
                                        {{ reviewForm.review.length }}/1000
                                    </span>

                                </div>

                            </div>


                            <!-- SUBMIT -->

                            <button
                                type="button"
                                @click="submitReview"
                                :disabled="
                                    reviewForm.processing ||
                                    !selectedRating
                                "
                                class="w-full
                                       sm:w-auto
                                       inline-flex
                                       items-center
                                       justify-center
                                       gap-2
                                       bg-blue-600
                                       hover:bg-blue-700
                                       disabled:bg-gray-300
                                       disabled:cursor-not-allowed
                                       text-white
                                       px-6 py-3
                                       rounded-xl
                                       font-semibold
                                       text-sm
                                       transition"
                            >

                                <Loader2
                                    v-if="reviewForm.processing"
                                    class="w-5 h-5
                                           animate-spin"
                                />

                                <Star
                                    v-else
                                    class="w-5 h-5"
                                />

                                {{
                                    reviewForm.processing
                                        ? 'Submitting...'
                                        : 'Submit Review'
                                }}

                            </button>


                            <p
                                v-if="!selectedRating"
                                class="text-xs
                                       text-gray-400
                                       mt-3"
                            >
                                Please select a rating before submitting.
                            </p>

                        </div>


                        <!-- ================================================= -->
                        <!-- REVIEW SUMMARY -->
                        <!-- ================================================= -->

                        <div
                            v-if="reviews.length"
                            class="grid md:grid-cols-2
                                   gap-8
                                   border
                                   border-gray-100
                                   rounded-2xl
                                   p-6"
                        >

                            <!-- AVERAGE -->

                            <div
                                class="text-center
                                       flex flex-col
                                       items-center
                                       justify-center"
                            >

                                <p
                                    class="text-5xl
                                           font-bold
                                           text-gray-900"
                                >
                                    {{ formattedAverageRating }}
                                </p>


                                <div
                                    class="flex
                                           justify-center
                                           gap-1 mt-2"
                                >

                                    <Star
                                        v-for="star in 5"
                                        :key="star"
                                        class="w-5 h-5"
                                        :class="
                                            star <= Math.round(averageRating)
                                                ? 'text-yellow-400 fill-yellow-400'
                                                : 'text-gray-300'
                                        "
                                    />

                                </div>


                                <p
                                    class="text-xs
                                           text-gray-500
                                           mt-2"
                                >
                                    Based on
                                    {{ reviews.length }}
                                    {{
                                        reviews.length === 1
                                            ? 'review'
                                            : 'reviews'
                                    }}
                                </p>

                            </div>


                            <!-- BREAKDOWN -->

                            <div
                                class="space-y-3
                                       flex flex-col
                                       justify-center"
                            >

                                <div
                                    v-for="rating in [5,4,3,2,1]"
                                    :key="rating"
                                    class="flex
                                           items-center
                                           gap-3
                                           text-sm"
                                >

                                    <span
                                        class="w-12
                                               text-gray-500"
                                    >
                                        {{ rating }} star
                                    </span>


                                    <div
                                        class="flex-1
                                               h-2
                                               bg-gray-200
                                               rounded-full
                                               overflow-hidden"
                                    >

                                        <div
                                            class="h-full
                                                   bg-yellow-400
                                                   rounded-full"
                                            :style="{
                                                width:
                                                    `${ratingPercentage(rating)}%`
                                            }"
                                        ></div>

                                    </div>


                                    <span
                                        class="w-6
                                               text-right
                                               text-gray-500"
                                    >
                                        {{ ratingCount(rating) }}
                                    </span>

                                </div>

                            </div>

                        </div>


                        <!-- ================================================= -->
                        <!-- NO REVIEWS -->
                        <!-- ================================================= -->

                        <div
                            v-if="!reviews.length"
                            class="py-12
                                   text-center
                                   border
                                   border-gray-100
                                   rounded-2xl"
                        >

                            <div
                                class="w-14 h-14
                                       mx-auto
                                       rounded-full
                                       bg-yellow-50
                                       flex items-center
                                       justify-center"
                            >

                                <Star
                                    class="w-7 h-7
                                           text-yellow-400"
                                />

                            </div>


                            <h3
                                class="text-lg
                                       font-semibold
                                       text-gray-900
                                       mt-4"
                            >
                                No reviews yet
                            </h3>


                            <p
                                class="text-sm
                                       text-gray-500
                                       mt-1"
                            >
                                Be the first customer
                                to review this product.
                            </p>

                        </div>


                        <!-- ================================================= -->
                        <!-- REVIEWS LIST -->
                        <!-- ================================================= -->

                        <div
                            v-if="reviews.length"
                            class="space-y-4"
                        >

                            <div
                                class="flex items-center
                                       justify-between"
                            >

                                <h3
                                    class="text-lg
                                           font-semibold
                                           text-gray-900"
                                >
                                    Customer Reviews
                                </h3>

                            </div>


                            <div
                                v-for="review in reviews"
                                :key="review.id"
                                class="border
                                       border-gray-100
                                       rounded-xl
                                       p-5"
                            >

                                <div
                                    class="flex items-start
                                           justify-between
                                           gap-4"
                                >

                                    <div>

                                        <p
                                            class="font-semibold
                                                   text-gray-800"
                                        >
                                            {{
                                                review.user?.name ||
                                                'Customer'
                                            }}
                                        </p>


                                        <div
                                            class="flex
                                                   gap-0.5
                                                   mt-1"
                                        >

                                            <Star
                                                v-for="star in 5"
                                                :key="star"
                                                class="w-4 h-4"
                                                :class="
                                                    star <= Number(review.rating)
                                                        ? 'text-yellow-400 fill-yellow-400'
                                                        : 'text-gray-300'
                                                "
                                            />

                                        </div>

                                    </div>


                                    <span
                                        class="text-xs
                                               text-gray-400"
                                    >
                                        {{
                                            formatReviewDate(
                                                review.created_at
                                            )
                                        }}
                                    </span>

                                </div>


                                <p
                                    v-if="review.review"
                                    class="text-sm
                                           text-gray-600
                                           leading-6
                                           mt-4"
                                >
                                    {{ review.review }}
                                </p>

                            </div>

                        </div>

                    </div>

                </div>

            </div>

        </section>


        <!-- ========================================================= -->
        <!-- RELATED PRODUCTS -->
        <!-- ========================================================= -->

        <section
            v-if="relatedProducts.length"
            class="max-w-7xl
                   mx-auto
                   px-4 sm:px-6 lg:px-8
                   pb-16"
        >

            <div class="mb-8">

                <h2
                    class="text-2xl
                           font-bold
                           text-gray-900"
                >
                    You May Also Like
                </h2>

                <p
                    class="text-sm
                           text-gray-500
                           mt-1"
                >
                    Similar products you might like
                </p>

            </div>


            <Swiper
                :modules="[Navigation, Pagination]"
                navigation
                pagination
                :slides-per-view="2"
                :space-between="16"
                :breakpoints="{
                    640: {
                        slidesPerView: 2
                    },
                    768: {
                        slidesPerView: 3
                    },
                    1024: {
                        slidesPerView: 4
                    }
                }"
                class="w-full"
            >

                <SwiperSlide
                    v-for="item in relatedProducts"
                    :key="item.id"
                >

                    <Link
                        :href="`/products/${item.slug}`"
                        class="group block
                               bg-white
                               border
                               border-gray-100
                               rounded-xl
                               overflow-hidden
                               hover:shadow-md
                               transition"
                    >

                        <div
                            class="overflow-hidden
                                   bg-gray-50"
                        >

                            <img
                                v-if="item.images?.[0]"
                                :src="`/storage/${item.images[0].image_path}`"
                                :alt="item.name"
                                class="w-full h-48
                                       object-cover
                                       group-hover:scale-105
                                       transition
                                       duration-300"
                            />

                            <div
                                v-else
                                class="w-full h-48
                                       bg-gray-100
                                       flex items-center
                                       justify-center"
                            >

                                <Package
                                    class="w-8 h-8
                                           text-gray-300"
                                />

                            </div>

                        </div>


                        <div
                            class="p-4"
                        >

                            <p
                                v-if="item.brand?.name"
                                class="text-[10px]
                                       text-gray-400
                                       uppercase
                                       tracking-wide"
                            >
                                {{ item.brand.name }}
                            </p>


                            <h3
                                class="text-sm
                                       font-medium
                                       text-gray-800
                                       line-clamp-2
                                       min-h-[40px]
                                       mt-1"
                            >
                                {{ item.name }}
                            </h3>


                            <div
                                class="flex items-center
                                       gap-2 mt-3"
                            >

                                <span
                                    class="text-sm
                                           font-bold
                                           text-blue-600"
                                >
                                    KES
                                    {{
                                        Number(
                                            item.sale_price ||
                                            item.price ||
                                            0
                                        ).toLocaleString()
                                    }}
                                </span>


                                <span
                                    v-if="
                                        item.sale_price &&
                                        item.price &&
                                        Number(item.sale_price) <
                                        Number(item.price)
                                    "
                                    class="text-xs
                                           text-gray-400
                                           line-through"
                                >
                                    KES
                                    {{
                                        Number(
                                            item.price
                                        ).toLocaleString()
                                    }}
                                </span>

                            </div>

                        </div>

                    </Link>

                </SwiperSlide>

            </Swiper>

        </section>

    </AppLayout>

</template>


<style scoped>

.swiper {
    padding-bottom: 40px;
}

.swiper-pagination {
    bottom: 0 !important;
}

</style>

