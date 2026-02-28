import { createRouter, createWebHistory } from 'vue-router'
import { useAuthStore } from '@/stores/auth.js'

const routes = [
  {
    path: '/',
    name: 'home',
    component: () => import('@/views/HomeView.vue'),
  },
  {
    path: '/livres',
    name: 'books',
    component: () => import('@/views/BooksView.vue'),
  },
  {
    path: '/livres/:id',
    name: 'book-detail',
    component: () => import('@/views/BookDetailView.vue'),
  },
  {
    path: '/connexion',
    name: 'login',
    component: () => import('@/views/LoginView.vue'),
    meta: { guestOnly: true },
  },
  {
    path: '/inscription',
    name: 'register',
    component: () => import('@/views/RegisterView.vue'),
    meta: { guestOnly: true },
  },
  {
    path: '/mes-emprunts',
    name: 'my-books',
    component: () => import('@/views/MyBooksView.vue'),
    meta: { requiresAuth: true },
  },
  {
    path: '/admin/livres/ajouter',
    name: 'create-book',
    component: () => import('@/views/admin/CreateBookView.vue'),
    meta: { requiresAuth: true, requiresLibrarian: true },
  },
  {
    path: '/:pathMatch(.*)*',
    name: 'not-found',
    component: () => import('@/views/NotFoundView.vue'),
  },
]

const router = createRouter({
  history: createWebHistory(),
  routes,
})

// Navigation Guards
router.beforeEach((to, from, next) => {
  const auth = useAuthStore()

  if (to.meta.requiresAuth && !auth.isAuthenticated) {
    return next({ name: 'login' })
  }

  if (to.meta.requiresLibrarian && !auth.isLibrarian) {
    return next({ name: 'home' })
  }

  if (to.meta.guestOnly && auth.isAuthenticated) {
    return next({ name: 'home' })
  }

  next()
})

export default router
