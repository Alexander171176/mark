<script setup>
import { computed, ref } from 'vue'

import AdminLayout from '@/Layouts/AdminLayout.vue'
import TitlePage from '@/Components/Admin/UI/Headlines/TitlePage.vue'

const props = defineProps({
    packageData: {
        type: Object,
        default: () => ({}),
    },
})

const search = ref('')
const copiedValue = ref('')

// Основные поля package.json
const mainInfoKeys = [
    'name',
    'version',
    'description',
    'private',
    'type',
    'main',
    'module',
    'license',
    'author',
    'homepage',
]

// Секции, которые выводятся отдельно
const reservedSections = [
    ...mainInfoKeys,
    'scripts',
    'dependencies',
    'devDependencies',
    'peerDependencies',
    'optionalDependencies',
]

const normalizedSearch = computed(() => {
    return search.value
        .trim()
        .toLowerCase()
})

// Основная информация
const mainInfo = computed(() => {
    return mainInfoKeys
        .filter((key) => props.packageData[key] !== undefined)
        .map((key) => ({
            key,
            value: props.packageData[key],
        }))
})

// Scripts
const scripts = computed(() => {
    return filterEntries(
        props.packageData.scripts || {}
    )
})

// Dependencies
const dependencies = computed(() => {
    return filterEntries(
        props.packageData.dependencies || {}
    )
})

// Dev dependencies
const devDependencies = computed(() => {
    return filterEntries(
        props.packageData.devDependencies || {}
    )
})

// Peer dependencies
const peerDependencies = computed(() => {
    return filterEntries(
        props.packageData.peerDependencies || {}
    )
})

// Optional dependencies
const optionalDependencies = computed(() => {
    return filterEntries(
        props.packageData.optionalDependencies || {}
    )
})

// Остальные секции
const otherSections = computed(() => {
    return Object.entries(props.packageData)
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

// Количество обычных зависимостей
const totalDependencies = computed(() => {
    return Object.keys(
        props.packageData.dependencies || {}
    ).length
})

// Количество dev-зависимостей
const totalDevDependencies = computed(() => {
    return Object.keys(
        props.packageData.devDependencies || {}
    ).length
})

// Количество peer-зависимостей
const totalPeerDependencies = computed(() => {
    return Object.keys(
        props.packageData.peerDependencies || {}
    ).length
})

// Количество optional-зависимостей
const totalOptionalDependencies = computed(() => {
    return Object.keys(
        props.packageData.optionalDependencies || {}
    ).length
})

// Общее количество пакетов
const totalPackages = computed(() => {
    return (
        totalDependencies.value +
        totalDevDependencies.value +
        totalPeerDependencies.value +
        totalOptionalDependencies.value
    )
})

// Количество scripts
const totalScripts = computed(() => {
    return Object.keys(
        props.packageData.scripts || {}
    ).length
})

function filterEntries(entries) {
    const query = normalizedSearch.value

    return Object.entries(entries)
        .filter(([name, value]) => {
            if (!query) {
                return true
            }

            return (
                String(name)
                    .toLowerCase()
                    .includes(query) ||
                String(value)
                    .toLowerCase()
                    .includes(query)
            )
        })
        .map(([name, value]) => ({
            name,
            value,
        }))
}

const isObject = (value) => {
    return (
        value !== null &&
        typeof value === 'object'
    )
}

const stringifyValue = (value) => {
    return JSON.stringify(
        value,
        null,
        2
    )
}

const displayValue = (value) => {
    if (typeof value === 'boolean') {
        return value ? 'true' : 'false'
    }

    if (value === null) {
        return 'null'
    }

    return value
}

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
            'Failed to copy package.json value:',
            error
        )
    }
}
</script>

