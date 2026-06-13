<script setup>
/**
 * @version PulsarCMS 1.0
 * @author Александр Косолапов <kosolapov1976@gmail.com>
 *
 * Список хештегов школы
 * - режимы обработки: frontend | server | auto
 * - локальный поиск/сортировка/пагинация
 * - серверный поиск/сортировка/пагинация
 */

import { computed, defineProps, ref, watch } from 'vue'
import { useI18n } from 'vue-i18n'
import { useToast } from 'vue-toastification'
import { router } from '@inertiajs/vue3'

import AdminLayout from '@/Layouts/AdminLayout.vue'
import TitlePage from '@/Components/Admin/UI/Headlines/TitlePage.vue'
import SearchInput from '@/Components/Admin/UI/Search/SearchInput.vue'
import ServerSearchInput from '@/Components/Admin/UI/Search/ServerSearchInput.vue'
import DefaultButton from '@/Components/Admin/UI/Buttons/DefaultButton.vue'
import ToggleViewButton from '@/Components/Admin/UI/Buttons/ToggleViewButton.vue'
import CountTable from '@/Components/Admin/UI/Count/CountTable.vue'
import ItemsPerPageSelect from '@/Components/Admin/UI/Select/ItemsPerPageSelect.vue'
import ServerItemsPerPageSelect from '@/Components/Admin/UI/Select/ServerItemsPerPageSelect.vue'
import DangerModal from '@/Components/Admin/UI/Modal/DangerModal.vue'
import Pagination from '@/Components/Admin/UI/Pagination/Pagination.vue'
import AdminServerPagination from '@/Components/Admin/UI/Pagination/AdminServerPagination.vue'
import ProcessingModeSwitcher from '@/Components/Admin/UI/Processing/ProcessingModeSwitcher.vue'

import BulkActionSelect from '@/Components/Admin/School/SchoolHashtag/Select/BulkActionSelect.vue'
import SortSelect from '@/Components/Admin/School/SchoolHashtag/Sort/SortSelect.vue'
import HashtagTable from '@/Components/Admin/School/SchoolHashtag/Table/HashtagTable.vue'
import HashtagCardGrid from '@/Components/Admin/School/SchoolHashtag/View/HashtagCardGrid.vue'

/* ==========================================================
 * БАЗОВЫЕ СЕРВИСЫ И PROPS
 * ========================================================== */

/** Локализация интерфейса */
const { t } = useI18n()

/** Уведомления */
const toast = useToast()

/** Данные страницы из Inertia */
const props = defineProps({
    currentLocale: { type: String, default: '' },
    availableLocales: { type: Array, default: () => [] },

    adminSchoolHashtagsProcessingMode: { type: String, default: 'frontend' },
    useServerProcessing: { type: Boolean, default: false },

    hashtags: { type: [Array, Object], default: () => [] },
    hashtagsCount: { type: Number, default: 0 },

    adminSchoolHashtagsPerPage: { type: Number, default: 6 },
    adminSchoolHashtagsDefaultSort: { type: String, default: 'idDesc' },

    sortParam: { type: String, default: '' },
    search: { type: String, default: '' },

    errors: { type: Object, default: () => ({}) },
})

/* ==========================================================
 * РЕЖИМ ОТОБРАЖЕНИЯ
 * ========================================================== */

/** Текущий режим отображения (таблица / карточки) */
const viewMode = ref(localStorage.getItem('admin_view_mode_hashtags') || 'table')

/** Сохраняем выбранный вид локально */
watch(viewMode, (val) => {
    localStorage.setItem('admin_view_mode_hashtags', val)
})

/* ==========================================================
 * ИСТОЧНИК ДАННЫХ
 * ========================================================== */

/**
 * Унифицированный список хештегов:
 * frontend → обычный массив
 * server → hashtags.data
 */
const hashtagsList = computed(() => {
    if (Array.isArray(props.hashtags)) {
        return props.hashtags
    }

    if (Array.isArray(props.hashtags?.data)) {
        return props.hashtags.data
    }

    return []
})

/* ==========================================================
 * ЛОКАЛЬНОЕ ХРАНИЛИЩЕ ДАННЫХ
 * ========================================================== */

/**
 * Локальная копия списка.
 * Используется для:
 * - локального поиска
 * - локальной сортировки
 * - моментального обновления UI
 */
