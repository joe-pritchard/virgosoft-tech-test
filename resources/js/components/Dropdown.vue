<script lang="ts" setup>
import { CheckIcon, ChevronDownIcon } from '@heroicons/vue/20/solid'
import {
    computed,
    ComputedRef,
    nextTick,
    PropType,
    Ref,
    ref,
    useAttrs,
    watch,
} from 'vue'

export type SelectValue = string | number | null

export interface SelectOption {
    label: string
    value: any
}

const attrs = useAttrs()
const emit = defineEmits(['update:modelValue'])
const props = defineProps({
    /**
     * The selected option (or options if I'm a multiple select) bound to v-model
     */
    modelValue: {
        type: [String, Number, Array] as PropType<SelectValue | SelectValue[]>,
        required: false,
        default: null,
    },
    options: {
        type: Array as PropType<SelectOption[]>,
        required: true,
    },
    errors: {
        type: Array as () => string[],
        required: false,
        default: () => [],
    },
    disabled: { type: Boolean, required: false, default: false },
    loading: { type: Boolean, required: false, default: false },
})

const isOpen = ref(false)
const highlightedOption: Ref<SelectOption | null> = ref(null)

const selectedOption: ComputedRef<SelectOption | null> = computed(() => {
    return (
        props.options.find((option) => option.value === props.modelValue) ||
        null
    )
})

/**
 * The label to display above the input
 */
const mainLabel: ComputedRef<string> = computed(() => {
    return selectedOption.value ? selectedOption.value.label : 'Select one...'
})

// Two variables needed to set the list's width based on the element's intrinsic size, will be set in the isOpen watcher
const listWidth = ref('unset')
const listContainer: Ref<undefined | HTMLDivElement> = ref()
const listParent: Ref<undefined | HTMLDivElement> = ref()

/**
 * On click, close the dropdown, as long as the clicked element is not within the dropdown component
 * @param {MouseEvent} event
 */
const clickaway = (event: MouseEvent): void => {
    if (!(event.target as HTMLElement).closest('.listbox')) {
        isOpen.value = false
    }
}

watch(isOpen, (isOpen: boolean) => {
    if (isOpen) {
        // on open, register the clickaway listener
        document.addEventListener('click', clickaway)

        // and calculate the width of the list
        nextTick(() => {
            listWidth.value = 'unset'

            nextTick(() => {
                const defaultWidth =
                    (listContainer.value?.getBoundingClientRect().width ?? 0) +
                    16
                const parentWidth =
                    listParent.value?.getBoundingClientRect().width ?? 0

                listWidth.value = `${Math.max(defaultWidth, parentWidth)}px`
            })
        })
    } else {
        // on close, remove the clickaway listener
        document.removeEventListener('click', clickaway)
    }
})

/**
 * Determine if the given option is selected
 */
const isSelected = (option: SelectOption): boolean => {
    return props.modelValue === option.value
}

/**
 * Get the HTML id that will be used for a given option's list element
 */
const optionId = (index: number): string => {
    return `${attrs.id}-${index}`
}

const highlightNextOption = (): void => {
    if (!isOpen.value) {
        // when using the keyboard to highlight a value, open the box
        isOpen.value = true
    } else if (highlightedOption.value === null) {
        // if we don't have anything highlighted we can just highlight the first option
        highlightedOption.value = props.options[0]
    } else if (
        highlightedOption.value.value !==
        props.options[props.options.length - 1].value
    ) {
        // else if we're not already at the end, we can just highlight the next option
        const index = props.options.findIndex(
            (option) => option.value === highlightedOption.value?.value,
        )
        if (index > -1) {
            highlightedOption.value = props.options[index + 1]
        }
    }
}

const highlightPreviousOption = (): void => {
    if (!isOpen.value) {
        // when using the keyboard to highlight a value, open the box
        isOpen.value = true
    } else {
        const index = props.options.findIndex(
            (option) => option.value === highlightedOption.value?.value,
        )
        if (index === -1) {
            // if nothing was highlighted, then highlight the last option
        } else if (index > 0) {
            // else just highlight the previous value. Don't wrap if first item already selected
            highlightedOption.value = props.options[index - 1]
        }
    }
}

