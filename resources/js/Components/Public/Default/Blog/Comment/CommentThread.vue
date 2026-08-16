<script setup>
import {
    ref,
    onMounted,
    watch,
} from 'vue'

import { useI18n } from 'vue-i18n'
import axios from 'axios'

const { t } = useI18n()

const props = defineProps({
    /**
     * Полный class модели:
     *
     * App\Models\Admin\Blog\BlogArticle\BlogArticle
     * или
     * App\Models\Admin\Blog\BlogVideo\BlogVideo
     */
    commentableType: {
        type: String,
        required: true,
    },

    commentableId: {
        type: Number,
        required: true,
    },

    /**
     * null для гостя.
     */
    authUser: {
        type: Object,
        default: null,
    },
})

/**
 * Видимость блока.
 */
const isVisible = ref(true)

const toggleVisibility = () => {
    isVisible.value = !isVisible.value
}

/**
 * Comments state.
 */
const comments = ref([])
const loading = ref(false)
const error = ref(null)

/**
 * Edit state.
 */
const editingId = ref(null)
const editText = ref('')

/**
 * Reply state.
 */
const replyingId = ref(null)
const replyText = ref('')

/**
 * New comment state.
 */
const newText = ref('')

/**
 * Отправка запроса.
 */
const submitting = ref(false)

/**
 * Нормализация автора.
 */
const normalizeUser = (user) => {
    if (!user) {
        return {
            id: null,
            name: t('unknownAuthor'),
            profile_photo_url: null,
        }
    }

    return {
        id: user.id ?? null,
        name:
            user.name
            || t('unknownAuthor'),

        profile_photo_url:
            user.profile_photo_url
            || null,
    }
}

/**
 * Нормализация ответа.
 */
const normalizeReply = (reply) => {
    return {
        id:
            reply?.id ?? null,

        parent_id:
            reply?.parent_id ?? null,

        content:
            reply?.content ?? '',

        user:
            normalizeUser(
                reply?.user
            ),

        created_at:
            reply?.created_at ?? null,

        updated_at:
            reply?.updated_at ?? null,
    }
}

/**
 * Нормализация корневого комментария.
 */
const normalizeComment = (comment) => {
    const replies = Array.isArray(
        comment?.replies
    )
        ? comment.replies
        : (
            Array.isArray(
                comment?.replies?.data
            )
                ? comment.replies.data
                : []
        )

    return {
        id:
            comment?.id ?? null,

        parent_id:
            comment?.parent_id ?? null,

        content:
            comment?.content ?? '',

        user:
            normalizeUser(
                comment?.user
            ),

        replies_count:
            Number(
                comment?.replies_count
                ?? replies.length
                ?? 0
            ),

        replies:
            replies.map(
                normalizeReply
            ),

        created_at:
            comment?.created_at ?? null,

        updated_at:
            comment?.updated_at ?? null,
    }
}

/**
 * Нормализация ответа ResourceCollection.
 */
const normalizeComments = (payload) => {
    const list = Array.isArray(payload)
        ? payload
        : Array.isArray(payload?.data)
            ? payload.data
            : []

    return list.map(
        normalizeComment
    )
}

/**
 * Загрузка публичных комментариев.
 *
 * Авторизация для чтения не требуется.
 */
const fetchComments = async () => {
    loading.value = true
    error.value = null

    try {
        const { data } = await axios.get(
            '/api/comments',
            {
                params: {
                    commentable_type:
                        String(
                            props.commentableType
                        ),

                    commentable_id:
                        Number(
                            props.commentableId
                        ),
                },
            }
        )

        comments.value =
            normalizeComments(data)
    } catch (err) {
        console.error(
            t('commentErrorLoading'),
            err.response || err
        )

        if (
            err.response?.data?.message
        ) {
            error.value =
                `${t('error')}: ${err.response.data.message}`
        } else {
            error.value =
                t('commentErrorLoading')
        }

        comments.value = []
    } finally {
        loading.value = false
    }
}

/**
 * Начать редактирование.
 */
const startEdit = (comment) => {
    if (!props.authUser) {
        return
    }

    editingId.value =
        comment.id

    editText.value =
        comment.content ?? ''
}

/**
 * Отменить редактирование.
 */
const cancelEdit = () => {
    editingId.value = null
    editText.value = ''
}

/**
 * Сохранить изменение.
 *
 * После редактирования комментарий
 * отправляется на повторную модерацию,
 * поэтому после reload может исчезнуть
 * из публичного списка.
 */
