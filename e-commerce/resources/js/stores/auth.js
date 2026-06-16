import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import * as authApi from '../api/auth'

export const useAuthStore = defineStore('auth', () => {
  const user = ref(null)
  const token = ref(localStorage.getItem('token'))

  const isAuthenticated = computed(() => !!token.value)
  const isAdmin = computed(() => user.value?.role === 'admin')

  async function login(credentials) {
    const { data } = await authApi.login(credentials)
    token.value = data.token
    user.value = data.user
    localStorage.setItem('token', data.token)
  }

  async function register(payload) {
    const { data } = await authApi.register(payload)
    token.value = data.token
    user.value = data.user
    localStorage.setItem('token', data.token)
  }

  async function logout() {
    await authApi.logout()
    token.value = null
    user.value = null
    localStorage.removeItem('token')
  }

  async function fetchUser() {
    if (!token.value) return
    try {
      const { data } = await authApi.getUser()
      user.value = data
    } catch {
      token.value = null
      user.value = null
      localStorage.removeItem('token')
    }
  }

  return { user, token, isAuthenticated, isAdmin, login, register, logout, fetchUser }
})
