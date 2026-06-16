<template>
  <div>
    <h1 class="text-2xl font-bold mb-6">Users</h1>

    <div v-if="loading" class="text-center py-16 text-gray-500">Loading...</div>
    <div v-else class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
      <table class="w-full text-sm">
        <thead class="bg-gray-50 border-b border-gray-200">
          <tr>
            <th class="text-left px-4 py-3 font-medium text-gray-700">Name</th>
            <th class="text-left px-4 py-3 font-medium text-gray-700">Email</th>
            <th class="text-left px-4 py-3 font-medium text-gray-700">Role</th>
            <th class="px-4 py-3"></th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="user in users" :key="user.id" class="border-b border-gray-100 hover:bg-gray-50">
            <td class="px-4 py-3 font-medium">{{ user.name }}</td>
            <td class="px-4 py-3 text-gray-500">{{ user.email }}</td>
            <td class="px-4 py-3">
              <span :class="user.role === 'admin' ? 'bg-purple-100 text-purple-800' : 'bg-gray-100 text-gray-700'"
                class="text-xs font-medium px-2.5 py-1 rounded-full capitalize">
                {{ user.role }}
              </span>
            </td>
            <td class="px-4 py-3 text-right">
              <button v-if="user.role !== 'admin'" @click="setRole(user, 'admin')"
                class="text-xs bg-purple-100 text-purple-700 px-2.5 py-1 rounded hover:bg-purple-200 mr-1">
                Make Admin
              </button>
              <button v-if="user.role === 'admin'" @click="setRole(user, 'customer')"
                class="text-xs bg-gray-100 text-gray-700 px-2.5 py-1 rounded hover:bg-gray-200">
                Make Customer
              </button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <Pagination v-if="meta" :meta="meta" @page="fetchUsers" />
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import Pagination from '../../components/Pagination.vue'
import { getAdminUsers, updateUserRole } from '../../api/admin'

const users = ref([])
const meta = ref(null)
const loading = ref(false)

async function fetchUsers(page = 1) {
  loading.value = true
  try {
    const { data } = await getAdminUsers({ page })
    users.value = data.data
    meta.value = { current_page: data.current_page, last_page: data.last_page, per_page: data.per_page, total: data.total }
  } finally {
    loading.value = false
  }
}

async function setRole(user, role) {
  await updateUserRole(user.id, { role })
  user.role = role
}

onMounted(fetchUsers)
</script>
