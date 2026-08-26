<script setup>
import { computed, ref } from 'vue'

import {
    SlidersHorizontal,
    X,
} from 'lucide-vue-next'


/*
|--------------------------------------------------------------------------
| PROPS
|--------------------------------------------------------------------------
*/

const props = defineProps({
    categories: {
        type: Array,
        default: () => [],
    },

    brands: {
        type: Array,
        default: () => [],
    },

    selectedCategories: {
        type: Array,
        default: () => [],
    },

    selectedBrands: {
        type: Array,
        default: () => [],
    },
})


/*
|--------------------------------------------------------------------------
| EMITS
|--------------------------------------------------------------------------
*/

const emit = defineEmits([
    'update:selectedCategories',
    'update:selectedBrands',
    'change',
])


/*
|--------------------------------------------------------------------------
| STATE
|--------------------------------------------------------------------------
*/

const mobileOpen = ref(false)


/*
|--------------------------------------------------------------------------
| MODELS
|--------------------------------------------------------------------------
*/

const categoryModel = computed({
    get() {
        return props.selectedCategories
    },

    set(value) {
        emit(
            'update:selectedCategories',
            value
        )
    },
})


const brandModel = computed({
    get() {
        return props.selectedBrands
    },

    set(value) {
        emit(
            'update:selectedBrands',
            value
        )
    },
})


/*
|--------------------------------------------------------------------------
| ACTIVE FILTER COUNT
|--------------------------------------------------------------------------
*/

const activeFilterCount = computed(() => {
    return (
        props.selectedCategories.length +
        props.selectedBrands.length
    )
})


/*
|--------------------------------------------------------------------------
| FILTER CHANGE
|--------------------------------------------------------------------------
*/

const filtersChanged = () => {
    emit('change')
}


/*
|--------------------------------------------------------------------------
| CLEAR FILTERS
|--------------------------------------------------------------------------
*/

const clearFilters = () => {

    emit(
        'update:selectedCategories',
        []
    )

    emit(
        'update:selectedBrands',
        []
    )

    emit('change')
}
</script>


