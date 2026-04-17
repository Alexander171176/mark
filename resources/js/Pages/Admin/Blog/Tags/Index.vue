<script setup>
/**
 * @version PulsarCMS 1.0
 * @author Александр Косолапов
 * Теги — список + локали + модерация
 */
import { defineProps, ref, computed, watch } from 'vue'
import { useI18n } from 'vue-i18n'
import { useToast } from 'vue-toastification'
import { router, Link, usePage } from '@inertiajs/vue3'

import AdminLayout from '@/Layouts/AdminLayout.vue'
import TitlePage from '@/Components/Admin/Headlines/TitlePage.vue'
import DefaultButton from '@/Components/Admin/Buttons/DefaultButton.vue'
import DangerModal from '@/Components/Admin/Modal/DangerModal.vue'
import Pagination from '@/Components/Admin/Pagination/Pagination.vue'
import ItemsPerPageSelect from '@/Components/Admin/Select/ItemsPerPageSelect.vue'
import SearchInput from '@/Components/Admin/Search/SearchInput.vue'
import SortSelect from '@/Components/Admin/Tag/Sort/SortSelect.vue'
import TagTable from '@/Components/Admin/Tag/Table/TagTable.vue'
import TagCardGrid from '@/Components/Admin/Tag/View/TagCardGrid.vue'
import CountTable from '@/Components/Admin/Count/CountTable.vue'
import BulkActionSelect from '@/Components/Admin/Tag/Select/BulkActionSelect.vue'
import ToggleViewButton from '@/Components/Admin/Buttons/ToggleViewButton.vue'

const { t, locale } = useI18n()
const toast = useToast()
const page = usePage()

/** Роль пользователя */
const isAdmin = computed(() => {
    const roles = page.props?.auth?.user?.roles || []
    return roles.some(r => r?.name === 'admin')
})

/** Props */
const props = defineProps({
    tags: Array,
    tagsCount: Number,

    adminCountTags: Number,
    adminSortTags: String,

    currentLocale: String,
    availableLocales: Array,

    errors: Object,
})

/** Вид: table/cards */
const viewMode = ref(localStorage.getItem('admin_view_mode') || 'table')
watch(viewMode, (val) => localStorage.setItem('admin_view_mode', val))

/** Локальная копия списка тегов (для оптимистичных обновлений) */
const localTags = ref([])
watch(
    () => props.tags,
    (newVal) => {
        localTags.value = JSON.parse(JSON.stringify(newVal || []))
    },
    { immediate: true, deep: true }
)

/** Пагинация */
const itemsPerPage = ref(props.adminCountTags)
watch(itemsPerPage, (newVal) => {
    router.put(route('admin.settings.updateAdminCountTags'), { value: newVal }, {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => toast.info(`Показ ${newVal} элементов на странице.`),
        onError: (errors) => toast.error(errors.value || 'Ошибка обновления кол-ва элементов.'),
    })
})

/** Параметры сортировки */
const sortParam = ref(props.adminSortTags)
watch(sortParam, (newVal) => {
    router.put(route('admin.settings.updateAdminSortTags'), { value: newVal }, {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => toast.info('Сортировка успешно изменена'),
        onError: (errors) => toast.error(errors.value || 'Ошибка обновления сортировки.'),
    })
})

/** Поиск/пагинация */
const currentPage = ref(1)
const searchQuery = ref('')

watch([itemsPerPage, searchQuery], () => {
    currentPage.value = 1
})

const normalize = (v) => (v ?? '').toString().trim().toLowerCase()
const moderationNum = (v) => {
    const n = Number(v)
    return Number.isFinite(n) ? n : 0
}

