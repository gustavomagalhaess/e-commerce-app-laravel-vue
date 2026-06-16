<template>
  <div class="max-w-3xl mx-auto">
    <h1 class="text-2xl font-bold mb-6">Your Cart</h1>

    <div v-if="cartStore.items.length === 0" class="text-center py-16 text-gray-500">
      Your cart is empty.
      <router-link to="/dashboard" class="text-blue-600 hover:underline ml-1">Continue shopping</router-link>
    </div>

    <div v-else>
      <div class="space-y-3 mb-6">
        <div v-for="item in cartStore.items" :key="item.id"
          class="card flex items-center gap-4">
          <img v-if="item.product?.image_path"
            :src="`/storage/${item.product.image_path}`"
            :alt="item.product.name"
            class="w-16 h-16 object-cover rounded-md flex-shrink-0" />
          <div v-else class="w-16 h-16 bg-gray-100 rounded-md flex items-center justify-center text-gray-400 text-xs flex-shrink-0">
            No img
          </div>

          <div class="flex-1 min-w-0">
            <p class="font-semibold truncate">{{ item.product?.name }}</p>
            <p class="text-sm text-gray-500">R$ {{ item.product?.price }} each</p>
          </div>

          <div class="flex items-center gap-2 flex-shrink-0">
            <button @click="cartStore.updateQty(item.id, item.quantity - 1)"
              :disabled="item.quantity <= 1"
              class="qty-btn disabled:opacity-40">
              −
            </button>
            <span class="w-8 text-center font-medium">{{ item.quantity }}</span>
            <button @click="cartStore.updateQty(item.id, item.quantity + 1)"
              class="qty-btn">
              +
            </button>
          </div>

          <p class="font-bold w-24 text-right flex-shrink-0">
            R$ {{ (item.quantity * parseFloat(item.product?.price ?? 0)).toFixed(2) }}
          </p>

          <button @click="cartStore.removeItem(item.id)"
            class="text-gray-400 hover:text-red-600 flex-shrink-0 text-xl leading-none">
            ×
          </button>
        </div>
      </div>

      <div class="card flex items-center justify-between">
        <div>
          <p class="text-xl font-bold">Total: R$ {{ cartStore.total }}</p>
          <p class="text-sm text-gray-500">{{ cartStore.itemCount }} item(s)</p>
        </div>
        <div class="flex gap-3 items-center">
          <button @click="cartStore.clear()" class="text-red-600 hover:underline text-sm">Clear cart</button>
          <router-link to="/checkout"
            class="bg-blue-600 text-white px-6 py-2.5 rounded-md hover:bg-blue-700 font-semibold">
            Checkout
          </router-link>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { useCartStore } from '../stores/cart'

const cartStore = useCartStore()
</script>

<style scoped>
@reference "../../css/app.css";

.card {
  @apply bg-white p-4 rounded-lg shadow-sm border border-gray-200;
}

.qty-btn {
  @apply w-8 h-8 border border-gray-300 rounded text-lg leading-none hover:bg-gray-100;
}
</style>
