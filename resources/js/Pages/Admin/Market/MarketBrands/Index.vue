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

import { computed, ref, watch } from 'vue'
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
    return roles.some(role => role?.name === 'admin')
})

/* =========================================================
 * HELPERS
 * ========================================================= */

/** Текущий перевод бренда */
const getBrandTranslation = brand => brand?.translation || {}

/** Название бренда */
const getBrandTitle = brand =>
    getBrandTranslation(brand)?.title || `ID: ${brand?.id}`

/** Имя владельца */
const getOwnerName = brand => brand?.owner?.name || ''

/** Email владельца */
const getOwnerEmail = brand => brand?.owner?.email || ''

/** Нормализация строки */
const normalize = value =>
    (value ?? '').toString().trim().toLowerCase()

/** Безопасное преобразование в число */
const safeNumber = value => {
    const number = Number(value)
    return Number.isFinite(number) ? number : 0
}

/** Безопасное преобразование даты */
const safeDate = value => {
    const time = new Date(value || 0).getTime()
    return Number.isFinite(time) ? time : 0
}

/** Нормализация статуса модерации */
const moderationNum = value => {
    const number = Number(value)
    return Number.isFinite(number) ? number : 0
}

/** ID DESC */
const byIdDesc = (a, b) =>
    safeNumber(b?.id) - safeNumber(a?.id)

/** Число ASC + ID DESC */
const byNumberAsc = field => (a, b) =>
    safeNumber(a?.[field]) - safeNumber(b?.[field])
    || byIdDesc(a, b)

/** Число DESC + ID DESC */
const byNumberDesc = field => (a, b) =>
    safeNumber(b?.[field]) - safeNumber(a?.[field])
    || byIdDesc(a, b)

/** Дата ASC + ID DESC */
const byDateAsc = field => (a, b) =>
    safeDate(a?.[field]) - safeDate(b?.[field])
    || byIdDesc(a, b)

/** Дата DESC + ID DESC */
const byDateDesc = field => (a, b) =>
    safeDate(b?.[field]) - safeDate(a?.[field])
    || byIdDesc(a, b)

/** Строка ASC + ID DESC */
const byStringAsc = getter => (a, b) =>
    normalize(getter(a)).localeCompare(
        normalize(getter(b)),
        locale.value
    ) || byIdDesc(a, b)

/** Строка DESC + ID DESC */
const byStringDesc = getter => (a, b) =>
    normalize(getter(b)).localeCompare(
        normalize(getter(a)),
        locale.value
    ) || byIdDesc(a, b)

/** Фильтр с последующей сортировкой ID DESC */
const filterWithIdDesc = (list, callback) =>
    list.filter(callback).sort(byIdDesc)

/* =========================================================
 * VIEW MODE
 * ========================================================= */

/** Режим отображения */
const viewMode = ref(
    localStorage.getItem('admin_view_mode_market_brands') || 'cards'
)

/** Сохранение режима отображения */
watch(viewMode, value => {
    localStorage.setItem(
        'admin_view_mode_market_brands',
        value
    )
})

/* =========================================================
 * ITEMS PER PAGE
 * ========================================================= */

/** Количество элементов на странице */
const itemsPerPage = ref(
    props.adminMarketBrandsPerPage || 6
)

/** Обновление количества элементов */
watch(itemsPerPage, newVal => {
    router.put(
        route('admin.settings.updateAdminCountMarketBrands'),
        { value: newVal },
        {
            preserveScroll: true,
            preserveState: true,

            onSuccess: () => {
                toast.info(
                    `Показ ${newVal} брендов на странице.`
                )
            },

            onError: errors => {
                toast.error(
                    errors.value
                    || 'Ошибка обновления кол-ва брендов.'
                )
            },
        }
    )
})

/* =========================================================
 * SORT
 * ========================================================= */

/** Текущая сортировка */
const sortParam = ref(
    props.sortParam
    || props.adminMarketBrandsDefaultSort
    || 'idDesc'
)

/** Обновление серверной настройки сортировки */
watch(sortParam, newVal => {
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
                            ...Object.fromEntries(
                                new URLSearchParams(
                                    window.location.search
                                )
                            ),
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

                toast.info(
                    'Сортировка брендов успешно изменена.'
                )
            },

            onError: errors => {
                toast.error(
                    errors.value
                    || 'Ошибка обновления сортировки брендов.'
                )
            },
        }
    )
})

