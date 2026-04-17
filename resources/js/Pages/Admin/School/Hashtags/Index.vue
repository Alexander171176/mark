<script setup>
/**
 * @version PulsarCMS 1.0
 * @author Александр Косолапов <kosolapov1976@gmail.com>
 * Список хештегов (паттерн)
 */
import { defineProps, ref, computed, watch } from 'vue';
import { useI18n } from 'vue-i18n';
import { useToast } from 'vue-toastification';
import { router, Link } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import TitlePage from '@/Components/Admin/Headlines/TitlePage.vue';
import SearchInput from '@/Components/Admin/Search/SearchInput.vue';
import DefaultButton from "@/Components/Admin/Buttons/DefaultButton.vue";
import BulkActionSelect from '@/Components/Admin/Hashtag/Select/BulkActionSelect.vue';
import CountTable from '@/Components/Admin/Count/CountTable.vue';
import ItemsPerPageSelect from "@/Components/Admin/Select/ItemsPerPageSelect.vue";
import HashtagTable from '@/Components/Admin/Hashtag/Table/HashtagTable.vue';
import HashtagCardGrid from '@/Components/Admin/Hashtag/View/HashtagCardGrid.vue';
import DangerModal from '@/Components/Admin/Modal/DangerModal.vue';
import Pagination from '@/Components/Admin/Pagination/Pagination.vue';
import SortSelect from "@/Components/Admin/Hashtag/Sort/SortSelect.vue";
import ToggleViewButton from '@/Components/Admin/Buttons/ToggleViewButton.vue';

// --- Инициализация экземпляр i18n, toast ---
const { t } = useI18n();
const toast = useToast();

/**
 * Вид: таблица или карточки.
 * table | cards
 */
const viewMode = ref(localStorage.getItem('admin_view_mode') || 'table')

watch(viewMode, (val) => {
    localStorage.setItem('admin_view_mode', val)
})

/**
 * Входные свойства компонента.
 */
const props = defineProps({
    hashtags: Array,
    hashtagsCount: Number,
    adminCountHashtags: Number,
    adminSortHashtags: String,
    currentLocale: String,
    availableLocales: Array,
});

/**
 * Реактивная переменная для хранения текущего количества элементов на странице.
 */
const itemsPerPage = ref(props.adminCountHashtags);

/**
 * Наблюдатель за изменением количества элементов на странице.
 */
watch(itemsPerPage, (newVal) => {
    router.put(route('admin.settings.updateAdminCountHashtags'), { value: newVal }, {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => toast.info(`Показ ${newVal} элементов на странице.`),
        onError: (errors) => toast.error(errors.value || 'Ошибка обновления кол-ва элементов.'),
    });
});

/**
 * Реактивная переменная для хранения текущего параметра сортировки.
 */
const sortParam = ref(props.adminSortHashtags);

/**
 * Наблюдатель за изменением параметра сортировки.
 */
