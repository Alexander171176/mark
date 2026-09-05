<script setup>
/**
 * @version PulsarCMS 1.0
 * @author Александр Косолапов
 *
 * Список вариантов товаров маркетплейса.
 *
 * Возможности:
 * - frontend | server | auto;
 * - локальный и серверный поиск;
 * - локальная и серверная пагинация;
 * - сортировка;
 * - таблица и карточки;
 * - массовые действия;
 * - модерация;
 * - активность;
 * - drag-and-drop сортировка.
 */

import { computed, defineProps, ref, watch } from 'vue'
import { router, usePage } from '@inertiajs/vue3'
import { useI18n } from 'vue-i18n'
import { useToast } from 'vue-toastification'

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

import BulkActionSelect from '@/Components/Admin/Market/MarketProductVariant/Select/BulkActionSelect.vue'
import SortSelect from '@/Components/Admin/Market/MarketProductVariant/Sort/SortSelect.vue'

import VariantTable from '@/Components/Admin/Market/MarketProductVariant/Table/VariantTable.vue'
import VariantCardGrid from '@/Components/Admin/Market/MarketProductVariant/View/VariantCardGrid.vue'

/* ======================== Services ======================== */

const { t, locale } = useI18n()
const toast = useToast()
const page = usePage()

/* ======================== Props ======================== */

const props = defineProps({
    currentLocale: {
        type: String,
        default: '',
    },

    availableLocales: {
        type: Array,
        default: () => [],
    },

    adminMarketProductVariantsProcessingMode: {
        type: String,
        default: 'frontend',
    },

    useServerProcessing: {
        type: Boolean,
        default: false,
    },

    adminMarketProductVariantsPerPage: {
        type: Number,
        default: 10,
    },

    adminMarketProductVariantsDefaultSort: {
        type: String,
        default: 'idDesc',
    },

    variants: {
        type: [Array, Object],
        default: () => [],
    },

    variantsCount: {
        type: Number,
        default: 0,
    },

    sortParam: {
        type: String,
        default: '',
    },

    search: {
        type: String,
        default: '',
    },

    filters: {
        type: Object,
        default: () => ({}),
    },

    products: {
        type: Array,
        default: () => [],
    },

    currentProductId: {
        type: Number,
        default: null,
    },

    error: {
        type: String,
        default: '',
    },

    errors: {
        type: Object,
        default: () => ({}),
    },
})

/* ======================== Current user ======================== */

/** Текущий пользователь является администратором */
const isAdmin = computed(() => {
    const roles = page.props?.auth?.user?.roles || []

    return roles.some((role) => role?.name === 'admin')
})

/* ======================== Variant helpers ======================== */

/** Текущий перевод варианта */
const getVariantTranslation = (variant) => {
    return variant?.translation || {}
}

/** Название варианта */
const getVariantTitle = (variant) => {
    return variant?.display_title || getVariantTranslation(variant)?.title || variant?.code || variant?.sku || `ID: ${variant?.id}`
}

/** Название родительского товара */
const getProductTitle = (variant) => {
    return variant?.product?.translation?.title || `ID: ${variant?.market_product_id}`
}

/* ======================== Normalization ======================== */

/** Нормализация строки */
const normalize = (value) => {
    return (value ?? '').toString().trim().toLowerCase()
}

/** Безопасное число */
const safeNumber = (value) => {
    const number = Number(value)

    return Number.isFinite(number) ? number : 0
}

/** Безопасная дата */
const safeDate = (value) => {
    const time = new Date(value || 0).getTime()

    return Number.isFinite(time) ? time : 0
}

/** Статус модерации в виде числа */
const moderationNum = (value) => {
    const number = Number(value)

    return Number.isFinite(number) ? number : 0
}

/* ======================== Product filter ======================== */

/** Выбранный родительский товар */
const selectedProductId = ref(
    props.currentProductId ?? props.filters?.market_product_id ?? null
)

/** Доступные товары */
const productOptions = computed(() => props.products || [])

/** Название товара в селекте */
const productOptionLabel = (product) => {
    if (!product) {
        return ''
    }

    const title = product.title || product.code || product.sku || `ID: ${product.id}`
    const variantsCount = Number(product.variants_count || 0)

    return `[ID: ${product.id}] ${title} — ${t('variants')}: ${variantsCount}`
}

