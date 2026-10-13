<script setup>
/**
 * @version PulsarCMS 1.0
 *
 * Список слайдеров Slider.
 * - локальный поиск, сортировка и пагинация;
 * - отображение таблицей и карточками;
 * - управление активностью и массовыми действиями;
 * - изменение порядка через Drag & Drop.
 */

import { computed, ref, watch } from 'vue'
import { useI18n } from 'vue-i18n'
import { useToast } from 'vue-toastification'
import { router } from '@inertiajs/vue3'

import AdminLayout from '@/Layouts/AdminLayout.vue'
import TitlePage from '@/Components/Admin/UI/Headlines/TitlePage.vue'
import DefaultButton from '@/Components/Admin/UI/Buttons/DefaultButton.vue'
import SortSelect from '@/Components/Admin/Slider/Slider/Sort/SortSelect.vue'
import BulkActionSelect from '@/Components/Admin/Slider/Slider/Select/BulkActionSelect.vue'
import DangerModal from '@/Components/Admin/UI/Modal/DangerModal.vue'
import CountTable from '@/Components/Admin/UI/Count/CountTable.vue'
import ToggleViewButton from '@/Components/Admin/UI/Buttons/ToggleViewButton.vue'
import SearchInput from '@/Components/Admin/UI/Search/SearchInput.vue'
import Pagination from '@/Components/Admin/UI/Pagination/Pagination.vue'
import ItemsPerPageSelect from '@/Components/Admin/UI/Select/ItemsPerPageSelect.vue'

import SliderCardGrid from '@/Components/Admin/Slider/Slider/View/SliderCardGrid.vue'
import SliderTable from '@/Components/Admin/Slider/Slider/Table/SliderTable.vue'

/** Локализация и уведомления */
const { t, locale } = useI18n()
const toast = useToast()

/** Входные параметры страницы */
const props = defineProps({
    sliders: { type: [Array, Object], default: () => [] },
    currentLocale: { type: String, default: '' },
    availableLocales: { type: Array, default: () => [] },
    isAdmin: { type: Boolean, default: false },
    adminSlidersPerPage: { type: Number, default: 12 },
    adminSlidersDefaultSort: { type: String, default: 'sortAsc' },
})

/* =========================================================
 * HELPERS
 * ========================================================= */

/** Название слайдера в текущем переводе */
const getSliderTitle = slider =>
    slider?.translation?.title || `ID: ${slider?.id}`

/** Нормализация поискового значения */
const normalize = value =>
    String(value ?? '').trim().toLowerCase()

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

/** Получение текста ошибки Laravel/Inertia */
const getErrorText = (errors, fallback) => {
    const firstError = Object.values(errors || {})[0]

    return errors?.general || firstError || fallback
}

/** Ошибка, переданная Laravel через flash при успешном Inertia-ответе. */
const hasFlashError = page => {
    const error = page?.props?.flash?.error
    if (!error) return false
    toast.error(typeof error === 'string' ? error : 'Операция не выполнена.')
    return true
}

/** Сортировка ID по убыванию (вторичный критерий) */
const byIdDesc = (a, b) =>
    safeNumber(b?.id) - safeNumber(a?.id)

/** Числовое поле по возрастанию */
const byNumberAsc = field => (a, b) =>
    safeNumber(a?.[field]) - safeNumber(b?.[field]) || byIdDesc(a, b)

/** Числовое поле по убыванию */
const byNumberDesc = field => (a, b) =>
    safeNumber(b?.[field]) - safeNumber(a?.[field]) || byIdDesc(a, b)

/** Строковое поле по возрастанию */
const byStringAsc = getter => (a, b) =>
    normalize(getter(a)).localeCompare(
        normalize(getter(b)),
        locale.value
    ) || byIdDesc(a, b)

/** Строковое поле по убыванию */
const byStringDesc = getter => (a, b) =>
    normalize(getter(b)).localeCompare(
        normalize(getter(a)),
        locale.value
    ) || byIdDesc(a, b)

/* =========================================================
 * SOURCE DATA
 * ========================================================= */

/** Нормализация ответа Inertia ResourceCollection */
const slidersList = computed(() => {
    if (Array.isArray(props.sliders)) {
        return props.sliders
    }

    if (Array.isArray(props.sliders?.data)) {
        return props.sliders.data
    }

    if (Array.isArray(props.sliders?.resource)) {
        return props.sliders.resource
    }

    if (Array.isArray(props.sliders?.data?.data)) {
        return props.sliders.data.data
    }

    return []
})

