<script setup>
/**
 * @version PulsarCMS 1.0
 * @author Александр Косолапов <kosolapov1976@gmail.com>
 * Список заданий (паттерн)
 */
import { defineProps, ref, computed, watch } from 'vue'
import { useI18n } from 'vue-i18n'
import { useToast } from 'vue-toastification'
import { router, Link } from '@inertiajs/vue3'

import AdminLayout from '@/Layouts/AdminLayout.vue'
import TitlePage from '@/Components/Admin/Headlines/TitlePage.vue'
import SearchInput from '@/Components/Admin/Search/SearchInput.vue'
import DefaultButton from '@/Components/Admin/Buttons/DefaultButton.vue'
import BulkActionSelect from '@/Components/Admin/Assignment/Select/BulkActionSelect.vue'
import CountTable from '@/Components/Admin/Count/CountTable.vue'
import ItemsPerPageSelect from '@/Components/Admin/Select/ItemsPerPageSelect.vue'
import AssignmentTable from '@/Components/Admin/Assignment/Table/AssignmentTable.vue'
import AssignmentCardGrid from '@/Components/Admin/Assignment/View/AssignmentCardGrid.vue'
import DangerModal from '@/Components/Admin/Modal/DangerModal.vue'
import Pagination from '@/Components/Admin/Pagination/Pagination.vue'
import SortSelect from '@/Components/Admin/Assignment/Sort/SortSelect.vue'
import ToggleViewButton from '@/Components/Admin/Buttons/ToggleViewButton.vue'

// --- Инициализация экземпляр i18n, toast ---
const { t } = useI18n()
const toast = useToast()

/**
 * Входные свойства компонента.
 */
const props = defineProps({
    assignments: Array,
    assignmentsCount: Number,
    adminCountAssignments: Number,
    adminSortAssignments: String,
    currentLocale: String,
    availableLocales: Array,
})

/** Вид: таблица или карточки (общий ключ для админки) */
const viewMode = ref(localStorage.getItem('admin_view_mode') || 'table')

watch(viewMode, (val) => {
    localStorage.setItem('admin_view_mode', val)
})

/** Кол-во элементов на странице */
const itemsPerPage = ref(props.adminCountAssignments || 10)

/**
 * Наблюдатель за изменением количества элементов на странице.
 */
watch(itemsPerPage, (newVal) => {
    router.put(route('admin.settings.updateAdminCountAssignments'), { value: newVal }, {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => toast.info(`Показ ${newVal} элементов на странице.`),
        onError: (errors) =>
            toast.error(errors.value || 'Ошибка обновления кол-ва элементов.'),
    })
})

/** Параметр сортировки */
const sortParam = ref(props.adminSortAssignments || 'idDesc')

/**
 * Наблюдатель за изменением параметра сортировки.
 */
watch(sortParam, (newVal) => {
    router.put(route('admin.settings.updateAdminSortAssignments'), { value: newVal }, {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => toast.info('Сортировка успешно изменена'),
        onError: (errors) => {
            const firstError = errors ? Object.values(errors)[0] : null
            toast.error(firstError || 'Ошибка обновления кол-ва элементов.')
        },
    })
})

/** Модалка удаления */
const showConfirmDeleteModal = ref(false)
const assignmentToDeleteId = ref(null)
const assignmentToDeleteTitle = ref('')

/**
 * Открывает модальное окно подтверждения удаления с входными переменными.
 */
const confirmDelete = (id, title) => {
    assignmentToDeleteId.value = id
    assignmentToDeleteTitle.value = title
    showConfirmDeleteModal.value = true
}

/**
 * Закрывает модальное окно подтверждения и сбрасывает связанные переменные.
 */
const closeModal = () => {
    showConfirmDeleteModal.value = false
    assignmentToDeleteId.value = null
    assignmentToDeleteTitle.value = ''
}

/**
 * Отправляет запрос на удаление.
 */
const deleteAssignment = () => {
    if (assignmentToDeleteId.value === null) return
    const idToDelete = assignmentToDeleteId.value
    const titleToDelete = assignmentToDeleteTitle.value

    router.delete(route('admin.assignments.destroy', { assignment: idToDelete }), {
        preserveScroll: true,
        preserveState: false,
        onSuccess: () => {
            closeModal()
            toast.success(`Задание "${titleToDelete || 'ID: ' + idToDelete}" удалено.`)
        },
        onError: (errors) => {
            closeModal()
            const errorMsg =
                errors.general ||
                errors[Object.keys(errors)[0]] ||
                'Произошла ошибка при удалении.'
            toast.error(`${errorMsg} (Задание: ${titleToDelete || 'ID: ' + idToDelete})`)
        },
        onFinish: () => {
            assignmentToDeleteId.value = null
            assignmentToDeleteTitle.value = ''
        },
    })
}

