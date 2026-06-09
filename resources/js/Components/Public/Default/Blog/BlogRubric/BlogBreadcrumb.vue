<script setup>
import { Link } from '@inertiajs/vue3'
import { useI18n } from 'vue-i18n'

const { t } = useI18n()
defineProps({
    rubricTitle: { type: String, default: '' },
})
</script>

<template>
    <nav class="text-sm" aria-label="Breadcrumb">
        <ol class="list-reset flex items-center">
            <li>
                <Link :href="route('home')"
                      class="hover:underline
                             text-slate-900 hover:text-blue-900
                             dark:text-slate-100 dark:hover:text-blue-100">
                    {{ t('home') }}
                </Link>
            </li>
            <li><span class="mx-1 text-slate-700 dark:text-slate-300">/</span></li>
            <li>
                <Link :href="route('public.rubrics.index')"
                      class="hover:underline text-slate-900 hover:text-blue-900
                             dark:text-slate-100 dark:hover:text-blue-100">
                    {{ t('blog') }}
                </Link>
            </li>
            <li><span class="mx-1 text-slate-700 dark:text-slate-300">/</span></li>
            <li class="text-slate-700 dark:text-slate-300">
                {{ rubricTitle }}
            </li>
        </ol>
    </nav>
</template>
