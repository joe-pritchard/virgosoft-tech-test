import Login from '@/views/Login.vue';
import { createRouter, createWebHistory } from 'vue-router';

const router = createRouter({
    history: createWebHistory(),
    routes: [
        {
            path: '/',
            redirect: '/login',
        },
        {
            component: Login,
            path: '/login',
        },
    ],
});

export default router;
