<script setup>
/**
 * Заявки форм — Index
 *
 * Поддерживает:
 * - frontend / server / auto режимы обработки;
 * - локальный поиск / сортировку / пагинацию;
 * - серверный поиск / сортировку / пагинацию;
 * - CRM-фильтры через Laravel;
 * - табличное и карточное представление;
 * - просмотр / редактирование / удаление заявок.
 */

import { computed, defineProps, ref, watch } from 'vue'
import { router } from '@inertiajs/vue3'
import { useI18n } from 'vue-i18n'
import { useToast } from 'vue-toastification'

import AdminLayout from '@/Layouts/AdminLayout.vue'
import TitlePage from '@/Components/Admin/UI/Headlines/TitlePage.vue'
import DangerModal from '@/Components/Admin/UI/Modal/DangerModal.vue'
import Pagination from '@/Components/Admin/UI/Pagination/Pagination.vue'
import AdminServerPagination from '@/Components/Admin/UI/Pagination/AdminServerPagination.vue'
import ItemsPerPageSelect from '@/Components/Admin/UI/Select/ItemsPerPageSelect.vue'
import ServerItemsPerPageSelect from '@/Components/Admin/UI/Select/ServerItemsPerPageSelect.vue'
import SearchInput from '@/Components/Admin/UI/Search/SearchInput.vue'
import ServerSearchInput from '@/Components/Admin/UI/Search/ServerSearchInput.vue'
import CountTable from '@/Components/Admin/UI/Count/CountTable.vue'
import ToggleViewButton from '@/Components/Admin/UI/Buttons/ToggleViewButton.vue'
import ProcessingModeSwitcher from '@/Components/Admin/UI/Processing/ProcessingModeSwitcher.vue'

import FormSubmissionFilters from '@/Components/Admin/Form/FormSubmission/Filter/FormSubmissionFilters.vue'
import SortSelect from '@/Components/Admin/Form/FormSubmission/Sort/SortSelect.vue'
import FormSubmissionTable from '@/Components/Admin/Form/FormSubmission/Table/FormSubmissionTable.vue'
import FormSubmissionCardGrid from '@/Components/Admin/Form/FormSubmission/View/FormSubmissionCardGrid.vue'

const { t } = useI18n()
const toast = useToast()

const props = defineProps({
    submissions: { type: [Array, Object], default: () => [] },
    submissionsCount: { type: Number, default: 0 },
    useServerProcessing: { type: Boolean, default: false },
    adminFormSubmissionsProcessingMode: { type: String, default: 'auto' },
    adminFormSubmissionsPerPage: { type: Number, default: 20 },
    adminFormSubmissionsDefaultSort: { type: String, default: 'submittedAtDesc' },
    sortParam: { type: String, default: '' },
    search: { type: String, default: '' },
    filters: { type: Object, default: () => ({}) },
    statuses: { type: [Array, Object], default: () => [] },
    currentLocale: { type: String, default: 'ru' },
    availableLocales: { type: Array, default: () => [] },
    error: { type: String, default: '' },
    errors: { type: Object, default: () => ({}) },
})

/*
|--------------------------------------------------------------------------
| Режим отображения
|--------------------------------------------------------------------------
*/

const viewMode = ref(localStorage.getItem('admin_view_mode_form_submissions') || 'table')

watch(viewMode, (value) => {
    localStorage.setItem('admin_view_mode_form_submissions', value)
})

/*
|--------------------------------------------------------------------------
| Нормализация данных
|--------------------------------------------------------------------------
*/

const submissionsList = computed(() => {
    if (Array.isArray(props.submissions)) return props.submissions
    if (Array.isArray(props.submissions?.data)) return props.submissions.data
    return []
})

const localSubmissions = ref([])

watch(
    submissionsList,
    (value) => {
        localSubmissions.value = JSON.parse(JSON.stringify(value || []))
    },
    { immediate: true, deep: true }
)

/*
|--------------------------------------------------------------------------
| Helpers
|--------------------------------------------------------------------------
*/

const normalize = (value) => (value ?? '').toString().trim().toLowerCase()

const safeNumber = (value) => {
    const number = Number(value)
    return Number.isFinite(number) ? number : 0
}

const safeDate = (value) => {
    const time = new Date(value || 0).getTime()
    return Number.isFinite(time) ? time : 0
}

const formTitle = (submission) => {
    return submission?.form?.translation?.title
        || submission?.form?.title
        || submission?.form?.code
        || ''
}