watch(sortParam, (newVal) => {
    router.put(route('admin.settings.updateAdminSortHashtags'), { value: newVal }, {
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
const hashtagToDeleteId = ref(null);

/**
 * Название для отображения в модальном окне.
 */
const hashtagToDeleteName = ref('');

/**
 * Открывает модальное окно подтверждения удаления с входными переменными.
 */
const confirmDelete = (id, name) => {
    hashtagToDeleteId.value = id;
    hashtagToDeleteName.value = name;
    showConfirmDeleteModal.value = true;
};

/**
 * Закрывает модальное окно подтверждения и сбрасывает связанные переменные.
 */
const closeModal = () => {
    showConfirmDeleteModal.value = false;
    hashtagToDeleteId.value = null;
    hashtagToDeleteName.value = '';
};

/**
 * Отправляет запрос на удаление.
 */
const deleteHashtag = () => {
    if (hashtagToDeleteId.value === null) return;
    const idToDelete = hashtagToDeleteId.value;
    const nameToDelete = hashtagToDeleteName.value;
    router.delete(route('admin.hashtags.destroy', { hashtag: idToDelete }), {
        preserveScroll: true,
        preserveState: false,
        onSuccess: () => {
            closeModal();
            toast.success(`Тег обучения "${nameToDelete || 'ID: ' + idToDelete}" удален.`);
        },
        onError: (errors) => {
            closeModal();
            const errorMsg = errors.general || errors[Object.keys(errors)[0]]
                || 'Произошла ошибка при удалении.';
            toast.error(`${errorMsg} (Тег обучения: ${nameToDelete || 'ID: ' + idToDelete})`);
        },
        onFinish: () => {
            hashtagToDeleteId.value = null;
            hashtagToDeleteName.value = '';
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
const sortHashtags = (hashtags) => {
    if (sortParam.value === 'idAsc') return hashtags.slice().sort((a, b) => a.id - b.id);
    if (sortParam.value === 'idDesc') return hashtags.slice().sort((a, b) => b.id - a.id);
    if (sortParam.value === 'activity') return hashtags.filter(i => i.activity);
    if (sortParam.value === 'inactive') return hashtags.filter(i => !i.activity);
    // Для просмотров сортировка по убыванию:
    if (sortParam.value === 'views') {
        return hashtags.slice().sort((a, b) => b[sortParam.value] - a[sortParam.value]);
    }
    return hashtags.slice().sort((a, b) => {
        if (a[sortParam.value] < b[sortParam.value]) return -1
        if (a[sortParam.value] > b[sortParam.value]) return 1
        return 0
    });
};

/**
 * Вычисляемое свойство, отсортированный список поиска.
 */
const filteredHashtags = computed(() => {
    let filtered = props.hashtags || [];
    if (searchQuery.value) {
        filtered = filtered.filter(i => (i.name || '')
            .toLowerCase().includes(searchQuery.value.toLowerCase()));
    }
    return sortHashtags(filtered);
});

/**
 * Вычисляемое свойство пагинации, возвращающее для текущей страницы.
 */
const paginatedHashtags = computed(() => {
    const start = (currentPage.value - 1) * itemsPerPage.value;
    return filteredHashtags.value.slice(start, start + itemsPerPage.value);
});

/**
 * Вычисляемое свойство, возвращающее общее количество страниц пагинации.
 */
const totalPages = computed(() =>
    Math.ceil((filteredHashtags.value.length || 0) / itemsPerPage.value));

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
        route('admin.actions.hashtags.updateSortBulk'),
        { hashtags: sortData },
        {
            preserveScroll: true,
            preserveState: true,
            onSuccess: () => toast.success("Порядок тегов обучения успешно обновлен."),
            onError: (errors) => {
                console.error("Ошибка обновления сортировки:", errors);
                toast.error(
                    errors?.general ||
                    errors?.hashtags ||
                    "Не удалось обновить порядок тегов обучения."
                );
                router.reload({ only: ['hashtags'], preserveScroll: true });
            },
        }
    );
};

/**
 * Массив выбранных ID для массовых действий.
 */
const selectedHashtags = ref([]);

/**
 * Логика выбора всех для массовых действий.
 * Выбрать/снять всех на текущей странице
 */
const toggleAll = ({ ids, checked }) => {
    if (checked) {
        // Выбрать всех
        selectedHashtags.value = [...ids];
    } else {
        // Снять выделение
        selectedHashtags.value = [];
    }
};

/**
 * Обрабатывает событие выбора/снятия выбора одной строки.
 */
const toggleSelectLearningTag = (id) => {
    const idx = selectedHashtags.value.indexOf(id);
    if (idx > -1) selectedHashtags.value.splice(idx, 1);
    else selectedHashtags.value.push(id);
};

/**
 * Выполняет массовое включение/выключение активности выбранных.
 */
const bulkToggleActivity = (newActivity) => {
    if (!selectedHashtags.value.length) {
        toast.warning('Выберите тегов обучения для активации/деактивации');
        return;
    }
    router.put(route('admin.actions.hashtags.bulkUpdateActivity'),
        { ids: selectedHashtags.value, activity: newActivity },
        {
            preserveScroll: true,
            preserveState: true,
            onSuccess: () => {
                toast.success('Активность массово обновлена');
                const updatedIds = [...selectedHashtags.value];
                selectedHashtags.value = [];
                paginatedHashtags.value.forEach(a => {
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
 * Выполняет массовое удаление выбранных.
 */
const bulkDelete = () => {
    if (selectedHashtags.value.length === 0) {
        toast.warning('Выберите хотя бы один тег для удаления.'); // <--- Используем toast
        return;
    }
    if (!confirm(`Вы уверены, что хотите их удалить ?`)) {
        return;
    }
    router.delete(route('admin.actions.hashtags.bulkDestroy'), {
        data: {ids: selectedHashtags.value},
        preserveScroll: true,
        preserveState: false, // Перезагружаем данные страницы
        onSuccess: (page) => {
            selectedHashtags.value = []; // Очищаем выбор
            toast.success('Массовое удаление тегов успешно завершено.');
            // console.log('Массовое удаление статей успешно завершено.');
        },
        onError: (errors) => {
            console.error("Ошибка массового удаления:", errors);
            // Отображаем первую ошибку
            const errorKey = Object.keys(errors)[0];
            const errorMessage = errors[errorKey] || 'Произошла ошибка при удалении тегов.';
            toast.error(errorMessage);
        },
    });
};

/**
 * Обрабатывает выбор действия в селекте массовых действий.
 */
const handleBulkAction = (event) => {
    const action = event.target.value;
    if (action === 'selectAll') {
        selectedHashtags.value = paginatedHashtags.value.map(r => r.id);
    } else if (action === 'deselectAll') {
        selectedHashtags.value = [];
    } else if (action === 'activate') {
        bulkToggleActivity(true);
    } else if (action === 'deactivate') {
        bulkToggleActivity(false);
    } else if (action === 'delete') {
        bulkDelete();
    }
    event.target.value = '';
};

/**
 * Отправляет запрос для изменения статуса активности.
 */
const toggleActivity = (hashtag) => {
    const newActivity = !hashtag.activity;
    const actionText = newActivity ? t('activated') : t('deactivated');

    router.put(
        route('admin.actions.hashtags.updateActivity',
            { hashtag: hashtag.id }),
        { activity: newActivity },
        {
            preserveScroll: true,
            preserveState: true,
            onSuccess: () => {
                // ЛОКАЛЬНО обновляем объект, чтобы UI сразу отразил статус
                hashtag.activity = newActivity;
                toast.success(`Тег обучения "${hashtag.name}" ${actionText}.`);
            },
            onError: (errors) => {
                toast.error(
                    errors?.activity ||
                    errors?.general ||
                    `Ошибка изменения активности для "${hashtag.name}".`
                );
            },
        }
    );
};

/** ССЫЛКА ДЛЯ ТАБОВ ЛОКАЛЕЙ */
const localeLink = (locale) => route('admin.hashtags.index', { locale });
</script>

<template>
    <AdminLayout :title="t('hashtags')">
        <template #header>
            <TitlePage>
                {{ t('hashtags') }}
            </TitlePage>
        </template>

        <div class="px-2 py-2 w-full max-w-12xl mx-auto">
            <div class="p-4 bg-slate-50 dark:bg-slate-700 border border-blue-400 dark:border-blue-200
                        overflow-hidden shadow-md shadow-gray-500 dark:shadow-slate-400
                        bg-opacity-95 dark:bg-opacity-95">

                <div class="sm:flex sm:justify-between sm:items-center mb-2">
                    <!-- Кнопка добавить инструктора -->
                    <DefaultButton :href="route('admin.hashtags.create')">
                        <template #icon>
                            <svg class="w-4 h-4 fill-current opacity-50 shrink-0"
                                 viewBox="0 0 16 16">
                                <path
                                    d="M15 7H9V1c0-.6-.4-1-1-1S7 .4 7 1v6H1c-.6 0-1 .4-1 1s.4 1 1 1h6v6c0 .6.4 1 1 1s1-.4 1-1V9h6c.6 0 1-.4 1-1s-.4-1-1-1z" />
                            </svg>
                        </template>
                        {{ t('addLearningTag') }}
                    </DefaultButton>

                    <BulkActionSelect v-if="hashtagsCount" @change="handleBulkAction" />
                </div>

                <!-- Переключатель локалей, переключение режима вида, количество элементов -->
                <div class="flex items-center justify-between mt-5">

                    <div class="flex items-center justify-end space-x-2 px-3 py-1
                                border-x border-t border-gray-400 rounded-t-lg
                                bg-gray-100 dark:bg-gray-900">
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

                    <div class="flex items-center">
                        <CountTable v-if="hashtagsCount">
                            {{ hashtagsCount }}
                        </CountTable>
                        <ToggleViewButton v-model:viewMode="viewMode" />
                    </div>
                </div>

                <SearchInput v-if="hashtagsCount"
                             v-model="searchQuery" :placeholder="t('searchByName')" />

                <!-- Таблица -->
                <HashtagTable
                    v-if="viewMode === 'table'"
                    :hashtags="paginatedHashtags"
                    :selected-hashtags="selectedHashtags"
                    @toggle-activity="toggleActivity"
                    @delete="confirmDelete"
                    @update-sort-order="handleSortOrderUpdate"
                    @toggle-select="toggleSelectLearningTag"
                    @toggle-all="toggleAll"
                />

                <!-- Карточки -->
                <HashtagCardGrid
                    v-else
                    :hashtags="paginatedHashtags"
                    :selected-hashtags="selectedHashtags"
                    @toggle-activity="toggleActivity"
                    @delete="confirmDelete"
                    @toggle-select="toggleSelectLearningTag"
                    @toggle-all="toggleAll"
                    @update-sort-order="handleSortOrderUpdate"
                />

                <div class="flex justify-between items-center flex-col md:flex-row my-1"
                     v-if="hashtagsCount">
                    <ItemsPerPageSelect
                        :items-per-page="itemsPerPage"
                        @update:itemsPerPage="itemsPerPage = $event" />
                    <Pagination
                        :current-page="currentPage"
                        :items-per-page="itemsPerPage"
                        :total-items="filteredHashtags.length"
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
            :onConfirm="deleteHashtag"
            :cancelText="t('cancel')"
            :confirmText="t('yesDelete')"
        />
    </AdminLayout>
</template>
