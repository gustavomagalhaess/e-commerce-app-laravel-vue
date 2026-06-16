<template>
  <nav class="bg-white shadow-sm border-b border-gray-200">
    <div class="container mx-auto px-4 py-3 flex items-center justify-between">
      <router-link to="/dashboard" class="text-xl font-semibold text-gray-900">ShopC2C</router-link>

      <div class="flex items-center gap-4 text-sm">
        <router-link to="/dashboard" class="nav-link">Dashboard</router-link>

        <template v-if="authStore.isAuthenticated">
          <router-link to="/cart" class="nav-link relative">
            Cart
            <span v-if="cartStore.itemCount > 0" class="cart-badge">
              {{ cartStore.itemCount }}
            </span>
          </router-link>

          <router-link v-if="authStore.isAdmin" to="/admin" class="text-purple-600 hover:text-purple-900 font-medium">Admin</router-link>

          <!-- User dropdown -->
          <div class="relative">
            <button @click="open = !open" class="dropdown-trigger">
              <b>Hello, {{ firstName }}</b>
              <svg class="w-3.5 h-3.5 mt-px" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
              </svg>
            </button>

            <div v-if="open" class="fixed inset-0 z-40" @click="open = false" />

            <div v-if="open" class="dropdown-panel">
              <router-link to="/my-products" @click="open = false" class="dropdown-item">My Products</router-link>
              <router-link to="/orders"      @click="open = false" class="dropdown-item">Orders</router-link>
              <router-link to="/my-sales"    @click="open = false" class="dropdown-item">Sales</router-link>
              <router-link to="/profile"     @click="open = false" class="dropdown-item">Profile</router-link>
              <hr class="my-1 border-gray-100" />
              <button @click="handleLogout" class="dropdown-item dropdown-item--danger w-full text-left">Logout</button>
            </div>
          </div>
        </template>

        <template v-else>
          <router-link to="/login" class="nav-link">Login</router-link>
          <router-link to="/register" class="bg-blue-600 text-white px-4 py-1.5 rounded-lg hover:bg-blue-700">Register</router-link>
        </template>
      </div>
    </div>
  </nav>
</template>

<script setup>
import { ref, computed } from 'vue'
import { useAuthStore } from '../stores/auth'
import { useCartStore } from '../stores/cart'
import { useRouter } from 'vue-router'

const authStore = useAuthStore()
const cartStore = useCartStore()
const router = useRouter()
const open = ref(false)

const firstName = computed(() => authStore.user?.name?.split(' ')[0] ?? '')

async function handleLogout() {
  open.value = false
  await authStore.logout()
  cartStore.reset()
  await router.push('/login')
}
</script>

<style scoped>
@reference "../../css/app.css";

.nav-link {
  @apply text-gray-600 hover:text-gray-900;
}

.cart-badge {
  @apply ml-1 inline-flex items-center justify-center bg-blue-600 text-white text-xs font-bold rounded-full w-5 h-5;
}

.dropdown-trigger {
  @apply flex items-center gap-1 text-gray-700 hover:text-gray-900 font-medium;
}

.dropdown-panel {
  @apply absolute right-0 mt-1 w-44 bg-white border border-gray-200 rounded-lg shadow-lg py-1 z-50;
}

.dropdown-item {
  @apply block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50;
}

.dropdown-item--danger {
  @apply text-red-600;
}
</style>
