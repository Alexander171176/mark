<script setup>
/**
 * @version PulsarCMS 1.0
 * @author Александр Косолапов <kosolapov1976@gmail.com>
 *
 * Список инструкторов (вид в строку и карточками)
 * - Выбор количества элементов на странице
 * - Сортировка
 * - Массовые действия
 * - Поиск
 * - Пагинация
 */
import { defineProps, ref, computed, watch } from 'vue';
import { useI18n } from 'vue-i18n';
import { useToast } from 'vue-toastification';
import { router } from '@inertiajs/vue3';

import AdminLayout from '@/Layouts/AdminLayout.vue';
import TitlePage from '@/Components/Admin/UI/Headlines/TitlePage.vue';
import SearchInput from '@/Components/Admin/UI/Search/SearchInput.vue';
import DefaultButton from "@/Components/Admin/UI/Buttons/DefaultButton.vue";
import CountTable from '@/Components/Admin/UI/Count/CountTable.vue';
import ItemsPerPageSelect from "@/Components/Admin/UI/Select/ItemsPerPageSelect.vue";
import DangerModal from '@/Components/Admin/UI/Modal/DangerModal.vue';
import Pagination from '@/Components/Admin/UI/Pagination/Pagination.vue';
import ToggleViewButton from '@/Components/Admin/UI/Buttons/ToggleViewButton.vue'

import SortSelect from "@/Components/Admin/School/SchoolInstructorProfile/Sort/SortSelect.vue";
import BulkActionSelect from '@/Components/Admin/School/SchoolInstructorProfile/Select/BulkActionSelect.vue';
import InstructorProfileTable from '@/Components/Admin/School/SchoolInstructorProfile/Table/InstructorProfileTable.vue';
import InstuctorCardGrid from '@/Components/Admin/School/SchoolInstructorProfile/View/InstuctorCardGrid.vue'

// --- Инициализация экземпляр i18n, toast ---
const { t } = useI18n();
const toast = useToast();

/**
 * Входные свойства компонента.
 */
const props = defineProps({
    instructorProfiles: Array,
    instructorProfilesCount: Number,
    adminSchoolInstructorsPerPage: Number,
    adminSchoolInstructorsDefaultSort: String,
    currentLocale: String,
    availableLocales: Array,
});

/**
 * Вид: таблица или карточки.
 * table | cards
 */
// 1. Загружаем состояние из localStorage
const viewMode = ref(localStorage.getItem('admin_view_mode') || 'table')

// 2. Сохраняем при каждом изменении
watch(viewMode, (val) => {
    localStorage.setItem('admin_view_mode', val)
})

/**
 * Реактивная переменная для хранения текущего количества элементов на странице.
 */
const itemsPerPage = ref(props.adminSchoolInstructorsPerPage);

/**
 * Наблюдатель за изменением количества элементов на странице.
 */
watch(itemsPerPage, (newVal) => {
    router.put(route('admin.settings.updateAdminCountInstructors'), { value: newVal }, {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => toast.info(`Показ ${newVal} элементов на странице.`),
        onError: (errors) => toast.error(errors.value || 'Ошибка обновления кол-ва элементов.'),
    });
});

/**
 * Реактивная переменная для хранения текущего параметра сортировки.
 */
const sortParam = ref(props.adminSchoolInstructorsDefaultSort);

/**
 * Наблюдатель за изменением параметра сортировки.
 */
watch(sortParam, (newVal) => {
    router.put(route('admin.settings.updateAdminSortInstructors'), { value: newVal }, {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => toast.info('Сортировка успешно изменена'),
        onError: (errors) => toast.error(errors.value || 'Ошибка обновления сортировки.'),
    });
});

/**
 * Флаг отображения модального окна подтверждения удаления.
 */
const showConfirmDeleteModal = ref(false);

/**
 * ID для удаления.
 */
const instructorProfileToDeleteId = ref(null);

/**
 * Название для отображения в модальном окне.
 */
const instructorProfileToDeleteTitle = ref('');

/**
 * Открывает модальное окно подтверждения удаления с входными переменными.
 */
const confirmDelete = (id, title) => {
    instructorProfileToDeleteId.value = id;
    instructorProfileToDeleteTitle.value = title;
    showConfirmDeleteModal.value = true;
};

/**
 * Закрывает модальное окно подтверждения и сбрасывает связанные переменные.
 */
const closeModal = () => {
    showConfirmDeleteModal.value = false;
    instructorProfileToDeleteId.value = null;
    instructorProfileToDeleteTitle.value = '';
};

/**
 * Отправляет запрос на удаление.
 */