/** Сортировка + фильтры (включая модерацию + owner) */
const sortTags = (tags) => {
    const list = (tags || []).slice()

    // owner сортировки
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

    // id
    if (sortParam.value === 'idAsc') return list.sort((a, b) => (a.id ?? 0) - (b.id ?? 0))
    if (sortParam.value === 'idDesc') return list.sort((a, b) => (b.id ?? 0) - (a.id ?? 0))

    // flags/filters
    if (sortParam.value === 'activity') return list.filter(t => !!t.activity)
    if (sortParam.value === 'inactive') return list.filter(t => !t.activity)

    // locale
    if (sortParam.value === 'locale') {
        return list.sort((a, b) => {
            if ((a.locale || '') < (b.locale || '')) return 1
            if ((a.locale || '') > (b.locale || '')) return -1
            return 0
        })
    }

    // views
    if (sortParam.value === 'views') return list.sort((a, b) => (b.views ?? 0) - (a.views ?? 0))

    // moderation filters
    if (sortParam.value === 'moderation_pending') return list.filter(t => moderationNum(t?.moderation_status) === 0)
    if (sortParam.value === 'moderation_approved') return list.filter(t => moderationNum(t?.moderation_status) === 1)
    if (sortParam.value === 'moderation_rejected') return list.filter(t => moderationNum(t?.moderation_status) === 2)

    // moderation sorting
    if (sortParam.value === 'moderation_statusAsc') {
        return list.sort((a, b) => moderationNum(a?.moderation_status) - moderationNum(b?.moderation_status))
    }
    if (sortParam.value === 'moderation_statusDesc') {
        return list.sort((a, b) => moderationNum(b?.moderation_status) - moderationNum(a?.moderation_status))
    }

    // fallback
    return list.sort((a, b) => {
        const key = sortParam.value
        const av = a?.[key]
        const bv = b?.[key]
        if (av < bv) return -1
        if (av > bv) return 1
        return 0
    })
}

/** Фильтр поиска: name + owner.name + owner.email */
const filteredTags = computed(() => {
    let filtered = localTags.value || []
    const q = normalize(searchQuery.value)

    if (q) {
        filtered = filtered.filter(tag => {
            const name = normalize(tag?.name)
            const ownerName = normalize(tag?.owner?.name)
            const ownerEmail = normalize(tag?.owner?.email)
            return name.includes(q) || ownerName.includes(q) || ownerEmail.includes(q)
        })
    }

    return sortTags(filtered)
})

/** Пагинация */
const paginatedTags = computed(() => {
    const per = Number(itemsPerPage.value || 10)
    const start = (currentPage.value - 1) * per
    return filteredTags.value.slice(start, start + per)
})

/** Модалка удаления */
const showConfirmDeleteModal = ref(false)
const tagToDeleteId = ref(null)
const tagToDeleteName = ref('')

const confirmDelete = (id, name) => {
    tagToDeleteId.value = id
    tagToDeleteName.value = name
    showConfirmDeleteModal.value = true
}

const closeModal = () => {
    showConfirmDeleteModal.value = false
    tagToDeleteId.value = null
    tagToDeleteName.value = ''
}

const deleteTag = () => {
    if (tagToDeleteId.value === null) return

    const idToDelete = tagToDeleteId.value
    const nameToDelete = tagToDeleteName.value

    router.delete(route('admin.tags.destroy', { tag: idToDelete }), {
        preserveScroll: true,
        preserveState: false,
        onSuccess: () => {
            closeModal()
            toast.success(`Тег "${nameToDelete || 'ID: ' + idToDelete}" удален.`)
        },
        onError: (errors) => {
            closeModal()
            const errorMsg = errors.general || errors[Object.keys(errors)[0]] || 'Произошла ошибка при удалении.'
            toast.error(`${errorMsg} (Тег: ${nameToDelete || 'ID: ' + idToDelete})`)
        },
        onFinish: () => {
            tagToDeleteId.value = null
            tagToDeleteName.value = ''
        }
    })
}

/** Оптимистичные патчи */
const patchLocal = (id, patch) => {
    const idx = localTags.value.findIndex(t => t.id === id)
    if (idx !== -1) localTags.value[idx] = { ...localTags.value[idx], ...patch }
}

/** toggle activity */
const toggleActivity = (tag) => {
    const newActivity = !tag.activity
    const actionText = newActivity ? t('activated') : t('deactivated')

    router.put(
        route('admin.actions.tags.updateActivity', { tag: tag.id }),
        { activity: newActivity },
        {
            preserveScroll: true,
            preserveState: true,
            onSuccess: () => {
                patchLocal(tag.id, { activity: newActivity })
                toast.success(`Тег "${tag.name}" ${actionText}.`)
            },
            onError: (errors) => {
                toast.error(errors.activity || errors.general || `Ошибка изменения активности для "${tag.name}".`)
            },
        }
    )
}