/** Локальная копия списка для обновления без полной перезагрузки */
const localSliders = ref([])

/** Синхронизация с данными контроллера */
watch(
    slidersList,
    items => {
        // JSON-копирование корректно работает с реактивными Proxy Vue.
        localSliders.value = JSON.parse(
            JSON.stringify(items || [])
        )
    },
    { immediate: true, deep: true }
)

/** Обновление одной записи в локальном списке */
const patchLocalSlider = (sliderId, callback) => {
    const slider = localSliders.value.find(
        item => Number(item.id) === Number(sliderId)
    )

    if (slider) {
        callback(slider)
    }
}

/* =========================================================
 * VIEW MODE
 * ========================================================= */

/** Режим отображения таблица/карточки */
const viewMode = ref(
    localStorage.getItem('admin_view_mode_sliders') || 'cards'
)

/** Сохранение режима отображения в браузере */
watch(viewMode, value => {
    localStorage.setItem('admin_view_mode_sliders', value)
})

/* =========================================================
 * SETTINGS: ITEMS PER PAGE / SORT
 * ========================================================= */

/** Количество записей на странице */
const itemsPerPage = ref(
    Math.max(1, Number(props.adminSlidersPerPage) || 12)
)

/** Текущая сортировка */
const sortParam = ref(
    props.adminSlidersDefaultSort || 'sortAsc'
)

/** Флаг сохранения настроек */
const settingBusy = ref(false)

/** Синхронизация настройки количества из Inertia */
watch(() => props.adminSlidersPerPage, value => {
    itemsPerPage.value = Math.max(1, Number(value) || 12)
})

/** Синхронизация настройки сортировки из Inertia */
watch(() => props.adminSlidersDefaultSort, value => {
    sortParam.value = value || 'sortAsc'
})

/** Сохранение количества записей на странице */
const savePerPage = value => {
    const next = Math.max(1, Number(value) || 12)

    if (next === itemsPerPage.value || settingBusy.value) {
        return
    }

    const previous = itemsPerPage.value
    itemsPerPage.value = next
    settingBusy.value = true

    router.put(
        route('admin.settings.updateAdminCountSliders'),
        { value: next },
        {
            preserveScroll: true,
            preserveState: true,

            onSuccess: page => {
                if (hasFlashError(page)) {
                    itemsPerPage.value = previous
                    return
                }
                toast.info(`Показ ${next} слайдеров на странице.`)
            },

            onError: errors => {
                itemsPerPage.value = previous
                toast.error(getErrorText(
                    errors,
                    'Ошибка сохранения количества слайдеров.'
                ))
            },

            onFinish: () => {
                settingBusy.value = false
            },
        }
    )
}

/** Сохранение сортировки в настройках администратора */
const saveSort = value => {
    if (value === sortParam.value || settingBusy.value) {
        return
    }

    const previous = sortParam.value
    sortParam.value = value
    settingBusy.value = true

    router.put(
        route('admin.settings.updateAdminSortSliders'),
        { value },
        {
            preserveScroll: true,
            preserveState: true,

            onSuccess: page => {
                if (hasFlashError(page)) {
                    sortParam.value = previous
                    return
                }
                toast.info('Сортировка слайдеров сохранена.')
            },

            onError: errors => {
                sortParam.value = previous
                toast.error(getErrorText(
                    errors,
                    'Ошибка сохранения сортировки.'
                ))
            },

            onFinish: () => {
                settingBusy.value = false
            },
        }
    )
}

/* =========================================================
 * SEARCH / SORT / PAGINATION
 * ========================================================= */

/** Строка локального поиска */
const searchQuery = ref('')

/** Текущая страница локальной пагинации */
const currentPage = ref(1)

/** Сортировка списка слайдеров */
const sortSliders = sliders => {
    const list = [...(sliders || [])]

    const comparators = {
        sortAsc: byNumberAsc('sort'),
        sortDesc: byNumberDesc('sort'),
        idAsc: (a, b) => safeNumber(a?.id) - safeNumber(b?.id),
        idDesc: byIdDesc,
        titleAsc: byStringAsc(getSliderTitle),
        titleDesc: byStringDesc(getSliderTitle),
        slidesDesc: byNumberDesc('slides_count'),
        createdDesc: (a, b) =>
            safeDate(b?.created_at) - safeDate(a?.created_at) || byIdDesc(a, b),
    }

    return list.sort(
        comparators[sortParam.value] || comparators.sortAsc
    )
}

