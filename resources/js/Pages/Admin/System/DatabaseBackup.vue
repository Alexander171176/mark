<script setup>
import { ref, computed, onMounted, onBeforeUnmount } from 'vue'
import { useI18n } from 'vue-i18n'
import axios from 'axios'
import { useToast } from 'vue-toastification'

import AdminLayout from '@/Layouts/AdminLayout.vue'
import TitlePage from '@/Components/Admin/UI/Headlines/TitlePage.vue'

const { t } = useI18n()
const toast = useToast()

const backups = ref([])
const isProcessing = ref(false)
const processTitle = ref('')
const processMessage = ref('')
const processStatus = ref('')
const processStep = ref(0)
const progress = ref(0)
const activeFile = ref('')
const elapsedSeconds = ref(0)
const processLog = ref([])

let timer = null
let pollTimer = null

const elapsedTime = computed(() => {
    const minutes = Math.floor(elapsedSeconds.value / 60)
    const seconds = elapsedSeconds.value % 60

    return `${minutes}:${seconds.toString().padStart(2, '0')}`
})

const stepLabel = computed(() => {
    if (processStep.value === 1) return 'Создание страховочного дампа'
    if (processStep.value === 2) return 'Восстановление выбранного дампа'
    if (processStep.value === 3) return 'Откат при ошибке'
    if (processStep.value === 4) return 'Завершено'

    return processTitle.value
})

const addLog = (message) => {
    processMessage.value = message

    processLog.value.unshift({
        time: new Date().toLocaleTimeString(),
        message,
    })

    if (processLog.value.length > 12) {
        processLog.value.pop()
    }
}

const startTimer = () => {
    clearInterval(timer)

    elapsedSeconds.value = 0

    timer = setInterval(() => {
        elapsedSeconds.value++
    }, 1000)
}

const stopTimer = () => {
    clearInterval(timer)
    timer = null
}

const clearPollTimer = () => {
    clearTimeout(pollTimer)
    pollTimer = null
}

const resetProcess = () => {
    isProcessing.value = false
    processTitle.value = ''
    processMessage.value = ''
    processStatus.value = ''
    processStep.value = 0
    progress.value = 0
    activeFile.value = ''
    elapsedSeconds.value = 0
    processLog.value = []

    stopTimer()
    clearPollTimer()
}

const startProcess = (title, file = '') => {
    isProcessing.value = true
    processTitle.value = title
    processMessage.value = ''
    processStatus.value = 'processing'
    processStep.value = 0
    progress.value = 3
    activeFile.value = file
    processLog.value = []

    startTimer()
}

const updateProcessFromStatus = (data) => {
    processStatus.value = data.status || 'processing'
    processStep.value = data.step || 0
    progress.value = data.progress || progress.value
    processMessage.value = data.message || processMessage.value

    if (data.message) {
        addLog(data.message)
    }
}

const fetchBackups = async () => {
    try {
        const response = await axios.get(route('admin.backup.list'))
        backups.value = response.data.backups || []
    } catch {
        toast.error(t('failedToLoadBackups'))
    }
}

const createBackup = async () => {
    if (isProcessing.value) return

    startProcess('Создание резервной копии БД')
    addLog('Запуск создания SQL-дампа')

    try {
        progress.value = 30

        const response = await axios.post(route('admin.backup.create'))

        progress.value = 100
        processStatus.value = 'done'

        addLog(response.data.message || 'Бэкап успешно создан')
        toast.success(t('backupCreated'))

        await fetchBackups()

        setTimeout(resetProcess, 2500)
    } catch (error) {
        processStatus.value = 'error'
        progress.value = 100

        const message = error?.response?.data?.message || t('backupFailed')

        addLog(message)
        toast.error(message)
    }
}

const handleRestore = async (filename) => {
    if (!filename) return

    if (!confirm(t('backupConfirmRestoreBackup'))) {
        return
    }

    startProcess('Восстановление базы данных', filename)
    addLog(`Запуск восстановления из файла: ${filename}`)

    try {
        const response = await axios.post(route('admin.backup.restore.start'), {
            file: filename,
        })

        updateProcessFromStatus(response.data)

        pollRestoreStatus(response.data.job)
    } catch (error) {
        processStatus.value = 'error'
        progress.value = 100

        const message = error?.response?.data?.message || t('backupRestoreFailed')

        addLog(message)
        toast.error(message)
        stopTimer()
    }
}

