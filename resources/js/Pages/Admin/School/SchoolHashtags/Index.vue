<script setup>
/**
 * @version PulsarCMS 1.0
 * @author Александр Косолапов <kosolapov1976@gmail.com>
 *
 * Список хештегов (паттерн)
 */
import { defineProps, ref, computed, watch } from 'vue'
import { useI18n } from 'vue-i18n'
import { useToast } from 'vue-toastification'
import { router } from '@inertiajs/vue3'

/** UI компоненты */
import AdminLayout from '@/Layouts/AdminLayout.vue'
import TitlePage from '@/Components/Admin/UI/Headlines/TitlePage.vue'
import SearchInput from '@/Components/Admin/UI/Search/SearchInput.vue'
import DefaultButton from '@/Components/Admin/UI/Buttons/DefaultButton.vue'
import CountTable from '@/Components/Admin/UI/Count/CountTable.vue'
import ItemsPerPageSelect from '@/Components/Admin/UI/Select/ItemsPerPageSelect.vue'
import DangerModal from '@/Components/Admin/UI/Modal/DangerModal.vue'
import Pagination from '@/Components/Admin/UI/Pagination/Pagination.vue'
import ToggleViewButton from '@/Components/Admin/UI/Buttons/ToggleViewButton.vue'

/** Компоненты хештегов */
import BulkActionSelect from '@/Components/Admin/School/SchoolHashtag/Select/BulkActionSelect.vue'
import HashtagTable from '@/Components/Admin/School/SchoolHashtag/Table/HashtagTable.vue'
import HashtagCardGrid from '@/Components/Admin/School/SchoolHashtag/View/HashtagCardGrid.vue'
import SortSelect from '@/Components/Admin/School/SchoolHashtag/Sort/SortSelect.vue'

/** ==================== INIT ==================== */
const { t } = useI18n()           // перевод
const toast = useToast()          // уведомления

/** ==================== VIEW MODE ==================== */
/** Режим отображения (таблица / карточки) */
const viewMode = ref(localStorage.getItem('admin_view_mode') || 'table')

/** Сохраняем режим отображения в localStorage */
watch(viewMode, (val) => {
    localStorage.setItem('admin_view_mode', val)
})

/** ==================== PROPS ==================== */
const props = defineProps({
    hashtags: Array,              // список хештегов
    hashtagsCount: Number,        // общее количество
    adminSchoolHashtagsPerPage: Number,   // кол-во на страницу
    adminSchoolHashtagsDefaultSort: String,    // сортировка
    currentLocale: String,
    availableLocales: Array,
})

/** ==================== PAGINATION SETTINGS ==================== */
/** Количество элементов на странице */
const itemsPerPage = ref(props.adminSchoolHashtagsPerPage)

/** Обновление количества элементов */
watch(itemsPerPage, (newVal) => {
    router.put(route('admin.settings.updateAdminCountHashtags'), { value: newVal }, {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => toast.info(`Показ ${newVal} элементов на странице.`),
        onError: (errors) => toast.error(errors.value || 'Ошибка обновления кол-ва элементов.'),
    })
})

/** ==================== SORT ==================== */
/** Параметр сортировки */
const sortParam = ref(props.adminSchoolHashtagsDefaultSort)

/** Обновление сортировки */
watch(sortParam, (newVal) => {
    router.put(route('admin.settings.updateAdminSortHashtags'), { value: newVal }, {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => toast.info('Сортировка успешно изменена'),
        onError: (errors) => toast.error(errors.value || 'Ошибка обновления сортировки.'),
    })
})

/** ==================== DELETE ==================== */
/** Модалка удаления */
const showConfirmDeleteModal = ref(false)
const hashtagToDeleteId = ref(null)
const hashtagToDeleteName = ref('')

/** Открыть модалку удаления */
const confirmDelete = (id, name) => {
    hashtagToDeleteId.value = id
    hashtagToDeleteName.value = name
    showConfirmDeleteModal.value = true
}

/** Закрыть модалку */
const closeModal = () => {
    showConfirmDeleteModal.value = false
    hashtagToDeleteId.value = null
    hashtagToDeleteName.value = ''
}

/** Удаление хештега */
const deleteHashtag = () => {
    if (hashtagToDeleteId.value === null) return

    const idToDelete = hashtagToDeleteId.value
    const nameToDelete = hashtagToDeleteName.value

    router.delete(route('admin.schoolHashtags.destroy', { schoolHashtag: idToDelete }), {
        preserveScroll: true,
        preserveState: false,
        onSuccess: () => {
            closeModal()
            toast.success(`Хештег "${nameToDelete || 'ID: ' + idToDelete}" удалён.`)
        },
        onError: (errors) => {
            closeModal()
            const errorMsg = errors.general || errors[Object.keys(errors)[0]]
                || 'Произошла ошибка при удалении.'
            toast.error(errorMsg)
        },
    })
}

