import { computed, ref, watch, onMounted, onBeforeUnmount, mergeProps, unref, nextTick, useSSRContext, withCtx, createVNode, toDisplayString, createBlock, createCommentVNode, openBlock, createTextVNode, withDirectives, Fragment, renderList, vModelSelect } from "vue";
import { ssrRenderAttrs, ssrRenderAttr, ssrInterpolate, ssrRenderList, ssrRenderClass, ssrRenderTeleport, ssrRenderStyle, ssrIncludeBooleanAttr, ssrRenderComponent, ssrLooseContain, ssrLooseEqual } from "vue/server-renderer";
import { Link, usePage, Head } from "@inertiajs/vue3";
import { useI18n } from "vue-i18n";
import { _ as _sfc_main$3 } from "./DefaultLayout-6zxGxPtK.js";
import { _ as _sfc_main$4, a as _sfc_main$8, P as Progress } from "./Progress-CyfOKcqP.js";
import { _ as _sfc_main$5, a as _sfc_main$6 } from "./RightSidebarMarket-DpLr4Pjm.js";
import { L as LikeButtonEntity } from "./LikeButtonEntity-ZC4HMEAO.js";
import { U as UniversalImageSlider } from "./UniversalImageSlider-Cu2Xndcn.js";
import { u as useRecentlyViewedProducts, _ as _sfc_main$7 } from "./useRecentlyViewedProducts-CPkZCnFT.js";
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
const DEFAULT_ZOOM_INDEX = 2;
const _sfc_main$2 = {
  __name: "MarketProductGallery",
  __ssrInlineRender: true,
  props: {
    images: {
      type: [Array, Object],
      default: () => []
    },
    title: {
      type: String,
      default: ""
    }
  },
  setup(__props) {
    const { t } = useI18n();
    const props = __props;
    const imagesList = computed(() => {
      var _a;
      if (Array.isArray(props.images)) {
        return props.images;
      }
      if (Array.isArray((_a = props.images) == null ? void 0 : _a.data)) {
        return props.images.data;
      }
      return [];
    });
    const totalImages = computed(() => imagesList.value.length);
    const currentIndex = ref(0);
    const currentImage = computed(() => {
      return imagesList.value[currentIndex.value] ?? null;
    });
    const getImageUrl = (image) => {
      return (image == null ? void 0 : image.url) || (image == null ? void 0 : image.original_url) || (image == null ? void 0 : image.full_url) || (image == null ? void 0 : image.image_url) || (image == null ? void 0 : image.src) || "";
    };
    const getThumbnailUrl = (image) => {
      return (image == null ? void 0 : image.thumb_url) || (image == null ? void 0 : image.thumbnail_url) || (image == null ? void 0 : image.small_url) || getImageUrl(image);
    };
    const getImageAlt = (image, index) => {
      return (image == null ? void 0 : image.alt) || (image == null ? void 0 : image.title) || props.title || `Image ${index + 1}`;
    };
    const thumbnailsRef = ref(null);
    const fullscreenThumbnailsRef = ref(null);
    const scrollThumbnailContainer = async (containerRef) => {
      await nextTick();
      const container = containerRef.value;
      if (!container) {
        return;
      }
      const activeThumbnail = container.querySelector(
        `[data-thumbnail-index="${currentIndex.value}"]`
      );
      activeThumbnail == null ? void 0 : activeThumbnail.scrollIntoView({
        behavior: "smooth",
        block: "nearest",
        inline: "center"
      });
    };
    const scrollActiveThumbnail = () => {
      scrollThumbnailContainer(thumbnailsRef);
      scrollThumbnailContainer(fullscreenThumbnailsRef);
    };
    const prev = () => {
      if (totalImages.value <= 1) {
        return;
      }
      currentIndex.value = currentIndex.value <= 0 ? totalImages.value - 1 : currentIndex.value - 1;
      scrollActiveThumbnail();
    };
    const next = () => {
      if (totalImages.value <= 1) {
        return;
      }
      currentIndex.value = currentIndex.value >= totalImages.value - 1 ? 0 : currentIndex.value + 1;
      scrollActiveThumbnail();
    };
    const isFullscreen = ref(false);
    ref(null);
    const closeFullscreen = () => {
      isFullscreen.value = false;
    };
    const zoomLevels = [
      0.5,
      0.75,
      1,
      1.25,
      1.5,
      1.75,
      2,
      2.25,
      2.5,
      2.75,
      3
    ];
    const zoomIndex = ref(DEFAULT_ZOOM_INDEX);
    const zoomScale = computed(() => {
      return zoomLevels[zoomIndex.value];
    });
    const zoomPercent = computed(() => {
      return Math.round(
        zoomScale.value * 100
      );
    });
    const canZoomIn = computed(() => {
      return zoomIndex.value < zoomLevels.length - 1;
    });
    const canZoomOut = computed(() => {
      return zoomIndex.value > 0;
    });
    const resetZoom = () => {
      zoomIndex.value = DEFAULT_ZOOM_INDEX;
    };
    const handleKeydown = (event) => {
      if (!isFullscreen.value) {
        return;
      }
      if (event.key === "Escape") {
        closeFullscreen();
        return;
      }
      if (event.key === "ArrowLeft") {
        prev();
        return;
      }
      if (event.key === "ArrowRight") {
        next();
      }
    };
    ref(0);
    ref(0);
    watch(imagesList, () => {
      if (currentIndex.value >= totalImages.value) {
        currentIndex.value = 0;
      }
      resetZoom();
    });
    watch(isFullscreen, (value) => {
      if (typeof document === "undefined") {
        return;
      }
      document.body.style.overflow = value ? "hidden" : "";
    });
    onMounted(() => {
      window.addEventListener(
        "keydown",
        handleKeydown
      );
    });
    onBeforeUnmount(() => {
      window.removeEventListener(
        "keydown",
        handleKeydown
      );
      if (typeof document !== "undefined") {
        document.body.style.overflow = "";
      }
    });
    return (_ctx, _push, _parent, _attrs) => {
      _push(`<div${ssrRenderAttrs(mergeProps({ class: "w-full min-w-0" }, _attrs))}>`);
      if (totalImages.value) {
        _push(`<div class="flex flex-col gap-3"><div class="relative min-w-0 overflow-hidden"><div class="flex h-[180px] w-full items-center justify-center sm:h-[410px] lg:h-[360px]"><img${ssrRenderAttr("src", getImageUrl(currentImage.value))}${ssrRenderAttr("alt", getImageAlt(currentImage.value, currentIndex.value))} class="max-h-full max-w-full object-contain"></div><div class="absolute right-3 top-3 z-10 flex items-center gap-2">`);
        if (totalImages.value > 1) {
          _push(`<div class="flex h-8 items-center justify-center rounded-sm bg-black/55 px-3 text-xs font-semibold text-white shadow-sm backdrop-blur-sm">${ssrInterpolate(currentIndex.value + 1)} / ${ssrInterpolate(totalImages.value)}</div>`);
        } else {
          _push(`<!---->`);
        }
        _push(`<button type="button" class="flex h-8 w-8 items-center justify-center rounded-sm bg-black/55 text-white shadow-sm backdrop-blur-sm transition hover:bg-black/75 focus:outline-none" aria-label="Open image fullscreen"><svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M8 3H5a2 2 0 0 0-2 2v3"></path><path d="M16 3h3a2 2 0 0 1 2 2v3"></path><path d="M8 21H5a2 2 0 0 1-2-2v-3"></path><path d="M16 21h3a2 2 0 0 0 2-2v-3"></path></svg></button></div></div>`);
        if (totalImages.value > 1) {
          _push(`<div class="flex w-full items-center gap-2"><button type="button" class="flex h-16 w-10 shrink-0 items-center justify-center rounded-sm border border-gray-400 bg-white text-slate-600 shadow-sm transition hover:border-indigo-400 hover:bg-slate-100 hover:text-indigo-500 focus:outline-none dark:border-gray-600 dark:bg-slate-900 dark:text-slate-300 dark:hover:border-indigo-500 dark:hover:bg-slate-800" aria-label="Previous image"><svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m15 18-6-6 6-6"></path></svg></button><div class="flex min-w-0 flex-1 justify-center gap-2 overflow-x-auto scroll-smooth [scrollbar-width:none] [&amp;::-webkit-scrollbar]:hidden"><!--[-->`);
          ssrRenderList(imagesList.value, (image, index) => {
            _push(`<button${ssrRenderAttr("data-thumbnail-index", index)} type="button" class="${ssrRenderClass([currentIndex.value === index ? "border-indigo-500 shadow-sm" : "border-gray-300 dark:border-gray-700", "relative h-16 w-16 shrink-0 overflow-hidden rounded-sm border-2 bg-white transition hover:border-indigo-400 dark:bg-slate-950"])}"${ssrRenderAttr("aria-label", `Image ${index + 1}`)}><img${ssrRenderAttr("src", getThumbnailUrl(image))}${ssrRenderAttr("alt", getImageAlt(image, index))} loading="lazy" class="h-full w-full object-contain p-1">`);
            if (currentIndex.value === index) {
              _push(`<span class="pointer-events-none absolute inset-0 ring-1 ring-inset ring-indigo-500"></span>`);
            } else {
              _push(`<!---->`);
            }
            _push(`</button>`);
          });
          _push(`<!--]--></div><button type="button" class="flex h-16 w-10 shrink-0 items-center justify-center rounded-sm border border-gray-400 bg-white text-slate-600 shadow-sm transition hover:border-indigo-400 hover:bg-slate-100 hover:text-indigo-500 focus:outline-none dark:border-gray-600 dark:bg-slate-900 dark:text-slate-300 dark:hover:border-indigo-500 dark:hover:bg-slate-800" aria-label="Next image"><svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m9 18 6-6-6-6"></path></svg></button></div>`);
        } else {
          _push(`<!---->`);
        }
        _push(`</div>`);
      } else {
        _push(`<div class="flex min-h-72 items-center justify-center rounded-sm border border-gray-300 bg-slate-100 text-sm text-slate-400 dark:border-gray-700 dark:bg-slate-800">${ssrInterpolate(unref(t)("noImage"))}</div>`);
      }
      ssrRenderTeleport(_push, (_push2) => {
        if (isFullscreen.value) {
          _push2(`<div tabindex="-1" role="dialog" aria-modal="true" class="fixed inset-0 z-[100] flex flex-col bg-gray-800/95 outline-none"><div class="relative z-30 flex h-16 shrink-0 items-center justify-between border-b border-white/10 bg-black/20 px-4 text-white"><div class="text-sm font-semibold text-white/80">${ssrInterpolate(currentIndex.value + 1)} / ${ssrInterpolate(totalImages.value)}</div><div class="flex items-center gap-2"><div class="hidden min-w-14 text-center text-xs font-semibold text-white/70 sm:block">${ssrInterpolate(zoomPercent.value)}% </div><button type="button"${ssrIncludeBooleanAttr(!canZoomOut.value) ? " disabled" : ""} class="flex h-10 w-10 items-center justify-center rounded-sm bg-white/10 transition hover:bg-white/20 disabled:cursor-not-allowed disabled:opacity-30" aria-label="Zoom out"><svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="7"></circle><path d="m20 20-3.5-3.5"></path><path d="M8 11h6"></path></svg></button><button type="button"${ssrIncludeBooleanAttr(!canZoomIn.value) ? " disabled" : ""} class="flex h-10 w-10 items-center justify-center rounded-sm bg-white/10 transition hover:bg-white/20 disabled:cursor-not-allowed disabled:opacity-30" aria-label="Zoom in"><svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="7"></circle><path d="m20 20-3.5-3.5"></path><path d="M11 8v6"></path><path d="M8 11h6"></path></svg></button><button type="button" class="hidden h-10 items-center justify-center rounded-sm bg-white/10 px-3 text-xs font-semibold transition hover:bg-white/20 sm:flex" aria-label="Reset zoom"> 100% </button><button type="button" class="flex h-10 w-10 items-center justify-center rounded-sm bg-white/10 transition hover:bg-white/20" aria-label="Close"><svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 6 6 18"></path><path d="m6 6 12 12"></path></svg></button></div></div><div class="relative flex min-h-0 flex-1 items-center justify-center overflow-auto px-16 py-5"><div class="flex min-h-full min-w-full items-center justify-center"><img${ssrRenderAttr("src", getImageUrl(currentImage.value))}${ssrRenderAttr("alt", getImageAlt(
            currentImage.value,
            currentIndex.value
          ))} class="max-h-[calc(100vh-11rem)] max-w-[calc(100vw-10rem)] object-contain transition-transform duration-200 ease-out select-none" style="${ssrRenderStyle({
            transform: `scale(${zoomScale.value})`
          })}" draggable="false"></div>`);
          if (totalImages.value > 1) {
            _push2(`<button type="button" class="fixed left-4 top-1/2 z-20 flex h-12 w-12 -translate-y-1/2 items-center justify-center rounded-sm bg-white/10 text-white backdrop-blur-sm transition hover:bg-white/20" aria-label="Previous image"><svg class="h-7 w-7" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m15 18-6-6 6-6"></path></svg></button>`);
          } else {
            _push2(`<!---->`);
          }
          if (totalImages.value > 1) {
            _push2(`<button type="button" class="fixed right-4 top-1/2 z-20 flex h-12 w-12 -translate-y-1/2 items-center justify-center rounded-sm bg-white/10 text-white backdrop-blur-sm transition hover:bg-white/20" aria-label="Next image"><svg class="h-7 w-7" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m9 18 6-6-6-6"></path></svg></button>`);
          } else {
            _push2(`<!---->`);
          }
          _push2(`</div>`);
          if (totalImages.value > 1) {
            _push2(`<div class="relative z-30 shrink-0 border-t border-white/10 bg-black/30 px-4 py-3"><div class="mx-auto flex max-w-5xl items-center gap-2"><button type="button" class="flex h-16 w-10 shrink-0 items-center justify-center rounded-sm bg-white/10 text-white transition hover:bg-white/20" aria-label="Previous image"><svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m15 18-6-6 6-6"></path></svg></button><div class="flex min-w-0 flex-1 items-center justify-center gap-2 overflow-x-auto scroll-smooth [scrollbar-width:none] [&amp;::-webkit-scrollbar]:hidden"><!--[-->`);
            ssrRenderList(imagesList.value, (image, index) => {
              _push2(`<button${ssrRenderAttr("data-thumbnail-index", index)} type="button" class="${ssrRenderClass([currentIndex.value === index ? "border-white opacity-100" : "border-transparent opacity-60 hover:opacity-100", "h-16 w-16 shrink-0 overflow-hidden rounded-sm border-2 bg-white/5 transition"])}"${ssrRenderAttr("aria-label", `Image ${index + 1}`)}><img${ssrRenderAttr("src", getThumbnailUrl(image))}${ssrRenderAttr("alt", getImageAlt(image, index))} loading="lazy" class="h-full w-full object-contain p-1"></button>`);
            });
            _push2(`<!--]--></div><button type="button" class="flex h-16 w-10 shrink-0 items-center justify-center rounded-sm bg-white/10 text-white transition hover:bg-white/20" aria-label="Next image"><svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m9 18 6-6-6-6"></path></svg></button></div></div>`);
          } else {
            _push2(`<!---->`);
          }
          _push2(`</div>`);
        } else {
          _push2(`<!---->`);
        }
      }, "body", false, _parent);
      _push(`</div>`);
    };
  }
};
const _sfc_setup$2 = _sfc_main$2.setup;
_sfc_main$2.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Components/Public/Default/Market/MarketProduct/MarketProductGallery.vue");
  return _sfc_setup$2 ? _sfc_setup$2(props, ctx) : void 0;
};
const _sfc_main$1 = {
  __name: "MarketRecommendedProducts",
  __ssrInlineRender: true,
  props: {
    products: {
      type: Array,
      default: () => []
    },
    cols: {
      type: Number,
      default: 4
    },
    locale: {
      type: String,
      default: "ru"
    }
  },
  setup(__props) {
    const { t } = useI18n();
    const props = __props;
    const cardClass = computed(() => {
      switch (props.cols) {
        case 4:
          return "w-full sm:w-[calc(50%-0.5rem)] xl:w-[calc(25%-0.75rem)]";
        case 3:
          return "w-full sm:w-[calc(50%-0.5rem)] xl:w-[calc(33.333%-0.7rem)]";
        case 2:
        default:
          return "w-full sm:w-[calc(50%-0.5rem)]";
      }
    });
    const productLink = (product) => {
      return (product == null ? void 0 : product.url) ? route("public.marketProducts.show", { url: product.url }) : "#";
    };
    const getProductTitle = (product) => {
      var _a, _b, _c, _d;
      return (product == null ? void 0 : product.title) || ((_a = product == null ? void 0 : product.translation) == null ? void 0 : _a.title) || ((_b = product == null ? void 0 : product.current_translation) == null ? void 0 : _b.title) || ((_d = (_c = product == null ? void 0 : product.translations) == null ? void 0 : _c[0]) == null ? void 0 : _d.title) || "";
    };
    const getProductShort = (product) => {
      var _a, _b, _c, _d, _e, _f, _g, _h;
      return (product == null ? void 0 : product.short) || ((_a = product == null ? void 0 : product.translation) == null ? void 0 : _a.short) || ((_b = product == null ? void 0 : product.translation) == null ? void 0 : _b.description) || ((_c = product == null ? void 0 : product.current_translation) == null ? void 0 : _c.short) || ((_d = product == null ? void 0 : product.current_translation) == null ? void 0 : _d.description) || ((_f = (_e = product == null ? void 0 : product.translations) == null ? void 0 : _e[0]) == null ? void 0 : _f.short) || ((_h = (_g = product == null ? void 0 : product.translations) == null ? void 0 : _g[0]) == null ? void 0 : _h.description) || "";
    };
    const getBrandTitle = (product) => {
      var _a;
      return ((_a = product == null ? void 0 : product.brand) == null ? void 0 : _a.title) || "";
    };
    const getPrice = (product) => {
      const value = Number(product == null ? void 0 : product.price);
      return Number.isFinite(value) ? value : 0;
    };
    const getOldPrice = (product) => {
      const value = Number(product == null ? void 0 : product.old_price);
      return Number.isFinite(value) ? value : 0;
    };
    const hasOldPrice = (product) => {
      return getOldPrice(product) > getPrice(product);
    };
    const getCurrency = (product) => {
      var _a, _b, _c;
      return ((_a = product == null ? void 0 : product.currency) == null ? void 0 : _a.symbol) || ((_b = product == null ? void 0 : product.currency) == null ? void 0 : _b.sign) || ((_c = product == null ? void 0 : product.currency) == null ? void 0 : _c.code) || "";
    };
    const formatPrice = (value) => {
      const number = Number(value);
      if (!Number.isFinite(number)) {
        return "0";
      }
      return new Intl.NumberFormat(props.locale, {
        minimumFractionDigits: 0,
        maximumFractionDigits: 2
      }).format(number);
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
      const value = Number((product == null ? void 0 : product.rating_avg) ?? 0);
      return Number.isFinite(value) ? value : 0;
    };
    const reviewsCount = (product) => {
      return Number((product == null ? void 0 : product.reviews_count) ?? 0);
    };
    const hasMarketingFlags = (product) => {
      return Boolean(
        (product == null ? void 0 : product.is_new) || (product == null ? void 0 : product.is_hit) || (product == null ? void 0 : product.is_sale)
      );
    };
    return (_ctx, _push, _parent, _attrs) => {
      _push(`<div${ssrRenderAttrs(mergeProps({ class: "flex flex-wrap justify-center gap-4" }, _attrs))}><!--[-->`);
      ssrRenderList(__props.products, (product) => {
        _push(`<div class="${ssrRenderClass([cardClass.value, "group flex h-full flex-col overflow-hidden rounded-md border border-gray-200 bg-white shadow-sm transition hover:-translate-y-0.5 hover:shadow-md dark:border-gray-700 dark:bg-gray-900"])}"><div class="relative">`);
        _push(ssrRenderComponent(unref(Link), {
          href: productLink(product)
        }, {
          default: withCtx((_, _push2, _parent2, _scopeId) => {
            if (_push2) {
              _push2(ssrRenderComponent(UniversalImageSlider, {
                entity: product,
                "height-class": "h-48",
                "rounded-class": "",
                "wrapper-class": "",
                "img-class": "w-full h-full object-cover transition\n                                   duration-300 group-hover:scale-105"
              }, null, _parent2, _scopeId));
            } else {
              return [
                createVNode(UniversalImageSlider, {
                  entity: product,
                  "height-class": "h-48",
                  "rounded-class": "",
                  "wrapper-class": "",
                  "img-class": "w-full h-full object-cover transition\n                                   duration-300 group-hover:scale-105"
                }, null, 8, ["entity"])
              ];
            }
          }),
          _: 2
        }, _parent));
        if (hasMarketingFlags(product)) {
          _push(`<div class="absolute left-2 top-2 z-10 flex flex-wrap gap-1">`);
          if (product.is_new) {
            _push(`<span class="rounded-sm bg-teal-500 px-1 py-0.5 text-[9px] font-bold uppercase tracking-wide text-white shadow-sm"> NEW </span>`);
          } else {
            _push(`<!---->`);
          }
          if (product.is_hit) {
            _push(`<span class="rounded-sm bg-amber-500 px-1 py-0.5 text-[9px] font-bold uppercase tracking-wide text-slate-900 shadow-sm"> HIT </span>`);
          } else {
            _push(`<!---->`);
          }
          if (product.is_sale) {
            _push(`<span class="rounded-sm bg-red-500 px-1 py-0.5 text-[9px] font-bold uppercase tracking-wide text-white shadow-sm"> SALE </span>`);
          } else {
            _push(`<!---->`);
          }
          _push(`</div>`);
        } else {
          _push(`<!---->`);
        }
        _push(`</div><div class="flex flex-1 flex-col px-1 pb-1.5 pt-6">`);
        if (getBrandTitle(product)) {
          _push(`<div class="mb-0.5 flex items-center justify-center"><span class="text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">${ssrInterpolate(getBrandTitle(product))}</span></div>`);
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
              _push2(`<span class="text-xs font-semibold text-slate-900/85 group-hover:opacity-75 dark:text-slate-100/85 dark:group-hover:opacity-75"${_scopeId}>${ssrInterpolate(getProductTitle(product))}</span>`);
            } else {
              return [
                createVNode("span", { class: "text-xs font-semibold text-slate-900/85 group-hover:opacity-75 dark:text-slate-100/85 dark:group-hover:opacity-75" }, toDisplayString(getProductTitle(product)), 1)
              ];
            }
          }),
          _: 2
        }, _parent));
        _push(`</div><div class="mt-1 flex flex-col items-center justify-center"><span class="text-sm font-bold text-teal-600 dark:text-teal-400">${ssrInterpolate(formatPrice(getPrice(product)))} ${ssrInterpolate(getCurrency(product))}</span>`);
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
        _push(`</div><div class="mt-1 flex items-center justify-center">`);
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
        _push(`</div></div></div>`);
      });
      _push(`<!--]--></div>`);
    };
  }
};
const _sfc_setup$1 = _sfc_main$1.setup;
_sfc_main$1.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Components/Public/Default/Market/MarketProduct/MarketRecommendedProducts.vue");
  return _sfc_setup$1 ? _sfc_setup$1(props, ctx) : void 0;
};
const LEFT_SIDEBAR_KEY = "public_left_sidebar_collapsed";
const RIGHT_SIDEBAR_KEY = "public_right_sidebar_collapsed";
const _sfc_main = {
  __name: "Show",
  __ssrInlineRender: true,
  props: {
    locale: {
      type: String,
      default: ""
    },
    product: {
      type: Object,
      default: () => ({})
    },
    breadcrumbCategory: {
      type: Object,
      default: null
    },
    categoryTree: {
      type: Array,
      default: () => []
    },
    /** Недавно просмотренные товары */
    recentlyViewedProducts: {
      type: [Array, Object],
      default: () => []
    }
  },
  setup(__props) {
    var _a, _b;
    const { t } = useI18n();
    const page = usePage();
    const props = __props;
    const siteSettings = ((_a = page.props) == null ? void 0 : _a.siteSettings) || {};
    const productData = computed(() => {
      return props.product ?? {};
    });
    const categoryTree = computed(() => {
      return Array.isArray(props.categoryTree) ? props.categoryTree : [];
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
    const {
      products: recentlyViewed,
      rememberGuestProduct,
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
      var _a2, _b2, _c, _d, _e;
      if ((_b2 = (_a2 = page.props) == null ? void 0 : _a2.auth) == null ? void 0 : _b2.user) {
        setRecentlyViewedProducts(
          initialRecentlyViewedProducts.value
        );
        await mergeGuestHistory(
          (_c = productData.value) == null ? void 0 : _c.id
        );
        return;
      }
      await loadRecentlyViewedProducts(
        (_d = productData.value) == null ? void 0 : _d.id
      );
      rememberGuestProduct(
        (_e = productData.value) == null ? void 0 : _e.id
      );
    });
    const translation = computed(() => {
      var _a2;
      return ((_a2 = productData.value) == null ? void 0 : _a2.translation) ?? null;
    });
    const productTitle = computed(() => {
      var _a2;
      return ((_a2 = translation.value) == null ? void 0 : _a2.title) || "";
    });
    const productSubtitle = computed(() => {
      var _a2;
      return ((_a2 = translation.value) == null ? void 0 : _a2.subtitle) || "";
    });
    const productShort = computed(() => {
      var _a2;
      return ((_a2 = translation.value) == null ? void 0 : _a2.short) || "";
    });
    const productDescription = computed(() => {
      var _a2;
      return ((_a2 = translation.value) == null ? void 0 : _a2.description) || "";
    });
    const seoTitle = computed(() => {
      var _a2;
      return ((_a2 = translation.value) == null ? void 0 : _a2.meta_title) || productTitle.value || t("products");
    });
    const seoKeywords = computed(() => {
      var _a2;
      return ((_a2 = translation.value) == null ? void 0 : _a2.meta_keywords) || "";
    });
    const seoDescription = computed(() => {
      var _a2;
      return ((_a2 = translation.value) == null ? void 0 : _a2.meta_description) || productShort.value || "";
    });
    const canonicalUrl = computed(() => {
      var _a2;
      if (!((_a2 = productData.value) == null ? void 0 : _a2.url)) {
        return "";
      }
      return String(
        route("public.marketProducts.show", {
          url: productData.value.url
        })
      );
    });
    const contentLocale = computed(() => {
      return props.locale || "";
    });
    const openGraphLocale = computed(() => {
      return contentLocale.value ? contentLocale.value.replace("-", "_") : "";
    });
    const brandTitle = computed(() => {
      var _a2, _b2, _c;
      return ((_c = (_b2 = (_a2 = productData.value) == null ? void 0 : _a2.brand) == null ? void 0 : _b2.translation) == null ? void 0 : _c.title) || "";
    });
    const hasAggregateRating = computed(() => {
      return rating.value > 0 && ratingCount.value > 0;
    });
    const breadcrumbCategoryTitle = computed(() => {
      var _a2, _b2;
      return ((_b2 = (_a2 = props.breadcrumbCategory) == null ? void 0 : _a2.translation) == null ? void 0 : _b2.title) || "";
    });
    const primaryImage = computed(() => {
      return productImages.value[0] ?? null;
    });
    const primaryImageUrl = computed(() => {
      const image = primaryImage.value;
      return (image == null ? void 0 : image.url) || (image == null ? void 0 : image.original_url) || (image == null ? void 0 : image.full_url) || (image == null ? void 0 : image.image_url) || (image == null ? void 0 : image.src) || "";
    });
    const currencyCode = computed(() => {
      var _a2;
      return ((_a2 = activeCurrency.value) == null ? void 0 : _a2.code) || "";
    });
    const schemaAvailability = computed(() => {
      return hasStock.value ? "https://schema.org/InStock" : "https://schema.org/OutOfStock";
    });
    const productAvailability = computed(() => {
      return hasStock.value ? "in stock" : "out of stock";
    });
    const productImages = computed(() => {
      var _a2;
      return normalizeList((_a2 = productData.value) == null ? void 0 : _a2.images);
    });
    const categories = computed(() => {
      var _a2;
      return normalizeList((_a2 = productData.value) == null ? void 0 : _a2.categories);
    });
    const tags = computed(() => {
      var _a2;
      return normalizeList((_a2 = productData.value) == null ? void 0 : _a2.tags);
    });
    const attributeValues = computed(() => {
      var _a2;
      return normalizeList((_a2 = productData.value) == null ? void 0 : _a2.attribute_values);
    });
    const publicVariants = computed(() => {
      var _a2;
      return normalizeList((_a2 = productData.value) == null ? void 0 : _a2.public_variants);
    });
    const relatedProducts = computed(() => {
      var _a2;
      return normalizeList((_a2 = productData.value) == null ? void 0 : _a2.related_products);
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
    const productMainGridClass = computed(() => {
      const leftExpanded = showLeft.value && !leftCollapsed.value;
      const rightExpanded = showRight.value && !rightCollapsed.value;
      if (leftExpanded && rightExpanded) {
        return "grid-cols-1";
      }
      return "grid-cols-1 lg:grid-cols-2";
    });
    const relatedGridCols = computed(() => {
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
    const defaultPublicVariant = computed(() => {
      if (!publicVariants.value.length) {
        return null;
      }
      return publicVariants.value.find(
        (variant) => variant == null ? void 0 : variant.is_default
      ) || publicVariants.value[0];
    });
    const selectedVariantId = ref(
      ((_b = defaultPublicVariant.value) == null ? void 0 : _b.id) ?? null
    );
    const selectedVariant = computed(() => {
      if (!selectedVariantId.value) {
        return null;
      }
      return publicVariants.value.find(
        (variant) => Number(variant.id) === Number(selectedVariantId.value)
      ) || null;
    });
    watch(publicVariants, () => {
      var _a2;
      if (selectedVariantId.value && publicVariants.value.some(
        (variant) => Number(variant.id) === Number(selectedVariantId.value)
      )) {
        return;
      }
      selectedVariantId.value = ((_a2 = defaultPublicVariant.value) == null ? void 0 : _a2.id) ?? null;
    });
    const getVariantTitle = (variant) => {
      var _a2;
      return (variant == null ? void 0 : variant.display_title) || ((_a2 = variant == null ? void 0 : variant.translation) == null ? void 0 : _a2.title) || (variant == null ? void 0 : variant.code) || (variant == null ? void 0 : variant.sku) || `#${(variant == null ? void 0 : variant.id) ?? ""}`;
    };
    const toNumber = (value) => {
      const number = Number(value);
      return Number.isFinite(number) ? number : 0;
    };
    const currentPrice = computed(() => {
      var _a2;
      if (selectedVariant.value && selectedVariant.value.effective_price !== null && selectedVariant.value.effective_price !== void 0) {
        return toNumber(
          selectedVariant.value.effective_price
        );
      }
      return toNumber((_a2 = productData.value) == null ? void 0 : _a2.price);
    });
    const currentOldPrice = computed(() => {
      var _a2;
      if (selectedVariant.value && selectedVariant.value.effective_old_price !== null && selectedVariant.value.effective_old_price !== void 0) {
        return toNumber(
          selectedVariant.value.effective_old_price
        );
      }
      return toNumber((_a2 = productData.value) == null ? void 0 : _a2.old_price);
    });
    const hasOldPrice = computed(() => {
      return currentOldPrice.value > currentPrice.value;
    });
    const formatPrice = (value) => {
      return new Intl.NumberFormat(props.locale, {
        minimumFractionDigits: 0,
        maximumFractionDigits: 2
      }).format(toNumber(value));
    };
    const productCurrency = computed(() => {
      var _a2;
      return ((_a2 = productData.value) == null ? void 0 : _a2.currency) ?? null;
    });
    const selectedVariantCurrency = computed(() => {
      var _a2;
      return ((_a2 = selectedVariant.value) == null ? void 0 : _a2.currency) ?? null;
    });
    const activeCurrency = computed(() => {
      return selectedVariantCurrency.value || productCurrency.value || null;
    });
    const currencyLabel = computed(() => {
      var _a2, _b2, _c;
      return ((_a2 = activeCurrency.value) == null ? void 0 : _a2.symbol) || ((_b2 = activeCurrency.value) == null ? void 0 : _b2.sign) || ((_c = activeCurrency.value) == null ? void 0 : _c.code) || "";
    });
    const hasStock = computed(() => {
      var _a2, _b2, _c, _d;
      if (selectedVariant.value) {
        return Boolean((_a2 = selectedVariant.value) == null ? void 0 : _a2.has_stock);
      }
      return Boolean(
        ((_b2 = productData.value) == null ? void 0 : _b2.has_stock) ?? (((_c = productData.value) == null ? void 0 : _c.in_stock) && Number(((_d = productData.value) == null ? void 0 : _d.quantity) ?? 0) > 0)
      );
    });
    const quantity = computed(() => {
      var _a2, _b2;
      if (selectedVariant.value) {
        return Number(((_a2 = selectedVariant.value) == null ? void 0 : _a2.quantity) ?? 0);
      }
      return Number(((_b2 = productData.value) == null ? void 0 : _b2.quantity) ?? 0);
    });
    const wholesalePrice = computed(() => {
      var _a2;
      if (selectedVariant.value && selectedVariant.value.effective_wholesale_price !== null && selectedVariant.value.effective_wholesale_price !== void 0) {
        return toNumber(
          selectedVariant.value.effective_wholesale_price
        );
      }
      return toNumber((_a2 = productData.value) == null ? void 0 : _a2.wholesale_price);
    });
    const wholesaleMinQuantity = computed(() => {
      var _a2;
      if (selectedVariant.value && selectedVariant.value.effective_wholesale_min_quantity !== null && selectedVariant.value.effective_wholesale_min_quantity !== void 0) {
        return Number(
          selectedVariant.value.effective_wholesale_min_quantity
        );
      }
      return Number(
        ((_a2 = productData.value) == null ? void 0 : _a2.wholesale_min_quantity) ?? 0
      );
    });
    const hasWholesalePrice = computed(() => {
      return wholesalePrice.value > 0 && wholesaleMinQuantity.value > 0;
    });
    const rating = computed(() => {
      var _a2;
      return toNumber((_a2 = productData.value) == null ? void 0 : _a2.rating_avg);
    });
    const ratingCount = computed(() => {
      var _a2;
      return Number(((_a2 = productData.value) == null ? void 0 : _a2.rating_count) ?? 0);
    });
    const getAttributeValue = (item) => {
      var _a2;
      if ((_a2 = item == null ? void 0 : item.attribute_value) == null ? void 0 : _a2.title) {
        return item.attribute_value.title;
      }
      if ((item == null ? void 0 : item.value_string) !== null && (item == null ? void 0 : item.value_string) !== void 0 && (item == null ? void 0 : item.value_string) !== "") {
        return item.value_string;
      }
      if ((item == null ? void 0 : item.value_number) !== null && (item == null ? void 0 : item.value_number) !== void 0) {
        return `${item.value_number}${item.unit ? ` ${item.unit}` : ""}`;
      }
      if ((item == null ? void 0 : item.value_boolean) !== null && (item == null ? void 0 : item.value_boolean) !== void 0) {
        return item.value_boolean ? t("yes") : t("no");
      }
      if (item == null ? void 0 : item.value_date) {
        return item.value_date;
      }
      if (item == null ? void 0 : item.value_json) {
        return typeof item.value_json === "string" ? item.value_json : JSON.stringify(item.value_json);
      }
      return "—";
    };
    const hasPhysicalParameters = computed(() => {
      var _a2, _b2, _c, _d;
      return Boolean(
        ((_a2 = productData.value) == null ? void 0 : _a2.weight) || ((_b2 = productData.value) == null ? void 0 : _b2.length) || ((_c = productData.value) == null ? void 0 : _c.width) || ((_d = productData.value) == null ? void 0 : _d.height)
      );
    });
    const hasMarketingFlags = computed(() => {
      var _a2, _b2, _c;
      return Boolean(
        ((_a2 = productData.value) == null ? void 0 : _a2.is_new) || ((_b2 = productData.value) == null ? void 0 : _b2.is_hit) || ((_c = productData.value) == null ? void 0 : _c.is_sale)
      );
    });
    return (_ctx, _push, _parent, _attrs) => {
      _push(`<!--[-->`);
      _push(ssrRenderComponent(unref(Head), null, {
        default: withCtx((_, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(`<title${_scopeId}>${ssrInterpolate(seoTitle.value)}</title>`);
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
            _push2(`<meta name="robots" content="index, follow, max-image-preview:large"${_scopeId}>`);
            if (contentLocale.value) {
              _push2(`<meta http-equiv="content-language"${ssrRenderAttr("content", contentLocale.value)}${_scopeId}>`);
            } else {
              _push2(`<!---->`);
            }
            if (canonicalUrl.value) {
              _push2(`<link rel="canonical"${ssrRenderAttr("href", canonicalUrl.value)}${_scopeId}>`);
            } else {
              _push2(`<!---->`);
            }
            _push2(`<meta property="og:type" content="product"${_scopeId}><meta property="og:title"${ssrRenderAttr("content", seoTitle.value)}${_scopeId}>`);
            if (seoDescription.value) {
              _push2(`<meta property="og:description"${ssrRenderAttr("content", seoDescription.value)}${_scopeId}>`);
            } else {
              _push2(`<!---->`);
            }
            if (canonicalUrl.value) {
              _push2(`<meta property="og:url"${ssrRenderAttr("content", canonicalUrl.value)}${_scopeId}>`);
            } else {
              _push2(`<!---->`);
            }
            if (openGraphLocale.value) {
              _push2(`<meta property="og:locale"${ssrRenderAttr("content", openGraphLocale.value)}${_scopeId}>`);
            } else {
              _push2(`<!---->`);
            }
            if (primaryImageUrl.value) {
              _push2(`<meta property="og:image"${ssrRenderAttr("content", primaryImageUrl.value)}${_scopeId}>`);
            } else {
              _push2(`<!---->`);
            }
            if (productTitle.value) {
              _push2(`<meta property="og:image:alt"${ssrRenderAttr("content", productTitle.value)}${_scopeId}>`);
            } else {
              _push2(`<!---->`);
            }
            if (currentPrice.value > 0) {
              _push2(`<meta property="product:price:amount"${ssrRenderAttr("content", String(currentPrice.value))}${_scopeId}>`);
            } else {
              _push2(`<!---->`);
            }
            if (currencyCode.value) {
              _push2(`<meta property="product:price:currency"${ssrRenderAttr("content", currencyCode.value)}${_scopeId}>`);
            } else {
              _push2(`<!---->`);
            }
            _push2(`<meta property="product:availability"${ssrRenderAttr("content", productAvailability.value)}${_scopeId}><meta property="product:condition" content="new"${_scopeId}><meta name="twitter:card"${ssrRenderAttr("content", primaryImageUrl.value ? "summary_large_image" : "summary")}${_scopeId}><meta name="twitter:title"${ssrRenderAttr("content", seoTitle.value)}${_scopeId}>`);
            if (seoDescription.value) {
              _push2(`<meta name="twitter:description"${ssrRenderAttr("content", seoDescription.value)}${_scopeId}>`);
            } else {
              _push2(`<!---->`);
            }
            if (primaryImageUrl.value) {
              _push2(`<meta name="twitter:image"${ssrRenderAttr("content", primaryImageUrl.value)}${_scopeId}>`);
            } else {
              _push2(`<!---->`);
            }
            if (productTitle.value) {
              _push2(`<meta name="twitter:image:alt"${ssrRenderAttr("content", productTitle.value)}${_scopeId}>`);
            } else {
              _push2(`<!---->`);
            }
            _push2(`<meta name="DC.title"${ssrRenderAttr("content", seoTitle.value)}${_scopeId}>`);
            if (seoDescription.value) {
              _push2(`<meta name="DC.description"${ssrRenderAttr("content", seoDescription.value)}${_scopeId}>`);
            } else {
              _push2(`<!---->`);
            }
            if (seoKeywords.value) {
              _push2(`<meta name="DC.subject"${ssrRenderAttr("content", seoKeywords.value)}${_scopeId}>`);
            } else {
              _push2(`<!---->`);
            }
            if (contentLocale.value) {
              _push2(`<meta name="DC.language"${ssrRenderAttr("content", contentLocale.value)}${_scopeId}>`);
            } else {
              _push2(`<!---->`);
            }
            if (canonicalUrl.value) {
              _push2(`<meta name="DC.identifier"${ssrRenderAttr("content", canonicalUrl.value)}${_scopeId}>`);
            } else {
              _push2(`<!---->`);
            }
            _push2(`<meta name="DC.type" content="Product"${_scopeId}><meta name="DC.format" content="text/html"${_scopeId}>`);
          } else {
            return [
              createVNode("title", null, toDisplayString(seoTitle.value), 1),
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
                content: "index, follow, max-image-preview:large"
              }),
              contentLocale.value ? (openBlock(), createBlock("meta", {
                key: 2,
                "http-equiv": "content-language",
                content: contentLocale.value
              }, null, 8, ["content"])) : createCommentVNode("", true),
              canonicalUrl.value ? (openBlock(), createBlock("link", {
                key: 3,
                rel: "canonical",
                href: canonicalUrl.value
              }, null, 8, ["href"])) : createCommentVNode("", true),
              createVNode("meta", {
                property: "og:type",
                content: "product"
              }),
              createVNode("meta", {
                property: "og:title",
                content: seoTitle.value
              }, null, 8, ["content"]),
              seoDescription.value ? (openBlock(), createBlock("meta", {
                key: 4,
                property: "og:description",
                content: seoDescription.value
              }, null, 8, ["content"])) : createCommentVNode("", true),
              canonicalUrl.value ? (openBlock(), createBlock("meta", {
                key: 5,
                property: "og:url",
                content: canonicalUrl.value
              }, null, 8, ["content"])) : createCommentVNode("", true),
              openGraphLocale.value ? (openBlock(), createBlock("meta", {
                key: 6,
                property: "og:locale",
                content: openGraphLocale.value
              }, null, 8, ["content"])) : createCommentVNode("", true),
              primaryImageUrl.value ? (openBlock(), createBlock("meta", {
                key: 7,
                property: "og:image",
                content: primaryImageUrl.value
              }, null, 8, ["content"])) : createCommentVNode("", true),
              productTitle.value ? (openBlock(), createBlock("meta", {
                key: 8,
                property: "og:image:alt",
                content: productTitle.value
              }, null, 8, ["content"])) : createCommentVNode("", true),
              currentPrice.value > 0 ? (openBlock(), createBlock("meta", {
                key: 9,
                property: "product:price:amount",
                content: String(currentPrice.value)
              }, null, 8, ["content"])) : createCommentVNode("", true),
              currencyCode.value ? (openBlock(), createBlock("meta", {
                key: 10,
                property: "product:price:currency",
                content: currencyCode.value
              }, null, 8, ["content"])) : createCommentVNode("", true),
              createVNode("meta", {
                property: "product:availability",
                content: productAvailability.value
              }, null, 8, ["content"]),
              createVNode("meta", {
                property: "product:condition",
                content: "new"
              }),
              createVNode("meta", {
                name: "twitter:card",
                content: primaryImageUrl.value ? "summary_large_image" : "summary"
              }, null, 8, ["content"]),
              createVNode("meta", {
                name: "twitter:title",
                content: seoTitle.value
              }, null, 8, ["content"]),
              seoDescription.value ? (openBlock(), createBlock("meta", {
                key: 11,
                name: "twitter:description",
                content: seoDescription.value
              }, null, 8, ["content"])) : createCommentVNode("", true),
              primaryImageUrl.value ? (openBlock(), createBlock("meta", {
                key: 12,
                name: "twitter:image",
                content: primaryImageUrl.value
              }, null, 8, ["content"])) : createCommentVNode("", true),
              productTitle.value ? (openBlock(), createBlock("meta", {
                key: 13,
                name: "twitter:image:alt",
                content: productTitle.value
              }, null, 8, ["content"])) : createCommentVNode("", true),
              createVNode("meta", {
                name: "DC.title",
                content: seoTitle.value
              }, null, 8, ["content"]),
              seoDescription.value ? (openBlock(), createBlock("meta", {
                key: 14,
                name: "DC.description",
                content: seoDescription.value
              }, null, 8, ["content"])) : createCommentVNode("", true),
              seoKeywords.value ? (openBlock(), createBlock("meta", {
                key: 15,
                name: "DC.subject",
                content: seoKeywords.value
              }, null, 8, ["content"])) : createCommentVNode("", true),
              contentLocale.value ? (openBlock(), createBlock("meta", {
                key: 16,
                name: "DC.language",
                content: contentLocale.value
              }, null, 8, ["content"])) : createCommentVNode("", true),
              canonicalUrl.value ? (openBlock(), createBlock("meta", {
                key: 17,
                name: "DC.identifier",
                content: canonicalUrl.value
              }, null, 8, ["content"])) : createCommentVNode("", true),
              createVNode("meta", {
                name: "DC.type",
                content: "Product"
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
          var _a2, _b2, _c, _d;
          if (_push2) {
            _push2(ssrRenderComponent(_sfc_main$4, null, null, _parent2, _scopeId));
            _push2(`<main class="min-h-screen px-1 lg:px-6 max-w-full"${_scopeId}><div class="mx-auto tracking-wider pt-20 lg:pt-44"${_scopeId}><div class="ext-color w-full min-w-0 py-3 px-1 flex flex-col lg:flex-row gap-4 rounded-3xl border-2 border-slate-300 dark:border-slate-500"${_scopeId}>`);
            if (showLeft.value) {
              _push2(`<aside class="${ssrRenderClass([leftCollapsed.value ? "lg:w-6" : "lg:w-72", "shrink-0 pl-3 transition-all duration-300"])}"${_scopeId}>`);
              _push2(ssrRenderComponent(_sfc_main$5, {
                "category-tree": categoryTree.value,
                collapsed: leftCollapsed.value,
                onCollapsed: ($event) => leftCollapsed.value = $event
              }, null, _parent2, _scopeId));
              _push2(`</aside>`);
            } else {
              _push2(`<!---->`);
            }
            _push2(`<div class="min-w-0 flex-1 pb-6 slate-1"${_scopeId}><div class="w-full"${_scopeId}><article itemscope itemtype="https://schema.org/Product"${ssrRenderAttr("itemid", canonicalUrl.value)} class="selection:bg-red-400 selection:text-white transition-all"${_scopeId}><meta itemprop="url"${ssrRenderAttr("content", canonicalUrl.value)}${_scopeId}>`);
            if (productData.value.sku) {
              _push2(`<meta itemprop="sku"${ssrRenderAttr("content", productData.value.sku)}${_scopeId}>`);
            } else {
              _push2(`<!---->`);
            }
            if (primaryImageUrl.value) {
              _push2(`<meta itemprop="image"${ssrRenderAttr("content", primaryImageUrl.value)}${_scopeId}>`);
            } else {
              _push2(`<!---->`);
            }
            if (breadcrumbCategoryTitle.value) {
              _push2(`<meta itemprop="category"${ssrRenderAttr("content", breadcrumbCategoryTitle.value)}${_scopeId}>`);
            } else {
              _push2(`<!---->`);
            }
            _push2(`<nav class="text-sm mb-3" aria-label="Breadcrumb" itemscope itemtype="https://schema.org/BreadcrumbList"${_scopeId}><ol class="flex flex-wrap items-center font-semibold"${_scopeId}><li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem" class="flex items-center"${_scopeId}>`);
            _push2(ssrRenderComponent(unref(Link), {
              itemprop: "item",
              href: _ctx.route("public.marketProducts.index"),
              class: "transition hover:text-indigo-500"
            }, {
              default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                if (_push3) {
                  _push3(`<span itemprop="name"${_scopeId2}>${ssrInterpolate(unref(t)("products"))}</span>`);
                } else {
                  return [
                    createVNode("span", { itemprop: "name" }, toDisplayString(unref(t)("products")), 1)
                  ];
                }
              }),
              _: 1
            }, _parent2, _scopeId));
            _push2(`<meta itemprop="position" content="1"${_scopeId}></li>`);
            if (__props.breadcrumbCategory) {
              _push2(`<li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem" class="flex items-center"${_scopeId}><span class="mx-2 breadcrumbs"${_scopeId}>/</span>`);
              _push2(ssrRenderComponent(unref(Link), {
                itemprop: "item",
                href: _ctx.route("public.marketCategories.show", {
                  url: __props.breadcrumbCategory.url
                }),
                class: "transition hover:text-indigo-500"
              }, {
                default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                  if (_push3) {
                    _push3(`<span itemprop="name"${_scopeId2}>${ssrInterpolate(breadcrumbCategoryTitle.value)}</span>`);
                  } else {
                    return [
                      createVNode("span", { itemprop: "name" }, toDisplayString(breadcrumbCategoryTitle.value), 1)
                    ];
                  }
                }),
                _: 1
              }, _parent2, _scopeId));
              _push2(`<meta itemprop="position" content="2"${_scopeId}></li>`);
            } else {
              _push2(`<!---->`);
            }
            _push2(`<li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem" class="flex items-center" aria-current="page"${_scopeId}><span class="mx-2 breadcrumbs"${_scopeId}>/</span><span itemprop="name" class="breadcrumbs"${_scopeId}>${ssrInterpolate(productTitle.value)}</span><meta itemprop="item"${ssrRenderAttr("content", canonicalUrl.value)}${_scopeId}><meta itemprop="position"${ssrRenderAttr("content", __props.breadcrumbCategory ? "3" : "2")}${_scopeId}></li></ol></nav><section class="overflow-hidden rounded-sm border border-gray-300 dark:border-gray-600 bg-slate-50 dark:bg-slate-900 shadow-sm"${_scopeId}><div class="${ssrRenderClass([productMainGridClass.value, "grid gap-6 p-4"])}"${_scopeId}><div class="min-w-0"${_scopeId}>`);
            _push2(ssrRenderComponent(_sfc_main$2, {
              images: productImages.value,
              title: productTitle.value
            }, null, _parent2, _scopeId));
            _push2(`</div><div class="flex min-w-0 flex-col"${_scopeId}><div class="flex items-center justify-between mb-3"${_scopeId}>`);
            if (hasMarketingFlags.value) {
              _push2(`<div class="flex flex-wrap gap-2"${_scopeId}>`);
              if (productData.value.is_new) {
                _push2(`<span class="inline-flex items-center rounded-sm bg-teal-500 dark:bg-teal-600 px-3 py-1 text-[10px] font-bold uppercase tracking-wide text-white"${_scopeId}> NEW </span>`);
              } else {
                _push2(`<!---->`);
              }
              if (productData.value.is_hit) {
                _push2(`<span class="inline-flex items-center rounded-sm bg-amber-500 dark:bg-amber-400 px-3 py-1 text-[10px] font-bold uppercase tracking-wide text-white dark:text-slate-900"${_scopeId}> HIT </span>`);
              } else {
                _push2(`<!---->`);
              }
              if (productData.value.is_sale) {
                _push2(`<span class="inline-flex items-center rounded-sm bg-red-500 dark:bg-red-600 px-3 py-1 text-[10px] font-bold uppercase tracking-wide text-white"${_scopeId}> SALE </span>`);
              } else {
                _push2(`<!---->`);
              }
              _push2(`</div>`);
            } else {
              _push2(`<!---->`);
            }
            _push2(`<div class="flex flex-wrap items-center gap-3"${_scopeId}>`);
            if (productData.value.reviews_count > 0) {
              _push2(`<div class="text-xs font-semibold text-slate-500 dark:text-slate-400"${_scopeId}>${ssrInterpolate(unref(t)("reviews"))}: ${ssrInterpolate(productData.value.reviews_count)}</div>`);
            } else {
              _push2(`<!---->`);
            }
            if (productData.value.views > 0) {
              _push2(`<div class="text-xs font-semibold text-slate-500 dark:text-slate-400"${_scopeId}>${ssrInterpolate(unref(t)("views"))}: ${ssrInterpolate(productData.value.views)}</div>`);
            } else {
              _push2(`<!---->`);
            }
            if (hasAggregateRating.value) {
              _push2(`<div${ssrRenderAttr("title", unref(t)("rating"))} itemprop="aggregateRating" itemscope itemtype="https://schema.org/AggregateRating" class="inline-flex items-center gap-1 text-sm font-semibold"${_scopeId}><meta itemprop="ratingValue"${ssrRenderAttr("content", String(rating.value))}${_scopeId}><meta itemprop="ratingCount"${ssrRenderAttr("content", String(ratingCount.value))}${_scopeId}>`);
              if (productData.value.reviews_count > 0) {
                _push2(`<meta itemprop="reviewCount"${ssrRenderAttr("content", String(productData.value.reviews_count))}${_scopeId}>`);
              } else {
                _push2(`<!---->`);
              }
              _push2(`<meta itemprop="bestRating" content="5"${_scopeId}><meta itemprop="worstRating" content="1"${_scopeId}><svg class="h-4 w-4 text-amber-500" viewBox="0 0 24 24" fill="currentColor"${_scopeId}><path d="M12.746,1.464l3.11,6.3L22.81,8.776a.831.831,0,0,1,.461,1.418l-5.033,4.9,1.188,6.926a.832.832,0,0,1-1.207.877L12,19.632,5.78,22.9a.833.833,0,0,1-1.207-.878L5.761,15.1l-5.033-4.9a.831.831,0,0,1,.461-1.418L8.143,7.765l3.11-6.3A.833.833,0,0,1,12.746,1.464Z"${_scopeId}></path></svg><span${_scopeId}>${ssrInterpolate(rating.value.toFixed(1))}</span><span class="font-normal text-slate-500"${_scopeId}> (${ssrInterpolate(ratingCount.value)}) </span></div>`);
            } else {
              _push2(`<!---->`);
            }
            _push2(`</div></div>`);
            if (brandTitle.value) {
              _push2(`<div itemprop="brand" itemscope itemtype="https://schema.org/Brand" class="mb-1 text-sm font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400"${_scopeId}><span itemprop="name"${_scopeId}>${ssrInterpolate(brandTitle.value)}</span></div>`);
            } else {
              _push2(`<!---->`);
            }
            _push2(`<h1 itemprop="name" class="text-xl font-bold text-slate-700 dark:text-slate-300"${_scopeId}>${ssrInterpolate(productTitle.value)}</h1>`);
            if (productSubtitle.value) {
              _push2(`<div class="mt-2 text-sm font-medium text-slate-500 dark:text-slate-400"${_scopeId}>${ssrInterpolate(productSubtitle.value)}</div>`);
            } else {
              _push2(`<!---->`);
            }
            if (productShort.value) {
              _push2(`<div itemprop="description" class="mt-3 text-sm leading-6 text-slate-700 dark:text-slate-300"${_scopeId}>${ssrInterpolate(productShort.value)}</div>`);
            } else {
              _push2(`<!---->`);
            }
            _push2(`<div class="mt-2 py-2 border-y border-dashed border-gray-600 dark:border-gray-400"${_scopeId}><div class="${ssrRenderClass([hasStock.value ? "text-emerald-600 dark:text-emerald-300" : "text-red-500 dark:text-red-400", "flex items-center gap-2 text-sm font-semibold"])}"${_scopeId}><svg class="h-4 w-4" viewBox="0 0 24 24" fill="currentColor"${_scopeId}>`);
            if (hasStock.value) {
              _push2(`<path d="M9 16.17 4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41L9 16.17Z"${_scopeId}></path>`);
            } else {
              _push2(`<path d="M18.3 5.71 12 12l6.3 6.29-1.41 1.42L10.59 13.41 4.29 19.71 2.88 18.3 9.17 12 2.88 5.71 4.29 4.29 10.59 10.59 16.89 4.29 18.3 5.71Z"${_scopeId}></path>`);
            }
            _push2(`</svg><span${_scopeId}>${ssrInterpolate(hasStock.value ? unref(t)("remainder") : unref(t)("outOfStock"))}</span>`);
            if (hasStock.value && quantity.value > 0) {
              _push2(`<span${_scopeId}> [${ssrInterpolate(quantity.value)}] </span>`);
            } else {
              _push2(`<!---->`);
            }
            _push2(`</div><div itemprop="offers" itemscope itemtype="https://schema.org/Offer"${_scopeId}>`);
            if (canonicalUrl.value) {
              _push2(`<meta itemprop="url"${ssrRenderAttr("content", canonicalUrl.value)}${_scopeId}>`);
            } else {
              _push2(`<!---->`);
            }
            if (currencyCode.value) {
              _push2(`<meta itemprop="priceCurrency"${ssrRenderAttr("content", currencyCode.value)}${_scopeId}>`);
            } else {
              _push2(`<!---->`);
            }
            if (currentPrice.value > 0) {
              _push2(`<meta itemprop="price"${ssrRenderAttr("content", Number(currentPrice.value).toFixed(2))}${_scopeId}>`);
            } else {
              _push2(`<!---->`);
            }
            _push2(`<link itemprop="availability"${ssrRenderAttr("href", schemaAvailability.value)}${_scopeId}><link itemprop="itemCondition" href="https://schema.org/NewCondition"${_scopeId}><div class="flex flex-wrap items-end gap-3"${_scopeId}><div class="text-xl font-bold text-teal-600 dark:text-teal-400"${_scopeId}>${ssrInterpolate(formatPrice(currentPrice.value))} ${ssrInterpolate(currencyLabel.value)}</div>`);
            if (hasOldPrice.value) {
              _push2(`<div class="pb-1 text-base font-semibold text-slate-400 line-through dark:text-slate-500"${_scopeId}>${ssrInterpolate(formatPrice(currentOldPrice.value))} ${ssrInterpolate(currencyLabel.value)}</div>`);
            } else {
              _push2(`<!---->`);
            }
            _push2(`</div>`);
            if (hasWholesalePrice.value) {
              _push2(`<div class="mt-1 text-xs font-semibold text-gray-500 dark:text-gray-400"${_scopeId}>${ssrInterpolate(unref(t)("wholesalePrice"))}: ${ssrInterpolate(formatPrice(wholesalePrice.value))} ${ssrInterpolate(currencyLabel.value)} <span${_scopeId}> / ${ssrInterpolate(unref(t)("wholesaleMinQuantity"))}: ${ssrInterpolate(wholesaleMinQuantity.value)}</span></div>`);
            } else {
              _push2(`<!---->`);
            }
            _push2(`</div></div>`);
            if (publicVariants.value.length) {
              _push2(`<div class="mt-1"${_scopeId}><div class="mb-2 text-sm font-semibold text-slate-700 dark:text-slate-300"${_scopeId}>${ssrInterpolate(unref(t)("variants"))}</div><select class="w-full rounded-sm border bg-white dark:bg-gray-800 px-3 py-1 border-gray-400 dark:border-gray-600 text-sm text-slate-700 dark:text-slate-300 focus:border-indigo-500 focus:ring-indigo-500"${_scopeId}><!--[-->`);
              ssrRenderList(publicVariants.value, (variant) => {
                var _a3, _b3, _c2;
                _push2(`<option${ssrRenderAttr("value", variant.id)}${ssrIncludeBooleanAttr(!variant.has_stock) ? " disabled" : ""}${ssrIncludeBooleanAttr(Array.isArray(selectedVariantId.value) ? ssrLooseContain(selectedVariantId.value, variant.id) : ssrLooseEqual(selectedVariantId.value, variant.id)) ? " selected" : ""}${_scopeId}>${ssrInterpolate(getVariantTitle(variant))} — ${ssrInterpolate(formatPrice(variant.effective_price))} ${ssrInterpolate(((_a3 = variant.currency) == null ? void 0 : _a3.symbol) || ((_b3 = variant.currency) == null ? void 0 : _b3.sign) || ((_c2 = variant.currency) == null ? void 0 : _c2.code) || currencyLabel.value)} ${ssrInterpolate(!variant.has_stock ? `(${unref(t)("outOfStock")})` : "")}</option>`);
              });
              _push2(`<!--]--></select>`);
              if ((_b2 = (_a2 = selectedVariant.value) == null ? void 0 : _a2.values) == null ? void 0 : _b2.length) {
                _push2(`<div class="mt-3 flex flex-wrap gap-2"${_scopeId}><!--[-->`);
                ssrRenderList(selectedVariant.value.values, (value) => {
                  _push2(`<span class="bg-white dark:bg-slate-950 rounded-sm border border-slate-400 dark:border-slate-600 px-2 py-1 text-xs font-semibold text-slate-600 dark:text-slate-300"${_scopeId}>${ssrInterpolate(value.display_value)}</span>`);
                });
                _push2(`<!--]--></div>`);
              } else {
                _push2(`<!---->`);
              }
              _push2(`</div>`);
            } else {
              _push2(`<!---->`);
            }
            if (productData.value.sku || productData.value.vendor_code || productData.value.barcode) {
              _push2(`<div class="mt-3 flex lg:flex-row lg:justify-between items-center gap-2 text-xs text-slate-600 dark:text-slate-400"${_scopeId}>`);
              if (productData.value.vendor_code) {
                _push2(`<div${_scopeId}>${ssrInterpolate(unref(t)("vendorCode"))}: <span class="font-semibold"${_scopeId}>${ssrInterpolate(productData.value.vendor_code)}</span></div>`);
              } else {
                _push2(`<!---->`);
              }
              if (productData.value.sku) {
                _push2(`<div${_scopeId}>${ssrInterpolate(unref(t)("sku"))}: <span class="font-semibold"${_scopeId}>${ssrInterpolate(productData.value.sku)}</span></div>`);
              } else {
                _push2(`<!---->`);
              }
              _push2(`</div>`);
            } else {
              _push2(`<!---->`);
            }
            _push2(`<div class="mt-3"${_scopeId}>`);
            _push2(ssrRenderComponent(LikeButtonEntity, {
              "likes-count": productData.value.likes_count || 0,
              "already-liked": productData.value.already_liked || false,
              "route-name": "public.marketProducts.like",
              "route-params": { id: productData.value.id },
              "icon-class": "w-5 h-5 hover:scale-110\n                                                                active:scale-95"
            }, null, _parent2, _scopeId));
            _push2(`</div></div></div></section>`);
            if (productDescription.value) {
              _push2(`<section class="mt-4 p-4 rounded-sm shadow-sm border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900"${_scopeId}><h2 class="mb-4 text-lg font-semibold text-slate-800 dark:text-slate-200"${_scopeId}>${ssrInterpolate(unref(t)("description"))}</h2><div class="prose max-w-none dark:prose-invert"${_scopeId}>${productDescription.value ?? ""}</div></section>`);
            } else {
              _push2(`<!---->`);
            }
            if (hasPhysicalParameters.value) {
              _push2(`<section class="mt-4 p-4 rounded-sm shadow-sm border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900"${_scopeId}><h2 class="mb-4 text-lg font-semibold text-slate-800 dark:text-slate-200"${_scopeId}>${ssrInterpolate(unref(t)("dimensions"))}</h2><div class="grid gap-3 text-sm sm:grid-cols-2 lg:grid-cols-4"${_scopeId}>`);
              if (productData.value.weight) {
                _push2(`<div${_scopeId}><span class="text-slate-500 dark:text-slate-400"${_scopeId}>${ssrInterpolate(unref(t)("weight"))}: </span><span class="ml-1 font-semibold"${_scopeId}>${ssrInterpolate(productData.value.weight)}</span></div>`);
              } else {
                _push2(`<!---->`);
              }
              if (productData.value.length) {
                _push2(`<div${_scopeId}><span class="text-slate-500 dark:text-slate-400"${_scopeId}>${ssrInterpolate(unref(t)("length"))}: </span><span class="ml-1 font-semibold"${_scopeId}>${ssrInterpolate(productData.value.length)}</span></div>`);
              } else {
                _push2(`<!---->`);
              }
              if (productData.value.width) {
                _push2(`<div${_scopeId}><span class="text-slate-500 dark:text-slate-400"${_scopeId}>${ssrInterpolate(unref(t)("width"))}: </span><span class="ml-1 font-semibold"${_scopeId}>${ssrInterpolate(productData.value.width)}</span></div>`);
              } else {
                _push2(`<!---->`);
              }
              if (productData.value.height) {
                _push2(`<div${_scopeId}><span class="text-slate-500 dark:text-slate-400"${_scopeId}>${ssrInterpolate(unref(t)("height"))}: </span><span class="ml-1 font-semibold"${_scopeId}>${ssrInterpolate(productData.value.height)}</span></div>`);
              } else {
                _push2(`<!---->`);
              }
              _push2(`</div></section>`);
            } else {
              _push2(`<!---->`);
            }
            if (attributeValues.value.length) {
              _push2(`<section class="mt-4 p-4 rounded-sm shadow-sm border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900"${_scopeId}><h2 class="mb-4 text-lg font-semibold text-slate-800 dark:text-slate-200"${_scopeId}>${ssrInterpolate(unref(t)("attributes"))}</h2><div class="divide-y divide-gray-400 dark:divide-gray-600"${_scopeId}><!--[-->`);
              ssrRenderList(attributeValues.value, (item) => {
                var _a3;
                _push2(`<div class="grid gap-2 py-2 text-sm sm:grid-cols-2"${_scopeId}><div class="font-semibold text-slate-600 dark:text-slate-400"${_scopeId}>${ssrInterpolate(((_a3 = item.attribute) == null ? void 0 : _a3.title) || "—")}</div><div class="text-slate-800 dark:text-slate-200"${_scopeId}>${ssrInterpolate(getAttributeValue(item))}</div></div>`);
              });
              _push2(`<!--]--></div></section>`);
            } else {
              _push2(`<!---->`);
            }
            if (categories.value.length) {
              _push2(`<section class="mt-4 p-4 rounded-sm shadow-sm border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900"${_scopeId}><h2 class="mb-3 text-lg font-semibold text-slate-800 dark:text-slate-200"${_scopeId}>${ssrInterpolate(unref(t)("categories"))}</h2><div class="flex flex-wrap gap-2"${_scopeId}><!--[-->`);
              ssrRenderList(categories.value, (category) => {
                _push2(ssrRenderComponent(unref(Link), {
                  key: category.id,
                  href: _ctx.route("public.marketCategories.show", {
                    url: category.url
                  }),
                  class: "rounded-sm border border-slate-300 dark:border-slate-600 px-3 py-1 text-xs font-semibold text-slate-600 dark:text-slate-300 hover:text-blue-600 dark:hover:text-blue-400 transition bg-slate-50 dark:bg-slate-950 hover:bg-slate-100 dark:hover:bg-slate-800"
                }, {
                  default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                    var _a3, _b3;
                    if (_push3) {
                      _push3(`${ssrInterpolate(((_a3 = category.translation) == null ? void 0 : _a3.title) || "")}`);
                    } else {
                      return [
                        createTextVNode(toDisplayString(((_b3 = category.translation) == null ? void 0 : _b3.title) || ""), 1)
                      ];
                    }
                  }),
                  _: 2
                }, _parent2, _scopeId));
              });
              _push2(`<!--]--></div></section>`);
            } else {
              _push2(`<!---->`);
            }
            if (tags.value.length) {
              _push2(`<section class="mt-4 p-4 rounded-sm shadow-sm border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900"${_scopeId}><h2 class="mb-3 text-lg font-semibold text-slate-800 dark:text-slate-200"${_scopeId}>${ssrInterpolate(unref(t)("tags"))}</h2><div class="flex flex-wrap gap-2"${_scopeId}><!--[-->`);
              ssrRenderList(tags.value, (tag) => {
                _push2(ssrRenderComponent(unref(Link), {
                  key: tag.id,
                  href: _ctx.route("public.marketTags.show", {
                    url: tag.url
                  }),
                  class: "rounded-sm border border-slate-300 dark:border-slate-600 px-3 py-1 text-xs font-semibold text-slate-600 dark:text-slate-300 hover:text-blue-600 dark:hover:text-blue-400 transition bg-slate-50 dark:bg-slate-950 hover:bg-slate-100 dark:hover:bg-slate-800"
                }, {
                  default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                    var _a3, _b3;
                    if (_push3) {
                      _push3(`${ssrInterpolate(((_a3 = tag.translation) == null ? void 0 : _a3.title) || tag.title)}`);
                    } else {
                      return [
                        createTextVNode(toDisplayString(((_b3 = tag.translation) == null ? void 0 : _b3.title) || tag.title), 1)
                      ];
                    }
                  }),
                  _: 2
                }, _parent2, _scopeId));
              });
              _push2(`<!--]--></div></section>`);
            } else {
              _push2(`<!---->`);
            }
            if (relatedProducts.value.length) {
              _push2(`<section class="mt-6"${_scopeId}><h2 class="mb-4 text-xl font-semibold text-center text-slate-800 dark:text-slate-200"${_scopeId}>${ssrInterpolate(unref(t)("relatedProducts"))}</h2>`);
              _push2(ssrRenderComponent(_sfc_main$1, {
                products: relatedProducts.value,
                cols: relatedGridCols.value,
                locale: props.locale
              }, null, _parent2, _scopeId));
              _push2(`</section>`);
            } else {
              _push2(`<!---->`);
            }
            _push2(`</article></div></div>`);
            if (showRight.value) {
              _push2(`<aside class="${ssrRenderClass([rightCollapsed.value ? "lg:w-6" : "lg:w-72", "shrink-0 pr-3 transition-all duration-300"])}"${_scopeId}>`);
              _push2(ssrRenderComponent(_sfc_main$6, {
                collapsed: rightCollapsed.value,
                onCollapsed: ($event) => rightCollapsed.value = $event
              }, null, _parent2, _scopeId));
              _push2(`</aside>`);
            } else {
              _push2(`<!---->`);
            }
            _push2(`</div>`);
            _push2(ssrRenderComponent(_sfc_main$7, { products: unref(recentlyViewed) }, null, _parent2, _scopeId));
            _push2(`</div></main>`);
            _push2(ssrRenderComponent(_sfc_main$8, null, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(Progress, null, null, _parent2, _scopeId));
          } else {
            return [
              createVNode(_sfc_main$4),
              createVNode("main", { class: "min-h-screen px-1 lg:px-6 max-w-full" }, [
                createVNode("div", { class: "mx-auto tracking-wider pt-20 lg:pt-44" }, [
                  createVNode("div", { class: "ext-color w-full min-w-0 py-3 px-1 flex flex-col lg:flex-row gap-4 rounded-3xl border-2 border-slate-300 dark:border-slate-500" }, [
                    showLeft.value ? (openBlock(), createBlock("aside", {
                      key: 0,
                      class: ["shrink-0 pl-3 transition-all duration-300", leftCollapsed.value ? "lg:w-6" : "lg:w-72"]
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
                          itemtype: "https://schema.org/Product",
                          itemid: canonicalUrl.value,
                          class: "selection:bg-red-400 selection:text-white transition-all"
                        }, [
                          createVNode("meta", {
                            itemprop: "url",
                            content: canonicalUrl.value
                          }, null, 8, ["content"]),
                          productData.value.sku ? (openBlock(), createBlock("meta", {
                            key: 0,
                            itemprop: "sku",
                            content: productData.value.sku
                          }, null, 8, ["content"])) : createCommentVNode("", true),
                          primaryImageUrl.value ? (openBlock(), createBlock("meta", {
                            key: 1,
                            itemprop: "image",
                            content: primaryImageUrl.value
                          }, null, 8, ["content"])) : createCommentVNode("", true),
                          breadcrumbCategoryTitle.value ? (openBlock(), createBlock("meta", {
                            key: 2,
                            itemprop: "category",
                            content: breadcrumbCategoryTitle.value
                          }, null, 8, ["content"])) : createCommentVNode("", true),
                          createVNode("nav", {
                            class: "text-sm mb-3",
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
                                  href: _ctx.route("public.marketProducts.index"),
                                  class: "transition hover:text-indigo-500"
                                }, {
                                  default: withCtx(() => [
                                    createVNode("span", { itemprop: "name" }, toDisplayString(unref(t)("products")), 1)
                                  ]),
                                  _: 1
                                }, 8, ["href"]),
                                createVNode("meta", {
                                  itemprop: "position",
                                  content: "1"
                                })
                              ]),
                              __props.breadcrumbCategory ? (openBlock(), createBlock("li", {
                                key: 0,
                                itemprop: "itemListElement",
                                itemscope: "",
                                itemtype: "https://schema.org/ListItem",
                                class: "flex items-center"
                              }, [
                                createVNode("span", { class: "mx-2 breadcrumbs" }, "/"),
                                createVNode(unref(Link), {
                                  itemprop: "item",
                                  href: _ctx.route("public.marketCategories.show", {
                                    url: __props.breadcrumbCategory.url
                                  }),
                                  class: "transition hover:text-indigo-500"
                                }, {
                                  default: withCtx(() => [
                                    createVNode("span", { itemprop: "name" }, toDisplayString(breadcrumbCategoryTitle.value), 1)
                                  ]),
                                  _: 1
                                }, 8, ["href"]),
                                createVNode("meta", {
                                  itemprop: "position",
                                  content: "2"
                                })
                              ])) : createCommentVNode("", true),
                              createVNode("li", {
                                itemprop: "itemListElement",
                                itemscope: "",
                                itemtype: "https://schema.org/ListItem",
                                class: "flex items-center",
                                "aria-current": "page"
                              }, [
                                createVNode("span", { class: "mx-2 breadcrumbs" }, "/"),
                                createVNode("span", {
                                  itemprop: "name",
                                  class: "breadcrumbs"
                                }, toDisplayString(productTitle.value), 1),
                                createVNode("meta", {
                                  itemprop: "item",
                                  content: canonicalUrl.value
                                }, null, 8, ["content"]),
                                createVNode("meta", {
                                  itemprop: "position",
                                  content: __props.breadcrumbCategory ? "3" : "2"
                                }, null, 8, ["content"])
                              ])
                            ])
                          ]),
                          createVNode("section", { class: "overflow-hidden rounded-sm border border-gray-300 dark:border-gray-600 bg-slate-50 dark:bg-slate-900 shadow-sm" }, [
                            createVNode("div", {
                              class: ["grid gap-6 p-4", productMainGridClass.value]
                            }, [
                              createVNode("div", { class: "min-w-0" }, [
                                createVNode(_sfc_main$2, {
                                  images: productImages.value,
                                  title: productTitle.value
                                }, null, 8, ["images", "title"])
                              ]),
                              createVNode("div", { class: "flex min-w-0 flex-col" }, [
                                createVNode("div", { class: "flex items-center justify-between mb-3" }, [
                                  hasMarketingFlags.value ? (openBlock(), createBlock("div", {
                                    key: 0,
                                    class: "flex flex-wrap gap-2"
                                  }, [
                                    productData.value.is_new ? (openBlock(), createBlock("span", {
                                      key: 0,
                                      class: "inline-flex items-center rounded-sm bg-teal-500 dark:bg-teal-600 px-3 py-1 text-[10px] font-bold uppercase tracking-wide text-white"
                                    }, " NEW ")) : createCommentVNode("", true),
                                    productData.value.is_hit ? (openBlock(), createBlock("span", {
                                      key: 1,
                                      class: "inline-flex items-center rounded-sm bg-amber-500 dark:bg-amber-400 px-3 py-1 text-[10px] font-bold uppercase tracking-wide text-white dark:text-slate-900"
                                    }, " HIT ")) : createCommentVNode("", true),
                                    productData.value.is_sale ? (openBlock(), createBlock("span", {
                                      key: 2,
                                      class: "inline-flex items-center rounded-sm bg-red-500 dark:bg-red-600 px-3 py-1 text-[10px] font-bold uppercase tracking-wide text-white"
                                    }, " SALE ")) : createCommentVNode("", true)
                                  ])) : createCommentVNode("", true),
                                  createVNode("div", { class: "flex flex-wrap items-center gap-3" }, [
                                    productData.value.reviews_count > 0 ? (openBlock(), createBlock("div", {
                                      key: 0,
                                      class: "text-xs font-semibold text-slate-500 dark:text-slate-400"
                                    }, toDisplayString(unref(t)("reviews")) + ": " + toDisplayString(productData.value.reviews_count), 1)) : createCommentVNode("", true),
                                    productData.value.views > 0 ? (openBlock(), createBlock("div", {
                                      key: 1,
                                      class: "text-xs font-semibold text-slate-500 dark:text-slate-400"
                                    }, toDisplayString(unref(t)("views")) + ": " + toDisplayString(productData.value.views), 1)) : createCommentVNode("", true),
                                    hasAggregateRating.value ? (openBlock(), createBlock("div", {
                                      key: 2,
                                      title: unref(t)("rating"),
                                      itemprop: "aggregateRating",
                                      itemscope: "",
                                      itemtype: "https://schema.org/AggregateRating",
                                      class: "inline-flex items-center gap-1 text-sm font-semibold"
                                    }, [
                                      createVNode("meta", {
                                        itemprop: "ratingValue",
                                        content: String(rating.value)
                                      }, null, 8, ["content"]),
                                      createVNode("meta", {
                                        itemprop: "ratingCount",
                                        content: String(ratingCount.value)
                                      }, null, 8, ["content"]),
                                      productData.value.reviews_count > 0 ? (openBlock(), createBlock("meta", {
                                        key: 0,
                                        itemprop: "reviewCount",
                                        content: String(productData.value.reviews_count)
                                      }, null, 8, ["content"])) : createCommentVNode("", true),
                                      createVNode("meta", {
                                        itemprop: "bestRating",
                                        content: "5"
                                      }),
                                      createVNode("meta", {
                                        itemprop: "worstRating",
                                        content: "1"
                                      }),
                                      (openBlock(), createBlock("svg", {
                                        class: "h-4 w-4 text-amber-500",
                                        viewBox: "0 0 24 24",
                                        fill: "currentColor"
                                      }, [
                                        createVNode("path", { d: "M12.746,1.464l3.11,6.3L22.81,8.776a.831.831,0,0,1,.461,1.418l-5.033,4.9,1.188,6.926a.832.832,0,0,1-1.207.877L12,19.632,5.78,22.9a.833.833,0,0,1-1.207-.878L5.761,15.1l-5.033-4.9a.831.831,0,0,1,.461-1.418L8.143,7.765l3.11-6.3A.833.833,0,0,1,12.746,1.464Z" })
                                      ])),
                                      createVNode("span", null, toDisplayString(rating.value.toFixed(1)), 1),
                                      createVNode("span", { class: "font-normal text-slate-500" }, " (" + toDisplayString(ratingCount.value) + ") ", 1)
                                    ], 8, ["title"])) : createCommentVNode("", true)
                                  ])
                                ]),
                                brandTitle.value ? (openBlock(), createBlock("div", {
                                  key: 0,
                                  itemprop: "brand",
                                  itemscope: "",
                                  itemtype: "https://schema.org/Brand",
                                  class: "mb-1 text-sm font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400"
                                }, [
                                  createVNode("span", { itemprop: "name" }, toDisplayString(brandTitle.value), 1)
                                ])) : createCommentVNode("", true),
                                createVNode("h1", {
                                  itemprop: "name",
                                  class: "text-xl font-bold text-slate-700 dark:text-slate-300"
                                }, toDisplayString(productTitle.value), 1),
                                productSubtitle.value ? (openBlock(), createBlock("div", {
                                  key: 1,
                                  class: "mt-2 text-sm font-medium text-slate-500 dark:text-slate-400"
                                }, toDisplayString(productSubtitle.value), 1)) : createCommentVNode("", true),
                                productShort.value ? (openBlock(), createBlock("div", {
                                  key: 2,
                                  itemprop: "description",
                                  class: "mt-3 text-sm leading-6 text-slate-700 dark:text-slate-300"
                                }, toDisplayString(productShort.value), 1)) : createCommentVNode("", true),
                                createVNode("div", { class: "mt-2 py-2 border-y border-dashed border-gray-600 dark:border-gray-400" }, [
                                  createVNode("div", {
                                    class: ["flex items-center gap-2 text-sm font-semibold", hasStock.value ? "text-emerald-600 dark:text-emerald-300" : "text-red-500 dark:text-red-400"]
                                  }, [
                                    (openBlock(), createBlock("svg", {
                                      class: "h-4 w-4",
                                      viewBox: "0 0 24 24",
                                      fill: "currentColor"
                                    }, [
                                      hasStock.value ? (openBlock(), createBlock("path", {
                                        key: 0,
                                        d: "M9 16.17 4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41L9 16.17Z"
                                      })) : (openBlock(), createBlock("path", {
                                        key: 1,
                                        d: "M18.3 5.71 12 12l6.3 6.29-1.41 1.42L10.59 13.41 4.29 19.71 2.88 18.3 9.17 12 2.88 5.71 4.29 4.29 10.59 10.59 16.89 4.29 18.3 5.71Z"
                                      }))
                                    ])),
                                    createVNode("span", null, toDisplayString(hasStock.value ? unref(t)("remainder") : unref(t)("outOfStock")), 1),
                                    hasStock.value && quantity.value > 0 ? (openBlock(), createBlock("span", { key: 0 }, " [" + toDisplayString(quantity.value) + "] ", 1)) : createCommentVNode("", true)
                                  ], 2),
                                  createVNode("div", {
                                    itemprop: "offers",
                                    itemscope: "",
                                    itemtype: "https://schema.org/Offer"
                                  }, [
                                    canonicalUrl.value ? (openBlock(), createBlock("meta", {
                                      key: 0,
                                      itemprop: "url",
                                      content: canonicalUrl.value
                                    }, null, 8, ["content"])) : createCommentVNode("", true),
                                    currencyCode.value ? (openBlock(), createBlock("meta", {
                                      key: 1,
                                      itemprop: "priceCurrency",
                                      content: currencyCode.value
                                    }, null, 8, ["content"])) : createCommentVNode("", true),
                                    currentPrice.value > 0 ? (openBlock(), createBlock("meta", {
                                      key: 2,
                                      itemprop: "price",
                                      content: Number(currentPrice.value).toFixed(2)
                                    }, null, 8, ["content"])) : createCommentVNode("", true),
                                    createVNode("link", {
                                      itemprop: "availability",
                                      href: schemaAvailability.value
                                    }, null, 8, ["href"]),
                                    createVNode("link", {
                                      itemprop: "itemCondition",
                                      href: "https://schema.org/NewCondition"
                                    }),
                                    createVNode("div", { class: "flex flex-wrap items-end gap-3" }, [
                                      createVNode("div", { class: "text-xl font-bold text-teal-600 dark:text-teal-400" }, toDisplayString(formatPrice(currentPrice.value)) + " " + toDisplayString(currencyLabel.value), 1),
                                      hasOldPrice.value ? (openBlock(), createBlock("div", {
                                        key: 0,
                                        class: "pb-1 text-base font-semibold text-slate-400 line-through dark:text-slate-500"
                                      }, toDisplayString(formatPrice(currentOldPrice.value)) + " " + toDisplayString(currencyLabel.value), 1)) : createCommentVNode("", true)
                                    ]),
                                    hasWholesalePrice.value ? (openBlock(), createBlock("div", {
                                      key: 3,
                                      class: "mt-1 text-xs font-semibold text-gray-500 dark:text-gray-400"
                                    }, [
                                      createTextVNode(toDisplayString(unref(t)("wholesalePrice")) + ": " + toDisplayString(formatPrice(wholesalePrice.value)) + " " + toDisplayString(currencyLabel.value) + " ", 1),
                                      createVNode("span", null, " / " + toDisplayString(unref(t)("wholesaleMinQuantity")) + ": " + toDisplayString(wholesaleMinQuantity.value), 1)
                                    ])) : createCommentVNode("", true)
                                  ])
                                ]),
                                publicVariants.value.length ? (openBlock(), createBlock("div", {
                                  key: 3,
                                  class: "mt-1"
                                }, [
                                  createVNode("div", { class: "mb-2 text-sm font-semibold text-slate-700 dark:text-slate-300" }, toDisplayString(unref(t)("variants")), 1),
                                  withDirectives(createVNode("select", {
                                    "onUpdate:modelValue": ($event) => selectedVariantId.value = $event,
                                    class: "w-full rounded-sm border bg-white dark:bg-gray-800 px-3 py-1 border-gray-400 dark:border-gray-600 text-sm text-slate-700 dark:text-slate-300 focus:border-indigo-500 focus:ring-indigo-500"
                                  }, [
                                    (openBlock(true), createBlock(Fragment, null, renderList(publicVariants.value, (variant) => {
                                      var _a3, _b3, _c2;
                                      return openBlock(), createBlock("option", {
                                        key: variant.id,
                                        value: variant.id,
                                        disabled: !variant.has_stock
                                      }, toDisplayString(getVariantTitle(variant)) + " — " + toDisplayString(formatPrice(variant.effective_price)) + " " + toDisplayString(((_a3 = variant.currency) == null ? void 0 : _a3.symbol) || ((_b3 = variant.currency) == null ? void 0 : _b3.sign) || ((_c2 = variant.currency) == null ? void 0 : _c2.code) || currencyLabel.value) + " " + toDisplayString(!variant.has_stock ? `(${unref(t)("outOfStock")})` : ""), 9, ["value", "disabled"]);
                                    }), 128))
                                  ], 8, ["onUpdate:modelValue"]), [
                                    [vModelSelect, selectedVariantId.value]
                                  ]),
                                  ((_d = (_c = selectedVariant.value) == null ? void 0 : _c.values) == null ? void 0 : _d.length) ? (openBlock(), createBlock("div", {
                                    key: 0,
                                    class: "mt-3 flex flex-wrap gap-2"
                                  }, [
                                    (openBlock(true), createBlock(Fragment, null, renderList(selectedVariant.value.values, (value) => {
                                      return openBlock(), createBlock("span", {
                                        key: value.id,
                                        class: "bg-white dark:bg-slate-950 rounded-sm border border-slate-400 dark:border-slate-600 px-2 py-1 text-xs font-semibold text-slate-600 dark:text-slate-300"
                                      }, toDisplayString(value.display_value), 1);
                                    }), 128))
                                  ])) : createCommentVNode("", true)
                                ])) : createCommentVNode("", true),
                                productData.value.sku || productData.value.vendor_code || productData.value.barcode ? (openBlock(), createBlock("div", {
                                  key: 4,
                                  class: "mt-3 flex lg:flex-row lg:justify-between items-center gap-2 text-xs text-slate-600 dark:text-slate-400"
                                }, [
                                  productData.value.vendor_code ? (openBlock(), createBlock("div", { key: 0 }, [
                                    createTextVNode(toDisplayString(unref(t)("vendorCode")) + ": ", 1),
                                    createVNode("span", { class: "font-semibold" }, toDisplayString(productData.value.vendor_code), 1)
                                  ])) : createCommentVNode("", true),
                                  productData.value.sku ? (openBlock(), createBlock("div", { key: 1 }, [
                                    createTextVNode(toDisplayString(unref(t)("sku")) + ": ", 1),
                                    createVNode("span", { class: "font-semibold" }, toDisplayString(productData.value.sku), 1)
                                  ])) : createCommentVNode("", true)
                                ])) : createCommentVNode("", true),
                                createVNode("div", { class: "mt-3" }, [
                                  createVNode(LikeButtonEntity, {
                                    "likes-count": productData.value.likes_count || 0,
                                    "already-liked": productData.value.already_liked || false,
                                    "route-name": "public.marketProducts.like",
                                    "route-params": { id: productData.value.id },
                                    "icon-class": "w-5 h-5 hover:scale-110\n                                                                active:scale-95"
                                  }, null, 8, ["likes-count", "already-liked", "route-params"])
                                ])
                              ])
                            ], 2)
                          ]),
                          productDescription.value ? (openBlock(), createBlock("section", {
                            key: 3,
                            class: "mt-4 p-4 rounded-sm shadow-sm border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900"
                          }, [
                            createVNode("h2", { class: "mb-4 text-lg font-semibold text-slate-800 dark:text-slate-200" }, toDisplayString(unref(t)("description")), 1),
                            createVNode("div", {
                              class: "prose max-w-none dark:prose-invert",
                              innerHTML: productDescription.value
                            }, null, 8, ["innerHTML"])
                          ])) : createCommentVNode("", true),
                          hasPhysicalParameters.value ? (openBlock(), createBlock("section", {
                            key: 4,
                            class: "mt-4 p-4 rounded-sm shadow-sm border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900"
                          }, [
                            createVNode("h2", { class: "mb-4 text-lg font-semibold text-slate-800 dark:text-slate-200" }, toDisplayString(unref(t)("dimensions")), 1),
                            createVNode("div", { class: "grid gap-3 text-sm sm:grid-cols-2 lg:grid-cols-4" }, [
                              productData.value.weight ? (openBlock(), createBlock("div", { key: 0 }, [
                                createVNode("span", { class: "text-slate-500 dark:text-slate-400" }, toDisplayString(unref(t)("weight")) + ": ", 1),
                                createVNode("span", { class: "ml-1 font-semibold" }, toDisplayString(productData.value.weight), 1)
                              ])) : createCommentVNode("", true),
                              productData.value.length ? (openBlock(), createBlock("div", { key: 1 }, [
                                createVNode("span", { class: "text-slate-500 dark:text-slate-400" }, toDisplayString(unref(t)("length")) + ": ", 1),
                                createVNode("span", { class: "ml-1 font-semibold" }, toDisplayString(productData.value.length), 1)
                              ])) : createCommentVNode("", true),
                              productData.value.width ? (openBlock(), createBlock("div", { key: 2 }, [
                                createVNode("span", { class: "text-slate-500 dark:text-slate-400" }, toDisplayString(unref(t)("width")) + ": ", 1),
                                createVNode("span", { class: "ml-1 font-semibold" }, toDisplayString(productData.value.width), 1)
                              ])) : createCommentVNode("", true),
                              productData.value.height ? (openBlock(), createBlock("div", { key: 3 }, [
                                createVNode("span", { class: "text-slate-500 dark:text-slate-400" }, toDisplayString(unref(t)("height")) + ": ", 1),
                                createVNode("span", { class: "ml-1 font-semibold" }, toDisplayString(productData.value.height), 1)
                              ])) : createCommentVNode("", true)
                            ])
                          ])) : createCommentVNode("", true),
                          attributeValues.value.length ? (openBlock(), createBlock("section", {
                            key: 5,
                            class: "mt-4 p-4 rounded-sm shadow-sm border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900"
                          }, [
                            createVNode("h2", { class: "mb-4 text-lg font-semibold text-slate-800 dark:text-slate-200" }, toDisplayString(unref(t)("attributes")), 1),
                            createVNode("div", { class: "divide-y divide-gray-400 dark:divide-gray-600" }, [
                              (openBlock(true), createBlock(Fragment, null, renderList(attributeValues.value, (item) => {
                                var _a3;
                                return openBlock(), createBlock("div", {
                                  key: item.id,
                                  class: "grid gap-2 py-2 text-sm sm:grid-cols-2"
                                }, [
                                  createVNode("div", { class: "font-semibold text-slate-600 dark:text-slate-400" }, toDisplayString(((_a3 = item.attribute) == null ? void 0 : _a3.title) || "—"), 1),
                                  createVNode("div", { class: "text-slate-800 dark:text-slate-200" }, toDisplayString(getAttributeValue(item)), 1)
                                ]);
                              }), 128))
                            ])
                          ])) : createCommentVNode("", true),
                          categories.value.length ? (openBlock(), createBlock("section", {
                            key: 6,
                            class: "mt-4 p-4 rounded-sm shadow-sm border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900"
                          }, [
                            createVNode("h2", { class: "mb-3 text-lg font-semibold text-slate-800 dark:text-slate-200" }, toDisplayString(unref(t)("categories")), 1),
                            createVNode("div", { class: "flex flex-wrap gap-2" }, [
                              (openBlock(true), createBlock(Fragment, null, renderList(categories.value, (category) => {
                                return openBlock(), createBlock(unref(Link), {
                                  key: category.id,
                                  href: _ctx.route("public.marketCategories.show", {
                                    url: category.url
                                  }),
                                  class: "rounded-sm border border-slate-300 dark:border-slate-600 px-3 py-1 text-xs font-semibold text-slate-600 dark:text-slate-300 hover:text-blue-600 dark:hover:text-blue-400 transition bg-slate-50 dark:bg-slate-950 hover:bg-slate-100 dark:hover:bg-slate-800"
                                }, {
                                  default: withCtx(() => {
                                    var _a3;
                                    return [
                                      createTextVNode(toDisplayString(((_a3 = category.translation) == null ? void 0 : _a3.title) || ""), 1)
                                    ];
                                  }),
                                  _: 2
                                }, 1032, ["href"]);
                              }), 128))
                            ])
                          ])) : createCommentVNode("", true),
                          tags.value.length ? (openBlock(), createBlock("section", {
                            key: 7,
                            class: "mt-4 p-4 rounded-sm shadow-sm border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900"
                          }, [
                            createVNode("h2", { class: "mb-3 text-lg font-semibold text-slate-800 dark:text-slate-200" }, toDisplayString(unref(t)("tags")), 1),
                            createVNode("div", { class: "flex flex-wrap gap-2" }, [
                              (openBlock(true), createBlock(Fragment, null, renderList(tags.value, (tag) => {
                                return openBlock(), createBlock(unref(Link), {
                                  key: tag.id,
                                  href: _ctx.route("public.marketTags.show", {
                                    url: tag.url
                                  }),
                                  class: "rounded-sm border border-slate-300 dark:border-slate-600 px-3 py-1 text-xs font-semibold text-slate-600 dark:text-slate-300 hover:text-blue-600 dark:hover:text-blue-400 transition bg-slate-50 dark:bg-slate-950 hover:bg-slate-100 dark:hover:bg-slate-800"
                                }, {
                                  default: withCtx(() => {
                                    var _a3;
                                    return [
                                      createTextVNode(toDisplayString(((_a3 = tag.translation) == null ? void 0 : _a3.title) || tag.title), 1)
                                    ];
                                  }),
                                  _: 2
                                }, 1032, ["href"]);
                              }), 128))
                            ])
                          ])) : createCommentVNode("", true),
                          relatedProducts.value.length ? (openBlock(), createBlock("section", {
                            key: 8,
                            class: "mt-6"
                          }, [
                            createVNode("h2", { class: "mb-4 text-xl font-semibold text-center text-slate-800 dark:text-slate-200" }, toDisplayString(unref(t)("relatedProducts")), 1),
                            createVNode(_sfc_main$1, {
                              products: relatedProducts.value,
                              cols: relatedGridCols.value,
                              locale: props.locale
                            }, null, 8, ["products", "cols", "locale"])
                          ])) : createCommentVNode("", true)
                        ], 8, ["itemid"])
                      ])
                    ]),
                    showRight.value ? (openBlock(), createBlock("aside", {
                      key: 1,
                      class: ["shrink-0 pr-3 transition-all duration-300", rightCollapsed.value ? "lg:w-6" : "lg:w-72"]
                    }, [
                      createVNode(_sfc_main$6, {
                        collapsed: rightCollapsed.value,
                        onCollapsed: ($event) => rightCollapsed.value = $event
                      }, null, 8, ["collapsed", "onCollapsed"])
                    ], 2)) : createCommentVNode("", true)
                  ]),
                  createVNode(_sfc_main$7, { products: unref(recentlyViewed) }, null, 8, ["products"])
                ])
              ]),
              createVNode(_sfc_main$8),
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
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Pages/Public/Default/Market/MarketProducts/Show.vue");
  return _sfc_setup ? _sfc_setup(props, ctx) : void 0;
};
export {
  _sfc_main as default
};
