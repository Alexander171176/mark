import { mergeProps, unref, useSSRContext, ref, watch, computed, withCtx, createTextVNode, toDisplayString, createBlock, openBlock, Fragment, createVNode, createCommentVNode } from "vue";
import { ssrRenderAttrs, ssrInterpolate, ssrRenderList, ssrRenderAttr, ssrRenderClass, ssrRenderStyle, ssrRenderComponent } from "vue/server-renderer";
import { router } from "@inertiajs/vue3";
import { useI18n } from "vue-i18n";
import { useToast } from "vue-toastification";
import { A as AdminLayout } from "./AdminLayout-03yd3nxC.js";
import { T as TitlePage } from "./TitlePage-CBZZ8YZB.js";
import { _ as _sfc_main$a, a as _sfc_main$d, b as _sfc_main$e } from "./ItemsPerPageSelect-DOO-E4Z0.js";
import { _ as _sfc_main$9, a as _sfc_main$f } from "./ServerSearchInput-CxQUCmzV.js";
import { _ as _sfc_main$8 } from "./SearchInput-xZSYbbms.js";
import { _ as _sfc_main$c } from "./CountTable-p8tyXGUL.js";
import { _ as _sfc_main$b } from "./ServerItemsPerPageSelect-B_wSkKlU.js";
import { _ as _sfc_main$7 } from "./ProcessingModeSwitcher-BJvzFf6_.js";
import { _ as _sfc_main$g } from "./DangerModal-DjIe5VDg.js";
import { _ as _sfc_main$6 } from "./DefaultButton-DMDyGacf.js";
import { _ as _sfc_main$4 } from "./IconEdit-Bw90OQvk.js";
import { _ as _sfc_main$5 } from "./DeleteIconButton-DLv2Mr1x.js";
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
const _sfc_main$3 = {
  __name: "EnrollmentTable",
  __ssrInlineRender: true,
  props: {
    enrollments: {
      type: Array,
      default: () => []
    }
  },
  emits: [
    "delete"
  ],
  setup(__props, { emit: __emit }) {
    const { t } = useI18n();
    const emit = __emit;
    const statusLabelKeyMap = {
      active: "statusEnrollmentActive",
      completed: "statusEnrollmentCompleted",
      cancelled: "statusEnrollmentCancelled",
      expired: "statusEnrollmentExpired",
      paused: "statusEnrollmentPaused"
    };
    const getStatusLabel = (status) => {
      if (!status) {
        return "—";
      }
      const key = statusLabelKeyMap[status];
      return key ? t(key) : status;
    };
    const getStatusClasses = (status) => {
      switch (status) {
        case "active":
          return "border-emerald-400 bg-emerald-100 dark:bg-emerald-900/40 text-emerald-800 dark:text-emerald-100";
        case "completed":
          return "border-blue-400 bg-blue-100 dark:bg-blue-900/40 text-blue-800 dark:text-blue-100";
        case "paused":
          return "border-amber-400 bg-amber-100 dark:bg-amber-900/40 text-amber-800 dark:text-amber-100";
        case "expired":
        case "cancelled":
          return "border-rose-400 bg-rose-100 dark:bg-rose-900/40 text-rose-800 dark:text-rose-100";
        default:
          return "border-slate-400 bg-slate-200 dark:bg-slate-700 text-slate-800 dark:text-slate-100";
      }
    };
    const getUserName = (enrollment) => {
      var _a;
      return ((_a = enrollment == null ? void 0 : enrollment.user) == null ? void 0 : _a.name) || ((enrollment == null ? void 0 : enrollment.user_id) ? `#${enrollment.user_id}` : "—");
    };
    const getUserEmail = (enrollment) => {
      var _a;
      return ((_a = enrollment == null ? void 0 : enrollment.user) == null ? void 0 : _a.email) || "";
    };
    const getCourseTitle = (enrollment) => {
      var _a, _b;
      return ((_b = (_a = enrollment == null ? void 0 : enrollment.course) == null ? void 0 : _a.translation) == null ? void 0 : _b.title) || ((enrollment == null ? void 0 : enrollment.school_course_id) ? `#${enrollment.school_course_id}` : "—");
    };
    const getCourseSlug = (enrollment) => {
      var _a;
      return ((_a = enrollment == null ? void 0 : enrollment.course) == null ? void 0 : _a.slug) || "";
    };
    const getScheduleTitle = (enrollment) => {
      var _a, _b;
      return ((_b = (_a = enrollment == null ? void 0 : enrollment.schedule) == null ? void 0 : _a.translation) == null ? void 0 : _b.title) || ((enrollment == null ? void 0 : enrollment.school_course_schedule_id) ? `#${enrollment.school_course_schedule_id}` : "—");
    };
    const formatDateTime = (value) => {
      if (!value) {
        return "—";
      }
      const date = new Date(value);
      if (Number.isNaN(
        date.getTime()
      )) {
        return value;
      }
      return date.toLocaleString(
        "ru-RU"
      );
    };
    const formatDaysLeft = (daysLeft) => {
      if (daysLeft === null || daysLeft === void 0) {
        return "∞";
      }
      if (typeof daysLeft !== "number") {
        return daysLeft;
      }
      return daysLeft < 0 ? t(
        "daysLeftPast",
        {
          days: Math.abs(daysLeft)
        }
      ) : t(
        "daysLeft",
        {
          days: daysLeft
        }
      );
    };
    const progressWidth = (value) => {
      return `${Math.min(
        Math.max(
          value ?? 0,
          0
        ),
        100
      )}%`;
    };
    const formatDatesTitle = (enrollment) => {
      return [
        `${t("shortStarted")}: ${formatDateTime(enrollment.started_at)}`,
        `${t("shortExpires")}: ${formatDateTime(enrollment.expires_at)}`,
        `${t("shortCompleted")}: ${formatDateTime(enrollment.completed_at)}`
      ].join("\n");
    };
    const handleDelete = (enrollment) => {
      emit(
        "delete",
        enrollment
      );
    };
    return (_ctx, _push, _parent, _attrs) => {
      _push(`<div${ssrRenderAttrs(mergeProps({ class: "relative rounded-sm border border-slate-200 bg-white shadow-lg dark:border-slate-600 dark:bg-slate-700" }, _attrs))}><div class="overflow-x-auto">`);
      if (__props.enrollments.length > 0) {
        _push(`<table class="table-auto w-full text-slate-700 dark:text-slate-100"><thead class="border border-solid border-gray-300 bg-slate-200 text-sm uppercase dark:border-gray-700 dark:bg-cyan-900"><tr><th class="w-px px-2 py-3"><div class="text-left text-sm font-medium">${ssrInterpolate(unref(t)("id"))}</div></th><th class="px-2 py-3 whitespace-nowrap"><div class="text-left text-sm font-medium">${ssrInterpolate(unref(t)("users"))}</div></th><th class="px-2 py-3 whitespace-nowrap"><div class="text-left text-sm font-medium">${ssrInterpolate(unref(t)("course"))}</div></th><th class="px-2 py-3 whitespace-nowrap"><div class="text-left text-sm font-medium">${ssrInterpolate(unref(t)("schedule"))}</div></th><th class="px-2 py-3 whitespace-nowrap"><div class="text-center text-sm font-medium">${ssrInterpolate(unref(t)("status"))}</div></th><th class="px-2 py-3 whitespace-nowrap"><div class="text-left text-sm font-medium">${ssrInterpolate(unref(t)("access"))}</div></th><th class="px-2 py-3 whitespace-nowrap"><div class="text-left text-sm font-medium">${ssrInterpolate(unref(t)("progress"))}</div></th><th class="w-px px-2 py-3"><div class="text-right text-sm font-medium">${ssrInterpolate(unref(t)("actions"))}</div></th></tr></thead><tbody><!--[-->`);
        ssrRenderList(__props.enrollments, (enrollment) => {
          var _a, _b;
          _push(`<tr class="border-b-2 text-xs font-semibold hover:bg-slate-100 dark:hover:bg-cyan-800"><td class="px-2 py-3 whitespace-nowrap"><div class="text-left font-semibold text-slate-800 dark:text-slate-200">${ssrInterpolate(enrollment.id)}</div></td><td class="px-2 py-3 whitespace-nowrap"><div class="flex flex-col"${ssrRenderAttr("title", enrollment.notes || "—")}><span class="text-sm text-slate-800 dark:text-slate-100">${ssrInterpolate(getUserName(enrollment))}</span>`);
          if (getUserEmail(enrollment)) {
            _push(`<span class="text-xs text-slate-500 dark:text-slate-300">${ssrInterpolate(getUserEmail(enrollment))}</span>`);
          } else {
            _push(`<!---->`);
          }
          _push(`</div></td><td class="px-2 py-3"><div class="flex flex-col"><span class="text-sm text-sky-700 dark:text-sky-200">${ssrInterpolate(getCourseTitle(enrollment))}</span>`);
          if (getCourseSlug(enrollment)) {
            _push(`<span class="text-xs text-slate-500 dark:text-slate-300">${ssrInterpolate(getCourseSlug(enrollment))}</span>`);
          } else {
            _push(`<!---->`);
          }
          _push(`</div></td><td class="px-2 py-3"><div class="flex flex-col"><span class="text-xs text-amber-700 dark:text-amber-200">${ssrInterpolate(getScheduleTitle(enrollment))}</span>`);
          if ((_a = enrollment.schedule) == null ? void 0 : _a.starts_at) {
            _push(`<span class="text-[11px] text-slate-500 dark:text-slate-300">${ssrInterpolate(formatDateTime(
              enrollment.schedule.starts_at
            ))} `);
            if ((_b = enrollment.schedule) == null ? void 0 : _b.ends_at) {
              _push(`<span> — <br> ${ssrInterpolate(formatDateTime(
                enrollment.schedule.ends_at
              ))}</span>`);
            } else {
              _push(`<!---->`);
            }
            _push(`</span>`);
          } else {
            _push(`<!---->`);
          }
          _push(`</div></td><td class="px-2 py-3 whitespace-nowrap"><div class="flex justify-center"><span class="${ssrRenderClass([
            "inline-flex items-center rounded-sm border px-2 py-0.5 text-[11px] font-semibold",
            getStatusClasses(enrollment.status)
          ])}">${ssrInterpolate(getStatusLabel(enrollment.status))}</span></div></td><td class="px-2 py-3"${ssrRenderAttr("title", formatDatesTitle(enrollment))}><div class="flex flex-col"><span class="${ssrRenderClass([
            enrollment.is_accessible ? "text-emerald-700 dark:text-emerald-300" : "text-rose-700 dark:text-rose-300",
            "text-xs font-semibold"
          ])}">${ssrInterpolate(enrollment.is_accessible ? unref(t)("accessGranted") : unref(t)("accessDenied"))}</span><span class="text-[11px] text-slate-500 dark:text-slate-300">${ssrInterpolate(formatDaysLeft(enrollment.days_left))}</span>`);
          if (enrollment.certificate) {
            _push(`<span class="mt-1 inline-flex items-center text-[11px] text-yellow-600 dark:text-yellow-300"> ★ ${ssrInterpolate(unref(t)("certificateIssued"))}</span>`);
          } else {
            _push(`<!---->`);
          }
          _push(`</div></td><td class="px-2 py-3 whitespace-nowrap"><div class="flex flex-col"><span class="text-xs text-slate-800 dark:text-slate-100">${ssrInterpolate(enrollment.progress_percent ?? 0)}% </span><div class="mt-1 h-1.5 w-24 bg-slate-300 dark:bg-slate-600"><div class="h-1.5 bg-emerald-500 dark:bg-emerald-400" style="${ssrRenderStyle({
            width: progressWidth(
              enrollment.progress_percent
            )
          })}"></div></div></div></td><td class="px-2 py-3 whitespace-nowrap"><div class="flex items-center justify-end space-x-1">`);
          _push(ssrRenderComponent(_sfc_main$4, {
            href: _ctx.route(
              "admin.schoolEnrollments.edit",
              {
                schoolEnrollment: enrollment.id
              }
            )
          }, null, _parent));
          _push(ssrRenderComponent(_sfc_main$5, {
            title: unref(t)("delete"),
            onDelete: ($event) => handleDelete(enrollment)
          }, null, _parent));
          _push(`</div></td></tr>`);
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
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Components/Admin/School/SchoolEnrollment/Table/EnrollmentTable.vue");
  return _sfc_setup$3 ? _sfc_setup$3(props, ctx) : void 0;
};
const _sfc_main$2 = {
  __name: "EnrollmentCardGrid",
  __ssrInlineRender: true,
  props: {
    enrollments: {
      type: Array,
      default: () => []
    }
  },
  emits: [
    "delete"
  ],
  setup(__props, { emit: __emit }) {
    const { t } = useI18n();
    const emit = __emit;
    const statusLabelKeyMap = {
      active: "statusEnrollmentActive",
      completed: "statusEnrollmentCompleted",
      cancelled: "statusEnrollmentCancelled",
      expired: "statusEnrollmentExpired",
      paused: "statusEnrollmentPaused"
    };
    const getStatusLabel = (status) => {
      if (!status) {
        return "—";
      }
      const key = statusLabelKeyMap[status];
      return key ? t(key) : status;
    };
    const getStatusClasses = (status) => {
      switch (status) {
        case "active":
          return "border-emerald-400 bg-emerald-100 dark:bg-emerald-900/40 text-emerald-800 dark:text-emerald-100";
        case "completed":
          return "border-blue-400 bg-blue-100 dark:bg-blue-900/40 text-blue-800 dark:text-blue-100";
        case "paused":
          return "border-amber-400 bg-amber-100 dark:bg-amber-900/40 text-amber-800 dark:text-amber-100";
        case "expired":
        case "cancelled":
          return "border-rose-400 bg-rose-100 dark:bg-rose-900/40 text-rose-800 dark:text-rose-100";
        default:
          return "border-slate-400 bg-slate-200 dark:bg-slate-700 text-slate-800 dark:text-slate-100";
      }
    };
    const getUserName = (enrollment) => {
      var _a;
      return ((_a = enrollment == null ? void 0 : enrollment.user) == null ? void 0 : _a.name) || ((enrollment == null ? void 0 : enrollment.user_id) ? `#${enrollment.user_id}` : "—");
    };
    const getUserEmail = (enrollment) => {
      var _a;
      return ((_a = enrollment == null ? void 0 : enrollment.user) == null ? void 0 : _a.email) || "";
    };
    const getCourseTitle = (enrollment) => {
      var _a, _b;
      return ((_b = (_a = enrollment == null ? void 0 : enrollment.course) == null ? void 0 : _a.translation) == null ? void 0 : _b.title) || ((enrollment == null ? void 0 : enrollment.school_course_id) ? `#${enrollment.school_course_id}` : "—");
    };
    const getCourseSlug = (enrollment) => {
      var _a;
      return ((_a = enrollment == null ? void 0 : enrollment.course) == null ? void 0 : _a.slug) || "";
    };
    const getScheduleTitle = (enrollment) => {
      var _a, _b;
      return ((_b = (_a = enrollment == null ? void 0 : enrollment.schedule) == null ? void 0 : _a.translation) == null ? void 0 : _b.title) || "";
    };
    const formatDateTime = (value) => {
      if (!value) {
        return "—";
      }
      const date = new Date(value);
      if (Number.isNaN(
        date.getTime()
      )) {
        return value;
      }
      return date.toLocaleString(
        "ru-RU"
      );
    };
    const formatDaysLeft = (daysLeft) => {
      if (daysLeft === null || daysLeft === void 0) {
        return "∞";
      }
      if (typeof daysLeft !== "number") {
        return daysLeft;
      }
      return daysLeft < 0 ? t(
        "daysLeftPast",
        {
          days: Math.abs(daysLeft)
        }
      ) : t(
        "daysLeft",
        {
          days: daysLeft
        }
      );
    };
    const progressWidth = (value) => {
      return `${Math.min(
        Math.max(
          value ?? 0,
          0
        ),
        100
      )}%`;
    };
    const handleDelete = (enrollment) => {
      emit(
        "delete",
        enrollment
      );
    };
    return (_ctx, _push, _parent, _attrs) => {
      _push(`<div${ssrRenderAttrs(mergeProps({ class: "relative rounded-sm border border-slate-400 bg-white shadow-lg dark:border-slate-500 dark:bg-slate-700" }, _attrs))}>`);
      if (__props.enrollments.length) {
        _push(`<div class="p-3"><div class="grid grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4"><!--[-->`);
        ssrRenderList(__props.enrollments, (enrollment) => {
          var _a, _b;
          _push(`<article class="relative flex h-full flex-col rounded-md border border-slate-400 bg-slate-50/70 shadow-sm transition-shadow duration-150 hover:shadow-md dark:border-slate-500 dark:bg-slate-800/80"><header class="flex items-center justify-between border-b border-dashed border-slate-400 px-2 py-1 dark:border-slate-500"><div class="rounded-sm border border-gray-400 bg-slate-200 px-1.5 py-0.5 text-[10px] font-semibold text-slate-800 dark:bg-slate-700 dark:text-blue-100"> ID: ${ssrInterpolate(enrollment.id)}</div><div><span class="${ssrRenderClass([
            "inline-flex items-center rounded-sm border px-2 py-0.5 text-[11px] font-semibold",
            getStatusClasses(enrollment.status)
          ])}"${ssrRenderAttr("title", unref(t)("status"))}>${ssrInterpolate(getStatusLabel(enrollment.status))}</span></div></header><div class="flex flex-1 flex-col space-y-2 px-3 py-2"><div class="text-center"><div class="text-[12px] font-medium text-slate-900 dark:text-slate-50">${ssrInterpolate(getUserName(enrollment))}</div>`);
          if (getUserEmail(enrollment)) {
            _push(`<div class="text-[11px] text-slate-500 dark:text-slate-300">${ssrInterpolate(getUserEmail(enrollment))}</div>`);
          } else {
            _push(`<!---->`);
          }
          _push(`</div><div class="space-y-0.5 text-center border border-dotted border-slate-400 dark:border-slate-500"><div class="line-clamp-2 text-[12px] font-semibold text-sky-700 dark:text-sky-200"${ssrRenderAttr("title", getCourseSlug(enrollment))}>${ssrInterpolate(getCourseTitle(enrollment))}</div>`);
          if (getCourseSlug(enrollment)) {
            _push(`<div class="text-[9px] text-slate-500 dark:text-slate-400">${ssrInterpolate(getCourseSlug(enrollment))}</div>`);
          } else {
            _push(`<!---->`);
          }
          _push(`</div><div class="flex h-12 flex-col justify-center space-y-0.5 text-center"><div class="line-clamp-2 text-[11px] font-semibold text-amber-700 dark:text-amber-200">${ssrInterpolate(getScheduleTitle(enrollment) || unref(t)("scheduleNotSet"))}</div>`);
          if ((_a = enrollment.schedule) == null ? void 0 : _a.starts_at) {
            _push(`<div class="text-[10px] text-slate-500 dark:text-slate-300">${ssrInterpolate(formatDateTime(
              enrollment.schedule.starts_at
            ))} `);
            if ((_b = enrollment.schedule) == null ? void 0 : _b.ends_at) {
              _push(`<span> — <br> ${ssrInterpolate(formatDateTime(
                enrollment.schedule.ends_at
              ))}</span>`);
            } else {
              _push(`<!---->`);
            }
            _push(`</div>`);
          } else {
            _push(`<!---->`);
          }
          _push(`</div><div class="mt-1 space-y-1"><div class="flex items-center justify-center space-x-2"><span class="${ssrRenderClass([
            enrollment.is_accessible ? "text-emerald-700 dark:text-emerald-300" : "text-rose-700 dark:text-rose-300",
            "text-[11px] font-semibold"
          ])}">${ssrInterpolate(enrollment.is_accessible ? unref(t)("accessGranted") : unref(t)("accessDenied"))}</span><span class="text-[10px] text-slate-500 dark:text-slate-300">${ssrInterpolate(formatDaysLeft(enrollment.days_left))}</span></div><div class="flex flex-col items-center"><span class="mb-0.5 text-[11px] text-slate-700 dark:text-slate-200">${ssrInterpolate(unref(t)("progress"))}: ${ssrInterpolate(enrollment.progress_percent ?? 0)}% </span><div class="h-1.5 w-24 overflow-hidden rounded-full bg-slate-300 dark:bg-slate-600"><div class="h-1.5 bg-emerald-500 dark:bg-emerald-400" style="${ssrRenderStyle({
            width: progressWidth(
              enrollment.progress_percent
            )
          })}"></div></div></div></div><div class="mt-1 flex flex-col justify-center space-y-0.5 text-center"><div class="line-clamp-3 text-center text-[11px] font-semibold text-gray-800 dark:text-gray-200"${ssrRenderAttr("title", enrollment.notes || "—")}>${ssrInterpolate(enrollment.notes || "—")}</div><span class="mt-0.5 text-[10px] text-slate-500 dark:text-slate-300"${ssrRenderAttr("title", unref(t)("shortStarted"))}>${ssrInterpolate(formatDateTime(enrollment.started_at))}</span></div></div><footer class="border-t border-dashed border-slate-400 px-3 py-2 dark:border-slate-500"><div class="flex items-center justify-center space-x-2"><div class="flex items-center space-x-1">`);
          if (enrollment.certificate) {
            _push(`<span class="inline-flex items-center text-[12px] text-yellow-700 dark:text-yellow-300"><svg class="mr-1 h-4 w-4" viewBox="0 0 16 16" fill="currentColor"><path d="M8 0L6.59 4.26 2 4.62l3.5 2.88L4.33 12 8 9.8 11.67 12 10.5 7.5 14 4.62l-4.59-.36L8 0z"></path></svg> ${ssrInterpolate(unref(t)("certificateIssued"))}</span>`);
          } else {
            _push(`<!---->`);
          }
          _push(`</div><div class="flex items-center space-x-1">`);
          _push(ssrRenderComponent(_sfc_main$4, {
            href: _ctx.route(
              "admin.schoolEnrollments.edit",
              {
                schoolEnrollment: enrollment.id
              }
            )
          }, null, _parent));
          _push(ssrRenderComponent(_sfc_main$5, {
            title: unref(t)("delete"),
            onDelete: ($event) => handleDelete(enrollment)
          }, null, _parent));
          _push(`</div></div></footer></article>`);
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
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Components/Admin/School/SchoolEnrollment/View/EnrollmentCardGrid.vue");
  return _sfc_setup$2 ? _sfc_setup$2(props, ctx) : void 0;
};
const _sfc_main$1 = {
  __name: "SortSelect",
  __ssrInlineRender: true,
  props: {
    sortParam: {
      type: String,
      default: "idDesc"
    }
  },
  emits: ["update:sortParam"],
  setup(__props, { emit: __emit }) {
    const { t } = useI18n();
    return (_ctx, _push, _parent, _attrs) => {
      _push(`<div${ssrRenderAttrs(mergeProps({ class: "flex justify-center items-center h-fit my-2" }, _attrs))}><label for="sortParam" class="hidden lg:block sm:mr-2 tracking-wider text-sm font-semibold text-slate-600 dark:text-slate-100">${ssrInterpolate(unref(t)("sort"))}</label><select id="sortParam"${ssrRenderAttr("value", __props.sortParam)} class="w-56 px-3 py-0.5 form-select bg-white dark:bg-gray-200 text-gray-600 dark:text-gray-900 border border-slate-400 dark:border-slate-600 rounded-sm shadow-sm"><option value="idDesc">${ssrInterpolate(unref(t)("idDesc"))}</option><option value="idAsc">${ssrInterpolate(unref(t)("idAsc"))}</option><option disabled>──────────────────</option><option value="statusAsc">${ssrInterpolate(unref(t)("status"))} A→Z</option><option value="statusDesc">${ssrInterpolate(unref(t)("status"))} Z→A</option><option value="progressDesc">${ssrInterpolate(unref(t)("progress"))} 9→0</option><option value="progressAsc">${ssrInterpolate(unref(t)("progress"))} 0→9</option><option disabled>──────────────────</option><option value="startedAtDesc">${ssrInterpolate(unref(t)("shortStarted"))} ↓</option><option value="startedAtAsc">${ssrInterpolate(unref(t)("shortStarted"))} ↑</option><option value="expiresAtDesc">${ssrInterpolate(unref(t)("shortExpires"))} ↓</option><option value="expiresAtAsc">${ssrInterpolate(unref(t)("shortExpires"))} ↑</option><option value="completedAtDesc">${ssrInterpolate(unref(t)("shortCompleted"))} ↓</option><option value="completedAtAsc">${ssrInterpolate(unref(t)("shortCompleted"))} ↑</option><option disabled>──────────────────</option><option value="userNameAsc">${ssrInterpolate(unref(t)("user"))} A→Z</option><option value="userNameDesc">${ssrInterpolate(unref(t)("user"))} Z→A</option><option value="userEmailAsc">${ssrInterpolate(unref(t)("email"))} A→Z</option><option value="userEmailDesc">${ssrInterpolate(unref(t)("email"))} Z→A</option><option disabled>──────────────────</option><option value="courseTitleAsc">${ssrInterpolate(unref(t)("course"))} A→Z</option><option value="courseTitleDesc">${ssrInterpolate(unref(t)("course"))} Z→A</option><option value="scheduleTitleAsc">${ssrInterpolate(unref(t)("schedule"))} A→Z</option><option value="scheduleTitleDesc">${ssrInterpolate(unref(t)("schedule"))} Z→A</option><option disabled>──────────────────</option><option value="progressRecordsDesc">${ssrInterpolate(unref(t)("progress"))} 9→0</option><option value="progressRecordsAsc">${ssrInterpolate(unref(t)("progress"))} 0→9</option><option disabled>──────────────────</option><option value="createdAtDesc">${ssrInterpolate(unref(t)("createdAt"))} ↓</option><option value="createdAtAsc">${ssrInterpolate(unref(t)("createdAt"))} ↑</option><option value="updatedAtDesc">${ssrInterpolate(unref(t)("updatedAt"))} ↓</option><option value="updatedAtAsc">${ssrInterpolate(unref(t)("updatedAt"))} ↑</option><option disabled>──────────────────</option></select></div>`);
    };
  }
};
const _sfc_setup$1 = _sfc_main$1.setup;
_sfc_main$1.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Components/Admin/School/SchoolEnrollment/Sort/SortSelect.vue");
  return _sfc_setup$1 ? _sfc_setup$1(props, ctx) : void 0;
};
const _sfc_main = {
  __name: "Index",
  __ssrInlineRender: true,
  props: {
    /**
     * Locale.
     */
    currentLocale: {
      type: String,
      default: ""
    },
    availableLocales: {
      type: Array,
      default: () => []
    },
    /**
     * Processing.
     */
    adminSchoolEnrollmentsProcessingMode: {
      type: String,
      default: "frontend"
    },
    useServerProcessing: {
      type: Boolean,
      default: false
    },
    /**
     * FRONTEND:
     * Array / ResourceCollection.
     *
     * SERVER:
     * paginator object.
     */
    enrollments: {
      type: [Array, Object],
      default: () => []
    },
    enrollmentsCount: {
      type: Number,
      default: 0
    },
    /**
     * Admin settings.
     */
    adminSchoolEnrollmentsPerPage: {
      type: Number,
      default: 10
    },
    adminSchoolEnrollmentsDefaultSort: {
      type: String,
      default: "idDesc"
    },
    /**
     * Query state.
     */
    sortParam: {
      type: String,
      default: ""
    },
    search: {
      type: String,
      default: ""
    },
    filters: {
      type: Object,
      default: () => ({})
    },
    errors: {
      type: Object,
      default: () => ({})
    }
  },
  setup(__props) {
    const { t } = useI18n();
    const toast = useToast();
    const props = __props;
    const viewMode = ref(
      localStorage.getItem(
        "admin_view_mode_enrollments"
      ) || "table"
    );
    watch(viewMode, (value) => {
      localStorage.setItem(
        "admin_view_mode_enrollments",
        value
      );
    });
    const enrollmentsList = computed(() => {
      if (Array.isArray(props.enrollments)) {
        return props.enrollments;
      }
      if (props.enrollments && Array.isArray(
        props.enrollments.data
      )) {
        return props.enrollments.data;
      }
      return [];
    });
    const localEnrollments = ref([]);
    watch(
      enrollmentsList,
      (items) => {
        localEnrollments.value = JSON.parse(
          JSON.stringify(
            items || []
          )
        );
      },
      {
        immediate: true,
        deep: true
      }
    );
    const itemsPerPage = ref(
      props.adminSchoolEnrollmentsPerPage || 10
    );
    watch(
      () => props.adminSchoolEnrollmentsPerPage,
      (value) => {
        if (value && Number(value) !== Number(itemsPerPage.value)) {
          itemsPerPage.value = Number(value);
        }
      }
    );
    watch(itemsPerPage, (value, oldValue) => {
      if (value === oldValue || props.useServerProcessing) {
        return;
      }
      currentPage.value = 1;
      router.put(
        route(
          "admin.settings.updateAdminCountSchoolEnrollments"
        ),
        {
          value
        },
        {
          preserveScroll: true,
          preserveState: true,
          onSuccess: () => {
            toast.info(
              `Показ ${value} элементов на странице.`
            );
          },
          onError: (errors) => {
            toast.error(
              (errors == null ? void 0 : errors.value) || "Ошибка обновления кол-ва элементов."
            );
          }
        }
      );
    });
    const sortParam = ref(
      props.sortParam || props.adminSchoolEnrollmentsDefaultSort || "idDesc"
    );
    watch(
      () => props.sortParam,
      (value) => {
        if (value && value !== sortParam.value) {
          sortParam.value = value;
        }
      }
    );
    watch(sortParam, (value, oldValue) => {
      if (value === oldValue) {
        return;
      }
      currentPage.value = 1;
      router.put(
        route(
          "admin.settings.updateAdminSortSchoolEnrollments"
        ),
        {
          value
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
                  sort: value || void 0,
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
    });
    const searchQuery = ref(
      props.search || ""
    );
    watch(
      () => props.search,
      (value) => {
        const normalized = value || "";
        if (normalized !== searchQuery.value) {
          searchQuery.value = normalized;
        }
      }
    );
    const currentPage = ref(1);
    watch(
      [
        itemsPerPage,
        searchQuery
      ],
      () => {
        currentPage.value = 1;
      }
    );
    const normalize = (value) => {
      return (value ?? "").toString().trim().toLowerCase();
    };
    const safeNumber = (value) => {
      const number = Number(value);
      return Number.isFinite(number) ? number : 0;
    };
    const safeDate = (value) => {
      if (!value) {
        return 0;
      }
      const time = new Date(value).getTime();
      return Number.isFinite(time) ? time : 0;
    };
    const getUserName = (enrollment) => {
      var _a;
      return ((_a = enrollment == null ? void 0 : enrollment.user) == null ? void 0 : _a.name) || "";
    };
    const getUserEmail = (enrollment) => {
      var _a;
      return ((_a = enrollment == null ? void 0 : enrollment.user) == null ? void 0 : _a.email) || "";
    };
    const getCourseTitle = (enrollment) => {
      var _a, _b;
      return ((_b = (_a = enrollment == null ? void 0 : enrollment.course) == null ? void 0 : _a.translation) == null ? void 0 : _b.title) || "";
    };
    const getCourseSlug = (enrollment) => {
      var _a;
      return ((_a = enrollment == null ? void 0 : enrollment.course) == null ? void 0 : _a.slug) || "";
    };
    const getCourseShort = (enrollment) => {
      var _a, _b;
      return ((_b = (_a = enrollment == null ? void 0 : enrollment.course) == null ? void 0 : _a.translation) == null ? void 0 : _b.short) || "";
    };
    const getCourseDescription = (enrollment) => {
      var _a, _b;
      return ((_b = (_a = enrollment == null ? void 0 : enrollment.course) == null ? void 0 : _a.translation) == null ? void 0 : _b.description) || "";
    };
    const getScheduleTitle = (enrollment) => {
      var _a, _b;
      return ((_b = (_a = enrollment == null ? void 0 : enrollment.schedule) == null ? void 0 : _a.translation) == null ? void 0 : _b.title) || "";
    };
    const getScheduleSlug = (enrollment) => {
      var _a;
      return ((_a = enrollment == null ? void 0 : enrollment.schedule) == null ? void 0 : _a.slug) || "";
    };
    const getScheduleSubtitle = (enrollment) => {
      var _a, _b;
      return ((_b = (_a = enrollment == null ? void 0 : enrollment.schedule) == null ? void 0 : _a.translation) == null ? void 0 : _b.subtitle) || "";
    };
    const getScheduleShort = (enrollment) => {
      var _a, _b;
      return ((_b = (_a = enrollment == null ? void 0 : enrollment.schedule) == null ? void 0 : _a.translation) == null ? void 0 : _b.short) || "";
    };
    const getScheduleDescription = (enrollment) => {
      var _a, _b;
      return ((_b = (_a = enrollment == null ? void 0 : enrollment.schedule) == null ? void 0 : _a.translation) == null ? void 0 : _b.description) || "";
    };
    const getScheduleCourseTitle = (enrollment) => {
      var _a, _b, _c;
      return ((_c = (_b = (_a = enrollment == null ? void 0 : enrollment.schedule) == null ? void 0 : _a.course) == null ? void 0 : _b.translation) == null ? void 0 : _c.title) || "";
    };
    const getScheduleCourseShort = (enrollment) => {
      var _a, _b, _c;
      return ((_c = (_b = (_a = enrollment == null ? void 0 : enrollment.schedule) == null ? void 0 : _a.course) == null ? void 0 : _b.translation) == null ? void 0 : _c.short) || "";
    };
    const getOrderNumber = (enrollment) => {
      var _a, _b;
      return ((_a = enrollment == null ? void 0 : enrollment.order) == null ? void 0 : _a.number) || ((_b = enrollment == null ? void 0 : enrollment.order) == null ? void 0 : _b.id) || "";
    };
    const byNumberAsc = (field) => (a, b) => safeNumber(a == null ? void 0 : a[field]) - safeNumber(b == null ? void 0 : b[field]) || safeNumber(a == null ? void 0 : a.id) - safeNumber(b == null ? void 0 : b.id);
    const byNumberDesc = (field) => (a, b) => safeNumber(b == null ? void 0 : b[field]) - safeNumber(a == null ? void 0 : a[field]) || safeNumber(b == null ? void 0 : b.id) - safeNumber(a == null ? void 0 : a.id);
    const byDateAsc = (field) => (a, b) => safeDate(a == null ? void 0 : a[field]) - safeDate(b == null ? void 0 : b[field]) || safeNumber(a == null ? void 0 : a.id) - safeNumber(b == null ? void 0 : b.id);
    const byDateDesc = (field) => (a, b) => safeDate(b == null ? void 0 : b[field]) - safeDate(a == null ? void 0 : a[field]) || safeNumber(b == null ? void 0 : b.id) - safeNumber(a == null ? void 0 : a.id);
    const byStringGetterAsc = (getter) => (a, b) => normalize(
      getter(a)
    ).localeCompare(
      normalize(
        getter(b)
      )
    ) || safeNumber(a == null ? void 0 : a.id) - safeNumber(b == null ? void 0 : b.id);
    const byStringGetterDesc = (getter) => (a, b) => normalize(
      getter(b)
    ).localeCompare(
      normalize(
        getter(a)
      )
    ) || safeNumber(b == null ? void 0 : b.id) - safeNumber(a == null ? void 0 : a.id);
    const sortEnrollments = (items) => {
      const list = (items || []).slice();
      const sortMap = {
        idAsc: byNumberAsc("id"),
        idDesc: byNumberDesc("id"),
        startedAtAsc: byDateAsc(
          "started_at"
        ),
        startedAtDesc: byDateDesc(
          "started_at"
        ),
        expiresAtAsc: byDateAsc(
          "expires_at"
        ),
        expiresAtDesc: byDateDesc(
          "expires_at"
        ),
        completedAtAsc: byDateAsc(
          "completed_at"
        ),
        completedAtDesc: byDateDesc(
          "completed_at"
        ),
        progressAsc: byNumberAsc(
          "progress_percent"
        ),
        progressDesc: byNumberDesc(
          "progress_percent"
        ),
        statusAsc: (a, b) => normalize(
          a == null ? void 0 : a.status
        ).localeCompare(
          normalize(
            b == null ? void 0 : b.status
          )
        ) || safeNumber(a == null ? void 0 : a.id) - safeNumber(b == null ? void 0 : b.id),
        statusDesc: (a, b) => normalize(
          b == null ? void 0 : b.status
        ).localeCompare(
          normalize(
            a == null ? void 0 : a.status
          )
        ) || safeNumber(b == null ? void 0 : b.id) - safeNumber(a == null ? void 0 : a.id),
        userNameAsc: byStringGetterAsc(
          getUserName
        ),
        userNameDesc: byStringGetterDesc(
          getUserName
        ),
        userEmailAsc: byStringGetterAsc(
          getUserEmail
        ),
        userEmailDesc: byStringGetterDesc(
          getUserEmail
        ),
        courseTitleAsc: byStringGetterAsc(
          getCourseTitle
        ),
        courseTitleDesc: byStringGetterDesc(
          getCourseTitle
        ),
        scheduleTitleAsc: byStringGetterAsc(
          getScheduleTitle
        ),
        scheduleTitleDesc: byStringGetterDesc(
          getScheduleTitle
        ),
        progressRecordsAsc: byNumberAsc(
          "progress_records_count"
        ),
        progressRecordsDesc: byNumberDesc(
          "progress_records_count"
        ),
        createdAtAsc: byDateAsc(
          "created_at"
        ),
        createdAtDesc: byDateDesc(
          "created_at"
        ),
        updatedAtAsc: byDateAsc(
          "updated_at"
        ),
        updatedAtDesc: byDateDesc(
          "updated_at"
        )
      };
      const sorter = sortMap[sortParam.value];
      return sorter ? list.sort(sorter) : list;
    };
    const filteredEnrollments = computed(() => {
      const query = normalize(
        searchQuery.value
      );
      let items = localEnrollments.value || [];
      if (query) {
        items = items.filter(
          (enrollment) => {
            const values = [
              /**
               * Enrollment.
               */
              enrollment == null ? void 0 : enrollment.id,
              enrollment == null ? void 0 : enrollment.status,
              enrollment == null ? void 0 : enrollment.notes,
              enrollment == null ? void 0 : enrollment.progress_percent,
              /**
               * User.
               */
              getUserName(enrollment),
              getUserEmail(enrollment),
              /**
               * Course.
               */
              getCourseTitle(enrollment),
              getCourseSlug(enrollment),
              getCourseShort(enrollment),
              getCourseDescription(enrollment),
              /**
               * Schedule.
               */
              getScheduleTitle(enrollment),
              getScheduleSlug(enrollment),
              getScheduleSubtitle(enrollment),
              getScheduleShort(enrollment),
              getScheduleDescription(enrollment),
              /**
               * Course самого Schedule.
               */
              getScheduleCourseTitle(enrollment),
              getScheduleCourseShort(enrollment),
              /**
               * Order.
               */
              getOrderNumber(enrollment)
            ];
            return values.some(
              (value) => normalize(value).includes(query)
            );
          }
        );
      }
      return sortEnrollments(
        items
      );
    });
    const filteredCount = computed(() => {
      return props.useServerProcessing ? Number(
        props.enrollmentsCount || 0
      ) : filteredEnrollments.value.length;
    });
    const paginatedEnrollments = computed(() => {
      const perPage = Math.max(
        1,
        Number(
          itemsPerPage.value || 10
        )
      );
      const totalPages = Math.max(
        1,
        Math.ceil(
          filteredEnrollments.value.length / perPage
        )
      );
      if (currentPage.value > totalPages) {
        currentPage.value = totalPages;
      }
      const start = (currentPage.value - 1) * perPage;
      return filteredEnrollments.value.slice(
        start,
        start + perPage
      );
    });
    const displayedEnrollments = computed(() => {
      return props.useServerProcessing ? enrollmentsList.value : paginatedEnrollments.value;
    });
    const totalCount = computed(() => {
      return Number(
        props.enrollmentsCount || 0
      );
    });
    const showConfirmDeleteModal = ref(false);
    const enrollmentToDelete = ref(null);
    const confirmDelete = (enrollment) => {
      enrollmentToDelete.value = enrollment;
      showConfirmDeleteModal.value = true;
    };
    const closeModal = () => {
      showConfirmDeleteModal.value = false;
      enrollmentToDelete.value = null;
    };
    const deleteEnrollment = () => {
      var _a;
      if (!((_a = enrollmentToDelete.value) == null ? void 0 : _a.id)) {
        return;
      }
      const id = enrollmentToDelete.value.id;
      router.delete(
        route(
          "admin.schoolEnrollments.destroy",
          {
            schoolEnrollment: id
          }
        ),
        {
          preserveScroll: true,
          preserveState: false,
          onSuccess: () => {
            toast.success(
              `Зачисление ID: ${id} удалено.`
            );
          },
          onError: (errors) => {
            const firstKey = Object.keys(
              errors || {}
            )[0];
            const message = (errors == null ? void 0 : errors.general) || (errors == null ? void 0 : errors[firstKey]) || "Ошибка при удалении зачисления.";
            toast.error(
              `${message} ID: ${id}`
            );
          },
          onFinish: () => {
            closeModal();
          }
        }
      );
    };
    return (_ctx, _push, _parent, _attrs) => {
      _push(ssrRenderComponent(AdminLayout, mergeProps({
        title: unref(t)("enrollments")
      }, _attrs), {
        header: withCtx((_, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(ssrRenderComponent(TitlePage, null, {
              default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                if (_push3) {
                  _push3(`${ssrInterpolate(unref(t)("enrollments"))}`);
                } else {
                  return [
                    createTextVNode(toDisplayString(unref(t)("enrollments")), 1)
                  ];
                }
              }),
              _: 1
            }, _parent2, _scopeId));
          } else {
            return [
              createVNode(TitlePage, null, {
                default: withCtx(() => [
                  createTextVNode(toDisplayString(unref(t)("enrollments")), 1)
                ]),
                _: 1
              })
            ];
          }
        }),
        default: withCtx((_, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(`<div class="px-2 py-2 w-full max-w-12xl mx-auto"${_scopeId}><div class="p-4 bg-slate-50 dark:bg-slate-700 border border-blue-400 dark:border-blue-200 overflow-hidden shadow-md shadow-gray-500 dark:shadow-slate-400 bg-opacity-95 dark:bg-opacity-95"${_scopeId}><div class="sm:flex sm:justify-between sm:items-center mb-3 gap-3"${_scopeId}>`);
            _push2(ssrRenderComponent(_sfc_main$6, {
              href: _ctx.route(
                "admin.schoolEnrollments.create"
              )
            }, {
              default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                if (_push3) {
                  _push3(`${ssrInterpolate(unref(t)("addEnrollment"))}`);
                } else {
                  return [
                    createTextVNode(toDisplayString(unref(t)("addEnrollment")), 1)
                  ];
                }
              }),
              _: 1
            }, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$7, {
              "setting-key": "adminSchoolEnrollmentsProcessingMode",
              mode: __props.adminSchoolEnrollmentsProcessingMode,
              "use-server-processing": __props.useServerProcessing,
              total: totalCount.value
            }, null, _parent2, _scopeId));
            _push2(`</div>`);
            if (totalCount.value && !__props.useServerProcessing) {
              _push2(ssrRenderComponent(_sfc_main$8, {
                modelValue: searchQuery.value,
                "onUpdate:modelValue": ($event) => searchQuery.value = $event,
                placeholder: unref(t)("search")
              }, null, _parent2, _scopeId));
            } else {
              _push2(`<!---->`);
            }
            if (totalCount.value && __props.useServerProcessing) {
              _push2(ssrRenderComponent(_sfc_main$9, {
                modelValue: searchQuery.value,
                "onUpdate:modelValue": ($event) => searchQuery.value = $event
              }, null, _parent2, _scopeId));
            } else {
              _push2(`<!---->`);
            }
            if (totalCount.value) {
              _push2(`<div class="flex justify-between items-center flex-col md:flex-row my-3 gap-3"${_scopeId}>`);
              if (!__props.useServerProcessing) {
                _push2(ssrRenderComponent(_sfc_main$a, {
                  "items-per-page": itemsPerPage.value,
                  "onUpdate:itemsPerPage": ($event) => itemsPerPage.value = $event
                }, null, _parent2, _scopeId));
              } else {
                _push2(ssrRenderComponent(_sfc_main$b, {
                  "items-per-page": itemsPerPage.value,
                  "update-route": "admin.settings.updateAdminCountSchoolEnrollments"
                }, null, _parent2, _scopeId));
              }
              _push2(ssrRenderComponent(_sfc_main$1, {
                sortParam: sortParam.value,
                "onUpdate:sortParam": ($event) => sortParam.value = $event
              }, null, _parent2, _scopeId));
              _push2(`</div>`);
            } else {
              _push2(`<!---->`);
            }
            if (totalCount.value) {
              _push2(`<div class="flex justify-between items-center flex-col md:flex-row my-3 gap-3"${_scopeId}>`);
              _push2(ssrRenderComponent(_sfc_main$c, null, {
                default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                  if (_push3) {
                    if (!__props.useServerProcessing && searchQuery.value) {
                      _push3(`<!--[-->${ssrInterpolate(filteredCount.value)} / ${ssrInterpolate(totalCount.value)}<!--]-->`);
                    } else {
                      _push3(`<!--[-->${ssrInterpolate(totalCount.value)}<!--]-->`);
                    }
                  } else {
                    return [
                      !__props.useServerProcessing && searchQuery.value ? (openBlock(), createBlock(Fragment, { key: 0 }, [
                        createTextVNode(toDisplayString(filteredCount.value) + " / " + toDisplayString(totalCount.value), 1)
                      ], 64)) : (openBlock(), createBlock(Fragment, { key: 1 }, [
                        createTextVNode(toDisplayString(totalCount.value), 1)
                      ], 64))
                    ];
                  }
                }),
                _: 1
              }, _parent2, _scopeId));
              _push2(ssrRenderComponent(_sfc_main$d, {
                viewMode: viewMode.value,
                "onUpdate:viewMode": ($event) => viewMode.value = $event
              }, null, _parent2, _scopeId));
              _push2(`</div>`);
            } else {
              _push2(`<!---->`);
            }
            if (totalCount.value) {
              _push2(`<div class="flex justify-center items-center flex-col md:flex-row mb-3"${_scopeId}>`);
              if (!__props.useServerProcessing) {
                _push2(ssrRenderComponent(_sfc_main$e, {
                  "current-page": currentPage.value,
                  "items-per-page": itemsPerPage.value,
                  "total-items": filteredCount.value,
                  "onUpdate:currentPage": ($event) => currentPage.value = $event
                }, null, _parent2, _scopeId));
              } else {
                _push2(ssrRenderComponent(_sfc_main$f, { pagination: __props.enrollments }, null, _parent2, _scopeId));
              }
              _push2(`</div>`);
            } else {
              _push2(`<!---->`);
            }
            if (viewMode.value === "table") {
              _push2(ssrRenderComponent(_sfc_main$3, {
                enrollments: displayedEnrollments.value,
                onDelete: confirmDelete
              }, null, _parent2, _scopeId));
            } else {
              _push2(ssrRenderComponent(_sfc_main$2, {
                enrollments: displayedEnrollments.value,
                onDelete: confirmDelete
              }, null, _parent2, _scopeId));
            }
            if (totalCount.value) {
              _push2(`<div class="flex justify-center items-center flex-col md:flex-row mt-3"${_scopeId}>`);
              if (!__props.useServerProcessing) {
                _push2(ssrRenderComponent(_sfc_main$e, {
                  "current-page": currentPage.value,
                  "items-per-page": itemsPerPage.value,
                  "total-items": filteredCount.value,
                  "onUpdate:currentPage": ($event) => currentPage.value = $event
                }, null, _parent2, _scopeId));
              } else {
                _push2(ssrRenderComponent(_sfc_main$f, { pagination: __props.enrollments }, null, _parent2, _scopeId));
              }
              _push2(`</div>`);
            } else {
              _push2(`<!---->`);
            }
            if (!totalCount.value) {
              _push2(`<div class="py-8 text-center text-sm font-semibold text-slate-500 dark:text-slate-300"${_scopeId}>${ssrInterpolate(unref(t)("noData"))}</div>`);
            } else if (!__props.useServerProcessing && searchQuery.value && filteredCount.value === 0) {
              _push2(`<div class="py-4 text-center text-sm font-semibold text-slate-500 dark:text-slate-300"${_scopeId}>${ssrInterpolate(unref(t)("noData"))}</div>`);
            } else {
              _push2(`<!---->`);
            }
            _push2(`</div></div>`);
            _push2(ssrRenderComponent(_sfc_main$g, {
              show: showConfirmDeleteModal.value,
              onCancel: closeModal,
              onConfirm: deleteEnrollment,
              cancelText: unref(t)("cancel"),
              confirmText: unref(t)("yesDelete"),
              onClose: closeModal
            }, null, _parent2, _scopeId));
          } else {
            return [
              createVNode("div", { class: "px-2 py-2 w-full max-w-12xl mx-auto" }, [
                createVNode("div", { class: "p-4 bg-slate-50 dark:bg-slate-700 border border-blue-400 dark:border-blue-200 overflow-hidden shadow-md shadow-gray-500 dark:shadow-slate-400 bg-opacity-95 dark:bg-opacity-95" }, [
                  createVNode("div", { class: "sm:flex sm:justify-between sm:items-center mb-3 gap-3" }, [
                    createVNode(_sfc_main$6, {
                      href: _ctx.route(
                        "admin.schoolEnrollments.create"
                      )
                    }, {
                      default: withCtx(() => [
                        createTextVNode(toDisplayString(unref(t)("addEnrollment")), 1)
                      ]),
                      _: 1
                    }, 8, ["href"]),
                    createVNode(_sfc_main$7, {
                      "setting-key": "adminSchoolEnrollmentsProcessingMode",
                      mode: __props.adminSchoolEnrollmentsProcessingMode,
                      "use-server-processing": __props.useServerProcessing,
                      total: totalCount.value
                    }, null, 8, ["mode", "use-server-processing", "total"])
                  ]),
                  totalCount.value && !__props.useServerProcessing ? (openBlock(), createBlock(_sfc_main$8, {
                    key: 0,
                    modelValue: searchQuery.value,
                    "onUpdate:modelValue": ($event) => searchQuery.value = $event,
                    placeholder: unref(t)("search")
                  }, null, 8, ["modelValue", "onUpdate:modelValue", "placeholder"])) : createCommentVNode("", true),
                  totalCount.value && __props.useServerProcessing ? (openBlock(), createBlock(_sfc_main$9, {
                    key: 1,
                    modelValue: searchQuery.value,
                    "onUpdate:modelValue": ($event) => searchQuery.value = $event
                  }, null, 8, ["modelValue", "onUpdate:modelValue"])) : createCommentVNode("", true),
                  totalCount.value ? (openBlock(), createBlock("div", {
                    key: 2,
                    class: "flex justify-between items-center flex-col md:flex-row my-3 gap-3"
                  }, [
                    !__props.useServerProcessing ? (openBlock(), createBlock(_sfc_main$a, {
                      key: 0,
                      "items-per-page": itemsPerPage.value,
                      "onUpdate:itemsPerPage": ($event) => itemsPerPage.value = $event
                    }, null, 8, ["items-per-page", "onUpdate:itemsPerPage"])) : (openBlock(), createBlock(_sfc_main$b, {
                      key: 1,
                      "items-per-page": itemsPerPage.value,
                      "update-route": "admin.settings.updateAdminCountSchoolEnrollments"
                    }, null, 8, ["items-per-page"])),
                    createVNode(_sfc_main$1, {
                      sortParam: sortParam.value,
                      "onUpdate:sortParam": ($event) => sortParam.value = $event
                    }, null, 8, ["sortParam", "onUpdate:sortParam"])
                  ])) : createCommentVNode("", true),
                  totalCount.value ? (openBlock(), createBlock("div", {
                    key: 3,
                    class: "flex justify-between items-center flex-col md:flex-row my-3 gap-3"
                  }, [
                    createVNode(_sfc_main$c, null, {
                      default: withCtx(() => [
                        !__props.useServerProcessing && searchQuery.value ? (openBlock(), createBlock(Fragment, { key: 0 }, [
                          createTextVNode(toDisplayString(filteredCount.value) + " / " + toDisplayString(totalCount.value), 1)
                        ], 64)) : (openBlock(), createBlock(Fragment, { key: 1 }, [
                          createTextVNode(toDisplayString(totalCount.value), 1)
                        ], 64))
                      ]),
                      _: 1
                    }),
                    createVNode(_sfc_main$d, {
                      viewMode: viewMode.value,
                      "onUpdate:viewMode": ($event) => viewMode.value = $event
                    }, null, 8, ["viewMode", "onUpdate:viewMode"])
                  ])) : createCommentVNode("", true),
                  totalCount.value ? (openBlock(), createBlock("div", {
                    key: 4,
                    class: "flex justify-center items-center flex-col md:flex-row mb-3"
                  }, [
                    !__props.useServerProcessing ? (openBlock(), createBlock(_sfc_main$e, {
                      key: 0,
                      "current-page": currentPage.value,
                      "items-per-page": itemsPerPage.value,
                      "total-items": filteredCount.value,
                      "onUpdate:currentPage": ($event) => currentPage.value = $event
                    }, null, 8, ["current-page", "items-per-page", "total-items", "onUpdate:currentPage"])) : (openBlock(), createBlock(_sfc_main$f, {
                      key: 1,
                      pagination: __props.enrollments
                    }, null, 8, ["pagination"]))
                  ])) : createCommentVNode("", true),
                  viewMode.value === "table" ? (openBlock(), createBlock(_sfc_main$3, {
                    key: 5,
                    enrollments: displayedEnrollments.value,
                    onDelete: confirmDelete
                  }, null, 8, ["enrollments", "onDelete"])) : (openBlock(), createBlock(_sfc_main$2, {
                    key: 6,
                    enrollments: displayedEnrollments.value,
                    onDelete: confirmDelete
                  }, null, 8, ["enrollments", "onDelete"])),
                  totalCount.value ? (openBlock(), createBlock("div", {
                    key: 7,
                    class: "flex justify-center items-center flex-col md:flex-row mt-3"
                  }, [
                    !__props.useServerProcessing ? (openBlock(), createBlock(_sfc_main$e, {
                      key: 0,
                      "current-page": currentPage.value,
                      "items-per-page": itemsPerPage.value,
                      "total-items": filteredCount.value,
                      "onUpdate:currentPage": ($event) => currentPage.value = $event
                    }, null, 8, ["current-page", "items-per-page", "total-items", "onUpdate:currentPage"])) : (openBlock(), createBlock(_sfc_main$f, {
                      key: 1,
                      pagination: __props.enrollments
                    }, null, 8, ["pagination"]))
                  ])) : createCommentVNode("", true),
                  !totalCount.value ? (openBlock(), createBlock("div", {
                    key: 8,
                    class: "py-8 text-center text-sm font-semibold text-slate-500 dark:text-slate-300"
                  }, toDisplayString(unref(t)("noData")), 1)) : !__props.useServerProcessing && searchQuery.value && filteredCount.value === 0 ? (openBlock(), createBlock("div", {
                    key: 9,
                    class: "py-4 text-center text-sm font-semibold text-slate-500 dark:text-slate-300"
                  }, toDisplayString(unref(t)("noData")), 1)) : createCommentVNode("", true)
                ])
              ]),
              createVNode(_sfc_main$g, {
                show: showConfirmDeleteModal.value,
                onCancel: closeModal,
                onConfirm: deleteEnrollment,
                cancelText: unref(t)("cancel"),
                confirmText: unref(t)("yesDelete"),
                onClose: closeModal
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
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Pages/Admin/School/SchoolEnrollments/Index.vue");
  return _sfc_setup ? _sfc_setup(props, ctx) : void 0;
};
export {
  _sfc_main as default
};
