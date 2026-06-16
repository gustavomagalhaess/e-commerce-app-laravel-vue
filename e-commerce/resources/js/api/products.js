import api from './axios'

export const getProducts = (params) => api.get('/products', { params })
export const getProduct = (id) => api.get(`/products/${id}`)
export const createProduct = (data) => api.post('/products', data)
export const updateProduct = (id, data) => api.post(`/products/${id}`, data)
export const deleteProduct = (id) => api.delete(`/products/${id}`)
export const getMyProducts = (params) => api.get('/my-products', { params })