/** Поиск по полям слайдера, переводу и владельцу */
const filteredSliders = computed(() => {
    const query = normalize(searchQuery.value)
    let list = localSliders.value || []

    if (query) {
        list = list.filter(slider => {
            const values = [
                slider?.id,
                slider?.code,
                slider?.type,
                slider?.status,
                slider?.effect,
                slider?.translation?.title,
                slider?.translation?.subtitle,
                slider?.owner?.name,
                slider?.owner?.email,
                slider?.moderation_note,
            ]

            return values.some(
                value => normalize(value).includes(query)
            )
        })
    }

    return sortSliders(list)
})

/** Количество страниц с учётом поиска */
const totalPages = computed(() => Math.max(
    1,
    Math.ceil(filteredSliders.value.length / itemsPerPage.value)
))

/** Записи текущей страницы */
const displayedSliders = computed(() => {
    const start = (currentPage.value - 1) * itemsPerPage.value

    return filteredSliders.value.slice(
        start,
        start + itemsPerPage.value
    )
})

/** Возврат на первую страницу при изменении параметров */
watch([searchQuery, sortParam, itemsPerPage], () => {
    currentPage.value = 1
})

/** Корректировка страницы после удаления или изменения списка */
watch(totalPages, maxPage => {
    if (currentPage.value > maxPage) {
        currentPage.value = maxPage
    }
})

/* =========================================================
 * SELECTION
 * ========================================================= */

/** ID выбранных слайдеров */
const selectedIds = ref([])

/** Выбор или снятие выбора одной записи */
const toggleSelect = sliderOrId => {
    const rawId = typeof sliderOrId === 'object' ? sliderOrId?.id : sliderOrId
    const sliderId = Number(rawId)
    if (!Number.isInteger(sliderId) || sliderId <= 0) return

    if (selectedIds.value.includes(sliderId)) {
        selectedIds.value = selectedIds.value.filter(
            id => Number(id) !== Number(sliderId)
        )
    } else {
        selectedIds.value = [...selectedIds.value, sliderId]
    }
}

/** Выделение или снятие выделения с записей текущей страницы */
const toggleAll = payload => {
    const ids = payload?.ids || displayedSliders.value.map(
        slider => slider.id
    )
    const checked = typeof payload === 'boolean'
        ? payload
        : Boolean(payload?.target?.checked ?? payload?.checked)
    const selected = new Set(selectedIds.value.map(Number))

    ids.forEach(rawId => {
        const id = Number(rawId)
        if (!Number.isInteger(id) || id <= 0) return
        if (checked) {
            selected.add(id)
        } else {
            selected.delete(id)
        }
    })

    selectedIds.value = [...selected]
}

const allDisplayedSelected = computed(() => {
    const ids = displayedSliders.value.map(slider => slider.id)
    return ids.length > 0 && ids.every(id => selectedIds.value.includes(Number(id)))
})

/* =========================================================
 * SINGLE ACTIVITY
 * ========================================================= */

/** Общий флаг выполнения CRUD-действия */
const busy = ref(false)

/** Переключение активности одного слайдера */
const toggleActivity = slider => {
    if (busy.value) {
        return
    }

    const newActivity = !slider.activity
    const title = getSliderTitle(slider)
    busy.value = true

    router.put(
        route('admin.actions.sliders.updateActivity', { slider: slider.id }),
        { activity: newActivity },
        {
            preserveScroll: true,
            preserveState: true,

            onSuccess: page => {
                if (hasFlashError(page)) return
                patchLocalSlider(slider.id, item => {
                    item.activity = newActivity
                })

                toast.success(`Активность слайдера «${title}» обновлена.`)
            },

            onError: errors => {
                toast.error(getErrorText(
                    errors,
                    `Не удалось изменить активность «${title}».`
                ))
            },

            onFinish: () => {
                busy.value = false
            },
        }
    )
}

/* =========================================================
 * BULK ACTIONS
 * ========================================================= */