const localHashtags = ref([])

watch(
    hashtagsList,
    (newVal) => {
        localHashtags.value = JSON.parse(JSON.stringify(newVal || []))
    },
    { immediate: true, deep: true }
)

/* ==========================================================
 * НАСТРОЙКИ ПАГИНАЦИИ И СОРТИРОВКИ
 * ========================================================== */

/** Количество элементов на странице */
const itemsPerPage = ref(props.adminSchoolHashtagsPerPage || 6)

/** Сохраняем настройку количества элементов */
watch(itemsPerPage, (newVal) => {
    router.put(
        route('admin.settings.updateAdminCountHashtags'),
        { value: newVal },
        {
            preserveScroll: true,
            preserveState: true,
            onSuccess: () => toast.info(`Показ ${newVal} элементов на странице.`),
            onError: (errors) => toast.error(errors.value || 'Ошибка обновления кол-ва элементов.'),
        }
    )
})

/** Текущий параметр сортировки */
const sortParam = ref(props.sortParam || props.adminSchoolHashtagsDefaultSort || 'idDesc')

/** Сохраняем сортировку и при server-режиме перезагружаем список */
watch(sortParam, (newVal) => {
    currentPage.value = 1

    router.put(
        route('admin.settings.updateAdminSortHashtags'),
        { value: newVal },
        {
            preserveScroll: true,
            preserveState: true,

            onSuccess: () => {
                if (props.useServerProcessing) {
                    router.get(
                        window.location.pathname,
                        {
                            ...Object.fromEntries(new URLSearchParams(window.location.search)),
                            sort: newVal || undefined,
                            page: undefined,
                        },
                        {
                            preserveScroll: true,
                            preserveState: false,
                            replace: true,
                        }
                    )
                }

                toast.info('Сортировка успешно изменена')
            },

            onError: (errors) => {
                toast.error(errors.value || 'Ошибка обновления сортировки.')
            },
        }
    )
})

/* ==========================================================
 * ПОИСК И ПАГИНАЦИЯ
 * ========================================================== */

/** Поисковый запрос */
const searchQuery = ref(props.search || '')

/** Текущая страница frontend-пагинации */
const currentPage = ref(1)

/* ==========================================================
 * ВСПОМОГАТЕЛЬНЫЕ ФУНКЦИИ
 * ========================================================== */

/** Нормализация строки */
const normalize = (value) => (value ?? '').toString().trim().toLowerCase()

/** Безопасное преобразование в число */
const safeNumber = (value) => {
    const number = Number(value)
    return Number.isFinite(number) ? number : 0
}

/** Безопасное преобразование даты */
const safeDate = (value) => {
    const time = new Date(value || 0).getTime()
    return Number.isFinite(time) ? time : 0
}

/* ==========================================================
 * ИЗВЛЕЧЕНИЕ ДАННЫХ ИЗ РЕСУРСОВ
 * ========================================================== */

/** Получение названия хештега */
const getHashtagName = (hashtag) => {
    return hashtag?.name
        || hashtag?.translation?.name
        || hashtag?.translations?.[0]?.name
        || `ID: ${hashtag?.id}`
}

/** Получение краткого описания хештега */
const getHashtagShort = (hashtag) => {
    return hashtag?.short
        || hashtag?.translation?.short
        || hashtag?.translations?.[0]?.short
        || ''
}

/** Получение описания хештега */
const getHashtagDescription = (hashtag) => {
    return hashtag?.description
        || hashtag?.translation?.description
        || hashtag?.translations?.[0]?.description
        || ''
}

/** Получение slug хештега */
const getHashtagSlug = (hashtag) => {
    return hashtag?.slug
        || hashtag?.translation?.slug
        || hashtag?.translations?.[0]?.slug
        || ''
}

/* ==========================================================
 * СОРТИРОВКА FRONTEND
 * ========================================================== */

/** Сортировка чисел ↑ */
const byNumberAsc = (field) => (a, b) =>
    safeNumber(a?.[field]) - safeNumber(b?.[field])
    || safeNumber(a?.id) - safeNumber(b?.id)

/** Сортировка чисел ↓ */
const byNumberDesc = (field) => (a, b) =>
    safeNumber(b?.[field]) - safeNumber(a?.[field])
    || safeNumber(b?.id) - safeNumber(a?.id)