const userName = (submission) => submission?.user?.name || ''
const userEmail = (submission) => submission?.user?.email || ''

const assignedUserName = (submission) => {
    return submission?.assigned_user?.name || submission?.assignedUser?.name || ''
}

const assignedUserEmail = (submission) => {
    return submission?.assigned_user?.email || submission?.assignedUser?.email || ''
}

/*
|--------------------------------------------------------------------------
| Пагинация / количество элементов
|--------------------------------------------------------------------------
*/

const currentPage = ref(1)
const itemsPerPage = ref(Number(props.adminFormSubmissionsPerPage || 20))

watch(itemsPerPage, (newValue) => {
    currentPage.value = 1

    router.put(
        route('admin.settings.updateAdminCountFormSubmissions'),
        { value: newValue },
        {
            preserveScroll: true,
            preserveState: true,
            onSuccess: () => toast.info(`Показ ${newValue} элементов на странице.`),
            onError: (errors) => toast.error(
                errors?.value || 'Ошибка обновления количества элементов.'
            ),
        }
    )
})

/*
|--------------------------------------------------------------------------
| Сортировка
|--------------------------------------------------------------------------
*/

const sortParam = ref(
    props.sortParam
    || props.adminFormSubmissionsDefaultSort
    || 'submittedAtDesc'
)

watch(sortParam, (newValue) => {
    currentPage.value = 1

    router.put(
        route('admin.settings.updateAdminSortFormSubmissions'),
        { value: newValue },
        {
            preserveScroll: true,
            preserveState: true,
            onSuccess: () => {
                if (props.useServerProcessing) {
                    reloadServerList({
                        sort: newValue || undefined,
                        page: undefined,
                    })
                }

                toast.info('Сортировка успешно изменена.')
            },
            onError: (errors) => toast.error(
                errors?.value || 'Ошибка обновления сортировки.'
            ),
        }
    )
})

/*
|--------------------------------------------------------------------------
| Поиск
|--------------------------------------------------------------------------
*/

const searchQuery = ref(props.search || '')

watch(searchQuery, () => {
    currentPage.value = 1
})

/*
|--------------------------------------------------------------------------
| Локальная сортировка
|--------------------------------------------------------------------------
|
| Повторяет сортировочную часть FormSubmission::scopeSortByParam().
| CRM-фильтрация здесь не выполняется.
|
*/

const compareIdAsc = (a, b) => safeNumber(a?.id) - safeNumber(b?.id)
const compareIdDesc = (a, b) => safeNumber(b?.id) - safeNumber(a?.id)

const compareTextAsc = (a, b) => {
    return normalize(a).localeCompare(normalize(b), props.currentLocale)
}

const compareTextDesc = (a, b) => {
    return normalize(b).localeCompare(normalize(a), props.currentLocale)
}

const compareNumberAsc = (a, b) => safeNumber(a) - safeNumber(b)
const compareNumberDesc = (a, b) => safeNumber(b) - safeNumber(a)
const compareDateAsc = (a, b) => safeDate(a) - safeDate(b)
const compareDateDesc = (a, b) => safeDate(b) - safeDate(a)

const withIdAsc = (compare) => {
    return (a, b) => {
        const result = compare(a, b)
        return result !== 0 ? result : compareIdAsc(a, b)
    }
}

const withIdDesc = (compare) => {
    return (a, b) => {
        const result = compare(a, b)
        return result !== 0 ? result : compareIdDesc(a, b)
    }
}

/**
 * Аналог FormSubmission::scopeOrdered().
 * submitted_at DESC, id DESC.
 */
const orderedSubmissions = (submissions) => {
    return submissions.slice().sort(
        withIdDesc((a, b) => compareDateDesc(a?.submitted_at, b?.submitted_at))
    )
}

