<template>
  <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden flex flex-col">
    <img v-if="product.image_path"
      :src="`/storage/${product.image_path}`"
      :alt="product.name"
      class="w-full h-48 object-cover" />
    <div v-else class="w-full h-48 bg-gray-100 flex items-center justify-center text-gray-400 text-sm">
      No image
    </div>
    <div class="p-4 flex flex-col flex-1">
      <h3 class="font-semibold text-gray-900 truncate">{{ product.name }}</h3>
      <p class="text-sm text-gray-500 mt-1 line-clamp-2 flex-1">{{ product.description }}</p>
      <div class="mt-3 flex items-center justify-between">
        <span class="text-lg font-bold text-gray-900">R$ {{ product.price }}</span>
        <router-link :to="`/products/${product.id}`" class="text-sm text-blue-600 hover:underline">View →</router-link>
      </div>
      <div class="mt-2 flex flex-wrap gap-1">
        <CategoryBadge v-for="cat in product.categories" :key="cat.id" :category="cat" />
      </div>
    </div>
  </div>
</template>

<script setup>
import CategoryBadge from './CategoryBadge.vue'

defineProps({
  product: { type: Object, required: true },
})
</script>
