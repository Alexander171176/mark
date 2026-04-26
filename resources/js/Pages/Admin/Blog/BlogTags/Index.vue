<script setup>
/**
 * @version PulsarCMS 1.0
 * @author Александр Косолапов
 *
 * Теги блога — Index
 * Новая мультиязычная архитектура:
 * - blog_tags
 * - blog_tag_translations
 */
import { defineProps, ref, watch, computed } from 'vue'
import { useI18n } from 'vue-i18n'
import { useToast } from 'vue-toastification'
import { router, usePage } from '@inertiajs/vue3'

import AdminLayout from '@/Layouts/AdminLayout.vue'
import TitlePage from '@/Components/Admin/Headlines/TitlePage.vue'
import DefaultButton from '@/Components/Admin/Buttons/DefaultButton.vue'
import DangerModal from '@/Components/Admin/Modal/DangerModal.vue'
import CountTable from '@/Components/Admin/Count/CountTable.vue'
import BulkActionSelect from '@/Components/Admin/Blog/Tag/Select/BulkActionSelect.vue'
import ToggleViewButton from '@/Components/Admin/Buttons/ToggleViewButton.vue'
import SearchInput from '@/Components/Admin/Search/SearchInput.vue'
import Pagination from '@/Components/Admin/Pagination/Pagination.vue'
import ItemsPerPageSelect from '@/Components/Admin/Select/ItemsPerPageSelect.vue'
import SortSelect from '@/Components/Admin/Blog/Tag/Sort/SortSelect.vue'
import TagTable from '@/Components/Admin/Blog/Tag/Table/TagTable.vue'
import TagCardGrid from '@/Components/Admin/Blog/Tag/View/TagCardGrid.vue'

const { t, locale } = useI18n()
const toast = useToast()

/** Данные текущей страницы Inertia */
const page = usePage()

/** Props приходят из BlogTagController@index */
const props = defineProps({
    tags: { type: Array, default: () => [] },
    tagsCount: { type: Number, default: 0 },

    adminCountTags: { type: Number, default: 15 },
    adminSortTags: { type: String, default: 'idDesc' },

    currentLocale: { type: String, default: 'ru' },
    availableLocales: { type: Array, default: () => ['ru', 'en', 'kk'] },

    search: { type: String, default: '' },
    sortParam: { type: String, default: '' },
    errors: { type: Object, default: () => ({}) },
})

/** Проверяем, является ли текущий пользователь администратором */
const isAdmin = computed(() => {
    const roles = page.props?.auth?.user?.roles || []
    return roles.some((role) => role?.name === 'admin')
})

/** Получение текущего перевода тега */
const getTagTranslation = (tag) => {
    return tag?.translation || {}
}

/** Название тега в текущей локали */
const getTagName = (tag) => {
    return getTagTranslation(tag)?.name || `ID: ${tag?.id}`
}

/** Краткое описание тега в текущей локали */
const getTagShort = (tag) => {
    return getTagTranslation(tag)?.short || ''
}

/** Локаль текущего перевода тега */
const getTagLocale = (tag) => {
    return getTagTranslation(tag)?.locale || props.currentLocale || ''
}

/** Нормализация строки */
const normalize = (value) => (value ?? '').toString().trim().toLowerCase()

/** Безопасное приведение статуса модерации к числу */
const moderationNum = (value) => {
    const number = Number(value)
    return Number.isFinite(number) ? number : 0
}

/**
 * Режим отображения:
 * - cards = карточки
 * - table = таблица с drag-and-drop
 */
const viewMode = ref(localStorage.getItem('admin_view_mode_tags') || 'cards')

watch(viewMode, (value) => {
    localStorage.setItem('admin_view_mode_tags', value)
})

/** Количество элементов на странице */
const itemsPerPage = ref(props.adminCountTags || 15)

watch(itemsPerPage, (newVal) => {
    router.put(
        route('admin.settings.updateAdminCountTags'),
        { value: newVal },
        {
            preserveScroll: true,
            preserveState: true,
            onSuccess: () => toast.info(`Показ ${newVal} элементов на странице.`),
            onError: (errors) => toast.error(errors.value || 'Ошибка обновления кол-ва элементов.')
        }
    )
})

/** Параметр сортировки */
const sortParam = ref(props.sortParam || props.adminSortTags || 'idDesc')

watch(sortParam, (newVal) => {
    router.put(
        route('admin.settings.updateAdminSortTags'),
        { value: newVal },
        {
            preserveScroll: true,
            preserveState: true,
            onSuccess: () => toast.info('Сортировка успешно изменена'),
            onError: (errors) => toast.error(errors.value || 'Ошибка обновления сортировки.')
        }
    )
})

