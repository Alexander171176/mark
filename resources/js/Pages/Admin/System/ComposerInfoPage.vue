<script setup>
import { computed, ref } from 'vue'

import AdminLayout from '@/Layouts/AdminLayout.vue'
import TitlePage from '@/Components/Admin/UI/Headlines/TitlePage.vue'

const props = defineProps({
    composer: {
        type: Object,
        default: () => ({}),
    },
})

const search = ref('')
const copiedValue = ref('')

// Основные поля composer.json
const mainInfoKeys = [
    'name',
    'description',
    'type',
    'license',
    'homepage',
    'minimum-stability',
    'prefer-stable',
]

// Секции, которые выводятся отдельно
const reservedSections = [
    ...mainInfoKeys,
    'require',
    'require-dev',
]

// Основная информация о проекте
const mainInfo = computed(() => {
    return mainInfoKeys
        .filter((key) => props.composer[key] !== undefined)
        .map((key) => ({
            key,
            value: props.composer[key],
        }))
})

// Production-зависимости
const requirePackages = computed(() => {
    return filterPackages(
        props.composer.require || {}
    )
})

// Development-зависимости
const requireDevPackages = computed(() => {
    return filterPackages(
        props.composer['require-dev'] || {}
    )
})

// Остальные секции composer.json
const otherSections = computed(() => {
    return Object.entries(props.composer)
        .filter(([key]) => !reservedSections.includes(key))
        .filter(([key, value]) => {
            const query = normalizedSearch.value

            if (!query) {
                return true
            }

            return (
                String(key).toLowerCase().includes(query) ||
                JSON.stringify(value)
                    .toLowerCase()
                    .includes(query)
            )
        })
        .map(([key, value]) => ({
            key,
            value,
        }))
})

const normalizedSearch = computed(() => {
    return search.value
        .trim()
        .toLowerCase()
})

const totalDependencies = computed(() => {
    return Object.keys(
        props.composer.require || {}
    ).length
})

const totalDevDependencies = computed(() => {
    return Object.keys(
        props.composer['require-dev'] || {}
    ).length
})

// Фильтрация пакетов
function filterPackages(packages) {
    const query = normalizedSearch.value

    return Object.entries(packages)
        .filter(([name, version]) => {
            if (!query) {
                return true
            }

            return (
                name.toLowerCase().includes(query) ||
                String(version).toLowerCase().includes(query)
            )
        })
        .map(([name, version]) => ({
            name,
            version,
        }))
}

// Проверка типа значения
const isObject = (value) => {
    return (
        value !== null &&
        typeof value === 'object'
    )
}

// Красивый JSON
const stringifyValue = (value) => {
    return JSON.stringify(
        value,
        null,
        2
    )
}

// Копирование значения
const copyToClipboard = async (value, key) => {
    try {
        const text = isObject(value)
            ? stringifyValue(value)
            : String(value ?? '')

        await navigator.clipboard.writeText(text)

        copiedValue.value = key

        setTimeout(() => {
            if (copiedValue.value === key) {
                copiedValue.value = ''
            }
        }, 1500)
    } catch (error) {
        console.error(
            'Failed to copy composer value:',
            error
        )
    }
}
</script>