/** ==================== SEARCH ==================== */
/** Текущая страница */
const currentPage = ref(1)

/** Поисковый запрос */
const searchQuery = ref('')

/** ==================== SORT LOGIC ==================== */
/** Сортировка списка */
const normalize = (value) => String(value ?? '').toLowerCase()

const safeNumber = (value) => {
    const number = Number(value)
    return Number.isFinite(number) ? number : 0
}

/** Сортировка списка */
const sortHashtags = (hashtags) => {
    const list = (hashtags || []).slice()

    if (sortParam.value === 'idAsc') {
        return list.sort((a, b) => safeNumber(a.id) - safeNumber(b.id))
    }

    if (sortParam.value === 'idDesc') {
        return list.sort((a, b) => safeNumber(b.id) - safeNumber(a.id))
    }

    if (sortParam.value === 'sortAsc') {
        return list.sort((a, b) => safeNumber(a.sort) - safeNumber(b.sort))
    }

    if (sortParam.value === 'sortDesc') {
        return list.sort((a, b) => safeNumber(b.sort) - safeNumber(a.sort))
    }

    if (sortParam.value === 'nameAsc') {
        return list.sort((a, b) => normalize(a.name).localeCompare(normalize(b.name), props.currentLocale))
    }

    if (sortParam.value === 'nameDesc') {
        return list.sort((a, b) => normalize(b.name).localeCompare(normalize(a.name), props.currentLocale))
    }

    if (sortParam.value === 'slugAsc') {
        return list.sort((a, b) => normalize(a.slug).localeCompare(normalize(b.slug), props.currentLocale))
    }

    if (sortParam.value === 'slugDesc') {
        return list.sort((a, b) => normalize(b.slug).localeCompare(normalize(a.slug), props.currentLocale))
    }

    if (sortParam.value === 'colorAsc') {
        return list.sort((a, b) => normalize(a.color).localeCompare(normalize(b.color), props.currentLocale))
    }

    if (sortParam.value === 'colorDesc') {
        return list.sort((a, b) => normalize(b.color).localeCompare(normalize(a.color), props.currentLocale))
    }

    if (sortParam.value === 'viewsAsc') {
        return list.sort((a, b) => safeNumber(a.views) - safeNumber(b.views))
    }

    if (sortParam.value === 'viewsDesc') {
        return list.sort((a, b) => safeNumber(b.views) - safeNumber(a.views))
    }

    if (sortParam.value === 'likesAsc') {
        return list.sort((a, b) => safeNumber(a.likes) - safeNumber(b.likes))
    }

    if (sortParam.value === 'likesDesc') {
        return list.sort((a, b) => safeNumber(b.likes) - safeNumber(a.likes))
    }

    if (sortParam.value === 'coursesAsc') {
        return list.sort((a, b) => safeNumber(a.courses_count) - safeNumber(b.courses_count))
    }

    if (sortParam.value === 'coursesDesc') {
        return list.sort((a, b) => safeNumber(b.courses_count) - safeNumber(a.courses_count))
    }

    if (sortParam.value === 'modulesAsc') {
        return list.sort((a, b) => safeNumber(a.modules_count) - safeNumber(b.modules_count))
    }

    if (sortParam.value === 'modulesDesc') {
        return list.sort((a, b) => safeNumber(b.modules_count) - safeNumber(a.modules_count))
    }

    if (sortParam.value === 'lessonsAsc') {
        return list.sort((a, b) => safeNumber(a.lessons_count) - safeNumber(b.lessons_count))
    }

    if (sortParam.value === 'lessonsDesc') {
        return list.sort((a, b) => safeNumber(b.lessons_count) - safeNumber(a.lessons_count))
    }

    if (sortParam.value === 'createdAtAsc') {
        return list.sort((a, b) => new Date(a.created_at || 0) - new Date(b.created_at || 0))
    }

    if (sortParam.value === 'createdAtDesc') {
        return list.sort((a, b) => new Date(b.created_at || 0) - new Date(a.created_at || 0))
    }

    if (sortParam.value === 'updatedAtAsc') {
        return list.sort((a, b) => new Date(a.updated_at || 0) - new Date(b.updated_at || 0))
    }

    if (sortParam.value === 'updatedAtDesc') {
        return list.sort((a, b) => new Date(b.updated_at || 0) - new Date(a.updated_at || 0))
    }

    if (sortParam.value === 'activityAsc') {
        return list.sort((a, b) => Number(a.activity) - Number(b.activity))
    }

    if (sortParam.value === 'activityDesc') {
        return list.sort((a, b) => Number(b.activity) - Number(a.activity))
    }

    if (sortParam.value === 'activity') {
        return list.filter((item) => item.activity)
    }

    if (sortParam.value === 'inactive') {
        return list.filter((item) => !item.activity)
    }

    return list
}

