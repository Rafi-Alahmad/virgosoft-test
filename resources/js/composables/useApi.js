import axios from 'axios';
import { useToast } from './useToast';

/**
 * API Composable for making HTTP requests
 * Provides a centralized way to handle API calls with consistent error handling
 */

const BASE_URL = import.meta.env.VITE_API_BASE_URL || '/api';

const api = axios.create({
    baseURL: BASE_URL,
    headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
    },
});

// Request interceptor - adds auth token to requests
api.interceptors.request.use(
    (config) => {
        const token = localStorage.getItem('auth_token');
        if (token) {
            config.headers.Authorization = `Bearer ${token}`;
        }
        return config;
    },
    (error) => Promise.reject(error)
);

// Response interceptor - handles common error responses
api.interceptors.response.use(
    (response) => response,
    (error) => {
        // Handle 401 Unauthorized - clear token and redirect
        if (error.response?.status === 401) {
            localStorage.removeItem('auth_token');
            window.location.href = '/login';
        }
        return Promise.reject(error);
    }
);

const isAbsoluteUrl = (url) => {
    return /^https?:\/\//i.test(url);
};

export function useApi(options = {}) {
    const { showToast = true } = options;

    let toast = null;
    try {
        toast = useToast();
    } catch (e) {
        // Toast not available (outside Vue context)
    }

    const showErrorToast = (message) => {
        if (showToast && toast) {
            toast.error(message);
        }
    };

    const showValidationErrorToasts = (errors) => {
        if (showToast && toast) {
            Object.keys(errors).forEach(key => {
                errors[key].forEach(error => {
                    toast.error(error);
                });
            });
        }
    };

    const showSuccessToast = (message) => {
        if (showToast && toast) {
            toast.success(message);
        }
    };

    const request = async (method, url, data = null, config = {}) => {
        try {
            const isAbsolute = isAbsoluteUrl(url);
            const requestConfig = {
                method,
                url,
                ...config,
            };

            if (data && ['post', 'put', 'patch'].includes(method)) {
                requestConfig.data = data;
            }

            let response;
            if (isAbsolute) {
                // Use axios directly for absolute URLs
                response = await axios({
                    ...requestConfig,
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        ...config.headers,
                    },
                });
            } else {
                // Use configured api instance for relative URLs
                response = await api.request(requestConfig);
            }

            return { data: response.data, error: null };
        } catch (error) {
            const handledError = handleError(error);
            // Show toast for non-validation errors
            if (handledError.type !== 'validation') {
                showErrorToast(handledError.message);
            } else {
                showValidationErrorToasts(handledError.errors);
            }

            return { data: null, error: handledError };
        }
    };

    const get = (url, config = {}) => request('get', url, null, config);
    const post = (url, data = {}, config = {}) => request('post', url, data, config);
    const put = (url, data = {}, config = {}) => request('put', url, data, config);
    const patch = (url, data = {}, config = {}) => request('patch', url, data, config);
    const del = (url, config = {}) => request('delete', url, null, config);

    const handleError = (error) => {
        if (error.response) {
            const { status, data } = error.response;

            if (status === 422) {
                return {
                    type: 'validation',
                    message: data.message || 'Validation failed',
                    errors: data.errors || {},
                };
            }

            if (status === 401) {
                return {
                    type: 'auth',
                    message: data.message || 'Unauthorized',
                    errors: {},
                };
            }

            if (status === 403) {
                return {
                    type: 'forbidden',
                    message: data.message || 'Access denied',
                    errors: {},
                };
            }

            if (status === 404) {
                return {
                    type: 'notfound',
                    message: data.message || 'Resource not found',
                    errors: {},
                };
            }

            if (status >= 500) {
                return {
                    type: 'server',
                    message: data.message || 'Server error occurred',
                    errors: {},
                };
            }

            return {
                type: 'error',
                message: data.message || 'An error occurred',
                errors: {},
            };
        }

        if (error.request) {
            // Request made but no response
            return {
                type: 'network',
                message: 'Network error. Please check your connection.',
                errors: {},
            };
        }

        // Something else happened
        return {
            type: 'unknown',
            message: error.message || 'An unexpected error occurred',
            errors: {},
        };
    };

    /**
     * Set authorization token
     */
    const setAuthToken = (token) => {
        if (token) {
            localStorage.setItem('auth_token', token);
            api.defaults.headers.common['Authorization'] = `Bearer ${token}`;
        } else {
            localStorage.removeItem('auth_token');
            delete api.defaults.headers.common['Authorization'];
        }
    };

    return {
        get,
        post,
        put,
        patch,
        del,
        setAuthToken,
        showSuccessToast,
        showErrorToast,
        api,
    };
}

export default useApi;
