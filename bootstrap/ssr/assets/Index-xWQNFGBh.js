import { mergeProps, unref, useSSRContext, ref, watch, withCtx, createVNode, createBlock, openBlock, toDisplayString, computed, createTextVNode, createCommentVNode } from "vue";
import { ssrRenderAttrs, ssrInterpolate, ssrRenderAttr, ssrRenderComponent, ssrIncludeBooleanAttr } from "vue/server-renderer";
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
import { _ as _sfc_main$6 } from "./ActivityToggle-B1-nFMYK.js";
import { _ as _sfc_main$5 } from "./CloneIconButton-BfVfDOWt.js";
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
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Components/Admin/School/SchoolLesson/Select/BulkActionSelect.vue");
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
      _push(`<div${ssrRenderAttrs(mergeProps({ class: "flex justify-center items-center h-fit my-2" }, _attrs))}><label for="sortParam" class="hidden lg:block sm:mr-2 tracking-wider text-sm font-semibold text-slate-600 dark:text-slate-100">${ssrInterpolate(unref(t)("sort"))}</label><select id="sortParam"${ssrRenderAttr("value", __props.sortParam)} class="w-50 px-3 py-0.5 form-select bg-white dark:bg-gray-200 text-gray-600 dark:text-gray-900 border border-slate-400 dark:border-slate-600 rounded-sm shadow-sm"><option value="idDesc">${ssrInterpolate(unref(t)("idDesc"))}</option><option value="idAsc">${ssrInterpolate(unref(t)("idAsc"))}</option><option value="sortAsc">${ssrInterpolate(unref(t)("sortNumber"))} ↑</option><option value="sortDesc">${ssrInterpolate(unref(t)("sortNumber"))} ↓</option><option disabled>──────────────────────────────</option><option value="titleAsc">${ssrInterpolate(unref(t)("title"))} ↑</option><option value="titleDesc">${ssrInterpolate(unref(t)("title"))} ↓</option><option disabled>──────────────────────────────</option><option value="slugAsc">Slug A→Z</option><option value="slugDesc">Slug Z→A</option><option disabled>──────────────────────────────</option><option value="activityDesc">${ssrInterpolate(unref(t)("activity"))} ON→OFF</option><option value="activityAsc">${ssrInterpolate(unref(t)("activity"))} OFF→ON</option><option value="activity">${ssrInterpolate(unref(t)("active"))}</option><option value="inactive">${ssrInterpolate(unref(t)("inactive"))}</option><option disabled>──────────────────────────────</option><option value="publishedAtDesc">${ssrInterpolate(unref(t)("publishedAt"))} ↓</option><option value="publishedAtAsc">${ssrInterpolate(unref(t)("publishedAt"))} ↑</option><option disabled>──────────────────────────────</option><option value="viewsDesc">${ssrInterpolate(unref(t)("views"))} 9→0</option><option value="viewsAsc">${ssrInterpolate(unref(t)("views"))} 0→9</option><option value="likesDesc">${ssrInterpolate(unref(t)("likes"))} 9→0</option><option value="likesAsc">${ssrInterpolate(unref(t)("likes"))} 0→9</option><option value="likesCountDesc">${ssrInterpolate(unref(t)("ratingCount"))} 9→0</option><option value="likesCountAsc">${ssrInterpolate(unref(t)("ratingCount"))} 0→9</option><option disabled>──────────────────────────────</option><option value="popularityDesc">${ssrInterpolate(unref(t)("popularity"))} 9→0</option><option value="popularityAsc">${ssrInterpolate(unref(t)("popularity"))} 0→9</option><option value="ratingCountDesc">${ssrInterpolate(unref(t)("ratingCount"))} 9→0</option><option value="ratingCountAsc">${ssrInterpolate(unref(t)("ratingCount"))} 0→9</option><option value="ratingAvgDesc">${ssrInterpolate(unref(t)("ratingAvg"))} 9→0</option><option value="ratingAvgAsc">${ssrInterpolate(unref(t)("ratingAvg"))} 0→9</option><option disabled>──────────────────────────────</option><option value="statusAsc">${ssrInterpolate(unref(t)("status"))} A→Z</option><option value="statusDesc">${ssrInterpolate(unref(t)("status"))} Z→A</option><option value="availabilityAsc">${ssrInterpolate(unref(t)("availability"))} A→Z</option><option value="availabilityDesc">${ssrInterpolate(unref(t)("availability"))} Z→A</option><option value="accessTypeAsc">${ssrInterpolate(unref(t)("accessType"))} A→Z</option><option value="accessTypeDesc">${ssrInterpolate(unref(t)("accessType"))} Z→A</option><option disabled>──────────────────────────────</option><option value="difficultyDesc">${ssrInterpolate(unref(t)("sortDifficulty"))} 9→0</option><option value="difficultyAsc">${ssrInterpolate(unref(t)("sortDifficulty"))} 0→9</option><option value="durationDesc">${ssrInterpolate(unref(t)("duration"))} 9→0</option><option value="durationAsc">${ssrInterpolate(unref(t)("duration"))} 0→9</option><option disabled>──────────────────────────────</option><option value="moduleDesc">${ssrInterpolate(unref(t)("module"))} 9→0</option><option value="moduleAsc">${ssrInterpolate(unref(t)("module"))} 0→9</option><option value="hashtagsDesc">${ssrInterpolate(unref(t)("hashtags"))} 9→0</option><option value="hashtagsAsc">${ssrInterpolate(unref(t)("hashtags"))} 0→9</option><option value="imagesDesc">${ssrInterpolate(unref(t)("images"))} 9→0</option><option value="imagesAsc">${ssrInterpolate(unref(t)("images"))} 0→9</option><option disabled>──────────────────────────────</option><option value="contentTypeAsc">${ssrInterpolate(unref(t)("type"))} A→Z</option><option value="contentTypeDesc">${ssrInterpolate(unref(t)("type"))} Z→A</option><option value="contentIdDesc">${ssrInterpolate(unref(t)("content"))} ID 9→0</option><option value="contentIdAsc">${ssrInterpolate(unref(t)("content"))} ID 0→9</option><option value="previewValueDesc">${ssrInterpolate(unref(t)("previewPercent"))} 9→0</option><option value="previewValueAsc">${ssrInterpolate(unref(t)("previewPercent"))} 0→9</option><option disabled>──────────────────────────────</option><option value="createdAtDesc">${ssrInterpolate(unref(t)("createdAt"))} ↓</option><option value="createdAtAsc">${ssrInterpolate(unref(t)("createdAt"))} ↑</option><option value="updatedAtDesc">${ssrInterpolate(unref(t)("updatedAt"))} ↓</option><option value="updatedAtAsc">${ssrInterpolate(unref(t)("updatedAt"))} ↑</option><option disabled>──────────────────────────────</option></select></div>`);
    };
  }
};
const _sfc_setup$3 = _sfc_main$3.setup;
_sfc_main$3.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Components/Admin/School/SchoolLesson/Sort/SortSelect.vue");
  return _sfc_setup$3 ? _sfc_setup$3(props, ctx) : void 0;
};
const _sfc_main$2 = {
  __name: "LessonTable",
  __ssrInlineRender: true,
  props: {
    lessons: { type: Array, default: () => [] },
    selectedLessons: { type: Array, default: () => [] }
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
    const lessonStatusLabelKeyMap = {
      draft: "statusDraft",
      published: "statusPublished",
      archived: "statusArchived"
    };
    const lessonAvailabilityLabelKeyMap = {
      unlisted: "availabilityUnlisted",
      public: "availabilityPublic",
      private: "availabilityPrivate"
    };
    const lessonAccessTypeLabelKeyMap = {
      free: "free",
      paid: "paid",
      bonus: "bonus"
    };
    const lessonPreviewModeLabelKeyMap = {
      none: "previewNone",
      full: "previewFull",
      percent: "previewPercent",
      duration: "previewDuration",
      chars: "previewChars"
    };
    const getLessonStatusLabel = (status) => {
      return t(
        lessonStatusLabelKeyMap[status] || status || "no"
      );
    };
    const getLessonAvailabilityLabel = (availability) => {
      return t(
        lessonAvailabilityLabelKeyMap[availability] || availability || "no"
      );
    };
    const getLessonAccessTypeLabel = (accessType) => {
      return t(
        lessonAccessTypeLabelKeyMap[accessType] || accessType || "no"
      );
    };
    const getLessonPreviewModeLabel = (previewMode) => {
      return t(
        lessonPreviewModeLabelKeyMap[previewMode] || previewMode || "no"
      );
    };
    const localLessons = ref([]);
    watch(
      () => props.lessons,
      (lessons) => {
        localLessons.value = JSON.parse(
          JSON.stringify(lessons || [])
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
        localLessons.value.map(
          (lesson) => lesson.id
        )
      );
    };
    const getLessonTitle = (lesson) => {
      var _a;
      return ((_a = lesson == null ? void 0 : lesson.translation) == null ? void 0 : _a.title) || `ID: ${lesson == null ? void 0 : lesson.id}`;
    };
    const getLessonSubtitle = (lesson) => {
      var _a;
      return ((_a = lesson == null ? void 0 : lesson.translation) == null ? void 0 : _a.subtitle) || "";
    };
    const getLessonShort = (lesson) => {
      var _a;
      return ((_a = lesson == null ? void 0 : lesson.translation) == null ? void 0 : _a.short) || "";
    };
    const getModuleTitle = (lesson) => {
      var _a, _b;
      return ((_b = (_a = lesson == null ? void 0 : lesson.module) == null ? void 0 : _a.translation) == null ? void 0 : _b.title) || `ID: ${(lesson == null ? void 0 : lesson.school_module_id) || "-"}`;
    };
    const getCourse = (lesson) => {
      var _a;
      return ((_a = lesson == null ? void 0 : lesson.module) == null ? void 0 : _a.course) || null;
    };
    const getCourseTitle = (lesson) => {
      var _a;
      const course = getCourse(lesson);
      return ((_a = course == null ? void 0 : course.translation) == null ? void 0 : _a.title) || ((course == null ? void 0 : course.id) ? `ID: ${course.id}` : "—");
    };
    const getPrimaryImage = (lesson) => {
      if (lesson == null ? void 0 : lesson.primary_image) {
        return lesson.primary_image;
      }
      if (Array.isArray(lesson == null ? void 0 : lesson.images) && lesson.images.length) {
        return [...lesson.images].sort(
          (a, b) => Number((a == null ? void 0 : a.order) ?? 0) - Number((b == null ? void 0 : b.order) ?? 0)
        )[0];
      }
      return null;
    };
    const getImageUrl = (image) => {
      return (image == null ? void 0 : image.thumb_url) || (image == null ? void 0 : image.webp_url) || (image == null ? void 0 : image.image_url) || (image == null ? void 0 : image.url) || "";
    };
    const formatDate = (dateStr) => {
      if (!dateStr) {
        return "";
      }
      const date = new Date(dateStr);
      if (Number.isNaN(date.getTime())) {
        return "";
      }
      return date.toLocaleDateString("ru-RU", {
        year: "numeric",
        month: "long",
        day: "numeric"
      });
    };
    return (_ctx, _push, _parent, _attrs) => {
      _push(`<div${ssrRenderAttrs(mergeProps({ class: "bg-white dark:bg-slate-700 shadow-lg rounded-sm border border-slate-200 dark:border-slate-600 relative" }, _attrs))}><div class="flex items-center justify-between px-3 py-2 border-b border-slate-400 dark:border-slate-500"><div class="text-xs text-slate-600 dark:text-slate-200">${ssrInterpolate(unref(t)("selected"))}: ${ssrInterpolate(__props.selectedLessons.length)}</div>`);
      if (localLessons.value.length) {
        _push(`<label class="flex items-center text-xs text-slate-600 dark:text-slate-200 cursor-pointer"><span>${ssrInterpolate(unref(t)("selectAll"))}</span><input type="checkbox" class="mx-2"></label>`);
      } else {
        _push(`<!---->`);
      }
      _push(`</div><div class="overflow-x-auto">`);
      if (localLessons.value.length > 0) {
        _push(`<table class="table-auto w-full text-slate-700 dark:text-slate-100"><thead class="text-sm uppercase bg-slate-200 dark:bg-cyan-900 border border-solid border-gray-300 dark:border-gray-700"><tr><th class="px-2 py-3 w-px"><svg class="w-4 h-4 fill-current text-slate-800 dark:text-slate-200" viewBox="0 0 24 24"><path d="M12.707,2.293a1,1,0,0,0-1.414,0l-5,5A1,1,0,0,0,7.707,8.707L12,4.414l4.293,4.293a1,1,0,0,0,1.414-1.414Z"></path><path d="M16.293,15.293,12,19.586,7.707,15.293a1,1,0,0,0-1.414,1.414l5,5a1,1,0,0,0,1.414,0l5-5a1,1,0,0,0-1.414-1.414Z"></path></svg></th><th class="px-2 py-3 w-px"><div class="font-medium text-center">${ssrInterpolate(unref(t)("id"))}</div></th><th class="px-2 py-3 whitespace-nowrap"><div class="flex justify-center"${ssrRenderAttr("title", unref(t)("image"))}><svg class="w-6 h-6 fill-current shrink-0" viewBox="0 0 512 512"><path d="M0 96C0 60.7 28.7 32 64 32l384 0c35.3 0 64 28.7 64 64l0 320c0 35.3-28.7 64-64 64L64 480c-35.3 0-64-28.7-64-64L0 96zM323.8 202.5c-4.5-6.6-11.9-10.5-19.8-10.5s-15.4 3.9-19.8 10.5l-87 127.6L170.7 297c-4.6-5.7-11.5-9-18.7-9s-14.2 3.3-18.7 9l-64 80c-5.8 7.2-6.9 17.1-2.9 25.4s12.4 13.6 21.6 13.6l96 0 32 0 208 0c8.9 0 17.1-4.9 21.2-12.8s3.6-17.4-1.4-24.7l-120-176zM112 192a48 48 0 1 0 0-96 48 48 0 1 0 0 96z"></path></svg></div></th><th class="px-2 py-3 whitespace-nowrap"><div class="font-semibold text-left">${ssrInterpolate(unref(t)("lesson"))}</div></th><th class="px-2 py-3 whitespace-nowrap"><div class="font-semibold text-center">${ssrInterpolate(unref(t)("status"))}</div></th><th class="px-2 py-3 whitespace-nowrap"><div class="flex justify-center"${ssrRenderAttr("title", unref(t)("duration"))}><svg class="w-4 h-4" viewBox="0 0 24 24"><path class="fill-current text-violet-700 dark:text-violet-300" d="M22,13a1,1,0,0,1,0-2h1.949A12.006,12.006,0,0,0,13,.051V2a1,1,0,0,1-2,0V.051A12.006,12.006,0,0,0,.051,11H2a1,1,0,0,1,0,2H.051A12.006,12.006,0,0,0,11,23.949V22a1,1,0,0,1,2,0v1.949A12.006,12.006,0,0,0,23.949,13Zm-6,0H12a1,1,0,0,1-.832-.445l-4-6a1,1,0,1,1,1.664-1.11L12.535,11H16a1,1,0,0,1,0,2Z"></path></svg></div></th><th class="px-2 py-3 whitespace-nowrap"><div class="flex justify-center"${ssrRenderAttr("title", unref(t)("views"))}>${ssrInterpolate(unref(t)("views"))}</div></th><th class="px-2 py-3 whitespace-nowrap"><div class="flex justify-center"${ssrRenderAttr("title", unref(t)("rating"))}>${ssrInterpolate(unref(t)("rating"))}</div></th><th class="px-2 py-3 whitespace-nowrap"><div class="flex justify-center"${ssrRenderAttr("title", unref(t)("likes"))}>${ssrInterpolate(unref(t)("likes"))}</div></th><th class="px-2 py-3 whitespace-nowrap"><div class="font-semibold text-end">${ssrInterpolate(unref(t)("actions"))}</div></th><th class="px-2 py-3 whitespace-nowrap"><div class="text-center"><input type="checkbox"></div></th></tr></thead>`);
        _push(ssrRenderComponent(unref(draggable), {
          modelValue: localLessons.value,
          "onUpdate:modelValue": ($event) => localLessons.value = $event,
          tag: "tbody",
          "item-key": "id",
          handle: ".handle",
          onEnd: handleDragEnd
        }, {
          item: withCtx(({ element: lesson }, _push2, _parent2, _scopeId) => {
            var _a, _b, _c, _d;
            if (_push2) {
              _push2(`<tr class="text-sm font-semibold border-b-2 hover:bg-slate-100 dark:hover:bg-cyan-800"${_scopeId}><td class="px-2 py-1 text-center cursor-move handle"${_scopeId}><svg class="w-4 h-4 text-gray-500 dark:text-gray-300" fill="currentColor" viewBox="0 0 20 20"${_scopeId}><path d="M7 4h2v2H7V4zm4 0h2v2h-2V4zM7 8h2v2H7V8zm4 0h2v2h-2V8zM7 12h2v2H7v-2zm4 0h2v2h-2v-2z"${_scopeId}></path></svg></td><td class="px-2 py-3 whitespace-nowrap"${_scopeId}><div class="text-center text-xs text-slate-800 dark:text-blue-200"${ssrRenderAttr("title", `[sort: ${lesson.sort}] ${formatDate(lesson.published_at)}`)}${_scopeId}>${ssrInterpolate(lesson.id)}</div></td><td class="px-2 py-3 whitespace-nowrap"${_scopeId}><div class="flex justify-center"${ssrRenderAttr("title", getLessonTitle(lesson))}${_scopeId}>`);
              if (getPrimaryImage(lesson)) {
                _push2(`<img${ssrRenderAttr("src", getImageUrl(getPrimaryImage(lesson)))}${ssrRenderAttr("alt", ((_a = getPrimaryImage(lesson)) == null ? void 0 : _a.alt) || unref(t)("defaultImageAlt"))}${ssrRenderAttr("title", ((_b = getPrimaryImage(lesson)) == null ? void 0 : _b.caption) || unref(t)("lessonImage"))} class="h-8 w-12 object-cover rounded-sm"${_scopeId}>`);
              } else {
                _push2(`<img src="/storage/school/school_lesson_images/default-image.png"${ssrRenderAttr("alt", unref(t)("defaultImageTitle"))} class="h-8 w-12 object-cover rounded-sm"${_scopeId}>`);
              }
              _push2(`</div></td><td class="px-2 py-3 whitespace-nowrap"${_scopeId}><div class="min-w-0"${_scopeId}><a${ssrRenderAttr("href", `/school/lessons/${encodeURIComponent(lesson.slug)}`)} class="text-xs text-sky-600 dark:text-sky-200 hover:underline" target="_blank" rel="noopener noreferrer"${ssrRenderAttr("title", getLessonSubtitle(lesson) || getLessonShort(lesson))}${_scopeId}>${ssrInterpolate(getLessonTitle(lesson))}</a><div class="flex flex-row items-center gap-1 text-xs text-teal-700 dark:text-teal-200"${ssrRenderAttr("title", `ID: ${lesson.school_module_id}`)}${_scopeId}><span${_scopeId}>${ssrInterpolate(getModuleTitle(lesson))}</span></div><div class="flex flex-row items-center gap-1 text-xs text-slate-700 dark:text-slate-200"${_scopeId}><span${_scopeId}>${ssrInterpolate(getCourseTitle(lesson))}</span></div></div></td><td class="px-2 py-3 whitespace-nowrap"${_scopeId}><div class="text-center text-xs text-fuchsia-800 dark:text-fuchsia-400"${_scopeId}>${ssrInterpolate(getLessonStatusLabel(lesson.status))}</div><div class="text-center text-xs text-orange-600 dark:text-orange-300"${_scopeId}>${ssrInterpolate(getLessonAvailabilityLabel(lesson.availability))}</div><div class="text-center text-[10px] text-slate-500 dark:text-slate-300"${_scopeId}>${ssrInterpolate(getLessonAccessTypeLabel(lesson.access_type))}</div></td><td class="px-2 py-3 whitespace-nowrap"${_scopeId}><div class="text-center text-xs text-violet-700 dark:text-violet-300"${_scopeId}>${ssrInterpolate(lesson.duration ?? 0)} ${ssrInterpolate(getLessonPreviewModeLabel(lesson.preview_mode))}</div><div class="text-center text-[10px] text-slate-500 dark:text-slate-300"${_scopeId}>${ssrInterpolate(unref(t)("difficulty"))}: ${ssrInterpolate(lesson.difficulty ?? 0)}</div></td><td class="px-2 py-3 whitespace-nowrap"${_scopeId}><div class="text-center text-xs text-blue-700 dark:text-blue-300"${_scopeId}>${ssrInterpolate(lesson.views ?? 0)}</div></td><td class="px-2 py-3 whitespace-nowrap"${_scopeId}><div class="flex items-center justify-center gap-1"${_scopeId}><div class="text-center text-xs text-rose-500 dark:text-rose-300"${_scopeId}>${ssrInterpolate(lesson.rating_avg ?? 0)}</div><div class="text-center text-[10px] text-slate-500 dark:text-slate-300"${_scopeId}>${ssrInterpolate(lesson.rating_count ?? 0)}</div></div></td><td class="px-2 py-3 whitespace-nowrap"${_scopeId}><div class="text-center text-xs"${_scopeId}>${ssrInterpolate(lesson.likes ?? 0)}</div></td><td class="px-2 py-3 whitespace-nowrap"${_scopeId}><div class="flex flex-row items-center justify-end gap-1"${_scopeId}>`);
              _push2(ssrRenderComponent(_sfc_main$5, {
                title: unref(t)("clone"),
                onClone: ($event) => emit("clone", lesson)
              }, null, _parent2, _scopeId));
              _push2(ssrRenderComponent(_sfc_main$6, {
                isActive: lesson.activity,
                title: lesson.activity ? unref(t)("enabled") : unref(t)("disabled"),
                onToggleActivity: ($event) => emit("toggle-activity", lesson)
              }, null, _parent2, _scopeId));
              _push2(ssrRenderComponent(_sfc_main$7, {
                href: _ctx.route("admin.schoolLessons.edit", {
                  schoolLesson: lesson.id
                })
              }, null, _parent2, _scopeId));
              _push2(ssrRenderComponent(_sfc_main$8, {
                onDelete: ($event) => emit("delete", lesson)
              }, null, _parent2, _scopeId));
              _push2(`</div></td><td class="px-2 py-3 whitespace-nowrap"${_scopeId}><div class="text-center"${_scopeId}><input type="checkbox"${ssrIncludeBooleanAttr(__props.selectedLessons.includes(lesson.id)) ? " checked" : ""}${_scopeId}></div></td></tr>`);
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
                      title: `[sort: ${lesson.sort}] ${formatDate(lesson.published_at)}`
                    }, toDisplayString(lesson.id), 9, ["title"])
                  ]),
                  createVNode("td", { class: "px-2 py-3 whitespace-nowrap" }, [
                    createVNode("div", {
                      class: "flex justify-center",
                      title: getLessonTitle(lesson)
                    }, [
                      getPrimaryImage(lesson) ? (openBlock(), createBlock("img", {
                        key: 0,
                        src: getImageUrl(getPrimaryImage(lesson)),
                        alt: ((_c = getPrimaryImage(lesson)) == null ? void 0 : _c.alt) || unref(t)("defaultImageAlt"),
                        title: ((_d = getPrimaryImage(lesson)) == null ? void 0 : _d.caption) || unref(t)("lessonImage"),
                        class: "h-8 w-12 object-cover rounded-sm"
                      }, null, 8, ["src", "alt", "title"])) : (openBlock(), createBlock("img", {
                        key: 1,
                        src: "/storage/school/school_lesson_images/default-image.png",
                        alt: unref(t)("defaultImageTitle"),
                        class: "h-8 w-12 object-cover rounded-sm"
                      }, null, 8, ["alt"]))
                    ], 8, ["title"])
                  ]),
                  createVNode("td", { class: "px-2 py-3 whitespace-nowrap" }, [
                    createVNode("div", { class: "min-w-0" }, [
                      createVNode("a", {
                        href: `/school/lessons/${encodeURIComponent(lesson.slug)}`,
                        class: "text-xs text-sky-600 dark:text-sky-200 hover:underline",
                        target: "_blank",
                        rel: "noopener noreferrer",
                        title: getLessonSubtitle(lesson) || getLessonShort(lesson)
                      }, toDisplayString(getLessonTitle(lesson)), 9, ["href", "title"]),
                      createVNode("div", {
                        class: "flex flex-row items-center gap-1 text-xs text-teal-700 dark:text-teal-200",
                        title: `ID: ${lesson.school_module_id}`
                      }, [
                        createVNode("span", null, toDisplayString(getModuleTitle(lesson)), 1)
                      ], 8, ["title"]),
                      createVNode("div", { class: "flex flex-row items-center gap-1 text-xs text-slate-700 dark:text-slate-200" }, [
                        createVNode("span", null, toDisplayString(getCourseTitle(lesson)), 1)
                      ])
                    ])
                  ]),
                  createVNode("td", { class: "px-2 py-3 whitespace-nowrap" }, [
                    createVNode("div", { class: "text-center text-xs text-fuchsia-800 dark:text-fuchsia-400" }, toDisplayString(getLessonStatusLabel(lesson.status)), 1),
                    createVNode("div", { class: "text-center text-xs text-orange-600 dark:text-orange-300" }, toDisplayString(getLessonAvailabilityLabel(lesson.availability)), 1),
                    createVNode("div", { class: "text-center text-[10px] text-slate-500 dark:text-slate-300" }, toDisplayString(getLessonAccessTypeLabel(lesson.access_type)), 1)
                  ]),
                  createVNode("td", { class: "px-2 py-3 whitespace-nowrap" }, [
                    createVNode("div", { class: "text-center text-xs text-violet-700 dark:text-violet-300" }, toDisplayString(lesson.duration ?? 0) + " " + toDisplayString(getLessonPreviewModeLabel(lesson.preview_mode)), 1),
                    createVNode("div", { class: "text-center text-[10px] text-slate-500 dark:text-slate-300" }, toDisplayString(unref(t)("difficulty")) + ": " + toDisplayString(lesson.difficulty ?? 0), 1)
                  ]),
                  createVNode("td", { class: "px-2 py-3 whitespace-nowrap" }, [
                    createVNode("div", { class: "text-center text-xs text-blue-700 dark:text-blue-300" }, toDisplayString(lesson.views ?? 0), 1)
                  ]),
                  createVNode("td", { class: "px-2 py-3 whitespace-nowrap" }, [
                    createVNode("div", { class: "flex items-center justify-center gap-1" }, [
                      createVNode("div", { class: "text-center text-xs text-rose-500 dark:text-rose-300" }, toDisplayString(lesson.rating_avg ?? 0), 1),
                      createVNode("div", { class: "text-center text-[10px] text-slate-500 dark:text-slate-300" }, toDisplayString(lesson.rating_count ?? 0), 1)
                    ])
                  ]),
                  createVNode("td", { class: "px-2 py-3 whitespace-nowrap" }, [
                    createVNode("div", { class: "text-center text-xs" }, toDisplayString(lesson.likes ?? 0), 1)
                  ]),
                  createVNode("td", { class: "px-2 py-3 whitespace-nowrap" }, [
                    createVNode("div", { class: "flex flex-row items-center justify-end gap-1" }, [
                      createVNode(_sfc_main$5, {
                        title: unref(t)("clone"),
                        onClone: ($event) => emit("clone", lesson)
                      }, null, 8, ["title", "onClone"]),
                      createVNode(_sfc_main$6, {
                        isActive: lesson.activity,
                        title: lesson.activity ? unref(t)("enabled") : unref(t)("disabled"),
                        onToggleActivity: ($event) => emit("toggle-activity", lesson)
                      }, null, 8, ["isActive", "title", "onToggleActivity"]),
                      createVNode(_sfc_main$7, {
                        href: _ctx.route("admin.schoolLessons.edit", {
                          schoolLesson: lesson.id
                        })
                      }, null, 8, ["href"]),
                      createVNode(_sfc_main$8, {
                        onDelete: ($event) => emit("delete", lesson)
                      }, null, 8, ["onDelete"])
                    ])
                  ]),
                  createVNode("td", { class: "px-2 py-3 whitespace-nowrap" }, [
                    createVNode("div", { class: "text-center" }, [
                      createVNode("input", {
                        type: "checkbox",
                        checked: __props.selectedLessons.includes(lesson.id),
                        onChange: ($event) => emit("toggle-select", lesson.id)
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
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Components/Admin/School/SchoolLesson/Table/LessonTable.vue");
  return _sfc_setup$2 ? _sfc_setup$2(props, ctx) : void 0;
};
const _sfc_main$1 = {
  __name: "LessonCardGrid",
  __ssrInlineRender: true,
  props: {
    lessons: { type: Array, default: () => [] },
    selectedLessons: { type: Array, default: () => [] }
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
    const lessonStatusLabelKeyMap = {
      draft: "statusDraft",
      published: "statusPublished",
      archived: "statusArchived"
    };
    const lessonAvailabilityLabelKeyMap = {
      unlisted: "availabilityUnlisted",
      public: "availabilityPublic",
      private: "availabilityPrivate"
    };
    const lessonAccessTypeLabelKeyMap = {
      free: "free",
      paid: "paid",
      bonus: "bonus"
    };
    const lessonPreviewModeLabelKeyMap = {
      none: "previewNone",
      full: "previewFull",
      percent: "previewPercent",
      duration: "previewDuration",
      chars: "previewChars"
    };
    const getLessonStatusLabel = (status) => {
      return t(
        lessonStatusLabelKeyMap[status] || status || "no"
      );
    };
    const getLessonAvailabilityLabel = (availability) => {
      return t(
        lessonAvailabilityLabelKeyMap[availability] || availability || "no"
      );
    };
    const getLessonAccessTypeLabel = (accessType) => {
      return t(
        lessonAccessTypeLabelKeyMap[accessType] || accessType || "no"
      );
    };
    const getLessonPreviewModeLabel = (previewMode) => {
      return t(
        lessonPreviewModeLabelKeyMap[previewMode] || previewMode || "no"
      );
    };
    const localLessons = ref([]);
    watch(
      () => props.lessons,
      (lessons) => {
        localLessons.value = JSON.parse(
          JSON.stringify(lessons || [])
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
        localLessons.value.map(
          (lesson) => lesson.id
        )
      );
    };
    const getLessonTitle = (lesson) => {
      var _a;
      return ((_a = lesson == null ? void 0 : lesson.translation) == null ? void 0 : _a.title) || `ID: ${lesson == null ? void 0 : lesson.id}`;
    };
    const getLessonSubtitle = (lesson) => {
      var _a;
      return ((_a = lesson == null ? void 0 : lesson.translation) == null ? void 0 : _a.subtitle) || "";
    };
    const getLessonShort = (lesson) => {
      var _a;
      return ((_a = lesson == null ? void 0 : lesson.translation) == null ? void 0 : _a.short) || "";
    };
    const getModuleTitle = (lesson) => {
      var _a, _b;
      return ((_b = (_a = lesson == null ? void 0 : lesson.module) == null ? void 0 : _a.translation) == null ? void 0 : _b.title) || `ID: ${(lesson == null ? void 0 : lesson.school_module_id) || "-"}`;
    };
    const getCourse = (lesson) => {
      var _a;
      return ((_a = lesson == null ? void 0 : lesson.module) == null ? void 0 : _a.course) || null;
    };
    const getCourseTitle = (lesson) => {
      var _a;
      const course = getCourse(lesson);
      return ((_a = course == null ? void 0 : course.translation) == null ? void 0 : _a.title) || ((course == null ? void 0 : course.id) ? `ID: ${course.id}` : "—");
    };
    const getPrimaryImage = (lesson) => {
      if (lesson == null ? void 0 : lesson.primary_image) {
        return lesson.primary_image;
      }
      if (Array.isArray(lesson == null ? void 0 : lesson.images) && lesson.images.length) {
        return [...lesson.images].sort(
          (a, b) => Number((a == null ? void 0 : a.order) ?? 0) - Number((b == null ? void 0 : b.order) ?? 0)
        )[0];
      }
      return null;
    };
    const getImageUrl = (image) => {
      return (image == null ? void 0 : image.thumb_url) || (image == null ? void 0 : image.webp_url) || (image == null ? void 0 : image.image_url) || (image == null ? void 0 : image.url) || "";
    };
    const formatDate = (dateStr) => {
      if (!dateStr) {
        return "";
      }
      const date = new Date(dateStr);
      if (Number.isNaN(date.getTime())) {
        return "";
      }
      return date.toLocaleDateString("ru-RU", {
        year: "numeric",
        month: "short",
        day: "numeric"
      });
    };
    return (_ctx, _push, _parent, _attrs) => {
      _push(`<div${ssrRenderAttrs(mergeProps({ class: "bg-white dark:bg-slate-700 shadow-lg rounded-sm border border-slate-400 dark:border-slate-500 relative" }, _attrs))}><div class="flex items-center justify-between px-3 py-2 border-b border-slate-400 dark:border-slate-500"><div class="text-xs text-slate-600 dark:text-slate-200">${ssrInterpolate(unref(t)("selected"))}: ${ssrInterpolate(__props.selectedLessons.length)}</div>`);
      if (localLessons.value.length) {
        _push(`<label class="flex items-center text-xs text-slate-600 dark:text-slate-200 cursor-pointer"><span>${ssrInterpolate(unref(t)("selectAll"))}</span><input type="checkbox" class="mx-2"></label>`);
      } else {
        _push(`<!---->`);
      }
      _push(`</div>`);
      if (localLessons.value.length) {
        _push(`<div class="p-3">`);
        _push(ssrRenderComponent(unref(draggable), {
          modelValue: localLessons.value,
          "onUpdate:modelValue": ($event) => localLessons.value = $event,
          tag: "div",
          "item-key": "id",
          handle: ".drag-handle",
          class: "grid gap-3 grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4",
          onEnd: handleDragEnd
        }, {
          item: withCtx(({ element: lesson }, _push2, _parent2, _scopeId) => {
            var _a, _b, _c, _d;
            if (_push2) {
              _push2(`<div class="relative flex flex-col h-full rounded-md border border-slate-400 dark:border-slate-500 bg-slate-50/70 dark:bg-slate-800/80 shadow-sm hover:shadow-md transition-shadow duration-150"${_scopeId}><div class="flex items-center justify-between px-2 py-1 border-b border-dashed border-slate-400 dark:border-slate-500"${_scopeId}><div class="flex items-center space-x-2"${_scopeId}><button type="button" class="drag-handle text-slate-400 hover:text-slate-700 dark:hover:text-slate-100"${ssrRenderAttr("title", unref(t)("dragDrop"))}${_scopeId}><svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"${_scopeId}><path d="M7 4h2v2H7V4zm4 0h2v2h-2V4zM7 8h2v2H7V8zm4 0h2v2h-2V8zM7 12h2v2H7v-2zm4 0h2v2h-2v-2z"${_scopeId}></path></svg></button><div class="text-[10px] font-semibold px-1.5 py-0.5 rounded-sm border border-gray-400 bg-slate-200 dark:bg-slate-700 text-slate-800 dark:text-blue-100"${ssrRenderAttr("title", `[sort: ${lesson.sort}] ${formatDate(lesson.published_at)}`)}${_scopeId}> ID: ${ssrInterpolate(lesson.id)}</div></div><div class="flex items-center space-x-2"${_scopeId}><span class="text-[10px] px-1.5 py-0.5 rounded-sm border border-gray-400 bg-fuchsia-100 dark:bg-fuchsia-900/50 text-fuchsia-700 dark:text-fuchsia-300"${_scopeId}>${ssrInterpolate(getLessonAvailabilityLabel(lesson.availability))}</span><input type="checkbox"${ssrIncludeBooleanAttr(__props.selectedLessons.includes(lesson.id)) ? " checked" : ""}${_scopeId}></div></div><div class="relative w-full h-32 bg-slate-200 dark:bg-slate-900"${_scopeId}>`);
              if (getPrimaryImage(lesson)) {
                _push2(`<img${ssrRenderAttr("src", getImageUrl(getPrimaryImage(lesson)))}${ssrRenderAttr("alt", ((_a = getPrimaryImage(lesson)) == null ? void 0 : _a.alt) || unref(t)("defaultImageAlt"))}${ssrRenderAttr("title", ((_b = getPrimaryImage(lesson)) == null ? void 0 : _b.caption) || getLessonTitle(lesson))} class="w-full h-full object-cover"${_scopeId}>`);
              } else {
                _push2(`<img src="/storage/school/school_lesson_images/default-image.png"${ssrRenderAttr("alt", unref(t)("defaultImageTitle"))} class="w-full h-full object-cover"${_scopeId}>`);
              }
              _push2(`</div><div class="flex flex-col flex-1 px-3 py-2 space-y-1"${_scopeId}><a${ssrRenderAttr("href", `/school/lessons/${encodeURIComponent(lesson.slug)}`)} target="_blank" rel="noopener noreferrer" class="text-sm font-semibold text-sky-700 dark:text-sky-200 hover:underline line-clamp-2 text-center"${ssrRenderAttr("title", getLessonSubtitle(lesson) || getLessonShort(lesson))}${_scopeId}>${ssrInterpolate(getLessonTitle(lesson))}</a><div class="text-[11px] text-center"${_scopeId}><div class="text-slate-600 dark:text-slate-400 font-semibold"${_scopeId}>${ssrInterpolate(unref(t)("module"))}: ${ssrInterpolate(getModuleTitle(lesson))}</div><div class="text-teal-700 dark:text-teal-200 font-semibold"${_scopeId}>${ssrInterpolate(unref(t)("course"))}: ${ssrInterpolate(getCourseTitle(lesson))}</div></div><div class="text-[10px] text-center text-slate-500 dark:text-slate-300 truncate"${ssrRenderAttr("title", lesson.slug)}${_scopeId}>${ssrInterpolate(lesson.slug)}</div><div class="flex flex-wrap justify-center gap-1 mt-1 text-[10px] font-semibold"${_scopeId}><span class="px-2 py-0.5 rounded-sm bg-sky-100 dark:bg-sky-900 border border-gray-400 text-sky-700 dark:text-sky-200"${_scopeId}>${ssrInterpolate(getLessonStatusLabel(lesson.status))}</span><span class="px-2 py-0.5 rounded-sm bg-emerald-100 dark:bg-emerald-900 border border-gray-400 text-emerald-700 dark:text-emerald-200"${_scopeId}>${ssrInterpolate(getLessonAccessTypeLabel(lesson.access_type))}</span></div><div class="flex flex-col justify-center text-gray-700 dark:text-gray-400 text-center text-[11px] mt-2"${_scopeId}><div${_scopeId}>${ssrInterpolate(unref(t)("duration"))}: ${ssrInterpolate(lesson.duration ?? "—")} ${ssrInterpolate(getLessonPreviewModeLabel(lesson.preview_mode))}</div><div${_scopeId}>${ssrInterpolate(unref(t)("difficulty"))}: ${ssrInterpolate(lesson.difficulty ?? "—")}</div></div><div class="flex flex-wrap justify-center gap-3 mt-2 text-[11px] text-slate-900 dark:text-slate-200"${_scopeId}><span${_scopeId}>${ssrInterpolate(unref(t)("views"))}: ${ssrInterpolate(lesson.views ?? 0)}</span><span${_scopeId}>${ssrInterpolate(unref(t)("likes"))}: ${ssrInterpolate(lesson.likes ?? 0)}</span></div><div class="flex flex-col justify-center mt-2 text-center text-[11px] text-slate-900 dark:text-slate-200"${_scopeId}><span${_scopeId}>${ssrInterpolate(unref(t)("ratingCount"))}: ${ssrInterpolate(lesson.rating_count ?? 0)}</span><span${_scopeId}>${ssrInterpolate(unref(t)("ratingAvg"))}: ${ssrInterpolate(lesson.rating_avg ?? 0)}</span></div></div><div class="flex items-center justify-center px-3 py-2 border-t border-dashed border-slate-400 dark:border-slate-500"${_scopeId}><div class="flex items-center space-x-1"${_scopeId}>`);
              _push2(ssrRenderComponent(_sfc_main$5, {
                title: unref(t)("clone"),
                onClone: ($event) => emit("clone", lesson)
              }, null, _parent2, _scopeId));
              _push2(ssrRenderComponent(_sfc_main$6, {
                isActive: lesson.activity,
                title: lesson.activity ? unref(t)("enabled") : unref(t)("disabled"),
                onToggleActivity: ($event) => emit("toggle-activity", lesson)
              }, null, _parent2, _scopeId));
              _push2(ssrRenderComponent(_sfc_main$7, {
                href: _ctx.route("admin.schoolLessons.edit", {
                  schoolLesson: lesson.id
                })
              }, null, _parent2, _scopeId));
              _push2(ssrRenderComponent(_sfc_main$8, {
                onDelete: ($event) => emit("delete", lesson)
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
                        title: `[sort: ${lesson.sort}] ${formatDate(lesson.published_at)}`
                      }, " ID: " + toDisplayString(lesson.id), 9, ["title"])
                    ]),
                    createVNode("div", { class: "flex items-center space-x-2" }, [
                      createVNode("span", { class: "text-[10px] px-1.5 py-0.5 rounded-sm border border-gray-400 bg-fuchsia-100 dark:bg-fuchsia-900/50 text-fuchsia-700 dark:text-fuchsia-300" }, toDisplayString(getLessonAvailabilityLabel(lesson.availability)), 1),
                      createVNode("input", {
                        type: "checkbox",
                        checked: __props.selectedLessons.includes(lesson.id),
                        onChange: ($event) => emit("toggle-select", lesson.id)
                      }, null, 40, ["checked", "onChange"])
                    ])
                  ]),
                  createVNode("div", { class: "relative w-full h-32 bg-slate-200 dark:bg-slate-900" }, [
                    getPrimaryImage(lesson) ? (openBlock(), createBlock("img", {
                      key: 0,
                      src: getImageUrl(getPrimaryImage(lesson)),
                      alt: ((_c = getPrimaryImage(lesson)) == null ? void 0 : _c.alt) || unref(t)("defaultImageAlt"),
                      title: ((_d = getPrimaryImage(lesson)) == null ? void 0 : _d.caption) || getLessonTitle(lesson),
                      class: "w-full h-full object-cover"
                    }, null, 8, ["src", "alt", "title"])) : (openBlock(), createBlock("img", {
                      key: 1,
                      src: "/storage/school/school_lesson_images/default-image.png",
                      alt: unref(t)("defaultImageTitle"),
                      class: "w-full h-full object-cover"
                    }, null, 8, ["alt"]))
                  ]),
                  createVNode("div", { class: "flex flex-col flex-1 px-3 py-2 space-y-1" }, [
                    createVNode("a", {
                      href: `/school/lessons/${encodeURIComponent(lesson.slug)}`,
                      target: "_blank",
                      rel: "noopener noreferrer",
                      class: "text-sm font-semibold text-sky-700 dark:text-sky-200 hover:underline line-clamp-2 text-center",
                      title: getLessonSubtitle(lesson) || getLessonShort(lesson)
                    }, toDisplayString(getLessonTitle(lesson)), 9, ["href", "title"]),
                    createVNode("div", { class: "text-[11px] text-center" }, [
                      createVNode("div", { class: "text-slate-600 dark:text-slate-400 font-semibold" }, toDisplayString(unref(t)("module")) + ": " + toDisplayString(getModuleTitle(lesson)), 1),
                      createVNode("div", { class: "text-teal-700 dark:text-teal-200 font-semibold" }, toDisplayString(unref(t)("course")) + ": " + toDisplayString(getCourseTitle(lesson)), 1)
                    ]),
                    createVNode("div", {
                      class: "text-[10px] text-center text-slate-500 dark:text-slate-300 truncate",
                      title: lesson.slug
                    }, toDisplayString(lesson.slug), 9, ["title"]),
                    createVNode("div", { class: "flex flex-wrap justify-center gap-1 mt-1 text-[10px] font-semibold" }, [
                      createVNode("span", { class: "px-2 py-0.5 rounded-sm bg-sky-100 dark:bg-sky-900 border border-gray-400 text-sky-700 dark:text-sky-200" }, toDisplayString(getLessonStatusLabel(lesson.status)), 1),
                      createVNode("span", { class: "px-2 py-0.5 rounded-sm bg-emerald-100 dark:bg-emerald-900 border border-gray-400 text-emerald-700 dark:text-emerald-200" }, toDisplayString(getLessonAccessTypeLabel(lesson.access_type)), 1)
                    ]),
                    createVNode("div", { class: "flex flex-col justify-center text-gray-700 dark:text-gray-400 text-center text-[11px] mt-2" }, [
                      createVNode("div", null, toDisplayString(unref(t)("duration")) + ": " + toDisplayString(lesson.duration ?? "—") + " " + toDisplayString(getLessonPreviewModeLabel(lesson.preview_mode)), 1),
                      createVNode("div", null, toDisplayString(unref(t)("difficulty")) + ": " + toDisplayString(lesson.difficulty ?? "—"), 1)
                    ]),
                    createVNode("div", { class: "flex flex-wrap justify-center gap-3 mt-2 text-[11px] text-slate-900 dark:text-slate-200" }, [
                      createVNode("span", null, toDisplayString(unref(t)("views")) + ": " + toDisplayString(lesson.views ?? 0), 1),
                      createVNode("span", null, toDisplayString(unref(t)("likes")) + ": " + toDisplayString(lesson.likes ?? 0), 1)
                    ]),
                    createVNode("div", { class: "flex flex-col justify-center mt-2 text-center text-[11px] text-slate-900 dark:text-slate-200" }, [
                      createVNode("span", null, toDisplayString(unref(t)("ratingCount")) + ": " + toDisplayString(lesson.rating_count ?? 0), 1),
                      createVNode("span", null, toDisplayString(unref(t)("ratingAvg")) + ": " + toDisplayString(lesson.rating_avg ?? 0), 1)
                    ])
                  ]),
                  createVNode("div", { class: "flex items-center justify-center px-3 py-2 border-t border-dashed border-slate-400 dark:border-slate-500" }, [
                    createVNode("div", { class: "flex items-center space-x-1" }, [
                      createVNode(_sfc_main$5, {
                        title: unref(t)("clone"),
                        onClone: ($event) => emit("clone", lesson)
                      }, null, 8, ["title", "onClone"]),
                      createVNode(_sfc_main$6, {
                        isActive: lesson.activity,
                        title: lesson.activity ? unref(t)("enabled") : unref(t)("disabled"),
                        onToggleActivity: ($event) => emit("toggle-activity", lesson)
                      }, null, 8, ["isActive", "title", "onToggleActivity"]),
                      createVNode(_sfc_main$7, {
                        href: _ctx.route("admin.schoolLessons.edit", {
                          schoolLesson: lesson.id
                        })
                      }, null, 8, ["href"]),
                      createVNode(_sfc_main$8, {
                        onDelete: ($event) => emit("delete", lesson)
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
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Components/Admin/School/SchoolLesson/View/LessonCardGrid.vue");
  return _sfc_setup$1 ? _sfc_setup$1(props, ctx) : void 0;
};
const _sfc_main = {
  __name: "Index",
  __ssrInlineRender: true,
  props: {
    currentLocale: { type: String, default: "" },
    availableLocales: { type: Array, default: () => [] },
    adminSchoolLessonsProcessingMode: { type: String, default: "frontend" },
    useServerProcessing: { type: Boolean, default: false },
    lessons: { type: [Array, Object], default: () => [] },
    lessonsCount: { type: Number, default: 0 },
    adminSchoolLessonsPerPage: { type: Number, default: 6 },
    adminSchoolLessonsDefaultSort: { type: String, default: "idDesc" },
    sortParam: { type: String, default: "" },
    search: { type: String, default: "" },
    errors: { type: Object, default: () => ({}) }
  },
  setup(__props) {
    const { t } = useI18n();
    const toast = useToast();
    const props = __props;
    const viewMode = ref(
      localStorage.getItem("admin_view_mode_lessons") || "table"
    );
    watch(viewMode, (value) => {
      localStorage.setItem("admin_view_mode_lessons", value);
    });
    const lessonsList = computed(() => {
      var _a;
      if (Array.isArray(props.lessons)) {
        return props.lessons;
      }
      if (Array.isArray((_a = props.lessons) == null ? void 0 : _a.data)) {
        return props.lessons.data;
      }
      return [];
    });
    const localLessons = ref([]);
    watch(
      lessonsList,
      (lessons) => {
        localLessons.value = JSON.parse(
          JSON.stringify(lessons || [])
        );
      },
      {
        immediate: true,
        deep: true
      }
    );
    const itemsPerPage = ref(
      props.adminSchoolLessonsPerPage || 6
    );
    const sortParam = ref(
      props.sortParam || props.adminSchoolLessonsDefaultSort || "idDesc"
    );
    const searchQuery = ref(
      props.search || ""
    );
    const currentPage = ref(1);
    const serverCurrentPage = computed(() => {
      var _a, _b, _c;
      return Number(
        ((_b = (_a = props.lessons) == null ? void 0 : _a.meta) == null ? void 0 : _b.current_page) ?? ((_c = props.lessons) == null ? void 0 : _c.current_page) ?? 1
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
        route("admin.settings.updateAdminCountSchoolLessons"),
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
        route("admin.settings.updateAdminSortSchoolLessons"),
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
    const getLessonTitle = (lesson) => {
      var _a;
      return ((_a = lesson == null ? void 0 : lesson.translation) == null ? void 0 : _a.title) || `ID: ${lesson == null ? void 0 : lesson.id}`;
    };
    const getLessonSubtitle = (lesson) => {
      var _a;
      return ((_a = lesson == null ? void 0 : lesson.translation) == null ? void 0 : _a.subtitle) || "";
    };
    const getLessonShort = (lesson) => {
      var _a;
      return ((_a = lesson == null ? void 0 : lesson.translation) == null ? void 0 : _a.short) || "";
    };
    const getLessonDescription = (lesson) => {
      var _a;
      return ((_a = lesson == null ? void 0 : lesson.translation) == null ? void 0 : _a.description) || "";
    };
    const getLessonSlug = (lesson) => {
      return (lesson == null ? void 0 : lesson.slug) || "";
    };
    const getModuleTitle = (lesson) => {
      var _a, _b;
      return ((_b = (_a = lesson == null ? void 0 : lesson.module) == null ? void 0 : _a.translation) == null ? void 0 : _b.title) || `ID: ${(lesson == null ? void 0 : lesson.school_module_id) || "-"}`;
    };
    const getModuleSlug = (lesson) => {
      var _a;
      return ((_a = lesson == null ? void 0 : lesson.module) == null ? void 0 : _a.slug) || "";
    };
    const getCourse = (lesson) => {
      var _a;
      return ((_a = lesson == null ? void 0 : lesson.module) == null ? void 0 : _a.course) || null;
    };
    const getCourseTitle = (lesson) => {
      var _a;
      const course = getCourse(lesson);
      return ((_a = course == null ? void 0 : course.translation) == null ? void 0 : _a.title) || ((course == null ? void 0 : course.id) ? `ID: ${course.id}` : "");
    };
    const getCourseSlug = (lesson) => {
      var _a;
      return ((_a = getCourse(lesson)) == null ? void 0 : _a.slug) || "";
    };
    const getHashtagTitle = (hashtag) => {
      var _a, _b;
      return ((_a = hashtag == null ? void 0 : hashtag.translation) == null ? void 0 : _a.name) || ((_b = hashtag == null ? void 0 : hashtag.translation) == null ? void 0 : _b.title) || (hashtag == null ? void 0 : hashtag.slug) || "";
    };
    const getHashtagsText = (lesson) => {
      const hashtags = Array.isArray(lesson == null ? void 0 : lesson.hashtags) ? lesson.hashtags : [];
      return hashtags.map(getHashtagTitle).filter(Boolean).join(" ");
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
    const sortLessons = (items) => {
      const list = [
        ...items || []
      ];
      if (sortParam.value === "activity") {
        return list.filter(
          (lesson) => !!lesson.activity
        );
      }
      if (sortParam.value === "inactive") {
        return list.filter(
          (lesson) => !lesson.activity
        );
      }
      const sortMap = {
        idAsc: byNumberAsc("id"),
        idDesc: byNumberDesc("id"),
        sortAsc: byNumberAsc("sort"),
        sortDesc: byNumberDesc("sort"),
        moduleAsc: byNumberAsc("school_module_id"),
        moduleDesc: byNumberDesc("school_module_id"),
        titleAsc: (a, b) => compareText(
          getLessonTitle(a),
          getLessonTitle(b)
        ) || safeNumber(a == null ? void 0 : a.id) - safeNumber(b == null ? void 0 : b.id),
        titleDesc: (a, b) => compareText(
          getLessonTitle(b),
          getLessonTitle(a)
        ) || safeNumber(b == null ? void 0 : b.id) - safeNumber(a == null ? void 0 : a.id),
        slugAsc: (a, b) => compareText(
          getLessonSlug(a),
          getLessonSlug(b)
        ) || safeNumber(a == null ? void 0 : a.id) - safeNumber(b == null ? void 0 : b.id),
        slugDesc: (a, b) => compareText(
          getLessonSlug(b),
          getLessonSlug(a)
        ) || safeNumber(b == null ? void 0 : b.id) - safeNumber(a == null ? void 0 : a.id),
        statusAsc: byStringAsc("status"),
        statusDesc: byStringDesc("status"),
        availabilityAsc: byStringAsc("availability"),
        availabilityDesc: byStringDesc("availability"),
        accessTypeAsc: byStringAsc("access_type"),
        accessTypeDesc: byStringDesc("access_type"),
        contentTypeAsc: byStringAsc("content_type"),
        contentTypeDesc: byStringDesc("content_type"),
        contentIdAsc: byNumberAsc("content_id"),
        contentIdDesc: byNumberDesc("content_id"),
        difficultyAsc: byNumberAsc("difficulty"),
        difficultyDesc: byNumberDesc("difficulty"),
        durationAsc: byNumberAsc("duration"),
        durationDesc: byNumberDesc("duration"),
        previewValueAsc: byNumberAsc("preview_value"),
        previewValueDesc: byNumberDesc("preview_value"),
        popularityAsc: byNumberAsc("popularity"),
        popularityDesc: byNumberDesc("popularity"),
        ratingCountAsc: byNumberAsc("rating_count"),
        ratingCountDesc: byNumberDesc("rating_count"),
        ratingAvgAsc: byNumberAsc("rating_avg"),
        ratingAvgDesc: byNumberDesc("rating_avg"),
        viewsAsc: byNumberAsc("views"),
        viewsDesc: byNumberDesc("views"),
        likesAsc: byNumberAsc("likes"),
        likesDesc: byNumberDesc("likes"),
        likesCountAsc: byNumberAsc("likes_count"),
        likesCountDesc: byNumberDesc("likes_count"),
        imagesAsc: byNumberAsc("images_count"),
        imagesDesc: byNumberDesc("images_count"),
        hashtagsAsc: byNumberAsc("hashtags_count"),
        hashtagsDesc: byNumberDesc("hashtags_count"),
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
    const filteredLessons = computed(() => {
      let lessons = localLessons.value || [];
      if (props.useServerProcessing) {
        return lessons;
      }
      const query = normalize(searchQuery.value);
      if (query) {
        lessons = lessons.filter((lesson) => {
          const title = normalize(
            getLessonTitle(lesson)
          );
          const subtitle = normalize(
            getLessonSubtitle(lesson)
          );
          const slug = normalize(
            getLessonSlug(lesson)
          );
          const short = normalize(
            getLessonShort(lesson)
          );
          const description = normalize(
            getLessonDescription(lesson)
          );
          const moduleTitle = normalize(
            getModuleTitle(lesson)
          );
          const moduleSlug = normalize(
            getModuleSlug(lesson)
          );
          const courseTitle = normalize(
            getCourseTitle(lesson)
          );
          const courseSlug = normalize(
            getCourseSlug(lesson)
          );
          const hashtags = normalize(
            getHashtagsText(lesson)
          );
          const status = normalize(
            lesson == null ? void 0 : lesson.status
          );
          const availability = normalize(
            lesson == null ? void 0 : lesson.availability
          );
          const accessType = normalize(
            lesson == null ? void 0 : lesson.access_type
          );
          const contentType = normalize(
            lesson == null ? void 0 : lesson.content_type
          );
          const contentId = normalize(
            lesson == null ? void 0 : lesson.content_id
          );
          return title.includes(query) || subtitle.includes(query) || slug.includes(query) || short.includes(query) || description.includes(query) || moduleTitle.includes(query) || moduleSlug.includes(query) || courseTitle.includes(query) || courseSlug.includes(query) || hashtags.includes(query) || status.includes(query) || availability.includes(query) || accessType.includes(query) || contentType.includes(query) || contentId.includes(query);
        });
      }
      return sortLessons(
        lessons
      );
    });
    const paginatedLessons = computed(() => {
      const perPage = Number(itemsPerPage.value) || 6;
      const start = (currentPage.value - 1) * perPage;
      return filteredLessons.value.slice(
        start,
        start + perPage
      );
    });
    const displayedLessons = computed(() => {
      return props.useServerProcessing ? lessonsList.value : paginatedLessons.value;
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
    const lessonToDeleteId = ref(null);
    const lessonToDeleteTitle = ref("");
    const confirmDelete = (lessonOrId, title = null) => {
      if (typeof lessonOrId === "object" && lessonOrId !== null) {
        lessonToDeleteId.value = lessonOrId.id;
        lessonToDeleteTitle.value = title || getLessonTitle(
          lessonOrId
        );
      } else {
        lessonToDeleteId.value = lessonOrId;
        lessonToDeleteTitle.value = title || `ID: ${lessonOrId}`;
      }
      showConfirmDeleteModal.value = true;
    };
    const closeModal = () => {
      showConfirmDeleteModal.value = false;
      lessonToDeleteId.value = null;
      lessonToDeleteTitle.value = "";
    };
    const deleteLesson = () => {
      if (lessonToDeleteId.value === null) {
        return;
      }
      const id = lessonToDeleteId.value;
      const title = lessonToDeleteTitle.value;
      router.delete(
        route(
          "admin.schoolLessons.destroy",
          {
            schoolLesson: id
          }
        ),
        {
          preserveScroll: true,
          preserveState: false,
          onSuccess: () => {
            toast.success(
              `Урок "${title || `ID: ${id}`}" удалён.`
            );
          },
          onError: (errors) => {
            const errorKey = Object.keys(
              errors || {}
            )[0];
            const message = (errors == null ? void 0 : errors.general) || (errors == null ? void 0 : errors[errorKey]) || "Произошла ошибка при удалении.";
            toast.error(
              `${message} (Урок: ${title || `ID: ${id}`})`
            );
          },
          onFinish: () => {
            closeModal();
          }
        }
      );
    };
    const patchLesson = (lessonId, payload) => {
      const index = localLessons.value.findIndex(
        (lesson) => lesson.id === lessonId
      );
      if (index === -1) {
        return;
      }
      localLessons.value[index] = {
        ...localLessons.value[index],
        ...payload
      };
    };
    const selectedLessons = ref([]);
    const toggleAll = (payload) => {
      var _a;
      const checked = (payload == null ? void 0 : payload.checked) ?? ((_a = payload == null ? void 0 : payload.target) == null ? void 0 : _a.checked) ?? false;
      const ids = (payload == null ? void 0 : payload.ids) ?? displayedLessons.value.map(
        (lesson) => lesson.id
      );
      if (checked) {
        selectedLessons.value = [
          .../* @__PURE__ */ new Set([
            ...selectedLessons.value,
            ...ids
          ])
        ];
        return;
      }
      selectedLessons.value = selectedLessons.value.filter(
        (id) => !ids.includes(id)
      );
    };
    const toggleSelectLesson = (id) => {
      const index = selectedLessons.value.indexOf(id);
      if (index > -1) {
        selectedLessons.value.splice(
          index,
          1
        );
        return;
      }
      selectedLessons.value.push(id);
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
          "admin.actions.schoolLessons.updateSortBulk"
        ),
        {
          items
        },
        {
          preserveScroll: true,
          preserveState: true,
          onSuccess: () => {
            toast.success(
              "Порядок уроков успешно обновлён."
            );
          },
          onError: (errors) => {
            console.error(
              "Ошибка обновления сортировки уроков:",
              errors
            );
            toast.error(
              (errors == null ? void 0 : errors.message) || (errors == null ? void 0 : errors.general) || "Не удалось обновить порядок уроков."
            );
            router.reload({
              only: ["lessons"],
              preserveScroll: true
            });
          }
        }
      );
    };
    const bulkToggleActivity = (newActivity) => {
      if (!selectedLessons.value.length) {
        toast.warning(
          "Выберите уроки для активации/деактивации."
        );
        return;
      }
      const ids = [
        ...selectedLessons.value
      ];
      router.put(
        route(
          "admin.actions.schoolLessons.bulkUpdateActivity"
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
              patchLesson(
                id,
                {
                  activity: newActivity
                }
              );
            });
            selectedLessons.value = [];
            toast.success(
              "Активность выбранных уроков обновлена."
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
    const toggleActivity = (lesson) => {
      const newActivity = !lesson.activity;
      const lessonTitle = getLessonTitle(lesson);
      const actionText = newActivity ? t("activated") : t("deactivated");
      router.put(
        route(
          "admin.actions.schoolLessons.updateActivity",
          {
            schoolLesson: lesson.id
          }
        ),
        {
          activity: newActivity
        },
        {
          preserveScroll: true,
          preserveState: true,
          onSuccess: () => {
            patchLesson(
              lesson.id,
              {
                activity: newActivity
              }
            );
            lesson.activity = newActivity;
            toast.success(
              `Урок "${lessonTitle}" ${actionText}.`
            );
          },
          onError: (errors) => {
            toast.error(
              (errors == null ? void 0 : errors.activity) || (errors == null ? void 0 : errors.general) || `Ошибка изменения активности для урока "${lessonTitle}".`
            );
          }
        }
      );
    };
    const cloneLesson = (lesson) => {
      router.post(
        route(
          "admin.actions.schoolLessons.clone",
          {
            schoolLesson: lesson.id
          }
        ),
        {},
        {
          preserveScroll: true,
          onSuccess: () => {
            toast.success(
              "Урок успешно клонирован."
            );
          },
          onError: () => {
            toast.error(
              "Ошибка при клонировании урока."
            );
          }
        }
      );
    };
    return (_ctx, _push, _parent, _attrs) => {
      _push(ssrRenderComponent(AdminLayout, mergeProps({
        title: unref(t)("lessons")
      }, _attrs), {
        header: withCtx((_, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(ssrRenderComponent(TitlePage, null, {
              default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                if (_push3) {
                  _push3(`${ssrInterpolate(unref(t)("lessons"))}`);
                } else {
                  return [
                    createTextVNode(toDisplayString(unref(t)("lessons")), 1)
                  ];
                }
              }),
              _: 1
            }, _parent2, _scopeId));
          } else {
            return [
              createVNode(TitlePage, null, {
                default: withCtx(() => [
                  createTextVNode(toDisplayString(unref(t)("lessons")), 1)
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
              href: _ctx.route("admin.schoolLessons.create")
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
                  _push3(` ${ssrInterpolate(unref(t)("addLesson"))}`);
                } else {
                  return [
                    createTextVNode(" " + toDisplayString(unref(t)("addLesson")), 1)
                  ];
                }
              }),
              _: 1
            }, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$a, {
              "setting-key": "adminSchoolLessonsProcessingMode",
              mode: __props.adminSchoolLessonsProcessingMode,
              "use-server-processing": __props.useServerProcessing,
              total: __props.lessonsCount
            }, null, _parent2, _scopeId));
            _push2(`</div>`);
            if (__props.lessonsCount && !__props.useServerProcessing) {
              _push2(ssrRenderComponent(_sfc_main$b, {
                modelValue: searchQuery.value,
                "onUpdate:modelValue": ($event) => searchQuery.value = $event,
                placeholder: unref(t)("searchByName")
              }, null, _parent2, _scopeId));
            } else {
              _push2(`<!---->`);
            }
            if (__props.lessonsCount && __props.useServerProcessing) {
              _push2(ssrRenderComponent(_sfc_main$c, {
                modelValue: searchQuery.value,
                "onUpdate:modelValue": ($event) => searchQuery.value = $event
              }, null, _parent2, _scopeId));
            } else {
              _push2(`<!---->`);
            }
            if (__props.lessonsCount) {
              _push2(`<div class="flex justify-between items-center flex-col md:flex-row my-3 gap-3"${_scopeId}>`);
              if (!__props.useServerProcessing) {
                _push2(ssrRenderComponent(_sfc_main$d, {
                  "items-per-page": itemsPerPage.value,
                  "onUpdate:itemsPerPage": ($event) => itemsPerPage.value = $event
                }, null, _parent2, _scopeId));
              } else {
                _push2(ssrRenderComponent(_sfc_main$e, {
                  "items-per-page": itemsPerPage.value,
                  "update-route": "admin.settings.updateAdminCountSchoolLessons"
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
            if (__props.lessonsCount) {
              _push2(`<div class="flex flex-col lg:flex-row items-center justify-between gap-3"${_scopeId}>`);
              _push2(ssrRenderComponent(_sfc_main$f, null, {
                default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                  if (_push3) {
                    _push3(`${ssrInterpolate(__props.lessonsCount)}`);
                  } else {
                    return [
                      createTextVNode(toDisplayString(__props.lessonsCount), 1)
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
            if (__props.lessonsCount) {
              _push2(`<div class="flex justify-center items-center flex-col md:flex-row mt-3"${_scopeId}>`);
              if (!__props.useServerProcessing) {
                _push2(ssrRenderComponent(_sfc_main$h, {
                  "current-page": currentPage.value,
                  "items-per-page": itemsPerPage.value,
                  "total-items": filteredLessons.value.length,
                  "onUpdate:currentPage": ($event) => currentPage.value = $event
                }, null, _parent2, _scopeId));
              } else {
                _push2(ssrRenderComponent(_sfc_main$i, { pagination: __props.lessons }, null, _parent2, _scopeId));
              }
              _push2(`</div>`);
            } else {
              _push2(`<!---->`);
            }
            if (viewMode.value === "table") {
              _push2(ssrRenderComponent(_sfc_main$2, {
                lessons: displayedLessons.value,
                "selected-lessons": selectedLessons.value,
                onToggleActivity: toggleActivity,
                onDelete: confirmDelete,
                onUpdateSortOrder: handleSortOrderUpdate,
                onToggleSelect: toggleSelectLesson,
                onToggleAll: toggleAll,
                onClone: cloneLesson
              }, null, _parent2, _scopeId));
            } else {
              _push2(ssrRenderComponent(_sfc_main$1, {
                lessons: displayedLessons.value,
                "selected-lessons": selectedLessons.value,
                onToggleActivity: toggleActivity,
                onDelete: confirmDelete,
                onUpdateSortOrder: handleSortOrderUpdate,
                onToggleSelect: toggleSelectLesson,
                onToggleAll: toggleAll,
                onClone: cloneLesson
              }, null, _parent2, _scopeId));
            }
            if (__props.lessonsCount) {
              _push2(`<div class="flex justify-center items-center flex-col md:flex-row mt-3"${_scopeId}>`);
              if (!__props.useServerProcessing) {
                _push2(ssrRenderComponent(_sfc_main$h, {
                  "current-page": currentPage.value,
                  "items-per-page": itemsPerPage.value,
                  "total-items": filteredLessons.value.length,
                  "onUpdate:currentPage": ($event) => currentPage.value = $event
                }, null, _parent2, _scopeId));
              } else {
                _push2(ssrRenderComponent(_sfc_main$i, { pagination: __props.lessons }, null, _parent2, _scopeId));
              }
              _push2(`</div>`);
            } else {
              _push2(`<!---->`);
            }
            _push2(`</div></div>`);
            _push2(ssrRenderComponent(_sfc_main$j, {
              show: showConfirmDeleteModal.value,
              onCancel: closeModal,
              onConfirm: deleteLesson,
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
                      href: _ctx.route("admin.schoolLessons.create")
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
                        createTextVNode(" " + toDisplayString(unref(t)("addLesson")), 1)
                      ]),
                      _: 1
                    }, 8, ["href"]),
                    createVNode(_sfc_main$a, {
                      "setting-key": "adminSchoolLessonsProcessingMode",
                      mode: __props.adminSchoolLessonsProcessingMode,
                      "use-server-processing": __props.useServerProcessing,
                      total: __props.lessonsCount
                    }, null, 8, ["mode", "use-server-processing", "total"])
                  ]),
                  __props.lessonsCount && !__props.useServerProcessing ? (openBlock(), createBlock(_sfc_main$b, {
                    key: 0,
                    modelValue: searchQuery.value,
                    "onUpdate:modelValue": ($event) => searchQuery.value = $event,
                    placeholder: unref(t)("searchByName")
                  }, null, 8, ["modelValue", "onUpdate:modelValue", "placeholder"])) : createCommentVNode("", true),
                  __props.lessonsCount && __props.useServerProcessing ? (openBlock(), createBlock(_sfc_main$c, {
                    key: 1,
                    modelValue: searchQuery.value,
                    "onUpdate:modelValue": ($event) => searchQuery.value = $event
                  }, null, 8, ["modelValue", "onUpdate:modelValue"])) : createCommentVNode("", true),
                  __props.lessonsCount ? (openBlock(), createBlock("div", {
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
                      "update-route": "admin.settings.updateAdminCountSchoolLessons"
                    }, null, 8, ["items-per-page"])),
                    createVNode(_sfc_main$3, {
                      sortParam: sortParam.value,
                      "onUpdate:sortParam": ($event) => sortParam.value = $event
                    }, null, 8, ["sortParam", "onUpdate:sortParam"])
                  ])) : createCommentVNode("", true),
                  __props.lessonsCount ? (openBlock(), createBlock("div", {
                    key: 3,
                    class: "flex flex-col lg:flex-row items-center justify-between gap-3"
                  }, [
                    createVNode(_sfc_main$f, null, {
                      default: withCtx(() => [
                        createTextVNode(toDisplayString(__props.lessonsCount), 1)
                      ]),
                      _: 1
                    }),
                    createVNode(_sfc_main$4, { onChange: handleBulkAction }),
                    createVNode(_sfc_main$g, {
                      viewMode: viewMode.value,
                      "onUpdate:viewMode": ($event) => viewMode.value = $event
                    }, null, 8, ["viewMode", "onUpdate:viewMode"])
                  ])) : createCommentVNode("", true),
                  __props.lessonsCount ? (openBlock(), createBlock("div", {
                    key: 4,
                    class: "flex justify-center items-center flex-col md:flex-row mt-3"
                  }, [
                    !__props.useServerProcessing ? (openBlock(), createBlock(_sfc_main$h, {
                      key: 0,
                      "current-page": currentPage.value,
                      "items-per-page": itemsPerPage.value,
                      "total-items": filteredLessons.value.length,
                      "onUpdate:currentPage": ($event) => currentPage.value = $event
                    }, null, 8, ["current-page", "items-per-page", "total-items", "onUpdate:currentPage"])) : (openBlock(), createBlock(_sfc_main$i, {
                      key: 1,
                      pagination: __props.lessons
                    }, null, 8, ["pagination"]))
                  ])) : createCommentVNode("", true),
                  viewMode.value === "table" ? (openBlock(), createBlock(_sfc_main$2, {
                    key: 5,
                    lessons: displayedLessons.value,
                    "selected-lessons": selectedLessons.value,
                    onToggleActivity: toggleActivity,
                    onDelete: confirmDelete,
                    onUpdateSortOrder: handleSortOrderUpdate,
                    onToggleSelect: toggleSelectLesson,
                    onToggleAll: toggleAll,
                    onClone: cloneLesson
                  }, null, 8, ["lessons", "selected-lessons"])) : (openBlock(), createBlock(_sfc_main$1, {
                    key: 6,
                    lessons: displayedLessons.value,
                    "selected-lessons": selectedLessons.value,
                    onToggleActivity: toggleActivity,
                    onDelete: confirmDelete,
                    onUpdateSortOrder: handleSortOrderUpdate,
                    onToggleSelect: toggleSelectLesson,
                    onToggleAll: toggleAll,
                    onClone: cloneLesson
                  }, null, 8, ["lessons", "selected-lessons"])),
                  __props.lessonsCount ? (openBlock(), createBlock("div", {
                    key: 7,
                    class: "flex justify-center items-center flex-col md:flex-row mt-3"
                  }, [
                    !__props.useServerProcessing ? (openBlock(), createBlock(_sfc_main$h, {
                      key: 0,
                      "current-page": currentPage.value,
                      "items-per-page": itemsPerPage.value,
                      "total-items": filteredLessons.value.length,
                      "onUpdate:currentPage": ($event) => currentPage.value = $event
                    }, null, 8, ["current-page", "items-per-page", "total-items", "onUpdate:currentPage"])) : (openBlock(), createBlock(_sfc_main$i, {
                      key: 1,
                      pagination: __props.lessons
                    }, null, 8, ["pagination"]))
                  ])) : createCommentVNode("", true)
                ])
              ]),
              createVNode(_sfc_main$j, {
                show: showConfirmDeleteModal.value,
                onCancel: closeModal,
                onConfirm: deleteLesson,
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
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Pages/Admin/School/SchoolLessons/Index.vue");
  return _sfc_setup ? _sfc_setup(props, ctx) : void 0;
};
export {
  _sfc_main as default
};
