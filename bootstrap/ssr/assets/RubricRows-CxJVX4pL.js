import { computed, mergeProps, unref, withCtx, createVNode, createBlock, createCommentVNode, openBlock, toDisplayString, useSSRContext } from "vue";
import { ssrRenderAttrs, ssrRenderAttr, ssrRenderList, ssrRenderComponent, ssrInterpolate } from "vue/server-renderer";
import { Link } from "@inertiajs/vue3";
import { useI18n } from "vue-i18n";
import { U as UniversalImageSlider } from "./UniversalImageSlider-Cu2Xndcn.js";
const _sfc_main$1 = {
  __name: "RubricGrid",
  __ssrInlineRender: true,
  props: {
    rubrics: { type: Array, default: () => [] },
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
    const getTranslation = (rubric) => {
      return (rubric == null ? void 0 : rubric.translation) || {};
    };
    const getTitle = (rubric) => {
      return getTranslation(rubric).title || "";
    };
    const getShort = (rubric) => {
      return getTranslation(rubric).short || "";
    };
    const getShowRoute = (rubric) => {
      return route("public.blogRubrics.show", { url: rubric.url });
    };
    const hasSvgIcon = (rubric) => {
      if (!(rubric == null ? void 0 : rubric.icon)) {
        return false;
      }
      return /^\s*<svg[\s\S]*<\/svg>\s*$/i.test(String(rubric.icon));
    };
    return (_ctx, _push, _parent, _attrs) => {
      _push(`<div${ssrRenderAttrs(mergeProps({
        class: gridClass.value,
        itemscope: "",
        itemtype: "https://schema.org/ItemList"
      }, _attrs))}><meta itemprop="numberOfItems"${ssrRenderAttr("content", __props.rubrics.length)}><!--[-->`);
      ssrRenderList(__props.rubrics, (rubric, index) => {
        var _a, _b;
        _push(`<div itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem" class="group flex h-full flex-col overflow-hidden rounded-md border border-gray-200 bg-white shadow-sm transition hover:-translate-y-0.5 hover:shadow-md dark:border-gray-700 dark:bg-gray-900"><meta itemprop="position"${ssrRenderAttr("content", index + 1)}><article itemprop="item" itemscope itemtype="https://schema.org/CollectionPage"${ssrRenderAttr("itemid", getShowRoute(rubric))} class="contents"><meta itemprop="name"${ssrRenderAttr("content", getTitle(rubric))}>`);
        if (getShort(rubric)) {
          _push(`<meta itemprop="description"${ssrRenderAttr("content", getShort(rubric))}>`);
        } else {
          _push(`<!---->`);
        }
        _push(`<meta itemprop="url"${ssrRenderAttr("content", getShowRoute(rubric))}><div itemprop="interactionStatistic" itemscope itemtype="https://schema.org/InteractionCounter" class="contents"><link itemprop="interactionType" href="https://schema.org/ViewAction"><meta itemprop="userInteractionCount"${ssrRenderAttr("content", rubric.views || 0)}></div>`);
        _push(ssrRenderComponent(unref(Link), {
          href: getShowRoute(rubric)
        }, {
          default: withCtx((_, _push2, _parent2, _scopeId) => {
            if (_push2) {
              _push2(ssrRenderComponent(UniversalImageSlider, {
                entity: rubric,
                "height-class": "h-48",
                "rounded-class": "",
                "wrapper-class": "",
                "img-class": "w-full h-full object-cover transition\n                               duration-300 group-hover:scale-105"
              }, null, _parent2, _scopeId));
            } else {
              return [
                createVNode(UniversalImageSlider, {
                  entity: rubric,
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
          href: getShowRoute(rubric),
          title: `${unref(t)("articles")}: ${rubric.articles_count || 0}`,
          class: "inline-flex items-center gap-1"
        }, {
          default: withCtx((_, _push2, _parent2, _scopeId) => {
            if (_push2) {
              if (hasSvgIcon(rubric)) {
                _push2(`<span class="flex"${_scopeId}>${rubric.icon ?? ""}</span>`);
              } else {
                _push2(`<!---->`);
              }
              _push2(`<span itemprop="headline" class="text-lg font-semibold text-slate-900/85 group-hover:opacity-75 dark:text-slate-100/85 dark:group-hover:opacity-75"${_scopeId}>${ssrInterpolate(getTitle(rubric))}</span>`);
            } else {
              return [
                hasSvgIcon(rubric) ? (openBlock(), createBlock("span", {
                  key: 0,
                  class: "flex",
                  innerHTML: rubric.icon
                }, null, 8, ["innerHTML"])) : createCommentVNode("", true),
                createVNode("span", {
                  itemprop: "headline",
                  class: "text-lg font-semibold text-slate-900/85 group-hover:opacity-75 dark:text-slate-100/85 dark:group-hover:opacity-75"
                }, toDisplayString(getTitle(rubric)), 1)
              ];
            }
          }),
          _: 2
        }, _parent));
        if ((rubric == null ? void 0 : rubric.views) > 0) {
          _push(`<div class="inline-flex items-center gap-2"><svg class="h-4 w-4 text-slate-600/85 dark:text-slate-200/85" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512" fill="currentColor"><path d="M569.354 231.631C512.97 135.949 407.81 72 288 72 168.14 72 63.004 135.994 6.646 231.631a47.999 47.999 0 0 0 0 48.739C63.031 376.051 168.19 440 288 440c119.86 0 224.996-63.994 281.354-159.631a47.997 47.997 0 0 0 0-48.738zM288 392c-102.556 0-192.091-54.701-240-136 44.157-74.933 123.677-127.27 216.162-135.007C273.958 131.078 280 144.83 280 160c0 30.928-25.072 56-56 56s-56-25.072-56-56l.001-.042C157.794 179.043 152 200.844 152 224c0 75.111 60.889 136 136 136s136-60.889 136-136c0-31.031-10.4-59.629-27.895-82.515C451.704 164.638 498.009 205.106 528 256c-47.908 81.299-137.444 136-240 136z"></path></svg><span class="text-xs font-semibold text-slate-500 dark:text-slate-400">${ssrInterpolate(rubric.views)}</span></div>`);
        } else {
          _push(`<!---->`);
        }
        _push(`</div>`);
        if (getShort(rubric)) {
          _push(`<div itemprop="abstract" class="mt-3 line-clamp-3 text-sm text-slate-700 dark:text-slate-300">${ssrInterpolate(getShort(rubric))}</div>`);
        } else {
          _push(`<!---->`);
        }
        if (rubric == null ? void 0 : rubric.owner) {
          _push(`<div class="mt-4 flex items-center justify-center gap-2">`);
          if ((_a = rubric.owner) == null ? void 0 : _a.profile_photo_url) {
            _push(`<img${ssrRenderAttr("src", rubric.owner.profile_photo_url)}${ssrRenderAttr("alt", rubric.owner.name)} loading="lazy" class="h-6 w-6 rounded-full object-cover ring-1 ring-gray-200 dark:ring-gray-700">`);
          } else {
            _push(`<!---->`);
          }
          _push(`<div class="min-w-0 text-xs font-semibold text-slate-700/85 dark:text-slate-300/85">${ssrInterpolate((_b = rubric.owner) == null ? void 0 : _b.name)}</div></div>`);
        } else {
          _push(`<!---->`);
        }
        _push(`<div class="mt-auto pt-4">`);
        _push(ssrRenderComponent(unref(Link), {
          href: getShowRoute(rubric),
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
        _push(`</div></div></article></div>`);
      });
      _push(`<!--]--></div>`);
    };
  }
};
const _sfc_setup$1 = _sfc_main$1.setup;
_sfc_main$1.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Components/Public/Default/Blog/BlogRubric/RubricGrid.vue");
  return _sfc_setup$1 ? _sfc_setup$1(props, ctx) : void 0;
};
const _sfc_main = {
  __name: "RubricRows",
  __ssrInlineRender: true,
  props: {
    rubrics: { type: Array, default: () => [] }
  },
  setup(__props) {
    const { t } = useI18n();
    const getTranslation = (rubric) => {
      return (rubric == null ? void 0 : rubric.translation) || {};
    };
    const getTitle = (rubric) => {
      return getTranslation(rubric).title || "";
    };
    const getShort = (rubric) => {
      return getTranslation(rubric).short || "";
    };
    const getShowRoute = (rubric) => {
      return route("public.blogRubrics.show", { url: rubric.url });
    };
    const hasSvgIcon = (rubric) => {
      if (!(rubric == null ? void 0 : rubric.icon)) {
        return false;
      }
      return /^\s*<svg[\s\S]*<\/svg>\s*$/i.test(String(rubric.icon));
    };
    return (_ctx, _push, _parent, _attrs) => {
      _push(`<div${ssrRenderAttrs(mergeProps({
        class: "space-y-3",
        itemscope: "",
        itemtype: "https://schema.org/ItemList"
      }, _attrs))}><meta itemprop="numberOfItems"${ssrRenderAttr("content", __props.rubrics.length)}><!--[-->`);
      ssrRenderList(__props.rubrics, (rubric, index) => {
        var _a, _b;
        _push(`<div itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem" class="group flex flex-col sm:flex-row gap-3 rounded-md border border-gray-200 bg-white shadow-sm p-3 transition hover:shadow-md dark:border-gray-700 dark:bg-gray-900"><meta itemprop="position"${ssrRenderAttr("content", index + 1)}><article itemprop="item" itemscope itemtype="https://schema.org/CollectionPage"${ssrRenderAttr("itemid", getShowRoute(rubric))} class="contents"><meta itemprop="name"${ssrRenderAttr("content", getTitle(rubric))}>`);
        if (getShort(rubric)) {
          _push(`<meta itemprop="description"${ssrRenderAttr("content", getShort(rubric))}>`);
        } else {
          _push(`<!---->`);
        }
        _push(`<meta itemprop="url"${ssrRenderAttr("content", getShowRoute(rubric))}><div itemprop="interactionStatistic" itemscope itemtype="https://schema.org/InteractionCounter" class="contents"><link itemprop="interactionType" href="https://schema.org/ViewAction"><meta itemprop="userInteractionCount"${ssrRenderAttr("content", rubric.views || 0)}></div>`);
        _push(ssrRenderComponent(unref(Link), {
          href: getShowRoute(rubric),
          class: "shrink-0"
        }, {
          default: withCtx((_, _push2, _parent2, _scopeId) => {
            if (_push2) {
              _push2(ssrRenderComponent(UniversalImageSlider, {
                entity: rubric,
                "height-class": "h-44",
                "rounded-class": "rounded-md",
                "wrapper-class": "w-full sm:w-60 border border-gray-400 dark:border-gray-600",
                "img-class": "w-full h-full object-cover transition\n                               duration-300 group-hover:scale-105"
              }, null, _parent2, _scopeId));
            } else {
              return [
                createVNode(UniversalImageSlider, {
                  entity: rubric,
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
        _push(`<div class="min-w-0 flex-1 flex flex-col justify-around"><div class="flex items-start justify-between gap-3">`);
        _push(ssrRenderComponent(unref(Link), {
          href: getShowRoute(rubric),
          class: "min-w-0 inline-flex items-center gap-2",
          title: `${unref(t)("articles")}: ${rubric.articles_count || 0}`
        }, {
          default: withCtx((_, _push2, _parent2, _scopeId) => {
            if (_push2) {
              if (hasSvgIcon(rubric)) {
                _push2(`<span class="flex shrink-0"${_scopeId}>${rubric.icon ?? ""}</span>`);
              } else {
                _push2(`<!---->`);
              }
              _push2(`<span itemprop="headline" class="truncate text-lg font-semibold text-slate-900/85 dark:text-slate-100/85 group-hover:opacity-75"${_scopeId}>${ssrInterpolate(getTitle(rubric))}</span>`);
            } else {
              return [
                hasSvgIcon(rubric) ? (openBlock(), createBlock("span", {
                  key: 0,
                  class: "flex shrink-0",
                  innerHTML: rubric.icon
                }, null, 8, ["innerHTML"])) : createCommentVNode("", true),
                createVNode("span", {
                  itemprop: "headline",
                  class: "truncate text-lg font-semibold text-slate-900/85 dark:text-slate-100/85 group-hover:opacity-75"
                }, toDisplayString(getTitle(rubric)), 1)
              ];
            }
          }),
          _: 2
        }, _parent));
        _push(`<div class="flex items-center gap-3 shrink-0">`);
        if ((rubric == null ? void 0 : rubric.views) > 0) {
          _push(`<div class="inline-flex items-center gap-1"><svg class="h-4 w-4 text-slate-600/85 dark:text-slate-200/85" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512" fill="currentColor"><path d="M569.354 231.631C512.97 135.949 407.81 72 288 72 168.14 72 63.004 135.994 6.646 231.631a47.999 47.999 0 0 0 0 48.739C63.031 376.051 168.19 440 288 440c119.86 0 224.996-63.994 281.354-159.631a47.997 47.997 0 0 0 0-48.738zM288 392c-102.556 0-192.091-54.701-240-136 44.157-74.933 123.677-127.27 216.162-135.007C273.958 131.078 280 144.83 280 160c0 30.928-25.072 56-56 56s-56-25.072-56-56l.001-.042C157.794 179.043 152 200.844 152 224c0 75.111 60.889 136 136 136s136-60.889 136-136c0-31.031-10.4-59.629-27.895-82.515C451.704 164.638 498.009 205.106 528 256c-47.908 81.299-137.444 136-240 136z"></path></svg><span class="text-xs font-semibold text-slate-500 dark:text-slate-400">${ssrInterpolate(rubric.views)}</span></div>`);
        } else {
          _push(`<!---->`);
        }
        _push(`</div></div>`);
        if (getShort(rubric)) {
          _push(`<div itemprop="abstract" class="mt-2 line-clamp-2 text-sm text-slate-700 dark:text-slate-300">${ssrInterpolate(getShort(rubric))}</div>`);
        } else {
          _push(`<!---->`);
        }
        _push(`<div class="mt-2 flex items-center justify-between">`);
        if (rubric == null ? void 0 : rubric.owner) {
          _push(`<div class="flex items-center gap-2">`);
          if ((_a = rubric.owner) == null ? void 0 : _a.profile_photo_url) {
            _push(`<img${ssrRenderAttr("src", rubric.owner.profile_photo_url)}${ssrRenderAttr("alt", rubric.owner.name)} loading="lazy" class="h-6 w-6 rounded-full object-cover ring-1 ring-gray-200 dark:ring-gray-700">`);
          } else {
            _push(`<!---->`);
          }
          _push(`<div class="text-xs font-semibold text-slate-700/85 dark:text-slate-300/85 truncate">${ssrInterpolate((_b = rubric.owner) == null ? void 0 : _b.name)}</div></div>`);
        } else {
          _push(`<!---->`);
        }
        _push(`<div class="mt-auto">`);
        _push(ssrRenderComponent(unref(Link), {
          href: getShowRoute(rubric),
          class: "flex w-full items-center justify-center gap-2 rounded-sm px-3 py-1 btn-default"
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
        _push(`</div></div></div></article></div>`);
      });
      _push(`<!--]--></div>`);
    };
  }
};
const _sfc_setup = _sfc_main.setup;
_sfc_main.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Components/Public/Default/Blog/BlogRubric/RubricRows.vue");
  return _sfc_setup ? _sfc_setup(props, ctx) : void 0;
};
export {
  _sfc_main$1 as _,
  _sfc_main as a
};
