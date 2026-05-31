<script setup>
import { computed, ref, watch } from 'vue'
import { usePage, router } from '@inertiajs/vue3'
import { useI18n } from 'vue-i18n'

import AdminLayout from '@/Layouts/AdminLayout.vue'
import TitlePage from '@/Components/Admin/UI/Headlines/TitlePage.vue'
import CodeMirrorEditor from '@/Components/Admin/UI/CodeMirrorEditor/CodeMirrorEditor.vue'

const { t } = useI18n()

const { props } = usePage()

const fileContents = ref(props.fileContents || {})

const groups = computed(() => Object.keys(fileContents.value || {}))

const selectedGroup = ref(groups.value[0] || '')
const selectedFile = ref('')
const fileContent = ref('')
const errorMessage = ref('')
const successMessage = ref('')
const isSaving = ref(false)

const filesInSelectedGroup = computed(() => {
    if (!selectedGroup.value) {
        return {}
    }

    return fileContents.value[selectedGroup.value] || {}
})

const selectedFileShortName = computed(() => {
    if (!selectedFile.value) {
        return ''
    }

    return selectedFile.value.split('/').pop()
})

const selectGroup = (group) => {
    selectedGroup.value = group
    selectedFile.value = ''
    fileContent.value = ''
    errorMessage.value = ''
    successMessage.value = ''
}

const selectFile = (fileName) => {
    selectedFile.value = fileName
    fileContent.value = filesInSelectedGroup.value[fileName] || ''
    errorMessage.value = ''
    successMessage.value = ''
}

const saveChanges = () => {
    if (!selectedFile.value) {
        errorMessage.value = 'Сначала выберите файл для редактирования'
        return
    }

    errorMessage.value = ''
    successMessage.value = ''
    isSaving.value = true

    router.post(
        route('admin.components.save'),
        {
            fileName: selectedFile.value,
            fileContent: fileContent.value,
        },
        {
            preserveScroll: true,
            onSuccess: () => {
                successMessage.value = 'Файл успешно сохранён'

                if (
                    selectedGroup.value
                    && fileContents.value[selectedGroup.value]
                    && selectedFile.value
                ) {
                    fileContents.value[selectedGroup.value][selectedFile.value] = fileContent.value
                }
            },
            onError: () => {
                errorMessage.value = 'Не удалось сохранить изменения в файле'
            },
            onFinish: () => {
                isSaving.value = false
            },
        }
    )
}

watch(groups, (newGroups) => {
    if (!selectedGroup.value && newGroups.length) {
        selectedGroup.value = newGroups[0]
    }
})

const asideStorageKey = 'component-editor-aside-collapsed'

const isAsideCollapsed = ref(
    localStorage.getItem(asideStorageKey) === 'true'
)

const toggleAside = () => {
    isAsideCollapsed.value = !isAsideCollapsed.value
    localStorage.setItem(asideStorageKey, String(isAsideCollapsed.value))
}
</script>

