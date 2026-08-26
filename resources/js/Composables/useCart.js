import { usePage } from '@inertiajs/vue3'
import axios from 'axios'
import { ref } from 'vue'

 const cartMessage = ref('')


export const useCart = () => {

  const page = usePage()

  const isLoggedIn = () => !!page.props.auth.user

  // ---------------- GET CART ----------------
  const getCart = async () => {
    if (!isLoggedIn()) return []

    const res = await axios.get('/cart')

    return res.data.cart.map(item => ({
      id: item.id,              // cart item id
      product_id: item.product_id,
      name: item.name,
      price: item.price,
      image: item.image,
      quantity: item.quantity
    }))
  }

  // ---------------- ADD ----------------
  const addToCart = async (productId) => {

   if (!isLoggedIn()) {

    // Remember what product the user wanted to add
    sessionStorage.setItem(
        'pending_cart_product',
        productId
    )

    // Remember where the user currently is
    const currentUrl =
        window.location.pathname +
        window.location.search +
        window.location.hash

    window.location.href =
        `/login?redirect=${encodeURIComponent(currentUrl)}`

    return false
}

    try {

        await axios.post('/cart/add', {
            product_id: productId
        })

        window.dispatchEvent(new Event('cartUpdated'))

        return true

    } catch (error) {

        showMessage(
            error.response?.data?.message ||
            'Something went wrong'
        )

        return false
    }
}


  // ---------------- INCREASE ----------------
 const increaseQty = async (productId) => {

    try {

        await axios.post('/cart/add', {
            product_id: productId
        })

        window.dispatchEvent(new Event('cartUpdated'))

        return true

    } catch (error) {

        showMessage(
            error.response?.data?.message ||
            'Something went wrong'
        )

        return false
    }

}

  // ---------------- DECREASE ----------------
  const decreaseQty = async (itemId, quantity) => {
    if (quantity > 1) {
      await axios.post('/cart/update', {
        item_id: itemId,
        quantity: quantity - 1
      })
    } else {
      await axios.post('/cart/remove', {
        item_id: itemId
      })
    }

    window.dispatchEvent(new Event('cartUpdated'))
  }

  // ---------------- REMOVE ----------------
  const removeItem = async (itemId) => {
    await axios.post('/cart/remove', {
      item_id: itemId
    })

    window.dispatchEvent(new Event('cartUpdated'))
  }

  // ---------------- COUNT ----------------
  const getCartCount = async () => {
    const cart = await getCart()
    return cart.reduce((total, item) => total + item.quantity, 0)
  }

  // ---------------- SINGLE ITEM ----------------
  const getCartItem = async (productId) => {
    const cart = await getCart()
    return cart.find(item => item.product_id === productId)
  }



const getCartTotal = async () => {
  const cart = await getCart()

  return cart.reduce((total, item) => {
    return total + item.price * item.quantity
  }, 0)
}




const getCartRecommendations = async () => {
  if (!isLoggedIn()) return []

  const res = await axios.get('/cart/recommendations')

  return res.data.products
}



const showMessage = (message) => {

    cartMessage.value = message

    setTimeout(() => {
        cartMessage.value = ''
    }, 3000)
}


const processPendingCart = async () => {

    if (!isLoggedIn()) {
        return false
    }

    const productId =
        sessionStorage.getItem(
            'pending_cart_product'
        )

    if (!productId) {
        return false
    }

    try {

        await axios.post('/cart/add', {
            product_id: productId
        })

        // Only remove it after successfully adding
        sessionStorage.removeItem(
            'pending_cart_product'
        )

        window.dispatchEvent(
            new Event('cartUpdated')
        )

        return true

    } catch (error) {

        showMessage(
            error.response?.data?.message ||
            'Could not add the product to your cart'
        )

        return false
    }
}



  return {
    addToCart,
    processPendingCart,
    increaseQty,
    decreaseQty,
    getCart,
    getCartItem,
    getCartCount,
    removeItem,
    getCartRecommendations,
    getCartTotal,
    showMessage,
    cartMessage
  }
}