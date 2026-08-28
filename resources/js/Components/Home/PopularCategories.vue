<script setup>
import { Link } from '@inertiajs/vue3'

defineProps({
    categories: {
        type: Array,
        default: () => [],
    },
})
</script>


<template>
    <section
        class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-14"
    >

        <!-- ========================================================= -->
        <!-- HEADER -->
        <!-- ========================================================= -->

        <div
            class="mb-7 flex items-end justify-between gap-4"
        >
            <div>
                <h2
                    class="text-2xl font-bold text-gray-900 sm:text-3xl"
                >
                    Popular Categories
                </h2>

                <p class="mt-1 text-sm text-gray-500">
                    Browse products by category
                </p>
            </div>

            <Link
                href="/productListing"
                class="
                    shrink-0
                    text-sm
                    font-semibold
                    text-blue-600
                    transition
                    hover:text-blue-700
                    hover:underline
                "
            >
                View All →
            </Link>
        </div>


        <!-- ========================================================= -->
        <!-- CATEGORIES -->
        <!-- ========================================================= -->

        <div
    class="
        category-scroll
        flex
        items-stretch
        gap-3
        overflow-x-auto
        pb-2
    "
>
            <Link
                v-for="category in categories"
                :key="category.id"
                :href="`/productListing?category=${category.slug}`"
                class="group"
            >
                <div
                    class="
                        flex
                        h-full
                        overflow-hidden
                        rounded-2xl
                        border
                        border-gray-100
                        bg-white
                        p-2
                        transition-all
                        duration-300
                        hover:-translate-y-1
                        hover:border-blue-100
                        hover:shadow-lg
                    "
                >

                    <!-- IMAGE -->

                    <div
                        class="
                            h-20
                            w-20
                            shrink-0
                            overflow-hidden
                            rounded-xl
                            bg-gray-50
                            sm:h-24
                            sm:w-20
                        "
                    >
                        <img
                            :src="`/storage/${category.image}`"
                            :alt="category.name"
                            class="
                                h-full
                                w-full
                                object-cover
                                transition
                                duration-500
                                group-hover:scale-110
                            "
                        />
                    </div>


                    <!-- CONTENT -->

                    <div
                        class="
                            flex
                            min-w-0
                            flex-1
                            flex-col
                            justify-center
                            px-3
                        "
                    >
                        <h3
                            class="
                                truncate
                                text-sm
                                font-semibold
                                text-gray-900
                                transition
                                group-hover:text-blue-600
                            "
                        >
                            {{ category.name }}
                        </h3>
                    </div>

                </div>
            </Link>
        </div>

    </section>
</template>



<style scoped>
.category-scroll {
    scrollbar-width: none;
    -ms-overflow-style: none;
}

.category-scroll::-webkit-scrollbar {
    display: none;
}
</style>