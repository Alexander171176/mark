import { computed, ref, unref, withCtx, createVNode, resolveDynamicComponent, createBlock, createCommentVNode, toDisplayString, openBlock, createTextVNode, Fragment, renderList, useSSRContext } from "vue";
import { ssrRenderComponent, ssrInterpolate, ssrRenderAttr, ssrRenderVNode, ssrRenderClass, ssrRenderList } from "vue/server-renderer";
import { usePage, Head, Link } from "@inertiajs/vue3";
import { useI18n } from "vue-i18n";
import { _ as _sfc_main$1 } from "./DefaultLayout-6zxGxPtK.js";
import { _ as _sfc_main$2, a as _sfc_main$6, P as Progress } from "./Progress-CyfOKcqP.js";
import { _ as _sfc_main$3, a as _sfc_main$5 } from "./RightSidebarSchool-DlAegojf.js";
import { L as LikeButtonEntity } from "./LikeButtonEntity-ZC4HMEAO.js";
import { I as ImageGalleryMain } from "./ImageGalleryMain-mIfXDUWm.js";
import { _ as _sfc_main$4, S as SectionBanners } from "./SectionBanners-Cdgmr0Bw.js";
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
const _sfc_main = {
  __name: "Show",
  __ssrInlineRender: true,
  props: {
    title: String,
    canLogin: Boolean,
    canRegister: Boolean,
    lesson: {
      type: Object,
      default: () => ({})
    },
    trackTree: {
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
    const absoluteUrl = (value) => {
      if (!value) return "";
      if (/^https?:\/\//i.test(value)) {
        return value;
      }
      if (typeof window === "undefined") {
        return value;
      }
      try {
        return new URL(value, window.location.origin).href;
      } catch {
        return value;
      }
    };
    const stripHtml = (value) => {
      if (!value) return "";
      return String(value).replace(/<[^>]*>/g, " ").replace(/\s+/g, " ").trim();
    };
    const lessonData = computed(() => props.lesson ?? {});
    const translation = computed(
      () => {
        var _a;
        return ((_a = lessonData.value) == null ? void 0 : _a.translation) ?? {};
      }
    );
    const lessonTitle = computed(
      () => {
        var _a;
        return ((_a = translation.value) == null ? void 0 : _a.title) ?? "";
      }
    );
    computed(
      () => {
        var _a;
        return ((_a = translation.value) == null ? void 0 : _a.subtitle) ?? "";
      }
    );
    const lessonShort = computed(
      () => {
        var _a;
        return ((_a = translation.value) == null ? void 0 : _a.short) ?? "";
      }
    );
    const lessonDescription = computed(
      () => {
        var _a;
        return ((_a = translation.value) == null ? void 0 : _a.description) ?? "";
      }
    );
    const moduleData = computed(
      () => {
        var _a;
        return ((_a = lessonData.value) == null ? void 0 : _a.module) ?? null;
      }
    );
    const courseData = computed(
      () => {
        var _a;
        return ((_a = moduleData.value) == null ? void 0 : _a.course) ?? null;
      }
    );
    const hashtags = computed(
      () => {
        var _a;
        return normalizeList((_a = lessonData.value) == null ? void 0 : _a.hashtags);
      }
    );
    const contentData = computed(
      () => {
        var _a;
        return ((_a = lessonData.value) == null ? void 0 : _a.content) ?? null;
      }
    );
    const lessonImages = computed(
      () => {
        var _a;
        return normalizeList((_a = lessonData.value) == null ? void 0 : _a.images);
      }
    );
    const firstLessonImage = computed(
      () => lessonImages.value[0] ?? null
    );
    const hasLessonImages = computed(
      () => lessonImages.value.length > 0
    );
    const firstLessonImageUrl = computed(
      () => {
        var _a, _b, _c, _d;
        return absoluteUrl(
          ((_a = firstLessonImage.value) == null ? void 0 : _a.webp_url) || ((_b = firstLessonImage.value) == null ? void 0 : _b.image_url) || ((_c = firstLessonImage.value) == null ? void 0 : _c.url) || ((_d = firstLessonImage.value) == null ? void 0 : _d.thumb_url) || ""
        );
      }
    );
    const seoTitle = computed(
      () => {
        var _a;
        return ((_a = translation.value) == null ? void 0 : _a.meta_title) || lessonTitle.value || t("lessons");
      }
    );
    const seoDescription = computed(
      () => {
        var _a;
        return ((_a = translation.value) == null ? void 0 : _a.meta_desc) || lessonShort.value || stripHtml(lessonDescription.value) || "";
      }
    );
    const seoKeywords = computed(
      () => {
        var _a;
        return ((_a = translation.value) == null ? void 0 : _a.meta_keywords) ?? "";
      }
    );
    const contentLocale = computed(
      () => {
        var _a, _b;
        return ((_a = translation.value) == null ? void 0 : _a.locale) || ((_b = page.props) == null ? void 0 : _b.locale) || "";
      }
    );
    const lessonRouteUrl = computed(() => {
      var _a;
      if (!((_a = lessonData.value) == null ? void 0 : _a.slug)) return "";
      return route("public.schoolLessons.show", {
        slug: lessonData.value.slug
      });
    });
    const canonicalUrl = computed(
      () => absoluteUrl(lessonRouteUrl.value)
    );
    const lessonsIndexUrl = computed(
      () => absoluteUrl(
        route("public.schoolLessons.index")
      )
    );
    const homeUrl = computed(
      () => absoluteUrl(
        route("home")
      )
    );
    const courseUrl = computed(() => {
      var _a;
      if (!((_a = courseData.value) == null ? void 0 : _a.slug)) return "";
      return absoluteUrl(
        route("public.schoolCourses.show", {
          slug: courseData.value.slug
        })
      );
    });
    const moduleUrl = computed(() => {
      var _a, _b;
      if (!((_a = courseData.value) == null ? void 0 : _a.slug) || !((_b = moduleData.value) == null ? void 0 : _b.slug)) {
        return "";
      }
      return absoluteUrl(
        route("public.schoolModules.show", {
          courseSlug: courseData.value.slug,
          slug: moduleData.value.slug
        })
      );
    });
    const schemaDuration = computed(() => {
      var _a;
      const minutes = Number((_a = lessonData.value) == null ? void 0 : _a.duration);
      if (!Number.isFinite(minutes) || minutes <= 0) {
        return null;
      }
      return `PT${Math.round(minutes)}M`;
    });
    const lessonSchema = computed(() => {
      var _a, _b, _c, _d, _e, _f, _g;
      const schema = {
        "@context": "https://schema.org",
        "@type": "LearningResource",
        name: lessonTitle.value || seoTitle.value,
        url: canonicalUrl.value || lessonRouteUrl.value,
        learningResourceType: "Lesson"
      };
      if (contentLocale.value) {
        schema.inLanguage = contentLocale.value;
      }
      const description = stripHtml(
        seoDescription.value || lessonShort.value || lessonDescription.value
      );
      if (description) {
        schema.description = description;
      }
      if (firstLessonImageUrl.value) {
        schema.image = firstLessonImageUrl.value;
      }
      if ((_a = lessonData.value) == null ? void 0 : _a.published_at) {
        schema.datePublished = lessonData.value.published_at;
      }
      if (((_b = lessonData.value) == null ? void 0 : _b.difficulty) !== null && ((_c = lessonData.value) == null ? void 0 : _c.difficulty) !== void 0) {
        schema.educationalLevel = String(lessonData.value.difficulty);
      }
      if (schemaDuration.value) {
        schema.timeRequired = schemaDuration.value;
      }
      if (seoKeywords.value) {
        schema.keywords = seoKeywords.value;
      }
      if ((_e = (_d = courseData.value) == null ? void 0 : _d.translation) == null ? void 0 : _e.title) {
        schema.isPartOf = {
          "@type": "Course",
          name: courseData.value.translation.title
        };
        if (courseUrl.value) {
          schema.isPartOf.url = courseUrl.value;
        }
      }
      const interactions = [];
      const views = Number((_f = lessonData.value) == null ? void 0 : _f.views);
      const likes = Number((_g = lessonData.value) == null ? void 0 : _g.likes_count);
      if (Number.isFinite(views) && views > 0) {
        interactions.push({
          "@type": "InteractionCounter",
          interactionType: {
            "@type": "ViewAction"
          },
          userInteractionCount: views
        });
      }
      if (Number.isFinite(likes) && likes > 0) {
        interactions.push({
          "@type": "InteractionCounter",
          interactionType: {
            "@type": "LikeAction"
          },
          userInteractionCount: likes
        });
      }
      if (interactions.length) {
        schema.interactionStatistic = interactions;
      }
      return schema;
    });
    const breadcrumbSchema = computed(() => {
      var _a, _b, _c, _d;
      const items = [];
      let position = 1;
      items.push({
        "@type": "ListItem",
        position: position++,
        name: t("home"),
        item: homeUrl.value
      });
      items.push({
        "@type": "ListItem",
        position: position++,
        name: t("lessons"),
        item: lessonsIndexUrl.value
      });
      if (courseUrl.value && ((_b = (_a = courseData.value) == null ? void 0 : _a.translation) == null ? void 0 : _b.title)) {
        items.push({
          "@type": "ListItem",
          position: position++,
          name: courseData.value.translation.title,
          item: courseUrl.value
        });
      }
      if (moduleUrl.value && ((_d = (_c = moduleData.value) == null ? void 0 : _c.translation) == null ? void 0 : _d.title)) {
        items.push({
          "@type": "ListItem",
          position: position++,
          name: moduleData.value.translation.title,
          item: moduleUrl.value
        });
      }
      items.push({
        "@type": "ListItem",
        position,
        name: lessonTitle.value || seoTitle.value,
        item: canonicalUrl.value || lessonRouteUrl.value
      });
      return {
        "@context": "https://schema.org",
        "@type": "BreadcrumbList",
        itemListElement: items
      };
    });
    const lessonSchemaJson = computed(
      () => JSON.stringify(lessonSchema.value)
    );
    const breadcrumbSchemaJson = computed(
      () => JSON.stringify(breadcrumbSchema.value)
    );
    const trackTree = computed(
      () => Array.isArray(props.trackTree) ? props.trackTree : []
    );
    const { siteSettings } = page.props;
    const showLeft = computed(
      () => !(siteSettings == null ? void 0 : siteSettings.ViewLeftColumn) || siteSettings.ViewLeftColumn === "true"
    );
    const showRight = computed(
      () => !(siteSettings == null ? void 0 : siteSettings.ViewRightColumn) || siteSettings.ViewRightColumn === "true"
    );
    const leftCollapsed = ref(false);
    const rightCollapsed = ref(false);
    const mainVideosList = computed(
      () => normalizeList(props.mainVideos)
    );
    const mainBannersList = computed(
      () => normalizeList(props.mainBanners)
    );
    const translateAccessType = (value) => {
      const normalized = String(value ?? "").trim().toLowerCase();
      const map = {
        free: "free",
        paid: "paid",
        preview: "preview"
      };
      return map[normalized] ? t(map[normalized]) : value;
    };
    return (_ctx, _push, _parent, _attrs) => {
      _push(`<!--[-->`);
      _push(ssrRenderComponent(unref(Head), null, {
        default: withCtx((_, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(`<title${_scopeId}>${ssrInterpolate(seoTitle.value)}</title><meta name="title"${ssrRenderAttr("content", seoTitle.value)}${_scopeId}><meta name="description"${ssrRenderAttr("content", seoDescription.value)}${_scopeId}>`);
            if (seoKeywords.value) {
              _push2(`<meta name="keywords"${ssrRenderAttr("content", seoKeywords.value)}${_scopeId}>`);
            } else {
              _push2(`<!---->`);
            }
            _push2(`<meta name="viewport" content="width=device-width, initial-scale=1"${_scopeId}><meta name="robots" content="index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1"${_scopeId}>`);
            if (canonicalUrl.value) {
              _push2(`<link rel="canonical"${ssrRenderAttr("href", canonicalUrl.value)}${_scopeId}>`);
            } else {
              _push2(`<!---->`);
            }
            _push2(`<meta property="og:title"${ssrRenderAttr("content", seoTitle.value)}${_scopeId}><meta property="og:description"${ssrRenderAttr("content", seoDescription.value)}${_scopeId}><meta property="og:type" content="article"${_scopeId}>`);
            if (canonicalUrl.value) {
              _push2(`<meta property="og:url"${ssrRenderAttr("content", canonicalUrl.value)}${_scopeId}>`);
            } else {
              _push2(`<!---->`);
            }
            if (firstLessonImageUrl.value) {
              _push2(`<meta property="og:image"${ssrRenderAttr("content", firstLessonImageUrl.value)}${_scopeId}>`);
            } else {
              _push2(`<!---->`);
            }
            if (firstLessonImageUrl.value) {
              _push2(`<meta property="og:image:alt"${ssrRenderAttr("content", lessonTitle.value)}${_scopeId}>`);
            } else {
              _push2(`<!---->`);
            }
            if (contentLocale.value) {
              _push2(`<meta property="og:locale"${ssrRenderAttr("content", contentLocale.value)}${_scopeId}>`);
            } else {
              _push2(`<!---->`);
            }
            if (__props.title) {
              _push2(`<meta property="og:site_name"${ssrRenderAttr("content", __props.title)}${_scopeId}>`);
            } else {
              _push2(`<!---->`);
            }
            if (lessonData.value.published_at) {
              _push2(`<meta property="article:published_time"${ssrRenderAttr("content", lessonData.value.published_at)}${_scopeId}>`);
            } else {
              _push2(`<!---->`);
            }
            _push2(`<meta name="twitter:card"${ssrRenderAttr("content", firstLessonImageUrl.value ? "summary_large_image" : "summary")}${_scopeId}><meta name="twitter:title"${ssrRenderAttr("content", seoTitle.value)}${_scopeId}><meta name="twitter:description"${ssrRenderAttr("content", seoDescription.value)}${_scopeId}>`);
            if (firstLessonImageUrl.value) {
              _push2(`<meta name="twitter:image"${ssrRenderAttr("content", firstLessonImageUrl.value)}${_scopeId}>`);
            } else {
              _push2(`<!---->`);
            }
            if (firstLessonImageUrl.value) {
              _push2(`<meta name="twitter:image:alt"${ssrRenderAttr("content", lessonTitle.value)}${_scopeId}>`);
            } else {
              _push2(`<!---->`);
            }
            _push2(`<meta name="DC.Title"${ssrRenderAttr("content", seoTitle.value)}${_scopeId}><meta name="DC.Description"${ssrRenderAttr("content", seoDescription.value)}${_scopeId}>`);
            if (seoKeywords.value) {
              _push2(`<meta name="DC.Subject"${ssrRenderAttr("content", seoKeywords.value)}${_scopeId}>`);
            } else {
              _push2(`<!---->`);
            }
            _push2(`<meta name="DC.Type" content="Text"${_scopeId}><meta name="DC.Format" content="text/html"${_scopeId}>`);
            if (contentLocale.value) {
              _push2(`<meta name="DC.Language"${ssrRenderAttr("content", contentLocale.value)}${_scopeId}>`);
            } else {
              _push2(`<!---->`);
            }
            if (canonicalUrl.value) {
              _push2(`<meta name="DC.Identifier"${ssrRenderAttr("content", canonicalUrl.value)}${_scopeId}>`);
            } else {
              _push2(`<!---->`);
            }
            if (lessonData.value.published_at) {
              _push2(`<meta name="DC.Date"${ssrRenderAttr("content", lessonData.value.published_at)}${_scopeId}>`);
            } else {
              _push2(`<!---->`);
            }
            ssrRenderVNode(_push2, createVNode(resolveDynamicComponent("script"), { type: "application/ld+json" }, null), _parent2, _scopeId);
            ssrRenderVNode(_push2, createVNode(resolveDynamicComponent("script"), { type: "application/ld+json" }, null), _parent2, _scopeId);
          } else {
            return [
              createVNode("title", null, toDisplayString(seoTitle.value), 1),
              createVNode("meta", {
                name: "title",
                content: seoTitle.value
              }, null, 8, ["content"]),
              createVNode("meta", {
                name: "description",
                content: seoDescription.value
              }, null, 8, ["content"]),
              seoKeywords.value ? (openBlock(), createBlock("meta", {
                key: 0,
                name: "keywords",
                content: seoKeywords.value
              }, null, 8, ["content"])) : createCommentVNode("", true),
              createVNode("meta", {
                name: "viewport",
                content: "width=device-width, initial-scale=1"
              }),
              createVNode("meta", {
                name: "robots",
                content: "index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1"
              }),
              canonicalUrl.value ? (openBlock(), createBlock("link", {
                key: 1,
                rel: "canonical",
                href: canonicalUrl.value
              }, null, 8, ["href"])) : createCommentVNode("", true),
              createVNode("meta", {
                property: "og:title",
                content: seoTitle.value
              }, null, 8, ["content"]),
              createVNode("meta", {
                property: "og:description",
                content: seoDescription.value
              }, null, 8, ["content"]),
              createVNode("meta", {
                property: "og:type",
                content: "article"
              }),
              canonicalUrl.value ? (openBlock(), createBlock("meta", {
                key: 2,
                property: "og:url",
                content: canonicalUrl.value
              }, null, 8, ["content"])) : createCommentVNode("", true),
              firstLessonImageUrl.value ? (openBlock(), createBlock("meta", {
                key: 3,
                property: "og:image",
                content: firstLessonImageUrl.value
              }, null, 8, ["content"])) : createCommentVNode("", true),
              firstLessonImageUrl.value ? (openBlock(), createBlock("meta", {
                key: 4,
                property: "og:image:alt",
                content: lessonTitle.value
              }, null, 8, ["content"])) : createCommentVNode("", true),
              contentLocale.value ? (openBlock(), createBlock("meta", {
                key: 5,
                property: "og:locale",
                content: contentLocale.value
              }, null, 8, ["content"])) : createCommentVNode("", true),
              __props.title ? (openBlock(), createBlock("meta", {
                key: 6,
                property: "og:site_name",
                content: __props.title
              }, null, 8, ["content"])) : createCommentVNode("", true),
              lessonData.value.published_at ? (openBlock(), createBlock("meta", {
                key: 7,
                property: "article:published_time",
                content: lessonData.value.published_at
              }, null, 8, ["content"])) : createCommentVNode("", true),
              createVNode("meta", {
                name: "twitter:card",
                content: firstLessonImageUrl.value ? "summary_large_image" : "summary"
              }, null, 8, ["content"]),
              createVNode("meta", {
                name: "twitter:title",
                content: seoTitle.value
              }, null, 8, ["content"]),
              createVNode("meta", {
                name: "twitter:description",
                content: seoDescription.value
              }, null, 8, ["content"]),
              firstLessonImageUrl.value ? (openBlock(), createBlock("meta", {
                key: 8,
                name: "twitter:image",
                content: firstLessonImageUrl.value
              }, null, 8, ["content"])) : createCommentVNode("", true),
              firstLessonImageUrl.value ? (openBlock(), createBlock("meta", {
                key: 9,
                name: "twitter:image:alt",
                content: lessonTitle.value
              }, null, 8, ["content"])) : createCommentVNode("", true),
              createVNode("meta", {
                name: "DC.Title",
                content: seoTitle.value
              }, null, 8, ["content"]),
              createVNode("meta", {
                name: "DC.Description",
                content: seoDescription.value
              }, null, 8, ["content"]),
              seoKeywords.value ? (openBlock(), createBlock("meta", {
                key: 10,
                name: "DC.Subject",
                content: seoKeywords.value
              }, null, 8, ["content"])) : createCommentVNode("", true),
              createVNode("meta", {
                name: "DC.Type",
                content: "Text"
              }),
              createVNode("meta", {
                name: "DC.Format",
                content: "text/html"
              }),
              contentLocale.value ? (openBlock(), createBlock("meta", {
                key: 11,
                name: "DC.Language",
                content: contentLocale.value
              }, null, 8, ["content"])) : createCommentVNode("", true),
              canonicalUrl.value ? (openBlock(), createBlock("meta", {
                key: 12,
                name: "DC.Identifier",
                content: canonicalUrl.value
              }, null, 8, ["content"])) : createCommentVNode("", true),
              lessonData.value.published_at ? (openBlock(), createBlock("meta", {
                key: 13,
                name: "DC.Date",
                content: lessonData.value.published_at
              }, null, 8, ["content"])) : createCommentVNode("", true),
              (openBlock(), createBlock(resolveDynamicComponent("script"), {
                type: "application/ld+json",
                textContent: toDisplayString(lessonSchemaJson.value)
              }, null, 8, ["textContent"])),
              (openBlock(), createBlock(resolveDynamicComponent("script"), {
                type: "application/ld+json",
                textContent: toDisplayString(breadcrumbSchemaJson.value)
              }, null, 8, ["textContent"]))
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
          var _a, _b, _c, _d, _e, _f, _g, _h, _i, _j, _k, _l;
          if (_push2) {
            _push2(ssrRenderComponent(_sfc_main$2, null, null, _parent2, _scopeId));
            _push2(`<main class="min-h-screen px-1 lg:px-6 max-w-full"${_scopeId}><div class="mx-auto tracking-wider pt-20 lg:pt-44"${_scopeId}><div class="ext-color w-full min-w-0 py-3 px-1 flex flex-col lg:flex-row gap-4 rounded-3xl border-2 border-slate-300 dark:border-slate-500"${_scopeId}>`);
            if (showLeft.value) {
              _push2(`<aside class="${ssrRenderClass([leftCollapsed.value ? "lg:w-6" : "lg:w-72", "shrink-0 pl-3 transition-all duration-300"])}"${_scopeId}>`);
              _push2(ssrRenderComponent(_sfc_main$3, {
                "track-tree": trackTree.value,
                onCollapsed: ($event) => leftCollapsed.value = $event
              }, null, _parent2, _scopeId));
              _push2(`</aside>`);
            } else {
              _push2(`<!---->`);
            }
            _push2(`<section class="w-full pb-6 slate-1 min-w-0"${_scopeId}><div class="mx-auto max-w-6xl"${_scopeId}><article class="selection:bg-red-400 selection:text-white" itemscope itemtype="https://schema.org/LearningResource"${_scopeId}>`);
            if (canonicalUrl.value) {
              _push2(`<meta itemprop="url"${ssrRenderAttr("content", canonicalUrl.value)}${_scopeId}>`);
            } else {
              _push2(`<!---->`);
            }
            if (contentLocale.value) {
              _push2(`<meta itemprop="inLanguage"${ssrRenderAttr("content", contentLocale.value)}${_scopeId}>`);
            } else {
              _push2(`<!---->`);
            }
            _push2(`<meta itemprop="learningResourceType" content="Lesson"${_scopeId}>`);
            if (lessonData.value.published_at) {
              _push2(`<meta itemprop="datePublished"${ssrRenderAttr("content", lessonData.value.published_at)}${_scopeId}>`);
            } else {
              _push2(`<!---->`);
            }
            if (schemaDuration.value) {
              _push2(`<meta itemprop="timeRequired"${ssrRenderAttr("content", schemaDuration.value)}${_scopeId}>`);
            } else {
              _push2(`<!---->`);
            }
            if (lessonData.value.difficulty !== null && lessonData.value.difficulty !== void 0) {
              _push2(`<meta itemprop="educationalLevel"${ssrRenderAttr("content", String(lessonData.value.difficulty))}${_scopeId}>`);
            } else {
              _push2(`<!---->`);
            }
            _push2(`<nav class="text-sm mb-3" aria-label="Breadcrumb"${_scopeId}><ol class="flex flex-wrap items-center font-semibold"${_scopeId}><li${_scopeId}>`);
            _push2(ssrRenderComponent(unref(Link), {
              href: _ctx.route("home"),
              class: "breadcrumb-link hover:underline"
            }, {
              default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                if (_push3) {
                  _push3(`${ssrInterpolate(unref(t)("home"))}`);
                } else {
                  return [
                    createTextVNode(toDisplayString(unref(t)("home")), 1)
                  ];
                }
              }),
              _: 1
            }, _parent2, _scopeId));
            _push2(`</li><li${_scopeId}><span class="mx-2 breadcrumbs"${_scopeId}>/</span></li><li${_scopeId}>`);
            _push2(ssrRenderComponent(unref(Link), {
              href: _ctx.route("public.schoolLessons.index"),
              class: "breadcrumb-link hover:underline"
            }, {
              default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                if (_push3) {
                  _push3(`${ssrInterpolate(unref(t)("lessons"))}`);
                } else {
                  return [
                    createTextVNode(toDisplayString(unref(t)("lessons")), 1)
                  ];
                }
              }),
              _: 1
            }, _parent2, _scopeId));
            _push2(`</li>`);
            if ((_a = courseData.value) == null ? void 0 : _a.slug) {
              _push2(`<!--[--><li${_scopeId}><span class="mx-2 breadcrumbs"${_scopeId}>/</span></li><li${_scopeId}>`);
              _push2(ssrRenderComponent(unref(Link), {
                href: _ctx.route("public.schoolCourses.show", {
                  slug: courseData.value.slug
                }),
                class: "breadcrumb-link hover:underline"
              }, {
                default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                  var _a2, _b2;
                  if (_push3) {
                    _push3(`${ssrInterpolate((_a2 = courseData.value.translation) == null ? void 0 : _a2.title)}`);
                  } else {
                    return [
                      createTextVNode(toDisplayString((_b2 = courseData.value.translation) == null ? void 0 : _b2.title), 1)
                    ];
                  }
                }),
                _: 1
              }, _parent2, _scopeId));
              _push2(`</li><!--]-->`);
            } else {
              _push2(`<!---->`);
            }
            if (((_b = courseData.value) == null ? void 0 : _b.slug) && ((_c = moduleData.value) == null ? void 0 : _c.slug)) {
              _push2(`<!--[--><li${_scopeId}><span class="mx-2 breadcrumbs"${_scopeId}>/</span></li><li${_scopeId}>`);
              _push2(ssrRenderComponent(unref(Link), {
                href: _ctx.route("public.schoolModules.show", {
                  courseSlug: courseData.value.slug,
                  slug: moduleData.value.slug
                }),
                class: "breadcrumb-link hover:underline"
              }, {
                default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                  var _a2, _b2;
                  if (_push3) {
                    _push3(`${ssrInterpolate((_a2 = moduleData.value.translation) == null ? void 0 : _a2.title)}`);
                  } else {
                    return [
                      createTextVNode(toDisplayString((_b2 = moduleData.value.translation) == null ? void 0 : _b2.title), 1)
                    ];
                  }
                }),
                _: 1
              }, _parent2, _scopeId));
              _push2(`</li><!--]-->`);
            } else {
              _push2(`<!---->`);
            }
            _push2(`<li${_scopeId}><span class="mx-2 breadcrumbs"${_scopeId}>/</span></li><li class="breadcrumbs" aria-current="page"${_scopeId}>${ssrInterpolate(translation.value.title)}</li></ol></nav><div class="flex flex-wrap items-center justify-center gap-3 title my-3"${_scopeId}><h1 class="text-2xl font-bold" itemprop="name"${_scopeId}>${ssrInterpolate(lessonTitle.value)}</h1>`);
            if (lessonData.value.views > 0) {
              _push2(`<div${ssrRenderAttr("title", unref(t)("views"))} class="flex items-center justify-center gap-1" itemprop="interactionStatistic" itemscope itemtype="https://schema.org/InteractionCounter"${_scopeId}><meta itemprop="interactionType" content="https://schema.org/ViewAction"${_scopeId}><meta itemprop="userInteractionCount"${ssrRenderAttr("content", lessonData.value.views)}${_scopeId}></div>`);
            } else {
              _push2(`<!---->`);
            }
            _push2(`</div>`);
            if (translation.value.subtitle) {
              _push2(`<div class="mt-1 mb-3 text-sm subtitle text-center"${_scopeId}>${ssrInterpolate(translation.value.subtitle)}</div>`);
            } else {
              _push2(`<!---->`);
            }
            if (hasLessonImages.value) {
              _push2(`<div class="flex items-center justify-center"${_scopeId}><div class="w-full"${_scopeId}>`);
              _push2(ssrRenderComponent(ImageGalleryMain, {
                images: lessonImages.value,
                alt: lessonTitle.value,
                itemprop: "image",
                loading: "eager",
                fetchpriority: "high",
                "rounded-class": "rounded-lg",
                "shadow-class": "shadow-lg shadow-gray-400 dark:shadow-gray-700",
                "img-class": "w-full h-full object-cover"
              }, null, _parent2, _scopeId));
              _push2(`</div></div>`);
            } else {
              _push2(`<!---->`);
            }
            _push2(`<div class="my-4 flex flex-wrap items-center justify-center gap-3 text-sm text-slate-600 dark:text-slate-300"${_scopeId}>`);
            if ((_d = courseData.value) == null ? void 0 : _d.slug) {
              _push2(ssrRenderComponent(unref(Link), {
                href: _ctx.route("public.schoolCourses.show", {
                  slug: courseData.value.slug
                }),
                class: "rounded-sm border border-gray-400 flex items-center justify-center gap-1 px-3 py-1 hover:text-blue-600 dark:hover:text-blue-400"
              }, {
                default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                  var _a2, _b2;
                  if (_push3) {
                    _push3(`<svg class="shrink-0 h-3 w-3 text-sky-600/85 dark:text-sky-200/85" fill="currentColor" viewBox="0 0 448 512"${_scopeId2}><path d="M318.38 208h-39.09c-1.49 27.03-6.54 51.35-14.21 70.41 27.71-13.24 48.02-39.19 53.3-70.41zm0-32c-5.29-31.22-25.59-57.17-53.3-70.41 7.68 19.06 12.72 43.38 14.21 70.41h39.09zM224 97.31c-7.69 7.45-20.77 34.42-23.43 78.69h46.87c-2.67-44.26-15.75-71.24-23.44-78.69zm-41.08 8.28c-27.71 13.24-48.02 39.19-53.3 70.41h39.09c1.49-27.03 6.53-51.35 14.21-70.41zm0 172.82c-7.68-19.06-12.72-43.38-14.21-70.41h-39.09c5.28 31.22 25.59 57.17 53.3 70.41zM247.43 208h-46.87c2.66 44.26 15.74 71.24 23.43 78.69 7.7-7.45 20.78-34.43 23.44-78.69zM448 358.4V25.6c0-16-9.6-25.6-25.6-25.6H96C41.6 0 0 41.6 0 96v320c0 54.4 41.6 96 96 96h326.4c12.8 0 25.6-9.6 25.6-25.6v-16c0-6.4-3.2-12.8-9.6-19.2-3.2-16-3.2-60.8 0-73.6 6.4-3.2 9.6-9.6 9.6-19.2zM224 64c70.69 0 128 57.31 128 128s-57.31 128-128 128S96 262.69 96 192 153.31 64 224 64zm160 384H96c-19.2 0-32-12.8-32-32s16-32 32-32h288v64z"${_scopeId2}></path></svg> ${ssrInterpolate(unref(t)("course"))}: ${ssrInterpolate((_a2 = courseData.value.translation) == null ? void 0 : _a2.title)}`);
                  } else {
                    return [
                      (openBlock(), createBlock("svg", {
                        class: "shrink-0 h-3 w-3 text-sky-600/85 dark:text-sky-200/85",
                        fill: "currentColor",
                        viewBox: "0 0 448 512"
                      }, [
                        createVNode("path", { d: "M318.38 208h-39.09c-1.49 27.03-6.54 51.35-14.21 70.41 27.71-13.24 48.02-39.19 53.3-70.41zm0-32c-5.29-31.22-25.59-57.17-53.3-70.41 7.68 19.06 12.72 43.38 14.21 70.41h39.09zM224 97.31c-7.69 7.45-20.77 34.42-23.43 78.69h46.87c-2.67-44.26-15.75-71.24-23.44-78.69zm-41.08 8.28c-27.71 13.24-48.02 39.19-53.3 70.41h39.09c1.49-27.03 6.53-51.35 14.21-70.41zm0 172.82c-7.68-19.06-12.72-43.38-14.21-70.41h-39.09c5.28 31.22 25.59 57.17 53.3 70.41zM247.43 208h-46.87c2.66 44.26 15.74 71.24 23.43 78.69 7.7-7.45 20.78-34.43 23.44-78.69zM448 358.4V25.6c0-16-9.6-25.6-25.6-25.6H96C41.6 0 0 41.6 0 96v320c0 54.4 41.6 96 96 96h326.4c12.8 0 25.6-9.6 25.6-25.6v-16c0-6.4-3.2-12.8-9.6-19.2-3.2-16-3.2-60.8 0-73.6 6.4-3.2 9.6-9.6 9.6-19.2zM224 64c70.69 0 128 57.31 128 128s-57.31 128-128 128S96 262.69 96 192 153.31 64 224 64zm160 384H96c-19.2 0-32-12.8-32-32s16-32 32-32h288v64z" })
                      ])),
                      createTextVNode(" " + toDisplayString(unref(t)("course")) + ": " + toDisplayString((_b2 = courseData.value.translation) == null ? void 0 : _b2.title), 1)
                    ];
                  }
                }),
                _: 1
              }, _parent2, _scopeId));
            } else {
              _push2(`<!---->`);
            }
            if (((_e = courseData.value) == null ? void 0 : _e.slug) && ((_f = moduleData.value) == null ? void 0 : _f.slug)) {
              _push2(ssrRenderComponent(unref(Link), {
                href: _ctx.route("public.schoolModules.show", {
                  courseSlug: courseData.value.slug,
                  slug: moduleData.value.slug
                }),
                class: "rounded-sm border border-gray-400 flex items-center justify-center gap-1 px-3 py-1 hover:text-blue-600 dark:hover:text-blue-400"
              }, {
                default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                  var _a2, _b2;
                  if (_push3) {
                    _push3(`<svg class="shrink-0 h-3 w-3 text-teal-600/85 dark:text-teal-200/85" fill="currentColor" viewBox="0 0 24 24"${_scopeId2}><rect x="1" y="1" width="10" height="10" rx="2"${_scopeId2}></rect><path class="fill-current text-teal-400" d="M23.428,4.618,19.381,.572a1.957,1.957,0,0,0-2.762,0L12.572,4.618a1.959,1.959,0,0,0,0,2.764l4.047,4.047a1.957,1.957,0,0,0,2.762,0l4.047-4.046A1.959,1.959,0,0,0,23.428,4.618Z"${_scopeId2}></path><rect x="13" y="13" width="10" height="10" rx="2"${_scopeId2}></rect><rect x="1" y="13" width="10" height="10" rx="2"${_scopeId2}></rect></svg> ${ssrInterpolate(unref(t)("module"))}: ${ssrInterpolate((_a2 = moduleData.value.translation) == null ? void 0 : _a2.title)}`);
                  } else {
                    return [
                      (openBlock(), createBlock("svg", {
                        class: "shrink-0 h-3 w-3 text-teal-600/85 dark:text-teal-200/85",
                        fill: "currentColor",
                        viewBox: "0 0 24 24"
                      }, [
                        createVNode("rect", {
                          x: "1",
                          y: "1",
                          width: "10",
                          height: "10",
                          rx: "2"
                        }),
                        createVNode("path", {
                          class: "fill-current text-teal-400",
                          d: "M23.428,4.618,19.381,.572a1.957,1.957,0,0,0-2.762,0L12.572,4.618a1.959,1.959,0,0,0,0,2.764l4.047,4.047a1.957,1.957,0,0,0,2.762,0l4.047-4.046A1.959,1.959,0,0,0,23.428,4.618Z"
                        }),
                        createVNode("rect", {
                          x: "13",
                          y: "13",
                          width: "10",
                          height: "10",
                          rx: "2"
                        }),
                        createVNode("rect", {
                          x: "1",
                          y: "13",
                          width: "10",
                          height: "10",
                          rx: "2"
                        })
                      ])),
                      createTextVNode(" " + toDisplayString(unref(t)("module")) + ": " + toDisplayString((_b2 = moduleData.value.translation) == null ? void 0 : _b2.title), 1)
                    ];
                  }
                }),
                _: 1
              }, _parent2, _scopeId));
            } else {
              _push2(`<!---->`);
            }
            if (lessonData.value.access_type) {
              _push2(`<span class="rounded-sm border border-gray-400 flex items-center justify-center gap-1 px-3 py-1"${_scopeId}><svg class="h-3 w-3 text-blue-600 dark:text-blue-400" fill="currentColor" viewBox="0 0 576 512"${_scopeId}><path d="M423.5 0C339.5.3 272 69.5 272 153.5V224H48c-26.5 0-48 21.5-48 48v192c0 26.5 21.5 48 48 48h352c26.5 0 48-21.5 48-48V272c0-26.5-21.5-48-48-48h-48v-71.1c0-39.6 31.7-72.5 71.3-72.9 40-.4 72.7 32.1 72.7 72v80c0 13.3 10.7 24 24 24h32c13.3 0 24-10.7 24-24v-80C576 68 507.5-.3 423.5 0z"${_scopeId}></path></svg> ${ssrInterpolate(unref(t)("access"))}: ${ssrInterpolate(translateAccessType(lessonData.value.access_type))}</span>`);
            } else {
              _push2(`<!---->`);
            }
            if (lessonData.value.rating_avg) {
              _push2(`<span class="rounded-sm border border-gray-400 flex items-center justify-center gap-1 px-3 py-1"${_scopeId}><svg viewBox="0 0 24 24" class="h-3 w-3 text-red-400 dark:text-red-300"${_scopeId}><path class="fill-current" d="M12.746,1.464l3.11,6.3L22.81,8.776a.831.831,0,0,1,.461,1.418l-5.033,4.9,1.188,6.926a.832.832,0,0,1-1.207.877L12,19.632,5.78,22.9a.833.833,0,0,1-1.207-.878L5.761,15.1l-5.033-4.9a.831.831,0,0,1,.461-1.418L8.143,7.765l3.11-6.3A.833.833,0,0,1,12.746,1.464Z"${_scopeId}></path></svg> ${ssrInterpolate(unref(t)("rating"))}: ${ssrInterpolate(Number(lessonData.value.rating_avg).toFixed(1))}</span>`);
            } else {
              _push2(`<!---->`);
            }
            if (lessonData.value.duration) {
              _push2(`<span class="rounded-sm border border-gray-400 flex items-center justify-center gap-1 px-3 py-1"${_scopeId}><svg class="w-3 h-3 text-blue-700 dark:text-blue-300" viewBox="0 0 24 24"${_scopeId}><path class="fill-current" d="M22,13a1,1,0,0,1,0-2h1.949A12.006,12.006,0,0,0,13,.051V2a1,1,0,0,1-2,0V.051A12.006,12.006,0,0,0,.051,11H2a1,1,0,0,1,0,2H.051A12.006,12.006,0,0,0,11,23.949V22a1,1,0,0,1,2,0v1.949A12.006,12.006,0,0,0,23.949,13Zm-6,0H12a1,1,0,0,1-.832-.445l-4-6a1,1,0,1,1,1.664-1.11L12.535,11H16a1,1,0,0,1,0,2Z"${_scopeId}></path></svg> ${ssrInterpolate(unref(t)("duration"))} ${ssrInterpolate(unref(t)("minutes"))} ${ssrInterpolate(lessonData.value.duration)}</span>`);
            } else {
              _push2(`<!---->`);
            }
            _push2(`</div>`);
            if (lessonDescription.value) {
              _push2(`<div class="mt-4 text-sm subtitle" itemprop="description"${_scopeId}>${lessonDescription.value ?? ""}</div>`);
            } else {
              _push2(`<!---->`);
            }
            _push2(`<div class="my-1 flex items-center justify-center"${_scopeId}><div itemprop="interactionStatistic" itemscope itemtype="https://schema.org/InteractionCounter"${_scopeId}><meta itemprop="interactionType" content="https://schema.org/LikeAction"${_scopeId}><meta itemprop="userInteractionCount"${ssrRenderAttr("content", lessonData.value.likes_count || 0)}${_scopeId}>`);
            _push2(ssrRenderComponent(LikeButtonEntity, {
              "likes-count": lessonData.value.likes_count || 0,
              "already-liked": lessonData.value.already_liked || false,
              "route-name": "public.schoolLessons.like",
              "route-params": lessonData.value.id,
              title: unref(t)("like")
            }, null, _parent2, _scopeId));
            _push2(`</div></div>`);
            if (hashtags.value.length) {
              _push2(`<div class="mt-4 flex flex-wrap items-center justify-center gap-2"${_scopeId}><!--[-->`);
              ssrRenderList(hashtags.value, (hashtag) => {
                _push2(ssrRenderComponent(unref(Link), {
                  key: hashtag.id,
                  href: _ctx.route("public.schoolHashtags.show", {
                    slug: hashtag.slug
                  }),
                  class: "rounded-sm px-2 py-1 text-xs font-semibold text-indigo-700 bg-indigo-50 dark:text-indigo-300 dark:bg-indigo-950/50 border border-indigo-400 hover:underline"
                }, {
                  default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                    var _a2, _b2;
                    if (_push3) {
                      _push3(` #${ssrInterpolate((_a2 = hashtag.translation) == null ? void 0 : _a2.name)}`);
                    } else {
                      return [
                        createTextVNode(" #" + toDisplayString((_b2 = hashtag.translation) == null ? void 0 : _b2.name), 1)
                      ];
                    }
                  }),
                  _: 2
                }, _parent2, _scopeId));
              });
              _push2(`<!--]--></div>`);
            } else {
              _push2(`<!---->`);
            }
            if (contentData.value) {
              _push2(`<div class="mt-8 rounded-md border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-700 dark:bg-gray-900"${_scopeId}><h2 class="mb-3 text-center text-lg font-semibold text-gray-700 dark:text-gray-300"${_scopeId}>${ssrInterpolate(unref(t)("content"))}</h2><div class="flex flex-wrap items-center justify-center gap-3 text-sm"${_scopeId}>`);
              if (contentData.value.type) {
                _push2(`<span class="rounded-sm border border-gray-400 px-3 py-1"${_scopeId}>${ssrInterpolate(unref(t)("type"))}: ${ssrInterpolate(contentData.value.type)}</span>`);
              } else {
                _push2(`<!---->`);
              }
              if (contentData.value.slug) {
                _push2(ssrRenderComponent(unref(Link), {
                  href: `#`,
                  class: "rounded-sm border border-gray-400 px-3 py-1 hover:underline"
                }, {
                  default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                    if (_push3) {
                      _push3(`${ssrInterpolate(contentData.value.title || unref(t)("open"))}`);
                    } else {
                      return [
                        createTextVNode(toDisplayString(contentData.value.title || unref(t)("open")), 1)
                      ];
                    }
                  }),
                  _: 1
                }, _parent2, _scopeId));
              } else {
                _push2(`<span${_scopeId}>${ssrInterpolate(contentData.value.title)}</span>`);
              }
              _push2(`</div></div>`);
            } else {
              _push2(`<!---->`);
            }
            _push2(`</article>`);
            _push2(ssrRenderComponent(_sfc_main$4, { videos: mainVideosList.value }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(SectionBanners, { banners: mainBannersList.value }, null, _parent2, _scopeId));
            _push2(`</div></section>`);
            if (showRight.value) {
              _push2(`<aside class="${ssrRenderClass([rightCollapsed.value ? "lg:w-6" : "lg:w-72", "shrink-0 pr-3 transition-all duration-300"])}"${_scopeId}>`);
              _push2(ssrRenderComponent(_sfc_main$5, {
                onCollapsed: ($event) => rightCollapsed.value = $event
              }, null, _parent2, _scopeId));
              _push2(`</aside>`);
            } else {
              _push2(`<!---->`);
            }
            _push2(`</div></div></main>`);
            _push2(ssrRenderComponent(_sfc_main$6, null, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(Progress, null, null, _parent2, _scopeId));
          } else {
            return [
              createVNode(_sfc_main$2),
              createVNode("main", { class: "min-h-screen px-1 lg:px-6 max-w-full" }, [
                createVNode("div", { class: "mx-auto tracking-wider pt-20 lg:pt-44" }, [
                  createVNode("div", { class: "ext-color w-full min-w-0 py-3 px-1 flex flex-col lg:flex-row gap-4 rounded-3xl border-2 border-slate-300 dark:border-slate-500" }, [
                    showLeft.value ? (openBlock(), createBlock("aside", {
                      key: 0,
                      class: ["shrink-0 pl-3 transition-all duration-300", leftCollapsed.value ? "lg:w-6" : "lg:w-72"]
                    }, [
                      createVNode(_sfc_main$3, {
                        "track-tree": trackTree.value,
                        onCollapsed: ($event) => leftCollapsed.value = $event
                      }, null, 8, ["track-tree", "onCollapsed"])
                    ], 2)) : createCommentVNode("", true),
                    createVNode("section", { class: "w-full pb-6 slate-1 min-w-0" }, [
                      createVNode("div", { class: "mx-auto max-w-6xl" }, [
                        createVNode("article", {
                          class: "selection:bg-red-400 selection:text-white",
                          itemscope: "",
                          itemtype: "https://schema.org/LearningResource"
                        }, [
                          canonicalUrl.value ? (openBlock(), createBlock("meta", {
                            key: 0,
                            itemprop: "url",
                            content: canonicalUrl.value
                          }, null, 8, ["content"])) : createCommentVNode("", true),
                          contentLocale.value ? (openBlock(), createBlock("meta", {
                            key: 1,
                            itemprop: "inLanguage",
                            content: contentLocale.value
                          }, null, 8, ["content"])) : createCommentVNode("", true),
                          createVNode("meta", {
                            itemprop: "learningResourceType",
                            content: "Lesson"
                          }),
                          lessonData.value.published_at ? (openBlock(), createBlock("meta", {
                            key: 2,
                            itemprop: "datePublished",
                            content: lessonData.value.published_at
                          }, null, 8, ["content"])) : createCommentVNode("", true),
                          schemaDuration.value ? (openBlock(), createBlock("meta", {
                            key: 3,
                            itemprop: "timeRequired",
                            content: schemaDuration.value
                          }, null, 8, ["content"])) : createCommentVNode("", true),
                          lessonData.value.difficulty !== null && lessonData.value.difficulty !== void 0 ? (openBlock(), createBlock("meta", {
                            key: 4,
                            itemprop: "educationalLevel",
                            content: String(lessonData.value.difficulty)
                          }, null, 8, ["content"])) : createCommentVNode("", true),
                          createVNode("nav", {
                            class: "text-sm mb-3",
                            "aria-label": "Breadcrumb"
                          }, [
                            createVNode("ol", { class: "flex flex-wrap items-center font-semibold" }, [
                              createVNode("li", null, [
                                createVNode(unref(Link), {
                                  href: _ctx.route("home"),
                                  class: "breadcrumb-link hover:underline"
                                }, {
                                  default: withCtx(() => [
                                    createTextVNode(toDisplayString(unref(t)("home")), 1)
                                  ]),
                                  _: 1
                                }, 8, ["href"])
                              ]),
                              createVNode("li", null, [
                                createVNode("span", { class: "mx-2 breadcrumbs" }, "/")
                              ]),
                              createVNode("li", null, [
                                createVNode(unref(Link), {
                                  href: _ctx.route("public.schoolLessons.index"),
                                  class: "breadcrumb-link hover:underline"
                                }, {
                                  default: withCtx(() => [
                                    createTextVNode(toDisplayString(unref(t)("lessons")), 1)
                                  ]),
                                  _: 1
                                }, 8, ["href"])
                              ]),
                              ((_g = courseData.value) == null ? void 0 : _g.slug) ? (openBlock(), createBlock(Fragment, { key: 0 }, [
                                createVNode("li", null, [
                                  createVNode("span", { class: "mx-2 breadcrumbs" }, "/")
                                ]),
                                createVNode("li", null, [
                                  createVNode(unref(Link), {
                                    href: _ctx.route("public.schoolCourses.show", {
                                      slug: courseData.value.slug
                                    }),
                                    class: "breadcrumb-link hover:underline"
                                  }, {
                                    default: withCtx(() => {
                                      var _a2;
                                      return [
                                        createTextVNode(toDisplayString((_a2 = courseData.value.translation) == null ? void 0 : _a2.title), 1)
                                      ];
                                    }),
                                    _: 1
                                  }, 8, ["href"])
                                ])
                              ], 64)) : createCommentVNode("", true),
                              ((_h = courseData.value) == null ? void 0 : _h.slug) && ((_i = moduleData.value) == null ? void 0 : _i.slug) ? (openBlock(), createBlock(Fragment, { key: 1 }, [
                                createVNode("li", null, [
                                  createVNode("span", { class: "mx-2 breadcrumbs" }, "/")
                                ]),
                                createVNode("li", null, [
                                  createVNode(unref(Link), {
                                    href: _ctx.route("public.schoolModules.show", {
                                      courseSlug: courseData.value.slug,
                                      slug: moduleData.value.slug
                                    }),
                                    class: "breadcrumb-link hover:underline"
                                  }, {
                                    default: withCtx(() => {
                                      var _a2;
                                      return [
                                        createTextVNode(toDisplayString((_a2 = moduleData.value.translation) == null ? void 0 : _a2.title), 1)
                                      ];
                                    }),
                                    _: 1
                                  }, 8, ["href"])
                                ])
                              ], 64)) : createCommentVNode("", true),
                              createVNode("li", null, [
                                createVNode("span", { class: "mx-2 breadcrumbs" }, "/")
                              ]),
                              createVNode("li", {
                                class: "breadcrumbs",
                                "aria-current": "page"
                              }, toDisplayString(translation.value.title), 1)
                            ])
                          ]),
                          createVNode("div", { class: "flex flex-wrap items-center justify-center gap-3 title my-3" }, [
                            createVNode("h1", {
                              class: "text-2xl font-bold",
                              itemprop: "name"
                            }, toDisplayString(lessonTitle.value), 1),
                            lessonData.value.views > 0 ? (openBlock(), createBlock("div", {
                              key: 0,
                              title: unref(t)("views"),
                              class: "flex items-center justify-center gap-1",
                              itemprop: "interactionStatistic",
                              itemscope: "",
                              itemtype: "https://schema.org/InteractionCounter"
                            }, [
                              createVNode("meta", {
                                itemprop: "interactionType",
                                content: "https://schema.org/ViewAction"
                              }),
                              createVNode("meta", {
                                itemprop: "userInteractionCount",
                                content: lessonData.value.views
                              }, null, 8, ["content"])
                            ], 8, ["title"])) : createCommentVNode("", true)
                          ]),
                          translation.value.subtitle ? (openBlock(), createBlock("div", {
                            key: 5,
                            class: "mt-1 mb-3 text-sm subtitle text-center"
                          }, toDisplayString(translation.value.subtitle), 1)) : createCommentVNode("", true),
                          hasLessonImages.value ? (openBlock(), createBlock("div", {
                            key: 6,
                            class: "flex items-center justify-center"
                          }, [
                            createVNode("div", { class: "w-full" }, [
                              createVNode(ImageGalleryMain, {
                                images: lessonImages.value,
                                alt: lessonTitle.value,
                                itemprop: "image",
                                loading: "eager",
                                fetchpriority: "high",
                                "rounded-class": "rounded-lg",
                                "shadow-class": "shadow-lg shadow-gray-400 dark:shadow-gray-700",
                                "img-class": "w-full h-full object-cover"
                              }, null, 8, ["images", "alt"])
                            ])
                          ])) : createCommentVNode("", true),
                          createVNode("div", { class: "my-4 flex flex-wrap items-center justify-center gap-3 text-sm text-slate-600 dark:text-slate-300" }, [
                            ((_j = courseData.value) == null ? void 0 : _j.slug) ? (openBlock(), createBlock(unref(Link), {
                              key: 0,
                              href: _ctx.route("public.schoolCourses.show", {
                                slug: courseData.value.slug
                              }),
                              class: "rounded-sm border border-gray-400 flex items-center justify-center gap-1 px-3 py-1 hover:text-blue-600 dark:hover:text-blue-400"
                            }, {
                              default: withCtx(() => {
                                var _a2;
                                return [
                                  (openBlock(), createBlock("svg", {
                                    class: "shrink-0 h-3 w-3 text-sky-600/85 dark:text-sky-200/85",
                                    fill: "currentColor",
                                    viewBox: "0 0 448 512"
                                  }, [
                                    createVNode("path", { d: "M318.38 208h-39.09c-1.49 27.03-6.54 51.35-14.21 70.41 27.71-13.24 48.02-39.19 53.3-70.41zm0-32c-5.29-31.22-25.59-57.17-53.3-70.41 7.68 19.06 12.72 43.38 14.21 70.41h39.09zM224 97.31c-7.69 7.45-20.77 34.42-23.43 78.69h46.87c-2.67-44.26-15.75-71.24-23.44-78.69zm-41.08 8.28c-27.71 13.24-48.02 39.19-53.3 70.41h39.09c1.49-27.03 6.53-51.35 14.21-70.41zm0 172.82c-7.68-19.06-12.72-43.38-14.21-70.41h-39.09c5.28 31.22 25.59 57.17 53.3 70.41zM247.43 208h-46.87c2.66 44.26 15.74 71.24 23.43 78.69 7.7-7.45 20.78-34.43 23.44-78.69zM448 358.4V25.6c0-16-9.6-25.6-25.6-25.6H96C41.6 0 0 41.6 0 96v320c0 54.4 41.6 96 96 96h326.4c12.8 0 25.6-9.6 25.6-25.6v-16c0-6.4-3.2-12.8-9.6-19.2-3.2-16-3.2-60.8 0-73.6 6.4-3.2 9.6-9.6 9.6-19.2zM224 64c70.69 0 128 57.31 128 128s-57.31 128-128 128S96 262.69 96 192 153.31 64 224 64zm160 384H96c-19.2 0-32-12.8-32-32s16-32 32-32h288v64z" })
                                  ])),
                                  createTextVNode(" " + toDisplayString(unref(t)("course")) + ": " + toDisplayString((_a2 = courseData.value.translation) == null ? void 0 : _a2.title), 1)
                                ];
                              }),
                              _: 1
                            }, 8, ["href"])) : createCommentVNode("", true),
                            ((_k = courseData.value) == null ? void 0 : _k.slug) && ((_l = moduleData.value) == null ? void 0 : _l.slug) ? (openBlock(), createBlock(unref(Link), {
                              key: 1,
                              href: _ctx.route("public.schoolModules.show", {
                                courseSlug: courseData.value.slug,
                                slug: moduleData.value.slug
                              }),
                              class: "rounded-sm border border-gray-400 flex items-center justify-center gap-1 px-3 py-1 hover:text-blue-600 dark:hover:text-blue-400"
                            }, {
                              default: withCtx(() => {
                                var _a2;
                                return [
                                  (openBlock(), createBlock("svg", {
                                    class: "shrink-0 h-3 w-3 text-teal-600/85 dark:text-teal-200/85",
                                    fill: "currentColor",
                                    viewBox: "0 0 24 24"
                                  }, [
                                    createVNode("rect", {
                                      x: "1",
                                      y: "1",
                                      width: "10",
                                      height: "10",
                                      rx: "2"
                                    }),
                                    createVNode("path", {
                                      class: "fill-current text-teal-400",
                                      d: "M23.428,4.618,19.381,.572a1.957,1.957,0,0,0-2.762,0L12.572,4.618a1.959,1.959,0,0,0,0,2.764l4.047,4.047a1.957,1.957,0,0,0,2.762,0l4.047-4.046A1.959,1.959,0,0,0,23.428,4.618Z"
                                    }),
                                    createVNode("rect", {
                                      x: "13",
                                      y: "13",
                                      width: "10",
                                      height: "10",
                                      rx: "2"
                                    }),
                                    createVNode("rect", {
                                      x: "1",
                                      y: "13",
                                      width: "10",
                                      height: "10",
                                      rx: "2"
                                    })
                                  ])),
                                  createTextVNode(" " + toDisplayString(unref(t)("module")) + ": " + toDisplayString((_a2 = moduleData.value.translation) == null ? void 0 : _a2.title), 1)
                                ];
                              }),
                              _: 1
                            }, 8, ["href"])) : createCommentVNode("", true),
                            lessonData.value.access_type ? (openBlock(), createBlock("span", {
                              key: 2,
                              class: "rounded-sm border border-gray-400 flex items-center justify-center gap-1 px-3 py-1"
                            }, [
                              (openBlock(), createBlock("svg", {
                                class: "h-3 w-3 text-blue-600 dark:text-blue-400",
                                fill: "currentColor",
                                viewBox: "0 0 576 512"
                              }, [
                                createVNode("path", { d: "M423.5 0C339.5.3 272 69.5 272 153.5V224H48c-26.5 0-48 21.5-48 48v192c0 26.5 21.5 48 48 48h352c26.5 0 48-21.5 48-48V272c0-26.5-21.5-48-48-48h-48v-71.1c0-39.6 31.7-72.5 71.3-72.9 40-.4 72.7 32.1 72.7 72v80c0 13.3 10.7 24 24 24h32c13.3 0 24-10.7 24-24v-80C576 68 507.5-.3 423.5 0z" })
                              ])),
                              createTextVNode(" " + toDisplayString(unref(t)("access")) + ": " + toDisplayString(translateAccessType(lessonData.value.access_type)), 1)
                            ])) : createCommentVNode("", true),
                            lessonData.value.rating_avg ? (openBlock(), createBlock("span", {
                              key: 3,
                              class: "rounded-sm border border-gray-400 flex items-center justify-center gap-1 px-3 py-1"
                            }, [
                              (openBlock(), createBlock("svg", {
                                viewBox: "0 0 24 24",
                                class: "h-3 w-3 text-red-400 dark:text-red-300"
                              }, [
                                createVNode("path", {
                                  class: "fill-current",
                                  d: "M12.746,1.464l3.11,6.3L22.81,8.776a.831.831,0,0,1,.461,1.418l-5.033,4.9,1.188,6.926a.832.832,0,0,1-1.207.877L12,19.632,5.78,22.9a.833.833,0,0,1-1.207-.878L5.761,15.1l-5.033-4.9a.831.831,0,0,1,.461-1.418L8.143,7.765l3.11-6.3A.833.833,0,0,1,12.746,1.464Z"
                                })
                              ])),
                              createTextVNode(" " + toDisplayString(unref(t)("rating")) + ": " + toDisplayString(Number(lessonData.value.rating_avg).toFixed(1)), 1)
                            ])) : createCommentVNode("", true),
                            lessonData.value.duration ? (openBlock(), createBlock("span", {
                              key: 4,
                              class: "rounded-sm border border-gray-400 flex items-center justify-center gap-1 px-3 py-1"
                            }, [
                              (openBlock(), createBlock("svg", {
                                class: "w-3 h-3 text-blue-700 dark:text-blue-300",
                                viewBox: "0 0 24 24"
                              }, [
                                createVNode("path", {
                                  class: "fill-current",
                                  d: "M22,13a1,1,0,0,1,0-2h1.949A12.006,12.006,0,0,0,13,.051V2a1,1,0,0,1-2,0V.051A12.006,12.006,0,0,0,.051,11H2a1,1,0,0,1,0,2H.051A12.006,12.006,0,0,0,11,23.949V22a1,1,0,0,1,2,0v1.949A12.006,12.006,0,0,0,23.949,13Zm-6,0H12a1,1,0,0,1-.832-.445l-4-6a1,1,0,1,1,1.664-1.11L12.535,11H16a1,1,0,0,1,0,2Z"
                                })
                              ])),
                              createTextVNode(" " + toDisplayString(unref(t)("duration")) + " " + toDisplayString(unref(t)("minutes")) + " " + toDisplayString(lessonData.value.duration), 1)
                            ])) : createCommentVNode("", true)
                          ]),
                          lessonDescription.value ? (openBlock(), createBlock("div", {
                            key: 7,
                            class: "mt-4 text-sm subtitle",
                            itemprop: "description",
                            innerHTML: lessonDescription.value
                          }, null, 8, ["innerHTML"])) : createCommentVNode("", true),
                          createVNode("div", { class: "my-1 flex items-center justify-center" }, [
                            createVNode("div", {
                              itemprop: "interactionStatistic",
                              itemscope: "",
                              itemtype: "https://schema.org/InteractionCounter"
                            }, [
                              createVNode("meta", {
                                itemprop: "interactionType",
                                content: "https://schema.org/LikeAction"
                              }),
                              createVNode("meta", {
                                itemprop: "userInteractionCount",
                                content: lessonData.value.likes_count || 0
                              }, null, 8, ["content"]),
                              createVNode(LikeButtonEntity, {
                                "likes-count": lessonData.value.likes_count || 0,
                                "already-liked": lessonData.value.already_liked || false,
                                "route-name": "public.schoolLessons.like",
                                "route-params": lessonData.value.id,
                                title: unref(t)("like")
                              }, null, 8, ["likes-count", "already-liked", "route-params", "title"])
                            ])
                          ]),
                          hashtags.value.length ? (openBlock(), createBlock("div", {
                            key: 8,
                            class: "mt-4 flex flex-wrap items-center justify-center gap-2"
                          }, [
                            (openBlock(true), createBlock(Fragment, null, renderList(hashtags.value, (hashtag) => {
                              return openBlock(), createBlock(unref(Link), {
                                key: hashtag.id,
                                href: _ctx.route("public.schoolHashtags.show", {
                                  slug: hashtag.slug
                                }),
                                class: "rounded-sm px-2 py-1 text-xs font-semibold text-indigo-700 bg-indigo-50 dark:text-indigo-300 dark:bg-indigo-950/50 border border-indigo-400 hover:underline"
                              }, {
                                default: withCtx(() => {
                                  var _a2;
                                  return [
                                    createTextVNode(" #" + toDisplayString((_a2 = hashtag.translation) == null ? void 0 : _a2.name), 1)
                                  ];
                                }),
                                _: 2
                              }, 1032, ["href"]);
                            }), 128))
                          ])) : createCommentVNode("", true),
                          contentData.value ? (openBlock(), createBlock("div", {
                            key: 9,
                            class: "mt-8 rounded-md border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-700 dark:bg-gray-900"
                          }, [
                            createVNode("h2", { class: "mb-3 text-center text-lg font-semibold text-gray-700 dark:text-gray-300" }, toDisplayString(unref(t)("content")), 1),
                            createVNode("div", { class: "flex flex-wrap items-center justify-center gap-3 text-sm" }, [
                              contentData.value.type ? (openBlock(), createBlock("span", {
                                key: 0,
                                class: "rounded-sm border border-gray-400 px-3 py-1"
                              }, toDisplayString(unref(t)("type")) + ": " + toDisplayString(contentData.value.type), 1)) : createCommentVNode("", true),
                              contentData.value.slug ? (openBlock(), createBlock(unref(Link), {
                                key: 1,
                                href: `#`,
                                class: "rounded-sm border border-gray-400 px-3 py-1 hover:underline"
                              }, {
                                default: withCtx(() => [
                                  createTextVNode(toDisplayString(contentData.value.title || unref(t)("open")), 1)
                                ]),
                                _: 1
                              })) : (openBlock(), createBlock("span", { key: 2 }, toDisplayString(contentData.value.title), 1))
                            ])
                          ])) : createCommentVNode("", true)
                        ]),
                        createVNode(_sfc_main$4, { videos: mainVideosList.value }, null, 8, ["videos"]),
                        createVNode(SectionBanners, { banners: mainBannersList.value }, null, 8, ["banners"])
                      ])
                    ]),
                    showRight.value ? (openBlock(), createBlock("aside", {
                      key: 1,
                      class: ["shrink-0 pr-3 transition-all duration-300", rightCollapsed.value ? "lg:w-6" : "lg:w-72"]
                    }, [
                      createVNode(_sfc_main$5, {
                        onCollapsed: ($event) => rightCollapsed.value = $event
                      }, null, 8, ["onCollapsed"])
                    ], 2)) : createCommentVNode("", true)
                  ])
                ])
              ]),
              createVNode(_sfc_main$6),
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
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Pages/Public/Default/School/SchoolLessons/Show.vue");
  return _sfc_setup ? _sfc_setup(props, ctx) : void 0;
};
export {
  _sfc_main as default
};
