import { ref, computed, onMounted, nextTick, onBeforeUnmount, mergeProps, unref, withCtx, createVNode, toDisplayString, useSSRContext } from "vue";
import { ssrRenderAttrs, ssrRenderAttr, ssrInterpolate, ssrIncludeBooleanAttr, ssrRenderList, ssrRenderComponent, ssrRenderClass } from "vue/server-renderer";
import { Link, usePage } from "@inertiajs/vue3";
import { useI18n } from "vue-i18n";
import { U as UniversalImageSlider } from "./UniversalImageSlider-Cu2Xndcn.js";
import { L as LikeButtonEntity } from "./LikeButtonEntity-ZC4HMEAO.js";
import axios from "axios";
const _sfc_main = {
  __name: "MarketRecentlyViewedProducts",
  __ssrInlineRender: true,
  props: {
    products: {
      type: Array,
      default: () => []
    }
  },
  setup(__props) {
    const { t, locale } = useI18n();
    const sliderRef = ref(null);
    const canScrollLeft = ref(false);
    const canScrollRight = ref(false);
    const updateScrollState = () => {
      const slider = sliderRef.value;
      if (!slider) {
        canScrollLeft.value = false;
        canScrollRight.value = false;
        return;
      }
      const maxScrollLeft = slider.scrollWidth - slider.clientWidth;
      canScrollLeft.value = slider.scrollLeft > 2;
      canScrollRight.value = maxScrollLeft > 2 && slider.scrollLeft < maxScrollLeft - 2;
    };
    const scrollAmount = computed(() => {
      const slider = sliderRef.value;
      if (!slider) {
        return 300;
      }
      return Math.max(
        240,
        slider.clientWidth * 0.8
      );
    });
    const scrollPrev = () => {
      const slider = sliderRef.value;
      if (!slider) {
        return;
      }
      const maxScrollLeft = slider.scrollWidth - slider.clientWidth;
      if (!canScrollLeft.value) {
        slider.scrollTo({
          left: maxScrollLeft,
          behavior: "smooth"
        });
        return;
      }
      slider.scrollBy({
        left: -scrollAmount.value,
        behavior: "smooth"
      });
    };
    const scrollNext = () => {
      const slider = sliderRef.value;
      if (!slider) {
        return;
      }
      if (!canScrollRight.value) {
        slider.scrollTo({
          left: 0,
          behavior: "smooth"
        });
        return;
      }
      slider.scrollBy({
        left: scrollAmount.value,
        behavior: "smooth"
      });
    };
    let wheelLocked = false;
    const handleWheel = (event) => {
      const slider = sliderRef.value;
      if (!slider) {
        return;
      }
      const maxScrollLeft = slider.scrollWidth - slider.clientWidth;
      if (maxScrollLeft <= 0) {
        return;
      }
      const delta = Math.abs(event.deltaY) >= Math.abs(event.deltaX) ? event.deltaY : event.deltaX;
      if (Math.abs(delta) < 2) {
        return;
      }
      event.preventDefault();
      if (wheelLocked) {
        return;
      }
      wheelLocked = true;
      if (delta > 0) {
        scrollNext();
      } else {
        scrollPrev();
      }
      window.setTimeout(() => {
        wheelLocked = false;
      }, 350);
    };
    const bindWheel = () => {
      const slider = sliderRef.value;
      if (!slider) {
        return;
      }
      slider.addEventListener(
        "wheel",
        handleWheel,
        {
          passive: false
        }
      );
    };
    const unbindWheel = () => {
      const slider = sliderRef.value;
      if (!slider) {
        return;
      }
      slider.removeEventListener(
        "wheel",
        handleWheel
      );
    };
    const handleResize = async () => {
      await nextTick();
      updateScrollState();
    };
    const productLink = (product) => {
      return (product == null ? void 0 : product.url) ? route("public.marketProducts.show", {
        url: product.url
      }) : "#";
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
    const toNumber = (value) => {
      const number = Number(value);
      return Number.isFinite(number) ? number : 0;
    };
    const getPrice = (product) => {
      return toNumber(product == null ? void 0 : product.price);
    };
    const getOldPrice = (product) => {
      return toNumber(product == null ? void 0 : product.old_price);
    };
    const hasOldPrice = (product) => {
      return getOldPrice(product) > getPrice(product);
    };
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
      return (product == null ? void 0 : product.url) ? route("public.marketProducts.show", {
        url: product.url
      }) : "";
    };
    const productAvailability = (product) => {
      return hasStock(product) ? "https://schema.org/InStock" : "https://schema.org/OutOfStock";
    };
    const ratingCount = (product) => {
      return Number((product == null ? void 0 : product.rating_count) ?? 0);
    };
    const hasAggregateRating = (product) => {
      return rating(product) > 0 && ratingCount(product) > 0;
    };
    const formatPrice = (value) => {
      return new Intl.NumberFormat(locale.value, {
        minimumFractionDigits: 0,
        maximumFractionDigits: 2
      }).format(toNumber(value));
    };
    const hasStock = (product) => {
      return Boolean(
        (product == null ? void 0 : product.has_stock) ?? ((product == null ? void 0 : product.in_stock) && Number((product == null ? void 0 : product.quantity) ?? 0) > 0)
      );
    };
    const quantity = (product) => {
      return Number((product == null ? void 0 : product.quantity) ?? 0);
    };
    const rating = (product) => {
      return toNumber(product == null ? void 0 : product.rating_avg);
    };
    const reviewsCount = (product) => {
      return Number((product == null ? void 0 : product.reviews_count) ?? 0);
    };
    const hasMarketingFlags = (product) => {
      return Boolean(
        (product == null ? void 0 : product.is_new) || (product == null ? void 0 : product.is_hit) || (product == null ? void 0 : product.is_sale)
      );
    };
    onMounted(async () => {
      await nextTick();
      updateScrollState();
      bindWheel();
      window.addEventListener(
        "resize",
        handleResize
      );
    });
    onBeforeUnmount(() => {
      unbindWheel();
      window.removeEventListener(
        "resize",
        handleResize
      );
    });
    return (_ctx, _push, _parent, _attrs) => {
      if (__props.products.length) {
        _push(`<section${ssrRenderAttrs(mergeProps({
          class: "mx-auto mt-5 w-full min-w-0 py-3 px-1 lg:px-6 max-w-screen-2xl ext-color rounded-3xl border-2 border-slate-300 dark:border-slate-500",
          itemscope: "",
          itemtype: "https://schema.org/ItemList"
        }, _attrs))}><meta itemprop="numberOfItems"${ssrRenderAttr("content", String(__props.products.length))}><div class="mb-4 flex items-center justify-center gap-3"><button type="button"${ssrIncludeBooleanAttr(__props.products.length <= 1) ? " disabled" : ""} class="shrink-0 items-center justify-center rounded-sm border border-dotted border-gray-600 bg-slate-50 text-slate-600 shadow-sm transition hover:border-2 hover:border-solid hover:border-indigo-400 hover:bg-slate-200 hover:text-indigo-500 disabled:cursor-default disabled:opacity-30 dark:border-gray-400 dark:bg-slate-950 dark:text-slate-300 dark:hover:border-indigo-500 dark:hover:bg-slate-800 sm:flex h-8 w-8"${ssrRenderAttr("title", unref(t)("previous"))} aria-label="Previous products"><svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m15 18-6-6 6-6"></path></svg></button><h2 itemprop="name" class="flex items-center justify-center text-sm sm:text-xl font-semibold text-slate-800 dark:text-slate-200"><span>${ssrInterpolate(unref(t)("recentlyViewedProducts"))}</span><span class="ml-2 inline-flex min-w-6 items-center justify-center rounded-full border border-teal-200 bg-teal-50 px-2 py-0.5 text-xs font-bold text-teal-600 shadow-sm dark:border-teal-700/70 dark:bg-teal-950/60 dark:text-teal-300">${ssrInterpolate(__props.products.length)}</span></h2><button type="button"${ssrIncludeBooleanAttr(__props.products.length <= 1) ? " disabled" : ""} class="shrink-0 items-center justify-center rounded-sm border border-dotted border-gray-600 bg-slate-50 text-slate-600 shadow-sm transition hover:border-2 hover:border-solid hover:border-indigo-400 hover:bg-slate-200 hover:text-indigo-500 disabled:cursor-default disabled:opacity-30 dark:border-gray-400 dark:bg-slate-950 dark:text-slate-300 dark:hover:border-indigo-500 dark:hover:bg-slate-800 sm:flex h-8 w-8"${ssrRenderAttr("title", unref(t)("next"))} aria-label="Next products"><svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m9 18 6-6-6-6"></path></svg></button></div><div class="flex min-w-0 w-full snap-x snap-mandatory gap-4 overflow-x-auto scroll-smooth pb-2 [scrollbar-width:none] [&amp;::-webkit-scrollbar]:hidden"><!--[-->`);
        ssrRenderList(__props.products, (product, index) => {
          _push(`<article class="group flex shrink-0 snap-start w-full sm:w-[calc(50%-0.5rem)] md:w-[calc(33.333333%-0.667rem)] lg:w-[calc(25%-0.75rem)] flex-col overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm transition hover:-translate-y-0.5 hover:shadow-md dark:border-gray-700 dark:bg-gray-900" itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem"><meta itemprop="position"${ssrRenderAttr("content", String(index + 1))}><div class="flex h-full flex-col" itemprop="item" itemscope itemtype="https://schema.org/Product">`);
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
                  "height-class": "h-40",
                  "rounded-class": "",
                  "wrapper-class": "",
                  "img-class": "w-full h-full object-cover transition duration-300\n                                           group-hover:scale-105"
                }, null, _parent2, _scopeId));
              } else {
                return [
                  createVNode(UniversalImageSlider, {
                    entity: product,
                    "height-class": "h-40",
                    "rounded-class": "",
                    "wrapper-class": "",
                    "img-class": "w-full h-full object-cover transition duration-300\n                                           group-hover:scale-105"
                  }, null, 8, ["entity"])
                ];
              }
            }),
            _: 2
          }, _parent));
          if (hasMarketingFlags(product)) {
            _push(`<div class="absolute left-2 top-2 z-10 flex flex-wrap gap-1">`);
            if (product.is_new) {
              _push(`<span class="rounded-sm bg-teal-500 px-1.5 py-0.5 text-[9px] font-bold uppercase tracking-wide text-white shadow-sm"> NEW </span>`);
            } else {
              _push(`<!---->`);
            }
            if (product.is_hit) {
              _push(`<span class="rounded-sm bg-amber-500 px-1.5 py-0.5 text-[9px] font-bold uppercase tracking-wide text-slate-900 shadow-sm"> HIT </span>`);
            } else {
              _push(`<!---->`);
            }
            if (product.is_sale) {
              _push(`<span class="rounded-sm bg-red-500 px-1.5 py-0.5 text-[9px] font-bold uppercase tracking-wide text-white shadow-sm"> SALE </span>`);
            } else {
              _push(`<!---->`);
            }
            _push(`</div>`);
          } else {
            _push(`<!---->`);
          }
          _push(`</div><div class="flex flex-1 flex-col p-3">`);
          if (getBrandTitle(product)) {
            _push(`<div class="mb-1 text-center" itemprop="brand" itemscope itemtype="https://schema.org/Brand"><span itemprop="name" class="text-[10px] font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">${ssrInterpolate(getBrandTitle(product))}</span></div>`);
          } else {
            _push(`<!---->`);
          }
          _push(`<div class="text-center">`);
          _push(ssrRenderComponent(unref(Link), {
            href: productLink(product),
            title: getProductShort(product),
            class: "inline-flex"
          }, {
            default: withCtx((_, _push2, _parent2, _scopeId) => {
              if (_push2) {
                _push2(`<span itemprop="name" class="line-clamp-2 text-xs font-semibold text-slate-900/85 transition group-hover:opacity-75 dark:text-slate-100/85"${_scopeId}>${ssrInterpolate(getProductTitle(product))}</span>`);
              } else {
                return [
                  createVNode("span", {
                    itemprop: "name",
                    class: "line-clamp-2 text-xs font-semibold text-slate-900/85 transition group-hover:opacity-75 dark:text-slate-100/85"
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
          _push(`<link itemprop="availability"${ssrRenderAttr("href", productAvailability(product))}><div class="mt-2 flex flex-col items-center justify-center"><span class="text-sm font-bold text-teal-600 dark:text-teal-400">${ssrInterpolate(formatPrice(getPrice(product)))} ${ssrInterpolate(getCurrency(product))}</span>`);
          if (hasOldPrice(product)) {
            _push(`<span class="text-[11px] font-semibold text-slate-400 line-through dark:text-slate-500">${ssrInterpolate(formatPrice(getOldPrice(product)))} ${ssrInterpolate(getCurrency(product))}</span>`);
          } else {
            _push(`<!---->`);
          }
          _push(`</div><div class="mt-1 flex items-center justify-center gap-1 text-[10px] font-semibold text-slate-500 dark:text-slate-400"><svg viewBox="0 0 24 24" fill="currentColor" class="${ssrRenderClass([hasStock(product) ? "text-emerald-600 dark:text-emerald-300" : "text-red-500 dark:text-red-300", "h-3 w-3 shrink-0"])}">`);
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
          _push(`</div></div><div class="mt-2 flex flex-wrap items-center justify-center gap-2">`);
          if (product.views > 0) {
            _push(`<div class="inline-flex items-center gap-1 text-[10px] font-semibold text-slate-500 dark:text-slate-400"><svg class="h-3 w-3 text-blue-600/85 dark:text-blue-200/85" viewBox="0 0 576 512" fill="currentColor"><path d="M569.354 231.631C512.97 135.949 407.81 72 288 72 168.14 72 63.004 135.994 6.646 231.631a47.999 47.999 0 0 0 0 48.739C63.031 376.051 168.19 440 288 440c119.86 0 224.996-63.994 281.354-159.631a47.997 47.997 0 0 0 0-48.738zM288 392c-102.556 0-192.091-54.701-240-136 44.157-74.933 123.677-127.27 216.162-135.007C273.958 131.078 280 144.83 280 160c0 30.928-25.072 56-56 56s-56-25.072-56-56l.001-.042C157.794 179.043 152 200.844 152 224c0 75.111 60.889 136 136 136s136-60.889 136-136c0-31.031-10.4-59.629-27.895-82.515C451.704 164.638 498.009 205.106 528 256c-47.908 81.299-137.444 136-240 136z"></path></svg> ${ssrInterpolate(product.views)}</div>`);
          } else {
            _push(`<!---->`);
          }
          if (rating(product) > 0) {
            _push(`<div class="inline-flex items-center gap-1 text-[10px] font-semibold text-slate-500 dark:text-slate-400"><svg class="h-3 w-3 text-amber-500 dark:text-amber-300" viewBox="0 0 24 24" fill="currentColor"><path d="M12.746,1.464l3.11,6.3L22.81,8.776a.831.831,0,0,1,.461,1.418l-5.033,4.9,1.188,6.926a.832.832,0,0,1-1.207.877L12,19.632,5.78,22.9a.833.833,0,0,1-1.207-.878L5.761,15.1l-5.033-4.9a.831.831,0,0,1,.461-1.418L8.143,7.765l3.11-6.3A.833.833,0,0,1,12.746,1.464Z"></path></svg> ${ssrInterpolate(rating(product).toFixed(1))}</div>`);
          } else {
            _push(`<!---->`);
          }
          if (reviewsCount(product) > 0) {
            _push(`<div class="text-[10px] font-semibold text-slate-500 dark:text-slate-400">${ssrInterpolate(unref(t)("reviews"))}: ${ssrInterpolate(reviewsCount(product))}</div>`);
          } else {
            _push(`<!---->`);
          }
          _push(`</div>`);
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
          _push(`<div class="mt-auto flex items-center justify-center pt-2">`);
          _push(ssrRenderComponent(LikeButtonEntity, {
            "likes-count": product.likes_count || 0,
            "already-liked": product.already_liked || false,
            "route-name": "public.marketProducts.like",
            "route-params": { id: product.id },
            "icon-class": "w-3 h-3 hover:scale-110 active:scale-95"
          }, null, _parent));
          _push(`</div></div></div></article>`);
        });
        _push(`<!--]--></div></section>`);
      } else {
        _push(`<!---->`);
      }
    };
  }
};
const _sfc_setup = _sfc_main.setup;
_sfc_main.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Components/Public/Default/Market/MarketProduct/MarketRecentlyViewedProducts.vue");
  return _sfc_setup ? _sfc_setup(props, ctx) : void 0;
};
const STORAGE_KEY = "market_recently_viewed_products";
const STORAGE_LIMIT = 8;
const useRecentlyViewedProducts = () => {
  const page = usePage();
  const products = ref([]);
  const loading = ref(false);
  const error = ref(null);
  const isAuthenticated = () => {
    var _a, _b, _c;
    return Boolean(
      ((_b = (_a = page.props) == null ? void 0 : _a.auth) == null ? void 0 : _b.user) || ((_c = page.props) == null ? void 0 : _c.user)
    );
  };
  const getGuestIds = () => {
    if (typeof window === "undefined") {
      return [];
    }
    try {
      const stored = window.localStorage.getItem(STORAGE_KEY);
      if (!stored) {
        return [];
      }
      const parsed = JSON.parse(stored);
      if (!Array.isArray(parsed)) {
        return [];
      }
      return parsed.map((id) => Number(id)).filter((id) => Number.isInteger(id) && id > 0).filter((id, index, array) => {
        return array.indexOf(id) === index;
      }).slice(0, STORAGE_LIMIT);
    } catch {
      return [];
    }
  };
  const saveGuestIds = (ids) => {
    if (typeof window === "undefined") {
      return;
    }
    const normalized = ids.map((id) => Number(id)).filter((id) => Number.isInteger(id) && id > 0).filter((id, index, array) => {
      return array.indexOf(id) === index;
    }).slice(0, STORAGE_LIMIT);
    window.localStorage.setItem(
      STORAGE_KEY,
      JSON.stringify(normalized)
    );
  };
  const rememberGuestProduct = (productId) => {
    if (isAuthenticated()) {
      return;
    }
    const id = Number(productId);
    if (!Number.isInteger(id) || id <= 0) {
      return;
    }
    const ids = getGuestIds().filter((storedId) => storedId !== id);
    ids.unshift(id);
    saveGuestIds(ids);
  };
  const clearGuestHistory = () => {
    if (typeof window === "undefined") {
      return;
    }
    window.localStorage.removeItem(STORAGE_KEY);
  };
  const excludeProduct = (items, excludeProductId = null) => {
    const list = Array.isArray(items) ? items : [];
    const excludedId = Number(excludeProductId);
    if (!Number.isInteger(excludedId) || excludedId <= 0) {
      return list;
    }
    return list.filter(
      (product) => Number(product == null ? void 0 : product.id) !== excludedId
    );
  };
  const load = async (excludeProductId = null) => {
    var _a;
    loading.value = true;
    error.value = null;
    try {
      let ids = [];
      if (!isAuthenticated()) {
        ids = getGuestIds();
        if (excludeProductId) {
          const excludedId = Number(
            excludeProductId
          );
          ids = ids.filter(
            (id) => id !== excludedId
          );
        }
      }
      const response = await axios.post(
        route("public.marketProducts.recentlyViewed"),
        {
          ids
        }
      );
      const responseProducts = Array.isArray(
        (_a = response.data) == null ? void 0 : _a.products
      ) ? response.data.products : [];
      products.value = excludeProduct(
        responseProducts,
        excludeProductId
      );
      return products.value;
    } catch (exception) {
      console.error(
        "Ошибка загрузки недавно просмотренных товаров:",
        exception
      );
      error.value = exception;
      products.value = [];
      return [];
    } finally {
      loading.value = false;
    }
  };
  const mergeGuestHistory = async (excludeProductId = null) => {
    var _a;
    if (!isAuthenticated()) {
      return [];
    }
    const ids = getGuestIds();
    if (!ids.length) {
      products.value = excludeProduct(
        products.value,
        excludeProductId
      );
      return products.value;
    }
    loading.value = true;
    error.value = null;
    try {
      const response = await axios.post(
        route(
          "public.marketProducts.recentlyViewed.merge"
        ),
        {
          ids
        }
      );
      const responseProducts = Array.isArray(
        (_a = response.data) == null ? void 0 : _a.products
      ) ? response.data.products : [];
      products.value = excludeProduct(
        responseProducts,
        excludeProductId
      );
      clearGuestHistory();
      return products.value;
    } catch (exception) {
      console.error(
        "Ошибка объединения истории просмотренных товаров:",
        exception
      );
      error.value = exception;
      return products.value;
    } finally {
      loading.value = false;
    }
  };
  const clear = async () => {
    error.value = null;
    if (!isAuthenticated()) {
      clearGuestHistory();
      products.value = [];
      return true;
    }
    loading.value = true;
    try {
      await axios.delete(
        route(
          "public.marketProducts.recentlyViewed.clear"
        )
      );
      products.value = [];
      clearGuestHistory();
      return true;
    } catch (exception) {
      console.error(
        "Ошибка очистки просмотренных товаров:",
        exception
      );
      error.value = exception;
      return false;
    } finally {
      loading.value = false;
    }
  };
  const setProducts = (items) => {
    products.value = Array.isArray(items) ? items : [];
  };
  return {
    /** State */
    products,
    loading,
    error,
    /** Auth */
    isAuthenticated,
    /** Guest storage */
    getGuestIds,
    rememberGuestProduct,
    clearGuestHistory,
    /** API */
    load,
    mergeGuestHistory,
    clear,
    /** Initial data */
    setProducts
  };
};
export {
  _sfc_main as _,
  useRecentlyViewedProducts as u
};
