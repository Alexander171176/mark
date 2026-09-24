import { computed, ref, watch, onMounted, unref, withCtx, createVNode, resolveDynamicComponent, createBlock, createCommentVNode, toDisplayString, openBlock, Fragment, useSSRContext } from "vue";
import { ssrRenderComponent, ssrInterpolate, ssrRenderAttr, ssrRenderVNode, ssrRenderClass, ssrRenderStyle } from "vue/server-renderer";
import { usePage, Head, Link, router } from "@inertiajs/vue3";
import { useI18n } from "vue-i18n";
import { _ as _sfc_main$1 } from "./DefaultLayout-6zxGxPtK.js";
import { _ as _sfc_main$2, a as _sfc_main$b, P as Progress } from "./Progress-CyfOKcqP.js";
import { _ as _sfc_main$7, a as _sfc_main$8, b as _sfc_main$c } from "./PublicAdminBottomPanel-BhLEQMoJ.js";
import { _ as _sfc_main$3, a as _sfc_main$a } from "./RightSidebarSchool-DlAegojf.js";
import { _ as _sfc_main$4 } from "./EntityPageToolbar-DT32FtSd.js";
import { _ as _sfc_main$5 } from "./CourseGrid-DoYn9a32.js";
import { _ as _sfc_main$6 } from "./CourseRows-Q6HeVebv.js";
import { a as unwrap, u as unwrapList, _ as _sfc_main$9, S as SectionBanners } from "./SectionBanners-Cdgmr0Bw.js";
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
import "./ProcessingModeSwitcher-BJvzFf6_.js";
import "./ViewModeToggle-DMCnQ0wo.js";
import "./UniversalImageSlider-Cu2Xndcn.js";
import "./EntityStats-Dy-sjN5R.js";
import "./LikeButtonEntity-ZC4HMEAO.js";
const DEFAULT_SORT = "idDesc";
const VIEW_KEY = "public_school_courses_view";
const LEFT_SIDEBAR_KEY = "public_left_sidebar_collapsed";
const RIGHT_SIDEBAR_KEY = "public_right_sidebar_collapsed";
const _sfc_main = {
  __name: "Show",
  __ssrInlineRender: true,
  props: {
    title: { type: String, default: "" },
    canLogin: { type: Boolean, default: false },
    canRegister: { type: Boolean, default: false },
    locale: { type: String, default: "ru" },
    hashtag: { type: Object, default: () => ({}) },
    useServerProcessing: { type: Boolean, default: false },
    publicSchoolCoursesProcessingMode: { type: String, default: "server" },
    courses: { type: [Array, Object], default: () => [] },
    coursesCount: { type: Number, default: 0 },
    coursesFound: { type: Number, default: 0 },
    filters: { type: Object, default: () => ({}) },
    trackTree: { type: Array, default: () => [] },
    mainVideos: { type: [Array, Object], default: () => [] },
    mainBanners: { type: [Array, Object], default: () => [] }
  },
  setup(__props) {
    var _a, _b, _c;
    const { t } = useI18n();
    const page = usePage();
    const props = __props;
    const normalizeText = (value) => String(value ?? "").trim().toLocaleLowerCase();
    const absoluteUrl = (value) => {
      const url = String(value ?? "").trim();
      if (!url) return "";
      try {
        return new URL(url, typeof window !== "undefined" ? window.location.origin : void 0).href;
      } catch {
        return url;
      }
    };
    const hashtag = computed(() => unwrap(props.hashtag) ?? {});
    const translation = computed(() => {
      var _a2;
      return ((_a2 = hashtag.value) == null ? void 0 : _a2.translation) ?? {};
    });
    const hashtagName = computed(() => {
      var _a2, _b2;
      return ((_a2 = translation.value) == null ? void 0 : _a2.name) || ((_b2 = hashtag.value) == null ? void 0 : _b2.slug) || t("hashtags");
    });
    const hashtagShort = computed(() => {
      var _a2;
      return ((_a2 = translation.value) == null ? void 0 : _a2.short) || "";
    });
    const hashtagDescription = computed(() => {
      var _a2;
      return ((_a2 = translation.value) == null ? void 0 : _a2.description) || "";
    });
    const hashtagCoursesCount = computed(() => {
      var _a2;
      return Number(((_a2 = hashtag.value) == null ? void 0 : _a2.courses_count) ?? props.coursesCount ?? 0);
    });
    const hashtagModulesCount = computed(() => {
      var _a2;
      return Number(((_a2 = hashtag.value) == null ? void 0 : _a2.modules_count) ?? 0);
    });
    const hashtagLessonsCount = computed(() => {
      var _a2;
      return Number(((_a2 = hashtag.value) == null ? void 0 : _a2.lessons_count) ?? 0);
    });
    const seoTitle = computed(() => {
      var _a2;
      return ((_a2 = translation.value) == null ? void 0 : _a2.meta_title) || hashtagName.value;
    });
    const seoDescription = computed(() => {
      var _a2;
      return ((_a2 = translation.value) == null ? void 0 : _a2.meta_desc) || hashtagShort.value || "";
    });
    const seoKeywords = computed(() => {
      var _a2;
      return ((_a2 = translation.value) == null ? void 0 : _a2.meta_keywords) || "";
    });
    const contentLocale = computed(() => {
      var _a2;
      return ((_a2 = translation.value) == null ? void 0 : _a2.locale) || props.locale || "ru";
    });
    const ogLocale = computed(() => {
      const locale = contentLocale.value.replace("-", "_");
      if (locale === "ru") return "ru_RU";
      if (locale === "en") return "en_US";
      if (locale === "kk") return "kk_KZ";
      return locale;
    });
    const canonicalUrl = computed(() => {
      var _a2;
      if (!((_a2 = hashtag.value) == null ? void 0 : _a2.slug)) return "";
      return absoluteUrl(route("public.schoolHashtags.show", { slug: hashtag.value.slug }));
    });
    const coursesIndexUrl = computed(() => absoluteUrl(route("public.schoolCourses.index")));
    const homeUrl = computed(() => absoluteUrl(route("home")));
    const dcSubject = computed(() => seoKeywords.value || hashtagName.value);
    const jsonLd = computed(() => {
      if (!canonicalUrl.value) return null;
      const graph = [
        {
          "@type": "CollectionPage",
          "@id": canonicalUrl.value,
          url: canonicalUrl.value,
          name: seoTitle.value,
          inLanguage: contentLocale.value,
          isPartOf: {
            "@type": "WebSite",
            url: homeUrl.value
          },
          mainEntity: {
            "@id": `${canonicalUrl.value}#courses`
          }
        },
        {
          "@type": "BreadcrumbList",
          "@id": `${canonicalUrl.value}#breadcrumb`,
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
              name: `#${hashtagName.value}`,
              item: canonicalUrl.value
            }
          ]
        },
        {
          "@type": "ItemList",
          "@id": `${canonicalUrl.value}#courses`,
          name: `${t("courses")}: #${hashtagName.value}`,
          numberOfItems: Number(props.coursesCount ?? hashtagCoursesCount.value),
          itemListElement: coursesData.value.map((course, index) => ({
            "@type": "ListItem",
            position: index + 1,
            name: getCourseTitle(course),
            url: (course == null ? void 0 : course.slug) ? absoluteUrl(route("public.schoolCourses.show", { slug: course.slug })) : void 0
          })).filter((item) => item.url)
        }
      ];
      if (seoDescription.value) graph[0].description = seoDescription.value;
      if (seoKeywords.value) graph[0].keywords = seoKeywords.value;
      return {
        "@context": "https://schema.org",
        "@graph": graph
      };
    });
    const coursesData = computed(() => unwrapList(props.courses));
    const qCourses = ref(String(((_a = props.filters) == null ? void 0 : _a.q_courses) ?? ""));
    const perPageCourses = computed(() => {
      var _a2;
      return Number(((_a2 = props.filters) == null ? void 0 : _a2.per_page_courses) ?? 12);
    });
    const sortCourses = ref(String(((_b = props.filters) == null ? void 0 : _b.sort_courses) ?? DEFAULT_SORT));
    const courseSortOptions = computed(() => [
      { value: "idDesc", label: t("idDesc") },
      { value: "idAsc", label: t("idAsc") },
      { value: "sortAsc", label: t("sortDefault") },
      { value: "sortDesc", label: t("sortReverse") },
      { value: "titleAsc", label: t("sortNameAsc") },
      { value: "titleDesc", label: t("sortNameDesc") },
      { value: "studentsCountAsc", label: `${t("students")} ↑` },
      { value: "studentsCountDesc", label: `${t("students")} ↓` },
      { value: "viewsAsc", label: `${t("views")} ↑` },
      { value: "viewsDesc", label: `${t("views")} ↓` },
      { value: "likesAsc", label: `${t("likes")} ↑` },
      { value: "likesDesc", label: `${t("likes")} ↓` },
      { value: "popularityAsc", label: `${t("popularity")} ↑` },
      { value: "popularityDesc", label: `${t("popularity")} ↓` },
      { value: "ratingAvgAsc", label: `${t("rating")} ↑` },
      { value: "ratingAvgDesc", label: `${t("rating")} ↓` },
      { value: "ratingCountAsc", label: `${t("rating")} ↑` },
      { value: "ratingCountDesc", label: `${t("rating")} ↓` },
      { value: "difficultyAsc", label: `${t("difficulty")} ↑` },
      { value: "difficultyDesc", label: `${t("difficulty")} ↓` },
      { value: "durationAsc", label: `${t("duration")} ↑` },
      { value: "durationDesc", label: `${t("duration")} ↓` },
      { value: "levelAsc", label: `${t("level")} ↑` },
      { value: "levelDesc", label: `${t("level")} ↓` },
      { value: "modulesAsc", label: `${t("modules")} ↑` },
      { value: "modulesDesc", label: `${t("modules")} ↓` },
      { value: "lessonsAsc", label: `${t("lessons")} ↑` },
      { value: "lessonsDesc", label: `${t("lessons")} ↓` },
      { value: "tracksAsc", label: `${t("tracks")} ↑` },
      { value: "tracksDesc", label: `${t("tracks")} ↓` },
      { value: "hashtagsAsc", label: `${t("hashtags")} ↑` },
      { value: "hashtagsDesc", label: `${t("hashtags")} ↓` },
      { value: "reviewsAsc", label: `${t("reviews")} ↑` },
      { value: "reviewsDesc", label: `${t("reviews")} ↓` },
      { value: "publishedAtAsc", label: `${t("date")} ↑` },
      { value: "publishedAtDesc", label: `${t("date")} ↓` }
    ]);
    const getStoredView = () => {
      if (typeof window === "undefined") return "grid";
      try {
        const value = localStorage.getItem(VIEW_KEY);
        return ["grid", "rows"].includes(value) ? value : "grid";
      } catch {
        return "grid";
      }
    };
    const viewMode = ref(getStoredView());
    watch(viewMode, (value) => {
      if (typeof window === "undefined") return;
      try {
        localStorage.setItem(VIEW_KEY, value);
      } catch {
      }
    });
    const getCourseTitle = (course) => {
      var _a2;
      return ((_a2 = course == null ? void 0 : course.translation) == null ? void 0 : _a2.title) || "";
    };
    const getCourseShort = (course) => {
      var _a2;
      return ((_a2 = course == null ? void 0 : course.translation) == null ? void 0 : _a2.short) || "";
    };
    const getInstructorName = (course) => {
      var _a2, _b2, _c2, _d;
      return ((_b2 = (_a2 = course == null ? void 0 : course.instructorProfile) == null ? void 0 : _a2.translation) == null ? void 0 : _b2.title) || ((_d = (_c2 = course == null ? void 0 : course.instructorProfile) == null ? void 0 : _c2.user) == null ? void 0 : _d.name) || "";
    };
    const frontendFilteredCourses = computed(() => {
      if (props.useServerProcessing) return coursesData.value;
      const query = normalizeText(qCourses.value);
      if (!query) return coursesData.value;
      return coursesData.value.filter((course) => [
        course == null ? void 0 : course.slug,
        getCourseTitle(course),
        getCourseShort(course),
        getInstructorName(course)
      ].some((value) => normalizeText(value).includes(query)));
    });
    const compareText = (a, b) => String(a ?? "").localeCompare(String(b ?? ""), props.locale, { sensitivity: "base" });
    const compareNumber = (a, b) => Number(a ?? 0) - Number(b ?? 0);
    const frontendSortedCourses = computed(() => {
      if (props.useServerProcessing) return frontendFilteredCourses.value;
      const list = [...frontendFilteredCourses.value];
      list.sort((a, b) => {
        switch (sortCourses.value) {
          case "idAsc":
            return compareNumber(a == null ? void 0 : a.id, b == null ? void 0 : b.id);
          case "idDesc":
            return compareNumber(b == null ? void 0 : b.id, a == null ? void 0 : a.id);
          case "sortAsc":
            return compareNumber(a == null ? void 0 : a.sort, b == null ? void 0 : b.sort) || compareNumber(b == null ? void 0 : b.id, a == null ? void 0 : a.id);
          case "sortDesc":
            return compareNumber(b == null ? void 0 : b.sort, a == null ? void 0 : a.sort) || compareNumber(b == null ? void 0 : b.id, a == null ? void 0 : a.id);
          case "titleAsc":
            return compareText(getCourseTitle(a), getCourseTitle(b)) || compareNumber(b == null ? void 0 : b.id, a == null ? void 0 : a.id);
          case "titleDesc":
            return compareText(getCourseTitle(b), getCourseTitle(a)) || compareNumber(b == null ? void 0 : b.id, a == null ? void 0 : a.id);
          case "studentsCountAsc":
            return compareNumber(a == null ? void 0 : a.students_count, b == null ? void 0 : b.students_count) || compareNumber(b == null ? void 0 : b.id, a == null ? void 0 : a.id);
          case "studentsCountDesc":
            return compareNumber(b == null ? void 0 : b.students_count, a == null ? void 0 : a.students_count) || compareNumber(b == null ? void 0 : b.id, a == null ? void 0 : a.id);
          case "viewsAsc":
            return compareNumber(a == null ? void 0 : a.views, b == null ? void 0 : b.views) || compareNumber(b == null ? void 0 : b.id, a == null ? void 0 : a.id);
          case "viewsDesc":
            return compareNumber(b == null ? void 0 : b.views, a == null ? void 0 : a.views) || compareNumber(b == null ? void 0 : b.id, a == null ? void 0 : a.id);
          case "likesAsc":
            return compareNumber(a == null ? void 0 : a.likes_count, b == null ? void 0 : b.likes_count) || compareNumber(b == null ? void 0 : b.id, a == null ? void 0 : a.id);
          case "likesDesc":
            return compareNumber(b == null ? void 0 : b.likes_count, a == null ? void 0 : a.likes_count) || compareNumber(b == null ? void 0 : b.id, a == null ? void 0 : a.id);
          case "popularityAsc":
            return compareNumber(a == null ? void 0 : a.popularity, b == null ? void 0 : b.popularity) || compareNumber(b == null ? void 0 : b.id, a == null ? void 0 : a.id);
          case "popularityDesc":
            return compareNumber(b == null ? void 0 : b.popularity, a == null ? void 0 : a.popularity) || compareNumber(b == null ? void 0 : b.id, a == null ? void 0 : a.id);
          case "ratingAvgAsc":
            return compareNumber(a == null ? void 0 : a.rating_avg, b == null ? void 0 : b.rating_avg) || compareNumber(b == null ? void 0 : b.id, a == null ? void 0 : a.id);
          case "ratingAvgDesc":
            return compareNumber(b == null ? void 0 : b.rating_avg, a == null ? void 0 : a.rating_avg) || compareNumber(b == null ? void 0 : b.id, a == null ? void 0 : a.id);
          case "ratingCountAsc":
            return compareNumber(a == null ? void 0 : a.rating_count, b == null ? void 0 : b.rating_count) || compareNumber(b == null ? void 0 : b.id, a == null ? void 0 : a.id);
          case "ratingCountDesc":
            return compareNumber(b == null ? void 0 : b.rating_count, a == null ? void 0 : a.rating_count) || compareNumber(b == null ? void 0 : b.id, a == null ? void 0 : a.id);
          case "difficultyAsc":
            return compareNumber(a == null ? void 0 : a.difficulty, b == null ? void 0 : b.difficulty) || compareNumber(b == null ? void 0 : b.id, a == null ? void 0 : a.id);
          case "difficultyDesc":
            return compareNumber(b == null ? void 0 : b.difficulty, a == null ? void 0 : a.difficulty) || compareNumber(b == null ? void 0 : b.id, a == null ? void 0 : a.id);
          case "durationAsc":
            return compareNumber(a == null ? void 0 : a.duration, b == null ? void 0 : b.duration) || compareNumber(b == null ? void 0 : b.id, a == null ? void 0 : a.id);
          case "durationDesc":
            return compareNumber(b == null ? void 0 : b.duration, a == null ? void 0 : a.duration) || compareNumber(b == null ? void 0 : b.id, a == null ? void 0 : a.id);
          case "levelAsc":
            return compareText(a == null ? void 0 : a.level, b == null ? void 0 : b.level) || compareNumber(b == null ? void 0 : b.id, a == null ? void 0 : a.id);
          case "levelDesc":
            return compareText(b == null ? void 0 : b.level, a == null ? void 0 : a.level) || compareNumber(b == null ? void 0 : b.id, a == null ? void 0 : a.id);
          case "modulesAsc":
            return compareNumber(a == null ? void 0 : a.modules_count, b == null ? void 0 : b.modules_count) || compareNumber(b == null ? void 0 : b.id, a == null ? void 0 : a.id);
          case "modulesDesc":
            return compareNumber(b == null ? void 0 : b.modules_count, a == null ? void 0 : a.modules_count) || compareNumber(b == null ? void 0 : b.id, a == null ? void 0 : a.id);
          case "lessonsAsc":
            return compareNumber(a == null ? void 0 : a.lessons_count, b == null ? void 0 : b.lessons_count) || compareNumber(b == null ? void 0 : b.id, a == null ? void 0 : a.id);
          case "lessonsDesc":
            return compareNumber(b == null ? void 0 : b.lessons_count, a == null ? void 0 : a.lessons_count) || compareNumber(b == null ? void 0 : b.id, a == null ? void 0 : a.id);
          case "tracksAsc":
            return compareNumber(a == null ? void 0 : a.tracks_count, b == null ? void 0 : b.tracks_count) || compareNumber(b == null ? void 0 : b.id, a == null ? void 0 : a.id);
          case "tracksDesc":
            return compareNumber(b == null ? void 0 : b.tracks_count, a == null ? void 0 : a.tracks_count) || compareNumber(b == null ? void 0 : b.id, a == null ? void 0 : a.id);
          case "hashtagsAsc":
            return compareNumber(a == null ? void 0 : a.hashtags_count, b == null ? void 0 : b.hashtags_count) || compareNumber(b == null ? void 0 : b.id, a == null ? void 0 : a.id);
          case "hashtagsDesc":
            return compareNumber(b == null ? void 0 : b.hashtags_count, a == null ? void 0 : a.hashtags_count) || compareNumber(b == null ? void 0 : b.id, a == null ? void 0 : a.id);
          case "reviewsAsc":
            return compareNumber(a == null ? void 0 : a.reviews_count, b == null ? void 0 : b.reviews_count) || compareNumber(b == null ? void 0 : b.id, a == null ? void 0 : a.id);
          case "reviewsDesc":
            return compareNumber(b == null ? void 0 : b.reviews_count, a == null ? void 0 : a.reviews_count) || compareNumber(b == null ? void 0 : b.id, a == null ? void 0 : a.id);
          case "publishedAtAsc":
            return compareText(a == null ? void 0 : a.published_at, b == null ? void 0 : b.published_at) || compareNumber(b == null ? void 0 : b.id, a == null ? void 0 : a.id);
          case "publishedAtDesc":
            return compareText(b == null ? void 0 : b.published_at, a == null ? void 0 : a.published_at) || compareNumber(b == null ? void 0 : b.id, a == null ? void 0 : a.id);
          default:
            return compareNumber(a == null ? void 0 : a.sort, b == null ? void 0 : b.sort) || compareNumber(b == null ? void 0 : b.id, a == null ? void 0 : a.id);
        }
      });
      return list;
    });
    const frontendCurrentPage = ref(1);
    watch([qCourses, sortCourses], () => {
      if (!props.useServerProcessing) frontendCurrentPage.value = 1;
    });
    const effectiveCoursesFound = computed(
      () => props.useServerProcessing ? Number(props.coursesFound ?? 0) : frontendSortedCourses.value.length
    );
    const frontendPaginatedCourses = computed(() => {
      if (props.useServerProcessing) return coursesData.value;
      const perPage = Math.max(1, perPageCourses.value);
      const start = (frontendCurrentPage.value - 1) * perPage;
      return frontendSortedCourses.value.slice(start, start + perPage);
    });
    const displayedCourses = computed(
      () => props.useServerProcessing ? coursesData.value : frontendPaginatedCourses.value
    );
    const currentPage = computed(
      () => {
        var _a2, _b2, _c2;
        return Number(((_b2 = (_a2 = props.courses) == null ? void 0 : _a2.meta) == null ? void 0 : _b2.current_page) ?? ((_c2 = props.courses) == null ? void 0 : _c2.current_page) ?? 1) || 1;
      }
    );
    const lastPage = computed(
      () => {
        var _a2, _b2, _c2;
        return Number(((_b2 = (_a2 = props.courses) == null ? void 0 : _a2.meta) == null ? void 0 : _b2.last_page) ?? ((_c2 = props.courses) == null ? void 0 : _c2.last_page) ?? 1) || 1;
      }
    );
    const loadServerCourses = (pageNumber = 1) => {
      if (!props.useServerProcessing || !canonicalUrl.value) return;
      router.get(
        canonicalUrl.value,
        {
          q_courses: qCourses.value || void 0,
          sort_courses: sortCourses.value,
          page_courses: pageNumber
        },
        {
          preserveScroll: true,
          preserveState: true,
          replace: true
        }
      );
    };
    const applyFilters = () => {
      if (props.useServerProcessing) loadServerCourses(1);
      else frontendCurrentPage.value = 1;
    };
    const resetFilters = () => {
      qCourses.value = "";
      sortCourses.value = DEFAULT_SORT;
      if (props.useServerProcessing) loadServerCourses(1);
      else frontendCurrentPage.value = 1;
    };
    const goToPage = (pageNumber) => {
      const target = Math.min(Math.max(1, Number(pageNumber) || 1), lastPage.value);
      loadServerCourses(target);
    };
    const goPrev = () => {
      if (currentPage.value > 1) goToPage(currentPage.value - 1);
    };
    const goNext = () => {
      if (currentPage.value < lastPage.value) goToPage(currentPage.value + 1);
    };
    const siteSettings = ((_c = page.props) == null ? void 0 : _c.siteSettings) || {};
    const isAdmin = computed(() => {
      var _a2;
      return ((_a2 = page.props) == null ? void 0 : _a2.isAdmin) === true;
    });
    const showLeft = computed(
      () => !(siteSettings == null ? void 0 : siteSettings.ViewLeftColumn) || siteSettings.ViewLeftColumn === "true"
    );
    const showRight = computed(
      () => !(siteSettings == null ? void 0 : siteSettings.ViewRightColumn) || siteSettings.ViewRightColumn === "true"
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
        localStorage.setItem(key, String(Boolean(value)));
      } catch {
      }
    };
    onMounted(() => {
      leftCollapsed.value = readStoredBoolean(LEFT_SIDEBAR_KEY, true);
      rightCollapsed.value = readStoredBoolean(RIGHT_SIDEBAR_KEY, true);
    });
    const setLeftCollapsed = (value) => {
      leftCollapsed.value = Boolean(value);
      writeStoredBoolean(LEFT_SIDEBAR_KEY, leftCollapsed.value);
    };
    const setRightCollapsed = (value) => {
      rightCollapsed.value = Boolean(value);
      writeStoredBoolean(RIGHT_SIDEBAR_KEY, rightCollapsed.value);
    };
    const courseGridCols = computed(() => {
      const leftExpanded = showLeft.value && !leftCollapsed.value;
      const rightExpanded = showRight.value && !rightCollapsed.value;
      if (leftExpanded && rightExpanded) return 2;
      if (leftExpanded || rightExpanded) return 3;
      return 4;
    });
    const trackTree = computed(() => Array.isArray(props.trackTree) ? props.trackTree : []);
    const mainVideos = computed(() => unwrapList(props.mainVideos));
    const mainBanners = computed(() => unwrapList(props.mainBanners));
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
            _push2(`<meta name="robots" content="index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1"${_scopeId}><meta name="googlebot" content="index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1"${_scopeId}>`);
            if (canonicalUrl.value) {
              _push2(`<link rel="canonical"${ssrRenderAttr("href", canonicalUrl.value)}${_scopeId}>`);
            } else {
              _push2(`<!---->`);
            }
            _push2(`<meta property="og:type" content="website"${_scopeId}><meta property="og:title"${ssrRenderAttr("content", seoTitle.value)}${_scopeId}>`);
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
            _push2(`<meta property="og:locale"${ssrRenderAttr("content", ogLocale.value)}${_scopeId}><meta name="twitter:card" content="summary"${_scopeId}><meta name="twitter:title"${ssrRenderAttr("content", seoTitle.value)}${_scopeId}>`);
            if (seoDescription.value) {
              _push2(`<meta name="twitter:description"${ssrRenderAttr("content", seoDescription.value)}${_scopeId}>`);
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
            _push2(`<meta name="DC.language"${ssrRenderAttr("content", contentLocale.value)}${_scopeId}>`);
            if (canonicalUrl.value) {
              _push2(`<meta name="DC.identifier"${ssrRenderAttr("content", canonicalUrl.value)}${_scopeId}>`);
            } else {
              _push2(`<!---->`);
            }
            _push2(`<meta name="DC.type" content="Collection"${_scopeId}><meta name="DC.format" content="text/html"${_scopeId}>`);
            if (jsonLd.value) {
              ssrRenderVNode(_push2, createVNode(resolveDynamicComponent("script"), { type: "application/ld+json" }, null), _parent2, _scopeId);
            } else {
              _push2(`<!---->`);
            }
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
                content: "index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1"
              }),
              createVNode("meta", {
                name: "googlebot",
                content: "index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1"
              }),
              canonicalUrl.value ? (openBlock(), createBlock("link", {
                key: 2,
                rel: "canonical",
                href: canonicalUrl.value
              }, null, 8, ["href"])) : createCommentVNode("", true),
              createVNode("meta", {
                property: "og:type",
                content: "website"
              }),
              createVNode("meta", {
                property: "og:title",
                content: seoTitle.value
              }, null, 8, ["content"]),
              seoDescription.value ? (openBlock(), createBlock("meta", {
                key: 3,
                property: "og:description",
                content: seoDescription.value
              }, null, 8, ["content"])) : createCommentVNode("", true),
              canonicalUrl.value ? (openBlock(), createBlock("meta", {
                key: 4,
                property: "og:url",
                content: canonicalUrl.value
              }, null, 8, ["content"])) : createCommentVNode("", true),
              createVNode("meta", {
                property: "og:locale",
                content: ogLocale.value
              }, null, 8, ["content"]),
              createVNode("meta", {
                name: "twitter:card",
                content: "summary"
              }),
              createVNode("meta", {
                name: "twitter:title",
                content: seoTitle.value
              }, null, 8, ["content"]),
              seoDescription.value ? (openBlock(), createBlock("meta", {
                key: 5,
                name: "twitter:description",
                content: seoDescription.value
              }, null, 8, ["content"])) : createCommentVNode("", true),
              createVNode("meta", {
                name: "DC.title",
                content: seoTitle.value
              }, null, 8, ["content"]),
              seoDescription.value ? (openBlock(), createBlock("meta", {
                key: 6,
                name: "DC.description",
                content: seoDescription.value
              }, null, 8, ["content"])) : createCommentVNode("", true),
              dcSubject.value ? (openBlock(), createBlock("meta", {
                key: 7,
                name: "DC.subject",
                content: dcSubject.value
              }, null, 8, ["content"])) : createCommentVNode("", true),
              createVNode("meta", {
                name: "DC.language",
                content: contentLocale.value
              }, null, 8, ["content"]),
              canonicalUrl.value ? (openBlock(), createBlock("meta", {
                key: 8,
                name: "DC.identifier",
                content: canonicalUrl.value
              }, null, 8, ["content"])) : createCommentVNode("", true),
              createVNode("meta", {
                name: "DC.type",
                content: "Collection"
              }),
              createVNode("meta", {
                name: "DC.format",
                content: "text/html"
              }),
              jsonLd.value ? (openBlock(), createBlock(resolveDynamicComponent("script"), {
                key: 9,
                type: "application/ld+json",
                textContent: toDisplayString(JSON.stringify(jsonLd.value))
              }, null, 8, ["textContent"])) : createCommentVNode("", true)
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
              _push2(`<aside class="${ssrRenderClass([leftCollapsed.value ? "lg:w-6" : "lg:w-72", "shrink-0 pl-3 transition-all duration-300"])}"${_scopeId}>`);
              _push2(ssrRenderComponent(_sfc_main$3, {
                "track-tree": trackTree.value,
                collapsed: leftCollapsed.value,
                onCollapsed: setLeftCollapsed
              }, null, _parent2, _scopeId));
              _push2(`</aside>`);
            } else {
              _push2(`<!---->`);
            }
            _push2(`<article itemscope itemtype="https://schema.org/CollectionPage"${ssrRenderAttr("itemid", canonicalUrl.value || void 0)} class="slate-1 w-full min-w-0 pb-6"${_scopeId}><meta itemprop="name"${ssrRenderAttr("content", seoTitle.value)}${_scopeId}>`);
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
            if (canonicalUrl.value) {
              _push2(`<link itemprop="url"${ssrRenderAttr("href", canonicalUrl.value)}${_scopeId}>`);
            } else {
              _push2(`<!---->`);
            }
            _push2(`<meta itemprop="inLanguage"${ssrRenderAttr("content", contentLocale.value)}${_scopeId}><div class="mx-auto max-w-6xl"${_scopeId}><nav class="mb-3 text-sm" aria-label="Breadcrumb" itemscope itemtype="https://schema.org/BreadcrumbList"${_scopeId}><ol class="flex flex-wrap items-center font-semibold"${_scopeId}><li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem" class="flex items-center"${_scopeId}>`);
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
            _push2(`<meta itemprop="position" content="1"${_scopeId}></li><li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem" class="flex items-center"${_scopeId}><span class="mx-2 breadcrumbs"${_scopeId}>/</span>`);
            _push2(ssrRenderComponent(unref(Link), {
              itemprop: "item",
              href: _ctx.route("public.schoolCourses.index"),
              class: "breadcrumb-link hover:underline"
            }, {
              default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                if (_push3) {
                  _push3(`<span itemprop="name"${_scopeId2}>${ssrInterpolate(unref(t)("courses"))}</span>`);
                } else {
                  return [
                    createVNode("span", { itemprop: "name" }, toDisplayString(unref(t)("courses")), 1)
                  ];
                }
              }),
              _: 1
            }, _parent2, _scopeId));
            _push2(`<meta itemprop="position" content="2"${_scopeId}></li><li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem" class="flex items-center" aria-current="page"${_scopeId}><span class="mx-2 breadcrumbs"${_scopeId}>/</span><span itemprop="name" class="breadcrumbs"${_scopeId}> #${ssrInterpolate(hashtagName.value)}</span>`);
            if (canonicalUrl.value) {
              _push2(`<link itemprop="item"${ssrRenderAttr("href", canonicalUrl.value)}${_scopeId}>`);
            } else {
              _push2(`<!---->`);
            }
            _push2(`<meta itemprop="position" content="3"${_scopeId}></li></ol></nav><header class="mb-5 rounded-md border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-700 dark:bg-gray-900"${_scopeId}><div class="flex flex-wrap items-center justify-center gap-3"${_scopeId}><h1 itemprop="headline" class="text-2xl font-bold text-slate-800 dark:text-slate-100"${_scopeId}> #${ssrInterpolate(hashtagName.value)}</h1>`);
            if (hashtag.value.color) {
              _push2(`<span class="h-4 w-4 rounded-full border border-slate-400" style="${ssrRenderStyle({ backgroundColor: hashtag.value.color })}" aria-hidden="true"${_scopeId}></span>`);
            } else {
              _push2(`<!---->`);
            }
            _push2(`</div>`);
            if (hashtagShort.value) {
              _push2(`<div itemprop="abstract" class="mt-2 text-center text-sm text-slate-600 dark:text-slate-300"${_scopeId}>${ssrInterpolate(hashtagShort.value)}</div>`);
            } else {
              _push2(`<!---->`);
            }
            if (hashtagDescription.value) {
              _push2(`<div itemprop="text" class="mt-4 text-sm text-slate-700 dark:text-slate-300"${_scopeId}>${hashtagDescription.value ?? ""}</div>`);
            } else {
              _push2(`<!---->`);
            }
            _push2(`<div class="mt-4 flex flex-wrap justify-center gap-x-5 gap-y-2 text-xs text-slate-500 dark:text-slate-400"${_scopeId}>`);
            if (hashtagCoursesCount.value) {
              _push2(`<span${_scopeId}>${ssrInterpolate(unref(t)("courses"))}: ${ssrInterpolate(hashtagCoursesCount.value)}</span>`);
            } else {
              _push2(`<!---->`);
            }
            if (hashtagModulesCount.value) {
              _push2(`<span${_scopeId}>${ssrInterpolate(unref(t)("modules"))}: ${ssrInterpolate(hashtagModulesCount.value)}</span>`);
            } else {
              _push2(`<!---->`);
            }
            if (hashtagLessonsCount.value) {
              _push2(`<span${_scopeId}>${ssrInterpolate(unref(t)("lessons"))}: ${ssrInterpolate(hashtagLessonsCount.value)}</span>`);
            } else {
              _push2(`<!---->`);
            }
            if (hashtag.value.views) {
              _push2(`<span${_scopeId}>${ssrInterpolate(unref(t)("views"))}: ${ssrInterpolate(hashtag.value.views)}</span>`);
            } else {
              _push2(`<!---->`);
            }
            if (hashtag.value.likes) {
              _push2(`<span${_scopeId}>${ssrInterpolate(unref(t)("likes"))}: ${ssrInterpolate(hashtag.value.likes)}</span>`);
            } else {
              _push2(`<!---->`);
            }
            _push2(`</div></header>`);
            _push2(ssrRenderComponent(_sfc_main$4, {
              modelValue: qCourses.value,
              "onUpdate:modelValue": ($event) => qCourses.value = $event,
              "view-mode": viewMode.value,
              "onUpdate:viewMode": ($event) => viewMode.value = $event,
              "sort-value": sortCourses.value,
              "onUpdate:sortValue": ($event) => sortCourses.value = $event,
              found: effectiveCoursesFound.value,
              "sort-options": courseSortOptions.value,
              "default-sort": DEFAULT_SORT,
              "found-label": unref(t)("courses"),
              "search-placeholder": unref(t)("searchByName"),
              onSubmit: applyFilters,
              onReset: resetFilters
            }, null, _parent2, _scopeId));
            if (!displayedCourses.value.length) {
              _push2(`<div class="rounded-md border border-gray-200 bg-white p-8 text-center text-sm text-slate-500 dark:border-gray-700 dark:bg-gray-900 dark:text-slate-400"${_scopeId}>${ssrInterpolate(unref(t)("nothingFound"))}</div>`);
            } else {
              _push2(`<!--[-->`);
              if (viewMode.value === "grid") {
                _push2(ssrRenderComponent(_sfc_main$5, {
                  courses: displayedCourses.value,
                  cols: courseGridCols.value
                }, null, _parent2, _scopeId));
              } else {
                _push2(ssrRenderComponent(_sfc_main$6, { courses: displayedCourses.value }, null, _parent2, _scopeId));
              }
              _push2(`<!--]-->`);
            }
            if (__props.useServerProcessing && lastPage.value > 1) {
              _push2(ssrRenderComponent(_sfc_main$7, {
                "current-page": currentPage.value,
                "last-page": lastPage.value,
                found: __props.coursesFound,
                onPrev: goPrev,
                onNext: goNext,
                onGo: goToPage
              }, null, _parent2, _scopeId));
            } else {
              _push2(`<!---->`);
            }
            if (!__props.useServerProcessing && effectiveCoursesFound.value > perPageCourses.value) {
              _push2(ssrRenderComponent(_sfc_main$8, {
                currentPage: frontendCurrentPage.value,
                "onUpdate:currentPage": ($event) => frontendCurrentPage.value = $event,
                "items-per-page": perPageCourses.value,
                "total-items": effectiveCoursesFound.value
              }, null, _parent2, _scopeId));
            } else {
              _push2(`<!---->`);
            }
            _push2(ssrRenderComponent(_sfc_main$9, { videos: mainVideos.value }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(SectionBanners, { banners: mainBanners.value }, null, _parent2, _scopeId));
            _push2(`</div></article>`);
            if (showRight.value) {
              _push2(`<aside class="${ssrRenderClass([rightCollapsed.value ? "lg:w-6" : "lg:w-72", "shrink-0 pr-3 transition-all duration-300"])}"${_scopeId}>`);
              _push2(ssrRenderComponent(_sfc_main$a, {
                collapsed: rightCollapsed.value,
                onCollapsed: setRightCollapsed
              }, null, _parent2, _scopeId));
              _push2(`</aside>`);
            } else {
              _push2(`<!---->`);
            }
            _push2(`</div></div></main>`);
            _push2(ssrRenderComponent(_sfc_main$b, null, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(Progress, null, null, _parent2, _scopeId));
            if (isAdmin.value) {
              _push2(ssrRenderComponent(_sfc_main$c, {
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
                      class: ["shrink-0 pl-3 transition-all duration-300", leftCollapsed.value ? "lg:w-6" : "lg:w-72"]
                    }, [
                      createVNode(_sfc_main$3, {
                        "track-tree": trackTree.value,
                        collapsed: leftCollapsed.value,
                        onCollapsed: setLeftCollapsed
                      }, null, 8, ["track-tree", "collapsed"])
                    ], 2)) : createCommentVNode("", true),
                    createVNode("article", {
                      itemscope: "",
                      itemtype: "https://schema.org/CollectionPage",
                      itemid: canonicalUrl.value || void 0,
                      class: "slate-1 w-full min-w-0 pb-6"
                    }, [
                      createVNode("meta", {
                        itemprop: "name",
                        content: seoTitle.value
                      }, null, 8, ["content"]),
                      seoDescription.value ? (openBlock(), createBlock("meta", {
                        key: 0,
                        itemprop: "description",
                        content: seoDescription.value
                      }, null, 8, ["content"])) : createCommentVNode("", true),
                      seoKeywords.value ? (openBlock(), createBlock("meta", {
                        key: 1,
                        itemprop: "keywords",
                        content: seoKeywords.value
                      }, null, 8, ["content"])) : createCommentVNode("", true),
                      canonicalUrl.value ? (openBlock(), createBlock("link", {
                        key: 2,
                        itemprop: "url",
                        href: canonicalUrl.value
                      }, null, 8, ["href"])) : createCommentVNode("", true),
                      createVNode("meta", {
                        itemprop: "inLanguage",
                        content: contentLocale.value
                      }, null, 8, ["content"]),
                      createVNode("div", { class: "mx-auto max-w-6xl" }, [
                        createVNode("nav", {
                          class: "mb-3 text-sm",
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
                              createVNode("span", { class: "mx-2 breadcrumbs" }, "/"),
                              createVNode(unref(Link), {
                                itemprop: "item",
                                href: _ctx.route("public.schoolCourses.index"),
                                class: "breadcrumb-link hover:underline"
                              }, {
                                default: withCtx(() => [
                                  createVNode("span", { itemprop: "name" }, toDisplayString(unref(t)("courses")), 1)
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
                              createVNode("span", { class: "mx-2 breadcrumbs" }, "/"),
                              createVNode("span", {
                                itemprop: "name",
                                class: "breadcrumbs"
                              }, " #" + toDisplayString(hashtagName.value), 1),
                              canonicalUrl.value ? (openBlock(), createBlock("link", {
                                key: 0,
                                itemprop: "item",
                                href: canonicalUrl.value
                              }, null, 8, ["href"])) : createCommentVNode("", true),
                              createVNode("meta", {
                                itemprop: "position",
                                content: "3"
                              })
                            ])
                          ])
                        ]),
                        createVNode("header", { class: "mb-5 rounded-md border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-700 dark:bg-gray-900" }, [
                          createVNode("div", { class: "flex flex-wrap items-center justify-center gap-3" }, [
                            createVNode("h1", {
                              itemprop: "headline",
                              class: "text-2xl font-bold text-slate-800 dark:text-slate-100"
                            }, " #" + toDisplayString(hashtagName.value), 1),
                            hashtag.value.color ? (openBlock(), createBlock("span", {
                              key: 0,
                              class: "h-4 w-4 rounded-full border border-slate-400",
                              style: { backgroundColor: hashtag.value.color },
                              "aria-hidden": "true"
                            }, null, 4)) : createCommentVNode("", true)
                          ]),
                          hashtagShort.value ? (openBlock(), createBlock("div", {
                            key: 0,
                            itemprop: "abstract",
                            class: "mt-2 text-center text-sm text-slate-600 dark:text-slate-300"
                          }, toDisplayString(hashtagShort.value), 1)) : createCommentVNode("", true),
                          hashtagDescription.value ? (openBlock(), createBlock("div", {
                            key: 1,
                            itemprop: "text",
                            class: "mt-4 text-sm text-slate-700 dark:text-slate-300",
                            innerHTML: hashtagDescription.value
                          }, null, 8, ["innerHTML"])) : createCommentVNode("", true),
                          createVNode("div", { class: "mt-4 flex flex-wrap justify-center gap-x-5 gap-y-2 text-xs text-slate-500 dark:text-slate-400" }, [
                            hashtagCoursesCount.value ? (openBlock(), createBlock("span", { key: 0 }, toDisplayString(unref(t)("courses")) + ": " + toDisplayString(hashtagCoursesCount.value), 1)) : createCommentVNode("", true),
                            hashtagModulesCount.value ? (openBlock(), createBlock("span", { key: 1 }, toDisplayString(unref(t)("modules")) + ": " + toDisplayString(hashtagModulesCount.value), 1)) : createCommentVNode("", true),
                            hashtagLessonsCount.value ? (openBlock(), createBlock("span", { key: 2 }, toDisplayString(unref(t)("lessons")) + ": " + toDisplayString(hashtagLessonsCount.value), 1)) : createCommentVNode("", true),
                            hashtag.value.views ? (openBlock(), createBlock("span", { key: 3 }, toDisplayString(unref(t)("views")) + ": " + toDisplayString(hashtag.value.views), 1)) : createCommentVNode("", true),
                            hashtag.value.likes ? (openBlock(), createBlock("span", { key: 4 }, toDisplayString(unref(t)("likes")) + ": " + toDisplayString(hashtag.value.likes), 1)) : createCommentVNode("", true)
                          ])
                        ]),
                        createVNode(_sfc_main$4, {
                          modelValue: qCourses.value,
                          "onUpdate:modelValue": ($event) => qCourses.value = $event,
                          "view-mode": viewMode.value,
                          "onUpdate:viewMode": ($event) => viewMode.value = $event,
                          "sort-value": sortCourses.value,
                          "onUpdate:sortValue": ($event) => sortCourses.value = $event,
                          found: effectiveCoursesFound.value,
                          "sort-options": courseSortOptions.value,
                          "default-sort": DEFAULT_SORT,
                          "found-label": unref(t)("courses"),
                          "search-placeholder": unref(t)("searchByName"),
                          onSubmit: applyFilters,
                          onReset: resetFilters
                        }, null, 8, ["modelValue", "onUpdate:modelValue", "view-mode", "onUpdate:viewMode", "sort-value", "onUpdate:sortValue", "found", "sort-options", "found-label", "search-placeholder"]),
                        !displayedCourses.value.length ? (openBlock(), createBlock("div", {
                          key: 0,
                          class: "rounded-md border border-gray-200 bg-white p-8 text-center text-sm text-slate-500 dark:border-gray-700 dark:bg-gray-900 dark:text-slate-400"
                        }, toDisplayString(unref(t)("nothingFound")), 1)) : (openBlock(), createBlock(Fragment, { key: 1 }, [
                          viewMode.value === "grid" ? (openBlock(), createBlock(_sfc_main$5, {
                            key: 0,
                            courses: displayedCourses.value,
                            cols: courseGridCols.value
                          }, null, 8, ["courses", "cols"])) : (openBlock(), createBlock(_sfc_main$6, {
                            key: 1,
                            courses: displayedCourses.value
                          }, null, 8, ["courses"]))
                        ], 64)),
                        __props.useServerProcessing && lastPage.value > 1 ? (openBlock(), createBlock(_sfc_main$7, {
                          key: 2,
                          "current-page": currentPage.value,
                          "last-page": lastPage.value,
                          found: __props.coursesFound,
                          onPrev: goPrev,
                          onNext: goNext,
                          onGo: goToPage
                        }, null, 8, ["current-page", "last-page", "found"])) : createCommentVNode("", true),
                        !__props.useServerProcessing && effectiveCoursesFound.value > perPageCourses.value ? (openBlock(), createBlock(_sfc_main$8, {
                          key: 3,
                          currentPage: frontendCurrentPage.value,
                          "onUpdate:currentPage": ($event) => frontendCurrentPage.value = $event,
                          "items-per-page": perPageCourses.value,
                          "total-items": effectiveCoursesFound.value
                        }, null, 8, ["currentPage", "onUpdate:currentPage", "items-per-page", "total-items"])) : createCommentVNode("", true),
                        createVNode(_sfc_main$9, { videos: mainVideos.value }, null, 8, ["videos"]),
                        createVNode(SectionBanners, { banners: mainBanners.value }, null, 8, ["banners"])
                      ])
                    ], 8, ["itemid"]),
                    showRight.value ? (openBlock(), createBlock("aside", {
                      key: 1,
                      class: ["shrink-0 pr-3 transition-all duration-300", rightCollapsed.value ? "lg:w-6" : "lg:w-72"]
                    }, [
                      createVNode(_sfc_main$a, {
                        collapsed: rightCollapsed.value,
                        onCollapsed: setRightCollapsed
                      }, null, 8, ["collapsed"])
                    ], 2)) : createCommentVNode("", true)
                  ])
                ])
              ]),
              createVNode(_sfc_main$b),
              createVNode(Progress),
              isAdmin.value ? (openBlock(), createBlock(_sfc_main$c, {
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
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Pages/Public/Default/School/SchoolHashtags/Show.vue");
  return _sfc_setup ? _sfc_setup(props, ctx) : void 0;
};
export {
  _sfc_main as default
};
