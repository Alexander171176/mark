<script setup>
/**
 * @version PulsarCMS 1.0
 * @author Александр Косолапов <kosolapov1976@gmail.com>
 *
 * Список брендов MarketBrand
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
import ServerSearchInput from '@/Components/Admin/UI/Search/ServerSearchInput.vue'
import Pagination from '@/Components/Admin/UI/Pagination/Pagination.vue'
import AdminServerPagination from '@/Components/Admin/UI/Pagination/AdminServerPagination.vue'
import ItemsPerPageSelect from '@/Components/Admin/UI/Select/ItemsPerPageSelect.vue'
import ServerItemsPerPageSelect from '@/Components/Admin/UI/Select/ServerItemsPerPageSelect.vue'
import ProcessingModeSwitcher from '@/Components/Admin/UI/Processing/ProcessingModeSwitcher.vue'

import BulkActionSelect from '@/Components/Admin/Market/MarketBrand/Select/BulkActionSelect.vue'
import SortSelect from '@/Components/Admin/Market/MarketBrand/Sort/SortSelect.vue'

import BrandTable from '@/Components/Admin/Market/MarketBrand/Table/BrandTable.vue'
import BrandCardGrid from '@/Components/Admin/Market/MarketBrand/View/BrandCardGrid.vue'

/** Локализация и сервисы */
const { t, locale } = useI18n()
const toast = useToast()
const page = usePage()

/** Входные параметры страницы */
const props = defineProps({
    currentLocale: { type: String, default: '' },
    availableLocales: { type: Array, default: () => [] },

    adminMarketBrandsProcessingMode: { type: String, default: 'frontend' },
    useServerProcessing: { type: Boolean, default: false },

    adminMarketBrandsPerPage: { type: Number, default: 6 },
    adminMarketBrandsDefaultSort: { type: String, default: 'idDesc' },

    brands: { type: [Array, Object], default: () => [] },
    brandsCount: { type: Number, default: 0 },

    sortParam: { type: String, default: '' },
    search: { type: String, default: '' },

    errors: { type: Object, default: () => ({}) },
})

/** Проверка роли администратора */
const isAdmin = computed(() => {
    const roles = page.props?.auth?.user?.roles || []
    return roles.some((role) => role?.name === 'admin')
})

/** Получение перевода бренда */
const getBrandTranslation = (brand) => brand?.translation || brand?.translations?.[0] || {}

/** Получение названия бренда */
const getBrandTitle = (brand) => getBrandTranslation(brand)?.title || `ID: ${brand?.id}`

/** Получение краткого описания бренда */
const getBrandShort = (brand) => getBrandTranslation(brand)?.short || ''

/** Получение полного описания бренда */
const getBrandDescription = (brand) => getBrandTranslation(brand)?.description || ''

/** Получение имени владельца */
const getOwnerName = (brand) => brand?.owner?.name || ''

/** Получение email владельца */
const getOwnerEmail = (brand) => brand?.owner?.email || ''

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

/** Нормализация статуса модерации */
const moderationNum = (value) => {
    const number = Number(value)
    return Number.isFinite(number) ? number : 0
}

/** Режим отображения списка */
const viewMode = ref(
    localStorage.getItem('admin_view_mode_market_brands') || 'cards'
)

/** Сохранение режима отображения */
watch(viewMode, (value) => {
    localStorage.setItem('admin_view_mode_market_brands', value)
})

/** Количество элементов на странице */
const itemsPerPage = ref(props.adminMarketBrandsPerPage || 6)

/** Обновление количества элементов */
watch(itemsPerPage, (newVal) => {
    router.put(
        route('admin.settings.updateAdminCountMarketBrands'),
        { value: newVal },
        {
            preserveScroll: true,
            preserveState: true,
            onSuccess: () => toast.info(`Показ ${newVal} брендов на странице.`),
            onError: (errors) => toast.error(errors.value || 'Ошибка обновления кол-ва брендов.'),
        }
    )
})

