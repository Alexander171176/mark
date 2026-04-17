<script setup>
/**
 * @version PulsarCMS 1.0
 * @author Александр
 * Баннеры — список + локали + модерация
 */
import { defineProps, ref, computed, watch } from 'vue'
import { useI18n } from 'vue-i18n'
import { useToast } from 'vue-toastification'
import { router, Link, usePage } from '@inertiajs/vue3'
import axios from 'axios'

import AdminLayout from '@/Layouts/AdminLayout.vue'
import TitlePage from '@/Components/Admin/Headlines/TitlePage.vue'
import DefaultButton from '@/Components/Admin/Buttons/DefaultButton.vue'
import DangerModal from '@/Components/Admin/Modal/DangerModal.vue'
import Pagination from '@/Components/Admin/Pagination/Pagination.vue'
import ItemsPerPageSelect from '@/Components/Admin/Select/ItemsPerPageSelect.vue'
import SearchInput from '@/Components/Admin/Search/SearchInput.vue'
import SortSelect from '@/Components/Admin/Banner/Sort/SortSelect.vue'
import BannerTable from '@/Components/Admin/Banner/Table/BannerTable.vue'
import BannerCardGrid from '@/Components/Admin/Banner/View/BannerCardGrid.vue'
import CountTable from '@/Components/Admin/Count/CountTable.vue'
import BulkActionSelect from '@/Components/Admin/Banner/Select/BulkActionSelect.vue'
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
    banners: Array,
    bannersCount: Number,

    adminCountBanners: Number,
    adminSortBanners: String,

    currentLocale: String,
    availableLocales: Array,

    errors: Object,
})

/** Вид: table/cards */
const viewMode = ref(localStorage.getItem('admin_view_mode') || 'table')
watch(viewMode, (val) => localStorage.setItem('admin_view_mode', val))

/** Локальная копия списка баннеров (для оптимистичных обновлений) */
const localBanners = ref([])
watch(
    () => props.banners,
    (newVal) => {
        localBanners.value = JSON.parse(JSON.stringify(newVal || []))
    },
    { immediate: true, deep: true }
)

/** Пагинация */
const itemsPerPage = ref(props.adminCountBanners)
watch(itemsPerPage, (newVal) => {
    router.put(route('admin.settings.updateAdminCountBanners'), { value: newVal }, {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => toast.info(`Показ ${newVal} элементов на странице.`),
        onError: (errors) => toast.error(errors.value || 'Ошибка обновления кол-ва элементов.'),
    })
})

/** Параметры сортировки */
const sortParam = ref(props.adminSortBanners)
watch(sortParam, (newVal) => {
    router.put(route('admin.settings.updateAdminSortBanners'), { value: newVal }, {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => toast.info('Сортировка успешно изменена'),
        onError: (errors) => toast.error(errors.value || 'Ошибка обновления сортировки.'),
    })
})

/** Модалка удаления */
const showConfirmDeleteModal = ref(false)
const bannerToDeleteId = ref(null)
const bannerToDeleteTitle = ref('')

const confirmDelete = (id, title) => {
    bannerToDeleteId.value = id
    bannerToDeleteTitle.value = title
    showConfirmDeleteModal.value = true
}

const closeModal = () => {
    showConfirmDeleteModal.value = false
    bannerToDeleteId.value = null
    bannerToDeleteTitle.value = ''
}

const deleteBanner = () => {
    if (bannerToDeleteId.value === null) return

    const idToDelete = bannerToDeleteId.value
    const titleToDelete = bannerToDeleteTitle.value

    router.delete(route('admin.banners.destroy', { banner: idToDelete }), {
        preserveScroll: true,
        preserveState: false,
        onSuccess: () => {
            closeModal()
            toast.success(`Баннер "${titleToDelete || 'ID: ' + idToDelete}" удален.`)
        },
        onError: (errors) => {
            closeModal()
            const errorMsg = errors.general || errors[Object.keys(errors)[0]] || 'Произошла ошибка при удалении.'
            toast.error(`${errorMsg} (Баннер: ${titleToDelete || 'ID: ' + idToDelete})`)
        },
        onFinish: () => {
            bannerToDeleteId.value = null
            bannerToDeleteTitle.value = ''
        }
    })
}

