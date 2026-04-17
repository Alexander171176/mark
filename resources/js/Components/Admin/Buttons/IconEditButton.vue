<script setup>
import { Link } from '@inertiajs/vue3'
import {computed, defineProps} from 'vue'
import { useI18n } from 'vue-i18n';

const { t } = useI18n();

const props = defineProps({
    href: {
        required: true
    },
    title: String
})

const buttonClass = computed(() => [
    'flex items-center py-1 px-2 rounded',
    'border border-gray-700',
    'hover:border-blue-700'
]);
</script>

<template>
    <div class="absolute top-3 right-3 z-10">
        <Link :href="href"
              :class="buttonClass"
              :title="t('edit')">
            <svg class="w-4 h-6 fill-current
                        text-gray-900 hover:text-blue-700 shrink-0"
                 viewBox="0 0 16 16">
                <path d="M11.7.3c-.4-.4-1-.4-1.4 0l-10 10c-.2.2-.3.4-.3.7v4c0 .6.4 1 1 1h4c.3 0 .5-.1.7-.3l10-10c.4-.4.4-1 0-1.4l-4-4zM4.6 14H2v-2.6l6-6L10.6 8l-6 6zM12 6.6L9.4 4 11 2.4 13.6 5 12 6.6z"/>
            </svg>
        </Link>
    </div>
</template>
