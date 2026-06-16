<script setup>
import { ref, watch, computed } from 'vue'
import { useI18n } from 'vue-i18n'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import TitlePage from '@/Components/Admin/UI/Headlines/TitlePage.vue'
import * as XLSX from 'xlsx'

// @ts-ignore
import html2pdf from 'html2pdf.js'
import JSZip from 'jszip'

// @ts-ignore
import { saveAs } from 'file-saver'

import {
    Document,
    Packer,
    Paragraph,
    Table,
    TableCell,
    TableRow,
    WidthType,
    PageOrientation,
    TextRun,
} from 'docx'

const { t } = useI18n()

const props = defineProps({
    tables: { type: Array, default: () => [] },
    selectedTable: { type: String, default: '' },
    columns: { type: Array, default: () => [] },
    items: { type: Array, default: () => [] },
})

// Выбранная таблица
const selectedTable = ref(props.selectedTable || props.tables?.[0] || '')

// Строки выбранной таблицы
const items = ref(props.items || [])

// Колонки выбранной таблицы
const columns = ref(props.columns || [])

// Поля, которые попадут в отчёт
const selectedFields = ref(props.columns || [])

// Состояние загрузки
const loading = ref(false)

// Ошибка загрузки
const errorMessage = ref('')

// Даты для заголовков и имён файлов
const currentDate = new Date().toLocaleDateString('ru-RU')
const currentDateTime = new Date().toISOString().slice(0, 19).replace(/:/g, '-')

// Есть ли данные
const hasData = computed(() => items.value.length > 0)

// Заголовок отчёта
const reportTitle = computed(() => `Отчёт: ${selectedTable.value} - ${currentDate}`)

