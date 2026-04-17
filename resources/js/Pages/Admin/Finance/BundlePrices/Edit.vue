<script setup>
/**
 * @version PulsarCMS 1.0
 * @author Александр Косолапов
 * Редактирование прайса бандла (BundlePrice)
 */
import { computed, defineProps, ref, watch } from 'vue'
import { useForm } from '@inertiajs/vue3'
import { useI18n } from 'vue-i18n'
import { useToast } from 'vue-toastification'

import AdminLayout from '@/Layouts/AdminLayout.vue'
import TitlePage from '@/Components/Admin/Headlines/TitlePage.vue'
import DefaultButton from '@/Components/Admin/Buttons/DefaultButton.vue'
import PrimaryButton from '@/Components/Admin/Buttons/PrimaryButton.vue'
import LabelCheckbox from '@/Components/Admin/Checkbox/LabelCheckbox.vue'
import ActivityCheckbox from '@/Components/Admin/Checkbox/ActivityCheckbox.vue'
import InputNumber from '@/Components/Admin/Input/InputNumber.vue'
import InputMoney from '@/Components/Admin/Input/InputMoney.vue'
import LabelInput from '@/Components/Admin/Input/LabelInput.vue'
import InputText from '@/Components/Admin/Input/InputText.vue'
import InputError from '@/Components/Admin/Input/InputError.vue'

import VueMultiselect from 'vue-multiselect'

// --- i18n / toast
const toast = useToast()
const { t } = useI18n()

/**
 * Пропсы из контроллера:
 * return Inertia::render('Admin/BundlePrices/Edit', [
 *   'bundlePrice' => (new BundlePriceResource($bundlePrice))->resolve(),
 *   'bundles' => $bundles,
 *   'currencies' => $currencies,
 * ]);
 */
const props = defineProps({
    bundlePrice: { type: Object, required: true },
    bundles: { type: Array, default: () => [] },
    currencies: { type: Array, default: () => [] },
})

/** форма */
const form = useForm({
    _method: 'PUT',

    bundle_id: props.bundlePrice.bundle_id ?? props.bundlePrice.bundle?.id ?? null,
    currency_id: props.bundlePrice.currency_id ?? props.bundlePrice.currency?.id ?? null,

    price: props.bundlePrice.price ?? '',
    sale_price: props.bundlePrice.sale_price ?? '',
    compare_at_price: props.bundlePrice.compare_at_price ?? '',

    // контроллер отдаёт ISO, input datetime-local принимает YYYY-MM-DDTHH:mm
    starts_at: '',
    ends_at: '',

    activity: Boolean(props.bundlePrice.activity),
    sort: props.bundlePrice.sort ?? 0,

    meta: props.bundlePrice.meta ?? null,
})

/** helpers */
/**
 * Универсальный лимит для любых options:
 * количество элементов + 10 запас.
 */
const dynamicOptionsLimit = (items) => {
    if (!items) return 10
    return items.length + 10
}

/** форматирование дат */
const isoToLocalInput = (iso) => {
    // "2025-01-01T10:30:00.000000Z" -> "2025-01-01T10:30"
    if (!iso) return ''
    const d = new Date(iso)
    if (isNaN(d.getTime())) return ''
    const pad = (n) => String(n).padStart(2, '0')
    return `${d.getFullYear()}-${pad(d.getMonth() + 1)}-${pad(d.getDate())}T${pad(d.getHours())}:${pad(d.getMinutes())}`
}

/** хелпер пунктуации валюты */
const toMoneyString = (val) => {
    if (val === null || typeof val === 'undefined') return null
    if (typeof val === 'string') {
        const v = val.trim()
        if (v === '') return null
        return v.replace(',', '.')
    }
    return String(val)
}

/** хелпер дат */
const toDateTimeStringOrNull = (val) => {
    if (!val) return null
    return String(val)
}