const deleteInstructorProfile = () => {
    if (instructorProfileToDeleteId.value === null) return;
    const idToDelete = instructorProfileToDeleteId.value;
    const titleToDelete = instructorProfileToDeleteTitle.value;
    router.delete(route('admin.schoolInstructorProfiles.destroy',
        { schoolInstructorProfile: idToDelete }), {
        preserveScroll: true,
        preserveState: false,
        onSuccess: () => {
            closeModal();
            toast.success(`Инструктор "${titleToDelete || 'ID: ' + idToDelete}" удален.`);
        },
        onError: (errors) => {
            closeModal();
            const errorMsg = errors.general || errors[Object.keys(errors)[0]]
                || 'Произошла ошибка при удалении.';
            toast.error(`${errorMsg} (Инструктор: ${titleToDelete || 'ID: ' + idToDelete})`);
        },
        onFinish: () => {
            instructorProfileToDeleteId.value = null;
            instructorProfileToDeleteTitle.value = '';
        }
    });
};

/**
 * Текущая страница пагинации.
 */
const currentPage = ref(1);

/**
 * Строка поискового запроса.
 */
const searchQuery = ref('');

/**
 * Сортирует массив на основе текущего параметра сортировки.
 */
const sortInstructorProfiles = (instructorProfiles) => {
    const items = instructorProfiles.slice();

    const safeString = (value) => (value ?? '').toString().toLowerCase();
    const safeNumber = (value) => Number(value ?? 0);
    const safeDate = (value) => {
        const time = new Date(value ?? 0).getTime();
        return Number.isNaN(time) ? 0 : time;
    };

    switch (sortParam.value) {
        case 'idAsc':
            return items.sort((a, b) => safeNumber(a.id) - safeNumber(b.id));

        case 'idDesc':
            return items.sort((a, b) => safeNumber(b.id) - safeNumber(a.id));

        case 'sortAsc':
            return items.sort((a, b) => safeNumber(a.sort) - safeNumber(b.sort));

        case 'sortDesc':
            return items.sort((a, b) => safeNumber(b.sort) - safeNumber(a.sort));

        case 'titleAsc':
            return items.sort((a, b) => safeString(a.title).localeCompare(safeString(b.title)));

        case 'titleDesc':
            return items.sort((a, b) => safeString(b.title).localeCompare(safeString(a.title)));

        case 'slugAsc':
            return items.sort((a, b) => safeString(a.slug).localeCompare(safeString(b.slug)));

        case 'slugDesc':
            return items.sort((a, b) => safeString(b.slug).localeCompare(safeString(a.slug)));

        case 'ratingAvgAsc':
            return items.sort((a, b) => safeNumber(a.rating?.avg ?? a.rating_avg)
                - safeNumber(b.rating?.avg ?? b.rating_avg));

        case 'ratingAvgDesc':
            return items.sort((a, b) => safeNumber(b.rating?.avg ?? b.rating_avg)
                - safeNumber(a.rating?.avg ?? a.rating_avg));

        case 'ratingCountAsc':
            return items.sort((a, b) => safeNumber(a.rating?.count ?? a.rating_count)
                - safeNumber(b.rating?.count ?? b.rating_count));

        case 'ratingCountDesc':
            return items.sort((a, b) => safeNumber(b.rating?.count ?? b.rating_count)
                - safeNumber(a.rating?.count ?? a.rating_count));

        case 'hourlyRateAsc':
            return items.sort((a, b) => safeNumber(a.hourly_rate)
                - safeNumber(b.hourly_rate));

        case 'hourlyRateDesc':
            return items.sort((a, b) => safeNumber(b.hourly_rate)
                - safeNumber(a.hourly_rate));

        case 'experienceAsc':
            return items.sort((a, b) => safeNumber(a.experience_years)
                - safeNumber(b.experience_years));

        case 'experienceDesc':
            return items.sort((a, b) => safeNumber(b.experience_years)
                - safeNumber(a.experience_years));

        case 'viewsAsc':
            return items.sort((a, b) => safeNumber(a.views)
                - safeNumber(b.views));

        case 'viewsDesc':
            return items.sort((a, b) => safeNumber(b.views)
                - safeNumber(a.views));

        case 'createdAtAsc':
            return items.sort((a, b) => safeDate(a.created_at)
                - safeDate(b.created_at));

        case 'createdAtDesc':
            return items.sort((a, b) => safeDate(b.created_at)
                - safeDate(a.created_at));

        case 'updatedAtAsc':
            return items.sort((a, b) => safeDate(a.updated_at)
                - safeDate(b.updated_at));

        case 'updatedAtDesc':
            return items.sort((a, b) => safeDate(b.updated_at)
                - safeDate(a.updated_at));

        case 'activity':
            return items.filter(i => i.activity);

        case 'inactive':
            return items.filter(i => !i.activity);

        case 'activityAsc':
            return items.sort((a, b) => Number(a.activity) - Number(b.activity));

        case 'activityDesc':
            return items.sort((a, b) => Number(b.activity) - Number(a.activity));

        case 'coursesAsc':
            return items.sort((a, b) => safeNumber(a.courses_count)
                - safeNumber(b.courses_count));

        case 'coursesDesc':
            return items.sort((a, b) => safeNumber(b.courses_count)
                - safeNumber(a.courses_count));

        default:
            return items.sort((a, b) => safeNumber(a.sort) - safeNumber(b.sort));
    }
};

