<template>
  <div class="min-h-screen flex items-center justify-center bg-gray-50">
    <div class="text-center">
      <FileQuestion :size="80" class="mx-auto text-gray-300 mb-6" />
      <h1 class="text-8xl font-bold text-gray-200 mb-4">404</h1>
      <h2 class="text-2xl font-bold text-gray-900 mb-2">Page introuvable</h2>
      <p class="text-gray-500 mb-8">La page que vous cherchez n'existe pas.</p>
      <RouterLink to="/" class="inline-flex items-center gap-2 px-6 py-3 bg-blue-600 text-white font-semibold rounded-xl hover:bg-blue-700 transition-colors">
        <Home :size="18" /> Retour à l'accueil
      </RouterLink>
    </div>
  </div>
</template>

<script setup>
import { FileQuestion, Home } from 'lucide-vue-next'
</script>
