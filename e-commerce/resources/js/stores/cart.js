import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import * as cartApi from '../api/cart'

export const useCartStore = defineStore('cart', () => {
  const items = ref([])

  const itemCount = computed(() =>
    items.value.reduce((sum, i) => sum + i.quantity, 0)
  )

  const total = computed(() =>
    items.value
      .reduce((sum, i) => sum + i.quantity * parseFloat(i.product?.price ?? 0), 0)
      .toFixed(2)
  )

  async function fetch() {
    const { data } = await cartApi.getCart()
    items.value = data.data
  }

  async function addItem(product_id, quantity = 1) {
    await cartApi.addItem({ product_id, quantity })
    await fetch()
  }

  async function updateQty(id, quantity) {
    await cartApi.updateItem(id, { quantity })
    await fetch()
  }

  async function removeItem(id) {
    await cartApi.removeItem(id)
    await fetch()
  }

  async function clear() {
    await cartApi.clearCart()
    items.value = []
  }

  function reset() {
    items.value = []
  }

  return { items, itemCount, total, fetch, addItem, updateQty, removeItem, clear, reset }
})
