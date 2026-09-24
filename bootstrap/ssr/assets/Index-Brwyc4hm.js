import { mergeProps, unref, useSSRContext, ref, watch, withCtx, createVNode, createBlock, openBlock, toDisplayString, createCommentVNode, computed, createTextVNode } from "vue";
import { ssrRenderAttrs, ssrInterpolate, ssrRenderAttr, ssrRenderComponent, ssrRenderClass, ssrIncludeBooleanAttr } from "vue/server-renderer";
import { router } from "@inertiajs/vue3";
import { useI18n } from "vue-i18n";
import { useToast } from "vue-toastification";
import { A as AdminLayout } from "./AdminLayout-03yd3nxC.js";
import { T as TitlePage } from "./TitlePage-CBZZ8YZB.js";
import { _ as _sfc_main$e } from "./SearchInput-xZSYbbms.js";
import { _ as _sfc_main$f, a as _sfc_main$l } from "./ServerSearchInput-CxQUCmzV.js";
import { _ as _sfc_main$c } from "./DefaultButton-DMDyGacf.js";
import { _ as _sfc_main$g, a as _sfc_main$j, b as _sfc_main$k } from "./ItemsPerPageSelect-DOO-E4Z0.js";
import { _ as _sfc_main$i } from "./CountTable-p8tyXGUL.js";
import { _ as _sfc_main$h } from "./ServerItemsPerPageSelect-B_wSkKlU.js";
import { _ as _sfc_main$m } from "./DangerModal-DjIe5VDg.js";
import { _ as _sfc_main$d } from "./ProcessingModeSwitcher-BJvzFf6_.js";
import draggable from "vuedraggable";
import { _ as _sfc_main$a } from "./IconEdit-Bw90OQvk.js";
import { _ as _sfc_main$b } from "./DeleteIconButton-DLv2Mr1x.js";
import { _ as _sfc_main$8 } from "./ActivityToggle-B1-nFMYK.js";
import { _ as _sfc_main$9 } from "./CloneIconButton-BfVfDOWt.js";
import { _ as _sfc_main$5, a as _sfc_main$6, b as _sfc_main$7 } from "./RightToggle-r8SYzaEU.js";
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
      _push(`<div${ssrRenderAttrs(mergeProps({ class: "flex flex-col sm:flex-row items-center" }, _attrs))}><label class="block mb-2 sm:mb-0 sm:mr-2 font-semibold text-sm text-slate-700 dark:text-slate-300">${ssrInterpolate(unref(t)("bulkActions"))}</label><select class="w-auto px-3 py-0.5 form-select rounded-sm shadow-sm bg-white dark:bg-gray-200 text-gray-600 dark:text-gray-900 border border-slate-400 dark:border-slate-600"><option value=""> — ${ssrInterpolate(unref(t)("selectAction"))} — </option><option value="selectAll">${ssrInterpolate(unref(t)("selectAll"))}</option><option value="deselectAll">${ssrInterpolate(unref(t)("deselectAll"))}</option><option disabled>──────────────────</option><option value="activate">${ssrInterpolate(unref(t)("activate"))}</option><option value="deactivate">${ssrInterpolate(unref(t)("deactivate"))}</option><option disabled>──────────────────</option><option value="delete">${ssrInterpolate(unref(t)("deleteSelected"))}</option><option disabled>──────────────────</option></select></div>`);
    };
  }
};
const _sfc_setup$4 = _sfc_main$4.setup;
_sfc_main$4.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Components/Admin/School/SchoolAssignment/Select/BulkActionSelect.vue");
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
      _push(`<div${ssrRenderAttrs(mergeProps({ class: "flex justify-center items-center h-fit my-2" }, _attrs))}><label for="sortParam" class="hidden lg:block sm:mr-2 tracking-wider text-sm font-semibold text-slate-600 dark:text-slate-100">${ssrInterpolate(unref(t)("sort"))}</label><select id="sortParam"${ssrRenderAttr("value", __props.sortParam)} class="w-44 px-3 py-0.5 form-select bg-white dark:bg-gray-200 text-gray-600 dark:text-gray-900 border border-slate-400 dark:border-slate-600 rounded-sm shadow-sm"><option value="idDesc">${ssrInterpolate(unref(t)("idDesc"))}</option><option value="idAsc">${ssrInterpolate(unref(t)("idAsc"))}</option><option disabled>──────────────</option><option value="sortAsc">${ssrInterpolate(unref(t)("sortNumber"))} ↑</option><option value="sortDesc">${ssrInterpolate(unref(t)("sortNumber"))} ↓</option><option disabled>──────────────</option><option value="activityDesc">${ssrInterpolate(unref(t)("activity"))} ON→OFF</option><option value="activityAsc">${ssrInterpolate(unref(t)("activity"))} OFF→ON</option><option value="activity">${ssrInterpolate(unref(t)("active"))}</option><option value="inactive">${ssrInterpolate(unref(t)("inactive"))}</option><option disabled>──────────────</option><option value="titleAsc">${ssrInterpolate(unref(t)("title"))} ↑</option><option value="titleDesc">${ssrInterpolate(unref(t)("title"))} ↓</option><option disabled>──────────────</option><option value="slugAsc">Slug A→Z</option><option value="slugDesc">Slug Z→A</option><option disabled>──────────────</option><option value="left">${ssrInterpolate(unref(t)("inLeft"))}</option><option value="noLeft">${ssrInterpolate(unref(t)("notLeft"))}</option><option disabled>──────────────</option><option value="main">${ssrInterpolate(unref(t)("inMain"))}</option><option value="noMain">${ssrInterpolate(unref(t)("notMain"))}</option><option disabled>──────────────</option><option value="right">${ssrInterpolate(unref(t)("inRight"))}</option><option value="noRight">${ssrInterpolate(unref(t)("notRight"))}</option><option disabled>──────────────</option><option value="statusAsc">${ssrInterpolate(unref(t)("status"))} A→Z</option><option value="statusDesc">${ssrInterpolate(unref(t)("status"))} Z→A</option><option value="visibilityAsc">${ssrInterpolate(unref(t)("visibility"))} A→Z</option><option value="visibilityDesc">${ssrInterpolate(unref(t)("visibility"))} Z→A</option><option disabled>──────────────</option><option value="gradingTypeAsc">${ssrInterpolate(unref(t)("gradingType"))} A→Z</option><option value="gradingTypeDesc">${ssrInterpolate(unref(t)("gradingType"))} Z→A</option><option value="attemptsLimitDesc">${ssrInterpolate(unref(t)("attemptsLimit"))} 9→0</option><option value="attemptsLimitAsc">${ssrInterpolate(unref(t)("attemptsLimit"))} 0→9</option><option disabled>──────────────</option><option value="maxScoreDesc">${ssrInterpolate(unref(t)("maxScore"))} 9→0</option><option value="maxScoreAsc">${ssrInterpolate(unref(t)("maxScore"))} 0→9</option><option value="submissionsDesc">${ssrInterpolate(unref(t)("submissions"))} 9→0</option><option value="submissionsAsc">${ssrInterpolate(unref(t)("submissions"))} 0→9</option><option disabled>──────────────</option><option value="imagesDesc">${ssrInterpolate(unref(t)("images"))} 9→0</option><option value="imagesAsc">${ssrInterpolate(unref(t)("images"))} 0→9</option><option disabled>──────────────</option><option value="courseDesc">${ssrInterpolate(unref(t)("course"))} ID 9→0</option><option value="courseAsc">${ssrInterpolate(unref(t)("course"))} ID 0→9</option><option value="courseTitleAsc">${ssrInterpolate(unref(t)("course"))} A→Z</option><option value="courseTitleDesc">${ssrInterpolate(unref(t)("course"))} Z→A</option><option disabled>──────────────</option><option value="moduleDesc">${ssrInterpolate(unref(t)("module"))} ID 9→0</option><option value="moduleAsc">${ssrInterpolate(unref(t)("module"))} ID 0→9</option><option value="moduleTitleAsc">${ssrInterpolate(unref(t)("module"))} A→Z</option><option value="moduleTitleDesc">${ssrInterpolate(unref(t)("module"))} Z→A</option><option disabled>──────────────</option><option value="lessonDesc">${ssrInterpolate(unref(t)("lesson"))} ID 9→0</option><option value="lessonAsc">${ssrInterpolate(unref(t)("lesson"))} ID 0→9</option><option value="lessonTitleAsc">${ssrInterpolate(unref(t)("lesson"))} A→Z</option><option value="lessonTitleDesc">${ssrInterpolate(unref(t)("lesson"))} Z→A</option><option disabled>──────────────</option><option value="instructorDesc">${ssrInterpolate(unref(t)("instructor"))} ID 9→0</option><option value="instructorAsc">${ssrInterpolate(unref(t)("instructor"))} ID 0→9</option><option value="instructorTitleAsc">${ssrInterpolate(unref(t)("instructor"))} A→Z</option><option value="instructorTitleDesc">${ssrInterpolate(unref(t)("instructor"))} Z→A</option><option disabled>──────────────</option><option value="publishedAtDesc">${ssrInterpolate(unref(t)("publishedAt"))} ↓</option><option value="publishedAtAsc">${ssrInterpolate(unref(t)("publishedAt"))} ↑</option><option value="dueAtDesc">${ssrInterpolate(unref(t)("dueAt"))} ↓</option><option value="dueAtAsc">${ssrInterpolate(unref(t)("dueAt"))} ↑</option><option disabled>──────────────</option><option value="createdAtDesc">${ssrInterpolate(unref(t)("createdAt"))} ↓</option><option value="createdAtAsc">${ssrInterpolate(unref(t)("createdAt"))} ↑</option><option value="updatedAtDesc">${ssrInterpolate(unref(t)("updatedAt"))} ↓</option><option value="updatedAtAsc">${ssrInterpolate(unref(t)("updatedAt"))} ↑</option><option disabled>──────────────</option></select></div>`);
    };
  }
};
const _sfc_setup$3 = _sfc_main$3.setup;
_sfc_main$3.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Components/Admin/School/SchoolAssignment/Sort/SortSelect.vue");
  return _sfc_setup$3 ? _sfc_setup$3(props, ctx) : void 0;
};
const _sfc_main$2 = {
  __name: "AssignmentTable",
  __ssrInlineRender: true,
  props: {
    assignments: { type: Array, default: () => [] },
    selectedAssignments: { type: Array, default: () => [] }
  },
  emits: [
    "toggle-left",
    "toggle-main",
    "toggle-right",
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
    const localAssignments = ref([]);
    watch(
      () => props.assignments,
      (assignments) => {
        localAssignments.value = JSON.parse(
          JSON.stringify(assignments || [])
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
        localAssignments.value.map(
          (assignment) => assignment.id
        )
      );
    };
    const getAssignmentTitle = (assignment) => {
      var _a;
      return ((_a = assignment == null ? void 0 : assignment.translation) == null ? void 0 : _a.title) || `ID: ${assignment == null ? void 0 : assignment.id}`;
    };
    const getAssignmentSubtitle = (assignment) => {
      var _a;
      return ((_a = assignment == null ? void 0 : assignment.translation) == null ? void 0 : _a.subtitle) || "";
    };
    const getAssignmentShort = (assignment) => {
      var _a;
      return ((_a = assignment == null ? void 0 : assignment.translation) == null ? void 0 : _a.short) || "";
    };
    const getCourseLabel = (assignment) => {
      var _a, _b, _c;
      return ((_b = (_a = assignment == null ? void 0 : assignment.course) == null ? void 0 : _a.translation) == null ? void 0 : _b.title) || (((_c = assignment == null ? void 0 : assignment.course) == null ? void 0 : _c.id) ? `ID: ${assignment.course.id}` : "");
    };
    const getModuleLabel = (assignment) => {
      var _a, _b, _c;
      return ((_b = (_a = assignment == null ? void 0 : assignment.module) == null ? void 0 : _a.translation) == null ? void 0 : _b.title) || (((_c = assignment == null ? void 0 : assignment.module) == null ? void 0 : _c.id) ? `ID: ${assignment.module.id}` : "");
    };
    const getLessonLabel = (assignment) => {
      var _a, _b, _c;
      return ((_b = (_a = assignment == null ? void 0 : assignment.lesson) == null ? void 0 : _a.translation) == null ? void 0 : _b.title) || (((_c = assignment == null ? void 0 : assignment.lesson) == null ? void 0 : _c.id) ? `ID: ${assignment.lesson.id}` : "");
    };
    const getInstructorLabel = (assignment) => {
      var _a, _b, _c;
      const instructor = assignment == null ? void 0 : assignment.instructor;
      if (!instructor) {
        return "";
      }
      const title = ((_a = instructor == null ? void 0 : instructor.translation) == null ? void 0 : _a.title) || "";
      const userName = ((_b = instructor == null ? void 0 : instructor.user) == null ? void 0 : _b.name) || ((_c = instructor == null ? void 0 : instructor.user) == null ? void 0 : _c.email) || "";
      if (title && userName && title !== userName) {
        return `${title}: ${userName}`;
      }
      return title || userName || ((instructor == null ? void 0 : instructor.id) ? `ID: ${instructor.id}` : "");
    };
    const getPrimaryImage = (assignment) => {
      if (assignment == null ? void 0 : assignment.primary_image) {
        return assignment.primary_image;
      }
      if (Array.isArray(assignment == null ? void 0 : assignment.images) && assignment.images.length) {
        return [...assignment.images].sort(
          (a, b) => Number((a == null ? void 0 : a.order) ?? 0) - Number((b == null ? void 0 : b.order) ?? 0)
        )[0];
      }
      return null;
    };
    const getImageUrl = (image) => {
      return (image == null ? void 0 : image.thumb_url) || (image == null ? void 0 : image.webp_url) || (image == null ? void 0 : image.image_url) || (image == null ? void 0 : image.url) || "";
    };
    const assignmentStatusLabelKeyMap = {
      draft: "statusDraft",
      published: "statusPublished",
      archived: "statusArchived"
    };
    const assignmentVisibilityLabelKeyMap = {
      public: "assignmentGeneral",
      enrolled: "assignmentTest",
      private: "assignmentThematic"
    };
    const assignmentGradingTypeLabelKeyMap = {
      manual: "gradingManual",
      auto: "gradingAuto"
    };
    const getAssignmentStatusLabel = (status) => {
      return t(
        assignmentStatusLabelKeyMap[status] || status || "no"
      );
    };
    const getAssignmentVisibilityLabel = (visibility) => {
      return t(
        assignmentVisibilityLabelKeyMap[visibility] || visibility || "no"
      );
    };
    const getAssignmentGradingTypeLabel = (gradingType) => {
      return t(
        assignmentGradingTypeLabelKeyMap[gradingType] || gradingType || "no"
      );
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
      _push(`<div${ssrRenderAttrs(mergeProps({ class: "relative rounded-sm border border-slate-200 bg-white shadow-lg dark:border-slate-600 dark:bg-slate-700" }, _attrs))}><div class="flex items-center justify-between border-b border-slate-400 px-3 py-2 dark:border-slate-500"><div class="text-xs text-slate-600 dark:text-slate-200">${ssrInterpolate(unref(t)("selected"))}: ${ssrInterpolate(__props.selectedAssignments.length)}</div>`);
      if (localAssignments.value.length) {
        _push(`<label class="flex cursor-pointer items-center text-xs text-slate-600 dark:text-slate-200"><span>${ssrInterpolate(unref(t)("selectAll"))}</span><input type="checkbox" class="mx-2"></label>`);
      } else {
        _push(`<!---->`);
      }
      _push(`</div><div class="overflow-x-auto">`);
      if (localAssignments.value.length) {
        _push(`<table class="table-auto w-full text-slate-700 dark:text-slate-100"><thead class="border border-solid border-gray-300 bg-slate-200 text-sm uppercase dark:border-gray-700 dark:bg-cyan-900"><tr><th class="w-px px-2 py-3"><svg class="h-4 w-4 fill-current text-slate-800 dark:text-slate-200" viewBox="0 0 24 24"><path d="M12.707,2.293a1,1,0,0,0-1.414,0l-5,5A1,1,0,0,0,7.707,8.707L12,4.414l4.293,4.293a1,1,0,0,0,1.414-1.414Z"></path><path d="M16.293,15.293,12,19.586,7.707,15.293a1,1,0,0,0-1.414,1.414l5,5a1,1,0,0,0,1.414,0l5-5a1,1,0,0,0-1.414-1.414Z"></path></svg></th><th class="w-px px-2 py-3"><div class="text-center font-medium">${ssrInterpolate(unref(t)("id"))}</div></th><th class="px-2 py-3 whitespace-nowrap"><div class="flex justify-center"${ssrRenderAttr("title", unref(t)("image"))}><svg class="h-6 w-6 shrink-0 fill-current" viewBox="0 0 512 512"><path d="M0 96C0 60.7 28.7 32 64 32l384 0c35.3 0 64 28.7 64 64l0 320c0 35.3-28.7 64-64 64L64 480c-35.3 0-64-28.7-64-64L0 96zM323.8 202.5c-4.5-6.6-11.9-10.5-19.8-10.5s-15.4 3.9-19.8 10.5l-87 127.6L170.7 297c-4.6-5.7-11.5-9-18.7-9s-14.2 3.3-18.7 9l-64 80c-5.8 7.2-6.9 17.1-2.9 25.4s12.4 13.6 21.6 13.6l96 0 32 0 208 0c8.9 0 17.1-4.9 21.2-12.8s3.6-17.4-1.4-24.7l-120-176zM112 192a48 48 0 1 0 0-96 48 48 0 1 0 0 96z"></path></svg></div></th><th class="px-2 py-3 whitespace-nowrap"><div class="text-left font-semibold">${ssrInterpolate(unref(t)("assignment"))}</div></th><th class="px-2 py-3 whitespace-nowrap"><div class="text-center font-semibold">${ssrInterpolate(unref(t)("status"))}</div></th><th class="px-2 py-3 whitespace-nowrap"><div class="text-center font-semibold">${ssrInterpolate(unref(t)("points"))}</div></th><th class="px-2 py-3 whitespace-nowrap"><div class="text-center font-semibold">${ssrInterpolate(unref(t)("show"))}</div></th><th class="px-2 py-3 whitespace-nowrap"><div class="text-end font-semibold">${ssrInterpolate(unref(t)("actions"))}</div></th><th class="px-2 py-3 whitespace-nowrap"><div class="text-center"><input type="checkbox"></div></th></tr></thead>`);
        _push(ssrRenderComponent(unref(draggable), {
          modelValue: localAssignments.value,
          "onUpdate:modelValue": ($event) => localAssignments.value = $event,
          tag: "tbody",
          "item-key": "id",
          handle: ".handle",
          onEnd: handleDragEnd
        }, {
          item: withCtx(({ element: assignment }, _push2, _parent2, _scopeId) => {
            var _a, _b, _c, _d;
            if (_push2) {
              _push2(`<tr class="border-b-2 text-sm font-semibold hover:bg-slate-100 dark:hover:bg-cyan-800"${_scopeId}><td class="handle cursor-move px-2 py-1 text-center"${_scopeId}><svg class="h-4 w-4 text-gray-500 dark:text-gray-300" fill="currentColor" viewBox="0 0 20 20"${_scopeId}><path d="M7 4h2v2H7V4zm4 0h2v2h-2V4zM7 8h2v2H7V8zm4 0h2v2h-2V8zM7 12h2v2H7v-2zm4 0h2v2h-2v-2z"${_scopeId}></path></svg></td><td class="px-2 py-3 whitespace-nowrap"${_scopeId}><div class="text-center text-xs text-slate-800 dark:text-blue-200"${ssrRenderAttr("title", `[sort: ${assignment.sort}] ${formatDate(assignment.published_at)}`)}${_scopeId}>${ssrInterpolate(assignment.id)}</div></td><td class="px-2 py-3 whitespace-nowrap"${_scopeId}><div class="flex justify-center"${_scopeId}>`);
              if (getPrimaryImage(assignment)) {
                _push2(`<img${ssrRenderAttr("src", getImageUrl(getPrimaryImage(assignment)))}${ssrRenderAttr("alt", ((_a = getPrimaryImage(assignment)) == null ? void 0 : _a.alt) || unref(t)("defaultImageAlt"))}${ssrRenderAttr("title", ((_b = getPrimaryImage(assignment)) == null ? void 0 : _b.caption) || unref(t)("assignmentImage"))} class="h-8 w-12 rounded-sm object-cover"${_scopeId}>`);
              } else {
                _push2(`<img src="/storage/school/school_assignment_images/default-image.png"${ssrRenderAttr("alt", unref(t)("defaultImageTitle"))} class="h-8 w-12 rounded-sm object-cover"${_scopeId}>`);
              }
              _push2(`</div></td><td class="px-2 py-3"${_scopeId}><a${ssrRenderAttr("href", `/school/assignments/${encodeURIComponent(assignment.slug)}`)} class="text-xs text-sky-600 hover:underline dark:text-sky-200" target="_blank" rel="noopener noreferrer"${ssrRenderAttr("title", getAssignmentSubtitle(assignment) || getAssignmentShort(assignment))}${_scopeId}>${ssrInterpolate(getAssignmentTitle(assignment))}</a><div class="text-xs text-slate-600 dark:text-slate-300"${ssrRenderAttr("title", getLessonLabel(assignment))}${_scopeId}>${ssrInterpolate(unref(t)("lesson"))}: ${ssrInterpolate(getLessonLabel(assignment) || "—")}</div><div class="text-xs text-slate-600 dark:text-slate-300"${ssrRenderAttr("title", getModuleLabel(assignment))}${_scopeId}>${ssrInterpolate(unref(t)("module"))}: ${ssrInterpolate(getModuleLabel(assignment) || "—")}</div><div class="text-xs text-slate-600 dark:text-slate-300"${ssrRenderAttr("title", getCourseLabel(assignment))}${_scopeId}>${ssrInterpolate(unref(t)("course"))}: ${ssrInterpolate(getCourseLabel(assignment) || "—")}</div><div class="text-xs text-teal-600 dark:text-teal-300"${ssrRenderAttr("title", getInstructorLabel(assignment))}${_scopeId}>${ssrInterpolate(unref(t)("instructor"))}: ${ssrInterpolate(getInstructorLabel(assignment) || "—")}</div></td><td class="px-2 py-3 whitespace-nowrap"${_scopeId}><div class="text-center text-xs text-fuchsia-800 dark:text-fuchsia-400"${_scopeId}>${ssrInterpolate(getAssignmentStatusLabel(assignment.status))}</div><div class="text-center text-xs text-amber-600 dark:text-yellow-200"${_scopeId}>${ssrInterpolate(getAssignmentVisibilityLabel(assignment.visibility))}</div></td><td class="px-2 py-3 whitespace-nowrap"${_scopeId}><div class="text-center text-xs text-violet-700 dark:text-violet-300"${ssrRenderAttr("title", unref(t)("maxScore"))}${_scopeId}>${ssrInterpolate(assignment.max_score ?? 0)}</div>`);
              if (assignment.due_at) {
                _push2(`<div class="${ssrRenderClass([
                  assignment.is_overdue ? "text-red-500" : "text-slate-500 dark:text-slate-300",
                  "text-center text-[10px]"
                ])}"${_scopeId}>${ssrInterpolate(unref(t)("dueAt"))}: ${ssrInterpolate(formatDate(assignment.due_at))}</div>`);
              } else {
                _push2(`<!---->`);
              }
              _push2(`<div class="text-center text-[10px] text-slate-500 dark:text-slate-300"${_scopeId}>${ssrInterpolate(unref(t)("quizAttemptItems"))}: ${ssrInterpolate(assignment.attempts_limit ?? 0)}</div><div class="text-center text-[10px] text-slate-500 dark:text-slate-300"${_scopeId}>${ssrInterpolate(getAssignmentGradingTypeLabel(assignment.grading_type))}</div></td><td class="px-2 py-3 whitespace-nowrap"${_scopeId}><div class="flex justify-center gap-2"${_scopeId}>`);
              _push2(ssrRenderComponent(_sfc_main$5, {
                isActive: assignment.left,
                title: assignment.left ? unref(t)("enabled") : unref(t)("disabled"),
                onToggleLeft: ($event) => emit("toggle-left", assignment)
              }, null, _parent2, _scopeId));
              _push2(ssrRenderComponent(_sfc_main$6, {
                isActive: assignment.main,
                title: assignment.main ? unref(t)("enabled") : unref(t)("disabled"),
                onToggleMain: ($event) => emit("toggle-main", assignment)
              }, null, _parent2, _scopeId));
              _push2(ssrRenderComponent(_sfc_main$7, {
                isActive: assignment.right,
                title: assignment.right ? unref(t)("enabled") : unref(t)("disabled"),
                onToggleRight: ($event) => emit("toggle-right", assignment)
              }, null, _parent2, _scopeId));
              _push2(`</div></td><td class="px-2 py-3 whitespace-nowrap"${_scopeId}><div class="flex justify-end gap-2"${_scopeId}>`);
              _push2(ssrRenderComponent(_sfc_main$8, {
                isActive: assignment.activity,
                title: assignment.activity ? unref(t)("enabled") : unref(t)("disabled"),
                onToggleActivity: ($event) => emit("toggle-activity", assignment)
              }, null, _parent2, _scopeId));
              _push2(ssrRenderComponent(_sfc_main$9, {
                title: unref(t)("clone"),
                onClone: ($event) => emit("clone", assignment)
              }, null, _parent2, _scopeId));
              _push2(ssrRenderComponent(_sfc_main$a, {
                href: _ctx.route("admin.schoolAssignments.edit", {
                  schoolAssignment: assignment.id
                })
              }, null, _parent2, _scopeId));
              _push2(ssrRenderComponent(_sfc_main$b, {
                onDelete: ($event) => emit("delete", assignment)
              }, null, _parent2, _scopeId));
              _push2(`</div></td><td class="px-2 py-3 whitespace-nowrap"${_scopeId}><div class="text-center"${_scopeId}><input type="checkbox"${ssrIncludeBooleanAttr(__props.selectedAssignments.includes(assignment.id)) ? " checked" : ""}${_scopeId}></div></td></tr>`);
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
                      title: `[sort: ${assignment.sort}] ${formatDate(assignment.published_at)}`
                    }, toDisplayString(assignment.id), 9, ["title"])
                  ]),
                  createVNode("td", { class: "px-2 py-3 whitespace-nowrap" }, [
                    createVNode("div", { class: "flex justify-center" }, [
                      getPrimaryImage(assignment) ? (openBlock(), createBlock("img", {
                        key: 0,
                        src: getImageUrl(getPrimaryImage(assignment)),
                        alt: ((_c = getPrimaryImage(assignment)) == null ? void 0 : _c.alt) || unref(t)("defaultImageAlt"),
                        title: ((_d = getPrimaryImage(assignment)) == null ? void 0 : _d.caption) || unref(t)("assignmentImage"),
                        class: "h-8 w-12 rounded-sm object-cover"
                      }, null, 8, ["src", "alt", "title"])) : (openBlock(), createBlock("img", {
                        key: 1,
                        src: "/storage/school/school_assignment_images/default-image.png",
                        alt: unref(t)("defaultImageTitle"),
                        class: "h-8 w-12 rounded-sm object-cover"
                      }, null, 8, ["alt"]))
                    ])
                  ]),
                  createVNode("td", { class: "px-2 py-3" }, [
                    createVNode("a", {
                      href: `/school/assignments/${encodeURIComponent(assignment.slug)}`,
                      class: "text-xs text-sky-600 hover:underline dark:text-sky-200",
                      target: "_blank",
                      rel: "noopener noreferrer",
                      title: getAssignmentSubtitle(assignment) || getAssignmentShort(assignment)
                    }, toDisplayString(getAssignmentTitle(assignment)), 9, ["href", "title"]),
                    createVNode("div", {
                      class: "text-xs text-slate-600 dark:text-slate-300",
                      title: getLessonLabel(assignment)
                    }, toDisplayString(unref(t)("lesson")) + ": " + toDisplayString(getLessonLabel(assignment) || "—"), 9, ["title"]),
                    createVNode("div", {
                      class: "text-xs text-slate-600 dark:text-slate-300",
                      title: getModuleLabel(assignment)
                    }, toDisplayString(unref(t)("module")) + ": " + toDisplayString(getModuleLabel(assignment) || "—"), 9, ["title"]),
                    createVNode("div", {
                      class: "text-xs text-slate-600 dark:text-slate-300",
                      title: getCourseLabel(assignment)
                    }, toDisplayString(unref(t)("course")) + ": " + toDisplayString(getCourseLabel(assignment) || "—"), 9, ["title"]),
                    createVNode("div", {
                      class: "text-xs text-teal-600 dark:text-teal-300",
                      title: getInstructorLabel(assignment)
                    }, toDisplayString(unref(t)("instructor")) + ": " + toDisplayString(getInstructorLabel(assignment) || "—"), 9, ["title"])
                  ]),
                  createVNode("td", { class: "px-2 py-3 whitespace-nowrap" }, [
                    createVNode("div", { class: "text-center text-xs text-fuchsia-800 dark:text-fuchsia-400" }, toDisplayString(getAssignmentStatusLabel(assignment.status)), 1),
                    createVNode("div", { class: "text-center text-xs text-amber-600 dark:text-yellow-200" }, toDisplayString(getAssignmentVisibilityLabel(assignment.visibility)), 1)
                  ]),
                  createVNode("td", { class: "px-2 py-3 whitespace-nowrap" }, [
                    createVNode("div", {
                      class: "text-center text-xs text-violet-700 dark:text-violet-300",
                      title: unref(t)("maxScore")
                    }, toDisplayString(assignment.max_score ?? 0), 9, ["title"]),
                    assignment.due_at ? (openBlock(), createBlock("div", {
                      key: 0,
                      class: [
                        "text-center text-[10px]",
                        assignment.is_overdue ? "text-red-500" : "text-slate-500 dark:text-slate-300"
                      ]
                    }, toDisplayString(unref(t)("dueAt")) + ": " + toDisplayString(formatDate(assignment.due_at)), 3)) : createCommentVNode("", true),
                    createVNode("div", { class: "text-center text-[10px] text-slate-500 dark:text-slate-300" }, toDisplayString(unref(t)("quizAttemptItems")) + ": " + toDisplayString(assignment.attempts_limit ?? 0), 1),
                    createVNode("div", { class: "text-center text-[10px] text-slate-500 dark:text-slate-300" }, toDisplayString(getAssignmentGradingTypeLabel(assignment.grading_type)), 1)
                  ]),
                  createVNode("td", { class: "px-2 py-3 whitespace-nowrap" }, [
                    createVNode("div", { class: "flex justify-center gap-2" }, [
                      createVNode(_sfc_main$5, {
                        isActive: assignment.left,
                        title: assignment.left ? unref(t)("enabled") : unref(t)("disabled"),
                        onToggleLeft: ($event) => emit("toggle-left", assignment)
                      }, null, 8, ["isActive", "title", "onToggleLeft"]),
                      createVNode(_sfc_main$6, {
                        isActive: assignment.main,
                        title: assignment.main ? unref(t)("enabled") : unref(t)("disabled"),
                        onToggleMain: ($event) => emit("toggle-main", assignment)
                      }, null, 8, ["isActive", "title", "onToggleMain"]),
                      createVNode(_sfc_main$7, {
                        isActive: assignment.right,
                        title: assignment.right ? unref(t)("enabled") : unref(t)("disabled"),
                        onToggleRight: ($event) => emit("toggle-right", assignment)
                      }, null, 8, ["isActive", "title", "onToggleRight"])
                    ])
                  ]),
                  createVNode("td", { class: "px-2 py-3 whitespace-nowrap" }, [
                    createVNode("div", { class: "flex justify-end gap-2" }, [
                      createVNode(_sfc_main$8, {
                        isActive: assignment.activity,
                        title: assignment.activity ? unref(t)("enabled") : unref(t)("disabled"),
                        onToggleActivity: ($event) => emit("toggle-activity", assignment)
                      }, null, 8, ["isActive", "title", "onToggleActivity"]),
                      createVNode(_sfc_main$9, {
                        title: unref(t)("clone"),
                        onClone: ($event) => emit("clone", assignment)
                      }, null, 8, ["title", "onClone"]),
                      createVNode(_sfc_main$a, {
                        href: _ctx.route("admin.schoolAssignments.edit", {
                          schoolAssignment: assignment.id
                        })
                      }, null, 8, ["href"]),
                      createVNode(_sfc_main$b, {
                        onDelete: ($event) => emit("delete", assignment)
                      }, null, 8, ["onDelete"])
                    ])
                  ]),
                  createVNode("td", { class: "px-2 py-3 whitespace-nowrap" }, [
                    createVNode("div", { class: "text-center" }, [
                      createVNode("input", {
                        type: "checkbox",
                        checked: __props.selectedAssignments.includes(assignment.id),
                        onChange: ($event) => emit("toggle-select", assignment.id)
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
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Components/Admin/School/SchoolAssignment/Table/AssignmentTable.vue");
  return _sfc_setup$2 ? _sfc_setup$2(props, ctx) : void 0;
};
const _sfc_main$1 = {
  __name: "AssignmentCardGrid",
  __ssrInlineRender: true,
  props: {
    assignments: { type: Array, default: () => [] },
    selectedAssignments: { type: Array, default: () => [] }
  },
  emits: [
    "toggle-left",
    "toggle-main",
    "toggle-right",
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
    const localAssignments = ref([]);
    watch(
      () => props.assignments,
      (assignments) => {
        localAssignments.value = JSON.parse(
          JSON.stringify(assignments || [])
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
        localAssignments.value.map(
          (assignment) => assignment.id
        )
      );
    };
    const getAssignmentTitle = (assignment) => {
      var _a;
      return ((_a = assignment == null ? void 0 : assignment.translation) == null ? void 0 : _a.title) || `ID: ${assignment == null ? void 0 : assignment.id}`;
    };
    const getAssignmentSubtitle = (assignment) => {
      var _a;
      return ((_a = assignment == null ? void 0 : assignment.translation) == null ? void 0 : _a.subtitle) || "";
    };
    const getAssignmentShort = (assignment) => {
      var _a;
      return ((_a = assignment == null ? void 0 : assignment.translation) == null ? void 0 : _a.short) || "";
    };
    const getCourseLabel = (assignment) => {
      var _a, _b, _c;
      return ((_b = (_a = assignment == null ? void 0 : assignment.course) == null ? void 0 : _a.translation) == null ? void 0 : _b.title) || (((_c = assignment == null ? void 0 : assignment.course) == null ? void 0 : _c.id) ? `ID: ${assignment.course.id}` : "");
    };
    const getModuleLabel = (assignment) => {
      var _a, _b, _c;
      return ((_b = (_a = assignment == null ? void 0 : assignment.module) == null ? void 0 : _a.translation) == null ? void 0 : _b.title) || (((_c = assignment == null ? void 0 : assignment.module) == null ? void 0 : _c.id) ? `ID: ${assignment.module.id}` : "");
    };
    const getLessonLabel = (assignment) => {
      var _a, _b, _c;
      return ((_b = (_a = assignment == null ? void 0 : assignment.lesson) == null ? void 0 : _a.translation) == null ? void 0 : _b.title) || (((_c = assignment == null ? void 0 : assignment.lesson) == null ? void 0 : _c.id) ? `ID: ${assignment.lesson.id}` : "");
    };
    const getInstructorLabel = (assignment) => {
      var _a, _b, _c;
      const instructor = assignment == null ? void 0 : assignment.instructor;
      if (!instructor) {
        return "";
      }
      const title = ((_a = instructor == null ? void 0 : instructor.translation) == null ? void 0 : _a.title) || "";
      const userName = ((_b = instructor == null ? void 0 : instructor.user) == null ? void 0 : _b.name) || ((_c = instructor == null ? void 0 : instructor.user) == null ? void 0 : _c.email) || "";
      if (title && userName && title !== userName) {
        return `${title}: ${userName}`;
      }
      return title || userName || ((instructor == null ? void 0 : instructor.id) ? `ID: ${instructor.id}` : "");
    };
    const getPrimaryImage = (assignment) => {
      if (assignment == null ? void 0 : assignment.primary_image) {
        return assignment.primary_image;
      }
      if (Array.isArray(assignment == null ? void 0 : assignment.images) && assignment.images.length) {
        return [...assignment.images].sort(
          (a, b) => Number((a == null ? void 0 : a.order) ?? 0) - Number((b == null ? void 0 : b.order) ?? 0)
        )[0];
      }
      return null;
    };
    const getImageUrl = (image) => {
      return (image == null ? void 0 : image.thumb_url) || (image == null ? void 0 : image.webp_url) || (image == null ? void 0 : image.image_url) || (image == null ? void 0 : image.url) || "";
    };
    const assignmentStatusLabelKeyMap = {
      draft: "statusDraft",
      published: "statusPublished",
      archived: "statusArchived"
    };
    const assignmentVisibilityLabelKeyMap = {
      public: "assignmentGeneral",
      enrolled: "assignmentTest",
      private: "assignmentThematic"
    };
    const assignmentGradingTypeLabelKeyMap = {
      manual: "gradingManual",
      auto: "gradingAuto"
    };
    const getAssignmentStatusLabel = (status) => {
      return t(
        assignmentStatusLabelKeyMap[status] || status || "no"
      );
    };
    const getAssignmentVisibilityLabel = (visibility) => {
      return t(
        assignmentVisibilityLabelKeyMap[visibility] || visibility || "no"
      );
    };
    const getAssignmentGradingTypeLabel = (gradingType) => {
      return t(
        assignmentGradingTypeLabelKeyMap[gradingType] || gradingType || "no"
      );
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
      _push(`<div${ssrRenderAttrs(mergeProps({ class: "relative rounded-sm border border-slate-400 bg-white shadow-lg dark:border-slate-500 dark:bg-slate-700" }, _attrs))}><div class="flex items-center justify-between border-b border-slate-400 px-3 py-2 dark:border-slate-500"><div class="text-xs text-slate-600 dark:text-slate-200">${ssrInterpolate(unref(t)("selected"))}: ${ssrInterpolate(__props.selectedAssignments.length)}</div>`);
      if (localAssignments.value.length) {
        _push(`<label class="flex cursor-pointer items-center text-xs text-slate-600 dark:text-slate-200"><span>${ssrInterpolate(unref(t)("selectAll"))}</span><input type="checkbox" class="mx-2"></label>`);
      } else {
        _push(`<!---->`);
      }
      _push(`</div>`);
      if (localAssignments.value.length) {
        _push(`<div class="p-3">`);
        _push(ssrRenderComponent(unref(draggable), {
          modelValue: localAssignments.value,
          "onUpdate:modelValue": ($event) => localAssignments.value = $event,
          tag: "div",
          "item-key": "id",
          handle: ".handle",
          class: "grid grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4",
          onEnd: handleDragEnd
        }, {
          item: withCtx(({ element: assignment }, _push2, _parent2, _scopeId) => {
            var _a, _b, _c, _d;
            if (_push2) {
              _push2(`<div class="relative flex h-full flex-col rounded-md border border-slate-400 bg-slate-50/70 shadow-sm transition-shadow duration-150 hover:shadow-md dark:border-slate-500 dark:bg-slate-800/80"${_scopeId}><div class="flex items-center justify-between border-b border-dashed border-slate-400 px-2 py-1 dark:border-slate-500"${_scopeId}><div class="flex items-center gap-2"${_scopeId}><button type="button" class="handle text-slate-400 hover:text-slate-700 dark:hover:text-slate-100"${ssrRenderAttr("title", unref(t)("dragDrop"))}${_scopeId}><svg class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20"${_scopeId}><path d="M7 4h2v2H7V4zm4 0h2v2h-2V4zM7 8h2v2H7V8zm4 0h2v2h-2V8zM7 12h2v2H7v-2zm4 0h2v2h-2v-2z"${_scopeId}></path></svg></button><div class="rounded-sm border border-gray-400 bg-slate-200 px-1.5 py-0.5 text-[10px] font-semibold text-slate-800 dark:bg-slate-700 dark:text-blue-100"${ssrRenderAttr("title", `[sort: ${assignment.sort}] ${formatDate(assignment.published_at)}`)}${_scopeId}> ID: ${ssrInterpolate(assignment.id)}</div></div><div class="flex items-center gap-2"${_scopeId}><span class="rounded-sm border border-gray-400 bg-fuchsia-100 px-1.5 py-0.5 text-[10px] text-fuchsia-800 dark:bg-fuchsia-900/50 dark:text-fuchsia-400"${ssrRenderAttr("title", `${unref(t)("status")}: ${getAssignmentStatusLabel(assignment.status)} / ${getAssignmentVisibilityLabel(assignment.visibility)}`)}${_scopeId}>${ssrInterpolate(getAssignmentStatusLabel(assignment.status))}</span><input type="checkbox"${ssrIncludeBooleanAttr(__props.selectedAssignments.includes(assignment.id)) ? " checked" : ""}${_scopeId}></div></div><div class="relative h-32 w-full bg-slate-200 dark:bg-slate-900"${_scopeId}>`);
              if (getPrimaryImage(assignment)) {
                _push2(`<img${ssrRenderAttr("src", getImageUrl(getPrimaryImage(assignment)))}${ssrRenderAttr("alt", ((_a = getPrimaryImage(assignment)) == null ? void 0 : _a.alt) || unref(t)("defaultImageAlt"))}${ssrRenderAttr("title", ((_b = getPrimaryImage(assignment)) == null ? void 0 : _b.caption) || unref(t)("assignmentImage"))} class="h-full w-full object-cover"${_scopeId}>`);
              } else {
                _push2(`<img src="/storage/school/school_assignment_images/default-image.png"${ssrRenderAttr("alt", unref(t)("defaultImageTitle"))} class="h-full w-full object-cover"${_scopeId}>`);
              }
              _push2(`</div><div class="flex flex-1 flex-col space-y-1 px-3 py-2"${_scopeId}><a${ssrRenderAttr("href", `/school/assignments/${encodeURIComponent(assignment.slug)}`)} class="line-clamp-2 text-center text-sm font-semibold text-sky-700 hover:underline dark:text-sky-200" target="_blank" rel="noopener noreferrer"${ssrRenderAttr("title", getAssignmentSubtitle(assignment) || getAssignmentShort(assignment))}${_scopeId}>${ssrInterpolate(getAssignmentTitle(assignment))}</a><div class="truncate text-center text-[10px] text-slate-600 dark:text-slate-300"${ssrRenderAttr("title", assignment.slug)}${_scopeId}>${ssrInterpolate(assignment.slug)}</div><div class="text-xs font-semibold text-slate-600 dark:text-slate-300"${ssrRenderAttr("title", getLessonLabel(assignment))}${_scopeId}>${ssrInterpolate(unref(t)("lesson"))}: ${ssrInterpolate(getLessonLabel(assignment) || "—")}</div><div class="text-xs font-semibold text-slate-600 dark:text-slate-300"${ssrRenderAttr("title", getModuleLabel(assignment))}${_scopeId}>${ssrInterpolate(unref(t)("module"))}: ${ssrInterpolate(getModuleLabel(assignment) || "—")}</div><div class="text-xs font-semibold text-slate-600 dark:text-slate-300"${ssrRenderAttr("title", getCourseLabel(assignment))}${_scopeId}>${ssrInterpolate(unref(t)("course"))}: ${ssrInterpolate(getCourseLabel(assignment) || "—")}</div><div class="text-center text-xs font-semibold text-teal-600 dark:text-teal-300"${ssrRenderAttr("title", getInstructorLabel(assignment))}${_scopeId}>${ssrInterpolate(unref(t)("instructor"))}: ${ssrInterpolate(getInstructorLabel(assignment) || "—")}</div><div class="mt-1 flex flex-wrap justify-center gap-1 text-[10px] font-semibold"${_scopeId}><span class="rounded-sm border border-gray-400 bg-amber-50 px-2 py-0.5 text-amber-600 dark:bg-yellow-950/50 dark:text-yellow-200"${_scopeId}>${ssrInterpolate(getAssignmentVisibilityLabel(assignment.visibility))}</span><span class="rounded-sm border border-gray-400 bg-emerald-100 px-2 py-0.5 text-emerald-700 dark:bg-emerald-900 dark:text-emerald-200"${_scopeId}>${ssrInterpolate(getAssignmentGradingTypeLabel(assignment.grading_type))}</span></div><div class="mt-2 flex flex-col justify-center text-center text-[11px] text-gray-700 dark:text-gray-400"${_scopeId}><div${_scopeId}>${ssrInterpolate(unref(t)("maxScore"))}: ${ssrInterpolate(assignment.max_score ?? 0)}</div><div${_scopeId}>${ssrInterpolate(unref(t)("quizAttemptItems"))}: ${ssrInterpolate(assignment.attempts_limit ?? 0)}</div>`);
              if (assignment.due_at) {
                _push2(`<div class="${ssrRenderClass(assignment.is_overdue ? "text-red-500" : "")}"${_scopeId}>${ssrInterpolate(unref(t)("dueAt"))}: ${ssrInterpolate(formatDate(assignment.due_at))}</div>`);
              } else {
                _push2(`<!---->`);
              }
              _push2(`<div${_scopeId}>${ssrInterpolate(unref(t)("submissions"))}: ${ssrInterpolate(assignment.submissions_count ?? 0)}</div><div${_scopeId}>${ssrInterpolate(unref(t)("images"))}: ${ssrInterpolate(assignment.images_count ?? 0)}</div></div></div><div class="flex items-center justify-between border-t border-dashed border-slate-400 px-3 py-2 dark:border-slate-500"${_scopeId}><div class="flex items-center gap-1"${_scopeId}>`);
              _push2(ssrRenderComponent(_sfc_main$5, {
                isActive: assignment.left,
                title: assignment.left ? unref(t)("enabled") : unref(t)("disabled"),
                onToggleLeft: ($event) => emit("toggle-left", assignment)
              }, null, _parent2, _scopeId));
              _push2(ssrRenderComponent(_sfc_main$6, {
                isActive: assignment.main,
                title: assignment.main ? unref(t)("enabled") : unref(t)("disabled"),
                onToggleMain: ($event) => emit("toggle-main", assignment)
              }, null, _parent2, _scopeId));
              _push2(ssrRenderComponent(_sfc_main$7, {
                isActive: assignment.right,
                title: assignment.right ? unref(t)("enabled") : unref(t)("disabled"),
                onToggleRight: ($event) => emit("toggle-right", assignment)
              }, null, _parent2, _scopeId));
              _push2(`</div><div class="flex items-center gap-1"${_scopeId}>`);
              _push2(ssrRenderComponent(_sfc_main$8, {
                isActive: assignment.activity,
                title: assignment.activity ? unref(t)("enabled") : unref(t)("disabled"),
                onToggleActivity: ($event) => emit("toggle-activity", assignment)
              }, null, _parent2, _scopeId));
              _push2(ssrRenderComponent(_sfc_main$9, {
                title: unref(t)("clone"),
                onClone: ($event) => emit("clone", assignment)
              }, null, _parent2, _scopeId));
              _push2(ssrRenderComponent(_sfc_main$a, {
                href: _ctx.route("admin.schoolAssignments.edit", {
                  schoolAssignment: assignment.id
                })
              }, null, _parent2, _scopeId));
              _push2(ssrRenderComponent(_sfc_main$b, {
                onDelete: ($event) => emit("delete", assignment)
              }, null, _parent2, _scopeId));
              _push2(`</div></div></div>`);
            } else {
              return [
                createVNode("div", { class: "relative flex h-full flex-col rounded-md border border-slate-400 bg-slate-50/70 shadow-sm transition-shadow duration-150 hover:shadow-md dark:border-slate-500 dark:bg-slate-800/80" }, [
                  createVNode("div", { class: "flex items-center justify-between border-b border-dashed border-slate-400 px-2 py-1 dark:border-slate-500" }, [
                    createVNode("div", { class: "flex items-center gap-2" }, [
                      createVNode("button", {
                        type: "button",
                        class: "handle text-slate-400 hover:text-slate-700 dark:hover:text-slate-100",
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
                        title: `[sort: ${assignment.sort}] ${formatDate(assignment.published_at)}`
                      }, " ID: " + toDisplayString(assignment.id), 9, ["title"])
                    ]),
                    createVNode("div", { class: "flex items-center gap-2" }, [
                      createVNode("span", {
                        class: "rounded-sm border border-gray-400 bg-fuchsia-100 px-1.5 py-0.5 text-[10px] text-fuchsia-800 dark:bg-fuchsia-900/50 dark:text-fuchsia-400",
                        title: `${unref(t)("status")}: ${getAssignmentStatusLabel(assignment.status)} / ${getAssignmentVisibilityLabel(assignment.visibility)}`
                      }, toDisplayString(getAssignmentStatusLabel(assignment.status)), 9, ["title"]),
                      createVNode("input", {
                        type: "checkbox",
                        checked: __props.selectedAssignments.includes(assignment.id),
                        onChange: ($event) => emit("toggle-select", assignment.id)
                      }, null, 40, ["checked", "onChange"])
                    ])
                  ]),
                  createVNode("div", { class: "relative h-32 w-full bg-slate-200 dark:bg-slate-900" }, [
                    getPrimaryImage(assignment) ? (openBlock(), createBlock("img", {
                      key: 0,
                      src: getImageUrl(getPrimaryImage(assignment)),
                      alt: ((_c = getPrimaryImage(assignment)) == null ? void 0 : _c.alt) || unref(t)("defaultImageAlt"),
                      title: ((_d = getPrimaryImage(assignment)) == null ? void 0 : _d.caption) || unref(t)("assignmentImage"),
                      class: "h-full w-full object-cover"
                    }, null, 8, ["src", "alt", "title"])) : (openBlock(), createBlock("img", {
                      key: 1,
                      src: "/storage/school/school_assignment_images/default-image.png",
                      alt: unref(t)("defaultImageTitle"),
                      class: "h-full w-full object-cover"
                    }, null, 8, ["alt"]))
                  ]),
                  createVNode("div", { class: "flex flex-1 flex-col space-y-1 px-3 py-2" }, [
                    createVNode("a", {
                      href: `/school/assignments/${encodeURIComponent(assignment.slug)}`,
                      class: "line-clamp-2 text-center text-sm font-semibold text-sky-700 hover:underline dark:text-sky-200",
                      target: "_blank",
                      rel: "noopener noreferrer",
                      title: getAssignmentSubtitle(assignment) || getAssignmentShort(assignment)
                    }, toDisplayString(getAssignmentTitle(assignment)), 9, ["href", "title"]),
                    createVNode("div", {
                      class: "truncate text-center text-[10px] text-slate-600 dark:text-slate-300",
                      title: assignment.slug
                    }, toDisplayString(assignment.slug), 9, ["title"]),
                    createVNode("div", {
                      class: "text-xs font-semibold text-slate-600 dark:text-slate-300",
                      title: getLessonLabel(assignment)
                    }, toDisplayString(unref(t)("lesson")) + ": " + toDisplayString(getLessonLabel(assignment) || "—"), 9, ["title"]),
                    createVNode("div", {
                      class: "text-xs font-semibold text-slate-600 dark:text-slate-300",
                      title: getModuleLabel(assignment)
                    }, toDisplayString(unref(t)("module")) + ": " + toDisplayString(getModuleLabel(assignment) || "—"), 9, ["title"]),
                    createVNode("div", {
                      class: "text-xs font-semibold text-slate-600 dark:text-slate-300",
                      title: getCourseLabel(assignment)
                    }, toDisplayString(unref(t)("course")) + ": " + toDisplayString(getCourseLabel(assignment) || "—"), 9, ["title"]),
                    createVNode("div", {
                      class: "text-center text-xs font-semibold text-teal-600 dark:text-teal-300",
                      title: getInstructorLabel(assignment)
                    }, toDisplayString(unref(t)("instructor")) + ": " + toDisplayString(getInstructorLabel(assignment) || "—"), 9, ["title"]),
                    createVNode("div", { class: "mt-1 flex flex-wrap justify-center gap-1 text-[10px] font-semibold" }, [
                      createVNode("span", { class: "rounded-sm border border-gray-400 bg-amber-50 px-2 py-0.5 text-amber-600 dark:bg-yellow-950/50 dark:text-yellow-200" }, toDisplayString(getAssignmentVisibilityLabel(assignment.visibility)), 1),
                      createVNode("span", { class: "rounded-sm border border-gray-400 bg-emerald-100 px-2 py-0.5 text-emerald-700 dark:bg-emerald-900 dark:text-emerald-200" }, toDisplayString(getAssignmentGradingTypeLabel(assignment.grading_type)), 1)
                    ]),
                    createVNode("div", { class: "mt-2 flex flex-col justify-center text-center text-[11px] text-gray-700 dark:text-gray-400" }, [
                      createVNode("div", null, toDisplayString(unref(t)("maxScore")) + ": " + toDisplayString(assignment.max_score ?? 0), 1),
                      createVNode("div", null, toDisplayString(unref(t)("quizAttemptItems")) + ": " + toDisplayString(assignment.attempts_limit ?? 0), 1),
                      assignment.due_at ? (openBlock(), createBlock("div", {
                        key: 0,
                        class: assignment.is_overdue ? "text-red-500" : ""
                      }, toDisplayString(unref(t)("dueAt")) + ": " + toDisplayString(formatDate(assignment.due_at)), 3)) : createCommentVNode("", true),
                      createVNode("div", null, toDisplayString(unref(t)("submissions")) + ": " + toDisplayString(assignment.submissions_count ?? 0), 1),
                      createVNode("div", null, toDisplayString(unref(t)("images")) + ": " + toDisplayString(assignment.images_count ?? 0), 1)
                    ])
                  ]),
                  createVNode("div", { class: "flex items-center justify-between border-t border-dashed border-slate-400 px-3 py-2 dark:border-slate-500" }, [
                    createVNode("div", { class: "flex items-center gap-1" }, [
                      createVNode(_sfc_main$5, {
                        isActive: assignment.left,
                        title: assignment.left ? unref(t)("enabled") : unref(t)("disabled"),
                        onToggleLeft: ($event) => emit("toggle-left", assignment)
                      }, null, 8, ["isActive", "title", "onToggleLeft"]),
                      createVNode(_sfc_main$6, {
                        isActive: assignment.main,
                        title: assignment.main ? unref(t)("enabled") : unref(t)("disabled"),
                        onToggleMain: ($event) => emit("toggle-main", assignment)
                      }, null, 8, ["isActive", "title", "onToggleMain"]),
                      createVNode(_sfc_main$7, {
                        isActive: assignment.right,
                        title: assignment.right ? unref(t)("enabled") : unref(t)("disabled"),
                        onToggleRight: ($event) => emit("toggle-right", assignment)
                      }, null, 8, ["isActive", "title", "onToggleRight"])
                    ]),
                    createVNode("div", { class: "flex items-center gap-1" }, [
                      createVNode(_sfc_main$8, {
                        isActive: assignment.activity,
                        title: assignment.activity ? unref(t)("enabled") : unref(t)("disabled"),
                        onToggleActivity: ($event) => emit("toggle-activity", assignment)
                      }, null, 8, ["isActive", "title", "onToggleActivity"]),
                      createVNode(_sfc_main$9, {
                        title: unref(t)("clone"),
                        onClone: ($event) => emit("clone", assignment)
                      }, null, 8, ["title", "onClone"]),
                      createVNode(_sfc_main$a, {
                        href: _ctx.route("admin.schoolAssignments.edit", {
                          schoolAssignment: assignment.id
                        })
                      }, null, 8, ["href"]),
                      createVNode(_sfc_main$b, {
                        onDelete: ($event) => emit("delete", assignment)
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
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Components/Admin/School/SchoolAssignment/View/AssignmentCardGrid.vue");
  return _sfc_setup$1 ? _sfc_setup$1(props, ctx) : void 0;
};
const _sfc_main = {
  __name: "Index",
  __ssrInlineRender: true,
  props: {
    currentLocale: { type: String, default: "" },
    availableLocales: { type: Array, default: () => [] },
    adminSchoolAssignmentsProcessingMode: { type: String, default: "frontend" },
    useServerProcessing: { type: Boolean, default: false },
    assignments: { type: [Array, Object], default: () => [] },
    assignmentsCount: { type: Number, default: 0 },
    adminSchoolAssignmentsPerPage: { type: Number, default: 6 },
    adminSchoolAssignmentsDefaultSort: { type: String, default: "idDesc" },
    sortParam: { type: String, default: "" },
    search: { type: String, default: "" },
    errors: { type: Object, default: () => ({}) }
  },
  setup(__props) {
    const { t } = useI18n();
    const toast = useToast();
    const props = __props;
    const viewMode = ref(
      localStorage.getItem("admin_view_mode_assignments") || "table"
    );
    watch(viewMode, (value) => {
      localStorage.setItem(
        "admin_view_mode_assignments",
        value
      );
    });
    const assignmentsList = computed(() => {
      var _a;
      if (Array.isArray(props.assignments)) {
        return props.assignments;
      }
      if (Array.isArray((_a = props.assignments) == null ? void 0 : _a.data)) {
        return props.assignments.data;
      }
      return [];
    });
    const localAssignments = ref([]);
    watch(
      assignmentsList,
      (assignments) => {
        localAssignments.value = JSON.parse(
          JSON.stringify(assignments || [])
        );
      },
      {
        immediate: true,
        deep: true
      }
    );
    const itemsPerPage = ref(
      props.adminSchoolAssignmentsPerPage || 6
    );
    const sortParam = ref(
      props.sortParam || props.adminSchoolAssignmentsDefaultSort || "idDesc"
    );
    const searchQuery = ref(
      props.search || ""
    );
    const currentPage = ref(1);
    const serverCurrentPage = computed(() => {
      var _a, _b, _c;
      return Number(
        ((_b = (_a = props.assignments) == null ? void 0 : _a.meta) == null ? void 0 : _b.current_page) ?? ((_c = props.assignments) == null ? void 0 : _c.current_page) ?? 1
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
          "admin.settings.updateAdminCountSchoolAssignments"
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
          "admin.settings.updateAdminSortSchoolAssignments"
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
    const getAssignmentTitle = (assignment) => {
      var _a;
      return ((_a = assignment == null ? void 0 : assignment.translation) == null ? void 0 : _a.title) || `ID: ${assignment == null ? void 0 : assignment.id}`;
    };
    const getAssignmentSubtitle = (assignment) => {
      var _a;
      return ((_a = assignment == null ? void 0 : assignment.translation) == null ? void 0 : _a.subtitle) || "";
    };
    const getAssignmentShort = (assignment) => {
      var _a;
      return ((_a = assignment == null ? void 0 : assignment.translation) == null ? void 0 : _a.short) || "";
    };
    const getAssignmentDescription = (assignment) => {
      var _a;
      return ((_a = assignment == null ? void 0 : assignment.translation) == null ? void 0 : _a.description) || "";
    };
    const getAssignmentInstructions = (assignment) => {
      var _a;
      return ((_a = assignment == null ? void 0 : assignment.translation) == null ? void 0 : _a.instructions) || "";
    };
    const getCourseTitle = (assignment) => {
      var _a, _b, _c;
      return ((_b = (_a = assignment == null ? void 0 : assignment.course) == null ? void 0 : _a.translation) == null ? void 0 : _b.title) || (((_c = assignment == null ? void 0 : assignment.course) == null ? void 0 : _c.id) ? `ID: ${assignment.course.id}` : "");
    };
    const getCourseSlug = (assignment) => {
      var _a;
      return ((_a = assignment == null ? void 0 : assignment.course) == null ? void 0 : _a.slug) || "";
    };
    const getModuleTitle = (assignment) => {
      var _a, _b, _c;
      return ((_b = (_a = assignment == null ? void 0 : assignment.module) == null ? void 0 : _a.translation) == null ? void 0 : _b.title) || (((_c = assignment == null ? void 0 : assignment.module) == null ? void 0 : _c.id) ? `ID: ${assignment.module.id}` : "");
    };
    const getModuleSlug = (assignment) => {
      var _a;
      return ((_a = assignment == null ? void 0 : assignment.module) == null ? void 0 : _a.slug) || "";
    };
    const getLessonTitle = (assignment) => {
      var _a, _b, _c;
      return ((_b = (_a = assignment == null ? void 0 : assignment.lesson) == null ? void 0 : _a.translation) == null ? void 0 : _b.title) || (((_c = assignment == null ? void 0 : assignment.lesson) == null ? void 0 : _c.id) ? `ID: ${assignment.lesson.id}` : "");
    };
    const getLessonSlug = (assignment) => {
      var _a;
      return ((_a = assignment == null ? void 0 : assignment.lesson) == null ? void 0 : _a.slug) || "";
    };
    const getInstructorTitle = (assignment) => {
      var _a, _b, _c, _d, _e;
      return ((_b = (_a = assignment == null ? void 0 : assignment.instructor) == null ? void 0 : _a.translation) == null ? void 0 : _b.title) || ((_d = (_c = assignment == null ? void 0 : assignment.instructor) == null ? void 0 : _c.user) == null ? void 0 : _d.name) || (((_e = assignment == null ? void 0 : assignment.instructor) == null ? void 0 : _e.id) ? `ID: ${assignment.instructor.id}` : "");
    };
    const getInstructorName = (assignment) => {
      var _a, _b;
      return ((_b = (_a = assignment == null ? void 0 : assignment.instructor) == null ? void 0 : _a.user) == null ? void 0 : _b.name) || "";
    };
    const getInstructorEmail = (assignment) => {
      var _a, _b;
      return ((_b = (_a = assignment == null ? void 0 : assignment.instructor) == null ? void 0 : _a.user) == null ? void 0 : _b.email) || "";
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
    const sortAssignments = (items) => {
      const list = [
        ...items || []
      ];
      if (sortParam.value === "activity") {
        return list.filter(
          (assignment) => !!assignment.activity
        );
      }
      if (sortParam.value === "inactive") {
        return list.filter(
          (assignment) => !assignment.activity
        );
      }
      if (sortParam.value === "left") {
        return list.filter(
          (assignment) => !!assignment.left
        );
      }
      if (sortParam.value === "noLeft") {
        return list.filter(
          (assignment) => !assignment.left
        );
      }
      if (sortParam.value === "main") {
        return list.filter(
          (assignment) => !!assignment.main
        );
      }
      if (sortParam.value === "noMain") {
        return list.filter(
          (assignment) => !assignment.main
        );
      }
      if (sortParam.value === "right") {
        return list.filter(
          (assignment) => !!assignment.right
        );
      }
      if (sortParam.value === "noRight") {
        return list.filter(
          (assignment) => !assignment.right
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
          getAssignmentTitle(a),
          getAssignmentTitle(b)
        ) || safeNumber(a == null ? void 0 : a.id) - safeNumber(b == null ? void 0 : b.id),
        titleDesc: (a, b) => compareText(
          getAssignmentTitle(b),
          getAssignmentTitle(a)
        ) || safeNumber(b == null ? void 0 : b.id) - safeNumber(a == null ? void 0 : a.id),
        courseAsc: byNumberAsc("school_course_id"),
        courseDesc: byNumberDesc("school_course_id"),
        courseTitleAsc: (a, b) => compareText(
          getCourseTitle(a),
          getCourseTitle(b)
        ) || safeNumber(a == null ? void 0 : a.id) - safeNumber(b == null ? void 0 : b.id),
        courseTitleDesc: (a, b) => compareText(
          getCourseTitle(b),
          getCourseTitle(a)
        ) || safeNumber(b == null ? void 0 : b.id) - safeNumber(a == null ? void 0 : a.id),
        moduleAsc: byNumberAsc("school_module_id"),
        moduleDesc: byNumberDesc("school_module_id"),
        moduleTitleAsc: (a, b) => compareText(
          getModuleTitle(a),
          getModuleTitle(b)
        ) || safeNumber(a == null ? void 0 : a.id) - safeNumber(b == null ? void 0 : b.id),
        moduleTitleDesc: (a, b) => compareText(
          getModuleTitle(b),
          getModuleTitle(a)
        ) || safeNumber(b == null ? void 0 : b.id) - safeNumber(a == null ? void 0 : a.id),
        lessonAsc: byNumberAsc("school_lesson_id"),
        lessonDesc: byNumberDesc("school_lesson_id"),
        lessonTitleAsc: (a, b) => compareText(
          getLessonTitle(a),
          getLessonTitle(b)
        ) || safeNumber(a == null ? void 0 : a.id) - safeNumber(b == null ? void 0 : b.id),
        lessonTitleDesc: (a, b) => compareText(
          getLessonTitle(b),
          getLessonTitle(a)
        ) || safeNumber(b == null ? void 0 : b.id) - safeNumber(a == null ? void 0 : a.id),
        instructorAsc: byNumberAsc(
          "school_instructor_profile_id"
        ),
        instructorDesc: byNumberDesc(
          "school_instructor_profile_id"
        ),
        instructorTitleAsc: (a, b) => compareText(
          getInstructorTitle(a),
          getInstructorTitle(b)
        ) || safeNumber(a == null ? void 0 : a.id) - safeNumber(b == null ? void 0 : b.id),
        instructorTitleDesc: (a, b) => compareText(
          getInstructorTitle(b),
          getInstructorTitle(a)
        ) || safeNumber(b == null ? void 0 : b.id) - safeNumber(a == null ? void 0 : a.id),
        statusAsc: byStringAsc("status"),
        statusDesc: byStringDesc("status"),
        visibilityAsc: byStringAsc("visibility"),
        visibilityDesc: byStringDesc("visibility"),
        gradingTypeAsc: byStringAsc("grading_type"),
        gradingTypeDesc: byStringDesc("grading_type"),
        attemptsLimitAsc: byNumberAsc("attempts_limit"),
        attemptsLimitDesc: byNumberDesc("attempts_limit"),
        maxScoreAsc: byNumberAsc("max_score"),
        maxScoreDesc: byNumberDesc("max_score"),
        submissionsAsc: byNumberAsc("submissions_count"),
        submissionsDesc: byNumberDesc("submissions_count"),
        imagesAsc: byNumberAsc("images_count"),
        imagesDesc: byNumberDesc("images_count"),
        activityAsc: byNumberAsc("activity"),
        activityDesc: byNumberDesc("activity"),
        publishedAtAsc: (a, b) => safeDate(a == null ? void 0 : a.published_at) - safeDate(b == null ? void 0 : b.published_at) || safeNumber(a == null ? void 0 : a.id) - safeNumber(b == null ? void 0 : b.id),
        publishedAtDesc: (a, b) => safeDate(b == null ? void 0 : b.published_at) - safeDate(a == null ? void 0 : a.published_at) || safeNumber(b == null ? void 0 : b.id) - safeNumber(a == null ? void 0 : a.id),
        dateAsc: (a, b) => safeDate(a == null ? void 0 : a.published_at) - safeDate(b == null ? void 0 : b.published_at) || safeNumber(a == null ? void 0 : a.id) - safeNumber(b == null ? void 0 : b.id),
        dateDesc: (a, b) => safeDate(b == null ? void 0 : b.published_at) - safeDate(a == null ? void 0 : a.published_at) || safeNumber(b == null ? void 0 : b.id) - safeNumber(a == null ? void 0 : a.id),
        dueAtAsc: (a, b) => safeDate(a == null ? void 0 : a.due_at) - safeDate(b == null ? void 0 : b.due_at) || safeNumber(a == null ? void 0 : a.id) - safeNumber(b == null ? void 0 : b.id),
        dueAtDesc: (a, b) => safeDate(b == null ? void 0 : b.due_at) - safeDate(a == null ? void 0 : a.due_at) || safeNumber(b == null ? void 0 : b.id) - safeNumber(a == null ? void 0 : a.id),
        createdAtAsc: (a, b) => safeDate(a == null ? void 0 : a.created_at) - safeDate(b == null ? void 0 : b.created_at) || safeNumber(a == null ? void 0 : a.id) - safeNumber(b == null ? void 0 : b.id),
        createdAtDesc: (a, b) => safeDate(b == null ? void 0 : b.created_at) - safeDate(a == null ? void 0 : a.created_at) || safeNumber(b == null ? void 0 : b.id) - safeNumber(a == null ? void 0 : a.id),
        updatedAtAsc: (a, b) => safeDate(a == null ? void 0 : a.updated_at) - safeDate(b == null ? void 0 : b.updated_at) || safeNumber(a == null ? void 0 : a.id) - safeNumber(b == null ? void 0 : b.id),
        updatedAtDesc: (a, b) => safeDate(b == null ? void 0 : b.updated_at) - safeDate(a == null ? void 0 : a.updated_at) || safeNumber(b == null ? void 0 : b.id) - safeNumber(a == null ? void 0 : a.id)
      };
      const sorter = sortMap[sortParam.value];
      return sorter ? list.sort(sorter) : list;
    };
    const filteredAssignments = computed(() => {
      let assignments = localAssignments.value || [];
      if (props.useServerProcessing) {
        return assignments;
      }
      const query = normalize(searchQuery.value);
      if (query) {
        assignments = assignments.filter(
          (assignment) => {
            const title = normalize(
              getAssignmentTitle(
                assignment
              )
            );
            const subtitle = normalize(
              getAssignmentSubtitle(
                assignment
              )
            );
            const slug = normalize(
              assignment == null ? void 0 : assignment.slug
            );
            const short = normalize(
              getAssignmentShort(
                assignment
              )
            );
            const description = normalize(
              getAssignmentDescription(
                assignment
              )
            );
            const instructions = normalize(
              getAssignmentInstructions(
                assignment
              )
            );
            const courseTitle = normalize(
              getCourseTitle(
                assignment
              )
            );
            const courseSlug = normalize(
              getCourseSlug(
                assignment
              )
            );
            const moduleTitle = normalize(
              getModuleTitle(
                assignment
              )
            );
            const moduleSlug = normalize(
              getModuleSlug(
                assignment
              )
            );
            const lessonTitle = normalize(
              getLessonTitle(
                assignment
              )
            );
            const lessonSlug = normalize(
              getLessonSlug(
                assignment
              )
            );
            const instructorTitle = normalize(
              getInstructorTitle(
                assignment
              )
            );
            const instructorName = normalize(
              getInstructorName(
                assignment
              )
            );
            const instructorEmail = normalize(
              getInstructorEmail(
                assignment
              )
            );
            const status = normalize(
              assignment == null ? void 0 : assignment.status
            );
            const visibility = normalize(
              assignment == null ? void 0 : assignment.visibility
            );
            const gradingType = normalize(
              assignment == null ? void 0 : assignment.grading_type
            );
            const ids = [
              assignment == null ? void 0 : assignment.id,
              assignment == null ? void 0 : assignment.sort,
              assignment == null ? void 0 : assignment.school_course_id,
              assignment == null ? void 0 : assignment.school_module_id,
              assignment == null ? void 0 : assignment.school_lesson_id,
              assignment == null ? void 0 : assignment.school_instructor_profile_id,
              assignment == null ? void 0 : assignment.max_score,
              assignment == null ? void 0 : assignment.attempts_limit
            ].map(normalize).join(" ");
            return title.includes(query) || subtitle.includes(query) || slug.includes(query) || short.includes(query) || description.includes(query) || instructions.includes(query) || courseTitle.includes(query) || courseSlug.includes(query) || moduleTitle.includes(query) || moduleSlug.includes(query) || lessonTitle.includes(query) || lessonSlug.includes(query) || instructorTitle.includes(query) || instructorName.includes(query) || instructorEmail.includes(query) || status.includes(query) || visibility.includes(query) || gradingType.includes(query) || ids.includes(query);
          }
        );
      }
      return sortAssignments(
        assignments
      );
    });
    const paginatedAssignments = computed(() => {
      const perPage = Number(itemsPerPage.value) || 6;
      const start = (currentPage.value - 1) * perPage;
      return filteredAssignments.value.slice(
        start,
        start + perPage
      );
    });
    const displayedAssignments = computed(() => {
      return props.useServerProcessing ? assignmentsList.value : paginatedAssignments.value;
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
    const assignmentToDeleteId = ref(null);
    const assignmentToDeleteTitle = ref("");
    const confirmDelete = (assignmentOrId, title = null) => {
      if (typeof assignmentOrId === "object" && assignmentOrId !== null) {
        assignmentToDeleteId.value = assignmentOrId.id;
        assignmentToDeleteTitle.value = title || getAssignmentTitle(
          assignmentOrId
        );
      } else {
        assignmentToDeleteId.value = assignmentOrId;
        assignmentToDeleteTitle.value = title || `ID: ${assignmentOrId}`;
      }
      showConfirmDeleteModal.value = true;
    };
    const closeModal = () => {
      showConfirmDeleteModal.value = false;
      assignmentToDeleteId.value = null;
      assignmentToDeleteTitle.value = "";
    };
    const deleteAssignment = () => {
      if (assignmentToDeleteId.value === null) {
        return;
      }
      const id = assignmentToDeleteId.value;
      const title = assignmentToDeleteTitle.value;
      router.delete(
        route(
          "admin.schoolAssignments.destroy",
          {
            schoolAssignment: id
          }
        ),
        {
          preserveScroll: true,
          preserveState: false,
          onSuccess: () => {
            toast.success(
              `Задание "${title || `ID: ${id}`}" удалено.`
            );
          },
          onError: (errors) => {
            const errorKey = Object.keys(
              errors || {}
            )[0];
            const message = (errors == null ? void 0 : errors.general) || (errors == null ? void 0 : errors[errorKey]) || "Произошла ошибка при удалении.";
            toast.error(
              `${message} (Задание: ${title || `ID: ${id}`})`
            );
          },
          onFinish: () => {
            closeModal();
          }
        }
      );
    };
    const patchAssignment = (assignmentId, payload) => {
      const index = localAssignments.value.findIndex(
        (assignment) => assignment.id === assignmentId
      );
      if (index === -1) {
        return;
      }
      localAssignments.value[index] = {
        ...localAssignments.value[index],
        ...payload
      };
    };
    const selectedAssignments = ref([]);
    const toggleAll = (payload) => {
      var _a;
      const checked = (payload == null ? void 0 : payload.checked) ?? ((_a = payload == null ? void 0 : payload.target) == null ? void 0 : _a.checked) ?? false;
      const ids = (payload == null ? void 0 : payload.ids) ?? displayedAssignments.value.map(
        (assignment) => assignment.id
      );
      if (checked) {
        selectedAssignments.value = [
          .../* @__PURE__ */ new Set([
            ...selectedAssignments.value,
            ...ids
          ])
        ];
        return;
      }
      selectedAssignments.value = selectedAssignments.value.filter(
        (id) => !ids.includes(id)
      );
    };
    const toggleSelectAssignment = (id) => {
      const index = selectedAssignments.value.indexOf(id);
      if (index > -1) {
        selectedAssignments.value.splice(
          index,
          1
        );
        return;
      }
      selectedAssignments.value.push(id);
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
          "admin.actions.schoolAssignments.updateSortBulk"
        ),
        {
          items
        },
        {
          preserveScroll: true,
          preserveState: true,
          onSuccess: () => {
            toast.success(
              "Порядок заданий успешно обновлён."
            );
          },
          onError: (errors) => {
            console.error(
              "Ошибка обновления сортировки заданий:",
              errors
            );
            toast.error(
              (errors == null ? void 0 : errors.message) || (errors == null ? void 0 : errors.general) || "Не удалось обновить порядок заданий."
            );
            router.reload({
              only: [
                "assignments"
              ],
              preserveScroll: true
            });
          }
        }
      );
    };
    const bulkToggleActivity = (newActivity) => {
      if (!selectedAssignments.value.length) {
        toast.warning(
          "Выберите задания для активации/деактивации."
        );
        return;
      }
      const ids = [
        ...selectedAssignments.value
      ];
      router.put(
        route(
          "admin.actions.schoolAssignments.bulkUpdateActivity"
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
              patchAssignment(
                id,
                {
                  activity: newActivity
                }
              );
            });
            selectedAssignments.value = [];
            toast.success(
              "Активность выбранных заданий обновлена."
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
    const bulkToggleFlag = (field, newValue, routeName, successMessage) => {
      if (!selectedAssignments.value.length) {
        toast.warning(
          "Выберите задания для массового действия."
        );
        return;
      }
      const ids = [
        ...selectedAssignments.value
      ];
      router.put(
        route(
          routeName
        ),
        {
          ids,
          [field]: newValue
        },
        {
          preserveScroll: true,
          preserveState: true,
          onSuccess: () => {
            ids.forEach((id) => {
              patchAssignment(
                id,
                {
                  [field]: newValue
                }
              );
            });
            selectedAssignments.value = [];
            toast.success(
              successMessage
            );
          },
          onError: (errors) => {
            toast.error(
              (errors == null ? void 0 : errors.ids) || (errors == null ? void 0 : errors[field]) || (errors == null ? void 0 : errors.general) || "Ошибка массового обновления."
            );
          }
        }
      );
    };
    const bulkDelete = () => {
      if (!selectedAssignments.value.length) {
        toast.warning(
          "Выберите задания для удаления."
        );
        return;
      }
      if (!confirm(
        "Вы уверены, что хотите удалить выбранные задания?"
      )) {
        return;
      }
      router.delete(
        route(
          "admin.actions.schoolAssignments.bulkDestroy"
        ),
        {
          data: {
            ids: selectedAssignments.value
          },
          preserveScroll: true,
          preserveState: false,
          onSuccess: () => {
            selectedAssignments.value = [];
            toast.success(
              "Выбранные задания успешно удалены."
            );
          },
          onError: (errors) => {
            const errorKey = Object.keys(
              errors || {}
            )[0];
            toast.error(
              (errors == null ? void 0 : errors[errorKey]) || "Ошибка массового удаления заданий."
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
      if (action === "left") {
        bulkToggleFlag(
          "left",
          true,
          "admin.actions.schoolAssignments.bulkUpdateLeft",
          "Задания добавлены в левую колонку."
        );
      }
      if (action === "noLeft") {
        bulkToggleFlag(
          "left",
          false,
          "admin.actions.schoolAssignments.bulkUpdateLeft",
          "Задания убраны из левой колонки."
        );
      }
      if (action === "main") {
        bulkToggleFlag(
          "main",
          true,
          "admin.actions.schoolAssignments.bulkUpdateMain",
          "Задания добавлены в главный блок."
        );
      }
      if (action === "noMain") {
        bulkToggleFlag(
          "main",
          false,
          "admin.actions.schoolAssignments.bulkUpdateMain",
          "Задания убраны из главного блока."
        );
      }
      if (action === "right") {
        bulkToggleFlag(
          "right",
          true,
          "admin.actions.schoolAssignments.bulkUpdateRight",
          "Задания добавлены в правую колонку."
        );
      }
      if (action === "noRight") {
        bulkToggleFlag(
          "right",
          false,
          "admin.actions.schoolAssignments.bulkUpdateRight",
          "Задания убраны из правой колонки."
        );
      }
      if (action === "delete") {
        bulkDelete();
      }
      event.target.value = "";
    };
    const toggleActivity = (assignment) => {
      const newActivity = !assignment.activity;
      const assignmentTitle = getAssignmentTitle(
        assignment
      );
      const actionText = newActivity ? t("activated") : t("deactivated");
      router.put(
        route(
          "admin.actions.schoolAssignments.updateActivity",
          {
            schoolAssignment: assignment.id
          }
        ),
        {
          activity: newActivity
        },
        {
          preserveScroll: true,
          preserveState: true,
          onSuccess: () => {
            patchAssignment(
              assignment.id,
              {
                activity: newActivity
              }
            );
            assignment.activity = newActivity;
            toast.success(
              `Задание "${assignmentTitle}" ${actionText}.`
            );
          },
          onError: (errors) => {
            toast.error(
              (errors == null ? void 0 : errors.activity) || (errors == null ? void 0 : errors.general) || `Ошибка изменения активности для задания "${assignmentTitle}".`
            );
          }
        }
      );
    };
    const toggleFlag = (assignment, field, routeName, successMessage, errorMessage) => {
      const newValue = !assignment[field];
      router.put(
        route(
          routeName,
          {
            schoolAssignment: assignment.id
          }
        ),
        {
          [field]: newValue
        },
        {
          preserveScroll: true,
          preserveState: true,
          onSuccess: () => {
            patchAssignment(
              assignment.id,
              {
                [field]: newValue
              }
            );
            assignment[field] = newValue;
            toast.success(
              successMessage
            );
          },
          onError: (errors) => {
            toast.error(
              (errors == null ? void 0 : errors[field]) || (errors == null ? void 0 : errors.general) || errorMessage
            );
          }
        }
      );
    };
    const toggleLeft = (assignment) => {
      toggleFlag(
        assignment,
        "left",
        "admin.actions.schoolAssignments.updateLeft",
        "Левая колонка обновлена.",
        "Ошибка обновления левой колонки."
      );
    };
    const toggleMain = (assignment) => {
      toggleFlag(
        assignment,
        "main",
        "admin.actions.schoolAssignments.updateMain",
        "Главный блок обновлён.",
        "Ошибка обновления главного блока."
      );
    };
    const toggleRight = (assignment) => {
      toggleFlag(
        assignment,
        "right",
        "admin.actions.schoolAssignments.updateRight",
        "Правая колонка обновлена.",
        "Ошибка обновления правой колонки."
      );
    };
    const cloneAssignment = (assignment) => {
      router.post(
        route(
          "admin.actions.schoolAssignments.clone",
          {
            schoolAssignment: assignment.id
          }
        ),
        {},
        {
          preserveScroll: true,
          onSuccess: () => {
            toast.success(
              "Задание успешно клонировано."
            );
          },
          onError: () => {
            toast.error(
              "Ошибка при клонировании задания."
            );
          }
        }
      );
    };
    return (_ctx, _push, _parent, _attrs) => {
      _push(ssrRenderComponent(AdminLayout, mergeProps({
        title: unref(t)("assignments")
      }, _attrs), {
        header: withCtx((_, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(ssrRenderComponent(TitlePage, null, {
              default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                if (_push3) {
                  _push3(`${ssrInterpolate(unref(t)("assignments"))}`);
                } else {
                  return [
                    createTextVNode(toDisplayString(unref(t)("assignments")), 1)
                  ];
                }
              }),
              _: 1
            }, _parent2, _scopeId));
          } else {
            return [
              createVNode(TitlePage, null, {
                default: withCtx(() => [
                  createTextVNode(toDisplayString(unref(t)("assignments")), 1)
                ]),
                _: 1
              })
            ];
          }
        }),
        default: withCtx((_, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(`<div class="px-2 py-2 w-full max-w-12xl mx-auto"${_scopeId}><div class="p-4 bg-slate-50 dark:bg-slate-700 border border-blue-400 dark:border-blue-200 overflow-hidden shadow-md shadow-gray-500 dark:shadow-slate-400 bg-opacity-95 dark:bg-opacity-95"${_scopeId}><div class="sm:flex sm:justify-between sm:items-center mb-3 gap-3"${_scopeId}>`);
            _push2(ssrRenderComponent(_sfc_main$c, {
              href: _ctx.route("admin.schoolAssignments.create")
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
                  _push3(` ${ssrInterpolate(unref(t)("addAssignment"))}`);
                } else {
                  return [
                    createTextVNode(" " + toDisplayString(unref(t)("addAssignment")), 1)
                  ];
                }
              }),
              _: 1
            }, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$d, {
              "setting-key": "adminSchoolAssignmentsProcessingMode",
              mode: __props.adminSchoolAssignmentsProcessingMode,
              "use-server-processing": __props.useServerProcessing,
              total: __props.assignmentsCount
            }, null, _parent2, _scopeId));
            _push2(`</div>`);
            if (__props.assignmentsCount && !__props.useServerProcessing) {
              _push2(ssrRenderComponent(_sfc_main$e, {
                modelValue: searchQuery.value,
                "onUpdate:modelValue": ($event) => searchQuery.value = $event,
                placeholder: unref(t)("searchByName")
              }, null, _parent2, _scopeId));
            } else {
              _push2(`<!---->`);
            }
            if (__props.assignmentsCount && __props.useServerProcessing) {
              _push2(ssrRenderComponent(_sfc_main$f, {
                modelValue: searchQuery.value,
                "onUpdate:modelValue": ($event) => searchQuery.value = $event
              }, null, _parent2, _scopeId));
            } else {
              _push2(`<!---->`);
            }
            if (__props.assignmentsCount) {
              _push2(`<div class="flex justify-between items-center flex-col md:flex-row my-3 gap-3"${_scopeId}>`);
              if (!__props.useServerProcessing) {
                _push2(ssrRenderComponent(_sfc_main$g, {
                  "items-per-page": itemsPerPage.value,
                  "onUpdate:itemsPerPage": ($event) => itemsPerPage.value = $event
                }, null, _parent2, _scopeId));
              } else {
                _push2(ssrRenderComponent(_sfc_main$h, {
                  "items-per-page": itemsPerPage.value,
                  "update-route": "admin.settings.updateAdminCountSchoolAssignments"
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
            if (__props.assignmentsCount) {
              _push2(`<div class="flex flex-col lg:flex-row items-center justify-between gap-3"${_scopeId}>`);
              _push2(ssrRenderComponent(_sfc_main$i, null, {
                default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                  if (_push3) {
                    _push3(`${ssrInterpolate(__props.assignmentsCount)}`);
                  } else {
                    return [
                      createTextVNode(toDisplayString(__props.assignmentsCount), 1)
                    ];
                  }
                }),
                _: 1
              }, _parent2, _scopeId));
              _push2(ssrRenderComponent(_sfc_main$4, { onChange: handleBulkAction }, null, _parent2, _scopeId));
              _push2(ssrRenderComponent(_sfc_main$j, {
                viewMode: viewMode.value,
                "onUpdate:viewMode": ($event) => viewMode.value = $event
              }, null, _parent2, _scopeId));
              _push2(`</div>`);
            } else {
              _push2(`<!---->`);
            }
            if (__props.assignmentsCount) {
              _push2(`<div class="flex justify-center items-center flex-col md:flex-row mt-3"${_scopeId}>`);
              if (!__props.useServerProcessing) {
                _push2(ssrRenderComponent(_sfc_main$k, {
                  "current-page": currentPage.value,
                  "items-per-page": itemsPerPage.value,
                  "total-items": filteredAssignments.value.length,
                  "onUpdate:currentPage": ($event) => currentPage.value = $event
                }, null, _parent2, _scopeId));
              } else {
                _push2(ssrRenderComponent(_sfc_main$l, { pagination: __props.assignments }, null, _parent2, _scopeId));
              }
              _push2(`</div>`);
            } else {
              _push2(`<!---->`);
            }
            if (viewMode.value === "table") {
              _push2(ssrRenderComponent(_sfc_main$2, {
                assignments: displayedAssignments.value,
                "selected-assignments": selectedAssignments.value,
                onToggleActivity: toggleActivity,
                onToggleLeft: toggleLeft,
                onToggleMain: toggleMain,
                onToggleRight: toggleRight,
                onDelete: confirmDelete,
                onUpdateSortOrder: handleSortOrderUpdate,
                onToggleSelect: toggleSelectAssignment,
                onToggleAll: toggleAll,
                onClone: cloneAssignment
              }, null, _parent2, _scopeId));
            } else {
              _push2(ssrRenderComponent(_sfc_main$1, {
                assignments: displayedAssignments.value,
                "selected-assignments": selectedAssignments.value,
                onToggleActivity: toggleActivity,
                onToggleLeft: toggleLeft,
                onToggleMain: toggleMain,
                onToggleRight: toggleRight,
                onDelete: confirmDelete,
                onUpdateSortOrder: handleSortOrderUpdate,
                onToggleSelect: toggleSelectAssignment,
                onToggleAll: toggleAll,
                onClone: cloneAssignment
              }, null, _parent2, _scopeId));
            }
            if (__props.assignmentsCount) {
              _push2(`<div class="flex justify-center items-center flex-col md:flex-row mt-3"${_scopeId}>`);
              if (!__props.useServerProcessing) {
                _push2(ssrRenderComponent(_sfc_main$k, {
                  "current-page": currentPage.value,
                  "items-per-page": itemsPerPage.value,
                  "total-items": filteredAssignments.value.length,
                  "onUpdate:currentPage": ($event) => currentPage.value = $event
                }, null, _parent2, _scopeId));
              } else {
                _push2(ssrRenderComponent(_sfc_main$l, { pagination: __props.assignments }, null, _parent2, _scopeId));
              }
              _push2(`</div>`);
            } else {
              _push2(`<!---->`);
            }
            _push2(`</div></div>`);
            _push2(ssrRenderComponent(_sfc_main$m, {
              show: showConfirmDeleteModal.value,
              onCancel: closeModal,
              onConfirm: deleteAssignment,
              cancelText: unref(t)("cancel"),
              confirmText: unref(t)("yesDelete"),
              onClose: closeModal
            }, null, _parent2, _scopeId));
          } else {
            return [
              createVNode("div", { class: "px-2 py-2 w-full max-w-12xl mx-auto" }, [
                createVNode("div", { class: "p-4 bg-slate-50 dark:bg-slate-700 border border-blue-400 dark:border-blue-200 overflow-hidden shadow-md shadow-gray-500 dark:shadow-slate-400 bg-opacity-95 dark:bg-opacity-95" }, [
                  createVNode("div", { class: "sm:flex sm:justify-between sm:items-center mb-3 gap-3" }, [
                    createVNode(_sfc_main$c, {
                      href: _ctx.route("admin.schoolAssignments.create")
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
                        createTextVNode(" " + toDisplayString(unref(t)("addAssignment")), 1)
                      ]),
                      _: 1
                    }, 8, ["href"]),
                    createVNode(_sfc_main$d, {
                      "setting-key": "adminSchoolAssignmentsProcessingMode",
                      mode: __props.adminSchoolAssignmentsProcessingMode,
                      "use-server-processing": __props.useServerProcessing,
                      total: __props.assignmentsCount
                    }, null, 8, ["mode", "use-server-processing", "total"])
                  ]),
                  __props.assignmentsCount && !__props.useServerProcessing ? (openBlock(), createBlock(_sfc_main$e, {
                    key: 0,
                    modelValue: searchQuery.value,
                    "onUpdate:modelValue": ($event) => searchQuery.value = $event,
                    placeholder: unref(t)("searchByName")
                  }, null, 8, ["modelValue", "onUpdate:modelValue", "placeholder"])) : createCommentVNode("", true),
                  __props.assignmentsCount && __props.useServerProcessing ? (openBlock(), createBlock(_sfc_main$f, {
                    key: 1,
                    modelValue: searchQuery.value,
                    "onUpdate:modelValue": ($event) => searchQuery.value = $event
                  }, null, 8, ["modelValue", "onUpdate:modelValue"])) : createCommentVNode("", true),
                  __props.assignmentsCount ? (openBlock(), createBlock("div", {
                    key: 2,
                    class: "flex justify-between items-center flex-col md:flex-row my-3 gap-3"
                  }, [
                    !__props.useServerProcessing ? (openBlock(), createBlock(_sfc_main$g, {
                      key: 0,
                      "items-per-page": itemsPerPage.value,
                      "onUpdate:itemsPerPage": ($event) => itemsPerPage.value = $event
                    }, null, 8, ["items-per-page", "onUpdate:itemsPerPage"])) : (openBlock(), createBlock(_sfc_main$h, {
                      key: 1,
                      "items-per-page": itemsPerPage.value,
                      "update-route": "admin.settings.updateAdminCountSchoolAssignments"
                    }, null, 8, ["items-per-page"])),
                    createVNode(_sfc_main$3, {
                      sortParam: sortParam.value,
                      "onUpdate:sortParam": ($event) => sortParam.value = $event
                    }, null, 8, ["sortParam", "onUpdate:sortParam"])
                  ])) : createCommentVNode("", true),
                  __props.assignmentsCount ? (openBlock(), createBlock("div", {
                    key: 3,
                    class: "flex flex-col lg:flex-row items-center justify-between gap-3"
                  }, [
                    createVNode(_sfc_main$i, null, {
                      default: withCtx(() => [
                        createTextVNode(toDisplayString(__props.assignmentsCount), 1)
                      ]),
                      _: 1
                    }),
                    createVNode(_sfc_main$4, { onChange: handleBulkAction }),
                    createVNode(_sfc_main$j, {
                      viewMode: viewMode.value,
                      "onUpdate:viewMode": ($event) => viewMode.value = $event
                    }, null, 8, ["viewMode", "onUpdate:viewMode"])
                  ])) : createCommentVNode("", true),
                  __props.assignmentsCount ? (openBlock(), createBlock("div", {
                    key: 4,
                    class: "flex justify-center items-center flex-col md:flex-row mt-3"
                  }, [
                    !__props.useServerProcessing ? (openBlock(), createBlock(_sfc_main$k, {
                      key: 0,
                      "current-page": currentPage.value,
                      "items-per-page": itemsPerPage.value,
                      "total-items": filteredAssignments.value.length,
                      "onUpdate:currentPage": ($event) => currentPage.value = $event
                    }, null, 8, ["current-page", "items-per-page", "total-items", "onUpdate:currentPage"])) : (openBlock(), createBlock(_sfc_main$l, {
                      key: 1,
                      pagination: __props.assignments
                    }, null, 8, ["pagination"]))
                  ])) : createCommentVNode("", true),
                  viewMode.value === "table" ? (openBlock(), createBlock(_sfc_main$2, {
                    key: 5,
                    assignments: displayedAssignments.value,
                    "selected-assignments": selectedAssignments.value,
                    onToggleActivity: toggleActivity,
                    onToggleLeft: toggleLeft,
                    onToggleMain: toggleMain,
                    onToggleRight: toggleRight,
                    onDelete: confirmDelete,
                    onUpdateSortOrder: handleSortOrderUpdate,
                    onToggleSelect: toggleSelectAssignment,
                    onToggleAll: toggleAll,
                    onClone: cloneAssignment
                  }, null, 8, ["assignments", "selected-assignments"])) : (openBlock(), createBlock(_sfc_main$1, {
                    key: 6,
                    assignments: displayedAssignments.value,
                    "selected-assignments": selectedAssignments.value,
                    onToggleActivity: toggleActivity,
                    onToggleLeft: toggleLeft,
                    onToggleMain: toggleMain,
                    onToggleRight: toggleRight,
                    onDelete: confirmDelete,
                    onUpdateSortOrder: handleSortOrderUpdate,
                    onToggleSelect: toggleSelectAssignment,
                    onToggleAll: toggleAll,
                    onClone: cloneAssignment
                  }, null, 8, ["assignments", "selected-assignments"])),
                  __props.assignmentsCount ? (openBlock(), createBlock("div", {
                    key: 7,
                    class: "flex justify-center items-center flex-col md:flex-row mt-3"
                  }, [
                    !__props.useServerProcessing ? (openBlock(), createBlock(_sfc_main$k, {
                      key: 0,
                      "current-page": currentPage.value,
                      "items-per-page": itemsPerPage.value,
                      "total-items": filteredAssignments.value.length,
                      "onUpdate:currentPage": ($event) => currentPage.value = $event
                    }, null, 8, ["current-page", "items-per-page", "total-items", "onUpdate:currentPage"])) : (openBlock(), createBlock(_sfc_main$l, {
                      key: 1,
                      pagination: __props.assignments
                    }, null, 8, ["pagination"]))
                  ])) : createCommentVNode("", true)
                ])
              ]),
              createVNode(_sfc_main$m, {
                show: showConfirmDeleteModal.value,
                onCancel: closeModal,
                onConfirm: deleteAssignment,
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
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Pages/Admin/School/SchoolAssignments/Index.vue");
  return _sfc_setup ? _sfc_setup(props, ctx) : void 0;
};
export {
  _sfc_main as default
};