/** Текущая сортировка */
const sortParam = ref(
    props.sortParam || props.adminMarketBrandsDefaultSort || 'idDesc'
)

/** Обновление сортировки */
watch(sortParam, (newVal) => {
    router.put(
        route('admin.settings.updateAdminSortMarketBrands'),
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

                toast.info('Сортировка брендов успешно изменена.')
            },

            onError: (errors) => {
                toast.error(errors.value || 'Ошибка обновления сортировки брендов.')
            },
        }
    )
})

/** Локальный список брендов */
const localBrands = ref([])

/** Нормализация списка брендов */
const brandsList = computed(() => {
    if (Array.isArray(props.brands)) {
        return props.brands
    }

    if (Array.isArray(props.brands?.data)) {
        return props.brands.data
    }

    if (Array.isArray(props.brands?.data?.data)) {
        return props.brands.data.data
    }

    if (Array.isArray(props.brands?.resource)) {
        return props.brands.resource
    }

    return []
})

/** Синхронизация локального списка */
watch(
    brandsList,
    (newVal) => {
        localBrands.value = JSON.parse(JSON.stringify(newVal || []))
    },
    { immediate: true, deep: true }
)

/** Модальное окно удаления */
const showConfirmDeleteModal = ref(false)
const brandToDeleteId = ref(null)
const brandToDeleteTitle = ref('')

/** Подготовка удаления бренда */
const confirmDelete = (brandOrId, title = null) => {
    if (typeof brandOrId === 'object') {
        brandToDeleteId.value = brandOrId.id
        brandToDeleteTitle.value = title || getBrandTitle(brandOrId)
    } else {
        brandToDeleteId.value = brandOrId
        brandToDeleteTitle.value = title || `ID: ${brandOrId}`
    }

    showConfirmDeleteModal.value = true
}

/** Закрытие модального окна */
const closeModal = () => {
    showConfirmDeleteModal.value = false
    brandToDeleteId.value = null
    brandToDeleteTitle.value = ''
}

/** Удаление бренда */
const deleteBrand = () => {
    if (brandToDeleteId.value === null) return

    const idToDelete = brandToDeleteId.value
    const titleToDelete = brandToDeleteTitle.value

    router.delete(route('admin.marketBrands.destroy', { marketBrand: idToDelete }), {
        preserveScroll: true,
        preserveState: false,

        onSuccess: () => {
            toast.success(`Бренд "${titleToDelete || 'ID: ' + idToDelete}" удалён.`)
        },

        onError: (errors) => {
            const errorKey = Object.keys(errors || {})[0]
            const errorMsg = errors.general || errors[errorKey] || 'Произошла ошибка при удалении.'
            toast.error(`${errorMsg} (Бренд: ${titleToDelete || 'ID: ' + idToDelete})`)
        },

        onFinish: () => closeModal(),
    })
}

/** Локальное обновление бренда */
const patchLocalBrand = (brandId, callback) => {
    const index = localBrands.value.findIndex((brand) => brand.id === brandId)

    if (index !== -1) {
        callback(localBrands.value[index])
    }
}

/** Переключение активности */
const toggleActivity = (brand) => {
    const newActivity = !brand.activity
    const title = getBrandTitle(brand)
    const actionText = newActivity ? t('activated') : t('deactivated')

    router.put(
        route('admin.actions.marketBrands.updateActivity', { marketBrand: brand.id }),
        { activity: newActivity },
        {
            preserveScroll: true,
            preserveState: true,

            onSuccess: () => {
                patchLocalBrand(brand.id, (node) => {
                    node.activity = newActivity
                })

                toast.success(`Бренд "${title}" ${actionText}.`)
            },

            onError: (errors) => {
                toast.error(errors.activity || errors.general || `Ошибка изменения активности для "${title}".`)
            },
        }
    )
}

