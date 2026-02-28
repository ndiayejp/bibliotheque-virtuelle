import api from './api.js'

export const bookService = {
  async getAll(page = 1, limit = 20, filter = 'all') {
    const response = await api.get('/books', { params: { page, limit, filter } })
    return response.data
  },

  async getById(id) {
    const response = await api.get(`/books/${id}`)
    return response.data
  },

  async create(data) {
    const response = await api.post('/books', data)
    return response.data
  },

  async borrow(id) {
    const response = await api.post(`/books/${id}/borrow`)
    return response.data
  },

  async return(id) {
    const response = await api.post(`/books/${id}/return`)
    return response.data
  },
}
