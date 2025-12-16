<script setup lang="ts">
import Alert from '@/components/Alert.vue'
import Card from '@/components/Card.vue'
import FormButton from '@/components/FormButton.vue'
import FormInput from '@/components/FormInput.vue'
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

        <Card>
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
                    <FormInput
                        label="Email address"
                        type="email"
                        name="email"
                        autocomplete="email"
                        v-model="email"
                        required
                    />
                </fieldset>

                <fieldset :disabled="isLoggingIn">
                    <FormInput
                        label="Password"
                        type="password"
                        name="password"
                        autocomplete="current-password"
                        required
                        v-model="password"
                    />
                </fieldset>

                <FormButton
                    :disabled="isLoggingIn"
                    type="submit"
                    class="w-full"
                >
                    Sign in
                </FormButton>
            </form>
        </Card>
    </div>
</template>
