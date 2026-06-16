<template>
  <div class="max-w-md mx-auto">
    <h1 class="text-2xl font-bold mb-6">Change Password</h1>

    <form @submit.prevent="handleSubmit" class="bg-white p-6 rounded-lg shadow-sm border border-gray-200 space-y-4">
      <div>
        <label class="form-label">Current Password</label>
        <input v-model="form.current_password" type="password" required class="input" />
      </div>
      <div>
        <label class="form-label">New Password</label>
        <input v-model="form.password" type="password" required minlength="8" class="input" />
      </div>
      <div>
        <label class="form-label">Confirm New Password</label>
        <input v-model="form.password_confirmation" type="password" required class="input" />
      </div>

      <div v-if="errors.length" class="text-red-600 text-sm space-y-1">
        <p v-for="e in errors" :key="e">{{ e }}</p>
      </div>
      <p v-if="success" class="text-green-600 text-sm">Password updated successfully.</p>

      <div class="flex gap-3">
        <button type="submit" :disabled="loading" class="btn-primary">
          {{ loading ? 'Saving...' : 'Update Password' }}
        </button>
        <router-link to="/profile" class="px-6 py-2 rounded-md border border-gray-300 hover:bg-gray-50 text-sm">
          Back
        </router-link>
      </div>
    </form>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { updatePassword } from '../../api/auth'

const form = ref({ current_password: '', password: '', password_confirmation: '' })
const loading = ref(false)
const errors = ref([])
const success = ref(false)

async function handleSubmit() {
  loading.value = true
  errors.value = []
  success.value = false
  try {
    await updatePassword(form.value)
    success.value = true
    form.value = { current_password: '', password: '', password_confirmation: '' }
  } catch (e) {
    const d = e.response?.data
    if (d?.errors) {
      errors.value = Object.values(d.errors).flat()
    } else {
      errors.value = [d?.message || 'Failed to update password.']
    }
  } finally {
    loading.value = false
  }
}
</script>

<style scoped>
@reference "../../../css/app.css";

.form-label {
  @apply block text-sm font-medium text-gray-700;
}

.input {
  @apply mt-1 block w-full border border-gray-300 rounded-md shadow-sm px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500;
}

.btn-primary {
  @apply bg-blue-600 text-white px-6 py-2 rounded-md hover:bg-blue-700 disabled:opacity-50 font-medium;
}
</style>