/** Перезагрузка списка с выбранным товаром */
const reloadWithProductFilter = () => {
    const params = {
        ...Object.fromEntries(new URLSearchParams(window.location.search)),
        page: undefined,
    }

    if (selectedProductId.value) {
        params.market_product_id = selectedProductId.value
    } else {
        delete params.market_product_id
    }

    router.get(
        route('admin.marketProductVariants.index'),
        params,
        {
            preserveScroll: true,
            preserveState: false,
            replace: true,
        }
    )
}

/** Изменение фильтра товара */
const handleProductFilterChange = () => {
    selectedVariants.value = []
    currentPage.value = 1
    reloadWithProductFilter()
}

/* ======================== View mode ======================== */

/** Режим отображения */
const viewMode = ref(localStorage.getItem('admin_view_mode_market_product_variants') || 'cards')

/** Сохранение режима отображения */
watch(viewMode, (value) => {
    localStorage.setItem('admin_view_mode_market_product_variants', value)
})

/* ======================== Settings ======================== */

/** Количество элементов на странице */
const itemsPerPage = ref(props.adminMarketProductVariantsPerPage || 10)

/** Сохранение количества элементов */
watch(itemsPerPage, (newValue, oldValue) => {
    if (newValue === oldValue) {
        return
    }

    router.put(
        route('admin.settings.updateAdminCountMarketProductVariants'),
        {
            value: newValue,
        },
        {
            preserveScroll: true,
            preserveState: true,

            onSuccess: () => {
                toast.info(`Показ ${newValue} вариантов на странице.`)
            },

            onError: (errors) => {
                toast.error(errors?.value || 'Ошибка обновления количества вариантов.')
            },
        }
    )
})

/** Текущая сортировка */
const sortParam = ref(props.sortParam || props.adminMarketProductVariantsDefaultSort || 'idDesc')

/** Сохранение сортировки */
watch(sortParam, (newValue, oldValue) => {
    if (newValue === oldValue) {
        return
    }

    router.put(
        route('admin.settings.updateAdminSortMarketProductVariants'),
        {
            value: newValue,
        },
        {
            preserveScroll: true,
            preserveState: true,

            onSuccess: () => {
                if (props.useServerProcessing) {
                    router.get(
                        window.location.pathname,
                        {
                            ...Object.fromEntries(new URLSearchParams(window.location.search)),
                            sort: newValue || undefined,
                            page: undefined,
                        },
                        {
                            preserveScroll: true,
                            preserveState: false,
                            replace: true,
                        }
                    )
                }

                toast.info('Сортировка вариантов товаров успешно изменена.')
            },

            onError: (errors) => {
                toast.error(errors?.value || 'Ошибка обновления сортировки вариантов товаров.')
            },
        }
    )
})

/* ======================== Variant collection ======================== */

/** Нормализованный список вариантов */
const variantsList = computed(() => {
    if (Array.isArray(props.variants)) {
        return props.variants
    }

    if (Array.isArray(props.variants?.data)) {
        return props.variants.data
    }

    if (Array.isArray(props.variants?.data?.data)) {
        return props.variants.data.data
    }

    if (Array.isArray(props.variants?.resource)) {
        return props.variants.resource
    }

    return []
})

/** Локальная копия вариантов */
const localVariants = ref([])

/** Синхронизация props с локальной копией */
watch(
    variantsList,
    (newValue) => {
        localVariants.value = JSON.parse(JSON.stringify(newValue || []))
    },
    {
        immediate: true,
        deep: true,
    }
)

/* ======================== Delete modal ======================== */

const showConfirmDeleteModal = ref(false)

const variantToDeleteId = ref(null)
const variantToDeleteTitle = ref('')

/** Открытие подтверждения удаления */
const confirmDelete = (variantOrId, title = null) => {
    if (typeof variantOrId === 'object') {
        variantToDeleteId.value = variantOrId?.id
        variantToDeleteTitle.value = title || getVariantTitle(variantOrId)
    } else {
        variantToDeleteId.value = variantOrId
        variantToDeleteTitle.value = title || `ID: ${variantOrId}`
    }

    showConfirmDeleteModal.value = true
}

/** Закрытие подтверждения */
const closeModal = () => {
    showConfirmDeleteModal.value = false
    variantToDeleteId.value = null
    variantToDeleteTitle.value = ''
}

