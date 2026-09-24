import { ref, mergeProps, withCtx, createVNode, createBlock, openBlock, createTextVNode, createCommentVNode, withDirectives, withKeys, vModelText, toDisplayString, nextTick, useSSRContext } from "vue";
import { ssrRenderComponent, ssrRenderAttr, ssrInterpolate } from "vue/server-renderer";
import { A as AdminLayout } from "./AdminLayout-03yd3nxC.js";
import { T as TitlePage } from "./TitlePage-CBZZ8YZB.js";
import html2pdf from "html2pdf.js";
import { _ as _export_sfc } from "./_plugin-vue_export-helper-1tPrXgE0.js";
import "@inertiajs/vue3";
import "vue-toastification";
import "./LocaleSelectOption-BeLdazeX.js";
import "vue-i18n";
import "./ResponsiveNavLink-gtte0z5g.js";
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
  __name: "PhpInfoPage",
  __ssrInlineRender: true,
  props: {
    phpinfo: {
      type: String,
      default: ""
    }
  },
  setup(__props) {
    const props = __props;
    const search = ref("");
    const searchCount = ref(0);
    const phpInfoContent = ref(null);
    const currentDateTime = (/* @__PURE__ */ new Date()).toISOString().slice(0, 19).replace(/:/g, "-");
    const clearHighlights = () => {
      const container = phpInfoContent.value;
      if (!container) {
        searchCount.value = 0;
        return;
      }
      const marks = container.querySelectorAll(
        "mark.php-info-search-mark"
      );
      marks.forEach((mark) => {
        const parent = mark.parentNode;
        if (!parent) {
          return;
        }
        parent.replaceChild(
          document.createTextNode(
            mark.textContent || ""
          ),
          mark
        );
        parent.normalize();
      });
      searchCount.value = 0;
    };
    const searchPhpInfo = async () => {
      clearHighlights();
      const query = search.value.trim().toLowerCase();
      if (!query) {
        return;
      }
      await nextTick();
      const container = phpInfoContent.value;
      if (!container) {
        return;
      }
      const walker = document.createTreeWalker(
        container,
        window.NodeFilter.SHOW_TEXT
      );
      const nodes = [];
      let currentNode = walker.nextNode();
      while (currentNode) {
        const parent = currentNode.parentElement;
        if (parent && parent.tagName !== "SCRIPT" && parent.tagName !== "STYLE") {
          nodes.push(currentNode);
        }
        currentNode = walker.nextNode();
      }
      let count = 0;
      let firstMatch = null;
      nodes.forEach((textNode) => {
        const text = textNode.nodeValue || "";
        const lowerText = text.toLowerCase();
        if (!lowerText.includes(query)) {
          return;
        }
        const fragment = document.createDocumentFragment();
        let start = 0;
        let index = lowerText.indexOf(query);
        while (index !== -1) {
          if (index > start) {
            fragment.appendChild(
              document.createTextNode(
                text.slice(
                  start,
                  index
                )
              )
            );
          }
          const mark = document.createElement("mark");
          mark.className = "php-info-search-mark";
          mark.textContent = text.slice(
            index,
            index + query.length
          );
          fragment.appendChild(mark);
          if (!firstMatch) {
            firstMatch = mark;
          }
          count++;
          start = index + query.length;
          index = lowerText.indexOf(
            query,
            start
          );
        }
        if (start < text.length) {
          fragment.appendChild(
            document.createTextNode(
              text.slice(start)
            )
          );
        }
        if (textNode.parentNode) {
          textNode.parentNode.replaceChild(
            fragment,
            textNode
          );
        }
      });
      searchCount.value = count;
      if (firstMatch) {
        firstMatch.scrollIntoView({
          behavior: "smooth",
          block: "center"
        });
      }
    };
    const clearSearch = () => {
      search.value = "";
      clearHighlights();
    };
    const downloadPDF = () => {
      const wrapper = document.createElement("div");
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

            ${props.phpinfo || ""}
        </div>
    `;
      const options = {
        margin: 0.35,
        filename: `php-info_${currentDateTime}.pdf`,
        image: {
          type: "jpeg",
          quality: 0.98
        },
        html2canvas: {
          scale: 1,
          logging: false,
          useCORS: true,
          backgroundColor: "#ffffff"
        },
        jsPDF: {
          unit: "in",
          format: "a4",
          orientation: "portrait"
        },
        pagebreak: {
          mode: [
            "css",
            "legacy"
          ],
          avoid: [
            "tr"
          ]
        }
      };
      html2pdf().set(options).from(wrapper).save();
    };
    return (_ctx, _push, _parent, _attrs) => {
      _push(ssrRenderComponent(AdminLayout, mergeProps({ title: "PHP Info" }, _attrs), {
        header: withCtx((_, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(ssrRenderComponent(TitlePage, null, {
              default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                if (_push3) {
                  _push3(` PHP Info `);
                } else {
                  return [
                    createTextVNode(" PHP Info ")
                  ];
                }
              }),
              _: 1
            }, _parent2, _scopeId));
          } else {
            return [
              createVNode(TitlePage, null, {
                default: withCtx(() => [
                  createTextVNode(" PHP Info ")
                ]),
                _: 1
              })
            ];
          }
        }),
        default: withCtx((_, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(`<div class="px-2 py-2 w-full max-w-12xl mx-auto" data-v-29961bd2${_scopeId}><div class="p-4 bg-slate-50 dark:bg-slate-700 border border-blue-400 dark:border-blue-200 overflow-hidden shadow-md shadow-gray-500 dark:shadow-slate-400 bg-opacity-95 dark:bg-opacity-95" data-v-29961bd2${_scopeId}><div class="mb-4 px-4 py-3 border border-orange-300 dark:border-orange-700 bg-orange-50 dark:bg-orange-900/20 rounded-md" data-v-29961bd2${_scopeId}><div class="flex items-start gap-3" data-v-29961bd2${_scopeId}><div class="shrink-0 flex items-center justify-center w-8 h-8 rounded-full bg-orange-100 dark:bg-orange-800 text-orange-700 dark:text-orange-200" data-v-29961bd2${_scopeId}><svg class="w-5 h-5 fill-current" viewBox="0 0 24 24" data-v-29961bd2${_scopeId}><path d="M11 17h2v-6h-2v6zm1-15C6.48 2
                                       2 6.48 2 12s4.48 10
                                       10 10 10-4.48 10-10S17.52
                                       2 12 2zm0 18c-4.41
                                       0-8-3.59-8-8s3.59-8
                                       8-8 8 3.59 8 8-3.59
                                       8-8 8zM11 9h2V7h-2v2z" data-v-29961bd2${_scopeId}></path></svg></div><div data-v-29961bd2${_scopeId}><div class="text-sm font-semibold text-orange-800 dark:text-orange-200" data-v-29961bd2${_scopeId}> Конфигурация PHP </div><div class="mt-0.5 text-xs text-orange-700 dark:text-orange-300" data-v-29961bd2${_scopeId}> Здесь отображается информация, полученная непосредственно через <span class="font-mono font-semibold" data-v-29961bd2${_scopeId}> phpinfo() </span>. Страница доступна только для просмотра. </div></div></div></div><div class="flex flex-col xl:flex-row xl:items-start xl:justify-between gap-3 mb-4" data-v-29961bd2${_scopeId}><button type="button" class="h-8 px-3 inline-flex items-center justify-center gap-1 bg-teal-600 text-white rounded-sm hover:bg-teal-700 transition" data-v-29961bd2${_scopeId}><svg class="h-4 w-4 fill-current" viewBox="0 0 384 512" data-v-29961bd2${_scopeId}><path d="M181.9 256.1c-5-16-4.9-46.9-2-46.9
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
                   37.1 15.8 42.8 9 42.8 9z" data-v-29961bd2${_scopeId}></path></svg><span data-v-29961bd2${_scopeId}> PDF </span></button><div class="w-full xl:w-auto" data-v-29961bd2${_scopeId}><div class="flex flex-col sm:flex-row sm:items-center gap-2" data-v-29961bd2${_scopeId}><div class="relative w-full sm:w-96" data-v-29961bd2${_scopeId}><svg class="absolute left-2.5 top-1/2 -translate-y-1/2 w-4 h-4 fill-current text-slate-400" viewBox="0 0 24 24" data-v-29961bd2${_scopeId}><path d="M9.5 3a6.5 6.5 0 1 0
                           3.98 11.64L19.85 21
                           21 19.85l-6.36-6.37A6.5
                           6.5 0 0 0 9.5 3zm0
                           2a4.5 4.5 0 1 1 0
                           9 4.5 4.5 0 0 1 0-9z" data-v-29961bd2${_scopeId}></path></svg><input${ssrRenderAttr("value", search.value)} type="text" placeholder="memory_limit, curl, PDO, openssl..." class="w-full pl-8 pr-8 py-1.5 text-sm font-mono border border-slate-300 dark:border-slate-500 rounded-md bg-white dark:bg-slate-800 text-slate-800 dark:text-slate-100 placeholder:text-slate-400 focus:outline-none focus:ring-1 focus:ring-sky-500 focus:border-sky-500" data-v-29961bd2${_scopeId}>`);
            if (search.value) {
              _push2(`<button type="button" title="Очистить поиск" class="absolute right-2 top-1/2 -translate-y-1/2 text-slate-400 hover:text-red-500 transition" data-v-29961bd2${_scopeId}> × </button>`);
            } else {
              _push2(`<!---->`);
            }
            _push2(`</div><button type="button" class="h-8 px-3 inline-flex items-center justify-center bg-sky-600 text-white rounded-sm hover:bg-sky-700 transition" data-v-29961bd2${_scopeId}> Найти </button></div>`);
            if (search.value) {
              _push2(`<div class="mt-1 text-xs text-slate-600 dark:text-slate-300" data-v-29961bd2${_scopeId}> Найдено: <span class="font-semibold text-sky-700 dark:text-sky-300" data-v-29961bd2${_scopeId}>${ssrInterpolate(searchCount.value)}</span></div>`);
            } else {
              _push2(`<!---->`);
            }
            _push2(`</div></div><div class="overflow-x-auto border border-slate-300 rounded-md shadow p-4 bg-white text-gray-900 text-sm" data-v-29961bd2${_scopeId}><h1 class="text-xl font-semibold mb-4 text-gray-900" data-v-29961bd2${_scopeId}> PHP Info </h1><div class="php-info-content" data-v-29961bd2${_scopeId}>${props.phpinfo ?? ""}</div></div><div class="mt-3 text-xs text-slate-500 dark:text-slate-400" data-v-29961bd2${_scopeId}> Информация PHP доступна только для просмотра. Изменение конфигурации PHP из административной панели не выполняется. </div></div></div>`);
          } else {
            return [
              createVNode("div", { class: "px-2 py-2 w-full max-w-12xl mx-auto" }, [
                createVNode("div", { class: "p-4 bg-slate-50 dark:bg-slate-700 border border-blue-400 dark:border-blue-200 overflow-hidden shadow-md shadow-gray-500 dark:shadow-slate-400 bg-opacity-95 dark:bg-opacity-95" }, [
                  createVNode("div", { class: "mb-4 px-4 py-3 border border-orange-300 dark:border-orange-700 bg-orange-50 dark:bg-orange-900/20 rounded-md" }, [
                    createVNode("div", { class: "flex items-start gap-3" }, [
                      createVNode("div", { class: "shrink-0 flex items-center justify-center w-8 h-8 rounded-full bg-orange-100 dark:bg-orange-800 text-orange-700 dark:text-orange-200" }, [
                        (openBlock(), createBlock("svg", {
                          class: "w-5 h-5 fill-current",
                          viewBox: "0 0 24 24"
                        }, [
                          createVNode("path", { d: "M11 17h2v-6h-2v6zm1-15C6.48 2\n                                       2 6.48 2 12s4.48 10\n                                       10 10 10-4.48 10-10S17.52\n                                       2 12 2zm0 18c-4.41\n                                       0-8-3.59-8-8s3.59-8\n                                       8-8 8 3.59 8 8-3.59\n                                       8-8 8zM11 9h2V7h-2v2z" })
                        ]))
                      ]),
                      createVNode("div", null, [
                        createVNode("div", { class: "text-sm font-semibold text-orange-800 dark:text-orange-200" }, " Конфигурация PHP "),
                        createVNode("div", { class: "mt-0.5 text-xs text-orange-700 dark:text-orange-300" }, [
                          createTextVNode(" Здесь отображается информация, полученная непосредственно через "),
                          createVNode("span", { class: "font-mono font-semibold" }, " phpinfo() "),
                          createTextVNode(". Страница доступна только для просмотра. ")
                        ])
                      ])
                    ])
                  ]),
                  createVNode("div", { class: "flex flex-col xl:flex-row xl:items-start xl:justify-between gap-3 mb-4" }, [
                    createVNode("button", {
                      type: "button",
                      class: "h-8 px-3 inline-flex items-center justify-center gap-1 bg-teal-600 text-white rounded-sm hover:bg-teal-700 transition",
                      onClick: downloadPDF
                    }, [
                      (openBlock(), createBlock("svg", {
                        class: "h-4 w-4 fill-current",
                        viewBox: "0 0 384 512"
                      }, [
                        createVNode("path", { d: "M181.9 256.1c-5-16-4.9-46.9-2-46.9\n                   8.4 0 7.6 36.9 2 46.9zm-1.7\n                   47.2c-7.7 20.2-17.3 43.3-28.4\n                   62.7 18.3-7 39-17.2\n                   62.9-21.9-12.7-9.6-24.9-23.4-34.5-40.8zM86.1\n                   428.1c0 .8 13.2-5.4\n                   34.9-40.2-6.7 6.3-29.1\n                   24.5-34.9 40.2zM248\n                   160h136v328c0 13.3-10.7\n                   24-24 24H24c-13.3\n                   0-24-10.7-24-24V24C0\n                   10.7 10.7 0 24 0h200v136c0\n                   13.2 10.8 24 24\n                   24zm-8 171.8c-20-12.2-33.3-29-42.7-53.8\n                   4.5-18.5 11.6-46.6\n                   6.2-64.2-4.7-29.4-42.4-26.5-47.8-6.8-5\n                   18.3-.4 44.1 8.1\n                   77-11.6 27.6-28.7\n                   64.6-40.8 85.8-.1 0-.1.1-.2.1-27.1\n                   13.9-73.6 44.5-54.5\n                   68 5.6 6.9 16 10\n                   21.5 10 17.9 0 35.7-18\n                   61.1-61.8 25.8-8.5 54.1-19.1\n                   79-23.2 21.7 11.8\n                   47.1 19.5 64 19.5 29.2\n                   0 31.2-32 19.7-43.4-13.9-13.6-54.3-9.7-73.6-7.2zM377\n                   105 279 7c-4.5-4.5-10.6-7-17-7h-6v128h128v-6.1c0-6.3-2.5-12.4-7-16.9zm-74.1\n                   255.3c4.1-2.7-2.5-11.9-42.8-9\n                   37.1 15.8 42.8 9 42.8 9z" })
                      ])),
                      createVNode("span", null, " PDF ")
                    ]),
                    createVNode("div", { class: "w-full xl:w-auto" }, [
                      createVNode("div", { class: "flex flex-col sm:flex-row sm:items-center gap-2" }, [
                        createVNode("div", { class: "relative w-full sm:w-96" }, [
                          (openBlock(), createBlock("svg", {
                            class: "absolute left-2.5 top-1/2 -translate-y-1/2 w-4 h-4 fill-current text-slate-400",
                            viewBox: "0 0 24 24"
                          }, [
                            createVNode("path", { d: "M9.5 3a6.5 6.5 0 1 0\n                           3.98 11.64L19.85 21\n                           21 19.85l-6.36-6.37A6.5\n                           6.5 0 0 0 9.5 3zm0\n                           2a4.5 4.5 0 1 1 0\n                           9 4.5 4.5 0 0 1 0-9z" })
                          ])),
                          withDirectives(createVNode("input", {
                            "onUpdate:modelValue": ($event) => search.value = $event,
                            type: "text",
                            placeholder: "memory_limit, curl, PDO, openssl...",
                            class: "w-full pl-8 pr-8 py-1.5 text-sm font-mono border border-slate-300 dark:border-slate-500 rounded-md bg-white dark:bg-slate-800 text-slate-800 dark:text-slate-100 placeholder:text-slate-400 focus:outline-none focus:ring-1 focus:ring-sky-500 focus:border-sky-500",
                            onKeyup: withKeys(searchPhpInfo, ["enter"])
                          }, null, 40, ["onUpdate:modelValue"]), [
                            [vModelText, search.value]
                          ]),
                          search.value ? (openBlock(), createBlock("button", {
                            key: 0,
                            type: "button",
                            title: "Очистить поиск",
                            class: "absolute right-2 top-1/2 -translate-y-1/2 text-slate-400 hover:text-red-500 transition",
                            onClick: clearSearch
                          }, " × ")) : createCommentVNode("", true)
                        ]),
                        createVNode("button", {
                          type: "button",
                          class: "h-8 px-3 inline-flex items-center justify-center bg-sky-600 text-white rounded-sm hover:bg-sky-700 transition",
                          onClick: searchPhpInfo
                        }, " Найти ")
                      ]),
                      search.value ? (openBlock(), createBlock("div", {
                        key: 0,
                        class: "mt-1 text-xs text-slate-600 dark:text-slate-300"
                      }, [
                        createTextVNode(" Найдено: "),
                        createVNode("span", { class: "font-semibold text-sky-700 dark:text-sky-300" }, toDisplayString(searchCount.value), 1)
                      ])) : createCommentVNode("", true)
                    ])
                  ]),
                  createVNode("div", { class: "overflow-x-auto border border-slate-300 rounded-md shadow p-4 bg-white text-gray-900 text-sm" }, [
                    createVNode("h1", { class: "text-xl font-semibold mb-4 text-gray-900" }, " PHP Info "),
                    createVNode("div", {
                      ref_key: "phpInfoContent",
                      ref: phpInfoContent,
                      class: "php-info-content",
                      innerHTML: props.phpinfo
                    }, null, 8, ["innerHTML"])
                  ]),
                  createVNode("div", { class: "mt-3 text-xs text-slate-500 dark:text-slate-400" }, " Информация PHP доступна только для просмотра. Изменение конфигурации PHP из административной панели не выполняется. ")
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
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Pages/Admin/System/PhpInfoPage.vue");
  return _sfc_setup ? _sfc_setup(props, ctx) : void 0;
};
const PhpInfoPage = /* @__PURE__ */ _export_sfc(_sfc_main, [["__scopeId", "data-v-29961bd2"]]);
export {
  PhpInfoPage as default
};
