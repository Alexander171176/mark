import { ref, computed, watch, mergeProps, useSSRContext } from "vue";
import { ssrRenderAttrs, ssrRenderAttr, ssrRenderClass, ssrRenderList } from "vue/server-renderer";
import { _ as _export_sfc } from "./_plugin-vue_export-helper-1tPrXgE0.js";
const _sfc_main = {
  __name: "ImageGalleryMain",
  __ssrInlineRender: true,
  props: {
    images: {
      type: Array,
      default: () => []
    },
    alt: {
      type: String,
      default: ""
    },
    fallbackSrc: {
      type: String,
      default: "/article_images/default-image.png"
    },
    roundedClass: {
      type: String,
      default: "rounded-sm"
    },
    shadowClass: {
      type: String,
      default: "shadow-md shadow-gray-600 dark:shadow-gray-900"
    },
    imgClass: {
      type: String,
      default: "w-full h-full object-cover"
    },
    showDots: {
      type: Boolean,
      default: true
    },
    showArrows: {
      type: Boolean,
      default: true
    },
    /**
     * Schema.org itemprop.
     *
     * Компонент универсальный, поэтому по умолчанию
     * никакую Schema.org-семантику не навязываем.
     */
    itemprop: {
      type: String,
      default: ""
    },
    /**
     * Основное изображение страницы можно загружать eagerly,
     * остальные сценарии оставляем lazy по умолчанию.
     */
    loading: {
      type: String,
      default: "lazy"
    },
    fetchpriority: {
      type: String,
      default: "auto"
    },
    decoding: {
      type: String,
      default: "async"
    },
    /**
     * Доступность элементов управления.
     *
     * Пока не создаём новые i18n-ключи внутри
     * универсального компонента.
     */
    previousLabel: {
      type: String,
      default: "Previous image"
    },
    nextLabel: {
      type: String,
      default: "Next image"
    },
    slideLabel: {
      type: String,
      default: "Image"
    }
  },
  setup(__props) {
    const props = __props;
    const currentIndex = ref(0);
    const normalizedImages = computed(() => {
      const raw = Array.isArray(props.images) ? props.images : [];
      return [...raw].sort((a, b) => {
        var _a, _b;
        const aOrder = Number((a == null ? void 0 : a.order) ?? ((_a = a == null ? void 0 : a.pivot) == null ? void 0 : _a.order) ?? 999999);
        const bOrder = Number((b == null ? void 0 : b.order) ?? ((_b = b == null ? void 0 : b.pivot) == null ? void 0 : _b.order) ?? 999999);
        return aOrder - bOrder;
      }).map((img, index) => {
        const src = (img == null ? void 0 : img.webp_url) || (img == null ? void 0 : img.image_url) || (img == null ? void 0 : img.url) || (img == null ? void 0 : img.thumb_url) || null;
        return {
          id: (img == null ? void 0 : img.id) ?? `img-${index}`,
          alt: (img == null ? void 0 : img.alt) || props.alt || "",
          src
        };
      }).filter((img) => !!img.src);
    });
    const totalImages = computed(
      () => normalizedImages.value.length
    );
    watch(
      () => normalizedImages.value,
      () => {
        currentIndex.value = 0;
      },
      { deep: true }
    );
    const safeIndex = computed(() => {
      if (!totalImages.value) {
        return 0;
      }
      return Math.min(
        currentIndex.value,
        totalImages.value - 1
      );
    });
    const currentImage = computed(() => {
      if (!totalImages.value) {
        return null;
      }
      return normalizedImages.value[safeIndex.value] ?? normalizedImages.value[0] ?? null;
    });
    const currentImageSrc = computed(
      () => {
        var _a;
        return ((_a = currentImage.value) == null ? void 0 : _a.src) || props.fallbackSrc;
      }
    );
    const currentImageAlt = computed(
      () => {
        var _a;
        return ((_a = currentImage.value) == null ? void 0 : _a.alt) || props.alt || "";
      }
    );
    const slideAriaLabel = (index) => `${props.slideLabel} ${index + 1}`;
    return (_ctx, _push, _parent, _attrs) => {
      _push(`<div${ssrRenderAttrs(mergeProps({
        class: "relative w-full h-full overflow-hidden",
        role: "region",
        "aria-label": __props.alt || void 0
      }, _attrs))} data-v-5de23db3><img${ssrRenderAttr("src", currentImageSrc.value)}${ssrRenderAttr("alt", currentImageAlt.value)} class="${ssrRenderClass([
        __props.imgClass,
        __props.roundedClass,
        __props.shadowClass
      ])}"${ssrRenderAttr("loading", __props.loading)}${ssrRenderAttr("fetchpriority", __props.fetchpriority)}${ssrRenderAttr("decoding", __props.decoding)}${ssrRenderAttr("itemprop", __props.itemprop || void 0)} data-v-5de23db3>`);
      if (__props.showArrows && totalImages.value > 1) {
        _push(`<button type="button" class="absolute top-1/2 left-0 -translate-y-1/2 bg-gray-700/75 text-white px-3 py-1 rounded-r focus:outline-none focus-visible:ring-2 focus-visible:ring-white transition hover:bg-gray-800/85"${ssrRenderAttr("aria-label", __props.previousLabel)} data-v-5de23db3> ❮ </button>`);
      } else {
        _push(`<!---->`);
      }
      if (__props.showArrows && totalImages.value > 1) {
        _push(`<button type="button" class="absolute top-1/2 right-0 -translate-y-1/2 bg-gray-700/75 text-white px-3 py-1 rounded-l focus:outline-none focus-visible:ring-2 focus-visible:ring-white transition hover:bg-gray-800/85"${ssrRenderAttr("aria-label", __props.nextLabel)} data-v-5de23db3> ❯ </button>`);
      } else {
        _push(`<!---->`);
      }
      if (__props.showDots && totalImages.value > 1) {
        _push(`<div class="absolute bottom-2 left-1/2 -translate-x-1/2 flex items-center gap-2" data-v-5de23db3><!--[-->`);
        ssrRenderList(totalImages.value, (_, index) => {
          _push(`<button type="button" class="${ssrRenderClass([
            safeIndex.value === index ? "bg-red-500" : "bg-white/80 hover:bg-white",
            "w-2.5 h-2.5 rounded-full border border-gray-400 transition focus:outline-none focus-visible:ring-2 focus-visible:ring-white"
          ])}"${ssrRenderAttr("aria-label", slideAriaLabel(index))}${ssrRenderAttr(
            "aria-current",
            safeIndex.value === index ? "true" : void 0
          )} data-v-5de23db3></button>`);
        });
        _push(`<!--]--></div>`);
      } else {
        _push(`<!---->`);
      }
      _push(`</div>`);
    };
  }
};
const _sfc_setup = _sfc_main.setup;
_sfc_main.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Components/Public/Default/Media/ImageGalleryMain.vue");
  return _sfc_setup ? _sfc_setup(props, ctx) : void 0;
};
const ImageGalleryMain = /* @__PURE__ */ _export_sfc(_sfc_main, [["__scopeId", "data-v-5de23db3"]]);
export {
  ImageGalleryMain as I
};
