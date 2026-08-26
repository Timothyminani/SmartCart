<script setup>
import { computed } from 'vue'
import { router, usePage } from '@inertiajs/vue3'
import {
    X,
    LayoutGrid,
    Tag,
    ChevronRight,
    ShoppingBag,
    BadgePercent,
    Truck,
} from 'lucide-vue-next'

defineProps({
    open: {
        type: Boolean,
        default: false,
    },
})

const emit = defineEmits(['close'])

const page = usePage()

const categories = computed(() => {
    return page.props.navigation?.categories ?? []
})

const brands = computed(() => {
    return page.props.navigation?.brands ?? []
})

const close = () => {
    emit('close')
}


/*
|--------------------------------------------------------------------------
| CATEGORY NAVIGATION
|--------------------------------------------------------------------------
*/

const goToCategory = (category) => {

    close()

    router.get(
        '/productListing',
        {
            categories: [category.id],
        },
        {
            preserveState: false,
        }
    )
}


/*
|--------------------------------------------------------------------------
| BRAND NAVIGATION
|--------------------------------------------------------------------------
*/

const goToBrand = (brand) => {

    close()

    router.get(
        '/productListing',
        {
            brands: [brand.id],
        },
        {
            preserveState: false,
        }
    )
}


/*
|--------------------------------------------------------------------------
| ALL PRODUCTS
|--------------------------------------------------------------------------
*/

const goToProducts = () => {

    close()

    router.get('/productListing')
}
</script>