/** init datetime-local */
form.starts_at = isoToLocalInput(props.bundlePrice.starts_at)
form.ends_at = isoToLocalInput(props.bundlePrice.ends_at)

/** options бандлов */
const bundleOptions = computed(() =>
    (props.bundles || []).map(b => {
        const title = b.title || b.slug || `#${b.id}`
        const locale = b.locale || '—'
        const slug = b.slug ? ` — ${b.slug}` : ''
        return {
            id: b.id,
            label: `[ID: ${b.id}] [${locale}] ${title}${slug}`,
        }
    })
)

/** options валют */
const currencyOptions = computed(() =>
    (props.currencies || []).map(cur => {
        const code = cur.code || `#${cur.id}`
        const name = cur.name ? ` — ${cur.name}` : ''
        return {
            id: cur.id,
            label: `${code}${name}`,
        }
    })
)

/** текущие selected */
/** выбранный бандл */
const selectedBundle = ref(
    bundleOptions.value.find(o => o.id === (props.bundlePrice.bundle_id ?? props.bundlePrice.bundle?.id)) ?? null
)

/** выбранная валюта */
const selectedCurrency = ref(
    currencyOptions.value.find(o => o.id === (props.bundlePrice.currency_id ?? props.bundlePrice.currency?.id)) ?? null
)

/** sync to form */
/** Синхронизируем IDs бандлов в форму */
watch(selectedBundle, (val) => {
    form.bundle_id = val?.id ?? null
})

/** Синхронизируем IDs валют в форму */
watch(selectedCurrency, (val) => {
    form.currency_id = val?.id ?? null
})

/** отправка формы submit */
const submitForm = () => {
    form.transform((data) => {
        return {
            ...data,
            bundle_id: data.bundle_id ? Number(data.bundle_id) : null,
            currency_id: data.currency_id ? Number(data.currency_id) : null,

            price: toMoneyString(data.price),
            sale_price: toMoneyString(data.sale_price),
            compare_at_price: toMoneyString(data.compare_at_price),

            starts_at: toDateTimeStringOrNull(data.starts_at),
            ends_at: toDateTimeStringOrNull(data.ends_at),

            activity: data.activity ? 1 : 0,
            sort: Number.isFinite(Number(data.sort)) ? Number(data.sort) : 0,

            meta: data.meta,
        }
    })

    form.post(route('admin.bundlePrices.update', props.bundlePrice.id), {
        preserveScroll: true,
        onSuccess: () => {
            toast.success('Прайс успешно обновлён!')
        },
        onError: (errors) => {
            console.error('❌ Ошибка при обновлении прайса:', errors)
            const firstKey = Object.keys(errors || {})[0]
            const firstError = firstKey ? errors[firstKey] : null
            toast.error(firstError || 'Пожалуйста, проверьте правильность заполнения полей.')
        },
    })
}
</script>

