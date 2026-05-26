<script setup>
/**
 * @version PulsarCMS 1.0
 * @author Александр Косолапов <kosolapov1976@gmail.com>
 */
import {defineProps, ref, computed, watch} from 'vue';
import {useToast} from 'vue-toastification';
import { useI18n } from 'vue-i18n';
import {router} from "@inertiajs/vue3";

import AdminLayout from '@/Layouts/AdminLayout.vue';
import TitlePage from '@/Components/Admin/UI/Headlines/TitlePage.vue';
import DangerModal from '@/Components/Admin/UI/Modal/DangerModal.vue';
import Pagination from '@/Components/Admin/UI/Pagination/Pagination.vue';
import SearchInput from '@/Components/Admin/UI/Search/SearchInput.vue';
import CountTable from '@/Components/Admin/UI/Count/CountTable.vue';
import DefaultButton from "@/Components/Admin/UI/Buttons/DefaultButton.vue";
import ItemsPerPageSelect from "@/Components/Admin/UI/Select/ItemsPerPageSelect.vue";
import ToggleViewButton from '@/Components/Admin/UI/Buttons/ToggleViewButton.vue';

import SortSelect from "@/Components/Admin/System/Role/Sort/SortSelect.vue";
import RoleTable from '@/Components/Admin/System/Role/Table/RoleTable.vue';
import RoleCardGrid from '@/Components/Admin/System/Role/View/RoleCardGrid.vue';

// --- Инициализация экземпляр i18n, toast ---
const {t} = useI18n();
const toast = useToast();

/**
 * Входные свойства компонента.
 */
const props = defineProps({
    roles: {
        type: Array,
        default: () => [],
    },

    rolesCount: {
        type: Number,
        default: 0,
    },

    adminCountRoles: {
        type: Number,
        default: 20,
    },

    adminSortRoles: {
        type: String,
        default: 'idAsc',
    },
})

/**
 * Вид: таблица или карточки.
 * table | cards
 */
const viewMode = ref(localStorage.getItem('admin_view_mode') || 'table')

watch(viewMode, (val) => {
    localStorage.setItem('admin_view_mode', val)
})

/**
 * Реактивная переменная для хранения текущего количества элементов на странице.
 */
const itemsPerPage = ref(props.adminCountRoles); // Используем значение из props

/**
 * Наблюдатель за изменением количества элементов на странице.
 */
watch(itemsPerPage, (newVal) => {
    router.put(route('admin.settings.updateAdminCountRoles'), {value: newVal}, {
        preserveScroll: true,
        preserveState: true, // Не перезагружаем все props
        onSuccess: () => toast.info(`Показ ${newVal} элементов на странице.`),
        onError: (errors) => toast.error(errors.value || 'Ошибка обновления кол-ва элементов.'),
    });
});

/**
 * Реактивная переменная для хранения текущего параметра сортировки.
 */
const sortParam = ref(props.adminSortRoles); // Используем значение из props

/**
 * Наблюдатель за изменением параметра сортировки.
 */
