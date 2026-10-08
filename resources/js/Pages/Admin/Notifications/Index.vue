<script setup>
/**
 * Внутренние уведомления — Index
 *
 * Поддерживает:
 * - frontend / server / auto режимы обработки;
 * - локальный поиск / сортировку / пагинацию;
 * - серверный поиск / сортировку / пагинацию;
 * - фильтры через Laravel;
 * - табличное и карточное представление;
 * - просмотр уведомлений;
 * - отметку собственных уведомлений как прочитанных;
 * - удаление собственных уведомлений.
 */

import { computed, defineProps, ref, watch } from 'vue'
import { router } from '@inertiajs/vue3'
import axios from 'axios'
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

import NotificationFilters from '@/Components/Admin/Notification/Filter/NotificationFilters.vue'
import SortSelect from '@/Components/Admin/Notification/Sort/SortSelect.vue'
import NotificationTable from '@/Components/Admin/Notification/Table/NotificationTable.vue'
import NotificationCardGrid from '@/Components/Admin/Notification/View/NotificationCardGrid.vue'

const { t } = useI18n()
const toast = useToast()

const props = defineProps({
    notifications: { type: [Array, Object], default: () => [] },
    notificationsCount: { type: Number, default: 0 },
    useServerProcessing: { type: Boolean, default: false },
    adminNotificationsProcessingMode: { type: String, default: 'auto' },
    adminNotificationsPerPage: { type: Number, default: 20 },
    adminNotificationsDefaultSort: { type: String, default: 'createdAtDesc' },
    sortParam: { type: String, default: '' },
    search: { type: String, default: '' },
    filters: { type: Object, default: () => ({}) },
    error: { type: String, default: '' },
    errors: { type: Object, default: () => ({}) },
})

/*
|--------------------------------------------------------------------------
| Режим отображения
|--------------------------------------------------------------------------
*/

const viewMode = ref(
    localStorage.getItem('admin_view_mode_notifications')
    || 'table'
)

watch(viewMode, (value) => {
    localStorage.setItem(
        'admin_view_mode_notifications',
        value
    )
})

/*
|--------------------------------------------------------------------------
| Нормализация данных
|--------------------------------------------------------------------------
*/

const notificationsList = computed(() => {
    if (Array.isArray(props.notifications)) {
        return props.notifications
    }

    if (Array.isArray(props.notifications?.data)) {
        return props.notifications.data
    }

    return []
})

const localNotifications = ref([])

watch(
    notificationsList,
    (value) => {
        localNotifications.value = JSON.parse(
            JSON.stringify(value || [])
        )
    },
    {
        immediate: true,
        deep: true,
    }
)

/*
|--------------------------------------------------------------------------
| Helpers
|--------------------------------------------------------------------------
*/

const normalize = (value) => {
    return (value ?? '')
        .toString()
        .trim()
        .toLowerCase()
}

const safeDate = (value) => {
    const time = new Date(value || 0).getTime()

    return Number.isFinite(time)
        ? time
        : 0
}

const compareTextAsc = (a, b) => {
    return normalize(a).localeCompare(
        normalize(b)
    )
}

const compareTextDesc = (a, b) => {
    return normalize(b).localeCompare(
        normalize(a)
    )
}

const compareDateAsc = (a, b) => {
    return safeDate(a) - safeDate(b)
}

const compareDateDesc = (a, b) => {
    return safeDate(b) - safeDate(a)
}

/**
 * UUID используется как стабильный tie-breaker.
 *
 * Хронологическую сортировку по UUID
 * не выполняем.
 */
const compareUuidAsc = (a, b) => {
    return normalize(a?.id).localeCompare(
        normalize(b?.id)
    )
}

const compareUuidDesc = (a, b) => {
    return normalize(b?.id).localeCompare(
        normalize(a?.id)
    )
}

const withUuidAsc = (compare) => {
    return (a, b) => {
        const result = compare(a, b)

        return result !== 0
            ? result
            : compareUuidAsc(a, b)
    }
}

const withUuidDesc = (compare) => {
    return (a, b) => {
        const result = compare(a, b)

        return result !== 0
            ? result
            : compareUuidDesc(a, b)
    }
}

/*
|--------------------------------------------------------------------------
| Пагинация / количество элементов
|--------------------------------------------------------------------------
*/

const currentPage = ref(1)

