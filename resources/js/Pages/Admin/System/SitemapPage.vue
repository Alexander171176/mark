<script setup>
import { computed, ref, watch } from 'vue'
import { useForm } from '@inertiajs/vue3'
import { useI18n } from 'vue-i18n'
import { useToast } from 'vue-toastification'
import JSZip from 'jszip'

// @ts-ignore
import { saveAs } from 'file-saver'

import AdminLayout from '@/Layouts/AdminLayout.vue'
import TitlePage from '@/Components/Admin/UI/Headlines/TitlePage.vue'
import PrimaryButton from '@/Components/Admin/UI/Buttons/PrimaryButton.vue'

const { t } = useI18n()
const toast = useToast()

const props = defineProps({
    files: { type: Array, default: () => [] },
    selectedFile: { type: String, default: '' },
    content: { type: String, default: '' },
})

const form = useForm({})

const activeFile = ref(
    props.selectedFile || props.files.find(file => file.exists)?.name || ''
)

const activeContent = ref(props.content)

const contents = ref(
    activeFile.value
        ? { [activeFile.value]: props.content }
        : {}
)

const loading = ref(false)
const archiveProcessing = ref(false)

const currentFile = computed(() =>
    props.files.find(file => file.name === activeFile.value) || null
)

const currentFileExists = computed(() =>
    Boolean(currentFile.value?.exists)
)

const existingFiles = computed(() =>
    props.files.filter(file => file.exists)
)

const lineCount = computed(() => {
    if (!activeContent.value) return 0

    return activeContent.value.split(/\r\n|\r|\n/).length
})

const contentLines = computed(() => {
    if (!activeContent.value) return []

    return activeContent.value.split(/\r\n|\r|\n/)
})

const generate = () => {
    form.post(route('admin.sitemap.generate'), {
        preserveScroll: true,

        onSuccess: () => {
            contents.value = {}

            if (props.selectedFile) {
                activeFile.value = props.selectedFile
                activeContent.value = props.content
                contents.value[props.selectedFile] = props.content
            }

            toast.success(t('sitemapSuccess'))
        },

        onError: errors => {
            toast.error(errors?.sitemap || t('sitemapError'))
        },
    })
}

/** Загрузка выбранного XML. */
const selectFile = async (file) => {
    if (!file.exists) return

    activeFile.value = file.name

    if (
        Object.prototype.hasOwnProperty.call(
            contents.value,
            file.name
        )
    ) {
        activeContent.value = contents.value[file.name]
        return
    }

    loading.value = true

    try {
        const response = await fetch(
            route('admin.sitemap.content', { file: file.name }),
            {
                headers: { Accept: 'application/json' },
            }
        )

        if (!response.ok) {
            console.error(
                'Не удалось загрузить sitemap:',
                response.status
            )

            activeContent.value = ''
            toast.error(t('sitemapError'))

            return
        }

        const data = await response.json()

        contents.value[file.name] = data.content || ''
        activeContent.value = data.content || ''
    } catch (error) {
        console.error(
            'Не удалось загрузить sitemap:',
            error
        )

        activeContent.value = ''
        toast.error(t('sitemapError'))
    } finally {
        loading.value = false
    }
}

/** Скачать все существующие XML одним ZIP. */
const downloadArchive = async () => {
    if (!existingFiles.value.length) return

    archiveProcessing.value = true

    try {
        const zip = new JSZip()

        for (const file of existingFiles.value) {
            const response = await fetch(
                route('admin.sitemap.download', { file: file.name })
            )

            if (!response.ok) {
                console.error(
                    'Не удалось скачать sitemap:',
                    file.name,
                    response.status
                )

                toast.error(t('sitemapError'))

                return
            }

            const blob = await response.blob()

            zip.file(file.name, blob)
        }

        const archive = await zip.generateAsync({
            type: 'blob',
        })

        saveAs(archive, 'sitemaps.zip')
    } catch (error) {
        console.error(
            'Не удалось скачать архив sitemap:',
            error
        )

        toast.error(t('sitemapError'))
    } finally {
        archiveProcessing.value = false
    }
}

/** Обновление содержимого после генерации sitemap. */
watch(
    () => props.content,
    value => {
        if (!props.selectedFile) return

        contents.value[props.selectedFile] = value || ''

        if (activeFile.value === props.selectedFile) {
            activeContent.value = value || ''
        }
    }
)
</script>

