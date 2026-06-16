<template>
  <div>
    <NavBar />
    <main class="container mx-auto px-4 py-8">
      <router-view />
    </main>
  </div>
</template>

<script setup>
import { onMounted } from 'vue'
import NavBar from './components/NavBar.vue'
import { useAuthStore } from './stores/auth'
import { useCartStore } from './stores/cart'

const authStore = useAuthStore()
const cartStore = useCartStore()

onMounted(async () => {
  await authStore.fetchUser()
  if (authStore.isAuthenticated) {
    await cartStore.fetch()
  }
})
</script>
