<template>
  <div>
    <h1 class="text-2xl font-bold mb-6">Categories</h1>

    <form @submit.prevent="handleCreate" class="flex gap-3 mb-6 bg-white p-4 rounded-lg shadow-sm border border-gray-200">
      <input v-model="newName" type="text" placeholder="Category name" required class="input flex-1" />
      <input v-model="newSlug" type="text" placeholder="category-slug" required class="input flex-1" />
      <button type="submit" :disabled="creating" class="btn-primary">
        {{ creating ? 'Adding...' : 'Add' }}
      </button>
    </form>
    <p v-if="createError" class="text-red-600 text-sm mb-4">{{ createError }}</p>

    <div class="table-wrap">
      <table class="w-full text-sm">
        <thead class="bg-gray-50 border-b border-gray-200">
          <tr>
            <th class="th">Name</th>
            <th class="th">Slug</th>
            <th class="px-4 py-3 w-40"></th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="cat in categories" :key="cat.id" class="border-b border-gray-100">
            <td class="td">
              <input v-if="editing?.id === cat.id" v-model="editing.name"
                class="border border-gray-300 rounded-md px-2 py-1 text-sm w-full" />
              <span v-else>{{ cat.name }}</span>
            </td>
            <td class="td">
              <input v-if="editing?.id === cat.id" v-model="editing.slug"
                class="border border-gray-300 rounded-md px-2 py-1 text-sm w-full" />
              <span v-else class="text-gray-500">{{ cat.slug }}</span>
            </td>
            <td class="td">
              <div class="flex gap-2 justify-end">
                <template v-if="editing?.id === cat.id">
                  <button @click="handleUpdate" class="btn-xs btn-xs-blue">Save</button>
                  <button @click="editing = null" class="btn-xs btn-xs-gray">Cancel</button>
                </template>
                <template v-else>
                  <button @click="editing = { ...cat }" class="btn-xs btn-xs-gray">Edit</button>
                  <button @click="handleDelete(cat)" class="btn-xs btn-xs-red">Delete</button>
                </template>
              </div>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { getCategories } from '../../api/categories'
import { createCategory, updateCategory, deleteCategory } from '../../api/admin'

const categories = ref([])
const newName = ref('')
const newSlug = ref('')
const creating = ref(false)
const createError = ref('')
const editing = ref(null)

async function load() {
  const { data } = await getCategories()
  categories.value = data.data
}

async function handleCreate() {
  creating.value = true
  createError.value = ''
  try {
    await createCategory({ name: newName.value, slug: newSlug.value })
    newName.value = ''
    newSlug.value = ''
    await load()
  } catch (e) {
    const d = e.response?.data
    createError.value = d?.errors
      ? Object.values(d.errors).flat().join(', ')
      : d?.message || 'Failed to create.'
  } finally {
    creating.value = false
  }
}

async function handleUpdate() {
  await updateCategory(editing.value.id, { name: editing.value.name, slug: editing.value.slug })
  editing.value = null
  await load()
}

async function handleDelete(cat) {
  if (!confirm(`Delete "${cat.name}"?`)) return
  await deleteCategory(cat.id)
  await load()
}

onMounted(load)
</script>

<style scoped>
@reference "../../../css/app.css";

.input {
  @apply border border-gray-300 rounded-md shadow-sm px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500;
}

.btn-primary {
  @apply bg-blue-600 text-white px-5 py-2 rounded-md hover:bg-blue-700 disabled:opacity-50 font-medium;
}

.table-wrap {
  @apply bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden;
}

.th {
  @apply text-left px-4 py-3 font-medium text-gray-700;
}

.td {
  @apply px-4 py-3;
}

.btn-xs {
  @apply text-xs px-2.5 py-1 rounded;
}

.btn-xs-blue {
  @apply bg-blue-100 text-blue-700 hover:bg-blue-200;
}

.btn-xs-gray {
  @apply bg-gray-100 text-gray-700 hover:bg-gray-200;
}

.btn-xs-red {
  @apply bg-red-100 text-red-700 hover:bg-red-200;
}
</style>
