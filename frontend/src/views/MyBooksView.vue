<template>
  <div class="max-w-7xl mx-auto px-4 py-8">
    <div class="mb-8">
      <h1 class="text-3xl font-bold text-gray-900">Mes emprunts</h1>
      <p class="text-gray-500 mt-1 flex items-center gap-1">
        <BookMarked :size="15" /> Livres actuellement en votre possession
      </p>
    </div>

    <div v-if="loading" class="flex justify-center py-20">
      <Loader2 :size="40" class="animate-spin text-blue-600" />
    </div>

    <div v-else-if="myBooks.length === 0" class="text-center py-20">
      <BookX :size="56" class="mx-auto text-gray-300 mb-4" />
      <h2 class="text-xl font-semibold text-gray-700 mb-2">Aucun emprunt en cours</h2>
      <p class="text-gray-500 mb-6">Vous n'avez pas de livre emprunté pour le moment.</p>
      <RouterLink to="/livres" class="inline-flex items-center gap-2 px-6 py-3 bg-blue-600 text-white font-semibold rounded-xl hover:bg-blue-700 transition-colors">
        <Library :size="18" /> Parcourir le catalogue
      </RouterLink>
    </div>

    <div v-else class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
      <BookCard v-for="book in myBooks" :key="book.id" :book="book" @return="handleReturn" />
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useBooksStore } from '@/stores/books.js'
import BookCard from '@/components/book/BookCard.vue'
import { BookMarked, BookX, Loader2, Library } from 'lucide-vue-next'

const books = useBooksStore()
const myBooks = ref([])
const loading = ref(false)

onMounted(async () => {
  loading.value = true
  await books.fetchBooks()
  myBooks.value = books.books.filter(b => !b.isAvailable)
  loading.value = false
})

async function handleReturn(id) {
  try {
    await books.returnBook(id)
    myBooks.value = myBooks.value.filter(b => b.id !== id)
  } catch (e) {
    alert(e.response?.data?.error ?? 'Erreur lors du retour.')
  }
}
</script>
