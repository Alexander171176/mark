import { computed, ref, watch, unref, withCtx, createVNode, resolveDynamicComponent, createBlock, createCommentVNode, toDisplayString, openBlock, useSSRContext } from "vue";
import { ssrRenderComponent, ssrInterpolate, ssrRenderAttr, ssrRenderVNode, ssrRenderClass } from "vue/server-renderer";
import { usePage, Head, Link, router } from "@inertiajs/vue3";
import { useI18n } from "vue-i18n";
import { u as useSmoothScrollTo } from "./useSmoothScrollTo-g9G3kyDv.js";
import { _ as _sfc_main$1 } from "./DefaultLayout-6zxGxPtK.js";
import { _ as _sfc_main$2, a as _sfc_main$c, P as Progress } from "./Progress-CyfOKcqP.js";
import { _ as _sfc_main$3, a as _sfc_main$b } from "./RightSidebarSchool-DlAegojf.js";
import { _ as _sfc_main$4 } from "./EntityPageToolbar-DT32FtSd.js";
import { _ as _sfc_main$5 } from "./FrontendEntityPageToolbar-Xr_r9znL.js";
import { _ as _sfc_main$8, a as _sfc_main$9, b as _sfc_main$d } from "./PublicAdminBottomPanel-BhLEQMoJ.js";
import { _ as _sfc_main$a, S as SectionBanners } from "./SectionBanners-Cdgmr0Bw.js";
import { _ as _sfc_main$6 } from "./CourseGrid-DoYn9a32.js";
import { _ as _sfc_main$7 } from "./CourseRows-Q6HeVebv.js";
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
import "./ViewModeToggle-DMCnQ0wo.js";
import "./ProcessingModeSwitcher-BJvzFf6_.js";
import "./UniversalImageSlider-Cu2Xndcn.js";
import "./EntityStats-Dy-sjN5R.js";
import "./LikeButtonEntity-ZC4HMEAO.js";
const LEFT_SIDEBAR_KEY = "public_left_sidebar_collapsed";
const RIGHT_SIDEBAR_KEY = "public_right_sidebar_collapsed";
const DEFAULT_SORT = "idDesc";
const VIEW_KEY = "public_school_courses_view";
const _sfc_main = {
  __name: "Index",
  __ssrInlineRender: true,
  props: {
    locale: {
      type: String,
      default: "ru"
    },
    seo: {
      type: Object,
      default: () => ({
        title: "",
        keywords: "",
        description: ""
      })
    },
    useServerProcessing: {
      type: Boolean,
      default: false
    },
    publicSchoolCoursesProcessingMode: {
      type: String,
      default: "server"
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
    trackTree: {
      type: Array,
      default: () => []
    },
    courses: {
      type: [Array, Object],
      default: () => []
    },
    coursesCount: {
      type: Number,
      default: 0
    },
    coursesFound: {
      type: Number,
      default: 0
    },
    filters: {
      type: Object,
      default: () => ({})
    },
    hashtags: {
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
    var _a, _b, _c, _d;
    const { t } = useI18n();
    const props = __props;
    const page = usePage();
    const siteSettings = ((_a = page.props) == null ? void 0 : _a.siteSettings) || {};
    const isAdmin = computed(() => {
      var _a2;
      return ((_a2 = page.props) == null ? void 0 : _a2.isAdmin) === true;
    });
    const trackTree = computed(() => {
      return Array.isArray(props.trackTree) ? props.trackTree : [];
    });
    const coursesData = computed(() => {
      var _a2;
      if (Array.isArray(props.courses)) {
        return props.courses;
      }
      if (Array.isArray((_a2 = props.courses) == null ? void 0 : _a2.data)) {
        return props.courses.data;
      }
      return [];
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
    const gridCols = computed(() => {
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
    const q = ref(
      String(((_b = props.filters) == null ? void 0 : _b.q) ?? "")
    );
    const sort = ref(
      String(((_c = props.filters) == null ? void 0 : _c.sort) ?? DEFAULT_SORT)
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
    const courseSortOptions = [
      { value: "idDesc", label: t("idDesc") },
      { value: "idAsc", label: t("idAsc") },
      { value: "sortAsc", label: `${t("sortNumber")} 0→9` },
      { value: "sortDesc", label: `${t("sortNumber")} 9→0` },
      { value: "titleAsc", label: `${t("title")} A→Z` },
      { value: "titleDesc", label: `${t("title")} Z→A` },
      { value: "studentsCountDesc", label: `${t("students")} 9→0` },
      { value: "studentsCountAsc", label: `${t("students")} 0→9` },
      { value: "viewsDesc", label: `${t("views")} 9→0` },
      { value: "viewsAsc", label: `${t("views")} 0→9` },
      { value: "likesDesc", label: `${t("likes")} 9→0` },
      { value: "likesAsc", label: `${t("likes")} 0→9` },
      { value: "popularityDesc", label: `${t("popularity")} 9→0` },
      { value: "popularityAsc", label: `${t("popularity")} 0→9` },
      { value: "ratingAvgDesc", label: `${t("ratingAvg")} 9→0` },
      { value: "ratingAvgAsc", label: `${t("ratingAvg")} 0→9` },
      { value: "ratingCountDesc", label: `${t("ratingCount")} 9→0` },
      { value: "ratingCountAsc", label: `${t("ratingCount")} 0→9` },
      { value: "difficultyDesc", label: `${t("sortDifficulty")} 9→0` },
      { value: "difficultyAsc", label: `${t("sortDifficulty")} 0→9` },
      { value: "durationDesc", label: `${t("duration")} 9→0` },
      { value: "durationAsc", label: `${t("duration")} 0→9` },
      { value: "levelAsc", label: `${t("level")} A→Z` },
      { value: "levelDesc", label: `${t("level")} Z→A` },
      { value: "modulesDesc", label: `${t("modules")} 9→0` },
      { value: "modulesAsc", label: `${t("modules")} 0→9` },
      { value: "lessonsDesc", label: `${t("lessons")} 9→0` },
      { value: "lessonsAsc", label: `${t("lessons")} 0→9` },
      { value: "tracksDesc", label: `${t("tracks")} 9→0` },
      { value: "tracksAsc", label: `${t("tracks")} 0→9` },
      { value: "hashtagsDesc", label: `${t("hashtags")} 9→0` },
      { value: "hashtagsAsc", label: `${t("hashtags")} 0→9` },
      { value: "reviewsDesc", label: `${t("reviews")} 9→0` },
      { value: "reviewsAsc", label: `${t("reviews")} 0→9` },
      { value: "publishedAtDesc", label: `${t("publishedAt")} ↓` },
      { value: "publishedAtAsc", label: `${t("publishedAt")} ↑` }
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
      return String(value ?? "").toLowerCase();
    };
    const getCourseTitle = (course) => {
      var _a2;
      return ((_a2 = course == null ? void 0 : course.translation) == null ? void 0 : _a2.title) || "";
    };
    const getCourseShort = (course) => {
      var _a2;
      return ((_a2 = course == null ? void 0 : course.translation) == null ? void 0 : _a2.short) || "";
    };
    const getCourseSlug = (course) => {
      return (course == null ? void 0 : course.slug) || "";
    };
    const filteredCourses = computed(() => {
      const query = normalizeText(q.value).trim();
      if (!query) {
        return coursesData.value;
      }
      return coursesData.value.filter((course) => {
        var _a2, _b2, _c2, _d2;
        return [
          getCourseTitle(course),
          getCourseShort(course),
          getCourseSlug(course),
          (_b2 = (_a2 = course == null ? void 0 : course.instructorProfile) == null ? void 0 : _a2.translation) == null ? void 0 : _b2.title,
          (_d2 = (_c2 = course == null ? void 0 : course.instructorProfile) == null ? void 0 : _c2.user) == null ? void 0 : _d2.name
        ].some(
          (value) => normalizeText(value).includes(query)
        );
      });
    });
    const sortedCourses = computed(() => {
      const list = [...filteredCourses.value];
      return list.sort((a, b) => {
        switch (sort.value) {
          case "idAsc":
            return (a.id ?? 0) - (b.id ?? 0);
          case "idDesc":
            return (b.id ?? 0) - (a.id ?? 0);
          case "sortAsc":
            return (a.sort ?? 0) - (b.sort ?? 0);
          case "sortDesc":
            return (b.sort ?? 0) - (a.sort ?? 0);
          case "titleAsc":
            return normalizeText(getCourseTitle(a)).localeCompare(
              normalizeText(getCourseTitle(b))
            );
          case "titleDesc":
            return normalizeText(getCourseTitle(b)).localeCompare(
              normalizeText(getCourseTitle(a))
            );
          case "studentsCountAsc":
            return (a.students_count ?? 0) - (b.students_count ?? 0);
          case "studentsCountDesc":
            return (b.students_count ?? 0) - (a.students_count ?? 0);
          case "viewsAsc":
            return (a.views ?? 0) - (b.views ?? 0);
          case "viewsDesc":
            return (b.views ?? 0) - (a.views ?? 0);
          case "likesAsc":
            return (a.likes_count ?? 0) - (b.likes_count ?? 0);
          case "likesDesc":
            return (b.likes_count ?? 0) - (a.likes_count ?? 0);
          case "popularityAsc":
            return (a.popularity ?? 0) - (b.popularity ?? 0);
          case "popularityDesc":
            return (b.popularity ?? 0) - (a.popularity ?? 0);
          case "ratingAvgAsc":
            return (a.rating_avg ?? 0) - (b.rating_avg ?? 0);
          case "ratingAvgDesc":
            return (b.rating_avg ?? 0) - (a.rating_avg ?? 0);
          case "ratingCountAsc":
            return (a.rating_count ?? 0) - (b.rating_count ?? 0);
          case "ratingCountDesc":
            return (b.rating_count ?? 0) - (a.rating_count ?? 0);
          case "difficultyAsc":
            return (a.difficulty ?? 0) - (b.difficulty ?? 0);
          case "difficultyDesc":
            return (b.difficulty ?? 0) - (a.difficulty ?? 0);
          case "durationAsc":
            return (a.duration ?? 0) - (b.duration ?? 0);
          case "durationDesc":
            return (b.duration ?? 0) - (a.duration ?? 0);
          case "levelAsc":
            return normalizeText(a.level).localeCompare(
              normalizeText(b.level)
            );
          case "levelDesc":
            return normalizeText(b.level).localeCompare(
              normalizeText(a.level)
            );
          case "modulesAsc":
            return (a.modules_count ?? 0) - (b.modules_count ?? 0);
          case "modulesDesc":
            return (b.modules_count ?? 0) - (a.modules_count ?? 0);
          case "lessonsAsc":
            return (a.lessons_count ?? 0) - (b.lessons_count ?? 0);
          case "lessonsDesc":
            return (b.lessons_count ?? 0) - (a.lessons_count ?? 0);
          case "tracksAsc":
            return (a.tracks_count ?? 0) - (b.tracks_count ?? 0);
          case "tracksDesc":
            return (b.tracks_count ?? 0) - (a.tracks_count ?? 0);
          case "hashtagsAsc":
            return (a.hashtags_count ?? 0) - (b.hashtags_count ?? 0);
          case "hashtagsDesc":
            return (b.hashtags_count ?? 0) - (a.hashtags_count ?? 0);
          case "reviewsAsc":
            return (a.reviews_count ?? 0) - (b.reviews_count ?? 0);
          case "reviewsDesc":
            return (b.reviews_count ?? 0) - (a.reviews_count ?? 0);
          case "publishedAtAsc":
            return new Date(a.published_at ?? 0) - new Date(b.published_at ?? 0);
          case "publishedAtDesc":
            return new Date(b.published_at ?? 0) - new Date(a.published_at ?? 0);
          default:
            return 0;
        }
      });
    });
    const frontendPaginatedCourses = computed(() => {
      const start = (frontendCurrentPage.value - 1) * perPage.value;
      return sortedCourses.value.slice(
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
        ((_b2 = (_a2 = props.courses) == null ? void 0 : _a2.meta) == null ? void 0 : _b2.current_page) ?? ((_c2 = props.courses) == null ? void 0 : _c2.current_page) ?? 1
      ) || 1;
    });
    const lastPage = computed(() => {
      var _a2, _b2, _c2;
      return Number(
        ((_b2 = (_a2 = props.courses) == null ? void 0 : _a2.meta) == null ? void 0 : _b2.last_page) ?? ((_c2 = props.courses) == null ? void 0 : _c2.last_page) ?? 1
      ) || 1;
    });
    const indexRoute = () => {
      return route("public.schoolCourses.index");
    };
    const reloadCourses = (page2 = 1) => {
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
    const submitSearch = () => {
      reloadCourses(1);
    };
    const resetSearch = () => {
      q.value = "";
      sort.value = DEFAULT_SORT;
      frontendCurrentPage.value = 1;
      if (props.useServerProcessing) {
        reloadCourses(1);
      }
    };
    const updateSort = (value) => {
      sort.value = value || DEFAULT_SORT;
      if (props.useServerProcessing) {
        reloadCourses(1);
      }
    };
    const updateViewMode = (value) => {
      viewMode.value = value || "grid";
      frontendCurrentPage.value = 1;
      if (props.useServerProcessing) {
        reloadCourses(1);
      }
    };
    const goToPage = (pageNumber) => {
      const value = Number(pageNumber);
      if (!Number.isFinite(value)) {
        return;
      }
      const safePage = Math.max(
        1,
        Math.min(value, lastPage.value)
      );
      reloadCourses(safePage);
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
    const displayedCourses = computed(() => {
      return props.useServerProcessing ? coursesData.value : frontendPaginatedCourses.value;
    });
    const seoTitle = computed(() => {
      var _a2;
      return String(
        ((_a2 = props.seo) == null ? void 0 : _a2.title) || t("courses")
      ).trim();
    });
    const seoDescription = computed(() => {
      var _a2;
      return String(
        ((_a2 = props.seo) == null ? void 0 : _a2.description) || t("courses")
      ).trim();
    });
    const seoKeywords = computed(() => {
      var _a2;
      return String(
        ((_a2 = props.seo) == null ? void 0 : _a2.keywords) || ""
      ).trim();
    });
    const ogLocale = computed(() => {
      const locale = String(props.locale || "").trim().replace("_", "-");
      if (!locale) {
        return void 0;
      }
      try {
        const normalized = new Intl.Locale(locale).maximize();
        return normalized.region ? `${normalized.language}_${normalized.region}` : normalized.language;
      } catch {
        return locale.replace("-", "_");
      }
    });
    const seoCurrentPage = computed(() => {
      return props.useServerProcessing ? currentPage.value : frontendCurrentPage.value;
    });
    const canonicalPath = computed(() => {
      const base = `/${props.locale}/school/courses`;
      return seoCurrentPage.value > 1 ? `${base}?page=${seoCurrentPage.value}` : base;
    });
    const canonicalUrl = computed(() => {
      if (typeof window === "undefined") {
        return canonicalPath.value;
      }
      return new URL(
        canonicalPath.value,
        window.location.origin
      ).toString();
    });
    const homeUrl = computed(() => {
      if (typeof window === "undefined") {
        return "/";
      }
      return new URL(
        route("home"),
        window.location.origin
      ).toString();
    });
    const getAbsoluteCourseUrl = (course) => {
      const url = route(
        "public.schoolCourses.show",
        {
          slug: course.slug
        }
      );
      if (typeof window === "undefined") {
        return url;
      }
      return new URL(
        url,
        window.location.origin
      ).toString();
    };
    const siteName = computed(() => {
      return String(
        (siteSettings == null ? void 0 : siteSettings.siteName) || (siteSettings == null ? void 0 : siteSettings.SiteName) || ""
      ).trim();
    });
    const robotsContent = computed(() => {
      return q.value.trim() ? "noindex, follow" : "index, follow";
    });
    const collectionPageSchema = computed(() => ({
      "@context": "https://schema.org",
      "@type": "CollectionPage",
      "@id": `${canonicalUrl.value}#webpage`,
      url: canonicalUrl.value,
      name: seoTitle.value,
      description: seoDescription.value,
      inLanguage: props.locale,
      isPartOf: {
        "@type": "WebSite",
        url: homeUrl.value,
        ...siteName.value ? { name: siteName.value } : {}
      }
    }));
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
          name: seoTitle.value,
          item: canonicalUrl.value
        }
      ]
    }));
    const courseItemListSchema = computed(() => ({
      "@context": "https://schema.org",
      "@type": "ItemList",
      name: seoTitle.value,
      numberOfItems: displayedCourses.value.length,
      itemListElement: displayedCourses.value.map(
        (course, index) => {
          const courseUrl = getAbsoluteCourseUrl(course);
          const item = {
            "@type": "Course",
            "@id": `${courseUrl}#course`,
            url: courseUrl,
            name: getCourseTitle(course),
            inLanguage: props.locale
          };
          if (getCourseShort(course)) {
            item.description = getCourseShort(course);
          }
          return {
            "@type": "ListItem",
            position: (seoCurrentPage.value - 1) * perPage.value + index + 1,
            url: courseUrl,
            item
          };
        }
      )
    }));
    const collectionPageJsonLd = computed(() => {
      return JSON.stringify(
        collectionPageSchema.value
      );
    });
    const breadcrumbJsonLd = computed(() => {
      return JSON.stringify(
        breadcrumbSchema.value
      );
    });
    const courseItemListJsonLd = computed(() => {
      return JSON.stringify(
        courseItemListSchema.value
      );
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
            _push2(`<meta name="robots"${ssrRenderAttr("content", robotsContent.value)}${_scopeId}><meta name="googlebot"${ssrRenderAttr("content", robotsContent.value)}${_scopeId}><link rel="canonical"${ssrRenderAttr("href", canonicalUrl.value)}${_scopeId}><meta property="og:title"${ssrRenderAttr("content", seoTitle.value)}${_scopeId}><meta property="og:description"${ssrRenderAttr("content", seoDescription.value)}${_scopeId}><meta property="og:type" content="website"${_scopeId}><meta property="og:url"${ssrRenderAttr("content", canonicalUrl.value)}${_scopeId}><meta property="og:locale"${ssrRenderAttr("content", ogLocale.value)}${_scopeId}>`);
            if (siteName.value) {
              _push2(`<meta property="og:site_name"${ssrRenderAttr("content", siteName.value)}${_scopeId}>`);
            } else {
              _push2(`<!---->`);
            }
            _push2(`<meta name="twitter:card" content="summary"${_scopeId}><meta name="twitter:title"${ssrRenderAttr("content", seoTitle.value)}${_scopeId}><meta name="twitter:description"${ssrRenderAttr("content", seoDescription.value)}${_scopeId}><meta name="DC.title"${ssrRenderAttr("content", seoTitle.value)}${_scopeId}><meta name="DC.description"${ssrRenderAttr("content", seoDescription.value)}${_scopeId}>`);
            if (seoKeywords.value) {
              _push2(`<meta name="DC.subject"${ssrRenderAttr("content", seoKeywords.value)}${_scopeId}>`);
            } else {
              _push2(`<!---->`);
            }
            _push2(`<meta name="DC.identifier"${ssrRenderAttr("content", canonicalUrl.value)}${_scopeId}><meta name="DC.language"${ssrRenderAttr("content", __props.locale)}${_scopeId}><meta name="DC.type" content="Collection"${_scopeId}><meta name="DC.format" content="text/html"${_scopeId}>`);
            if (siteName.value) {
              _push2(`<meta name="DC.publisher"${ssrRenderAttr("content", siteName.value)}${_scopeId}>`);
            } else {
              _push2(`<!---->`);
            }
            ssrRenderVNode(_push2, createVNode(resolveDynamicComponent("script"), { type: "application/ld+json" }, null), _parent2, _scopeId);
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
                name: "robots",
                content: robotsContent.value
              }, null, 8, ["content"]),
              createVNode("meta", {
                name: "googlebot",
                content: robotsContent.value
              }, null, 8, ["content"]),
              createVNode("link", {
                rel: "canonical",
                href: canonicalUrl.value
              }, null, 8, ["href"]),
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
                content: canonicalUrl.value
              }, null, 8, ["content"]),
              createVNode("meta", {
                property: "og:locale",
                content: ogLocale.value
              }, null, 8, ["content"]),
              siteName.value ? (openBlock(), createBlock("meta", {
                key: 1,
                property: "og:site_name",
                content: siteName.value
              }, null, 8, ["content"])) : createCommentVNode("", true),
              createVNode("meta", {
                name: "twitter:card",
                content: "summary"
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
                name: "DC.title",
                content: seoTitle.value
              }, null, 8, ["content"]),
              createVNode("meta", {
                name: "DC.description",
                content: seoDescription.value
              }, null, 8, ["content"]),
              seoKeywords.value ? (openBlock(), createBlock("meta", {
                key: 2,
                name: "DC.subject",
                content: seoKeywords.value
              }, null, 8, ["content"])) : createCommentVNode("", true),
              createVNode("meta", {
                name: "DC.identifier",
                content: canonicalUrl.value
              }, null, 8, ["content"]),
              createVNode("meta", {
                name: "DC.language",
                content: __props.locale
              }, null, 8, ["content"]),
              createVNode("meta", {
                name: "DC.type",
                content: "Collection"
              }),
              createVNode("meta", {
                name: "DC.format",
                content: "text/html"
              }),
              siteName.value ? (openBlock(), createBlock("meta", {
                key: 3,
                name: "DC.publisher",
                content: siteName.value
              }, null, 8, ["content"])) : createCommentVNode("", true),
              (openBlock(), createBlock(resolveDynamicComponent("script"), {
                type: "application/ld+json",
                innerHTML: collectionPageJsonLd.value
              }, null, 8, ["innerHTML"])),
              (openBlock(), createBlock(resolveDynamicComponent("script"), {
                type: "application/ld+json",
                innerHTML: breadcrumbJsonLd.value
              }, null, 8, ["innerHTML"])),
              (openBlock(), createBlock(resolveDynamicComponent("script"), {
                type: "application/ld+json",
                innerHTML: courseItemListJsonLd.value
              }, null, 8, ["innerHTML"]))
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
          if (_push2) {
            _push2(ssrRenderComponent(_sfc_main$2, null, null, _parent2, _scopeId));
            _push2(`<main class="min-h-screen px-1 lg:px-6 max-w-full"${_scopeId}><div class="mx-auto tracking-wider pt-20 lg:pt-44"${_scopeId}><div class="ext-color w-full min-w-0 py-3 px-1 flex flex-col lg:flex-row gap-4 rounded-3xl border-2 border-slate-300 dark:border-slate-500"${_scopeId}>`);
            if (showLeft.value) {
              _push2(`<aside class="${ssrRenderClass([leftCollapsed.value ? "lg:w-6" : "lg:w-72", "shrink-0 transition-all duration-300"])}"${_scopeId}>`);
              _push2(ssrRenderComponent(_sfc_main$3, {
                "track-tree": trackTree.value,
                collapsed: leftCollapsed.value,
                onCollapsed: ($event) => leftCollapsed.value = $event
              }, null, _parent2, _scopeId));
              _push2(`</aside>`);
            } else {
              _push2(`<!---->`);
            }
            _push2(`<article class="w-full pb-6 slate-1" itemscope itemtype="https://schema.org/CollectionPage"${_scopeId}><div class="mx-auto max-w-6xl"${_scopeId}><nav class="text-sm" aria-label="Breadcrumb" itemscope itemtype="https://schema.org/BreadcrumbList"${_scopeId}><ol class="flex flex-wrap items-center font-semibold"${_scopeId}><li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem"${_scopeId}>`);
            _push2(ssrRenderComponent(unref(Link), {
              href: _ctx.route("home"),
              class: "breadcrumb-link hover:underline",
              itemprop: "item"
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
            _push2(`<meta itemprop="position" content="1"${_scopeId}></li><li aria-hidden="true"${_scopeId}><span class="mx-2 breadcrumbs"${_scopeId}> / </span></li><li class="breadcrumbs" itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem" aria-current="page"${_scopeId}><span itemprop="name"${_scopeId}>${ssrInterpolate(unref(t)("courses"))}</span><meta itemprop="item"${ssrRenderAttr("content", canonicalUrl.value)}${_scopeId}><meta itemprop="position" content="2"${_scopeId}></li></ol></nav><div class="my-3 flex flex-wrap items-center justify-center gap-3 title"${_scopeId}><svg class="shrink-0 h-5 w-5 text-slate-600/85 dark:text-slate-200/85" fill="currentColor" viewBox="0 0 448 512" aria-hidden="true"${_scopeId}><path d="M318.38 208h-39.09c-1.49 27.03-6.54 51.35-14.21 70.41 27.71-13.24 48.02-39.19 53.3-70.41zm0-32c-5.29-31.22-25.59-57.17-53.3-70.41 7.68 19.06 12.72 43.38 14.21 70.41h39.09zM224 97.31c-7.69 7.45-20.77 34.42-23.43 78.69h46.87c-2.67-44.26-15.75-71.24-23.44-78.69zm-41.08 8.28c-27.71 13.24-48.02 39.19-53.3 70.41h39.09c1.49-27.03 6.53-51.35 14.21-70.41zm0 172.82c-7.68-19.06-12.72-43.38-14.21-70.41h-39.09c5.28 31.22 25.59 57.17 53.3 70.41zM247.43 208h-46.87c2.66 44.26 15.74 71.24 23.43 78.69 7.7-7.45 20.78-34.43 23.44-78.69zM448 358.4V25.6c0-16-9.6-25.6-25.6-25.6H96C41.6 0 0 41.6 0 96v320c0 54.4 41.6 96 96 96h326.4c12.8 0 25.6-9.6 25.6-25.6v-16c0-6.4-3.2-12.8-9.6-19.2-3.2-16-3.2-60.8 0-73.6 6.4-3.2 9.6-9.6 9.6-19.2zM224 64c70.69 0 128 57.31 128 128s-57.31 128-128 128S96 262.69 96 192 153.31 64 224 64zm160 384H96c-19.2 0-32-12.8-32-32s16-32 32-32h288v64z"${_scopeId}></path></svg><h1 class="text-2xl font-bold" itemprop="name"${_scopeId}>${ssrInterpolate(unref(t)("courses"))}</h1></div>`);
            if (seoDescription.value) {
              _push2(`<div class="my-1 text-sm subtitle text-center" itemprop="description"${_scopeId}>${ssrInterpolate(seoDescription.value)}</div>`);
            } else {
              _push2(`<!---->`);
            }
            if (__props.useServerProcessing) {
              _push2(ssrRenderComponent(_sfc_main$4, {
                modelValue: q.value,
                "onUpdate:modelValue": ($event) => q.value = $event,
                found: __props.coursesFound,
                "view-mode": viewMode.value,
                "sort-value": sort.value,
                "sort-options": courseSortOptions,
                "default-sort": DEFAULT_SORT,
                "found-label": unref(t)("courses"),
                "search-placeholder": unref(t)("searchByName"),
                onSubmit: submitSearch,
                onReset: resetSearch,
                "onUpdate:viewMode": updateViewMode,
                "onUpdate:sortValue": updateSort
              }, null, _parent2, _scopeId));
            } else {
              _push2(ssrRenderComponent(_sfc_main$5, {
                modelValue: q.value,
                "onUpdate:modelValue": ($event) => q.value = $event,
                found: sortedCourses.value.length,
                "view-mode": viewMode.value,
                "sort-value": sort.value,
                "sort-options": courseSortOptions,
                "default-sort": DEFAULT_SORT,
                "found-label": unref(t)("courses"),
                "search-placeholder": unref(t)("searchByName"),
                onReset: resetSearch,
                "onUpdate:viewMode": updateViewMode,
                "onUpdate:sortValue": updateSort
              }, null, _parent2, _scopeId));
            }
            _push2(`<div${_scopeId}></div>`);
            if (displayedCourses.value.length === 0) {
              _push2(`<div class="mt-6 text-center text-slate-700 dark:text-slate-300"${_scopeId}>${ssrInterpolate(unref(t)("noData"))}</div>`);
            } else {
              _push2(`<section${ssrRenderAttr("aria-label", unref(t)("courses"))} itemprop="mainEntity"${_scopeId}>`);
              if (viewMode.value === "grid") {
                _push2(ssrRenderComponent(_sfc_main$6, {
                  courses: displayedCourses.value,
                  cols: gridCols.value
                }, null, _parent2, _scopeId));
              } else {
                _push2(ssrRenderComponent(_sfc_main$7, { courses: displayedCourses.value }, null, _parent2, _scopeId));
              }
              _push2(`</section>`);
            }
            if (__props.useServerProcessing) {
              _push2(ssrRenderComponent(_sfc_main$8, {
                "current-page": currentPage.value,
                "last-page": lastPage.value,
                found: __props.coursesFound,
                onPrev: goPrev,
                onNext: goNext,
                onGo: goToPage
              }, null, _parent2, _scopeId));
            } else {
              _push2(ssrRenderComponent(_sfc_main$9, {
                currentPage: frontendCurrentPage.value,
                "onUpdate:currentPage": ($event) => frontendCurrentPage.value = $event,
                "items-per-page": perPage.value,
                "total-items": sortedCourses.value.length
              }, null, _parent2, _scopeId));
            }
            _push2(ssrRenderComponent(_sfc_main$a, { videos: __props.mainVideos }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(SectionBanners, { banners: __props.mainBanners }, null, _parent2, _scopeId));
            _push2(`</div></article>`);
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
            _push2(`</div></div></main>`);
            _push2(ssrRenderComponent(_sfc_main$c, null, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(Progress, null, null, _parent2, _scopeId));
            if (isAdmin.value) {
              _push2(ssrRenderComponent(_sfc_main$d, {
                "setting-key": "publicSchoolCoursesProcessingMode",
                mode: __props.publicSchoolCoursesProcessingMode,
                "use-server-processing": __props.useServerProcessing,
                total: __props.coursesCount
              }, null, _parent2, _scopeId));
            } else {
              _push2(`<!---->`);
            }
          } else {
            return [
              createVNode(_sfc_main$2),
              createVNode("main", { class: "min-h-screen px-1 lg:px-6 max-w-full" }, [
                createVNode("div", { class: "mx-auto tracking-wider pt-20 lg:pt-44" }, [
                  createVNode("div", { class: "ext-color w-full min-w-0 py-3 px-1 flex flex-col lg:flex-row gap-4 rounded-3xl border-2 border-slate-300 dark:border-slate-500" }, [
                    showLeft.value ? (openBlock(), createBlock("aside", {
                      key: 0,
                      class: ["shrink-0 transition-all duration-300", leftCollapsed.value ? "lg:w-6" : "lg:w-72"]
                    }, [
                      createVNode(_sfc_main$3, {
                        "track-tree": trackTree.value,
                        collapsed: leftCollapsed.value,
                        onCollapsed: ($event) => leftCollapsed.value = $event
                      }, null, 8, ["track-tree", "collapsed", "onCollapsed"])
                    ], 2)) : createCommentVNode("", true),
                    createVNode("article", {
                      class: "w-full pb-6 slate-1",
                      itemscope: "",
                      itemtype: "https://schema.org/CollectionPage"
                    }, [
                      createVNode("div", { class: "mx-auto max-w-6xl" }, [
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
                              itemtype: "https://schema.org/ListItem"
                            }, [
                              createVNode(unref(Link), {
                                href: _ctx.route("home"),
                                class: "breadcrumb-link hover:underline",
                                itemprop: "item"
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
                            createVNode("li", { "aria-hidden": "true" }, [
                              createVNode("span", { class: "mx-2 breadcrumbs" }, " / ")
                            ]),
                            createVNode("li", {
                              class: "breadcrumbs",
                              itemprop: "itemListElement",
                              itemscope: "",
                              itemtype: "https://schema.org/ListItem",
                              "aria-current": "page"
                            }, [
                              createVNode("span", { itemprop: "name" }, toDisplayString(unref(t)("courses")), 1),
                              createVNode("meta", {
                                itemprop: "item",
                                content: canonicalUrl.value
                              }, null, 8, ["content"]),
                              createVNode("meta", {
                                itemprop: "position",
                                content: "2"
                              })
                            ])
                          ])
                        ]),
                        createVNode("div", { class: "my-3 flex flex-wrap items-center justify-center gap-3 title" }, [
                          (openBlock(), createBlock("svg", {
                            class: "shrink-0 h-5 w-5 text-slate-600/85 dark:text-slate-200/85",
                            fill: "currentColor",
                            viewBox: "0 0 448 512",
                            "aria-hidden": "true"
                          }, [
                            createVNode("path", { d: "M318.38 208h-39.09c-1.49 27.03-6.54 51.35-14.21 70.41 27.71-13.24 48.02-39.19 53.3-70.41zm0-32c-5.29-31.22-25.59-57.17-53.3-70.41 7.68 19.06 12.72 43.38 14.21 70.41h39.09zM224 97.31c-7.69 7.45-20.77 34.42-23.43 78.69h46.87c-2.67-44.26-15.75-71.24-23.44-78.69zm-41.08 8.28c-27.71 13.24-48.02 39.19-53.3 70.41h39.09c1.49-27.03 6.53-51.35 14.21-70.41zm0 172.82c-7.68-19.06-12.72-43.38-14.21-70.41h-39.09c5.28 31.22 25.59 57.17 53.3 70.41zM247.43 208h-46.87c2.66 44.26 15.74 71.24 23.43 78.69 7.7-7.45 20.78-34.43 23.44-78.69zM448 358.4V25.6c0-16-9.6-25.6-25.6-25.6H96C41.6 0 0 41.6 0 96v320c0 54.4 41.6 96 96 96h326.4c12.8 0 25.6-9.6 25.6-25.6v-16c0-6.4-3.2-12.8-9.6-19.2-3.2-16-3.2-60.8 0-73.6 6.4-3.2 9.6-9.6 9.6-19.2zM224 64c70.69 0 128 57.31 128 128s-57.31 128-128 128S96 262.69 96 192 153.31 64 224 64zm160 384H96c-19.2 0-32-12.8-32-32s16-32 32-32h288v64z" })
                          ])),
                          createVNode("h1", {
                            class: "text-2xl font-bold",
                            itemprop: "name"
                          }, toDisplayString(unref(t)("courses")), 1)
                        ]),
                        seoDescription.value ? (openBlock(), createBlock("div", {
                          key: 0,
                          class: "my-1 text-sm subtitle text-center",
                          itemprop: "description"
                        }, toDisplayString(seoDescription.value), 1)) : createCommentVNode("", true),
                        __props.useServerProcessing ? (openBlock(), createBlock(_sfc_main$4, {
                          key: 1,
                          modelValue: q.value,
                          "onUpdate:modelValue": ($event) => q.value = $event,
                          found: __props.coursesFound,
                          "view-mode": viewMode.value,
                          "sort-value": sort.value,
                          "sort-options": courseSortOptions,
                          "default-sort": DEFAULT_SORT,
                          "found-label": unref(t)("courses"),
                          "search-placeholder": unref(t)("searchByName"),
                          onSubmit: submitSearch,
                          onReset: resetSearch,
                          "onUpdate:viewMode": updateViewMode,
                          "onUpdate:sortValue": updateSort
                        }, null, 8, ["modelValue", "onUpdate:modelValue", "found", "view-mode", "sort-value", "found-label", "search-placeholder"])) : (openBlock(), createBlock(_sfc_main$5, {
                          key: 2,
                          modelValue: q.value,
                          "onUpdate:modelValue": ($event) => q.value = $event,
                          found: sortedCourses.value.length,
                          "view-mode": viewMode.value,
                          "sort-value": sort.value,
                          "sort-options": courseSortOptions,
                          "default-sort": DEFAULT_SORT,
                          "found-label": unref(t)("courses"),
                          "search-placeholder": unref(t)("searchByName"),
                          onReset: resetSearch,
                          "onUpdate:viewMode": updateViewMode,
                          "onUpdate:sortValue": updateSort
                        }, null, 8, ["modelValue", "onUpdate:modelValue", "found", "view-mode", "sort-value", "found-label", "search-placeholder"])),
                        createVNode("div", {
                          ref_key: "scrollTarget",
                          ref: scrollTarget
                        }, null, 512),
                        displayedCourses.value.length === 0 ? (openBlock(), createBlock("div", {
                          key: 3,
                          class: "mt-6 text-center text-slate-700 dark:text-slate-300"
                        }, toDisplayString(unref(t)("noData")), 1)) : (openBlock(), createBlock("section", {
                          key: 4,
                          "aria-label": unref(t)("courses"),
                          itemprop: "mainEntity"
                        }, [
                          viewMode.value === "grid" ? (openBlock(), createBlock(_sfc_main$6, {
                            key: 0,
                            courses: displayedCourses.value,
                            cols: gridCols.value
                          }, null, 8, ["courses", "cols"])) : (openBlock(), createBlock(_sfc_main$7, {
                            key: 1,
                            courses: displayedCourses.value
                          }, null, 8, ["courses"]))
                        ], 8, ["aria-label"])),
                        __props.useServerProcessing ? (openBlock(), createBlock(_sfc_main$8, {
                          key: 5,
                          "current-page": currentPage.value,
                          "last-page": lastPage.value,
                          found: __props.coursesFound,
                          onPrev: goPrev,
                          onNext: goNext,
                          onGo: goToPage
                        }, null, 8, ["current-page", "last-page", "found"])) : (openBlock(), createBlock(_sfc_main$9, {
                          key: 6,
                          currentPage: frontendCurrentPage.value,
                          "onUpdate:currentPage": ($event) => frontendCurrentPage.value = $event,
                          "items-per-page": perPage.value,
                          "total-items": sortedCourses.value.length
                        }, null, 8, ["currentPage", "onUpdate:currentPage", "items-per-page", "total-items"])),
                        createVNode(_sfc_main$a, { videos: __props.mainVideos }, null, 8, ["videos"]),
                        createVNode(SectionBanners, { banners: __props.mainBanners }, null, 8, ["banners"])
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
                  ])
                ])
              ]),
              createVNode(_sfc_main$c),
              createVNode(Progress),
              isAdmin.value ? (openBlock(), createBlock(_sfc_main$d, {
                key: 0,
                "setting-key": "publicSchoolCoursesProcessingMode",
                mode: __props.publicSchoolCoursesProcessingMode,
                "use-server-processing": __props.useServerProcessing,
                total: __props.coursesCount
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
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Pages/Public/Default/School/SchoolCourses/Index.vue");
  return _sfc_setup ? _sfc_setup(props, ctx) : void 0;
};
export {
  _sfc_main as default
};
