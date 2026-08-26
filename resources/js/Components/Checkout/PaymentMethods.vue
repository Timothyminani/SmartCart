<script setup>
import {
    Banknote,
    Check,
} from 'lucide-vue-next'

import mpesaLogo from '@/assets/images/mpesa-logo.jpg'


defineProps({
    modelValue: {
        type: String,
        default: 'cod',
    },
})


const emit = defineEmits([
    'update:modelValue',
])
</script>


<template>

    <section>

        <!-- HEADER -->

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
                    2
                </span>


                <h2
                    class="
                        text-lg
                        font-semibold
                        text-gray-900
                    "
                >
                    Payment
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
                Choose how you'd like to pay.
            </p>

        </div>


        <div
            class="
                grid
                sm:grid-cols-2

                gap-3
            "
        >

            <!-- COD -->

            <label
                class="
                    relative

                    flex
                    items-start
                    gap-3

                    p-4

                    rounded-xl

                    border

                    cursor-pointer

                    transition
                "
                :class="
                    modelValue === 'cod'
                        ? 'border-blue-500 bg-blue-50/50'
                        : 'border-gray-200 bg-white hover:border-gray-300'
                "
            >

                <input
                    type="radio"
                    value="cod"
                    :checked="modelValue === 'cod'"
                    class="sr-only"
                    @change="
                        emit(
                            'update:modelValue',
                            'cod'
                        )
                    "
                />


                <div
                    class="
                        flex
                        h-10
                        w-10

                        shrink-0

                        items-center
                        justify-center

                        rounded-lg

                        bg-gray-100
                        text-gray-700
                    "
                >
                    <Banknote class="w-5 h-5" />
                </div>


                <div class="min-w-0">

                    <p
                        class="
                            text-sm
                            font-semibold
                            text-gray-900
                        "
                    >
                        Cash on delivery
                    </p>

                    <p
                        class="
                            mt-1

                            text-xs
                            leading-5
                            text-gray-500
                        "
                    >
                        Pay when your order arrives.
                    </p>

                </div>


                <div
                    class="
                        absolute
                        right-3
                        top-3

                        w-5
                        h-5

                        rounded-full

                        flex
                        items-center
                        justify-center

                        border
                    "
                    :class="
                        modelValue === 'cod'
                            ? 'border-blue-600 bg-blue-600 text-white'
                            : 'border-gray-300'
                    "
                >

                    <Check
                        v-if="modelValue === 'cod'"
                        class="w-3 h-3"
                    />

                </div>

            </label>


            <!-- MPESA -->

            <label
                class="
                    relative

                    flex
                    items-start
                    gap-3

                    p-4

                    rounded-xl

                    border

                    cursor-pointer

                    transition
                "
                :class="
                    modelValue === 'mpesa'
                        ? 'border-blue-500 bg-blue-50/50'
                        : 'border-gray-200 bg-white hover:border-gray-300'
                "
            >

                <input
                    type="radio"
                    value="mpesa"
                    :checked="
                        modelValue === 'mpesa'
                    "
                    class="sr-only"
                    @change="
                        emit(
                            'update:modelValue',
                            'mpesa'
                        )
                    "
                />


                <div
                    class="
                        flex
                        h-10
                        w-12

                        shrink-0

                        items-center
                        justify-center

                        rounded-lg

                        border
                        border-gray-100

                        bg-white

                        p-1
                    "
                >

                    <img
                        :src="mpesaLogo"
                        alt="M-Pesa"
                        class="
                            max-h-full
                            max-w-full
                            object-contain
                        "
                    />

                </div>


                <div class="min-w-0 pr-5">

                    <p
                        class="
                            text-sm
                            font-semibold
                            text-gray-900
                        "
                    >
                        M-Pesa
                    </p>

                    <p
                        class="
                            mt-1

                            text-xs
                            leading-5
                            text-gray-500
                        "
                    >
                        Continue to secure M-Pesa
                        payment after placing your order.
                    </p>

                </div>


                <div
                    class="
                        absolute
                        right-3
                        top-3

                        w-5
                        h-5

                        rounded-full

                        flex
                        items-center
                        justify-center

                        border
                    "
                    :class="
                        modelValue === 'mpesa'
                            ? 'border-blue-600 bg-blue-600 text-white'
                            : 'border-gray-300'
                    "
                >

                    <Check
                        v-if="
                            modelValue === 'mpesa'
                        "
                        class="w-3 h-3"
                    />

                </div>

            </label>

        </div>

    </section>

</template>