/** Модерация тега (approve/reject) */
const approveTag = (tag, status = 1, note = '') => {
    if (!tag?.id) return

    router.put(
        route('admin.actions.tags.approve', { tag: tag.id }),
        { moderation_status: status, moderation_note: note },
        {
            preserveScroll: true,
            preserveState: true,
            onSuccess: () => {
                patchLocal(tag.id, {
                    moderation_status: status,
                    is_approved: status === 1,
                    moderation_note: note,
                })
                toast.success(status === 1 ? 'Тег одобрен' : 'Тег отклонён')
            },
            onError: () => toast.error('Ошибка модерации тега'),
        }
    )
}

/** Drag & Drop sort (bulk) — новая схема: items + locale */
const handleSortOrderUpdate = (orderedIds) => {
    const startSort = (currentPage.value - 1) * Number(itemsPerPage.value || 10)

    const sortData = orderedIds.map((id, index) => ({
        id,
        sort: startSort + index + 1,
    }))

    router.put(
        route('admin.actions.tags.updateSortBulk'),
        { items: sortData, locale: props.currentLocale },
        {
            preserveScroll: true,
            preserveState: true,
            onSuccess: () => toast.success('Порядок тегов успешно обновлен.'),
            onError: (errors) => {
                console.error('Ошибка обновления сортировки:', errors)
                toast.error(errors.message || errors.general || 'Не удалось обновить порядок тегов.')
                router.reload({ only: ['tags'], preserveScroll: true })
            },
        }
    )
}

/** Массовые действия */
const selectedTags = ref([])

const toggleAll = ({ ids, checked }) => {
    if (checked) {
        selectedTags.value = [...new Set([...selectedTags.value, ...ids])]
    } else {
        selectedTags.value = selectedTags.value.filter(id => !ids.includes(id))
    }
}

const toggleSelectTag = (tagId) => {
    const index = selectedTags.value.indexOf(tagId)
    if (index > -1) selectedTags.value.splice(index, 1)
    else selectedTags.value.push(tagId)
}

const bulkToggleActivity = (newActivity) => {
    if (!selectedTags.value.length) {
        toast.warning('Выберите теги для активации/деактивации')
        return
    }

    const idsToUpdate = [...selectedTags.value]

    router.put(
        route('admin.actions.tags.bulkUpdateActivity'),
        { ids: idsToUpdate, activity: newActivity },
        {
            preserveScroll: true,
            preserveState: true,
            onSuccess: () => {
                localTags.value = localTags.value.map(tg => {
                    if (idsToUpdate.includes(tg.id)) return { ...tg, activity: newActivity }
                    return tg
                })
                selectedTags.value = []
                toast.success('Активность тегов массово обновлена')
            },
            onError: (errors) => {
                const msg =
                    errors?.ids || errors?.activity || errors?.general ||
                    'Не удалось массово обновить активность тегов'
                toast.error(msg)
            },
        }
    )
}

const bulkDelete = () => {
    if (!selectedTags.value.length) {
        toast.warning('Выберите хотя бы один тег для удаления.')
        return
    }
    if (!confirm('Вы уверены, что хотите удалить выбранные теги?')) return

    router.delete(route('admin.actions.tags.bulkDestroy'), {
        data: { ids: selectedTags.value },
        preserveScroll: true,
        preserveState: false,
        onSuccess: () => {
            selectedTags.value = []
            toast.success('Массовое удаление тегов успешно завершено.')
        },
        onError: (errors) => {
            console.error('Ошибка массового удаления:', errors)
            const errorKey = Object.keys(errors)[0]
            toast.error(errors[errorKey] || 'Произошла ошибка при удалении тегов.')
        },
    })
}

const handleBulkAction = (event) => {
    const action = event.target.value

    if (action === 'selectAll') {
        selectedTags.value = paginatedTags.value.map(tg => tg.id)
    } else if (action === 'deselectAll') {
        selectedTags.value = []
    } else if (action === 'activate') {
        bulkToggleActivity(true)
    } else if (action === 'deactivate') {
        bulkToggleActivity(false)
    } else if (action === 'delete') {
        bulkDelete()
    }

    event.target.value = ''
}

