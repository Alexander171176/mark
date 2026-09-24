import { ref, watch, mergeProps, unref, useSSRContext, computed, onMounted, nextTick, onBeforeUnmount, withCtx, createVNode, toDisplayString, createBlock, createCommentVNode, openBlock } from "vue";
import { ssrRenderAttrs, ssrInterpolate, ssrRenderList, ssrRenderAttr, ssrIncludeBooleanAttr, ssrLooseContain, ssrLooseEqual, ssrRenderComponent, ssrRenderClass } from "vue/server-renderer";
import { Link, usePage, Head, router } from "@inertiajs/vue3";
import { useI18n } from "vue-i18n";
import { u as useSmoothScrollTo } from "./useSmoothScrollTo-g9G3kyDv.js";
import { _ as _sfc_main$4 } from "./DefaultLayout-6zxGxPtK.js";
import { _ as _sfc_main$5, a as _sfc_main$d, P as Progress } from "./Progress-CyfOKcqP.js";
import { _ as _sfc_main$6, a as _sfc_main$b } from "./RightSidebarMarket-DpLr4Pjm.js";
import { _ as _sfc_main$3 } from "./ViewModeToggle-DMCnQ0wo.js";
import { _ as _sfc_main$7, a as _sfc_main$8 } from "./MarketProductRows-D4G6aZpS.js";
import { u as useRecentlyViewedProducts, _ as _sfc_main$c } from "./useRecentlyViewedProducts-CPkZCnFT.js";
import { _ as _sfc_main$9, a as _sfc_main$a, b as _sfc_main$e } from "./PublicAdminBottomPanel-BhLEQMoJ.js";
import "./UniversalImageSlider-Cu2Xndcn.js";
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
import "./LikeButtonEntity-ZC4HMEAO.js";
import "./ProcessingModeSwitcher-BJvzFf6_.js";
const _sfc_main$2 = {
  __name: "EntityListToolbar",
  __ssrInlineRender: true,
  props: {
    found: {
      type: Number,
      default: 0
    },
    viewMode: {
      type: String,
      default: "grid"
    },
    sortValue: {
      type: String,
      default: ""
    },
    sortOptions: {
      type: Array,
      default: () => []
    },
    foundLabel: {
      type: String,
      default: ""
    }
  },
  emits: [
    "update:viewMode",
    "update:sortValue"
  ],
  setup(__props, { emit: __emit }) {
    const { t } = useI18n();
    const props = __props;
    const emit = __emit;
    const sortLocal = ref(
      String(props.sortValue ?? "")
    );
    watch(
      () => props.sortValue,
      (value) => {
        sortLocal.value = String(value ?? "");
      }
    );
    const setView = (mode) => {
      emit("update:viewMode", mode);
    };
    return (_ctx, _push, _parent, _attrs) => {
      _push(`<div${ssrRenderAttrs(mergeProps({ class: "my-4 p-2 rounded-md shadow-sm bg-white dark:bg-gray-900 border border-gray-300 dark:border-gray-600" }, _attrs))}><div class="px-2 py-2 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between"><div class="text-start text-xs text-slate-500 dark:text-slate-400">${ssrInterpolate(unref(t)("found"))} ${ssrInterpolate(__props.foundLabel)}: <span class="font-bold">${ssrInterpolate(__props.found)}</span></div><div class="flex items-center justify-end gap-2"><div class="flex flex-row items-center gap-2"><label class="hidden xl:block text-xs font-semibold text-slate-500 dark:text-slate-400">${ssrInterpolate(unref(t)("sort"))}</label><select class="rounded-sm pl-2 pr-6 py-1.5 text-xs font-semibold border border-gray-400 dark:border-gray-500 bg-white dark:bg-gray-900 outline-none text-slate-700 dark:text-slate-300 focus:border-indigo-400 focus:ring-2 focus:ring-indigo-200 dark:focus:border-indigo-500 dark:focus:ring-indigo-900/40"><!--[-->`);
      ssrRenderList(__props.sortOptions, (option) => {
        _push(`<option${ssrRenderAttr("value", option.value)}${ssrIncludeBooleanAttr(Array.isArray(sortLocal.value) ? ssrLooseContain(sortLocal.value, option.value) : ssrLooseEqual(sortLocal.value, option.value)) ? " selected" : ""}>${ssrInterpolate(option.label)}</option>`);
      });
      _push(`<!--]--></select></div>`);
      _push(ssrRenderComponent(_sfc_main$3, {
        "model-value": __props.viewMode,
        "onUpdate:modelValue": setView
      }, null, _parent));
      _push(`</div></div></div>`);
    };
  }
};
const _sfc_setup$2 = _sfc_main$2.setup;
_sfc_main$2.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Components/Public/Default/PageToolbar/EntityListToolbar.vue");
  return _sfc_setup$2 ? _sfc_setup$2(props, ctx) : void 0;
};
const _sfc_main$1 = {
  __name: "MarketBrandCarousel",
  __ssrInlineRender: true,
  props: {
    brands: {
      type: Array,
      default: () => []
    }
  },
  setup(__props) {
    const { t } = useI18n();
    const props = __props;
    const brandsData = computed(() => {
      return Array.isArray(props.brands) ? props.brands : [];
    });
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
    const brandLink = (brand) => {
      return (brand == null ? void 0 : brand.url) ? route("public.marketBrands.show", {
        url: brand.url
      }) : "#";
    };
    const getBrandTitle = (brand) => {
      var _a;
      return ((_a = brand == null ? void 0 : brand.translation) == null ? void 0 : _a.title) || "";
    };
    const getBrandShort = (brand) => {
      var _a;
      return ((_a = brand == null ? void 0 : brand.translation) == null ? void 0 : _a.short) || "";
    };
    const getProductsCount = (brand) => {
      const count = Number(
        (brand == null ? void 0 : brand.products_count) ?? 0
      );
      return Number.isFinite(count) ? count : 0;
    };
    const normalizeStorageUrl = (value) => {
      const url = String(value || "").trim();
      if (!url) {
        return "";
      }
      if (url.startsWith("http://") || url.startsWith("https://") || url.startsWith("//") || url.startsWith("/") || url.startsWith("data:") || url.startsWith("blob:")) {
        return url;
      }
      return `/storage/${url}`;
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
    const getBrandImage = (brand) => {
      const image = getBrandImages(brand)[0];
      const url = (image == null ? void 0 : image.webp_url) || (image == null ? void 0 : image.image_url) || (image == null ? void 0 : image.url) || (image == null ? void 0 : image.thumb_url) || "";
      return normalizeStorageUrl(url);
    };
    const getBrandLogo = (brand) => {
      return normalizeStorageUrl(
        brand == null ? void 0 : brand.logo
      );
    };
    const getBrandVisual = (brand) => {
      return getBrandImage(brand) || getBrandLogo(brand);
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
      if (brandsData.value.length) {
        _push(`<section${ssrRenderAttrs(mergeProps({
          class: "mx-auto mt-5 w-full min-w-0 py-3 px-1 lg:px-6 max-w-screen-2xl ext-color rounded-3xl border-2 border-slate-300 dark:border-slate-500",
          itemscope: "",
          itemtype: "https://schema.org/ItemList"
        }, _attrs))}><meta itemprop="numberOfItems"${ssrRenderAttr("content", String(brandsData.value.length))}><div class="mb-4 flex items-center justify-center gap-3"><button type="button"${ssrIncludeBooleanAttr(brandsData.value.length <= 1) ? " disabled" : ""} class="shrink-0 items-center justify-center rounded-sm border border-dotted border-gray-600 bg-slate-50 text-slate-600 shadow-sm transition hover:border-2 hover:border-solid hover:border-indigo-400 hover:bg-slate-200 hover:text-indigo-500 disabled:cursor-default disabled:opacity-30 dark:border-gray-400 dark:bg-slate-950 dark:text-slate-300 dark:hover:border-indigo-500 dark:hover:bg-slate-800 sm:flex h-8 w-8"${ssrRenderAttr("title", unref(t)("previous"))} aria-label="Previous brands"><svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m15 18-6-6 6-6"></path></svg></button><h2 itemprop="name" class="flex items-center justify-center text-sm sm:text-xl font-semibold text-slate-800 dark:text-slate-200"><span>${ssrInterpolate(unref(t)("brands"))}</span><span class="ml-2 inline-flex min-w-6 items-center justify-center rounded-full border border-teal-200 bg-teal-50 px-2 py-0.5 text-xs font-bold text-teal-600 shadow-sm dark:border-teal-700/70 dark:bg-teal-950/60 dark:text-teal-300">${ssrInterpolate(brandsData.value.length)}</span></h2><button type="button"${ssrIncludeBooleanAttr(brandsData.value.length <= 1) ? " disabled" : ""} class="shrink-0 items-center justify-center rounded-sm border border-dotted border-gray-600 bg-slate-50 text-slate-600 shadow-sm transition hover:border-2 hover:border-solid hover:border-indigo-400 hover:bg-slate-200 hover:text-indigo-500 disabled:cursor-default disabled:opacity-30 dark:border-gray-400 dark:bg-slate-950 dark:text-slate-300 dark:hover:border-indigo-500 dark:hover:bg-slate-800 sm:flex h-8 w-8"${ssrRenderAttr("title", unref(t)("next"))} aria-label="Next brands"><svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m9 18 6-6-6-6"></path></svg></button></div><div class="flex min-w-0 w-full snap-x snap-mandatory gap-4 overflow-x-auto scroll-smooth pb-2 [scrollbar-width:none] [&amp;::-webkit-scrollbar]:hidden"><!--[-->`);
        ssrRenderList(brandsData.value, (brand, index) => {
          _push(`<article class="group flex shrink-0 snap-start w-full sm:w-[calc(50%-0.5rem)] md:w-[calc(33.333333%-0.667rem)] lg:w-[calc(25%-0.75rem)] flex-col overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm transition hover:-translate-y-0.5 hover:shadow-md dark:border-gray-700 dark:bg-gray-900" itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem"><meta itemprop="position"${ssrRenderAttr("content", String(index + 1))}><div class="flex h-full flex-col" itemprop="item" itemscope itemtype="https://schema.org/Brand"><meta itemprop="url"${ssrRenderAttr("content", brandLink(brand))}>`);
          if (getBrandVisual(brand)) {
            _push(`<meta itemprop="image"${ssrRenderAttr("content", getBrandVisual(brand))}>`);
          } else {
            _push(`<!---->`);
          }
          if (getBrandShort(brand)) {
            _push(`<meta itemprop="description"${ssrRenderAttr("content", getBrandShort(brand))}>`);
          } else {
            _push(`<!---->`);
          }
          _push(`<div class="flex flex-1 flex-col items-center p-3 text-center">`);
          if (getBrandLogo(brand) && getBrandLogo(brand) !== getBrandVisual(brand)) {
            _push(ssrRenderComponent(unref(Link), {
              href: brandLink(brand),
              class: "mt-1 mb-2 flex h-32 w-auto items-center justify-center rounded-sm border border-slate-200 bg-white p-2 shadow-sm dark:border-slate-700 dark:bg-slate-950",
              title: getBrandTitle(brand)
            }, {
              default: withCtx((_, _push2, _parent2, _scopeId) => {
                if (_push2) {
                  _push2(`<img${ssrRenderAttr("src", getBrandLogo(brand))}${ssrRenderAttr("alt", getBrandTitle(brand))} class="max-h-full max-w-full object-contain" loading="lazy" decoding="async"${_scopeId}>`);
                } else {
                  return [
                    createVNode("img", {
                      src: getBrandLogo(brand),
                      alt: getBrandTitle(brand),
                      class: "max-h-full max-w-full object-contain",
                      loading: "lazy",
                      decoding: "async"
                    }, null, 8, ["src", "alt"])
                  ];
                }
              }),
              _: 2
            }, _parent));
          } else {
            _push(`<!---->`);
          }
          _push(ssrRenderComponent(unref(Link), {
            href: brandLink(brand),
            class: "inline-flex",
            title: getBrandShort(brand)
          }, {
            default: withCtx((_, _push2, _parent2, _scopeId) => {
              if (_push2) {
                _push2(`<span itemprop="name" class="line-clamp-2 text-sm font-bold text-slate-900/90 transition group-hover:text-indigo-600 dark:text-slate-100/90 dark:group-hover:text-indigo-400"${_scopeId}>${ssrInterpolate(getBrandTitle(brand))}</span>`);
              } else {
                return [
                  createVNode("span", {
                    itemprop: "name",
                    class: "line-clamp-2 text-sm font-bold text-slate-900/90 transition group-hover:text-indigo-600 dark:text-slate-100/90 dark:group-hover:text-indigo-400"
                  }, toDisplayString(getBrandTitle(brand)), 1)
                ];
              }
            }),
            _: 2
          }, _parent));
          if (getBrandShort(brand)) {
            _push(`<p class="mt-2 line-clamp-3 text-xs leading-5 text-slate-500 dark:text-slate-400">${ssrInterpolate(getBrandShort(brand))}</p>`);
          } else {
            _push(`<!---->`);
          }
          _push(`<div class="mt-auto pt-3">`);
          _push(ssrRenderComponent(unref(Link), {
            href: brandLink(brand),
            class: "inline-flex items-center gap-1.5 rounded-full border border-teal-200 bg-teal-50 px-3 py-1 text-[11px] font-semibold text-teal-700 transition hover:border-teal-300 hover:bg-teal-100 dark:border-teal-800 dark:bg-teal-950/50 dark:text-teal-300 dark:hover:border-teal-700 dark:hover:bg-teal-950"
          }, {
            default: withCtx((_, _push2, _parent2, _scopeId) => {
              if (_push2) {
                _push2(`<span${_scopeId}>${ssrInterpolate(unref(t)("products"))}: </span><span${_scopeId}>${ssrInterpolate(getProductsCount(brand))}</span>`);
              } else {
                return [
                  createVNode("span", null, toDisplayString(unref(t)("products")) + ": ", 1),
                  createVNode("span", null, toDisplayString(getProductsCount(brand)), 1)
                ];
              }
            }),
            _: 2
          }, _parent));
          _push(`</div></div></div></article>`);
        });
        _push(`<!--]--></div></section>`);
      } else {
        _push(`<!---->`);
      }
    };
  }
};
const _sfc_setup$1 = _sfc_main$1.setup;
_sfc_main$1.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Components/Public/Default/Market/MarketBrand/MarketBrandCarousel.vue");
  return _sfc_setup$1 ? _sfc_setup$1(props, ctx) : void 0;
};
const LEFT_SIDEBAR_KEY = "public_left_sidebar_collapsed";
const RIGHT_SIDEBAR_KEY = "public_right_sidebar_collapsed";
const VIEW_KEY = "public_market_products_view";
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
    useServerProcessing: { type: Boolean, default: false },
    publicMarketProductsProcessingMode: { type: String, default: "server" },
    /** Сортировка Public по умолчанию из backend */
    defaultSort: { type: String, default: "sortAsc" },
    categoryTree: { type: Array, default: () => [] },
    products: { type: [Array, Object], default: () => [] },
    productsCount: { type: Number, default: 0 },
    productsFound: { type: Number, default: 0 },
    /** Недавно просмотренные товары */
    recentlyViewedProducts: {
      type: [Array, Object],
      default: () => []
    },
    /** Карусель брендов */
    marketBrandCarousel: {
      type: [Array, Object],
      default: () => []
    },
    filters: { type: Object, default: () => ({}) }
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
    const productsData = computed(() => {
      var _a2;
      if (Array.isArray(props.products)) {
        return props.products;
      }
      if (Array.isArray((_a2 = props.products) == null ? void 0 : _a2.data)) {
        return props.products.data;
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
    const brandCarousel = computed(() => {
      return normalizeList(
        props.marketBrandCarousel
      );
    });
    const {
      products: recentlyViewed,
      load: loadRecentlyViewedProducts,
      mergeGuestHistory,
      setProducts: setRecentlyViewedProducts
    } = useRecentlyViewedProducts();
    const initialRecentlyViewedProducts = computed(() => {
      return normalizeList(
        props.recentlyViewedProducts
      );
    });
    onMounted(async () => {
      var _a2, _b2;
      if ((_b2 = (_a2 = page.props) == null ? void 0 : _a2.auth) == null ? void 0 : _b2.user) {
        setRecentlyViewedProducts(
          initialRecentlyViewedProducts.value
        );
        await mergeGuestHistory();
        return;
      }
      await loadRecentlyViewedProducts();
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
    const productGridCols = computed(() => {
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
    const q = ref(String(((_b = props.filters) == null ? void 0 : _b.q) ?? ""));
    const sort = ref(
      String(
        ((_c = props.filters) == null ? void 0 : _c.sort) ?? props.defaultSort
      )
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
    const productSortOptions = [
      { value: "idDesc", label: t("idDesc") },
      { value: "idAsc", label: t("idAsc") },
      { value: "sortAsc", label: `${t("sortNumber")} 0→9` },
      { value: "sortDesc", label: `${t("sortNumber")} 9→0` },
      { value: "titleAsc", label: `${t("title")} A→Z` },
      { value: "titleDesc", label: `${t("title")} Z→A` },
      { value: "priceAsc", label: `${t("price")} 0→9` },
      { value: "priceDesc", label: `${t("price")} 9→0` },
      { value: "quantityAsc", label: `${t("quantity")} 0→9` },
      { value: "quantityDesc", label: `${t("quantity")} 9→0` },
      { value: "viewsDesc", label: `${t("views")} 9→0` },
      { value: "viewsAsc", label: `${t("views")} 0→9` },
      { value: "likesDesc", label: `${t("likes")} 9→0` },
      { value: "likesAsc", label: `${t("likes")} 0→9` },
      { value: "ratingDesc", label: `${t("rating")} 9→0` },
      { value: "ratingAsc", label: `${t("rating")} 0→9` },
      { value: "ratingCountDesc", label: `${t("ratingCount")} 9→0` },
      { value: "ratingCountAsc", label: `${t("ratingCount")} 0→9` },
      { value: "reviewsDesc", label: `${t("reviews")} 9→0` },
      { value: "reviewsAsc", label: `${t("reviews")} 0→9` },
      { value: "brandAsc", label: `${t("brand")} A→Z` },
      { value: "brandDesc", label: `${t("brand")} Z→A` },
      { value: "publishedAtDesc", label: `${t("publishedAt")} ↓` },
      { value: "publishedAtAsc", label: `${t("publishedAt")} ↑` },
      { value: "createdAtDesc", label: `${t("createdAt")} ↓` },
      { value: "createdAtAsc", label: `${t("createdAt")} ↑` }
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
    const normalizeDate = (value) => {
      if (!value) {
        return 0;
      }
      const timestamp = new Date(value).getTime();
      return Number.isFinite(timestamp) ? timestamp : 0;
    };
    const getProductTitle = (product) => {
      var _a2;
      return ((_a2 = product == null ? void 0 : product.translation) == null ? void 0 : _a2.title) || "";
    };
    const getProductSubtitle = (product) => {
      var _a2;
      return ((_a2 = product == null ? void 0 : product.translation) == null ? void 0 : _a2.subtitle) || "";
    };
    const getProductShort = (product) => {
      var _a2;
      return ((_a2 = product == null ? void 0 : product.translation) == null ? void 0 : _a2.short) || "";
    };
    const getBrandTitle = (product) => {
      var _a2, _b2;
      return ((_b2 = (_a2 = product == null ? void 0 : product.brand) == null ? void 0 : _a2.translation) == null ? void 0 : _b2.title) || "";
    };
    const searchWords = computed(() => {
      return normalizeText(q.value).split(/\s+/u).filter((word) => word.length >= 2);
    });
    const filteredProducts = computed(() => {
      const words = searchWords.value;
      if (!words.length) {
        return productsData.value;
      }
      return productsData.value.filter((product) => {
        const fields = [
          getProductTitle(product),
          getProductSubtitle(product),
          getProductShort(product),
          product == null ? void 0 : product.url,
          product == null ? void 0 : product.sku,
          product == null ? void 0 : product.vendor_code,
          product == null ? void 0 : product.barcode,
          getBrandTitle(product)
        ].map(normalizeText);
        return words.every((word) => {
          return fields.some((field) => {
            return field.includes(word);
          });
        });
      });
    });
    const compareIdDesc = (a, b) => {
      return normalizeNumber(b == null ? void 0 : b.id) - normalizeNumber(a == null ? void 0 : a.id);
    };
    const compareNumber = (a, b, field, direction = "asc") => {
      const first = normalizeNumber(a == null ? void 0 : a[field]);
      const second = normalizeNumber(b == null ? void 0 : b[field]);
      const result = direction === "desc" ? second - first : first - second;
      return result || compareIdDesc(a, b);
    };
    const compareText = (first, second, direction = "asc") => {
      const a = normalizeText(first);
      const b = normalizeText(second);
      return direction === "desc" ? b.localeCompare(a) : a.localeCompare(b);
    };
    const compareDate = (a, b, field, direction = "asc") => {
      const first = normalizeDate(a == null ? void 0 : a[field]);
      const second = normalizeDate(b == null ? void 0 : b[field]);
      const result = direction === "desc" ? second - first : first - second;
      return result || compareIdDesc(a, b);
    };
    const sortedProducts = computed(() => {
      const list = [...filteredProducts.value];
      return list.sort((a, b) => {
        switch (sort.value) {
          case "idAsc":
            return normalizeNumber(a.id) - normalizeNumber(b.id);
          case "idDesc":
            return normalizeNumber(b.id) - normalizeNumber(a.id);
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
              getProductTitle(a),
              getProductTitle(b),
              "asc"
            );
            return result || compareIdDesc(a, b);
          }
          case "titleDesc": {
            const result = compareText(
              getProductTitle(a),
              getProductTitle(b),
              "desc"
            );
            return result || compareIdDesc(a, b);
          }
          case "priceAsc":
            return compareNumber(
              a,
              b,
              "price",
              "asc"
            );
          case "priceDesc":
            return compareNumber(
              a,
              b,
              "price",
              "desc"
            );
          case "quantityAsc":
            return compareNumber(
              a,
              b,
              "quantity",
              "asc"
            );
          case "quantityDesc":
            return compareNumber(
              a,
              b,
              "quantity",
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
          case "likesAsc":
            return compareNumber(
              a,
              b,
              "likes_count",
              "asc"
            );
          case "likesDesc":
            return compareNumber(
              a,
              b,
              "likes_count",
              "desc"
            );
          case "ratingAsc":
            return compareNumber(
              a,
              b,
              "rating_avg",
              "asc"
            );
          case "ratingDesc":
            return compareNumber(
              a,
              b,
              "rating_avg",
              "desc"
            );
          case "ratingCountAsc":
            return compareNumber(
              a,
              b,
              "rating_count",
              "asc"
            );
          case "ratingCountDesc":
            return compareNumber(
              a,
              b,
              "rating_count",
              "desc"
            );
          case "reviewsAsc":
            return compareNumber(
              a,
              b,
              "reviews_count",
              "asc"
            );
          case "reviewsDesc":
            return compareNumber(
              a,
              b,
              "reviews_count",
              "desc"
            );
          case "brandAsc": {
            const result = compareText(
              getBrandTitle(a),
              getBrandTitle(b),
              "asc"
            );
            return result || compareIdDesc(a, b);
          }
          case "brandDesc": {
            const result = compareText(
              getBrandTitle(a),
              getBrandTitle(b),
              "desc"
            );
            return result || compareIdDesc(a, b);
          }
          case "publishedAtAsc":
            return compareDate(
              a,
              b,
              "published_at",
              "asc"
            );
          case "publishedAtDesc":
            return compareDate(
              a,
              b,
              "published_at",
              "desc"
            );
          case "createdAtAsc":
            return compareDate(
              a,
              b,
              "created_at",
              "asc"
            );
          case "createdAtDesc":
            return compareDate(
              a,
              b,
              "created_at",
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
    const frontendPaginatedProducts = computed(() => {
      const start = (frontendCurrentPage.value - 1) * perPage.value;
      return sortedProducts.value.slice(
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
        ((_b2 = (_a2 = props.products) == null ? void 0 : _a2.meta) == null ? void 0 : _b2.current_page) ?? ((_c2 = props.products) == null ? void 0 : _c2.current_page) ?? 1
      ) || 1;
    });
    const lastPage = computed(() => {
      var _a2, _b2, _c2;
      return Number(
        ((_b2 = (_a2 = props.products) == null ? void 0 : _a2.meta) == null ? void 0 : _b2.last_page) ?? ((_c2 = props.products) == null ? void 0 : _c2.last_page) ?? 1
      ) || 1;
    });
    const seoTitle = computed(() => {
      var _a2;
      return ((_a2 = props.seo) == null ? void 0 : _a2.title) || t("products");
    });
    const seoKeywords = computed(() => {
      var _a2;
      return ((_a2 = props.seo) == null ? void 0 : _a2.keywords) || "";
    });
    const seoDescription = computed(() => {
      var _a2;
      return ((_a2 = props.seo) == null ? void 0 : _a2.description) || t("catalogDesc") || "";
    });
    const contentLocale = computed(() => {
      return String(props.locale || "");
    });
    const ogLocale = computed(() => {
      return contentLocale.value;
    });
    const dcSubject = computed(() => {
      return seoKeywords.value || seoTitle.value;
    });
    const seoPreview = computed(() => {
      for (const product of productsData.value) {
        const images = normalizeList(
          product == null ? void 0 : product.images
        );
        const image = images[0];
        const url = (image == null ? void 0 : image.webp_url) || (image == null ? void 0 : image.image_url) || (image == null ? void 0 : image.thumb_url) || (image == null ? void 0 : image.url) || "";
        if (url) {
          return {
            url,
            alt: getProductTitle(product) || seoTitle.value
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
        route("public.marketProducts.index")
      );
      if (props.useServerProcessing && currentPage.value > 1) {
        return `${baseUrl}?page=${currentPage.value}`;
      }
      return baseUrl;
    });
    const robotsContent = computed(() => {
      const hasSearch = String(q.value || "").trim() !== "";
      const hasAlternativeSort = String(sort.value || props.defaultSort) !== String(props.defaultSort);
      if (props.useServerProcessing && (hasSearch || hasAlternativeSort)) {
        return "noindex, follow, max-image-preview:large";
      }
      return "index, follow, max-image-preview:large";
    });
    const indexRoute = () => {
      return route("public.marketProducts.index");
    };
    const reloadProducts = (page2 = 1) => {
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
    const updateSort = (value) => {
      sort.value = value || props.defaultSort;
      if (props.useServerProcessing) {
        reloadProducts(1);
      }
    };
    const updateViewMode = (value) => {
      viewMode.value = value || "grid";
      frontendCurrentPage.value = 1;
      if (props.useServerProcessing) {
        reloadProducts(1);
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
      reloadProducts(safePage);
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
    const displayedProducts = computed(() => {
      return props.useServerProcessing ? productsData.value : frontendPaginatedProducts.value;
    });
    const productListStartPosition = computed(() => {
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
                key: 2,
                property: "og:description",
                content: seoDescription.value
              }, null, 8, ["content"])) : createCommentVNode("", true),
              createVNode("meta", {
                property: "og:url",
                content: canonicalUrl.value
              }, null, 8, ["content"]),
              ogLocale.value ? (openBlock(), createBlock("meta", {
                key: 3,
                property: "og:locale",
                content: ogLocale.value
              }, null, 8, ["content"])) : createCommentVNode("", true),
              seoImage.value ? (openBlock(), createBlock("meta", {
                key: 4,
                property: "og:image",
                content: seoImage.value
              }, null, 8, ["content"])) : createCommentVNode("", true),
              seoImage.value && seoImageAlt.value ? (openBlock(), createBlock("meta", {
                key: 5,
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
                key: 6,
                name: "twitter:description",
                content: seoDescription.value
              }, null, 8, ["content"])) : createCommentVNode("", true),
              seoImage.value ? (openBlock(), createBlock("meta", {
                key: 7,
                name: "twitter:image",
                content: seoImage.value
              }, null, 8, ["content"])) : createCommentVNode("", true),
              seoImage.value && seoImageAlt.value ? (openBlock(), createBlock("meta", {
                key: 8,
                name: "twitter:image:alt",
                content: seoImageAlt.value
              }, null, 8, ["content"])) : createCommentVNode("", true),
              createVNode("meta", {
                name: "DC.title",
                content: seoTitle.value
              }, null, 8, ["content"]),
              seoDescription.value ? (openBlock(), createBlock("meta", {
                key: 9,
                name: "DC.description",
                content: seoDescription.value
              }, null, 8, ["content"])) : createCommentVNode("", true),
              dcSubject.value ? (openBlock(), createBlock("meta", {
                key: 10,
                name: "DC.subject",
                content: dcSubject.value
              }, null, 8, ["content"])) : createCommentVNode("", true),
              contentLocale.value ? (openBlock(), createBlock("meta", {
                key: 11,
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
      _push(ssrRenderComponent(_sfc_main$4, null, {
        default: withCtx((_, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(ssrRenderComponent(_sfc_main$5, null, null, _parent2, _scopeId));
            _push2(`<main class="min-h-screen px-1 lg:px-6 max-w-full"${_scopeId}><div class="mx-auto tracking-wider pt-20 lg:pt-44"${_scopeId}><div class="ext-color w-full min-w-0 py-3 px-1 flex flex-col lg:flex-row gap-4 rounded-3xl border-2 border-slate-300 dark:border-slate-500"${_scopeId}>`);
            if (showLeft.value) {
              _push2(`<aside class="${ssrRenderClass([leftCollapsed.value ? "lg:w-6" : "lg:w-72", "shrink-0 transition-all duration-300"])}"${_scopeId}>`);
              _push2(ssrRenderComponent(_sfc_main$6, {
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
            _push2(`<meta itemprop="position" content="2"${_scopeId}></li><li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem" class="flex items-center" aria-current="page"${_scopeId}><span class="mx-2 breadcrumbs"${_scopeId}> / </span><h1 itemprop="name" class="breadcrumbs text-sm font-semibold"${_scopeId}>${ssrInterpolate(unref(t)("products"))}</h1><meta itemprop="item"${ssrRenderAttr("content", canonicalUrl.value)}${_scopeId}><meta itemprop="position" content="3"${_scopeId}></li></ol></nav>`);
            _push2(ssrRenderComponent(_sfc_main$2, {
              found: __props.useServerProcessing ? __props.productsFound : sortedProducts.value.length,
              "view-mode": viewMode.value,
              "sort-value": sort.value,
              "sort-options": productSortOptions,
              "found-label": unref(t)("products"),
              "onUpdate:viewMode": updateViewMode,
              "onUpdate:sortValue": updateSort
            }, null, _parent2, _scopeId));
            _push2(`<div${_scopeId}></div>`);
            if (displayedProducts.value.length === 0) {
              _push2(`<div class="mt-6 text-center text-slate-700 dark:text-slate-300"${_scopeId}>${ssrInterpolate(unref(t)("noData"))}</div>`);
            } else {
              _push2(`<div${_scopeId}>`);
              if (viewMode.value === "grid") {
                _push2(ssrRenderComponent(_sfc_main$7, {
                  products: displayedProducts.value,
                  cols: productGridCols.value,
                  "start-position": productListStartPosition.value
                }, null, _parent2, _scopeId));
              } else {
                _push2(ssrRenderComponent(_sfc_main$8, {
                  products: displayedProducts.value,
                  "start-position": productListStartPosition.value
                }, null, _parent2, _scopeId));
              }
              _push2(`</div>`);
            }
            if (__props.useServerProcessing) {
              _push2(ssrRenderComponent(_sfc_main$9, {
                "current-page": currentPage.value,
                "last-page": lastPage.value,
                found: __props.productsFound,
                onPrev: goPrev,
                onNext: goNext,
                onGo: goToPage
              }, null, _parent2, _scopeId));
            } else {
              _push2(ssrRenderComponent(_sfc_main$a, {
                currentPage: frontendCurrentPage.value,
                "onUpdate:currentPage": ($event) => frontendCurrentPage.value = $event,
                "items-per-page": perPage.value,
                "total-items": sortedProducts.value.length
              }, null, _parent2, _scopeId));
            }
            _push2(`</article></div></div>`);
            if (showRight.value) {
              _push2(`<aside class="${ssrRenderClass([rightCollapsed.value ? "lg:w-6" : "lg:w-72", "shrink-0 transition-all duration-300"])}"${_scopeId}>`);
              _push2(ssrRenderComponent(_sfc_main$b, {
                collapsed: rightCollapsed.value,
                onCollapsed: ($event) => rightCollapsed.value = $event
              }, null, _parent2, _scopeId));
              _push2(`</aside>`);
            } else {
              _push2(`<!---->`);
            }
            _push2(`</div>`);
            _push2(ssrRenderComponent(_sfc_main$1, { brands: brandCarousel.value }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$c, { products: unref(recentlyViewed) }, null, _parent2, _scopeId));
            _push2(`</div></main>`);
            _push2(ssrRenderComponent(_sfc_main$d, null, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(Progress, null, null, _parent2, _scopeId));
            if (isAdmin.value) {
              _push2(ssrRenderComponent(_sfc_main$e, {
                "setting-key": "publicMarketProductsProcessingMode",
                mode: __props.publicMarketProductsProcessingMode,
                "use-server-processing": __props.useServerProcessing,
                total: __props.productsCount
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
                                }, toDisplayString(unref(t)("products")), 1),
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
                          createVNode(_sfc_main$2, {
                            found: __props.useServerProcessing ? __props.productsFound : sortedProducts.value.length,
                            "view-mode": viewMode.value,
                            "sort-value": sort.value,
                            "sort-options": productSortOptions,
                            "found-label": unref(t)("products"),
                            "onUpdate:viewMode": updateViewMode,
                            "onUpdate:sortValue": updateSort
                          }, null, 8, ["found", "view-mode", "sort-value", "found-label"]),
                          createVNode("div", {
                            ref_key: "scrollTarget",
                            ref: scrollTarget
                          }, null, 512),
                          displayedProducts.value.length === 0 ? (openBlock(), createBlock("div", {
                            key: 3,
                            class: "mt-6 text-center text-slate-700 dark:text-slate-300"
                          }, toDisplayString(unref(t)("noData")), 1)) : (openBlock(), createBlock("div", { key: 4 }, [
                            viewMode.value === "grid" ? (openBlock(), createBlock(_sfc_main$7, {
                              key: 0,
                              products: displayedProducts.value,
                              cols: productGridCols.value,
                              "start-position": productListStartPosition.value
                            }, null, 8, ["products", "cols", "start-position"])) : (openBlock(), createBlock(_sfc_main$8, {
                              key: 1,
                              products: displayedProducts.value,
                              "start-position": productListStartPosition.value
                            }, null, 8, ["products", "start-position"]))
                          ])),
                          __props.useServerProcessing ? (openBlock(), createBlock(_sfc_main$9, {
                            key: 5,
                            "current-page": currentPage.value,
                            "last-page": lastPage.value,
                            found: __props.productsFound,
                            onPrev: goPrev,
                            onNext: goNext,
                            onGo: goToPage
                          }, null, 8, ["current-page", "last-page", "found"])) : (openBlock(), createBlock(_sfc_main$a, {
                            key: 6,
                            currentPage: frontendCurrentPage.value,
                            "onUpdate:currentPage": ($event) => frontendCurrentPage.value = $event,
                            "items-per-page": perPage.value,
                            "total-items": sortedProducts.value.length
                          }, null, 8, ["currentPage", "onUpdate:currentPage", "items-per-page", "total-items"]))
                        ], 8, ["itemid"])
                      ])
                    ]),
                    showRight.value ? (openBlock(), createBlock("aside", {
                      key: 1,
                      class: ["shrink-0 transition-all duration-300", rightCollapsed.value ? "lg:w-6" : "lg:w-72"]
                    }, [
                      createVNode(_sfc_main$b, {
                        collapsed: rightCollapsed.value,
                        onCollapsed: ($event) => rightCollapsed.value = $event
                      }, null, 8, ["collapsed", "onCollapsed"])
                    ], 2)) : createCommentVNode("", true)
                  ]),
                  createVNode(_sfc_main$1, { brands: brandCarousel.value }, null, 8, ["brands"]),
                  createVNode(_sfc_main$c, { products: unref(recentlyViewed) }, null, 8, ["products"])
                ])
              ]),
              createVNode(_sfc_main$d),
              createVNode(Progress),
              isAdmin.value ? (openBlock(), createBlock(_sfc_main$e, {
                key: 0,
                "setting-key": "publicMarketProductsProcessingMode",
                mode: __props.publicMarketProductsProcessingMode,
                "use-server-processing": __props.useServerProcessing,
                total: __props.productsCount
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
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Pages/Public/Default/Market/MarketProducts/Index.vue");
  return _sfc_setup ? _sfc_setup(props, ctx) : void 0;
};
export {
  _sfc_main as default
};
