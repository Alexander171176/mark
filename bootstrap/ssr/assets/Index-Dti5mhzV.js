import { mergeProps, unref, useSSRContext, ref, watch, withCtx, createVNode, createBlock, openBlock, toDisplayString, createCommentVNode, computed, createTextVNode } from "vue";
import { ssrRenderAttrs, ssrInterpolate, ssrRenderAttr, ssrRenderComponent, ssrIncludeBooleanAttr } from "vue/server-renderer";
import { useI18n } from "vue-i18n";
import { useToast } from "vue-toastification";
import { router } from "@inertiajs/vue3";
import { A as AdminLayout } from "./AdminLayout-03yd3nxC.js";
import { T as TitlePage } from "./TitlePage-CBZZ8YZB.js";
import { _ as _sfc_main$g } from "./SearchInput-xZSYbbms.js";
import { _ as _sfc_main$h, a as _sfc_main$n } from "./ServerSearchInput-CxQUCmzV.js";
import { _ as _sfc_main$e } from "./DefaultButton-DMDyGacf.js";
import { _ as _sfc_main$i, a as _sfc_main$l, b as _sfc_main$m } from "./ItemsPerPageSelect-DOO-E4Z0.js";
import { _ as _sfc_main$k } from "./CountTable-p8tyXGUL.js";
import { _ as _sfc_main$j } from "./ServerItemsPerPageSelect-B_wSkKlU.js";
import { _ as _sfc_main$o } from "./DangerModal-DjIe5VDg.js";
import { _ as _sfc_main$f } from "./ProcessingModeSwitcher-BJvzFf6_.js";
import draggable from "vuedraggable";
import { _ as _sfc_main$c } from "./IconEdit-Bw90OQvk.js";
import { _ as _sfc_main$d } from "./DeleteIconButton-DLv2Mr1x.js";
import { _ as _sfc_main$b } from "./ActivityToggle-B1-nFMYK.js";
import { _ as _sfc_main$5, a as _sfc_main$6, b as _sfc_main$7 } from "./RightToggle-r8SYzaEU.js";
import { _ as _sfc_main$8, a as _sfc_main$9, b as _sfc_main$a } from "./HitButtonToggle-DYH18bkK.js";
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
      _push(`<div${ssrRenderAttrs(mergeProps({ class: "flex flex-col sm:flex-row items-center" }, _attrs))}><label class="block mb-2 sm:mb-0 sm:mr-2 font-semibold text-sm text-slate-700 dark:text-slate-300">${ssrInterpolate(unref(t)("bulkActions"))}</label><select class="w-auto px-3 py-0.5 form-select rounded-sm shadow-sm bg-white dark:bg-gray-200 text-gray-600 dark:text-gray-900 border border-slate-400 dark:border-slate-600"><option value=""> — ${ssrInterpolate(unref(t)("selectAction"))} — </option><option value="selectAll">${ssrInterpolate(unref(t)("selectAll"))}</option><option value="deselectAll">${ssrInterpolate(unref(t)("deselectAll"))}</option><option disabled>──────────────────</option><option value="activate">${ssrInterpolate(unref(t)("activate"))}</option><option value="deactivate">${ssrInterpolate(unref(t)("deactivate"))}</option><option disabled>──────────────────</option><option value="leftOn">${ssrInterpolate(unref(t)("left"))}: ON </option><option value="leftOff">${ssrInterpolate(unref(t)("left"))}: OFF </option><option value="mainOn">${ssrInterpolate(unref(t)("main"))}: ON </option><option value="mainOff">${ssrInterpolate(unref(t)("main"))}: OFF </option><option value="rightOn">${ssrInterpolate(unref(t)("right"))}: ON </option><option value="rightOff">${ssrInterpolate(unref(t)("right"))}: OFF </option><option disabled>──────────────────</option><option value="isNewOn">${ssrInterpolate(unref(t)("sortIsNew"))}: ON </option><option value="isNewOff">${ssrInterpolate(unref(t)("sortIsNew"))}: OFF </option><option value="isHitOn">${ssrInterpolate(unref(t)("sortIsHit"))}: ON </option><option value="isHitOff">${ssrInterpolate(unref(t)("sortIsHit"))}: OFF </option><option value="isSaleOn">${ssrInterpolate(unref(t)("sortIsSale"))}: ON </option><option value="isSaleOff">${ssrInterpolate(unref(t)("sortIsSale"))}: OFF </option><option disabled>──────────────────</option></select></div>`);
    };
  }
};
const _sfc_setup$4 = _sfc_main$4.setup;
_sfc_main$4.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Components/Admin/School/SchoolCourse/Select/BulkActionSelect.vue");
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
      _push(`<div${ssrRenderAttrs(mergeProps({ class: "flex justify-center items-center h-fit my-2" }, _attrs))}><label for="sortParam" class="hidden lg:block sm:mr-2 tracking-wider text-sm font-semibold text-slate-600 dark:text-slate-100">${ssrInterpolate(unref(t)("sort"))}</label><select id="sortParam"${ssrRenderAttr("value", __props.sortParam)} class="w-50 px-3 py-0.5 form-select bg-white dark:bg-gray-200 text-gray-600 dark:text-gray-900 border border-slate-400 dark:border-slate-600 rounded-sm shadow-sm"><option value="idDesc">${ssrInterpolate(unref(t)("idDesc"))}</option><option value="idAsc">${ssrInterpolate(unref(t)("idAsc"))}</option><option value="sortAsc">${ssrInterpolate(unref(t)("sortNumber"))} ↑</option><option value="sortDesc">${ssrInterpolate(unref(t)("sortNumber"))} ↓</option><option disabled>───────────────────</option><option value="titleAsc">${ssrInterpolate(unref(t)("title"))} ↑</option><option value="titleDesc">${ssrInterpolate(unref(t)("title"))} ↓</option><option disabled>───────────────────</option><option value="activity">${ssrInterpolate(unref(t)("active"))}</option><option value="inactive">${ssrInterpolate(unref(t)("inactive"))}</option><option disabled>───────────────────</option><option value="left">${ssrInterpolate(unref(t)("inLeft"))}</option><option value="noLeft">${ssrInterpolate(unref(t)("notLeft"))}</option><option disabled>───────────────────</option><option value="main">${ssrInterpolate(unref(t)("inMain"))}</option><option value="noMain">${ssrInterpolate(unref(t)("notMain"))}</option><option disabled>───────────────────</option><option value="right">${ssrInterpolate(unref(t)("inRight"))}</option><option value="noRight">${ssrInterpolate(unref(t)("notRight"))}</option><option disabled>───────────────────</option><option value="isNew">${ssrInterpolate(unref(t)("sortIsNew"))}</option><option value="isHit">${ssrInterpolate(unref(t)("sortIsHit"))}</option><option value="isSale">${ssrInterpolate(unref(t)("sortIsSale"))}</option><option disabled>───────────────────</option><option value="viewsDesc">${ssrInterpolate(unref(t)("views"))} 9→0</option><option value="viewsAsc">${ssrInterpolate(unref(t)("views"))} 0→9</option><option value="likesDesc">${ssrInterpolate(unref(t)("likes"))} 9→0</option><option value="likesAsc">${ssrInterpolate(unref(t)("likes"))} 0→9</option><option disabled>───────────────────</option><option value="popularityDesc">${ssrInterpolate(unref(t)("popularity"))} 9→0</option><option value="popularityAsc">${ssrInterpolate(unref(t)("popularity"))} 0→9</option><option value="ratingCountDesc">${ssrInterpolate(unref(t)("ratingCount"))} 9→0</option><option value="ratingCountAsc">${ssrInterpolate(unref(t)("ratingCount"))} 0→9</option><option value="ratingAvgDesc">${ssrInterpolate(unref(t)("ratingAvg"))} 9→0</option><option value="ratingAvgAsc">${ssrInterpolate(unref(t)("ratingAvg"))} 0→9</option><option disabled>───────────────────</option><option value="levelAsc">${ssrInterpolate(unref(t)("level"))} A→Z</option><option value="levelDesc">${ssrInterpolate(unref(t)("level"))} Z→A</option><option value="statusAsc">${ssrInterpolate(unref(t)("status"))} A→Z</option><option value="statusDesc">${ssrInterpolate(unref(t)("status"))} Z→A</option><option value="availabilityAsc">${ssrInterpolate(unref(t)("availability"))} A→Z</option><option value="availabilityDesc">${ssrInterpolate(unref(t)("availability"))} Z→A</option><option disabled>───────────────────</option><option value="difficultyDesc">${ssrInterpolate(unref(t)("sortDifficulty"))} 9→0</option><option value="difficultyAsc">${ssrInterpolate(unref(t)("sortDifficulty"))} 0→9</option><option value="durationDesc">${ssrInterpolate(unref(t)("duration"))} 9→0</option><option value="durationAsc">${ssrInterpolate(unref(t)("duration"))} 0→9</option><option disabled>───────────────────</option><option value="studentsCountDesc">${ssrInterpolate(unref(t)("sortStudentsCount"))} 9→0</option><option value="studentsCountAsc">${ssrInterpolate(unref(t)("sortStudentsCount"))} 0→9</option><option disabled>───────────────────</option><option value="modulesDesc">${ssrInterpolate(unref(t)("modules"))} 9→0</option><option value="modulesAsc">${ssrInterpolate(unref(t)("modules"))} 0→9</option><option value="lessonsDesc">${ssrInterpolate(unref(t)("lessons"))} 9→0</option><option value="lessonsAsc">${ssrInterpolate(unref(t)("lessons"))} 0→9</option><option value="tracksDesc">${ssrInterpolate(unref(t)("tracks"))} 9→0</option><option value="tracksAsc">${ssrInterpolate(unref(t)("tracks"))} 0→9</option><option value="hashtagsDesc">${ssrInterpolate(unref(t)("hashtags"))} 9→0</option><option value="hashtagsAsc">${ssrInterpolate(unref(t)("hashtags"))} 0→9</option><option disabled>───────────────────</option><option value="reviewsDesc">${ssrInterpolate(unref(t)("reviews"))} 9→0</option><option value="reviewsAsc">${ssrInterpolate(unref(t)("reviews"))} 0→9</option><option value="enrollmentsDesc">${ssrInterpolate(unref(t)("enrollments"))} 9→0</option><option value="enrollmentsAsc">${ssrInterpolate(unref(t)("enrollments"))} 0→9</option><option value="imagesDesc">${ssrInterpolate(unref(t)("images"))} 9→0</option><option value="imagesAsc">${ssrInterpolate(unref(t)("images"))} 0→9</option><option value="pricesDesc">${ssrInterpolate(unref(t)("prices"))} 9→0</option><option value="pricesAsc">${ssrInterpolate(unref(t)("prices"))} 0→9</option><option disabled>───────────────────</option><option value="publishedAtDesc">${ssrInterpolate(unref(t)("publishedAt"))} ↓</option><option value="publishedAtAsc">${ssrInterpolate(unref(t)("publishedAt"))} ↑</option><option value="createdAtDesc">${ssrInterpolate(unref(t)("createdAt"))} ↓</option><option value="createdAtAsc">${ssrInterpolate(unref(t)("createdAt"))} ↑</option><option value="updatedAtDesc">${ssrInterpolate(unref(t)("updatedAt"))} ↓</option><option value="updatedAtAsc">${ssrInterpolate(unref(t)("updatedAt"))} ↑</option><option disabled>───────────────────</option></select></div>`);
    };
  }
};
const _sfc_setup$3 = _sfc_main$3.setup;
_sfc_main$3.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Components/Admin/School/SchoolCourse/Sort/SortSelect.vue");
  return _sfc_setup$3 ? _sfc_setup$3(props, ctx) : void 0;
};
const _sfc_main$2 = {
  __name: "CourseTable",
  __ssrInlineRender: true,
  props: {
    courses: {
      type: Array,
      default: () => []
    },
    selectedCourses: {
      type: Array,
      default: () => []
    }
  },
  emits: [
    "toggle-activity",
    "toggle-left",
    "toggle-main",
    "toggle-right",
    "toggle-is-new",
    "toggle-is-hit",
    "toggle-is-sale",
    "edit",
    "delete",
    "update-sort-order",
    "toggle-select",
    "toggle-all"
  ],
  setup(__props, { emit: __emit }) {
    const { t } = useI18n();
    const props = __props;
    const courseStatusLabelKeyMap = {
      draft: "statusDraft",
      published: "statusPublished",
      archived: "statusArchived"
    };
    const courseAvailabilityLabelKeyMap = {
      unlisted: "availabilityUnlisted",
      public: "availabilityPublic",
      private: "availabilityPrivate"
    };
    const courseLevelLabelKeyMap = {
      beginner: "levelBeginner",
      intermediate: "levelIntermediate",
      advanced: "levelAdvanced"
    };
    const getCourseTitle = (course) => {
      var _a;
      return ((_a = course == null ? void 0 : course.translation) == null ? void 0 : _a.title) || "";
    };
    const getCourseSubtitle = (course) => {
      var _a;
      return ((_a = course == null ? void 0 : course.translation) == null ? void 0 : _a.subtitle) || "";
    };
    const getCourseStatusLabel = (status) => {
      return t(courseStatusLabelKeyMap[status] || status || "no");
    };
    const getCourseAvailabilityLabel = (availability) => {
      return t(courseAvailabilityLabelKeyMap[availability] || availability || "no");
    };
    const getCourseLevelLabel = (level) => {
      return t(courseLevelLabelKeyMap[level] || level || "no");
    };
    const emits = __emit;
    const localCourses = ref([]);
    watch(
      () => props.courses,
      (newVal) => {
        localCourses.value = JSON.parse(JSON.stringify(newVal || []));
      },
      { immediate: true, deep: true }
    );
    const handleDragEnd = () => {
      const newOrderIds = localCourses.value.map((course) => course.id);
      emits("update-sort-order", newOrderIds);
    };
    const getPrimaryImage = (course) => {
      if (course.images && course.images.length) {
        return [...course.images].sort((a, b) => a.order - b.order)[0];
      }
      return null;
    };
    const getInstructorName = (course) => {
      var _a, _b;
      const instructor = course == null ? void 0 : course.instructorProfile;
      return ((_a = instructor == null ? void 0 : instructor.translation) == null ? void 0 : _a.title) || ((_b = instructor == null ? void 0 : instructor.user) == null ? void 0 : _b.name) || "—";
    };
    const formatDate = (dateStr) => {
      if (!dateStr) return "";
      const d = new Date(dateStr);
      if (isNaN(d)) return "";
      return d.toLocaleDateString("ru-RU", {
        year: "numeric",
        month: "long",
        day: "numeric"
      });
    };
    return (_ctx, _push, _parent, _attrs) => {
      _push(`<div${ssrRenderAttrs(mergeProps({ class: "bg-white dark:bg-slate-700 shadow-lg rounded-sm border border-slate-200 dark:border-slate-600 relative" }, _attrs))}><div class="flex items-center justify-between px-3 py-2 border-b border-slate-400 dark:border-slate-500"><div class="text-xs text-slate-600 dark:text-slate-200">${ssrInterpolate(unref(t)("selected"))}: ${ssrInterpolate(__props.selectedCourses.length)}</div>`);
      if (localCourses.value.length) {
        _push(`<label class="flex items-center text-xs text-slate-600 dark:text-slate-200 cursor-pointer"><span>${ssrInterpolate(unref(t)("selectAll"))}</span><input type="checkbox" class="mx-2"></label>`);
      } else {
        _push(`<!---->`);
      }
      _push(`</div><div class="overflow-x-auto">`);
      if (__props.courses.length > 0) {
        _push(`<table class="table-auto w-full text-slate-700 dark:text-slate-100"><thead class="text-sm uppercase bg-slate-200 dark:bg-cyan-900 border border-solid border-gray-300 dark:border-gray-700"><tr><th class="px-2 py-3 w-px"><svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 fill-current text-slate-800 dark:text-slate-200" height="24" width="24" viewBox="0 0 24 24"><path d="M12.707,2.293a1,1,0,0,0-1.414,0l-5,5A1,1,0,0,0,7.707,8.707L12,4.414l4.293,4.293a1,1,0,0,0,1.414-1.414Z"></path><path d="M16.293,15.293,12,19.586,7.707,15.293a1,1,0,0,0-1.414,1.414l5,5a1,1,0,0,0,1.414,0l5-5a1,1,0,0,0-1.414-1.414Z"></path></svg></th><th class="px-2 py-3 w-px"><div class="font-medium text-center">${ssrInterpolate(unref(t)("id"))}</div></th><th class="px-2 py-3 whitespace-nowrap"><div class="flex justify-center"${ssrRenderAttr("title", unref(t)("image"))}><svg class="w-6 h-6 fill-current shrink-0" viewBox="0 0 512 512"><path d="M0 96C0 60.7 28.7 32 64 32l384 0c35.3 0 64 28.7 64 64l0 320c0 35.3-28.7 64-64 64L64 480c-35.3 0-64-28.7-64-64L0 96zM323.8 202.5c-4.5-6.6-11.9-10.5-19.8-10.5s-15.4 3.9-19.8 10.5l-87 127.6L170.7 297c-4.6-5.7-11.5-9-18.7-9s-14.2 3.3-18.7 9l-64 80c-5.8 7.2-6.9 17.1-2.9 25.4s12.4 13.6 21.6 13.6l96 0 32 0 208 0c8.9 0 17.1-4.9 21.2-12.8s3.6-17.4-1.4-24.7l-120-176zM112 192a48 48 0 1 0 0-96 48 48 0 1 0 0 96z"></path></svg></div></th><th class="px-2 py-3 whitespace-nowrap"><div class="font-semibold text-left">${ssrInterpolate(unref(t)("course"))}</div></th><th class="px-2 py-3 whitespace-nowrap"><div class="flex justify-center"${ssrRenderAttr("title", `${unref(t)("availability")} / ${unref(t)("level")}`)}><svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 fill-current text-slate-600 dark:text-slate-200" height="24" width="24" viewBox="0 0 24 24"><path class="fill-current text-teal-600 dark:text-teal-300" d="M12,24a1,1,0,0,1,0-2A10,10,0,0,0,12,2a1,1,0,0,1,0-2,12,12,0,0,1,0,24Z"></path><path class="fill-current text-teal-600 dark:text-teal-300" d="M1.045,13.913a1,1,0,0,1-1-.919C.022,12.665,0,12.336,0,12s.022-.665.049-.994a1,1,0,1,1,1.993.162C2.021,11.442,2,11.719,2,12s.021.558.042.832a1,1,0,0,1-.916,1.078Q1.086,13.913,1.045,13.913Z"></path><path class="fill-current text-teal-600 dark:text-teal-300" d="M6.243,3.641a1,1,0,0,1-.526-1.852,12.022,12.022,0,0,1,1.774-.9,1,1,0,1,1,.754,1.851,10.133,10.133,0,0,0-1.478.757A.993.993,0,0,1,6.243,3.641Z"></path><path class="fill-current text-teal-600 dark:text-teal-300" d="M2.188,8.044a.988.988,0,0,1-.451-.108A1,1,0,0,1,1.3,6.592,12.131,12.131,0,0,1,2.342,4.9,1,1,0,0,1,3.953,6.083,10.1,10.1,0,0,0,3.081,7.5,1,1,0,0,1,2.188,8.044Z"></path><path class="fill-current text-teal-600 dark:text-teal-300" d="M3.128,19.482a1,1,0,0,1-.808-.409,12.049,12.049,0,0,1-1.041-1.7,1,1,0,1,1,1.787-.9,10.047,10.047,0,0,0,.868,1.418,1,1,0,0,1-.217,1.4A.986.986,0,0,1,3.128,19.482Z"></path><path d="M7.853,23.185a.983.983,0,0,1-.377-.075A11.879,11.879,0,0,1,5.7,22.2,1,1,0,0,1,6.75,20.5a10.041,10.041,0,0,0,1.48.761,1,1,0,0,1-.377,1.926Z"></path><path d="M10,17a1,1,0,0,1-.707-.293l-4-4a1,1,0,0,1,1.414-1.414L10,14.586l7.293-7.293a1,1,0,1,1,1.414,1.414l-8,8A1,1,0,0,1,10,17Z"></path></svg></div></th><th class="px-2 py-3 whitespace-nowrap"><div class="flex justify-center"${ssrRenderAttr("title", unref(t)("duration"))}><svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" height="24" width="24" viewBox="0 0 24 24"><path class="fill-current text-violet-700 dark:text-violet-300" d="M22,13a1,1,0,0,1,0-2h1.949A12.006,12.006,0,0,0,13,.051V2a1,1,0,0,1-2,0V.051A12.006,12.006,0,0,0,.051,11H2a1,1,0,0,1,0,2H.051A12.006,12.006,0,0,0,11,23.949V22a1,1,0,0,1,2,0v1.949A12.006,12.006,0,0,0,23.949,13Zm-6,0H12a1,1,0,0,1-.832-.445l-4-6a1,1,0,1,1,1.664-1.11L12.535,11H16a1,1,0,0,1,0,2Z"></path></svg></div></th><th class="px-2 py-3 whitespace-nowrap"><div class="flex justify-center"${ssrRenderAttr("title", unref(t)("views"))}><svg class="w-4 h-4 fill-current shrink-0" viewBox="0 0 16 16"><path class="fill-current text-blue-600 dark:text-blue-300" d="M8 2C3.246 2 .251 7.29.127 7.515a.998.998 0 0 0 .002.975c.07.125 1.044 1.801 2.695 3.274C4.738 13.582 6.283 14 8 14c4.706 0 7.743-5.284 7.872-5.507a1 1 0 0 0 0-.98A13.292 13.292 0 0 0 8 2zm0 10a4 4 0 1 1 0-8 4 4 0 0 1 0 8zm0-6a2 2 0 1 0 0 4 2 2 0 0 0 0-4z"></path></svg></div></th><th class="px-2 py-3 whitespace-nowrap"><div class="flex justify-center"${ssrRenderAttr("title", unref(t)("popularity"))}><div class="flex justify-center"><svg class="h-8 w-8 fill-current" viewBox="0 0 32 32"><path class="fill-current text-red-400 dark:text-red-300" d="M22.682 11.318A4.485 4.485 0 0019.5 10a4.377 4.377 0 00-3.5 1.707A4.383 4.383 0 0012.5 10a4.5 4.5 0 00-3.182 7.682L16 24l6.682-6.318a4.5 4.5 0 000-6.364zm-1.4 4.933L16 21.247l-5.285-5A2.5 2.5 0 0112.5 12c1.437 0 2.312.681 3.5 2.625C17.187 12.681 18.062 12 19.5 12a2.5 2.5 0 011.785 4.251h-.003z"></path></svg></div></div></th><th class="px-2 py-3 whitespace-nowrap"><div class="flex justify-center"${ssrRenderAttr("title", unref(t)("ratingCount"))}><svg xmlns="http://www.w3.org/2000/svg" height="24" width="24" viewBox="0 0 24 24" class="shrink-0 h-4 w-4"><path class="fill-current text-red-400 dark:text-red-300" d="M14.586,9.439S15.7,2.858,11.138,0A8.055,8.055,0,0,1,8.1,5.831C6.149,7.546,2.481,11.4,2.52,15.51A9.435,9.435,0,0,0,7.7,24a5.975,5.975,0,0,1,2.091-4.132,4.877,4.877,0,0,0,1.869-3.278,8.786,8.786,0,0,1,4.652,7.322v.02a8.827,8.827,0,0,0,5.137-7.659c.324-3.863-1.792-9.112-3.668-10.828A10.192,10.192,0,0,1,14.586,9.439Z"></path></svg></div></th><th class="px-2 py-3 whitespace-nowrap"><div class="flex justify-center"${ssrRenderAttr("title", unref(t)("ratingAvg"))}><svg xmlns="http://www.w3.org/2000/svg" height="24" width="24" viewBox="0 0 24 24" class="shrink-0 h-4 w-4"><path class="fill-current text-red-400 dark:text-red-300" d="M12.746,1.464l3.11,6.3L22.81,8.776a.831.831,0,0,1,.461,1.418l-5.033,4.9,1.188,6.926a.832.832,0,0,1-1.207.877L12,19.632,5.78,22.9a.833.833,0,0,1-1.207-.878L5.761,15.1l-5.033-4.9a.831.831,0,0,1,.461-1.418L8.143,7.765l3.11-6.3A.833.833,0,0,1,12.746,1.464Z"></path></svg></div></th><th class="px-2 py-3 whitespace-nowrap"><div class="flex justify-center"${ssrRenderAttr("title", unref(t)("likes"))}><svg xmlns="http://www.w3.org/2000/svg" height="24" width="24" viewBox="0 0 24 24" class="shrink-0 h-4 w-4"><path class="fill-current text-red-400 dark:text-red-300" d="M3,9H1a1,1,0,0,0-1,1V22a1,1,0,0,0,1,1H4V10A1,1,0,0,0,3,9Z"></path><path class="fill-current text-red-400 dark:text-red-300" d="M21.882,8.133A2.986,2.986,0,0,0,21,8H15V5c0-3.824-2.589-4.942-3.958-5a1.017,1.017,0,0,0-.734.277A1,1,0,0,0,10,1V5.638l-4,4.8V23H18.23A2.985,2.985,0,0,0,21.1,20.882l2.769-9A3,3,0,0,0,21.882,8.133Z"></path></svg></div></th><th class="px-2 py-3 whitespace-nowrap"><div class="font-semibold text-end">${ssrInterpolate(unref(t)("show"))}</div></th><th class="px-2 py-3 whitespace-nowrap"><div class="font-semibold text-end">${ssrInterpolate(unref(t)("actions"))}</div></th><th class="px-2 py-3 whitespace-nowrap"><div class="text-center"><input type="checkbox"></div></th></tr></thead>`);
        _push(ssrRenderComponent(unref(draggable), {
          tag: "tbody",
          modelValue: localCourses.value,
          "onUpdate:modelValue": ($event) => localCourses.value = $event,
          onEnd: handleDragEnd,
          "item-key": "id",
          handle: ".handle"
        }, {
          item: withCtx(({ element: course }, _push2, _parent2, _scopeId) => {
            var _a, _b, _c, _d;
            if (_push2) {
              _push2(`<tr class="text-sm font-semibold border-b-2 hover:bg-slate-100 dark:hover:bg-cyan-800"${_scopeId}><td class="px-2 py-1 text-center cursor-move handle"${_scopeId}><svg class="w-4 h-4 text-gray-500 dark:text-gray-300" fill="currentColor" viewBox="0 0 20 20"${_scopeId}><path d="M7 4h2v2H7V4zm4 0h2v2h-2V4zM7 8h2v2H7V8zm4 0h2v2h-2V8zM7 12h2v2H7v-2zm4 0h2v2h-2v-2z"${_scopeId}></path></svg></td><td class="px-2 py-3 whitespace-nowrap"${_scopeId}><div class="text-center text-xs text-slate-800 dark:text-blue-200"${ssrRenderAttr("title", `[${course.sort}] ${formatDate(course.published_at)}`)}${_scopeId}>${ssrInterpolate(course.id)}</div></td><td class="px-2 py-3 whitespace-nowrap"${_scopeId}><div class="flex justify-center"${ssrRenderAttr("title", ((_a = course == null ? void 0 : course.instructorProfile) == null ? void 0 : _a.title) || (course == null ? void 0 : course.title) || "")}${_scopeId}>`);
              if (course.images && course.images.length) {
                _push2(`<img${ssrRenderAttr("src", getPrimaryImage(course).webp_url || getPrimaryImage(course).url)}${ssrRenderAttr("alt", getPrimaryImage(course).alt || unref(t)("defaultImageAlt"))}${ssrRenderAttr(
                  "title",
                  getPrimaryImage(course).caption || unref(t)("currentImage")
                )} class="h-8 w-12 object-cover rounded-sm"${_scopeId}>`);
              } else {
                _push2(`<img src="/storage/school/school_course_images/default-image.png"${ssrRenderAttr("alt", unref(t)("defaultImageTitle"))} class="h-8 w-12 object-cover rounded-sm"${_scopeId}>`);
              }
              _push2(`</div></td><td class="px-2 py-3 whitespace-nowrap"${_scopeId}><div class="min-w-0"${_scopeId}><a${ssrRenderAttr("href", `/school/courses/${encodeURIComponent(course.slug)}`)} class="text-xs text-sky-600 dark:text-sky-200 hover:underline hover:text-sky-600 dark:hover:text-sky-200" target="_blank" rel="noopener noreferrer"${ssrRenderAttr("title", getCourseSubtitle(course) || getCourseTitle(course))}${_scopeId}>${ssrInterpolate(getCourseTitle(course))}</a><div class="text-xs text-teal-700 dark:text-teal-200 truncate"${ssrRenderAttr("title", `ID: ${((_b = course == null ? void 0 : course.instructorProfile) == null ? void 0 : _b.id) ?? "-"} | ${getInstructorName(course)}`)}${_scopeId}>${ssrInterpolate(getInstructorName(course))}</div></div></td><td class="px-2 py-3 whitespace-nowrap"${_scopeId}><div class="text-center text-xs text-fuchsia-800 dark:text-fuchsia-400"${ssrRenderAttr("title", `${unref(t)("status")} : ${getCourseStatusLabel(course.status)}`)}${_scopeId}>${ssrInterpolate(getCourseAvailabilityLabel(course.availability))}</div><div class="text-center text-xs text-teal-600 dark:text-teal-300"${ssrRenderAttr("title", `${unref(t)("level")} : ${getCourseLevelLabel(course.level)} (${course.difficulty || "-"})`)}${_scopeId}>${ssrInterpolate(getCourseLevelLabel(course.level))}</div></td><td class="px-2 py-3 whitespace-nowrap"${_scopeId}><div class="text-center text-xs text-violet-700 dark:text-violet-300"${ssrRenderAttr("title", `${unref(t)("duration")} [${course.duration}]`)}${_scopeId}>${ssrInterpolate(course.duration)}</div></td><td class="px-2 py-3 whitespace-nowrap"${_scopeId}><div class="text-center text-xs text-blue-700 dark:text-blue-300"${ssrRenderAttr("title", course.students_count)}${_scopeId}>${ssrInterpolate(course.views)}</div></td><td class="px-2 py-3 whitespace-nowrap"${_scopeId}><div class="text-center text-xs text-rose-500 dark:text-rose-300"${_scopeId}>${ssrInterpolate(course.popularity)}</div></td><td class="px-2 py-3 whitespace-nowrap"${_scopeId}><div class="text-center text-xs"${_scopeId}>${ssrInterpolate(course.rating_count)}</div></td><td class="px-2 py-3 whitespace-nowrap"${_scopeId}><div class="text-center text-xs text-rose-500 dark:text-rose-300"${_scopeId}>${ssrInterpolate(course.rating_avg ?? "no")}</div></td><td class="px-2 py-3 whitespace-nowrap"${_scopeId}><div class="text-center text-xs"${_scopeId}>${ssrInterpolate(course.likes)}</div></td><td class="px-2 py-3 whitespace-nowrap"${_scopeId}><div class="flex flex-col justify-center items-center gap-1"${_scopeId}><div class="flex flex-row items-center justify-center gap-1"${_scopeId}>`);
              _push2(ssrRenderComponent(_sfc_main$5, {
                isActive: course.left,
                onToggleLeft: ($event) => _ctx.$emit("toggle-left", course),
                title: course.left ? unref(t)("enabled") : unref(t)("disabled")
              }, null, _parent2, _scopeId));
              _push2(ssrRenderComponent(_sfc_main$6, {
                isActive: course.main,
                onToggleMain: ($event) => _ctx.$emit("toggle-main", course),
                title: course.main ? unref(t)("enabled") : unref(t)("disabled")
              }, null, _parent2, _scopeId));
              _push2(ssrRenderComponent(_sfc_main$7, {
                isActive: course.right,
                onToggleRight: ($event) => _ctx.$emit("toggle-right", course),
                title: course.right ? unref(t)("enabled") : unref(t)("disabled")
              }, null, _parent2, _scopeId));
              _push2(`</div><div class="flex flex-row items-center justify-center gap-1"${_scopeId}>`);
              _push2(ssrRenderComponent(_sfc_main$8, {
                isActive: course.is_new,
                onToggleIsNew: ($event) => _ctx.$emit("toggle-is-new", course),
                title: course.is_new ? unref(t)("enabled") : unref(t)("disabled")
              }, null, _parent2, _scopeId));
              _push2(ssrRenderComponent(_sfc_main$9, {
                isActive: course.is_hit,
                onToggleIsHit: ($event) => _ctx.$emit("toggle-is-hit", course),
                title: course.is_hit ? unref(t)("enabled") : unref(t)("disabled")
              }, null, _parent2, _scopeId));
              _push2(ssrRenderComponent(_sfc_main$a, {
                isActive: course.is_sale,
                onToggleIsSale: ($event) => _ctx.$emit("toggle-is-sale", course),
                title: course.is_sale ? unref(t)("enabled") : unref(t)("disabled")
              }, null, _parent2, _scopeId));
              _push2(`</div></div></td><td class="px-2 py-3 whitespace-nowrap"${_scopeId}><div class="flex flex-row items-center justify-center gap-1"${_scopeId}>`);
              _push2(ssrRenderComponent(_sfc_main$b, {
                isActive: course.activity,
                onToggleActivity: ($event) => _ctx.$emit("toggle-activity", course),
                title: course.activity ? unref(t)("enabled") : unref(t)("disabled")
              }, null, _parent2, _scopeId));
              _push2(ssrRenderComponent(_sfc_main$c, {
                href: _ctx.route("admin.schoolCourses.edit", { schoolCourse: course.id })
              }, null, _parent2, _scopeId));
              _push2(ssrRenderComponent(_sfc_main$d, {
                onDelete: ($event) => _ctx.$emit("delete", course)
              }, null, _parent2, _scopeId));
              _push2(`</div></td><td class="px-2 py-3 whitespace-nowrap"${_scopeId}><div class="text-center"${_scopeId}><input type="checkbox"${ssrIncludeBooleanAttr(__props.selectedCourses.includes(course.id)) ? " checked" : ""}${_scopeId}></div></td></tr>`);
            } else {
              return [
                createVNode("tr", { class: "text-sm font-semibold border-b-2 hover:bg-slate-100 dark:hover:bg-cyan-800" }, [
                  createVNode("td", { class: "px-2 py-1 text-center cursor-move handle" }, [
                    (openBlock(), createBlock("svg", {
                      class: "w-4 h-4 text-gray-500 dark:text-gray-300",
                      fill: "currentColor",
                      viewBox: "0 0 20 20"
                    }, [
                      createVNode("path", { d: "M7 4h2v2H7V4zm4 0h2v2h-2V4zM7 8h2v2H7V8zm4 0h2v2h-2V8zM7 12h2v2H7v-2zm4 0h2v2h-2v-2z" })
                    ]))
                  ]),
                  createVNode("td", { class: "px-2 py-3 whitespace-nowrap" }, [
                    createVNode("div", {
                      class: "text-center text-xs text-slate-800 dark:text-blue-200",
                      title: `[${course.sort}] ${formatDate(course.published_at)}`
                    }, toDisplayString(course.id), 9, ["title"])
                  ]),
                  createVNode("td", { class: "px-2 py-3 whitespace-nowrap" }, [
                    createVNode("div", {
                      class: "flex justify-center",
                      title: ((_c = course == null ? void 0 : course.instructorProfile) == null ? void 0 : _c.title) || (course == null ? void 0 : course.title) || ""
                    }, [
                      course.images && course.images.length ? (openBlock(), createBlock("img", {
                        key: 0,
                        src: getPrimaryImage(course).webp_url || getPrimaryImage(course).url,
                        alt: getPrimaryImage(course).alt || unref(t)("defaultImageAlt"),
                        title: getPrimaryImage(course).caption || unref(t)("currentImage"),
                        class: "h-8 w-12 object-cover rounded-sm"
                      }, null, 8, ["src", "alt", "title"])) : (openBlock(), createBlock("img", {
                        key: 1,
                        src: "/storage/school/school_course_images/default-image.png",
                        alt: unref(t)("defaultImageTitle"),
                        class: "h-8 w-12 object-cover rounded-sm"
                      }, null, 8, ["alt"]))
                    ], 8, ["title"])
                  ]),
                  createVNode("td", { class: "px-2 py-3 whitespace-nowrap" }, [
                    createVNode("div", { class: "min-w-0" }, [
                      createVNode("a", {
                        href: `/school/courses/${encodeURIComponent(course.slug)}`,
                        class: "text-xs text-sky-600 dark:text-sky-200 hover:underline hover:text-sky-600 dark:hover:text-sky-200",
                        target: "_blank",
                        rel: "noopener noreferrer",
                        title: getCourseSubtitle(course) || getCourseTitle(course)
                      }, toDisplayString(getCourseTitle(course)), 9, ["href", "title"]),
                      createVNode("div", {
                        class: "text-xs text-teal-700 dark:text-teal-200 truncate",
                        title: `ID: ${((_d = course == null ? void 0 : course.instructorProfile) == null ? void 0 : _d.id) ?? "-"} | ${getInstructorName(course)}`
                      }, toDisplayString(getInstructorName(course)), 9, ["title"])
                    ])
                  ]),
                  createVNode("td", { class: "px-2 py-3 whitespace-nowrap" }, [
                    createVNode("div", {
                      class: "text-center text-xs text-fuchsia-800 dark:text-fuchsia-400",
                      title: `${unref(t)("status")} : ${getCourseStatusLabel(course.status)}`
                    }, toDisplayString(getCourseAvailabilityLabel(course.availability)), 9, ["title"]),
                    createVNode("div", {
                      class: "text-center text-xs text-teal-600 dark:text-teal-300",
                      title: `${unref(t)("level")} : ${getCourseLevelLabel(course.level)} (${course.difficulty || "-"})`
                    }, toDisplayString(getCourseLevelLabel(course.level)), 9, ["title"])
                  ]),
                  createVNode("td", { class: "px-2 py-3 whitespace-nowrap" }, [
                    createVNode("div", {
                      class: "text-center text-xs text-violet-700 dark:text-violet-300",
                      title: `${unref(t)("duration")} [${course.duration}]`
                    }, toDisplayString(course.duration), 9, ["title"])
                  ]),
                  createVNode("td", { class: "px-2 py-3 whitespace-nowrap" }, [
                    createVNode("div", {
                      class: "text-center text-xs text-blue-700 dark:text-blue-300",
                      title: course.students_count
                    }, toDisplayString(course.views), 9, ["title"])
                  ]),
                  createVNode("td", { class: "px-2 py-3 whitespace-nowrap" }, [
                    createVNode("div", { class: "text-center text-xs text-rose-500 dark:text-rose-300" }, toDisplayString(course.popularity), 1)
                  ]),
                  createVNode("td", { class: "px-2 py-3 whitespace-nowrap" }, [
                    createVNode("div", { class: "text-center text-xs" }, toDisplayString(course.rating_count), 1)
                  ]),
                  createVNode("td", { class: "px-2 py-3 whitespace-nowrap" }, [
                    createVNode("div", { class: "text-center text-xs text-rose-500 dark:text-rose-300" }, toDisplayString(course.rating_avg ?? "no"), 1)
                  ]),
                  createVNode("td", { class: "px-2 py-3 whitespace-nowrap" }, [
                    createVNode("div", { class: "text-center text-xs" }, toDisplayString(course.likes), 1)
                  ]),
                  createVNode("td", { class: "px-2 py-3 whitespace-nowrap" }, [
                    createVNode("div", { class: "flex flex-col justify-center items-center gap-1" }, [
                      createVNode("div", { class: "flex flex-row items-center justify-center gap-1" }, [
                        createVNode(_sfc_main$5, {
                          isActive: course.left,
                          onToggleLeft: ($event) => _ctx.$emit("toggle-left", course),
                          title: course.left ? unref(t)("enabled") : unref(t)("disabled")
                        }, null, 8, ["isActive", "onToggleLeft", "title"]),
                        createVNode(_sfc_main$6, {
                          isActive: course.main,
                          onToggleMain: ($event) => _ctx.$emit("toggle-main", course),
                          title: course.main ? unref(t)("enabled") : unref(t)("disabled")
                        }, null, 8, ["isActive", "onToggleMain", "title"]),
                        createVNode(_sfc_main$7, {
                          isActive: course.right,
                          onToggleRight: ($event) => _ctx.$emit("toggle-right", course),
                          title: course.right ? unref(t)("enabled") : unref(t)("disabled")
                        }, null, 8, ["isActive", "onToggleRight", "title"])
                      ]),
                      createVNode("div", { class: "flex flex-row items-center justify-center gap-1" }, [
                        createVNode(_sfc_main$8, {
                          isActive: course.is_new,
                          onToggleIsNew: ($event) => _ctx.$emit("toggle-is-new", course),
                          title: course.is_new ? unref(t)("enabled") : unref(t)("disabled")
                        }, null, 8, ["isActive", "onToggleIsNew", "title"]),
                        createVNode(_sfc_main$9, {
                          isActive: course.is_hit,
                          onToggleIsHit: ($event) => _ctx.$emit("toggle-is-hit", course),
                          title: course.is_hit ? unref(t)("enabled") : unref(t)("disabled")
                        }, null, 8, ["isActive", "onToggleIsHit", "title"]),
                        createVNode(_sfc_main$a, {
                          isActive: course.is_sale,
                          onToggleIsSale: ($event) => _ctx.$emit("toggle-is-sale", course),
                          title: course.is_sale ? unref(t)("enabled") : unref(t)("disabled")
                        }, null, 8, ["isActive", "onToggleIsSale", "title"])
                      ])
                    ])
                  ]),
                  createVNode("td", { class: "px-2 py-3 whitespace-nowrap" }, [
                    createVNode("div", { class: "flex flex-row items-center justify-center gap-1" }, [
                      createVNode(_sfc_main$b, {
                        isActive: course.activity,
                        onToggleActivity: ($event) => _ctx.$emit("toggle-activity", course),
                        title: course.activity ? unref(t)("enabled") : unref(t)("disabled")
                      }, null, 8, ["isActive", "onToggleActivity", "title"]),
                      createVNode(_sfc_main$c, {
                        href: _ctx.route("admin.schoolCourses.edit", { schoolCourse: course.id })
                      }, null, 8, ["href"]),
                      createVNode(_sfc_main$d, {
                        onDelete: ($event) => _ctx.$emit("delete", course)
                      }, null, 8, ["onDelete"])
                    ])
                  ]),
                  createVNode("td", { class: "px-2 py-3 whitespace-nowrap" }, [
                    createVNode("div", { class: "text-center" }, [
                      createVNode("input", {
                        type: "checkbox",
                        checked: __props.selectedCourses.includes(course.id),
                        onChange: ($event) => _ctx.$emit("toggle-select", course.id)
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
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Components/Admin/School/SchoolCourse/Table/CourseTable.vue");
  return _sfc_setup$2 ? _sfc_setup$2(props, ctx) : void 0;
};
const _sfc_main$1 = {
  __name: "CourseCardGrid",
  __ssrInlineRender: true,
  props: {
    courses: {
      type: Array,
      default: () => []
    },
    selectedCourses: {
      type: Array,
      default: () => []
    }
  },
  emits: [
    "toggle-activity",
    "toggle-left",
    "toggle-main",
    "toggle-right",
    "toggle-is-new",
    "toggle-is-hit",
    "toggle-is-sale",
    "edit",
    "delete",
    "update-sort-order",
    "toggle-select",
    "toggle-all"
  ],
  setup(__props, { emit: __emit }) {
    const { t } = useI18n();
    const props = __props;
    const emits = __emit;
    const courseStatusLabelKeyMap = {
      draft: "statusDraft",
      published: "statusPublished",
      archived: "statusArchived"
    };
    const courseAvailabilityLabelKeyMap = {
      unlisted: "availabilityUnlisted",
      public: "availabilityPublic",
      private: "availabilityPrivate"
    };
    const courseLevelLabelKeyMap = {
      beginner: "levelBeginner",
      intermediate: "levelIntermediate",
      advanced: "levelAdvanced"
    };
    const getCourseTitle = (course) => {
      var _a;
      return ((_a = course == null ? void 0 : course.translation) == null ? void 0 : _a.title) || "";
    };
    const getCourseSubtitle = (course) => {
      var _a;
      return ((_a = course == null ? void 0 : course.translation) == null ? void 0 : _a.subtitle) || "";
    };
    const getCourseStatusLabel = (status) => {
      if (!status) return "—";
      const key = courseStatusLabelKeyMap[status];
      return key ? t(key) : status;
    };
    const getCourseAvailabilityLabel = (availability) => {
      if (!availability) return "—";
      const key = courseAvailabilityLabelKeyMap[availability];
      return key ? t(key) : availability;
    };
    const getCourseLevelLabel = (level) => {
      if (!level) return "—";
      const key = courseLevelLabelKeyMap[level];
      return key ? t(key) : level;
    };
    const localCourses = ref([]);
    watch(
      () => props.courses,
      (newVal) => {
        localCourses.value = JSON.parse(JSON.stringify(newVal || []));
      },
      { immediate: true, deep: true }
    );
    const handleDragEnd = () => {
      const newOrderIds = localCourses.value.map((course) => course.id);
      emits("update-sort-order", newOrderIds);
    };
    const getPrimaryImage = (course) => {
      if (course.images && course.images.length) {
        return [...course.images].sort((a, b) => a.order - b.order)[0];
      }
      return null;
    };
    const getInstructorProfile = (course) => {
      return (course == null ? void 0 : course.instructorProfile) || null;
    };
    const getInstructorImages = (course) => {
      const instructorProfile = getInstructorProfile(course);
      return Array.isArray(instructorProfile == null ? void 0 : instructorProfile.images) ? instructorProfile.images : [];
    };
    const getInstructorPrimaryImage = (course) => {
      const images = getInstructorImages(course);
      if (!images.length) return null;
      return [...images].sort((a, b) => {
        var _a, _b;
        const aOrder = Number((a == null ? void 0 : a.order) ?? ((_a = a == null ? void 0 : a.pivot) == null ? void 0 : _a.order) ?? 999999);
        const bOrder = Number((b == null ? void 0 : b.order) ?? ((_b = b == null ? void 0 : b.pivot) == null ? void 0 : _b.order) ?? 999999);
        return aOrder - bOrder;
      })[0];
    };
    const getInstructorImageUrl = (course) => {
      const image = getInstructorPrimaryImage(course);
      return (image == null ? void 0 : image.webp_url) || (image == null ? void 0 : image.url) || (image == null ? void 0 : image.image_url) || (image == null ? void 0 : image.thumb_url) || null;
    };
    const getInstructorName = (course) => {
      var _a, _b;
      const instructor = course == null ? void 0 : course.instructorProfile;
      return ((_a = instructor == null ? void 0 : instructor.translation) == null ? void 0 : _a.title) || ((_b = instructor == null ? void 0 : instructor.user) == null ? void 0 : _b.name) || "—";
    };
    const formatDate = (dateStr) => {
      if (!dateStr) return "";
      const d = new Date(dateStr);
      if (isNaN(d)) return "";
      return d.toLocaleDateString("ru-RU", {
        year: "numeric",
        month: "short",
        day: "numeric"
      });
    };
    return (_ctx, _push, _parent, _attrs) => {
      _push(`<div${ssrRenderAttrs(mergeProps({ class: "bg-white dark:bg-slate-700 shadow-lg rounded-sm border border-slate-400 dark:border-slate-500 relative" }, _attrs))}><div class="flex items-center justify-between px-3 py-2 border-b border-slate-400 dark:border-slate-500"><div class="text-xs text-slate-600 dark:text-slate-200">${ssrInterpolate(unref(t)("selected"))}: ${ssrInterpolate(__props.selectedCourses.length)}</div>`);
      if (localCourses.value.length) {
        _push(`<label class="flex items-center text-xs text-slate-600 dark:text-slate-200 cursor-pointer"><span>${ssrInterpolate(unref(t)("selectAll"))}</span><input type="checkbox" class="mx-2"></label>`);
      } else {
        _push(`<!---->`);
      }
      _push(`</div>`);
      if (localCourses.value.length) {
        _push(`<div class="p-3">`);
        _push(ssrRenderComponent(unref(draggable), {
          tag: "div",
          modelValue: localCourses.value,
          "onUpdate:modelValue": ($event) => localCourses.value = $event,
          "item-key": "id",
          onEnd: handleDragEnd,
          handle: ".drag-handle",
          class: "grid gap-3 grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4"
        }, {
          item: withCtx(({ element: course }, _push2, _parent2, _scopeId) => {
            var _a, _b, _c, _d;
            if (_push2) {
              _push2(`<div class="relative flex flex-col h-full rounded-md border border-slate-400 dark:border-slate-500 bg-slate-50/70 dark:bg-slate-800/80 shadow-sm hover:shadow-md transition-shadow duration-150"${_scopeId}><div class="flex items-center justify-between px-2 py-1 border-b border-dashed border-slate-400 dark:border-slate-500"${_scopeId}><div class="flex items-center space-x-2"${_scopeId}><button type="button" class="drag-handle text-slate-400 hover:text-slate-700 dark:hover:text-slate-100"${ssrRenderAttr("title", unref(t)("dragDrop"))}${_scopeId}><svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"${_scopeId}><path d="M7 4h2v2H7V4zm4 0h2v2h-2V4zM7 8h2v2H7V8zm4 0h2v2h-2V8zM7 12h2v2H7v-2zm4 0h2v2h-2v-2z"${_scopeId}></path></svg></button><div class="text-[10px] font-semibold px-1.5 py-0.5 rounded-sm border border-gray-400 bg-slate-200 dark:bg-slate-700 text-slate-800 dark:text-blue-100"${ssrRenderAttr("title", `[${course.sort}] ${formatDate(course.published_at)}`)}${_scopeId}> ID: ${ssrInterpolate(course.id)}</div></div><div class="flex items-center space-x-2"${_scopeId}><span class="text-[10px] px-1.5 py-0.5 rounded-sm border border-gray-400 bg-fuchsia-100 dark:bg-fuchsia-900/50 text-fuchsia-700 dark:text-fuchsia-300"${ssrRenderAttr("title", unref(t)("availability"))}${_scopeId}>${ssrInterpolate(getCourseAvailabilityLabel(course.availability))}</span><input type="checkbox"${ssrIncludeBooleanAttr(__props.selectedCourses.includes(course.id)) ? " checked" : ""}${_scopeId}></div></div><div class="relative w-full h-32 bg-slate-200 dark:bg-slate-900"${_scopeId}>`);
              if ((_a = course.images) == null ? void 0 : _a.length) {
                _push2(`<img${ssrRenderAttr("src", getPrimaryImage(course).webp_url || getPrimaryImage(course).url)}${ssrRenderAttr("alt", getPrimaryImage(course).alt || unref(t)("defaultImageAlt"))}${ssrRenderAttr("title", getPrimaryImage(course).caption || unref(t)("currentImage"))} class="w-full h-full object-cover"${_scopeId}>`);
              } else {
                _push2(`<img src="/storage/school/school_course_images/default-image.png"${ssrRenderAttr("alt", unref(t)("defaultImageTitle"))} class="w-full h-full object-cover"${_scopeId}>`);
              }
              _push2(`</div><div class="flex flex-col flex-1 px-3 py-2 space-y-1"${_scopeId}><a${ssrRenderAttr("href", `/school/courses/${encodeURIComponent(course.slug)}`)} target="_blank" rel="noopener noreferrer" class="text-sm font-semibold text-sky-700 dark:text-sky-200 hover:underline line-clamp-2 text-center"${ssrRenderAttr("title", getCourseSubtitle(course) || getCourseTitle(course))}${_scopeId}>${ssrInterpolate(getCourseTitle(course))}</a><div class="flex flex-wrap justify-center gap-1 mt-1 text-[10px] font-semibold"${_scopeId}><span class="px-2 py-0.5 rounded-sm bg-sky-100 dark:bg-sky-900 border border-gray-400 text-sky-700 dark:text-sky-200"${ssrRenderAttr("title", unref(t)("status"))}${_scopeId}>${ssrInterpolate(getCourseStatusLabel(course.status))}</span><span class="px-2 py-0.5 rounded-sm bg-emerald-100 dark:bg-emerald-900 border border-gray-400 text-emerald-700 dark:text-emerald-200"${ssrRenderAttr("title", `${unref(t)("level")} : ${getCourseLevelLabel(course.level)}`)}${_scopeId}>${ssrInterpolate(unref(t)("level"))}: ${ssrInterpolate(getCourseLevelLabel(course.level))}</span></div><div class="flex flex-col justify-center text-gray-700 dark:text-gray-400 text-center text-[11px] mt-2"${_scopeId}><div${_scopeId}>${ssrInterpolate(unref(t)("duration"))}: ${ssrInterpolate(course.duration || "—")}</div></div><div class="flex flex-wrap justify-center gap-3 mt-2 text-[11px] text-slate-900 dark:text-slate-200"${_scopeId}>`);
              if (course.views) {
                _push2(`<span${_scopeId}>${ssrInterpolate(unref(t)("views"))}: ${ssrInterpolate(course.views)}</span>`);
              } else {
                _push2(`<!---->`);
              }
              if (course.likes) {
                _push2(`<span${_scopeId}>${ssrInterpolate(unref(t)("likes"))}: ${ssrInterpolate(course.likes)}</span>`);
              } else {
                _push2(`<!---->`);
              }
              _push2(`</div><div class="flex flex-col justify-center mt-2 text-center text-[11px] text-slate-900 dark:text-slate-200"${_scopeId}>`);
              if (course.rating_count) {
                _push2(`<span${_scopeId}>${ssrInterpolate(unref(t)("ratingCount"))}: ${ssrInterpolate(course.rating_count)}</span>`);
              } else {
                _push2(`<!---->`);
              }
              if (course.rating_avg) {
                _push2(`<span${_scopeId}>${ssrInterpolate(unref(t)("ratingAvg"))}: ${ssrInterpolate(course.rating_avg)}</span>`);
              } else {
                _push2(`<!---->`);
              }
              _push2(`</div><div class="flex items-center justify-center gap-2 mt-1"${_scopeId}>`);
              if (getInstructorImageUrl(course)) {
                _push2(`<img${ssrRenderAttr("src", getInstructorImageUrl(course))}${ssrRenderAttr("alt", getInstructorName(course))} class="h-7 w-7 rounded-full object-cover ring-1 ring-slate-300 dark:ring-slate-600 shrink-0" loading="lazy"${_scopeId}>`);
              } else {
                _push2(`<!---->`);
              }
              _push2(`<div class="text-[11px] font-semibold text-teal-700 dark:text-teal-200 text-center"${ssrRenderAttr("title", `ID: ${((_b = course == null ? void 0 : course.instructorProfile) == null ? void 0 : _b.id) ?? "-"} | ${getInstructorName(course)}`)}${_scopeId}>${ssrInterpolate(getInstructorName(course))}</div></div></div><div class="flex items-center justify-center px-3 py-2 border-t border-dashed border-slate-400 dark:border-slate-500"${_scopeId}><div class="flex flex-wrap items-center justify-center gap-1"${_scopeId}><div class="flex flex-row items-center space-x-7"${_scopeId}><div class="flex flex-row items-center space-x-1"${_scopeId}>`);
              _push2(ssrRenderComponent(_sfc_main$5, {
                isActive: course.left,
                onToggleLeft: ($event) => _ctx.$emit("toggle-left", course),
                title: course.left ? unref(t)("enabled") : unref(t)("disabled")
              }, null, _parent2, _scopeId));
              _push2(ssrRenderComponent(_sfc_main$6, {
                isActive: course.main,
                onToggleMain: ($event) => _ctx.$emit("toggle-main", course),
                title: course.main ? unref(t)("enabled") : unref(t)("disabled")
              }, null, _parent2, _scopeId));
              _push2(ssrRenderComponent(_sfc_main$7, {
                isActive: course.right,
                onToggleRight: ($event) => _ctx.$emit("toggle-right", course),
                title: course.right ? unref(t)("enabled") : unref(t)("disabled")
              }, null, _parent2, _scopeId));
              _push2(`</div><div class="flex flex-row items-center space-x-1"${_scopeId}>`);
              _push2(ssrRenderComponent(_sfc_main$8, {
                isActive: course.is_new,
                onToggleIsNew: ($event) => _ctx.$emit("toggle-is-new", course),
                title: course.is_new ? unref(t)("enabled") : unref(t)("disabled")
              }, null, _parent2, _scopeId));
              _push2(ssrRenderComponent(_sfc_main$9, {
                isActive: course.is_hit,
                onToggleIsHit: ($event) => _ctx.$emit("toggle-is-hit", course),
                title: course.is_hit ? unref(t)("enabled") : unref(t)("disabled")
              }, null, _parent2, _scopeId));
              _push2(ssrRenderComponent(_sfc_main$a, {
                isActive: course.is_sale,
                onToggleIsSale: ($event) => _ctx.$emit("toggle-is-sale", course),
                title: course.is_sale ? unref(t)("enabled") : unref(t)("disabled")
              }, null, _parent2, _scopeId));
              _push2(`</div></div><div class="flex flex-row items-center space-x-1"${_scopeId}>`);
              _push2(ssrRenderComponent(_sfc_main$b, {
                isActive: course.activity,
                onToggleActivity: ($event) => _ctx.$emit("toggle-activity", course),
                title: course.activity ? unref(t)("enabled") : unref(t)("disabled")
              }, null, _parent2, _scopeId));
              _push2(ssrRenderComponent(_sfc_main$c, {
                href: _ctx.route("admin.schoolCourses.edit", { schoolCourse: course.id })
              }, null, _parent2, _scopeId));
              _push2(ssrRenderComponent(_sfc_main$d, {
                onDelete: ($event) => _ctx.$emit("delete", course)
              }, null, _parent2, _scopeId));
              _push2(`</div></div></div></div>`);
            } else {
              return [
                createVNode("div", { class: "relative flex flex-col h-full rounded-md border border-slate-400 dark:border-slate-500 bg-slate-50/70 dark:bg-slate-800/80 shadow-sm hover:shadow-md transition-shadow duration-150" }, [
                  createVNode("div", { class: "flex items-center justify-between px-2 py-1 border-b border-dashed border-slate-400 dark:border-slate-500" }, [
                    createVNode("div", { class: "flex items-center space-x-2" }, [
                      createVNode("button", {
                        type: "button",
                        class: "drag-handle text-slate-400 hover:text-slate-700 dark:hover:text-slate-100",
                        title: unref(t)("dragDrop")
                      }, [
                        (openBlock(), createBlock("svg", {
                          class: "w-4 h-4",
                          fill: "currentColor",
                          viewBox: "0 0 20 20"
                        }, [
                          createVNode("path", { d: "M7 4h2v2H7V4zm4 0h2v2h-2V4zM7 8h2v2H7V8zm4 0h2v2h-2V8zM7 12h2v2H7v-2zm4 0h2v2h-2v-2z" })
                        ]))
                      ], 8, ["title"]),
                      createVNode("div", {
                        class: "text-[10px] font-semibold px-1.5 py-0.5 rounded-sm border border-gray-400 bg-slate-200 dark:bg-slate-700 text-slate-800 dark:text-blue-100",
                        title: `[${course.sort}] ${formatDate(course.published_at)}`
                      }, " ID: " + toDisplayString(course.id), 9, ["title"])
                    ]),
                    createVNode("div", { class: "flex items-center space-x-2" }, [
                      createVNode("span", {
                        class: "text-[10px] px-1.5 py-0.5 rounded-sm border border-gray-400 bg-fuchsia-100 dark:bg-fuchsia-900/50 text-fuchsia-700 dark:text-fuchsia-300",
                        title: unref(t)("availability")
                      }, toDisplayString(getCourseAvailabilityLabel(course.availability)), 9, ["title"]),
                      createVNode("input", {
                        type: "checkbox",
                        checked: __props.selectedCourses.includes(course.id),
                        onChange: ($event) => _ctx.$emit("toggle-select", course.id)
                      }, null, 40, ["checked", "onChange"])
                    ])
                  ]),
                  createVNode("div", { class: "relative w-full h-32 bg-slate-200 dark:bg-slate-900" }, [
                    ((_c = course.images) == null ? void 0 : _c.length) ? (openBlock(), createBlock("img", {
                      key: 0,
                      src: getPrimaryImage(course).webp_url || getPrimaryImage(course).url,
                      alt: getPrimaryImage(course).alt || unref(t)("defaultImageAlt"),
                      title: getPrimaryImage(course).caption || unref(t)("currentImage"),
                      class: "w-full h-full object-cover"
                    }, null, 8, ["src", "alt", "title"])) : (openBlock(), createBlock("img", {
                      key: 1,
                      src: "/storage/school/school_course_images/default-image.png",
                      alt: unref(t)("defaultImageTitle"),
                      class: "w-full h-full object-cover"
                    }, null, 8, ["alt"]))
                  ]),
                  createVNode("div", { class: "flex flex-col flex-1 px-3 py-2 space-y-1" }, [
                    createVNode("a", {
                      href: `/school/courses/${encodeURIComponent(course.slug)}`,
                      target: "_blank",
                      rel: "noopener noreferrer",
                      class: "text-sm font-semibold text-sky-700 dark:text-sky-200 hover:underline line-clamp-2 text-center",
                      title: getCourseSubtitle(course) || getCourseTitle(course)
                    }, toDisplayString(getCourseTitle(course)), 9, ["href", "title"]),
                    createVNode("div", { class: "flex flex-wrap justify-center gap-1 mt-1 text-[10px] font-semibold" }, [
                      createVNode("span", {
                        class: "px-2 py-0.5 rounded-sm bg-sky-100 dark:bg-sky-900 border border-gray-400 text-sky-700 dark:text-sky-200",
                        title: unref(t)("status")
                      }, toDisplayString(getCourseStatusLabel(course.status)), 9, ["title"]),
                      createVNode("span", {
                        class: "px-2 py-0.5 rounded-sm bg-emerald-100 dark:bg-emerald-900 border border-gray-400 text-emerald-700 dark:text-emerald-200",
                        title: `${unref(t)("level")} : ${getCourseLevelLabel(course.level)}`
                      }, toDisplayString(unref(t)("level")) + ": " + toDisplayString(getCourseLevelLabel(course.level)), 9, ["title"])
                    ]),
                    createVNode("div", { class: "flex flex-col justify-center text-gray-700 dark:text-gray-400 text-center text-[11px] mt-2" }, [
                      createVNode("div", null, toDisplayString(unref(t)("duration")) + ": " + toDisplayString(course.duration || "—"), 1)
                    ]),
                    createVNode("div", { class: "flex flex-wrap justify-center gap-3 mt-2 text-[11px] text-slate-900 dark:text-slate-200" }, [
                      course.views ? (openBlock(), createBlock("span", { key: 0 }, toDisplayString(unref(t)("views")) + ": " + toDisplayString(course.views), 1)) : createCommentVNode("", true),
                      course.likes ? (openBlock(), createBlock("span", { key: 1 }, toDisplayString(unref(t)("likes")) + ": " + toDisplayString(course.likes), 1)) : createCommentVNode("", true)
                    ]),
                    createVNode("div", { class: "flex flex-col justify-center mt-2 text-center text-[11px] text-slate-900 dark:text-slate-200" }, [
                      course.rating_count ? (openBlock(), createBlock("span", { key: 0 }, toDisplayString(unref(t)("ratingCount")) + ": " + toDisplayString(course.rating_count), 1)) : createCommentVNode("", true),
                      course.rating_avg ? (openBlock(), createBlock("span", { key: 1 }, toDisplayString(unref(t)("ratingAvg")) + ": " + toDisplayString(course.rating_avg), 1)) : createCommentVNode("", true)
                    ]),
                    createVNode("div", { class: "flex items-center justify-center gap-2 mt-1" }, [
                      getInstructorImageUrl(course) ? (openBlock(), createBlock("img", {
                        key: 0,
                        src: getInstructorImageUrl(course),
                        alt: getInstructorName(course),
                        class: "h-7 w-7 rounded-full object-cover ring-1 ring-slate-300 dark:ring-slate-600 shrink-0",
                        loading: "lazy"
                      }, null, 8, ["src", "alt"])) : createCommentVNode("", true),
                      createVNode("div", {
                        class: "text-[11px] font-semibold text-teal-700 dark:text-teal-200 text-center",
                        title: `ID: ${((_d = course == null ? void 0 : course.instructorProfile) == null ? void 0 : _d.id) ?? "-"} | ${getInstructorName(course)}`
                      }, toDisplayString(getInstructorName(course)), 9, ["title"])
                    ])
                  ]),
                  createVNode("div", { class: "flex items-center justify-center px-3 py-2 border-t border-dashed border-slate-400 dark:border-slate-500" }, [
                    createVNode("div", { class: "flex flex-wrap items-center justify-center gap-1" }, [
                      createVNode("div", { class: "flex flex-row items-center space-x-7" }, [
                        createVNode("div", { class: "flex flex-row items-center space-x-1" }, [
                          createVNode(_sfc_main$5, {
                            isActive: course.left,
                            onToggleLeft: ($event) => _ctx.$emit("toggle-left", course),
                            title: course.left ? unref(t)("enabled") : unref(t)("disabled")
                          }, null, 8, ["isActive", "onToggleLeft", "title"]),
                          createVNode(_sfc_main$6, {
                            isActive: course.main,
                            onToggleMain: ($event) => _ctx.$emit("toggle-main", course),
                            title: course.main ? unref(t)("enabled") : unref(t)("disabled")
                          }, null, 8, ["isActive", "onToggleMain", "title"]),
                          createVNode(_sfc_main$7, {
                            isActive: course.right,
                            onToggleRight: ($event) => _ctx.$emit("toggle-right", course),
                            title: course.right ? unref(t)("enabled") : unref(t)("disabled")
                          }, null, 8, ["isActive", "onToggleRight", "title"])
                        ]),
                        createVNode("div", { class: "flex flex-row items-center space-x-1" }, [
                          createVNode(_sfc_main$8, {
                            isActive: course.is_new,
                            onToggleIsNew: ($event) => _ctx.$emit("toggle-is-new", course),
                            title: course.is_new ? unref(t)("enabled") : unref(t)("disabled")
                          }, null, 8, ["isActive", "onToggleIsNew", "title"]),
                          createVNode(_sfc_main$9, {
                            isActive: course.is_hit,
                            onToggleIsHit: ($event) => _ctx.$emit("toggle-is-hit", course),
                            title: course.is_hit ? unref(t)("enabled") : unref(t)("disabled")
                          }, null, 8, ["isActive", "onToggleIsHit", "title"]),
                          createVNode(_sfc_main$a, {
                            isActive: course.is_sale,
                            onToggleIsSale: ($event) => _ctx.$emit("toggle-is-sale", course),
                            title: course.is_sale ? unref(t)("enabled") : unref(t)("disabled")
                          }, null, 8, ["isActive", "onToggleIsSale", "title"])
                        ])
                      ]),
                      createVNode("div", { class: "flex flex-row items-center space-x-1" }, [
                        createVNode(_sfc_main$b, {
                          isActive: course.activity,
                          onToggleActivity: ($event) => _ctx.$emit("toggle-activity", course),
                          title: course.activity ? unref(t)("enabled") : unref(t)("disabled")
                        }, null, 8, ["isActive", "onToggleActivity", "title"]),
                        createVNode(_sfc_main$c, {
                          href: _ctx.route("admin.schoolCourses.edit", { schoolCourse: course.id })
                        }, null, 8, ["href"]),
                        createVNode(_sfc_main$d, {
                          onDelete: ($event) => _ctx.$emit("delete", course)
                        }, null, 8, ["onDelete"])
                      ])
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
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Components/Admin/School/SchoolCourse/View/CourseCardGrid.vue");
  return _sfc_setup$1 ? _sfc_setup$1(props, ctx) : void 0;
};
const _sfc_main = {
  __name: "Index",
  __ssrInlineRender: true,
  props: {
    currentLocale: { type: String, default: "" },
    availableLocales: { type: Array, default: () => [] },
    adminSchoolCoursesProcessingMode: { type: String, default: "frontend" },
    useServerProcessing: { type: Boolean, default: false },
    courses: { type: [Array, Object], default: () => [] },
    coursesCount: { type: Number, default: 0 },
    adminSchoolCoursesPerPage: { type: Number, default: 6 },
    adminSchoolCoursesDefaultSort: { type: String, default: "idDesc" },
    sortParam: { type: String, default: "" },
    search: { type: String, default: "" },
    errors: { type: Object, default: () => ({}) }
  },
  setup(__props) {
    const { t } = useI18n();
    const toast = useToast();
    const props = __props;
    const viewMode = ref(localStorage.getItem("admin_view_mode_courses") || "table");
    watch(viewMode, (val) => {
      localStorage.setItem("admin_view_mode_courses", val);
    });
    const coursesList = computed(() => {
      var _a;
      if (Array.isArray(props.courses)) {
        return props.courses;
      }
      if (Array.isArray((_a = props.courses) == null ? void 0 : _a.data)) {
        return props.courses.data;
      }
      return [];
    });
    const localCourses = ref([]);
    watch(
      coursesList,
      (newVal) => {
        localCourses.value = JSON.parse(JSON.stringify(newVal || []));
      },
      { immediate: true, deep: true }
    );
    const itemsPerPage = ref(props.adminSchoolCoursesPerPage || 6);
    watch(itemsPerPage, (newVal) => {
      router.put(
        route("admin.settings.updateAdminCountSchoolCourses"),
        { value: newVal },
        {
          preserveScroll: true,
          preserveState: true,
          onSuccess: () => toast.info(`Показ ${newVal} элементов на странице.`),
          onError: (errors) => toast.error(errors.value || "Ошибка обновления кол-ва элементов.")
        }
      );
    });
    const sortParam = ref(props.sortParam || props.adminSchoolCoursesDefaultSort || "idDesc");
    watch(sortParam, (newVal) => {
      currentPage.value = 1;
      router.put(
        route("admin.settings.updateAdminSortSchoolCourses"),
        { value: newVal },
        {
          preserveScroll: true,
          preserveState: true,
          onSuccess: () => {
            if (props.useServerProcessing) {
              router.get(
                window.location.pathname,
                {
                  ...Object.fromEntries(new URLSearchParams(window.location.search)),
                  sort: newVal || void 0,
                  page: void 0
                },
                {
                  preserveScroll: true,
                  preserveState: false,
                  replace: true
                }
              );
            }
            toast.info("Сортировка успешно изменена");
          },
          onError: (errors) => {
            toast.error(errors.value || "Ошибка обновления сортировки.");
          }
        }
      );
    });
    const searchQuery = ref(props.search || "");
    const currentPage = ref(1);
    const normalize = (value) => (value ?? "").toString().trim().toLowerCase();
    const safeNumber = (value) => {
      const number = Number(value);
      return Number.isFinite(number) ? number : 0;
    };
    const safeDate = (value) => {
      const time = new Date(value || 0).getTime();
      return Number.isFinite(time) ? time : 0;
    };
    const getCourseTitle = (course) => {
      var _a;
      return ((_a = course == null ? void 0 : course.translation) == null ? void 0 : _a.title) || `ID: ${course == null ? void 0 : course.id}`;
    };
    const getCourseSubtitle = (course) => {
      var _a;
      return ((_a = course == null ? void 0 : course.translation) == null ? void 0 : _a.subtitle) || "";
    };
    const getCourseShort = (course) => {
      var _a;
      return ((_a = course == null ? void 0 : course.translation) == null ? void 0 : _a.short) || "";
    };
    const getCourseDescription = (course) => {
      var _a;
      return ((_a = course == null ? void 0 : course.translation) == null ? void 0 : _a.description) || "";
    };
    const getNestedTitle = (item) => {
      var _a, _b, _c;
      return ((_a = item == null ? void 0 : item.translation) == null ? void 0 : _a.title) || ((_b = item == null ? void 0 : item.translation) == null ? void 0 : _b.name) || ((_c = item == null ? void 0 : item.user) == null ? void 0 : _c.name) || "";
    };
    const getInstructorTitle = (course) => {
      return getNestedTitle((course == null ? void 0 : course.instructorProfile) || (course == null ? void 0 : course.instructor_profile));
    };
    const getTracksText = (course) => {
      const tracks = Array.isArray(course == null ? void 0 : course.tracks) ? course.tracks : [];
      return tracks.map(getNestedTitle).filter(Boolean).join(" ");
    };
    const getHashtagsText = (course) => {
      const hashtags = Array.isArray(course == null ? void 0 : course.hashtags) ? course.hashtags : [];
      return hashtags.map(getNestedTitle).filter(Boolean).join(" ");
    };
    const byNumberAsc = (field) => (a, b) => safeNumber(a == null ? void 0 : a[field]) - safeNumber(b == null ? void 0 : b[field]) || safeNumber(a == null ? void 0 : a.id) - safeNumber(b == null ? void 0 : b.id);
    const byNumberDesc = (field) => (a, b) => safeNumber(b == null ? void 0 : b[field]) - safeNumber(a == null ? void 0 : a[field]) || safeNumber(b == null ? void 0 : b.id) - safeNumber(a == null ? void 0 : a.id);
    const byStringAsc = (field) => (a, b) => normalize(a == null ? void 0 : a[field]).localeCompare(normalize(b == null ? void 0 : b[field]), props.currentLocale) || safeNumber(a == null ? void 0 : a.id) - safeNumber(b == null ? void 0 : b.id);
    const byStringDesc = (field) => (a, b) => normalize(b == null ? void 0 : b[field]).localeCompare(normalize(a == null ? void 0 : a[field]), props.currentLocale) || safeNumber(b == null ? void 0 : b.id) - safeNumber(a == null ? void 0 : a.id);
    const sortCourses = (items) => {
      const list = (items || []).slice();
      if (sortParam.value === "activity") return list.filter((item) => !!item.activity);
      if (sortParam.value === "inactive") return list.filter((item) => !item.activity);
      if (sortParam.value === "left") return list.filter((item) => !!item.left);
      if (sortParam.value === "noLeft") return list.filter((item) => !item.left);
      if (sortParam.value === "main") return list.filter((item) => !!item.main);
      if (sortParam.value === "noMain") return list.filter((item) => !item.main);
      if (sortParam.value === "right") return list.filter((item) => !!item.right);
      if (sortParam.value === "noRight") return list.filter((item) => !item.right);
      if (sortParam.value === "isNew") return list.filter((item) => !!item.is_new);
      if (sortParam.value === "isHit") return list.filter((item) => !!item.is_hit);
      if (sortParam.value === "isSale") return list.filter((item) => !!item.is_sale);
      const sortMap = {
        idAsc: byNumberAsc("id"),
        idDesc: byNumberDesc("id"),
        sortAsc: byNumberAsc("sort"),
        sortDesc: byNumberDesc("sort"),
        titleAsc: (a, b) => normalize(getCourseTitle(a)).localeCompare(normalize(getCourseTitle(b)), props.currentLocale) || safeNumber(a == null ? void 0 : a.id) - safeNumber(b == null ? void 0 : b.id),
        titleDesc: (a, b) => normalize(getCourseTitle(b)).localeCompare(normalize(getCourseTitle(a)), props.currentLocale) || safeNumber(b == null ? void 0 : b.id) - safeNumber(a == null ? void 0 : a.id),
        levelAsc: byStringAsc("level"),
        levelDesc: byStringDesc("level"),
        statusAsc: byStringAsc("status"),
        statusDesc: byStringDesc("status"),
        availabilityAsc: byStringAsc("availability"),
        availabilityDesc: byStringDesc("availability"),
        studentsCountAsc: byNumberAsc("students_count"),
        studentsCountDesc: byNumberDesc("students_count"),
        viewsAsc: byNumberAsc("views"),
        viewsDesc: byNumberDesc("views"),
        likesAsc: byNumberAsc("likes"),
        likesDesc: byNumberDesc("likes"),
        popularityAsc: byNumberAsc("popularity"),
        popularityDesc: byNumberDesc("popularity"),
        ratingCountAsc: byNumberAsc("rating_count"),
        ratingCountDesc: byNumberDesc("rating_count"),
        ratingAvgAsc: byNumberAsc("rating_avg"),
        ratingAvgDesc: byNumberDesc("rating_avg"),
        difficultyAsc: byNumberAsc("difficulty"),
        difficultyDesc: byNumberDesc("difficulty"),
        durationAsc: byNumberAsc("duration"),
        durationDesc: byNumberDesc("duration"),
        modulesAsc: byNumberAsc("modules_count"),
        modulesDesc: byNumberDesc("modules_count"),
        lessonsAsc: byNumberAsc("lessons_count"),
        lessonsDesc: byNumberDesc("lessons_count"),
        tracksAsc: byNumberAsc("tracks_count"),
        tracksDesc: byNumberDesc("tracks_count"),
        hashtagsAsc: byNumberAsc("hashtags_count"),
        hashtagsDesc: byNumberDesc("hashtags_count"),
        imagesAsc: byNumberAsc("images_count"),
        imagesDesc: byNumberDesc("images_count"),
        pricesAsc: byNumberAsc("prices_count"),
        pricesDesc: byNumberDesc("prices_count"),
        reviewsAsc: byNumberAsc("reviews_count"),
        reviewsDesc: byNumberDesc("reviews_count"),
        enrollmentsAsc: byNumberAsc("enrollments_count"),
        enrollmentsDesc: byNumberDesc("enrollments_count"),
        activityAsc: byNumberAsc("activity"),
        activityDesc: byNumberDesc("activity"),
        isNewAsc: byNumberAsc("is_new"),
        isNewDesc: byNumberDesc("is_new"),
        isHitAsc: byNumberAsc("is_hit"),
        isHitDesc: byNumberDesc("is_hit"),
        isSaleAsc: byNumberAsc("is_sale"),
        isSaleDesc: byNumberDesc("is_sale"),
        publishedAtAsc: (a, b) => safeDate(a == null ? void 0 : a.published_at) - safeDate(b == null ? void 0 : b.published_at) || safeNumber(a == null ? void 0 : a.id) - safeNumber(b == null ? void 0 : b.id),
        publishedAtDesc: (a, b) => safeDate(b == null ? void 0 : b.published_at) - safeDate(a == null ? void 0 : a.published_at) || safeNumber(b == null ? void 0 : b.id) - safeNumber(a == null ? void 0 : a.id),
        dateAsc: (a, b) => safeDate(a == null ? void 0 : a.published_at) - safeDate(b == null ? void 0 : b.published_at) || safeNumber(a == null ? void 0 : a.id) - safeNumber(b == null ? void 0 : b.id),
        dateDesc: (a, b) => safeDate(b == null ? void 0 : b.published_at) - safeDate(a == null ? void 0 : a.published_at) || safeNumber(b == null ? void 0 : b.id) - safeNumber(a == null ? void 0 : a.id),
        createdAtAsc: (a, b) => safeDate(a == null ? void 0 : a.created_at) - safeDate(b == null ? void 0 : b.created_at) || safeNumber(a == null ? void 0 : a.id) - safeNumber(b == null ? void 0 : b.id),
        createdAtDesc: (a, b) => safeDate(b == null ? void 0 : b.created_at) - safeDate(a == null ? void 0 : a.created_at) || safeNumber(b == null ? void 0 : b.id) - safeNumber(a == null ? void 0 : a.id),
        updatedAtAsc: (a, b) => safeDate(a == null ? void 0 : a.updated_at) - safeDate(b == null ? void 0 : b.updated_at) || safeNumber(a == null ? void 0 : a.id) - safeNumber(b == null ? void 0 : b.id),
        updatedAtDesc: (a, b) => safeDate(b == null ? void 0 : b.updated_at) - safeDate(a == null ? void 0 : a.updated_at) || safeNumber(b == null ? void 0 : b.id) - safeNumber(a == null ? void 0 : a.id)
      };
      return sortMap[sortParam.value] ? list.sort(sortMap[sortParam.value]) : list;
    };
    const filteredCourses = computed(() => {
      let filtered = localCourses.value || [];
      const query = normalize(searchQuery.value);
      if (!query) {
        return sortCourses(filtered);
      }
      filtered = filtered.filter((course) => {
        var _a;
        const title = normalize(getCourseTitle(course));
        const subtitle = normalize(getCourseSubtitle(course));
        const slug = normalize((course == null ? void 0 : course.slug) || ((_a = course == null ? void 0 : course.translation) == null ? void 0 : _a.slug));
        const short = normalize(getCourseShort(course));
        const description = normalize(getCourseDescription(course));
        const instructor = normalize(getInstructorTitle(course));
        const tracks = normalize(getTracksText(course));
        const hashtags = normalize(getHashtagsText(course));
        return title.includes(query) || subtitle.includes(query) || slug.includes(query) || short.includes(query) || description.includes(query) || instructor.includes(query) || tracks.includes(query) || hashtags.includes(query);
      });
      return sortCourses(filtered);
    });
    const paginatedCourses = computed(() => {
      const per = Number(itemsPerPage.value || 10);
      const start = (currentPage.value - 1) * per;
      return filteredCourses.value.slice(start, start + per);
    });
    const displayedCourses = computed(() => {
      return props.useServerProcessing ? coursesList.value : paginatedCourses.value;
    });
    watch([itemsPerPage, searchQuery], () => {
      currentPage.value = 1;
    });
    const showConfirmDeleteModal = ref(false);
    const courseToDeleteId = ref(null);
    const courseToDeleteTitle = ref("");
    const confirmDelete = (courseOrId, title = null) => {
      if (typeof courseOrId === "object") {
        courseToDeleteId.value = courseOrId.id;
        courseToDeleteTitle.value = title || getCourseTitle(courseOrId);
      } else {
        courseToDeleteId.value = courseOrId;
        courseToDeleteTitle.value = title || `ID: ${courseOrId}`;
      }
      showConfirmDeleteModal.value = true;
    };
    const closeModal = () => {
      showConfirmDeleteModal.value = false;
      courseToDeleteId.value = null;
      courseToDeleteTitle.value = "";
    };
    const deleteCourse = () => {
      if (courseToDeleteId.value === null) return;
      const idToDelete = courseToDeleteId.value;
      const titleToDelete = courseToDeleteTitle.value;
      router.delete(route("admin.schoolCourses.destroy", { schoolCourse: idToDelete }), {
        preserveScroll: true,
        preserveState: false,
        onSuccess: () => {
          toast.success(`Курс "${titleToDelete || "ID: " + idToDelete}" удалён.`);
        },
        onError: (errors) => {
          const errorKey = Object.keys(errors || {})[0];
          const errorMsg = errors.general || errors[errorKey] || "Произошла ошибка при удалении.";
          toast.error(`${errorMsg} (Курс: ${titleToDelete || "ID: " + idToDelete})`);
        },
        onFinish: () => closeModal()
      });
    };
    const patchCourse = (courseId, payload) => {
      const index = localCourses.value.findIndex((course) => course.id === courseId);
      if (index !== -1) {
        localCourses.value[index] = {
          ...localCourses.value[index],
          ...payload
        };
      }
    };
    const selectedCourses = ref([]);
    const toggleAll = (payload) => {
      var _a;
      const checked = (payload == null ? void 0 : payload.checked) ?? ((_a = payload == null ? void 0 : payload.target) == null ? void 0 : _a.checked) ?? false;
      const ids = (payload == null ? void 0 : payload.ids) ?? displayedCourses.value.map((course) => course.id);
      if (checked) {
        selectedCourses.value = [.../* @__PURE__ */ new Set([...selectedCourses.value, ...ids])];
      } else {
        selectedCourses.value = selectedCourses.value.filter((id) => !ids.includes(id));
      }
    };
    const toggleSelectCourse = (id) => {
      const index = selectedCourses.value.indexOf(id);
      if (index > -1) {
        selectedCourses.value.splice(index, 1);
      } else {
        selectedCourses.value.push(id);
      }
    };
    const handleSortOrderUpdate = (orderedIds) => {
      const startSort = (currentPage.value - 1) * itemsPerPage.value;
      const items = orderedIds.map((id, index) => ({
        id,
        sort: startSort + index + 1
      }));
      if (!items.length) return;
      router.put(route("admin.actions.schoolCourses.updateSortBulk"), { items }, {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => toast.success("Порядок курсов успешно обновлён."),
        onError: (errors) => {
          console.error("Ошибка обновления сортировки курсов:", errors);
          toast.error((errors == null ? void 0 : errors.message) || (errors == null ? void 0 : errors.general) || "Не удалось обновить порядок курсов.");
          router.reload({
            only: ["courses"],
            preserveScroll: true
          });
        }
      });
    };
    const bulkToggleActivity = (newActivity) => {
      if (!selectedCourses.value.length) {
        toast.warning("Выберите курсы для активации/деактивации.");
        return;
      }
      const idsToUpdate = [...selectedCourses.value];
      router.put(route("admin.actions.schoolCourses.bulkUpdateActivity"), {
        ids: idsToUpdate,
        activity: newActivity
      }, {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => {
          idsToUpdate.forEach((id) => patchCourse(id, { activity: newActivity }));
          selectedCourses.value = [];
          toast.success("Активность выбранных курсов обновлена.");
        },
        onError: (errors) => {
          toast.error((errors == null ? void 0 : errors.ids) || (errors == null ? void 0 : errors.activity) || (errors == null ? void 0 : errors.general) || "Ошибка массового обновления активности.");
        }
      });
    };
    const bulkToggleFlag = (field, newValue, routeName, successMessage) => {
      if (!selectedCourses.value.length) {
        toast.warning("Выберите курсы для массового действия.");
        return;
      }
      const idsToUpdate = [...selectedCourses.value];
      router.put(route(routeName), {
        ids: idsToUpdate,
        [field]: newValue
      }, {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => {
          idsToUpdate.forEach((id) => patchCourse(id, { [field]: newValue }));
          selectedCourses.value = [];
          toast.success(successMessage);
        },
        onError: (errors) => {
          toast.error((errors == null ? void 0 : errors.ids) || (errors == null ? void 0 : errors[field]) || (errors == null ? void 0 : errors.general) || "Ошибка массового обновления.");
        }
      });
    };
    const handleBulkAction = (event) => {
      const action = event.target.value;
      if (action === "selectAll") {
        toggleAll({ target: { checked: true } });
      } else if (action === "deselectAll") {
        toggleAll({ target: { checked: false } });
      } else if (action === "activate") {
        bulkToggleActivity(true);
      } else if (action === "deactivate") {
        bulkToggleActivity(false);
      } else if (action === "isNewOn") {
        bulkToggleFlag("is_new", true, "admin.actions.schoolCourses.bulkUpdateIsNew", "Курсы отмечены как новые.");
      } else if (action === "isNewOff") {
        bulkToggleFlag("is_new", false, "admin.actions.schoolCourses.bulkUpdateIsNew", "Флаг новых курсов снят.");
      } else if (action === "isHitOn") {
        bulkToggleFlag("is_hit", true, "admin.actions.schoolCourses.bulkUpdateIsHit", "Курсы отмечены как популярные.");
      } else if (action === "isHitOff") {
        bulkToggleFlag("is_hit", false, "admin.actions.schoolCourses.bulkUpdateIsHit", "Флаг популярных курсов снят.");
      } else if (action === "isSaleOn") {
        bulkToggleFlag("is_sale", true, "admin.actions.schoolCourses.bulkUpdateIsSale", "Курсы отмечены как скидочные.");
      } else if (action === "isSaleOff") {
        bulkToggleFlag("is_sale", false, "admin.actions.schoolCourses.bulkUpdateIsSale", "Флаг скидки снят.");
      } else if (action === "leftOn") {
        bulkToggleFlag("left", true, "admin.actions.schoolCourses.bulkUpdateLeft", "Курсы добавлены в левую колонку.");
      } else if (action === "leftOff") {
        bulkToggleFlag("left", false, "admin.actions.schoolCourses.bulkUpdateLeft", "Курсы убраны из левой колонки.");
      } else if (action === "mainOn") {
        bulkToggleFlag("main", true, "admin.actions.schoolCourses.bulkUpdateMain", "Курсы добавлены в главный блок.");
      } else if (action === "mainOff") {
        bulkToggleFlag("main", false, "admin.actions.schoolCourses.bulkUpdateMain", "Курсы убраны из главного блока.");
      } else if (action === "rightOn") {
        bulkToggleFlag("right", true, "admin.actions.schoolCourses.bulkUpdateRight", "Курсы добавлены в правую колонку.");
      } else if (action === "rightOff") {
        bulkToggleFlag("right", false, "admin.actions.schoolCourses.bulkUpdateRight", "Курсы убраны из правой колонки.");
      }
      event.target.value = "";
    };
    const toggleActivity = (course) => {
      const newActivity = !course.activity;
      const courseTitle = getCourseTitle(course);
      const actionText = newActivity ? t("activated") : t("deactivated");
      router.put(route("admin.actions.schoolCourses.updateActivity", {
        schoolCourse: course.id
      }), {
        activity: newActivity
      }, {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => {
          patchCourse(course.id, { activity: newActivity });
          course.activity = newActivity;
          toast.success(`Курс "${courseTitle}" ${actionText}.`);
        },
        onError: (errors) => {
          toast.error((errors == null ? void 0 : errors.activity) || (errors == null ? void 0 : errors.general) || `Ошибка изменения активности для курса "${courseTitle}".`);
        }
      });
    };
    const toggleFlag = (course, field, routeName, successMessage, errorMessage) => {
      const newValue = !course[field];
      router.put(route(routeName, {
        schoolCourse: course.id
      }), {
        [field]: newValue
      }, {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => {
          patchCourse(course.id, { [field]: newValue });
          course[field] = newValue;
          toast.success(successMessage);
        },
        onError: (errors) => {
          toast.error((errors == null ? void 0 : errors[field]) || (errors == null ? void 0 : errors.general) || errorMessage);
        }
      });
    };
    const toggleLeft = (course) => {
      toggleFlag(
        course,
        "left",
        "admin.actions.schoolCourses.updateLeft",
        "Левая колонка обновлена.",
        "Ошибка обновления левой колонки."
      );
    };
    const toggleMain = (course) => {
      toggleFlag(
        course,
        "main",
        "admin.actions.schoolCourses.updateMain",
        "Главный блок обновлён.",
        "Ошибка обновления главного блока."
      );
    };
    const toggleRight = (course) => {
      toggleFlag(
        course,
        "right",
        "admin.actions.schoolCourses.updateRight",
        "Правая колонка обновлена.",
        "Ошибка обновления правой колонки."
      );
    };
    const toggleIsNew = (course) => {
      toggleFlag(
        course,
        "is_new",
        "admin.actions.schoolCourses.updateIsNew",
        'Флаг "новый" обновлён.',
        'Ошибка обновления флага "новый".'
      );
    };
    const toggleIsHit = (course) => {
      toggleFlag(
        course,
        "is_hit",
        "admin.actions.schoolCourses.updateIsHit",
        'Флаг "популярный" обновлён.',
        'Ошибка обновления флага "популярный".'
      );
    };
    const toggleIsSale = (course) => {
      toggleFlag(
        course,
        "is_sale",
        "admin.actions.schoolCourses.updateIsSale",
        'Флаг "скидка" обновлён.',
        'Ошибка обновления флага "скидка".'
      );
    };
    return (_ctx, _push, _parent, _attrs) => {
      _push(ssrRenderComponent(AdminLayout, mergeProps({
        title: unref(t)("courses")
      }, _attrs), {
        header: withCtx((_, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(ssrRenderComponent(TitlePage, null, {
              default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                if (_push3) {
                  _push3(`${ssrInterpolate(unref(t)("courses"))}`);
                } else {
                  return [
                    createTextVNode(toDisplayString(unref(t)("courses")), 1)
                  ];
                }
              }),
              _: 1
            }, _parent2, _scopeId));
          } else {
            return [
              createVNode(TitlePage, null, {
                default: withCtx(() => [
                  createTextVNode(toDisplayString(unref(t)("courses")), 1)
                ]),
                _: 1
              })
            ];
          }
        }),
        default: withCtx((_, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(`<div class="px-2 py-2 w-full max-w-12xl mx-auto"${_scopeId}><div class="p-4 bg-slate-50 dark:bg-slate-700 border border-blue-400 dark:border-blue-200 overflow-hidden shadow-md shadow-gray-500 dark:shadow-slate-400 bg-opacity-95 dark:bg-opacity-95"${_scopeId}><div class="sm:flex sm:justify-between sm:items-center mb-3 gap-3"${_scopeId}>`);
            _push2(ssrRenderComponent(_sfc_main$e, {
              href: _ctx.route("admin.schoolCourses.create")
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
                  _push3(` ${ssrInterpolate(unref(t)("addCourse"))}`);
                } else {
                  return [
                    createTextVNode(" " + toDisplayString(unref(t)("addCourse")), 1)
                  ];
                }
              }),
              _: 1
            }, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$f, {
              "setting-key": "adminSchoolCoursesProcessingMode",
              mode: __props.adminSchoolCoursesProcessingMode,
              "use-server-processing": __props.useServerProcessing,
              total: __props.coursesCount
            }, null, _parent2, _scopeId));
            _push2(`</div>`);
            if (__props.coursesCount && !__props.useServerProcessing) {
              _push2(ssrRenderComponent(_sfc_main$g, {
                modelValue: searchQuery.value,
                "onUpdate:modelValue": ($event) => searchQuery.value = $event,
                placeholder: unref(t)("searchByName")
              }, null, _parent2, _scopeId));
            } else {
              _push2(`<!---->`);
            }
            if (__props.coursesCount && __props.useServerProcessing) {
              _push2(ssrRenderComponent(_sfc_main$h, {
                modelValue: searchQuery.value,
                "onUpdate:modelValue": ($event) => searchQuery.value = $event
              }, null, _parent2, _scopeId));
            } else {
              _push2(`<!---->`);
            }
            if (__props.coursesCount) {
              _push2(`<div class="flex justify-between items-center flex-col md:flex-row my-3"${_scopeId}>`);
              if (!__props.useServerProcessing) {
                _push2(ssrRenderComponent(_sfc_main$i, {
                  "items-per-page": itemsPerPage.value,
                  "onUpdate:itemsPerPage": ($event) => itemsPerPage.value = $event
                }, null, _parent2, _scopeId));
              } else {
                _push2(ssrRenderComponent(_sfc_main$j, {
                  "items-per-page": itemsPerPage.value,
                  "update-route": "admin.settings.updateAdminCountSchoolCourses"
                }, null, _parent2, _scopeId));
              }
              _push2(ssrRenderComponent(_sfc_main$3, {
                sortParam: sortParam.value,
                "onUpdate:sortParam": (val) => sortParam.value = val
              }, null, _parent2, _scopeId));
              _push2(`</div>`);
            } else {
              _push2(`<!---->`);
            }
            if (__props.coursesCount) {
              _push2(`<div class="flex flex-col lg:flex-row items-center justify-between gap-3"${_scopeId}>`);
              _push2(ssrRenderComponent(_sfc_main$k, null, {
                default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                  if (_push3) {
                    _push3(`${ssrInterpolate(__props.coursesCount)}`);
                  } else {
                    return [
                      createTextVNode(toDisplayString(__props.coursesCount), 1)
                    ];
                  }
                }),
                _: 1
              }, _parent2, _scopeId));
              _push2(ssrRenderComponent(_sfc_main$4, { onChange: handleBulkAction }, null, _parent2, _scopeId));
              _push2(ssrRenderComponent(_sfc_main$l, {
                viewMode: viewMode.value,
                "onUpdate:viewMode": ($event) => viewMode.value = $event
              }, null, _parent2, _scopeId));
              _push2(`</div>`);
            } else {
              _push2(`<!---->`);
            }
            if (__props.coursesCount) {
              _push2(`<div class="flex justify-center items-center flex-col md:flex-row mt-3"${_scopeId}>`);
              if (!__props.useServerProcessing) {
                _push2(ssrRenderComponent(_sfc_main$m, {
                  "current-page": currentPage.value,
                  "items-per-page": itemsPerPage.value,
                  "total-items": filteredCourses.value.length,
                  "onUpdate:currentPage": ($event) => currentPage.value = $event
                }, null, _parent2, _scopeId));
              } else {
                _push2(ssrRenderComponent(_sfc_main$n, { pagination: __props.courses }, null, _parent2, _scopeId));
              }
              _push2(`</div>`);
            } else {
              _push2(`<!---->`);
            }
            if (viewMode.value === "table") {
              _push2(ssrRenderComponent(_sfc_main$2, {
                courses: displayedCourses.value,
                "selected-courses": selectedCourses.value,
                onToggleActivity: toggleActivity,
                onToggleLeft: toggleLeft,
                onToggleMain: toggleMain,
                onToggleRight: toggleRight,
                onToggleIsNew: toggleIsNew,
                onToggleIsHit: toggleIsHit,
                onToggleIsSale: toggleIsSale,
                onDelete: confirmDelete,
                onUpdateSortOrder: handleSortOrderUpdate,
                onToggleSelect: toggleSelectCourse,
                onToggleAll: toggleAll
              }, null, _parent2, _scopeId));
            } else {
              _push2(ssrRenderComponent(_sfc_main$1, {
                courses: displayedCourses.value,
                "selected-courses": selectedCourses.value,
                onToggleActivity: toggleActivity,
                onToggleLeft: toggleLeft,
                onToggleMain: toggleMain,
                onToggleRight: toggleRight,
                onToggleIsNew: toggleIsNew,
                onToggleIsHit: toggleIsHit,
                onToggleIsSale: toggleIsSale,
                onDelete: confirmDelete,
                onUpdateSortOrder: handleSortOrderUpdate,
                onToggleSelect: toggleSelectCourse,
                onToggleAll: toggleAll
              }, null, _parent2, _scopeId));
            }
            if (__props.coursesCount) {
              _push2(`<div class="flex justify-center items-center flex-col md:flex-row mt-3"${_scopeId}>`);
              if (!__props.useServerProcessing) {
                _push2(ssrRenderComponent(_sfc_main$m, {
                  "current-page": currentPage.value,
                  "items-per-page": itemsPerPage.value,
                  "total-items": filteredCourses.value.length,
                  "onUpdate:currentPage": ($event) => currentPage.value = $event
                }, null, _parent2, _scopeId));
              } else {
                _push2(ssrRenderComponent(_sfc_main$n, { pagination: __props.courses }, null, _parent2, _scopeId));
              }
              _push2(`</div>`);
            } else {
              _push2(`<!---->`);
            }
            _push2(`</div></div>`);
            _push2(ssrRenderComponent(_sfc_main$o, {
              show: showConfirmDeleteModal.value,
              onCancel: closeModal,
              onConfirm: deleteCourse,
              cancelText: unref(t)("cancel"),
              confirmText: unref(t)("yesDelete"),
              onClose: closeModal
            }, null, _parent2, _scopeId));
          } else {
            return [
              createVNode("div", { class: "px-2 py-2 w-full max-w-12xl mx-auto" }, [
                createVNode("div", { class: "p-4 bg-slate-50 dark:bg-slate-700 border border-blue-400 dark:border-blue-200 overflow-hidden shadow-md shadow-gray-500 dark:shadow-slate-400 bg-opacity-95 dark:bg-opacity-95" }, [
                  createVNode("div", { class: "sm:flex sm:justify-between sm:items-center mb-3 gap-3" }, [
                    createVNode(_sfc_main$e, {
                      href: _ctx.route("admin.schoolCourses.create")
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
                        createTextVNode(" " + toDisplayString(unref(t)("addCourse")), 1)
                      ]),
                      _: 1
                    }, 8, ["href"]),
                    createVNode(_sfc_main$f, {
                      "setting-key": "adminSchoolCoursesProcessingMode",
                      mode: __props.adminSchoolCoursesProcessingMode,
                      "use-server-processing": __props.useServerProcessing,
                      total: __props.coursesCount
                    }, null, 8, ["mode", "use-server-processing", "total"])
                  ]),
                  __props.coursesCount && !__props.useServerProcessing ? (openBlock(), createBlock(_sfc_main$g, {
                    key: 0,
                    modelValue: searchQuery.value,
                    "onUpdate:modelValue": ($event) => searchQuery.value = $event,
                    placeholder: unref(t)("searchByName")
                  }, null, 8, ["modelValue", "onUpdate:modelValue", "placeholder"])) : createCommentVNode("", true),
                  __props.coursesCount && __props.useServerProcessing ? (openBlock(), createBlock(_sfc_main$h, {
                    key: 1,
                    modelValue: searchQuery.value,
                    "onUpdate:modelValue": ($event) => searchQuery.value = $event
                  }, null, 8, ["modelValue", "onUpdate:modelValue"])) : createCommentVNode("", true),
                  __props.coursesCount ? (openBlock(), createBlock("div", {
                    key: 2,
                    class: "flex justify-between items-center flex-col md:flex-row my-3"
                  }, [
                    !__props.useServerProcessing ? (openBlock(), createBlock(_sfc_main$i, {
                      key: 0,
                      "items-per-page": itemsPerPage.value,
                      "onUpdate:itemsPerPage": ($event) => itemsPerPage.value = $event
                    }, null, 8, ["items-per-page", "onUpdate:itemsPerPage"])) : (openBlock(), createBlock(_sfc_main$j, {
                      key: 1,
                      "items-per-page": itemsPerPage.value,
                      "update-route": "admin.settings.updateAdminCountSchoolCourses"
                    }, null, 8, ["items-per-page"])),
                    createVNode(_sfc_main$3, {
                      sortParam: sortParam.value,
                      "onUpdate:sortParam": (val) => sortParam.value = val
                    }, null, 8, ["sortParam", "onUpdate:sortParam"])
                  ])) : createCommentVNode("", true),
                  __props.coursesCount ? (openBlock(), createBlock("div", {
                    key: 3,
                    class: "flex flex-col lg:flex-row items-center justify-between gap-3"
                  }, [
                    createVNode(_sfc_main$k, null, {
                      default: withCtx(() => [
                        createTextVNode(toDisplayString(__props.coursesCount), 1)
                      ]),
                      _: 1
                    }),
                    createVNode(_sfc_main$4, { onChange: handleBulkAction }),
                    createVNode(_sfc_main$l, {
                      viewMode: viewMode.value,
                      "onUpdate:viewMode": ($event) => viewMode.value = $event
                    }, null, 8, ["viewMode", "onUpdate:viewMode"])
                  ])) : createCommentVNode("", true),
                  __props.coursesCount ? (openBlock(), createBlock("div", {
                    key: 4,
                    class: "flex justify-center items-center flex-col md:flex-row mt-3"
                  }, [
                    !__props.useServerProcessing ? (openBlock(), createBlock(_sfc_main$m, {
                      key: 0,
                      "current-page": currentPage.value,
                      "items-per-page": itemsPerPage.value,
                      "total-items": filteredCourses.value.length,
                      "onUpdate:currentPage": ($event) => currentPage.value = $event
                    }, null, 8, ["current-page", "items-per-page", "total-items", "onUpdate:currentPage"])) : (openBlock(), createBlock(_sfc_main$n, {
                      key: 1,
                      pagination: __props.courses
                    }, null, 8, ["pagination"]))
                  ])) : createCommentVNode("", true),
                  viewMode.value === "table" ? (openBlock(), createBlock(_sfc_main$2, {
                    key: 5,
                    courses: displayedCourses.value,
                    "selected-courses": selectedCourses.value,
                    onToggleActivity: toggleActivity,
                    onToggleLeft: toggleLeft,
                    onToggleMain: toggleMain,
                    onToggleRight: toggleRight,
                    onToggleIsNew: toggleIsNew,
                    onToggleIsHit: toggleIsHit,
                    onToggleIsSale: toggleIsSale,
                    onDelete: confirmDelete,
                    onUpdateSortOrder: handleSortOrderUpdate,
                    onToggleSelect: toggleSelectCourse,
                    onToggleAll: toggleAll
                  }, null, 8, ["courses", "selected-courses"])) : (openBlock(), createBlock(_sfc_main$1, {
                    key: 6,
                    courses: displayedCourses.value,
                    "selected-courses": selectedCourses.value,
                    onToggleActivity: toggleActivity,
                    onToggleLeft: toggleLeft,
                    onToggleMain: toggleMain,
                    onToggleRight: toggleRight,
                    onToggleIsNew: toggleIsNew,
                    onToggleIsHit: toggleIsHit,
                    onToggleIsSale: toggleIsSale,
                    onDelete: confirmDelete,
                    onUpdateSortOrder: handleSortOrderUpdate,
                    onToggleSelect: toggleSelectCourse,
                    onToggleAll: toggleAll
                  }, null, 8, ["courses", "selected-courses"])),
                  __props.coursesCount ? (openBlock(), createBlock("div", {
                    key: 7,
                    class: "flex justify-center items-center flex-col md:flex-row mt-3"
                  }, [
                    !__props.useServerProcessing ? (openBlock(), createBlock(_sfc_main$m, {
                      key: 0,
                      "current-page": currentPage.value,
                      "items-per-page": itemsPerPage.value,
                      "total-items": filteredCourses.value.length,
                      "onUpdate:currentPage": ($event) => currentPage.value = $event
                    }, null, 8, ["current-page", "items-per-page", "total-items", "onUpdate:currentPage"])) : (openBlock(), createBlock(_sfc_main$n, {
                      key: 1,
                      pagination: __props.courses
                    }, null, 8, ["pagination"]))
                  ])) : createCommentVNode("", true)
                ])
              ]),
              createVNode(_sfc_main$o, {
                show: showConfirmDeleteModal.value,
                onCancel: closeModal,
                onConfirm: deleteCourse,
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
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Pages/Admin/School/SchoolCourses/Index.vue");
  return _sfc_setup ? _sfc_setup(props, ctx) : void 0;
};
export {
  _sfc_main as default
};
