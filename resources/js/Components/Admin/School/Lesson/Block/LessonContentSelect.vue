<script setup>
import { computed, ref, watch } from 'vue'
import { useI18n } from 'vue-i18n'
import VueMultiselect from 'vue-multiselect'
import LabelInput from '@/Components/Admin/UI/Input/LabelInput.vue'
import InputError from '@/Components/Admin/UI/Input/InputError.vue'
import ContentIdInput from '@/Components/Admin/School/Lesson/Input/ContentIdInput.vue'

const { t } = useI18n()

// Props компонента
const props = defineProps({
    // Тип связанного контента (FQCN модели)
    contentType: { type: String, default: null },

    // ID связанного контента
    contentId: { type: [Number, String, null], default: null },

    // Список статей
    articles: { type: Array, default: () => [] },

    // Список видео
    videos: { type: Array, default: () => [] },

    // Ошибка типа контента
    errorType: { type: String, default: '' },

    // Ошибка ID контента
    errorId: { type: String, default: '' },
})

// Emits для v-model
const emit = defineEmits(['update:contentType', 'update:contentId'])

// Соответствие локального типа и FQCN модели
const CONTENT_TYPE_MAP = {
    article: 'App\\Models\\Admin\\Blog\\BlogArticle\\BlogArticle',
    video: 'App\\Models\\Admin\\Blog\\BlogVideo\\BlogVideo',
}

// Опции статей для VueMultiselect
const articleOptions = computed(() =>
    props.articles.map(a => ({
        id: a.id,
        label: `[ID: ${a.id}] ${a.title || a.name || `Article #${a.id}`}`,
    })),
)

// Опции видео для VueMultiselect
const videoOptions = computed(() =>
    props.videos.map(v => ({
        id: v.id,
        label: `[ID: ${v.id}] ${v.title || v.name || `Video #${v.id}`}`,
    })),
)

// Текущий локальный тип контента
const localType = ref(null)

// Текущий выбранный элемент
const selectedItem = ref(null)

// Кэш выбранных элементов для восстановления при переключении табов
const cachedSelected = ref({
    article: null,
    video: null,
})

// Активный список опций в зависимости от выбранного типа
const activeOptions = computed(() => {
    if (localType.value === 'article') return articleOptions.value
    if (localType.value === 'video') return videoOptions.value

    return []
})

// Определение локального типа по FQCN модели
const detectType = (contentType) => {
    if (contentType === CONTENT_TYPE_MAP.article) return 'article'
    if (contentType === CONTENT_TYPE_MAP.video) return 'video'

    return null
}

// Поиск выбранного элемента по типу и ID
const findOption = (type, id) => {
    if (!type || !id) return null

    const options = type === 'article'
        ? articleOptions.value
        : videoOptions.value

    return options.find(item => Number(item.id) === Number(id)) || null
}

// Синхронизация props с локальным состоянием
watch(
    () => [props.contentType, props.contentId, props.articles.length, props.videos.length],
    () => {
        const type = detectType(props.contentType)

        localType.value = type

        // Сброс состояния если тип не выбран
        if (!type) {
            selectedItem.value = null
            return
        }

        // Поиск выбранного элемента
        const found = findOption(type, props.contentId)

        // Установка текущего элемента
        selectedItem.value = found

        // Сохранение в кэш
        cachedSelected.value[type] = found
    },
    { immediate: true },
)

// Переключение типа связанного контента
const handleTypeChange = (type) => {
    // Защита от повторного выбора
    if (type === localType.value) return

    localType.value = type

    // Полный сброс связи
    if (!type) {
        selectedItem.value = null

        emit('update:contentType', null)
        emit('update:contentId', null)

        return
    }

    // Восстановление из кэша
    const cached = cachedSelected.value[type]

    selectedItem.value = cached

    // Обновление типа и ID
    emit('update:contentType', CONTENT_TYPE_MAP[type])
    emit('update:contentId', cached ? Number(cached.id) : null)
}

// Обновление content_id при выборе элемента
watch(selectedItem, (item) => {
    // Сохраняем выбранный элемент в кэш
    if (localType.value) {
        cachedSelected.value[localType.value] = item
    }

    // Отправляем новый ID наверх
    emit('update:contentId', item ? Number(item.id) : null)
})

// Прокси для ручного ввода ID
const contentIdProxy = computed({
    get() {
        return props.contentId ?? ''
    },

    set(val) {
        // Сброс значения
        if (val === '' || val === null) {
            selectedItem.value = null

            emit('update:contentId', null)

            return
        }

        // Преобразование в число
        const num = Number(val)

        // Проверка корректности ID
        if (!Number.isFinite(num)) {
            emit('update:contentId', null)

            return
        }

        // Поиск элемента по ID
        selectedItem.value = findOption(localType.value, num)

        // Обновление ID
        emit('update:contentId', num)
    },
})
</script>

<template>
    <div
        class="mb-3 flex flex-col items-start
               border-t border-b border-dashed border-slate-500 py-3 w-full">

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
