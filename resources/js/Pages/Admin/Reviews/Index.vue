<script setup>
/**
 * @version PulsarCMS 1.0
 * @author Александр Косолапов <kosolapov1976@gmail.com>
 *
 * Отзывы — Index.
 * - frontend / server / auto режимы обработки;
 * - локальный и серверный поиск;
 * - локальная и серверная сортировка;
 * - локальная и серверная пагинация;
 * - одиночная и массовая активность;
 * - одиночное и массовое удаление;
 * - административная модерация;
 * - табличный и карточный режим отображения.
 */

import { computed, ref, watch } from 'vue'
import { router } from '@inertiajs/vue3'
import { useI18n } from 'vue-i18n'
import { useToast } from 'vue-toastification'
import axios from 'axios'

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

import BulkActionSelect from '@/Components/Admin/Review/Select/BulkActionSelect.vue'
import SortSelect from '@/Components/Admin/Review/Sort/SortSelect.vue'
import ReviewTable from '@/Components/Admin/Review/Table/ReviewTable.vue'
import ReviewCardGrid from '@/Components/Admin/Review/View/ReviewCardGrid.vue'
import ReviewDetailsModal from '@/Components/Admin/Review/Modal/ReviewDetailsModal.vue'

const { t, locale } = useI18n()
const toast = useToast()

const props = defineProps({
    reviews: { type: [Array, Object], default: () => [] },
    reviewsCount: { type: Number, default: 0 },
    adminReviewsProcessingMode: { type: String, default: 'frontend' },
    useServerProcessing: { type: Boolean, default: false },
    adminReviewsPerPage: { type: Number, default: 10 },
    adminReviewsDefaultSort: { type: String, default: 'idDesc' },
    sortParam: { type: String, default: '' },
    search: { type: String, default: '' },
    currentLocale: { type: String, default: '' },
    availableLocales: { type: Array, default: () => [] },
    isAdmin: { type: Boolean, default: false },
    error: { type: String, default: '' },
    errors: { type: Object, default: () => ({}) },
})

/** Пользователь имеет административные права. */
const isAdmin = computed(() => Boolean(props.isAdmin))

/** Режим отображения списка. */
const viewMode = ref(
    localStorage.getItem('admin_view_mode_reviews') || 'table'
)

watch(viewMode, (value) => {
    localStorage.setItem('admin_view_mode_reviews', value)
})

/** Приведение ресурса или пагинатора к обычному массиву. */
const reviewsList = computed(() => {
    if (Array.isArray(props.reviews)) return props.reviews
    if (Array.isArray(props.reviews?.data)) return props.reviews.data

    return []
})

/** Локальная копия отзывов для мгновенного обновления интерфейса. */
const localReviews = ref([])

watch(
    reviewsList,
    (reviews) => {
        localReviews.value = JSON.parse(
            JSON.stringify(reviews || [])
        )
    },
    {
        immediate: true,
        deep: true,
    }
)

/** Частичное обновление одного отзыва в локальном списке. */
const patchLocal = (id, patch) => {
    const index = localReviews.value.findIndex(
        (review) => Number(review.id) === Number(id)
    )

    if (index === -1) return

    localReviews.value[index] = {
        ...localReviews.value[index],
        ...patch,
    }
}

/* ======================== Pagination ======================== */

/** Количество элементов на странице. */
const itemsPerPage = ref(
    Number(props.adminReviewsPerPage || 10)
)

watch(itemsPerPage, (value) => {
    router.put(
        route('admin.settings.updateAdminCountReviews'),
        { value },
        {
            preserveScroll: true,
            preserveState: true,

            onSuccess: () =>
                toast.info(
                    `Показ ${value} элементов на странице.`
                ),

            onError: (errors) =>
                toast.error(
                    errors?.value
                    || 'Ошибка обновления количества элементов.'
                ),
        }
    )
})

/** Текущая frontend-страница. */
const currentPage = ref(1)

/* ======================== Sorting ======================== */

/** Текущий параметр сортировки. */
const sortParam = ref(
    props.sortParam
    || props.adminReviewsDefaultSort
    || 'idDesc'
)

watch(sortParam, (value) => {
    currentPage.value = 1

    router.put(
        route('admin.settings.updateAdminSortReviews'),
        { value },
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

                            sort: value || undefined,
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
                    'Сортировка успешно изменена.'
                )
            },

            onError: (errors) =>
                toast.error(
                    errors?.value
                    || 'Ошибка обновления сортировки.'
                ),
        }
    )
})