/** Оптимистичный patch локального массива */
const patchLocal = (id, patch) => {
    const idx = localBanners.value.findIndex(b => b.id === id)
    if (idx !== -1) localBanners.value[idx] = { ...localBanners.value[idx], ...patch }
}

/** toggle left */
const toggleLeft = (banner) => {
    const newLeft = !banner.left
    const actionText = newLeft ? 'активирован в левой колонке' : 'деактивирован в левой колонке'

    router.put(route('admin.actions.banners.updateLeft', { banner: banner.id }), { left: newLeft }, {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => {
            patchLocal(banner.id, { left: newLeft })
            toast.success(`Баннер "${banner.title}" ${actionText}.`)
        },
        onError: (errors) => {
            toast.error(errors.left || errors.general || `Ошибка изменения статуса для "${banner.title}".`)
        },
    })
}

/** toggle main */
const toggleMain = (banner) => {
    const newMain = !banner.main
    const actionText = newMain ? 'активирован в главном' : 'деактивирован в главном'

    router.put(route('admin.actions.banners.updateMain', { banner: banner.id }), { main: newMain }, {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => {
            patchLocal(banner.id, { main: newMain })
            toast.success(`Баннер "${banner.title}" ${actionText}.`)
        },
        onError: (errors) => {
            toast.error(errors.main || errors.general || `Ошибка изменения статуса для "${banner.title}".`)
        },
    })
}

/** toggle right */
const toggleRight = (banner) => {
    const newRight = !banner.right
    const actionText = newRight ? 'активирован в правой колонке' : 'деактивирован в правой колонке'

    router.put(route('admin.actions.banners.updateRight', { banner: banner.id }), { right: newRight }, {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => {
            patchLocal(banner.id, { right: newRight })
            toast.success(`Баннер "${banner.title}" ${actionText}.`)
        },
        onError: (errors) => {
            toast.error(errors.right || errors.general || `Ошибка изменения статуса для "${banner.title}".`)
        },
    })
}

/** toggle activity */
const toggleActivity = (banner) => {
    const newActivity = !banner.activity
    const actionText = newActivity ? t('activated') : t('deactivated')

    router.put(route('admin.actions.banners.updateActivity', { banner: banner.id }), { activity: newActivity }, {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => {
            patchLocal(banner.id, { activity: newActivity })
            toast.success(`Баннер "${banner.title}" ${actionText}.`)
        },
        onError: (errors) => {
            toast.error(errors.activity || errors.general || `Ошибка изменения активности для "${banner.title}".`)
        },
    })
}

/** Модерация баннера (approve/reject) */
const approveBanner = (banner, status = 1, note = '') => {
    if (!banner?.id) return

    router.put(
        route('admin.actions.banners.approve', { banner: banner.id }),
        { moderation_status: status, moderation_note: note },
        {
            preserveScroll: true,
            preserveState: true,
            onSuccess: () => {
                patchLocal(banner.id, {
                    moderation_status: status,
                    is_approved: status === 1,
                    moderation_note: note,
                })
                toast.success(status === 1 ? 'Баннер одобрен' : 'Баннер отклонен')
            },
            onError: () => toast.error('Ошибка модерации баннера'),
        }
    )
}

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