/**
 * Отправляет запрос для изменения статуса активности в левой колонке.
 */
const toggleLeft = (assignment) => {
    const newLeft = !assignment.left;
    const actionText = newLeft ? 'активировано в левой колонке' : 'деактивировано в левой колонке';

    // Используем Inertia.put для простого обновления
    router.put(route('admin.actions.assignments.updateLeft', {assignment: assignment.id}),
        {left: newLeft},
        {
            preserveScroll: true, // Сохраняем скролл
            preserveState: true,  // Обновляем только измененные props (если бэк отдает reload: false)
            // Или false, если бэк всегда отдает reload: true и нужно перезагрузить данные
            onSuccess: () => {
                // Обновляем состояние локально СРАЗУ ЖЕ (оптимистичное обновление)
                // Или дожидаемся обновления props, если preserveState: false
                // assignment.left = newLeft; // Уже не нужно, если preserveState: false
                toast.success(`Задание "${assignment.title}" ${actionText}.`);
            },
            onError: (errors) => {
                toast.error(errors.left || errors.general || `Ошибка изменения активности для "${assignment.title}".`);
                // Можно откатить изменение на фронте, если нужно
                // assignment.left = !newLeft;
            },
        }
    );
};

/**
 * Отправляет запрос для изменения статуса активности в главном.
 */
const toggleMain = (assignment) => {
    const newMain = !assignment.main;
    const actionText = newMain ? 'активировано в главном' : 'деактивировано в главном';

    // Используем Inertia.put для простого обновления
    router.put(route('admin.actions.assignments.updateMain', {assignment: assignment.id}),
        {main: newMain},
        {
            preserveScroll: true, // Сохраняем скролл
            preserveState: true,  // Обновляем только измененные props (если бэк отдает reload: false)
            // Или false, если бэк всегда отдает reload: true и нужно перезагрузить данные
            onSuccess: () => {
                // Обновляем состояние локально СРАЗУ ЖЕ (оптимистичное обновление)
                // Или дожидаемся обновления props, если preserveState: false
                // assignment.main = newMain; // Уже не нужно, если preserveState: false
                toast.success(`Задание "${assignment.title}" ${actionText}.`);
            },
            onError: (errors) => {
                toast.error(errors.main || errors.general || `Ошибка изменения активности для "${assignment.title}".`);
                // Можно откатить изменение на фронте, если нужно
                // assignment.main = !newMain;
            },
        }
    );
};

/**
 * Отправляет запрос для изменения статуса активности в правой колонке.
 */
const toggleRight = (assignment) => {
    const newRight = !assignment.right;
    const actionText = newRight ? 'активировано в правой колонке' : 'деактивировано в правой колонке';

    // Используем Inertia.put для простого обновления
    router.put(route('admin.actions.assignments.updateRight', {assignment: assignment.id}),
        {right: newRight},
        {
            preserveScroll: true, // Сохраняем скролл
            preserveState: true,  // Обновляем только измененные props (если бэк отдает reload: false)
            // Или false, если бэк всегда отдает reload: true и нужно перезагрузить данные
            onSuccess: () => {
                // Обновляем состояние локально СРАЗУ ЖЕ (оптимистичное обновление)
                // Или дожидаемся обновления props, если preserveState: false
                // assignment.right = newRight; // Уже не нужно, если preserveState: false
                toast.success(`Задание "${assignment.title}" ${actionText}.`);
            },
            onError: (errors) => {
                toast.error(errors.right || errors.general || `Ошибка изменения активности для "${assignment.title}".`);
                // Можно откатить изменение на фронте, если нужно
                // assignment.right = !newRight;
            },
        }
    );
};

/**
 * Отправляет запрос для клонирования.
 */
