<template>
  <div>
    <h1 class="text-2xl font-bold mb-6">All Products</h1>

    <div v-if="loading" class="state-message">Loading...</div>
    <div v-else class="table-wrap">
      <table class="w-full text-sm">
        <thead class="bg-gray-50 border-b border-gray-200">
          <tr>
            <th class="th">Name</th>
            <th class="th">Seller</th>
            <th class="th">Price</th>
            <th class="px-4 py-3 w-20"></th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="product in products" :key="product.id" class="tr">
            <td class="td font-medium">{{ product.name }}</td>
            <td class="td text-gray-500">{{ product.seller?.name }}</td>
            <td class="td">R$ {{ product.price }}</td>
            <td class="td text-right">
              <button @click="handleDelete(product)"
                class="text-xs bg-red-100 text-red-700 px-2.5 py-1 rounded hover:bg-red-200">Delete</button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <Pagination v-if="meta" :meta="meta" @page="fetchProducts" />
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import Pagination from '../../components/Pagination.vue'
import { getAdminProducts, deleteAdminProduct } from '../../api/admin'

const products = ref([])
const meta = ref(null)
const loading = ref(false)

async function fetchProducts(page = 1) {
  loading.value = true
  try {
    const { data } = await getAdminProducts({ page })
    products.value = data.data
    meta.value = { current_page: data.current_page, last_page: data.last_page, per_page: data.per_page, total: data.total }
  } finally {
    loading.value = false
  }
}

async function handleDelete(product) {
  if (!confirm(`Delete "${product.name}"?`)) return
  await deleteAdminProduct(product.id)
  await fetchProducts()
}

onMounted(fetchProducts)
</script>

<style scoped>
@reference "../../../css/app.css";

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
