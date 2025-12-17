import { useAuthStore } from '@/stores/auth'
import { AssetSymbol, Order } from '@/types'
import { fetchJson } from '@/utils/fetch'
import { defineStore } from 'pinia'
import { ref } from 'vue'

interface FetchOrderOptions {
    symbol?: AssetSymbol
    userId?: number
}

export const useOrdersStore = defineStore('orders', () => {
    const authStore = useAuthStore()
    const creating = ref(false)
    const cancelling = ref(false)
    const loading = ref(false)
    const orders = ref<Order[]>([])

    const fetchOrders = async (options: FetchOrderOptions) => {
        loading.value = true
        const search = new URLSearchParams()
        if (options.symbol) {
            search.append('symbol', options.symbol)
        }

        if (options.userId) {
            search.append('user', options.userId)
        }

        const response = await fetchJson<Order[]>(
            `/api/orders?${search.toString()}`,
            'GET',
        ).finally(() => (loading.value = false))

        orders.value = response.data
    }

    const placeOrder = async (orderData: Partial<Order>) => {
        creating.value = true

        const response = await fetchJson<Order>(
            '/api/orders',
            'POST',
            orderData,
        ).finally(() => (creating.value = false))

        orders.value.push(response.data)
        return authStore.fetchUser()
    }

    const patchOrder = async (order: Order) => {
        const orderIndex = orders.value.findIndex((o) => o.id === order.id)
        if (orderIndex > -1) {
            orders.value = orders.value.toSpliced(orderIndex, 1, order)
            await authStore.fetchUser()
        }
    }

    const cancelOrder = async (id: number) => {
        cancelling.value = true

        const response = await fetchJson<Order>(
            `/api/orders/${id}/cancel`,
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
