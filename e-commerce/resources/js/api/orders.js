import api from './axios'

export const checkout = (data) => api.post('/orders', data)
export const getOrders = (params) => api.get('/orders', { params })
export const getOrder = (id) => api.get(`/orders/${id}`)
export const getMySales = (params) => api.get('/my-sales', { params })
