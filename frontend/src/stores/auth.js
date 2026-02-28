import { ref, computed } from 'vue'
import { defineStore } from 'pinia'
import { authService } from '@/services/authService.js'

export const useAuthStore = defineStore('auth', () => {
  const token = ref(localStorage.getItem('jwt_token') ?? null)
  const currentUser = ref(null)

  const isAuthenticated = computed(() => token.value !== null)
  const isLibrarian = computed(() =>
    currentUser.value?.roles?.some(r => ['ROLE_LIBRARIAN', 'ROLE_ADMIN'].includes(r))
  )
  const isAdmin = computed(() =>
    currentUser.value?.roles?.includes('ROLE_ADMIN')
  )

  async function login(email, password) {
    const data = await authService.login(email, password)
    token.value = data.token
    localStorage.setItem('jwt_token', data.token)
    await fetchCurrentUser()
  }

  async function register(userData) {
    await authService.register(userData)
  }

  async function fetchCurrentUser() {
    if (!token.value) return
    try {
      const data = await authService.getMe()
      currentUser.value = data.data
    } catch {
      logout()
    }
  }

  function logout() {
    token.value = null
    currentUser.value = null
    localStorage.removeItem('jwt_token')
  }

  // Restaurer l'utilisateur au démarrage si token présent
  if (token.value) {
    fetchCurrentUser()
  }

  return {
    token,
    currentUser,
    isAuthenticated,
    isLibrarian,
    isAdmin,
    login,
    register,
    logout,
    fetchCurrentUser,
  }
})