const cloneAssignment = (assignmentObject) => { // Переименовываем параметр для ясности
    // Извлекаем ID из объекта
    const assignmentId = assignmentObject?.id; // Используем опциональную цепочку на случай undefined/null
    const assignmentTitle = assignmentObject?.title || `ID: ${assignmentId}`; // Пытаемся получить title или используем ID

    // Проверяем, что ID получен
    if (typeof assignmentId === 'undefined' || assignmentId === null) {
        console.error("Не удалось получить ID задания для клонирования", assignmentObject);
        toast.error("Не удалось определить задание для клонирования.");
        return;
    }

    // Используем confirm с извлеченным ID (или title)
    if (!confirm(`Вы уверены, что хотите клонировать задание "${assignmentTitle}"?`)) {
        return;
    }

    // В route() передаем именно assignmentId
    router.post(route('admin.actions.assignments.clone', {assignment: assignmentId}), {}, {
        preserveScroll: true,
        preserveState: false,
        onSuccess: (page) => {
            // Используем assignmentTitle или assignmentId в сообщении
            toast.success(`Задание "${assignmentTitle}" успешно клонировано.`);
        },
        onError: (errors) => {
            const errorKey = Object.keys(errors)[0];
            const errorMessage = errors[errorKey] || `Ошибка клонирования задания "${assignmentTitle}".`;
            toast.error(errorMessage);
        }
    });
};

/** Пагинация и поиск */
const currentPage = ref(1)
const searchQuery = ref('')

/** Сортировка массива уроков */
const sortAssignments = (assignments) => {
    const value = sortParam.value
    const list = assignments.slice()

    if (value === 'idAsc') {
        return list.sort((a, b) => a.id - b.id)
    }

    if (value === 'idDesc') {
        return list.sort((a, b) => b.id - a.id)
    }


    if (sortParam.value === 'left') {
        return list.sort(assignment => assignment.left);
    }
    if (sortParam.value === 'noLeft') {
        return list.sort(assignment => !assignment.left);
    }
    if (sortParam.value === 'main') {
        return list.sort(assignment => assignment.main);
    }

    // Дата публикации — от новых к старым, null в конец
    if (value === 'published_at') {
        return list.sort((a, b) => {
            const aTime = a.published_at ? new Date(a.published_at).getTime() : 0
            const bTime = b.published_at ? new Date(b.published_at).getTime() : 0
            return bTime - aTime
        })
    }

    // Фильтры по активности и колонкам
    if (value === 'activity') return assignments.filter(l => l.activity)
    if (value === 'inactive') return assignments.filter(l => !l.activity)

    // status, availability, title, sort и прочее — обычная сортировка по возрастанию
    return list.sort((a, b) => {
        const av = a[value] ?? ''
        const bv = b[value] ?? ''
        if (av < bv) return -1
        if (av > bv) return 1
        return 0
    })
}

/** Фильтрация + сортировка (поиск по title) */
const filteredAssignments = computed(() => {
    let filtered = props.assignments || []

    if (searchQuery.value) {
        const q = searchQuery.value.toLowerCase()
        filtered = filtered.filter(a =>
            (a.title || '').toLowerCase().includes(q) ||
            (a.slug || '').toLowerCase().includes(q)
        )
    }

    return sortAssignments(filtered)
})

/** Пагинация */
const paginatedAssignments = computed(() => {
    const start = (currentPage.value - 1) * itemsPerPage.value
    return filteredAssignments.value.slice(start, start + itemsPerPage.value)
})

/**
 * Вычисляемое свойство, возвращающее общее количество страниц пагинации.
 */
const totalPages = computed(() =>
    Math.ceil((filteredAssignments.value.length || 0) / itemsPerPage.value)
)

/** Обновление сортировки (drag&drop) */
const handleSortOrderUpdate = (orderedIds) => {
    const startSort = (currentPage.value - 1) * itemsPerPage.value

    const sortData = orderedIds.map((id, index) => ({
        id,
        sort: startSort + index + 1,
    }))

    router.put(
        route('admin.actions.assignments.updateSortBulk'),
        { assignments: sortData },
        {
            preserveScroll: true,
            preserveState: true,
            onSuccess: () => toast.success('Порядок заданий успешно обновлён.'),
            onError: (errors) => {
                console.error('Ошибка обновления сортировки заданий:', errors)
                toast.error(
                    errors?.general ||
                    errors?.assignments ||
                    'Не удалось обновить порядок заданий.'
                )
                router.reload({ only: ['assignments'], preserveScroll: true })
            },
        }
    )
}

/** Массовые действия */
const selectedAssignments = ref([])

/**
 * Логика выбора всех для массовых действий.
 */
const toggleAll = ({ ids, checked }) => {
    if (checked) {
        selectedAssignments.value = [...ids]
    } else {
        selectedAssignments.value = []
    }
}

/**
 * Обрабатывает событие выбора/снятия выбора одной строки.
 */
