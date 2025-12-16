<script setup lang="ts">
import { AssetSymbol, Order } from '@/types'

import Dropdown from '@/components/Dropdown.vue'
import FormInput from '@/components/FormInput.vue'

import Alert from '@/components/Alert.vue'
import FormButton from '@/components/FormButton.vue'
import { useAuthStore } from '@/stores/auth'
import { useOrdersStore } from '@/stores/orders'
import { storeToRefs } from 'pinia'
import { computed, ref } from 'vue'

const assetSymbols = Object.entries(AssetSymbol).map(([label, value]) => ({
    label,
    value,
}))

const sideOptions = [
    { label: 'Buy', value: 'buy' },
    { label: 'Sell', value: 'sell' },
]

const form = ref<HTMLFormElement>()
const errorMessage = ref<string | null>(null)

const { user } = storeToRefs(useAuthStore())
const orderStore = useOrdersStore()

const symbol = ref(null)
const side = ref<Order['side']>('buy')
const price = ref<number | null>(null)
const amount = ref(0)

const emit = defineEmits(['close'])

const userHasEnoughBalance = computed(() => {
    if (!price.value || !amount.value) return false
    if (side.value === 'sell') {
        const asset = user.value?.assets.find(
            (asset) => asset.symbol === symbol.value,
        )
        return asset !== undefined && asset.amount >= amount.value
    }

    return (user.value?.balance ?? 0) >= price.value * amount.value
})

const onSubmit = async () => {
    const isValid = form.value?.checkValidity()

    if (!isValid) return

    if (symbol.value === null) {
        return (errorMessage.value = 'Please select an asset')
    }

    if (!userHasEnoughBalance.value) {
        return (errorMessage.value = 'Insufficient balance')
    }

    try {
        await orderStore.placeOrder({
            symbol: symbol.value,
            side: side.value!,
            price: price.value!,
            amount: amount.value,
        })

        // Reset form
        symbol.value = null
        side.value = 'buy'
        price.value = null
        amount.value = 0

        emit('close')
    } catch (error) {
        errorMessage.value = (error as Error).message
    }
}
</script>

<template>
    <form
        action="/api/order"
        method="POST"
        ref="form"
        @submit.prevent="onSubmit"
        @input="errorMessage = null"
    >
        <fieldset
            class="grid grid-cols-2 gap-6"
            :disabled="orderStore.creating"
        >
            <Dropdown
                :options="assetSymbols"
                v-model="symbol"
                @update:modelValue="errorMessage = null"
            >
                Asset
            </Dropdown>

            <Dropdown
                :options="sideOptions"
                label="Symbol"
                name="symbol"
                v-model="side"
                required
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
                <FormButton
                    class="w-full"
                    type="submit"
                    :disabled="errorMessage !== null || orderStore.creating"
                >
                    Place Order
                </FormButton>
            </div>
        </fieldset>

        <Alert
            v-if="errorMessage !== null"
            class="mt-4"
            title="Unable to place order"
            :messages="[errorMessage]"
        />
    </form>
</template>
