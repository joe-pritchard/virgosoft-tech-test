import App from '@/App.vue'
import router from '@/router/router'
import { useAuthStore } from '@/stores/auth'
import { fetchJson } from '@/utils/fetch'
import { configureEcho } from '@laravel/echo-vue'
import { createPinia } from 'pinia'
import { createApp } from 'vue'
import '../css/app.css'

configureEcho({
    broadcaster: 'pusher',
    key: import.meta.env.VITE_PUSHER_APP_KEY,
    cluster: import.meta.env.VITE_PUSHER_APP_CLUSTER,
    forceTLS: true,
    authorizer: (channel) => ({
        authorize: (socketId, callback) => {
            fetchJson('/api/broadcasting/auth', 'POST', {
                socket_id: socketId,
                channel_name: channel.name,
            })
                .then((response) => {
                    callback(false, response.data)
                })
                .catch((error) => {
                    callback(true, error)
                })
        },
    }),
})

const pinia = createPinia()
const app = createApp(App).use(router).use(pinia)

const { fetchUser } = useAuthStore()
fetchUser().then(() => {
    app.mount('#app')
})
