import { ref, onMounted, onUnmounted, mergeProps, unref, withCtx, createTextVNode, toDisplayString, useSSRContext, computed, createVNode, createBlock, createCommentVNode, openBlock, Fragment, renderList } from "vue";
import { ssrRenderAttrs, ssrInterpolate, ssrRenderList, ssrRenderAttr, ssrRenderClass, ssrRenderStyle, ssrRenderComponent } from "vue/server-renderer";
import { Link, usePage, Head } from "@inertiajs/vue3";
import { useI18n } from "vue-i18n";
import { _ as _sfc_main$2 } from "./DefaultLayout-6zxGxPtK.js";
import { _ as _sfc_main$3, a as _sfc_main$9, P as Progress } from "./Progress-CyfOKcqP.js";
import { _ as _sfc_main$4, a as _sfc_main$8 } from "./RightSidebar-OMLtHB67.js";
import { L as LikeButtonEntity } from "./LikeButtonEntity-ZC4HMEAO.js";
import { I as ImageGalleryMain } from "./ImageGalleryMain-mIfXDUWm.js";
import { _ as _sfc_main$6 } from "./CommentThread-BV0-9YmO.js";
import { _ as _sfc_main$7, S as SectionBanners } from "./SectionBanners-Cdgmr0Bw.js";
import { _ as _sfc_main$5 } from "./ArticleGrid-BwxxSomM.js";
import { _ as _export_sfc } from "./_plugin-vue_export-helper-1tPrXgE0.js";
import "axios";
import "@inertiajs/inertia";
import "./ThemeToggle-DA16u1ft.js";
import "@vueuse/core";
import "./LocaleSelectOption-BeLdazeX.js";
import "./Checkbox-CgE3PSwb.js";
import "./TextInput-CCxUFX3K.js";
import "./InputLabel-Ds0Eo91B.js";
import "./PrimaryButton-D7EZDGT_.js";
import "./UniversalImageSlider-Cu2Xndcn.js";
import "./EntityStats-0c7h3PEr.js";
const _sfc_main$1 = {
  __name: "RecommendedVideos",
  __ssrInlineRender: true,
  props: {
    videos: Array
  },
  setup(__props) {
    const props = __props;
    const { t } = useI18n();
    const currentSlides = ref({});
    const activeVideoIds = ref({});
    const intervalIds = ref({});
    const startSlideshow = (videoId, imagesLength) => {
      stopSlideshow(videoId);
      if (imagesLength > 1) {
        intervalIds.value[videoId] = setInterval(() => {
          currentSlides.value[videoId] = (currentSlides.value[videoId] + 1) % imagesLength;
        }, 4e3);
      }
    };
    const stopSlideshow = (videoId) => {
      if (intervalIds.value[videoId]) {
        clearInterval(intervalIds.value[videoId]);
        delete intervalIds.value[videoId];
      }
    };
    const getVideoUrl = (video) => {
      const source = video.source_type;
      const id = video.external_video_id;
      try {
        if (source === "youtube") {
          let videoId = null;
          const url = new URL(id);
          videoId = url.searchParams.get("v");
          if (!videoId && url.hostname === "youtu.be") {
            videoId = url.pathname.slice(1);
          }
          if (!videoId && url.pathname) {
            const match = url.pathname.match(/(?:\/shorts\/|\/watch\/|\/)([a-zA-Z0-9_-]{11})/);
            videoId = match ? match[1] : null;
          }
          return videoId ? `https://www.youtube.com/embed/${videoId}` : null;
        }
        if (source === "vimeo") {
          const match = id.match(/vimeo\.com\/(?:video\/)?(\d+)/);
          return match ? `https://player.vimeo.com/video/${match[1]}` : null;
        }
        if (source === "local") {
          return video.video_url || `/storage/${id}`;
        }
        if (source === "code") {
          return video.video_code || video.embed_code || null;
        }
      } catch (e) {
        console.error("❌ Video URL error:", e);
        return null;
      }
      return null;
    };
    onMounted(() => {
      props.videos.forEach((video) => {
        var _a;
        currentSlides.value[video.id] = 0;
        if (((_a = video.images) == null ? void 0 : _a.length) > 1) startSlideshow(video.id, video.images.length);
      });
    });
    onUnmounted(() => {
      props.videos.forEach((video) => stopSlideshow(video.id));
    });
    return (_ctx, _push, _parent, _attrs) => {
      if (__props.videos && __props.videos.length > 0) {
        _push(`<div${ssrRenderAttrs(mergeProps({ class: "mt-4" }, _attrs))} data-v-4d6db4b9><h2 class="mb-4 tracking-wide text-center font-semibold text-lg text-gray-700 dark:text-gray-300" data-v-4d6db4b9>${ssrInterpolate(unref(t)("relatedVideos"))}</h2><div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-1 xl:grid-cols-2 gap-3" data-v-4d6db4b9><!--[-->`);
        ssrRenderList(__props.videos, (item) => {
          var _a;
          _push(`<div class="relative flex flex-col overflow-hidden bg-slate-100 dark:bg-[hsl(240_33%_12%)] rounded-sm border border-gray-400 dark:border-gray-500 shadow-md shadow-gray-400 dark:shadow-gray-800" data-v-4d6db4b9><div class="relative w-full bg-black aspect-video overflow-hidden" data-v-4d6db4b9>`);
          if (((_a = item.images) == null ? void 0 : _a.length) && !activeVideoIds.value[item.id]) {
            _push(`<div class="relative w-full h-full" data-v-4d6db4b9><!--[-->`);
            ssrRenderList(item.images, (img, index) => {
              _push(`<img${ssrRenderAttr("src", img.url)}${ssrRenderAttr("alt", img.alt || item.title)} class="${ssrRenderClass([{ "slide-fade-active": index === currentSlides.value[item.id] }, "slide-fade w-full h-full object-cover"])}" loading="lazy" data-v-4d6db4b9>`);
            });
            _push(`<!--]--><div class="absolute inset-0 flex items-center justify-center z-20" data-v-4d6db4b9><button class="bg-white/30 hover:bg-white/40 backdrop-blur-md rounded-full p-2 border-8 border-white/30" data-v-4d6db4b9><svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8 text-red-600" viewBox="0 0 24 24" fill="currentColor" data-v-4d6db4b9><path d="M8 5v14l11-7z" data-v-4d6db4b9></path></svg></button></div></div>`);
          } else {
            _push(`<!--[-->`);
            if (item.source_type === "code") {
              _push(`<div class="w-full h-full" data-v-4d6db4b9>${getVideoUrl(item) ?? ""}</div>`);
            } else if (["youtube", "vimeo"].includes(item.source_type)) {
              _push(`<iframe class="w-full h-full"${ssrRenderAttr("src", getVideoUrl(item))} style="${ssrRenderStyle({ "width": "100% !important", "aspect-ratio": "16 / 9 !important", "display": "block !important" })}" frameborder="0" allow="autoplay; fullscreen; picture-in-picture" allowfullscreen loading="lazy" data-v-4d6db4b9></iframe>`);
            } else if (item.source_type === "local") {
              _push(`<video class="w-full h-full object-contain" controls data-v-4d6db4b9><source${ssrRenderAttr("src", getVideoUrl(item))} type="video/mp4" data-v-4d6db4b9> ${ssrInterpolate(unref(t)("videoNotSupported"))}</video>`);
            } else {
              _push(`<!---->`);
            }
            _push(`<!--]-->`);
          }
          _push(`</div><div class="text-center text-sm font-semibold text-slate-800 dark:text-slate-100 px-2 py-3" data-v-4d6db4b9>`);
          _push(ssrRenderComponent(unref(Link), {
            href: `/videos/${item.url}`,
            class: "block text-sm font-semibold text-slate-800 dark:text-slate-100 hover:text-blue-600 transition"
          }, {
            default: withCtx((_, _push2, _parent2, _scopeId) => {
              if (_push2) {
                _push2(`${ssrInterpolate(item.title)}`);
              } else {
                return [
                  createTextVNode(toDisplayString(item.title), 1)
                ];
              }
            }),
            _: 2
          }, _parent));
          _push(`</div></div>`);
        });
        _push(`<!--]--></div></div>`);
      } else {
        _push(`<!---->`);
      }
    };
  }
};
const _sfc_setup$1 = _sfc_main$1.setup;
_sfc_main$1.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Components/Public/Default/Blog/BlogVideo/RecommendedVideos.vue");
  return _sfc_setup$1 ? _sfc_setup$1(props, ctx) : void 0;
};
const RecommendedVideos = /* @__PURE__ */ _export_sfc(_sfc_main$1, [["__scopeId", "data-v-4d6db4b9"]]);
const LEFT_SIDEBAR_KEY = "public_left_sidebar_collapsed";
const RIGHT_SIDEBAR_KEY = "public_right_sidebar_collapsed";
const _sfc_main = {
  __name: "Show",
  __ssrInlineRender: true,
  props: {
    title: { type: String, default: "" },
    canLogin: { type: Boolean, default: false },
    canRegister: { type: Boolean, default: false },
    article: {
      type: Object,
      default: () => ({})
    },
    breadcrumbRubric: {
      type: Object,
      default: () => null
    },
    recommendedArticles: {
      type: [Array, Object],
      default: () => []
    },
    articleVideos: {
      type: [Array, Object],
      default: () => []
    },
    rubricTree: {
      type: Array,
      default: () => []
    },
    mainVideos: {
      type: [Array, Object],
      default: () => []
    },
    mainBanners: {
      type: [Array, Object],
      default: () => []
    },
    locale: {
      type: String,
      default: "ru"
    }
  },
  setup(__props) {
    const { t } = useI18n();
    const page = usePage();
    const props = __props;
    const normalizeList = (value) => {
      if (Array.isArray(value)) return value;
      if (Array.isArray(value == null ? void 0 : value.data)) return value.data;
      return [];
    };
    const settingEnabled = (value, defaultValue = true) => {
      if (value === void 0 || value === null || value === "") {
        return defaultValue;
      }
      if (typeof value === "boolean") {
        return value;
      }
      return String(value) === "true";
    };
    const articleData = computed(
      () => props.article ?? {}
    );
    const articleTranslation = computed(
      () => {
        var _a;
        return ((_a = articleData.value) == null ? void 0 : _a.translation) ?? {};
      }
    );
    const articleTitle = computed(
      () => {
        var _a;
        return ((_a = articleTranslation.value) == null ? void 0 : _a.title) || "";
      }
    );
    const articleShort = computed(
      () => {
        var _a;
        return ((_a = articleTranslation.value) == null ? void 0 : _a.short) || "";
      }
    );
    const articleDescription = computed(
      () => {
        var _a;
        return ((_a = articleTranslation.value) == null ? void 0 : _a.description) || articleShort.value || "";
      }
    );
    const articlePseudonym = computed(
      () => {
        var _a;
        return ((_a = articleTranslation.value) == null ? void 0 : _a.pseudonym) || "";
      }
    );
    const articleLocale = computed(
      () => {
        var _a;
        return ((_a = articleTranslation.value) == null ? void 0 : _a.locale) || props.locale || "ru";
      }
    );
    const articleAuthor = computed(
      () => {
        var _a, _b;
        return articlePseudonym.value || ((_b = (_a = articleData.value) == null ? void 0 : _a.owner) == null ? void 0 : _b.name) || "";
      }
    );
    const rubricTree = computed(
      () => Array.isArray(props.rubricTree) ? props.rubricTree : []
    );
    const breadcrumbRubricData = computed(
      () => props.breadcrumbRubric ?? null
    );
    const breadcrumbRubricTranslation = computed(
      () => {
        var _a;
        return ((_a = breadcrumbRubricData.value) == null ? void 0 : _a.translation) ?? {};
      }
    );
    const breadcrumbRubricTitle = computed(
      () => {
        var _a;
        return ((_a = breadcrumbRubricTranslation.value) == null ? void 0 : _a.title) || "";
      }
    );
    const hasBreadcrumbRubric = computed(
      () => {
        var _a, _b;
        return Boolean(
          ((_a = breadcrumbRubricData.value) == null ? void 0 : _a.id) && ((_b = breadcrumbRubricData.value) == null ? void 0 : _b.url)
        );
      }
    );
    const articleImages = computed(
      () => {
        var _a;
        return normalizeList(
          (_a = articleData.value) == null ? void 0 : _a.images
        );
      }
    );
    const hasArticleImages = computed(
      () => articleImages.value.length > 0
    );
    const activeTags = computed(
      () => {
        var _a;
        return normalizeList(
          (_a = articleData.value) == null ? void 0 : _a.tags
        );
      }
    );
    const recommendedArticlesList = computed(
      () => normalizeList(
        props.recommendedArticles
      )
    );
    const articleVideosList = computed(
      () => normalizeList(
        props.articleVideos
      )
    );
    const mainVideosList = computed(
      () => normalizeList(
        props.mainVideos
      )
    );
    const mainBannersList = computed(
      () => normalizeList(
        props.mainBanners
      )
    );
    const authUser = computed(
      () => {
        var _a, _b;
        return ((_b = (_a = page.props) == null ? void 0 : _a.auth) == null ? void 0 : _b.user) ?? null;
      }
    );
    const seoTitle = computed(
      () => {
        var _a;
        return ((_a = articleTranslation.value) == null ? void 0 : _a.meta_title) || articleTitle.value;
      }
    );
    const seoKeywords = computed(
      () => {
        var _a;
        return ((_a = articleTranslation.value) == null ? void 0 : _a.meta_keywords) || "";
      }
    );
    const seoDescription = computed(
      () => {
        var _a;
        return ((_a = articleTranslation.value) == null ? void 0 : _a.meta_desc) || articleShort.value || "";
      }
    );
    const canonicalUrl = computed(() => {
      var _a;
      if (!((_a = articleData.value) == null ? void 0 : _a.url)) {
        return "";
      }
      return String(
        route("public.blogArticles.show", {
          url: articleData.value.url
        })
      );
    });
    const ogLocale = computed(
      () => articleLocale.value === "ru" ? "ru_RU" : articleLocale.value
    );
    const firstImage = computed(
      () => articleImages.value[0] ?? null
    );
    const firstImageUrl = computed(
      () => {
        var _a, _b, _c, _d;
        return ((_a = firstImage.value) == null ? void 0 : _a.webp_url) || ((_b = firstImage.value) == null ? void 0 : _b.image_url) || ((_c = firstImage.value) == null ? void 0 : _c.thumb_url) || ((_d = firstImage.value) == null ? void 0 : _d.url) || "";
      }
    );
    const firstImageAlt = computed(
      () => {
        var _a;
        return ((_a = firstImage.value) == null ? void 0 : _a.alt) || articleTitle.value || "";
      }
    );
    const articleSection = computed(
      () => breadcrumbRubricTitle.value || ""
    );
    const articleTagNames = computed(
      () => activeTags.value.map(
        (tag) => {
          var _a;
          return ((_a = tag == null ? void 0 : tag.translation) == null ? void 0 : _a.name) || (tag == null ? void 0 : tag.name) || "";
        }
      ).filter(Boolean)
    );
    const articleTagsText = computed(
      () => articleTagNames.value.join(", ")
    );
    const publishedAt = computed(
      () => {
        var _a, _b;
        return ((_a = articleData.value) == null ? void 0 : _a.published_at) || ((_b = articleData.value) == null ? void 0 : _b.created_at) || "";
      }
    );
    const modifiedAt = computed(
      () => {
        var _a;
        return ((_a = articleData.value) == null ? void 0 : _a.updated_at) || "";
      }
    );
    const dcSubject = computed(
      () => articleTagsText.value || seoKeywords.value || articleTitle.value
    );
    const siteSettings = computed(
      () => {
        var _a;
        return ((_a = page.props) == null ? void 0 : _a.siteSettings) ?? {};
      }
    );
    const showLeft = computed(
      () => {
        var _a;
        return settingEnabled(
          (_a = siteSettings.value) == null ? void 0 : _a.ViewLeftColumn,
          true
        );
      }
    );
    const showRight = computed(
      () => {
        var _a;
        return settingEnabled(
          (_a = siteSettings.value) == null ? void 0 : _a.ViewRightColumn,
          true
        );
      }
    );
    const leftCollapsed = ref(true);
    const rightCollapsed = ref(true);
    const readStoredBoolean = (key, fallback = true) => {
      try {
        const value = localStorage.getItem(key);
        return value === null ? fallback : value === "true";
      } catch {
        return fallback;
      }
    };
    const writeStoredBoolean = (key, value) => {
      try {
        localStorage.setItem(
          key,
          String(Boolean(value))
        );
      } catch {
      }
    };
    onMounted(() => {
      leftCollapsed.value = readStoredBoolean(
        LEFT_SIDEBAR_KEY,
        true
      );
      rightCollapsed.value = readStoredBoolean(
        RIGHT_SIDEBAR_KEY,
        true
      );
    });
    const setLeftCollapsed = (value) => {
      leftCollapsed.value = Boolean(value);
      writeStoredBoolean(
        LEFT_SIDEBAR_KEY,
        leftCollapsed.value
      );
    };
    const setRightCollapsed = (value) => {
      rightCollapsed.value = Boolean(value);
      writeStoredBoolean(
        RIGHT_SIDEBAR_KEY,
        rightCollapsed.value
      );
    };
    const articleGridCols = computed(() => {
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
            if (articleTagsText.value || seoKeywords.value) {
              _push2(`<meta itemprop="keywords"${ssrRenderAttr("content", articleTagsText.value || seoKeywords.value)}${_scopeId}>`);
            } else {
              _push2(`<!---->`);
            }
            if (articleSection.value) {
              _push2(`<meta itemprop="articleSection"${ssrRenderAttr("content", articleSection.value)}${_scopeId}>`);
            } else {
              _push2(`<!---->`);
            }
            if (articleTagsText.value) {
              _push2(`<meta itemprop="keywords"${ssrRenderAttr("content", articleTagsText.value)}${_scopeId}>`);
            } else {
              _push2(`<!---->`);
            }
            if (articleAuthor.value) {
              _push2(`<meta name="author"${ssrRenderAttr("content", articleAuthor.value)}${_scopeId}>`);
            } else {
              _push2(`<!---->`);
            }
            _push2(`<meta name="robots" content="index, follow, max-image-preview:large"${_scopeId}>`);
            if (canonicalUrl.value) {
              _push2(`<link rel="canonical"${ssrRenderAttr("href", canonicalUrl.value)}${_scopeId}>`);
            } else {
              _push2(`<!---->`);
            }
            _push2(`<meta property="og:type" content="article"${_scopeId}><meta property="og:title"${ssrRenderAttr("content", seoTitle.value)}${_scopeId}>`);
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
            _push2(`<meta property="og:locale"${ssrRenderAttr("content", ogLocale.value)}${_scopeId}>`);
            if (firstImageUrl.value) {
              _push2(`<meta property="og:image"${ssrRenderAttr("content", firstImageUrl.value)}${_scopeId}>`);
            } else {
              _push2(`<!---->`);
            }
            if (firstImageUrl.value && firstImageAlt.value) {
              _push2(`<meta property="og:image:alt"${ssrRenderAttr("content", firstImageAlt.value)}${_scopeId}>`);
            } else {
              _push2(`<!---->`);
            }
            if (publishedAt.value) {
              _push2(`<meta property="article:published_time"${ssrRenderAttr("content", publishedAt.value)}${_scopeId}>`);
            } else {
              _push2(`<!---->`);
            }
            if (modifiedAt.value) {
              _push2(`<meta property="article:modified_time"${ssrRenderAttr("content", modifiedAt.value)}${_scopeId}>`);
            } else {
              _push2(`<!---->`);
            }
            if (articleAuthor.value) {
              _push2(`<meta property="article:author"${ssrRenderAttr("content", articleAuthor.value)}${_scopeId}>`);
            } else {
              _push2(`<!---->`);
            }
            if (articleSection.value) {
              _push2(`<meta property="article:section"${ssrRenderAttr("content", articleSection.value)}${_scopeId}>`);
            } else {
              _push2(`<!---->`);
            }
            _push2(`<!--[-->`);
            ssrRenderList(articleTagNames.value, (tagName) => {
              _push2(`<meta property="article:tag"${ssrRenderAttr("content", tagName)}${_scopeId}>`);
            });
            _push2(`<!--]--><meta name="twitter:card"${ssrRenderAttr(
              "content",
              firstImageUrl.value ? "summary_large_image" : "summary"
            )}${_scopeId}>`);
            if (firstImageUrl.value && firstImageAlt.value) {
              _push2(`<meta name="twitter:image:alt"${ssrRenderAttr("content", firstImageAlt.value)}${_scopeId}>`);
            } else {
              _push2(`<!---->`);
            }
            _push2(`<meta name="twitter:title"${ssrRenderAttr("content", seoTitle.value)}${_scopeId}>`);
            if (seoDescription.value) {
              _push2(`<meta name="twitter:description"${ssrRenderAttr("content", seoDescription.value)}${_scopeId}>`);
            } else {
              _push2(`<!---->`);
            }
            if (firstImageUrl.value) {
              _push2(`<meta name="twitter:image"${ssrRenderAttr("content", firstImageUrl.value)}${_scopeId}>`);
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
            if (articleAuthor.value) {
              _push2(`<meta name="DC.creator"${ssrRenderAttr("content", articleAuthor.value)}${_scopeId}>`);
            } else {
              _push2(`<!---->`);
            }
            _push2(`<meta name="DC.type" content="Text"${_scopeId}><meta name="DC.format" content="text/html"${_scopeId}><meta name="DC.language"${ssrRenderAttr("content", articleLocale.value)}${_scopeId}>`);
            if (canonicalUrl.value) {
              _push2(`<meta name="DC.identifier"${ssrRenderAttr("content", canonicalUrl.value)}${_scopeId}>`);
            } else {
              _push2(`<!---->`);
            }
            if (publishedAt.value) {
              _push2(`<meta name="DCTERMS.issued"${ssrRenderAttr("content", publishedAt.value)}${_scopeId}>`);
            } else {
              _push2(`<!---->`);
            }
            if (modifiedAt.value) {
              _push2(`<meta name="DCTERMS.modified"${ssrRenderAttr("content", modifiedAt.value)}${_scopeId}>`);
            } else {
              _push2(`<!---->`);
            }
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
              articleTagsText.value || seoKeywords.value ? (openBlock(), createBlock("meta", {
                key: 1,
                itemprop: "keywords",
                content: articleTagsText.value || seoKeywords.value
              }, null, 8, ["content"])) : createCommentVNode("", true),
              articleSection.value ? (openBlock(), createBlock("meta", {
                key: 2,
                itemprop: "articleSection",
                content: articleSection.value
              }, null, 8, ["content"])) : createCommentVNode("", true),
              articleTagsText.value ? (openBlock(), createBlock("meta", {
                key: 3,
                itemprop: "keywords",
                content: articleTagsText.value
              }, null, 8, ["content"])) : createCommentVNode("", true),
              articleAuthor.value ? (openBlock(), createBlock("meta", {
                key: 4,
                name: "author",
                content: articleAuthor.value
              }, null, 8, ["content"])) : createCommentVNode("", true),
              createVNode("meta", {
                name: "robots",
                content: "index, follow, max-image-preview:large"
              }),
              canonicalUrl.value ? (openBlock(), createBlock("link", {
                key: 5,
                rel: "canonical",
                href: canonicalUrl.value
              }, null, 8, ["href"])) : createCommentVNode("", true),
              createVNode("meta", {
                property: "og:type",
                content: "article"
              }),
              createVNode("meta", {
                property: "og:title",
                content: seoTitle.value
              }, null, 8, ["content"]),
              seoDescription.value ? (openBlock(), createBlock("meta", {
                key: 6,
                property: "og:description",
                content: seoDescription.value
              }, null, 8, ["content"])) : createCommentVNode("", true),
              canonicalUrl.value ? (openBlock(), createBlock("meta", {
                key: 7,
                property: "og:url",
                content: canonicalUrl.value
              }, null, 8, ["content"])) : createCommentVNode("", true),
              createVNode("meta", {
                property: "og:locale",
                content: ogLocale.value
              }, null, 8, ["content"]),
              firstImageUrl.value ? (openBlock(), createBlock("meta", {
                key: 8,
                property: "og:image",
                content: firstImageUrl.value
              }, null, 8, ["content"])) : createCommentVNode("", true),
              firstImageUrl.value && firstImageAlt.value ? (openBlock(), createBlock("meta", {
                key: 9,
                property: "og:image:alt",
                content: firstImageAlt.value
              }, null, 8, ["content"])) : createCommentVNode("", true),
              publishedAt.value ? (openBlock(), createBlock("meta", {
                key: 10,
                property: "article:published_time",
                content: publishedAt.value
              }, null, 8, ["content"])) : createCommentVNode("", true),
              modifiedAt.value ? (openBlock(), createBlock("meta", {
                key: 11,
                property: "article:modified_time",
                content: modifiedAt.value
              }, null, 8, ["content"])) : createCommentVNode("", true),
              articleAuthor.value ? (openBlock(), createBlock("meta", {
                key: 12,
                property: "article:author",
                content: articleAuthor.value
              }, null, 8, ["content"])) : createCommentVNode("", true),
              articleSection.value ? (openBlock(), createBlock("meta", {
                key: 13,
                property: "article:section",
                content: articleSection.value
              }, null, 8, ["content"])) : createCommentVNode("", true),
              (openBlock(true), createBlock(Fragment, null, renderList(articleTagNames.value, (tagName) => {
                return openBlock(), createBlock("meta", {
                  key: `og-tag-${tagName}`,
                  property: "article:tag",
                  content: tagName
                }, null, 8, ["content"]);
              }), 128)),
              createVNode("meta", {
                name: "twitter:card",
                content: firstImageUrl.value ? "summary_large_image" : "summary"
              }, null, 8, ["content"]),
              firstImageUrl.value && firstImageAlt.value ? (openBlock(), createBlock("meta", {
                key: 14,
                name: "twitter:image:alt",
                content: firstImageAlt.value
              }, null, 8, ["content"])) : createCommentVNode("", true),
              createVNode("meta", {
                name: "twitter:title",
                content: seoTitle.value
              }, null, 8, ["content"]),
              seoDescription.value ? (openBlock(), createBlock("meta", {
                key: 15,
                name: "twitter:description",
                content: seoDescription.value
              }, null, 8, ["content"])) : createCommentVNode("", true),
              firstImageUrl.value ? (openBlock(), createBlock("meta", {
                key: 16,
                name: "twitter:image",
                content: firstImageUrl.value
              }, null, 8, ["content"])) : createCommentVNode("", true),
              createVNode("meta", {
                name: "DC.title",
                content: seoTitle.value
              }, null, 8, ["content"]),
              seoDescription.value ? (openBlock(), createBlock("meta", {
                key: 17,
                name: "DC.description",
                content: seoDescription.value
              }, null, 8, ["content"])) : createCommentVNode("", true),
              dcSubject.value ? (openBlock(), createBlock("meta", {
                key: 18,
                name: "DC.subject",
                content: dcSubject.value
              }, null, 8, ["content"])) : createCommentVNode("", true),
              articleAuthor.value ? (openBlock(), createBlock("meta", {
                key: 19,
                name: "DC.creator",
                content: articleAuthor.value
              }, null, 8, ["content"])) : createCommentVNode("", true),
              createVNode("meta", {
                name: "DC.type",
                content: "Text"
              }),
              createVNode("meta", {
                name: "DC.format",
                content: "text/html"
              }),
              createVNode("meta", {
                name: "DC.language",
                content: articleLocale.value
              }, null, 8, ["content"]),
              canonicalUrl.value ? (openBlock(), createBlock("meta", {
                key: 20,
                name: "DC.identifier",
                content: canonicalUrl.value
              }, null, 8, ["content"])) : createCommentVNode("", true),
              publishedAt.value ? (openBlock(), createBlock("meta", {
                key: 21,
                name: "DCTERMS.issued",
                content: publishedAt.value
              }, null, 8, ["content"])) : createCommentVNode("", true),
              modifiedAt.value ? (openBlock(), createBlock("meta", {
                key: 22,
                name: "DCTERMS.modified",
                content: modifiedAt.value
              }, null, 8, ["content"])) : createCommentVNode("", true)
            ];
          }
        }),
        _: 1
      }, _parent));
      _push(ssrRenderComponent(_sfc_main$2, {
        title: __props.title,
        "can-login": __props.canLogin,
        "can-register": __props.canRegister
      }, {
        default: withCtx((_, _push2, _parent2, _scopeId) => {
          var _a, _b;
          if (_push2) {
            _push2(ssrRenderComponent(_sfc_main$3, null, null, _parent2, _scopeId));
            _push2(`<main class="min-h-screen px-1 lg:px-6 max-w-full"${_scopeId}><div class="mx-auto tracking-wider pt-20 lg:pt-44"${_scopeId}><div class="ext-color w-full min-w-0 py-3 px-1 flex flex-col lg:flex-row gap-4 rounded-3xl border-2 border-slate-300 dark:border-slate-500"${_scopeId}>`);
            if (showLeft.value) {
              _push2(`<aside class="${ssrRenderClass([leftCollapsed.value ? "lg:w-6" : "lg:w-72", "shrink-0 transition-all duration-300 overflow-hidden"])}"${_scopeId}>`);
              _push2(ssrRenderComponent(_sfc_main$4, {
                "rubric-tree": rubricTree.value,
                collapsed: leftCollapsed.value,
                onCollapsed: setLeftCollapsed
              }, null, _parent2, _scopeId));
              _push2(`</aside>`);
            } else {
              _push2(`<!---->`);
            }
            _push2(`<section class="w-full pb-6 slate-1 min-w-0"${_scopeId}><div class="mx-auto max-w-6xl"${_scopeId}><article itemscope itemtype="https://schema.org/BlogPosting"${ssrRenderAttr("itemid", canonicalUrl.value)} class="selection:bg-red-400 selection:text-white"${_scopeId}>`);
            if (canonicalUrl.value) {
              _push2(`<link itemprop="mainEntityOfPage"${ssrRenderAttr("href", canonicalUrl.value)}${_scopeId}>`);
            } else {
              _push2(`<!---->`);
            }
            _push2(`<meta itemprop="inLanguage"${ssrRenderAttr("content", articleLocale.value)}${_scopeId}>`);
            if (publishedAt.value) {
              _push2(`<meta itemprop="datePublished"${ssrRenderAttr("content", publishedAt.value)}${_scopeId}>`);
            } else {
              _push2(`<!---->`);
            }
            if (modifiedAt.value) {
              _push2(`<meta itemprop="dateModified"${ssrRenderAttr("content", modifiedAt.value)}${_scopeId}>`);
            } else {
              _push2(`<!---->`);
            }
            if (firstImageUrl.value) {
              _push2(`<meta itemprop="image"${ssrRenderAttr("content", firstImageUrl.value)}${_scopeId}>`);
            } else {
              _push2(`<!---->`);
            }
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
            _push2(`<nav class="text-sm mb-3" aria-label="Breadcrumb" itemscope itemtype="https://schema.org/BreadcrumbList"${_scopeId}><ol class="flex flex-wrap items-center font-semibold"${_scopeId}><li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem" class="flex items-center"${_scopeId}>`);
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
              href: _ctx.route("public.blogArticles.index"),
              class: "breadcrumb-link hover:underline"
            }, {
              default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                if (_push3) {
                  _push3(`<span itemprop="name"${_scopeId2}>${ssrInterpolate(unref(t)("articles"))}</span>`);
                } else {
                  return [
                    createVNode("span", { itemprop: "name" }, toDisplayString(unref(t)("articles")), 1)
                  ];
                }
              }),
              _: 1
            }, _parent2, _scopeId));
            _push2(`<meta itemprop="position" content="2"${_scopeId}></li>`);
            if (hasBreadcrumbRubric.value) {
              _push2(`<li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem" class="flex items-center"${_scopeId}><span class="mx-2 breadcrumbs"${_scopeId}> / </span>`);
              _push2(ssrRenderComponent(unref(Link), {
                itemprop: "item",
                href: _ctx.route("public.blogRubrics.show", {
                  url: breadcrumbRubricData.value.url
                }),
                class: "breadcrumb-link hover:underline"
              }, {
                default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                  if (_push3) {
                    _push3(`<span itemprop="name"${_scopeId2}>${ssrInterpolate(breadcrumbRubricTitle.value)}</span>`);
                  } else {
                    return [
                      createVNode("span", { itemprop: "name" }, toDisplayString(breadcrumbRubricTitle.value), 1)
                    ];
                  }
                }),
                _: 1
              }, _parent2, _scopeId));
              _push2(`<meta itemprop="position" content="3"${_scopeId}></li>`);
            } else {
              _push2(`<!---->`);
            }
            _push2(`<li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem" class="flex items-center" aria-current="page"${_scopeId}><span class="mx-2 breadcrumbs"${_scopeId}> / </span><span itemprop="name" class="breadcrumbs"${_scopeId}>${ssrInterpolate(articleTitle.value)}</span>`);
            if (canonicalUrl.value) {
              _push2(`<meta itemprop="item"${ssrRenderAttr("content", canonicalUrl.value)}${_scopeId}>`);
            } else {
              _push2(`<!---->`);
            }
            _push2(`<meta itemprop="position"${ssrRenderAttr("content", hasBreadcrumbRubric.value ? "4" : "3")}${_scopeId}></li></ol></nav>`);
            if (hasArticleImages.value) {
              _push2(`<div class="flex items-center justify-center"${_scopeId}><div class="w-full"${_scopeId}>`);
              _push2(ssrRenderComponent(ImageGalleryMain, {
                images: articleImages.value,
                alt: articleTitle.value,
                "rounded-class": "rounded-lg",
                "shadow-class": "shadow-lg shadow-gray-400 dark:shadow-gray-700",
                "img-class": "w-full h-full object-cover"
              }, null, _parent2, _scopeId));
              _push2(`</div></div>`);
            } else {
              _push2(`<!---->`);
            }
            _push2(`<div class="my-3 flex flex-wrap items-center justify-center gap-3 title"${_scopeId}><h1 itemprop="headline" class="text-2xl font-bold"${_scopeId}>${ssrInterpolate(articleTitle.value)}</h1><div${ssrRenderAttr("title", unref(t)("views"))} class="flex items-center justify-center gap-1" itemprop="interactionStatistic" itemscope itemtype="https://schema.org/InteractionCounter"${_scopeId}><svg class="h-4 w-4 text-slate-600/85 dark:text-slate-200/85" viewBox="0 0 576 512" fill="currentColor"${_scopeId}><path d="M569.354 231.631C512.97 135.949 407.81 72 288 72 168.14 72 63.004 135.994 6.646 231.631a47.999 47.999 0 0 0 0 48.739C63.031 376.051 168.19 440 288 440c119.86 0 224.996-63.994 281.354-159.631a47.997 47.997 0 0 0 0-48.738zM288 392c-102.556 0-192.091-54.701-240-136 44.157-74.933 123.677-127.27 216.162-135.007C273.958 131.078 280 144.83 280 160c0 30.928-25.072 56-56 56s-56-25.072-56-56l.001-.042C157.794 179.043 152 200.844 152 224c0 75.111 60.889 136 136 136s136-60.889 136-136c0-31.031-10.4-59.629-27.895-82.515C451.704 164.638 498.009 205.106 528 256c-47.908 81.299-137.444 136-240 136z"${_scopeId}></path></svg><meta itemprop="interactionType" content="https://schema.org/ViewAction"${_scopeId}><meta itemprop="userInteractionCount"${ssrRenderAttr("content", articleData.value.views || 0)}${_scopeId}><span class="text-sm text-gray-500"${_scopeId}>${ssrInterpolate(articleData.value.views || 0)}</span></div></div>`);
            if (articleDescription.value) {
              _push2(`<div itemprop="articleBody" class="my-3 text-sm subtitle"${_scopeId}>${articleDescription.value ?? ""}</div>`);
            } else {
              _push2(`<!---->`);
            }
            _push2(`<div class="flex flex-wrap items-center justify-center gap-3"${_scopeId}>`);
            if (activeTags.value.length) {
              _push2(`<div class="flex flex-wrap items-center justify-center gap-1 font-semibold italic"${_scopeId}><!--[-->`);
              ssrRenderList(activeTags.value, (tag, index) => {
                _push2(`<!--[-->`);
                _push2(ssrRenderComponent(unref(Link), {
                  href: _ctx.route("public.blogTags.show", {
                    slug: tag.slug
                  }),
                  class: "text-sm text-blue-500 dark:text-violet-300 hover:text-rose-400 dark:hover:text-rose-300"
                }, {
                  default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                    var _a2, _b2;
                    if (_push3) {
                      _push3(`${ssrInterpolate(((_a2 = tag.translation) == null ? void 0 : _a2.name) || tag.name)}`);
                    } else {
                      return [
                        createTextVNode(toDisplayString(((_b2 = tag.translation) == null ? void 0 : _b2.name) || tag.name), 1)
                      ];
                    }
                  }),
                  _: 2
                }, _parent2, _scopeId));
                if (index < activeTags.value.length - 1) {
                  _push2(`<span class="text-slate-500 dark:text-slate-400"${_scopeId}> , </span>`);
                } else {
                  _push2(`<!---->`);
                }
                _push2(`<!--]-->`);
              });
              _push2(`<!--]--></div>`);
            } else {
              _push2(`<!---->`);
            }
            _push2(ssrRenderComponent(LikeButtonEntity, {
              "likes-count": articleData.value.likes_count || 0,
              "already-liked": articleData.value.already_liked || false,
              "route-name": "public.blogArticles.like",
              "route-params": { id: articleData.value.id },
              title: unref(t)("like"),
              "icon-class": "w-4 h-4"
            }, null, _parent2, _scopeId));
            _push2(`</div>`);
            if (articleAuthor.value) {
              _push2(`<div itemprop="author" itemscope itemtype="https://schema.org/Person" class="mt-4 flex items-center justify-center gap-2"${_scopeId}><meta itemprop="name"${ssrRenderAttr("content", articleAuthor.value)}${_scopeId}>`);
              if ((_a = articleData.value.owner) == null ? void 0 : _a.profile_photo_url) {
                _push2(`<img${ssrRenderAttr("src", articleData.value.owner.profile_photo_url)}${ssrRenderAttr("alt", articleAuthor.value)} loading="lazy" class="h-8 w-8 rounded-full object-cover ring-1 ring-gray-200 dark:ring-gray-700"${_scopeId}>`);
              } else {
                _push2(`<!---->`);
              }
              _push2(`<div class="min-w-0 text-sm font-semibold text-slate-700/85 dark:text-slate-300/85"${_scopeId}>${ssrInterpolate(articleAuthor.value)}</div></div>`);
            } else {
              _push2(`<!---->`);
            }
            if (recommendedArticlesList.value.length) {
              _push2(`<div class="mt-8"${_scopeId}><h2 class="mb-4 text-center text-lg font-semibold text-gray-700 dark:text-gray-300"${_scopeId}>${ssrInterpolate(unref(t)("relatedArticles"))}</h2>`);
              _push2(ssrRenderComponent(_sfc_main$5, {
                articles: recommendedArticlesList.value,
                cols: articleGridCols.value
              }, null, _parent2, _scopeId));
              _push2(`</div>`);
            } else {
              _push2(`<!---->`);
            }
            if (articleVideosList.value.length) {
              _push2(`<div class="mt-8"${_scopeId}>`);
              _push2(ssrRenderComponent(RecommendedVideos, { videos: articleVideosList.value }, null, _parent2, _scopeId));
              _push2(`</div>`);
            } else {
              _push2(`<!---->`);
            }
            _push2(ssrRenderComponent(_sfc_main$6, {
              "commentable-type": "App\\Models\\Admin\\Blog\\BlogArticle\\BlogArticle",
              "commentable-id": articleData.value.id,
              "auth-user": authUser.value
            }, null, _parent2, _scopeId));
            _push2(`</article>`);
            _push2(ssrRenderComponent(_sfc_main$7, { videos: mainVideosList.value }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(SectionBanners, { banners: mainBannersList.value }, null, _parent2, _scopeId));
            _push2(`</div></section>`);
            if (showRight.value) {
              _push2(`<aside class="${ssrRenderClass([rightCollapsed.value ? "lg:w-6" : "lg:w-72", "shrink-0 transition-all duration-300 overflow-hidden"])}"${_scopeId}>`);
              _push2(ssrRenderComponent(_sfc_main$8, {
                collapsed: rightCollapsed.value,
                onCollapsed: setRightCollapsed
              }, null, _parent2, _scopeId));
              _push2(`</aside>`);
            } else {
              _push2(`<!---->`);
            }
            _push2(`</div></div></main>`);
            _push2(ssrRenderComponent(_sfc_main$9, null, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(Progress, null, null, _parent2, _scopeId));
          } else {
            return [
              createVNode(_sfc_main$3),
              createVNode("main", { class: "min-h-screen px-1 lg:px-6 max-w-full" }, [
                createVNode("div", { class: "mx-auto tracking-wider pt-20 lg:pt-44" }, [
                  createVNode("div", { class: "ext-color w-full min-w-0 py-3 px-1 flex flex-col lg:flex-row gap-4 rounded-3xl border-2 border-slate-300 dark:border-slate-500" }, [
                    showLeft.value ? (openBlock(), createBlock("aside", {
                      key: 0,
                      class: ["shrink-0 transition-all duration-300 overflow-hidden", leftCollapsed.value ? "lg:w-6" : "lg:w-72"]
                    }, [
                      createVNode(_sfc_main$4, {
                        "rubric-tree": rubricTree.value,
                        collapsed: leftCollapsed.value,
                        onCollapsed: setLeftCollapsed
                      }, null, 8, ["rubric-tree", "collapsed"])
                    ], 2)) : createCommentVNode("", true),
                    createVNode("section", { class: "w-full pb-6 slate-1 min-w-0" }, [
                      createVNode("div", { class: "mx-auto max-w-6xl" }, [
                        createVNode("article", {
                          itemscope: "",
                          itemtype: "https://schema.org/BlogPosting",
                          itemid: canonicalUrl.value,
                          class: "selection:bg-red-400 selection:text-white"
                        }, [
                          canonicalUrl.value ? (openBlock(), createBlock("link", {
                            key: 0,
                            itemprop: "mainEntityOfPage",
                            href: canonicalUrl.value
                          }, null, 8, ["href"])) : createCommentVNode("", true),
                          createVNode("meta", {
                            itemprop: "inLanguage",
                            content: articleLocale.value
                          }, null, 8, ["content"]),
                          publishedAt.value ? (openBlock(), createBlock("meta", {
                            key: 1,
                            itemprop: "datePublished",
                            content: publishedAt.value
                          }, null, 8, ["content"])) : createCommentVNode("", true),
                          modifiedAt.value ? (openBlock(), createBlock("meta", {
                            key: 2,
                            itemprop: "dateModified",
                            content: modifiedAt.value
                          }, null, 8, ["content"])) : createCommentVNode("", true),
                          firstImageUrl.value ? (openBlock(), createBlock("meta", {
                            key: 3,
                            itemprop: "image",
                            content: firstImageUrl.value
                          }, null, 8, ["content"])) : createCommentVNode("", true),
                          seoDescription.value ? (openBlock(), createBlock("meta", {
                            key: 4,
                            itemprop: "description",
                            content: seoDescription.value
                          }, null, 8, ["content"])) : createCommentVNode("", true),
                          seoKeywords.value ? (openBlock(), createBlock("meta", {
                            key: 5,
                            itemprop: "keywords",
                            content: seoKeywords.value
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
                                  href: _ctx.route("public.blogArticles.index"),
                                  class: "breadcrumb-link hover:underline"
                                }, {
                                  default: withCtx(() => [
                                    createVNode("span", { itemprop: "name" }, toDisplayString(unref(t)("articles")), 1)
                                  ]),
                                  _: 1
                                }, 8, ["href"]),
                                createVNode("meta", {
                                  itemprop: "position",
                                  content: "2"
                                })
                              ]),
                              hasBreadcrumbRubric.value ? (openBlock(), createBlock("li", {
                                key: 0,
                                itemprop: "itemListElement",
                                itemscope: "",
                                itemtype: "https://schema.org/ListItem",
                                class: "flex items-center"
                              }, [
                                createVNode("span", { class: "mx-2 breadcrumbs" }, " / "),
                                createVNode(unref(Link), {
                                  itemprop: "item",
                                  href: _ctx.route("public.blogRubrics.show", {
                                    url: breadcrumbRubricData.value.url
                                  }),
                                  class: "breadcrumb-link hover:underline"
                                }, {
                                  default: withCtx(() => [
                                    createVNode("span", { itemprop: "name" }, toDisplayString(breadcrumbRubricTitle.value), 1)
                                  ]),
                                  _: 1
                                }, 8, ["href"]),
                                createVNode("meta", {
                                  itemprop: "position",
                                  content: "3"
                                })
                              ])) : createCommentVNode("", true),
                              createVNode("li", {
                                itemprop: "itemListElement",
                                itemscope: "",
                                itemtype: "https://schema.org/ListItem",
                                class: "flex items-center",
                                "aria-current": "page"
                              }, [
                                createVNode("span", { class: "mx-2 breadcrumbs" }, " / "),
                                createVNode("span", {
                                  itemprop: "name",
                                  class: "breadcrumbs"
                                }, toDisplayString(articleTitle.value), 1),
                                canonicalUrl.value ? (openBlock(), createBlock("meta", {
                                  key: 0,
                                  itemprop: "item",
                                  content: canonicalUrl.value
                                }, null, 8, ["content"])) : createCommentVNode("", true),
                                createVNode("meta", {
                                  itemprop: "position",
                                  content: hasBreadcrumbRubric.value ? "4" : "3"
                                }, null, 8, ["content"])
                              ])
                            ])
                          ]),
                          hasArticleImages.value ? (openBlock(), createBlock("div", {
                            key: 6,
                            class: "flex items-center justify-center"
                          }, [
                            createVNode("div", { class: "w-full" }, [
                              createVNode(ImageGalleryMain, {
                                images: articleImages.value,
                                alt: articleTitle.value,
                                "rounded-class": "rounded-lg",
                                "shadow-class": "shadow-lg shadow-gray-400 dark:shadow-gray-700",
                                "img-class": "w-full h-full object-cover"
                              }, null, 8, ["images", "alt"])
                            ])
                          ])) : createCommentVNode("", true),
                          createVNode("div", { class: "my-3 flex flex-wrap items-center justify-center gap-3 title" }, [
                            createVNode("h1", {
                              itemprop: "headline",
                              class: "text-2xl font-bold"
                            }, toDisplayString(articleTitle.value), 1),
                            createVNode("div", {
                              title: unref(t)("views"),
                              class: "flex items-center justify-center gap-1",
                              itemprop: "interactionStatistic",
                              itemscope: "",
                              itemtype: "https://schema.org/InteractionCounter"
                            }, [
                              (openBlock(), createBlock("svg", {
                                class: "h-4 w-4 text-slate-600/85 dark:text-slate-200/85",
                                viewBox: "0 0 576 512",
                                fill: "currentColor"
                              }, [
                                createVNode("path", { d: "M569.354 231.631C512.97 135.949 407.81 72 288 72 168.14 72 63.004 135.994 6.646 231.631a47.999 47.999 0 0 0 0 48.739C63.031 376.051 168.19 440 288 440c119.86 0 224.996-63.994 281.354-159.631a47.997 47.997 0 0 0 0-48.738zM288 392c-102.556 0-192.091-54.701-240-136 44.157-74.933 123.677-127.27 216.162-135.007C273.958 131.078 280 144.83 280 160c0 30.928-25.072 56-56 56s-56-25.072-56-56l.001-.042C157.794 179.043 152 200.844 152 224c0 75.111 60.889 136 136 136s136-60.889 136-136c0-31.031-10.4-59.629-27.895-82.515C451.704 164.638 498.009 205.106 528 256c-47.908 81.299-137.444 136-240 136z" })
                              ])),
                              createVNode("meta", {
                                itemprop: "interactionType",
                                content: "https://schema.org/ViewAction"
                              }),
                              createVNode("meta", {
                                itemprop: "userInteractionCount",
                                content: articleData.value.views || 0
                              }, null, 8, ["content"]),
                              createVNode("span", { class: "text-sm text-gray-500" }, toDisplayString(articleData.value.views || 0), 1)
                            ], 8, ["title"])
                          ]),
                          articleDescription.value ? (openBlock(), createBlock("div", {
                            key: 7,
                            itemprop: "articleBody",
                            class: "my-3 text-sm subtitle",
                            innerHTML: articleDescription.value
                          }, null, 8, ["innerHTML"])) : createCommentVNode("", true),
                          createVNode("div", { class: "flex flex-wrap items-center justify-center gap-3" }, [
                            activeTags.value.length ? (openBlock(), createBlock("div", {
                              key: 0,
                              class: "flex flex-wrap items-center justify-center gap-1 font-semibold italic"
                            }, [
                              (openBlock(true), createBlock(Fragment, null, renderList(activeTags.value, (tag, index) => {
                                return openBlock(), createBlock(Fragment, {
                                  key: tag.id
                                }, [
                                  createVNode(unref(Link), {
                                    href: _ctx.route("public.blogTags.show", {
                                      slug: tag.slug
                                    }),
                                    class: "text-sm text-blue-500 dark:text-violet-300 hover:text-rose-400 dark:hover:text-rose-300"
                                  }, {
                                    default: withCtx(() => {
                                      var _a2;
                                      return [
                                        createTextVNode(toDisplayString(((_a2 = tag.translation) == null ? void 0 : _a2.name) || tag.name), 1)
                                      ];
                                    }),
                                    _: 2
                                  }, 1032, ["href"]),
                                  index < activeTags.value.length - 1 ? (openBlock(), createBlock("span", {
                                    key: 0,
                                    class: "text-slate-500 dark:text-slate-400"
                                  }, " , ")) : createCommentVNode("", true)
                                ], 64);
                              }), 128))
                            ])) : createCommentVNode("", true),
                            createVNode(LikeButtonEntity, {
                              "likes-count": articleData.value.likes_count || 0,
                              "already-liked": articleData.value.already_liked || false,
                              "route-name": "public.blogArticles.like",
                              "route-params": { id: articleData.value.id },
                              title: unref(t)("like"),
                              "icon-class": "w-4 h-4"
                            }, null, 8, ["likes-count", "already-liked", "route-params", "title"])
                          ]),
                          articleAuthor.value ? (openBlock(), createBlock("div", {
                            key: 8,
                            itemprop: "author",
                            itemscope: "",
                            itemtype: "https://schema.org/Person",
                            class: "mt-4 flex items-center justify-center gap-2"
                          }, [
                            createVNode("meta", {
                              itemprop: "name",
                              content: articleAuthor.value
                            }, null, 8, ["content"]),
                            ((_b = articleData.value.owner) == null ? void 0 : _b.profile_photo_url) ? (openBlock(), createBlock("img", {
                              key: 0,
                              src: articleData.value.owner.profile_photo_url,
                              alt: articleAuthor.value,
                              loading: "lazy",
                              class: "h-8 w-8 rounded-full object-cover ring-1 ring-gray-200 dark:ring-gray-700"
                            }, null, 8, ["src", "alt"])) : createCommentVNode("", true),
                            createVNode("div", { class: "min-w-0 text-sm font-semibold text-slate-700/85 dark:text-slate-300/85" }, toDisplayString(articleAuthor.value), 1)
                          ])) : createCommentVNode("", true),
                          recommendedArticlesList.value.length ? (openBlock(), createBlock("div", {
                            key: 9,
                            class: "mt-8"
                          }, [
                            createVNode("h2", { class: "mb-4 text-center text-lg font-semibold text-gray-700 dark:text-gray-300" }, toDisplayString(unref(t)("relatedArticles")), 1),
                            createVNode(_sfc_main$5, {
                              articles: recommendedArticlesList.value,
                              cols: articleGridCols.value
                            }, null, 8, ["articles", "cols"])
                          ])) : createCommentVNode("", true),
                          articleVideosList.value.length ? (openBlock(), createBlock("div", {
                            key: 10,
                            class: "mt-8"
                          }, [
                            createVNode(RecommendedVideos, { videos: articleVideosList.value }, null, 8, ["videos"])
                          ])) : createCommentVNode("", true),
                          createVNode(_sfc_main$6, {
                            "commentable-type": "App\\Models\\Admin\\Blog\\BlogArticle\\BlogArticle",
                            "commentable-id": articleData.value.id,
                            "auth-user": authUser.value
                          }, null, 8, ["commentable-id", "auth-user"])
                        ], 8, ["itemid"]),
                        createVNode(_sfc_main$7, { videos: mainVideosList.value }, null, 8, ["videos"]),
                        createVNode(SectionBanners, { banners: mainBannersList.value }, null, 8, ["banners"])
                      ])
                    ]),
                    showRight.value ? (openBlock(), createBlock("aside", {
                      key: 1,
                      class: ["shrink-0 transition-all duration-300 overflow-hidden", rightCollapsed.value ? "lg:w-6" : "lg:w-72"]
                    }, [
                      createVNode(_sfc_main$8, {
                        collapsed: rightCollapsed.value,
                        onCollapsed: setRightCollapsed
                      }, null, 8, ["collapsed"])
                    ], 2)) : createCommentVNode("", true)
                  ])
                ])
              ]),
              createVNode(_sfc_main$9),
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
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Pages/Public/Default/Blog/BlogArticles/Show.vue");
  return _sfc_setup ? _sfc_setup(props, ctx) : void 0;
};
export {
  _sfc_main as default
};
