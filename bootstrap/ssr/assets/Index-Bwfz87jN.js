import { mergeProps, unref, useSSRContext, ref, watch, computed, withCtx, createTextVNode, toDisplayString, createVNode, createBlock, createCommentVNode, openBlock } from "vue";
import { ssrRenderAttrs, ssrInterpolate, ssrRenderAttr, ssrIncludeBooleanAttr, ssrRenderList, ssrRenderClass, ssrRenderComponent } from "vue/server-renderer";
import { useI18n } from "vue-i18n";
import { useToast } from "vue-toastification";
import { router } from "@inertiajs/vue3";
import { A as AdminLayout } from "./AdminLayout-03yd3nxC.js";
import { T as TitlePage } from "./TitlePage-CBZZ8YZB.js";
import { _ as _sfc_main$8 } from "./SearchInput-xZSYbbms.js";
import { _ as _sfc_main$9, a as _sfc_main$f } from "./ServerSearchInput-CxQUCmzV.js";
import { _ as _sfc_main$c } from "./CountTable-p8tyXGUL.js";
import { _ as _sfc_main$a, a as _sfc_main$d, b as _sfc_main$e } from "./ItemsPerPageSelect-DOO-E4Z0.js";
import { _ as _sfc_main$b } from "./ServerItemsPerPageSelect-B_wSkKlU.js";
import { _ as _sfc_main$g } from "./DangerModal-DjIe5VDg.js";
import { _ as _sfc_main$7 } from "./ProcessingModeSwitcher-BJvzFf6_.js";
import { _ as _sfc_main$5 } from "./IconEdit-Bw90OQvk.js";
import { _ as _sfc_main$6 } from "./DeleteIconButton-DLv2Mr1x.js";
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
const _sfc_main$4 = {
  __name: "SortSelect",
  __ssrInlineRender: true,
  props: {
    sortParam: String
  },
  emits: ["update:sortParam"],
  setup(__props, { emit: __emit }) {
    const { t } = useI18n();
    return (_ctx, _push, _parent, _attrs) => {
      _push(`<div${ssrRenderAttrs(mergeProps({ class: "flex justify-center items-center h-fit my-2" }, _attrs))}><label for="sortParam" class="hidden lg:block sm:mr-2 tracking-wider text-sm font-semibold text-slate-600 dark:text-slate-100">${ssrInterpolate(unref(t)("sort"))}</label><select id="sortParam"${ssrRenderAttr("value", __props.sortParam)} class="w-56 px-3 py-0.5 form-select bg-white dark:bg-gray-200 text-gray-600 dark:text-gray-900 border border-slate-400 dark:border-slate-600 rounded-sm shadow-sm"><option value="idDesc">${ssrInterpolate(unref(t)("idDesc"))}</option><option value="idAsc">${ssrInterpolate(unref(t)("idAsc"))}</option><option disabled>──────────</option><option value="attemptAsc">${ssrInterpolate(unref(t)("attemptNumber"))} ↑ </option><option value="attemptDesc">${ssrInterpolate(unref(t)("attemptNumber"))} ↓ </option><option disabled>──────────</option><option value="scoreAsc">${ssrInterpolate(unref(t)("score"))} ↑ </option><option value="scoreDesc">${ssrInterpolate(unref(t)("score"))} ↓ </option><option value="maxScoreAsc">${ssrInterpolate(unref(t)("maxScore"))} ↑ </option><option value="maxScoreDesc">${ssrInterpolate(unref(t)("maxScore"))} ↓ </option><option value="percentAsc">${ssrInterpolate(unref(t)("percent"))} ↑ </option><option value="percentDesc">${ssrInterpolate(unref(t)("percent"))} ↓ </option><option disabled>──────────</option><option value="durationAsc">${ssrInterpolate(unref(t)("duration"))} ↑ </option><option value="durationDesc">${ssrInterpolate(unref(t)("duration"))} ↓ </option><option value="startedAtAsc">${ssrInterpolate(unref(t)("shortStarted"))} ↑ </option><option value="startedAtDesc">${ssrInterpolate(unref(t)("shortStarted"))} ↓ </option><option value="finishedAtAsc">${ssrInterpolate(unref(t)("shortExpires"))} ↑ </option><option value="finishedAtDesc">${ssrInterpolate(unref(t)("shortExpires"))} ↓ </option><option disabled>──────────</option><option value="statusAsc">${ssrInterpolate(unref(t)("status"))} ↑ </option><option value="statusDesc">${ssrInterpolate(unref(t)("status"))} ↓ </option><option value="inProgress">${ssrInterpolate(unref(t)("setStatusInProgress"))}</option><option value="completed">${ssrInterpolate(unref(t)("setStatusCompleted"))}</option><option value="graded">${ssrInterpolate(unref(t)("setStatusGraded"))}</option><option disabled>──────────</option><option value="itemsAsc">${ssrInterpolate(unref(t)("answers"))} ↑ </option><option value="itemsDesc">${ssrInterpolate(unref(t)("answers"))} ↓ </option><option value="userNameAsc">${ssrInterpolate(unref(t)("user"))} ↑ </option><option value="userNameDesc">${ssrInterpolate(unref(t)("user"))} ↓ </option><option value="quizTitleAsc">${ssrInterpolate(unref(t)("quiz"))} ↑ </option><option value="quizTitleDesc">${ssrInterpolate(unref(t)("quiz"))} ↓ </option></select></div>`);
    };
  }
};
const _sfc_setup$4 = _sfc_main$4.setup;
_sfc_main$4.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Components/Admin/School/SchoolQuizAttempt/Sort/SortSelect.vue");
  return _sfc_setup$4 ? _sfc_setup$4(props, ctx) : void 0;
};
const _sfc_main$3 = {
  __name: "QuizAttemptTable",
  __ssrInlineRender: true,
  props: {
    attempts: {
      type: Array,
      default: () => []
    },
    selectedAttempts: {
      type: Array,
      default: () => []
    }
  },
  emits: [
    "delete",
    "toggle-select",
    "toggle-all"
  ],
  setup(__props, { emit: __emit }) {
    const { t } = useI18n();
    const props = __props;
    const emit = __emit;
    const localAttempts = ref([]);
    watch(
      () => props.attempts,
      (newValue) => {
        localAttempts.value = JSON.parse(JSON.stringify(newValue || []));
      },
      { immediate: true, deep: true }
    );
    const getNestedTitle = (item) => {
      var _a, _b;
      return ((_a = item == null ? void 0 : item.translation) == null ? void 0 : _a.title) || ((_b = item == null ? void 0 : item.translation) == null ? void 0 : _b.name) || "";
    };
    const getQuizTitle = (attempt) => {
      return getNestedTitle(attempt == null ? void 0 : attempt.quiz) || `Quiz ID: ${(attempt == null ? void 0 : attempt.school_quiz_id) || "—"}`;
    };
    const getCourseTitle = (attempt) => getNestedTitle(attempt == null ? void 0 : attempt.course);
    const getModuleTitle = (attempt) => getNestedTitle(attempt == null ? void 0 : attempt.module);
    const getLessonTitle = (attempt) => getNestedTitle(attempt == null ? void 0 : attempt.lesson);
    const formatDateTime = (value) => {
      if (!value) return "—";
      const date = new Date(value);
      if (Number.isNaN(date.getTime())) return "—";
      return date.toLocaleString("ru-RU");
    };
    const formatDuration = (seconds) => {
      const total = Number(seconds || 0);
      if (!total) return "—";
      const hours = Math.floor(total / 3600);
      const minutes = Math.floor(total % 3600 / 60);
      const secs = total % 60;
      if (hours) return `${hours}ч ${minutes}м ${secs}с`;
      if (minutes) return `${minutes}м ${secs}с`;
      return `${secs}с`;
    };
    const formatPercent = (value) => {
      if (value === null || typeof value === "undefined") return "—";
      const number = Number(value);
      return Number.isFinite(number) ? `${number}%` : "—";
    };
    const statusLabel = (status) => {
      if (status === "in_progress") return t("setStatusInProgress");
      if (status === "completed") return t("setStatusCompleted");
      if (status === "graded") return t("setStatusGraded");
      return status || "—";
    };
    const statusClass = (status) => {
      if (status === "completed") {
        return "border-emerald-500 bg-emerald-50 dark:bg-emerald-900/40 text-emerald-700 dark:text-emerald-100";
      }
      if (status === "graded") {
        return "border-indigo-500 bg-indigo-50 dark:bg-indigo-900/40 text-indigo-700 dark:text-indigo-100";
      }
      return "border-amber-500 bg-amber-50 dark:bg-amber-900/40 text-amber-700 dark:text-amber-100";
    };
    return (_ctx, _push, _parent, _attrs) => {
      _push(`<div${ssrRenderAttrs(mergeProps({ class: "bg-white dark:bg-slate-700 shadow-lg rounded-sm border border-slate-200 dark:border-slate-600 relative" }, _attrs))}><div class="overflow-x-auto">`);
      if (localAttempts.value.length) {
        _push(`<table class="table-auto w-full text-slate-700 dark:text-slate-100"><thead class="text-sm uppercase bg-slate-200 dark:bg-cyan-900 border border-solid border-gray-300 dark:border-gray-700"><tr><th class="px-2 py-3 w-px"><div class="font-medium text-sm text-left">${ssrInterpolate(unref(t)("id"))}</div></th><th class="px-2 py-3 whitespace-nowrap"><div class="font-medium text-sm text-left">${ssrInterpolate(unref(t)("users"))}</div></th><th class="px-2 py-3 whitespace-nowrap"><div class="font-medium text-sm text-left">${ssrInterpolate(unref(t)("quiz"))}</div></th><th class="px-2 py-3 whitespace-nowrap"><div class="font-medium text-sm text-center">${ssrInterpolate(unref(t)("date"))}</div></th><th class="px-2 py-3 whitespace-nowrap"><div class="font-medium text-sm text-center">${ssrInterpolate(unref(t)("status"))}</div></th><th class="px-2 py-3 whitespace-nowrap"><div class="font-medium text-sm text-center">${ssrInterpolate(unref(t)("attemptNumber"))}</div></th><th class="px-2 py-3 whitespace-nowrap"><div class="font-medium text-sm text-center">${ssrInterpolate(unref(t)("points"))}</div></th><th class="px-2 py-3 whitespace-nowrap"><div class="font-medium text-sm text-end">${ssrInterpolate(unref(t)("actions"))}</div></th><th class="px-2 py-3 whitespace-nowrap text-center"><input type="checkbox"${ssrIncludeBooleanAttr(localAttempts.value.length && localAttempts.value.every((attempt) => __props.selectedAttempts.includes(attempt.id))) ? " checked" : ""}></th></tr></thead><tbody><!--[-->`);
        ssrRenderList(localAttempts.value, (attempt) => {
          var _a, _b, _c;
          _push(`<tr class="text-sm font-semibold border-b-2 hover:bg-slate-100 dark:hover:bg-cyan-800"><td class="px-2 py-3 w-px text-center"><div class="text-xs text-slate-800 dark:text-blue-200">${ssrInterpolate(attempt.id)}</div></td><td class="px-2 py-3 whitespace-nowrap"><div class="flex flex-col"><span class="text-sm text-orange-600 dark:text-orange-200">${ssrInterpolate(((_a = attempt.user) == null ? void 0 : _a.name) || `User ID: ${attempt.user_id || "—"}`)}</span>`);
          if ((_b = attempt.user) == null ? void 0 : _b.email) {
            _push(`<span class="text-xs text-slate-500 dark:text-slate-200">${ssrInterpolate(attempt.user.email)}</span>`);
          } else {
            _push(`<!---->`);
          }
          _push(`</div></td><td class="px-2 py-3"><div class="flex flex-col"${ssrRenderAttr("title", ((_c = attempt.quiz) == null ? void 0 : _c.slug) || "")}><span class="text-xs text-blue-700 dark:text-blue-300">${ssrInterpolate(getQuizTitle(attempt))}</span><div class="mt-1 text-[10px] text-teal-700 dark:text-teal-300">`);
          if (getLessonTitle(attempt)) {
            _push(`<span>${ssrInterpolate(getLessonTitle(attempt))}</span>`);
          } else {
            _push(`<!---->`);
          }
          if (getModuleTitle(attempt)) {
            _push(`<span> · ${ssrInterpolate(getModuleTitle(attempt))}</span>`);
          } else {
            _push(`<!---->`);
          }
          if (getCourseTitle(attempt)) {
            _push(`<span> · ${ssrInterpolate(getCourseTitle(attempt))}</span>`);
          } else {
            _push(`<!---->`);
          }
          _push(`</div></div></td><td class="px-2 py-3 whitespace-nowrap"><div class="flex flex-col text-[11px] gap-2"><span class="text-sky-700 dark:text-sky-200"><span class="text-slate-500 dark:text-slate-300">${ssrInterpolate(unref(t)("shortStarted"))}: </span><br> ${ssrInterpolate(formatDateTime(attempt.started_at))}</span><span class="text-sky-700 dark:text-sky-200"><span class="text-slate-500 dark:text-slate-300">${ssrInterpolate(unref(t)("shortExpires"))}: </span><br> ${ssrInterpolate(formatDateTime(attempt.finished_at))}</span></div></td><td class="px-2 py-3 whitespace-nowrap text-center space-y-1"><span class="${ssrRenderClass([
            "inline-flex items-center px-2 py-0.5 rounded-sm border text-[11px] font-semibold",
            statusClass(attempt.status)
          ])}">${ssrInterpolate(statusLabel(attempt.status))}</span><div class="text-xs text-slate-700 dark:text-slate-200">${ssrInterpolate(formatPercent(attempt.percent))}</div></td><td class="px-2 py-3 text-center"><div class="text-xs text-rose-700 dark:text-rose-300">${ssrInterpolate(attempt.attempt_number ?? "—")}</div><div class="text-xs"><span class="text-slate-500 dark:text-slate-300">${ssrInterpolate(unref(t)("duration"))}: </span><span class="text-slate-700 dark:text-slate-200">${ssrInterpolate(formatDuration(attempt.duration_seconds))}</span></div></td><td class="px-2 py-3 whitespace-nowrap text-center"><div class="text-xs text-amber-800 dark:text-amber-200">${ssrInterpolate(attempt.score ?? "—")} / ${ssrInterpolate(attempt.max_score ?? "—")}</div></td><td class="px-2 py-3 whitespace-nowrap"><div class="flex justify-end space-x-2">`);
          _push(ssrRenderComponent(_sfc_main$5, {
            href: _ctx.route("admin.schoolQuizAttempts.edit", {
              schoolQuizAttempt: attempt.id
            })
          }, null, _parent));
          _push(ssrRenderComponent(_sfc_main$6, {
            title: unref(t)("delete"),
            onDelete: ($event) => emit("delete", attempt)
          }, null, _parent));
          _push(`</div></td><td class="px-2 py-3 whitespace-nowrap text-center"><input type="checkbox"${ssrIncludeBooleanAttr(__props.selectedAttempts.includes(attempt.id)) ? " checked" : ""}></td></tr>`);
        });
        _push(`<!--]--></tbody></table>`);
      } else {
        _push(`<div class="p-5 text-center text-slate-700 dark:text-slate-100">${ssrInterpolate(unref(t)("noData"))}</div>`);
      }
      _push(`</div></div>`);
    };
  }
};
const _sfc_setup$3 = _sfc_main$3.setup;
_sfc_main$3.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Components/Admin/School/SchoolQuizAttempt/Table/QuizAttemptTable.vue");
  return _sfc_setup$3 ? _sfc_setup$3(props, ctx) : void 0;
};
const _sfc_main$2 = {
  __name: "QuizAttemptCardGrid",
  __ssrInlineRender: true,
  props: {
    attempts: {
      type: Array,
      default: () => []
    },
    selectedAttempts: {
      type: Array,
      default: () => []
    }
  },
  emits: [
    "delete",
    "toggle-select",
    "toggle-all"
  ],
  setup(__props, { emit: __emit }) {
    const { t } = useI18n();
    const props = __props;
    const emit = __emit;
    const localAttempts = ref([]);
    watch(
      () => props.attempts,
      (newValue) => {
        localAttempts.value = JSON.parse(JSON.stringify(newValue || []));
      },
      { immediate: true, deep: true }
    );
    const getNestedTitle = (item) => {
      var _a, _b;
      return ((_a = item == null ? void 0 : item.translation) == null ? void 0 : _a.title) || ((_b = item == null ? void 0 : item.translation) == null ? void 0 : _b.name) || "";
    };
    const getQuizTitle = (attempt) => {
      return getNestedTitle(attempt == null ? void 0 : attempt.quiz) || `Quiz ID: ${(attempt == null ? void 0 : attempt.school_quiz_id) || "—"}`;
    };
    const getCourseTitle = (attempt) => getNestedTitle(attempt == null ? void 0 : attempt.course);
    const getModuleTitle = (attempt) => getNestedTitle(attempt == null ? void 0 : attempt.module);
    const getLessonTitle = (attempt) => getNestedTitle(attempt == null ? void 0 : attempt.lesson);
    const formatDateTime = (value) => {
      if (!value) return "—";
      const date = new Date(value);
      if (Number.isNaN(date.getTime())) return "—";
      return date.toLocaleString("ru-RU");
    };
    const formatDuration = (seconds) => {
      const total = Number(seconds || 0);
      if (!total) return "—";
      const hours = Math.floor(total / 3600);
      const minutes = Math.floor(total % 3600 / 60);
      const secs = total % 60;
      if (hours) return `${hours}ч ${minutes}м ${secs}с`;
      if (minutes) return `${minutes}м ${secs}с`;
      return `${secs}с`;
    };
    const formatPercent = (value) => {
      if (value === null || typeof value === "undefined") return "—";
      const number = Number(value);
      return Number.isFinite(number) ? `${number}%` : "—";
    };
    const statusLabel = (status) => {
      if (status === "in_progress") return t("setStatusInProgress");
      if (status === "completed") return t("setStatusCompleted");
      if (status === "graded") return t("setStatusGraded");
      return status || "—";
    };
    const statusClass = (status) => {
      if (status === "completed") {
        return "border-emerald-500 bg-emerald-50 dark:bg-emerald-900/40 text-emerald-700 dark:text-emerald-100";
      }
      if (status === "graded") {
        return "border-indigo-500 bg-indigo-50 dark:bg-indigo-900/40 text-indigo-700 dark:text-indigo-100";
      }
      return "border-amber-500 bg-amber-50 dark:bg-amber-900/40 text-amber-700 dark:text-amber-100";
    };
    return (_ctx, _push, _parent, _attrs) => {
      _push(`<div${ssrRenderAttrs(mergeProps({ class: "bg-white dark:bg-slate-700 shadow-lg rounded-sm border border-slate-400 dark:border-slate-500 relative" }, _attrs))}><div class="flex items-center justify-between px-3 py-2 border-b border-slate-400 dark:border-slate-500"><div class="text-xs text-slate-600 dark:text-slate-200">${ssrInterpolate(unref(t)("selected"))}: ${ssrInterpolate(__props.selectedAttempts.length)}</div>`);
      if (localAttempts.value.length) {
        _push(`<label class="flex items-center text-xs text-slate-600 dark:text-slate-200 cursor-pointer"><span>${ssrInterpolate(unref(t)("selectAll"))}</span><input type="checkbox" class="rounded-sm border-slate-400 mx-2"${ssrIncludeBooleanAttr(localAttempts.value.length && localAttempts.value.every((attempt) => __props.selectedAttempts.includes(attempt.id))) ? " checked" : ""}></label>`);
      } else {
        _push(`<!---->`);
      }
      _push(`</div>`);
      if (localAttempts.value.length) {
        _push(`<div class="p-3"><div class="grid gap-3 grid-cols-1 sm:grid-cols-2 lg:grid-cols-4"><!--[-->`);
        ssrRenderList(localAttempts.value, (attempt) => {
          var _a, _b, _c;
          _push(`<article class="relative flex flex-col h-full rounded-md border border-slate-400 dark:border-slate-500 bg-slate-50/70 dark:bg-slate-800/80 shadow-sm hover:shadow-md transition-shadow duration-150"><header class="flex items-center justify-between px-2 py-1 border-b border-dashed border-slate-400 dark:border-slate-500"><div class="text-[10px] font-semibold px-1.5 py-0.5 rounded-sm border border-gray-400 bg-slate-200 dark:bg-slate-700 text-slate-800 dark:text-blue-100"> ID: ${ssrInterpolate(attempt.id)}</div><div class="flex items-center gap-2"><span class="${ssrRenderClass([
            "inline-flex items-center px-2 py-0.5 rounded-sm border text-[11px] font-semibold",
            statusClass(attempt.status)
          ])}">${ssrInterpolate(statusLabel(attempt.status))}</span><input type="checkbox"${ssrIncludeBooleanAttr(__props.selectedAttempts.includes(attempt.id)) ? " checked" : ""}></div></header><div class="flex flex-col flex-1 px-3 py-2 space-y-2"><div class="text-center space-y-0.5"><div class="text-[12px] font-medium text-orange-600 dark:text-orange-200">${ssrInterpolate(((_a = attempt.user) == null ? void 0 : _a.name) || `User ID: ${attempt.user_id || "—"}`)}</div>`);
          if ((_b = attempt.user) == null ? void 0 : _b.email) {
            _push(`<div class="text-[11px] font-semibold text-slate-500 dark:text-slate-200">${ssrInterpolate(attempt.user.email)}</div>`);
          } else {
            _push(`<!---->`);
          }
          _push(`</div><div class="text-center"${ssrRenderAttr("title", ((_c = attempt.quiz) == null ? void 0 : _c.slug) || "")}><div class="text-[12px] font-semibold text-blue-700 dark:text-blue-300">${ssrInterpolate(getQuizTitle(attempt))}</div><div class="mt-1 text-[10px] font-semibold text-teal-700 dark:text-teal-300">`);
          if (getCourseTitle(attempt)) {
            _push(`<span>${ssrInterpolate(getCourseTitle(attempt))}</span>`);
          } else {
            _push(`<!---->`);
          }
          if (getModuleTitle(attempt)) {
            _push(`<span> · ${ssrInterpolate(getModuleTitle(attempt))}</span>`);
          } else {
            _push(`<!---->`);
          }
          if (getLessonTitle(attempt)) {
            _push(`<span> · ${ssrInterpolate(getLessonTitle(attempt))}</span>`);
          } else {
            _push(`<!---->`);
          }
          _push(`</div></div><div class="grid grid-cols-2 gap-2 text-[11px] font-semibold text-center"><div class="rounded-sm border border-dashed border-slate-300 dark:border-slate-600 p-1"><div class="text-slate-500 dark:text-slate-300">${ssrInterpolate(unref(t)("attemptNumber"))}</div><div class="text-rose-700 dark:text-rose-300">${ssrInterpolate(attempt.attempt_number ?? "—")}</div></div><div class="rounded-sm border border-dashed border-slate-300 dark:border-slate-600 p-1"><div class="text-slate-500 dark:text-slate-300">${ssrInterpolate(unref(t)("percent"))}</div><div class="text-green-700 dark:text-green-300">${ssrInterpolate(formatPercent(attempt.percent))}</div></div></div><div class="font-semibold text-center text-[11px]"><span class="text-slate-700 dark:text-slate-200">${ssrInterpolate(unref(t)("score"))}: </span><span class="text-amber-800 dark:text-amber-200">${ssrInterpolate(attempt.score ?? "—")} / ${ssrInterpolate(attempt.max_score ?? "—")}</span></div><div class="font-semibold text-left text-[10px]"><span class="text-slate-500 dark:text-slate-300">${ssrInterpolate(unref(t)("shortStarted"))}: </span><span class="text-sky-700 dark:text-sky-200">${ssrInterpolate(formatDateTime(attempt.started_at))}</span></div><div class="font-semibold text-left text-[10px]"><span class="text-slate-500 dark:text-slate-300">${ssrInterpolate(unref(t)("shortExpires"))}: </span><span class="text-sky-700 dark:text-sky-200">${ssrInterpolate(formatDateTime(attempt.finished_at))}</span></div><div class="font-semibold text-center text-[10px]"><span class="text-slate-500 dark:text-slate-300">${ssrInterpolate(unref(t)("duration"))}: </span><span class="text-slate-900 dark:text-slate-100">${ssrInterpolate(formatDuration(attempt.duration_seconds))}</span></div><div class="grid grid-cols-2 gap-1 text-[10px] text-center"><span class="border border-dashed border-slate-300 dark:border-slate-600 rounded-sm px-1 py-0.5"> User ID: ${ssrInterpolate(attempt.user_id ?? "—")}</span><span class="border border-dashed border-slate-300 dark:border-slate-600 rounded-sm px-1 py-0.5"> Quiz ID: ${ssrInterpolate(attempt.school_quiz_id ?? "—")}</span></div><div class="text-center text-[10px]"><span class="inline-flex border border-dashed border-slate-300 dark:border-slate-600 rounded-sm px-2 py-0.5">${ssrInterpolate(unref(t)("items"))}: ${ssrInterpolate(attempt.items_count ?? 0)}</span></div></div><footer class="flex items-center justify-center px-3 py-2 border-t border-dashed border-slate-400 dark:border-slate-500"><div class="flex items-center space-x-1">`);
          _push(ssrRenderComponent(_sfc_main$5, {
            href: _ctx.route("admin.schoolQuizAttempts.edit", {
              schoolQuizAttempt: attempt.id
            })
          }, null, _parent));
          _push(ssrRenderComponent(_sfc_main$6, {
            title: unref(t)("delete"),
            onDelete: ($event) => emit("delete", attempt)
          }, null, _parent));
          _push(`</div></footer></article>`);
        });
        _push(`<!--]--></div></div>`);
      } else {
        _push(`<div class="p-5 text-center text-slate-700 dark:text-slate-100">${ssrInterpolate(unref(t)("noData"))}</div>`);
      }
      _push(`</div>`);
    };
  }
};
const _sfc_setup$2 = _sfc_main$2.setup;
_sfc_main$2.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Components/Admin/School/SchoolQuizAttempt/View/QuizAttemptCardGrid.vue");
  return _sfc_setup$2 ? _sfc_setup$2(props, ctx) : void 0;
};
const _sfc_main$1 = {
  __name: "BulkActionSelect",
  __ssrInlineRender: true,
  emits: ["change"],
  setup(__props, { emit: __emit }) {
    const { t } = useI18n();
    return (_ctx, _push, _parent, _attrs) => {
      _push(`<div${ssrRenderAttrs(mergeProps({ class: "flex flex-col sm:flex-row items-center" }, _attrs))}><label class="block mb-2 sm:mb-0 sm:mr-2 font-semibold text-sm text-slate-700 dark:text-slate-300">${ssrInterpolate(unref(t)("bulkActions"))}</label><select class="w-auto px-3 py-0.5 form-select rounded-sm shadow-sm bg-white dark:bg-gray-200 text-gray-600 dark:text-gray-900 border border-slate-400 dark:border-slate-600"><option value=""> — ${ssrInterpolate(unref(t)("selectAction"))} — </option><option value="selectAll">${ssrInterpolate(unref(t)("selectAll"))}</option><option value="deselectAll">${ssrInterpolate(unref(t)("deselectAll"))}</option><option disabled>──────────────────</option><option value="status:in_progress">${ssrInterpolate(unref(t)("setStatusInProgress") || "Статус: В процессе")}</option><option value="status:completed">${ssrInterpolate(unref(t)("setStatusCompleted") || "Статус: Завершён")}</option><option value="status:graded">${ssrInterpolate(unref(t)("setStatusGraded") || "Статус: Проверен")}</option><option disabled>──────────────────</option><option value="delete">${ssrInterpolate(unref(t)("deleteSelected"))}</option><option disabled>──────────────────</option></select></div>`);
    };
  }
};
const _sfc_setup$1 = _sfc_main$1.setup;
_sfc_main$1.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Components/Admin/School/SchoolQuizAttempt/Select/BulkActionSelect.vue");
  return _sfc_setup$1 ? _sfc_setup$1(props, ctx) : void 0;
};
const _sfc_main = {
  __name: "Index",
  __ssrInlineRender: true,
  props: {
    currentLocale: {
      type: String,
      default: ""
    },
    availableLocales: {
      type: Array,
      default: () => []
    },
    useServerProcessing: {
      type: Boolean,
      default: false
    },
    adminSchoolQuizAttemptsProcessingMode: {
      type: String,
      default: "frontend"
    },
    attempts: {
      type: [Array, Object],
      default: () => []
    },
    attemptsCount: {
      type: Number,
      default: 0
    },
    filters: {
      type: Object,
      default: () => ({})
    },
    adminSchoolQuizAttemptsPerPage: {
      type: Number,
      default: 10
    },
    adminSchoolQuizAttemptsDefaultSort: {
      type: String,
      default: "idDesc"
    },
    sortParam: {
      type: String,
      default: ""
    },
    search: {
      type: String,
      default: ""
    },
    users: {
      type: Array,
      default: () => []
    },
    quizzes: {
      type: Array,
      default: () => []
    },
    enrollments: {
      type: Array,
      default: () => []
    },
    courses: {
      type: Array,
      default: () => []
    },
    modules: {
      type: Array,
      default: () => []
    },
    lessons: {
      type: Array,
      default: () => []
    }
  },
  setup(__props) {
    const { t } = useI18n();
    const toast = useToast();
    const props = __props;
    const viewMode = ref(
      localStorage.getItem(
        "admin_view_mode_quiz_attempts"
      ) || "table"
    );
    watch(
      viewMode,
      (value) => {
        localStorage.setItem(
          "admin_view_mode_quiz_attempts",
          value
        );
      }
    );
    const attemptsList = computed(() => {
      var _a;
      if (Array.isArray(props.attempts)) {
        return props.attempts;
      }
      if (Array.isArray(
        (_a = props.attempts) == null ? void 0 : _a.data
      )) {
        return props.attempts.data;
      }
      return [];
    });
    const localAttempts = ref([]);
    watch(
      attemptsList,
      (newValue) => {
        localAttempts.value = JSON.parse(
          JSON.stringify(
            newValue || []
          )
        );
      },
      {
        immediate: true,
        deep: true
      }
    );
    const itemsPerPage = ref(
      props.adminSchoolQuizAttemptsPerPage || 10
    );
    watch(
      itemsPerPage,
      (newValue) => {
        router.put(
          route(
            "admin.settings.updateAdminCountSchoolQuizAttempts"
          ),
          {
            value: newValue
          },
          {
            preserveScroll: true,
            preserveState: true,
            onSuccess: () => {
              toast.info(
                `Показ ${newValue} элементов на странице.`
              );
            },
            onError: (errors) => {
              toast.error(
                (errors == null ? void 0 : errors.value) || "Ошибка обновления кол-ва элементов."
              );
            }
          }
        );
      }
    );
    const sortParam = ref(
      props.sortParam || props.adminSchoolQuizAttemptsDefaultSort || "idDesc"
    );
    watch(
      sortParam,
      (newValue) => {
        currentPage.value = 1;
        router.put(
          route(
            "admin.settings.updateAdminSortSchoolQuizAttempts"
          ),
          {
            value: newValue
          },
          {
            preserveScroll: true,
            preserveState: true,
            onSuccess: () => {
              if (props.useServerProcessing) {
                router.get(
                  window.location.pathname,
                  {
                    ...Object.fromEntries(
                      new URLSearchParams(
                        window.location.search
                      )
                    ),
                    sort: newValue || void 0,
                    page: void 0
                  },
                  {
                    preserveScroll: true,
                    preserveState: false,
                    replace: true
                  }
                );
              }
              toast.info(
                "Сортировка успешно изменена"
              );
            },
            onError: (errors) => {
              toast.error(
                (errors == null ? void 0 : errors.value) || "Ошибка обновления сортировки."
              );
            }
          }
        );
      }
    );
    const currentPage = ref(1);
    const searchQuery = ref(
      props.search || ""
    );
    const normalize = (value) => {
      return (value ?? "").toString().trim().toLowerCase();
    };
    const safeNumber = (value) => {
      const number = Number(value);
      return Number.isFinite(number) ? number : 0;
    };
    const safeDate = (value) => {
      const time = new Date(
        value || 0
      ).getTime();
      return Number.isFinite(time) ? time : 0;
    };
    const getNestedTitle = (item) => {
      var _a, _b;
      return ((_a = item == null ? void 0 : item.translation) == null ? void 0 : _a.title) || ((_b = item == null ? void 0 : item.translation) == null ? void 0 : _b.name) || "";
    };
    const getUserName = (attempt) => {
      var _a;
      return ((_a = attempt == null ? void 0 : attempt.user) == null ? void 0 : _a.name) || "";
    };
    const getQuizTitle = (attempt) => {
      return getNestedTitle(
        attempt == null ? void 0 : attempt.quiz
      );
    };
    const getCourseTitle = (attempt) => {
      return getNestedTitle(
        attempt == null ? void 0 : attempt.course
      );
    };
    const getModuleTitle = (attempt) => {
      return getNestedTitle(
        attempt == null ? void 0 : attempt.module
      );
    };
    const getLessonTitle = (attempt) => {
      return getNestedTitle(
        attempt == null ? void 0 : attempt.lesson
      );
    };
    const byNumberAsc = (field) => (a, b) => safeNumber(a == null ? void 0 : a[field]) - safeNumber(b == null ? void 0 : b[field]) || safeNumber(a == null ? void 0 : a.id) - safeNumber(b == null ? void 0 : b.id);
    const byNumberDesc = (field) => (a, b) => safeNumber(b == null ? void 0 : b[field]) - safeNumber(a == null ? void 0 : a[field]) || safeNumber(b == null ? void 0 : b.id) - safeNumber(a == null ? void 0 : a.id);
    const byStringAsc = (field) => (a, b) => normalize(
      a == null ? void 0 : a[field]
    ).localeCompare(
      normalize(
        b == null ? void 0 : b[field]
      )
    ) || safeNumber(a == null ? void 0 : a.id) - safeNumber(b == null ? void 0 : b.id);
    const byStringDesc = (field) => (a, b) => normalize(
      b == null ? void 0 : b[field]
    ).localeCompare(
      normalize(
        a == null ? void 0 : a[field]
      )
    ) || safeNumber(b == null ? void 0 : b.id) - safeNumber(a == null ? void 0 : a.id);
    const byDateAsc = (field) => (a, b) => safeDate(
      a == null ? void 0 : a[field]
    ) - safeDate(
      b == null ? void 0 : b[field]
    ) || safeNumber(a == null ? void 0 : a.id) - safeNumber(b == null ? void 0 : b.id);
    const byDateDesc = (field) => (a, b) => safeDate(
      b == null ? void 0 : b[field]
    ) - safeDate(
      a == null ? void 0 : a[field]
    ) || safeNumber(b == null ? void 0 : b.id) - safeNumber(a == null ? void 0 : a.id);
    const sortAttempts = (items) => {
      const list = (items || []).slice();
      if (sortParam.value === "inProgress") {
        return list.filter(
          (item) => item.status === "in_progress"
        );
      }
      if (sortParam.value === "completed") {
        return list.filter(
          (item) => item.status === "completed"
        );
      }
      if (sortParam.value === "graded") {
        return list.filter(
          (item) => item.status === "graded"
        );
      }
      const sortMap = {
        idAsc: byNumberAsc("id"),
        idDesc: byNumberDesc("id"),
        attemptAsc: byNumberAsc(
          "attempt_number"
        ),
        attemptDesc: byNumberDesc(
          "attempt_number"
        ),
        scoreAsc: byNumberAsc("score"),
        scoreDesc: byNumberDesc("score"),
        maxScoreAsc: byNumberAsc(
          "max_score"
        ),
        maxScoreDesc: byNumberDesc(
          "max_score"
        ),
        percentAsc: byNumberAsc("percent"),
        percentDesc: byNumberDesc("percent"),
        durationAsc: byNumberAsc(
          "duration_seconds"
        ),
        durationDesc: byNumberDesc(
          "duration_seconds"
        ),
        startedAtAsc: byDateAsc(
          "started_at"
        ),
        startedAtDesc: byDateDesc(
          "started_at"
        ),
        finishedAtAsc: byDateAsc(
          "finished_at"
        ),
        finishedAtDesc: byDateDesc(
          "finished_at"
        ),
        statusAsc: byStringAsc("status"),
        statusDesc: byStringDesc("status"),
        itemsAsc: byNumberAsc(
          "items_count"
        ),
        itemsDesc: byNumberDesc(
          "items_count"
        ),
        userNameAsc: (a, b) => normalize(
          getUserName(a)
        ).localeCompare(
          normalize(
            getUserName(b)
          )
        ) || safeNumber(a == null ? void 0 : a.id) - safeNumber(b == null ? void 0 : b.id),
        userNameDesc: (a, b) => normalize(
          getUserName(b)
        ).localeCompare(
          normalize(
            getUserName(a)
          )
        ) || safeNumber(b == null ? void 0 : b.id) - safeNumber(a == null ? void 0 : a.id),
        quizTitleAsc: (a, b) => normalize(
          getQuizTitle(a)
        ).localeCompare(
          normalize(
            getQuizTitle(b)
          )
        ) || safeNumber(a == null ? void 0 : a.id) - safeNumber(b == null ? void 0 : b.id),
        quizTitleDesc: (a, b) => normalize(
          getQuizTitle(b)
        ).localeCompare(
          normalize(
            getQuizTitle(a)
          )
        ) || safeNumber(b == null ? void 0 : b.id) - safeNumber(a == null ? void 0 : a.id)
      };
      return sortMap[sortParam.value] ? list.sort(
        sortMap[sortParam.value]
      ) : list;
    };
    const filteredAttempts = computed(
      () => {
        let filtered = localAttempts.value || [];
        const query = normalize(
          searchQuery.value
        );
        if (!query) {
          return sortAttempts(
            filtered
          );
        }
        filtered = filtered.filter(
          (attempt) => {
            var _a, _b, _c, _d, _e, _f;
            const values = [
              attempt == null ? void 0 : attempt.id,
              attempt == null ? void 0 : attempt.status,
              attempt == null ? void 0 : attempt.attempt_number,
              attempt == null ? void 0 : attempt.score,
              attempt == null ? void 0 : attempt.max_score,
              attempt == null ? void 0 : attempt.percent,
              attempt == null ? void 0 : attempt.duration_seconds,
              /**
               * User.
               */
              (_a = attempt == null ? void 0 : attempt.user) == null ? void 0 : _a.name,
              (_b = attempt == null ? void 0 : attempt.user) == null ? void 0 : _b.email,
              /**
               * Quiz.
               */
              getQuizTitle(
                attempt
              ),
              (_c = attempt == null ? void 0 : attempt.quiz) == null ? void 0 : _c.slug,
              /**
               * Course.
               */
              getCourseTitle(
                attempt
              ),
              (_d = attempt == null ? void 0 : attempt.course) == null ? void 0 : _d.slug,
              /**
               * Module.
               */
              getModuleTitle(
                attempt
              ),
              (_e = attempt == null ? void 0 : attempt.module) == null ? void 0 : _e.slug,
              /**
               * Lesson.
               */
              getLessonTitle(
                attempt
              ),
              (_f = attempt == null ? void 0 : attempt.lesson) == null ? void 0 : _f.slug,
              /**
               * Технические данные.
               */
              attempt == null ? void 0 : attempt.ip_address,
              attempt == null ? void 0 : attempt.user_agent
            ];
            return values.some(
              (value) => normalize(
                value
              ).includes(
                query
              )
            );
          }
        );
        return sortAttempts(
          filtered
        );
      }
    );
    const paginatedAttempts = computed(
      () => {
        const per = Number(
          itemsPerPage.value || 10
        );
        const start = (currentPage.value - 1) * per;
        return filteredAttempts.value.slice(
          start,
          start + per
        );
      }
    );
    const displayedAttempts = computed(
      () => {
        return props.useServerProcessing ? attemptsList.value : paginatedAttempts.value;
      }
    );
    watch(
      [
        itemsPerPage,
        searchQuery
      ],
      () => {
        currentPage.value = 1;
      }
    );
    const showConfirmDeleteModal = ref(false);
    const attemptToDelete = ref(null);
    const confirmDelete = (attempt) => {
      attemptToDelete.value = attempt;
      showConfirmDeleteModal.value = true;
    };
    const closeModal = () => {
      showConfirmDeleteModal.value = false;
      attemptToDelete.value = null;
    };
    const deleteAttempt = () => {
      var _a;
      if (!((_a = attemptToDelete.value) == null ? void 0 : _a.id)) {
        return;
      }
      const idToDelete = attemptToDelete.value.id;
      router.delete(
        route(
          "admin.schoolQuizAttempts.destroy",
          {
            schoolQuizAttempt: idToDelete
          }
        ),
        {
          preserveScroll: true,
          preserveState: false,
          onSuccess: () => {
            toast.success(
              `Попытка ID: ${idToDelete} удалена.`
            );
          },
          onError: (errors) => {
            const firstKey = Object.keys(
              errors || {}
            )[0];
            const errorMessage = (errors == null ? void 0 : errors.general) || (errors == null ? void 0 : errors[firstKey]) || "Ошибка при удалении попытки.";
            toast.error(
              `${errorMessage} ID: ${idToDelete}`
            );
          },
          onFinish: () => closeModal()
        }
      );
    };
    const patchAttempt = (attemptId, payload) => {
      const index = localAttempts.value.findIndex(
        (item) => item.id === attemptId
      );
      if (index === -1) {
        return;
      }
      localAttempts.value[index] = {
        ...localAttempts.value[index],
        ...payload
      };
    };
    const selectedAttempts = ref([]);
    const toggleAll = (payload) => {
      var _a;
      const checked = (payload == null ? void 0 : payload.checked) ?? ((_a = payload == null ? void 0 : payload.target) == null ? void 0 : _a.checked) ?? false;
      const ids = (payload == null ? void 0 : payload.ids) ?? displayedAttempts.value.map(
        (attempt) => attempt.id
      );
      if (checked) {
        selectedAttempts.value = [
          .../* @__PURE__ */ new Set([
            ...selectedAttempts.value,
            ...ids
          ])
        ];
        return;
      }
      selectedAttempts.value = selectedAttempts.value.filter(
        (id) => !ids.includes(id)
      );
    };
    const toggleSelectAttempt = (id) => {
      const index = selectedAttempts.value.indexOf(
        id
      );
      if (index > -1) {
        selectedAttempts.value.splice(
          index,
          1
        );
        return;
      }
      selectedAttempts.value.push(
        id
      );
    };
    const bulkUpdateStatus = (status) => {
      if (!selectedAttempts.value.length) {
        toast.warning(
          "Выберите попытки."
        );
        return;
      }
      const idsToUpdate = [
        ...selectedAttempts.value
      ];
      router.put(
        route(
          "admin.actions.schoolQuizAttempts.bulkUpdateStatus"
        ),
        {
          ids: idsToUpdate,
          status
        },
        {
          preserveScroll: true,
          preserveState: true,
          onSuccess: () => {
            idsToUpdate.forEach(
              (id) => patchAttempt(
                id,
                {
                  status
                }
              )
            );
            selectedAttempts.value = [];
            toast.success(
              "Статус выбранных попыток обновлён."
            );
          },
          onError: (errors) => {
            toast.error(
              (errors == null ? void 0 : errors.ids) || (errors == null ? void 0 : errors.status) || (errors == null ? void 0 : errors.general) || "Ошибка массового обновления статуса."
            );
          }
        }
      );
    };
    const bulkDestroy = () => {
      var _a, _b, _c;
      if (!selectedAttempts.value.length) {
        toast.warning(
          "Выберите попытки для удаления."
        );
        return;
      }
      if (!confirm(
        "Вы уверены, что хотите удалить выбранные попытки?"
      )) {
        return;
      }
      router.delete(
        route(
          "admin.actions.schoolQuizAttempts.bulkDestroy"
        ),
        {
          data: {
            ids: selectedAttempts.value,
            ...((_a = props.filters) == null ? void 0 : _a.school_quiz_id) ? {
              school_quiz_id: props.filters.school_quiz_id
            } : {},
            ...((_b = props.filters) == null ? void 0 : _b.user_id) ? {
              user_id: props.filters.user_id
            } : {},
            ...((_c = props.filters) == null ? void 0 : _c.status) ? {
              status: props.filters.status
            } : {}
          },
          preserveScroll: true,
          preserveState: false,
          onSuccess: () => {
            selectedAttempts.value = [];
            toast.success(
              "Выбранные попытки успешно удалены."
            );
          },
          onError: (errors) => {
            const firstKey = Object.keys(
              errors || {}
            )[0];
            toast.error(
              (errors == null ? void 0 : errors[firstKey]) || "Ошибка массового удаления попыток."
            );
          }
        }
      );
    };
    const handleBulkAction = (event) => {
      const action = event.target.value;
      if (action === "selectAll") {
        toggleAll({
          target: {
            checked: true
          }
        });
      } else if (action === "deselectAll") {
        toggleAll({
          target: {
            checked: false
          }
        });
      } else if (action.startsWith(
        "status:"
      )) {
        bulkUpdateStatus(
          action.split(":")[1]
        );
      } else if (action === "delete") {
        bulkDestroy();
      }
      event.target.value = "";
    };
    return (_ctx, _push, _parent, _attrs) => {
      _push(ssrRenderComponent(AdminLayout, mergeProps({
        title: unref(t)("quizAttempts")
      }, _attrs), {
        header: withCtx((_, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(ssrRenderComponent(TitlePage, null, {
              default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                if (_push3) {
                  _push3(`${ssrInterpolate(unref(t)("quizAttempts"))}`);
                } else {
                  return [
                    createTextVNode(toDisplayString(unref(t)("quizAttempts")), 1)
                  ];
                }
              }),
              _: 1
            }, _parent2, _scopeId));
          } else {
            return [
              createVNode(TitlePage, null, {
                default: withCtx(() => [
                  createTextVNode(toDisplayString(unref(t)("quizAttempts")), 1)
                ]),
                _: 1
              })
            ];
          }
        }),
        default: withCtx((_, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(`<div class="px-2 py-2 w-full max-w-12xl mx-auto"${_scopeId}><div class="p-4 bg-slate-50 dark:bg-slate-700 border border-blue-400 dark:border-blue-200 overflow-hidden shadow-md shadow-gray-500 dark:shadow-slate-400 bg-opacity-95 dark:bg-opacity-95"${_scopeId}><div class="sm:flex sm:justify-end sm:items-center mb-3"${_scopeId}>`);
            _push2(ssrRenderComponent(_sfc_main$7, {
              "setting-key": "adminSchoolQuizAttemptsProcessingMode",
              mode: __props.adminSchoolQuizAttemptsProcessingMode,
              "use-server-processing": __props.useServerProcessing,
              total: __props.attemptsCount
            }, null, _parent2, _scopeId));
            _push2(`</div>`);
            if (__props.attemptsCount && !__props.useServerProcessing) {
              _push2(ssrRenderComponent(_sfc_main$8, {
                modelValue: searchQuery.value,
                "onUpdate:modelValue": ($event) => searchQuery.value = $event,
                placeholder: unref(t)("search")
              }, null, _parent2, _scopeId));
            } else {
              _push2(`<!---->`);
            }
            if (__props.attemptsCount && __props.useServerProcessing) {
              _push2(ssrRenderComponent(_sfc_main$9, {
                modelValue: searchQuery.value,
                "onUpdate:modelValue": ($event) => searchQuery.value = $event
              }, null, _parent2, _scopeId));
            } else {
              _push2(`<!---->`);
            }
            if (__props.attemptsCount) {
              _push2(`<div class="flex justify-between items-center flex-col md:flex-row my-3"${_scopeId}>`);
              if (!__props.useServerProcessing) {
                _push2(ssrRenderComponent(_sfc_main$a, {
                  "items-per-page": itemsPerPage.value,
                  "onUpdate:itemsPerPage": ($event) => itemsPerPage.value = $event
                }, null, _parent2, _scopeId));
              } else {
                _push2(ssrRenderComponent(_sfc_main$b, {
                  "items-per-page": itemsPerPage.value,
                  "update-route": "admin.settings.updateAdminCountSchoolQuizAttempts"
                }, null, _parent2, _scopeId));
              }
              _push2(ssrRenderComponent(_sfc_main$4, {
                sortParam: sortParam.value,
                "onUpdate:sortParam": (value) => sortParam.value = value
              }, null, _parent2, _scopeId));
              _push2(`</div>`);
            } else {
              _push2(`<!---->`);
            }
            if (__props.attemptsCount) {
              _push2(`<div class="flex justify-between items-center flex-col md:flex-row my-3"${_scopeId}>`);
              _push2(ssrRenderComponent(_sfc_main$c, null, {
                default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                  if (_push3) {
                    _push3(`${ssrInterpolate(__props.attemptsCount)}`);
                  } else {
                    return [
                      createTextVNode(toDisplayString(__props.attemptsCount), 1)
                    ];
                  }
                }),
                _: 1
              }, _parent2, _scopeId));
              _push2(ssrRenderComponent(_sfc_main$1, {
                onChange: handleBulkAction
              }, null, _parent2, _scopeId));
              _push2(ssrRenderComponent(_sfc_main$d, {
                viewMode: viewMode.value,
                "onUpdate:viewMode": ($event) => viewMode.value = $event
              }, null, _parent2, _scopeId));
              _push2(`</div>`);
            } else {
              _push2(`<!---->`);
            }
            if (__props.attemptsCount) {
              _push2(`<div class="flex justify-center items-center flex-col md:flex-row mb-3"${_scopeId}>`);
              if (!__props.useServerProcessing) {
                _push2(ssrRenderComponent(_sfc_main$e, {
                  "current-page": currentPage.value,
                  "items-per-page": itemsPerPage.value,
                  "total-items": filteredAttempts.value.length,
                  "onUpdate:currentPage": ($event) => currentPage.value = $event
                }, null, _parent2, _scopeId));
              } else {
                _push2(ssrRenderComponent(_sfc_main$f, { pagination: __props.attempts }, null, _parent2, _scopeId));
              }
              _push2(`</div>`);
            } else {
              _push2(`<!---->`);
            }
            if (viewMode.value === "table") {
              _push2(ssrRenderComponent(_sfc_main$3, {
                attempts: displayedAttempts.value,
                "selected-attempts": selectedAttempts.value,
                onToggleSelect: toggleSelectAttempt,
                onToggleAll: toggleAll,
                onDelete: confirmDelete
              }, null, _parent2, _scopeId));
            } else {
              _push2(ssrRenderComponent(_sfc_main$2, {
                attempts: displayedAttempts.value,
                "selected-attempts": selectedAttempts.value,
                onToggleSelect: toggleSelectAttempt,
                onToggleAll: toggleAll,
                onDelete: confirmDelete
              }, null, _parent2, _scopeId));
            }
            if (__props.attemptsCount) {
              _push2(`<div class="flex justify-center items-center flex-col md:flex-row mt-3"${_scopeId}>`);
              if (!__props.useServerProcessing) {
                _push2(ssrRenderComponent(_sfc_main$e, {
                  "current-page": currentPage.value,
                  "items-per-page": itemsPerPage.value,
                  "total-items": filteredAttempts.value.length,
                  "onUpdate:currentPage": ($event) => currentPage.value = $event
                }, null, _parent2, _scopeId));
              } else {
                _push2(ssrRenderComponent(_sfc_main$f, { pagination: __props.attempts }, null, _parent2, _scopeId));
              }
              _push2(`</div>`);
            } else {
              _push2(`<!---->`);
            }
            _push2(`</div></div>`);
            _push2(ssrRenderComponent(_sfc_main$g, {
              show: showConfirmDeleteModal.value,
              onClose: closeModal,
              onCancel: closeModal,
              onConfirm: deleteAttempt,
              cancelText: unref(t)("cancel"),
              confirmText: unref(t)("yesDelete")
            }, null, _parent2, _scopeId));
          } else {
            return [
              createVNode("div", { class: "px-2 py-2 w-full max-w-12xl mx-auto" }, [
                createVNode("div", { class: "p-4 bg-slate-50 dark:bg-slate-700 border border-blue-400 dark:border-blue-200 overflow-hidden shadow-md shadow-gray-500 dark:shadow-slate-400 bg-opacity-95 dark:bg-opacity-95" }, [
                  createVNode("div", { class: "sm:flex sm:justify-end sm:items-center mb-3" }, [
                    createVNode(_sfc_main$7, {
                      "setting-key": "adminSchoolQuizAttemptsProcessingMode",
                      mode: __props.adminSchoolQuizAttemptsProcessingMode,
                      "use-server-processing": __props.useServerProcessing,
                      total: __props.attemptsCount
                    }, null, 8, ["mode", "use-server-processing", "total"])
                  ]),
                  __props.attemptsCount && !__props.useServerProcessing ? (openBlock(), createBlock(_sfc_main$8, {
                    key: 0,
                    modelValue: searchQuery.value,
                    "onUpdate:modelValue": ($event) => searchQuery.value = $event,
                    placeholder: unref(t)("search")
                  }, null, 8, ["modelValue", "onUpdate:modelValue", "placeholder"])) : createCommentVNode("", true),
                  __props.attemptsCount && __props.useServerProcessing ? (openBlock(), createBlock(_sfc_main$9, {
                    key: 1,
                    modelValue: searchQuery.value,
                    "onUpdate:modelValue": ($event) => searchQuery.value = $event
                  }, null, 8, ["modelValue", "onUpdate:modelValue"])) : createCommentVNode("", true),
                  __props.attemptsCount ? (openBlock(), createBlock("div", {
                    key: 2,
                    class: "flex justify-between items-center flex-col md:flex-row my-3"
                  }, [
                    !__props.useServerProcessing ? (openBlock(), createBlock(_sfc_main$a, {
                      key: 0,
                      "items-per-page": itemsPerPage.value,
                      "onUpdate:itemsPerPage": ($event) => itemsPerPage.value = $event
                    }, null, 8, ["items-per-page", "onUpdate:itemsPerPage"])) : (openBlock(), createBlock(_sfc_main$b, {
                      key: 1,
                      "items-per-page": itemsPerPage.value,
                      "update-route": "admin.settings.updateAdminCountSchoolQuizAttempts"
                    }, null, 8, ["items-per-page"])),
                    createVNode(_sfc_main$4, {
                      sortParam: sortParam.value,
                      "onUpdate:sortParam": (value) => sortParam.value = value
                    }, null, 8, ["sortParam", "onUpdate:sortParam"])
                  ])) : createCommentVNode("", true),
                  __props.attemptsCount ? (openBlock(), createBlock("div", {
                    key: 3,
                    class: "flex justify-between items-center flex-col md:flex-row my-3"
                  }, [
                    createVNode(_sfc_main$c, null, {
                      default: withCtx(() => [
                        createTextVNode(toDisplayString(__props.attemptsCount), 1)
                      ]),
                      _: 1
                    }),
                    createVNode(_sfc_main$1, {
                      onChange: handleBulkAction
                    }, null, 8, ["onChange"]),
                    createVNode(_sfc_main$d, {
                      viewMode: viewMode.value,
                      "onUpdate:viewMode": ($event) => viewMode.value = $event
                    }, null, 8, ["viewMode", "onUpdate:viewMode"])
                  ])) : createCommentVNode("", true),
                  __props.attemptsCount ? (openBlock(), createBlock("div", {
                    key: 4,
                    class: "flex justify-center items-center flex-col md:flex-row mb-3"
                  }, [
                    !__props.useServerProcessing ? (openBlock(), createBlock(_sfc_main$e, {
                      key: 0,
                      "current-page": currentPage.value,
                      "items-per-page": itemsPerPage.value,
                      "total-items": filteredAttempts.value.length,
                      "onUpdate:currentPage": ($event) => currentPage.value = $event
                    }, null, 8, ["current-page", "items-per-page", "total-items", "onUpdate:currentPage"])) : (openBlock(), createBlock(_sfc_main$f, {
                      key: 1,
                      pagination: __props.attempts
                    }, null, 8, ["pagination"]))
                  ])) : createCommentVNode("", true),
                  viewMode.value === "table" ? (openBlock(), createBlock(_sfc_main$3, {
                    key: 5,
                    attempts: displayedAttempts.value,
                    "selected-attempts": selectedAttempts.value,
                    onToggleSelect: toggleSelectAttempt,
                    onToggleAll: toggleAll,
                    onDelete: confirmDelete
                  }, null, 8, ["attempts", "selected-attempts"])) : (openBlock(), createBlock(_sfc_main$2, {
                    key: 6,
                    attempts: displayedAttempts.value,
                    "selected-attempts": selectedAttempts.value,
                    onToggleSelect: toggleSelectAttempt,
                    onToggleAll: toggleAll,
                    onDelete: confirmDelete
                  }, null, 8, ["attempts", "selected-attempts"])),
                  __props.attemptsCount ? (openBlock(), createBlock("div", {
                    key: 7,
                    class: "flex justify-center items-center flex-col md:flex-row mt-3"
                  }, [
                    !__props.useServerProcessing ? (openBlock(), createBlock(_sfc_main$e, {
                      key: 0,
                      "current-page": currentPage.value,
                      "items-per-page": itemsPerPage.value,
                      "total-items": filteredAttempts.value.length,
                      "onUpdate:currentPage": ($event) => currentPage.value = $event
                    }, null, 8, ["current-page", "items-per-page", "total-items", "onUpdate:currentPage"])) : (openBlock(), createBlock(_sfc_main$f, {
                      key: 1,
                      pagination: __props.attempts
                    }, null, 8, ["pagination"]))
                  ])) : createCommentVNode("", true)
                ])
              ]),
              createVNode(_sfc_main$g, {
                show: showConfirmDeleteModal.value,
                onClose: closeModal,
                onCancel: closeModal,
                onConfirm: deleteAttempt,
                cancelText: unref(t)("cancel"),
                confirmText: unref(t)("yesDelete")
              }, null, 8, ["show", "cancelText", "confirmText"])
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
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Pages/Admin/School/SchoolQuizAttempts/Index.vue");
  return _sfc_setup ? _sfc_setup(props, ctx) : void 0;
};
export {
  _sfc_main as default
};
