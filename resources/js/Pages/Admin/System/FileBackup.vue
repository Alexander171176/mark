<script setup>
import { ref, onMounted } from 'vue'
import { useI18n } from 'vue-i18n'
import axios from 'axios'
import { useToast } from 'vue-toastification'

import AdminLayout from '@/Layouts/AdminLayout.vue'
import TitlePage from '@/Components/Admin/UI/Headlines/TitlePage.vue'

const { t } = useI18n()
const toast = useToast()

// Флаг активного процесса
const isProcessing = ref(false)

// Прогресс текущей операции
const progress = ref(0)

// Список архивов
const archives = ref([])

// Заголовок блока процесса
const processTitle = ref('')

// Главное сообщение процесса
const processMessage = ref('')

// Статус процесса: processing | done | error
const processStatus = ref('')

// Количество обработанных файлов
const processedFiles = ref(0)

// Общее количество файлов
const totalFiles = ref(0)

// Имя текущего архива
const currentArchive = ref('')

// Журнал последних сообщений процесса
const processLog = ref([])

// Сбросить блок процесса
const resetProcess = () => {
    isProcessing.value = false
    progress.value = 0
    processTitle.value = ''
    processMessage.value = ''
    processStatus.value = ''
    processedFiles.value = 0
    totalFiles.value = 0
    currentArchive.value = ''
    processLog.value = []
}

// Подготовить блок процесса
const startProcess = (title, archiveName = '') => {
    isProcessing.value = true
    progress.value = 1
    processStatus.value = 'processing'
    processTitle.value = title
    processMessage.value = ''
    processedFiles.value = 0
    totalFiles.value = 0
    currentArchive.value = archiveName
    processLog.value = []
}

// Добавить сообщение в журнал процесса
const addProcessMessage = (message) => {
    processMessage.value = message

    processLog.value.unshift({
        time: new Date().toLocaleTimeString(),
        message,
    })

    if (processLog.value.length > 10) {
        processLog.value.pop()
    }
}

// Обновить состояние процесса из ответа сервера
const updateProcessState = (data, fallbackMessage = '') => {
    progress.value = data.progress || 0
    processStatus.value = data.status || 'processing'
    processedFiles.value = data.processed || 0
    totalFiles.value = data.total || 0
    currentArchive.value = data.filename || currentArchive.value

    addProcessMessage(data.message || fallbackMessage)
}

// Загрузить список архивов
const fetchArchives = async () => {
    try {
        const res = await axios.get(route('admin.files.list'))
        archives.value = res.data.archives || []
    } catch (error) {
        console.error('Failed to load archives:', error)
        toast.error(t('failedToLoadBackups'))
        archives.value = []
    }
}

// Создать архив сайта поэтапно
const createArchive = async () => {
    if (isProcessing.value) return

    startProcess('Создание резервной копии сайта')
    addProcessMessage('Подготовка списка файлов...')

    try {
        const startResponse = await axios.post(route('admin.files.start'))
        const job = startResponse.data.job

        updateProcessState(startResponse.data, 'Список файлов подготовлен')

        // eslint-disable-next-line no-constant-condition
        while (true) {
            const response = await axios.post(route('admin.files.process'), { job })
            const data = response.data

            updateProcessState(data, 'Архивирование продолжается...')

            if (data.status === 'done') {
                progress.value = 100
                processStatus.value = 'done'

                addProcessMessage('Архив успешно создан')
                toast.success(t('archiveCreated'))

                await fetchArchives()

                setTimeout(() => {
                    resetProcess()
                }, 3000)

                break
            }

            if (data.status === 'error') {
                processStatus.value = 'error'

                addProcessMessage(data.message || 'Ошибка создания архива')
                toast.error(data.message || t('archiveCreateFailed'))

                break
            }
        }
    } catch (error) {
        const message = error?.response?.data?.message || t('archiveCreateFailed')

        console.error('Archive create error:', error)
        console.log('Archive error data:', error?.response?.data)

        processStatus.value = 'error'
        addProcessMessage(message)
        toast.error(message)
    }
}

