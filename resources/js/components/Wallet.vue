<script setup lang="ts">
import { useAuthStore } from '@/stores/auth'
import { formatCurrency } from '@/utils/formatCurrency'
import { storeToRefs } from 'pinia'

const { user } = storeToRefs(useAuthStore())
</script>

<template>
    <h1 class="text-base font-semibold text-gray-900">Your Wallet</h1>

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
                                class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900"
                            >
                                Asset
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
                                Pending amount (locked)
                            </th>
                        </tr>
                    </thead>
                    <tbody
                        v-if="user !== null"
                        class="divide-y divide-gray-200"
                    >
                        <tr>
                            <td
                                class="px-3 py-4 text-sm whitespace-nowrap text-gray-500"
                            >
                                USD
                            </td>
                            <td
                                class="px-3 py-4 text-sm whitespace-nowrap text-gray-500"
                            >
                                {{ formatCurrency(user.balance) }}
                            </td>
                            <td
                                class="px-3 py-4 text-sm whitespace-nowrap text-gray-500"
                            >
                                --
                            </td>
                        </tr>

                        <tr v-for="asset in user.assets" :key="asset.symbol">
                            <td
                                class="px-3 py-4 text-sm whitespace-nowrap text-gray-500"
                            >
                                {{ asset.symbol }}
                            </td>
                            <td
                                class="px-3 py-4 text-sm whitespace-nowrap text-gray-500"
                            >
                                {{ asset.amount }}
                            </td>
                            <td
                                class="px-3 py-4 text-sm whitespace-nowrap text-gray-500"
                            >
                                {{ asset.locked_amount }}
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</template>

<style scoped></style>
