import { computed, ref, onMounted, unref, withCtx, createVNode, createBlock, createCommentVNode, toDisplayString, openBlock, useSSRContext } from "vue";
import { ssrRenderComponent, ssrInterpolate, ssrRenderAttr, ssrRenderClass } from "vue/server-renderer";
import { usePage, Head, Link } from "@inertiajs/vue3";
import { useI18n } from "vue-i18n";
import { _ as _sfc_main$1 } from "./DefaultLayout-6zxGxPtK.js";
import { _ as _sfc_main$2, a as _sfc_main$8, P as Progress } from "./Progress-CyfOKcqP.js";
import { _ as _sfc_main$3, a as _sfc_main$7 } from "./RightSidebar-OMLtHB67.js";
import { V as VideoPlayer, _ as _sfc_main$6, S as SectionBanners } from "./SectionBanners-Cdgmr0Bw.js";
import { _ as _sfc_main$5 } from "./VideoGrid-lyRFayP0.js";
import { _ as _sfc_main$4 } from "./CommentThread-BV0-9YmO.js";
import { L as LikeButtonEntity } from "./LikeButtonEntity-ZC4HMEAO.js";
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
import "./EntityStats-0c7h3PEr.js";
const LEFT_SIDEBAR_KEY = "public_left_sidebar_collapsed";
const RIGHT_SIDEBAR_KEY = "public_right_sidebar_collapsed";
const _sfc_main = {
  __name: "Show",
  __ssrInlineRender: true,
  props: {
    title: { type: String, default: "" },
    canLogin: { type: Boolean, default: false },
    canRegister: { type: Boolean, default: false },
    video: {
      type: Object,
      default: () => ({})
    },
    recommendedVideos: {
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
    const videoData = computed(
      () => props.video ?? {}
    );
    const videoTranslation = computed(
      () => {
        var _a;
        return ((_a = videoData.value) == null ? void 0 : _a.translation) ?? {};
      }
    );
    const videoTitle = computed(
      () => {
        var _a;
        return ((_a = videoTranslation.value) == null ? void 0 : _a.title) || "";
      }
    );
    const videoShort = computed(
      () => {
        var _a;
        return ((_a = videoTranslation.value) == null ? void 0 : _a.short) || "";
      }
    );
    const videoDescription = computed(
      () => {
        var _a;
        return ((_a = videoTranslation.value) == null ? void 0 : _a.description) || videoShort.value || "";
      }
    );
    const videoPseudonym = computed(
      () => {
        var _a;
        return ((_a = videoTranslation.value) == null ? void 0 : _a.pseudonym) || "";
      }
    );
    const videoLocale = computed(
      () => {
        var _a;
        return ((_a = videoTranslation.value) == null ? void 0 : _a.locale) || props.locale || "ru";
      }
    );
    const videoAuthor = computed(
      () => {
        var _a, _b;
        return videoPseudonym.value || ((_b = (_a = videoData.value) == null ? void 0 : _a.owner) == null ? void 0 : _b.name) || "";
      }
    );
    const rubricTree = computed(
      () => Array.isArray(props.rubricTree) ? props.rubricTree : []
    );
    const recommendedVideosList = computed(
      () => normalizeList(
        props.recommendedVideos
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
    const videoImages = computed(
      () => {
        var _a;
        return normalizeList(
          (_a = videoData.value) == null ? void 0 : _a.images
        );
      }
    );
    const firstImage = computed(
      () => videoImages.value[0] ?? null
    );
    const firstImageUrl = computed(
      () => {
        var _a, _b, _c, _d;
        return ((_a = firstImage.value) == null ? void 0 : _a.webp_url) || ((_b = firstImage.value) == null ? void 0 : _b.image_url) || ((_c = firstImage.value) == null ? void 0 : _c.thumb_url) || ((_d = firstImage.value) == null ? void 0 : _d.url) || "";
      }
    );
    const contentUrl = computed(
      () => {
        var _a;
        return ((_a = videoData.value) == null ? void 0 : _a.video_url) || "";
      }
    );
    const embedUrl = computed(
      () => {
        var _a;
        return ((_a = videoData.value) == null ? void 0 : _a.embed_url) || "";
      }
    );
    const schemaDuration = computed(() => {
      var _a;
      const seconds = Number(
        ((_a = videoData.value) == null ? void 0 : _a.duration) ?? 0
      );
      if (!Number.isFinite(seconds) || seconds <= 0) {
        return "";
      }
      const hours = Math.floor(
        seconds / 3600
      );
      const minutes = Math.floor(
        seconds % 3600 / 60
      );
      const remainingSeconds = Math.floor(seconds % 60);
      let value = "PT";
      if (hours > 0) {
        value += `${hours}H`;
      }
      if (minutes > 0) {
        value += `${minutes}M`;
      }
      if (remainingSeconds > 0 || hours === 0 && minutes === 0) {
        value += `${remainingSeconds}S`;
      }
      return value;
    });
    const seoTitle = computed(
      () => {
        var _a;
        return ((_a = videoTranslation.value) == null ? void 0 : _a.meta_title) || videoTitle.value;
      }
    );
    const seoKeywords = computed(
      () => {
        var _a;
        return ((_a = videoTranslation.value) == null ? void 0 : _a.meta_keywords) || "";
      }
    );
    const seoDescription = computed(
      () => {
        var _a;
        return ((_a = videoTranslation.value) == null ? void 0 : _a.meta_desc) || videoShort.value || "";
      }
    );
    const canonicalUrl = computed(() => {
      var _a;
      if (!((_a = videoData.value) == null ? void 0 : _a.url)) {
        return "";
      }
      return String(
        route("public.blogVideos.show", {
          url: videoData.value.url
        })
      );
    });
    const ogLocale = computed(
      () => videoLocale.value === "ru" ? "ru_RU" : videoLocale.value
    );
    const publishedAt = computed(
      () => {
        var _a, _b;
        return ((_a = videoData.value) == null ? void 0 : _a.published_at) || ((_b = videoData.value) == null ? void 0 : _b.created_at) || "";
      }
    );
    const dcSubject = computed(
      () => seoKeywords.value || videoTitle.value
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
    const videoGridCols = computed(() => {
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
            if (seoKeywords.value) {
              _push2(`<meta name="keywords"${ssrRenderAttr("content", seoKeywords.value)}${_scopeId}>`);
            } else {
              _push2(`<!---->`);
            }
            if (videoAuthor.value) {
              _push2(`<meta name="author"${ssrRenderAttr("content", videoAuthor.value)}${_scopeId}>`);
            } else {
              _push2(`<!---->`);
            }
            _push2(`<meta name="robots" content="index, follow, max-image-preview:large"${_scopeId}>`);
            if (canonicalUrl.value) {
              _push2(`<link rel="canonical"${ssrRenderAttr("href", canonicalUrl.value)}${_scopeId}>`);
            } else {
              _push2(`<!---->`);
            }
            _push2(`<meta property="og:type" content="video.other"${_scopeId}><meta property="og:title"${ssrRenderAttr("content", seoTitle.value)}${_scopeId}>`);
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
            if (contentUrl.value) {
              _push2(`<meta property="og:video"${ssrRenderAttr("content", contentUrl.value)}${_scopeId}>`);
            } else if (embedUrl.value) {
              _push2(`<meta property="og:video"${ssrRenderAttr("content", embedUrl.value)}${_scopeId}>`);
            } else {
              _push2(`<!---->`);
            }
            _push2(`<meta name="twitter:card"${ssrRenderAttr(
              "content",
              firstImageUrl.value ? "summary_large_image" : "summary"
            )}${_scopeId}><meta name="twitter:title"${ssrRenderAttr("content", seoTitle.value)}${_scopeId}>`);
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
            if (videoAuthor.value) {
              _push2(`<meta name="DC.creator"${ssrRenderAttr("content", videoAuthor.value)}${_scopeId}>`);
            } else {
              _push2(`<!---->`);
            }
            _push2(`<meta name="DC.type" content="MovingImage"${_scopeId}><meta name="DC.format" content="text/html"${_scopeId}><meta name="DC.language"${ssrRenderAttr("content", videoLocale.value)}${_scopeId}>`);
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
              videoAuthor.value ? (openBlock(), createBlock("meta", {
                key: 2,
                name: "author",
                content: videoAuthor.value
              }, null, 8, ["content"])) : createCommentVNode("", true),
              createVNode("meta", {
                name: "robots",
                content: "index, follow, max-image-preview:large"
              }),
              canonicalUrl.value ? (openBlock(), createBlock("link", {
                key: 3,
                rel: "canonical",
                href: canonicalUrl.value
              }, null, 8, ["href"])) : createCommentVNode("", true),
              createVNode("meta", {
                property: "og:type",
                content: "video.other"
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
              createVNode("meta", {
                property: "og:locale",
                content: ogLocale.value
              }, null, 8, ["content"]),
              firstImageUrl.value ? (openBlock(), createBlock("meta", {
                key: 6,
                property: "og:image",
                content: firstImageUrl.value
              }, null, 8, ["content"])) : createCommentVNode("", true),
              contentUrl.value ? (openBlock(), createBlock("meta", {
                key: 7,
                property: "og:video",
                content: contentUrl.value
              }, null, 8, ["content"])) : embedUrl.value ? (openBlock(), createBlock("meta", {
                key: 8,
                property: "og:video",
                content: embedUrl.value
              }, null, 8, ["content"])) : createCommentVNode("", true),
              createVNode("meta", {
                name: "twitter:card",
                content: firstImageUrl.value ? "summary_large_image" : "summary"
              }, null, 8, ["content"]),
              createVNode("meta", {
                name: "twitter:title",
                content: seoTitle.value
              }, null, 8, ["content"]),
              seoDescription.value ? (openBlock(), createBlock("meta", {
                key: 9,
                name: "twitter:description",
                content: seoDescription.value
              }, null, 8, ["content"])) : createCommentVNode("", true),
              firstImageUrl.value ? (openBlock(), createBlock("meta", {
                key: 10,
                name: "twitter:image",
                content: firstImageUrl.value
              }, null, 8, ["content"])) : createCommentVNode("", true),
              createVNode("meta", {
                name: "DC.title",
                content: seoTitle.value
              }, null, 8, ["content"]),
              seoDescription.value ? (openBlock(), createBlock("meta", {
                key: 11,
                name: "DC.description",
                content: seoDescription.value
              }, null, 8, ["content"])) : createCommentVNode("", true),
              dcSubject.value ? (openBlock(), createBlock("meta", {
                key: 12,
                name: "DC.subject",
                content: dcSubject.value
              }, null, 8, ["content"])) : createCommentVNode("", true),
              videoAuthor.value ? (openBlock(), createBlock("meta", {
                key: 13,
                name: "DC.creator",
                content: videoAuthor.value
              }, null, 8, ["content"])) : createCommentVNode("", true),
              createVNode("meta", {
                name: "DC.type",
                content: "MovingImage"
              }),
              createVNode("meta", {
                name: "DC.format",
                content: "text/html"
              }),
              createVNode("meta", {
                name: "DC.language",
                content: videoLocale.value
              }, null, 8, ["content"]),
              canonicalUrl.value ? (openBlock(), createBlock("meta", {
                key: 14,
                name: "DC.identifier",
                content: canonicalUrl.value
              }, null, 8, ["content"])) : createCommentVNode("", true),
              publishedAt.value ? (openBlock(), createBlock("meta", {
                key: 15,
                name: "DCTERMS.issued",
                content: publishedAt.value
              }, null, 8, ["content"])) : createCommentVNode("", true)
            ];
          }
        }),
        _: 1
      }, _parent));
      _push(ssrRenderComponent(_sfc_main$1, {
        title: __props.title,
        "can-login": __props.canLogin,
        "can-register": __props.canRegister
      }, {
        default: withCtx((_, _push2, _parent2, _scopeId) => {
          var _a, _b;
          if (_push2) {
            _push2(ssrRenderComponent(_sfc_main$2, null, null, _parent2, _scopeId));
            _push2(`<main class="min-h-screen px-1 lg:px-6 max-w-full"${_scopeId}><div class="mx-auto tracking-wider pt-20 lg:pt-44"${_scopeId}><div class="ext-color w-full min-w-0 py-3 px-1 flex flex-col lg:flex-row gap-4 rounded-3xl border-2 border-slate-300 dark:border-slate-500"${_scopeId}>`);
            if (showLeft.value) {
              _push2(`<aside class="${ssrRenderClass([leftCollapsed.value ? "lg:w-6" : "lg:w-72", "shrink-0 transition-all duration-300 overflow-hidden"])}"${_scopeId}>`);
              _push2(ssrRenderComponent(_sfc_main$3, {
                "rubric-tree": rubricTree.value,
                collapsed: leftCollapsed.value,
                onCollapsed: setLeftCollapsed
              }, null, _parent2, _scopeId));
              _push2(`</aside>`);
            } else {
              _push2(`<!---->`);
            }
            _push2(`<section class="w-full pb-6 slate-1 min-w-0"${_scopeId}><div class="mx-auto max-w-6xl"${_scopeId}><article itemscope itemtype="https://schema.org/VideoObject"${ssrRenderAttr("itemid", canonicalUrl.value)} class="selection:bg-red-400 selection:text-white"${_scopeId}><meta itemprop="name"${ssrRenderAttr("content", videoTitle.value)}${_scopeId}>`);
            if (seoDescription.value) {
              _push2(`<meta itemprop="description"${ssrRenderAttr("content", seoDescription.value)}${_scopeId}>`);
            } else {
              _push2(`<!---->`);
            }
            _push2(`<meta itemprop="inLanguage"${ssrRenderAttr("content", videoLocale.value)}${_scopeId}>`);
            if (canonicalUrl.value) {
              _push2(`<meta itemprop="mainEntityOfPage"${ssrRenderAttr("content", canonicalUrl.value)}${_scopeId}>`);
            } else {
              _push2(`<!---->`);
            }
            if (firstImageUrl.value) {
              _push2(`<meta itemprop="thumbnailUrl"${ssrRenderAttr("content", firstImageUrl.value)}${_scopeId}>`);
            } else {
              _push2(`<!---->`);
            }
            if (publishedAt.value) {
              _push2(`<meta itemprop="uploadDate"${ssrRenderAttr("content", publishedAt.value)}${_scopeId}>`);
            } else {
              _push2(`<!---->`);
            }
            if (schemaDuration.value) {
              _push2(`<meta itemprop="duration"${ssrRenderAttr("content", schemaDuration.value)}${_scopeId}>`);
            } else {
              _push2(`<!---->`);
            }
            if (contentUrl.value) {
              _push2(`<meta itemprop="contentUrl"${ssrRenderAttr("content", contentUrl.value)}${_scopeId}>`);
            } else {
              _push2(`<!---->`);
            }
            if (embedUrl.value) {
              _push2(`<meta itemprop="embedUrl"${ssrRenderAttr("content", embedUrl.value)}${_scopeId}>`);
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
              href: _ctx.route("public.blogVideos.index"),
              class: "breadcrumb-link hover:underline"
            }, {
              default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                if (_push3) {
                  _push3(`<span itemprop="name"${_scopeId2}>${ssrInterpolate(unref(t)("videos"))}</span>`);
                } else {
                  return [
                    createVNode("span", { itemprop: "name" }, toDisplayString(unref(t)("videos")), 1)
                  ];
                }
              }),
              _: 1
            }, _parent2, _scopeId));
            _push2(`<meta itemprop="position" content="2"${_scopeId}></li><li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem" class="flex items-center" aria-current="page"${_scopeId}><span class="mx-2 breadcrumbs"${_scopeId}> / </span><span itemprop="name" class="breadcrumbs"${_scopeId}>${ssrInterpolate(videoTitle.value)}</span>`);
            if (canonicalUrl.value) {
              _push2(`<meta itemprop="item"${ssrRenderAttr("content", canonicalUrl.value)}${_scopeId}>`);
            } else {
              _push2(`<!---->`);
            }
            _push2(`<meta itemprop="position" content="3"${_scopeId}></li></ol></nav><div class="overflow-hidden rounded-sm mt-4 shadow-md shadow-gray-400 dark:shadow-gray-800"${_scopeId}>`);
            _push2(ssrRenderComponent(VideoPlayer, { video: videoData.value }, null, _parent2, _scopeId));
            _push2(`</div><div class="my-3 flex flex-wrap items-center justify-center gap-3 title"${_scopeId}><h1 class="text-2xl font-bold"${_scopeId}>${ssrInterpolate(videoTitle.value)}</h1><div${ssrRenderAttr("title", unref(t)("views"))} class="flex items-center justify-center gap-1" itemprop="interactionStatistic" itemscope itemtype="https://schema.org/InteractionCounter"${_scopeId}><svg class="h-4 w-4 text-slate-600/85 dark:text-slate-200/85" viewBox="0 0 576 512" fill="currentColor"${_scopeId}><path d="M569.354 231.631C512.97 135.949 407.81 72 288 72 168.14 72 63.004 135.994 6.646 231.631a47.999 47.999 0 0 0 0 48.739C63.031 376.051 168.19 440 288 440c119.86 0 224.996-63.994 281.354-159.631a47.997 47.997 0 0 0 0-48.738zM288 392c-102.556 0-192.091-54.701-240-136 44.157-74.933 123.677-127.27 216.162-135.007C273.958 131.078 280 144.83 280 160c0 30.928-25.072 56-56 56s-56-25.072-56-56l.001-.042C157.794 179.043 152 200.844 152 224c0 75.111 60.889 136 136 136s136-60.889 136-136c0-31.031-10.4-59.629-27.895-82.515C451.704 164.638 498.009 205.106 528 256c-47.908 81.299-137.444 136-240 136z"${_scopeId}></path></svg><meta itemprop="interactionType" content="https://schema.org/WatchAction"${_scopeId}><meta itemprop="userInteractionCount"${ssrRenderAttr("content", videoData.value.views || 0)}${_scopeId}><span class="text-sm text-gray-500"${_scopeId}>${ssrInterpolate(videoData.value.views || 0)}</span></div></div>`);
            if (videoDescription.value) {
              _push2(`<div class="my-4 text-sm subtitle text-center"${_scopeId}>${videoDescription.value ?? ""}</div>`);
            } else {
              _push2(`<!---->`);
            }
            _push2(`<div class="flex items-center justify-center gap-3"${_scopeId}>`);
            _push2(ssrRenderComponent(LikeButtonEntity, {
              "likes-count": videoData.value.likes_count || 0,
              "already-liked": videoData.value.already_liked || false,
              "route-name": "public.blogVideos.like",
              "route-params": { id: videoData.value.id },
              title: unref(t)("like"),
              "icon-class": "w-4 h-4"
            }, null, _parent2, _scopeId));
            _push2(`</div>`);
            if (videoData.value.owner) {
              _push2(`<div itemprop="author" itemscope itemtype="https://schema.org/Person" class="mt-4 flex items-center justify-center gap-2"${_scopeId}><meta itemprop="name"${ssrRenderAttr("content", videoAuthor.value)}${_scopeId}>`);
              if ((_a = videoData.value.owner) == null ? void 0 : _a.profile_photo_url) {
                _push2(`<img${ssrRenderAttr("src", videoData.value.owner.profile_photo_url)}${ssrRenderAttr("alt", videoAuthor.value)} loading="lazy" class="h-8 w-8 rounded-full object-cover ring-1 ring-gray-200 dark:ring-gray-700"${_scopeId}>`);
              } else {
                _push2(`<!---->`);
              }
              _push2(`<div class="min-w-0 text-sm font-semibold text-slate-700/85 dark:text-slate-300/85"${_scopeId}>${ssrInterpolate(videoAuthor.value)}</div></div>`);
            } else {
              _push2(`<!---->`);
            }
            _push2(ssrRenderComponent(_sfc_main$4, {
              "commentable-type": "App\\Models\\Admin\\Blog\\BlogVideo\\BlogVideo",
              "commentable-id": videoData.value.id,
              "auth-user": authUser.value
            }, null, _parent2, _scopeId));
            if (recommendedVideosList.value.length) {
              _push2(`<div class="mt-8"${_scopeId}><h2 class="mb-4 text-center text-lg font-semibold text-gray-700 dark:text-gray-300"${_scopeId}>${ssrInterpolate(unref(t)("relatedVideos"))}</h2>`);
              _push2(ssrRenderComponent(_sfc_main$5, {
                videos: recommendedVideosList.value,
                cols: videoGridCols.value
              }, null, _parent2, _scopeId));
              _push2(`</div>`);
            } else {
              _push2(`<!---->`);
            }
            _push2(`</article>`);
            _push2(ssrRenderComponent(_sfc_main$6, { videos: mainVideosList.value }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(SectionBanners, { banners: mainBannersList.value }, null, _parent2, _scopeId));
            _push2(`</div></section>`);
            if (showRight.value) {
              _push2(`<aside class="${ssrRenderClass([rightCollapsed.value ? "lg:w-6" : "lg:w-72", "shrink-0 transition-all duration-300 overflow-hidden"])}"${_scopeId}>`);
              _push2(ssrRenderComponent(_sfc_main$7, {
                collapsed: rightCollapsed.value,
                onCollapsed: setRightCollapsed
              }, null, _parent2, _scopeId));
              _push2(`</aside>`);
            } else {
              _push2(`<!---->`);
            }
            _push2(`</div></div></main>`);
            _push2(ssrRenderComponent(_sfc_main$8, null, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(Progress, null, null, _parent2, _scopeId));
          } else {
            return [
              createVNode(_sfc_main$2),
              createVNode("main", { class: "min-h-screen px-1 lg:px-6 max-w-full" }, [
                createVNode("div", { class: "mx-auto tracking-wider pt-20 lg:pt-44" }, [
                  createVNode("div", { class: "ext-color w-full min-w-0 py-3 px-1 flex flex-col lg:flex-row gap-4 rounded-3xl border-2 border-slate-300 dark:border-slate-500" }, [
                    showLeft.value ? (openBlock(), createBlock("aside", {
                      key: 0,
                      class: ["shrink-0 transition-all duration-300 overflow-hidden", leftCollapsed.value ? "lg:w-6" : "lg:w-72"]
                    }, [
                      createVNode(_sfc_main$3, {
                        "rubric-tree": rubricTree.value,
                        collapsed: leftCollapsed.value,
                        onCollapsed: setLeftCollapsed
                      }, null, 8, ["rubric-tree", "collapsed"])
                    ], 2)) : createCommentVNode("", true),
                    createVNode("section", { class: "w-full pb-6 slate-1 min-w-0" }, [
                      createVNode("div", { class: "mx-auto max-w-6xl" }, [
                        createVNode("article", {
                          itemscope: "",
                          itemtype: "https://schema.org/VideoObject",
                          itemid: canonicalUrl.value,
                          class: "selection:bg-red-400 selection:text-white"
                        }, [
                          createVNode("meta", {
                            itemprop: "name",
                            content: videoTitle.value
                          }, null, 8, ["content"]),
                          seoDescription.value ? (openBlock(), createBlock("meta", {
                            key: 0,
                            itemprop: "description",
                            content: seoDescription.value
                          }, null, 8, ["content"])) : createCommentVNode("", true),
                          createVNode("meta", {
                            itemprop: "inLanguage",
                            content: videoLocale.value
                          }, null, 8, ["content"]),
                          canonicalUrl.value ? (openBlock(), createBlock("meta", {
                            key: 1,
                            itemprop: "mainEntityOfPage",
                            content: canonicalUrl.value
                          }, null, 8, ["content"])) : createCommentVNode("", true),
                          firstImageUrl.value ? (openBlock(), createBlock("meta", {
                            key: 2,
                            itemprop: "thumbnailUrl",
                            content: firstImageUrl.value
                          }, null, 8, ["content"])) : createCommentVNode("", true),
                          publishedAt.value ? (openBlock(), createBlock("meta", {
                            key: 3,
                            itemprop: "uploadDate",
                            content: publishedAt.value
                          }, null, 8, ["content"])) : createCommentVNode("", true),
                          schemaDuration.value ? (openBlock(), createBlock("meta", {
                            key: 4,
                            itemprop: "duration",
                            content: schemaDuration.value
                          }, null, 8, ["content"])) : createCommentVNode("", true),
                          contentUrl.value ? (openBlock(), createBlock("meta", {
                            key: 5,
                            itemprop: "contentUrl",
                            content: contentUrl.value
                          }, null, 8, ["content"])) : createCommentVNode("", true),
                          embedUrl.value ? (openBlock(), createBlock("meta", {
                            key: 6,
                            itemprop: "embedUrl",
                            content: embedUrl.value
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
                                  href: _ctx.route("public.blogVideos.index"),
                                  class: "breadcrumb-link hover:underline"
                                }, {
                                  default: withCtx(() => [
                                    createVNode("span", { itemprop: "name" }, toDisplayString(unref(t)("videos")), 1)
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
                                createVNode("span", {
                                  itemprop: "name",
                                  class: "breadcrumbs"
                                }, toDisplayString(videoTitle.value), 1),
                                canonicalUrl.value ? (openBlock(), createBlock("meta", {
                                  key: 0,
                                  itemprop: "item",
                                  content: canonicalUrl.value
                                }, null, 8, ["content"])) : createCommentVNode("", true),
                                createVNode("meta", {
                                  itemprop: "position",
                                  content: "3"
                                })
                              ])
                            ])
                          ]),
                          createVNode("div", { class: "overflow-hidden rounded-sm mt-4 shadow-md shadow-gray-400 dark:shadow-gray-800" }, [
                            createVNode(VideoPlayer, { video: videoData.value }, null, 8, ["video"])
                          ]),
                          createVNode("div", { class: "my-3 flex flex-wrap items-center justify-center gap-3 title" }, [
                            createVNode("h1", { class: "text-2xl font-bold" }, toDisplayString(videoTitle.value), 1),
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
                                content: "https://schema.org/WatchAction"
                              }),
                              createVNode("meta", {
                                itemprop: "userInteractionCount",
                                content: videoData.value.views || 0
                              }, null, 8, ["content"]),
                              createVNode("span", { class: "text-sm text-gray-500" }, toDisplayString(videoData.value.views || 0), 1)
                            ], 8, ["title"])
                          ]),
                          videoDescription.value ? (openBlock(), createBlock("div", {
                            key: 7,
                            class: "my-4 text-sm subtitle text-center",
                            innerHTML: videoDescription.value
                          }, null, 8, ["innerHTML"])) : createCommentVNode("", true),
                          createVNode("div", { class: "flex items-center justify-center gap-3" }, [
                            createVNode(LikeButtonEntity, {
                              "likes-count": videoData.value.likes_count || 0,
                              "already-liked": videoData.value.already_liked || false,
                              "route-name": "public.blogVideos.like",
                              "route-params": { id: videoData.value.id },
                              title: unref(t)("like"),
                              "icon-class": "w-4 h-4"
                            }, null, 8, ["likes-count", "already-liked", "route-params", "title"])
                          ]),
                          videoData.value.owner ? (openBlock(), createBlock("div", {
                            key: 8,
                            itemprop: "author",
                            itemscope: "",
                            itemtype: "https://schema.org/Person",
                            class: "mt-4 flex items-center justify-center gap-2"
                          }, [
                            createVNode("meta", {
                              itemprop: "name",
                              content: videoAuthor.value
                            }, null, 8, ["content"]),
                            ((_b = videoData.value.owner) == null ? void 0 : _b.profile_photo_url) ? (openBlock(), createBlock("img", {
                              key: 0,
                              src: videoData.value.owner.profile_photo_url,
                              alt: videoAuthor.value,
                              loading: "lazy",
                              class: "h-8 w-8 rounded-full object-cover ring-1 ring-gray-200 dark:ring-gray-700"
                            }, null, 8, ["src", "alt"])) : createCommentVNode("", true),
                            createVNode("div", { class: "min-w-0 text-sm font-semibold text-slate-700/85 dark:text-slate-300/85" }, toDisplayString(videoAuthor.value), 1)
                          ])) : createCommentVNode("", true),
                          createVNode(_sfc_main$4, {
                            "commentable-type": "App\\Models\\Admin\\Blog\\BlogVideo\\BlogVideo",
                            "commentable-id": videoData.value.id,
                            "auth-user": authUser.value
                          }, null, 8, ["commentable-id", "auth-user"]),
                          recommendedVideosList.value.length ? (openBlock(), createBlock("div", {
                            key: 9,
                            class: "mt-8"
                          }, [
                            createVNode("h2", { class: "mb-4 text-center text-lg font-semibold text-gray-700 dark:text-gray-300" }, toDisplayString(unref(t)("relatedVideos")), 1),
                            createVNode(_sfc_main$5, {
                              videos: recommendedVideosList.value,
                              cols: videoGridCols.value
                            }, null, 8, ["videos", "cols"])
                          ])) : createCommentVNode("", true)
                        ], 8, ["itemid"]),
                        createVNode(_sfc_main$6, { videos: mainVideosList.value }, null, 8, ["videos"]),
                        createVNode(SectionBanners, { banners: mainBannersList.value }, null, 8, ["banners"])
                      ])
                    ]),
                    showRight.value ? (openBlock(), createBlock("aside", {
                      key: 1,
                      class: ["shrink-0 transition-all duration-300 overflow-hidden", rightCollapsed.value ? "lg:w-6" : "lg:w-72"]
                    }, [
                      createVNode(_sfc_main$7, {
                        collapsed: rightCollapsed.value,
                        onCollapsed: setRightCollapsed
                      }, null, 8, ["collapsed"])
                    ], 2)) : createCommentVNode("", true)
                  ])
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
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Pages/Public/Default/Blog/BlogVideos/Show.vue");
  return _sfc_setup ? _sfc_setup(props, ctx) : void 0;
};
export {
  _sfc_main as default
};
