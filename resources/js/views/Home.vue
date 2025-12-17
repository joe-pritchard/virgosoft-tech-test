<script setup lang="ts">
import Card from '@/components/Card.vue'
import FormButton from '@/components/FormButton.vue'
import Modal from '@/components/Modal.vue'
import OrderForm from '@/components/OrderForm.vue'
import OrderTable from '@/components/OrderTable.vue'
import Wallet from '@/components/Wallet.vue'
import { useAuthStore } from '@/stores/auth'
import { useOrdersStore } from '@/stores/orders'
import { Order } from '@/types'
import { PlusIcon } from '@heroicons/vue/20/solid'
import { useEcho } from '@laravel/echo-vue'
import { storeToRefs } from 'pinia'
import { onMounted, onUnmounted, ref, watch } from 'vue'

interface OrderMatchedEvent {
    order: Order
}

const isOrderFormOpen = ref(false)
const orderStore = useOrdersStore()
const { user } = storeToRefs(useAuthStore())

const onCancel = async (orderId: number) => {
    await orderStore.cancelOrder(orderId)
}

let leaveChannel: (() => void) | null = null
onMounted(() => {
    orderStore.fetchOrders({ userId: user.value.id })
})

onUnmounted(() => {
    if (leaveChannel) {
        leaveChannel()
    }
})

watch(
    user,
    () => {
        if (!user.value) return

        const { leave } = useEcho(
            `user.${user.value.id}`,
            'App.Events.OrderMatched',
            (event: OrderMatchedEvent) => {
                orderStore.patchOrder(event.order)
            },
        )

        leaveChannel = leave
    },
    { immediate: true },
)
</script>

<template>
    <div class="flex flex-col gap-6">
        <Card class="w-full">
            <Wallet />
        </Card>

        <div class="grid grid-cols-3 gap-6">
            <Card class="col-span-2 w-full">
                <Modal :open="isOrderFormOpen" @close="isOrderFormOpen = false">
                    <template #header>Place order</template>
                    <OrderForm @close="isOrderFormOpen = false" />
                </Modal>
                <div v-if="orderStore.loading" class="text-center">
                    <h3 class="mt-2 text-sm font-semibold text-gray-900">
                        Loading orders...
                    </h3>
                    <p class="mt-1 text-sm text-gray-500">
                        Please wait while we fetch your orders
                    </p>
                </div>
                <div
                    v-else-if="orderStore.orders.length === 0"
                    class="text-center"
                >
                    <h3 class="mt-2 text-sm font-semibold text-gray-900">
                        No orders
                    </h3>
                    <p class="mt-1 text-sm text-gray-500">
                        Get started by placing your first order
                    </p>
                    <div class="mt-6">
                        <FormButton
                            type="button"
                            class="mx-auto flex items-center"
                            @click="isOrderFormOpen = true"
                        >
                            <PlusIcon
                                class="mr-1.5 -ml-0.5 size-5"
                                aria-hidden="true"
                            />
                            Place a new order
                        </FormButton>
                    </div>
                </div>

                <OrderTable
                    v-else
                    :orders="orderStore.orders"
                    @create="isOrderFormOpen = true"
                    @cancel="onCancel"
                />
            </Card>
            <Card class="w-full">
                <OrderBook />
            </Card>
        </div>
    </div>
</template>
