<script setup>
import {
    Truck,
    Zap,
    MapPin,
    Check,
} from 'lucide-vue-next'


defineProps({
    modelValue: {
        type: String,
        default: 'standard',
    },
})


const emit = defineEmits([
    'update:modelValue',
])
</script>


<template>

    <section>

        <!-- HEADER -->

        <div class="mb-4">

            <h3
                class="
                    text-sm
                    font-semibold
                    text-gray-900
                "
            >
                Delivery method
            </h3>

            <p
                class="
                    mt-1
                    text-sm
                    text-gray-500
                "
            >
                Choose the option that works best for you.
            </p>

        </div>


        <!-- OPTIONS -->

        <div
            class="
                divide-y
                divide-gray-100

                border
                border-gray-200

                rounded-xl

                overflow-hidden
            "
        >

            <!-- STANDARD -->

            <label
                class="
                    flex
                    items-center
                    justify-between
                    gap-4

                    px-4
                    py-4

                    cursor-pointer

                    transition

                    hover:bg-gray-50
                "
                :class="
                    modelValue === 'standard'
                        ? 'bg-blue-50/50'
                        : 'bg-white'
                "
            >

                <input
                    type="radio"
                    value="standard"
                    :checked="modelValue === 'standard'"
                    class="sr-only"
                    @change="
                        emit(
                            'update:modelValue',
                            'standard'
                        )
                    "
                />


                <div
                    class="
                        flex
                        items-center
                        gap-3
                    "
                >

                    <div
                        class="
                            flex
                            h-9
                            w-9

                            items-center
                            justify-center

                            rounded-lg

                            bg-gray-100
                            text-gray-600
                        "
                    >
                        <Truck class="w-4 h-4" />
                    </div>


                    <div>

                        <p
                            class="
                                text-sm
                                font-medium
                                text-gray-900
                            "
                        >
                            Standard delivery
                        </p>

                        <p
                            class="
                                mt-0.5
                                text-xs
                                text-gray-500
                            "
                        >
                            2–4 business days
                        </p>

                    </div>

                </div>


                <div
                    class="
                        flex
                        items-center
                        gap-3
                    "
                >

                    <span
                        class="
                            text-sm
                            font-medium
                            text-gray-900
                        "
                    >
                        KES 250
                    </span>


                    <div
                        class="
                            w-5
                            h-5

                            rounded-full

                            flex
                            items-center
                            justify-center

                            border
                        "
                        :class="
                            modelValue === 'standard'
                                ? 'border-blue-600 bg-blue-600 text-white'
                                : 'border-gray-300'
                        "
                    >

                        <Check
                            v-if="modelValue === 'standard'"
                            class="w-3 h-3"
                        />

                    </div>

                </div>

            </label>


            <!-- EXPRESS -->

            <label
                class="
                    flex
                    items-center
                    justify-between
                    gap-4

                    px-4
                    py-4

                    cursor-pointer

                    transition

                    hover:bg-gray-50
                "
                :class="
                    modelValue === 'express'
                        ? 'bg-blue-50/50'
                        : 'bg-white'
                "
            >

                <input
                    type="radio"
                    value="express"
                    :checked="modelValue === 'express'"
                    class="sr-only"
                    @change="
                        emit(
                            'update:modelValue',
                            'express'
                        )
                    "
                />


                <div
                    class="
                        flex
                        items-center
                        gap-3
                    "
                >

                    <div
                        class="
                            flex
                            h-9
                            w-9

                            items-center
                            justify-center

                            rounded-lg

                            bg-gray-100
                            text-gray-600
                        "
                    >
                        <Zap class="w-4 h-4" />
                    </div>


                    <div>

                        <p
                            class="
                                text-sm
                                font-medium
                                text-gray-900
                            "
                        >
                            Express delivery
                        </p>

                        <p
                            class="
                                mt-0.5
                                text-xs
                                text-gray-500
                            "
                        >
                            Same-day delivery
                        </p>

                    </div>

                </div>


                <div
                    class="
                        flex
                        items-center
                        gap-3
                    "
                >

                    <span
                        class="
                            text-sm
                            font-medium
                            text-gray-900
                        "
                    >
                        KES 500
                    </span>


                    <div
                        class="
                            w-5
                            h-5

                            rounded-full

                            flex
                            items-center
                            justify-center

                            border
                        "
                        :class="
                            modelValue === 'express'
                                ? 'border-blue-600 bg-blue-600 text-white'
                                : 'border-gray-300'
                        "
                    >

                        <Check
                            v-if="modelValue === 'express'"
                            class="w-3 h-3"
                        />

                    </div>

                </div>

            </label>


            <!-- PICKUP -->

            <label
                class="
                    flex
                    items-center
                    justify-between
                    gap-4

                    px-4
                    py-4

                    cursor-pointer

                    transition

                    hover:bg-gray-50
                "
                :class="
                    modelValue === 'pickup'
                        ? 'bg-blue-50/50'
                        : 'bg-white'
                "
            >

                <input
                    type="radio"
                    value="pickup"
                    :checked="modelValue === 'pickup'"
                    class="sr-only"
                    @change="
                        emit(
                            'update:modelValue',
                            'pickup'
                        )
                    "
                />


                <div
                    class="
                        flex
                        items-center
                        gap-3
                    "
                >

                    <div
                        class="
                            flex
                            h-9
                            w-9

                            items-center
                            justify-center

                            rounded-lg

                            bg-gray-100
                            text-gray-600
                        "
                    >
                        <MapPin class="w-4 h-4" />
                    </div>


                    <div>

                        <p
                            class="
                                text-sm
                                font-medium
                                text-gray-900
                            "
                        >
                            Pickup station
                        </p>

                        <p
                            class="
                                mt-0.5
                                text-xs
                                text-gray-500
                            "
                        >
                            Collect from our store
                        </p>

                    </div>

                </div>


                <div
                    class="
                        flex
                        items-center
                        gap-3
                    "
                >

                    <span
                        class="
                            text-sm
                            font-semibold
                            text-green-600
                        "
                    >
                        Free
                    </span>


                    <div
                        class="
                            w-5
                            h-5

                            rounded-full

                            flex
                            items-center
                            justify-center

                            border
                        "
                        :class="
                            modelValue === 'pickup'
                                ? 'border-blue-600 bg-blue-600 text-white'
                                : 'border-gray-300'
                        "
                    >

                        <Check
                            v-if="modelValue === 'pickup'"
                            class="w-3 h-3"
                        />

                    </div>

                </div>

            </label>

        </div>

    </section>

</template>