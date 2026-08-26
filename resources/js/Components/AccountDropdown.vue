<script setup>
import { ref, computed, onMounted, onBeforeUnmount } from 'vue'
import { Link, router, usePage } from '@inertiajs/vue3'

import {
    User,
    Package,
    Heart,
    LogOut,
    ChevronDown,
    LogIn,
    UserPlus,
} from 'lucide-vue-next'


/*
|--------------------------------------------------------------------------
| EMITS
|--------------------------------------------------------------------------
*/

const emit = defineEmits([
    'open-wishlist'
])


/*
|--------------------------------------------------------------------------
| PAGE / USER
|--------------------------------------------------------------------------
*/

const page = usePage()

const user = computed(() => {
    return page.props.auth?.user || null
})

const isLoggedIn = computed(() => {
    return !!user.value
})


/*
|--------------------------------------------------------------------------
| DROPDOWN
|--------------------------------------------------------------------------
*/

const open = ref(false)
const dropdownRef = ref(null)

const toggleDropdown = () => {
    open.value = !open.value
}

const closeDropdown = () => {
    open.value = false
}


/*
|--------------------------------------------------------------------------
| USER INITIAL
|--------------------------------------------------------------------------
*/

const userInitial = computed(() => {

    if (!user.value?.name) {
        return 'U'
    }

    return user.value.name
        .trim()
        .charAt(0)
        .toUpperCase()
})


/*
|--------------------------------------------------------------------------
| WISHLIST
|--------------------------------------------------------------------------
*/

const openWishlist = () => {

    closeDropdown()

    emit('open-wishlist')
}


/*
|--------------------------------------------------------------------------
| LOGOUT
|--------------------------------------------------------------------------
*/

const logout = () => {

    closeDropdown()

    router.post(
        route('logout')
    )
}


/*
|--------------------------------------------------------------------------
| CLICK OUTSIDE
|--------------------------------------------------------------------------
*/

const handleClickOutside = (event) => {

    if (
        dropdownRef.value &&
        !dropdownRef.value.contains(event.target)
    ) {
        closeDropdown()
    }
}


/*
|--------------------------------------------------------------------------
| LIFECYCLE
|--------------------------------------------------------------------------
*/

onMounted(() => {

    document.addEventListener(
        'click',
        handleClickOutside
    )

})


onBeforeUnmount(() => {

    document.removeEventListener(
        'click',
        handleClickOutside
    )

})
</script>


