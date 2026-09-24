import { useAttrs, computed, ref, watch, onMounted, onBeforeUnmount, mergeProps, useSSRContext } from "vue";
import { ssrRenderAttrs, ssrRenderAttr, ssrRenderList, ssrRenderClass } from "vue/server-renderer";
import { _ as _export_sfc } from "./_plugin-vue_export-helper-1tPrXgE0.js";
const _sfc_main = /* @__PURE__ */ Object.assign({
  inheritAttrs: false
}, {
  __name: "UniversalImageSlider",
  __ssrInlineRender: true,
  props: {
    entity: {
      type: Object,
      default: () => ({})
    },
    alt: {
      type: String,
      default: ""
    },
    heightClass: {
      type: String,
      default: "h-48"
    },
    roundedClass: {
      type: String,
      default: ""
    },
    wrapperClass: {
      type: String,
      default: ""
    },
    imgClass: {
      type: String,
      default: "w-full h-full object-cover transition duration-300 group-hover:scale-105"
    },
    showDots: {
      type: Boolean,
      default: true
    },
    autoplay: {
      type: Boolean,
      default: true
    },
    interval: {
      type: Number,
      default: 4e3
    },
    width: {
      type: Number,
      default: 800
    },
    height: {
      type: Number,
      default: 450
    }
  },
  setup(__props) {
    const attrs = useAttrs();
    const props = __props;
    const entityTitle = computed(() => {
      var _a, _b, _c;
      return props.alt || ((_b = (_a = props.entity) == null ? void 0 : _a.translation) == null ? void 0 : _b.title) || ((_c = props.entity) == null ? void 0 : _c.title) || "";
    });
    const normalizeImages = (entity) => {
      var _a;
      const raw = Array.isArray(entity == null ? void 0 : entity.images) ? entity.images : Array.isArray((_a = entity == null ? void 0 : entity.images) == null ? void 0 : _a.data) ? entity.images.data : [];
      return [...raw].sort((a, b) => {
        var _a2, _b;
        const aOrder = Number((a == null ? void 0 : a.order) ?? ((_a2 = a == null ? void 0 : a.pivot) == null ? void 0 : _a2.order) ?? 999999);
        const bOrder = Number((b == null ? void 0 : b.order) ?? ((_b = b == null ? void 0 : b.pivot) == null ? void 0 : _b.order) ?? 999999);
        return aOrder - bOrder;
      }).map((img, index) => ({
        id: (img == null ? void 0 : img.id) ?? `img-${index}`,
        alt: (img == null ? void 0 : img.alt) || entityTitle.value,
        src: (img == null ? void 0 : img.webp_url) || (img == null ? void 0 : img.url) || (img == null ? void 0 : img.image_url) || (img == null ? void 0 : img.thumb_url) || null
      })).filter((img) => !!img.src);
    };
    const images = computed(() => normalizeImages(props.entity));
    const hasImages = computed(() => images.value.length > 0);
    const hasManyImages = computed(() => images.value.length > 1);
    const currentIndex = ref(0);
    const isHovered = ref(false);
    const currentImage = computed(() => images.value[currentIndex.value] ?? null);
    const imageAttrs = computed(() => {
      const {
        class: _class,
        style: _style,
        ...rest
      } = attrs;
      return rest;
    });
    const nextSlide = () => {
      if (!hasManyImages.value) return;
      currentIndex.value = (currentIndex.value + 1) % images.value.length;
    };
    let timer = null;
    const stopAutoplay = () => {
      if (!timer) return;
      clearInterval(timer);
      timer = null;
    };
    const startAutoplay = () => {
      stopAutoplay();
      if (!props.autoplay || !hasManyImages.value) return;
      timer = setInterval(() => {
        if (!isHovered.value) nextSlide();
      }, Math.max(1500, Number(props.interval) || 4e3));
    };
    watch(images, () => {
      currentIndex.value = 0;
      startAutoplay();
    });
    watch(() => props.interval, startAutoplay);
    watch(() => props.autoplay, startAutoplay);
    onMounted(startAutoplay);
    onBeforeUnmount(stopAutoplay);
    return (_ctx, _push, _parent, _attrs) => {
      var _a, _b, _c, _d, _e, _f;
      if (hasImages.value) {
        _push(`<div${ssrRenderAttrs(mergeProps({
          class: ["relative overflow-hidden group", [__props.heightClass, __props.roundedClass, __props.wrapperClass]]
        }, _attrs))} data-v-9920f4d4>`);
        if (!hasManyImages.value) {
          _push(`<img${ssrRenderAttrs(mergeProps(imageAttrs.value, {
            src: (_a = currentImage.value) == null ? void 0 : _a.src,
            alt: ((_b = currentImage.value) == null ? void 0 : _b.alt) || entityTitle.value,
            class: __props.imgClass,
            width: __props.width,
            height: __props.height,
            loading: "lazy",
            decoding: "async"
          }))} data-v-9920f4d4>`);
        } else {
          _push(`<!--[--><img${ssrRenderAttrs(mergeProps({
            key: ((_c = currentImage.value) == null ? void 0 : _c.id) || ((_d = currentImage.value) == null ? void 0 : _d.src)
          }, imageAttrs.value, {
            src: (_e = currentImage.value) == null ? void 0 : _e.src,
            alt: ((_f = currentImage.value) == null ? void 0 : _f.alt) || entityTitle.value,
            class: __props.imgClass,
            width: __props.width,
            height: __props.height,
            loading: "lazy",
            decoding: "async"
          }))} data-v-9920f4d4>`);
          if (__props.showDots) {
            _push(`<div class="absolute bottom-2 left-1/2 -translate-x-1/2 flex items-center gap-1.5" role="group"${ssrRenderAttr("aria-label", entityTitle.value)} data-v-9920f4d4><!--[-->`);
            ssrRenderList(images.value, (img, index) => {
              _push(`<button type="button" class="${ssrRenderClass([index === currentIndex.value ? "bg-red-600 dark:bg-red-400 shadow border border-rose-600" : "bg-white/50 hover:bg-white/80 border border-gray-600", "h-2 w-2 rounded-full transition"])}"${ssrRenderAttr("aria-label", `${index + 1} / ${images.value.length}: ${img.alt || entityTitle.value}`)}${ssrRenderAttr("aria-current", index === currentIndex.value ? "true" : void 0)} data-v-9920f4d4></button>`);
            });
            _push(`<!--]--></div>`);
          } else {
            _push(`<!---->`);
          }
          _push(`<!--]-->`);
        }
        _push(`</div>`);
      } else {
        _push(`<!---->`);
      }
    };
  }
});
const _sfc_setup = _sfc_main.setup;
_sfc_main.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Components/Public/Default/Images/UniversalImageSlider.vue");
  return _sfc_setup ? _sfc_setup(props, ctx) : void 0;
};
const UniversalImageSlider = /* @__PURE__ */ _export_sfc(_sfc_main, [["__scopeId", "data-v-9920f4d4"]]);
export {
  UniversalImageSlider as U
};
