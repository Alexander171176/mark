<script setup>
/**
 * Статьи блога — Index
 * Новая мультиязычная архитектура:
 * - blog_articles
 * - blog_article_translations
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
import ToggleViewButton from '@/Components/Admin/Buttons/ToggleViewButton.vue'
import SearchInput from '@/Components/Admin/Search/SearchInput.vue'
import Pagination from '@/Components/Admin/Pagination/Pagination.vue'
import ItemsPerPageSelect from '@/Components/Admin/Select/ItemsPerPageSelect.vue'

import BulkActionSelect from '@/Components/Admin/Blog/Article/Select/BulkActionSelect.vue'
import SortSelect from '@/Components/Admin/Blog/Article/Sort/SortSelect.vue'
import ArticleTable from '@/Components/Admin/Blog/Article/Table/ArticleTable.vue'
import ArticleCardGrid from '@/Components/Admin/Blog/Article/View/ArticleCardGrid.vue'

const { t, locale } = useI18n()
const toast = useToast()
const page = usePage()

/** Props приходят из BlogArticleController@index */
const props = defineProps({
    articles: { type: Array, default: () => [] },
    articlesCount: { type: Number, default: 0 },

    adminCountArticles: { type: Number, default: 15 },
    adminSortArticles: { type: String, default: 'idDesc' },

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

/** Текущий перевод статьи */
const getArticleTranslation = (article) => article?.translation || {}

/** Название статьи */
const getArticleTitle = (article) => {
    return getArticleTranslation(article)?.title || `ID: ${article?.id}`
}

/** Краткое описание статьи */
const getArticleShort = (article) => {
    return getArticleTranslation(article)?.short || ''
}

/** Описание статьи */
const getArticleDescription = (article) => {
    return getArticleTranslation(article)?.description || ''
}

/** Локаль текущего перевода статьи */
const getArticleLocale = (article) => {
    return getArticleTranslation(article)?.locale || props.currentLocale || ''
}

/** Нормализация строки */
const normalize = (value) => (value ?? '').toString().trim().toLowerCase()

/** Безопасное число статуса модерации */
const moderationNum = (value) => {
    const number = Number(value)
    return Number.isFinite(number) ? number : 0
}

/** Режим отображения */
const viewMode = ref(localStorage.getItem('admin_view_mode_articles') || 'cards')

watch(viewMode, (value) => {
    localStorage.setItem('admin_view_mode_articles', value)
})

/** Количество элементов на странице */
const itemsPerPage = ref(props.adminCountArticles || 15)

watch(itemsPerPage, (newVal) => {
    router.put(
        route('admin.settings.updateAdminCountArticles'),
        { value: newVal },
        {
            preserveScroll: true,
            preserveState: true,
            onSuccess: () => toast.info(`Показ ${newVal} элементов на странице.`),
            onError: (errors) => toast.error(errors.value || 'Ошибка обновления кол-ва элементов.'),
        }
    )
})

/** Параметр сортировки */
const sortParam = ref(props.sortParam || props.adminSortArticles || 'idDesc')

watch(sortParam, (newVal) => {
    router.put(
        route('admin.settings.updateAdminSortArticles'),
        { value: newVal },
        {
            preserveScroll: true,
            preserveState: true,
            onSuccess: () => toast.info('Сортировка успешно изменена'),
            onError: (errors) => toast.error(errors.value || 'Ошибка обновления сортировки.'),
        }
    )
})

/** Локальная копия статей */
const localArticles = ref([])

watch(
    () => props.articles,
    (newVal) => {
        localArticles.value = JSON.parse(JSON.stringify(newVal || []))
    },
    { immediate: true, deep: true }
)

/** Модалка удаления */
const showConfirmDeleteModal = ref(false)
const articleToDeleteId = ref(null)
const articleToDeleteTitle = ref('')

/** Открыть модалку удаления */
const confirmDelete = (articleOrId, title = null) => {
    if (typeof articleOrId === 'object') {
        articleToDeleteId.value = articleOrId.id
        articleToDeleteTitle.value = title || getArticleTitle(articleOrId)
    } else {
        articleToDeleteId.value = articleOrId
        articleToDeleteTitle.value = title || `ID: ${articleOrId}`
    }

    showConfirmDeleteModal.value = true
}

/** Закрыть модалку */
const closeModal = () => {
    showConfirmDeleteModal.value = false
    articleToDeleteId.value = null
    articleToDeleteTitle.value = ''
}

/** Удалить статью */
const deleteArticle = () => {
    if (articleToDeleteId.value === null) return

    const idToDelete = articleToDeleteId.value
    const titleToDelete = articleToDeleteTitle.value

    router.delete(route('admin.blogArticles.destroy', { blogArticle: idToDelete }), {
        preserveScroll: true,
        preserveState: false,
        onSuccess: () => {
            toast.success(`Статья "${titleToDelete || 'ID: ' + idToDelete}" удалена.`)
        },
        onError: (errors) => {
            const errorKey = Object.keys(errors)[0]
            const errorMsg = errors.general || errors[errorKey] || 'Произошла ошибка при удалении.'
            toast.error(`${errorMsg} (Статья: ${titleToDelete || 'ID: ' + idToDelete})`)
        },
        onFinish: () => closeModal(),
    })
}

/** Локальное обновление статьи */
const patchLocalArticle = (articleId, callback) => {
    const index = localArticles.value.findIndex((article) => article.id === articleId)

    if (index !== -1) {
        callback(localArticles.value[index])
    }
}

/** Переключение активности */
const toggleActivity = (article) => {
    const newActivity = !article.activity
    const title = getArticleTitle(article)
    const actionText = newActivity ? t('activated') : t('deactivated')

    router.put(
        route('admin.actions.blogArticles.updateActivity', { blogArticle: article.id }),
        { activity: newActivity },
        {
            preserveScroll: true,
            preserveState: true,
            onSuccess: () => {
                patchLocalArticle(article.id, (node) => {
                    node.activity = newActivity
                })

                toast.success(`Статья "${title}" ${actionText}.`)
            },
            onError: (errors) => {
                toast.error(errors.activity || errors.general || `Ошибка изменения активности для "${title}".`)
            },
        }
    )
}

/** Переключение left */
const toggleLeft = (article) => {
    const newLeft = !article.left
    const title = getArticleTitle(article)

    router.put(
        route('admin.actions.blogArticles.updateLeft', { blogArticle: article.id }),
        { left: newLeft },
        {
            preserveScroll: true,
            preserveState: true,
            onSuccess: () => {
                patchLocalArticle(article.id, (node) => {
                    node.left = newLeft
                })

                toast.success(`Позиция left для статьи "${title}" обновлена.`)
            },
            onError: (errors) => {
                toast.error(errors.left || errors.general || `Ошибка изменения left для "${title}".`)
            },
        }
    )
}

/** Переключение main */
const toggleMain = (article) => {
    const newMain = !article.main
    const title = getArticleTitle(article)

    router.put(
        route('admin.actions.blogArticles.updateMain', { blogArticle: article.id }),
        { main: newMain },
        {
            preserveScroll: true,
            preserveState: true,
            onSuccess: () => {
                patchLocalArticle(article.id, (node) => {
                    node.main = newMain
                })

                toast.success(`Позиция main для статьи "${title}" обновлена.`)
            },
            onError: (errors) => {
                toast.error(errors.main || errors.general || `Ошибка изменения main для "${title}".`)
            },
        }
    )
}

/** Переключение right */
const toggleRight = (article) => {
    const newRight = !article.right
    const title = getArticleTitle(article)

    router.put(
        route('admin.actions.blogArticles.updateRight', { blogArticle: article.id }),
        { right: newRight },
        {
            preserveScroll: true,
            preserveState: true,
            onSuccess: () => {
                patchLocalArticle(article.id, (node) => {
                    node.right = newRight
                })

                toast.success(`Позиция right для статьи "${title}" обновлена.`)
            },
            onError: (errors) => {
                toast.error(errors.right || errors.general || `Ошибка изменения right для "${title}".`)
            },
        }
    )
}

/** Поиск */
const searchQuery = ref(props.search || '')
const currentPage = ref(1)

/** Локальная сортировка */
const sortArticles = (articles) => {
    const list = (articles || []).slice()

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

    if (sortParam.value === 'titleAsc') {
        return list.sort((a, b) => normalize(getArticleTitle(a)).localeCompare(normalize(getArticleTitle(b)), locale.value))
    }

    if (sortParam.value === 'titleDesc') {
        return list.sort((a, b) => normalize(getArticleTitle(b)).localeCompare(normalize(getArticleTitle(a)), locale.value))
    }

    if (sortParam.value === 'activity') {
        return list.filter((article) => !!article.activity)
    }

    if (sortParam.value === 'inactive') {
        return list.filter((article) => !article.activity)
    }

    if (sortParam.value === 'left') {
        return list.filter((article) => !!article.left)
    }

    if (sortParam.value === 'noLeft') {
        return list.filter((article) => !article.left)
    }

    if (sortParam.value === 'main') {
        return list.filter((article) => !!article.main)
    }

    if (sortParam.value === 'noMain') {
        return list.filter((article) => !article.main)
    }

    if (sortParam.value === 'right') {
        return list.filter((article) => !!article.right)
    }

    if (sortParam.value === 'noRight') {
        return list.filter((article) => !article.right)
    }

    if (sortParam.value === 'publishedAtDesc') {
        return list.sort((a, b) => new Date(b.published_at || 0) - new Date(a.published_at || 0))
    }

    if (sortParam.value === 'publishedAtAsc') {
        return list.sort((a, b) => new Date(a.published_at || 0) - new Date(b.published_at || 0))
    }

    if (sortParam.value === 'locale') {
        return list.sort((a, b) => getArticleLocale(a).localeCompare(getArticleLocale(b), locale.value))
    }

    if (sortParam.value === 'views' || sortParam.value === 'viewsDesc') {
        return list.sort((a, b) => (b.views ?? 0) - (a.views ?? 0))
    }

    if (sortParam.value === 'viewsAsc') {
        return list.sort((a, b) => (a.views ?? 0) - (b.views ?? 0))
    }

    if (sortParam.value === 'likes' || sortParam.value === 'likesDesc') {
        return list.sort((a, b) => (b.likes_count ?? 0) - (a.likes_count ?? 0))
    }

    if (sortParam.value === 'likesAsc') {
        return list.sort((a, b) => (a.likes_count ?? 0) - (b.likes_count ?? 0))
    }

    if (sortParam.value === 'commentsDesc') {
        return list.sort((a, b) => (b.comments_count ?? 0) - (a.comments_count ?? 0))
    }

    if (sortParam.value === 'commentsAsc') {
        return list.sort((a, b) => (a.comments_count ?? 0) - (b.comments_count ?? 0))
    }

    if (sortParam.value === 'moderation_pending') {
        return list.filter((article) => moderationNum(article?.moderation_status) === 0)
    }

    if (sortParam.value === 'moderation_approved') {
        return list.filter((article) => moderationNum(article?.moderation_status) === 1)
    }

    if (sortParam.value === 'moderation_rejected') {
        return list.filter((article) => moderationNum(article?.moderation_status) === 2)
    }

    if (sortParam.value === 'moderation_statusAsc') {
        return list.sort((a, b) => moderationNum(a?.moderation_status) - moderationNum(b?.moderation_status))
    }

    if (sortParam.value === 'moderation_statusDesc') {
        return list.sort((a, b) => moderationNum(b?.moderation_status) - moderationNum(a?.moderation_status))
    }

    return list
}

/** Фильтрация статей */
const filteredArticles = computed(() => {
    let filtered = localArticles.value || []
    const query = normalize(searchQuery.value)

    if (!query) {
        return sortArticles(filtered)
    }

    filtered = filtered.filter((article) => {
        const title = normalize(getArticleTitle(article))
        const short = normalize(getArticleShort(article))
        const description = normalize(getArticleDescription(article))
        const url = normalize(article?.url)
        const ownerName = normalize(article?.owner?.name)
        const ownerEmail = normalize(article?.owner?.email)

        return title.includes(query)
            || short.includes(query)
            || description.includes(query)
            || url.includes(query)
            || ownerName.includes(query)
            || ownerEmail.includes(query)
    })

    return sortArticles(filtered)
})

/** Пагинация */
const paginatedArticles = computed(() => {
    const perPage = Number(itemsPerPage.value || 10)
    const start = (currentPage.value - 1) * perPage

    return filteredArticles.value.slice(start, start + perPage)
})

watch([itemsPerPage, searchQuery], () => {
    currentPage.value = 1
})

/** Выбранные статьи */
const selectedArticles = ref([])

/** Выбрать/снять все статьи */
const toggleAll = (payload) => {
    const checked = payload?.checked ?? payload?.target?.checked ?? false
    const ids = payload?.ids ?? paginatedArticles.value.map((article) => article.id)

    if (checked) {
        selectedArticles.value = [...new Set([...selectedArticles.value, ...ids])]
    } else {
        selectedArticles.value = selectedArticles.value.filter((id) => !ids.includes(id))
    }
}

/** Выбрать или снять одну статью */
const toggleSelectArticle = (articleId) => {
    const index = selectedArticles.value.indexOf(articleId)

    if (index > -1) {
        selectedArticles.value.splice(index, 1)
    } else {
        selectedArticles.value.push(articleId)
    }
}

/** Массовое включение/выключение активности */
const bulkToggleActivity = (newActivity) => {
    if (!selectedArticles.value.length) {
        toast.warning('Выберите статьи для активации/деактивации')
        return
    }

    const idsToUpdate = [...selectedArticles.value]

    router.put(
        route('admin.actions.blogArticles.bulkUpdateActivity'),
        {
            ids: idsToUpdate,
            activity: newActivity,
        },
        {
            preserveScroll: true,
            preserveState: true,
            onSuccess: () => {
                localArticles.value = localArticles.value.map((article) => {
                    if (idsToUpdate.includes(article.id)) {
                        return { ...article, activity: newActivity }
                    }

                    return article
                })

                selectedArticles.value = []
                toast.success('Активность статей массово обновлена')
            },
            onError: (errors) => {
                const msg = errors?.ids || errors?.activity || errors?.general || 'Ошибка массового обновления активности'
                toast.error(msg)
            },
        }
    )
}

/** Массовое изменение boolean-поля */
const bulkToggleFlag = (field, newValue, routeName, successMessage) => {
    if (!selectedArticles.value.length) {
        toast.warning('Выберите статьи для массового действия')
        return
    }

    const idsToUpdate = [...selectedArticles.value]

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
                localArticles.value = localArticles.value.map((article) => {
                    if (idsToUpdate.includes(article.id)) {
                        return { ...article, [field]: newValue }
                    }

                    return article
                })

                selectedArticles.value = []
                toast.success(successMessage)
            },
            onError: (errors) => {
                const msg = errors?.ids || errors?.[field] || errors?.general || 'Ошибка массового обновления'
                toast.error(msg)
            },
        }
    )
}