<template>

    <!-- OVERLAY -->
    <Transition
        enter-active-class="transition-opacity duration-300"
        enter-from-class="opacity-0"
        enter-to-class="opacity-100"
        leave-active-class="transition-opacity duration-300"
        leave-from-class="opacity-100"
        leave-to-class="opacity-0"
    >
        <div
            v-if="open"
            @click="close"
            class="fixed inset-0
                   bg-black/40
                   backdrop-blur-[1px]
                   z-[60]"
        />
    </Transition>


    <!-- SIDEBAR -->
    <aside
        class="fixed
               top-0
               left-0
               z-[70]
               h-full
               w-[88%]
               max-w-[340px]
               bg-white
               shadow-2xl
               transform
               transition-transform
               duration-300
               ease-out
               flex
               flex-col"
        :class="
            open
                ? 'translate-x-0'
                : '-translate-x-full'
        "
    >

        <!-- HEADER -->
        <div
            class="flex
                   items-center
                   justify-between
                   px-5
                   py-5
                   border-b
                   border-gray-100"
        >

            <div>

                <div class="flex items-center gap-2">

                    <div
                        class="w-9
                               h-9
                               rounded-xl
                               bg-blue-50
                               flex
                               items-center
                               justify-center"
                    >
                        <LayoutGrid
                            class="w-5 h-5 text-blue-600"
                        />
                    </div>

                    <div>

                        <h2
                            class="font-bold
                                   text-lg
                                   text-gray-900"
                        >
                            Browse
                        </h2>

                        <p
                            class="text-xs
                                   text-gray-500"
                        >
                            Find what you're looking for
                        </p>

                    </div>

                </div>

            </div>


            <button
                @click="close"
                class="w-9
                       h-9
                       rounded-full
                       flex
                       items-center
                       justify-center
                       text-gray-500
                       hover:text-gray-900
                       hover:bg-gray-100
                       transition"
            >
                <X class="w-5 h-5" />
            </button>

        </div>


        <!-- SCROLLABLE CONTENT -->
        <div
            class="flex-1
                   overflow-y-auto
                   px-4
                   py-5"
        >

            <!-- CATEGORIES -->
            <section>

                <div
                    class="flex
                           items-center
                           justify-between
                           px-2
                           mb-3"
                >

                    <div
                        class="flex
                               items-center
                               gap-2"
                    >

                        <LayoutGrid
                            class="w-4 h-4 text-gray-500"
                        />

                        <h3
                            class="text-xs
                                   font-bold
                                   tracking-wider
                                   text-gray-500
                                   uppercase"
                        >
                            Shop by Category
                        </h3>

                    </div>

                    <span
                        class="text-[11px]
                               text-gray-400"
                    >
                        {{ categories.length }}
                    </span>

                </div>


                <div class="space-y-1">

                    <button
                        v-for="category in categories"
                        :key="category.id"
                        @click="goToCategory(category)"
                        class="group
                               w-full
                               flex
                               items-center
                               justify-between
                               px-3
                               py-2.5
                               rounded-xl
                               text-left
                               hover:bg-blue-50
                               transition"
                    >

                        <span
                            class="text-sm
                                   font-medium
                                   text-gray-700
                                   group-hover:text-blue-600
                                   transition"
                        >
                            {{ category.name }}
                        </span>

                        <ChevronRight
                            class="w-4
                                   h-4
                                   text-gray-300
                                   group-hover:text-blue-500
                                   group-hover:translate-x-0.5
                                   transition"
                        />

                    </button>

                </div>


                <!-- EMPTY -->
                <p
                    v-if="!categories.length"
                    class="text-sm
                           text-gray-400
                           px-3
                           py-3"
                >
                    No categories available.
                </p>

            </section>


            <!-- DIVIDER -->
            <div
                class="border-t
                       border-gray-100
                       my-6"
            ></div>


            <!-- BRANDS -->
            <section>

                <div
                    class="flex
                           items-center
                           justify-between
                           px-2
                           mb-3"
                >

                    <div
                        class="flex
                               items-center
                               gap-2"
                    >

                        <Tag
                            class="w-4 h-4 text-gray-500"
                        />

                        <h3
                            class="text-xs
                                   font-bold
                                   tracking-wider
                                   text-gray-500
                                   uppercase"
                        >
                            Shop by Brand
                        </h3>

                    </div>

                    <span
                        class="text-[11px]
                               text-gray-400"
                    >
                        {{ brands.length }}
                    </span>

                </div>


                <div class="space-y-1">

                    <button
                        v-for="brand in brands"
                        :key="brand.id"
                        @click="goToBrand(brand)"
                        class="group
                               w-full
                               flex
                               items-center
                               gap-3
                               px-3
                               py-2
                               rounded-xl
                               hover:bg-blue-50
                               transition"
                    >

                        <!-- BRAND LOGO -->
                        <div
                            class="w-9
                                   h-9
                                   rounded-lg
                                   border
                                   border-gray-100
                                   bg-white
                                   flex
                                   items-center
                                   justify-center
                                   overflow-hidden
                                   shrink-0"
                        >

                            <img
                                v-if="brand.logo"
                                :src="`/storage/${brand.logo}`"
                                :alt="brand.name"
                                class="w-full
                                       h-full
                                       object-contain
                                       p-1.5"
                            />

                            <!-- FALLBACK -->
                            <span
                                v-else
                                class="text-xs
                                       font-bold
                                       text-blue-600"
                            >
                                {{ brand.name.charAt(0).toUpperCase() }}
                            </span>

                        </div>


                        <span
                            class="flex-1
                                   text-left
                                   text-sm
                                   font-medium
                                   text-gray-700
                                   group-hover:text-blue-600
                                   transition"
                        >
                            {{ brand.name }}
                        </span>


                        <ChevronRight
                            class="w-4
                                   h-4
                                   text-gray-300
                                   group-hover:text-blue-500
                                   group-hover:translate-x-0.5
                                   transition"
                        />

                    </button>

                </div>


                <p
                    v-if="!brands.length"
                    class="text-sm
                           text-gray-400
                           px-3
                           py-3"
                >
                    No brands available.
                </p>

            </section>


            <!-- DIVIDER -->
            <div
                class="border-t
                       border-gray-100
                       my-6"
            ></div>


            <!-- QUICK LINKS -->
            <section>

                <h3
                    class="px-2
                           mb-3
                           text-xs
                           font-bold
                           tracking-wider
                           text-gray-500
                           uppercase"
                >
                    Quick Links
                </h3>


                <button
                    @click="goToProducts"
                    class="w-full
                           flex
                           items-center
                           gap-3
                           px-3
                           py-2.5
                           rounded-xl
                           text-sm
                           font-medium
                           text-gray-700
                           hover:text-blue-600
                           hover:bg-blue-50
                           transition"
                >

                    <ShoppingBag class="w-4 h-4" />

                    All Products

                </button>


                <button
                    class="w-full
                           flex
                           items-center
                           gap-3
                           px-3
                           py-2.5
                           rounded-xl
                           text-sm
                           font-medium
                           text-gray-700
                           hover:text-blue-600
                           hover:bg-blue-50
                           transition"
                >

                    <BadgePercent class="w-4 h-4" />

                    Deals & Offers

                </button>

            </section>


            <!-- DELIVERY CARD -->
            <div
                class="mt-6
                       rounded-2xl
                       bg-blue-50
                       border
                       border-blue-100
                       p-4"
            >

                <div
                    class="flex
                           items-start
                           gap-3"
                >

                    <div
                        class="w-10
                               h-10
                               rounded-xl
                               bg-blue-600
                               text-white
                               flex
                               items-center
                               justify-center
                               shrink-0"
                    >
                        <Truck class="w-5 h-5" />
                    </div>


                    <div>

                        <p
                            class="text-sm
                                   font-bold
                                   text-gray-900"
                        >
                            Free Delivery
                        </p>

                        <p
                            class="text-xs
                                   text-gray-500
                                   mt-1
                                   leading-5"
                        >
                            Enjoy free delivery on orders
                            above KES 2,000.
                        </p>

                    </div>

                </div>

            </div>

        </div>

    </aside>

</template>