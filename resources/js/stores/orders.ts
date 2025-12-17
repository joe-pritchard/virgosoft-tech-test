import { Order } from '@/types'
import { fetchJson } from '@/utils/fetch'
import { defineStore } from 'pinia'
import { ref } from 'vue'

export const useOrdersStore = defineStore('orders', () => {
    const creating = ref(false)
    const cancelling = ref(false)
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

    const patchOrder = async (order: Order) => {
        const orderIndex = orders.value.findIndex((o) => o.id === order.id)
        if (orderIndex > -1) {
            orders.value = orders.value.toSpliced(orderIndex, 1, order)
        }
    }

    const cancelOrder = async (id: number) => {
        cancelling.value = true

        const response = await fetchJson<Order>(
            `/api/order/${id}/cancel`,
            'POST',
        ).finally(() => (cancelling.value = false))

        return patchOrder(response.data)
    }

    return {
        creating,
        cancelling,
        loading,
        orders,
        fetchOrders,
        placeOrder,
        patchOrder,
        cancelOrder,
    }
})
