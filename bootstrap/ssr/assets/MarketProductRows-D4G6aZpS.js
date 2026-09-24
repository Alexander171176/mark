import { computed, mergeProps, unref, withCtx, createVNode, toDisplayString, useSSRContext, createBlock, createCommentVNode, openBlock } from "vue";
import { ssrRenderAttrs, ssrRenderAttr, ssrRenderList, ssrRenderComponent, ssrInterpolate, ssrRenderClass } from "vue/server-renderer";
import { Link } from "@inertiajs/vue3";
import { useI18n } from "vue-i18n";
import { U as UniversalImageSlider } from "./UniversalImageSlider-Cu2Xndcn.js";
import { L as LikeButtonEntity } from "./LikeButtonEntity-ZC4HMEAO.js";
const _sfc_main$1 = {
  __name: "MarketProductGrid",
  __ssrInlineRender: true,
  props: {
    products: { type: Array, default: () => [] },
    cols: { type: Number, default: 2 },
    /** Начальная позиция списка Schema.org */
    startPosition: { type: Number, default: 0 },
    /** Полное количество элементов списка Schema.org */
    totalItems: { type: Number, default: null },
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
    const productLink = (product) => {
      return (product == null ? void 0 : product.url) ? route("public.marketProducts.show", { url: product.url }) : "#";
    };
    const getProductTitle = (product) => {
      var _a;
      return ((_a = product == null ? void 0 : product.translation) == null ? void 0 : _a.title) || "";
    };
    const getProductShort = (product) => {
      var _a;
      return ((_a = product == null ? void 0 : product.translation) == null ? void 0 : _a.short) || "";
    };
    const getBrandTitle = (product) => {
      var _a, _b;
      return ((_b = (_a = product == null ? void 0 : product.brand) == null ? void 0 : _a.translation) == null ? void 0 : _b.title) || "";
    };
    const brandLink = (product) => {
      var _a;
      return ((_a = product == null ? void 0 : product.brand) == null ? void 0 : _a.url) ? route("public.marketBrands.show", { url: product.brand.url }) : null;
    };
    const getPrice = (product) => {
      const value = Number(product == null ? void 0 : product.price);
      return Number.isFinite(value) ? value : 0;
    };
    const getOldPrice = (product) => {
      const value = Number(product == null ? void 0 : product.old_price);
      return Number.isFinite(value) ? value : 0;
    };
    const hasOldPrice = (product) => getOldPrice(product) > getPrice(product);
    const getCurrency = (product) => {
      var _a, _b, _c;
      return ((_a = product == null ? void 0 : product.currency) == null ? void 0 : _a.symbol) || ((_b = product == null ? void 0 : product.currency) == null ? void 0 : _b.sign) || ((_c = product == null ? void 0 : product.currency) == null ? void 0 : _c.code) || "";
    };
    const getCurrencyCode = (product) => {
      var _a;
      return ((_a = product == null ? void 0 : product.currency) == null ? void 0 : _a.code) || "";
    };
    const getProductImage = (product) => {
      var _a;
      const images = Array.isArray(product == null ? void 0 : product.images) ? product.images : Array.isArray((_a = product == null ? void 0 : product.images) == null ? void 0 : _a.data) ? product.images.data : [];
      const image = images[0];
      return (image == null ? void 0 : image.webp_url) || (image == null ? void 0 : image.image_url) || (image == null ? void 0 : image.thumb_url) || (image == null ? void 0 : image.url) || "";
    };
    const productAbsoluteUrl = (product) => {
      return (product == null ? void 0 : product.url) ? route("public.marketProducts.show", { url: product.url }) : "";
    };
    const productAvailability = (product) => {
      return hasStock(product) ? "https://schema.org/InStock" : "https://schema.org/OutOfStock";
    };
    const ratingCount = (product) => Number((product == null ? void 0 : product.rating_count) ?? 0);
    const hasAggregateRating = (product) => {
      return rating(product) > 0 && ratingCount(product) > 0;
    };
    const formatPrice = (value) => {
      const number = Number(value);
      if (!Number.isFinite(number)) {
        return "0";
      }
      return new Intl.NumberFormat("ru-RU", {
        minimumFractionDigits: 0,
        maximumFractionDigits: 2
      }).format(number);
    };
    const hasStock = (product) => {
      return Boolean(
        (product == null ? void 0 : product.has_stock) ?? ((product == null ? void 0 : product.in_stock) && Number((product == null ? void 0 : product.quantity) ?? 0) > 0)
      );
    };
    const quantity = (product) => Number((product == null ? void 0 : product.quantity) ?? 0);
    const rating = (product) => {
      const value = Number((product == null ? void 0 : product.rating_avg) ?? 0);
      return Number.isFinite(value) ? value : 0;
    };
    const reviewsCount = (product) => Number((product == null ? void 0 : product.reviews_count) ?? 0);
    const hasMarketingFlags = (product) => {
      return Boolean(
        (product == null ? void 0 : product.is_new) || (product == null ? void 0 : product.is_hit) || (product == null ? void 0 : product.is_sale)
      );
    };
    return (_ctx, _push, _parent, _attrs) => {
      _push(`<div${ssrRenderAttrs(mergeProps({
        class: ["grid gap-4", gridClass.value],
        itemprop: __props.schemaProperty || void 0,
        itemscope: "",
        itemtype: "https://schema.org/ItemList"
      }, _attrs))}><meta itemprop="numberOfItems"${ssrRenderAttr("content", String(__props.totalItems ?? __props.products.length))}><!--[-->`);
      ssrRenderList(__props.products, (product, index) => {
        _push(`<article class="group flex h-full flex-col overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm transition hover:-translate-y-0.5 hover:shadow-md dark:border-gray-700 dark:bg-gray-900" itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem"><meta itemprop="position"${ssrRenderAttr("content", String(__props.startPosition + index + 1))}><div class="flex h-full flex-col" itemprop="item" itemscope itemtype="https://schema.org/Product">`);
        if (getProductImage(product)) {
          _push(`<meta itemprop="image"${ssrRenderAttr("content", getProductImage(product))}>`);
        } else {
          _push(`<!---->`);
        }
        _push(`<div class="relative">`);
        _push(ssrRenderComponent(unref(Link), {
          href: productLink(product),
          itemprop: "url"
        }, {
          default: withCtx((_, _push2, _parent2, _scopeId) => {
            if (_push2) {
              _push2(ssrRenderComponent(UniversalImageSlider, {
                entity: product,
                "height-class": "h-48",
                "rounded-class": "",
                "wrapper-class": "",
                "img-class": "w-full h-full object-cover transition duration-300 group-hover:scale-105"
              }, null, _parent2, _scopeId));
            } else {
              return [
                createVNode(UniversalImageSlider, {
                  entity: product,
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
        if (hasMarketingFlags(product)) {
          _push(`<div class="absolute left-2 top-2 z-10 flex flex-wrap gap-1">`);
          if (product.is_new) {
            _push(`<span class="rounded-sm bg-emerald-600/70 px-1 py-0.5 text-[9px] font-bold uppercase tracking-wide text-white shadow-sm"> NEW </span>`);
          } else {
            _push(`<!---->`);
          }
          if (product.is_hit) {
            _push(`<span class="rounded-sm bg-amber-500/70 px-1 py-0.5 text-[9px] font-bold uppercase tracking-wide text-white shadow-sm"> HIT </span>`);
          } else {
            _push(`<!---->`);
          }
          if (product.is_sale) {
            _push(`<span class="rounded-sm bg-red-500/70 px-1 py-0.5 text-[9px] font-bold uppercase tracking-wide text-white shadow-sm"> SALE </span>`);
          } else {
            _push(`<!---->`);
          }
          _push(`</div>`);
        } else {
          _push(`<!---->`);
        }
        _push(`</div><div class="flex flex-1 flex-col pt-6 pb-1.5 px-1">`);
        if (getBrandTitle(product)) {
          _push(`<div class="mb-0.5 flex items-center justify-center" itemprop="brand" itemscope itemtype="https://schema.org/Brand">`);
          if (brandLink(product)) {
            _push(ssrRenderComponent(unref(Link), {
              href: brandLink(product),
              itemprop: "url",
              class: "text-xs font-semibold uppercase tracking-wide text-slate-500 transition hover:text-blue-600 hover:underline dark:text-slate-400 dark:hover:text-blue-400"
            }, {
              default: withCtx((_, _push2, _parent2, _scopeId) => {
                if (_push2) {
                  _push2(`<span itemprop="name"${_scopeId}>${ssrInterpolate(getBrandTitle(product))}</span>`);
                } else {
                  return [
                    createVNode("span", { itemprop: "name" }, toDisplayString(getBrandTitle(product)), 1)
                  ];
                }
              }),
              _: 2
            }, _parent));
          } else {
            _push(`<span itemprop="name" class="text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">${ssrInterpolate(getBrandTitle(product))}</span>`);
          }
          _push(`</div>`);
        } else {
          _push(`<!---->`);
        }
        _push(`<div class="flex items-center justify-center text-center">`);
        _push(ssrRenderComponent(unref(Link), {
          href: productLink(product),
          title: getProductShort(product),
          class: "inline-flex items-center gap-2"
        }, {
          default: withCtx((_, _push2, _parent2, _scopeId) => {
            if (_push2) {
              _push2(`<span itemprop="name" class="text-xs font-semibold text-slate-900/85 group-hover:opacity-75 dark:text-slate-100/85 dark:group-hover:opacity-75"${_scopeId}>${ssrInterpolate(getProductTitle(product))}</span>`);
            } else {
              return [
                createVNode("span", {
                  itemprop: "name",
                  class: "text-xs font-semibold text-slate-900/85 group-hover:opacity-75 dark:text-slate-100/85 dark:group-hover:opacity-75"
                }, toDisplayString(getProductTitle(product)), 1)
              ];
            }
          }),
          _: 2
        }, _parent));
        _push(`</div>`);
        if (getProductShort(product)) {
          _push(`<meta itemprop="description"${ssrRenderAttr("content", getProductShort(product))}>`);
        } else {
          _push(`<!---->`);
        }
        if (product.sku) {
          _push(`<meta itemprop="sku"${ssrRenderAttr("content", product.sku)}>`);
        } else {
          _push(`<!---->`);
        }
        _push(`<div itemprop="offers" itemscope itemtype="https://schema.org/Offer"><meta itemprop="url"${ssrRenderAttr("content", productAbsoluteUrl(product))}><meta itemprop="price"${ssrRenderAttr("content", String(getPrice(product)))}>`);
        if (getCurrencyCode(product)) {
          _push(`<meta itemprop="priceCurrency"${ssrRenderAttr("content", getCurrencyCode(product))}>`);
        } else {
          _push(`<!---->`);
        }
        _push(`<link itemprop="availability"${ssrRenderAttr("href", productAvailability(product))}><div class="mt-1 flex flex-col items-center justify-center gap-0.1"><span class="text-sm font-bold text-teal-600 dark:text-teal-400">${ssrInterpolate(formatPrice(getPrice(product)))} ${ssrInterpolate(getCurrency(product))}</span>`);
        if (hasOldPrice(product)) {
          _push(`<span class="text-xs font-semibold text-slate-400 line-through dark:text-slate-500">${ssrInterpolate(formatPrice(getOldPrice(product)))} ${ssrInterpolate(getCurrency(product))}</span>`);
        } else {
          _push(`<!---->`);
        }
        _push(`</div><div class="flex items-center justify-center gap-1 px-2 py-0 text-[10px] font-semibold text-gray-500 dark:text-gray-400"${ssrRenderAttr("title", unref(t)("availability"))}><svg viewBox="0 0 24 24" fill="currentColor" class="${ssrRenderClass([hasStock(product) ? "text-emerald-600 dark:text-emerald-300" : "text-red-500 dark:text-red-300", "h-3 w-3 shrink-0"])}">`);
        if (hasStock(product)) {
          _push(`<path d="M9 16.17 4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41L9 16.17Z"></path>`);
        } else {
          _push(`<path d="M18.3 5.71 12 12l6.3 6.29-1.41 1.42L10.59 13.41 4.29 19.71 2.88 18.3 9.17 12 2.88 5.71 4.29 4.29 10.59 10.59 16.89 4.29 18.3 5.71Z"></path>`);
        }
        _push(`</svg><span>${ssrInterpolate(hasStock(product) ? unref(t)("remainder") : unref(t)("outOfStock"))}</span>`);
        if (hasStock(product) && quantity(product) > 0) {
          _push(`<span> [${ssrInterpolate(quantity(product))}] </span>`);
        } else {
          _push(`<!---->`);
        }
        _push(`</div></div><div class="mt-1 flex items-center justify-center">`);
        _push(ssrRenderComponent(LikeButtonEntity, {
          "likes-count": product.likes_count || 0,
          "already-liked": product.already_liked || false,
          "route-name": "public.marketProducts.like",
          "route-params": { id: product.id },
          "icon-class": "w-3 h-3 hover:scale-110 active:scale-95"
        }, null, _parent));
        _push(`</div><div class="mt-2 flex flex-wrap items-center justify-center gap-2 text-xs font-semibold text-slate-500 dark:text-slate-400">`);
        if (product.variants_count) {
          _push(`<div class="flex items-center justify-center gap-1 rounded-sm border border-slate-400 px-2 py-1"${ssrRenderAttr("title", unref(t)("variants"))}><svg class="h-3 w-3 shrink-0 text-violet-600 dark:text-violet-300" viewBox="0 0 24 24" fill="currentColor"><path d="M4 4h6v6H4V4Zm10 0h6v6h-6V4ZM4 14h6v6H4v-6Zm10 0h6v6h-6v-6Z"></path></svg> ${ssrInterpolate(product.variants_count)}</div>`);
        } else {
          _push(`<!---->`);
        }
        if (product.views > 0) {
          _push(`<div class="flex items-center justify-center gap-1 rounded-sm border border-slate-400 px-2 py-1"${ssrRenderAttr("title", unref(t)("views"))}><svg class="h-3 w-3 text-blue-600/85 dark:text-blue-200/85" viewBox="0 0 576 512" fill="currentColor"><path d="M569.354 231.631C512.97 135.949 407.81 72 288 72 168.14 72 63.004 135.994 6.646 231.631a47.999 47.999 0 0 0 0 48.739C63.031 376.051 168.19 440 288 440c119.86 0 224.996-63.994 281.354-159.631a47.997 47.997 0 0 0 0-48.738zM288 392c-102.556 0-192.091-54.701-240-136 44.157-74.933 123.677-127.27 216.162-135.007C273.958 131.078 280 144.83 280 160c0 30.928-25.072 56-56 56s-56-25.072-56-56l.001-.042C157.794 179.043 152 200.844 152 224c0 75.111 60.889 136 136 136s136-60.889 136-136c0-31.031-10.4-59.629-27.895-82.515C451.704 164.638 498.009 205.106 528 256c-47.908 81.299-137.444 136-240 136z"></path></svg> ${ssrInterpolate(product.views)}</div>`);
        } else {
          _push(`<!---->`);
        }
        if (hasAggregateRating(product)) {
          _push(`<div itemprop="aggregateRating" itemscope itemtype="https://schema.org/AggregateRating" class="hidden"><meta itemprop="ratingValue"${ssrRenderAttr("content", String(rating(product)))}><meta itemprop="ratingCount"${ssrRenderAttr("content", String(ratingCount(product)))}><meta itemprop="bestRating" content="5"><meta itemprop="worstRating" content="1">`);
          if (reviewsCount(product) > 0) {
            _push(`<meta itemprop="reviewCount"${ssrRenderAttr("content", String(reviewsCount(product)))}>`);
          } else {
            _push(`<!---->`);
          }
          _push(`</div>`);
        } else {
          _push(`<!---->`);
        }
        if (rating(product) > 0) {
          _push(`<div class="flex items-center justify-center gap-1 rounded-sm border border-slate-400 px-2 py-1"${ssrRenderAttr("title", unref(t)("rating"))}><svg viewBox="0 0 24 24" class="h-3 w-3 shrink-0"><path class="fill-current text-amber-500 dark:text-amber-300" d="M12.746,1.464l3.11,6.3L22.81,8.776a.831.831,0,0,1,.461,1.418l-5.033,4.9,1.188,6.926a.832.832,0,0,1-1.207.877L12,19.632,5.78,22.9a.833.833,0,0,1-1.207-.878L5.761,15.1l-5.033-4.9a.831.831,0,0,1,.461-1.418L8.143,7.765l3.11-6.3A.833.833,0,0,1,12.746,1.464Z"></path></svg> ${ssrInterpolate(rating(product).toFixed(1))}</div>`);
        } else {
          _push(`<!---->`);
        }
        if (reviewsCount(product) > 0) {
          _push(`<div class="flex items-center justify-center gap-1 rounded-sm border border-slate-400 px-2 py-1"${ssrRenderAttr("title", unref(t)("reviews"))}><svg class="h-3 w-3 shrink-0 text-sky-600 dark:text-sky-300" viewBox="0 0 24 24" fill="currentColor"><path d="M4 3h16a2 2 0 0 1 2 2v11a2 2 0 0 1-2 2H9l-5 4v-4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2Zm2 5v2h12V8H6Zm0 4v2h8v-2H6Z"></path></svg> ${ssrInterpolate(reviewsCount(product))}</div>`);
        } else {
          _push(`<!---->`);
        }
        _push(`</div></div></div></article>`);
      });
      _push(`<!--]--></div>`);
    };
  }
};
const _sfc_setup$1 = _sfc_main$1.setup;
_sfc_main$1.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Components/Public/Default/Market/MarketProduct/MarketProductGrid.vue");
  return _sfc_setup$1 ? _sfc_setup$1(props, ctx) : void 0;
};
const _sfc_main = {
  __name: "MarketProductRows",
  __ssrInlineRender: true,
  props: {
    products: { type: Array, default: () => [] },
    /** Начальная позиция списка Schema.org */
    startPosition: { type: Number, default: 0 },
    /** Полное количество элементов списка Schema.org */
    totalItems: { type: Number, default: null },
    /** Связь ItemList с родительской Schema.org-сущностью */
    schemaProperty: { type: String, default: "" }
  },
  setup(__props) {
    const { t } = useI18n();
    const productLink = (product) => {
      return (product == null ? void 0 : product.url) ? route("public.marketProducts.show", { url: product.url }) : "#";
    };
    const getProductTitle = (product) => {
      var _a;
      return ((_a = product == null ? void 0 : product.translation) == null ? void 0 : _a.title) || "";
    };
    const getProductShort = (product) => {
      var _a;
      return ((_a = product == null ? void 0 : product.translation) == null ? void 0 : _a.short) || "";
    };
    const getBrandTitle = (product) => {
      var _a, _b;
      return ((_b = (_a = product == null ? void 0 : product.brand) == null ? void 0 : _a.translation) == null ? void 0 : _b.title) || "";
    };
    const brandLink = (product) => {
      var _a;
      return ((_a = product == null ? void 0 : product.brand) == null ? void 0 : _a.url) ? route("public.marketBrands.show", { url: product.brand.url }) : null;
    };
    const getPrice = (product) => {
      const value = Number(product == null ? void 0 : product.price);
      return Number.isFinite(value) ? value : 0;
    };
    const getOldPrice = (product) => {
      const value = Number(product == null ? void 0 : product.old_price);
      return Number.isFinite(value) ? value : 0;
    };
    const hasOldPrice = (product) => getOldPrice(product) > getPrice(product);
    const getCurrency = (product) => {
      var _a, _b, _c;
      return ((_a = product == null ? void 0 : product.currency) == null ? void 0 : _a.symbol) || ((_b = product == null ? void 0 : product.currency) == null ? void 0 : _b.sign) || ((_c = product == null ? void 0 : product.currency) == null ? void 0 : _c.code) || "";
    };
    const getCurrencyCode = (product) => {
      var _a;
      return ((_a = product == null ? void 0 : product.currency) == null ? void 0 : _a.code) || "";
    };
    const getProductImage = (product) => {
      var _a;
      const images = Array.isArray(product == null ? void 0 : product.images) ? product.images : Array.isArray((_a = product == null ? void 0 : product.images) == null ? void 0 : _a.data) ? product.images.data : [];
      const image = images[0];
      return (image == null ? void 0 : image.webp_url) || (image == null ? void 0 : image.image_url) || (image == null ? void 0 : image.thumb_url) || (image == null ? void 0 : image.url) || "";
    };
    const productAbsoluteUrl = (product) => {
      return (product == null ? void 0 : product.url) ? route("public.marketProducts.show", { url: product.url }) : "";
    };
    const productAvailability = (product) => {
      return hasStock(product) ? "https://schema.org/InStock" : "https://schema.org/OutOfStock";
    };
    const ratingCount = (product) => Number((product == null ? void 0 : product.rating_count) ?? 0);
    const hasAggregateRating = (product) => {
      return rating(product) > 0 && ratingCount(product) > 0;
    };
    const formatPrice = (value) => {
      const number = Number(value);
      if (!Number.isFinite(number)) {
        return "0";
      }
      return new Intl.NumberFormat("ru-RU", {
        minimumFractionDigits: 0,
        maximumFractionDigits: 2
      }).format(number);
    };
    const hasStock = (product) => {
      return Boolean(
        (product == null ? void 0 : product.has_stock) ?? ((product == null ? void 0 : product.in_stock) && Number((product == null ? void 0 : product.quantity) ?? 0) > 0)
      );
    };
    const quantity = (product) => Number((product == null ? void 0 : product.quantity) ?? 0);
    const rating = (product) => {
      const value = Number((product == null ? void 0 : product.rating_avg) ?? 0);
      return Number.isFinite(value) ? value : 0;
    };
    const reviewsCount = (product) => Number((product == null ? void 0 : product.reviews_count) ?? 0);
    const hasMarketingFlags = (product) => {
      return Boolean(
        (product == null ? void 0 : product.is_new) || (product == null ? void 0 : product.is_hit) || (product == null ? void 0 : product.is_sale)
      );
    };
    return (_ctx, _push, _parent, _attrs) => {
      _push(`<div${ssrRenderAttrs(mergeProps({
        class: "space-y-4",
        itemprop: __props.schemaProperty || void 0,
        itemscope: "",
        itemtype: "https://schema.org/ItemList"
      }, _attrs))}><meta itemprop="numberOfItems"${ssrRenderAttr("content", String(__props.totalItems ?? __props.products.length))}><!--[-->`);
      ssrRenderList(__props.products, (product, index) => {
        _push(`<article class="group rounded-2xl border border-gray-200 bg-white shadow-sm transition hover:shadow-md dark:border-gray-700 dark:bg-gray-900" itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem"><meta itemprop="position"${ssrRenderAttr("content", String(__props.startPosition + index + 1))}><div class="flex flex-col gap-3 p-3 sm:flex-row" itemprop="item" itemscope itemtype="https://schema.org/Product"><div class="relative shrink-0">`);
        _push(ssrRenderComponent(unref(Link), {
          href: productLink(product),
          itemprop: "url"
        }, {
          default: withCtx((_, _push2, _parent2, _scopeId) => {
            if (_push2) {
              if (getProductImage(product)) {
                _push2(`<meta itemprop="image"${ssrRenderAttr("content", getProductImage(product))}${_scopeId}>`);
              } else {
                _push2(`<!---->`);
              }
              _push2(ssrRenderComponent(UniversalImageSlider, {
                entity: product,
                "height-class": "h-44",
                "rounded-class": "rounded-md",
                "wrapper-class": "w-full sm:w-60 border border-gray-400 dark:border-gray-600",
                "img-class": "w-full h-full object-cover transition duration-300 group-hover:scale-105"
              }, null, _parent2, _scopeId));
            } else {
              return [
                getProductImage(product) ? (openBlock(), createBlock("meta", {
                  key: 0,
                  itemprop: "image",
                  content: getProductImage(product)
                }, null, 8, ["content"])) : createCommentVNode("", true),
                createVNode(UniversalImageSlider, {
                  entity: product,
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
        if (hasMarketingFlags(product)) {
          _push(`<div class="absolute left-2 top-2 z-10 flex flex-wrap gap-1">`);
          if (product.is_new) {
            _push(`<span class="rounded-sm bg-emerald-600/70 px-1 py-0.5 text-[9px] font-bold uppercase tracking-wide text-white shadow-sm"> NEW </span>`);
          } else {
            _push(`<!---->`);
          }
          if (product.is_hit) {
            _push(`<span class="rounded-sm bg-amber-500/70 px-1 py-0.5 text-[9px] font-bold uppercase tracking-wide text-white shadow-sm"> HIT </span>`);
          } else {
            _push(`<!---->`);
          }
          if (product.is_sale) {
            _push(`<span class="rounded-sm bg-red-500/70 px-1 py-0.5 text-[9px] font-bold uppercase tracking-wide text-white shadow-sm"> SALE </span>`);
          } else {
            _push(`<!---->`);
          }
          _push(`</div>`);
        } else {
          _push(`<!---->`);
        }
        _push(`</div><div class="min-w-0 flex-1 flex flex-col justify-around ml-7"><div class="flex flex-wrap items-start justify-between gap-3"><div class="min-w-0 flex-1">`);
        if (getBrandTitle(product)) {
          _push(`<div class="mb-1 text-xs font-semibold uppercase tracking-wide" itemprop="brand" itemscope itemtype="https://schema.org/Brand">`);
          if (brandLink(product)) {
            _push(ssrRenderComponent(unref(Link), {
              href: brandLink(product),
              itemprop: "url",
              class: "text-slate-500 transition hover:text-blue-600 hover:underline dark:text-slate-400 dark:hover:text-blue-400"
            }, {
              default: withCtx((_, _push2, _parent2, _scopeId) => {
                if (_push2) {
                  _push2(`<span itemprop="name"${_scopeId}>${ssrInterpolate(getBrandTitle(product))}</span>`);
                } else {
                  return [
                    createVNode("span", { itemprop: "name" }, toDisplayString(getBrandTitle(product)), 1)
                  ];
                }
              }),
              _: 2
            }, _parent));
          } else {
            _push(`<span itemprop="name" class="text-slate-500 dark:text-slate-400">${ssrInterpolate(getBrandTitle(product))}</span>`);
          }
          _push(`</div>`);
        } else {
          _push(`<!---->`);
        }
        _push(ssrRenderComponent(unref(Link), {
          href: productLink(product),
          class: "inline-flex min-w-0 items-center gap-2"
        }, {
          default: withCtx((_, _push2, _parent2, _scopeId) => {
            if (_push2) {
              _push2(`<span itemprop="name" class="text-lg font-semibold text-slate-900/85 group-hover:opacity-75 dark:text-slate-100/85"${_scopeId}>${ssrInterpolate(getProductTitle(product))}</span>`);
            } else {
              return [
                createVNode("span", {
                  itemprop: "name",
                  class: "text-lg font-semibold text-slate-900/85 group-hover:opacity-75 dark:text-slate-100/85"
                }, toDisplayString(getProductTitle(product)), 1)
              ];
            }
          }),
          _: 2
        }, _parent));
        _push(`</div><div itemprop="offers" itemscope itemtype="https://schema.org/Offer"><meta itemprop="url"${ssrRenderAttr("content", productAbsoluteUrl(product))}><meta itemprop="price"${ssrRenderAttr("content", String(getPrice(product)))}>`);
        if (getCurrencyCode(product)) {
          _push(`<meta itemprop="priceCurrency"${ssrRenderAttr("content", getCurrencyCode(product))}>`);
        } else {
          _push(`<!---->`);
        }
        _push(`<link itemprop="availability"${ssrRenderAttr("href", productAvailability(product))}><div class="shrink-0 text-right"><div class="text-lg font-bold text-teal-600 dark:text-teal-400">${ssrInterpolate(formatPrice(getPrice(product)))} ${ssrInterpolate(getCurrency(product))}</div>`);
        if (hasOldPrice(product)) {
          _push(`<div class="text-sm font-semibold text-slate-400 line-through dark:text-slate-500">${ssrInterpolate(formatPrice(getOldPrice(product)))} ${ssrInterpolate(getCurrency(product))}</div>`);
        } else {
          _push(`<!---->`);
        }
        _push(`<div class="flex items-center justify-center gap-1 mt-1 text-[10px] font-semibold text-gray-500 dark:text-gray-400"${ssrRenderAttr("title", unref(t)("availability"))}><svg viewBox="0 0 24 24" fill="currentColor" class="${ssrRenderClass([hasStock(product) ? "text-emerald-600 dark:text-emerald-300" : "text-red-500 dark:text-red-300", "h-3 w-3 shrink-0"])}">`);
        if (hasStock(product)) {
          _push(`<path d="M9 16.17 4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41L9 16.17Z"></path>`);
        } else {
          _push(`<path d="M18.3 5.71 12 12l6.3 6.29-1.41 1.42L10.59 13.41 4.29 19.71 2.88 18.3 9.17 12 2.88 5.71 4.29 4.29 10.59 10.59 16.89 4.29 18.3 5.71Z"></path>`);
        }
        _push(`</svg><span>${ssrInterpolate(hasStock(product) ? unref(t)("remainder") : unref(t)("outOfStock"))}</span>`);
        if (hasStock(product) && quantity(product) > 0) {
          _push(`<span> [${ssrInterpolate(quantity(product))}] </span>`);
        } else {
          _push(`<!---->`);
        }
        _push(`</div></div></div></div>`);
        if (getProductShort(product)) {
          _push(`<div itemprop="description" class="mt-2 text-sm text-slate-700 dark:text-slate-300">${ssrInterpolate(getProductShort(product))}</div>`);
        } else {
          _push(`<!---->`);
        }
        if (product.sku || product.vendor_code) {
          _push(`<div class="mt-2 flex flex-wrap items-center gap-2 text-[10px] text-slate-400 dark:text-slate-500">`);
          if (product.sku) {
            _push(`<span> SKU: <span itemprop="sku">${ssrInterpolate(product.sku)}</span></span>`);
          } else {
            _push(`<!---->`);
          }
          if (product.vendor_code) {
            _push(`<span>${ssrInterpolate(product.vendor_code)}</span>`);
          } else {
            _push(`<!---->`);
          }
          _push(`</div>`);
        } else {
          _push(`<!---->`);
        }
        _push(`<div class="flex items-center justify-between gap-2"><div class="mt-3 flex flex-wrap items-center justify-center gap-2 text-xs font-semibold text-slate-500 dark:text-slate-400">`);
        if (product.variants_count) {
          _push(`<div class="flex items-center justify-center gap-1 rounded-sm border border-slate-400 px-2 py-1"${ssrRenderAttr("title", unref(t)("variants"))}><svg class="h-3 w-3 shrink-0 text-violet-600 dark:text-violet-300" viewBox="0 0 24 24" fill="currentColor"><path d="M4 4h6v6H4V4Zm10 0h6v6h-6V4ZM4 14h6v6H4v-6Zm10 0h6v6h-6v-6Z"></path></svg> ${ssrInterpolate(product.variants_count)}</div>`);
        } else {
          _push(`<!---->`);
        }
        if (product.views > 0) {
          _push(`<div class="flex items-center justify-center gap-1 rounded-sm border border-slate-400 px-2 py-1"${ssrRenderAttr("title", unref(t)("views"))}><svg class="h-3 w-3 text-blue-600/85 dark:text-blue-200/85" viewBox="0 0 576 512" fill="currentColor"><path d="M569.354 231.631C512.97 135.949 407.81 72 288 72 168.14 72 63.004 135.994 6.646 231.631a47.999 47.999 0 0 0 0 48.739C63.031 376.051 168.19 440 288 440c119.86 0 224.996-63.994 281.354-159.631a47.997 47.997 0 0 0 0-48.738zM288 392c-102.556 0-192.091-54.701-240-136 44.157-74.933 123.677-127.27 216.162-135.007C273.958 131.078 280 144.83 280 160c0 30.928-25.072 56-56 56s-56-25.072-56-56l.001-.042C157.794 179.043 152 200.844 152 224c0 75.111 60.889 136 136 136s136-60.889 136-136c0-31.031-10.4-59.629-27.895-82.515C451.704 164.638 498.009 205.106 528 256c-47.908 81.299-137.444 136-240 136z"></path></svg> ${ssrInterpolate(product.views)}</div>`);
        } else {
          _push(`<!---->`);
        }
        if (hasAggregateRating(product)) {
          _push(`<div itemprop="aggregateRating" itemscope itemtype="https://schema.org/AggregateRating" class="hidden"><meta itemprop="ratingValue"${ssrRenderAttr("content", String(rating(product)))}><meta itemprop="ratingCount"${ssrRenderAttr("content", String(ratingCount(product)))}><meta itemprop="bestRating" content="5"><meta itemprop="worstRating" content="1">`);
          if (reviewsCount(product) > 0) {
            _push(`<meta itemprop="reviewCount"${ssrRenderAttr("content", String(reviewsCount(product)))}>`);
          } else {
            _push(`<!---->`);
          }
          _push(`</div>`);
        } else {
          _push(`<!---->`);
        }
        if (rating(product) > 0) {
          _push(`<div class="flex items-center justify-center gap-1 rounded-sm border border-slate-400 px-2 py-1"${ssrRenderAttr("title", unref(t)("rating"))}><svg viewBox="0 0 24 24" class="h-3 w-3 shrink-0"><path class="fill-current text-amber-500 dark:text-amber-300" d="M12.746,1.464l3.11,6.3L22.81,8.776a.831.831,0,0,1,.461,1.418l-5.033,4.9,1.188,6.926a.832.832,0,0,1-1.207.877L12,19.632,5.78,22.9a.833.833,0,0,1-1.207-.878L5.761,15.1l-5.033-4.9a.831.831,0,0,1,.461-1.418L8.143,7.765l3.11-6.3A.833.833,0,0,1,12.746,1.464Z"></path></svg> ${ssrInterpolate(rating(product).toFixed(1))}</div>`);
        } else {
          _push(`<!---->`);
        }
        if (reviewsCount(product) > 0) {
          _push(`<div class="flex items-center justify-center gap-1 rounded-sm border border-slate-400 px-2 py-1"${ssrRenderAttr("title", unref(t)("reviews"))}><svg class="h-3 w-3 shrink-0 text-sky-600 dark:text-sky-300" viewBox="0 0 24 24" fill="currentColor"><path d="M4 3h16a2 2 0 0 1 2 2v11a2 2 0 0 1-2 2H9l-5 4v-4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2Zm2 5v2h12V8H6Zm0 4v2h8v-2H6Z"></path></svg> ${ssrInterpolate(reviewsCount(product))}</div>`);
        } else {
          _push(`<!---->`);
        }
        _push(`<div class="mt-1 flex items-center justify-center">`);
        _push(ssrRenderComponent(LikeButtonEntity, {
          "likes-count": product.likes_count || 0,
          "already-liked": product.already_liked || false,
          "route-name": "public.marketProducts.like",
          "route-params": { id: product.id },
          "icon-class": "w-3 h-3 hover:scale-110 active:scale-95"
        }, null, _parent));
        _push(`</div></div>`);
        _push(ssrRenderComponent(unref(Link), {
          href: productLink(product),
          class: "flex w-fit items-center justify-center gap-2 rounded-sm px-3 py-1 btn-default"
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
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Components/Public/Default/Market/MarketProduct/MarketProductRows.vue");
  return _sfc_setup ? _sfc_setup(props, ctx) : void 0;
};
export {
  _sfc_main$1 as _,
  _sfc_main as a
};
