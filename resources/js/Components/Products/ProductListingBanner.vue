<script setup>
import BannerImage from '@/assets/images/banner4.jpg'
import { Sparkles } from 'lucide-vue-next'
import { Link } from '@inertiajs/vue3'
</script>


<template>

    <section
        class="
            mb-6
            sm:mb-8
            lg:mb-10
        "
    >

        <div
            class="
                relative
                overflow-hidden

                min-h-[180px]
                sm:min-h-[200px]
                md:min-h-[220px]

                rounded-2xl
            "
        >

            <!-- BACKGROUND IMAGE -->

            <img
                :src="BannerImage"
                alt="Browse SmartCart products"
                class="
                    absolute
                    inset-0

                    w-full
                    h-full

                    object-cover
                "
            />


            <!-- OVERLAY -->

            <div
                class="
                    absolute
                    inset-0

                    bg-gradient-to-r
                    from-black/70
                    via-black/45
                    to-black/10
                "
            ></div>


            <!-- CONTENT -->

            <div
                class="
                    relative
                    z-10

                    min-h-[180px]
                    sm:min-h-[200px]
                    md:min-h-[220px]

                    flex
                    items-center

                    px-5
                    sm:px-7
                    lg:px-9

                    py-6
                "
            >

                <div
                    class="
                        max-w-lg
                        text-white
                    "
                >

                    <!-- LABEL -->

                    <p
                        class="
                            text-xs
                            sm:text-sm

                            font-medium

                            text-blue-200

                            mb-2
                        "
                    >
                        SmartCart Catalog
                    </p>


                    <!-- TITLE -->

                    <h1
                        class="
                            text-2xl
                            sm:text-3xl
                            lg:text-4xl

                            font-bold

                            leading-tight
                        "
                    >
                        Explore Products
                    </h1>


                    <!-- DESCRIPTION -->

                    <p
                        class="
                            text-sm
                            sm:text-base

                            text-gray-200

                            mt-2

                            max-w-md

                            leading-6
                        "
                    >
                        Browse the full catalog,
                        compare options, and find
                        the right product for your needs.
                    </p>


                    <!-- ACTION -->

                    <div class="mt-5">

                        <Link
                            href="/ai-search"
                            class="
                                inline-flex
                                items-center
                                justify-center
                                gap-2

                                px-4
                                sm:px-5

                                py-2.5

                                rounded-xl

                                bg-white
                                text-gray-900

                                text-sm
                                font-semibold

                                hover:bg-gray-100

                                transition
                            "
                        >

                            <Sparkles
                                class="
                                    w-4
                                    h-4

                                    text-blue-600
                                "
                            />

                            Ask SmartCart AI

                        </Link>

                    </div>

                </div>

            </div>

        </div>

    </section>

</template>