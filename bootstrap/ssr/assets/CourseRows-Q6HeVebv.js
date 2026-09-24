import { mergeProps, unref, withCtx, createVNode, toDisplayString, createBlock, openBlock, useSSRContext } from "vue";
import { ssrRenderAttrs, ssrRenderList, ssrRenderAttr, ssrRenderComponent, ssrInterpolate } from "vue/server-renderer";
import { Link } from "@inertiajs/vue3";
import { useI18n } from "vue-i18n";
import { U as UniversalImageSlider } from "./UniversalImageSlider-Cu2Xndcn.js";
import { _ as _sfc_main$1 } from "./EntityStats-Dy-sjN5R.js";
const _sfc_main = {
  __name: "CourseRows",
  __ssrInlineRender: true,
  props: {
    courses: {
      type: Array,
      default: () => []
    }
  },
  setup(__props) {
    const { t } = useI18n();
    const translateLevel = (level) => {
      const normalized = (level || "").toString().trim().toLowerCase();
      const map = {
        beginner: "levelBeginner",
        intermediate: "levelIntermediate",
        advanced: "levelAdvanced"
      };
      return map[normalized] ? t(map[normalized]) : level;
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
      if (!images.length) {
        return null;
      }
      return [...images].sort((a, b) => {
        var _a, _b;
        const aOrder = Number(
          (a == null ? void 0 : a.order) ?? ((_a = a == null ? void 0 : a.pivot) == null ? void 0 : _a.order) ?? 999999
        );
        const bOrder = Number(
          (b == null ? void 0 : b.order) ?? ((_b = b == null ? void 0 : b.pivot) == null ? void 0 : _b.order) ?? 999999
        );
        return aOrder - bOrder;
      })[0];
    };
    const getInstructorImageUrl = (course) => {
      const image = getInstructorPrimaryImage(course);
      return (image == null ? void 0 : image.webp_url) || (image == null ? void 0 : image.url) || (image == null ? void 0 : image.image_url) || (image == null ? void 0 : image.thumb_url) || null;
    };
    const getInstructorName = (course) => {
      var _a, _b;
      const instructorProfile = getInstructorProfile(course);
      return ((_a = instructorProfile == null ? void 0 : instructorProfile.translation) == null ? void 0 : _a.title) || ((_b = instructorProfile == null ? void 0 : instructorProfile.user) == null ? void 0 : _b.name) || t("instructor");
    };
    return (_ctx, _push, _parent, _attrs) => {
      _push(`<div${ssrRenderAttrs(mergeProps({
        class: "space-y-4",
        itemscope: "",
        itemtype: "https://schema.org/ItemList"
      }, _attrs))}><!--[-->`);
      ssrRenderList(__props.courses, (course, index) => {
        var _a, _b, _c;
        _push(`<article class="group flex flex-col sm:flex-row gap-3 rounded-md border border-gray-200 bg-white shadow-sm p-3 transition hover:shadow-md dark:border-gray-700 dark:bg-gray-900" itemprop="itemListElement" itemscope itemtype="https://schema.org/Course"><meta itemprop="position"${ssrRenderAttr("content", String(index + 1))}>`);
        _push(ssrRenderComponent(unref(Link), {
          href: _ctx.route(
            "public.schoolCourses.show",
            { slug: course.slug }
          ),
          "aria-label": ((_a = course.translation) == null ? void 0 : _a.title) || unref(t)("courses"),
          class: "shrink-0",
          itemprop: "url"
        }, {
          default: withCtx((_, _push2, _parent2, _scopeId) => {
            if (_push2) {
              _push2(ssrRenderComponent(UniversalImageSlider, {
                entity: course,
                "height-class": "h-44",
                "rounded-class": "rounded-md",
                "wrapper-class": "w-full sm:w-60 border\n                                   border-gray-400\n                                   dark:border-gray-600",
                "img-class": "w-full h-full object-cover\n                               transition duration-300\n                               group-hover:scale-105"
              }, null, _parent2, _scopeId));
            } else {
              return [
                createVNode(UniversalImageSlider, {
                  entity: course,
                  "height-class": "h-44",
                  "rounded-class": "rounded-md",
                  "wrapper-class": "w-full sm:w-60 border\n                                   border-gray-400\n                                   dark:border-gray-600",
                  "img-class": "w-full h-full object-cover\n                               transition duration-300\n                               group-hover:scale-105"
                }, null, 8, ["entity"])
              ];
            }
          }),
          _: 2
        }, _parent));
        _push(`<div class="min-w-0 flex-1 flex flex-col justify-around"><div class="flex items-start justify-center gap-3"><h2 class="min-w-0" itemprop="name">`);
        _push(ssrRenderComponent(unref(Link), {
          href: _ctx.route(
            "public.schoolCourses.show",
            { slug: course.slug }
          ),
          class: "min-w-0 inline-flex items-center gap-2"
        }, {
          default: withCtx((_, _push2, _parent2, _scopeId) => {
            var _a2, _b2;
            if (_push2) {
              _push2(`<span class="truncate text-lg font-semibold text-slate-900/85 dark:text-slate-100/85 group-hover:opacity-75"${_scopeId}>${ssrInterpolate((_a2 = course.translation) == null ? void 0 : _a2.title)}</span>`);
            } else {
              return [
                createVNode("span", { class: "truncate text-lg font-semibold text-slate-900/85 dark:text-slate-100/85 group-hover:opacity-75" }, toDisplayString((_b2 = course.translation) == null ? void 0 : _b2.title), 1)
              ];
            }
          }),
          _: 2
        }, _parent));
        _push(`</h2></div>`);
        if ((_b = course.translation) == null ? void 0 : _b.short) {
          _push(`<div class="mt-2 line-clamp-2 text-sm text-slate-700 dark:text-slate-300" itemprop="description">${ssrInterpolate(course.translation.short)}</div>`);
        } else {
          _push(`<!---->`);
        }
        _push(`<div class="mt-3 flex flex-wrap items-center justify-center gap-2 text-xs font-semibold text-slate-500 dark:text-slate-400">`);
        if (course.level) {
          _push(`<div class="flex items-center justify-center gap-1 px-2 py-1 rounded-sm border border-slate-600 dark:border-slate-400"${ssrRenderAttr("title", unref(t)("level"))}><svg class="w-3 h-3 fill-current text-slate-600 dark:text-slate-200" viewBox="0 0 24 24" aria-hidden="true"><path class="fill-current text-teal-600 dark:text-teal-300" d="M12,24a1,1,0,0,1,0-2A10,10,0,0,0,12,2a1,1,0,0,1,0-2,12,12,0,0,1,0,24Z"></path><path class="fill-current text-teal-600 dark:text-teal-300" d="M1.045,13.913a1,1,0,0,1-1-.919C.022,12.665,0,12.336,0,12s.022-.665.049-.994a1,1,0,1,1,1.993.162C2.021,11.442,2,11.719,2,12s.021.558.042.832a1,1,0,0,1-.916,1.078Q1.086,13.913,1.045,13.913Z"></path><path class="fill-current text-teal-600 dark:text-teal-300" d="M6.243,3.641a1,1,0,0,1-.526-1.852,12.022,12.022,0,0,1,1.774-.9,1,1,0,1,1,.754,1.851,10.133,10.133,0,0,0-1.478.757A.993.993,0,0,1,6.243,3.641Z"></path><path class="fill-current text-teal-600 dark:text-teal-300" d="M2.188,8.044a.988.988,0,0,1-.451-.108A1,1,0,0,1,1.3,6.592,12.131,12.131,0,0,1,2.342,4.9,1,1,0,0,1,3.953,6.083,10.1,10.1,0,0,0,3.081,7.5,1,1,0,0,1,2.188,8.044Z"></path><path class="fill-current text-teal-600 dark:text-teal-300" d="M3.128,19.482a1,1,0,0,1-.808-.409,12.049,12.049,0,0,1-1.041-1.7,1,1,0,1,1,1.787-.9,10.047,10.047,0,0,0,.868,1.418,1,1,0,0,1-.217,1.4A.986.986,0,0,1,3.128,19.482Z"></path><path d="M7.853,23.185a.983.983,0,0,1-.377-.075A11.879,11.879,0,0,1,5.7,22.2,1,1,0,0,1,6.75,20.5a10.041,10.041,0,0,0,1.48.761,1,1,0,0,1-.377,1.926Z"></path><path d="M10,17a1,1,0,0,1-.707-.293l-4-4a1,1,0,0,1,1.414-1.414L10,14.586l7.293-7.293a1,1,0,1,1,1.414,1.414l-8,8A1,1,0,0,1,10,17Z"></path></svg> ${ssrInterpolate(translateLevel(course.level))}</div>`);
        } else {
          _push(`<!---->`);
        }
        if (course.duration) {
          _push(`<div class="flex items-center justify-center gap-1 px-2 py-1 rounded-sm border border-slate-600 dark:border-slate-400"${ssrRenderAttr("title", unref(t)("duration"))}><svg class="w-3 h-3" viewBox="0 0 24 24" aria-hidden="true"><path class="fill-current text-blue-700 dark:text-blue-300" d="M22,13a1,1,0,0,1,0-2h1.949A12.006,12.006,0,0,0,13,.051V2a1,1,0,0,1-2,0V.051A12.006,12.006,0,0,0,.051,11H2a1,1,0,0,1,0,2H.051A12.006,12.006,0,0,0,11,23.949V22a1,1,0,0,1,2,0v1.949A12.006,12.006,0,0,0,23.949,13Zm-6,0H12a1,1,0,0,1-.832-.445l-4-6a1,1,0,1,1,1.664-1.11L12.535,11H16a1,1,0,0,1,0,2Z"></path></svg> ${ssrInterpolate(course.duration)} ${ssrInterpolate(unref(t)("minutes"))}</div>`);
        } else {
          _push(`<!---->`);
        }
        if (course.rating_avg) {
          _push(`<div class="flex items-center justify-center gap-1 px-2 py-1 rounded-sm border border-slate-600 dark:border-slate-400"${ssrRenderAttr("title", unref(t)("rating"))}><svg viewBox="0 0 24 24" class="shrink-0 h-3 w-3" aria-hidden="true"><path class="fill-current text-red-400 dark:text-red-300" d="M12.746,1.464l3.11,6.3L22.81,8.776a.831.831,0,0,1,.461,1.418l-5.033,4.9,1.188,6.926a.832.832,0,0,1-1.207.877L12,19.632,5.78,22.9a.833.833,0,0,1-1.207-.878L5.761,15.1l-5.033-4.9a.831.831,0,0,1,.461-1.418L8.143,7.765l3.11-6.3A.833.833,0,0,1,12.746,1.464Z"></path></svg> ${ssrInterpolate(Number(course.rating_avg).toFixed(1))}</div>`);
        } else {
          _push(`<!---->`);
        }
        _push(`</div>`);
        if (course == null ? void 0 : course.instructorProfile) {
          _push(`<div class="mt-4 flex items-center justify-center gap-2">`);
          if (getInstructorImageUrl(course)) {
            _push(`<img${ssrRenderAttr("src", getInstructorImageUrl(course))}${ssrRenderAttr("alt", getInstructorName(course))} loading="lazy" class="h-6 w-6 rounded-full object-cover ring-1 ring-gray-200 dark:ring-gray-700">`);
          } else {
            _push(`<!---->`);
          }
          _push(`<div class="flex items-center justify-center gap-1 min-w-0 text-xs font-semibold text-slate-700/85 dark:text-slate-300/85"${ssrRenderAttr("title", `ID: ${course.instructorProfile.id}`)}>${ssrInterpolate(getInstructorName(course))} <svg class="shrink-0 h-4 w-4 text-violet-600/85 dark:text-violet-200/85" fill="currentColor" viewBox="0 0 640 512" aria-hidden="true"><path d="M622.34 153.2L343.4 67.5c-15.2-4.67-31.6-4.67-46.79 0L17.66 153.2c-23.54 7.23-23.54 38.36 0 45.59l48.63 14.94c-10.67 13.19-17.23 29.28-17.88 46.9C38.78 266.15 32 276.11 32 288c0 10.78 5.68 19.85 13.86 25.65L20.33 428.53C18.11 438.52 25.71 448 35.94 448h56.11c10.24 0 17.84-9.48 15.62-19.47L82.14 313.65C90.32 307.85 96 298.78 96 288c0-11.57-6.47-21.25-15.66-26.87.76-15.02 8.44-28.3 20.69-36.72L296.6 284.5c9.06 2.78 26.44 6.25 46.79 0l278.95-85.7c23.55-7.24 23.55-38.36 0-45.6zM352.79 315.09c-28.53 8.76-52.84 3.92-65.59 0l-145.02-44.55L128 384c0 35.35 85.96 64 192 64s192-28.65 192-64l-14.18-113.47-145.03 44.56z"></path></svg></div></div>`);
        } else {
          _push(`<!---->`);
        }
        _push(`<div class="mt-2 flex items-center justify-between gap-3">`);
        _push(ssrRenderComponent(_sfc_main$1, {
          views: course.views || 0,
          "likes-count": course.likes_count || 0,
          "already-liked": course.already_liked || false,
          "route-name": "public.schoolCourses.like",
          "route-params": course.id,
          "show-likes-button": true,
          compact: ""
        }, null, _parent));
        _push(ssrRenderComponent(unref(Link), {
          href: _ctx.route(
            "public.schoolCourses.show",
            { slug: course.slug }
          ),
          class: "flex w-full items-center justify-center gap-2 rounded-sm px-3 py-1 btn-default",
          "aria-label": `${unref(t)("readMore")}: ${((_c = course.translation) == null ? void 0 : _c.title) || ""}`
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
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Components/Public/Default/School/SchoolCourse/CourseRows.vue");
  return _sfc_setup ? _sfc_setup(props, ctx) : void 0;
};
export {
  _sfc_main as _
};