const toggleSelectAssignment = (id) => {
    const idx = selectedAssignments.value.indexOf(id)
    if (idx > -1) selectedAssignments.value.splice(idx, 1)
    else selectedAssignments.value.push(id)
}

/**
 * Выполняет массовое включение/выключение активности выбранных.
 */
const bulkToggleActivity = (newActivity) => {
    if (!selectedAssignments.value.length) {
        toast.warning('Выберите задания для активации/деактивации')
        return
    }

    router.put(
        route('admin.actions.assignments.bulkUpdateActivity'),
        { ids: selectedAssignments.value, activity: newActivity },
        {
            preserveScroll: true,
            preserveState: true,
            onSuccess: () => {
                toast.success('Активность заданий массово обновлена')
                const updatedIds = [...selectedAssignments.value]
                selectedAssignments.value = []

                paginatedAssignments.value.forEach(a => {
                    if (updatedIds.includes(a.id)) a.activity = newActivity
                })
            },
            onError: (errors) => {
                const msg =
                    errors?.ids || errors?.activity || errors?.general ||
                    'Не удалось массово обновить активность заданий'
                toast.error(msg)
            },
        }
    )
}

/**
 * Выполняет массовое удаление выбранных.
 */
const bulkDelete = () => {
    if (selectedAssignments.value.length === 0) {
        toast.warning('Выберите хотя бы одно задание для удаления.'); // <--- Используем toast
        return;
    }
    if (!confirm(`Вы уверены, что хотите их удалить ?`)) {
        return;
    }
    router.delete(route('admin.actions.assignments.bulkDestroy'), {
        data: {ids: selectedAssignments.value},
        preserveScroll: true,
        preserveState: false, // Перезагружаем данные страницы
        onSuccess: (page) => {
            selectedAssignments.value = []; // Очищаем выбор
            toast.success('Массовое удаление заданий успешно завершено.');
            // console.log('Массовое удаление статей успешно завершено.');
        },
        onError: (errors) => {
            console.error("Ошибка массового удаления:", errors);
            // Отображаем первую ошибку
            const errorKey = Object.keys(errors)[0];
            const errorMessage = errors[errorKey] || 'Произошла ошибка при удалении заданий.';
            toast.error(errorMessage);
        },
    });
};

/**
 * Обрабатывает выбор действия в селекте массовых действий.
 */
const handleBulkAction = (event) => {
    const action = event.target.value

    if (action === 'selectAll') {
        selectedAssignments.value = paginatedAssignments.value.map(a => a.id)
    } else if (action === 'deselectAll') {
        selectedAssignments.value = []
    } else if (action === 'activate') {
        bulkToggleActivity(true)
    } else if (action === 'deactivate') {
        bulkToggleActivity(false)
    } else if (action === 'delete') {
        bulkDelete();
    }

    event.target.value = ''
}

/** Переключение активности одного урока */
const toggleActivity = (assignment) => {
    const newActivity = !assignment.activity;
    const actionText = newActivity ? t('activated') : t('deactivated');

    // Используем Inertia.put для простого обновления
    router.put(route('admin.actions.assignments.updateActivity', {assignment: assignment.id}),
        {activity: newActivity},
        {
            preserveScroll: true, // Сохраняем скролл
            preserveState: true,  // Обновляем только измененные props (если бэк отдает reload: false)
            // Или false, если бэк всегда отдает reload: true и нужно перезагрузить данные
            onSuccess: () => {
                // Обновляем состояние локально СРАЗУ ЖЕ (оптимистичное обновление)
                // Или дожидаемся обновления props, если preserveState: false
                // assignment.activity = newActivity; // Уже не нужно, если preserveState: false
                toast.success(`Статья "${assignment.title}" ${actionText}.`);
            },
            onError: (errors) => {
                toast.error(errors.activity || errors.general || `Ошибка изменения активности для "${assignment.title}".`);
                // Можно откатить изменение на фронте, если нужно
                // assignment.activity = !newActivity;
            },
        }
    );
};

/** Ссылка для табов локалей */
const localeLink = (locale) => route('admin.assignments.index', { locale })
</script>