// Получение данных выбранной таблицы
const fetchData = async () => {
    if (!selectedTable.value) return

    loading.value = true
    errorMessage.value = ''

    try {
        const response = await fetch(`/admin/reports?table=${selectedTable.value}`, {
            headers: { Accept: 'application/json' },
        })

        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`)
        }

        const data = await response.json()

        items.value = data.data || []
        columns.value = data.columns || []
        selectedFields.value = data.columns || []
    } catch (error) {
        console.error('Не удалось получить данные:', error)
        errorMessage.value = 'Не удалось получить данные таблицы.'
        items.value = []
        columns.value = []
        selectedFields.value = []
    } finally {
        loading.value = false
    }
}

// Следим за сменой таблицы
watch(selectedTable, fetchData)

// Безопасное отображение значения
const formatValue = (value) => {
    if (value === null || value === undefined) return ''

    if (typeof value === 'boolean') {
        return value ? 'true' : 'false'
    }

    if (Array.isArray(value)) {
        return value.join(', ')
    }

    if (typeof value === 'object') {
        return JSON.stringify(value)
    }

    return String(value)
}

// Подготовка данных для экспорта
const formatData = (data) => {
    return data.map((item) => {
        const row = {}

        selectedFields.value.forEach((field) => {
            row[field] = formatValue(item[field])
        })

        return row
    })
}

// Выбор формата скачивания
const downloadReport = (format) => {
    if (!hasData.value || !selectedFields.value.length) {
        alert('Нет данных для экспорта.')
        return
    }

    switch (format) {
        case 'csv':
            downloadCSV()
            break
        case 'xls':
            downloadXLS()
            break
        case 'pdf':
            downloadPDF()
            break
        case 'zip':
            downloadZIP()
            break
        case 'docx':
            downloadDOCX()
            break
    }
}

// Скачивание CSV
const downloadCSV = () => {
    const worksheet = XLSX.utils.json_to_sheet(formatData(items.value))
    const csvOutput = XLSX.utils.sheet_to_csv(worksheet)
    const blob = new Blob(['\uFEFF' + csvOutput], { type: 'text/csv;charset=utf-8;' })

    saveAs(blob, `reports_${selectedTable.value}_${currentDateTime}.csv`)
}

// Скачивание Excel
const downloadXLS = () => {
    const worksheet = XLSX.utils.json_to_sheet(formatData(items.value))
    const workbook = XLSX.utils.book_new()

    XLSX.utils.book_append_sheet(workbook, worksheet, 'Report')

    const xlsOutput = XLSX.write(workbook, { bookType: 'xlsx', type: 'array' })
    const blob = new Blob([xlsOutput], { type: 'application/octet-stream' })

    saveAs(blob, `reports_${selectedTable.value}_${currentDateTime}.xlsx`)
}

// Скачивание PDF
const downloadPDF = () => {
    const formattedData = formatData(items.value)

    const tableHeaders = selectedFields.value
        .map((field) => `<th>${field}</th>`)
        .join('')

    const tableRows = formattedData
        .map((item) => {
            const row = selectedFields.value
                .map((field) => `<td>${formatValue(item[field])}</td>`)
                .join('')

            return `<tr>${row}</tr>`
        })
        .join('')

    const wrapper = document.createElement('div')

    wrapper.innerHTML = `
        <div style="font-family: Arial, sans-serif; font-size: 9px; color: #111; background: #fff;">
            <h1 style="font-size: 18px; margin-bottom: 12px;">${reportTitle.value}</h1>

            <style>
                table {
                    width: 100%;
                    border-collapse: collapse;
                    table-layout: fixed;
                }

                th, td {
                    border: 1px solid #999;
                    padding: 4px 6px;
                    vertical-align: top;
                    word-break: break-word;
                    overflow-wrap: anywhere;
                }

                th {
                    background: #f0f0f0;
                    font-weight: bold;
                }

                tr {
                    page-break-inside: avoid;
                }
            </style>

            <table>
                <thead>
                    <tr>${tableHeaders}</tr>
                </thead>
                <tbody>
                    ${tableRows}
                </tbody>
            </table>
        </div>
    `

    const options = {
        margin: 0.35,
        filename: `reports_${selectedTable.value}_${currentDateTime}.pdf`,
        image: { type: 'jpeg', quality: 0.98 },
        html2canvas: {
            scale: 1,
            logging: false,
            useCORS: true,
            backgroundColor: '#ffffff',
        },
        jsPDF: {
            unit: 'in',
            format: 'a4',
            orientation: 'landscape',
        },
        pagebreak: {
            mode: ['css', 'legacy'],
            avoid: ['tr'],
        },
    }

    html2pdf()
        .set(options)
        .from(wrapper)
        .save()
}

// Скачивание ZIP
const downloadZIP = async () => {
    const zip = new JSZip()
    const formattedData = formatData(items.value)

    const worksheet = XLSX.utils.json_to_sheet(formattedData)
    const csvOutput = XLSX.utils.sheet_to_csv(worksheet)
    const csvBlob = new Blob(['\uFEFF' + csvOutput], { type: 'text/csv;charset=utf-8;' })

    zip.file(`reports_${selectedTable.value}_${currentDateTime}.csv`, csvBlob)

    const workbook = XLSX.utils.book_new()
    XLSX.utils.book_append_sheet(workbook, worksheet, 'Report')

    const xlsOutput = XLSX.write(workbook, { bookType: 'xlsx', type: 'array' })
    const xlsBlob = new Blob([xlsOutput], { type: 'application/octet-stream' })

    zip.file(`reports_${selectedTable.value}_${currentDateTime}.xlsx`, xlsBlob)

    const doc = createDOCX()
    const docBlob = await Packer.toBlob(doc)

    zip.file(`reports_${selectedTable.value}_${currentDateTime}.docx`, docBlob)

    const content = await zip.generateAsync({ type: 'blob' })

    saveAs(content, `reports_${selectedTable.value}_${currentDateTime}.zip`)
}

// Скачивание Word
const downloadDOCX = () => {
    const doc = createDOCX()

    Packer.toBlob(doc).then((blob) => {
        saveAs(blob, `reports_${selectedTable.value}_${currentDateTime}.docx`)
    })
}

// Создание Word-документа
const createDOCX = () => {
    const formattedData = formatData(items.value)

    const tableRows = [
        new TableRow({
            children: selectedFields.value.map((key) => new TableCell({
                children: [
                    new Paragraph({
                        children: [new TextRun(key)],
                        spacing: { after: 200 },
                    }),
                ],
            })),
        }),
    ]

    formattedData.forEach((item) => {
        tableRows.push(
            new TableRow({
                children: selectedFields.value.map((key) => new TableCell({
                    children: [
                        new Paragraph({
                            children: [new TextRun(String(item[key] ?? ''))],
                            spacing: { after: 200 },
                        }),
                    ],
                })),
            })
        )
    })

    return new Document({
        sections: [
            {
                properties: {
                    page: {
                        size: {
                            orientation: PageOrientation.LANDSCAPE,
                        },
                    },
                },
                children: [
                    new Paragraph({
                        text: reportTitle.value,
                        heading: 'Heading1',
                        spacing: { after: 400 },
                    }),
                    new Table({
                        rows: tableRows,
                        width: { size: 100, type: WidthType.PERCENTAGE },
                    }),
                ],
            },
        ],
    })
}
</script>

<template>
    <AdminLayout :title="t('reports')">
        <template #header>
            <TitlePage>{{ t('reports') }}</TitlePage>
        </template>

        <div class="px-2 py-2 w-full max-w-12xl mx-auto">
            <div
                class="sm:flex sm:justify-between sm:items-center mb-4
                       p-4 bg-slate-50 dark:bg-slate-700
                       border border-blue-400 dark:border-blue-200
                       overflow-hidden shadow-lg shadow-gray-500 dark:shadow-slate-400
                       bg-opacity-95 dark:bg-opacity-95"
            >
                <div>
                    <label
                        for="reportTable"
                        class="block text-sm font-medium text-gray-700 dark:text-gray-300"
                    >
                        {{ t('selectReportType') }}
                    </label>

                    <select
                        id="reportTable"
                        v-model="selectedTable"
                        class="mt-1 block w-full py-1 px-3 border border-gray-300
                               bg-white dark:bg-gray-700 dark:text-white rounded-md shadow-sm
                               focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                    >
                        <option
                            v-for="table in tables"
                            :key="table"
                            :value="table"
                        >
                            {{ table }}
                        </option>
                    </select>
                </div>

                <div class="mt-4 sm:mt-0 sm:ml-4">
                    <button
                        @click="downloadReport('csv')"
                        class="inline-flex items-center px-2 py-1 border border-transparent
                               text-sm font-medium rounded-md text-white bg-purple-600 hover:bg-purple-700"
                    >
                        CSV
                    </button>

                    <button
                        @click="downloadReport('xls')"
                        class="ml-2 inline-flex items-center px-2 py-1 border border-transparent
                               text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700"
                    >
                        Excel
                    </button>

                    <button
                        @click="downloadReport('docx')"
                        class="ml-2 inline-flex items-center px-2 py-1 border border-transparent
                               text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700"
                    >
                        Word
                    </button>

                    <button
                        @click="downloadReport('pdf')"
                        class="ml-2 inline-flex items-center px-2 py-1 border border-transparent
                               text-sm font-medium rounded-md text-white bg-red-600 hover:bg-red-700"
                    >
                        PDF
                    </button>

                    <button
                        @click="downloadReport('zip')"
                        class="ml-2 inline-flex items-center px-2 py-1 border border-transparent
                               text-sm font-medium rounded-md text-white bg-slate-600 hover:bg-slate-700"
                    >
                        ZIP
                    </button>
                </div>
            </div>

            <div
                class="p-4 bg-slate-50 dark:bg-slate-700
                       border border-blue-400 dark:border-blue-200
                       overflow-hidden shadow-lg shadow-gray-500 dark:shadow-slate-400
                       bg-opacity-95 dark:bg-opacity-95"
            >
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                    {{ t('selectFieldsPrint') }}
                </label>

                <div
                    v-if="columns.length"
                    class="mt-2 grid grid-cols-2 md:grid-cols-4 gap-2"
                >
                    <div
                        v-for="column in columns"
                        :key="column"
                    >
                        <input
                            type="checkbox"
                            v-model="selectedFields"
                            :value="column"
                            class="form-checkbox h-4 w-4 text-indigo-600 transition duration-150 ease-in-out"
                        >

                        <span class="ml-2 text-gray-700 dark:text-gray-300">
                            {{ column }}
                        </span>
                    </div>
                </div>

                <div
                    v-else
                    class="mt-2 text-sm text-gray-700 dark:text-gray-300"
                >
                    Нет доступных колонок.
                </div>
            </div>

            <div
                id="reportContent"
                class="p-4 bg-slate-50 dark:bg-slate-700
                       border border-blue-400 dark:border-blue-200
                       overflow-hidden shadow-lg shadow-gray-500 dark:shadow-slate-400
                       bg-opacity-95 dark:bg-opacity-95 text-xs"
            >
                <h1 class="text-md font-semibold mb-4 dark:text-white">
                    {{ reportTitle }}
                </h1>

                <div
                    v-if="loading"
                    class="text-slate-700 dark:text-slate-100"
                >
                    Загрузка...
                </div>

                <div
                    v-else-if="errorMessage"
                    class="text-red-600 dark:text-red-300 font-semibold"
                >
                    {{ errorMessage }}
                </div>

                <div
                    v-else-if="!hasData"
                    class="text-slate-700 dark:text-slate-100"
                >
                    {{ t('noData') }}
                </div>

                <div
                    v-else
                    class="overflow-x-auto"
                >
                    <table class="min-w-full bg-white dark:bg-gray-800 dark:text-white text-xxs">
                        <thead>
                        <tr class="dark:text-slate-700">
                            <th
                                v-for="key in selectedFields"
                                :key="key"
                                class="px-4 py-1 dark:bg-gray-900 dark:text-white"
                            >
                                {{ key }}
                            </th>
                        </tr>
                        </thead>

                        <tbody>
                        <tr
                            v-for="(item, index) in items"
                            :key="item.id || index"
                            class="bg-white dark:bg-gray-800 border-b dark:border-gray-700"
                        >
                            <td
                                v-for="key in selectedFields"
                                :key="key"
                                :class="{ truncate: String(item[key] || '').length > 80 }"
                                class="px-4 py-1"
                            >
                                {{ formatValue(item[key]) }}
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>

<style scoped>
table {
    width: 100%;
    border-collapse: collapse;
}

th,
td {
    border: 1px solid black;
    padding: 8px;
    text-align: left;
    font-size: 10px;
}

th {
    background-color: #f2f2f2;
}

.truncate {
    max-width: 240px;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.dark th {
    background-color: #374151;
    color: #ffffff;
}
</style>
