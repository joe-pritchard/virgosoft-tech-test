<script setup lang="ts">
import Alert from '@/components/Alert.vue'
import { useAuthStore } from '@/stores/auth'
import { ref, watch } from 'vue'
import { useRouter } from 'vue-router'

const auth = useAuthStore()

const email = ref('test@example.com')
const password = ref('secret')
const errorMessage = ref<string | null>(null)

const isLoggingIn = ref(false)
const router = useRouter()
const form = ref<HTMLFormElement>()

const onSubmit = async () => {
    const isValid = form.value?.checkValidity()

    if (!isValid) return

    isLoggingIn.value = true
    try {
        await auth.login(email.value, password.value)
    } catch (error) {
        errorMessage.value = (error as Error).message
    }

    isLoggingIn.value = false
}

watch(
    () => auth.isLoggedIn,
    () => {
        if (auth.isLoggedIn) {
            router.push({ path: '/' })
        }
    },
    { immediate: true },
)
</script>

<template>
    <div class="grid h-full w-full grid-cols-1 items-center justify-center">
        <div class="mx-auto min-w-md">
            <h2
                class="mt-10 text-center text-2xl/9 font-bold tracking-tight text-gray-900"
            >
                Sign in to your account
            </h2>
        </div>

        <div
            class="mx-auto mt-4 min-w-md rounded-md border border-blue-700 bg-white p-6 shadow"
        >
            <Alert
                v-if="errorMessage !== null"
                title="Unable to log you in"
                :messages="[errorMessage]"
            />
            <form
                class="space-y-6"
                action="/login"
                method="POST"
                ref="form"
                @submit.prevent="onSubmit"
                @input="errorMessage = null"
            >
                <fieldset :disabled="isLoggingIn">
                    <label
                        for="email"
                        class="block text-sm/6 font-medium text-gray-900"
                    >
                        Email address
                    </label>
                    <div class="mt-2">
                        <input
                            type="email"
                            name="email"
                            id="email"
                            autocomplete="email"
                            required
                            class="block w-full rounded-md bg-white px-3 py-1.5 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6"
                            v-model="email"
                        />
                    </div>
                </fieldset>

                <fieldset :disabled="isLoggingIn">
                    <label
                        for="password"
                        class="block text-sm/6 font-medium text-gray-900"
                    >
                        Password
                    </label>
                    <div class="mt-2">
                        <input
                            type="password"
                            name="password"
                            id="password"
                            autocomplete="current-password"
                            required
                            class="block w-full rounded-md bg-white px-3 py-1.5 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6"
                            v-model="password"
                        />
                    </div>
                </fieldset>

                <div>
                    <button
                        :disabled="isLoggingIn"
                        type="submit"
                        class="flex w-full justify-center rounded-md bg-indigo-600 px-3 py-1.5 text-sm/6 font-semibold text-white shadow-xs hover:bg-indigo-500 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 disabled:cursor-not-allowed disabled:opacity-50"
                    >
                        Sign in
                    </button>
                </div>
            </form>
        </div>
    </div>
</template>
