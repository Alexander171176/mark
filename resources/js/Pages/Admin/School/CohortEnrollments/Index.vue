<script setup>
/**
 * @version PulsarCMS 1.0
 * @author Александр Косолапов
 */
import { defineProps, ref, computed, watch } from 'vue';
import { useI18n } from 'vue-i18n';
import { useToast } from 'vue-toastification';
import { router } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import TitlePage from '@/Components/Admin/Headlines/TitlePage.vue';
import Pagination from '@/Components/Admin/Pagination/Pagination.vue';
import SearchInput from '@/Components/Admin/Search/SearchInput.vue';
import CohortEnrollmentTable from '@/Components/Admin/CohortEnrollment/Table/CohortEnrollmentTable.vue';
import CohortEnrollmentCardGrid from '@/Components/Admin/CohortEnrollment/View/CohortEnrollmentCardGrid.vue';
import CountTable from '@/Components/Admin/Count/CountTable.vue';
import ItemsPerPageSelect from '@/Components/Admin/Select/ItemsPerPageSelect.vue';
import SortSelect from '@/Components/Admin/CohortEnrollment/Sort/SortSelect.vue';
import ToggleViewButton from '@/Components/Admin/Buttons/ToggleViewButton.vue';

// --- i18n, toast ---
const { t } = useI18n();
const toast = useToast();

/**
 * Входные свойства компонента.
 */
const props = defineProps([
    'enrollments',                // Array (из CohortEnrollmentResource::collection(...)->resolve())
    'enrollmentsCount',           // Общее количество (на всякий случай, но мы считаем и по массиву)
    'adminCountCohortEnrollments',
    'adminSortCohortEnrollments',
]);

/**
 * Вид: таблица или карточки (общий ключ, как у Courses / Lessons / Modules)
 */
const viewMode = ref(localStorage.getItem('admin_view_mode') || 'table')

watch(viewMode, (val) => {
    localStorage.setItem('admin_view_mode', val)
})

/**
 * Кол-во на странице (локально).
 */
const itemsPerPage = ref(props.adminCountCohortEnrollments ?? 10);

/**
 * Обновление значения в настройках (count).
 */
watch(itemsPerPage, (newVal) => {
    router.put(route('admin.settings.updateAdminCountCohortEnrollments'), { value: newVal }, {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => toast.info(`Показ ${newVal} элементов на странице.`),
        onError: (errors) => toast.error(errors.value || 'Ошибка обновления кол-ва элементов.'),
    });
});

/**
 * Параметр сортировки.
 * По смыслу — строка вида "idAsc", "idDesc", "status", "enrolled_at" и т.п.
 */
const sortParam = ref(props.adminSortCohortEnrollments ?? 'idDesc');

/**
 * Обновление значения в настройках (sort).
 */
watch(sortParam, (newVal) => {
    router.put(route('admin.settings.updateAdminSortCohortEnrollments'), { value: newVal }, {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => toast.info('Сортировка успешно изменена'),
        onError: (errors) => toast.error(errors.value || 'Ошибка обновления сортировки.'),
    });
});

/**
 * Текущая страница.
 */
const currentPage = ref(1);

/**
 * Поисковый запрос (фронтовый).
 */
const searchQuery = ref('');

/**
 * Сортировка массива на основе sortParam.
 *
 * Сейчас:
 * - 'idAsc'  / 'idDesc' — по id
 * - любое другое значение трактуем как имя поля (по нему asc)
 *
 * Если захочешь — можешь расширить под формат "fieldAsc"/"fieldDesc".
 */
const sortEnrollments = (enrollments) => {
    const list = enrollments.slice()
    const sort = sortParam.value

    if (!sort) return list

    // 🔹 1) ID
    if (sort === 'idAsc') {
        return list.sort((a, b) => a.id - b.id)
    }
    if (sort === 'idDesc') {
        return list.sort((a, b) => b.id - a.id)
    }

    // 🔹 2) Статус (кастомный порядок)
    // pending → approved → rejected → cancelled
    const statusOrder = {
        pending: 1,
        approved: 2,
        rejected: 3,
        cancelled: 4,
    }

    if (sort === 'statusAsc' || sort === 'status' /* совместимость со старым значением */) {
        return list.sort((a, b) => {
            const av = statusOrder[a.status] ?? 999
            const bv = statusOrder[b.status] ?? 999
            if (av < bv) return -1
            if (av > bv) return 1
            return 0
        })
    }

    if (sort === 'statusDesc') {
        return list.sort((a, b) => {
            const av = statusOrder[a.status] ?? 999
            const bv = statusOrder[b.status] ?? 999
            if (av < bv) return 1
            if (av > bv) return -1
            return 0
        })
    }

    // 🔹 3) Дата зачисления (enrolled_at)
    if (sort === 'enrolledAtAsc' || sort === 'enrolledAtDesc') {
        return list.sort((a, b) => {
            const ad = a.enrolled_at ? new Date(a.enrolled_at).getTime() : null
            const bd = b.enrolled_at ? new Date(b.enrolled_at).getTime() : null

            // null-даты отправляем в конец
            if (ad == null && bd == null) return 0
            if (ad == null) return 1
            if (bd == null) return -1

            if (sort === 'enrolledAtAsc') {
                return ad - bd
            } else {
                return bd - ad
            }
        })
    }

    // 🔹 4) Имя пользователя (user.name)
    if (sort === 'userNameAsc' || sort === 'userNameDesc') {
        return list.sort((a, b) => {
            const an = (a.user?.name || '').toString().toLowerCase()
            const bn = (b.user?.name || '').toString().toLowerCase()

            if (an < bn) return sort === 'userNameAsc' ? -1 : 1
            if (an > bn) return sort === 'userNameAsc' ? 1 : -1
            return 0
        })
    }

    // 🔹 5) Название потока (schedule.title)
    if (sort === 'scheduleTitleAsc' || sort === 'scheduleTitleDesc') {
        return list.sort((a, b) => {
            const at = (a.schedule?.title || '').toString().toLowerCase()
            const bt = (b.schedule?.title || '').toString().toLowerCase()

            if (at < bt) return sort === 'scheduleTitleAsc' ? -1 : 1
            if (at > bt) return sort === 'scheduleTitleAsc' ? 1 : -1
            return 0
        })
    }

    // 🔹 6) Фолбэк: простая сортировка по полю asc (если вдруг что-то новое появится)
    return list.sort((a, b) => {
        const av = a[sort]
        const bv = b[sort]

        if (av == null && bv == null) return 0
        if (av == null) return -1
        if (bv == null) return 1

        if (av < bv) return -1
        if (av > bv) return 1
        return 0
    })
}