/** Сортировка строк ↑ */
const byStringAsc = (field) => (a, b) =>
    normalize(a?.[field]).localeCompare(normalize(b?.[field]), props.currentLocale)
    || safeNumber(a?.id) - safeNumber(b?.id)

/** Сортировка строк ↓ */
const byStringDesc = (field) => (a, b) =>
    normalize(b?.[field]).localeCompare(normalize(a?.[field]), props.currentLocale)
    || safeNumber(b?.id) - safeNumber(a?.id)

/**
 * Главный обработчик сортировки.
 * Должен совпадать со scopeSortByParam() модели и SortSelect.vue.
 */
const sortHashtags = (items) => {
    const list = (items || []).slice()

    if (sortParam.value === 'activity') return list.filter(item => !!item.activity)
    if (sortParam.value === 'inactive') return list.filter(item => !item.activity)

    const sortMap = {
        idAsc: byNumberAsc('id'),
        idDesc: byNumberDesc('id'),

        sortAsc: byNumberAsc('sort'),
        sortDesc: byNumberDesc('sort'),

        nameAsc: (a, b) =>
            normalize(getHashtagName(a)).localeCompare(normalize(getHashtagName(b)), props.currentLocale)
            || safeNumber(a?.id) - safeNumber(b?.id),

        nameDesc: (a, b) =>
            normalize(getHashtagName(b)).localeCompare(normalize(getHashtagName(a)), props.currentLocale)
            || safeNumber(b?.id) - safeNumber(a?.id),

        slugAsc: (a, b) =>
            normalize(getHashtagSlug(a)).localeCompare(normalize(getHashtagSlug(b)), props.currentLocale)
            || safeNumber(a?.id) - safeNumber(b?.id),

        slugDesc: (a, b) =>
            normalize(getHashtagSlug(b)).localeCompare(normalize(getHashtagSlug(a)), props.currentLocale)
            || safeNumber(b?.id) - safeNumber(a?.id),

        colorAsc: byStringAsc('color'),
        colorDesc: byStringDesc('color'),

        viewsAsc: byNumberAsc('views'),
        viewsDesc: byNumberDesc('views'),

        likesAsc: byNumberAsc('likes'),
        likesDesc: byNumberDesc('likes'),

        coursesAsc: byNumberAsc('courses_count'),
        coursesDesc: byNumberDesc('courses_count'),

        modulesAsc: byNumberAsc('modules_count'),
        modulesDesc: byNumberDesc('modules_count'),

        lessonsAsc: byNumberAsc('lessons_count'),
        lessonsDesc: byNumberDesc('lessons_count'),

        activityAsc: byNumberAsc('activity'),
        activityDesc: byNumberDesc('activity'),

        dateAsc: (a, b) =>
            safeDate(a?.created_at) - safeDate(b?.created_at)
            || safeNumber(a?.id) - safeNumber(b?.id),

        dateDesc: (a, b) =>
            safeDate(b?.created_at) - safeDate(a?.created_at)
            || safeNumber(b?.id) - safeNumber(a?.id),

        createdAtAsc: (a, b) =>
            safeDate(a?.created_at) - safeDate(b?.created_at)
            || safeNumber(a?.id) - safeNumber(b?.id),

        createdAtDesc: (a, b) =>
            safeDate(b?.created_at) - safeDate(a?.created_at)
            || safeNumber(b?.id) - safeNumber(a?.id),

        updatedAtAsc: (a, b) =>
            safeDate(a?.updated_at) - safeDate(b?.updated_at)
            || safeNumber(a?.id) - safeNumber(b?.id),

        updatedAtDesc: (a, b) =>
            safeDate(b?.updated_at) - safeDate(a?.updated_at)
            || safeNumber(b?.id) - safeNumber(a?.id),
    }

    return sortMap[sortParam.value]
        ? list.sort(sortMap[sortParam.value])
        : list
}

/* ==========================================================
 * ПОИСК FRONTEND
 * ========================================================== */

/**
 * Фильтрация списка.
 *
 * frontend:
 * поиск выполняется здесь
 *
 * server:
 * поиск выполняется контроллером
 */
