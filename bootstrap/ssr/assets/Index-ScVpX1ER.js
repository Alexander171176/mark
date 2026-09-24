import { mergeProps, unref, useSSRContext, ref, watch, withCtx, createVNode, createBlock, openBlock, toDisplayString, Fragment, renderList, createCommentVNode, computed, createTextVNode } from "vue";
import { ssrRenderAttrs, ssrInterpolate, ssrRenderAttr, ssrRenderComponent, ssrRenderList, ssrIncludeBooleanAttr } from "vue/server-renderer";
import { useI18n } from "vue-i18n";
import { useToast } from "vue-toastification";
import { router } from "@inertiajs/vue3";
import { A as AdminLayout } from "./AdminLayout-03yd3nxC.js";
import { T as TitlePage } from "./TitlePage-CBZZ8YZB.js";
import { _ as _sfc_main$a } from "./SearchInput-xZSYbbms.js";
import { _ as _sfc_main$b, a as _sfc_main$h } from "./ServerSearchInput-CxQUCmzV.js";
import { _ as _sfc_main$8 } from "./DefaultButton-DMDyGacf.js";
import { _ as _sfc_main$c, a as _sfc_main$f, b as _sfc_main$g } from "./ItemsPerPageSelect-DOO-E4Z0.js";
import { _ as _sfc_main$e } from "./CountTable-p8tyXGUL.js";
import { _ as _sfc_main$d } from "./ServerItemsPerPageSelect-B_wSkKlU.js";
import { _ as _sfc_main$i } from "./DangerModal-DjIe5VDg.js";
import { _ as _sfc_main$9 } from "./ProcessingModeSwitcher-BJvzFf6_.js";
import draggable from "vuedraggable";
import { _ as _sfc_main$6 } from "./IconEdit-Bw90OQvk.js";
import { _ as _sfc_main$7 } from "./DeleteIconButton-DLv2Mr1x.js";
import { _ as _sfc_main$5 } from "./ActivityToggle-B1-nFMYK.js";
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
  __name: "SortSelect",
  __ssrInlineRender: true,
  props: {
    sortParam: String
  },
  emits: ["update:sortParam"],
  setup(__props, { emit: __emit }) {
    const { t } = useI18n();
    return (_ctx, _push, _parent, _attrs) => {
      _push(`<div${ssrRenderAttrs(mergeProps({ class: "flex justify-center items-center h-fit my-2" }, _attrs))}><label for="sortParam" class="hidden lg:block sm:mr-2 tracking-wider text-sm font-semibold text-slate-600 dark:text-slate-100">${ssrInterpolate(unref(t)("sort"))}</label><select id="sortParam"${ssrRenderAttr("value", __props.sortParam)} class="w-50 px-3 py-0.5 form-select bg-white dark:bg-gray-200 text-gray-600 dark:text-gray-900 border border-slate-400 dark:border-slate-600 rounded-sm shadow-sm"><option value="idDesc">${ssrInterpolate(unref(t)("idDesc"))}</option><option value="idAsc">${ssrInterpolate(unref(t)("idAsc"))}</option><option disabled>─────────────</option><option value="sortAsc">${ssrInterpolate(unref(t)("sortNumber"))} 0→9</option><option value="sortDesc">${ssrInterpolate(unref(t)("sortNumber"))} 9→0</option><option disabled>─────────────</option><option value="titleAsc">${ssrInterpolate(unref(t)("title"))} A→Z</option><option value="titleDesc">${ssrInterpolate(unref(t)("title"))} Z→A</option><option disabled>─────────────</option><option value="slugAsc">${ssrInterpolate(unref(t)("url"))} ↑</option><option value="slugDesc">${ssrInterpolate(unref(t)("url"))} ↓</option><option disabled>─────────────</option><option value="activityDesc">${ssrInterpolate(unref(t)("activity"))} ON→OFF</option><option value="activityAsc">${ssrInterpolate(unref(t)("activity"))} OFF→ON</option><option value="activity">${ssrInterpolate(unref(t)("active"))}</option><option value="inactive">${ssrInterpolate(unref(t)("inactive"))}</option><option disabled>─────────────</option><option value="viewsAsc">${ssrInterpolate(unref(t)("views"))} ↑</option><option value="viewsDesc">${ssrInterpolate(unref(t)("views"))} ↓</option><option disabled>─────────────</option><option value="ratingAvgAsc">${ssrInterpolate(unref(t)("rating"))} AVG 0→9</option><option value="ratingAvgDesc">${ssrInterpolate(unref(t)("rating"))} AVG 9→0</option><option value="ratingCountAsc">${ssrInterpolate(unref(t)("rating"))} ↑</option><option value="ratingCountDesc">${ssrInterpolate(unref(t)("rating"))} ↓</option><option disabled>─────────────</option><option value="hourlyRateAsc">${ssrInterpolate(unref(t)("hourlyRate"))} ↑</option><option value="hourlyRateDesc">${ssrInterpolate(unref(t)("hourlyRate"))} ↓</option><option disabled>─────────────</option><option value="experienceAsc">${ssrInterpolate(unref(t)("experienceYears"))} ↑</option><option value="experienceDesc">${ssrInterpolate(unref(t)("experienceYears"))} ↓</option><option disabled>─────────────</option><option value="dateAsc">${ssrInterpolate(unref(t)("sortOldestFirst"))}</option><option value="dateDesc">${ssrInterpolate(unref(t)("sortNewestFirst"))}</option><option value="createdAtAsc">${ssrInterpolate(unref(t)("createdAt"))} ↑</option><option value="createdAtDesc">${ssrInterpolate(unref(t)("createdAt"))} ↓</option><option value="updatedAtAsc">${ssrInterpolate(unref(t)("updatedAt"))} ↑</option><option value="updatedAtDesc">${ssrInterpolate(unref(t)("updatedAt"))} ↓</option><option disabled>─────────────</option><option value="coursesDesc">${ssrInterpolate(unref(t)("courses"))} 9→0</option><option value="coursesAsc">${ssrInterpolate(unref(t)("courses"))} 0→9</option><option disabled>─────────────</option><option value="payoutsDesc">${ssrInterpolate(unref(t)("payouts"))} 9→0</option><option value="payoutsAsc">${ssrInterpolate(unref(t)("payouts"))} 0→9</option><option value="imagesDesc">${ssrInterpolate(unref(t)("images"))} 9→0</option><option value="imagesAsc">${ssrInterpolate(unref(t)("images"))} 0→9</option><option disabled>─────────────</option></select></div>`);
    };
  }
};
const _sfc_setup$4 = _sfc_main$4.setup;
_sfc_main$4.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Components/Admin/School/SchoolInstructorProfile/Sort/SortSelect.vue");
  return _sfc_setup$4 ? _sfc_setup$4(props, ctx) : void 0;
};
const _sfc_main$3 = {
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
const _sfc_setup$3 = _sfc_main$3.setup;
_sfc_main$3.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Components/Admin/School/SchoolInstructorProfile/Select/BulkActionSelect.vue");
  return _sfc_setup$3 ? _sfc_setup$3(props, ctx) : void 0;
};
const _sfc_main$2 = {
  __name: "InstructorProfileTable",
  __ssrInlineRender: true,
  props: {
    instructorProfiles: {
      type: Array,
      default: () => []
    },
    selectedInstructorProfiles: {
      type: Array,
      default: () => []
    }
  },
  emits: [
    "toggle-activity",
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
    const localInstructorProfiles = ref([]);
    watch(
      () => props.instructorProfiles,
      (newVal) => {
        localInstructorProfiles.value = JSON.parse(JSON.stringify(newVal || []));
      },
      { immediate: true, deep: true }
    );
    const handleDragEnd = () => {
      const newOrderIds = localInstructorProfiles.value.map(
        (instructorProfile) => instructorProfile.id
      );
      emits("update-sort-order", newOrderIds);
    };
    const getPrimaryImage = (instructorProfile) => {
      if (instructorProfile.images && instructorProfile.images.length) {
        return [...instructorProfile.images].sort((a, b) => a.order - b.order)[0];
      }
      return null;
    };
    return (_ctx, _push, _parent, _attrs) => {
      _push(`<div${ssrRenderAttrs(mergeProps({ class: "bg-white dark:bg-slate-700 shadow-lg rounded-sm border border-slate-200 dark:border-slate-600 relative" }, _attrs))}><div class="flex items-center justify-between px-3 py-2 border-b border-slate-400 dark:border-slate-500"><div class="text-xs text-slate-600 dark:text-slate-200">${ssrInterpolate(unref(t)("selected"))}: ${ssrInterpolate(__props.selectedInstructorProfiles.length)}</div>`);
      if (localInstructorProfiles.value.length) {
        _push(`<label class="flex items-center text-xs text-slate-600 dark:text-slate-200 cursor-pointer"><span>${ssrInterpolate(unref(t)("selectAll"))}</span><input type="checkbox" class="mx-2"></label>`);
      } else {
        _push(`<!---->`);
      }
      _push(`</div><div class="overflow-x-auto">`);
      if (__props.instructorProfiles.length > 0) {
        _push(`<table class="table-auto w-full text-slate-700 dark:text-slate-100"><thead class="text-sm uppercase bg-slate-200 dark:bg-cyan-900 border border-solid border-gray-300 dark:border-gray-700"><tr><th class="px-2 py-3 w-px"><svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 fill-current text-slate-800 dark:text-slate-200" height="24" width="24" viewBox="0 0 24 24"><path d="M12.707,2.293a1,1,0,0,0-1.414,0l-5,5A1,1,0,0,0,7.707,8.707L12,4.414l4.293,4.293a1,1,0,0,0,1.414-1.414Z"></path><path d="M16.293,15.293,12,19.586,7.707,15.293a1,1,0,0,0-1.414,1.414l5,5a1,1,0,0,0,1.414,0l5-5a1,1,0,0,0-1.414-1.414Z"></path></svg></th><th class="px-2 first:pl-11 last:pr-11 py-3 whitespace-nowrap w-px"><div class="font-medium text-center">${ssrInterpolate(unref(t)("id"))}</div></th><th class="px-2 first:pl-11 last:pr-11 py-3 whitespace-nowrap"><div class="flex justify-center"${ssrRenderAttr("title", unref(t)("image"))}><svg class="w-6 h-6 fill-current shrink-0" viewBox="0 0 512 512"><path d="M0 96C0 60.7 28.7 32 64 32l384 0c35.3 0 64 28.7 64 64l0 320c0 35.3-28.7 64-64 64L64 480c-35.3 0-64-28.7-64-64L0 96zM323.8 202.5c-4.5-6.6-11.9-10.5-19.8-10.5s-15.4 3.9-19.8 10.5l-87 127.6L170.7 297c-4.6-5.7-11.5-9-18.7-9s-14.2 3.3-18.7 9l-64 80c-5.8 7.2-6.9 17.1-2.9 25.4s12.4 13.6 21.6 13.6l96 0 32 0 208 0c8.9 0 17.1-4.9 21.2-12.8s3.6-17.4-1.4-24.7l-120-176zM112 192a48 48 0 1 0 0-96 48 48 0 1 0 0 96z"></path></svg></div></th><th class="px-2 first:pl-11 last:pr-11 py-3 whitespace-nowrap"><div class="font-semibold text-left">${ssrInterpolate(unref(t)("instructor"))}</div></th><th class="px-2 first:pl-11 last:pr-11 py-3 whitespace-nowrap"><div class="flex justify-center items-center font-semibold text-left">${ssrInterpolate(unref(t)("experienceDesc"))}</div></th><th class="px-2 first:pl-11 last:pr-11 py-3 whitespace-nowrap"><div class="font-medium text-center">${ssrInterpolate(unref(t)("courses"))}</div></th><th class="px-2 first:pl-11 last:pr-11 py-3 whitespace-nowrap"><div class="flex justify-center items-center"${ssrRenderAttr("title", unref(t)("hourlyRate"))}><svg xmlns="http://www.w3.org/2000/svg" height="24" width="24" viewBox="0 0 24 24" class="shrink-0 h-6 w-6"><path class="fill-current text-green-600 dark:text-green-200" d="M0,5V19H24V5ZM7,9.749a.492.492,0,0,1-.19.392,7.537,7.537,0,0,0-2.795,5.366A.517.517,0,0,1,3.507,16c-.63,0-.992.005-.992.005a.506.506,0,0,1-.5-.52A7.27,7.27,0,0,1,4.431,10H1.5A.5.5,0,0,1,1,9.5v-1A.5.5,0,0,1,1.5,8h5a.5.5,0,0,1,.5.5Zm8,0a.492.492,0,0,1-.19.392,7.537,7.537,0,0,0-2.795,5.366.517.517,0,0,1-.508.488c-.63,0-.992.005-.992.005a.506.506,0,0,1-.5-.52A7.27,7.27,0,0,1,12.431,10H9.5A.5.5,0,0,1,9,9.5v-1A.5.5,0,0,1,9.5,8h5a.5.5,0,0,1,.5.5Zm8,0a.492.492,0,0,1-.19.392,7.537,7.537,0,0,0-2.795,5.366.517.517,0,0,1-.508.488c-.63,0-.992.005-.992.005a.506.506,0,0,1-.5-.52A7.27,7.27,0,0,1,20.431,10H17.5a.5.5,0,0,1-.5-.5v-1a.5.5,0,0,1,.5-.5h5a.5.5,0,0,1,.5.5Z"></path><path class="fill-current text-green-600 dark:text-green-200" d="M1,1H23a1,1,0,0,1,1,1V3a0,0,0,0,1,0,0H0A0,0,0,0,1,0,3V2A1,1,0,0,1,1,1Z"></path><path class="fill-current text-green-600 dark:text-green-200" d="M0,21H24a0,0,0,0,1,0,0v1a1,1,0,0,1-1,1H1a1,1,0,0,1-1-1V21A0,0,0,0,1,0,21Z"></path></svg></div></th><th class="px-2 first:pl-11 last:pr-11 py-3 whitespace-nowrap"><div class="flex justify-center"${ssrRenderAttr("title", unref(t)("views"))}><svg class="w-4 h-4 fill-current shrink-0" viewBox="0 0 16 16"><path class="fill-current text-blue-600 dark:text-blue-300" d="M8 2C3.246 2 .251 7.29.127 7.515a.998.998 0 0 0 .002.975c.07.125 1.044 1.801 2.695 3.274C4.738 13.582 6.283 14 8 14c4.706 0 7.743-5.284 7.872-5.507a1 1 0 0 0 0-.98A13.292 13.292 0 0 0 8 2zm0 10a4 4 0 1 1 0-8 4 4 0 0 1 0 8zm0-6a2 2 0 1 0 0 4 2 2 0 0 0 0-4z"></path></svg></div></th><th class="px-2 first:pl-11 last:pr-11 py-3 whitespace-nowrap"><div${ssrRenderAttr("title", unref(t)("ratingCount"))}><svg xmlns="http://www.w3.org/2000/svg" height="24" width="24" viewBox="0 0 24 24" class="shrink-0 h-4 w-4"><path class="fill-current text-red-400 dark:text-red-300" d="M14.586,9.439S15.7,2.858,11.138,0A8.055,8.055,0,0,1,8.1,5.831C6.149,7.546,2.481,11.4,2.52,15.51A9.435,9.435,0,0,0,7.7,24a5.975,5.975,0,0,1,2.091-4.132,4.877,4.877,0,0,0,1.869-3.278,8.786,8.786,0,0,1,4.652,7.322v.02a8.827,8.827,0,0,0,5.137-7.659c.324-3.863-1.792-9.112-3.668-10.828A10.192,10.192,0,0,1,14.586,9.439Z"></path></svg></div></th><th class="px-2 first:pl-11 last:pr-11 py-3 whitespace-nowrap"><div${ssrRenderAttr("title", unref(t)("ratingAvg"))}><svg xmlns="http://www.w3.org/2000/svg" height="24" width="24" viewBox="0 0 24 24" class="shrink-0 h-4 w-4"><path class="fill-current text-red-400 dark:text-red-300" d="M12.746,1.464l3.11,6.3L22.81,8.776a.831.831,0,0,1,.461,1.418l-5.033,4.9,1.188,6.926a.832.832,0,0,1-1.207.877L12,19.632,5.78,22.9a.833.833,0,0,1-1.207-.878L5.761,15.1l-5.033-4.9a.831.831,0,0,1,.461-1.418L8.143,7.765l3.11-6.3A.833.833,0,0,1,12.746,1.464Z"></path></svg></div></th><th class="px-2 first:pl-11 last:pr-11 py-3 whitespace-nowrap"><div class="font-semibold text-end">${ssrInterpolate(unref(t)("actions"))}</div></th><th class="px-2 first:pl-11 last:pr-11 py-3 whitespace-nowrap"><div class="text-center"><input type="checkbox"></div></th></tr></thead>`);
        _push(ssrRenderComponent(unref(draggable), {
          tag: "tbody",
          modelValue: localInstructorProfiles.value,
          "onUpdate:modelValue": ($event) => localInstructorProfiles.value = $event,
          onEnd: handleDragEnd,
          "item-key": "id",
          handle: ".handle"
        }, {
          item: withCtx(({ element: instructorProfile }, _push2, _parent2, _scopeId) => {
            var _a, _b, _c, _d;
            if (_push2) {
              _push2(`<tr class="text-sm font-semibold border-b-2 hover:bg-slate-100 dark:hover:bg-cyan-800"${_scopeId}><td class="px-2 py-1 text-center cursor-move handle"${_scopeId}><svg class="w-4 h-4 text-gray-500 dark:text-gray-300" fill="currentColor" viewBox="0 0 20 20"${_scopeId}><path d="M7 4h2v2H7V4zm4 0h2v2h-2V4zM7 8h2v2H7V8zm4 0h2v2h-2V8zM7 12h2v2H7v-2zm4 0h2v2h-2v-2z"${_scopeId}></path></svg></td><td class="px-2 first:pl-11 last:pr-11 py-1 whitespace-nowrap"${_scopeId}><div class="text-center text-slate-800 dark:text-blue-200"${_scopeId}>${ssrInterpolate(instructorProfile.id)}</div></td><td class="first:pl-11 last:pr-11 py-1"${_scopeId}><div class="relative w-32 h-32 flex items-center justify-center"${_scopeId}>`);
              if (instructorProfile.images && instructorProfile.images.length) {
                _push2(`<img${ssrRenderAttr("src", getPrimaryImage(instructorProfile).webp_url || getPrimaryImage(instructorProfile).url)}${ssrRenderAttr("alt", getPrimaryImage(instructorProfile).alt || unref(t)("defaultImageAlt"))}${ssrRenderAttr("title", getPrimaryImage(instructorProfile).caption || unref(t)("postImage"))} class="h-20 w-20 object-cover rounded-full border border-slate-300 dark:border-slate-500"${_scopeId}>`);
              } else {
                _push2(`<img src="/storage/school/school_instructor_profile_images/default-image.png"${ssrRenderAttr("alt", unref(t)("defaultImageTitle"))} class="h-20 w-20 object-cover rounded-full border border-slate-300 dark:border-slate-500"${_scopeId}>`);
              }
              _push2(`</div></td><td class="px-2 first:pl-11 last:pr-11 py-1 whitespace-nowrap align-middle"${_scopeId}><div class="flex flex-col items-center justify-center gap-1"${_scopeId}><a${ssrRenderAttr("href", `/school/instructors/${encodeURIComponent(instructorProfile.slug)}`)} class="text-orange-600 dark:text-orange-200 hover:underline hover:text-orange-600 dark:hover:text-orange-200" target="_blank" rel="noopener noreferrer"${_scopeId}>${ssrInterpolate(instructorProfile.title)}</a><div class="text-center text-teal-600 dark:text-teal-200"${ssrRenderAttr("title", (_a = instructorProfile.user) == null ? void 0 : _a.id)}${_scopeId}>${ssrInterpolate(((_b = instructorProfile.user) == null ? void 0 : _b.name) || "—")}</div></div></td><td class="px-2 first:pl-11 last:pr-11 py-1 whitespace-nowrap"${_scopeId}><div class="flex justify-center items-center text-left uppercase text-violet-600 dark:text-violet-200"${_scopeId}>${ssrInterpolate(instructorProfile.experience_years)}</div></td><td class="px-2 first:pl-11 last:pr-11 py-1"${_scopeId}><div class="text-center text-sky-700 dark:text-sky-200"${ssrRenderAttr("title", unref(t)("quantity"))}${_scopeId}> [${ssrInterpolate(instructorProfile.courses_count ?? 0)}] </div>`);
              if (instructorProfile.courses && instructorProfile.courses.length) {
                _push2(`<div class="flex justify-start flex-wrap gap-1 p-1 border border-dashed border-slate-400 dark:border-slate-500"${_scopeId}><!--[-->`);
                ssrRenderList(instructorProfile.courses, (course) => {
                  _push2(`<a${ssrRenderAttr("href", `/school/courses/${encodeURIComponent(course.slug)}`)} target="_blank" rel="noopener noreferrer" class="inline-flex items-center rounded-sm bg-sky-100 px-2 py-0.5 text-[9px] font-semibold border border-slate-400 text-blue-700 hover:bg-sky-200 dark:bg-sky-900 dark:text-blue-300 dark:hover:bg-sky-800"${ssrRenderAttr("title", course.title)}${_scopeId}><span${ssrRenderAttr("title", `ID: ${course.id}`)}${_scopeId}>${ssrInterpolate(course.title)}</span></a>`);
                });
                _push2(`<!--]--></div>`);
              } else {
                _push2(`<div class="flex items-center justify-center text-slate-400 dark:text-slate-500"${_scopeId}> — </div>`);
              }
              _push2(`</td><td class="px-2 first:pl-11 last:pr-11 py-1 whitespace-nowrap"${_scopeId}><div class="flex justify-center items-center"${_scopeId}>`);
              if (Number(instructorProfile.hourly_rate) > 0) {
                _push2(`<div class="relative inline-flex text-center px-3 py-1 rounded bg-emerald-500"${_scopeId}><div class="absolute w-3 h-3 rounded-full bg-white left-0 -translate-x-1/2 top-1/2 -translate-y-1/2" aria-hidden="true"${_scopeId}></div><div class="absolute w-3 h-3 rounded-full bg-white right-0 translate-x-1/2 top-1/2 -translate-y-1/2" aria-hidden="true"${_scopeId}></div><span class="text-sm text-emerald-50 font-medium uppercase"${_scopeId}>${ssrInterpolate(instructorProfile.hourly_rate)}</span></div>`);
              } else {
                _push2(`<!---->`);
              }
              _push2(`</div></td><td class="px-2 first:pl-11 last:pr-11 py-1 whitespace-nowrap"${_scopeId}><div class="text-center"${_scopeId}>${ssrInterpolate(instructorProfile.views)}</div></td><td class="px-2 first:pl-11 last:pr-11 py-1 whitespace-nowrap"${_scopeId}>`);
              if (Number(instructorProfile.rating_count ?? 0) > 0) {
                _push2(`<div class="text-left uppercase text-slate-700 dark:text-slate-200"${_scopeId}>${ssrInterpolate(instructorProfile.rating_count)}</div>`);
              } else {
                _push2(`<div class="text-left text-slate-400 dark:text-slate-500"${_scopeId}> 0 </div>`);
              }
              _push2(`</td><td class="px-2 first:pl-11 last:pr-11 py-1 whitespace-nowrap"${_scopeId}><div class="text-left uppercase text-rose-500 dark:text-rose-300"${_scopeId}>${ssrInterpolate(instructorProfile.rating_avg ?? 0)}</div></td><td class="px-2 first:pl-11 last:pr-11 py-1 whitespace-nowrap"${_scopeId}><div class="flex justify-end space-x-2"${_scopeId}>`);
              _push2(ssrRenderComponent(_sfc_main$5, {
                isActive: instructorProfile.activity,
                onToggleActivity: ($event) => _ctx.$emit("toggle-activity", instructorProfile),
                title: instructorProfile.activity ? unref(t)("enabled") : unref(t)("disabled")
              }, null, _parent2, _scopeId));
              _push2(ssrRenderComponent(_sfc_main$6, {
                href: _ctx.route("admin.schoolInstructorProfiles.edit", instructorProfile.id)
              }, null, _parent2, _scopeId));
              _push2(ssrRenderComponent(_sfc_main$7, {
                onDelete: ($event) => _ctx.$emit("delete", instructorProfile.id)
              }, null, _parent2, _scopeId));
              _push2(`</div></td><td class="px-2 first:pl-11 last:pr-11 py-1 whitespace-nowrap"${_scopeId}><div class="text-center"${_scopeId}><div class="text-center"${_scopeId}><input type="checkbox"${ssrIncludeBooleanAttr(__props.selectedInstructorProfiles.includes(instructorProfile.id)) ? " checked" : ""}${_scopeId}></div></div></td></tr>`);
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
                  createVNode("td", { class: "px-2 first:pl-11 last:pr-11 py-1 whitespace-nowrap" }, [
                    createVNode("div", { class: "text-center text-slate-800 dark:text-blue-200" }, toDisplayString(instructorProfile.id), 1)
                  ]),
                  createVNode("td", { class: "first:pl-11 last:pr-11 py-1" }, [
                    createVNode("div", { class: "relative w-32 h-32 flex items-center justify-center" }, [
                      instructorProfile.images && instructorProfile.images.length ? (openBlock(), createBlock("img", {
                        key: 0,
                        src: getPrimaryImage(instructorProfile).webp_url || getPrimaryImage(instructorProfile).url,
                        alt: getPrimaryImage(instructorProfile).alt || unref(t)("defaultImageAlt"),
                        title: getPrimaryImage(instructorProfile).caption || unref(t)("postImage"),
                        class: "h-20 w-20 object-cover rounded-full border border-slate-300 dark:border-slate-500"
                      }, null, 8, ["src", "alt", "title"])) : (openBlock(), createBlock("img", {
                        key: 1,
                        src: "/storage/school/school_instructor_profile_images/default-image.png",
                        alt: unref(t)("defaultImageTitle"),
                        class: "h-20 w-20 object-cover rounded-full border border-slate-300 dark:border-slate-500"
                      }, null, 8, ["alt"]))
                    ])
                  ]),
                  createVNode("td", { class: "px-2 first:pl-11 last:pr-11 py-1 whitespace-nowrap align-middle" }, [
                    createVNode("div", { class: "flex flex-col items-center justify-center gap-1" }, [
                      createVNode("a", {
                        href: `/school/instructors/${encodeURIComponent(instructorProfile.slug)}`,
                        class: "text-orange-600 dark:text-orange-200 hover:underline hover:text-orange-600 dark:hover:text-orange-200",
                        target: "_blank",
                        rel: "noopener noreferrer"
                      }, toDisplayString(instructorProfile.title), 9, ["href"]),
                      createVNode("div", {
                        class: "text-center text-teal-600 dark:text-teal-200",
                        title: (_c = instructorProfile.user) == null ? void 0 : _c.id
                      }, toDisplayString(((_d = instructorProfile.user) == null ? void 0 : _d.name) || "—"), 9, ["title"])
                    ])
                  ]),
                  createVNode("td", { class: "px-2 first:pl-11 last:pr-11 py-1 whitespace-nowrap" }, [
                    createVNode("div", { class: "flex justify-center items-center text-left uppercase text-violet-600 dark:text-violet-200" }, toDisplayString(instructorProfile.experience_years), 1)
                  ]),
                  createVNode("td", { class: "px-2 first:pl-11 last:pr-11 py-1" }, [
                    createVNode("div", {
                      class: "text-center text-sky-700 dark:text-sky-200",
                      title: unref(t)("quantity")
                    }, " [" + toDisplayString(instructorProfile.courses_count ?? 0) + "] ", 9, ["title"]),
                    instructorProfile.courses && instructorProfile.courses.length ? (openBlock(), createBlock("div", {
                      key: 0,
                      class: "flex justify-start flex-wrap gap-1 p-1 border border-dashed border-slate-400 dark:border-slate-500"
                    }, [
                      (openBlock(true), createBlock(Fragment, null, renderList(instructorProfile.courses, (course) => {
                        return openBlock(), createBlock("a", {
                          key: course.id,
                          href: `/school/courses/${encodeURIComponent(course.slug)}`,
                          target: "_blank",
                          rel: "noopener noreferrer",
                          class: "inline-flex items-center rounded-sm bg-sky-100 px-2 py-0.5 text-[9px] font-semibold border border-slate-400 text-blue-700 hover:bg-sky-200 dark:bg-sky-900 dark:text-blue-300 dark:hover:bg-sky-800",
                          title: course.title
                        }, [
                          createVNode("span", {
                            title: `ID: ${course.id}`
                          }, toDisplayString(course.title), 9, ["title"])
                        ], 8, ["href", "title"]);
                      }), 128))
                    ])) : (openBlock(), createBlock("div", {
                      key: 1,
                      class: "flex items-center justify-center text-slate-400 dark:text-slate-500"
                    }, " — "))
                  ]),
                  createVNode("td", { class: "px-2 first:pl-11 last:pr-11 py-1 whitespace-nowrap" }, [
                    createVNode("div", { class: "flex justify-center items-center" }, [
                      Number(instructorProfile.hourly_rate) > 0 ? (openBlock(), createBlock("div", {
                        key: 0,
                        class: "relative inline-flex text-center px-3 py-1 rounded bg-emerald-500"
                      }, [
                        createVNode("div", {
                          class: "absolute w-3 h-3 rounded-full bg-white left-0 -translate-x-1/2 top-1/2 -translate-y-1/2",
                          "aria-hidden": "true"
                        }),
                        createVNode("div", {
                          class: "absolute w-3 h-3 rounded-full bg-white right-0 translate-x-1/2 top-1/2 -translate-y-1/2",
                          "aria-hidden": "true"
                        }),
                        createVNode("span", { class: "text-sm text-emerald-50 font-medium uppercase" }, toDisplayString(instructorProfile.hourly_rate), 1)
                      ])) : createCommentVNode("", true)
                    ])
                  ]),
                  createVNode("td", { class: "px-2 first:pl-11 last:pr-11 py-1 whitespace-nowrap" }, [
                    createVNode("div", { class: "text-center" }, toDisplayString(instructorProfile.views), 1)
                  ]),
                  createVNode("td", { class: "px-2 first:pl-11 last:pr-11 py-1 whitespace-nowrap" }, [
                    Number(instructorProfile.rating_count ?? 0) > 0 ? (openBlock(), createBlock("div", {
                      key: 0,
                      class: "text-left uppercase text-slate-700 dark:text-slate-200"
                    }, toDisplayString(instructorProfile.rating_count), 1)) : (openBlock(), createBlock("div", {
                      key: 1,
                      class: "text-left text-slate-400 dark:text-slate-500"
                    }, " 0 "))
                  ]),
                  createVNode("td", { class: "px-2 first:pl-11 last:pr-11 py-1 whitespace-nowrap" }, [
                    createVNode("div", { class: "text-left uppercase text-rose-500 dark:text-rose-300" }, toDisplayString(instructorProfile.rating_avg ?? 0), 1)
                  ]),
                  createVNode("td", { class: "px-2 first:pl-11 last:pr-11 py-1 whitespace-nowrap" }, [
                    createVNode("div", { class: "flex justify-end space-x-2" }, [
                      createVNode(_sfc_main$5, {
                        isActive: instructorProfile.activity,
                        onToggleActivity: ($event) => _ctx.$emit("toggle-activity", instructorProfile),
                        title: instructorProfile.activity ? unref(t)("enabled") : unref(t)("disabled")
                      }, null, 8, ["isActive", "onToggleActivity", "title"]),
                      createVNode(_sfc_main$6, {
                        href: _ctx.route("admin.schoolInstructorProfiles.edit", instructorProfile.id)
                      }, null, 8, ["href"]),
                      createVNode(_sfc_main$7, {
                        onDelete: ($event) => _ctx.$emit("delete", instructorProfile.id)
                      }, null, 8, ["onDelete"])
                    ])
                  ]),
                  createVNode("td", { class: "px-2 first:pl-11 last:pr-11 py-1 whitespace-nowrap" }, [
                    createVNode("div", { class: "text-center" }, [
                      createVNode("div", { class: "text-center" }, [
                        createVNode("input", {
                          type: "checkbox",
                          checked: __props.selectedInstructorProfiles.includes(instructorProfile.id),
                          onChange: ($event) => _ctx.$emit("toggle-select", instructorProfile.id)
                        }, null, 40, ["checked", "onChange"])
                      ])
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
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Components/Admin/School/SchoolInstructorProfile/Table/InstructorProfileTable.vue");
  return _sfc_setup$2 ? _sfc_setup$2(props, ctx) : void 0;
};
const _sfc_main$1 = {
  __name: "InstructorCardGrid",
  __ssrInlineRender: true,
  props: {
    instructorProfiles: {
      type: Array,
      default: () => []
    },
    selectedInstructorProfiles: {
      type: Array,
      default: () => []
    }
  },
  emits: [
    "toggle-activity",
    "delete",
    "update-sort-order",
    "toggle-select",
    "toggle-all"
  ],
  setup(__props, { emit: __emit }) {
    const { t } = useI18n();
    const props = __props;
    const emits = __emit;
    const localInstructorProfiles = ref([]);
    watch(
      () => props.instructorProfiles,
      (newVal) => {
        localInstructorProfiles.value = JSON.parse(JSON.stringify(newVal || []));
      },
      { immediate: true, deep: true }
    );
    const handleDragEnd = () => {
      const newOrderIds = localInstructorProfiles.value.map(
        (instructorProfile) => instructorProfile.id
      );
      emits("update-sort-order", newOrderIds);
    };
    const getPrimaryImage = (instructorProfile) => {
      if (instructorProfile.images && instructorProfile.images.length) {
        return [...instructorProfile.images].sort((a, b) => a.order - b.order)[0];
      }
      return null;
    };
    return (_ctx, _push, _parent, _attrs) => {
      _push(`<div${ssrRenderAttrs(mergeProps({ class: "bg-white dark:bg-slate-700 shadow-lg rounded-sm border border-slate-400 dark:border-slate-500 relative" }, _attrs))}><div class="flex items-center justify-between px-3 py-2 border-b border-slate-400 dark:border-slate-500"><div class="text-xs text-slate-600 dark:text-slate-200">${ssrInterpolate(unref(t)("selected"))}: ${ssrInterpolate(__props.selectedInstructorProfiles.length)}</div>`);
      if (localInstructorProfiles.value.length) {
        _push(`<label class="flex items-center text-xs text-slate-600 dark:text-slate-200 cursor-pointer"><span>${ssrInterpolate(unref(t)("selectAll"))}</span><input type="checkbox" class="mx-2"></label>`);
      } else {
        _push(`<!---->`);
      }
      _push(`</div>`);
      if (localInstructorProfiles.value.length) {
        _push(`<div class="p-3">`);
        _push(ssrRenderComponent(unref(draggable), {
          tag: "div",
          modelValue: localInstructorProfiles.value,
          "onUpdate:modelValue": ($event) => localInstructorProfiles.value = $event,
          "item-key": "id",
          onEnd: handleDragEnd,
          handle: ".drag-handle",
          class: "grid gap-3 grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4"
        }, {
          item: withCtx(({ element: instructorProfile }, _push2, _parent2, _scopeId) => {
            var _a, _b, _c, _d;
            if (_push2) {
              _push2(`<div class="relative flex flex-col h-full rounded-md border border-slate-400 dark:border-slate-500 bg-slate-50/70 dark:bg-slate-800/80 shadow-sm hover:shadow-md transition-shadow duration-150"${_scopeId}><div class="flex items-center justify-between px-2 py-1 border-b border-dashed border-slate-400 dark:border-slate-500"${_scopeId}><div class="flex items-center space-x-2"${_scopeId}><button type="button" class="drag-handle text-slate-400 hover:text-slate-700 dark:hover:text-slate-100"${ssrRenderAttr("title", unref(t)("dragDrop"))}${_scopeId}><svg class="w-4 h-4" fill="currentColor" viewBox="0 0 128 128"${_scopeId}><path d="M10.4,119.2l44.1-19.4c2.2-1,3.6-3.1,3.6-5.5v-8.9c-2.8,2.1-6.2,3.3-9.8,3.3c-4.4,0-8.5-1.7-11.5-4.8L20.3,67.4l-0.2-0.2
                                            L19.9,67c-0.4-0.5-1.2-1.3-1.9-2.4c-0.8-1.3-1.5-2.8-1.9-4.4c-1.5-5.4,0-11.5,4.1-15.7l0,0c0.6-0.6,3.2-3.2,16.3-16.3
                                            c3.1-3.1,7.2-4.8,11.5-4.8c3.7,0,7.3,1.3,10.1,3.5V12.1c0-4.3-4.5-7.2-8.4-5.5L5.6,26.1C3.4,27,2,29.2,2,31.6v82.2
                                            C2,118.1,6.5,121,10.4,119.2z"${_scopeId}></path><path d="M91.6,28.2c14,14,15.9,15.9,16.3,16.3l0,0c4,4.2,5.6,10.2,4.1,15.7c-0.5,1.6-1.1,3.1-1.9,4.4c-0.7,1.1-1.5,2-1.9,2.4
                                            l-0.2,0.2l-0.2,0.2L91.2,83.9c-3.1,3.1-7.2,4.8-11.5,4.8c-3.6,0-7-1.2-9.8-3.3v8.9c0,2.4,1.4,4.5,3.6,5.5l44.1,19.4
                                            c4,1.7,8.4-1.2,8.4-5.5V31.6c0-2.4-1.4-4.5-3.6-5.5L78.3,6.6c-4-1.7-8.4,1.2-8.4,5.5V27c2.9-2.3,6.4-3.5,10.1-3.5
                                            C84.4,23.4,88.5,25.1,91.6,28.2z"${_scopeId}></path><path d="M100.6,51.4c0,0-16.1-16.1-16.1-16.1c-1.2-1.2-2.9-1.9-4.5-1.9s-3.2,0.6-4.5,1.9c-2.5,2.5-2.5,6.5,0,9l5.3,5.3H47.1l5.3-5.3
                                            c2.5-2.5,2.5-6.5,0-9c-1.2-1.2-2.9-1.9-4.5-1.9c-1.6,0-3.2,0.6-4.5,1.9c0,0-16.1,16.1-16.1,16.1c-2.5,2.5-2.2,6.4,0,9l16.5,16.5
                                            c1.2,1.2,2.9,1.9,4.5,1.9c1.6,0,3.2-0.6,4.5-1.9c2.5-2.5,2.5-6.5,0-9l-5.7-5.7h33.7l-5.7,5.7c-2.5,2.5-2.5,6.5,0,9
                                            c1.2,1.2,2.9,1.9,4.5,1.9c1.6,0,3.2-0.6,4.5-1.9c0,0,16.5-16.5,16.5-16.5C103.1,57.9,103,53.9,100.6,51.4z"${_scopeId}></path></svg></button><div class="text-[10px] font-semibold px-1.5 py-0.5 rounded-sm border border-gray-400 bg-slate-200 dark:bg-slate-700 text-slate-800 dark:text-blue-100"${ssrRenderAttr("title", `ID: ${instructorProfile.id}`)}${_scopeId}> ID: ${ssrInterpolate(instructorProfile.id)}</div></div><div class="flex items-center space-x-2"${_scopeId}><input type="checkbox"${ssrIncludeBooleanAttr(__props.selectedInstructorProfiles.includes(instructorProfile.id)) ? " checked" : ""}${_scopeId}></div></div><div class="relative w-full h-32 flex items-center justify-center bg-slate-200 dark:bg-slate-900"${_scopeId}>`);
              if ((_a = instructorProfile.images) == null ? void 0 : _a.length) {
                _push2(`<img${ssrRenderAttr("src", getPrimaryImage(instructorProfile).webp_url || getPrimaryImage(instructorProfile).url)}${ssrRenderAttr("alt", getPrimaryImage(instructorProfile).alt || unref(t)("defaultImageAlt"))} class="h-20 w-20 object-cover rounded-full border border-slate-300 dark:border-slate-500"${_scopeId}>`);
              } else {
                _push2(`<img src="/storage/school/school_instructor_profile_images/default-image.png"${ssrRenderAttr("alt", unref(t)("defaultImageTitle"))} class="h-20 w-20 object-cover rounded-full border border-slate-300 dark:border-slate-500"${_scopeId}>`);
              }
              _push2(`</div><div class="flex flex-col flex-1 px-3 py-2 space-y-1"${_scopeId}><a${ssrRenderAttr("href", `/instructors/${encodeURIComponent(instructorProfile.slug)}`)} target="_blank" rel="noopener noreferrer" class="text-sm font-semibold text-orange-600 dark:text-orange-200 hover:underline line-clamp-2 text-center"${ssrRenderAttr("title", instructorProfile.title)}${_scopeId}>${ssrInterpolate(instructorProfile.title)}</a><div class="font-semibold text-[13px] text-teal-700 dark:text-teal-200 text-center"${_scopeId}>${ssrInterpolate(((_b = instructorProfile.user) == null ? void 0 : _b.name) || "—")}</div><div class="flex justify-center items-center text-[11px] font-semibold text-blue-700 dark:text-blue-200 mt-1"${_scopeId}>${ssrInterpolate(unref(t)("experienceYears"))}: ${ssrInterpolate(instructorProfile.experience_years ?? "—")}</div><div class="flex justify-center items-center mt-1"${_scopeId}>`);
              if (Number(instructorProfile.hourly_rate) > 0) {
                _push2(`<div class="relative inline-flex text-center px-3 py-1 rounded bg-emerald-500"${_scopeId}><div class="absolute w-3 h-3 rounded-full bg-white left-0 -translate-x-1/2 top-1/2 -translate-y-1/2" aria-hidden="true"${_scopeId}></div><div class="absolute w-3 h-3 rounded-full bg-white right-0 translate-x-1/2 top-1/2 -translate-y-1/2" aria-hidden="true"${_scopeId}></div><span class="text-sm text-emerald-50 font-medium uppercase"${_scopeId}>${ssrInterpolate(instructorProfile.hourly_rate)}</span></div>`);
              } else {
                _push2(`<!---->`);
              }
              _push2(`</div><div class="pt-2 flex flex-wrap justify-center gap-4 font-semibold text-[11px] text-slate-900 dark:text-slate-200"${_scopeId}>`);
              if (instructorProfile.views > 0) {
                _push2(`<div${ssrRenderAttr("title", unref(t)("views"))} class="flex flex-row items-center"${_scopeId}><svg class="w-4 h-4 fill-current shrink-0" viewBox="0 0 16 16"${_scopeId}><path class="fill-current text-blue-600 dark:text-blue-300" d="M8 2C3.246 2 .251 7.29.127 7.515a.998.998 0 0 0 .002.975c.07.125 1.044 1.801 2.695 3.274C4.738 13.582 6.283 14 8 14c4.706 0 7.743-5.284 7.872-5.507a1 1 0 0 0 0-.98A13.292 13.292 0 0 0 8 2zm0 10a4 4 0 1 1 0-8 4 4 0 0 1 0 8zm0-6a2 2 0 1 0 0 4 2 2 0 0 0 0-4z"${_scopeId}></path></svg><span class="ml-1"${_scopeId}>${ssrInterpolate(instructorProfile.views ?? 0)}</span></div>`);
              } else {
                _push2(`<!---->`);
              }
              if (Number(instructorProfile.rating_count ?? 0) > 0) {
                _push2(`<div${ssrRenderAttr("title", unref(t)("ratingCount"))} class="flex flex-row items-center"${_scopeId}><svg xmlns="http://www.w3.org/2000/svg" height="24" width="24" viewBox="0 0 24 24" class="shrink-0 h-4 w-4"${_scopeId}><path class="fill-current text-red-400 dark:text-red-300" d="M14.586,9.439S15.7,2.858,11.138,0A8.055,8.055,0,0,1,8.1,5.831C6.149,7.546,2.481,11.4,2.52,15.51A9.435,9.435,0,0,0,7.7,24a5.975,5.975,0,0,1,2.091-4.132,4.877,4.877,0,0,0,1.869-3.278,8.786,8.786,0,0,1,4.652,7.322v.02a8.827,8.827,0,0,0,5.137-7.659c.324-3.863-1.792-9.112-3.668-10.828A10.192,10.192,0,0,1,14.586,9.439Z"${_scopeId}></path></svg><span class="ml-1"${_scopeId}>${ssrInterpolate(instructorProfile.rating_count)}</span></div>`);
              } else {
                _push2(`<!---->`);
              }
              if (Number(instructorProfile.rating_avg ?? 0) > 0) {
                _push2(`<div${ssrRenderAttr("title", unref(t)("ratingAvg"))} class="flex flex-row items-center"${_scopeId}><svg xmlns="http://www.w3.org/2000/svg" height="24" width="24" viewBox="0 0 24 24" class="shrink-0 h-4 w-4"${_scopeId}><path class="fill-current text-red-400 dark:text-red-300" d="M12.746,1.464l3.11,6.3L22.81,8.776a.831.831,0,0,1,.461,1.418l-5.033,4.9,1.188,6.926a.832.832,0,0,1-1.207.877L12,19.632,5.78,22.9a.833.833,0,0,1-1.207-.878L5.761,15.1l-5.033-4.9a.831.831,0,0,1,.461-1.418L8.143,7.765l3.11-6.3A.833.833,0,0,1,12.746,1.464Z"${_scopeId}></path></svg><span class="ml-1"${_scopeId}>${ssrInterpolate(instructorProfile.rating_avg)}</span></div>`);
              } else {
                _push2(`<!---->`);
              }
              _push2(`</div>`);
              if (instructorProfile.courses && instructorProfile.courses.length) {
                _push2(`<div class="flex justify-center flex-wrap gap-1 p-1 border border-dashed border-slate-400 dark:border-slate-500"${_scopeId}><!--[-->`);
                ssrRenderList(instructorProfile.courses, (course) => {
                  _push2(`<a${ssrRenderAttr("href", `/school/courses/${encodeURIComponent(course.slug)}`)} target="_blank" rel="noopener noreferrer" class="inline-flex items-center rounded-sm bg-sky-100 px-2 py-0.5 text-[9px] font-semibold border border-slate-400 text-blue-700 hover:bg-sky-200 dark:bg-sky-900 dark:text-blue-300 dark:hover:bg-sky-800"${ssrRenderAttr("title", course.title)}${_scopeId}><span${ssrRenderAttr("title", `ID: ${course.id}`)}${_scopeId}>${ssrInterpolate(course.title)}</span></a>`);
                });
                _push2(`<!--]--></div>`);
              } else {
                _push2(`<div class="flex items-center justify-center text-slate-400 dark:text-slate-500"${_scopeId}> — </div>`);
              }
              _push2(`</div><div class="flex items-center justify-center px-3 py-2 border-t border-dashed border-slate-400 dark:border-slate-500"${_scopeId}><div class="flex items-center space-x-1"${_scopeId}>`);
              _push2(ssrRenderComponent(_sfc_main$5, {
                isActive: instructorProfile.activity,
                onToggleActivity: ($event) => _ctx.$emit("toggle-activity", instructorProfile),
                title: instructorProfile.activity ? unref(t)("enabled") : unref(t)("disabled")
              }, null, _parent2, _scopeId));
              _push2(ssrRenderComponent(_sfc_main$6, {
                href: _ctx.route("admin.schoolInstructorProfiles.edit", instructorProfile.id)
              }, null, _parent2, _scopeId));
              _push2(ssrRenderComponent(_sfc_main$7, {
                onDelete: ($event) => _ctx.$emit("delete", instructorProfile.id)
              }, null, _parent2, _scopeId));
              _push2(`</div></div></div>`);
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
                          viewBox: "0 0 128 128"
                        }, [
                          createVNode("path", { d: "M10.4,119.2l44.1-19.4c2.2-1,3.6-3.1,3.6-5.5v-8.9c-2.8,2.1-6.2,3.3-9.8,3.3c-4.4,0-8.5-1.7-11.5-4.8L20.3,67.4l-0.2-0.2\n                                            L19.9,67c-0.4-0.5-1.2-1.3-1.9-2.4c-0.8-1.3-1.5-2.8-1.9-4.4c-1.5-5.4,0-11.5,4.1-15.7l0,0c0.6-0.6,3.2-3.2,16.3-16.3\n                                            c3.1-3.1,7.2-4.8,11.5-4.8c3.7,0,7.3,1.3,10.1,3.5V12.1c0-4.3-4.5-7.2-8.4-5.5L5.6,26.1C3.4,27,2,29.2,2,31.6v82.2\n                                            C2,118.1,6.5,121,10.4,119.2z" }),
                          createVNode("path", { d: "M91.6,28.2c14,14,15.9,15.9,16.3,16.3l0,0c4,4.2,5.6,10.2,4.1,15.7c-0.5,1.6-1.1,3.1-1.9,4.4c-0.7,1.1-1.5,2-1.9,2.4\n                                            l-0.2,0.2l-0.2,0.2L91.2,83.9c-3.1,3.1-7.2,4.8-11.5,4.8c-3.6,0-7-1.2-9.8-3.3v8.9c0,2.4,1.4,4.5,3.6,5.5l44.1,19.4\n                                            c4,1.7,8.4-1.2,8.4-5.5V31.6c0-2.4-1.4-4.5-3.6-5.5L78.3,6.6c-4-1.7-8.4,1.2-8.4,5.5V27c2.9-2.3,6.4-3.5,10.1-3.5\n                                            C84.4,23.4,88.5,25.1,91.6,28.2z" }),
                          createVNode("path", { d: "M100.6,51.4c0,0-16.1-16.1-16.1-16.1c-1.2-1.2-2.9-1.9-4.5-1.9s-3.2,0.6-4.5,1.9c-2.5,2.5-2.5,6.5,0,9l5.3,5.3H47.1l5.3-5.3\n                                            c2.5-2.5,2.5-6.5,0-9c-1.2-1.2-2.9-1.9-4.5-1.9c-1.6,0-3.2,0.6-4.5,1.9c0,0-16.1,16.1-16.1,16.1c-2.5,2.5-2.2,6.4,0,9l16.5,16.5\n                                            c1.2,1.2,2.9,1.9,4.5,1.9c1.6,0,3.2-0.6,4.5-1.9c2.5-2.5,2.5-6.5,0-9l-5.7-5.7h33.7l-5.7,5.7c-2.5,2.5-2.5,6.5,0,9\n                                            c1.2,1.2,2.9,1.9,4.5,1.9c1.6,0,3.2-0.6,4.5-1.9c0,0,16.5-16.5,16.5-16.5C103.1,57.9,103,53.9,100.6,51.4z" })
                        ]))
                      ], 8, ["title"]),
                      createVNode("div", {
                        class: "text-[10px] font-semibold px-1.5 py-0.5 rounded-sm border border-gray-400 bg-slate-200 dark:bg-slate-700 text-slate-800 dark:text-blue-100",
                        title: `ID: ${instructorProfile.id}`
                      }, " ID: " + toDisplayString(instructorProfile.id), 9, ["title"])
                    ]),
                    createVNode("div", { class: "flex items-center space-x-2" }, [
                      createVNode("input", {
                        type: "checkbox",
                        checked: __props.selectedInstructorProfiles.includes(instructorProfile.id),
                        onChange: ($event) => _ctx.$emit("toggle-select", instructorProfile.id)
                      }, null, 40, ["checked", "onChange"])
                    ])
                  ]),
                  createVNode("div", { class: "relative w-full h-32 flex items-center justify-center bg-slate-200 dark:bg-slate-900" }, [
                    ((_c = instructorProfile.images) == null ? void 0 : _c.length) ? (openBlock(), createBlock("img", {
                      key: 0,
                      src: getPrimaryImage(instructorProfile).webp_url || getPrimaryImage(instructorProfile).url,
                      alt: getPrimaryImage(instructorProfile).alt || unref(t)("defaultImageAlt"),
                      class: "h-20 w-20 object-cover rounded-full border border-slate-300 dark:border-slate-500"
                    }, null, 8, ["src", "alt"])) : (openBlock(), createBlock("img", {
                      key: 1,
                      src: "/storage/school/school_instructor_profile_images/default-image.png",
                      alt: unref(t)("defaultImageTitle"),
                      class: "h-20 w-20 object-cover rounded-full border border-slate-300 dark:border-slate-500"
                    }, null, 8, ["alt"]))
                  ]),
                  createVNode("div", { class: "flex flex-col flex-1 px-3 py-2 space-y-1" }, [
                    createVNode("a", {
                      href: `/instructors/${encodeURIComponent(instructorProfile.slug)}`,
                      target: "_blank",
                      rel: "noopener noreferrer",
                      class: "text-sm font-semibold text-orange-600 dark:text-orange-200 hover:underline line-clamp-2 text-center",
                      title: instructorProfile.title
                    }, toDisplayString(instructorProfile.title), 9, ["href", "title"]),
                    createVNode("div", { class: "font-semibold text-[13px] text-teal-700 dark:text-teal-200 text-center" }, toDisplayString(((_d = instructorProfile.user) == null ? void 0 : _d.name) || "—"), 1),
                    createVNode("div", { class: "flex justify-center items-center text-[11px] font-semibold text-blue-700 dark:text-blue-200 mt-1" }, toDisplayString(unref(t)("experienceYears")) + ": " + toDisplayString(instructorProfile.experience_years ?? "—"), 1),
                    createVNode("div", { class: "flex justify-center items-center mt-1" }, [
                      Number(instructorProfile.hourly_rate) > 0 ? (openBlock(), createBlock("div", {
                        key: 0,
                        class: "relative inline-flex text-center px-3 py-1 rounded bg-emerald-500"
                      }, [
                        createVNode("div", {
                          class: "absolute w-3 h-3 rounded-full bg-white left-0 -translate-x-1/2 top-1/2 -translate-y-1/2",
                          "aria-hidden": "true"
                        }),
                        createVNode("div", {
                          class: "absolute w-3 h-3 rounded-full bg-white right-0 translate-x-1/2 top-1/2 -translate-y-1/2",
                          "aria-hidden": "true"
                        }),
                        createVNode("span", { class: "text-sm text-emerald-50 font-medium uppercase" }, toDisplayString(instructorProfile.hourly_rate), 1)
                      ])) : createCommentVNode("", true)
                    ]),
                    createVNode("div", { class: "pt-2 flex flex-wrap justify-center gap-4 font-semibold text-[11px] text-slate-900 dark:text-slate-200" }, [
                      instructorProfile.views > 0 ? (openBlock(), createBlock("div", {
                        key: 0,
                        title: unref(t)("views"),
                        class: "flex flex-row items-center"
                      }, [
                        (openBlock(), createBlock("svg", {
                          class: "w-4 h-4 fill-current shrink-0",
                          viewBox: "0 0 16 16"
                        }, [
                          createVNode("path", {
                            class: "fill-current text-blue-600 dark:text-blue-300",
                            d: "M8 2C3.246 2 .251 7.29.127 7.515a.998.998 0 0 0 .002.975c.07.125 1.044 1.801 2.695 3.274C4.738 13.582 6.283 14 8 14c4.706 0 7.743-5.284 7.872-5.507a1 1 0 0 0 0-.98A13.292 13.292 0 0 0 8 2zm0 10a4 4 0 1 1 0-8 4 4 0 0 1 0 8zm0-6a2 2 0 1 0 0 4 2 2 0 0 0 0-4z"
                          })
                        ])),
                        createVNode("span", { class: "ml-1" }, toDisplayString(instructorProfile.views ?? 0), 1)
                      ], 8, ["title"])) : createCommentVNode("", true),
                      Number(instructorProfile.rating_count ?? 0) > 0 ? (openBlock(), createBlock("div", {
                        key: 1,
                        title: unref(t)("ratingCount"),
                        class: "flex flex-row items-center"
                      }, [
                        (openBlock(), createBlock("svg", {
                          xmlns: "http://www.w3.org/2000/svg",
                          height: "24",
                          width: "24",
                          viewBox: "0 0 24 24",
                          class: "shrink-0 h-4 w-4"
                        }, [
                          createVNode("path", {
                            class: "fill-current text-red-400 dark:text-red-300",
                            d: "M14.586,9.439S15.7,2.858,11.138,0A8.055,8.055,0,0,1,8.1,5.831C6.149,7.546,2.481,11.4,2.52,15.51A9.435,9.435,0,0,0,7.7,24a5.975,5.975,0,0,1,2.091-4.132,4.877,4.877,0,0,0,1.869-3.278,8.786,8.786,0,0,1,4.652,7.322v.02a8.827,8.827,0,0,0,5.137-7.659c.324-3.863-1.792-9.112-3.668-10.828A10.192,10.192,0,0,1,14.586,9.439Z"
                          })
                        ])),
                        createVNode("span", { class: "ml-1" }, toDisplayString(instructorProfile.rating_count), 1)
                      ], 8, ["title"])) : createCommentVNode("", true),
                      Number(instructorProfile.rating_avg ?? 0) > 0 ? (openBlock(), createBlock("div", {
                        key: 2,
                        title: unref(t)("ratingAvg"),
                        class: "flex flex-row items-center"
                      }, [
                        (openBlock(), createBlock("svg", {
                          xmlns: "http://www.w3.org/2000/svg",
                          height: "24",
                          width: "24",
                          viewBox: "0 0 24 24",
                          class: "shrink-0 h-4 w-4"
                        }, [
                          createVNode("path", {
                            class: "fill-current text-red-400 dark:text-red-300",
                            d: "M12.746,1.464l3.11,6.3L22.81,8.776a.831.831,0,0,1,.461,1.418l-5.033,4.9,1.188,6.926a.832.832,0,0,1-1.207.877L12,19.632,5.78,22.9a.833.833,0,0,1-1.207-.878L5.761,15.1l-5.033-4.9a.831.831,0,0,1,.461-1.418L8.143,7.765l3.11-6.3A.833.833,0,0,1,12.746,1.464Z"
                          })
                        ])),
                        createVNode("span", { class: "ml-1" }, toDisplayString(instructorProfile.rating_avg), 1)
                      ], 8, ["title"])) : createCommentVNode("", true)
                    ]),
                    instructorProfile.courses && instructorProfile.courses.length ? (openBlock(), createBlock("div", {
                      key: 0,
                      class: "flex justify-center flex-wrap gap-1 p-1 border border-dashed border-slate-400 dark:border-slate-500"
                    }, [
                      (openBlock(true), createBlock(Fragment, null, renderList(instructorProfile.courses, (course) => {
                        return openBlock(), createBlock("a", {
                          key: course.id,
                          href: `/school/courses/${encodeURIComponent(course.slug)}`,
                          target: "_blank",
                          rel: "noopener noreferrer",
                          class: "inline-flex items-center rounded-sm bg-sky-100 px-2 py-0.5 text-[9px] font-semibold border border-slate-400 text-blue-700 hover:bg-sky-200 dark:bg-sky-900 dark:text-blue-300 dark:hover:bg-sky-800",
                          title: course.title
                        }, [
                          createVNode("span", {
                            title: `ID: ${course.id}`
                          }, toDisplayString(course.title), 9, ["title"])
                        ], 8, ["href", "title"]);
                      }), 128))
                    ])) : (openBlock(), createBlock("div", {
                      key: 1,
                      class: "flex items-center justify-center text-slate-400 dark:text-slate-500"
                    }, " — "))
                  ]),
                  createVNode("div", { class: "flex items-center justify-center px-3 py-2 border-t border-dashed border-slate-400 dark:border-slate-500" }, [
                    createVNode("div", { class: "flex items-center space-x-1" }, [
                      createVNode(_sfc_main$5, {
                        isActive: instructorProfile.activity,
                        onToggleActivity: ($event) => _ctx.$emit("toggle-activity", instructorProfile),
                        title: instructorProfile.activity ? unref(t)("enabled") : unref(t)("disabled")
                      }, null, 8, ["isActive", "onToggleActivity", "title"]),
                      createVNode(_sfc_main$6, {
                        href: _ctx.route("admin.schoolInstructorProfiles.edit", instructorProfile.id)
                      }, null, 8, ["href"]),
                      createVNode(_sfc_main$7, {
                        onDelete: ($event) => _ctx.$emit("delete", instructorProfile.id)
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
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Components/Admin/School/SchoolInstructorProfile/View/InstructorCardGrid.vue");
  return _sfc_setup$1 ? _sfc_setup$1(props, ctx) : void 0;
};
const _sfc_main = {
  __name: "Index",
  __ssrInlineRender: true,
  props: {
    currentLocale: { type: String, default: "" },
    availableLocales: { type: Array, default: () => [] },
    adminSchoolInstructorsProcessingMode: { type: String, default: "frontend" },
    useServerProcessing: { type: Boolean, default: false },
    instructorProfiles: { type: [Array, Object], default: () => [] },
    instructorProfilesCount: { type: Number, default: 0 },
    adminSchoolInstructorsPerPage: { type: Number, default: 6 },
    adminSchoolInstructorsDefaultSort: { type: String, default: "idDesc" },
    sortParam: { type: String, default: "" },
    search: { type: String, default: "" },
    errors: { type: Object, default: () => ({}) }
  },
  setup(__props) {
    const { t } = useI18n();
    const toast = useToast();
    const props = __props;
    const viewMode = ref(localStorage.getItem("admin_view_mode_instructors") || "table");
    watch(viewMode, (val) => {
      localStorage.setItem("admin_view_mode_instructors", val);
    });
    const instructorProfilesList = computed(() => {
      var _a;
      if (Array.isArray(props.instructorProfiles)) {
        return props.instructorProfiles;
      }
      if (Array.isArray((_a = props.instructorProfiles) == null ? void 0 : _a.data)) {
        return props.instructorProfiles.data;
      }
      return [];
    });
    const localInstructorProfiles = ref([]);
    watch(
      instructorProfilesList,
      (newVal) => {
        localInstructorProfiles.value = JSON.parse(JSON.stringify(newVal || []));
      },
      { immediate: true, deep: true }
    );
    const itemsPerPage = ref(props.adminSchoolInstructorsPerPage || 6);
    watch(itemsPerPage, (newVal) => {
      router.put(
        route("admin.settings.updateAdminCountSchoolInstructors"),
        { value: newVal },
        {
          preserveScroll: true,
          preserveState: true,
          onSuccess: () => toast.info(`Показ ${newVal} элементов на странице.`),
          onError: (errors) => toast.error(errors.value || "Ошибка обновления кол-ва элементов.")
        }
      );
    });
    const sortParam = ref(props.sortParam || props.adminSchoolInstructorsDefaultSort || "idDesc");
    watch(sortParam, (newVal) => {
      currentPage.value = 1;
      router.put(
        route("admin.settings.updateAdminSortSchoolInstructors"),
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
    const getInstructorTitle = (instructorProfile) => {
      var _a;
      return (instructorProfile == null ? void 0 : instructorProfile.title) || ((_a = instructorProfile == null ? void 0 : instructorProfile.user) == null ? void 0 : _a.name) || `ID: ${instructorProfile == null ? void 0 : instructorProfile.id}`;
    };
    const getInstructorShort = (instructorProfile) => {
      return (instructorProfile == null ? void 0 : instructorProfile.short) || "";
    };
    const getInstructorBio = (instructorProfile) => {
      return (instructorProfile == null ? void 0 : instructorProfile.bio) || "";
    };
    const getInstructorSlug = (instructorProfile) => {
      return (instructorProfile == null ? void 0 : instructorProfile.slug) || "";
    };
    const getUserName = (instructorProfile) => {
      var _a;
      return ((_a = instructorProfile == null ? void 0 : instructorProfile.user) == null ? void 0 : _a.name) || "";
    };
    const getUserEmail = (instructorProfile) => {
      var _a;
      return ((_a = instructorProfile == null ? void 0 : instructorProfile.user) == null ? void 0 : _a.email) || "";
    };
    const getNestedTitle = (item) => {
      return (item == null ? void 0 : item.title) || "";
    };
    const getCoursesText = (instructorProfile) => {
      const courses = Array.isArray(instructorProfile == null ? void 0 : instructorProfile.courses) ? instructorProfile.courses : [];
      return courses.map(getNestedTitle).filter(Boolean).join(" ");
    };
    const byNumberAsc = (field) => (a, b) => safeNumber(a == null ? void 0 : a[field]) - safeNumber(b == null ? void 0 : b[field]) || safeNumber(a == null ? void 0 : a.id) - safeNumber(b == null ? void 0 : b.id);
    const byNumberDesc = (field) => (a, b) => safeNumber(b == null ? void 0 : b[field]) - safeNumber(a == null ? void 0 : a[field]) || safeNumber(b == null ? void 0 : b.id) - safeNumber(a == null ? void 0 : a.id);
    const sortInstructorProfiles = (items) => {
      const list = (items || []).slice();
      if (sortParam.value === "activity") return list.filter((item) => !!item.activity);
      if (sortParam.value === "inactive") return list.filter((item) => !item.activity);
      const sortMap = {
        idAsc: byNumberAsc("id"),
        idDesc: byNumberDesc("id"),
        sortAsc: byNumberAsc("sort"),
        sortDesc: byNumberDesc("sort"),
        titleAsc: (a, b) => normalize(getInstructorTitle(a)).localeCompare(normalize(getInstructorTitle(b)), props.currentLocale) || safeNumber(a == null ? void 0 : a.id) - safeNumber(b == null ? void 0 : b.id),
        titleDesc: (a, b) => normalize(getInstructorTitle(b)).localeCompare(normalize(getInstructorTitle(a)), props.currentLocale) || safeNumber(b == null ? void 0 : b.id) - safeNumber(a == null ? void 0 : a.id),
        slugAsc: (a, b) => normalize(getInstructorSlug(a)).localeCompare(normalize(getInstructorSlug(b)), props.currentLocale) || safeNumber(a == null ? void 0 : a.id) - safeNumber(b == null ? void 0 : b.id),
        slugDesc: (a, b) => normalize(getInstructorSlug(b)).localeCompare(normalize(getInstructorSlug(a)), props.currentLocale) || safeNumber(b == null ? void 0 : b.id) - safeNumber(a == null ? void 0 : a.id),
        viewsAsc: byNumberAsc("views"),
        viewsDesc: byNumberDesc("views"),
        ratingAvgAsc: byNumberAsc("rating_avg"),
        ratingAvgDesc: byNumberDesc("rating_avg"),
        ratingCountAsc: byNumberAsc("rating_count"),
        ratingCountDesc: byNumberDesc("rating_count"),
        hourlyRateAsc: byNumberAsc("hourly_rate"),
        hourlyRateDesc: byNumberDesc("hourly_rate"),
        experienceAsc: byNumberAsc("experience_years"),
        experienceDesc: byNumberDesc("experience_years"),
        coursesAsc: byNumberAsc("courses_count"),
        coursesDesc: byNumberDesc("courses_count"),
        payoutsAsc: byNumberAsc("payouts_count"),
        payoutsDesc: byNumberDesc("payouts_count"),
        imagesAsc: byNumberAsc("images_count"),
        imagesDesc: byNumberDesc("images_count"),
        activityAsc: byNumberAsc("activity"),
        activityDesc: byNumberDesc("activity"),
        dateAsc: (a, b) => safeDate(a == null ? void 0 : a.created_at) - safeDate(b == null ? void 0 : b.created_at) || safeNumber(a == null ? void 0 : a.id) - safeNumber(b == null ? void 0 : b.id),
        dateDesc: (a, b) => safeDate(b == null ? void 0 : b.created_at) - safeDate(a == null ? void 0 : a.created_at) || safeNumber(b == null ? void 0 : b.id) - safeNumber(a == null ? void 0 : a.id),
        createdAtAsc: (a, b) => safeDate(a == null ? void 0 : a.created_at) - safeDate(b == null ? void 0 : b.created_at) || safeNumber(a == null ? void 0 : a.id) - safeNumber(b == null ? void 0 : b.id),
        createdAtDesc: (a, b) => safeDate(b == null ? void 0 : b.created_at) - safeDate(a == null ? void 0 : a.created_at) || safeNumber(b == null ? void 0 : b.id) - safeNumber(a == null ? void 0 : a.id),
        updatedAtAsc: (a, b) => safeDate(a == null ? void 0 : a.updated_at) - safeDate(b == null ? void 0 : b.updated_at) || safeNumber(a == null ? void 0 : a.id) - safeNumber(b == null ? void 0 : b.id),
        updatedAtDesc: (a, b) => safeDate(b == null ? void 0 : b.updated_at) - safeDate(a == null ? void 0 : a.updated_at) || safeNumber(b == null ? void 0 : b.id) - safeNumber(a == null ? void 0 : a.id)
      };
      return sortMap[sortParam.value] ? list.sort(sortMap[sortParam.value]) : list;
    };
    const filteredInstructorProfiles = computed(() => {
      let filtered = localInstructorProfiles.value || [];
      const query = normalize(searchQuery.value);
      if (!query) {
        return sortInstructorProfiles(filtered);
      }
      filtered = filtered.filter((instructorProfile) => {
        const title = normalize(getInstructorTitle(instructorProfile));
        const short = normalize(getInstructorShort(instructorProfile));
        const bio = normalize(getInstructorBio(instructorProfile));
        const slug = normalize(getInstructorSlug(instructorProfile));
        const userName = normalize(getUserName(instructorProfile));
        const userEmail = normalize(getUserEmail(instructorProfile));
        const courses = normalize(getCoursesText(instructorProfile));
        return title.includes(query) || short.includes(query) || bio.includes(query) || slug.includes(query) || userName.includes(query) || userEmail.includes(query) || courses.includes(query);
      });
      return sortInstructorProfiles(filtered);
    });
    const paginatedInstructorProfiles = computed(() => {
      const per = Number(itemsPerPage.value || 10);
      const start = (currentPage.value - 1) * per;
      return filteredInstructorProfiles.value.slice(start, start + per);
    });
    const displayedInstructorProfiles = computed(() => {
      return props.useServerProcessing ? instructorProfilesList.value : paginatedInstructorProfiles.value;
    });
    watch([itemsPerPage, searchQuery], () => {
      currentPage.value = 1;
    });
    const showConfirmDeleteModal = ref(false);
    const instructorProfileToDeleteId = ref(null);
    const instructorProfileToDeleteTitle = ref("");
    const confirmDelete = (instructorProfileOrId, title = null) => {
      if (typeof instructorProfileOrId === "object") {
        instructorProfileToDeleteId.value = instructorProfileOrId.id;
        instructorProfileToDeleteTitle.value = title || getInstructorTitle(instructorProfileOrId);
      } else {
        instructorProfileToDeleteId.value = instructorProfileOrId;
        instructorProfileToDeleteTitle.value = title || `ID: ${instructorProfileOrId}`;
      }
      showConfirmDeleteModal.value = true;
    };
    const closeModal = () => {
      showConfirmDeleteModal.value = false;
      instructorProfileToDeleteId.value = null;
      instructorProfileToDeleteTitle.value = "";
    };
    const deleteInstructorProfile = () => {
      if (instructorProfileToDeleteId.value === null) return;
      const idToDelete = instructorProfileToDeleteId.value;
      const titleToDelete = instructorProfileToDeleteTitle.value;
      router.delete(route("admin.schoolInstructorProfiles.destroy", {
        schoolInstructorProfile: idToDelete
      }), {
        preserveScroll: true,
        preserveState: false,
        onSuccess: () => {
          toast.success(`Инструктор "${titleToDelete || "ID: " + idToDelete}" удалён.`);
        },
        onError: (errors) => {
          const errorKey = Object.keys(errors || {})[0];
          const errorMsg = errors.general || errors[errorKey] || "Произошла ошибка при удалении.";
          toast.error(`${errorMsg} (Инструктор: ${titleToDelete || "ID: " + idToDelete})`);
        },
        onFinish: () => closeModal()
      });
    };
    const patchInstructorProfile = (instructorProfileId, payload) => {
      const index = localInstructorProfiles.value.findIndex((item) => item.id === instructorProfileId);
      if (index !== -1) {
        localInstructorProfiles.value[index] = {
          ...localInstructorProfiles.value[index],
          ...payload
        };
      }
    };
    const selectedInstructorProfiles = ref([]);
    const toggleAll = (payload) => {
      var _a;
      const checked = (payload == null ? void 0 : payload.checked) ?? ((_a = payload == null ? void 0 : payload.target) == null ? void 0 : _a.checked) ?? false;
      const ids = (payload == null ? void 0 : payload.ids) ?? displayedInstructorProfiles.value.map((item) => item.id);
      if (checked) {
        selectedInstructorProfiles.value = [.../* @__PURE__ */ new Set([...selectedInstructorProfiles.value, ...ids])];
      } else {
        selectedInstructorProfiles.value = selectedInstructorProfiles.value.filter((id) => !ids.includes(id));
      }
    };
    const toggleSelectInstructorProfile = (id) => {
      const index = selectedInstructorProfiles.value.indexOf(id);
      if (index > -1) {
        selectedInstructorProfiles.value.splice(index, 1);
      } else {
        selectedInstructorProfiles.value.push(id);
      }
    };
    const handleSortOrderUpdate = (orderedIds) => {
      const startSort = (currentPage.value - 1) * itemsPerPage.value;
      const items = orderedIds.map((id, index) => ({
        id,
        sort: startSort + index + 1
      }));
      if (!items.length) return;
      router.put(route("admin.actions.schoolInstructorProfiles.updateSortBulk"), { items }, {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => toast.success("Порядок инструкторов успешно обновлён."),
        onError: (errors) => {
          console.error("Ошибка обновления сортировки инструкторов:", errors);
          toast.error((errors == null ? void 0 : errors.general) || (errors == null ? void 0 : errors.instructorProfiles) || "Не удалось обновить порядок инструкторов.");
          router.reload({
            only: ["instructorProfiles"],
            preserveScroll: true
          });
        }
      });
    };
    const bulkToggleActivity = (newActivity) => {
      if (!selectedInstructorProfiles.value.length) {
        toast.warning("Выберите инструкторов для активации/деактивации.");
        return;
      }
      const idsToUpdate = [...selectedInstructorProfiles.value];
      router.put(route("admin.actions.schoolInstructorProfiles.bulkUpdateActivity"), {
        ids: idsToUpdate,
        activity: newActivity
      }, {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => {
          idsToUpdate.forEach((id) => patchInstructorProfile(id, { activity: newActivity }));
          selectedInstructorProfiles.value = [];
          toast.success("Активность выбранных инструкторов обновлена.");
        },
        onError: (errors) => {
          toast.error((errors == null ? void 0 : errors.ids) || (errors == null ? void 0 : errors.activity) || (errors == null ? void 0 : errors.general) || "Ошибка массового обновления активности.");
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
      }
      event.target.value = "";
    };
    const toggleActivity = (instructorProfile) => {
      const newActivity = !instructorProfile.activity;
      const instructorTitle = getInstructorTitle(instructorProfile);
      const actionText = newActivity ? t("activated") : t("deactivated");
      router.put(route("admin.actions.schoolInstructorProfiles.updateActivity", {
        instructorProfile: instructorProfile.id
      }), {
        activity: newActivity
      }, {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => {
          patchInstructorProfile(instructorProfile.id, { activity: newActivity });
          instructorProfile.activity = newActivity;
          toast.success(`Инструктор "${instructorTitle}" ${actionText}.`);
        },
        onError: (errors) => {
          toast.error((errors == null ? void 0 : errors.activity) || (errors == null ? void 0 : errors.general) || `Ошибка изменения активности для инструктора "${instructorTitle}".`);
        }
      });
    };
    return (_ctx, _push, _parent, _attrs) => {
      _push(ssrRenderComponent(AdminLayout, mergeProps({
        title: unref(t)("instructors")
      }, _attrs), {
        header: withCtx((_, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(ssrRenderComponent(TitlePage, null, {
              default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                if (_push3) {
                  _push3(`${ssrInterpolate(unref(t)("instructors"))}`);
                } else {
                  return [
                    createTextVNode(toDisplayString(unref(t)("instructors")), 1)
                  ];
                }
              }),
              _: 1
            }, _parent2, _scopeId));
          } else {
            return [
              createVNode(TitlePage, null, {
                default: withCtx(() => [
                  createTextVNode(toDisplayString(unref(t)("instructors")), 1)
                ]),
                _: 1
              })
            ];
          }
        }),
        default: withCtx((_, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(`<div class="px-2 py-2 w-full max-w-12xl mx-auto"${_scopeId}><div class="p-4 bg-slate-50 dark:bg-slate-700 border border-blue-400 dark:border-blue-200 overflow-hidden shadow-md shadow-gray-500 dark:shadow-slate-400 bg-opacity-95 dark:bg-opacity-95"${_scopeId}><div class="sm:flex sm:justify-between sm:items-center mb-3 gap-3"${_scopeId}>`);
            _push2(ssrRenderComponent(_sfc_main$8, {
              href: _ctx.route("admin.schoolInstructorProfiles.create")
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
                  _push3(` ${ssrInterpolate(unref(t)("addInstructor"))}`);
                } else {
                  return [
                    createTextVNode(" " + toDisplayString(unref(t)("addInstructor")), 1)
                  ];
                }
              }),
              _: 1
            }, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$9, {
              "setting-key": "adminSchoolInstructorsProcessingMode",
              mode: __props.adminSchoolInstructorsProcessingMode,
              "use-server-processing": __props.useServerProcessing,
              total: __props.instructorProfilesCount
            }, null, _parent2, _scopeId));
            _push2(`</div>`);
            if (__props.instructorProfilesCount && !__props.useServerProcessing) {
              _push2(ssrRenderComponent(_sfc_main$a, {
                modelValue: searchQuery.value,
                "onUpdate:modelValue": ($event) => searchQuery.value = $event,
                placeholder: unref(t)("searchByName")
              }, null, _parent2, _scopeId));
            } else {
              _push2(`<!---->`);
            }
            if (__props.instructorProfilesCount && __props.useServerProcessing) {
              _push2(ssrRenderComponent(_sfc_main$b, {
                modelValue: searchQuery.value,
                "onUpdate:modelValue": ($event) => searchQuery.value = $event
              }, null, _parent2, _scopeId));
            } else {
              _push2(`<!---->`);
            }
            if (__props.instructorProfilesCount) {
              _push2(`<div class="flex justify-between items-center flex-col md:flex-row my-3"${_scopeId}>`);
              if (!__props.useServerProcessing) {
                _push2(ssrRenderComponent(_sfc_main$c, {
                  "items-per-page": itemsPerPage.value,
                  "onUpdate:itemsPerPage": ($event) => itemsPerPage.value = $event
                }, null, _parent2, _scopeId));
              } else {
                _push2(ssrRenderComponent(_sfc_main$d, {
                  "items-per-page": itemsPerPage.value,
                  "update-route": "admin.settings.updateAdminCountSchoolInstructors"
                }, null, _parent2, _scopeId));
              }
              _push2(ssrRenderComponent(_sfc_main$4, {
                sortParam: sortParam.value,
                "onUpdate:sortParam": (val) => sortParam.value = val
              }, null, _parent2, _scopeId));
              _push2(`</div>`);
            } else {
              _push2(`<!---->`);
            }
            if (__props.instructorProfilesCount) {
              _push2(`<div class="flex flex-col lg:flex-row items-center justify-between gap-3"${_scopeId}>`);
              _push2(ssrRenderComponent(_sfc_main$e, null, {
                default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                  if (_push3) {
                    _push3(`${ssrInterpolate(__props.instructorProfilesCount)}`);
                  } else {
                    return [
                      createTextVNode(toDisplayString(__props.instructorProfilesCount), 1)
                    ];
                  }
                }),
                _: 1
              }, _parent2, _scopeId));
              _push2(ssrRenderComponent(_sfc_main$3, { onChange: handleBulkAction }, null, _parent2, _scopeId));
              _push2(ssrRenderComponent(_sfc_main$f, {
                viewMode: viewMode.value,
                "onUpdate:viewMode": ($event) => viewMode.value = $event
              }, null, _parent2, _scopeId));
              _push2(`</div>`);
            } else {
              _push2(`<!---->`);
            }
            if (__props.instructorProfilesCount) {
              _push2(`<div class="flex justify-center items-center flex-col md:flex-row mt-3"${_scopeId}>`);
              if (!__props.useServerProcessing) {
                _push2(ssrRenderComponent(_sfc_main$g, {
                  "current-page": currentPage.value,
                  "items-per-page": itemsPerPage.value,
                  "total-items": filteredInstructorProfiles.value.length,
                  "onUpdate:currentPage": ($event) => currentPage.value = $event
                }, null, _parent2, _scopeId));
              } else {
                _push2(ssrRenderComponent(_sfc_main$h, { pagination: __props.instructorProfiles }, null, _parent2, _scopeId));
              }
              _push2(`</div>`);
            } else {
              _push2(`<!---->`);
            }
            if (viewMode.value === "table") {
              _push2(ssrRenderComponent(_sfc_main$2, {
                "instructor-profiles": displayedInstructorProfiles.value,
                "selected-instructor-profiles": selectedInstructorProfiles.value,
                onToggleActivity: toggleActivity,
                onDelete: confirmDelete,
                onUpdateSortOrder: handleSortOrderUpdate,
                onToggleSelect: toggleSelectInstructorProfile,
                onToggleAll: toggleAll
              }, null, _parent2, _scopeId));
            } else {
              _push2(ssrRenderComponent(_sfc_main$1, {
                "instructor-profiles": displayedInstructorProfiles.value,
                "selected-instructor-profiles": selectedInstructorProfiles.value,
                onToggleActivity: toggleActivity,
                onDelete: confirmDelete,
                onToggleSelect: toggleSelectInstructorProfile,
                onToggleAll: toggleAll,
                onUpdateSortOrder: handleSortOrderUpdate
              }, null, _parent2, _scopeId));
            }
            if (__props.instructorProfilesCount) {
              _push2(`<div class="flex justify-center items-center flex-col md:flex-row mt-3"${_scopeId}>`);
              if (!__props.useServerProcessing) {
                _push2(ssrRenderComponent(_sfc_main$g, {
                  "current-page": currentPage.value,
                  "items-per-page": itemsPerPage.value,
                  "total-items": filteredInstructorProfiles.value.length,
                  "onUpdate:currentPage": ($event) => currentPage.value = $event
                }, null, _parent2, _scopeId));
              } else {
                _push2(ssrRenderComponent(_sfc_main$h, { pagination: __props.instructorProfiles }, null, _parent2, _scopeId));
              }
              _push2(`</div>`);
            } else {
              _push2(`<!---->`);
            }
            _push2(`</div></div>`);
            _push2(ssrRenderComponent(_sfc_main$i, {
              show: showConfirmDeleteModal.value,
              onCancel: closeModal,
              onConfirm: deleteInstructorProfile,
              cancelText: unref(t)("cancel"),
              confirmText: unref(t)("yesDelete"),
              onClose: closeModal
            }, null, _parent2, _scopeId));
          } else {
            return [
              createVNode("div", { class: "px-2 py-2 w-full max-w-12xl mx-auto" }, [
                createVNode("div", { class: "p-4 bg-slate-50 dark:bg-slate-700 border border-blue-400 dark:border-blue-200 overflow-hidden shadow-md shadow-gray-500 dark:shadow-slate-400 bg-opacity-95 dark:bg-opacity-95" }, [
                  createVNode("div", { class: "sm:flex sm:justify-between sm:items-center mb-3 gap-3" }, [
                    createVNode(_sfc_main$8, {
                      href: _ctx.route("admin.schoolInstructorProfiles.create")
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
                        createTextVNode(" " + toDisplayString(unref(t)("addInstructor")), 1)
                      ]),
                      _: 1
                    }, 8, ["href"]),
                    createVNode(_sfc_main$9, {
                      "setting-key": "adminSchoolInstructorsProcessingMode",
                      mode: __props.adminSchoolInstructorsProcessingMode,
                      "use-server-processing": __props.useServerProcessing,
                      total: __props.instructorProfilesCount
                    }, null, 8, ["mode", "use-server-processing", "total"])
                  ]),
                  __props.instructorProfilesCount && !__props.useServerProcessing ? (openBlock(), createBlock(_sfc_main$a, {
                    key: 0,
                    modelValue: searchQuery.value,
                    "onUpdate:modelValue": ($event) => searchQuery.value = $event,
                    placeholder: unref(t)("searchByName")
                  }, null, 8, ["modelValue", "onUpdate:modelValue", "placeholder"])) : createCommentVNode("", true),
                  __props.instructorProfilesCount && __props.useServerProcessing ? (openBlock(), createBlock(_sfc_main$b, {
                    key: 1,
                    modelValue: searchQuery.value,
                    "onUpdate:modelValue": ($event) => searchQuery.value = $event
                  }, null, 8, ["modelValue", "onUpdate:modelValue"])) : createCommentVNode("", true),
                  __props.instructorProfilesCount ? (openBlock(), createBlock("div", {
                    key: 2,
                    class: "flex justify-between items-center flex-col md:flex-row my-3"
                  }, [
                    !__props.useServerProcessing ? (openBlock(), createBlock(_sfc_main$c, {
                      key: 0,
                      "items-per-page": itemsPerPage.value,
                      "onUpdate:itemsPerPage": ($event) => itemsPerPage.value = $event
                    }, null, 8, ["items-per-page", "onUpdate:itemsPerPage"])) : (openBlock(), createBlock(_sfc_main$d, {
                      key: 1,
                      "items-per-page": itemsPerPage.value,
                      "update-route": "admin.settings.updateAdminCountSchoolInstructors"
                    }, null, 8, ["items-per-page"])),
                    createVNode(_sfc_main$4, {
                      sortParam: sortParam.value,
                      "onUpdate:sortParam": (val) => sortParam.value = val
                    }, null, 8, ["sortParam", "onUpdate:sortParam"])
                  ])) : createCommentVNode("", true),
                  __props.instructorProfilesCount ? (openBlock(), createBlock("div", {
                    key: 3,
                    class: "flex flex-col lg:flex-row items-center justify-between gap-3"
                  }, [
                    createVNode(_sfc_main$e, null, {
                      default: withCtx(() => [
                        createTextVNode(toDisplayString(__props.instructorProfilesCount), 1)
                      ]),
                      _: 1
                    }),
                    createVNode(_sfc_main$3, { onChange: handleBulkAction }),
                    createVNode(_sfc_main$f, {
                      viewMode: viewMode.value,
                      "onUpdate:viewMode": ($event) => viewMode.value = $event
                    }, null, 8, ["viewMode", "onUpdate:viewMode"])
                  ])) : createCommentVNode("", true),
                  __props.instructorProfilesCount ? (openBlock(), createBlock("div", {
                    key: 4,
                    class: "flex justify-center items-center flex-col md:flex-row mt-3"
                  }, [
                    !__props.useServerProcessing ? (openBlock(), createBlock(_sfc_main$g, {
                      key: 0,
                      "current-page": currentPage.value,
                      "items-per-page": itemsPerPage.value,
                      "total-items": filteredInstructorProfiles.value.length,
                      "onUpdate:currentPage": ($event) => currentPage.value = $event
                    }, null, 8, ["current-page", "items-per-page", "total-items", "onUpdate:currentPage"])) : (openBlock(), createBlock(_sfc_main$h, {
                      key: 1,
                      pagination: __props.instructorProfiles
                    }, null, 8, ["pagination"]))
                  ])) : createCommentVNode("", true),
                  viewMode.value === "table" ? (openBlock(), createBlock(_sfc_main$2, {
                    key: 5,
                    "instructor-profiles": displayedInstructorProfiles.value,
                    "selected-instructor-profiles": selectedInstructorProfiles.value,
                    onToggleActivity: toggleActivity,
                    onDelete: confirmDelete,
                    onUpdateSortOrder: handleSortOrderUpdate,
                    onToggleSelect: toggleSelectInstructorProfile,
                    onToggleAll: toggleAll
                  }, null, 8, ["instructor-profiles", "selected-instructor-profiles"])) : (openBlock(), createBlock(_sfc_main$1, {
                    key: 6,
                    "instructor-profiles": displayedInstructorProfiles.value,
                    "selected-instructor-profiles": selectedInstructorProfiles.value,
                    onToggleActivity: toggleActivity,
                    onDelete: confirmDelete,
                    onToggleSelect: toggleSelectInstructorProfile,
                    onToggleAll: toggleAll,
                    onUpdateSortOrder: handleSortOrderUpdate
                  }, null, 8, ["instructor-profiles", "selected-instructor-profiles"])),
                  __props.instructorProfilesCount ? (openBlock(), createBlock("div", {
                    key: 7,
                    class: "flex justify-center items-center flex-col md:flex-row mt-3"
                  }, [
                    !__props.useServerProcessing ? (openBlock(), createBlock(_sfc_main$g, {
                      key: 0,
                      "current-page": currentPage.value,
                      "items-per-page": itemsPerPage.value,
                      "total-items": filteredInstructorProfiles.value.length,
                      "onUpdate:currentPage": ($event) => currentPage.value = $event
                    }, null, 8, ["current-page", "items-per-page", "total-items", "onUpdate:currentPage"])) : (openBlock(), createBlock(_sfc_main$h, {
                      key: 1,
                      pagination: __props.instructorProfiles
                    }, null, 8, ["pagination"]))
                  ])) : createCommentVNode("", true)
                ])
              ]),
              createVNode(_sfc_main$i, {
                show: showConfirmDeleteModal.value,
                onCancel: closeModal,
                onConfirm: deleteInstructorProfile,
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
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Pages/Admin/School/SchoolInstructorProfiles/Index.vue");
  return _sfc_setup ? _sfc_setup(props, ctx) : void 0;
};
export {
  _sfc_main as default
};
