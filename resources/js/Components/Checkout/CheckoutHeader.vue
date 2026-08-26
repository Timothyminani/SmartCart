<script setup>
import {
    Check,
    ShoppingCart,
    Truck,
    CreditCard,
} from 'lucide-vue-next'
</script>


<template>

    <header
        class="
            mb-8
            sm:mb-10
        "
    >

        <!-- TITLE -->

        <div>

            <p
                class="
                    text-sm
                    font-medium
                    text-blue-600
                "
            >
                Secure checkout
            </p>


            <h1
                class="
                    mt-1

                    text-2xl
                    sm:text-3xl

                    font-bold
                    tracking-tight
                    text-gray-900
                "
            >
                Complete your order
            </h1>


            <p
                class="
                    mt-2

                    text-sm
                    sm:text-base
                    text-gray-500
                "
            >
                Review your details and choose how
                you'd like to receive and pay for your order.
            </p>

        </div>


        <!-- PROGRESS -->

        <div
            class="
                mt-7

                flex
                items-center

                max-w-xl
            "
        >

            <!-- CART -->

            <div
                class="
                    flex
                    items-center
                    gap-2
                    shrink-0
                "
            >

                <div
                    class="
                        w-8
                        h-8

                        rounded-full

                        bg-blue-600
                        text-white

                        flex
                        items-center
                        justify-center
                    "
                >
                    <Check class="w-4 h-4" />
                </div>


                <div class="hidden sm:block">

                    <p
                        class="
                            text-xs
                            text-gray-400
                        "
                    >
                        Step 1
                    </p>

                    <p
                        class="
                            text-sm
                            font-medium
                            text-gray-700
                        "
                    >
                        Cart
                    </p>

                </div>

            </div>


            <!-- LINE -->

            <div
                class="
                    h-px
                    flex-1

                    mx-3
                    sm:mx-5

                    bg-blue-200
                "
            ></div>


            <!-- DELIVERY -->

            <div
                class="
                    flex
                    items-center
                    gap-2
                    shrink-0
                "
            >

                <div
                    class="
                        w-8
                        h-8

                        rounded-full

                        border-2
                        border-blue-600

                        bg-white
                        text-blue-600

                        flex
                        items-center
                        justify-center

                        text-sm
                        font-semibold
                    "
                >
                    2
                </div>


                <div class="hidden sm:block">

                    <p
                        class="
                            text-xs
                            text-gray-400
                        "
                    >
                        Step 2
                    </p>

                    <p
                        class="
                            text-sm
                            font-medium
                            text-gray-900
                        "
                    >
                        Delivery
                    </p>

                </div>

            </div>


            <!-- LINE -->

            <div
                class="
                    h-px
                    flex-1

                    mx-3
                    sm:mx-5

                    bg-gray-200
                "
            ></div>


            <!-- PAYMENT -->

            <div
                class="
                    flex
                    items-center
                    gap-2
                    shrink-0
                "
            >

                <div
                    class="
                        w-8
                        h-8

                        rounded-full

                        border
                        border-gray-300

                        bg-white
                        text-gray-500

                        flex
                        items-center
                        justify-center

                        text-sm
                        font-semibold
                    "
                >
                    3
                </div>


                <div class="hidden sm:block">

                    <p
                        class="
                            text-xs
                            text-gray-400
                        "
                    >
                        Step 3
                    </p>

                    <p
                        class="
                            text-sm
                            font-medium
                            text-gray-500
                        "
                    >
                        Payment
                    </p>

                </div>

            </div>

        </div>

    </header>

</template>