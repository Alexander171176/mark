<script setup>
import { ref, computed, onMounted } from 'vue'
import { useI18n } from 'vue-i18n'
import axios from 'axios'
import { useToast } from 'vue-toastification'

import AdminLayout from '@/Layouts/AdminLayout.vue'
import TitlePage from '@/Components/Admin/UI/Headlines/TitlePage.vue'

const { t } = useI18n()
const toast = useToast()

const isProcessing = ref(false)
const progress = ref(0)

const archives = ref([])

const processTitle = ref('')
const processMessage = ref('')
const processStatus = ref('')

const processedFiles = ref(0)
const totalFiles = ref(0)

const addedFiles = ref(0)
const skippedFiles = ref(0)

const archiveFiles = ref(0)
const archiveSize = ref(0)

const currentArchive = ref('')
const processLog = ref([])

const formattedArchiveSize = computed(() => {
    if (!archiveSize.value) {
        return ''
    }

    return `${(
        archiveSize.value /
        1024 /
        1024
    ).toFixed(2)} MB`
})

/*
|--------------------------------------------------------------------------
| Журнал процесса
|--------------------------------------------------------------------------
*/

const addProcessMessage = (message) => {
    if (!message) {
        return
    }

    processMessage.value = message

    processLog.value.unshift({
        time: new Date().toLocaleTimeString(),
        message,
    })

    if (processLog.value.length > 20) {
        processLog.value.pop()
    }
}

const addProcessErrors = (errors) => {
    if (!Array.isArray(errors)) {
        return
    }

    errors.forEach((error) => {
        if (error) {
            addProcessMessage(error)
        }
    })
}

/*
|--------------------------------------------------------------------------
| Состояние процесса
|--------------------------------------------------------------------------
*/

const resetProcess = () => {
    isProcessing.value = false
    progress.value = 0

    processTitle.value = ''
    processMessage.value = ''
    processStatus.value = ''

    processedFiles.value = 0
    totalFiles.value = 0

    addedFiles.value = 0
    skippedFiles.value = 0

    archiveFiles.value = 0
    archiveSize.value = 0

    currentArchive.value = ''
    processLog.value = []
}

const startProcess = (
    title,
    archiveName = ''
) => {
    isProcessing.value = true
    progress.value = 1

    processStatus.value = 'processing'
    processTitle.value = title
    processMessage.value = ''

    processedFiles.value = 0
    totalFiles.value = 0

    addedFiles.value = 0
    skippedFiles.value = 0

    archiveFiles.value = 0
    archiveSize.value = 0

    currentArchive.value = archiveName
    processLog.value = []
}

const updateProcessState = (
    data,
    fallbackMessage = ''
) => {
    progress.value =
        data.progress ??
        progress.value

    processStatus.value =
        data.status ||
        processStatus.value ||
        'processing'

    processedFiles.value =
        data.processed ??
        processedFiles.value

    totalFiles.value =
        data.total ??
        totalFiles.value

    addedFiles.value =
        data.added ??
        addedFiles.value

    skippedFiles.value =
        data.skipped ??
        skippedFiles.value

    archiveFiles.value =
        data.archive_files ??
        archiveFiles.value

    archiveSize.value =
        data.archive_size ??
        archiveSize.value

    currentArchive.value =
        data.filename ||
        currentArchive.value

    if (data.message) {
        addProcessMessage(
            data.message
        )
    } else if (
        fallbackMessage &&
        !processMessage.value
    ) {
        addProcessMessage(
            fallbackMessage
        )
    }

    if (
        Array.isArray(data.errors) &&
        data.errors.length
    ) {
        addProcessErrors(
            data.errors
        )
    }
}

/*
|--------------------------------------------------------------------------
| Список архивов
|--------------------------------------------------------------------------
*/

const fetchArchives = async () => {
    try {
        const response =
            await axios.get(
                route('admin.files.list')
            )

        archives.value =
            response.data.archives || []
    } catch (error) {
        console.error(
            'Failed to load archives:',
            error
        )

        archives.value = []

        toast.error(
            t('failedToLoadBackups')
        )
    }
}

/*
|--------------------------------------------------------------------------
| Создание архива
|--------------------------------------------------------------------------
*/

