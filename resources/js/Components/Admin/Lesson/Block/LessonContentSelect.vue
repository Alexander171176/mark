<script setup>
import { ref, computed, watchEffect, watch } from 'vue'
import { useI18n } from 'vue-i18n'
import VueMultiselect from 'vue-multiselect'
import LabelInput from '@/Components/Admin/Input/LabelInput.vue'
import InputError from '@/Components/Admin/Input/InputError.vue'
import ContentIdInput from '@/Components/Admin/Lesson/Input/ContentIdInput.vue'

const { t } = useI18n()

const props = defineProps({
    // FQCN модели в базе: App\Models\Admin\Article\Article и т.п.
    contentType: {
        type: String,
        default: null,
    },
    // id связанного контента
    contentId: {
        type: [Number, String, null],
        default: null,
    },
    // Списки статей и видео из контроллера
    articles: {
        type: Array,
        default: () => [],
    },
    videos: {
        type: Array,
        default: () => [],
    },
    // Ошибки из формы
    errorType: {
        type: String,
        default: '',
    },
    errorId: {
        type: String,
        default: '',
    },
})

const emit = defineEmits(['update:contentType', 'update:contentId'])

const CONTENT_TYPE_MAP = {
    article: 'App\\Models\\Admin\\Article\\Article',
    video:   'App\\Models\\Admin\\Video\\Video',
}

// Опции для селекта статей/видео
const articleOptions = computed(() =>
    props.articles.map(a => ({
        id: a.id,
        label: a.title || a.name || `Article #${a.id}`,
    })),
)

const videoOptions = computed(() =>
    props.videos.map(v => ({
        id: v.id,
        label: v.title || v.name || `Video #${v.id}`,
    })),
)

// Локальный тип: 'article' | 'video' | null
const localType = ref(null)
// Выбранная сущность: {id, label} | null
const selectedItem = ref(null)

// Активный список опций в зависимости от типа
const activeOptions = computed(() => {
    if (localType.value === 'article') return articleOptions.value
    if (localType.value === 'video')   return videoOptions.value
    return []
})

/**
 * Синхронизация локального состояния с пропсами
 * (на случай переиспользования в Edit.vue)
 */
watchEffect(() => {
    // contentType (FQCN) -> localType
    if (props.contentType === CONTENT_TYPE_MAP.article) {
        localType.value = 'article'
    } else if (props.contentType === CONTENT_TYPE_MAP.video) {
        localType.value = 'video'
    } else {
        localType.value = null
    }

    // contentId -> selectedItem
    if (props.contentId && localType.value) {
        const options = localType.value === 'article'
            ? articleOptions.value
            : videoOptions.value

        const found = options.find(o => Number(o.id) === Number(props.contentId))
        selectedItem.value = found || null
    } else if (!props.contentId) {
        selectedItem.value = null
    }
})

/**
 * Смена типа контента кнопками
 */
const handleTypeChange = (type) => {
    if (type === localType.value) return

    localType.value = type

    if (!type) {
        emit('update:contentType', null)
        emit('update:contentId', null)
        selectedItem.value = null
        return
    }

    emit('update:contentType', CONTENT_TYPE_MAP[type] || null)
    emit('update:contentId', null)
    selectedItem.value = null
}

/**
 * При выборе статьи/видео из мультиселекта — выставляем content_id
 */
watch(selectedItem, (item) => {
    if (!item) {
        emit('update:contentId', null)
    } else {
        emit('update:contentId', Number(item.id))
    }
})

/**
 * Прокси для ручного ввода ID (ContentIdInput)
 */
const contentIdProxy = computed({
    get() {
        return props.contentId ?? ''
    },
    set(val) {
        if (val === '' || val === null) {
            emit('update:contentId', null)
        } else {
            const num = Number(val)
            emit('update:contentId', Number.isFinite(num) ? num : null)
        }
    },
})
</script>

<template>
    <div
        class="mb-3 flex flex-col items-start
               border-t border-dashed border-slate-500 pt-3 w-full">

        <div class="w-full mb-2 text-sm font-semibold text-gray-900 dark:text-gray-200">
            {{ t('relatedContent') }}
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 w-full">
            <!-- Тип контента -->
            <div class="flex flex-col items-start">
                <LabelInput for="content_type">
                    {{ t('contentType') }}
                </LabelInput>

                <div class="flex flex-wrap gap-3 mt-1">
                    <button
                        type="button"
                        class="px-3 py-1 rounded border text-sm"
                        :class="localType === 'article'
              ? 'bg-emerald-600 text-white border-emerald-700'
              : 'bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-100 border-slate-300 dark:border-slate-600'"
                        @click="handleTypeChange('article')"
                    >
                        {{ t('article') }}
                    </button>

                    <button
                        type="button"
                        class="px-3 py-1 rounded border text-sm"
                        :class="localType === 'video'
              ? 'bg-emerald-600 text-white border-emerald-700'
              : 'bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-100 border-slate-300 dark:border-slate-600'"
                        @click="handleTypeChange('video')"
                    >
                        {{ t('video') }}
                    </button>

                    <button
                        type="button"
                        class="px-2 py-0.5 rounded border text-xs"
                        :class="!localType
                              ? 'bg-red-500 text-white border-red-700'
                              : 'bg-slate-200 dark:bg-slate-800 ' +
                               'text-slate-800 dark:text-slate-200 ' +
                               'border-slate-300 dark:border-slate-600'"
                        @click="handleTypeChange(null)"
                    >
                        {{ t('reset') }}
                    </button>
                </div>

                <InputError class="mt-1" :message="errorType" />
            </div>

            <!-- Конкретная статья / видео + ручной ID -->
            <div class="flex flex-col items-start w-full">
                <LabelInput for="content_id">
                    {{ t('contentId') }}
                </LabelInput>

                <!-- Список статей/видео, если тип выбран -->
                <VueMultiselect
                    v-if="localType"
                    id="content_id_select"
                    v-model="selectedItem"
                    :options="activeOptions"
                    :multiple="false"
                    :close-on-select="true"
                    :clear-on-select="false"
                    :preserve-search="true"
                    :placeholder="t('select')"
                    label="label"
                    track-by="id"
                    class="w-full mb-2"
                />

                <!-- Ручной ввод ID (всегда доступен) -->
                <div class="flex items-center gap-2 w-full">
                    <ContentIdInput
                        id="content_id"
                        :min="0"
                        v-model="contentIdProxy"
                        class="w-32"
                    />
                    <span class="font-semibold text-xs text-slate-600 dark:text-slate-400">
                        {{ t('orEnterIdManually') }}
                    </span>
                </div>

                <InputError class="mt-1" :message="errorId" />
            </div>
        </div>
    </div>
</template>
