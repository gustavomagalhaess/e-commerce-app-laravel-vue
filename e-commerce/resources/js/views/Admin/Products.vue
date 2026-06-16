<template>
  <div>
    <h1 class="text-2xl font-bold mb-6">All Products</h1>

    <div v-if="loading" class="text-center py-16 text-gray-500">Loading...</div>
    <div v-else class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
      <table class="w-full text-sm">
        <thead class="bg-gray-50 border-b border-gray-200">
          <tr>
            <th class="text-left px-4 py-3 font-medium text-gray-700">Name</th>
            <th class="text-left px-4 py-3 font-medium text-gray-700">Seller</th>
            <th class="text-left px-4 py-3 font-medium text-gray-700">Price</th>
            <th class="px-4 py-3 w-20"></th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="product in products" :key="product.id" class="border-b border-gray-100 hover:bg-gray-50">
            <td class="px-4 py-3 font-medium">{{ product.name }}</td>
            <td class="px-4 py-3 text-gray-500">{{ product.seller?.name }}</td>
            <td class="px-4 py-3">R$ {{ product.price }}</td>
            <td class="px-4 py-3 text-right">
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