/** Массовая активация или деактивация выбранных слайдеров */
const bulkActivity = newActivity => {
    if (busy.value || !selectedIds.value.length) {
        return
    }

    const ids = [...selectedIds.value]
    busy.value = true

    router.put(
        route('admin.actions.sliders.bulkUpdateActivity'),
        { ids, activity: newActivity },
        {
            preserveScroll: true,
            preserveState: true,

            onSuccess: page => {
                if (hasFlashError(page)) return
                const selected = new Set(ids)

                localSliders.value.forEach(slider => {
                    if (selected.has(Number(slider.id))) {
                        slider.activity = newActivity
                    }
                })

                selectedIds.value = []
                toast.success('Активность выбранных слайдеров обновлена.')
            },

            onError: errors => {
                toast.error(getErrorText(
                    errors,
                    'Ошибка массового обновления активности.'
                ))
            },

            onFinish: () => {
                busy.value = false
            },
        }
    )
}

/** Обработка действий из BulkActionSelect */
const handleBulkAction = payload => {
    const action = typeof payload === 'string'
        ? payload
        : payload?.target?.value

    switch (action) {
        case 'selectAll':
            toggleAll({ checked: true })
            break

        case 'deselectAll':
            toggleAll({ checked: false })
            break

        case 'activate':
            bulkActivity(true)
            break

        case 'deactivate':
            bulkActivity(false)
            break

        case 'delete':
            bulkDeleteSliders()
            break
    }

    if (payload?.target) payload.target.value = ''
}

/* =========================================================
 * MODERATION
 * ========================================================= */

/** Модерация слайдера, как в MarketProduct Index. */
const approveSlider = (slider, status = 1, note = '') => {
    if (!props.isAdmin || !slider?.id || busy.value) return

    const nextStatus = Number(status)
    if (![0, 1, 2].includes(nextStatus)) return

    busy.value = true

    router.put(
        route('admin.actions.sliders.approve', { slider: slider.id }),
        { moderation_status: nextStatus, moderation_note: String(note ?? '').trim() || null },
        {
            preserveScroll: true,
            preserveState: true,
            onSuccess: page => {
                if (hasFlashError(page)) return
                // Приоритет у данных, возвращённых обновлённым ресурсом Laravel.
                const responseSliders = page?.props?.sliders
                const responseList = Array.isArray(responseSliders)
                    ? responseSliders
                    : (responseSliders?.data || [])
                const actual = Array.isArray(responseList)
                    ? responseList.find(item => Number(item.id) === Number(slider.id))
                    : null

                patchLocalSlider(slider.id, item => {
                    item.moderation_status = actual?.moderation_status ?? nextStatus
                    item.is_pending = Number(item.moderation_status) === 0
                    item.is_approved = Number(item.moderation_status) === 1
                    item.is_rejected = Number(item.moderation_status) === 2
                    item.moderation_note = actual?.moderation_note ?? (String(note ?? '').trim() || null)
                    if (actual) {
                        item.moderated_by = actual.moderated_by
                        item.moderated_at = actual.moderated_at
                    }
                })
                toast.success('Модерация слайдера обновлена.')
            },
            onError: errors => {
                toast.error(getErrorText(errors, 'Ошибка модерации слайдера.'))
            },
            onFinish: () => {
                busy.value = false
            },
        }
    )
}

/* =========================================================
 * DRAG & DROP
 * ========================================================= */

/**
 * Перестановка возможна на ЛЮБОЙ странице списка.
 * Порядок в БД изменяется только при сортировке sortAsc и без фильтра поиска.
 * Наличие локальной пагинации больше не блокирует Drag & Drop.
 */
const canReorder = computed(() => {
    return !busy.value
        && !settingBusy.value
        && !searchQuery.value.trim()
        && sortParam.value === 'sortAsc'
        && displayedSliders.value.length > 1
})

/**
 * Сохранение нового порядка, полученного от таблицы или карточек.
 *
 * При количестве до 500 записей перенумеровываем весь список, включая
 * скрытые страницы, чтобы не получить дубликаты поля sort.
 * Для больших списков изменяем только значения sort текущей страницы.
 */
