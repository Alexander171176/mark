import { ref, computed, onMounted, mergeProps, unref, withCtx, createVNode, createBlock, createCommentVNode, createTextVNode, openBlock, toDisplayString, Fragment, renderList, useSSRContext } from "vue";
import { ssrRenderComponent, ssrInterpolate, ssrIncludeBooleanAttr, ssrRenderClass, ssrRenderStyle, ssrRenderList, ssrRenderAttr } from "vue/server-renderer";
import { useI18n } from "vue-i18n";
import axios from "axios";
import { useToast } from "vue-toastification";
import { A as AdminLayout } from "./AdminLayout-03yd3nxC.js";
import { T as TitlePage } from "./TitlePage-CBZZ8YZB.js";
import "@inertiajs/vue3";
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
  __name: "FileBackup",
  __ssrInlineRender: true,
  setup(__props) {
    const { t } = useI18n();
    const toast = useToast();
    const isProcessing = ref(false);
    const progress = ref(0);
    const archives = ref([]);
    const processTitle = ref("");
    const processMessage = ref("");
    const processStatus = ref("");
    const processedFiles = ref(0);
    const totalFiles = ref(0);
    const addedFiles = ref(0);
    const skippedFiles = ref(0);
    const archiveFiles = ref(0);
    const archiveSize = ref(0);
    const currentArchive = ref("");
    const processLog = ref([]);
    const formattedArchiveSize = computed(() => {
      if (!archiveSize.value) {
        return "";
      }
      return `${(archiveSize.value / 1024 / 1024).toFixed(2)} MB`;
    });
    const addProcessMessage = (message) => {
      if (!message) {
        return;
      }
      processMessage.value = message;
      processLog.value.unshift({
        time: (/* @__PURE__ */ new Date()).toLocaleTimeString(),
        message
      });
      if (processLog.value.length > 20) {
        processLog.value.pop();
      }
    };
    const addProcessErrors = (errors) => {
      if (!Array.isArray(errors)) {
        return;
      }
      errors.forEach((error) => {
        if (error) {
          addProcessMessage(error);
        }
      });
    };
    const resetProcess = () => {
      isProcessing.value = false;
      progress.value = 0;
      processTitle.value = "";
      processMessage.value = "";
      processStatus.value = "";
      processedFiles.value = 0;
      totalFiles.value = 0;
      addedFiles.value = 0;
      skippedFiles.value = 0;
      archiveFiles.value = 0;
      archiveSize.value = 0;
      currentArchive.value = "";
      processLog.value = [];
    };
    const startProcess = (title, archiveName = "") => {
      isProcessing.value = true;
      progress.value = 1;
      processStatus.value = "processing";
      processTitle.value = title;
      processMessage.value = "";
      processedFiles.value = 0;
      totalFiles.value = 0;
      addedFiles.value = 0;
      skippedFiles.value = 0;
      archiveFiles.value = 0;
      archiveSize.value = 0;
      currentArchive.value = archiveName;
      processLog.value = [];
    };
    const updateProcessState = (data, fallbackMessage = "") => {
      progress.value = data.progress ?? progress.value;
      processStatus.value = data.status || processStatus.value || "processing";
      processedFiles.value = data.processed ?? processedFiles.value;
      totalFiles.value = data.total ?? totalFiles.value;
      addedFiles.value = data.added ?? addedFiles.value;
      skippedFiles.value = data.skipped ?? skippedFiles.value;
      archiveFiles.value = data.archive_files ?? archiveFiles.value;
      archiveSize.value = data.archive_size ?? archiveSize.value;
      currentArchive.value = data.filename || currentArchive.value;
      if (data.message) {
        addProcessMessage(
          data.message
        );
      } else if (fallbackMessage && !processMessage.value) {
        addProcessMessage(
          fallbackMessage
        );
      }
      if (Array.isArray(data.errors) && data.errors.length) {
        addProcessErrors(
          data.errors
        );
      }
    };
    const fetchArchives = async () => {
      try {
        const response = await axios.get(
          route("admin.files.list")
        );
        archives.value = response.data.archives || [];
      } catch (error) {
        console.error(
          "Failed to load archives:",
          error
        );
        archives.value = [];
        toast.error(
          t("failedToLoadBackups")
        );
      }
    };
    const createArchive = async () => {
      var _a;
      if (isProcessing.value) {
        return;
      }
      startProcess(
        "Создание резервной копии сайта"
      );
      addProcessMessage(
        "Подготовка списка файлов..."
      );
      try {
        const startResponse = await axios.post(
          route(
            "admin.files.start"
          )
        );
        const job = startResponse.data.job;
        if (!job) {
          throw new Error(
            "Не получен идентификатор задачи архивации"
          );
        }
        updateProcessState(
          startResponse.data,
          "Список файлов подготовлен"
        );
        while (true) {
          const response = await axios.post(
            route(
              "admin.files.process"
            ),
            {
              job
            }
          );
          const data = response.data;
          updateProcessState(
            data,
            "Архивирование продолжается..."
          );
          if (data.status === "done") {
            progress.value = 100;
            processStatus.value = "done";
            isProcessing.value = false;
            addProcessMessage(
              "Архив успешно создан"
            );
            toast.success(
              t("archiveCreated")
            );
            await fetchArchives();
            setTimeout(
              resetProcess,
              5e3
            );
            break;
          }
          if (data.status === "error") {
            processStatus.value = "error";
            isProcessing.value = false;
            toast.error(
              data.message || t(
                "archiveCreateFailed"
              )
            );
            break;
          }
        }
      } catch (error) {
        const data = (_a = error == null ? void 0 : error.response) == null ? void 0 : _a.data;
        const message = (data == null ? void 0 : data.message) || (error == null ? void 0 : error.message) || t("archiveCreateFailed");
        processStatus.value = "error";
        isProcessing.value = false;
        addProcessMessage(
          message
        );
        if (Array.isArray(data == null ? void 0 : data.errors)) {
          addProcessErrors(
            data.errors
          );
        }
        toast.error(
          message
        );
      }
    };
    const handleDelete = async (filename) => {
      var _a, _b;
      if (!filename) {
        return;
      }
      if (!confirm(
        t(
          "backupConfirmDeleteBackup"
        )
      )) {
        return;
      }
      startProcess(
        "Удаление архива",
        filename
      );
      progress.value = 50;
      addProcessMessage(
        `Удаление архива: ${filename}`
      );
      try {
        await axios.delete(
          route(
            "admin.files.delete"
          ),
          {
            data: {
              file: filename
            }
          }
        );
        progress.value = 100;
        processStatus.value = "done";
        isProcessing.value = false;
        addProcessMessage(
          "Архив успешно удалён"
        );
        toast.success(
          t("backupDeleted")
        );
        await fetchArchives();
        setTimeout(
          resetProcess,
          1500
        );
      } catch (error) {
        const message = ((_b = (_a = error == null ? void 0 : error.response) == null ? void 0 : _a.data) == null ? void 0 : _b.message) || t(
          "backupDeleteFailed"
        );
        processStatus.value = "error";
        isProcessing.value = false;
        addProcessMessage(
          message
        );
        toast.error(
          message
        );
      }
    };
    const downloadArchive = (filename) => {
      window.open(
        route(
          "admin.files.download",
          {
            file: filename
          }
        ),
        "_blank"
      );
    };
    onMounted(
      fetchArchives
    );
    return (_ctx, _push, _parent, _attrs) => {
      _push(ssrRenderComponent(AdminLayout, mergeProps({
        title: unref(t)("fileBackup")
      }, _attrs), {
        header: withCtx((_, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(ssrRenderComponent(TitlePage, null, {
              default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                if (_push3) {
                  _push3(`${ssrInterpolate(unref(t)("fileBackup"))}`);
                } else {
                  return [
                    createTextVNode(toDisplayString(unref(t)("fileBackup")), 1)
                  ];
                }
              }),
              _: 1
            }, _parent2, _scopeId));
          } else {
            return [
              createVNode(TitlePage, null, {
                default: withCtx(() => [
                  createTextVNode(toDisplayString(unref(t)("fileBackup")), 1)
                ]),
                _: 1
              })
            ];
          }
        }),
        default: withCtx((_, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(`<div class="px-2 py-2 w-full max-w-12xl mx-auto"${_scopeId}><div class="p-4 bg-slate-50 dark:bg-slate-700 border border-blue-400 dark:border-blue-200 overflow-hidden shadow-md shadow-gray-500 dark:shadow-slate-400 bg-opacity-95 dark:bg-opacity-95"${_scopeId}><div class="sm:flex sm:justify-between sm:items-center mb-4"${_scopeId}><button type="button"${ssrIncludeBooleanAttr(isProcessing.value) ? " disabled" : ""} class="flex items-center btn px-2 py-0.5 bg-sky-600 text-white text-sm font-semibold rounded-sm shadow-md transition-colors duration-300 ease-in-out hover:bg-sky-700 focus:bg-sky-700 focus:outline-none disabled:opacity-50 disabled:cursor-not-allowed"${_scopeId}><svg class="w-4 h-4 fill-current opacity-50 shrink-0 mr-1" viewBox="0 0 16 16"${_scopeId}><path d="M15 7H9V1c0-.6-.4-1-1-1S7 .4 7 1v6H1c-.6 0-1 .4-1 1s.4 1 1 1h6v6c0 .6.4 1 1 1s1-.4 1-1V9h6c.6 0 1-.4 1-1s-.4-1-1-1z"${_scopeId}></path></svg> ${ssrInterpolate(unref(t)("createArchive"))}</button></div>`);
            if (isProcessing.value || processStatus.value) {
              _push2(`<div class="mb-4 rounded border border-blue-300 dark:border-blue-200 bg-white dark:bg-slate-800 shadow p-4"${_scopeId}><div class="flex items-center justify-between mb-2"${_scopeId}><div${_scopeId}><h3 class="text-sm font-semibold text-slate-700 dark:text-slate-100"${_scopeId}>${ssrInterpolate(processTitle.value)}</h3><div class="text-xs text-slate-500 dark:text-slate-300"${_scopeId}>${ssrInterpolate(currentArchive.value)}</div></div><span class="${ssrRenderClass([{
                "bg-blue-100 text-blue-700": processStatus.value === "processing",
                "bg-green-100 text-green-700": processStatus.value === "done",
                "bg-red-100 text-red-700": processStatus.value === "error"
              }, "px-2 py-0.5 rounded text-xs font-semibold"])}"${_scopeId}>${ssrInterpolate(processStatus.value === "done" ? "Готово" : processStatus.value === "error" ? "Ошибка" : "В процессе")}</span></div><div class="${ssrRenderClass([{
                "text-blue-700 dark:text-blue-200": processStatus.value !== "error",
                "text-red-700 dark:text-red-300": processStatus.value === "error"
              }, "mb-2 text-sm font-medium"])}"${_scopeId}>${ssrInterpolate(processMessage.value)}</div><div class="w-full h-4 bg-gray-300 dark:bg-gray-600 rounded-full overflow-hidden"${_scopeId}><div class="${ssrRenderClass([{
                "bg-green-600": processStatus.value === "done",
                "bg-red-600": processStatus.value === "error"
              }, "h-full bg-blue-600 transition-all duration-500"])}" style="${ssrRenderStyle({
                width: `${progress.value}%`
              })}"${_scopeId}></div></div><div class="flex justify-between mt-1 text-xs text-slate-600 dark:text-slate-300"${_scopeId}><span${_scopeId}>${ssrInterpolate(progress.value)}% </span>`);
              if (totalFiles.value) {
                _push2(`<span${_scopeId}>${ssrInterpolate(processedFiles.value)} / ${ssrInterpolate(totalFiles.value)} файлов </span>`);
              } else {
                _push2(`<!---->`);
              }
              _push2(`</div>`);
              if (totalFiles.value) {
                _push2(`<div class="grid grid-cols-2 md:grid-cols-4 gap-2 mt-3 text-xs"${_scopeId}><div class="p-2 rounded bg-slate-100 dark:bg-slate-700"${_scopeId}><div class="text-slate-500 dark:text-slate-300"${_scopeId}> Всего </div><div class="font-semibold text-slate-700 dark:text-slate-100"${_scopeId}>${ssrInterpolate(totalFiles.value)}</div></div><div class="p-2 rounded bg-green-50 dark:bg-green-900/30"${_scopeId}><div class="text-green-700 dark:text-green-300"${_scopeId}> Добавлено </div><div class="font-semibold text-green-700 dark:text-green-200"${_scopeId}>${ssrInterpolate(addedFiles.value)}</div></div><div class="p-2 rounded bg-amber-50 dark:bg-amber-900/30"${_scopeId}><div class="text-amber-700 dark:text-amber-300"${_scopeId}> Пропущено </div><div class="font-semibold text-amber-700 dark:text-amber-200"${_scopeId}>${ssrInterpolate(skippedFiles.value)}</div></div><div class="p-2 rounded bg-sky-50 dark:bg-sky-900/30"${_scopeId}><div class="text-sky-700 dark:text-sky-300"${_scopeId}> В ZIP </div><div class="font-semibold text-sky-700 dark:text-sky-200"${_scopeId}>${ssrInterpolate(archiveFiles.value || addedFiles.value)}</div></div></div>`);
              } else {
                _push2(`<!---->`);
              }
              if (formattedArchiveSize.value) {
                _push2(`<div class="mt-2 text-xs text-slate-600 dark:text-slate-300"${_scopeId}><span class="font-semibold"${_scopeId}> Размер архива: </span> ${ssrInterpolate(formattedArchiveSize.value)}</div>`);
              } else {
                _push2(`<!---->`);
              }
              if (processLog.value.length) {
                _push2(`<div class="mt-3 max-h-48 overflow-y-auto border rounded bg-slate-50 dark:bg-slate-900"${_scopeId}><!--[-->`);
                ssrRenderList(processLog.value, (item, index) => {
                  _push2(`<div class="px-2 py-1 text-xs border-b border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-200 whitespace-pre-wrap break-words"${_scopeId}><span class="font-semibold text-sky-700 dark:text-sky-300"${_scopeId}>${ssrInterpolate(item.time)}</span> — <span${_scopeId}>${ssrInterpolate(item.message)}</span></div>`);
                });
                _push2(`<!--]--></div>`);
              } else {
                _push2(`<!---->`);
              }
              _push2(`</div>`);
            } else {
              _push2(`<!---->`);
            }
            _push2(`<div${_scopeId}><h2 class="text-slate-700 dark:text-slate-200 text-center text-md font-semibold mb-2"${_scopeId}>${ssrInterpolate(unref(t)("availableArchives"))}</h2>`);
            if (Array.isArray(archives.value) && archives.value.length) {
              _push2(`<ul class="divide-y border rounded"${_scopeId}><!--[-->`);
              ssrRenderList(archives.value, (archive) => {
                _push2(`<li class="flex items-center justify-between px-3 py-1 bg-gray-100 hover:bg-gray-50 dark:bg-gray-700 dark:hover:bg-slate-800"${_scopeId}><div${_scopeId}><div class="font-medium text-sm text-amber-700 dark:text-amber-200"${_scopeId}>${ssrInterpolate(archive.name)}</div><div class="text-xs text-gray-500 dark:text-gray-400"${_scopeId}>${ssrInterpolate((archive.size / 1024 / 1024).toFixed(2))} MB </div></div><div class="flex items-center space-x-2"${_scopeId}><button type="button"${ssrRenderAttr("title", unref(t)("download"))} class="w-8 h-8 flex items-center justify-center rounded-sm border border-slate-400 dark:border-slate-200 bg-indigo-100 hover:bg-indigo-200 dark:bg-indigo-700 dark:hover:bg-indigo-500 text-indigo-600 dark:text-slate-100 transition"${_scopeId}><svg class="w-6 h-6 fill-current" viewBox="0 0 24 24"${_scopeId}><path d="M5 20h14v-2H5m14-9h-4V3H9v6H5l7 7 7-7z"${_scopeId}></path></svg></button><button type="button"${ssrRenderAttr("title", unref(t)("remove"))}${ssrIncludeBooleanAttr(isProcessing.value) ? " disabled" : ""} class="w-8 h-8 flex items-center justify-center rounded-sm border border-slate-400 dark:border-slate-200 bg-red-200 hover:bg-red-300 dark:bg-red-700 dark:hover:bg-red-600 text-red-600 dark:text-slate-100 transition disabled:opacity-50 disabled:cursor-not-allowed"${_scopeId}><svg class="w-4 h-4 fill-current" viewBox="0 0 16 16"${_scopeId}><path d="M5 7h2v6H5V7zm4 0h2v6H9V7zm3-6v2h4v2h-1v10c0 .6-.4 1-1 1H2c-.6 0-1-.4-1-1V5H0V3h4V1c0-.6.4-1 1-1h6c.6 0 0 1 1 1zM6 2v1h4V2H6zm7 3H3v9h10V5z"${_scopeId}></path></svg></button></div></li>`);
              });
              _push2(`<!--]--></ul>`);
            } else {
              _push2(`<div class="text-gray-500 text-center"${_scopeId}>${ssrInterpolate(unref(t)("noData"))}</div>`);
            }
            _push2(`</div></div></div>`);
          } else {
            return [
              createVNode("div", { class: "px-2 py-2 w-full max-w-12xl mx-auto" }, [
                createVNode("div", { class: "p-4 bg-slate-50 dark:bg-slate-700 border border-blue-400 dark:border-blue-200 overflow-hidden shadow-md shadow-gray-500 dark:shadow-slate-400 bg-opacity-95 dark:bg-opacity-95" }, [
                  createVNode("div", { class: "sm:flex sm:justify-between sm:items-center mb-4" }, [
                    createVNode("button", {
                      type: "button",
                      disabled: isProcessing.value,
                      class: "flex items-center btn px-2 py-0.5 bg-sky-600 text-white text-sm font-semibold rounded-sm shadow-md transition-colors duration-300 ease-in-out hover:bg-sky-700 focus:bg-sky-700 focus:outline-none disabled:opacity-50 disabled:cursor-not-allowed",
                      onClick: createArchive
                    }, [
                      (openBlock(), createBlock("svg", {
                        class: "w-4 h-4 fill-current opacity-50 shrink-0 mr-1",
                        viewBox: "0 0 16 16"
                      }, [
                        createVNode("path", { d: "M15 7H9V1c0-.6-.4-1-1-1S7 .4 7 1v6H1c-.6 0-1 .4-1 1s.4 1 1 1h6v6c0 .6.4 1 1 1s1-.4 1-1V9h6c.6 0 1-.4 1-1s-.4-1-1-1z" })
                      ])),
                      createTextVNode(" " + toDisplayString(unref(t)("createArchive")), 1)
                    ], 8, ["disabled"])
                  ]),
                  isProcessing.value || processStatus.value ? (openBlock(), createBlock("div", {
                    key: 0,
                    class: "mb-4 rounded border border-blue-300 dark:border-blue-200 bg-white dark:bg-slate-800 shadow p-4"
                  }, [
                    createVNode("div", { class: "flex items-center justify-between mb-2" }, [
                      createVNode("div", null, [
                        createVNode("h3", { class: "text-sm font-semibold text-slate-700 dark:text-slate-100" }, toDisplayString(processTitle.value), 1),
                        createVNode("div", { class: "text-xs text-slate-500 dark:text-slate-300" }, toDisplayString(currentArchive.value), 1)
                      ]),
                      createVNode("span", {
                        class: ["px-2 py-0.5 rounded text-xs font-semibold", {
                          "bg-blue-100 text-blue-700": processStatus.value === "processing",
                          "bg-green-100 text-green-700": processStatus.value === "done",
                          "bg-red-100 text-red-700": processStatus.value === "error"
                        }]
                      }, toDisplayString(processStatus.value === "done" ? "Готово" : processStatus.value === "error" ? "Ошибка" : "В процессе"), 3)
                    ]),
                    createVNode("div", {
                      class: ["mb-2 text-sm font-medium", {
                        "text-blue-700 dark:text-blue-200": processStatus.value !== "error",
                        "text-red-700 dark:text-red-300": processStatus.value === "error"
                      }]
                    }, toDisplayString(processMessage.value), 3),
                    createVNode("div", { class: "w-full h-4 bg-gray-300 dark:bg-gray-600 rounded-full overflow-hidden" }, [
                      createVNode("div", {
                        class: ["h-full bg-blue-600 transition-all duration-500", {
                          "bg-green-600": processStatus.value === "done",
                          "bg-red-600": processStatus.value === "error"
                        }],
                        style: {
                          width: `${progress.value}%`
                        }
                      }, null, 6)
                    ]),
                    createVNode("div", { class: "flex justify-between mt-1 text-xs text-slate-600 dark:text-slate-300" }, [
                      createVNode("span", null, toDisplayString(progress.value) + "% ", 1),
                      totalFiles.value ? (openBlock(), createBlock("span", { key: 0 }, toDisplayString(processedFiles.value) + " / " + toDisplayString(totalFiles.value) + " файлов ", 1)) : createCommentVNode("", true)
                    ]),
                    totalFiles.value ? (openBlock(), createBlock("div", {
                      key: 0,
                      class: "grid grid-cols-2 md:grid-cols-4 gap-2 mt-3 text-xs"
                    }, [
                      createVNode("div", { class: "p-2 rounded bg-slate-100 dark:bg-slate-700" }, [
                        createVNode("div", { class: "text-slate-500 dark:text-slate-300" }, " Всего "),
                        createVNode("div", { class: "font-semibold text-slate-700 dark:text-slate-100" }, toDisplayString(totalFiles.value), 1)
                      ]),
                      createVNode("div", { class: "p-2 rounded bg-green-50 dark:bg-green-900/30" }, [
                        createVNode("div", { class: "text-green-700 dark:text-green-300" }, " Добавлено "),
                        createVNode("div", { class: "font-semibold text-green-700 dark:text-green-200" }, toDisplayString(addedFiles.value), 1)
                      ]),
                      createVNode("div", { class: "p-2 rounded bg-amber-50 dark:bg-amber-900/30" }, [
                        createVNode("div", { class: "text-amber-700 dark:text-amber-300" }, " Пропущено "),
                        createVNode("div", { class: "font-semibold text-amber-700 dark:text-amber-200" }, toDisplayString(skippedFiles.value), 1)
                      ]),
                      createVNode("div", { class: "p-2 rounded bg-sky-50 dark:bg-sky-900/30" }, [
                        createVNode("div", { class: "text-sky-700 dark:text-sky-300" }, " В ZIP "),
                        createVNode("div", { class: "font-semibold text-sky-700 dark:text-sky-200" }, toDisplayString(archiveFiles.value || addedFiles.value), 1)
                      ])
                    ])) : createCommentVNode("", true),
                    formattedArchiveSize.value ? (openBlock(), createBlock("div", {
                      key: 1,
                      class: "mt-2 text-xs text-slate-600 dark:text-slate-300"
                    }, [
                      createVNode("span", { class: "font-semibold" }, " Размер архива: "),
                      createTextVNode(" " + toDisplayString(formattedArchiveSize.value), 1)
                    ])) : createCommentVNode("", true),
                    processLog.value.length ? (openBlock(), createBlock("div", {
                      key: 2,
                      class: "mt-3 max-h-48 overflow-y-auto border rounded bg-slate-50 dark:bg-slate-900"
                    }, [
                      (openBlock(true), createBlock(Fragment, null, renderList(processLog.value, (item, index) => {
                        return openBlock(), createBlock("div", {
                          key: index,
                          class: "px-2 py-1 text-xs border-b border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-200 whitespace-pre-wrap break-words"
                        }, [
                          createVNode("span", { class: "font-semibold text-sky-700 dark:text-sky-300" }, toDisplayString(item.time), 1),
                          createTextVNode(" — "),
                          createVNode("span", null, toDisplayString(item.message), 1)
                        ]);
                      }), 128))
                    ])) : createCommentVNode("", true)
                  ])) : createCommentVNode("", true),
                  createVNode("div", null, [
                    createVNode("h2", { class: "text-slate-700 dark:text-slate-200 text-center text-md font-semibold mb-2" }, toDisplayString(unref(t)("availableArchives")), 1),
                    Array.isArray(archives.value) && archives.value.length ? (openBlock(), createBlock("ul", {
                      key: 0,
                      class: "divide-y border rounded"
                    }, [
                      (openBlock(true), createBlock(Fragment, null, renderList(archives.value, (archive) => {
                        return openBlock(), createBlock("li", {
                          key: archive.name,
                          class: "flex items-center justify-between px-3 py-1 bg-gray-100 hover:bg-gray-50 dark:bg-gray-700 dark:hover:bg-slate-800"
                        }, [
                          createVNode("div", null, [
                            createVNode("div", { class: "font-medium text-sm text-amber-700 dark:text-amber-200" }, toDisplayString(archive.name), 1),
                            createVNode("div", { class: "text-xs text-gray-500 dark:text-gray-400" }, toDisplayString((archive.size / 1024 / 1024).toFixed(2)) + " MB ", 1)
                          ]),
                          createVNode("div", { class: "flex items-center space-x-2" }, [
                            createVNode("button", {
                              type: "button",
                              title: unref(t)("download"),
                              class: "w-8 h-8 flex items-center justify-center rounded-sm border border-slate-400 dark:border-slate-200 bg-indigo-100 hover:bg-indigo-200 dark:bg-indigo-700 dark:hover:bg-indigo-500 text-indigo-600 dark:text-slate-100 transition",
                              onClick: ($event) => downloadArchive(
                                archive.name
                              )
                            }, [
                              (openBlock(), createBlock("svg", {
                                class: "w-6 h-6 fill-current",
                                viewBox: "0 0 24 24"
                              }, [
                                createVNode("path", { d: "M5 20h14v-2H5m14-9h-4V3H9v6H5l7 7 7-7z" })
                              ]))
                            ], 8, ["title", "onClick"]),
                            createVNode("button", {
                              type: "button",
                              title: unref(t)("remove"),
                              disabled: isProcessing.value,
                              class: "w-8 h-8 flex items-center justify-center rounded-sm border border-slate-400 dark:border-slate-200 bg-red-200 hover:bg-red-300 dark:bg-red-700 dark:hover:bg-red-600 text-red-600 dark:text-slate-100 transition disabled:opacity-50 disabled:cursor-not-allowed",
                              onClick: ($event) => handleDelete(
                                archive.name
                              )
                            }, [
                              (openBlock(), createBlock("svg", {
                                class: "w-4 h-4 fill-current",
                                viewBox: "0 0 16 16"
                              }, [
                                createVNode("path", { d: "M5 7h2v6H5V7zm4 0h2v6H9V7zm3-6v2h4v2h-1v10c0 .6-.4 1-1 1H2c-.6 0-1-.4-1-1V5H0V3h4V1c0-.6.4-1 1-1h6c.6 0 0 1 1 1zM6 2v1h4V2H6zm7 3H3v9h10V5z" })
                              ]))
                            ], 8, ["title", "disabled", "onClick"])
                          ])
                        ]);
                      }), 128))
                    ])) : (openBlock(), createBlock("div", {
                      key: 1,
                      class: "text-gray-500 text-center"
                    }, toDisplayString(unref(t)("noData")), 1))
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
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Pages/Admin/System/FileBackup.vue");
  return _sfc_setup ? _sfc_setup(props, ctx) : void 0;
};
export {
  _sfc_main as default
};
