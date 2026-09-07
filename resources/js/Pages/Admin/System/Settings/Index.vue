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
 * Основной раздел настройки.
 */
const resolveSection = (setting) => {
    const option = String(setting?.option || '')

    if (setting?.category === 'admin') return 'Admin'
    if (option.startsWith('publicBlog')) return 'Blog'
    if (option.startsWith('publicSchool')) return 'School'
    if (option.startsWith('publicMarket')) return 'Market'

    return 'Общие'
}

/**
 * Подгруппа настройки.
 */
const resolveSubgroup = (setting) => {
    const option = String(setting?.option || '')

    // Blog
    if (option.startsWith('publicBlogRubrics')) return 'Rubrics'
    if (option.startsWith('publicBlogArticles')) return 'Articles'
    if (option.startsWith('publicBlogTags')) return 'Tags'
    if (option.startsWith('publicBlogBanners')) return 'Banners'
    if (option.startsWith('publicBlogVideos')) return 'Videos'

    // School
    if (option.startsWith('publicSchoolHashtags')) return 'Hashtags'
    if (option.startsWith('publicSchoolInstructors')) return 'Instructors'
    if (option.startsWith('publicSchoolTracks')) return 'Tracks'
    if (option.startsWith('publicSchoolCourseSchedules')) return 'Course Schedules'
    if (option.startsWith('publicSchoolCourses')) return 'Courses'
    if (option.startsWith('publicSchoolModules')) return 'Modules'
    if (option.startsWith('publicSchoolLessons')) return 'Lessons'
    if (option.startsWith('publicSchoolAssignments')) return 'Assignments'
    if (option.startsWith('publicSchoolQuizAttemptItems')) return 'Quiz Attempt Items'
    if (option.startsWith('publicSchoolQuizAttempts')) return 'Quiz Attempts'
    if (option.startsWith('publicSchoolQuizzes')) return 'Quizzes'
    if (option.startsWith('publicSchoolBundles')) return 'Bundles'

    // Market
    if (option.startsWith('publicMarketProductVariants')) return 'Product Variants'
    if (option.startsWith('publicMarketProductBundles')) return 'Product Bundles'
    if (option.startsWith('publicMarketProducts')) return 'Products'
    if (option.startsWith('publicMarketCompanies')) return 'Companies'
    if (option.startsWith('publicMarketShops')) return 'Shops'
    if (option.startsWith('publicMarketCategories')) return 'Categories'
    if (option.startsWith('publicMarketBrands')) return 'Brands'
    if (option.startsWith('publicMarketTags')) return 'Tags'

    return 'Основные'
}

/**
 * Группировка и сортировка по sort.
 */
const groupedSettings = computed(() => {
    const groups = {}

    filteredSettings.value.forEach((setting) => {
        const section = resolveSection(setting)
        const subgroup = resolveSubgroup(setting)

        if (!groups[section]) groups[section] = {}
        if (!groups[section][subgroup]) groups[section][subgroup] = []

        groups[section][subgroup].push(setting)
    })

    Object.values(groups).forEach((section) => {
        Object.values(section).forEach((settings) => {
            settings.sort((a, b) => {
                const sortDifference = (Number(a.sort) || 0) - (Number(b.sort) || 0)

                return sortDifference || b.id - a.id
            })
        })
    })

    return groups
})

/**
 * Порядок основных разделов.
 */
const sectionOrder = [
    'Общие',
    'Blog',
    'School',
    'Market',
    'Admin',
]

/**
 * Порядок подгрупп.
 */
const subgroupOrder = {
    'Общие': [
        'Основные',
    ],
    Blog: [
        'Rubrics',
        'Articles',
        'Tags',
        'Banners',
        'Videos',
        'Основные',
    ],
    School: [
        'Hashtags',
        'Instructors',
        'Tracks',
        'Courses',
        'Modules',
        'Lessons',
        'Assignments',
        'Course Schedules',
        'Quizzes',
        'Quiz Attempts',
        'Quiz Attempt Items',
        'Bundles',
        'Основные',
    ],
    Market: [
        'Companies',
        'Shops',
        'Categories',
        'Brands',
        'Tags',
        'Products',
        'Product Variants',
        'Product Bundles',
        'Основные',
    ],
    Admin: [
        'Основные',
    ],
}

