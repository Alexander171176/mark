<script setup>
import { computed } from 'vue'
import { useForm } from '@inertiajs/vue3'
import { useToast } from 'vue-toastification'
import { useI18n } from 'vue-i18n'

import AdminLayout from '@/Layouts/AdminLayout.vue'
import TitlePage from '@/Components/Admin/UI/Headlines/TitlePage.vue'
import PrimaryButton from '@/Components/Admin/UI/Buttons/PrimaryButton.vue'

const { t } = useI18n()
const toast = useToast()

const props = defineProps({
    content: {
        type: String,
        default: '',
    },
    groups: {
        type: Array,
        default: () => [],
    },
    sitemaps: {
        type: Array,
        default: () => [],
    },
    blockAll: {
        type: Boolean,
        default: false,
    },
})

const createDefaultGroup = () => ({
    user_agents: ['*'],
    allow: [],
    disallow: [],
    clean_params: [],
})

const form = useForm({
    editor_mode: 'builder',
    content: props.content,
    block_all: props.blockAll,
    groups: props.groups?.length
        ? JSON.parse(JSON.stringify(props.groups))
        : [createDefaultGroup()],
    sitemaps: props.sitemaps?.length
        ? [...props.sitemaps]
        : [],
})

const cleanArray = (items) => {
    return [
        ...new Set(
            (items || [])
                .map(item => String(item || '').trim())
                .filter(Boolean)
        ),
    ]
}

const preview = computed(() => {
    const lines = [
        '# robots.txt',
        '# Generated from admin panel',
        '',
    ]

    if (form.block_all) {
        lines.push('User-agent: *')
        lines.push('Disallow: /')
    } else {
        form.groups.forEach((group) => {
            let userAgents = cleanArray(group.user_agents || [])

            if (!userAgents.length) {
                userAgents = ['*']
            }

            userAgents.forEach(userAgent => {
                lines.push(`User-agent: ${userAgent}`)
            })

            cleanArray(group.disallow || []).forEach(path => {
                lines.push(`Disallow: ${path}`)
            })

            cleanArray(group.allow || []).forEach(path => {
                lines.push(`Allow: ${path}`)
            })

            ;(group.clean_params || []).forEach((item) => {
                const params = String(item.params || '').trim()
                const path = String(item.path || '').trim()

                if (!params) {
                    return
                }

                let value = `Clean-param: ${params}`

                if (path) {
                    value += ` ${path}`
                }

                lines.push(value)
            })

            lines.push('')
        })
    }

    const sitemaps = cleanArray(form.sitemaps || [])

    if (sitemaps.length) {
        if (lines.length && lines[lines.length - 1] !== '') {
            lines.push('')
        }

        sitemaps.forEach(sitemap => {
            lines.push(`Sitemap: ${sitemap}`)
        })
    }

    while (lines.length && lines[lines.length - 1] === '') {
        lines.pop()
    }

    return lines.join('\n')
})

const setEditorMode = (mode) => {
    if (mode === 'manual' && form.editor_mode === 'builder') {
        form.content = preview.value
    }

    form.editor_mode = mode
}

const addGroup = () => {
    form.groups.push(createDefaultGroup())
}

const removeGroup = (groupIndex) => {
    if (form.groups.length <= 1) {
        return
    }

    form.groups.splice(groupIndex, 1)
}

const addUserAgent = (group) => {
    group.user_agents.push('')
}

const removeUserAgent = (group, index) => {
    if (group.user_agents.length <= 1) {
        return
    }

    group.user_agents.splice(index, 1)
}

const addDisallow = (group) => {
    group.disallow.push('')
}

const removeDisallow = (group, index) => {
    group.disallow.splice(index, 1)
}

const addAllow = (group) => {
    group.allow.push('')
}

const removeAllow = (group, index) => {
    group.allow.splice(index, 1)
}

const addCleanParam = (group) => {
    group.clean_params.push({
        params: '',
        path: '',
    })
}

const removeCleanParam = (group, index) => {
    group.clean_params.splice(index, 1)
}

