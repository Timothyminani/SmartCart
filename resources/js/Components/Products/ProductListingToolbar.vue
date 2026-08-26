<script setup>
defineProps({
    productCount: {
        type: Number,
        default: 0,
    },

    sort: {
        type: String,
        default: '',
    },
})


const emit = defineEmits([
    'update:sort',
    'change',
])


const updateSort = (event) => {

    emit(
        'update:sort',
        event.target.value
    )

    emit('change')
}
</script>


<template>

    <div
        class="
            flex
            flex-col

            sm:flex-row
            sm:items-center
            sm:justify-between

            gap-3

            mb-5
        "
    >

        <!-- PRODUCT COUNT -->

        <p
            class="
                hidden
                sm:block
                text-sm
                text-gray-500
            "
        >
            <span
                class="
                    font-semibold
                    text-gray-900
                "
            >
                {{ productCount }}
            </span>

            {{
                productCount === 1
                    ? 'product'
                    : 'products'
            }}
        </p>


        <!-- SORT -->

        <div
            class="
                w-full
                sm:w-auto
            "
        >

            <select
                :value="sort"
                @change="updateSort"
                class="
                    w-full
                    sm:w-auto

                    min-w-[180px]

                    bg-white

                    border
                    border-gray-200

                    rounded-xl

                    px-3
                    py-2.5

                    text-sm
                    text-gray-700

                    cursor-pointer

                    focus:outline-none
                    focus:border-blue-500
                    focus:ring-2
                    focus:ring-blue-100

                    transition
                "
            >

                <option value="">
                    Sort products
                </option>

                <option value="price_asc">
                    Price: Low to High
                </option>

                <option value="price_desc">
                    Price: High to Low
                </option>

                <option value="newest">
                    Newest
                </option>

            </select>

        </div>

    </div>

</template>