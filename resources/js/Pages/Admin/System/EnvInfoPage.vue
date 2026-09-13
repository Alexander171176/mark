<script setup>
import { computed, ref } from 'vue'

import AdminLayout from '@/Layouts/AdminLayout.vue'
import TitlePage from '@/Components/Admin/UI/Headlines/TitlePage.vue'

const props = defineProps({
    env: {
        type: Array,
        default: () => [],
    },
})

// Поиск по ключам и значениям
const search = ref('')

// Ключ последнего скопированного параметра
const copiedKey = ref('')

// Отфильтрованный список параметров
const filteredEnv = computed(() => {
    const query = search.value
        .trim()
        .toLowerCase()

    if (!query) {
        return props.env
    }

    return props.env.filter((entry) => {
        const key = String(
            entry?.key ?? ''
        ).toLowerCase()

        const value = String(
            entry?.value ?? ''
        ).toLowerCase()

        return (
            key.includes(query) ||
            value.includes(query)
        )
    })
})

// Копирование значения параметра
const copyValue = async (entry) => {
    try {
        await navigator.clipboard.writeText(
            String(entry?.value ?? '')
        )

        copiedKey.value = entry.key

        setTimeout(() => {
            if (copiedKey.value === entry.key) {
                copiedKey.value = ''
            }
        }, 1500)
    } catch (error) {
        console.error(
            'Failed to copy .env value:',
            error
        )
    }
}
</script>