const sortSubmissions = (submissions) => {
    const list = (submissions || []).slice()

    const sortMap = {
        idAsc: compareIdAsc,
        idDesc: compareIdDesc,

        submittedAtAsc: withIdAsc((a, b) => compareDateAsc(a?.submitted_at, b?.submitted_at)),
        submittedAtDesc: withIdDesc((a, b) => compareDateDesc(a?.submitted_at, b?.submitted_at)),

        statusAsc: withIdAsc((a, b) => compareTextAsc(a?.status, b?.status)),
        statusDesc: withIdDesc((a, b) => compareTextDesc(a?.status, b?.status)),

        sourceAsc: withIdAsc((a, b) => compareTextAsc(a?.source, b?.source)),
        sourceDesc: withIdDesc((a, b) => compareTextDesc(a?.source, b?.source)),

        localeAsc: withIdAsc((a, b) => compareTextAsc(a?.locale, b?.locale)),
        localeDesc: withIdDesc((a, b) => compareTextDesc(a?.locale, b?.locale)),

        formIdAsc: withIdAsc((a, b) => compareNumberAsc(a?.form_id, b?.form_id)),
        formIdDesc: withIdDesc((a, b) => compareNumberDesc(a?.form_id, b?.form_id)),

        formTitleAsc: withIdAsc((a, b) => compareTextAsc(formTitle(a), formTitle(b))),
        formTitleDesc: withIdDesc((a, b) => compareTextDesc(formTitle(a), formTitle(b))),

        userNameAsc: withIdAsc((a, b) => compareTextAsc(userName(a), userName(b))),
        userNameDesc: withIdDesc((a, b) => compareTextDesc(userName(a), userName(b))),

        assignedUserNameAsc: withIdAsc(
            (a, b) => compareTextAsc(assignedUserName(a), assignedUserName(b))
        ),
        assignedUserNameDesc: withIdDesc(
            (a, b) => compareTextDesc(assignedUserName(a), assignedUserName(b))
        ),

        valuesAsc: withIdAsc((a, b) => compareNumberAsc(a?.values_count, b?.values_count)),
        valuesDesc: withIdDesc((a, b) => compareNumberDesc(a?.values_count, b?.values_count)),

        filesAsc: withIdAsc((a, b) => compareNumberAsc(a?.files_count, b?.files_count)),
        filesDesc: withIdDesc((a, b) => compareNumberDesc(a?.files_count, b?.files_count)),

        processedAtAsc: withIdAsc((a, b) => compareDateAsc(a?.processed_at, b?.processed_at)),
        processedAtDesc: withIdDesc((a, b) => compareDateDesc(a?.processed_at, b?.processed_at)),

        completedAtAsc: withIdAsc((a, b) => compareDateAsc(a?.completed_at, b?.completed_at)),
        completedAtDesc: withIdDesc((a, b) => compareDateDesc(a?.completed_at, b?.completed_at)),

        createdAtAsc: withIdAsc((a, b) => compareDateAsc(a?.created_at, b?.created_at)),
        createdAtDesc: withIdDesc((a, b) => compareDateDesc(a?.created_at, b?.created_at)),

        dateAsc: withIdAsc((a, b) => compareDateAsc(a?.created_at, b?.created_at)),
        dateDesc: withIdDesc((a, b) => compareDateDesc(a?.created_at, b?.created_at)),

        updatedAtAsc: withIdAsc((a, b) => compareDateAsc(a?.updated_at, b?.updated_at)),
        updatedAtDesc: withIdDesc((a, b) => compareDateDesc(a?.updated_at, b?.updated_at)),
    }

    return sortMap[sortParam.value]
        ? list.sort(sortMap[sortParam.value])
        : orderedSubmissions(list)
}

/*
|--------------------------------------------------------------------------
| Локальный поиск
|--------------------------------------------------------------------------
*/

const filteredSubmissions = computed(() => {
    let filtered = localSubmissions.value || []
    const query = normalize(searchQuery.value)

    if (!query) return sortSubmissions(filtered)

    filtered = filtered.filter((submission) => {
        const values = [
            submission?.id,
            submission?.form_id,
            submission?.form?.code,
            formTitle(submission),
            submission?.status,
            submission?.source,
            submission?.locale,
            submission?.user_id,
            userName(submission),
            userEmail(submission),
            submission?.assigned_user_id,
            assignedUserName(submission),
            assignedUserEmail(submission),
        ]

        return values.some((value) => normalize(value).includes(query))
    })

    return sortSubmissions(filtered)
})

/*
|--------------------------------------------------------------------------
| Локальная пагинация
|--------------------------------------------------------------------------
*/

const paginatedSubmissions = computed(() => {
    const perPage = Number(itemsPerPage.value || 20)
    const start = (currentPage.value - 1) * perPage

    return filteredSubmissions.value.slice(start, start + perPage)
})

const displayedSubmissions = computed(() => {
    return props.useServerProcessing
        ? submissionsList.value
        : paginatedSubmissions.value
})

/*
|--------------------------------------------------------------------------
| Счётчики
|--------------------------------------------------------------------------
*/

const totalSubmissionsCount = computed(() => Number(props.submissionsCount || 0))

