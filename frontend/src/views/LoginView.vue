<template>
  <div class="min-h-screen bg-gray-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 w-full max-w-md p-8">

      <div class="text-center mb-8">
        <div class="w-14 h-14 bg-blue-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
          <BookOpen :size="28" class="text-blue-600" />
        </div>
        <h1 class="text-2xl font-bold text-gray-900">Connexion</h1>
        <p class="text-gray-500 text-sm mt-1">Accédez à votre espace bibliothèque</p>
      </div>

      <div v-if="error" class="mb-4 flex items-center gap-2 p-3 bg-red-50 border border-red-200 rounded-lg text-red-700 text-sm">
        <AlertCircle :size="16" /> {{ error }}
      </div>

      <div class="space-y-4">
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
          <div class="relative">
            <Mail :size="16" class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400" />
            <input v-model="form.email" type="email" placeholder="admin@bibliotheque.fr"
              class="w-full pl-9 pr-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm" />
          </div>
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Mot de passe</label>
          <div class="relative">
            <Lock :size="16" class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400" />
            <input v-model="form.password" type="password" placeholder="••••••••" @keyup.enter="handleLogin"
              class="w-full pl-9 pr-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm" />
          </div>
        </div>
      </div>

      <button @click="handleLogin" :disabled="loading"
        class="w-full mt-6 flex items-center justify-center gap-2 py-3 bg-blue-600 text-white font-semibold rounded-xl hover:bg-blue-700 disabled:opacity-50 transition-colors">
        <Loader2 v-if="loading" :size="18" class="animate-spin" />
        <LogIn v-else :size="18" />
        {{ loading ? 'Connexion...' : 'Se connecter' }}
      </button>

      <p class="text-center text-sm text-gray-500 mt-4">
        Pas de compte ?
        <RouterLink to="/inscription" class="text-blue-600 hover:underline font-medium">S'inscrire</RouterLink>
      </p>

      <div class="mt-6 flex items-start gap-2 p-3 bg-blue-50 border border-blue-200 rounded-lg text-sm text-blue-700">
        <Info :size="16" class="flex-shrink-0 mt-0.5" />
        <span><strong>Compte démo :</strong> admin@bibliotheque.fr / admin123</span>
      </div>

    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth.js'
import { BookOpen, Mail, Lock, LogIn, Loader2, AlertCircle, Info } from 'lucide-vue-next'

const auth = useAuthStore()
const router = useRouter()
const form = ref({ email: '', password: '' })
const loading = ref(false)
const error = ref(null)

async function handleLogin() {
  error.value = null
  loading.value = true
  try {
    await auth.login(form.value.email, form.value.password)
    router.push({ name: 'home' })
  } catch (e) {
    error.value = e.response?.data?.message ?? 'Email ou mot de passe incorrect.'
  } finally {
    loading.value = false
  }
}
</script>