<template>

    <div
        ref="dropdownRef"
        class="relative"
    >

        <!-- ========================================================= -->
        <!-- ACCOUNT BUTTON -->
        <!-- ========================================================= -->

        <button
            @click.stop="toggleDropdown"
            type="button"
            class="flex items-center gap-1.5
                   text-gray-600
                   hover:text-blue-600
                   transition"
        >

            <div
                class="w-9 h-9
                       rounded-full
                       flex items-center
                       justify-center
                       border border-black
                       bg-white
                      
                       transition"
            >

                <User
                    v-if="!isLoggedIn"
                    class="w-5 h-5"
                />

                <span
                    v-else
                    class="text-lg
                           font-bold
                           text-blue-600"
                >
                    {{ userInitial }}
                </span>

            </div>


            <ChevronDown
                class="w-4 h-4
                       transition-transform
                       duration-200"
                :class="open
                    ? 'rotate-180'
                    : ''
                "
            />

        </button>


        <!-- ========================================================= -->
        <!-- DROPDOWN -->
        <!-- ========================================================= -->

        <Transition name="dropdown">

            <div
                v-if="open"
                class="absolute
                       right-0
                       mt-3
                       w-[290px]
                       bg-white
                       border border-gray-100
                       rounded-2xl
                       shadow-2xl
                       overflow-hidden
                       z-[100]"
            >

                <!-- ================================================= -->
                <!-- GUEST -->
                <!-- ================================================= -->

                <template v-if="!isLoggedIn">

                    <!-- HEADER -->

                    <div
                        class="px-5 py-5
                               border-b
                               border-gray-100"
                    >

                        <div
                            class="w-11 h-11
                                   rounded-full
                                   bg-blue-50
                                   flex items-center
                                   justify-center
                                   mb-3"
                        >

                            <User
                                class="w-5 h-5
                                       text-blue-600"
                            />

                        </div>


                        <h3
                            class="font-bold
                                   text-gray-900"
                        >
                            Welcome to SmartCart
                        </h3>


                        <p
                            class="text-sm
                                   text-gray-500
                                   mt-1
                                   leading-5"
                        >
                            Sign in to manage your orders,
                            account and saved items.
                        </p>

                    </div>


                    <!-- ACTIONS -->

                    <div
                        class="p-4
                               space-y-3"
                    >

                        <Link
                            :href="route('login')"
                            @click="closeDropdown"
                            class="w-full
                                   flex items-center
                                   justify-center
                                   gap-2
                                   bg-blue-600
                                   hover:bg-blue-700
                                   text-white
                                   py-2.5
                                   rounded-xl
                                   text-sm
                                   font-semibold
                                   transition"
                        >

                            <LogIn
                                class="w-4 h-4"
                            />

                            Sign In

                        </Link>


                        <Link
                            :href="route('register')"
                            @click="closeDropdown"
                            class="w-full
                                   flex items-center
                                   justify-center
                                   gap-2
                                   border
                                   border-gray-200
                                   hover:bg-gray-50
                                   text-gray-700
                                   py-2.5
                                   rounded-xl
                                   text-sm
                                   font-semibold
                                   transition"
                        >

                            <UserPlus
                                class="w-4 h-4"
                            />

                            Create Account

                        </Link>

                    </div>

                </template>


                <!-- ================================================= -->
                <!-- AUTHENTICATED -->
                <!-- ================================================= -->

                <template v-else>

                    <!-- USER HEADER -->

                    <div
                        class="px-5 py-5
                               border-b
                               border-gray-100"
                    >

                        <div
                            class="flex
                                   items-center
                                   gap-3"
                        >

                            <div
                                class="w-11 h-11
                                       rounded-full
                                       bg-blue-100
                                       text-blue-600
                                       flex items-center
                                       justify-center
                                       font-bold"
                            >
                                {{ userInitial }}
                            </div>


                            <div
                                class="min-w-0"
                            >

                                <p
                                    class="font-semibold
                                           text-gray-900
                                           truncate"
                                >
                                    {{ user.name }}
                                </p>


                                <p
                                    class="text-xs
                                           text-gray-500
                                           truncate
                                           mt-0.5"
                                >
                                    {{ user.email }}
                                </p>

                            </div>

                        </div>

                    </div>


                    <!-- MENU -->

                    <div
                        class="p-2"
                    >

                        <!-- PROFILE -->

                        <Link
                            :href="route('profile.edit')"
                            @click="closeDropdown"
                            class="flex
                                   items-center
                                   gap-3
                                   px-3 py-2.5
                                   rounded-xl
                                   text-sm
                                   text-gray-700
                                   hover:bg-gray-50
                                   hover:text-blue-600
                                   transition"
                        >

                            <div
                                class="w-8 h-8
                                       rounded-lg
                                       bg-gray-100
                                       flex items-center
                                       justify-center"
                            >

                                <User
                                    class="w-4 h-4"
                                />

                            </div>

                            <span>
                                My Profile
                            </span>

                        </Link>


                        <!-- ORDERS -->

                        <Link
                            :href="route('orders.index')"
                            @click="closeDropdown"
                            class="flex
                                   items-center
                                   gap-3
                                   px-3 py-2.5
                                   rounded-xl
                                   text-sm
                                   text-gray-700
                                   hover:bg-gray-50
                                   hover:text-blue-600
                                   transition"
                        >

                            <div
                                class="w-8 h-8
                                       rounded-lg
                                       bg-gray-100
                                       flex items-center
                                       justify-center"
                            >

                                <Package
                                    class="w-4 h-4"
                                />

                            </div>

                            <span>
                                My Orders
                            </span>

                        </Link>


                        <!-- WISHLIST -->

                        <button
                            @click="openWishlist"
                            type="button"
                            class="w-full
                                   flex
                                   items-center
                                   gap-3
                                   px-3 py-2.5
                                   rounded-xl
                                   text-sm
                                   text-gray-700
                                   hover:bg-gray-50
                                   hover:text-red-500
                                   transition"
                        >

                            <div
                                class="w-8 h-8
                                       rounded-lg
                                       bg-gray-100
                                       flex items-center
                                       justify-center"
                            >

                                <Heart
                                    class="w-4 h-4"
                                />

                            </div>

                            <span>
                                Wishlist
                            </span>

                        </button>

                    </div>


                    <!-- LOGOUT -->

                    <div
                        class="border-t
                               border-gray-100
                               p-2"
                    >

                        <button
                            @click="logout"
                            type="button"
                            class="w-full
                                   flex
                                   items-center
                                   gap-3
                                   px-3 py-2.5
                                   rounded-xl
                                   text-sm
                                   text-red-500
                                   hover:bg-red-50
                                   transition"
                        >

                            <div
                                class="w-8 h-8
                                       rounded-lg
                                       bg-red-50
                                       flex items-center
                                       justify-center"
                            >

                                <LogOut
                                    class="w-4 h-4"
                                />

                            </div>

                            <span>
                                Logout
                            </span>

                        </button>

                    </div>

                </template>

            </div>

        </Transition>

    </div>

</template>


<style scoped>

.dropdown-enter-active,
.dropdown-leave-active {
    transition:
        opacity 0.18s ease,
        transform 0.18s ease;
}

.dropdown-enter-from,
.dropdown-leave-to {
    opacity: 0;
    transform: translateY(-6px) scale(0.98);
}

</style>