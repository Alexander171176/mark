import { computed, mergeProps, unref, withCtx, createVNode, toDisplayString, createBlock, openBlock, useSSRContext, ref, watch, resolveDynamicComponent, createCommentVNode } from "vue";
import { ssrRenderAttrs, ssrRenderList, ssrRenderAttr, ssrRenderComponent, ssrInterpolate, ssrRenderVNode, ssrRenderClass } from "vue/server-renderer";
import { Link, usePage, Head, router } from "@inertiajs/vue3";
import { useI18n } from "vue-i18n";
import { u as useSmoothScrollTo } from "./useSmoothScrollTo-g9G3kyDv.js";
import { _ as _sfc_main$4 } from "./DefaultLayout-6zxGxPtK.js";
import { _ as _sfc_main$5, a as _sfc_main$d, P as Progress } from "./Progress-CyfOKcqP.js";
import { _ as _sfc_main$6, a as _sfc_main$c } from "./RightSidebarSchool-DlAegojf.js";
import { _ as _sfc_main$7 } from "./EntityPageToolbar-DT32FtSd.js";
import { _ as _sfc_main$8 } from "./FrontendEntityPageToolbar-Xr_r9znL.js";
import { _ as _sfc_main$9, a as _sfc_main$a, b as _sfc_main$e } from "./PublicAdminBottomPanel-BhLEQMoJ.js";
import { _ as _sfc_main$b, S as SectionBanners } from "./SectionBanners-Cdgmr0Bw.js";
import { U as UniversalImageSlider } from "./UniversalImageSlider-Cu2Xndcn.js";
import { _ as _sfc_main$3 } from "./EntityStats-Dy-sjN5R.js";
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
import "./LikeButtonEntity-ZC4HMEAO.js";
const _sfc_main$2 = {
  __name: "LessonGrid",
  __ssrInlineRender: true,
  props: {
    lessons: { type: Array, default: () => [] },
    cols: { type: Number, default: 2 }
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
    const lessonLink = (lesson) => {
      return route(
        "public.schoolLessons.show",
        { slug: lesson.slug }
      );
    };
    const getTitle = (lesson) => {
      var _a;
      return ((_a = lesson == null ? void 0 : lesson.translation) == null ? void 0 : _a.title) || t("lesson");
    };
    const getShort = (lesson) => {
      var _a;
      return ((_a = lesson == null ? void 0 : lesson.translation) == null ? void 0 : _a.short) || "";
    };
    const getAccessType = (lesson) => {
      return (lesson == null ? void 0 : lesson.access_type) || "";
    };
    const translateAccessType = (value) => {
      const normalized = String(value ?? "").trim().toLowerCase();
      const map = {
        free: "free",
        paid: "paid",
        preview: "preview"
      };
      return map[normalized] ? t(map[normalized]) : value;
    };
    const getDuration = (lesson) => {
      const duration = Number(
        (lesson == null ? void 0 : lesson.duration) ?? 0
      );
      return Number.isFinite(duration) ? duration : 0;
    };
    const getRating = (lesson) => {
      const rating = Number(
        (lesson == null ? void 0 : lesson.rating_avg) ?? 0
      );
      return Number.isFinite(rating) ? rating : 0;
    };
    return (_ctx, _push, _parent, _attrs) => {
      _push(`<div${ssrRenderAttrs(mergeProps({
        class: ["grid gap-4", gridClass.value],
        itemscope: "",
        itemtype: "https://schema.org/ItemList"
      }, _attrs))}><!--[-->`);
      ssrRenderList(__props.lessons, (lesson, index) => {
        var _a;
        _push(`<article class="group flex h-full flex-col overflow-hidden rounded-md border border-gray-200 bg-white shadow-sm transition hover:-translate-y-0.5 hover:shadow-md dark:border-gray-700 dark:bg-gray-900" itemprop="itemListElement" itemscope itemtype="https://schema.org/LearningResource"><meta itemprop="position"${ssrRenderAttr("content", String(index + 1))}><meta itemprop="learningResourceType" content="Lesson">`);
        if ((_a = lesson.translation) == null ? void 0 : _a.locale) {
          _push(`<meta itemprop="inLanguage"${ssrRenderAttr("content", lesson.translation.locale)}>`);
        } else {
          _push(`<!---->`);
        }
        if (lesson.published_at) {
          _push(`<meta itemprop="datePublished"${ssrRenderAttr("content", lesson.published_at)}>`);
        } else {
          _push(`<!---->`);
        }
        if (lesson.difficulty !== null && lesson.difficulty !== void 0) {
          _push(`<meta itemprop="educationalLevel"${ssrRenderAttr("content", String(lesson.difficulty))}>`);
        } else {
          _push(`<!---->`);
        }
        if (getDuration(lesson) > 0) {
          _push(`<meta itemprop="timeRequired"${ssrRenderAttr("content", `PT${Math.round(getDuration(lesson))}M`)}>`);
        } else {
          _push(`<!---->`);
        }
        _push(ssrRenderComponent(unref(Link), {
          href: lessonLink(lesson),
          "aria-label": getTitle(lesson),
          itemprop: "url"
        }, {
          default: withCtx((_, _push2, _parent2, _scopeId) => {
            if (_push2) {
              _push2(ssrRenderComponent(UniversalImageSlider, {
                entity: lesson,
                "height-class": "h-48",
                "rounded-class": "",
                "wrapper-class": "",
                "img-class": "w-full h-full object-cover transition\n                   duration-300 group-hover:scale-105"
              }, null, _parent2, _scopeId));
            } else {
              return [
                createVNode(UniversalImageSlider, {
                  entity: lesson,
                  "height-class": "h-48",
                  "rounded-class": "",
                  "wrapper-class": "",
                  "img-class": "w-full h-full object-cover transition\n                   duration-300 group-hover:scale-105"
                }, null, 8, ["entity"])
              ];
            }
          }),
          _: 2
        }, _parent));
        _push(`<div class="flex flex-1 flex-col p-4"><div class="flex items-center justify-center text-center"><h2 itemprop="name">`);
        _push(ssrRenderComponent(unref(Link), {
          href: lessonLink(lesson),
          class: "inline-flex items-center gap-2"
        }, {
          default: withCtx((_, _push2, _parent2, _scopeId) => {
            if (_push2) {
              _push2(`<span class="text-base font-semibold text-slate-900/85 group-hover:opacity-75 dark:text-slate-100/85 dark:group-hover:opacity-75"${_scopeId}>${ssrInterpolate(getTitle(lesson))} #${ssrInterpolate(lesson.id)}</span>`);
            } else {
              return [
                createVNode("span", { class: "text-base font-semibold text-slate-900/85 group-hover:opacity-75 dark:text-slate-100/85 dark:group-hover:opacity-75" }, toDisplayString(getTitle(lesson)) + " #" + toDisplayString(lesson.id), 1)
              ];
            }
          }),
          _: 2
        }, _parent));
        _push(`</h2></div>`);
        if (getShort(lesson)) {
          _push(`<div class="mt-3 line-clamp-3 text-sm text-slate-700 dark:text-slate-300" itemprop="description">${ssrInterpolate(getShort(lesson))}</div>`);
        } else {
          _push(`<!---->`);
        }
        _push(`<div class="mt-3 flex flex-wrap items-center justify-center gap-2 text-xs font-semibold text-slate-500 dark:text-slate-400">`);
        if (getAccessType(lesson)) {
          _push(`<div class="flex items-center justify-center gap-1 px-2 py-1 rounded-sm border border-slate-600 dark:border-slate-400"${ssrRenderAttr("title", unref(t)("access"))}><svg class="w-3 h-3" viewBox="0 0 24 24" fill="currentColor"><path class="fill-current text-teal-600 dark:text-teal-300" d="M12 1a5 5 0 00-5 5v3H6a2 2 0 00-2 2v8a2 2 0 002 2h12a2 2 0 002-2v-8a2 2 0 00-2-2h-1V6a5 5 0 00-5-5zm-3 8V6a3 3 0 116 0v3H9z"></path></svg> ${ssrInterpolate(translateAccessType(getAccessType(lesson)))}</div>`);
        } else {
          _push(`<!---->`);
        }
        if (getDuration(lesson) > 0) {
          _push(`<div class="flex items-center justify-center gap-1 px-2 py-1 rounded-sm border border-slate-600 dark:border-slate-400"${ssrRenderAttr("title", unref(t)("duration"))}><svg class="w-3 h-3" viewBox="0 0 24 24"><path class="fill-current text-blue-700 dark:text-blue-300" d="M22,13a1,1,0,0,1,0-2h1.949A12.006,12.006,0,0,0,13,.051V2a1,1,0,0,1-2,0V.051A12.006,12.006,0,0,0,.051,11H2a1,1,0,0,1,0,2H.051A12.006,12.006,0,0,0,11,23.949V22a1,1,0,0,1,2,0v1.949A12.006,12.006,0,0,0,23.949,13Zm-6,0H12a1,1,0,0,1-.832-.445l-4-6a1,1,0,1,1,1.664-1.11L12.535,11H16a1,1,0,0,1,0,2Z"></path></svg> ${ssrInterpolate(getDuration(lesson))} ${ssrInterpolate(unref(t)("minutes"))}</div>`);
        } else {
          _push(`<!---->`);
        }
        if (getRating(lesson) > 0) {
          _push(`<div class="flex items-center justify-center gap-1 px-2 py-1 rounded-sm border border-slate-600 dark:border-slate-400"${ssrRenderAttr("title", unref(t)("rating"))} itemprop="aggregateRating" itemscope itemtype="https://schema.org/AggregateRating"><meta itemprop="ratingValue"${ssrRenderAttr("content", getRating(lesson).toFixed(1))}><meta itemprop="bestRating" content="5"><meta itemprop="worstRating" content="1">`);
          if (lesson.rating_count > 0) {
            _push(`<meta itemprop="ratingCount"${ssrRenderAttr("content", String(lesson.rating_count))}>`);
          } else {
            _push(`<!---->`);
          }
          _push(`<svg viewBox="0 0 24 24" class="shrink-0 h-3 w-3" aria-hidden="true"><path class="fill-current text-red-400 dark:text-red-300" d="M12.746,1.464l3.11,6.3L22.81,8.776a.831.831,0,0,1,.461,1.418l-5.033,4.9,1.188,6.926a.832.832,0,0,1-1.207.877L12,19.632,5.78,22.9a.833.833,0,0,1-1.207-.878L5.761,15.1l-5.033-4.9a.831.831,0,0,1,.461-1.418L8.143,7.765l3.11-6.3A.833.833,0,0,1,12.746,1.464Z"></path></svg> ${ssrInterpolate(getRating(lesson).toFixed(1))}</div>`);
        } else {
          _push(`<!---->`);
        }
        _push(`</div><div class="mt-3 flex items-center justify-center">`);
        _push(ssrRenderComponent(_sfc_main$3, {
          views: lesson.views || 0,
          "likes-count": lesson.likes_count || 0,
          "already-liked": lesson.already_liked || false,
          "route-name": "public.schoolLessons.like",
          "route-params": lesson.id,
          "show-likes-button": true,
          compact: ""
        }, null, _parent));
        _push(`</div><div class="mt-auto pt-4">`);
        _push(ssrRenderComponent(unref(Link), {
          href: lessonLink(lesson),
          class: "flex w-full items-center justify-center gap-2 rounded-sm px-3 py-2 btn-default",
          "aria-label": `${unref(t)("readMore")}: ${getTitle(lesson)}`
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
        _push(`</div></div></article>`);
      });
      _push(`<!--]--></div>`);
    };
  }
};
const _sfc_setup$2 = _sfc_main$2.setup;
_sfc_main$2.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Components/Public/Default/School/SchoolLesson/LessonGrid.vue");
  return _sfc_setup$2 ? _sfc_setup$2(props, ctx) : void 0;
};
const _sfc_main$1 = {
  __name: "LessonRows",
  __ssrInlineRender: true,
  props: {
    lessons: {
      type: Array,
      default: () => []
    }
  },
  setup(__props) {
    const { t } = useI18n();
    const lessonLink = (lesson) => {
      return route(
        "public.schoolLessons.show",
        { slug: lesson.slug }
      );
    };
    const getTitle = (lesson) => {
      var _a;
      return ((_a = lesson == null ? void 0 : lesson.translation) == null ? void 0 : _a.title) || t("lesson");
    };
    const getShort = (lesson) => {
      var _a;
      return ((_a = lesson == null ? void 0 : lesson.translation) == null ? void 0 : _a.short) || "";
    };
    const getAccessType = (lesson) => {
      return (lesson == null ? void 0 : lesson.access_type) || "";
    };
    const translateAccessType = (value) => {
      const normalized = String(value ?? "").trim().toLowerCase();
      const map = {
        free: "free",
        paid: "paid",
        preview: "preview"
      };
      return map[normalized] ? t(map[normalized]) : value;
    };
    const getDuration = (lesson) => {
      const duration = Number(
        (lesson == null ? void 0 : lesson.duration) ?? 0
      );
      return Number.isFinite(duration) ? duration : 0;
    };
    const getRating = (lesson) => {
      const rating = Number(
        (lesson == null ? void 0 : lesson.rating_avg) ?? 0
      );
      return Number.isFinite(rating) ? rating : 0;
    };
    return (_ctx, _push, _parent, _attrs) => {
      _push(`<div${ssrRenderAttrs(mergeProps({
        class: "space-y-4",
        itemscope: "",
        itemtype: "https://schema.org/ItemList"
      }, _attrs))}><!--[-->`);
      ssrRenderList(__props.lessons, (lesson, index) => {
        var _a;
        _push(`<article class="group flex flex-col sm:flex-row gap-3 rounded-md border border-gray-200 bg-white shadow-sm p-3 transition hover:shadow-md dark:border-gray-700 dark:bg-gray-900" itemprop="itemListElement" itemscope itemtype="https://schema.org/LearningResource"><meta itemprop="position"${ssrRenderAttr("content", String(index + 1))}><meta itemprop="learningResourceType" content="Lesson">`);
        if ((_a = lesson.translation) == null ? void 0 : _a.locale) {
          _push(`<meta itemprop="inLanguage"${ssrRenderAttr("content", lesson.translation.locale)}>`);
        } else {
          _push(`<!---->`);
        }
        if (lesson.published_at) {
          _push(`<meta itemprop="datePublished"${ssrRenderAttr("content", lesson.published_at)}>`);
        } else {
          _push(`<!---->`);
        }
        if (lesson.difficulty !== null && lesson.difficulty !== void 0) {
          _push(`<meta itemprop="educationalLevel"${ssrRenderAttr("content", String(lesson.difficulty))}>`);
        } else {
          _push(`<!---->`);
        }
        if (getDuration(lesson) > 0) {
          _push(`<meta itemprop="timeRequired"${ssrRenderAttr("content", `PT${Math.round(getDuration(lesson))}M`)}>`);
        } else {
          _push(`<!---->`);
        }
        _push(ssrRenderComponent(unref(Link), {
          href: lessonLink(lesson),
          "aria-label": getTitle(lesson),
          class: "shrink-0",
          itemprop: "url"
        }, {
          default: withCtx((_, _push2, _parent2, _scopeId) => {
            if (_push2) {
              _push2(ssrRenderComponent(UniversalImageSlider, {
                entity: lesson,
                "height-class": "h-44",
                "rounded-class": "rounded-md",
                "wrapper-class": "w-full sm:w-60 border border-gray-400 dark:border-gray-600",
                "img-class": "w-full h-full object-cover transition\n                               duration-300 group-hover:scale-105"
              }, null, _parent2, _scopeId));
            } else {
              return [
                createVNode(UniversalImageSlider, {
                  entity: lesson,
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
        _push(`<div class="min-w-0 flex-1 flex flex-col justify-around"><div class="flex items-start justify-center gap-3"><div class="flex items-start justify-center gap-3"><h2 class="min-w-0" itemprop="name">`);
        _push(ssrRenderComponent(unref(Link), {
          href: lessonLink(lesson),
          class: "min-w-0 inline-flex items-center gap-2"
        }, {
          default: withCtx((_, _push2, _parent2, _scopeId) => {
            if (_push2) {
              _push2(`<span class="truncate text-lg font-semibold text-slate-900/85 dark:text-slate-100/85 group-hover:opacity-75"${_scopeId}>${ssrInterpolate(getTitle(lesson))}</span>`);
            } else {
              return [
                createVNode("span", { class: "truncate text-lg font-semibold text-slate-900/85 dark:text-slate-100/85 group-hover:opacity-75" }, toDisplayString(getTitle(lesson)), 1)
              ];
            }
          }),
          _: 2
        }, _parent));
        _push(`</h2></div></div>`);
        if (getShort(lesson)) {
          _push(`<div class="mt-2 line-clamp-2 text-sm text-slate-700 dark:text-slate-300" itemprop="description">${ssrInterpolate(getShort(lesson))}</div>`);
        } else {
          _push(`<!---->`);
        }
        _push(`<div class="mt-3 flex flex-wrap items-center justify-center gap-2 text-xs font-semibold text-slate-500 dark:text-slate-400">`);
        if (getAccessType(lesson)) {
          _push(`<div class="flex items-center justify-center gap-1 px-2 py-1 rounded-sm border border-slate-600 dark:border-slate-400"${ssrRenderAttr("title", unref(t)("access"))}><svg class="w-3 h-3" viewBox="0 0 24 24" fill="currentColor"><path class="fill-current text-teal-600 dark:text-teal-300" d="M12 1a5 5 0 00-5 5v3H6a2 2 0 00-2 2v8a2 2 0 002 2h12a2 2 0 002-2v-8a2 2 0 00-2-2h-1V6a5 5 0 00-5-5zm-3 8V6a3 3 0 116 0v3H9z"></path></svg> ${ssrInterpolate(translateAccessType(getAccessType(lesson)))}</div>`);
        } else {
          _push(`<!---->`);
        }
        if (getDuration(lesson) > 0) {
          _push(`<div class="flex items-center justify-center gap-1 px-2 py-1 rounded-sm border border-slate-600 dark:border-slate-400"${ssrRenderAttr("title", unref(t)("duration"))}><svg class="w-3 h-3" viewBox="0 0 24 24"><path class="fill-current text-blue-700 dark:text-blue-300" d="M22,13a1,1,0,0,1,0-2h1.949A12.006,12.006,0,0,0,13,.051V2a1,1,0,0,1-2,0V.051A12.006,12.006,0,0,0,.051,11H2a1,1,0,0,1,0,2H.051A12.006,12.006,0,0,0,11,23.949V22a1,1,0,0,1,2,0v1.949A12.006,12.006,0,0,0,23.949,13Zm-6,0H12a1,1,0,0,1-.832-.445l-4-6a1,1,0,1,1,1.664-1.11L12.535,11H16a1,1,0,0,1,0,2Z"></path></svg> ${ssrInterpolate(getDuration(lesson))} ${ssrInterpolate(unref(t)("minutes"))}</div>`);
        } else {
          _push(`<!---->`);
        }
        if (getRating(lesson) > 0) {
          _push(`<div class="flex items-center justify-center gap-1 px-2 py-1 rounded-sm border border-slate-600 dark:border-slate-400"${ssrRenderAttr("title", unref(t)("rating"))} itemprop="aggregateRating" itemscope itemtype="https://schema.org/AggregateRating"><meta itemprop="ratingValue"${ssrRenderAttr("content", getRating(lesson).toFixed(1))}><meta itemprop="bestRating" content="5"><meta itemprop="worstRating" content="1">`);
          if (lesson.rating_count > 0) {
            _push(`<meta itemprop="ratingCount"${ssrRenderAttr("content", String(lesson.rating_count))}>`);
          } else {
            _push(`<!---->`);
          }
          _push(`<svg viewBox="0 0 24 24" class="shrink-0 h-3 w-3" aria-hidden="true"><path class="fill-current text-red-400 dark:text-red-300" d="M12.746,1.464l3.11,6.3L22.81,8.776a.831.831,0,0,1,.461,1.418l-5.033,4.9,1.188,6.926a.832.832,0,0,1-1.207.877L12,19.632,5.78,22.9a.833.833,0,0,1-1.207-.878L5.761,15.1l-5.033-4.9a.831.831,0,0,1,.461-1.418L8.143,7.765l3.11-6.3A.833.833,0,0,1,12.746,1.464Z"></path></svg> ${ssrInterpolate(getRating(lesson).toFixed(1))}</div>`);
        } else {
          _push(`<!---->`);
        }
        _push(`</div><div class="mt-2 flex flex-wrap items-center justify-between gap-3">`);
        _push(ssrRenderComponent(_sfc_main$3, {
          views: lesson.views || 0,
          "likes-count": lesson.likes_count || 0,
          "already-liked": lesson.already_liked || false,
          "route-name": "public.schoolLessons.like",
          "route-params": lesson.id,
          "show-likes-button": true,
          compact: ""
        }, null, _parent));
        _push(ssrRenderComponent(unref(Link), {
          href: lessonLink(lesson),
          class: "w-full lg:w-1/4 flex items-center justify-center gap-2 rounded-sm px-3 py-1 btn-default",
          "aria-label": `${unref(t)("readMore")}: ${getTitle(lesson)}`
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
        _push(`</div></div></article>`);
      });
      _push(`<!--]--></div>`);
    };
  }
};
const _sfc_setup$1 = _sfc_main$1.setup;
_sfc_main$1.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Components/Public/Default/School/SchoolLesson/LessonRows.vue");
  return _sfc_setup$1 ? _sfc_setup$1(props, ctx) : void 0;
};
const LEFT_SIDEBAR_KEY = "public_left_sidebar_collapsed";
const RIGHT_SIDEBAR_KEY = "public_right_sidebar_collapsed";
const DEFAULT_SORT = "idDesc";
const VIEW_KEY = "public_school_lessons_view";
const _sfc_main = {
  __name: "Index",
  __ssrInlineRender: true,
  props: {
    locale: { type: String, default: "ru" },
    seo: {
      type: Object,
      default: () => ({
        title: "",
        keywords: "",
        description: ""
      })
    },
    useServerProcessing: { type: Boolean, default: false },
    publicSchoolLessonsProcessingMode: { type: String, default: "server" },
    title: { type: String, default: "" },
    canLogin: { type: Boolean, default: false },
    canRegister: { type: Boolean, default: false },
    trackTree: { type: Array, default: () => [] },
    lessons: { type: [Array, Object], default: () => [] },
    lessonsCount: { type: Number, default: 0 },
    lessonsFound: { type: Number, default: 0 },
    filters: { type: Object, default: () => ({}) },
    mainVideos: { type: [Array, Object], default: () => [] },
    mainBanners: { type: [Array, Object], default: () => [] }
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
    const normalizeList = (value) => {
      if (Array.isArray(value)) {
        return value;
      }
      if (Array.isArray(value == null ? void 0 : value.data)) {
        return value.data;
      }
      return [];
    };
    const trackTree = computed(() => {
      return Array.isArray(props.trackTree) ? props.trackTree : [];
    });
    const lessonsData = computed(() => {
      var _a2;
      if (Array.isArray(props.lessons)) {
        return props.lessons;
      }
      if (Array.isArray((_a2 = props.lessons) == null ? void 0 : _a2.data)) {
        return props.lessons.data;
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
      getStoredBoolean(LEFT_SIDEBAR_KEY, true)
    );
    const rightCollapsed = ref(
      getStoredBoolean(RIGHT_SIDEBAR_KEY, true)
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
    watch([leftCollapsed, rightCollapsed], () => {
      localStorage.setItem(
        LEFT_SIDEBAR_KEY,
        String(leftCollapsed.value)
      );
      localStorage.setItem(
        RIGHT_SIDEBAR_KEY,
        String(rightCollapsed.value)
      );
    });
    const q = ref(
      String(((_b = props.filters) == null ? void 0 : _b.q) ?? "")
    );
    const sort = ref(
      String(((_c = props.filters) == null ? void 0 : _c.sort) ?? DEFAULT_SORT)
    );
    const viewMode = ref(
      String(
        ((_d = props.filters) == null ? void 0 : _d.view) || localStorage.getItem(VIEW_KEY) || "grid"
      )
    );
    watch(viewMode, (value) => {
      localStorage.setItem(VIEW_KEY, value);
    });
    const perPage = computed(() => {
      var _a2;
      const value = Number((_a2 = props.filters) == null ? void 0 : _a2.per_page);
      return Number.isFinite(value) && value > 0 ? value : 12;
    });
    const lessonSortOptions = [
      { value: "idDesc", label: t("idDesc") },
      { value: "idAsc", label: t("idAsc") },
      { value: "sortAsc", label: `${t("sortNumber")} 0→9` },
      { value: "sortDesc", label: `${t("sortNumber")} 9→0` },
      { value: "titleAsc", label: `${t("title")} A→Z` },
      { value: "titleDesc", label: `${t("title")} Z→A` },
      { value: "difficultyDesc", label: `${t("sortDifficulty")} 9→0` },
      { value: "difficultyAsc", label: `${t("sortDifficulty")} 0→9` },
      { value: "durationDesc", label: `${t("duration")} 9→0` },
      { value: "durationAsc", label: `${t("duration")} 0→9` },
      { value: "viewsDesc", label: `${t("views")} 9→0` },
      { value: "viewsAsc", label: `${t("views")} 0→9` },
      { value: "likesDesc", label: `${t("likes")} 9→0` },
      { value: "likesAsc", label: `${t("likes")} 0→9` },
      { value: "popularityDesc", label: `${t("popularity")} 9→0` },
      { value: "popularityAsc", label: `${t("popularity")} 0→9` },
      { value: "ratingCountDesc", label: `${t("ratingCount")} 9→0` },
      { value: "ratingCountAsc", label: `${t("ratingCount")} 0→9` },
      { value: "ratingAvgDesc", label: `${t("ratingAvg")} 9→0` },
      { value: "ratingAvgAsc", label: `${t("ratingAvg")} 0→9` },
      { value: "publishedAtDesc", label: `${t("publishedAt")} ↓` },
      { value: "publishedAtAsc", label: `${t("publishedAt")} ↑` }
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
      return String(value ?? "").toLowerCase().trim();
    };
    const getLessonTitle = (lesson) => {
      var _a2;
      return ((_a2 = lesson == null ? void 0 : lesson.translation) == null ? void 0 : _a2.title) || "";
    };
    const getLessonShort = (lesson) => {
      var _a2;
      return ((_a2 = lesson == null ? void 0 : lesson.translation) == null ? void 0 : _a2.short) || "";
    };
    const filteredLessons = computed(() => {
      const term = normalizeText(q.value);
      if (!term) {
        return lessonsData.value;
      }
      return lessonsData.value.filter((lesson) => {
        return [
          lesson.id,
          lesson.slug,
          getLessonTitle(lesson),
          getLessonShort(lesson)
        ].some((value) => {
          return normalizeText(value).includes(term);
        });
      });
    });
    const sortedLessons = computed(() => {
      const list = [...filteredLessons.value];
      return list.sort((a, b) => {
        switch (sort.value) {
          case "idAsc":
            return (a.id ?? 0) - (b.id ?? 0);
          case "idDesc":
            return (b.id ?? 0) - (a.id ?? 0);
          case "sortAsc":
            return (a.sort ?? 0) - (b.sort ?? 0) || (b.id ?? 0) - (a.id ?? 0);
          case "sortDesc":
            return (b.sort ?? 0) - (a.sort ?? 0) || (b.id ?? 0) - (a.id ?? 0);
          case "titleAsc":
            return normalizeText(getLessonTitle(a)).localeCompare(
              normalizeText(getLessonTitle(b))
            ) || (b.id ?? 0) - (a.id ?? 0);
          case "titleDesc":
            return normalizeText(getLessonTitle(b)).localeCompare(
              normalizeText(getLessonTitle(a))
            ) || (b.id ?? 0) - (a.id ?? 0);
          case "difficultyAsc":
            return (a.difficulty ?? 0) - (b.difficulty ?? 0) || (b.id ?? 0) - (a.id ?? 0);
          case "difficultyDesc":
            return (b.difficulty ?? 0) - (a.difficulty ?? 0) || (b.id ?? 0) - (a.id ?? 0);
          case "durationAsc":
            return (a.duration ?? 0) - (b.duration ?? 0) || (b.id ?? 0) - (a.id ?? 0);
          case "durationDesc":
            return (b.duration ?? 0) - (a.duration ?? 0) || (b.id ?? 0) - (a.id ?? 0);
          case "viewsAsc":
            return (a.views ?? 0) - (b.views ?? 0) || (b.id ?? 0) - (a.id ?? 0);
          case "viewsDesc":
            return (b.views ?? 0) - (a.views ?? 0) || (b.id ?? 0) - (a.id ?? 0);
          case "likesAsc":
            return (a.likes_count ?? 0) - (b.likes_count ?? 0) || (b.id ?? 0) - (a.id ?? 0);
          case "likesDesc":
            return (b.likes_count ?? 0) - (a.likes_count ?? 0) || (b.id ?? 0) - (a.id ?? 0);
          case "popularityAsc":
            return (a.popularity ?? 0) - (b.popularity ?? 0) || (b.id ?? 0) - (a.id ?? 0);
          case "popularityDesc":
            return (b.popularity ?? 0) - (a.popularity ?? 0) || (b.id ?? 0) - (a.id ?? 0);
          case "ratingCountAsc":
            return (a.rating_count ?? 0) - (b.rating_count ?? 0) || (b.id ?? 0) - (a.id ?? 0);
          case "ratingCountDesc":
            return (b.rating_count ?? 0) - (a.rating_count ?? 0) || (b.id ?? 0) - (a.id ?? 0);
          case "ratingAvgAsc":
            return (a.rating_avg ?? 0) - (b.rating_avg ?? 0) || (b.id ?? 0) - (a.id ?? 0);
          case "ratingAvgDesc":
            return (b.rating_avg ?? 0) - (a.rating_avg ?? 0) || (b.id ?? 0) - (a.id ?? 0);
          case "publishedAtAsc":
          case "dateAsc":
            return new Date(a.published_at ?? 0) - new Date(b.published_at ?? 0) || (b.id ?? 0) - (a.id ?? 0);
          case "publishedAtDesc":
          case "dateDesc":
            return new Date(b.published_at ?? 0) - new Date(a.published_at ?? 0) || (b.id ?? 0) - (a.id ?? 0);
          default:
            return (a.sort ?? 0) - (b.sort ?? 0) || (b.id ?? 0) - (a.id ?? 0);
        }
      });
    });
    const frontendPaginatedLessons = computed(() => {
      const start = (frontendCurrentPage.value - 1) * perPage.value;
      return sortedLessons.value.slice(
        start,
        start + perPage.value
      );
    });
    watch([q, sort, viewMode], () => {
      frontendCurrentPage.value = 1;
    });
    watch(frontendCurrentPage, () => {
      if (!props.useServerProcessing) {
        scrollToTarget();
      }
    });
    const currentPage = computed(() => {
      var _a2, _b2, _c2;
      return Number(
        ((_b2 = (_a2 = props.lessons) == null ? void 0 : _a2.meta) == null ? void 0 : _b2.current_page) ?? ((_c2 = props.lessons) == null ? void 0 : _c2.current_page) ?? 1
      ) || 1;
    });
    const lastPage = computed(() => {
      var _a2, _b2, _c2;
      return Number(
        ((_b2 = (_a2 = props.lessons) == null ? void 0 : _a2.meta) == null ? void 0 : _b2.last_page) ?? ((_c2 = props.lessons) == null ? void 0 : _c2.last_page) ?? 1
      ) || 1;
    });
    const indexRoute = () => {
      return route("public.schoolLessons.index");
    };
    const reloadLessons = (page2 = 1) => {
      router.get(
        indexRoute(),
        {
          q: q.value || void 0,
          sort: sort.value || void 0,
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
      reloadLessons(1);
    };
    const resetSearch = () => {
      q.value = "";
      sort.value = DEFAULT_SORT;
      frontendCurrentPage.value = 1;
      if (props.useServerProcessing) {
        reloadLessons(1);
      }
    };
    const updateSort = (value) => {
      sort.value = value || DEFAULT_SORT;
      if (props.useServerProcessing) {
        reloadLessons(1);
      }
    };
    const updateViewMode = (value) => {
      viewMode.value = value || "grid";
      frontendCurrentPage.value = 1;
      if (props.useServerProcessing) {
        reloadLessons(1);
      }
    };
    const goToPage = (page2) => {
      const value = Number(page2);
      if (!Number.isFinite(value)) {
        return;
      }
      const safePage = Math.max(
        1,
        Math.min(value, lastPage.value)
      );
      reloadLessons(safePage);
    };
    const goPrev = () => {
      if (currentPage.value <= 1) {
        return;
      }
      goToPage(currentPage.value - 1);
    };
    const goNext = () => {
      if (currentPage.value >= lastPage.value) {
        return;
      }
      goToPage(currentPage.value + 1);
    };
    const displayedLessons = computed(() => {
      return props.useServerProcessing ? lessonsData.value : frontendPaginatedLessons.value;
    });
    const seoTitle = computed(() => {
      var _a2;
      return String(
        ((_a2 = props.seo) == null ? void 0 : _a2.title) || t("lessons")
      ).trim();
    });
    const seoDescription = computed(() => {
      var _a2;
      return String(
        ((_a2 = props.seo) == null ? void 0 : _a2.description) || t("lessons")
      ).trim();
    });
    const seoKeywords = computed(() => {
      var _a2;
      return String(
        ((_a2 = props.seo) == null ? void 0 : _a2.keywords) || ""
      ).trim();
    });
    const ogLocale = computed(() => {
      const locale = String(
        props.locale || ""
      ).trim().replace("_", "-");
      if (!locale) {
        return void 0;
      }
      try {
        const normalized = new Intl.Locale(locale).maximize();
        return normalized.region ? `${normalized.language}_${normalized.region}` : normalized.language;
      } catch {
        return locale.replace("-", "_");
      }
    });
    const seoCurrentPage = computed(() => {
      return props.useServerProcessing ? currentPage.value : frontendCurrentPage.value;
    });
    const canonicalPath = computed(() => {
      const base = `/${props.locale}/school/lessons`;
      return seoCurrentPage.value > 1 ? `${base}?page=${seoCurrentPage.value}` : base;
    });
    const canonicalUrl = computed(() => {
      if (typeof window === "undefined") {
        return canonicalPath.value;
      }
      return new URL(
        canonicalPath.value,
        window.location.origin
      ).toString();
    });
    const homeUrl = computed(() => {
      if (typeof window === "undefined") {
        return route("home");
      }
      return new URL(
        route("home"),
        window.location.origin
      ).toString();
    });
    const getAbsoluteLessonUrl = (lesson) => {
      const url = route(
        "public.schoolLessons.show",
        {
          slug: lesson.slug
        }
      );
      if (typeof window === "undefined") {
        return url;
      }
      return new URL(
        url,
        window.location.origin
      ).toString();
    };
    const siteName = computed(() => {
      return String(
        (siteSettings == null ? void 0 : siteSettings.siteName) || (siteSettings == null ? void 0 : siteSettings.SiteName) || ""
      ).trim();
    });
    const robotsContent = computed(() => {
      return q.value.trim() ? "noindex, follow" : "index, follow";
    });
    const collectionPageSchema = computed(() => ({
      "@context": "https://schema.org",
      "@type": "CollectionPage",
      "@id": `${canonicalUrl.value}#webpage`,
      url: canonicalUrl.value,
      name: seoTitle.value,
      description: seoDescription.value,
      inLanguage: props.locale,
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
        {
          "@type": "ListItem",
          position: 1,
          name: t("home"),
          item: homeUrl.value
        },
        {
          "@type": "ListItem",
          position: 2,
          name: seoTitle.value,
          item: canonicalUrl.value
        }
      ]
    }));
    const lessonItemListSchema = computed(() => ({
      "@context": "https://schema.org",
      "@type": "ItemList",
      name: seoTitle.value,
      numberOfItems: displayedLessons.value.length,
      itemListElement: displayedLessons.value.map(
        (lesson, index) => {
          var _a2;
          const lessonUrl = getAbsoluteLessonUrl(lesson);
          const item = {
            "@type": "LearningResource",
            "@id": `${lessonUrl}#learning-resource`,
            url: lessonUrl,
            name: getLessonTitle(lesson) || t("lesson"),
            learningResourceType: "Lesson",
            inLanguage: ((_a2 = lesson == null ? void 0 : lesson.translation) == null ? void 0 : _a2.locale) || props.locale
          };
          if (getLessonShort(lesson)) {
            item.description = getLessonShort(lesson);
          }
          const duration = Number(lesson == null ? void 0 : lesson.duration);
          if (Number.isFinite(duration) && duration > 0) {
            item.timeRequired = `PT${Math.round(duration)}M`;
          }
          if ((lesson == null ? void 0 : lesson.difficulty) !== null && (lesson == null ? void 0 : lesson.difficulty) !== void 0) {
            item.educationalLevel = String(lesson.difficulty);
          }
          if (lesson == null ? void 0 : lesson.published_at) {
            item.datePublished = lesson.published_at;
          }
          return {
            "@type": "ListItem",
            position: (seoCurrentPage.value - 1) * perPage.value + index + 1,
            url: lessonUrl,
            item
          };
        }
      )
    }));
    const collectionPageJsonLd = computed(() => {
      return JSON.stringify(
        collectionPageSchema.value
      );
    });
    const breadcrumbJsonLd = computed(() => {
      return JSON.stringify(
        breadcrumbSchema.value
      );
    });
    const lessonItemListJsonLd = computed(() => {
      return JSON.stringify(
        lessonItemListSchema.value
      );
    });
    const mainVideosList = computed(() => {
      return normalizeList(props.mainVideos);
    });
    const mainBannersList = computed(() => {
      return normalizeList(props.mainBanners);
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
            _push2(`<meta name="twitter:card" content="summary"${_scopeId}><meta name="twitter:title"${ssrRenderAttr("content", seoTitle.value)}${_scopeId}><meta name="twitter:description"${ssrRenderAttr("content", seoDescription.value)}${_scopeId}><meta name="DC.Title"${ssrRenderAttr("content", seoTitle.value)}${_scopeId}><meta name="DC.Description"${ssrRenderAttr("content", seoDescription.value)}${_scopeId}>`);
            if (seoKeywords.value) {
              _push2(`<meta name="DC.Subject"${ssrRenderAttr("content", seoKeywords.value)}${_scopeId}>`);
            } else {
              _push2(`<!---->`);
            }
            _push2(`<meta name="DC.Type" content="Collection"${_scopeId}><meta name="DC.Format" content="text/html"${_scopeId}><meta name="DC.Language"${ssrRenderAttr("content", __props.locale)}${_scopeId}><meta name="DC.Identifier"${ssrRenderAttr("content", canonicalUrl.value)}${_scopeId}>`);
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
              createVNode("meta", {
                name: "twitter:card",
                content: "summary"
              }),
              createVNode("meta", {
                name: "twitter:title",
                content: seoTitle.value
              }, null, 8, ["content"]),
              createVNode("meta", {
                name: "twitter:description",
                content: seoDescription.value
              }, null, 8, ["content"]),
              createVNode("meta", {
                name: "DC.Title",
                content: seoTitle.value
              }, null, 8, ["content"]),
              createVNode("meta", {
                name: "DC.Description",
                content: seoDescription.value
              }, null, 8, ["content"]),
              seoKeywords.value ? (openBlock(), createBlock("meta", {
                key: 3,
                name: "DC.Subject",
                content: seoKeywords.value
              }, null, 8, ["content"])) : createCommentVNode("", true),
              createVNode("meta", {
                name: "DC.Type",
                content: "Collection"
              }),
              createVNode("meta", {
                name: "DC.Format",
                content: "text/html"
              }),
              createVNode("meta", {
                name: "DC.Language",
                content: __props.locale
              }, null, 8, ["content"]),
              createVNode("meta", {
                name: "DC.Identifier",
                content: canonicalUrl.value
              }, null, 8, ["content"]),
              (openBlock(), createBlock(resolveDynamicComponent("script"), {
                type: "application/ld+json",
                textContent: toDisplayString(collectionPageJsonLd.value)
              }, null, 8, ["textContent"])),
              (openBlock(), createBlock(resolveDynamicComponent("script"), {
                type: "application/ld+json",
                textContent: toDisplayString(breadcrumbJsonLd.value)
              }, null, 8, ["textContent"])),
              (openBlock(), createBlock(resolveDynamicComponent("script"), {
                type: "application/ld+json",
                textContent: toDisplayString(lessonItemListJsonLd.value)
              }, null, 8, ["textContent"]))
            ];
          }
        }),
        _: 1
      }, _parent));
      _push(ssrRenderComponent(_sfc_main$4, {
        title: __props.title,
        "can-login": __props.canLogin,
        "can-register": __props.canRegister
      }, {
        default: withCtx((_, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(ssrRenderComponent(_sfc_main$5, null, null, _parent2, _scopeId));
            _push2(`<main class="min-h-screen px-1 lg:px-6 max-w-full"${_scopeId}><div class="mx-auto tracking-wider pt-20 lg:pt-44"${_scopeId}><div class="ext-color w-full min-w-0 py-3 px-1 flex flex-col lg:flex-row gap-4 rounded-3xl border-2 border-slate-300 dark:border-slate-500"${_scopeId}>`);
            if (showLeft.value) {
              _push2(`<aside class="${ssrRenderClass([leftCollapsed.value ? "lg:w-6" : "lg:w-72", "shrink-0 transition-all duration-300"])}"${_scopeId}>`);
              _push2(ssrRenderComponent(_sfc_main$6, {
                "track-tree": trackTree.value,
                collapsed: leftCollapsed.value,
                onCollapsed: ($event) => leftCollapsed.value = $event
              }, null, _parent2, _scopeId));
              _push2(`</aside>`);
            } else {
              _push2(`<!---->`);
            }
            _push2(`<div class="w-full pb-6 slate-1"${_scopeId}><div class="mx-auto max-w-6xl"${_scopeId}><nav class="text-sm" aria-label="Breadcrumb" itemscope itemtype="https://schema.org/BreadcrumbList"${_scopeId}><ol class="flex flex-wrap items-center font-semibold"${_scopeId}><li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem"${_scopeId}>`);
            _push2(ssrRenderComponent(unref(Link), {
              href: _ctx.route("home"),
              class: "breadcrumb-link hover:underline",
              itemprop: "item"
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
            _push2(`<meta itemprop="position" content="1"${_scopeId}></li><li${_scopeId}><span class="mx-2 breadcrumbs"${_scopeId}> / </span></li><li class="breadcrumbs" itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem"${_scopeId}><span itemprop="name"${_scopeId}>${ssrInterpolate(unref(t)("lessons"))}</span><meta itemprop="position" content="2"${_scopeId}><meta itemprop="item"${ssrRenderAttr("content", canonicalUrl.value)}${_scopeId}></li></ol></nav><div class="my-3 flex flex-wrap items-center justify-center gap-3 title"${_scopeId}><svg class="shrink-0 h-6 w-6 text-slate-600/85 dark:text-slate-200/85" fill="currentColor" viewBox="0 0 24 24"${_scopeId}><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25"${_scopeId}></path></svg><h1 class="text-2xl font-bold"${_scopeId}>${ssrInterpolate(seoTitle.value)}</h1></div><div class="my-1 text-sm subtitle text-center"${_scopeId}> Выберите урок и продолжайте обучение в удобном формате. </div>`);
            if (__props.useServerProcessing) {
              _push2(ssrRenderComponent(_sfc_main$7, {
                modelValue: q.value,
                "onUpdate:modelValue": ($event) => q.value = $event,
                found: __props.lessonsFound,
                "view-mode": viewMode.value,
                "sort-value": sort.value,
                "sort-options": lessonSortOptions,
                "default-sort": DEFAULT_SORT,
                "found-label": unref(t)("lessons"),
                "search-placeholder": unref(t)("searchByName"),
                onSubmit: submitSearch,
                onReset: resetSearch,
                "onUpdate:viewMode": updateViewMode,
                "onUpdate:sortValue": updateSort
              }, null, _parent2, _scopeId));
            } else {
              _push2(ssrRenderComponent(_sfc_main$8, {
                modelValue: q.value,
                "onUpdate:modelValue": ($event) => q.value = $event,
                found: sortedLessons.value.length,
                "view-mode": viewMode.value,
                "sort-value": sort.value,
                "sort-options": lessonSortOptions,
                "default-sort": DEFAULT_SORT,
                "found-label": unref(t)("lessons"),
                "search-placeholder": unref(t)("searchByName"),
                onReset: resetSearch,
                "onUpdate:viewMode": updateViewMode,
                "onUpdate:sortValue": updateSort
              }, null, _parent2, _scopeId));
            }
            _push2(`<div${_scopeId}></div>`);
            if (displayedLessons.value.length === 0) {
              _push2(`<div class="mt-6 text-center text-slate-700 dark:text-slate-300"${_scopeId}>${ssrInterpolate(unref(t)("noData"))}</div>`);
            } else {
              _push2(`<div${_scopeId}>`);
              if (viewMode.value === "grid") {
                _push2(ssrRenderComponent(_sfc_main$2, {
                  lessons: displayedLessons.value,
                  cols: gridCols.value
                }, null, _parent2, _scopeId));
              } else {
                _push2(ssrRenderComponent(_sfc_main$1, { lessons: displayedLessons.value }, null, _parent2, _scopeId));
              }
              _push2(`</div>`);
            }
            if (__props.useServerProcessing) {
              _push2(ssrRenderComponent(_sfc_main$9, {
                "current-page": currentPage.value,
                "last-page": lastPage.value,
                found: __props.lessonsFound,
                onPrev: goPrev,
                onNext: goNext,
                onGo: goToPage
              }, null, _parent2, _scopeId));
            } else {
              _push2(ssrRenderComponent(_sfc_main$a, {
                currentPage: frontendCurrentPage.value,
                "onUpdate:currentPage": ($event) => frontendCurrentPage.value = $event,
                "items-per-page": perPage.value,
                "total-items": sortedLessons.value.length
              }, null, _parent2, _scopeId));
            }
            _push2(ssrRenderComponent(_sfc_main$b, { videos: mainVideosList.value }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(SectionBanners, { banners: mainBannersList.value }, null, _parent2, _scopeId));
            _push2(`</div></div>`);
            if (showRight.value) {
              _push2(`<aside class="${ssrRenderClass([rightCollapsed.value ? "lg:w-6" : "lg:w-72", "shrink-0 transition-all duration-300"])}"${_scopeId}>`);
              _push2(ssrRenderComponent(_sfc_main$c, {
                collapsed: rightCollapsed.value,
                onCollapsed: ($event) => rightCollapsed.value = $event
              }, null, _parent2, _scopeId));
              _push2(`</aside>`);
            } else {
              _push2(`<!---->`);
            }
            _push2(`</div></div></main>`);
            _push2(ssrRenderComponent(_sfc_main$d, null, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(Progress, null, null, _parent2, _scopeId));
            if (isAdmin.value) {
              _push2(ssrRenderComponent(_sfc_main$e, {
                "setting-key": "publicSchoolLessonsProcessingMode",
                mode: __props.publicSchoolLessonsProcessingMode,
                "use-server-processing": __props.useServerProcessing,
                total: __props.lessonsCount
              }, null, _parent2, _scopeId));
            } else {
              _push2(`<!---->`);
            }
          } else {
            return [
              createVNode(_sfc_main$5),
              createVNode("main", { class: "min-h-screen px-1 lg:px-6 max-w-full" }, [
                createVNode("div", { class: "mx-auto tracking-wider pt-20 lg:pt-44" }, [
                  createVNode("div", { class: "ext-color w-full min-w-0 py-3 px-1 flex flex-col lg:flex-row gap-4 rounded-3xl border-2 border-slate-300 dark:border-slate-500" }, [
                    showLeft.value ? (openBlock(), createBlock("aside", {
                      key: 0,
                      class: ["shrink-0 transition-all duration-300", leftCollapsed.value ? "lg:w-6" : "lg:w-72"]
                    }, [
                      createVNode(_sfc_main$6, {
                        "track-tree": trackTree.value,
                        collapsed: leftCollapsed.value,
                        onCollapsed: ($event) => leftCollapsed.value = $event
                      }, null, 8, ["track-tree", "collapsed", "onCollapsed"])
                    ], 2)) : createCommentVNode("", true),
                    createVNode("div", { class: "w-full pb-6 slate-1" }, [
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
                              itemtype: "https://schema.org/ListItem"
                            }, [
                              createVNode(unref(Link), {
                                href: _ctx.route("home"),
                                class: "breadcrumb-link hover:underline",
                                itemprop: "item"
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
                            createVNode("li", null, [
                              createVNode("span", { class: "mx-2 breadcrumbs" }, " / ")
                            ]),
                            createVNode("li", {
                              class: "breadcrumbs",
                              itemprop: "itemListElement",
                              itemscope: "",
                              itemtype: "https://schema.org/ListItem"
                            }, [
                              createVNode("span", { itemprop: "name" }, toDisplayString(unref(t)("lessons")), 1),
                              createVNode("meta", {
                                itemprop: "position",
                                content: "2"
                              }),
                              createVNode("meta", {
                                itemprop: "item",
                                content: canonicalUrl.value
                              }, null, 8, ["content"])
                            ])
                          ])
                        ]),
                        createVNode("div", { class: "my-3 flex flex-wrap items-center justify-center gap-3 title" }, [
                          (openBlock(), createBlock("svg", {
                            class: "shrink-0 h-6 w-6 text-slate-600/85 dark:text-slate-200/85",
                            fill: "currentColor",
                            viewBox: "0 0 24 24"
                          }, [
                            createVNode("path", {
                              "stroke-linecap": "round",
                              "stroke-linejoin": "round",
                              d: "M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25"
                            })
                          ])),
                          createVNode("h1", { class: "text-2xl font-bold" }, toDisplayString(seoTitle.value), 1)
                        ]),
                        createVNode("div", { class: "my-1 text-sm subtitle text-center" }, " Выберите урок и продолжайте обучение в удобном формате. "),
                        __props.useServerProcessing ? (openBlock(), createBlock(_sfc_main$7, {
                          key: 0,
                          modelValue: q.value,
                          "onUpdate:modelValue": ($event) => q.value = $event,
                          found: __props.lessonsFound,
                          "view-mode": viewMode.value,
                          "sort-value": sort.value,
                          "sort-options": lessonSortOptions,
                          "default-sort": DEFAULT_SORT,
                          "found-label": unref(t)("lessons"),
                          "search-placeholder": unref(t)("searchByName"),
                          onSubmit: submitSearch,
                          onReset: resetSearch,
                          "onUpdate:viewMode": updateViewMode,
                          "onUpdate:sortValue": updateSort
                        }, null, 8, ["modelValue", "onUpdate:modelValue", "found", "view-mode", "sort-value", "found-label", "search-placeholder"])) : (openBlock(), createBlock(_sfc_main$8, {
                          key: 1,
                          modelValue: q.value,
                          "onUpdate:modelValue": ($event) => q.value = $event,
                          found: sortedLessons.value.length,
                          "view-mode": viewMode.value,
                          "sort-value": sort.value,
                          "sort-options": lessonSortOptions,
                          "default-sort": DEFAULT_SORT,
                          "found-label": unref(t)("lessons"),
                          "search-placeholder": unref(t)("searchByName"),
                          onReset: resetSearch,
                          "onUpdate:viewMode": updateViewMode,
                          "onUpdate:sortValue": updateSort
                        }, null, 8, ["modelValue", "onUpdate:modelValue", "found", "view-mode", "sort-value", "found-label", "search-placeholder"])),
                        createVNode("div", {
                          ref_key: "scrollTarget",
                          ref: scrollTarget
                        }, null, 512),
                        displayedLessons.value.length === 0 ? (openBlock(), createBlock("div", {
                          key: 2,
                          class: "mt-6 text-center text-slate-700 dark:text-slate-300"
                        }, toDisplayString(unref(t)("noData")), 1)) : (openBlock(), createBlock("div", { key: 3 }, [
                          viewMode.value === "grid" ? (openBlock(), createBlock(_sfc_main$2, {
                            key: 0,
                            lessons: displayedLessons.value,
                            cols: gridCols.value
                          }, null, 8, ["lessons", "cols"])) : (openBlock(), createBlock(_sfc_main$1, {
                            key: 1,
                            lessons: displayedLessons.value
                          }, null, 8, ["lessons"]))
                        ])),
                        __props.useServerProcessing ? (openBlock(), createBlock(_sfc_main$9, {
                          key: 4,
                          "current-page": currentPage.value,
                          "last-page": lastPage.value,
                          found: __props.lessonsFound,
                          onPrev: goPrev,
                          onNext: goNext,
                          onGo: goToPage
                        }, null, 8, ["current-page", "last-page", "found"])) : (openBlock(), createBlock(_sfc_main$a, {
                          key: 5,
                          currentPage: frontendCurrentPage.value,
                          "onUpdate:currentPage": ($event) => frontendCurrentPage.value = $event,
                          "items-per-page": perPage.value,
                          "total-items": sortedLessons.value.length
                        }, null, 8, ["currentPage", "onUpdate:currentPage", "items-per-page", "total-items"])),
                        createVNode(_sfc_main$b, { videos: mainVideosList.value }, null, 8, ["videos"]),
                        createVNode(SectionBanners, { banners: mainBannersList.value }, null, 8, ["banners"])
                      ])
                    ]),
                    showRight.value ? (openBlock(), createBlock("aside", {
                      key: 1,
                      class: ["shrink-0 transition-all duration-300", rightCollapsed.value ? "lg:w-6" : "lg:w-72"]
                    }, [
                      createVNode(_sfc_main$c, {
                        collapsed: rightCollapsed.value,
                        onCollapsed: ($event) => rightCollapsed.value = $event
                      }, null, 8, ["collapsed", "onCollapsed"])
                    ], 2)) : createCommentVNode("", true)
                  ])
                ])
              ]),
              createVNode(_sfc_main$d),
              createVNode(Progress),
              isAdmin.value ? (openBlock(), createBlock(_sfc_main$e, {
                key: 0,
                "setting-key": "publicSchoolLessonsProcessingMode",
                mode: __props.publicSchoolLessonsProcessingMode,
                "use-server-processing": __props.useServerProcessing,
                total: __props.lessonsCount
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
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Pages/Public/Default/School/SchoolLessons/Index.vue");
  return _sfc_setup ? _sfc_setup(props, ctx) : void 0;
};
export {
  _sfc_main as default
};
