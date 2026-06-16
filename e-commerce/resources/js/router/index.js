import { createRouter, createWebHistory } from 'vue-router'
import { useAuthStore } from '../stores/auth'

const routes = [
  { path: '/', redirect: '/dashboard' },

  { path: '/login', component: () => import('../views/Auth/Login.vue'), meta: { guest: true } },
  { path: '/register', component: () => import('../views/Auth/Register.vue'), meta: { guest: true } },

  { path: '/dashboard', component: () => import('../views/Dashboard.vue') },

  { path: '/products/create', component: () => import('../views/Products/Form.vue'), meta: { auth: true } },
  { path: '/products/:id/edit', component: () => import('../views/Products/Form.vue'), meta: { auth: true } },
  { path: '/products/:id', component: () => import('../views/Products/Show.vue') },
  { path: '/my-products', component: () => import('../views/Products/MyProducts.vue'), meta: { auth: true } },

  { path: '/cart', component: () => import('../views/Cart.vue'), meta: { auth: true } },
  { path: '/checkout', component: () => import('../views/Checkout.vue'), meta: { auth: true } },
  { path: '/order-confirmation/:id', component: () => import('../views/OrderConfirmation.vue'), meta: { auth: true } },

  { path: '/orders', component: () => import('../views/Orders/MyOrders.vue'), meta: { auth: true } },
  { path: '/orders/:id', component: () => import('../views/Orders/Show.vue'), meta: { auth: true } },
  { path: '/my-sales', component: () => import('../views/Orders/MySales.vue'), meta: { auth: true } },

  { path: '/profile', component: () => import('../views/Profile/Info.vue'), meta: { auth: true } },
  { path: '/profile/security', component: () => import('../views/Profile/Security.vue'), meta: { auth: true } },

  { path: '/admin', component: () => import('../views/Admin/Dashboard.vue'), meta: { auth: true, admin: true } },
  { path: '/admin/categories', component: () => import('../views/Admin/Categories.vue'), meta: { auth: true, admin: true } },
  { path: '/admin/products', component: () => import('../views/Admin/Products.vue'), meta: { auth: true, admin: true } },
  { path: '/admin/users', component: () => import('../views/Admin/Users.vue'), meta: { auth: true, admin: true } },
]

const router = createRouter({
  history: createWebHistory(),
  routes,
})

router.beforeEach((to) => {
  const authStore = useAuthStore()
  if (to.meta.guest && authStore.isAuthenticated) return '/dashboard'
  if (to.meta.auth && !authStore.isAuthenticated) return '/login'
  if (to.meta.admin && !authStore.isAdmin) return '/dashboard'
})

export default router