/** Переключение позиции left */
const toggleLeft = (brand) => {
    const newLeft = !brand.left
    const title = getBrandTitle(brand)

    router.put(
        route('admin.actions.marketBrands.updateLeft', { marketBrand: brand.id }),
        { left: newLeft },
        {
            preserveScroll: true,
            preserveState: true,

            onSuccess: () => {
                patchLocalBrand(brand.id, (node) => {
                    node.left = newLeft
                })

                toast.success(`Позиция left для бренда "${title}" обновлена.`)
            },

            onError: (errors) => {
                toast.error(errors.left || errors.general || `Ошибка изменения left для "${title}".`)
            },
        }
    )
}

/** Переключение позиции main */
const toggleMain = (brand) => {
    const newMain = !brand.main
    const title = getBrandTitle(brand)

    router.put(
        route('admin.actions.marketBrands.updateMain', { marketBrand: brand.id }),
        { main: newMain },
        {
            preserveScroll: true,
            preserveState: true,

            onSuccess: () => {
                patchLocalBrand(brand.id, (node) => {
                    node.main = newMain
                })

                toast.success(`Позиция main для бренда "${title}" обновлена.`)
            },

            onError: (errors) => {
                toast.error(errors.main || errors.general || `Ошибка изменения main для "${title}".`)
            },
        }
    )
}