/**
 * Вычисляемое свойство, отсортированный список поиска.
 */
const filteredInstructorProfiles = computed(() => {
    let filtered = props.instructorProfiles || []

    if (searchQuery.value) {
        const query = searchQuery.value.toLowerCase()

        filtered = filtered.filter(i => {
            const title = (i.title || '').toLowerCase()
            const userName = (i.user?.name || '').toLowerCase()
            const slug = (i.slug || '').toLowerCase()

            const hasCourse = (i.courses || []).some(course =>
                (course.title || '').toLowerCase().includes(query)
            )

            return (
                title.includes(query) ||
                userName.includes(query) ||
                slug.includes(query) ||
                hasCourse
            )
        })
    }

    return sortInstructorProfiles(filtered)
})

/**
 * Вычисляемое свойство пагинации, возвращающее для текущей страницы.
 */
const paginatedInstructorProfiles = computed(() => {
    const start = (currentPage.value - 1) * itemsPerPage.value;
    return filteredInstructorProfiles.value.slice(start, start + itemsPerPage.value);
});

/**
 * Обрабатывает событие обновления порядка сортировки от компонента таблицы (Drag and drop).
 */
const handleSortOrderUpdate = (orderedIds) => {
    const startSort = (currentPage.value - 1) * itemsPerPage.value;

    const sortData = orderedIds.map((id, index) => ({
        id,
        sort: startSort + index + 1,
    }));

    router.put(
        route('admin.actions.schoolInstructorProfiles.updateSortBulk'),
        { items: sortData },
        {
            preserveScroll: true,
            preserveState: true,
            onSuccess: () => toast.success("Порядок инструкторов успешно обновлен."),
            onError: (errors) => {
                console.error("Ошибка обновления сортировки:", errors);
                toast.error(
                    errors?.general ||
                    errors?.instructorProfiles ||
                    "Не удалось обновить порядок инструкторов."
                );
                router.reload({ only: ['instructorProfiles'], preserveScroll: true });
            },
        }
    );
};

/**
 * Массив выбранных ID для массовых действий.
 */
const selectedInstructorProfiles = ref([]);

/**
 * Логика выбора всех для массовых действий.
 */
const toggleAll = ({ ids, checked }) => {
    if (checked) {
        // Выбрать всех
        selectedInstructorProfiles.value = [...ids];
    } else {
        // Снять выделение
        selectedInstructorProfiles.value = [];
    }
};

/**
 * Обрабатывает событие выбора/снятия выбора одной строки.
 */
const toggleSelectInstructorProfile = (id) => {
    const idx = selectedInstructorProfiles.value.indexOf(id);
    if (idx > -1) selectedInstructorProfiles.value.splice(idx, 1);
    else selectedInstructorProfiles.value.push(id);
};

/**
 * Выполняет массовое включение/выключение активности выбранных.
 */
const bulkToggleActivity = (newActivity) => {
    if (!selectedInstructorProfiles.value.length) {
        toast.warning('Выберите инструкторов для активации/деактивации');
        return;
    }
    router.put(route('admin.actions.schoolInstructorProfiles.bulkUpdateActivity'),
        { ids: selectedInstructorProfiles.value, activity: newActivity },
        {
            preserveScroll: true,
            preserveState: true,
            onSuccess: () => {
                toast.success('Активность массово обновлена');
                const updatedIds = [...selectedInstructorProfiles.value];
                selectedInstructorProfiles.value = [];
                paginatedInstructorProfiles.value.forEach(a => {
                    if (updatedIds.includes(a.id)) a.activity = newActivity;
                });
            },
            onError: (errors) => {
                const msg = errors?.ids || errors?.activity || errors?.general
                    || 'Не удалось обновить активность';
                toast.error(msg);
            },
        }
    );
};

/**
 * Обрабатывает выбор действия в селекте массовых действий.
 */
const handleBulkAction = (event) => {
    const action = event.target.value;
    if (action === 'selectAll') {
        selectedInstructorProfiles.value = paginatedInstructorProfiles.value.map(r => r.id);
    } else if (action === 'deselectAll') {
        selectedInstructorProfiles.value = [];
    } else if (action === 'activate') {
        bulkToggleActivity(true);
    } else if (action === 'deactivate') {
        bulkToggleActivity(false);
    }
    event.target.value = '';
};

/**
 * Отправляет запрос для изменения статуса активности.
 */
