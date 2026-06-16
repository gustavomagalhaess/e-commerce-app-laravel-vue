<template>
  <div class="max-w-md mx-auto">
    <h1 class="text-2xl font-bold mb-6">Profile</h1>

    <form @submit.prevent="handleSubmit" class="bg-white p-6 rounded-lg shadow-sm border border-gray-200 space-y-4">
      <div>
        <label class="block text-sm font-medium text-gray-700">Name</label>
        <input v-model="form.name" type="text" required
          class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" />
      </div>
      <div>
        <label class="block text-sm font-medium text-gray-700">Email</label>
        <input v-model="form.email" type="email" required
          class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" />
      </div>

      <div v-if="errors.length" class="text-red-600 text-sm space-y-1">
        <p v-for="e in errors" :key="e">{{ e }}</p>
      </div>
      <p v-if="success" class="text-green-600 text-sm">Profile updated successfully.</p>

      <button type="submit" :disabled="loading"
        class="bg-blue-600 text-white px-6 py-2 rounded-md hover:bg-blue-700 disabled:opacity-50 font-medium">
        {{ loading ? 'Saving...' : 'Save Changes' }}
      </button>
    </form>

    <div class="mt-4 pt-4 border-t border-gray-200">
      <router-link to="/profile/security" class="text-blue-600 hover:underline text-sm">
        Change Password →
      </router-link>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { updateProfile } from '../../api/auth'
import { useAuthStore } from '../../stores/auth'

const authStore = useAuthStore()
const form = ref({ name: '', email: '' })
const loading = ref(false)
const errors = ref([])
const success = ref(false)

onMounted(() => {
  form.value.name = authStore.user?.name ?? ''
  form.value.email = authStore.user?.email ?? ''
})

async function handleSubmit() {
  loading.value = true
  errors.value = []
  success.value = false
  try {
    const { data } = await updateProfile(form.value)
    authStore.user = data
    success.value = true
  } catch (e) {
    const d = e.response?.data
    if (d?.errors) {
      errors.value = Object.values(d.errors).flat()
    } else {
      errors.value = [d?.message || 'Failed to update profile.']
    }
  } finally {
    loading.value = false
  }
}
</script>