const highlightFirstOption = (): void => {
    if (isOpen.value) {
        highlightedOption.value = props.options[0]

        const optionElement = document.getElementById(
            optionId(highlightedOption.value.value),
        )
        optionElement?.scrollIntoView({ behavior: 'smooth' })
    }
}

const highlightLastOption = (): void => {
    if (isOpen.value) {
        highlightedOption.value = props.options[props.options.length - 1]

        const optionElement = document.getElementById(
            optionId(highlightedOption.value.value),
        )
        optionElement?.scrollIntoView({ behavior: 'smooth' })
    }
}

const selectHighlightedOption = (): void => {
    if (highlightedOption.value !== null) {
        selectOption(highlightedOption.value)
    }
}

const selectOption = (option: SelectOption): void => {
    emit('update:modelValue', option.value)
    isOpen.value = false
}
</script>

<template>
    <div
        class="listbox"
        @keydown.prevent.down="highlightNextOption"
        @keydown.prevent.up="highlightPreviousOption"
        @keydown.prevent.home="highlightFirstOption"
        @keydown.prevent.end="highlightLastOption"
        @keyup.esc="isOpen = false"
        @keyup.enter="selectHighlightedOption"
        @keyup.space="selectHighlightedOption"
    >
        <label
            v-if="$slots.default"
            :id="`listbox-label-${$attrs.id}`"
            class="text-medium block text-sm font-medium"
        >
            <slot />
        </label>
        <div
            class="text-medium-shade"
            :class="{
                'mt-2': $slots.default,
                'text-opacity-50 pointer-events-none': disabled,
            }"
        >
            <button
                ref="listParent"
                type="button"
                aria-haspopup="listbox"
                aria-expanded="true"
                :aria-labelledby="`listbox-label-${$attrs.id}`"
                class="relative w-full cursor-default rounded-md bg-white py-2 pr-10 pl-3 text-left outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm"
                @click="isOpen = !isOpen"
            >
                <span class="block truncate" :title="mainLabel">{{
                    mainLabel
                }}</span>
                <span
                    class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-2"
                >
                    <ChevronDownIcon
                        class="text-medium-tint h-5 w-5 transition-transform duration-150"
                        :class="{ 'rotate-180 transform': isOpen }"
                    />
                </span>
            </button>
            <Transition name="select-options">
                <div
                    v-if="isOpen"
                    ref="listContainer"
                    class="absolute z-10 mt-1 rounded-md bg-white shadow-lg"
                    :style="{ width: listWidth }"
                >
                    <ul
                        tabindex="-1"
                        role="listbox"
                        :aria-labelledby="`listbox-label-${$attrs.id}`"
                        class="ring-opacity-5 max-h-60 overflow-auto rounded-md py-1 text-base ring-1 ring-indigo-600 focus:outline-none sm:text-sm"
                    >
                        <li
                            v-for="(option, index) in options"
                            :id="optionId(index)"
                            :key="option.value"
                            role="option"
                            class="relative cursor-default py-2 pr-9 pl-3 text-left select-none"
                            :class="{
                                'bg-indigo-600 text-white':
                                    highlightedOption &&
                                    highlightedOption.value === option.value,
                                'text-gray-900':
                                    highlightedOption &&
                                    highlightedOption.value !== option.value,
                            }"
                            @mouseenter="highlightedOption = option"
                            @click="selectOption(option)"
                            @keyup.self.enter="selectOption(option)"
                        >
                            <span
                                class="block truncate"
                                :title="option.label"
                                :class="{
                                    'font-normal': !isSelected(option),
                                    'font-medium': isSelected(option),
                                }"
                            >
                                <slot name="option" :option="option">
                                    {{ option.label }}
                                </slot>
                            </span>

                            <span
                                v-if="isSelected(option)"
                                class="absolute inset-y-0 right-0 flex items-center pr-4"
                                :class="{
                                    'text-white':
                                        highlightedOption &&
                                        highlightedOption.value ===
                                            option.value,
                                    'text-indigo-600':
                                        highlightedOption &&
                                        highlightedOption.value !==
                                            option.value,
                                }"
                            >
                                <CheckIcon class="h-5 w-5" />
                            </span>
                        </li>
                    </ul>
                </div>
            </Transition>
        </div>

        <p v-if="errors && errors[attrs.id]" class="text-sm text-red-500">
            {{ errors[attrs.id] }}
        </p>
    </div>
</template>
