import Echo from "laravel-echo";
import Pusher from "pusher-js";
import axios from "axios";

const key = import.meta.env.VITE_PUSHER_APP_KEY;

if (!key) {
    console.warn("[Echo] VITE_PUSHER_APP_KEY is not set; skipping Echo setup.");
}

const cluster = import.meta.env.VITE_PUSHER_APP_CLUSTER ?? "mt1";
const host = import.meta.env.VITE_PUSHER_HOST ?? `ws-${cluster}.pusher.com`;
const port = Number(import.meta.env.VITE_PUSHER_PORT ?? 443);
const scheme = import.meta.env.VITE_PUSHER_SCHEME ?? "https";
const forceTLS = scheme === "https";

window.Pusher = Pusher;

// Create axios instance for auth requests with token
const authClient = axios.create({
    baseURL: '/',
    headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
    },
});

// Interceptor to add auth token dynamically
authClient.interceptors.request.use((config) => {
    const token = localStorage.getItem('auth_token');
    if (token) {
        config.headers.Authorization = `Bearer ${token}`;
    }
    return config;
});

export const echo = key ? new Echo({
    broadcaster: "pusher",
    key,
    cluster,
    wsHost: host,
    wsPort: port,
    wssPort: port,
    forceTLS,
    encrypted: forceTLS,
    disableStats: true,
    enabledTransports: ["ws", "wss"],
    authEndpoint: '/api/broadcasting/auth',
    auth: {
        headers: {
            Accept: 'application/json',
        },
    },
    authorizer: (channel, options) => {
        return {
            authorize: (socketId, callback) => {
                const token = localStorage.getItem('auth_token');
                authClient.post('/api/broadcasting/auth', {
                    socket_id: socketId,
                    channel_name: channel.name,
                }, {
                    headers: {
                        Authorization: token ? `Bearer ${token}` : '',
                    },
                })
                .then(response => {
                    callback(null, response.data);
                })
                .catch(error => {
                    console.error('Broadcasting auth error:', error);
                    callback(error, null);
                });
            },
        };
    },
}) : null;
