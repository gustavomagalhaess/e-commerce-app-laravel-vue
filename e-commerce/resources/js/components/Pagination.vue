<template>
  <div v-if="meta.last_page > 1" class="flex items-center justify-center gap-3 mt-8">
    <button
      @click="$emit('page', meta.current_page - 1)"
      :disabled="meta.current_page === 1"
      class="pagination-button">
      ← Prev
    </button>
    <span class="text-sm text-gray-600">Page {{ meta.current_page }} of {{ meta.last_page }}</span>
    <button
      @click="$emit('page', meta.current_page + 1)"
      :disabled="meta.current_page === meta.last_page"
      class="pagination-button">
      Next →
    </button>
  </div>
</template>

<script setup>
defineProps({
  meta: { type: Object, required: true },
})
defineEmits(['page'])
</script>

<style scoped>
@reference "../../css/app.css";

.pagination-button {
    @apply px-3 py-1.5 rounded border border-gray-300 text-sm disabled:opacity-40 hover:bg-gray-100;
}
</style>
