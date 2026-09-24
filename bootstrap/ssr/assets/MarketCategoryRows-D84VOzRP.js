import { computed, mergeProps, unref, withCtx, createVNode, createBlock, createCommentVNode, openBlock, toDisplayString, useSSRContext } from "vue";
import { ssrRenderAttrs, ssrRenderAttr, ssrRenderList, ssrRenderComponent, ssrInterpolate } from "vue/server-renderer";
import { Link } from "@inertiajs/vue3";
import { useI18n } from "vue-i18n";
import { U as UniversalImageSlider } from "./UniversalImageSlider-Cu2Xndcn.js";
const _sfc_main$1 = {
  __name: "MarketCategoryGrid",
  __ssrInlineRender: true,
  props: {
    categories: { type: Array, default: () => [] },
    cols: { type: Number, default: 3 },
    /** Начальная позиция списка Schema.org */
    startPosition: { type: Number, default: 0 },
    /** Связь ItemList с родительской Schema.org-сущностью */
    schemaProperty: { type: String, default: "" }
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
    const categoryLink = (category) => {
      return (category == null ? void 0 : category.url) ? route("public.marketCategories.show", { url: category.url }) : "#";
    };
    const getCategoryTitle = (category) => {
      var _a;
      return ((_a = category == null ? void 0 : category.translation) == null ? void 0 : _a.title) || "";
    };
    const getCategorySubtitle = (category) => {
      var _a;
      return ((_a = category == null ? void 0 : category.translation) == null ? void 0 : _a.subtitle) || "";
    };
    const getCategoryShort = (category) => {
      var _a;
      return ((_a = category == null ? void 0 : category.translation) == null ? void 0 : _a.short) || "";
    };
    const childrenCount = (category) => Number((category == null ? void 0 : category.children_count) ?? 0);
    const productsCount = (category) => Number((category == null ? void 0 : category.products_count) ?? 0);
    const hasSvgIcon = (category) => {
      return typeof (category == null ? void 0 : category.icon) === "string" && category.icon.trim().startsWith("<svg");
    };
    return (_ctx, _push, _parent, _attrs) => {
      _push(`<div${ssrRenderAttrs(mergeProps({
        class: ["grid gap-4", gridClass.value],
        itemprop: __props.schemaProperty || void 0,
        itemscope: "",
        itemtype: "https://schema.org/ItemList"
      }, _attrs))}><meta itemprop="numberOfItems"${ssrRenderAttr("content", String(__props.categories.length))}><!--[-->`);
      ssrRenderList(__props.categories, (category, index) => {
        _push(`<article class="group flex h-full flex-col overflow-hidden rounded-md border border-gray-200 bg-white shadow-sm transition hover:-translate-y-0.5 hover:shadow-md dark:border-gray-700 dark:bg-gray-900" itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem"><meta itemprop="position"${ssrRenderAttr("content", String(__props.startPosition + index + 1))}><div class="flex h-full flex-col" itemprop="item" itemscope itemtype="https://schema.org/CollectionPage"><meta itemprop="url"${ssrRenderAttr("content", categoryLink(category))}><meta itemprop="name"${ssrRenderAttr("content", getCategoryTitle(category))}>`);
        if (getCategoryShort(category)) {
          _push(`<meta itemprop="description"${ssrRenderAttr("content", getCategoryShort(category))}>`);
        } else {
          _push(`<!---->`);
        }
        _push(ssrRenderComponent(unref(Link), {
          href: categoryLink(category),
          class: "block overflow-hidden",
          itemprop: "url"
        }, {
          default: withCtx((_, _push2, _parent2, _scopeId) => {
            if (_push2) {
              _push2(ssrRenderComponent(UniversalImageSlider, {
                entity: category,
                itemprop: "image",
                "height-class": "h-48",
                "rounded-class": "",
                "wrapper-class": "",
                "img-class": "w-full h-full object-cover transition duration-300 group-hover:scale-105"
              }, null, _parent2, _scopeId));
            } else {
              return [
                createVNode(UniversalImageSlider, {
                  entity: category,
                  itemprop: "image",
                  "height-class": "h-48",
                  "rounded-class": "",
                  "wrapper-class": "",
                  "img-class": "w-full h-full object-cover transition duration-300 group-hover:scale-105"
                }, null, 8, ["entity"])
              ];
            }
          }),
          _: 2
        }, _parent));
        _push(`<div class="flex flex-1 flex-col p-4"><div class="flex items-center justify-center gap-2 text-center">`);
        _push(ssrRenderComponent(unref(Link), {
          href: categoryLink(category),
          title: `${unref(t)("products")}: ${productsCount(category)}`,
          class: "inline-flex items-center justify-center gap-2",
          itemprop: "url"
        }, {
          default: withCtx((_, _push2, _parent2, _scopeId) => {
            if (_push2) {
              if (hasSvgIcon(category)) {
                _push2(`<span class="flex shrink-0"${_scopeId}>${category.icon ?? ""}</span>`);
              } else {
                _push2(`<!---->`);
              }
              _push2(`<span class="text-base font-semibold text-slate-900/85 group-hover:opacity-75 dark:text-slate-100/85 dark:group-hover:opacity-75"${_scopeId}>${ssrInterpolate(getCategoryTitle(category))}</span>`);
            } else {
              return [
                hasSvgIcon(category) ? (openBlock(), createBlock("span", {
                  key: 0,
                  class: "flex shrink-0",
                  innerHTML: category.icon
                }, null, 8, ["innerHTML"])) : createCommentVNode("", true),
                createVNode("span", { class: "text-base font-semibold text-slate-900/85 group-hover:opacity-75 dark:text-slate-100/85 dark:group-hover:opacity-75" }, toDisplayString(getCategoryTitle(category)), 1)
              ];
            }
          }),
          _: 2
        }, _parent));
        _push(`</div>`);
        if (getCategorySubtitle(category)) {
          _push(`<div class="mt-2 text-center text-xs font-semibold text-slate-500 dark:text-slate-400">${ssrInterpolate(getCategorySubtitle(category))}</div>`);
        } else {
          _push(`<!---->`);
        }
        if (getCategoryShort(category)) {
          _push(`<div class="mt-3 line-clamp-3 text-sm text-slate-700 dark:text-slate-300">${ssrInterpolate(getCategoryShort(category))}</div>`);
        } else {
          _push(`<!---->`);
        }
        _push(`<div class="mt-3 flex flex-wrap items-center justify-center gap-2 text-xs font-semibold text-slate-500 dark:text-slate-400">`);
        if (childrenCount(category)) {
          _push(`<div class="flex items-center justify-center gap-1 rounded-sm border border-slate-600 px-2 py-1 dark:border-slate-400"${ssrRenderAttr("title", unref(t)("subheadings"))}><svg class="h-3 w-3 shrink-0 text-fuchsia-600/85 dark:text-fuchsia-200/85" viewBox="0 0 24 24" fill="currentColor"><path d="M4 3a2 2 0 1 0 0 4 2 2 0 0 0 0-4Zm0 7a2 2 0 1 0 0 4 2 2 0 0 0 0-4Zm0 7a2 2 0 1 0 0 4 2 2 0 0 0 0-4Zm6-12h10v2H10V5Zm0 7h10v2H10v-2Zm0 7h10v2H10v-2Z"></path></svg> ${ssrInterpolate(unref(t)("subheadings"))}: ${ssrInterpolate(childrenCount(category))}</div>`);
        } else {
          _push(`<!---->`);
        }
        if (productsCount(category)) {
          _push(`<div class="flex items-center justify-center gap-1 rounded-sm border border-slate-600 px-2 py-1 dark:border-slate-400"${ssrRenderAttr("title", unref(t)("products"))}><svg class="h-3 w-3 shrink-0 text-sky-600/85 dark:text-sky-200/85" viewBox="0 0 24 24" fill="currentColor"><path d="M21 8.5 12 3 3 8.5V19l9 5 9-5V8.5ZM12 5.3l5.8 3.5-2.2 1.3L10 6.8 12 5.3Zm-3.8 2.6 5.8 3.5-2 1.2-5.8-3.5 2-1.2ZM5 10.6l6 3.6v7L5 17.8v-7.2Zm8 10.6v-7l6-3.6v7.2l-6 3.4Z"></path></svg> ${ssrInterpolate(unref(t)("products"))}: ${ssrInterpolate(productsCount(category))}</div>`);
        } else {
          _push(`<!---->`);
        }
        if (category.images_count) {
          _push(`<div class="flex items-center justify-center gap-1 rounded-sm border border-slate-600 px-2 py-1 dark:border-slate-400"${ssrRenderAttr("title", unref(t)("images"))}>${ssrInterpolate(unref(t)("images"))}: ${ssrInterpolate(category.images_count)}</div>`);
        } else {
          _push(`<!---->`);
        }
        _push(`</div>`);
        if (category.level) {
          _push(`<div class="mt-3 flex items-center justify-center"><div class="rounded-sm border border-slate-400 px-2 py-1 text-xs font-semibold text-slate-500 dark:text-slate-400">${ssrInterpolate(unref(t)("level"))}: ${ssrInterpolate(category.level)}</div></div>`);
        } else {
          _push(`<!---->`);
        }
        _push(`<div class="mt-auto pt-4">`);
        _push(ssrRenderComponent(unref(Link), {
          href: categoryLink(category),
          class: "flex w-full items-center justify-center gap-2 rounded-sm px-3 py-2 btn-default",
          itemprop: "url"
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
        _push(`</div></div></div></article>`);
      });
      _push(`<!--]--></div>`);
    };
  }
};
const _sfc_setup$1 = _sfc_main$1.setup;
_sfc_main$1.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Components/Public/Default/Market/MarketCategory/MarketCategoryGrid.vue");
  return _sfc_setup$1 ? _sfc_setup$1(props, ctx) : void 0;
};
const _sfc_main = {
  __name: "MarketCategoryRows",
  __ssrInlineRender: true,
  props: {
    categories: { type: Array, default: () => [] },
    /** Начальная позиция списка Schema.org */
    startPosition: { type: Number, default: 0 },
    /** Связь ItemList с родительской Schema.org-сущностью */
    schemaProperty: { type: String, default: "" }
  },
  setup(__props) {
    const { t } = useI18n();
    const categoryLink = (category) => {
      return (category == null ? void 0 : category.url) ? route("public.marketCategories.show", { url: category.url }) : "#";
    };
    const getCategoryTitle = (category) => {
      var _a;
      return ((_a = category == null ? void 0 : category.translation) == null ? void 0 : _a.title) || "";
    };
    const getCategorySubtitle = (category) => {
      var _a;
      return ((_a = category == null ? void 0 : category.translation) == null ? void 0 : _a.subtitle) || "";
    };
    const getCategoryShort = (category) => {
      var _a;
      return ((_a = category == null ? void 0 : category.translation) == null ? void 0 : _a.short) || "";
    };
    const childrenCount = (category) => Number((category == null ? void 0 : category.children_count) ?? 0);
    const productsCount = (category) => Number((category == null ? void 0 : category.products_count) ?? 0);
    const hasSvgIcon = (category) => {
      return typeof (category == null ? void 0 : category.icon) === "string" && category.icon.trim().startsWith("<svg");
    };
    return (_ctx, _push, _parent, _attrs) => {
      _push(`<div${ssrRenderAttrs(mergeProps({
        class: "space-y-4",
        itemprop: __props.schemaProperty || void 0,
        itemscope: "",
        itemtype: "https://schema.org/ItemList"
      }, _attrs))}><meta itemprop="numberOfItems"${ssrRenderAttr("content", String(__props.categories.length))}><!--[-->`);
      ssrRenderList(__props.categories, (category, index) => {
        _push(`<article class="group rounded-md border border-gray-200 bg-white p-3 shadow-sm transition hover:shadow-md dark:border-gray-700 dark:bg-gray-900" itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem"><meta itemprop="position"${ssrRenderAttr("content", String(__props.startPosition + index + 1))}><div class="flex flex-col gap-3 sm:flex-row" itemprop="item" itemscope itemtype="https://schema.org/CollectionPage"><meta itemprop="url"${ssrRenderAttr("content", categoryLink(category))}><meta itemprop="name"${ssrRenderAttr("content", getCategoryTitle(category))}>`);
        if (getCategoryShort(category)) {
          _push(`<meta itemprop="description"${ssrRenderAttr("content", getCategoryShort(category))}>`);
        } else {
          _push(`<!---->`);
        }
        _push(`<div class="relative shrink-0">`);
        _push(ssrRenderComponent(unref(Link), {
          href: categoryLink(category),
          itemprop: "url"
        }, {
          default: withCtx((_, _push2, _parent2, _scopeId) => {
            if (_push2) {
              _push2(ssrRenderComponent(UniversalImageSlider, {
                entity: category,
                itemprop: "image",
                "height-class": "h-44",
                "rounded-class": "rounded-md",
                "wrapper-class": "w-full sm:w-60 border border-gray-400 dark:border-gray-600",
                "img-class": "w-full h-full object-cover transition duration-300 group-hover:scale-105"
              }, null, _parent2, _scopeId));
            } else {
              return [
                createVNode(UniversalImageSlider, {
                  entity: category,
                  itemprop: "image",
                  "height-class": "h-44",
                  "rounded-class": "rounded-md",
                  "wrapper-class": "w-full sm:w-60 border border-gray-400 dark:border-gray-600",
                  "img-class": "w-full h-full object-cover transition duration-300 group-hover:scale-105"
                }, null, 8, ["entity"])
              ];
            }
          }),
          _: 2
        }, _parent));
        _push(`</div><div class="flex min-w-0 flex-1 flex-col justify-around"><div class="min-w-0 flex-1"><div class="flex items-start justify-between gap-3">`);
        _push(ssrRenderComponent(unref(Link), {
          href: categoryLink(category),
          title: `${unref(t)("products")}: ${productsCount(category)}`,
          class: "inline-flex min-w-0 items-center gap-2",
          itemprop: "url"
        }, {
          default: withCtx((_, _push2, _parent2, _scopeId) => {
            if (_push2) {
              if (hasSvgIcon(category)) {
                _push2(`<span class="flex shrink-0"${_scopeId}>${category.icon ?? ""}</span>`);
              } else {
                _push2(`<!---->`);
              }
              _push2(`<span class="truncate text-lg font-semibold text-slate-900/85 group-hover:opacity-75 dark:text-slate-100/85"${_scopeId}>${ssrInterpolate(getCategoryTitle(category))}</span>`);
            } else {
              return [
                hasSvgIcon(category) ? (openBlock(), createBlock("span", {
                  key: 0,
                  class: "flex shrink-0",
                  innerHTML: category.icon
                }, null, 8, ["innerHTML"])) : createCommentVNode("", true),
                createVNode("span", { class: "truncate text-lg font-semibold text-slate-900/85 group-hover:opacity-75 dark:text-slate-100/85" }, toDisplayString(getCategoryTitle(category)), 1)
              ];
            }
          }),
          _: 2
        }, _parent));
        if (category.level) {
          _push(`<div class="shrink-0 rounded-sm border border-slate-400 px-2 py-1 text-xs font-semibold text-slate-500 dark:text-slate-400">${ssrInterpolate(unref(t)("level"))}: ${ssrInterpolate(category.level)}</div>`);
        } else {
          _push(`<!---->`);
        }
        _push(`</div>`);
        if (getCategorySubtitle(category)) {
          _push(`<div class="mt-1 text-xs font-semibold text-slate-500 dark:text-slate-400">${ssrInterpolate(getCategorySubtitle(category))}</div>`);
        } else {
          _push(`<!---->`);
        }
        if (getCategoryShort(category)) {
          _push(`<div class="mt-2 line-clamp-2 text-sm text-slate-700 dark:text-slate-300">${ssrInterpolate(getCategoryShort(category))}</div>`);
        } else {
          _push(`<!---->`);
        }
        _push(`</div><div class="mt-3 flex flex-wrap items-center justify-center gap-2 text-xs font-semibold text-slate-500 dark:text-slate-400">`);
        if (childrenCount(category)) {
          _push(`<div class="flex items-center justify-center gap-1 rounded-sm border border-slate-600 px-2 py-1 dark:border-slate-400"${ssrRenderAttr("title", unref(t)("subheadings"))}><svg class="h-3 w-3 shrink-0 text-fuchsia-600/85 dark:text-fuchsia-200/85" viewBox="0 0 24 24" fill="currentColor"><path d="M4 3a2 2 0 1 0 0 4 2 2 0 0 0 0-4Zm0 7a2 2 0 1 0 0 4 2 2 0 0 0 0-4Zm0 7a2 2 0 1 0 0 4 2 2 0 0 0 0-4Zm6-12h10v2H10V5Zm0 7h10v2H10v-2Zm0 7h10v2H10v-2Z"></path></svg> ${ssrInterpolate(unref(t)("subheadings"))}: ${ssrInterpolate(childrenCount(category))}</div>`);
        } else {
          _push(`<!---->`);
        }
        if (productsCount(category)) {
          _push(`<div class="flex items-center justify-center gap-1 rounded-sm border border-slate-600 px-2 py-1 dark:border-slate-400"${ssrRenderAttr("title", unref(t)("products"))}><svg class="h-3 w-3 shrink-0 text-sky-600/85 dark:text-sky-200/85" viewBox="0 0 24 24" fill="currentColor"><path d="M21 8.5 12 3 3 8.5V19l9 5 9-5V8.5ZM12 5.3l5.8 3.5-2.2 1.3L10 6.8 12 5.3Zm-3.8 2.6 5.8 3.5-2 1.2-5.8-3.5 2-1.2ZM5 10.6l6 3.6v7L5 17.8v-7.2Zm8 10.6v-7l6-3.6v7.2l-6 3.4Z"></path></svg> ${ssrInterpolate(unref(t)("products"))}: ${ssrInterpolate(productsCount(category))}</div>`);
        } else {
          _push(`<!---->`);
        }
        if (category.images_count) {
          _push(`<div class="flex items-center justify-center gap-1 rounded-sm border border-slate-600 px-2 py-1 dark:border-slate-400"${ssrRenderAttr("title", unref(t)("images"))}><svg class="h-3 w-3 shrink-0 text-emerald-600/85 dark:text-emerald-200/85" viewBox="0 0 24 24" fill="currentColor"><path d="M4 3h16a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2Zm0 2v11.2l4.2-4.2 3 3 2.8-2.8L20 18.2V5H4Zm3 2a2 2 0 1 0 0 4 2 2 0 0 0 0-4Z"></path></svg> ${ssrInterpolate(category.images_count)}</div>`);
        } else {
          _push(`<!---->`);
        }
        _push(`</div><div class="mt-3 flex items-center justify-end gap-3">`);
        _push(ssrRenderComponent(unref(Link), {
          href: categoryLink(category),
          class: "flex w-full items-center justify-center gap-2 rounded-sm px-3 py-1 btn-default sm:w-1/2",
          itemprop: "url"
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
        _push(`</div></div></div></article>`);
      });
      _push(`<!--]--></div>`);
    };
  }
};
const _sfc_setup = _sfc_main.setup;
_sfc_main.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Components/Public/Default/Market/MarketCategory/MarketCategoryRows.vue");
  return _sfc_setup ? _sfc_setup(props, ctx) : void 0;
};
export {
  _sfc_main$1 as _,
  _sfc_main as a
};
