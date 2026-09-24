import { computed, mergeProps, unref, withCtx, createVNode, toDisplayString, createTextVNode, createBlock, openBlock, useSSRContext, ref, watch, createCommentVNode } from "vue";
import { ssrRenderAttrs, ssrRenderList, ssrRenderComponent, ssrInterpolate, ssrRenderAttr, ssrRenderClass } from "vue/server-renderer";
import { Link, usePage, Head, router } from "@inertiajs/vue3";
import { useI18n } from "vue-i18n";
import { u as useSmoothScrollTo } from "./useSmoothScrollTo-g9G3kyDv.js";
import { _ as _sfc_main$3 } from "./DefaultLayout-6zxGxPtK.js";
import { _ as _sfc_main$4, a as _sfc_main$c, P as Progress } from "./Progress-CyfOKcqP.js";
import { _ as _sfc_main$5, a as _sfc_main$b } from "./RightSidebarSchool-DlAegojf.js";
import { _ as _sfc_main$6 } from "./EntityPageToolbar-DT32FtSd.js";
import { _ as _sfc_main$7 } from "./FrontendEntityPageToolbar-Xr_r9znL.js";
import { _ as _sfc_main$8, a as _sfc_main$9, b as _sfc_main$d } from "./PublicAdminBottomPanel-BhLEQMoJ.js";
import { _ as _sfc_main$a, S as SectionBanners } from "./SectionBanners-Cdgmr0Bw.js";
import { U as UniversalImageSlider } from "./UniversalImageSlider-Cu2Xndcn.js";
import "axios";
import "@inertiajs/inertia";
import "./ThemeToggle-DA16u1ft.js";
import "@vueuse/core";
import "./LocaleSelectOption-BeLdazeX.js";
import "./Checkbox-CgE3PSwb.js";
import "./TextInput-CCxUFX3K.js";
import "./InputLabel-Ds0Eo91B.js";
import "./PrimaryButton-D7EZDGT_.js";
import "./_plugin-vue_export-helper-1tPrXgE0.js";
import "./ViewModeToggle-DMCnQ0wo.js";
import "./ProcessingModeSwitcher-BJvzFf6_.js";
const _sfc_main$2 = {
  __name: "AssignmentGrid",
  __ssrInlineRender: true,
  props: {
    assignments: {
      type: Array,
      default: () => []
    },
    cols: {
      type: Number,
      default: 2
    }
  },
  setup(__props) {
    const { t } = useI18n();
    const props = __props;
    const gridClass = computed(() => {
      switch (props.cols) {
        case 4:
          return "grid-cols-1 sm:grid-cols-2 xl:grid-cols-4";
        case 3:
          return "grid-cols-1 sm:grid-cols-2 xl:grid-cols-3";
        case 2:
        default:
          return "grid-cols-1 sm:grid-cols-2";
      }
    });
    const getTranslation = (assignment) => {
      return (assignment == null ? void 0 : assignment.translation) || null;
    };
    const getAssignmentTitle = (assignment) => {
      var _a;
      return ((_a = getTranslation(assignment)) == null ? void 0 : _a.title) || "";
    };
    const getAssignmentShort = (assignment) => {
      var _a;
      return ((_a = getTranslation(assignment)) == null ? void 0 : _a.short) || "";
    };
    const assignmentLink = (assignment) => {
      return route(
        "public.schoolAssignments.show",
        {
          slug: assignment.slug
        }
      );
    };
    const getCourse = (assignment) => {
      return (assignment == null ? void 0 : assignment.course) || null;
    };
    const getModule = (assignment) => {
      return (assignment == null ? void 0 : assignment.module) || null;
    };
    const getLesson = (assignment) => {
      return (assignment == null ? void 0 : assignment.lesson) || null;
    };
    const getInstructor = (assignment) => {
      return (assignment == null ? void 0 : assignment.instructor) || null;
    };
    const courseLink = (assignment) => {
      const course = getCourse(assignment);
      return (course == null ? void 0 : course.slug) ? route(
        "public.schoolCourses.show",
        {
          slug: course.slug
        }
      ) : "#";
    };
    const moduleLink = (assignment) => {
      const course = getCourse(assignment);
      const module = getModule(assignment);
      return (course == null ? void 0 : course.slug) && (module == null ? void 0 : module.slug) ? route(
        "public.schoolModules.show",
        {
          courseSlug: course.slug,
          slug: module.slug
        }
      ) : "#";
    };
    const lessonLink = (assignment) => {
      const lesson = getLesson(assignment);
      return (lesson == null ? void 0 : lesson.slug) ? route(
        "public.schoolLessons.show",
        {
          slug: lesson.slug
        }
      ) : "#";
    };
    const getCourseTitle = (assignment) => {
      var _a;
      return ((_a = getCourse(assignment)) == null ? void 0 : _a.title) || t("course");
    };
    const getModuleTitle = (assignment) => {
      var _a;
      return ((_a = getModule(assignment)) == null ? void 0 : _a.title) || t("module");
    };
    const getLessonTitle = (assignment) => {
      var _a;
      return ((_a = getLesson(assignment)) == null ? void 0 : _a.title) || t("lesson");
    };
    const getInstructorName = (assignment) => {
      const instructor = getInstructor(assignment);
      return (instructor == null ? void 0 : instructor.public_name) || (instructor == null ? void 0 : instructor.title) || t("instructor");
    };
    const formatDate = (value, assignment) => {
      var _a;
      if (!value) {
        return null;
      }
      const date = new Date(value);
      if (Number.isNaN(date.getTime())) {
        return value;
      }
      const locale = ((_a = getTranslation(assignment)) == null ? void 0 : _a.locale) || void 0;
      return new Intl.DateTimeFormat(
        locale,
        {
          day: "2-digit",
          month: "2-digit",
          year: "numeric",
          hour: "2-digit",
          minute: "2-digit"
        }
      ).format(date);
    };
    const translateGradingType = (value) => {
      const map = {
        manual: t("gradingManual"),
        auto: t("gradingAuto")
      };
      return map[value] || value || "—";
    };
    const translateVisibility = (value) => {
      const map = {
        public: t("public"),
        enrolled: t("enrolled"),
        private: t("private")
      };
      return map[value] || value || "—";
    };
    const translateStatus = (value) => {
      const map = {
        draft: t("statusDraft"),
        published: t("statusPublished"),
        archived: t("statusArchived")
      };
      return map[value] || value || "—";
    };
    return (_ctx, _push, _parent, _attrs) => {
      _push(`<div${ssrRenderAttrs(mergeProps({
        class: ["grid gap-4", gridClass.value]
      }, _attrs))}><!--[-->`);
      ssrRenderList(__props.assignments, (assignment) => {
        _push(`<div class="group flex h-full flex-col overflow-hidden rounded-md border border-gray-200 bg-white shadow-sm transition hover:-translate-y-0.5 hover:shadow-md dark:border-gray-700 dark:bg-gray-900">`);
        _push(ssrRenderComponent(unref(Link), {
          href: assignmentLink(assignment)
        }, {
          default: withCtx((_, _push2, _parent2, _scopeId) => {
            if (_push2) {
              _push2(ssrRenderComponent(UniversalImageSlider, {
                entity: assignment,
                "height-class": "h-48",
                "rounded-class": "",
                "wrapper-class": "",
                "img-class": "w-full h-full object-cover\n                               transition duration-300 group-hover:scale-105"
              }, null, _parent2, _scopeId));
            } else {
              return [
                createVNode(UniversalImageSlider, {
                  entity: assignment,
                  "height-class": "h-48",
                  "rounded-class": "",
                  "wrapper-class": "",
                  "img-class": "w-full h-full object-cover\n                               transition duration-300 group-hover:scale-105"
                }, null, 8, ["entity"])
              ];
            }
          }),
          _: 2
        }, _parent));
        _push(`<div class="flex flex-1 flex-col p-4"><div class="flex items-center justify-center gap-3">`);
        _push(ssrRenderComponent(unref(Link), {
          href: assignmentLink(assignment),
          class: "inline-flex items-center gap-2"
        }, {
          default: withCtx((_, _push2, _parent2, _scopeId) => {
            if (_push2) {
              _push2(`<span class="text-base font-semibold text-slate-900/85 group-hover:opacity-95 dark:text-slate-100/85 dark:group-hover:opacity-95"${_scopeId}>${ssrInterpolate(getAssignmentTitle(assignment))}</span><span class="text-xs font-semibold text-slate-500 dark:text-slate-400"${_scopeId}> #${ssrInterpolate(assignment.id)}</span>`);
            } else {
              return [
                createVNode("span", { class: "text-base font-semibold text-slate-900/85 group-hover:opacity-95 dark:text-slate-100/85 dark:group-hover:opacity-95" }, toDisplayString(getAssignmentTitle(assignment)), 1),
                createVNode("span", { class: "text-xs font-semibold text-slate-500 dark:text-slate-400" }, " #" + toDisplayString(assignment.id), 1)
              ];
            }
          }),
          _: 2
        }, _parent));
        _push(`</div>`);
        if (getAssignmentShort(assignment)) {
          _push(`<div class="mt-3 line-clamp-3 text-sm text-slate-800 dark:text-slate-200">${ssrInterpolate(getAssignmentShort(assignment))}</div>`);
        } else {
          _push(`<!---->`);
        }
        _push(`<div class="mt-3 flex flex-wrap items-center justify-center gap-2 text-xs font-semibold text-slate-500 dark:text-slate-400">`);
        if (getInstructor(assignment)) {
          _push(`<div class="flex items-center justify-center gap-1 px-2 py-1 rounded-sm border border-slate-600 dark:border-slate-400"><div class="flex items-center justify-center gap-1 min-w-0 text-xs font-semibold text-slate-700/85 dark:text-slate-300/85"${ssrRenderAttr("title", getInstructorName(assignment))}><svg class="shrink-0 h-4 w-4 text-violet-600/85 dark:text-violet-200/85" fill="currentColor" viewBox="0 0 640 512"><path d="M622.34 153.2L343.4 67.5c-15.2-4.67-31.6-4.67-46.79 0L17.66 153.2c-23.54 7.23-23.54 38.36 0 45.59l48.63 14.94c-10.67 13.19-17.23 29.28-17.88 46.9C38.78 266.15 32 276.11 32 288c0 10.78 5.68 19.85 13.86 25.65L20.33 428.53C18.11 438.52 25.71 448 35.94 448h56.11c10.24 0 17.84-9.48 15.62-19.47L82.14 313.65C90.32 307.85 96 298.78 96 288c0-11.57-6.47-21.25-15.66-26.87.76-15.02 8.44-28.3 20.69-36.72L296.6 284.5c9.06 2.78 26.44 6.25 46.79 0l278.95-85.7c23.55-7.24 23.55-38.36 0-45.6zM352.79 315.09c-28.53 8.76-52.84 3.92-65.59 0l-145.02-44.55L128 384c0 35.35 85.96 64 192 64s192-28.65 192-64l-14.18-113.47-145.03 44.56z"></path></svg><div class="text-slate-500 dark:text-slate-400">${ssrInterpolate(unref(t)("instructor"))}: </div><div class="ml-1">${ssrInterpolate(getInstructorName(assignment))}</div></div></div>`);
        } else {
          _push(`<!---->`);
        }
        if (getCourse(assignment)) {
          _push(`<div class="flex items-center justify-center gap-1 px-2 py-1 rounded-sm border border-slate-600 dark:border-slate-400"><div class="flex items-center justify-center gap-1 min-w-0 text-xs font-semibold text-slate-700/85 dark:text-slate-300/85"${ssrRenderAttr("title", getCourseTitle(assignment))}><svg class="shrink-0 h-3 w-3 text-sky-600/85 dark:text-sky-200/85" fill="currentColor" viewBox="0 0 448 512"><path d="M318.38 208h-39.09c-1.49 27.03-6.54 51.35-14.21 70.41 27.71-13.24 48.02-39.19 53.3-70.41zm0-32c-5.29-31.22-25.59-57.17-53.3-70.41 7.68 19.06 12.72 43.38 14.21 70.41h39.09zM224 97.31c-7.69 7.45-20.77 34.42-23.43 78.69h46.87c-2.67-44.26-15.75-71.24-23.44-78.69zm-41.08 8.28c-27.71 13.24-48.02 39.19-53.3 70.41h39.09c1.49-27.03 6.53-51.35 14.21-70.41zm0 172.82c-7.68-19.06-12.72-43.38-14.21-70.41h-39.09c5.28 31.22 25.59 57.17 53.3 70.41zM247.43 208h-46.87c2.66 44.26 15.74 71.24 23.43 78.69 7.7-7.45 20.78-34.43 23.44-78.69zM448 358.4V25.6c0-16-9.6-25.6-25.6-25.6H96C41.6 0 0 41.6 0 96v320c0 54.4 41.6 96 96 96h326.4c12.8 0 25.6-9.6 25.6-25.6v-16c0-6.4-3.2-12.8-9.6-19.2-3.2-16-3.2-60.8 0-73.6 6.4-3.2 9.6-9.6 9.6-19.2zM224 64c70.69 0 128 57.31 128 128s-57.31 128-128 128S96 262.69 96 192 153.31 64 224 64zm160 384H96c-19.2 0-32-12.8-32-32s16-32 32-32h288v64z"></path></svg><div class="text-slate-500 dark:text-slate-400">${ssrInterpolate(unref(t)("course"))}: </div>`);
          _push(ssrRenderComponent(unref(Link), {
            href: courseLink(assignment),
            class: "ml-1 hover:text-blue-600 dark:hover:text-blue-400"
          }, {
            default: withCtx((_, _push2, _parent2, _scopeId) => {
              if (_push2) {
                _push2(`${ssrInterpolate(getCourseTitle(assignment))}`);
              } else {
                return [
                  createTextVNode(toDisplayString(getCourseTitle(assignment)), 1)
                ];
              }
            }),
            _: 2
          }, _parent));
          _push(`</div></div>`);
        } else {
          _push(`<!---->`);
        }
        if (getModule(assignment)) {
          _push(`<div class="flex items-center justify-center gap-1 px-2 py-1 rounded-sm border border-slate-600 dark:border-slate-400"><div class="flex items-center justify-center gap-1 min-w-0 text-xs font-semibold text-slate-700/85 dark:text-slate-300/85"${ssrRenderAttr("title", getModuleTitle(assignment))}><svg class="shrink-0 h-3 w-3 text-teal-600/85 dark:text-teal-200/85" fill="currentColor" viewBox="0 0 24 24"><rect x="1" y="1" width="10" height="10" rx="2"></rect><path class="fill-current text-teal-400" d="M23.428,4.618,19.381.572h0a1.957,1.957,0,0,0-2.762,0L12.572,4.618a1.959,1.959,0,0,0,0,2.764l4.047,4.047a1.957,1.957,0,0,0,2.762,0l4.047-4.046A1.959,1.959,0,0,0,23.428,4.618Z"></path><rect x="13" y="13" width="10" height="10" rx="2"></rect><rect x="1" y="13" width="10" height="10" rx="2"></rect></svg><div class="text-slate-500 dark:text-slate-400">${ssrInterpolate(unref(t)("module"))}: </div>`);
          _push(ssrRenderComponent(unref(Link), {
            href: moduleLink(assignment),
            class: "ml-1 hover:text-blue-600 dark:hover:text-blue-400"
          }, {
            default: withCtx((_, _push2, _parent2, _scopeId) => {
              if (_push2) {
                _push2(`${ssrInterpolate(getModuleTitle(assignment))}`);
              } else {
                return [
                  createTextVNode(toDisplayString(getModuleTitle(assignment)), 1)
                ];
              }
            }),
            _: 2
          }, _parent));
          _push(`</div></div>`);
        } else {
          _push(`<!---->`);
        }
        if (getLesson(assignment)) {
          _push(`<div class="flex items-center justify-center gap-1 px-2 py-1 rounded-sm border border-slate-600 dark:border-slate-400"><div class="flex items-center justify-center gap-1 min-w-0 text-xs font-semibold text-slate-700/85 dark:text-slate-300/85"${ssrRenderAttr("title", getLessonTitle(assignment))}><svg class="h-4 w-4 text-sky-600/85 dark:text-sky-300/85" fill="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25"></path></svg><div class="text-slate-500 dark:text-slate-400">${ssrInterpolate(unref(t)("lesson"))}: </div>`);
          _push(ssrRenderComponent(unref(Link), {
            href: lessonLink(assignment),
            class: "ml-1 hover:text-blue-600 dark:hover:text-blue-400"
          }, {
            default: withCtx((_, _push2, _parent2, _scopeId) => {
              if (_push2) {
                _push2(`${ssrInterpolate(getLessonTitle(assignment))}`);
              } else {
                return [
                  createTextVNode(toDisplayString(getLessonTitle(assignment)), 1)
                ];
              }
            }),
            _: 2
          }, _parent));
          _push(`</div></div>`);
        } else {
          _push(`<!---->`);
        }
        if (assignment.status) {
          _push(`<div class="flex items-center justify-center gap-1 px-2 py-1 rounded-sm border border-slate-600 dark:border-slate-400"${ssrRenderAttr("title", unref(t)("status"))}><svg class="w-3 h-3 text-violet-600 dark:text-violet-300" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2a10 10 0 1 0 10 10A10.01 10.01 0 0 0 12 2Zm1 15h-2v-2h2Zm0-4h-2V7h2Z"></path></svg> ${ssrInterpolate(translateStatus(assignment.status))}</div>`);
        } else {
          _push(`<!---->`);
        }
        if (assignment.published_at) {
          _push(`<span class="flex items-center justify-center gap-1 px-2 py-1 rounded-sm border border-slate-600 dark:border-slate-400"><svg class="h-3 w-3 text-slate-600 dark:text-slate-300" fill="currentColor" viewBox="0 0 24 24"><path d="M19 4h-1V2h-2v2H8V2H6v2H5a2 2 0 0 0-2 2v13a3 3 0 0 0 3 3h12a3 3 0 0 0 3-3V6a2 2 0 0 0-2-2Zm0 15a1 1 0 0 1-1 1H6a1 1 0 0 1-1-1V10h14Z"></path></svg> ${ssrInterpolate(unref(t)("publishedAt"))}: ${ssrInterpolate(formatDate(assignment.published_at, assignment))}</span>`);
        } else {
          _push(`<!---->`);
        }
        if (assignment.grading_type) {
          _push(`<div class="flex items-center justify-center gap-1 px-2 py-1 rounded-sm border border-slate-600 dark:border-slate-400"${ssrRenderAttr("title", unref(t)("gradingType"))}><svg class="w-3 h-3 text-emerald-600 dark:text-emerald-300" fill="currentColor" viewBox="0 0 24 24"><path d="M9 16.17 4.83 12 3.41 13.41 9 19l12-12-1.41-1.41z"></path></svg> ${ssrInterpolate(translateGradingType(assignment.grading_type))}</div>`);
        } else {
          _push(`<!---->`);
        }
        if (assignment.max_score) {
          _push(`<div class="flex items-center justify-center gap-1 px-2 py-1 rounded-sm border border-slate-600 dark:border-slate-400"${ssrRenderAttr("title", unref(t)("maxScore"))}><svg class="w-3 h-3 text-amber-600 dark:text-amber-300" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2 14.85 8.15 21.5 9.27l-4.75 4.63 1.12 6.6L12 17.27 6.13 20.5l1.12-6.6L2.5 9.27l6.65-1.12L12 2Z"></path></svg> ${ssrInterpolate(assignment.max_score)}</div>`);
        } else {
          _push(`<!---->`);
        }
        if (assignment.attempts_limit !== null && assignment.attempts_limit !== void 0) {
          _push(`<div class="flex items-center justify-center gap-1 px-2 py-1 rounded-sm border border-slate-600 dark:border-slate-400"${ssrRenderAttr("title", unref(t)("attemptsLimit"))}><svg class="w-3 h-3 text-blue-600 dark:text-blue-300" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2a10 10 0 1 0 10 10A10.01 10.01 0 0 0 12 2Zm1 5h-2v6l5 3 1-1.73-4-2.27Z"></path></svg> ${ssrInterpolate(assignment.attempts_limit === 0 ? unref(t)("no") : assignment.attempts_limit)}</div>`);
        } else {
          _push(`<!---->`);
        }
        if (assignment.visibility) {
          _push(`<div class="flex items-center justify-center gap-1 px-2 py-1 rounded-sm border border-slate-600 dark:border-slate-400"${ssrRenderAttr("title", unref(t)("visibility"))}><svg class="w-3 h-3 text-sky-600 dark:text-sky-300" fill="currentColor" viewBox="0 0 24 24"><path d="M12 5c-7 0-10 7-10 7s3 7 10 7 10-7 10-7-3-7-10-7Zm0 11a4 4 0 1 1 4-4 4 4 0 0 1-4 4Z"></path></svg> ${ssrInterpolate(translateVisibility(assignment.visibility))}</div>`);
        } else {
          _push(`<!---->`);
        }
        _push(`</div>`);
        if (assignment.due_at) {
          _push(`<div class="mt-2 flex items-center justify-center gap-2 text-xs font-semibold text-rose-600 dark:text-rose-300"><svg class="h-3 w-3" fill="currentColor" viewBox="0 0 24 24"><path d="M19 4h-1V2h-2v2H8V2H6v2H5a2 2 0 0 0-2 2v13a3 3 0 0 0 3 3h12a3 3 0 0 0 3-3V6a2 2 0 0 0-2-2Zm0 15a1 1 0 0 1-1 1H6a1 1 0 0 1-1-1V10h14Z"></path></svg> ${ssrInterpolate(unref(t)("dueAt"))}: ${ssrInterpolate(formatDate(assignment.due_at, assignment))}</div>`);
        } else {
          _push(`<!---->`);
        }
        _push(`<div class="mt-auto pt-4">`);
        _push(ssrRenderComponent(unref(Link), {
          href: assignmentLink(assignment),
          class: "flex w-full items-center justify-center gap-2 rounded-sm px-3 py-2 btn-default"
        }, {
          default: withCtx((_, _push2, _parent2, _scopeId) => {
            if (_push2) {
              _push2(`<span class="text-sm font-semibold"${_scopeId}>${ssrInterpolate(unref(t)("readMore"))}</span><svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor"${_scopeId}><path fill-rule="evenodd" d="M7.21 14.77a.75.75 0 0 1 .02-1.06L10.94 10 7.23 6.29a.75.75 0 1 1 1.06-1.06l4.25 4.25a.75.75 0 0 1 0 1.06l-4.25 4.25a.75.75 0 0 1-1.06.02Z" clip-rule="evenodd"${_scopeId}></path></svg>`);
            } else {
              return [
                createVNode("span", { class: "text-sm font-semibold" }, toDisplayString(unref(t)("readMore")), 1),
                (openBlock(), createBlock("svg", {
                  class: "h-4 w-4",
                  viewBox: "0 0 20 20",
                  fill: "currentColor"
                }, [
                  createVNode("path", {
                    "fill-rule": "evenodd",
                    d: "M7.21 14.77a.75.75 0 0 1 .02-1.06L10.94 10 7.23 6.29a.75.75 0 1 1 1.06-1.06l4.25 4.25a.75.75 0 0 1 0 1.06l-4.25 4.25a.75.75 0 0 1-1.06.02Z",
                    "clip-rule": "evenodd"
                  })
                ]))
              ];
            }
          }),
          _: 2
        }, _parent));
        _push(`</div></div></div>`);
      });
      _push(`<!--]--></div>`);
    };
  }
};
const _sfc_setup$2 = _sfc_main$2.setup;
_sfc_main$2.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Components/Public/Default/School/SchoolAssignment/AssignmentGrid.vue");
  return _sfc_setup$2 ? _sfc_setup$2(props, ctx) : void 0;
};
const _sfc_main$1 = {
  __name: "AssignmentRows",
  __ssrInlineRender: true,
  props: {
    assignments: {
      type: Array,
      default: () => []
    }
  },
  setup(__props) {
    const { t } = useI18n();
    const getTranslation = (assignment) => {
      return (assignment == null ? void 0 : assignment.translation) || null;
    };
    const getAssignmentTitle = (assignment) => {
      var _a;
      return ((_a = getTranslation(assignment)) == null ? void 0 : _a.title) || "";
    };
    const getAssignmentShort = (assignment) => {
      var _a;
      return ((_a = getTranslation(assignment)) == null ? void 0 : _a.short) || "";
    };
    const assignmentLink = (assignment) => {
      return route(
        "public.schoolAssignments.show",
        {
          slug: assignment.slug
        }
      );
    };
    const getCourse = (assignment) => {
      return (assignment == null ? void 0 : assignment.course) || null;
    };
    const getModule = (assignment) => {
      return (assignment == null ? void 0 : assignment.module) || null;
    };
    const getLesson = (assignment) => {
      return (assignment == null ? void 0 : assignment.lesson) || null;
    };
    const getInstructor = (assignment) => {
      return (assignment == null ? void 0 : assignment.instructor) || null;
    };
    const courseLink = (assignment) => {
      const course = getCourse(assignment);
      return (course == null ? void 0 : course.slug) ? route(
        "public.schoolCourses.show",
        {
          slug: course.slug
        }
      ) : "#";
    };
    const moduleLink = (assignment) => {
      const course = getCourse(assignment);
      const module = getModule(assignment);
      return (course == null ? void 0 : course.slug) && (module == null ? void 0 : module.slug) ? route(
        "public.schoolModules.show",
        {
          courseSlug: course.slug,
          slug: module.slug
        }
      ) : "#";
    };
    const lessonLink = (assignment) => {
      const lesson = getLesson(assignment);
      return (lesson == null ? void 0 : lesson.slug) ? route(
        "public.schoolLessons.show",
        {
          slug: lesson.slug
        }
      ) : "#";
    };
    const getCourseTitle = (assignment) => {
      var _a;
      return ((_a = getCourse(assignment)) == null ? void 0 : _a.title) || t("course");
    };
    const getModuleTitle = (assignment) => {
      var _a;
      return ((_a = getModule(assignment)) == null ? void 0 : _a.title) || t("module");
    };
    const getLessonTitle = (assignment) => {
      var _a;
      return ((_a = getLesson(assignment)) == null ? void 0 : _a.title) || t("lesson");
    };
    const getInstructorName = (assignment) => {
      const instructor = getInstructor(assignment);
      return (instructor == null ? void 0 : instructor.public_name) || (instructor == null ? void 0 : instructor.title) || t("instructor");
    };
    const formatDate = (value, assignment) => {
      var _a;
      if (!value) {
        return null;
      }
      const date = new Date(value);
      if (Number.isNaN(date.getTime())) {
        return value;
      }
      const locale = ((_a = getTranslation(assignment)) == null ? void 0 : _a.locale) || void 0;
      return new Intl.DateTimeFormat(
        locale,
        {
          day: "2-digit",
          month: "2-digit",
          year: "numeric",
          hour: "2-digit",
          minute: "2-digit"
        }
      ).format(date);
    };
    const translateGradingType = (value) => {
      const map = {
        manual: t("gradingManual"),
        auto: t("gradingAuto")
      };
      return map[value] || value || "—";
    };
    const translateVisibility = (value) => {
      const map = {
        public: t("public"),
        enrolled: t("enrolled"),
        private: t("private")
      };
      return map[value] || value || "—";
    };
    const translateStatus = (value) => {
      const map = {
        draft: t("statusDraft"),
        published: t("statusPublished"),
        archived: t("statusArchived")
      };
      return map[value] || value || "—";
    };
    return (_ctx, _push, _parent, _attrs) => {
      _push(`<div${ssrRenderAttrs(mergeProps({ class: "space-y-4" }, _attrs))}><!--[-->`);
      ssrRenderList(__props.assignments, (assignment) => {
        _push(`<div class="group flex flex-col sm:flex-row gap-3 rounded-md border border-gray-200 bg-white shadow-sm p-3 transition hover:shadow-md dark:border-gray-700 dark:bg-gray-900">`);
        _push(ssrRenderComponent(unref(Link), {
          href: assignmentLink(assignment),
          class: "shrink-0"
        }, {
          default: withCtx((_, _push2, _parent2, _scopeId) => {
            if (_push2) {
              _push2(ssrRenderComponent(UniversalImageSlider, {
                entity: assignment,
                "height-class": "h-44",
                "rounded-class": "rounded-md",
                "wrapper-class": "w-full sm:w-60\n                                   border border-gray-400 dark:border-gray-600",
                "img-class": "w-full h-full object-cover transition\n                               duration-300 group-hover:scale-105"
              }, null, _parent2, _scopeId));
            } else {
              return [
                createVNode(UniversalImageSlider, {
                  entity: assignment,
                  "height-class": "h-44",
                  "rounded-class": "rounded-md",
                  "wrapper-class": "w-full sm:w-60\n                                   border border-gray-400 dark:border-gray-600",
                  "img-class": "w-full h-full object-cover transition\n                               duration-300 group-hover:scale-105"
                }, null, 8, ["entity"])
              ];
            }
          }),
          _: 2
        }, _parent));
        _push(`<div class="min-w-0 flex-1 flex flex-col justify-around"><div class="flex items-center justify-center gap-3">`);
        _push(ssrRenderComponent(unref(Link), {
          href: assignmentLink(assignment),
          class: "min-w-0 inline-flex items-center gap-2"
        }, {
          default: withCtx((_, _push2, _parent2, _scopeId) => {
            if (_push2) {
              _push2(`<span class="text-base font-semibold text-slate-900/85 group-hover:opacity-95 dark:text-slate-100/85 dark:group-hover:opacity-95"${_scopeId}>${ssrInterpolate(getAssignmentTitle(assignment))} <span class="text-xs font-semibold text-slate-500 dark:text-slate-400"${_scopeId}> #${ssrInterpolate(assignment.id)}</span></span>`);
            } else {
              return [
                createVNode("span", { class: "text-base font-semibold text-slate-900/85 group-hover:opacity-95 dark:text-slate-100/85 dark:group-hover:opacity-95" }, [
                  createTextVNode(toDisplayString(getAssignmentTitle(assignment)) + " ", 1),
                  createVNode("span", { class: "text-xs font-semibold text-slate-500 dark:text-slate-400" }, " #" + toDisplayString(assignment.id), 1)
                ])
              ];
            }
          }),
          _: 2
        }, _parent));
        _push(`</div>`);
        if (getAssignmentShort(assignment)) {
          _push(`<div class="mt-3 line-clamp-3 text-sm text-slate-800 dark:text-slate-200">${ssrInterpolate(getAssignmentShort(assignment))}</div>`);
        } else {
          _push(`<!---->`);
        }
        _push(`<div class="mt-3 flex flex-wrap items-center justify-center gap-2 text-xs font-semibold text-slate-500 dark:text-slate-400">`);
        if (getInstructor(assignment)) {
          _push(`<div class="flex items-center justify-center gap-1 px-2 py-1 rounded-sm border border-slate-600 dark:border-slate-400"><div class="flex items-center justify-center gap-1 min-w-0 text-xs font-semibold text-slate-700/85 dark:text-slate-300/85"${ssrRenderAttr("title", getInstructorName(assignment))}><svg class="shrink-0 h-4 w-4 text-violet-600/85 dark:text-violet-200/85" fill="currentColor" viewBox="0 0 640 512"><path d="M622.34 153.2L343.4 67.5c-15.2-4.67-31.6-4.67-46.79 0L17.66 153.2c-23.54 7.23-23.54 38.36 0 45.59l48.63 14.94c-10.67 13.19-17.23 29.28-17.88 46.9C38.78 266.15 32 276.11 32 288c0 10.78 5.68 19.85 13.86 25.65L20.33 428.53C18.11 438.52 25.71 448 35.94 448h56.11c10.24 0 17.84-9.48 15.62-19.47L82.14 313.65C90.32 307.85 96 298.78 96 288c0-11.57-6.47-21.25-15.66-26.87.76-15.02 8.44-28.3 20.69-36.72L296.6 284.5c9.06 2.78 26.44 6.25 46.79 0l278.95-85.7c23.55-7.24 23.55-38.36 0-45.6zM352.79 315.09c-28.53 8.76-52.84 3.92-65.59 0l-145.02-44.55L128 384c0 35.35 85.96 64 192 64s192-28.65 192-64l-14.18-113.47-145.03 44.56z"></path></svg><div class="text-slate-500 dark:text-slate-400">${ssrInterpolate(unref(t)("instructor"))}: </div><div class="ml-1">${ssrInterpolate(getInstructorName(assignment))}</div></div></div>`);
        } else {
          _push(`<!---->`);
        }
        if (getCourse(assignment)) {
          _push(`<div class="flex items-center justify-center gap-1 px-2 py-1 rounded-sm border border-slate-600 dark:border-slate-400"><div class="flex items-center justify-center gap-1 min-w-0 text-xs font-semibold text-slate-700/85 dark:text-slate-300/85"${ssrRenderAttr("title", getCourseTitle(assignment))}><svg class="shrink-0 h-3 w-3 text-sky-600/85 dark:text-sky-200/85" fill="currentColor" viewBox="0 0 448 512"><path d="M318.38 208h-39.09c-1.49 27.03-6.54 51.35-14.21 70.41 27.71-13.24 48.02-39.19 53.3-70.41zm0-32c-5.29-31.22-25.59-57.17-53.3-70.41 7.68 19.06 12.72 43.38 14.21 70.41h39.09zM224 97.31c-7.69 7.45-20.77 34.42-23.43 78.69h46.87c-2.67-44.26-15.75-71.24-23.44-78.69zm-41.08 8.28c-27.71 13.24-48.02 39.19-53.3 70.41h39.09c1.49-27.03 6.53-51.35 14.21-70.41zm0 172.82c-7.68-19.06-12.72-43.38-14.21-70.41h-39.09c5.28 31.22 25.59 57.17 53.3 70.41zM247.43 208h-46.87c2.66 44.26 15.74 71.24 23.43 78.69 7.7-7.45 20.78-34.43 23.44-78.69zM448 358.4V25.6c0-16-9.6-25.6-25.6-25.6H96C41.6 0 0 41.6 0 96v320c0 54.4 41.6 96 96 96h326.4c12.8 0 25.6-9.6 25.6-25.6v-16c0-6.4-3.2-12.8-9.6-19.2-3.2-16-3.2-60.8 0-73.6 6.4-3.2 9.6-9.6 9.6-19.2zM224 64c70.69 0 128 57.31 128 128s-57.31 128-128 128S96 262.69 96 192 153.31 64 224 64zm160 384H96c-19.2 0-32-12.8-32-32s16-32 32-32h288v64z"></path></svg><div class="text-slate-500 dark:text-slate-400">${ssrInterpolate(unref(t)("course"))}: </div>`);
          _push(ssrRenderComponent(unref(Link), {
            href: courseLink(assignment),
            class: "ml-1 hover:text-blue-600 dark:hover:text-blue-400"
          }, {
            default: withCtx((_, _push2, _parent2, _scopeId) => {
              if (_push2) {
                _push2(`${ssrInterpolate(getCourseTitle(assignment))}`);
              } else {
                return [
                  createTextVNode(toDisplayString(getCourseTitle(assignment)), 1)
                ];
              }
            }),
            _: 2
          }, _parent));
          _push(`</div></div>`);
        } else {
          _push(`<!---->`);
        }
        if (getModule(assignment)) {
          _push(`<div class="flex items-center justify-center gap-1 px-2 py-1 rounded-sm border border-slate-600 dark:border-slate-400"><div class="flex items-center justify-center gap-1 min-w-0 text-xs font-semibold text-slate-700/85 dark:text-slate-300/85"${ssrRenderAttr("title", getModuleTitle(assignment))}><svg class="shrink-0 h-3 w-3 text-teal-600/85 dark:text-teal-200/85" fill="currentColor" viewBox="0 0 24 24"><rect x="1" y="1" width="10" height="10" rx="2"></rect><path class="fill-current text-teal-400" d="M23.428,4.618,19.381.572h0a1.957,1.957,0,0,0-2.762,0L12.572,4.618a1.959,1.959,0,0,0,0,2.764l4.047,4.047a1.957,1.957,0,0,0,2.762,0l4.047-4.046A1.959,1.959,0,0,0,23.428,4.618Z"></path><rect x="13" y="13" width="10" height="10" rx="2"></rect><rect x="1" y="13" width="10" height="10" rx="2"></rect></svg><div class="text-slate-500 dark:text-slate-400">${ssrInterpolate(unref(t)("module"))}: </div>`);
          _push(ssrRenderComponent(unref(Link), {
            href: moduleLink(assignment),
            class: "ml-1 hover:text-blue-600 dark:hover:text-blue-400"
          }, {
            default: withCtx((_, _push2, _parent2, _scopeId) => {
              if (_push2) {
                _push2(`${ssrInterpolate(getModuleTitle(assignment))}`);
              } else {
                return [
                  createTextVNode(toDisplayString(getModuleTitle(assignment)), 1)
                ];
              }
            }),
            _: 2
          }, _parent));
          _push(`</div></div>`);
        } else {
          _push(`<!---->`);
        }
        if (getLesson(assignment)) {
          _push(`<div class="flex items-center justify-center gap-1 px-2 py-1 rounded-sm border border-slate-600 dark:border-slate-400"><div class="flex items-center justify-center gap-1 min-w-0 text-xs font-semibold text-slate-700/85 dark:text-slate-300/85"${ssrRenderAttr("title", getLessonTitle(assignment))}><svg class="h-4 w-4 text-sky-600/85 dark:text-sky-300/85" fill="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25"></path></svg><div class="text-slate-500 dark:text-slate-400">${ssrInterpolate(unref(t)("lesson"))}: </div>`);
          _push(ssrRenderComponent(unref(Link), {
            href: lessonLink(assignment),
            class: "ml-1 hover:text-blue-600 dark:hover:text-blue-400"
          }, {
            default: withCtx((_, _push2, _parent2, _scopeId) => {
              if (_push2) {
                _push2(`${ssrInterpolate(getLessonTitle(assignment))}`);
              } else {
                return [
                  createTextVNode(toDisplayString(getLessonTitle(assignment)), 1)
                ];
              }
            }),
            _: 2
          }, _parent));
          _push(`</div></div>`);
        } else {
          _push(`<!---->`);
        }
        if (assignment.status) {
          _push(`<div class="flex items-center justify-center gap-1 px-2 py-1 rounded-sm border border-slate-600 dark:border-slate-400"${ssrRenderAttr("title", unref(t)("status"))}><svg class="w-3 h-3 text-violet-600 dark:text-violet-300" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2a10 10 0 1 0 10 10A10.01 10.01 0 0 0 12 2Zm1 15h-2v-2h2Zm0-4h-2V7h2Z"></path></svg> ${ssrInterpolate(translateStatus(assignment.status))}</div>`);
        } else {
          _push(`<!---->`);
        }
        if (assignment.published_at) {
          _push(`<span class="flex items-center justify-center gap-1 px-2 py-1 rounded-sm border border-slate-600 dark:border-slate-400"><svg class="h-3 w-3 text-slate-600 dark:text-slate-300" fill="currentColor" viewBox="0 0 24 24"><path d="M19 4h-1V2h-2v2H8V2H6v2H5a2 2 0 0 0-2 2v13a3 3 0 0 0 3 3h12a3 3 0 0 0 3-3V6a2 2 0 0 0-2-2Zm0 15a1 1 0 0 1-1 1H6a1 1 0 0 1-1-1V10h14Z"></path></svg> ${ssrInterpolate(unref(t)("publishedAt"))}: ${ssrInterpolate(formatDate(assignment.published_at, assignment))}</span>`);
        } else {
          _push(`<!---->`);
        }
        if (assignment.grading_type) {
          _push(`<div class="flex items-center justify-center gap-1 px-2 py-1 rounded-sm border border-slate-600 dark:border-slate-400"${ssrRenderAttr("title", unref(t)("gradingType"))}><svg class="w-3 h-3 text-emerald-600 dark:text-emerald-300" fill="currentColor" viewBox="0 0 24 24"><path d="M9 16.17 4.83 12 3.41 13.41 9 19l12-12-1.41-1.41z"></path></svg> ${ssrInterpolate(translateGradingType(assignment.grading_type))}</div>`);
        } else {
          _push(`<!---->`);
        }
        if (assignment.max_score) {
          _push(`<div class="flex items-center justify-center gap-1 px-2 py-1 rounded-sm border border-slate-600 dark:border-slate-400"${ssrRenderAttr("title", unref(t)("maxScore"))}><svg class="w-3 h-3 text-amber-600 dark:text-amber-300" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2 14.85 8.15 21.5 9.27l-4.75 4.63 1.12 6.6L12 17.27 6.13 20.5l1.12-6.6L2.5 9.27l6.65-1.12L12 2Z"></path></svg> ${ssrInterpolate(assignment.max_score)}</div>`);
        } else {
          _push(`<!---->`);
        }
        if (assignment.attempts_limit !== null && assignment.attempts_limit !== void 0) {
          _push(`<div class="flex items-center justify-center gap-1 px-2 py-1 rounded-sm border border-slate-600 dark:border-slate-400"${ssrRenderAttr("title", unref(t)("attemptsLimit"))}><svg class="w-3 h-3 text-blue-600 dark:text-blue-300" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2a10 10 0 1 0 10 10A10.01 10.01 0 0 0 12 2Zm1 5h-2v6l5 3 1-1.73-4-2.27Z"></path></svg> ${ssrInterpolate(assignment.attempts_limit === 0 ? unref(t)("no") : assignment.attempts_limit)}</div>`);
        } else {
          _push(`<!---->`);
        }
        if (assignment.visibility) {
          _push(`<div class="flex items-center justify-center gap-1 px-2 py-1 rounded-sm border border-slate-600 dark:border-slate-400"${ssrRenderAttr("title", unref(t)("visibility"))}><svg class="w-3 h-3 text-sky-600 dark:text-sky-300" fill="currentColor" viewBox="0 0 24 24"><path d="M12 5c-7 0-10 7-10 7s3 7 10 7 10-7 10-7-3-7-10-7Zm0 11a4 4 0 1 1 4-4 4 4 0 0 1-4 4Z"></path></svg> ${ssrInterpolate(translateVisibility(assignment.visibility))}</div>`);
        } else {
          _push(`<!---->`);
        }
        _push(`</div><div class="mt-auto pt-4 flex items-center justify-between">`);
        if (assignment.due_at) {
          _push(`<div class="mt-2 flex items-center justify-center gap-2 text-xs font-semibold text-rose-600 dark:text-rose-300"><svg class="h-3 w-3" fill="currentColor" viewBox="0 0 24 24"><path d="M19 4h-1V2h-2v2H8V2H6v2H5a2 2 0 0 0-2 2v13a3 3 0 0 0 3 3h12a3 3 0 0 0 3-3V6a2 2 0 0 0-2-2Zm0 15a1 1 0 0 1-1 1H6a1 1 0 0 1-1-1V10h14Z"></path></svg> ${ssrInterpolate(unref(t)("dueAt"))}: ${ssrInterpolate(formatDate(assignment.due_at, assignment))}</div>`);
        } else {
          _push(`<!---->`);
        }
        _push(ssrRenderComponent(unref(Link), {
          href: assignmentLink(assignment),
          class: "w-1/2 flex items-center justify-center gap-2 rounded-sm px-3 py-1 btn-default"
        }, {
          default: withCtx((_, _push2, _parent2, _scopeId) => {
            if (_push2) {
              _push2(`<span class="text-sm font-semibold"${_scopeId}>${ssrInterpolate(unref(t)("readMore"))}</span><svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor"${_scopeId}><path fill-rule="evenodd" d="M7.21 14.77a.75.75 0 0 1 .02-1.06L10.94 10 7.23 6.29a.75.75 0 1 1 1.06-1.06l4.25 4.25a.75.75 0 0 1 0 1.06l-4.25 4.25a.75.75 0 0 1-1.06.02Z" clip-rule="evenodd"${_scopeId}></path></svg>`);
            } else {
              return [
                createVNode("span", { class: "text-sm font-semibold" }, toDisplayString(unref(t)("readMore")), 1),
                (openBlock(), createBlock("svg", {
                  class: "h-4 w-4",
                  viewBox: "0 0 20 20",
                  fill: "currentColor"
                }, [
                  createVNode("path", {
                    "fill-rule": "evenodd",
                    d: "M7.21 14.77a.75.75 0 0 1 .02-1.06L10.94 10 7.23 6.29a.75.75 0 1 1 1.06-1.06l4.25 4.25a.75.75 0 0 1 0 1.06l-4.25 4.25a.75.75 0 0 1-1.06.02Z",
                    "clip-rule": "evenodd"
                  })
                ]))
              ];
            }
          }),
          _: 2
        }, _parent));
        _push(`</div></div></div>`);
      });
      _push(`<!--]--></div>`);
    };
  }
};
const _sfc_setup$1 = _sfc_main$1.setup;
_sfc_main$1.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Components/Public/Default/School/SchoolAssignment/AssignmentRows.vue");
  return _sfc_setup$1 ? _sfc_setup$1(props, ctx) : void 0;
};
const LEFT_SIDEBAR_KEY = "public_left_sidebar_collapsed";
const RIGHT_SIDEBAR_KEY = "public_right_sidebar_collapsed";
const VIEW_KEY = "public_school_assignments_view";
const _sfc_main = {
  __name: "Index",
  __ssrInlineRender: true,
  props: {
    locale: {
      type: String,
      default: ""
    },
    seo: {
      type: Object,
      default: () => ({
        title: "",
        keywords: "",
        description: ""
      })
    },
    useServerProcessing: {
      type: Boolean,
      default: false
    },
    publicSchoolAssignmentsProcessingMode: {
      type: String,
      default: "server"
    },
    title: {
      type: String,
      default: ""
    },
    canLogin: {
      type: Boolean,
      default: false
    },
    canRegister: {
      type: Boolean,
      default: false
    },
    trackTree: {
      type: Array,
      default: () => []
    },
    assignments: {
      type: [Array, Object],
      default: () => []
    },
    assignmentsCount: {
      type: Number,
      default: 0
    },
    assignmentsFound: {
      type: Number,
      default: 0
    },
    filters: {
      type: Object,
      default: () => ({})
    },
    /**
     * Сортировка по умолчанию.
     *
     * Источник истины:
     * PublicSettingsService.
     */
    defaultSort: {
      type: String,
      default: ""
    },
    mainVideos: {
      type: [Array, Object],
      default: () => []
    },
    mainBanners: {
      type: [Array, Object],
      default: () => []
    }
  },
  setup(__props) {
    var _a, _b, _c, _d;
    const { t } = useI18n();
    const props = __props;
    const page = usePage();
    const siteSettings = ((_a = page.props) == null ? void 0 : _a.siteSettings) || {};
    const isAdmin = computed(() => {
      var _a2;
      return ((_a2 = page.props) == null ? void 0 : _a2.isAdmin) === true;
    });
    const trackTree = computed(() => {
      return Array.isArray(props.trackTree) ? props.trackTree : [];
    });
    const normalizeList = (value) => {
      if (Array.isArray(value)) {
        return value;
      }
      if (Array.isArray(value == null ? void 0 : value.data)) {
        return value.data;
      }
      return [];
    };
    const assignmentsData = computed(() => {
      var _a2;
      if (Array.isArray(props.assignments)) {
        return props.assignments;
      }
      if (Array.isArray((_a2 = props.assignments) == null ? void 0 : _a2.data)) {
        return props.assignments.data;
      }
      return [];
    });
    const showLeft = computed(() => {
      return !(siteSettings == null ? void 0 : siteSettings.ViewLeftColumn) || siteSettings.ViewLeftColumn === "true";
    });
    const showRight = computed(() => {
      return !(siteSettings == null ? void 0 : siteSettings.ViewRightColumn) || siteSettings.ViewRightColumn === "true";
    });
    const getStoredBoolean = (key, defaultValue = true) => {
      const value = localStorage.getItem(key);
      if (value === null) {
        return defaultValue;
      }
      return value === "true";
    };
    const leftCollapsed = ref(
      getStoredBoolean(
        LEFT_SIDEBAR_KEY,
        true
      )
    );
    const rightCollapsed = ref(
      getStoredBoolean(
        RIGHT_SIDEBAR_KEY,
        true
      )
    );
    const gridCols = computed(() => {
      const leftExpanded = showLeft.value && !leftCollapsed.value;
      const rightExpanded = showRight.value && !rightCollapsed.value;
      if (leftExpanded && rightExpanded) {
        return 2;
      }
      if (leftExpanded || rightExpanded) {
        return 3;
      }
      return 4;
    });
    watch(
      [leftCollapsed, rightCollapsed],
      () => {
        localStorage.setItem(
          LEFT_SIDEBAR_KEY,
          String(leftCollapsed.value)
        );
        localStorage.setItem(
          RIGHT_SIDEBAR_KEY,
          String(rightCollapsed.value)
        );
      }
    );
    const q = ref(
      String(
        ((_b = props.filters) == null ? void 0 : _b.q) ?? ""
      )
    );
    const sort = ref(
      String(
        ((_c = props.filters) == null ? void 0 : _c.sort) || props.defaultSort || ""
      )
    );
    const viewMode = ref(
      String(
        ((_d = props.filters) == null ? void 0 : _d.view) || localStorage.getItem(VIEW_KEY) || "grid"
      )
    );
    watch(viewMode, (value) => {
      localStorage.setItem(
        VIEW_KEY,
        value
      );
    });
    const perPage = computed(() => {
      var _a2;
      const value = Number(
        (_a2 = props.filters) == null ? void 0 : _a2.per_page
      );
      return Number.isFinite(value) && value > 0 ? value : 1;
    });
    const assignmentSortOptions = [
      {
        value: "idDesc",
        label: t("idDesc")
      },
      {
        value: "idAsc",
        label: t("idAsc")
      },
      {
        value: "sortAsc",
        label: `${t("sortNumber")} 0→9`
      },
      {
        value: "sortDesc",
        label: `${t("sortNumber")} 9→0`
      },
      {
        value: "titleAsc",
        label: `${t("title")} A→Z`
      },
      {
        value: "titleDesc",
        label: `${t("title")} Z→A`
      },
      {
        value: "statusAsc",
        label: `${t("status")} A→Z`
      },
      {
        value: "statusDesc",
        label: `${t("status")} Z→A`
      },
      {
        value: "gradingTypeAsc",
        label: `${t("gradingType")} A→Z`
      },
      {
        value: "gradingTypeDesc",
        label: `${t("gradingType")} Z→A`
      },
      {
        value: "attemptsLimitAsc",
        label: `${t("attemptsLimit")} 0→9`
      },
      {
        value: "attemptsLimitDesc",
        label: `${t("attemptsLimit")} 9→0`
      },
      {
        value: "maxScoreAsc",
        label: `${t("maxScore")} 0→9`
      },
      {
        value: "maxScoreDesc",
        label: `${t("maxScore")} 9→0`
      },
      {
        value: "submissionsAsc",
        label: `${t("submissions")} 0→9`
      },
      {
        value: "submissionsDesc",
        label: `${t("submissions")} 9→0`
      },
      {
        value: "imagesAsc",
        label: `${t("images")} 0→9`
      },
      {
        value: "imagesDesc",
        label: `${t("images")} 9→0`
      },
      {
        value: "dueAtAsc",
        label: `${t("dueAt")} ↑`
      },
      {
        value: "dueAtDesc",
        label: `${t("dueAt")} ↓`
      },
      {
        value: "publishedAtAsc",
        label: `${t("publishedAt")} ↑`
      },
      {
        value: "publishedAtDesc",
        label: `${t("publishedAt")} ↓`
      },
      {
        value: "dateAsc",
        label: t("sortOldestFirst")
      },
      {
        value: "dateDesc",
        label: t("sortNewestFirst")
      }
    ];
    const frontendCurrentPage = ref(1);
    const {
      targetRef: scrollTarget,
      scrollToTarget
    } = useSmoothScrollTo({
      offset: 80,
      duration: 1200
    });
    const normalizeText = (value) => {
      return String(value ?? "").toLowerCase();
    };
    const getAssignmentTranslation = (assignment) => {
      return (assignment == null ? void 0 : assignment.translation) || null;
    };
    const getAssignmentTitle = (assignment) => {
      var _a2;
      return ((_a2 = assignment == null ? void 0 : assignment.translation) == null ? void 0 : _a2.title) || "";
    };
    const getAssignmentSubtitle = (assignment) => {
      var _a2;
      return ((_a2 = assignment == null ? void 0 : assignment.translation) == null ? void 0 : _a2.subtitle) || "";
    };
    const getAssignmentShort = (assignment) => {
      var _a2;
      return ((_a2 = assignment == null ? void 0 : assignment.translation) == null ? void 0 : _a2.short) || "";
    };
    const getAssignmentSlug = (assignment) => {
      return (assignment == null ? void 0 : assignment.slug) || "";
    };
    const getRelationTitle = (item) => {
      var _a2;
      return ((_a2 = item == null ? void 0 : item.translation) == null ? void 0 : _a2.title) || (item == null ? void 0 : item.title) || "";
    };
    const getInstructorTitle = (assignment) => {
      var _a2, _b2, _c2;
      return ((_b2 = (_a2 = assignment == null ? void 0 : assignment.instructor) == null ? void 0 : _a2.translation) == null ? void 0 : _b2.title) || ((_c2 = assignment == null ? void 0 : assignment.instructor) == null ? void 0 : _c2.title) || "";
    };
    const getInstructorUserName = (assignment) => {
      var _a2, _b2;
      return ((_b2 = (_a2 = assignment == null ? void 0 : assignment.instructor) == null ? void 0 : _a2.user) == null ? void 0 : _b2.name) || "";
    };
    const splitSearchWords = (value) => {
      return String(value ?? "").toLowerCase().split(/[\s:#№,"'«»(){}\[\].!?/\\|]+/u).map((word) => word.trim()).filter((word) => word.length >= 2);
    };
    const filteredAssignments = computed(() => {
      const words = splitSearchWords(q.value);
      if (words.length === 0) {
        return assignmentsData.value;
      }
      return assignmentsData.value.filter((assignment) => {
        const haystack = normalizeText([
          assignment.id,
          assignment.sort,
          assignment.school_course_id,
          assignment.school_module_id,
          assignment.school_lesson_id,
          assignment.school_instructor_profile_id,
          assignment.status,
          assignment.visibility,
          assignment.grading_type,
          assignment.attempts_limit,
          assignment.max_score,
          getAssignmentSlug(assignment),
          getAssignmentTitle(assignment),
          getAssignmentSubtitle(assignment),
          getAssignmentShort(assignment),
          getRelationTitle(assignment.course),
          getRelationTitle(assignment.module),
          getRelationTitle(assignment.lesson),
          getInstructorTitle(assignment),
          getInstructorUserName(assignment)
        ].filter(Boolean).join(" "));
        return words.every((word) => {
          return haystack.includes(word);
        });
      });
    });
    const dateValue = (value) => {
      if (!value) {
        return 0;
      }
      const timestamp = new Date(value).getTime();
      return Number.isFinite(timestamp) ? timestamp : 0;
    };
    const compareTitles = (first, second) => {
      var _a2, _b2;
      const firstTitle = getAssignmentTitle(first);
      const secondTitle = getAssignmentTitle(second);
      const locale = ((_a2 = getAssignmentTranslation(first)) == null ? void 0 : _a2.locale) || ((_b2 = getAssignmentTranslation(second)) == null ? void 0 : _b2.locale) || void 0;
      return firstTitle.localeCompare(
        secondTitle,
        locale
      );
    };
    const sortedAssignments = computed(() => {
      const list = [
        ...filteredAssignments.value
      ];
      return list.sort((a, b) => {
        switch (sort.value) {
          case "idAsc":
            return (a.id ?? 0) - (b.id ?? 0);
          case "idDesc":
            return (b.id ?? 0) - (a.id ?? 0);
          case "sortAsc":
            return (a.sort ?? 0) - (b.sort ?? 0);
          case "sortDesc":
            return (b.sort ?? 0) - (a.sort ?? 0);
          case "titleAsc":
            return compareTitles(a, b);
          case "titleDesc":
            return compareTitles(b, a);
          case "statusAsc":
            return normalizeText(
              a.status
            ).localeCompare(
              normalizeText(
                b.status
              )
            );
          case "statusDesc":
            return normalizeText(
              b.status
            ).localeCompare(
              normalizeText(
                a.status
              )
            );
          case "gradingTypeAsc":
            return normalizeText(
              a.grading_type
            ).localeCompare(
              normalizeText(
                b.grading_type
              )
            );
          case "gradingTypeDesc":
            return normalizeText(
              b.grading_type
            ).localeCompare(
              normalizeText(
                a.grading_type
              )
            );
          case "attemptsLimitAsc":
            return (a.attempts_limit ?? 0) - (b.attempts_limit ?? 0);
          case "attemptsLimitDesc":
            return (b.attempts_limit ?? 0) - (a.attempts_limit ?? 0);
          case "maxScoreAsc":
            return (a.max_score ?? 0) - (b.max_score ?? 0);
          case "maxScoreDesc":
            return (b.max_score ?? 0) - (a.max_score ?? 0);
          case "submissionsAsc":
            return (a.submissions_count ?? 0) - (b.submissions_count ?? 0);
          case "submissionsDesc":
            return (b.submissions_count ?? 0) - (a.submissions_count ?? 0);
          case "imagesAsc":
            return (a.images_count ?? 0) - (b.images_count ?? 0);
          case "imagesDesc":
            return (b.images_count ?? 0) - (a.images_count ?? 0);
          case "dueAtAsc":
            return dateValue(
              a.due_at
            ) - dateValue(
              b.due_at
            );
          case "dueAtDesc":
            return dateValue(
              b.due_at
            ) - dateValue(
              a.due_at
            );
          case "publishedAtAsc":
          case "dateAsc":
            return dateValue(
              a.published_at
            ) - dateValue(
              b.published_at
            );
          case "publishedAtDesc":
          case "dateDesc":
            return dateValue(
              b.published_at
            ) - dateValue(
              a.published_at
            );
          default:
            return 0;
        }
      });
    });
    const frontendPaginatedAssignments = computed(() => {
      const start = (frontendCurrentPage.value - 1) * perPage.value;
      return sortedAssignments.value.slice(
        start,
        start + perPage.value
      );
    });
    watch(
      [q, sort, viewMode],
      () => {
        frontendCurrentPage.value = 1;
      }
    );
    watch(
      frontendCurrentPage,
      () => {
        if (!props.useServerProcessing) {
          scrollToTarget();
        }
      }
    );
    const currentPage = computed(() => {
      var _a2, _b2, _c2;
      return Number(
        ((_b2 = (_a2 = props.assignments) == null ? void 0 : _a2.meta) == null ? void 0 : _b2.current_page) ?? ((_c2 = props.assignments) == null ? void 0 : _c2.current_page) ?? 1
      ) || 1;
    });
    const lastPage = computed(() => {
      var _a2, _b2, _c2;
      return Number(
        ((_b2 = (_a2 = props.assignments) == null ? void 0 : _a2.meta) == null ? void 0 : _b2.last_page) ?? ((_c2 = props.assignments) == null ? void 0 : _c2.last_page) ?? 1
      ) || 1;
    });
    const indexRoute = () => {
      return route(
        "public.schoolAssignments.index"
      );
    };
    const reloadAssignments = (page2 = 1) => {
      router.get(
        indexRoute(),
        {
          q: q.value || void 0,
          sort: sort.value || props.defaultSort || void 0,
          view: viewMode.value || void 0,
          page: page2
        },
        {
          preserveState: true,
          replace: true,
          preserveScroll: true
        }
      );
    };
    const submitSearch = () => {
      reloadAssignments(1);
    };
    const resetSearch = () => {
      q.value = "";
      sort.value = props.defaultSort || "";
      frontendCurrentPage.value = 1;
      if (props.useServerProcessing) {
        reloadAssignments(1);
      }
    };
    const updateSort = (value) => {
      sort.value = value || props.defaultSort || "";
      frontendCurrentPage.value = 1;
      if (props.useServerProcessing) {
        reloadAssignments(1);
      }
    };
    const updateViewMode = (value) => {
      viewMode.value = value || "grid";
      frontendCurrentPage.value = 1;
      if (props.useServerProcessing) {
        reloadAssignments(1);
      }
    };
    const goToPage = (page2) => {
      const value = Number(page2);
      if (!Number.isFinite(value)) {
        return;
      }
      const safePage = Math.max(
        1,
        Math.min(
          value,
          lastPage.value
        )
      );
      reloadAssignments(
        safePage
      );
    };
    const goPrev = () => {
      if (currentPage.value <= 1) {
        return;
      }
      goToPage(
        currentPage.value - 1
      );
    };
    const goNext = () => {
      if (currentPage.value >= lastPage.value) {
        return;
      }
      goToPage(
        currentPage.value + 1
      );
    };
    const displayedAssignments = computed(() => {
      return props.useServerProcessing ? assignmentsData.value : frontendPaginatedAssignments.value;
    });
    const mainVideosList = computed(() => {
      return normalizeList(
        props.mainVideos
      );
    });
    const mainBannersList = computed(() => {
      return normalizeList(
        props.mainBanners
      );
    });
    return (_ctx, _push, _parent, _attrs) => {
      _push(`<!--[-->`);
      _push(ssrRenderComponent(unref(Head), null, {
        default: withCtx((_, _push2, _parent2, _scopeId) => {
          var _a2, _b2, _c2, _d2, _e, _f, _g, _h, _i, _j, _k, _l, _m, _n, _o, _p, _q, _r, _s, _t;
          if (_push2) {
            _push2(`<title${_scopeId}>
            ${ssrInterpolate(((_a2 = __props.seo) == null ? void 0 : _a2.title) || unref(t)("assignments"))}
        </title><meta name="title"${ssrRenderAttr("content", ((_b2 = __props.seo) == null ? void 0 : _b2.title) || unref(t)("assignments"))}${_scopeId}><meta name="keywords"${ssrRenderAttr("content", ((_c2 = __props.seo) == null ? void 0 : _c2.keywords) || "")}${_scopeId}><meta name="description"${ssrRenderAttr("content", ((_d2 = __props.seo) == null ? void 0 : _d2.description) || unref(t)("assignments"))}${_scopeId}><meta property="og:title"${ssrRenderAttr("content", ((_e = __props.seo) == null ? void 0 : _e.title) || unref(t)("assignments"))}${_scopeId}><meta property="og:description"${ssrRenderAttr("content", ((_f = __props.seo) == null ? void 0 : _f.description) || unref(t)("assignments"))}${_scopeId}><meta property="og:type" content="website"${_scopeId}><meta property="og:url"${ssrRenderAttr("content", `/${__props.locale}/school/assignments`)}${_scopeId}><meta property="og:image" content=""${_scopeId}><meta property="og:locale"${ssrRenderAttr("content", __props.locale)}${_scopeId}><meta name="twitter:card" content="summary_large_image"${_scopeId}><meta name="twitter:title"${ssrRenderAttr("content", ((_g = __props.seo) == null ? void 0 : _g.title) || unref(t)("assignments"))}${_scopeId}><meta name="twitter:description"${ssrRenderAttr("content", ((_h = __props.seo) == null ? void 0 : _h.description) || unref(t)("assignments"))}${_scopeId}><meta name="twitter:image" content=""${_scopeId}><meta name="DC.title"${ssrRenderAttr("content", ((_i = __props.seo) == null ? void 0 : _i.title) || unref(t)("assignments"))}${_scopeId}><meta name="DC.description"${ssrRenderAttr("content", ((_j = __props.seo) == null ? void 0 : _j.description) || unref(t)("assignments"))}${_scopeId}><meta name="DC.identifier"${ssrRenderAttr("content", `/${__props.locale}/school/assignments`)}${_scopeId}><meta name="DC.language"${ssrRenderAttr("content", __props.locale)}${_scopeId}>`);
          } else {
            return [
              createVNode("title", null, "\n            " + toDisplayString(((_k = __props.seo) == null ? void 0 : _k.title) || unref(t)("assignments")) + "\n        ", 1),
              createVNode("meta", {
                name: "title",
                content: ((_l = __props.seo) == null ? void 0 : _l.title) || unref(t)("assignments")
              }, null, 8, ["content"]),
              createVNode("meta", {
                name: "keywords",
                content: ((_m = __props.seo) == null ? void 0 : _m.keywords) || ""
              }, null, 8, ["content"]),
              createVNode("meta", {
                name: "description",
                content: ((_n = __props.seo) == null ? void 0 : _n.description) || unref(t)("assignments")
              }, null, 8, ["content"]),
              createVNode("meta", {
                property: "og:title",
                content: ((_o = __props.seo) == null ? void 0 : _o.title) || unref(t)("assignments")
              }, null, 8, ["content"]),
              createVNode("meta", {
                property: "og:description",
                content: ((_p = __props.seo) == null ? void 0 : _p.description) || unref(t)("assignments")
              }, null, 8, ["content"]),
              createVNode("meta", {
                property: "og:type",
                content: "website"
              }),
              createVNode("meta", {
                property: "og:url",
                content: `/${__props.locale}/school/assignments`
              }, null, 8, ["content"]),
              createVNode("meta", {
                property: "og:image",
                content: ""
              }),
              createVNode("meta", {
                property: "og:locale",
                content: __props.locale
              }, null, 8, ["content"]),
              createVNode("meta", {
                name: "twitter:card",
                content: "summary_large_image"
              }),
              createVNode("meta", {
                name: "twitter:title",
                content: ((_q = __props.seo) == null ? void 0 : _q.title) || unref(t)("assignments")
              }, null, 8, ["content"]),
              createVNode("meta", {
                name: "twitter:description",
                content: ((_r = __props.seo) == null ? void 0 : _r.description) || unref(t)("assignments")
              }, null, 8, ["content"]),
              createVNode("meta", {
                name: "twitter:image",
                content: ""
              }),
              createVNode("meta", {
                name: "DC.title",
                content: ((_s = __props.seo) == null ? void 0 : _s.title) || unref(t)("assignments")
              }, null, 8, ["content"]),
              createVNode("meta", {
                name: "DC.description",
                content: ((_t = __props.seo) == null ? void 0 : _t.description) || unref(t)("assignments")
              }, null, 8, ["content"]),
              createVNode("meta", {
                name: "DC.identifier",
                content: `/${__props.locale}/school/assignments`
              }, null, 8, ["content"]),
              createVNode("meta", {
                name: "DC.language",
                content: __props.locale
              }, null, 8, ["content"])
            ];
          }
        }),
        _: 1
      }, _parent));
      _push(ssrRenderComponent(_sfc_main$3, {
        title: __props.title,
        "can-login": __props.canLogin,
        "can-register": __props.canRegister
      }, {
        default: withCtx((_, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(ssrRenderComponent(_sfc_main$4, null, null, _parent2, _scopeId));
            _push2(`<main class="min-h-screen px-1 lg:px-6 max-w-full"${_scopeId}><div class="mx-auto tracking-wider pt-20 lg:pt-44"${_scopeId}><div class="ext-color w-full min-w-0 py-3 px-1 flex flex-col lg:flex-row gap-4 rounded-3xl border-2 border-slate-300 dark:border-slate-500"${_scopeId}>`);
            if (showLeft.value) {
              _push2(`<aside class="${ssrRenderClass([
                leftCollapsed.value ? "lg:w-6" : "lg:w-72",
                "shrink-0 transition-all duration-300"
              ])}"${_scopeId}>`);
              _push2(ssrRenderComponent(_sfc_main$5, {
                "track-tree": trackTree.value,
                collapsed: leftCollapsed.value,
                onCollapsed: ($event) => leftCollapsed.value = $event
              }, null, _parent2, _scopeId));
              _push2(`</aside>`);
            } else {
              _push2(`<!---->`);
            }
            _push2(`<div class="w-full pb-6 slate-1"${_scopeId}><div class="mx-auto max-w-6xl"${_scopeId}><nav class="text-sm" aria-label="Breadcrumb"${_scopeId}><ol class="flex flex-wrap items-center font-semibold"${_scopeId}><li${_scopeId}>`);
            _push2(ssrRenderComponent(unref(Link), {
              href: _ctx.route("home"),
              class: "breadcrumb-link hover:underline"
            }, {
              default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                if (_push3) {
                  _push3(`${ssrInterpolate(unref(t)("home"))}`);
                } else {
                  return [
                    createTextVNode(toDisplayString(unref(t)("home")), 1)
                  ];
                }
              }),
              _: 1
            }, _parent2, _scopeId));
            _push2(`</li><li${_scopeId}><span class="mx-2 breadcrumbs"${_scopeId}> / </span></li><li${_scopeId}>`);
            _push2(ssrRenderComponent(unref(Link), {
              href: _ctx.route(
                "public.schoolTracks.index"
              ),
              class: "breadcrumb-link hover:underline"
            }, {
              default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                if (_push3) {
                  _push3(`${ssrInterpolate(unref(t)("tracks"))}`);
                } else {
                  return [
                    createTextVNode(toDisplayString(unref(t)("tracks")), 1)
                  ];
                }
              }),
              _: 1
            }, _parent2, _scopeId));
            _push2(`</li><li${_scopeId}><span class="mx-2 breadcrumbs"${_scopeId}> / </span></li><li${_scopeId}>`);
            _push2(ssrRenderComponent(unref(Link), {
              href: _ctx.route(
                "public.schoolCourses.index"
              ),
              class: "breadcrumb-link hover:underline"
            }, {
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
            _push2(`</li><li${_scopeId}><span class="mx-2 breadcrumbs"${_scopeId}> / </span></li><li${_scopeId}>`);
            _push2(ssrRenderComponent(unref(Link), {
              href: _ctx.route(
                "public.schoolModules.index"
              ),
              class: "breadcrumb-link hover:underline"
            }, {
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
            _push2(`</li><li${_scopeId}><span class="mx-2 breadcrumbs"${_scopeId}> / </span></li><li${_scopeId}>`);
            _push2(ssrRenderComponent(unref(Link), {
              href: _ctx.route(
                "public.schoolLessons.index"
              ),
              class: "breadcrumb-link hover:underline"
            }, {
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
            _push2(`</li><li${_scopeId}><span class="mx-2 breadcrumbs"${_scopeId}> / </span></li><li class="breadcrumbs"${_scopeId}>${ssrInterpolate(unref(t)("assignments"))}</li></ol></nav><div class="my-3 flex flex-wrap items-center justify-center gap-3 title"${_scopeId}><svg class="shrink-0 h-5 w-5 text-slate-600/85 dark:text-slate-200/85" fill="currentColor" viewBox="0 0 24 24"${_scopeId}><path d="M15,18v2H9v-2H1v5c0,0.552,0.448,1,1,1h20c0.552,0,1-0.448,1-1v-5H15z"${_scopeId}></path><path d="M23,4h-6V1c0-0.552-0.448-1-1-1H8C7.448,0,7,0.448,7,1v3H1C0.448,4,0,4.448,0,5v10c0,0.552,0.448,1,1,1h8v-3 h6v3h8c0.552,0,1-0.448,1-1V5C24,4.448,23.552,4,23,4z M15,4H9V2h6V4z"${_scopeId}></path></svg><h1 class="text-2xl font-bold"${_scopeId}>${ssrInterpolate(unref(t)("assignments"))}</h1></div><div class="my-1 text-sm subtitle text-center"${_scopeId}>${ssrInterpolate(unref(t)("assignments"))}</div>`);
            if (__props.useServerProcessing) {
              _push2(ssrRenderComponent(_sfc_main$6, {
                modelValue: q.value,
                "onUpdate:modelValue": ($event) => q.value = $event,
                found: __props.assignmentsFound,
                "view-mode": viewMode.value,
                "sort-value": sort.value,
                "sort-options": assignmentSortOptions,
                "default-sort": __props.defaultSort,
                "found-label": unref(t)("assignments"),
                "search-placeholder": unref(t)("searchByName"),
                onSubmit: submitSearch,
                onReset: resetSearch,
                "onUpdate:viewMode": updateViewMode,
                "onUpdate:sortValue": updateSort
              }, null, _parent2, _scopeId));
            } else {
              _push2(ssrRenderComponent(_sfc_main$7, {
                modelValue: q.value,
                "onUpdate:modelValue": ($event) => q.value = $event,
                found: sortedAssignments.value.length,
                "view-mode": viewMode.value,
                "sort-value": sort.value,
                "sort-options": assignmentSortOptions,
                "default-sort": __props.defaultSort,
                "found-label": unref(t)("assignments"),
                "search-placeholder": unref(t)("searchByName"),
                onReset: resetSearch,
                "onUpdate:viewMode": updateViewMode,
                "onUpdate:sortValue": updateSort
              }, null, _parent2, _scopeId));
            }
            _push2(`<div${_scopeId}></div>`);
            if (displayedAssignments.value.length === 0) {
              _push2(`<div class="mt-6 text-center text-slate-700 dark:text-slate-300"${_scopeId}>${ssrInterpolate(unref(t)("noData"))}</div>`);
            } else {
              _push2(`<div${_scopeId}>`);
              if (viewMode.value === "grid") {
                _push2(ssrRenderComponent(_sfc_main$2, {
                  assignments: displayedAssignments.value,
                  cols: gridCols.value
                }, null, _parent2, _scopeId));
              } else {
                _push2(ssrRenderComponent(_sfc_main$1, {
                  assignments: displayedAssignments.value
                }, null, _parent2, _scopeId));
              }
              _push2(`</div>`);
            }
            if (__props.useServerProcessing) {
              _push2(ssrRenderComponent(_sfc_main$8, {
                "current-page": currentPage.value,
                "last-page": lastPage.value,
                found: __props.assignmentsFound,
                onPrev: goPrev,
                onNext: goNext,
                onGo: goToPage
              }, null, _parent2, _scopeId));
            } else {
              _push2(ssrRenderComponent(_sfc_main$9, {
                currentPage: frontendCurrentPage.value,
                "onUpdate:currentPage": ($event) => frontendCurrentPage.value = $event,
                "items-per-page": perPage.value,
                "total-items": sortedAssignments.value.length
              }, null, _parent2, _scopeId));
            }
            _push2(ssrRenderComponent(_sfc_main$a, { videos: mainVideosList.value }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(SectionBanners, { banners: mainBannersList.value }, null, _parent2, _scopeId));
            _push2(`</div></div>`);
            if (showRight.value) {
              _push2(`<aside class="${ssrRenderClass([
                rightCollapsed.value ? "lg:w-6" : "lg:w-72",
                "shrink-0 transition-all duration-300"
              ])}"${_scopeId}>`);
              _push2(ssrRenderComponent(_sfc_main$b, {
                collapsed: rightCollapsed.value,
                onCollapsed: ($event) => rightCollapsed.value = $event
              }, null, _parent2, _scopeId));
              _push2(`</aside>`);
            } else {
              _push2(`<!---->`);
            }
            _push2(`</div></div></main>`);
            _push2(ssrRenderComponent(_sfc_main$c, null, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(Progress, null, null, _parent2, _scopeId));
            if (isAdmin.value) {
              _push2(ssrRenderComponent(_sfc_main$d, {
                "setting-key": "publicSchoolAssignmentsProcessingMode",
                mode: __props.publicSchoolAssignmentsProcessingMode,
                "use-server-processing": __props.useServerProcessing,
                total: __props.assignmentsCount
              }, null, _parent2, _scopeId));
            } else {
              _push2(`<!---->`);
            }
          } else {
            return [
              createVNode(_sfc_main$4),
              createVNode("main", { class: "min-h-screen px-1 lg:px-6 max-w-full" }, [
                createVNode("div", { class: "mx-auto tracking-wider pt-20 lg:pt-44" }, [
                  createVNode("div", { class: "ext-color w-full min-w-0 py-3 px-1 flex flex-col lg:flex-row gap-4 rounded-3xl border-2 border-slate-300 dark:border-slate-500" }, [
                    showLeft.value ? (openBlock(), createBlock("aside", {
                      key: 0,
                      class: [
                        "shrink-0 transition-all duration-300",
                        leftCollapsed.value ? "lg:w-6" : "lg:w-72"
                      ]
                    }, [
                      createVNode(_sfc_main$5, {
                        "track-tree": trackTree.value,
                        collapsed: leftCollapsed.value,
                        onCollapsed: ($event) => leftCollapsed.value = $event
                      }, null, 8, ["track-tree", "collapsed", "onCollapsed"])
                    ], 2)) : createCommentVNode("", true),
                    createVNode("div", { class: "w-full pb-6 slate-1" }, [
                      createVNode("div", { class: "mx-auto max-w-6xl" }, [
                        createVNode("nav", {
                          class: "text-sm",
                          "aria-label": "Breadcrumb"
                        }, [
                          createVNode("ol", { class: "flex flex-wrap items-center font-semibold" }, [
                            createVNode("li", null, [
                              createVNode(unref(Link), {
                                href: _ctx.route("home"),
                                class: "breadcrumb-link hover:underline"
                              }, {
                                default: withCtx(() => [
                                  createTextVNode(toDisplayString(unref(t)("home")), 1)
                                ]),
                                _: 1
                              }, 8, ["href"])
                            ]),
                            createVNode("li", null, [
                              createVNode("span", { class: "mx-2 breadcrumbs" }, " / ")
                            ]),
                            createVNode("li", null, [
                              createVNode(unref(Link), {
                                href: _ctx.route(
                                  "public.schoolTracks.index"
                                ),
                                class: "breadcrumb-link hover:underline"
                              }, {
                                default: withCtx(() => [
                                  createTextVNode(toDisplayString(unref(t)("tracks")), 1)
                                ]),
                                _: 1
                              }, 8, ["href"])
                            ]),
                            createVNode("li", null, [
                              createVNode("span", { class: "mx-2 breadcrumbs" }, " / ")
                            ]),
                            createVNode("li", null, [
                              createVNode(unref(Link), {
                                href: _ctx.route(
                                  "public.schoolCourses.index"
                                ),
                                class: "breadcrumb-link hover:underline"
                              }, {
                                default: withCtx(() => [
                                  createTextVNode(toDisplayString(unref(t)("courses")), 1)
                                ]),
                                _: 1
                              }, 8, ["href"])
                            ]),
                            createVNode("li", null, [
                              createVNode("span", { class: "mx-2 breadcrumbs" }, " / ")
                            ]),
                            createVNode("li", null, [
                              createVNode(unref(Link), {
                                href: _ctx.route(
                                  "public.schoolModules.index"
                                ),
                                class: "breadcrumb-link hover:underline"
                              }, {
                                default: withCtx(() => [
                                  createTextVNode(toDisplayString(unref(t)("modules")), 1)
                                ]),
                                _: 1
                              }, 8, ["href"])
                            ]),
                            createVNode("li", null, [
                              createVNode("span", { class: "mx-2 breadcrumbs" }, " / ")
                            ]),
                            createVNode("li", null, [
                              createVNode(unref(Link), {
                                href: _ctx.route(
                                  "public.schoolLessons.index"
                                ),
                                class: "breadcrumb-link hover:underline"
                              }, {
                                default: withCtx(() => [
                                  createTextVNode(toDisplayString(unref(t)("lessons")), 1)
                                ]),
                                _: 1
                              }, 8, ["href"])
                            ]),
                            createVNode("li", null, [
                              createVNode("span", { class: "mx-2 breadcrumbs" }, " / ")
                            ]),
                            createVNode("li", { class: "breadcrumbs" }, toDisplayString(unref(t)("assignments")), 1)
                          ])
                        ]),
                        createVNode("div", { class: "my-3 flex flex-wrap items-center justify-center gap-3 title" }, [
                          (openBlock(), createBlock("svg", {
                            class: "shrink-0 h-5 w-5 text-slate-600/85 dark:text-slate-200/85",
                            fill: "currentColor",
                            viewBox: "0 0 24 24"
                          }, [
                            createVNode("path", { d: "M15,18v2H9v-2H1v5c0,0.552,0.448,1,1,1h20c0.552,0,1-0.448,1-1v-5H15z" }),
                            createVNode("path", { d: "M23,4h-6V1c0-0.552-0.448-1-1-1H8C7.448,0,7,0.448,7,1v3H1C0.448,4,0,4.448,0,5v10c0,0.552,0.448,1,1,1h8v-3 h6v3h8c0.552,0,1-0.448,1-1V5C24,4.448,23.552,4,23,4z M15,4H9V2h6V4z" })
                          ])),
                          createVNode("h1", { class: "text-2xl font-bold" }, toDisplayString(unref(t)("assignments")), 1)
                        ]),
                        createVNode("div", { class: "my-1 text-sm subtitle text-center" }, toDisplayString(unref(t)("assignments")), 1),
                        __props.useServerProcessing ? (openBlock(), createBlock(_sfc_main$6, {
                          key: 0,
                          modelValue: q.value,
                          "onUpdate:modelValue": ($event) => q.value = $event,
                          found: __props.assignmentsFound,
                          "view-mode": viewMode.value,
                          "sort-value": sort.value,
                          "sort-options": assignmentSortOptions,
                          "default-sort": __props.defaultSort,
                          "found-label": unref(t)("assignments"),
                          "search-placeholder": unref(t)("searchByName"),
                          onSubmit: submitSearch,
                          onReset: resetSearch,
                          "onUpdate:viewMode": updateViewMode,
                          "onUpdate:sortValue": updateSort
                        }, null, 8, ["modelValue", "onUpdate:modelValue", "found", "view-mode", "sort-value", "default-sort", "found-label", "search-placeholder"])) : (openBlock(), createBlock(_sfc_main$7, {
                          key: 1,
                          modelValue: q.value,
                          "onUpdate:modelValue": ($event) => q.value = $event,
                          found: sortedAssignments.value.length,
                          "view-mode": viewMode.value,
                          "sort-value": sort.value,
                          "sort-options": assignmentSortOptions,
                          "default-sort": __props.defaultSort,
                          "found-label": unref(t)("assignments"),
                          "search-placeholder": unref(t)("searchByName"),
                          onReset: resetSearch,
                          "onUpdate:viewMode": updateViewMode,
                          "onUpdate:sortValue": updateSort
                        }, null, 8, ["modelValue", "onUpdate:modelValue", "found", "view-mode", "sort-value", "default-sort", "found-label", "search-placeholder"])),
                        createVNode("div", {
                          ref_key: "scrollTarget",
                          ref: scrollTarget
                        }, null, 512),
                        displayedAssignments.value.length === 0 ? (openBlock(), createBlock("div", {
                          key: 2,
                          class: "mt-6 text-center text-slate-700 dark:text-slate-300"
                        }, toDisplayString(unref(t)("noData")), 1)) : (openBlock(), createBlock("div", { key: 3 }, [
                          viewMode.value === "grid" ? (openBlock(), createBlock(_sfc_main$2, {
                            key: 0,
                            assignments: displayedAssignments.value,
                            cols: gridCols.value
                          }, null, 8, ["assignments", "cols"])) : (openBlock(), createBlock(_sfc_main$1, {
                            key: 1,
                            assignments: displayedAssignments.value
                          }, null, 8, ["assignments"]))
                        ])),
                        __props.useServerProcessing ? (openBlock(), createBlock(_sfc_main$8, {
                          key: 4,
                          "current-page": currentPage.value,
                          "last-page": lastPage.value,
                          found: __props.assignmentsFound,
                          onPrev: goPrev,
                          onNext: goNext,
                          onGo: goToPage
                        }, null, 8, ["current-page", "last-page", "found"])) : (openBlock(), createBlock(_sfc_main$9, {
                          key: 5,
                          currentPage: frontendCurrentPage.value,
                          "onUpdate:currentPage": ($event) => frontendCurrentPage.value = $event,
                          "items-per-page": perPage.value,
                          "total-items": sortedAssignments.value.length
                        }, null, 8, ["currentPage", "onUpdate:currentPage", "items-per-page", "total-items"])),
                        createVNode(_sfc_main$a, { videos: mainVideosList.value }, null, 8, ["videos"]),
                        createVNode(SectionBanners, { banners: mainBannersList.value }, null, 8, ["banners"])
                      ])
                    ]),
                    showRight.value ? (openBlock(), createBlock("aside", {
                      key: 1,
                      class: [
                        "shrink-0 transition-all duration-300",
                        rightCollapsed.value ? "lg:w-6" : "lg:w-72"
                      ]
                    }, [
                      createVNode(_sfc_main$b, {
                        collapsed: rightCollapsed.value,
                        onCollapsed: ($event) => rightCollapsed.value = $event
                      }, null, 8, ["collapsed", "onCollapsed"])
                    ], 2)) : createCommentVNode("", true)
                  ])
                ])
              ]),
              createVNode(_sfc_main$c),
              createVNode(Progress),
              isAdmin.value ? (openBlock(), createBlock(_sfc_main$d, {
                key: 0,
                "setting-key": "publicSchoolAssignmentsProcessingMode",
                mode: __props.publicSchoolAssignmentsProcessingMode,
                "use-server-processing": __props.useServerProcessing,
                total: __props.assignmentsCount
              }, null, 8, ["mode", "use-server-processing", "total"])) : createCommentVNode("", true)
            ];
          }
        }),
        _: 1
      }, _parent));
      _push(`<!--]-->`);
    };
  }
};
const _sfc_setup = _sfc_main.setup;
_sfc_main.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Pages/Public/Default/School/SchoolAssignments/Index.vue");
  return _sfc_setup ? _sfc_setup(props, ctx) : void 0;
};
export {
  _sfc_main as default
};
