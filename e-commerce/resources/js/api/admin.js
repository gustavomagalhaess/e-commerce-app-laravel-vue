import api from './axios'

export const createCategory = (data) => api.post('/admin/categories', data)
export const updateCategory = (id, data) => api.put(`/admin/categories/${id}`, data)
export const deleteCategory = (id) => api.delete(`/admin/categories/${id}`)
export const getAdminProducts = (params) => api.get('/admin/products', { params })
export const deleteAdminProduct = (id) => api.delete(`/admin/products/${id}`)
export const getAdminUsers = (params) => api.get('/admin/users', { params })
export const updateUserRole = (id, data) => api.put(`/admin/users/${id}/role`, data)
