import { User } from '@/types'
import { fetchJson } from '@/utils/fetch'
import { defineStore } from 'pinia'

export interface AuthState {
    user: User | null
}

export const useAuthStore = defineStore<'auth', AuthState>('auth', {
    state: () => ({
        user: null,
    }),
    getters: {
        isLoggedIn: (state) => state.user !== null,
    },
    actions: {
        async login(email: string, password: string) {
            try {
                await fetchJson('/login', 'POST', { email, password })
            } catch {
                return (this.user = null)
            }

            await this.fetchUser()
        },
        async fetchUser() {
            this.user = await fetchJson<User>('/api/user', 'GET')
        },
        async logout() {
            await fetchJson('/logout', 'POST')
            this.user = null
        },
    },
})