/**
 * Фильтрация + поиск на фронте.
 * Ищем по:
 * - status
 * - user.name
 * - user.email
 * - schedule.title
 */
const filteredEnrollments = computed(() => {
    let filtered = Array.isArray(props.enrollments) ? props.enrollments : [];

    if (searchQuery.value) {
        const q = searchQuery.value.toLowerCase();

        filtered = filtered.filter((enrollment) => {
            const status = (enrollment.status ?? '').toString().toLowerCase();
            const name = (enrollment.user?.name ?? '').toString().toLowerCase();
            const email = (enrollment.user?.email ?? '').toString().toLowerCase();
            const schedule = (enrollment.schedule?.title ?? '').toString().toLowerCase();

            return (
                status.includes(q) ||
                name.includes(q) ||
                email.includes(q) ||
                schedule.includes(q)
            );
        });
    }

    return sortEnrollments(filtered);
});

/**
 * Пагинация на фронте.
 */
const paginatedEnrollments = computed(() => {
    const start = (currentPage.value - 1) * itemsPerPage.value;
    return filteredEnrollments.value.slice(start, start + itemsPerPage.value);
});

/**
 * Общее количество страниц.
 */
const totalPages = computed(() => {
    if (!itemsPerPage.value) return 1;
    return Math.ceil(filteredEnrollments.value.length / itemsPerPage.value) || 1;
});

// Если текущая страница вдруг больше, чем totalPages (например, после фильтрации) — откатываем.
watch(filteredEnrollments, () => {
    if (currentPage.value > totalPages.value) {
        currentPage.value = totalPages.value;
    }
});
</script>

<template>
    <AdminLayout :title="t('cohortEnrollments')">
        <template #header>
            <TitlePage>
                {{ t('cohortEnrollments') }}
            </TitlePage>
        </template>

        <div class="px-2 py-2 w-full max-w-12xl mx-auto">
            <div
                class="p-4 bg-slate-50 dark:bg-slate-700 border border-blue-400 dark:border-blue-200
                       overflow-hidden shadow-md shadow-gray-500 dark:shadow-slate-400
                       bg-opacity-95 dark:bg-opacity-95"
            >
                <div class="sm:flex sm:justify-between sm:items-center mb-2">
                    <!-- Кнопка "Добавить" здесь не нужна, записи создаются на публичной части -->
                </div>

                <!-- Поиск по фронту -->
                <SearchInput
                    v-if="enrollmentsCount"
                    v-model="searchQuery"
                    :placeholder="t('search')"
                />

                <!-- Кол-во записей + переключатель вида -->
                <div
                    v-if="enrollmentsCount"
                    class="flex items-center justify-between my-2"
                >
                    <CountTable>
                        {{ enrollmentsCount }}
                    </CountTable>

                    <ToggleViewButton v-model:viewMode="viewMode" />
                </div>

                <!-- Таблица с записями -->
                <CohortEnrollmentTable
                    v-if="viewMode === 'table'"
                    :enrollments="paginatedEnrollments"
                />

                <!-- Карточки с записями -->
                <CohortEnrollmentCardGrid
                    v-else
                    :enrollments="paginatedEnrollments"
                />


                <!-- Управление пагинацией / сортировкой -->
                <div
                    v-if="enrollmentsCount"
                    class="flex justify-between items-center flex-col md:flex-row my-1"
                >
                    <ItemsPerPageSelect
                        :items-per-page="itemsPerPage"
                        @update:itemsPerPage="itemsPerPage = $event"
                    />

                    <Pagination
                        :current-page="currentPage"
                        :items-per-page="itemsPerPage"
                        :total-items="filteredEnrollments.length"
                        @update:currentPage="currentPage = $event"
                        @update:itemsPerPage="itemsPerPage = $event"
                    />

                    <SortSelect
                        :sortParam="sortParam"
                        @update:sortParam="val => sortParam = val"
                    />
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
