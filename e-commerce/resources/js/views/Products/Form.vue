<template>
  <div class="max-w-2xl mx-auto">
    <h1 class="text-2xl font-bold mb-6">{{ isEdit ? 'Edit Product' : 'New Product' }}</h1>

    <form @submit.prevent="handleSubmit" class="space-y-5 bg-white p-6 rounded-lg shadow-sm border border-gray-200">
      <div>
        <label class="block text-sm font-medium text-gray-700">Name</label>
        <input v-model="form.name" type="text" required
          class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" />
      </div>

      <div>
        <label class="block text-sm font-medium text-gray-700">Description</label>
        <textarea v-model="form.description" required rows="4"
          class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>
      </div>

      <div>
        <label class="block text-sm font-medium text-gray-700">Price (R$)</label>
        <input v-model="form.price" type="number" step="0.01" min="0.01" required
          class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" />
      </div>

      <div>
        <label class="block text-sm font-medium text-gray-700 mb-2">Categories <span class="text-red-500">*</span></label>
        <div class="grid grid-cols-2 sm:grid-cols-3 gap-2">
          <label v-for="cat in categories" :key="cat.id" class="flex items-center gap-2 cursor-pointer text-sm">
            <input type="checkbox" :value="cat.id" v-model="form.category_ids" />
            {{ cat.name }}
          </label>
        </div>
      </div>

      <ImageUpload :current-image="product?.image_path ?? null" @change="(f) => imageFile = f" />

      <div v-if="errors.length" class="text-red-600 text-sm space-y-1">
        <p v-for="e in errors" :key="e">{{ e }}</p>
      </div>

      <div class="flex gap-3 pt-2">
        <button type="submit" :disabled="loading"
          class="bg-blue-600 text-white px-6 py-2 rounded-md hover:bg-blue-700 disabled:opacity-50 font-medium">
          {{ loading ? 'Saving...' : (isEdit ? 'Update Product' : 'Create Product') }}
        </button>
        <router-link to="/my-products" class="px-6 py-2 rounded-md border border-gray-300 hover:bg-gray-50 text-sm">
          Cancel
        </router-link>
      </div>
    </form>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { getProduct, createProduct, updateProduct } from '../../api/products'
import { getCategories } from '../../api/categories'
import ImageUpload from '../../components/ImageUpload.vue'

const route = useRoute()
const router = useRouter()

const isEdit = computed(() => !!route.params.id)
const product = ref(null)
const categories = ref([])
const loading = ref(false)
const errors = ref([])
const imageFile = ref(null)

const form = ref({
  name: '',
  description: '',
  price: '',
  category_ids: [],
})

onMounted(async () => {
  const catRes = await getCategories()
  categories.value = catRes.data.data

  if (isEdit.value) {
    const { data } = await getProduct(route.params.id)
    product.value = data.data
    form.value.name = product.value.name
    form.value.description = product.value.description
    form.value.price = product.value.price
    form.value.category_ids = product.value.categories.map((c) => c.id)
  }
})

async function handleSubmit() {
  loading.value = true
  errors.value = []

  const fd = new FormData()
  fd.append('name', form.value.name)
  fd.append('description', form.value.description)
  fd.append('price', form.value.price)
  form.value.category_ids.forEach((id) => fd.append('category_ids[]', id))
  if (imageFile.value) fd.append('image', imageFile.value)

  try {
    if (isEdit.value) {
      fd.append('_method', 'PUT')
      await updateProduct(route.params.id, fd)
    } else {
      await createProduct(fd)
    }
    router.push('/my-products')
  } catch (e) {
    const d = e.response?.data
    if (d?.errors) {
      errors.value = Object.values(d.errors).flat()
    } else {
      errors.value = [d?.message || 'Failed to save product.']
    }
  } finally {
    loading.value = false
  }
}
</script>
