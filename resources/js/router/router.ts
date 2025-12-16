import { useAuthStore } from '@/stores/auth'
import Home from '@/views/Home.vue'
import Login from '@/views/Login.vue'
import { createRouter, createWebHistory } from 'vue-router'

const router = createRouter({
    history: createWebHistory(),
    routes: [
        {
            path: '/',
            name: 'Dashboard',
            component: Home,
        },
        {
            component: Login,
            path: '/login',
        },
    ],
})

const redirectIfNotLoggedIn = (to: any, from: any, next: any) => {
    const { isLoggedIn } = useAuthStore()
    if (!isLoggedIn && to.path !== '/login') {
        return next('/login')
    }

    next()
}

const redirectIfLoggedIn = (to: any, from: any, next: any) => {
    const { isLoggedIn } = useAuthStore()
    if (isLoggedIn && to.path === '/login') {
        return next('/')
    }
    
    next()
}

router.beforeEach(redirectIfNotLoggedIn)
router.beforeEach(redirectIfLoggedIn)

export default router
