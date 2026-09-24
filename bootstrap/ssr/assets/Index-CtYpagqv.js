import { mergeProps, unref, useSSRContext, ref, watch, withCtx, createVNode, createBlock, openBlock, toDisplayString, computed, createTextVNode, createCommentVNode } from "vue";
import { ssrRenderAttrs, ssrInterpolate, ssrRenderAttr, ssrRenderComponent, ssrIncludeBooleanAttr } from "vue/server-renderer";
import { router } from "@inertiajs/vue3";
import { useI18n } from "vue-i18n";
import { useToast } from "vue-toastification";
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
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Components/Admin/School/SchoolModule/Select/BulkActionSelect.vue");
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
      _push(`<div${ssrRenderAttrs(mergeProps({ class: "flex justify-center items-center h-fit my-2" }, _attrs))}><label for="sortParam" class="hidden lg:block sm:mr-2 tracking-wider text-sm font-semibold text-slate-600 dark:text-slate-100">${ssrInterpolate(unref(t)("sort"))}</label><select id="sortParam"${ssrRenderAttr("value", __props.sortParam)} class="w-50 px-3 py-0.5 form-select bg-white dark:bg-gray-200 text-gray-600 dark:text-gray-900 border border-slate-400 dark:border-slate-600 rounded-sm shadow-sm"><option value="idDesc">${ssrInterpolate(unref(t)("idDesc"))}</option><option value="idAsc">${ssrInterpolate(unref(t)("idAsc"))}</option><option disabled>─────────────</option><option value="sortAsc">${ssrInterpolate(unref(t)("sortNumber"))} ↑</option><option value="sortDesc">${ssrInterpolate(unref(t)("sortNumber"))} ↓</option><option disabled>─────────────</option><option value="titleAsc">${ssrInterpolate(unref(t)("title"))} ↑</option><option value="titleDesc">${ssrInterpolate(unref(t)("title"))} ↓</option><option disabled>─────────────</option><option value="slugAsc">Slug A→Z</option><option value="slugDesc">Slug Z→A</option><option disabled>─────────────</option><option value="activityDesc">${ssrInterpolate(unref(t)("activity"))} ON→OFF</option><option value="activityAsc">${ssrInterpolate(unref(t)("activity"))} OFF→ON</option><option value="activity">${ssrInterpolate(unref(t)("active"))}</option><option value="inactive">${ssrInterpolate(unref(t)("inactive"))}</option><option disabled>─────────────</option><option value="lessonsDesc">${ssrInterpolate(unref(t)("lessonsCount"))} 9→0</option><option value="lessonsAsc">${ssrInterpolate(unref(t)("lessonsCount"))} 0→9</option><option value="imagesDesc">${ssrInterpolate(unref(t)("images"))} 9→0</option><option value="imagesAsc">${ssrInterpolate(unref(t)("images"))} 0→9</option><option disabled>─────────────</option><option value="viewsDesc">${ssrInterpolate(unref(t)("views"))} 9→0</option><option value="viewsAsc">${ssrInterpolate(unref(t)("views"))} 0→9</option><option value="likesDesc">${ssrInterpolate(unref(t)("likes"))} 9→0</option><option value="likesAsc">${ssrInterpolate(unref(t)("likes"))} 0→9</option><option value="likesCountDesc">${ssrInterpolate(unref(t)("ratingCount"))} 9→0</option><option value="likesCountAsc">${ssrInterpolate(unref(t)("ratingCount"))} 0→9</option><option disabled>─────────────</option><option value="popularityDesc">${ssrInterpolate(unref(t)("popularity"))} 9→0</option><option value="popularityAsc">${ssrInterpolate(unref(t)("popularity"))} 0→9</option><option value="ratingCountDesc">${ssrInterpolate(unref(t)("ratingCount"))} 9→0</option><option value="ratingCountAsc">${ssrInterpolate(unref(t)("ratingCount"))} 0→9</option><option value="ratingAvgDesc">${ssrInterpolate(unref(t)("ratingAvg"))} 9→0</option><option value="ratingAvgAsc">${ssrInterpolate(unref(t)("ratingAvg"))} 0→9</option><option disabled>─────────────</option><option value="statusAsc">${ssrInterpolate(unref(t)("status"))} A→Z</option><option value="statusDesc">${ssrInterpolate(unref(t)("status"))} Z→A</option><option value="availabilityAsc">${ssrInterpolate(unref(t)("availability"))} A→Z</option><option value="availabilityDesc">${ssrInterpolate(unref(t)("availability"))} Z→A</option><option disabled>─────────────</option><option value="difficultyDesc">${ssrInterpolate(unref(t)("sortDifficulty"))} 9→0</option><option value="difficultyAsc">${ssrInterpolate(unref(t)("sortDifficulty"))} 0→9</option><option value="durationDesc">${ssrInterpolate(unref(t)("duration"))} 9→0</option><option value="durationAsc">${ssrInterpolate(unref(t)("duration"))} 0→9</option><option disabled>─────────────</option><option value="publishedAtDesc">${ssrInterpolate(unref(t)("publishedAt"))} ↓</option><option value="publishedAtAsc">${ssrInterpolate(unref(t)("publishedAt"))} ↑</option><option value="createdAtDesc">${ssrInterpolate(unref(t)("createdAt"))} ↓</option><option value="createdAtAsc">${ssrInterpolate(unref(t)("createdAt"))} ↑</option><option value="updatedAtDesc">${ssrInterpolate(unref(t)("updatedAt"))} ↓</option><option value="updatedAtAsc">${ssrInterpolate(unref(t)("updatedAt"))} ↑</option><option disabled>─────────────</option><option value="courseDesc">${ssrInterpolate(unref(t)("course"))} ID 9→0</option><option value="courseAsc">${ssrInterpolate(unref(t)("course"))} ID 0→9</option><option disabled>─────────────</option></select></div>`);
    };
  }
};
const _sfc_setup$3 = _sfc_main$3.setup;
_sfc_main$3.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Components/Admin/School/SchoolModule/Sort/SortSelect.vue");
  return _sfc_setup$3 ? _sfc_setup$3(props, ctx) : void 0;
};
const _sfc_main$2 = {
  __name: "ModuleTable",
  __ssrInlineRender: true,
  props: {
    modules: {
      type: Array,
      default: () => []
    },
    selectedModules: {
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
    const emit = __emit;
    const localModules = ref([]);
    watch(
      () => props.modules,
      (newVal) => {
        localModules.value = JSON.parse(JSON.stringify(newVal || []));
      },
      { immediate: true, deep: true }
    );
    const moduleStatusLabelKeyMap = {
      draft: "statusDraft",
      published: "statusPublished",
      archived: "statusArchived"
    };
    const moduleAvailabilityLabelKeyMap = {
      unlisted: "availabilityUnlisted",
      public: "availabilityPublic",
      private: "availabilityPrivate"
    };
    const getModuleStatusLabel = (status) => {
      return t(moduleStatusLabelKeyMap[status] || status || "no");
    };
    const getModuleAvailabilityLabel = (availability) => {
      return t(moduleAvailabilityLabelKeyMap[availability] || availability || "no");
    };
    const handleDragEnd = () => {
      const newOrderIds = localModules.value.map((module) => module.id);
      emit("update-sort-order", newOrderIds);
    };
    const getPrimaryImage = (module) => {
      if (module == null ? void 0 : module.primary_image) {
        return module.primary_image;
      }
      if (Array.isArray(module == null ? void 0 : module.images) && module.images.length) {
        return [...module.images].sort((a, b) => Number((a == null ? void 0 : a.order) ?? 0) - Number((b == null ? void 0 : b.order) ?? 0))[0];
      }
      return null;
    };
    const getModuleTitle = (module) => {
      var _a;
      return ((_a = module == null ? void 0 : module.translation) == null ? void 0 : _a.title) || `ID: ${module == null ? void 0 : module.id}`;
    };
    const getModuleSubtitle = (module) => {
      var _a;
      return ((_a = module == null ? void 0 : module.translation) == null ? void 0 : _a.subtitle) || "";
    };
    const getModuleShort = (module) => {
      var _a;
      return ((_a = module == null ? void 0 : module.translation) == null ? void 0 : _a.short) || "";
    };
    const getCourseTitle = (module) => {
      var _a, _b;
      return ((_b = (_a = module == null ? void 0 : module.course) == null ? void 0 : _a.translation) == null ? void 0 : _b.title) || `ID: ${(module == null ? void 0 : module.school_course_id) || "-"}`;
    };
    const getImageUrl = (image) => {
      return (image == null ? void 0 : image.thumb_url) || (image == null ? void 0 : image.webp_url) || (image == null ? void 0 : image.image_url) || (image == null ? void 0 : image.url) || "";
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
      _push(`<div${ssrRenderAttrs(mergeProps({ class: "bg-white dark:bg-slate-700 shadow-lg rounded-sm border border-slate-200 dark:border-slate-600 relative" }, _attrs))}><div class="flex items-center justify-between px-3 py-2 border-b border-slate-400 dark:border-slate-500"><div class="text-xs text-slate-600 dark:text-slate-200">${ssrInterpolate(unref(t)("selected"))}: ${ssrInterpolate(__props.selectedModules.length)}</div>`);
      if (localModules.value.length) {
        _push(`<label class="flex items-center text-xs text-slate-600 dark:text-slate-200 cursor-pointer"><span>${ssrInterpolate(unref(t)("selectAll"))}</span><input type="checkbox" class="mx-2"></label>`);
      } else {
        _push(`<!---->`);
      }
      _push(`</div><div class="overflow-x-auto">`);
      if (localModules.value.length > 0) {
        _push(`<table class="table-auto w-full text-slate-700 dark:text-slate-100"><thead class="text-sm uppercase bg-slate-200 dark:bg-cyan-900 border border-solid border-gray-300 dark:border-gray-700"><tr><th class="px-2 py-3 w-px"><svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 fill-current text-slate-800 dark:text-slate-200" height="24" width="24" viewBox="0 0 24 24"><path d="M12.707,2.293a1,1,0,0,0-1.414,0l-5,5A1,1,0,0,0,7.707,8.707L12,4.414l4.293,4.293a1,1,0,0,0,1.414-1.414Z"></path><path d="M16.293,15.293,12,19.586,7.707,15.293a1,1,0,0,0-1.414,1.414l5,5a1,1,0,0,0,1.414,0l5-5a1,1,0,0,0-1.414-1.414Z"></path></svg></th><th class="px-2 py-3 w-px"><div class="font-medium text-center">${ssrInterpolate(unref(t)("id"))}</div></th><th class="px-2 py-3 whitespace-nowrap"><div class="flex justify-center"${ssrRenderAttr("title", unref(t)("image"))}><svg class="w-6 h-6 fill-current shrink-0" viewBox="0 0 512 512"><path d="M0 96C0 60.7 28.7 32 64 32l384 0c35.3 0 64 28.7 64 64l0 320c0 35.3-28.7 64-64 64L64 480c-35.3 0-64-28.7-64-64L0 96zM323.8 202.5c-4.5-6.6-11.9-10.5-19.8-10.5s-15.4 3.9-19.8 10.5l-87 127.6L170.7 297c-4.6-5.7-11.5-9-18.7-9s-14.2 3.3-18.7 9l-64 80c-5.8 7.2-6.9 17.1-2.9 25.4s12.4 13.6 21.6 13.6l96 0 32 0 208 0c8.9 0 17.1-4.9 21.2-12.8s3.6-17.4-1.4-24.7l-120-176zM112 192a48 48 0 1 0 0-96 48 48 0 1 0 0 96z"></path></svg></div></th><th class="px-2 py-3 whitespace-nowrap"><div class="font-semibold text-left">${ssrInterpolate(unref(t)("module"))}</div></th><th class="px-2 py-3 whitespace-nowrap"><div class="font-semibold text-center">${ssrInterpolate(unref(t)("status"))}</div></th><th class="px-2 py-3 whitespace-nowrap"><div class="flex justify-center"${ssrRenderAttr("title", unref(t)("duration"))}><svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" height="24" width="24" viewBox="0 0 24 24"><path class="fill-current text-violet-700 dark:text-violet-300" d="M22,13a1,1,0,0,1,0-2h1.949A12.006,12.006,0,0,0,13,.051V2a1,1,0,0,1-2,0V.051A12.006,12.006,0,0,0,.051,11H2a1,1,0,0,1,0,2H.051A12.006,12.006,0,0,0,11,23.949V22a1,1,0,0,1,2,0v1.949A12.006,12.006,0,0,0,23.949,13Zm-6,0H12a1,1,0,0,1-.832-.445l-4-6a1,1,0,1,1,1.664-1.11L12.535,11H16a1,1,0,0,1,0,2Z"></path></svg></div></th><th class="px-2 py-3 whitespace-nowrap"><div class="flex justify-center"${ssrRenderAttr("title", unref(t)("lessonsCount"))}><svg class="shrink-0 h-5 w-5" viewBox="0 0 24 24"><path class="fill-current text-cyan-600 dark:text-cyan-30" stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25"></path></svg></div></th><th class="px-2 py-3 whitespace-nowrap"><div class="flex justify-center"${ssrRenderAttr("title", unref(t)("views"))}><svg class="w-4 h-4 fill-current shrink-0" viewBox="0 0 16 16"><path class="fill-current text-blue-600 dark:text-blue-300" d="M8 2C3.246 2 .251 7.29.127 7.515a.998.998 0 0 0 .002.975c.07.125 1.044 1.801 2.695 3.274C4.738 13.582 6.283 14 8 14c4.706 0 7.743-5.284 7.872-5.507a1 1 0 0 0 0-.98A13.292 13.292 0 0 0 8 2zm0 10a4 4 0 1 1 0-8 4 4 0 0 1 0 8zm0-6a2 2 0 1 0 0 4 2 2 0 0 0 0-4z"></path></svg></div></th><th class="px-2 py-3 whitespace-nowrap"><div class="flex justify-center"${ssrRenderAttr("title", unref(t)("rating"))}><svg xmlns="http://www.w3.org/2000/svg" height="24" width="24" viewBox="0 0 24 24" class="shrink-0 h-4 w-4"><path class="fill-current text-red-400 dark:text-red-300" d="M12.746,1.464l3.11,6.3L22.81,8.776a.831.831,0,0,1,.461,1.418l-5.033,4.9,1.188,6.926a.832.832,0,0,1-1.207.877L12,19.632,5.78,22.9a.833.833,0,0,1-1.207-.878L5.761,15.1l-5.033-4.9a.831.831,0,0,1,.461-1.418L8.143,7.765l3.11-6.3A.833.833,0,0,1,12.746,1.464Z"></path></svg></div></th><th class="px-2 py-3 whitespace-nowrap"><div class="flex justify-center"${ssrRenderAttr("title", unref(t)("likes"))}><svg xmlns="http://www.w3.org/2000/svg" height="24" width="24" viewBox="0 0 24 24" class="shrink-0 h-4 w-4"><path class="fill-current text-red-400 dark:text-red-300" d="M3,9H1a1,1,0,0,0-1,1V22a1,1,0,0,0,1,1H4V10A1,1,0,0,0,3,9Z"></path><path class="fill-current text-red-400 dark:text-red-300" d="M21.882,8.133A2.986,2.986,0,0,0,21,8H15V5c0-3.824-2.589-4.942-3.958-5a1.017,1.017,0,0,0-.734.277A1,1,0,0,0,10,1V5.638l-4,4.8V23H18.23A2.985,2.985,0,0,0,21.1,20.882l2.769-9A3,3,0,0,0,21.882,8.133Z"></path></svg></div></th><th class="px-2 py-3 whitespace-nowrap"><div class="font-semibold text-end">${ssrInterpolate(unref(t)("actions"))}</div></th><th class="px-2 py-3 whitespace-nowrap"><div class="text-center"><input type="checkbox"></div></th></tr></thead>`);
        _push(ssrRenderComponent(unref(draggable), {
          modelValue: localModules.value,
          "onUpdate:modelValue": ($event) => localModules.value = $event,
          tag: "tbody",
          "item-key": "id",
          handle: ".handle",
          onEnd: handleDragEnd
        }, {
          item: withCtx(({ element: module }, _push2, _parent2, _scopeId) => {
            var _a, _b, _c, _d;
            if (_push2) {
              _push2(`<tr class="text-sm font-semibold border-b-2 hover:bg-slate-100 dark:hover:bg-cyan-800"${_scopeId}><td class="px-2 py-1 text-center cursor-move handle"${_scopeId}><svg class="w-4 h-4 text-gray-500 dark:text-gray-300" fill="currentColor" viewBox="0 0 20 20"${_scopeId}><path d="M7 4h2v2H7V4zm4 0h2v2h-2V4zM7 8h2v2H7V8zm4 0h2v2h-2V8zM7 12h2v2H7v-2zm4 0h2v2h-2v-2z"${_scopeId}></path></svg></td><td class="px-2 py-3 whitespace-nowrap"${_scopeId}><div class="text-center text-xs text-slate-800 dark:text-blue-200"${ssrRenderAttr("title", `[sort: ${module.sort}] ${formatDate(module.published_at)}`)}${_scopeId}>${ssrInterpolate(module.id)}</div></td><td class="px-2 py-3 whitespace-nowrap"${_scopeId}><div class="flex justify-center"${ssrRenderAttr("title", getModuleTitle(module))}${_scopeId}>`);
              if (module.images && module.images.length) {
                _push2(`<img${ssrRenderAttr("src", getImageUrl(getPrimaryImage(module)))}${ssrRenderAttr("alt", ((_a = getPrimaryImage(module)) == null ? void 0 : _a.alt) || unref(t)("defaultImageAlt"))}${ssrRenderAttr("title", ((_b = getPrimaryImage(module)) == null ? void 0 : _b.caption) || unref(t)("currentImage"))} class="h-8 w-12 object-cover rounded-sm"${_scopeId}>`);
              } else {
                _push2(`<img src="/storage/school/school_module_images/default-image.png"${ssrRenderAttr("alt", unref(t)("defaultImageTitle"))} class="h-8 w-12 object-cover rounded-sm"${_scopeId}>`);
              }
              _push2(`</div></td><td class="px-2 py-3 whitespace-nowrap"${_scopeId}><div class="min-w-0"${_scopeId}><a${ssrRenderAttr("href", `/school/modules/${encodeURIComponent(module.slug)}`)} class="text-xs text-sky-600 dark:text-sky-200 hover:underline hover:text-sky-600 dark:hover:text-sky-200" target="_blank" rel="noopener noreferrer"${ssrRenderAttr("title", getModuleSubtitle(module) || getModuleShort(module))}${_scopeId}>${ssrInterpolate(getModuleTitle(module))}</a><div class="text-xs text-slate-700 dark:text-slate-200"${ssrRenderAttr("title", `ID: ${module.school_course_id}`)}${_scopeId}>${ssrInterpolate(unref(t)("course"))}: ${ssrInterpolate(getCourseTitle(module))}</div></div></td><td class="px-2 py-3 whitespace-nowrap"${_scopeId}><div class="text-center text-xs text-fuchsia-800 dark:text-fuchsia-400"${ssrRenderAttr("title", `${unref(t)("status")}: ${getModuleStatusLabel(module.status)}`)}${_scopeId}>${ssrInterpolate(getModuleStatusLabel(module.status))}</div><div class="text-center text-xs text-teal-600 dark:text-teal-300"${ssrRenderAttr("title", `${unref(t)("availability")}: ${getModuleAvailabilityLabel(module.availability)}`)}${_scopeId}>${ssrInterpolate(getModuleAvailabilityLabel(module.availability))}</div></td><td class="px-2 py-3 whitespace-nowrap"${_scopeId}><div class="text-center text-xs text-violet-700 dark:text-violet-300"${ssrRenderAttr("title", `${unref(t)("duration")}: ${module.duration ?? 0}`)}${_scopeId}>${ssrInterpolate(module.duration ?? 0)}</div></td><td class="px-2 py-3 whitespace-nowrap"${_scopeId}><div class="text-center text-xs text-sky-700 dark:text-sky-300"${ssrRenderAttr("title", `${unref(t)("lessons")}: ${module.lessons_count ?? 0}`)}${_scopeId}>${ssrInterpolate(module.lessons_count ?? 0)}</div></td><td class="px-2 py-3 whitespace-nowrap"${_scopeId}><div class="text-center text-xs text-blue-700 dark:text-blue-300"${ssrRenderAttr("title", `${unref(t)("views")}: ${module.views ?? 0}`)}${_scopeId}>${ssrInterpolate(module.views ?? 0)}</div></td><td class="px-2 py-3 whitespace-nowrap"${_scopeId}><div class="flex items-center justify-center gap-1"${_scopeId}><div class="text-center text-xs text-rose-500 dark:text-rose-300"${ssrRenderAttr("title", `${unref(t)("ratingCount")}: ${module.rating_count ?? 0}`)}${_scopeId}>${ssrInterpolate(module.rating_avg ?? 0)}</div><div class="text-center text-[10px] text-slate-500 dark:text-slate-300"${_scopeId}>${ssrInterpolate(module.rating_count ?? 0)}</div></div></td><td class="px-2 py-3 whitespace-nowrap"${_scopeId}><div class="text-center text-xs"${ssrRenderAttr("title", `${unref(t)("likes")}: ${module.likes ?? 0}`)}${_scopeId}>${ssrInterpolate(module.likes ?? 0)}</div></td><td class="px-2 py-3 whitespace-nowrap"${_scopeId}><div class="flex flex-row items-center justify-end gap-1"${_scopeId}>`);
              _push2(ssrRenderComponent(_sfc_main$5, {
                isActive: module.activity,
                title: module.activity ? unref(t)("enabled") : unref(t)("disabled"),
                onToggleActivity: ($event) => emit("toggle-activity", module)
              }, null, _parent2, _scopeId));
              _push2(ssrRenderComponent(_sfc_main$6, {
                href: _ctx.route("admin.schoolModules.edit", { schoolModule: module.id })
              }, null, _parent2, _scopeId));
              _push2(ssrRenderComponent(_sfc_main$7, {
                onDelete: ($event) => emit("delete", module)
              }, null, _parent2, _scopeId));
              _push2(`</div></td><td class="px-2 py-3 whitespace-nowrap"${_scopeId}><div class="text-center"${_scopeId}><input type="checkbox"${ssrIncludeBooleanAttr(__props.selectedModules.includes(module.id)) ? " checked" : ""}${_scopeId}></div></td></tr>`);
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
                      title: `[sort: ${module.sort}] ${formatDate(module.published_at)}`
                    }, toDisplayString(module.id), 9, ["title"])
                  ]),
                  createVNode("td", { class: "px-2 py-3 whitespace-nowrap" }, [
                    createVNode("div", {
                      class: "flex justify-center",
                      title: getModuleTitle(module)
                    }, [
                      module.images && module.images.length ? (openBlock(), createBlock("img", {
                        key: 0,
                        src: getImageUrl(getPrimaryImage(module)),
                        alt: ((_c = getPrimaryImage(module)) == null ? void 0 : _c.alt) || unref(t)("defaultImageAlt"),
                        title: ((_d = getPrimaryImage(module)) == null ? void 0 : _d.caption) || unref(t)("currentImage"),
                        class: "h-8 w-12 object-cover rounded-sm"
                      }, null, 8, ["src", "alt", "title"])) : (openBlock(), createBlock("img", {
                        key: 1,
                        src: "/storage/school/school_module_images/default-image.png",
                        alt: unref(t)("defaultImageTitle"),
                        class: "h-8 w-12 object-cover rounded-sm"
                      }, null, 8, ["alt"]))
                    ], 8, ["title"])
                  ]),
                  createVNode("td", { class: "px-2 py-3 whitespace-nowrap" }, [
                    createVNode("div", { class: "min-w-0" }, [
                      createVNode("a", {
                        href: `/school/modules/${encodeURIComponent(module.slug)}`,
                        class: "text-xs text-sky-600 dark:text-sky-200 hover:underline hover:text-sky-600 dark:hover:text-sky-200",
                        target: "_blank",
                        rel: "noopener noreferrer",
                        title: getModuleSubtitle(module) || getModuleShort(module)
                      }, toDisplayString(getModuleTitle(module)), 9, ["href", "title"]),
                      createVNode("div", {
                        class: "text-xs text-slate-700 dark:text-slate-200",
                        title: `ID: ${module.school_course_id}`
                      }, toDisplayString(unref(t)("course")) + ": " + toDisplayString(getCourseTitle(module)), 9, ["title"])
                    ])
                  ]),
                  createVNode("td", { class: "px-2 py-3 whitespace-nowrap" }, [
                    createVNode("div", {
                      class: "text-center text-xs text-fuchsia-800 dark:text-fuchsia-400",
                      title: `${unref(t)("status")}: ${getModuleStatusLabel(module.status)}`
                    }, toDisplayString(getModuleStatusLabel(module.status)), 9, ["title"]),
                    createVNode("div", {
                      class: "text-center text-xs text-teal-600 dark:text-teal-300",
                      title: `${unref(t)("availability")}: ${getModuleAvailabilityLabel(module.availability)}`
                    }, toDisplayString(getModuleAvailabilityLabel(module.availability)), 9, ["title"])
                  ]),
                  createVNode("td", { class: "px-2 py-3 whitespace-nowrap" }, [
                    createVNode("div", {
                      class: "text-center text-xs text-violet-700 dark:text-violet-300",
                      title: `${unref(t)("duration")}: ${module.duration ?? 0}`
                    }, toDisplayString(module.duration ?? 0), 9, ["title"])
                  ]),
                  createVNode("td", { class: "px-2 py-3 whitespace-nowrap" }, [
                    createVNode("div", {
                      class: "text-center text-xs text-sky-700 dark:text-sky-300",
                      title: `${unref(t)("lessons")}: ${module.lessons_count ?? 0}`
                    }, toDisplayString(module.lessons_count ?? 0), 9, ["title"])
                  ]),
                  createVNode("td", { class: "px-2 py-3 whitespace-nowrap" }, [
                    createVNode("div", {
                      class: "text-center text-xs text-blue-700 dark:text-blue-300",
                      title: `${unref(t)("views")}: ${module.views ?? 0}`
                    }, toDisplayString(module.views ?? 0), 9, ["title"])
                  ]),
                  createVNode("td", { class: "px-2 py-3 whitespace-nowrap" }, [
                    createVNode("div", { class: "flex items-center justify-center gap-1" }, [
                      createVNode("div", {
                        class: "text-center text-xs text-rose-500 dark:text-rose-300",
                        title: `${unref(t)("ratingCount")}: ${module.rating_count ?? 0}`
                      }, toDisplayString(module.rating_avg ?? 0), 9, ["title"]),
                      createVNode("div", { class: "text-center text-[10px] text-slate-500 dark:text-slate-300" }, toDisplayString(module.rating_count ?? 0), 1)
                    ])
                  ]),
                  createVNode("td", { class: "px-2 py-3 whitespace-nowrap" }, [
                    createVNode("div", {
                      class: "text-center text-xs",
                      title: `${unref(t)("likes")}: ${module.likes ?? 0}`
                    }, toDisplayString(module.likes ?? 0), 9, ["title"])
                  ]),
                  createVNode("td", { class: "px-2 py-3 whitespace-nowrap" }, [
                    createVNode("div", { class: "flex flex-row items-center justify-end gap-1" }, [
                      createVNode(_sfc_main$5, {
                        isActive: module.activity,
                        title: module.activity ? unref(t)("enabled") : unref(t)("disabled"),
                        onToggleActivity: ($event) => emit("toggle-activity", module)
                      }, null, 8, ["isActive", "title", "onToggleActivity"]),
                      createVNode(_sfc_main$6, {
                        href: _ctx.route("admin.schoolModules.edit", { schoolModule: module.id })
                      }, null, 8, ["href"]),
                      createVNode(_sfc_main$7, {
                        onDelete: ($event) => emit("delete", module)
                      }, null, 8, ["onDelete"])
                    ])
                  ]),
                  createVNode("td", { class: "px-2 py-3 whitespace-nowrap" }, [
                    createVNode("div", { class: "text-center" }, [
                      createVNode("input", {
                        type: "checkbox",
                        checked: __props.selectedModules.includes(module.id),
                        onChange: ($event) => emit("toggle-select", module.id)
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
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Components/Admin/School/SchoolModule/Table/ModuleTable.vue");
  return _sfc_setup$2 ? _sfc_setup$2(props, ctx) : void 0;
};
const _sfc_main$1 = {
  __name: "ModuleCardGrid",
  __ssrInlineRender: true,
  props: {
    modules: {
      type: Array,
      default: () => []
    },
    selectedModules: {
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
    const emit = __emit;
    const moduleStatusLabelKeyMap = {
      draft: "statusDraft",
      published: "statusPublished",
      archived: "statusArchived"
    };
    const moduleAvailabilityLabelKeyMap = {
      unlisted: "availabilityUnlisted",
      public: "availabilityPublic",
      private: "availabilityPrivate"
    };
    const getModuleStatusLabel = (status) => {
      return t(moduleStatusLabelKeyMap[status] || status || "no");
    };
    const getModuleAvailabilityLabel = (availability) => {
      return t(moduleAvailabilityLabelKeyMap[availability] || availability || "no");
    };
    const localModules = ref([]);
    watch(
      () => props.modules,
      (newVal) => {
        localModules.value = JSON.parse(JSON.stringify(newVal || []));
      },
      { immediate: true, deep: true }
    );
    const handleDragEnd = () => {
      const newOrderIds = localModules.value.map((module) => module.id);
      emit("update-sort-order", newOrderIds);
    };
    const getPrimaryImage = (module) => {
      if (module == null ? void 0 : module.primary_image) {
        return module.primary_image;
      }
      if (Array.isArray(module == null ? void 0 : module.images) && module.images.length) {
        return [...module.images].sort((a, b) => Number((a == null ? void 0 : a.order) ?? 0) - Number((b == null ? void 0 : b.order) ?? 0))[0];
      }
      return null;
    };
    const getModuleTitle = (module) => {
      var _a;
      return ((_a = module == null ? void 0 : module.translation) == null ? void 0 : _a.title) || `ID: ${module == null ? void 0 : module.id}`;
    };
    const getModuleSubtitle = (module) => {
      var _a;
      return ((_a = module == null ? void 0 : module.translation) == null ? void 0 : _a.subtitle) || "";
    };
    const getCourseTitle = (module) => {
      var _a, _b;
      return ((_b = (_a = module == null ? void 0 : module.course) == null ? void 0 : _a.translation) == null ? void 0 : _b.title) || `ID: ${(module == null ? void 0 : module.school_course_id) || "-"}`;
    };
    const getImageUrl = (image) => {
      return (image == null ? void 0 : image.thumb_url) || (image == null ? void 0 : image.webp_url) || (image == null ? void 0 : image.image_url) || (image == null ? void 0 : image.url) || "";
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
      _push(`<div${ssrRenderAttrs(mergeProps({ class: "bg-white dark:bg-slate-700 shadow-lg rounded-sm border border-slate-400 dark:border-slate-500 relative" }, _attrs))}><div class="flex items-center justify-between px-3 py-2 border-b border-slate-400 dark:border-slate-500"><div class="text-xs text-slate-600 dark:text-slate-200">${ssrInterpolate(unref(t)("selected"))}: ${ssrInterpolate(__props.selectedModules.length)}</div>`);
      if (localModules.value.length) {
        _push(`<label class="flex items-center text-xs text-slate-600 dark:text-slate-200 cursor-pointer"><span>${ssrInterpolate(unref(t)("selectAll"))}</span><input type="checkbox" class="mx-2"></label>`);
      } else {
        _push(`<!---->`);
      }
      _push(`</div>`);
      if (localModules.value.length) {
        _push(`<div class="p-3">`);
        _push(ssrRenderComponent(unref(draggable), {
          modelValue: localModules.value,
          "onUpdate:modelValue": ($event) => localModules.value = $event,
          tag: "div",
          "item-key": "id",
          handle: ".drag-handle",
          class: "grid gap-3 grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4",
          onEnd: handleDragEnd
        }, {
          item: withCtx(({ element: module }, _push2, _parent2, _scopeId) => {
            var _a, _b, _c, _d;
            if (_push2) {
              _push2(`<div class="relative flex flex-col h-full rounded-md border border-slate-400 dark:border-slate-500 bg-slate-50/70 dark:bg-slate-800/80 shadow-sm hover:shadow-md transition-shadow duration-150"${_scopeId}><div class="flex items-center justify-between px-2 py-1 border-b border-dashed border-slate-400 dark:border-slate-500"${_scopeId}><div class="flex items-center space-x-2"${_scopeId}><button type="button" class="drag-handle text-slate-400 hover:text-slate-700 dark:hover:text-slate-100"${ssrRenderAttr("title", unref(t)("dragDrop"))}${_scopeId}><svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"${_scopeId}><path d="M7 4h2v2H7V4zm4 0h2v2h-2V4zM7 8h2v2H7V8zm4 0h2v2h-2V8zM7 12h2v2H7v-2zm4 0h2v2h-2v-2z"${_scopeId}></path></svg></button><div class="text-[10px] font-semibold px-1.5 py-0.5 rounded-sm border border-gray-400 bg-slate-200 dark:bg-slate-700 text-slate-800 dark:text-blue-100"${ssrRenderAttr("title", `[sort: ${module.sort}] ${formatDate(module.published_at)}`)}${_scopeId}> ID: ${ssrInterpolate(module.id)}</div></div><div class="flex items-center space-x-2"${_scopeId}><span class="text-[10px] px-1.5 py-0.5 rounded-sm border border-gray-400 bg-fuchsia-100 dark:bg-fuchsia-900/50 text-fuchsia-700 dark:text-fuchsia-300"${ssrRenderAttr("title", unref(t)("availability"))}${_scopeId}>${ssrInterpolate(getModuleAvailabilityLabel(module.availability))}</span><input type="checkbox"${ssrIncludeBooleanAttr(__props.selectedModules.includes(module.id)) ? " checked" : ""}${_scopeId}></div></div><div class="relative w-full h-32 bg-slate-200 dark:bg-slate-900"${_scopeId}>`);
              if (module.images && module.images.length) {
                _push2(`<img${ssrRenderAttr("src", getImageUrl(getPrimaryImage(module)))}${ssrRenderAttr("alt", ((_a = getPrimaryImage(module)) == null ? void 0 : _a.alt) || unref(t)("defaultImageAlt"))}${ssrRenderAttr("title", ((_b = getPrimaryImage(module)) == null ? void 0 : _b.caption) || unref(t)("moduleImage"))} class="w-full h-full object-cover"${_scopeId}>`);
              } else {
                _push2(`<img src="/storage/school/school_module_images/default-image.png"${ssrRenderAttr("alt", unref(t)("defaultImageTitle"))} class="w-full h-full object-cover"${_scopeId}>`);
              }
              _push2(`</div><div class="flex flex-col flex-1 px-3 py-2 space-y-1"${_scopeId}><a${ssrRenderAttr("href", `/school/modules/${encodeURIComponent(module.slug)}`)} target="_blank" rel="noopener noreferrer" class="text-sm font-semibold text-sky-700 dark:text-sky-200 hover:underline line-clamp-2 text-center"${ssrRenderAttr("title", getModuleSubtitle(module) || getModuleTitle(module))}${_scopeId}>${ssrInterpolate(getModuleTitle(module))}</a><div class="text-[11px] font-semibold text-teal-600 dark:text-teal-200 text-center"${ssrRenderAttr("title", `ID: ${module.school_course_id}`)}${_scopeId}>${ssrInterpolate(unref(t)("course"))}: ${ssrInterpolate(getCourseTitle(module))}</div><div class="text-[10px] text-center text-slate-500 dark:text-slate-300 truncate"${ssrRenderAttr("title", module.slug)}${_scopeId}>${ssrInterpolate(module.slug)}</div><div class="flex flex-wrap justify-center gap-1 mt-1 text-[10px] font-semibold"${_scopeId}><span class="px-2 py-0.5 rounded-sm bg-sky-100 dark:bg-sky-900 border border-gray-400 text-sky-700 dark:text-sky-200"${ssrRenderAttr("title", unref(t)("status"))}${_scopeId}>${ssrInterpolate(getModuleStatusLabel(module.status))}</span></div><div class="flex flex-col justify-center text-gray-700 dark:text-gray-400 text-center text-[11px] mt-2"${_scopeId}><div${_scopeId}>${ssrInterpolate(unref(t)("duration"))}: ${ssrInterpolate(module.duration ?? "—")}</div><div${_scopeId}>${ssrInterpolate(unref(t)("lessons"))}: ${ssrInterpolate(module.lessons_count ?? 0)}</div></div><div class="flex flex-wrap justify-center gap-3 mt-2 text-[11px] text-slate-900 dark:text-slate-200"${_scopeId}><span${_scopeId}>${ssrInterpolate(unref(t)("views"))}: ${ssrInterpolate(module.views ?? 0)}</span><span${_scopeId}>${ssrInterpolate(unref(t)("likes"))}: ${ssrInterpolate(module.likes ?? 0)}</span></div><div class="flex flex-col justify-center mt-2 text-center text-[11px] text-slate-900 dark:text-slate-200"${_scopeId}><span${_scopeId}>${ssrInterpolate(unref(t)("ratingCount"))}: ${ssrInterpolate(module.rating_count ?? 0)}</span><span${_scopeId}>${ssrInterpolate(unref(t)("ratingAvg"))}: ${ssrInterpolate(module.rating_avg ?? 0)}</span></div></div><div class="flex items-center justify-center px-3 py-2 border-t border-dashed border-slate-400 dark:border-slate-500"${_scopeId}><div class="flex items-center space-x-1"${_scopeId}>`);
              _push2(ssrRenderComponent(_sfc_main$5, {
                isActive: module.activity,
                title: module.activity ? unref(t)("enabled") : unref(t)("disabled"),
                onToggleActivity: ($event) => emit("toggle-activity", module)
              }, null, _parent2, _scopeId));
              _push2(ssrRenderComponent(_sfc_main$6, {
                href: _ctx.route("admin.schoolModules.edit", { schoolModule: module.id })
              }, null, _parent2, _scopeId));
              _push2(ssrRenderComponent(_sfc_main$7, {
                onDelete: ($event) => emit("delete", module)
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
                          viewBox: "0 0 20 20"
                        }, [
                          createVNode("path", { d: "M7 4h2v2H7V4zm4 0h2v2h-2V4zM7 8h2v2H7V8zm4 0h2v2h-2V8zM7 12h2v2H7v-2zm4 0h2v2h-2v-2z" })
                        ]))
                      ], 8, ["title"]),
                      createVNode("div", {
                        class: "text-[10px] font-semibold px-1.5 py-0.5 rounded-sm border border-gray-400 bg-slate-200 dark:bg-slate-700 text-slate-800 dark:text-blue-100",
                        title: `[sort: ${module.sort}] ${formatDate(module.published_at)}`
                      }, " ID: " + toDisplayString(module.id), 9, ["title"])
                    ]),
                    createVNode("div", { class: "flex items-center space-x-2" }, [
                      createVNode("span", {
                        class: "text-[10px] px-1.5 py-0.5 rounded-sm border border-gray-400 bg-fuchsia-100 dark:bg-fuchsia-900/50 text-fuchsia-700 dark:text-fuchsia-300",
                        title: unref(t)("availability")
                      }, toDisplayString(getModuleAvailabilityLabel(module.availability)), 9, ["title"]),
                      createVNode("input", {
                        type: "checkbox",
                        checked: __props.selectedModules.includes(module.id),
                        onChange: ($event) => emit("toggle-select", module.id)
                      }, null, 40, ["checked", "onChange"])
                    ])
                  ]),
                  createVNode("div", { class: "relative w-full h-32 bg-slate-200 dark:bg-slate-900" }, [
                    module.images && module.images.length ? (openBlock(), createBlock("img", {
                      key: 0,
                      src: getImageUrl(getPrimaryImage(module)),
                      alt: ((_c = getPrimaryImage(module)) == null ? void 0 : _c.alt) || unref(t)("defaultImageAlt"),
                      title: ((_d = getPrimaryImage(module)) == null ? void 0 : _d.caption) || unref(t)("moduleImage"),
                      class: "w-full h-full object-cover"
                    }, null, 8, ["src", "alt", "title"])) : (openBlock(), createBlock("img", {
                      key: 1,
                      src: "/storage/school/school_module_images/default-image.png",
                      alt: unref(t)("defaultImageTitle"),
                      class: "w-full h-full object-cover"
                    }, null, 8, ["alt"]))
                  ]),
                  createVNode("div", { class: "flex flex-col flex-1 px-3 py-2 space-y-1" }, [
                    createVNode("a", {
                      href: `/school/modules/${encodeURIComponent(module.slug)}`,
                      target: "_blank",
                      rel: "noopener noreferrer",
                      class: "text-sm font-semibold text-sky-700 dark:text-sky-200 hover:underline line-clamp-2 text-center",
                      title: getModuleSubtitle(module) || getModuleTitle(module)
                    }, toDisplayString(getModuleTitle(module)), 9, ["href", "title"]),
                    createVNode("div", {
                      class: "text-[11px] font-semibold text-teal-600 dark:text-teal-200 text-center",
                      title: `ID: ${module.school_course_id}`
                    }, toDisplayString(unref(t)("course")) + ": " + toDisplayString(getCourseTitle(module)), 9, ["title"]),
                    createVNode("div", {
                      class: "text-[10px] text-center text-slate-500 dark:text-slate-300 truncate",
                      title: module.slug
                    }, toDisplayString(module.slug), 9, ["title"]),
                    createVNode("div", { class: "flex flex-wrap justify-center gap-1 mt-1 text-[10px] font-semibold" }, [
                      createVNode("span", {
                        class: "px-2 py-0.5 rounded-sm bg-sky-100 dark:bg-sky-900 border border-gray-400 text-sky-700 dark:text-sky-200",
                        title: unref(t)("status")
                      }, toDisplayString(getModuleStatusLabel(module.status)), 9, ["title"])
                    ]),
                    createVNode("div", { class: "flex flex-col justify-center text-gray-700 dark:text-gray-400 text-center text-[11px] mt-2" }, [
                      createVNode("div", null, toDisplayString(unref(t)("duration")) + ": " + toDisplayString(module.duration ?? "—"), 1),
                      createVNode("div", null, toDisplayString(unref(t)("lessons")) + ": " + toDisplayString(module.lessons_count ?? 0), 1)
                    ]),
                    createVNode("div", { class: "flex flex-wrap justify-center gap-3 mt-2 text-[11px] text-slate-900 dark:text-slate-200" }, [
                      createVNode("span", null, toDisplayString(unref(t)("views")) + ": " + toDisplayString(module.views ?? 0), 1),
                      createVNode("span", null, toDisplayString(unref(t)("likes")) + ": " + toDisplayString(module.likes ?? 0), 1)
                    ]),
                    createVNode("div", { class: "flex flex-col justify-center mt-2 text-center text-[11px] text-slate-900 dark:text-slate-200" }, [
                      createVNode("span", null, toDisplayString(unref(t)("ratingCount")) + ": " + toDisplayString(module.rating_count ?? 0), 1),
                      createVNode("span", null, toDisplayString(unref(t)("ratingAvg")) + ": " + toDisplayString(module.rating_avg ?? 0), 1)
                    ])
                  ]),
                  createVNode("div", { class: "flex items-center justify-center px-3 py-2 border-t border-dashed border-slate-400 dark:border-slate-500" }, [
                    createVNode("div", { class: "flex items-center space-x-1" }, [
                      createVNode(_sfc_main$5, {
                        isActive: module.activity,
                        title: module.activity ? unref(t)("enabled") : unref(t)("disabled"),
                        onToggleActivity: ($event) => emit("toggle-activity", module)
                      }, null, 8, ["isActive", "title", "onToggleActivity"]),
                      createVNode(_sfc_main$6, {
                        href: _ctx.route("admin.schoolModules.edit", { schoolModule: module.id })
                      }, null, 8, ["href"]),
                      createVNode(_sfc_main$7, {
                        onDelete: ($event) => emit("delete", module)
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
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Components/Admin/School/SchoolModule/View/ModuleCardGrid.vue");
  return _sfc_setup$1 ? _sfc_setup$1(props, ctx) : void 0;
};
const _sfc_main = {
  __name: "Index",
  __ssrInlineRender: true,
  props: {
    currentLocale: { type: String, default: "" },
    availableLocales: { type: Array, default: () => [] },
    adminSchoolModulesProcessingMode: { type: String, default: "frontend" },
    useServerProcessing: { type: Boolean, default: false },
    modules: { type: [Array, Object], default: () => [] },
    modulesCount: { type: Number, default: 0 },
    adminSchoolModulesPerPage: { type: Number, default: 6 },
    adminSchoolModulesDefaultSort: { type: String, default: "idDesc" },
    sortParam: { type: String, default: "" },
    search: { type: String, default: "" },
    errors: { type: Object, default: () => ({}) }
  },
  setup(__props) {
    const { t } = useI18n();
    const toast = useToast();
    const props = __props;
    const viewMode = ref(
      localStorage.getItem("admin_view_mode_modules") || "table"
    );
    watch(viewMode, (value) => {
      localStorage.setItem(
        "admin_view_mode_modules",
        value
      );
    });
    const modulesList = computed(() => {
      var _a;
      if (Array.isArray(props.modules)) {
        return props.modules;
      }
      if (Array.isArray((_a = props.modules) == null ? void 0 : _a.data)) {
        return props.modules.data;
      }
      return [];
    });
    const localModules = ref([]);
    watch(
      modulesList,
      (modules) => {
        localModules.value = JSON.parse(
          JSON.stringify(modules || [])
        );
      },
      {
        immediate: true,
        deep: true
      }
    );
    const itemsPerPage = ref(
      props.adminSchoolModulesPerPage || 6
    );
    const sortParam = ref(
      props.sortParam || props.adminSchoolModulesDefaultSort || "idDesc"
    );
    const searchQuery = ref(
      props.search || ""
    );
    const currentPage = ref(1);
    const serverCurrentPage = computed(() => {
      var _a, _b, _c;
      return Number(
        ((_b = (_a = props.modules) == null ? void 0 : _a.meta) == null ? void 0 : _b.current_page) ?? ((_c = props.modules) == null ? void 0 : _c.current_page) ?? 1
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
        route("admin.settings.updateAdminCountSchoolModules"),
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
        route("admin.settings.updateAdminSortSchoolModules"),
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
    const getModuleTitle = (module) => {
      var _a;
      return ((_a = module == null ? void 0 : module.translation) == null ? void 0 : _a.title) || `ID: ${module == null ? void 0 : module.id}`;
    };
    const getModuleSubtitle = (module) => {
      var _a;
      return ((_a = module == null ? void 0 : module.translation) == null ? void 0 : _a.subtitle) || "";
    };
    const getModuleShort = (module) => {
      var _a;
      return ((_a = module == null ? void 0 : module.translation) == null ? void 0 : _a.short) || "";
    };
    const getModuleDescription = (module) => {
      var _a;
      return ((_a = module == null ? void 0 : module.translation) == null ? void 0 : _a.description) || "";
    };
    const getModuleSlug = (module) => {
      return (module == null ? void 0 : module.slug) || "";
    };
    const getCourseTitle = (module) => {
      var _a, _b;
      return ((_b = (_a = module == null ? void 0 : module.course) == null ? void 0 : _a.translation) == null ? void 0 : _b.title) || `ID: ${(module == null ? void 0 : module.school_course_id) || "-"}`;
    };
    const getCourseSlug = (module) => {
      var _a;
      return ((_a = module == null ? void 0 : module.course) == null ? void 0 : _a.slug) || "";
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
    const sortModules = (items) => {
      const list = [
        ...items || []
      ];
      if (sortParam.value === "activity") {
        return list.filter(
          (module) => !!module.activity
        );
      }
      if (sortParam.value === "inactive") {
        return list.filter(
          (module) => !module.activity
        );
      }
      const sortMap = {
        idAsc: byNumberAsc("id"),
        idDesc: byNumberDesc("id"),
        sortAsc: byNumberAsc("sort"),
        sortDesc: byNumberDesc("sort"),
        courseAsc: byNumberAsc("school_course_id"),
        courseDesc: byNumberDesc("school_course_id"),
        titleAsc: (a, b) => compareText(
          getModuleTitle(a),
          getModuleTitle(b)
        ) || safeNumber(a == null ? void 0 : a.id) - safeNumber(b == null ? void 0 : b.id),
        titleDesc: (a, b) => compareText(
          getModuleTitle(b),
          getModuleTitle(a)
        ) || safeNumber(b == null ? void 0 : b.id) - safeNumber(a == null ? void 0 : a.id),
        slugAsc: (a, b) => compareText(
          getModuleSlug(a),
          getModuleSlug(b)
        ) || safeNumber(a == null ? void 0 : a.id) - safeNumber(b == null ? void 0 : b.id),
        slugDesc: (a, b) => compareText(
          getModuleSlug(b),
          getModuleSlug(a)
        ) || safeNumber(b == null ? void 0 : b.id) - safeNumber(a == null ? void 0 : a.id),
        statusAsc: byStringAsc("status"),
        statusDesc: byStringDesc("status"),
        availabilityAsc: byStringAsc("availability"),
        availabilityDesc: byStringDesc("availability"),
        difficultyAsc: byNumberAsc("difficulty"),
        difficultyDesc: byNumberDesc("difficulty"),
        durationAsc: byNumberAsc("duration"),
        durationDesc: byNumberDesc("duration"),
        lessonsAsc: byNumberAsc("lessons_count"),
        lessonsDesc: byNumberDesc("lessons_count"),
        imagesAsc: byNumberAsc("images_count"),
        imagesDesc: byNumberDesc("images_count"),
        likesAsc: byNumberAsc("likes"),
        likesDesc: byNumberDesc("likes"),
        likesCountAsc: byNumberAsc("likes_count"),
        likesCountDesc: byNumberDesc("likes_count"),
        viewsAsc: byNumberAsc("views"),
        viewsDesc: byNumberDesc("views"),
        popularityAsc: byNumberAsc("popularity"),
        popularityDesc: byNumberDesc("popularity"),
        ratingCountAsc: byNumberAsc("rating_count"),
        ratingCountDesc: byNumberDesc("rating_count"),
        ratingAvgAsc: byNumberAsc("rating_avg"),
        ratingAvgDesc: byNumberDesc("rating_avg"),
        activityAsc: byNumberAsc("activity"),
        activityDesc: byNumberDesc("activity"),
        publishedAtAsc: (a, b) => safeDate(a == null ? void 0 : a.published_at) - safeDate(b == null ? void 0 : b.published_at) || safeNumber(a == null ? void 0 : a.id) - safeNumber(b == null ? void 0 : b.id),
        publishedAtDesc: (a, b) => safeDate(b == null ? void 0 : b.published_at) - safeDate(a == null ? void 0 : a.published_at) || safeNumber(b == null ? void 0 : b.id) - safeNumber(a == null ? void 0 : a.id),
        dateAsc: (a, b) => safeDate(a == null ? void 0 : a.published_at) - safeDate(b == null ? void 0 : b.published_at) || safeNumber(a == null ? void 0 : a.id) - safeNumber(b == null ? void 0 : b.id),
        dateDesc: (a, b) => safeDate(b == null ? void 0 : b.published_at) - safeDate(a == null ? void 0 : a.published_at) || safeNumber(b == null ? void 0 : b.id) - safeNumber(a == null ? void 0 : a.id),
        createdAtAsc: (a, b) => safeDate(a == null ? void 0 : a.created_at) - safeDate(b == null ? void 0 : b.created_at) || safeNumber(a == null ? void 0 : a.id) - safeNumber(b == null ? void 0 : b.id),
        createdAtDesc: (a, b) => safeDate(b == null ? void 0 : b.created_at) - safeDate(a == null ? void 0 : a.created_at) || safeNumber(b == null ? void 0 : b.id) - safeNumber(a == null ? void 0 : a.id),
        updatedAtAsc: (a, b) => safeDate(a == null ? void 0 : a.updated_at) - safeDate(b == null ? void 0 : b.updated_at) || safeNumber(a == null ? void 0 : a.id) - safeNumber(b == null ? void 0 : b.id),
        updatedAtDesc: (a, b) => safeDate(b == null ? void 0 : b.updated_at) - safeDate(a == null ? void 0 : a.updated_at) || safeNumber(b == null ? void 0 : b.id) - safeNumber(a == null ? void 0 : a.id)
      };
      const sorter = sortMap[sortParam.value];
      return sorter ? list.sort(sorter) : list;
    };
    const filteredModules = computed(() => {
      let modules = localModules.value || [];
      if (props.useServerProcessing) {
        return modules;
      }
      const query = normalize(searchQuery.value);
      if (query) {
        modules = modules.filter((module) => {
          const title = normalize(
            getModuleTitle(module)
          );
          const subtitle = normalize(
            getModuleSubtitle(module)
          );
          const slug = normalize(
            getModuleSlug(module)
          );
          const short = normalize(
            getModuleShort(module)
          );
          const description = normalize(
            getModuleDescription(module)
          );
          const courseTitle = normalize(
            getCourseTitle(module)
          );
          const courseSlug = normalize(
            getCourseSlug(module)
          );
          return title.includes(query) || subtitle.includes(query) || slug.includes(query) || short.includes(query) || description.includes(query) || courseTitle.includes(query) || courseSlug.includes(query);
        });
      }
      return sortModules(
        modules
      );
    });
    const paginatedModules = computed(() => {
      const perPage = Number(itemsPerPage.value) || 6;
      const start = (currentPage.value - 1) * perPage;
      return filteredModules.value.slice(
        start,
        start + perPage
      );
    });
    const displayedModules = computed(() => {
      return props.useServerProcessing ? modulesList.value : paginatedModules.value;
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
    const moduleToDeleteId = ref(null);
    const moduleToDeleteTitle = ref("");
    const confirmDelete = (moduleOrId, title = null) => {
      if (typeof moduleOrId === "object" && moduleOrId !== null) {
        moduleToDeleteId.value = moduleOrId.id;
        moduleToDeleteTitle.value = title || getModuleTitle(
          moduleOrId
        );
      } else {
        moduleToDeleteId.value = moduleOrId;
        moduleToDeleteTitle.value = title || `ID: ${moduleOrId}`;
      }
      showConfirmDeleteModal.value = true;
    };
    const closeModal = () => {
      showConfirmDeleteModal.value = false;
      moduleToDeleteId.value = null;
      moduleToDeleteTitle.value = "";
    };
    const deleteModule = () => {
      if (moduleToDeleteId.value === null) {
        return;
      }
      const id = moduleToDeleteId.value;
      const title = moduleToDeleteTitle.value;
      router.delete(
        route(
          "admin.schoolModules.destroy",
          {
            schoolModule: id
          }
        ),
        {
          preserveScroll: true,
          preserveState: false,
          onSuccess: () => {
            toast.success(
              `Модуль "${title || `ID: ${id}`}" удалён.`
            );
          },
          onError: (errors) => {
            const errorKey = Object.keys(
              errors || {}
            )[0];
            const message = (errors == null ? void 0 : errors.general) || (errors == null ? void 0 : errors[errorKey]) || "Произошла ошибка при удалении.";
            toast.error(
              `${message} (Модуль: ${title || `ID: ${id}`})`
            );
          },
          onFinish: () => {
            closeModal();
          }
        }
      );
    };
    const patchModule = (moduleId, payload) => {
      const index = localModules.value.findIndex(
        (module) => module.id === moduleId
      );
      if (index === -1) {
        return;
      }
      localModules.value[index] = {
        ...localModules.value[index],
        ...payload
      };
    };
    const selectedModules = ref([]);
    const toggleAll = (payload) => {
      var _a;
      const checked = (payload == null ? void 0 : payload.checked) ?? ((_a = payload == null ? void 0 : payload.target) == null ? void 0 : _a.checked) ?? false;
      const ids = (payload == null ? void 0 : payload.ids) ?? displayedModules.value.map(
        (module) => module.id
      );
      if (checked) {
        selectedModules.value = [
          .../* @__PURE__ */ new Set([
            ...selectedModules.value,
            ...ids
          ])
        ];
        return;
      }
      selectedModules.value = selectedModules.value.filter(
        (id) => !ids.includes(id)
      );
    };
    const toggleSelectModule = (id) => {
      const index = selectedModules.value.indexOf(id);
      if (index > -1) {
        selectedModules.value.splice(
          index,
          1
        );
        return;
      }
      selectedModules.value.push(id);
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
          "admin.actions.schoolModules.updateSortBulk"
        ),
        {
          items
        },
        {
          preserveScroll: true,
          preserveState: true,
          onSuccess: () => {
            toast.success(
              "Порядок модулей успешно обновлён."
            );
          },
          onError: (errors) => {
            console.error(
              "Ошибка обновления сортировки модулей:",
              errors
            );
            toast.error(
              (errors == null ? void 0 : errors.message) || (errors == null ? void 0 : errors.general) || "Не удалось обновить порядок модулей."
            );
            router.reload({
              only: ["modules"],
              preserveScroll: true
            });
          }
        }
      );
    };
    const bulkToggleActivity = (newActivity) => {
      if (!selectedModules.value.length) {
        toast.warning(
          "Выберите модули для активации/деактивации."
        );
        return;
      }
      const ids = [
        ...selectedModules.value
      ];
      router.put(
        route(
          "admin.actions.schoolModules.bulkUpdateActivity"
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
              patchModule(
                id,
                {
                  activity: newActivity
                }
              );
            });
            selectedModules.value = [];
            toast.success(
              "Активность выбранных модулей обновлена."
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
        bulkToggleActivity(true);
      }
      if (action === "deactivate") {
        bulkToggleActivity(false);
      }
      event.target.value = "";
    };
    const toggleActivity = (module) => {
      const newActivity = !module.activity;
      const moduleTitle = getModuleTitle(module);
      const actionText = newActivity ? t("activated") : t("deactivated");
      router.put(
        route(
          "admin.actions.schoolModules.updateActivity",
          {
            schoolModule: module.id
          }
        ),
        {
          activity: newActivity
        },
        {
          preserveScroll: true,
          preserveState: true,
          onSuccess: () => {
            patchModule(
              module.id,
              {
                activity: newActivity
              }
            );
            module.activity = newActivity;
            toast.success(
              `Модуль "${moduleTitle}" ${actionText}.`
            );
          },
          onError: (errors) => {
            toast.error(
              (errors == null ? void 0 : errors.activity) || (errors == null ? void 0 : errors.general) || `Ошибка изменения активности для модуля "${moduleTitle}".`
            );
          }
        }
      );
    };
    return (_ctx, _push, _parent, _attrs) => {
      _push(ssrRenderComponent(AdminLayout, mergeProps({
        title: unref(t)("modules")
      }, _attrs), {
        header: withCtx((_, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(ssrRenderComponent(TitlePage, null, {
              default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                if (_push3) {
                  _push3(`${ssrInterpolate(unref(t)("modules"))}`);
                } else {
                  return [
                    createTextVNode(toDisplayString(unref(t)("modules")), 1)
                  ];
                }
              }),
              _: 1
            }, _parent2, _scopeId));
          } else {
            return [
              createVNode(TitlePage, null, {
                default: withCtx(() => [
                  createTextVNode(toDisplayString(unref(t)("modules")), 1)
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
              href: _ctx.route("admin.schoolModules.create")
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
                  _push3(` ${ssrInterpolate(unref(t)("addModule"))}`);
                } else {
                  return [
                    createTextVNode(" " + toDisplayString(unref(t)("addModule")), 1)
                  ];
                }
              }),
              _: 1
            }, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$9, {
              "setting-key": "adminSchoolModulesProcessingMode",
              mode: __props.adminSchoolModulesProcessingMode,
              "use-server-processing": __props.useServerProcessing,
              total: __props.modulesCount
            }, null, _parent2, _scopeId));
            _push2(`</div>`);
            if (__props.modulesCount && !__props.useServerProcessing) {
              _push2(ssrRenderComponent(_sfc_main$a, {
                modelValue: searchQuery.value,
                "onUpdate:modelValue": ($event) => searchQuery.value = $event,
                placeholder: unref(t)("searchByName")
              }, null, _parent2, _scopeId));
            } else {
              _push2(`<!---->`);
            }
            if (__props.modulesCount && __props.useServerProcessing) {
              _push2(ssrRenderComponent(_sfc_main$b, {
                modelValue: searchQuery.value,
                "onUpdate:modelValue": ($event) => searchQuery.value = $event
              }, null, _parent2, _scopeId));
            } else {
              _push2(`<!---->`);
            }
            if (__props.modulesCount) {
              _push2(`<div class="flex justify-between items-center flex-col md:flex-row my-3 gap-3"${_scopeId}>`);
              if (!__props.useServerProcessing) {
                _push2(ssrRenderComponent(_sfc_main$c, {
                  "items-per-page": itemsPerPage.value,
                  "onUpdate:itemsPerPage": ($event) => itemsPerPage.value = $event
                }, null, _parent2, _scopeId));
              } else {
                _push2(ssrRenderComponent(_sfc_main$d, {
                  "items-per-page": itemsPerPage.value,
                  "update-route": "admin.settings.updateAdminCountSchoolModules"
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
            if (__props.modulesCount) {
              _push2(`<div class="flex flex-col lg:flex-row items-center justify-between gap-3"${_scopeId}>`);
              _push2(ssrRenderComponent(_sfc_main$e, null, {
                default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                  if (_push3) {
                    _push3(`${ssrInterpolate(__props.modulesCount)}`);
                  } else {
                    return [
                      createTextVNode(toDisplayString(__props.modulesCount), 1)
                    ];
                  }
                }),
                _: 1
              }, _parent2, _scopeId));
              _push2(ssrRenderComponent(_sfc_main$4, { onChange: handleBulkAction }, null, _parent2, _scopeId));
              _push2(ssrRenderComponent(_sfc_main$f, {
                viewMode: viewMode.value,
                "onUpdate:viewMode": ($event) => viewMode.value = $event
              }, null, _parent2, _scopeId));
              _push2(`</div>`);
            } else {
              _push2(`<!---->`);
            }
            if (__props.modulesCount) {
              _push2(`<div class="flex justify-center items-center flex-col md:flex-row mt-3"${_scopeId}>`);
              if (!__props.useServerProcessing) {
                _push2(ssrRenderComponent(_sfc_main$g, {
                  "current-page": currentPage.value,
                  "items-per-page": itemsPerPage.value,
                  "total-items": filteredModules.value.length,
                  "onUpdate:currentPage": ($event) => currentPage.value = $event
                }, null, _parent2, _scopeId));
              } else {
                _push2(ssrRenderComponent(_sfc_main$h, { pagination: __props.modules }, null, _parent2, _scopeId));
              }
              _push2(`</div>`);
            } else {
              _push2(`<!---->`);
            }
            if (viewMode.value === "table") {
              _push2(ssrRenderComponent(_sfc_main$2, {
                modules: displayedModules.value,
                "selected-modules": selectedModules.value,
                onToggleActivity: toggleActivity,
                onDelete: confirmDelete,
                onUpdateSortOrder: handleSortOrderUpdate,
                onToggleSelect: toggleSelectModule,
                onToggleAll: toggleAll
              }, null, _parent2, _scopeId));
            } else {
              _push2(ssrRenderComponent(_sfc_main$1, {
                modules: displayedModules.value,
                "selected-modules": selectedModules.value,
                onToggleActivity: toggleActivity,
                onDelete: confirmDelete,
                onUpdateSortOrder: handleSortOrderUpdate,
                onToggleSelect: toggleSelectModule,
                onToggleAll: toggleAll
              }, null, _parent2, _scopeId));
            }
            if (__props.modulesCount) {
              _push2(`<div class="flex justify-center items-center flex-col md:flex-row mt-3"${_scopeId}>`);
              if (!__props.useServerProcessing) {
                _push2(ssrRenderComponent(_sfc_main$g, {
                  "current-page": currentPage.value,
                  "items-per-page": itemsPerPage.value,
                  "total-items": filteredModules.value.length,
                  "onUpdate:currentPage": ($event) => currentPage.value = $event
                }, null, _parent2, _scopeId));
              } else {
                _push2(ssrRenderComponent(_sfc_main$h, { pagination: __props.modules }, null, _parent2, _scopeId));
              }
              _push2(`</div>`);
            } else {
              _push2(`<!---->`);
            }
            _push2(`</div></div>`);
            _push2(ssrRenderComponent(_sfc_main$i, {
              show: showConfirmDeleteModal.value,
              onCancel: closeModal,
              onConfirm: deleteModule,
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
                      href: _ctx.route("admin.schoolModules.create")
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
                        createTextVNode(" " + toDisplayString(unref(t)("addModule")), 1)
                      ]),
                      _: 1
                    }, 8, ["href"]),
                    createVNode(_sfc_main$9, {
                      "setting-key": "adminSchoolModulesProcessingMode",
                      mode: __props.adminSchoolModulesProcessingMode,
                      "use-server-processing": __props.useServerProcessing,
                      total: __props.modulesCount
                    }, null, 8, ["mode", "use-server-processing", "total"])
                  ]),
                  __props.modulesCount && !__props.useServerProcessing ? (openBlock(), createBlock(_sfc_main$a, {
                    key: 0,
                    modelValue: searchQuery.value,
                    "onUpdate:modelValue": ($event) => searchQuery.value = $event,
                    placeholder: unref(t)("searchByName")
                  }, null, 8, ["modelValue", "onUpdate:modelValue", "placeholder"])) : createCommentVNode("", true),
                  __props.modulesCount && __props.useServerProcessing ? (openBlock(), createBlock(_sfc_main$b, {
                    key: 1,
                    modelValue: searchQuery.value,
                    "onUpdate:modelValue": ($event) => searchQuery.value = $event
                  }, null, 8, ["modelValue", "onUpdate:modelValue"])) : createCommentVNode("", true),
                  __props.modulesCount ? (openBlock(), createBlock("div", {
                    key: 2,
                    class: "flex justify-between items-center flex-col md:flex-row my-3 gap-3"
                  }, [
                    !__props.useServerProcessing ? (openBlock(), createBlock(_sfc_main$c, {
                      key: 0,
                      "items-per-page": itemsPerPage.value,
                      "onUpdate:itemsPerPage": ($event) => itemsPerPage.value = $event
                    }, null, 8, ["items-per-page", "onUpdate:itemsPerPage"])) : (openBlock(), createBlock(_sfc_main$d, {
                      key: 1,
                      "items-per-page": itemsPerPage.value,
                      "update-route": "admin.settings.updateAdminCountSchoolModules"
                    }, null, 8, ["items-per-page"])),
                    createVNode(_sfc_main$3, {
                      sortParam: sortParam.value,
                      "onUpdate:sortParam": ($event) => sortParam.value = $event
                    }, null, 8, ["sortParam", "onUpdate:sortParam"])
                  ])) : createCommentVNode("", true),
                  __props.modulesCount ? (openBlock(), createBlock("div", {
                    key: 3,
                    class: "flex flex-col lg:flex-row items-center justify-between gap-3"
                  }, [
                    createVNode(_sfc_main$e, null, {
                      default: withCtx(() => [
                        createTextVNode(toDisplayString(__props.modulesCount), 1)
                      ]),
                      _: 1
                    }),
                    createVNode(_sfc_main$4, { onChange: handleBulkAction }),
                    createVNode(_sfc_main$f, {
                      viewMode: viewMode.value,
                      "onUpdate:viewMode": ($event) => viewMode.value = $event
                    }, null, 8, ["viewMode", "onUpdate:viewMode"])
                  ])) : createCommentVNode("", true),
                  __props.modulesCount ? (openBlock(), createBlock("div", {
                    key: 4,
                    class: "flex justify-center items-center flex-col md:flex-row mt-3"
                  }, [
                    !__props.useServerProcessing ? (openBlock(), createBlock(_sfc_main$g, {
                      key: 0,
                      "current-page": currentPage.value,
                      "items-per-page": itemsPerPage.value,
                      "total-items": filteredModules.value.length,
                      "onUpdate:currentPage": ($event) => currentPage.value = $event
                    }, null, 8, ["current-page", "items-per-page", "total-items", "onUpdate:currentPage"])) : (openBlock(), createBlock(_sfc_main$h, {
                      key: 1,
                      pagination: __props.modules
                    }, null, 8, ["pagination"]))
                  ])) : createCommentVNode("", true),
                  viewMode.value === "table" ? (openBlock(), createBlock(_sfc_main$2, {
                    key: 5,
                    modules: displayedModules.value,
                    "selected-modules": selectedModules.value,
                    onToggleActivity: toggleActivity,
                    onDelete: confirmDelete,
                    onUpdateSortOrder: handleSortOrderUpdate,
                    onToggleSelect: toggleSelectModule,
                    onToggleAll: toggleAll
                  }, null, 8, ["modules", "selected-modules"])) : (openBlock(), createBlock(_sfc_main$1, {
                    key: 6,
                    modules: displayedModules.value,
                    "selected-modules": selectedModules.value,
                    onToggleActivity: toggleActivity,
                    onDelete: confirmDelete,
                    onUpdateSortOrder: handleSortOrderUpdate,
                    onToggleSelect: toggleSelectModule,
                    onToggleAll: toggleAll
                  }, null, 8, ["modules", "selected-modules"])),
                  __props.modulesCount ? (openBlock(), createBlock("div", {
                    key: 7,
                    class: "flex justify-center items-center flex-col md:flex-row mt-3"
                  }, [
                    !__props.useServerProcessing ? (openBlock(), createBlock(_sfc_main$g, {
                      key: 0,
                      "current-page": currentPage.value,
                      "items-per-page": itemsPerPage.value,
                      "total-items": filteredModules.value.length,
                      "onUpdate:currentPage": ($event) => currentPage.value = $event
                    }, null, 8, ["current-page", "items-per-page", "total-items", "onUpdate:currentPage"])) : (openBlock(), createBlock(_sfc_main$h, {
                      key: 1,
                      pagination: __props.modules
                    }, null, 8, ["pagination"]))
                  ])) : createCommentVNode("", true)
                ])
              ]),
              createVNode(_sfc_main$i, {
                show: showConfirmDeleteModal.value,
                onCancel: closeModal,
                onConfirm: deleteModule,
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
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Pages/Admin/School/SchoolModules/Index.vue");
  return _sfc_setup ? _sfc_setup(props, ctx) : void 0;
};
export {
  _sfc_main as default
};
