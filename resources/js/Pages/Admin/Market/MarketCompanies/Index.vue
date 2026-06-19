<script setup>
/**
 * @version PulsarCMS 1.0
 * @author Александр Косолапов <kosolapov1976@gmail.com>
 *
 * Список компаний Market
 * - режимы обработки: frontend | server | auto
 * - локальный поиск/сортировка/пагинация
 * - серверный поиск/сортировка/пагинация
 */
import { defineProps, ref, watch, computed } from 'vue'
import { useI18n } from 'vue-i18n'
import { useToast } from 'vue-toastification'
import { router, usePage } from '@inertiajs/vue3'

import AdminLayout from '@/Layouts/AdminLayout.vue'
import TitlePage from '@/Components/Admin/UI/Headlines/TitlePage.vue'
import DefaultButton from '@/Components/Admin/UI/Buttons/DefaultButton.vue'
import DangerModal from '@/Components/Admin/UI/Modal/DangerModal.vue'
import CountTable from '@/Components/Admin/UI/Count/CountTable.vue'
import ToggleViewButton from '@/Components/Admin/UI/Buttons/ToggleViewButton.vue'
import SearchInput from '@/Components/Admin/UI/Search/SearchInput.vue'
import Pagination from '@/Components/Admin/UI/Pagination/Pagination.vue'
import ItemsPerPageSelect from '@/Components/Admin/UI/Select/ItemsPerPageSelect.vue'

import ProcessingModeSwitcher from '@/Components/Admin/UI/Processing/ProcessingModeSwitcher.vue'
import AdminServerPagination from '@/Components/Admin/UI/Pagination/AdminServerPagination.vue'
import ServerItemsPerPageSelect from '@/Components/Admin/UI/Select/ServerItemsPerPageSelect.vue'
import ServerSearchInput from '@/Components/Admin/UI/Search/ServerSearchInput.vue'

import BulkActionSelect from '@/Components/Admin/Market/MarketCompany/Select/BulkActionSelect.vue'
import SortSelect from '@/Components/Admin/Market/MarketCompany/Sort/SortSelect.vue'
import CompanyTable from '@/Components/Admin/Market/MarketCompany/Table/CompanyTable.vue'
import CompanyCardGrid from '@/Components/Admin/Market/MarketCompany/View/CompanyCardGrid.vue'

/** Локализация и сервисы */
const { t, locale } = useI18n()
const toast = useToast()
const page = usePage()

/** Входные параметры страницы */
const props = defineProps({
    currentLocale: { type: String, default: '' },
    availableLocales: { type: Array, default: () => [] },

    adminMarketCompaniesProcessingMode: { type: String, default: 'frontend' },
    useServerProcessing: { type: Boolean, default: false },

    adminMarketCompaniesPerPage: { type: Number, default: 6 },
    adminMarketCompaniesDefaultSort: { type: String, default: 'idDesc' },

    companies: { type: [Array, Object], default: () => [] },
    companiesCount: { type: Number, default: 0 },

    sortParam: { type: String, default: '' },
    search: { type: String, default: '' },

    errors: { type: Object, default: () => ({}) },
})

/** Проверка роли администратора */
const isAdmin = computed(() => {
    const roles = page.props?.auth?.user?.roles || []
    return roles.some((role) => role?.name === 'admin')
})

/** Получение локализованных данных компании */
const getCompanyTranslation = (company) => company?.translation || {}

/** Получение заголовка компании */
const getCompanyTitle = (company) => getCompanyTranslation(company)?.title || company?.legal_name || `ID: ${company?.id}`

/** Получение краткого описания */
const getCompanyShort = (company) => getCompanyTranslation(company)?.short || ''

/** Получение полного описания */
const getCompanyDescription = (company) => getCompanyTranslation(company)?.description || ''

/** Нормализация строк */
const normalize = (value) => (value ?? '').toString().trim().toLowerCase()

