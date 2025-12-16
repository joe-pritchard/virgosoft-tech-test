import { User } from '@/types'
import { fetchJson } from '@/utils/fetch'
import { defineStore } from 'pinia'
import { computed, ref } from 'vue'

export const useAuthStore = defineStore('auth', () => {
    const user = ref<User | null>(null)
    const isLoggedIn = computed(() => user.value !== null)

    const login = async (email: string, password: string) => {
        try {
            await fetch('/sanctum/csrf-cookie', { method: 'GET' })
            await fetchJson('/login', 'POST', { email, password })
        } catch (error) {
            user.value = null
            throw error
        }

        await fetchUser()
    }

    const fetchUser = async () => {
        try {
            user.value = await fetchJson<User>('/api/profile', 'GET')
        } catch (error) {
            console.error(error)
            user.value = null
        }
    }

    const logout = async () => {
        await fetchJson('/logout', 'POST').catch(() => {})

        user.value = null
    }

    return {
        user,
        isLoggedIn,
        login,
        fetchUser,
        logout,
    }
})