/** Локальная копия тегов */
const localTags = ref([])

watch(
    () => props.tags,
    (newVal) => {
        localTags.value = JSON.parse(JSON.stringify(newVal || []))
    },
    { immediate: true, deep: true }
)

/** Модальное подтверждение удаления */
const showConfirmDeleteModal = ref(false)
const tagToDeleteId = ref(null)
const tagToDeleteName = ref('')

/** Открываем модалку удаления */
const confirmDelete = (tagOrId, name = null) => {
    if (typeof tagOrId === 'object') {
        tagToDeleteId.value = tagOrId.id
        tagToDeleteName.value = name || getTagName(tagOrId)
    } else {
        tagToDeleteId.value = tagOrId
        tagToDeleteName.value = name || `ID: ${tagOrId}`
    }

    showConfirmDeleteModal.value = true
}

/** Закрываем модалку удаления */
const closeModal = () => {
    showConfirmDeleteModal.value = false
    tagToDeleteId.value = null
    tagToDeleteName.value = ''
}

/** Удаление одного тега */
const deleteTag = () => {
    if (tagToDeleteId.value === null) return

    const idToDelete = tagToDeleteId.value
    const nameToDelete = tagToDeleteName.value

    router.delete(route('admin.blogTags.destroy', { blogTag: idToDelete }), {
        preserveScroll: true,
        preserveState: false,
        onSuccess: () => {
            toast.success(`Тег "${nameToDelete || 'ID: ' + idToDelete}" удалён.`)
        },
        onError: (errors) => {
            const errorKey = Object.keys(errors)[0]
            const errorMsg = errors.general || errors[errorKey] || 'Произошла ошибка при удалении.'
            toast.error(`${errorMsg} (Тег: ${nameToDelete || 'ID: ' + idToDelete})`)
        },
        onFinish: () => closeModal()
    })
}

/** Локальное обновление одного тега */
const patchLocalTag = (tagId, callback) => {
    const index = localTags.value.findIndex((tag) => tag.id === tagId)

    if (index !== -1) {
        callback(localTags.value[index])
    }
}

/** Переключение активности одного тега */
const toggleActivity = (tag) => {
    const newActivity = !tag.activity
    const name = getTagName(tag)
    const actionText = newActivity ? t('activated') : t('deactivated')

    router.put(
        route('admin.actions.blogTags.updateActivity', { blogTag: tag.id }),
        { activity: newActivity },
        {
            preserveScroll: true,
            preserveState: true,
            onSuccess: () => {
                patchLocalTag(tag.id, (node) => {
                    node.activity = newActivity
                })

                toast.success(`Тег "${name}" ${actionText}.`)
            },
            onError: (errors) => {
                toast.error(errors.activity || errors.general || `Ошибка изменения активности для "${name}".`)
            }
        }
    )
}

/** Поиск */
const searchQuery = ref(props.search || '')
const currentPage = ref(1)

/** Локальная сортировка */
const sortTags = (tags) => {
    const list = (tags || []).slice()

    if (sortParam.value === 'ownerNameAsc') {
        return list.sort((a, b) => normalize(a?.owner?.name).localeCompare(normalize(b?.owner?.name), locale.value))
    }

    if (sortParam.value === 'ownerNameDesc') {
        return list.sort((a, b) => normalize(b?.owner?.name).localeCompare(normalize(a?.owner?.name), locale.value))
    }

    if (sortParam.value === 'ownerEmailAsc') {
        return list.sort((a, b) => normalize(a?.owner?.email).localeCompare(normalize(b?.owner?.email), locale.value))
    }

    if (sortParam.value === 'ownerEmailDesc') {
        return list.sort((a, b) => normalize(b?.owner?.email).localeCompare(normalize(a?.owner?.email), locale.value))
    }

    if (sortParam.value === 'idAsc') {
        return list.sort((a, b) => (a.id ?? 0) - (b.id ?? 0))
    }

    if (sortParam.value === 'idDesc') {
        return list.sort((a, b) => (b.id ?? 0) - (a.id ?? 0))
    }

    if (sortParam.value === 'sortAsc') {
        return list.sort((a, b) => (a.sort ?? 0) - (b.sort ?? 0))
    }

    if (sortParam.value === 'sortDesc') {
        return list.sort((a, b) => (b.sort ?? 0) - (a.sort ?? 0))
    }

    if (sortParam.value === 'activity') {
        return list.filter((tag) => !!tag.activity)
    }

    if (sortParam.value === 'inactive') {
        return list.filter((tag) => !tag.activity)
    }

    if (sortParam.value === 'locale') {
        return list.sort((a, b) => getTagLocale(a).localeCompare(getTagLocale(b), locale.value))
    }

    if (sortParam.value === 'viewsDesc') {
        return list.sort((a, b) => (b.views ?? 0) - (a.views ?? 0))
    }

    if (sortParam.value === 'viewsAsc') {
        return list.sort((a, b) => (a.views ?? 0) - (b.views ?? 0))
    }

    if (sortParam.value === 'nameAsc') {
        return list.sort((a, b) => normalize(getTagName(a)).localeCompare(normalize(getTagName(b)), locale.value))
    }

    if (sortParam.value === 'nameDesc') {
        return list.sort((a, b) => normalize(getTagName(b)).localeCompare(normalize(getTagName(a)), locale.value))
    }

    if (sortParam.value === 'moderation_pending') {
        return list.filter((tag) => moderationNum(tag?.moderation_status) === 0)
    }

    if (sortParam.value === 'moderation_approved') {
        return list.filter((tag) => moderationNum(tag?.moderation_status) === 1)
    }

    if (sortParam.value === 'moderation_rejected') {
        return list.filter((tag) => moderationNum(tag?.moderation_status) === 2)
    }

    if (sortParam.value === 'moderation_statusAsc') {
        return list.sort((a, b) => moderationNum(a?.moderation_status) - moderationNum(b?.moderation_status))
    }

    if (sortParam.value === 'moderation_statusDesc') {
        return list.sort((a, b) => moderationNum(b?.moderation_status) - moderationNum(a?.moderation_status))
    }

    return list
}