/** Удаление одного варианта */
const deleteVariant = () => {
    if (variantToDeleteId.value === null) {
        return
    }

    const id = variantToDeleteId.value
    const title = variantToDeleteTitle.value

    router.delete(
        route('admin.marketProductVariants.destroy', {
            marketProductVariant: id,
        }),
        {
            preserveScroll: true,
            preserveState: false,

            onSuccess: () => {
                toast.success(`Вариант товара "${title || `ID: ${id}`}" удалён.`)
            },

            onError: (errors) => {
                const firstKey = Object.keys(errors || {})[0]
                const message = errors?.general || errors?.[firstKey] || 'Ошибка при удалении варианта товара.'

                toast.error(`${message} (Вариант: ${title || `ID: ${id}`})`)
            },

            onFinish: closeModal,
        }
    )
}

/* ======================== Local patch ======================== */

/** Локальное обновление варианта */
const patchLocalVariant = (variantId, callback) => {
    const index = localVariants.value.findIndex((variant) => Number(variant.id) === Number(variantId))

    if (index === -1) {
        return
    }

    callback(localVariants.value[index])
}

/* ======================== Single actions ======================== */

/** Переключение активности */
const toggleActivity = (variant) => {
    if (!variant?.id) {
        return
    }

    const activity = !variant.activity
    const title = getVariantTitle(variant)

    router.put(
        route('admin.actions.marketProductVariants.updateActivity', {
            marketProductVariant: variant.id,
        }),
        {
            activity,
        },
        {
            preserveScroll: true,
            preserveState: true,

            onSuccess: () => {
                patchLocalVariant(variant.id, (item) => {
                    item.activity = activity
                    item.is_active = activity
                })

                toast.success(
                    activity
                        ? `Вариант товара "${title}" активирован.`
                        : `Вариант товара "${title}" деактивирован.`
                )
            },

            onError: (errors) => {
                toast.error(errors?.activity || errors?.general || `Ошибка изменения активности варианта "${title}".`)
            },
        }
    )
}

/** Модерация варианта */
const approveVariant = (variant, status = 1, note = '') => {
    if (!variant?.id || !isAdmin.value) {
        return
    }

    router.put(
        route('admin.actions.marketProductVariants.approve', {
            marketProductVariant: variant.id,
        }),
        {
            moderation_status: status,
            moderation_note: note,
        },
        {
            preserveScroll: true,
            preserveState: true,

            onSuccess: () => {
                patchLocalVariant(variant.id, (item) => {
                    item.moderation_status = status
                    item.is_pending = Number(status) === 0
                    item.is_approved = Number(status) === 1
                    item.is_rejected = Number(status) === 2
                    item.moderation_note = note
                })

                if (Number(status) === 1) {
                    toast.success('Вариант товара одобрен.')
                } else if (Number(status) === 2) {
                    toast.success('Вариант товара отклонён.')
                } else {
                    toast.success('Вариант товара возвращён на модерацию.')
                }
            },

            onError: (errors) => {
                toast.error(errors?.moderation_status || errors?.general || 'Ошибка модерации варианта товара.')
            },
        }
    )
}

/**
 * Назначить вариант основным.
 */
const makeDefaultVariant = (variant) => {
    if (!variant?.id || variant.is_default) {
        return
    }

    router.put(
        route('admin.actions.marketProductVariants.makeDefault', {
            marketProductVariant: variant.id,
        }),
        {},
        {
            preserveScroll: true,
            preserveState: true,

            onSuccess: () => {
                localVariants.value.forEach((item) => {
                    if (Number(item.market_product_id) !== Number(variant.market_product_id)) {
                        return
                    }

                    item.is_default = Number(item.id) === Number(variant.id)
                })

                toast.success(
                    `Вариант «${getVariantTitle(variant)}» назначен основным.`
                )
            },

            onError: (errors) => {
                toast.error(
                    errors?.general
                    || 'Ошибка назначения основного варианта товара.'
                )
            },
        }
    )
}

/* ======================== Frontend filtering ======================== */

const searchQuery = ref(props.search || '')
const currentPage = ref(1)

/**
 * Проверка совпадения варианта с поиском.
 *
 * Контракт полностью соответствует MarketProductVariant::scopeSearch():
 * - code, sku, vendor_code, barcode;
 * - status, moderation_note;
 * - текущий перевод варианта: title, subtitle, short, description;
 * - текущий перевод родительского товара: title;
 * - moderator: name, email.
 */