/** Таб локалей */
const localeLink = (loc) => route('admin.tags.index', { locale: loc })
</script>

<template>
    <AdminLayout :title="t('tags')">
        <template #header>
            <TitlePage>
                {{ t('tags') }}
            </TitlePage>
        </template>

        <div class="px-2 py-2 w-full max-w-12xl mx-auto">
            <div class="p-4 bg-slate-50 dark:bg-slate-700 border border-blue-400 dark:border-blue-200
                  overflow-hidden shadow-md shadow-gray-500 dark:shadow-slate-400
                  bg-opacity-95 dark:bg-opacity-95">

                <div class="sm:flex sm:justify-between sm:items-center mb-2">
                    <DefaultButton :href="route('admin.tags.create', { locale: currentLocale })">
                        <template #icon>
                            <svg class="w-4 h-4 fill-current opacity-50 shrink-0" viewBox="0 0 16 16">
                                <path
                                    d="M15 7H9V1c0-.6-.4-1-1-1S7 .4 7 1v6H1c-.6 0-1 .4-1 1s.4 1 1 1h6v6c0 .6.4 1 1 1s1-.4 1-1V9h6c.6 0 1-.4 1-1s-.4-1-1-1z"></path>
                            </svg>
                        </template>
                        {{ t('addTag') }}
                    </DefaultButton>

                    <BulkActionSelect v-if="tagsCount" @change="handleBulkAction" />
                </div>

                <!-- Локали + счётчик + переключатель вида -->
                <div class="flex items-center justify-between mt-5">
                    <div class="flex items-center justify-end space-x-2 px-3 py-1
                      border-x border-t border-gray-400 rounded-t-lg
                      bg-gray-100 dark:bg-gray-900">
            <span class="text-sm font-medium text-slate-700 dark:text-slate-200">
              {{ t('localization') }}:
            </span>

                        <template v-for="loc in availableLocales" :key="loc">
                            <Link
                                :href="localeLink(loc)"
                                :class="[
                  'px-3 py-1 text-sm font-medium rounded-sm',
                  currentLocale === loc
                    ? 'bg-blue-500 text-white'
                    : 'bg-slate-100 dark:bg-slate-900 text-slate-700 dark:text-slate-200 hover:bg-slate-300 dark:hover:bg-slate-600'
                ]"
                                preserve-scroll
                                preserve-state
                            >
                                {{ loc.toUpperCase() }}
                            </Link>
                        </template>
                    </div>

                    <div class="flex items-center space-x-3" v-if="tagsCount">
                        <CountTable>{{ tagsCount }}</CountTable>
                        <ToggleViewButton v-model:viewMode="viewMode" />
                    </div>
                </div>

                <SearchInput v-if="tagsCount" v-model="searchQuery" :placeholder="t('searchByName')" />

                <!-- Таблица / Карточки -->
                <TagTable
                    v-if="viewMode === 'table'"
                    :tags="paginatedTags"
                    :selected-tags="selectedTags"
                    :isAdmin="isAdmin"
                    @toggle-activity="toggleActivity"
                    @update-sort-order="handleSortOrderUpdate"
                    @delete="confirmDelete"
                    @toggle-select="toggleSelectTag"
                    @toggle-all="toggleAll"
                    @approve="approveTag"
                />

                <TagCardGrid
                    v-else
                    :tags="paginatedTags"
                    :selected-tags="selectedTags"
                    :isAdmin="isAdmin"
                    @toggle-activity="toggleActivity"
                    @update-sort-order="handleSortOrderUpdate"
                    @delete="confirmDelete"
                    @toggle-select="toggleSelectTag"
                    @toggle-all="toggleAll"
                    @approve="approveTag"
                />

                <div class="flex justify-between items-center flex-col md:flex-row my-1" v-if="tagsCount">
                    <ItemsPerPageSelect :items-per-page="itemsPerPage" @update:itemsPerPage="itemsPerPage = $event" />
                    <Pagination
                        :current-page="currentPage"
                        :items-per-page="itemsPerPage"
                        :total-items="filteredTags.length"
                        @update:currentPage="currentPage = $event"
                        @update:itemsPerPage="itemsPerPage = $event"
                    />
                    <SortSelect :sortParam="sortParam" @update:sortParam="val => sortParam = val" />
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
