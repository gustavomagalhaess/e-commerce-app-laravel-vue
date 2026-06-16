<template>
  <div v-if="product" class="max-w-3xl mx-auto">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
      <div>
        <img v-if="product.image_path"
          :src="`/storage/${product.image_path}`"
          :alt="product.name"
          class="w-full rounded-lg shadow-sm" />
        <div v-else class="w-full h-64 bg-gray-100 rounded-lg flex items-center justify-center text-gray-400">
          No image
        </div>
      </div>

      <div>
        <div class="flex flex-wrap gap-2 mb-3">
          <CategoryBadge v-for="cat in product.categories" :key="cat.id" :category="cat" />
        </div>
        <h1 class="text-2xl font-bold text-gray-900">{{ product.name }}</h1>
        <p class="text-3xl font-bold text-blue-600 mt-2">R$ {{ product.price }}</p>
        <p class="text-sm text-gray-500 mt-2">Sold by: <span class="font-medium">{{ product.seller?.name }}</span></p>
        <p class="text-gray-700 mt-4 leading-relaxed">{{ product.description }}</p>

        <div v-if="authStore.isAuthenticated && product.seller_id !== authStore.user?.id" class="mt-6">
          <div class="flex items-center gap-3">
            <input v-model.number="qty" type="number" min="1"
              class="w-20 border border-gray-300 rounded-md shadow-sm px-3 py-2 text-center" />
            <button @click="handleAddToCart" :disabled="addingToCart" class="btn-primary">
              {{ addingToCart ? 'Adding...' : 'Add to Cart' }}
            </button>
          </div>
          <p v-if="cartError" class="text-red-600 text-sm mt-2">{{ cartError }}</p>
          <p v-if="cartSuccess" class="text-green-600 text-sm mt-2">Added to cart!</p>
        </div>

        <div v-if="canEdit" class="mt-6 flex gap-3">
          <router-link :to="`/products/${product.id}/edit`" class="btn-action">Edit</router-link>
          <button @click="handleDelete" class="btn-action-danger">Delete</button>
        </div>
      </div>
    </div>
  </div>
  <div v-else-if="loading" class="state-message">Loading...</div>
  <div v-else class="state-message">Product not found.</div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { getProduct, deleteProduct } from '../../api/products'
import { useAuthStore } from '../../stores/auth'
import { useCartStore } from '../../stores/cart'
import CategoryBadge from '../../components/CategoryBadge.vue'

const route = useRoute()
const router = useRouter()
const authStore = useAuthStore()
const cartStore = useCartStore()

const product = ref(null)
const loading = ref(true)
const qty = ref(1)
const addingToCart = ref(false)
const cartError = ref('')
const cartSuccess = ref(false)

const canEdit = computed(() =>
  authStore.isAuthenticated &&
  (authStore.user?.id === product.value?.seller_id || authStore.isAdmin)
)

onMounted(async () => {
  try {
    const { data } = await getProduct(route.params.id)
    product.value = data.data
  } finally {
    loading.value = false
  }
})

async function handleAddToCart() {
  addingToCart.value = true
  cartError.value = ''
  cartSuccess.value = false
  try {
    await cartStore.addItem(product.value.id, qty.value)
    cartSuccess.value = true
    setTimeout(() => { cartSuccess.value = false }, 2000)
  } catch (e) {
    cartError.value = e.response?.data?.message || 'Could not add to cart.'
  } finally {
    addingToCart.value = false
  }
}

async function handleDelete() {
  if (!confirm('Delete this product?')) return
  await deleteProduct(product.value.id)
  router.push('/my-products')
}
</script>

<style scoped>
@reference "../../../css/app.css";

.state-message {
  @apply text-center py-16 text-gray-500;
}

.btn-primary {
  @apply bg-blue-600 text-white px-6 py-2 rounded-md hover:bg-blue-700 disabled:opacity-50 font-medium;
}

.btn-action {
  @apply bg-gray-100 text-gray-700 px-4 py-2 rounded-md hover:bg-gray-200 text-sm font-medium;
}

.btn-action-danger {
  @apply bg-red-100 text-red-700 px-4 py-2 rounded-md hover:bg-red-200 text-sm font-medium;
}
</style>