const saveEdit = async () => {
    if (
        !props.authUser
        || !editingId.value
        || !editText.value.trim()
        || submitting.value
    ) {
        return
    }

    submitting.value = true

    try {
        await axios.put(
            `/api/comments/${editingId.value}`,
            {
                content:
                    editText.value.trim(),
            }
        )

        cancelEdit()

        await fetchComments()
    } catch (err) {
        console.error(
            t('robotError'),
            err.response || err
        )

        alert(
            err.response?.data?.message
            || t('robotError')
        )
    } finally {
        submitting.value = false
    }
}

/**
 * Удалить свой комментарий / ответ.
 */
const removeComment = async (id) => {
    if (
        !props.authUser
        || !id
        || submitting.value
    ) {
        return
    }

    if (!confirm(t('commentDelete'))) {
        return
    }

    submitting.value = true

    try {
        await axios.delete(
            `/api/comments/${id}`
        )

        await fetchComments()
    } catch (err) {
        console.error(
            t('commentDeleteError'),
            err.response || err
        )

        alert(
            err.response?.data?.message
            || t('commentDeleteError')
        )
    } finally {
        submitting.value = false
    }
}

/**
 * Начать ответ.
 */
const startReply = (id) => {
    if (!props.authUser) {
        return
    }

    replyingId.value = id
    replyText.value = ''
}

/**
 * Отменить ответ.
 */
const cancelReply = () => {
    replyingId.value = null
    replyText.value = ''
}

/**
 * Отправить ответ.
 *
 * Новый ответ находится pending,
 * поэтому сразу в публичном списке
 * не появится до модерации.
 */
const sendReply = async (parentId) => {
    if (
        !props.authUser
        || !replyText.value.trim()
        || submitting.value
    ) {
        return
    }

    submitting.value = true

    try {
        await axios.post(
            '/api/comments',
            {
                commentable_type:
                props.commentableType,

                commentable_id:
                props.commentableId,

                parent_id:
                parentId,

                content:
                    replyText.value.trim(),
            }
        )

        cancelReply()

        await fetchComments()
    } catch (err) {
        console.error(
            t('commentErrorResponse'),
            err.response || err
        )

        alert(
            err.response?.data?.message
            || t('commentErrorResponse')
        )
    } finally {
        submitting.value = false
    }
}

/**
 * Добавить новый комментарий.
 */
const sendNew = async () => {
    if (
        !props.authUser
        || !newText.value.trim()
        || submitting.value
    ) {
        return
    }

    submitting.value = true

    try {
        await axios.post(
            '/api/comments',
            {
                commentable_type:
                props.commentableType,

                commentable_id:
                props.commentableId,

                content:
                    newText.value.trim(),
            }
        )

        newText.value = ''

        /**
         * Комментарий pending,
         * поэтому публичный endpoint
         * его пока не вернёт.
         */
        await fetchComments()
    } catch (err) {
        if (
            err.response?.data?.errors
        ) {
            const messages =
                Object.values(
                    err.response.data.errors
                )
                    .flat()
                    .join('\n')

            console.error(
                'Validation errors:',
                err.response.data.errors
            )

            alert(messages)
        } else {
            console.error(
                t('commentErrorSending'),
                err.response || err
            )

            alert(
                err.response?.data?.message
                || t('commentErrorAdding')
            )
        }
    } finally {
        submitting.value = false
    }
}

/**
 * Публичные комментарии
 * загружаем для всех пользователей,
 * включая гостей.
 */
onMounted(() => {
    fetchComments()
})

/**
 * Компонент универсальный:
 * Article Show / Video Show.
 *
 * Если изменился объект —
 * перезагружаем комментарии.
 */
watch(
    () => [
        props.commentableType,
        props.commentableId,
    ],
    () => {
        cancelEdit()
        cancelReply()

        fetchComments()
    }
)
</script>

