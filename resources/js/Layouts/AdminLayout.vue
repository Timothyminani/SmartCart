
<template>
    <div class="flex p-2 gap-2 h-screen bg-gradient-to-r from-blue-50 via-white to-white ">

  <!-- Sidebar -->
<aside
    :class="[
        collapsed ? 'w-24' : 'w-72',
        'bg-white/20 backdrop-blur-2xl border border-white/30 rounded-2xl shadow-xl flex flex-col transition-all duration-300'
    ]"
>

    <!-- ================================= -->
    <!-- Logo -->
    <!-- ================================= -->
    <div class="flex items-center justify-between p-3 border-b-2 border-gray-100">

       

            <img
            v-if="!collapsed"
                :src="logo"
                alt="SmartCart Logo"
                class="h-16 w-60 "
            />

            
                <img
                    v-else
                    :src="logo"
                    class="w-10 h-10 object-contain"
                />


            <div>
                

                
           

        </div>

       <button
       v-if="!collapsed"
    @click="collapsed = !collapsed"
    class="p-2 rounded-lg hover:bg-white/30 transition"
>
    <PanelLeft 
    class="w-7 h-7 text-gray-700" 
    />

  
    
</button>

 <button
v-if="collapsed"
    @click="collapsed=false"
    class="p-2 rounded-lg hover:bg-white/30 transition"
>
   
  <PanelRight 
    class="w-7 h-7 text-gray-700" 
   />
    
</button>




    </div>



    <!-- ================================= -->
    <!-- Navigation -->
    <!-- ================================= -->

    <nav class="flex-1  px-4 py-5 text-gray-700 overflow-y-auto">

       

        <Link
            href="/admin/dashboard"
            :class="[
                'flex items-center justify-between px-3 py-3 rounded-xl transition font-bold mb-1',
                $page.url.startsWith('/admin/dashboard')
                    ? 'bg-blue-600 text-white shadow-xl'
                    : 'hover:bg-white'
            ]"
        >

            <div class="flex items-center gap-3">
                <LayoutDashboard size="21" class="font-bold" />
                <span v-if="!collapsed">Dashboard</span>
            </div>

            <ChevronRight size="18"/>

        </Link>



        <!-- USERS -->

        <Link
            href="/admin/users"
            :class="[
                'flex items-center justify-between px-3 py-3 rounded-xl transition font-bold mb-1',
                $page.url.startsWith('/admin/users')
                    ? 'bg-blue-600 text-white shadow-lg'
                    : 'hover:bg-white'
            ]"
        >

            <div class="flex items-center gap-3  ">
                <Users size="21" class="font-bold"/>
                <span v-if="!collapsed">Customers</span>
            </div>

            <ChevronRight size="16"/>

        </Link>



      

        <Link
            href="/admin/categories"
            :class="[
                'flex items-center justify-between px-3 py-3 rounded-xl transition font-bold mb-1',
                $page.url.startsWith('/admin/categories')
                    ? 'bg-blue-600 text-white shadow-lg'
                    : 'hover:bg-white'
            ]"
        >

            <div class="flex items-center gap-3">
                <Folder size="21" class="font-bold"/>
                <span v-if="!collapsed">Categories</span>
            </div>

            <ChevronRight size="18"/>

        </Link>



        <Link
            href="/admin/brands"
            :class="[
                'flex items-center justify-between px-3 py-3 rounded-xl transition font-bold mb-1',
                $page.url.startsWith('/admin/brands')
                    ? 'bg-blue-600 text-white shadow-lg'
                    : 'hover:bg-white'
            ]"
        >

            <div class="flex items-center gap-3">
                <Tags size="21" class="font-bold"/>
                <span v-if="!collapsed">Brands</span>
            </div>

            <ChevronRight size="18"/>

        </Link>



        <!-- PRODUCTS -->

        <button
            @click="openProducts = !openProducts"
            class="w-full flex items-center justify-between px-3 py-3 rounded-xl hover:bg-white/30 transition"
        >

            <div class="flex items-center font-bold gap-3">

                <ShoppingBag size="21" class="font-bold"/>

                <span v-if="!collapsed">Products</span>

                <span
                    v-if="lowStockCount && !collapsed"
                    class="w-6 h-6 rounded-full bg-red-500 text-white flex items-center justify-center text-xs"
                >
                    {{ lowStockCount }}
                </span>

            </div>

            <ChevronDown
            v-if="!collapsed"
                size="21"
                class="transition"
                :class="{ 'rotate-180': openProducts }"
            />

        </button>


        <div
            v-if="openProducts"
            class="ml-10 mt-2 space-y-2"
        >

            <Link
                href="/admin/products"
                class="block py-2 px-3 rounded-lg text-sm hover:bg-white/30"
            >
                All Products
            </Link>

            <Link
                href="/admin/products/create"
                class="block py-2 px-3 rounded-lg text-sm hover:bg-white/30"
            >
                Add Product
            </Link>

        </div>



       


        <Link
            href="/admin/orders"
            :class="[
                'flex items-center justify-between px-3 py-3 rounded-xl font-bold transition',
                $page.url.startsWith('/admin/orders')
                    ? 'bg-blue-600 text-white shadow-lg'
                    : 'hover:bg-white'
            ]"
        >

            <div class="flex items-center gap-3">

                <ShoppingCart size="21" class="font-bold"/>

                <span v-if="!collapsed">Orders</span>

            </div>

            <span
                v-if="pendingOrdersCount && !collapsed"
                class="w-6 h-6 rounded-full bg-red-500 text-white flex items-center justify-center text-xs"
            >
                {{ pendingOrdersCount }}
            </span>

        </Link>

    </nav>



    <!-- ================================= -->
    <!-- User -->
    <!-- ================================= -->

    <div class=" border-t-2 border-gray-100">

        <div class="flex flex-col  bg-white/20 rounded-xl p-4 gap-2">

            <div class="flex items-center gap-3 mb-2">

                <UserCircle
                    class="text-gray-600"
                    size="30"
                />

                <div>

                    <p 
                    v-if="!collapsed"
                    class="font-semibold text-gray-800">
                        {{ user.name }}
                    </p>

                    <p 
                    v-if="!collapsed"
                    class="text-xs text-gray-500">
                        Administrator
                    </p>

                </div>

            </div>

            <Link
                href="/logout"
                method="post"
                as="button"
                class="w-full flex  gap-2 text-red-600 px-1  rounded-lg transition"
            >

                <LogOut size="21"/>

               <span v-if="!collapsed">Logout</span>

            </Link>

        </div>

    </div>