watch(sortParam, (newVal) => {
    router.put(route('admin.settings.updateAdminSortRoles'), {value: newVal}, {
        preserveScroll: true,
        preserveState: true,
        // onSuccess: () => toast.info(`Сортировка изменена на ${newVal}.`), // TODO: добавить перевод для newVal
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
const roleToDeleteId = ref(null);

/**
 * Название для отображения в модальном окне.
 */
const roleToDeleteName = ref('');

/**
 * Открывает модальное окно подтверждения удаления с входными переменными.
 */
const confirmDelete = (id, name) => {
    roleToDeleteId.value = id;
    roleToDeleteName.value = name;
    showConfirmDeleteModal.value = true;
};
/**
 * Закрывает модальное окно подтверждения и сбрасывает связанные переменные.
 */
const closeModal = () => {
    showConfirmDeleteModal.value = false;
    roleToDeleteId.value = null;
    roleToDeleteName.value = '';
};

/**
 * Отправляет запрос на удаление.
 */
const deleteRole = () => {
    if (roleToDeleteId.value === null) return;

    const idToDelete = roleToDeleteId.value; // Сохраняем ID во временную переменную
    const nameToDelete = roleToDeleteName.value; // Сохраняем name во временную переменную

    router.delete(route('admin.roles.destroy', {role: idToDelete}), { // Используем временную переменную
        preserveScroll: true,
        preserveState: false,
        onSuccess: () => {
            closeModal(); // Закрываем модалку
            toast.success(`Роль "${nameToDelete || 'ID: ' + idToDelete}" удалена.`);
            // console.log('Удаление успешно.');
        },
        onError: (errors) => {
            closeModal();
            const errorMsg = errors.general || errors[Object.keys(errors)[0]] || 'Произошла ошибка при удалении.';
            toast.error(`${errorMsg} (Роль: ${nameToDelete || 'ID: ' + idToDelete})`);
            console.error('Ошибка удаления:', errors);
        },
        onFinish: () => {
            // console.log('Запрос на удаление завершен.');
            roleToDeleteId.value = null;
            roleToDeleteName.value = '';
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
const sortRoles = (roles) => {
    // Добавляем сортировку по id в двух направлениях:
    if (sortParam.value === 'idAsc') {
        return roles.slice().sort((a, b) => a.id - b.id);
    }
    if (sortParam.value === 'idDesc') {
        return roles.slice().sort((a, b) => b.id - a.id);
    }
    // Для остальных полей — стандартное сравнение:
    return roles.slice().sort((a, b) => {
        if (a[sortParam.value] < b[sortParam.value]) return -1
        if (a[sortParam.value] > b[sortParam.value]) return 1
        return 0
    });
};

/**
 * Вычисляемое свойство, отсортированный список поиска.
 */
const filteredRoles = computed(() => {
    let filtered = props.roles;

    if (searchQuery.value) {
        filtered = filtered.filter(role =>
            role.name.toLowerCase().includes(searchQuery.value.toLowerCase())
        );
    }

    return sortRoles(filtered);
});

/**
 * Вычисляемое свойство пагинации, возвращающее для текущей страницы.
 */
const paginatedRoles = computed(() => {
    const start = (currentPage.value - 1) * itemsPerPage.value;
    return filteredRoles.value.slice(start, start + itemsPerPage.value);
});

</script>

<template>
    <AdminLayout :title="t('roles')">
        <template #header>
            <TitlePage>
                {{ t('roles') }}
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
                    <!-- Кнопка добавить -->
                    <DefaultButton :href="route('admin.roles.create')">
                        <template #icon>
                            <svg class="w-4 h-4 fill-current opacity-50 shrink-0" viewBox="0 0 16 16">
                                <path
                                    d="M15 7H9V1c0-.6-.4-1-1-1S7 .4 7 1v6H1c-.6 0-1 .4-1 1s.4 1 1 1h6v6c0 .6.4 1 1 1s1-.4 1-1V9h6c.6 0 1-.4 1-1s-.4-1-1-1z"></path>
                            </svg>
                        </template>
                        {{ t('addRole') }}
                    </DefaultButton>
                </div>
                <SearchInput
                    v-if="rolesCount"
                    v-model="searchQuery"
                    :placeholder="t('searchByName')"
                />

                <div
                    v-if="rolesCount"
                    class="flex justify-between items-center flex-col md:flex-row my-3"
                >

                    <ItemsPerPageSelect
                        :items-per-page="itemsPerPage"
                        @update:itemsPerPage="itemsPerPage = $event" />

                    <SortSelect :sortParam="sortParam" @update:sortParam="val => sortParam = val" />
                </div>

                <!-- Количество + переключатель вида -->
                <div
                    v-if="rolesCount"
                    class="flex justify-between items-center flex-col md:flex-row my-3"
                >
                    <CountTable>
                        {{ rolesCount }}
                    </CountTable>

                    <ToggleViewButton v-model:viewMode="viewMode" />
                </div>

                <div class="flex justify-center items-center flex-col md:flex-row"
                     v-if="rolesCount">
                    <Pagination
                        :current-page="currentPage"
                        :items-per-page="itemsPerPage"
                        :total-items="filteredRoles.length"
                        @update:currentPage="currentPage = $event"
                        @update:itemsPerPage="itemsPerPage = $event" />
                </div>

                <!-- Таблица -->
                <RoleTable
                    v-if="viewMode === 'table'"
                    :roles="paginatedRoles"
                    @delete="confirmDelete"
                />

                <!-- Карточки -->
                <RoleCardGrid
                    v-else
                    :roles="paginatedRoles"
                    @delete="confirmDelete"
                />

                <div class="flex justify-center items-center flex-col md:flex-row mt-3"
                     v-if="rolesCount">
                    <Pagination
                        :current-page="currentPage"
                        :items-per-page="itemsPerPage"
                        :total-items="filteredRoles.length"
                        @update:currentPage="currentPage = $event"
                        @update:itemsPerPage="itemsPerPage = $event" />
                </div>
            </div>
        </div>

        <DangerModal
            :show="showConfirmDeleteModal"
            @close="closeModal"
            :onCancel="closeModal"
            :onConfirm="deleteRole"
            :cancelText="t('cancel')"
            :confirmText="t('yesDelete')"
        />
    </AdminLayout>
</template>
