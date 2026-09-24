import { ref, computed, mergeProps, unref, withCtx, createVNode, toDisplayString, withDirectives, createBlock, openBlock, Fragment, renderList, vModelText, createTextVNode, useSSRContext } from "vue";
import { ssrRenderComponent, ssrInterpolate, ssrRenderAttr, ssrRenderList, ssrIncludeBooleanAttr, ssrRenderClass } from "vue/server-renderer";
import { useI18n } from "vue-i18n";
import { useForm, router } from "@inertiajs/vue3";
import { A as AdminLayout } from "./AdminLayout-03yd3nxC.js";
import { T as TitlePage } from "./TitlePage-CBZZ8YZB.js";
import "vue-toastification";
import "./LocaleSelectOption-BeLdazeX.js";
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
  __name: "Index",
  __ssrInlineRender: true,
  props: {
    log: String,
    files: Array,
    selectedFile: String
  },
  setup(__props) {
    const { t } = useI18n();
    const props = __props;
    const clearForm = useForm({});
    const searchQuery = ref("");
    const logLines = computed(() => props.log.split("\n"));
    const filteredLines = computed(() => {
      if (!searchQuery.value) return logLines.value;
      return logLines.value.filter((line) => line.toLowerCase().includes(searchQuery.value.toLowerCase()));
    });
    const changeFile = (fileKey) => {
      router.get(route("admin.logs.index", { file: fileKey }));
    };
    const clearLog = () => {
      if (confirm("Очистить выбранный лог?")) {
        clearForm.delete(route("admin.logs.clear", { file: props.selectedFile }));
      }
    };
    const getLineColor = (line) => {
      if (/ERROR|exception|critical/i.test(line)) return "text-red-500 dark:text-red-200 font-bold";
      if (/WARN|warning/i.test(line)) return "text-yellow-600 font-semibold";
      if (/INFO/i.test(line)) return "text-indigo-800 dark:text-indigo-200";
      if (/DEBUG/i.test(line)) return "text-fuchsia-800 dark:text-fuchsia-200 font-bold";
      return "text-gray-900 dark:text-gray-100";
    };
    const copyLog = async () => {
      const text = filteredLines.value.join("\n");
      if (!text.trim()) {
        alert("Нет данных для копирования.");
        return;
      }
      try {
        await navigator.clipboard.writeText(text);
        alert("Лог скопирован в буфер обмена.");
      } catch (error) {
        console.error(error);
        alert("Не удалось скопировать лог.");
      }
    };
    const downloadLog = () => {
      window.location.href = route("admin.logs.download", { file: props.selectedFile });
    };
    return (_ctx, _push, _parent, _attrs) => {
      _push(ssrRenderComponent(AdminLayout, mergeProps({
        title: unref(t)("logs")
      }, _attrs), {
        header: withCtx((_, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(ssrRenderComponent(TitlePage, null, {
              default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                if (_push3) {
                  _push3(`${ssrInterpolate(unref(t)("logs"))}`);
                } else {
                  return [
                    createTextVNode(toDisplayString(unref(t)("logs")), 1)
                  ];
                }
              }),
              _: 1
            }, _parent2, _scopeId));
          } else {
            return [
              createVNode(TitlePage, null, {
                default: withCtx(() => [
                  createTextVNode(toDisplayString(unref(t)("logs")), 1)
                ]),
                _: 1
              })
            ];
          }
        }),
        default: withCtx((_, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(`<div class="px-2 py-2 w-full max-w-12xl mx-auto"${_scopeId}><div class="bg-slate-50 dark:bg-slate-700 border border-blue-400 dark:border-blue-200 overflow-hidden shadow-md shadow-gray-500 dark:shadow-slate-400 bg-opacity-95 dark:bg-opacity-95"${_scopeId}><div class="p-6 bg-slate-100 dark:bg-slate-600 rounded shadow"${_scopeId}><h1 class="text-slate-700 dark:text-slate-100 text-center text-xl font-semibold mb-4"${_scopeId}>${ssrInterpolate(unref(t)("viewingLogs"))}</h1><div class="flex flex-wrap gap-4 mb-4"${_scopeId}><select${ssrRenderAttr("value", props.selectedFile)} class="w-32 border rounded-sm h-8 py-0 px-2 bg-slate-100 dark:bg-slate-900 font-semibold text-slate-700 dark:text-slate-50"${_scopeId}><!--[-->`);
            ssrRenderList(props.files, (file) => {
              _push2(`<option${ssrRenderAttr("value", file)} class="text-md"${_scopeId}>${ssrInterpolate(file)}</option>`);
            });
            _push2(`<!--]--></select><input${ssrRenderAttr("value", searchQuery.value)}${ssrRenderAttr("placeholder", `${unref(t)("search")}...`)} class="bg-slate-100 dark:bg-slate-900 text-slate-700 dark:text-slate-50 border rounded-sm h-8 py-0 px-2 flex-1 font-semibold"${_scopeId}><button${ssrIncludeBooleanAttr(unref(clearForm).processing) ? " disabled" : ""} class="h-8 py-0 px-2 bg-rose-500 text-white rounded-sm hover:bg-amber-500 disabled:opacity-50"${ssrRenderAttr("title", unref(t)("clearLog"))}${_scopeId}><svg class="h-4 w-4 fill-current" viewBox="0 0 512 512"${_scopeId}><path d="M10.8 247.2C-.7 251.8-3.7 266.7 5 275.4l54.8 54.8 73.2-24.4-24.4 73.2 128 128c8.8 8.8 23.6 5.7 28.2-5.8l98.1-243.7-108.4-108.4-243.7 98.1zM507.3 27.3L484.7 4.7c-6.2-6.3-16.4-6.3-22.6 0L359.8 106.9l-40.4-40.4c-4.2-4.3-11.4-3-13.9 2.5l-27.2 58.6 106.1 106.1 58.6-27.2c5.4-2.5 6.7-9.7 2.5-13.9l-40.4-40.4L507.3 49.9c6.3-6.2 6.3-16.3 0-22.6z"${_scopeId}></path></svg></button><button class="h-8 py-0 px-2 bg-blue-500 text-white rounded-sm hover:bg-indigo-500"${ssrRenderAttr("title", unref(t)("copy"))}${_scopeId}><svg class="h-4 w-4 fill-current" viewBox="0 0 512 512"${_scopeId}><path d="M464 0c26.51 0 48 21.49 48 48v288c0 26.51-21.49 48-48 48H176c-26.51 0-48-21.49-48-48V48c0-26.51 21.49-48 48-48h288M176 416c-44.112 0-80-35.888-80-80V128H48c-26.51 0-48 21.49-48 48v288c0 26.51 21.49 48 48 48h288c26.51 0 48-21.49 48-48v-48H176z"${_scopeId}></path></svg></button><button class="h-8 py-0 px-2 bg-green-500 text-white rounded-sm hover:bg-teal-500"${ssrRenderAttr("title", unref(t)("download"))}${_scopeId}><svg class="h-4 w-4 fill-current" viewBox="0 0 384 512"${_scopeId}><path d="M224 136V0H24C10.7 0 0 10.7 0 24v464c0 13.3 10.7 24 24 24h336c13.3 0 24-10.7 24-24V160H248c-13.2 0-24-10.8-24-24zm76.45 211.36l-96.42 95.7c-6.65 6.61-17.39 6.61-24.04 0l-96.42-95.7C73.42 337.29 80.54 320 94.82 320H160v-80c0-8.84 7.16-16 16-16h32c8.84 0 16 7.16 16 16v80h65.18c14.28 0 21.4 17.29 11.27 27.36zM377 105L279.1 7c-4.5-4.5-10.6-7-17-7H256v128h128v-6.1c0-6.3-2.5-12.4-7-16.9z"${_scopeId}></path></svg></button></div><div class="border rounded p-4 bg-gray-50 dark:bg-gray-700 overflow-auto max-h-[600px] text-sm font-mono"${_scopeId}>`);
            if (filteredLines.value.length === 0) {
              _push2(`<div class="text-slate-700 dark:text-slate-100"${_scopeId}>${ssrInterpolate(unref(t)("noData"))}</div>`);
            } else {
              _push2(`<!--[-->`);
              ssrRenderList(filteredLines.value, (line, idx) => {
                _push2(`<div${ssrRenderAttr("id", "line-" + idx)} class="${ssrRenderClass(["group flex hover:bg-yellow-100 dark:hover:bg-slate-900 transition-colors duration-200", getLineColor(line)])}"${_scopeId}><span class="font-semibold text-blue-500 dark:text-blue-200 inline-block w-12 text-right mr-2 select-none"${_scopeId}>${ssrInterpolate(idx + 1)}. </span><pre class="whitespace-pre-wrap flex-1"${_scopeId}>${ssrInterpolate(line)}</pre></div>`);
              });
              _push2(`<!--]-->`);
            }
            _push2(`</div></div></div></div>`);
          } else {
            return [
              createVNode("div", { class: "px-2 py-2 w-full max-w-12xl mx-auto" }, [
                createVNode("div", { class: "bg-slate-50 dark:bg-slate-700 border border-blue-400 dark:border-blue-200 overflow-hidden shadow-md shadow-gray-500 dark:shadow-slate-400 bg-opacity-95 dark:bg-opacity-95" }, [
                  createVNode("div", { class: "p-6 bg-slate-100 dark:bg-slate-600 rounded shadow" }, [
                    createVNode("h1", { class: "text-slate-700 dark:text-slate-100 text-center text-xl font-semibold mb-4" }, toDisplayString(unref(t)("viewingLogs")), 1),
                    createVNode("div", { class: "flex flex-wrap gap-4 mb-4" }, [
                      createVNode("select", {
                        onChange: (e) => changeFile(e.target.value),
                        value: props.selectedFile,
                        class: "w-32 border rounded-sm h-8 py-0 px-2 bg-slate-100 dark:bg-slate-900 font-semibold text-slate-700 dark:text-slate-50"
                      }, [
                        (openBlock(true), createBlock(Fragment, null, renderList(props.files, (file) => {
                          return openBlock(), createBlock("option", {
                            key: file,
                            value: file,
                            class: "text-md"
                          }, toDisplayString(file), 9, ["value"]);
                        }), 128))
                      ], 40, ["onChange", "value"]),
                      withDirectives(createVNode("input", {
                        "onUpdate:modelValue": ($event) => searchQuery.value = $event,
                        placeholder: `${unref(t)("search")}...`,
                        class: "bg-slate-100 dark:bg-slate-900 text-slate-700 dark:text-slate-50 border rounded-sm h-8 py-0 px-2 flex-1 font-semibold"
                      }, null, 8, ["onUpdate:modelValue", "placeholder"]), [
                        [vModelText, searchQuery.value]
                      ]),
                      createVNode("button", {
                        onClick: clearLog,
                        disabled: unref(clearForm).processing,
                        class: "h-8 py-0 px-2 bg-rose-500 text-white rounded-sm hover:bg-amber-500 disabled:opacity-50",
                        title: unref(t)("clearLog")
                      }, [
                        (openBlock(), createBlock("svg", {
                          class: "h-4 w-4 fill-current",
                          viewBox: "0 0 512 512"
                        }, [
                          createVNode("path", { d: "M10.8 247.2C-.7 251.8-3.7 266.7 5 275.4l54.8 54.8 73.2-24.4-24.4 73.2 128 128c8.8 8.8 23.6 5.7 28.2-5.8l98.1-243.7-108.4-108.4-243.7 98.1zM507.3 27.3L484.7 4.7c-6.2-6.3-16.4-6.3-22.6 0L359.8 106.9l-40.4-40.4c-4.2-4.3-11.4-3-13.9 2.5l-27.2 58.6 106.1 106.1 58.6-27.2c5.4-2.5 6.7-9.7 2.5-13.9l-40.4-40.4L507.3 49.9c6.3-6.2 6.3-16.3 0-22.6z" })
                        ]))
                      ], 8, ["disabled", "title"]),
                      createVNode("button", {
                        onClick: copyLog,
                        class: "h-8 py-0 px-2 bg-blue-500 text-white rounded-sm hover:bg-indigo-500",
                        title: unref(t)("copy")
                      }, [
                        (openBlock(), createBlock("svg", {
                          class: "h-4 w-4 fill-current",
                          viewBox: "0 0 512 512"
                        }, [
                          createVNode("path", { d: "M464 0c26.51 0 48 21.49 48 48v288c0 26.51-21.49 48-48 48H176c-26.51 0-48-21.49-48-48V48c0-26.51 21.49-48 48-48h288M176 416c-44.112 0-80-35.888-80-80V128H48c-26.51 0-48 21.49-48 48v288c0 26.51 21.49 48 48 48h288c26.51 0 48-21.49 48-48v-48H176z" })
                        ]))
                      ], 8, ["title"]),
                      createVNode("button", {
                        onClick: downloadLog,
                        class: "h-8 py-0 px-2 bg-green-500 text-white rounded-sm hover:bg-teal-500",
                        title: unref(t)("download")
                      }, [
                        (openBlock(), createBlock("svg", {
                          class: "h-4 w-4 fill-current",
                          viewBox: "0 0 384 512"
                        }, [
                          createVNode("path", { d: "M224 136V0H24C10.7 0 0 10.7 0 24v464c0 13.3 10.7 24 24 24h336c13.3 0 24-10.7 24-24V160H248c-13.2 0-24-10.8-24-24zm76.45 211.36l-96.42 95.7c-6.65 6.61-17.39 6.61-24.04 0l-96.42-95.7C73.42 337.29 80.54 320 94.82 320H160v-80c0-8.84 7.16-16 16-16h32c8.84 0 16 7.16 16 16v80h65.18c14.28 0 21.4 17.29 11.27 27.36zM377 105L279.1 7c-4.5-4.5-10.6-7-17-7H256v128h128v-6.1c0-6.3-2.5-12.4-7-16.9z" })
                        ]))
                      ], 8, ["title"])
                    ]),
                    createVNode("div", { class: "border rounded p-4 bg-gray-50 dark:bg-gray-700 overflow-auto max-h-[600px] text-sm font-mono" }, [
                      filteredLines.value.length === 0 ? (openBlock(), createBlock("div", {
                        key: 0,
                        class: "text-slate-700 dark:text-slate-100"
                      }, toDisplayString(unref(t)("noData")), 1)) : (openBlock(true), createBlock(Fragment, { key: 1 }, renderList(filteredLines.value, (line, idx) => {
                        return openBlock(), createBlock("div", {
                          key: idx,
                          id: "line-" + idx,
                          class: ["group flex hover:bg-yellow-100 dark:hover:bg-slate-900 transition-colors duration-200", getLineColor(line)]
                        }, [
                          createVNode("span", { class: "font-semibold text-blue-500 dark:text-blue-200 inline-block w-12 text-right mr-2 select-none" }, toDisplayString(idx + 1) + ". ", 1),
                          createVNode("pre", { class: "whitespace-pre-wrap flex-1" }, toDisplayString(line), 1)
                        ], 10, ["id"]);
                      }), 128))
                    ])
                  ])
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
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Pages/Admin/System/Log/Index.vue");
  return _sfc_setup ? _sfc_setup(props, ctx) : void 0;
};
export {
  _sfc_main as default
};