/** Нормализация статуса модерации */
const moderationNum = (value) => {
    const number = Number(value)
    return Number.isFinite(number) ? number : 0
}

/** Режим отображения списка */
const viewMode = ref(localStorage.getItem('admin_view_mode_market_companies') || 'cards')

/** Сохранение режима отображения */
watch(viewMode, (value) => {
    localStorage.setItem('admin_view_mode_market_companies', value)
})

/** Количество элементов на странице */
const itemsPerPage = ref(props.adminMarketCompaniesPerPage || 6)

/** Обновление количества элементов */
watch(itemsPerPage, (newVal) => {
    router.put(
        route('admin.settings.updateAdminCountCompanies'),
        { value: newVal },
        {
            preserveScroll: true,
            preserveState: true,
            onSuccess: () => toast.info(`Показ ${newVal} компаний на странице.`),
            onError: (errors) => toast.error(errors.value || 'Ошибка обновления кол-ва компаний.'),
        }
    )
})

/** Текущая сортировка */
const sortParam = ref(props.sortParam || props.adminMarketCompaniesDefaultSort || 'idDesc')

/** Обновление сортировки */
watch(sortParam, (newVal) => {
    router.put(
        route('admin.settings.updateAdminSortCompanies'),
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

                toast.info('Сортировка компаний успешно изменена')
            },

            onError: (errors) => {
                toast.error(errors.value || 'Ошибка обновления сортировки компаний.')
            },
        }
    )
})

/** Локальный список компаний */
const localCompanies = ref([])

/** Нормализация списка компаний */
const companiesList = computed(() => {
    if (Array.isArray(props.companies)) {
        return props.companies
    }

    if (Array.isArray(props.companies?.data)) {
        return props.companies.data
    }

    if (Array.isArray(props.companies?.data?.data)) {
        return props.companies.data.data
    }

    if (Array.isArray(props.companies?.resource)) {
        return props.companies.resource
    }

    return []
})

/** Синхронизация локального списка */
watch(
    companiesList,
    (newVal) => {
        localCompanies.value = JSON.parse(JSON.stringify(newVal || []))
    },
    { immediate: true, deep: true }
)

/** Модальное окно удаления */
const showConfirmDeleteModal = ref(false)
const companyToDeleteId = ref(null)
const companyToDeleteTitle = ref('')

/** Подготовка удаления компании */
const confirmDelete = (companyOrId, title = null) => {
    if (typeof companyOrId === 'object') {
        companyToDeleteId.value = companyOrId.id
        companyToDeleteTitle.value = title || getCompanyTitle(companyOrId)
    } else {
        companyToDeleteId.value = companyOrId
        companyToDeleteTitle.value = title || `ID: ${companyOrId}`
    }

    showConfirmDeleteModal.value = true
}

/** Закрытие модального окна */
const closeModal = () => {
    showConfirmDeleteModal.value = false
    companyToDeleteId.value = null
    companyToDeleteTitle.value = ''
}

/** Удаление компании */
const deleteCompany = () => {
    if (companyToDeleteId.value === null) return

    const idToDelete = companyToDeleteId.value
    const titleToDelete = companyToDeleteTitle.value

    router.delete(route('admin.marketCompanies.destroy', { marketCompany: idToDelete }), {
        preserveScroll: true,
        preserveState: false,
        onSuccess: () => {
            toast.success(`Компания "${titleToDelete || 'ID: ' + idToDelete}" удалена.`)
        },
        onError: (errors) => {
            const errorKey = Object.keys(errors)[0]
            const errorMsg = errors.general || errors[errorKey] || 'Произошла ошибка при удалении.'
            toast.error(`${errorMsg} (Компания: ${titleToDelete || 'ID: ' + idToDelete})`)
        },
        onFinish: () => closeModal(),
    })
}