const filteredHashtags = computed(() => {
    let filtered = localHashtags.value || []
    const query = normalize(searchQuery.value)

    if (!query) {
        return sortHashtags(filtered)
    }

    filtered = filtered.filter((hashtag) => {
        const name = normalize(getHashtagName(hashtag))
        const slug = normalize(getHashtagSlug(hashtag))
        const short = normalize(getHashtagShort(hashtag))
        const description = normalize(getHashtagDescription(hashtag))
        const color = normalize(hashtag?.color)

        return name.includes(query)
            || slug.includes(query)
            || short.includes(query)
            || description.includes(query)
            || color.includes(query)
    })

    return sortHashtags(filtered)
})

/* ==========================================================
 * ЛОКАЛЬНАЯ ПАГИНАЦИЯ
 * ========================================================== */

/** Разбиение списка по страницам */
const paginatedHashtags = computed(() => {
    const per = Number(itemsPerPage.value || 10)
    const start = (currentPage.value - 1) * per

    return filteredHashtags.value.slice(start, start + per)
})

/**
 * Итоговый список:
 * frontend → локальная пагинация
 * server → данные сервера
 */
const displayedHashtags = computed(() => {
    return props.useServerProcessing
        ? hashtagsList.value
        : paginatedHashtags.value
})

watch([itemsPerPage, searchQuery], () => {
    currentPage.value = 1
})

/* ==========================================================
 * УДАЛЕНИЕ
 * ========================================================== */

/** Состояние модального окна удаления */
const showConfirmDeleteModal = ref(false)
const hashtagToDeleteId = ref(null)
const hashtagToDeleteName = ref('')

/** Открыть подтверждение удаления */
const confirmDelete = (hashtagOrId, name = null) => {
    if (typeof hashtagOrId === 'object') {
        hashtagToDeleteId.value = hashtagOrId.id
        hashtagToDeleteName.value = name || getHashtagName(hashtagOrId)
    } else {
        hashtagToDeleteId.value = hashtagOrId
        hashtagToDeleteName.value = name || `ID: ${hashtagOrId}`
    }

    showConfirmDeleteModal.value = true
}

/** Закрыть окно */
const closeModal = () => {
    showConfirmDeleteModal.value = false
    hashtagToDeleteId.value = null
    hashtagToDeleteName.value = ''
}

/** Выполнить удаление */
const deleteHashtag = () => {
    if (hashtagToDeleteId.value === null) return

    const idToDelete = hashtagToDeleteId.value
    const nameToDelete = hashtagToDeleteName.value

    router.delete(route('admin.schoolHashtags.destroy', { schoolHashtag: idToDelete }), {
        preserveScroll: true,
        preserveState: false,
        onSuccess: () => {
            toast.success(`Хештег "${nameToDelete || 'ID: ' + idToDelete}" удалён.`)
        },
        onError: (errors) => {
            const errorKey = Object.keys(errors || {})[0]
            const errorMsg = errors.general || errors[errorKey] || 'Произошла ошибка при удалении.'
            toast.error(`${errorMsg} (Хештег: ${nameToDelete || 'ID: ' + idToDelete})`)
        },
        onFinish: () => closeModal(),
    })
}

/* ==========================================================
 * ЛОКАЛЬНОЕ ОБНОВЛЕНИЕ UI
 * ========================================================== */

/**
 * Обновление записи локально
 * без полной перезагрузки страницы
 */
const patchHashtag = (hashtagId, payload) => {
    const index = localHashtags.value.findIndex(hashtag => hashtag.id === hashtagId)

    if (index !== -1) {
        localHashtags.value[index] = {
            ...localHashtags.value[index],
            ...payload,
        }
    }
}

/* ==========================================================
 * МАССОВЫЕ ОПЕРАЦИИ
 * ========================================================== */

/** Выбранные элементы */
const selectedHashtags = ref([])

/** Выбрать все */
const toggleAll = (payload) => {
    const checked = payload?.checked ?? payload?.target?.checked ?? false
    const ids = payload?.ids ?? displayedHashtags.value.map((hashtag) => hashtag.id)

    if (checked) {
        selectedHashtags.value = [...new Set([...selectedHashtags.value, ...ids])]
    } else {
        selectedHashtags.value = selectedHashtags.value.filter((id) => !ids.includes(id))
    }
}

/** Выбрать элемент */
const toggleSelectHashtag = (id) => {
    const index = selectedHashtags.value.indexOf(id)

    if (index > -1) {
        selectedHashtags.value.splice(index, 1)
    } else {
        selectedHashtags.value.push(id)
    }
}

