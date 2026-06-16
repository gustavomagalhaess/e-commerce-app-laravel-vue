import api from './axios'

export const login = (data) => api.post('/login', data)
export const register = (data) => api.post('/register', data)
export const logout = () => api.post('/logout')
export const getUser = () => api.get('/user')
export const updateProfile = (data) => api.put('/user/profile', data)
export const updatePassword = (data) => api.put('/user/password', data)
