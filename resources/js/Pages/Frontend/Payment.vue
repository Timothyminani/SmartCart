<template>
  <AppLayout>

    <div class="max-w-7xl mx-auto px-6 py-12 grid lg:grid-cols-2 gap-10">

      <!-- ========================= -->
      <!-- LEFT -->
      <!-- ========================= -->
      <div class="space-y-6">

        <!-- HEADER -->
        <div>
          <div class="flex items-center gap-2">

            <ShieldCheck class="w-6 h-6 text-green-600" />

            <h1 class="text-3xl font-bold text-gray-800">
              Complete Your Payment
            </h1>

          </div>

          <p class="text-gray-500 mt-2">
            Secure checkout powered by multiple payment methods
          </p>
        </div>

        <!-- PAYMENT METHODS -->
        <div class="bg-white border rounded-2xl p-6 shadow-sm space-y-4">

          <h2 class="text-lg font-semibold text-gray-800">
            Payment Method
          </h2>

          <div class="grid grid-cols-2 gap-4">

            <!-- MPESA -->
            <button
              @click="method = 'mpesa'"
              :class="[
                'border rounded-xl p-4 flex items-center justify-center gap-3 transition',
                method === 'mpesa'
                  ? 'border-green-500 bg-green-50'
                  : 'hover:border-gray-400'
              ]"
            >
              <img
                :src="mpesaLogo"
                class="w-10 h-10 object-contain"
              />

              <span class="font-medium text-gray-800">
                M-Pesa
              </span>
            </button>

            <!-- CARD -->
            <button
              @click="method = 'card'"
              :class="[
                'border rounded-xl p-4 flex items-center justify-center gap-3 transition',
                method === 'card'
                  ? 'border-blue-500 bg-blue-50'
                  : 'hover:border-gray-400'
              ]"
            >

              <CreditCard class="w-6 h-6 text-blue-600" />

              <span class="font-medium text-gray-800">
                Card Payment
              </span>

            </button>

          </div>

        </div>

        <!-- PAYMENT CARD -->
        <div class="bg-white border rounded-2xl p-6 shadow-sm space-y-6">

          <!-- ========================= -->
          <!-- MPESA -->
          <!-- ========================= -->
          <div v-if="method === 'mpesa'" class="space-y-6">

            <!-- LOGO -->
            <div class="flex justify-center">
              <img
                :src="mpesaLogo"
                class="w-24 h-24 object-contain"
              />
            </div>

            <!-- PHONE -->
            <div>
              <label class="text-sm text-gray-600">
                M-Pesa Phone Number
              </label>

              <input
                v-model="phone"
                type="text"
                class="w-full border rounded-xl px-4 py-3 mt-2 focus:ring-2 focus:ring-green-500 outline-none"
              />
            </div>

            <!-- TOTAL -->
            <div class="bg-gray-50 rounded-xl p-5 text-center">

              <p class="text-sm text-gray-500">
                Total Amount
              </p>

              <h2 class="text-4xl font-bold text-green-600 mt-2">
                KES {{ formatPrice(payment.amount) }}
              </h2>

            </div>

            <!-- INSTRUCTIONS -->
            <div class="bg-blue-50 border border-blue-100 rounded-xl p-5">

              <h3 class="font-semibold text-gray-800 mb-4">
                How To Pay
              </h3>

              <div class="space-y-3 text-sm text-gray-600">

                <div class="flex gap-3">
                  <span class="font-bold text-blue-600">1.</span>
                  <p>Click the Pay with M-Pesa button</p>
                </div>

                <div class="flex gap-3">
                  <span class="font-bold text-blue-600">2.</span>
                  <p>Check your phone for the M-Pesa prompt</p>
                </div>

                <div class="flex gap-3">
                  <span class="font-bold text-blue-600">3.</span>
                  <p>Enter your M-Pesa PIN</p>
                </div>

                <div class="flex gap-3">
                  <span class="font-bold text-blue-600">4.</span>
                  <p>Wait for payment confirmation</p>
                </div>

              </div>

            </div>

            <!-- BUTTON -->
            <button
              @click="payNow"
              :disabled="loading"
              class="w-full bg-green-600 hover:bg-green-700 text-white py-4 rounded-xl font-medium transition flex items-center justify-center gap-2"
            >

              <Loader2
                v-if="loading"
                class="w-5 h-5 animate-spin"
              />

              <span>
                {{
                  loading
                    ? 'Sending STK Push...'
                    : 'Pay with M-Pesa'
                }}
              </span>

            </button>

          </div>

          <!-- ========================= -->
          <!-- CARD PAYMENT -->
          <!-- ========================= -->
          <div v-else class="space-y-6">

            <div class="flex justify-center">

              <div class="w-24 h-24 rounded-full bg-blue-50 flex items-center justify-center">
                <CreditCard class="w-12 h-12 text-blue-600" />
              </div>

            </div>

            <!-- CARD NUMBER -->
            <div>
              <label class="text-sm text-gray-600">
                Card Number
              </label>

              <input
                type="text"
                placeholder="1234 5678 9012 3456"
                class="w-full border rounded-xl px-4 py-3 mt-2 focus:ring-2 focus:ring-blue-500 outline-none"
              />
            </div>

            <!-- NAME -->
            <div>
              <label class="text-sm text-gray-600">
                Card Holder Name
              </label>

              <input
                type="text"
                placeholder="John Doe"
                class="w-full border rounded-xl px-4 py-3 mt-2 focus:ring-2 focus:ring-blue-500 outline-none"
              />
            </div>

            <!-- EXPIRY + CVV -->
            <div class="grid grid-cols-2 gap-4">

              <div>
                <label class="text-sm text-gray-600">
                  Expiry Date
                </label>

                <input
                  type="text"
                  placeholder="MM/YY"
                  class="w-full border rounded-xl px-4 py-3 mt-2 focus:ring-2 focus:ring-blue-500 outline-none"
                />
              </div>

              <div>
                <label class="text-sm text-gray-600">
                  CVV
                </label>

                <input
                  type="text"
                  placeholder="123"
                  class="w-full border rounded-xl px-4 py-3 mt-2 focus:ring-2 focus:ring-blue-500 outline-none"
                />
              </div>

            </div>

            <!-- TOTAL -->
            <div class="bg-gray-50 rounded-xl p-5 text-center">

              <p class="text-sm text-gray-500">
                Total Amount
              </p>

              <h2 class="text-4xl font-bold text-blue-600 mt-2">
                KES {{ formatPrice(payment.amount) }}
              </h2>

            </div>

            <!-- BUTTON -->
            <button
              class="w-full bg-blue-600 hover:bg-blue-700 text-white py-4 rounded-xl font-medium transition"
            >
              Pay with Card
            </button>

          </div>

        </div>

      </div>

      <!-- ========================= -->
      <!-- RIGHT -->
      <!-- ========================= -->
      <div>

        <div class="bg-white border rounded-2xl p-6 shadow-sm space-y-6">

          <h2 class="text-2xl font-bold text-gray-800">
            Order Summary
          </h2>

          <!-- PAYMENT DETAILS -->
          <div class="space-y-4">

            <div class="flex justify-between text-sm">
              <span class="text-gray-500">
                Payment ID
              </span>

              <span class="font-medium">
                #{{ payment.id }}
              </span>
            </div>

            <div class="flex justify-between text-sm">
              <span class="text-gray-500">
                Status
              </span>

              <span
                class="bg-yellow-100 text-yellow-700 text-xs px-3 py-1 rounded-full"
              >
                {{ payment.status }}
              </span>
            </div>

            <div class="flex justify-between text-sm">
              <span class="text-gray-500">
                Delivery Method
              </span>

              <span class="capitalize font-medium">
                {{ payment.delivery_method }}
              </span>
            </div>

            <div class="flex justify-between text-sm">
              <span class="text-gray-500">
                Phone
              </span>

              <span class="font-medium">
                {{ payment.phone }}
              </span>
            </div>

            <div class="border-t pt-4 flex justify-between">

              <span class="text-lg font-semibold">
                Total
              </span>

              <span
                :class="[
                  'text-2xl font-bold',
                  method === 'mpesa'
                    ? 'text-green-600'
                    : 'text-blue-600'
                ]"
              >
                KES {{ formatPrice(payment.amount) }}
              </span>

            </div>

          </div>

        </div>

      </div>

    </div>

  </AppLayout>
</template>

<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'
import { ref } from 'vue'
import axios from 'axios'

import {
  ShieldCheck,
  CreditCard,
  Loader2
} from 'lucide-vue-next'

import mpesaLogo from '@/assets/images/mpesa-logo.jpg'

const props = defineProps({
  payment: Object
})

const phone = ref(props.payment.phone)
const loading = ref(false)

const method = ref('mpesa')

const formatPrice = (value) => {
  return new Intl.NumberFormat().format(value)
}

const payNow = async () => {

  loading.value = true

  try {

    console.log('Initiate STK Push')

  } catch (error) {

    console.error(error)

  }

  loading.value = false
}
</script>