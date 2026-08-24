<script setup>
import { computed, ref, watch } from 'vue'
import { useI18n } from 'vue-i18n'
import VueMultiselect from 'vue-multiselect'

import LabelInput from '@/Components/Admin/UI/Input/LabelInput.vue'
import InputError from '@/Components/Admin/UI/Input/InputError.vue'
import ContentIdInput from '@/Components/Admin/School/SchoolLesson/Input/ContentIdInput.vue'

const { t } = useI18n()

const props = defineProps({
    contentType: {
        type: String,
        default: null,
    },

    contentId: {
        type: [Number, String, null],
        default: null,
    },

    articles: {
        type: Array,
        default: () => [],
    },

    videos: {
        type: Array,
        default: () => [],
    },

    errorType: {
        type: String,
        default: '',
    },

    errorId: {
        type: String,
        default: '',
    },
})

const emit = defineEmits([
    'update:contentType',
    'update:contentId',
])

/* ==========================================================
 * CONTENT TYPES
 * ========================================================== */

const CONTENT_TYPE_MAP = {
    article:
        'App\\Models\\Admin\\Blog\\BlogArticle\\BlogArticle',

    video:
        'App\\Models\\Admin\\Blog\\BlogVideo\\BlogVideo',
}

const detectType = (contentType) => {
    if (
        contentType
        === CONTENT_TYPE_MAP.article
    ) {
        return 'article'
    }

    if (
        contentType
        === CONTENT_TYPE_MAP.video
    ) {
        return 'video'
    }

    return null
}

/* ==========================================================
 * OPTIONS
 * ========================================================== */

/**
 * Новый краткий Resource contract:
 *
 * article.translation.title
 */