<template>
    <AdminLayout title="package.json">
        <template #header>
            <TitlePage>
                package.json
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
                           border border-cyan-300
                           dark:border-cyan-700
                           bg-cyan-50
                           dark:bg-cyan-900/20
                           rounded-md"
                >
                    <div class="flex items-start gap-3">
                        <div
                            class="shrink-0 flex items-center justify-center
                                   w-8 h-8 rounded-full
                                   bg-cyan-100 dark:bg-cyan-800
                                   text-cyan-700 dark:text-cyan-200"
                        >
                            <svg
                                class="w-5 h-5 fill-current"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    d="M12 2 2 7v10l10 5 10-5V7L12 2zm0 2.18
                                       7.74 3.87L12 11.92 4.26 8.05 12 4.18zM4
                                       9.62l7 3.5v6.7l-7-3.5v-6.7zm9 10.2v-6.7
                                       l7-3.5v6.7l-7 3.5z"
                                />
                            </svg>
                        </div>

                        <div>
                            <div
                                class="text-sm font-semibold
                                       text-cyan-800
                                       dark:text-cyan-200"
                            >
                                Конфигурация package.json
                            </div>

                            <div
                                class="mt-0.5 text-xs
                                       text-cyan-700
                                       dark:text-cyan-300"
                            >
                                Информация загружена непосредственно из
                                <span class="font-mono font-semibold">
                                    package.json
                                </span>.
                                Страница предназначена только для просмотра.
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Статистика -->
                <div
                    class="grid grid-cols-1 sm:grid-cols-4
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
                                   text-slate-500
                                   dark:text-slate-400"
                        >
                            Packages
                        </div>

                        <div
                            class="mt-1 text-xl font-semibold
                                   text-indigo-700
                                   dark:text-indigo-300"
                        >
                            {{ totalPackages }}
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
                                   text-slate-500
                                   dark:text-slate-400"
                        >
                            Scripts
                        </div>

                        <div
                            class="mt-1 text-xl font-semibold
                                   text-sky-700
                                   dark:text-sky-300"
                        >
                            {{ totalScripts }}
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
                                   text-slate-500
                                   dark:text-slate-400"
                        >
                            Dependencies
                        </div>

                        <div
                            class="mt-1 text-xl font-semibold
                                   text-emerald-700
                                   dark:text-emerald-300"
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
                                   text-slate-500
                                   dark:text-slate-400"
                        >
                            Dev Dependencies
                        </div>

                        <div
                            class="mt-1 text-xl font-semibold
                                   text-amber-700
                                   dark:text-amber-300"
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
                               text-slate-600
                               dark:text-slate-300"
                    >
                        Поиск по пакетам, версиям, scripts и секциям
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
                            placeholder="vue, vite, build, axios..."
                            class="w-full pl-8 pr-8 py-1.5
                                   text-sm font-mono
                                   border border-slate-300
                                   dark:border-slate-500
                                   rounded-md
                                   bg-white dark:bg-slate-800
                                   text-slate-800
                                   dark:text-slate-100
                                   placeholder:text-slate-400
                                   focus:outline-none
                                   focus:ring-1
                                   focus:ring-orange-500
                                   focus:border-orange-500"
                        >

                        <button
                            v-if="search"
                            type="button"
                            title="Очистить поиск"
                            class="absolute right-2 top-1/2
                                   -translate-y-1/2
                                   text-slate-400
                                   hover:text-red-500
                                   transition"
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
                               text-indigo-700
                               dark:text-indigo-300"
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
                                               text-orange-700
                                               dark:text-orange-300
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
                                        <pre
                                            v-if="isObject(entry.value)"
                                            class="m-0 whitespace-pre-wrap
                                                   break-words
                                                   font-mono text-xs"
                                        >{{ stringifyValue(entry.value) }}</pre>

                                    <template v-else>
                                        {{ displayValue(entry.value) }}
                                    </template>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Scripts -->
                <div
                    v-if="
                        scripts.length ||
                        packageData.scripts
                    "
                    class="mb-4"
                >
                    <div
                        class="flex items-center justify-between
                               mb-2"
                    >
                        <div
                            class="text-sm font-semibold
                                   text-sky-700
                                   dark:text-sky-300"
                        >
                            scripts
                        </div>

                        <div
                            class="text-xs
                                   text-slate-500
                                   dark:text-slate-400"
                        >
                            {{ scripts.length }}
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
                                class="bg-slate-100
                                       dark:bg-slate-800"
                            >
                            <tr>
                                <th
                                    class="w-52 px-3 py-2
                                               border-b border-slate-300
                                               dark:border-slate-600
                                               text-xs font-semibold
                                               text-slate-600
                                               dark:text-slate-300"
                                >
                                    Script
                                </th>

                                <th
                                    class="px-3 py-2
                                               border-b border-slate-300
                                               dark:border-slate-600
                                               text-xs font-semibold
                                               text-slate-600
                                               dark:text-slate-300"
                                >
                                    Command
                                </th>
                            </tr>
                            </thead>

                            <tbody>
                            <tr
                                v-for="item in scripts"
                                :key="item.name"
                                class="border-b border-slate-200
                                           dark:border-slate-700
                                           last:border-b-0
                                           hover:bg-sky-50
                                           dark:hover:bg-slate-800/80"
                            >
                                <td
                                    class="px-3 py-1.5
                                               font-mono font-semibold
                                               text-sky-700
                                               dark:text-sky-300
                                               align-top"
                                >
                                    {{ item.name }}
                                </td>

                                <td
                                    class="px-3 py-1.5
                                               font-mono
                                               text-slate-800
                                               dark:text-slate-100
                                               break-all"
                                >
                                    {{ item.value }}
                                </td>
                            </tr>

                            <tr v-if="!scripts.length">
                                <td
                                    colspan="2"
                                    class="px-4 py-6
                                               text-center text-sm
                                               text-slate-500
                                               dark:text-slate-400"
                                >
                                    Scripts не найдены
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Dependencies -->
                <div
                    v-if="
                        dependencies.length ||
                        packageData.dependencies
                    "
                    class="mb-4"
                >
                    <div
                        class="flex items-center justify-between
                               mb-2"
                    >
                        <div
                            class="text-sm font-semibold
                                   text-emerald-700
                                   dark:text-emerald-300"
                        >
                            dependencies
                        </div>

                        <div
                            class="text-xs
                                   text-slate-500
                                   dark:text-slate-400"
                        >
                            {{ dependencies.length }}
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
                                class="bg-slate-100
                                       dark:bg-slate-800"
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
                                v-for="item in dependencies"
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
                                    {{ item.value }}
                                </td>
                            </tr>

                            <tr v-if="!dependencies.length">
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

                <!-- Dev Dependencies -->
                <div
                    v-if="
                        devDependencies.length ||
                        packageData.devDependencies
                    "
                    class="mb-4"
                >
                    <div
                        class="flex items-center justify-between
                               mb-2"
                    >
                        <div
                            class="text-sm font-semibold
                                   text-amber-700
                                   dark:text-amber-300"
                        >
                            devDependencies
                        </div>

                        <div
                            class="text-xs
                                   text-slate-500
                                   dark:text-slate-400"
                        >
                            {{ devDependencies.length }}
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
                                class="bg-slate-100
                                       dark:bg-slate-800"
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
                                v-for="item in devDependencies"
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
                                    {{ item.value }}
                                </td>
                            </tr>

                            <tr v-if="!devDependencies.length">
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

                <!-- Peer Dependencies -->
                <div
                    v-if="
                        peerDependencies.length ||
                        packageData.peerDependencies
                    "
                    class="mb-4"
                >
                    <div
                        class="flex items-center justify-between
                               mb-2"
                    >
                        <div
                            class="text-sm font-semibold
                                   text-violet-700
                                   dark:text-violet-300"
                        >
                            peerDependencies
                        </div>

                        <div
                            class="text-xs
                                   text-slate-500
                                   dark:text-slate-400"
                        >
                            {{ peerDependencies.length }}
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
                            <tbody>
                            <tr
                                v-for="item in peerDependencies"
                                :key="item.name"
                                class="border-b border-slate-200
                                           dark:border-slate-700
                                           last:border-b-0"
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
                                    class="w-52 px-3 py-1.5
                                               font-mono
                                               text-violet-700
                                               dark:text-violet-300"
                                >
                                    {{ item.value }}
                                </td>
                            </tr>

                            <tr v-if="!peerDependencies.length">
                                <td
                                    colspan="2"
                                    class="px-4 py-6
                                               text-center text-sm
                                               text-slate-500
                                               dark:text-slate-400"
                                >
                                    Peer-зависимости не найдены
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Optional Dependencies -->
                <div
                    v-if="
                        optionalDependencies.length ||
                        packageData.optionalDependencies
                    "
                    class="mb-4"
                >
                    <div
                        class="flex items-center justify-between
                               mb-2"
                    >
                        <div
                            class="text-sm font-semibold
                                   text-fuchsia-700
                                   dark:text-fuchsia-300"
                        >
                            optionalDependencies
                        </div>

                        <div
                            class="text-xs
                                   text-slate-500
                                   dark:text-slate-400"
                        >
                            {{ optionalDependencies.length }}
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
                            <tbody>
                            <tr
                                v-for="item in optionalDependencies"
                                :key="item.name"
                                class="border-b border-slate-200
                                           dark:border-slate-700
                                           last:border-b-0"
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
                                    class="w-52 px-3 py-1.5
                                               font-mono
                                               text-fuchsia-700
                                               dark:text-fuchsia-300"
                                >
                                    {{ item.value }}
                                </td>
                            </tr>

                            <tr v-if="!optionalDependencies.length">
                                <td
                                    colspan="2"
                                    class="px-4 py-6
                                               text-center text-sm
                                               text-slate-500
                                               dark:text-slate-400"
                                >
                                    Optional-зависимости не найдены
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
                                       text-orange-700
                                       dark:text-orange-300"
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
                                       hover:text-orange-600
                                       hover:bg-orange-100
                                       dark:hover:text-orange-300
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
                                {{ displayValue(section.value) }}
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Ничего не найдено -->
                <div
                    v-if="
                        search &&
                        !scripts.length &&
                        !dependencies.length &&
                        !devDependencies.length &&
                        !peerDependencies.length &&
                        !optionalDependencies.length &&
                        !otherSections.length
                    "
                    class="py-8 text-center
                           text-sm
                           text-slate-500
                           dark:text-slate-400"
                >
                    Ничего не найдено
                </div>

                <!-- Подвал -->
                <div
                    class="mt-3 text-xs
                           text-slate-500
                           dark:text-slate-400"
                >
                    Конфигурация package.json доступна только для просмотра.
                    Изменение зависимостей и запуск npm-команд через
                    административную панель отключены.
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
