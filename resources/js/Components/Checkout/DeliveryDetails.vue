<script setup>
import {
    Phone,
    MapPin,
} from 'lucide-vue-next'


defineProps({
    phone: {
        type: String,
        default: '',
    },

    address: {
        type: String,
        default: '',
    },
})


const emit = defineEmits([
    'update:phone',
    'update:address',
])
</script>


<template>

    <section>

        <!-- SECTION HEADER -->

        <div class="mb-5">

            <div
                class="
                    flex
                    items-center
                    gap-3
                "
            >

                <span
                    class="
                        flex
                        h-7
                        w-7

                        items-center
                        justify-center

                        rounded-full

                        bg-gray-900
                        text-white

                        text-xs
                        font-semibold
                    "
                >
                    1
                </span>


                <h2
                    class="
                        text-lg
                        font-semibold
                        text-gray-900
                    "
                >
                    Contact & delivery
                </h2>

            </div>


            <p
                class="
                    mt-2
                    ml-10

                    text-sm
                    text-gray-500
                "
            >
                Tell us where we should deliver your order.
            </p>

        </div>


        <!-- FIELDS -->

        <div
            class="
                grid
                gap-5
            "
        >

            <!-- PHONE -->

            <div>

                <label
                    for="checkout-phone"
                    class="
                        block

                        mb-2

                        text-sm
                        font-medium
                        text-gray-700
                    "
                >
                    Phone number
                </label>


                <div class="relative">

                    <Phone
                        class="
                            absolute
                            left-3.5
                            top-1/2

                            -translate-y-1/2

                            w-4
                            h-4

                            text-gray-400
                        "
                    />


                    <input
                        id="checkout-phone"
                        :value="phone"
                        @input="
                            emit(
                                'update:phone',
                                $event.target.value
                            )
                        "
                        type="tel"
                        inputmode="tel"
                        autocomplete="tel"
                        placeholder="07XXXXXXXX"
                        class="
                            w-full

                            rounded-xl

                            border
                            border-gray-200

                            bg-white

                            py-3
                            pl-10
                            pr-4

                            text-sm
                            text-gray-900

                            placeholder:text-gray-400

                            outline-none

                            transition

                            focus:border-blue-500
                            focus:ring-2
                            focus:ring-blue-100
                        "
                    />

                </div>


                <p
                    class="
                        mt-1.5
                        text-xs
                        text-gray-400
                    "
                >
                    We'll use this number for delivery
                    updates and M-Pesa payments.
                </p>

            </div>


            <!-- ADDRESS -->

            <div>

                <label
                    for="checkout-address"
                    class="
                        block

                        mb-2

                        text-sm
                        font-medium
                        text-gray-700
                    "
                >
                    Delivery address
                </label>


                <div class="relative">

                    <MapPin
                        class="
                            absolute
                            left-3.5
                            top-3.5

                            w-4
                            h-4

                            text-gray-400
                        "
                    />


                    <textarea
                        id="checkout-address"
                        :value="address"
                        @input="
                            emit(
                                'update:address',
                                $event.target.value
                            )
                        "
                        rows="3"
                        autocomplete="street-address"
                        placeholder="e.g. Nairobi, Westlands, near ABC Place..."
                        class="
                            w-full

                            resize-none

                            rounded-xl

                            border
                            border-gray-200

                            bg-white

                            py-3
                            pl-10
                            pr-4

                            text-sm
                            leading-6
                            text-gray-900

                            placeholder:text-gray-400

                            outline-none

                            transition

                            focus:border-blue-500
                            focus:ring-2
                            focus:ring-blue-100
                        "
                    ></textarea>

                </div>

            </div>

        </div>

    </section>

</template>