<template>
    <AdminLayout title="composer.json">
        <template #header>
            <TitlePage>
                composer.json
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
                           border border-sky-300 dark:border-sky-700
                           bg-sky-50 dark:bg-sky-900/20
                           rounded-md"
                >
                    <div class="flex items-start gap-3">
                        <div
                            class="shrink-0 flex items-center justify-center
                                   w-8 h-8 rounded-full
                                   bg-sky-100 dark:bg-sky-800
                                   text-sky-700 dark:text-sky-200"
                        >
                            <svg
                                class="w-5 h-5 fill-current"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    d="M4 3h16c1.1 0 2 .9 2 2v14c0 1.1-.9 2-2
                                       2H4c-1.1 0-2-.9-2-2V5c0-1.1.9-2 2-2zm0
                                       2v14h16V5H4zm3 3h10v2H7V8zm0 4h10v2H7v-2zm0
                                       4h6v2H7v-2z"
                                />
                            </svg>
                        </div>

                        <div>
                            <div
                                class="text-sm font-semibold
                                       text-sky-800 dark:text-sky-200"
                            >
                                Конфигурация Composer
                            </div>

                            <div
                                class="mt-0.5 text-xs
                                       text-sky-700 dark:text-sky-300"
                            >
                                Информация загружена непосредственно из
                                <span class="font-mono font-semibold">
                                    composer.json
                                </span>.
                                Страница предназначена только для просмотра.
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Краткая статистика -->
                <div
                    class="grid grid-cols-1 sm:grid-cols-3
                           gap-3 mb-4"
                >
                    <div
                        class="p-3 border border-slate-300
                               dark:border-slate-600
                               rounded-md
                               bg-white dark:bg-slate-900"
                    >
                        <div
                            class="text-xs uppercase tracking-wide
                                   text-slate-500 dark:text-slate-400"
                        >
                            Package
                        </div>

                        <div
                            class="mt-1 font-mono text-sm font-semibold
                                   text-indigo-700 dark:text-indigo-300
                                   break-all"
                        >
                            {{ composer.name || '—' }}
                        </div>
                    </div>

                    <div
                        class="p-3 border border-slate-300
                               dark:border-slate-600
                               rounded-md
                               bg-white dark:bg-slate-900"
                    >
                        <div
                            class="text-xs uppercase tracking-wide
                                   text-slate-500 dark:text-slate-400"
                        >
                            Require
                        </div>

                        <div
                            class="mt-1 text-xl font-semibold
                                   text-emerald-700 dark:text-emerald-300"
                        >
                            {{ totalDependencies }}
                        </div>
                    </div>

                    <div
                        class="p-3 border border-slate-300
                               dark:border-slate-600
                               rounded-md
                               bg-white dark:bg-slate-900"
                    >
                        <div
                            class="text-xs uppercase tracking-wide
                                   text-slate-500 dark:text-slate-400"
                        >
                            Require Dev
                        </div>

                        <div
                            class="mt-1 text-xl font-semibold
                                   text-amber-700 dark:text-amber-300"
                        >
                            {{ totalDevDependencies }}
                        </div>
                    </div>
                </div>

                <!-- Поиск -->
                <div
                    class="flex flex-col sm:flex-row
                           sm:items-center sm:justify-between
                           gap-3 mb-4"
                >
                    <div
                        class="text-sm
                               text-slate-600 dark:text-slate-300"
                    >
                        Поиск по пакетам, версиям и секциям
                    </div>

                    <div class="relative w-full sm:w-96">
                        <svg
                            class="absolute left-2.5 top-1/2
                                   -translate-y-1/2
                                   w-4 h-4 fill-current
                                   text-slate-400"
                            viewBox="0 0 24 24"
                        >
                            <path
                                d="M9.5 3a6.5 6.5 0 1 0 3.98 11.64L19.85
                                   21 21 19.85l-6.36-6.37A6.5 6.5 0 0 0
                                   9.5 3zm0 2a4.5 4.5 0 1 1 0 9 4.5 4.5
                                   0 0 1 0-9z"
                            />
                        </svg>

                        <input
                            v-model="search"
                            type="text"
                            placeholder="laravel, inertia, scripts..."
                            class="w-full pl-8 pr-8 py-1.5
                                   text-sm font-mono
                                   border border-slate-300
                                   dark:border-slate-500
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

                <!-- Основная информация -->
                <div
                    v-if="mainInfo.length"
                    class="mb-4"
                >
                    <div
                        class="mb-2 text-sm font-semibold
                               text-indigo-700 dark:text-indigo-300"
                    >
                        Основная информация
                    </div>

                    <div
                        class="overflow-hidden
                               border border-slate-300
                               dark:border-slate-600
                               rounded-md
                               bg-white dark:bg-gray-900"
                    >
                        <table
                            class="w-full text-left
                                   border-collapse text-sm"
                        >
                            <tbody>
                            <tr
                                v-for="entry in mainInfo"
                                :key="entry.key"
                                class="border-b border-slate-200
                                           dark:border-slate-700
                                           last:border-b-0"
                            >
                                <td
                                    class="w-1/3 px-3 py-2
                                               font-mono font-semibold
                                               text-sky-700
                                               dark:text-sky-300
                                               align-top"
                                >
                                    {{ entry.key }}
                                </td>

                                <td
                                    class="px-3 py-2
                                               font-mono
                                               text-slate-800
                                               dark:text-slate-100
                                               break-all"
                                >
                                    {{ entry.value }}
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Require -->
                <div class="mb-4">
                    <div
                        class="flex items-center justify-between
                               mb-2"
                    >
                        <div
                            class="text-sm font-semibold
                                   text-emerald-700
                                   dark:text-emerald-300"
                        >
                            require
                        </div>

                        <div
                            class="text-xs
                                   text-slate-500 dark:text-slate-400"
                        >
                            {{ requirePackages.length }}
                        </div>
                    </div>

                    <div
                        class="overflow-x-auto
                               border border-slate-300
                               dark:border-slate-600
                               rounded-md
                               bg-white dark:bg-gray-900"
                    >
                        <table
                            class="w-full text-left
                                   border-collapse text-sm"
                        >
                            <thead
                                class="bg-slate-100 dark:bg-slate-800"
                            >
                            <tr>
                                <th
                                    class="px-3 py-2
                                               border-b border-slate-300
                                               dark:border-slate-600
                                               text-xs font-semibold
                                               text-slate-600
                                               dark:text-slate-300"
                                >
                                    Package
                                </th>

                                <th
                                    class="w-52 px-3 py-2
                                               border-b border-slate-300
                                               dark:border-slate-600
                                               text-xs font-semibold
                                               text-slate-600
                                               dark:text-slate-300"
                                >
                                    Version
                                </th>
                            </tr>
                            </thead>

                            <tbody>
                            <tr
                                v-for="item in requirePackages"
                                :key="item.name"
                                class="border-b border-slate-200
                                           dark:border-slate-700
                                           last:border-b-0
                                           hover:bg-emerald-50
                                           dark:hover:bg-slate-800/80"
                            >
                                <td
                                    class="px-3 py-1.5
                                               font-mono font-semibold
                                               text-slate-800
                                               dark:text-slate-100"
                                >
                                    {{ item.name }}
                                </td>

                                <td
                                    class="px-3 py-1.5
                                               font-mono
                                               text-emerald-700
                                               dark:text-emerald-300"
                                >
                                    {{ item.version }}
                                </td>
                            </tr>

                            <tr v-if="!requirePackages.length">
                                <td
                                    colspan="2"
                                    class="px-4 py-6
                                               text-center text-sm
                                               text-slate-500
                                               dark:text-slate-400"
                                >
                                    Зависимости не найдены
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Require Dev -->
                <div class="mb-4">
                    <div
                        class="flex items-center justify-between
                               mb-2"
                    >
                        <div
                            class="text-sm font-semibold
                                   text-amber-700
                                   dark:text-amber-300"
                        >
                            require-dev
                        </div>

                        <div
                            class="text-xs
                                   text-slate-500 dark:text-slate-400"
                        >
                            {{ requireDevPackages.length }}
                        </div>
                    </div>

                    <div
                        class="overflow-x-auto
                               border border-slate-300
                               dark:border-slate-600
                               rounded-md
                               bg-white dark:bg-gray-900"
                    >
                        <table
                            class="w-full text-left
                                   border-collapse text-sm"
                        >
                            <thead
                                class="bg-slate-100 dark:bg-slate-800"
                            >
                            <tr>
                                <th
                                    class="px-3 py-2
                                               border-b border-slate-300
                                               dark:border-slate-600
                                               text-xs font-semibold
                                               text-slate-600
                                               dark:text-slate-300"
                                >
                                    Package
                                </th>

                                <th
                                    class="w-52 px-3 py-2
                                               border-b border-slate-300
                                               dark:border-slate-600
                                               text-xs font-semibold
                                               text-slate-600
                                               dark:text-slate-300"
                                >
                                    Version
                                </th>
                            </tr>
                            </thead>

                            <tbody>
                            <tr
                                v-for="item in requireDevPackages"
                                :key="item.name"
                                class="border-b border-slate-200
                                           dark:border-slate-700
                                           last:border-b-0
                                           hover:bg-amber-50
                                           dark:hover:bg-slate-800/80"
                            >
                                <td
                                    class="px-3 py-1.5
                                               font-mono font-semibold
                                               text-slate-800
                                               dark:text-slate-100"
                                >
                                    {{ item.name }}
                                </td>

                                <td
                                    class="px-3 py-1.5
                                               font-mono
                                               text-amber-700
                                               dark:text-amber-300"
                                >
                                    {{ item.version }}
                                </td>
                            </tr>

                            <tr v-if="!requireDevPackages.length">
                                <td
                                    colspan="2"
                                    class="px-4 py-6
                                               text-center text-sm
                                               text-slate-500
                                               dark:text-slate-400"
                                >
                                    Dev-зависимости не найдены
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Остальные секции -->
                <div
                    v-if="otherSections.length"
                    class="space-y-3"
                >
                    <div
                        v-for="section in otherSections"
                        :key="section.key"
                        class="border border-slate-300
                               dark:border-slate-600
                               rounded-md
                               overflow-hidden
                               bg-white dark:bg-gray-900"
                    >
                        <div
                            class="flex items-center justify-between
                                   px-3 py-2
                                   bg-slate-100 dark:bg-slate-800
                                   border-b border-slate-300
                                   dark:border-slate-600"
                        >
                            <div
                                class="font-mono text-sm font-semibold
                                       text-indigo-700
                                       dark:text-indigo-300"
                            >
                                {{ section.key }}
                            </div>

                            <button
                                type="button"
                                :title="
                                    copiedValue === section.key
                                        ? 'Скопировано'
                                        : 'Копировать'
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
                                @click="
                                    copyToClipboard(
                                        section.value,
                                        section.key
                                    )
                                "
                            >
                                <svg
                                    v-if="copiedValue !== section.key"
                                    class="w-4 h-4 fill-current"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        d="M16 1H4c-1.1 0-2 .9-2
                                           2v14h2V3h12V1zm3 4H8c-1.1
                                           0-2 .9-2 2v14c0 1.1.9 2
                                           2 2h11c1.1 0 2-.9
                                           2-2V7c0-1.1-.9-2-2-2zm0
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
                                        d="M9 16.17 4.83 12l-1.42
                                           1.41L9 19 21 7l-1.41-1.41z"
                                    />
                                </svg>
                            </button>
                        </div>

                        <div class="p-3">
                            <pre
                                v-if="isObject(section.value)"
                                class="m-0 whitespace-pre-wrap
                                       break-words
                                       font-mono text-xs
                                       leading-5
                                       text-slate-800
                                       dark:text-slate-100"
                            >{{ stringifyValue(section.value) }}</pre>

                            <div
                                v-else
                                class="font-mono text-sm
                                       text-slate-800
                                       dark:text-slate-100
                                       break-all"
                            >
                                {{ section.value }}
                            </div>
                        </div>
                    </div>
                </div>

                <div
                    v-if="
                        search &&
                        !requirePackages.length &&
                        !requireDevPackages.length &&
                        !otherSections.length
                    "
                    class="py-8 text-center
                           text-sm
                           text-slate-500 dark:text-slate-400"
                >
                    Ничего не найдено
                </div>

                <!-- Подвал -->
                <div
                    class="mt-3 text-xs
                           text-slate-500 dark:text-slate-400"
                >
                    Конфигурация Composer доступна только для просмотра.
                    Изменение composer.json через административную панель
                    отключено.
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
