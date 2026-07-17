<script setup>
import { computed, ref, watch } from 'vue'
import { useI18n } from 'vue-i18n'
import VueMultiselect from 'vue-multiselect'

import ActivityCheckbox from '@/Components/Admin/UI/Checkbox/ActivityCheckbox.vue'
import LabelCheckbox from '@/Components/Admin/UI/Checkbox/LabelCheckbox.vue'
import InputDecimalExt from '@/Components/Admin/UI/Input/InputDecimalExt.vue'
import InputError from '@/Components/Admin/UI/Input/InputError.vue'
import InputText from '@/Components/Admin/UI/Input/InputText.vue'
import LabelInput from '@/Components/Admin/UI/Input/LabelInput.vue'

const { t } = useI18n()

const props = defineProps({
    modelValue: { type: Array, default: () => [] },
    attributes: { type: [Array, Object], default: () => [] },
    attributeValues: { type: [Array, Object], default: () => [] },
    errors: { type: Object, default: () => ({}) }
})

const emit = defineEmits(['update:modelValue'])

const resourceList = (value) => {
    if (Array.isArray(value)) return value
    if (Array.isArray(value?.data)) return value.data
    return []
}

const translationTitle = (item) => {
    return item?.translation?.title || item?.title || item?.code || `ID: ${item?.id}`
}

const normalizeNullableInput = (value) => {
    return value === null || value === undefined ? '' : value
}

const normalizeRow = (item = {}, order = 0) => ({
    id: item.id || null,
    market_attribute_id: item.market_attribute_id || item.attribute?.id || null,
    market_attribute_value_id: item.market_attribute_value_id ||
        item.attribute_value?.id ||
        item.attributeValue?.id || null,
    value_string: item.value_string || '',
    value_number: normalizeNullableInput(item.value_number),
    value_boolean: item.value_boolean === null ||
    item.value_boolean === undefined ? null : Boolean(item.value_boolean),
    value_date: item.value_date ? String(item.value_date).slice(0, 10) : '',
    value_json: Array.isArray(item.value_json) ? item.value_json : null,
    unit: item.unit || '',
    order: Number(item.order ?? order),
    activity: item.activity === undefined ? true : Boolean(item.activity)
})

const normalizeRows = (items) => resourceList(items).map((item, index) => normalizeRow(item, index))

const rows = ref(normalizeRows(props.modelValue))
let syncingFromParent = false

watch(() => props.modelValue, (value) => {
    const normalized = normalizeRows(value)

    if (JSON.stringify(normalized) === JSON.stringify(rows.value)) return

    syncingFromParent = true
    rows.value = normalized
    syncingFromParent = false
}, { deep: true })

watch(rows, (value) => {
    if (syncingFromParent) return

    emit('update:modelValue', value.map((item, index) => ({
        ...item,
        order: index
    })))
}, { deep: true, immediate: true })

const attributeList = computed(() => resourceList(props.attributes))
const attributeValueList = computed(() => resourceList(props.attributeValues))

const attributeOptions = computed(() => attributeList.value.map(item => ({
    ...item,
    label: `[ID: ${item.id}] ${translationTitle(item)}${item.unit ? ` (${item.unit})` : ''}`
})))

const selectedAttribute = (row) => {
    return attributeOptions.value.find(item =>
        Number(item.id) === Number(row.market_attribute_id)) || null
}

const attributeType = (row) => {
    return selectedAttribute(row)?.type || 'string'
}

const attributeValueOptions = (row) => {
    const attribute = selectedAttribute(row)

    if (!attribute) return []

    const embeddedValues = resourceList(attribute.values)
    const values = embeddedValues.length
        ? embeddedValues
        : attributeValueList.value.filter(item =>
            Number(item.market_attribute_id) === Number(attribute.id))

    return values.map(item => ({
        ...item,
        label: `[ID: ${item.id}] ${translationTitle(item)}`
    }))
}