const pollRestoreStatus = async (job) => {
    try {
        const response = await axios.get(route('admin.backup.restore.status', { job }))
        const data = response.data

        updateProcessFromStatus(data)

        if (data.status === 'processing') {
            pollTimer = setTimeout(() => {
                pollRestoreStatus(job)
            }, 1500)

            return
        }

        stopTimer()

        if (data.status === 'done') {
            progress.value = 100

            addLog('Восстановление базы данных завершено')
            toast.success(t('backupRestored'))

            await fetchBackups()

            setTimeout(resetProcess, 4000)

            return
        }

        if (data.status === 'error') {
            progress.value = 100

            addLog(data.message || 'Ошибка восстановления')
            toast.error(data.message || t('backupRestoreFailed'))

            await fetchBackups()
        }
    } catch (error) {
        processStatus.value = 'error'
        progress.value = 100

        const message = error?.response?.data?.message || t('backupRestoreFailed')

        addLog(message)
        toast.error(message)
        stopTimer()
    }
}

const handleDelete = async (filename) => {
    if (!filename || !confirm(t('backupConfirmDeleteBackup'))) return

    startProcess('Удаление бэкапа', filename)
    progress.value = 50
    addLog(`Удаление файла: ${filename}`)

    try {
        await axios.delete(route('admin.backup.delete'), {
            data: { file: filename },
        })

        progress.value = 100
        processStatus.value = 'done'

        addLog('Бэкап успешно удалён')
        toast.success(t('backupDeleted'))

        await fetchBackups()

        setTimeout(resetProcess, 1500)
    } catch (error) {
        processStatus.value = 'error'
        progress.value = 100

        const message = error?.response?.data?.message || t('backupDeleteFailed')

        addLog(message)
        toast.error(message)
    }
}

onMounted(fetchBackups)

onBeforeUnmount(() => {
    stopTimer()
    clearPollTimer()
})
</script>

