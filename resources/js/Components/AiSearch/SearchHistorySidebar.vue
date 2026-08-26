<script setup>
import { ref } from 'vue'
import {
    PanelLeft,
    RotateCcw,
} from 'lucide-vue-next'


/*
|--------------------------------------------------------------------------
| PROPS
|--------------------------------------------------------------------------
*/

defineProps({
    searches: {
        type: Array,
        default: () => [],
    },
})


/*
|--------------------------------------------------------------------------
| EMITS
|--------------------------------------------------------------------------
*/

const emit = defineEmits([
    'restore',
])


/*
|--------------------------------------------------------------------------
| STATE
|--------------------------------------------------------------------------
*/

const sidebarOpen = ref(true)


/*
|--------------------------------------------------------------------------
| SIDEBAR
|--------------------------------------------------------------------------
*/

const toggleSidebar = () => {
    sidebarOpen.value =
        !sidebarOpen.value
}


/*
|--------------------------------------------------------------------------
| RESTORE SEARCH
|--------------------------------------------------------------------------
*/

const restoreSearch = (search) => {
    emit('restore', search)
}


/*
|--------------------------------------------------------------------------
| FORMAT TIME
|--------------------------------------------------------------------------
*/

const formatTime = (date) => {

    if (!date) {
        return ''
    }

    const now = new Date()
    const created = new Date(date)

    const diffInSeconds =
        Math.floor(
            (now - created) / 1000
        )

    if (diffInSeconds < 60) {
        return 'Just now'
    }

    const diffInMinutes =
        Math.floor(
            diffInSeconds / 60
        )

    if (diffInMinutes < 60) {
        return `${diffInMinutes} min ago`
    }

    const diffInHours =
        Math.floor(
            diffInMinutes / 60
        )

    if (diffInHours < 24) {
        return `${diffInHours} hr ago`
    }

    const diffInDays =
        Math.floor(
            diffInHours / 24
        )

    if (diffInDays < 7) {
        return `${diffInDays} ${
            diffInDays === 1
                ? 'day'
                : 'days'
        } ago`
    }

    return created.toLocaleDateString()
}
</script>


