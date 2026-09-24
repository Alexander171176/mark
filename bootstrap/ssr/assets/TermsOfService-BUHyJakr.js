import { unref, withCtx, createVNode, toDisplayString, createTextVNode, useSSRContext } from "vue";
import { ssrRenderComponent, ssrInterpolate } from "vue/server-renderer";
import { Head } from "@inertiajs/vue3";
import { useI18n } from "vue-i18n";
import { _ as _sfc_main$2, a as _sfc_main$3, P as Progress } from "./Progress-CyfOKcqP.js";
import { _ as _sfc_main$1 } from "./DefaultLayout-6zxGxPtK.js";
import "@inertiajs/inertia";
import "./ThemeToggle-DA16u1ft.js";
import "@vueuse/core";
import "./LocaleSelectOption-BeLdazeX.js";
import "./Checkbox-CgE3PSwb.js";
import "./TextInput-CCxUFX3K.js";
import "./InputLabel-Ds0Eo91B.js";
import "./PrimaryButton-D7EZDGT_.js";
import "./_plugin-vue_export-helper-1tPrXgE0.js";
import "axios";
const _sfc_main = {
  __name: "TermsOfService",
  __ssrInlineRender: true,
  props: {
    terms: String
  },
  setup(__props) {
    const { t } = useI18n();
    return (_ctx, _push, _parent, _attrs) => {
      _push(`<!--[-->`);
      _push(ssrRenderComponent(unref(Head), {
        title: unref(t)("termsOfService")
      }, null, _parent));
      _push(ssrRenderComponent(_sfc_main$1, null, {
        default: withCtx((_, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(ssrRenderComponent(_sfc_main$2, null, null, _parent2, _scopeId));
            _push2(`<main class="min-h-screen px-1 lg:px-6 max-w-full"${_scopeId}><div class="mx-auto tracking-wider pt-20 lg:pt-44"${_scopeId}><div class="ext-color w-full min-w-0 py-3 px-1 flex flex-col lg:flex-row gap-4 rounded-3xl border-2 border-slate-300 dark:border-slate-500"${_scopeId}><div class="mx-auto flex w-full max-w-6xl flex-col items-center px-4 py-8 sm:px-6 lg:px-8"${_scopeId}><article class="w-full max-w-3xl rounded-2xl border border-gray-200 bg-white px-6 py-8 shadow-xl dark:border-gray-800 dark:bg-gray-900 sm:px-10 sm:py-10 lg:px-12 lg:py-12"${_scopeId}><header class="mb-10 border-b border-gray-200 pb-8 dark:border-gray-800"${_scopeId}><h1 class="text-2xl font-bold tracking-tight text-gray-900 dark:text-white sm:text-3xl text-center"${_scopeId}>${ssrInterpolate(unref(t)("termsOfService"))}</h1><p class="mt-3 text-sm text-center text-gray-500 dark:text-gray-400"${_scopeId}> Дата последнего обновления: 20 сентября 2026 года </p></header><div class="space-y-10 text-base leading-7 text-gray-700 dark:text-gray-300"${_scopeId}><section${_scopeId}><p${_scopeId}> Настоящие Условия обслуживания регулируют порядок использования сайта и сервисов Платформы (далее — «Сайт», «Платформа»), включая интернет магазин, онлайн-школу, информационные материалы, личный кабинет пользователя и другие доступные функции. </p><p class="mt-4"${_scopeId}> Используя Платформу, пользователь подтверждает, что ознакомился с настоящими Условиями обслуживания и принимает их в полном объеме. </p></section><section${_scopeId}><h2 class="text-xl font-semibold text-gray-900 dark:text-white"${_scopeId}> 1. Общие положения </h2><p class="mt-4"${_scopeId}> Настоящие Условия определяют права, обязанности и ответственность пользователей и оператора Платформы. </p><p class="mt-4"${_scopeId}> Отдельные сервисы Платформы могут регулироваться дополнительными правилами, которые являются частью настоящих Условий. </p><div class="mt-5 rounded-xl border border-gray-200 bg-gray-50 p-5 dark:border-gray-700 dark:bg-gray-800/50"${_scopeId}><p class="font-medium text-gray-900 dark:text-white"${_scopeId}> Оператор Платформы: </p><div class="mt-3 space-y-1 text-sm"${_scopeId}><p${_scopeId}>[Полное наименование организации / ИП]</p><p${_scopeId}>БИН/ИИН: [указать]</p><p${_scopeId}>Адрес: [указать]</p><p${_scopeId}>Электронная почта: [указать]</p></div></div></section><section${_scopeId}><h2 class="text-xl font-semibold text-gray-900 dark:text-white"${_scopeId}> 2. Использование Платформы </h2><p class="mt-4"${_scopeId}> Пользователь вправе использовать функции Платформы исключительно законными способами. </p><p class="mt-4"${_scopeId}> Запрещается использовать Платформу для нарушения законодательства, распространения вредоносного программного обеспечения, мошенничества или иных действий, способных причинить ущерб пользователям или оператору. </p></section><section${_scopeId}><h2 class="text-xl font-semibold text-gray-900 dark:text-white"${_scopeId}> 3. Регистрация и учетная запись </h2><p class="mt-4"${_scopeId}> Для использования отдельных функций требуется создание учетной записи. </p><ul class="mt-4 list-disc space-y-2 pl-6 marker:text-gray-400"${_scopeId}><li${_scopeId}>предоставлять достоверную информацию;</li><li${_scopeId}>сохранять конфиденциальность пароля;</li><li${_scopeId}>не передавать учетную запись третьим лицам;</li><li${_scopeId}>незамедлительно сообщать о подозрительной активности.</li></ul></section><section${_scopeId}><h2 class="text-xl font-semibold text-gray-900 dark:text-white"${_scopeId}> 4. Интернет магазин </h2><p class="mt-4"${_scopeId}> Платформа предоставляет возможность размещения товаров, взаимодействия покупателей и продавцов, а также использования сопутствующих функций каталога. </p><p class="mt-4"${_scopeId}> Размещение товаров должно соответствовать законодательству и внутренним правилам Платформы. </p><p class="mt-4"${_scopeId}> Оператор вправе применять модерацию, ограничивать публикацию материалов или удалять нарушения правил использования. </p></section><section${_scopeId}><h2 class="text-xl font-semibold text-gray-900 dark:text-white"${_scopeId}> 5. Онлайн-школа </h2><p class="mt-4"${_scopeId}> Образовательные материалы предоставляются пользователям в соответствии с условиями доступа к конкретному курсу. </p><p class="mt-4"${_scopeId}> Если курс является платным, доступ предоставляется после выполнения условий приобретения. </p><p class="mt-4"${_scopeId}> Запрещается незаконное копирование, распространение или передача учебных материалов третьим лицам. </p></section><section${_scopeId}><h2 class="text-xl font-semibold text-gray-900 dark:text-white"${_scopeId}> 6. Контент пользователей </h2><p class="mt-4"${_scopeId}> Пользователь может размещать отзывы, комментарии, информацию о товарах и другие материалы. </p><ul class="mt-4 list-disc space-y-2 pl-6 marker:text-gray-400"${_scopeId}><li${_scopeId}>не нарушать права третьих лиц;</li><li${_scopeId}>не публиковать незаконный контент;</li><li${_scopeId}>не распространять ложную информацию;</li><li${_scopeId}>не использовать Платформу для спама.</li></ul><p class="mt-4"${_scopeId}> Оператор вправе удалять материалы, нарушающие настоящие Условия. </p></section><section${_scopeId}><h2 class="text-xl font-semibold text-gray-900 dark:text-white"${_scopeId}> 7. Интеллектуальная собственность </h2><p class="mt-4"${_scopeId}> Программное обеспечение, дизайн, логотипы, тексты, изображения и другие элементы Платформы охраняются законодательством об интеллектуальной собственности. </p><p class="mt-4"${_scopeId}> Использование материалов допускается только в пределах, предусмотренных законодательством или письменным разрешением правообладателя. </p></section><section${_scopeId}><h2 class="text-xl font-semibold text-gray-900 dark:text-white"${_scopeId}> 8. Платежи </h2><p class="mt-4"${_scopeId}> Если Платформа предусматривает оплату товаров или услуг, расчеты могут осуществляться через сторонние платежные сервисы. </p><p class="mt-4"${_scopeId}> Возвраты денежных средств осуществляются в порядке, предусмотренном законодательством и условиями соответствующей услуги. </p></section><section${_scopeId}><h2 class="text-xl font-semibold text-gray-900 dark:text-white"${_scopeId}> 9. Ограничение ответственности </h2><p class="mt-4"${_scopeId}> Платформа предоставляется по принципу «как есть». </p><p class="mt-4"${_scopeId}> Мы стремимся обеспечивать стабильную работу сервиса, однако не гарантируем отсутствие технических сбоев, временной недоступности или ошибок. </p><p class="mt-4"${_scopeId}> Пользователь самостоятельно принимает решения, основанные на информации, размещенной на Платформе. </p></section><section${_scopeId}><h2 class="text-xl font-semibold text-gray-900 dark:text-white"${_scopeId}> 10. Безопасность </h2><p class="mt-4"${_scopeId}> Пользователь обязуется не предпринимать действий, направленных на нарушение безопасности Платформы. </p><ul class="mt-4 list-disc space-y-2 pl-6 marker:text-gray-400"${_scopeId}><li${_scopeId}>подбор паролей;</li><li${_scopeId}>обход механизмов защиты;</li><li${_scopeId}>автоматизированные атаки;</li><li${_scopeId}>вмешательство в работу серверов.</li></ul></section><section${_scopeId}><h2 class="text-xl font-semibold text-gray-900 dark:text-white"${_scopeId}> 11. Приостановление доступа </h2><p class="mt-4"${_scopeId}> Оператор вправе ограничить или прекратить доступ пользователя при нарушении настоящих Условий либо требований законодательства. </p><p class="mt-4"${_scopeId}> Ограничение доступа может быть временным или постоянным, в зависимости от характера нарушения. </p></section><section${_scopeId}><h2 class="text-xl font-semibold text-gray-900 dark:text-white"${_scopeId}> 12. Изменение условий </h2><p class="mt-4"${_scopeId}> Настоящие Условия обслуживания могут периодически обновляться. </p><p class="mt-4"${_scopeId}> Актуальная редакция публикуется на этой странице. </p><p class="mt-4"${_scopeId}> Продолжение использования Платформы после вступления изменений в силу означает принятие новой редакции Условий. </p></section><section${_scopeId}><h2 class="text-xl font-semibold text-gray-900 dark:text-white"${_scopeId}> 13. Применимое право </h2><p class="mt-4"${_scopeId}> Настоящие Условия применяются с учетом законодательства, регулирующего деятельность Платформы и соответствующие правоотношения. </p><p class="mt-4"${_scopeId}> Споры подлежат разрешению в порядке, установленном применимым законодательством. </p></section><section${_scopeId}><h2 class="text-xl font-semibold text-gray-900 dark:text-white"${_scopeId}> 14. Контактная информация </h2><p class="mt-4"${_scopeId}> По вопросам использования Платформы можно обратиться: </p><div class="mt-5 rounded-xl border border-gray-200 bg-gray-50 p-5 dark:border-gray-700 dark:bg-gray-800/50"${_scopeId}><div class="space-y-2"${_scopeId}><p class="font-medium text-gray-900 dark:text-white"${_scopeId}> [Полное наименование организации / ИП] </p><p${_scopeId}><span class="font-medium"${_scopeId}>Адрес:</span> [указать] </p><p${_scopeId}><span class="font-medium"${_scopeId}>Электронная почта:</span> [указать] </p><p${_scopeId}><span class="font-medium"${_scopeId}>Телефон:</span> [указать] </p></div></div><p class="mt-5"${_scopeId}> Мы стремимся своевременно рассматривать обращения пользователей и предоставлять ответы по вопросам использования Платформы. </p></section></div></article><p class="mt-6 pb-6 text-center text-xs text-gray-500 dark:text-gray-500"${_scopeId}> © ${ssrInterpolate((/* @__PURE__ */ new Date()).getFullYear())}. Все права защищены. </p></div></div></div></main>`);
            _push2(ssrRenderComponent(_sfc_main$3, null, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(Progress, null, null, _parent2, _scopeId));
          } else {
            return [
              createVNode(_sfc_main$2),
              createVNode("main", { class: "min-h-screen px-1 lg:px-6 max-w-full" }, [
                createVNode("div", { class: "mx-auto tracking-wider pt-20 lg:pt-44" }, [
                  createVNode("div", { class: "ext-color w-full min-w-0 py-3 px-1 flex flex-col lg:flex-row gap-4 rounded-3xl border-2 border-slate-300 dark:border-slate-500" }, [
                    createVNode("div", { class: "mx-auto flex w-full max-w-6xl flex-col items-center px-4 py-8 sm:px-6 lg:px-8" }, [
                      createVNode("article", { class: "w-full max-w-3xl rounded-2xl border border-gray-200 bg-white px-6 py-8 shadow-xl dark:border-gray-800 dark:bg-gray-900 sm:px-10 sm:py-10 lg:px-12 lg:py-12" }, [
                        createVNode("header", { class: "mb-10 border-b border-gray-200 pb-8 dark:border-gray-800" }, [
                          createVNode("h1", { class: "text-2xl font-bold tracking-tight text-gray-900 dark:text-white sm:text-3xl text-center" }, toDisplayString(unref(t)("termsOfService")), 1),
                          createVNode("p", { class: "mt-3 text-sm text-center text-gray-500 dark:text-gray-400" }, " Дата последнего обновления: 20 сентября 2026 года ")
                        ]),
                        createVNode("div", { class: "space-y-10 text-base leading-7 text-gray-700 dark:text-gray-300" }, [
                          createVNode("section", null, [
                            createVNode("p", null, " Настоящие Условия обслуживания регулируют порядок использования сайта и сервисов Платформы (далее — «Сайт», «Платформа»), включая интернет магазин, онлайн-школу, информационные материалы, личный кабинет пользователя и другие доступные функции. "),
                            createVNode("p", { class: "mt-4" }, " Используя Платформу, пользователь подтверждает, что ознакомился с настоящими Условиями обслуживания и принимает их в полном объеме. ")
                          ]),
                          createVNode("section", null, [
                            createVNode("h2", { class: "text-xl font-semibold text-gray-900 dark:text-white" }, " 1. Общие положения "),
                            createVNode("p", { class: "mt-4" }, " Настоящие Условия определяют права, обязанности и ответственность пользователей и оператора Платформы. "),
                            createVNode("p", { class: "mt-4" }, " Отдельные сервисы Платформы могут регулироваться дополнительными правилами, которые являются частью настоящих Условий. "),
                            createVNode("div", { class: "mt-5 rounded-xl border border-gray-200 bg-gray-50 p-5 dark:border-gray-700 dark:bg-gray-800/50" }, [
                              createVNode("p", { class: "font-medium text-gray-900 dark:text-white" }, " Оператор Платформы: "),
                              createVNode("div", { class: "mt-3 space-y-1 text-sm" }, [
                                createVNode("p", null, "[Полное наименование организации / ИП]"),
                                createVNode("p", null, "БИН/ИИН: [указать]"),
                                createVNode("p", null, "Адрес: [указать]"),
                                createVNode("p", null, "Электронная почта: [указать]")
                              ])
                            ])
                          ]),
                          createVNode("section", null, [
                            createVNode("h2", { class: "text-xl font-semibold text-gray-900 dark:text-white" }, " 2. Использование Платформы "),
                            createVNode("p", { class: "mt-4" }, " Пользователь вправе использовать функции Платформы исключительно законными способами. "),
                            createVNode("p", { class: "mt-4" }, " Запрещается использовать Платформу для нарушения законодательства, распространения вредоносного программного обеспечения, мошенничества или иных действий, способных причинить ущерб пользователям или оператору. ")
                          ]),
                          createVNode("section", null, [
                            createVNode("h2", { class: "text-xl font-semibold text-gray-900 dark:text-white" }, " 3. Регистрация и учетная запись "),
                            createVNode("p", { class: "mt-4" }, " Для использования отдельных функций требуется создание учетной записи. "),
                            createVNode("ul", { class: "mt-4 list-disc space-y-2 pl-6 marker:text-gray-400" }, [
                              createVNode("li", null, "предоставлять достоверную информацию;"),
                              createVNode("li", null, "сохранять конфиденциальность пароля;"),
                              createVNode("li", null, "не передавать учетную запись третьим лицам;"),
                              createVNode("li", null, "незамедлительно сообщать о подозрительной активности.")
                            ])
                          ]),
                          createVNode("section", null, [
                            createVNode("h2", { class: "text-xl font-semibold text-gray-900 dark:text-white" }, " 4. Интернет магазин "),
                            createVNode("p", { class: "mt-4" }, " Платформа предоставляет возможность размещения товаров, взаимодействия покупателей и продавцов, а также использования сопутствующих функций каталога. "),
                            createVNode("p", { class: "mt-4" }, " Размещение товаров должно соответствовать законодательству и внутренним правилам Платформы. "),
                            createVNode("p", { class: "mt-4" }, " Оператор вправе применять модерацию, ограничивать публикацию материалов или удалять нарушения правил использования. ")
                          ]),
                          createVNode("section", null, [
                            createVNode("h2", { class: "text-xl font-semibold text-gray-900 dark:text-white" }, " 5. Онлайн-школа "),
                            createVNode("p", { class: "mt-4" }, " Образовательные материалы предоставляются пользователям в соответствии с условиями доступа к конкретному курсу. "),
                            createVNode("p", { class: "mt-4" }, " Если курс является платным, доступ предоставляется после выполнения условий приобретения. "),
                            createVNode("p", { class: "mt-4" }, " Запрещается незаконное копирование, распространение или передача учебных материалов третьим лицам. ")
                          ]),
                          createVNode("section", null, [
                            createVNode("h2", { class: "text-xl font-semibold text-gray-900 dark:text-white" }, " 6. Контент пользователей "),
                            createVNode("p", { class: "mt-4" }, " Пользователь может размещать отзывы, комментарии, информацию о товарах и другие материалы. "),
                            createVNode("ul", { class: "mt-4 list-disc space-y-2 pl-6 marker:text-gray-400" }, [
                              createVNode("li", null, "не нарушать права третьих лиц;"),
                              createVNode("li", null, "не публиковать незаконный контент;"),
                              createVNode("li", null, "не распространять ложную информацию;"),
                              createVNode("li", null, "не использовать Платформу для спама.")
                            ]),
                            createVNode("p", { class: "mt-4" }, " Оператор вправе удалять материалы, нарушающие настоящие Условия. ")
                          ]),
                          createVNode("section", null, [
                            createVNode("h2", { class: "text-xl font-semibold text-gray-900 dark:text-white" }, " 7. Интеллектуальная собственность "),
                            createVNode("p", { class: "mt-4" }, " Программное обеспечение, дизайн, логотипы, тексты, изображения и другие элементы Платформы охраняются законодательством об интеллектуальной собственности. "),
                            createVNode("p", { class: "mt-4" }, " Использование материалов допускается только в пределах, предусмотренных законодательством или письменным разрешением правообладателя. ")
                          ]),
                          createVNode("section", null, [
                            createVNode("h2", { class: "text-xl font-semibold text-gray-900 dark:text-white" }, " 8. Платежи "),
                            createVNode("p", { class: "mt-4" }, " Если Платформа предусматривает оплату товаров или услуг, расчеты могут осуществляться через сторонние платежные сервисы. "),
                            createVNode("p", { class: "mt-4" }, " Возвраты денежных средств осуществляются в порядке, предусмотренном законодательством и условиями соответствующей услуги. ")
                          ]),
                          createVNode("section", null, [
                            createVNode("h2", { class: "text-xl font-semibold text-gray-900 dark:text-white" }, " 9. Ограничение ответственности "),
                            createVNode("p", { class: "mt-4" }, " Платформа предоставляется по принципу «как есть». "),
                            createVNode("p", { class: "mt-4" }, " Мы стремимся обеспечивать стабильную работу сервиса, однако не гарантируем отсутствие технических сбоев, временной недоступности или ошибок. "),
                            createVNode("p", { class: "mt-4" }, " Пользователь самостоятельно принимает решения, основанные на информации, размещенной на Платформе. ")
                          ]),
                          createVNode("section", null, [
                            createVNode("h2", { class: "text-xl font-semibold text-gray-900 dark:text-white" }, " 10. Безопасность "),
                            createVNode("p", { class: "mt-4" }, " Пользователь обязуется не предпринимать действий, направленных на нарушение безопасности Платформы. "),
                            createVNode("ul", { class: "mt-4 list-disc space-y-2 pl-6 marker:text-gray-400" }, [
                              createVNode("li", null, "подбор паролей;"),
                              createVNode("li", null, "обход механизмов защиты;"),
                              createVNode("li", null, "автоматизированные атаки;"),
                              createVNode("li", null, "вмешательство в работу серверов.")
                            ])
                          ]),
                          createVNode("section", null, [
                            createVNode("h2", { class: "text-xl font-semibold text-gray-900 dark:text-white" }, " 11. Приостановление доступа "),
                            createVNode("p", { class: "mt-4" }, " Оператор вправе ограничить или прекратить доступ пользователя при нарушении настоящих Условий либо требований законодательства. "),
                            createVNode("p", { class: "mt-4" }, " Ограничение доступа может быть временным или постоянным, в зависимости от характера нарушения. ")
                          ]),
                          createVNode("section", null, [
                            createVNode("h2", { class: "text-xl font-semibold text-gray-900 dark:text-white" }, " 12. Изменение условий "),
                            createVNode("p", { class: "mt-4" }, " Настоящие Условия обслуживания могут периодически обновляться. "),
                            createVNode("p", { class: "mt-4" }, " Актуальная редакция публикуется на этой странице. "),
                            createVNode("p", { class: "mt-4" }, " Продолжение использования Платформы после вступления изменений в силу означает принятие новой редакции Условий. ")
                          ]),
                          createVNode("section", null, [
                            createVNode("h2", { class: "text-xl font-semibold text-gray-900 dark:text-white" }, " 13. Применимое право "),
                            createVNode("p", { class: "mt-4" }, " Настоящие Условия применяются с учетом законодательства, регулирующего деятельность Платформы и соответствующие правоотношения. "),
                            createVNode("p", { class: "mt-4" }, " Споры подлежат разрешению в порядке, установленном применимым законодательством. ")
                          ]),
                          createVNode("section", null, [
                            createVNode("h2", { class: "text-xl font-semibold text-gray-900 dark:text-white" }, " 14. Контактная информация "),
                            createVNode("p", { class: "mt-4" }, " По вопросам использования Платформы можно обратиться: "),
                            createVNode("div", { class: "mt-5 rounded-xl border border-gray-200 bg-gray-50 p-5 dark:border-gray-700 dark:bg-gray-800/50" }, [
                              createVNode("div", { class: "space-y-2" }, [
                                createVNode("p", { class: "font-medium text-gray-900 dark:text-white" }, " [Полное наименование организации / ИП] "),
                                createVNode("p", null, [
                                  createVNode("span", { class: "font-medium" }, "Адрес:"),
                                  createTextVNode(" [указать] ")
                                ]),
                                createVNode("p", null, [
                                  createVNode("span", { class: "font-medium" }, "Электронная почта:"),
                                  createTextVNode(" [указать] ")
                                ]),
                                createVNode("p", null, [
                                  createVNode("span", { class: "font-medium" }, "Телефон:"),
                                  createTextVNode(" [указать] ")
                                ])
                              ])
                            ]),
                            createVNode("p", { class: "mt-5" }, " Мы стремимся своевременно рассматривать обращения пользователей и предоставлять ответы по вопросам использования Платформы. ")
                          ])
                        ])
                      ]),
                      createVNode("p", { class: "mt-6 pb-6 text-center text-xs text-gray-500 dark:text-gray-500" }, " © " + toDisplayString((/* @__PURE__ */ new Date()).getFullYear()) + ". Все права защищены. ", 1)
                    ])
                  ])
                ])
              ]),
              createVNode(_sfc_main$3),
              createVNode(Progress)
            ];
          }
        }),
        _: 1
      }, _parent));
      _push(`<!--]-->`);
    };
  }
};
const _sfc_setup = _sfc_main.setup;
_sfc_main.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Pages/TermsOfService.vue");
  return _sfc_setup ? _sfc_setup(props, ctx) : void 0;
};
export {
  _sfc_main as default
};
