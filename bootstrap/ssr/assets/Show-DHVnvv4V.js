import { computed, ref, watch, mergeProps, unref, useSSRContext, withCtx, createVNode, resolveDynamicComponent, createBlock, createCommentVNode, toDisplayString, openBlock, createTextVNode, Fragment, renderList } from "vue";
import { ssrRenderAttrs, ssrInterpolate, ssrRenderComponent, ssrRenderAttr, ssrRenderVNode, ssrRenderClass, ssrRenderList } from "vue/server-renderer";
import { usePage, Head, Link } from "@inertiajs/vue3";
import { useI18n } from "vue-i18n";
import { _ as _sfc_main$5 } from "./DefaultLayout-6zxGxPtK.js";
import { _ as _sfc_main$6, a as _sfc_main$b, P as Progress } from "./Progress-CyfOKcqP.js";
import { _ as _sfc_main$7, a as _sfc_main$a } from "./RightSidebarSchool-DlAegojf.js";
import { L as LikeButtonEntity } from "./LikeButtonEntity-ZC4HMEAO.js";
import { I as ImageGalleryMain } from "./ImageGalleryMain-mIfXDUWm.js";
import { _ as _sfc_main$9, S as SectionBanners } from "./SectionBanners-Cdgmr0Bw.js";
import { _ as _sfc_main$3, a as _sfc_main$4 } from "./ModuleRows-BG2403lW.js";
import { _ as _sfc_main$2 } from "./ViewModeToggle-DMCnQ0wo.js";
import { _ as _sfc_main$8 } from "./CourseGrid-DoYn9a32.js";
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
import "./UniversalImageSlider-Cu2Xndcn.js";
import "./EntityStats-Dy-sjN5R.js";
const VIEW_KEY = "public_course_modules_view";
const _sfc_main$1 = {
  __name: "ModulesSection",
  __ssrInlineRender: true,
  props: {
    modules: { type: [Array, Object], default: () => [] },
    cols: { type: Number, default: 2 }
  },
  setup(__props) {
    const { t } = useI18n();
    const props = __props;
    const normalizeList = (value) => {
      if (Array.isArray(value)) return value;
      if (Array.isArray(value == null ? void 0 : value.data)) return value.data;
      return [];
    };
    const modulesList = computed(() => normalizeList(props.modules));
    const viewMode = ref(localStorage.getItem(VIEW_KEY) || "grid");
    watch(viewMode, (v) => {
      localStorage.setItem(VIEW_KEY, v);
    });
    const modulesCount = computed(() => modulesList.value.length);
    return (_ctx, _push, _parent, _attrs) => {
      if (modulesCount.value > 0) {
        _push(`<section${ssrRenderAttrs(mergeProps({ class: "mt-8" }, _attrs))}><div class="mb-4 flex flex-wrap items-center justify-between gap-3"><div class="my-3 text-start center text-xs text-slate-500 dark:text-slate-400">${ssrInterpolate(unref(t)("modules"))}: <span class="font-bold">${ssrInterpolate(modulesCount.value)}</span></div><div class="flex items-center gap-2"><svg class="shrink-0 h-4 w-4" viewBox="0 0 24 24"><rect class="fill-current text-slate-400" x="1" y="1" width="10" height="10" rx="2"></rect><path class="fill-current text-slate-600" d="M23.428,4.618,19.381.572h0a1.957,1.957,0,0,0-2.762,0L12.572,4.618a1.959,1.959,0,0,0,0,2.764l4.047,4.047a1.957,1.957,0,0,0,2.762,0l4.047-4.046A1.959,1.959,0,0,0,23.428,4.618Z"></path><rect class="fill-current text-slate-400" x="13" y="13" width="10" height="10" rx="2"></rect><rect class="fill-current text-slate-400" x="1" y="13" width="10" height="10" rx="2"></rect></svg><h2 class="text-lg font-semibold text-gray-700 dark:text-gray-300">${ssrInterpolate(unref(t)("modules"))}</h2></div>`);
        _push(ssrRenderComponent(_sfc_main$2, {
          modelValue: viewMode.value,
          "onUpdate:modelValue": ($event) => viewMode.value = $event
        }, null, _parent));
        _push(`</div>`);
        if (viewMode.value === "grid") {
          _push(ssrRenderComponent(_sfc_main$3, {
            modules: modulesList.value,
            cols: __props.cols
          }, null, _parent));
        } else {
          _push(ssrRenderComponent(_sfc_main$4, { modules: modulesList.value }, null, _parent));
        }
        _push(`</section>`);
      } else {
        _push(`<!---->`);
      }
    };
  }
};
const _sfc_setup$1 = _sfc_main$1.setup;
_sfc_main$1.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Components/Public/Default/School/SchoolModule/ModulesSection.vue");
  return _sfc_setup$1 ? _sfc_setup$1(props, ctx) : void 0;
};
const LEFT_SIDEBAR_KEY = "public_left_sidebar_collapsed";
const RIGHT_SIDEBAR_KEY = "public_right_sidebar_collapsed";
const _sfc_main = {
  __name: "Show",
  __ssrInlineRender: true,
  props: {
    title: String,
    canLogin: Boolean,
    canRegister: Boolean,
    course: {
      type: Object,
      default: () => ({})
    },
    modules: {
      type: [Array, Object],
      default: () => []
    },
    trackTree: {
      type: Array,
      default: () => []
    },
    locale: {
      type: String,
      default: "ru"
    },
    mainVideos: {
      type: Array,
      default: () => []
    },
    mainBanners: {
      type: Array,
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
    const translateLevel = (level) => {
      const normalized = String(level ?? "").trim().toLowerCase();
      const map = {
        beginner: "levelBeginner",
        intermediate: "levelIntermediate",
        advanced: "levelAdvanced"
      };
      return map[normalized] ? t(map[normalized]) : level;
    };
    const absoluteUrl = (value) => {
      if (!value) return "";
      if (/^https?:\/\//i.test(value)) return value;
      if (typeof window === "undefined") return value;
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
    const courseData = computed(() => props.course ?? {});
    const translation = computed(() => {
      var _a;
      return ((_a = courseData.value) == null ? void 0 : _a.translation) ?? {};
    });
    const courseTitle = computed(() => {
      var _a;
      return ((_a = translation.value) == null ? void 0 : _a.title) ?? "";
    });
    const courseSubtitle = computed(() => {
      var _a;
      return ((_a = translation.value) == null ? void 0 : _a.subtitle) ?? "";
    });
    const courseShort = computed(() => {
      var _a;
      return ((_a = translation.value) == null ? void 0 : _a.short) ?? "";
    });
    const courseDescription = computed(() => {
      var _a;
      return ((_a = translation.value) == null ? void 0 : _a.description) ?? "";
    });
    const seoTitle = computed(
      () => {
        var _a;
        return ((_a = translation.value) == null ? void 0 : _a.meta_title) || courseTitle.value || t("courses");
      }
    );
    const seoDescription = computed(
      () => {
        var _a;
        return ((_a = translation.value) == null ? void 0 : _a.meta_desc) || courseShort.value || stripHtml(courseDescription.value) || "";
      }
    );
    const seoKeywords = computed(() => {
      var _a;
      return ((_a = translation.value) == null ? void 0 : _a.meta_keywords) ?? "";
    });
    const contentLocale = computed(
      () => {
        var _a;
        return ((_a = translation.value) == null ? void 0 : _a.locale) || props.locale || "ru";
      }
    );
    const ogLocale = computed(
      () => contentLocale.value === "ru" ? "ru_RU" : contentLocale.value
    );
    const courseRouteUrl = computed(() => {
      var _a;
      if (!((_a = courseData.value) == null ? void 0 : _a.slug)) return "";
      return route("public.schoolCourses.show", {
        slug: courseData.value.slug
      });
    });
    const canonicalUrl = computed(() => absoluteUrl(courseRouteUrl.value));
    const coursesIndexUrl = computed(() => absoluteUrl(route("public.schoolCourses.index")));
    const homeUrl = computed(() => absoluteUrl(route("home")));
    const courseImages = computed(() => {
      var _a;
      return normalizeList((_a = courseData.value) == null ? void 0 : _a.images);
    });
    const firstCourseImage = computed(() => courseImages.value[0] ?? null);
    const hasCourseImages = computed(() => courseImages.value.length > 0);
    const firstCourseImageUrl = computed(
      () => {
        var _a, _b, _c, _d;
        return absoluteUrl(
          ((_a = firstCourseImage.value) == null ? void 0 : _a.webp_url) || ((_b = firstCourseImage.value) == null ? void 0 : _b.image_url) || ((_c = firstCourseImage.value) == null ? void 0 : _c.url) || ((_d = firstCourseImage.value) == null ? void 0 : _d.thumb_url) || ""
        );
      }
    );
    const instructorProfile = computed(() => {
      var _a;
      return ((_a = courseData.value) == null ? void 0 : _a.instructorProfile) ?? null;
    });
    const instructorImages = computed(() => {
      var _a;
      return normalizeList((_a = instructorProfile.value) == null ? void 0 : _a.images);
    });
    const instructorPrimaryImage = computed(() => instructorImages.value[0] ?? null);
    const instructorImageUrl = computed(
      () => {
        var _a, _b, _c, _d;
        return ((_a = instructorPrimaryImage.value) == null ? void 0 : _a.webp_url) || ((_b = instructorPrimaryImage.value) == null ? void 0 : _b.image_url) || ((_c = instructorPrimaryImage.value) == null ? void 0 : _c.url) || ((_d = instructorPrimaryImage.value) == null ? void 0 : _d.thumb_url) || null;
      }
    );
    const instructorName = computed(
      () => {
        var _a, _b, _c, _d;
        return ((_b = (_a = instructorProfile.value) == null ? void 0 : _a.translation) == null ? void 0 : _b.title) || ((_d = (_c = instructorProfile.value) == null ? void 0 : _c.user) == null ? void 0 : _d.name) || t("instructor");
      }
    );
    const hashtags = computed(() => {
      var _a;
      return normalizeList((_a = courseData.value) == null ? void 0 : _a.hashtags);
    });
    const reviews = computed(() => {
      var _a;
      return normalizeList((_a = courseData.value) == null ? void 0 : _a.reviews);
    });
    const relatedCourses = computed(() => {
      var _a;
      return normalizeList((_a = courseData.value) == null ? void 0 : _a.related_courses);
    });
    const modulesList = computed(() => normalizeList(props.modules));
    const trackTree = computed(
      () => Array.isArray(props.trackTree) ? props.trackTree : []
    );
    const mainVideosList = computed(() => normalizeList(props.mainVideos));
    const mainBannersList = computed(() => normalizeList(props.mainBanners));
    const schemaDuration = computed(() => {
      var _a;
      const minutes = Number((_a = courseData.value) == null ? void 0 : _a.duration);
      if (!Number.isFinite(minutes) || minutes <= 0) return null;
      return `PT${Math.round(minutes)}M`;
    });
    const courseSchema = computed(() => {
      var _a, _b, _c, _d;
      const schema = {
        "@context": "https://schema.org",
        "@type": "Course",
        name: courseTitle.value || seoTitle.value,
        url: canonicalUrl.value || courseRouteUrl.value,
        inLanguage: contentLocale.value
      };
      const description = stripHtml(
        seoDescription.value || courseShort.value || courseDescription.value
      );
      if (description) schema.description = description;
      if (firstCourseImageUrl.value) schema.image = firstCourseImageUrl.value;
      if ((_a = courseData.value) == null ? void 0 : _a.published_at) schema.datePublished = courseData.value.published_at;
      if ((_b = courseData.value) == null ? void 0 : _b.level) {
        schema.educationalLevel = translateLevel(courseData.value.level);
      }
      if (schemaDuration.value) {
        schema.timeRequired = schemaDuration.value;
      }
      if (instructorProfile.value && instructorName.value) {
        schema.provider = {
          "@type": "Person",
          name: instructorName.value
        };
        if (instructorImageUrl.value) {
          schema.provider.image = absoluteUrl(instructorImageUrl.value);
        }
      }
      const interactions = [];
      const views = Number((_c = courseData.value) == null ? void 0 : _c.views);
      const likes = Number((_d = courseData.value) == null ? void 0 : _d.likes_count);
      if (Number.isFinite(views) && views > 0) {
        interactions.push({
          "@type": "InteractionCounter",
          interactionType: { "@type": "ViewAction" },
          userInteractionCount: views
        });
      }
      if (Number.isFinite(likes) && likes > 0) {
        interactions.push({
          "@type": "InteractionCounter",
          interactionType: { "@type": "LikeAction" },
          userInteractionCount: likes
        });
      }
      if (interactions.length) {
        schema.interactionStatistic = interactions;
      }
      return schema;
    });
    const breadcrumbSchema = computed(() => ({
      "@context": "https://schema.org",
      "@type": "BreadcrumbList",
      itemListElement: [
        {
          "@type": "ListItem",
          position: 1,
          name: t("home"),
          item: homeUrl.value
        },
        {
          "@type": "ListItem",
          position: 2,
          name: t("courses"),
          item: coursesIndexUrl.value
        },
        {
          "@type": "ListItem",
          position: 3,
          name: courseTitle.value || seoTitle.value,
          item: canonicalUrl.value || courseRouteUrl.value
        }
      ]
    }));
    const courseSchemaJson = computed(() => JSON.stringify(courseSchema.value));
    const breadcrumbSchemaJson = computed(() => JSON.stringify(breadcrumbSchema.value));
    const { siteSettings } = page.props;
    const showLeft = computed(
      () => !(siteSettings == null ? void 0 : siteSettings.ViewLeftColumn) || siteSettings.ViewLeftColumn === "true"
    );
    const showRight = computed(
      () => !(siteSettings == null ? void 0 : siteSettings.ViewRightColumn) || siteSettings.ViewRightColumn === "true"
    );
    const getStoredBoolean = (key, defaultValue = false) => {
      const value = localStorage.getItem(key);
      return value === null ? defaultValue : value === "true";
    };
    const leftCollapsed = ref(getStoredBoolean(LEFT_SIDEBAR_KEY, false));
    const rightCollapsed = ref(getStoredBoolean(RIGHT_SIDEBAR_KEY, false));
    watch(leftCollapsed, (value) => {
      localStorage.setItem(LEFT_SIDEBAR_KEY, String(value));
    });
    watch(rightCollapsed, (value) => {
      localStorage.setItem(RIGHT_SIDEBAR_KEY, String(value));
    });
    const gridCols = computed(() => {
      const leftExpanded = showLeft.value && !leftCollapsed.value;
      const rightExpanded = showRight.value && !rightCollapsed.value;
      if (leftExpanded && rightExpanded) return 2;
      if (leftExpanded || rightExpanded) return 3;
      return 4;
    });
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
            _push2(`<meta name="viewport" content="width=device-width, initial-scale=1"${_scopeId}><meta name="robots" content="index, follow, max-image-preview:large"${_scopeId}>`);
            if (canonicalUrl.value) {
              _push2(`<link rel="canonical"${ssrRenderAttr("href", canonicalUrl.value)}${_scopeId}>`);
            } else {
              _push2(`<!---->`);
            }
            _push2(`<meta property="og:title"${ssrRenderAttr("content", seoTitle.value)}${_scopeId}><meta property="og:description"${ssrRenderAttr("content", seoDescription.value)}${_scopeId}><meta property="og:type" content="website"${_scopeId}>`);
            if (canonicalUrl.value) {
              _push2(`<meta property="og:url"${ssrRenderAttr("content", canonicalUrl.value)}${_scopeId}>`);
            } else {
              _push2(`<!---->`);
            }
            if (firstCourseImageUrl.value) {
              _push2(`<meta property="og:image"${ssrRenderAttr("content", firstCourseImageUrl.value)}${_scopeId}>`);
            } else {
              _push2(`<!---->`);
            }
            if (firstCourseImageUrl.value) {
              _push2(`<meta property="og:image:alt"${ssrRenderAttr("content", courseTitle.value)}${_scopeId}>`);
            } else {
              _push2(`<!---->`);
            }
            _push2(`<meta property="og:locale"${ssrRenderAttr("content", ogLocale.value)}${_scopeId}><meta name="twitter:card"${ssrRenderAttr("content", firstCourseImageUrl.value ? "summary_large_image" : "summary")}${_scopeId}><meta name="twitter:title"${ssrRenderAttr("content", seoTitle.value)}${_scopeId}><meta name="twitter:description"${ssrRenderAttr("content", seoDescription.value)}${_scopeId}>`);
            if (firstCourseImageUrl.value) {
              _push2(`<meta name="twitter:image"${ssrRenderAttr("content", firstCourseImageUrl.value)}${_scopeId}>`);
            } else {
              _push2(`<!---->`);
            }
            if (firstCourseImageUrl.value) {
              _push2(`<meta name="twitter:image:alt"${ssrRenderAttr("content", courseTitle.value)}${_scopeId}>`);
            } else {
              _push2(`<!---->`);
            }
            _push2(`<meta name="DC.Title"${ssrRenderAttr("content", seoTitle.value)}${_scopeId}><meta name="DC.Description"${ssrRenderAttr("content", seoDescription.value)}${_scopeId}>`);
            if (seoKeywords.value) {
              _push2(`<meta name="DC.Subject"${ssrRenderAttr("content", seoKeywords.value)}${_scopeId}>`);
            } else {
              _push2(`<!---->`);
            }
            _push2(`<meta name="DC.Type" content="Text"${_scopeId}><meta name="DC.Format" content="text/html"${_scopeId}><meta name="DC.Language"${ssrRenderAttr("content", contentLocale.value)}${_scopeId}>`);
            if (canonicalUrl.value) {
              _push2(`<meta name="DC.Identifier"${ssrRenderAttr("content", canonicalUrl.value)}${_scopeId}>`);
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
                content: "index, follow, max-image-preview:large"
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
                content: "website"
              }),
              canonicalUrl.value ? (openBlock(), createBlock("meta", {
                key: 2,
                property: "og:url",
                content: canonicalUrl.value
              }, null, 8, ["content"])) : createCommentVNode("", true),
              firstCourseImageUrl.value ? (openBlock(), createBlock("meta", {
                key: 3,
                property: "og:image",
                content: firstCourseImageUrl.value
              }, null, 8, ["content"])) : createCommentVNode("", true),
              firstCourseImageUrl.value ? (openBlock(), createBlock("meta", {
                key: 4,
                property: "og:image:alt",
                content: courseTitle.value
              }, null, 8, ["content"])) : createCommentVNode("", true),
              createVNode("meta", {
                property: "og:locale",
                content: ogLocale.value
              }, null, 8, ["content"]),
              createVNode("meta", {
                name: "twitter:card",
                content: firstCourseImageUrl.value ? "summary_large_image" : "summary"
              }, null, 8, ["content"]),
              createVNode("meta", {
                name: "twitter:title",
                content: seoTitle.value
              }, null, 8, ["content"]),
              createVNode("meta", {
                name: "twitter:description",
                content: seoDescription.value
              }, null, 8, ["content"]),
              firstCourseImageUrl.value ? (openBlock(), createBlock("meta", {
                key: 5,
                name: "twitter:image",
                content: firstCourseImageUrl.value
              }, null, 8, ["content"])) : createCommentVNode("", true),
              firstCourseImageUrl.value ? (openBlock(), createBlock("meta", {
                key: 6,
                name: "twitter:image:alt",
                content: courseTitle.value
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
                key: 7,
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
              createVNode("meta", {
                name: "DC.Language",
                content: contentLocale.value
              }, null, 8, ["content"]),
              canonicalUrl.value ? (openBlock(), createBlock("meta", {
                key: 8,
                name: "DC.Identifier",
                content: canonicalUrl.value
              }, null, 8, ["content"])) : createCommentVNode("", true),
              (openBlock(), createBlock(resolveDynamicComponent("script"), {
                type: "application/ld+json",
                textContent: toDisplayString(courseSchemaJson.value)
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
      _push(ssrRenderComponent(_sfc_main$5, {
        title: __props.title,
        "can-login": __props.canLogin,
        "can-register": __props.canRegister
      }, {
        default: withCtx((_, _push2, _parent2, _scopeId) => {
          var _a, _b;
          if (_push2) {
            _push2(ssrRenderComponent(_sfc_main$6, null, null, _parent2, _scopeId));
            _push2(`<main class="min-h-screen px-1 lg:px-6 max-w-full"${_scopeId}><div class="mx-auto tracking-wider pt-20 lg:pt-44"${_scopeId}><div class="ext-color w-full min-w-0 py-3 px-1 flex flex-col lg:flex-row gap-4 rounded-3xl border-2 border-slate-300 dark:border-slate-500"${_scopeId}>`);
            if (showLeft.value) {
              _push2(`<aside class="${ssrRenderClass([leftCollapsed.value ? "lg:w-6" : "lg:w-72", "shrink-0 transition-all duration-300 overflow-hidden"])}"${_scopeId}>`);
              _push2(ssrRenderComponent(_sfc_main$7, {
                "track-tree": trackTree.value,
                collapsed: leftCollapsed.value,
                onCollapsed: ($event) => leftCollapsed.value = $event
              }, null, _parent2, _scopeId));
              _push2(`</aside>`);
            } else {
              _push2(`<!---->`);
            }
            _push2(`<section class="w-full pb-6 slate-1"${_scopeId}><div class="mx-auto max-w-6xl"${_scopeId}><article class="selection:bg-red-400 selection:text-white" itemscope itemtype="https://schema.org/Course"${_scopeId}><meta itemprop="url"${ssrRenderAttr("content", canonicalUrl.value)}${_scopeId}><meta itemprop="inLanguage"${ssrRenderAttr("content", contentLocale.value)}${_scopeId}>`);
            if (courseData.value.published_at) {
              _push2(`<meta itemprop="datePublished"${ssrRenderAttr("content", courseData.value.published_at)}${_scopeId}>`);
            } else {
              _push2(`<!---->`);
            }
            if (schemaDuration.value) {
              _push2(`<meta itemprop="timeRequired"${ssrRenderAttr("content", schemaDuration.value)}${_scopeId}>`);
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
            _push2(`</li><li aria-hidden="true"${_scopeId}><span class="mx-2 breadcrumbs"${_scopeId}>/</span></li><li${_scopeId}>`);
            _push2(ssrRenderComponent(unref(Link), {
              href: _ctx.route("public.schoolCourses.index"),
              class: "breadcrumb-link hover:underline"
            }, {
              default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                if (_push3) {
                  _push3(`${ssrInterpolate(unref(t)("courses"))}`);
                } else {
                  return [
                    createTextVNode(toDisplayString(unref(t)("courses")), 1)
                  ];
                }
              }),
              _: 1
            }, _parent2, _scopeId));
            _push2(`</li><li aria-hidden="true"${_scopeId}><span class="mx-2 breadcrumbs"${_scopeId}>/</span></li><li class="breadcrumbs" aria-current="page"${_scopeId}>${ssrInterpolate(courseTitle.value)}</li></ol></nav><div class="flex flex-wrap items-center justify-center gap-3 title my-3"${_scopeId}><h1 class="text-2xl font-bold" itemprop="name"${_scopeId}>${ssrInterpolate(courseTitle.value)}</h1>`);
            if (courseData.value.views > 0) {
              _push2(`<div${ssrRenderAttr("title", unref(t)("views"))} class="flex items-center justify-center gap-1" itemprop="interactionStatistic" itemscope itemtype="https://schema.org/InteractionCounter"${_scopeId}><meta itemprop="interactionType" content="https://schema.org/ViewAction"${_scopeId}><meta itemprop="userInteractionCount"${ssrRenderAttr("content", courseData.value.views)}${_scopeId}><svg class="h-4 w-4 text-slate-600/85 dark:text-slate-200/85" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512" fill="currentColor" aria-hidden="true"${_scopeId}><path d="M569.354 231.631C512.97 135.949 407.81 72 288 72 168.14 72 63.004 135.994 6.646 231.631a47.999 47.999 0 0 0 0 48.739C63.031 376.051 168.19 440 288 440c119.86 0 224.996-63.994 281.354-159.631a47.997 47.997 0 0 0 0-48.738zM288 392c-102.556 0-192.091-54.701-240-136 44.157-74.933 123.677-127.27 216.162-135.007C273.958 131.078 280 144.83 280 160c0 30.928-25.072 56-56 56s-56-25.072-56-56l.001-.042C157.794 179.043 152 200.844 152 224c0 75.111 60.889 136 136 136s136-60.889 136-136c0-31.031-10.4-59.629-27.895-82.515C451.704 164.638 498.009 205.106 528 256c-47.908 81.299-137.444 136-240 136z"${_scopeId}></path></svg><span class="text-center text-sm text-gray-500"${_scopeId}>${ssrInterpolate(courseData.value.views)}</span></div>`);
            } else {
              _push2(`<!---->`);
            }
            _push2(`</div>`);
            if (courseSubtitle.value) {
              _push2(`<div class="mt-1 mb-3 text-sm subtitle text-center"${_scopeId}>${ssrInterpolate(courseSubtitle.value)}</div>`);
            } else {
              _push2(`<!---->`);
            }
            if (hasCourseImages.value) {
              _push2(`<div class="flex items-center justify-center"${_scopeId}><div class="w-full"${_scopeId}>`);
              _push2(ssrRenderComponent(ImageGalleryMain, {
                images: courseImages.value,
                alt: courseTitle.value,
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
            _push2(`<div class="mt-3 flex flex-wrap items-center justify-center gap-2 text-xs font-semibold text-slate-500 dark:text-slate-400"${_scopeId}>`);
            if (courseData.value.level) {
              _push2(`<div class="flex items-center justify-center gap-1 px-2 py-1 rounded-sm border border-slate-600 dark:border-slate-400"${_scopeId}><meta itemprop="educationalLevel"${ssrRenderAttr("content", translateLevel(courseData.value.level))}${_scopeId}><svg class="w-3 h-3 fill-current text-teal-700 dark:text-teal-300" viewBox="0 0 24 24" aria-hidden="true"${_scopeId}><path d="M12,24a1,1,0,0,1,0-2A10,10,0,0,0,12,2a1,1,0,0,1,0-2,12,12,0,0,1,0,24Z"${_scopeId}></path><path d="M10,17a1,1,0,0,1-.707-.293l-4-4a1,1,0,0,1,1.414-1.414L10,14.586l7.293-7.293a1,1,0,1,1,1.414,1.414l-8,8A1,1,0,0,1,10,17Z"${_scopeId}></path></svg> ${ssrInterpolate(unref(t)("level"))}: ${ssrInterpolate(translateLevel(courseData.value.level))}</div>`);
            } else {
              _push2(`<!---->`);
            }
            if (courseData.value.duration) {
              _push2(`<div class="flex items-center justify-center gap-1 px-2 py-1 rounded-sm border border-slate-600 dark:border-slate-400"${_scopeId}><svg class="w-3 h-3 text-blue-700 dark:text-blue-300" viewBox="0 0 24 24" aria-hidden="true"${_scopeId}><path class="fill-current" d="M22,13a1,1,0,0,1,0-2h1.949A12.006,12.006,0,0,0,13,.051V2a1,1,0,0,1-2,0V.051A12.006,12.006,0,0,0,.051,11H2a1,1,0,0,1,0,2H.051A12.006,12.006,0,0,0,11,23.949V22a1,1,0,0,1,2,0v1.949A12.006,12.006,0,0,0,23.949,13Zm-6,0H12a1,1,0,0,1-.832-.445l-4-6a1,1,0,1,1,1.664-1.11L12.535,11H16a1,1,0,0,1,0,2Z"${_scopeId}></path></svg> ${ssrInterpolate(unref(t)("duration"))}: ${ssrInterpolate(courseData.value.duration)} ${ssrInterpolate(unref(t)("minutes"))}</div>`);
            } else {
              _push2(`<!---->`);
            }
            if (courseData.value.rating_avg) {
              _push2(`<div class="flex items-center justify-center gap-1 px-2 py-1 rounded-sm border border-slate-600 dark:border-slate-400"${_scopeId}><svg viewBox="0 0 24 24" class="h-3 w-3 text-red-400 dark:text-red-300" aria-hidden="true"${_scopeId}><path class="fill-current" d="M12.746,1.464l3.11,6.3L22.81,8.776a.831.831,0,0,1,.461,1.418l-5.033,4.9,1.188,6.926a.832.832,0,0,1-1.207.877L12,19.632,5.78,22.9a.833.833,0,0,1-1.207-.878L5.761,15.1l-5.033-4.9a.831.831,0,0,1,.461-1.418L8.143,7.765l3.11-6.3A.833.833,0,0,1,12.746,1.464Z"${_scopeId}></path></svg> ${ssrInterpolate(unref(t)("rating"))}: ${ssrInterpolate(Number(courseData.value.rating_avg).toFixed(1))}</div>`);
            } else {
              _push2(`<!---->`);
            }
            if (courseData.value.students_count) {
              _push2(`<div class="flex items-center justify-center gap-1 px-2 py-1 rounded-sm border border-slate-600 dark:border-slate-400"${_scopeId}><svg class="h-4 w-4" viewBox="0 0 24 24" aria-hidden="true"${_scopeId}><path class="fill-current text-cyan-600" d="M18.974 8H22a2 2 0 012 2v6h-2v5a1 1 0 01-1 1h-2a1 1 0 01-1-1v-5h-2v-6a2 2 0 012-2h.974zM20 7a2 2 0 11-.001-3.999A2 2 0 0120 7zM2.974 8H6a2 2 0 012 2v6H6v5a1 1 0 01-1 1H3a1 1 0 01-1-1v-5H0v-6a2 2 0 012-2h.974zM4 7a2 2 0 11-.001-3.999A2 2 0 014 7z"${_scopeId}></path><path class="fill-current text-cyan-400" d="M12 6a3 3 0 110-6 3 3 0 010 6zm2 18h-4a1 1 0 01-1-1v-6H6v-6a3 3 0 013-3h6a3 3 0 013 3v6h-3v6a1 1 0 01-1 1z"${_scopeId}></path></svg> ${ssrInterpolate(unref(t)("students"))}: ${ssrInterpolate(courseData.value.students_count)}</div>`);
            } else {
              _push2(`<!---->`);
            }
            _push2(`</div>`);
            if (courseDescription.value) {
              _push2(`<div class="mt-4 text-sm subtitle" itemprop="description"${_scopeId}>${courseDescription.value ?? ""}</div>`);
            } else {
              _push2(`<!---->`);
            }
            _push2(`<div class="my-1 flex items-center justify-center"${_scopeId}><div itemprop="interactionStatistic" itemscope itemtype="https://schema.org/InteractionCounter"${_scopeId}><meta itemprop="interactionType" content="https://schema.org/LikeAction"${_scopeId}><meta itemprop="userInteractionCount"${ssrRenderAttr("content", courseData.value.likes_count || 0)}${_scopeId}>`);
            _push2(ssrRenderComponent(LikeButtonEntity, {
              "likes-count": courseData.value.likes_count || 0,
              "already-liked": courseData.value.already_liked || false,
              "route-name": "public.schoolCourses.like",
              "route-params": courseData.value.id,
              title: unref(t)("like")
            }, null, _parent2, _scopeId));
            _push2(`</div></div>`);
            if (hashtags.value.length) {
              _push2(`<div class="mt-4 flex flex-wrap items-center justify-center gap-2"${_scopeId}><!--[-->`);
              ssrRenderList(hashtags.value, (hashtag) => {
                _push2(ssrRenderComponent(unref(Link), {
                  key: hashtag.id,
                  href: _ctx.route("public.schoolHashtags.show", { slug: hashtag.slug }),
                  class: "rounded-sm px-2 py-1 text-xs font-semibold text-indigo-700 bg-indigo-50 dark:text-indigo-300 dark:bg-indigo-950/50 border border-indigo-400"
                }, {
                  default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                    var _a2, _b2;
                    if (_push3) {
                      _push3(` #${ssrInterpolate(((_a2 = hashtag.translation) == null ? void 0 : _a2.name) || hashtag.slug)}`);
                    } else {
                      return [
                        createTextVNode(" #" + toDisplayString(((_b2 = hashtag.translation) == null ? void 0 : _b2.name) || hashtag.slug), 1)
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
            if (instructorProfile.value) {
              _push2(`<div class="mt-4 flex items-center justify-center gap-3" itemprop="provider" itemscope itemtype="https://schema.org/Person"${_scopeId}>`);
              if (instructorImageUrl.value) {
                _push2(`<img${ssrRenderAttr("src", instructorImageUrl.value)}${ssrRenderAttr("alt", ((_a = instructorPrimaryImage.value) == null ? void 0 : _a.alt) || instructorName.value)} loading="lazy" decoding="async" itemprop="image" class="h-12 w-12 rounded-full object-cover ring-1 ring-gray-200 dark:ring-gray-700"${_scopeId}>`);
              } else {
                _push2(`<!---->`);
              }
              _push2(`<div class="flex flex-col items-start"${_scopeId}><span class="text-xs text-slate-500 dark:text-slate-400"${_scopeId}>${ssrInterpolate(unref(t)("instructor"))}</span><span class="text-sm font-semibold text-slate-700 dark:text-slate-300" itemprop="name"${_scopeId}>${ssrInterpolate(instructorName.value)}</span></div></div>`);
            } else {
              _push2(`<!---->`);
            }
            if (reviews.value.length) {
              _push2(`<section class="mt-8"${ssrRenderAttr("aria-labelledby", `course-reviews-${courseData.value.id}`)}${_scopeId}><h2${ssrRenderAttr("id", `course-reviews-${courseData.value.id}`)} class="mb-4 text-center text-lg font-semibold text-gray-700 dark:text-gray-300"${_scopeId}>${ssrInterpolate(unref(t)("reviews"))}</h2><div class="space-y-3"${_scopeId}><!--[-->`);
              ssrRenderList(reviews.value, (review) => {
                var _a2;
                _push2(`<article class="rounded-md border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-700 dark:bg-gray-900"${_scopeId}><div class="flex items-center justify-between gap-3"${_scopeId}><div class="text-sm font-semibold text-slate-700 dark:text-slate-300"${_scopeId}>${ssrInterpolate(((_a2 = review.user) == null ? void 0 : _a2.name) || unref(t)("user"))}</div>`);
                if (review.rating !== null && review.rating !== void 0) {
                  _push2(`<div class="text-xs text-slate-500 dark:text-slate-400"${_scopeId}>${ssrInterpolate(unref(t)("rating"))}: ${ssrInterpolate(review.rating)}</div>`);
                } else {
                  _push2(`<!---->`);
                }
                _push2(`</div>`);
                if (review.title) {
                  _push2(`<h3 class="mt-2 text-sm font-semibold text-slate-700 dark:text-slate-300"${_scopeId}>${ssrInterpolate(review.title)}</h3>`);
                } else {
                  _push2(`<!---->`);
                }
                if (review.body) {
                  _push2(`<div class="mt-2 text-sm text-slate-700 dark:text-slate-300"${_scopeId}>${ssrInterpolate(review.body)}</div>`);
                } else {
                  _push2(`<!---->`);
                }
                _push2(`</article>`);
              });
              _push2(`<!--]--></div></section>`);
            } else {
              _push2(`<!---->`);
            }
            _push2(ssrRenderComponent(_sfc_main$1, {
              modules: modulesList.value,
              cols: gridCols.value
            }, null, _parent2, _scopeId));
            if (relatedCourses.value.length) {
              _push2(`<section class="mt-8"${ssrRenderAttr("aria-labelledby", `related-courses-${courseData.value.id}`)}${_scopeId}><h2${ssrRenderAttr("id", `related-courses-${courseData.value.id}`)} class="mb-4 tracking-wide text-center font-semibold text-lg text-gray-700 dark:text-gray-300"${_scopeId}>${ssrInterpolate(unref(t)("relatedCourses"))}</h2>`);
              _push2(ssrRenderComponent(_sfc_main$8, {
                courses: relatedCourses.value,
                cols: gridCols.value
              }, null, _parent2, _scopeId));
              _push2(`</section>`);
            } else {
              _push2(`<!---->`);
            }
            _push2(`</article>`);
            _push2(ssrRenderComponent(_sfc_main$9, { videos: mainVideosList.value }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(SectionBanners, { banners: mainBannersList.value }, null, _parent2, _scopeId));
            _push2(`</div></section>`);
            if (showRight.value) {
              _push2(`<aside class="${ssrRenderClass([rightCollapsed.value ? "lg:w-6" : "lg:w-72", "shrink-0 transition-all duration-300 overflow-hidden"])}"${_scopeId}>`);
              _push2(ssrRenderComponent(_sfc_main$a, {
                collapsed: rightCollapsed.value,
                onCollapsed: ($event) => rightCollapsed.value = $event
              }, null, _parent2, _scopeId));
              _push2(`</aside>`);
            } else {
              _push2(`<!---->`);
            }
            _push2(`</div></div></main>`);
            _push2(ssrRenderComponent(_sfc_main$b, null, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(Progress, null, null, _parent2, _scopeId));
          } else {
            return [
              createVNode(_sfc_main$6),
              createVNode("main", { class: "min-h-screen px-1 lg:px-6 max-w-full" }, [
                createVNode("div", { class: "mx-auto tracking-wider pt-20 lg:pt-44" }, [
                  createVNode("div", { class: "ext-color w-full min-w-0 py-3 px-1 flex flex-col lg:flex-row gap-4 rounded-3xl border-2 border-slate-300 dark:border-slate-500" }, [
                    showLeft.value ? (openBlock(), createBlock("aside", {
                      key: 0,
                      class: ["shrink-0 transition-all duration-300 overflow-hidden", leftCollapsed.value ? "lg:w-6" : "lg:w-72"]
                    }, [
                      createVNode(_sfc_main$7, {
                        "track-tree": trackTree.value,
                        collapsed: leftCollapsed.value,
                        onCollapsed: ($event) => leftCollapsed.value = $event
                      }, null, 8, ["track-tree", "collapsed", "onCollapsed"])
                    ], 2)) : createCommentVNode("", true),
                    createVNode("section", { class: "w-full pb-6 slate-1" }, [
                      createVNode("div", { class: "mx-auto max-w-6xl" }, [
                        createVNode("article", {
                          class: "selection:bg-red-400 selection:text-white",
                          itemscope: "",
                          itemtype: "https://schema.org/Course"
                        }, [
                          createVNode("meta", {
                            itemprop: "url",
                            content: canonicalUrl.value
                          }, null, 8, ["content"]),
                          createVNode("meta", {
                            itemprop: "inLanguage",
                            content: contentLocale.value
                          }, null, 8, ["content"]),
                          courseData.value.published_at ? (openBlock(), createBlock("meta", {
                            key: 0,
                            itemprop: "datePublished",
                            content: courseData.value.published_at
                          }, null, 8, ["content"])) : createCommentVNode("", true),
                          schemaDuration.value ? (openBlock(), createBlock("meta", {
                            key: 1,
                            itemprop: "timeRequired",
                            content: schemaDuration.value
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
                              createVNode("li", { "aria-hidden": "true" }, [
                                createVNode("span", { class: "mx-2 breadcrumbs" }, "/")
                              ]),
                              createVNode("li", null, [
                                createVNode(unref(Link), {
                                  href: _ctx.route("public.schoolCourses.index"),
                                  class: "breadcrumb-link hover:underline"
                                }, {
                                  default: withCtx(() => [
                                    createTextVNode(toDisplayString(unref(t)("courses")), 1)
                                  ]),
                                  _: 1
                                }, 8, ["href"])
                              ]),
                              createVNode("li", { "aria-hidden": "true" }, [
                                createVNode("span", { class: "mx-2 breadcrumbs" }, "/")
                              ]),
                              createVNode("li", {
                                class: "breadcrumbs",
                                "aria-current": "page"
                              }, toDisplayString(courseTitle.value), 1)
                            ])
                          ]),
                          createVNode("div", { class: "flex flex-wrap items-center justify-center gap-3 title my-3" }, [
                            createVNode("h1", {
                              class: "text-2xl font-bold",
                              itemprop: "name"
                            }, toDisplayString(courseTitle.value), 1),
                            courseData.value.views > 0 ? (openBlock(), createBlock("div", {
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
                                content: courseData.value.views
                              }, null, 8, ["content"]),
                              (openBlock(), createBlock("svg", {
                                class: "h-4 w-4 text-slate-600/85 dark:text-slate-200/85",
                                xmlns: "http://www.w3.org/2000/svg",
                                viewBox: "0 0 576 512",
                                fill: "currentColor",
                                "aria-hidden": "true"
                              }, [
                                createVNode("path", { d: "M569.354 231.631C512.97 135.949 407.81 72 288 72 168.14 72 63.004 135.994 6.646 231.631a47.999 47.999 0 0 0 0 48.739C63.031 376.051 168.19 440 288 440c119.86 0 224.996-63.994 281.354-159.631a47.997 47.997 0 0 0 0-48.738zM288 392c-102.556 0-192.091-54.701-240-136 44.157-74.933 123.677-127.27 216.162-135.007C273.958 131.078 280 144.83 280 160c0 30.928-25.072 56-56 56s-56-25.072-56-56l.001-.042C157.794 179.043 152 200.844 152 224c0 75.111 60.889 136 136 136s136-60.889 136-136c0-31.031-10.4-59.629-27.895-82.515C451.704 164.638 498.009 205.106 528 256c-47.908 81.299-137.444 136-240 136z" })
                              ])),
                              createVNode("span", { class: "text-center text-sm text-gray-500" }, toDisplayString(courseData.value.views), 1)
                            ], 8, ["title"])) : createCommentVNode("", true)
                          ]),
                          courseSubtitle.value ? (openBlock(), createBlock("div", {
                            key: 2,
                            class: "mt-1 mb-3 text-sm subtitle text-center"
                          }, toDisplayString(courseSubtitle.value), 1)) : createCommentVNode("", true),
                          hasCourseImages.value ? (openBlock(), createBlock("div", {
                            key: 3,
                            class: "flex items-center justify-center"
                          }, [
                            createVNode("div", { class: "w-full" }, [
                              createVNode(ImageGalleryMain, {
                                images: courseImages.value,
                                alt: courseTitle.value,
                                itemprop: "image",
                                loading: "eager",
                                fetchpriority: "high",
                                "rounded-class": "rounded-lg",
                                "shadow-class": "shadow-lg shadow-gray-400 dark:shadow-gray-700",
                                "img-class": "w-full h-full object-cover"
                              }, null, 8, ["images", "alt"])
                            ])
                          ])) : createCommentVNode("", true),
                          createVNode("div", { class: "mt-3 flex flex-wrap items-center justify-center gap-2 text-xs font-semibold text-slate-500 dark:text-slate-400" }, [
                            courseData.value.level ? (openBlock(), createBlock("div", {
                              key: 0,
                              class: "flex items-center justify-center gap-1 px-2 py-1 rounded-sm border border-slate-600 dark:border-slate-400"
                            }, [
                              createVNode("meta", {
                                itemprop: "educationalLevel",
                                content: translateLevel(courseData.value.level)
                              }, null, 8, ["content"]),
                              (openBlock(), createBlock("svg", {
                                class: "w-3 h-3 fill-current text-teal-700 dark:text-teal-300",
                                viewBox: "0 0 24 24",
                                "aria-hidden": "true"
                              }, [
                                createVNode("path", { d: "M12,24a1,1,0,0,1,0-2A10,10,0,0,0,12,2a1,1,0,0,1,0-2,12,12,0,0,1,0,24Z" }),
                                createVNode("path", { d: "M10,17a1,1,0,0,1-.707-.293l-4-4a1,1,0,0,1,1.414-1.414L10,14.586l7.293-7.293a1,1,0,1,1,1.414,1.414l-8,8A1,1,0,0,1,10,17Z" })
                              ])),
                              createTextVNode(" " + toDisplayString(unref(t)("level")) + ": " + toDisplayString(translateLevel(courseData.value.level)), 1)
                            ])) : createCommentVNode("", true),
                            courseData.value.duration ? (openBlock(), createBlock("div", {
                              key: 1,
                              class: "flex items-center justify-center gap-1 px-2 py-1 rounded-sm border border-slate-600 dark:border-slate-400"
                            }, [
                              (openBlock(), createBlock("svg", {
                                class: "w-3 h-3 text-blue-700 dark:text-blue-300",
                                viewBox: "0 0 24 24",
                                "aria-hidden": "true"
                              }, [
                                createVNode("path", {
                                  class: "fill-current",
                                  d: "M22,13a1,1,0,0,1,0-2h1.949A12.006,12.006,0,0,0,13,.051V2a1,1,0,0,1-2,0V.051A12.006,12.006,0,0,0,.051,11H2a1,1,0,0,1,0,2H.051A12.006,12.006,0,0,0,11,23.949V22a1,1,0,0,1,2,0v1.949A12.006,12.006,0,0,0,23.949,13Zm-6,0H12a1,1,0,0,1-.832-.445l-4-6a1,1,0,1,1,1.664-1.11L12.535,11H16a1,1,0,0,1,0,2Z"
                                })
                              ])),
                              createTextVNode(" " + toDisplayString(unref(t)("duration")) + ": " + toDisplayString(courseData.value.duration) + " " + toDisplayString(unref(t)("minutes")), 1)
                            ])) : createCommentVNode("", true),
                            courseData.value.rating_avg ? (openBlock(), createBlock("div", {
                              key: 2,
                              class: "flex items-center justify-center gap-1 px-2 py-1 rounded-sm border border-slate-600 dark:border-slate-400"
                            }, [
                              (openBlock(), createBlock("svg", {
                                viewBox: "0 0 24 24",
                                class: "h-3 w-3 text-red-400 dark:text-red-300",
                                "aria-hidden": "true"
                              }, [
                                createVNode("path", {
                                  class: "fill-current",
                                  d: "M12.746,1.464l3.11,6.3L22.81,8.776a.831.831,0,0,1,.461,1.418l-5.033,4.9,1.188,6.926a.832.832,0,0,1-1.207.877L12,19.632,5.78,22.9a.833.833,0,0,1-1.207-.878L5.761,15.1l-5.033-4.9a.831.831,0,0,1,.461-1.418L8.143,7.765l3.11-6.3A.833.833,0,0,1,12.746,1.464Z"
                                })
                              ])),
                              createTextVNode(" " + toDisplayString(unref(t)("rating")) + ": " + toDisplayString(Number(courseData.value.rating_avg).toFixed(1)), 1)
                            ])) : createCommentVNode("", true),
                            courseData.value.students_count ? (openBlock(), createBlock("div", {
                              key: 3,
                              class: "flex items-center justify-center gap-1 px-2 py-1 rounded-sm border border-slate-600 dark:border-slate-400"
                            }, [
                              (openBlock(), createBlock("svg", {
                                class: "h-4 w-4",
                                viewBox: "0 0 24 24",
                                "aria-hidden": "true"
                              }, [
                                createVNode("path", {
                                  class: "fill-current text-cyan-600",
                                  d: "M18.974 8H22a2 2 0 012 2v6h-2v5a1 1 0 01-1 1h-2a1 1 0 01-1-1v-5h-2v-6a2 2 0 012-2h.974zM20 7a2 2 0 11-.001-3.999A2 2 0 0120 7zM2.974 8H6a2 2 0 012 2v6H6v5a1 1 0 01-1 1H3a1 1 0 01-1-1v-5H0v-6a2 2 0 012-2h.974zM4 7a2 2 0 11-.001-3.999A2 2 0 014 7z"
                                }),
                                createVNode("path", {
                                  class: "fill-current text-cyan-400",
                                  d: "M12 6a3 3 0 110-6 3 3 0 010 6zm2 18h-4a1 1 0 01-1-1v-6H6v-6a3 3 0 013-3h6a3 3 0 013 3v6h-3v6a1 1 0 01-1 1z"
                                })
                              ])),
                              createTextVNode(" " + toDisplayString(unref(t)("students")) + ": " + toDisplayString(courseData.value.students_count), 1)
                            ])) : createCommentVNode("", true)
                          ]),
                          courseDescription.value ? (openBlock(), createBlock("div", {
                            key: 4,
                            class: "mt-4 text-sm subtitle",
                            itemprop: "description",
                            innerHTML: courseDescription.value
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
                                content: courseData.value.likes_count || 0
                              }, null, 8, ["content"]),
                              createVNode(LikeButtonEntity, {
                                "likes-count": courseData.value.likes_count || 0,
                                "already-liked": courseData.value.already_liked || false,
                                "route-name": "public.schoolCourses.like",
                                "route-params": courseData.value.id,
                                title: unref(t)("like")
                              }, null, 8, ["likes-count", "already-liked", "route-params", "title"])
                            ])
                          ]),
                          hashtags.value.length ? (openBlock(), createBlock("div", {
                            key: 5,
                            class: "mt-4 flex flex-wrap items-center justify-center gap-2"
                          }, [
                            (openBlock(true), createBlock(Fragment, null, renderList(hashtags.value, (hashtag) => {
                              return openBlock(), createBlock(unref(Link), {
                                key: hashtag.id,
                                href: _ctx.route("public.schoolHashtags.show", { slug: hashtag.slug }),
                                class: "rounded-sm px-2 py-1 text-xs font-semibold text-indigo-700 bg-indigo-50 dark:text-indigo-300 dark:bg-indigo-950/50 border border-indigo-400"
                              }, {
                                default: withCtx(() => {
                                  var _a2;
                                  return [
                                    createTextVNode(" #" + toDisplayString(((_a2 = hashtag.translation) == null ? void 0 : _a2.name) || hashtag.slug), 1)
                                  ];
                                }),
                                _: 2
                              }, 1032, ["href"]);
                            }), 128))
                          ])) : createCommentVNode("", true),
                          instructorProfile.value ? (openBlock(), createBlock("div", {
                            key: 6,
                            class: "mt-4 flex items-center justify-center gap-3",
                            itemprop: "provider",
                            itemscope: "",
                            itemtype: "https://schema.org/Person"
                          }, [
                            instructorImageUrl.value ? (openBlock(), createBlock("img", {
                              key: 0,
                              src: instructorImageUrl.value,
                              alt: ((_b = instructorPrimaryImage.value) == null ? void 0 : _b.alt) || instructorName.value,
                              loading: "lazy",
                              decoding: "async",
                              itemprop: "image",
                              class: "h-12 w-12 rounded-full object-cover ring-1 ring-gray-200 dark:ring-gray-700"
                            }, null, 8, ["src", "alt"])) : createCommentVNode("", true),
                            createVNode("div", { class: "flex flex-col items-start" }, [
                              createVNode("span", { class: "text-xs text-slate-500 dark:text-slate-400" }, toDisplayString(unref(t)("instructor")), 1),
                              createVNode("span", {
                                class: "text-sm font-semibold text-slate-700 dark:text-slate-300",
                                itemprop: "name"
                              }, toDisplayString(instructorName.value), 1)
                            ])
                          ])) : createCommentVNode("", true),
                          reviews.value.length ? (openBlock(), createBlock("section", {
                            key: 7,
                            class: "mt-8",
                            "aria-labelledby": `course-reviews-${courseData.value.id}`
                          }, [
                            createVNode("h2", {
                              id: `course-reviews-${courseData.value.id}`,
                              class: "mb-4 text-center text-lg font-semibold text-gray-700 dark:text-gray-300"
                            }, toDisplayString(unref(t)("reviews")), 9, ["id"]),
                            createVNode("div", { class: "space-y-3" }, [
                              (openBlock(true), createBlock(Fragment, null, renderList(reviews.value, (review) => {
                                var _a2;
                                return openBlock(), createBlock("article", {
                                  key: review.id,
                                  class: "rounded-md border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-700 dark:bg-gray-900"
                                }, [
                                  createVNode("div", { class: "flex items-center justify-between gap-3" }, [
                                    createVNode("div", { class: "text-sm font-semibold text-slate-700 dark:text-slate-300" }, toDisplayString(((_a2 = review.user) == null ? void 0 : _a2.name) || unref(t)("user")), 1),
                                    review.rating !== null && review.rating !== void 0 ? (openBlock(), createBlock("div", {
                                      key: 0,
                                      class: "text-xs text-slate-500 dark:text-slate-400"
                                    }, toDisplayString(unref(t)("rating")) + ": " + toDisplayString(review.rating), 1)) : createCommentVNode("", true)
                                  ]),
                                  review.title ? (openBlock(), createBlock("h3", {
                                    key: 0,
                                    class: "mt-2 text-sm font-semibold text-slate-700 dark:text-slate-300"
                                  }, toDisplayString(review.title), 1)) : createCommentVNode("", true),
                                  review.body ? (openBlock(), createBlock("div", {
                                    key: 1,
                                    class: "mt-2 text-sm text-slate-700 dark:text-slate-300"
                                  }, toDisplayString(review.body), 1)) : createCommentVNode("", true)
                                ]);
                              }), 128))
                            ])
                          ], 8, ["aria-labelledby"])) : createCommentVNode("", true),
                          createVNode(_sfc_main$1, {
                            modules: modulesList.value,
                            cols: gridCols.value
                          }, null, 8, ["modules", "cols"]),
                          relatedCourses.value.length ? (openBlock(), createBlock("section", {
                            key: 8,
                            class: "mt-8",
                            "aria-labelledby": `related-courses-${courseData.value.id}`
                          }, [
                            createVNode("h2", {
                              id: `related-courses-${courseData.value.id}`,
                              class: "mb-4 tracking-wide text-center font-semibold text-lg text-gray-700 dark:text-gray-300"
                            }, toDisplayString(unref(t)("relatedCourses")), 9, ["id"]),
                            createVNode(_sfc_main$8, {
                              courses: relatedCourses.value,
                              cols: gridCols.value
                            }, null, 8, ["courses", "cols"])
                          ], 8, ["aria-labelledby"])) : createCommentVNode("", true)
                        ]),
                        createVNode(_sfc_main$9, { videos: mainVideosList.value }, null, 8, ["videos"]),
                        createVNode(SectionBanners, { banners: mainBannersList.value }, null, 8, ["banners"])
                      ])
                    ]),
                    showRight.value ? (openBlock(), createBlock("aside", {
                      key: 1,
                      class: ["shrink-0 transition-all duration-300 overflow-hidden", rightCollapsed.value ? "lg:w-6" : "lg:w-72"]
                    }, [
                      createVNode(_sfc_main$a, {
                        collapsed: rightCollapsed.value,
                        onCollapsed: ($event) => rightCollapsed.value = $event
                      }, null, 8, ["collapsed", "onCollapsed"])
                    ], 2)) : createCommentVNode("", true)
                  ])
                ])
              ]),
              createVNode(_sfc_main$b),
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
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Pages/Public/Default/School/SchoolCourses/Show.vue");
  return _sfc_setup ? _sfc_setup(props, ctx) : void 0;
};
export {
  _sfc_main as default
};
