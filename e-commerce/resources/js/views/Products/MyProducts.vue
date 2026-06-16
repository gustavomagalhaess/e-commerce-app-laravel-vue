<template>
  <div>
    <div class="flex items-center justify-between mb-6">
      <h1 class="text-2xl font-bold">My Products</h1>
      <router-link to="/products/create" class="btn-primary">+ New Product</router-link>
    </div>

    <div v-if="loading" class="state-message">Loading...</div>
    <div v-else-if="products.length === 0" class="state-message">
      You haven't listed any products yet.
    </div>
    <div v-else class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
      <div v-for="product in products" :key="product.id" class="card overflow-hidden">
        <img v-if="product.image_path"
          :src="`/storage/${product.image_path}`"
          :alt="product.name"
          class="w-full h-40 object-cover" />
        <div v-else class="w-full h-40 bg-gray-100 flex items-center justify-center text-gray-400 text-sm">
          No image
        </div>
        <div class="p-4">
          <h3 class="font-semibold truncate">{{ product.name }}</h3>
          <p class="text-blue-600 font-bold mt-1">R$ {{ product.price }}</p>
          <div class="mt-3 flex gap-2">
            <router-link :to="`/products/${product.id}/edit`" class="btn-sm">Edit</router-link>
            <button @click="handleDelete(product)" class="btn-sm-danger">Delete</button>
          </div>
        </div>
      </div>
    </div>

    <Pagination v-if="meta" :meta="meta" @page="fetchProducts" />
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import Pagination from '../../components/Pagination.vue'
import { getMyProducts, deleteProduct } from '../../api/products'

const products = ref([])
const meta = ref(null)
const loading = ref(false)

async function fetchProducts(page = 1) {
  loading.value = true
  try {
    const { data } = await getMyProducts({ page })
    products.value = data.data
    meta.value = { current_page: data.current_page, last_page: data.last_page, per_page: data.per_page, total: data.total }
  } finally {
    loading.value = false
  }
}

async function handleDelete(product) {
  if (!confirm(`Delete "${product.name}"?`)) return
  await deleteProduct(product.id)
  await fetchProducts()
}

onMounted(fetchProducts)
</script>

<style scoped>
@reference "../../../css/app.css";

.state-message {
  @apply text-center py-16 text-gray-500;
}

.card {
  @apply bg-white rounded-lg shadow-sm border border-gray-200;
}

.btn-primary {
  @apply bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 text-sm font-medium;
}

.btn-sm {
  @apply text-xs bg-gray-100 text-gray-700 px-3 py-1.5 rounded hover:bg-gray-200;
}

.btn-sm-danger {
  @apply text-xs bg-red-100 text-red-700 px-3 py-1.5 rounded hover:bg-red-200;
}
</style>
