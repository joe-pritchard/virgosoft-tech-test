<script setup lang="ts">
import { XCircleIcon } from '@heroicons/vue/20/solid'

defineProps({
    title: { type: String, required: true },
    messages: { type: Array as () => PropType<string[]>, required: true },
})
</script>
<template>
    <div class="rounded-md bg-red-50 p-4">
        <div class="flex">
            <div class="shrink-0">
                <XCircleIcon class="size-5 text-red-400" aria-hidden="true" />
            </div>
            <div class="ml-3">
                <h3 class="text-sm font-medium text-red-800">
                    {{ title }}
                </h3>
                <div class="mt-2 text-sm text-red-700">
                    <ul role="list" class="list-disc space-y-1 pl-5">
                        <li v-for="(message, index) in messages" :key="index">
                            {{ message }}
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</template>
