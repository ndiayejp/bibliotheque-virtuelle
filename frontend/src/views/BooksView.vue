<template>
  <div class="max-w-7xl mx-auto px-4 py-8">

    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
      <div>
        <h1 class="text-3xl font-bold text-gray-900">Catalogue</h1>
        <p class="text-gray-500 mt-1 flex items-center gap-1">
          <BookOpen :size="15" /> {{ books.total }} livre(s)
        </p>
      </div>
      <div class="flex gap-2">
        <button v-for="f in filters" :key="f.value" @click="books.setFilter(f.value)"
          :class="['flex items-center gap-1.5 px-4 py-2 rounded-lg text-sm font-medium transition-colors',
            books.activeFilter === f.value ? 'bg-blue-600 text-white' : 'border border-gray-300 text-gray-600 hover:bg-gray-50']">
          <component :is="f.icon" :size="14" />
          {{ f.label }}
        </button>
      </div>
    </div>

    <div v-if="books.loading" class="flex justify-center py-20">
      <Loader2 :size="40" class="animate-spin text-blue-600" />
    </div>

    <div v-else-if="books.error" class="flex items-center gap-2 p-4 bg-red-50 border border-red-200 rounded-xl text-red-700">
      <AlertCircle :size="18" /> {{ books.error }}
    </div>

    <div v-else>
      <div v-if="books.books.length === 0" class="text-center py-20">
        <BookX :size="48" class="mx-auto text-gray-300 mb-4" />
        <p class="text-gray-500">Aucun livre trouvé.</p>
      </div>

      <div v-else class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
        <BookCard v-for="book in books.books" :key="book.id" :book="book"
          @borrow="handleBorrow" @return="handleReturn" />
      </div>

      <div v-if="books.totalPages > 1" class="flex items-center justify-center gap-4 mt-10">
        <button @click="books.prevPage" :disabled="!books.hasPrevPage"
          class="flex items-center gap-1.5 px-4 py-2 rounded-lg border border-gray-300 text-gray-600 hover:bg-gray-50 disabled:opacity-40 disabled:cursor-not-allowed transition-colors">
          <ChevronLeft :size="16" /> Précédent
        </button>
        <span class="text-sm text-gray-500">
          Page <strong>{{ books.page }}</strong> / {{ books.totalPages }}
        </span>
        <button @click="books.nextPage" :disabled="!books.hasNextPage"
          class="flex items-center gap-1.5 px-4 py-2 rounded-lg border border-gray-300 text-gray-600 hover:bg-gray-50 disabled:opacity-40 disabled:cursor-not-allowed transition-colors">
          Suivant <ChevronRight :size="16" />
        </button>
      </div>
    </div>

  </div>
</template>

<script setup>
import { onMounted } from 'vue'
import { useBooksStore } from '@/stores/books.js'
import BookCard from '@/components/book/BookCard.vue'
import { BookOpen, BookX, Loader2, AlertCircle, ChevronLeft, ChevronRight, Library, CheckCircle } from 'lucide-vue-next'

const books = useBooksStore()

const filters = [
  { value: 'all', label: 'Tous', icon: Library },
  { value: 'available', label: 'Disponibles', icon: CheckCircle },
]

onMounted(() => books.fetchBooks())

async function handleBorrow(id) {
  try { await books.borrowBook(id) }
  catch (e) { alert(e.response?.data?.error ?? 'Erreur lors de l\'emprunt.') }
}

async function handleReturn(id) {
  try { await books.returnBook(id) }
  catch (e) { alert(e.response?.data?.error ?? 'Erreur lors du retour.') }
}
</script>