/** Массовое удаление */
const bulkDelete = () => {
    if (!selectedArticles.value.length) {
        toast.warning('Выберите хотя бы одну статью для удаления.')
        return
    }

    if (!confirm('Вы уверены, что хотите удалить выбранные статьи?')) return

    router.delete(route('admin.actions.blogArticles.bulkDestroy'), {
        data: { ids: selectedArticles.value },
        preserveScroll: true,
        preserveState: false,
        onSuccess: () => {
            selectedArticles.value = []
            toast.success('Массовое удаление статей успешно завершено.')
        },
        onError: (errors) => {
            const errorKey = Object.keys(errors)[0]
            toast.error(errors[errorKey] || 'Произошла ошибка при удалении статей.')
        },
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
    } else if (action === 'left') {
        bulkToggleFlag('left', true, 'admin.actions.blogArticles.bulkUpdateLeft', 'Статьи добавлены в левую колонку')
    } else if (action === 'noLeft') {
        bulkToggleFlag('left', false, 'admin.actions.blogArticles.bulkUpdateLeft', 'Статьи убраны из левой колонки')
    } else if (action === 'main') {
        bulkToggleFlag('main', true, 'admin.actions.blogArticles.bulkUpdateMain', 'Статьи добавлены в главный блок')
    } else if (action === 'noMain') {
        bulkToggleFlag('main', false, 'admin.actions.blogArticles.bulkUpdateMain', 'Статьи убраны из главного блока')
    } else if (action === 'right') {
        bulkToggleFlag('right', true, 'admin.actions.blogArticles.bulkUpdateRight', 'Статьи добавлены в правую колонку')
    } else if (action === 'noRight') {
        bulkToggleFlag('right', false, 'admin.actions.blogArticles.bulkUpdateRight', 'Статьи убраны из правой колонке')
    } else if (action === 'delete') {
        bulkDelete()
    }

    event.target.value = ''
}

/** Одобрение / отклонение статьи */
const approveArticle = (article, status = 1, note = '') => {
    if (!article?.id) return

    router.put(
        route('admin.actions.blogArticles.approve', { blogArticle: article.id }),
        {
            moderation_status: status,
            moderation_note: note,
        },
        {
            preserveScroll: true,
            preserveState: true,
            onSuccess: () => {
                patchLocalArticle(article.id, (node) => {
                    node.moderation_status = status
                    node.is_approved = status === 1
                    node.moderation_note = note
                })

                toast.success(status === 1 ? 'Статья одобрена' : 'Статья отклонена')
            },
            onError: () => toast.error('Ошибка модерации статьи'),
        }
    )
}

/** Обновление сортировки */
const handleSortOrderUpdate = (newOrderIds) => {
    const items = newOrderIds.map((id, index) => ({
        id,
        sort: index,
    }))

    if (!items.length) return

    router.put(
        route('admin.actions.blogArticles.updateSortBulk'),
        { items },
        {
            preserveScroll: true,
            preserveState: true,
            onSuccess: () => toast.success('Сортировка статей обновлена'),
            onError: (errors) => {
                console.error('Ошибка сортировки статей:', errors)
                toast.error(errors.message || 'Ошибка обновления сортировки')
            },
        }
    )
}
</script>

<template>
    <AdminLayout :title="t('articles')">
        <template #header>
            <TitlePage>{{ t('articles') }}</TitlePage>
        </template>

        <div class="px-2 py-2 w-full max-w-12xl mx-auto">
            <div
                class="p-4 bg-slate-50 dark:bg-slate-700
                       border border-blue-400 dark:border-blue-200
                       overflow-hidden shadow-md shadow-gray-500 dark:shadow-slate-400
                       bg-opacity-95 dark:bg-opacity-95"
            >
                <div class="sm:flex sm:justify-between sm:items-center mb-3">
                    <DefaultButton :href="route('admin.blogArticles.create')">
                        <template #icon>
                            <svg class="w-4 h-4 fill-current opacity-50 shrink-0" viewBox="0 0 16 16">
                                <path
                                    d="M15 7H9V1c0-.6-.4-1-1-1S7 .4 7 1v6H1c-.6 0-1 .4-1 1s.4 1 1 1h6v6c0 .6.4 1 1 1s1-.4 1-1V9h6c.6 0 1-.4 1-1s-.4-1-1-1z"
                                />
                            </svg>
                        </template>

                        {{ t('addArticle') }}
                    </DefaultButton>
                </div>

                <SearchInput
                    v-if="articlesCount"
                    v-model="searchQuery"
                />

                <div
                    v-if="articlesCount"
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
                    v-if="articlesCount"
                    class="flex items-center justify-between mb-3"
                >
                    <CountTable>{{ articlesCount }}</CountTable>

                    <BulkActionSelect
                        v-if="articlesCount"
                        @change="handleBulkAction"
                    />

                    <ToggleViewButton v-model:viewMode="viewMode" />
                </div>

                <ArticleTable
                    v-if="viewMode === 'table'"
                    :articles="paginatedArticles"
                    :selected-articles="selectedArticles"
                    :is-admin="isAdmin"
                    @toggle-left="toggleLeft"
                    @toggle-main="toggleMain"
                    @toggle-right="toggleRight"
                    @toggle-activity="toggleActivity"
                    @delete="confirmDelete"
                    @update-sort-order="handleSortOrderUpdate"
                    @toggle-select="toggleSelectArticle"
                    @toggle-all="toggleAll"
                    @approve="approveArticle"
                />

                <ArticleCardGrid
                    v-else
                    :articles="paginatedArticles"
                    :selected-articles="selectedArticles"
                    :is-admin="isAdmin"
                    @toggle-left="toggleLeft"
                    @toggle-main="toggleMain"
                    @toggle-right="toggleRight"
                    @toggle-activity="toggleActivity"
                    @delete="confirmDelete"
                    @update-sort-order="handleSortOrderUpdate"
                    @toggle-select="toggleSelectArticle"
                    @toggle-all="toggleAll"
                    @approve="approveArticle"
                />

                <div
                    v-if="articlesCount"
                    class="flex justify-center items-center flex-col md:flex-row mt-3"
                >
                    <Pagination
                        :current-page="currentPage"
                        :items-per-page="itemsPerPage"
                        :total-items="filteredArticles.length"
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
            :onConfirm="deleteArticle"
            :cancelText="t('cancel')"
            :confirmText="t('yesDelete')"
        />
    </AdminLayout>
</template>
