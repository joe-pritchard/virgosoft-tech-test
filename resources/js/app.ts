import '../css/app.css'

import App from '@/App.vue'
import router from '@/router/router'
import { createPinia } from 'pinia'
import { createApp } from 'vue'

const pinia = createPinia()

createApp(App).use(router).use(pinia).mount('#app')