<template>
    <AdminLayout :title="t('editBundlePrice')">
        <template #header>
            <TitlePage>
                {{ t('editBundlePrice') }} - [ID: {{ props.bundlePrice.id }}]
            </TitlePage>
        </template>

        <div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-12xl mx-auto">
            <div
                class="p-4 bg-slate-50 dark:bg-slate-700
                       border border-blue-400 dark:border-blue-200
                       shadow-lg shadow-gray-500 dark:shadow-slate-400
                       bg-opacity-95 dark:bg-opacity-95"
            >
                <div class="sm:flex sm:justify-between sm:items-center mb-2">
                    <!-- Назад -->
                    <DefaultButton :href="route('admin.bundlePrices.index')">
                        <template #icon>
                            <svg class="w-4 h-4 fill-current text-slate-100 shrink-0 mr-2" viewBox="0 0 16 16">
                                <path
                                    d="M4.3 4.5c1.9-1.9 5.1-1.9 7 0 .7.7 1.2 1.7 1.4 2.7l2-.3c-.2-1.5-.9-2.8-1.9-3.8C10.1.4 5.7.4 2.9 3.1L.7.9 0 7.3l6.4-.7-2.1-2.1zM15.6 8.7l-6.4.7 2.1 2.1c-1.9 1.9-5.1 1.9-7 0-.7-.7-1.2-1.7-1.4-2.7l-2 .3c-.2 1.5.9 2.8 1.9 3.8 1.4 1.4 3.1 2 4.9 2 1.8 0 3.6-.7 4.9-2l2.2 2.2 .8-6.4z"
                                />
                            </svg>
                        </template>
                        {{ t('back') }}
                    </DefaultButton>

                    <div class="grid grid-flow-col sm:auto-cols-max justify-start sm:justify-end gap-2">
                        <!-- доп действия (если будут) -->
                    </div>
                </div>

                <form @submit.prevent="submitForm" class="p-3 w-full">
                    <!-- Активность + sort -->
                    <div class="mb-3 flex justify-between flex-col lg:flex-row items-center gap-4">
                        <div class="flex flex-row items-center gap-2">
                            <ActivityCheckbox v-model="form.activity" />
                            <LabelCheckbox
                                for="activity"
                                :text="t('activity')"
                                class="text-sm h-8 flex items-center"
                            />
                            <InputError class="mt-2 lg:mt-0" :message="form.errors.activity" />
                        </div>

                        <div class="flex flex-row items-center gap-2">
                            <div class="h-8 flex items-center">
                                <LabelInput for="sort" :value="t('sort')" class="text-sm" />
                            </div>
                            <InputNumber
                                id="sort"
                                type="number"
                                min="0"
                                v-model="form.sort"
                                autocomplete="sort"
                                class="w-full lg:w-28"
                            />
                            <InputError class="mt-2 lg:mt-0" :message="form.errors.sort" />
                        </div>
                    </div>

                    <!-- Бандл -->
                    <div class="mb-3 flex flex-col items-start">
                        <LabelInput for="bundle_id">
                            <span class="text-red-500 dark:text-red-300 font-semibold">*</span>
                            {{ t('bundle') }}
                        </LabelInput>

                        <VueMultiselect
                            id="bundle_id"
                            v-model="selectedBundle"
                            :options="bundleOptions"
                            :options-limit="dynamicOptionsLimit(bundleOptions)"
                            :multiple="false"
                            :close-on-select="true"
                            :allow-empty="true"
                            :placeholder="t('select')"
                            label="label"
                            track-by="id"
                            class="w-full"
                        />

                        <InputError class="mt-2" :message="form.errors.bundle_id" />
                    </div>

                    <!-- Валюта -->
                    <div class="mb-3 flex flex-col items-start">
                        <LabelInput for="currency_id">
                            <span class="text-red-500 dark:text-red-300 font-semibold">*</span>
                            {{ t('currency') }}
                        </LabelInput>

                        <VueMultiselect
                            id="currency_id"
                            v-model="selectedCurrency"
                            :options="currencyOptions"
                            :options-limit="dynamicOptionsLimit(currencyOptions)"
                            :multiple="false"
                            :close-on-select="true"
                            :allow-empty="true"
                            :placeholder="t('select')"
                            label="label"
                            track-by="id"
                            class="w-full"
                        />

                        <InputError class="mt-2" :message="form.errors.currency_id" />
                    </div>

                    <!-- Цены -->
                    <div class="mb-3 grid grid-cols-1 lg:grid-cols-3 gap-4">
                        <div class="flex flex-col items-start">
                            <LabelInput for="price">
                                <span class="text-red-500 dark:text-red-300 font-semibold">*</span>
                                {{ t('price') }}
                            </LabelInput>

                            <InputMoney
                                id="price"
                                v-model="form.price"
                                :min="0"
                                :step="0.01"
                                :fraction-digits="2"
                                class="w-full"
                            />

                            <InputError class="mt-2" :message="form.errors.price" />
                        </div>

                        <div class="flex flex-col items-start">
                            <LabelInput for="sale_price">{{ t('salePrice') }}</LabelInput>

                            <InputMoney
                                id="sale_price"
                                v-model="form.sale_price"
                                :min="0"
                                :step="0.01"
                                :fraction-digits="2"
                                class="w-full"
                            />

                            <InputError class="mt-2" :message="form.errors.sale_price" />
                        </div>

                        <div class="flex flex-col items-start">
                            <LabelInput for="compare_at_price">
                                {{ t('compareAtPrice') }}
                            </LabelInput>

                            <InputMoney
                                id="compare_at_price"
                                v-model="form.compare_at_price"
                                :min="0"
                                :step="0.01"
                                :fraction-digits="2"
                                class="w-full"
                            />

                            <InputError class="mt-2" :message="form.errors.compare_at_price" />
                        </div>
                    </div>

                    <!-- Период -->
                    <div class="mb-3 grid grid-cols-1 lg:grid-cols-2 gap-4">
                        <div class="flex flex-col items-start">
                            <LabelInput for="starts_at">{{ t('priceStartsAt') }}</LabelInput>
                            <InputText
                                id="starts_at"
                                type="datetime-local"
                                v-model="form.starts_at"
                                class="w-full max-w-xs"
                            />
                            <InputError class="mt-2" :message="form.errors.starts_at" />
                        </div>

                        <div class="flex flex-col items-end">
                            <LabelInput for="ends_at">{{ t('priceEndsAt') }}</LabelInput>
                            <InputText
                                id="ends_at"
                                type="datetime-local"
                                v-model="form.ends_at"
                                class="w-full max-w-xs"
                            />
                            <InputError class="mt-2" :message="form.errors.ends_at" />
                        </div>
                    </div>

                    <!-- Кнопки -->
                    <div class="flex items-center justify-center mt-4">
                        <DefaultButton :href="route('admin.bundlePrices.index')" class="mb-3">
                            <template #icon>
                                <svg class="w-4 h-4 fill-current text-slate-100 shrink-0 mr-2" viewBox="0 0 16 16">
                                    <path
                                        d="M4.3 4.5c1.9-1.9 5.1-1.9 7 0 .7.7 1.2 1.7 1.4 2.7l2-.3c-.2-1.5-.9-2.8-1.9-3.8C10.1.4 5.7.4 2.9 3.1L.7.9 0 7.3l6.4-.7-2.1-2.1zM15.6 8.7l-6.4.7 2.1 2.1c-1.9 1.9-5.1-1.9-7 0-.7-.7-1.2-1.7-1.4-2.7l-2 .3c-.2 1.5.9 2.8 1.9 3.8 1.4 1.4 3.1 2 4.9 2 1.8 0 3.6-.7 4.9-2l2.2 2.2 .8-6.4z"
                                    />
                                </svg>
                            </template>
                            {{ t('back') }}
                        </DefaultButton>

                        <PrimaryButton
                            class="ms-4 mb-0"
                            :disabled="form.processing"
                            :class="{ 'opacity-25': form.processing }"
                        >
                            <template #icon>
                                <svg class="w-4 h-4 fill-current text-slate-100" viewBox="0 0 16 16">
                                    <path
                                        d="M14.3 2.3L5 11.6 1.7 8.3c-.4-.4-1-.4-1.4 0-.4.4-.4 1 0 1.4l4 4c.2.2.4.3.7.3.3 0 .5-.1.7-.3l10-10c.4-.4.4-1 0-1.4-.4-.4-1-.4-1.4 0z"
                                    />
                                </svg>
                            </template>
                            {{ t('save') }}
                        </PrimaryButton>
                    </div>
                </form>
            </div>
        </div>
    </AdminLayout>
</template>

<style src="/resources/css/vue-multiselect.min.css"></style>