<template>

   
    <!-- ========================================================= -->
    <!-- DESKTOP SIDEBAR -->
    <!-- ========================================================= -->

    <aside
        :class="[
            `
                hidden
                lg:flex

                bg-white

                border-r
                border-gray-100

                flex-col

                shrink-0

                transition-[width]
                duration-300
                ease-in-out
            `,

            sidebarOpen
                ? 'w-[268px]'
                : 'w-[72px]'
        ]"
    >

        <!-- ===================================================== -->
        <!-- TOP -->
        <!-- ===================================================== -->

        <div
            :class="[
                `
                    border-b
                    border-gray-100
                `,

                sidebarOpen
                    ? 'px-4 py-5'
                    : 'px-3 py-5'
            ]"
        >

            <div
                :class="[
                    'flex items-center',

                    sidebarOpen
                        ? 'justify-between gap-3'
                        : 'justify-center'
                ]"
            >

                <!-- IDENTITY -->

                <div
                    v-if="sidebarOpen"
                    class="
                        min-w-0
                    "
                >

                    <h2
                        class="
                            text-base
                            font-bold
                            text-gray-900
                            leading-tight
                        "
                    >
                        SmartCart AI
                    </h2>

                    <p
                        class="
                            text-xs
                            text-gray-400
                            mt-1
                        "
                    >
                        Your shopping assistant
                    </p>

                </div>


                <!-- TOGGLE -->

                <button
                    @click="toggleSidebar"
                    type="button"
                    class="
                        w-9
                        h-9

                        shrink-0

                        rounded-xl

                        flex
                        items-center
                        justify-center

                        text-gray-500

                        hover:text-gray-800
                        hover:bg-gray-100

                        transition
                    "
                    :title="
                        sidebarOpen
                            ? 'Collapse sidebar'
                            : 'Expand sidebar'
                    "
                >

                    <PanelLeft
                        class="
                            w-4.5
                            h-4.5
                        "
                    />

                </button>

            </div>

        </div>



        <!-- ===================================================== -->
        <!-- HISTORY -->
        <!-- ===================================================== -->

        <div
            :class="[
                `
                    flex-1
                    overflow-y-auto
                `,

                sidebarOpen
                    ? 'px-3 py-5'
                    : 'px-2 py-5'
            ]"
        >

            <!-- HISTORY HEADER -->

            <div
                v-if="sidebarOpen"
                class="
                    flex
                    items-center
                    justify-between
                    gap-3

                    px-2
                    mb-3
                "
            >

                <h3
                    class="
                        text-[11px]
                        font-semibold
                        uppercase
                        tracking-wider
                        text-gray-400
                    "
                >
                    Recent Searches
                </h3>


                <span
                    v-if="searches.length"
                    class="
                        min-w-6
                        h-6

                        px-2

                        rounded-full

                        bg-gray-100

                        text-[10px]
                        font-medium
                        text-gray-500

                        flex
                        items-center
                        justify-center
                    "
                >
                    {{ searches.length }}
                </span>

            </div>



            <!-- ================================================= -->
            <!-- EMPTY HISTORY -->
            <!-- ================================================= -->

            <div
                v-if="
                    sidebarOpen &&
                    !searches.length
                "
                class="
                    px-3
                    py-8
                    text-center
                "
            >

                <div
                    class="
                        w-10
                        h-10

                        mx-auto

                        rounded-xl

                        bg-gray-50

                        flex
                        items-center
                        justify-center
                    "
                >

                    <RotateCcw
                        class="
                            w-4
                            h-4
                            text-gray-300
                        "
                    />

                </div>


                <p
                    class="
                        text-xs
                        leading-5
                        text-gray-400
                        mt-3
                    "
                >
                    Your recent searches
                    will appear here.
                </p>

            </div>



            <!-- ================================================= -->
            <!-- SEARCH HISTORY -->
            <!-- ================================================= -->

            <div
                v-else
                class="
                    space-y-1
                "
            >

                <button
                    v-for="search in searches"
                    :key="search.id"
                    @click="restoreSearch(search)"
                    type="button"
                    class="
                        group

                        w-full

                        rounded-xl

                        hover:bg-blue-50

                        transition
                    "
                    :title="
                        !sidebarOpen
                            ? search.query
                            : undefined
                    "
                >

                    <!-- ========================================== -->
                    <!-- EXPANDED -->
                    <!-- ========================================== -->

                    <div
                        v-if="sidebarOpen"
                        class="
                            flex
                            items-start
                            gap-3

                            px-2.5
                            py-3

                            text-left
                        "
                    >

                        <!-- ICON -->

                        <div
                            class="
                                w-8
                                h-8

                                shrink-0

                                rounded-lg

                                bg-gray-50

                                flex
                                items-center
                                justify-center

                                group-hover:bg-white

                                transition
                            "
                        >

                            <RotateCcw
                                class="
                                    w-3.5
                                    h-3.5

                                    text-gray-400

                                    group-hover:text-blue-500

                                    transition
                                "
                            />

                        </div>


                        <!-- TEXT -->

                        <div
                            class="
                                flex-1
                                min-w-0
                            "
                        >

                            <p
                                class="
                                    text-[13px]
                                    font-medium
                                    leading-5

                                    text-gray-700

                                    line-clamp-2

                                    group-hover:text-blue-700

                                    transition
                                "
                            >
                                {{ search.query }}
                            </p>


                            <p
                                class="
                                    text-[11px]
                                    text-gray-400
                                    mt-1
                                "
                            >
                                {{
                                    formatTime(
                                        search.created_at
                                    )
                                }}
                            </p>

                        </div>

                    </div>



                    <!-- ========================================== -->
                    <!-- COLLAPSED -->
                    <!-- ========================================== -->

                    <div
                        v-else
                        class="
                            flex
                            justify-center
                            py-2
                        "
                    >

                        <div
                            class="
                                w-9
                                h-9

                                rounded-xl

                                flex
                                items-center
                                justify-center

                                text-gray-400

                                group-hover:bg-blue-50
                                group-hover:text-blue-600

                                transition
                            "
                        >

                            <RotateCcw
                                class="
                                    w-4
                                    h-4
                                "
                            />

                        </div>

                    </div>

                </button>

            </div>

        </div>

    </aside>

</template>