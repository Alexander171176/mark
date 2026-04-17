<script setup>
/**
 * @version PulsarCMS 1.0
 * @author Александр
 * Статьи — список + локали + модерация
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
import SortSelect from '@/Components/Admin/Article/Sort/SortSelect.vue'
import ArticleTable from '@/Components/Admin/Article/Table/ArticleTable.vue'
import ArticleCardGrid from '@/Components/Admin/Article/View/ArticleCardGrid.vue'
import CountTable from '@/Components/Admin/Count/CountTable.vue'
import BulkActionSelect from '@/Components/Admin/Article/Select/BulkActionSelect.vue'
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
    articles: Array,
    articlesCount: Number,

    adminCountArticles: Number,
    adminSortArticles: String,

    currentLocale: String,
    availableLocales: Array,

    errors: Object,
})

/** Вид: table/cards */
const viewMode = ref(localStorage.getItem('admin_view_mode') || 'table')
watch(viewMode, (val) => localStorage.setItem('admin_view_mode', val))

/** Локальная копия списка статей (для оптимистичных обновлений) */
const localArticles = ref([])
watch(
    () => props.articles,
    (newVal) => {
        localArticles.value = JSON.parse(JSON.stringify(newVal || []))
    },
    { immediate: true, deep: true }
)

/** Пагинация */
const itemsPerPage = ref(props.adminCountArticles)
watch(itemsPerPage, (newVal) => {
    router.put(route('admin.settings.updateAdminCountArticles'), { value: newVal }, {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => toast.info(`Показ ${newVal} элементов на странице.`),
        onError: (errors) => toast.error(errors.value || 'Ошибка обновления кол-ва элементов.'),
    })
})

/** Параметры сортировки */
const sortParam = ref(props.adminSortArticles)
watch(sortParam, (newVal) => {
    router.put(route('admin.settings.updateAdminSortArticles'), { value: newVal }, {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => toast.info('Сортировка успешно изменена'),
        onError: (errors) => toast.error(errors.value || 'Ошибка обновления сортировки.'),
    })
})

/** Модалка удаления */
const showConfirmDeleteModal = ref(false)
const articleToDeleteId = ref(null)
const articleToDeleteTitle = ref('')

const confirmDelete = (id, title) => {
    articleToDeleteId.value = id
    articleToDeleteTitle.value = title
    showConfirmDeleteModal.value = true
}

const closeModal = () => {
    showConfirmDeleteModal.value = false
    articleToDeleteId.value = null
    articleToDeleteTitle.value = ''
}

const deleteArticle = () => {
    if (articleToDeleteId.value === null) return

    const idToDelete = articleToDeleteId.value
    const titleToDelete = articleToDeleteTitle.value

    router.delete(route('admin.articles.destroy', { article: idToDelete }), {
        preserveScroll: true,
        preserveState: false,
        onSuccess: () => {
            closeModal()
            toast.success(`Статья "${titleToDelete || 'ID: ' + idToDelete}" удалена.`)
        },
        onError: (errors) => {
            closeModal()
            const errorMsg = errors.general || errors[Object.keys(errors)[0]] || 'Произошла ошибка при удалении.'
            toast.error(`${errorMsg} (Статья: ${titleToDelete || 'ID: ' + idToDelete})`)
        },
        onFinish: () => {
            articleToDeleteId.value = null
            articleToDeleteTitle.value = ''
        }
    })
}

/** toggle left/main/right/activity (оптимистично патчим localArticles) */
const patchLocal = (id, patch) => {
    const idx = localArticles.value.findIndex(a => a.id === id)
    if (idx !== -1) localArticles.value[idx] = { ...localArticles.value[idx], ...patch }
}

const toggleLeft = (article) => {
    const newLeft = !article.left
    const actionText = newLeft ? 'активирована в левой колонке' : 'деактивирована в левой колонке'

    router.put(route('admin.actions.articles.updateLeft', { article: article.id }), { left: newLeft }, {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => {
            patchLocal(article.id, { left: newLeft })
            toast.success(`Статья "${article.title}" ${actionText}.`)
        },
        onError: (errors) => {
            toast.error(errors.left || errors.general || `Ошибка изменения статуса для "${article.title}".`)
        },
    })
}

const toggleMain = (article) => {
    const newMain = !article.main
    const actionText = newMain ? 'активирована в главном' : 'деактивирована в главном'

    router.put(route('admin.actions.articles.updateMain', { article: article.id }), { main: newMain }, {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => {
            patchLocal(article.id, { main: newMain })
            toast.success(`Статья "${article.title}" ${actionText}.`)
        },
        onError: (errors) => {
            toast.error(errors.main || errors.general || `Ошибка изменения статуса для "${article.title}".`)
        },
    })
}

