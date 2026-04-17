<script setup>
import { ref, onMounted } from 'vue'
import { useI18n } from 'vue-i18n'
import axios from 'axios'

const { t } = useI18n()

const props = defineProps({
    commentableType: { type: String, required: true },
    commentableId: { type: Number, required: true },
    authUser: { type: Object, default: null }
})

const isVisible = ref(true)

const toggleVisibility = () => {
    isVisible.value = !isVisible.value
}

const comments = ref([])
const loading = ref(false)
const error = ref(null)

const editingId = ref(null)
const editText = ref('')
const replyingId = ref(null)
const replyText = ref('')
const newText = ref('')

/**
 * Безопасная нормализация одного комментария / ответа
 */
const normalizeComment = (comment) => {
    return {
        id: comment?.id ?? null,
        content: comment?.content ?? '',
        user: {
            id: comment?.user?.id ?? null,
            name: comment?.user?.name ?? t('unknownAuthor'),
        },
        replies: Array.isArray(comment?.replies)
            ? comment.replies.map((reply) => ({
                id: reply?.id ?? null,
                content: reply?.content ?? '',
                user: {
                    id: reply?.user?.id ?? null,
                    name: reply?.user?.name ?? t('unknownAuthor'),
                },
            }))
            : [],
    }
}

/**
 * Нормализация списка комментариев
 */
const normalizeComments = (payload) => {
    const list = Array.isArray(payload)
        ? payload
        : Array.isArray(payload?.data)
            ? payload.data
            : []

    return list.map(normalizeComment)
}

const fetchComments = async () => {
    loading.value = true
    error.value = null

    try {
        const { data } = await axios.get('/api/comments', {
            params: {
                commentable_type: String(props.commentableType),
                commentable_id: Number(props.commentableId)
            }
        })

        comments.value = normalizeComments(data)
    } catch (err) {
        console.error(t('commentErrorLoading'), err.response || err)

        if (err.response?.data?.message) {
            error.value = `${t('error')}: ${err.response.data.message}`
        } else {
            error.value = t('commentErrorLoading')
        }

        comments.value = []
    } finally {
        loading.value = false
    }
}

const startEdit = (comment) => {
    editingId.value = comment.id
    editText.value = comment.content ?? ''
}

const cancelEdit = () => {
    editingId.value = null
    editText.value = ''
}

const saveEdit = async () => {
    if (!editingId.value || !editText.value.trim()) return

    try {
        await axios.put(`/api/comments/${editingId.value}`, {
            content: editText.value
        })

        cancelEdit()
        await fetchComments()
    } catch (e) {
        console.error(t('robotError'), e)
        alert(t('robotError'))
    }
}

const removeComment = async (id) => {
    if (!id) return

    if (confirm(t('commentDelete'))) {
        try {
            await axios.delete(`/api/comments/${id}`)
            await fetchComments()
        } catch (e) {
            console.error(t('commentDeleteError'), e)
            alert(t('commentDeleteError'))
        }
    }
}

const startReply = (id) => {
    replyingId.value = id
    replyText.value = ''
}

const cancelReply = () => {
    replyingId.value = null
    replyText.value = ''
}

const sendReply = async (parentId) => {
    if (!replyText.value.trim()) return

    try {
        await axios.post('/api/comments', {
            commentable_type: props.commentableType,
            commentable_id: props.commentableId,
            parent_id: parentId,
            content: replyText.value
        })

        cancelReply()
        await fetchComments()
    } catch (e) {
        console.error(t('commentErrorResponse'), e)
        alert(t('commentErrorResponse'))
    }
}

const sendNew = async () => {
    if (!newText.value.trim()) return

    try {
        await axios.post('/api/comments', {
            commentable_type: props.commentableType,
            commentable_id: props.commentableId,
            content: newText.value
        })

        newText.value = ''
        await fetchComments()
    } catch (e) {
        if (e.response?.data?.errors) {
            console.error('Validation errors:', e.response.data.errors)
            alert(Object.values(e.response.data.errors).flat().join('\n'))
        } else {
            console.error(t('commentErrorSending'), e)
            alert(t('commentErrorAdding'))
        }
    }
}

