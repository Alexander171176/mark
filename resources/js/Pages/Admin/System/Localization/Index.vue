<script setup>
/**
 * Админка → Локализация (словарь i18n)
 * Возможности:
 * 1) Просмотр словаря (key/value) + поиск
 * 2) Проверка дублей значений (одинаковый текст у разных ключей)
 * 3) Проверка missing keys (RU — эталон, подсветка отсутствующих ключей в EN/KK)
 * 4) Экспорт отчётов в TXT
 */

import { ref, computed } from 'vue'
import { useI18n } from 'vue-i18n'
import { useToast } from 'vue-toastification'

import AdminLayout from '@/Layouts/AdminLayout.vue'
import TitlePage from '@/Components/Admin/Headlines/TitlePage.vue'
import SearchInput from '@/Components/Admin/Search/SearchInput.vue'
import CountTable from '@/Components/Admin/Count/CountTable.vue'

/**
 * Корневые файлы локалей должны оставаться в /locales,
 * чтобы движок мог определить доступные языки.
 */
import ru from '@/locales/ru.js'
import en from '@/locales/en.js'
import kk from '@/locales/kk.js'

/**
 * i18n t() — переводы интерфейса (админки)
 * toast — уведомления
 */
const { t } = useI18n()
const toast = useToast()

/**
 * Список доступных локалей (фиксированный, чтобы не гадать)
 */
const availableLocales = ['ru', 'en', 'kk']

/**
 * Выбранная локаль для просмотра (таблица + дубли)
 */
const locale = ref('ru')

/**
 * Режим отображения:
 * - table: таблица key/value + поиск
 * - checks: проверки (duplicates + missing keys)
 */
const viewMode = ref('table') // 'table' | 'checks'

/**
 * Хранилище словарей по локали
 */
const dictionaries = {
    ru,
    en,
    kk
}

/**
 * Поисковый запрос
 */
const query = ref('')

/**
 * Где искать:
 * - all: key + value
 * - key: только key
 * - value: только value
 */
const searchIn = ref('all') // 'all' | 'key' | 'value'

/**
 * Текущий выбранный словарь (объект ключ -> значение)
 */
const currentDict = computed(() => dictionaries[locale.value] || {})

/**
 * Нормализация текста для поиска/сравнения:
 * - привести к строке
 * - схлопнуть пробелы
 * - trim
 * - lowerCase
 */
const normalize = (v) =>
    String(v ?? '')
        .replace(/\s+/g, ' ')
        .trim()
        .toLowerCase()

/**
 * Превращаем словарь в строки таблицы:
 * [{ key, value }]
 * + фильтрация по query/searchIn
 */
const rows = computed(() => {
    const dict = currentDict.value || {}
    const q = normalize(query.value)

    const list = Object.keys(dict)
        .sort((a, b) => a.localeCompare(b))
        .map((key) => ({
            key,
            value: String(dict[key] ?? '')
        }))

    if (!q) return list

    return list.filter((row) => {
        const k = normalize(row.key)
        const v = normalize(row.value)

        if (searchIn.value === 'key') return k.includes(q)
        if (searchIn.value === 'value') return v.includes(q)
        return k.includes(q) || v.includes(q)
    })
})

/**
 * Duplicates:
 * группируем одинаковые значения по нормализованной строке.
 * Возвращаем массив:
 * [
 *   { value: 'Оригинальный текст', keys: ['k1','k2',...] },
 *   ...
 * ]
 */
const duplicates = computed(() => {
    const dict = currentDict.value || {}
    const map = new Map() // normValue -> { value, keys[] }

    for (const key of Object.keys(dict)) {
        const raw = String(dict[key] ?? '')
        const norm = normalize(raw)

        // пустые значения пропускаем
        if (!norm) continue

        if (!map.has(norm)) {
            map.set(norm, { value: raw, keys: [] })
        }

        map.get(norm).keys.push(key)
    }

    return Array.from(map.values())
        .filter((g) => g.keys.length > 1)
        .sort((a, b) => b.keys.length - a.keys.length)
})

/**
 * Missing keys:
 * RU — эталон, сравниваем наличие ключей в EN/KK
 */
const baseLocale = 'ru'
const baseDict = computed(() => dictionaries[baseLocale] || {})

const missingKeysByLocale = computed(() => {
    const base = baseDict.value || {}
    const result = {}

    for (const loc of Object.keys(dictionaries)) {
        if (loc === baseLocale) continue
        const dict = dictionaries[loc] || {}

        const missing = []
        for (const key of Object.keys(base)) {
            if (!(key in dict)) missing.push(key)
        }

        result[loc] = missing.sort((a, b) => a.localeCompare(b))
    }

    return result
})

