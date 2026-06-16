<template>
  <div class="max-w-md mx-auto mt-8">
    <h1 class="text-2xl font-bold mb-6">Create Account</h1>
    <form @submit.prevent="handleSubmit" class="space-y-4 bg-white p-6 rounded-lg shadow-sm border border-gray-200">
      <div>
        <label class="form-label">Name</label>
        <input v-model="form.name" type="text" required class="input" />
      </div>
      <div>
        <label class="form-label">Email</label>
        <input v-model="form.email" type="email" required class="input" />
      </div>
      <div>
        <label class="form-label">Password</label>
        <input v-model="form.password" type="password" required minlength="8" class="input" />
      </div>
      <div>
        <label class="form-label">Confirm Password</label>
        <input v-model="form.password_confirmation" type="password" required class="input" />
      </div>
      <div v-if="errors.length" class="text-red-600 text-sm space-y-1">
        <p v-for="e in errors" :key="e">{{ e }}</p>
      </div>
      <button type="submit" :disabled="loading" class="btn-primary w-full">
        {{ loading ? 'Creating account...' : 'Register' }}
      </button>
    </form>
    <p class="mt-4 text-center text-sm text-gray-600">
      Already have an account?
      <router-link to="/login" class="text-blue-600 hover:underline">Login</router-link>
    </p>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '../../stores/auth'
import { useCartStore } from '../../stores/cart'

const router = useRouter()
const authStore = useAuthStore()
const cartStore = useCartStore()

const form = ref({ name: '', email: '', password: '', password_confirmation: '' })
const loading = ref(false)
const errors = ref([])

async function handleSubmit() {
  loading.value = true
  errors.value = []
  try {
    await authStore.register(form.value)
    await cartStore.fetch()
    router.push('/dashboard')
  } catch (e) {
    const d = e.response?.data
    if (d?.errors) {
      errors.value = Object.values(d.errors).flat()
    } else {
      errors.value = [d?.message || 'Registration failed.']
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
  @apply bg-blue-600 text-white py-2 px-4 rounded-md hover:bg-blue-700 disabled:opacity-50 font-medium;
}
</style>
