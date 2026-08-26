<script setup>
import {
    ref,
    computed,
    onMounted,
    onBeforeUnmount,
    nextTick,
} from 'vue'

import {
    ShoppingCart,
    Heart,
    Phone,
    Menu,
} from 'lucide-vue-next'

import { usePage, router } from '@inertiajs/vue3'

import ProductSearch from '@/Components/ProductSearch.vue'
import AccountDropdown from '@/Components/AccountDropdown.vue'

import { useCart } from '@/composables/useCart'
import { useWishlist } from '@/composables/useWishlist'


/*
|--------------------------------------------------------------------------
| EMITS
|--------------------------------------------------------------------------
*/

const emit = defineEmits([
    'open-cart',
    'open-wishlist',
    'open-sidebar',
    'height-change',
])


/*
|--------------------------------------------------------------------------
| PAGE
|--------------------------------------------------------------------------
*/

const page = usePage()


/*
|--------------------------------------------------------------------------
| HEADER STATE
|--------------------------------------------------------------------------
*/

const headerRef = ref(null)
const showTopBar = ref(true)

let resizeObserver = null


/*
|--------------------------------------------------------------------------
| ROUTE STATE
|--------------------------------------------------------------------------
*/

const hideBottomNavigation = computed(() => {

    const url = page.url

    return (
        url.startsWith('/productListing') ||
        url.startsWith('/products') ||
        url.startsWith('/checkout') ||
        url.startsWith('/order-success') ||
        url.startsWith('/orders') ||
        url.startsWith('/payment/') ||
        url.startsWith('/compare') ||
        url.startsWith('/ai-search')
    )
})


/*
|--------------------------------------------------------------------------
| WISHLIST
|--------------------------------------------------------------------------
*/

const {
    wishlistCount,
    loadWishlist,
} = useWishlist()


/*
|--------------------------------------------------------------------------
| CART
|--------------------------------------------------------------------------
*/

const {
    getCartCount,
} = useCart()

const cartCount = ref(0)
const cartAnimate = ref(false)


const updateCartCount = async () => {

    try {

        cartCount.value =
            await getCartCount()

        cartAnimate.value = true

        setTimeout(() => {
            cartAnimate.value = false
        }, 300)

    } catch (error) {

        console.error(
            'Failed to update cart count:',
            error
        )

    }
}


/*
|--------------------------------------------------------------------------
| EVENTS
|--------------------------------------------------------------------------
*/

const openCart = () => {
    emit('open-cart')
}


const openWishlist = () => {
    emit('open-wishlist')
}


const openSidebar = () => {
    emit('open-sidebar')
}


/*
|--------------------------------------------------------------------------
| NAVIGATION
|--------------------------------------------------------------------------
*/

const goHome = () => {
    router.visit(
        route('home')
    )
}


const goShop = () => {
    router.visit(
        route('products.index')
    )
}

const goAiShopping = () => {

    router.visit(
        route('home'),
        {
            onSuccess: () => {

                setTimeout(() => {

                    document
                        .getElementById('ai-shopping')
                        ?.scrollIntoView({
                            behavior: 'smooth',
                            block: 'center',
                        })

                }, 100)

            },
        }
    )
}


const goNewArrivals = () => {

    router.visit(
        route('products.index'),
        {
            data: {
                sort: 'newest',
            },
        }
    )
}


const goOrders = () => {

    router.visit('/orders')
}

/*
|--------------------------------------------------------------------------
| SCROLL
|--------------------------------------------------------------------------
*/

const handleScroll = () => {

    const shouldShow =
        window.scrollY <= 50

    if (
        showTopBar.value !== shouldShow
    ) {

        showTopBar.value =
            shouldShow

        nextTick(() => {
            updateHeaderHeight()
        })
    }
}


/*
|--------------------------------------------------------------------------
| HEADER HEIGHT
|--------------------------------------------------------------------------
*/

const updateHeaderHeight = () => {

    if (!headerRef.value) {
        return
    }

    emit(
        'height-change',
        headerRef.value.offsetHeight
    )
}


/*
|--------------------------------------------------------------------------
| LIFECYCLE
|--------------------------------------------------------------------------
*/