const createArchive = async () => {
    if (isProcessing.value) {
        return
    }

    startProcess(
        'Создание резервной копии сайта'
    )

    addProcessMessage(
        'Подготовка списка файлов...'
    )

    try {
        const startResponse =
            await axios.post(
                route(
                    'admin.files.start'
                )
            )

        const job =
            startResponse.data.job

        if (!job) {
            throw new Error(
                'Не получен идентификатор задачи архивации'
            )
        }

        updateProcessState(
            startResponse.data,
            'Список файлов подготовлен'
        )

        // eslint-disable-next-line no-constant-condition
        while (true) {
            const response =
                await axios.post(
                    route(
                        'admin.files.process'
                    ),
                    {
                        job,
                    }
                )

            const data =
                response.data

            updateProcessState(
                data,
                'Архивирование продолжается...'
            )

            if (
                data.status ===
                'done'
            ) {
                progress.value = 100
                processStatus.value = 'done'
                isProcessing.value = false

                addProcessMessage(
                    'Архив успешно создан'
                )

                toast.success(
                    t('archiveCreated')
                )

                await fetchArchives()

                setTimeout(
                    resetProcess,
                    5000
                )

                break
            }

            if (
                data.status ===
                'error'
            ) {
                processStatus.value = 'error'
                isProcessing.value = false

                toast.error(
                    data.message ||
                    t(
                        'archiveCreateFailed'
                    )
                )

                break
            }
        }
    } catch (error) {
        const data =
            error?.response?.data

        const message =
            data?.message ||
            error?.message ||
            t('archiveCreateFailed')

        processStatus.value = 'error'
        isProcessing.value = false

        addProcessMessage(
            message
        )

        if (
            Array.isArray(data?.errors)
        ) {
            addProcessErrors(
                data.errors
            )
        }

        toast.error(
            message
        )
    }
}

/*
|--------------------------------------------------------------------------
| Удаление архива
|--------------------------------------------------------------------------
*/

const handleDelete = async (
    filename
) => {
    if (!filename) {
        return
    }

    if (
        !confirm(
            t(
                'backupConfirmDeleteBackup'
            )
        )
    ) {
        return
    }

    startProcess(
        'Удаление архива',
        filename
    )

    progress.value = 50

    addProcessMessage(
        `Удаление архива: ${filename}`
    )

    try {
        await axios.delete(
            route(
                'admin.files.delete'
            ),
            {
                data: {
                    file: filename,
                },
            }
        )

        progress.value = 100
        processStatus.value = 'done'
        isProcessing.value = false

        addProcessMessage(
            'Архив успешно удалён'
        )

        toast.success(
            t('backupDeleted')
        )

        await fetchArchives()

        setTimeout(
            resetProcess,
            1500
        )
    } catch (error) {
        const message =
            error?.response?.data?.message ||
            t(
                'backupDeleteFailed'
            )

        processStatus.value = 'error'
        isProcessing.value = false

        addProcessMessage(
            message
        )

        toast.error(
            message
        )
    }
}

/*
|--------------------------------------------------------------------------
| Скачать архив
|--------------------------------------------------------------------------
*/

const downloadArchive = (
    filename
) => {
    window.open(
        route(
            'admin.files.download',
            {
                file: filename,
            }
        ),
        '_blank'
    )
}

/*
|--------------------------------------------------------------------------
| Lifecycle
|--------------------------------------------------------------------------
*/

onMounted(
    fetchArchives
)
</script>

