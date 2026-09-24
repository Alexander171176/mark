import { ref, computed, mergeProps, withCtx, createVNode, createBlock, openBlock, createTextVNode, createCommentVNode, toDisplayString, Fragment, withDirectives, vModelText, renderList, useSSRContext } from "vue";
import { ssrRenderComponent, ssrInterpolate, ssrRenderAttr, ssrRenderList } from "vue/server-renderer";
import { A as AdminLayout } from "./AdminLayout-03yd3nxC.js";
import { T as TitlePage } from "./TitlePage-CBZZ8YZB.js";
import "@inertiajs/vue3";
import "vue-toastification";
import "./LocaleSelectOption-BeLdazeX.js";
import "vue-i18n";
import "./ResponsiveNavLink-gtte0z5g.js";
import "./_plugin-vue_export-helper-1tPrXgE0.js";
import "./ThemeToggle-DA16u1ft.js";
import "@vueuse/core";
import "vuedraggable";
import "./ScrollButtons-2xyFJfJ4.js";
import "@fortawesome/vue-fontawesome";
import "@fortawesome/fontawesome-svg-core";
import "@fortawesome/free-solid-svg-icons";
import "@inertiajs/inertia";
import "vue-smooth-dnd";
const _sfc_main = {
  __name: "EnvInfoPage",
  __ssrInlineRender: true,
  props: {
    env: {
      type: Array,
      default: () => []
    }
  },
  setup(__props) {
    const props = __props;
    const search = ref("");
    const copiedKey = ref("");
    const filteredEnv = computed(() => {
      const query = search.value.trim().toLowerCase();
      if (!query) {
        return props.env;
      }
      return props.env.filter((entry) => {
        const key = String(
          (entry == null ? void 0 : entry.key) ?? ""
        ).toLowerCase();
        const value = String(
          (entry == null ? void 0 : entry.value) ?? ""
        ).toLowerCase();
        return key.includes(query) || value.includes(query);
      });
    });
    const copyValue = async (entry) => {
      try {
        await navigator.clipboard.writeText(
          String((entry == null ? void 0 : entry.value) ?? "")
        );
        copiedKey.value = entry.key;
        setTimeout(() => {
          if (copiedKey.value === entry.key) {
            copiedKey.value = "";
          }
        }, 1500);
      } catch (error) {
        console.error(
          "Failed to copy .env value:",
          error
        );
      }
    };
    return (_ctx, _push, _parent, _attrs) => {
      _push(ssrRenderComponent(AdminLayout, mergeProps({ title: "Configuration .env" }, _attrs), {
        header: withCtx((_, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(ssrRenderComponent(TitlePage, null, {
              default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                if (_push3) {
                  _push3(` Configuration .env `);
                } else {
                  return [
                    createTextVNode(" Configuration .env ")
                  ];
                }
              }),
              _: 1
            }, _parent2, _scopeId));
          } else {
            return [
              createVNode(TitlePage, null, {
                default: withCtx(() => [
                  createTextVNode(" Configuration .env ")
                ]),
                _: 1
              })
            ];
          }
        }),
        default: withCtx((_, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(`<div class="px-2 py-2 w-full max-w-12xl mx-auto"${_scopeId}><div class="p-4 bg-slate-50 dark:bg-slate-700 border border-blue-400 dark:border-blue-200 overflow-hidden shadow-md shadow-gray-500 dark:shadow-slate-400 bg-opacity-95 dark:bg-opacity-95"${_scopeId}><div class="mb-4 px-4 py-3 border border-amber-300 dark:border-amber-600 bg-amber-50 dark:bg-amber-900/20 rounded-md"${_scopeId}><div class="flex items-start gap-3"${_scopeId}><div class="shrink-0 flex items-center justify-center w-8 h-8 rounded-full bg-amber-100 dark:bg-amber-800 text-amber-700 dark:text-amber-200"${_scopeId}><svg class="w-5 h-5 fill-current" viewBox="0 0 24 24"${_scopeId}><path d="M11 17h2v-6h-2v6zm0-8h2V7h-2v2zm1-7C6.48 2 2 6.48 2 12s4.48 10 10 10
                                       10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8
                                       8-8 8 3.59 8 8-3.59 8-8 8z"${_scopeId}></path></svg></div><div${_scopeId}><div class="text-sm font-semibold text-amber-800 dark:text-amber-200"${_scopeId}> Конфигурация окружения </div><div class="mt-0.5 text-xs text-amber-700 dark:text-amber-300"${_scopeId}> Данные загружены непосредственно из файла <span class="font-mono font-semibold"${_scopeId}>.env</span>. Страница предназначена только для просмотра конфигурации сервера. </div></div></div></div><div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-3"${_scopeId}><div class="text-sm text-slate-600 dark:text-slate-300"${_scopeId}> Параметров: <span class="font-semibold text-indigo-700 dark:text-indigo-300"${_scopeId}>${ssrInterpolate(__props.env.length)}</span>`);
            if (search.value) {
              _push2(`<!--[--><span class="mx-1"${_scopeId}>·</span> Найдено: <span class="font-semibold text-sky-700 dark:text-sky-300"${_scopeId}>${ssrInterpolate(filteredEnv.value.length)}</span><!--]-->`);
            } else {
              _push2(`<!---->`);
            }
            _push2(`</div><div class="relative w-full sm:w-80"${_scopeId}><svg class="absolute left-2.5 top-1/2 -translate-y-1/2 w-4 h-4 fill-current text-slate-400" viewBox="0 0 24 24"${_scopeId}><path d="M9.5 3a6.5 6.5 0 1 0 3.98 11.64L19.85 21
                                   21 19.85l-6.36-6.37A6.5 6.5 0 0 0 9.5 3zm0
                                   2a4.5 4.5 0 1 1 0 9 4.5 4.5 0 0 1 0-9z"${_scopeId}></path></svg><input${ssrRenderAttr("value", search.value)} type="text" placeholder="Поиск по .env..." class="w-full pl-8 pr-8 py-1.5 text-sm font-mono border border-slate-300 dark:border-slate-500 rounded-md bg-white dark:bg-slate-800 text-slate-800 dark:text-slate-100 placeholder:text-slate-400 focus:outline-none focus:ring-1 focus:ring-sky-500 focus:border-sky-500"${_scopeId}>`);
            if (search.value) {
              _push2(`<button type="button" title="Очистить поиск" class="absolute right-2 top-1/2 -translate-y-1/2 text-slate-400 hover:text-red-500 transition"${_scopeId}> × </button>`);
            } else {
              _push2(`<!---->`);
            }
            _push2(`</div></div><div class="overflow-x-auto border border-slate-300 dark:border-slate-600 rounded-md shadow bg-white dark:bg-gray-900"${_scopeId}><table class="w-full text-left border-collapse text-sm"${_scopeId}><thead class="bg-slate-100 dark:bg-slate-800"${_scopeId}><tr${_scopeId}><th class="w-12 px-3 py-2 border-b border-slate-300 dark:border-slate-600 text-center text-xs font-semibold text-slate-500 dark:text-slate-400"${_scopeId}> # </th><th class="w-1/3 px-3 py-2 border-b border-slate-300 dark:border-slate-600 font-semibold text-indigo-700 dark:text-indigo-300"${_scopeId}> Key </th><th class="px-3 py-2 border-b border-slate-300 dark:border-slate-600 font-semibold text-indigo-700 dark:text-indigo-300"${_scopeId}> Value </th><th class="w-12 px-2 py-2 border-b border-slate-300 dark:border-slate-600"${_scopeId}></th></tr></thead><tbody${_scopeId}><!--[-->`);
            ssrRenderList(filteredEnv.value, (entry, index) => {
              _push2(`<tr class="border-b border-slate-200 dark:border-slate-700 last:border-b-0 hover:bg-sky-50 dark:hover:bg-slate-800/80 transition-colors"${_scopeId}><td class="px-3 py-1.5 text-center align-top text-xs text-slate-400 dark:text-slate-500"${_scopeId}>${ssrInterpolate(index + 1)}</td><td class="px-3 py-1.5 pr-5 align-top font-mono font-semibold text-sky-700 dark:text-sky-300 whitespace-nowrap"${_scopeId}>${ssrInterpolate(entry.key)}</td><td class="px-3 py-1.5 align-top font-mono break-all text-slate-800 dark:text-slate-100"${_scopeId}>`);
              if (entry.value !== "") {
                _push2(`<span${_scopeId}>${ssrInterpolate(entry.value)}</span>`);
              } else {
                _push2(`<span class="italic text-slate-400 dark:text-slate-500"${_scopeId}> empty </span>`);
              }
              _push2(`</td><td class="px-2 py-1 align-middle text-center"${_scopeId}><button type="button"${ssrRenderAttr(
                "title",
                copiedKey.value === entry.key ? "Скопировано" : "Копировать значение"
              )} class="w-7 h-7 inline-flex items-center justify-center rounded text-slate-400 hover:text-sky-600 hover:bg-sky-100 dark:hover:text-sky-300 dark:hover:bg-slate-700 transition"${_scopeId}>`);
              if (copiedKey.value !== entry.key) {
                _push2(`<svg class="w-4 h-4 fill-current" viewBox="0 0 24 24"${_scopeId}><path d="M16 1H4c-1.1 0-2 .9-2 2v14h2V3h12V1zm3
                                                   4H8c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2
                                                   2h11c1.1 0 2-.9 2-2V7c0-1.1-.9-2-2-2zm0
                                                   16H8V7h11v14z"${_scopeId}></path></svg>`);
              } else {
                _push2(`<svg class="w-4 h-4 fill-current text-green-600 dark:text-green-400" viewBox="0 0 24 24"${_scopeId}><path d="M9 16.17 4.83 12l-1.42 1.41L9
                                                   19 21 7l-1.41-1.41z"${_scopeId}></path></svg>`);
              }
              _push2(`</button></td></tr>`);
            });
            _push2(`<!--]-->`);
            if (!filteredEnv.value.length) {
              _push2(`<tr${_scopeId}><td colspan="4" class="px-4 py-8 text-center text-sm text-slate-500 dark:text-slate-400"${_scopeId}> Параметры не найдены </td></tr>`);
            } else {
              _push2(`<!---->`);
            }
            _push2(`</tbody></table></div><div class="mt-2 text-xs text-slate-500 dark:text-slate-400"${_scopeId}> Файл доступен только для просмотра. Изменение конфигурации через административную панель отключено. </div></div></div>`);
          } else {
            return [
              createVNode("div", { class: "px-2 py-2 w-full max-w-12xl mx-auto" }, [
                createVNode("div", { class: "p-4 bg-slate-50 dark:bg-slate-700 border border-blue-400 dark:border-blue-200 overflow-hidden shadow-md shadow-gray-500 dark:shadow-slate-400 bg-opacity-95 dark:bg-opacity-95" }, [
                  createVNode("div", { class: "mb-4 px-4 py-3 border border-amber-300 dark:border-amber-600 bg-amber-50 dark:bg-amber-900/20 rounded-md" }, [
                    createVNode("div", { class: "flex items-start gap-3" }, [
                      createVNode("div", { class: "shrink-0 flex items-center justify-center w-8 h-8 rounded-full bg-amber-100 dark:bg-amber-800 text-amber-700 dark:text-amber-200" }, [
                        (openBlock(), createBlock("svg", {
                          class: "w-5 h-5 fill-current",
                          viewBox: "0 0 24 24"
                        }, [
                          createVNode("path", { d: "M11 17h2v-6h-2v6zm0-8h2V7h-2v2zm1-7C6.48 2 2 6.48 2 12s4.48 10 10 10\n                                       10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8\n                                       8-8 8 3.59 8 8-3.59 8-8 8z" })
                        ]))
                      ]),
                      createVNode("div", null, [
                        createVNode("div", { class: "text-sm font-semibold text-amber-800 dark:text-amber-200" }, " Конфигурация окружения "),
                        createVNode("div", { class: "mt-0.5 text-xs text-amber-700 dark:text-amber-300" }, [
                          createTextVNode(" Данные загружены непосредственно из файла "),
                          createVNode("span", { class: "font-mono font-semibold" }, ".env"),
                          createTextVNode(". Страница предназначена только для просмотра конфигурации сервера. ")
                        ])
                      ])
                    ])
                  ]),
                  createVNode("div", { class: "flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-3" }, [
                    createVNode("div", { class: "text-sm text-slate-600 dark:text-slate-300" }, [
                      createTextVNode(" Параметров: "),
                      createVNode("span", { class: "font-semibold text-indigo-700 dark:text-indigo-300" }, toDisplayString(__props.env.length), 1),
                      search.value ? (openBlock(), createBlock(Fragment, { key: 0 }, [
                        createVNode("span", { class: "mx-1" }, "·"),
                        createTextVNode(" Найдено: "),
                        createVNode("span", { class: "font-semibold text-sky-700 dark:text-sky-300" }, toDisplayString(filteredEnv.value.length), 1)
                      ], 64)) : createCommentVNode("", true)
                    ]),
                    createVNode("div", { class: "relative w-full sm:w-80" }, [
                      (openBlock(), createBlock("svg", {
                        class: "absolute left-2.5 top-1/2 -translate-y-1/2 w-4 h-4 fill-current text-slate-400",
                        viewBox: "0 0 24 24"
                      }, [
                        createVNode("path", { d: "M9.5 3a6.5 6.5 0 1 0 3.98 11.64L19.85 21\n                                   21 19.85l-6.36-6.37A6.5 6.5 0 0 0 9.5 3zm0\n                                   2a4.5 4.5 0 1 1 0 9 4.5 4.5 0 0 1 0-9z" })
                      ])),
                      withDirectives(createVNode("input", {
                        "onUpdate:modelValue": ($event) => search.value = $event,
                        type: "text",
                        placeholder: "Поиск по .env...",
                        class: "w-full pl-8 pr-8 py-1.5 text-sm font-mono border border-slate-300 dark:border-slate-500 rounded-md bg-white dark:bg-slate-800 text-slate-800 dark:text-slate-100 placeholder:text-slate-400 focus:outline-none focus:ring-1 focus:ring-sky-500 focus:border-sky-500"
                      }, null, 8, ["onUpdate:modelValue"]), [
                        [vModelText, search.value]
                      ]),
                      search.value ? (openBlock(), createBlock("button", {
                        key: 0,
                        type: "button",
                        title: "Очистить поиск",
                        class: "absolute right-2 top-1/2 -translate-y-1/2 text-slate-400 hover:text-red-500 transition",
                        onClick: ($event) => search.value = ""
                      }, " × ", 8, ["onClick"])) : createCommentVNode("", true)
                    ])
                  ]),
                  createVNode("div", { class: "overflow-x-auto border border-slate-300 dark:border-slate-600 rounded-md shadow bg-white dark:bg-gray-900" }, [
                    createVNode("table", { class: "w-full text-left border-collapse text-sm" }, [
                      createVNode("thead", { class: "bg-slate-100 dark:bg-slate-800" }, [
                        createVNode("tr", null, [
                          createVNode("th", { class: "w-12 px-3 py-2 border-b border-slate-300 dark:border-slate-600 text-center text-xs font-semibold text-slate-500 dark:text-slate-400" }, " # "),
                          createVNode("th", { class: "w-1/3 px-3 py-2 border-b border-slate-300 dark:border-slate-600 font-semibold text-indigo-700 dark:text-indigo-300" }, " Key "),
                          createVNode("th", { class: "px-3 py-2 border-b border-slate-300 dark:border-slate-600 font-semibold text-indigo-700 dark:text-indigo-300" }, " Value "),
                          createVNode("th", { class: "w-12 px-2 py-2 border-b border-slate-300 dark:border-slate-600" })
                        ])
                      ]),
                      createVNode("tbody", null, [
                        (openBlock(true), createBlock(Fragment, null, renderList(filteredEnv.value, (entry, index) => {
                          return openBlock(), createBlock("tr", {
                            key: entry.key,
                            class: "border-b border-slate-200 dark:border-slate-700 last:border-b-0 hover:bg-sky-50 dark:hover:bg-slate-800/80 transition-colors"
                          }, [
                            createVNode("td", { class: "px-3 py-1.5 text-center align-top text-xs text-slate-400 dark:text-slate-500" }, toDisplayString(index + 1), 1),
                            createVNode("td", { class: "px-3 py-1.5 pr-5 align-top font-mono font-semibold text-sky-700 dark:text-sky-300 whitespace-nowrap" }, toDisplayString(entry.key), 1),
                            createVNode("td", { class: "px-3 py-1.5 align-top font-mono break-all text-slate-800 dark:text-slate-100" }, [
                              entry.value !== "" ? (openBlock(), createBlock("span", { key: 0 }, toDisplayString(entry.value), 1)) : (openBlock(), createBlock("span", {
                                key: 1,
                                class: "italic text-slate-400 dark:text-slate-500"
                              }, " empty "))
                            ]),
                            createVNode("td", { class: "px-2 py-1 align-middle text-center" }, [
                              createVNode("button", {
                                type: "button",
                                title: copiedKey.value === entry.key ? "Скопировано" : "Копировать значение",
                                class: "w-7 h-7 inline-flex items-center justify-center rounded text-slate-400 hover:text-sky-600 hover:bg-sky-100 dark:hover:text-sky-300 dark:hover:bg-slate-700 transition",
                                onClick: ($event) => copyValue(entry)
                              }, [
                                copiedKey.value !== entry.key ? (openBlock(), createBlock("svg", {
                                  key: 0,
                                  class: "w-4 h-4 fill-current",
                                  viewBox: "0 0 24 24"
                                }, [
                                  createVNode("path", { d: "M16 1H4c-1.1 0-2 .9-2 2v14h2V3h12V1zm3\n                                                   4H8c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2\n                                                   2h11c1.1 0 2-.9 2-2V7c0-1.1-.9-2-2-2zm0\n                                                   16H8V7h11v14z" })
                                ])) : (openBlock(), createBlock("svg", {
                                  key: 1,
                                  class: "w-4 h-4 fill-current text-green-600 dark:text-green-400",
                                  viewBox: "0 0 24 24"
                                }, [
                                  createVNode("path", { d: "M9 16.17 4.83 12l-1.42 1.41L9\n                                                   19 21 7l-1.41-1.41z" })
                                ]))
                              ], 8, ["title", "onClick"])
                            ])
                          ]);
                        }), 128)),
                        !filteredEnv.value.length ? (openBlock(), createBlock("tr", { key: 0 }, [
                          createVNode("td", {
                            colspan: "4",
                            class: "px-4 py-8 text-center text-sm text-slate-500 dark:text-slate-400"
                          }, " Параметры не найдены ")
                        ])) : createCommentVNode("", true)
                      ])
                    ])
                  ]),
                  createVNode("div", { class: "mt-2 text-xs text-slate-500 dark:text-slate-400" }, " Файл доступен только для просмотра. Изменение конфигурации через административную панель отключено. ")
                ])
              ])
            ];
          }
        }),
        _: 1
      }, _parent));
    };
  }
};
const _sfc_setup = _sfc_main.setup;
_sfc_main.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Pages/Admin/System/EnvInfoPage.vue");
  return _sfc_setup ? _sfc_setup(props, ctx) : void 0;
};
export {
  _sfc_main as default
};
