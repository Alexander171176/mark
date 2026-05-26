<script setup>
import { ref, watch, computed } from 'vue'
import { useI18n } from 'vue-i18n'

const { t } = useI18n()

const props = defineProps({
    /**
     * ✅ Режим 1 (таблица/карточки): передаём currency целиком
     * ожидание полей: id, is_default, activity, rate_vs_default, rate_at, rate_provider
     */
    currency: { type: Object, default: null },

    /**
     * ✅ Режим 2 (карточки/любой список): можно работать без currency
     * тогда обязательно id
     */
    id: { type: [Number, String, null], default: null },

    /**
     * Текущее значение курса (для режима без currency или для явного переопределения)
     * Если передан currency, но modelValue тоже передан — приоритет у modelValue.
     */
    modelValue: { type: [String, Number, null], default: null },

    /**
     * Активность/дефолт (для режима без currency)
     * Если передан currency — берём оттуда.
     */
    isDefault: { type: Boolean, default: false },
    activity: { type: Boolean, default: true },

    placeholder: { type: String, default: '' },
    step: { type: String, default: '0.00000001' },
    precision: { type: Number, default: 8 },
    min: { type: Number, default: 0.00000001 }
})

const emit = defineEmits(['save'])

/** источники (currency имеет приоритет, но modelValue может переопределить rate_vs_default) */
const currencyId = computed(() => props.currency?.id ?? props.id ?? null)
const isBase = computed(() => !!(props.currency?.is_default ?? props.isDefault))
const isActive = computed(() => !!(props.currency?.activity ?? props.activity))
const rateValue = computed(() => {
    // приоритет: modelValue -> currency.rate_vs_default
    const v = props.modelValue ?? props.currency?.rate_vs_default
    return v === undefined ? null : v
})
const rateAt = computed(() => props.currency?.rate_at ?? null)
const rateAtLabel = computed(() => {
    if (!rateAt.value) return ''
    const d = new Date(rateAt.value)
    if (isNaN(d)) return String(rateAt.value) // если вдруг уже строка
    return d.toLocaleString('ru-RU', {
        year: 'numeric',
        month: '2-digit',
        day: '2-digit',
        hour: '2-digit',
        minute: '2-digit',
    })
})
const provider = computed(() => props.currency?.rate_provider ?? null)

const local = ref('')

const fmt = (val) => {
    if (val === null || val === undefined || val === '') return ''
    const num = Number(String(val).replace(',', '.'))
    if (!Number.isFinite(num)) return ''
    return num.toFixed(props.precision)
}

const initLocal = () => {
    if (isBase.value) {
        local.value = fmt(1)
        return
    }
    local.value = fmt(rateValue.value)
}
initLocal()

/**
 * ✅ Синхронизация:
 * - если обновился currency / modelValue (Inertia пришла) — обновляем local
 */
watch(
    () => props.currency,
    () => initLocal(),
    { deep: true }
)

watch(
    () => props.modelValue,
    () => initLocal()
)

/** ввод: допускаем пробелы и запятую */
const normalized = computed(() => {
    const raw = String(local.value ?? '').trim()
    if (!raw) return null
    return raw.replace(/\s+/g, '').replace(',', '.')
})

const isDisabledInput = computed(() => isBase.value || !isActive.value)

const isValid = computed(() => {
    if (isBase.value) return false
    if (isDisabledInput.value) return false
    if (normalized.value === null) return false
    const v = Number(normalized.value)
    return Number.isFinite(v) && v > 0
})

const busy = ref(false)

const save = () => {
    if (busy.value) return
    if (!isValid.value) return

    const id = currencyId.value
    if (id === null || id === undefined) return

    const num = Number(normalized.value)
    if (!Number.isFinite(num) || num <= 0) return

    busy.value = true
    try {
        emit('save', { id, value: num })
    } finally {
        // сбросим сразу, реальную “занятость” контролирует родитель (Inertia)
        busy.value = false
    }
}
</script>

<template>
    <div class="w-fit flex flex-col items-center justify-center gap-1">
        <div v-if="provider"
             class="px-2 py-0 rounded-sm
                    text-xs opacity-70
                    bg-slate-100 dark:bg-slate-900/60
                    border border-gray-400
                    text-slate-700 dark:text-slate-200">
            {{ t('provider') }}: {{ provider }}
        </div>
        <div class="flex items-center gap-2">
            <input
                type="number"
                v-model="local"
                :step="step"
                :min="min"
                :disabled="isDisabledInput || busy"
                :placeholder="placeholder || t('rateForOneBase')"
                :title="rateAtLabel"
                class="w-28 md:w-32 lg:w-36 px-2 py-0 rounded
                   border border-slate-300 dark:border-slate-600
                   bg-white dark:bg-slate-800 text-slate-800 dark:text-slate-100
                   focus:outline-none focus:ring-2 focus:ring-indigo-400
                   disabled:opacity-60 disabled:cursor-not-allowed"
                @keydown.enter.prevent="save"
            />
            <button
                type="button"
                class="inline-flex items-center gap-1 p-1 rounded
                       text-white bg-emerald-500 hover:bg-emerald-700
                       dark:bg-emerald-800 dark:hover:bg-emerald-600
                       disabled:opacity-50 disabled:cursor-not-allowed"
                :disabled="!isValid || isBase || busy"
                @click="save"
                :title="isBase ? t('baseUnit') : t('saveRate')"
            >
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor">
                    <path
                        d="M22.707,6.707,17.293,1.293A1,1,0,0,0,16.586,1H4A3,3,0,0,0,1,4V20a3,3,0,0,0,3,3H20a3,3,0,0,0,3-3V7.414A1,1,0,0,0,22.707,6.707ZM14.5,4h1a.5.5,0,0,1,.5.5v4a.5.5,0,0,1-.5.5h-1a.5.5,0,0,1-.5-.5v-4A.5.5,0,0,1,14.5,4ZM19,12.5v6a.5.5,0,0,1-.5.5H5.5a.5.5,0,0,1-.5-.5v-6a.5.5,0,0,1,.5-.5h13A.5.5,0,0,1,19,12.5Z"></path>
                </svg>
            </button>
        </div>
        <div
            v-if="rateAtLabel"
            class="font-semibold text-[10px] text-sky-700 dark:text-sky-300"
        >
            {{ t('updatedAt') }}: {{ rateAtLabel }}
        </div>

        <div
            v-else
            class="font-semibold text-[10px] text-emerald-700 dark:text-emerald-300"
        >
            {{ t('baseUnit') }}
        </div>

    </div>
</template>
