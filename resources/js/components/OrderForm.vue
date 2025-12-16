<script setup lang="ts">
import Card from '@/components/Card.vue'
import Dropdown from '@/components/Dropdown.vue'
import FormInput from '@/components/FormInput.vue'

import FormButton from '@/components/FormButton.vue'
import { useAuthStore } from '@/stores/auth'
import { AssetSymbol } from '@/types'
import { storeToRefs } from 'pinia'
import { computed, ref } from 'vue'

const assetSymbols = Object.entries(AssetSymbol).map(([label, value]) => ({
    label,
    value,
}))

const sideOptions = [
    { label: 'Buy', value: 'BUY' },
    { label: 'Sell', value: 'SELL' },
]

const form = ref<HTMLFormElement>()
const errorMessage = ref<string | null>(null)

const { user } = storeToRefs(useAuthStore())

const symbol = ref(null)
const side = ref('BUY')
const price = ref<number | null>(null)
const amount = ref(0)

const userHasEnoughBalance = computed(() => {
    if (!price.value || !amount.value) return false

    return (user.value?.balance ?? 0) >= price.value * amount.value
})

const onSubmit = async () => {
    const isValid = form.value?.checkValidity()

    if (!isValid || !userHasEnoughBalance.value) return
}
</script>

<template>
    <Card>
        <form
            class="grid grid-cols-2 gap-6"
            action="/api/order"
            method="POST"
            ref="form"
            @submit.prevent="onSubmit"
            @input="errorMessage = null"
        >
            <Dropdown :options="assetSymbols" v-model="symbol">
                Asset
            </Dropdown>

            <Dropdown
                :options="sideOptions"
                label="Symbol"
                name="symbol"
                v-model="side"
            >
                Buy/Sell
            </Dropdown>

            <FormInput
                label="Price"
                v-model.number="price"
                placeholder="$0.00"
                type="number"
                step="0.01"
                min="0.01"
                required
            />

            <FormInput
                label="Amount"
                v-model.number="amount"
                type="number"
                step="0.01"
                min="0.01"
                required
            />

            <div class="col-span-2">
                <FormButton>Place Order</FormButton>
            </div>
        </form>
    </Card>
</template>