onMounted(async () => {

    /*
    |--------------------------------------------------------------------------
    | Wishlist
    |--------------------------------------------------------------------------
    */

    loadWishlist()


    /*
    |--------------------------------------------------------------------------
    | Cart
    |--------------------------------------------------------------------------
    */

    await updateCartCount()

    window.addEventListener(
        'cartUpdated',
        updateCartCount
    )


    /*
    |--------------------------------------------------------------------------
    | Scroll
    |--------------------------------------------------------------------------
    */

    window.addEventListener(
        'scroll',
        handleScroll
    )


    /*
    |--------------------------------------------------------------------------
    | Header Resize
    |--------------------------------------------------------------------------
    */

    await nextTick()

    updateHeaderHeight()

    if (
        typeof ResizeObserver !==
        'undefined'
    ) {

        resizeObserver =
            new ResizeObserver(() => {
                updateHeaderHeight()
            })

        if (headerRef.value) {

            resizeObserver.observe(
                headerRef.value
            )

        }
    }
})


onBeforeUnmount(() => {

    window.removeEventListener(
        'cartUpdated',
        updateCartCount
    )

    window.removeEventListener(
        'scroll',
        handleScroll
    )

    if (resizeObserver) {
        resizeObserver.disconnect()
    }
})
</script>


