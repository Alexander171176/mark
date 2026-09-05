<script setup>
import { computed } from 'vue'
import { useI18n } from 'vue-i18n'
import VueMultiselect from 'vue-multiselect'

import InputError from '@/Components/Admin/UI/Input/InputError.vue'
import LabelInput from '@/Components/Admin/UI/Input/LabelInput.vue'

const { t } = useI18n()

const props = defineProps({
    modelValue: { type: Array, default: () => [] },
    products: { type: [Array, Object], default: () => [] },
    errors: { type: Object, default: () => ({}) },
    excludeProductId: { type: [Number, String, null], default: null },
})

const emit = defineEmits(['update:modelValue'])

const resourceList = (value) => {
    if (Array.isArray(value)) {
        return value
    }

    if (Array.isArray(value?.data)) {
        return value.data
    }

    return []
}

const translationTitle = (item) => {
    return item?.translation?.title
        || item?.url
        || `ID: ${item?.id}`
}

const productOptions = computed(() => {
    return resourceList(props.products)
        .filter((item) => {
            return !props.excludeProductId
                || Number(item.id) !== Number(props.excludeProductId)
        })
        .map((item) => ({
            ...item,

            label:
                `[ID: ${item.id}] ${translationTitle(item)}`
                + (item.sku ? ` — SKU: ${item.sku}` : ''),
        }))
})

const normalizeModelItem = (item, index = 0) => ({
    id: Number(
        item?.id
        ?? item?.related_product_id
        ?? item?.product?.id
    ),

    type:
        item?.type
        ?? item?.relation_type
        ?? item?.pivot?.type
        ?? 'related',

    order: Number(
        item?.order
        ?? item?.relation_order
        ?? item?.pivot?.order
        ?? index
    ),

    activity:
        item?.activity
        ?? item?.relation_activity
        ?? item?.pivot?.activity
        ?? true,
})

const normalizedModelValue = computed(() => {
    return resourceList(props.modelValue)
        .map((item, index) => normalizeModelItem(item, index))
        .filter((item) => Number.isFinite(item.id) && item.id > 0)
        .sort((a, b) => a.order - b.order)
})

const selectedProducts = computed({
    get: () => {
        return normalizedModelValue.value
            .map((relation) => {
                const option = productOptions.value.find((product) => {
                    return Number(product.id) === Number(relation.id)
                })

                if (!option) {
                    return null
                }

                return {
                    ...option,
                    relation_type: relation.type,
                    relation_order: relation.order,
                    relation_activity: Boolean(relation.activity),
                }
            })
            .filter(Boolean)
    },

    set: (items) => {
        emit(
            'update:modelValue',
            resourceList(items).map((item, index) => {
                const current = normalizedModelValue.value.find((relation) => {
                    return Number(relation.id) === Number(item.id)
                })

                return {
                    id: Number(item.id),
                    type:
                        item?.relation_type
                        ?? current?.type
                        ?? 'related',

                    order: index,

                    activity:
                        typeof item?.relation_activity !== 'undefined'
                            ? Boolean(item.relation_activity)
                            : current?.activity ?? true,
                }
            })
        )
    },
})

const relationTypeOptions = computed(() => [
    { value: 'related', label: t('related') },
    { value: 'similar', label: t('similar') },
    { value: 'accessory', label: t('accessory') },
    { value: 'analog', label: t('analog') },
])

const dynamicOptionsLimit = computed(() => {
    return productOptions.value.length + 10
})

const updateRelationType = (index, type) => {
    const next = normalizedModelValue.value.map((item) => ({ ...item }))

    if (!next[index]) {
        return
    }

    next[index].type = type
    next[index].order = index

    emit('update:modelValue', next)
}

const itemError = (index, field = '') => {
    const key = field
        ? `related_products.${index}.${field}`
        : `related_products.${index}`

    return props.errors?.[key] || null
}
</script>

<template>
    <div class="my-5 p-3 border border-slate-300 dark:border-slate-500
                bg-white dark:bg-slate-800 rounded-sm">
        <h3 class="mb-3 text-base font-semibold text-slate-700 dark:text-slate-100">
            {{ t('relatedProducts') }}
        </h3>

        <LabelInput for="related-products" :value="t('relatedProducts')" />

        <VueMultiselect
            id="related-products"
            v-model="selectedProducts"
            :options="productOptions"
            label="label"
            track-by="id"
            :multiple="true"
            :close-on-select="false"
            :searchable="true"
            :show-labels="false"
            :options-limit="dynamicOptionsLimit"
            :placeholder="t('selectRelatedProducts')"
            class="w-full min-w-0"
        />

        <InputError class="mt-2" :message="errors?.related_products" />

        <div v-if="selectedProducts.length" class="mt-3 space-y-2">
            <div
                v-for="(product, index) in selectedProducts"
                :key="product.id"
                class="flex flex-col gap-3 rounded-sm bg-slate-100 px-3 py-2
                       dark:bg-slate-900 lg:flex-row lg:items-center lg:justify-between"
            >
                <span
                    class="min-w-0 truncate text-sm font-semibold
                           text-slate-700 dark:text-slate-100"
                    :title="product.label"
                >
                    {{ product.label }}
                </span>

                <select
                    :value="product.relation_type"
                    class="w-full rounded-sm border border-slate-400 bg-white px-2 py-1
                           text-sm text-slate-700 dark:bg-slate-200
                           dark:text-slate-900 lg:w-44"
                    @change="updateRelationType(index, $event.target.value)"
                >
                    <option
                        v-for="option in relationTypeOptions"
                        :key="option.value"
                        :value="option.value"
                    >
                        {{ option.label }}
                    </option>
                </select>

                <InputError :message="itemError(index)" />
                <InputError :message="itemError(index, 'id')" />
                <InputError :message="itemError(index, 'type')" />
            </div>
        </div>
    </div>
</template>