/** Фильтрация тегов */
const filteredTags = computed(() => {
    let filtered = localTags.value || []
    const query = normalize(searchQuery.value)

    if (!query) {
        return sortTags(filtered)
    }

    filtered = filtered.filter((tag) => {
        const name = normalize(getTagName(tag))
        const short = normalize(getTagShort(tag))
        const slug = normalize(tag?.slug)
        const ownerName = normalize(tag?.owner?.name)
        const ownerEmail = normalize(tag?.owner?.email)

        return name.includes(query)
            || short.includes(query)
            || slug.includes(query)
            || ownerName.includes(query)
            || ownerEmail.includes(query)
    })

    return sortTags(filtered)
})

/** Пагинация */
const paginatedTags = computed(() => {
    const perPage = Number(itemsPerPage.value || 10)
    const start = (currentPage.value - 1) * perPage

    return filteredTags.value.slice(start, start + perPage)
})

watch([itemsPerPage, searchQuery], () => {
    currentPage.value = 1
})

/** Выбранные теги */
const selectedTags = ref([])

/** Выбрать/снять все теги */
const toggleAll = (payload) => {
    const checked = payload?.checked ?? payload?.target?.checked ?? false
    const ids = payload?.ids ?? paginatedTags.value.map((tag) => tag.id)

    if (checked) {
        selectedTags.value = [...new Set([...selectedTags.value, ...ids])]
    } else {
        selectedTags.value = selectedTags.value.filter((id) => !ids.includes(id))
    }
}

/** Выбрать или снять один тег */
const toggleSelectTag = (tagId) => {
    const index = selectedTags.value.indexOf(tagId)

    if (index > -1) {
        selectedTags.value.splice(index, 1)
    } else {
        selectedTags.value.push(tagId)
    }
}

/** Массовое включение/выключение активности */
const bulkToggleActivity = (newActivity) => {
    if (!selectedTags.value.length) {
        toast.warning('Выберите теги для активации/деактивации')
        return
    }

    const idsToUpdate = [...selectedTags.value]

    router.put(
        route('admin.actions.blogTags.bulkUpdateActivity'),
        {
            ids: idsToUpdate,
            activity: newActivity
        },
        {
            preserveScroll: true,
            preserveState: true,
            onSuccess: () => {
                localTags.value = localTags.value.map((tag) => {
                    if (idsToUpdate.includes(tag.id)) {
                        return { ...tag, activity: newActivity }
                    }

                    return tag
                })

                selectedTags.value = []
                toast.success('Активность тегов массово обновлена')
            },
            onError: (errors) => {
                const msg = errors?.ids || errors?.activity || errors?.general || 'Ошибка массового обновления активности'
                toast.error(msg)
            }
        }
    )
}

/** Массовое удаление */
const bulkDelete = () => {
    if (!selectedTags.value.length) {
        toast.warning('Выберите хотя бы один тег для удаления.')
        return
    }

    if (!confirm('Вы уверены, что хотите удалить выбранные теги?')) return

    router.delete(route('admin.actions.blogTags.bulkDestroy'), {
        data: { ids: selectedTags.value },
        preserveScroll: true,
        preserveState: false,
        onSuccess: () => {
            selectedTags.value = []
            toast.success('Массовое удаление тегов успешно завершено.')
        },
        onError: (errors) => {
            const errorKey = Object.keys(errors)[0]
            toast.error(errors[errorKey] || 'Произошла ошибка при удалении тегов.')
        }
    })
}