const variantMatchesSearch = (variant, query) => {
    if (!query) {
        return true
    }

    const translation = getVariantTranslation(variant)

    const searchValues = [
        variant?.code,
        variant?.sku,
        variant?.vendor_code,
        variant?.barcode,
        variant?.status,
        variant?.moderation_note,

        translation?.title,
        translation?.subtitle,
        translation?.short,
        translation?.description,

        variant?.product?.translation?.title,

        variant?.moderator?.name,
        variant?.moderator?.email,
    ]

    return searchValues.some((value) => normalize(value).includes(query))
}

/** Отфильтрованные варианты */
const filteredVariants = computed(() => {
    if (props.useServerProcessing) {
        return localVariants.value
    }

    const query = normalize(searchQuery.value)

    return localVariants.value.filter((variant) => variantMatchesSearch(variant, query))
})

/* ======================== Frontend sorting ======================== */

/** Сравнение строк */
const compareText = (left, right) => {
    return normalize(left).localeCompare(normalize(right), locale.value)
}

/** Сортировка чисел по возрастанию */
const byNumberAsc = (field) => (a, b) => {
    return safeNumber(a?.[field]) - safeNumber(b?.[field]) || safeNumber(b?.id) - safeNumber(a?.id)
}

/** Сортировка чисел по убыванию */
const byNumberDesc = (field) => (a, b) => {
    return safeNumber(b?.[field]) - safeNumber(a?.[field]) || safeNumber(b?.id) - safeNumber(a?.id)
}

/** Сортировка строк по возрастанию */
const byStringAsc = (field) => (a, b) => {
    return compareText(a?.[field], b?.[field]) || safeNumber(b?.id) - safeNumber(a?.id)
}

/** Сортировка строк по убыванию */
const byStringDesc = (field) => (a, b) => {
    return compareText(b?.[field], a?.[field]) || safeNumber(b?.id) - safeNumber(a?.id)
}

/** Сортировка дат по возрастанию */
const byDateAsc = (field) => (a, b) => {
    return safeDate(a?.[field]) - safeDate(b?.[field]) || safeNumber(b?.id) - safeNumber(a?.id)
}

/** Сортировка дат по убыванию */
const byDateDesc = (field) => (a, b) => {
    return safeDate(b?.[field]) - safeDate(a?.[field]) || safeNumber(b?.id) - safeNumber(a?.id)
}

