<script setup lang="ts">
import { useVModel } from '@vueuse/core'
import { useId } from 'vue'

const props = defineProps({
    label: { type: String, required: true },
    modelValue: { type: String, required: true },
})

const value = useVModel(props, 'modelValue')
const id = useId()

defineOptions({ inheritAttrs: false })
</script>

<template>
    <label :for="id" class="block text-sm/6 font-medium text-gray-900">
        {{ label }}
    </label>
    <div class="mt-2">
        <input
            v-bind="$attrs"
            :id="id"
            class="block w-full rounded-md bg-white px-3 py-1.5 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6"
            v-model="value"
        />
    </div>
</template>