const updateSortOrder = ids => {
    if (!canReorder.value || !Array.isArray(ids)) {
        return
    }

    const visible = displayedSliders.value
    const oldIds = visible.map(slider => slider.id)
    const oldSet = new Set(oldIds)

    // Запрещаем потерянные, повторяющиеся и чужие ID.
    if (
        ids.length !== oldIds.length
        || new Set(ids).size !== ids.length
        || ids.some(id => !oldSet.has(id))
    ) {
        toast.error('Некорректный порядок слайдеров.')
        return
    }

    if (ids.every((id, index) => id === oldIds[index])) {
        return
    }

    const allSorted = sortSliders(localSliders.value)
    const start = (currentPage.value - 1) * itemsPerPage.value
    const nextIds = allSorted.map(slider => slider.id)

    nextIds.splice(start, ids.length, ...ids)

    let items

    if (nextIds.length <= 500) {
        // Полная перенумерация гарантирует однозначный порядок.
        items = nextIds.map((id, index) => ({ id, sort: index }))
    } else {
        // При большом списке сохраняем исходные позиции только этой страницы.
        const slots = visible.map(slider => Number(slider.sort))

        if (new Set(slots).size !== slots.length) {
            toast.error('На странице повторяются значения sort. Сначала нормализуйте порядок.')
            return
        }

        items = ids.map((id, index) => ({ id, sort: slots[index] }))
    }

    busy.value = true

    router.put(
        route('admin.actions.sliders.updateSortBulk'),
        { items },
        {
            preserveScroll: true,
            preserveState: true,

            onSuccess: page => {
                if (hasFlashError(page)) return
                items.forEach(item => {
                    patchLocalSlider(item.id, slider => {
                        slider.sort = item.sort
                    })
                })

                toast.success('Порядок слайдеров сохранён.')
            },

            onError: errors => {
                toast.error(getErrorText(
                    errors,
                    'Не удалось сохранить порядок слайдеров.'
                ))
            },

            onFinish: () => {
                busy.value = false
            },
        }
    )
}

/* =========================================================
 * DELETE
 * ========================================================= */


/**
 * Массовое удаление выбранных слайдеров.
 */
const bulkDeleteSliders = () => {
    if (busy.value || !selectedIds.value.length) {
        return
    }

    const ids = [...selectedIds.value]

    if (!window.confirm(
        `Удалить выбранные слайдеры (${ids.length})?`
    )) {
        return
    }

    busy.value = true

    router.delete(
        route('admin.actions.sliders.bulkDestroy'),
        {
            data: { ids },

            preserveScroll: true,
            preserveState: false,

            onSuccess: page => {
                if (hasFlashError(page)) return

                selectedIds.value = []
                toast.success(page?.props?.flash?.success || 'Операция удаления завершена.')
            },

            onError: errors => {
                toast.error(getErrorText(
                    errors,
                    'Ошибка массового удаления слайдеров.'
                ))
            },

            onFinish: () => {
                busy.value = false
            },
        }
    )
}

/** Слайдер, выбранный для удаления */
const deleteTarget = ref(null)

/** Видимость модального окна подтверждения */
const showDeleteModal = ref(false)

/** Открытие окна подтверждения удаления */
const confirmDelete = slider => {
    deleteTarget.value = slider
    showDeleteModal.value = true
}

/** Закрытие модального окна */
const closeDelete = () => {
    if (busy.value) {
        return
    }

    showDeleteModal.value = false
    deleteTarget.value = null
}

/** Удаление одного слайдера */
const deleteSlider = () => {
    if (!deleteTarget.value || busy.value) {
        return
    }

    const sliderId = deleteTarget.value.id
    const title = getSliderTitle(deleteTarget.value)
    busy.value = true

    router.delete(
        route('admin.sliders.destroy', { slider: sliderId }),
        {
            preserveScroll: true,
            preserveState: false,

            onSuccess: page => {
                if (hasFlashError(page)) return
                selectedIds.value = selectedIds.value.filter(
                    id => Number(id) !== Number(sliderId)
                )

                toast.success(`Слайдер «${title}» удалён.`)
            },

            onError: errors => {
                toast.error(getErrorText(
                    errors,
                    `Не удалось удалить слайдер «${title}».`
                ))
            },

            onFinish: () => {
                busy.value = false
                closeDelete()
            },
        }
    )
}
</script>