<template>
    <AdminLayout :title="t('componentEditorHeader')">
        <template #header>
            <TitlePage>
                {{ t('componentEditorHeader') }}
            </TitlePage>
        </template>

        <div class="w-full px-2 py-3 sm:px-4 lg:px-6">
            <div
                class="mx-auto w-full max-w-7xl overflow-hidden rounded-xl
                       border border-blue-300 bg-slate-50 shadow-md
                       dark:border-blue-200 dark:bg-slate-800"
            >
                <div
                    class="border-b border-rose-200 bg-amber-50 px-3 py-2
                           text-center text-xs font-semibold italic text-rose-500
                           sm:text-sm"
                >
                    {{ t('componentEditorWarning') }}
                </div>

                <div class="flex justify-center border-b border-slate-200 bg-white p-3
                            dark:border-slate-700 dark:bg-slate-900">
                    <div class="flex gap-2 overflow-x-auto pb-1">
                        <button
                            v-for="group in groups"
                            :key="group"
                            type="button"
                            @click="selectGroup(group)"
                            class="shrink-0 rounded-sm border px-3 py-1 text-xs font-semibold
                                   transition-colors sm:text-sm"
                            :class="{
                                'border-blue-600 bg-blue-600 text-white shadow-sm': selectedGroup === group,
                                'border-slate-300 bg-slate-100 text-slate-700 hover:bg-slate-200 dark:border-slate-600 dark:bg-slate-700 dark:text-slate-100 dark:hover:bg-slate-600': selectedGroup !== group,
                            }"
                        >
                            {{ group }}
                        </button>
                    </div>
                </div>

                <div
                    class="grid grid-cols-1 gap-0 transition-all duration-300 lg:grid-cols-12"
                >
                    <aside
                        class="border-b border-slate-200 bg-slate-100 p-3
                               transition-all duration-300
                               dark:border-slate-700 dark:bg-slate-900
                               lg:border-b-0 lg:border-r"
                        :class="{
                            'lg:col-span-1': isAsideCollapsed,
                            'lg:col-span-4 xl:col-span-3': !isAsideCollapsed,
                        }"
                    >
                        <div
                            class="mb-3 flex items-center gap-2"
                            :class="{
                                'justify-center': isAsideCollapsed,
                                'justify-between': !isAsideCollapsed,
                            }"
                        >
                            <h2
                                v-if="!isAsideCollapsed"
                                class="text-sm font-bold text-slate-800 dark:text-slate-100"
                            >
                                Файлы
                            </h2>

                            <span
                                v-if="!isAsideCollapsed"
                                class="rounded-full bg-slate-300 px-2 py-0.5
                                       font-semibold text-xs text-slate-600
                                       dark:bg-slate-700 dark:text-slate-200"
                            >
                                {{ Object.keys(filesInSelectedGroup).length }}
                            </span>

                            <button
                                type="button"
                                @click="toggleAside"
                                class="inline-flex items-center justify-center
                                       rounded-sm border border-slate-300
                                       bg-white px-2 py-1 text-xs font-semibold
                                       text-slate-700 transition-colors hover:bg-slate-100
                                       dark:border-slate-600 dark:bg-slate-800
                                       dark:text-slate-100 dark:hover:bg-slate-700"
                                :class="{
                                    'mx-auto': isAsideCollapsed,
                                    'ml-auto': !isAsideCollapsed,
                                }"
                            >
                                <span v-if="isAsideCollapsed">
                                    ▶
                                </span>
                                <span v-else>
                                    ◀
                                </span>
                            </button>
                        </div>

                        <div
                            v-if="isAsideCollapsed"
                            class="hidden flex-col items-center gap-2 lg:flex"
                        >
                            <button
                                v-for="(content, fileName) in filesInSelectedGroup"
                                :key="fileName"
                                type="button"
                                @click="selectFile(fileName)"
                                :title="fileName"
                                class="flex h-8 w-8 items-center justify-center rounded-sm border
                                       text-xs font-bold transition-colors"
                                :class="{
                                    'border-teal-600 bg-teal-600 text-white': selectedFile === fileName,
                                    'border-slate-300 bg-white text-slate-700 hover:bg-teal-50 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-100': selectedFile !== fileName,
                                }"
                            >
                                {{ fileName.split('/').pop().slice(0, 1).toUpperCase() }}
                            </button>
                        </div>

                        <div
                            v-else-if="Object.keys(filesInSelectedGroup).length"
                            class="flex max-h-72 flex-col gap-2 overflow-y-auto pr-1 lg:max-h-[70vh]"
                        >
                            <button
                                v-for="(content, fileName) in filesInSelectedGroup"
                                :key="fileName"
                                type="button"
                                @click="selectFile(fileName)"
                                class="w-full rounded-sm border px-3 py-1 text-left text-xs
                                       transition-colors sm:text-sm"
                                :class="{
                                    'border-teal-600 bg-teal-600 text-white shadow-sm': selectedFile === fileName,
                                    'border-slate-300 bg-white text-slate-700 hover:border-teal-400 hover:bg-teal-50 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-100 dark:hover:bg-slate-700': selectedFile !== fileName,
                                }"
                            >
                                <span class="block truncate font-semibold">
                                    {{ fileName.split('/').pop() }}
                                </span>

                                <span class="mt-1 block truncate text-[11px] opacity-75">
                                    {{ fileName }}
                                </span>
                            </button>
                        </div>

                        <div
                            v-else
                            class="rounded-lg border border-dashed border-slate-300 p-4 text-center
                                   text-sm text-slate-500 dark:border-slate-600 dark:text-slate-300"
                        >
                            В этой группе нет доступных файлов
                        </div>
                    </aside>

                    <main
                        class="min-w-0 bg-slate-50 p-3 transition-all duration-300
                               dark:bg-slate-800"
                        :class="{
                            'lg:col-span-11': isAsideCollapsed,
                            'lg:col-span-8 xl:col-span-9': !isAsideCollapsed,
                        }"
                    >
                        <div
                            v-if="selectedFile"
                            class="overflow-hidden rounded-xl border border-slate-300 bg-white
                                   shadow-sm dark:border-slate-600 dark:bg-slate-900"
                        >
                            <div
                                class="flex flex-col gap-3 border-b border-slate-200 px-3 py-3
                                       dark:border-slate-700 sm:flex-row sm:items-center
                                       sm:justify-between"
                            >
                                <div class="min-w-0">
                                    <div class="text-xs font-semibold uppercase text-blue-500">
                                        {{ t('editingFile') }}
                                    </div>

                                    <h2 class="truncate text-sm font-bold text-slate-900
                                               dark:text-slate-100 sm:text-base">
                                        {{ selectedFileShortName }}
                                    </h2>

                                    <p class="mt-1 break-all text-xs text-slate-500
                                              dark:text-slate-400">
                                        {{ selectedFile }}
                                    </p>
                                </div>

                                <button
                                    type="button"
                                    @click="saveChanges"
                                    :disabled="isSaving"
                                    class="inline-flex items-center justify-center rounded-sm
                                           bg-teal-600 px-3 py-1 text-xs font-semibold text-white
                                           shadow-sm transition-colors hover:bg-teal-700
                                           disabled:cursor-not-allowed disabled:opacity-60"
                                >
                                    <svg
                                        class="mr-2 h-4 w-4 fill-current"
                                        viewBox="0 0 16 16"
                                    >
                                        <path
                                            d="M14.3 2.3L5 11.6 1.7 8.3c-.4-.4-1-.4-1.4 0-.4.4-.4 1 0 1.4l4 4c.2.2.4.3.7.3.3 0 .5-.1.7-.3l10-10c.4-.4.4-1 0-1.4-.4-.4-1-.4-1.4 0z"
                                        />
                                    </svg>

                                    <span v-if="isSaving">
                                        Сохранение...
                                    </span>

                                    <span v-else>
                                        {{ t('save') }}
                                    </span>
                                </button>
                            </div>

                            <div class="p-2 sm:p-3">
                                <div class="overflow-hidden rounded-lg border border-slate-700">
                                    <CodeMirrorEditor
                                        v-model="fileContent"
                                        theme="dark"
                                        class="min-h-[420px] w-full"
                                    />
                                </div>

                                <div
                                    v-if="errorMessage"
                                    class="mt-3 rounded-lg border border-red-300 bg-red-50 px-3 py-2
                                           text-sm text-red-700"
                                >
                                    {{ errorMessage }}
                                </div>

                                <div
                                    v-if="successMessage"
                                    class="mt-3 rounded-lg border border-emerald-300 bg-emerald-50
                                           px-3 py-2 text-sm text-emerald-700"
                                >
                                    {{ successMessage }}
                                </div>
                            </div>
                        </div>

                        <div
                            v-else
                            class="flex min-h-[420px] items-center justify-center rounded-xl
                                   border border-dashed border-slate-300 bg-white p-6 text-center
                                   dark:border-slate-600 dark:bg-slate-900"
                        >
                            <div>
                                <div class="text-base font-bold text-slate-700 dark:text-slate-100">
                                    Выберите файл
                                </div>

                                <p class="mt-2 text-sm text-slate-500 dark:text-slate-400">
                                    Сначала выберите группу, затем файл из списка.
                                </p>
                            </div>
                        </div>
                    </main>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>

<style>
canvas {
    border: 1px solid #ccc;
    margin-top: 20px;
}

/* кастомные стили редактора для тёмного режима */
.ͼo {
    color: #F2F2F2;
    background-color: #2B2B2B;
}

.ͼo .cm-gutters {
    background-color: #111111;
    color: #FFFFFF;
}

.cm-line .ͼq {
    color: #98ACBF;
}

.ͼo .cm-line .ͼf {
    color: #FF4747;
}

.ͼo .cm-line .ͼb {
    color: #FFFF00;
}

.ͼo .cm-line .ͼg {
    color: #24B87F;
}

.ͼo .cm-line .ͼe {
    color: #FFC46B;
}

.ͼo .cm-line .ͼm {
    color: #9F9F9E;
}

.ͼo .cm-line .ͼc {
    color: #9775A6;
}

.ͼo .cm-line .ͼv {
    color: #56b6c2;
}

.ͼo .cm-line .ͼr {
    color: #61afef;
}

.ͼo .cm-line .ͼd {
    color: #00CCFF;
}

.ͼo .cm-line .ͼi {
    color: #99CC00;
}

.cm-line {
    font-size: 13px;
}
</style>
