<script setup>
import { ref } from 'vue'
import { router } from '@inertiajs/vue3'
import { useI18n } from 'vue-i18n'

const { t } = useI18n()

const props = defineProps({
    itemsPerPage: {
        type: Number,
        required: true,
    },
    updateRoute: {
        type: String,
        required: true,
    },
})

const processing = ref(false)

const reloadPage = () => {
    window.location.href = window.location.pathname
}

const updateItemsPerPage = (event) => {
    const value = Number(event.target.value)

    if (value === props.itemsPerPage || processing.value) {
        return
    }

    processing.value = true

    router.put(
        route(props.updateRoute),
        { value },
        {
            preserveScroll: true,
            preserveState: false,

            onSuccess: () => {
                reloadPage()
            },

            onError: () => {
                processing.value = false
            },

            onFinish: () => {
                processing.value = false
            },
        }
    )
}
</script>

<template>
    <select
        :title="t('titleItemsPerPage')"
        :value="itemsPerPage"
        :disabled="processing"
        @change="updateItemsPerPage"
        class="w-20 px-3 py-0.5 form-select bg-white dark:bg-gray-200
               text-gray-600 dark:text-gray-900
               border border-slate-400 dark:border-slate-600
               rounded-sm shadow-sm disabled:opacity-50"
    >
        <option value="4">4</option>
        <option value="8">8</option>
        <option value="16">16</option>
        <option value="32">32</option>
        <option value="64">64</option>
        <option value="128">128</option>
        <option value="256">256</option>
    </select>
</template>
