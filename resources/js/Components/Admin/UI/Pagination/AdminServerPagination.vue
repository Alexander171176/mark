<script setup>
import { computed, ref, watch } from 'vue'
import { router } from '@inertiajs/vue3'
import { useI18n } from 'vue-i18n'

const { t } = useI18n()

const props = defineProps({
    pagination: {
        type: Object,
        required: true,
    },
})

const currentPage = computed(() => props.pagination?.meta?.current_page || 1)
const lastPage = computed(() => props.pagination?.meta?.last_page || 1)

const pageInput = ref(currentPage.value)

watch(currentPage, (value) => {
    pageInput.value = value
})

const changePage = (page) => {
    const targetPage = Number(page)

    if (
        !Number.isFinite(targetPage)
        || targetPage < 1
        || targetPage > lastPage.value
        || targetPage === currentPage.value
    ) {
        return
    }

    router.get(
        window.location.pathname,
        {
            ...Object.fromEntries(new URLSearchParams(window.location.search)),
            page: targetPage,
        },
        {
            preserveScroll: true,
            preserveState: false,
            replace: true,
        }
    )
}

const handlePageInput = () => {
    let page = Number(pageInput.value)

    if (page < 1) page = 1
    if (page > lastPage.value) page = lastPage.value

    changePage(page)
}
</script>

<template>
    <div class="w-full sm:w-fit flex justify-center items-center">
        <div class="flex flex-col md:flex-row justify-center items-center
                    px-2 py-1 bg-white dark:bg-slate-700">
            <button
                type="button"
                @click="changePage(currentPage - 1)"
                :disabled="currentPage === 1"
                class="btn font-semibold text-sm bg-slate-50 dark:bg-slate-300
                       border border-green-500 text-teal-700 px-3 py-0.5 rounded
                       hover:text-rose-500 disabled:opacity-50 disabled:text-slate-400"
            >
                {{ t('previous') }}
            </button>

            <span class="flex flex-row items-center font-semibold text-sm
                         ml-2 mr-2 text-slate-700 dark:text-slate-100">
                <span class="hidden lg:block">{{ t('page') }}</span>

                <input
                    type="number"
                    v-model="pageInput"
                    @change="handlePageInput"
                    :disabled="lastPage === 1"
                    min="1"
                    :max="lastPage"
                    class="w-28 mx-2 py-0.5 text-center border border-slate-400
                           rounded dark:bg-slate-300 dark:text-slate-700"
                />

                <span class="text-blue-500 dark:text-rose-300">
                    {{ t('of') }} {{ lastPage }}
                </span>
            </span>

            <button
                type="button"
                @click="changePage(currentPage + 1)"
                :disabled="currentPage === lastPage"
                class="btn font-semibold text-sm bg-white dark:bg-slate-100
                       border border-green-500 text-teal-700 px-2 py-0.5 rounded
                       hover:text-rose-500 disabled:opacity-50 disabled:text-slate-400"
            >
                {{ t('next') }}
            </button>
        </div>
    </div>
</template>