/** Локальное обновление компании */
const patchLocalCompany = (companyId, callback) => {
    const index = localCompanies.value.findIndex((company) => company.id === companyId)

    if (index !== -1) {
        callback(localCompanies.value[index])
    }
}

/** Переключение активности */
const toggleActivity = (company) => {
    const newActivity = !company.activity
    const title = getCompanyTitle(company)
    const actionText = newActivity ? t('activated') : t('deactivated')

    router.put(
        route('admin.actions.marketCompanies.updateActivity', { marketCompany: company.id }),
        { activity: newActivity },
        {
            preserveScroll: true,
            preserveState: true,
            onSuccess: () => {
                patchLocalCompany(company.id, (node) => {
                    node.activity = newActivity
                })

                toast.success(`Компания "${title}" ${actionText}.`)
            },
            onError: (errors) => {
                toast.error(errors.activity || errors.general || `Ошибка изменения активности для "${title}".`)
            },
        }
    )
}

/** Переключение позиции left */
const toggleLeft = (company) => {
    const newLeft = !company.left
    const title = getCompanyTitle(company)

    router.put(
        route('admin.actions.marketCompanies.updateLeft', { marketCompany: company.id }),
        { left: newLeft },
        {
            preserveScroll: true,
            preserveState: true,
            onSuccess: () => {
                patchLocalCompany(company.id, (node) => {
                    node.left = newLeft
                })

                toast.success(`Позиция left для компании "${title}" обновлена.`)
            },
            onError: (errors) => {
                toast.error(errors.left || errors.general || `Ошибка изменения left для "${title}".`)
            },
        }
    )
}

/** Переключение позиции main */
const toggleMain = (company) => {
    const newMain = !company.main
    const title = getCompanyTitle(company)

    router.put(
        route('admin.actions.marketCompanies.updateMain', { marketCompany: company.id }),
        { main: newMain },
        {
            preserveScroll: true,
            preserveState: true,
            onSuccess: () => {
                patchLocalCompany(company.id, (node) => {
                    node.main = newMain
                })

                toast.success(`Позиция main для компании "${title}" обновлена.`)
            },
            onError: (errors) => {
                toast.error(errors.main || errors.general || `Ошибка изменения main для "${title}".`)
            },
        }
    )
}

/** Переключение позиции right */
const toggleRight = (company) => {
    const newRight = !company.right
    const title = getCompanyTitle(company)

    router.put(
        route('admin.actions.marketCompanies.updateRight', { marketCompany: company.id }),
        { right: newRight },
        {
            preserveScroll: true,
            preserveState: true,
            onSuccess: () => {
                patchLocalCompany(company.id, (node) => {
                    node.right = newRight
                })

                toast.success(`Позиция right для компании "${title}" обновлена.`)
            },
            onError: (errors) => {
                toast.error(errors.right || errors.general || `Ошибка изменения right для "${title}".`)
            },
        }
    )
}

/** Поиск и пагинация */
const searchQuery = ref(props.search || '')
const currentPage = ref(1)