/** ==================== FILTER ==================== */
/** Фильтрация + поиск */
const filteredHashtags = computed(() => {
    let filtered = props.hashtags || []

    if (searchQuery.value) {
        const q = searchQuery.value.toLowerCase()

        filtered = filtered.filter(i =>
            normalize(i.name).includes(q)
            || normalize(i.slug).includes(q)
            || normalize(i.short).includes(q)
            || normalize(i.description).includes(q)
        )
    }

    return sortHashtags(filtered)
})

/** ==================== PAGINATION ==================== */
/** Пагинированный список */
const paginatedHashtags = computed(() => {
    const start = (currentPage.value - 1) * itemsPerPage.value
    return filteredHashtags.value.slice(start, start + itemsPerPage.value)
})

/** ==================== SORT DRAG ==================== */
/** Обновление сортировки drag&drop */
const handleSortOrderUpdate = (orderedIds) => {
    const startSort = (currentPage.value - 1) * itemsPerPage.value

    const sortData = orderedIds.map((id, index) => ({
        id,
        sort: startSort + index + 1,
    }))

    router.put(route('admin.actions.schoolHashtags.updateSortBulk'), { items: sortData }, {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => toast.success('Порядок обновлён'),
    })
}

/** ==================== SELECTION ==================== */
/** Выбранные хештеги */
const selectedHashtags = ref([])

/** Выбрать все */
const toggleAll = ({ ids, checked }) => {
    selectedHashtags.value = checked ? [...ids] : []
}

/** Выбрать один */
const toggleSelectHashtag = (id) => {
    const idx = selectedHashtags.value.indexOf(id)
    idx > -1
        ? selectedHashtags.value.splice(idx, 1)
        : selectedHashtags.value.push(id)
}

/** ==================== BULK ==================== */
/** Массовое изменение активности */
const bulkToggleActivity = (newActivity) => {

    if (!selectedHashtags.value.length) {
        toast.warning('Выберите элементы')
        return
    }

    router.put(route('admin.actions.schoolHashtags.bulkUpdateActivity'), {
        ids: selectedHashtags.value,
        activity: newActivity
    }, {
        onSuccess: () => {
            toast.success('Активность обновлена')
            selectedHashtags.value = []
        }
    })
}

/** Массовое удаление */
const bulkDelete = () => {

    if (!selectedHashtags.value.length) {
        toast.warning('Выберите элементы')
        return
    }

    if (!confirm('Удалить выбранные?')) return

    router.delete(route('admin.actions.schoolHashtags.bulkDestroy'), {
        data: { ids: selectedHashtags.value },
        onSuccess: () => {
            selectedHashtags.value = []
            toast.success('Удалено')
        }
    })
}

/** Обработка массовых действий */
const handleBulkAction = (event) => {
    const action = event.target.value

    if (action === 'selectAll') selectedHashtags.value = paginatedHashtags.value.map(r => r.id)
    else if (action === 'deselectAll') selectedHashtags.value = []
    else if (action === 'activate') bulkToggleActivity(true)
    else if (action === 'deactivate') bulkToggleActivity(false)
    else if (action === 'delete') bulkDelete()

    event.target.value = ''
}

/** ==================== SINGLE ACTIVITY ==================== */
/** Переключение активности */
const toggleActivity = (hashtag) => {

    const newActivity = !hashtag.activity

    router.put(route('admin.actions.schoolHashtags.updateActivity', {
        schoolHashtag: hashtag.id
    }), {
        activity: newActivity
    }, {
        onSuccess: () => {
            hashtag.activity = newActivity
            toast.success('Активность изменена')
        }
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

                <div class="sm:flex sm:justify-between sm:items-center mb-3">
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
                </div>

                <SearchInput
                    v-if="hashtagsCount"
                    v-model="searchQuery"
                    :placeholder="t('searchByName')" />

                <div
                    v-if="hashtagsCount"
                    class="flex justify-between items-center flex-col md:flex-row my-3"
                >
                    <ItemsPerPageSelect
                        :items-per-page="itemsPerPage"
                        @update:itemsPerPage="itemsPerPage = $event"
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
                        :current-page="currentPage"
                        :items-per-page="itemsPerPage"
                        :total-items="filteredHashtags.length"
                        @update:currentPage="currentPage = $event"
                        @update:itemsPerPage="itemsPerPage = $event"
                    />
                </div>

                <!-- Таблица -->
                <HashtagTable
                    v-if="viewMode === 'table'"
                    :hashtags="paginatedHashtags"
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
                    :hashtags="paginatedHashtags"
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
                        :current-page="currentPage"
                        :items-per-page="itemsPerPage"
                        :total-items="filteredHashtags.length"
                        @update:currentPage="currentPage = $event"
                        @update:itemsPerPage="itemsPerPage = $event"
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
