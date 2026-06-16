<template>
  <div v-if="order" class="max-w-2xl mx-auto">
    <div class="flex items-center justify-between mb-6">
      <div>
        <h1 class="text-2xl font-bold">Order #{{ order.id }}</h1>
        <p class="text-sm text-gray-500 mt-1">{{ new Date(order.created_at).toLocaleString('pt-BR') }}</p>
      </div>
      <span :class="statusClass(order.status)" class="text-sm font-medium px-3 py-1 rounded-full">
        {{ order.status }}
      </span>
    </div>

    <div class="card mb-4">
      <div class="grid grid-cols-2 gap-4 text-sm">
        <div>
          <p class="text-gray-500">Payment Method</p>
          <p class="font-medium capitalize mt-1">{{ order.payment_method?.replace('_', ' ') }}</p>
        </div>
        <div>
          <p class="text-gray-500">Status</p>
          <p class="font-medium mt-1 capitalize">{{ order.status }}</p>
        </div>
      </div>
    </div>

    <div class="card">
      <h2 class="font-semibold text-gray-900 mb-4">Items</h2>
      <div class="space-y-3">
        <div v-for="item in order.items" :key="item.id"
          class="flex items-center justify-between py-2 border-b border-gray-100 last:border-0">
          <div>
            <p class="font-medium">{{ item.product?.name }}</p>
            <p class="text-sm text-gray-500 mt-0.5">
              Seller: {{ item.seller?.name }} · Qty: {{ item.quantity }} × R$ {{ item.price_at_purchase }}
            </p>
          </div>
          <p class="font-semibold">
            R$ {{ (item.quantity * parseFloat(item.price_at_purchase)).toFixed(2) }}
          </p>
        </div>
      </div>
      <div class="mt-4 pt-4 border-t border-gray-200 flex justify-between font-bold text-lg">
        <span>Total</span>
        <span>R$ {{ order.total }}</span>
      </div>
    </div>

    <div class="mt-4">
      <router-link to="/orders" class="text-blue-600 hover:underline text-sm">← Back to orders</router-link>
    </div>
  </div>
  <div v-else-if="loading" class="state-message">Loading...</div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRoute } from 'vue-router'
import { getOrder } from '../../api/orders'

const route = useRoute()
const order = ref(null)
const loading = ref(true)

function statusClass(status) {
  const map = {
    pending: 'bg-yellow-100 text-yellow-800',
    paid: 'bg-green-100 text-green-800',
    failed: 'bg-red-100 text-red-800',
    cancelled: 'bg-gray-100 text-gray-800',
  }
  return map[status] ?? 'bg-gray-100 text-gray-800'
}

onMounted(async () => {
  try {
    const { data } = await getOrder(route.params.id)
    order.value = data.data
  } finally {
    loading.value = false
  }
})
</script>

<style scoped>
@reference "../../../css/app.css";

.state-message {
  @apply text-center py-16 text-gray-500;
}

.card {
  @apply bg-white rounded-lg shadow-sm border border-gray-200 p-5;
}
</style>
