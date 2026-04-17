<!-- resources/js/Components/Admin/Select/ItemsPerPageSelect.vue -->
<script setup>
import { defineProps, defineEmits } from 'vue'
import { useI18n } from 'vue-i18n'

const { t } = useI18n()

const props = defineProps({
    itemsPerPage: {
        type: Number,
        required: true
    }
})

const emits = defineEmits(['update:itemsPerPage'])

const updateItemsPerPage = (event) => {
    emits('update:itemsPerPage', Number(event.target.value))
}
</script>

<template>
    <select :title="t('titleItemsPerPage')" :value="props.itemsPerPage" @change="updateItemsPerPage"
            class="w-20 sm:ml-4 px-3 py-0.5 form-select bg-white dark:bg-gray-200
                 text-gray-600 dark:text-gray-900
                 border border-slate-400 dark:border-slate-600
                 rounded-sm shadow-sm">
        <option value="4">4</option>
        <option value="8">8</option>
        <option value="16">16</option>
        <option value="32">32</option>
        <option value="64">64</option>
        <option value="128">128</option>
        <option value="256">256</option>
    </select>
</template>
