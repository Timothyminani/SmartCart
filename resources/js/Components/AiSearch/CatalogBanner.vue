<script setup>
import { Link } from '@inertiajs/vue3'
import {
    ArrowRight,
    ShoppingBag,
} from 'lucide-vue-next'

import bannerImage from '@/assets/images/banner4.jpg'
</script>


<template>

    <section
        class="
            mb-12
            flex
            justify-center
        "
    >

        <div
            class="
                relative
                overflow-hidden

                w-full
                max-w-4xl

                min-h-[240px]
                sm:min-h-[260px]

                rounded-2xl
                sm:rounded-3xl

                bg-cover
                bg-center

                p-5
                sm:p-8

                text-white
                shadow-xl
            "
            :style="{
                backgroundImage: `url(${bannerImage})`
            }"
        >

            <!-- ===================================================== -->
            <!-- OVERLAY -->
            <!-- ===================================================== -->

            <div
                class="
                    absolute
                    inset-0
                    bg-black/55
                "
            ></div>


            <!-- ===================================================== -->
            <!-- CONTENT -->
            <!-- ===================================================== -->

            <div
                class="
                    relative
                    z-10

                    min-h-[200px]

                    flex
                    flex-col
                    md:flex-row

                    items-start
                    md:items-center

                    justify-between

                    gap-8
                "
            >

                <!-- ================================================= -->
                <!-- TEXT -->
                <!-- ================================================= -->

                <div
                    class="
                        max-w-xl
                    "
                >

                    <!-- LABEL -->

                    <div
                        class="
                            inline-flex
                            items-center
                            gap-2

                            mb-4

                            px-3
                            py-1.5

                            rounded-full

                            bg-white/10
                            backdrop-blur-sm

                            border
                            border-white/10
                        "
                    >

                        <ShoppingBag
                            class="
                                w-4
                                h-4
                                text-blue-200
                            "
                        />

                        <span
                            class="
                                text-xs
                                sm:text-sm
                                uppercase
                                tracking-wider
                                text-blue-100
                                font-medium
                            "
                        >
                            Explore More
                        </span>

                    </div>


                    <!-- TITLE -->

                    <h2
                        class="
                            text-2xl
                            sm:text-3xl

                            font-bold

                            leading-tight
                        "
                    >
                        Browse Our Full Product Catalog
                    </h2>


                    <!-- DESCRIPTION -->

                    <p
                        class="
                            text-sm
                            sm:text-base

                            text-gray-200

                            leading-6
                            sm:leading-7

                            mt-3
                        "
                    >
                        Discover trending products,
                        explore categories, and shop with
                        powerful filters beyond AI search.
                    </p>

                </div>


                <!-- ================================================= -->
                <!-- ACTION -->
                <!-- ================================================= -->

                <Link
                    href="/productListing"
                    class="
                        w-full
                        md:w-auto

                        shrink-0

                        inline-flex
                        items-center
                        justify-center
                        gap-2

                        px-6
                        py-3

                        rounded-xl
                        sm:rounded-2xl

                        bg-white
                        text-gray-900

                        font-semibold
                        text-sm

                        hover:bg-gray-100

                        transition
                    "
                >

                    <span>
                        Browse Catalog
                    </span>

                    <ArrowRight
                        class="
                            w-4
                            h-4
                        "
                    />

                </Link>

            </div>

        </div>

    </section>

</template>