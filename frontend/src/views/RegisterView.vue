<template>
  <div class="min-h-screen bg-gray-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 w-full max-w-md p-8">

      <div class="text-center mb-8">
        <div class="w-14 h-14 bg-blue-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
          <UserPlus :size="28" class="text-blue-600" />
        </div>
        <h1 class="text-2xl font-bold text-gray-900">Créer un compte</h1>
        <p class="text-gray-500 text-sm mt-1">Rejoignez la bibliothèque virtuelle</p>
      </div>

      <div v-if="error" class="mb-4 flex items-center gap-2 p-3 bg-red-50 border border-red-200 rounded-lg text-red-700 text-sm">
        <AlertCircle :size="16" /> {{ error }}
      </div>
      <div v-if="success" class="mb-4 flex items-center gap-2 p-3 bg-green-50 border border-green-200 rounded-lg text-green-700 text-sm">
        <CheckCircle :size="16" /> {{ success }}
      </div>

      <div class="space-y-4">
        <div class="grid grid-cols-2 gap-3">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Prénom</label>
            <div class="relative">
              <User :size="16" class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400" />
              <input v-model="form.firstName" type="text" placeholder="Jean"
                class="w-full pl-9 pr-3 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm" />
            </div>
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Nom</label>
            <div class="relative">
              <User :size="16" class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400" />
              <input v-model="form.lastName" type="text" placeholder="Dupont"
                class="w-full pl-9 pr-3 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm" />
            </div>
          </div>
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
          <div class="relative">
            <Mail :size="16" class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400" />
            <input v-model="form.email" type="email" placeholder="jean@exemple.fr"
              class="w-full pl-9 pr-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm" />
          </div>
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Mot de passe</label>
          <div class="relative">
            <Lock :size="16" class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400" />
            <input v-model="form.password" type="password" placeholder="8 caractères minimum"
              class="w-full pl-9 pr-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm" />
          </div>
        </div>
      </div>

      <button @click="handleRegister" :disabled="loading"
        class="w-full mt-6 flex items-center justify-center gap-2 py-3 bg-blue-600 text-white font-semibold rounded-xl hover:bg-blue-700 disabled:opacity-50 transition-colors">
        <Loader2 v-if="loading" :size="18" class="animate-spin" />
        <UserPlus v-else :size="18" />
        {{ loading ? 'Création...' : 'Créer mon compte' }}
      </button>

      <p class="text-center text-sm text-gray-500 mt-4">
        Déjà un compte ?
        <RouterLink to="/connexion" class="text-blue-600 hover:underline font-medium">Se connecter</RouterLink>
      </p>

    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth.js'
import { UserPlus, User, Mail, Lock, Loader2, AlertCircle, CheckCircle } from 'lucide-vue-next'

const auth = useAuthStore()
const router = useRouter()
const form = ref({ firstName: '', lastName: '', email: '', password: '' })
const loading = ref(false)
const error = ref(null)
const success = ref(null)

async function handleRegister() {
  error.value = null
  loading.value = true
  try {
    await auth.register(form.value)
    success.value = 'Compte créé ! Redirection...'
    setTimeout(() => router.push({ name: 'login' }), 1500)
  } catch (e) {
    error.value = e.response?.data?.error ?? 'Erreur lors de l\'inscription.'
  } finally {
    loading.value = false
  }
}
</script>
