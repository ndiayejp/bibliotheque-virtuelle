<template>
  <nav class="bg-white border-b border-gray-200 shadow-sm sticky top-0 z-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="flex items-center justify-between h-16">

        <!-- Logo -->
        <RouterLink to="/" class="flex items-center gap-2 font-bold text-xl text-blue-600 hover:text-blue-700">
          <BookOpen :size="24" /> Bibliothèque
        </RouterLink>

        <!-- Liens navigation -->
        <div class="hidden md:flex items-center gap-6">
          <RouterLink
            to="/livres"
            class="flex items-center gap-2 text-gray-600 hover:text-blue-600 font-medium transition-colors"
            active-class="text-blue-600"
          >
             <Library :size="16" /> Catalogue
          </RouterLink>
          <RouterLink
            v-if="auth.isAuthenticated"
            to="/mes-emprunts"
            class="flex items-center gap-2 text-gray-600 hover:text-blue-600 font-medium transition-colors"
            active-class="text-blue-600"
          >
           <BookMarked :size="16" /> Mes emprunts
          </RouterLink>
          <RouterLink
            v-if="auth.isLibrarian"
            to="/admin/livres/ajouter"
            class="flex items-center gap-2 text-gray-600 hover:text-blue-600 font-medium transition-colors"
            active-class="text-blue-600"
          >
            <PlusCircle :size="16" /> Ajouter un livre
          </RouterLink>
        </div>

        <!-- Utilisateur -->
        <div class="flex items-center gap-3">
          <template v-if="auth.isAuthenticated">
            <div class="flex items-center gap-2">
              <div class="w-8 h-8 rounded-full bg-blue-100 flex items-center justify-center">
                <span class="text-blue-600 font-semibold text-sm">
                  {{ auth.currentUser?.email?.charAt(0).toUpperCase() }}
                </span>
              </div>
              <span class="hidden md:block text-sm text-gray-600">
                {{ auth.currentUser?.email }}
              </span>
            </div>
            <button
              @click="handleLogout"
              class="flex items-center gap-2 text-sm px-3 py-1.5 rounded-lg border border-gray-300 text-gray-600 hover:bg-gray-50 transition-colors"
            >
              <LogOut :size="14" /> Déconnexion
            </button>
          </template>
          <template v-else>
            <RouterLink
              to="/connexion"
              class="flex items-center gap-2 text-sm px-4 py-2 rounded-lg border border-gray-300 text-gray-700 hover:bg-gray-50 transition-colors"
            >
              <LogIn :size="14" /> Connexion
            </RouterLink>
            <RouterLink
              to="/inscription"
              class="flex items-center gap-2 text-sm px-4 py-2 rounded-lg bg-blue-600 text-white hover:bg-blue-700 transition-colors"
            >
              <UserPlus :size="14" /> Inscription
            </RouterLink>
          </template>
        </div>

      </div>
    </div>
  </nav>
</template>

<script setup>
import { BookOpen, Library, BookMarked, PlusCircle, User, LogOut, LogIn, UserPlus } from 'lucide-vue-next'
import { useAuthStore } from '@/stores/auth.js'
import { useRouter } from 'vue-router'

const auth = useAuthStore()
const router = useRouter()

function handleLogout() {
  auth.logout()
  router.push({ name: 'login' })
}
</script>