/** Сортировка компаний */
const sortCompanies = (companies) => {
    const list = (companies || []).slice()

    if (sortParam.value === 'ownerNameAsc') return list.sort((a, b) => normalize(a?.owner?.name).localeCompare(normalize(b?.owner?.name), locale.value))
    if (sortParam.value === 'ownerNameDesc') return list.sort((a, b) => normalize(b?.owner?.name).localeCompare(normalize(a?.owner?.name), locale.value))
    if (sortParam.value === 'ownerEmailAsc') return list.sort((a, b) => normalize(a?.owner?.email).localeCompare(normalize(b?.owner?.email), locale.value))
    if (sortParam.value === 'ownerEmailDesc') return list.sort((a, b) => normalize(b?.owner?.email).localeCompare(normalize(a?.owner?.email), locale.value))

    if (sortParam.value === 'idAsc') return list.sort((a, b) => (a.id ?? 0) - (b.id ?? 0))
    if (sortParam.value === 'idDesc') return list.sort((a, b) => (b.id ?? 0) - (a.id ?? 0))
    if (sortParam.value === 'sortAsc') return list.sort((a, b) => (a.sort ?? 0) - (b.sort ?? 0))
    if (sortParam.value === 'sortDesc') return list.sort((a, b) => (b.sort ?? 0) - (a.sort ?? 0))

    if (sortParam.value === 'titleAsc') return list.sort((a, b) => normalize(getCompanyTitle(a)).localeCompare(normalize(getCompanyTitle(b)), locale.value))
    if (sortParam.value === 'titleDesc') return list.sort((a, b) => normalize(getCompanyTitle(b)).localeCompare(normalize(getCompanyTitle(a)), locale.value))

    if (sortParam.value === 'legalNameAsc') return list.sort((a, b) => normalize(a?.legal_name).localeCompare(normalize(b?.legal_name), locale.value))
    if (sortParam.value === 'legalNameDesc') return list.sort((a, b) => normalize(b?.legal_name).localeCompare(normalize(a?.legal_name), locale.value))

    if (sortParam.value === 'companyTypeAsc') return list.sort((a, b) => normalize(a?.company_type).localeCompare(normalize(b?.company_type), locale.value))
    if (sortParam.value === 'companyTypeDesc') return list.sort((a, b) => normalize(b?.company_type).localeCompare(normalize(a?.company_type), locale.value))

    if (sortParam.value === 'cityAsc') return list.sort((a, b) => normalize(a?.city).localeCompare(normalize(b?.city), locale.value))
    if (sortParam.value === 'cityDesc') return list.sort((a, b) => normalize(b?.city).localeCompare(normalize(a?.city), locale.value))

    if (sortParam.value === 'activity') return list.filter((company) => !!company.activity)
    if (sortParam.value === 'inactive') return list.filter((company) => !company.activity)

    if (sortParam.value === 'left') return list.filter((company) => !!company.left)
    if (sortParam.value === 'noLeft') return list.filter((company) => !company.left)
    if (sortParam.value === 'main') return list.filter((company) => !!company.main)
    if (sortParam.value === 'noMain') return list.filter((company) => !company.main)
    if (sortParam.value === 'right') return list.filter((company) => !!company.right)
    if (sortParam.value === 'noRight') return list.filter((company) => !company.right)

    if (sortParam.value === 'publishedAtDesc') return list.sort((a, b) => new Date(b.published_at || 0) - new Date(a.published_at || 0))
    if (sortParam.value === 'publishedAtAsc') return list.sort((a, b) => new Date(a.published_at || 0) - new Date(b.published_at || 0))

    if (sortParam.value === 'views' || sortParam.value === 'viewsDesc') return list.sort((a, b) => (b.views ?? 0) - (a.views ?? 0))
    if (sortParam.value === 'viewsAsc') return list.sort((a, b) => (a.views ?? 0) - (b.views ?? 0))

    if (sortParam.value === 'moderationPending') return list.filter((company) => moderationNum(company?.moderation_status) === 0)
    if (sortParam.value === 'moderationApproved') return list.filter((company) => moderationNum(company?.moderation_status) === 1)
    if (sortParam.value === 'moderationRejected') return list.filter((company) => moderationNum(company?.moderation_status) === 2)

    if (sortParam.value === 'moderationStatusAsc') return list.sort((a, b) => moderationNum(a?.moderation_status) - moderationNum(b?.moderation_status))
    if (sortParam.value === 'moderationStatusDesc') return list.sort((a, b) => moderationNum(b?.moderation_status) - moderationNum(a?.moderation_status))

    return list
}

