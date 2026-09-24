import { computed, mergeProps, unref, withCtx, createVNode, toDisplayString, createBlock, openBlock, useSSRContext, ref, watch, resolveDynamicComponent, createCommentVNode, createTextVNode, Fragment } from "vue";
import { ssrRenderAttrs, ssrRenderList, ssrRenderComponent, ssrInterpolate, ssrRenderAttr, ssrRenderVNode, ssrRenderClass } from "vue/server-renderer";
import { Link, usePage, Head } from "@inertiajs/vue3";
import { useI18n } from "vue-i18n";
import { _ as _sfc_main$6 } from "./DefaultLayout-6zxGxPtK.js";
import { _ as _sfc_main$7, a as _sfc_main$b, P as Progress } from "./Progress-CyfOKcqP.js";
import { _ as _sfc_main$8, a as _sfc_main$a } from "./RightSidebarSchool-DlAegojf.js";
import { I as ImageGalleryMain } from "./ImageGalleryMain-mIfXDUWm.js";
import { L as LikeButtonEntity } from "./LikeButtonEntity-ZC4HMEAO.js";
import { _ as _sfc_main$9, S as SectionBanners } from "./SectionBanners-Cdgmr0Bw.js";
import { _ as _sfc_main$5 } from "./ViewModeToggle-DMCnQ0wo.js";
import { U as UniversalImageSlider } from "./UniversalImageSlider-Cu2Xndcn.js";
import { _ as _sfc_main$4 } from "./EntityStats-Dy-sjN5R.js";
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
const _sfc_main$3 = {
  __name: "ModuleLessonGrid",
  __ssrInlineRender: true,
  props: {
    lessons: { type: Array, default: () => [] },
    cols: { type: Number, default: 2 }
  },
  setup(__props) {
    const { t } = useI18n();
    const props = __props;
    const gridClass = computed(() => {
      return props.cols === 2 ? "grid-cols-1 sm:grid-cols-2" : "grid-cols-1 sm:grid-cols-2 lg:grid-cols-3";
    });
    const lessonLink = (lesson) => {
      return (lesson == null ? void 0 : lesson.slug) ? route("public.schoolLessons.show", { slug: lesson.slug }) : "#";
    };
    const shortText = (lesson) => {
      return (lesson == null ? void 0 : lesson.short) || (lesson == null ? void 0 : lesson.subtitle) || "";
    };
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
    const getLessonStatusLabel = (status) => {
      if (!status) return "";
      const key = lessonStatusLabelKeyMap[status];
      return key ? t(key) : status;
    };
    const getLessonAvailabilityLabel = (availability) => {
      if (!availability) return "";
      const key = lessonAvailabilityLabelKeyMap[availability];
      return key ? t(key) : availability;
    };
    const getLessonAccessTypeLabel = (accessType) => {
      if (!accessType) return "";
      const key = lessonAccessTypeLabelKeyMap[accessType];
      return key ? t(key) : accessType;
    };
    return (_ctx, _push, _parent, _attrs) => {
      _push(`<div${ssrRenderAttrs(mergeProps({
        class: ["grid gap-4", gridClass.value]
      }, _attrs))}><!--[-->`);
      ssrRenderList(__props.lessons, (lesson) => {
        _push(`<div class="group flex h-full flex-col overflow-hidden rounded-md border border-gray-200 bg-white shadow-sm transition hover:-translate-y-0.5 hover:shadow-md dark:border-gray-700 dark:bg-gray-900">`);
        _push(ssrRenderComponent(unref(Link), {
          href: lessonLink(lesson)
        }, {
          default: withCtx((_, _push2, _parent2, _scopeId) => {
            if (_push2) {
              _push2(ssrRenderComponent(UniversalImageSlider, {
                entity: lesson,
                "height-class": "h-48",
                "rounded-class": "",
                "wrapper-class": "",
                "img-class": "w-full h-full object-cover transition duration-300 group-hover:scale-105",
                autoplay: true,
                interval: 4e3
              }, null, _parent2, _scopeId));
            } else {
              return [
                createVNode(UniversalImageSlider, {
                  entity: lesson,
                  "height-class": "h-48",
                  "rounded-class": "",
                  "wrapper-class": "",
                  "img-class": "w-full h-full object-cover transition duration-300 group-hover:scale-105",
                  autoplay: true,
                  interval: 4e3
                }, null, 8, ["entity"])
              ];
            }
          }),
          _: 2
        }, _parent));
        _push(`<div class="flex flex-1 flex-col p-4">`);
        _push(ssrRenderComponent(unref(Link), {
          href: lessonLink(lesson),
          class: "inline-flex items-center justify-center gap-2"
        }, {
          default: withCtx((_, _push2, _parent2, _scopeId) => {
            if (_push2) {
              _push2(`<span class="text-base font-semibold text-slate-900/85 group-hover:opacity-75 dark:text-slate-100/85 dark:group-hover:opacity-75"${_scopeId}>${ssrInterpolate(lesson.title)}</span>`);
            } else {
              return [
                createVNode("span", { class: "text-base font-semibold text-slate-900/85 group-hover:opacity-75 dark:text-slate-100/85 dark:group-hover:opacity-75" }, toDisplayString(lesson.title), 1)
              ];
            }
          }),
          _: 2
        }, _parent));
        if (shortText(lesson)) {
          _push(`<div class="mt-3 line-clamp-3 text-sm text-slate-700 dark:text-slate-300">${ssrInterpolate(shortText(lesson))}</div>`);
        } else {
          _push(`<!---->`);
        }
        _push(`<div class="mt-3 flex flex-wrap items-center justify-center gap-2 text-xs font-semibold text-slate-500 dark:text-slate-400">`);
        if (lesson.status) {
          _push(`<div class="flex items-center justify-center gap-1 px-2 py-1 rounded-sm border border-slate-600 dark:border-slate-400"><svg class="h-3 w-3 fill-current text-cyan-700 dark:text-blue-300" viewBox="0 0 24 24"><path d="M22.641,4.232c-.427-.354-1.056-.296-1.409,.128L11.933,15.519l-.433-.433-1.286,1.543,1.078,1.078c.188,.188,.442,.293,.707,.293,.015,0,.03,0,.045,0,.281-.013,.543-.143,.724-.359L22.769,5.64c.354-.424,.296-1.055-.128-1.408Z"></path><path d="M6,18c-.265,0-.52-.105-.707-.293L1.293,13.707c-.391-.391-.391-1.023,0-1.414s1.023-.391,1.414,0l3.226,3.226L15.231,4.36c.354-.424,.983-.481,1.409-.128,.424,.354,.481,.984,.128,1.408L6.769,17.64c-.181,.216-.442,.346-.724,.359-.015,0-.03,0-.045,0Z"></path></svg> ${ssrInterpolate(getLessonStatusLabel(lesson.status))}</div>`);
        } else {
          _push(`<!---->`);
        }
        if (lesson.availability) {
          _push(`<div class="flex items-center justify-center gap-1 px-2 py-1 rounded-sm border border-slate-600 dark:border-slate-400"><svg xmlns="http://www.w3.org/2000/svg" class="w-3 h-3 fill-current text-blue-700 dark:text-blue-300" viewBox="0 0 24 24"><path d="M22.707,6.707,17.293,1.293A1,1,0,0,0,16.586,1H4A3,3,0,0,0,1,4V20a3,3,0,0,0,3,3H20a3,3,0,0,0,3-3V7.414A1,1,0,0,0,22.707,6.707ZM14.5,4h1a.5.5,0,0,1,.5.5v4a.5.5,0,0,1-.5.5h-1a.5.5,0,0,1-.5-.5v-4A.5.5,0,0,1,14.5,4ZM19,12.5v6a.5.5,0,0,1-.5.5H5.5a.5.5,0,0,1-.5-.5v-6a.5.5,0,0,1,.5-.5h13A.5.5,0,0,1,19,12.5Z"></path></svg> ${ssrInterpolate(getLessonAvailabilityLabel(lesson.availability))}</div>`);
        } else {
          _push(`<!---->`);
        }
        if (lesson.access_type) {
          _push(`<div class="flex items-center justify-center gap-1 px-2 py-1 rounded-sm border border-slate-600 dark:border-slate-400"><svg class="h-3 w-3 text-blue-600 dark:text-blue-400" fill="currentColor" viewBox="0 0 576 512"><path d="M423.5 0C339.5.3 272 69.5 272 153.5V224H48c-26.5 0-48 21.5-48 48v192c0 26.5 21.5 48 48 48h352c26.5 0 48-21.5 48-48V272c0-26.5-21.5-48-48-48h-48v-71.1c0-39.6 31.7-72.5 71.3-72.9 40-.4 72.7 32.1 72.7 72v80c0 13.3 10.7 24 24 24h32c13.3 0 24-10.7 24-24v-80C576 68 507.5-.3 423.5 0z"></path></svg> ${ssrInterpolate(getLessonAccessTypeLabel(lesson.access_type))}</div>`);
        } else {
          _push(`<!---->`);
        }
        if (lesson.difficulty !== null && lesson.difficulty !== void 0) {
          _push(`<div class="flex items-center justify-center gap-1 px-2 py-1 rounded-sm border border-slate-600 dark:border-slate-400"><svg xmlns="http://www.w3.org/2000/svg" class="w-3 h-3 fill-current text-amber-600 dark:text-amber-300" viewBox="0 0 24 24"><path d="M12,24a1,1,0,0,1,0-2A10,10,0,0,0,12,2a1,1,0,0,1,0-2,12,12,0,0,1,0,24Z"></path><path class="fill-current text-teal-600 dark:text-teal-300" d="M1.045,13.913a1,1,0,0,1-1-.919C.022,12.665,0,12.336,0,12s.022-.665.049-.994a1,1,0,1,1,1.993.162C2.021,11.442,2,11.719,2,12s.021.558.042.832a1,1,0,0,1-.916,1.078Q1.086,13.913,1.045,13.913Z"></path><path class="fill-current text-teal-600 dark:text-teal-300" d="M6.243,3.641a1,1,0,0,1-.526-1.852,12.022,12.022,0,0,1,1.774-.9,1,1,0,1,1,.754,1.851,10.133,10.133,0,0,0-1.478.757A.993.993,0,0,1,6.243,3.641Z"></path><path class="fill-current text-teal-600 dark:text-teal-300" d="M2.188,8.044a.988.988,0,0,1-.451-.108A1,1,0,0,1,1.3,6.592,12.131,12.131,0,0,1,2.342,4.9,1,1,0,0,1,3.953,6.083,10.1,10.1,0,0,0,3.081,7.5,1,1,0,0,1,2.188,8.044Z"></path><path class="fill-current text-teal-600 dark:text-teal-300" d="M3.128,19.482a1,1,0,0,1-.808-.409,12.049,12.049,0,0,1-1.041-1.7,1,1,0,1,1,1.787-.9,10.047,10.047,0,0,0,.868,1.418,1,1,0,0,1-.217,1.4A.986.986,0,0,1,3.128,19.482Z"></path><path d="M7.853,23.185a.983.983,0,0,1-.377-.075A11.879,11.879,0,0,1,5.7,22.2,1,1,0,0,1,6.75,20.5a10.041,10.041,0,0,0,1.48.761,1,1,0,0,1-.377,1.926Z"></path><path d="M10,17a1,1,0,0,1-.707-.293l-4-4a1,1,0,0,1,1.414-1.414L10,14.586l7.293-7.293a1,1,0,1,1,1.414,1.414l-8,8A1,1,0,0,1,10,17Z"></path></svg> ${ssrInterpolate(unref(t)("difficulty"))}: ${ssrInterpolate(lesson.difficulty)}</div>`);
        } else {
          _push(`<!---->`);
        }
        if (lesson.duration) {
          _push(`<div class="flex items-center justify-center gap-1 px-2 py-1 rounded-sm border border-slate-600 dark:border-slate-400"><svg xmlns="http://www.w3.org/2000/svg" class="w-3 h-3 fill-current text-blue-700 dark:text-blue-300" viewBox="0 0 24 24"><path d="M22,13a1,1,0,0,1,0-2h1.949A12.006,12.006,0,0,0,13,.051V2a1,1,0,0,1-2,0V.051A12.006,12.006,0,0,0,.051,11H2a1,1,0,0,1,0,2H.051A12.006,12.006,0,0,0,11,23.949V22a1,1,0,0,1,2,0v1.949A12.006,12.006,0,0,0,23.949,13Zm-6,0H12a1,1,0,0,1-.832-.445l-4-6a1,1,0,1,1,1.664-1.11L12.535,11H16a1,1,0,0,1,0,2Z"></path></svg> ${ssrInterpolate(unref(t)("duration"))}: ${ssrInterpolate(lesson.duration)} ${ssrInterpolate(unref(t)("minutes"))}</div>`);
        } else {
          _push(`<!---->`);
        }
        if (lesson.rating_avg) {
          _push(`<div class="flex items-center justify-center gap-1 px-2 py-1 rounded-sm border border-slate-600 dark:border-slate-400"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="h-3 w-3 fill-current text-red-400 dark:text-red-300"><path d="M12.746,1.464l3.11,6.3L22.81,8.776a.831.831,0,0,1,.461,1.418l-5.033,4.9,1.188,6.926a.832.832,0,0,1-1.207.877L12,19.632,5.78,22.9a.833.833,0,0,1-1.207-.878L5.761,15.1l-5.033-4.9a.831.831,0,0,1,.461-1.418L8.143,7.765l3.11-6.3A.833.833,0,0,1,12.746,1.464Z"></path></svg> ${ssrInterpolate(unref(t)("rating"))}: ${ssrInterpolate(Number(lesson.rating_avg).toFixed(1))}</div>`);
        } else {
          _push(`<!---->`);
        }
        if (lesson.rating_count) {
          _push(`<div class="flex items-center justify-center gap-1 px-2 py-1 rounded-sm border border-slate-600 dark:border-slate-400"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" class="h-3 w-3 fill-current text-green-600 dark:text-green-400"><path d="M448 0H64C28.7 0 0 28.7 0 64v288c0 35.3 28.7 64 64 64h96v84c0 9.8 11.2 15.5 19.1 9.7L304 416h144c35.3 0 64-28.7 64-64V64c0-35.3-28.7-64-64-64z"></path></svg> ${ssrInterpolate(unref(t)("reviews"))}: ${ssrInterpolate(lesson.rating_count)}</div>`);
        } else {
          _push(`<!---->`);
        }
        _push(`</div><div class="mt-3 flex items-center justify-center">`);
        _push(ssrRenderComponent(_sfc_main$4, {
          views: lesson.views || 0,
          "likes-count": lesson.likes_count || lesson.likes || 0,
          "already-liked": lesson.already_liked || false,
          "route-name": "public.schoolLessons.like",
          "route-params": lesson.id,
          "show-likes-button": true,
          compact: ""
        }, null, _parent));
        _push(`</div><div class="mt-auto pt-4">`);
        _push(ssrRenderComponent(unref(Link), {
          href: lessonLink(lesson),
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
const _sfc_setup$3 = _sfc_main$3.setup;
_sfc_main$3.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Components/Public/Default/School/SchoolLesson/ModuleLessonGrid.vue");
  return _sfc_setup$3 ? _sfc_setup$3(props, ctx) : void 0;
};
const _sfc_main$2 = {
  __name: "ModuleLessonRows",
  __ssrInlineRender: true,
  props: {
    lessons: { type: Array, default: () => [] }
  },
  setup(__props) {
    const { t } = useI18n();
    const lessonLink = (lesson) => {
      return (lesson == null ? void 0 : lesson.slug) ? route("public.schoolLessons.show", { slug: lesson.slug }) : "#";
    };
    const shortText = (lesson) => {
      return (lesson == null ? void 0 : lesson.short) || (lesson == null ? void 0 : lesson.subtitle) || "";
    };
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
    const getLessonStatusLabel = (status) => {
      if (!status) return "";
      const key = lessonStatusLabelKeyMap[status];
      return key ? t(key) : status;
    };
    const getLessonAvailabilityLabel = (availability) => {
      if (!availability) return "";
      const key = lessonAvailabilityLabelKeyMap[availability];
      return key ? t(key) : availability;
    };
    const getLessonAccessTypeLabel = (accessType) => {
      if (!accessType) return "";
      const key = lessonAccessTypeLabelKeyMap[accessType];
      return key ? t(key) : accessType;
    };
    return (_ctx, _push, _parent, _attrs) => {
      _push(`<div${ssrRenderAttrs(mergeProps({ class: "space-y-4" }, _attrs))}><!--[-->`);
      ssrRenderList(__props.lessons, (lesson) => {
        var _a;
        _push(`<div class="group flex flex-col sm:flex-row gap-3 rounded-md border border-gray-200 bg-white shadow-sm p-3 transition hover:shadow-md dark:border-gray-700 dark:bg-gray-900">`);
        if ((_a = lesson.images) == null ? void 0 : _a.length) {
          _push(ssrRenderComponent(unref(Link), {
            href: lessonLink(lesson),
            class: "shrink-0"
          }, {
            default: withCtx((_, _push2, _parent2, _scopeId) => {
              if (_push2) {
                _push2(`<div class="w-full sm:w-[260px]"${_scopeId}>`);
                _push2(ssrRenderComponent(UniversalImageSlider, {
                  entity: lesson,
                  "height-class": "h-48",
                  "rounded-class": "rounded-md",
                  "wrapper-class": "",
                  "img-class": "\n                w-full h-full object-cover\n                transition duration-300\n                group-hover:scale-105\n            ",
                  autoplay: true,
                  interval: 4e3
                }, null, _parent2, _scopeId));
                _push2(`</div>`);
              } else {
                return [
                  createVNode("div", { class: "w-full sm:w-[260px]" }, [
                    createVNode(UniversalImageSlider, {
                      entity: lesson,
                      "height-class": "h-48",
                      "rounded-class": "rounded-md",
                      "wrapper-class": "",
                      "img-class": "\n                w-full h-full object-cover\n                transition duration-300\n                group-hover:scale-105\n            ",
                      autoplay: true,
                      interval: 4e3
                    }, null, 8, ["entity"])
                  ])
                ];
              }
            }),
            _: 2
          }, _parent));
        } else {
          _push(`<!---->`);
        }
        _push(`<div class="min-w-0 flex-1 flex flex-col justify-around"><div class="min-w-0 flex-1 flex flex-col justify-around"><div class="flex items-start justify-start">`);
        _push(ssrRenderComponent(unref(Link), {
          href: lessonLink(lesson),
          class: "min-w-0 inline-flex items-center gap-2"
        }, {
          default: withCtx((_, _push2, _parent2, _scopeId) => {
            if (_push2) {
              _push2(`<span class="truncate text-lg font-semibold text-slate-900/85 dark:text-slate-100/85 group-hover:opacity-75"${_scopeId}>${ssrInterpolate(lesson.title)}</span>`);
            } else {
              return [
                createVNode("span", { class: "truncate text-lg font-semibold text-slate-900/85 dark:text-slate-100/85 group-hover:opacity-75" }, toDisplayString(lesson.title), 1)
              ];
            }
          }),
          _: 2
        }, _parent));
        _push(`</div>`);
        if (shortText(lesson)) {
          _push(`<div class="mt-2 line-clamp-2 text-sm text-slate-700 dark:text-slate-300">${ssrInterpolate(shortText(lesson))}</div>`);
        } else {
          _push(`<!---->`);
        }
        _push(`</div><div class="mt-3 flex flex-wrap items-center justify-center gap-2 text-xs font-semibold text-slate-500 dark:text-slate-400">`);
        if (lesson.status) {
          _push(`<div class="flex items-center justify-center gap-1 px-2 py-1 rounded-sm border border-slate-600 dark:border-slate-400"><svg class="h-3 w-3 fill-current text-cyan-700 dark:text-blue-300" viewBox="0 0 24 24"><path d="M22.641,4.232c-.427-.354-1.056-.296-1.409,.128L11.933,15.519l-.433-.433-1.286,1.543,1.078,1.078c.188,.188,.442,.293,.707,.293,.015,0,.03,0,.045,0,.281-.013,.543-.143,.724-.359L22.769,5.64c.354-.424,.296-1.055-.128-1.408Z"></path><path d="M6,18c-.265,0-.52-.105-.707-.293L1.293,13.707c-.391-.391-.391-1.023,0-1.414s1.023-.391,1.414,0l3.226,3.226L15.231,4.36c.354-.424,.983-.481,1.409-.128,.424,.354,.481,.984,.128,1.408L6.769,17.64c-.181,.216-.442,.346-.724,.359-.015,0-.03,0-.045,0Z"></path></svg> ${ssrInterpolate(getLessonStatusLabel(lesson.status))}</div>`);
        } else {
          _push(`<!---->`);
        }
        if (lesson.availability) {
          _push(`<div class="flex items-center justify-center gap-1 px-2 py-1 rounded-sm border border-slate-600 dark:border-slate-400"><svg xmlns="http://www.w3.org/2000/svg" class="w-3 h-3 fill-current text-blue-700 dark:text-blue-300" viewBox="0 0 24 24"><path d="M22.707,6.707,17.293,1.293A1,1,0,0,0,16.586,1H4A3,3,0,0,0,1,4V20a3,3,0,0,0,3,3H20a3,3,0,0,0,3-3V7.414A1,1,0,0,0,22.707,6.707ZM14.5,4h1a.5.5,0,0,1,.5.5v4a.5.5,0,0,1-.5.5h-1a.5.5,0,0,1-.5-.5v-4A.5.5,0,0,1,14.5,4ZM19,12.5v6a.5.5,0,0,1-.5.5H5.5a.5.5,0,0,1-.5-.5v-6a.5.5,0,0,1,.5-.5h13A.5.5,0,0,1,19,12.5Z"></path></svg> ${ssrInterpolate(getLessonAvailabilityLabel(lesson.availability))}</div>`);
        } else {
          _push(`<!---->`);
        }
        if (lesson.access_type) {
          _push(`<div class="flex items-center justify-center gap-1 px-2 py-1 rounded-sm border border-slate-600 dark:border-slate-400"><svg class="h-3 w-3 text-blue-600 dark:text-blue-400" fill="currentColor" viewBox="0 0 576 512"><path d="M423.5 0C339.5.3 272 69.5 272 153.5V224H48c-26.5 0-48 21.5-48 48v192c0 26.5 21.5 48 48 48h352c26.5 0 48-21.5 48-48V272c0-26.5-21.5-48-48-48h-48v-71.1c0-39.6 31.7-72.5 71.3-72.9 40-.4 72.7 32.1 72.7 72v80c0 13.3 10.7 24 24 24h32c13.3 0 24-10.7 24-24v-80C576 68 507.5-.3 423.5 0z"></path></svg> ${ssrInterpolate(getLessonAccessTypeLabel(lesson.access_type))}</div>`);
        } else {
          _push(`<!---->`);
        }
        if (lesson.difficulty !== null && lesson.difficulty !== void 0) {
          _push(`<div class="flex items-center justify-center gap-1 px-2 py-1 rounded-sm border border-slate-600 dark:border-slate-400"><svg xmlns="http://www.w3.org/2000/svg" class="w-3 h-3 fill-current text-amber-600 dark:text-amber-300" viewBox="0 0 24 24"><path d="M12,24a1,1,0,0,1,0-2A10,10,0,0,0,12,2a1,1,0,0,1,0-2,12,12,0,0,1,0,24Z"></path><path class="fill-current text-teal-600 dark:text-teal-300" d="M1.045,13.913a1,1,0,0,1-1-.919C.022,12.665,0,12.336,0,12s.022-.665.049-.994a1,1,0,1,1,1.993.162C2.021,11.442,2,11.719,2,12s.021.558.042.832a1,1,0,0,1-.916,1.078Q1.086,13.913,1.045,13.913Z"></path><path class="fill-current text-teal-600 dark:text-teal-300" d="M6.243,3.641a1,1,0,0,1-.526-1.852,12.022,12.022,0,0,1,1.774-.9,1,1,0,1,1,.754,1.851,10.133,10.133,0,0,0-1.478.757A.993.993,0,0,1,6.243,3.641Z"></path><path class="fill-current text-teal-600 dark:text-teal-300" d="M2.188,8.044a.988.988,0,0,1-.451-.108A1,1,0,0,1,1.3,6.592,12.131,12.131,0,0,1,2.342,4.9,1,1,0,0,1,3.953,6.083,10.1,10.1,0,0,0,3.081,7.5,1,1,0,0,1,2.188,8.044Z"></path><path class="fill-current text-teal-600 dark:text-teal-300" d="M3.128,19.482a1,1,0,0,1-.808-.409,12.049,12.049,0,0,1-1.041-1.7,1,1,0,1,1,1.787-.9,10.047,10.047,0,0,0,.868,1.418,1,1,0,0,1-.217,1.4A.986.986,0,0,1,3.128,19.482Z"></path><path d="M7.853,23.185a.983.983,0,0,1-.377-.075A11.879,11.879,0,0,1,5.7,22.2,1,1,0,0,1,6.75,20.5a10.041,10.041,0,0,0,1.48.761,1,1,0,0,1-.377,1.926Z"></path><path d="M10,17a1,1,0,0,1-.707-.293l-4-4a1,1,0,0,1,1.414-1.414L10,14.586l7.293-7.293a1,1,0,1,1,1.414,1.414l-8,8A1,1,0,0,1,10,17Z"></path></svg> ${ssrInterpolate(unref(t)("difficulty"))}: ${ssrInterpolate(lesson.difficulty)}</div>`);
        } else {
          _push(`<!---->`);
        }
        if (lesson.duration) {
          _push(`<div class="flex items-center justify-center gap-1 px-2 py-1 rounded-sm border border-slate-600 dark:border-slate-400"><svg xmlns="http://www.w3.org/2000/svg" class="w-3 h-3 fill-current text-blue-700 dark:text-blue-300" viewBox="0 0 24 24"><path d="M22,13a1,1,0,0,1,0-2h1.949A12.006,12.006,0,0,0,13,.051V2a1,1,0,0,1-2,0V.051A12.006,12.006,0,0,0,.051,11H2a1,1,0,0,1,0,2H.051A12.006,12.006,0,0,0,11,23.949V22a1,1,0,0,1,2,0v1.949A12.006,12.006,0,0,0,23.949,13Zm-6,0H12a1,1,0,0,1-.832-.445l-4-6a1,1,0,1,1,1.664-1.11L12.535,11H16a1,1,0,0,1,0,2Z"></path></svg> ${ssrInterpolate(unref(t)("duration"))}: ${ssrInterpolate(lesson.duration)} ${ssrInterpolate(unref(t)("minutes"))}</div>`);
        } else {
          _push(`<!---->`);
        }
        if (lesson.rating_avg) {
          _push(`<div class="flex items-center justify-center gap-1 px-2 py-1 rounded-sm border border-slate-600 dark:border-slate-400"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="h-3 w-3 fill-current text-red-400 dark:text-red-300"><path d="M12.746,1.464l3.11,6.3L22.81,8.776a.831.831,0,0,1,.461,1.418l-5.033,4.9,1.188,6.926a.832.832,0,0,1-1.207.877L12,19.632,5.78,22.9a.833.833,0,0,1-1.207-.878L5.761,15.1l-5.033-4.9a.831.831,0,0,1,.461-1.418L8.143,7.765l3.11-6.3A.833.833,0,0,1,12.746,1.464Z"></path></svg> ${ssrInterpolate(unref(t)("rating"))}: ${ssrInterpolate(Number(lesson.rating_avg).toFixed(1))}</div>`);
        } else {
          _push(`<!---->`);
        }
        if (lesson.rating_count) {
          _push(`<div class="flex items-center justify-center gap-1 px-2 py-1 rounded-sm border border-slate-600 dark:border-slate-400"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" class="h-3 w-3 fill-current text-green-600 dark:text-green-400"><path d="M448 0H64C28.7 0 0 28.7 0 64v288c0 35.3 28.7 64 64 64h96v84c0 9.8 11.2 15.5 19.1 9.7L304 416h144c35.3 0 64-28.7 64-64V64c0-35.3-28.7-64-64-64z"></path></svg> ${ssrInterpolate(unref(t)("reviews"))}: ${ssrInterpolate(lesson.rating_count)}</div>`);
        } else {
          _push(`<!---->`);
        }
        _push(`</div><div class="mt-2 flex items-center justify-between gap-3">`);
        _push(ssrRenderComponent(_sfc_main$4, {
          views: lesson.views || 0,
          "likes-count": lesson.likes_count || lesson.likes || 0,
          "already-liked": lesson.already_liked || false,
          "route-name": "public.schoolLessons.like",
          "route-params": lesson.id,
          "show-likes-button": true,
          compact: ""
        }, null, _parent));
        _push(ssrRenderComponent(unref(Link), {
          href: lessonLink(lesson),
          class: "w-1/2 flex items-center justify-center gap-1 rounded-sm px-3 py-1 btn-default"
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
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Components/Public/Default/School/SchoolLesson/ModuleLessonRows.vue");
  return _sfc_setup$2 ? _sfc_setup$2(props, ctx) : void 0;
};
const VIEW_KEY = "public_module_lessons_view";
const _sfc_main$1 = {
  __name: "ModuleLessonsSection",
  __ssrInlineRender: true,
  props: {
    lessons: { type: [Array, Object], default: () => [] },
    cols: { type: Number, default: 2 }
  },
  setup(__props) {
    const { t } = useI18n();
    const props = __props;
    const normalizeList = (value) => {
      if (Array.isArray(value)) return value;
      if (Array.isArray(value == null ? void 0 : value.data)) return value.data;
      return [];
    };
    const lessonsList = computed(() => normalizeList(props.lessons));
    const viewMode = ref(localStorage.getItem(VIEW_KEY) || "grid");
    watch(viewMode, (v) => {
      localStorage.setItem(VIEW_KEY, v);
    });
    const lessonsCount = computed(() => lessonsList.value.length);
    return (_ctx, _push, _parent, _attrs) => {
      if (lessonsCount.value > 0) {
        _push(`<section${ssrRenderAttrs(mergeProps({ class: "mt-8" }, _attrs))}><div class="mb-4 flex flex-wrap items-center justify-between gap-3"><div class="my-3 text-start center text-xs text-slate-500 dark:text-slate-400">${ssrInterpolate(unref(t)("lessons"))}: <span class="font-bold">${ssrInterpolate(lessonsCount.value)}</span></div><div class="flex items-center gap-2"><svg class="h-6 w-6 text-slate-600 dark:text-slate-300" fill="currentColor" viewBox="0 0 24 24"><path d="M4 4h16v14H4zm2 2v10h12V6zm-2 14h16v2H4z"></path></svg><h2 class="text-lg font-semibold text-gray-700 dark:text-gray-300">${ssrInterpolate(unref(t)("lessons"))}</h2></div>`);
        _push(ssrRenderComponent(_sfc_main$5, {
          modelValue: viewMode.value,
          "onUpdate:modelValue": ($event) => viewMode.value = $event,
          "grid-value": "grid",
          "row-value": "row",
          size: "sm"
        }, null, _parent));
        _push(`</div>`);
        if (viewMode.value === "grid") {
          _push(ssrRenderComponent(_sfc_main$3, {
            lessons: lessonsList.value,
            cols: __props.cols
          }, null, _parent));
        } else {
          _push(ssrRenderComponent(_sfc_main$2, { lessons: lessonsList.value }, null, _parent));
        }
        _push(`</section>`);
      } else {
        _push(`<!---->`);
      }
    };
  }
};
const _sfc_setup$1 = _sfc_main$1.setup;
_sfc_main$1.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Components/Public/Default/School/SchoolLesson/ModuleLessonsSection.vue");
  return _sfc_setup$1 ? _sfc_setup$1(props, ctx) : void 0;
};
const _sfc_main = {
  __name: "Show",
  __ssrInlineRender: true,
  props: {
    title: String,
    canLogin: Boolean,
    canRegister: Boolean,
    module: { type: Object, default: () => ({}) },
    lessons: { type: [Array, Object], default: () => [] },
    trackTree: { type: Array, default: () => [] },
    locale: { type: String, default: "ru" },
    mainVideos: { type: Array, default: () => [] },
    mainBanners: { type: Array, default: () => [] }
  },
  setup(__props) {
    const { t } = useI18n();
    const page = usePage();
    const props = __props;
    const normalizeList = (value) => {
      if (Array.isArray(value)) return value;
      if (Array.isArray(value == null ? void 0 : value.data)) return value.data;
      return [];
    };
    const absoluteUrl = (value) => {
      if (!value) return "";
      if (/^https?:\/\//i.test(value)) return value;
      if (typeof window === "undefined") return value;
      try {
        return new URL(value, window.location.origin).href;
      } catch {
        return value;
      }
    };
    const stripHtml = (value) => {
      if (!value) return "";
      return String(value).replace(/<[^>]*>/g, " ").replace(/\s+/g, " ").trim();
    };
    const moduleData = computed(() => props.module ?? {});
    const translation = computed(() => {
      var _a;
      return ((_a = moduleData.value) == null ? void 0 : _a.translation) ?? {};
    });
    const moduleTitle = computed(() => {
      var _a;
      return ((_a = translation.value) == null ? void 0 : _a.title) ?? "";
    });
    const moduleSubtitle = computed(() => {
      var _a;
      return ((_a = translation.value) == null ? void 0 : _a.subtitle) ?? "";
    });
    const moduleShort = computed(() => {
      var _a;
      return ((_a = translation.value) == null ? void 0 : _a.short) ?? "";
    });
    const moduleDescription = computed(() => {
      var _a;
      return ((_a = translation.value) == null ? void 0 : _a.description) ?? "";
    });
    const parentCourse = computed(() => {
      var _a;
      return ((_a = moduleData.value) == null ? void 0 : _a.course) ?? null;
    });
    const courseTranslation = computed(() => {
      var _a;
      return ((_a = parentCourse.value) == null ? void 0 : _a.translation) ?? {};
    });
    const courseTitle = computed(
      () => {
        var _a;
        return ((_a = courseTranslation.value) == null ? void 0 : _a.title) || t("course");
      }
    );
    const courseRouteUrl = computed(() => {
      var _a;
      if (!((_a = parentCourse.value) == null ? void 0 : _a.slug)) return "";
      return route("public.schoolCourses.show", {
        slug: parentCourse.value.slug
      });
    });
    const courseUrl = computed(() => absoluteUrl(courseRouteUrl.value));
    const lessonsList = computed(() => normalizeList(props.lessons));
    const trackTree = computed(
      () => Array.isArray(props.trackTree) ? props.trackTree : []
    );
    const mainVideosList = computed(() => normalizeList(props.mainVideos));
    const mainBannersList = computed(() => normalizeList(props.mainBanners));
    const lessonsCount = computed(
      () => {
        var _a;
        return Number(((_a = moduleData.value) == null ? void 0 : _a.lessons_count) ?? lessonsList.value.length ?? 0) || 0;
      }
    );
    const moduleImages = computed(() => {
      var _a;
      return normalizeList((_a = moduleData.value) == null ? void 0 : _a.images);
    });
    const firstModuleImage = computed(() => moduleImages.value[0] ?? null);
    const hasModuleImages = computed(() => moduleImages.value.length > 0);
    const firstModuleImageUrl = computed(
      () => {
        var _a, _b, _c, _d;
        return absoluteUrl(
          ((_a = firstModuleImage.value) == null ? void 0 : _a.webp_url) || ((_b = firstModuleImage.value) == null ? void 0 : _b.image_url) || ((_c = firstModuleImage.value) == null ? void 0 : _c.url) || ((_d = firstModuleImage.value) == null ? void 0 : _d.thumb_url) || ""
        );
      }
    );
    const seoTitle = computed(
      () => {
        var _a;
        return ((_a = translation.value) == null ? void 0 : _a.meta_title) || moduleTitle.value || t("modules");
      }
    );
    const seoDescription = computed(
      () => {
        var _a;
        return ((_a = translation.value) == null ? void 0 : _a.meta_desc) || moduleShort.value || stripHtml(moduleDescription.value) || "";
      }
    );
    const seoKeywords = computed(
      () => {
        var _a;
        return ((_a = translation.value) == null ? void 0 : _a.meta_keywords) ?? "";
      }
    );
    const contentLocale = computed(
      () => {
        var _a;
        return ((_a = translation.value) == null ? void 0 : _a.locale) || props.locale || "ru";
      }
    );
    const ogLocale = computed(() => {
      try {
        const locale = new Intl.Locale(contentLocale.value).maximize();
        return `${locale.language}_${locale.region || locale.language.toUpperCase()}`;
      } catch {
        return contentLocale.value;
      }
    });
    const moduleRouteUrl = computed(() => {
      var _a, _b;
      if (!((_a = parentCourse.value) == null ? void 0 : _a.slug) || !((_b = moduleData.value) == null ? void 0 : _b.slug)) return "";
      return route("public.schoolModules.show", {
        courseSlug: parentCourse.value.slug,
        slug: moduleData.value.slug
      });
    });
    const canonicalUrl = computed(() => absoluteUrl(moduleRouteUrl.value));
    const coursesIndexUrl = computed(() => absoluteUrl(route("public.schoolCourses.index")));
    const homeUrl = computed(() => absoluteUrl(route("home")));
    const siteName = computed(
      () => {
        var _a, _b, _c, _d;
        return ((_b = (_a = page.props) == null ? void 0 : _a.siteSettings) == null ? void 0 : _b.siteName) || ((_d = (_c = page.props) == null ? void 0 : _c.siteSettings) == null ? void 0 : _d.SiteName) || "";
      }
    );
    const schemaDuration = computed(() => {
      var _a;
      const minutes = Number((_a = moduleData.value) == null ? void 0 : _a.duration);
      if (!Number.isFinite(minutes) || minutes <= 0) return null;
      return `PT${Math.round(minutes)}M`;
    });
    const moduleSchema = computed(() => {
      var _a, _b, _c, _d, _e;
      const schema = {
        "@context": "https://schema.org",
        "@type": "LearningResource",
        name: moduleTitle.value || seoTitle.value,
        url: canonicalUrl.value || moduleRouteUrl.value,
        inLanguage: contentLocale.value
      };
      const description = stripHtml(
        seoDescription.value || moduleShort.value || moduleDescription.value
      );
      if (description) schema.description = description;
      if (firstModuleImageUrl.value) schema.image = firstModuleImageUrl.value;
      if ((_a = moduleData.value) == null ? void 0 : _a.published_at) schema.datePublished = moduleData.value.published_at;
      if (schemaDuration.value) schema.timeRequired = schemaDuration.value;
      if (parentCourse.value && courseTitle.value) {
        schema.isPartOf = {
          "@type": "Course",
          name: courseTitle.value
        };
        if (courseUrl.value) {
          schema.isPartOf.url = courseUrl.value;
        }
      }
      const ratingAvg = Number((_b = moduleData.value) == null ? void 0 : _b.rating_avg);
      const ratingCount = Number((_c = moduleData.value) == null ? void 0 : _c.rating_count);
      if (Number.isFinite(ratingAvg) && ratingAvg > 0 && Number.isFinite(ratingCount) && ratingCount > 0) {
        schema.aggregateRating = {
          "@type": "AggregateRating",
          ratingValue: ratingAvg,
          ratingCount,
          bestRating: 5,
          worstRating: 1
        };
      }
      const interactions = [];
      const views = Number((_d = moduleData.value) == null ? void 0 : _d.views);
      const likes = Number((_e = moduleData.value) == null ? void 0 : _e.likes_count);
      if (Number.isFinite(views) && views > 0) {
        interactions.push({
          "@type": "InteractionCounter",
          interactionType: { "@type": "ViewAction" },
          userInteractionCount: views
        });
      }
      if (Number.isFinite(likes) && likes > 0) {
        interactions.push({
          "@type": "InteractionCounter",
          interactionType: { "@type": "LikeAction" },
          userInteractionCount: likes
        });
      }
      if (interactions.length) {
        schema.interactionStatistic = interactions;
      }
      return schema;
    });
    const breadcrumbSchema = computed(() => {
      var _a;
      const items = [
        {
          "@type": "ListItem",
          position: 1,
          name: t("home"),
          item: homeUrl.value
        },
        {
          "@type": "ListItem",
          position: 2,
          name: t("courses"),
          item: coursesIndexUrl.value
        }
      ];
      if ((_a = parentCourse.value) == null ? void 0 : _a.slug) {
        items.push({
          "@type": "ListItem",
          position: 3,
          name: courseTitle.value,
          item: courseUrl.value
        });
      }
      items.push({
        "@type": "ListItem",
        position: items.length + 1,
        name: moduleTitle.value || seoTitle.value,
        item: canonicalUrl.value || moduleRouteUrl.value
      });
      return {
        "@context": "https://schema.org",
        "@type": "BreadcrumbList",
        itemListElement: items
      };
    });
    const moduleSchemaJson = computed(() => JSON.stringify(moduleSchema.value));
    const breadcrumbSchemaJson = computed(() => JSON.stringify(breadcrumbSchema.value));
    const { siteSettings } = page.props;
    const showLeft = computed(
      () => !(siteSettings == null ? void 0 : siteSettings.ViewLeftColumn) || siteSettings.ViewLeftColumn === "true"
    );
    const showRight = computed(
      () => !(siteSettings == null ? void 0 : siteSettings.ViewRightColumn) || siteSettings.ViewRightColumn === "true"
    );
    const leftCollapsed = ref(false);
    const rightCollapsed = ref(false);
    const gridCols = computed(() => {
      const leftExpanded = showLeft.value && !leftCollapsed.value;
      const rightExpanded = showRight.value && !rightCollapsed.value;
      if (leftExpanded && rightExpanded) return 2;
      if (leftExpanded || rightExpanded) return 3;
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
            _push2(`<meta name="viewport" content="width=device-width, initial-scale=1"${_scopeId}><meta name="robots" content="index, follow, max-image-preview:large"${_scopeId}><meta name="googlebot" content="index, follow, max-image-preview:large"${_scopeId}>`);
            if (canonicalUrl.value) {
              _push2(`<link rel="canonical"${ssrRenderAttr("href", canonicalUrl.value)}${_scopeId}>`);
            } else {
              _push2(`<!---->`);
            }
            _push2(`<meta property="og:title"${ssrRenderAttr("content", seoTitle.value)}${_scopeId}><meta property="og:description"${ssrRenderAttr("content", seoDescription.value)}${_scopeId}><meta property="og:type" content="website"${_scopeId}>`);
            if (canonicalUrl.value) {
              _push2(`<meta property="og:url"${ssrRenderAttr("content", canonicalUrl.value)}${_scopeId}>`);
            } else {
              _push2(`<!---->`);
            }
            if (firstModuleImageUrl.value) {
              _push2(`<meta property="og:image"${ssrRenderAttr("content", firstModuleImageUrl.value)}${_scopeId}>`);
            } else {
              _push2(`<!---->`);
            }
            if (firstModuleImageUrl.value) {
              _push2(`<meta property="og:image:alt"${ssrRenderAttr("content", moduleTitle.value)}${_scopeId}>`);
            } else {
              _push2(`<!---->`);
            }
            _push2(`<meta property="og:locale"${ssrRenderAttr("content", ogLocale.value)}${_scopeId}>`);
            if (siteName.value) {
              _push2(`<meta property="og:site_name"${ssrRenderAttr("content", siteName.value)}${_scopeId}>`);
            } else {
              _push2(`<!---->`);
            }
            _push2(`<meta name="twitter:card"${ssrRenderAttr("content", firstModuleImageUrl.value ? "summary_large_image" : "summary")}${_scopeId}><meta name="twitter:title"${ssrRenderAttr("content", seoTitle.value)}${_scopeId}><meta name="twitter:description"${ssrRenderAttr("content", seoDescription.value)}${_scopeId}>`);
            if (firstModuleImageUrl.value) {
              _push2(`<meta name="twitter:image"${ssrRenderAttr("content", firstModuleImageUrl.value)}${_scopeId}>`);
            } else {
              _push2(`<!---->`);
            }
            if (firstModuleImageUrl.value) {
              _push2(`<meta name="twitter:image:alt"${ssrRenderAttr("content", moduleTitle.value)}${_scopeId}>`);
            } else {
              _push2(`<!---->`);
            }
            _push2(`<meta name="DC.Title"${ssrRenderAttr("content", seoTitle.value)}${_scopeId}><meta name="DC.Description"${ssrRenderAttr("content", seoDescription.value)}${_scopeId}>`);
            if (seoKeywords.value) {
              _push2(`<meta name="DC.Subject"${ssrRenderAttr("content", seoKeywords.value)}${_scopeId}>`);
            } else {
              _push2(`<!---->`);
            }
            _push2(`<meta name="DC.Type" content="Text"${_scopeId}><meta name="DC.Format" content="text/html"${_scopeId}><meta name="DC.Language"${ssrRenderAttr("content", contentLocale.value)}${_scopeId}>`);
            if (canonicalUrl.value) {
              _push2(`<meta name="DC.Identifier"${ssrRenderAttr("content", canonicalUrl.value)}${_scopeId}>`);
            } else {
              _push2(`<!---->`);
            }
            if (siteName.value) {
              _push2(`<meta name="DC.Publisher"${ssrRenderAttr("content", siteName.value)}${_scopeId}>`);
            } else {
              _push2(`<!---->`);
            }
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
                name: "viewport",
                content: "width=device-width, initial-scale=1"
              }),
              createVNode("meta", {
                name: "robots",
                content: "index, follow, max-image-preview:large"
              }),
              createVNode("meta", {
                name: "googlebot",
                content: "index, follow, max-image-preview:large"
              }),
              canonicalUrl.value ? (openBlock(), createBlock("link", {
                key: 1,
                rel: "canonical",
                href: canonicalUrl.value
              }, null, 8, ["href"])) : createCommentVNode("", true),
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
              canonicalUrl.value ? (openBlock(), createBlock("meta", {
                key: 2,
                property: "og:url",
                content: canonicalUrl.value
              }, null, 8, ["content"])) : createCommentVNode("", true),
              firstModuleImageUrl.value ? (openBlock(), createBlock("meta", {
                key: 3,
                property: "og:image",
                content: firstModuleImageUrl.value
              }, null, 8, ["content"])) : createCommentVNode("", true),
              firstModuleImageUrl.value ? (openBlock(), createBlock("meta", {
                key: 4,
                property: "og:image:alt",
                content: moduleTitle.value
              }, null, 8, ["content"])) : createCommentVNode("", true),
              createVNode("meta", {
                property: "og:locale",
                content: ogLocale.value
              }, null, 8, ["content"]),
              siteName.value ? (openBlock(), createBlock("meta", {
                key: 5,
                property: "og:site_name",
                content: siteName.value
              }, null, 8, ["content"])) : createCommentVNode("", true),
              createVNode("meta", {
                name: "twitter:card",
                content: firstModuleImageUrl.value ? "summary_large_image" : "summary"
              }, null, 8, ["content"]),
              createVNode("meta", {
                name: "twitter:title",
                content: seoTitle.value
              }, null, 8, ["content"]),
              createVNode("meta", {
                name: "twitter:description",
                content: seoDescription.value
              }, null, 8, ["content"]),
              firstModuleImageUrl.value ? (openBlock(), createBlock("meta", {
                key: 6,
                name: "twitter:image",
                content: firstModuleImageUrl.value
              }, null, 8, ["content"])) : createCommentVNode("", true),
              firstModuleImageUrl.value ? (openBlock(), createBlock("meta", {
                key: 7,
                name: "twitter:image:alt",
                content: moduleTitle.value
              }, null, 8, ["content"])) : createCommentVNode("", true),
              createVNode("meta", {
                name: "DC.Title",
                content: seoTitle.value
              }, null, 8, ["content"]),
              createVNode("meta", {
                name: "DC.Description",
                content: seoDescription.value
              }, null, 8, ["content"]),
              seoKeywords.value ? (openBlock(), createBlock("meta", {
                key: 8,
                name: "DC.Subject",
                content: seoKeywords.value
              }, null, 8, ["content"])) : createCommentVNode("", true),
              createVNode("meta", {
                name: "DC.Type",
                content: "Text"
              }),
              createVNode("meta", {
                name: "DC.Format",
                content: "text/html"
              }),
              createVNode("meta", {
                name: "DC.Language",
                content: contentLocale.value
              }, null, 8, ["content"]),
              canonicalUrl.value ? (openBlock(), createBlock("meta", {
                key: 9,
                name: "DC.Identifier",
                content: canonicalUrl.value
              }, null, 8, ["content"])) : createCommentVNode("", true),
              siteName.value ? (openBlock(), createBlock("meta", {
                key: 10,
                name: "DC.Publisher",
                content: siteName.value
              }, null, 8, ["content"])) : createCommentVNode("", true),
              (openBlock(), createBlock(resolveDynamicComponent("script"), {
                type: "application/ld+json",
                textContent: toDisplayString(moduleSchemaJson.value)
              }, null, 8, ["textContent"])),
              (openBlock(), createBlock(resolveDynamicComponent("script"), {
                type: "application/ld+json",
                textContent: toDisplayString(breadcrumbSchemaJson.value)
              }, null, 8, ["textContent"]))
            ];
          }
        }),
        _: 1
      }, _parent));
      _push(ssrRenderComponent(_sfc_main$6, {
        title: __props.title,
        "can-login": __props.canLogin,
        "can-register": __props.canRegister
      }, {
        default: withCtx((_, _push2, _parent2, _scopeId) => {
          var _a, _b, _c, _d;
          if (_push2) {
            _push2(ssrRenderComponent(_sfc_main$7, null, null, _parent2, _scopeId));
            _push2(`<main class="min-h-screen px-1 lg:px-6 max-w-full"${_scopeId}><div class="mx-auto tracking-wider pt-20 lg:pt-44"${_scopeId}><div class="ext-color w-full min-w-0 py-3 px-1 flex flex-col lg:flex-row gap-4 rounded-3xl border-2 border-slate-300 dark:border-slate-500"${_scopeId}>`);
            if (showLeft.value) {
              _push2(`<aside class="${ssrRenderClass([leftCollapsed.value ? "lg:w-6" : "lg:w-72", "shrink-0 pl-3 transition-all duration-300"])}"${_scopeId}>`);
              _push2(ssrRenderComponent(_sfc_main$8, {
                "track-tree": trackTree.value,
                collapsed: leftCollapsed.value,
                onCollapsed: ($event) => leftCollapsed.value = $event
              }, null, _parent2, _scopeId));
              _push2(`</aside>`);
            } else {
              _push2(`<!---->`);
            }
            _push2(`<section class="w-full pb-6 slate-1 min-w-0"${_scopeId}><div class="mx-auto max-w-6xl"${_scopeId}><article class="selection:bg-red-400 selection:text-white" itemscope itemtype="https://schema.org/LearningResource"${ssrRenderAttr("itemid", canonicalUrl.value)}${_scopeId}><meta itemprop="url"${ssrRenderAttr("content", canonicalUrl.value)}${_scopeId}><meta itemprop="inLanguage"${ssrRenderAttr("content", contentLocale.value)}${_scopeId}>`);
            if (moduleData.value.published_at) {
              _push2(`<meta itemprop="datePublished"${ssrRenderAttr("content", moduleData.value.published_at)}${_scopeId}>`);
            } else {
              _push2(`<!---->`);
            }
            if (schemaDuration.value) {
              _push2(`<meta itemprop="timeRequired"${ssrRenderAttr("content", schemaDuration.value)}${_scopeId}>`);
            } else {
              _push2(`<!---->`);
            }
            _push2(`<nav class="text-sm mb-3" aria-label="Breadcrumb"${_scopeId}><ol class="flex flex-wrap items-center font-semibold"${_scopeId}><li${_scopeId}>`);
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
            _push2(`</li><li aria-hidden="true"${_scopeId}><span class="mx-2 breadcrumbs"${_scopeId}>/</span></li><li${_scopeId}>`);
            _push2(ssrRenderComponent(unref(Link), {
              href: _ctx.route("public.schoolCourses.index"),
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
            _push2(`</li>`);
            if ((_a = parentCourse.value) == null ? void 0 : _a.slug) {
              _push2(`<!--[--><li aria-hidden="true"${_scopeId}><span class="mx-2 breadcrumbs"${_scopeId}>/</span></li><li${_scopeId}>`);
              _push2(ssrRenderComponent(unref(Link), {
                href: _ctx.route("public.schoolCourses.show", { slug: parentCourse.value.slug }),
                class: "breadcrumb-link hover:underline"
              }, {
                default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                  if (_push3) {
                    _push3(`${ssrInterpolate(courseTitle.value)}`);
                  } else {
                    return [
                      createTextVNode(toDisplayString(courseTitle.value), 1)
                    ];
                  }
                }),
                _: 1
              }, _parent2, _scopeId));
              _push2(`</li><!--]-->`);
            } else {
              _push2(`<!---->`);
            }
            _push2(`<li aria-hidden="true"${_scopeId}><span class="mx-2 breadcrumbs"${_scopeId}>/</span></li><li class="breadcrumbs" aria-current="page"${_scopeId}>${ssrInterpolate(moduleTitle.value)}</li></ol></nav><div class="flex flex-wrap items-center justify-center gap-3 title my-3"${_scopeId}><h1 class="text-2xl font-bold" itemprop="name"${_scopeId}>${ssrInterpolate(moduleTitle.value)}</h1>`);
            if (moduleData.value.views > 0) {
              _push2(`<div${ssrRenderAttr("title", unref(t)("views"))} class="flex items-center justify-center gap-1" itemprop="interactionStatistic" itemscope itemtype="https://schema.org/InteractionCounter"${_scopeId}><link itemprop="interactionType" href="https://schema.org/ViewAction"${_scopeId}><meta itemprop="userInteractionCount"${ssrRenderAttr("content", moduleData.value.views)}${_scopeId}><svg class="h-4 w-4 text-slate-600/85 dark:text-slate-200/85" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512" fill="currentColor" aria-hidden="true"${_scopeId}><path d="M569.354 231.631C512.97 135.949 407.81 72 288 72 168.14 72 63.004 135.994 6.646 231.631a47.999 47.999 0 0 0 0 48.739C63.031 376.051 168.19 440 288 440c119.86 0 224.996-63.994 281.354-159.631a47.997 47.997 0 0 0 0-48.738zM288 392c-102.556 0-192.091-54.701-240-136 44.157-74.933 123.677-127.27 216.162-135.007C273.958 131.078 280 144.83 280 160c0 30.928-25.072 56-56 56s-56-25.072-56-56l.001-.042C157.794 179.043 152 200.844 152 224c0 75.111 60.889 136 136 136s136-60.889 136-136c0-31.031-10.4-59.629-27.895-82.515C451.704 164.638 498.009 205.106 528 256c-47.908 81.299-137.444 136-240 136z"${_scopeId}></path></svg><span class="text-center text-sm text-gray-500"${_scopeId}>${ssrInterpolate(moduleData.value.views)}</span></div>`);
            } else {
              _push2(`<!---->`);
            }
            _push2(`</div>`);
            if (moduleSubtitle.value) {
              _push2(`<div class="mt-1 mb-3 text-sm subtitle text-center"${_scopeId}>${ssrInterpolate(moduleSubtitle.value)}</div>`);
            } else {
              _push2(`<!---->`);
            }
            if (hasModuleImages.value) {
              _push2(`<div class="flex items-center justify-center"${_scopeId}><div class="w-full"${_scopeId}>`);
              _push2(ssrRenderComponent(ImageGalleryMain, {
                images: moduleImages.value,
                alt: moduleTitle.value,
                itemprop: "image",
                loading: "eager",
                fetchpriority: "high",
                "rounded-class": "rounded-lg",
                "shadow-class": "shadow-lg shadow-gray-400 dark:shadow-gray-700",
                "img-class": "w-full h-full object-cover"
              }, null, _parent2, _scopeId));
              _push2(`</div></div>`);
            } else {
              _push2(`<!---->`);
            }
            _push2(`<div class="my-4 flex flex-wrap items-center justify-center gap-3 text-sm text-slate-600 dark:text-slate-300"${_scopeId}>`);
            if (lessonsCount.value) {
              _push2(`<span class="rounded-sm border border-gray-400 flex items-center justify-center gap-1 px-3 py-1"${_scopeId}><svg class="h-4 w-4 text-sky-600/85 dark:text-sky-300/85" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true"${_scopeId}><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25"${_scopeId}></path></svg> ${ssrInterpolate(unref(t)("lessons"))}: ${ssrInterpolate(lessonsCount.value)}</span>`);
            } else {
              _push2(`<!---->`);
            }
            if (moduleData.value.duration) {
              _push2(`<span class="rounded-sm border border-gray-400 flex items-center justify-center gap-1 px-3 py-1"${_scopeId}><svg class="w-3 h-3 text-blue-700 dark:text-blue-300" viewBox="0 0 24 24" aria-hidden="true"${_scopeId}><path class="fill-current" d="M22,13a1,1,0,0,1,0-2h1.949A12.006,12.006,0,0,0,13,.051V2a1,1,0,0,1-2,0V.051A12.006,12.006,0,0,0,.051,11H2a1,1,0,0,1,0,2H.051A12.006,12.006,0,0,0,11,23.949V22a1,1,0,0,1,2,0v1.949A12.006,12.006,0,0,0,23.949,13Zm-6,0H12a1,1,0,0,1-.832-.445l-4-6a1,1,0,1,1,1.664-1.11L12.535,11H16a1,1,0,0,1,0,2Z"${_scopeId}></path></svg> ${ssrInterpolate(unref(t)("duration"))}: ${ssrInterpolate(moduleData.value.duration)} ${ssrInterpolate(unref(t)("minutes"))}</span>`);
            } else {
              _push2(`<!---->`);
            }
            if (moduleData.value.rating_avg) {
              _push2(`<span class="rounded-sm border border-gray-400 flex items-center justify-center gap-1 px-3 py-1" itemprop="aggregateRating" itemscope itemtype="https://schema.org/AggregateRating"${_scopeId}><meta itemprop="ratingValue"${ssrRenderAttr("content", moduleData.value.rating_avg)}${_scopeId}><meta itemprop="ratingCount"${ssrRenderAttr("content", moduleData.value.rating_count || 0)}${_scopeId}><meta itemprop="bestRating" content="5"${_scopeId}><meta itemprop="worstRating" content="1"${_scopeId}><svg viewBox="0 0 24 24" class="h-3 w-3 text-red-400 dark:text-red-300" aria-hidden="true"${_scopeId}><path class="fill-current" d="M12.746,1.464l3.11,6.3L22.81,8.776a.831.831,0,0,1,.461,1.418l-5.033,4.9,1.188,6.926a.832.832,0,0,1-1.207.877L12,19.632,5.78,22.9a.833.833,0,0,1-1.207-.878L5.761,15.1l-5.033-4.9a.831.831,0,0,1,.461-1.418L8.143,7.765l3.11-6.3A.833.833,0,0,1,12.746,1.464Z"${_scopeId}></path></svg> ${ssrInterpolate(unref(t)("rating"))}: ${ssrInterpolate(Number(moduleData.value.rating_avg).toFixed(1))}</span>`);
            } else {
              _push2(`<!---->`);
            }
            if (moduleData.value.rating_count) {
              _push2(`<span class="rounded-sm border border-gray-400 flex items-center justify-center gap-1 px-3 py-1"${_scopeId}><svg class="w-3 h-3 text-teal-600/85 dark:text-teal-300/85" fill="currentColor" viewBox="0 0 512 512" aria-hidden="true"${_scopeId}><path d="M448 0H64C28.7 0 0 28.7 0 64v288c0 35.3 28.7 64 64 64h96v84c0 9.8 11.2 15.5 19.1 9.7L304 416h144c35.3 0 64-28.7 64-64V64c0-35.3-28.7-64-64-64z"${_scopeId}></path></svg> ${ssrInterpolate(unref(t)("reviews"))}: ${ssrInterpolate(moduleData.value.rating_count)}</span>`);
            } else {
              _push2(`<!---->`);
            }
            _push2(`</div>`);
            if (moduleDescription.value) {
              _push2(`<div class="mt-4 text-sm subtitle" itemprop="description"${_scopeId}>${moduleDescription.value ?? ""}</div>`);
            } else {
              _push2(`<!---->`);
            }
            _push2(`<div class="my-1 flex items-center justify-center"${_scopeId}><div itemprop="interactionStatistic" itemscope itemtype="https://schema.org/InteractionCounter"${_scopeId}><link itemprop="interactionType" href="https://schema.org/LikeAction"${_scopeId}><meta itemprop="userInteractionCount"${ssrRenderAttr("content", moduleData.value.likes_count || 0)}${_scopeId}>`);
            _push2(ssrRenderComponent(LikeButtonEntity, {
              "likes-count": moduleData.value.likes_count || 0,
              "already-liked": moduleData.value.already_liked || false,
              "route-name": "public.schoolModules.like",
              "route-params": moduleData.value.id,
              title: unref(t)("like")
            }, null, _parent2, _scopeId));
            _push2(`</div></div>`);
            if ((_b = parentCourse.value) == null ? void 0 : _b.slug) {
              _push2(`<div class="mt-6 flex items-center justify-center gap-2 text-sm" itemprop="isPartOf" itemscope itemtype="https://schema.org/Course"${_scopeId}><link itemprop="url"${ssrRenderAttr("href", courseUrl.value)}${_scopeId}><span class="flex items-center justify-center gap-0.5 text-slate-500 dark:text-slate-400 uppercase"${_scopeId}><svg class="h-5 w-5 text-slate-600/85 dark:text-slate-200/85" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true"${_scopeId}><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25"${_scopeId}></path></svg> ${ssrInterpolate(unref(t)("course"))}: </span>`);
              _push2(ssrRenderComponent(unref(Link), {
                href: _ctx.route("public.schoolCourses.show", { slug: parentCourse.value.slug }),
                class: "font-semibold text-indigo-700 hover:underline dark:text-indigo-300",
                itemprop: "name"
              }, {
                default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                  if (_push3) {
                    _push3(`${ssrInterpolate(courseTitle.value)}`);
                  } else {
                    return [
                      createTextVNode(toDisplayString(courseTitle.value), 1)
                    ];
                  }
                }),
                _: 1
              }, _parent2, _scopeId));
              _push2(`</div>`);
            } else {
              _push2(`<!---->`);
            }
            _push2(ssrRenderComponent(_sfc_main$1, {
              lessons: lessonsList.value,
              cols: gridCols.value
            }, null, _parent2, _scopeId));
            _push2(`</article>`);
            _push2(ssrRenderComponent(_sfc_main$9, { videos: mainVideosList.value }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(SectionBanners, { banners: mainBannersList.value }, null, _parent2, _scopeId));
            _push2(`</div></section>`);
            if (showRight.value) {
              _push2(`<aside class="${ssrRenderClass([rightCollapsed.value ? "lg:w-6" : "lg:w-72", "shrink-0 pr-3 transition-all duration-300"])}"${_scopeId}>`);
              _push2(ssrRenderComponent(_sfc_main$a, {
                collapsed: rightCollapsed.value,
                onCollapsed: ($event) => rightCollapsed.value = $event
              }, null, _parent2, _scopeId));
              _push2(`</aside>`);
            } else {
              _push2(`<!---->`);
            }
            _push2(`</div></div></main>`);
            _push2(ssrRenderComponent(_sfc_main$b, null, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(Progress, null, null, _parent2, _scopeId));
          } else {
            return [
              createVNode(_sfc_main$7),
              createVNode("main", { class: "min-h-screen px-1 lg:px-6 max-w-full" }, [
                createVNode("div", { class: "mx-auto tracking-wider pt-20 lg:pt-44" }, [
                  createVNode("div", { class: "ext-color w-full min-w-0 py-3 px-1 flex flex-col lg:flex-row gap-4 rounded-3xl border-2 border-slate-300 dark:border-slate-500" }, [
                    showLeft.value ? (openBlock(), createBlock("aside", {
                      key: 0,
                      class: ["shrink-0 pl-3 transition-all duration-300", leftCollapsed.value ? "lg:w-6" : "lg:w-72"]
                    }, [
                      createVNode(_sfc_main$8, {
                        "track-tree": trackTree.value,
                        collapsed: leftCollapsed.value,
                        onCollapsed: ($event) => leftCollapsed.value = $event
                      }, null, 8, ["track-tree", "collapsed", "onCollapsed"])
                    ], 2)) : createCommentVNode("", true),
                    createVNode("section", { class: "w-full pb-6 slate-1 min-w-0" }, [
                      createVNode("div", { class: "mx-auto max-w-6xl" }, [
                        createVNode("article", {
                          class: "selection:bg-red-400 selection:text-white",
                          itemscope: "",
                          itemtype: "https://schema.org/LearningResource",
                          itemid: canonicalUrl.value
                        }, [
                          createVNode("meta", {
                            itemprop: "url",
                            content: canonicalUrl.value
                          }, null, 8, ["content"]),
                          createVNode("meta", {
                            itemprop: "inLanguage",
                            content: contentLocale.value
                          }, null, 8, ["content"]),
                          moduleData.value.published_at ? (openBlock(), createBlock("meta", {
                            key: 0,
                            itemprop: "datePublished",
                            content: moduleData.value.published_at
                          }, null, 8, ["content"])) : createCommentVNode("", true),
                          schemaDuration.value ? (openBlock(), createBlock("meta", {
                            key: 1,
                            itemprop: "timeRequired",
                            content: schemaDuration.value
                          }, null, 8, ["content"])) : createCommentVNode("", true),
                          createVNode("nav", {
                            class: "text-sm mb-3",
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
                              createVNode("li", { "aria-hidden": "true" }, [
                                createVNode("span", { class: "mx-2 breadcrumbs" }, "/")
                              ]),
                              createVNode("li", null, [
                                createVNode(unref(Link), {
                                  href: _ctx.route("public.schoolCourses.index"),
                                  class: "breadcrumb-link hover:underline"
                                }, {
                                  default: withCtx(() => [
                                    createTextVNode(toDisplayString(unref(t)("courses")), 1)
                                  ]),
                                  _: 1
                                }, 8, ["href"])
                              ]),
                              ((_c = parentCourse.value) == null ? void 0 : _c.slug) ? (openBlock(), createBlock(Fragment, { key: 0 }, [
                                createVNode("li", { "aria-hidden": "true" }, [
                                  createVNode("span", { class: "mx-2 breadcrumbs" }, "/")
                                ]),
                                createVNode("li", null, [
                                  createVNode(unref(Link), {
                                    href: _ctx.route("public.schoolCourses.show", { slug: parentCourse.value.slug }),
                                    class: "breadcrumb-link hover:underline"
                                  }, {
                                    default: withCtx(() => [
                                      createTextVNode(toDisplayString(courseTitle.value), 1)
                                    ]),
                                    _: 1
                                  }, 8, ["href"])
                                ])
                              ], 64)) : createCommentVNode("", true),
                              createVNode("li", { "aria-hidden": "true" }, [
                                createVNode("span", { class: "mx-2 breadcrumbs" }, "/")
                              ]),
                              createVNode("li", {
                                class: "breadcrumbs",
                                "aria-current": "page"
                              }, toDisplayString(moduleTitle.value), 1)
                            ])
                          ]),
                          createVNode("div", { class: "flex flex-wrap items-center justify-center gap-3 title my-3" }, [
                            createVNode("h1", {
                              class: "text-2xl font-bold",
                              itemprop: "name"
                            }, toDisplayString(moduleTitle.value), 1),
                            moduleData.value.views > 0 ? (openBlock(), createBlock("div", {
                              key: 0,
                              title: unref(t)("views"),
                              class: "flex items-center justify-center gap-1",
                              itemprop: "interactionStatistic",
                              itemscope: "",
                              itemtype: "https://schema.org/InteractionCounter"
                            }, [
                              createVNode("link", {
                                itemprop: "interactionType",
                                href: "https://schema.org/ViewAction"
                              }),
                              createVNode("meta", {
                                itemprop: "userInteractionCount",
                                content: moduleData.value.views
                              }, null, 8, ["content"]),
                              (openBlock(), createBlock("svg", {
                                class: "h-4 w-4 text-slate-600/85 dark:text-slate-200/85",
                                xmlns: "http://www.w3.org/2000/svg",
                                viewBox: "0 0 576 512",
                                fill: "currentColor",
                                "aria-hidden": "true"
                              }, [
                                createVNode("path", { d: "M569.354 231.631C512.97 135.949 407.81 72 288 72 168.14 72 63.004 135.994 6.646 231.631a47.999 47.999 0 0 0 0 48.739C63.031 376.051 168.19 440 288 440c119.86 0 224.996-63.994 281.354-159.631a47.997 47.997 0 0 0 0-48.738zM288 392c-102.556 0-192.091-54.701-240-136 44.157-74.933 123.677-127.27 216.162-135.007C273.958 131.078 280 144.83 280 160c0 30.928-25.072 56-56 56s-56-25.072-56-56l.001-.042C157.794 179.043 152 200.844 152 224c0 75.111 60.889 136 136 136s136-60.889 136-136c0-31.031-10.4-59.629-27.895-82.515C451.704 164.638 498.009 205.106 528 256c-47.908 81.299-137.444 136-240 136z" })
                              ])),
                              createVNode("span", { class: "text-center text-sm text-gray-500" }, toDisplayString(moduleData.value.views), 1)
                            ], 8, ["title"])) : createCommentVNode("", true)
                          ]),
                          moduleSubtitle.value ? (openBlock(), createBlock("div", {
                            key: 2,
                            class: "mt-1 mb-3 text-sm subtitle text-center"
                          }, toDisplayString(moduleSubtitle.value), 1)) : createCommentVNode("", true),
                          hasModuleImages.value ? (openBlock(), createBlock("div", {
                            key: 3,
                            class: "flex items-center justify-center"
                          }, [
                            createVNode("div", { class: "w-full" }, [
                              createVNode(ImageGalleryMain, {
                                images: moduleImages.value,
                                alt: moduleTitle.value,
                                itemprop: "image",
                                loading: "eager",
                                fetchpriority: "high",
                                "rounded-class": "rounded-lg",
                                "shadow-class": "shadow-lg shadow-gray-400 dark:shadow-gray-700",
                                "img-class": "w-full h-full object-cover"
                              }, null, 8, ["images", "alt"])
                            ])
                          ])) : createCommentVNode("", true),
                          createVNode("div", { class: "my-4 flex flex-wrap items-center justify-center gap-3 text-sm text-slate-600 dark:text-slate-300" }, [
                            lessonsCount.value ? (openBlock(), createBlock("span", {
                              key: 0,
                              class: "rounded-sm border border-gray-400 flex items-center justify-center gap-1 px-3 py-1"
                            }, [
                              (openBlock(), createBlock("svg", {
                                class: "h-4 w-4 text-sky-600/85 dark:text-sky-300/85",
                                fill: "currentColor",
                                viewBox: "0 0 24 24",
                                "aria-hidden": "true"
                              }, [
                                createVNode("path", {
                                  "stroke-linecap": "round",
                                  "stroke-linejoin": "round",
                                  d: "M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25"
                                })
                              ])),
                              createTextVNode(" " + toDisplayString(unref(t)("lessons")) + ": " + toDisplayString(lessonsCount.value), 1)
                            ])) : createCommentVNode("", true),
                            moduleData.value.duration ? (openBlock(), createBlock("span", {
                              key: 1,
                              class: "rounded-sm border border-gray-400 flex items-center justify-center gap-1 px-3 py-1"
                            }, [
                              (openBlock(), createBlock("svg", {
                                class: "w-3 h-3 text-blue-700 dark:text-blue-300",
                                viewBox: "0 0 24 24",
                                "aria-hidden": "true"
                              }, [
                                createVNode("path", {
                                  class: "fill-current",
                                  d: "M22,13a1,1,0,0,1,0-2h1.949A12.006,12.006,0,0,0,13,.051V2a1,1,0,0,1-2,0V.051A12.006,12.006,0,0,0,.051,11H2a1,1,0,0,1,0,2H.051A12.006,12.006,0,0,0,11,23.949V22a1,1,0,0,1,2,0v1.949A12.006,12.006,0,0,0,23.949,13Zm-6,0H12a1,1,0,0,1-.832-.445l-4-6a1,1,0,1,1,1.664-1.11L12.535,11H16a1,1,0,0,1,0,2Z"
                                })
                              ])),
                              createTextVNode(" " + toDisplayString(unref(t)("duration")) + ": " + toDisplayString(moduleData.value.duration) + " " + toDisplayString(unref(t)("minutes")), 1)
                            ])) : createCommentVNode("", true),
                            moduleData.value.rating_avg ? (openBlock(), createBlock("span", {
                              key: 2,
                              class: "rounded-sm border border-gray-400 flex items-center justify-center gap-1 px-3 py-1",
                              itemprop: "aggregateRating",
                              itemscope: "",
                              itemtype: "https://schema.org/AggregateRating"
                            }, [
                              createVNode("meta", {
                                itemprop: "ratingValue",
                                content: moduleData.value.rating_avg
                              }, null, 8, ["content"]),
                              createVNode("meta", {
                                itemprop: "ratingCount",
                                content: moduleData.value.rating_count || 0
                              }, null, 8, ["content"]),
                              createVNode("meta", {
                                itemprop: "bestRating",
                                content: "5"
                              }),
                              createVNode("meta", {
                                itemprop: "worstRating",
                                content: "1"
                              }),
                              (openBlock(), createBlock("svg", {
                                viewBox: "0 0 24 24",
                                class: "h-3 w-3 text-red-400 dark:text-red-300",
                                "aria-hidden": "true"
                              }, [
                                createVNode("path", {
                                  class: "fill-current",
                                  d: "M12.746,1.464l3.11,6.3L22.81,8.776a.831.831,0,0,1,.461,1.418l-5.033,4.9,1.188,6.926a.832.832,0,0,1-1.207.877L12,19.632,5.78,22.9a.833.833,0,0,1-1.207-.878L5.761,15.1l-5.033-4.9a.831.831,0,0,1,.461-1.418L8.143,7.765l3.11-6.3A.833.833,0,0,1,12.746,1.464Z"
                                })
                              ])),
                              createTextVNode(" " + toDisplayString(unref(t)("rating")) + ": " + toDisplayString(Number(moduleData.value.rating_avg).toFixed(1)), 1)
                            ])) : createCommentVNode("", true),
                            moduleData.value.rating_count ? (openBlock(), createBlock("span", {
                              key: 3,
                              class: "rounded-sm border border-gray-400 flex items-center justify-center gap-1 px-3 py-1"
                            }, [
                              (openBlock(), createBlock("svg", {
                                class: "w-3 h-3 text-teal-600/85 dark:text-teal-300/85",
                                fill: "currentColor",
                                viewBox: "0 0 512 512",
                                "aria-hidden": "true"
                              }, [
                                createVNode("path", { d: "M448 0H64C28.7 0 0 28.7 0 64v288c0 35.3 28.7 64 64 64h96v84c0 9.8 11.2 15.5 19.1 9.7L304 416h144c35.3 0 64-28.7 64-64V64c0-35.3-28.7-64-64-64z" })
                              ])),
                              createTextVNode(" " + toDisplayString(unref(t)("reviews")) + ": " + toDisplayString(moduleData.value.rating_count), 1)
                            ])) : createCommentVNode("", true)
                          ]),
                          moduleDescription.value ? (openBlock(), createBlock("div", {
                            key: 4,
                            class: "mt-4 text-sm subtitle",
                            itemprop: "description",
                            innerHTML: moduleDescription.value
                          }, null, 8, ["innerHTML"])) : createCommentVNode("", true),
                          createVNode("div", { class: "my-1 flex items-center justify-center" }, [
                            createVNode("div", {
                              itemprop: "interactionStatistic",
                              itemscope: "",
                              itemtype: "https://schema.org/InteractionCounter"
                            }, [
                              createVNode("link", {
                                itemprop: "interactionType",
                                href: "https://schema.org/LikeAction"
                              }),
                              createVNode("meta", {
                                itemprop: "userInteractionCount",
                                content: moduleData.value.likes_count || 0
                              }, null, 8, ["content"]),
                              createVNode(LikeButtonEntity, {
                                "likes-count": moduleData.value.likes_count || 0,
                                "already-liked": moduleData.value.already_liked || false,
                                "route-name": "public.schoolModules.like",
                                "route-params": moduleData.value.id,
                                title: unref(t)("like")
                              }, null, 8, ["likes-count", "already-liked", "route-params", "title"])
                            ])
                          ]),
                          ((_d = parentCourse.value) == null ? void 0 : _d.slug) ? (openBlock(), createBlock("div", {
                            key: 5,
                            class: "mt-6 flex items-center justify-center gap-2 text-sm",
                            itemprop: "isPartOf",
                            itemscope: "",
                            itemtype: "https://schema.org/Course"
                          }, [
                            createVNode("link", {
                              itemprop: "url",
                              href: courseUrl.value
                            }, null, 8, ["href"]),
                            createVNode("span", { class: "flex items-center justify-center gap-0.5 text-slate-500 dark:text-slate-400 uppercase" }, [
                              (openBlock(), createBlock("svg", {
                                class: "h-5 w-5 text-slate-600/85 dark:text-slate-200/85",
                                fill: "currentColor",
                                viewBox: "0 0 24 24",
                                "aria-hidden": "true"
                              }, [
                                createVNode("path", {
                                  "stroke-linecap": "round",
                                  "stroke-linejoin": "round",
                                  d: "M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25"
                                })
                              ])),
                              createTextVNode(" " + toDisplayString(unref(t)("course")) + ": ", 1)
                            ]),
                            createVNode(unref(Link), {
                              href: _ctx.route("public.schoolCourses.show", { slug: parentCourse.value.slug }),
                              class: "font-semibold text-indigo-700 hover:underline dark:text-indigo-300",
                              itemprop: "name"
                            }, {
                              default: withCtx(() => [
                                createTextVNode(toDisplayString(courseTitle.value), 1)
                              ]),
                              _: 1
                            }, 8, ["href"])
                          ])) : createCommentVNode("", true),
                          createVNode(_sfc_main$1, {
                            lessons: lessonsList.value,
                            cols: gridCols.value
                          }, null, 8, ["lessons", "cols"])
                        ], 8, ["itemid"]),
                        createVNode(_sfc_main$9, { videos: mainVideosList.value }, null, 8, ["videos"]),
                        createVNode(SectionBanners, { banners: mainBannersList.value }, null, 8, ["banners"])
                      ])
                    ]),
                    showRight.value ? (openBlock(), createBlock("aside", {
                      key: 1,
                      class: ["shrink-0 pr-3 transition-all duration-300", rightCollapsed.value ? "lg:w-6" : "lg:w-72"]
                    }, [
                      createVNode(_sfc_main$a, {
                        collapsed: rightCollapsed.value,
                        onCollapsed: ($event) => rightCollapsed.value = $event
                      }, null, 8, ["collapsed", "onCollapsed"])
                    ], 2)) : createCommentVNode("", true)
                  ])
                ])
              ]),
              createVNode(_sfc_main$b),
              createVNode(Progress)
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
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Pages/Public/Default/School/SchoolModules/Show.vue");
  return _sfc_setup ? _sfc_setup(props, ctx) : void 0;
};
export {
  _sfc_main as default
};
