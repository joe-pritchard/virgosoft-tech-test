import { Order } from '@/types'
import { fetchJson } from '@/utils/fetch'
import { defineStore } from 'pinia'
import { ref } from 'vue'

export const useOrdersStore = defineStore('orders', () => {
    const creating = ref(false)
    const loading = ref(false)
    const orders = ref<Order[]>([])

    const fetchOrders = async () => {
        loading.value = true
        const response = await fetchJson<Order[]>('/api/order', 'GET').finally(
            () => (loading.value = false),
        )

        orders.value = response.data
    }

    const placeOrder = async (orderData: Partial<Order>) => {
        creating.value = true

        const response = await fetchJson<Order>(
            '/api/order',
            'POST',
            orderData,
        ).finally(() => (creating.value = false))

        orders.value.push(response.data)
    }

    const cancelOrder = async (orderId: number) => {
        // todo
    }

    return { creating, loading, orders, fetchOrders, placeOrder }
})
