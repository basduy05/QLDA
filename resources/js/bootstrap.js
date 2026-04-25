import './realtime-client';
import axios from 'axios';
import * as api from './api/index';

window.axios = axios;
window.api = api;

// Setup axios defaults
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

// Auto-login if token exists
const token = localStorage.getItem('auth_token');
if (token) {
    window.axios.defaults.headers.common['Authorization'] = `Bearer ${token}`;
}

// Setup error handling
window.axios.interceptors.response.use(
    response => response,
    error => {
        // Handle common errors
        if (error.response?.status === 401) {
            // Unauthorized - redirect to login
            localStorage.removeItem('auth_token');
            localStorage.removeItem('refresh_token');
            window.location.href = '/login';
        } else if (error.response?.status === 403) {
            // Forbidden
            window.notify?.('Bạn không có quyền truy cập', 'error');
        } else if (error.response?.status === 404) {
            // Not found
            window.notify?.('Không tìm thấy tài nguyên', 'error');
        } else if (error.response?.status === 422) {
            // Validation error
            const errors = error.response.data?.errors || {};
            Object.keys(errors).forEach(field => {
                window.notify?.(`${field}: ${errors[field][0]}`, 'error');
            });
        } else if (error.response?.status >= 500) {
            // Server error
            window.notify?.('Lỗi server, vui lòng thử lại sau', 'error');
        } else if (error.message === 'Network Error') {
            // Network error
            window.notify?.('Lỗi kết nối mạng', 'error');
        }
        return Promise.reject(error);
    }
);
