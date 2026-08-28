<script setup>
import loginImage from '@/assets/images/loginImage.jpeg'
import {
    ShoppingBag,
    ShieldCheck,
    Truck,
} from 'lucide-vue-next'
</script>


<template>
    <div
        class="
            min-h-screen

            grid
            lg:grid-cols-2

            bg-white
        "
    >

        <!-- ========================================================= -->
        <!-- LEFT SIDE -->
        <!-- ========================================================= -->

        <div
            class="
                hidden
                lg:flex

                relative
                overflow-hidden

                bg-cover
                bg-center

                text-white
            "
            :style="{
                backgroundImage: `url(${loginImage})`
            }"
        >

            <!-- DARK OVERLAY -->

            <div
                class="
                    absolute
                    inset-0

                    bg-blue-900/50
                "
            ></div>


            <!-- DECORATIVE BLUR -->

            <div
                class="
                    absolute
                    top-0
                    right-0

                    w-96
                    h-96

                    rounded-full

                    bg-white/10

                    blur-3xl
                "
            ></div>


            <div
                class="
                    absolute
                    bottom-0
                    left-0

                    w-80
                    h-80

                    rounded-full

                    bg-white/10

                    blur-3xl
                "
            ></div>


            <!-- CONTENT -->

            <div
                class="
                    relative
                    z-10

                    flex
                    flex-col
                    justify-between

                    w-full
                    max-w-2xl

                    px-10
                    xl:px-14

                    py-10
                    xl:py-14
                "
            >

                <!-- HERO -->

                <div>
                    <slot name="hero">

                        <h1
                            class="
                                text-4xl
                                xl:text-5xl

                                font-bold
                                leading-tight
                            "
                        >
                            Shop Smarter With AI
                        </h1>


                        <p
                            class="
                                mt-5

                                max-w-lg

                                text-base
                                xl:text-lg

                                leading-relaxed
                                text-blue-100
                            "
                        >
                            Discover thousands of products
                            and make confident buying decisions.
                        </p>

                    </slot>
                </div>


                <!-- FEATURES -->

                <div
                    class="
                        mt-12

                        grid
                        grid-cols-3

                        gap-6
                    "
                >

                    <!-- FEATURE 1 -->

                    <div>
                        <div
                            class="
                                w-11
                                h-11

                                flex
                                items-center
                                justify-center

                                rounded-xl

                                bg-blue-900/60

                                backdrop-blur-sm
                            "
                        >
                            <ShoppingBag
                                class="w-5 h-5"
                            />
                        </div>


                        <h3
                            class="
                                mt-4

                                text-sm
                                font-semibold
                            "
                        >
                            Thousands of Products
                        </h3>


                        <p
                            class="
                                mt-1

                                text-xs
                                leading-5
                                text-blue-100
                            "
                        >
                            Browse products from trusted brands.
                        </p>
                    </div>


                    <!-- FEATURE 2 -->

                    <div>
                        <div
                            class="
                                w-11
                                h-11

                                flex
                                items-center
                                justify-center

                                rounded-xl

                                bg-blue-900/60

                                backdrop-blur-sm
                            "
                        >
                            <ShieldCheck
                                class="w-5 h-5"
                            />
                        </div>


                        <h3
                            class="
                                mt-4

                                text-sm
                                font-semibold
                            "
                        >
                            AI Powered Comparison
                        </h3>


                        <p
                            class="
                                mt-1

                                text-xs
                                leading-5
                                text-blue-100
                            "
                        >
                            Compare products intelligently before buying.
                        </p>
                    </div>


                    <!-- FEATURE 3 -->

                    <div>
                        <div
                            class="
                                w-11
                                h-11

                                flex
                                items-center
                                justify-center

                                rounded-xl

                                bg-blue-900/60

                                backdrop-blur-sm
                            "
                        >
                            <Truck
                                class="w-5 h-5"
                            />
                        </div>


                        <h3
                            class="
                                mt-4

                                text-sm
                                font-semibold
                            "
                        >
                            Reliable Delivery
                        </h3>


                        <p
                            class="
                                mt-1

                                text-xs
                                leading-5
                                text-blue-100
                            "
                        >
                            Shop securely and receive your orders reliably.
                        </p>
                    </div>

                </div>

            </div>

        </div>


        <!-- ========================================================= -->
        <!-- RIGHT SIDE -->
        <!-- ========================================================= -->

        <div
            class="
                flex
                items-center
                justify-center

                bg-gray-50

                px-3
                py-6

                sm:px-6
                sm:py-8

                lg:px-10
                xl:px-14
            "
        >

            <div
                class="
                    w-full
                    max-w-md
                "
            >
                <slot />
            </div>

        </div>

    </div>
</template>