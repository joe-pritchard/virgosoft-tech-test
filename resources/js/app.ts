import '../css/app.css'

import App from '@/App.vue'
import router from '@/router/router'
import { useAuthStore } from '@/stores/auth'
import { createPinia } from 'pinia'
import { createApp } from 'vue'

const pinia = createPinia()
const app = createApp(App).use(router).use(pinia)

const { fetchUser } = useAuthStore()
fetchUser().then(() => {
    app.mount('#app')
})