const toggleRight = (article) => {
    const newRight = !article.right
    const actionText = newRight ? 'активирована в правой колонке' : 'деактивирована в правой колонке'

    router.put(route('admin.actions.articles.updateRight', { article: article.id }), { right: newRight }, {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => {
            patchLocal(article.id, { right: newRight })
            toast.success(`Статья "${article.title}" ${actionText}.`)
        },
        onError: (errors) => {
            toast.error(errors.right || errors.general || `Ошибка изменения статуса для "${article.title}".`)
        },
    })
}

const toggleActivity = (article) => {
    const newActivity = !article.activity
    const actionText = newActivity ? t('activated') : t('deactivated')

    router.put(route('admin.actions.articles.updateActivity', { article: article.id }), { activity: newActivity }, {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => {
            patchLocal(article.id, { activity: newActivity })
            toast.success(`Статья "${article.title}" ${actionText}.`)
        },
        onError: (errors) => {
            toast.error(errors.activity || errors.general || `Ошибка изменения активности для "${article.title}".`)
        },
    })
}

/** Клон */
const cloneArticle = (articleObject) => {
    const articleId = articleObject?.id
    const articleTitle = articleObject?.title || `ID: ${articleId}`

    if (!articleId) {
        toast.error('Не удалось определить статью для клонирования.')
        return
    }

    if (!confirm(`Вы уверены, что хотите клонировать статью "${articleTitle}"?`)) return

    router.post(route('admin.actions.articles.clone', { article: articleId }), {}, {
        preserveScroll: true,
        preserveState: false,
        onSuccess: () => toast.success(`Статья "${articleTitle}" успешно клонирована.`),
        onError: (errors) => {
            const errorKey = Object.keys(errors)[0]
            toast.error(errors[errorKey] || `Ошибка клонирования статьи "${articleTitle}".`)
        }
    })
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

/** Сортировка + фильтры (включая модерацию + owner) */
const sortArticles = (articles) => {
    const list = (articles || []).slice()

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
    if (sortParam.value === 'activity') return list.filter(a => !!a.activity)
    if (sortParam.value === 'inactive') return list.filter(a => !a.activity)

    if (sortParam.value === 'left') return list.filter(a => !!a.left)
    if (sortParam.value === 'noLeft') return list.filter(a => !a.left)

    if (sortParam.value === 'main') return list.filter(a => !!a.main)
    if (sortParam.value === 'noMain') return list.filter(a => !a.main)

    if (sortParam.value === 'right') return list.filter(a => !!a.right)
    if (sortParam.value === 'noRight') return list.filter(a => !a.right)

    // locale
    if (sortParam.value === 'locale') {
        return list.sort((a, b) => {
            if ((a.locale || '') < (b.locale || '')) return 1
            if ((a.locale || '') > (b.locale || '')) return -1
            return 0
        })
    }

    // views/likes
    if (sortParam.value === 'views') return list.sort((a, b) => (b.views ?? 0) - (a.views ?? 0))
    if (sortParam.value === 'likes') return list.sort((a, b) => (b.likes ?? 0) - (a.likes ?? 0))

    // --- moderation filters ---
    if (sortParam.value === 'moderation_pending') return list.filter(a => moderationNum(a?.moderation_status) === 0)
    if (sortParam.value === 'moderation_approved') return list.filter(a => moderationNum(a?.moderation_status) === 1)
    if (sortParam.value === 'moderation_rejected') return list.filter(a => moderationNum(a?.moderation_status) === 2)

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

/** Фильтр поиска: title + owner.name + owner.email */
const filteredArticles = computed(() => {
    let filtered = localArticles.value || []
    const q = normalize(searchQuery.value)

    if (q) {
        filtered = filtered.filter(a => {
            const title = normalize(a?.title)
            const ownerName = normalize(a?.owner?.name)
            const ownerEmail = normalize(a?.owner?.email)
            return title.includes(q) || ownerName.includes(q) || ownerEmail.includes(q)
        })
    }

    return sortArticles(filtered)
})

/** Пагинация */
const paginatedArticles = computed(() => {
    const per = Number(itemsPerPage.value || 10)
    const start = (currentPage.value - 1) * per
    return filteredArticles.value.slice(start, start + per)
})

/** Drag & Drop sort (bulk) */
const handleSortOrderUpdate = (orderedIds) => {
    const startSort = (currentPage.value - 1) * Number(itemsPerPage.value || 10)

    const sortData = orderedIds.map((id, index) => ({
        id,
        sort: startSort + index + 1,
    }))

    router.put(
        route('admin.actions.articles.updateSortBulk'),
        { items: sortData, locale: props.currentLocale },
        {
            preserveScroll: true,
            preserveState: true,
            onSuccess: () => toast.success('Порядок статей успешно обновлен.'),
            onError: (errors) => {
                console.error('Ошибка обновления сортировки:', errors)
                toast.error(errors.message || errors.general || 'Не удалось обновить порядок статей.')
                router.reload({ only: ['articles'], preserveScroll: true })
            },
        }
    )
}

/** Массовые действия */
const selectedArticles = ref([])

const toggleAll = ({ ids, checked }) => {
    if (checked) {
        selectedArticles.value = [...new Set([...selectedArticles.value, ...ids])]
    } else {
        selectedArticles.value = selectedArticles.value.filter(id => !ids.includes(id))
    }
}

const toggleSelectArticle = (articleId) => {
    const index = selectedArticles.value.indexOf(articleId)
    if (index > -1) selectedArticles.value.splice(index, 1)
    else selectedArticles.value.push(articleId)
}

const bulkToggleActivity = (newActivity) => {
    if (!selectedArticles.value.length) {
        toast.warning('Выберите статьи для активации/деактивации')
        return
    }

    const idsToUpdate = [...selectedArticles.value]

    router.put(
        route('admin.actions.articles.bulkUpdateActivity'),
        { ids: idsToUpdate, activity: newActivity },
        {
            preserveScroll: true,
            preserveState: true,
            onSuccess: () => {
                // оптимистично обновим локальный массив
                localArticles.value = localArticles.value.map(a => {
                    if (idsToUpdate.includes(a.id)) return { ...a, activity: newActivity }
                    return a
                })
                selectedArticles.value = []
                toast.success('Активность статей массово обновлена')
            },
            onError: (errors) => {
                const msg =
                    errors?.ids || errors?.activity || errors?.general ||
                    'Не удалось массово обновить активность статей'
                toast.error(msg)
            },
        }
    )
}

const bulkToggleLeft = (newLeft) => {
    if (!selectedArticles.value.length) {
        toast.warning(`Выберите статьи для ${newLeft ? 'активации в левой колонке' : 'деактивации в левой колонке'}.`)
        return
    }

    const idsToUpdate = [...selectedArticles.value]

    axios.put(route('admin.actions.articles.bulkUpdateLeft'), {
        ids: idsToUpdate,
        left: newLeft,
    }).then(() => {
        localArticles.value = localArticles.value.map(a => {
            if (idsToUpdate.includes(a.id)) return { ...a, left: newLeft }
            return a
        })
        selectedArticles.value = []
        toast.success('Статус в левой колонке массово обновлен')
    }).catch(() => {
        toast.error('Не удалось обновить статус в левой колонке')
    })
}

const bulkToggleMain = (newMain) => {
    if (!selectedArticles.value.length) {
        toast.warning(`Выберите статьи для ${newMain ? 'активации в главном' : 'деактивации в главном'}.`)
        return
    }

    const idsToUpdate = [...selectedArticles.value]

    axios.put(route('admin.actions.articles.bulkUpdateMain'), {
        ids: idsToUpdate,
        main: newMain,
    }).then(() => {
        localArticles.value = localArticles.value.map(a => {
            if (idsToUpdate.includes(a.id)) return { ...a, main: newMain }
            return a
        })
        selectedArticles.value = []
        toast.success('Статус в главном массово обновлен')
    }).catch(() => {
        toast.error('Не удалось обновить статус в главном')
    })
}

const bulkToggleRight = (newRight) => {
    if (!selectedArticles.value.length) {
        toast.warning(`Выберите статьи для ${newRight ? 'активации в правой колонке' : 'деактивации в правой колонке'}.`)
        return
    }

    const idsToUpdate = [...selectedArticles.value]

    axios.put(route('admin.actions.articles.bulkUpdateRight'), {
        ids: idsToUpdate,
        right: newRight,
    }).then(() => {
        localArticles.value = localArticles.value.map(a => {
            if (idsToUpdate.includes(a.id)) return { ...a, right: newRight }
            return a
        })
        selectedArticles.value = []
        toast.success('Статус в правой колонке массово обновлен')
    }).catch(() => {
        toast.error('Не удалось обновить статус в правой колонке')
    })
}

const bulkDelete = () => {
    if (!selectedArticles.value.length) {
        toast.warning('Выберите хотя бы одну статью для удаления.')
        return
    }
    if (!confirm('Вы уверены, что хотите удалить выбранные статьи?')) return

    router.delete(route('admin.actions.articles.bulkDestroy'), {
        data: { ids: selectedArticles.value },
        preserveScroll: true,
        preserveState: false,
        onSuccess: () => {
            selectedArticles.value = []
            toast.success('Массовое удаление статей успешно завершено.')
        },
        onError: (errors) => {
            console.error('Ошибка массового удаления:', errors)
            const errorKey = Object.keys(errors)[0]
            toast.error(errors[errorKey] || 'Произошла ошибка при удалении статей.')
        },
    })
}

/** Модерация статьи (approve/reject) */
const approveArticle = (article, status = 1, note = '') => {
    if (!article?.id) return

    router.put(
        route('admin.actions.articles.approve', { article: article.id }),
        { moderation_status: status, moderation_note: note },
        {
            preserveScroll: true,
            preserveState: true,
            onSuccess: () => {
                patchLocal(article.id, {
                    moderation_status: status,
                    is_approved: status === 1,
                    moderation_note: note,
                })
                toast.success(status === 1 ? 'Статья одобрена' : 'Статья отклонена')
            },
            onError: () => toast.error('Ошибка модерации статьи'),
        }
    )
}

/** Обработчик массовых действий (селект) */
const handleBulkAction = (event) => {
    const action = event.target.value

    if (action === 'selectAll') {
        selectedArticles.value = paginatedArticles.value.map(a => a.id)
    } else if (action === 'deselectAll') {
        selectedArticles.value = []
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
    }

    event.target.value = ''
}

/** Таб локалей */
const localeLink = (loc) => route('admin.articles.index', { locale: loc })
</script>

<template>
    <AdminLayout :title="t('posts')">
        <template #header>
            <TitlePage>
                {{ t('posts') }}
            </TitlePage>
        </template>

        <div class="px-2 py-2 w-full max-w-12xl mx-auto">
            <div class="p-4 bg-slate-50 dark:bg-slate-700 border border-blue-400 dark:border-blue-200
                        overflow-hidden shadow-md shadow-gray-500 dark:shadow-slate-400
                        bg-opacity-95 dark:bg-opacity-95">

                <div class="sm:flex sm:justify-between sm:items-center mb-2">
                    <DefaultButton :href="route('admin.articles.create', { locale: currentLocale })">
                        <template #icon>
                            <svg class="w-4 h-4 fill-current opacity-50 shrink-0" viewBox="0 0 16 16">
                                <path
                                    d="M15 7H9V1c0-.6-.4-1-1-1S7 .4 7 1v6H1c-.6 0-1 .4-1 1s.4 1 1 1h6v6c0 .6.4 1 1 1s1-.4 1-1V9h6c.6 0 1-.4 1-1s-.4-1-1-1z"></path>
                            </svg>
                        </template>
                        {{ t('addPost') }}
                    </DefaultButton>

                    <BulkActionSelect v-if="articlesCount" @change="handleBulkAction" />
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

                    <div class="flex items-center space-x-3" v-if="articlesCount">
                        <CountTable>{{ articlesCount }}</CountTable>
                        <ToggleViewButton v-model:viewMode="viewMode" />
                    </div>
                </div>

                <SearchInput v-if="articlesCount" v-model="searchQuery" :placeholder="t('searchByName')" />

                <!-- Таблица / Карточки -->
                <ArticleTable
                    v-if="viewMode === 'table'"
                    :articles="paginatedArticles"
                    :selected-articles="selectedArticles"
                    :isAdmin="isAdmin"
                    @toggle-left="toggleLeft"
                    @toggle-main="toggleMain"
                    @toggle-right="toggleRight"
                    @toggle-activity="toggleActivity"
                    @delete="confirmDelete"
                    @clone="cloneArticle"
                    @update-sort-order="handleSortOrderUpdate"
                    @toggle-select="toggleSelectArticle"
                    @toggle-all="toggleAll"
                    @approve="approveArticle"
                />

                <ArticleCardGrid
                    v-else
                    :articles="paginatedArticles"
                    :selected-articles="selectedArticles"
                    :isAdmin="isAdmin"
                    @toggle-left="toggleLeft"
                    @toggle-main="toggleMain"
                    @toggle-right="toggleRight"
                    @toggle-activity="toggleActivity"
                    @delete="confirmDelete"
                    @clone="cloneArticle"
                    @update-sort-order="handleSortOrderUpdate"
                    @toggle-select="toggleSelectArticle"
                    @toggle-all="toggleAll"
                    @approve="approveArticle"
                />

                <div class="flex justify-between items-center flex-col md:flex-row my-1" v-if="articlesCount">
                    <ItemsPerPageSelect :items-per-page="itemsPerPage" @update:itemsPerPage="itemsPerPage = $event" />
                    <Pagination
                        :current-page="currentPage"
                        :items-per-page="itemsPerPage"
                        :total-items="filteredArticles.length"
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
            :onConfirm="deleteArticle"
            :cancelText="t('cancel')"
            :confirmText="t('yesDelete')"
        />
    </AdminLayout>
</template>
