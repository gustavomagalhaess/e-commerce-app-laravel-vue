<template>
  <span class="inline-block bg-blue-100 text-blue-800 text-xs font-medium px-2 py-0.5 rounded-full">
    {{ category.name }}
  </span>
</template>

<script setup>
defineProps({
  category: { type: Object, required: true },
})
</script>
