<template>
  <div>
    <h1 class="text-2xl font-bold mb-6">My Sales</h1>

    <div v-if="loading" class="state-message">Loading...</div>
    <div v-else-if="sales.length === 0" class="state-message">No sales yet.</div>
    <div v-else class="table-wrap">
      <table class="w-full text-sm">
        <thead class="bg-gray-50 border-b border-gray-200">
          <tr>
            <th class="th">Product</th>
            <th class="th">Buyer</th>
            <th class="th">Qty</th>
            <th class="th">Unit Price</th>
            <th class="th">Total</th>
            <th class="th">Status</th>
            <th class="th">Date</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="sale in sales" :key="sale.id" class="tr">
            <td class="td font-medium">{{ sale.product?.name }}</td>
            <td class="td text-gray-600">{{ sale.order?.buyer?.name }}</td>
            <td class="td">{{ sale.quantity }}</td>
            <td class="td">R$ {{ sale.price_at_purchase }}</td>
            <td class="td font-semibold">R$ {{ (sale.quantity * parseFloat(sale.price_at_purchase)).toFixed(2) }}</td>
            <td class="td">
              <span :class="statusClass(sale.order?.status)" class="text-xs font-medium px-2 py-0.5 rounded-full">
                {{ sale.order?.status }}
              </span>
            </td>
            <td class="td text-gray-500">{{ new Date(sale.created_at).toLocaleDateString('pt-BR') }}</td>
          </tr>
        </tbody>
      </table>
    </div>

    <Pagination v-if="meta" :meta="meta" @page="fetchSales" />
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import Pagination from '../../components/Pagination.vue'
import { getMySales } from '../../api/orders'

const sales = ref([])
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

async function fetchSales(page = 1) {
  loading.value = true
  try {
    const { data } = await getMySales({ page })
    sales.value = data.data
    meta.value = { current_page: data.current_page, last_page: data.last_page, per_page: data.per_page, total: data.total }
  } finally {
    loading.value = false
  }
}

onMounted(fetchSales)
</script>

<style scoped>
@reference "../../css/app.css";

.state-message {
  @apply text-center py-16 text-gray-500;
}

.table-wrap {
  @apply bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden;
}

.th {
  @apply text-left px-4 py-3 font-medium text-gray-700;
}

.tr {
  @apply border-b border-gray-100 hover:bg-gray-50;
}

.td {
  @apply px-4 py-3;
}
</style>