<template>

    <header
        ref="headerRef"
        class="fixed
               top-0 left-0
               w-full
               z-50
               bg-white"
    >

        <!-- ========================================================= -->
        <!-- TOP BAR -->
        <!-- ========================================================= -->

        <transition name="slide-up">

            <div
                v-if="showTopBar"
                class="hidden md:block
                       bg-blue-50
                       text-black
                       text-xs
                       "
            >

                <div
                    class="max-w-7xl
                           mx-auto
                           px-4 sm:px-6 lg:px-10
                           py-1
                           flex items-center
                           justify-between
                           gap-4"
                >

                    <!-- CONTACT -->

                    <div
                        class="flex items-center
                               gap-2
                               shrink-0"
                    >

                        <Phone
                            class="w-3 h-3"
                        />

                        <span
                            class="hidden sm:inline"
                        >
                            +254 712 345 678
                        </span>

                    </div>


                    <!-- OFFER -->

                    <div
                        class="flex
                               items-center
                               justify-center
                               text-[10px]
                               sm:text-[11px]
                               text-black
                               flex-1"
                    >

                        <span
                            class="hidden sm:inline-flex
                                   bg-blue-600
                                   text-white
                                   px-2
                                   py-0.5
                                   rounded
                                   mr-2
                                   text-[10px]"
                        >
                            NEW
                        </span>

                        <span
                            class="text-center"
                        >
                            Free delivery on orders above KES 2,000
                        </span>

                    </div>


                    <!-- DESKTOP LINKS -->

                    <div
                        class="hidden lg:flex
                               items-center
                               space-x-6
                               shrink-0"
                    >

                        <div
                            class="flex items-center
                                   space-x-4
                                   text-[11px]"
                        >

                            <a
                                href="#"
                                class="hover:text-black transition"
                            >
                                About
                            </a>

                            <a
                                href="#"
                                class="hover:text-black transition"
                            >
                                Terms
                            </a>

                            <a
                                href="#"
                                class="hover:text-black transition"
                            >
                                Privacy
                            </a>

                            <a
                                href="#"
                                class="hover:text-black transition"
                            >
                                FAQs
                            </a>

                        </div>


                        <div
                            class="h-3
                                   w-px
                                   bg-gray-300"
                        ></div>


                        <!-- SOCIALS -->

                        <div
                            class="flex items-center
                                   space-x-4"
                        >

                            <!-- WhatsApp -->

                            <a
                                href="#"
                                class="hover:text-blue-400 transition"
                                title="WhatsApp"
                            >

                                <svg
                                    class="w-4 h-4 fill-current"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        d="M20.52 3.48A11.91 11.91 0 0012 0C5.37 0 .04 5.33.04 11.96c0 2.11.55 4.16 1.6 5.96L0 24l6.24-1.63a11.96 11.96 0 005.76 1.47h.01c6.63 0 11.96-5.33 11.96-11.96a11.9 11.9 0 00-3.45-8.4zM12 21.5a9.4 9.4 0 01-4.8-1.3l-.34-.2-3.7.97.99-3.61-.22-.37A9.4 9.4 0 1121.4 12 9.4 9.4 0 0112 21.5zm5.2-7.1c-.28-.14-1.66-.82-1.92-.91-.26-.1-.45-.14-.64.14-.19.28-.73.91-.9 1.1-.17.19-.33.21-.61.07-.28-.14-1.18-.43-2.25-1.37-.83-.74-1.39-1.66-1.55-1.94-.16-.28-.02-.43.12-.57.13-.13.28-.33.42-.5.14-.17.19-.28.28-.47.09-.19.05-.36-.02-.5-.07-.14-.64-1.55-.88-2.12-.23-.56-.47-.49-.64-.5h-.55c-.19 0-.5.07-.76.36-.26.28-1 1-.99 2.44.02 1.44 1.04 2.83 1.18 3.02.14.19 2.04 3.11 4.95 4.36.69.3 1.23.48 1.65.62.69.22 1.31.19 1.8.11.55-.08 1.66-.68 1.9-1.33.23-.64.23-1.2.16-1.33-.07-.13-.26-.21-.55-.36z"
                                    />
                                </svg>

                            </a>


                            <!-- Facebook -->

                            <a
                                href="#"
                                class="hover:text-blue-500 transition"
                                title="Facebook"
                            >

                                <svg
                                    class="w-4 h-4 fill-current"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        d="M22 12a10 10 0 10-11.63 9.87v-6.99H7.9V12h2.47V9.8c0-2.44 1.45-3.79 3.67-3.79 1.06 0 2.17.19 2.17.19v2.39h-1.22c-1.2 0-1.57.75-1.57 1.51V12h2.67l-.43 2.88h-2.24v6.99A10 10 0 0022 12z"
                                    />
                                </svg>

                            </a>


                            <!-- Instagram -->

                            <a
                                href="#"
                                class="hover:text-pink-500 transition"
                                title="Instagram"
                            >

                                <svg
                                    class="w-4 h-4 fill-current"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        d="M7.75 2C4.57 2 2 4.57 2 7.75v8.5C2 19.43 4.57 22 7.75 22h8.5c3.18 0 5.75-2.57 5.75-5.75v-8.5C22 4.57 19.43 2 16.25 2h-8.5zM12 7a5 5 0 110 10 5 5 0 010-10zm6.5-.25a1.25 1.25 0 11-2.5 0 1.25 1.25 0 012.5 0z"
                                    />
                                </svg>

                            </a>


                            <!-- TikTok -->

                            <a
                                href="#"
                                class="hover:text-gray-500 transition"
                                title="TikTok"
                            >

                                <svg
                                    class="w-4 h-4 fill-current"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        d="M16 1c.4 2.3 2.3 4.2 4.6 4.6v3.1c-1.5 0-2.9-.4-4.1-1.2v7.4a6.6 6.6 0 11-6.6-6.6c.4 0 .8 0 1.2.1v3.2a3.4 3.4 0 00-1.2-.2 3.4 3.4 0 103.4 3.4V1h2.7z"
                                    />
                                </svg>

                            </a>

                        </div>

                    </div>

                </div>

            </div>

        </transition>


        <!-- ========================================================= -->
        <!-- MAIN NAVBAR -->
        <!-- ========================================================= -->

        <div class="bg-white">

            <div
                class="max-w-7xl
                       mx-auto
                       px-4 sm:px-6 lg:px-10
                       py-3"
            >

                <!-- DESKTOP / TABLET ROW -->

                <div
                    class="flex items-center
                           justify-between
                           gap-3
                           lg:gap-6"
                >

                    <!-- MOBILE MENU -->

                    <button
                        @click="openSidebar"
                        type="button"
                        class="lg:hidden
                               w-9 h-9
                               flex items-center
                               justify-center
                               rounded-lg
                               hover:bg-gray-100
                               text-gray-600
                               transition
                               shrink-0"
                    >

                        <Menu
                            class="w-6 h-6"
                        />

                    </button>


                    <!-- LOGO -->

                    <button
                        @click="goHome"
                        type="button"
                        class="text-xl
                               sm:text-2xl
                               font-black
                               tracking-tight
                               leading-none
                               cursor-pointer
                               select-none
                               font-[Poppins]
                               shrink-0"
                    >

                        <span class="text-blue-600">
                            Smart
                        </span>

                        <span class="text-blue-600">
                            Cart
                        </span>

                    </button>


                    <!-- DESKTOP SEARCH -->

                    <div
                        class="hidden md:block
                               flex-1
                               max-w-xl"
                    >
                        <ProductSearch />
                    </div>


                    <!-- ICONS -->

                    <div
                        class="flex
                               items-center
                               gap-3
                               sm:gap-5
                               text-gray-600
                               shrink-0"
                    >

                        <!-- WISHLIST -->

                        <button
                            @click="openWishlist"
                            type="button"
                            class="relative
                                   hover:text-red-500
                                   transition"
                        >

                            <Heart
                                class="w-6 h-6
                                       sm:w-7 sm:h-7"
                            />

                            <span
                                v-if="wishlistCount > 0"
                                class="absolute
                                       -top-2
                                       -right-2
                                       min-w-4
                                       h-4
                                       px-1
                                       flex
                                       items-center
                                       justify-center
                                       rounded-full
                                       bg-red-500
                                       text-white
                                       text-[10px]
                                       font-bold"
                            >
                                {{ wishlistCount }}
                            </span>

                        </button>


                        <!-- CART -->

                        <button
                            @click="openCart"
                            type="button"
                            class="hover:text-blue-600
                                   transition
                                   relative
                                   rounded-full
                                   flex
                                   items-center
                                   justify-center
                                   p-1"
                        >

                            <ShoppingCart
                                class="w-6 h-6
                                       sm:w-7 sm:h-7"
                            />

                            <span
                                v-if="cartCount > 0"
                                class="absolute
                                       -top-2
                                       -right-2
                                       text-[10px]
                                       sm:text-xs
                                       bg-blue-600
                                       text-white
                                       min-w-4
                                       h-4
                                       px-1
                                       flex
                                       items-center
                                       justify-center
                                       rounded-full
                                       transition"
                                :class="{
                                    'animate-bounce':
                                        cartAnimate
                                }"
                            >
                                {{ cartCount }}
                            </span>

                        </button>


                        <!-- ACCOUNT -->

                        <AccountDropdown
                            @open-wishlist="
                                openWishlist
                            "
                        />

                    </div>

                </div>


                <!-- MOBILE SEARCH -->

                <div
                    class="md:hidden
                           mt-3
                           w-full"
                >
                    <ProductSearch />
                </div>

            </div>

        </div>


        <!-- ========================================================= -->
        <!-- BOTTOM NAVIGATION -->
        <!-- ========================================================= -->

        <transition name="slide-up">

            <div
                v-if="
                    showTopBar &&
                    !hideBottomNavigation
                "
                class="hidden lg:block
                       px-10
                       py-1
                       bg-white"
            >

                <div
                    class="border
                           rounded-2xl
                           max-w-7xl
                           mx-auto
                           px-6
                           py-2
                           flex
                           items-center
                           space-x-8
                           text-sm
                           shadow-lg
                           text-gray-600"
                >

                    <!-- CATEGORY -->

                    <button
                        @click="openSidebar"
                        type="button"
                        class="flex
                               items-center
                               gap-2
                               px-3 py-2
                               border-r
                               text-sm
                               text-blue-600
                               hover:bg-gray-200
                               transition
                               shrink-0"
                    >

                        <div
                            class="flex
                                   flex-col
                                   justify-center
                                   items-start
                                   gap-[4px]"
                        >

                            <span
                                class="block
                                       w-5
                                       h-[2px]
                                       bg-blue-600"
                            ></span>

                            <span
                                class="block
                                       w-3
                                       h-[2px]
                                       bg-blue-600"
                            ></span>

                            <span
                                class="block
                                       w-5
                                       h-[2px]
                                       bg-blue-600"
                            ></span>

                        </div>

                        <span class="font-semibold">
                          All Categories
                        </span>

                    </button>


                    <!-- LINKS -->

                        <button
                            @click="goHome"
                            type="button"
                            class="
                                font-medium
                                hover:text-blue-600
                                transition
                            "
                        >
                            Home
                        </button>


                        <button
                            @click="goShop"
                            type="button"
                            class="
                                font-medium
                                hover:text-blue-600
                                transition
                            "
                        >
                            Shop
                        </button>


                        <button
                            type="button"
                            @click="goAiShopping"
                            class="
                                font-medium
                                hover:text-blue-600
                                transition
                            "
                        >
                            AI Shopping
                        </button>


                       
                        <button
                            type="button"
                             @click="goNewArrivals"
                            class="
                                font-medium
                                hover:text-blue-600
                                transition
                            "
                        >
                            New Arrivals
                        </button>


                        <button
                            type="button"
                            @click="goOrders"
                            class="
                                font-medium
                                hover:text-blue-600
                                transition
                            "
                        >
                            My Orders
                        </button>


                        
                </div>

            </div>

        </transition>

    </header>

</template>


<style scoped>

.slide-up-enter-active,
.slide-up-leave-active {
    transition: all 0.3s ease;
}

.slide-up-enter-from,
.slide-up-leave-to {
    opacity: 0;
    transform: translateY(-100%);
}

</style>