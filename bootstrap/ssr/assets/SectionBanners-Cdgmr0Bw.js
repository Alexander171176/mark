import { computed, ref, onMounted, onBeforeUnmount, watch, mergeProps, unref, useSSRContext, onUnmounted, withCtx, createTextVNode, toDisplayString } from "vue";
import { ssrRenderAttrs, ssrRenderList, ssrRenderAttr, ssrRenderClass, ssrInterpolate, ssrRenderComponent } from "vue/server-renderer";
import { _ as _export_sfc } from "./_plugin-vue_export-helper-1tPrXgE0.js";
import { Link, usePage } from "@inertiajs/vue3";
import { useI18n } from "vue-i18n";
const unwrap = (v) => (v == null ? void 0 : v.data) ?? v ?? {};
const unwrapList = (v) => (v == null ? void 0 : v.data) ?? v ?? [];
const _sfc_main$4 = {
  __name: "BannersSidebar",
  __ssrInlineRender: true,
  props: {
    banners: {
      type: [Array, Object],
      default: () => []
    },
    intervalMs: {
      type: Number,
      default: 4500
    },
    pauseOnHover: {
      type: Boolean,
      default: true
    },
    pauseOnHidden: {
      type: Boolean,
      default: true
    }
  },
  setup(__props) {
    const props = __props;
    const list = computed(
      () => unwrapList(props.banners)
    );
    const getBannerTranslation = (banner) => {
      const item = unwrap(banner);
      return (item == null ? void 0 : item.translation) || {};
    };
    const getBannerTitle = (banner) => {
      var _a;
      return ((_a = getBannerTranslation(banner)) == null ? void 0 : _a.title) || "";
    };
    const getBannerLink = (banner) => {
      var _a;
      return ((_a = getBannerTranslation(banner)) == null ? void 0 : _a.link) || "";
    };
    const getBannerImages = (banner) => {
      var _a;
      const item = unwrap(banner);
      const imagesRaw = Array.isArray(item == null ? void 0 : item.images) ? item.images : ((_a = item == null ? void 0 : item.images) == null ? void 0 : _a.data) ?? [];
      return (Array.isArray(imagesRaw) ? imagesRaw : []).slice().sort(
        (a, b) => Number((a == null ? void 0 : a.order) ?? 0) - Number((b == null ? void 0 : b.order) ?? 0)
      ).map((image, index) => {
        const src = (image == null ? void 0 : image.image_url) || (image == null ? void 0 : image.url) || (image == null ? void 0 : image.src) || (image == null ? void 0 : image.path) || (image == null ? void 0 : image.image) || "";
        return {
          id: (image == null ? void 0 : image.id) ?? `${src || "image"}-${(image == null ? void 0 : image.order) ?? index}`,
          src,
          alt: (image == null ? void 0 : image.alt) || getBannerTitle(banner),
          title: (image == null ? void 0 : image.title) || (image == null ? void 0 : image.alt) || getBannerTitle(banner),
          order: Number(
            (image == null ? void 0 : image.order) ?? 0
          )
        };
      }).filter((image) => Boolean(image.src));
    };
    const currentByBanner = ref({});
    const hoveredByBanner = ref({});
    let timer = null;
    const getCurrent = (bannerId) => {
      var _a;
      return Number(
        ((_a = currentByBanner.value) == null ? void 0 : _a[bannerId]) ?? 0
      );
    };
    const canRun = () => {
      return !(props.pauseOnHidden && typeof document !== "undefined" && document.hidden);
    };
    const tick = () => {
      var _a;
      if (!canRun()) {
        return;
      }
      const nextState = {
        ...currentByBanner.value
      };
      for (const bannerItem of list.value) {
        const banner = unwrap(bannerItem);
        const id = banner == null ? void 0 : banner.id;
        if (!id) {
          continue;
        }
        const images = getBannerImages(bannerItem);
        if (images.length <= 1) {
          continue;
        }
        if (props.pauseOnHover && ((_a = hoveredByBanner.value) == null ? void 0 : _a[id])) {
          continue;
        }
        const current = Number(nextState[id] ?? 0);
        nextState[id] = (current + 1) % images.length;
      }
      currentByBanner.value = nextState;
    };
    const stop = () => {
      if (!timer) {
        return;
      }
      clearInterval(timer);
      timer = null;
    };
    const start = () => {
      stop();
      const hasAnySlider = list.value.some(
        (banner) => getBannerImages(banner).length > 1
      );
      if (!hasAnySlider) {
        return;
      }
      timer = setInterval(
        tick,
        Math.max(
          1500,
          Number(props.intervalMs) || 4500
        )
      );
    };
    const resetState = () => {
      var _a;
      const initialState = {};
      for (const banner of list.value) {
        const id = (_a = unwrap(banner)) == null ? void 0 : _a.id;
        if (id) {
          initialState[id] = 0;
        }
      }
      currentByBanner.value = initialState;
    };
    const onVisibilityChange = () => {
      start();
    };
    onMounted(() => {
      resetState();
      start();
      if (props.pauseOnHidden && typeof document !== "undefined") {
        document.addEventListener(
          "visibilitychange",
          onVisibilityChange
        );
      }
    });
    onBeforeUnmount(() => {
      stop();
      if (props.pauseOnHidden && typeof document !== "undefined") {
        document.removeEventListener(
          "visibilitychange",
          onVisibilityChange
        );
      }
    });
    watch(
      () => [
        list.value.length,
        props.intervalMs
      ],
      () => {
        resetState();
        start();
      }
    );
    return (_ctx, _push, _parent, _attrs) => {
      if (list.value.length) {
        _push(`<div${ssrRenderAttrs(mergeProps({ class: "mb-1" }, _attrs))} data-v-100b7db9><div class="grid gap-4" data-v-100b7db9><!--[-->`);
        ssrRenderList(list.value, (banner) => {
          var _a, _b, _c, _d, _e, _f;
          _push(`<div class="overflow-hidden rounded-md border border-slate-400 dark:border-slate-500 shadow-md shadow-gray-400 dark:shadow-gray-800 hover:shadow-lg transition-shadow" data-v-100b7db9>`);
          if (getBannerImages(banner).length) {
            _push(`<div class="relative w-full overflow-hidden bg-slate-100 dark:bg-slate-900 aspect-[16/9] sm:aspect-[4/3] lg:aspect-[16/10]" data-v-100b7db9>`);
            if (getBannerLink(banner)) {
              _push(`<a${ssrRenderAttr("href", getBannerLink(banner))} target="_blank" rel="noopener noreferrer" class="block w-full h-full" data-v-100b7db9><img class="w-full h-full object-cover"${ssrRenderAttr(
                "src",
                (_a = getBannerImages(banner)[getCurrent(
                  unref(unwrap)(banner).id
                )]) == null ? void 0 : _a.src
              )}${ssrRenderAttr(
                "alt",
                (_b = getBannerImages(banner)[getCurrent(
                  unref(unwrap)(banner).id
                )]) == null ? void 0 : _b.alt
              )}${ssrRenderAttr(
                "title",
                (_c = getBannerImages(banner)[getCurrent(
                  unref(unwrap)(banner).id
                )]) == null ? void 0 : _c.title
              )} loading="lazy" data-v-100b7db9></a>`);
            } else {
              _push(`<img class="w-full h-full object-cover"${ssrRenderAttr(
                "src",
                (_d = getBannerImages(banner)[getCurrent(
                  unref(unwrap)(banner).id
                )]) == null ? void 0 : _d.src
              )}${ssrRenderAttr(
                "alt",
                (_e = getBannerImages(banner)[getCurrent(
                  unref(unwrap)(banner).id
                )]) == null ? void 0 : _e.alt
              )}${ssrRenderAttr(
                "title",
                (_f = getBannerImages(banner)[getCurrent(
                  unref(unwrap)(banner).id
                )]) == null ? void 0 : _f.title
              )} loading="lazy" data-v-100b7db9>`);
            }
            if (getBannerImages(banner).length > 1) {
              _push(`<div class="absolute left-0 right-0 bottom-0 px-3 pb-2" data-v-100b7db9><div class="flex items-center justify-center gap-1.5" data-v-100b7db9><!--[-->`);
              ssrRenderList(getBannerImages(banner), (image, index) => {
                _push(`<button type="button" class="${ssrRenderClass([
                  index === getCurrent(
                    unref(unwrap)(banner).id
                  ) ? "bg-orange-400 shadow ring-1 ring-black/40" : "bg-white/60 hover:bg-orange-400/80",
                  "h-2 w-2 rounded-full transition-all"
                ])}"${ssrRenderAttr("aria-label", `banner image ${index + 1}`)}${ssrRenderAttr("title", image.title)} data-v-100b7db9></button>`);
              });
              _push(`<!--]--></div></div>`);
            } else {
              _push(`<!---->`);
            }
            _push(`</div>`);
          } else {
            _push(`<!---->`);
          }
          if (getBannerTitle(banner)) {
            _push(`<div class="p-3" data-v-100b7db9><div class="text-center font-semibold text-sm leading-snug" data-v-100b7db9>`);
            if (getBannerLink(banner)) {
              _push(`<a${ssrRenderAttr("href", getBannerLink(banner))} target="_blank" rel="noopener noreferrer" class="hover:underline transition text-slate-900/85 dark:text-slate-100/85 hover:text-indigo-700 dark:hover:text-indigo-300" data-v-100b7db9>${ssrInterpolate(getBannerTitle(banner))}</a>`);
            } else {
              _push(`<span class="text-slate-900/85 dark:text-slate-100/85" data-v-100b7db9>${ssrInterpolate(getBannerTitle(banner))}</span>`);
            }
            _push(`</div></div>`);
          } else {
            _push(`<!---->`);
          }
          _push(`</div>`);
        });
        _push(`<!--]--></div></div>`);
      } else {
        _push(`<!---->`);
      }
    };
  }
};
const _sfc_setup$4 = _sfc_main$4.setup;
_sfc_main$4.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Components/Public/Default/Blog/BlogBanner/BannersSidebar.vue");
  return _sfc_setup$4 ? _sfc_setup$4(props, ctx) : void 0;
};
const BannersSidebar = /* @__PURE__ */ _export_sfc(_sfc_main$4, [["__scopeId", "data-v-100b7db9"]]);
const _sfc_main$3 = {
  __name: "VideoPlayer",
  __ssrInlineRender: true,
  props: {
    video: {
      type: Object,
      required: true
    }
  },
  setup(__props) {
    const props = __props;
    const { t } = useI18n();
    const currentSlide = ref(0);
    const intervalId = ref(null);
    const activeVideoId = ref(null);
    const videoTitle = computed(
      () => {
        var _a, _b;
        return ((_b = (_a = props.video) == null ? void 0 : _a.translation) == null ? void 0 : _b.title) || "";
      }
    );
    const videoImages = computed(
      () => {
        var _a;
        return Array.isArray((_a = props.video) == null ? void 0 : _a.images) ? props.video.images : [];
      }
    );
    const hasImages = computed(
      () => videoImages.value.length > 0
    );
    const getImageUrl = (image) => (image == null ? void 0 : image.webp_url) || (image == null ? void 0 : image.thumb_url) || (image == null ? void 0 : image.image_url) || (image == null ? void 0 : image.url) || "";
    const extractYouTubeId = (value) => {
      if (!value) {
        return null;
      }
      const source = String(value).trim();
      if (/^[a-zA-Z0-9_-]{11}$/.test(source)) {
        return source;
      }
      const match = source.match(
        /(?:youtube\.com\/(?:watch\?v=|embed\/|shorts\/)|youtu\.be\/|youtube-nocookie\.com\/embed\/)([a-zA-Z0-9_-]{11})/
      );
      if (match == null ? void 0 : match[1]) {
        return match[1];
      }
      try {
        const url = new URL(source);
        return url.searchParams.get("v");
      } catch {
        return null;
      }
    };
    const normalizeYouTubeEmbedUrl = (value) => {
      const id = extractYouTubeId(
        value
      );
      return id ? `https://www.youtube-nocookie.com/embed/${id}?rel=0` : null;
    };
    const extractVimeoId = (value) => {
      if (!value) {
        return null;
      }
      const source = String(value).trim();
      if (/^\d+$/.test(source)) {
        return source;
      }
      const match = source.match(
        /vimeo\.com\/(?:video\/)?(\d+)/
      );
      return (match == null ? void 0 : match[1]) || null;
    };
    const normalizeVimeoEmbedUrl = (value) => {
      if (!value) {
        return null;
      }
      const source = String(value).trim();
      if (source.includes(
        "player.vimeo.com/video/"
      )) {
        return source;
      }
      const id = extractVimeoId(
        source
      );
      return id ? `https://player.vimeo.com/video/${id}` : null;
    };
    const videoUrl = computed(() => {
      const video = props.video || {};
      switch (video.source_type) {
        case "youtube":
          return normalizeYouTubeEmbedUrl(
            video.embed_url || video.external_video_id || video.video_url
          );
        case "vimeo":
          return normalizeVimeoEmbedUrl(
            video.embed_url || video.external_video_id || video.video_url
          );
        case "local":
          return video.video_url || null;
        case "code":
          return video.embed_code || null;
        default:
          return null;
      }
    });
    const stopSlideshow = () => {
      if (intervalId.value) {
        clearInterval(
          intervalId.value
        );
        intervalId.value = null;
      }
    };
    const startSlideshow = () => {
      stopSlideshow();
      if (videoImages.value.length > 1) {
        intervalId.value = setInterval(
          () => {
            currentSlide.value = (currentSlide.value + 1) % videoImages.value.length;
          },
          4e3
        );
      }
    };
    watch(
      () => {
        var _a;
        return (_a = props.video) == null ? void 0 : _a.id;
      },
      () => {
        currentSlide.value = 0;
        activeVideoId.value = null;
        startSlideshow();
      }
    );
    onMounted(() => {
      startSlideshow();
    });
    onUnmounted(() => {
      stopSlideshow();
    });
    return (_ctx, _push, _parent, _attrs) => {
      _push(`<div${ssrRenderAttrs(mergeProps({ class: "relative w-full aspect-video bg-black mb-4 overflow-hidden" }, _attrs))} data-v-31cbbf50>`);
      if (hasImages.value && activeVideoId.value !== __props.video.id) {
        _push(`<div class="relative w-full h-full" data-v-31cbbf50><!--[-->`);
        ssrRenderList(videoImages.value, (img, index) => {
          _push(`<!--[-->`);
          if (getImageUrl(img)) {
            _push(`<img${ssrRenderAttr("src", getImageUrl(img))}${ssrRenderAttr(
              "alt",
              img.alt || videoTitle.value
            )} loading="lazy" class="${ssrRenderClass([{
              "slide-fade-active": index === currentSlide.value
            }, "slide-fade w-full h-full object-cover"])}" data-v-31cbbf50>`);
          } else {
            _push(`<!---->`);
          }
          _push(`<!--]-->`);
        });
        _push(`<!--]--><div class="absolute inset-0 flex items-center justify-center z-20" data-v-31cbbf50><button type="button"${ssrRenderAttr("aria-label", videoTitle.value || unref(t)("videos"))} class="bg-white/30 hover:bg-white/40 backdrop-blur-md rounded-full p-2 border-8 border-white/30" data-v-31cbbf50><svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8 text-red-600" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true" data-v-31cbbf50><path d="M8 5v14l11-7z" data-v-31cbbf50></path></svg></button></div></div>`);
      } else {
        _push(`<!--[-->`);
        if (__props.video.source_type === "code" && videoUrl.value) {
          _push(`<div class="w-full h-full" data-v-31cbbf50>${videoUrl.value ?? ""}</div>`);
        } else if (videoUrl.value && [
          "youtube",
          "vimeo"
        ].includes(
          __props.video.source_type
        )) {
          _push(`<iframe${ssrRenderAttr("src", videoUrl.value)}${ssrRenderAttr("title", videoTitle.value)} class="w-full h-full" frameborder="0" allow="accelerometer;
                       autoplay;
                       clipboard-write;
                       encrypted-media;
                       gyroscope;
                       picture-in-picture;
                       fullscreen" loading="lazy" allowfullscreen data-v-31cbbf50></iframe>`);
        } else if (__props.video.source_type === "local" && videoUrl.value) {
          _push(`<video class="w-full h-full object-contain" controls preload="metadata" data-v-31cbbf50><source${ssrRenderAttr("src", videoUrl.value)} data-v-31cbbf50> ${ssrInterpolate(unref(t)("videoNotSupported"))}</video>`);
        } else {
          _push(`<div class="flex h-full w-full items-center justify-center text-sm text-slate-200" data-v-31cbbf50>${ssrInterpolate(unref(t)("videoNotSupported"))}</div>`);
        }
        _push(`<!--]-->`);
      }
      _push(`</div>`);
    };
  }
};
const _sfc_setup$3 = _sfc_main$3.setup;
_sfc_main$3.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Components/Public/Default/Blog/BlogVideo/VideoPlayer.vue");
  return _sfc_setup$3 ? _sfc_setup$3(props, ctx) : void 0;
};
const VideoPlayer = /* @__PURE__ */ _export_sfc(_sfc_main$3, [["__scopeId", "data-v-31cbbf50"]]);
const _sfc_main$2 = {
  __name: "VideosSidebar",
  __ssrInlineRender: true,
  props: {
    videos: {
      type: [Array, Object],
      default: () => []
    }
  },
  setup(__props) {
    const props = __props;
    const list = computed(
      () => unwrapList(props.videos)
    );
    const getVideoTitle = (video) => {
      var _a;
      const item = unwrap(video);
      return ((_a = item == null ? void 0 : item.translation) == null ? void 0 : _a.title) || "";
    };
    const getVideoUrl = (video) => {
      const item = unwrap(video);
      return route(
        "public.blogVideos.show",
        {
          url: item == null ? void 0 : item.url
        }
      );
    };
    return (_ctx, _push, _parent, _attrs) => {
      if (list.value.length) {
        _push(`<div${ssrRenderAttrs(_attrs)}><ul><!--[-->`);
        ssrRenderList(list.value, (video) => {
          _push(`<li class="mb-4 overflow-hidden rounded-sm border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 shadow-sm hover:shadow-md transition-shadow">`);
          _push(ssrRenderComponent(VideoPlayer, {
            video: unref(unwrap)(video)
          }, null, _parent));
          _push(`<div class="px-3 pb-3"><div class="text-center font-semibold text-sm leading-snug">`);
          _push(ssrRenderComponent(unref(Link), {
            href: getVideoUrl(video),
            class: "hover:underline transition text-slate-900/85 dark:text-slate-100/85 hover:text-indigo-700 dark:hover:text-indigo-300"
          }, {
            default: withCtx((_, _push2, _parent2, _scopeId) => {
              if (_push2) {
                _push2(`${ssrInterpolate(getVideoTitle(video))}`);
              } else {
                return [
                  createTextVNode(toDisplayString(getVideoTitle(video)), 1)
                ];
              }
            }),
            _: 2
          }, _parent));
          _push(`</div></div></li>`);
        });
        _push(`<!--]--></ul></div>`);
      } else {
        _push(`<!---->`);
      }
    };
  }
};
const _sfc_setup$2 = _sfc_main$2.setup;
_sfc_main$2.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Components/Public/Default/Blog/BlogVideo/VideosSidebar.vue");
  return _sfc_setup$2 ? _sfc_setup$2(props, ctx) : void 0;
};
const _sfc_main$1 = {
  __name: "SectionVideoList",
  __ssrInlineRender: true,
  props: {
    videos: {
      type: Array,
      default: () => []
    }
  },
  setup(__props) {
    const props = __props;
    const normalizedVideos = computed(
      () => Array.isArray(props.videos) ? props.videos : []
    );
    const videoTitle = (video) => {
      var _a;
      return ((_a = video == null ? void 0 : video.translation) == null ? void 0 : _a.title) || "";
    };
    const videoShort = (video) => {
      var _a;
      return ((_a = video == null ? void 0 : video.translation) == null ? void 0 : _a.short) || "";
    };
    const videoAuthorName = (video) => {
      var _a, _b;
      return ((_a = video == null ? void 0 : video.translation) == null ? void 0 : _a.pseudonym) || ((_b = video == null ? void 0 : video.owner) == null ? void 0 : _b.name) || "";
    };
    const videoShowRoute = (video) => route(
      "public.blogVideos.show",
      {
        url: (video == null ? void 0 : video.url) || ""
      }
    );
    const videoImages = (video) => Array.isArray(video == null ? void 0 : video.images) ? video.images : [];
    const imageUrl = (image) => (image == null ? void 0 : image.webp_url) || (image == null ? void 0 : image.image_url) || (image == null ? void 0 : image.thumb_url) || (image == null ? void 0 : image.url) || "";
    const videoThumbnail = (video) => imageUrl(
      videoImages(video)[0]
    );
    const videoPublishedAt = (video) => (video == null ? void 0 : video.published_at) || (video == null ? void 0 : video.created_at) || "";
    const schemaDuration = (seconds) => {
      const value = Number(seconds);
      if (!Number.isFinite(value) || value <= 0) {
        return "";
      }
      const total = Math.floor(value);
      const hours = Math.floor(
        total / 3600
      );
      const minutes = Math.floor(
        total % 3600 / 60
      );
      const secs = total % 60;
      return [
        "PT",
        hours ? `${hours}H` : "",
        minutes ? `${minutes}M` : "",
        secs || !hours && !minutes ? `${secs}S` : ""
      ].join("");
    };
    return (_ctx, _push, _parent, _attrs) => {
      if (normalizedVideos.value.length) {
        _push(`<section${ssrRenderAttrs(mergeProps({
          itemprop: "hasPart",
          itemscope: "",
          itemtype: "https://schema.org/ItemList",
          class: "mt-8 space-y-6"
        }, _attrs))}><meta itemprop="numberOfItems"${ssrRenderAttr("content", normalizedVideos.value.length)}><div class="flex flex-col gap-4"><!--[-->`);
        ssrRenderList(normalizedVideos.value, (video, index) => {
          _push(`<div itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem" class="mb-3 pb-3 border-b border-slate-500 dark:border-slate-100"><meta itemprop="position"${ssrRenderAttr("content", index + 1)}><article itemprop="item" itemscope itemtype="https://schema.org/VideoObject"><meta itemprop="url"${ssrRenderAttr("content", videoShowRoute(video))}><meta itemprop="name"${ssrRenderAttr("content", videoTitle(video))}>`);
          if (videoShort(video)) {
            _push(`<meta itemprop="description"${ssrRenderAttr("content", videoShort(video))}>`);
          } else {
            _push(`<!---->`);
          }
          if (videoThumbnail(video)) {
            _push(`<meta itemprop="thumbnailUrl"${ssrRenderAttr("content", videoThumbnail(video))}>`);
          } else {
            _push(`<!---->`);
          }
          if (videoPublishedAt(video)) {
            _push(`<meta itemprop="uploadDate"${ssrRenderAttr("content", videoPublishedAt(video))}>`);
          } else {
            _push(`<!---->`);
          }
          if (schemaDuration(video == null ? void 0 : video.duration)) {
            _push(`<meta itemprop="duration"${ssrRenderAttr("content", schemaDuration(video == null ? void 0 : video.duration))}>`);
          } else {
            _push(`<!---->`);
          }
          if (videoAuthorName(video)) {
            _push(`<div itemprop="author" itemscope itemtype="https://schema.org/Person"><meta itemprop="name"${ssrRenderAttr("content", videoAuthorName(video))}></div>`);
          } else {
            _push(`<!---->`);
          }
          _push(ssrRenderComponent(VideoPlayer, { video }, null, _parent));
          _push(ssrRenderComponent(unref(Link), {
            href: videoShowRoute(video),
            class: "flex justify-center font-semibold text-md mb-1 hover:underline transition text-indigo-700 dark:text-indigo-300 hover:text-indigo-500 dark:hover:text-indigo-500"
          }, {
            default: withCtx((_, _push2, _parent2, _scopeId) => {
              if (_push2) {
                _push2(`${ssrInterpolate(videoTitle(video))}`);
              } else {
                return [
                  createTextVNode(toDisplayString(videoTitle(video)), 1)
                ];
              }
            }),
            _: 2
          }, _parent));
          if (videoShort(video)) {
            _push(`<p class="flex items-center font-semibold tracking-wide text-xs text-slate-700/85 dark:text-slate-300/85">${ssrInterpolate(videoShort(video))}</p>`);
          } else {
            _push(`<!---->`);
          }
          _push(`</article></div>`);
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
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Components/Public/Default/Blog/BlogVideo/SectionVideoList.vue");
  return _sfc_setup$1 ? _sfc_setup$1(props, ctx) : void 0;
};
const _sfc_main = {
  __name: "SectionBanners",
  __ssrInlineRender: true,
  props: {
    banners: { type: [Array, Object], default: () => [] },
    // автосмена (мс)
    intervalMs: { type: Number, default: 4500 },
    // пауза при наведении
    pauseOnHover: { type: Boolean, default: true },
    // пауза если вкладка скрыта
    pauseOnHidden: { type: Boolean, default: true }
  },
  setup(__props) {
    const { t } = useI18n();
    const props = __props;
    const { appUrl } = usePage().props;
    const unwrapList2 = (v) => (v == null ? void 0 : v.data) ?? v ?? [];
    const list = computed(() => {
      const banners = unwrapList2(props.banners);
      return Array.isArray(banners) ? banners : [];
    });
    const hasLink = (banner) => {
      var _a;
      const link = (_a = unwrap(banner)) == null ? void 0 : _a.link;
      return typeof link === "string" && link.trim().length > 0;
    };
    const getImgSrc = (imgPath) => {
      if (!imgPath) return "";
      const base = (appUrl == null ? void 0 : appUrl.endsWith("/")) ? appUrl.slice(0, -1) : appUrl || "";
      const path = imgPath.startsWith("/") ? imgPath.slice(1) : imgPath;
      return `${base}/storage/${path}`;
    };
    const normalizeSrc = (raw) => {
      if (!raw) return "";
      if (/^https?:\/\//i.test(raw)) return raw;
      if (raw.startsWith("/")) return raw;
      return getImgSrc(raw);
    };
    const normalizeBannerImages = (banner) => {
      var _a;
      const b = unwrap(banner);
      const imgsRaw = Array.isArray(b == null ? void 0 : b.images) ? b.images : ((_a = b == null ? void 0 : b.images) == null ? void 0 : _a.data) ?? [];
      return (Array.isArray(imgsRaw) ? imgsRaw : []).slice().sort((a, c) => Number((a == null ? void 0 : a.order) ?? 0) - Number((c == null ? void 0 : c.order) ?? 0)).map((img, idx) => {
        const raw = (img == null ? void 0 : img.image_url) || (img == null ? void 0 : img.url) || (img == null ? void 0 : img.src) || (img == null ? void 0 : img.path) || (img == null ? void 0 : img.image) || null;
        const src = normalizeSrc(raw);
        return {
          id: (img == null ? void 0 : img.id) ?? `${raw}-${(img == null ? void 0 : img.order) ?? idx}`,
          src,
          alt: (img == null ? void 0 : img.alt) ?? "",
          title: (img == null ? void 0 : img.title) ?? (img == null ? void 0 : img.alt) ?? "",
          order: Number((img == null ? void 0 : img.order) ?? 0)
        };
      }).filter((img) => !!img.src);
    };
    const imagesMap = computed(() => {
      var _a;
      const map = {};
      for (const b of list.value) {
        const id = (_a = unwrap(b)) == null ? void 0 : _a.id;
        if (!id) continue;
        map[id] = normalizeBannerImages(b);
      }
      return map;
    });
    const getImages = (banner) => {
      var _a;
      const id = (_a = unwrap(banner)) == null ? void 0 : _a.id;
      return id && imagesMap.value[id] ? imagesMap.value[id] : [];
    };
    const currentByBanner = ref({});
    const hoveredByBanner = ref({});
    let timer = null;
    const getCurrent = (bannerId) => {
      var _a;
      return Number(((_a = currentByBanner.value) == null ? void 0 : _a[bannerId]) ?? 0);
    };
    const canRun = () => {
      return !(props.pauseOnHidden && typeof document !== "undefined" && document.hidden);
    };
    const tick = () => {
      var _a, _b;
      if (!canRun()) return;
      const nextState = { ...currentByBanner.value };
      for (const b of list.value) {
        const id = (_a = unwrap(b)) == null ? void 0 : _a.id;
        if (!id) continue;
        const imgs = getImages(b);
        if (imgs.length <= 1) continue;
        if (props.pauseOnHover && ((_b = hoveredByBanner.value) == null ? void 0 : _b[id])) continue;
        const cur = Number(nextState[id] ?? 0);
        nextState[id] = (cur + 1) % imgs.length;
      }
      currentByBanner.value = nextState;
    };
    const stop = () => {
      if (timer) {
        clearInterval(timer);
        timer = null;
      }
    };
    const start = () => {
      stop();
      const hasAnySlider = list.value.some((b) => getImages(b).length > 1);
      if (!hasAnySlider) return;
      timer = setInterval(tick, Math.max(1500, Number(props.intervalMs) || 4500));
    };
    const onVisibilityChange = () => start();
    onMounted(() => {
      var _a;
      const init = {};
      for (const b of list.value) {
        const id = (_a = unwrap(b)) == null ? void 0 : _a.id;
        if (id) init[id] = 0;
      }
      currentByBanner.value = init;
      start();
      if (props.pauseOnHidden && typeof document !== "undefined") {
        document.addEventListener("visibilitychange", onVisibilityChange);
      }
    });
    onBeforeUnmount(() => {
      stop();
      if (props.pauseOnHidden && typeof document !== "undefined") {
        document.removeEventListener("visibilitychange", onVisibilityChange);
      }
    });
    watch(
      () => [list.value.length, props.intervalMs],
      () => start()
    );
    return (_ctx, _push, _parent, _attrs) => {
      if (list.value.length) {
        _push(`<div${ssrRenderAttrs(mergeProps({ class: "mt-6" }, _attrs))} data-v-fef66697><div class="grid grid-cols-1 lg:grid-cols-2 gap-4" data-v-fef66697><!--[-->`);
        ssrRenderList(list.value, (b) => {
          var _a, _b, _c, _d, _e, _f;
          _push(`<div class="overflow-hidden rounded-md border border-gray-300 dark:border-gray-700 bg-slate-100 dark:bg-slate-900 shadow-sm hover:shadow-md transition-shadow" data-v-fef66697>`);
          if (getImages(b).length) {
            _push(`<div class="relative w-full overflow-hidden bg-slate-100 dark:bg-slate-900 aspect-[16/9] sm:aspect-[4/3] lg:aspect-[16/10]" data-v-fef66697>`);
            if (hasLink(b)) {
              _push(`<a${ssrRenderAttr("href", unref(unwrap)(b).link)} target="_blank" rel="noopener noreferrer" class="block w-full h-full" data-v-fef66697><img class="w-full h-full object-cover"${ssrRenderAttr("src", (_a = getImages(b)[getCurrent(unref(unwrap)(b).id)]) == null ? void 0 : _a.src)}${ssrRenderAttr("alt", (_b = getImages(b)[getCurrent(unref(unwrap)(b).id)]) == null ? void 0 : _b.alt)}${ssrRenderAttr("title", (_c = getImages(b)[getCurrent(unref(unwrap)(b).id)]) == null ? void 0 : _c.title)} loading="lazy" data-v-fef66697></a>`);
            } else {
              _push(`<img class="w-full h-full object-cover"${ssrRenderAttr("src", (_d = getImages(b)[getCurrent(unref(unwrap)(b).id)]) == null ? void 0 : _d.src)}${ssrRenderAttr("alt", (_e = getImages(b)[getCurrent(unref(unwrap)(b).id)]) == null ? void 0 : _e.alt)}${ssrRenderAttr("title", (_f = getImages(b)[getCurrent(unref(unwrap)(b).id)]) == null ? void 0 : _f.title)} loading="lazy" data-v-fef66697>`);
            }
            if (getImages(b).length > 1) {
              _push(`<div class="absolute left-0 right-0 bottom-0 px-3 pb-2" data-v-fef66697><div class="flex items-center justify-center gap-1.5" data-v-fef66697><!--[-->`);
              ssrRenderList(getImages(b), (img, idx) => {
                _push(`<button type="button" class="${ssrRenderClass([idx === getCurrent(unref(unwrap)(b).id) ? "bg-orange-400 shadow ring-1 ring-black/40" : "bg-white/60 hover:bg-orange-400/80", "h-2 w-2 rounded-full transition-all"])}"${ssrRenderAttr("aria-label", `banner image ${idx + 1}`)} data-v-fef66697></button>`);
              });
              _push(`<!--]--></div></div>`);
            } else {
              _push(`<!---->`);
            }
            _push(`</div>`);
          } else {
            _push(`<!---->`);
          }
          _push(`<div class="p-4" data-v-fef66697><div class="flex items-start justify-between gap-3" data-v-fef66697><div class="min-w-0 w-full" data-v-fef66697>`);
          if (unref(unwrap)(b).title) {
            _push(`<div class="text-md font-semibold text-center leading-snug" data-v-fef66697>`);
            if (hasLink(b)) {
              _push(`<a${ssrRenderAttr("href", unref(unwrap)(b).link)} target="_blank" rel="noopener noreferrer" class="hover:underline transition text-indigo-700 dark:text-indigo-300 hover:text-indigo-500 dark:hover:text-indigo-500" data-v-fef66697>${ssrInterpolate(unref(unwrap)(b).title)}</a>`);
            } else {
              _push(`<span class="text-slate-700 dark:text-slate-300" data-v-fef66697>${ssrInterpolate(unref(unwrap)(b).title)}</span>`);
            }
            _push(`</div>`);
          } else {
            _push(`<!---->`);
          }
          if (unref(unwrap)(b).short) {
            _push(`<div class="mt-2 text-xs font-semibold text-slate-700/85 dark:text-slate-300/85 leading-relaxed" data-v-fef66697>${ssrInterpolate(unref(unwrap)(b).short)}</div>`);
          } else {
            _push(`<!---->`);
          }
          _push(`</div></div>`);
          if (hasLink(b)) {
            _push(`<div class="gap-3 flex items-center justify-between mt-2 text-center text-xs break-all" data-v-fef66697><div class="font-semibold text-[10px] text-sky-700 dark:text-sky-300" data-v-fef66697>${ssrInterpolate(unref(unwrap)(b).link)}</div><a${ssrRenderAttr("href", unref(unwrap)(b).link)} target="_blank" rel="noopener noreferrer" class="shrink-0 p-1 rounded-sm border border-slate-400 dark:border-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700 transition"${ssrRenderAttr("title", unref(t)("openLink"))} data-v-fef66697><svg xmlns="http://www.w3.org/2000/svg" class="w-3 h-3" height="24" width="24" viewBox="0 0 24 24" data-v-fef66697><path class="fill-current text-blue-500" d="M22,1H14a1,1,0,0,0-.707,1.707L16.586,6l-6.293,6.293a1,1,0,1,0,1.414,1.414L18,7.414l3.293,3.293A1,1,0,0,0,22,11a.987.987,0,0,0,.383-.076A1,1,0,0,0,23,10V2A1,1,0,0,0,22,1Z" data-v-fef66697></path><path class="fill-current text-blue-500" d="M4,23H18a3,3,0,0,0,3-3V15a1,1,0,0,0-2,0v5a1,1,0,0,1-1,1H4a1,1,0,0,1-1-1V6A1,1,0,0,1,4,5H9A1,1,0,0,0,9,3H4A3,3,0,0,0,1,6V20A3,3,0,0,0,4,23Z" data-v-fef66697></path></svg></a></div>`);
          } else {
            _push(`<!---->`);
          }
          _push(`</div></div>`);
        });
        _push(`<!--]--></div></div>`);
      } else {
        _push(`<!---->`);
      }
    };
  }
};
const _sfc_setup = _sfc_main.setup;
_sfc_main.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Components/Public/Default/Blog/BlogBanner/SectionBanners.vue");
  return _sfc_setup ? _sfc_setup(props, ctx) : void 0;
};
const SectionBanners = /* @__PURE__ */ _export_sfc(_sfc_main, [["__scopeId", "data-v-fef66697"]]);
export {
  BannersSidebar as B,
  SectionBanners as S,
  VideoPlayer as V,
  _sfc_main$1 as _,
  unwrap as a,
  _sfc_main$2 as b,
  unwrapList as u
};
