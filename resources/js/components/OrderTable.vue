<script setup lang="ts">
import FormButton from '@/components/FormButton.vue'
import { Order } from '@/types'
import { PropType } from 'vue'

defineEmits(['create', 'cancel'])
defineProps({
    orders: {
        type: Array as PropType<Order[]>,
        required: true,
    },
})
</script>
<template>
    <div class="sm:flex sm:items-center">
        <div class="sm:flex-auto">
            <h1 class="text-base font-semibold text-gray-900">Your orders</h1>
        </div>
        <div class="mt-4 sm:mt-0 sm:ml-16 sm:flex-none">
            <FormButton type="button" @click="$emit('create')">
                New order
            </FormButton>
        </div>
    </div>
    <div class="mt-8 flow-root">
        <div class="-mx-4 -my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
            <div
                class="inline-block min-w-full py-2 align-middle sm:px-6 lg:px-8"
            >
                <table class="relative min-w-full divide-y divide-gray-300">
                    <thead>
                        <tr>
                            <th
                                scope="col"
                                class="py-3.5 pr-3 pl-4 text-left text-sm font-semibold text-gray-900 sm:pl-0"
                            >
                                Placed
                            </th>
                            <th
                                scope="col"
                                class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900"
                            >
                                Asset
                            </th>
                            <th
                                scope="col"
                                class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900"
                            >
                                Price
                            </th>
                            <th
                                scope="col"
                                class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900"
                            >
                                Amount
                            </th>
                            <th
                                scope="col"
                                class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900"
                            >
                                Status
                            </th>
                            <th scope="col" class="py-3.5 pr-4 pl-3 sm:pr-0">
                                <span class="sr-only">Cancel</span>
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        <OrderTableRow
                            v-for="order in orders"
                            :key="order.id"
                        />
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</template>
