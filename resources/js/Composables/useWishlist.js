import { ref, computed } from 'vue'

const wishlist = ref([])

const STORAGE_KEY = 'wishlist'

export function useWishlist() {

    const loadWishlist = () => {
        const stored = localStorage.getItem(STORAGE_KEY)

        wishlist.value = stored
            ? JSON.parse(stored)
            : []
    }

    const saveWishlist = () => {
        localStorage.setItem(
            STORAGE_KEY,
            JSON.stringify(wishlist.value)
        )
    }

    const isInWishlist = (productId) => {
        return wishlist.value.includes(productId)
    }

    const addToWishlist = (productId) => {

        if (isInWishlist(productId)) {
            return
        }

        wishlist.value.push(productId)

        saveWishlist()
    }

    const removeFromWishlist = (productId) => {

        wishlist.value = wishlist.value.filter(
            id => id !== productId
        )

        saveWishlist()
    }

    const toggleWishlist = (productId) => {

        if (isInWishlist(productId)) {
            removeFromWishlist(productId)
        } else {
            addToWishlist(productId)
        }
    }

    const wishlistCount = computed(() => wishlist.value.length)

    return {
        wishlist,
        wishlistCount,
        loadWishlist,
        isInWishlist,
        addToWishlist,
        removeFromWishlist,
        toggleWishlist
    }
}