<script setup>
import { Head } from '@inertiajs/vue3'
import { useI18n } from 'vue-i18n'
import Progress from '@/Components/Public/Default/Progress/Progress.vue'
import DefaultLayout from '@/Layouts/DefaultLayout.vue'
import Navbar from '@/Partials/Default/Navbar.vue'
import FooterBlog from '@/Partials/Default/FooterBlog.vue'

const { t } = useI18n()

defineProps({
    terms: String
})
</script>

<template>
    <Head :title="t('termsOfService')" />

    <DefaultLayout>
        <!-- Шапка -->
        <Navbar />
        <main class="min-h-screen px-1 lg:px-6 max-w-full">
            <div
                class="mx-auto tracking-wider pt-20 lg:pt-44"
            >
                <div
                    class="ext-color w-full min-w-0 py-3 px-1
                           flex flex-col lg:flex-row gap-4 rounded-3xl
                           border-2 border-slate-300 dark:border-slate-500"
                >
                    <div
                        class="mx-auto flex
                               w-full max-w-6xl flex-col
                               items-center px-4 py-8
                               sm:px-6 lg:px-8"
                    >
                        <!-- Документ -->
                        <article
                            class="w-full max-w-3xl
                                   rounded-2xl
                                   border border-gray-200
                                   bg-white px-6 py-8
                                   shadow-xl
                                   dark:border-gray-800
                                   dark:bg-gray-900
                                   sm:px-10 sm:py-10
                                   lg:px-12 lg:py-12"
                        >
                            <!-- Заголовок -->
                            <header
                                class="mb-10 border-b border-gray-200 pb-8
                                       dark:border-gray-800"
                            >
                                <h1
                                    class="text-2xl font-bold tracking-tight
                                           text-gray-900 dark:text-white
                                           sm:text-3xl text-center"
                                >
                                    {{ t('termsOfService') }}
                                </h1>

                                <p
                                    class="mt-3 text-sm text-center
                                            text-gray-500 dark:text-gray-400"
                                >
                                    Дата последнего обновления: 20 сентября 2026 года
                                </p>
                            </header>

                            <div
                                class="space-y-10
                                       text-base leading-7
                                       text-gray-700 dark:text-gray-300"
                            >
                                <!-- Введение -->
                                <section>
                                    <p>
                                        Настоящие Условия обслуживания регулируют порядок использования
                                        сайта и сервисов Платформы (далее — «Сайт», «Платформа»),
                                        включая интернет магазин, онлайн-школу, информационные материалы,
                                        личный кабинет пользователя и другие доступные функции.
                                    </p>

                                    <p class="mt-4">
                                        Используя Платформу, пользователь подтверждает, что ознакомился
                                        с настоящими Условиями обслуживания и принимает их в полном объеме.
                                    </p>
                                </section>

                                <!-- 1 -->
                                <section>
                                    <h2 class="text-xl font-semibold text-gray-900 dark:text-white">
                                        1. Общие положения
                                    </h2>

                                    <p class="mt-4">
                                        Настоящие Условия определяют права, обязанности и ответственность
                                        пользователей и оператора Платформы.
                                    </p>

                                    <p class="mt-4">
                                        Отдельные сервисы Платформы могут регулироваться дополнительными
                                        правилами, которые являются частью настоящих Условий.
                                    </p>

                                    <div
                                        class="mt-5 rounded-xl border border-gray-200
                                               bg-gray-50 p-5
                                               dark:border-gray-700
                                               dark:bg-gray-800/50"
                                    >
                                        <p class="font-medium text-gray-900 dark:text-white">
                                            Оператор Платформы:
                                        </p>

                                        <div class="mt-3 space-y-1 text-sm">
                                            <p>[Полное наименование организации / ИП]</p>
                                            <p>БИН/ИИН: [указать]</p>
                                            <p>Адрес: [указать]</p>
                                            <p>Электронная почта: [указать]</p>
                                        </div>
                                    </div>
                                </section>

                                <!-- 2 -->
                                <section>
                                    <h2 class="text-xl font-semibold text-gray-900 dark:text-white">
                                        2. Использование Платформы
                                    </h2>

                                    <p class="mt-4">
                                        Пользователь вправе использовать функции Платформы исключительно
                                        законными способами.
                                    </p>

                                    <p class="mt-4">
                                        Запрещается использовать Платформу для нарушения законодательства,
                                        распространения вредоносного программного обеспечения, мошенничества
                                        или иных действий, способных причинить ущерб пользователям или
                                        оператору.
                                    </p>
                                </section>

                                <!-- 3 -->
                                <section>
                                    <h2 class="text-xl font-semibold text-gray-900 dark:text-white">
                                        3. Регистрация и учетная запись
                                    </h2>

                                    <p class="mt-4">
                                        Для использования отдельных функций требуется создание учетной записи.
                                    </p>

                                    <ul class="mt-4 list-disc space-y-2 pl-6 marker:text-gray-400">
                                        <li>предоставлять достоверную информацию;</li>
                                        <li>сохранять конфиденциальность пароля;</li>
                                        <li>не передавать учетную запись третьим лицам;</li>
                                        <li>незамедлительно сообщать о подозрительной активности.</li>
                                    </ul>
                                </section>

                                <!-- 4 -->
                                <section>
                                    <h2 class="text-xl font-semibold text-gray-900 dark:text-white">
                                        4. Интернет магазин
                                    </h2>

                                    <p class="mt-4">
                                        Платформа предоставляет возможность размещения товаров,
                                        взаимодействия покупателей и продавцов, а также использования
                                        сопутствующих функций каталога.
                                    </p>

                                    <p class="mt-4">
                                        Размещение товаров должно соответствовать законодательству
                                        и внутренним правилам Платформы.
                                    </p>

                                    <p class="mt-4">
                                        Оператор вправе применять модерацию, ограничивать публикацию
                                        материалов или удалять нарушения правил использования.
                                    </p>
                                </section>

                                <!-- 5 -->
                                <section>
                                    <h2 class="text-xl font-semibold text-gray-900 dark:text-white">
                                        5. Онлайн-школа
                                    </h2>

                                    <p class="mt-4">
                                        Образовательные материалы предоставляются пользователям
                                        в соответствии с условиями доступа к конкретному курсу.
                                    </p>

                                    <p class="mt-4">
                                        Если курс является платным, доступ предоставляется после выполнения
                                        условий приобретения.
                                    </p>

                                    <p class="mt-4">
                                        Запрещается незаконное копирование, распространение или передача
                                        учебных материалов третьим лицам.
                                    </p>
                                </section>

                                <!-- 6 -->
                                <section>
                                    <h2 class="text-xl font-semibold text-gray-900 dark:text-white">
                                        6. Контент пользователей
                                    </h2>

                                    <p class="mt-4">
                                        Пользователь может размещать отзывы, комментарии,
                                        информацию о товарах и другие материалы.
                                    </p>

                                    <ul class="mt-4 list-disc space-y-2 pl-6 marker:text-gray-400">
                                        <li>не нарушать права третьих лиц;</li>
                                        <li>не публиковать незаконный контент;</li>
                                        <li>не распространять ложную информацию;</li>
                                        <li>не использовать Платформу для спама.</li>
                                    </ul>

                                    <p class="mt-4">
                                        Оператор вправе удалять материалы, нарушающие настоящие Условия.
                                    </p>
                                </section>

                                <!-- 7 -->
                                <section>
                                    <h2 class="text-xl font-semibold text-gray-900 dark:text-white">
                                        7. Интеллектуальная собственность
                                    </h2>

                                    <p class="mt-4">
                                        Программное обеспечение, дизайн, логотипы, тексты,
                                        изображения и другие элементы Платформы охраняются
                                        законодательством об интеллектуальной собственности.
                                    </p>

                                    <p class="mt-4">
                                        Использование материалов допускается только в пределах,
                                        предусмотренных законодательством или письменным разрешением правообладателя.
                                    </p>
                                </section>

                                <!-- 8 -->
                                <section>
                                    <h2 class="text-xl font-semibold text-gray-900 dark:text-white">
                                        8. Платежи
                                    </h2>

                                    <p class="mt-4">
                                        Если Платформа предусматривает оплату товаров или услуг,
                                        расчеты могут осуществляться через сторонние платежные сервисы.
                                    </p>

                                    <p class="mt-4">
                                        Возвраты денежных средств осуществляются в порядке,
                                        предусмотренном законодательством и условиями соответствующей услуги.
                                    </p>
                                </section>

                                <!-- 9 -->
                                <section>
                                    <h2 class="text-xl font-semibold text-gray-900 dark:text-white">
                                        9. Ограничение ответственности
                                    </h2>

                                    <p class="mt-4">
                                        Платформа предоставляется по принципу «как есть».
                                    </p>

                                    <p class="mt-4">
                                        Мы стремимся обеспечивать стабильную работу сервиса,
                                        однако не гарантируем отсутствие технических сбоев,
                                        временной недоступности или ошибок.
                                    </p>

                                    <p class="mt-4">
                                        Пользователь самостоятельно принимает решения,
                                        основанные на информации, размещенной на Платформе.
                                    </p>
                                </section>

                                <!-- 10 -->
                                <section>
                                    <h2 class="text-xl font-semibold text-gray-900 dark:text-white">
                                        10. Безопасность
                                    </h2>

                                    <p class="mt-4">
                                        Пользователь обязуется не предпринимать действий,
                                        направленных на нарушение безопасности Платформы.
                                    </p>

                                    <ul class="mt-4 list-disc space-y-2 pl-6 marker:text-gray-400">
                                        <li>подбор паролей;</li>
                                        <li>обход механизмов защиты;</li>
                                        <li>автоматизированные атаки;</li>
                                        <li>вмешательство в работу серверов.</li>
                                    </ul>
                                </section>

                                <!-- 11 -->
                                <section>
                                    <h2 class="text-xl font-semibold text-gray-900 dark:text-white">
                                        11. Приостановление доступа
                                    </h2>

                                    <p class="mt-4">
                                        Оператор вправе ограничить или прекратить доступ пользователя
                                        при нарушении настоящих Условий либо требований законодательства.
                                    </p>

                                    <p class="mt-4">
                                        Ограничение доступа может быть временным или постоянным,
                                        в зависимости от характера нарушения.
                                    </p>
                                </section>

                                <!-- 12 -->
                                <section>
                                    <h2 class="text-xl font-semibold text-gray-900 dark:text-white">
                                        12. Изменение условий
                                    </h2>

                                    <p class="mt-4">
                                        Настоящие Условия обслуживания могут периодически обновляться.
                                    </p>

                                    <p class="mt-4">
                                        Актуальная редакция публикуется на этой странице.
                                    </p>

                                    <p class="mt-4">
                                        Продолжение использования Платформы после вступления изменений
                                        в силу означает принятие новой редакции Условий.
                                    </p>
                                </section>

                                <!-- 13 -->
                                <section>
                                    <h2 class="text-xl font-semibold text-gray-900 dark:text-white">
                                        13. Применимое право
                                    </h2>

                                    <p class="mt-4">
                                        Настоящие Условия применяются с учетом законодательства,
                                        регулирующего деятельность Платформы и соответствующие правоотношения.
                                    </p>

                                    <p class="mt-4">
                                        Споры подлежат разрешению в порядке,
                                        установленном применимым законодательством.
                                    </p>
                                </section>

                                <!-- 14 -->
                                <section>
                                    <h2 class="text-xl font-semibold text-gray-900 dark:text-white">
                                        14. Контактная информация
                                    </h2>

                                    <p class="mt-4">
                                        По вопросам использования Платформы можно обратиться:
                                    </p>

                                    <div
                                        class="mt-5 rounded-xl border border-gray-200
                                               bg-gray-50 p-5
                                               dark:border-gray-700
                                               dark:bg-gray-800/50"
                                    >
                                        <div class="space-y-2">
                                            <p class="font-medium text-gray-900 dark:text-white">
                                                [Полное наименование организации / ИП]
                                            </p>

                                            <p>
                                                <span class="font-medium">Адрес:</span>
                                                [указать]
                                            </p>

                                            <p>
                                                <span class="font-medium">Электронная почта:</span>
                                                [указать]
                                            </p>

                                            <p>
                                                <span class="font-medium">Телефон:</span>
                                                [указать]
                                            </p>
                                        </div>
                                    </div>

                                    <p class="mt-5">
                                        Мы стремимся своевременно рассматривать обращения пользователей
                                        и предоставлять ответы по вопросам использования Платформы.
                                    </p>
                                </section>
                            </div>
                        </article>

                        <!-- Нижняя подпись -->
                        <p
                            class="mt-6 pb-6 text-center text-xs
                                   text-gray-500 dark:text-gray-500"
                        >
                            © {{ new Date().getFullYear() }}.
                            Все права защищены.
                        </p>
                    </div>
                </div>
            </div>
        </main>

        <!-- Подвал -->
        <FooterBlog />

        <!-- Прогресс -->
        <Progress />
    </DefaultLayout>
</template>