/**
 * Счётчики для UI (в зависимости от режима)
 */
const tableCount = computed(() => rows.value.length)
const duplicatesCount = computed(() => duplicates.value.length)
const missingCount = computed(() => {
    const miss = missingKeysByLocale.value || {}
    return (miss.en?.length || 0) + (miss.kk?.length || 0)
})

/**
 * Универсальная функция для скачивания TXT файла в браузере
 */
const downloadTxt = (filename, text) => {
    const blob = new Blob([text], { type: 'text/plain;charset=utf-8' })
    const url = URL.createObjectURL(blob)

    const a = document.createElement('a')
    a.href = url
    a.download = filename
    document.body.appendChild(a)
    a.click()
    a.remove()

    URL.revokeObjectURL(url)
}

/**
 * Экспорт текущей локали: key=value по строкам
 */
const exportCurrentLocaleTxt = () => {
    const dict = currentDict.value || {}
    const keys = Object.keys(dict).sort((a, b) => a.localeCompare(b))

    const lines = keys.map((k) => `${k}=${String(dict[k] ?? '')}`)
    downloadTxt(`locale_${locale.value}.txt`, lines.join('\n'))

    toast.success(`Экспорт TXT: ${locale.value.toUpperCase()}`)
}

/**
 * Экспорт отчёта duplicates по текущей локали
 */
const exportDuplicatesTxt = () => {
    const groups = duplicates.value || []
    const header = [
        `DUPLICATES report`,
        `Locale: ${locale.value.toUpperCase()}`,
        `Groups: ${groups.length}`,
        `Generated: ${new Date().toISOString()}`,
        `---`
    ]

    const body = groups.flatMap((g, idx) => [
        `#${idx + 1} (${g.keys.length} keys)`,
        `value: ${g.value}`,
        ...g.keys.map((k) => ` - ${k}`),
        ``
    ])

    downloadTxt(`locale_${locale.value}_duplicates.txt`, [...header, ...body].join('\n'))
    toast.success('Экспорт TXT: duplicates')
}

/**
 * Экспорт missing keys (RU эталон)
 */
const exportMissingTxt = () => {
    const miss = missingKeysByLocale.value || {}

    const header = [
        `MISSING KEYS report`,
        `Base locale: ${baseLocale.toUpperCase()}`,
        `Generated: ${new Date().toISOString()}`,
        `---`
    ]

    const body = Object.entries(miss).flatMap(([loc, keys]) => [
        `${loc.toUpperCase()}: missing ${keys.length}`,
        ...keys.map((k) => ` - ${k}`),
        ``
    ])

    downloadTxt(`locale_missing_keys_${baseLocale}.txt`, [...header, ...body].join('\n'))
    toast.success('Экспорт TXT: missing keys')
}

/**
 * Копирование ключа в буфер обмена
 */
const copyKey = async (key) => {
    try {
        await navigator.clipboard.writeText(key)
        toast.success(`Ключ скопирован: ${key}`)
    } catch {
        toast.error('Не удалось скопировать ключ')
    }
}

/**
 * Классы активной/неактивной кнопки локали
 */
const localeLinkClass = (l) =>
    locale.value === l
        ? 'bg-blue-500 text-white'
        : 'bg-slate-100 dark:bg-slate-900 text-slate-700 dark:text-slate-200 ' +
        'hover:bg-slate-300 dark:hover:bg-slate-600'

/**
 * Классы активной/неактивной кнопки режима (table/checks)
 */
const viewBtnClass = (m) =>
    viewMode.value === m
        ? 'bg-gray-200 dark:bg-gray-800 border border-blue-400'
        : 'bg-slate-50 dark:bg-slate-950 border border-gray-400 ' +
        'hover:bg-slate-300 dark:hover:bg-slate-600'
</script>