/** Сортировка вариантов */
const sortedVariants = computed(() => {
    const list = [...filteredVariants.value]

    /** Фильтры активности */
    if (sortParam.value === 'activity') {
        return list
            .filter((variant) => Boolean(variant.activity))
            .sort((a, b) => safeNumber(b?.id) - safeNumber(a?.id))
    }

    if (sortParam.value === 'inactive') {
        return list
            .filter((variant) => !variant.activity)
            .sort((a, b) => safeNumber(b?.id) - safeNumber(a?.id))
    }

    /**
     * Фильтры наличия.
     *
     * Вариант находится в наличии только тогда, когда:
     * - in_stock = true;
     * - quantity > 0.
     *
     * Это соответствует scopeInStock()
     * и scopeOutOfStock() модели.
     */
    if (sortParam.value === 'inStock') {
        return list
            .filter((variant) => {
                return Boolean(variant.in_stock)
                    && safeNumber(variant.quantity) > 0
            })
            .sort((a, b) => safeNumber(b?.id) - safeNumber(a?.id))
    }

    if (sortParam.value === 'outOfStock') {
        return list
            .filter((variant) => {
                return !variant.in_stock
                    || safeNumber(variant.quantity) <= 0
            })
            .sort((a, b) => safeNumber(b?.id) - safeNumber(a?.id))
    }

    /** Основной вариант */
    if (sortParam.value === 'default') {
        return list
            .filter((variant) => Boolean(variant.is_default))
            .sort((a, b) => safeNumber(b?.id) - safeNumber(a?.id))
    }

    if (sortParam.value === 'notDefault') {
        return list
            .filter((variant) => !variant.is_default)
            .sort((a, b) => safeNumber(b?.id) - safeNumber(a?.id))
    }

    /** Фильтры статуса публикации */
    if (sortParam.value === 'statusDraft') {
        return list
            .filter((variant) => {
                return variant.status === 'draft'
            })
            .sort((a, b) => safeNumber(b?.id) - safeNumber(a?.id))
    }

    if (sortParam.value === 'statusPublished') {
        return list
            .filter((variant) => {
                return variant.status === 'published'
            })
            .sort((a, b) => safeNumber(b?.id) - safeNumber(a?.id))
    }

    if (sortParam.value === 'statusArchived') {
        return list
            .filter((variant) => {
                return variant.status === 'archived'
            })
            .sort((a, b) => safeNumber(b?.id) - safeNumber(a?.id))
    }

    /** Фильтры модерации */
    if (sortParam.value === 'moderationPending') {
        return list
            .filter((variant) => {
                return moderationNum(variant.moderation_status) === 0
            })
            .sort((a, b) => safeNumber(b?.id) - safeNumber(a?.id))
    }

    if (sortParam.value === 'moderationApproved') {
        return list
            .filter((variant) => {
                return moderationNum(variant.moderation_status) === 1
            })
            .sort((a, b) => safeNumber(b?.id) - safeNumber(a?.id))
    }

    if (sortParam.value === 'moderationRejected') {
        return list
            .filter((variant) => {
                return moderationNum(variant.moderation_status) === 2
            })
            .sort((a, b) => safeNumber(b?.id) - safeNumber(a?.id))
    }

    const sortMap = {
        /** ID и ручная сортировка */
        idAsc: (a, b) => safeNumber(a?.id) - safeNumber(b?.id),
        idDesc: (a, b) => safeNumber(b?.id) - safeNumber(a?.id),

        sortAsc: byNumberAsc('sort'),
        sortDesc: byNumberDesc('sort'),

        /** Название варианта */
        titleAsc: (a, b) => {
            return compareText(
                getVariantTitle(a),
                getVariantTitle(b)
            ) || safeNumber(b?.id) - safeNumber(a?.id)
        },

        titleDesc: (a, b) => {
            return compareText(
                getVariantTitle(b),
                getVariantTitle(a)
            ) || safeNumber(b?.id) - safeNumber(a?.id)
        },

        /** Название родительского товара */
        productTitleAsc: (a, b) => {
            return compareText(
                getProductTitle(a),
                getProductTitle(b)
            ) || safeNumber(b?.id) - safeNumber(a?.id)
        },

        productTitleDesc: (a, b) => {
            return compareText(
                getProductTitle(b),
                getProductTitle(a)
            ) || safeNumber(b?.id) - safeNumber(a?.id)
        },

        /** Торговые коды */
        codeAsc: byStringAsc('code'),
        codeDesc: byStringDesc('code'),

        skuAsc: byStringAsc('sku'),
        skuDesc: byStringDesc('sku'),

        vendorCodeAsc: byStringAsc('vendor_code'),
        vendorCodeDesc: byStringDesc('vendor_code'),

        barcodeAsc: byStringAsc('barcode'),
        barcodeDesc: byStringDesc('barcode'),

        /**
         * Эффективные цены.
         *
         * Используется собственное значение варианта
         * либо унаследованное значение товара.
         */
        priceAsc: byNumberAsc('effective_price'),
        priceDesc: byNumberDesc('effective_price'),

        oldPriceAsc: byNumberAsc('effective_old_price'),
        oldPriceDesc: byNumberDesc('effective_old_price'),

        purchasePriceAsc: byNumberAsc(
            'effective_purchase_price'
        ),

        purchasePriceDesc: byNumberDesc(
            'effective_purchase_price'
        ),

        wholesalePriceAsc: byNumberAsc(
            'effective_wholesale_price'
        ),

        wholesalePriceDesc: byNumberDesc(
            'effective_wholesale_price'
        ),

        wholesaleMinQuantityAsc: byNumberAsc(
            'effective_wholesale_min_quantity'
        ),

        wholesaleMinQuantityDesc: byNumberDesc(
            'effective_wholesale_min_quantity'
        ),

        /** Остаток и наличие */
        quantityAsc: byNumberAsc('quantity'),
        quantityDesc: byNumberDesc('quantity'),

        inStockAsc: byNumberAsc('in_stock'),
        inStockDesc: byNumberDesc('in_stock'),

        /**
         * Эффективные физические параметры.
         *
         * Используются собственные значения варианта
         * либо унаследованные значения товара.
         */
        weightAsc: byNumberAsc('effective_weight'),
        weightDesc: byNumberDesc('effective_weight'),

        lengthAsc: byNumberAsc('effective_length'),
        lengthDesc: byNumberDesc('effective_length'),

        widthAsc: byNumberAsc('effective_width'),
        widthDesc: byNumberDesc('effective_width'),

        heightAsc: byNumberAsc('effective_height'),
        heightDesc: byNumberDesc('effective_height'),

        /** Основной вариант */
        defaultAsc: byNumberAsc('is_default'),
        defaultDesc: byNumberDesc('is_default'),

        /** Количество связей */
        valuesAsc: byNumberAsc('values_count'),
        valuesDesc: byNumberDesc('values_count'),

        imagesAsc: byNumberAsc('images_count'),
        imagesDesc: byNumberDesc('images_count'),

        /** Активность */
        activityAsc: byNumberAsc('activity'),
        activityDesc: byNumberDesc('activity'),

        /** Статус публикации */
        statusAsc: byStringAsc('status'),
        statusDesc: byStringDesc('status'),

        /** Статус модерации */
        moderationStatusAsc: byNumberAsc(
            'moderation_status'
        ),

        moderationStatusDesc: byNumberDesc(
            'moderation_status'
        ),

        /** Даты */
        publishedAtAsc: byDateAsc('published_at'),
        publishedAtDesc: byDateDesc('published_at'),

        showFromAtAsc: byDateAsc('show_from_at'),
        showFromAtDesc: byDateDesc('show_from_at'),

        showToAtAsc: byDateAsc('show_to_at'),
        showToAtDesc: byDateDesc('show_to_at'),

        createdAtAsc: byDateAsc('created_at'),
        createdAtDesc: byDateDesc('created_at'),

        updatedAtAsc: byDateAsc('updated_at'),
        updatedAtDesc: byDateDesc('updated_at'),
    }

    const sorter = sortMap[sortParam.value] || sortMap.idDesc

    return list.sort(sorter)
})