// Удалить архив
const handleDelete = async (filename) => {
    if (!filename || !confirm(t('backupConfirmDeleteBackup'))) return

    startProcess('Удаление архива', filename)

    progress.value = 50
    addProcessMessage(`Удаление архива: ${filename}`)

    try {
        await axios.delete(route('admin.files.delete'), {
            data: { file: filename },
        })

        progress.value = 100
        processStatus.value = 'done'

        addProcessMessage('Архив успешно удалён')
        toast.success(t('backupDeleted'))

        await fetchArchives()
    } catch (error) {
        const message = error?.response?.data?.message || t('backupDeleteFailed')

        processStatus.value = 'error'
        addProcessMessage(message)
        toast.error(message)
    } finally {
        setTimeout(() => {
            resetProcess()
        }, 1500)
    }
}

// Скачать архив
const downloadArchive = (filename) => {
    window.open(route('admin.files.download', { file: filename }), '_blank')
}

// Первичная загрузка архивов
onMounted(fetchArchives)
</script>

<template>
    <AdminLayout :title="t('fileBackup')">
        <template #header>
            <TitlePage>
                {{ t('fileBackup') }}
            </TitlePage>
        </template>

        <div class="px-2 py-2 w-full max-w-12xl mx-auto">
            <div
                class="p-4 bg-slate-50 dark:bg-slate-700 border border-blue-400 dark:border-blue-200
                       overflow-hidden shadow-md shadow-gray-500 dark:shadow-slate-400
                       bg-opacity-95 dark:bg-opacity-95"
            >
                <div class="sm:flex sm:justify-between sm:items-center mb-4">
                    <button
                        @click="createArchive"
                        :disabled="isProcessing"
                        class="flex items-center btn px-2 py-0.5 bg-sky-600
                               text-white text-sm font-semibold rounded-sm shadow-md
                               transition-colors duration-300 ease-in-out
                               hover:bg-sky-700 focus:bg-sky-700 focus:outline-none
                               disabled:opacity-50 disabled:cursor-not-allowed"
                    >
                        <svg class="w-4 h-4 fill-current opacity-50 shrink-0 mr-1" viewBox="0 0 16 16">
                            <path
                                d="M15 7H9V1c0-.6-.4-1-1-1S7 .4 7 1v6H1c-.6 0-1 .4-1 1s.4 1 1 1h6v6c0 .6.4 1 1 1s1-.4 1-1V9h6c.6 0 1-.4 1-1s-.4-1-1-1z" />
                        </svg>
                        {{ t('createArchive') }}
                    </button>
                </div>

                <div
                    v-if="isProcessing || processStatus"
                    class="mb-4 rounded border border-blue-300 dark:border-blue-200
                           bg-white dark:bg-slate-800 shadow p-4"
                >
                    <div class="flex items-center justify-between mb-2">
                        <div>
                            <h3 class="text-sm font-semibold text-slate-700 dark:text-slate-100">
                                {{ processTitle }}
                            </h3>

                            <div class="text-xs text-slate-500 dark:text-slate-300">
                                {{ currentArchive }}
                            </div>
                        </div>

                        <span
                            class="px-2 py-0.5 rounded text-xs font-semibold"
                            :class="{
                                'bg-blue-100 text-blue-700': processStatus === 'processing',
                                'bg-green-100 text-green-700': processStatus === 'done',
                                'bg-red-100 text-red-700': processStatus === 'error',
                            }"
                        >
    {{ processStatus === 'done' ? 'Готово' : processStatus === 'error' ? 'Ошибка' : 'В процессе' }}
                        </span>
                    </div>

                    <div class="mb-2 text-sm text-blue-700 dark:text-blue-200 font-medium">
                        {{ processMessage }}
                    </div>

                    <div class="w-full h-4 bg-gray-300 dark:bg-gray-600 rounded-full overflow-hidden">
                        <div
                            class="h-full bg-blue-600 transition-all duration-500"
                            :class="{
                                'bg-green-600': processStatus === 'done',
                                'bg-red-600': processStatus === 'error',
                            }"
                            :style="{ width: `${progress}%` }"
                        />
                    </div>

                    <div class="flex justify-between mt-1 text-xs text-slate-600 dark:text-slate-300">
                        <span>{{ progress }}%</span>

                        <span v-if="totalFiles">
                            {{ processedFiles }} / {{ totalFiles }} файлов
                        </span>
                    </div>

                    <div
                        v-if="processLog.length"
                        class="mt-3 max-h-36 overflow-y-auto border rounded bg-slate-50 dark:bg-slate-900"
                    >
                        <div
                            v-for="(item, index) in processLog"
                            :key="index"
                            class="px-2 py-1 text-xs border-b border-slate-200 dark:border-slate-700
                                   text-slate-700 dark:text-slate-200"
                        >
                            <span class="font-semibold text-sky-700 dark:text-sky-300">
                                {{ item.time }}
                            </span>
                            —
                            <span>{{ item.message }}</span>
                        </div>
                    </div>
                </div>

                <div>
                    <h2 class="text-slate-700 dark:text-slate-200 text-center text-md font-semibold mb-2">
                        {{ t('availableArchives') }}
                    </h2>

                    <ul
                        v-if="Array.isArray(archives) && archives.length"
                        class="divide-y border rounded"
                    >
                        <li
                            v-for="archive in archives"
                            :key="archive.name"
                            class="flex items-center justify-between px-3 py-1
                                   bg-gray-100 hover:bg-gray-50
                                   dark:bg-gray-700 dark:hover:bg-slate-800"
                        >
                            <div>
                                <div class="font-medium text-sm text-amber-700 dark:text-amber-200">
                                    {{ archive.name }}
                                </div>

                                <div class="text-xs text-gray-500 dark:text-gray-400">
                                    {{ (archive.size / 1024 / 1024).toFixed(2) }} MB
                                </div>
                            </div>

                            <div class="flex items-center space-x-2">
                                <button
                                    @click="downloadArchive(archive.name)"
                                    :title="t('download')"
                                    class="w-8 h-8 flex items-center justify-center rounded-sm
                                           border border-slate-400 dark:border-slate-200
                                           bg-indigo-100 hover:bg-indigo-200
                                           dark:bg-indigo-700 dark:hover:bg-indigo-500
                                           text-indigo-600 dark:text-slate-100 transition"
                                >
                                    <svg class="w-6 h-6 fill-current" viewBox="0 0 24 24">
                                        <path d="M5 20h14v-2H5m14-9h-4V3H9v6H5l7 7 7-7z" />
                                    </svg>
                                </button>

                                <button
                                    @click="handleDelete(archive.name)"
                                    :disabled="isProcessing"
                                    :title="t('remove')"
                                    class="w-8 h-8 flex items-center justify-center rounded-sm
                                           border border-slate-400 dark:border-slate-200
                                           bg-red-200 hover:bg-red-300 dark:bg-red-700
                                           dark:hover:bg-red-600 text-red-600 dark:text-slate-100
                                           transition disabled:opacity-50 disabled:cursor-not-allowed"
                                >
                                    <svg class="w-4 h-4 fill-current" viewBox="0 0 16 16">
                                        <path
                                            d="M5 7h2v6H5V7zm4 0h2v6H9V7zm3-6v2h4v2h-1v10c0 .6-.4 1-1 1H2c-.6 0-1-.4-1-1V5H0V3h4V1c0-.6.4-1 1-1h6c.6 0 1 .4 1 1zM6 2v1h4V2H6zm7 3H3v9h10V5z" />
                                    </svg>
                                </button>
                            </div>
                        </li>
                    </ul>

                    <div v-else class="text-gray-500 text-center">
                        {{ t('noData') }}
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
