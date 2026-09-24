import { mergeProps, unref, useSSRContext, ref, watch, withCtx, createVNode, createBlock, openBlock, toDisplayString, createCommentVNode, Fragment, computed, createTextVNode } from "vue";
import { ssrRenderAttrs, ssrInterpolate, ssrRenderAttr, ssrRenderComponent, ssrRenderClass, ssrIncludeBooleanAttr } from "vue/server-renderer";
import { router } from "@inertiajs/vue3";
import { useI18n } from "vue-i18n";
import { useToast } from "vue-toastification";
import { A as AdminLayout } from "./AdminLayout-03yd3nxC.js";
import { T as TitlePage } from "./TitlePage-CBZZ8YZB.js";
import { _ as _sfc_main$b } from "./SearchInput-xZSYbbms.js";
import { _ as _sfc_main$c, a as _sfc_main$i } from "./ServerSearchInput-CxQUCmzV.js";
import { _ as _sfc_main$9 } from "./DefaultButton-DMDyGacf.js";
import { _ as _sfc_main$d, a as _sfc_main$g, b as _sfc_main$h } from "./ItemsPerPageSelect-DOO-E4Z0.js";
import { _ as _sfc_main$f } from "./CountTable-p8tyXGUL.js";
import { _ as _sfc_main$e } from "./ServerItemsPerPageSelect-B_wSkKlU.js";
import { _ as _sfc_main$j } from "./DangerModal-DjIe5VDg.js";
import { _ as _sfc_main$a } from "./ProcessingModeSwitcher-BJvzFf6_.js";
import draggable from "vuedraggable";
import { _ as _sfc_main$7 } from "./IconEdit-Bw90OQvk.js";
import { _ as _sfc_main$8 } from "./DeleteIconButton-DLv2Mr1x.js";
import { _ as _sfc_main$5 } from "./ActivityToggle-B1-nFMYK.js";
import { _ as _sfc_main$6 } from "./CloneIconButton-BfVfDOWt.js";
import "./LocaleSelectOption-BeLdazeX.js";
import "./ResponsiveNavLink-gtte0z5g.js";
import "./_plugin-vue_export-helper-1tPrXgE0.js";
import "./ThemeToggle-DA16u1ft.js";
import "@vueuse/core";
import "./ScrollButtons-2xyFJfJ4.js";
import "@fortawesome/vue-fontawesome";
import "@fortawesome/fontawesome-svg-core";
import "@fortawesome/free-solid-svg-icons";
import "@inertiajs/inertia";
import "vue-smooth-dnd";
const _sfc_main$4 = {
  __name: "BulkActionSelect",
  __ssrInlineRender: true,
  emits: ["change"],
  setup(__props, { emit: __emit }) {
    const { t } = useI18n();
    return (_ctx, _push, _parent, _attrs) => {
      _push(`<div${ssrRenderAttrs(mergeProps({ class: "flex flex-col sm:flex-row items-center" }, _attrs))}><label class="block mb-2 sm:mb-0 sm:mr-2 font-semibold text-sm text-slate-700 dark:text-slate-300">${ssrInterpolate(unref(t)("bulkActions"))}</label><select class="w-auto px-3 py-0.5 form-select rounded-sm shadow-sm bg-white dark:bg-gray-200 text-gray-600 dark:text-gray-900 border border-slate-400 dark:border-slate-600"><option value=""> — ${ssrInterpolate(unref(t)("selectAction"))} — </option><option value="selectAll">${ssrInterpolate(unref(t)("selectAll"))}</option><option value="deselectAll">${ssrInterpolate(unref(t)("deselectAll"))}</option><option disabled>──────────────────</option><option value="activate">${ssrInterpolate(unref(t)("activate"))}</option><option value="deactivate">${ssrInterpolate(unref(t)("deactivate"))}</option><option disabled>──────────────────</option></select></div>`);
    };
  }
};
const _sfc_setup$4 = _sfc_main$4.setup;
_sfc_main$4.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Components/Admin/School/SchoolCourseSchedule/Select/BulkActionSelect.vue");
  return _sfc_setup$4 ? _sfc_setup$4(props, ctx) : void 0;
};
const _sfc_main$3 = {
  __name: "SortSelect",
  __ssrInlineRender: true,
  props: {
    sortParam: String
  },
  emits: ["update:sortParam"],
  setup(__props, { emit: __emit }) {
    const { t } = useI18n();
    return (_ctx, _push, _parent, _attrs) => {
      _push(`<div${ssrRenderAttrs(mergeProps({ class: "flex justify-center items-center h-fit my-2" }, _attrs))}><label for="sortParam" class="hidden lg:block sm:mr-2 tracking-wider text-sm font-semibold text-slate-600 dark:text-slate-100">${ssrInterpolate(unref(t)("sort"))}</label><select id="sortParam"${ssrRenderAttr("value", __props.sortParam)} class="w-50 px-3 py-0.5 form-select bg-white dark:bg-gray-200 text-gray-600 dark:text-gray-900 border border-slate-400 dark:border-slate-600 rounded-sm shadow-sm"><option value="idDesc">${ssrInterpolate(unref(t)("idDesc"))}</option><option value="idAsc">${ssrInterpolate(unref(t)("idAsc"))}</option><option disabled>──────────────────</option><option value="sortAsc">${ssrInterpolate(unref(t)("sortNumber"))} 0→9</option><option value="sortDesc">${ssrInterpolate(unref(t)("sortNumber"))} 9→0</option><option disabled>──────────────────</option><option value="titleAsc">${ssrInterpolate(unref(t)("title"))} A→Z</option><option value="titleDesc">${ssrInterpolate(unref(t)("title"))} Z→A</option><option value="slugAsc">Slug A→Z</option><option value="slugDesc">Slug Z→A</option><option disabled>──────────────────</option><option value="activityDesc">${ssrInterpolate(unref(t)("activity"))} ON→OFF</option><option value="activityAsc">${ssrInterpolate(unref(t)("activity"))} OFF→ON</option><option value="activity">${ssrInterpolate(unref(t)("active"))}</option><option value="inactive">${ssrInterpolate(unref(t)("inactive"))}</option><option disabled>──────────────────</option><option value="online">${ssrInterpolate(unref(t)("online"))}</option><option value="offline">${ssrInterpolate(unref(t)("offline"))}</option><option value="onlineDesc">${ssrInterpolate(unref(t)("online"))} ON→OFF</option><option value="onlineAsc">${ssrInterpolate(unref(t)("online"))} OFF→ON</option><option disabled>──────────────────</option><option value="statusAsc">${ssrInterpolate(unref(t)("status"))} A→Z</option><option value="statusDesc">${ssrInterpolate(unref(t)("status"))} Z→A</option><option value="timezoneAsc">${ssrInterpolate(unref(t)("timezone"))} A→Z</option><option value="timezoneDesc">${ssrInterpolate(unref(t)("timezone"))} Z→A</option><option value="locationAsc">${ssrInterpolate(unref(t)("location"))} A→Z</option><option value="locationDesc">${ssrInterpolate(unref(t)("location"))} Z→A</option><option disabled>──────────────────</option><option value="capacityDesc">${ssrInterpolate(unref(t)("capacity"))} 9→0</option><option value="capacityAsc">${ssrInterpolate(unref(t)("capacity"))} 0→9</option><option value="viewsDesc">${ssrInterpolate(unref(t)("views"))} 9→0</option><option value="viewsAsc">${ssrInterpolate(unref(t)("views"))} 0→9</option><option value="imagesDesc">${ssrInterpolate(unref(t)("images"))} 9→0</option><option value="imagesAsc">${ssrInterpolate(unref(t)("images"))} 0→9</option><option value="cohortEnrollmentsDesc">${ssrInterpolate(unref(t)("enrollments"))} 9→0</option><option value="cohortEnrollmentsAsc">${ssrInterpolate(unref(t)("enrollments"))} 0→9</option><option disabled>──────────────────</option><option value="startsAtDesc">${ssrInterpolate(unref(t)("scheduleStartsAt"))} ↓</option><option value="startsAtAsc">${ssrInterpolate(unref(t)("scheduleStartsAt"))} ↑</option><option value="endsAtDesc">${ssrInterpolate(unref(t)("scheduleEndsAt"))} ↓</option><option value="endsAtAsc">${ssrInterpolate(unref(t)("scheduleEndsAt"))} ↑</option><option disabled>──────────────────</option><option value="enrollStartsAtDesc">${ssrInterpolate(unref(t)("scheduleEnrollStartsAt"))} ↓</option><option value="enrollStartsAtAsc">${ssrInterpolate(unref(t)("scheduleEnrollStartsAt"))} ↑</option><option value="enrollEndsAtDesc">${ssrInterpolate(unref(t)("scheduleEnrollEndsAt"))} ↓</option><option value="enrollEndsAtAsc">${ssrInterpolate(unref(t)("scheduleEnrollEndsAt"))} ↑</option><option disabled>──────────────────</option><option value="createdAtDesc">${ssrInterpolate(unref(t)("createdAt"))} ↓</option><option value="createdAtAsc">${ssrInterpolate(unref(t)("createdAt"))} ↑</option><option value="updatedAtDesc">${ssrInterpolate(unref(t)("updatedAt"))} ↓</option><option value="updatedAtAsc">${ssrInterpolate(unref(t)("updatedAt"))} ↑</option><option disabled>──────────────────</option></select></div>`);
    };
  }
};
const _sfc_setup$3 = _sfc_main$3.setup;
_sfc_main$3.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Components/Admin/School/SchoolCourseSchedule/Sort/SortSelect.vue");
  return _sfc_setup$3 ? _sfc_setup$3(props, ctx) : void 0;
};
const _sfc_main$2 = {
  __name: "CourseScheduleTable",
  __ssrInlineRender: true,
  props: {
    schedules: {
      type: Array,
      default: () => []
    },
    selectedSchedules: {
      type: Array,
      default: () => []
    }
  },
  emits: [
    "toggle-activity",
    "delete",
    "update-sort-order",
    "toggle-select",
    "toggle-all",
    "clone"
  ],
  setup(__props, { emit: __emit }) {
    const { t } = useI18n();
    const props = __props;
    const emit = __emit;
    const localSchedules = ref([]);
    watch(
      () => props.schedules,
      (schedules) => {
        localSchedules.value = JSON.parse(
          JSON.stringify(
            schedules || []
          )
        );
      },
      {
        immediate: true,
        deep: true
      }
    );
    const handleDragEnd = () => {
      emit(
        "update-sort-order",
        localSchedules.value.map(
          (schedule) => schedule.id
        )
      );
    };
    const getScheduleTitle = (schedule) => {
      var _a;
      return ((_a = schedule == null ? void 0 : schedule.translation) == null ? void 0 : _a.title) || `ID: ${schedule == null ? void 0 : schedule.id}`;
    };
    const getScheduleSubtitle = (schedule) => {
      var _a;
      return ((_a = schedule == null ? void 0 : schedule.translation) == null ? void 0 : _a.subtitle) || "";
    };
    const getScheduleShort = (schedule) => {
      var _a;
      return ((_a = schedule == null ? void 0 : schedule.translation) == null ? void 0 : _a.short) || "";
    };
    const getCourseTitle = (schedule) => {
      var _a, _b, _c;
      return ((_b = (_a = schedule == null ? void 0 : schedule.course) == null ? void 0 : _a.translation) == null ? void 0 : _b.title) || (((_c = schedule == null ? void 0 : schedule.course) == null ? void 0 : _c.id) ? `ID: ${schedule.course.id}` : "");
    };
    const getInstructorTitle = (schedule) => {
      var _a, _b;
      return ((_b = (_a = schedule == null ? void 0 : schedule.instructor) == null ? void 0 : _a.translation) == null ? void 0 : _b.title) || "";
    };
    const getInstructorUserLabel = (schedule) => {
      var _a, _b, _c, _d;
      return ((_b = (_a = schedule == null ? void 0 : schedule.instructor) == null ? void 0 : _a.user) == null ? void 0 : _b.name) || ((_d = (_c = schedule == null ? void 0 : schedule.instructor) == null ? void 0 : _c.user) == null ? void 0 : _d.email) || "";
    };
    const scheduleStatusLabelKeyMap = {
      draft: "statusDraft",
      published: "statusPublished",
      archived: "statusArchived",
      cancelled: "statusCancelled"
    };
    const getScheduleStatusLabel = (status) => {
      if (!status) {
        return "—";
      }
      const key = scheduleStatusLabelKeyMap[status];
      return key ? t(key) : status;
    };
    const getScheduleOnlineLabel = (isOnline) => {
      return t(
        isOnline ? "online" : "offline"
      );
    };
    const formatDate = (dateStr) => {
      if (!dateStr) {
        return "";
      }
      const date = new Date(dateStr);
      if (Number.isNaN(
        date.getTime()
      )) {
        return "";
      }
      return date.toLocaleDateString(
        "ru-RU",
        {
          year: "numeric",
          month: "long",
          day: "numeric"
        }
      );
    };
    return (_ctx, _push, _parent, _attrs) => {
      _push(`<div${ssrRenderAttrs(mergeProps({ class: "relative rounded-sm border border-slate-200 bg-white shadow-lg dark:border-slate-600 dark:bg-slate-700" }, _attrs))}><div class="flex items-center justify-between border-b border-slate-400 px-3 py-2 dark:border-slate-500"><div class="text-xs text-slate-600 dark:text-slate-200">${ssrInterpolate(unref(t)("selected"))}: ${ssrInterpolate(__props.selectedSchedules.length)}</div>`);
      if (localSchedules.value.length) {
        _push(`<label class="flex cursor-pointer items-center text-xs text-slate-600 dark:text-slate-200"><span>${ssrInterpolate(unref(t)("selectAll"))}</span><input type="checkbox" class="mx-2"></label>`);
      } else {
        _push(`<!---->`);
      }
      _push(`</div><div class="overflow-x-auto">`);
      if (localSchedules.value.length) {
        _push(`<table class="table-auto w-full text-slate-700 dark:text-slate-100"><thead class="border border-solid border-gray-300 bg-slate-200 text-sm uppercase dark:border-gray-700 dark:bg-cyan-900"><tr><th class="w-px px-2 py-3"><svg class="h-4 w-4 fill-current text-slate-800 dark:text-slate-200" viewBox="0 0 24 24"><path d="M12.707,2.293a1,1,0,0,0-1.414,0l-5,5A1,1,0,0,0,7.707,8.707L12,4.414l4.293,4.293a1,1,0,0,0,1.414-1.414Z"></path><path d="M16.293,15.293,12,19.586,7.707,15.293a1,1,0,0,0-1.414,1.414l5,5a1,1,0,0,0,1.414,0l5-5a1,1,0,0,0-1.414-1.414Z"></path></svg></th><th class="w-px px-2 py-3"><div class="text-center font-medium">${ssrInterpolate(unref(t)("id"))}</div></th><th class="px-2 py-3 whitespace-nowrap"><div class="flex justify-center"${ssrRenderAttr("title", unref(t)("image"))}><svg class="h-6 w-6 shrink-0 fill-current" viewBox="0 0 512 512"><path d="M0 96C0 60.7 28.7 32 64 32l384 0c35.3 0 64 28.7 64 64l0 320c0 35.3-28.7 64-64 64L64 480c-35.3 0-64-28.7-64-64L0 96zM323.8 202.5c-4.5-6.6-11.9-10.5-19.8-10.5s-15.4 3.9-19.8 10.5l-87 127.6L170.7 297c-4.6-5.7-11.5-9-18.7-9s-14.2 3.3-18.7 9l-64 80c-5.8 7.2-6.9 17.1-2.9 25.4s12.4 13.6 21.6 13.6l96 0 32 0 208 0c8.9 0 17.1-4.9 21.2-12.8s3.6-17.4-1.4-24.7l-120-176zM112 192a48 48 0 1 0 0-96 48 48 0 1 0 0 96z"></path></svg></div></th><th class="px-2 py-3 whitespace-nowrap"><div class="text-left font-semibold">${ssrInterpolate(unref(t)("title"))}</div></th><th class="px-2 py-3 whitespace-nowrap"><div class="text-center font-semibold">${ssrInterpolate(unref(t)("course"))} / ${ssrInterpolate(unref(t)("instructor"))}</div></th><th class="px-2 py-3 whitespace-nowrap"><div class="flex items-center justify-center"${ssrRenderAttr("title", unref(t)("scheduleStartsAt"))}><svg class="h-4 w-4 shrink-0 fill-current" viewBox="0 0 448 512"><path d="M148 288h-40c-6.6 0-12-5.4-12-12v-40c0-6.6 5.4-12 12-12h40c6.6 0 12 5.4 12 12v40c0 6.6-5.4 12-12 12zm108-12v-40c0-6.6-5.4-12-12-12h-40c-6.6 0-12 5.4-12 12v40c0 6.6 5.4 12 12 12h40c6.6 0 12-5.4 12-12zm96 0v-40c0-6.6-5.4-12-12-12h-40c-6.6 0-12 5.4-12 12v40c0 6.6 5.4 12 12 12h40c6.6 0 12-5.4 12-12zm-96 96v-40c0-6.6-5.4-12-12-12h-40c-6.6 0-12 5.4-12 12v40c0 6.6 5.4 12 12 12h40c6.6 0 12-5.4 12-12zm-96 0v-40c0-6.6-5.4-12-12-12h-40c-6.6 0-12 5.4-12 12v40c0 6.6 5.4 12 12 12h40c6.6 0 12-5.4 12-12zm192 0v-40c0-6.6-5.4-12-12-12h-40c-6.6 0-12 5.4-12 12v40c0 6.6 5.4 12 12 12h40c6.6 0 12-5.4 12-12zm96-260v352c0 26.5-21.5 48-48 48H48c-26.5 0-48-21.5-48-48V112c0-26.5 21.5-48 48-48h48V12c0-6.6 5.4-12 12-12h40c6.6 0 12 5.4 12 12v52h128V12c0-6.6 5.4-12 12-12h40c6.6 0 12 5.4 12 12v52h48c26.5 0 48 21.5 48 48zm-48 346V160H48v298c0 3.3 2.7 6 6 6h340c3.3 0 6-2.7 6-6z"></path></svg></div></th><th class="px-2 py-3 whitespace-nowrap"><div class="flex items-center justify-center"${ssrRenderAttr("title", unref(t)("scheduleEnrollStartsAt"))}>${ssrInterpolate(unref(t)("enrollments"))}</div></th><th class="px-2 py-3 whitespace-nowrap"><div class="text-center font-semibold">${ssrInterpolate(unref(t)("status"))}</div></th><th class="px-2 py-3 whitespace-nowrap"><div class="flex justify-center"${ssrRenderAttr("title", unref(t)("views"))}><svg class="h-4 w-4 shrink-0 fill-current" viewBox="0 0 16 16"><path class="fill-current text-blue-600 dark:text-blue-300" d="M8 2C3.246 2 .251 7.29.127 7.515a.998.998 0 0 0 .002.975c.07.125 1.044 1.801 2.695 3.274C4.738 13.582 6.283 14 8 14c4.706 0 7.743-5.284 7.872-5.507a1 1 0 0 0 0-.98A13.292 13.292 0 0 0 8 2zm0 10a4 4 0 1 1 0-8 4 4 0 0 1 0 8zm0-6a2 2 0 1 0 0 4 2 2 0 0 0 0-4z"></path></svg></div></th><th class="px-2 py-3 whitespace-nowrap"><div class="text-end font-semibold">${ssrInterpolate(unref(t)("actions"))}</div></th><th class="px-2 py-3 whitespace-nowrap"><div class="text-center"><input type="checkbox"></div></th></tr></thead>`);
        _push(ssrRenderComponent(unref(draggable), {
          modelValue: localSchedules.value,
          "onUpdate:modelValue": ($event) => localSchedules.value = $event,
          tag: "tbody",
          "item-key": "id",
          handle: ".handle",
          onEnd: handleDragEnd
        }, {
          item: withCtx(({ element: schedule }, _push2, _parent2, _scopeId) => {
            if (_push2) {
              _push2(`<tr class="border-b-2 text-sm font-semibold hover:bg-slate-100 dark:hover:bg-cyan-800"${_scopeId}><td class="handle cursor-move px-2 py-1 text-center"${_scopeId}><svg class="h-4 w-4 text-gray-500 dark:text-gray-300" fill="currentColor" viewBox="0 0 20 20"${_scopeId}><path d="M7 4h2v2H7V4zm4 0h2v2h-2V4zM7 8h2v2H7V8zm4 0h2v2h-2V8zM7 12h2v2H7v-2zm4 0h2v2h-2v-2z"${_scopeId}></path></svg></td><td class="px-2 py-3 whitespace-nowrap"${_scopeId}><div class="text-center text-xs text-slate-800 dark:text-blue-200"${ssrRenderAttr("title", `[sort: ${schedule.sort}]`)}${_scopeId}>${ssrInterpolate(schedule.id)}</div></td><td class="px-2 py-3 whitespace-nowrap"${_scopeId}><div class="flex justify-center"${_scopeId}>`);
              if (schedule.thumbnail_url) {
                _push2(`<img${ssrRenderAttr("src", schedule.thumbnail_url)}${ssrRenderAttr("alt", getScheduleTitle(schedule))}${ssrRenderAttr("title", getScheduleTitle(schedule))} class="h-8 w-12 rounded-sm object-cover"${_scopeId}>`);
              } else {
                _push2(`<img src="/storage/school/school_course_schedule_images/default-image.png"${ssrRenderAttr("alt", unref(t)("defaultImageTitle"))} class="h-8 w-12 rounded-sm object-cover"${_scopeId}>`);
              }
              _push2(`</div></td><td class="px-2 py-3"${_scopeId}><a${ssrRenderAttr("href", `/school/schedules/${encodeURIComponent(schedule.slug)}`)} target="_blank" rel="noopener noreferrer" class="text-xs text-amber-600 hover:text-red-600 hover:underline dark:text-amber-200 dark:hover:text-red-300"${ssrRenderAttr(
                "title",
                getScheduleSubtitle(schedule) || getScheduleShort(schedule)
              )}${_scopeId}>${ssrInterpolate(getScheduleTitle(schedule))}</a><div class="truncate text-xs text-slate-500 dark:text-slate-300"${ssrRenderAttr("title", schedule.slug)}${_scopeId}>${ssrInterpolate(schedule.slug)}</div>`);
              if (schedule.location) {
                _push2(`<div class="truncate text-[10px] text-teal-700 dark:text-teal-300"${ssrRenderAttr("title", schedule.location)}${_scopeId}>${ssrInterpolate(schedule.location)}</div>`);
              } else {
                _push2(`<!---->`);
              }
              if (schedule.timezone) {
                _push2(`<div class="text-[9px] text-slate-500 dark:text-slate-400"${_scopeId}>${ssrInterpolate(schedule.timezone)}</div>`);
              } else {
                _push2(`<!---->`);
              }
              _push2(`</td><td class="px-2 py-3"${_scopeId}><div class="text-center text-xs text-blue-700 dark:text-blue-300"${ssrRenderAttr("title", getCourseTitle(schedule))}${_scopeId}>${ssrInterpolate(getCourseTitle(schedule) || "—")}</div><div class="mt-1 text-center text-xs"${_scopeId}><span class="text-slate-700 dark:text-slate-300"${_scopeId}>${ssrInterpolate(getInstructorUserLabel(schedule) || "—")}</span>`);
              if (getInstructorTitle(schedule)) {
                _push2(`<!--[--><br${_scopeId}><span class="text-teal-600 dark:text-teal-300"${_scopeId}>${ssrInterpolate(getInstructorTitle(schedule))}</span><!--]-->`);
              } else {
                _push2(`<!---->`);
              }
              _push2(`</div></td><td class="px-2 py-3"${_scopeId}><div class="text-center text-[10px]"${_scopeId}>`);
              if (schedule.starts_at) {
                _push2(`<span class="text-sky-600 dark:text-sky-400"${_scopeId}>${ssrInterpolate(formatDate(schedule.starts_at))}</span>`);
              } else {
                _push2(`<!---->`);
              }
              if (schedule.starts_at && schedule.ends_at) {
                _push2(`<span${_scopeId}> / </span>`);
              } else {
                _push2(`<!---->`);
              }
              if (schedule.ends_at) {
                _push2(`<br${_scopeId}>`);
              } else {
                _push2(`<!---->`);
              }
              if (schedule.ends_at) {
                _push2(`<span class="text-red-400 dark:text-red-200"${_scopeId}>${ssrInterpolate(formatDate(schedule.ends_at))}</span>`);
              } else {
                _push2(`<!---->`);
              }
              if (!schedule.starts_at && !schedule.ends_at) {
                _push2(`<span${_scopeId}> — </span>`);
              } else {
                _push2(`<!---->`);
              }
              _push2(`</div><div class="text-center text-[10px] text-emerald-500 dark:text-emerald-300"${_scopeId}>${ssrInterpolate(getScheduleOnlineLabel(schedule.is_online))}</div>`);
              if (schedule.meeting_url) {
                _push2(`<a${ssrRenderAttr("href", schedule.meeting_url)} target="_blank" rel="noopener noreferrer" class="block max-w-40 truncate text-center text-[8px] text-sky-600 hover:underline dark:text-sky-300"${ssrRenderAttr("title", schedule.meeting_url)}${_scopeId}>${ssrInterpolate(schedule.meeting_url)}</a>`);
              } else {
                _push2(`<!---->`);
              }
              _push2(`</td><td class="px-2 py-3"${_scopeId}><div class="text-center text-[10px]"${_scopeId}>`);
              if (schedule.enroll_starts_at) {
                _push2(`<span class="text-blue-700 dark:text-blue-300"${_scopeId}>${ssrInterpolate(formatDate(schedule.enroll_starts_at))}</span>`);
              } else {
                _push2(`<!---->`);
              }
              if (schedule.enroll_starts_at && schedule.enroll_ends_at) {
                _push2(`<span${_scopeId}> / </span>`);
              } else {
                _push2(`<!---->`);
              }
              if (schedule.enroll_ends_at) {
                _push2(`<br${_scopeId}>`);
              } else {
                _push2(`<!---->`);
              }
              if (schedule.enroll_ends_at) {
                _push2(`<span class="text-rose-600 dark:text-rose-400"${_scopeId}>${ssrInterpolate(formatDate(schedule.enroll_ends_at))}</span>`);
              } else {
                _push2(`<!---->`);
              }
              if (!schedule.enroll_starts_at && !schedule.enroll_ends_at) {
                _push2(`<span${_scopeId}> — </span>`);
              } else {
                _push2(`<!---->`);
              }
              _push2(`</div><div class="${ssrRenderClass([
                schedule.is_enrollment_open ? "text-emerald-600 dark:text-emerald-300" : "text-slate-500 dark:text-slate-400",
                "mt-1 text-center text-[10px]"
              ])}"${_scopeId}>${ssrInterpolate(schedule.is_enrollment_open ? unref(t)("enabled") : unref(t)("disabled"))}</div></td><td class="px-2 py-3 whitespace-nowrap"${_scopeId}><div class="text-center text-[10px] text-fuchsia-800 dark:text-fuchsia-400"${ssrRenderAttr(
                "title",
                `${unref(t)("status")}: ${getScheduleStatusLabel(schedule.status)}`
              )}${_scopeId}>${ssrInterpolate(getScheduleStatusLabel(schedule.status))}</div><div class="text-center text-xs text-amber-600 dark:text-amber-400"${ssrRenderAttr("title", unref(t)("capacity"))}${_scopeId}>${ssrInterpolate(schedule.capacity ?? 0)}</div><div class="text-center text-[10px] text-slate-500 dark:text-slate-300"${_scopeId}>${ssrInterpolate(unref(t)("enrollments"))}: ${ssrInterpolate(schedule.cohort_enrollments_count ?? 0)}</div><div class="text-center text-[10px] text-slate-500 dark:text-slate-300"${_scopeId}>${ssrInterpolate(unref(t)("images"))}: ${ssrInterpolate(schedule.images_count ?? 0)}</div></td><td class="px-2 py-3 whitespace-nowrap"${_scopeId}><div class="text-center text-xs text-blue-700 dark:text-blue-300"${_scopeId}>${ssrInterpolate(schedule.views ?? 0)}</div></td><td class="px-2 py-3 whitespace-nowrap"${_scopeId}><div class="flex justify-end gap-2"${_scopeId}>`);
              _push2(ssrRenderComponent(_sfc_main$5, {
                isActive: schedule.activity,
                title: schedule.activity ? unref(t)("enabled") : unref(t)("disabled"),
                onToggleActivity: ($event) => emit("toggle-activity", schedule)
              }, null, _parent2, _scopeId));
              _push2(ssrRenderComponent(_sfc_main$6, {
                title: unref(t)("clone"),
                onClone: ($event) => emit("clone", schedule)
              }, null, _parent2, _scopeId));
              _push2(ssrRenderComponent(_sfc_main$7, {
                href: _ctx.route(
                  "admin.schoolCourseSchedules.edit",
                  {
                    schoolCourseSchedule: schedule.id
                  }
                )
              }, null, _parent2, _scopeId));
              _push2(ssrRenderComponent(_sfc_main$8, {
                onDelete: ($event) => emit("delete", schedule)
              }, null, _parent2, _scopeId));
              _push2(`</div></td><td class="px-2 py-3 whitespace-nowrap"${_scopeId}><div class="text-center"${_scopeId}><input type="checkbox"${ssrIncludeBooleanAttr(
                __props.selectedSchedules.includes(
                  schedule.id
                )
              ) ? " checked" : ""}${_scopeId}></div></td></tr>`);
            } else {
              return [
                createVNode("tr", { class: "border-b-2 text-sm font-semibold hover:bg-slate-100 dark:hover:bg-cyan-800" }, [
                  createVNode("td", { class: "handle cursor-move px-2 py-1 text-center" }, [
                    (openBlock(), createBlock("svg", {
                      class: "h-4 w-4 text-gray-500 dark:text-gray-300",
                      fill: "currentColor",
                      viewBox: "0 0 20 20"
                    }, [
                      createVNode("path", { d: "M7 4h2v2H7V4zm4 0h2v2h-2V4zM7 8h2v2H7V8zm4 0h2v2h-2V8zM7 12h2v2H7v-2zm4 0h2v2h-2v-2z" })
                    ]))
                  ]),
                  createVNode("td", { class: "px-2 py-3 whitespace-nowrap" }, [
                    createVNode("div", {
                      class: "text-center text-xs text-slate-800 dark:text-blue-200",
                      title: `[sort: ${schedule.sort}]`
                    }, toDisplayString(schedule.id), 9, ["title"])
                  ]),
                  createVNode("td", { class: "px-2 py-3 whitespace-nowrap" }, [
                    createVNode("div", { class: "flex justify-center" }, [
                      schedule.thumbnail_url ? (openBlock(), createBlock("img", {
                        key: 0,
                        src: schedule.thumbnail_url,
                        alt: getScheduleTitle(schedule),
                        title: getScheduleTitle(schedule),
                        class: "h-8 w-12 rounded-sm object-cover"
                      }, null, 8, ["src", "alt", "title"])) : (openBlock(), createBlock("img", {
                        key: 1,
                        src: "/storage/school/school_course_schedule_images/default-image.png",
                        alt: unref(t)("defaultImageTitle"),
                        class: "h-8 w-12 rounded-sm object-cover"
                      }, null, 8, ["alt"]))
                    ])
                  ]),
                  createVNode("td", { class: "px-2 py-3" }, [
                    createVNode("a", {
                      href: `/school/schedules/${encodeURIComponent(schedule.slug)}`,
                      target: "_blank",
                      rel: "noopener noreferrer",
                      class: "text-xs text-amber-600 hover:text-red-600 hover:underline dark:text-amber-200 dark:hover:text-red-300",
                      title: getScheduleSubtitle(schedule) || getScheduleShort(schedule)
                    }, toDisplayString(getScheduleTitle(schedule)), 9, ["href", "title"]),
                    createVNode("div", {
                      class: "truncate text-xs text-slate-500 dark:text-slate-300",
                      title: schedule.slug
                    }, toDisplayString(schedule.slug), 9, ["title"]),
                    schedule.location ? (openBlock(), createBlock("div", {
                      key: 0,
                      class: "truncate text-[10px] text-teal-700 dark:text-teal-300",
                      title: schedule.location
                    }, toDisplayString(schedule.location), 9, ["title"])) : createCommentVNode("", true),
                    schedule.timezone ? (openBlock(), createBlock("div", {
                      key: 1,
                      class: "text-[9px] text-slate-500 dark:text-slate-400"
                    }, toDisplayString(schedule.timezone), 1)) : createCommentVNode("", true)
                  ]),
                  createVNode("td", { class: "px-2 py-3" }, [
                    createVNode("div", {
                      class: "text-center text-xs text-blue-700 dark:text-blue-300",
                      title: getCourseTitle(schedule)
                    }, toDisplayString(getCourseTitle(schedule) || "—"), 9, ["title"]),
                    createVNode("div", { class: "mt-1 text-center text-xs" }, [
                      createVNode("span", { class: "text-slate-700 dark:text-slate-300" }, toDisplayString(getInstructorUserLabel(schedule) || "—"), 1),
                      getInstructorTitle(schedule) ? (openBlock(), createBlock(Fragment, { key: 0 }, [
                        createVNode("br"),
                        createVNode("span", { class: "text-teal-600 dark:text-teal-300" }, toDisplayString(getInstructorTitle(schedule)), 1)
                      ], 64)) : createCommentVNode("", true)
                    ])
                  ]),
                  createVNode("td", { class: "px-2 py-3" }, [
                    createVNode("div", { class: "text-center text-[10px]" }, [
                      schedule.starts_at ? (openBlock(), createBlock("span", {
                        key: 0,
                        class: "text-sky-600 dark:text-sky-400"
                      }, toDisplayString(formatDate(schedule.starts_at)), 1)) : createCommentVNode("", true),
                      schedule.starts_at && schedule.ends_at ? (openBlock(), createBlock("span", { key: 1 }, " / ")) : createCommentVNode("", true),
                      schedule.ends_at ? (openBlock(), createBlock("br", { key: 2 })) : createCommentVNode("", true),
                      schedule.ends_at ? (openBlock(), createBlock("span", {
                        key: 3,
                        class: "text-red-400 dark:text-red-200"
                      }, toDisplayString(formatDate(schedule.ends_at)), 1)) : createCommentVNode("", true),
                      !schedule.starts_at && !schedule.ends_at ? (openBlock(), createBlock("span", { key: 4 }, " — ")) : createCommentVNode("", true)
                    ]),
                    createVNode("div", { class: "text-center text-[10px] text-emerald-500 dark:text-emerald-300" }, toDisplayString(getScheduleOnlineLabel(schedule.is_online)), 1),
                    schedule.meeting_url ? (openBlock(), createBlock("a", {
                      key: 0,
                      href: schedule.meeting_url,
                      target: "_blank",
                      rel: "noopener noreferrer",
                      class: "block max-w-40 truncate text-center text-[8px] text-sky-600 hover:underline dark:text-sky-300",
                      title: schedule.meeting_url
                    }, toDisplayString(schedule.meeting_url), 9, ["href", "title"])) : createCommentVNode("", true)
                  ]),
                  createVNode("td", { class: "px-2 py-3" }, [
                    createVNode("div", { class: "text-center text-[10px]" }, [
                      schedule.enroll_starts_at ? (openBlock(), createBlock("span", {
                        key: 0,
                        class: "text-blue-700 dark:text-blue-300"
                      }, toDisplayString(formatDate(schedule.enroll_starts_at)), 1)) : createCommentVNode("", true),
                      schedule.enroll_starts_at && schedule.enroll_ends_at ? (openBlock(), createBlock("span", { key: 1 }, " / ")) : createCommentVNode("", true),
                      schedule.enroll_ends_at ? (openBlock(), createBlock("br", { key: 2 })) : createCommentVNode("", true),
                      schedule.enroll_ends_at ? (openBlock(), createBlock("span", {
                        key: 3,
                        class: "text-rose-600 dark:text-rose-400"
                      }, toDisplayString(formatDate(schedule.enroll_ends_at)), 1)) : createCommentVNode("", true),
                      !schedule.enroll_starts_at && !schedule.enroll_ends_at ? (openBlock(), createBlock("span", { key: 4 }, " — ")) : createCommentVNode("", true)
                    ]),
                    createVNode("div", {
                      class: [
                        "mt-1 text-center text-[10px]",
                        schedule.is_enrollment_open ? "text-emerald-600 dark:text-emerald-300" : "text-slate-500 dark:text-slate-400"
                      ]
                    }, toDisplayString(schedule.is_enrollment_open ? unref(t)("enabled") : unref(t)("disabled")), 3)
                  ]),
                  createVNode("td", { class: "px-2 py-3 whitespace-nowrap" }, [
                    createVNode("div", {
                      class: "text-center text-[10px] text-fuchsia-800 dark:text-fuchsia-400",
                      title: `${unref(t)("status")}: ${getScheduleStatusLabel(schedule.status)}`
                    }, toDisplayString(getScheduleStatusLabel(schedule.status)), 9, ["title"]),
                    createVNode("div", {
                      class: "text-center text-xs text-amber-600 dark:text-amber-400",
                      title: unref(t)("capacity")
                    }, toDisplayString(schedule.capacity ?? 0), 9, ["title"]),
                    createVNode("div", { class: "text-center text-[10px] text-slate-500 dark:text-slate-300" }, toDisplayString(unref(t)("enrollments")) + ": " + toDisplayString(schedule.cohort_enrollments_count ?? 0), 1),
                    createVNode("div", { class: "text-center text-[10px] text-slate-500 dark:text-slate-300" }, toDisplayString(unref(t)("images")) + ": " + toDisplayString(schedule.images_count ?? 0), 1)
                  ]),
                  createVNode("td", { class: "px-2 py-3 whitespace-nowrap" }, [
                    createVNode("div", { class: "text-center text-xs text-blue-700 dark:text-blue-300" }, toDisplayString(schedule.views ?? 0), 1)
                  ]),
                  createVNode("td", { class: "px-2 py-3 whitespace-nowrap" }, [
                    createVNode("div", { class: "flex justify-end gap-2" }, [
                      createVNode(_sfc_main$5, {
                        isActive: schedule.activity,
                        title: schedule.activity ? unref(t)("enabled") : unref(t)("disabled"),
                        onToggleActivity: ($event) => emit("toggle-activity", schedule)
                      }, null, 8, ["isActive", "title", "onToggleActivity"]),
                      createVNode(_sfc_main$6, {
                        title: unref(t)("clone"),
                        onClone: ($event) => emit("clone", schedule)
                      }, null, 8, ["title", "onClone"]),
                      createVNode(_sfc_main$7, {
                        href: _ctx.route(
                          "admin.schoolCourseSchedules.edit",
                          {
                            schoolCourseSchedule: schedule.id
                          }
                        )
                      }, null, 8, ["href"]),
                      createVNode(_sfc_main$8, {
                        onDelete: ($event) => emit("delete", schedule)
                      }, null, 8, ["onDelete"])
                    ])
                  ]),
                  createVNode("td", { class: "px-2 py-3 whitespace-nowrap" }, [
                    createVNode("div", { class: "text-center" }, [
                      createVNode("input", {
                        type: "checkbox",
                        checked: __props.selectedSchedules.includes(
                          schedule.id
                        ),
                        onChange: ($event) => emit(
                          "toggle-select",
                          schedule.id
                        )
                      }, null, 40, ["checked", "onChange"])
                    ])
                  ])
                ])
              ];
            }
          }),
          _: 1
        }, _parent));
        _push(`</table>`);
      } else {
        _push(`<div class="p-5 text-center text-slate-700 dark:text-slate-100">${ssrInterpolate(unref(t)("noData"))}</div>`);
      }
      _push(`</div></div>`);
    };
  }
};
const _sfc_setup$2 = _sfc_main$2.setup;
_sfc_main$2.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Components/Admin/School/SchoolCourseSchedule/Table/CourseScheduleTable.vue");
  return _sfc_setup$2 ? _sfc_setup$2(props, ctx) : void 0;
};
const _sfc_main$1 = {
  __name: "CourseScheduleCardGrid",
  __ssrInlineRender: true,
  props: {
    schedules: {
      type: Array,
      default: () => []
    },
    selectedSchedules: {
      type: Array,
      default: () => []
    }
  },
  emits: [
    "toggle-activity",
    "delete",
    "update-sort-order",
    "toggle-select",
    "toggle-all",
    "clone"
  ],
  setup(__props, { emit: __emit }) {
    const { t } = useI18n();
    const props = __props;
    const emit = __emit;
    const localSchedules = ref([]);
    watch(
      () => props.schedules,
      (schedules) => {
        localSchedules.value = JSON.parse(
          JSON.stringify(
            schedules || []
          )
        );
      },
      {
        immediate: true,
        deep: true
      }
    );
    const handleDragEnd = () => {
      emit(
        "update-sort-order",
        localSchedules.value.map(
          (schedule) => schedule.id
        )
      );
    };
    const getScheduleTitle = (schedule) => {
      var _a;
      return ((_a = schedule == null ? void 0 : schedule.translation) == null ? void 0 : _a.title) || `ID: ${schedule == null ? void 0 : schedule.id}`;
    };
    const getScheduleSubtitle = (schedule) => {
      var _a;
      return ((_a = schedule == null ? void 0 : schedule.translation) == null ? void 0 : _a.subtitle) || "";
    };
    const getScheduleShort = (schedule) => {
      var _a;
      return ((_a = schedule == null ? void 0 : schedule.translation) == null ? void 0 : _a.short) || "";
    };
    const getCourseTitle = (schedule) => {
      var _a, _b, _c;
      return ((_b = (_a = schedule == null ? void 0 : schedule.course) == null ? void 0 : _a.translation) == null ? void 0 : _b.title) || (((_c = schedule == null ? void 0 : schedule.course) == null ? void 0 : _c.id) ? `ID: ${schedule.course.id}` : "");
    };
    const getInstructorTitle = (schedule) => {
      var _a, _b;
      return ((_b = (_a = schedule == null ? void 0 : schedule.instructor) == null ? void 0 : _a.translation) == null ? void 0 : _b.title) || "";
    };
    const getInstructorUserLabel = (schedule) => {
      var _a, _b, _c, _d;
      return ((_b = (_a = schedule == null ? void 0 : schedule.instructor) == null ? void 0 : _a.user) == null ? void 0 : _b.name) || ((_d = (_c = schedule == null ? void 0 : schedule.instructor) == null ? void 0 : _c.user) == null ? void 0 : _d.email) || "";
    };
    const scheduleStatusLabelKeyMap = {
      draft: "statusDraft",
      published: "statusPublished",
      archived: "statusArchived",
      cancelled: "statusCancelled"
    };
    const getScheduleStatusLabel = (status) => {
      if (!status) {
        return "—";
      }
      const key = scheduleStatusLabelKeyMap[status];
      return key ? t(key) : status;
    };
    const getScheduleOnlineLabel = (isOnline) => {
      return t(
        isOnline ? "online" : "offline"
      );
    };
    const formatDate = (dateStr) => {
      if (!dateStr) {
        return "";
      }
      const date = new Date(dateStr);
      if (Number.isNaN(
        date.getTime()
      )) {
        return "";
      }
      return date.toLocaleDateString(
        "ru-RU",
        {
          year: "numeric",
          month: "short",
          day: "numeric"
        }
      );
    };
    return (_ctx, _push, _parent, _attrs) => {
      _push(`<div${ssrRenderAttrs(mergeProps({ class: "relative rounded-sm border border-slate-400 bg-white shadow-lg dark:border-slate-500 dark:bg-slate-700" }, _attrs))}><div class="flex items-center justify-between border-b border-slate-400 px-3 py-2 dark:border-slate-500"><div class="text-xs text-slate-600 dark:text-slate-200">${ssrInterpolate(unref(t)("selected"))}: ${ssrInterpolate(__props.selectedSchedules.length)}</div>`);
      if (localSchedules.value.length) {
        _push(`<label class="flex cursor-pointer items-center text-xs text-slate-600 dark:text-slate-200"><span>${ssrInterpolate(unref(t)("selectAll"))}</span><input type="checkbox" class="mx-2"></label>`);
      } else {
        _push(`<!---->`);
      }
      _push(`</div>`);
      if (localSchedules.value.length) {
        _push(`<div class="p-3">`);
        _push(ssrRenderComponent(unref(draggable), {
          modelValue: localSchedules.value,
          "onUpdate:modelValue": ($event) => localSchedules.value = $event,
          tag: "div",
          "item-key": "id",
          handle: ".drag-handle",
          class: "grid grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4",
          onEnd: handleDragEnd
        }, {
          item: withCtx(({ element: schedule }, _push2, _parent2, _scopeId) => {
            if (_push2) {
              _push2(`<div class="relative flex h-full flex-col rounded-md border border-slate-400 bg-slate-50/70 shadow-sm transition-shadow duration-150 hover:shadow-md dark:border-slate-500 dark:bg-slate-800/80"${_scopeId}><div class="flex items-center justify-between border-b border-dashed border-slate-400 px-2 py-1 dark:border-slate-500"${_scopeId}><div class="flex items-center gap-2"${_scopeId}><button type="button" class="drag-handle text-slate-400 hover:text-slate-700 dark:hover:text-slate-100"${ssrRenderAttr("title", unref(t)("dragDrop"))}${_scopeId}><svg class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20"${_scopeId}><path d="M7 4h2v2H7V4zm4 0h2v2h-2V4zM7 8h2v2H7V8zm4 0h2v2h-2V8zM7 12h2v2H7v-2zm4 0h2v2h-2v-2z"${_scopeId}></path></svg></button><div class="rounded-sm border border-gray-400 bg-slate-200 px-1.5 py-0.5 text-[10px] font-semibold text-slate-800 dark:bg-slate-700 dark:text-blue-100"${ssrRenderAttr("title", `[sort: ${schedule.sort}]`)}${_scopeId}> ID: ${ssrInterpolate(schedule.id)}</div></div><div class="flex items-center gap-2"${_scopeId}><span class="rounded-sm border border-gray-400 bg-emerald-100 px-1.5 py-0.5 text-[10px] text-emerald-700 dark:bg-emerald-900/50 dark:text-emerald-200"${ssrRenderAttr("title", unref(t)("online"))}${_scopeId}>${ssrInterpolate(getScheduleOnlineLabel(schedule.is_online))}</span><input type="checkbox"${ssrIncludeBooleanAttr(
                __props.selectedSchedules.includes(
                  schedule.id
                )
              ) ? " checked" : ""}${_scopeId}></div></div><div class="relative h-32 w-full bg-slate-200 dark:bg-slate-900"${_scopeId}>`);
              if (schedule.thumbnail_url) {
                _push2(`<img${ssrRenderAttr("src", schedule.thumbnail_url)}${ssrRenderAttr("alt", getScheduleTitle(schedule))}${ssrRenderAttr("title", getScheduleTitle(schedule))} class="h-full w-full object-cover"${_scopeId}>`);
              } else {
                _push2(`<img src="/storage/school/school_course_schedule_images/default-image.png"${ssrRenderAttr("alt", unref(t)("defaultImageTitle"))} class="h-full w-full object-cover"${_scopeId}>`);
              }
              _push2(`</div><div class="flex flex-1 flex-col space-y-1 px-3 py-2 text-[11px]"${_scopeId}><a${ssrRenderAttr("href", `/school/schedules/${encodeURIComponent(schedule.slug)}`)} target="_blank" rel="noopener noreferrer" class="text-center text-xs font-semibold text-amber-600 hover:text-red-600 hover:underline dark:text-amber-200 dark:hover:text-red-300"${ssrRenderAttr(
                "title",
                getScheduleSubtitle(schedule) || getScheduleShort(schedule)
              )}${_scopeId}>${ssrInterpolate(getScheduleTitle(schedule))}</a><div class="text-center text-[10px] text-slate-500 dark:text-slate-300"${ssrRenderAttr("title", schedule.slug)}${_scopeId}>${ssrInterpolate(schedule.slug)}</div><div class="text-center text-[10px] font-semibold text-blue-700 dark:text-blue-300"${ssrRenderAttr("title", getCourseTitle(schedule))}${_scopeId}>${ssrInterpolate(getCourseTitle(schedule) || "—")}</div><div class="mt-1 text-center"${_scopeId}><span class="font-semibold text-slate-700 dark:text-slate-300"${_scopeId}>${ssrInterpolate(getInstructorUserLabel(schedule) || "—")}</span>`);
              if (getInstructorTitle(schedule)) {
                _push2(`<!--[--><br${_scopeId}><span class="font-semibold text-teal-600 dark:text-teal-300"${_scopeId}>${ssrInterpolate(getInstructorTitle(schedule))}</span><!--]-->`);
              } else {
                _push2(`<!---->`);
              }
              _push2(`</div>`);
              if (schedule.location) {
                _push2(`<div class="text-center text-[10px] text-amber-700 dark:text-amber-300"${ssrRenderAttr("title", schedule.location)}${_scopeId}>${ssrInterpolate(schedule.location)}</div>`);
              } else {
                _push2(`<!---->`);
              }
              if (schedule.timezone) {
                _push2(`<div class="text-center text-[9px] text-slate-500 dark:text-slate-400"${_scopeId}>${ssrInterpolate(schedule.timezone)}</div>`);
              } else {
                _push2(`<!---->`);
              }
              _push2(`<div class="mt-1 border border-dashed border-gray-400 px-1 text-left text-[9px] font-semibold"${_scopeId}><div${_scopeId}><span class="text-gray-600 dark:text-gray-300"${_scopeId}>${ssrInterpolate(unref(t)("scheduleStartsAt"))}: </span><span class="text-sky-700 dark:text-sky-300"${_scopeId}>${ssrInterpolate(schedule.starts_at ? formatDate(schedule.starts_at) : "—")}</span></div><div${_scopeId}><span class="text-gray-600 dark:text-gray-300"${_scopeId}>${ssrInterpolate(unref(t)("scheduleEndsAt"))}: </span><span class="text-rose-600 dark:text-rose-300"${_scopeId}>${ssrInterpolate(schedule.ends_at ? formatDate(schedule.ends_at) : "—")}</span></div></div><div class="mt-1 text-center text-[9px] font-semibold"${_scopeId}><div class="flex flex-col"${_scopeId}><span class="text-slate-800 dark:text-slate-100"${_scopeId}>${ssrInterpolate(unref(t)("scheduleEnrollStartsAt"))}: </span><span class="text-blue-700 dark:text-blue-300"${_scopeId}>${ssrInterpolate(schedule.enroll_starts_at ? formatDate(schedule.enroll_starts_at) : "—")}</span></div><div class="flex flex-col"${_scopeId}><span class="text-slate-800 dark:text-slate-100"${_scopeId}>${ssrInterpolate(unref(t)("scheduleEnrollEndsAt"))}: </span><span class="text-rose-600 dark:text-rose-300"${_scopeId}>${ssrInterpolate(schedule.enroll_ends_at ? formatDate(schedule.enroll_ends_at) : "—")}</span></div></div>`);
              if (schedule.meeting_url) {
                _push2(`<a${ssrRenderAttr("href", schedule.meeting_url)} target="_blank" rel="noopener noreferrer" class="truncate text-center text-[9px] text-sky-600 hover:underline dark:text-sky-300"${ssrRenderAttr("title", schedule.meeting_url)}${_scopeId}>${ssrInterpolate(schedule.meeting_url)}</a>`);
              } else {
                _push2(`<!---->`);
              }
              _push2(`<div class="mt-2 flex flex-wrap justify-center gap-1 text-[10px] font-semibold"${_scopeId}><span class="rounded-sm border border-gray-400 bg-amber-100 px-2 py-0.5 text-amber-700 dark:bg-amber-900 dark:text-amber-200"${ssrRenderAttr("title", unref(t)("capacity"))}${_scopeId}>${ssrInterpolate(unref(t)("capacity"))}: ${ssrInterpolate(schedule.capacity ?? 0)}</span><span class="rounded-sm border border-gray-400 bg-violet-100 px-2 py-0.5 text-violet-700 dark:bg-violet-900 dark:text-violet-200"${_scopeId}>${ssrInterpolate(unref(t)("enrollments"))}: ${ssrInterpolate(schedule.cohort_enrollments_count ?? 0)}</span><span class="rounded-sm border border-gray-400 bg-slate-100 px-2 py-0.5 text-slate-700 dark:bg-slate-700 dark:text-slate-200"${_scopeId}>${ssrInterpolate(unref(t)("images"))}: ${ssrInterpolate(schedule.images_count ?? 0)}</span><span class="rounded-sm border border-gray-400 bg-sky-100 px-2 py-0.5 text-sky-700 dark:bg-sky-900 dark:text-sky-200"${ssrRenderAttr("title", unref(t)("status"))}${_scopeId}>${ssrInterpolate(getScheduleStatusLabel(schedule.status))}</span><span class="rounded-sm border border-gray-400 bg-blue-100 px-2 py-0.5 text-blue-700 dark:bg-blue-900 dark:text-blue-200"${ssrRenderAttr("title", unref(t)("views"))}${_scopeId}>${ssrInterpolate(unref(t)("views"))}: ${ssrInterpolate(schedule.views ?? 0)}</span></div><div class="${ssrRenderClass([
                schedule.is_enrollment_open ? "text-emerald-600 dark:text-emerald-300" : "text-slate-500 dark:text-slate-400",
                "mt-1 text-center text-[10px] font-semibold"
              ])}"${_scopeId}>${ssrInterpolate(schedule.is_enrollment_open ? unref(t)("enabled") : unref(t)("disabled"))}</div></div><div class="flex items-center justify-center border-t border-dashed border-slate-400 px-3 py-2 dark:border-slate-500"${_scopeId}><div class="flex items-center gap-1"${_scopeId}>`);
              _push2(ssrRenderComponent(_sfc_main$5, {
                isActive: schedule.activity,
                title: schedule.activity ? unref(t)("enabled") : unref(t)("disabled"),
                onToggleActivity: ($event) => emit(
                  "toggle-activity",
                  schedule
                )
              }, null, _parent2, _scopeId));
              _push2(ssrRenderComponent(_sfc_main$6, {
                title: unref(t)("clone"),
                onClone: ($event) => emit(
                  "clone",
                  schedule
                )
              }, null, _parent2, _scopeId));
              _push2(ssrRenderComponent(_sfc_main$7, {
                href: _ctx.route(
                  "admin.schoolCourseSchedules.edit",
                  {
                    schoolCourseSchedule: schedule.id
                  }
                )
              }, null, _parent2, _scopeId));
              _push2(ssrRenderComponent(_sfc_main$8, {
                onDelete: ($event) => emit(
                  "delete",
                  schedule
                )
              }, null, _parent2, _scopeId));
              _push2(`</div></div></div>`);
            } else {
              return [
                createVNode("div", { class: "relative flex h-full flex-col rounded-md border border-slate-400 bg-slate-50/70 shadow-sm transition-shadow duration-150 hover:shadow-md dark:border-slate-500 dark:bg-slate-800/80" }, [
                  createVNode("div", { class: "flex items-center justify-between border-b border-dashed border-slate-400 px-2 py-1 dark:border-slate-500" }, [
                    createVNode("div", { class: "flex items-center gap-2" }, [
                      createVNode("button", {
                        type: "button",
                        class: "drag-handle text-slate-400 hover:text-slate-700 dark:hover:text-slate-100",
                        title: unref(t)("dragDrop")
                      }, [
                        (openBlock(), createBlock("svg", {
                          class: "h-4 w-4",
                          fill: "currentColor",
                          viewBox: "0 0 20 20"
                        }, [
                          createVNode("path", { d: "M7 4h2v2H7V4zm4 0h2v2h-2V4zM7 8h2v2H7V8zm4 0h2v2h-2V8zM7 12h2v2H7v-2zm4 0h2v2h-2v-2z" })
                        ]))
                      ], 8, ["title"]),
                      createVNode("div", {
                        class: "rounded-sm border border-gray-400 bg-slate-200 px-1.5 py-0.5 text-[10px] font-semibold text-slate-800 dark:bg-slate-700 dark:text-blue-100",
                        title: `[sort: ${schedule.sort}]`
                      }, " ID: " + toDisplayString(schedule.id), 9, ["title"])
                    ]),
                    createVNode("div", { class: "flex items-center gap-2" }, [
                      createVNode("span", {
                        class: "rounded-sm border border-gray-400 bg-emerald-100 px-1.5 py-0.5 text-[10px] text-emerald-700 dark:bg-emerald-900/50 dark:text-emerald-200",
                        title: unref(t)("online")
                      }, toDisplayString(getScheduleOnlineLabel(schedule.is_online)), 9, ["title"]),
                      createVNode("input", {
                        type: "checkbox",
                        checked: __props.selectedSchedules.includes(
                          schedule.id
                        ),
                        onChange: ($event) => emit(
                          "toggle-select",
                          schedule.id
                        )
                      }, null, 40, ["checked", "onChange"])
                    ])
                  ]),
                  createVNode("div", { class: "relative h-32 w-full bg-slate-200 dark:bg-slate-900" }, [
                    schedule.thumbnail_url ? (openBlock(), createBlock("img", {
                      key: 0,
                      src: schedule.thumbnail_url,
                      alt: getScheduleTitle(schedule),
                      title: getScheduleTitle(schedule),
                      class: "h-full w-full object-cover"
                    }, null, 8, ["src", "alt", "title"])) : (openBlock(), createBlock("img", {
                      key: 1,
                      src: "/storage/school/school_course_schedule_images/default-image.png",
                      alt: unref(t)("defaultImageTitle"),
                      class: "h-full w-full object-cover"
                    }, null, 8, ["alt"]))
                  ]),
                  createVNode("div", { class: "flex flex-1 flex-col space-y-1 px-3 py-2 text-[11px]" }, [
                    createVNode("a", {
                      href: `/school/schedules/${encodeURIComponent(schedule.slug)}`,
                      target: "_blank",
                      rel: "noopener noreferrer",
                      class: "text-center text-xs font-semibold text-amber-600 hover:text-red-600 hover:underline dark:text-amber-200 dark:hover:text-red-300",
                      title: getScheduleSubtitle(schedule) || getScheduleShort(schedule)
                    }, toDisplayString(getScheduleTitle(schedule)), 9, ["href", "title"]),
                    createVNode("div", {
                      class: "text-center text-[10px] text-slate-500 dark:text-slate-300",
                      title: schedule.slug
                    }, toDisplayString(schedule.slug), 9, ["title"]),
                    createVNode("div", {
                      class: "text-center text-[10px] font-semibold text-blue-700 dark:text-blue-300",
                      title: getCourseTitle(schedule)
                    }, toDisplayString(getCourseTitle(schedule) || "—"), 9, ["title"]),
                    createVNode("div", { class: "mt-1 text-center" }, [
                      createVNode("span", { class: "font-semibold text-slate-700 dark:text-slate-300" }, toDisplayString(getInstructorUserLabel(schedule) || "—"), 1),
                      getInstructorTitle(schedule) ? (openBlock(), createBlock(Fragment, { key: 0 }, [
                        createVNode("br"),
                        createVNode("span", { class: "font-semibold text-teal-600 dark:text-teal-300" }, toDisplayString(getInstructorTitle(schedule)), 1)
                      ], 64)) : createCommentVNode("", true)
                    ]),
                    schedule.location ? (openBlock(), createBlock("div", {
                      key: 0,
                      class: "text-center text-[10px] text-amber-700 dark:text-amber-300",
                      title: schedule.location
                    }, toDisplayString(schedule.location), 9, ["title"])) : createCommentVNode("", true),
                    schedule.timezone ? (openBlock(), createBlock("div", {
                      key: 1,
                      class: "text-center text-[9px] text-slate-500 dark:text-slate-400"
                    }, toDisplayString(schedule.timezone), 1)) : createCommentVNode("", true),
                    createVNode("div", { class: "mt-1 border border-dashed border-gray-400 px-1 text-left text-[9px] font-semibold" }, [
                      createVNode("div", null, [
                        createVNode("span", { class: "text-gray-600 dark:text-gray-300" }, toDisplayString(unref(t)("scheduleStartsAt")) + ": ", 1),
                        createVNode("span", { class: "text-sky-700 dark:text-sky-300" }, toDisplayString(schedule.starts_at ? formatDate(schedule.starts_at) : "—"), 1)
                      ]),
                      createVNode("div", null, [
                        createVNode("span", { class: "text-gray-600 dark:text-gray-300" }, toDisplayString(unref(t)("scheduleEndsAt")) + ": ", 1),
                        createVNode("span", { class: "text-rose-600 dark:text-rose-300" }, toDisplayString(schedule.ends_at ? formatDate(schedule.ends_at) : "—"), 1)
                      ])
                    ]),
                    createVNode("div", { class: "mt-1 text-center text-[9px] font-semibold" }, [
                      createVNode("div", { class: "flex flex-col" }, [
                        createVNode("span", { class: "text-slate-800 dark:text-slate-100" }, toDisplayString(unref(t)("scheduleEnrollStartsAt")) + ": ", 1),
                        createVNode("span", { class: "text-blue-700 dark:text-blue-300" }, toDisplayString(schedule.enroll_starts_at ? formatDate(schedule.enroll_starts_at) : "—"), 1)
                      ]),
                      createVNode("div", { class: "flex flex-col" }, [
                        createVNode("span", { class: "text-slate-800 dark:text-slate-100" }, toDisplayString(unref(t)("scheduleEnrollEndsAt")) + ": ", 1),
                        createVNode("span", { class: "text-rose-600 dark:text-rose-300" }, toDisplayString(schedule.enroll_ends_at ? formatDate(schedule.enroll_ends_at) : "—"), 1)
                      ])
                    ]),
                    schedule.meeting_url ? (openBlock(), createBlock("a", {
                      key: 2,
                      href: schedule.meeting_url,
                      target: "_blank",
                      rel: "noopener noreferrer",
                      class: "truncate text-center text-[9px] text-sky-600 hover:underline dark:text-sky-300",
                      title: schedule.meeting_url
                    }, toDisplayString(schedule.meeting_url), 9, ["href", "title"])) : createCommentVNode("", true),
                    createVNode("div", { class: "mt-2 flex flex-wrap justify-center gap-1 text-[10px] font-semibold" }, [
                      createVNode("span", {
                        class: "rounded-sm border border-gray-400 bg-amber-100 px-2 py-0.5 text-amber-700 dark:bg-amber-900 dark:text-amber-200",
                        title: unref(t)("capacity")
                      }, toDisplayString(unref(t)("capacity")) + ": " + toDisplayString(schedule.capacity ?? 0), 9, ["title"]),
                      createVNode("span", { class: "rounded-sm border border-gray-400 bg-violet-100 px-2 py-0.5 text-violet-700 dark:bg-violet-900 dark:text-violet-200" }, toDisplayString(unref(t)("enrollments")) + ": " + toDisplayString(schedule.cohort_enrollments_count ?? 0), 1),
                      createVNode("span", { class: "rounded-sm border border-gray-400 bg-slate-100 px-2 py-0.5 text-slate-700 dark:bg-slate-700 dark:text-slate-200" }, toDisplayString(unref(t)("images")) + ": " + toDisplayString(schedule.images_count ?? 0), 1),
                      createVNode("span", {
                        class: "rounded-sm border border-gray-400 bg-sky-100 px-2 py-0.5 text-sky-700 dark:bg-sky-900 dark:text-sky-200",
                        title: unref(t)("status")
                      }, toDisplayString(getScheduleStatusLabel(schedule.status)), 9, ["title"]),
                      createVNode("span", {
                        class: "rounded-sm border border-gray-400 bg-blue-100 px-2 py-0.5 text-blue-700 dark:bg-blue-900 dark:text-blue-200",
                        title: unref(t)("views")
                      }, toDisplayString(unref(t)("views")) + ": " + toDisplayString(schedule.views ?? 0), 9, ["title"])
                    ]),
                    createVNode("div", {
                      class: [
                        "mt-1 text-center text-[10px] font-semibold",
                        schedule.is_enrollment_open ? "text-emerald-600 dark:text-emerald-300" : "text-slate-500 dark:text-slate-400"
                      ]
                    }, toDisplayString(schedule.is_enrollment_open ? unref(t)("enabled") : unref(t)("disabled")), 3)
                  ]),
                  createVNode("div", { class: "flex items-center justify-center border-t border-dashed border-slate-400 px-3 py-2 dark:border-slate-500" }, [
                    createVNode("div", { class: "flex items-center gap-1" }, [
                      createVNode(_sfc_main$5, {
                        isActive: schedule.activity,
                        title: schedule.activity ? unref(t)("enabled") : unref(t)("disabled"),
                        onToggleActivity: ($event) => emit(
                          "toggle-activity",
                          schedule
                        )
                      }, null, 8, ["isActive", "title", "onToggleActivity"]),
                      createVNode(_sfc_main$6, {
                        title: unref(t)("clone"),
                        onClone: ($event) => emit(
                          "clone",
                          schedule
                        )
                      }, null, 8, ["title", "onClone"]),
                      createVNode(_sfc_main$7, {
                        href: _ctx.route(
                          "admin.schoolCourseSchedules.edit",
                          {
                            schoolCourseSchedule: schedule.id
                          }
                        )
                      }, null, 8, ["href"]),
                      createVNode(_sfc_main$8, {
                        onDelete: ($event) => emit(
                          "delete",
                          schedule
                        )
                      }, null, 8, ["onDelete"])
                    ])
                  ])
                ])
              ];
            }
          }),
          _: 1
        }, _parent));
        _push(`</div>`);
      } else {
        _push(`<div class="p-5 text-center text-slate-700 dark:text-slate-100">${ssrInterpolate(unref(t)("noData"))}</div>`);
      }
      _push(`</div>`);
    };
  }
};
const _sfc_setup$1 = _sfc_main$1.setup;
_sfc_main$1.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Components/Admin/School/SchoolCourseSchedule/View/CourseScheduleCardGrid.vue");
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
    adminSchoolCourseSchedulesProcessingMode: {
      type: String,
      default: "frontend"
    },
    useServerProcessing: {
      type: Boolean,
      default: false
    },
    schedules: {
      type: [Array, Object],
      default: () => []
    },
    schedulesCount: {
      type: Number,
      default: 0
    },
    adminSchoolCourseSchedulesPerPage: {
      type: Number,
      default: 6
    },
    adminSchoolCourseSchedulesDefaultSort: {
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
        "admin_view_mode_course_schedules"
      ) || "table"
    );
    watch(viewMode, (value) => {
      localStorage.setItem(
        "admin_view_mode_course_schedules",
        value
      );
    });
    const schedulesList = computed(() => {
      var _a;
      if (Array.isArray(props.schedules)) {
        return props.schedules;
      }
      if (Array.isArray((_a = props.schedules) == null ? void 0 : _a.data)) {
        return props.schedules.data;
      }
      return [];
    });
    const localSchedules = ref([]);
    watch(
      schedulesList,
      (schedules) => {
        localSchedules.value = JSON.parse(
          JSON.stringify(
            schedules || []
          )
        );
      },
      {
        immediate: true,
        deep: true
      }
    );
    const itemsPerPage = ref(
      props.adminSchoolCourseSchedulesPerPage || 6
    );
    const sortParam = ref(
      props.sortParam || props.adminSchoolCourseSchedulesDefaultSort || "idDesc"
    );
    const searchQuery = ref(
      props.search || ""
    );
    const currentPage = ref(1);
    const serverCurrentPage = computed(() => {
      var _a, _b, _c;
      return Number(
        ((_b = (_a = props.schedules) == null ? void 0 : _a.meta) == null ? void 0 : _b.current_page) ?? ((_c = props.schedules) == null ? void 0 : _c.current_page) ?? 1
      ) || 1;
    });
    const activeCurrentPage = computed(() => {
      return props.useServerProcessing ? serverCurrentPage.value : currentPage.value;
    });
    watch(itemsPerPage, (newValue) => {
      if (props.useServerProcessing) {
        return;
      }
      currentPage.value = 1;
      router.put(
        route(
          "admin.settings.updateAdminCountSchoolCourseSchedules"
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
              (errors == null ? void 0 : errors.value) || "Ошибка обновления количества элементов."
            );
          }
        }
      );
    });
    watch(sortParam, (newValue) => {
      currentPage.value = 1;
      router.put(
        route(
          "admin.settings.updateAdminSortSchoolCourseSchedules"
        ),
        {
          value: newValue
        },
        {
          preserveScroll: true,
          preserveState: true,
          onSuccess: () => {
            if (props.useServerProcessing) {
              const query = Object.fromEntries(
                new URLSearchParams(
                  window.location.search
                )
              );
              router.get(
                window.location.pathname,
                {
                  ...query,
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
    });
    const getScheduleTitle = (schedule) => {
      var _a;
      return ((_a = schedule == null ? void 0 : schedule.translation) == null ? void 0 : _a.title) || `ID: ${schedule == null ? void 0 : schedule.id}`;
    };
    const getScheduleSubtitle = (schedule) => {
      var _a;
      return ((_a = schedule == null ? void 0 : schedule.translation) == null ? void 0 : _a.subtitle) || "";
    };
    const getScheduleShort = (schedule) => {
      var _a;
      return ((_a = schedule == null ? void 0 : schedule.translation) == null ? void 0 : _a.short) || "";
    };
    const getScheduleDescription = (schedule) => {
      var _a;
      return ((_a = schedule == null ? void 0 : schedule.translation) == null ? void 0 : _a.description) || "";
    };
    const getCourseTitle = (schedule) => {
      var _a, _b, _c;
      return ((_b = (_a = schedule == null ? void 0 : schedule.course) == null ? void 0 : _a.translation) == null ? void 0 : _b.title) || (((_c = schedule == null ? void 0 : schedule.course) == null ? void 0 : _c.id) ? `ID: ${schedule.course.id}` : "");
    };
    const getCourseSlug = (schedule) => {
      var _a;
      return ((_a = schedule == null ? void 0 : schedule.course) == null ? void 0 : _a.slug) || "";
    };
    const getInstructorTitle = (schedule) => {
      var _a, _b;
      const instructor = schedule == null ? void 0 : schedule.instructor;
      if (!instructor) {
        return "";
      }
      return ((_a = instructor == null ? void 0 : instructor.translation) == null ? void 0 : _a.title) || ((_b = instructor == null ? void 0 : instructor.user) == null ? void 0 : _b.name) || ((instructor == null ? void 0 : instructor.id) ? `ID: ${instructor.id}` : "");
    };
    const getInstructorName = (schedule) => {
      var _a, _b;
      return ((_b = (_a = schedule == null ? void 0 : schedule.instructor) == null ? void 0 : _a.user) == null ? void 0 : _b.name) || "";
    };
    const getInstructorEmail = (schedule) => {
      var _a, _b;
      return ((_b = (_a = schedule == null ? void 0 : schedule.instructor) == null ? void 0 : _a.user) == null ? void 0 : _b.email) || "";
    };
    const normalize = (value) => {
      return String(value ?? "").trim().toLocaleLowerCase();
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
    const compareText = (a, b) => {
      return normalize(a).localeCompare(
        normalize(b),
        props.currentLocale || void 0,
        {
          sensitivity: "base"
        }
      );
    };
    const byNumberAsc = (field) => {
      return (a, b) => safeNumber(a == null ? void 0 : a[field]) - safeNumber(b == null ? void 0 : b[field]) || safeNumber(a == null ? void 0 : a.id) - safeNumber(b == null ? void 0 : b.id);
    };
    const byNumberDesc = (field) => {
      return (a, b) => safeNumber(b == null ? void 0 : b[field]) - safeNumber(a == null ? void 0 : a[field]) || safeNumber(b == null ? void 0 : b.id) - safeNumber(a == null ? void 0 : a.id);
    };
    const byStringAsc = (field) => {
      return (a, b) => compareText(
        a == null ? void 0 : a[field],
        b == null ? void 0 : b[field]
      ) || safeNumber(a == null ? void 0 : a.id) - safeNumber(b == null ? void 0 : b.id);
    };
    const byStringDesc = (field) => {
      return (a, b) => compareText(
        b == null ? void 0 : b[field],
        a == null ? void 0 : a[field]
      ) || safeNumber(b == null ? void 0 : b.id) - safeNumber(a == null ? void 0 : a.id);
    };
    const byDateAsc = (field) => {
      return (a, b) => safeDate(a == null ? void 0 : a[field]) - safeDate(b == null ? void 0 : b[field]) || safeNumber(a == null ? void 0 : a.id) - safeNumber(b == null ? void 0 : b.id);
    };
    const byDateDesc = (field) => {
      return (a, b) => safeDate(b == null ? void 0 : b[field]) - safeDate(a == null ? void 0 : a[field]) || safeNumber(b == null ? void 0 : b.id) - safeNumber(a == null ? void 0 : a.id);
    };
    const sortSchedules = (items) => {
      const list = [
        ...items || []
      ];
      if (sortParam.value === "activity") {
        return list.filter(
          (schedule) => !!schedule.activity
        );
      }
      if (sortParam.value === "inactive") {
        return list.filter(
          (schedule) => !schedule.activity
        );
      }
      if (sortParam.value === "online") {
        return list.filter(
          (schedule) => !!schedule.is_online
        );
      }
      if (sortParam.value === "offline") {
        return list.filter(
          (schedule) => !schedule.is_online
        );
      }
      const sortMap = {
        idAsc: byNumberAsc("id"),
        idDesc: byNumberDesc("id"),
        sortAsc: byNumberAsc("sort"),
        sortDesc: byNumberDesc("sort"),
        slugAsc: byStringAsc("slug"),
        slugDesc: byStringDesc("slug"),
        titleAsc: (a, b) => compareText(
          getScheduleTitle(a),
          getScheduleTitle(b)
        ) || safeNumber(a == null ? void 0 : a.id) - safeNumber(b == null ? void 0 : b.id),
        titleDesc: (a, b) => compareText(
          getScheduleTitle(b),
          getScheduleTitle(a)
        ) || safeNumber(b == null ? void 0 : b.id) - safeNumber(a == null ? void 0 : a.id),
        statusAsc: byStringAsc("status"),
        statusDesc: byStringDesc("status"),
        timezoneAsc: byStringAsc("timezone"),
        timezoneDesc: byStringDesc("timezone"),
        locationAsc: byStringAsc("location"),
        locationDesc: byStringDesc("location"),
        meetingUrlAsc: byStringAsc("meeting_url"),
        meetingUrlDesc: byStringDesc("meeting_url"),
        capacityAsc: byNumberAsc("capacity"),
        capacityDesc: byNumberDesc("capacity"),
        viewsAsc: byNumberAsc("views"),
        viewsDesc: byNumberDesc("views"),
        imagesAsc: byNumberAsc("images_count"),
        imagesDesc: byNumberDesc("images_count"),
        cohortEnrollmentsAsc: byNumberAsc(
          "cohort_enrollments_count"
        ),
        cohortEnrollmentsDesc: byNumberDesc(
          "cohort_enrollments_count"
        ),
        activityAsc: byNumberAsc("activity"),
        activityDesc: byNumberDesc("activity"),
        onlineAsc: byNumberAsc("is_online"),
        onlineDesc: byNumberDesc("is_online"),
        startsAtAsc: byDateAsc("starts_at"),
        startsAtDesc: byDateDesc("starts_at"),
        endsAtAsc: byDateAsc("ends_at"),
        endsAtDesc: byDateDesc("ends_at"),
        enrollStartsAtAsc: byDateAsc(
          "enroll_starts_at"
        ),
        enrollStartsAtDesc: byDateDesc(
          "enroll_starts_at"
        ),
        enrollEndsAtAsc: byDateAsc(
          "enroll_ends_at"
        ),
        enrollEndsAtDesc: byDateDesc(
          "enroll_ends_at"
        ),
        createdAtAsc: byDateAsc("created_at"),
        createdAtDesc: byDateDesc("created_at"),
        updatedAtAsc: byDateAsc("updated_at"),
        updatedAtDesc: byDateDesc("updated_at")
      };
      const sorter = sortMap[sortParam.value];
      return sorter ? list.sort(sorter) : list;
    };
    const filteredSchedules = computed(() => {
      let schedules = localSchedules.value || [];
      if (props.useServerProcessing) {
        return schedules;
      }
      const query = normalize(
        searchQuery.value
      );
      if (query) {
        schedules = schedules.filter(
          (schedule) => {
            const title = normalize(
              getScheduleTitle(
                schedule
              )
            );
            const subtitle = normalize(
              getScheduleSubtitle(
                schedule
              )
            );
            const short = normalize(
              getScheduleShort(
                schedule
              )
            );
            const description = normalize(
              getScheduleDescription(
                schedule
              )
            );
            const slug = normalize(
              schedule == null ? void 0 : schedule.slug
            );
            const location = normalize(
              schedule == null ? void 0 : schedule.location
            );
            const meetingUrl = normalize(
              schedule == null ? void 0 : schedule.meeting_url
            );
            const timezone = normalize(
              schedule == null ? void 0 : schedule.timezone
            );
            const status = normalize(
              schedule == null ? void 0 : schedule.status
            );
            const courseTitle = normalize(
              getCourseTitle(
                schedule
              )
            );
            const courseSlug = normalize(
              getCourseSlug(
                schedule
              )
            );
            const instructorTitle = normalize(
              getInstructorTitle(
                schedule
              )
            );
            const instructorName = normalize(
              getInstructorName(
                schedule
              )
            );
            const instructorEmail = normalize(
              getInstructorEmail(
                schedule
              )
            );
            const ids = [
              schedule == null ? void 0 : schedule.id,
              schedule == null ? void 0 : schedule.sort,
              schedule == null ? void 0 : schedule.school_course_id,
              schedule == null ? void 0 : schedule.school_instructor_profile_id,
              schedule == null ? void 0 : schedule.capacity,
              schedule == null ? void 0 : schedule.views,
              schedule == null ? void 0 : schedule.images_count,
              schedule == null ? void 0 : schedule.cohort_enrollments_count
            ].map(normalize).join(" ");
            return title.includes(query) || subtitle.includes(query) || short.includes(query) || description.includes(query) || slug.includes(query) || location.includes(query) || meetingUrl.includes(query) || timezone.includes(query) || status.includes(query) || courseTitle.includes(query) || courseSlug.includes(query) || instructorTitle.includes(query) || instructorName.includes(query) || instructorEmail.includes(query) || ids.includes(query);
          }
        );
      }
      return sortSchedules(
        schedules
      );
    });
    const paginatedSchedules = computed(() => {
      const perPage = Number(
        itemsPerPage.value
      ) || 6;
      const start = (currentPage.value - 1) * perPage;
      return filteredSchedules.value.slice(
        start,
        start + perPage
      );
    });
    const displayedSchedules = computed(() => {
      return props.useServerProcessing ? schedulesList.value : paginatedSchedules.value;
    });
    watch(
      [
        itemsPerPage,
        searchQuery
      ],
      () => {
        if (!props.useServerProcessing) {
          currentPage.value = 1;
        }
      }
    );
    const showConfirmDeleteModal = ref(false);
    const scheduleToDeleteId = ref(null);
    const scheduleToDeleteTitle = ref("");
    const confirmDelete = (scheduleOrId, title = null) => {
      if (typeof scheduleOrId === "object" && scheduleOrId !== null) {
        scheduleToDeleteId.value = scheduleOrId.id;
        scheduleToDeleteTitle.value = title || getScheduleTitle(
          scheduleOrId
        );
      } else {
        scheduleToDeleteId.value = scheduleOrId;
        scheduleToDeleteTitle.value = title || `ID: ${scheduleOrId}`;
      }
      showConfirmDeleteModal.value = true;
    };
    const closeModal = () => {
      showConfirmDeleteModal.value = false;
      scheduleToDeleteId.value = null;
      scheduleToDeleteTitle.value = "";
    };
    const deleteSchedule = () => {
      if (scheduleToDeleteId.value === null) {
        return;
      }
      const id = scheduleToDeleteId.value;
      const title = scheduleToDeleteTitle.value;
      router.delete(
        route(
          "admin.schoolCourseSchedules.destroy",
          {
            schoolCourseSchedule: id
          }
        ),
        {
          preserveScroll: true,
          preserveState: false,
          onSuccess: () => {
            toast.success(
              `Расписание "${title || `ID: ${id}`}" удалено.`
            );
          },
          onError: (errors) => {
            const errorKey = Object.keys(
              errors || {}
            )[0];
            const message = (errors == null ? void 0 : errors.general) || (errors == null ? void 0 : errors[errorKey]) || "Произошла ошибка при удалении.";
            toast.error(
              `${message} (Расписание: ${title || `ID: ${id}`})`
            );
          },
          onFinish: () => {
            closeModal();
          }
        }
      );
    };
    const patchSchedule = (scheduleId, payload) => {
      const index = localSchedules.value.findIndex(
        (schedule) => schedule.id === scheduleId
      );
      if (index === -1) {
        return;
      }
      localSchedules.value[index] = {
        ...localSchedules.value[index],
        ...payload
      };
    };
    const selectedSchedules = ref([]);
    const toggleAll = (payload) => {
      var _a;
      const checked = (payload == null ? void 0 : payload.checked) ?? ((_a = payload == null ? void 0 : payload.target) == null ? void 0 : _a.checked) ?? false;
      const ids = (payload == null ? void 0 : payload.ids) ?? displayedSchedules.value.map(
        (schedule) => schedule.id
      );
      if (checked) {
        selectedSchedules.value = [
          .../* @__PURE__ */ new Set([
            ...selectedSchedules.value,
            ...ids
          ])
        ];
        return;
      }
      selectedSchedules.value = selectedSchedules.value.filter(
        (id) => !ids.includes(id)
      );
    };
    const toggleSelectSchedule = (id) => {
      const index = selectedSchedules.value.indexOf(
        id
      );
      if (index > -1) {
        selectedSchedules.value.splice(
          index,
          1
        );
        return;
      }
      selectedSchedules.value.push(
        id
      );
    };
    const handleSortOrderUpdate = (orderedIds) => {
      const startSort = (activeCurrentPage.value - 1) * Number(
        itemsPerPage.value || 6
      );
      const items = orderedIds.map(
        (id, index) => ({
          id,
          sort: startSort + index + 1
        })
      );
      if (!items.length) {
        return;
      }
      router.put(
        route(
          "admin.actions.schoolCourseSchedules.updateSortBulk"
        ),
        {
          items
        },
        {
          preserveScroll: true,
          preserveState: true,
          onSuccess: () => {
            toast.success(
              "Порядок расписаний успешно обновлён."
            );
          },
          onError: (errors) => {
            console.error(
              "Ошибка обновления сортировки расписаний:",
              errors
            );
            toast.error(
              (errors == null ? void 0 : errors.message) || (errors == null ? void 0 : errors.general) || "Не удалось обновить порядок расписаний."
            );
            router.reload({
              only: [
                "schedules"
              ],
              preserveScroll: true
            });
          }
        }
      );
    };
    const bulkToggleActivity = (newActivity) => {
      if (!selectedSchedules.value.length) {
        toast.warning(
          "Выберите расписания для активации/деактивации."
        );
        return;
      }
      const ids = [
        ...selectedSchedules.value
      ];
      router.put(
        route(
          "admin.actions.schoolCourseSchedules.bulkUpdateActivity"
        ),
        {
          ids,
          activity: newActivity
        },
        {
          preserveScroll: true,
          preserveState: true,
          onSuccess: () => {
            ids.forEach((id) => {
              patchSchedule(
                id,
                {
                  activity: newActivity
                }
              );
            });
            selectedSchedules.value = [];
            toast.success(
              "Активность выбранных расписаний обновлена."
            );
          },
          onError: (errors) => {
            toast.error(
              (errors == null ? void 0 : errors.ids) || (errors == null ? void 0 : errors.activity) || (errors == null ? void 0 : errors.general) || "Ошибка массового обновления активности."
            );
          }
        }
      );
    };
    const bulkDelete = () => {
      if (!selectedSchedules.value.length) {
        toast.warning(
          "Выберите расписания для удаления."
        );
        return;
      }
      if (!confirm(
        "Вы уверены, что хотите удалить выбранные расписания?"
      )) {
        return;
      }
      router.delete(
        route(
          "admin.actions.schoolCourseSchedules.bulkDestroy"
        ),
        {
          data: {
            ids: selectedSchedules.value
          },
          preserveScroll: true,
          preserveState: false,
          onSuccess: () => {
            selectedSchedules.value = [];
            toast.success(
              "Выбранные расписания успешно удалены."
            );
          },
          onError: (errors) => {
            const errorKey = Object.keys(
              errors || {}
            )[0];
            toast.error(
              (errors == null ? void 0 : errors[errorKey]) || "Ошибка массового удаления расписаний."
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
      }
      if (action === "deselectAll") {
        toggleAll({
          target: {
            checked: false
          }
        });
      }
      if (action === "activate") {
        bulkToggleActivity(
          true
        );
      }
      if (action === "deactivate") {
        bulkToggleActivity(
          false
        );
      }
      if (action === "delete") {
        bulkDelete();
      }
      event.target.value = "";
    };
    const toggleActivity = (schedule) => {
      const newActivity = !schedule.activity;
      const scheduleTitle = getScheduleTitle(
        schedule
      );
      const actionText = newActivity ? t("activated") : t("deactivated");
      router.put(
        route(
          "admin.actions.schoolCourseSchedules.updateActivity",
          {
            schoolCourseSchedule: schedule.id
          }
        ),
        {
          activity: newActivity
        },
        {
          preserveScroll: true,
          preserveState: true,
          onSuccess: () => {
            patchSchedule(
              schedule.id,
              {
                activity: newActivity
              }
            );
            schedule.activity = newActivity;
            toast.success(
              `Расписание "${scheduleTitle}" ${actionText}.`
            );
          },
          onError: (errors) => {
            toast.error(
              (errors == null ? void 0 : errors.activity) || (errors == null ? void 0 : errors.general) || `Ошибка изменения активности для расписания "${scheduleTitle}".`
            );
          }
        }
      );
    };
    const cloneSchedule = (schedule) => {
      router.post(
        route(
          "admin.actions.schoolCourseSchedules.clone",
          {
            schoolCourseSchedule: schedule.id
          }
        ),
        {},
        {
          preserveScroll: true,
          onSuccess: () => {
            toast.success(
              "Расписание успешно клонировано."
            );
          },
          onError: () => {
            toast.error(
              "Ошибка при клонировании расписания."
            );
          }
        }
      );
    };
    return (_ctx, _push, _parent, _attrs) => {
      _push(ssrRenderComponent(AdminLayout, mergeProps({
        title: unref(t)("schedules")
      }, _attrs), {
        header: withCtx((_, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(ssrRenderComponent(TitlePage, null, {
              default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                if (_push3) {
                  _push3(`${ssrInterpolate(unref(t)("schedules"))}`);
                } else {
                  return [
                    createTextVNode(toDisplayString(unref(t)("schedules")), 1)
                  ];
                }
              }),
              _: 1
            }, _parent2, _scopeId));
          } else {
            return [
              createVNode(TitlePage, null, {
                default: withCtx(() => [
                  createTextVNode(toDisplayString(unref(t)("schedules")), 1)
                ]),
                _: 1
              })
            ];
          }
        }),
        default: withCtx((_, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(`<div class="px-2 py-2 w-full max-w-12xl mx-auto"${_scopeId}><div class="p-4 bg-slate-50 dark:bg-slate-700 border border-blue-400 dark:border-blue-200 overflow-hidden shadow-md shadow-gray-500 dark:shadow-slate-400 bg-opacity-95 dark:bg-opacity-95"${_scopeId}><div class="sm:flex sm:justify-between sm:items-center mb-3 gap-3"${_scopeId}>`);
            _push2(ssrRenderComponent(_sfc_main$9, {
              href: _ctx.route("admin.schoolCourseSchedules.create")
            }, {
              icon: withCtx((_2, _push3, _parent3, _scopeId2) => {
                if (_push3) {
                  _push3(`<svg class="w-4 h-4 fill-current opacity-50 shrink-0" viewBox="0 0 16 16"${_scopeId2}><path d="M15 7H9V1c0-.6-.4-1-1-1S7 .4 7 1v6H1c-.6 0-1 .4-1 1s.4 1 1 1h6v6c0 .6.4 1 1 1s1-.4 1-1V9h6c.6 0 1-.4 1-1s-.4-1-1-1z"${_scopeId2}></path></svg>`);
                } else {
                  return [
                    (openBlock(), createBlock("svg", {
                      class: "w-4 h-4 fill-current opacity-50 shrink-0",
                      viewBox: "0 0 16 16"
                    }, [
                      createVNode("path", { d: "M15 7H9V1c0-.6-.4-1-1-1S7 .4 7 1v6H1c-.6 0-1 .4-1 1s.4 1 1 1h6v6c0 .6.4 1 1 1s1-.4 1-1V9h6c.6 0 1-.4 1-1s-.4-1-1-1z" })
                    ]))
                  ];
                }
              }),
              default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                if (_push3) {
                  _push3(` ${ssrInterpolate(unref(t)("addSchedule"))}`);
                } else {
                  return [
                    createTextVNode(" " + toDisplayString(unref(t)("addSchedule")), 1)
                  ];
                }
              }),
              _: 1
            }, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$a, {
              "setting-key": "adminSchoolCourseSchedulesProcessingMode",
              mode: __props.adminSchoolCourseSchedulesProcessingMode,
              "use-server-processing": __props.useServerProcessing,
              total: __props.schedulesCount
            }, null, _parent2, _scopeId));
            _push2(`</div>`);
            if (__props.schedulesCount && !__props.useServerProcessing) {
              _push2(ssrRenderComponent(_sfc_main$b, {
                modelValue: searchQuery.value,
                "onUpdate:modelValue": ($event) => searchQuery.value = $event,
                placeholder: unref(t)("searchByName")
              }, null, _parent2, _scopeId));
            } else {
              _push2(`<!---->`);
            }
            if (__props.schedulesCount && __props.useServerProcessing) {
              _push2(ssrRenderComponent(_sfc_main$c, {
                modelValue: searchQuery.value,
                "onUpdate:modelValue": ($event) => searchQuery.value = $event
              }, null, _parent2, _scopeId));
            } else {
              _push2(`<!---->`);
            }
            if (__props.schedulesCount) {
              _push2(`<div class="flex justify-between items-center flex-col md:flex-row my-3 gap-3"${_scopeId}>`);
              if (!__props.useServerProcessing) {
                _push2(ssrRenderComponent(_sfc_main$d, {
                  "items-per-page": itemsPerPage.value,
                  "onUpdate:itemsPerPage": ($event) => itemsPerPage.value = $event
                }, null, _parent2, _scopeId));
              } else {
                _push2(ssrRenderComponent(_sfc_main$e, {
                  "items-per-page": itemsPerPage.value,
                  "update-route": "admin.settings.updateAdminCountSchoolCourseSchedules"
                }, null, _parent2, _scopeId));
              }
              _push2(ssrRenderComponent(_sfc_main$3, {
                sortParam: sortParam.value,
                "onUpdate:sortParam": ($event) => sortParam.value = $event
              }, null, _parent2, _scopeId));
              _push2(`</div>`);
            } else {
              _push2(`<!---->`);
            }
            if (__props.schedulesCount) {
              _push2(`<div class="flex flex-col lg:flex-row items-center justify-between gap-3"${_scopeId}>`);
              _push2(ssrRenderComponent(_sfc_main$f, null, {
                default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                  if (_push3) {
                    _push3(`${ssrInterpolate(__props.schedulesCount)}`);
                  } else {
                    return [
                      createTextVNode(toDisplayString(__props.schedulesCount), 1)
                    ];
                  }
                }),
                _: 1
              }, _parent2, _scopeId));
              _push2(ssrRenderComponent(_sfc_main$4, { onChange: handleBulkAction }, null, _parent2, _scopeId));
              _push2(ssrRenderComponent(_sfc_main$g, {
                viewMode: viewMode.value,
                "onUpdate:viewMode": ($event) => viewMode.value = $event
              }, null, _parent2, _scopeId));
              _push2(`</div>`);
            } else {
              _push2(`<!---->`);
            }
            if (__props.schedulesCount) {
              _push2(`<div class="flex justify-center items-center flex-col md:flex-row mt-3"${_scopeId}>`);
              if (!__props.useServerProcessing) {
                _push2(ssrRenderComponent(_sfc_main$h, {
                  "current-page": currentPage.value,
                  "items-per-page": itemsPerPage.value,
                  "total-items": filteredSchedules.value.length,
                  "onUpdate:currentPage": ($event) => currentPage.value = $event
                }, null, _parent2, _scopeId));
              } else {
                _push2(ssrRenderComponent(_sfc_main$i, { pagination: __props.schedules }, null, _parent2, _scopeId));
              }
              _push2(`</div>`);
            } else {
              _push2(`<!---->`);
            }
            if (viewMode.value === "table") {
              _push2(ssrRenderComponent(_sfc_main$2, {
                schedules: displayedSchedules.value,
                "selected-schedules": selectedSchedules.value,
                onToggleActivity: toggleActivity,
                onDelete: confirmDelete,
                onClone: cloneSchedule,
                onUpdateSortOrder: handleSortOrderUpdate,
                onToggleSelect: toggleSelectSchedule,
                onToggleAll: toggleAll
              }, null, _parent2, _scopeId));
            } else {
              _push2(ssrRenderComponent(_sfc_main$1, {
                schedules: displayedSchedules.value,
                "selected-schedules": selectedSchedules.value,
                onToggleActivity: toggleActivity,
                onDelete: confirmDelete,
                onClone: cloneSchedule,
                onUpdateSortOrder: handleSortOrderUpdate,
                onToggleSelect: toggleSelectSchedule,
                onToggleAll: toggleAll
              }, null, _parent2, _scopeId));
            }
            if (__props.schedulesCount) {
              _push2(`<div class="flex justify-center items-center flex-col md:flex-row mt-3"${_scopeId}>`);
              if (!__props.useServerProcessing) {
                _push2(ssrRenderComponent(_sfc_main$h, {
                  "current-page": currentPage.value,
                  "items-per-page": itemsPerPage.value,
                  "total-items": filteredSchedules.value.length,
                  "onUpdate:currentPage": ($event) => currentPage.value = $event
                }, null, _parent2, _scopeId));
              } else {
                _push2(ssrRenderComponent(_sfc_main$i, { pagination: __props.schedules }, null, _parent2, _scopeId));
              }
              _push2(`</div>`);
            } else {
              _push2(`<!---->`);
            }
            _push2(`</div></div>`);
            _push2(ssrRenderComponent(_sfc_main$j, {
              show: showConfirmDeleteModal.value,
              onCancel: closeModal,
              onConfirm: deleteSchedule,
              cancelText: unref(t)("cancel"),
              confirmText: unref(t)("yesDelete"),
              onClose: closeModal
            }, null, _parent2, _scopeId));
          } else {
            return [
              createVNode("div", { class: "px-2 py-2 w-full max-w-12xl mx-auto" }, [
                createVNode("div", { class: "p-4 bg-slate-50 dark:bg-slate-700 border border-blue-400 dark:border-blue-200 overflow-hidden shadow-md shadow-gray-500 dark:shadow-slate-400 bg-opacity-95 dark:bg-opacity-95" }, [
                  createVNode("div", { class: "sm:flex sm:justify-between sm:items-center mb-3 gap-3" }, [
                    createVNode(_sfc_main$9, {
                      href: _ctx.route("admin.schoolCourseSchedules.create")
                    }, {
                      icon: withCtx(() => [
                        (openBlock(), createBlock("svg", {
                          class: "w-4 h-4 fill-current opacity-50 shrink-0",
                          viewBox: "0 0 16 16"
                        }, [
                          createVNode("path", { d: "M15 7H9V1c0-.6-.4-1-1-1S7 .4 7 1v6H1c-.6 0-1 .4-1 1s.4 1 1 1h6v6c0 .6.4 1 1 1s1-.4 1-1V9h6c.6 0 1-.4 1-1s-.4-1-1-1z" })
                        ]))
                      ]),
                      default: withCtx(() => [
                        createTextVNode(" " + toDisplayString(unref(t)("addSchedule")), 1)
                      ]),
                      _: 1
                    }, 8, ["href"]),
                    createVNode(_sfc_main$a, {
                      "setting-key": "adminSchoolCourseSchedulesProcessingMode",
                      mode: __props.adminSchoolCourseSchedulesProcessingMode,
                      "use-server-processing": __props.useServerProcessing,
                      total: __props.schedulesCount
                    }, null, 8, ["mode", "use-server-processing", "total"])
                  ]),
                  __props.schedulesCount && !__props.useServerProcessing ? (openBlock(), createBlock(_sfc_main$b, {
                    key: 0,
                    modelValue: searchQuery.value,
                    "onUpdate:modelValue": ($event) => searchQuery.value = $event,
                    placeholder: unref(t)("searchByName")
                  }, null, 8, ["modelValue", "onUpdate:modelValue", "placeholder"])) : createCommentVNode("", true),
                  __props.schedulesCount && __props.useServerProcessing ? (openBlock(), createBlock(_sfc_main$c, {
                    key: 1,
                    modelValue: searchQuery.value,
                    "onUpdate:modelValue": ($event) => searchQuery.value = $event
                  }, null, 8, ["modelValue", "onUpdate:modelValue"])) : createCommentVNode("", true),
                  __props.schedulesCount ? (openBlock(), createBlock("div", {
                    key: 2,
                    class: "flex justify-between items-center flex-col md:flex-row my-3 gap-3"
                  }, [
                    !__props.useServerProcessing ? (openBlock(), createBlock(_sfc_main$d, {
                      key: 0,
                      "items-per-page": itemsPerPage.value,
                      "onUpdate:itemsPerPage": ($event) => itemsPerPage.value = $event
                    }, null, 8, ["items-per-page", "onUpdate:itemsPerPage"])) : (openBlock(), createBlock(_sfc_main$e, {
                      key: 1,
                      "items-per-page": itemsPerPage.value,
                      "update-route": "admin.settings.updateAdminCountSchoolCourseSchedules"
                    }, null, 8, ["items-per-page"])),
                    createVNode(_sfc_main$3, {
                      sortParam: sortParam.value,
                      "onUpdate:sortParam": ($event) => sortParam.value = $event
                    }, null, 8, ["sortParam", "onUpdate:sortParam"])
                  ])) : createCommentVNode("", true),
                  __props.schedulesCount ? (openBlock(), createBlock("div", {
                    key: 3,
                    class: "flex flex-col lg:flex-row items-center justify-between gap-3"
                  }, [
                    createVNode(_sfc_main$f, null, {
                      default: withCtx(() => [
                        createTextVNode(toDisplayString(__props.schedulesCount), 1)
                      ]),
                      _: 1
                    }),
                    createVNode(_sfc_main$4, { onChange: handleBulkAction }),
                    createVNode(_sfc_main$g, {
                      viewMode: viewMode.value,
                      "onUpdate:viewMode": ($event) => viewMode.value = $event
                    }, null, 8, ["viewMode", "onUpdate:viewMode"])
                  ])) : createCommentVNode("", true),
                  __props.schedulesCount ? (openBlock(), createBlock("div", {
                    key: 4,
                    class: "flex justify-center items-center flex-col md:flex-row mt-3"
                  }, [
                    !__props.useServerProcessing ? (openBlock(), createBlock(_sfc_main$h, {
                      key: 0,
                      "current-page": currentPage.value,
                      "items-per-page": itemsPerPage.value,
                      "total-items": filteredSchedules.value.length,
                      "onUpdate:currentPage": ($event) => currentPage.value = $event
                    }, null, 8, ["current-page", "items-per-page", "total-items", "onUpdate:currentPage"])) : (openBlock(), createBlock(_sfc_main$i, {
                      key: 1,
                      pagination: __props.schedules
                    }, null, 8, ["pagination"]))
                  ])) : createCommentVNode("", true),
                  viewMode.value === "table" ? (openBlock(), createBlock(_sfc_main$2, {
                    key: 5,
                    schedules: displayedSchedules.value,
                    "selected-schedules": selectedSchedules.value,
                    onToggleActivity: toggleActivity,
                    onDelete: confirmDelete,
                    onClone: cloneSchedule,
                    onUpdateSortOrder: handleSortOrderUpdate,
                    onToggleSelect: toggleSelectSchedule,
                    onToggleAll: toggleAll
                  }, null, 8, ["schedules", "selected-schedules"])) : (openBlock(), createBlock(_sfc_main$1, {
                    key: 6,
                    schedules: displayedSchedules.value,
                    "selected-schedules": selectedSchedules.value,
                    onToggleActivity: toggleActivity,
                    onDelete: confirmDelete,
                    onClone: cloneSchedule,
                    onUpdateSortOrder: handleSortOrderUpdate,
                    onToggleSelect: toggleSelectSchedule,
                    onToggleAll: toggleAll
                  }, null, 8, ["schedules", "selected-schedules"])),
                  __props.schedulesCount ? (openBlock(), createBlock("div", {
                    key: 7,
                    class: "flex justify-center items-center flex-col md:flex-row mt-3"
                  }, [
                    !__props.useServerProcessing ? (openBlock(), createBlock(_sfc_main$h, {
                      key: 0,
                      "current-page": currentPage.value,
                      "items-per-page": itemsPerPage.value,
                      "total-items": filteredSchedules.value.length,
                      "onUpdate:currentPage": ($event) => currentPage.value = $event
                    }, null, 8, ["current-page", "items-per-page", "total-items", "onUpdate:currentPage"])) : (openBlock(), createBlock(_sfc_main$i, {
                      key: 1,
                      pagination: __props.schedules
                    }, null, 8, ["pagination"]))
                  ])) : createCommentVNode("", true)
                ])
              ]),
              createVNode(_sfc_main$j, {
                show: showConfirmDeleteModal.value,
                onCancel: closeModal,
                onConfirm: deleteSchedule,
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
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Pages/Admin/School/SchoolCourseSchedules/Index.vue");
  return _sfc_setup ? _sfc_setup(props, ctx) : void 0;
};
export {
  _sfc_main as default
};
