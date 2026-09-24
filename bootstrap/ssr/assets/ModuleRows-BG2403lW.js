import { computed, mergeProps, unref, withCtx, createVNode, toDisplayString, createTextVNode, createBlock, openBlock, useSSRContext } from "vue";
import { ssrRenderAttrs, ssrRenderList, ssrRenderAttr, ssrRenderComponent, ssrInterpolate } from "vue/server-renderer";
import { Link } from "@inertiajs/vue3";
import { useI18n } from "vue-i18n";
import { U as UniversalImageSlider } from "./UniversalImageSlider-Cu2Xndcn.js";
import { _ as _sfc_main$2 } from "./EntityStats-Dy-sjN5R.js";
const _sfc_main$1 = {
  __name: "ModuleGrid",
  __ssrInlineRender: true,
  props: {
    modules: { type: Array, default: () => [] },
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
    const moduleTitle = (module) => {
      var _a;
      return ((_a = module == null ? void 0 : module.translation) == null ? void 0 : _a.title) || "";
    };
    const moduleSubtitle = (module) => {
      var _a;
      return ((_a = module == null ? void 0 : module.translation) == null ? void 0 : _a.subtitle) || "";
    };
    const moduleShort = (module) => {
      var _a;
      return ((_a = module == null ? void 0 : module.translation) == null ? void 0 : _a.short) || "";
    };
    const moduleDescription = (module) => moduleShort(module) || moduleSubtitle(module);
    const courseTitle = (module) => {
      var _a, _b;
      return ((_b = (_a = module == null ? void 0 : module.course) == null ? void 0 : _a.translation) == null ? void 0 : _b.title) || "";
    };
    const moduleLink = (module) => {
      var _a;
      return ((_a = module == null ? void 0 : module.course) == null ? void 0 : _a.slug) && (module == null ? void 0 : module.slug) ? route("public.schoolModules.show", {
        courseSlug: module.course.slug,
        slug: module.slug
      }) : "#";
    };
    const courseLink = (module) => {
      var _a;
      return ((_a = module == null ? void 0 : module.course) == null ? void 0 : _a.slug) ? route("public.schoolCourses.show", { slug: module.course.slug }) : "#";
    };
    const moduleDuration = (module) => {
      const duration = Number((module == null ? void 0 : module.duration) || 0);
      return duration > 0 ? `PT${duration}M` : null;
    };
    return (_ctx, _push, _parent, _attrs) => {
      _push(`<div${ssrRenderAttrs(mergeProps({
        class: ["grid gap-4", gridClass.value]
      }, _attrs))}><!--[-->`);
      ssrRenderList(__props.modules, (module) => {
        var _a;
        _push(`<article class="group flex h-full flex-col overflow-hidden rounded-md border border-gray-200 bg-white shadow-sm transition hover:-translate-y-0.5 hover:shadow-md dark:border-gray-700 dark:bg-gray-900" itemscope itemtype="https://schema.org/LearningResource"><link itemprop="url"${ssrRenderAttr("href", moduleLink(module))}>`);
        _push(ssrRenderComponent(unref(Link), {
          href: moduleLink(module),
          "aria-label": moduleTitle(module)
        }, {
          default: withCtx((_, _push2, _parent2, _scopeId) => {
            if (_push2) {
              _push2(ssrRenderComponent(UniversalImageSlider, {
                entity: module,
                alt: moduleTitle(module),
                itemprop: "image",
                "height-class": "h-48",
                "rounded-class": "",
                "wrapper-class": "",
                "img-class": "w-full h-full object-cover transition duration-300 group-hover:scale-105"
              }, null, _parent2, _scopeId));
            } else {
              return [
                createVNode(UniversalImageSlider, {
                  entity: module,
                  alt: moduleTitle(module),
                  itemprop: "image",
                  "height-class": "h-48",
                  "rounded-class": "",
                  "wrapper-class": "",
                  "img-class": "w-full h-full object-cover transition duration-300 group-hover:scale-105"
                }, null, 8, ["entity", "alt"])
              ];
            }
          }),
          _: 2
        }, _parent));
        _push(`<div class="flex flex-1 flex-col p-4"><div class="flex items-center justify-center gap-2">`);
        _push(ssrRenderComponent(unref(Link), {
          href: moduleLink(module),
          class: "inline-flex items-center gap-2"
        }, {
          default: withCtx((_, _push2, _parent2, _scopeId) => {
            if (_push2) {
              _push2(`<span class="text-base font-semibold text-slate-900/85 group-hover:opacity-75 dark:text-slate-100/85 dark:group-hover:opacity-75" itemprop="name"${_scopeId}>${ssrInterpolate(moduleTitle(module))}</span>`);
            } else {
              return [
                createVNode("span", {
                  class: "text-base font-semibold text-slate-900/85 group-hover:opacity-75 dark:text-slate-100/85 dark:group-hover:opacity-75",
                  itemprop: "name"
                }, toDisplayString(moduleTitle(module)), 1)
              ];
            }
          }),
          _: 2
        }, _parent));
        _push(`</div>`);
        if (moduleSubtitle(module)) {
          _push(`<div class="mt-1 text-center text-xs font-semibold text-slate-500 dark:text-slate-400">${ssrInterpolate(moduleSubtitle(module))}</div>`);
        } else {
          _push(`<!---->`);
        }
        if (moduleShort(module)) {
          _push(`<div class="mt-3 line-clamp-3 text-sm text-slate-700 dark:text-slate-300" itemprop="description">${ssrInterpolate(moduleShort(module))}</div>`);
        } else if (moduleDescription(module)) {
          _push(`<meta itemprop="description"${ssrRenderAttr("content", moduleDescription(module))}>`);
        } else {
          _push(`<!---->`);
        }
        if ((_a = module.translation) == null ? void 0 : _a.locale) {
          _push(`<meta itemprop="inLanguage"${ssrRenderAttr("content", module.translation.locale)}>`);
        } else {
          _push(`<!---->`);
        }
        if (moduleDuration(module)) {
          _push(`<meta itemprop="timeRequired"${ssrRenderAttr("content", moduleDuration(module))}>`);
        } else {
          _push(`<!---->`);
        }
        _push(`<div class="mt-3 flex flex-wrap items-center justify-center gap-2 text-xs font-semibold text-slate-500 dark:text-slate-400">`);
        if (module.lessons_count) {
          _push(`<div class="flex items-center justify-center gap-1 px-2 py-1 rounded-sm border border-slate-600 dark:border-slate-400"${ssrRenderAttr("title", unref(t)("lessons"))}><svg class="shrink-0 h-4 w-4 text-indigo-600/85 dark:text-indigo-200/85" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25" stroke-linecap="round" stroke-linejoin="round"></path></svg> ${ssrInterpolate(module.lessons_count)} <span itemscope itemprop="interactionStatistic" itemtype="https://schema.org/InteractionCounter" class="hidden"><link itemprop="interactionType" href="https://schema.org/ConsumeAction"><meta itemprop="userInteractionCount"${ssrRenderAttr("content", module.lessons_count)}></span></div>`);
        } else {
          _push(`<!---->`);
        }
        if (module.duration) {
          _push(`<div class="flex items-center justify-center gap-1 px-2 py-1 rounded-sm border border-slate-600 dark:border-slate-400"${ssrRenderAttr("title", unref(t)("duration"))}><svg class="w-3 h-3" viewBox="0 0 24 24" aria-hidden="true"><path class="fill-current text-blue-700 dark:text-blue-300" d="M22,13a1,1,0,0,1,0-2h1.949A12.006,12.006,0,0,0,13,.051V2a1,1,0,0,1-2,0V.051A12.006,12.006,0,0,0,.051,11H2a1,1,0,0,1,0,2H.051A12.006,12.006,0,0,0,11,23.949V22a1,1,0,0,1,2,0v1.949A12.006,12.006,0,0,0,23.949,13Zm-6,0H12a1,1,0,0,1-.832-.445l-4-6a1,1,0,1,1,1.664-1.11L12.535,11H16a1,1,0,0,1,0,2Z"></path></svg> ${ssrInterpolate(module.duration)} ${ssrInterpolate(unref(t)("minutes"))}</div>`);
        } else {
          _push(`<!---->`);
        }
        if (module.rating_avg && module.rating_count) {
          _push(`<div class="flex items-center justify-center gap-1 px-2 py-1 rounded-sm border border-slate-600 dark:border-slate-400"${ssrRenderAttr("title", unref(t)("rating"))} itemprop="aggregateRating" itemscope itemtype="https://schema.org/AggregateRating"><svg viewBox="0 0 24 24" class="shrink-0 h-3 w-3" aria-hidden="true"><path class="fill-current text-red-400 dark:text-red-300" d="M12.746,1.464l3.11,6.3L22.81,8.776a.831.831,0,0,1,.461,1.418l-5.033,4.9,1.188,6.926a.832.832,0,0,1-1.207.877L12,19.632,5.78,22.9a.833.833,0,0,1-1.207-.878L5.761,15.1l-5.033-4.9a.831.831,0,0,1,.461-1.418L8.143,7.765l3.11-6.3A.833.833,0,0,1,12.746,1.464Z"></path></svg><span itemprop="ratingValue">${ssrInterpolate(Number(module.rating_avg).toFixed(1))}</span><meta itemprop="ratingCount"${ssrRenderAttr("content", module.rating_count)}><meta itemprop="bestRating" content="5"><meta itemprop="worstRating" content="1"></div>`);
        } else {
          _push(`<!---->`);
        }
        _push(`</div>`);
        if (module.course && courseTitle(module)) {
          _push(`<div class="mt-4 flex items-center justify-center gap-2" itemprop="isPartOf" itemscope itemtype="https://schema.org/Course"><link itemprop="url"${ssrRenderAttr("href", courseLink(module))}><div class="flex items-center justify-center gap-1 min-w-0 text-xs font-semibold text-slate-700/85 dark:text-slate-300/85"${ssrRenderAttr("title", courseTitle(module))}><div class="text-slate-500 dark:text-slate-400">${ssrInterpolate(unref(t)("course"))}: </div>`);
          _push(ssrRenderComponent(unref(Link), {
            href: courseLink(module),
            class: "ml-1 truncate hover:text-blue-600 dark:hover:text-blue-400",
            itemprop: "name"
          }, {
            default: withCtx((_, _push2, _parent2, _scopeId) => {
              if (_push2) {
                _push2(`${ssrInterpolate(courseTitle(module))}`);
              } else {
                return [
                  createTextVNode(toDisplayString(courseTitle(module)), 1)
                ];
              }
            }),
            _: 2
          }, _parent));
          _push(`<svg class="shrink-0 h-3 w-3 text-sky-600/85 dark:text-sky-200/85" fill="currentColor" viewBox="0 0 448 512" aria-hidden="true"><path d="M318.38 208h-39.09c-1.49 27.03-6.54 51.35-14.21 70.41 27.71-13.24 48.02-39.19 53.3-70.41zm0-32c-5.29-31.22-25.59-57.17-53.3-70.41 7.68 19.06 12.72 43.38 14.21 70.41h39.09zM224 97.31c-7.69 7.45-20.77 34.42-23.43 78.69h46.87c-2.67-44.26-15.75-71.24-23.44-78.69zm-41.08 8.28c-27.71 13.24-48.02 39.19-53.3 70.41h39.09c1.49-27.03 6.53-51.35 14.21-70.41zm0 172.82c-7.68-19.06-12.72-43.38-14.21-70.41h-39.09c5.28 31.22 25.59 57.17 53.3 70.41zM247.43 208h-46.87c2.66 44.26 15.74 71.24 23.43 78.69 7.7-7.45 20.78-34.43 23.44-78.69zM448 358.4V25.6c0-16-9.6-25.6-25.6-25.6H96C41.6 0 0 41.6 0 96v320c0 54.4 41.6 96 96 96h326.4c12.8 0 25.6-9.6 25.6-25.6v-16c0-6.4-3.2-12.8-9.6-19.2-3.2-16-3.2-60.8 0-73.6 6.4-3.2 9.6-9.6 9.6-19.2zM224 64c70.69 0 128 57.31 128 128s-57.31 128-128 128S96 262.69 96 192 153.31 64 224 64zm160 384H96c-19.2 0-32-12.8-32-32s16-32 32-32h288v64z"></path></svg></div></div>`);
        } else {
          _push(`<!---->`);
        }
        _push(`<div class="mt-3 flex items-center justify-center">`);
        _push(ssrRenderComponent(_sfc_main$2, {
          views: module.views ?? 0,
          "likes-count": module.likes_count ?? 0,
          "already-liked": module.already_liked ?? false,
          "route-name": "public.schoolModules.like",
          "route-params": module.id,
          "show-likes-button": true,
          compact: ""
        }, null, _parent));
        _push(`</div>`);
        if (module.published_at) {
          _push(`<meta itemprop="datePublished"${ssrRenderAttr("content", module.published_at)}>`);
        } else {
          _push(`<!---->`);
        }
        _push(`<div class="mt-auto pt-4">`);
        _push(ssrRenderComponent(unref(Link), {
          href: moduleLink(module),
          class: "flex w-full items-center justify-center gap-2 rounded-sm px-3 py-2 btn-default",
          "aria-label": `${unref(t)("readMore")}: ${moduleTitle(module)}`
        }, {
          default: withCtx((_, _push2, _parent2, _scopeId) => {
            if (_push2) {
              _push2(`<span class="text-sm font-semibold"${_scopeId}>${ssrInterpolate(unref(t)("readMore"))}</span><svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"${_scopeId}><path fill-rule="evenodd" d="M7.21 14.77a.75.75 0 0 1 .02-1.06L10.94 10 7.23 6.29a.75.75 0 1 1 1.06-1.06l4.25 4.25a.75.75 0 0 1 0 1.06l-4.25 4.25a.75.75 0 0 1-1.06.02Z" clip-rule="evenodd"${_scopeId}></path></svg>`);
            } else {
              return [
                createVNode("span", { class: "text-sm font-semibold" }, toDisplayString(unref(t)("readMore")), 1),
                (openBlock(), createBlock("svg", {
                  class: "h-4 w-4",
                  viewBox: "0 0 20 20",
                  fill: "currentColor",
                  "aria-hidden": "true"
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
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Components/Public/Default/School/SchoolModule/ModuleGrid.vue");
  return _sfc_setup$1 ? _sfc_setup$1(props, ctx) : void 0;
};
const _sfc_main = {
  __name: "ModuleRows",
  __ssrInlineRender: true,
  props: {
    modules: { type: Array, default: () => [] }
  },
  setup(__props) {
    const { t } = useI18n();
    const moduleTitle = (module) => {
      var _a;
      return ((_a = module == null ? void 0 : module.translation) == null ? void 0 : _a.title) || "";
    };
    const moduleSubtitle = (module) => {
      var _a;
      return ((_a = module == null ? void 0 : module.translation) == null ? void 0 : _a.subtitle) || "";
    };
    const moduleShort = (module) => {
      var _a;
      return ((_a = module == null ? void 0 : module.translation) == null ? void 0 : _a.short) || "";
    };
    const moduleDescription = (module) => moduleShort(module) || moduleSubtitle(module);
    const courseTitle = (module) => {
      var _a, _b;
      return ((_b = (_a = module == null ? void 0 : module.course) == null ? void 0 : _a.translation) == null ? void 0 : _b.title) || "";
    };
    const moduleLink = (module) => {
      var _a;
      return ((_a = module == null ? void 0 : module.course) == null ? void 0 : _a.slug) && (module == null ? void 0 : module.slug) ? route("public.schoolModules.show", {
        courseSlug: module.course.slug,
        slug: module.slug
      }) : "#";
    };
    const courseLink = (module) => {
      var _a;
      return ((_a = module == null ? void 0 : module.course) == null ? void 0 : _a.slug) ? route("public.schoolCourses.show", { slug: module.course.slug }) : "#";
    };
    const moduleDuration = (module) => {
      const duration = Number((module == null ? void 0 : module.duration) || 0);
      return duration > 0 ? `PT${duration}M` : null;
    };
    return (_ctx, _push, _parent, _attrs) => {
      _push(`<div${ssrRenderAttrs(mergeProps({ class: "space-y-4" }, _attrs))}><!--[-->`);
      ssrRenderList(__props.modules, (module) => {
        var _a;
        _push(`<article class="group flex flex-col sm:flex-row gap-3 rounded-md border border-gray-200 bg-white shadow-sm p-3 transition hover:shadow-md dark:border-gray-700 dark:bg-gray-900" itemscope itemtype="https://schema.org/LearningResource"><link itemprop="url"${ssrRenderAttr("href", moduleLink(module))}>`);
        _push(ssrRenderComponent(unref(Link), {
          href: moduleLink(module),
          class: "shrink-0",
          "aria-label": moduleTitle(module)
        }, {
          default: withCtx((_, _push2, _parent2, _scopeId) => {
            if (_push2) {
              _push2(ssrRenderComponent(UniversalImageSlider, {
                entity: module,
                alt: moduleTitle(module),
                itemprop: "image",
                "height-class": "h-44",
                "rounded-class": "rounded-md",
                "wrapper-class": "w-full sm:w-60 border border-gray-400 dark:border-gray-600",
                "img-class": "w-full h-full object-cover transition duration-300 group-hover:scale-105"
              }, null, _parent2, _scopeId));
            } else {
              return [
                createVNode(UniversalImageSlider, {
                  entity: module,
                  alt: moduleTitle(module),
                  itemprop: "image",
                  "height-class": "h-44",
                  "rounded-class": "rounded-md",
                  "wrapper-class": "w-full sm:w-60 border border-gray-400 dark:border-gray-600",
                  "img-class": "w-full h-full object-cover transition duration-300 group-hover:scale-105"
                }, null, 8, ["entity", "alt"])
              ];
            }
          }),
          _: 2
        }, _parent));
        _push(`<div class="min-w-0 flex-1 flex flex-col justify-around"><div class="min-w-0 flex-1 flex flex-col justify-around"><div class="flex items-start justify-between gap-3">`);
        _push(ssrRenderComponent(unref(Link), {
          href: moduleLink(module),
          class: "min-w-0 inline-flex items-center gap-2"
        }, {
          default: withCtx((_, _push2, _parent2, _scopeId) => {
            if (_push2) {
              _push2(`<span class="truncate text-lg font-semibold text-slate-900/85 dark:text-slate-100/85 group-hover:opacity-75" itemprop="name"${_scopeId}>${ssrInterpolate(moduleTitle(module))}</span>`);
            } else {
              return [
                createVNode("span", {
                  class: "truncate text-lg font-semibold text-slate-900/85 dark:text-slate-100/85 group-hover:opacity-75",
                  itemprop: "name"
                }, toDisplayString(moduleTitle(module)), 1)
              ];
            }
          }),
          _: 2
        }, _parent));
        _push(`</div>`);
        if (moduleSubtitle(module)) {
          _push(`<div class="mt-1 text-xs font-semibold text-slate-500 dark:text-slate-400">${ssrInterpolate(moduleSubtitle(module))}</div>`);
        } else {
          _push(`<!---->`);
        }
        if (moduleShort(module)) {
          _push(`<div class="mt-2 line-clamp-2 text-sm text-slate-700 dark:text-slate-300" itemprop="description">${ssrInterpolate(moduleShort(module))}</div>`);
        } else if (moduleDescription(module)) {
          _push(`<meta itemprop="description"${ssrRenderAttr("content", moduleDescription(module))}>`);
        } else {
          _push(`<!---->`);
        }
        _push(`</div>`);
        if ((_a = module.translation) == null ? void 0 : _a.locale) {
          _push(`<meta itemprop="inLanguage"${ssrRenderAttr("content", module.translation.locale)}>`);
        } else {
          _push(`<!---->`);
        }
        if (moduleDuration(module)) {
          _push(`<meta itemprop="timeRequired"${ssrRenderAttr("content", moduleDuration(module))}>`);
        } else {
          _push(`<!---->`);
        }
        _push(`<div class="mt-3 flex flex-wrap items-center justify-center gap-2 text-xs font-semibold text-slate-500 dark:text-slate-400">`);
        if (module.lessons_count) {
          _push(`<div class="flex items-center justify-center gap-1 px-2 py-1 rounded-sm border border-slate-600 dark:border-slate-400"${ssrRenderAttr("title", unref(t)("lessons"))}><svg class="shrink-0 h-4 w-4 text-indigo-600/85 dark:text-indigo-200/85" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25" stroke-linecap="round" stroke-linejoin="round"></path></svg> ${ssrInterpolate(module.lessons_count)}</div>`);
        } else {
          _push(`<!---->`);
        }
        if (module.duration) {
          _push(`<div class="flex items-center justify-center gap-1 px-2 py-1 rounded-sm border border-slate-600 dark:border-slate-400"${ssrRenderAttr("title", unref(t)("duration"))}><svg class="w-3 h-3" viewBox="0 0 24 24" aria-hidden="true"><path class="fill-current text-blue-700 dark:text-blue-300" d="M22,13a1,1,0,0,1,0-2h1.949A12.006,12.006,0,0,0,13,.051V2a1,1,0,0,1-2,0V.051A12.006,12.006,0,0,0,.051,11H2a1,1,0,0,1,0,2H.051A12.006,12.006,0,0,0,11,23.949V22a1,1,0,0,1,2,0v1.949A12.006,12.006,0,0,0,23.949,13Zm-6,0H12a1,1,0,0,1-.832-.445l-4-6a1,1,0,1,1,1.664-1.11L12.535,11H16a1,1,0,0,1,0,2Z"></path></svg> ${ssrInterpolate(module.duration)} ${ssrInterpolate(unref(t)("minutes"))}</div>`);
        } else {
          _push(`<!---->`);
        }
        if (module.rating_avg && module.rating_count) {
          _push(`<div class="flex items-center justify-center gap-1 px-2 py-1 rounded-sm border border-slate-600 dark:border-slate-400"${ssrRenderAttr("title", unref(t)("rating"))} itemprop="aggregateRating" itemscope itemtype="https://schema.org/AggregateRating"><svg viewBox="0 0 24 24" class="shrink-0 h-3 w-3" aria-hidden="true"><path class="fill-current text-red-400 dark:text-red-300" d="M12.746,1.464l3.11,6.3L22.81,8.776a.831.831,0,0,1,.461,1.418l-5.033,4.9,1.188,6.926a.832.832,0,0,1-1.207.877L12,19.632,5.78,22.9a.833.833,0,0,1-1.207-.878L5.761,15.1l-5.033-4.9a.831.831,0,0,1,.461-1.418L8.143,7.765l3.11-6.3A.833.833,0,0,1,12.746,1.464Z"></path></svg><span itemprop="ratingValue">${ssrInterpolate(Number(module.rating_avg).toFixed(1))}</span><meta itemprop="ratingCount"${ssrRenderAttr("content", module.rating_count)}><meta itemprop="bestRating" content="5"><meta itemprop="worstRating" content="1"></div>`);
        } else {
          _push(`<!---->`);
        }
        _push(`</div>`);
        if (module.course && courseTitle(module)) {
          _push(`<div class="mt-4 flex items-center justify-center gap-2" itemprop="isPartOf" itemscope itemtype="https://schema.org/Course"><link itemprop="url"${ssrRenderAttr("href", courseLink(module))}><div class="flex items-center justify-center gap-1 min-w-0 text-xs font-semibold text-slate-700/85 dark:text-slate-300/85"${ssrRenderAttr("title", courseTitle(module))}><div class="text-slate-500 dark:text-slate-400">${ssrInterpolate(unref(t)("course"))}: </div>`);
          _push(ssrRenderComponent(unref(Link), {
            href: courseLink(module),
            class: "ml-1 truncate hover:text-blue-600 dark:hover:text-blue-400",
            itemprop: "name"
          }, {
            default: withCtx((_, _push2, _parent2, _scopeId) => {
              if (_push2) {
                _push2(`${ssrInterpolate(courseTitle(module))}`);
              } else {
                return [
                  createTextVNode(toDisplayString(courseTitle(module)), 1)
                ];
              }
            }),
            _: 2
          }, _parent));
          _push(`<svg class="shrink-0 h-3 w-3 text-sky-600/85 dark:text-sky-200/85" fill="currentColor" viewBox="0 0 448 512" aria-hidden="true"><path d="M318.38 208h-39.09c-1.49 27.03-6.54 51.35-14.21 70.41 27.71-13.24 48.02-39.19 53.3-70.41zm0-32c-5.29-31.22-25.59-57.17-53.3-70.41 7.68 19.06 12.72 43.38 14.21 70.41h39.09zM224 97.31c-7.69 7.45-20.77 34.42-23.43 78.69h46.87c-2.67-44.26-15.75-71.24-23.44-78.69zm-41.08 8.28c-27.71 13.24-48.02 39.19-53.3 70.41h39.09c1.49-27.03 6.53-51.35 14.21-70.41zm0 172.82c-7.68-19.06-12.72-43.38-14.21-70.41h-39.09c5.28 31.22 25.59 57.17 53.3 70.41zM247.43 208h-46.87c2.66 44.26 15.74 71.24 23.43 78.69 7.7-7.45 20.78-34.43 23.44-78.69zM448 358.4V25.6c0-16-9.6-25.6-25.6-25.6H96C41.6 0 0 41.6 0 96v320c0 54.4 41.6 96 96 96h326.4c12.8 0 25.6-9.6 25.6-25.6v-16c0-6.4-3.2-12.8-9.6-19.2-3.2-16-3.2-60.8 0-73.6 6.4-3.2 9.6-9.6 9.6-19.2zM224 64c70.69 0 128 57.31 128 128s-57.31 128-128 128S96 262.69 96 192 153.31 64 224 64zm160 384H96c-19.2 0-32-12.8-32-32s16-32 32-32h288v64z"></path></svg></div></div>`);
        } else {
          _push(`<!---->`);
        }
        if (module.published_at) {
          _push(`<meta itemprop="datePublished"${ssrRenderAttr("content", module.published_at)}>`);
        } else {
          _push(`<!---->`);
        }
        _push(`<div class="mt-2 flex items-center justify-between gap-3">`);
        _push(ssrRenderComponent(_sfc_main$2, {
          views: module.views ?? 0,
          "likes-count": module.likes_count ?? 0,
          "already-liked": module.already_liked ?? false,
          "route-name": "public.schoolModules.like",
          "route-params": module.id,
          "show-likes-button": true,
          compact: ""
        }, null, _parent));
        _push(ssrRenderComponent(unref(Link), {
          href: moduleLink(module),
          class: "flex w-1/2 items-center justify-center gap-2 rounded-sm px-3 py-1 btn-default",
          "aria-label": `${unref(t)("readMore")}: ${moduleTitle(module)}`
        }, {
          default: withCtx((_, _push2, _parent2, _scopeId) => {
            if (_push2) {
              _push2(`<span class="text-sm font-semibold"${_scopeId}>${ssrInterpolate(unref(t)("readMore"))}</span><svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"${_scopeId}><path fill-rule="evenodd" d="M7.21 14.77a.75.75 0 0 1 .02-1.06L10.94 10 7.23 6.29a.75.75 0 1 1 1.06-1.06l4.25 4.25a.75.75 0 0 1 0 1.06l-4.25 4.25a.75.75 0 0 1-1.06.02Z" clip-rule="evenodd"${_scopeId}></path></svg>`);
            } else {
              return [
                createVNode("span", { class: "text-sm font-semibold" }, toDisplayString(unref(t)("readMore")), 1),
                (openBlock(), createBlock("svg", {
                  class: "h-4 w-4",
                  viewBox: "0 0 20 20",
                  fill: "currentColor",
                  "aria-hidden": "true"
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
const _sfc_setup = _sfc_main.setup;
_sfc_main.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Components/Public/Default/School/SchoolModule/ModuleRows.vue");
  return _sfc_setup ? _sfc_setup(props, ctx) : void 0;
};
export {
  _sfc_main$1 as _,
  _sfc_main as a
};
