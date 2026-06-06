import { ref, computed } from 'vue'

const compareItems = ref(
  JSON.parse(localStorage.getItem('compareItems')) || []
)

const showCompareTray = ref(
  compareItems.value.length > 0
)

const compareMessage = ref('')

export const useCompare = () => {

  // ADD PRODUCT
  const addToCompare = (product) => {

    // already exists
    const exists = compareItems.value.find(
      item => item.id === product.id
    )

    if (exists) return

    // only 2 products
    if (compareItems.value.length >= 2) {
      return {
        error: 'Compare limit reached'
      }
    }

    compareItems.value.push(product)
    saveCompare()
    // auto open tray
    showCompareTray.value = true
  }

  // REMOVE
  const removeFromCompare = (productId) => {
    compareItems.value = compareItems.value.filter(
      item => item.id !== productId
    )
saveCompare()
    // hide tray if empty
    if (!compareItems.value.length) {
      showCompareTray.value = false
    }

    
  }


  // TOGGLE (ADD/REMOVE)
  const toggleCompare = (product) => {

  // already exists → remove
  const exists = compareItems.value.find(
    item => item.id === product.id
  )

  if (exists) {

    compareItems.value = compareItems.value.filter(
      item => item.id !== product.id
    )
    saveCompare()


    // hide tray if empty
    if (!compareItems.value.length) {
      showCompareTray.value = false
    }

    return
  }

  // limit to 2
  if (compareItems.value.length >= 2) {

    showMessage('You can only compare 2 products')

    return
  }

  // category validation
  if (compareItems.value.length > 0) {

    const firstCategory =
      compareItems.value[0].category_id

    if (firstCategory !== product.category_id) {

      showMessage(
        'You can only compare products from the same category'
      )

      return
    }

  }

  // add product
  compareItems.value.push(product)
saveCompare()
  // auto open tray
  showCompareTray.value = true
}

  
  // CHECKED STATE
  const isInCompare = (productId) => {
    return compareItems.value.some(
      item => item.id === productId
    )
  }

  // CLOSE TRAY ONLY
  const closeCompareTray = () => {
    showCompareTray.value = false
  }

  // OPEN AGAIN
  const openCompareTray = () => {
    if (compareItems.value.length) {
      showCompareTray.value = true
    }
  }

  // CLEAR ALL
  const clearCompare = () => {

  compareItems.value = []

  localStorage.removeItem('compareItems')

  showCompareTray.value = false
}



const showMessage = (message) => {

  compareMessage.value = message

  setTimeout(() => {
    compareMessage.value = ''
  }, 2500)

}



const saveCompare = () => {
  localStorage.setItem(
    'compareItems',
    JSON.stringify(compareItems.value)
  )
}


  return {
    compareItems,
    showCompareTray,
    toggleCompare,
    isInCompare,
    removeFromCompare,
    closeCompareTray,
    openCompareTray,
    clearCompare,
    compareMessage
  }
}