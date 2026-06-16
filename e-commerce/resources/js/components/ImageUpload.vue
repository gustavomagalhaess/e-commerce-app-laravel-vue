<template>
  <div>
    <label class="block text-sm font-medium text-gray-700 mb-2">Product Image</label>
    <div class="flex items-center gap-4">
      <img v-if="preview" :src="preview" alt="Preview" class="w-24 h-24 object-cover rounded-lg border border-gray-200" />
      <img v-else-if="currentImage" :src="`/storage/${currentImage}`" alt="Current" class="w-24 h-24 object-cover rounded-lg border border-gray-200" />
      <div v-else class="w-24 h-24 rounded-lg border-2 border-dashed border-gray-300 flex items-center justify-center text-gray-400 text-xs">
        No image
      </div>
      <input type="file" accept="image/*" @change="handleChange"
        class="block text-sm text-gray-500 file:mr-3 file:py-1.5 file:px-3 file:rounded file:border-0 file:bg-gray-100 file:text-gray-700 hover:file:bg-gray-200" />
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'

defineProps({
  currentImage: { type: String, default: null },
})
const emit = defineEmits(['change'])
const preview = ref(null)

function handleChange(e) {
  const file = e.target.files[0]
  if (!file) return
  preview.value = URL.createObjectURL(file)
  emit('change', file)
}
</script>
