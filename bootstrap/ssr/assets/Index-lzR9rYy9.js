import { computed, mergeProps, unref, withCtx, createVNode, toDisplayString, createBlock, openBlock, useSSRContext, ref, watch, createCommentVNode } from "vue";
import { ssrRenderAttrs, ssrRenderAttr, ssrRenderList, ssrRenderComponent, ssrRenderClass, ssrInterpolate } from "vue/server-renderer";
import { Link, usePage, Head, router } from "@inertiajs/vue3";
import { useI18n } from "vue-i18n";
import { u as useSmoothScrollTo } from "./useSmoothScrollTo-g9G3kyDv.js";
import { _ as _sfc_main$3 } from "./DefaultLayout-6zxGxPtK.js";
import { _ as _sfc_main$4, a as _sfc_main$b, P as Progress } from "./Progress-CyfOKcqP.js";
import { _ as _sfc_main$5, a as _sfc_main$a } from "./RightSidebarMarket-DpLr4Pjm.js";
import { _ as _sfc_main$6 } from "./EntityPageToolbar-DT32FtSd.js";
import { _ as _sfc_main$7 } from "./FrontendEntityPageToolbar-Xr_r9znL.js";
import { U as UniversalImageSlider } from "./UniversalImageSlider-Cu2Xndcn.js";
import { _ as _sfc_main$8, a as _sfc_main$9, b as _sfc_main$c } from "./PublicAdminBottomPanel-BhLEQMoJ.js";
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
  __name: "MarketBrandGrid",
  __ssrInlineRender: true,
  props: {
    brands: {
      type: Array,
      default: () => []
    },
    cols: {
      type: Number,
      default: 2
    },
    /** Начальная позиция списка Schema.org */
    startPosition: {
      type: Number,
      default: 0
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
    const brandLink = (brand) => {
      return (brand == null ? void 0 : brand.url) ? route(
        "public.marketBrands.show",
        {
          url: brand.url
        }
      ) : "#";
    };
    const brandAbsoluteUrl = (brand) => {
      return (brand == null ? void 0 : brand.url) ? route(
        "public.marketBrands.show",
        {
          url: brand.url
        }
      ) : "";
    };
    const getBrandTitle = (brand) => {
      var _a;
      return ((_a = brand == null ? void 0 : brand.translation) == null ? void 0 : _a.title) || "";
    };
    const getBrandShort = (brand) => {
      var _a;
      return ((_a = brand == null ? void 0 : brand.translation) == null ? void 0 : _a.short) || "";
    };
    const getBrandImages = (brand) => {
      var _a;
      if (Array.isArray(brand == null ? void 0 : brand.images)) {
        return brand.images;
      }
      if (Array.isArray((_a = brand == null ? void 0 : brand.images) == null ? void 0 : _a.data)) {
        return brand.images.data;
      }
      return [];
    };
    const hasBrandImages = (brand) => {
      return getBrandImages(brand).length > 0;
    };
    const getBrandImage = (brand) => {
      const image = getBrandImages(brand)[0];
      return (image == null ? void 0 : image.webp_url) || (image == null ? void 0 : image.image_url) || (image == null ? void 0 : image.thumb_url) || (image == null ? void 0 : image.url) || "";
    };
    const getBrandLogo = (brand) => {
      const logo = brand == null ? void 0 : brand.logo;
      if (!logo) {
        return "";
      }
      const value = String(logo).trim();
      if (!value) {
        return "";
      }
      if (value.startsWith("http://") || value.startsWith("https://") || value.startsWith("/storage/")) {
        return value;
      }
      return `/storage/${value}`;
    };
    const getProductsCount = (brand) => {
      const value = Number(
        (brand == null ? void 0 : brand.products_count) ?? 0
      );
      return Number.isFinite(value) ? value : 0;
    };
    const getViews = (brand) => {
      const value = Number(
        (brand == null ? void 0 : brand.views) ?? 0
      );
      return Number.isFinite(value) ? value : 0;
    };
    return (_ctx, _push, _parent, _attrs) => {
      _push(`<div${ssrRenderAttrs(mergeProps({
        class: ["grid gap-4", gridClass.value],
        itemscope: "",
        itemtype: "https://schema.org/ItemList"
      }, _attrs))}><meta itemprop="numberOfItems"${ssrRenderAttr("content", String(__props.brands.length))}><!--[-->`);
      ssrRenderList(__props.brands, (brand, index) => {
        _push(`<article class="group flex h-full flex-col overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm transition hover:-translate-y-0.5 hover:shadow-md dark:border-gray-700 dark:bg-gray-900" itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem"><meta itemprop="position"${ssrRenderAttr("content", String(
          __props.startPosition + index + 1
        ))}><div class="flex h-full flex-col" itemprop="item" itemscope itemtype="https://schema.org/Brand">`);
        if (brandAbsoluteUrl(brand)) {
          _push(`<meta itemprop="url"${ssrRenderAttr("content", brandAbsoluteUrl(brand))}>`);
        } else {
          _push(`<!---->`);
        }
        if (getBrandTitle(brand)) {
          _push(`<meta itemprop="name"${ssrRenderAttr("content", getBrandTitle(brand))}>`);
        } else {
          _push(`<!---->`);
        }
        if (getBrandShort(brand)) {
          _push(`<meta itemprop="description"${ssrRenderAttr("content", getBrandShort(brand))}>`);
        } else {
          _push(`<!---->`);
        }
        if (getBrandImage(brand)) {
          _push(`<meta itemprop="image"${ssrRenderAttr("content", getBrandImage(brand))}>`);
        } else {
          _push(`<!---->`);
        }
        if (getBrandLogo(brand)) {
          _push(`<meta itemprop="logo"${ssrRenderAttr("content", getBrandLogo(brand))}>`);
        } else {
          _push(`<!---->`);
        }
        if (hasBrandImages(brand)) {
          _push(`<div class="relative">`);
          _push(ssrRenderComponent(unref(Link), {
            href: brandLink(brand),
            title: getBrandTitle(brand)
          }, {
            default: withCtx((_, _push2, _parent2, _scopeId) => {
              if (_push2) {
                _push2(ssrRenderComponent(UniversalImageSlider, {
                  entity: brand,
                  "height-class": "h-48",
                  "rounded-class": "",
                  "wrapper-class": "",
                  "img-class": "w-full h-full object-cover transition\n                                       duration-300 group-hover:scale-105"
                }, null, _parent2, _scopeId));
              } else {
                return [
                  createVNode(UniversalImageSlider, {
                    entity: brand,
                    "height-class": "h-48",
                    "rounded-class": "",
                    "wrapper-class": "",
                    "img-class": "w-full h-full object-cover transition\n                                       duration-300 group-hover:scale-105"
                  }, null, 8, ["entity"])
                ];
              }
            }),
            _: 2
          }, _parent));
          _push(`</div>`);
        } else {
          _push(`<!---->`);
        }
        _push(`<div class="${ssrRenderClass([hasBrandImages(brand) ? "pt-3" : "pt-4", "flex flex-1 flex-col items-center px-4 pb-4 text-center"])}">`);
        if (getBrandLogo(brand)) {
          _push(ssrRenderComponent(unref(Link), {
            href: brandLink(brand),
            title: getBrandTitle(brand),
            class: ["mb-2 flex h-16 w-auto items-center justify-center overflow-hidden rounded-md border border-gray-200 bg-white p-1.5 shadow-sm dark:border-gray-600 dark:bg-gray-800", hasBrandImages(brand) ? "mt-1" : ""]
          }, {
            default: withCtx((_, _push2, _parent2, _scopeId) => {
              if (_push2) {
                _push2(`<img${ssrRenderAttr("src", getBrandLogo(brand))}${ssrRenderAttr("alt", getBrandTitle(brand))}${ssrRenderAttr("title", getBrandTitle(brand))} class="h-full w-full object-contain" loading="lazy"${_scopeId}>`);
              } else {
                return [
                  createVNode("img", {
                    src: getBrandLogo(brand),
                    alt: getBrandTitle(brand),
                    title: getBrandTitle(brand),
                    class: "h-full w-full object-contain",
                    loading: "lazy"
                  }, null, 8, ["src", "alt", "title"])
                ];
              }
            }),
            _: 2
          }, _parent));
        } else {
          _push(`<!---->`);
        }
        if (getBrandTitle(brand)) {
          _push(ssrRenderComponent(unref(Link), {
            href: brandLink(brand),
            title: getBrandShort(brand),
            class: "inline-flex items-center justify-center"
          }, {
            default: withCtx((_, _push2, _parent2, _scopeId) => {
              if (_push2) {
                _push2(`<span class="text-base font-semibold text-slate-900/85 group-hover:opacity-75 dark:text-slate-100/85"${_scopeId}>${ssrInterpolate(getBrandTitle(brand))}</span>`);
              } else {
                return [
                  createVNode("span", { class: "text-base font-semibold text-slate-900/85 group-hover:opacity-75 dark:text-slate-100/85" }, toDisplayString(getBrandTitle(brand)), 1)
                ];
              }
            }),
            _: 2
          }, _parent));
        } else {
          _push(`<!---->`);
        }
        if (getBrandShort(brand)) {
          _push(`<div class="mt-2 line-clamp-3 text-sm text-slate-600 dark:text-slate-300">${ssrInterpolate(getBrandShort(brand))}</div>`);
        } else {
          _push(`<!---->`);
        }
        _push(`<div class="mt-auto flex flex-wrap items-center justify-center gap-2 pt-4 text-xs font-semibold text-slate-500 dark:text-slate-400"><div class="flex items-center justify-center gap-1 rounded-sm border border-slate-400 px-2 py-1"${ssrRenderAttr("title", unref(t)("products"))}><svg class="h-3 w-3 shrink-0 text-violet-600 dark:text-violet-300" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M4 4h6v6H4V4Zm10 0h6v6h-6V4ZM4 14h6v6H4v-6Zm10 0h6v6h-6v-6Z"></path></svg> ${ssrInterpolate(getProductsCount(brand))}</div>`);
        if (getViews(brand) > 0) {
          _push(`<div class="flex items-center justify-center gap-1 rounded-sm border border-slate-400 px-2 py-1"${ssrRenderAttr("title", unref(t)("views"))}><svg class="h-3 w-3 text-blue-600/85 dark:text-blue-200/85" viewBox="0 0 576 512" fill="currentColor" aria-hidden="true"><path d="M569.354 231.631C512.97 135.949 407.81 72 288 72 168.14 72 63.004 135.994 6.646 231.631a47.999 47.999 0 0 0 0 48.739C63.031 376.051 168.19 440 288 440c119.86 0 224.996-63.994 281.354-159.631a47.997 47.997 0 0 0 0-48.738zM288 392c-102.556 0-192.091-54.701-240-136 44.157-74.933 123.677-127.27 216.162-135.007C273.958 131.078 280 144.83 280 160c0 30.928-25.072 56-56 56s-56-25.072-56-56l.001-.042C157.794 179.043 152 200.844 152 224c0 75.111 60.889 136 136 136s136-60.889 136-136c0-31.031-10.4-59.629-27.895-82.515C451.704 164.638 498.009 205.106 528 256c-47.908 81.299-137.444 136-240 136z"></path></svg> ${ssrInterpolate(getViews(brand))}</div>`);
        } else {
          _push(`<!---->`);
        }
        _push(`</div>`);
        _push(ssrRenderComponent(unref(Link), {
          href: brandLink(brand),
          title: getBrandTitle(brand),
          class: "mt-3 flex w-fit items-center justify-center gap-2 rounded-sm px-3 py-1 btn-default"
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
const _sfc_setup$2 = _sfc_main$2.setup;
_sfc_main$2.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Components/Public/Default/Market/MarketBrand/MarketBrandGrid.vue");
  return _sfc_setup$2 ? _sfc_setup$2(props, ctx) : void 0;
};
const _sfc_main$1 = {
  __name: "MarketBrandRows",
  __ssrInlineRender: true,
  props: {
    brands: {
      type: Array,
      default: () => []
    },
    /** Начальная позиция списка Schema.org */
    startPosition: {
      type: Number,
      default: 0
    }
  },
  setup(__props) {
    const { t } = useI18n();
    const brandLink = (brand) => {
      return (brand == null ? void 0 : brand.url) ? route(
        "public.marketBrands.show",
        {
          url: brand.url
        }
      ) : "#";
    };
    const brandAbsoluteUrl = (brand) => {
      return (brand == null ? void 0 : brand.url) ? route(
        "public.marketBrands.show",
        {
          url: brand.url
        }
      ) : "";
    };
    const getBrandTitle = (brand) => {
      var _a;
      return ((_a = brand == null ? void 0 : brand.translation) == null ? void 0 : _a.title) || "";
    };
    const getBrandShort = (brand) => {
      var _a;
      return ((_a = brand == null ? void 0 : brand.translation) == null ? void 0 : _a.short) || "";
    };
    const getBrandImages = (brand) => {
      var _a;
      if (Array.isArray(brand == null ? void 0 : brand.images)) {
        return brand.images;
      }
      if (Array.isArray((_a = brand == null ? void 0 : brand.images) == null ? void 0 : _a.data)) {
        return brand.images.data;
      }
      return [];
    };
    const hasBrandImages = (brand) => {
      return getBrandImages(brand).length > 0;
    };
    const getBrandImage = (brand) => {
      const image = getBrandImages(brand)[0];
      return (image == null ? void 0 : image.webp_url) || (image == null ? void 0 : image.image_url) || (image == null ? void 0 : image.thumb_url) || (image == null ? void 0 : image.url) || "";
    };
    const getBrandLogo = (brand) => {
      const logo = brand == null ? void 0 : brand.logo;
      if (!logo) {
        return "";
      }
      const value = String(logo).trim();
      if (!value) {
        return "";
      }
      if (value.startsWith("http://") || value.startsWith("https://") || value.startsWith("/storage/")) {
        return value;
      }
      return `/storage/${value}`;
    };
    const getProductsCount = (brand) => {
      const value = Number(
        (brand == null ? void 0 : brand.products_count) ?? 0
      );
      return Number.isFinite(value) ? value : 0;
    };
    const getViews = (brand) => {
      const value = Number(
        (brand == null ? void 0 : brand.views) ?? 0
      );
      return Number.isFinite(value) ? value : 0;
    };
    return (_ctx, _push, _parent, _attrs) => {
      _push(`<div${ssrRenderAttrs(mergeProps({
        class: "space-y-4",
        itemscope: "",
        itemtype: "https://schema.org/ItemList"
      }, _attrs))}><meta itemprop="numberOfItems"${ssrRenderAttr("content", String(__props.brands.length))}><!--[-->`);
      ssrRenderList(__props.brands, (brand, index) => {
        _push(`<article class="group rounded-2xl border border-gray-200 bg-white shadow-sm transition hover:shadow-md dark:border-gray-700 dark:bg-gray-900" itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem"><meta itemprop="position"${ssrRenderAttr("content", String(
          __props.startPosition + index + 1
        ))}><div class="flex flex-col gap-4 p-3 sm:flex-row" itemprop="item" itemscope itemtype="https://schema.org/Brand">`);
        if (brandAbsoluteUrl(brand)) {
          _push(`<meta itemprop="url"${ssrRenderAttr("content", brandAbsoluteUrl(brand))}>`);
        } else {
          _push(`<!---->`);
        }
        if (getBrandTitle(brand)) {
          _push(`<meta itemprop="name"${ssrRenderAttr("content", getBrandTitle(brand))}>`);
        } else {
          _push(`<!---->`);
        }
        if (getBrandShort(brand)) {
          _push(`<meta itemprop="description"${ssrRenderAttr("content", getBrandShort(brand))}>`);
        } else {
          _push(`<!---->`);
        }
        if (getBrandImage(brand)) {
          _push(`<meta itemprop="image"${ssrRenderAttr("content", getBrandImage(brand))}>`);
        } else {
          _push(`<!---->`);
        }
        if (getBrandLogo(brand)) {
          _push(`<meta itemprop="logo"${ssrRenderAttr("content", getBrandLogo(brand))}>`);
        } else {
          _push(`<!---->`);
        }
        if (hasBrandImages(brand)) {
          _push(`<div class="relative shrink-0">`);
          _push(ssrRenderComponent(unref(Link), {
            href: brandLink(brand),
            title: getBrandTitle(brand)
          }, {
            default: withCtx((_, _push2, _parent2, _scopeId) => {
              if (_push2) {
                _push2(ssrRenderComponent(UniversalImageSlider, {
                  entity: brand,
                  "height-class": "h-44",
                  "rounded-class": "rounded-md",
                  "wrapper-class": "w-full sm:w-60 border border-gray-400\n                                           dark:border-gray-600",
                  "img-class": "w-full h-full object-cover transition\n                                       duration-300 group-hover:scale-105"
                }, null, _parent2, _scopeId));
              } else {
                return [
                  createVNode(UniversalImageSlider, {
                    entity: brand,
                    "height-class": "h-44",
                    "rounded-class": "rounded-md",
                    "wrapper-class": "w-full sm:w-60 border border-gray-400\n                                           dark:border-gray-600",
                    "img-class": "w-full h-full object-cover transition\n                                       duration-300 group-hover:scale-105"
                  }, null, 8, ["entity"])
                ];
              }
            }),
            _: 2
          }, _parent));
          _push(`</div>`);
        } else {
          _push(`<!---->`);
        }
        _push(`<div class="flex min-w-0 flex-1 flex-col justify-between gap-3"><div><div class="flex flex-col gap-3 sm:flex-row sm:items-center">`);
        if (getBrandLogo(brand)) {
          _push(ssrRenderComponent(unref(Link), {
            href: brandLink(brand),
            title: getBrandTitle(brand),
            class: "flex h-16 w-auto shrink-0 items-center justify-center overflow-hidden rounded-md border border-gray-200 bg-white p-1.5 dark:border-gray-600 dark:bg-gray-800"
          }, {
            default: withCtx((_, _push2, _parent2, _scopeId) => {
              if (_push2) {
                _push2(`<img${ssrRenderAttr("src", getBrandLogo(brand))}${ssrRenderAttr("alt", getBrandTitle(brand))}${ssrRenderAttr("title", getBrandTitle(brand))} class="h-full w-full object-contain" loading="lazy"${_scopeId}>`);
              } else {
                return [
                  createVNode("img", {
                    src: getBrandLogo(brand),
                    alt: getBrandTitle(brand),
                    title: getBrandTitle(brand),
                    class: "h-full w-full object-contain",
                    loading: "lazy"
                  }, null, 8, ["src", "alt", "title"])
                ];
              }
            }),
            _: 2
          }, _parent));
        } else {
          _push(`<!---->`);
        }
        _push(`<div class="min-w-0 flex-1">`);
        if (getBrandTitle(brand)) {
          _push(ssrRenderComponent(unref(Link), {
            href: brandLink(brand),
            title: getBrandShort(brand),
            class: "inline-flex min-w-0 items-center gap-2"
          }, {
            default: withCtx((_, _push2, _parent2, _scopeId) => {
              if (_push2) {
                _push2(`<span class="text-lg font-semibold text-slate-900/85 group-hover:opacity-75 dark:text-slate-100/85"${_scopeId}>${ssrInterpolate(getBrandTitle(brand))}</span>`);
              } else {
                return [
                  createVNode("span", { class: "text-lg font-semibold text-slate-900/85 group-hover:opacity-75 dark:text-slate-100/85" }, toDisplayString(getBrandTitle(brand)), 1)
                ];
              }
            }),
            _: 2
          }, _parent));
        } else {
          _push(`<!---->`);
        }
        if (brand.website) {
          _push(`<a${ssrRenderAttr("href", brand.website)} target="_blank" rel="noopener noreferrer" class="mt-1 block w-fit max-w-full truncate text-xs text-amber-700 hover:underline dark:text-amber-300"${ssrRenderAttr("title", brand.website)}>${ssrInterpolate(brand.website)}</a>`);
        } else {
          _push(`<!---->`);
        }
        _push(`</div></div>`);
        if (getBrandShort(brand)) {
          _push(`<div class="mt-3 text-sm leading-relaxed text-slate-700 dark:text-slate-300">${ssrInterpolate(getBrandShort(brand))}</div>`);
        } else {
          _push(`<!---->`);
        }
        _push(`</div><div class="flex flex-wrap items-center justify-between gap-3"><div class="flex flex-wrap items-center gap-2 text-xs font-semibold text-slate-500 dark:text-slate-400"><div class="flex items-center justify-center gap-1 rounded-sm border border-slate-400 px-2 py-1"${ssrRenderAttr("title", unref(t)("products"))}><svg class="h-3 w-3 shrink-0 text-violet-600 dark:text-violet-300" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M4 4h6v6H4V4Zm10 0h6v6h-6V4ZM4 14h6v6H4v-6Zm10 0h6v6h-6v-6Z"></path></svg> ${ssrInterpolate(getProductsCount(brand))}</div>`);
        if (getViews(brand) > 0) {
          _push(`<div class="flex items-center justify-center gap-1 rounded-sm border border-slate-400 px-2 py-1"${ssrRenderAttr("title", unref(t)("views"))}><svg class="h-3 w-3 text-blue-600/85 dark:text-blue-200/85" viewBox="0 0 576 512" fill="currentColor" aria-hidden="true"><path d="M569.354 231.631C512.97 135.949 407.81 72 288 72 168.14 72 63.004 135.994 6.646 231.631a47.999 47.999 0 0 0 0 48.739C63.031 376.051 168.19 440 288 440c119.86 0 224.996-63.994 281.354-159.631a47.997 47.997 0 0 0 0-48.738zM288 392c-102.556 0-192.091-54.701-240-136 44.157-74.933 123.677-127.27 216.162-135.007C273.958 131.078 280 144.83 280 160c0 30.928-25.072 56-56 56s-56-25.072-56-56l.001-.042C157.794 179.043 152 200.844 152 224c0 75.111 60.889 136 136 136s136-60.889 136-136c0-31.031-10.4-59.629-27.895-82.515C451.704 164.638 498.009 205.106 528 256c-47.908 81.299-137.444 136-240 136z"></path></svg> ${ssrInterpolate(getViews(brand))}</div>`);
        } else {
          _push(`<!---->`);
        }
        _push(`</div>`);
        _push(ssrRenderComponent(unref(Link), {
          href: brandLink(brand),
          title: getBrandTitle(brand),
          class: "flex w-fit items-center justify-center gap-2 rounded-sm px-3 py-1 btn-default"
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
        _push(`</div></div></div></article>`);
      });
      _push(`<!--]--></div>`);
    };
  }
};
const _sfc_setup$1 = _sfc_main$1.setup;
_sfc_main$1.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Components/Public/Default/Market/MarketBrand/MarketBrandRows.vue");
  return _sfc_setup$1 ? _sfc_setup$1(props, ctx) : void 0;
};
const LEFT_SIDEBAR_KEY = "public_left_sidebar_collapsed";
const RIGHT_SIDEBAR_KEY = "public_right_sidebar_collapsed";
const VIEW_KEY = "public_market_brands_view";
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
    publicMarketBrandsProcessingMode: {
      type: String,
      default: "server"
    },
    /** Сортировка Public по умолчанию из backend */
    defaultSort: {
      type: String,
      default: "sortAsc"
    },
    categoryTree: {
      type: Array,
      default: () => []
    },
    brands: {
      type: [Array, Object],
      default: () => []
    },
    brandsCount: {
      type: Number,
      default: 0
    },
    brandsFound: {
      type: Number,
      default: 0
    },
    filters: {
      type: Object,
      default: () => ({})
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
    const categoryTree = computed(() => {
      return Array.isArray(props.categoryTree) ? props.categoryTree : [];
    });
    const brandsData = computed(() => {
      var _a2;
      if (Array.isArray(props.brands)) {
        return props.brands;
      }
      if (Array.isArray((_a2 = props.brands) == null ? void 0 : _a2.data)) {
        return props.brands.data;
      }
      return [];
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
    const brandGridCols = computed(() => {
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
        ((_c = props.filters) == null ? void 0 : _c.sort) ?? props.defaultSort ?? ""
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
    const brandSortOptions = [
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
        value: "productsDesc",
        label: `${t("products")} 9→0`
      },
      {
        value: "productsAsc",
        label: `${t("products")} 0→9`
      },
      {
        value: "viewsDesc",
        label: `${t("views")} 9→0`
      },
      {
        value: "viewsAsc",
        label: `${t("views")} 0→9`
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
      return String(value ?? "").toLocaleLowerCase().trim();
    };
    const normalizeNumber = (value) => {
      const number = Number(value);
      return Number.isFinite(number) ? number : 0;
    };
    const getBrandTitle = (brand) => {
      var _a2;
      return ((_a2 = brand == null ? void 0 : brand.translation) == null ? void 0 : _a2.title) || "";
    };
    const getBrandSubtitle = (brand) => {
      var _a2;
      return ((_a2 = brand == null ? void 0 : brand.translation) == null ? void 0 : _a2.subtitle) || "";
    };
    const getBrandShort = (brand) => {
      var _a2;
      return ((_a2 = brand == null ? void 0 : brand.translation) == null ? void 0 : _a2.short) || "";
    };
    const searchWords = computed(() => {
      return normalizeText(q.value).split(/\s+/u).filter((word) => {
        return word.length >= 2;
      });
    });
    const filteredBrands = computed(() => {
      const words = searchWords.value;
      if (!words.length) {
        return brandsData.value;
      }
      return brandsData.value.filter(
        (brand) => {
          var _a2;
          const fields = [
            getBrandTitle(brand),
            getBrandSubtitle(brand),
            getBrandShort(brand),
            (_a2 = brand == null ? void 0 : brand.translation) == null ? void 0 : _a2.description,
            brand == null ? void 0 : brand.url,
            brand == null ? void 0 : brand.website
          ].map(normalizeText);
          return words.every((word) => {
            return fields.some((field) => {
              return field.includes(word);
            });
          });
        }
      );
    });
    const compareIdDesc = (a, b) => {
      return normalizeNumber(b == null ? void 0 : b.id) - normalizeNumber(a == null ? void 0 : a.id);
    };
    const compareNumber = (a, b, field, direction = "asc") => {
      const first = normalizeNumber(
        a == null ? void 0 : a[field]
      );
      const second = normalizeNumber(
        b == null ? void 0 : b[field]
      );
      const result = direction === "desc" ? second - first : first - second;
      return result || compareIdDesc(a, b);
    };
    const compareText = (first, second, direction = "asc") => {
      const a = normalizeText(first);
      const b = normalizeText(second);
      return direction === "desc" ? b.localeCompare(a) : a.localeCompare(b);
    };
    const sortedBrands = computed(() => {
      const list = [
        ...filteredBrands.value
      ];
      return list.sort((a, b) => {
        switch (sort.value) {
          case "idAsc":
            return normalizeNumber(a == null ? void 0 : a.id) - normalizeNumber(b == null ? void 0 : b.id);
          case "idDesc":
            return normalizeNumber(b == null ? void 0 : b.id) - normalizeNumber(a == null ? void 0 : a.id);
          case "sortAsc":
            return compareNumber(
              a,
              b,
              "sort",
              "asc"
            );
          case "sortDesc":
            return compareNumber(
              a,
              b,
              "sort",
              "desc"
            );
          case "titleAsc": {
            const result = compareText(
              getBrandTitle(a),
              getBrandTitle(b),
              "asc"
            );
            return result || compareIdDesc(a, b);
          }
          case "titleDesc": {
            const result = compareText(
              getBrandTitle(a),
              getBrandTitle(b),
              "desc"
            );
            return result || compareIdDesc(a, b);
          }
          case "productsAsc":
            return compareNumber(
              a,
              b,
              "products_count",
              "asc"
            );
          case "productsDesc":
            return compareNumber(
              a,
              b,
              "products_count",
              "desc"
            );
          case "viewsAsc":
            return compareNumber(
              a,
              b,
              "views",
              "asc"
            );
          case "viewsDesc":
            return compareNumber(
              a,
              b,
              "views",
              "desc"
            );
          default:
            return compareNumber(
              a,
              b,
              "sort",
              "asc"
            );
        }
      });
    });
    const frontendPaginatedBrands = computed(() => {
      const start = (frontendCurrentPage.value - 1) * perPage.value;
      return sortedBrands.value.slice(
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
    watch(frontendCurrentPage, () => {
      if (!props.useServerProcessing) {
        scrollToTarget();
      }
    });
    const currentPage = computed(() => {
      var _a2, _b2, _c2;
      return Number(
        ((_b2 = (_a2 = props.brands) == null ? void 0 : _a2.meta) == null ? void 0 : _b2.current_page) ?? ((_c2 = props.brands) == null ? void 0 : _c2.current_page) ?? 1
      ) || 1;
    });
    const lastPage = computed(() => {
      var _a2, _b2, _c2;
      return Number(
        ((_b2 = (_a2 = props.brands) == null ? void 0 : _a2.meta) == null ? void 0 : _b2.last_page) ?? ((_c2 = props.brands) == null ? void 0 : _c2.last_page) ?? 1
      ) || 1;
    });
    const getBrandLogo = (brand) => {
      const logo = brand == null ? void 0 : brand.logo;
      if (!logo) {
        return "";
      }
      const value = String(logo).trim();
      if (!value) {
        return "";
      }
      if (value.startsWith("http://") || value.startsWith("https://") || value.startsWith("/storage/")) {
        return value;
      }
      return `/storage/${value}`;
    };
    const seoTitle = computed(() => {
      var _a2;
      return ((_a2 = props.seo) == null ? void 0 : _a2.title) || t("brands");
    });
    const seoKeywords = computed(() => {
      var _a2;
      return ((_a2 = props.seo) == null ? void 0 : _a2.keywords) || "";
    });
    const seoDescription = computed(() => {
      var _a2;
      return ((_a2 = props.seo) == null ? void 0 : _a2.description) || t("brands") || "";
    });
    const contentLocale = computed(() => {
      return String(
        props.locale || ""
      );
    });
    const ogLocale = computed(() => {
      return contentLocale.value;
    });
    const dcSubject = computed(() => {
      return seoKeywords.value || seoTitle.value;
    });
    const seoPreview = computed(() => {
      for (const brand of brandsData.value) {
        const images = normalizeList(
          brand == null ? void 0 : brand.images
        );
        const image = images[0];
        const url = (image == null ? void 0 : image.webp_url) || (image == null ? void 0 : image.image_url) || (image == null ? void 0 : image.thumb_url) || (image == null ? void 0 : image.url) || getBrandLogo(brand) || "";
        if (url) {
          return {
            url,
            alt: (image == null ? void 0 : image.alt) || getBrandTitle(brand) || seoTitle.value
          };
        }
      }
      return {
        url: "",
        alt: ""
      };
    });
    const seoImage = computed(() => {
      return seoPreview.value.url;
    });
    const seoImageAlt = computed(() => {
      return seoPreview.value.alt;
    });
    const canonicalUrl = computed(() => {
      const baseUrl = String(
        route("public.marketBrands.index")
      );
      if (props.useServerProcessing && currentPage.value > 1) {
        return `${baseUrl}?page=${currentPage.value}`;
      }
      return baseUrl;
    });
    const robotsContent = computed(() => {
      const hasSearch = String(q.value || "").trim() !== "";
      const hasAlternativeSort = String(
        sort.value || props.defaultSort
      ) !== String(
        props.defaultSort
      );
      if (props.useServerProcessing && (hasSearch || hasAlternativeSort)) {
        return "noindex, follow, max-image-preview:large";
      }
      return "index, follow, max-image-preview:large";
    });
    const indexRoute = () => {
      return route(
        "public.marketBrands.index"
      );
    };
    const reloadBrands = (page2 = 1) => {
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
      frontendCurrentPage.value = 1;
      if (props.useServerProcessing) {
        reloadBrands(1);
      }
    };
    const resetSearch = () => {
      q.value = "";
      sort.value = props.defaultSort || "";
      frontendCurrentPage.value = 1;
      if (props.useServerProcessing) {
        reloadBrands(1);
      }
    };
    const updateSort = (value) => {
      sort.value = value || props.defaultSort || "";
      frontendCurrentPage.value = 1;
      if (props.useServerProcessing) {
        reloadBrands(1);
      }
    };
    const updateViewMode = (value) => {
      viewMode.value = value || "grid";
      frontendCurrentPage.value = 1;
      if (props.useServerProcessing) {
        reloadBrands(1);
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
      reloadBrands(safePage);
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
    const displayedBrands = computed(() => {
      return props.useServerProcessing ? brandsData.value : frontendPaginatedBrands.value;
    });
    const brandListStartPosition = computed(() => {
      const pageNumber = props.useServerProcessing ? currentPage.value : frontendCurrentPage.value;
      return Math.max(
        0,
        (pageNumber - 1) * perPage.value
      );
    });
    return (_ctx, _push, _parent, _attrs) => {
      _push(`<!--[-->`);
      _push(ssrRenderComponent(unref(Head), null, {
        default: withCtx((_, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(`<title${_scopeId}>${ssrInterpolate(seoTitle.value)}</title><meta name="title"${ssrRenderAttr("content", seoTitle.value)}${_scopeId}>`);
            if (seoDescription.value) {
              _push2(`<meta name="description"${ssrRenderAttr("content", seoDescription.value)}${_scopeId}>`);
            } else {
              _push2(`<!---->`);
            }
            if (seoKeywords.value) {
              _push2(`<meta name="keywords"${ssrRenderAttr("content", seoKeywords.value)}${_scopeId}>`);
            } else {
              _push2(`<!---->`);
            }
            if (contentLocale.value) {
              _push2(`<meta http-equiv="content-language"${ssrRenderAttr("content", contentLocale.value)}${_scopeId}>`);
            } else {
              _push2(`<!---->`);
            }
            _push2(`<meta name="robots"${ssrRenderAttr("content", robotsContent.value)}${_scopeId}><link rel="canonical"${ssrRenderAttr("href", canonicalUrl.value)}${_scopeId}><meta property="og:type" content="website"${_scopeId}><meta property="og:title"${ssrRenderAttr("content", seoTitle.value)}${_scopeId}>`);
            if (seoDescription.value) {
              _push2(`<meta property="og:description"${ssrRenderAttr("content", seoDescription.value)}${_scopeId}>`);
            } else {
              _push2(`<!---->`);
            }
            _push2(`<meta property="og:url"${ssrRenderAttr("content", canonicalUrl.value)}${_scopeId}>`);
            if (ogLocale.value) {
              _push2(`<meta property="og:locale"${ssrRenderAttr("content", ogLocale.value)}${_scopeId}>`);
            } else {
              _push2(`<!---->`);
            }
            if (seoImage.value) {
              _push2(`<meta property="og:image"${ssrRenderAttr("content", seoImage.value)}${_scopeId}>`);
            } else {
              _push2(`<!---->`);
            }
            if (seoImage.value && seoImageAlt.value) {
              _push2(`<meta property="og:image:alt"${ssrRenderAttr("content", seoImageAlt.value)}${_scopeId}>`);
            } else {
              _push2(`<!---->`);
            }
            _push2(`<meta name="twitter:card"${ssrRenderAttr(
              "content",
              seoImage.value ? "summary_large_image" : "summary"
            )}${_scopeId}><meta name="twitter:title"${ssrRenderAttr("content", seoTitle.value)}${_scopeId}>`);
            if (seoDescription.value) {
              _push2(`<meta name="twitter:description"${ssrRenderAttr("content", seoDescription.value)}${_scopeId}>`);
            } else {
              _push2(`<!---->`);
            }
            if (seoImage.value) {
              _push2(`<meta name="twitter:image"${ssrRenderAttr("content", seoImage.value)}${_scopeId}>`);
            } else {
              _push2(`<!---->`);
            }
            if (seoImage.value && seoImageAlt.value) {
              _push2(`<meta name="twitter:image:alt"${ssrRenderAttr("content", seoImageAlt.value)}${_scopeId}>`);
            } else {
              _push2(`<!---->`);
            }
            _push2(`<meta name="DC.title"${ssrRenderAttr("content", seoTitle.value)}${_scopeId}>`);
            if (seoDescription.value) {
              _push2(`<meta name="DC.description"${ssrRenderAttr("content", seoDescription.value)}${_scopeId}>`);
            } else {
              _push2(`<!---->`);
            }
            if (dcSubject.value) {
              _push2(`<meta name="DC.subject"${ssrRenderAttr("content", dcSubject.value)}${_scopeId}>`);
            } else {
              _push2(`<!---->`);
            }
            if (contentLocale.value) {
              _push2(`<meta name="DC.language"${ssrRenderAttr("content", contentLocale.value)}${_scopeId}>`);
            } else {
              _push2(`<!---->`);
            }
            _push2(`<meta name="DC.identifier"${ssrRenderAttr("content", canonicalUrl.value)}${_scopeId}><meta name="DC.type" content="Collection"${_scopeId}><meta name="DC.format" content="text/html"${_scopeId}>`);
          } else {
            return [
              createVNode("title", null, toDisplayString(seoTitle.value), 1),
              createVNode("meta", {
                name: "title",
                content: seoTitle.value
              }, null, 8, ["content"]),
              seoDescription.value ? (openBlock(), createBlock("meta", {
                key: 0,
                name: "description",
                content: seoDescription.value
              }, null, 8, ["content"])) : createCommentVNode("", true),
              seoKeywords.value ? (openBlock(), createBlock("meta", {
                key: 1,
                name: "keywords",
                content: seoKeywords.value
              }, null, 8, ["content"])) : createCommentVNode("", true),
              contentLocale.value ? (openBlock(), createBlock("meta", {
                key: 2,
                "http-equiv": "content-language",
                content: contentLocale.value
              }, null, 8, ["content"])) : createCommentVNode("", true),
              createVNode("meta", {
                name: "robots",
                content: robotsContent.value
              }, null, 8, ["content"]),
              createVNode("link", {
                rel: "canonical",
                href: canonicalUrl.value
              }, null, 8, ["href"]),
              createVNode("meta", {
                property: "og:type",
                content: "website"
              }),
              createVNode("meta", {
                property: "og:title",
                content: seoTitle.value
              }, null, 8, ["content"]),
              seoDescription.value ? (openBlock(), createBlock("meta", {
                key: 3,
                property: "og:description",
                content: seoDescription.value
              }, null, 8, ["content"])) : createCommentVNode("", true),
              createVNode("meta", {
                property: "og:url",
                content: canonicalUrl.value
              }, null, 8, ["content"]),
              ogLocale.value ? (openBlock(), createBlock("meta", {
                key: 4,
                property: "og:locale",
                content: ogLocale.value
              }, null, 8, ["content"])) : createCommentVNode("", true),
              seoImage.value ? (openBlock(), createBlock("meta", {
                key: 5,
                property: "og:image",
                content: seoImage.value
              }, null, 8, ["content"])) : createCommentVNode("", true),
              seoImage.value && seoImageAlt.value ? (openBlock(), createBlock("meta", {
                key: 6,
                property: "og:image:alt",
                content: seoImageAlt.value
              }, null, 8, ["content"])) : createCommentVNode("", true),
              createVNode("meta", {
                name: "twitter:card",
                content: seoImage.value ? "summary_large_image" : "summary"
              }, null, 8, ["content"]),
              createVNode("meta", {
                name: "twitter:title",
                content: seoTitle.value
              }, null, 8, ["content"]),
              seoDescription.value ? (openBlock(), createBlock("meta", {
                key: 7,
                name: "twitter:description",
                content: seoDescription.value
              }, null, 8, ["content"])) : createCommentVNode("", true),
              seoImage.value ? (openBlock(), createBlock("meta", {
                key: 8,
                name: "twitter:image",
                content: seoImage.value
              }, null, 8, ["content"])) : createCommentVNode("", true),
              seoImage.value && seoImageAlt.value ? (openBlock(), createBlock("meta", {
                key: 9,
                name: "twitter:image:alt",
                content: seoImageAlt.value
              }, null, 8, ["content"])) : createCommentVNode("", true),
              createVNode("meta", {
                name: "DC.title",
                content: seoTitle.value
              }, null, 8, ["content"]),
              seoDescription.value ? (openBlock(), createBlock("meta", {
                key: 10,
                name: "DC.description",
                content: seoDescription.value
              }, null, 8, ["content"])) : createCommentVNode("", true),
              dcSubject.value ? (openBlock(), createBlock("meta", {
                key: 11,
                name: "DC.subject",
                content: dcSubject.value
              }, null, 8, ["content"])) : createCommentVNode("", true),
              contentLocale.value ? (openBlock(), createBlock("meta", {
                key: 12,
                name: "DC.language",
                content: contentLocale.value
              }, null, 8, ["content"])) : createCommentVNode("", true),
              createVNode("meta", {
                name: "DC.identifier",
                content: canonicalUrl.value
              }, null, 8, ["content"]),
              createVNode("meta", {
                name: "DC.type",
                content: "Collection"
              }),
              createVNode("meta", {
                name: "DC.format",
                content: "text/html"
              })
            ];
          }
        }),
        _: 1
      }, _parent));
      _push(ssrRenderComponent(_sfc_main$3, null, {
        default: withCtx((_, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(ssrRenderComponent(_sfc_main$4, null, null, _parent2, _scopeId));
            _push2(`<main class="min-h-screen px-1 lg:px-6 max-w-full"${_scopeId}><div class="mx-auto tracking-wider pt-20 lg:pt-44"${_scopeId}><div class="ext-color w-full min-w-0 py-3 px-1 flex flex-col lg:flex-row gap-4 rounded-3xl border-2 border-slate-300 dark:border-slate-500"${_scopeId}>`);
            if (showLeft.value) {
              _push2(`<aside class="${ssrRenderClass([leftCollapsed.value ? "lg:w-6" : "lg:w-72", "shrink-0 transition-all duration-300"])}"${_scopeId}>`);
              _push2(ssrRenderComponent(_sfc_main$5, {
                "category-tree": categoryTree.value,
                collapsed: leftCollapsed.value,
                onCollapsed: ($event) => leftCollapsed.value = $event
              }, null, _parent2, _scopeId));
              _push2(`</aside>`);
            } else {
              _push2(`<!---->`);
            }
            _push2(`<div class="min-w-0 flex-1 pb-6 slate-1"${_scopeId}><div class="w-full"${_scopeId}><article itemscope itemtype="https://schema.org/CollectionPage"${ssrRenderAttr("itemid", canonicalUrl.value)}${_scopeId}><meta itemprop="url"${ssrRenderAttr("content", canonicalUrl.value)}${_scopeId}><meta itemprop="name"${ssrRenderAttr("content", seoTitle.value)}${_scopeId}>`);
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
            if (contentLocale.value) {
              _push2(`<meta itemprop="inLanguage"${ssrRenderAttr("content", contentLocale.value)}${_scopeId}>`);
            } else {
              _push2(`<!---->`);
            }
            if (seoImage.value) {
              _push2(`<meta itemprop="primaryImageOfPage"${ssrRenderAttr("content", seoImage.value)}${_scopeId}>`);
            } else {
              _push2(`<!---->`);
            }
            _push2(`<nav class="text-sm" aria-label="Breadcrumb" itemscope itemtype="https://schema.org/BreadcrumbList"${_scopeId}><ol class="flex flex-wrap items-center font-semibold"${_scopeId}><li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem" class="flex items-center"${_scopeId}>`);
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
            _push2(`<meta itemprop="position" content="1"${_scopeId}></li><li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem" class="flex items-center"${_scopeId}><span class="mx-2 breadcrumbs"${_scopeId}> / </span>`);
            _push2(ssrRenderComponent(unref(Link), {
              itemprop: "item",
              href: _ctx.route("public.marketCategories.index"),
              class: "breadcrumb-link hover:underline"
            }, {
              default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                if (_push3) {
                  _push3(`<span itemprop="name"${_scopeId2}>${ssrInterpolate(unref(t)("categories"))}</span>`);
                } else {
                  return [
                    createVNode("span", { itemprop: "name" }, toDisplayString(unref(t)("categories")), 1)
                  ];
                }
              }),
              _: 1
            }, _parent2, _scopeId));
            _push2(`<meta itemprop="position" content="2"${_scopeId}></li><li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem" class="flex items-center" aria-current="page"${_scopeId}><span class="mx-2 breadcrumbs"${_scopeId}> / </span><h1 itemprop="name" class="breadcrumbs text-sm font-semibold"${_scopeId}>${ssrInterpolate(unref(t)("brands"))}</h1><meta itemprop="item"${ssrRenderAttr("content", canonicalUrl.value)}${_scopeId}><meta itemprop="position" content="3"${_scopeId}></li></ol></nav>`);
            if (__props.useServerProcessing) {
              _push2(ssrRenderComponent(_sfc_main$6, {
                modelValue: q.value,
                "onUpdate:modelValue": ($event) => q.value = $event,
                found: __props.brandsFound,
                "view-mode": viewMode.value,
                "sort-value": sort.value,
                "sort-options": brandSortOptions,
                "default-sort": __props.defaultSort,
                "found-label": unref(t)("brands"),
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
                found: sortedBrands.value.length,
                "view-mode": viewMode.value,
                "sort-value": sort.value,
                "sort-options": brandSortOptions,
                "default-sort": __props.defaultSort,
                "found-label": unref(t)("brands"),
                "search-placeholder": unref(t)("searchByName"),
                onReset: resetSearch,
                "onUpdate:viewMode": updateViewMode,
                "onUpdate:sortValue": updateSort
              }, null, _parent2, _scopeId));
            }
            _push2(`<div${_scopeId}></div>`);
            if (displayedBrands.value.length === 0) {
              _push2(`<div class="mt-6 text-center text-slate-700 dark:text-slate-300"${_scopeId}>${ssrInterpolate(unref(t)("noData"))}</div>`);
            } else {
              _push2(`<div itemprop="mainEntity"${_scopeId}>`);
              if (viewMode.value === "grid") {
                _push2(ssrRenderComponent(_sfc_main$2, {
                  brands: displayedBrands.value,
                  cols: brandGridCols.value,
                  "start-position": brandListStartPosition.value
                }, null, _parent2, _scopeId));
              } else {
                _push2(ssrRenderComponent(_sfc_main$1, {
                  brands: displayedBrands.value,
                  "start-position": brandListStartPosition.value
                }, null, _parent2, _scopeId));
              }
              _push2(`</div>`);
            }
            if (__props.useServerProcessing) {
              _push2(ssrRenderComponent(_sfc_main$8, {
                "current-page": currentPage.value,
                "last-page": lastPage.value,
                found: __props.brandsFound,
                onPrev: goPrev,
                onNext: goNext,
                onGo: goToPage
              }, null, _parent2, _scopeId));
            } else {
              _push2(ssrRenderComponent(_sfc_main$9, {
                currentPage: frontendCurrentPage.value,
                "onUpdate:currentPage": ($event) => frontendCurrentPage.value = $event,
                "items-per-page": perPage.value,
                "total-items": sortedBrands.value.length
              }, null, _parent2, _scopeId));
            }
            _push2(`</article></div></div>`);
            if (showRight.value) {
              _push2(`<aside class="${ssrRenderClass([rightCollapsed.value ? "lg:w-6" : "lg:w-72", "shrink-0 transition-all duration-300"])}"${_scopeId}>`);
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
            if (isAdmin.value) {
              _push2(ssrRenderComponent(_sfc_main$c, {
                "setting-key": "publicMarketBrandsProcessingMode",
                mode: __props.publicMarketBrandsProcessingMode,
                "use-server-processing": __props.useServerProcessing,
                total: __props.brandsCount
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
                      class: ["shrink-0 transition-all duration-300", leftCollapsed.value ? "lg:w-6" : "lg:w-72"]
                    }, [
                      createVNode(_sfc_main$5, {
                        "category-tree": categoryTree.value,
                        collapsed: leftCollapsed.value,
                        onCollapsed: ($event) => leftCollapsed.value = $event
                      }, null, 8, ["category-tree", "collapsed", "onCollapsed"])
                    ], 2)) : createCommentVNode("", true),
                    createVNode("div", { class: "min-w-0 flex-1 pb-6 slate-1" }, [
                      createVNode("div", { class: "w-full" }, [
                        createVNode("article", {
                          itemscope: "",
                          itemtype: "https://schema.org/CollectionPage",
                          itemid: canonicalUrl.value
                        }, [
                          createVNode("meta", {
                            itemprop: "url",
                            content: canonicalUrl.value
                          }, null, 8, ["content"]),
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
                          contentLocale.value ? (openBlock(), createBlock("meta", {
                            key: 2,
                            itemprop: "inLanguage",
                            content: contentLocale.value
                          }, null, 8, ["content"])) : createCommentVNode("", true),
                          seoImage.value ? (openBlock(), createBlock("meta", {
                            key: 3,
                            itemprop: "primaryImageOfPage",
                            content: seoImage.value
                          }, null, 8, ["content"])) : createCommentVNode("", true),
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
                                class: "flex items-center"
                              }, [
                                createVNode("span", { class: "mx-2 breadcrumbs" }, " / "),
                                createVNode(unref(Link), {
                                  itemprop: "item",
                                  href: _ctx.route("public.marketCategories.index"),
                                  class: "breadcrumb-link hover:underline"
                                }, {
                                  default: withCtx(() => [
                                    createVNode("span", { itemprop: "name" }, toDisplayString(unref(t)("categories")), 1)
                                  ]),
                                  _: 1
                                }, 8, ["href"]),
                                createVNode("meta", {
                                  itemprop: "position",
                                  content: "2"
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
                                createVNode("h1", {
                                  itemprop: "name",
                                  class: "breadcrumbs text-sm font-semibold"
                                }, toDisplayString(unref(t)("brands")), 1),
                                createVNode("meta", {
                                  itemprop: "item",
                                  content: canonicalUrl.value
                                }, null, 8, ["content"]),
                                createVNode("meta", {
                                  itemprop: "position",
                                  content: "3"
                                })
                              ])
                            ])
                          ]),
                          __props.useServerProcessing ? (openBlock(), createBlock(_sfc_main$6, {
                            key: 4,
                            modelValue: q.value,
                            "onUpdate:modelValue": ($event) => q.value = $event,
                            found: __props.brandsFound,
                            "view-mode": viewMode.value,
                            "sort-value": sort.value,
                            "sort-options": brandSortOptions,
                            "default-sort": __props.defaultSort,
                            "found-label": unref(t)("brands"),
                            "search-placeholder": unref(t)("searchByName"),
                            onSubmit: submitSearch,
                            onReset: resetSearch,
                            "onUpdate:viewMode": updateViewMode,
                            "onUpdate:sortValue": updateSort
                          }, null, 8, ["modelValue", "onUpdate:modelValue", "found", "view-mode", "sort-value", "default-sort", "found-label", "search-placeholder"])) : (openBlock(), createBlock(_sfc_main$7, {
                            key: 5,
                            modelValue: q.value,
                            "onUpdate:modelValue": ($event) => q.value = $event,
                            found: sortedBrands.value.length,
                            "view-mode": viewMode.value,
                            "sort-value": sort.value,
                            "sort-options": brandSortOptions,
                            "default-sort": __props.defaultSort,
                            "found-label": unref(t)("brands"),
                            "search-placeholder": unref(t)("searchByName"),
                            onReset: resetSearch,
                            "onUpdate:viewMode": updateViewMode,
                            "onUpdate:sortValue": updateSort
                          }, null, 8, ["modelValue", "onUpdate:modelValue", "found", "view-mode", "sort-value", "default-sort", "found-label", "search-placeholder"])),
                          createVNode("div", {
                            ref_key: "scrollTarget",
                            ref: scrollTarget
                          }, null, 512),
                          displayedBrands.value.length === 0 ? (openBlock(), createBlock("div", {
                            key: 6,
                            class: "mt-6 text-center text-slate-700 dark:text-slate-300"
                          }, toDisplayString(unref(t)("noData")), 1)) : (openBlock(), createBlock("div", {
                            key: 7,
                            itemprop: "mainEntity"
                          }, [
                            viewMode.value === "grid" ? (openBlock(), createBlock(_sfc_main$2, {
                              key: 0,
                              brands: displayedBrands.value,
                              cols: brandGridCols.value,
                              "start-position": brandListStartPosition.value
                            }, null, 8, ["brands", "cols", "start-position"])) : (openBlock(), createBlock(_sfc_main$1, {
                              key: 1,
                              brands: displayedBrands.value,
                              "start-position": brandListStartPosition.value
                            }, null, 8, ["brands", "start-position"]))
                          ])),
                          __props.useServerProcessing ? (openBlock(), createBlock(_sfc_main$8, {
                            key: 8,
                            "current-page": currentPage.value,
                            "last-page": lastPage.value,
                            found: __props.brandsFound,
                            onPrev: goPrev,
                            onNext: goNext,
                            onGo: goToPage
                          }, null, 8, ["current-page", "last-page", "found"])) : (openBlock(), createBlock(_sfc_main$9, {
                            key: 9,
                            currentPage: frontendCurrentPage.value,
                            "onUpdate:currentPage": ($event) => frontendCurrentPage.value = $event,
                            "items-per-page": perPage.value,
                            "total-items": sortedBrands.value.length
                          }, null, 8, ["currentPage", "onUpdate:currentPage", "items-per-page", "total-items"]))
                        ], 8, ["itemid"])
                      ])
                    ]),
                    showRight.value ? (openBlock(), createBlock("aside", {
                      key: 1,
                      class: ["shrink-0 transition-all duration-300", rightCollapsed.value ? "lg:w-6" : "lg:w-72"]
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
              createVNode(Progress),
              isAdmin.value ? (openBlock(), createBlock(_sfc_main$c, {
                key: 0,
                "setting-key": "publicMarketBrandsProcessingMode",
                mode: __props.publicMarketBrandsProcessingMode,
                "use-server-processing": __props.useServerProcessing,
                total: __props.brandsCount
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
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Pages/Public/Default/Market/MarketBrands/Index.vue");
  return _sfc_setup ? _sfc_setup(props, ctx) : void 0;
};
export {
  _sfc_main as default
};
