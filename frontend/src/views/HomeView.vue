<template>
  <div>
    <!-- Hero -->
    <section class="bg-gradient-to-br from-blue-600 to-blue-800 text-white py-24 px-4">
      <div class="max-w-4xl mx-auto text-center">
        <BookOpen :size="64" class="mx-auto mb-6 opacity-90" />
        <h1 class="text-4xl md:text-5xl font-bold mb-4">Bibliothèque Virtuelle</h1>
        <p class="text-blue-100 text-lg md:text-xl mb-8 max-w-2xl mx-auto">
          Découvrez notre catalogue de livres techniques et empruntez vos ouvrages préférés en quelques clics.
        </p>
        <div class="flex gap-4 justify-center flex-wrap">
          <RouterLink to="/livres" class="flex items-center gap-2 px-6 py-3 bg-white text-blue-600 font-semibold rounded-xl hover:bg-blue-50 transition-colors">
            <Library :size="18" /> Voir le catalogue
          </RouterLink>
          <RouterLink v-if="!auth.isAuthenticated" to="/inscription" class="flex items-center gap-2 px-6 py-3 border-2 border-white text-white font-semibold rounded-xl hover:bg-white/10 transition-colors">
            <UserPlus :size="18" /> Créer un compte
          </RouterLink>
        </div>
      </div>
    </section>

    <!-- Features -->
    <section class="max-w-7xl mx-auto px-4 py-16">
      <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
        <div class="bg-white rounded-xl p-6 border border-gray-200 shadow-sm text-center">
          <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center mx-auto mb-4">
            <Search :size="24" class="text-blue-600" />
          </div>
          <h3 class="font-semibold text-lg mb-2">Catalogue riche</h3>
          <p class="text-gray-500 text-sm">Parcourez des centaines de livres techniques soigneusement sélectionnés.</p>
        </div>
        <div class="bg-white rounded-xl p-6 border border-gray-200 shadow-sm text-center">
          <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center mx-auto mb-4">
            <Zap :size="24" class="text-green-600" />
          </div>
          <h3 class="font-semibold text-lg mb-2">Emprunt instantané</h3>
          <p class="text-gray-500 text-sm">Empruntez et retournez vos livres en un seul clic, disponible 24h/24.</p>
        </div>
        <div class="bg-white rounded-xl p-6 border border-gray-200 shadow-sm text-center">
          <div class="w-12 h-12 bg-purple-100 rounded-xl flex items-center justify-center mx-auto mb-4">
            <Layers :size="24" class="text-purple-600" />
          </div>
          <h3 class="font-semibold text-lg mb-2">Architecture hexagonale</h3>
          <p class="text-gray-500 text-sm">Projet développé avec les meilleures pratiques DDD et Clean Architecture.</p>
        </div>
      </div>
    </section>
  </div>
</template>

<script setup>
import { BookOpen, Library, UserPlus, Search, Zap, Layers } from 'lucide-vue-next'
import { useAuthStore } from '@/stores/auth.js'
const auth = useAuthStore()
</script>
