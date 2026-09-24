import { ref, computed, onMounted, onBeforeUnmount, mergeProps, unref, withCtx, createVNode, createBlock, createCommentVNode, createTextVNode, openBlock, toDisplayString, Fragment, renderList, useSSRContext } from "vue";
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
  __name: "DatabaseBackup",
  __ssrInlineRender: true,
  setup(__props) {
    const { t } = useI18n();
    const toast = useToast();
    const backups = ref([]);
    const isProcessing = ref(false);
    const processTitle = ref("");
    const processMessage = ref("");
    const processStatus = ref("");
    const processStep = ref(0);
    const progress = ref(0);
    const activeFile = ref("");
    const elapsedSeconds = ref(0);
    const processLog = ref([]);
    let timer = null;
    let pollTimer = null;
    const elapsedTime = computed(() => {
      const minutes = Math.floor(
        elapsedSeconds.value / 60
      );
      const seconds = elapsedSeconds.value % 60;
      return `${minutes}:${seconds.toString().padStart(2, "0")}`;
    });
    const stepLabel = computed(() => {
      if (processStep.value === 1) {
        return "Создание страховочного дампа";
      }
      if (processStep.value === 2) {
        return "Восстановление выбранного дампа";
      }
      if (processStep.value === 3) {
        return "Откат при ошибке";
      }
      if (processStep.value === 4) {
        return "Завершено";
      }
      return processTitle.value;
    });
    const addLog = (message) => {
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
    const startTimer = () => {
      clearInterval(timer);
      elapsedSeconds.value = 0;
      timer = setInterval(() => {
        elapsedSeconds.value++;
      }, 1e3);
    };
    const stopTimer = () => {
      clearInterval(timer);
      timer = null;
    };
    const clearPollTimer = () => {
      clearTimeout(pollTimer);
      pollTimer = null;
    };
    const resetProcess = () => {
      isProcessing.value = false;
      processTitle.value = "";
      processMessage.value = "";
      processStatus.value = "";
      processStep.value = 0;
      progress.value = 0;
      activeFile.value = "";
      elapsedSeconds.value = 0;
      processLog.value = [];
      stopTimer();
      clearPollTimer();
    };
    const startProcess = (title, file = "") => {
      isProcessing.value = true;
      processTitle.value = title;
      processMessage.value = "";
      processStatus.value = "processing";
      processStep.value = 0;
      progress.value = 3;
      activeFile.value = file;
      processLog.value = [];
      startTimer();
    };
    const updateProcessFromStatus = (data) => {
      processStatus.value = data.status || "processing";
      processStep.value = data.step || 0;
      progress.value = data.progress ?? progress.value;
      if (data.message) {
        addLog(
          data.message
        );
      }
      if (data.error) {
        addLog(
          data.error
        );
      }
    };
    const fetchBackups = async () => {
      try {
        const response = await axios.get(
          route("admin.backup.list")
        );
        backups.value = response.data.backups || [];
      } catch {
        toast.error(
          t("failedToLoadBackups")
        );
      }
    };
    const createBackup = async () => {
      var _a, _b, _c, _d;
      if (isProcessing.value) {
        return;
      }
      startProcess(
        "Создание резервной копии БД"
      );
      addLog(
        "Запуск создания SQL-дампа"
      );
      try {
        progress.value = 30;
        const response = await axios.post(
          route(
            "admin.backup.create"
          )
        );
        progress.value = 100;
        processStatus.value = "done";
        addLog(
          response.data.message || "Бэкап успешно создан"
        );
        toast.success(
          t("backupCreated")
        );
        await fetchBackups();
        setTimeout(
          resetProcess,
          2500
        );
      } catch (error) {
        processStatus.value = "error";
        progress.value = 100;
        const message = ((_b = (_a = error == null ? void 0 : error.response) == null ? void 0 : _a.data) == null ? void 0 : _b.message) || t("backupFailed");
        const detail = (_d = (_c = error == null ? void 0 : error.response) == null ? void 0 : _c.data) == null ? void 0 : _d.error;
        addLog(message);
        if (detail) {
          addLog(detail);
        }
        toast.error(message);
        stopTimer();
      }
    };
    const handleRestore = async (filename) => {
      var _a, _b, _c, _d;
      if (!filename) {
        return;
      }
      if (!confirm(
        t(
          "backupConfirmRestoreBackup"
        )
      )) {
        return;
      }
      startProcess(
        "Восстановление базы данных",
        filename
      );
      addLog(
        `Запуск восстановления из файла: ${filename}`
      );
      try {
        const response = await axios.post(
          route(
            "admin.backup.restore.start"
          ),
          {
            file: filename
          }
        );
        updateProcessFromStatus(
          response.data
        );
        if (!response.data.job) {
          throw new Error(
            "Не получен идентификатор задачи восстановления"
          );
        }
        pollRestoreStatus(
          response.data.job
        );
      } catch (error) {
        processStatus.value = "error";
        progress.value = 100;
        const message = ((_b = (_a = error == null ? void 0 : error.response) == null ? void 0 : _a.data) == null ? void 0 : _b.message) || (error == null ? void 0 : error.message) || t("backupRestoreFailed");
        const detail = (_d = (_c = error == null ? void 0 : error.response) == null ? void 0 : _c.data) == null ? void 0 : _d.error;
        addLog(message);
        if (detail) {
          addLog(detail);
        }
        toast.error(message);
        stopTimer();
        clearPollTimer();
      }
    };
    const pollRestoreStatus = async (job) => {
      var _a, _b, _c, _d;
      try {
        const response = await axios.get(
          route(
            "admin.backup.restore.status",
            {
              job
            }
          )
        );
        const data = response.data;
        updateProcessFromStatus(
          data
        );
        if (data.status === "processing") {
          pollTimer = setTimeout(
            () => {
              pollRestoreStatus(
                job
              );
            },
            1500
          );
          return;
        }
        stopTimer();
        clearPollTimer();
        if (data.status === "done") {
          progress.value = 100;
          processStatus.value = "done";
          addLog(
            "Восстановление базы данных завершено"
          );
          toast.success(
            t("backupRestored")
          );
          await fetchBackups();
          setTimeout(
            resetProcess,
            4e3
          );
          return;
        }
        if (data.status === "error") {
          progress.value = 100;
          processStatus.value = "error";
          toast.error(
            data.message || t(
              "backupRestoreFailed"
            )
          );
          await fetchBackups();
          return;
        }
        processStatus.value = "error";
        progress.value = 100;
        const message = "Получен неизвестный статус задачи восстановления";
        addLog(message);
        toast.error(message);
      } catch (error) {
        processStatus.value = "error";
        progress.value = 100;
        const message = ((_b = (_a = error == null ? void 0 : error.response) == null ? void 0 : _a.data) == null ? void 0 : _b.message) || t("backupRestoreFailed");
        const detail = (_d = (_c = error == null ? void 0 : error.response) == null ? void 0 : _c.data) == null ? void 0 : _d.error;
        addLog(message);
        if (detail) {
          addLog(detail);
        }
        toast.error(message);
        stopTimer();
        clearPollTimer();
      }
    };
    const handleDelete = async (filename) => {
      var _a, _b, _c, _d;
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
        "Удаление бэкапа",
        filename
      );
      progress.value = 50;
      addLog(
        `Удаление файла: ${filename}`
      );
      try {
        await axios.delete(
          route(
            "admin.backup.delete"
          ),
          {
            data: {
              file: filename
            }
          }
        );
        progress.value = 100;
        processStatus.value = "done";
        addLog(
          "Бэкап успешно удалён"
        );
        toast.success(
          t("backupDeleted")
        );
        await fetchBackups();
        setTimeout(
          resetProcess,
          1500
        );
      } catch (error) {
        processStatus.value = "error";
        progress.value = 100;
        const message = ((_b = (_a = error == null ? void 0 : error.response) == null ? void 0 : _a.data) == null ? void 0 : _b.message) || t("backupDeleteFailed");
        const detail = (_d = (_c = error == null ? void 0 : error.response) == null ? void 0 : _c.data) == null ? void 0 : _d.error;
        addLog(message);
        if (detail) {
          addLog(detail);
        }
        toast.error(message);
        stopTimer();
      }
    };
    onMounted(
      fetchBackups
    );
    onBeforeUnmount(() => {
      stopTimer();
      clearPollTimer();
    });
    return (_ctx, _push, _parent, _attrs) => {
      _push(ssrRenderComponent(AdminLayout, mergeProps({
        title: unref(t)("databaseBackup")
      }, _attrs), {
        header: withCtx((_, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(ssrRenderComponent(TitlePage, null, {
              default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                if (_push3) {
                  _push3(`${ssrInterpolate(unref(t)("databaseBackup"))}`);
                } else {
                  return [
                    createTextVNode(toDisplayString(unref(t)("databaseBackup")), 1)
                  ];
                }
              }),
              _: 1
            }, _parent2, _scopeId));
          } else {
            return [
              createVNode(TitlePage, null, {
                default: withCtx(() => [
                  createTextVNode(toDisplayString(unref(t)("databaseBackup")), 1)
                ]),
                _: 1
              })
            ];
          }
        }),
        default: withCtx((_, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(`<div class="px-2 py-2 w-full max-w-12xl mx-auto"${_scopeId}><div class="p-4 bg-slate-50 dark:bg-slate-700 border border-blue-400 dark:border-blue-200 overflow-hidden shadow-md shadow-gray-500 dark:shadow-slate-400 bg-opacity-95 dark:bg-opacity-95"${_scopeId}><div class="sm:flex sm:justify-between sm:items-center mb-4"${_scopeId}><button type="button"${ssrIncludeBooleanAttr(isProcessing.value) ? " disabled" : ""} class="flex items-center btn px-2 py-0.5 bg-sky-600 text-white text-sm font-semibold rounded-sm shadow-md transition-colors duration-300 ease-in-out hover:bg-sky-700 focus:bg-sky-700 focus:outline-none disabled:opacity-50 disabled:cursor-not-allowed"${_scopeId}><svg class="w-4 h-4 fill-current opacity-50 shrink-0 mr-1" viewBox="0 0 16 16"${_scopeId}><path d="M15 7H9V1c0-.6-.4-1-1-1S7 .4 7 1v6H1c-.6 0-1 .4-1 1s.4 1 1 1h6v6c0 .6.4 1 1 1s1-.4 1-1V9h6c.6 0 1-.4 1-1s-.4-1-1-1z"${_scopeId}></path></svg> ${ssrInterpolate(unref(t)("createBackup"))}</button></div>`);
            if (isProcessing.value || processStatus.value) {
              _push2(`<div class="mb-4 rounded border border-blue-300 dark:border-blue-200 bg-white dark:bg-slate-800 shadow p-4"${_scopeId}><div class="flex items-center justify-between mb-2"${_scopeId}><div${_scopeId}><h3 class="text-sm font-semibold text-slate-700 dark:text-slate-100"${_scopeId}>${ssrInterpolate(processTitle.value)}</h3><div class="text-xs text-slate-500 dark:text-slate-300"${_scopeId}>${ssrInterpolate(activeFile.value)}</div></div><span class="${ssrRenderClass([{
                "bg-blue-100 text-blue-700": processStatus.value === "processing",
                "bg-green-100 text-green-700": processStatus.value === "done",
                "bg-red-100 text-red-700": processStatus.value === "error"
              }, "px-2 py-0.5 rounded text-xs font-semibold"])}"${_scopeId}>${ssrInterpolate(processStatus.value === "done" ? "Готово" : processStatus.value === "error" ? "Ошибка" : "В процессе")}</span></div><div class="grid grid-cols-1 md:grid-cols-3 gap-2 mb-3 text-xs text-slate-600 dark:text-slate-300"${_scopeId}><div${_scopeId}><span class="font-semibold"${_scopeId}> Этап: </span> ${ssrInterpolate(stepLabel.value)}</div><div${_scopeId}><span class="font-semibold"${_scopeId}> Время: </span> ${ssrInterpolate(elapsedTime.value)}</div><div${_scopeId}><span class="font-semibold"${_scopeId}> Прогресс: </span> ${ssrInterpolate(progress.value)}% </div></div><div class="${ssrRenderClass([{
                "text-blue-700 dark:text-blue-200": processStatus.value !== "error",
                "text-red-700 dark:text-red-300": processStatus.value === "error"
              }, "mb-2 text-sm font-medium"])}"${_scopeId}>${ssrInterpolate(processMessage.value)}</div><div class="w-full h-4 bg-gray-300 dark:bg-gray-600 rounded-full overflow-hidden"${_scopeId}><div class="${ssrRenderClass([{
                "bg-green-600": processStatus.value === "done",
                "bg-red-600": processStatus.value === "error"
              }, "h-full bg-blue-600 transition-all duration-500"])}" style="${ssrRenderStyle({
                width: `${progress.value}%`
              })}"${_scopeId}></div></div>`);
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
            _push2(`<div class="mt-6"${_scopeId}><h2 class="text-slate-700 dark:text-slate-200 text-center text-md font-semibold mb-2"${_scopeId}>${ssrInterpolate(unref(t)("availableBackups"))}</h2>`);
            if (backups.value.length === 0) {
              _push2(`<div class="text-gray-500 text-center"${_scopeId}>${ssrInterpolate(unref(t)("noData"))}</div>`);
            } else {
              _push2(`<ul class="divide-y border rounded"${_scopeId}><!--[-->`);
              ssrRenderList(backups.value, (backup) => {
                _push2(`<li class="flex items-center justify-between px-3 py-1 bg-gray-100 hover:bg-gray-50 dark:bg-gray-700 dark:hover:bg-slate-800"${_scopeId}><div${_scopeId}><div class="font-medium text-sm text-amber-700 dark:text-amber-200"${_scopeId}>${ssrInterpolate(backup.name)}</div><div class="text-xs text-gray-500 dark:text-gray-400"${_scopeId}>${ssrInterpolate((backup.size / 1024).toFixed(1))} KB </div></div><div class="flex items-center space-x-2"${_scopeId}><a${ssrRenderAttr(
                  "href",
                  _ctx.route(
                    "admin.backup.download",
                    {
                      filename: backup.name
                    }
                  )
                )}${ssrRenderAttr("title", unref(t)("download"))} class="w-8 h-8 flex items-center justify-center rounded-sm border border-slate-400 dark:border-slate-200 bg-indigo-100 hover:bg-indigo-200 dark:bg-indigo-700 dark:hover:bg-indigo-500 text-indigo-600 dark:text-slate-100 transition"${_scopeId}><svg class="w-6 h-6 fill-current" viewBox="0 0 24 24"${_scopeId}><path d="M5 20h14v-2H5m14-9h-4V3H9v6H5l7 7 7-7z"${_scopeId}></path></svg></a><button type="button"${ssrRenderAttr("title", unref(t)("recover"))}${ssrIncludeBooleanAttr(isProcessing.value) ? " disabled" : ""} class="w-8 h-8 flex items-center justify-center rounded-sm border border-slate-400 dark:border-slate-200 bg-teal-200 hover:bg-teal-300 dark:bg-teal-700 dark:hover:bg-teal-600 text-teal-600 dark:text-slate-100 transition disabled:opacity-50 disabled:cursor-not-allowed"${_scopeId}><svg class="w-4 h-4 fill-current" viewBox="0 0 24 24"${_scopeId}><path d="M12,10C8.2,10,4.3,9.3,2,7.6V12c0,2.7,5.2,4,10,4s10-1.3,10-4V7.6C19.7,9.3,15.8,10,12,10z"${_scopeId}></path><path d="M12,18c-3.8,0-7.7-0.7-10-2.4V20c0,2.7,5.2,4,10,4s10-1.3,10-4v-4.4C19.7,17.3,15.8,18,12,18z"${_scopeId}></path><path d="M12,0C7.2,0,2,1.3,2,4s5.2,4,10,4s10-1.3,10-4S16.8,0,12,0z"${_scopeId}></path></svg></button><button type="button"${ssrRenderAttr("title", unref(t)("remove"))}${ssrIncludeBooleanAttr(isProcessing.value) ? " disabled" : ""} class="w-8 h-8 flex items-center justify-center rounded-sm border border-slate-400 dark:border-slate-200 bg-red-200 hover:bg-red-300 dark:bg-red-700 dark:hover:bg-red-600 text-red-600 dark:text-slate-100 transition disabled:opacity-50 disabled:cursor-not-allowed"${_scopeId}><svg class="w-4 h-4 fill-current" viewBox="0 0 16 16"${_scopeId}><path d="M5 7h2v6H5V7zm4 0h2v6H9V7zm3-6v2h4v2h-1v10c0 .6-.4 1-1 1H2c-.6 0-1-.4-1-1V5H0V3h4V1c0-.6.4-1 1-1h6c.6 0 0 1 1 1zM6 2v1h4V2H6zm7 3H3v9h10V5z"${_scopeId}></path></svg></button></div></li>`);
              });
              _push2(`<!--]--></ul>`);
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
                      onClick: createBackup
                    }, [
                      (openBlock(), createBlock("svg", {
                        class: "w-4 h-4 fill-current opacity-50 shrink-0 mr-1",
                        viewBox: "0 0 16 16"
                      }, [
                        createVNode("path", { d: "M15 7H9V1c0-.6-.4-1-1-1S7 .4 7 1v6H1c-.6 0-1 .4-1 1s.4 1 1 1h6v6c0 .6.4 1 1 1s1-.4 1-1V9h6c.6 0 1-.4 1-1s-.4-1-1-1z" })
                      ])),
                      createTextVNode(" " + toDisplayString(unref(t)("createBackup")), 1)
                    ], 8, ["disabled"])
                  ]),
                  isProcessing.value || processStatus.value ? (openBlock(), createBlock("div", {
                    key: 0,
                    class: "mb-4 rounded border border-blue-300 dark:border-blue-200 bg-white dark:bg-slate-800 shadow p-4"
                  }, [
                    createVNode("div", { class: "flex items-center justify-between mb-2" }, [
                      createVNode("div", null, [
                        createVNode("h3", { class: "text-sm font-semibold text-slate-700 dark:text-slate-100" }, toDisplayString(processTitle.value), 1),
                        createVNode("div", { class: "text-xs text-slate-500 dark:text-slate-300" }, toDisplayString(activeFile.value), 1)
                      ]),
                      createVNode("span", {
                        class: ["px-2 py-0.5 rounded text-xs font-semibold", {
                          "bg-blue-100 text-blue-700": processStatus.value === "processing",
                          "bg-green-100 text-green-700": processStatus.value === "done",
                          "bg-red-100 text-red-700": processStatus.value === "error"
                        }]
                      }, toDisplayString(processStatus.value === "done" ? "Готово" : processStatus.value === "error" ? "Ошибка" : "В процессе"), 3)
                    ]),
                    createVNode("div", { class: "grid grid-cols-1 md:grid-cols-3 gap-2 mb-3 text-xs text-slate-600 dark:text-slate-300" }, [
                      createVNode("div", null, [
                        createVNode("span", { class: "font-semibold" }, " Этап: "),
                        createTextVNode(" " + toDisplayString(stepLabel.value), 1)
                      ]),
                      createVNode("div", null, [
                        createVNode("span", { class: "font-semibold" }, " Время: "),
                        createTextVNode(" " + toDisplayString(elapsedTime.value), 1)
                      ]),
                      createVNode("div", null, [
                        createVNode("span", { class: "font-semibold" }, " Прогресс: "),
                        createTextVNode(" " + toDisplayString(progress.value) + "% ", 1)
                      ])
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
                    processLog.value.length ? (openBlock(), createBlock("div", {
                      key: 0,
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
                  createVNode("div", { class: "mt-6" }, [
                    createVNode("h2", { class: "text-slate-700 dark:text-slate-200 text-center text-md font-semibold mb-2" }, toDisplayString(unref(t)("availableBackups")), 1),
                    backups.value.length === 0 ? (openBlock(), createBlock("div", {
                      key: 0,
                      class: "text-gray-500 text-center"
                    }, toDisplayString(unref(t)("noData")), 1)) : (openBlock(), createBlock("ul", {
                      key: 1,
                      class: "divide-y border rounded"
                    }, [
                      (openBlock(true), createBlock(Fragment, null, renderList(backups.value, (backup) => {
                        return openBlock(), createBlock("li", {
                          key: backup.name,
                          class: "flex items-center justify-between px-3 py-1 bg-gray-100 hover:bg-gray-50 dark:bg-gray-700 dark:hover:bg-slate-800"
                        }, [
                          createVNode("div", null, [
                            createVNode("div", { class: "font-medium text-sm text-amber-700 dark:text-amber-200" }, toDisplayString(backup.name), 1),
                            createVNode("div", { class: "text-xs text-gray-500 dark:text-gray-400" }, toDisplayString((backup.size / 1024).toFixed(1)) + " KB ", 1)
                          ]),
                          createVNode("div", { class: "flex items-center space-x-2" }, [
                            createVNode("a", {
                              href: _ctx.route(
                                "admin.backup.download",
                                {
                                  filename: backup.name
                                }
                              ),
                              title: unref(t)("download"),
                              class: "w-8 h-8 flex items-center justify-center rounded-sm border border-slate-400 dark:border-slate-200 bg-indigo-100 hover:bg-indigo-200 dark:bg-indigo-700 dark:hover:bg-indigo-500 text-indigo-600 dark:text-slate-100 transition"
                            }, [
                              (openBlock(), createBlock("svg", {
                                class: "w-6 h-6 fill-current",
                                viewBox: "0 0 24 24"
                              }, [
                                createVNode("path", { d: "M5 20h14v-2H5m14-9h-4V3H9v6H5l7 7 7-7z" })
                              ]))
                            ], 8, ["href", "title"]),
                            createVNode("button", {
                              type: "button",
                              title: unref(t)("recover"),
                              disabled: isProcessing.value,
                              class: "w-8 h-8 flex items-center justify-center rounded-sm border border-slate-400 dark:border-slate-200 bg-teal-200 hover:bg-teal-300 dark:bg-teal-700 dark:hover:bg-teal-600 text-teal-600 dark:text-slate-100 transition disabled:opacity-50 disabled:cursor-not-allowed",
                              onClick: ($event) => handleRestore(
                                backup.name
                              )
                            }, [
                              (openBlock(), createBlock("svg", {
                                class: "w-4 h-4 fill-current",
                                viewBox: "0 0 24 24"
                              }, [
                                createVNode("path", { d: "M12,10C8.2,10,4.3,9.3,2,7.6V12c0,2.7,5.2,4,10,4s10-1.3,10-4V7.6C19.7,9.3,15.8,10,12,10z" }),
                                createVNode("path", { d: "M12,18c-3.8,0-7.7-0.7-10-2.4V20c0,2.7,5.2,4,10,4s10-1.3,10-4v-4.4C19.7,17.3,15.8,18,12,18z" }),
                                createVNode("path", { d: "M12,0C7.2,0,2,1.3,2,4s5.2,4,10,4s10-1.3,10-4S16.8,0,12,0z" })
                              ]))
                            ], 8, ["title", "disabled", "onClick"]),
                            createVNode("button", {
                              type: "button",
                              title: unref(t)("remove"),
                              disabled: isProcessing.value,
                              class: "w-8 h-8 flex items-center justify-center rounded-sm border border-slate-400 dark:border-slate-200 bg-red-200 hover:bg-red-300 dark:bg-red-700 dark:hover:bg-red-600 text-red-600 dark:text-slate-100 transition disabled:opacity-50 disabled:cursor-not-allowed",
                              onClick: ($event) => handleDelete(
                                backup.name
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
                    ]))
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
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Pages/Admin/System/DatabaseBackup.vue");
  return _sfc_setup ? _sfc_setup(props, ctx) : void 0;
};
export {
  _sfc_main as default
};