/* ======================== Search ======================== */

/** Строка поиска. */
const searchQuery = ref(props.search || '')

watch(
    [
        itemsPerPage,
        searchQuery,
    ],
    () => {
        currentPage.value = 1
    }
)

/** Нормализация значения для поиска и строковой сортировки. */
const normalize = (value) =>
    (value ?? '')
        .toString()
        .trim()
        .toLowerCase()

/**
 * Разбор поисковой строки.
 *
 * Должен соответствовать Review::scopeSearch():
 * - разделение по служебным символам;
 * - слова короче двух символов не участвуют;
 * - каждое слово должно быть найдено.
 */
const searchWords = (value) => {
    return String(value || '')
        .split(/[\s:#№,"'«»(){}\[\].!?/\\|;+=*&^%$@<>`~_-]+/u)
        .map((word) => normalize(word))
        .filter((word) => word.length >= 2)
}

/* ======================== Helpers ======================== */

/** Безопасное числовое значение. */
const numberValue = (value, fallback = 0) => {
    const number = Number(value)

    return Number.isFinite(number)
        ? number
        : fallback
}

/** Безопасная дата для локальной сортировки. */
const safeDate = (value) => {
    const time = new Date(value || 0).getTime()

    return Number.isFinite(time)
        ? time
        : 0
}

/** Короткий тип полиморфной сущности. */
const reviewableType = (review) => {
    return review?.reviewable?.type
        || review?.reviewable_type
        || ''
}

/** Название полиморфной сущности. */
const reviewableTitle = (review) => {
    return review?.reviewable?.title
        || review?.reviewable?.name
        || review?.reviewable?.url
        || ''
}

/** Наличие ответа на отзыв. */
const hasReply = (review) => {
    return Boolean(
        review?.has_reply
        || normalize(review?.reply)
    )
}

/**
 * Значения отзыва, участвующие во frontend-поиске.
 *
 * Набор соответствует Review::scopeSearch().
 */
const reviewSearchValues = (review) => [
    /** Сам отзыв */
    review?.reviewable_type,
    reviewableType(review),

    review?.advantages,
    review?.disadvantages,
    review?.comment,
    review?.reply,
    review?.moderation_note,

    /** Связанные пользователи */
    review?.author?.name,
    review?.author?.email,

    review?.replier?.name,
    review?.replier?.email,

    review?.moderator?.name,
    review?.moderator?.email,

    /** Полиморфная сущность */
    reviewableTitle(review),

    review?.reviewable?.url,
    review?.reviewable?.sku,

    /**
     * Эти поля могут присутствовать у MarketProduct.
     * Если Resource их не передаёт, значения просто undefined.
     */
    review?.reviewable?.vendor_code,
    review?.reviewable?.barcode,

    /**
     * Переводимые поля reviewable.
     * title уже нормализован в reviewable.title.
     * Остальные поля учитываются, если Resource их передаёт.
     */
    review?.reviewable?.subtitle,
    review?.reviewable?.short,
    review?.reviewable?.description,
]

/* ======================== Frontend sorting ======================== */

/** Локальная фильтрация и сортировка отзывов. */
const sortReviews = (reviews) => {
    const list = [...(reviews || [])]

    /** Фильтры */
    if (sortParam.value === 'activity') {
        return list.filter(
            (review) => Boolean(review.activity)
        )
    }

    if (sortParam.value === 'inactive') {
        return list.filter(
            (review) => !review.activity
        )
    }

    if (sortParam.value === 'verified') {
        return list.filter(
            (review) => Boolean(review.verified)
        )
    }

    if (sortParam.value === 'notVerified') {
        return list.filter(
            (review) => !review.verified
        )
    }

    if (sortParam.value === 'hasReply') {
        return list.filter(
            (review) => hasReply(review)
        )
    }

    if (sortParam.value === 'noReply') {
        return list.filter(
            (review) => !hasReply(review)
        )
    }

    if (sortParam.value === 'moderationPending') {
        return list.filter(
            (review) =>
                numberValue(
                    review.moderation_status
                ) === 0
        )
    }

    if (sortParam.value === 'moderationApproved') {
        return list.filter(
            (review) =>
                numberValue(
                    review.moderation_status
                ) === 1
        )
    }

    if (sortParam.value === 'moderationRejected') {
        return list.filter(
            (review) =>
                numberValue(
                    review.moderation_status
                ) === 2
        )
    }

    /** Сортировки */
    const sortMap = {
        idAsc: (a, b) =>
            numberValue(a.id)
            - numberValue(b.id),

        idDesc: (a, b) =>
            numberValue(b.id)
            - numberValue(a.id),

        authorNameAsc: (a, b) =>
            normalize(a?.author?.name)
                .localeCompare(
                    normalize(b?.author?.name),
                    locale.value
                ),

        authorNameDesc: (a, b) =>
            normalize(b?.author?.name)
                .localeCompare(
                    normalize(a?.author?.name),
                    locale.value
                ),

        authorEmailAsc: (a, b) =>
            normalize(a?.author?.email)
                .localeCompare(
                    normalize(b?.author?.email),
                    locale.value
                ),

        authorEmailDesc: (a, b) =>
            normalize(b?.author?.email)
                .localeCompare(
                    normalize(a?.author?.email),
                    locale.value
                ),

        ratingAsc: (a, b) =>
            numberValue(a.rating)
            - numberValue(b.rating),

        ratingDesc: (a, b) =>
            numberValue(b.rating)
            - numberValue(a.rating),

        likesAsc: (a, b) =>
            numberValue(a.likes)
            - numberValue(b.likes),

        likesDesc: (a, b) =>
            numberValue(b.likes)
            - numberValue(a.likes),

        imagesAsc: (a, b) =>
            numberValue(a.images_count)
            - numberValue(b.images_count),

        imagesDesc: (a, b) =>
            numberValue(b.images_count)
            - numberValue(a.images_count),

        commentAsc: (a, b) =>
            normalize(a.comment)
                .localeCompare(
                    normalize(b.comment),
                    locale.value
                ),

        commentDesc: (a, b) =>
            normalize(b.comment)
                .localeCompare(
                    normalize(a.comment),
                    locale.value
                ),

        reviewableTypeAsc: (a, b) =>
            normalize(reviewableType(a))
                .localeCompare(
                    normalize(reviewableType(b)),
                    locale.value
                ),

        reviewableTypeDesc: (a, b) =>
            normalize(reviewableType(b))
                .localeCompare(
                    normalize(reviewableType(a)),
                    locale.value
                ),

        verifiedAsc: (a, b) =>
            Number(Boolean(a.verified))
            - Number(Boolean(b.verified)),

        verifiedDesc: (a, b) =>
            Number(Boolean(b.verified))
            - Number(Boolean(a.verified)),

        replyAsc: (a, b) =>
            Number(hasReply(a))
            - Number(hasReply(b)),

        replyDesc: (a, b) =>
            Number(hasReply(b))
            - Number(hasReply(a)),

        createdAtAsc: (a, b) =>
            safeDate(a.created_at)
            - safeDate(b.created_at),

        createdAtDesc: (a, b) =>
            safeDate(b.created_at)
            - safeDate(a.created_at),

        updatedAtAsc: (a, b) =>
            safeDate(a.updated_at)
            - safeDate(b.updated_at),

        updatedAtDesc: (a, b) =>
            safeDate(b.updated_at)
            - safeDate(a.updated_at),

        activityAsc: (a, b) =>
            Number(Boolean(a.activity))
            - Number(Boolean(b.activity)),

        activityDesc: (a, b) =>
            Number(Boolean(b.activity))
            - Number(Boolean(a.activity)),

        moderationStatusAsc: (a, b) =>
            numberValue(a.moderation_status)
            - numberValue(b.moderation_status),

        moderationStatusDesc: (a, b) =>
            numberValue(b.moderation_status)
            - numberValue(a.moderation_status),
    }

    return sortMap[sortParam.value]
        ? list.sort(sortMap[sortParam.value])
        : list
}

/**
 * Отзывы после frontend-поиска и сортировки.
 *
 * Семантика соответствует backend:
 * каждое поисковое слово должно встретиться
 * хотя бы в одном доступном поле отзыва.
 */
const filteredReviews = computed(() => {
    const words = searchWords(searchQuery.value)

    if (!words.length) {
        return sortReviews(localReviews.value)
    }

    const filtered = localReviews.value.filter(
        (review) => {
            const values = reviewSearchValues(review)
                .map((value) => normalize(value))
                .filter(Boolean)

            return words.every(
                (word) =>
                    values.some(
                        (value) =>
                            value.includes(word)
                    )
            )
        }
    )

    return sortReviews(filtered)
})

/** Локальная пагинация. */
const paginatedReviews = computed(() => {
    const perPage = Number(
        itemsPerPage.value || 10
    )

    const start =
        (currentPage.value - 1)
        * perPage

    return filteredReviews.value.slice(
        start,
        start + perPage
    )
})

/** Фактически отображаемые отзывы. */
const displayedReviews = computed(() => {
    return props.useServerProcessing
        ? reviewsList.value
        : paginatedReviews.value
})

/* ======================== Selection ======================== */

/** Выбранные отзывы. */
const selectedReviews = ref([])

/** Выбрать или снять все отзывы текущего отображения. */
const toggleAll = (payload) => {
    const checked =
        payload?.checked
        ?? payload?.target?.checked
        ?? false

    const ids =
        payload?.ids
        ?? displayedReviews.value.map(
            (review) => review.id
        )

    if (checked) {
        selectedReviews.value = [
            ...new Set([
                ...selectedReviews.value,
                ...ids,
            ]),
        ]

        return
    }

    selectedReviews.value =
        selectedReviews.value.filter(
            (id) => !ids.includes(id)
        )
}

/** Выбрать один отзыв. */
const toggleSelectReview = (reviewId) => {
    const index =
        selectedReviews.value.indexOf(
            reviewId
        )

    if (index > -1) {
        selectedReviews.value.splice(
            index,
            1
        )

        return
    }

    selectedReviews.value.push(
        reviewId
    )
}

/* ======================== Bulk actions ======================== */

/** Массовое переключение активности. */
const bulkToggleActivity = (activity) => {
    if (!selectedReviews.value.length) {
        toast.warning(
            'Выберите отзывы для изменения активности.'
        )

        return
    }

    const ids = [
        ...selectedReviews.value,
    ]

    router.put(
        route(
            'admin.actions.reviews.bulkUpdateActivity'
        ),
        {
            ids,
            activity,
        },
        {
            preserveScroll: true,
            preserveState: true,

            onSuccess: () => {
                localReviews.value =
                    localReviews.value.map(
                        (review) =>
                            ids.includes(review.id)
                                ? {
                                    ...review,
                                    activity,
                                }
                                : review
                    )

                selectedReviews.value = []

                toast.success(
                    'Активность отзывов массово обновлена.'
                )
            },

            onError: (errors) =>
                toast.error(
                    errors?.ids
                    || errors?.activity
                    || errors?.general
                    || 'Не удалось массово обновить активность отзывов.'
                ),
        }
    )
}

/** Массовое удаление выбранных отзывов. */
const bulkDelete = async () => {
    if (!selectedReviews.value.length) {
        toast.warning(
            'Выберите хотя бы один отзыв для удаления.'
        )

        return
    }

    if (
        !confirm(
            'Вы уверены, что хотите удалить выбранные отзывы?'
        )
    ) {
        return
    }

    const ids = [
        ...selectedReviews.value,
    ]

    try {
        const response = await axios.delete(
            route(
                'admin.actions.reviews.bulkDestroy'
            ),
            {
                data: {
                    ids,
                },
            }
        )

        if (!response?.data?.success) {
            toast.error(
                response?.data?.message
                || 'Произошла ошибка при удалении отзывов.'
            )

            return
        }

        localReviews.value =
            localReviews.value.filter(
                (review) =>
                    !ids.includes(review.id)
            )

        selectedReviews.value = []

        toast.success(
            response.data.message
            || 'Массовое удаление отзывов успешно завершено.'
        )
    } catch (error) {
        console.error(
            'Ошибка массового удаления отзывов:',
            error
        )

        toast.error(
            error?.response?.data?.message
            || 'Произошла ошибка при удалении отзывов.'
        )
    }
}

/** Обработка выбранного массового действия. */
const handleBulkAction = (event) => {
    const action = event.target.value

    if (action === 'selectAll') {
        toggleAll({
            checked: true,
        })
    }

    if (action === 'deselectAll') {
        toggleAll({
            checked: false,
        })
    }

    if (action === 'activate') {
        bulkToggleActivity(true)
    }

    if (action === 'deactivate') {
        bulkToggleActivity(false)
    }

    if (action === 'delete') {
        bulkDelete()
    }

    event.target.value = ''
}

/* ======================== Details modal ======================== */

/** Модальное окно подробной информации. */
const showReviewDetailsModal = ref(false)
const reviewDetails = ref(null)

const viewReviewDetails = (review) => {
    reviewDetails.value = review
    showReviewDetailsModal.value = true
}

const closeReviewDetailsModal = () => {
    showReviewDetailsModal.value = false
    reviewDetails.value = null
}

/* ======================== Delete modal ======================== */

/** Модальное окно подтверждения одиночного удаления. */
const showConfirmDeleteModal = ref(false)
const reviewToDeleteId = ref(null)

const confirmDelete = (id) => {
    reviewToDeleteId.value = id
    showConfirmDeleteModal.value = true
}

const closeDeleteModal = () => {
    showConfirmDeleteModal.value = false
    reviewToDeleteId.value = null
}

/** Удаление одного отзыва. */
const deleteReview = () => {
    if (
        reviewToDeleteId.value === null
    ) {
        return
    }

    const id = reviewToDeleteId.value

    router.delete(
        route(
            'admin.reviews.destroy',
            {
                review: id,
            }
        ),
        {
            preserveScroll: true,
            preserveState: false,

            onSuccess: () =>
                toast.success(
                    `Отзыв "ID: ${id}" удалён.`
                ),

            onError: (errors) => {
                const firstKey =
                    Object.keys(
                        errors || {}
                    )[0]

                const message =
                    errors?.general
                    || errors?.[firstKey]
                    || 'Произошла ошибка при удалении.'

                toast.error(
                    `${message} (Отзыв: ID: ${id})`
                )
            },

            onFinish: closeDeleteModal,
        }
    )
}

/* ======================== Activity ======================== */

/** Переключение активности одного отзыва. */
const toggleActivity = async (review) => {
    const activity = !review.activity

    try {
        const response = await axios.put(
            route(
                'admin.actions.reviews.updateActivity',
                {
                    review: review.id,
                }
            ),
            {
                activity,
            }
        )

        patchLocal(
            review.id,
            {
                activity: Boolean(
                    response.data.activity
                ),
            }
        )

        toast.success(
            response.data.message
            || 'Активность отзыва обновлена.'
        )
    } catch (error) {
        console.error(
            'Ошибка изменения активности отзыва:',
            error
        )

        toast.error(
            error?.response?.data?.message
            || 'Ошибка при изменении активности отзыва.'
        )
    }
}

/* ======================== Moderation ======================== */

/** Обновление статуса модерации одного отзыва. */
const approveReview = async (
    review,
    status = 1,
    note = ''
) => {
    if (!isAdmin.value) {
        toast.error(
            'Модерация доступна только администратору.'
        )

        return
    }

    if (!review?.id) return

    try {
        const response = await axios.put(
            route(
                'admin.actions.reviews.approve',
                {
                    review: review.id,
                }
            ),
            {
                moderation_status: status,
                moderation_note: note,
            }
        )

        const resource =
            response.data?.review

        const data =
            resource?.data
            || resource

        patchLocal(
            review.id,
            data
                ? {
                    moderation_status:
                    data.moderation_status,

                    moderation_note:
                    data.moderation_note,

                    moderated_by:
                    data.moderated_by,

                    moderated_at:
                    data.moderated_at,

                    moderator:
                    data.moderator,

                    is_pending:
                    data.is_pending,

                    is_approved:
                    data.is_approved,

                    is_rejected:
                    data.is_rejected,
                }
                : {
                    moderation_status:
                    status,

                    moderation_note:
                    note,

                    is_pending:
                        Number(status) === 0,

                    is_approved:
                        Number(status) === 1,

                    is_rejected:
                        Number(status) === 2,
                }
        )

        toast.success(
            response.data?.message
            || 'Статус модерации обновлён.'
        )
    } catch (error) {
        if (
            error?.response?.status === 403
        ) {
            toast.error(
                'Доступ запрещён: модерировать отзывы может только администратор.'
            )

            return
        }

        const validationMessage =
            error?.response?.data
                ?.errors
                ?.moderation_note
                ?.[0]

        toast.error(
            validationMessage
            || error?.response?.data?.message
            || 'Ошибка при обновлении модерации отзыва.'
        )

        console.error(
            'Ошибка модерации отзыва:',
            error
        )
    }
}
</script>

<template>
    <AdminLayout :title="t('reviews')">
        <template #header>
            <TitlePage>
                {{ t('reviews') }}
            </TitlePage>
        </template>

        <div class="px-2 py-2 w-full max-w-12xl mx-auto">
            <div
                class="p-4 bg-slate-50 dark:bg-slate-700
                       border border-blue-400 dark:border-blue-200 overflow-hidden
                       shadow-md shadow-gray-500 dark:shadow-slate-400
                       bg-opacity-95 dark:bg-opacity-95">
                <div class="sm:flex sm:justify-end sm:items-center mb-3 gap-3">
                    <ProcessingModeSwitcher
                        setting-key="adminReviewsProcessingMode"
                        :mode="adminReviewsProcessingMode"
                        :use-server-processing="useServerProcessing"
                        :total="reviewsCount" />
                </div>

                <SearchInput
                    v-if="reviewsCount && !useServerProcessing" v-model="searchQuery"
                    :placeholder="t('search')" />
                <ServerSearchInput
                    v-if="reviewsCount && useServerProcessing" v-model="searchQuery"
                    :placeholder="t('search')" />

                <div v-if="reviewsCount"
                     class="flex justify-between items-center flex-col md:flex-row my-3">
                    <ItemsPerPageSelect
                        v-if="!useServerProcessing"
                        :items-per-page="itemsPerPage"
                        @update:itemsPerPage="itemsPerPage = $event" />
                    <ServerItemsPerPageSelect
                        v-else
                        :items-per-page="itemsPerPage"
                        update-route="admin.settings.updateAdminCountReviews" />
                    <SortSelect :sortParam="sortParam" @update:sortParam="sortParam = $event" />
                </div>

                <div v-if="reviewsCount"
                     class="flex flex-col lg:flex-row items-center justify-between gap-3">
                    <CountTable>
                        {{ reviewsCount }}
                    </CountTable>

                    <BulkActionSelect @change="handleBulkAction" />

                    <ToggleViewButton v-model:viewMode="viewMode" />
                </div>

                <div v-if="reviewsCount"
                     class="flex justify-center items-center flex-col md:flex-row mt-3">
                    <Pagination
                        v-if="!useServerProcessing"
                        :current-page="currentPage"
                        :items-per-page="itemsPerPage"
                        :total-items="filteredReviews.length"
                        @update:currentPage="currentPage = $event" />
                    <AdminServerPagination v-else :pagination="reviews" />
                </div>

                <ReviewTable
                    v-if="viewMode === 'table'"
                    :isAdmin="isAdmin"
                    :reviews="displayedReviews"
                    :selected-reviews="selectedReviews"
                    @toggle-activity="toggleActivity"
                    @delete="confirmDelete"
                    @toggle-select="toggleSelectReview"
                    @toggle-all="toggleAll"
                    @view-details="viewReviewDetails"
                    @approve-review="approveReview"
                />

                <ReviewCardGrid
                    v-else
                    :isAdmin="isAdmin"
                    :reviews="displayedReviews"
                    :selected-reviews="selectedReviews"
                    @toggle-activity="toggleActivity"
                    @delete="confirmDelete"
                    @toggle-select="toggleSelectReview"
                    @toggle-all="toggleAll"
                    @view-details="viewReviewDetails"
                    @approve-review="approveReview"
                />

                <ReviewDetailsModal
                    :show="showReviewDetailsModal"
                    :review="reviewDetails"
                    @close="closeReviewDetailsModal"
                />

                <div v-if="reviewsCount"
                     class="flex justify-center items-center flex-col md:flex-row mt-3">
                    <Pagination
                        v-if="!useServerProcessing"
                        :current-page="currentPage"
                        :items-per-page="itemsPerPage"
                        :total-items="filteredReviews.length"
                        @update:currentPage="currentPage = $event" />
                    <AdminServerPagination v-else :pagination="reviews" />
                </div>

                <div v-if="!reviewsCount && !props.error"
                     class="py-8 text-center text-sm font-semibold
                            text-slate-500 dark:text-slate-300">
                    {{ t('noData') }}
                </div>

                <div v-if="props.error"
                     class="mt-3 text-sm font-semibold text-rose-700 dark:text-rose-300">
                    {{ props.error }}
                </div>
            </div>
        </div>

        <DangerModal
            :show="showConfirmDeleteModal"
            :onCancel="closeDeleteModal"
            :onConfirm="deleteReview"
            :cancelText="t('cancel')"
            :confirmText="t('yesDelete')"
            @close="closeDeleteModal"
        />
    </AdminLayout>
</template>