/* =========================================================
 * SOURCE DATA
 * ========================================================= */

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

/** Локальная копия брендов */
const localBrands = ref([])

/** Синхронизация локального списка */
watch(
    brandsList,
    newVal => {
        localBrands.value = JSON.parse(
            JSON.stringify(newVal || [])
        )
    },
    {
        immediate: true,
        deep: true,
    }
)

/* =========================================================
 * DELETE
 * ========================================================= */

const showConfirmDeleteModal = ref(false)
const brandToDeleteId = ref(null)
const brandToDeleteTitle = ref('')

/** Подготовка удаления */
const confirmDelete = (brandOrId, title = null) => {
    if (
        brandOrId
        && typeof brandOrId === 'object'
    ) {
        brandToDeleteId.value = brandOrId.id
        brandToDeleteTitle.value =
            title || getBrandTitle(brandOrId)
    } else {
        brandToDeleteId.value = brandOrId
        brandToDeleteTitle.value =
            title || `ID: ${brandOrId}`
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
    if (brandToDeleteId.value === null) {
        return
    }

    const idToDelete = brandToDeleteId.value
    const titleToDelete = brandToDeleteTitle.value

    router.delete(
        route(
            'admin.marketBrands.destroy',
            { marketBrand: idToDelete }
        ),
        {
            preserveScroll: true,
            preserveState: false,

            onSuccess: () => {
                toast.success(
                    `Бренд "${titleToDelete || `ID: ${idToDelete}`}" удалён.`
                )
            },

            onError: errors => {
                const errorKey = Object.keys(
                    errors || {}
                )[0]

                const errorMsg =
                    errors.general
                    || errors[errorKey]
                    || 'Произошла ошибка при удалении.'

                toast.error(
                    `${errorMsg} (Бренд: ${
                        titleToDelete || `ID: ${idToDelete}`
                    })`
                )
            },

            onFinish: closeModal,
        }
    )
}

/* =========================================================
 * LOCAL PATCH
 * ========================================================= */

/** Локальное обновление бренда */
const patchLocalBrand = (brandId, callback) => {
    const index = localBrands.value.findIndex(
        brand => brand.id === brandId
    )

    if (index !== -1) {
        callback(localBrands.value[index])
    }
}

/* =========================================================
 * SINGLE FLAGS
 * ========================================================= */

/** Переключение активности */
const toggleActivity = brand => {
    const newActivity = !brand.activity
    const title = getBrandTitle(brand)
    const actionText = newActivity
        ? t('activated')
        : t('deactivated')

    router.put(
        route(
            'admin.actions.marketBrands.updateActivity',
            { marketBrand: brand.id }
        ),
        { activity: newActivity },
        {
            preserveScroll: true,
            preserveState: true,

            onSuccess: () => {
                patchLocalBrand(
                    brand.id,
                    node => {
                        node.activity = newActivity
                    }
                )

                toast.success(
                    `Бренд "${title}" ${actionText}.`
                )
            },

            onError: errors => {
                toast.error(
                    errors.activity
                    || errors.general
                    || `Ошибка изменения активности для "${title}".`
                )
            },
        }
    )
}

/** Переключение позиции left */
const toggleLeft = brand => {
    const newLeft = !brand.left
    const title = getBrandTitle(brand)

    router.put(
        route(
            'admin.actions.marketBrands.updateLeft',
            { marketBrand: brand.id }
        ),
        { left: newLeft },
        {
            preserveScroll: true,
            preserveState: true,

            onSuccess: () => {
                patchLocalBrand(
                    brand.id,
                    node => {
                        node.left = newLeft
                    }
                )

                toast.success(
                    `Позиция left для бренда "${title}" обновлена.`
                )
            },

            onError: errors => {
                toast.error(
                    errors.left
                    || errors.general
                    || `Ошибка изменения left для "${title}".`
                )
            },
        }
    )
}

/** Переключение позиции main */
const toggleMain = brand => {
    const newMain = !brand.main
    const title = getBrandTitle(brand)

    router.put(
        route(
            'admin.actions.marketBrands.updateMain',
            { marketBrand: brand.id }
        ),
        { main: newMain },
        {
            preserveScroll: true,
            preserveState: true,

            onSuccess: () => {
                patchLocalBrand(
                    brand.id,
                    node => {
                        node.main = newMain
                    }
                )

                toast.success(
                    `Позиция main для бренда "${title}" обновлена.`
                )
            },

            onError: errors => {
                toast.error(
                    errors.main
                    || errors.general
                    || `Ошибка изменения main для "${title}".`
                )
            },
        }
    )
}

/** Переключение позиции right */
const toggleRight = brand => {
    const newRight = !brand.right
    const title = getBrandTitle(brand)

    router.put(
        route(
            'admin.actions.marketBrands.updateRight',
            { marketBrand: brand.id }
        ),
        { right: newRight },
        {
            preserveScroll: true,
            preserveState: true,

            onSuccess: () => {
                patchLocalBrand(
                    brand.id,
                    node => {
                        node.right = newRight
                    }
                )

                toast.success(
                    `Позиция right для бренда "${title}" обновлена.`
                )
            },

            onError: errors => {
                toast.error(
                    errors.right
                    || errors.general
                    || `Ошибка изменения right для "${title}".`
                )
            },
        }
    )
}

/* =========================================================
 * SEARCH / SORT / PAGINATION
 * ========================================================= */

const searchQuery = ref(props.search || '')
const currentPage = ref(1)

/** Сортировка брендов */
const sortBrands = brands => {
    const list = (brands || []).slice()

    /* Фильтры активности */
    if (sortParam.value === 'activity') {
        return filterWithIdDesc(
            list,
            brand => Boolean(brand?.activity)
        )
    }

    if (sortParam.value === 'inactive') {
        return filterWithIdDesc(
            list,
            brand => !brand?.activity
        )
    }

    /* Левая зона */
    if (sortParam.value === 'left') {
        return filterWithIdDesc(
            list,
            brand => Boolean(brand?.left)
        )
    }

    if (sortParam.value === 'noLeft') {
        return filterWithIdDesc(
            list,
            brand => !brand?.left
        )
    }

    /* Главная зона */
    if (sortParam.value === 'main') {
        return filterWithIdDesc(
            list,
            brand => Boolean(brand?.main)
        )
    }

    if (sortParam.value === 'noMain') {
        return filterWithIdDesc(
            list,
            brand => !brand?.main
        )
    }

    /* Правая зона */
    if (sortParam.value === 'right') {
        return filterWithIdDesc(
            list,
            brand => Boolean(brand?.right)
        )
    }

    if (sortParam.value === 'noRight') {
        return filterWithIdDesc(
            list,
            brand => !brand?.right
        )
    }

    /* Статусы */
    if (sortParam.value === 'statusDraft') {
        return filterWithIdDesc(
            list,
            brand => brand?.status === 'draft'
        )
    }

    if (sortParam.value === 'statusPublished') {
        return filterWithIdDesc(
            list,
            brand => brand?.status === 'published'
        )
    }

    if (sortParam.value === 'statusArchived') {
        return filterWithIdDesc(
            list,
            brand => brand?.status === 'archived'
        )
    }

    /* Модерация */
    if (sortParam.value === 'moderationPending') {
        return filterWithIdDesc(
            list,
            brand =>
                moderationNum(
                    brand?.moderation_status
                ) === 0
        )
    }

    if (sortParam.value === 'moderationApproved') {
        return filterWithIdDesc(
            list,
            brand =>
                moderationNum(
                    brand?.moderation_status
                ) === 1
        )
    }

    if (sortParam.value === 'moderationRejected') {
        return filterWithIdDesc(
            list,
            brand =>
                moderationNum(
                    brand?.moderation_status
                ) === 2
        )
    }

    const sortMap = {
        /* ID */
        idAsc: (a, b) =>
            safeNumber(a?.id) - safeNumber(b?.id),

        idDesc: byIdDesc,

        /* Порядок */
        sortAsc: byNumberAsc('sort'),
        sortDesc: byNumberDesc('sort'),

        /* Название */
        titleAsc: byStringAsc(
            getBrandTitle
        ),

        titleDesc: byStringDesc(
            getBrandTitle
        ),

        /* URL */
        urlAsc: byStringAsc(
            brand => brand?.url
        ),

        urlDesc: byStringDesc(
            brand => brand?.url
        ),

        /* Website */
        websiteAsc: byStringAsc(
            brand => brand?.website
        ),

        websiteDesc: byStringDesc(
            brand => brand?.website
        ),

        /* Активность */
        activityAsc: byNumberAsc(
            'activity'
        ),

        activityDesc: byNumberDesc(
            'activity'
        ),

        /* Левая зона */
        leftAsc: byNumberAsc(
            'left'
        ),

        leftDesc: byNumberDesc(
            'left'
        ),

        /* Главная зона */
        mainAsc: byNumberAsc(
            'main'
        ),

        mainDesc: byNumberDesc(
            'main'
        ),

        /* Правая зона */
        rightAsc: byNumberAsc(
            'right'
        ),

        rightDesc: byNumberDesc(
            'right'
        ),

        /* Просмотры */
        viewsAsc: byNumberAsc(
            'views'
        ),

        viewsDesc: byNumberDesc(
            'views'
        ),

        /* Изображения */
        imagesAsc: byNumberAsc(
            'images_count'
        ),

        imagesDesc: byNumberDesc(
            'images_count'
        ),

        /* Статус */
        statusAsc: byStringAsc(
            brand => brand?.status
        ),

        statusDesc: byStringDesc(
            brand => brand?.status
        ),

        /* Модерация */
        moderationStatusAsc: (a, b) =>
            moderationNum(
                a?.moderation_status
            )
            - moderationNum(
                b?.moderation_status
            )
            || byIdDesc(a, b),

        moderationStatusDesc: (a, b) =>
            moderationNum(
                b?.moderation_status
            )
            - moderationNum(
                a?.moderation_status
            )
            || byIdDesc(a, b),

        /* Владелец */
        ownerNameAsc: byStringAsc(
            getOwnerName
        ),

        ownerNameDesc: byStringDesc(
            getOwnerName
        ),

        ownerEmailAsc: byStringAsc(
            getOwnerEmail
        ),

        ownerEmailDesc: byStringDesc(
            getOwnerEmail
        ),

        /* Даты */
        publishedAtAsc: byDateAsc(
            'published_at'
        ),

        publishedAtDesc: byDateDesc(
            'published_at'
        ),

        showFromAtAsc: byDateAsc(
            'show_from_at'
        ),

        showFromAtDesc: byDateDesc(
            'show_from_at'
        ),

        showToAtAsc: byDateAsc(
            'show_to_at'
        ),

        showToAtDesc: byDateDesc(
            'show_to_at'
        ),

        createdAtAsc: byDateAsc(
            'created_at'
        ),

        createdAtDesc: byDateDesc(
            'created_at'
        ),

        dateAsc: byDateAsc(
            'created_at'
        ),

        dateDesc: byDateDesc(
            'created_at'
        ),

        updatedAtAsc: byDateAsc(
            'updated_at'
        ),

        updatedAtDesc: byDateDesc(
            'updated_at'
        ),
    }

    const comparator =
        sortMap[sortParam.value]

    return comparator
        ? list.sort(comparator)
        : list.sort(byIdDesc)
}

/**
 * Фильтрация брендов.
 *
 * Полностью повторяет MarketBrand::scopeSearch():
 * - url
 * - website
 * - icon
 * - status
 * - moderation_note
 * - translation.title
 * - translation.subtitle
 * - translation.short
 * - translation.description
 * - owner.name
 * - owner.email
 */
const filteredBrands = computed(() => {
    const query = normalize(
        searchQuery.value
    )

    let filtered = localBrands.value || []

    if (query) {
        filtered = filtered.filter(brand => {
            const translation =
                getBrandTranslation(brand)

            const values = [
                brand?.url,
                brand?.website,
                brand?.icon,
                brand?.status,
                brand?.moderation_note,

                translation?.title,
                translation?.subtitle,
                translation?.short,
                translation?.description,

                getOwnerName(brand),
                getOwnerEmail(brand),
            ]

            return values.some(
                value =>
                    normalize(value).includes(query)
            )
        })
    }

    return sortBrands(filtered)
})

/** Локальная пагинация */
const paginatedBrands = computed(() => {
    const perPage = Number(
        itemsPerPage.value || 10
    )

    const start =
        (currentPage.value - 1)
        * perPage

    return filteredBrands.value.slice(
        start,
        start + perPage
    )
})

/** Список для отображения */
const displayedBrands = computed(() => {
    return props.useServerProcessing
        ? brandsList.value
        : paginatedBrands.value
})

/** Сброс локальной страницы при изменении фильтрации */
watch(
    [
        itemsPerPage,
        searchQuery,
        sortParam,
    ],
    () => {
        currentPage.value = 1
    }
)

/* =========================================================
 * SELECTION
 * ========================================================= */

/** Выбранные бренды */
const selectedBrands = ref([])

/** Массовое выделение */
const toggleAll = payload => {
    const checked = Boolean(
        payload?.checked
        ?? payload?.target?.checked
        ?? false
    )

    const ids =
        payload?.ids
        ?? displayedBrands.value.map(
            brand => brand.id
        )

    if (checked) {
        selectedBrands.value = [
            ...new Set([
                ...selectedBrands.value,
                ...ids,
            ]),
        ]
    } else {
        selectedBrands.value =
            selectedBrands.value.filter(
                id => !ids.includes(id)
            )
    }
}

/** Переключение одного бренда */
const toggleSelectBrand = brandId => {
    const index =
        selectedBrands.value.indexOf(
            brandId
        )

    if (index > -1) {
        selectedBrands.value.splice(
            index,
            1
        )
    } else {
        selectedBrands.value.push(
            brandId
        )
    }
}

/* =========================================================
 * BULK ACTIONS
 * ========================================================= */

/** Массовое обновление активности */
const bulkToggleActivity = newActivity => {
    if (!selectedBrands.value.length) {
        toast.warning(
            'Выберите бренды для активации/деактивации.'
        )
        return
    }

    const idsToUpdate = [
        ...selectedBrands.value,
    ]

    router.put(
        route(
            'admin.actions.marketBrands.bulkUpdateActivity'
        ),
        {
            ids: idsToUpdate,
            activity: newActivity,
        },
        {
            preserveScroll: true,
            preserveState: true,

            onSuccess: () => {
                localBrands.value =
                    localBrands.value.map(
                        brand =>
                            idsToUpdate.includes(
                                brand.id
                            )
                                ? {
                                    ...brand,
                                    activity: newActivity,
                                }
                                : brand
                    )

                selectedBrands.value = []

                toast.success(
                    'Активность брендов массово обновлена.'
                )
            },

            onError: errors => {
                toast.error(
                    errors?.ids
                    || errors?.activity
                    || errors?.general
                    || 'Ошибка массового обновления активности.'
                )
            },
        }
    )
}

/** Массовое обновление рекламной позиции */
const bulkToggleFlag = (
    field,
    newValue,
    routeName,
    successMessage
) => {
    if (!selectedBrands.value.length) {
        toast.warning(
            'Выберите бренды для массового действия.'
        )
        return
    }

    const idsToUpdate = [
        ...selectedBrands.value,
    ]

    router.put(
        route(routeName),
        {
            ids: idsToUpdate,
            [field]: newValue,
        },
        {
            preserveScroll: true,
            preserveState: true,

            onSuccess: () => {
                localBrands.value =
                    localBrands.value.map(
                        brand =>
                            idsToUpdate.includes(
                                brand.id
                            )
                                ? {
                                    ...brand,
                                    [field]: newValue,
                                }
                                : brand
                    )

                selectedBrands.value = []

                toast.success(
                    successMessage
                )
            },

            onError: errors => {
                toast.error(
                    errors?.ids
                    || errors?.[field]
                    || errors?.general
                    || 'Ошибка массового обновления.'
                )
            },
        }
    )
}

/** Массовое удаление */
const bulkDelete = () => {
    if (!selectedBrands.value.length) {
        toast.warning(
            'Выберите хотя бы один бренд для удаления.'
        )
        return
    }

    if (
        !confirm(
            'Вы уверены, что хотите удалить выбранные бренды?'
        )
    ) {
        return
    }

    router.delete(
        route(
            'admin.actions.marketBrands.bulkDestroy'
        ),
        {
            data: {
                ids: selectedBrands.value,
            },

            preserveScroll: true,
            preserveState: false,

            onSuccess: () => {
                selectedBrands.value = []

                toast.success(
                    'Массовое удаление брендов успешно завершено.'
                )
            },

            onError: errors => {
                const errorKey =
                    Object.keys(
                        errors || {}
                    )[0]

                toast.error(
                    errors[errorKey]
                    || 'Произошла ошибка при удалении брендов.'
                )
            },
        }
    )
}

/** Обработка массовых действий */
const handleBulkAction = event => {
    const action =
        event?.target?.value || ''

    switch (action) {
        case 'selectAll':
            toggleAll({
                checked: true,
                ids: displayedBrands.value.map(
                    brand => brand.id
                ),
            })
            break

        case 'deselectAll':
            toggleAll({
                checked: false,
                ids: displayedBrands.value.map(
                    brand => brand.id
                ),
            })
            break

        case 'activate':
            bulkToggleActivity(true)
            break

        case 'deactivate':
            bulkToggleActivity(false)
            break

        case 'left':
            bulkToggleFlag(
                'left',
                true,
                'admin.actions.marketBrands.bulkUpdateLeft',
                'Бренды добавлены в левую колонку.'
            )
            break

        case 'noLeft':
            bulkToggleFlag(
                'left',
                false,
                'admin.actions.marketBrands.bulkUpdateLeft',
                'Бренды убраны из левой колонки.'
            )
            break

        case 'main':
            bulkToggleFlag(
                'main',
                true,
                'admin.actions.marketBrands.bulkUpdateMain',
                'Бренды добавлены в главный блок.'
            )
            break

        case 'noMain':
            bulkToggleFlag(
                'main',
                false,
                'admin.actions.marketBrands.bulkUpdateMain',
                'Бренды убраны из главного блока.'
            )
            break

        case 'right':
            bulkToggleFlag(
                'right',
                true,
                'admin.actions.marketBrands.bulkUpdateRight',
                'Бренды добавлены в правую колонку.'
            )
            break

        case 'noRight':
            bulkToggleFlag(
                'right',
                false,
                'admin.actions.marketBrands.bulkUpdateRight',
                'Бренды убраны из правой колонки.'
            )
            break

        case 'delete':
            bulkDelete()
            break
    }

    if (event?.target) {
        event.target.value = ''
    }
}

/* =========================================================
 * MODERATION
 * ========================================================= */

/** Модерация бренда */
const approveBrand = (
    brand,
    status = 1,
    note = ''
) => {
    if (!brand?.id) {
        return
    }

    router.put(
        route(
            'admin.actions.marketBrands.approve',
            { marketBrand: brand.id }
        ),
        {
            moderation_status: status,
            moderation_note: note,
        },
        {
            preserveScroll: true,
            preserveState: true,

            onSuccess: () => {
                patchLocalBrand(
                    brand.id,
                    node => {
                        node.moderation_status =
                            status

                        node.is_pending =
                            status === 0

                        node.is_approved =
                            status === 1

                        node.is_rejected =
                            status === 2

                        node.moderation_note =
                            note
                    }
                )

                toast.success(
                    status === 1
                        ? 'Бренд одобрен.'
                        : status === 2
                            ? 'Бренд отклонён.'
                            : 'Бренд отправлен на модерацию.'
                )
            },

            onError: () => {
                toast.error(
                    'Ошибка модерации бренда.'
                )
            },
        }
    )
}

/* =========================================================
 * DRAG & DROP
 * ========================================================= */

/** Обновление порядка сортировки */
const handleSortOrderUpdate = newOrderIds => {
    const items = (
        newOrderIds || []
    ).map(
        (id, index) => ({
            id,
            sort: index,
        })
    )

    if (!items.length) {
        return
    }

    router.put(
        route(
            'admin.actions.marketBrands.updateSortBulk'
        ),
        { items },
        {
            preserveScroll: true,
            preserveState: true,

            onSuccess: () => {
                items.forEach(item => {
                    patchLocalBrand(
                        item.id,
                        brand => {
                            brand.sort =
                                item.sort
                        }
                    )
                })

                toast.success(
                    'Сортировка брендов обновлена.'
                )
            },

            onError: errors => {
                console.error(
                    'Ошибка сортировки брендов:',
                    errors
                )

                toast.error(
                    errors.message
                    || errors.general
                    || 'Ошибка обновления сортировки.'
                )
            },
        }
    )
}
</script>

<template>
    <AdminLayout :title="t('marketBrands')">
        <template #header>
            <TitlePage>
                {{ t('marketBrands') }}
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
                    <DefaultButton
                        :href="route('admin.marketBrands.create')"
                    >
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
                        @update:sortParam="sortParam = $event"
                    />
                </div>

                <div
                    v-if="brandsCount"
                    class="flex flex-col lg:flex-row items-center justify-between gap-3"
                >
                    <CountTable>
                        {{ brandsCount }}
                    </CountTable>

                    <BulkActionSelect
                        @change="handleBulkAction"
                    />

                    <ToggleViewButton
                        v-model:viewMode="viewMode"
                    />
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
            :onCancel="closeModal"
            :onConfirm="deleteBrand"
            :cancelText="t('cancel')"
            :confirmText="t('yesDelete')"
            @close="closeModal"
        />
    </AdminLayout>
</template>