<template>
    <div class="mt-4">
        <!-- Заголовок -->
        <h3
            class="mb-4 flex items-center justify-center
                   text-center cursor-pointer select-none
                   text-indigo-700 dark:text-indigo-300"
            @click="toggleVisibility"
        >
            <svg
                class="w-3 h-3"
                fill="currentColor"
                viewBox="0 0 512 512"
            >
                <path
                    d="M448 0H64C28.7 0 0 28.7 0 64v288c0 35.3 28.7 64 64 64h96v84c0 9.8 11.2 15.5 19.1 9.7L304 416h144c35.3 0 64-28.7 64-64V64c0-35.3-28.7-64-64-64z"
                />
            </svg>

            <span
                class="mx-2 border-b dashed
                       border-gray-400
                       font-semibold text-md"
            >
                {{ t('comments') }}
            </span>

            <span
                class="text-sm text-red-500
                       dark:text-yellow-300"
            >
                {{ isVisible ? '▲' : '▼' }}
            </span>
        </h3>

        <div v-show="isVisible">
            <!-- Загрузка -->
            <div
                v-if="loading"
                class="text-sm text-center
                       text-gray-500"
            >
                {{ t('uploadingComments') }}
            </div>

            <!-- Ошибка -->
            <div
                v-else-if="error"
                class="text-sm text-center
                       text-red-500"
            >
                {{ error }}
            </div>

            <template v-else>
                <!-- Нет комментариев -->
                <div
                    v-if="!comments.length"
                    class="text-sm text-center
                           text-gray-500
                           dark:text-gray-400"
                >
                    {{ t('noCommentsYet') }}
                </div>

                <!-- Комментарии -->
                <div
                    v-for="comment in comments"
                    :key="comment.id"
                    class="mb-3 rounded
                           border p-3
                           bg-gray-50
                           dark:bg-slate-800"
                >
                    <!-- Header -->
                    <div
                        class="flex items-center
                               justify-between
                               gap-3 text-sm"
                    >
                        <div
                            class="flex min-w-0
                                   items-center gap-2"
                        >
                            <img
                                v-if="
                                    comment.user
                                        ?.profile_photo_url
                                "
                                :src="
                                    comment.user
                                        .profile_photo_url
                                "
                                :alt="
                                    comment.user?.name
                                    || t('unknownAuthor')
                                "
                                loading="lazy"
                                class="h-6 w-6
                                       rounded-full
                                       object-cover"
                            />

                            <span
                                class="truncate font-semibold
                                       text-blue-600
                                       dark:text-blue-300"
                            >
                                {{
                                    comment.user?.name
                                    || t('unknownAuthor')
                                }}
                            </span>
                        </div>

                        <!-- Actions -->
                        <div
                            v-if="authUser"
                            class="flex flex-wrap
                                   items-center gap-1
                                   text-xs"
                        >
                            <!-- Reply -->
                            <button
                                type="button"
                                class="flex items-center
                                       justify-center gap-1
                                       px-3 py-1
                                       text-slate-600
                                       transition
                                       hover:text-blue-700
                                       dark:text-slate-400
                                       dark:hover:text-blue-300"
                                @click="
                                    startReply(
                                        comment.id
                                    )
                                "
                            >
                                <span>
                                    {{ t('reply') }}
                                </span>
                            </button>

                            <!-- Owner actions -->
                            <template
                                v-if="
                                    authUser.id
                                    === comment.user?.id
                                "
                            >
                                <button
                                    type="button"
                                    class="px-3 py-1
                                           text-green-600
                                           transition
                                           hover:text-teal-700
                                           dark:text-green-400
                                           dark:hover:text-teal-300"
                                    @click="
                                        startEdit(
                                            comment
                                        )
                                    "
                                >
                                    {{ t('edit') }}
                                </button>

                                <button
                                    type="button"
                                    class="px-3 py-1
                                           text-red-600
                                           transition
                                           hover:text-rose-700
                                           dark:text-red-400
                                           dark:hover:text-rose-300"
                                    @click="
                                        removeComment(
                                            comment.id
                                        )
                                    "
                                >
                                    {{ t('delete') }}
                                </button>
                            </template>
                        </div>
                    </div>

                    <!-- Edit root -->
                    <div
                        v-if="
                            editingId
                            === comment.id
                        "
                        class="mt-2 space-y-2"
                    >
                        <textarea
                            v-model="editText"
                            maxlength="500"
                            class="w-full rounded
                                   border p-2 text-sm
                                   bg-white text-black
                                   dark:bg-slate-700
                                   dark:text-white"
                        />

                        <div
                            class="flex
                                   justify-end gap-2"
                        >
                            <button
                                type="button"
                                :disabled="submitting"
                                class="rounded-sm
                                       px-3 py-1
                                       btn-default
                                       disabled:opacity-50"
                                @click="saveEdit"
                            >
                                {{ t('save') }}
                            </button>

                            <button
                                type="button"
                                :disabled="submitting"
                                class="rounded-sm
                                       border
                                       border-gray-400
                                       px-3 py-1"
                                @click="cancelEdit"
                            >
                                {{ t('cancel') }}
                            </button>
                        </div>
                    </div>

                    <!-- Content -->
                    <div
                        v-else
                        class="mt-2 text-sm
                               text-gray-800
                               dark:text-gray-200"
                    >
                        {{ comment.content }}
                    </div>

                    <!-- Reply form -->
                    <div
                        v-if="
                            authUser
                            && replyingId
                                === comment.id
                        "
                        class="mt-3"
                    >
                        <textarea
                            v-model="replyText"
                            maxlength="500"
                            class="w-full rounded
                                   border p-2 text-sm
                                   bg-white text-black
                                   dark:bg-slate-700
                                   dark:text-white"
                            :placeholder="
                                t('commentYourAnswer')
                            "
                        />

                        <div
                            class="mt-2 flex
                                   justify-end gap-2"
                        >
                            <button
                                type="button"
                                :disabled="
                                    submitting
                                    || !replyText.trim()
                                "
                                class="rounded-sm
                                       px-3 py-1
                                       btn-default
                                       disabled:opacity-50"
                                @click="
                                    sendReply(
                                        comment.id
                                    )
                                "
                            >
                                {{ t('send') }}
                            </button>

                            <button
                                type="button"
                                :disabled="submitting"
                                class="rounded-sm
                                       border
                                       border-gray-400
                                       px-3 py-1"
                                @click="cancelReply"
                            >
                                {{ t('cancel') }}
                            </button>
                        </div>
                    </div>

                    <!-- Replies -->
                    <div
                        v-if="
                            comment.replies?.length
                        "
                        class="mt-3 space-y-2
                               border-l-2
                               border-gray-400
                               pl-4"
                    >
                        <div
                            v-for="
                                reply
                                in comment.replies
                            "
                            :key="reply.id"
                        >
                            <div
                                class="flex
                                       items-center
                                       justify-between
                                       gap-2"
                            >
                                <span
                                    class="text-xs
                                           font-semibold
                                           text-purple-600
                                           dark:text-purple-300"
                                >
                                    {{
                                        reply.user?.name
                                        || t('unknownAuthor')
                                    }}
                                </span>

                                <div
                                    v-if="
                                        authUser
                                        && authUser.id
                                            === reply.user?.id
                                    "
                                    class="flex gap-2"
                                >
                                    <button
                                        type="button"
                                        class="text-xs
                                               font-semibold
                                               text-teal-700
                                               hover:opacity-75
                                               dark:text-teal-300"
                                        @click="
                                            startEdit(
                                                reply
                                            )
                                        "
                                    >
                                        {{ t('edit') }}
                                    </button>

                                    <button
                                        type="button"
                                        class="text-xs
                                               font-semibold
                                               text-red-600
                                               hover:opacity-75
                                               dark:text-red-300"
                                        @click="
                                            removeComment(
                                                reply.id
                                            )
                                        "
                                    >
                                        {{ t('delete') }}
                                    </button>
                                </div>
                            </div>

                            <!-- Edit reply -->
                            <div
                                v-if="
                                    editingId
                                    === reply.id
                                "
                                class="mt-1"
                            >
                                <textarea
                                    v-model="editText"
                                    maxlength="500"
                                    class="w-full rounded
                                           border p-2
                                           text-sm
                                           bg-white
                                           text-black
                                           dark:bg-slate-700
                                           dark:text-white"
                                />

                                <div
                                    class="mt-1 flex
                                           justify-end
                                           gap-2"
                                >
                                    <button
                                        type="button"
                                        :disabled="
                                            submitting
                                        "
                                        class="text-xs
                                               font-semibold"
                                        @click="saveEdit"
                                    >
                                        {{ t('save') }}
                                    </button>

                                    <button
                                        type="button"
                                        :disabled="
                                            submitting
                                        "
                                        class="text-xs
                                               font-semibold"
                                        @click="cancelEdit"
                                    >
                                        {{ t('cancel') }}
                                    </button>
                                </div>
                            </div>

                            <div
                                v-else
                                class="mt-1 text-sm
                                       text-gray-700
                                       dark:text-gray-200"
                            >
                                {{ reply.content }}
                            </div>
                        </div>
                    </div>
                </div>

                <!-- New comment -->
                <div
                    v-if="authUser"
                    class="mt-4 border-t pt-3"
                >
                    <h4
                        class="mb-2 text-sm
                               font-semibold
                               text-gray-700
                               dark:text-gray-300"
                    >
                        {{ t('commentLeave') }}
                    </h4>

                    <textarea
                        v-model="newText"
                        maxlength="500"
                        class="w-full rounded
                               border
                               border-gray-400
                               px-2 py-1
                               text-sm
                               bg-white
                               text-black
                               dark:bg-slate-700
                               dark:text-white"
                        :placeholder="
                            t('commentYour')
                        "
                    />

                    <div
                        class="mt-2 flex
                               justify-end"
                    >
                        <button
                            type="button"
                            :disabled="
                                submitting
                                || !newText.trim()
                            "
                            class="flex w-full
                                   items-center
                                   justify-center
                                   rounded-sm
                                   px-3 py-2
                                   btn-default
                                   disabled:opacity-50"
                            @click="sendNew"
                        >
                            {{ t('send') }}
                        </button>
                    </div>
                </div>

                <!-- Guest -->
                <div
                    v-else
                    class="mt-4 border-t pt-3
                           text-center text-sm
                           text-gray-500
                           dark:text-gray-400"
                >
                    {{ t('commentLogin') }}
                </div>
            </template>
        </div>
    </div>
</template>