</aside>



        <!-- Main Area -->
        <div class="flex-1 flex flex-col">

            <!-- Top Navbar -->
<header class="bg-white/15 backdrop-blur-xl border-1 shadow px-6 py-4 flex justify-between items-center rounded-xl  border">

<div class="flex items-center gap-4">

    <button
     v-if="showBackButton"
        @click="goBack"
        class="p-2 rounded-xl hover:bg-white/40 transition"
    >
        <ArrowLeft class="w-5 h-5 text-gray-700" />
    </button>

    <div>
        <h1 class="text-2xl font-bold text-gray-800">
            {{ title }}
        </h1>

        <p class="text-sm text-gray-500">
            {{ subtitle }}
        </p>
    </div>

</div>


<!-- Search -->

<div class="hidden lg:block w-96">
    <div class="relative">

        <!-- Search Icon -->
        <Search
            class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 pointer-events-none"
            :size="18"
        />

        <!-- Input -->
        <input
            type="text"
            placeholder="Search products, orders, customers..."
            class="w-full
                   pl-11 pr-4 py-2.5
                   rounded-xl
                   bg-white
                   text-gray-700
                   placeholder:text-gray-400
                   border border-gray-200
                   shadow-sm
                   focus:outline-none
                   focus:ring-2
                   focus:ring-blue-500
                   focus:border-blue-500
                   transition-all"
        />

    </div>
</div>





    <!-- Right Section -->
    <div class="flex items-center gap-4">

        <!-- Notification Icon -->
   
<div class="relative">

    <!-- Bell Button -->
    <button
        @click="showNotifications = !showNotifications"
        class="relative"
    >

        <Bell
            class="text-gray-600 hover:text-gray-800"
            size="27"
        />

        <!-- Badge -->
        <span
            v-if="unreadNotificationsCount > 0"
            class="absolute -top-1 -right-1 bg-red-500 text-white text-xs
                   w-5 h-5 flex items-center justify-center rounded-full"
        >
            {{ unreadNotificationsCount }}
        </span>

    </button>

    <!-- Dropdown -->
    <div
        v-if="showNotifications"
        class="absolute right-0 mt-3 w-96 bg-white rounded-2xl
               shadow-xl border z-50 overflow-hidden"
    >

        <!-- Header -->
        <div class="p-4 border-b">

            <h3 class="font-semibold text-gray-800">
                Notifications
            </h3>

        </div>

        <!-- Notifications -->
        <div
            v-if="notifications.length"
            class="max-h-96 overflow-y-auto"
        >

            <Link
                v-for="notification in notifications"
                :key="notification.id"
                :href="`/admin/notifications/${notification.id}/read`"
                 method="patch"
                 as="button"
                 class="block w-full text-left p-4 border-b hover:bg-gray-50 transition"
            >

                <div class="flex justify-between items-start">

                    <div>

                        <p class="font-medium text-gray-800">
                            {{ notification.title }}
                        </p>

                        <p class="text-sm text-gray-500 mt-1">
                            {{ notification.message }}
                        </p>

                    </div>

                    <!-- unread indicator -->
                    <span
                        v-if="!notification.is_read"
                        class="w-2 h-2 rounded-full bg-blue-500 mt-2"
                    ></span>

                </div>

                <p class="text-xs text-gray-400 mt-2">
                    {{ new Date(notification.created_at)
                        .toLocaleString() }}
                </p>

            </Link>

        </div>

        <!-- Empty state -->
        <div
            v-else
            class="p-6 text-center text-gray-500"
        >
            No notifications
        </div>

    </div>

</div>

<!-- User Profile -->
<div class="flex items-center gap-3">

    <!-- Avatar -->
    <div
        class="w-10 h-10 rounded-full
               bg-gradient-to-br from-blue-500 to-indigo-600
               text-white
               flex items-center justify-center
               font-semibold
               shadow-md"
    >
        {{ user.name.charAt(0).toUpperCase() }}
    </div>

  

</div>
       

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
import logo from '@/Assets/Images/logo4test.png'
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
    Users,
    PanelLeft,
    PanelRight,
    ArrowLeft,
    Search
} from 'lucide-vue-next'


const goBack = () => {
    window.history.back()
}

const page = usePage()
const show = ref(false)
const user = page.props.auth.user
const openProducts = ref(false)
const collapsed = ref(false)

const pendingOrdersCount =
    page.props.admin.pendingOrdersCount

const notifications =
    page.props.admin.notifications || []

const unreadNotificationsCount =
    page.props.admin.unreadNotificationsCount || 0

const lowStockCount =
    page.props.admin.lowStockCount || 0

const showNotifications = ref(false)

const props = defineProps({
    title: {
        type: String,
        default: 'Dashboard'
    },

    subtitle: {
        type: String,
        default: ''
    },

    showBackButton: {
        type: Boolean,
        default: true,
    },

})




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