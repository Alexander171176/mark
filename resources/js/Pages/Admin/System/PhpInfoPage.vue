<script setup>
import { defineProps } from 'vue'
import AdminLayout from "@/Layouts/AdminLayout.vue"
import TitlePage from "@/Components/Admin/UI/Headlines/TitlePage.vue"

// @ts-ignore
import html2pdf from 'html2pdf.js'

const props = defineProps({
    phpinfo: String,
})

const currentDateTime = new Date().toISOString().slice(0, 19).replace(/:/g, '-')

const downloadPDF = () => {
    const wrapper = document.createElement('div')

    wrapper.innerHTML = `
        <div style="font-family: Arial, sans-serif; font-size: 9px; color: #111; background: #fff;">
            <h1 style="font-size: 18px; margin-bottom: 12px;">PHP Info</h1>

            <style>
                table {
                    width: 100%;
                    border-collapse: collapse;
                    margin-bottom: 10px;
                    table-layout: fixed;
                }

                th, td {
                    border: 1px solid #999;
                    padding: 4px 6px;
                    vertical-align: top;
                    word-break: break-word;
                    overflow-wrap: anywhere;
                }

                .e {
                    background: #f0f0f0;
                    font-weight: bold;
                    width: 35%;
                }

                .v {
                    background: #ffffff;
                    width: 65%;
                }

                tr {
                    page-break-inside: avoid;
                }
            </style>

            ${props.phpinfo || ''}
        </div>
    `

    const options = {
        margin: 0.35,
        filename: `php-info_${currentDateTime}.pdf`,
        image: { type: 'jpeg', quality: 0.98 },
        html2canvas: {
            scale: 1,
            logging: false,
            useCORS: true,
            backgroundColor: '#ffffff',
        },
        jsPDF: {
            unit: 'in',
            format: 'a4',
            orientation: 'portrait',
        },
        pagebreak: {
            mode: ['css', 'legacy'],
            avoid: ['tr'],
        },
    }

    html2pdf()
        .set(options)
        .from(wrapper)
        .save()
}
</script>

<template>
    <AdminLayout title="PHP Info">
        <template #header>
            <TitlePage>PHP Info</TitlePage>
        </template>

        <div class="px-2 py-2 w-full max-w-12xl mx-auto">
            <div class="p-4 bg-slate-50 dark:bg-slate-700
                        border border-blue-400 dark:border-blue-200
                        overflow-hidden shadow-md shadow-gray-500 dark:shadow-slate-400
                        bg-opacity-95 dark:bg-opacity-95">

                <div class="flex justify-end mb-3">
                    <button
                        type="button"
                        @click="downloadPDF"
                        class="h-8 px-3 inline-flex items-center justify-center gap-1
                               bg-teal-600 text-white rounded-sm hover:bg-teal-700"
                    >
                        <svg class="h-4 w-4 fill-current"
                             viewBox="0 0 384 512">
                            <path d="M181.9 256.1c-5-16-4.9-46.9-2-46.9 8.4 0 7.6 36.9 2 46.9zm-1.7 47.2c-7.7 20.2-17.3 43.3-28.4 62.7 18.3-7 39-17.2 62.9-21.9-12.7-9.6-24.9-23.4-34.5-40.8zM86.1 428.1c0 .8 13.2-5.4 34.9-40.2-6.7 6.3-29.1 24.5-34.9 40.2zM248 160h136v328c0 13.3-10.7 24-24 24H24c-13.3 0-24-10.7-24-24V24C0 10.7 10.7 0 24 0h200v136c0 13.2 10.8 24 24 24zm-8 171.8c-20-12.2-33.3-29-42.7-53.8 4.5-18.5 11.6-46.6 6.2-64.2-4.7-29.4-42.4-26.5-47.8-6.8-5 18.3-.4 44.1 8.1 77-11.6 27.6-28.7 64.6-40.8 85.8-.1 0-.1.1-.2.1-27.1 13.9-73.6 44.5-54.5 68 5.6 6.9 16 10 21.5 10 17.9 0 35.7-18 61.1-61.8 25.8-8.5 54.1-19.1 79-23.2 21.7 11.8 47.1 19.5 64 19.5 29.2 0 31.2-32 19.7-43.4-13.9-13.6-54.3-9.7-73.6-7.2zM377 105L279 7c-4.5-4.5-10.6-7-17-7h-6v128h128v-6.1c0-6.3-2.5-12.4-7-16.9zm-74.1 255.3c4.1-2.7-2.5-11.9-42.8-9 37.1 15.8 42.8 9 42.8 9z" />
                        </svg>
                        <span>PDF</span>
                    </button>
                </div>

                <div
                    id="phpInfoContent"
                    class="overflow-x-auto border rounded-md shadow p-4
                           bg-white text-gray-900 text-sm"
                >
                    <h1 class="text-xl font-semibold mb-4">
                        PHP Info
                    </h1>

                    <div v-html="phpinfo" class="php-info-content" />
                </div>
            </div>
        </div>
    </AdminLayout>
</template>

<style scoped>
:deep(table) {
    width: 100%;
    border-collapse: collapse;
    margin-bottom: 1rem;
}

:deep(th),
:deep(td) {
    padding: 6px 10px;
    border: 1px solid #ccc;
    text-align: left;
    word-break: break-word;
}

:deep(.e) {
    background: #f0f0f0;
    font-weight: bold;
}

:deep(.v) {
    background: #ffffff;
}
</style>
