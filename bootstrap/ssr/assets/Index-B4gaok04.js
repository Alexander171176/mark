import { computed, mergeProps, unref, useSSRContext, ref, watch, withCtx, createBlock, openBlock, Fragment, createTextVNode, toDisplayString, createVNode, createCommentVNode } from "vue";
import { ssrRenderAttrs, ssrInterpolate, ssrRenderAttr, ssrRenderList, ssrRenderClass, ssrRenderComponent } from "vue/server-renderer";
import { router } from "@inertiajs/vue3";
import { useI18n } from "vue-i18n";
import { useToast } from "vue-toastification";
import { A as AdminLayout } from "./AdminLayout-03yd3nxC.js";
import { T as TitlePage } from "./TitlePage-CBZZ8YZB.js";
import { _ as _sfc_main$8, a as _sfc_main$b, b as _sfc_main$c } from "./ItemsPerPageSelect-DOO-E4Z0.js";
import { _ as _sfc_main$7, a as _sfc_main$d } from "./ServerSearchInput-CxQUCmzV.js";
import { _ as _sfc_main$6 } from "./SearchInput-xZSYbbms.js";
import { _ as _sfc_main$a } from "./CountTable-p8tyXGUL.js";
import { _ as _sfc_main$9 } from "./ServerItemsPerPageSelect-B_wSkKlU.js";
import { _ as _sfc_main$5 } from "./ProcessingModeSwitcher-BJvzFf6_.js";
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
  __name: "EditIconButton",
  __ssrInlineRender: true,
  props: {
    title: String
  },
  emits: ["edit"],
  setup(__props, { emit: __emit }) {
    const { t } = useI18n();
    const buttonClass = computed(() => [
      "flex items-center py-2 px-2 rounded",
      "border border-slate-400",
      "hover:border-sky-500",
      "dark:border-sky-300 dark:hover:border-sky-100"
    ]);
    return (_ctx, _push, _parent, _attrs) => {
      _push(`<button${ssrRenderAttrs(mergeProps({
        class: buttonClass.value,
        title: unref(t)("edit")
      }, _attrs))}><svg class="w-3 h-3 fill-current text-sky-500 hover:text-sky-700 dark:text-sky-300 dark:hover:text-sky-100 shrink-0" viewBox="0 0 16 16"><path d="M11.7.3c-.4-.4-1-.4-1.4 0l-10 10c-.2.2-.3.4-.3.7v4c0 .6.4 1 1 1h4c.3 0 .5-.1.7-.3l10-10c.4-.4.4-1 0-1.4l-4-4zM4.6 14H2v-2.6l6-6L10.6 8l-6 6zM12 6.6L9.4 4 11 2.4 13.6 5 12 6.6z"></path></svg></button>`);
    };
  }
};
const _sfc_setup$4 = _sfc_main$4.setup;
_sfc_main$4.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Components/Admin/UI/Buttons/EditIconButton.vue");
  return _sfc_setup$4 ? _sfc_setup$4(props, ctx) : void 0;
};
const _sfc_main$3 = {
  __name: "CohortEnrollmentTable",
  __ssrInlineRender: true,
  props: {
    enrollments: {
      type: Array,
      default: () => []
    }
  },
  setup(__props) {
    const { t } = useI18n();
    const toast = useToast();
    const statusOptions = [
      "pending",
      "approved",
      "rejected",
      "cancelled"
    ];
    const statusLabelKeyMap = {
      pending: "statusSelectPending",
      approved: "statusSelectApproved",
      rejected: "statusSelectRejected",
      cancelled: "statusSelectCancelled"
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
        case "approved":
          return {
            badge: "border-emerald-400 bg-emerald-100 dark:bg-emerald-900/40 text-emerald-800 dark:text-emerald-100"
          };
        case "pending":
          return {
            badge: "border-amber-400 bg-amber-100 dark:bg-amber-900/40 text-amber-800 dark:text-amber-100"
          };
        case "rejected":
          return {
            badge: "border-rose-400 bg-rose-100 dark:bg-rose-900/40 text-rose-800 dark:text-rose-100"
          };
        case "cancelled":
          return {
            badge: "border-slate-400 bg-slate-200 dark:bg-slate-700 text-slate-800 dark:text-slate-100"
          };
        default:
          return {
            badge: "border-blue-400 bg-blue-100 dark:bg-blue-900/40 text-blue-800 dark:text-blue-100"
          };
      }
    };
    const getScheduleTitle = (enrollment) => {
      var _a, _b;
      return ((_b = (_a = enrollment == null ? void 0 : enrollment.schedule) == null ? void 0 : _a.translation) == null ? void 0 : _b.title) || ((enrollment == null ? void 0 : enrollment.school_course_schedule_id) ? `ID: ${enrollment.school_course_schedule_id}` : "—");
    };
    const getScheduleSubtitle = (enrollment) => {
      var _a, _b;
      return ((_b = (_a = enrollment == null ? void 0 : enrollment.schedule) == null ? void 0 : _a.translation) == null ? void 0 : _b.subtitle) || "";
    };
    const getScheduleShort = (enrollment) => {
      var _a, _b;
      return ((_b = (_a = enrollment == null ? void 0 : enrollment.schedule) == null ? void 0 : _a.translation) == null ? void 0 : _b.short) || "";
    };
    const getCourseTitle = (enrollment) => {
      var _a, _b, _c, _d, _e;
      return ((_c = (_b = (_a = enrollment == null ? void 0 : enrollment.schedule) == null ? void 0 : _a.course) == null ? void 0 : _b.translation) == null ? void 0 : _c.title) || (((_e = (_d = enrollment == null ? void 0 : enrollment.schedule) == null ? void 0 : _d.course) == null ? void 0 : _e.id) ? `ID: ${enrollment.schedule.course.id}` : "");
    };
    const getUserName = (enrollment) => {
      var _a;
      return ((_a = enrollment == null ? void 0 : enrollment.user) == null ? void 0 : _a.name) || ((enrollment == null ? void 0 : enrollment.user_id) ? `ID: ${enrollment.user_id}` : "—");
    };
    const getUserEmail = (enrollment) => {
      var _a;
      return ((_a = enrollment == null ? void 0 : enrollment.user) == null ? void 0 : _a.email) || "";
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
    const editNotes = (enrollment) => {
      const current = (enrollment == null ? void 0 : enrollment.notes) || "";
      const updated = window.prompt(
        t("notes"),
        current
      );
      if (updated === null) {
        return;
      }
      router.put(
        route(
          "admin.actions.cohortEnrollments.updateNotes",
          enrollment.id
        ),
        {
          notes: updated
        },
        {
          preserveScroll: true,
          preserveState: true,
          onSuccess: () => {
            toast.success(
              "Заметки обновлены"
            );
          },
          onError: (errors) => {
            toast.error(
              (errors == null ? void 0 : errors.notes) || (errors == null ? void 0 : errors.general) || "Ошибка при обновлении заметок"
            );
          }
        }
      );
    };
    return (_ctx, _push, _parent, _attrs) => {
      _push(`<div${ssrRenderAttrs(mergeProps({ class: "relative rounded-sm border border-slate-200 bg-white shadow-lg dark:border-slate-600 dark:bg-slate-700" }, _attrs))}><div class="overflow-x-auto">`);
      if (__props.enrollments.length) {
        _push(`<table class="table-auto w-full text-slate-700 dark:text-slate-100"><thead class="border border-solid border-gray-300 bg-slate-200 text-sm uppercase dark:border-gray-700 dark:bg-cyan-900"><tr><th class="w-px px-2 py-3"><div class="text-center font-medium">${ssrInterpolate(unref(t)("id"))}</div></th><th class="px-2 py-3 whitespace-nowrap"><div class="flex items-center justify-center gap-2"${ssrRenderAttr("title", unref(t)("learningFlow"))}><svg class="h-4 w-4 fill-current text-cyan-600 dark:text-cyan-400" viewBox="0 0 512 512"><path d="M352 96c0-53.02-42.98-96-96-96s-96 42.98-96 96 42.98 96 96 96 96-42.98 96-96zM233.59 241.1c-59.33-36.32-155.43-46.3-203.79-49.05C13.55 191.13 0 203.51 0 219.14v222.8c0 14.33 11.59 26.28 26.49 27.05 43.66 2.29 131.99 10.68 193.04 41.43 9.37 4.72 20.48-1.71 20.48-11.87V252.56c-.01-4.67-2.32-8.95-6.42-11.46zm248.61-49.05c-48.35 2.74-144.46 12.73-203.78 49.05-4.1 2.51-6.41 6.96-6.41 11.63v245.79c0 10.19 11.14 16.63 20.54 11.9 61.04-30.72 149.32-39.11 192.97-41.4 14.9-.78 26.49-12.73 26.49-27.06V219.14c-.01-15.63-13.56-28.01-29.81-27.09z"></path></svg><span class="font-medium">${ssrInterpolate(unref(t)("learningFlow"))}</span></div></th><th class="px-2 py-3 whitespace-nowrap"><div class="text-center font-medium">${ssrInterpolate(unref(t)("course"))}</div></th><th class="px-2 py-3 whitespace-nowrap"><div class="flex items-center justify-center gap-2"><svg class="h-4 w-4 shrink-0" viewBox="0 0 24 24"><path class="fill-current text-cyan-400" d="M18.974 8H22a2 2 0 012 2v6h-2v5a1 1 0 01-1 1h-2a1 1 0 01-1-1v-5h-2v-6a2 2 0 012-2h.974zM20 7a2 2 0 11-.001-3.999A2 2 0 0120 7zM2.974 8H6a2 2 0 012 2v6H6v5a1 1 0 01-1 1H3a1 1 0 01-1-1v-5H0v-6a2 2 0 012-2h.974zM4 7a2 2 0 11-.001-3.999A2 2 0 014 7z"></path><path class="fill-current text-cyan-600" d="M12 6a3 3 0 110-6 3 3 0 010 6zm2 18h-4a1 1 0 01-1-1v-6H6v-6a3 3 0 013-3h6a3 3 0 013 3v6h-3v6a1 1 0 01-1 1z"></path></svg><span class="font-medium">${ssrInterpolate(unref(t)("users"))}</span></div></th><th class="px-2 py-3 whitespace-nowrap"><div class="text-center font-medium">${ssrInterpolate(unref(t)("status"))}</div></th><th class="px-2 py-3 whitespace-nowrap"><div class="flex justify-center"${ssrRenderAttr("title", unref(t)("enrolledAt"))}><svg class="h-4 w-4 shrink-0 fill-current" viewBox="0 0 448 512"><path d="M400 64h-48V12c0-6.627-5.373-12-12-12h-40c-6.627 0-12 5.373-12 12v52H160V12c0-6.627-5.373-12-12-12h-40c-6.627 0-12 5.373-12 12v52H48C21.49 64 0 85.49 0 112v352c0 26.51 21.49 48 48 48h352c26.51 0 48-21.49 48-48V112c0-26.51-21.49-48-48-48zm-6 400H54a6 6 0 0 1-6-6V160h352v298a6 6 0 0 1-6 6z"></path></svg></div></th><th class="px-2 py-3"><div class="text-left font-medium">${ssrInterpolate(unref(t)("notes"))}</div></th></tr></thead><tbody><!--[-->`);
        ssrRenderList(__props.enrollments, (enrollment) => {
          var _a;
          _push(`<tr class="border-b-2 text-xs font-semibold hover:bg-slate-100 dark:hover:bg-cyan-800"><td class="w-px px-2 py-3"><div class="text-center text-slate-800 dark:text-slate-200">${ssrInterpolate(enrollment.id)}</div></td><td class="px-2 py-3"><div class="text-left text-xs text-amber-600 dark:text-amber-200"${ssrRenderAttr(
            "title",
            getScheduleSubtitle(enrollment) || getScheduleShort(enrollment)
          )}>${ssrInterpolate(getScheduleTitle(enrollment))}</div>`);
          if ((_a = enrollment.schedule) == null ? void 0 : _a.slug) {
            _push(`<div class="mt-0.5 text-[10px] text-slate-500 dark:text-slate-400">${ssrInterpolate(enrollment.schedule.slug)}</div>`);
          } else {
            _push(`<!---->`);
          }
          _push(`</td><td class="px-2 py-3"><div class="text-center text-xs text-blue-700 dark:text-blue-300">${ssrInterpolate(getCourseTitle(enrollment) || "—")}</div></td><td class="px-2 py-3 whitespace-nowrap"><div class="flex flex-col"><span class="text-sm text-slate-700 dark:text-slate-200">${ssrInterpolate(getUserName(enrollment))}</span>`);
          if (getUserEmail(enrollment)) {
            _push(`<span class="text-xs text-slate-500 dark:text-slate-400">${ssrInterpolate(getUserEmail(enrollment))}</span>`);
          } else {
            _push(`<!---->`);
          }
          _push(`</div></td><td class="px-2 py-3 whitespace-nowrap"><div class="flex flex-col items-center justify-center"><span class="${ssrRenderClass([
            "mb-1 inline-flex items-center rounded-sm border px-2 py-0.5 text-[11px] font-semibold",
            getStatusClasses(enrollment.status).badge
          ])}">${ssrInterpolate(getStatusLabel(enrollment.status))}</span><select class="block w-full rounded-sm border border-slate-500 py-0.5 pl-3 pr-7 text-xs focus:border-indigo-500 focus:ring-indigo-500 dark:border-slate-500 dark:bg-slate-800 dark:text-slate-100"${ssrRenderAttr("value", enrollment.status)}><!--[-->`);
          ssrRenderList(statusOptions, (status) => {
            _push(`<option${ssrRenderAttr("value", status)}>${ssrInterpolate(getStatusLabel(status))}</option>`);
          });
          _push(`<!--]--></select></div></td><td class="px-2 py-3 whitespace-nowrap"><div class="text-center text-[10px] text-teal-700 dark:text-teal-300">${ssrInterpolate(formatDateTime(enrollment.enrolled_at))}</div></td><td class="px-2 py-3 max-w-xs"><div class="flex items-center justify-between gap-2"><div class="line-clamp-3 text-xs text-rose-800 dark:text-rose-200"${ssrRenderAttr("title", enrollment.notes || "—")}>${ssrInterpolate(enrollment.notes || "—")}</div>`);
          _push(ssrRenderComponent(_sfc_main$4, {
            class: "shrink-0",
            title: unref(t)("editNotes"),
            onClick: ($event) => editNotes(enrollment)
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
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Components/Admin/School/SchoolCohortEnrollment/Table/CohortEnrollmentTable.vue");
  return _sfc_setup$3 ? _sfc_setup$3(props, ctx) : void 0;
};
const _sfc_main$2 = {
  __name: "CohortEnrollmentCardGrid",
  __ssrInlineRender: true,
  props: {
    enrollments: {
      type: Array,
      default: () => []
    }
  },
  setup(__props) {
    const { t } = useI18n();
    const toast = useToast();
    const statusOptions = [
      "pending",
      "approved",
      "rejected",
      "cancelled"
    ];
    const statusLabelKeyMap = {
      pending: "statusSelectPending",
      approved: "statusSelectApproved",
      rejected: "statusSelectRejected",
      cancelled: "statusSelectCancelled"
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
        case "approved":
          return {
            card: "border-emerald-400 dark:border-emerald-500 bg-emerald-50/70 dark:bg-emerald-900/20",
            badge: "border-emerald-400 bg-emerald-100 dark:bg-emerald-900/40 text-emerald-800 dark:text-emerald-100"
          };
        case "pending":
          return {
            card: "border-amber-400 dark:border-amber-500 bg-amber-50/70 dark:bg-amber-900/20",
            badge: "border-amber-400 bg-amber-100 dark:bg-amber-900/40 text-amber-800 dark:text-amber-100"
          };
        case "rejected":
          return {
            card: "border-rose-400 dark:border-rose-500 bg-rose-50/70 dark:bg-rose-900/20",
            badge: "border-rose-400 bg-rose-100 dark:bg-rose-900/40 text-rose-800 dark:text-rose-100"
          };
        case "cancelled":
          return {
            card: "border-slate-400 dark:border-slate-500 bg-slate-100/70 dark:bg-slate-800/80",
            badge: "border-slate-400 bg-slate-200 dark:bg-slate-700 text-slate-800 dark:text-slate-100"
          };
        default:
          return {
            card: "border-blue-400 dark:border-blue-500 bg-blue-50/70 dark:bg-blue-900/20",
            badge: "border-blue-400 bg-blue-100 dark:bg-blue-900/40 text-blue-800 dark:text-blue-100"
          };
      }
    };
    const getScheduleTitle = (enrollment) => {
      var _a, _b;
      return ((_b = (_a = enrollment == null ? void 0 : enrollment.schedule) == null ? void 0 : _a.translation) == null ? void 0 : _b.title) || ((enrollment == null ? void 0 : enrollment.school_course_schedule_id) ? `ID: ${enrollment.school_course_schedule_id}` : "—");
    };
    const getScheduleSubtitle = (enrollment) => {
      var _a, _b;
      return ((_b = (_a = enrollment == null ? void 0 : enrollment.schedule) == null ? void 0 : _a.translation) == null ? void 0 : _b.subtitle) || "";
    };
    const getScheduleShort = (enrollment) => {
      var _a, _b;
      return ((_b = (_a = enrollment == null ? void 0 : enrollment.schedule) == null ? void 0 : _a.translation) == null ? void 0 : _b.short) || "";
    };
    const getCourseTitle = (enrollment) => {
      var _a, _b, _c, _d, _e;
      return ((_c = (_b = (_a = enrollment == null ? void 0 : enrollment.schedule) == null ? void 0 : _a.course) == null ? void 0 : _b.translation) == null ? void 0 : _c.title) || (((_e = (_d = enrollment == null ? void 0 : enrollment.schedule) == null ? void 0 : _d.course) == null ? void 0 : _e.id) ? `ID: ${enrollment.schedule.course.id}` : "");
    };
    const getUserName = (enrollment) => {
      var _a;
      return ((_a = enrollment == null ? void 0 : enrollment.user) == null ? void 0 : _a.name) || ((enrollment == null ? void 0 : enrollment.user_id) ? `ID: ${enrollment.user_id}` : "—");
    };
    const getUserEmail = (enrollment) => {
      var _a;
      return ((_a = enrollment == null ? void 0 : enrollment.user) == null ? void 0 : _a.email) || "";
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
    const editNotes = (enrollment) => {
      const current = (enrollment == null ? void 0 : enrollment.notes) || "";
      const updated = window.prompt(
        t("notes"),
        current
      );
      if (updated === null) {
        return;
      }
      router.put(
        route(
          "admin.actions.cohortEnrollments.updateNotes",
          enrollment.id
        ),
        {
          notes: updated
        },
        {
          preserveScroll: true,
          preserveState: true,
          onSuccess: () => {
            toast.success(
              "Заметки обновлены"
            );
          },
          onError: (errors) => {
            toast.error(
              (errors == null ? void 0 : errors.notes) || (errors == null ? void 0 : errors.general) || "Ошибка при обновлении заметок"
            );
          }
        }
      );
    };
    return (_ctx, _push, _parent, _attrs) => {
      _push(`<div${ssrRenderAttrs(mergeProps({ class: "relative rounded-sm border border-slate-400 bg-white shadow-lg dark:border-slate-500 dark:bg-slate-700" }, _attrs))}>`);
      if (__props.enrollments.length) {
        _push(`<div class="p-3"><div class="grid grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4"><!--[-->`);
        ssrRenderList(__props.enrollments, (enrollment) => {
          var _a;
          _push(`<article class="${ssrRenderClass([
            "relative flex h-full flex-col rounded-md border shadow-sm transition-shadow duration-150 hover:shadow-md",
            getStatusClasses(enrollment.status).card
          ])}"><header class="flex items-center justify-between border-b border-dashed border-slate-400 px-2 py-1 dark:border-slate-500"><div class="flex w-full items-center justify-between"><div class="rounded-sm border border-gray-400 bg-slate-200 px-1.5 py-0.5 text-[10px] font-semibold text-slate-800 dark:bg-slate-700 dark:text-blue-100"> ID: ${ssrInterpolate(enrollment.id)}</div><div class="${ssrRenderClass([
            "inline-flex items-center rounded-sm border px-2 py-0.5 text-[11px] font-semibold",
            getStatusClasses(enrollment.status).badge
          ])}"${ssrRenderAttr("title", unref(t)("status"))}>${ssrInterpolate(getStatusLabel(enrollment.status))}</div></div></header><div class="flex flex-1 flex-col space-y-2 px-3 py-2"><div class="text-center"><div class="text-[12px] font-semibold text-amber-700 dark:text-amber-200"${ssrRenderAttr(
            "title",
            getScheduleSubtitle(enrollment) || getScheduleShort(enrollment)
          )}>${ssrInterpolate(getScheduleTitle(enrollment))}</div>`);
          if ((_a = enrollment.schedule) == null ? void 0 : _a.slug) {
            _push(`<div class="mt-0.5 text-[9px] text-slate-500 dark:text-slate-400">${ssrInterpolate(enrollment.schedule.slug)}</div>`);
          } else {
            _push(`<!---->`);
          }
          _push(`</div>`);
          if (getCourseTitle(enrollment)) {
            _push(`<div class="text-center"><div class="text-[11px] font-semibold text-blue-700 dark:text-blue-300">${ssrInterpolate(getCourseTitle(enrollment))}</div></div>`);
          } else {
            _push(`<!---->`);
          }
          _push(`<div class="space-y-0.5 text-center"><div class="text-[12px] font-medium text-slate-900 dark:text-slate-50">${ssrInterpolate(getUserName(enrollment))}</div>`);
          if (getUserEmail(enrollment)) {
            _push(`<div class="text-[11px] text-slate-500 dark:text-slate-300">${ssrInterpolate(getUserEmail(enrollment))}</div>`);
          } else {
            _push(`<!---->`);
          }
          _push(`<span class="mt-0.5 text-[10px] text-teal-700 dark:text-teal-300"${ssrRenderAttr("title", unref(t)("enrolledAt"))}>${ssrInterpolate(formatDateTime(enrollment.enrolled_at))}</span></div><div class="mt-1"><div class="line-clamp-3 text-center text-[11px] text-rose-800 dark:text-rose-200"${ssrRenderAttr("title", enrollment.notes || "—")}>${ssrInterpolate(enrollment.notes || "—")}</div></div></div><footer class="border-t border-dashed border-slate-400 px-3 py-2 dark:border-slate-500"><div class="flex items-center justify-between gap-2">`);
          _push(ssrRenderComponent(_sfc_main$4, {
            class: "shrink-0",
            title: unref(t)("editNotes"),
            onClick: ($event) => editNotes(enrollment)
          }, null, _parent));
          _push(`<select class="block w-full rounded-sm border border-slate-500 py-1 pl-3 pr-6 text-xs focus:border-indigo-500 focus:ring-indigo-500 dark:border-slate-500 dark:bg-slate-800 dark:text-slate-100"${ssrRenderAttr("value", enrollment.status)}><!--[-->`);
          ssrRenderList(statusOptions, (status) => {
            _push(`<option${ssrRenderAttr("value", status)}>${ssrInterpolate(getStatusLabel(status))}</option>`);
          });
          _push(`<!--]--></select></div></footer></article>`);
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
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Components/Admin/School/SchoolCohortEnrollment/View/CohortEnrollmentCardGrid.vue");
  return _sfc_setup$2 ? _sfc_setup$2(props, ctx) : void 0;
};
const _sfc_main$1 = {
  __name: "SortSelect",
  __ssrInlineRender: true,
  props: {
    sortParam: String
  },
  emits: ["update:sortParam"],
  setup(__props, { emit: __emit }) {
    const { t } = useI18n();
    return (_ctx, _push, _parent, _attrs) => {
      _push(`<div${ssrRenderAttrs(mergeProps({ class: "flex justify-center items-center h-fit my-2" }, _attrs))}><label for="sortParam" class="hidden lg:block sm:mr-2 tracking-wider text-sm font-semibold text-slate-600 dark:text-slate-100">${ssrInterpolate(unref(t)("sort"))}</label><select id="sortParam"${ssrRenderAttr("value", __props.sortParam)} class="w-52 px-3 py-0.5 form-select bg-white dark:bg-gray-200 text-gray-600 dark:text-gray-900 border border-slate-400 dark:border-slate-600 rounded-sm shadow-sm"><option value="idDesc">${ssrInterpolate(unref(t)("idDesc"))}</option><option value="idAsc">${ssrInterpolate(unref(t)("idAsc"))}</option><option disabled>────────────────</option><option value="scheduleTitleAsc">${ssrInterpolate(unref(t)("sortScheduleTitleAsc"))}</option><option value="scheduleTitleDesc">${ssrInterpolate(unref(t)("sortScheduleTitleDesc"))}</option><option disabled>────────────────</option><option value="statusAsc">${ssrInterpolate(unref(t)("sortStatusAsc"))}</option><option value="statusDesc">${ssrInterpolate(unref(t)("sortStatusDesc"))}</option><option disabled>────────────────</option><option value="enrolledAtDesc">${ssrInterpolate(unref(t)("sortEnrolledAtDesc"))}</option><option value="enrolledAtAsc">${ssrInterpolate(unref(t)("sortEnrolledAtAsc"))}</option><option disabled>────────────────</option><option value="userNameAsc">${ssrInterpolate(unref(t)("sortUserNameAsc"))}</option><option value="userNameDesc">${ssrInterpolate(unref(t)("sortUserNameDesc"))}</option><option value="userEmailAsc">${ssrInterpolate(unref(t)("email"))} A→Z</option><option value="userEmailDesc">${ssrInterpolate(unref(t)("email"))} Z→A</option><option disabled>────────────────</option><option value="createdAtDesc">${ssrInterpolate(unref(t)("createdAt"))} ↓</option><option value="createdAtAsc">${ssrInterpolate(unref(t)("createdAt"))} ↑</option><option value="updatedAtDesc">${ssrInterpolate(unref(t)("updatedAt"))} ↓</option><option value="updatedAtAsc">${ssrInterpolate(unref(t)("updatedAt"))} ↑</option><option disabled>────────────────</option></select></div>`);
    };
  }
};
const _sfc_setup$1 = _sfc_main$1.setup;
_sfc_main$1.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Components/Admin/School/SchoolCohortEnrollment/Sort/SortSelect.vue");
  return _sfc_setup$1 ? _sfc_setup$1(props, ctx) : void 0;
};
const _sfc_main = {
  __name: "Index",
  __ssrInlineRender: true,
  props: {
    /**
     * Текущая locale.
     */
    currentLocale: {
      type: String,
      default: ""
    },
    /**
     * Processing mode.
     */
    adminSchoolCohortEnrollmentsProcessingMode: {
      type: String,
      default: "frontend"
    },
    useServerProcessing: {
      type: Boolean,
      default: false
    },
    /**
     * Может быть:
     *
     * frontend:
     *   Array / ResourceCollection;
     *
     * server:
     *   paginator object.
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
    adminSchoolCohortEnrollmentsPerPage: {
      type: Number,
      default: 10
    },
    adminSchoolCohortEnrollmentsDefaultSort: {
      type: String,
      default: "idDesc"
    },
    /**
     * Текущие query-параметры.
     */
    sortParam: {
      type: String,
      default: ""
    },
    search: {
      type: String,
      default: ""
    },
    /**
     * Реальные серверные фильтры.
     */
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
        "admin_view_mode_cohort_enrollments"
      ) || "table"
    );
    watch(viewMode, (value) => {
      localStorage.setItem(
        "admin_view_mode_cohort_enrollments",
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
      props.adminSchoolCohortEnrollmentsPerPage || 10
    );
    watch(itemsPerPage, (value) => {
      if (props.useServerProcessing) {
        return;
      }
      currentPage.value = 1;
      router.put(
        route(
          "admin.settings.updateAdminCountSchoolCohortEnrollments"
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
      props.sortParam || props.adminSchoolCohortEnrollmentsDefaultSort || "idDesc"
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
          "admin.settings.updateAdminSortSchoolCohortEnrollments"
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
    const getScheduleTitle = (enrollment) => {
      var _a, _b;
      return ((_b = (_a = enrollment == null ? void 0 : enrollment.schedule) == null ? void 0 : _a.translation) == null ? void 0 : _b.title) || "";
    };
    const getScheduleDescription = (enrollment) => {
      var _a, _b;
      return ((_b = (_a = enrollment == null ? void 0 : enrollment.schedule) == null ? void 0 : _a.translation) == null ? void 0 : _b.description) || "";
    };
    const getCourseTitle = (enrollment) => {
      var _a, _b, _c;
      return ((_c = (_b = (_a = enrollment == null ? void 0 : enrollment.schedule) == null ? void 0 : _a.course) == null ? void 0 : _b.translation) == null ? void 0 : _c.title) || "";
    };
    const getCourseShort = (enrollment) => {
      var _a, _b, _c;
      return ((_c = (_b = (_a = enrollment == null ? void 0 : enrollment.schedule) == null ? void 0 : _a.course) == null ? void 0 : _b.translation) == null ? void 0 : _c.short) || "";
    };
    const getCourseDescription = (enrollment) => {
      var _a, _b, _c;
      return ((_c = (_b = (_a = enrollment == null ? void 0 : enrollment.schedule) == null ? void 0 : _a.course) == null ? void 0 : _b.translation) == null ? void 0 : _c.description) || "";
    };
    const byNumberAsc = (field) => (a, b) => safeNumber(a == null ? void 0 : a[field]) - safeNumber(b == null ? void 0 : b[field]) || safeNumber(a == null ? void 0 : a.id) - safeNumber(b == null ? void 0 : b.id);
    const byNumberDesc = (field) => (a, b) => safeNumber(b == null ? void 0 : b[field]) - safeNumber(a == null ? void 0 : a[field]) || safeNumber(b == null ? void 0 : b.id) - safeNumber(a == null ? void 0 : a.id);
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
    const byDateAsc = (field) => (a, b) => safeDate(a == null ? void 0 : a[field]) - safeDate(b == null ? void 0 : b[field]) || safeNumber(a == null ? void 0 : a.id) - safeNumber(b == null ? void 0 : b.id);
    const byDateDesc = (field) => (a, b) => safeDate(b == null ? void 0 : b[field]) - safeDate(a == null ? void 0 : a[field]) || safeNumber(b == null ? void 0 : b.id) - safeNumber(a == null ? void 0 : a.id);
    const sortEnrollments = (items) => {
      const list = (items || []).slice();
      const sortMap = {
        idAsc: byNumberAsc("id"),
        idDesc: byNumberDesc("id"),
        enrolledAtAsc: byDateAsc(
          "enrolled_at"
        ),
        enrolledAtDesc: byDateDesc(
          "enrolled_at"
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
        scheduleTitleAsc: byStringGetterAsc(
          getScheduleTitle
        ),
        scheduleTitleDesc: byStringGetterDesc(
          getScheduleTitle
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
            const status = normalize(
              enrollment == null ? void 0 : enrollment.status
            );
            const notes = normalize(
              enrollment == null ? void 0 : enrollment.notes
            );
            const userName = normalize(
              getUserName(
                enrollment
              )
            );
            const userEmail = normalize(
              getUserEmail(
                enrollment
              )
            );
            const scheduleTitle = normalize(
              getScheduleTitle(
                enrollment
              )
            );
            const scheduleDescription = normalize(
              getScheduleDescription(
                enrollment
              )
            );
            const courseTitle = normalize(
              getCourseTitle(
                enrollment
              )
            );
            const courseShort = normalize(
              getCourseShort(
                enrollment
              )
            );
            const courseDescription = normalize(
              getCourseDescription(
                enrollment
              )
            );
            return status.includes(query) || notes.includes(query) || userName.includes(query) || userEmail.includes(query) || scheduleTitle.includes(query) || scheduleDescription.includes(query) || courseTitle.includes(query) || courseShort.includes(query) || courseDescription.includes(query);
          }
        );
      }
      return sortEnrollments(
        items
      );
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
        props.enrollmentsCount ?? 0
      );
    });
    const filteredCount = computed(() => {
      return props.useServerProcessing ? totalCount.value : filteredEnrollments.value.length;
    });
    return (_ctx, _push, _parent, _attrs) => {
      _push(ssrRenderComponent(AdminLayout, mergeProps({
        title: unref(t)("cohortEnrollments")
      }, _attrs), {
        header: withCtx((_, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(ssrRenderComponent(TitlePage, null, {
              default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                if (_push3) {
                  _push3(`${ssrInterpolate(unref(t)("cohortEnrollments"))}`);
                } else {
                  return [
                    createTextVNode(toDisplayString(unref(t)("cohortEnrollments")), 1)
                  ];
                }
              }),
              _: 1
            }, _parent2, _scopeId));
          } else {
            return [
              createVNode(TitlePage, null, {
                default: withCtx(() => [
                  createTextVNode(toDisplayString(unref(t)("cohortEnrollments")), 1)
                ]),
                _: 1
              })
            ];
          }
        }),
        default: withCtx((_, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(`<div class="px-2 py-2 w-full max-w-12xl mx-auto"${_scopeId}><div class="p-4 bg-slate-50 dark:bg-slate-700 border border-blue-400 dark:border-blue-200 overflow-hidden shadow-md shadow-gray-500 dark:shadow-slate-400 bg-opacity-95 dark:bg-opacity-95"${_scopeId}><div class="sm:flex sm:justify-end sm:items-center mb-3 gap-3"${_scopeId}>`);
            _push2(ssrRenderComponent(_sfc_main$5, {
              "setting-key": "adminSchoolCohortEnrollmentsProcessingMode",
              mode: __props.adminSchoolCohortEnrollmentsProcessingMode,
              "use-server-processing": __props.useServerProcessing,
              total: totalCount.value
            }, null, _parent2, _scopeId));
            _push2(`</div>`);
            if (totalCount.value && !__props.useServerProcessing) {
              _push2(ssrRenderComponent(_sfc_main$6, {
                modelValue: searchQuery.value,
                "onUpdate:modelValue": ($event) => searchQuery.value = $event,
                placeholder: unref(t)("search")
              }, null, _parent2, _scopeId));
            } else {
              _push2(`<!---->`);
            }
            if (totalCount.value && __props.useServerProcessing) {
              _push2(ssrRenderComponent(_sfc_main$7, {
                modelValue: searchQuery.value,
                "onUpdate:modelValue": ($event) => searchQuery.value = $event
              }, null, _parent2, _scopeId));
            } else {
              _push2(`<!---->`);
            }
            if (totalCount.value) {
              _push2(`<div class="flex justify-between items-center flex-col md:flex-row my-3 gap-3"${_scopeId}>`);
              if (!__props.useServerProcessing) {
                _push2(ssrRenderComponent(_sfc_main$8, {
                  "items-per-page": itemsPerPage.value,
                  "onUpdate:itemsPerPage": ($event) => itemsPerPage.value = $event
                }, null, _parent2, _scopeId));
              } else {
                _push2(ssrRenderComponent(_sfc_main$9, {
                  "items-per-page": itemsPerPage.value,
                  "update-route": "admin.settings.updateAdminCountSchoolCohortEnrollments"
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
              _push2(ssrRenderComponent(_sfc_main$a, null, {
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
              _push2(ssrRenderComponent(_sfc_main$b, {
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
                _push2(ssrRenderComponent(_sfc_main$c, {
                  "current-page": currentPage.value,
                  "items-per-page": itemsPerPage.value,
                  "total-items": filteredCount.value,
                  "onUpdate:currentPage": ($event) => currentPage.value = $event
                }, null, _parent2, _scopeId));
              } else {
                _push2(ssrRenderComponent(_sfc_main$d, { pagination: __props.enrollments }, null, _parent2, _scopeId));
              }
              _push2(`</div>`);
            } else {
              _push2(`<!---->`);
            }
            if (viewMode.value === "table") {
              _push2(ssrRenderComponent(_sfc_main$3, {
                enrollments: displayedEnrollments.value
              }, null, _parent2, _scopeId));
            } else {
              _push2(ssrRenderComponent(_sfc_main$2, {
                enrollments: displayedEnrollments.value
              }, null, _parent2, _scopeId));
            }
            if (totalCount.value) {
              _push2(`<div class="flex justify-center items-center flex-col md:flex-row mt-3"${_scopeId}>`);
              if (!__props.useServerProcessing) {
                _push2(ssrRenderComponent(_sfc_main$c, {
                  "current-page": currentPage.value,
                  "items-per-page": itemsPerPage.value,
                  "total-items": filteredCount.value,
                  "onUpdate:currentPage": ($event) => currentPage.value = $event
                }, null, _parent2, _scopeId));
              } else {
                _push2(ssrRenderComponent(_sfc_main$d, { pagination: __props.enrollments }, null, _parent2, _scopeId));
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
          } else {
            return [
              createVNode("div", { class: "px-2 py-2 w-full max-w-12xl mx-auto" }, [
                createVNode("div", { class: "p-4 bg-slate-50 dark:bg-slate-700 border border-blue-400 dark:border-blue-200 overflow-hidden shadow-md shadow-gray-500 dark:shadow-slate-400 bg-opacity-95 dark:bg-opacity-95" }, [
                  createVNode("div", { class: "sm:flex sm:justify-end sm:items-center mb-3 gap-3" }, [
                    createVNode(_sfc_main$5, {
                      "setting-key": "adminSchoolCohortEnrollmentsProcessingMode",
                      mode: __props.adminSchoolCohortEnrollmentsProcessingMode,
                      "use-server-processing": __props.useServerProcessing,
                      total: totalCount.value
                    }, null, 8, ["mode", "use-server-processing", "total"])
                  ]),
                  totalCount.value && !__props.useServerProcessing ? (openBlock(), createBlock(_sfc_main$6, {
                    key: 0,
                    modelValue: searchQuery.value,
                    "onUpdate:modelValue": ($event) => searchQuery.value = $event,
                    placeholder: unref(t)("search")
                  }, null, 8, ["modelValue", "onUpdate:modelValue", "placeholder"])) : createCommentVNode("", true),
                  totalCount.value && __props.useServerProcessing ? (openBlock(), createBlock(_sfc_main$7, {
                    key: 1,
                    modelValue: searchQuery.value,
                    "onUpdate:modelValue": ($event) => searchQuery.value = $event
                  }, null, 8, ["modelValue", "onUpdate:modelValue"])) : createCommentVNode("", true),
                  totalCount.value ? (openBlock(), createBlock("div", {
                    key: 2,
                    class: "flex justify-between items-center flex-col md:flex-row my-3 gap-3"
                  }, [
                    !__props.useServerProcessing ? (openBlock(), createBlock(_sfc_main$8, {
                      key: 0,
                      "items-per-page": itemsPerPage.value,
                      "onUpdate:itemsPerPage": ($event) => itemsPerPage.value = $event
                    }, null, 8, ["items-per-page", "onUpdate:itemsPerPage"])) : (openBlock(), createBlock(_sfc_main$9, {
                      key: 1,
                      "items-per-page": itemsPerPage.value,
                      "update-route": "admin.settings.updateAdminCountSchoolCohortEnrollments"
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
                    createVNode(_sfc_main$a, null, {
                      default: withCtx(() => [
                        !__props.useServerProcessing && searchQuery.value ? (openBlock(), createBlock(Fragment, { key: 0 }, [
                          createTextVNode(toDisplayString(filteredCount.value) + " / " + toDisplayString(totalCount.value), 1)
                        ], 64)) : (openBlock(), createBlock(Fragment, { key: 1 }, [
                          createTextVNode(toDisplayString(totalCount.value), 1)
                        ], 64))
                      ]),
                      _: 1
                    }),
                    createVNode(_sfc_main$b, {
                      viewMode: viewMode.value,
                      "onUpdate:viewMode": ($event) => viewMode.value = $event
                    }, null, 8, ["viewMode", "onUpdate:viewMode"])
                  ])) : createCommentVNode("", true),
                  totalCount.value ? (openBlock(), createBlock("div", {
                    key: 4,
                    class: "flex justify-center items-center flex-col md:flex-row mb-3"
                  }, [
                    !__props.useServerProcessing ? (openBlock(), createBlock(_sfc_main$c, {
                      key: 0,
                      "current-page": currentPage.value,
                      "items-per-page": itemsPerPage.value,
                      "total-items": filteredCount.value,
                      "onUpdate:currentPage": ($event) => currentPage.value = $event
                    }, null, 8, ["current-page", "items-per-page", "total-items", "onUpdate:currentPage"])) : (openBlock(), createBlock(_sfc_main$d, {
                      key: 1,
                      pagination: __props.enrollments
                    }, null, 8, ["pagination"]))
                  ])) : createCommentVNode("", true),
                  viewMode.value === "table" ? (openBlock(), createBlock(_sfc_main$3, {
                    key: 5,
                    enrollments: displayedEnrollments.value
                  }, null, 8, ["enrollments"])) : (openBlock(), createBlock(_sfc_main$2, {
                    key: 6,
                    enrollments: displayedEnrollments.value
                  }, null, 8, ["enrollments"])),
                  totalCount.value ? (openBlock(), createBlock("div", {
                    key: 7,
                    class: "flex justify-center items-center flex-col md:flex-row mt-3"
                  }, [
                    !__props.useServerProcessing ? (openBlock(), createBlock(_sfc_main$c, {
                      key: 0,
                      "current-page": currentPage.value,
                      "items-per-page": itemsPerPage.value,
                      "total-items": filteredCount.value,
                      "onUpdate:currentPage": ($event) => currentPage.value = $event
                    }, null, 8, ["current-page", "items-per-page", "total-items", "onUpdate:currentPage"])) : (openBlock(), createBlock(_sfc_main$d, {
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
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Pages/Admin/School/SchoolCohortEnrollments/Index.vue");
  return _sfc_setup ? _sfc_setup(props, ctx) : void 0;
};
export {
  _sfc_main as default
};
