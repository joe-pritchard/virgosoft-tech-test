<script setup lang="ts">
import { useAuthStore } from '@/stores/auth'
import { useRouter } from 'vue-router'

const router = useRouter()
const { isLoggedIn, logout } = useAuthStore()

const onLogout = async () => {
    await logout()
    router.push('/login')
}
</script>

<template>
    <nav class="border-b border-gray-200 bg-white">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="flex h-16 justify-between">
                <div class="flex">
                    <div class="flex shrink-0 items-center">
                        <img
                            class="h-8 w-auto"
                            src="https://virgosoft.io/assets/logo.png"
                            alt="VirgoSoft Logo"
                        />
                    </div>
                </div>
                <button v-if="isLoggedIn" @click="onLogout">Logout</button>
            </div>
        </div>
    </nav>
</template>