const addSitemap = () => {
    form.sitemaps.push('')
}

const removeSitemap = (index) => {
    form.sitemaps.splice(index, 1)
}

const submit = () => {
    form.put(route('admin.robot.update'), {
        preserveScroll: true,
        onSuccess: () => toast.success(t('robotSuccess')),
        onError: () => toast.error(t('robotError')),
    })
}
</script>

<template>
    <AdminLayout :title="t('robotTitle')">
        <template #header>
            <TitlePage>{{ t('robotTitle') }}</TitlePage>
        </template>

        <div class="px-2 py-2 w-full max-w-12xl mx-auto">
            <div
                class="p-4 bg-slate-50 dark:bg-slate-700
                       border border-blue-400 dark:border-blue-200
                       overflow-hidden shadow-md shadow-gray-500
                       dark:shadow-slate-400 bg-opacity-95 dark:bg-opacity-95">

                <!-- Информация -->
                <div
                    class="mb-4 px-4 py-3 bg-sky-50 dark:bg-sky-900/20
                           border border-sky-300 dark:border-sky-700 rounded">
                    <div class="text-sm font-semibold text-sky-800 dark:text-sky-200">
                        Генератор robots.txt
                    </div>

                    <div class="mt-1 text-xs text-sky-700 dark:text-sky-300">
                        Используйте конструктор для стандартных правил или ручной редактор
                        для полного контроля над содержимым файла.
                    </div>
                </div>

                <form class="space-y-4" @submit.prevent="submit">

                    <!-- Режим редактора -->
                    <div class="flex justify-end">
                        <div
                            class="inline-flex p-1 bg-slate-200 dark:bg-slate-800
                                   border border-slate-400 dark:border-slate-500 rounded">
                            <button
                                type="button"
                                class="px-4 py-1.5 text-sm rounded transition"
                                :class="form.editor_mode === 'builder'
                                    ? 'bg-white dark:bg-slate-600 text-sky-700 ' +
                                     'dark:text-sky-200 shadow'
                                    : 'text-slate-600 dark:text-slate-300'"
                                @click="setEditorMode('builder')"
                            >
                                Конструктор
                            </button>

                            <button
                                type="button"
                                class="px-4 py-1.5 text-sm rounded transition"
                                :class="form.editor_mode === 'manual'
                                    ? 'bg-white dark:bg-slate-600 text-sky-700 ' +
                                     'dark:text-sky-200 shadow'
                                    : 'text-slate-600 dark:text-slate-300'"
                                @click="setEditorMode('manual')"
                            >
                                Редактор
                            </button>
                        </div>
                    </div>

                    <!-- Конструктор -->
                    <div v-if="form.editor_mode === 'builder'" class="space-y-4">

                        <!-- Индексация -->
                        <div
                            class="p-4 bg-white dark:bg-slate-800
                                   border border-slate-400 dark:border-slate-500 rounded">
                            <div class="text-center text-sm font-semibold
                                        text-slate-800 dark:text-slate-100">
                                Индексация сайта
                            </div>

                            <div class="mt-3 flex justify-center items-center gap-6">
                                <label class="inline-flex items-center gap-2 cursor-pointer">
                                    <input
                                        v-model="form.block_all"
                                        :value="false"
                                        type="radio"
                                    >

                                    <span class="text-sm text-green-700 dark:text-green-400">
                                        Разрешена
                                    </span>
                                </label>

                                <label class="inline-flex items-center gap-2 cursor-pointer">
                                    <input
                                        v-model="form.block_all"
                                        :value="true"
                                        type="radio"
                                    >

                                    <span class="text-sm text-red-600 dark:text-red-400">
                                        Запрещена полностью
                                    </span>
                                </label>
                            </div>

                            <div
                                v-if="form.block_all"
                                class="mt-3 px-3 py-2 text-center text-xs
                                       bg-red-50 dark:bg-red-900/20 border border-red-300
                                       dark:border-red-700 text-red-700 dark:text-red-300 rounded"
                            >
                                Будет сформировано правило
                                <span class="font-mono font-semibold">Disallow: /</span>.
                                Поисковым роботам будет запрещено сканирование всего сайта.
                            </div>
                        </div>

                        <!-- Группы -->
                        <div v-if="!form.block_all" class="space-y-3">
                            <div class="flex items-center justify-between gap-3">
                                <div>
                                    <div class="text-sm font-semibold
                                                text-slate-800 dark:text-slate-100">
                                        Группы роботов
                                    </div>

                                    <div class="text-xs text-slate-500 dark:text-slate-400">
                                        Для каждой группы можно задать отдельные правила.
                                    </div>
                                </div>

                                <button
                                    type="button"
                                    class="inline-flex items-center gap-1 px-3 py-1
                                           text-xs font-medium text-white bg-sky-600
                                           hover:bg-sky-700 border border-sky-700
                                           dark:border-sky-500 rounded-sm shadow-sm
                                           transition-colors"
                                    @click="addGroup"
                                >
                                    <span class="text-sm leading-none">+</span>
                                    Добавить группу
                                </button>
                            </div>

                            <div
                                v-for="(group, groupIndex) in form.groups"
                                :key="groupIndex"
                                class="p-4 bg-white dark:bg-slate-800 border
                                       border-slate-400 dark:border-slate-500 rounded"
                            >
                                <!-- Заголовок группы -->
                                <div class="flex items-center justify-between gap-3 mb-4">
                                    <div class="text-sm font-semibold
                                                text-slate-800 dark:text-slate-100">
                                        Группа {{ groupIndex + 1 }}
                                    </div>

                                    <button
                                        v-if="form.groups.length > 1"
                                        type="button"
                                        class="inline-flex items-center gap-1 px-2.5 py-1
                                               text-xs font-medium text-amber-700 dark:text-amber-300
                                               bg-amber-50 dark:bg-amber-900/30 hover:bg-amber-100
                                               dark:hover:bg-amber-900/50 border border-amber-300
                                               dark:border-amber-700 rounded-sm shadow-sm
                                               transition-colors"
                                        @click="removeGroup(groupIndex)"
                                    >
                                        <span class="text-sm leading-none">×</span>
                                        Удалить группу
                                    </button>
                                </div>

                                <!-- User-agent -->
                                <div class="mb-4">
                                    <div class="flex items-center justify-between mb-2">
                                        <label class="text-sm font-medium text-slate-700
                                                      dark:text-slate-200">
                                            User-agent
                                        </label>

                                        <button
                                            type="button"
                                            class="inline-flex items-center gap-1 px-2.5 py-1
                                                   text-xs font-medium text-sky-700
                                                   dark:text-sky-200 bg-sky-50 dark:bg-sky-900/40
                                                   hover:bg-sky-100 dark:hover:bg-sky-800/60
                                                   border border-sky-300 dark:border-sky-600
                                                   rounded-sm shadow-sm transition-colors"
                                            @click="addUserAgent(group)"
                                        >
                                            <span class="text-sm leading-none">+</span>
                                            Добавить
                                        </button>
                                    </div>

                                    <div class="space-y-2">
                                        <div
                                            v-for="(userAgent, index) in group.user_agents"
                                            :key="index"
                                            class="flex items-center gap-2"
                                        >
                                            <input
                                                v-model="group.user_agents[index]"
                                                type="text"
                                                placeholder="*"
                                                class="w-full px-3 py-1 text-sm font-mono
                                                       border border-slate-400 dark:border-slate-500
                                                       rounded-sm bg-white dark:bg-slate-900
                                                       text-slate-800 dark:text-slate-100"
                                            >

                                            <button
                                                v-if="group.user_agents.length > 1"
                                                type="button"
                                                title="Удалить"
                                                class="flex-none w-8 h-8 inline-flex items-center
                                                       justify-center text-base font-semibold
                                                       text-amber-600 dark:text-amber-300 bg-amber-50
                                                       dark:bg-amber-900/30 hover:bg-amber-100
                                                       dark:hover:bg-amber-900/50 border
                                                       border-amber-300 dark:border-amber-700
                                                       rounded-sm shadow-sm transition-colors"
                                                @click="removeUserAgent(group, index)"
                                            >
                                                ×
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <!-- Disallow / Allow -->
                                <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 mb-4">

                                    <!-- Disallow -->
                                    <div>
                                        <div class="flex items-center justify-between mb-2">
                                            <label class="text-sm font-medium text-slate-700
                                                          dark:text-slate-200">
                                                Disallow
                                            </label>

                                            <button
                                                type="button"
                                                class="inline-flex items-center gap-1 px-2.5 py-1
                                                       text-xs font-medium text-sky-700
                                                       dark:text-sky-200 bg-sky-50
                                                       dark:bg-sky-900/40 hover:bg-sky-100
                                                       dark:hover:bg-sky-800/60 border
                                                       border-sky-300 dark:border-sky-600
                                                       rounded-sm shadow-sm transition-colors"
                                                @click="addDisallow(group)"
                                            >
                                                <span class="text-sm leading-none">+</span>
                                                Добавить
                                            </button>
                                        </div>

                                        <div v-if="!group.disallow.length"
                                             class="text-xs text-slate-400">
                                            Запрещённых путей нет.
                                        </div>

                                        <div class="space-y-2">
                                            <div
                                                v-for="(value, index) in group.disallow"
                                                :key="index"
                                                class="flex items-center gap-2"
                                            >
                                                <input
                                                    v-model="group.disallow[index]"
                                                    type="text"
                                                    placeholder="/admin/"
                                                    class="w-full px-3 py-1 text-sm font-mono
                                                           border border-slate-400
                                                           dark:border-slate-500 rounded-sm
                                                           bg-white dark:bg-slate-900
                                                           text-slate-800 dark:text-slate-100"
                                                >

                                                <button
                                                    type="button"
                                                    title="Удалить"
                                                    class="flex-none w-8 h-8 inline-flex
                                                           items-center justify-center text-base
                                                           font-semibold text-amber-600
                                                           dark:text-amber-300 bg-amber-50
                                                           dark:bg-amber-900/30 hover:bg-amber-100
                                                           dark:hover:bg-amber-900/50 border
                                                           border-amber-300 dark:border-amber-700
                                                           rounded-sm shadow-sm transition-colors"
                                                    @click="removeDisallow(group, index)"
                                                >
                                                    ×
                                                </button>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Allow -->
                                    <div>
                                        <div class="flex items-center justify-between mb-2">
                                            <label class="text-sm font-medium text-slate-700
                                                          dark:text-slate-200">
                                                Allow
                                            </label>

                                            <button
                                                type="button"
                                                class="inline-flex items-center gap-1 px-2.5 py-1
                                                       text-xs font-medium text-sky-700
                                                       dark:text-sky-200 bg-sky-50
                                                       dark:bg-sky-900/40 hover:bg-sky-100
                                                       dark:hover:bg-sky-800/60 border
                                                       border-sky-300 dark:border-sky-600
                                                       rounded-sm shadow-sm transition-colors"
                                                @click="addAllow(group)"
                                            >
                                                <span class="text-sm leading-none">+</span>
                                                Добавить
                                            </button>
                                        </div>

                                        <div v-if="!group.allow.length"
                                             class="text-xs text-slate-400">
                                            Явных разрешений нет.
                                        </div>

                                        <div class="space-y-2">
                                            <div
                                                v-for="(value, index) in group.allow"
                                                :key="index"
                                                class="flex items-center gap-2"
                                            >
                                                <input
                                                    v-model="group.allow[index]"
                                                    type="text"
                                                    placeholder="/public/"
                                                    class="w-full px-3 py-1 text-sm font-mono
                                                           border border-slate-400
                                                           dark:border-slate-500 rounded-sm
                                                           bg-white dark:bg-slate-900
                                                           text-slate-800 dark:text-slate-100"
                                                >

                                                <button
                                                    type="button"
                                                    title="Удалить"
                                                    class="flex-none w-8 h-8 inline-flex
                                                           items-center justify-center text-base
                                                           font-semibold text-amber-600
                                                           dark:text-amber-300 bg-amber-50
                                                           dark:bg-amber-900/30 hover:bg-amber-100
                                                           dark:hover:bg-amber-900/50 border
                                                           border-amber-300 dark:border-amber-700
                                                           rounded-sm shadow-sm transition-colors"
                                                    @click="removeAllow(group, index)"
                                                >
                                                    ×
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Clean-param -->
                                <div>
                                    <div class="flex items-center justify-between mb-2">
                                        <div>
                                            <label class="text-sm font-medium text-slate-700
                                                           dark:text-slate-200">
                                                Clean-param
                                            </label>

                                            <div class="text-xs text-slate-400">
                                                Дополнительная директива Yandex.
                                            </div>
                                        </div>

                                        <button
                                            type="button"
                                            class="inline-flex items-center gap-1 px-2.5 py-1
                                                   text-xs font-medium text-sky-700
                                                   dark:text-sky-200 bg-sky-50 dark:bg-sky-900/40
                                                   hover:bg-sky-100 dark:hover:bg-sky-800/60 border
                                                   border-sky-300 dark:border-sky-600 rounded-sm
                                                   shadow-sm transition-colors"
                                            @click="addCleanParam(group)"
                                        >
                                            <span class="text-sm leading-none">+</span>
                                            Добавить
                                        </button>
                                    </div>

                                    <div class="space-y-2">
                                        <div
                                            v-for="(item, index) in group.clean_params"
                                            :key="index"
                                            class="grid grid-cols-1 md:grid-cols-[1fr_1fr_auto] gap-2"
                                        >
                                            <input
                                                v-model="item.params"
                                                type="text"
                                                placeholder="utm_source&utm_medium"
                                                class="px-3 py-1 text-sm font-mono border
                                                       border-slate-400 dark:border-slate-500
                                                       rounded-sm bg-white dark:bg-slate-900
                                                       text-slate-800 dark:text-slate-100"
                                            >

                                            <input
                                                v-model="item.path"
                                                type="text"
                                                placeholder="/"
                                                class="px-3 py-1 text-sm font-mono border
                                                       border-slate-400 dark:border-slate-500
                                                       rounded-sm bg-white dark:bg-slate-900
                                                       text-slate-800 dark:text-slate-100"
                                            >

                                            <button
                                                type="button"
                                                title="Удалить"
                                                class="w-8 h-8 inline-flex items-center
                                                       justify-center text-base font-semibold
                                                       text-amber-600 dark:text-amber-300 bg-amber-50
                                                       dark:bg-amber-900/30 hover:bg-amber-100
                                                       dark:hover:bg-amber-900/50 border
                                                       border-amber-300 dark:border-amber-700
                                                       rounded-sm shadow-sm transition-colors"
                                                @click="removeCleanParam(group, index)"
                                            >
                                                ×
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Sitemap -->
                        <div
                            class="p-4 bg-white dark:bg-slate-800 border border-slate-400
                                   dark:border-slate-500 rounded">
                            <div class="flex items-center justify-between gap-3 mb-3">
                                <div>
                                    <div class="text-sm font-semibold
                                                text-slate-800 dark:text-slate-100">
                                        Sitemap
                                    </div>

                                    <div class="text-xs text-slate-500 dark:text-slate-400">
                                        Используйте абсолютный URL.
                                    </div>
                                </div>

                                <button
                                    type="button"
                                    class="inline-flex items-center gap-1 px-2.5 py-1 text-xs
                                           font-medium text-sky-700 dark:text-sky-200 bg-sky-50
                                           dark:bg-sky-900/40 hover:bg-sky-100
                                           dark:hover:bg-sky-800/60 border border-sky-300
                                           dark:border-sky-600 rounded-sm shadow-sm
                                           transition-colors"
                                    @click="addSitemap"
                                >
                                    <span class="text-sm leading-none">+</span>
                                    Добавить
                                </button>
                            </div>

                            <div v-if="!form.sitemaps.length" class="text-xs text-slate-400">
                                Sitemap пока не указан.
                            </div>

                            <div class="space-y-2">
                                <div
                                    v-for="(sitemap, index) in form.sitemaps"
                                    :key="index"
                                    class="flex items-center gap-2"
                                >
                                    <input
                                        v-model="form.sitemaps[index]"
                                        type="url"
                                        placeholder="https://example.com/sitemap.xml"
                                        class="w-full px-3 py-1 text-sm font-mono border
                                               border-slate-400 dark:border-slate-500
                                               rounded-sm bg-white dark:bg-slate-900
                                               text-slate-800 dark:text-slate-100"
                                    >

                                    <button
                                        type="button"
                                        title="Удалить"
                                        class="flex-none w-8 h-8 inline-flex items-center
                                               justify-center text-base font-semibold
                                               text-amber-600 dark:text-amber-300 bg-amber-50
                                               dark:bg-amber-900/30 hover:bg-amber-100
                                               dark:hover:bg-amber-900/50 border border-amber-300
                                               dark:border-amber-700 rounded-sm shadow-sm
                                               transition-colors"
                                        @click="removeSitemap(index)"
                                    >
                                        ×
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Предпросмотр -->
                        <div>
                            <div class="mb-2 flex items-center justify-between">
                                <label class="text-sm font-semibold
                                              text-slate-800 dark:text-slate-100">
                                    Предпросмотр robots.txt
                                </label>

                                <span class="text-xs text-slate-500 dark:text-slate-400">
                                    Обновляется автоматически
                                </span>
                            </div>

                            <pre
                                class="w-full min-h-52 p-3 overflow-x-auto text-xs
                                       leading-5 font-mono bg-white dark:bg-slate-900
                                       text-slate-800 dark:text-slate-100 border
                                       border-slate-400 dark:border-slate-500 rounded">{{ preview }}</pre>
                        </div>
                    </div>

                    <!-- Ручной редактор -->
                    <div v-if="form.editor_mode === 'manual'" class="space-y-2">
                        <div class="flex items-end justify-between gap-4">
                            <div>
                                <div class="text-sm font-semibold
                                            text-slate-800 dark:text-slate-100">
                                    Ручное редактирование robots.txt
                                </div>

                                <div class="mt-1 text-xs text-slate-500 dark:text-slate-400">
                                    Содержимое сохраняется без автоматического изменения генератором.
                                </div>
                            </div>

                            <div class="text-xs text-slate-400">
                                Полный контроль файла
                            </div>
                        </div>

                        <textarea
                            v-model="form.content"
                            rows="24"
                            spellcheck="false"
                            class="w-full min-h-[500px] p-3 font-mono text-sm
                                   leading-6 border border-slate-400 dark:border-slate-500
                                   rounded bg-white dark:bg-slate-900 text-slate-900
                                   dark:text-slate-100 placeholder:text-slate-400
                                   dark:placeholder:text-slate-500 focus:outline-none
                                   focus:ring-1 focus:ring-sky-500 focus:border-sky-500"
                        />
                    </div>

                    <!-- Ошибки -->
                    <div
                        v-if="Object.keys(form.errors).length"
                        class="p-3 bg-red-50 dark:bg-red-900/20 border
                               border-red-300 dark:border-red-700 rounded"
                    >
                        <div class="text-sm font-semibold text-red-700 dark:text-red-300">
                            Ошибка сохранения
                        </div>

                        <div
                            v-for="(error, key) in form.errors"
                            :key="key"
                            class="mt-1 text-xs text-red-600 dark:text-red-400"
                        >
                            {{ error }}
                        </div>
                    </div>

                    <!-- Сохранение -->
                    <div class="flex justify-end">
                        <PrimaryButton
                            :disabled="form.processing"
                            :class="{ 'opacity-50 cursor-not-allowed': form.processing }"
                        >
                            {{ form.processing ? 'Сохранение...' : t('save') }}
                        </PrimaryButton>
                    </div>
                </form>
            </div>
        </div>
    </AdminLayout>
</template>