const toggleActivity = (instructorProfile) => {
    const newActivity = !instructorProfile.activity;
    const actionText = newActivity ? t('activated') : t('deactivated');

    router.put(
        route('admin.actions.schoolInstructorProfiles.updateActivity',
            { instructorProfile: instructorProfile.id }),
        { activity: newActivity },
        {
            preserveScroll: true,
            preserveState: true,
            onSuccess: () => {
                // ЛОКАЛЬНО обновляем объект, чтобы UI сразу отразил статус
                instructorProfile.activity = newActivity;
                toast.success(`Инструктор "${instructorProfile.title}" ${actionText}.`);
            },
            onError: (errors) => {
                toast.error(
                    errors?.activity ||
                    errors?.general ||
                    `Ошибка изменения активности для "${instructorProfile.title}".`
                );
            },
        }
    );
};

</script>

<template>
    <AdminLayout :title="t('instructors')">
        <template #header>
            <TitlePage>
                {{ t('instructors') }}
            </TitlePage>
        </template>

        <div class="px-2 py-2 w-full max-w-12xl mx-auto">
            <div
                class="p-4 bg-slate-50 dark:bg-slate-700
                       border border-blue-400 dark:border-blue-200
                       overflow-hidden shadow-md shadow-gray-500 dark:shadow-slate-400
                       bg-opacity-95 dark:bg-opacity-95"
            >

                <div class="sm:flex sm:justify-between sm:items-center mb-3">
                    <!-- Кнопка добавить инструктора -->
                    <DefaultButton :href="route('admin.schoolInstructorProfiles.create')">
                        <template #icon>
                            <svg class="w-4 h-4 fill-current opacity-50 shrink-0"
                                 viewBox="0 0 16 16">
                                <path
                                    d="M15 7H9V1c0-.6-.4-1-1-1S7 .4 7 1v6H1c-.6 0-1 .4-1 1s.4 1 1 1h6v6c0 .6.4 1 1 1s1-.4 1-1V9h6c.6 0 1-.4 1-1s-.4-1-1-1z" />
                            </svg>
                        </template>
                        {{ t('addInstructor') }}
                    </DefaultButton>
                </div>

                <SearchInput
                    v-if="instructorProfilesCount"
                    v-model="searchQuery"
                    :placeholder="t('searchByName')" />

                <div
                    v-if="instructorProfilesCount"
                    class="flex justify-between items-center flex-col md:flex-row my-3"
                >
                    <ItemsPerPageSelect
                        :items-per-page="itemsPerPage"
                        @update:itemsPerPage="itemsPerPage = $event" />
                    <SortSelect
                        :sortParam="sortParam"
                        @update:sortParam="val => sortParam = val" />
                </div>

                <div
                    v-if="instructorProfilesCount"
                    class="flex flex-col lg:flex-row items-center justify-between gap-3"
                >
                    <CountTable>{{ instructorProfilesCount }}</CountTable>
                    <BulkActionSelect
                        v-if="instructorProfilesCount"
                        @change="handleBulkAction" />
                    <ToggleViewButton v-model:viewMode="viewMode" />
                </div>

                <div
                    v-if="instructorProfilesCount"
                    class="flex justify-center items-center flex-col md:flex-row mt-3">
                    <Pagination
                        :current-page="currentPage"
                        :items-per-page="itemsPerPage"
                        :total-items="filteredInstructorProfiles.length"
                        @update:currentPage="currentPage = $event"
                        @update:itemsPerPage="itemsPerPage = $event"
                    />
                </div>

                <!-- Таблица -->
                <InstructorProfileTable
                    v-if="viewMode === 'table'"
                    :instructor-profiles="paginatedInstructorProfiles"
                    :selected-instructor-profiles="selectedInstructorProfiles"
                    @toggle-activity="toggleActivity"
                    @delete="confirmDelete"
                    @update-sort-order="handleSortOrderUpdate"
                    @toggle-select="toggleSelectInstructorProfile"
                    @toggle-all="toggleAll"
                />

                <!-- Карточки -->
                <InstuctorCardGrid
                    v-else
                    :instructor-profiles="paginatedInstructorProfiles"
                    :selected-instructor-profiles="selectedInstructorProfiles"
                    @toggle-activity="toggleActivity"
                    @delete="confirmDelete"
                    @toggle-select="toggleSelectInstructorProfile"
                    @toggle-all="toggleAll"
                    @update-sort-order="handleSortOrderUpdate"
                />

                <div
                    v-if="instructorProfilesCount"
                    class="flex justify-center items-center flex-col md:flex-row mt-3">
                    <Pagination
                        :current-page="currentPage"
                        :items-per-page="itemsPerPage"
                        :total-items="filteredInstructorProfiles.length"
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
            :onConfirm="deleteInstructorProfile"
            :cancelText="t('cancel')"
            :confirmText="t('yesDelete')"
        />
    </AdminLayout>
</template>
