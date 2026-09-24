import { computed, ref, unref, withCtx, createVNode, createBlock, createCommentVNode, toDisplayString, openBlock, createTextVNode, Fragment, useSSRContext } from "vue";
import { ssrRenderComponent, ssrInterpolate, ssrRenderAttr, ssrRenderClass } from "vue/server-renderer";
import { usePage, Head, Link } from "@inertiajs/vue3";
import { useI18n } from "vue-i18n";
import { _ as _sfc_main$1 } from "./DefaultLayout-6zxGxPtK.js";
import { _ as _sfc_main$2, a as _sfc_main$6, P as Progress } from "./Progress-CyfOKcqP.js";
import { _ as _sfc_main$3, a as _sfc_main$5 } from "./RightSidebarSchool-DlAegojf.js";
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
    locale: {
      type: String,
      default: ""
    },
    title: {
      type: String,
      default: ""
    },
    canLogin: {
      type: Boolean,
      default: false
    },
    canRegister: {
      type: Boolean,
      default: false
    },
    assignment: {
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
    var _a;
    const { t } = useI18n();
    const page = usePage();
    const props = __props;
    const siteSettings = ((_a = page.props) == null ? void 0 : _a.siteSettings) || {};
    const showLeft = computed(() => {
      return !(siteSettings == null ? void 0 : siteSettings.ViewLeftColumn) || siteSettings.ViewLeftColumn === "true";
    });
    const showRight = computed(() => {
      return !(siteSettings == null ? void 0 : siteSettings.ViewRightColumn) || siteSettings.ViewRightColumn === "true";
    });
    const leftCollapsed = ref(false);
    const rightCollapsed = ref(false);
    const normalizeList = (value) => {
      if (Array.isArray(value)) {
        return value;
      }
      if (Array.isArray(value == null ? void 0 : value.data)) {
        return value.data;
      }
      return [];
    };
    const trackTree = computed(() => {
      return Array.isArray(props.trackTree) ? props.trackTree : [];
    });
    const mainVideosList = computed(() => {
      return normalizeList(props.mainVideos);
    });
    const mainBannersList = computed(() => {
      return normalizeList(props.mainBanners);
    });
    const assignmentData = computed(() => {
      return props.assignment ?? {};
    });
    const assignmentTranslation = computed(() => {
      var _a2;
      return ((_a2 = assignmentData.value) == null ? void 0 : _a2.translation) || null;
    });
    const assignmentTitle = computed(() => {
      var _a2;
      return ((_a2 = assignmentTranslation.value) == null ? void 0 : _a2.title) || "";
    });
    const assignmentSubtitle = computed(() => {
      var _a2;
      return ((_a2 = assignmentTranslation.value) == null ? void 0 : _a2.subtitle) || "";
    });
    const assignmentShort = computed(() => {
      var _a2;
      return ((_a2 = assignmentTranslation.value) == null ? void 0 : _a2.short) || "";
    });
    const assignmentDescription = computed(() => {
      var _a2;
      return ((_a2 = assignmentTranslation.value) == null ? void 0 : _a2.description) || "";
    });
    const assignmentInstructions = computed(() => {
      var _a2;
      return ((_a2 = assignmentTranslation.value) == null ? void 0 : _a2.instructions) || "";
    });
    const seoTitle = computed(() => {
      var _a2;
      return ((_a2 = assignmentTranslation.value) == null ? void 0 : _a2.meta_title) || assignmentTitle.value;
    });
    const seoKeywords = computed(() => {
      var _a2;
      return ((_a2 = assignmentTranslation.value) == null ? void 0 : _a2.meta_keywords) || "";
    });
    const seoDescription = computed(() => {
      var _a2;
      return ((_a2 = assignmentTranslation.value) == null ? void 0 : _a2.meta_description) || assignmentShort.value || assignmentSubtitle.value;
    });
    const resolvedLocale = computed(() => {
      var _a2;
      return ((_a2 = assignmentTranslation.value) == null ? void 0 : _a2.locale) || props.locale || "";
    });
    const assignmentImages = computed(() => {
      var _a2;
      return Array.isArray((_a2 = assignmentData.value) == null ? void 0 : _a2.images) ? assignmentData.value.images : [];
    });
    const firstAssignmentImage = computed(() => {
      return assignmentImages.value[0] || null;
    });
    const hasAssignmentImages = computed(() => {
      return assignmentImages.value.length > 0;
    });
    const firstAssignmentImageUrl = computed(() => {
      const image = firstAssignmentImage.value;
      return (image == null ? void 0 : image.webp_url) || (image == null ? void 0 : image.url) || (image == null ? void 0 : image.image_url) || (image == null ? void 0 : image.thumb_url) || "";
    });
    const courseData = computed(() => {
      var _a2;
      return ((_a2 = assignmentData.value) == null ? void 0 : _a2.course) || null;
    });
    const moduleData = computed(() => {
      var _a2;
      return ((_a2 = assignmentData.value) == null ? void 0 : _a2.module) || null;
    });
    const lessonData = computed(() => {
      var _a2;
      return ((_a2 = assignmentData.value) == null ? void 0 : _a2.lesson) || null;
    });
    const instructorData = computed(() => {
      var _a2;
      return ((_a2 = assignmentData.value) == null ? void 0 : _a2.instructor) || null;
    });
    const courseTitle = computed(() => {
      var _a2, _b;
      return ((_b = (_a2 = courseData.value) == null ? void 0 : _a2.translation) == null ? void 0 : _b.title) || t("course");
    });
    const moduleTitle = computed(() => {
      var _a2, _b;
      return ((_b = (_a2 = moduleData.value) == null ? void 0 : _a2.translation) == null ? void 0 : _b.title) || t("module");
    });
    const lessonTitle = computed(() => {
      var _a2, _b;
      return ((_b = (_a2 = lessonData.value) == null ? void 0 : _a2.translation) == null ? void 0 : _b.title) || t("lesson");
    });
    const instructorName = computed(() => {
      var _a2, _b, _c, _d;
      return ((_b = (_a2 = instructorData.value) == null ? void 0 : _a2.user) == null ? void 0 : _b.name) || ((_d = (_c = instructorData.value) == null ? void 0 : _c.translation) == null ? void 0 : _d.title) || t("instructor");
    });
    const instructorImages = computed(() => {
      var _a2;
      return Array.isArray((_a2 = instructorData.value) == null ? void 0 : _a2.images) ? instructorData.value.images : [];
    });
    const instructorPrimaryImage = computed(() => {
      if (!instructorImages.value.length) {
        return null;
      }
      return [...instructorImages.value].sort((a, b) => {
        var _a2, _b;
        const aOrder = Number(
          (a == null ? void 0 : a.order) ?? ((_a2 = a == null ? void 0 : a.pivot) == null ? void 0 : _a2.order) ?? 999999
        );
        const bOrder = Number(
          (b == null ? void 0 : b.order) ?? ((_b = b == null ? void 0 : b.pivot) == null ? void 0 : _b.order) ?? 999999
        );
        return aOrder - bOrder;
      })[0];
    });
    const instructorImageUrl = computed(() => {
      const image = instructorPrimaryImage.value;
      return (image == null ? void 0 : image.webp_url) || (image == null ? void 0 : image.url) || (image == null ? void 0 : image.image_url) || (image == null ? void 0 : image.thumb_url) || null;
    });
    const translateGradingType = (value) => {
      const map = {
        manual: t("gradingManual"),
        auto: t("gradingAuto")
      };
      return map[value] || value || "—";
    };
    const translateVisibility = (value) => {
      const map = {
        public: t("public"),
        enrolled: t("enrolled"),
        private: t("private")
      };
      return map[value] || value || "—";
    };
    const translateStatus = (value) => {
      const map = {
        draft: t("statusDraft"),
        published: t("statusPublished"),
        archived: t("statusArchived")
      };
      return map[value] || value || "—";
    };
    const formatDate = (value) => {
      if (!value) {
        return null;
      }
      const date = new Date(value);
      if (Number.isNaN(date.getTime())) {
        return value;
      }
      return new Intl.DateTimeFormat(
        resolvedLocale.value || void 0,
        {
          day: "2-digit",
          month: "2-digit",
          year: "numeric",
          hour: "2-digit",
          minute: "2-digit"
        }
      ).format(date);
    };
    return (_ctx, _push, _parent, _attrs) => {
      _push(`<!--[-->`);
      _push(ssrRenderComponent(unref(Head), null, {
        default: withCtx((_, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(`<title${_scopeId}>${ssrInterpolate(seoTitle.value)}</title><meta name="title"${ssrRenderAttr("content", seoTitle.value)}${_scopeId}><meta name="description"${ssrRenderAttr("content", seoDescription.value)}${_scopeId}><meta name="keywords"${ssrRenderAttr("content", seoKeywords.value)}${_scopeId}><meta name="viewport" content="width=device-width, initial-scale=1"${_scopeId}><meta property="og:title"${ssrRenderAttr("content", seoTitle.value)}${_scopeId}><meta property="og:description"${ssrRenderAttr("content", seoDescription.value)}${_scopeId}><meta property="og:type" content="website"${_scopeId}><meta property="og:url"${ssrRenderAttr("content", `/school/assignments/${assignmentData.value.slug || ""}`)}${_scopeId}><meta property="og:image"${ssrRenderAttr("content", firstAssignmentImageUrl.value)}${_scopeId}>`);
            if (resolvedLocale.value) {
              _push2(`<meta property="og:locale"${ssrRenderAttr("content", resolvedLocale.value)}${_scopeId}>`);
            } else {
              _push2(`<!---->`);
            }
            _push2(`<meta name="twitter:card" content="summary_large_image"${_scopeId}><meta name="twitter:title"${ssrRenderAttr("content", seoTitle.value)}${_scopeId}><meta name="twitter:description"${ssrRenderAttr("content", seoDescription.value)}${_scopeId}><meta name="twitter:image"${ssrRenderAttr("content", firstAssignmentImageUrl.value)}${_scopeId}>`);
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
              createVNode("meta", {
                name: "keywords",
                content: seoKeywords.value
              }, null, 8, ["content"]),
              createVNode("meta", {
                name: "viewport",
                content: "width=device-width, initial-scale=1"
              }),
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
              createVNode("meta", {
                property: "og:url",
                content: `/school/assignments/${assignmentData.value.slug || ""}`
              }, null, 8, ["content"]),
              createVNode("meta", {
                property: "og:image",
                content: firstAssignmentImageUrl.value
              }, null, 8, ["content"]),
              resolvedLocale.value ? (openBlock(), createBlock("meta", {
                key: 0,
                property: "og:locale",
                content: resolvedLocale.value
              }, null, 8, ["content"])) : createCommentVNode("", true),
              createVNode("meta", {
                name: "twitter:card",
                content: "summary_large_image"
              }),
              createVNode("meta", {
                name: "twitter:title",
                content: seoTitle.value
              }, null, 8, ["content"]),
              createVNode("meta", {
                name: "twitter:description",
                content: seoDescription.value
              }, null, 8, ["content"]),
              createVNode("meta", {
                name: "twitter:image",
                content: firstAssignmentImageUrl.value
              }, null, 8, ["content"])
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
          var _a2, _b, _c, _d, _e, _f, _g, _h, _i, _j, _k, _l, _m, _n, _o, _p;
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
            _push2(`<section class="w-full pb-6 slate-1 min-w-0"${_scopeId}><div class="mx-auto max-w-6xl"${_scopeId}><article class="selection:bg-red-400 selection:text-white"${_scopeId}><nav class="text-sm mb-3" aria-label="Breadcrumb"${_scopeId}><ol class="flex flex-wrap items-center font-semibold"${_scopeId}><li${_scopeId}>`);
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
            _push2(`</li>`);
            if ((_a2 = courseData.value) == null ? void 0 : _a2.slug) {
              _push2(`<!--[--><li${_scopeId}><span class="mx-2 breadcrumbs"${_scopeId}>/</span></li><li${_scopeId}>`);
              _push2(ssrRenderComponent(unref(Link), {
                href: _ctx.route(
                  "public.schoolCourses.show",
                  {
                    slug: courseData.value.slug
                  }
                ),
                class: "breadcrumb-link hover:underline"
              }, {
                default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                  if (_push3) {
                    _push3(`${ssrInterpolate(courseTitle.value)}`);
                  } else {
                    return [
                      createTextVNode(toDisplayString(courseTitle.value), 1)
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
                href: _ctx.route(
                  "public.schoolModules.show",
                  {
                    courseSlug: courseData.value.slug,
                    slug: moduleData.value.slug
                  }
                ),
                class: "breadcrumb-link hover:underline"
              }, {
                default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                  if (_push3) {
                    _push3(`${ssrInterpolate(moduleTitle.value)}`);
                  } else {
                    return [
                      createTextVNode(toDisplayString(moduleTitle.value), 1)
                    ];
                  }
                }),
                _: 1
              }, _parent2, _scopeId));
              _push2(`</li><!--]-->`);
            } else {
              _push2(`<!---->`);
            }
            if ((_d = lessonData.value) == null ? void 0 : _d.slug) {
              _push2(`<!--[--><li${_scopeId}><span class="mx-2 breadcrumbs"${_scopeId}>/</span></li><li${_scopeId}>`);
              _push2(ssrRenderComponent(unref(Link), {
                href: _ctx.route(
                  "public.schoolLessons.show",
                  {
                    slug: lessonData.value.slug
                  }
                ),
                class: "breadcrumb-link hover:underline"
              }, {
                default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                  if (_push3) {
                    _push3(`${ssrInterpolate(lessonTitle.value)}`);
                  } else {
                    return [
                      createTextVNode(toDisplayString(lessonTitle.value), 1)
                    ];
                  }
                }),
                _: 1
              }, _parent2, _scopeId));
              _push2(`</li><!--]-->`);
            } else {
              _push2(`<!---->`);
            }
            _push2(`<li${_scopeId}><span class="mx-2 breadcrumbs"${_scopeId}>/</span></li><li${_scopeId}>`);
            _push2(ssrRenderComponent(unref(Link), {
              href: _ctx.route(
                "public.schoolAssignments.index"
              ),
              class: "breadcrumb-link hover:underline"
            }, {
              default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                if (_push3) {
                  _push3(`${ssrInterpolate(unref(t)("assignments"))}`);
                } else {
                  return [
                    createTextVNode(toDisplayString(unref(t)("assignments")), 1)
                  ];
                }
              }),
              _: 1
            }, _parent2, _scopeId));
            _push2(`</li><li${_scopeId}><span class="mx-2 breadcrumbs"${_scopeId}>/</span></li><li class="breadcrumbs"${_scopeId}>${ssrInterpolate(assignmentTitle.value)}</li></ol></nav><div class="flex items-center justify-center gap-3 title my-3"${_scopeId}><svg class="shrink-0 h-5 w-5 text-slate-600/85 dark:text-slate-200/85" fill="currentColor" viewBox="0 0 24 24"${_scopeId}><path d="M15,18v2H9v-2H1v5c0,0.552,0.448,1,1,1h20c0.552,0,1-0.448,1-1v-5H15z"${_scopeId}></path><path d="M23,4h-6V1c0-0.552-0.448-1-1-1H8C7.448,0,7,0.448,7,1v3H1C0.448,4,0,4.448,0,5v10c0,0.552,0.448,1,1,1h8v-3 h6v3h8c0.552,0,1-0.448,1-1V5C24,4.448,23.552,4,23,4z M15,4H9V2h6V4z"${_scopeId}></path></svg><h1 class="text-2xl font-bold"${_scopeId}>${ssrInterpolate(assignmentTitle.value)}</h1></div>`);
            if (assignmentSubtitle.value) {
              _push2(`<div class="mt-1 mb-3 text-sm subtitle text-center"${_scopeId}>${ssrInterpolate(assignmentSubtitle.value)}</div>`);
            } else {
              _push2(`<!---->`);
            }
            if (hasAssignmentImages.value) {
              _push2(`<div class="flex items-center justify-center"${_scopeId}><div class="w-full"${_scopeId}>`);
              _push2(ssrRenderComponent(ImageGalleryMain, {
                images: assignmentImages.value,
                alt: assignmentTitle.value,
                "rounded-class": "rounded-lg",
                "shadow-class": "shadow-lg shadow-gray-400\n                                                      dark:shadow-gray-700",
                "img-class": "w-full h-full object-cover"
              }, null, _parent2, _scopeId));
              _push2(`</div></div>`);
            } else {
              _push2(`<!---->`);
            }
            _push2(`<div class="my-4 flex flex-wrap items-center justify-center gap-3 text-sm text-slate-600 dark:text-slate-300"${_scopeId}>`);
            if (instructorData.value) {
              _push2(`<span class="rounded-sm border border-gray-400 flex items-center justify-center gap-2 px-3 py-1"${_scopeId}>`);
              if (instructorImageUrl.value) {
                _push2(`<img${ssrRenderAttr("src", instructorImageUrl.value)}${ssrRenderAttr("alt", instructorName.value)} loading="lazy" class="h-6 w-6 rounded-full object-cover ring-1 ring-gray-200 dark:ring-gray-700"${_scopeId}>`);
              } else {
                _push2(`<svg class="h-4 w-4 text-violet-600/85 dark:text-violet-200/85" fill="currentColor" viewBox="0 0 640 512"${_scopeId}><path d="M622.34 153.2L343.4 67.5c-15.2-4.67-31.6-4.67-46.79 0L17.66 153.2c-23.54 7.23-23.54 38.36 0 45.59l48.63 14.94c-10.67 13.19-17.23 29.28-17.88 46.9C38.78 266.15 32 276.11 32 288c0 10.78 5.68 19.85 13.86 25.65L20.33 428.53C18.11 438.52 25.71 448 35.94 448h56.11c10.24 0 17.84-9.48 15.62-19.47L82.14 313.65C90.32 307.85 96 298.78 96 288c0-11.57-6.47-21.25-15.66-26.87.76-15.02 8.44-28.3 20.69-36.72L296.6 284.5c9.06 2.78 26.44 6.25 46.79 0l278.95-85.7c23.55-7.24 23.55-38.36 0-45.6zM352.79 315.09c-28.53 8.76-52.84 3.92-65.59 0l-145.02-44.55L128 384c0 35.35 85.96 64 192 64s192-28.65 192-64l-14.18-113.47-145.03 44.56z"${_scopeId}></path></svg>`);
              }
              _push2(`<span${_scopeId}>${ssrInterpolate(unref(t)("instructor"))}: ${ssrInterpolate(instructorName.value)}</span></span>`);
            } else {
              _push2(`<!---->`);
            }
            if ((_e = courseData.value) == null ? void 0 : _e.slug) {
              _push2(ssrRenderComponent(unref(Link), {
                href: _ctx.route(
                  "public.schoolCourses.show",
                  {
                    slug: courseData.value.slug
                  }
                ),
                class: "rounded-sm border border-gray-400 flex items-center justify-center gap-1 px-3 py-1 hover:text-blue-600 dark:hover:bg-blue-400"
              }, {
                default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                  if (_push3) {
                    _push3(`<svg class="shrink-0 h-3 w-3 text-sky-600/85 dark:text-sky-200/85" fill="currentColor" viewBox="0 0 448 512"${_scopeId2}><path d="M318.38 208h-39.09c-1.49 27.03-6.54 51.35-14.21 70.41 27.71-13.24 48.02-39.19 53.3-70.41zm0-32c-5.29-31.22-25.59-57.17-53.3-70.41 7.68 19.06 12.72 43.38 14.21 70.41h39.09zM224 97.31c-7.69 7.45-20.77 34.42-23.43 78.69h46.87c-2.67-44.26-15.75-71.24-23.44-78.69zm-41.08 8.28c-27.71 13.24-48.02 39.19-53.3 70.41h39.09c1.49-27.03 6.53-51.35 14.21-70.41zm0 172.82c-7.68-19.06-12.72-43.38-14.21-70.41h-39.09c5.28 31.22 25.59 57.17 53.3 70.41zM247.43 208h-46.87c2.66 44.26 15.74 71.24 23.43 78.69 7.7-7.45 20.78-34.43 23.44-78.69zM448 358.4V25.6c0-16-9.6-25.6-25.6-25.6H96C41.6 0 0 41.6 0 96v320c0 54.4 41.6 96 96 96h326.4c12.8 0 25.6-9.6 25.6-25.6v-16c0-6.4-3.2-12.8-9.6-19.2-3.2-16-3.2-60.8 0-73.6 6.4-3.2 9.6-9.6 9.6-19.2zM224 64c70.69 0 128 57.31 128 128s-57.31 128-128 128S96 262.69 96 192 153.31 64 224 64zm160 384H96c-19.2 0-32-12.8-32-32s16-32 32-32h288v64z"${_scopeId2}></path></svg> ${ssrInterpolate(unref(t)("course"))}: ${ssrInterpolate(courseTitle.value)}`);
                  } else {
                    return [
                      (openBlock(), createBlock("svg", {
                        class: "shrink-0 h-3 w-3 text-sky-600/85 dark:text-sky-200/85",
                        fill: "currentColor",
                        viewBox: "0 0 448 512"
                      }, [
                        createVNode("path", { d: "M318.38 208h-39.09c-1.49 27.03-6.54 51.35-14.21 70.41 27.71-13.24 48.02-39.19 53.3-70.41zm0-32c-5.29-31.22-25.59-57.17-53.3-70.41 7.68 19.06 12.72 43.38 14.21 70.41h39.09zM224 97.31c-7.69 7.45-20.77 34.42-23.43 78.69h46.87c-2.67-44.26-15.75-71.24-23.44-78.69zm-41.08 8.28c-27.71 13.24-48.02 39.19-53.3 70.41h39.09c1.49-27.03 6.53-51.35 14.21-70.41zm0 172.82c-7.68-19.06-12.72-43.38-14.21-70.41h-39.09c5.28 31.22 25.59 57.17 53.3 70.41zM247.43 208h-46.87c2.66 44.26 15.74 71.24 23.43 78.69 7.7-7.45 20.78-34.43 23.44-78.69zM448 358.4V25.6c0-16-9.6-25.6-25.6-25.6H96C41.6 0 0 41.6 0 96v320c0 54.4 41.6 96 96 96h326.4c12.8 0 25.6-9.6 25.6-25.6v-16c0-6.4-3.2-12.8-9.6-19.2-3.2-16-3.2-60.8 0-73.6 6.4-3.2 9.6-9.6 9.6-19.2zM224 64c70.69 0 128 57.31 128 128s-57.31 128-128 128S96 262.69 96 192 153.31 64 224 64zm160 384H96c-19.2 0-32-12.8-32-32s16-32 32-32h288v64z" })
                      ])),
                      createTextVNode(" " + toDisplayString(unref(t)("course")) + ": " + toDisplayString(courseTitle.value), 1)
                    ];
                  }
                }),
                _: 1
              }, _parent2, _scopeId));
            } else {
              _push2(`<!---->`);
            }
            if (((_f = courseData.value) == null ? void 0 : _f.slug) && ((_g = moduleData.value) == null ? void 0 : _g.slug)) {
              _push2(ssrRenderComponent(unref(Link), {
                href: _ctx.route(
                  "public.schoolModules.show",
                  {
                    courseSlug: courseData.value.slug,
                    slug: moduleData.value.slug
                  }
                ),
                class: "rounded-sm border border-gray-400 flex items-center justify-center gap-1 px-3 py-1 hover:text-blue-600 dark:hover:bg-blue-400"
              }, {
                default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                  if (_push3) {
                    _push3(`<svg class="shrink-0 h-3 w-3 text-teal-600/85 dark:text-teal-200/85" fill="currentColor" viewBox="0 0 24 24"${_scopeId2}><rect x="1" y="1" width="10" height="10" rx="2"${_scopeId2}></rect><path class="fill-current text-teal-400" d="M23.428,4.618,19.381.572h0a1.957,1.957,0,0,0-2.762,0L12.572,4.618a1.959,1.959,0,0,0,0,2.764l4.047,4.047a1.957,1.957,0,0,0,2.762,0l4.047-4.046A1.959,1.959,0,0,0,23.428,4.618Z"${_scopeId2}></path><rect x="13" y="13" width="10" height="10" rx="2"${_scopeId2}></rect><rect x="1" y="13" width="10" height="10" rx="2"${_scopeId2}></rect></svg> ${ssrInterpolate(unref(t)("module"))}: ${ssrInterpolate(moduleTitle.value)}`);
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
                          d: "M23.428,4.618,19.381.572h0a1.957,1.957,0,0,0-2.762,0L12.572,4.618a1.959,1.959,0,0,0,0,2.764l4.047,4.047a1.957,1.957,0,0,0,2.762,0l4.047-4.046A1.959,1.959,0,0,0,23.428,4.618Z"
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
                      createTextVNode(" " + toDisplayString(unref(t)("module")) + ": " + toDisplayString(moduleTitle.value), 1)
                    ];
                  }
                }),
                _: 1
              }, _parent2, _scopeId));
            } else {
              _push2(`<!---->`);
            }
            if ((_h = lessonData.value) == null ? void 0 : _h.slug) {
              _push2(ssrRenderComponent(unref(Link), {
                href: _ctx.route(
                  "public.schoolLessons.show",
                  {
                    slug: lessonData.value.slug
                  }
                ),
                class: "rounded-sm border border-gray-400 flex items-center justify-center gap-1 px-3 py-1 hover:text-blue-600 dark:hover:bg-blue-400"
              }, {
                default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                  if (_push3) {
                    _push3(`<svg class="shrink-0 h-3 w-3 text-indigo-600/85 dark:text-indigo-300/85" fill="currentColor" viewBox="0 0 24 24"${_scopeId2}><path d="M7 4.75h8.5A2.75 2.75 0 0 1 18.25 7.5v9A2.75 2.75 0 0 1 15.5 19.25H7A2.25 2.25 0 0 1 4.75 17V7A2.25 2.25 0 0 1 7 4.75Z"${_scopeId2}></path><path d="M8.5 8.5h6M8.5 12h6M8.5 15.5h4" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"${_scopeId2}></path></svg> ${ssrInterpolate(unref(t)("lesson"))}: ${ssrInterpolate(lessonTitle.value)}`);
                  } else {
                    return [
                      (openBlock(), createBlock("svg", {
                        class: "shrink-0 h-3 w-3 text-indigo-600/85 dark:text-indigo-300/85",
                        fill: "currentColor",
                        viewBox: "0 0 24 24"
                      }, [
                        createVNode("path", { d: "M7 4.75h8.5A2.75 2.75 0 0 1 18.25 7.5v9A2.75 2.75 0 0 1 15.5 19.25H7A2.25 2.25 0 0 1 4.75 17V7A2.25 2.25 0 0 1 7 4.75Z" }),
                        createVNode("path", {
                          d: "M8.5 8.5h6M8.5 12h6M8.5 15.5h4",
                          fill: "none",
                          stroke: "currentColor",
                          "stroke-width": "1.5",
                          "stroke-linecap": "round"
                        })
                      ])),
                      createTextVNode(" " + toDisplayString(unref(t)("lesson")) + ": " + toDisplayString(lessonTitle.value), 1)
                    ];
                  }
                }),
                _: 1
              }, _parent2, _scopeId));
            } else {
              _push2(`<!---->`);
            }
            _push2(`<span class="rounded-sm border border-gray-400 flex items-center justify-center gap-1 px-3 py-1"${_scopeId}><svg class="h-3 w-3 text-violet-600 dark:text-violet-300" fill="currentColor" viewBox="0 0 24 24"${_scopeId}><path d="M12 2a10 10 0 1 0 10 10A10.01 10.01 0 0 0 12 2Zm1 15h-2v-2h2Zm0-4h-2V7h2Z"${_scopeId}></path></svg> ${ssrInterpolate(unref(t)("status"))}: ${ssrInterpolate(translateStatus(assignmentData.value.status))}</span>`);
            if (assignmentData.value.published_at) {
              _push2(`<span class="rounded-sm border border-gray-400 flex items-center justify-center gap-1 px-3 py-1"${_scopeId}><svg class="h-3 w-3 text-slate-600 dark:text-slate-300" fill="currentColor" viewBox="0 0 24 24"${_scopeId}><path d="M19 4h-1V2h-2v2H8V2H6v2H5a2 2 0 0 0-2 2v13a3 3 0 0 0 3 3h12a3 3 0 0 0 3-3V6a2 2 0 0 0-2-2Zm0 15a1 1 0 0 1-1 1H6a1 1 0 0 1-1-1V10h14Z"${_scopeId}></path></svg> ${ssrInterpolate(unref(t)("publishedAt"))}: ${ssrInterpolate(formatDate(assignmentData.value.published_at))}</span>`);
            } else {
              _push2(`<!---->`);
            }
            _push2(`<span class="rounded-sm border border-gray-400 flex items-center justify-center gap-1 px-3 py-1"${_scopeId}><svg class="h-3 w-3 text-emerald-600 dark:text-emerald-300" fill="currentColor" viewBox="0 0 24 24"${_scopeId}><path d="M9 16.17 4.83 12 3.41 13.41 9 19l12-12-1.41-1.41z"${_scopeId}></path></svg> ${ssrInterpolate(unref(t)("gradingType"))}: ${ssrInterpolate(translateGradingType(assignmentData.value.grading_type))}</span>`);
            if (assignmentData.value.max_score !== null && assignmentData.value.max_score !== void 0) {
              _push2(`<span class="rounded-sm border border-gray-400 flex items-center justify-center gap-1 px-3 py-1"${_scopeId}><svg class="h-3 w-3 text-amber-500" fill="currentColor" viewBox="0 0 24 24"${_scopeId}><path d="M12 2 14.85 8.15 21.5 9.27l-4.75 4.63 1.12 6.6L12 17.27 6.13 20.5l1.12-6.6L2.5 9.27l6.65-1.12L12 2Z"${_scopeId}></path></svg> ${ssrInterpolate(unref(t)("maxScore"))}: ${ssrInterpolate(assignmentData.value.max_score)}</span>`);
            } else {
              _push2(`<!---->`);
            }
            if (assignmentData.value.attempts_limit !== null && assignmentData.value.attempts_limit !== void 0) {
              _push2(`<span class="rounded-sm border border-gray-400 flex items-center justify-center gap-1 px-3 py-1"${_scopeId}><svg class="h-3 w-3 text-blue-700 dark:text-blue-300" fill="currentColor" viewBox="0 0 24 24"${_scopeId}><path d="M12 2a10 10 0 1 0 10 10A10.01 10.01 0 0 0 12 2Zm1 5h-2v6l5 3 1-1.73-4-2.27Z"${_scopeId}></path></svg> ${ssrInterpolate(unref(t)("attemptsLimit"))}: ${ssrInterpolate(assignmentData.value.attempts_limit === 0 ? unref(t)("no") : assignmentData.value.attempts_limit)}</span>`);
            } else {
              _push2(`<!---->`);
            }
            _push2(`<span class="rounded-sm border border-gray-400 flex items-center justify-center gap-1 px-3 py-1"${_scopeId}><svg class="h-3 w-3 text-sky-600 dark:text-sky-300" fill="currentColor" viewBox="0 0 24 24"${_scopeId}><path d="M12 5c-7 0-10 7-10 7s3 7 10 7 10-7 10-7-3-7-10-7Zm0 11a4 4 0 1 1 4-4 4 4 0 0 1-4 4Z"${_scopeId}></path></svg> ${ssrInterpolate(unref(t)("visibility"))}: ${ssrInterpolate(translateVisibility(assignmentData.value.visibility))}</span>`);
            if (assignmentData.value.due_at) {
              _push2(`<span class="rounded-sm border border-gray-400 flex items-center justify-center gap-1 px-3 py-1 text-rose-600 dark:text-rose-300"${_scopeId}><svg class="h-3 w-3" fill="currentColor" viewBox="0 0 24 24"${_scopeId}><path d="M19 4h-1V2h-2v2H8V2H6v2H5a2 2 0 0 0-2 2v13a3 3 0 0 0 3 3h12a3 3 0 0 0 3-3V6a2 2 0 0 0-2-2Zm0 15a1 1 0 0 1-1 1H6a1 1 0 0 1-1-1V10h14Z"${_scopeId}></path></svg> ${ssrInterpolate(unref(t)("dueAt"))}: ${ssrInterpolate(formatDate(assignmentData.value.due_at))}</span>`);
            } else {
              _push2(`<!---->`);
            }
            _push2(`</div>`);
            if (assignmentDescription.value) {
              _push2(`<div class="mt-4 text-sm subtitle"${_scopeId}>${assignmentDescription.value ?? ""}</div>`);
            } else {
              _push2(`<!---->`);
            }
            if (assignmentInstructions.value) {
              _push2(`<div class="mt-6"${_scopeId}><h2 class="mb-3 text-lg font-semibold text-slate-800 dark:text-slate-200"${_scopeId}>${ssrInterpolate(unref(t)("instructions"))}</h2><div class="text-sm subtitle"${_scopeId}>${assignmentInstructions.value ?? ""}</div></div>`);
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
                        createVNode("article", { class: "selection:bg-red-400 selection:text-white" }, [
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
                              ((_i = courseData.value) == null ? void 0 : _i.slug) ? (openBlock(), createBlock(Fragment, { key: 0 }, [
                                createVNode("li", null, [
                                  createVNode("span", { class: "mx-2 breadcrumbs" }, "/")
                                ]),
                                createVNode("li", null, [
                                  createVNode(unref(Link), {
                                    href: _ctx.route(
                                      "public.schoolCourses.show",
                                      {
                                        slug: courseData.value.slug
                                      }
                                    ),
                                    class: "breadcrumb-link hover:underline"
                                  }, {
                                    default: withCtx(() => [
                                      createTextVNode(toDisplayString(courseTitle.value), 1)
                                    ]),
                                    _: 1
                                  }, 8, ["href"])
                                ])
                              ], 64)) : createCommentVNode("", true),
                              ((_j = courseData.value) == null ? void 0 : _j.slug) && ((_k = moduleData.value) == null ? void 0 : _k.slug) ? (openBlock(), createBlock(Fragment, { key: 1 }, [
                                createVNode("li", null, [
                                  createVNode("span", { class: "mx-2 breadcrumbs" }, "/")
                                ]),
                                createVNode("li", null, [
                                  createVNode(unref(Link), {
                                    href: _ctx.route(
                                      "public.schoolModules.show",
                                      {
                                        courseSlug: courseData.value.slug,
                                        slug: moduleData.value.slug
                                      }
                                    ),
                                    class: "breadcrumb-link hover:underline"
                                  }, {
                                    default: withCtx(() => [
                                      createTextVNode(toDisplayString(moduleTitle.value), 1)
                                    ]),
                                    _: 1
                                  }, 8, ["href"])
                                ])
                              ], 64)) : createCommentVNode("", true),
                              ((_l = lessonData.value) == null ? void 0 : _l.slug) ? (openBlock(), createBlock(Fragment, { key: 2 }, [
                                createVNode("li", null, [
                                  createVNode("span", { class: "mx-2 breadcrumbs" }, "/")
                                ]),
                                createVNode("li", null, [
                                  createVNode(unref(Link), {
                                    href: _ctx.route(
                                      "public.schoolLessons.show",
                                      {
                                        slug: lessonData.value.slug
                                      }
                                    ),
                                    class: "breadcrumb-link hover:underline"
                                  }, {
                                    default: withCtx(() => [
                                      createTextVNode(toDisplayString(lessonTitle.value), 1)
                                    ]),
                                    _: 1
                                  }, 8, ["href"])
                                ])
                              ], 64)) : createCommentVNode("", true),
                              createVNode("li", null, [
                                createVNode("span", { class: "mx-2 breadcrumbs" }, "/")
                              ]),
                              createVNode("li", null, [
                                createVNode(unref(Link), {
                                  href: _ctx.route(
                                    "public.schoolAssignments.index"
                                  ),
                                  class: "breadcrumb-link hover:underline"
                                }, {
                                  default: withCtx(() => [
                                    createTextVNode(toDisplayString(unref(t)("assignments")), 1)
                                  ]),
                                  _: 1
                                }, 8, ["href"])
                              ]),
                              createVNode("li", null, [
                                createVNode("span", { class: "mx-2 breadcrumbs" }, "/")
                              ]),
                              createVNode("li", { class: "breadcrumbs" }, toDisplayString(assignmentTitle.value), 1)
                            ])
                          ]),
                          createVNode("div", { class: "flex items-center justify-center gap-3 title my-3" }, [
                            (openBlock(), createBlock("svg", {
                              class: "shrink-0 h-5 w-5 text-slate-600/85 dark:text-slate-200/85",
                              fill: "currentColor",
                              viewBox: "0 0 24 24"
                            }, [
                              createVNode("path", { d: "M15,18v2H9v-2H1v5c0,0.552,0.448,1,1,1h20c0.552,0,1-0.448,1-1v-5H15z" }),
                              createVNode("path", { d: "M23,4h-6V1c0-0.552-0.448-1-1-1H8C7.448,0,7,0.448,7,1v3H1C0.448,4,0,4.448,0,5v10c0,0.552,0.448,1,1,1h8v-3 h6v3h8c0.552,0,1-0.448,1-1V5C24,4.448,23.552,4,23,4z M15,4H9V2h6V4z" })
                            ])),
                            createVNode("h1", { class: "text-2xl font-bold" }, toDisplayString(assignmentTitle.value), 1)
                          ]),
                          assignmentSubtitle.value ? (openBlock(), createBlock("div", {
                            key: 0,
                            class: "mt-1 mb-3 text-sm subtitle text-center"
                          }, toDisplayString(assignmentSubtitle.value), 1)) : createCommentVNode("", true),
                          hasAssignmentImages.value ? (openBlock(), createBlock("div", {
                            key: 1,
                            class: "flex items-center justify-center"
                          }, [
                            createVNode("div", { class: "w-full" }, [
                              createVNode(ImageGalleryMain, {
                                images: assignmentImages.value,
                                alt: assignmentTitle.value,
                                "rounded-class": "rounded-lg",
                                "shadow-class": "shadow-lg shadow-gray-400\n                                                      dark:shadow-gray-700",
                                "img-class": "w-full h-full object-cover"
                              }, null, 8, ["images", "alt"])
                            ])
                          ])) : createCommentVNode("", true),
                          createVNode("div", { class: "my-4 flex flex-wrap items-center justify-center gap-3 text-sm text-slate-600 dark:text-slate-300" }, [
                            instructorData.value ? (openBlock(), createBlock("span", {
                              key: 0,
                              class: "rounded-sm border border-gray-400 flex items-center justify-center gap-2 px-3 py-1"
                            }, [
                              instructorImageUrl.value ? (openBlock(), createBlock("img", {
                                key: 0,
                                src: instructorImageUrl.value,
                                alt: instructorName.value,
                                loading: "lazy",
                                class: "h-6 w-6 rounded-full object-cover ring-1 ring-gray-200 dark:ring-gray-700"
                              }, null, 8, ["src", "alt"])) : (openBlock(), createBlock("svg", {
                                key: 1,
                                class: "h-4 w-4 text-violet-600/85 dark:text-violet-200/85",
                                fill: "currentColor",
                                viewBox: "0 0 640 512"
                              }, [
                                createVNode("path", { d: "M622.34 153.2L343.4 67.5c-15.2-4.67-31.6-4.67-46.79 0L17.66 153.2c-23.54 7.23-23.54 38.36 0 45.59l48.63 14.94c-10.67 13.19-17.23 29.28-17.88 46.9C38.78 266.15 32 276.11 32 288c0 10.78 5.68 19.85 13.86 25.65L20.33 428.53C18.11 438.52 25.71 448 35.94 448h56.11c10.24 0 17.84-9.48 15.62-19.47L82.14 313.65C90.32 307.85 96 298.78 96 288c0-11.57-6.47-21.25-15.66-26.87.76-15.02 8.44-28.3 20.69-36.72L296.6 284.5c9.06 2.78 26.44 6.25 46.79 0l278.95-85.7c23.55-7.24 23.55-38.36 0-45.6zM352.79 315.09c-28.53 8.76-52.84 3.92-65.59 0l-145.02-44.55L128 384c0 35.35 85.96 64 192 64s192-28.65 192-64l-14.18-113.47-145.03 44.56z" })
                              ])),
                              createVNode("span", null, toDisplayString(unref(t)("instructor")) + ": " + toDisplayString(instructorName.value), 1)
                            ])) : createCommentVNode("", true),
                            ((_m = courseData.value) == null ? void 0 : _m.slug) ? (openBlock(), createBlock(unref(Link), {
                              key: 1,
                              href: _ctx.route(
                                "public.schoolCourses.show",
                                {
                                  slug: courseData.value.slug
                                }
                              ),
                              class: "rounded-sm border border-gray-400 flex items-center justify-center gap-1 px-3 py-1 hover:text-blue-600 dark:hover:bg-blue-400"
                            }, {
                              default: withCtx(() => [
                                (openBlock(), createBlock("svg", {
                                  class: "shrink-0 h-3 w-3 text-sky-600/85 dark:text-sky-200/85",
                                  fill: "currentColor",
                                  viewBox: "0 0 448 512"
                                }, [
                                  createVNode("path", { d: "M318.38 208h-39.09c-1.49 27.03-6.54 51.35-14.21 70.41 27.71-13.24 48.02-39.19 53.3-70.41zm0-32c-5.29-31.22-25.59-57.17-53.3-70.41 7.68 19.06 12.72 43.38 14.21 70.41h39.09zM224 97.31c-7.69 7.45-20.77 34.42-23.43 78.69h46.87c-2.67-44.26-15.75-71.24-23.44-78.69zm-41.08 8.28c-27.71 13.24-48.02 39.19-53.3 70.41h39.09c1.49-27.03 6.53-51.35 14.21-70.41zm0 172.82c-7.68-19.06-12.72-43.38-14.21-70.41h-39.09c5.28 31.22 25.59 57.17 53.3 70.41zM247.43 208h-46.87c2.66 44.26 15.74 71.24 23.43 78.69 7.7-7.45 20.78-34.43 23.44-78.69zM448 358.4V25.6c0-16-9.6-25.6-25.6-25.6H96C41.6 0 0 41.6 0 96v320c0 54.4 41.6 96 96 96h326.4c12.8 0 25.6-9.6 25.6-25.6v-16c0-6.4-3.2-12.8-9.6-19.2-3.2-16-3.2-60.8 0-73.6 6.4-3.2 9.6-9.6 9.6-19.2zM224 64c70.69 0 128 57.31 128 128s-57.31 128-128 128S96 262.69 96 192 153.31 64 224 64zm160 384H96c-19.2 0-32-12.8-32-32s16-32 32-32h288v64z" })
                                ])),
                                createTextVNode(" " + toDisplayString(unref(t)("course")) + ": " + toDisplayString(courseTitle.value), 1)
                              ]),
                              _: 1
                            }, 8, ["href"])) : createCommentVNode("", true),
                            ((_n = courseData.value) == null ? void 0 : _n.slug) && ((_o = moduleData.value) == null ? void 0 : _o.slug) ? (openBlock(), createBlock(unref(Link), {
                              key: 2,
                              href: _ctx.route(
                                "public.schoolModules.show",
                                {
                                  courseSlug: courseData.value.slug,
                                  slug: moduleData.value.slug
                                }
                              ),
                              class: "rounded-sm border border-gray-400 flex items-center justify-center gap-1 px-3 py-1 hover:text-blue-600 dark:hover:bg-blue-400"
                            }, {
                              default: withCtx(() => [
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
                                    d: "M23.428,4.618,19.381.572h0a1.957,1.957,0,0,0-2.762,0L12.572,4.618a1.959,1.959,0,0,0,0,2.764l4.047,4.047a1.957,1.957,0,0,0,2.762,0l4.047-4.046A1.959,1.959,0,0,0,23.428,4.618Z"
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
                                createTextVNode(" " + toDisplayString(unref(t)("module")) + ": " + toDisplayString(moduleTitle.value), 1)
                              ]),
                              _: 1
                            }, 8, ["href"])) : createCommentVNode("", true),
                            ((_p = lessonData.value) == null ? void 0 : _p.slug) ? (openBlock(), createBlock(unref(Link), {
                              key: 3,
                              href: _ctx.route(
                                "public.schoolLessons.show",
                                {
                                  slug: lessonData.value.slug
                                }
                              ),
                              class: "rounded-sm border border-gray-400 flex items-center justify-center gap-1 px-3 py-1 hover:text-blue-600 dark:hover:bg-blue-400"
                            }, {
                              default: withCtx(() => [
                                (openBlock(), createBlock("svg", {
                                  class: "shrink-0 h-3 w-3 text-indigo-600/85 dark:text-indigo-300/85",
                                  fill: "currentColor",
                                  viewBox: "0 0 24 24"
                                }, [
                                  createVNode("path", { d: "M7 4.75h8.5A2.75 2.75 0 0 1 18.25 7.5v9A2.75 2.75 0 0 1 15.5 19.25H7A2.25 2.25 0 0 1 4.75 17V7A2.25 2.25 0 0 1 7 4.75Z" }),
                                  createVNode("path", {
                                    d: "M8.5 8.5h6M8.5 12h6M8.5 15.5h4",
                                    fill: "none",
                                    stroke: "currentColor",
                                    "stroke-width": "1.5",
                                    "stroke-linecap": "round"
                                  })
                                ])),
                                createTextVNode(" " + toDisplayString(unref(t)("lesson")) + ": " + toDisplayString(lessonTitle.value), 1)
                              ]),
                              _: 1
                            }, 8, ["href"])) : createCommentVNode("", true),
                            createVNode("span", { class: "rounded-sm border border-gray-400 flex items-center justify-center gap-1 px-3 py-1" }, [
                              (openBlock(), createBlock("svg", {
                                class: "h-3 w-3 text-violet-600 dark:text-violet-300",
                                fill: "currentColor",
                                viewBox: "0 0 24 24"
                              }, [
                                createVNode("path", { d: "M12 2a10 10 0 1 0 10 10A10.01 10.01 0 0 0 12 2Zm1 15h-2v-2h2Zm0-4h-2V7h2Z" })
                              ])),
                              createTextVNode(" " + toDisplayString(unref(t)("status")) + ": " + toDisplayString(translateStatus(assignmentData.value.status)), 1)
                            ]),
                            assignmentData.value.published_at ? (openBlock(), createBlock("span", {
                              key: 4,
                              class: "rounded-sm border border-gray-400 flex items-center justify-center gap-1 px-3 py-1"
                            }, [
                              (openBlock(), createBlock("svg", {
                                class: "h-3 w-3 text-slate-600 dark:text-slate-300",
                                fill: "currentColor",
                                viewBox: "0 0 24 24"
                              }, [
                                createVNode("path", { d: "M19 4h-1V2h-2v2H8V2H6v2H5a2 2 0 0 0-2 2v13a3 3 0 0 0 3 3h12a3 3 0 0 0 3-3V6a2 2 0 0 0-2-2Zm0 15a1 1 0 0 1-1 1H6a1 1 0 0 1-1-1V10h14Z" })
                              ])),
                              createTextVNode(" " + toDisplayString(unref(t)("publishedAt")) + ": " + toDisplayString(formatDate(assignmentData.value.published_at)), 1)
                            ])) : createCommentVNode("", true),
                            createVNode("span", { class: "rounded-sm border border-gray-400 flex items-center justify-center gap-1 px-3 py-1" }, [
                              (openBlock(), createBlock("svg", {
                                class: "h-3 w-3 text-emerald-600 dark:text-emerald-300",
                                fill: "currentColor",
                                viewBox: "0 0 24 24"
                              }, [
                                createVNode("path", { d: "M9 16.17 4.83 12 3.41 13.41 9 19l12-12-1.41-1.41z" })
                              ])),
                              createTextVNode(" " + toDisplayString(unref(t)("gradingType")) + ": " + toDisplayString(translateGradingType(assignmentData.value.grading_type)), 1)
                            ]),
                            assignmentData.value.max_score !== null && assignmentData.value.max_score !== void 0 ? (openBlock(), createBlock("span", {
                              key: 5,
                              class: "rounded-sm border border-gray-400 flex items-center justify-center gap-1 px-3 py-1"
                            }, [
                              (openBlock(), createBlock("svg", {
                                class: "h-3 w-3 text-amber-500",
                                fill: "currentColor",
                                viewBox: "0 0 24 24"
                              }, [
                                createVNode("path", { d: "M12 2 14.85 8.15 21.5 9.27l-4.75 4.63 1.12 6.6L12 17.27 6.13 20.5l1.12-6.6L2.5 9.27l6.65-1.12L12 2Z" })
                              ])),
                              createTextVNode(" " + toDisplayString(unref(t)("maxScore")) + ": " + toDisplayString(assignmentData.value.max_score), 1)
                            ])) : createCommentVNode("", true),
                            assignmentData.value.attempts_limit !== null && assignmentData.value.attempts_limit !== void 0 ? (openBlock(), createBlock("span", {
                              key: 6,
                              class: "rounded-sm border border-gray-400 flex items-center justify-center gap-1 px-3 py-1"
                            }, [
                              (openBlock(), createBlock("svg", {
                                class: "h-3 w-3 text-blue-700 dark:text-blue-300",
                                fill: "currentColor",
                                viewBox: "0 0 24 24"
                              }, [
                                createVNode("path", { d: "M12 2a10 10 0 1 0 10 10A10.01 10.01 0 0 0 12 2Zm1 5h-2v6l5 3 1-1.73-4-2.27Z" })
                              ])),
                              createTextVNode(" " + toDisplayString(unref(t)("attemptsLimit")) + ": " + toDisplayString(assignmentData.value.attempts_limit === 0 ? unref(t)("no") : assignmentData.value.attempts_limit), 1)
                            ])) : createCommentVNode("", true),
                            createVNode("span", { class: "rounded-sm border border-gray-400 flex items-center justify-center gap-1 px-3 py-1" }, [
                              (openBlock(), createBlock("svg", {
                                class: "h-3 w-3 text-sky-600 dark:text-sky-300",
                                fill: "currentColor",
                                viewBox: "0 0 24 24"
                              }, [
                                createVNode("path", { d: "M12 5c-7 0-10 7-10 7s3 7 10 7 10-7 10-7-3-7-10-7Zm0 11a4 4 0 1 1 4-4 4 4 0 0 1-4 4Z" })
                              ])),
                              createTextVNode(" " + toDisplayString(unref(t)("visibility")) + ": " + toDisplayString(translateVisibility(assignmentData.value.visibility)), 1)
                            ]),
                            assignmentData.value.due_at ? (openBlock(), createBlock("span", {
                              key: 7,
                              class: "rounded-sm border border-gray-400 flex items-center justify-center gap-1 px-3 py-1 text-rose-600 dark:text-rose-300"
                            }, [
                              (openBlock(), createBlock("svg", {
                                class: "h-3 w-3",
                                fill: "currentColor",
                                viewBox: "0 0 24 24"
                              }, [
                                createVNode("path", { d: "M19 4h-1V2h-2v2H8V2H6v2H5a2 2 0 0 0-2 2v13a3 3 0 0 0 3 3h12a3 3 0 0 0 3-3V6a2 2 0 0 0-2-2Zm0 15a1 1 0 0 1-1 1H6a1 1 0 0 1-1-1V10h14Z" })
                              ])),
                              createTextVNode(" " + toDisplayString(unref(t)("dueAt")) + ": " + toDisplayString(formatDate(assignmentData.value.due_at)), 1)
                            ])) : createCommentVNode("", true)
                          ]),
                          assignmentDescription.value ? (openBlock(), createBlock("div", {
                            key: 2,
                            class: "mt-4 text-sm subtitle",
                            innerHTML: assignmentDescription.value
                          }, null, 8, ["innerHTML"])) : createCommentVNode("", true),
                          assignmentInstructions.value ? (openBlock(), createBlock("div", {
                            key: 3,
                            class: "mt-6"
                          }, [
                            createVNode("h2", { class: "mb-3 text-lg font-semibold text-slate-800 dark:text-slate-200" }, toDisplayString(unref(t)("instructions")), 1),
                            createVNode("div", {
                              class: "text-sm subtitle",
                              innerHTML: assignmentInstructions.value
                            }, null, 8, ["innerHTML"])
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
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Pages/Public/Default/School/SchoolAssignments/Show.vue");
  return _sfc_setup ? _sfc_setup(props, ctx) : void 0;
};
export {
  _sfc_main as default
};
