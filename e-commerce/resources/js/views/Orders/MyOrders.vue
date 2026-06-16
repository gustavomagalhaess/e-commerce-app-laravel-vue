<template>
  <div>
    <h1 class="text-2xl font-bold mb-6">My Orders</h1>

    <div v-if="loading" class="text-center py-16 text-gray-500">Loading...</div>
    <div v-else-if="orders.length === 0" class="text-center py-16 text-gray-500">No orders yet.</div>
    <div v-else class="space-y-4">
      <div v-for="order in orders" :key="order.id"
        class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
        <div class="flex items-start justify-between">
          <div>
            <p class="font-semibold text-gray-900">Order #{{ order.id }}</p>
            <p class="text-sm text-gray-500 mt-1">{{ new Date(order.created_at).toLocaleDateString('pt-BR') }}</p>
            <p class="text-sm text-gray-500 capitalize mt-0.5">{{ order.payment_method?.replace('_', ' ') }}</p>
          </div>
          <div class="text-right">
            <span :class="statusClass(order.status)" class="text-xs font-medium px-2.5 py-1 rounded-full">
              {{ order.status }}
            </span>
            <p class="font-bold mt-2">R$ {{ order.total }}</p>
          </div>
        </div>
        <div class="mt-3 pt-3 border-t border-gray-100">
          <router-link :to="`/orders/${order.id}`" class="text-sm text-blue-600 hover:underline">
            View details →
          </router-link>
        </div>
      </div>
    </div>

    <Pagination v-if="meta" :meta="meta" @page="fetchOrders" />
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import Pagination from '../../components/Pagination.vue'
import { getOrders } from '../../api/orders'

const orders = ref([])
const meta = ref(null)
const loading = ref(false)

function statusClass(status) {
  const map = {
    pending: 'bg-yellow-100 text-yellow-800',
    paid: 'bg-green-100 text-green-800',
    failed: 'bg-red-100 text-red-800',
    cancelled: 'bg-gray-100 text-gray-800',
  }
  return map[status] ?? 'bg-gray-100 text-gray-800'
}

async function fetchOrders(page = 1) {
  loading.value = true
  try {
    const { data } = await getOrders({ page })
    orders.value = data.data
    meta.value = { current_page: data.current_page, last_page: data.last_page, per_page: data.per_page, total: data.total }
  } finally {
    loading.value = false
  }
}

onMounted(fetchOrders)
</script>