const selectedReferenceValue = (row) => {
    return attributeValueOptions(row).find(item =>
        Number(item.id) === Number(row.market_attribute_value_id)) || null
}

const addRow = () => {
    rows.value.push(normalizeRow({}, rows.value.length))
}

const removeRow = (index) => {
    rows.value.splice(index, 1)
}

const updateAttribute = (row, value) => {
    row.market_attribute_id = value?.id ?? null
    row.market_attribute_value_id = null
    row.value_string = ''
    row.value_number = ''
    row.value_boolean = null
    row.value_date = ''
    row.value_json = null
    row.unit = value?.unit || ''
}

const updateReferenceValue = (row, value) => {
    row.market_attribute_value_id = value?.id ?? null
}

const rowError = (index, field = '') => {
    const exactKey = field ? `attribute_values.${index}.${field}` : `attribute_values.${index}`

    return props.errors?.[exactKey] || null
}
</script>

<template>
    <div class="my-5 p-3 border border-slate-300 dark:border-slate-500
                bg-white dark:bg-slate-800 rounded-sm">
        <div class="mb-3 flex items-center justify-between">
            <h3 class="text-base font-semibold text-slate-700 dark:text-slate-100">
                {{ t('attributes') }}
            </h3>

            <button type="button"
                    class="flex items-center rounded-sm bg-blue-600 px-3 py-1
                           text-xs font-semibold text-white hover:bg-blue-700"
                    @click="addRow">
                <svg class="w-4 h-4 fill-current opacity-50 shrink-0"
                     viewBox="0 0 16 16">
                    <path
                        d="M15 7H9V1c0-.6-.4-1-1-1S7 .4 7 1v6H1c-.6 0-1 .4-1 1s.4 1 1 1h6v6c0 .6.4 1 1 1s1-.4 1-1V9h6c.6 0 1-.4 1-1s-.4-1-1-1z" ></path>
                </svg>
                <span class="hidden sm:inline-block ml-2">
                    {{ t('addMarketAttribute') }}
                </span>
            </button>
        </div>

        <InputError class="mb-3" :message="errors?.attribute_values" />

        <div v-if="rows.length" class="space-y-3">
            <div v-for="(row, index) in rows" :key="row.id || `new-${index}`"
                 class="rounded-sm p-3 border border-slate-300 dark:border-slate-500">
                <div class="grid grid-cols-1 gap-3 lg:grid-cols-2">
                    <div class="min-w-0">
                        <LabelInput :for="`attribute-${index}`" :value="t('marketAttribute')" />

                        <VueMultiselect
                            :id="`attribute-${index}`"
                            :model-value="selectedAttribute(row)"
                            :options="attributeOptions"
                            label="label"
                            track-by="id"
                            :searchable="true"
                            :allow-empty="true"
                            :show-labels="false"
                            placeholder="Выберите характеристику"
                            class="w-full min-w-0"
                            @update:modelValue="updateAttribute(row, $event)"
                        />
                    </div>

                    <div v-if="attributeValueOptions(row).length" class="min-w-0">
                        <LabelInput
                            :for="`attribute-value-${index}`"
                            :value="t('marketAttributeValue')" />

                        <VueMultiselect
                            :id="`attribute-value-${index}`"
                            :model-value="selectedReferenceValue(row)"
                            :options="attributeValueOptions(row)"
                            label="label"
                            track-by="id"
                            :searchable="true"
                            :allow-empty="true"
                            :show-labels="false"
                            placeholder="Выберите значение"
                            class="w-full min-w-0"
                            @update:modelValue="updateReferenceValue(row, $event)"
                        />
                    </div>

                    <div v-else-if="attributeType(row) === 'number'">
                        <LabelInput :for="`attribute-number-${index}`" :value="t('value')" />
                        <InputDecimalExt
                            :id="`attribute-number-${index}`"
                            v-model="row.value_number"
                            :step="0.0001"
                            :fraction-digits="4"
                            class="w-full" />
                    </div>

                    <div v-else-if="attributeType(row) === 'boolean'">
                        <LabelInput :for="`attribute-boolean-${index}`" :value="t('value')" />

                        <select
                            :id="`attribute-boolean-${index}`"
                            v-model="row.value_boolean"
                            class="w-full rounded-sm border border-slate-400 bg-white px-3 py-2
                                   text-slate-700 dark:bg-slate-200 dark:text-slate-900"
                        >
                            <option :value="null">—</option>
                            <option :value="true">{{ t('yes') }}</option>
                            <option :value="false">{{ t('no') }}</option>
                        </select>
                    </div>

                    <div v-else-if="attributeType(row) === 'date'">
                        <LabelInput :for="`attribute-date-${index}`" :value="t('value')" />
                        <InputText
                            :id="`attribute-date-${index}`"
                            v-model="row.value_date"
                            type="date"
                            class="w-full" />
                    </div>

                    <div v-else>
                        <LabelInput :for="`attribute-string-${index}`" :value="t('value')" />
                        <InputText
                            :id="`attribute-string-${index}`"
                            v-model="row.value_string"
                            type="text" />
                    </div>

                    <div>
                        <LabelInput :for="`attribute-unit-${index}`" :value="t('unit')" />
                        <InputText
                            :id="`attribute-unit-${index}`"
                            v-model="row.unit"
                            type="text" />
                    </div>

                    <div class="flex items-end justify-between gap-3">
                        <div class="flex items-center gap-2">
                            <ActivityCheckbox
                                :id="`attribute-activity-${index}`"
                                v-model="row.activity" />
                            <LabelCheckbox
                                :for="`attribute-activity-${index}`"
                                :text="t('activity')"
                                class="text-sm h-8 flex items-center" />
                        </div>

                        <button type="button"
                                class="w-fit text-xs font-semibold px-3 py-1 rounded-sm
                                       bg-rose-500 hover:bg-rose-700 text-white
                                       flex items-center justify-center gap-1"
                                @click="removeRow(index)">
                            <svg
                                class="w-3 h-3 fill-current"
                                viewBox="0 0 448 512">
                                <path
                                    d="M0 84V56c0-13.3 10.7-24 24-24h112l9.4-18.7c4-8.2 12.3-13.3 21.4-13.3h114.3c9.1 0 17.4 5.1 21.5 13.3L312 32h112c13.3 0 24 10.7 24 24v28c0 6.6-5.4 12-12 12H12C5.4 96 0 90.6 0 84zm416 56v324c0 26.5-21.5 48-48 48H80c-26.5 0-48-21.5-48-48V140c0-6.6 5.4-12 12-12h360c6.6 0 12 5.4 12 12zm-272 68c0-8.8-7.2-16-16-16s-16 7.2-16 16v224c0 8.8 7.2 16 16 16s16-7.2 16-16V208zm96 0c0-8.8-7.2-16-16-16s-16 7.2-16 16v224c0 8.8 7.2 16 16 16s16-7.2 16-16V208zm96 0c0-8.8-7.2-16-16-16s-16 7.2-16 16v224c0 8.8 7.2 16 16 16s16-7.2 16-16V208z" />
                            </svg>
                            <span class="hidden sm:inline-block ml-2">
                                {{ t('delete') }}
                            </span>
                        </button>
                    </div>
                </div>

                <InputError :message="rowError(index)" />
                <InputError :message="rowError(index, 'market_attribute_id')" />
                <InputError :message="rowError(index, 'market_attribute_value_id')" />
                <InputError :message="rowError(index, 'value_string')" />
                <InputError :message="rowError(index, 'value_number')" />
                <InputError :message="rowError(index, 'value_boolean')" />
                <InputError :message="rowError(index, 'value_date')" />
                <InputError :message="rowError(index, 'value_json')" />
                <InputError :message="rowError(index, 'unit')" />
            </div>
        </div>

        <div v-else class="py-4 text-center text-sm text-slate-500 dark:text-slate-300">
            {{ t('noData') }}
        </div>
    </div>
</template>