<template>

    <!-- ========================================================= -->
    <!-- MOBILE FILTER BUTTON -->
    <!-- ========================================================= -->

    <div
        class="
            lg:hidden
            mb-4
        "
    >

        <button
            @click="mobileOpen = true"
            type="button"
            class="
                w-full

                flex
                items-center
                justify-between

                px-4
                py-3

                bg-white

                border
                border-gray-200

                rounded-xl

                text-sm
                font-medium
                text-gray-700
            "
        >

            <div
                class="
                    flex
                    items-center
                    gap-2
                "
            >

                <SlidersHorizontal
                    class="
                        w-4
                        h-4
                        text-blue-600
                    "
                />

                Filters

            </div>


            <span
                v-if="activeFilterCount"
                class="
                    min-w-6
                    h-6

                    px-1.5

                    rounded-full

                    bg-blue-600
                    text-white

                    text-xs

                    flex
                    items-center
                    justify-center
                "
            >
                {{ activeFilterCount }}
            </span>

        </button>

    </div>


    <!-- ========================================================= -->
    <!-- DESKTOP SIDEBAR -->
    <!-- ========================================================= -->

    <aside
        class="
            hidden
            lg:block

            w-64
            shrink-0
        "
    >

        <div
            class="
                bg-white

                border
                border-gray-200

                rounded-xl

                overflow-hidden
            "
        >

            <!-- HEADER -->

            <div
                class="
                    flex
                    items-center
                    justify-between

                    px-4
                    py-4

                    border-b
                    border-gray-100
                "
            >

                <div
                    class="
                        flex
                        items-center
                        gap-2
                    "
                >

                    <SlidersHorizontal
                        class="
                            w-4
                            h-4
                            text-gray-500
                        "
                    />

                    <h2
                        class="
                            text-sm
                            font-semibold
                            text-gray-900
                        "
                    >
                        Filters
                    </h2>

                </div>


                <button
                    v-if="activeFilterCount"
                    @click="clearFilters"
                    type="button"
                    class="
                        text-xs
                        font-medium
                        text-blue-600

                        hover:text-blue-700
                    "
                >
                    Clear
                </button>

            </div>


            <!-- CATEGORIES -->

            <div
                class="
                    px-4
                    py-4

                    border-b
                    border-gray-100
                "
            >

                <h3
                    class="
                        text-xs
                        font-semibold

                        uppercase
                        tracking-wide

                        text-gray-500

                        mb-3
                    "
                >
                    Categories
                </h3>


                <div class="space-y-2.5">

                    <label
                        v-for="category in categories"
                        :key="category.id"
                        class="
                            flex
                            items-center
                            gap-2.5

                            cursor-pointer

                            text-sm
                            text-gray-700
                        "
                    >

                        <input
                            v-model="categoryModel"
                            :value="category.id"
                            type="checkbox"
                            class="
                                w-4
                                h-4

                                accent-blue-600
                            "
                            @change="filtersChanged"
                        />

                        <span>
                            {{ category.name }}
                        </span>

                    </label>

                </div>

            </div>


            <!-- BRANDS -->

            <div
                class="
                    px-4
                    py-4
                "
            >

                <h3
                    class="
                        text-xs
                        font-semibold

                        uppercase
                        tracking-wide

                        text-gray-500

                        mb-3
                    "
                >
                    Brands
                </h3>


                <div class="space-y-3">

                    <label
                        v-for="brand in brands"
                        :key="brand.id"
                        class="
                            flex
                            items-center
                            gap-2.5

                            cursor-pointer
                        "
                    >

                        <input
                            v-model="brandModel"
                            :value="brand.id"
                            type="checkbox"
                            class="
                                w-4
                                h-4

                                accent-blue-600

                                shrink-0
                            "
                            @change="filtersChanged"
                        />


                        <img
                            v-if="brand.logo"
                            :src="`/storage/${brand.logo}`"
                            :alt="brand.name"
                            class="
                                w-6
                                h-6

                                object-contain

                                shrink-0
                            "
                        />


                        <span
                            class="
                                text-sm
                                text-gray-700
                            "
                        >
                            {{ brand.name }}
                        </span>

                    </label>

                </div>

            </div>

        </div>

    </aside>


    <!-- ========================================================= -->
    <!-- MOBILE OVERLAY -->
    <!-- ========================================================= -->

    <div
        v-if="mobileOpen"
        @click="mobileOpen = false"
        class="
            fixed
            inset-0

            bg-black/40

            z-[90]

            lg:hidden
        "
    ></div>


    <!-- ========================================================= -->
    <!-- MOBILE DRAWER -->
    <!-- ========================================================= -->

    <div
        class="
            fixed
            top-0
            left-0

            h-full
            w-[88%]
            max-w-sm

            bg-white

            z-[100]

            shadow-xl

            transform
            transition-transform
            duration-300

            lg:hidden

            flex
            flex-col
        "
        :class="
            mobileOpen
                ? 'translate-x-0'
                : '-translate-x-full'
        "
    >

        <!-- DRAWER HEADER -->

        <div
            class="
                flex
                items-center
                justify-between

                px-4
                py-4

                border-b
                border-gray-100

                shrink-0
            "
        >

            <div
                class="
                    flex
                    items-center
                    gap-2
                "
            >

                <SlidersHorizontal
                    class="
                        w-5
                        h-5
                        text-blue-600
                    "
                />

                <div>

                    <h2
                        class="
                            text-base
                            font-semibold
                            text-gray-900
                        "
                    >
                        Filters
                    </h2>

                    <p
                        v-if="activeFilterCount"
                        class="
                            text-xs
                            text-gray-500
                            mt-0.5
                        "
                    >
                        {{ activeFilterCount }}
                        active
                    </p>

                </div>

            </div>


            <button
                @click="mobileOpen = false"
                type="button"
                class="
                    w-9
                    h-9

                    rounded-full

                    flex
                    items-center
                    justify-center

                    hover:bg-gray-100
                "
            >

                <X
                    class="
                        w-5
                        h-5
                        text-gray-600
                    "
                />

            </button>

        </div>


        <!-- FILTER CONTENT -->

        <div
            class="
                flex-1
                overflow-y-auto
            "
        >

            <!-- CATEGORIES -->

            <div
                class="
                    px-4
                    py-5

                    border-b
                    border-gray-100
                "
            >

                <h3
                    class="
                        text-sm
                        font-semibold
                        text-gray-900

                        mb-4
                    "
                >
                    Categories
                </h3>


                <div class="space-y-3">

                    <label
                        v-for="category in categories"
                        :key="category.id"
                        class="
                            flex
                            items-center
                            gap-3

                            cursor-pointer
                        "
                    >

                        <input
                            v-model="categoryModel"
                            :value="category.id"
                            type="checkbox"
                            class="
                                w-4
                                h-4

                                accent-blue-600
                            "
                            @change="filtersChanged"
                        />

                        <span
                            class="
                                text-sm
                                text-gray-700
                            "
                        >
                            {{ category.name }}
                        </span>

                    </label>

                </div>

            </div>


            <!-- BRANDS -->

            <div
                class="
                    px-4
                    py-5
                "
            >

                <h3
                    class="
                        text-sm
                        font-semibold
                        text-gray-900

                        mb-4
                    "
                >
                    Brands
                </h3>


                <div class="space-y-3">

                    <label
                        v-for="brand in brands"
                        :key="brand.id"
                        class="
                            flex
                            items-center
                            gap-3

                            cursor-pointer
                        "
                    >

                        <input
                            v-model="brandModel"
                            :value="brand.id"
                            type="checkbox"
                            class="
                                w-4
                                h-4

                                accent-blue-600
                            "
                            @change="filtersChanged"
                        />


                        <img
                            v-if="brand.logo"
                            :src="`/storage/${brand.logo}`"
                            :alt="brand.name"
                            class="
                                w-7
                                h-7

                                object-contain

                                shrink-0
                            "
                        />


                        <span
                            class="
                                text-sm
                                text-gray-700
                            "
                        >
                            {{ brand.name }}
                        </span>

                    </label>

                </div>

            </div>

        </div>


        <!-- DRAWER FOOTER -->

        <div
            class="
                px-4
                py-4

                border-t
                border-gray-100

                bg-white

                shrink-0

                flex
                gap-3
            "
        >

            <button
                v-if="activeFilterCount"
                @click="clearFilters"
                type="button"
                class="
                    flex-1

                    px-4
                    py-3

                    border
                    border-gray-200

                    rounded-xl

                    text-sm
                    font-medium
                    text-gray-700
                "
            >
                Clear
            </button>


            <button
                @click="mobileOpen = false"
                type="button"
                class="
                    flex-1

                    px-4
                    py-3

                    bg-blue-600
                    hover:bg-blue-700

                    text-white

                    rounded-xl

                    text-sm
                    font-semibold

                    transition
                "
            >
                View Products
            </button>

        </div>

    </div>

</template>