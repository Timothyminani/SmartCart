

<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'
import { ref, onMounted, computed } from 'vue'
import axios from 'axios'
import { useCart } from '@/composables/useCart'
import { Loader2, Plus, Minus, ShieldCheck, Truck, RotateCcw } from 'lucide-vue-next'
import mpesaLogo from '@/assets/images/mpesa-logo.jpg'
import CheckoutHeader from '@/Components/Checkout/CheckoutHeader.vue'
import DeliveryDetails from '@/Components/Checkout/DeliveryDetails.vue'
import DeliveryOptions from '@/Components/Checkout/DeliveryOptions.vue'
import PaymentMethods from '@/Components/Checkout/PaymentMethods.vue'
import OrderSummary from '@/Components/Checkout/OrderSummary.vue'



const { getCart, increaseQty, decreaseQty, removeItem } = useCart()

// STATE
const cartItems = ref([])
const loading = ref(false)
const loadingItems = ref([])
const checkoutError = ref('')

// FORM
const form = ref({
  phone: '',
  address: '',
  delivery: 'standard',
  payment_method: 'cod'
})

// LOAD CART
onMounted(async () => {
  try {
    cartItems.value = await getCart()

    if (!cartItems.value.length) {
      window.location.href = '/'
    }
  } catch (error) {
    console.error('Error loading cart:', error)
  }
})

// =========================
// CART HANDLERS
// =========================
const handleIncrease = async (item) => {
  if (loadingItems.value.includes(item.id)) return

  loadingItems.value.push(item.id)

  try {
    item.quantity++
    await increaseQty(item.product_id)
  } catch (error) {
    console.error('Increase error:', error)
    item.quantity-- // rollback
  }

  loadingItems.value = loadingItems.value.filter(id => id !== item.id)
}

const handleDecrease = async (item) => {
  if (loadingItems.value.includes(item.id)) return

  loadingItems.value.push(item.id)

  try {
    if (item.quantity > 1) {
      item.quantity--
      await decreaseQty(item.id, item.quantity + 1)
    } else {
      await removeItem(item.id)
      cartItems.value = cartItems.value.filter(i => i.id !== item.id)
    }
  } catch (error) {
    console.error('Decrease error:', error)
  }

  loadingItems.value = loadingItems.value.filter(id => id !== item.id)
}

const handleRemove = async (item) => {
  if (loadingItems.value.includes(item.id)) return

  loadingItems.value.push(item.id)

  try {
    await removeItem(item.id)
    cartItems.value = cartItems.value.filter(i => i.id !== item.id)
  } catch (error) {
    console.error('Remove error:', error)
  }

  loadingItems.value = loadingItems.value.filter(id => id !== item.id)
}

// =========================
// TOTALS
// =========================
const total = computed(() => {
  return cartItems.value.reduce((sum, item) => {
    return sum + (item.price * item.quantity)
  }, 0)
})

const deliveryFee = computed(() => {
  switch (form.value.delivery) {
    case 'express':
      return 500
    case 'standard':
      return 250
    case 'pickup':
      return 0
    default:
      return total.value >= 2000 ? 0 : 250
  }
})

const grandTotal = computed(() => {
  return total.value + deliveryFee.value
})

const formatPrice = (value) => {
  return new Intl.NumberFormat().format(value)
}

// =========================
// PLACE ORDER
// =========================
const placeOrder = async () => {

    checkoutError.value = ''


    if (
        !form.value.phone.trim() ||
        !form.value.address.trim()
    ) {

        checkoutError.value =
            'Please enter your phone number and delivery address.'

        return
    }


    if (!cartItems.value.length) {

        checkoutError.value =
            'Your cart is empty.'

        return
    }


    loading.value = true


    try {

        const response = await axios.post(
            '/checkout',
            {
                phone:
                    form.value.phone.trim(),

                address:
                    form.value.address.trim(),

                delivery:
                    form.value.delivery,

                payment_method:
                    form.value.payment_method,
            }
        )


        if (
            response.data.type === 'cod'
        ) {

            window.location.href =
                `/order-success/${response.data.order_id}`

            return
        }


        if (
            response.data.type === 'mpesa'
        ) {

            window.location.href =
                `/payment/${response.data.payment_id}`

            return
        }


        checkoutError.value =
            'We could not determine the next checkout step. Please try again.'

    } catch (error) {

        checkoutError.value =
            error.response?.data?.message ||
            error.response?.data?.error ||
            'We could not place your order. Please try again.'

    } finally {

        loading.value = false

    }

}
</script>

<template>

    <AppLayout>

        <div
            class="
                max-w-7xl
                mx-auto

                px-4
                sm:px-6

                py-6
                sm:py-8
                lg:py-10
            "
        >

            <!-- HEADER -->

            <CheckoutHeader />


            <!-- ERROR -->

            <div
                v-if="checkoutError"
                class="
                    mb-6

                    rounded-xl

                    border
                    border-red-200

                    bg-red-50

                    px-4
                    py-3

                    text-sm
                    text-red-700
                "
            >
                {{ checkoutError }}
            </div>


            <!-- CHECKOUT -->

            <div
                class="
                    grid

                    lg:grid-cols-[minmax(0,1fr)_420px]

                    gap-8
                    lg:gap-12

                    items-start
                "
            >

                <!-- LEFT -->

                <div
                    class="
                        rounded-2xl

                        border
                        border-gray-200

                        bg-white

                        p-5
                        sm:p-7
                        lg:p-8
                    "
                >

                    <DeliveryDetails
                        v-model:phone="form.phone"
                        v-model:address="form.address"
                    />


                    <div
                        class="
                            my-8

                            border-t
                            border-gray-100
                        "
                    ></div>


                    <DeliveryOptions
                        v-model="form.delivery"
                    />


                    <div
                        class="
                            my-8

                            border-t
                            border-gray-100
                        "
                    ></div>


                    <PaymentMethods
                        v-model="
                            form.payment_method
                        "
                    />

                </div>


                <!-- RIGHT -->

                <OrderSummary
                    :items="cartItems"

                    :subtotal="total"
                    :delivery-fee="deliveryFee"
                    :total="grandTotal"

                    :payment-method="
                        form.payment_method
                    "

                    :loading="loading"

                    :loading-items="
                        loadingItems
                    "

                    @increase="handleIncrease"
                    @decrease="handleDecrease"
                    @remove="handleRemove"

                    @submit="placeOrder"
                />

            </div>

        </div>

    </AppLayout>

</template>