<template>
  <nav class="bg-white shadow-sm border-b border-gray-200">
    <div class="container mx-auto px-4 py-3 flex items-center justify-between">
      <router-link to="/dashboard" class="text-xl font-semibold text-gray-900">ShopC2C</router-link>

      <div class="flex items-center gap-4 text-sm">
        <router-link to="/dashboard" class="text-gray-600 hover:text-gray-900">Browse</router-link>

        <template v-if="authStore.isAuthenticated">
          <router-link to="/cart" class="relative text-gray-600 hover:text-gray-900">
            Cart
            <span v-if="cartStore.itemCount > 0"
              class="ml-1 inline-flex items-center justify-center bg-blue-600 text-white text-xs font-bold rounded-full w-5 h-5">
              {{ cartStore.itemCount }}
            </span>
          </router-link>
          <router-link to="/my-products" class="text-gray-600 hover:text-gray-900">My Products</router-link>
          <router-link to="/orders" class="text-gray-600 hover:text-gray-900">Orders</router-link>
          <router-link to="/my-sales" class="text-gray-600 hover:text-gray-900">Sales</router-link>
          <router-link v-if="authStore.isAdmin" to="/admin" class="text-purple-600 hover:text-purple-900 font-medium">Admin</router-link>
          <router-link to="/profile" class="text-gray-600 hover:text-gray-900">Profile</router-link>
          <button @click="handleLogout" class="text-red-600 hover:text-red-800">Logout</button>
        </template>

        <template v-else>
          <router-link to="/login" class="text-gray-600 hover:text-gray-900">Login</router-link>
          <router-link to="/register" class="bg-blue-600 text-white px-4 py-1.5 rounded-lg hover:bg-blue-700">Register</router-link>
        </template>
      </div>
    </div>
  </nav>
</template>

<script setup>
import { useAuthStore } from '../stores/auth'
import { useCartStore } from '../stores/cart'
import { useRouter } from 'vue-router'

const authStore = useAuthStore()
const cartStore = useCartStore()
const router = useRouter()

async function handleLogout() {
  await authStore.logout()
  cartStore.reset()
  router.push('/login')
}
</script>