<template>
    <AdminLayout title="Слайдеры">
        <template #header>
            <TitlePage>Слайдеры</TitlePage>
        </template>

        <div class="px-2 py-2 w-full max-w-12xl mx-auto">
            <div
                class="p-4 bg-slate-50 dark:bg-slate-700
                       border border-blue-400 dark:border-blue-200
                       overflow-hidden shadow-md shadow-gray-500 dark:shadow-slate-400
                       bg-opacity-95 dark:bg-opacity-95"
            >
                <!-- Верхняя панель: как у товаров, без переключателя обработки -->
                <div
                    class="flex flex-col gap-3 mb-3
                           sm:flex-row sm:items-center sm:justify-between"
                >
                    <DefaultButton :href="route('admin.sliders.create')">
                        Добавить слайдер
                    </DefaultButton>
                </div>

                <!-- Поиск только на frontend -->
                <SearchInput
                    v-if="localSliders.length"
                    v-model="searchQuery"
                />

                <!-- Количество записей и сортировка -->
                <div
                    v-if="localSliders.length"
                    class="flex flex-col md:flex-row
                           items-center justify-between gap-3 my-3"
                >
                    <ItemsPerPageSelect
                        :items-per-page="itemsPerPage"
                        @update:itemsPerPage="savePerPage"
                    />

                    <SortSelect
                        :sort-param="sortParam"
                        :disabled="settingBusy"
                        @update:sortParam="saveSort"
                    />
                </div>

                <!-- Счётчик, массовые действия и режим отображения -->
                <div
                    v-if="localSliders.length"
                    class="flex flex-col lg:flex-row
                           items-center justify-between gap-3"
                >
                    <CountTable>
                        {{ filteredSliders.length }} / {{ localSliders.length }}
                    </CountTable>

                    <BulkActionSelect
                        :disabled="busy || settingBusy"
                        @change="handleBulkAction"
                    />

                    <ToggleViewButton v-model:viewMode="viewMode" />
                </div>

                <!-- Верхняя пагинация -->
                <div
                    v-if="localSliders.length"
                    class="flex justify-center items-center
                           flex-col md:flex-row mt-3"
                >
                    <Pagination
                        :current-page="currentPage"
                        :items-per-page="itemsPerPage"
                        :total-items="filteredSliders.length"
                        @update:currentPage="currentPage = $event"
                    />
                </div>

                <!-- Таблица -->
                <SliderTable
                    v-if="viewMode === 'table' && displayedSliders.length"
                    :sliders="displayedSliders"
                    :selected-sliders="selectedIds"
                    :all-selected="allDisplayedSelected"
                    :is-admin="isAdmin"
                    :can-reorder="canReorder"
                    :busy="busy"
                    @toggle-select="toggleSelect"
                    @toggle-all="toggleAll"
                    @toggle-activity="toggleActivity"
                    @approve="approveSlider"
                    @delete="confirmDelete"
                    @update-sort-order="updateSortOrder"
                />

                <!-- Карточки -->
                <SliderCardGrid
                    v-else-if="viewMode !== 'table' && displayedSliders.length"
                    :sliders="displayedSliders"
                    :selected-sliders="selectedIds"
                    :all-selected="allDisplayedSelected"
                    :is-admin="isAdmin"
                    :can-reorder="canReorder"
                    :busy="busy"
                    @toggle-select="toggleSelect"
                    @toggle-all="toggleAll"
                    @toggle-activity="toggleActivity"
                    @approve="approveSlider"
                    @delete="confirmDelete"
                    @update-sort-order="updateSortOrder"
                />

                <!-- Пустой список / нет результатов поиска -->
                <div
                    v-if="!localSliders.length || !displayedSliders.length"
                    class="py-12 text-center text-slate-500 dark:text-slate-300"
                >
                    {{ t('noData') }}
                </div>

                <!-- Нижняя пагинация -->
                <div
                    v-if="localSliders.length"
                    class="flex justify-center items-center
                           flex-col md:flex-row mt-4"
                >
                    <Pagination
                        :current-page="currentPage"
                        :items-per-page="itemsPerPage"
                        :total-items="filteredSliders.length"
                        @update:currentPage="currentPage = $event"
                    />
                </div>
            </div>
        </div>

        <!-- Подтверждение удаления -->
        <DangerModal
            :show="showDeleteModal"
            :on-cancel="closeDelete"
            :on-confirm="deleteSlider"
            :cancel-text="t('cancel')"
            :confirm-text="t('yesDelete')"
            @close="closeDelete"
        >
            <template #default>
                <p class="text-sm text-slate-700 dark:text-slate-200">
                    {{ t('delete') }}
                    <strong>{{ getSliderTitle(deleteTarget) }}</strong>?
                </p>
            </template>
        </DangerModal>
    </AdminLayout>
</template>
