import { ref, computed, watch, mergeProps, unref, withCtx, createTextVNode, toDisplayString, createVNode, createBlock, createCommentVNode, withModifiers, openBlock, Fragment, renderList, useSSRContext } from "vue";
import { ssrRenderComponent, ssrInterpolate, ssrRenderAttr, ssrIncludeBooleanAttr, ssrRenderList, ssrRenderClass } from "vue/server-renderer";
import { useForm } from "@inertiajs/vue3";
import { useI18n } from "vue-i18n";
import { useToast } from "vue-toastification";
import JSZip from "jszip";
import { saveAs } from "file-saver";
import { A as AdminLayout } from "./AdminLayout-03yd3nxC.js";
import { T as TitlePage } from "./TitlePage-CBZZ8YZB.js";
import { _ as _sfc_main$1 } from "./PrimaryButton-B3InEAXg.js";
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
  __name: "SitemapPage",
  __ssrInlineRender: true,
  props: {
    files: { type: Array, default: () => [] },
    selectedFile: { type: String, default: "" },
    content: { type: String, default: "" }
  },
  setup(__props) {
    var _a;
    const { t } = useI18n();
    const toast = useToast();
    const props = __props;
    const form = useForm({});
    const activeFile = ref(
      props.selectedFile || ((_a = props.files.find((file) => file.exists)) == null ? void 0 : _a.name) || ""
    );
    const activeContent = ref(props.content);
    const contents = ref(
      activeFile.value ? { [activeFile.value]: props.content } : {}
    );
    const loading = ref(false);
    const archiveProcessing = ref(false);
    const currentFile = computed(
      () => props.files.find((file) => file.name === activeFile.value) || null
    );
    const currentFileExists = computed(
      () => {
        var _a2;
        return Boolean((_a2 = currentFile.value) == null ? void 0 : _a2.exists);
      }
    );
    const existingFiles = computed(
      () => props.files.filter((file) => file.exists)
    );
    const contentLines = computed(() => {
      if (!activeContent.value) return [];
      return activeContent.value.split(/\r\n|\r|\n/);
    });
    const lineCount = computed(() => contentLines.value.length);
    const generate = () => {
      form.post(route("admin.sitemap.generate"), {
        preserveScroll: true,
        onSuccess: () => {
          contents.value = {};
          if (props.selectedFile) {
            activeFile.value = props.selectedFile;
            activeContent.value = props.content;
            contents.value[props.selectedFile] = props.content;
          }
          toast.success(t("sitemapSuccess"));
        },
        onError: (errors) => {
          toast.error((errors == null ? void 0 : errors.sitemap) || t("sitemapError"));
        }
      });
    };
    const selectFile = async (file) => {
      if (!file.exists) return;
      activeFile.value = file.name;
      if (Object.prototype.hasOwnProperty.call(
        contents.value,
        file.name
      )) {
        activeContent.value = contents.value[file.name];
        return;
      }
      loading.value = true;
      try {
        const response = await fetch(
          route("admin.sitemap.content", { file: file.name }),
          {
            headers: { Accept: "application/json" }
          }
        );
        if (!response.ok) {
          console.error(
            "Не удалось загрузить sitemap:",
            response.status
          );
          activeContent.value = "";
          toast.error(t("sitemapError"));
          return;
        }
        const data = await response.json();
        contents.value[file.name] = data.content || "";
        activeContent.value = data.content || "";
      } catch (error) {
        console.error(
          "Не удалось загрузить sitemap:",
          error
        );
        activeContent.value = "";
        toast.error(t("sitemapError"));
      } finally {
        loading.value = false;
      }
    };
    const downloadArchive = async () => {
      if (!existingFiles.value.length) return;
      archiveProcessing.value = true;
      try {
        const zip = new JSZip();
        for (const file of existingFiles.value) {
          const response = await fetch(
            route("admin.sitemap.download", { file: file.name })
          );
          if (!response.ok) {
            console.error(
              "Не удалось скачать sitemap:",
              file.name,
              response.status
            );
            toast.error(t("sitemapError"));
            return;
          }
          const blob = await response.blob();
          zip.file(file.name, blob);
        }
        const archive = await zip.generateAsync({
          type: "blob"
        });
        saveAs(archive, "sitemaps.zip");
      } catch (error) {
        console.error(
          "Не удалось скачать архив sitemap:",
          error
        );
        toast.error(t("sitemapError"));
      } finally {
        archiveProcessing.value = false;
      }
    };
    watch(
      () => props.content,
      (value) => {
        if (!props.selectedFile) return;
        contents.value[props.selectedFile] = value || "";
        if (activeFile.value === props.selectedFile) {
          activeContent.value = value || "";
        }
      }
    );
    return (_ctx, _push, _parent, _attrs) => {
      _push(ssrRenderComponent(AdminLayout, mergeProps({
        title: unref(t)("sitemapTitle")
      }, _attrs), {
        header: withCtx((_, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(ssrRenderComponent(TitlePage, null, {
              default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                if (_push3) {
                  _push3(`${ssrInterpolate(unref(t)("sitemapTitle"))}`);
                } else {
                  return [
                    createTextVNode(toDisplayString(unref(t)("sitemapTitle")), 1)
                  ];
                }
              }),
              _: 1
            }, _parent2, _scopeId));
          } else {
            return [
              createVNode(TitlePage, null, {
                default: withCtx(() => [
                  createTextVNode(toDisplayString(unref(t)("sitemapTitle")), 1)
                ]),
                _: 1
              })
            ];
          }
        }),
        default: withCtx((_, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(`<div class="px-2 py-2 w-full max-w-12xl mx-auto"${_scopeId}><div class="p-4 bg-slate-50 dark:bg-slate-700 border border-blue-400 dark:border-blue-200 overflow-hidden shadow-md shadow-gray-500 dark:shadow-slate-400 bg-opacity-95 dark:bg-opacity-95"${_scopeId}><form${_scopeId}><div class="flex flex-wrap items-center justify-between gap-2"${_scopeId}>`);
            _push2(ssrRenderComponent(_sfc_main$1, {
              disabled: unref(form).processing
            }, {
              default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                if (_push3) {
                  _push3(`${ssrInterpolate(unref(t)("generate"))}`);
                } else {
                  return [
                    createTextVNode(toDisplayString(unref(t)("generate")), 1)
                  ];
                }
              }),
              _: 1
            }, _parent2, _scopeId));
            _push2(`<div class="flex flex-wrap items-center gap-2"${_scopeId}>`);
            if (currentFileExists.value) {
              _push2(`<a${ssrRenderAttr("href", _ctx.route("admin.sitemap.download", { file: activeFile.value }))} class="flex items-center btn px-2 py-0.5 bg-sky-600 text-white text-sm font-semibold rounded-sm shadow-md transition-colors duration-300 ease-in-out hover:bg-sky-700 focus:bg-sky-700 focus:outline-none"${_scopeId}><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="w-4 h-4 fill-current opacity-50 shrink-0"${_scopeId}><path d="M22,15a1,1,0,0,0-1,1v4a1,1,0,0,1-1,1H4a1,1,0,0,1-1-1V16a1,1,0,0,0-2,0v4a3,3,0,0,0,3,3H20a3,3,0,0,0,3-3V16A1,1,0,0,0,22,15Z"${_scopeId}></path><path d="M11.232,17.64a1,1,0,0,0,1.536,0l5-6A1,1,0,0,0,17,10H13V2a1,1,0,0,0-2,0v8H7a1,1,0,0,0-.768,1.64Z"${_scopeId}></path></svg><span class="ml-2"${_scopeId}>${ssrInterpolate(unref(t)("download"))} ${ssrInterpolate(activeFile.value)}</span></a>`);
            } else {
              _push2(`<!---->`);
            }
            if (existingFiles.value.length) {
              _push2(`<button type="button"${ssrIncludeBooleanAttr(archiveProcessing.value) ? " disabled" : ""} class="flex items-center btn px-2 py-0.5 bg-slate-600 text-white text-sm font-semibold rounded-sm shadow-md transition-colors duration-300 ease-in-out hover:bg-slate-700 focus:bg-slate-700 focus:outline-none disabled:opacity-50 disabled:cursor-not-allowed"${_scopeId}><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="w-4 h-4 fill-current opacity-50 shrink-0"${_scopeId}><path d="M22,15a1,1,0,0,0-1,1v4a1,1,0,0,1-1,1H4a1,1,0,0,1-1-1V16a1,1,0,0,0-2,0v4a3,3,0,0,0,3,3H20a3,3,0,0,0,3-3V16A1,1,0,0,0,22,15Z"${_scopeId}></path><path d="M11.232,17.64a1,1,0,0,0,1.536,0l5-6A1,1,0,0,0,17,10H13V2a1,1,0,0,0-2,0v8H7a1,1,0,0,0-.768,1.64Z"${_scopeId}></path></svg><span class="ml-2"${_scopeId}>${ssrInterpolate(archiveProcessing.value ? "ZIP..." : "ZIP")}</span></button>`);
            } else {
              _push2(`<!---->`);
            }
            _push2(`</div></div></form>`);
            if (__props.files.length) {
              _push2(`<div class="mt-4 flex flex-wrap gap-1 border-b border-gray-300 dark:border-gray-500"${_scopeId}><!--[-->`);
              ssrRenderList(__props.files, (file) => {
                _push2(`<button type="button"${ssrIncludeBooleanAttr(!file.exists) ? " disabled" : ""} class="${ssrRenderClass([
                  "px-3 py-1.5 text-xs font-semibold border border-b-0 rounded-t transition-colors duration-200",
                  activeFile.value === file.name ? "bg-sky-600 text-white border-sky-600" : "bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-200 border-gray-300 dark:border-gray-500 hover:bg-gray-200 dark:hover:bg-gray-700",
                  !file.exists ? "opacity-40 cursor-not-allowed" : ""
                ])}"${_scopeId}>${ssrInterpolate(file.name)}</button>`);
              });
              _push2(`<!--]--></div>`);
            } else {
              _push2(`<!---->`);
            }
            if (currentFileExists.value) {
              _push2(`<div class="mt-2 flex justify-center items-center gap-3"${_scopeId}><span class="font-semibold text-red-500 dark:text-red-400 text-xl"${_scopeId}>${ssrInterpolate(activeFile.value)}</span><span class="text-slate-700 dark:text-slate-300 text-[10px]"${_scopeId}> Строк: ${ssrInterpolate(lineCount.value)}</span></div>`);
            } else {
              _push2(`<!---->`);
            }
            if (loading.value) {
              _push2(`<div class="mt-2 p-4 text-sm text-slate-700 dark:text-slate-200"${_scopeId}>${ssrInterpolate(unref(t)("downloadStatus"))}</div>`);
            } else {
              _push2(`<div class="mt-2 h-[520px] overflow-auto border-2 border-gray-400 rounded bg-gray-100 dark:bg-gray-800 text-xs font-mono"${_scopeId}><!--[-->`);
              ssrRenderList(contentLines.value, (line, index) => {
                _push2(`<div class="flex min-w-max leading-5"${_scopeId}><div class="w-16 shrink-0 px-2 text-right select-none text-gray-400 dark:text-gray-500 border-r border-gray-300 dark:border-gray-600 bg-gray-200 dark:bg-gray-900"${_scopeId}>${ssrInterpolate(index + 1)}</div><pre class="px-3 m-0 font-semibold whitespace-pre text-blue-800 dark:text-blue-200"${_scopeId}>${ssrInterpolate(line)}</pre></div>`);
              });
              _push2(`<!--]--></div>`);
            }
            _push2(`</div></div>`);
          } else {
            return [
              createVNode("div", { class: "px-2 py-2 w-full max-w-12xl mx-auto" }, [
                createVNode("div", { class: "p-4 bg-slate-50 dark:bg-slate-700 border border-blue-400 dark:border-blue-200 overflow-hidden shadow-md shadow-gray-500 dark:shadow-slate-400 bg-opacity-95 dark:bg-opacity-95" }, [
                  createVNode("form", {
                    onSubmit: withModifiers(generate, ["prevent"])
                  }, [
                    createVNode("div", { class: "flex flex-wrap items-center justify-between gap-2" }, [
                      createVNode(_sfc_main$1, {
                        disabled: unref(form).processing
                      }, {
                        default: withCtx(() => [
                          createTextVNode(toDisplayString(unref(t)("generate")), 1)
                        ]),
                        _: 1
                      }, 8, ["disabled"]),
                      createVNode("div", { class: "flex flex-wrap items-center gap-2" }, [
                        currentFileExists.value ? (openBlock(), createBlock("a", {
                          key: 0,
                          href: _ctx.route("admin.sitemap.download", { file: activeFile.value }),
                          class: "flex items-center btn px-2 py-0.5 bg-sky-600 text-white text-sm font-semibold rounded-sm shadow-md transition-colors duration-300 ease-in-out hover:bg-sky-700 focus:bg-sky-700 focus:outline-none"
                        }, [
                          (openBlock(), createBlock("svg", {
                            xmlns: "http://www.w3.org/2000/svg",
                            viewBox: "0 0 24 24",
                            class: "w-4 h-4 fill-current opacity-50 shrink-0"
                          }, [
                            createVNode("path", { d: "M22,15a1,1,0,0,0-1,1v4a1,1,0,0,1-1,1H4a1,1,0,0,1-1-1V16a1,1,0,0,0-2,0v4a3,3,0,0,0,3,3H20a3,3,0,0,0,3-3V16A1,1,0,0,0,22,15Z" }),
                            createVNode("path", { d: "M11.232,17.64a1,1,0,0,0,1.536,0l5-6A1,1,0,0,0,17,10H13V2a1,1,0,0,0-2,0v8H7a1,1,0,0,0-.768,1.64Z" })
                          ])),
                          createVNode("span", { class: "ml-2" }, toDisplayString(unref(t)("download")) + " " + toDisplayString(activeFile.value), 1)
                        ], 8, ["href"])) : createCommentVNode("", true),
                        existingFiles.value.length ? (openBlock(), createBlock("button", {
                          key: 1,
                          type: "button",
                          disabled: archiveProcessing.value,
                          onClick: downloadArchive,
                          class: "flex items-center btn px-2 py-0.5 bg-slate-600 text-white text-sm font-semibold rounded-sm shadow-md transition-colors duration-300 ease-in-out hover:bg-slate-700 focus:bg-slate-700 focus:outline-none disabled:opacity-50 disabled:cursor-not-allowed"
                        }, [
                          (openBlock(), createBlock("svg", {
                            xmlns: "http://www.w3.org/2000/svg",
                            viewBox: "0 0 24 24",
                            class: "w-4 h-4 fill-current opacity-50 shrink-0"
                          }, [
                            createVNode("path", { d: "M22,15a1,1,0,0,0-1,1v4a1,1,0,0,1-1,1H4a1,1,0,0,1-1-1V16a1,1,0,0,0-2,0v4a3,3,0,0,0,3,3H20a3,3,0,0,0,3-3V16A1,1,0,0,0,22,15Z" }),
                            createVNode("path", { d: "M11.232,17.64a1,1,0,0,0,1.536,0l5-6A1,1,0,0,0,17,10H13V2a1,1,0,0,0-2,0v8H7a1,1,0,0,0-.768,1.64Z" })
                          ])),
                          createVNode("span", { class: "ml-2" }, toDisplayString(archiveProcessing.value ? "ZIP..." : "ZIP"), 1)
                        ], 8, ["disabled"])) : createCommentVNode("", true)
                      ])
                    ])
                  ], 32),
                  __props.files.length ? (openBlock(), createBlock("div", {
                    key: 0,
                    class: "mt-4 flex flex-wrap gap-1 border-b border-gray-300 dark:border-gray-500"
                  }, [
                    (openBlock(true), createBlock(Fragment, null, renderList(__props.files, (file) => {
                      return openBlock(), createBlock("button", {
                        key: file.key,
                        type: "button",
                        disabled: !file.exists,
                        onClick: ($event) => selectFile(file),
                        class: [
                          "px-3 py-1.5 text-xs font-semibold border border-b-0 rounded-t transition-colors duration-200",
                          activeFile.value === file.name ? "bg-sky-600 text-white border-sky-600" : "bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-200 border-gray-300 dark:border-gray-500 hover:bg-gray-200 dark:hover:bg-gray-700",
                          !file.exists ? "opacity-40 cursor-not-allowed" : ""
                        ]
                      }, toDisplayString(file.name), 11, ["disabled", "onClick"]);
                    }), 128))
                  ])) : createCommentVNode("", true),
                  currentFileExists.value ? (openBlock(), createBlock("div", {
                    key: 1,
                    class: "mt-2 flex justify-center items-center gap-3"
                  }, [
                    createVNode("span", { class: "font-semibold text-red-500 dark:text-red-400 text-xl" }, toDisplayString(activeFile.value), 1),
                    createVNode("span", { class: "text-slate-700 dark:text-slate-300 text-[10px]" }, " Строк: " + toDisplayString(lineCount.value), 1)
                  ])) : createCommentVNode("", true),
                  loading.value ? (openBlock(), createBlock("div", {
                    key: 2,
                    class: "mt-2 p-4 text-sm text-slate-700 dark:text-slate-200"
                  }, toDisplayString(unref(t)("downloadStatus")), 1)) : (openBlock(), createBlock("div", {
                    key: 3,
                    class: "mt-2 h-[520px] overflow-auto border-2 border-gray-400 rounded bg-gray-100 dark:bg-gray-800 text-xs font-mono"
                  }, [
                    (openBlock(true), createBlock(Fragment, null, renderList(contentLines.value, (line, index) => {
                      return openBlock(), createBlock("div", {
                        key: index,
                        class: "flex min-w-max leading-5"
                      }, [
                        createVNode("div", { class: "w-16 shrink-0 px-2 text-right select-none text-gray-400 dark:text-gray-500 border-r border-gray-300 dark:border-gray-600 bg-gray-200 dark:bg-gray-900" }, toDisplayString(index + 1), 1),
                        createVNode("pre", { class: "px-3 m-0 font-semibold whitespace-pre text-blue-800 dark:text-blue-200" }, toDisplayString(line), 1)
                      ]);
                    }), 128))
                  ]))
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
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Pages/Admin/System/SitemapPage.vue");
  return _sfc_setup ? _sfc_setup(props, ctx) : void 0;
};
export {
  _sfc_main as default
};