/**
 * Только существующие разделы.
 */
const sections = computed(() => {
    return sectionOrder.filter((section) => {
        return groupedSettings.value[section]
    })
})

/**
 * Только существующие подгруппы раздела.
 */
const getSubgroups = (section) => {
    const available = groupedSettings.value[section] || {}

    return (subgroupOrder[section] || ['Основные']).filter((subgroup) => {
        return available[subgroup]?.length
    })
}

/**
 * Количество настроек раздела.
 */
const getSectionCount = (section) => {
    return Object.values(groupedSettings.value[section] || {})
        .reduce((total, settings) => total + settings.length, 0)
}

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
                <div class="px-3 py-3 mb-4 border border-gray-400 dark:border-gray-500">
                    <div class="relative w-full">
                        <input
                            v-model="search"
                            type="text"
                            :placeholder="t('searchByParameter')"
                            class="w-full px-2 py-1 pr-8
                                   border border-slate-300 rounded-xs
                                   bg-white dark:bg-gray-800
                                   text-sm font-semibold
                                   text-gray-700 dark:text-gray-300"
                        >
                        <svg
                            class="absolute right-2 top-2 w-4 h-4
                                   text-gray-400 dark:text-gray-500"
                            fill="currentColor"
                            viewBox="0 0 20 20"
                        >
                            <path
                                fill-rule="evenodd"
                                d="M12.9 14.32a8 8 0 111.42-1.42l4.58 4.58a1 1 0 01-1.42 1.42l-4.58-4.58zm-4.9 0a6 6 0 100-12 6 6 0 000 12z"
                                clip-rule="evenodd"
                            />
                        </svg>
                    </div>
                </div>

                <!-- Основные разделы -->
                <div v-if="sections.length" class="space-y-10">
                    <section
                        v-for="section in sections"
                        :key="section"
                    >
                        <!-- Заголовок раздела -->
                        <div
                            class="mb-4 pb-2 border-b-2
                                   border-cyan-500 dark:border-cyan-300"
                        >
                            <div
                                class="flex flex-col gap-1
                                       sm:flex-row sm:items-end sm:justify-between"
                            >
                                <h2
                                    class="text-lg sm:text-xl font-bold
                                           text-cyan-700 dark:text-cyan-300"
                                >
                                    {{ section }}
                                </h2>

                                <div
                                    class="text-xs font-semibold
                                           text-cyan-700 dark:text-cyan-300"
                                >
                                    {{ t('settings') }}: {{ getSectionCount(section) }}
                                </div>
                            </div>
                        </div>

                        <!-- Подгруппы -->
                        <div class="space-y-6">
                            <div
                                v-for="subgroup in getSubgroups(section)"
                                :key="subgroup"
                                class="px-2 py-1 shadow-md shadow-slate-400 dark:shadow-slate-900
                                       rounded border border-gray-400 dark:border-gray-500"
                            >
                                <!-- Заголовок подгруппы -->
                                <div
                                    v-if="subgroup !== 'Основные' || getSubgroups(section).length > 1"
                                    class="mb-2 pb-1 border-b
                                           border-slate-400 dark:border-slate-500"
                                >
                                    <div
                                        class="flex flex-col gap-1
                                               sm:flex-row items-center sm:justify-between"
                                    >
                                        <h3
                                            class="text-sm sm:text-base font-semibold
                                                   text-fuchsia-700 dark:text-fuchsia-300"
                                        >
                                            {{ subgroup }}
                                        </h3>

                                        <div
                                            class="font-semibold text-xs
                                                   text-slate-500 dark:text-slate-400"
                                        >
                                            {{ t('settings') }}:
                                            {{ groupedSettings[section][subgroup].length }}
                                        </div>
                                    </div>
                                </div>

                                <!-- Настройки -->
                                <SettingField
                                    v-for="setting in groupedSettings[section][subgroup]"
                                    :key="setting.id"
                                    :setting="setting"
                                    @show-description="openModal"
                                />
                            </div>
                        </div>
                    </section>
                </div>

                <!-- Ничего не найдено -->
                <div
                    v-else-if="!error"
                    class="py-8 px-3 text-center text-sm sm:text-base
                           text-slate-500 dark:text-slate-300"
                >
                    {{ t('noData') }}
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
