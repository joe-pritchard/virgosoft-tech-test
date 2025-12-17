<script setup lang="ts">
import { useOrdersStore } from '@/stores/orders'
import { Order } from '@/types'
import { formatCurrency } from '@/utils/formatCurrency'
import { formatDate } from '@/utils/formatDate'
import { PropType } from 'vue'

defineProps({ order: { type: Object as PropType<Order>, required: true } })

const orderStore = useOrdersStore()
</script>

<template>
    <tr>
        <td
            class="py-4 pr-3 pl-4 text-sm font-medium whitespace-nowrap text-gray-900 sm:pl-0"
            :title="order.created_at"
        >
            {{ formatDate(order.created_at) }}
        </td>
        <td class="px-3 py-4 text-sm whitespace-nowrap text-gray-500">
            {{ order.symbol }}
        </td>
        <td class="px-3 py-4 text-sm whitespace-nowrap text-gray-500">
            {{ formatCurrency(order.price) }}
        </td>
        <td class="px-3 py-4 text-sm whitespace-nowrap text-gray-500">
            {{ order.amount }}
        </td>
        <td class="px-3 py-4 text-sm whitespace-nowrap text-gray-500">
            {{ order.status }}
        </td>
        <td
            class="py-4 pr-4 pl-3 text-right text-sm font-medium whitespace-nowrap sm:pr-0"
        >
            <button
                class="cursor-pointer text-red-600 hover:text-red-900"
                :disabled="orderStore.cancelling"
                @click.prevent="() => orderStore.cancelOrder(order.id)"
            >
                Cancel
            </button>
        </td>
    </tr>
</template>

<style scoped></style>
