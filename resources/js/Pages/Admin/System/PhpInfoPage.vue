<script setup>
import { nextTick, ref } from 'vue'

import AdminLayout from '@/Layouts/AdminLayout.vue'
import TitlePage from '@/Components/Admin/UI/Headlines/TitlePage.vue'

// @ts-ignore
import html2pdf from 'html2pdf.js'

const props = defineProps({
    phpinfo: {
        type: String,
        default: '',
    },
})

const search = ref('')
const searchCount = ref(0)
const phpInfoContent = ref(null)

const currentDateTime = new Date()
    .toISOString()
    .slice(0, 19)
    .replace(/:/g, '-')

/**
 * Удаление подсветки предыдущего поиска.
 */
const clearHighlights = () => {
    const container = phpInfoContent.value

    if (!container) {
        searchCount.value = 0
        return
    }

    const marks = container.querySelectorAll(
        'mark.php-info-search-mark'
    )

    marks.forEach((mark) => {
        const parent = mark.parentNode

        if (!parent) {
            return
        }

        parent.replaceChild(
            document.createTextNode(
                mark.textContent || ''
            ),
            mark
        )

        parent.normalize()
    })

    searchCount.value = 0
}

/**
 * Поиск по содержимому phpinfo().
 */
const searchPhpInfo = async () => {
    clearHighlights()

    const query = search.value
        .trim()
        .toLowerCase()

    if (!query) {
        return
    }

    await nextTick()

    const container = phpInfoContent.value

    if (!container) {
        return
    }

    const walker = document.createTreeWalker(
        container,
        window.NodeFilter.SHOW_TEXT
    )

    const nodes = []

    let currentNode = walker.nextNode()

    while (currentNode) {
        const parent = currentNode.parentElement

        if (
            parent &&
            parent.tagName !== 'SCRIPT' &&
            parent.tagName !== 'STYLE'
        ) {
            nodes.push(currentNode)
        }

        currentNode = walker.nextNode()
    }

    let count = 0
    let firstMatch = null

    nodes.forEach((textNode) => {
        const text = textNode.nodeValue || ''
        const lowerText = text.toLowerCase()

        if (!lowerText.includes(query)) {
            return
        }

        const fragment = document.createDocumentFragment()

        let start = 0
        let index = lowerText.indexOf(query)

        while (index !== -1) {
            if (index > start) {
                fragment.appendChild(
                    document.createTextNode(
                        text.slice(
                            start,
                            index
                        )
                    )
                )
            }

            const mark = document.createElement('mark')

            mark.className = 'php-info-search-mark'

            mark.textContent = text.slice(
                index,
                index + query.length
            )

            fragment.appendChild(mark)

            if (!firstMatch) {
                firstMatch = mark
            }

            count++

            start = index + query.length

            index = lowerText.indexOf(
                query,
                start
            )
        }

        if (start < text.length) {
            fragment.appendChild(
                document.createTextNode(
                    text.slice(start)
                )
            )
        }

        if (textNode.parentNode) {
            textNode.parentNode.replaceChild(
                fragment,
                textNode
            )
        }
    })

    searchCount.value = count

    if (firstMatch) {
        firstMatch.scrollIntoView({
            behavior: 'smooth',
            block: 'center',
        })
    }
}

/**
 * Очистка поиска.
 */
const clearSearch = () => {
    search.value = ''
    clearHighlights()
}

/**
 * Экспорт phpinfo() в PDF.
 */