/** Фильтрация компаний */
const filteredCompanies = computed(() => {
    let filtered = localCompanies.value || []
    const query = normalize(searchQuery.value)

    if (!query) {
        return sortCompanies(filtered)
    }

    filtered = filtered.filter((company) => {
        const title = normalize(getCompanyTitle(company))
        const short = normalize(getCompanyShort(company))
        const description = normalize(getCompanyDescription(company))
        const url = normalize(company?.url)
        const legalName = normalize(company?.legal_name)
        const binIin = normalize(company?.bin_iin)
        const email = normalize(company?.email)
        const phone = normalize(company?.phone)
        const city = normalize(company?.city)
        const ownerName = normalize(company?.owner?.name)
        const ownerEmail = normalize(company?.owner?.email)

        return title.includes(query)
            || short.includes(query)
            || description.includes(query)
            || url.includes(query)
            || legalName.includes(query)
            || binIin.includes(query)
            || email.includes(query)
            || phone.includes(query)
            || city.includes(query)
            || ownerName.includes(query)
            || ownerEmail.includes(query)
    })

    return sortCompanies(filtered)
})

/** Пагинация компаний */
const paginatedCompanies = computed(() => {
    const perPage = Number(itemsPerPage.value || 10)
    const start = (currentPage.value - 1) * perPage

    return filteredCompanies.value.slice(start, start + perPage)
})

/** Список для отображения */
const displayedCompanies = computed(() => {
    return props.useServerProcessing
        ? companiesList.value
        : paginatedCompanies.value
})

/** Сброс страницы при изменениях */
watch([itemsPerPage, searchQuery], () => {
    currentPage.value = 1
})

/** Выбранные компании */
const selectedCompanies = ref([])

/** Массовое выделение */
const toggleAll = (payload) => {
    const checked = payload?.checked ?? payload?.target?.checked ?? false
    const ids = payload?.ids ?? displayedCompanies.value.map((company) => company.id)

    if (checked) {
        selectedCompanies.value = [...new Set([...selectedCompanies.value, ...ids])]
    } else {
        selectedCompanies.value = selectedCompanies.value.filter((id) => !ids.includes(id))
    }
}

/** Переключение выбора компании */
const toggleSelectCompany = (companyId) => {
    const index = selectedCompanies.value.indexOf(companyId)

    if (index > -1) {
        selectedCompanies.value.splice(index, 1)
    } else {
        selectedCompanies.value.push(companyId)
    }
}

/** Массовое обновление активности */
const bulkToggleActivity = (newActivity) => {
    if (!selectedCompanies.value.length) {
        toast.warning('Выберите компании для активации/деактивации')
        return
    }

    const idsToUpdate = [...selectedCompanies.value]

    router.put(
        route('admin.actions.marketCompanies.bulkUpdateActivity'),
        { ids: idsToUpdate, activity: newActivity },
        {
            preserveScroll: true,
            preserveState: true,
            onSuccess: () => {
                localCompanies.value = localCompanies.value.map((company) => {
                    if (idsToUpdate.includes(company.id)) {
                        return { ...company, activity: newActivity }
                    }

                    return company
                })

                selectedCompanies.value = []
                toast.success('Активность компаний массово обновлена')
            },
            onError: (errors) => {
                const msg = errors?.ids || errors?.activity || errors?.general || 'Ошибка массового обновления активности'
                toast.error(msg)
            },
        }
    )
}

/** Массовое обновление позиции */
const bulkToggleFlag = (field, newValue, routeName, successMessage) => {
    if (!selectedCompanies.value.length) {
        toast.warning('Выберите компании для массового действия')
        return
    }

    const idsToUpdate = [...selectedCompanies.value]

    router.put(
        route(routeName),
        { ids: idsToUpdate, [field]: newValue },
        {
            preserveScroll: true,
            preserveState: true,
            onSuccess: () => {
                localCompanies.value = localCompanies.value.map((company) => {
                    if (idsToUpdate.includes(company.id)) {
                        return { ...company, [field]: newValue }
                    }

                    return company
                })

                selectedCompanies.value = []
                toast.success(successMessage)
            },
            onError: (errors) => {
                const msg = errors?.ids || errors?.[field] || errors?.general || 'Ошибка массового обновления'
                toast.error(msg)
            },
        }
    )
}