/* ======================== Frontend pagination ======================== */

/** Локально отображаемые варианты */
const frontendPaginatedVariants = computed(() => {
    const start = (currentPage.value - 1) * itemsPerPage.value

    return sortedVariants.value.slice(start, start + itemsPerPage.value)
})

/** Сброс страницы при фильтрации */
watch(
    [searchQuery, sortParam, itemsPerPage, viewMode],
    () => {
        currentPage.value = 1
    }
)

/* ======================== Displayed collection ======================== */

/** Итоговый список */
const displayedVariants = computed(() => {
    return props.useServerProcessing ? localVariants.value : frontendPaginatedVariants.value
})

/* ======================== Selection ======================== */

/** Выбранные варианты */
const selectedVariants = ref([])

/** Выбран ли вариант */
const isSelected = (variantId) => {
    return selectedVariants.value.includes(variantId)
}

/** Выбор одного варианта */
const toggleSelectVariant = (variantOrId) => {
    const id = typeof variantOrId === 'object' ? variantOrId?.id : variantOrId

    if (!id) {
        return
    }

    if (isSelected(id)) {
        selectedVariants.value = selectedVariants.value.filter((selectedId) => selectedId !== id)

        return
    }

    selectedVariants.value.push(id)
}

/** Выбрать или снять все видимые варианты */
const toggleAll = (eventOrValue) => {
    const checked = typeof eventOrValue === 'boolean'
        ? eventOrValue
        : Boolean(eventOrValue?.target?.checked ?? eventOrValue?.checked)

    if (!checked) {
        selectedVariants.value = []

        return
    }

    selectedVariants.value = displayedVariants.value.map((variant) => variant?.id).filter(Boolean)
}

/** Все видимые варианты выбраны */
const allDisplayedSelected = computed(() => {
    const ids = displayedVariants.value.map((variant) => variant?.id).filter(Boolean)

    return ids.length > 0 && ids.every((id) => selectedVariants.value.includes(id))
})

/* ======================== Bulk actions ======================== */

/** Массовое изменение активности */
const bulkToggleActivity = (activity) => {
    if (!selectedVariants.value.length) {
        toast.warning('Выберите хотя бы один вариант товара.')

        return
    }

    const ids = [...selectedVariants.value]

    router.put(
        route('admin.actions.marketProductVariants.bulkUpdateActivity'),
        {
            ids,
            activity,
        },
        {
            preserveScroll: true,
            preserveState: true,

            onSuccess: () => {
                localVariants.value.forEach((variant) => {
                    if (ids.includes(variant.id)) {
                        variant.activity = activity
                        variant.is_active = activity
                    }
                })

                selectedVariants.value = []

                toast.success('Активность выбранных вариантов товаров обновлена.')
            },

            onError: (errors) => {
                toast.error(errors?.ids || errors?.activity || errors?.general || 'Ошибка массового изменения активности.')
            },
        }
    )
}

