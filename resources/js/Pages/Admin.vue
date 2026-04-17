<script setup>
import { useI18n } from 'vue-i18n'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import Welcome from '@/Components/Welcome.vue'
import TitlePage from '@/Components/Admin/Headlines/TitlePage.vue'
import PagePropsModal from '@/Components/Admin/Dev/PagePropsModal.vue'

const { t } = useI18n()
</script>

<template>
    <AdminLayout :title="t('adminPanel')">
        <template #header>
            <TitlePage>
                {{ t('adminPanel') }}
            </TitlePage>
        </template>

        <div class="py-8">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="overflow-hidden shadow-xl sm:rounded-lg">
                    <Welcome />
                    <!-- Кнопка + модалка просмотра props -->
                    <div class="p-1 flex justify-center">
                        <PagePropsModal />
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