/** Обработка select массовых действий */
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

/** Одобрение / отклонение тега */
const approveTag = (tag, status = 1, note = '') => {
    if (!tag?.id) return

    router.put(
        route('admin.actions.blogTags.approve', { blogTag: tag.id }),
        {
            moderation_status: status,
            moderation_note: note
        },
        {
            preserveScroll: true,
            preserveState: true,
            onSuccess: () => {
                patchLocalTag(tag.id, (node) => {
                    node.moderation_status = status
                    node.is_approved = status === 1
                    node.moderation_note = note
                })

                toast.success(status === 1 ? 'Тег одобрен' : 'Тег отклонён')
            },
            onError: () => toast.error('Ошибка модерации тега')
        }
    )
}

/** Обновление сортировки */
const handleSortOrderUpdate = (newOrderIds) => {
    const items = newOrderIds.map((id, index) => ({
        id,
        sort: index
    }))

    if (!items.length) return

    router.put(
        route('admin.actions.blogTags.updateSortBulk'),
        { items },
        {
            preserveScroll: true,
            preserveState: true,
            onSuccess: () => toast.success('Сортировка тегов обновлена'),
            onError: (errors) => {
                console.error('Ошибка сортировки тегов:', errors)
                toast.error(errors.message || 'Ошибка обновления сортировки')
            }
        }
    )
}
</script>

<template>
    <AdminLayout :title="t('tags')">
        <template #header>
            <TitlePage>{{ t('tags') }}</TitlePage>
        </template>

        <div class="px-2 py-2 w-full max-w-12xl mx-auto">
            <div
                class="p-4 bg-slate-50 dark:bg-slate-700
                       border border-blue-400 dark:border-blue-200
                       overflow-hidden shadow-md shadow-gray-500 dark:shadow-slate-400
                       bg-opacity-95 dark:bg-opacity-95"
            >
                <div class="sm:flex sm:justify-between sm:items-center mb-3">
                    <DefaultButton :href="route('admin.blogTags.create')">
                        <template #icon>
                            <svg class="w-4 h-4 fill-current opacity-50 shrink-0" viewBox="0 0 16 16">
                                <path
                                    d="M15 7H9V1c0-.6-.4-1-1-1S7 .4 7 1v6H1c-.6 0-1 .4-1 1s.4 1 1 1h6v6c0 .6.4 1 1 1s1-.4 1-1V9h6c.6 0 1-.4 1-1s-.4-1-1-1z"
                                />
                            </svg>
                        </template>

                        {{ t('addTag') }}
                    </DefaultButton>
                </div>

                <SearchInput
                    v-if="tagsCount"
                    v-model="searchQuery"
                />

                <div
                    v-if="tagsCount"
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
                    v-if="tagsCount"
                    class="flex items-center justify-between mb-3"
                >
                    <CountTable>{{ tagsCount }}</CountTable>

                    <BulkActionSelect
                        v-if="tagsCount"
                        @change="handleBulkAction"
                    />

                    <ToggleViewButton v-model:viewMode="viewMode" />
                </div>

                <!-- Таблица -->
                <TagTable
                    v-if="viewMode === 'table'"
                    :tags="paginatedTags"
                    :selected-tags="selectedTags"
                    :is-admin="isAdmin"
                    @toggle-activity="toggleActivity"
                    @delete="confirmDelete"
                    @update-sort-order="handleSortOrderUpdate"
                    @toggle-select="toggleSelectTag"
                    @toggle-all="toggleAll"
                    @approve="approveTag"
                />

                <!-- Карточки -->
                <TagCardGrid
                    v-else
                    :tags="paginatedTags"
                    :selected-tags="selectedTags"
                    :is-admin="isAdmin"
                    @toggle-activity="toggleActivity"
                    @delete="confirmDelete"
                    @update-sort-order="handleSortOrderUpdate"
                    @toggle-select="toggleSelectTag"
                    @toggle-all="toggleAll"
                    @approve="approveTag"
                />

                <div
                    v-if="tagsCount"
                    class="flex justify-center items-center flex-col md:flex-row mt-3"
                >
                    <Pagination
                        :current-page="currentPage"
                        :items-per-page="itemsPerPage"
                        :total-items="filteredTags.length"
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
            :onConfirm="deleteTag"
            :cancelText="t('cancel')"
            :confirmText="t('yesDelete')"
        />
    </AdminLayout>
</template>
