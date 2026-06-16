<template>
  <div class="max-w-md mx-auto">
    <h1 class="text-2xl font-bold mb-6">Checkout</h1>

    <div class="bg-white p-4 rounded-lg shadow-sm border border-gray-200 mb-6">
      <p class="text-lg font-semibold">Total: R$ {{ cartStore.total }}</p>
      <p class="text-sm text-gray-500 mt-1">{{ cartStore.itemCount }} item(s) in cart</p>
    </div>

    <form @submit.prevent="handleSubmit" class="bg-white p-6 rounded-lg shadow-sm border border-gray-200 space-y-4">
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-3">Payment Method</label>
        <div class="space-y-3">
          <label v-for="opt in paymentOptions" :key="opt.value"
            class="flex items-center gap-3 cursor-pointer p-3 border rounded-md hover:bg-gray-50"
            :class="paymentMethod === opt.value ? 'border-blue-500 bg-blue-50' : 'border-gray-200'">
            <input type="radio" :value="opt.value" v-model="paymentMethod" required class="text-blue-600" />
            <span class="font-medium">{{ opt.label }}</span>
          </label>
        </div>
      </div>

      <p v-if="error" class="text-red-600 text-sm">{{ error }}</p>

      <button type="submit" :disabled="loading || !paymentMethod"
        class="w-full bg-blue-600 text-white py-3 px-4 rounded-md hover:bg-blue-700 disabled:opacity-50 font-semibold text-lg">
        {{ loading ? 'Placing order...' : 'Place Order' }}
      </button>
    </form>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { checkout } from '../api/orders'
import { useCartStore } from '../stores/cart'

const router = useRouter()
const cartStore = useCartStore()

const paymentMethod = ref('')
const loading = ref(false)
const error = ref('')

const paymentOptions = [
  { value: 'credit_card', label: 'Credit Card' },
  { value: 'pix', label: 'PIX' },
  { value: 'boleto', label: 'Boleto' },
]

async function handleSubmit() {
  loading.value = true
  error.value = ''
  try {
    const { data } = await checkout({ payment_method: paymentMethod.value })
    router.push(`/order-confirmation/${data.data.id}`)
  } catch (e) {
    error.value = e.response?.data?.message || 'Checkout failed. Please try again.'
  } finally {
    loading.value = false
  }
}
</script>