/** Сортировка + фильтры (включая модерацию + owner + позиции) */
const sortBanners = (banners) => {
    const list = (banners || []).slice()

    // --- owner сортировки ---
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
    if (sortParam.value === 'activity') return list.filter(b => !!b.activity)
    if (sortParam.value === 'inactive') return list.filter(b => !b.activity)

    if (sortParam.value === 'left') return list.filter(b => !!b.left)
    if (sortParam.value === 'noLeft') return list.filter(b => !b.left)

    if (sortParam.value === 'main') return list.filter(b => !!b.main)
    if (sortParam.value === 'noMain') return list.filter(b => !b.main)

    if (sortParam.value === 'right') return list.filter(b => !!b.right)
    if (sortParam.value === 'noRight') return list.filter(b => !b.right)

    // locale
    if (sortParam.value === 'locale') {
        return list.sort((a, b) => {
            if ((a.locale || '') < (b.locale || '')) return 1
            if ((a.locale || '') > (b.locale || '')) return -1
            return 0
        })
    }

    // --- moderation filters ---
    if (sortParam.value === 'moderation_pending') return list.filter(b => moderationNum(b?.moderation_status) === 0)
    if (sortParam.value === 'moderation_approved') return list.filter(b => moderationNum(b?.moderation_status) === 1)
    if (sortParam.value === 'moderation_rejected') return list.filter(b => moderationNum(b?.moderation_status) === 2)

    // --- moderation sorting ---
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

/** Фильтр поиска: title + owner.name + owner.email + short + comment */
const filteredBanners = computed(() => {
    let filtered = localBanners.value || []
    const q = normalize(searchQuery.value)

    if (q) {
        filtered = filtered.filter(b => {
            const title = normalize(b?.title)
            const short = normalize(b?.short)
            const comment = normalize(b?.comment)
            const link = normalize(b?.link)

            const ownerName = normalize(b?.owner?.name)
            const ownerEmail = normalize(b?.owner?.email)

            return (
                title.includes(q) ||
                short.includes(q) ||
                comment.includes(q) ||
                link.includes(q) ||
                ownerName.includes(q) ||
                ownerEmail.includes(q)
            )
        })
    }

    return sortBanners(filtered)
})

/** Пагинация */
const paginatedBanners = computed(() => {
    const per = Number(itemsPerPage.value || 10)
    const start = (currentPage.value - 1) * per
    return filteredBanners.value.slice(start, start + per)
})

/** Drag & Drop sort (bulk) */
const handleSortOrderUpdate = (orderedIds) => {
    const startSort = (currentPage.value - 1) * Number(itemsPerPage.value || 10)

    const sortData = orderedIds.map((id, index) => ({
        id,
        sort: startSort + index + 1,
    }))

    router.put(
        route('admin.actions.banners.updateSortBulk'),
        { items: sortData, locale: props.currentLocale },
        {
            preserveScroll: true,
            preserveState: true,
            onSuccess: () => toast.success('Порядок баннеров успешно обновлен.'),
            onError: (errors) => {
                console.error('Ошибка обновления сортировки:', errors)
                toast.error(errors.message || errors.general || 'Не удалось обновить порядок баннеров.')
                router.reload({ only: ['banners'], preserveScroll: true })
            },
        }
    )
}

/** Массовые действия */
const selectedBanners = ref([])

/** Выбрать все */
const toggleAll = ({ ids, checked }) => {
    if (checked) {
        selectedBanners.value = [...new Set([...selectedBanners.value, ...ids])]
    } else {
        selectedBanners.value = selectedBanners.value.filter(id => !ids.includes(id))
    }
}

/** Выбор одного */
const toggleSelectBanner = (bannerId) => {
    const index = selectedBanners.value.indexOf(bannerId)
    if (index > -1) selectedBanners.value.splice(index, 1)
    else selectedBanners.value.push(bannerId)
}

/** Массовое переключение активности */
const bulkToggleActivity = (newActivity) => {
    if (!selectedBanners.value.length) {
        toast.warning('Выберите баннеры для активации/деактивации')
        return
    }

    const idsToUpdate = [...selectedBanners.value]

    router.put(
        route('admin.actions.banners.bulkUpdateActivity'),
        { ids: idsToUpdate, activity: newActivity },
        {
            preserveScroll: true,
            preserveState: true,
            onSuccess: () => {
                localBanners.value = localBanners.value.map(b => {
                    if (idsToUpdate.includes(b.id)) return { ...b, activity: newActivity }
                    return b
                })
                selectedBanners.value = []
                toast.success('Активность баннеров массово обновлена')
            },
            onError: (errors) => {
                const msg =
                    errors?.ids || errors?.activity || errors?.general ||
                    'Не удалось массово обновить активность баннеров'
                toast.error(msg)
            },
        }
    )
}

/** Массовое переключение в левую колонку */
const bulkToggleLeft = (newLeft) => {
    if (!selectedBanners.value.length) {
        toast.warning(`Выберите баннеры для ${newLeft ? 'активации в левой колонке' : 'деактивации в левой колонке'}.`)
        return
    }

    const idsToUpdate = [...selectedBanners.value]

    axios.put(route('admin.actions.banners.bulkUpdateLeft'), {
        ids: idsToUpdate,
        left: newLeft,
    }).then(() => {
        localBanners.value = localBanners.value.map(b => {
            if (idsToUpdate.includes(b.id)) return { ...b, left: newLeft }
            return b
        })
        selectedBanners.value = []
        toast.success('Статус в левой колонке массово обновлен')
    }).catch(() => {
        toast.error('Не удалось обновить статус в левой колонке')
    })
}

/** Массовое переключение по центру */
const bulkToggleMain = (newMain) => {
    if (!selectedBanners.value.length) {
        toast.warning(`Выберите баннеры для ${newMain ? 'активации в главном' : 'деактивации в главном'}.`)
        return
    }

    const idsToUpdate = [...selectedBanners.value]

    axios.put(route('admin.actions.banners.bulkUpdateMain'), {
        ids: idsToUpdate,
        main: newMain,
    }).then(() => {
        localBanners.value = localBanners.value.map(b => {
            if (idsToUpdate.includes(b.id)) return { ...b, main: newMain }
            return b
        })
        selectedBanners.value = []
        toast.success('Статус в главном массово обновлен')
    }).catch(() => {
        toast.error('Не удалось обновить статус в главном')
    })
}

/** Массовое переключение в правую колонку */
const bulkToggleRight = (newRight) => {
    if (!selectedBanners.value.length) {
        toast.warning(`Выберите баннеры для ${newRight ? 'активации в правой колонке' : 'деактивации в правой колонке'}.`)
        return
    }

    const idsToUpdate = [...selectedBanners.value]

    axios.put(route('admin.actions.banners.bulkUpdateRight'), {
        ids: idsToUpdate,
        right: newRight,
    }).then(() => {
        localBanners.value = localBanners.value.map(b => {
            if (idsToUpdate.includes(b.id)) return { ...b, right: newRight }
            return b
        })
        selectedBanners.value = []
        toast.success('Статус в правой колонке массово обновлен')
    }).catch(() => {
        toast.error('Не удалось обновить статус в правой колонке')
    })
}

/** Массовое удаление */
const bulkDelete = () => {
    if (!selectedBanners.value.length) {
        toast.warning('Выберите хотя бы один баннер для удаления.')
        return
    }
    if (!confirm('Вы уверены, что хотите удалить выбранные баннеры?')) return

    router.delete(route('admin.actions.banners.bulkDestroy'), {
        data: { ids: selectedBanners.value },
        preserveScroll: true,
        preserveState: false,
        onSuccess: () => {
            selectedBanners.value = []
            toast.success('Массовое удаление баннеров успешно завершено.')
        },
        onError: (errors) => {
            console.error('Ошибка массового удаления:', errors)
            const errorKey = Object.keys(errors)[0]
            toast.error(errors[errorKey] || 'Произошла ошибка при удалении баннеров.')
        },
    })
}

/** Массовая модерация (для admin) */
const bulkApprove = (status = 1) => {
    if (!isAdmin.value) return
    if (!selectedBanners.value.length) {
        toast.warning('Выберите баннеры для модерации')
        return
    }

    // Без отдельного endpoint — не делаем массовую модерацию запросом.
    // Здесь оставлено место: если добавишь bulkApprove на бэке — подключим.
    toast.info('Массовая модерация: добавь endpoint bulkApprove на бэкенде (если нужно).')
}

/** обработчики массовых действий */
const handleBulkAction = (event) => {
    const action = event.target.value

    if (action === 'selectAll') {
        selectedBanners.value = paginatedBanners.value.map(b => b.id)
    } else if (action === 'deselectAll') {
        selectedBanners.value = []
    } else if (action === 'activate') {
        bulkToggleActivity(true)
    } else if (action === 'deactivate') {
        bulkToggleActivity(false)
    } else if (action === 'left') {
        bulkToggleLeft(true)
    } else if (action === 'noLeft') {
        bulkToggleLeft(false)
    } else if (action === 'main') {
        bulkToggleMain(true)
    } else if (action === 'noMain') {
        bulkToggleMain(false)
    } else if (action === 'right') {
        bulkToggleRight(true)
    } else if (action === 'noRight') {
        bulkToggleRight(false)
    } else if (action === 'delete') {
        bulkDelete()
    } else if (action === 'moderation_approve') {
        bulkApprove(1)
    } else if (action === 'moderation_reject') {
        bulkApprove(2)
    }

    event.target.value = ''
}

/** Таб локалей */
const localeLink = (loc) => route('admin.banners.index', { locale: loc })
</script>

<template>
    <AdminLayout :title="t('banners')">
        <template #header>
            <TitlePage>
                {{ t('banners') }}
            </TitlePage>
        </template>

        <div class="px-2 py-2 w-full max-w-12xl mx-auto">
            <div class="p-4 bg-slate-50 dark:bg-slate-700
                        border border-blue-400 dark:border-blue-200
                        overflow-hidden shadow-md shadow-gray-500 dark:shadow-slate-400
                        bg-opacity-95 dark:bg-opacity-95">

                <div class="sm:flex sm:justify-between sm:items-center mb-2">
                    <DefaultButton :href="route('admin.banners.create', { locale: currentLocale })">
                        <template #icon>
                            <svg class="w-4 h-4 fill-current opacity-50 shrink-0"
                                 viewBox="0 0 16 16">
                                <path
                                    d="M15 7H9V1c0-.6-.4-1-1-1S7 .4 7 1v6H1c-.6 0-1 .4-1 1s.4 1 1 1h6v6c0 .6.4 1 1 1s1-.4 1-1V9h6c.6 0 1-.4 1-1s-.4-1-1-1z"></path>
                            </svg>
                        </template>
                        {{ t('addBanner') }}
                    </DefaultButton>

                    <BulkActionSelect v-if="bannersCount" @change="handleBulkAction" />
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
                                        : 'bg-slate-100 dark:bg-slate-900 ' +
                                         'text-slate-700 dark:text-slate-200 ' +
                                          'hover:bg-slate-300 dark:hover:bg-slate-600'
                                ]"
                                preserve-scroll
                                preserve-state
                            >
                                {{ loc.toUpperCase() }}
                            </Link>
                        </template>
                    </div>

                    <div class="flex items-center space-x-3" v-if="bannersCount">
                        <CountTable>{{ bannersCount }}</CountTable>
                        <ToggleViewButton v-model:viewMode="viewMode" />
                    </div>
                </div>

                <SearchInput v-if="bannersCount" v-model="searchQuery"
                             :placeholder="t('searchByName')" />

                <!-- Таблица / Карточки -->
                <BannerTable
                    v-if="viewMode === 'table'"
                    :banners="paginatedBanners"
                    :selected-banners="selectedBanners"
                    :isAdmin="isAdmin"
                    @toggle-left="toggleLeft"
                    @toggle-main="toggleMain"
                    @toggle-right="toggleRight"
                    @toggle-activity="toggleActivity"
                    @delete="confirmDelete"
                    @update-sort-order="handleSortOrderUpdate"
                    @toggle-select="toggleSelectBanner"
                    @toggle-all="toggleAll"
                    @approve="approveBanner"
                />

                <BannerCardGrid
                    v-else
                    :banners="paginatedBanners"
                    :selected-banners="selectedBanners"
                    :isAdmin="isAdmin"
                    @toggle-left="toggleLeft"
                    @toggle-main="toggleMain"
                    @toggle-right="toggleRight"
                    @toggle-activity="toggleActivity"
                    @delete="confirmDelete"
                    @toggle-select="toggleSelectBanner"
                    @toggle-all="toggleAll"
                    @update-sort-order="handleSortOrderUpdate"
                    @approve="approveBanner"
                />

                <div class="flex justify-between items-center flex-col md:flex-row my-1"
                     v-if="bannersCount">
                    <ItemsPerPageSelect
                        :items-per-page="itemsPerPage"
                        @update:itemsPerPage="itemsPerPage = $event" />
                    <Pagination
                        :current-page="currentPage"
                        :items-per-page="itemsPerPage"
                        :total-items="filteredBanners.length"
                        @update:currentPage="currentPage = $event"
                        @update:itemsPerPage="itemsPerPage = $event"
                    />
                    <SortSelect
                        :sortParam="sortParam"
                        @update:sortParam="val => sortParam = val" />
                </div>
            </div>
        </div>

        <DangerModal
            :show="showConfirmDeleteModal"
            @close="closeModal"
            :onCancel="closeModal"
            :onConfirm="deleteBanner"
            :cancelText="t('cancel')"
            :confirmText="t('yesDelete')"
        />
    </AdminLayout>
</template>
