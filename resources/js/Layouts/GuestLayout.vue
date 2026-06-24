<script setup>
import { Link } from '@inertiajs/vue3';
import loginImage from '@/assets/images/loginImage.jpeg';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import { ShoppingBag, ShieldCheck, Truck } from 'lucide-vue-next';
</script>

<template>
    <div class="min-h-screen grid lg:grid-cols-2 bg-white overflow-y-hidden">



        <!-- LEFT SIDE -->
<div
    class="hidden lg:flex flex-col justify-center text-white relative overflow-hidden bg-cover bg-center"
    :style="{
    backgroundImage: `url(${loginImage})`
  }"
>






    <!-- Dark overlay -->
    <div class="absolute inset-0 bg-blue-900/50"></div>

    <!-- Decorative blur -->
    <div class="absolute top-0 right-0 w-96 h-96 bg-white/10 rounded-full blur-xl"></div>
    <div class="absolute bottom-0 left-0 w-80 h-80 bg-white/10 rounded-full blur-xl"></div>

    <div class="relative h-full flex flex-col z-10 max-w-xl py-10 px-10 justify-between ">

<!-- LOGO -->
    <div class="absolute top-0 left-0 rounded-br-lg">
        <Link href="/">
            <ApplicationLogo class="h-20 w-50" />
        </Link>
    </div>
        
        <div>

      

    
            <slot name="hero">
                <h1 class="text-5xl font-bold leading-tight">
                    Shop Smarter With AI
                </h1>

                <p class="mt-6 text-lg text-blue-100 leading-relaxed">
                    Discover thousands of products and make
                    confident buying decisions.
                </p>
            </slot>
        </div>

        <!-- Features -->
        <div class="flex flex-row  mt-12 space-x-6">

            <div class="flex flex-col items-center gap-4">
                <div class="bg-blue-900/60 p-3 rounded-xl backdrop-blur-sm">
                    <ShoppingBag class="w-6 h-6" />
                </div>

                <div>
                    <h3 class="font-semibold">
                        Thousands of Products
                    </h3>

                    <p class="text-sm text-blue-100">
                        Browse products from trusted brands.
                    </p>
                </div>
            </div>

            <div class="flex flex-col items-center gap-4">
                <div class="bg-blue-900/60 p-3 rounded-xl backdrop-blur-sm">
                    <ShieldCheck class="w-6 h-6" />
                </div>

                <div>
                    <h3 class="font-semibold">
                        AI Powered Comparison
                    </h3>

                    <p class="text-sm text-blue-100">
                        Compare products intelligently before buying.
                    </p>
                </div>
            </div>

            <div class="flex flex-col items-center gap-4">
                <div class="bg-blue-900/60 p-3 rounded-xl backdrop-blur-sm">
                    <Truck class="w-6 h-6" />
                </div>

                <div>
                    <h3 class="font-semibold">
                        Fast & Reliable Delivery
                    </h3>

                    <p class="text-sm text-blue-100">
                        Shop securely and receive your orders quickly.
                    </p>
                </div>
            </div>

        </div>

    </div>

</div>



        <!-- RIGHT SIDE -->
        <div
            class="flex items-center justify-center bg-gray-50 px-6"
        >

            <div
                class="w-full max-w-md bg-white rounded-3xl shadow-xl border border-gray-100 "
            >
                <div class="lg:hidden mb-8 text-center ">
                   
                </div>

                <slot />

            </div>

        </div>

    </div>
</template>