/** Массовое удаление */
const bulkDelete = () => {
    if (!selectedVariants.value.length) {
        toast.warning('Выберите хотя бы один вариант товара для удаления.')

        return
    }

    if (!confirm('Вы уверены, что хотите удалить выбранные варианты товаров?')) {
        return
    }

    router.delete(
        route('admin.actions.marketProductVariants.bulkDestroy'),
        {
            data: {
                ids: selectedVariants.value,
            },

            preserveScroll: true,
            preserveState: false,

            onSuccess: () => {
                selectedVariants.value = []

                toast.success('Выбранные варианты товаров успешно удалены.')
            },

            onError: (errors) => {
                const firstKey = Object.keys(errors || {})[0]

                toast.error(errors?.[firstKey] || 'Ошибка массового удаления вариантов товаров.')
            },
        }
    )
}

/** Обработка массового действия */
const handleBulkAction = (event) => {
    const action = event?.target?.value

    if (action === 'selectAll') {
        toggleAll(true)
    } else if (action === 'deselectAll') {
        toggleAll(false)
    } else if (action === 'activate') {
        bulkToggleActivity(true)
    } else if (action === 'deactivate') {
        bulkToggleActivity(false)
    } else if (action === 'delete') {
        bulkDelete()
    }

    if (event?.target) {
        event.target.value = ''
    }
}

/* ======================== Drag-and-drop sorting ======================== */

/** Массовое обновление sort */
const handleSortOrderUpdate = (newOrderIds) => {
    const items = (newOrderIds || []).map((id, index) => ({
        id,
        sort: index,
    }))

    if (!items.length) {
        return
    }

    router.put(
        route('admin.actions.marketProductVariants.updateSortBulk'),
        {
            items,
        },
        {
            preserveScroll: true,
            preserveState: true,

            onSuccess: () => {
                items.forEach((item) => {
                    patchLocalVariant(item.id, (variant) => {
                        variant.sort = item.sort
                    })
                })

                toast.success('Сортировка вариантов товаров обновлена.')
            },

            onError: (errors) => {
                console.error('Ошибка сортировки вариантов товаров:', errors)

                toast.error(errors?.message || errors?.items || 'Ошибка обновления сортировки вариантов товаров.')
            },
        }
    )
}
</script>