<template>
    <AdminLayout :title="t('fileBackup')">
        <template #header>
            <TitlePage>
                {{ t('fileBackup') }}
            </TitlePage>
        </template>

        <div
            class="px-2 py-2 w-full
                   max-w-12xl mx-auto"
        >
            <div
                class="p-4
                       bg-slate-50
                       dark:bg-slate-700
                       border
                       border-blue-400
                       dark:border-blue-200
                       overflow-hidden
                       shadow-md
                       shadow-gray-500
                       dark:shadow-slate-400
                       bg-opacity-95
                       dark:bg-opacity-95"
            >
                <!-- Создание архива -->
                <div
                    class="sm:flex
                           sm:justify-between
                           sm:items-center
                           mb-4"
                >
                    <button
                        type="button"
                        :disabled="isProcessing"
                        class="flex items-center
                               btn px-2 py-0.5
                               bg-sky-600
                               text-white
                               text-sm
                               font-semibold
                               rounded-sm
                               shadow-md
                               transition-colors
                               duration-300
                               ease-in-out
                               hover:bg-sky-700
                               focus:bg-sky-700
                               focus:outline-none
                               disabled:opacity-50
                               disabled:cursor-not-allowed"
                        @click="createArchive"
                    >
                        <svg
                            class="w-4 h-4
                                   fill-current
                                   opacity-50
                                   shrink-0 mr-1"
                            viewBox="0 0 16 16"
                        >
                            <path
                                d="M15 7H9V1c0-.6-.4-1-1-1S7 .4 7 1v6H1c-.6 0-1 .4-1 1s.4 1 1 1h6v6c0 .6.4 1 1 1s1-.4 1-1V9h6c.6 0 1-.4 1-1s-.4-1-1-1z"
                            />
                        </svg>

                        {{ t('createArchive') }}
                    </button>
                </div>

                <!-- Процесс -->
                <div
                    v-if="
                        isProcessing ||
                        processStatus
                    "
                    class="mb-4
                           rounded
                           border
                           border-blue-300
                           dark:border-blue-200
                           bg-white
                           dark:bg-slate-800
                           shadow p-4"
                >
                    <div
                        class="flex items-center
                               justify-between
                               mb-2"
                    >
                        <div>
                            <h3
                                class="text-sm
                                       font-semibold
                                       text-slate-700
                                       dark:text-slate-100"
                            >
                                {{ processTitle }}
                            </h3>

                            <div
                                class="text-xs
                                       text-slate-500
                                       dark:text-slate-300"
                            >
                                {{ currentArchive }}
                            </div>
                        </div>

                        <span
                            class="px-2 py-0.5
                                   rounded
                                   text-xs
                                   font-semibold"
                            :class="{
                                'bg-blue-100 text-blue-700':
                                    processStatus === 'processing',

                                'bg-green-100 text-green-700':
                                    processStatus === 'done',

                                'bg-red-100 text-red-700':
                                    processStatus === 'error',
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

                    <div
                        class="mb-2
                               text-sm
                               font-medium"
                        :class="{
                            'text-blue-700 dark:text-blue-200':
                                processStatus !== 'error',

                            'text-red-700 dark:text-red-300':
                                processStatus === 'error',
                        }"
                    >
                        {{ processMessage }}
                    </div>

                    <!-- Прогресс -->
                    <div
                        class="w-full h-4
                               bg-gray-300
                               dark:bg-gray-600
                               rounded-full
                               overflow-hidden"
                    >
                        <div
                            class="h-full
                                   bg-blue-600
                                   transition-all
                                   duration-500"
                            :class="{
                                'bg-green-600':
                                    processStatus === 'done',

                                'bg-red-600':
                                    processStatus === 'error',
                            }"
                            :style="{
                                width: `${progress}%`,
                            }"
                        />
                    </div>

                    <div
                        class="flex justify-between
                               mt-1
                               text-xs
                               text-slate-600
                               dark:text-slate-300"
                    >
                        <span>
                            {{ progress }}%
                        </span>

                        <span v-if="totalFiles">
                            {{ processedFiles }}
                            /
                            {{ totalFiles }}
                            файлов
                        </span>
                    </div>

                    <!-- Статистика -->
                    <div
                        v-if="totalFiles"
                        class="grid
                               grid-cols-2
                               md:grid-cols-4
                               gap-2
                               mt-3
                               text-xs"
                    >
                        <div
                            class="p-2
                                   rounded
                                   bg-slate-100
                                   dark:bg-slate-700"
                        >
                            <div
                                class="text-slate-500
                                       dark:text-slate-300"
                            >
                                Всего
                            </div>

                            <div
                                class="font-semibold
                                       text-slate-700
                                       dark:text-slate-100"
                            >
                                {{ totalFiles }}
                            </div>
                        </div>

                        <div
                            class="p-2
                                   rounded
                                   bg-green-50
                                   dark:bg-green-900/30"
                        >
                            <div
                                class="text-green-700
                                       dark:text-green-300"
                            >
                                Добавлено
                            </div>

                            <div
                                class="font-semibold
                                       text-green-700
                                       dark:text-green-200"
                            >
                                {{ addedFiles }}
                            </div>
                        </div>

                        <div
                            class="p-2
                                   rounded
                                   bg-amber-50
                                   dark:bg-amber-900/30"
                        >
                            <div
                                class="text-amber-700
                                       dark:text-amber-300"
                            >
                                Пропущено
                            </div>

                            <div
                                class="font-semibold
                                       text-amber-700
                                       dark:text-amber-200"
                            >
                                {{ skippedFiles }}
                            </div>
                        </div>

                        <div
                            class="p-2
                                   rounded
                                   bg-sky-50
                                   dark:bg-sky-900/30"
                        >
                            <div
                                class="text-sky-700
                                       dark:text-sky-300"
                            >
                                В ZIP
                            </div>

                            <div
                                class="font-semibold
                                       text-sky-700
                                       dark:text-sky-200"
                            >
                                {{
                                    archiveFiles ||
                                    addedFiles
                                }}
                            </div>
                        </div>
                    </div>

                    <div
                        v-if="formattedArchiveSize"
                        class="mt-2
                               text-xs
                               text-slate-600
                               dark:text-slate-300"
                    >
                        <span class="font-semibold">
                            Размер архива:
                        </span>

                        {{ formattedArchiveSize }}
                    </div>

                    <!-- Журнал -->
                    <div
                        v-if="processLog.length"
                        class="mt-3
                               max-h-48
                               overflow-y-auto
                               border
                               rounded
                               bg-slate-50
                               dark:bg-slate-900"
                    >
                        <div
                            v-for="(item, index) in processLog"
                            :key="index"
                            class="px-2 py-1
                                   text-xs
                                   border-b
                                   border-slate-200
                                   dark:border-slate-700
                                   text-slate-700
                                   dark:text-slate-200
                                   whitespace-pre-wrap
                                   break-words"
                        >
                            <span
                                class="font-semibold
                                       text-sky-700
                                       dark:text-sky-300"
                            >
                                {{ item.time }}
                            </span>

                            —

                            <span>
                                {{ item.message }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Список архивов -->
                <div>
                    <h2
                        class="text-slate-700
                               dark:text-slate-200
                               text-center
                               text-md
                               font-semibold
                               mb-2"
                    >
                        {{ t('availableArchives') }}
                    </h2>

                    <ul
                        v-if="
                            Array.isArray(archives) &&
                            archives.length
                        "
                        class="divide-y
                               border
                               rounded"
                    >
                        <li
                            v-for="archive in archives"
                            :key="archive.name"
                            class="flex
                                   items-center
                                   justify-between
                                   px-3 py-1
                                   bg-gray-100
                                   hover:bg-gray-50
                                   dark:bg-gray-700
                                   dark:hover:bg-slate-800"
                        >
                            <div>
                                <div
                                    class="font-medium
                                           text-sm
                                           text-amber-700
                                           dark:text-amber-200"
                                >
                                    {{ archive.name }}
                                </div>

                                <div
                                    class="text-xs
                                           text-gray-500
                                           dark:text-gray-400"
                                >
                                    {{
                                        (
                                            archive.size /
                                            1024 /
                                            1024
                                        ).toFixed(2)
                                    }}
                                    MB
                                </div>
                            </div>

                            <div
                                class="flex
                                       items-center
                                       space-x-2"
                            >
                                <!-- Скачать -->
                                <button
                                    type="button"
                                    :title="t('download')"
                                    class="w-8 h-8
                                           flex items-center
                                           justify-center
                                           rounded-sm
                                           border
                                           border-slate-400
                                           dark:border-slate-200
                                           bg-indigo-100
                                           hover:bg-indigo-200
                                           dark:bg-indigo-700
                                           dark:hover:bg-indigo-500
                                           text-indigo-600
                                           dark:text-slate-100
                                           transition"
                                    @click="
                                        downloadArchive(
                                            archive.name
                                        )
                                    "
                                >
                                    <svg
                                        class="w-6 h-6
                                               fill-current"
                                        viewBox="0 0 24 24"
                                    >
                                        <path
                                            d="M5 20h14v-2H5m14-9h-4V3H9v6H5l7 7 7-7z"
                                        />
                                    </svg>
                                </button>

                                <!-- Удалить -->
                                <button
                                    type="button"
                                    :title="t('remove')"
                                    :disabled="isProcessing"
                                    class="w-8 h-8
                                           flex items-center
                                           justify-center
                                           rounded-sm
                                           border
                                           border-slate-400
                                           dark:border-slate-200
                                           bg-red-200
                                           hover:bg-red-300
                                           dark:bg-red-700
                                           dark:hover:bg-red-600
                                           text-red-600
                                           dark:text-slate-100
                                           transition
                                           disabled:opacity-50
                                           disabled:cursor-not-allowed"
                                    @click="
                                        handleDelete(
                                            archive.name
                                        )
                                    "
                                >
                                    <svg
                                        class="w-4 h-4
                                               fill-current"
                                        viewBox="0 0 16 16"
                                    >
                                        <path
                                            d="M5 7h2v6H5V7zm4 0h2v6H9V7zm3-6v2h4v2h-1v10c0 .6-.4 1-1 1H2c-.6 0-1-.4-1-1V5H0V3h4V1c0-.6.4-1 1-1h6c.6 0 0 1 1 1zM6 2v1h4V2H6zm7 3H3v9h10V5z"
                                        />
                                    </svg>
                                </button>
                            </div>
                        </li>
                    </ul>

                    <div
                        v-else
                        class="text-gray-500
                               text-center"
                    >
                        {{ t('noData') }}
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