/** Изменить порядок */
const handleSortOrderUpdate = (orderedIds) => {
    const startSort = (currentPage.value - 1) * itemsPerPage.value

    const items = orderedIds.map((id, index) => ({
        id,
        sort: startSort + index + 1,
    }))

    if (!items.length) return

    router.put(route('admin.actions.schoolHashtags.updateSortBulk'), { items }, {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => toast.success('Порядок хештегов успешно обновлён.'),
        onError: (errors) => {
            console.error('Ошибка обновления сортировки хештегов:', errors)
            toast.error(errors?.message || errors?.general || 'Не удалось обновить порядок хештегов.')

            router.reload({
                only: ['hashtags'],
                preserveScroll: true,
            })
        },
    })
}

/** Массовая активность */
const bulkToggleActivity = (newActivity) => {
    if (!selectedHashtags.value.length) {
        toast.warning('Выберите хештеги для активации/деактивации.')
        return
    }

    const idsToUpdate = [...selectedHashtags.value]

    router.put(route('admin.actions.schoolHashtags.bulkUpdateActivity'), {
        ids: idsToUpdate,
        activity: newActivity,
    }, {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => {
            idsToUpdate.forEach(id => patchHashtag(id, { activity: newActivity }))
            selectedHashtags.value = []
            toast.success('Активность выбранных хештегов обновлена.')
        },
        onError: (errors) => {
            toast.error(errors?.ids || errors?.activity || errors?.general || 'Ошибка массового обновления активности.')
        },
    })
}

/** Массовое удаление */
const bulkDelete = () => {
    if (!selectedHashtags.value.length) {
        toast.warning('Выберите хештеги для удаления.')
        return
    }

    if (!confirm('Вы уверены, что хотите удалить выбранные хештеги?')) return

    router.delete(route('admin.actions.schoolHashtags.bulkDestroy'), {
        data: { ids: selectedHashtags.value },
        preserveScroll: true,
        preserveState: false,
        onSuccess: () => {
            selectedHashtags.value = []
            toast.success('Выбранные хештеги успешно удалены.')
        },
        onError: (errors) => {
            const errorKey = Object.keys(errors || {})[0]
            toast.error(errors[errorKey] || 'Ошибка массового удаления хештегов.')
        },
    })
}

/** Обработчик массовых действий */
const handleBulkAction = (event) => {
    const action = event.target.value

    if (action === 'selectAll') {
        toggleAll({ target: { checked: true } })
    } else if (action === 'deselectAll') {
        toggleAll({ target: { checked: false } })
    } else if (action === 'activate') {
        bulkToggleActivity(true)
    } else if (action === 'deactivate') {
        bulkToggleActivity(false)
    } else if (action === 'delete') {
        bulkDelete()
    }

    event.target.value = ''
}

/* ==========================================================
 * ОПЕРАЦИИ НАД ОДНОЙ ЗАПИСЬЮ
 * ========================================================== */

/** Переключение активности */
const toggleActivity = (hashtag) => {
    const newActivity = !hashtag.activity
    const hashtagName = getHashtagName(hashtag)
    const actionText = newActivity ? t('activated') : t('deactivated')

    router.put(route('admin.actions.schoolHashtags.updateActivity', {
        schoolHashtag: hashtag.id,
    }), {
        activity: newActivity,
    }, {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => {
            patchHashtag(hashtag.id, { activity: newActivity })
            hashtag.activity = newActivity
            toast.success(`Хештег "${hashtagName}" ${actionText}.`)
        },
        onError: (errors) => {
            toast.error(errors?.activity || errors?.general || `Ошибка изменения активности для хештега "${hashtagName}".`)
        },
    })
}
</script>