<template>
    <AdminLayout title="Configuration .env">
        <template #header>
            <TitlePage>
                Configuration .env
            </TitlePage>
        </template>

        <div class="px-2 py-2 w-full max-w-12xl mx-auto">
            <div
                class="p-4 bg-slate-50 dark:bg-slate-700
                       border border-blue-400 dark:border-blue-200
                       overflow-hidden shadow-md shadow-gray-500
                       dark:shadow-slate-400
                       bg-opacity-95 dark:bg-opacity-95"
            >
                <!-- Информация -->
                <div
                    class="mb-4 px-4 py-3
                           border border-amber-300 dark:border-amber-600
                           bg-amber-50 dark:bg-amber-900/20
                           rounded-md"
                >
                    <div class="flex items-start gap-3">
                        <div
                            class="shrink-0 flex items-center justify-center
                                   w-8 h-8 rounded-full
                                   bg-amber-100 dark:bg-amber-800
                                   text-amber-700 dark:text-amber-200"
                        >
                            <svg
                                class="w-5 h-5 fill-current"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    d="M11 17h2v-6h-2v6zm0-8h2V7h-2v2zm1-7C6.48 2 2 6.48 2 12s4.48 10 10 10
                                       10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8
                                       8-8 8 3.59 8 8-3.59 8-8 8z"
                                />
                            </svg>
                        </div>

                        <div>
                            <div
                                class="text-sm font-semibold
                                       text-amber-800 dark:text-amber-200"
                            >
                                Конфигурация окружения
                            </div>

                            <div
                                class="mt-0.5 text-xs
                                       text-amber-700 dark:text-amber-300"
                            >
                                Данные загружены непосредственно из файла
                                <span class="font-mono font-semibold">.env</span>.
                                Страница предназначена только для просмотра
                                конфигурации сервера.
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Панель -->
                <div
                    class="flex flex-col sm:flex-row
                           sm:items-center sm:justify-between
                           gap-3 mb-3"
                >
                    <div
                        class="text-sm text-slate-600 dark:text-slate-300"
                    >
                        Параметров:
                        <span
                            class="font-semibold
                                   text-indigo-700 dark:text-indigo-300"
                        >
                            {{ env.length }}
                        </span>

                        <template v-if="search">
                            <span class="mx-1">·</span>

                            Найдено:
                            <span
                                class="font-semibold
                                       text-sky-700 dark:text-sky-300"
                            >
                                {{ filteredEnv.length }}
                            </span>
                        </template>
                    </div>

                    <div class="relative w-full sm:w-80">
                        <svg
                            class="absolute left-2.5 top-1/2
                                   -translate-y-1/2
                                   w-4 h-4
                                   fill-current
                                   text-slate-400"
                            viewBox="0 0 24 24"
                        >
                            <path
                                d="M9.5 3a6.5 6.5 0 1 0 3.98 11.64L19.85 21
                                   21 19.85l-6.36-6.37A6.5 6.5 0 0 0 9.5 3zm0
                                   2a4.5 4.5 0 1 1 0 9 4.5 4.5 0 0 1 0-9z"
                            />
                        </svg>

                        <input
                            v-model="search"
                            type="text"
                            placeholder="Поиск по .env..."
                            class="w-full pl-8 pr-8 py-1.5
                                   text-sm font-mono
                                   border border-slate-300 dark:border-slate-500
                                   rounded-md
                                   bg-white dark:bg-slate-800
                                   text-slate-800 dark:text-slate-100
                                   placeholder:text-slate-400
                                   focus:outline-none
                                   focus:ring-1 focus:ring-sky-500
                                   focus:border-sky-500"
                        >

                        <button
                            v-if="search"
                            type="button"
                            title="Очистить поиск"
                            class="absolute right-2 top-1/2
                                   -translate-y-1/2
                                   text-slate-400
                                   hover:text-red-500 transition"
                            @click="search = ''"
                        >
                            ×
                        </button>
                    </div>
                </div>

                <!-- Таблица -->
                <div
                    class="overflow-x-auto
                           border border-slate-300 dark:border-slate-600
                           rounded-md shadow
                           bg-white dark:bg-gray-900"
                >
                    <table
                        class="w-full text-left border-collapse text-sm"
                    >
                        <thead
                            class="bg-slate-100 dark:bg-slate-800"
                        >
                        <tr>
                            <th
                                class="w-12 px-3 py-2
                                           border-b border-slate-300
                                           dark:border-slate-600
                                           text-center
                                           text-xs font-semibold
                                           text-slate-500 dark:text-slate-400"
                            >
                                #
                            </th>

                            <th
                                class="w-1/3 px-3 py-2
                                           border-b border-slate-300
                                           dark:border-slate-600
                                           font-semibold
                                           text-indigo-700
                                           dark:text-indigo-300"
                            >
                                Key
                            </th>

                            <th
                                class="px-3 py-2
                                           border-b border-slate-300
                                           dark:border-slate-600
                                           font-semibold
                                           text-indigo-700
                                           dark:text-indigo-300"
                            >
                                Value
                            </th>

                            <th
                                class="w-12 px-2 py-2
                                           border-b border-slate-300
                                           dark:border-slate-600"
                            />
                        </tr>
                        </thead>

                        <tbody>
                        <tr
                            v-for="(entry, index) in filteredEnv"
                            :key="entry.key"
                            class="border-b border-slate-200
                                       dark:border-slate-700
                                       last:border-b-0
                                       hover:bg-sky-50
                                       dark:hover:bg-slate-800/80
                                       transition-colors"
                        >
                            <td
                                class="px-3 py-1.5
                                           text-center align-top
                                           text-xs text-slate-400
                                           dark:text-slate-500"
                            >
                                {{ index + 1 }}
                            </td>

                            <td
                                class="px-3 py-1.5 pr-5
                                           align-top font-mono
                                           font-semibold
                                           text-sky-700
                                           dark:text-sky-300
                                           whitespace-nowrap"
                            >
                                {{ entry.key }}
                            </td>

                            <td
                                class="px-3 py-1.5
                                           align-top font-mono
                                           break-all
                                           text-slate-800
                                           dark:text-slate-100"
                            >
                                    <span
                                        v-if="entry.value !== ''"
                                    >
                                        {{ entry.value }}
                                    </span>

                                <span
                                    v-else
                                    class="italic
                                               text-slate-400
                                               dark:text-slate-500"
                                >
                                        empty
                                    </span>
                            </td>

                            <td
                                class="px-2 py-1
                                           align-middle text-center"
                            >
                                <button
                                    type="button"
                                    :title="
                                            copiedKey === entry.key
                                                ? 'Скопировано'
                                                : 'Копировать значение'
                                        "
                                    class="w-7 h-7
                                               inline-flex items-center
                                               justify-center
                                               rounded
                                               text-slate-400
                                               hover:text-sky-600
                                               hover:bg-sky-100
                                               dark:hover:text-sky-300
                                               dark:hover:bg-slate-700
                                               transition"
                                    @click="copyValue(entry)"
                                >
                                    <svg
                                        v-if="copiedKey !== entry.key"
                                        class="w-4 h-4 fill-current"
                                        viewBox="0 0 24 24"
                                    >
                                        <path
                                            d="M16 1H4c-1.1 0-2 .9-2 2v14h2V3h12V1zm3
                                                   4H8c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2
                                                   2h11c1.1 0 2-.9 2-2V7c0-1.1-.9-2-2-2zm0
                                                   16H8V7h11v14z"
                                        />
                                    </svg>

                                    <svg
                                        v-else
                                        class="w-4 h-4 fill-current
                                                   text-green-600
                                                   dark:text-green-400"
                                        viewBox="0 0 24 24"
                                    >
                                        <path
                                            d="M9 16.17 4.83 12l-1.42 1.41L9
                                                   19 21 7l-1.41-1.41z"
                                        />
                                    </svg>
                                </button>
                            </td>
                        </tr>

                        <tr v-if="!filteredEnv.length">
                            <td
                                colspan="4"
                                class="px-4 py-8
                                           text-center
                                           text-sm
                                           text-slate-500
                                           dark:text-slate-400"
                            >
                                Параметры не найдены
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Подвал -->
                <div
                    class="mt-2 text-xs
                           text-slate-500 dark:text-slate-400"
                >
                    Файл доступен только для просмотра.
                    Изменение конфигурации через административную
                    панель отключено.
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
