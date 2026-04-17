<script setup>
import { useI18n } from 'vue-i18n'

const { t } = useI18n()

const props = defineProps({
    modelValue: { type: String, default: 'grid' },
    gridValue: { type: String, default: 'grid' },
    rowValue: { type: String, default: 'row' },
    size: { type: String, default: 'md' }, // sm | md
})

const emit = defineEmits(['update:modelValue'])

const setView = (mode) => {
    emit('update:modelValue', mode)
}

const buttonClass = (mode) => {
    const active = props.modelValue === mode

    const sizeClass = props.size === 'sm'
        ? 'px-1.5 py-1.5'
        : 'px-1.5 py-1.5'

    return [
        sizeClass,
        'transition rounded-sm border border-gray-400 dark:border-gray-500 ' +
        'hover:bg-gray-50 dark:hover:bg-gray-800',
        active
            ? 'bg-blue-50 text-blue-700 dark:bg-blue-950 dark:text-blue-300'
            : 'bg-gray-100 dark:bg-gray-900 text-gray-500 dark:text-gray-400',
    ].join(' ')
}
</script>

<template>
    <div class="flex items-center gap-2">
        <button
            type="button"
            @click="setView(gridValue)"
            :class="buttonClass(gridValue)"
            :title="t('gridView')"
        >
            <svg class="h-4 w-4 hover:text-blue-500" fill="currentColor" viewBox="0 0 512 512">
                <path
                    d="M296 32h192c13.255 0 24 10.745 24 24v160c0 13.255-10.745 24-24 24H296c-13.255 0-24-10.745-24-24V56c0-13.255 10.745-24 24-24zm-80 0H24C10.745 32 0 42.745 0 56v160c0 13.255 10.745 24 24 24h192c13.255 0 24-10.745 24-24V56c0-13.255-10.745-24-24-24zM0 296v160c0 13.255 10.745 24 24 24h192c13.255 0 24-10.745 24-24V296c0-13.255-10.745-24-24-24H24c-13.255 0-24 10.745-24 24zm296 184h192c13.255 0 24-10.745 24-24V296c0-13.255-10.745-24-24-24H296c-13.255 0-24 10.745-24 24v160c0 13.255 10.745 24 24 24z" />
            </svg>
        </button>

        <button
            type="button"
            @click="setView(rowValue)"
            :class="buttonClass(rowValue)"
            :title="t('horizontalView')"
        >
            <svg class="h-4 w-4 hover:text-blue-500" fill="currentColor" viewBox="0 0 512 512">
                <path
                    d="M149.333 216v80c0 13.255-10.745 24-24 24H24c-13.255 0-24-10.745-24-24v-80c0-13.255 10.745-24 24-24h101.333c13.255 0 24 10.745 24 24zM0 376v80c0 13.255 10.745 24 24 24h101.333c13.255 0 24-10.745 24-24v-80c0-13.255-10.745-24-24-24H24c-13.255 0-24 10.745-24 24zM125.333 32H24C10.745 32 0 42.745 0 56v80c0 13.255 10.745 24 24 24h101.333c13.255 0 24-10.745 24-24V56c0-13.255-10.745-24-24-24zm80 448H488c13.255 0 24-10.745 24-24v-80c0-13.255-10.745-24-24-24H205.333c-13.255 0-24 10.745-24 24v80c0 13.255 10.745 24 24 24zm-24-424v80c0 13.255 10.745 24 24 24H488c13.255 0 24-10.745 24-24V56c0-13.255-10.745-24-24-24H205.333c-13.255 0-24 10.745-24 24zm24 264H488c13.255 0 24-10.745 24-24v-80c0-13.255-10.745-24-24-24H205.333c-13.255 0-24 10.745-24 24v80c0 13.255 10.745 24 24 24z" />
            </svg>
        </button>
    </div>
</template>
