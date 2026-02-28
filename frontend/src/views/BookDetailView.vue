<template>
  <div class="max-w-5xl mx-auto px-4 py-8">

    <button @click="router.back()" class="flex items-center gap-2 text-gray-500 hover:text-gray-700 mb-6 transition-colors">
      <ArrowLeft :size="18" /> Retour
    </button>

    <div v-if="books.loading" class="flex justify-center py-20">
      <Loader2 :size="40" class="animate-spin text-blue-600" />
    </div>

    <div v-else-if="books.currentBook" class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
      <div class="flex flex-col md:flex-row">

        <div class="md:w-64 bg-gray-100 flex-shrink-0">
          <img
            :src="books.currentBook.coverUrl || 'https://placehold.co/260x360?text=No+Cover'"
            :alt="books.currentBook.title"
            class="w-full h-64 md:h-full object-cover"
          />
        </div>

        <div class="flex-1 p-8 flex flex-col gap-4">
          <div>
            <h1 class="text-2xl font-bold text-gray-900 mb-1">{{ books.currentBook.title }}</h1>
            <div class="flex items-center gap-1.5 text-gray-500">
              <Pen :size="14" /> {{ books.currentBook.author }}
            </div>
          </div>

          <div class="flex items-center gap-3">
            <span :class="[
              'flex items-center gap-1.5 px-3 py-1 rounded-full text-sm font-semibold',
              books.currentBook.isAvailable ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'
            ]">
              <CheckCircle v-if="books.currentBook.isAvailable" :size="14" />
              <XCircle v-else :size="14" />
              {{ books.currentBook.isAvailable
                ? `${books.currentBook.availableCopies} exemplaire(s) disponible(s)`
                : 'Indisponible' }}
            </span>
          </div>

          <div class="flex items-center gap-1.5 text-sm text-gray-400 font-mono">
            <Hash :size="13" /> {{ books.currentBook.isbn }}
          </div>

          <p v-if="books.currentBook.description" class="text-gray-600 leading-relaxed">
            {{ books.currentBook.description }}
          </p>

          <div class="grid grid-cols-2 gap-4 text-sm bg-gray-50 rounded-xl p-4">
            <div class="flex items-center gap-2">
              <BookCopy :size="16" class="text-gray-400" />
              <div>
                <p class="text-gray-400 text-xs">Total exemplaires</p>
                <p class="font-semibold">{{ books.currentBook.totalCopies }}</p>
              </div>
            </div>
            <div class="flex items-center gap-2">
              <BookOpen :size="16" class="text-gray-400" />
              <div>
                <p class="text-gray-400 text-xs">Disponibles</p>
                <p class="font-semibold">{{ books.currentBook.availableCopies }}</p>
              </div>
            </div>
          </div>

          <div class="flex gap-3 mt-auto pt-4">
            <button v-if="auth.isAuthenticated && books.currentBook.isAvailable" @click="handleBorrow"
              class="flex items-center gap-2 px-6 py-2.5 bg-blue-600 text-white font-semibold rounded-xl hover:bg-blue-700 transition-colors">
              <BookPlus :size="18" /> Emprunter ce livre
            </button>
            <button v-if="auth.isAuthenticated && !books.currentBook.isAvailable" @click="handleReturn"
              class="flex items-center gap-2 px-6 py-2.5 bg-amber-500 text-white font-semibold rounded-xl hover:bg-amber-600 transition-colors">
              <Undo2 :size="18" /> Retourner ce livre
            </button>
            <RouterLink v-if="!auth.isAuthenticated" to="/connexion"
              class="flex items-center gap-2 px-6 py-2.5 bg-blue-600 text-white font-semibold rounded-xl hover:bg-blue-700 transition-colors">
              <LogIn :size="18" /> Connectez-vous pour emprunter
            </RouterLink>
          </div>
        </div>

      </div>
    </div>

  </div>
</template>

<script setup>
import { onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useBooksStore } from '@/stores/books.js'
import { useAuthStore } from '@/stores/auth.js'
import { ArrowLeft, Loader2, Pen, CheckCircle, XCircle, Hash, BookCopy, BookOpen, BookPlus, Undo2, LogIn } from 'lucide-vue-next'

const route = useRoute()
const router = useRouter()
const books = useBooksStore()
const auth = useAuthStore()

onMounted(() => books.fetchBook(route.params.id))

async function handleBorrow() {
  try { await books.borrowBook(route.params.id) }
  catch (e) { alert(e.response?.data?.error ?? 'Erreur lors de l\'emprunt.') }
}

async function handleReturn() {
  try { await books.returnBook(route.params.id) }
  catch (e) { alert(e.response?.data?.error ?? 'Erreur lors du retour.') }
}
</script>