<template>
    <AdminLayout :title="t('marketProductVariants')">
        <template #header>
            <TitlePage>
                {{ t('marketProductVariants') }}
            </TitlePage>
        </template>

        <div class="px-2 py-2 w-full max-w-12xl mx-auto">
            <div
                class="p-4
                       bg-slate-50 dark:bg-slate-700
                       border border-blue-400 dark:border-blue-200
                       overflow-hidden
                       shadow-md shadow-gray-500 dark:shadow-slate-400
                       bg-opacity-95 dark:bg-opacity-95"
            >
                <!-- Верхняя панель -->
                <div
                    class="flex flex-col gap-3 mb-3
                           sm:flex-row sm:items-center sm:justify-between"
                >
                    <DefaultButton
                        :href="route('admin.marketProductVariants.create', selectedProductId ? { market_product_id: selectedProductId } : {})"
                    >
                        {{ t('addMarketProductVariant') }}
                    </DefaultButton>

                    <ProcessingModeSwitcher
                        setting-key="adminMarketProductVariantsProcessingMode"
                        :mode="adminMarketProductVariantsProcessingMode"
                        :use-server-processing="useServerProcessing"
                        :total="variantsCount"
                    />
                </div>

                <!-- Ошибка загрузки -->
                <div
                    v-if="error"
                    class="mb-3 rounded-sm border border-red-400 bg-red-100 px-3 py-2 text-sm text-red-700 dark:border-red-700 dark:bg-red-900/40 dark:text-red-200"
                >
                    {{ error }}
                </div>

                <!-- Поиск -->
                <SearchInput
                    v-if="variantsCount && !useServerProcessing"
                    v-model="searchQuery"
                />

                <!-- Фильтр по товару -->
                <div class="mb-3">
                    <select
                        v-model.number="selectedProductId"
                        class="w-full px-2 py-1 text-xs bg-slate-200 dark:bg-cyan-900 dark:text-slate-100 border border-slate-400 dark:border-slate-600 rounded-sm"
                        @change="handleProductFilterChange"
                    >
                        <option :value="null">{{ t('allProducts') }}</option>

                        <option
                            v-for="product in productOptions"
                            :key="product.id"
                            :value="product.id"
                        >
                            {{ productOptionLabel(product) }}
                        </option>
                    </select>
                </div>

                <ServerSearchInput
                    v-if="variantsCount && useServerProcessing"
                    v-model="searchQuery"
                />

                <!-- Пагинация и сортировка -->
                <div
                    v-if="variantsCount"
                    class="flex flex-col md:flex-row
                           items-center justify-between
                           gap-3 my-3"
                >
                    <ItemsPerPageSelect
                        v-if="!useServerProcessing"
                        :items-per-page="itemsPerPage"
                        @update:itemsPerPage="itemsPerPage = $event"
                    />

                    <ServerItemsPerPageSelect
                        v-else
                        :items-per-page="itemsPerPage"
                        update-route="admin.settings.updateAdminCountMarketProductVariants"
                    />

                    <SortSelect
                        :sortParam="sortParam"
                        @update:sortParam="sortParam = $event"
                    />
                </div>

                <!-- Счётчик, массовые действия, вид -->
                <div
                    v-if="variantsCount"
                    class="flex flex-col lg:flex-row
                           items-center justify-between
                           gap-3"
                >
                    <CountTable>
                        {{ variantsCount }}
                    </CountTable>

                    <BulkActionSelect @change="handleBulkAction" />

                    <ToggleViewButton v-model:viewMode="viewMode" />
                </div>

                <!-- Верхняя пагинация -->
                <div
                    v-if="variantsCount"
                    class="flex justify-center items-center flex-col md:flex-row mt-3"
                >
                    <Pagination
                        v-if="!useServerProcessing"
                        :current-page="currentPage"
                        :items-per-page="itemsPerPage"
                        :total-items="filteredVariants.length"
                        @update:currentPage="currentPage = $event"
                    />

                    <AdminServerPagination
                        v-else
                        :pagination="variants"
                    />
                </div>

                <!-- Таблица -->
                <VariantTable
                    v-if="viewMode === 'table'"
                    :variants="displayedVariants"
                    :selected-variants="selectedVariants"
                    :all-selected="allDisplayedSelected"
                    :is-admin="isAdmin"
                    @toggle-select="toggleSelectVariant"
                    @toggle-all="toggleAll"
                    @toggle-activity="toggleActivity"
                    @approve="approveVariant"
                    @delete="confirmDelete"
                    @update-sort-order="handleSortOrderUpdate"
                    @make-default="makeDefaultVariant"
                />

                <!-- Карточки -->
                <VariantCardGrid
                    v-else
                    :variants="displayedVariants"
                    :selected-variants="selectedVariants"
                    :is-admin="isAdmin"
                    @toggle-select="toggleSelectVariant"
                    @toggle-all="toggleAll"
                    @toggle-activity="toggleActivity"
                    @approve="approveVariant"
                    @delete="confirmDelete"
                    @update-sort-order="handleSortOrderUpdate"
                    @make-default="makeDefaultVariant"
                />

                <!-- Пустой список -->
                <div
                    v-if="!variantsCount"
                    class="py-12 text-center text-slate-500 dark:text-slate-300"
                >
                    {{ t('noData') }}
                </div>

                <!-- Ничего не найдено -->
                <div
                    v-else-if="!useServerProcessing && !displayedVariants.length"
                    class="py-10 text-center text-slate-500 dark:text-slate-300"
                >
                    По вашему запросу варианты товаров не найдены.
                </div>

                <!-- Нижняя пагинация -->
                <div
                    v-if="variantsCount"
                    class="flex justify-center items-center flex-col md:flex-row mt-4"
                >
                    <Pagination
                        v-if="!useServerProcessing"
                        :current-page="currentPage"
                        :items-per-page="itemsPerPage"
                        :total-items="filteredVariants.length"
                        @update:currentPage="currentPage = $event"
                    />

                    <AdminServerPagination
                        v-else
                        :pagination="variants"
                    />
                </div>
            </div>
        </div>

        <!-- Подтверждение удаления -->
        <DangerModal
            :show="showConfirmDeleteModal"
            :on-cancel="closeModal"
            :on-confirm="deleteVariant"
            :cancel-text="t('cancel')"
            :confirm-text="t('yesDelete')"
            @close="closeModal"
        >
            <template #default>
                <p class="text-sm text-slate-700 dark:text-slate-200">
                    Удалить вариант товара
                    <strong>
                        {{ variantToDeleteTitle }}
                    </strong>
                    ?
                </p>
            </template>
        </DangerModal>
    </AdminLayout>
</template>
