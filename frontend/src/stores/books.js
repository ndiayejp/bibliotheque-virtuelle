import { ref, computed } from 'vue'
import { defineStore } from 'pinia'
import { bookService } from '@/services/bookService.js'

export const useBooksStore = defineStore('books', () => {
  const books = ref([])
  const currentBook = ref(null)
  const total = ref(0)
  const page = ref(1)
  const limit = ref(20)
  const activeFilter = ref('all')
  const loading = ref(false)
  const error = ref(null)

  const totalPages = computed(() => Math.ceil(total.value / limit.value))
  const hasNextPage = computed(() => page.value < totalPages.value)
  const hasPrevPage = computed(() => page.value > 1)

  async function fetchBooks() {
    loading.value = true
    error.value = null
    try {
      const data = await bookService.getAll(page.value, limit.value, activeFilter.value)
      books.value = data.data
      total.value = data.meta.total
    } catch (e) {
      error.value = 'Erreur lors du chargement des livres.'
    } finally {
      loading.value = false
    }
  }

  async function fetchBook(id) {
    loading.value = true
    error.value = null
    try {
      const data = await bookService.getById(id)
      currentBook.value = data.data
    } catch (e) {
      error.value = 'Livre introuvable.'
    } finally {
      loading.value = false
    }
  }

  async function borrowBook(id) {
    const data = await bookService.borrow(id)
    // Mise à jour optimiste
    const index = books.value.findIndex(b => b.id === id)
    if (index !== -1) books.value[index] = data.data
    if (currentBook.value?.id === id) currentBook.value = data.data
    return data
  }

  async function returnBook(id) {
    const data = await bookService.return(id)
    const index = books.value.findIndex(b => b.id === id)
    if (index !== -1) books.value[index] = data.data
    if (currentBook.value?.id === id) currentBook.value = data.data
    return data
  }

  async function createBook(bookData) {
    const data = await bookService.create(bookData)
    books.value.unshift(data.data)
    return data
  }

  function setFilter(filter) {
    activeFilter.value = filter
    page.value = 1
    fetchBooks()
  }

  function nextPage() {
    if (hasNextPage.value) { page.value++; fetchBooks() }
  }

  function prevPage() {
    if (hasPrevPage.value) { page.value--; fetchBooks() }
  }

  return {
    books, currentBook, total, page, limit,
    activeFilter, loading, error,
    totalPages, hasNextPage, hasPrevPage,
    fetchBooks, fetchBook, borrowBook,
    returnBook, createBook, setFilter,
    nextPage, prevPage,
  }
})
