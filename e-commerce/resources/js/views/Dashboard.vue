<template>
  <div>
    <div class="mb-6 flex gap-3">
      <input
        v-model="search"
        @input="debouncedFetch"
        type="text"
        placeholder="Search products..."
        class="flex-1 border border-gray-300 rounded-md shadow-sm px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" />
    </div>

    <div v-if="categories.length" class="mb-6 flex flex-wrap gap-3">
      <label v-for="cat in categories" :key="cat.id" class="flex items-center gap-1.5 cursor-pointer text-sm">
        <input type="checkbox" :value="cat.id" v-model="selectedCategories" @change="fetchProducts(1)" />
        {{ cat.name }}
      </label>
    </div>

    <div v-if="loading" class="text-center py-16 text-gray-500">Loading...</div>
    <div v-else-if="products.length === 0" class="text-center py-16 text-gray-500">No products found.</div>
    <div v-else class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
      <ProductCard v-for="product in products" :key="product.id" :product="product" />
    </div>

    <Pagination v-if="meta && meta.last_page > 1" :meta="meta" @page="fetchProducts" />
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import ProductCard from '../components/ProductCard.vue'
import Pagination from '../components/Pagination.vue'
import { getProducts } from '../api/products'
import { getCategories } from '../api/categories'

const products = ref([])
const categories = ref([])
const meta = ref(null)
const search = ref('')
const selectedCategories = ref([])
const loading = ref(false)

let debounceTimer = null
function debouncedFetch() {
  clearTimeout(debounceTimer)
  debounceTimer = setTimeout(() => fetchProducts(1), 400)
}

async function fetchProducts(page = 1) {
  loading.value = true
  try {
    const params = { page }
    if (search.value) params.search = search.value
    if (selectedCategories.value.length) params.category = selectedCategories.value
    const { data } = await getProducts(params)
    products.value = data.data
    meta.value = data.meta
  } finally {
    loading.value = false
  }
}

onMounted(async () => {
  const [, catRes] = await Promise.all([fetchProducts(), getCategories()])
  categories.value = catRes.data.data
})
</script>