<template>
    <AdminLayout :title="t('hashtags')">
        <template #header>
            <TitlePage>
                {{ t('hashtags') }}
            </TitlePage>
        </template>

        <div class="px-2 py-2 w-full max-w-12xl mx-auto">
            <div
                class="p-4 bg-slate-50 dark:bg-slate-700
                       border border-blue-400 dark:border-blue-200
                       overflow-hidden shadow-md shadow-gray-500 dark:shadow-slate-400
                       bg-opacity-95 dark:bg-opacity-95"
            >

                <div class="sm:flex sm:justify-between sm:items-center mb-3 gap-3">
                    <!-- Кнопка добавить -->
                    <DefaultButton :href="route('admin.schoolHashtags.create')">
                        <template #icon>
                            <svg class="w-4 h-4 fill-current opacity-50 shrink-0"
                                 viewBox="0 0 16 16">
                                <path
                                    d="M15 7H9V1c0-.6-.4-1-1-1S7 .4 7 1v6H1c-.6 0-1 .4-1 1s.4 1 1 1h6v6c0 .6.4 1 1 1s1-.4 1-1V9h6c.6 0 1-.4 1-1s-.4-1-1-1z" />
                            </svg>
                        </template>
                        {{ t('addLearningTag') }}
                    </DefaultButton>

                    <ProcessingModeSwitcher
                        setting-key="adminSchoolHashtagsProcessingMode"
                        :mode="adminSchoolHashtagsProcessingMode"
                        :use-server-processing="useServerProcessing"
                        :total="hashtagsCount"
                    />
                </div>

                <SearchInput
                    v-if="hashtagsCount && !useServerProcessing"
                    v-model="searchQuery"
                    :placeholder="t('searchByName')"
                />

                <ServerSearchInput
                    v-if="hashtagsCount && useServerProcessing"
                    v-model="searchQuery"
                />

                <div
                    v-if="hashtagsCount"
                    class="flex justify-between items-center flex-col md:flex-row my-3"
                >
                    <ItemsPerPageSelect
                        v-if="!useServerProcessing"
                        :items-per-page="itemsPerPage"
                        @update:itemsPerPage="itemsPerPage = $event"
                    />

                    <ServerItemsPerPageSelect
                        v-else
                        :items-per-page="itemsPerPage"
                        update-route="admin.settings.updateAdminCountHashtags"
                    />

                    <SortSelect
                        :sortParam="sortParam"
                        @update:sortParam="(val) => (sortParam = val)"
                    />
                </div>

                <div
                    v-if="hashtagsCount"
                    class="flex flex-col lg:flex-row items-center justify-between gap-3"
                >
                    <CountTable>{{ hashtagsCount }}</CountTable>

                    <BulkActionSelect
                        v-if="hashtagsCount"
                        @change="handleBulkAction"
                    />

                    <ToggleViewButton v-model:viewMode="viewMode" />
                </div>

                <div
                    v-if="hashtagsCount"
                    class="flex justify-center items-center flex-col md:flex-row mt-3">
                    <Pagination
                        v-if="!useServerProcessing"
                        :current-page="currentPage"
                        :items-per-page="itemsPerPage"
                        :total-items="filteredHashtags.length"
                        @update:currentPage="currentPage = $event"
                    />

                    <AdminServerPagination
                        v-else
                        :pagination="hashtags"
                    />
                </div>

                <!-- Таблица -->
                <HashtagTable
                    v-if="viewMode === 'table'"
                    :hashtags="displayedHashtags"
                    :selected-hashtags="selectedHashtags"
                    @toggle-activity="toggleActivity"
                    @delete="confirmDelete"
                    @update-sort-order="handleSortOrderUpdate"
                    @toggle-select="toggleSelectHashtag"
                    @toggle-all="toggleAll"
                />

                <!-- Карточки -->
                <HashtagCardGrid
                    v-else
                    :hashtags="displayedHashtags"
                    :selected-hashtags="selectedHashtags"
                    @toggle-activity="toggleActivity"
                    @delete="confirmDelete"
                    @toggle-select="toggleSelectHashtag"
                    @toggle-all="toggleAll"
                    @update-sort-order="handleSortOrderUpdate"
                />

                <div
                    v-if="hashtagsCount"
                    class="flex justify-center items-center flex-col md:flex-row mt-3">
                    <Pagination
                        v-if="!useServerProcessing"
                        :current-page="currentPage"
                        :items-per-page="itemsPerPage"
                        :total-items="filteredHashtags.length"
                        @update:currentPage="currentPage = $event"
                    />

                    <AdminServerPagination
                        v-else
                        :pagination="hashtags"
                    />
                </div>
            </div>
        </div>

        <DangerModal
            :show="showConfirmDeleteModal"
            @close="closeModal"
            :onCancel="closeModal"
            :onConfirm="deleteHashtag"
            :cancelText="t('cancel')"
            :confirmText="t('yesDelete')"
        />
    </AdminLayout>
</template>