const filteredCount = computed(() => {
    if (props.useServerProcessing) {
        return Number(
            props.submissions?.meta?.total
            ?? props.submissions?.total
            ?? submissionsList.value.length
        )
    }

    return filteredSubmissions.value.length
})

/*
|--------------------------------------------------------------------------
| Server query helpers
|--------------------------------------------------------------------------
*/

const currentQuery = () => {
    return Object.fromEntries(new URLSearchParams(window.location.search))
}

const cleanParams = (params) => {
    return Object.fromEntries(
        Object.entries(params).filter(([, value]) => {
            return value !== undefined && value !== null && value !== ''
        })
    )
}

const reloadServerList = (patch = {}, options = {}) => {
    const params = cleanParams({
        ...currentQuery(),
        ...patch,
    })

    router.get(
        route('admin.formSubmissions.index'),
        params,
        {
            preserveScroll: options.preserveScroll ?? true,
            preserveState: options.preserveState ?? true,
            replace: true,
        }
    )
}

/*
|--------------------------------------------------------------------------
| Server search
|--------------------------------------------------------------------------
*/

let searchTimer = null

watch(searchQuery, (newValue) => {
    if (!props.useServerProcessing) return

    clearTimeout(searchTimer)

    searchTimer = setTimeout(() => {
        reloadServerList({
            search: newValue || undefined,
            page: undefined,
        })
    }, 400)
})

/*
|--------------------------------------------------------------------------
| CRM-фильтры
|--------------------------------------------------------------------------
|
| Frontend:
| CRM-фильтры → Laravel → поиск → сортировка → пагинация Vue.
|
| Server:
| CRM-фильтры → Laravel → поиск → сортировка → пагинация Laravel.
|
*/

const emptyCrmFilters = () => ({
    form_id: '',
    status: '',
    source: '',
    locale: '',
    user_id: '',
    assigned_user_id: '',
    utm_source: '',
    utm_campaign: '',
    date_from: '',
    date_to: '',
})

const crmFilters = ref({
    ...emptyCrmFilters(),
    ...props.filters,
})

const hasActiveCrmFilters = computed(() => {
    return Object.values(crmFilters.value).some((value) => {
        return value !== '' && value !== null && value !== undefined
    })
})

const applyCrmFilters = () => {
    currentPage.value = 1

    reloadServerList(
        {
            ...crmFilters.value,
            search: props.useServerProcessing ? searchQuery.value || undefined : undefined,
            sort: props.useServerProcessing ? sortParam.value || undefined : undefined,
            page: undefined,
        },
        {
            preserveState: false,
        }
    )
}

const resetCrmFilters = () => {
    crmFilters.value = emptyCrmFilters()
    currentPage.value = 1

    router.get(
        route('admin.formSubmissions.index'),
        cleanParams({
            search: props.useServerProcessing ? searchQuery.value || undefined : undefined,
            sort: props.useServerProcessing ? sortParam.value || undefined : undefined,
        }),
        {
            preserveScroll: true,
            preserveState: false,
            replace: true,
        }
    )
}

/*
|--------------------------------------------------------------------------
| Удаление
|--------------------------------------------------------------------------
*/

const showDeleteModal = ref(false)
const submissionToDelete = ref(null)

const confirmDelete = (submission) => {
    submissionToDelete.value = submission
    showDeleteModal.value = true
}

const closeDeleteModal = () => {
    showDeleteModal.value = false
    submissionToDelete.value = null
}

const deleteSubmission = () => {
    if (!submissionToDelete.value?.id) return

    const submissionId = submissionToDelete.value.id

    router.delete(
        route('admin.formSubmissions.destroy', {
            formSubmission: submissionId,
        }),
        {
            preserveScroll: true,
            onSuccess: () => {
                localSubmissions.value = localSubmissions.value.filter(
                    (submission) => submission.id !== submissionId
                )

                closeDeleteModal()
                toast.success('Заявка успешно удалена.')
            },
            onError: (errors) => {
                toast.error(
                    errors?.error
                    || errors?.general
                    || 'Не удалось удалить заявку.'
                )
            },
        }
    )
}

/*
|--------------------------------------------------------------------------
| Backend error
|--------------------------------------------------------------------------
*/

watch(
    () => props.error,
    (value) => {
        if (value) toast.error(value)
    },
    { immediate: true }
)
</script>

