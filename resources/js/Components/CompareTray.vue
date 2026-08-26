<script setup>
import { computed } from 'vue'
import { router, usePage } from '@inertiajs/vue3'
import { useCompare } from '@/composables/useCompare'

const page = usePage()

const {
    compareItems,
    showCompareTray,
    removeFromCompare,
    compareMessage,
} = useCompare()

const isComparePage = computed(() => {
    return page.url.startsWith('/compare')
})

const goToCompare = () => {

    if (compareItems.value.length < 2) {
        return
    }

    router.get(
        route('compare.index'),
        {
            products: compareItems.value.map(
                item => item.id
            )
        }
    )
}
</script>


<template>

    <!-- ========================================================= -->
    <!-- COMPARE TRAY -->
    <!-- ========================================================= -->

    <transition name="slide-up">

        <div
            v-if="
                compareItems.length &&
                showCompareTray &&
                !isComparePage
            "
            class="fixed
                   bottom-4
                   left-1/2
                   -translate-x-1/2
                   z-50
                   bg-white
                   border
                   shadow-2xl
                   rounded-2xl
                   px-4
                   py-3
                   w-[95%]
                   max-w-2xl"
        >

            <div
                class="flex
                       items-center
                       justify-between
                       gap-4"
            >

                <!-- PRODUCTS -->

                <div
                    class="flex
                           items-center
                           gap-2
                           overflow-x-auto"
                >

                    <template
                        v-for="(item, index) in compareItems"
                        :key="item.id"
                    >

                        <!-- PRODUCT CARD -->

                        <div
                            class="flex
                                   items-center
                                   gap-2
                                   bg-gray-50
                                   rounded-xl
                                   px-2
                                   py-2
                                   border
                                   shrink-0"
                        >

                            <!-- IMAGE -->

                            <img
                                :src="
                                    item.image
                                        ? `/storage/${item.image}`
                                        : '/placeholder.png'
                                "
                                :alt="item.name"
                                class="w-12
                                       h-12
                                       rounded-lg
                                       object-cover"
                            />


                            <!-- INFO -->

                            <div
                                class="hidden
                                       sm:block
                                       max-w-[120px]"
                            >

                                <p
                                    class="text-xs
                                           font-medium
                                           line-clamp-1"
                                >
                                    {{ item.name }}
                                </p>


                                <p
                                    class="text-xs
                                           text-blue-600
                                           font-bold"
                                >
                                    KES
                                    {{
                                        Number(
                                            item.sale_price || 0
                                        ).toLocaleString()
                                    }}
                                </p>

                            </div>


                            <!-- REMOVE -->

                            <button
                                @click="
                                    removeFromCompare(
                                        item.id
                                    )
                                "
                                type="button"
                                class="text-gray-400
                                       hover:text-red-500
                                       text-xs
                                       ml-1"
                                title="Remove from compare"
                            >
                                ✕
                            </button>

                        </div>


                        <!-- VS -->

                        <div
                            v-if="
                                index <
                                compareItems.length - 1
                            "
                            class="text-xs
                                   font-bold
                                   text-gray-400
                                   px-1"
                        >
                            VS
                        </div>

                    </template>

                </div>


                <!-- ACTION AREA -->

                <div
                    class="flex
                           flex-col
                           items-end
                           shrink-0"
                >

                    <p
                        v-if="
                            compareItems.length < 2
                        "
                        class="text-[11px]
                               text-gray-500
                               mb-2
                               whitespace-nowrap"
                    >
                        Select one more product to compare
                    </p>


                    <button
                        @click="goToCompare"
                        :disabled="
                            compareItems.length < 2
                        "
                        type="button"
                        class="px-5
                               py-3
                               rounded-xl
                               text-sm
                               font-medium
                               transition
                               whitespace-nowrap"
                        :class="
                            compareItems.length < 2
                                ? 'bg-gray-200 text-gray-400 cursor-not-allowed'
                                : 'bg-blue-600 hover:bg-blue-700 text-white'
                        "
                    >
                        Compare
                    </button>

                </div>

            </div>

        </div>

    </transition>


    <!-- ========================================================= -->
    <!-- COMPARE MESSAGE -->
    <!-- ========================================================= -->

    <transition name="fade">

        <div
            v-if="compareMessage"
            class="fixed
                   bottom-28
                   left-1/2
                   -translate-x-1/2
                   bg-gray-900
                   text-white
                   text-sm
                   px-4
                   py-2
                   rounded-xl
                   shadow-lg
                   z-[100]"
        >
            {{ compareMessage }}
        </div>

    </transition>

</template>


<style scoped>

.slide-up-enter-active,
.slide-up-leave-active {
    transition: all 0.3s ease;
}

.slide-up-enter-from,
.slide-up-leave-to {
    opacity: 0;
    transform: translate(-50%, 20px);
}


.fade-enter-active,
.fade-leave-active {
    transition: all 0.3s ease;
}

.fade-enter-from,
.fade-leave-to {
    opacity: 0;
    transform: translate(-50%, 10px);
}

</style>