/** Переключение позиции right */
const toggleRight = (brand) => {
    const newRight = !brand.right
    const title = getBrandTitle(brand)

    router.put(
        route('admin.actions.marketBrands.updateRight', { marketBrand: brand.id }),
        { right: newRight },
        {
            preserveScroll: true,
            preserveState: true,

            onSuccess: () => {
                patchLocalBrand(brand.id, (node) => {
                    node.right = newRight
                })

                toast.success(`Позиция right для бренда "${title}" обновлена.`)
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

/** Сортировка по числу по возрастанию */
const byNumberAsc = (field) => (a, b) =>
    safeNumber(a?.[field]) - safeNumber(b?.[field])
    || safeNumber(a?.id) - safeNumber(b?.id)

/** Сортировка по числу по убыванию */
const byNumberDesc = (field) => (a, b) =>
    safeNumber(b?.[field]) - safeNumber(a?.[field])
    || safeNumber(b?.id) - safeNumber(a?.id)

/** Сортировка по дате по возрастанию */
const byDateAsc = (field) => (a, b) =>
    safeDate(a?.[field]) - safeDate(b?.[field])
    || safeNumber(a?.id) - safeNumber(b?.id)

/** Сортировка по дате по убыванию */
const byDateDesc = (field) => (a, b) =>
    safeDate(b?.[field]) - safeDate(a?.[field])
    || safeNumber(b?.id) - safeNumber(a?.id)

/** Сортировка брендов */
const sortBrands = (brands) => {
    const list = (brands || []).slice()

    if (sortParam.value === 'activity') return list.filter((brand) => !!brand.activity)
    if (sortParam.value === 'inactive') return list.filter((brand) => !brand.activity)

    if (sortParam.value === 'left') return list.filter((brand) => !!brand.left)
    if (sortParam.value === 'noLeft') return list.filter((brand) => !brand.left)
    if (sortParam.value === 'main') return list.filter((brand) => !!brand.main)
    if (sortParam.value === 'noMain') return list.filter((brand) => !brand.main)
    if (sortParam.value === 'right') return list.filter((brand) => !!brand.right)
    if (sortParam.value === 'noRight') return list.filter((brand) => !brand.right)

    if (sortParam.value === 'statusDraft') return list.filter((brand) => brand?.status === 'draft')
    if (sortParam.value === 'statusPublished') return list.filter((brand) => brand?.status === 'published')
    if (sortParam.value === 'statusArchived') return list.filter((brand) => brand?.status === 'archived')

    if (sortParam.value === 'moderationPending') return list.filter((brand) => moderationNum(brand?.moderation_status) === 0)
    if (sortParam.value === 'moderationApproved') return list.filter((brand) => moderationNum(brand?.moderation_status) === 1)
    if (sortParam.value === 'moderationRejected') return list.filter((brand) => moderationNum(brand?.moderation_status) === 2)

    const sortMap = {
        idAsc: byNumberAsc('id'),
        idDesc: byNumberDesc('id'),

        sortAsc: byNumberAsc('sort'),
        sortDesc: byNumberDesc('sort'),

        titleAsc: (a, b) =>
            normalize(getBrandTitle(a)).localeCompare(normalize(getBrandTitle(b)), locale.value)
            || safeNumber(a?.id) - safeNumber(b?.id),

        titleDesc: (a, b) =>
            normalize(getBrandTitle(b)).localeCompare(normalize(getBrandTitle(a)), locale.value)
            || safeNumber(b?.id) - safeNumber(a?.id),

        urlAsc: (a, b) =>
            normalize(a?.url).localeCompare(normalize(b?.url), locale.value)
            || safeNumber(a?.id) - safeNumber(b?.id),

        urlDesc: (a, b) =>
            normalize(b?.url).localeCompare(normalize(a?.url), locale.value)
            || safeNumber(b?.id) - safeNumber(a?.id),

        websiteAsc: (a, b) =>
            normalize(a?.website).localeCompare(normalize(b?.website), locale.value)
            || safeNumber(a?.id) - safeNumber(b?.id),

        websiteDesc: (a, b) =>
            normalize(b?.website).localeCompare(normalize(a?.website), locale.value)
            || safeNumber(b?.id) - safeNumber(a?.id),

        statusAsc: (a, b) =>
            normalize(a?.status).localeCompare(normalize(b?.status), locale.value)
            || safeNumber(a?.id) - safeNumber(b?.id),

        statusDesc: (a, b) =>
            normalize(b?.status).localeCompare(normalize(a?.status), locale.value)
            || safeNumber(b?.id) - safeNumber(a?.id),

        activityAsc: byNumberAsc('activity'),
        activityDesc: byNumberDesc('activity'),

        leftAsc: byNumberAsc('left'),
        leftDesc: byNumberDesc('left'),

        mainAsc: byNumberAsc('main'),
        mainDesc: byNumberDesc('main'),

        rightAsc: byNumberAsc('right'),
        rightDesc: byNumberDesc('right'),

        viewsAsc: byNumberAsc('views'),
        viewsDesc: byNumberDesc('views'),
        views: byNumberDesc('views'),

        imagesAsc: byNumberAsc('images_count'),
        imagesDesc: byNumberDesc('images_count'),

        moderationStatusAsc: (a, b) =>
            moderationNum(a?.moderation_status) - moderationNum(b?.moderation_status)
            || safeNumber(a?.id) - safeNumber(b?.id),

        moderationStatusDesc: (a, b) =>
            moderationNum(b?.moderation_status) - moderationNum(a?.moderation_status)
            || safeNumber(b?.id) - safeNumber(a?.id),

        ownerNameAsc: (a, b) =>
            normalize(getOwnerName(a)).localeCompare(normalize(getOwnerName(b)), locale.value)
            || safeNumber(a?.id) - safeNumber(b?.id),

        ownerNameDesc: (a, b) =>
            normalize(getOwnerName(b)).localeCompare(normalize(getOwnerName(a)), locale.value)
            || safeNumber(b?.id) - safeNumber(a?.id),

        ownerEmailAsc: (a, b) =>
            normalize(getOwnerEmail(a)).localeCompare(normalize(getOwnerEmail(b)), locale.value)
            || safeNumber(a?.id) - safeNumber(b?.id),

        ownerEmailDesc: (a, b) =>
            normalize(getOwnerEmail(b)).localeCompare(normalize(getOwnerEmail(a)), locale.value)
            || safeNumber(b?.id) - safeNumber(a?.id),

        publishedAtAsc: byDateAsc('published_at'),
        publishedAtDesc: byDateDesc('published_at'),

        showFromAtAsc: byDateAsc('show_from_at'),
        showFromAtDesc: byDateDesc('show_from_at'),

        showToAtAsc: byDateAsc('show_to_at'),
        showToAtDesc: byDateDesc('show_to_at'),

        createdAtAsc: byDateAsc('created_at'),
        createdAtDesc: byDateDesc('created_at'),
        dateAsc: byDateAsc('created_at'),
        dateDesc: byDateDesc('created_at'),

        updatedAtAsc: byDateAsc('updated_at'),
        updatedAtDesc: byDateDesc('updated_at'),
    }

    return sortMap[sortParam.value]
        ? list.sort(sortMap[sortParam.value])
        : list
}

/** Фильтрация брендов */
const filteredBrands = computed(() => {
    let filtered = localBrands.value || []
    const query = normalize(searchQuery.value)

    if (!query) {
        return sortBrands(filtered)
    }

    filtered = filtered.filter((brand) => {
        const values = [
            brand?.id,
            brand?.url,
            brand?.website,
            brand?.icon,
            brand?.status,
            brand?.moderation_note,
            brand?.views,
            getBrandTitle(brand),
            getBrandShort(brand),
            getBrandDescription(brand),
            getOwnerName(brand),
            getOwnerEmail(brand),
            brand?.moderator?.name,
            brand?.moderator?.email,
        ]

        return values.some((value) => normalize(value).includes(query))
    })

    return sortBrands(filtered)
})

/** Пагинация брендов */
const paginatedBrands = computed(() => {
    const perPage = Number(itemsPerPage.value || 10)
    const start = (currentPage.value - 1) * perPage

    return filteredBrands.value.slice(start, start + perPage)
})

/** Список для отображения */
const displayedBrands = computed(() => {
    return props.useServerProcessing
        ? brandsList.value
        : paginatedBrands.value
})

/** Сброс страницы при изменениях */
watch([itemsPerPage, searchQuery], () => {
    currentPage.value = 1
})

/** Выбранные бренды */
const selectedBrands = ref([])

/** Массовое выделение */
const toggleAll = (payload) => {
    const checked = payload?.checked ?? payload?.target?.checked ?? false
    const ids = payload?.ids ?? displayedBrands.value.map((brand) => brand.id)

    if (checked) {
        selectedBrands.value = [...new Set([...selectedBrands.value, ...ids])]
    } else {
        selectedBrands.value = selectedBrands.value.filter((id) => !ids.includes(id))
    }
}

/** Переключение выбора бренда */
const toggleSelectBrand = (brandId) => {
    const index = selectedBrands.value.indexOf(brandId)

    if (index > -1) {
        selectedBrands.value.splice(index, 1)
    } else {
        selectedBrands.value.push(brandId)
    }
}

/** Массовое обновление активности */
const bulkToggleActivity = (newActivity) => {
    if (!selectedBrands.value.length) {
        toast.warning('Выберите бренды для активации/деактивации.')
        return
    }

    const idsToUpdate = [...selectedBrands.value]

    router.put(
        route('admin.actions.marketBrands.bulkUpdateActivity'),
        { ids: idsToUpdate, activity: newActivity },
        {
            preserveScroll: true,
            preserveState: true,

            onSuccess: () => {
                localBrands.value = localBrands.value.map((brand) => {
                    return idsToUpdate.includes(brand.id)
                        ? { ...brand, activity: newActivity }
                        : brand
                })

                selectedBrands.value = []
                toast.success('Активность брендов массово обновлена.')
            },

            onError: (errors) => {
                const msg = errors?.ids || errors?.activity || errors?.general || 'Ошибка массового обновления активности.'
                toast.error(msg)
            },
        }
    )
}

/** Массовое обновление рекламной позиции */
const bulkToggleFlag = (field, newValue, routeName, successMessage) => {
    if (!selectedBrands.value.length) {
        toast.warning('Выберите бренды для массового действия.')
        return
    }

    const idsToUpdate = [...selectedBrands.value]

    router.put(
        route(routeName),
        { ids: idsToUpdate, [field]: newValue },
        {
            preserveScroll: true,
            preserveState: true,

            onSuccess: () => {
                localBrands.value = localBrands.value.map((brand) => {
                    return idsToUpdate.includes(brand.id)
                        ? { ...brand, [field]: newValue }
                        : brand
                })

                selectedBrands.value = []
                toast.success(successMessage)
            },

            onError: (errors) => {
                const msg = errors?.ids || errors?.[field] || errors?.general || 'Ошибка массового обновления.'
                toast.error(msg)
            },
        }
    )
}

/** Массовое удаление */
const bulkDelete = () => {
    if (!selectedBrands.value.length) {
        toast.warning('Выберите хотя бы один бренд для удаления.')
        return
    }

    if (!confirm('Вы уверены, что хотите удалить выбранные бренды?')) return

    router.delete(route('admin.actions.marketBrands.bulkDestroy'), {
        data: { ids: selectedBrands.value },
        preserveScroll: true,
        preserveState: false,

        onSuccess: () => {
            selectedBrands.value = []
            toast.success('Массовое удаление брендов успешно завершено.')
        },

        onError: (errors) => {
            const errorKey = Object.keys(errors || {})[0]
            toast.error(errors[errorKey] || 'Произошла ошибка при удалении брендов.')
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
        bulkToggleFlag('left', true, 'admin.actions.marketBrands.bulkUpdateLeft', 'Бренды добавлены в левую колонку.')
    } else if (action === 'noLeft') {
        bulkToggleFlag('left', false, 'admin.actions.marketBrands.bulkUpdateLeft', 'Бренды убраны из левой колонки.')
    } else if (action === 'main') {
        bulkToggleFlag('main', true, 'admin.actions.marketBrands.bulkUpdateMain', 'Бренды добавлены в главный блок.')
    } else if (action === 'noMain') {
        bulkToggleFlag('main', false, 'admin.actions.marketBrands.bulkUpdateMain', 'Бренды убраны из главного блока.')
    } else if (action === 'right') {
        bulkToggleFlag('right', true, 'admin.actions.marketBrands.bulkUpdateRight', 'Бренды добавлены в правую колонку.')
    } else if (action === 'noRight') {
        bulkToggleFlag('right', false, 'admin.actions.marketBrands.bulkUpdateRight', 'Бренды убраны из правой колонки.')
    } else if (action === 'delete') {
        bulkDelete()
    }

    event.target.value = ''
}

/** Модерация бренда */
const approveBrand = (brand, status = 1, note = '') => {
    if (!brand?.id) return

    router.put(
        route('admin.actions.marketBrands.approve', { marketBrand: brand.id }),
        {
            moderation_status: status,
            moderation_note: note,
        },
        {
            preserveScroll: true,
            preserveState: true,

            onSuccess: () => {
                patchLocalBrand(brand.id, (node) => {
                    node.moderation_status = status
                    node.is_approved = status === 1
                    node.moderation_note = note
                })

                toast.success(status === 1 ? 'Бренд одобрен.' : 'Бренд отклонён.')
            },

            onError: () => toast.error('Ошибка модерации бренда.'),
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
        route('admin.actions.marketBrands.updateSortBulk'),
        { items },
        {
            preserveScroll: true,
            preserveState: true,

            onSuccess: () => toast.success('Сортировка брендов обновлена.'),

            onError: (errors) => {
                console.error('Ошибка сортировки брендов:', errors)
                toast.error(errors.message || 'Ошибка обновления сортировки.')
            },
        }
    )
}
</script>

<template>
    <AdminLayout :title="t('marketBrands')">
        <template #header>
            <TitlePage>{{ t('marketBrands') }}</TitlePage>
        </template>

        <div class="px-2 py-2 w-full max-w-12xl mx-auto">
            <div
                class="p-4 bg-slate-50 dark:bg-slate-700
                       border border-blue-400 dark:border-blue-200
                       overflow-hidden shadow-md shadow-gray-500 dark:shadow-slate-400
                       bg-opacity-95 dark:bg-opacity-95"
            >
                <div class="sm:flex sm:justify-between sm:items-center mb-3 gap-3">
                    <DefaultButton :href="route('admin.marketBrands.create')">
                        {{ t('addMarketBrand') }}
                    </DefaultButton>

                    <ProcessingModeSwitcher
                        setting-key="adminMarketBrandsProcessingMode"
                        :mode="adminMarketBrandsProcessingMode"
                        :use-server-processing="useServerProcessing"
                        :total="brandsCount"
                    />
                </div>

                <SearchInput
                    v-if="brandsCount && !useServerProcessing"
                    v-model="searchQuery"
                />

                <ServerSearchInput
                    v-if="brandsCount && useServerProcessing"
                    v-model="searchQuery"
                />

                <div
                    v-if="brandsCount"
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
                        update-route="admin.settings.updateAdminCountMarketBrands"
                    />

                    <SortSelect
                        :sortParam="sortParam"
                        @update:sortParam="(val) => (sortParam = val)"
                    />
                </div>

                <div
                    v-if="brandsCount"
                    class="flex flex-col lg:flex-row items-center justify-between gap-3"
                >
                    <CountTable>{{ brandsCount }}</CountTable>

                    <BulkActionSelect
                        v-if="brandsCount"
                        @change="handleBulkAction"
                    />

                    <ToggleViewButton v-model:viewMode="viewMode" />
                </div>

                <div
                    v-if="brandsCount"
                    class="flex justify-center items-center flex-col md:flex-row mt-3"
                >
                    <Pagination
                        v-if="!useServerProcessing"
                        :current-page="currentPage"
                        :items-per-page="itemsPerPage"
                        :total-items="filteredBrands.length"
                        @update:currentPage="currentPage = $event"
                    />

                    <AdminServerPagination
                        v-else
                        :pagination="brands"
                    />
                </div>

                <BrandTable
                    v-if="viewMode === 'table'"
                    :brands="displayedBrands"
                    :selected-brands="selectedBrands"
                    :is-admin="isAdmin"
                    @toggle-left="toggleLeft"
                    @toggle-main="toggleMain"
                    @toggle-right="toggleRight"
                    @toggle-activity="toggleActivity"
                    @delete="confirmDelete"
                    @update-sort-order="handleSortOrderUpdate"
                    @toggle-select="toggleSelectBrand"
                    @toggle-all="toggleAll"
                    @approve="approveBrand"
                />

                <BrandCardGrid
                    v-else
                    :brands="displayedBrands"
                    :selected-brands="selectedBrands"
                    :is-admin="isAdmin"
                    @toggle-left="toggleLeft"
                    @toggle-main="toggleMain"
                    @toggle-right="toggleRight"
                    @toggle-activity="toggleActivity"
                    @delete="confirmDelete"
                    @update-sort-order="handleSortOrderUpdate"
                    @toggle-select="toggleSelectBrand"
                    @toggle-all="toggleAll"
                    @approve="approveBrand"
                />

                <div
                    v-if="brandsCount"
                    class="flex justify-center items-center flex-col md:flex-row mt-3"
                >
                    <Pagination
                        v-if="!useServerProcessing"
                        :current-page="currentPage"
                        :items-per-page="itemsPerPage"
                        :total-items="filteredBrands.length"
                        @update:currentPage="currentPage = $event"
                    />

                    <AdminServerPagination
                        v-else
                        :pagination="brands"
                    />
                </div>
            </div>
        </div>

        <DangerModal
            :show="showConfirmDeleteModal"
            @close="closeModal"
            :onCancel="closeModal"
            :onConfirm="deleteBrand"
            :cancelText="t('cancel')"
            :confirmText="t('yesDelete')"
        />
    </AdminLayout>
</template>