/** Массовое удаление */
const bulkDelete = () => {
    if (!selectedCompanies.value.length) {
        toast.warning('Выберите хотя бы одну компанию для удаления.')
        return
    }

    if (!confirm('Вы уверены, что хотите удалить выбранные компании?')) return

    router.delete(route('admin.actions.marketCompanies.bulkDestroy'), {
        data: { ids: selectedCompanies.value },
        preserveScroll: true,
        preserveState: false,
        onSuccess: () => {
            selectedCompanies.value = []
            toast.success('Массовое удаление компаний успешно завершено.')
        },
        onError: (errors) => {
            const errorKey = Object.keys(errors)[0]
            toast.error(errors[errorKey] || 'Произошла ошибка при удалении компаний.')
        },
    })
}

/** Обработка массовых действий */
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
    } else if (action === 'left') {
        bulkToggleFlag('left', true, 'admin.actions.marketCompanies.bulkUpdateLeft', 'Компании добавлены в левую колонку')
    } else if (action === 'noLeft') {
        bulkToggleFlag('left', false, 'admin.actions.marketCompanies.bulkUpdateLeft', 'Компании убраны из левой колонки')
    } else if (action === 'main') {
        bulkToggleFlag('main', true, 'admin.actions.marketCompanies.bulkUpdateMain', 'Компании добавлены в главный блок')
    } else if (action === 'noMain') {
        bulkToggleFlag('main', false, 'admin.actions.marketCompanies.bulkUpdateMain', 'Компании убраны из главного блока')
    } else if (action === 'right') {
        bulkToggleFlag('right', true, 'admin.actions.marketCompanies.bulkUpdateRight', 'Компании добавлены в правую колонку')
    } else if (action === 'noRight') {
        bulkToggleFlag('right', false, 'admin.actions.marketCompanies.bulkUpdateRight', 'Компании убраны из правой колонки')
    } else if (action === 'delete') {
        bulkDelete()
    }

    event.target.value = ''
}

/** Модерация компании */
const approveCompany = (company, status = 1, note = '') => {
    if (!company?.id) return

    router.put(
        route('admin.actions.marketCompanies.approve', { marketCompany: company.id }),
        { moderation_status: status, moderation_note: note },
        {
            preserveScroll: true,
            preserveState: true,
            onSuccess: () => {
                patchLocalCompany(company.id, (node) => {
                    node.moderation_status = status
                    node.is_approved = status === 1
                    node.moderation_note = note
                })

                toast.success(status === 1 ? 'Компания одобрена' : 'Компания отклонена')
            },
            onError: () => toast.error('Ошибка модерации компании'),
        }
    )
}

/** Обновление порядка сортировки */
const handleSortOrderUpdate = (newOrderIds) => {
    const items = newOrderIds.map((id, index) => ({
        id,
        sort: index,
    }))

    if (!items.length) return

    router.put(
        route('admin.actions.marketCompanies.updateSortBulk'),
        { items },
        {
            preserveScroll: true,
            preserveState: true,
            onSuccess: () => toast.success('Сортировка компаний обновлена'),
            onError: (errors) => {
                console.error('Ошибка сортировки компаний:', errors)
                toast.error(errors.message || 'Ошибка обновления сортировки')
            },
        }
    )
}
</script>

