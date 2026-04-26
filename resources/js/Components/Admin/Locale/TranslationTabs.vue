<script setup>
import { computed, ref } from 'vue'
import { useI18n } from 'vue-i18n'

const { t } = useI18n()

const props = defineProps({
    modelValue: {
        type: String,
        required: true,
    },
    translations: {
        type: Object,
        required: true,
    },
    availableLocales: {
        type: Array,
        default: () => ['ru', 'en', 'kk'],
    },
    makeTranslation: {
        type: Function,
        required: true,
    },
    minTranslations: {
        type: Number,
        default: 1,
    },
})

const emit = defineEmits([
    'update:modelValue',
    'update:translations',
    'removed',
    'added',
])

const localeToAdd = ref('')

const translationLocales = computed(() => Object.keys(props.translations || {}))

const availableLocalesForAdd = computed(() => {
    return props.availableLocales.filter(locale => !translationLocales.value.includes(locale))
})

const setActiveLocale = (locale) => {
    emit('update:modelValue', locale)
}

const addLocaleTab = () => {
    const locale = localeToAdd.value

    if (!locale || props.translations[locale]) return

    const updated = {
        ...props.translations,
        [locale]: props.makeTranslation(),
    }

    emit('update:translations', updated)
    emit('update:modelValue', locale)
    emit('added', locale)

    localeToAdd.value = ''
}

const removeLocaleTab = (locale) => {
    if (translationLocales.value.length <= props.minTranslations) {
        return
    }

    const updated = { ...props.translations }
    delete updated[locale]

    emit('update:translations', updated)
    emit('removed', locale)

    if (props.modelValue === locale) {
        emit('update:modelValue', Object.keys(updated)[0])
    }
}
</script>

<template>
    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-3 mb-4">
        <div class="flex flex-wrap gap-2">
            <button
                v-for="locale in translationLocales"
                :key="locale"
                type="button"
                class="px-2 py-0.5 text-sm rounded-sm border"
                :class="modelValue === locale
                    ? 'bg-blue-500 text-white border-blue-500'
                    : 'bg-slate-100 dark:bg-slate-700 ' +
                     'text-slate-700 dark:text-slate-100 ' +
                      'border-slate-300 dark:border-slate-500'"
                @click="setActiveLocale(locale)"
            >
                {{ locale.toUpperCase() }}
            </button>
        </div>

        <div class="flex items-center gap-2">
            <select
                v-model="localeToAdd"
                class="w-auto pl-3 pr-7 py-0.5 form-select rounded-sm shadow-sm
                       bg-white dark:bg-gray-200 text-gray-600 dark:text-gray-900
                       border border-slate-400 dark:border-slate-600"
            >
                <option value="">{{t('addLocale')}}</option>

                <option
                    v-for="locale in availableLocalesForAdd"
                    :key="locale"
                    :value="locale"
                >
                    {{ locale.toUpperCase() }}
                </option>
            </select>

            <button
                type="button"
                class="px-3 py-1 text-sm rounded-sm bg-blue-500 text-white
                       hover:bg-blue-600 disabled:opacity-50"
                :disabled="!localeToAdd"
                @click="addLocaleTab"
            >
                +
            </button>

            <button
                type="button"
                class="px-3 py-1 text-sm rounded-sm bg-yellow-400 text-white
                       hover:bg-yellow-500 disabled:opacity-50"
                :disabled="translationLocales.length <= minTranslations"
                @click="removeLocaleTab(modelValue)"
            >
                −
            </button>
        </div>
    </div>
</template>
