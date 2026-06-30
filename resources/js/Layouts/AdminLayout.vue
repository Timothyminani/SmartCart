
<template>
    <div class="flex h-screen bg-gray-100">

        <!-- Sidebar -->
        <aside class="w-64 bg-gray-900 text-white flex flex-col">

            <!-- Logo -->
            <div class="p-6 text-2xl font-bold border-b border-gray-700">
                Ecommerce Admin
            </div>

            <!-- Navigation -->
            <nav class="flex-1 p-4 space-y-2">

                <Link href="/admin/dashboard"
                      class="flex items-center gap-3 p-2 rounded hover:bg-gray-800">
                    <LayoutDashboard size="18" />
                    Dashboard
                </Link>

              <Link href="/admin/orders"
                      class="flex items-center gap-3 p-2 rounded hover:bg-gray-800">
                    <Users size="18" />
                    Users
                </Link>


                <Link href="/admin/categories"
                      class="flex items-center gap-3 p-2 rounded hover:bg-gray-800">
                    <Folder size="18" />
                    Categories
                </Link>

                <Link href="/admin/brands"
                      class="flex items-center gap-3 p-2 rounded hover:bg-gray-800">
                    <Tags size="18" />
                    Brands
                </Link>

               <div>
    <!-- Parent -->
    <button 
        @click="openProducts = !openProducts"
        class="w-full flex items-center justify-between p-2 rounded hover:bg-gray-800"
    >
        <div class="flex items-center gap-3">
            <ShoppingBag size="18" />
            <span>Products</span>
        </div>

        <ChevronDown 
            size="16" 
            :class="{'rotate-180': openProducts}" 
            class="transition"
        />
    </button>

    <!-- Dropdown -->
    <div v-if="openProducts" class="ml-8 mt-2 space-y-1">

        <Link href="/admin/products"
              class="block text-gray-400 text-sm hover:text-gray-100  hover:bg-gray-800 p-2 border-full rounded">
            All Products
        </Link>

        <Link href="/admin/products/create"
              class="block text-gray-400 text-sm hover:text-gray-100 hover:bg-gray-800 p-2 border-full rounded">
            Add Product
        </Link>

    </div>
</div>

        <Link
            href="/admin/orders"
            class="flex items-center justify-between p-2 rounded hover:bg-gray-800"
        >

            <div class="flex items-center gap-3">
                <ShoppingCart size="18" />
                <span>Orders</span>
            </div>

            <span
                v-if="pendingOrdersCount > 0"
                class="bg-red-500 text-white text-xs px-2 py-0.5 rounded-full"
            >
                {{ pendingOrdersCount }}
            </span>

        </Link>


               



            </nav>



     

 <!-- Bottom Section -->
    <div class="p-4 border-t border-gray-700">

        <!-- User Info -->
        <div class="flex items-center gap-3 mb-3">
            <UserCircle size="22" />
            <span class="text-sm font-medium">
                {{ user.name }}
            </span>
        </div>

        <!-- Logout -->
        <Link href="/logout"
              method="post"
              as="button"
              class="flex items-center gap-2 text-red-400 hover:text-red-600">

            <LogOut size="18" />
            Logout

        </Link>

    </div>





        </aside>

        <!-- Main Area -->
        <div class="flex-1 flex flex-col">

            <!-- Top Navbar -->
<header class="bg-white shadow px-6 py-4 flex justify-between items-center">

    <h1 class="text-lg font-semibold text-gray-700">
        Admin Dashboard
    </h1>

    <!-- Right Section -->
    <div class="flex items-center gap-4">

        <!-- Notification Icon -->
        <div class="relative cursor-pointer">
            <Bell class="text-gray-600 hover:text-gray-800" size="27" />

            <!-- Notification Badge -->
        <span
            v-if="pendingOrdersCount > 0"
            class="absolute -top-1 -right-1 bg-red-500 text-white text-xs
                w-5 h-5 flex items-center justify-center rounded-full"
        >
            {{ pendingOrdersCount }}
        </span>
        </div>

        <!-- User Name -->
        <span class="font-medium text-gray-700">
            Welcome, {{ user.name }}
        </span>

    </div>

</header>

            <!-- Page Content -->
            <main class="flex-1 overflow-y-auto p-6 ">

<div v-if="show && $page.props.flash.success" 
     class="bg-green-500 text-white px-4 py-3 rounded mb-4 flex justify-between items-center">

    <span>{{ $page.props.flash.success }}</span>

    <button @click="show = false" 
            class="ml-4 text-white hover:text-gray-200 transition">
        ✕
    </button>
</div>

<div v-if="show && $page.props.flash.error" 
     class="bg-red-500 text-white px-4 py-3 rounded mb-4 flex justify-between items-center">

    <span>{{ $page.props.flash.error }}</span>

    <button @click="show = false" 
            class="ml-4 text-white hover:text-gray-200 transition">
        ✕
    </button>
</div>



                <slot />
            </main>

        </div>
    </div>
</template>




<script setup>
import { Link} from '@inertiajs/vue3'
import { ref } from 'vue'
import { ChevronDown } from 'lucide-vue-next'
import { watch } from 'vue'
import { usePage } from '@inertiajs/vue3'

import {
    LayoutDashboard,
    Folder,
    Tags,
    ShoppingBag,
    ShoppingCart,
    UserCircle,
    LogOut,
    Settings,
    Bell,
    Users
} from 'lucide-vue-next'


const page = usePage()
const show = ref(false)
const user = page.props.auth.user
const openProducts = ref(false)

const pendingOrdersCount =
    page.props.admin.pendingOrdersCount


watch(
    () => page.props.flash.success,
    (value) => {
        if (value) {
            show.value = true

            setTimeout(() => {
                show.value = false
            }, 3000)
        }
    },
    { immediate: true }
)



</script>