const downloadPDF = () => {
    const wrapper = document.createElement('div')

    wrapper.innerHTML = `
        <div style="
            font-family: Arial, sans-serif;
            font-size: 9px;
            color: #111;
            background: #fff;
        ">
            <h1 style="
                font-size: 18px;
                margin-bottom: 12px;
            ">
                PHP Info
            </h1>

            <style>
                table {
                    width: 100%;
                    border-collapse: collapse;
                    margin-bottom: 10px;
                    table-layout: fixed;
                }

                th,
                td {
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

        filename:
            `php-info_${currentDateTime}.pdf`,

        image: {
            type: 'jpeg',
            quality: 0.98,
        },

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
            mode: [
                'css',
                'legacy',
            ],
            avoid: [
                'tr',
            ],
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
            <TitlePage>
                PHP Info
            </TitlePage>
        </template>

        <div class="px-2 py-2 w-full max-w-12xl mx-auto">
            <div
                class="p-4 bg-slate-50 dark:bg-slate-700
                       border border-blue-400 dark:border-blue-200
                       overflow-hidden
                       shadow-md shadow-gray-500
                       dark:shadow-slate-400
                       bg-opacity-95 dark:bg-opacity-95"
            >
                <!-- Информационный блок -->
                <div
                    class="mb-4 px-4 py-3
                           border border-orange-300
                           dark:border-orange-700
                           bg-orange-50
                           dark:bg-orange-900/20
                           rounded-md"
                >
                    <div class="flex items-start gap-3">
                        <div
                            class="shrink-0 flex items-center justify-center
                                   w-8 h-8 rounded-full
                                   bg-orange-100 dark:bg-orange-800
                                   text-orange-700 dark:text-orange-200"
                        >
                            <svg
                                class="w-5 h-5 fill-current"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    d="M11 17h2v-6h-2v6zm1-15C6.48 2
                                       2 6.48 2 12s4.48 10
                                       10 10 10-4.48 10-10S17.52
                                       2 12 2zm0 18c-4.41
                                       0-8-3.59-8-8s3.59-8
                                       8-8 8 3.59 8 8-3.59
                                       8-8 8zM11 9h2V7h-2v2z"
                                />
                            </svg>
                        </div>

                        <div>
                            <div
                                class="text-sm font-semibold
                                       text-orange-800
                                       dark:text-orange-200"
                            >
                                Конфигурация PHP
                            </div>

                            <div
                                class="mt-0.5 text-xs
                                       text-orange-700
                                       dark:text-orange-300"
                            >
                                Здесь отображается информация,
                                полученная непосредственно через
                                <span class="font-mono font-semibold">
                                    phpinfo()
                                </span>.
                                Страница доступна только для просмотра.
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Панель управления -->
                <div
                    class="flex flex-col xl:flex-row
           xl:items-start
           xl:justify-between
           gap-3 mb-4"
                >
                    <!-- PDF -->
                    <button
                        type="button"
                        class="h-8 px-3
               inline-flex items-center
               justify-center gap-1
               bg-teal-600 text-white
               rounded-sm
               hover:bg-teal-700
               transition"
                        @click="downloadPDF"
                    >
                        <svg
                            class="h-4 w-4 fill-current"
                            viewBox="0 0 384 512"
                        >
                            <path
                                d="M181.9 256.1c-5-16-4.9-46.9-2-46.9
                   8.4 0 7.6 36.9 2 46.9zm-1.7
                   47.2c-7.7 20.2-17.3 43.3-28.4
                   62.7 18.3-7 39-17.2
                   62.9-21.9-12.7-9.6-24.9-23.4-34.5-40.8zM86.1
                   428.1c0 .8 13.2-5.4
                   34.9-40.2-6.7 6.3-29.1
                   24.5-34.9 40.2zM248
                   160h136v328c0 13.3-10.7
                   24-24 24H24c-13.3
                   0-24-10.7-24-24V24C0
                   10.7 10.7 0 24 0h200v136c0
                   13.2 10.8 24 24
                   24zm-8 171.8c-20-12.2-33.3-29-42.7-53.8
                   4.5-18.5 11.6-46.6
                   6.2-64.2-4.7-29.4-42.4-26.5-47.8-6.8-5
                   18.3-.4 44.1 8.1
                   77-11.6 27.6-28.7
                   64.6-40.8 85.8-.1 0-.1.1-.2.1-27.1
                   13.9-73.6 44.5-54.5
                   68 5.6 6.9 16 10
                   21.5 10 17.9 0 35.7-18
                   61.1-61.8 25.8-8.5 54.1-19.1
                   79-23.2 21.7 11.8
                   47.1 19.5 64 19.5 29.2
                   0 31.2-32 19.7-43.4-13.9-13.6-54.3-9.7-73.6-7.2zM377
                   105 279 7c-4.5-4.5-10.6-7-17-7h-6v128h128v-6.1c0-6.3-2.5-12.4-7-16.9zm-74.1
                   255.3c4.1-2.7-2.5-11.9-42.8-9
                   37.1 15.8 42.8 9 42.8 9z"
                            />
                        </svg>

                        <span>
            PDF
        </span>
                    </button>

                    <!-- Поиск -->
                    <div class="w-full xl:w-auto">
                        <div
                            class="flex flex-col sm:flex-row
                   sm:items-center
                   gap-2"
                        >
                            <div class="relative w-full sm:w-96">
                                <svg
                                    class="absolute left-2.5 top-1/2
                           -translate-y-1/2
                           w-4 h-4 fill-current
                           text-slate-400"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        d="M9.5 3a6.5 6.5 0 1 0
                           3.98 11.64L19.85 21
                           21 19.85l-6.36-6.37A6.5
                           6.5 0 0 0 9.5 3zm0
                           2a4.5 4.5 0 1 1 0
                           9 4.5 4.5 0 0 1 0-9z"
                                    />
                                </svg>

                                <input
                                    v-model="search"
                                    type="text"
                                    placeholder="memory_limit, curl, PDO, openssl..."
                                    class="w-full pl-8 pr-8 py-1.5
                           text-sm font-mono
                           border border-slate-300
                           dark:border-slate-500
                           rounded-md
                           bg-white dark:bg-slate-800
                           text-slate-800
                           dark:text-slate-100
                           placeholder:text-slate-400
                           focus:outline-none
                           focus:ring-1
                           focus:ring-sky-500
                           focus:border-sky-500"
                                    @keyup.enter="searchPhpInfo"
                                >

                                <button
                                    v-if="search"
                                    type="button"
                                    title="Очистить поиск"
                                    class="absolute right-2 top-1/2
                           -translate-y-1/2
                           text-slate-400
                           hover:text-red-500
                           transition"
                                    @click="clearSearch"
                                >
                                    ×
                                </button>
                            </div>

                            <button
                                type="button"
                                class="h-8 px-3
                       inline-flex items-center
                       justify-center
                       bg-sky-600 text-white
                       rounded-sm
                       hover:bg-sky-700
                       transition"
                                @click="searchPhpInfo"
                            >
                                Найти
                            </button>
                        </div>

                        <!-- Результат поиска -->
                        <div
                            v-if="search"
                            class="mt-1 text-xs
                   text-slate-600
                   dark:text-slate-300"
                        >
                            Найдено:
                            <span
                                class="font-semibold
                       text-sky-700
                       dark:text-sky-300"
                            >
                {{ searchCount }}
            </span>
                        </div>
                    </div>
                </div>

                <!-- PHP Info -->
                <div
                    class="overflow-x-auto
                           border border-slate-300
                           rounded-md shadow
                           p-4 bg-white
                           text-gray-900 text-sm"
                >
                    <h1
                        class="text-xl font-semibold
                               mb-4 text-gray-900"
                    >
                        PHP Info
                    </h1>

                    <div
                        ref="phpInfoContent"
                        class="php-info-content"
                        v-html="props.phpinfo"
                    />
                </div>

                <div
                    class="mt-3 text-xs
                           text-slate-500
                           dark:text-slate-400"
                >
                    Информация PHP доступна только для просмотра.
                    Изменение конфигурации PHP из административной
                    панели не выполняется.
                </div>
            </div>
        </div>
    </AdminLayout>
</template>

<style scoped>
:deep(.php-info-content table) {
    width: 100%;
    border-collapse: collapse;
    margin-bottom: 1rem;
}

:deep(.php-info-content th),
:deep(.php-info-content td) {
    padding: 6px 10px;
    border: 1px solid #ccc;
    text-align: left;
    word-break: break-word;
}

:deep(.php-info-content .e) {
    background: #f0f0f0;
    font-weight: 600;
    color: #111827;
}

:deep(.php-info-content .v) {
    background: #ffffff;
    color: #111827;
}

:deep(.php-info-content h1),
:deep(.php-info-content h2) {
    margin-top: 1rem;
    margin-bottom: 0.5rem;
    font-weight: 600;
}

:deep(mark.php-info-search-mark) {
    background: #fde047 !important;
    color: #111827 !important;
    padding: 1px 2px;
    border-radius: 2px;
}
</style>
