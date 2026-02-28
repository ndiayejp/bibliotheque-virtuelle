<template>
  <div class="bg-white rounded-xl border border-gray-200 shadow-sm hover:shadow-md transition-shadow overflow-hidden flex flex-col">

    <!-- Couverture -->
    <div class="relative h-48 bg-gray-100">
      <img
        :src="book.coverUrl || 'https://placehold.co/300x200?text=No+Cover'"
        :alt="book.title"
        class="w-full h-full object-cover"
      />
      <span :class="[
        'absolute top-2 right-2 flex items-center gap-1 text-xs font-semibold px-2 py-1 rounded-full',
        book.isAvailable ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'
      ]">
        <CheckCircle v-if="book.isAvailable" :size="12" />
        <XCircle v-else :size="12" />
        {{ book.isAvailable ? `${book.availableCopies} dispo` : 'Indisponible' }}
      </span>
    </div>

    <!-- Infos -->
    <div class="p-4 flex flex-col gap-2 flex-1">
      <h3 class="font-semibold text-gray-900 line-clamp-2 leading-tight">{{ book.title }}</h3>
      <div class="flex items-center gap-1.5 text-sm text-gray-500">
        <Pen :size="13" /> {{ book.author }}
      </div>
      <p v-if="book.description" class="text-xs text-gray-400 line-clamp-2 mt-1">{{ book.description }}</p>
    </div>

    <!-- Actions -->
    <div class="px-4 pb-4 flex gap-2 flex-wrap">
      <RouterLink :to="`/livres/${book.id}`" class="flex-1 flex items-center justify-center gap-1.5 text-sm px-3 py-2 rounded-lg border border-gray-300 text-gray-700 hover:bg-gray-50 transition-colors">
        <Eye :size="14" /> Détails
      </RouterLink>
      <button v-if="auth.isAuthenticated && book.isAvailable" @click="$emit('borrow', book.id)"
        class="flex-1 flex items-center justify-center gap-1.5 text-sm px-3 py-2 rounded-lg bg-blue-600 text-white hover:bg-blue-700 transition-colors">
        <BookPlus :size="14" /> Emprunter
      </button>
      <button v-if="auth.isAuthenticated && isBorrowedByMe" @click="$emit('return', book.id)"
        class="flex-1 flex items-center justify-center gap-1.5 text-sm px-3 py-2 rounded-lg bg-amber-500 text-white hover:bg-amber-600 transition-colors">
        <Undo2 :size="14" /> Retourner
      </button>
    </div>

  </div>
</template>

<script setup>
import { computed } from 'vue'
import { CheckCircle, XCircle, Pen, Eye, BookPlus, Undo2 } from 'lucide-vue-next'
import { useAuthStore } from '@/stores/auth.js'

const props = defineProps({ book: { type: Object, required: true } })
defineEmits(['borrow', 'return'])
const auth = useAuthStore()
const isBorrowedByMe = computed(() => props.book.borrowedByUserId && auth.currentUser)
</script>
