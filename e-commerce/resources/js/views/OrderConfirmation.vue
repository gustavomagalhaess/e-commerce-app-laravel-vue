<template>
  <div class="max-w-md mx-auto text-center py-8">

    <div v-if="status === 'pending'">
      <div class="w-16 h-16 border-4 border-blue-600 border-t-transparent rounded-full animate-spin mx-auto mb-6"></div>
      <h1 class="text-xl font-semibold text-gray-900">Processing your order...</h1>
      <p class="text-gray-500 mt-2">Please wait while we confirm your payment.</p>
    </div>

    <div v-else-if="status === 'paid'">
      <div class="w-20 h-20 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-6">
        <svg class="w-10 h-10 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
        </svg>
      </div>
      <h1 class="text-2xl font-bold text-green-700">Order Confirmed!</h1>
      <p class="text-gray-500 mt-2">Order #{{ route.params.id }}</p>
      <div class="mt-8 flex gap-3 justify-center">
        <router-link :to="`/orders/${route.params.id}`"
          class="bg-blue-600 text-white px-6 py-2.5 rounded-md hover:bg-blue-700 font-medium">
          View Order Details
        </router-link>
        <router-link to="/dashboard"
          class="border border-gray-300 px-6 py-2.5 rounded-md hover:bg-gray-50 font-medium">
          Continue Shopping
        </router-link>
      </div>
    </div>

    <div v-else>
      <div class="w-20 h-20 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-6">
        <svg class="w-10 h-10 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
        </svg>
      </div>
      <h1 class="text-2xl font-bold text-red-700">Payment Failed</h1>
      <p class="text-gray-500 mt-2">Something went wrong processing your order.</p>
      <router-link to="/cart"
        class="mt-8 inline-block bg-blue-600 text-white px-6 py-2.5 rounded-md hover:bg-blue-700 font-medium">
        Back to Cart
      </router-link>
    </div>

  </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue'
import { useRoute } from 'vue-router'
import { getOrder } from '../api/orders'
import { useCartStore } from '../stores/cart'

const route = useRoute()
const cartStore = useCartStore()
const status = ref('pending')
let pollInterval = null

async function poll() {
  try {
    const { data } = await getOrder(route.params.id)
    status.value = data.data.status
    if (status.value !== 'pending') {
      clearInterval(pollInterval)
      if (status.value === 'paid') cartStore.reset()
    }
  } catch {
    clearInterval(pollInterval)
    status.value = 'failed'
  }
}

onMounted(() => {
  poll()
  pollInterval = setInterval(poll, 2000)
})

onUnmounted(() => {
  clearInterval(pollInterval)
})
</script>