<template>
    <AdminLayout :title="t('databaseBackup')">
        <template #header>
            <TitlePage>
                {{ t('databaseBackup') }}
            </TitlePage>
        </template>

        <div class="px-2 py-2 w-full max-w-12xl mx-auto">
            <div class="p-4 bg-slate-50 dark:bg-slate-700 border border-blue-400 dark:border-blue-200
                        overflow-hidden shadow-md shadow-gray-500 dark:shadow-slate-400
                        bg-opacity-95 dark:bg-opacity-95">

                <div class="sm:flex sm:justify-between sm:items-center mb-4">
                    <button
                        @click="createBackup"
                        :disabled="isProcessing"
                        class="flex items-center btn px-2 py-0.5 bg-sky-600
                               text-white text-sm font-semibold rounded-sm shadow-md
                               transition-colors duration-300 ease-in-out
                               hover:bg-sky-700 focus:bg-sky-700 focus:outline-none
                               disabled:opacity-50 disabled:cursor-not-allowed">
                        <svg class="w-4 h-4 fill-current opacity-50 shrink-0 mr-1"
                             viewBox="0 0 16 16">
                            <path d="M15 7H9V1c0-.6-.4-1-1-1S7 .4 7 1v6H1c-.6 0-1 .4-1 1s.4 1 1 1h6v6c0 .6.4 1 1 1s1-.4 1-1V9h6c.6 0 1-.4 1-1s-.4-1-1-1z"/>
                        </svg>
                        {{ t('createBackup') }}
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
                                {{ activeFile }}
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
                            {{
                                processStatus === 'done'
                                    ? 'Готово'
                                    : processStatus === 'error'
                                        ? 'Ошибка'
                                        : 'В процессе'
                            }}
                        </span>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-2 mb-3 text-xs text-slate-600 dark:text-slate-300">
                        <div>
                            <span class="font-semibold">Этап:</span>
                            {{ stepLabel }}
                        </div>

                        <div>
                            <span class="font-semibold">Время:</span>
                            {{ elapsedTime }}
                        </div>

                        <div>
                            <span class="font-semibold">Прогресс:</span>
                            {{ progress }}%
                        </div>
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
                            :style="{ width: `${progress}%` }">
                        </div>
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

                <div class="mt-6">
                    <h2 class="text-slate-700 dark:text-slate-200 text-center text-md
                               font-semibold mb-2">
                        {{ t('availableBackups') }}
                    </h2>

                    <div v-if="backups.length === 0" class="text-gray-500 text-center">
                        {{ t('noData') }}
                    </div>

                    <ul v-else class="divide-y border rounded">
                        <li v-for="backup in backups" :key="backup.name"
                            class="flex items-center justify-between px-3 py-1
                                   bg-gray-100 hover:bg-gray-50
                                   dark:bg-gray-700 dark:hover:bg-slate-800">

                            <div>
                                <div class="font-medium text-sm text-amber-700 dark:text-amber-200">
                                    {{ backup.name }}
                                </div>
                                <div class="text-xs text-gray-500 dark:text-gray-400">
                                    {{ (backup.size / 1024).toFixed(1) }} KB
                                </div>
                            </div>

                            <div class="flex items-center space-x-2">
                                <a
                                    :href="route('admin.backup.download', { filename: backup.name })"
                                    :title="t('download')"
                                    class="w-8 h-8 flex items-center justify-center rounded-sm
                                           border border-slate-400 dark:border-slate-200
                                           bg-indigo-100 hover:bg-indigo-200
                                           dark:bg-indigo-700 dark:hover:bg-indigo-500
                                           text-indigo-600 dark:text-slate-100 transition"
                                >
                                    <svg class="w-6 h-6 fill-current" viewBox="0 0 24 24">
                                        <path d="M5 20h14v-2H5m14-9h-4V3H9v6H5l7 7 7-7z"/>
                                    </svg>
                                </a>

                                <button
                                    :title="t('recover')"
                                    @click="handleRestore(backup.name)"
                                    :disabled="isProcessing"
                                    class="w-8 h-8 flex items-center justify-center rounded-sm
                                           border border-slate-400 dark:border-slate-200
                                           bg-teal-200 hover:bg-teal-300 dark:bg-teal-700
                                           dark:hover:bg-teal-600 text-teal-600 dark:text-slate-100
                                           transition disabled:opacity-50 disabled:cursor-not-allowed">
                                    <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24">
                                        <path d="M12,10C8.2,10,4.3,9.3,2,7.6V12c0,2.7,5.2,4,10,4s10-1.3,10-4V7.6C19.7,9.3,15.8,10,12,10z"/>
                                        <path d="M12,18c-3.8,0-7.7-0.7-10-2.4V20c0,2.7,5.2,4,10,4s10-1.3,10-4v-4.4C19.7,17.3,15.8,18,12,18z"/>
                                        <path d="M12,0C7.2,0,2,1.3,2,4s5.2,4,10,4s10-1.3,10-4S16.8,0,12,0z"/>
                                    </svg>
                                </button>

                                <button
                                    :title="t('remove')"
                                    @click="handleDelete(backup.name)"
                                    :disabled="isProcessing"
                                    class="w-8 h-8 flex items-center justify-center rounded-sm
                                           border border-slate-400 dark:border-slate-200
                                           bg-red-200 hover:bg-red-300 dark:bg-red-700
                                           dark:hover:bg-red-600 text-red-600 dark:text-slate-100
                                           transition disabled:opacity-50 disabled:cursor-not-allowed">
                                    <svg class="w-4 h-4 fill-current" viewBox="0 0 16 16">
                                        <path d="M5 7h2v6H5V7zm4 0h2v6H9V7zm3-6v2h4v2h-1v10c0 .6-.4 1-1 1H2c-.6 0-1-.4-1-1V5H0V3h4V1c0-.6.4-1 1-1h6c.6 0 0 1 1 1zM6 2v1h4V2H6zm7 3H3v9h10V5z"/>
                                    </svg>
                                </button>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