const articleOptions = computed(() => {
    return (props.articles || []).map(
        (article) => ({
            id: article.id,

            label:
                `[ID: ${article.id}] ${
                    article?.translation?.title
                    || article?.url
                    || `Article #${article.id}`
                }`,
        })
    )
})

/**
 * Новый краткий Resource contract:
 *
 * video.translation.title
 */
const videoOptions = computed(() => {
    return (props.videos || []).map(
        (video) => ({
            id: video.id,

            label:
                `[ID: ${video.id}] ${
                    video?.translation?.title
                    || video?.url
                    || `Video #${video.id}`
                }`,
        })
    )
})

const activeOptions = computed(() => {
    if (localType.value === 'article') {
        return articleOptions.value
    }

    if (localType.value === 'video') {
        return videoOptions.value
    }

    return []
})

/* ==========================================================
 * LOCAL STATE
 * ========================================================== */

const localType = ref(
    detectType(
        props.contentType
    )
)

const selectedItem = ref(null)

/**
 * Храним только ID.
 *
 * Не сохраняем старый option-объект,
 * потому что после смены locale
 * label может измениться.
 */
const cachedSelectedIds = ref({
    article: null,
    video: null,
})

const findOption = (
    type,
    id
) => {
    if (
        !type
        || id === null
        || id === ''
        || typeof id === 'undefined'
    ) {
        return null
    }

    const options =
        type === 'article'
            ? articleOptions.value
            : videoOptions.value

    return options.find(
        item =>
            Number(item.id)
            === Number(id)
    ) || null
}

/* ==========================================================
 * PROP SYNCHRONIZATION
 * ========================================================== */

/**
 * Синхронизация parent → component.
 *
 * При смене locale articles/videos
 * получают новые translation.title.
 *
 * Выбранный item восстанавливается
 * по ID уже из нового массива options.
 */
watch(
    [
        () => props.contentType,
        () => props.contentId,
        articleOptions,
        videoOptions,
    ],
    () => {
        const type =
            detectType(
                props.contentType
            )

        localType.value =
            type

        if (!type) {
            selectedItem.value =
                null

            return
        }

        const found =
            findOption(
                type,
                props.contentId
            )

        selectedItem.value =
            found

        cachedSelectedIds.value[type] =
            found?.id
            ?? (
                props.contentId
                    ? Number(props.contentId)
                    : null
            )
    },
    {
        immediate: true,
    }
)

/* ==========================================================
 * TYPE CHANGE
 * ========================================================== */

const handleTypeChange = (
    type
) => {
    if (
        type
        === localType.value
    ) {
        return
    }

    /**
     * Запоминаем текущий ID
     * перед переключением типа.
     */
    if (
        localType.value
        && selectedItem.value
    ) {
        cachedSelectedIds.value[
            localType.value
            ] = Number(
            selectedItem.value.id
        )
    }

    localType.value =
        type

    if (!type) {
        selectedItem.value =
            null

        emit(
            'update:contentType',
            null
        )

        emit(
            'update:contentId',
            null
        )

        return
    }

    const cachedId =
        cachedSelectedIds.value[
            type
            ]

    const cachedOption =
        findOption(
            type,
            cachedId
        )

    selectedItem.value =
        cachedOption

    emit(
        'update:contentType',
        CONTENT_TYPE_MAP[type]
    )

    emit(
        'update:contentId',
        cachedOption
            ? Number(
                cachedOption.id
            )
            : null
    )
}

/* ==========================================================
 * SELECTED ITEM
 * ========================================================== */

watch(
    selectedItem,
    (item) => {
        if (localType.value) {
            cachedSelectedIds.value[
                localType.value
                ] = item
                ? Number(item.id)
                : null
        }

        emit(
            'update:contentId',
            item
                ? Number(item.id)
                : null
        )
    }
)

/* ==========================================================
 * MANUAL CONTENT ID
 * ========================================================== */

const contentIdProxy = computed({
    get() {
        return props.contentId
            ?? ''
    },

    set(value) {
        if (
            value === ''
            || value === null
        ) {
            selectedItem.value =
                null

            emit(
                'update:contentId',
                null
            )

            return
        }

        const id =
            Number(value)

        if (
            !Number.isFinite(id)
        ) {
            selectedItem.value =
                null

            emit(
                'update:contentId',
                null
            )

            return
        }

        selectedItem.value =
            findOption(
                localType.value,
                id
            )

        if (localType.value) {
            cachedSelectedIds.value[
                localType.value
                ] = id
        }

        /**
         * Ручной ID разрешён даже если
         * такого элемента сейчас нет
         * среди загруженных options.
         */
        emit(
            'update:contentId',
            id
        )
    },
})
</script>

<template>
    <div
        class="mb-3 flex w-full flex-col items-start
               border-y border-dashed border-slate-500 py-3"
    >
        <div
            class="mb-2 w-full text-sm font-semibold
                   text-gray-900 dark:text-gray-200"
        >
            {{ t('relatedContent') }}
        </div>

        <div class="grid w-full grid-cols-1 gap-4 md:grid-cols-2">
            <!-- Тип контента -->
            <div class="flex flex-col items-start">
                <LabelInput for="content_type">
                    {{ t('contentType') }}
                </LabelInput>

                <div class="mt-1 flex flex-wrap gap-3">
                    <button
                        type="button"
                        class="rounded border px-3 py-1 text-sm"
                        :class="
                            localType === 'article'
                                ? 'border-emerald-700 bg-emerald-600 text-white'
                                : 'border-slate-300 bg-slate-100 text-slate-700 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-100'
                        "
                        @click="handleTypeChange('article')"
                    >
                        {{ t('article') }}
                    </button>

                    <button
                        type="button"
                        class="rounded border px-3 py-1 text-sm"
                        :class="
                            localType === 'video'
                                ? 'border-emerald-700 bg-emerald-600 text-white'
                                : 'border-slate-300 bg-slate-100 text-slate-700 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-100'
                        "
                        @click="handleTypeChange('video')"
                    >
                        {{ t('video') }}
                    </button>

                    <button
                        type="button"
                        class="rounded border px-2 py-0.5 text-xs"
                        :class="
                            !localType
                                ? 'border-red-700 bg-red-500 text-white'
                                : 'border-slate-300 bg-slate-200 text-slate-800 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-200'
                        "
                        @click="handleTypeChange(null)"
                    >
                        {{ t('reset') }}
                    </button>
                </div>

                <InputError
                    class="mt-1"
                    :message="errorType"
                />
            </div>

            <!-- Статья / видео -->
            <div class="flex w-full flex-col items-start">
                <LabelInput for="content_id">
                    {{ t('contentId') }}
                </LabelInput>

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
                    class="mb-2 w-full"
                />

                <div class="flex w-full items-center gap-2">
                    <ContentIdInput
                        id="content_id"
                        v-model="contentIdProxy"
                        :min="0"
                        class="w-32"
                    />

                    <span
                        class="text-xs font-semibold
                               text-slate-600 dark:text-slate-400"
                    >
                        {{ t('orEnterIdManually') }}
                    </span>
                </div>

                <InputError
                    class="mt-1"
                    :message="errorId"
                />
            </div>
        </div>
    </div>
</template>