const itemsPerPage = ref(
    Number(
        props.adminNotificationsPerPage
        || 20
    )
)

watch(itemsPerPage, (newValue) => {
    currentPage.value = 1

    router.put(
        route(
            'admin.settings.updateAdminCountNotifications'
        ),
        {
            value: newValue,
        },
        {
            preserveScroll: true,
            preserveState: true,

            onSuccess: () => {
                toast.info(
                    `Показ ${newValue} элементов на странице.`
                )
            },

            onError: (errors) => {
                toast.error(
                    errors?.value
                    || 'Ошибка обновления количества элементов.'
                )
            },
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
    || props.adminNotificationsDefaultSort
    || 'createdAtDesc'
)

watch(sortParam, (newValue) => {
    currentPage.value = 1

    router.put(
        route(
            'admin.settings.updateAdminSortNotifications'
        ),
        {
            value: newValue,
        },
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

                toast.info(
                    'Сортировка успешно изменена.'
                )
            },

            onError: (errors) => {
                toast.error(
                    errors?.value
                    || 'Ошибка обновления сортировки.'
                )
            },
        }
    )
})

/*
|--------------------------------------------------------------------------
| Поиск
|--------------------------------------------------------------------------
*/

const searchQuery = ref(
    props.search || ''
)

watch(searchQuery, () => {
    currentPage.value = 1
})

/*
|--------------------------------------------------------------------------
| Локальная сортировка
|--------------------------------------------------------------------------
|
| Повторяет серверную сортировку NotificationController::applySort().
|
*/

const orderedNotifications = (notifications) => {
    return notifications
        .slice()
        .sort(
            withUuidDesc(
                (a, b) => compareDateDesc(
                    a?.created_at,
                    b?.created_at
                )
            )
        )
}

const sortNotifications = (notifications) => {
    const list = (notifications || []).slice()

    const sortMap = {
        createdAtAsc: withUuidAsc(
            (a, b) => compareDateAsc(
                a?.created_at,
                b?.created_at
            )
        ),

        createdAtDesc: withUuidDesc(
            (a, b) => compareDateDesc(
                a?.created_at,
                b?.created_at
            )
        ),

        updatedAtAsc: withUuidAsc(
            (a, b) => compareDateAsc(
                a?.updated_at,
                b?.updated_at
            )
        ),

        updatedAtDesc: withUuidDesc(
            (a, b) => compareDateDesc(
                a?.updated_at,
                b?.updated_at
            )
        ),

        categoryAsc: withUuidAsc(
            (a, b) => compareTextAsc(
                a?.category,
                b?.category
            )
        ),

        categoryDesc: withUuidDesc(
            (a, b) => compareTextDesc(
                a?.category,
                b?.category
            )
        ),

        levelAsc: withUuidAsc(
            (a, b) => compareTextAsc(
                a?.level,
                b?.level
            )
        ),

        levelDesc: withUuidDesc(
            (a, b) => compareTextDesc(
                a?.level,
                b?.level
            )
        ),

        readAsc: withUuidAsc(
            (a, b) => {
                const aRead = a?.is_read ? 1 : 0
                const bRead = b?.is_read ? 1 : 0

                if (aRead !== bRead) {
                    return aRead - bRead
                }

                return compareDateAsc(
                    a?.read_at,
                    b?.read_at
                )
            }
        ),

        readDesc: withUuidDesc(
            (a, b) => {
                const aRead = a?.is_read ? 1 : 0
                const bRead = b?.is_read ? 1 : 0

                if (aRead !== bRead) {
                    return bRead - aRead
                }

                return compareDateDesc(
                    a?.read_at,
                    b?.read_at
                )
            }
        ),
    }

    return sortMap[sortParam.value]
        ? list.sort(
            sortMap[sortParam.value]
        )
        : orderedNotifications(list)
}

/*
|--------------------------------------------------------------------------
| Локальный поиск
|--------------------------------------------------------------------------
|
| В frontend-режиме поиск выполняется
| по тем же основным данным уведомления,
| которые доступны серверному поиску.
|
*/

const filteredNotifications = computed(() => {
    let filtered =
        localNotifications.value || []

    const query = normalize(
        searchQuery.value
    )

    if (!query) {
        return sortNotifications(
            filtered
        )
    }

    filtered = filtered.filter(
        (notification) => {
            const values = [
                notification?.id,
                notification?.category,
                notification?.type,
                notification?.level,
                notification?.title,
                notification?.message,
                notification?.entity_type,
                notification?.entity_id,
                notification?.notifiable?.type,
                notification?.notifiable?.id,
            ]

            /**
             * Вложенный data также участвует
             * в серверном поиске по JSON.
             */
            if (notification?.data) {
                try {
                    values.push(
                        JSON.stringify(
                            notification.data
                        )
                    )
                } catch {
                    // Игнорируем несериализуемое значение.
                }
            }

            return values.some(
                (value) => normalize(value)
                    .includes(query)
            )
        }
    )

    return sortNotifications(
        filtered
    )
})

/*
|--------------------------------------------------------------------------
| Локальная пагинация
|--------------------------------------------------------------------------
*/

const paginatedNotifications = computed(() => {
    const perPage = Number(
        itemsPerPage.value || 20
    )

    const start =
        (currentPage.value - 1)
        * perPage

    return filteredNotifications.value.slice(
        start,
        start + perPage
    )
})

const displayedNotifications = computed(() => {
    return props.useServerProcessing
        ? notificationsList.value
        : paginatedNotifications.value
})

/*
|--------------------------------------------------------------------------
| Счётчики
|--------------------------------------------------------------------------
*/

const totalNotificationsCount = computed(() => {
    return Number(
        props.notificationsCount || 0
    )
})

const filteredCount = computed(() => {
    if (props.useServerProcessing) {
        return Number(
            props.notifications?.meta?.total
            ?? props.notifications?.total
            ?? notificationsList.value.length
        )
    }

    return filteredNotifications.value.length
})

/*
|--------------------------------------------------------------------------
| Server query helpers
|--------------------------------------------------------------------------
*/

const currentQuery = () => {
    return Object.fromEntries(
        new URLSearchParams(
            window.location.search
        )
    )
}

const cleanParams = (params) => {
    return Object.fromEntries(
        Object.entries(params).filter(
            ([, value]) => {
                return value !== undefined
                    && value !== null
                    && value !== ''
            }
        )
    )
}

const reloadServerList = (
    patch = {},
    options = {}
) => {
    const params = cleanParams({
        ...currentQuery(),
        ...patch,
    })

    router.get(
        route('admin.notifications.index'),
        params,
        {
            preserveScroll:
                options.preserveScroll ?? true,

            preserveState:
                options.preserveState ?? true,

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
    if (!props.useServerProcessing) {
        return
    }

    clearTimeout(searchTimer)

    searchTimer = setTimeout(() => {
        reloadServerList({
            search:
                newValue || undefined,

            page: undefined,
        })
    }, 400)
})

/*
|--------------------------------------------------------------------------
| Фильтры
|--------------------------------------------------------------------------
|
| Frontend:
| фильтры → Laravel → поиск → сортировка → пагинация Vue.
|
| Server:
| фильтры → Laravel → поиск → сортировка → пагинация Laravel.
|
*/

const emptyFilters = () => ({
    read_status: '',
    category: '',
    level: '',
    ownership: '',
    date_from: '',
    date_to: '',
})

const notificationFilters = ref({
    ...emptyFilters(),
    ...props.filters,
})

const hasActiveFilters = computed(() => {
    return Object.values(
        notificationFilters.value
    ).some((value) => {
        return value !== ''
            && value !== null
            && value !== undefined
    })
})

const applyFilters = () => {
    currentPage.value = 1

    reloadServerList(
        {
            ...notificationFilters.value,

            search:
                props.useServerProcessing
                    ? searchQuery.value
                    || undefined
                    : undefined,

            sort:
                props.useServerProcessing
                    ? sortParam.value
                    || undefined
                    : undefined,

            page: undefined,
        },
        {
            preserveState: false,
        }
    )
}

const resetFilters = () => {
    notificationFilters.value =
        emptyFilters()

    currentPage.value = 1

    router.get(
        route('admin.notifications.index'),
        cleanParams({
            search:
                props.useServerProcessing
                    ? searchQuery.value
                    || undefined
                    : undefined,

            sort:
                props.useServerProcessing
                    ? sortParam.value
                    || undefined
                    : undefined,
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
| Открытие уведомления
|--------------------------------------------------------------------------
*/

const openNotification = async (notification) => {
    if (!notification) return

    /**
     * Чужое уведомление admin может просмотреть,
     * но не имеет права изменять его состояние.
     */
    if (
        notification.is_own
        && !notification.is_read
        && notification.id
    ) {
        try {
            await axios.patch(
                route(
                    'admin.notifications.markAsRead',
                    {
                        notification:
                        notification.id,
                    }
                )
            )

            notification.is_read = true
            notification.read_at =
                new Date().toISOString()
        } catch (error) {
            toast.error(
                error?.response?.data?.message
                || 'Не удалось отметить уведомление как прочитанное.'
            )

            return
        }
    }

    if (notification.url) {
        router.visit(
            notification.url
        )
    }
}

/*
|--------------------------------------------------------------------------
| Отметить как прочитанное
|--------------------------------------------------------------------------
*/

const markAsRead = async (notification) => {
    if (
        !notification?.id
        || !notification?.is_own
        || notification?.is_read
    ) {
        return
    }

    try {
        await axios.patch(
            route(
                'admin.notifications.markAsRead',
                {
                    notification:
                    notification.id,
                }
            )
        )

        notification.is_read = true
        notification.read_at =
            new Date().toISOString()

        toast.success(
            'Уведомление отмечено как прочитанное.'
        )
    } catch (error) {
        toast.error(
            error?.response?.data?.message
            || 'Не удалось отметить уведомление как прочитанное.'
        )
    }
}

/*
|--------------------------------------------------------------------------
| Отметить все собственные как прочитанные
|--------------------------------------------------------------------------
*/

const markAllAsRead = async () => {
    try {
        await axios.patch(
            route(
                'admin.notifications.markAllAsRead'
            )
        )

        localNotifications.value.forEach(
            (notification) => {
                if (
                    notification?.is_own
                    && !notification?.is_read
                ) {
                    notification.is_read = true
                    notification.read_at =
                        new Date().toISOString()
                }
            }
        )

        /**
         * В server-режиме текущая страница может
         * содержать только часть уведомлений.
         *
         * Перезагружаем список, чтобы состояние
         * фильтров unread/read осталось корректным.
         */
        if (props.useServerProcessing) {
            reloadServerList()
        }

        toast.success(
            'Все ваши уведомления отмечены как прочитанные.'
        )
    } catch (error) {
        toast.error(
            error?.response?.data?.message
            || 'Не удалось отметить уведомления как прочитанные.'
        )
    }
}

/*
|--------------------------------------------------------------------------
| Удаление
|--------------------------------------------------------------------------
*/

const showDeleteModal = ref(false)
const notificationToDelete = ref(null)

const confirmDelete = (notification) => {
    if (
        !notification?.id
        || !notification?.is_own
    ) {
        return
    }

    notificationToDelete.value =
        notification

    showDeleteModal.value = true
}

const closeDeleteModal = () => {
    showDeleteModal.value = false
    notificationToDelete.value = null
}

const deleteNotification = () => {
    if (
        !notificationToDelete.value?.id
        || !notificationToDelete.value?.is_own
    ) {
        return
    }

    const notificationId =
        notificationToDelete.value.id

    router.delete(
        route(
            'admin.notifications.destroy',
            {
                notification:
                notificationId,
            }
        ),
        {
            preserveScroll: true,

            onSuccess: () => {
                localNotifications.value =
                    localNotifications.value.filter(
                        (notification) =>
                            notification.id
                            !== notificationId
                    )

                closeDeleteModal()

                toast.success(
                    'Уведомление успешно удалено.'
                )
            },

            onError: (errors) => {
                toast.error(
                    errors?.error
                    || errors?.general
                    || 'Не удалось удалить уведомление.'
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
        if (value) {
            toast.error(value)
        }
    },
    {
        immediate: true,
    }
)
</script>

<template>
    <AdminLayout :title="t('notifications')">
        <template #header>
            <TitlePage>
                {{ t('notifications') }}
            </TitlePage>
        </template>

        <div class="py-3">
            <div class="mx-auto max-w-full sm:px-4 lg:px-6">

                <!-- ===================== Управление ===================== -->

                <div class="mb-3 flex flex-col gap-3
                            sm:flex-row sm:items-center sm:justify-between">
                    <button
                        v-if="notificationsCount"
                        type="button"
                        class="inline-flex items-center justify-center
                               rounded-sm border border-gray-300 bg-white
                               px-2 py-1 text-sm font-medium text-gray-700
                               shadow-sm transition hover:bg-gray-100 focus:outline-none
                               dark:border-gray-600 dark:bg-gray-800 dark:text-gray-200
                               dark:hover:bg-gray-900"
                        @click="markAllAsRead"
                    >
                        {{ t('markMineAsRead') }}
                    </button>

                    <ProcessingModeSwitcher
                        setting-key="adminNotificationsProcessingMode"
                        :mode="adminNotificationsProcessingMode"
                        :use-server-processing="useServerProcessing"
                        :total="notificationsCount"
                    />
                </div>

                <!-- ===================== Поиск ===================== -->

                <SearchInput
                    v-if="notificationsCount && !useServerProcessing"
                    v-model="searchQuery"
                />

                <ServerSearchInput
                    v-if="notificationsCount && useServerProcessing"
                    v-model="searchQuery"
                />

                <!-- ===================== Количество / сортировка ===================== -->

                <div
                    v-if="notificationsCount"
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
                        update-route="admin.settings.updateAdminCountNotifications"
                    />

                    <SortSelect
                        v-model:sortParam="sortParam"
                    />
                </div>

                <!-- ===================== Фильтры ===================== -->

                <NotificationFilters
                    v-model="notificationFilters"
                    @apply="applyFilters"
                    @reset="resetFilters"
                />

                <!-- ===================== Счётчик / вид ===================== -->

                <div
                    v-if="notificationsCount"
                    class="flex flex-col items-center justify-between gap-3 lg:flex-row"
                >
                    <div class="flex flex-wrap items-center gap-3">
                        <CountTable>
                            {{ filteredCount }}
                        </CountTable>

                        <span
                            v-if="filteredCount !== totalNotificationsCount"
                            class="text-xs text-gray-500 dark:text-gray-400"
                        >
                            Всего доступно:
                            {{ totalNotificationsCount }}
                        </span>
                    </div>

                    <ToggleViewButton
                        v-model:viewMode="viewMode"
                    />
                </div>

                <!-- ===================== Верхняя пагинация ===================== -->

                <div
                    v-if="notificationsCount"
                    class="mt-3 flex flex-col items-center justify-center md:flex-row"
                >
                    <Pagination
                        v-if="!useServerProcessing"
                        :current-page="currentPage"
                        :items-per-page="itemsPerPage"
                        :total-items="filteredNotifications.length"
                        @update:currentPage="currentPage = $event"
                    />

                    <AdminServerPagination
                        v-else
                        :pagination="notifications"
                    />
                </div>

                <!-- ===================== Пустой список ===================== -->

                <div
                    v-if="!displayedNotifications.length"
                    class="py-12 text-center text-gray-500 dark:text-gray-300"
                >
                    <template v-if="hasActiveFilters || searchQuery">
                        Уведомления по заданным условиям не найдены.
                    </template>

                    <template v-else>
                        Уведомления не найдены.
                    </template>
                </div>

                <!-- ===================== Таблица ===================== -->

                <NotificationTable
                    v-else-if="viewMode === 'table'"
                    :notifications="displayedNotifications"
                    @open="openNotification"
                    @read="markAsRead"
                    @delete="confirmDelete"
                />

                <!-- ===================== Карточки ===================== -->

                <NotificationCardGrid
                    v-else
                    :notifications="displayedNotifications"
                    @open="openNotification"
                    @read="markAsRead"
                    @delete="confirmDelete"
                />

                <!-- ===================== Нижняя пагинация ===================== -->

                <div
                    v-if="displayedNotifications.length"
                    class="mt-4 flex flex-col items-center justify-center md:flex-row"
                >
                    <Pagination
                        v-if="!useServerProcessing"
                        :current-page="currentPage"
                        :items-per-page="itemsPerPage"
                        :total-items="filteredNotifications.length"
                        @update:currentPage="currentPage = $event"
                    />

                    <AdminServerPagination
                        v-else
                        :pagination="notifications"
                    />
                </div>
            </div>
        </div>

        <!-- ===================== Удаление ===================== -->

        <DangerModal
            :show="showDeleteModal"
            @close="closeDeleteModal"
            :onCancel="closeDeleteModal"
            :onConfirm="deleteNotification"
        />
    </AdminLayout>
</template>