<template>
    <AdminLayout :title="t('localization')">
        <template #header>
            <TitlePage>{{ t('localization') }}</TitlePage>
        </template>

        <div class="px-2 py-2 w-full max-w-12xl mx-auto">
            <div
                class="p-4 bg-slate-50 dark:bg-slate-700
                       border border-blue-400 dark:border-blue-200
                       overflow-hidden shadow-md shadow-gray-500 dark:shadow-slate-400
                       bg-opacity-95 dark:bg-opacity-95"
            >

                <!-- Локали + режим + экспорт -->
                <div class="mt-2 grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-3 gap-3 items-center">

                    <!-- 1) Переключатель локали -->
                    <div class="flex lg:justify-start xl:justify-start">
                        <div
                            class="flex items-center justify-end space-x-2 px-3 py-1
             border-x border-t border-gray-400 rounded-t-lg
             bg-gray-100 dark:bg-gray-900"
                        >
      <span class="text-sm font-medium text-slate-700 dark:text-slate-200">
        {{ t('localization') }}:
      </span>

                            <button
                                v-for="l in availableLocales"
                                :key="l"
                                type="button"
                                class="px-3 py-1 text-sm font-medium rounded-sm"
                                :class="localeLinkClass(l)"
                                @click="locale = l"
                            >
                                {{ l.toUpperCase() }}
                            </button>
                        </div>
                    </div>

                    <!-- 2) экспорт -->
                    <div class="flex justify-start lg:justify-end xl:justify-center">
                        <div class="flex items-center gap-2 flex-wrap
                                    text-gray-800 dark:text-gray-200">

                            <button
                                type="button"
                                class="text-xs px-2 py-1 rounded-sm border border-slate-400
                                       hover:bg-slate-200 dark:hover:bg-slate-800"
                                @click="exportCurrentLocaleTxt"
                            >
                                {{ t('export')}} TXT
                            </button>

                            <button
                                type="button"
                                class="text-xs px-2 py-1 rounded-sm border border-slate-400
                                       hover:bg-slate-200 dark:hover:bg-slate-800"
                                @click="exportDuplicatesTxt"
                            >
                                {{ t('duplicates')}} TXT
                            </button>

                            <button
                                type="button"
                                class="text-xs px-2 py-1 rounded-sm border border-slate-400
                                       hover:bg-slate-200 dark:hover:bg-slate-800"
                                @click="exportMissingTxt"
                            >
                                {{ t('missing')}} TXT
                            </button>
                        </div>
                    </div>

                    <!-- 3) Переключатель вида -->
                    <div class="flex justify-end">
                        <div class="flex items-center gap-2">
                          <span class="font-semibold text-sm text-slate-800 dark:text-slate-200">
                            {{ t('view')}}:
                          </span>

                            <button
                                type="button"
                                class="px-2 py-1 text-sm font-medium rounded-sm"
                                :class="viewBtnClass('table')"
                                @click="viewMode = 'table'"
                                :title="`Таблица - ${t('quantity')}: [${tableCount}]`"
                            >
                                <svg class="shrink-0 h-4 w-4" viewBox="0 0 24 24">
                                    <path class="fill-current text-slate-800 dark:text-slate-200"
                                          d="M23,1H1A1,1,0,0,0,0,2V22a1,1,0,0,0,1,1H23a1,1,0,0,0,1-1V2A1,1,0,0,0,23,1ZM7,3A1,1,0,1,1,6,4,1,1,0,0,1,7,3ZM3,3A1,1,0,1,1,2,4,1,1,0,0,1,3,3ZM22,21H2V7H22Z"></path>
                                    <path class="fill-current text-slate-800 dark:text-slate-200"
                                          d="M20.851,12.475A1,1,0,0,0,20,12H11.445a4,4,0,1,0,0,4H14l1.5,1L17,16h2a1,1,0,0,0,.895-.553l1-2A1,1,0,0,0,20.851,12.475ZM7,15a1,1,0,1,1,1-1A1,1,0,0,1,7,15Z"></path>
                                </svg>
                            </button>

                            <button
                                type="button"
                                class="px-2 py-1 text-sm font-medium rounded-sm"
                                :class="viewBtnClass('checks')"
                                @click="viewMode = 'checks'"
                                :title="`Проверки - (dup: [${duplicatesCount}], miss: [${missingCount}])`"
                            >
                                <svg class="shrink-0 h-4 w-4" viewBox="0 0 24 24">
                                    <path class="fill-current text-slate-800 dark:text-slate-200"
                                          d="M13.494,18.405l6.131-3.539a1,1,0,1,0-1-1.732L12.5,16.673A7,7,0,0,1,13.494,18.405Z"></path>
                                    <path class="fill-current text-slate-800 dark:text-slate-200"
                                          d="M21,22H12V21a5.006,5.006,0,0,0-5-5,4.946,4.946,0,0,0-1.787.345L4.9,15.861A4.988,4.988,0,0,1,6.335,8.773l1.382-.8,1.417,2.454a1,1,0,0,0,1.366.366l.866-.5.7,1.206a1,1,0,0,0,.867.5,1,1,0,0,0,.866-1.5l-.7-1.206.866-.5a1,1,0,0,0,.366-1.366L10.33.5A1,1,0,0,0,8.964.134l-3.464,2A1,1,0,0,0,5.134,3.5L6.717,6.243l-1.41.816a6.984,6.984,0,0,0-2.063,9.916l.282.442A4.977,4.977,0,0,0,2,21v2a1,1,0,0,0,1,1H21a1,1,0,0,0,0-2Z"></path>
                                </svg>
                            </button>
                        </div>
                    </div>

                </div>

                <!-- Счётчик -->
                <div class="flex justify-center py-1 border border-gray-400">
                    <CountTable>
                        <template v-if="viewMode === 'table'">
                            {{ tableCount }}
                        </template>
                        <template v-else>
                {{t('duplicates')}}: {{ duplicatesCount }} | {{t('missing')}}: {{ missingCount }}
                        </template>
                    </CountTable>
                </div>

                <!-- Поиск (только в режиме таблицы) -->
                <div class="my-2" v-if="viewMode === 'table'">
                    <SearchInput v-model="query" :placeholder="`${t('search')} (key / value)`" />

                    <div class="mt-2 flex items-center justify-end gap-2">
                        <span class="text-sm text-slate-700 dark:text-slate-300">
                            Искать в:
                        </span>

                        <select
                            v-model="searchIn"
                            class="text-xs pl-2 pr-5 py-1 rounded-sm border border-slate-400
                                   bg-white dark:bg-slate-800 dark:text-slate-100 w-fit"
                        >
                            <option value="all">key + value</option>
                            <option value="key">только key</option>
                            <option value="value">только value</option>
                        </select>
                    </div>
                </div>

                <!-- ========================= -->
                <!-- РЕЖИМ: ТАБЛИЦА (key/value) -->
                <!-- ========================= -->
                <div
                    v-if="viewMode === 'table'"
                    class="bg-white dark:bg-slate-700 shadow-lg rounded-sm border border-gray-400"
                >
                    <div class="overflow-x-auto">
                        <table class="table-auto w-full text-slate-700 dark:text-slate-100">
                            <thead
                                class="text-sm uppercase bg-slate-200 dark:bg-cyan-900
                                       border border-solid border-gray-300 dark:border-gray-700"
                            >
                            <tr>
                                <th class="px-2 py-0.5 font-medium text-left w-[340px]">key</th>
                                <th class="px-2 py-0.5 font-medium text-left">value</th>
                                <th class="px-2 py-0.5 font-medium text-right w-[140px]">
                                    {{ t('actions') }}
                                </th>
                            </tr>
                            </thead>

                            <tbody>
                            <tr
                                v-for="row in rows"
                                :key="row.key"
                                class="text-sm border-b hover:bg-slate-100 dark:hover:bg-cyan-800"
                            >
                                <td class="px-2 py-0.5 font-mono text-sm font-semibold
                                           text-indigo-800 dark:text-indigo-200">
                                    {{ row.key }}
                                </td>
                                <td class="px-2 py-0.5 text-xs font-semibold
                                           text-gray-800 dark:text-gray-200">
                                    {{ row.value }}
                                </td>
                                <td class="px-2 py-0.5 text-right">
                                    <button
                                        type="button"
                                        class="text-xs px-2 py-1 rounded-sm border border-slate-400
                                                   hover:bg-slate-200 dark:hover:bg-slate-800"
                                        @click="copyKey(row.key)"
                                    >
                                        copy key
                                    </button>
                                </td>
                            </tr>

                            <tr v-if="!rows.length">
                                <td colspan="3" class="p-5 text-center text-slate-700 dark:text-slate-100">
                                    {{ t('noData') }}
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- ======================= -->
                <!-- РЕЖИМ: ПРОВЕРКИ (checks) -->
                <!-- ======================= -->
                <div v-if="viewMode === 'checks'">
                    <!-- Duplicates -->
                    <div class="mt-2">
                        <div class="flex items-center justify-between">
                            <div class="text-sm font-semibold pl-1
                                        text-slate-900 dark:text-slate-100">
                                {{t('duplicates')}} ({{ locale.toUpperCase() }})
                            </div>
                            <div class="text-xs text-slate-600 dark:text-slate-200 pr-1">
                                {{t('groups')}}: {{ duplicatesCount }}
                            </div>
                        </div>

                        <div
                            class="mt-2 bg-white dark:bg-slate-700 shadow-lg rounded-sm
                                   border border-slate-200 dark:border-slate-600"
                        >
                            <div class="overflow-x-auto">
                                <table class="table-auto w-full text-slate-700 dark:text-slate-100">
                                    <thead
                                        class="text-sm uppercase bg-slate-200 dark:bg-cyan-900
                                               border border-solid border-gray-400"
                                    >
                                    <tr>
                                        <th class="px-2 py-0.5 font-medium text-left w-[140px]">
                                            keys
                                        </th>
                                        <th class="px-2 py-0.5 font-medium text-left">
                                            value
                                        </th>
                                        <th class="px-2 py-0.5 font-medium text-left">
                                            key list
                                        </th>
                                    </tr>
                                    </thead>

                                    <tbody class="border border-solid border-gray-400">
                                    <tr
                                        v-for="(g, idx) in duplicates"
                                        :key="idx"
                                        class="text-sm border-b
                                               hover:bg-slate-100 dark:hover:bg-cyan-800"
                                    >
                                        <td class="px-2 py-0.5 text-xs font-semibold
                                                   text-rose-800 dark:text-rose-200">
                                            {{ g.keys.length }}
                                        </td>

                                        <td class="px-2 py-0.5 text-xs font-semibold
                                                   text-gray-800 dark:text-gray-200">
                                            {{ g.value }}
                                        </td>

                                        <td class="px-2 py-0.5 text-xs font-mono">
                                            <div class="flex flex-wrap gap-2">
                                                <button
                                                    v-for="k in g.keys"
                                                    :key="k"
                                                    type="button"
                                                    class="px-2 py-0.5 rounded-sm
                                                           border border-slate-400
                                                           hover:bg-slate-200
                                                           dark:hover:bg-slate-800 font-mono
                                                           text-xs font-semibold text-indigo-800
                                                           dark:text-indigo-200"
                                                    @click="copyKey(k)"
                                                    :title="'copy key: ' + k"
                                                >
                                                    {{ k }}
                                                </button>
                                            </div>
                                        </td>
                                    </tr>

                                    <tr v-if="!duplicates.length">
                                        <td colspan="3" class="p-4 text-center text-slate-700 dark:text-slate-100">
                                            Дубликатов значений не найдено.
                                        </td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Missing keys -->
                    <div class="mt-6">
                        <div class="flex items-center justify-between">
                            <div class="text-sm font-semibold pl-1
                                        text-slate-900 dark:text-slate-100">
                                Missing keys (base: {{ baseLocale.toUpperCase() }})
                            </div>
                            <div class="text-xs text-slate-600 dark:text-slate-200 pr-1">
                                EN: {{ missingKeysByLocale.en?.length || 0 }},
                                KK: {{ missingKeysByLocale.kk?.length || 0 }}
                            </div>
                        </div>

                        <div class="mt-2 grid grid-cols-1 md:grid-cols-2 gap-3">
                            <div
                                v-for="loc in ['en', 'kk']"
                                :key="loc"
                                class="bg-white dark:bg-slate-700 shadow-lg rounded-sm
                                       border border-slate-400"
                            >
                                <div
                                    class="px-2 py-0.5 border-b border-slate-400
                                           flex items-center justify-between">
                                    <div class="text-sm font-semibold
                                                text-amber-800 dark:text-amber-200">
                                        {{ loc.toUpperCase() }}
                                    </div>
                                    <div class="text-xs font-semibold
                                                text-teal-700 dark:text-teal-300">
                                        missing: {{ missingKeysByLocale[loc]?.length || 0 }}
                                    </div>
                                </div>

                                <div class="p-3">
                                    <div
                                        v-if="(missingKeysByLocale[loc]?.length || 0) === 0"
                                        class="italic font-semibold text-sm
                                               text-slate-600 dark:text-slate-400"
                                    >
                                        Всё ок — пропусков нет.
                                    </div>

                                    <div v-else class="flex flex-wrap gap-2">
                                        <button
                                            v-for="k in missingKeysByLocale[loc]"
                                            :key="k"
                                            type="button"
                                            class="text-xs font-mono px-2 py-0.5 rounded-sm
                                                   border border-rose-400
                                                   bg-rose-50 dark:bg-rose-900/30
                                                   font-medium text-rose-700 dark:text-rose-200
                                                   hover:bg-rose-100 dark:hover:bg-rose-900/50"
                                            @click="copyKey(k)"
                                            :title="'copy missing key: ' + k"
                                        >
                                            {{ k }}
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- /checks -->
            </div>
        </div>
    </AdminLayout>
</template>
