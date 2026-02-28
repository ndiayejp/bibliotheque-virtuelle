<template>
  <div class="max-w-2xl mx-auto px-4 py-8">

    <div class="mb-8">
      <h1 class="text-3xl font-bold text-gray-900 flex items-center gap-3">
        <BookPlus :size="32" class="text-blue-600" /> Ajouter un livre
      </h1>
      <p class="text-gray-500 mt-1">Remplissez les informations du nouveau livre</p>
    </div>

    <div v-if="error" class="mb-6 flex items-center gap-2 p-4 bg-red-50 border border-red-200 rounded-xl text-red-700 text-sm">
      <AlertCircle :size="16" /> {{ error }}
    </div>
    <div v-if="success" class="mb-6 flex items-center gap-2 p-4 bg-green-50 border border-green-200 rounded-xl text-green-700 text-sm">
      <CheckCircle :size="16" /> {{ success }}
    </div>

    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-8 space-y-5">

      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Titre <span class="text-red-500">*</span></label>
        <div class="relative">
          <Type :size="16" class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400" />
          <input v-model="form.title" type="text" placeholder="Clean Code"
            class="w-full pl-9 pr-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm" />
        </div>
      </div>

      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Auteur <span class="text-red-500">*</span></label>
        <div class="relative">
          <Pen :size="16" class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400" />
          <input v-model="form.author" type="text" placeholder="Robert C. Martin"
            class="w-full pl-9 pr-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm" />
        </div>
      </div>

      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">ISBN <span class="text-red-500">*</span></label>
        <div class="relative">
          <Hash :size="16" class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400" />
          <input v-model="form.isbn" type="text" placeholder="9780132350884"
            class="w-full pl-9 pr-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm" />
        </div>
      </div>

      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
        <textarea v-model="form.description" rows="4" placeholder="Description du livre..."
          class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm resize-none">
        </textarea>
      </div>

      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">URL de la couverture</label>
        <div class="relative">
          <ImageIcon :size="16" class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400" />
          <input v-model="form.coverUrl" type="url" placeholder="https://covers.openlibrary.org/..."
            class="w-full pl-9 pr-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm" />
        </div>
        <div v-if="form.coverUrl" class="mt-2">
          <img :src="form.coverUrl" alt="Aperçu" class="h-24 rounded-lg object-cover border border-gray-200" />
        </div>
      </div>

      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Nombre d'exemplaires</label>
        <div class="relative">
          <Copy :size="16" class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400" />
          <input v-model.number="form.totalCopies" type="number" min="1"
            class="w-full pl-9 pr-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm" />
        </div>
      </div>

      <div class="flex gap-3 pt-2">
        <button @click="router.back()"
          class="flex items-center gap-2 px-6 py-2.5 border border-gray-300 text-gray-600 rounded-xl hover:bg-gray-50 transition-colors">
          <X :size="16" /> Annuler
        </button>
        <button @click="handleCreate" :disabled="loading"
          class="flex-1 flex items-center justify-center gap-2 py-2.5 bg-blue-600 text-white font-semibold rounded-xl hover:bg-blue-700 disabled:opacity-50 transition-colors">
          <Loader2 v-if="loading" :size="18" class="animate-spin" />
          <Save v-else :size="18" />
          {{ loading ? 'Création...' : 'Créer le livre' }}
        </button>
      </div>

    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { useBooksStore } from '@/stores/books.js'
import { BookPlus, Type, Pen, Hash, Image as ImageIcon, Copy, X, Save, Loader2, AlertCircle, CheckCircle } from 'lucide-vue-next'

const books = useBooksStore()
const router = useRouter()
const form = ref({ title: '', author: '', isbn: '', description: '', coverUrl: '', totalCopies: 1 })
const loading = ref(false)
const error = ref(null)
const success = ref(null)

async function handleCreate() {
  error.value = null
  loading.value = true
  try {
    await books.createBook(form.value)
    success.value = 'Livre créé avec succès !'
    setTimeout(() => router.push({ name: 'books' }), 1500)
  } catch (e) {
    error.value = e.response?.data?.error ?? 'Erreur lors de la création.'
  } finally {
    loading.value = false
  }
}
</script>
