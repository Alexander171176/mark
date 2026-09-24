import { ref, onMounted, watch, useSSRContext, computed, mergeProps, unref, withCtx, createVNode, toDisplayString, createBlock, openBlock, Fragment, renderList, createCommentVNode, createTextVNode } from "vue";
import { ssrRenderAttrs, ssrIncludeBooleanAttr, ssrLooseContain, ssrRenderComponent, ssrInterpolate, ssrRenderList, ssrRenderClass, ssrRenderAttr } from "vue/server-renderer";
import { usePage, router } from "@inertiajs/vue3";
import { useI18n } from "vue-i18n";
import { A as AdminLayout } from "./AdminLayout-03yd3nxC.js";
import { T as TitlePage } from "./TitlePage-CBZZ8YZB.js";
import { EditorState } from "@codemirror/state";
import { EditorView, lineNumbers, highlightActiveLineGutter, keymap } from "@codemirror/view";
import { history, defaultKeymap, historyKeymap } from "@codemirror/commands";
import { foldGutter, indentOnInput, syntaxHighlighting, defaultHighlightStyle, foldKeymap } from "@codemirror/language";
import { javascript } from "@codemirror/lang-javascript";
import { oneDark } from "@codemirror/theme-one-dark";
import { _ as _export_sfc } from "./_plugin-vue_export-helper-1tPrXgE0.js";
import "vue-toastification";
import "./LocaleSelectOption-BeLdazeX.js";
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
const _sfc_main$1 = {
  __name: "CodeMirrorEditor",
  __ssrInlineRender: true,
  props: {
    modelValue: String,
    theme: { type: String, default: "light" }
  },
  emits: ["update:modelValue"],
  setup(__props, { emit: __emit }) {
    const props = __props;
    const emit = __emit;
    const editor = ref(null);
    const isDarkTheme = ref(false);
    let editorView;
    const buildExtensions = (darkTheme = false) => {
      const baseExtensions = [
        lineNumbers(),
        highlightActiveLineGutter(),
        history(),
        foldGutter(),
        indentOnInput(),
        syntaxHighlighting(defaultHighlightStyle),
        keymap.of([...defaultKeymap, ...historyKeymap, ...foldKeymap]),
        javascript(),
        EditorView.theme({ ".cm-content": { "font-size": "16px" } }),
        EditorView.updateListener.of((update) => {
          if (update.docChanged) {
            emit("update:modelValue", update.state.doc.toString());
          }
        })
      ];
      if (darkTheme) baseExtensions.push(oneDark);
      return baseExtensions;
    };
    onMounted(() => {
      editorView = new EditorView({
        state: EditorState.create({
          doc: props.modelValue,
          extensions: buildExtensions(isDarkTheme.value)
        }),
        parent: editor.value
      });
    });
    watch(() => props.modelValue, (newValue) => {
      if (editorView) {
        const currentValue = editorView.state.doc.toString();
        if (newValue !== currentValue) {
          editorView.dispatch({
            changes: { from: 0, to: currentValue.length, insert: newValue }
          });
        }
      }
    });
    watch(isDarkTheme, (newValue) => {
      if (editorView) {
        const newState = EditorState.create({
          doc: editorView.state.doc.toString(),
          extensions: buildExtensions(newValue)
        });
        editorView.setState(newState);
      }
    });
    return (_ctx, _push, _parent, _attrs) => {
      _push(`<div${ssrRenderAttrs(_attrs)} data-v-e59b5d9f><div class="flex justify-center items-center my-1" data-v-e59b5d9f><input type="checkbox" id="theme-toggle"${ssrIncludeBooleanAttr(Array.isArray(isDarkTheme.value) ? ssrLooseContain(isDarkTheme.value, null) : isDarkTheme.value) ? " checked" : ""} class="sr-only" data-v-e59b5d9f><label for="theme-toggle" class="flex items-center justify-center w-6 h-6 bg-slate-100 hover:bg-yellow-100 rounded-full border border-slate-300 cursor-pointer" data-v-e59b5d9f>`);
      if (!isDarkTheme.value) {
        _push(`<svg class="w-3 h-3" viewBox="0 0 16 16" xmlns="http://www.w3.org/2000/svg" data-v-e59b5d9f><path class="fill-current text-slate-400" d="M7 0h2v2H7V0Zm5.88 1.637 1.414 1.415-1.415 1.413-1.414-1.414 1.415-1.414ZM14 7h2v2h-2V7Zm-1.05 7.433-1.415-1.414 1.414-1.414 1.415 1.413-1.414 1.415ZM7 14h2v2H7v-2Zm-4.02.363L1.566 12.95l1.415-1.414 1.414 1.415-1.415 1.413ZM0 7h2v2H0V7Zm3.05-5.293L4.465 3.12 3.05 4.535 1.636 3.121 3.05 1.707Z" data-v-e59b5d9f></path><path class="fill-current text-slate-500" d="M8 4C5.8 4 4 5.8 4 8s1.8 4 4 4 4-1.8 4-4-1.8-4-4-4Z" data-v-e59b5d9f></path></svg>`);
      } else {
        _push(`<svg class="w-3 h-3" viewBox="0 0 16 16" xmlns="http://www.w3.org/2000/svg" data-v-e59b5d9f><path class="fill-current text-slate-400" d="M6.2 2C3.2 2.8 1 5.6 1 8.9 1 12.8 4.2 16 8.1 16c3.3 0 6-2.2 6.9-5.2C9.7 12.2 4.8 7.3 6.2 2Z" data-v-e59b5d9f></path><path class="fill-current text-slate-500" d="M12.5 6a.625.625 0 0 1-.625-.625 1.252 1.252 0 0 0-1.25-1.25.625.625 0 1 1 0-1.25 1.252 1.252 0 0 0 1.25-1.25.625.625 0 1 1 1.25 0c.001.69.56 1.249 1.25 1.25a.625.625 0 1 1 0 1.25c-.69.001-1.249.56-1.25 1.25A.625.625 0 0 1 12.5 6Z" data-v-e59b5d9f></path></svg>`);
      }
      _push(`</label></div><div class="code-editor" data-v-e59b5d9f></div></div>`);
    };
  }
};
const _sfc_setup$1 = _sfc_main$1.setup;
_sfc_main$1.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Components/Admin/UI/CodeMirrorEditor/CodeMirrorEditor.vue");
  return _sfc_setup$1 ? _sfc_setup$1(props, ctx) : void 0;
};
const CodeMirrorEditor = /* @__PURE__ */ _export_sfc(_sfc_main$1, [["__scopeId", "data-v-e59b5d9f"]]);
const asideStorageKey = "component-editor-aside-collapsed";
const _sfc_main = {
  __name: "Index",
  __ssrInlineRender: true,
  setup(__props) {
    const { t } = useI18n();
    const { props } = usePage();
    const fileContents = ref(props.fileContents || {});
    const groups = computed(() => Object.keys(fileContents.value || {}));
    const selectedGroup = ref(groups.value[0] || "");
    const selectedFile = ref("");
    const fileContent = ref("");
    const errorMessage = ref("");
    const successMessage = ref("");
    const isSaving = ref(false);
    const filesInSelectedGroup = computed(() => {
      if (!selectedGroup.value) {
        return {};
      }
      return fileContents.value[selectedGroup.value] || {};
    });
    const selectedFileShortName = computed(() => {
      if (!selectedFile.value) {
        return "";
      }
      return selectedFile.value.split("/").pop();
    });
    const selectGroup = (group) => {
      selectedGroup.value = group;
      selectedFile.value = "";
      fileContent.value = "";
      errorMessage.value = "";
      successMessage.value = "";
    };
    const selectFile = (fileName) => {
      selectedFile.value = fileName;
      fileContent.value = filesInSelectedGroup.value[fileName] || "";
      errorMessage.value = "";
      successMessage.value = "";
    };
    const saveChanges = () => {
      if (!selectedFile.value) {
        errorMessage.value = "Сначала выберите файл для редактирования";
        return;
      }
      errorMessage.value = "";
      successMessage.value = "";
      isSaving.value = true;
      router.post(
        route("admin.components.save"),
        {
          fileName: selectedFile.value,
          fileContent: fileContent.value
        },
        {
          preserveScroll: true,
          onSuccess: () => {
            successMessage.value = "Файл успешно сохранён";
            if (selectedGroup.value && fileContents.value[selectedGroup.value] && selectedFile.value) {
              fileContents.value[selectedGroup.value][selectedFile.value] = fileContent.value;
            }
          },
          onError: () => {
            errorMessage.value = "Не удалось сохранить изменения в файле";
          },
          onFinish: () => {
            isSaving.value = false;
          }
        }
      );
    };
    watch(groups, (newGroups) => {
      if (!selectedGroup.value && newGroups.length) {
        selectedGroup.value = newGroups[0];
      }
    });
    const isAsideCollapsed = ref(
      localStorage.getItem(asideStorageKey) === "true"
    );
    const toggleAside = () => {
      isAsideCollapsed.value = !isAsideCollapsed.value;
      localStorage.setItem(asideStorageKey, String(isAsideCollapsed.value));
    };
    return (_ctx, _push, _parent, _attrs) => {
      _push(ssrRenderComponent(AdminLayout, mergeProps({
        title: unref(t)("componentEditorHeader")
      }, _attrs), {
        header: withCtx((_, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(ssrRenderComponent(TitlePage, null, {
              default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                if (_push3) {
                  _push3(`${ssrInterpolate(unref(t)("componentEditorHeader"))}`);
                } else {
                  return [
                    createTextVNode(toDisplayString(unref(t)("componentEditorHeader")), 1)
                  ];
                }
              }),
              _: 1
            }, _parent2, _scopeId));
          } else {
            return [
              createVNode(TitlePage, null, {
                default: withCtx(() => [
                  createTextVNode(toDisplayString(unref(t)("componentEditorHeader")), 1)
                ]),
                _: 1
              })
            ];
          }
        }),
        default: withCtx((_, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(`<div class="w-full px-2 py-3 sm:px-4 lg:px-6"${_scopeId}><div class="mx-auto w-full max-w-7xl overflow-hidden rounded-xl border border-blue-300 bg-slate-50 shadow-md dark:border-blue-200 dark:bg-slate-800"${_scopeId}><div class="border-b border-rose-200 bg-amber-50 px-3 py-2 text-center text-xs font-semibold italic text-rose-500 sm:text-sm"${_scopeId}>${ssrInterpolate(unref(t)("componentEditorWarning"))}</div><div class="flex justify-center border-b border-slate-200 bg-white p-3 dark:border-slate-700 dark:bg-slate-900"${_scopeId}><div class="flex gap-2 overflow-x-auto pb-1"${_scopeId}><!--[-->`);
            ssrRenderList(groups.value, (group) => {
              _push2(`<button type="button" class="${ssrRenderClass([{
                "border-blue-600 bg-blue-600 text-white shadow-sm": selectedGroup.value === group,
                "border-slate-300 bg-slate-100 text-slate-700 hover:bg-slate-200 dark:border-slate-600 dark:bg-slate-700 dark:text-slate-100 dark:hover:bg-slate-600": selectedGroup.value !== group
              }, "shrink-0 rounded-sm border px-3 py-1 text-xs font-semibold transition-colors sm:text-sm"])}"${_scopeId}>${ssrInterpolate(group)}</button>`);
            });
            _push2(`<!--]--></div></div><div class="grid grid-cols-1 gap-0 transition-all duration-300 lg:grid-cols-12"${_scopeId}><aside class="${ssrRenderClass([{
              "lg:col-span-1": isAsideCollapsed.value,
              "lg:col-span-4 xl:col-span-3": !isAsideCollapsed.value
            }, "border-b border-slate-200 bg-slate-100 p-3 transition-all duration-300 dark:border-slate-700 dark:bg-slate-900 lg:border-b-0 lg:border-r"])}"${_scopeId}><div class="${ssrRenderClass([{
              "justify-center": isAsideCollapsed.value,
              "justify-between": !isAsideCollapsed.value
            }, "mb-3 flex items-center gap-2"])}"${_scopeId}>`);
            if (!isAsideCollapsed.value) {
              _push2(`<h2 class="text-sm font-bold text-slate-800 dark:text-slate-100"${_scopeId}> Файлы </h2>`);
            } else {
              _push2(`<!---->`);
            }
            if (!isAsideCollapsed.value) {
              _push2(`<span class="rounded-full bg-slate-300 px-2 py-0.5 font-semibold text-xs text-slate-600 dark:bg-slate-700 dark:text-slate-200"${_scopeId}>${ssrInterpolate(Object.keys(filesInSelectedGroup.value).length)}</span>`);
            } else {
              _push2(`<!---->`);
            }
            _push2(`<button type="button" class="${ssrRenderClass([{
              "mx-auto": isAsideCollapsed.value,
              "ml-auto": !isAsideCollapsed.value
            }, "inline-flex items-center justify-center rounded-sm border border-slate-300 bg-white px-2 py-1 text-xs font-semibold text-slate-700 transition-colors hover:bg-slate-100 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-100 dark:hover:bg-slate-700"])}"${_scopeId}>`);
            if (isAsideCollapsed.value) {
              _push2(`<span${_scopeId}> ▶ </span>`);
            } else {
              _push2(`<span${_scopeId}> ◀ </span>`);
            }
            _push2(`</button></div>`);
            if (isAsideCollapsed.value) {
              _push2(`<div class="hidden flex-col items-center gap-2 lg:flex"${_scopeId}><!--[-->`);
              ssrRenderList(filesInSelectedGroup.value, (content, fileName) => {
                _push2(`<button type="button"${ssrRenderAttr("title", fileName)} class="${ssrRenderClass([{
                  "border-teal-600 bg-teal-600 text-white": selectedFile.value === fileName,
                  "border-slate-300 bg-white text-slate-700 hover:bg-teal-50 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-100": selectedFile.value !== fileName
                }, "flex h-8 w-8 items-center justify-center rounded-sm border text-xs font-bold transition-colors"])}"${_scopeId}>${ssrInterpolate(fileName.split("/").pop().slice(0, 1).toUpperCase())}</button>`);
              });
              _push2(`<!--]--></div>`);
            } else if (Object.keys(filesInSelectedGroup.value).length) {
              _push2(`<div class="flex max-h-72 flex-col gap-2 overflow-y-auto pr-1 lg:max-h-[70vh]"${_scopeId}><!--[-->`);
              ssrRenderList(filesInSelectedGroup.value, (content, fileName) => {
                _push2(`<button type="button" class="${ssrRenderClass([{
                  "border-teal-600 bg-teal-600 text-white shadow-sm": selectedFile.value === fileName,
                  "border-slate-300 bg-white text-slate-700 hover:border-teal-400 hover:bg-teal-50 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-100 dark:hover:bg-slate-700": selectedFile.value !== fileName
                }, "w-full rounded-sm border px-3 py-1 text-left text-xs transition-colors sm:text-sm"])}"${_scopeId}><span class="block truncate font-semibold"${_scopeId}>${ssrInterpolate(fileName.split("/").pop())}</span><span class="mt-1 block truncate text-[11px] opacity-75"${_scopeId}>${ssrInterpolate(fileName)}</span></button>`);
              });
              _push2(`<!--]--></div>`);
            } else {
              _push2(`<div class="rounded-lg border border-dashed border-slate-300 p-4 text-center text-sm text-slate-500 dark:border-slate-600 dark:text-slate-300"${_scopeId}> В этой группе нет доступных файлов </div>`);
            }
            _push2(`</aside><main class="${ssrRenderClass([{
              "lg:col-span-11": isAsideCollapsed.value,
              "lg:col-span-8 xl:col-span-9": !isAsideCollapsed.value
            }, "min-w-0 bg-slate-50 p-3 transition-all duration-300 dark:bg-slate-800"])}"${_scopeId}>`);
            if (selectedFile.value) {
              _push2(`<div class="overflow-hidden rounded-xl border border-slate-300 bg-white shadow-sm dark:border-slate-600 dark:bg-slate-900"${_scopeId}><div class="flex flex-col gap-3 border-b border-slate-200 px-3 py-3 dark:border-slate-700 sm:flex-row sm:items-center sm:justify-between"${_scopeId}><div class="min-w-0"${_scopeId}><div class="text-xs font-semibold uppercase text-blue-500"${_scopeId}>${ssrInterpolate(unref(t)("editingFile"))}</div><h2 class="truncate text-sm font-bold text-slate-900 dark:text-slate-100 sm:text-base"${_scopeId}>${ssrInterpolate(selectedFileShortName.value)}</h2><p class="mt-1 break-all text-xs text-slate-500 dark:text-slate-400"${_scopeId}>${ssrInterpolate(selectedFile.value)}</p></div><button type="button"${ssrIncludeBooleanAttr(isSaving.value) ? " disabled" : ""} class="inline-flex items-center justify-center rounded-sm bg-teal-600 px-3 py-1 text-xs font-semibold text-white shadow-sm transition-colors hover:bg-teal-700 disabled:cursor-not-allowed disabled:opacity-60"${_scopeId}><svg class="mr-2 h-4 w-4 fill-current" viewBox="0 0 16 16"${_scopeId}><path d="M14.3 2.3L5 11.6 1.7 8.3c-.4-.4-1-.4-1.4 0-.4.4-.4 1 0 1.4l4 4c.2.2.4.3.7.3.3 0 .5-.1.7-.3l10-10c.4-.4.4-1 0-1.4-.4-.4-1-.4-1.4 0z"${_scopeId}></path></svg>`);
              if (isSaving.value) {
                _push2(`<span${_scopeId}> Сохранение... </span>`);
              } else {
                _push2(`<span${_scopeId}>${ssrInterpolate(unref(t)("save"))}</span>`);
              }
              _push2(`</button></div><div class="p-2 sm:p-3"${_scopeId}><div class="overflow-hidden rounded-lg border border-slate-700"${_scopeId}>`);
              _push2(ssrRenderComponent(CodeMirrorEditor, {
                modelValue: fileContent.value,
                "onUpdate:modelValue": ($event) => fileContent.value = $event,
                theme: "dark",
                class: "min-h-[420px] w-full"
              }, null, _parent2, _scopeId));
              _push2(`</div>`);
              if (errorMessage.value) {
                _push2(`<div class="mt-3 rounded-lg border border-red-300 bg-red-50 px-3 py-2 text-sm text-red-700"${_scopeId}>${ssrInterpolate(errorMessage.value)}</div>`);
              } else {
                _push2(`<!---->`);
              }
              if (successMessage.value) {
                _push2(`<div class="mt-3 rounded-lg border border-emerald-300 bg-emerald-50 px-3 py-2 text-sm text-emerald-700"${_scopeId}>${ssrInterpolate(successMessage.value)}</div>`);
              } else {
                _push2(`<!---->`);
              }
              _push2(`</div></div>`);
            } else {
              _push2(`<div class="flex min-h-[420px] items-center justify-center rounded-xl border border-dashed border-slate-300 bg-white p-6 text-center dark:border-slate-600 dark:bg-slate-900"${_scopeId}><div${_scopeId}><div class="text-base font-bold text-slate-700 dark:text-slate-100"${_scopeId}> Выберите файл </div><p class="mt-2 text-sm text-slate-500 dark:text-slate-400"${_scopeId}> Сначала выберите группу, затем файл из списка. </p></div></div>`);
            }
            _push2(`</main></div></div></div>`);
          } else {
            return [
              createVNode("div", { class: "w-full px-2 py-3 sm:px-4 lg:px-6" }, [
                createVNode("div", { class: "mx-auto w-full max-w-7xl overflow-hidden rounded-xl border border-blue-300 bg-slate-50 shadow-md dark:border-blue-200 dark:bg-slate-800" }, [
                  createVNode("div", { class: "border-b border-rose-200 bg-amber-50 px-3 py-2 text-center text-xs font-semibold italic text-rose-500 sm:text-sm" }, toDisplayString(unref(t)("componentEditorWarning")), 1),
                  createVNode("div", { class: "flex justify-center border-b border-slate-200 bg-white p-3 dark:border-slate-700 dark:bg-slate-900" }, [
                    createVNode("div", { class: "flex gap-2 overflow-x-auto pb-1" }, [
                      (openBlock(true), createBlock(Fragment, null, renderList(groups.value, (group) => {
                        return openBlock(), createBlock("button", {
                          key: group,
                          type: "button",
                          onClick: ($event) => selectGroup(group),
                          class: ["shrink-0 rounded-sm border px-3 py-1 text-xs font-semibold transition-colors sm:text-sm", {
                            "border-blue-600 bg-blue-600 text-white shadow-sm": selectedGroup.value === group,
                            "border-slate-300 bg-slate-100 text-slate-700 hover:bg-slate-200 dark:border-slate-600 dark:bg-slate-700 dark:text-slate-100 dark:hover:bg-slate-600": selectedGroup.value !== group
                          }]
                        }, toDisplayString(group), 11, ["onClick"]);
                      }), 128))
                    ])
                  ]),
                  createVNode("div", { class: "grid grid-cols-1 gap-0 transition-all duration-300 lg:grid-cols-12" }, [
                    createVNode("aside", {
                      class: ["border-b border-slate-200 bg-slate-100 p-3 transition-all duration-300 dark:border-slate-700 dark:bg-slate-900 lg:border-b-0 lg:border-r", {
                        "lg:col-span-1": isAsideCollapsed.value,
                        "lg:col-span-4 xl:col-span-3": !isAsideCollapsed.value
                      }]
                    }, [
                      createVNode("div", {
                        class: ["mb-3 flex items-center gap-2", {
                          "justify-center": isAsideCollapsed.value,
                          "justify-between": !isAsideCollapsed.value
                        }]
                      }, [
                        !isAsideCollapsed.value ? (openBlock(), createBlock("h2", {
                          key: 0,
                          class: "text-sm font-bold text-slate-800 dark:text-slate-100"
                        }, " Файлы ")) : createCommentVNode("", true),
                        !isAsideCollapsed.value ? (openBlock(), createBlock("span", {
                          key: 1,
                          class: "rounded-full bg-slate-300 px-2 py-0.5 font-semibold text-xs text-slate-600 dark:bg-slate-700 dark:text-slate-200"
                        }, toDisplayString(Object.keys(filesInSelectedGroup.value).length), 1)) : createCommentVNode("", true),
                        createVNode("button", {
                          type: "button",
                          onClick: toggleAside,
                          class: ["inline-flex items-center justify-center rounded-sm border border-slate-300 bg-white px-2 py-1 text-xs font-semibold text-slate-700 transition-colors hover:bg-slate-100 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-100 dark:hover:bg-slate-700", {
                            "mx-auto": isAsideCollapsed.value,
                            "ml-auto": !isAsideCollapsed.value
                          }]
                        }, [
                          isAsideCollapsed.value ? (openBlock(), createBlock("span", { key: 0 }, " ▶ ")) : (openBlock(), createBlock("span", { key: 1 }, " ◀ "))
                        ], 2)
                      ], 2),
                      isAsideCollapsed.value ? (openBlock(), createBlock("div", {
                        key: 0,
                        class: "hidden flex-col items-center gap-2 lg:flex"
                      }, [
                        (openBlock(true), createBlock(Fragment, null, renderList(filesInSelectedGroup.value, (content, fileName) => {
                          return openBlock(), createBlock("button", {
                            key: fileName,
                            type: "button",
                            onClick: ($event) => selectFile(fileName),
                            title: fileName,
                            class: ["flex h-8 w-8 items-center justify-center rounded-sm border text-xs font-bold transition-colors", {
                              "border-teal-600 bg-teal-600 text-white": selectedFile.value === fileName,
                              "border-slate-300 bg-white text-slate-700 hover:bg-teal-50 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-100": selectedFile.value !== fileName
                            }]
                          }, toDisplayString(fileName.split("/").pop().slice(0, 1).toUpperCase()), 11, ["onClick", "title"]);
                        }), 128))
                      ])) : Object.keys(filesInSelectedGroup.value).length ? (openBlock(), createBlock("div", {
                        key: 1,
                        class: "flex max-h-72 flex-col gap-2 overflow-y-auto pr-1 lg:max-h-[70vh]"
                      }, [
                        (openBlock(true), createBlock(Fragment, null, renderList(filesInSelectedGroup.value, (content, fileName) => {
                          return openBlock(), createBlock("button", {
                            key: fileName,
                            type: "button",
                            onClick: ($event) => selectFile(fileName),
                            class: ["w-full rounded-sm border px-3 py-1 text-left text-xs transition-colors sm:text-sm", {
                              "border-teal-600 bg-teal-600 text-white shadow-sm": selectedFile.value === fileName,
                              "border-slate-300 bg-white text-slate-700 hover:border-teal-400 hover:bg-teal-50 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-100 dark:hover:bg-slate-700": selectedFile.value !== fileName
                            }]
                          }, [
                            createVNode("span", { class: "block truncate font-semibold" }, toDisplayString(fileName.split("/").pop()), 1),
                            createVNode("span", { class: "mt-1 block truncate text-[11px] opacity-75" }, toDisplayString(fileName), 1)
                          ], 10, ["onClick"]);
                        }), 128))
                      ])) : (openBlock(), createBlock("div", {
                        key: 2,
                        class: "rounded-lg border border-dashed border-slate-300 p-4 text-center text-sm text-slate-500 dark:border-slate-600 dark:text-slate-300"
                      }, " В этой группе нет доступных файлов "))
                    ], 2),
                    createVNode("main", {
                      class: ["min-w-0 bg-slate-50 p-3 transition-all duration-300 dark:bg-slate-800", {
                        "lg:col-span-11": isAsideCollapsed.value,
                        "lg:col-span-8 xl:col-span-9": !isAsideCollapsed.value
                      }]
                    }, [
                      selectedFile.value ? (openBlock(), createBlock("div", {
                        key: 0,
                        class: "overflow-hidden rounded-xl border border-slate-300 bg-white shadow-sm dark:border-slate-600 dark:bg-slate-900"
                      }, [
                        createVNode("div", { class: "flex flex-col gap-3 border-b border-slate-200 px-3 py-3 dark:border-slate-700 sm:flex-row sm:items-center sm:justify-between" }, [
                          createVNode("div", { class: "min-w-0" }, [
                            createVNode("div", { class: "text-xs font-semibold uppercase text-blue-500" }, toDisplayString(unref(t)("editingFile")), 1),
                            createVNode("h2", { class: "truncate text-sm font-bold text-slate-900 dark:text-slate-100 sm:text-base" }, toDisplayString(selectedFileShortName.value), 1),
                            createVNode("p", { class: "mt-1 break-all text-xs text-slate-500 dark:text-slate-400" }, toDisplayString(selectedFile.value), 1)
                          ]),
                          createVNode("button", {
                            type: "button",
                            onClick: saveChanges,
                            disabled: isSaving.value,
                            class: "inline-flex items-center justify-center rounded-sm bg-teal-600 px-3 py-1 text-xs font-semibold text-white shadow-sm transition-colors hover:bg-teal-700 disabled:cursor-not-allowed disabled:opacity-60"
                          }, [
                            (openBlock(), createBlock("svg", {
                              class: "mr-2 h-4 w-4 fill-current",
                              viewBox: "0 0 16 16"
                            }, [
                              createVNode("path", { d: "M14.3 2.3L5 11.6 1.7 8.3c-.4-.4-1-.4-1.4 0-.4.4-.4 1 0 1.4l4 4c.2.2.4.3.7.3.3 0 .5-.1.7-.3l10-10c.4-.4.4-1 0-1.4-.4-.4-1-.4-1.4 0z" })
                            ])),
                            isSaving.value ? (openBlock(), createBlock("span", { key: 0 }, " Сохранение... ")) : (openBlock(), createBlock("span", { key: 1 }, toDisplayString(unref(t)("save")), 1))
                          ], 8, ["disabled"])
                        ]),
                        createVNode("div", { class: "p-2 sm:p-3" }, [
                          createVNode("div", { class: "overflow-hidden rounded-lg border border-slate-700" }, [
                            createVNode(CodeMirrorEditor, {
                              modelValue: fileContent.value,
                              "onUpdate:modelValue": ($event) => fileContent.value = $event,
                              theme: "dark",
                              class: "min-h-[420px] w-full"
                            }, null, 8, ["modelValue", "onUpdate:modelValue"])
                          ]),
                          errorMessage.value ? (openBlock(), createBlock("div", {
                            key: 0,
                            class: "mt-3 rounded-lg border border-red-300 bg-red-50 px-3 py-2 text-sm text-red-700"
                          }, toDisplayString(errorMessage.value), 1)) : createCommentVNode("", true),
                          successMessage.value ? (openBlock(), createBlock("div", {
                            key: 1,
                            class: "mt-3 rounded-lg border border-emerald-300 bg-emerald-50 px-3 py-2 text-sm text-emerald-700"
                          }, toDisplayString(successMessage.value), 1)) : createCommentVNode("", true)
                        ])
                      ])) : (openBlock(), createBlock("div", {
                        key: 1,
                        class: "flex min-h-[420px] items-center justify-center rounded-xl border border-dashed border-slate-300 bg-white p-6 text-center dark:border-slate-600 dark:bg-slate-900"
                      }, [
                        createVNode("div", null, [
                          createVNode("div", { class: "text-base font-bold text-slate-700 dark:text-slate-100" }, " Выберите файл "),
                          createVNode("p", { class: "mt-2 text-sm text-slate-500 dark:text-slate-400" }, " Сначала выберите группу, затем файл из списка. ")
                        ])
                      ]))
                    ], 2)
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
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Pages/Admin/System/Components/Index.vue");
  return _sfc_setup ? _sfc_setup(props, ctx) : void 0;
};
export {
  _sfc_main as default
};