<template>
    <AdminLayout :title="t('sitemapTitle')">
        <template #header>
            <TitlePage>{{ t('sitemapTitle') }}</TitlePage>
        </template>

        <div class="px-2 py-2 w-full max-w-12xl mx-auto">
            <div
                class="p-4 bg-slate-50 dark:bg-slate-700 border border-blue-400
                       dark:border-blue-200 overflow-hidden shadow-md shadow-gray-500
                       dark:shadow-slate-400 bg-opacity-95 dark:bg-opacity-95"
            >
                <form @submit.prevent="generate">
                    <div class="flex flex-wrap items-center justify-between gap-2">
                        <PrimaryButton :disabled="form.processing">
                            {{ t('generate') }}
                        </PrimaryButton>

                        <div class="flex flex-wrap items-center gap-2">
                            <a
                                v-if="currentFileExists"
                                :href="route('admin.sitemap.download', { file: activeFile })"
                                class="flex items-center btn px-2 py-0.5 bg-sky-600 text-white text-sm
                                       font-semibold rounded-sm shadow-md transition-colors duration-300
                                       ease-in-out hover:bg-sky-700 focus:bg-sky-700 focus:outline-none"
                            >
                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    viewBox="0 0 24 24"
                                    class="w-4 h-4 fill-current opacity-50 shrink-0"
                                >
                                    <path
                                        d="M22,15a1,1,0,0,0-1,1v4a1,1,0,0,1-1,1H4a1,1,0,0,1-1-1V16a1,1,0,0,0-2,0v4a3,3,0,0,0,3,3H20a3,3,0,0,0,3-3V16A1,1,0,0,0,22,15Z"
                                    />
                                    <path
                                        d="M11.232,17.64a1,1,0,0,0,1.536,0l5-6A1,1,0,0,0,17,10H13V2a1,1,0,0,0-2,0v8H7a1,1,0,0,0-.768,1.64Z"
                                    />
                                </svg>

                                <span class="ml-2">
                                    {{ t('download') }} {{ activeFile }}
                                </span>
                            </a>

                            <button
                                v-if="existingFiles.length"
                                type="button"
                                :disabled="archiveProcessing"
                                @click="downloadArchive"
                                class="flex items-center btn px-2 py-0.5 bg-slate-600 text-white text-sm
                                       font-semibold rounded-sm shadow-md transition-colors duration-300
                                       ease-in-out hover:bg-slate-700 focus:bg-slate-700 focus:outline-none
                                       disabled:opacity-50 disabled:cursor-not-allowed"
                            >
                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    viewBox="0 0 24 24"
                                    class="w-4 h-4 fill-current opacity-50 shrink-0"
                                >
                                    <path
                                        d="M22,15a1,1,0,0,0-1,1v4a1,1,0,0,1-1,1H4a1,1,0,0,1-1-1V16a1,1,0,0,0-2,0v4a3,3,0,0,0,3,3H20a3,3,0,0,0,3-3V16A1,1,0,0,0,22,15Z"
                                    />
                                    <path
                                        d="M11.232,17.64a1,1,0,0,0,1.536,0l5-6A1,1,0,0,0,17,10H13V2a1,1,0,0,0-2,0v8H7a1,1,0,0,0-.768,1.64Z"
                                    />
                                </svg>

                                <span class="ml-2">
                                    {{ archiveProcessing ? 'ZIP...' : 'ZIP' }}
                                </span>
                            </button>
                        </div>
                    </div>
                </form>

                <!-- Табы sitemap -->
                <div
                    v-if="files.length"
                    class="mt-4 flex flex-wrap gap-1 border-b border-gray-300 dark:border-gray-500"
                >
                    <button
                        v-for="file in files"
                        :key="file.key"
                        type="button"
                        :disabled="!file.exists"
                        @click="selectFile(file)"
                        :class="[
                            'px-3 py-1.5 text-xs font-semibold border border-b-0 rounded-t transition-colors duration-200',
                            activeFile === file.name
                                ? 'bg-sky-600 text-white border-sky-600'
                                : 'bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-200 border-gray-300 dark:border-gray-500 hover:bg-gray-200 dark:hover:bg-gray-700',
                            !file.exists ? 'opacity-40 cursor-not-allowed' : '',
                        ]"
                    >
                        {{ file.name }}
                    </button>
                </div>

                <!-- Информация о выбранном XML -->
                <div
                    v-if="currentFileExists"
                    class="mt-2 flex justify-center items-center gap-3"
                >
                    <span class="font-semibold text-red-500 dark:text-red-400 text-xl">
                            {{ activeFile }}
                        </span>

                    <span class="text-slate-700 dark:text-slate-300 text-[10px]">
                        Строк: {{ lineCount }}
                    </span>
                </div>

                <!-- XML -->
                <div
                    v-if="loading"
                    class="mt-2 p-4 text-sm text-slate-700 dark:text-slate-200"
                >
                    {{ t('downloadStatus') }}
                </div>

                <div
                    v-else
                    class="mt-2 h-[520px] overflow-auto border-2 border-gray-400 rounded
                           bg-gray-100 dark:bg-gray-800 text-xs font-mono"
                    >
                    <div
                        v-for="(line, index) in contentLines"
                        :key="index"
                        class="flex min-w-max leading-5"
                    >
                        <div
                            class="w-16 shrink-0 px-2 text-right select-none
                                   text-gray-400 dark:text-gray-500
                                   border-r border-gray-300 dark:border-gray-600
                                   bg-gray-200 dark:bg-gray-900"
                        >
                            {{ index + 1 }}
                        </div>

                        <pre
                            class="px-3 m-0 whitespace-pre font-semibold
                                   text-blue-800 dark:text-blue-200"
                        >
                            {{ line }}
                        </pre>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