<template>
    <AdminLayout :title="t('formSubmissions')">
        <template #header>
            <TitlePage>{{ t('formSubmissions') }}</TitlePage>
        </template>

        <div class="py-3">
            <div class="mx-auto max-w-full sm:px-4 lg:px-6">
                <!-- ===================== Управление ===================== -->

                <div class="sm:flex sm:justify-between sm:items-center mb-3 gap-3">
                    <div></div>

                    <ProcessingModeSwitcher
                        setting-key="adminFormSubmissionsProcessingMode"
                        :mode="adminFormSubmissionsProcessingMode"
                        :use-server-processing="useServerProcessing"
                        :total="submissionsCount"
                    />
                </div>

                <!-- ===================== Поиск ===================== -->

                <SearchInput
                    v-if="submissionsCount && !useServerProcessing"
                    v-model="searchQuery"
                />

                <ServerSearchInput
                    v-if="submissionsCount && useServerProcessing"
                    v-model="searchQuery"
                />

                <!-- ===================== Количество / сортировка ===================== -->

                <div
                    v-if="submissionsCount"
                    class="my-3 flex flex-col items-center justify-between gap-3 md:flex-row"
                >
                    <ItemsPerPageSelect
                        v-if="!useServerProcessing"
                        :items-per-page="itemsPerPage"
                        @update:itemsPerPage="itemsPerPage = $event"
                    />

                    <ServerItemsPerPageSelect
                        v-else
                        :items-per-page="itemsPerPage"
                        update-route="admin.settings.updateAdminCountFormSubmissions"
                    />

                    <SortSelect v-model:sortParam="sortParam" />
                </div>

                <!-- ===================== CRM-фильтры ===================== -->

                <FormSubmissionFilters
                    v-model="crmFilters"
                    :statuses="statuses"
                    :available-locales="availableLocales"
                    @apply="applyCrmFilters"
                    @reset="resetCrmFilters"
                />

                <!-- ===================== Счётчик / вид ===================== -->

                <div
                    v-if="submissionsCount"
                    class="flex flex-col items-center justify-between gap-3 lg:flex-row"
                >
                    <div class="flex flex-wrap items-center gap-3">
                        <CountTable>{{ filteredCount }}</CountTable>

                        <span
                            v-if="filteredCount !== totalSubmissionsCount"
                            class="text-xs text-gray-500 dark:text-gray-400"
                        >
                            Всего доступно: {{ totalSubmissionsCount }}
                        </span>
                    </div>

                    <ToggleViewButton v-model:viewMode="viewMode" />
                </div>

                <!-- ===================== Верхняя пагинация ===================== -->

                <div
                    v-if="submissionsCount"
                    class="mt-3 flex flex-col items-center justify-center md:flex-row"
                >
                    <Pagination
                        v-if="!useServerProcessing"
                        :current-page="currentPage"
                        :items-per-page="itemsPerPage"
                        :total-items="filteredSubmissions.length"
                        @update:currentPage="currentPage = $event"
                    />

                    <AdminServerPagination
                        v-else
                        :pagination="submissions"
                    />
                </div>

                <!-- ===================== Пустой список ===================== -->

                <div
                    v-if="!displayedSubmissions.length"
                    class="py-12 text-center text-gray-500 dark:text-gray-300"
                >
                    <template v-if="hasActiveCrmFilters || searchQuery">
                        Заявки по заданным условиям не найдены.
                    </template>

                    <template v-else>
                        Заявки форм не найдены.
                    </template>
                </div>

                <!-- ===================== Таблица ===================== -->

                <FormSubmissionTable
                    v-else-if="viewMode === 'table'"
                    :submissions="displayedSubmissions"
                    :statuses="statuses"
                    @delete="confirmDelete"
                />

                <!-- ===================== Карточки ===================== -->

                <FormSubmissionCardGrid
                    v-else
                    :submissions="displayedSubmissions"
                    :statuses="statuses"
                    @delete="confirmDelete"
                />

                <!-- ===================== Нижняя пагинация ===================== -->

                <div
                    v-if="displayedSubmissions.length"
                    class="mt-4 flex flex-col items-center justify-center md:flex-row"
                >
                    <Pagination
                        v-if="!useServerProcessing"
                        :current-page="currentPage"
                        :items-per-page="itemsPerPage"
                        :total-items="filteredSubmissions.length"
                        @update:currentPage="currentPage = $event"
                    />

                    <AdminServerPagination
                        v-else
                        :pagination="submissions"
                    />
                </div>
            </div>
        </div>

        <!-- ===================== Удаление ===================== -->

        <DangerModal
            :show="showDeleteModal"
            @close="closeDeleteModal"
            :onCancel="closeDeleteModal"
            :onConfirm="deleteSubmission"
        />
    </AdminLayout>
</template>