<template>
    <AdminLayout :title="t('marketCompanies')">
        <template #header>
            <TitlePage>{{ t('marketCompanies') }}</TitlePage>
        </template>

        <div class="px-2 py-2 w-full max-w-12xl mx-auto">
            <div
                class="p-4 bg-slate-50 dark:bg-slate-700
                       border border-blue-400 dark:border-blue-200
                       overflow-hidden shadow-md shadow-gray-500 dark:shadow-slate-400
                       bg-opacity-95 dark:bg-opacity-95"
            >
                <div class="sm:flex sm:justify-between sm:items-center mb-3 gap-3">
                    <DefaultButton :href="route('admin.marketCompanies.create')">
                        {{ t('addMarketCompany') }}
                    </DefaultButton>

                    <ProcessingModeSwitcher
                        setting-key="adminMarketCompaniesProcessingMode"
                        :mode="adminMarketCompaniesProcessingMode"
                        :use-server-processing="useServerProcessing"
                        :total="companiesCount"
                    />
                </div>

                <SearchInput
                    v-if="companiesCount && !useServerProcessing"
                    v-model="searchQuery"
                />

                <ServerSearchInput
                    v-if="companiesCount && useServerProcessing"
                    v-model="searchQuery"
                />

                <div
                    v-if="companiesCount"
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
                        update-route="admin.settings.updateAdminCountMarketCompanies"
                    />

                    <SortSelect
                        :sortParam="sortParam"
                        @update:sortParam="(val) => (sortParam = val)"
                    />
                </div>

                <div
                    v-if="companiesCount"
                    class="flex flex-col lg:flex-row items-center justify-between gap-3"
                >
                    <CountTable>{{ companiesCount }}</CountTable>

                    <BulkActionSelect
                        v-if="companiesCount"
                        @change="handleBulkAction"
                    />

                    <ToggleViewButton v-model:viewMode="viewMode" />
                </div>

                <div
                    v-if="companiesCount"
                    class="flex justify-center items-center flex-col md:flex-row mt-3"
                >
                    <Pagination
                        v-if="!useServerProcessing"
                        :current-page="currentPage"
                        :items-per-page="itemsPerPage"
                        :total-items="filteredCompanies.length"
                        @update:currentPage="currentPage = $event"
                    />

                    <AdminServerPagination
                        v-else
                        :pagination="companies"
                    />
                </div>

                <CompanyTable
                    v-if="viewMode === 'table'"
                    :companies="displayedCompanies"
                    :selected-companies="selectedCompanies"
                    :is-admin="isAdmin"
                    @toggle-left="toggleLeft"
                    @toggle-main="toggleMain"
                    @toggle-right="toggleRight"
                    @toggle-activity="toggleActivity"
                    @delete="confirmDelete"
                    @update-sort-order="handleSortOrderUpdate"
                    @toggle-select="toggleSelectCompany"
                    @toggle-all="toggleAll"
                    @approve="approveCompany"
                />

                <CompanyCardGrid
                    v-else
                    :companies="displayedCompanies"
                    :selected-companies="selectedCompanies"
                    :is-admin="isAdmin"
                    @toggle-left="toggleLeft"
                    @toggle-main="toggleMain"
                    @toggle-right="toggleRight"
                    @toggle-activity="toggleActivity"
                    @delete="confirmDelete"
                    @update-sort-order="handleSortOrderUpdate"
                    @toggle-select="toggleSelectCompany"
                    @toggle-all="toggleAll"
                    @approve="approveCompany"
                />

                <div
                    v-if="companiesCount"
                    class="flex justify-center items-center flex-col md:flex-row mt-3"
                >
                    <Pagination
                        v-if="!useServerProcessing"
                        :current-page="currentPage"
                        :items-per-page="itemsPerPage"
                        :total-items="filteredCompanies.length"
                        @update:currentPage="currentPage = $event"
                    />

                    <AdminServerPagination
                        v-else
                        :pagination="companies"
                    />
                </div>
            </div>
        </div>

        <DangerModal
            :show="showConfirmDeleteModal"
            :onCancel="closeModal"
            :onConfirm="deleteCompany"
            :cancelText="t('cancel')"
            :confirmText="t('yesDelete')"
            @close="closeModal"
        />
    </AdminLayout>
</template>
