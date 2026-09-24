import { computed, mergeProps, unref, withCtx, createVNode, toDisplayString, createBlock, openBlock, useSSRContext, ref, onMounted, watch, resolveDynamicComponent, createCommentVNode } from "vue";
import { ssrRenderAttrs, ssrRenderList, ssrRenderComponent, ssrInterpolate, ssrRenderAttr, ssrRenderVNode, ssrRenderClass } from "vue/server-renderer";
import { Link, usePage, Head, router } from "@inertiajs/vue3";
import { useI18n } from "vue-i18n";
import { u as useSmoothScrollTo } from "./useSmoothScrollTo-g9G3kyDv.js";
import { _ as _sfc_main$3 } from "./DefaultLayout-6zxGxPtK.js";
import { _ as _sfc_main$4, a as _sfc_main$b, P as Progress } from "./Progress-CyfOKcqP.js";
import { _ as _sfc_main$5, a as _sfc_main$a } from "./RightSidebarSchool-DlAegojf.js";
import { _ as _sfc_main$6 } from "./EntityPageToolbar-DT32FtSd.js";
import { _ as _sfc_main$7, a as _sfc_main$8, b as _sfc_main$c } from "./PublicAdminBottomPanel-BhLEQMoJ.js";
import { _ as _sfc_main$9, S as SectionBanners } from "./SectionBanners-Cdgmr0Bw.js";
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
  __name: "InstructorGrid",
  __ssrInlineRender: true,
  props: {
    instructors: { type: Array, default: () => [] },
    cols: { type: Number, default: 2 }
  },
  setup(__props) {
    const { t } = useI18n();
    const props = __props;
    const gridClass = computed(() => {
      switch (props.cols) {
        case 4:
          return "grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4";
        case 3:
          return "grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-3";
        case 2:
        default:
          return "grid grid-cols-1 gap-4 sm:grid-cols-2";
      }
    });
    const showRoute = (instructor) => {
      return route("public.schoolInstructors.show", { slug: instructor.slug });
    };
    const getInstructorName = (instructor) => {
      var _a, _b;
      return ((_a = instructor == null ? void 0 : instructor.translation) == null ? void 0 : _a.title) || ((_b = instructor == null ? void 0 : instructor.user) == null ? void 0 : _b.name) || t("instructor");
    };
    const getInstructorShort = (instructor) => {
      var _a;
      return ((_a = instructor == null ? void 0 : instructor.translation) == null ? void 0 : _a.short) || "";
    };
    const getRatingText = (instructor) => {
      const avg = instructor == null ? void 0 : instructor.rating_avg;
      const count = Number(
        (instructor == null ? void 0 : instructor.rating_count) ?? 0
      );
      if (avg === null || avg === void 0) {
        return "—";
      }
      return `${Number(avg).toFixed(1)} (${count})`;
    };
    const hasExperience = (instructor) => {
      return Number((instructor == null ? void 0 : instructor.experience_years) ?? 0) > 0;
    };
    const hasCourses = (instructor) => {
      return Number((instructor == null ? void 0 : instructor.courses_count) ?? 0) > 0;
    };
    const hasViews = (instructor) => {
      return Number((instructor == null ? void 0 : instructor.views) ?? 0) > 0;
    };
    return (_ctx, _push, _parent, _attrs) => {
      _push(`<div${ssrRenderAttrs(mergeProps({ class: gridClass.value }, _attrs))}><!--[-->`);
      ssrRenderList(__props.instructors, (instructor) => {
        _push(`<div class="group flex h-full flex-col overflow-hidden rounded-md border border-gray-200 bg-white shadow-sm transition hover:-translate-y-0.5 hover:shadow-md dark:border-gray-700 dark:bg-gray-900">`);
        _push(ssrRenderComponent(unref(Link), {
          href: showRoute(instructor)
        }, {
          default: withCtx((_, _push2, _parent2, _scopeId) => {
            if (_push2) {
              _push2(ssrRenderComponent(UniversalImageSlider, {
                entity: instructor,
                "height-class": "h-48",
                "rounded-class": "",
                "wrapper-class": "",
                "img-class": "w-full h-full object-cover transition\n                               duration-300 group-hover:scale-105"
              }, null, _parent2, _scopeId));
            } else {
              return [
                createVNode(UniversalImageSlider, {
                  entity: instructor,
                  "height-class": "h-48",
                  "rounded-class": "",
                  "wrapper-class": "",
                  "img-class": "w-full h-full object-cover transition\n                               duration-300 group-hover:scale-105"
                }, null, 8, ["entity"])
              ];
            }
          }),
          _: 2
        }, _parent));
        _push(`<div class="flex flex-1 flex-col p-4"><div class="flex items-center justify-between gap-2">`);
        _push(ssrRenderComponent(unref(Link), {
          href: showRoute(instructor),
          class: "inline-flex items-center gap-1"
        }, {
          default: withCtx((_, _push2, _parent2, _scopeId) => {
            if (_push2) {
              _push2(`<span class="text-lg font-semibold text-slate-900/85 group-hover:opacity-75 dark:text-slate-100/85 dark:group-hover:opacity-75"${_scopeId}>${ssrInterpolate(getInstructorName(instructor))}</span>`);
            } else {
              return [
                createVNode("span", { class: "text-lg font-semibold text-slate-900/85 group-hover:opacity-75 dark:text-slate-100/85 dark:group-hover:opacity-75" }, toDisplayString(getInstructorName(instructor)), 1)
              ];
            }
          }),
          _: 2
        }, _parent));
        if (hasViews) {
          _push(`<div class="inline-flex items-center gap-2"><svg class="h-4 w-4 text-slate-600/85 dark:text-slate-200/85" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512" fill="currentColor"><path d="M569.354 231.631C512.97 135.949 407.81 72 288 72 168.14 72 63.004 135.994 6.646 231.631a47.999 47.999 0 0 0 0 48.739C63.031 376.051 168.19 440 288 440c119.86 0 224.996-63.994 281.354-159.631a47.997 47.997 0 0 0 0-48.738zM288 392c-102.556 0-192.091-54.701-240-136 44.157-74.933 123.677-127.27 216.162-135.007C273.958 131.078 280 144.83 280 160c0 30.928-25.072 56-56 56s-56-25.072-56-56l.001-.042C157.794 179.043 152 200.844 152 224c0 75.111 60.889 136 136 136s136-60.889 136-136c0-31.031-10.4-59.629-27.895-82.515C451.704 164.638 498.009 205.106 528 256c-47.908 81.299-137.444 136-240 136z"></path></svg><span class="text-xs font-semibold text-slate-500 dark:text-slate-400">${ssrInterpolate(instructor.views)}</span></div>`);
        } else {
          _push(`<!---->`);
        }
        _push(`</div>`);
        if (getInstructorShort(instructor)) {
          _push(`<div class="mt-2 line-clamp-2 text-sm text-slate-700 dark:text-slate-300">${ssrInterpolate(getInstructorShort(instructor))}</div>`);
        } else {
          _push(`<!---->`);
        }
        _push(`<div class="mt-3 flex flex-wrap items-center justify-center gap-2 text-xs font-semibold text-slate-500 dark:text-slate-400">`);
        if (hasExperience) {
          _push(`<div class="flex items-center justify-center gap-1 px-2 py-1 rounded-sm border border-slate-600 dark:border-slate-400"><svg viewBox="0 0 24 24" class="shrink-0 h-3 w-3"><path class="fill-current text-red-400 dark:text-red-300" d="M14.586,9.439S15.7,2.858,11.138,0A8.055,8.055,0,0,1,8.1,5.831C6.149,7.546,2.481,11.4,2.52,15.51A9.435,9.435,0,0,0,7.7,24a5.975,5.975,0,0,1,2.091-4.132,4.877,4.877,0,0,0,1.869-3.278,8.786,8.786,0,0,1,4.652,7.322v.02a8.827,8.827,0,0,0,5.137-7.659c.324-3.863-1.792-9.112-3.668-10.828A10.192,10.192,0,0,1,14.586,9.439Z"></path></svg> ${ssrInterpolate(unref(t)("experienceYears"))}: ${ssrInterpolate(instructor.experience_years)}</div>`);
        } else {
          _push(`<!---->`);
        }
        _push(`<div class="flex items-center justify-center gap-1 px-2 py-1 rounded-sm border border-slate-600 dark:border-slate-400"><svg viewBox="0 0 24 24" class="shrink-0 h-3 w-3"><path class="fill-current text-red-400 dark:text-red-300" d="M12.746,1.464l3.11,6.3L22.81,8.776a.831.831,0,0,1,.461,1.418l-5.033,4.9,1.188,6.926a.832.832,0,0,1-1.207.877L12,19.632,5.78,22.9a.833.833,0,0,1-1.207-.878L5.761,15.1l-5.033-4.9a.831.831,0,0,1,.461-1.418L8.143,7.765l3.11-6.3A.833.833,0,0,1,12.746,1.464Z"></path></svg> ${ssrInterpolate(unref(t)("rating"))}: ${ssrInterpolate(getRatingText(instructor))}</div>`);
        if (hasCourses) {
          _push(`<div class="flex items-center justify-center gap-1 px-2 py-1 rounded-sm border border-slate-600 dark:border-slate-400"><svg class="shrink-0 h-3 w-3 text-sky-600/85 dark:text-sky-200/85" fill="currentColor" viewBox="0 0 448 512"><path d="M318.38 208h-39.09c-1.49 27.03-6.54 51.35-14.21 70.41 27.71-13.24 48.02-39.19 53.3-70.41zm0-32c-5.29-31.22-25.59-57.17-53.3-70.41 7.68 19.06 12.72 43.38 14.21 70.41h39.09zM224 97.31c-7.69 7.45-20.77 34.42-23.43 78.69h46.87c-2.67-44.26-15.75-71.24-23.44-78.69zm-41.08 8.28c-27.71 13.24-48.02 39.19-53.3 70.41h39.09c1.49-27.03 6.53-51.35 14.21-70.41zm0 172.82c-7.68-19.06-12.72-43.38-14.21-70.41h-39.09c5.28 31.22 25.59 57.17 53.3 70.41zM247.43 208h-46.87c2.66 44.26 15.74 71.24 23.43 78.69 7.7-7.45 20.78-34.43 23.44-78.69zM448 358.4V25.6c0-16-9.6-25.6-25.6-25.6H96C41.6 0 0 41.6 0 96v320c0 54.4 41.6 96 96 96h326.4c12.8 0 25.6-9.6 25.6-25.6v-16c0-6.4-3.2-12.8-9.6-19.2-3.2-16-3.2-60.8 0-73.6 6.4-3.2 9.6-9.6 9.6-19.2zM224 64c70.69 0 128 57.31 128 128s-57.31 128-128 128S96 262.69 96 192 153.31 64 224 64zm160 384H96c-19.2 0-32-12.8-32-32s16-32 32-32h288v64z"></path></svg> ${ssrInterpolate(unref(t)("courses"))}: ${ssrInterpolate(instructor.courses_count)}</div>`);
        } else {
          _push(`<!---->`);
        }
        _push(`</div><div class="mt-auto pt-4">`);
        _push(ssrRenderComponent(unref(Link), {
          href: showRoute(instructor),
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
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Components/Public/Default/School/SchoolInstructor/InstructorGrid.vue");
  return _sfc_setup$2 ? _sfc_setup$2(props, ctx) : void 0;
};
const _sfc_main$1 = {
  __name: "InstructorRows",
  __ssrInlineRender: true,
  props: {
    instructors: {
      type: Array,
      default: () => []
    }
  },
  setup(__props) {
    const { t } = useI18n();
    const showRoute = (instructor) => {
      return route("public.schoolInstructors.show", { slug: instructor.slug });
    };
    const getInstructorName = (instructor) => {
      var _a, _b;
      return ((_a = instructor == null ? void 0 : instructor.translation) == null ? void 0 : _a.title) || ((_b = instructor == null ? void 0 : instructor.user) == null ? void 0 : _b.name) || t("instructor");
    };
    const getInstructorShort = (instructor) => {
      var _a;
      return ((_a = instructor == null ? void 0 : instructor.translation) == null ? void 0 : _a.short) || "";
    };
    const getRatingText = (instructor) => {
      const avg = instructor == null ? void 0 : instructor.rating_avg;
      const count = Number(
        (instructor == null ? void 0 : instructor.rating_count) ?? 0
      );
      if (avg === null || avg === void 0) {
        return "—";
      }
      return `${Number(avg).toFixed(1)} (${count})`;
    };
    const hasExperience = (instructor) => {
      return Number((instructor == null ? void 0 : instructor.experience_years) ?? 0) > 0;
    };
    const hasCourses = (instructor) => {
      return Number((instructor == null ? void 0 : instructor.courses_count) ?? 0) > 0;
    };
    const hasViews = (instructor) => {
      return Number((instructor == null ? void 0 : instructor.views) ?? 0) > 0;
    };
    return (_ctx, _push, _parent, _attrs) => {
      _push(`<div${ssrRenderAttrs(mergeProps({ class: "space-y-4" }, _attrs))}><!--[-->`);
      ssrRenderList(__props.instructors, (instructor) => {
        _push(`<div class="group flex flex-col sm:flex-row gap-3 rounded-md border border-gray-200 bg-white shadow-sm p-3 transition hover:shadow-md dark:border-gray-700 dark:bg-gray-900">`);
        _push(ssrRenderComponent(unref(Link), {
          class: "shrink-0",
          href: showRoute(instructor)
        }, {
          default: withCtx((_, _push2, _parent2, _scopeId) => {
            if (_push2) {
              _push2(ssrRenderComponent(UniversalImageSlider, {
                entity: instructor,
                "height-class": "h-44",
                "rounded-class": "rounded-md",
                "wrapper-class": "w-full sm:w-60 border border-gray-400 dark:border-gray-600",
                "img-class": "w-full h-full object-cover transition\n                               duration-300 group-hover:scale-105"
              }, null, _parent2, _scopeId));
            } else {
              return [
                createVNode(UniversalImageSlider, {
                  entity: instructor,
                  "height-class": "h-44",
                  "rounded-class": "rounded-md",
                  "wrapper-class": "w-full sm:w-60 border border-gray-400 dark:border-gray-600",
                  "img-class": "w-full h-full object-cover transition\n                               duration-300 group-hover:scale-105"
                }, null, 8, ["entity"])
              ];
            }
          }),
          _: 2
        }, _parent));
        _push(`<div class="min-w-0 flex-1 flex flex-col justify-around"><div class="min-w-0 flex-1 flex flex-col justify-around"><div class="flex items-start justify-between gap-3">`);
        _push(ssrRenderComponent(unref(Link), {
          href: showRoute(instructor),
          class: "min-w-0 inline-flex items-center gap-2",
          title: `${unref(t)("courses")}: ${instructor.courses_count}`
        }, {
          default: withCtx((_, _push2, _parent2, _scopeId) => {
            if (_push2) {
              _push2(`<span class="truncate text-lg font-semibold text-slate-900/85 dark:text-slate-100/85 group-hover:opacity-75"${_scopeId}>${ssrInterpolate(getInstructorName(instructor))}</span>`);
            } else {
              return [
                createVNode("span", { class: "truncate text-lg font-semibold text-slate-900/85 dark:text-slate-100/85 group-hover:opacity-75" }, toDisplayString(getInstructorName(instructor)), 1)
              ];
            }
          }),
          _: 2
        }, _parent));
        _push(`<div class="flex items-center gap-3 shrink-0">`);
        if (hasViews(instructor)) {
          _push(`<div class="inline-flex items-center gap-1"><svg class="h-4 w-4 text-slate-600/85 dark:text-slate-200/85" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512" fill="currentColor"><path d="M569.354 231.631C512.97 135.949 407.81 72 288 72 168.14 72 63.004 135.994 6.646 231.631a47.999 47.999 0 0 0 0 48.739C63.031 376.051 168.19 440 288 440c119.86 0 224.996-63.994 281.354-159.631a47.997 47.997 0 0 0 0-48.738zM288 392c-102.556 0-192.091-54.701-240-136 44.157-74.933 123.677-127.27 216.162-135.007C273.958 131.078 280 144.83 280 160c0 30.928-25.072 56-56 56s-56-25.072-56-56l.001-.042C157.794 179.043 152 200.844 152 224c0 75.111 60.889 136 136 136s136-60.889 136-136c0-31.031-10.4-59.629-27.895-82.515C451.704 164.638 498.009 205.106 528 256c-47.908 81.299-137.444 136-240 136z"></path></svg><span class="text-xs font-semibold text-slate-500 dark:text-slate-400">${ssrInterpolate(instructor.views)}</span></div>`);
        } else {
          _push(`<!---->`);
        }
        _push(`</div></div>`);
        if (getInstructorShort(instructor)) {
          _push(`<div class="mt-2 line-clamp-2 text-sm text-slate-700 dark:text-slate-300">${ssrInterpolate(getInstructorShort(instructor))}</div>`);
        } else {
          _push(`<!---->`);
        }
        _push(`</div><div class="mt-3 flex flex-wrap items-center justify-center gap-2 text-xs font-semibold text-slate-500 dark:text-slate-400">`);
        if (hasExperience(instructor)) {
          _push(`<div class="flex items-center justify-center gap-1 px-2 py-1 rounded-sm border border-slate-600 dark:border-slate-400"><svg viewBox="0 0 24 24" class="shrink-0 h-3 w-3"><path class="fill-current text-red-400 dark:text-red-300" d="M14.586,9.439S15.7,2.858,11.138,0A8.055,8.055,0,0,1,8.1,5.831C6.149,7.546,2.481,11.4,2.52,15.51A9.435,9.435,0,0,0,7.7,24a5.975,5.975,0,0,1,2.091-4.132,4.877,4.877,0,0,0,1.869-3.278,8.786,8.786,0,0,1,4.652,7.322v.02a8.827,8.827,0,0,0,5.137-7.659c.324-3.863-1.792-9.112-3.668-10.828A10.192,10.192,0,0,1,14.586,9.439Z"></path></svg> ${ssrInterpolate(unref(t)("experienceYears"))}: ${ssrInterpolate(instructor.experience_years)}</div>`);
        } else {
          _push(`<!---->`);
        }
        _push(`<div class="flex items-center justify-center gap-1 px-2 py-1 rounded-sm border border-slate-600 dark:border-slate-400"><svg viewBox="0 0 24 24" class="shrink-0 h-3 w-3"><path class="fill-current text-red-400 dark:text-red-300" d="M12.746,1.464l3.11,6.3L22.81,8.776a.831.831,0,0,1,.461,1.418l-5.033,4.9,1.188,6.926a.832.832,0,0,1-1.207.877L12,19.632,5.78,22.9a.833.833,0,0,1-1.207-.878L5.761,15.1l-5.033-4.9a.831.831,0,0,1,.461-1.418L8.143,7.765l3.11-6.3A.833.833,0,0,1,12.746,1.464Z"></path></svg> ${ssrInterpolate(unref(t)("rating"))}: ${ssrInterpolate(getRatingText(instructor))}</div>`);
        if (hasCourses(instructor)) {
          _push(`<div class="flex items-center justify-center gap-1 px-2 py-1 rounded-sm border border-slate-600 dark:border-slate-400"><svg class="shrink-0 h-3 w-3 text-sky-600/85 dark:text-sky-200/85" fill="currentColor" viewBox="0 0 448 512"><path d="M318.38 208h-39.09c-1.49 27.03-6.54 51.35-14.21 70.41 27.71-13.24 48.02-39.19 53.3-70.41zm0-32c-5.29-31.22-25.59-57.17-53.3-70.41 7.68 19.06 12.72 43.38 14.21 70.41h39.09zM224 97.31c-7.69 7.45-20.77 34.42-23.43 78.69h46.87c-2.67-44.26-15.75-71.24-23.44-78.69zm-41.08 8.28c-27.71 13.24-48.02 39.19-53.3 70.41h39.09c1.49-27.03 6.53-51.35 14.21-70.41zm0 172.82c-7.68-19.06-12.72-43.38-14.21-70.41h-39.09c5.28 31.22 25.59 57.17 53.3 70.41zM247.43 208h-46.87c2.66 44.26 15.74 71.24 23.43 78.69 7.7-7.45 20.78-34.43 23.44-78.69zM448 358.4V25.6c0-16-9.6-25.6-25.6-25.6H96C41.6 0 0 41.6 0 96v320c0 54.4 41.6 96 96 96h326.4c12.8 0 25.6-9.6 25.6-25.6v-16c0-6.4-3.2-12.8-9.6-19.2-3.2-16-3.2-60.8 0-73.6 6.4-3.2 9.6-9.6 9.6-19.2zM224 64c70.69 0 128 57.31 128 128s-57.31 128-128 128S96 262.69 96 192 153.31 64 224 64zm160 384H96c-19.2 0-32-12.8-32-32s16-32 32-32h288v64z"></path></svg> ${ssrInterpolate(unref(t)("courses"))}: ${ssrInterpolate(instructor.courses_count)}</div>`);
        } else {
          _push(`<!---->`);
        }
        _push(`</div><div class="mt-auto pt-4 flex justify-center">`);
        _push(ssrRenderComponent(unref(Link), {
          href: showRoute(instructor),
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
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Components/Public/Default/School/SchoolInstructor/InstructorRows.vue");
  return _sfc_setup$1 ? _sfc_setup$1(props, ctx) : void 0;
};
const DEFAULT_SORT = "idDesc";
const VIEW_KEY = "public_school_instructors_view";
const LEFT_SIDEBAR_KEY = "public_left_sidebar_collapsed";
const RIGHT_SIDEBAR_KEY = "public_right_sidebar_collapsed";
const _sfc_main = {
  __name: "Index",
  __ssrInlineRender: true,
  props: {
    locale: {
      type: String,
      default: "ru"
    },
    seo: {
      type: Object,
      default: () => ({
        title: "",
        keywords: "",
        description: ""
      })
    },
    publicSchoolInstructorsProcessingMode: {
      type: String,
      default: "server"
    },
    useServerProcessing: {
      type: Boolean,
      default: false
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
    instructorProfiles: {
      type: [Array, Object],
      default: () => []
    },
    instructorProfilesCount: {
      type: Number,
      default: 0
    },
    instructorProfilesFound: {
      type: Number,
      default: 0
    },
    filters: {
      type: Object,
      default: () => ({})
    },
    trackTree: {
      type: Array,
      default: () => []
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
    var _a, _b, _c;
    const { t } = useI18n();
    const page = usePage();
    const props = __props;
    const normalizeList = (value) => {
      if (Array.isArray(value)) return value;
      if (Array.isArray(value == null ? void 0 : value.data)) return value.data;
      return [];
    };
    const normalizeText = (value) => String(value ?? "").trim().toLocaleLowerCase();
    const safeNumber = (value) => {
      const number = Number(value);
      return Number.isFinite(number) ? number : 0;
    };
    const safeDate = (value) => {
      const time = value ? Date.parse(value) : 0;
      return Number.isFinite(time) ? time : 0;
    };
    const siteSettings = computed(
      () => {
        var _a2;
        return ((_a2 = page.props) == null ? void 0 : _a2.siteSettings) ?? {};
      }
    );
    const isAdmin = computed(
      () => {
        var _a2;
        return ((_a2 = page.props) == null ? void 0 : _a2.isAdmin) === true;
      }
    );
    const trackTree = computed(
      () => Array.isArray(props.trackTree) ? props.trackTree : []
    );
    const mainVideos = computed(
      () => normalizeList(props.mainVideos)
    );
    const mainBanners = computed(
      () => normalizeList(props.mainBanners)
    );
    const instructorsData = computed(
      () => normalizeList(props.instructorProfiles)
    );
    const getInstructorTitle = (instructor) => {
      var _a2, _b2;
      return ((_a2 = instructor == null ? void 0 : instructor.translation) == null ? void 0 : _a2.title) || ((_b2 = instructor == null ? void 0 : instructor.user) == null ? void 0 : _b2.name) || t("instructor");
    };
    const getInstructorShort = (instructor) => {
      var _a2;
      return ((_a2 = instructor == null ? void 0 : instructor.translation) == null ? void 0 : _a2.short) || "";
    };
    const getInstructorSlug = (instructor) => (instructor == null ? void 0 : instructor.slug) || "";
    const seoTitle = computed(() => {
      var _a2;
      return String(((_a2 = props.seo) == null ? void 0 : _a2.title) || t("instructors")).trim();
    });
    const seoDescription = computed(() => {
      var _a2;
      return String(((_a2 = props.seo) == null ? void 0 : _a2.description) || t("instructors")).trim();
    });
    const seoKeywords = computed(() => {
      var _a2;
      return String(((_a2 = props.seo) == null ? void 0 : _a2.keywords) || "").trim();
    });
    const contentLocale = computed(() => String(props.locale || "").trim());
    const ogLocale = computed(() => {
      const locale = contentLocale.value.replace("_", "-");
      if (!locale) return void 0;
      try {
        const normalized = new Intl.Locale(locale).maximize();
        return normalized.region ? `${normalized.language}_${normalized.region}` : normalized.language;
      } catch {
        return locale.replace("-", "_");
      }
    });
    const seoCurrentPage = computed(() => props.useServerProcessing ? currentPage.value : frontendCurrentPage.value);
    const canonicalPath = computed(() => {
      const base = `/${props.locale}/school/instructors`;
      return seoCurrentPage.value > 1 ? `${base}?page=${seoCurrentPage.value}` : base;
    });
    const canonicalUrl = computed(() => {
      if (typeof window === "undefined") return canonicalPath.value;
      return new URL(canonicalPath.value, window.location.origin).toString();
    });
    const homeUrl = computed(() => {
      if (typeof window === "undefined") return "/";
      return new URL(route("home"), window.location.origin).toString();
    });
    const getAbsoluteInstructorUrl = (instructor) => {
      const url = route("public.schoolInstructors.show", { slug: instructor.slug });
      if (typeof window === "undefined") return url;
      return new URL(url, window.location.origin).toString();
    };
    const getImageUrl = (image) => (image == null ? void 0 : image.webp_url) || (image == null ? void 0 : image.image_url) || (image == null ? void 0 : image.thumb_url) || (image == null ? void 0 : image.url) || (image == null ? void 0 : image.preview) || "";
    const seoImage = computed(() => {
      for (const instructor of instructorsData.value) {
        const images = Array.isArray(instructor == null ? void 0 : instructor.images) ? instructor.images : [];
        const url = getImageUrl(images[0]);
        if (url) return url;
      }
      return "";
    });
    const siteName = computed(() => {
      var _a2, _b2;
      return String(((_a2 = siteSettings.value) == null ? void 0 : _a2.siteName) || ((_b2 = siteSettings.value) == null ? void 0 : _b2.SiteName) || "").trim();
    });
    const robotsContent = computed(() => q.value.trim() ? "noindex, follow" : "index, follow");
    const collectionPageSchema = computed(() => ({
      "@context": "https://schema.org",
      "@type": "CollectionPage",
      "@id": `${canonicalUrl.value}#webpage`,
      url: canonicalUrl.value,
      name: seoTitle.value,
      description: seoDescription.value,
      inLanguage: contentLocale.value,
      isPartOf: {
        "@type": "WebSite",
        url: homeUrl.value,
        ...siteName.value ? { name: siteName.value } : {}
      }
    }));
    const breadcrumbSchema = computed(() => ({
      "@context": "https://schema.org",
      "@type": "BreadcrumbList",
      itemListElement: [
        { "@type": "ListItem", position: 1, name: t("home"), item: homeUrl.value },
        { "@type": "ListItem", position: 2, name: seoTitle.value, item: canonicalUrl.value }
      ]
    }));
    const instructorItemListSchema = computed(() => ({
      "@context": "https://schema.org",
      "@type": "ItemList",
      name: seoTitle.value,
      numberOfItems: displayedInstructors.value.length,
      itemListElement: displayedInstructors.value.map((instructor, index) => {
        const instructorUrl = getAbsoluteInstructorUrl(instructor);
        const images = Array.isArray(instructor == null ? void 0 : instructor.images) ? instructor.images : [];
        const image = getImageUrl(images[0]);
        const item = {
          "@type": "Person",
          "@id": `${instructorUrl}#person`,
          url: instructorUrl,
          name: getInstructorTitle(instructor)
        };
        if (contentLocale.value) item.inLanguage = contentLocale.value;
        if (getInstructorShort(instructor)) item.description = getInstructorShort(instructor);
        if (image) item.image = image;
        return {
          "@type": "ListItem",
          position: (seoCurrentPage.value - 1) * perPage.value + index + 1,
          url: instructorUrl,
          item
        };
      })
    }));
    const collectionPageJsonLd = computed(() => JSON.stringify(collectionPageSchema.value));
    const breadcrumbJsonLd = computed(() => JSON.stringify(breadcrumbSchema.value));
    const instructorItemListJsonLd = computed(() => JSON.stringify(instructorItemListSchema.value));
    const q = ref(
      String(
        ((_a = props.filters) == null ? void 0 : _a.q) ?? ""
      )
    );
    const sort = ref(
      String(
        ((_b = props.filters) == null ? void 0 : _b.sort) ?? DEFAULT_SORT
      )
    );
    const perPage = computed(() => {
      var _a2;
      const value = Number(
        (_a2 = props.filters) == null ? void 0 : _a2.per_page
      );
      return Number.isFinite(value) && value > 0 ? value : 12;
    });
    const instructorSortOptions = [
      { value: "sortAsc", label: `${t("sortNumber")} 0→9` },
      { value: "sortDesc", label: `${t("sortNumber")} 9→0` },
      { value: "idDesc", label: t("idDesc") },
      { value: "idAsc", label: t("idAsc") },
      { value: "titleAsc", label: `${t("title")} A→Z` },
      { value: "titleDesc", label: `${t("title")} Z→A` },
      { value: "viewsDesc", label: `${t("views")} 9→0` },
      { value: "viewsAsc", label: `${t("views")} 0→9` },
      { value: "ratingCountDesc", label: `${t("ratingCount")} 9→0` },
      { value: "ratingCountAsc", label: `${t("ratingCount")} 0→9` },
      { value: "ratingAvgDesc", label: `${t("ratingAvg")} 9→0` },
      { value: "ratingAvgAsc", label: `${t("ratingAvg")} 0→9` },
      { value: "experienceDesc", label: `${t("experienceYears")} 9→0` },
      { value: "experienceAsc", label: `${t("experienceYears")} 0→9` },
      { value: "coursesDesc", label: `${t("courses")} 9→0` },
      { value: "coursesAsc", label: `${t("courses")} 0→9` },
      { value: "dateDesc", label: `${t("createdAt")} ↓` },
      { value: "dateAsc", label: `${t("createdAt")} ↑` }
    ];
    const viewMode = ref(
      String(
        ((_c = props.filters) == null ? void 0 : _c.view) || "grid"
      )
    );
    onMounted(() => {
      try {
        const storedView = localStorage.getItem(VIEW_KEY);
        if (storedView === "grid" || storedView === "rows") {
          viewMode.value = storedView;
        }
      } catch {
      }
    });
    watch(
      viewMode,
      (value) => {
        try {
          localStorage.setItem(
            VIEW_KEY,
            value
          );
        } catch {
        }
      }
    );
    const filteredInstructors = computed(() => {
      if (props.useServerProcessing) {
        return instructorsData.value;
      }
      const query = normalizeText(q.value);
      if (!query) {
        return instructorsData.value;
      }
      return instructorsData.value.filter(
        (instructor) => {
          var _a2;
          return [
            instructor == null ? void 0 : instructor.id,
            getInstructorTitle(instructor),
            getInstructorShort(instructor),
            getInstructorSlug(instructor),
            (_a2 = instructor == null ? void 0 : instructor.user) == null ? void 0 : _a2.name
          ].some(
            (value) => normalizeText(value).includes(query)
          );
        }
      );
    });
    const compareText = (a, b) => String(a ?? "").localeCompare(
      String(b ?? ""),
      props.locale,
      { sensitivity: "base" }
    );
    const compareNumber = (a, b) => safeNumber(a) - safeNumber(b);
    const sortedInstructors = computed(() => {
      if (props.useServerProcessing) {
        return filteredInstructors.value;
      }
      const list = [
        ...filteredInstructors.value
      ];
      switch (sort.value) {
        case "sortAsc":
          list.sort(
            (a, b) => compareNumber(a == null ? void 0 : a.sort, b == null ? void 0 : b.sort) || compareNumber(b == null ? void 0 : b.id, a == null ? void 0 : a.id)
          );
          break;
        case "sortDesc":
          list.sort(
            (a, b) => compareNumber(b == null ? void 0 : b.sort, a == null ? void 0 : a.sort) || compareNumber(b == null ? void 0 : b.id, a == null ? void 0 : a.id)
          );
          break;
        case "idAsc":
          list.sort(
            (a, b) => compareNumber(a == null ? void 0 : a.id, b == null ? void 0 : b.id)
          );
          break;
        case "idDesc":
          list.sort(
            (a, b) => compareNumber(b == null ? void 0 : b.id, a == null ? void 0 : a.id)
          );
          break;
        case "titleAsc":
          list.sort(
            (a, b) => compareText(
              getInstructorTitle(a),
              getInstructorTitle(b)
            )
          );
          break;
        case "titleDesc":
          list.sort(
            (a, b) => compareText(
              getInstructorTitle(b),
              getInstructorTitle(a)
            )
          );
          break;
        case "viewsAsc":
          list.sort(
            (a, b) => compareNumber(
              a == null ? void 0 : a.views,
              b == null ? void 0 : b.views
            )
          );
          break;
        case "viewsDesc":
          list.sort(
            (a, b) => compareNumber(
              b == null ? void 0 : b.views,
              a == null ? void 0 : a.views
            )
          );
          break;
        case "ratingAvgAsc":
          list.sort(
            (a, b) => compareNumber(
              a == null ? void 0 : a.rating_avg,
              b == null ? void 0 : b.rating_avg
            )
          );
          break;
        case "ratingAvgDesc":
          list.sort(
            (a, b) => compareNumber(
              b == null ? void 0 : b.rating_avg,
              a == null ? void 0 : a.rating_avg
            )
          );
          break;
        case "ratingCountAsc":
          list.sort(
            (a, b) => compareNumber(
              a == null ? void 0 : a.rating_count,
              b == null ? void 0 : b.rating_count
            )
          );
          break;
        case "ratingCountDesc":
          list.sort(
            (a, b) => compareNumber(
              b == null ? void 0 : b.rating_count,
              a == null ? void 0 : a.rating_count
            )
          );
          break;
        case "experienceAsc":
          list.sort(
            (a, b) => compareNumber(
              a == null ? void 0 : a.experience_years,
              b == null ? void 0 : b.experience_years
            )
          );
          break;
        case "experienceDesc":
          list.sort(
            (a, b) => compareNumber(
              b == null ? void 0 : b.experience_years,
              a == null ? void 0 : a.experience_years
            )
          );
          break;
        case "coursesAsc":
          list.sort(
            (a, b) => compareNumber(
              a == null ? void 0 : a.courses_count,
              b == null ? void 0 : b.courses_count
            )
          );
          break;
        case "coursesDesc":
          list.sort(
            (a, b) => compareNumber(
              b == null ? void 0 : b.courses_count,
              a == null ? void 0 : a.courses_count
            )
          );
          break;
        case "dateAsc":
          list.sort(
            (a, b) => safeDate(a == null ? void 0 : a.created_at) - safeDate(b == null ? void 0 : b.created_at)
          );
          break;
        case "dateDesc":
          list.sort(
            (a, b) => safeDate(b == null ? void 0 : b.created_at) - safeDate(a == null ? void 0 : a.created_at)
          );
          break;
      }
      return list;
    });
    const frontendCurrentPage = ref(1);
    const {
      targetRef: scrollTarget,
      scrollToTarget
    } = useSmoothScrollTo({
      offset: 80,
      duration: 1200
    });
    watch(
      [
        q,
        sort
      ],
      () => {
        if (!props.useServerProcessing) {
          frontendCurrentPage.value = 1;
        }
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
    const effectiveInstructorsFound = computed(
      () => props.useServerProcessing ? safeNumber(
        props.instructorProfilesFound
      ) : sortedInstructors.value.length
    );
    const frontendPaginatedInstructors = computed(() => {
      if (props.useServerProcessing) {
        return instructorsData.value;
      }
      const start = (frontendCurrentPage.value - 1) * perPage.value;
      return sortedInstructors.value.slice(
        start,
        start + perPage.value
      );
    });
    const displayedInstructors = computed(
      () => props.useServerProcessing ? instructorsData.value : frontendPaginatedInstructors.value
    );
    const currentPage = computed(
      () => {
        var _a2, _b2, _c2;
        return Number(
          ((_b2 = (_a2 = props.instructorProfiles) == null ? void 0 : _a2.meta) == null ? void 0 : _b2.current_page) ?? ((_c2 = props.instructorProfiles) == null ? void 0 : _c2.current_page) ?? 1
        ) || 1;
      }
    );
    const lastPage = computed(
      () => {
        var _a2, _b2, _c2;
        return Number(
          ((_b2 = (_a2 = props.instructorProfiles) == null ? void 0 : _a2.meta) == null ? void 0 : _b2.last_page) ?? ((_c2 = props.instructorProfiles) == null ? void 0 : _c2.last_page) ?? 1
        ) || 1;
      }
    );
    const reloadInstructors = (pageNumber = 1) => {
      if (!props.useServerProcessing) {
        return;
      }
      router.get(
        route(
          "public.schoolInstructors.index"
        ),
        {
          q: q.value || void 0,
          sort: sort.value || void 0,
          page: pageNumber
        },
        {
          preserveState: true,
          replace: true,
          preserveScroll: true
        }
      );
    };
    const goToPage = (pageNumber) => {
      const target = Math.min(
        Math.max(
          1,
          Number(pageNumber) || 1
        ),
        lastPage.value
      );
      reloadInstructors(target);
    };
    const goPrev = () => {
      if (currentPage.value > 1) {
        goToPage(
          currentPage.value - 1
        );
      }
    };
    const goNext = () => {
      if (currentPage.value < lastPage.value) {
        goToPage(
          currentPage.value + 1
        );
      }
    };
    const applyFilters = () => {
      if (props.useServerProcessing) {
        reloadInstructors(1);
      } else {
        frontendCurrentPage.value = 1;
      }
    };
    const resetFilters = () => {
      if (props.useServerProcessing) {
        reloadInstructors(1);
      } else {
        frontendCurrentPage.value = 1;
      }
    };
    const settingEnabled = (value, defaultValue = true) => {
      if (value === void 0 || value === null || value === "") {
        return defaultValue;
      }
      if (typeof value === "boolean") {
        return value;
      }
      return String(value) === "true";
    };
    const showLeft = computed(
      () => {
        var _a2;
        return settingEnabled(
          (_a2 = siteSettings.value) == null ? void 0 : _a2.ViewLeftColumn,
          true
        );
      }
    );
    const showRight = computed(
      () => {
        var _a2;
        return settingEnabled(
          (_a2 = siteSettings.value) == null ? void 0 : _a2.ViewRightColumn,
          true
        );
      }
    );
    const leftCollapsed = ref(true);
    const rightCollapsed = ref(true);
    const readStoredBoolean = (key, fallback = true) => {
      try {
        const value = localStorage.getItem(key);
        return value === null ? fallback : value === "true";
      } catch {
        return fallback;
      }
    };
    const writeStoredBoolean = (key, value) => {
      try {
        localStorage.setItem(
          key,
          String(Boolean(value))
        );
      } catch {
      }
    };
    onMounted(() => {
      leftCollapsed.value = readStoredBoolean(
        LEFT_SIDEBAR_KEY,
        true
      );
      rightCollapsed.value = readStoredBoolean(
        RIGHT_SIDEBAR_KEY,
        true
      );
    });
    const setLeftCollapsed = (value) => {
      leftCollapsed.value = Boolean(value);
      writeStoredBoolean(
        LEFT_SIDEBAR_KEY,
        leftCollapsed.value
      );
    };
    const setRightCollapsed = (value) => {
      rightCollapsed.value = Boolean(value);
      writeStoredBoolean(
        RIGHT_SIDEBAR_KEY,
        rightCollapsed.value
      );
    };
    const instructorGridCols = computed(() => {
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
    return (_ctx, _push, _parent, _attrs) => {
      _push(`<!--[-->`);
      _push(ssrRenderComponent(unref(Head), null, {
        default: withCtx((_, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(`<title${_scopeId}>${ssrInterpolate(seoTitle.value)}</title><meta name="title"${ssrRenderAttr("content", seoTitle.value)}${_scopeId}><meta name="description"${ssrRenderAttr("content", seoDescription.value)}${_scopeId}>`);
            if (seoKeywords.value) {
              _push2(`<meta name="keywords"${ssrRenderAttr("content", seoKeywords.value)}${_scopeId}>`);
            } else {
              _push2(`<!---->`);
            }
            _push2(`<meta name="robots"${ssrRenderAttr("content", robotsContent.value)}${_scopeId}><meta name="googlebot"${ssrRenderAttr("content", robotsContent.value)}${_scopeId}><link rel="canonical"${ssrRenderAttr("href", canonicalUrl.value)}${_scopeId}><meta property="og:title"${ssrRenderAttr("content", seoTitle.value)}${_scopeId}><meta property="og:description"${ssrRenderAttr("content", seoDescription.value)}${_scopeId}><meta property="og:type" content="website"${_scopeId}><meta property="og:url"${ssrRenderAttr("content", canonicalUrl.value)}${_scopeId}>`);
            if (ogLocale.value) {
              _push2(`<meta property="og:locale"${ssrRenderAttr("content", ogLocale.value)}${_scopeId}>`);
            } else {
              _push2(`<!---->`);
            }
            if (siteName.value) {
              _push2(`<meta property="og:site_name"${ssrRenderAttr("content", siteName.value)}${_scopeId}>`);
            } else {
              _push2(`<!---->`);
            }
            if (seoImage.value) {
              _push2(`<meta property="og:image"${ssrRenderAttr("content", seoImage.value)}${_scopeId}>`);
            } else {
              _push2(`<!---->`);
            }
            _push2(`<meta name="twitter:card"${ssrRenderAttr("content", seoImage.value ? "summary_large_image" : "summary")}${_scopeId}><meta name="twitter:title"${ssrRenderAttr("content", seoTitle.value)}${_scopeId}><meta name="twitter:description"${ssrRenderAttr("content", seoDescription.value)}${_scopeId}>`);
            if (seoImage.value) {
              _push2(`<meta name="twitter:image"${ssrRenderAttr("content", seoImage.value)}${_scopeId}>`);
            } else {
              _push2(`<!---->`);
            }
            _push2(`<meta name="DC.title"${ssrRenderAttr("content", seoTitle.value)}${_scopeId}><meta name="DC.description"${ssrRenderAttr("content", seoDescription.value)}${_scopeId}>`);
            if (seoKeywords.value) {
              _push2(`<meta name="DC.subject"${ssrRenderAttr("content", seoKeywords.value)}${_scopeId}>`);
            } else {
              _push2(`<!---->`);
            }
            _push2(`<meta name="DC.identifier"${ssrRenderAttr("content", canonicalUrl.value)}${_scopeId}><meta name="DC.language"${ssrRenderAttr("content", contentLocale.value)}${_scopeId}><meta name="DC.type" content="Collection"${_scopeId}><meta name="DC.format" content="text/html"${_scopeId}>`);
            if (siteName.value) {
              _push2(`<meta name="DC.publisher"${ssrRenderAttr("content", siteName.value)}${_scopeId}>`);
            } else {
              _push2(`<!---->`);
            }
            ssrRenderVNode(_push2, createVNode(resolveDynamicComponent("script"), { type: "application/ld+json" }, null), _parent2, _scopeId);
            ssrRenderVNode(_push2, createVNode(resolveDynamicComponent("script"), { type: "application/ld+json" }, null), _parent2, _scopeId);
            ssrRenderVNode(_push2, createVNode(resolveDynamicComponent("script"), { type: "application/ld+json" }, null), _parent2, _scopeId);
          } else {
            return [
              createVNode("title", null, toDisplayString(seoTitle.value), 1),
              createVNode("meta", {
                name: "title",
                content: seoTitle.value
              }, null, 8, ["content"]),
              createVNode("meta", {
                name: "description",
                content: seoDescription.value
              }, null, 8, ["content"]),
              seoKeywords.value ? (openBlock(), createBlock("meta", {
                key: 0,
                name: "keywords",
                content: seoKeywords.value
              }, null, 8, ["content"])) : createCommentVNode("", true),
              createVNode("meta", {
                name: "robots",
                content: robotsContent.value
              }, null, 8, ["content"]),
              createVNode("meta", {
                name: "googlebot",
                content: robotsContent.value
              }, null, 8, ["content"]),
              createVNode("link", {
                rel: "canonical",
                href: canonicalUrl.value
              }, null, 8, ["href"]),
              createVNode("meta", {
                property: "og:title",
                content: seoTitle.value
              }, null, 8, ["content"]),
              createVNode("meta", {
                property: "og:description",
                content: seoDescription.value
              }, null, 8, ["content"]),
              createVNode("meta", {
                property: "og:type",
                content: "website"
              }),
              createVNode("meta", {
                property: "og:url",
                content: canonicalUrl.value
              }, null, 8, ["content"]),
              ogLocale.value ? (openBlock(), createBlock("meta", {
                key: 1,
                property: "og:locale",
                content: ogLocale.value
              }, null, 8, ["content"])) : createCommentVNode("", true),
              siteName.value ? (openBlock(), createBlock("meta", {
                key: 2,
                property: "og:site_name",
                content: siteName.value
              }, null, 8, ["content"])) : createCommentVNode("", true),
              seoImage.value ? (openBlock(), createBlock("meta", {
                key: 3,
                property: "og:image",
                content: seoImage.value
              }, null, 8, ["content"])) : createCommentVNode("", true),
              createVNode("meta", {
                name: "twitter:card",
                content: seoImage.value ? "summary_large_image" : "summary"
              }, null, 8, ["content"]),
              createVNode("meta", {
                name: "twitter:title",
                content: seoTitle.value
              }, null, 8, ["content"]),
              createVNode("meta", {
                name: "twitter:description",
                content: seoDescription.value
              }, null, 8, ["content"]),
              seoImage.value ? (openBlock(), createBlock("meta", {
                key: 4,
                name: "twitter:image",
                content: seoImage.value
              }, null, 8, ["content"])) : createCommentVNode("", true),
              createVNode("meta", {
                name: "DC.title",
                content: seoTitle.value
              }, null, 8, ["content"]),
              createVNode("meta", {
                name: "DC.description",
                content: seoDescription.value
              }, null, 8, ["content"]),
              seoKeywords.value ? (openBlock(), createBlock("meta", {
                key: 5,
                name: "DC.subject",
                content: seoKeywords.value
              }, null, 8, ["content"])) : createCommentVNode("", true),
              createVNode("meta", {
                name: "DC.identifier",
                content: canonicalUrl.value
              }, null, 8, ["content"]),
              createVNode("meta", {
                name: "DC.language",
                content: contentLocale.value
              }, null, 8, ["content"]),
              createVNode("meta", {
                name: "DC.type",
                content: "Collection"
              }),
              createVNode("meta", {
                name: "DC.format",
                content: "text/html"
              }),
              siteName.value ? (openBlock(), createBlock("meta", {
                key: 6,
                name: "DC.publisher",
                content: siteName.value
              }, null, 8, ["content"])) : createCommentVNode("", true),
              (openBlock(), createBlock(resolveDynamicComponent("script"), {
                type: "application/ld+json",
                innerHTML: collectionPageJsonLd.value
              }, null, 8, ["innerHTML"])),
              (openBlock(), createBlock(resolveDynamicComponent("script"), {
                type: "application/ld+json",
                innerHTML: breadcrumbJsonLd.value
              }, null, 8, ["innerHTML"])),
              (openBlock(), createBlock(resolveDynamicComponent("script"), {
                type: "application/ld+json",
                innerHTML: instructorItemListJsonLd.value
              }, null, 8, ["innerHTML"]))
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
                "shrink-0 transition-all duration-300 overflow-hidden"
              ])}"${_scopeId}>`);
              _push2(ssrRenderComponent(_sfc_main$5, {
                "track-tree": trackTree.value,
                collapsed: leftCollapsed.value,
                onCollapsed: setLeftCollapsed
              }, null, _parent2, _scopeId));
              _push2(`</aside>`);
            } else {
              _push2(`<!---->`);
            }
            _push2(`<article itemscope itemtype="https://schema.org/CollectionPage"${ssrRenderAttr("itemid", canonicalUrl.value)} class="w-full pb-6 slate-1 min-w-0"${_scopeId}><meta itemprop="name"${ssrRenderAttr("content", seoTitle.value)}${_scopeId}>`);
            if (seoDescription.value) {
              _push2(`<meta itemprop="description"${ssrRenderAttr("content", seoDescription.value)}${_scopeId}>`);
            } else {
              _push2(`<!---->`);
            }
            if (seoKeywords.value) {
              _push2(`<meta itemprop="keywords"${ssrRenderAttr("content", seoKeywords.value)}${_scopeId}>`);
            } else {
              _push2(`<!---->`);
            }
            _push2(`<meta itemprop="url"${ssrRenderAttr("content", canonicalUrl.value)}${_scopeId}><meta itemprop="inLanguage"${ssrRenderAttr("content", contentLocale.value)}${_scopeId}><div class="mx-auto max-w-6xl"${_scopeId}><nav class="text-sm" aria-label="Breadcrumb" itemscope itemtype="https://schema.org/BreadcrumbList"${_scopeId}><ol class="flex flex-wrap items-center font-semibold"${_scopeId}><li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem" class="flex items-center"${_scopeId}>`);
            _push2(ssrRenderComponent(unref(Link), {
              itemprop: "item",
              href: _ctx.route("home"),
              class: "breadcrumb-link hover:underline"
            }, {
              default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                if (_push3) {
                  _push3(`<span itemprop="name"${_scopeId2}>${ssrInterpolate(unref(t)("home"))}</span>`);
                } else {
                  return [
                    createVNode("span", { itemprop: "name" }, toDisplayString(unref(t)("home")), 1)
                  ];
                }
              }),
              _: 1
            }, _parent2, _scopeId));
            _push2(`<meta itemprop="position" content="1"${_scopeId}></li><li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem" class="flex items-center" aria-current="page"${_scopeId}><span class="mx-2 breadcrumbs"${_scopeId}> / </span><span itemprop="name" class="breadcrumbs"${_scopeId}>${ssrInterpolate(unref(t)("instructors"))}</span><meta itemprop="item"${ssrRenderAttr("content", canonicalUrl.value)}${_scopeId}><meta itemprop="position" content="2"${_scopeId}></li></ol></nav><div class="my-3 flex flex-wrap items-center justify-center gap-2 title"${_scopeId}><svg class="shrink-0 h-7 w-7 text-slate-600/85 dark:text-slate-200/85" fill="currentColor" viewBox="0 0 640 512"${_scopeId}><path d="M622.34 153.2L343.4 67.5c-15.2-4.67-31.6-4.67-46.79 0L17.66 153.2c-23.54 7.23-23.54 38.36 0 45.59l48.63 14.94c-10.67 13.19-17.23 29.28-17.88 46.9C38.78 266.15 32 276.11 32 288c0 10.78 5.68 19.85 13.86 25.65L20.33 428.53C18.11 438.52 25.71 448 35.94 448h56.11c10.24 0 17.84-9.48 15.62-19.47L82.14 313.65C90.32 307.85 96 298.78 96 288c0-11.57-6.47-21.25-15.66-26.87.76-15.02 8.44-28.3 20.69-36.72L296.6 284.5c9.06 2.78 26.44 6.25 46.79 0l278.95-85.7c23.55-7.24 23.55-38.36 0-45.6zM352.79 315.09c-28.53 8.76-52.84 3.92-65.59 0l-145.02-44.55L128 384c0 35.35 85.96 64 192 64s192-28.65 192-64l-14.18-113.47-145.03 44.56z"${_scopeId}></path></svg><h1 itemprop="name" class="text-2xl font-bold"${_scopeId}>${ssrInterpolate(unref(t)("instructors"))}</h1></div>`);
            if (seoDescription.value) {
              _push2(`<div itemprop="description" class="my-1 text-sm subtitle text-center"${_scopeId}>${ssrInterpolate(seoDescription.value)}</div>`);
            } else {
              _push2(`<!---->`);
            }
            _push2(ssrRenderComponent(_sfc_main$6, {
              modelValue: q.value,
              "onUpdate:modelValue": ($event) => q.value = $event,
              "view-mode": viewMode.value,
              "onUpdate:viewMode": ($event) => viewMode.value = $event,
              "sort-value": sort.value,
              "onUpdate:sortValue": ($event) => sort.value = $event,
              found: effectiveInstructorsFound.value,
              "sort-options": instructorSortOptions,
              "default-sort": DEFAULT_SORT,
              "found-label": unref(t)("instructors"),
              "search-placeholder": unref(t)("searchByName"),
              onSubmit: applyFilters,
              onReset: resetFilters
            }, null, _parent2, _scopeId));
            _push2(`<div${_scopeId}></div>`);
            if (displayedInstructors.value.length === 0) {
              _push2(`<div class="mt-6 text-center text-slate-700 dark:text-slate-300"${_scopeId}>${ssrInterpolate(unref(t)("noData"))}</div>`);
            } else {
              _push2(`<div${_scopeId}>`);
              if (viewMode.value === "grid") {
                _push2(ssrRenderComponent(_sfc_main$2, {
                  instructors: displayedInstructors.value,
                  cols: instructorGridCols.value
                }, null, _parent2, _scopeId));
              } else {
                _push2(ssrRenderComponent(_sfc_main$1, { instructors: displayedInstructors.value }, null, _parent2, _scopeId));
              }
              _push2(`</div>`);
            }
            if (__props.useServerProcessing && lastPage.value > 1) {
              _push2(ssrRenderComponent(_sfc_main$7, {
                "current-page": currentPage.value,
                "last-page": lastPage.value,
                found: __props.instructorProfilesFound,
                onPrev: goPrev,
                onNext: goNext,
                onGo: goToPage
              }, null, _parent2, _scopeId));
            } else {
              _push2(`<!---->`);
            }
            if (!__props.useServerProcessing && effectiveInstructorsFound.value > perPage.value) {
              _push2(ssrRenderComponent(_sfc_main$8, {
                currentPage: frontendCurrentPage.value,
                "onUpdate:currentPage": ($event) => frontendCurrentPage.value = $event,
                "items-per-page": perPage.value,
                "total-items": effectiveInstructorsFound.value
              }, null, _parent2, _scopeId));
            } else {
              _push2(`<!---->`);
            }
            _push2(ssrRenderComponent(_sfc_main$9, { videos: mainVideos.value }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(SectionBanners, { banners: mainBanners.value }, null, _parent2, _scopeId));
            _push2(`</div></article>`);
            if (showRight.value) {
              _push2(`<aside class="${ssrRenderClass([
                rightCollapsed.value ? "lg:w-6" : "lg:w-72",
                "shrink-0 transition-all duration-300 overflow-hidden"
              ])}"${_scopeId}>`);
              _push2(ssrRenderComponent(_sfc_main$a, {
                collapsed: rightCollapsed.value,
                onCollapsed: setRightCollapsed
              }, null, _parent2, _scopeId));
              _push2(`</aside>`);
            } else {
              _push2(`<!---->`);
            }
            _push2(`</div></div></main>`);
            _push2(ssrRenderComponent(_sfc_main$b, null, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(Progress, null, null, _parent2, _scopeId));
            if (isAdmin.value) {
              _push2(ssrRenderComponent(_sfc_main$c, {
                "setting-key": "publicSchoolInstructorsProcessingMode",
                mode: __props.publicSchoolInstructorsProcessingMode,
                "use-server-processing": __props.useServerProcessing,
                total: __props.instructorProfilesCount
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
                        "shrink-0 transition-all duration-300 overflow-hidden",
                        leftCollapsed.value ? "lg:w-6" : "lg:w-72"
                      ]
                    }, [
                      createVNode(_sfc_main$5, {
                        "track-tree": trackTree.value,
                        collapsed: leftCollapsed.value,
                        onCollapsed: setLeftCollapsed
                      }, null, 8, ["track-tree", "collapsed"])
                    ], 2)) : createCommentVNode("", true),
                    createVNode("article", {
                      itemscope: "",
                      itemtype: "https://schema.org/CollectionPage",
                      itemid: canonicalUrl.value,
                      class: "w-full pb-6 slate-1 min-w-0"
                    }, [
                      createVNode("meta", {
                        itemprop: "name",
                        content: seoTitle.value
                      }, null, 8, ["content"]),
                      seoDescription.value ? (openBlock(), createBlock("meta", {
                        key: 0,
                        itemprop: "description",
                        content: seoDescription.value
                      }, null, 8, ["content"])) : createCommentVNode("", true),
                      seoKeywords.value ? (openBlock(), createBlock("meta", {
                        key: 1,
                        itemprop: "keywords",
                        content: seoKeywords.value
                      }, null, 8, ["content"])) : createCommentVNode("", true),
                      createVNode("meta", {
                        itemprop: "url",
                        content: canonicalUrl.value
                      }, null, 8, ["content"]),
                      createVNode("meta", {
                        itemprop: "inLanguage",
                        content: contentLocale.value
                      }, null, 8, ["content"]),
                      createVNode("div", { class: "mx-auto max-w-6xl" }, [
                        createVNode("nav", {
                          class: "text-sm",
                          "aria-label": "Breadcrumb",
                          itemscope: "",
                          itemtype: "https://schema.org/BreadcrumbList"
                        }, [
                          createVNode("ol", { class: "flex flex-wrap items-center font-semibold" }, [
                            createVNode("li", {
                              itemprop: "itemListElement",
                              itemscope: "",
                              itemtype: "https://schema.org/ListItem",
                              class: "flex items-center"
                            }, [
                              createVNode(unref(Link), {
                                itemprop: "item",
                                href: _ctx.route("home"),
                                class: "breadcrumb-link hover:underline"
                              }, {
                                default: withCtx(() => [
                                  createVNode("span", { itemprop: "name" }, toDisplayString(unref(t)("home")), 1)
                                ]),
                                _: 1
                              }, 8, ["href"]),
                              createVNode("meta", {
                                itemprop: "position",
                                content: "1"
                              })
                            ]),
                            createVNode("li", {
                              itemprop: "itemListElement",
                              itemscope: "",
                              itemtype: "https://schema.org/ListItem",
                              class: "flex items-center",
                              "aria-current": "page"
                            }, [
                              createVNode("span", { class: "mx-2 breadcrumbs" }, " / "),
                              createVNode("span", {
                                itemprop: "name",
                                class: "breadcrumbs"
                              }, toDisplayString(unref(t)("instructors")), 1),
                              createVNode("meta", {
                                itemprop: "item",
                                content: canonicalUrl.value
                              }, null, 8, ["content"]),
                              createVNode("meta", {
                                itemprop: "position",
                                content: "2"
                              })
                            ])
                          ])
                        ]),
                        createVNode("div", { class: "my-3 flex flex-wrap items-center justify-center gap-2 title" }, [
                          (openBlock(), createBlock("svg", {
                            class: "shrink-0 h-7 w-7 text-slate-600/85 dark:text-slate-200/85",
                            fill: "currentColor",
                            viewBox: "0 0 640 512"
                          }, [
                            createVNode("path", { d: "M622.34 153.2L343.4 67.5c-15.2-4.67-31.6-4.67-46.79 0L17.66 153.2c-23.54 7.23-23.54 38.36 0 45.59l48.63 14.94c-10.67 13.19-17.23 29.28-17.88 46.9C38.78 266.15 32 276.11 32 288c0 10.78 5.68 19.85 13.86 25.65L20.33 428.53C18.11 438.52 25.71 448 35.94 448h56.11c10.24 0 17.84-9.48 15.62-19.47L82.14 313.65C90.32 307.85 96 298.78 96 288c0-11.57-6.47-21.25-15.66-26.87.76-15.02 8.44-28.3 20.69-36.72L296.6 284.5c9.06 2.78 26.44 6.25 46.79 0l278.95-85.7c23.55-7.24 23.55-38.36 0-45.6zM352.79 315.09c-28.53 8.76-52.84 3.92-65.59 0l-145.02-44.55L128 384c0 35.35 85.96 64 192 64s192-28.65 192-64l-14.18-113.47-145.03 44.56z" })
                          ])),
                          createVNode("h1", {
                            itemprop: "name",
                            class: "text-2xl font-bold"
                          }, toDisplayString(unref(t)("instructors")), 1)
                        ]),
                        seoDescription.value ? (openBlock(), createBlock("div", {
                          key: 0,
                          itemprop: "description",
                          class: "my-1 text-sm subtitle text-center"
                        }, toDisplayString(seoDescription.value), 1)) : createCommentVNode("", true),
                        createVNode(_sfc_main$6, {
                          modelValue: q.value,
                          "onUpdate:modelValue": ($event) => q.value = $event,
                          "view-mode": viewMode.value,
                          "onUpdate:viewMode": ($event) => viewMode.value = $event,
                          "sort-value": sort.value,
                          "onUpdate:sortValue": ($event) => sort.value = $event,
                          found: effectiveInstructorsFound.value,
                          "sort-options": instructorSortOptions,
                          "default-sort": DEFAULT_SORT,
                          "found-label": unref(t)("instructors"),
                          "search-placeholder": unref(t)("searchByName"),
                          onSubmit: applyFilters,
                          onReset: resetFilters
                        }, null, 8, ["modelValue", "onUpdate:modelValue", "view-mode", "onUpdate:viewMode", "sort-value", "onUpdate:sortValue", "found", "found-label", "search-placeholder"]),
                        createVNode("div", {
                          ref_key: "scrollTarget",
                          ref: scrollTarget
                        }, null, 512),
                        displayedInstructors.value.length === 0 ? (openBlock(), createBlock("div", {
                          key: 1,
                          class: "mt-6 text-center text-slate-700 dark:text-slate-300"
                        }, toDisplayString(unref(t)("noData")), 1)) : (openBlock(), createBlock("div", { key: 2 }, [
                          viewMode.value === "grid" ? (openBlock(), createBlock(_sfc_main$2, {
                            key: 0,
                            instructors: displayedInstructors.value,
                            cols: instructorGridCols.value
                          }, null, 8, ["instructors", "cols"])) : (openBlock(), createBlock(_sfc_main$1, {
                            key: 1,
                            instructors: displayedInstructors.value
                          }, null, 8, ["instructors"]))
                        ])),
                        __props.useServerProcessing && lastPage.value > 1 ? (openBlock(), createBlock(_sfc_main$7, {
                          key: 3,
                          "current-page": currentPage.value,
                          "last-page": lastPage.value,
                          found: __props.instructorProfilesFound,
                          onPrev: goPrev,
                          onNext: goNext,
                          onGo: goToPage
                        }, null, 8, ["current-page", "last-page", "found"])) : createCommentVNode("", true),
                        !__props.useServerProcessing && effectiveInstructorsFound.value > perPage.value ? (openBlock(), createBlock(_sfc_main$8, {
                          key: 4,
                          currentPage: frontendCurrentPage.value,
                          "onUpdate:currentPage": ($event) => frontendCurrentPage.value = $event,
                          "items-per-page": perPage.value,
                          "total-items": effectiveInstructorsFound.value
                        }, null, 8, ["currentPage", "onUpdate:currentPage", "items-per-page", "total-items"])) : createCommentVNode("", true),
                        createVNode(_sfc_main$9, { videos: mainVideos.value }, null, 8, ["videos"]),
                        createVNode(SectionBanners, { banners: mainBanners.value }, null, 8, ["banners"])
                      ])
                    ], 8, ["itemid"]),
                    showRight.value ? (openBlock(), createBlock("aside", {
                      key: 1,
                      class: [
                        "shrink-0 transition-all duration-300 overflow-hidden",
                        rightCollapsed.value ? "lg:w-6" : "lg:w-72"
                      ]
                    }, [
                      createVNode(_sfc_main$a, {
                        collapsed: rightCollapsed.value,
                        onCollapsed: setRightCollapsed
                      }, null, 8, ["collapsed"])
                    ], 2)) : createCommentVNode("", true)
                  ])
                ])
              ]),
              createVNode(_sfc_main$b),
              createVNode(Progress),
              isAdmin.value ? (openBlock(), createBlock(_sfc_main$c, {
                key: 0,
                "setting-key": "publicSchoolInstructorsProcessingMode",
                mode: __props.publicSchoolInstructorsProcessingMode,
                "use-server-processing": __props.useServerProcessing,
                total: __props.instructorProfilesCount
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
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Pages/Public/Default/School/SchoolInstructors/Index.vue");
  return _sfc_setup ? _sfc_setup(props, ctx) : void 0;
};
export {
  _sfc_main as default
};