<template>
    <AdminLayout :title="t('assignments')">
        <template #header>
            <TitlePage>{{ t('assignments') }}</TitlePage>
        </template>

        <div class="px-2 py-2 w-full max-w-12xl mx-auto">
            <div
                class="p-4 bg-slate-50 dark:bg-slate-700 border border-blue-400 dark:border-blue-200
                       overflow-hidden shadow-md shadow-gray-500 dark:shadow-slate-400
                       bg-opacity-95 dark:bg-opacity-95"
            >
                <div class="sm:flex sm:justify-between sm:items-center mb-2">
                    <!-- Добавить модуль -->
                    <DefaultButton :href="route('admin.assignments.create')">
                        <template #icon>
                            <svg class="w-4 h-4 fill-current opacity-50 shrink-0" viewBox="0 0 16 16">
                                <path
                                    d="M15 7H9V1c0-.6-.4-1-1-1S7 .4 7 1v6H1c-.6 0-1 .4-1 1s.4 1 1 1h6v6c0 .6.4 1 1 1s1-.4 1-1V9h6c.6 0 1-.4 1-1s-.4-1-1-1z"
                                />
                            </svg>
                        </template>
                        {{ t('addAssignment') }}
                    </DefaultButton>

                    <BulkActionSelect
                        v-if="assignmentsCount"
                        @change="handleBulkAction"
                    />
                </div>

                <!-- Локали + счётчик -->
                <div class="flex items-center justify-between mt-5">
                    <div
                        class="flex items-center justify-end space-x-2 px-3 py-1
                               border-x border-t border-gray-400 rounded-t-lg
                               bg-gray-100 dark:bg-gray-900"
                    >
                        <span class="text-sm font-medium text-slate-700 dark:text-slate-200">
                            {{ t('localization') }}:
                        </span>
                        <template v-for="locale in availableLocales" :key="locale">
                            <Link
                                :href="localeLink(locale)"
                                :class="[
                                    'px-3 py-1 text-sm font-medium rounded-sm',
                                    currentLocale === locale
                                        ? 'bg-blue-500 text-white'
                                        : 'bg-slate-100 dark:bg-slate-900 text-slate-700 dark:text-slate-200 hover:bg-slate-300 dark:hover:bg-slate-600'
                                ]"
                                preserve-scroll
                                preserve-state
                            >
                                {{ locale.toUpperCase() }}
                            </Link>
                        </template>
                    </div>

                    <div class="flex items-center space-x-3">
                        <CountTable v-if="assignmentsCount">
                            {{ assignmentsCount }}
                        </CountTable>

                        <ToggleViewButton v-model:viewMode="viewMode" />
                    </div>
                </div>

                <SearchInput
                    v-if="assignmentsCount"
                    v-model="searchQuery"
                    :placeholder="t('searchByName')"
                />

                <!-- Таблица / Карточки -->
                <AssignmentTable
                    v-if="viewMode === 'table'"
                    :assignments="paginatedAssignments"
                    :selected-assignments="selectedAssignments"
                    @toggle-activity="toggleActivity"
                    @toggle-left="toggleLeft"
                    @toggle-main="toggleMain"
                    @toggle-right="toggleRight"
                    @delete="confirmDelete"
                    @clone="cloneAssignment"
                    @update-sort-order="handleSortOrderUpdate"
                    @toggle-select="toggleSelectAssignment"
                    @toggle-all="toggleAll"
                />

                <AssignmentCardGrid
                    v-else
                    :assignments="paginatedAssignments"
                    :selected-assignments="selectedAssignments"
                    @toggle-activity="toggleActivity"
                    @toggle-left="toggleLeft"
                    @toggle-main="toggleMain"
                    @toggle-right="toggleRight"
                    @delete="confirmDelete"
                    @clone="cloneAssignment"
                    @update-sort-order="handleSortOrderUpdate"
                    @toggle-select="toggleSelectAssignment"
                    @toggle-all="toggleAll"
                />

                <div
                    class="flex justify-between items-center flex-col md:flex-row my-1"
                    v-if="assignmentsCount"
                >
                    <ItemsPerPageSelect
                        :items-per-page="itemsPerPage"
                        @update:itemsPerPage="itemsPerPage = $event"
                    />
                    <Pagination
                        :current-page="currentPage"
                        :items-per-page="itemsPerPage"
                        :total-items="filteredAssignments.length"
                        @update:currentPage="currentPage = $event"
                        @update:itemsPerPage="itemsPerPage = $event"
                    />
                    <SortSelect
                        :sortParam="sortParam"
                        @update:sortParam="val => (sortParam = val)"
                    />
                </div>
            </div>
        </div>

        <DangerModal
            :show="showConfirmDeleteModal"
            @close="closeModal"
            :onCancel="closeModal"
            :onConfirm="deleteAssignment"
            :cancelText="t('cancel')"
            :confirmText="t('yesDelete')"
        />
    </AdminLayout>
</template>
