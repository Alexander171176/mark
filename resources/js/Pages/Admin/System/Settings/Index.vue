```vue
<script setup>
import { computed, defineProps, ref } from 'vue'
import { useI18n } from 'vue-i18n'

import AdminLayout from '@/Layouts/AdminLayout.vue'
import TitlePage from '@/Components/Admin/UI/Headlines/TitlePage.vue'
import SettingField from '@/Components/Admin/System/Setting/SettingField.vue'
import Modal from '@/Components/Admin/System/Setting/Modal/Modal.vue'

const { t } = useI18n()

const props = defineProps({
    settings: {
        type: [Array, Object],
        default: () => [],
    },
    error: {
        type: String,
        default: '',
    },
})

const search = ref('')
const showModal = ref(false)
const modalDescription = ref('')

/**
 * Нормализация ResourceCollection.
 */
const settingsList = computed(() => {
    if (Array.isArray(props.settings)) return props.settings
    if (Array.isArray(props.settings?.data)) return props.settings.data

    return []
})

/**
 * Фильтрация настроек по поиску.
 */
const filteredSettings = computed(() => {
    const term = search.value.trim().toLowerCase()

    if (!term) return settingsList.value

    return settingsList.value.filter((setting) => {
        const haystack = [
            setting.option,
            setting.description,
            setting.category,
            setting.type,
            setting.value,
        ]
            .filter(Boolean)
            .join(' ')
            .toLowerCase()

        return haystack.includes(term)
    })
})

/**
 * Определение визуальной группы.
 */
const resolveGroup = (setting) => {
    const option = String(setting?.option || '')

    if (setting?.category === 'admin') return 'Admin'
    if (option.startsWith('publicBlog')) return 'Blog'
    if (option.startsWith('publicSchool')) return 'School'
    if (option.startsWith('publicMarket')) return 'Market'

    return 'Общие'
}

/**
 * Группировка и сортировка настроек по sort.
 */
const groupedSettings = computed(() => {
    const groups = filteredSettings.value.reduce((result, setting) => {
        const group = resolveGroup(setting)

        if (!result[group]) result[group] = []

        result[group].push(setting)

        return result
    }, {})

    Object.values(groups).forEach((settings) => {
        settings.sort((a, b) => {
            const sortDifference = (Number(a.sort) || 0) - (Number(b.sort) || 0)

            return sortDifference || b.id - a.id
        })
    })

    return groups
})

/**
 * Порядок отображения групп.
 */
const groupOrder = [
    'Общие',
    'Blog',
    'School',
    'Market',
    'Admin',
]

const groups = computed(() => {
    return groupOrder.filter((group) => groupedSettings.value[group]?.length)
})

/**
 * Модальное окно описания.
 */
const openModal = (description = '') => {
    modalDescription.value = description || ''
    showModal.value = true
}

const closeModal = () => {
    showModal.value = false
    modalDescription.value = ''
}
</script>

<template>
    <AdminLayout :title="t('siteSettingsTitle')">
        <template #header>
            <TitlePage>
                {{ t('siteSettingsTitle') }}
            </TitlePage>
        </template>

        <div class="w-full max-w-12xl mx-auto px-2 sm:px-4 lg:px-6 py-3">
            <div
                class="p-3 sm:p-4 lg:p-6 bg-slate-50 dark:bg-slate-700
                       border border-blue-400 dark:border-blue-200
                       overflow-hidden shadow-md shadow-gray-500 dark:shadow-slate-400
                       bg-opacity-95 dark:bg-opacity-95"
            >
                <!-- Ошибка -->
                <div
                    v-if="error"
                    class="mb-4 p-3 text-sm border border-red-300
                           bg-red-50 dark:bg-red-900/20
                           text-red-700 dark:text-red-300"
                >
                    {{ error }}
                </div>

                <!-- Поиск -->
                <div class="px-3 py-3 mb-2 border border-gray-400 dark:border-gray-500">
                    <div class="relative w-full">
                        <input
                            v-model="search"
                            type="text"
                            :placeholder="t('searchByParameter')"
                            class="w-full px-2 py-1
                                    border border-slate-300
                                    rounded-xs
                                    bg-white dark:bg-gray-800
                                    text-sm font-semibold
                                    text-gray-700 dark:text-gray-300"
                        >
                        <svg class="absolute right-2 top-2 w-4 h-4
                                    text-gray-400 dark:text-gray-500"
                             fill="currentColor"
                             viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                  d="M12.9 14.32a8 8 0 111.42-1.42l4.58 4.58a1 1 0 01-1.42 1.42l-4.58-4.58zm-4.9 0a6 6 0 100-12 6 6 0 000 12z"
                                  clip-rule="evenodd" />
                        </svg>
                    </div>
                </div>

                <!-- Группы -->
                <div v-if="groups.length" class="space-y-8">
                    <section
                        v-for="group in groups"
                        :key="group"
                    >
                        <div
                            class="mb-3 pb-2 border-b border-slate-500 dark:border-slate-300"
                        >
                            <div
                                class="flex flex-col gap-1
                                       sm:flex-row sm:items-end sm:justify-between"
                            >
                                <h2
                                    class="text-base sm:text-lg font-semibold
                                           text-fuchsia-700 dark:text-fuchsia-300"
                                >
                                    {{ group }}
                                </h2>

                                <div
                                    class="font-semibold text-xs
                                           text-fuchsia-700 dark:text-fuchsia-300"
                                >
                                    {{ t('settings') }}: {{ groupedSettings[group].length }}
                                </div>
                            </div>
                        </div>

                        <SettingField
                            v-for="setting in groupedSettings[group]"
                            :key="setting.id"
                            :setting="setting"
                            @show-description="openModal"
                        />
                    </section>
                </div>

                <!-- Ничего не найдено -->
                <div
                    v-else-if="!error"
                    class="py-8 px-3 text-center text-sm sm:text-base
                           text-slate-500 dark:text-slate-300"
                >
                    Настройки не найдены.
                </div>
            </div>
        </div>

        <Modal
            v-if="showModal"
            :showModal="showModal"
            :modalDescription="modalDescription"
            @toggleModal="closeModal"
        />
    </AdminLayout>
</template>
```