onMounted(() => {
    if (props.authUser) {
        fetchComments()
    }
})
</script>

<template>
    <div class="mt-4">
        <h3
            class="mb-4 flex items-center justify-center
                   text-center cursor-pointer select-none
                   text-indigo-700 dark:text-indigo-300"
            @click="toggleVisibility"
        >
            <svg
                class="w-3 h-3"
                fill="currentColor"
                viewBox="0 0 512 512">
                <path d="M448 0H64C28.7 0 0 28.7 0 64v288c0 35.3 28.7 64 64 64h96v84c0 9.8 11.2 15.5 19.1 9.7L304 416h144c35.3 0 64-28.7 64-64V64c0-35.3-28.7-64-64-64z"/>
            </svg>

            <span
                class="mx-2 border-b dashed border-gray-400 font-semibold text-md"
            >
                {{ t('comments') }}
            </span>

            <span class="text-sm text-red-500 dark:text-yellow-300">
                {{ isVisible ? '▲' : '▼' }}
            </span>
        </h3>

        <div v-if="isVisible">
            <template v-if="authUser">
                <div v-if="loading" class="text-sm text-gray-500">
                    {{ t('uploadingComments') }}
                </div>

                <div v-else-if="error" class="text-red-500">
                    {{ error }}
                </div>

                <div
                    v-else-if="!comments.length"
                    class="text-sm text-gray-500 dark:text-gray-400"
                >
                    {{ t('noCommentsYet') }}
                </div>

                <div
                    v-for="comment in comments"
                    :key="comment.id"
                    class="mb-3 border p-3 rounded bg-gray-50 dark:bg-slate-800"
                >
                    <!-- Автор -->
                    <div class="flex justify-between items-center text-sm font-semibold gap-3">
                        <span class="text-blue-600 dark:text-blue-300">
                            {{ comment.user?.name || t('unknownAuthor') }}
                        </span>

                        <div class="flex flex-wrap gap-1 items-center text-xs">
                            <button @click="startReply(comment.id)"
                                    class="flex w-full items-center justify-center gap-1
                                           px-3 py-1 transition text-slate-600 dark:text-slate-400
                                           hover:text-blue-700 dark:hover:text-blue-300">
                                <svg
                                    class="h-3 w-3"
                                    fill="currentColor"
                                    viewBox="0 0 512 512">
                                    <path d="M503.691 189.836L327.687 37.851C312.281 24.546 288 35.347 288 56.015v80.053C127.371 137.907 0 170.1 0 322.326c0 61.441 39.581 122.309 83.333 154.132 13.653 9.931 33.111-2.533 28.077-18.631C66.066 312.814 132.917 274.316 288 272.085V360c0 20.7 24.3 31.453 39.687 18.164l176.004-152c11.071-9.562 11.086-26.753 0-36.328z"/>
                                </svg>
                                <span class="text-xs">
                                    {{ t('reply') }}
                                </span>
                            </button>

                            <template v-if="authUser && authUser.id === comment.user?.id">
                                <button @click="startEdit(comment)"
                                        class="flex w-full items-center justify-center gap-1
                                               px-3 py-1 transition
                                               text-green-600 dark:text-green-400
                                               hover:text-teal-700 dark:hover:text-teal-300">
                                    <svg
                                        class="h-3 w-3"
                                        fill="currentColor"
                                        viewBox="0 0 512 512">
                                        <path d="M497.94 74.17l-60.11-60.11c-18.75-18.75-49.16-18.75-67.91 0l-56.55 56.55 128.02 128.02 56.55-56.55c18.75-18.75 18.75-49.15 0-67.91zm-246.8-20.53c-15.62-15.62-40.94-15.62-56.56 0L75.8 172.43c-6.25 6.25-6.25 16.38 0 22.62l22.63 22.63c6.25 6.25 16.38 6.25 22.63 0l101.82-101.82 22.63 22.62L93.95 290.03A327.038 327.038 0 0 0 .17 485.11l-.03.23c-1.7 15.28 11.21 28.2 26.49 26.51a327.02 327.02 0 0 0 195.34-93.8l196.79-196.79-82.77-82.77-84.85-84.85z"/>
                                    </svg>
                                    <span class="text-xs">
                                        {{ t('edit') }}
                                    </span>
                                </button>
                                <button @click="removeComment(comment.id)"
                                        class="flex w-full items-center justify-center gap-1
                                               px-3 py-1 transition
                                               text-red-600 dark:text-red-400
                                               hover:text-rose-700 dark:hover:text-rose-300">
                                    <svg
                                        class="h-3 w-3"
                                        fill="currentColor"
                                        viewBox="0 0 448 512">
                                        <path d="M0 84V56c0-13.3 10.7-24 24-24h112l9.4-18.7c4-8.2 12.3-13.3 21.4-13.3h114.3c9.1 0 17.4 5.1 21.5 13.3L312 32h112c13.3 0 24 10.7 24 24v28c0 6.6-5.4 12-12 12H12C5.4 96 0 90.6 0 84zm416 56v324c0 26.5-21.5 48-48 48H80c-26.5 0-48-21.5-48-48V140c0-6.6 5.4-12 12-12h360c6.6 0 12 5.4 12 12zm-272 68c0-8.8-7.2-16-16-16s-16 7.2-16 16v224c0 8.8 7.2 16 16 16s16-7.2 16-16V208zm96 0c0-8.8-7.2-16-16-16s-16 7.2-16 16v224c0 8.8 7.2 16 16 16s16-7.2 16-16V208zm96 0c0-8.8-7.2-16-16-16s-16 7.2-16 16v224c0 8.8 7.2 16 16 16s16-7.2 16-16V208z"/>
                                    </svg>
                                    <span class="text-xs">
                                        {{ t('delete') }}
                                    </span>
                                </button>
                            </template>
                        </div>
                    </div>

                    <!-- Редактируемое сообщение -->
                    <div v-if="editingId === comment.id" class="mt-2 space-y-1">
                        <textarea
                            v-model="editText"
                            class="w-full border rounded p-1 text-sm
                                   bg-white dark:bg-slate-700 text-black dark:text-white"
                        />
                        <div class="flex items-center justify-end gap-2">
                            <button @click="saveEdit"
                                    class="flex w-full items-center justify-center gap-2
                                           px-3 py-1 transition rounded-sm
                                           border border-gray-500 dark:border-gray-400
                                           bg-teal-600 hover:bg-teal-700 text-slate-100
                                           hover:text-slate-50">
                                <svg
                                    class="h-4 w-4"
                                    fill="currentColor"
                                    viewBox="0 0 448 512">
                                    <path d="M433.941 129.941l-83.882-83.882A48 48 0 0 0 316.118 32H48C21.49 32 0 53.49 0 80v352c0 26.51 21.49 48 48 48h352c26.51 0 48-21.49 48-48V163.882a48 48 0 0 0-14.059-33.941zM224 416c-35.346 0-64-28.654-64-64 0-35.346 28.654-64 64-64s64 28.654 64 64c0 35.346-28.654 64-64 64zm96-304.52V212c0 6.627-5.373 12-12 12H76c-6.627 0-12-5.373-12-12V108c0-6.627 5.373-12 12-12h228.52c3.183 0 6.235 1.264 8.485 3.515l3.48 3.48A11.996 11.996 0 0 1 320 111.48z"/>
                                </svg>
                                <span class="text-sm">
                                    {{ t('save') }}
                                </span>
                            </button>
                            <button @click="cancelEdit"
                                    class="flex w-full items-center justify-center gap-2
                                           px-3 py-1 transition rounded-sm
                                           border border-gray-500 dark:border-gray-400
                                           bg-gray-100 dark:bg-gray-500
                                           hover:bg-gray-200 dark:hover:bg-gray-600
                                           text-slate-800 dark:text-slate-200
                                           hover:text-slate-900 dark:hover:text-slate-100">
                                <svg
                                    class="h-4 w-4"
                                    fill="currentColor"
                                    viewBox="0 0 512 512">
                                    <path d="M503.691 189.836L327.687 37.851C312.281 24.546 288 35.347 288 56.015v80.053C127.371 137.907 0 170.1 0 322.326c0 61.441 39.581 122.309 83.333 154.132 13.653 9.931 33.111-2.533 28.077-18.631C66.066 312.814 132.917 274.316 288 272.085V360c0 20.7 24.3 31.453 39.687 18.164l176.004-152c11.071-9.562 11.086-26.753 0-36.328z"/>
                                </svg>
                                <span class="text-sm">
                                    {{ t('cancel') }}
                                </span>
                            </button>
                        </div>
                    </div>

                    <div v-else class="text-sm text-gray-800 dark:text-gray-200">
                        {{ comment.content }}
                    </div>

                    <!-- Ответ -->
                    <div v-if="replyingId === comment.id" class="mt-2">
                        <textarea
                            v-model="replyText"
                            class="w-full border rounded p-1 text-sm bg-white dark:bg-slate-700
                                   text-black dark:text-white"
                            :placeholder="t('commentYourAnswer')"
                        />
                        <div class="flex items-center justify-end gap-2">
                            <button @click="sendReply(comment.id)"
                                    class="flex w-full items-center justify-center gap-2
                                           px-3 py-1 transition rounded-sm
                                           border border-gray-500 dark:border-gray-400
                                           bg-indigo-600 hover:bg-indigo-700 text-slate-100
                                           hover:text-slate-50">
                                <svg
                                    class="h-4 w-4"
                                    fill="currentColor"
                                    viewBox="0 0 512 512">
                                    <path d="M476 3.2L12.5 270.6c-18.1 10.4-15.8 35.6 2.2 43.2L121 358.4l287.3-253.2c5.5-4.9 13.3 2.6 8.6 8.3L176 407v80.5c0 23.6 28.5 32.9 42.5 15.8L282 426l124.6 52.2c14.2 6 30.4-2.9 33-18.2l72-432C515 7.8 493.3-6.8 476 3.2z"/>
                                </svg>
                                <span class="text-sm">
                                    {{ t('send') }}
                                </span>
                            </button>
                            <button @click="cancelReply"
                                    class="flex w-full items-center justify-center gap-2
                                           px-3 py-1 transition rounded-sm
                                           border border-gray-500 dark:border-gray-400
                                           bg-gray-100 dark:bg-gray-500
                                           hover:bg-gray-200 dark:hover:bg-gray-600
                                           text-slate-800 dark:text-slate-200
                                           hover:text-slate-900 dark:hover:text-slate-100">
                                <svg
                                    class="h-4 w-4"
                                    fill="currentColor"
                                    viewBox="0 0 512 512">
                                    <path d="M503.691 189.836L327.687 37.851C312.281 24.546 288 35.347 288 56.015v80.053C127.371 137.907 0 170.1 0 322.326c0 61.441 39.581 122.309 83.333 154.132 13.653 9.931 33.111-2.533 28.077-18.631C66.066 312.814 132.917 274.316 288 272.085V360c0 20.7 24.3 31.453 39.687 18.164l176.004-152c11.071-9.562 11.086-26.753 0-36.328z"/>
                                </svg>
                                <span class="text-sm">
                                    {{ t('cancel') }}
                                </span>
                            </button>
                        </div>
                    </div>

                    <!-- Вложенные ответы -->
                    <div
                        v-if="comment.replies?.length"
                        class="mt-3 pl-4 border-l-2 border-gray-400 space-y-2"
                    >
                        <div v-for="reply in comment.replies" :key="reply.id">
                            <div class="text-xs font-semibold text-purple-600 dark:text-purple-300">
                                {{ reply.user?.name || t('unknownAuthor') }}
                            </div>

                            <!-- Редактируемый ответ -->
                            <div v-if="editingId === reply.id">
                                <textarea
                                    v-model="editText"
                                    class="w-full px-2 py-0.5 rounded text-sm
                                           border border-gray-400
                                           bg-slate-100 dark:bg-slate-700
                                           text-black dark:text-white"
                                />
                                <div class="flex justify-end space-x-2 mt-1">
                                    <button
                                        @click="saveEdit"
                                        class="border border-gray-600 dark:border-gray-300
                                               text-xs text-black dark:text-slate-100
                                               hover:text-white dark:hover:text-white
                                               hover:bg-blue-600 dark:hover:bg-blue-900
                                               transition-colors duration-200 ease-in-out
                                               rounded-sm px-2 py-0.5 font-semibold"
                                    >
                                        {{ t('save') }}
                                    </button>
                                    <button
                                        @click="cancelEdit"
                                        class="border border-gray-600 dark:border-gray-300
                                               text-xs text-black dark:text-slate-100
                                               hover:text-white dark:hover:text-white
                                               hover:bg-gray-600 dark:hover:bg-black
                                               transition-colors duration-200 ease-in-out
                                               rounded-sm px-2 py-0.5 font-semibold"
                                    >
                                        {{ t('cancel') }}
                                    </button>
                                </div>
                            </div>

                            <div v-else class="text-sm text-gray-700 dark:text-gray-200">
                                {{ reply.content }}

                                <template v-if="authUser && authUser.id === reply.user?.id">
                                    <button
                                        class="mr-2 font-semibold hover:opacity-75
                                               text-xs text-teal-700 dark:text-teal-300
                                               border-b border-dashed
                                               border-teal-700 dark:border-teal-300"
                                        :title="t('edit')"
                                        @click="startEdit(reply)"
                                    >
                                        {{ t('edit') }}
                                    </button>

                                    <button
                                        class="font-semibold hover:opacity-75
                                               text-xs text-red-600 dark:text-red-300
                                               border-b border-dashed
                                               border-red-600 dark:border-red-300"
                                        :title="t('delete')"
                                        @click="removeComment(reply.id)"
                                    >
                                        {{ t('delete') }}
                                    </button>
                                </template>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Форма нового комментария -->
                <div class="mt-4 border-t pt-2">
                    <h4 class="font-semibold mb-2 text-sm text-gray-700 dark:text-gray-300">
                        {{ t('commentLeave') }}
                    </h4>

                    <textarea
                        v-model="newText"
                        class="w-full px-2 py-0.5 rounded text-sm
                               border border-gray-400
                               bg-white dark:bg-slate-700
                               text-black dark:text-white"
                        :placeholder="t('commentYour')"
                    />

                    <div class="mt-1 flex items-center justify-end">
                        <button
                            @click="sendNew"
                            class="flex w-full items-center justify-center gap-2
                                   rounded-sm px-3 py-2 btn-default"
                        >
                            <svg
                                class="h-4 w-4"
                                fill="currentColor"
                                viewBox="0 0 512 512">
                                <path d="M476 3.2L12.5 270.6c-18.1 10.4-15.8 35.6 2.2 43.2L121 358.4l287.3-253.2c5.5-4.9 13.3 2.6 8.6 8.3L176 407v80.5c0 23.6 28.5 32.9 42.5 15.8L282 426l124.6 52.2c14.2 6 30.4-2.9 33-18.2l72-432C515 7.8 493.3-6.8 476 3.2z"/>
                            </svg>
                            <span class="text-sm font-semibold">
                                {{ t('send') }}
                            </span>
                        </button>
                    </div>
                </div>
            </template>

            <div v-else class="text-sm text-gray-500 mt-4 text-center">
                {{ t('commentLogin') }}
            </div>
        </div>
    </div>
</template>
