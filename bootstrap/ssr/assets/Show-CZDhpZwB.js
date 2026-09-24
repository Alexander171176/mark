import { computed, ref, watch, onMounted, unref, withCtx, createVNode, resolveDynamicComponent, createBlock, createCommentVNode, toDisplayString, openBlock, useSSRContext } from "vue";
import { ssrRenderComponent, ssrInterpolate, ssrRenderAttr, ssrRenderVNode, ssrRenderClass } from "vue/server-renderer";
import { usePage, Head, Link, router } from "@inertiajs/vue3";
import { useI18n } from "vue-i18n";
import { _ as _sfc_main$1 } from "./DefaultLayout-6zxGxPtK.js";
import { _ as _sfc_main$2, a as _sfc_main$d, P as Progress } from "./Progress-CyfOKcqP.js";
import { _ as _sfc_main$3, a as _sfc_main$c } from "./RightSidebarSchool-DlAegojf.js";
import { _ as _sfc_main$4 } from "./EntityPageToolbar-DT32FtSd.js";
import { I as ImageGalleryMain } from "./ImageGalleryMain-mIfXDUWm.js";
import { _ as _sfc_main$7, a as _sfc_main$8, b as _sfc_main$e } from "./PublicAdminBottomPanel-BhLEQMoJ.js";
import { _ as _sfc_main$b, S as SectionBanners } from "./SectionBanners-Cdgmr0Bw.js";
import { L as LikeButtonEntity } from "./LikeButtonEntity-ZC4HMEAO.js";
import { _ as _sfc_main$5 } from "./CourseGrid-DoYn9a32.js";
import { _ as _sfc_main$6 } from "./CourseRows-Q6HeVebv.js";
import { _ as _sfc_main$9, a as _sfc_main$a } from "./TrackRows-BjGnlkhH.js";
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
const DEFAULT_SORT = "idDesc";
const VIEW_KEY = "public_school_courses_view";
const LEFT_SIDEBAR_KEY = "public_left_sidebar_collapsed";
const RIGHT_SIDEBAR_KEY = "public_right_sidebar_collapsed";
const _sfc_main = {
  __name: "Show",
  __ssrInlineRender: true,
  props: {
    title: String,
    canLogin: Boolean,
    canRegister: Boolean,
    locale: { type: String, default: "ru" },
    track: {
      type: Object,
      default: () => ({})
    },
    useServerProcessing: {
      type: Boolean,
      default: false
    },
    publicSchoolCoursesProcessingMode: {
      type: String,
      default: "server"
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
    var _a, _b;
    const { t } = useI18n();
    const page = usePage();
    const props = __props;
    const normalizeList = (value) => {
      if (Array.isArray(value)) return value;
      if (Array.isArray(value == null ? void 0 : value.data)) return value.data;
      return [];
    };
    const normalizeText = (value) => String(value ?? "").trim().toLocaleLowerCase();
    const safeNumber = (value) => {
      const number = Number(value);
      return Number.isFinite(number) ? number : 0;
    };
    const safeDate = (value) => {
      const time = value ? Date.parse(value) : 0;
      return Number.isFinite(time) ? time : 0;
    };
    const track = computed(() => props.track ?? {});
    const translation = computed(() => {
      var _a2;
      return ((_a2 = track.value) == null ? void 0 : _a2.translation) ?? {};
    });
    const trackName = computed(() => {
      var _a2;
      return ((_a2 = translation.value) == null ? void 0 : _a2.name) || "";
    });
    const trackShort = computed(() => {
      var _a2;
      return ((_a2 = translation.value) == null ? void 0 : _a2.short) || "";
    });
    const trackDescription = computed(() => {
      var _a2;
      return ((_a2 = translation.value) == null ? void 0 : _a2.description) || "";
    });
    const childTracks = computed(
      () => {
        var _a2;
        return Array.isArray((_a2 = track.value) == null ? void 0 : _a2.children) ? track.value.children : [];
      }
    );
    const hasChildren = computed(() => childTracks.value.length > 0);
    const trackMetaTitle = computed(
      () => {
        var _a2;
        return ((_a2 = translation.value) == null ? void 0 : _a2.meta_title) || trackName.value;
      }
    );
    const trackMetaKeywords = computed(
      () => {
        var _a2;
        return ((_a2 = translation.value) == null ? void 0 : _a2.meta_keywords) || "";
      }
    );
    const trackMetaDescription = computed(
      () => {
        var _a2;
        return ((_a2 = translation.value) == null ? void 0 : _a2.meta_desc) || trackShort.value || "";
      }
    );
    const trackLocale = computed(
      () => {
        var _a2;
        return ((_a2 = translation.value) == null ? void 0 : _a2.locale) || props.locale || "ru";
      }
    );
    const ogLocale = computed(
      () => trackLocale.value === "ru" ? "ru_RU" : trackLocale.value
    );
    const canonicalUrl = computed(() => {
      var _a2;
      if (!((_a2 = track.value) == null ? void 0 : _a2.slug)) return "";
      return String(route("public.schoolTracks.show", {
        slug: track.value.slug
      }));
    });
    const trackImages = computed(
      () => {
        var _a2;
        return Array.isArray((_a2 = track.value) == null ? void 0 : _a2.images) ? track.value.images : [];
      }
    );
    const hasTrackImages = computed(() => trackImages.value.length > 0);
    const trackSeoImage = computed(() => {
      const image = trackImages.value[0];
      return (image == null ? void 0 : image.webp_url) || (image == null ? void 0 : image.image_url) || (image == null ? void 0 : image.thumb_url) || (image == null ? void 0 : image.url) || "";
    });
    const trackSeoImageAlt = computed(
      () => trackName.value || trackMetaTitle.value
    );
    computed(
      () => {
        var _a2;
        return Number(((_a2 = track.value) == null ? void 0 : _a2.courses_count) ?? props.coursesCount ?? 0);
      }
    );
    computed(
      () => {
        var _a2;
        return Number(((_a2 = track.value) == null ? void 0 : _a2.children_count) ?? childTracks.value.length);
      }
    );
    const parentTrack = computed(
      () => {
        var _a2;
        return ((_a2 = track.value) == null ? void 0 : _a2.parent) ?? null;
      }
    );
    const parentTrackName = computed(
      () => {
        var _a2, _b2;
        return ((_b2 = (_a2 = parentTrack.value) == null ? void 0 : _a2.translation) == null ? void 0 : _b2.name) || "";
      }
    );
    const parentTrackUrl = computed(() => {
      var _a2;
      if (!((_a2 = parentTrack.value) == null ? void 0 : _a2.slug)) return "";
      return String(route("public.schoolTracks.show", {
        slug: parentTrack.value.slug
      }));
    });
    const tracksIndexUrl = computed(
      () => String(route("public.schoolTracks.index"))
    );
    const homeUrl = computed(
      () => String(route("home"))
    );
    const breadcrumbJsonLd = computed(() => {
      const items = [
        {
          "@type": "ListItem",
          position: 1,
          name: t("home"),
          item: homeUrl.value
        },
        {
          "@type": "ListItem",
          position: 2,
          name: t("tracks"),
          item: tracksIndexUrl.value
        }
      ];
      if (parentTrack.value && parentTrackUrl.value) {
        items.push({
          "@type": "ListItem",
          position: 3,
          name: parentTrackName.value,
          item: parentTrackUrl.value
        });
      }
      items.push({
        "@type": "ListItem",
        position: items.length + 1,
        name: trackName.value,
        item: canonicalUrl.value
      });
      return JSON.stringify({
        "@context": "https://schema.org",
        "@type": "BreadcrumbList",
        itemListElement: items
      });
    });
    const trackJsonLd = computed(() => {
      var _a2;
      const data = {
        "@context": "https://schema.org",
        "@type": "LearningResource",
        "@id": canonicalUrl.value,
        url: canonicalUrl.value,
        name: trackName.value,
        headline: trackMetaTitle.value,
        description: trackMetaDescription.value,
        inLanguage: trackLocale.value,
        identifier: String(((_a2 = track.value) == null ? void 0 : _a2.id) ?? "")
      };
      if (trackSeoImage.value) {
        data.image = trackSeoImage.value;
      }
      if (trackShort.value) {
        data.abstract = trackShort.value;
      }
      if (parentTrack.value && parentTrackUrl.value) {
        data.isPartOf = {
          "@type": "LearningResource",
          "@id": parentTrackUrl.value,
          url: parentTrackUrl.value,
          name: parentTrackName.value
        };
      }
      return JSON.stringify(data);
    });
    const childTracksJsonLd = computed(() => {
      if (!childTracks.value.length) return "";
      return JSON.stringify({
        "@context": "https://schema.org",
        "@type": "ItemList",
        name: `${t("subheadings")}: ${trackName.value}`,
        numberOfItems: childTracks.value.length,
        itemListElement: childTracks.value.map((child, index) => {
          var _a2, _b2;
          return {
            "@type": "ListItem",
            position: index + 1,
            url: (child == null ? void 0 : child.slug) ? String(route("public.schoolTracks.show", { slug: child.slug })) : void 0,
            name: ((_a2 = child == null ? void 0 : child.translation) == null ? void 0 : _a2.name) || "",
            description: ((_b2 = child == null ? void 0 : child.translation) == null ? void 0 : _b2.short) || void 0
          };
        })
      });
    });
    const coursesData = computed(() => normalizeList(props.courses));
    const qCourses = ref(String(((_a = props.filters) == null ? void 0 : _a.q_courses) ?? ""));
    const perPageCourses = computed(
      () => {
        var _a2;
        return Number(((_a2 = props.filters) == null ? void 0 : _a2.per_page_courses) ?? 12);
      }
    );
    const sortCourses = ref(
      String(((_b = props.filters) == null ? void 0 : _b.sort_courses) ?? DEFAULT_SORT)
    );
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
      { value: "availabilityAsc", label: `${t("availability")} A→Z` },
      { value: "availabilityDesc", label: `${t("availability")} Z→A` },
      { value: "modulesDesc", label: `${t("modules")} 9→0` },
      { value: "modulesAsc", label: `${t("modules")} 0→9` },
      { value: "lessonsDesc", label: `${t("lessons")} 9→0` },
      { value: "lessonsAsc", label: `${t("lessons")} 0→9` },
      { value: "tracksDesc", label: `${t("tracks")} 9→0` },
      { value: "tracksAsc", label: `${t("tracks")} 0→9` },
      { value: "hashtagsDesc", label: `${t("hashtags")} 9→0` },
      { value: "hashtagsAsc", label: `${t("hashtags")} 0→9` },
      { value: "imagesDesc", label: `${t("images")} 9→0` },
      { value: "imagesAsc", label: `${t("images")} 0→9` },
      { value: "pricesDesc", label: `${t("prices")} 9→0` },
      { value: "pricesAsc", label: `${t("prices")} 0→9` },
      { value: "reviewsDesc", label: `${t("reviews")} 9→0` },
      { value: "reviewsAsc", label: `${t("reviews")} 0→9` },
      { value: "publishedAtDesc", label: `${t("publishedAt")} ↓` },
      { value: "publishedAtAsc", label: `${t("publishedAt")} ↑` },
      { value: "createdAtDesc", label: `${t("createdAt")} ↓` },
      { value: "createdAtAsc", label: `${t("createdAt")} ↑` }
    ];
    const getCourseTitle = (course) => {
      var _a2;
      return ((_a2 = course == null ? void 0 : course.translation) == null ? void 0 : _a2.title) || "";
    };
    const getCourseShort = (course) => {
      var _a2;
      return ((_a2 = course == null ? void 0 : course.translation) == null ? void 0 : _a2.short) || "";
    };
    const getInstructorName = (course) => {
      var _a2, _b2, _c, _d;
      return ((_b2 = (_a2 = course == null ? void 0 : course.instructorProfile) == null ? void 0 : _a2.translation) == null ? void 0 : _b2.title) || ((_d = (_c = course == null ? void 0 : course.instructorProfile) == null ? void 0 : _c.user) == null ? void 0 : _d.name) || "";
    };
    const frontendFilteredCourses = computed(() => {
      if (props.useServerProcessing) return coursesData.value;
      const query = normalizeText(qCourses.value);
      if (!query) return coursesData.value;
      return coursesData.value.filter((course) => [
        course == null ? void 0 : course.id,
        course == null ? void 0 : course.slug,
        getCourseTitle(course),
        getCourseShort(course),
        getInstructorName(course),
        course == null ? void 0 : course.level,
        course == null ? void 0 : course.availability
      ].some((value) => normalizeText(value).includes(query)));
    });
    const compareText = (a, b) => String(a ?? "").localeCompare(
      String(b ?? ""),
      props.locale,
      { sensitivity: "base" }
    );
    const compareNumber = (a, b) => safeNumber(a) - safeNumber(b);
    const frontendSortedCourses = computed(() => {
      if (props.useServerProcessing) return frontendFilteredCourses.value;
      const list = [...frontendFilteredCourses.value];
      const numberSort = {
        idAsc: ["id", false],
        idDesc: ["id", true],
        sortAsc: ["sort", false],
        sortDesc: ["sort", true],
        studentsCountAsc: ["students_count", false],
        studentsCountDesc: ["students_count", true],
        viewsAsc: ["views", false],
        viewsDesc: ["views", true],
        likesAsc: ["likes_count", false],
        likesDesc: ["likes_count", true],
        popularityAsc: ["popularity", false],
        popularityDesc: ["popularity", true],
        ratingCountAsc: ["rating_count", false],
        ratingCountDesc: ["rating_count", true],
        ratingAvgAsc: ["rating_avg", false],
        ratingAvgDesc: ["rating_avg", true],
        difficultyAsc: ["difficulty", false],
        difficultyDesc: ["difficulty", true],
        durationAsc: ["duration", false],
        durationDesc: ["duration", true],
        modulesAsc: ["modules_count", false],
        modulesDesc: ["modules_count", true],
        lessonsAsc: ["lessons_count", false],
        lessonsDesc: ["lessons_count", true],
        tracksAsc: ["tracks_count", false],
        tracksDesc: ["tracks_count", true],
        hashtagsAsc: ["hashtags_count", false],
        hashtagsDesc: ["hashtags_count", true],
        imagesAsc: ["images_count", false],
        imagesDesc: ["images_count", true],
        pricesAsc: ["prices_count", false],
        pricesDesc: ["prices_count", true],
        reviewsAsc: ["reviews_count", false],
        reviewsDesc: ["reviews_count", true]
      };
      const numberRule = numberSort[sortCourses.value];
      if (numberRule) {
        const [field, desc] = numberRule;
        list.sort(
          (a, b) => desc ? compareNumber(b == null ? void 0 : b[field], a == null ? void 0 : a[field]) : compareNumber(a == null ? void 0 : a[field], b == null ? void 0 : b[field])
        );
        return list;
      }
      switch (sortCourses.value) {
        case "titleAsc":
          list.sort(
            (a, b) => compareText(getCourseTitle(a), getCourseTitle(b))
          );
          break;
        case "titleDesc":
          list.sort(
            (a, b) => compareText(getCourseTitle(b), getCourseTitle(a))
          );
          break;
        case "levelAsc":
          list.sort((a, b) => compareText(a == null ? void 0 : a.level, b == null ? void 0 : b.level));
          break;
        case "levelDesc":
          list.sort((a, b) => compareText(b == null ? void 0 : b.level, a == null ? void 0 : a.level));
          break;
        case "availabilityAsc":
          list.sort((a, b) => compareText(a == null ? void 0 : a.availability, b == null ? void 0 : b.availability));
          break;
        case "availabilityDesc":
          list.sort((a, b) => compareText(b == null ? void 0 : b.availability, a == null ? void 0 : a.availability));
          break;
        case "publishedAtAsc":
          list.sort(
            (a, b) => safeDate(a == null ? void 0 : a.published_at) - safeDate(b == null ? void 0 : b.published_at)
          );
          break;
        case "publishedAtDesc":
          list.sort(
            (a, b) => safeDate(b == null ? void 0 : b.published_at) - safeDate(a == null ? void 0 : a.published_at)
          );
          break;
        case "createdAtAsc":
          list.sort(
            (a, b) => safeDate(a == null ? void 0 : a.created_at) - safeDate(b == null ? void 0 : b.created_at)
          );
          break;
        case "createdAtDesc":
          list.sort(
            (a, b) => safeDate(b == null ? void 0 : b.created_at) - safeDate(a == null ? void 0 : a.created_at)
          );
          break;
      }
      return list;
    });
    const frontendCurrentPage = ref(1);
    watch([qCourses, sortCourses], () => {
      if (!props.useServerProcessing) {
        frontendCurrentPage.value = 1;
      }
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
    const hasCourses = computed(
      () => Number(props.coursesCount ?? 0) > 0
    );
    const currentPage = computed(
      () => {
        var _a2, _b2, _c;
        return Number(
          ((_b2 = (_a2 = props.courses) == null ? void 0 : _a2.meta) == null ? void 0 : _b2.current_page) ?? ((_c = props.courses) == null ? void 0 : _c.current_page) ?? 1
        ) || 1;
      }
    );
    const lastPage = computed(
      () => {
        var _a2, _b2, _c;
        return Number(
          ((_b2 = (_a2 = props.courses) == null ? void 0 : _a2.meta) == null ? void 0 : _b2.last_page) ?? ((_c = props.courses) == null ? void 0 : _c.last_page) ?? 1
        ) || 1;
      }
    );
    const applyCourseFilters = () => {
      if (props.useServerProcessing) {
        loadServerCourses(1);
      } else {
        frontendCurrentPage.value = 1;
      }
    };
    const resetCourseFilters = () => {
      if (props.useServerProcessing) {
        loadServerCourses(1);
      } else {
        frontendCurrentPage.value = 1;
      }
    };
    const loadServerCourses = (pageNumber = 1) => {
      if (!props.useServerProcessing || !canonicalUrl.value) return;
      router.get(
        canonicalUrl.value,
        {
          q_courses: qCourses.value || void 0,
          sort_courses: sortCourses.value || void 0,
          page_courses: pageNumber
        },
        {
          preserveState: true,
          replace: true,
          preserveScroll: true
        }
      );
    };
    const goToPage = (pageNumber) => {
      const target = Math.min(
        Math.max(1, Number(pageNumber) || 1),
        lastPage.value
      );
      loadServerCourses(target);
    };
    const goPrev = () => {
      if (currentPage.value > 1) {
        goToPage(currentPage.value - 1);
      }
    };
    const goNext = () => {
      if (currentPage.value < lastPage.value) {
        goToPage(currentPage.value + 1);
      }
    };
    const getStoredView = () => {
      if (typeof window === "undefined") return "grid";
      const value = localStorage.getItem(VIEW_KEY);
      return ["grid", "rows"].includes(value) ? value : "grid";
    };
    const viewMode = ref(getStoredView());
    watch(viewMode, (value) => {
      if (typeof window !== "undefined") {
        localStorage.setItem(VIEW_KEY, value);
      }
    });
    const siteSettings = computed(
      () => page.props.siteSettings ?? {}
    );
    const settingEnabled = (value, defaultValue = true) => {
      if (value === void 0 || value === null || value === "") {
        return defaultValue;
      }
      if (typeof value === "boolean") return value;
      return String(value) === "true";
    };
    const showLeft = computed(
      () => {
        var _a2;
        return settingEnabled((_a2 = siteSettings.value) == null ? void 0 : _a2.ViewLeftColumn, true);
      }
    );
    const showRight = computed(
      () => {
        var _a2;
        return settingEnabled((_a2 = siteSettings.value) == null ? void 0 : _a2.ViewRightColumn, true);
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
        localStorage.setItem(key, String(Boolean(value)));
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
      writeStoredBoolean(LEFT_SIDEBAR_KEY, leftCollapsed.value);
    };
    const setRightCollapsed = (value) => {
      rightCollapsed.value = Boolean(value);
      writeStoredBoolean(RIGHT_SIDEBAR_KEY, rightCollapsed.value);
    };
    const gridCols = computed(() => {
      const leftExpanded = showLeft.value && !leftCollapsed.value;
      const rightExpanded = showRight.value && !rightCollapsed.value;
      if (leftExpanded && rightExpanded) return 2;
      if (leftExpanded || rightExpanded) return 3;
      return 4;
    });
    const trackTree = computed(
      () => Array.isArray(props.trackTree) ? props.trackTree : []
    );
    const mainVideos = computed(
      () => normalizeList(props.mainVideos)
    );
    const mainBanners = computed(
      () => normalizeList(props.mainBanners)
    );
    const isAdmin = computed(
      () => {
        var _a2;
        return ((_a2 = page.props) == null ? void 0 : _a2.isAdmin) === true;
      }
    );
    return (_ctx, _push, _parent, _attrs) => {
      _push(`<!--[-->`);
      _push(ssrRenderComponent(unref(Head), null, {
        default: withCtx((_, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(`<title${_scopeId}>${ssrInterpolate(trackMetaTitle.value)}</title><meta name="title"${ssrRenderAttr("content", trackMetaTitle.value)}${_scopeId}>`);
            if (trackMetaDescription.value) {
              _push2(`<meta name="description"${ssrRenderAttr("content", trackMetaDescription.value)}${_scopeId}>`);
            } else {
              _push2(`<!---->`);
            }
            if (trackMetaKeywords.value) {
              _push2(`<meta name="keywords"${ssrRenderAttr("content", trackMetaKeywords.value)}${_scopeId}>`);
            } else {
              _push2(`<!---->`);
            }
            _push2(`<meta name="robots" content="index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1"${_scopeId}>`);
            if (canonicalUrl.value) {
              _push2(`<link rel="canonical"${ssrRenderAttr("href", canonicalUrl.value)}${_scopeId}>`);
            } else {
              _push2(`<!---->`);
            }
            _push2(`<meta property="og:type" content="website"${_scopeId}><meta property="og:title"${ssrRenderAttr("content", trackMetaTitle.value)}${_scopeId}>`);
            if (trackMetaDescription.value) {
              _push2(`<meta property="og:description"${ssrRenderAttr("content", trackMetaDescription.value)}${_scopeId}>`);
            } else {
              _push2(`<!---->`);
            }
            if (canonicalUrl.value) {
              _push2(`<meta property="og:url"${ssrRenderAttr("content", canonicalUrl.value)}${_scopeId}>`);
            } else {
              _push2(`<!---->`);
            }
            _push2(`<meta property="og:locale"${ssrRenderAttr("content", ogLocale.value)}${_scopeId}>`);
            if (trackSeoImage.value) {
              _push2(`<meta property="og:image"${ssrRenderAttr("content", trackSeoImage.value)}${_scopeId}>`);
            } else {
              _push2(`<!---->`);
            }
            if (trackSeoImage.value) {
              _push2(`<meta property="og:image:alt"${ssrRenderAttr("content", trackSeoImageAlt.value)}${_scopeId}>`);
            } else {
              _push2(`<!---->`);
            }
            _push2(`<meta name="twitter:card"${ssrRenderAttr("content", trackSeoImage.value ? "summary_large_image" : "summary")}${_scopeId}><meta name="twitter:title"${ssrRenderAttr("content", trackMetaTitle.value)}${_scopeId}>`);
            if (trackMetaDescription.value) {
              _push2(`<meta name="twitter:description"${ssrRenderAttr("content", trackMetaDescription.value)}${_scopeId}>`);
            } else {
              _push2(`<!---->`);
            }
            if (trackSeoImage.value) {
              _push2(`<meta name="twitter:image"${ssrRenderAttr("content", trackSeoImage.value)}${_scopeId}>`);
            } else {
              _push2(`<!---->`);
            }
            if (trackSeoImage.value) {
              _push2(`<meta name="twitter:image:alt"${ssrRenderAttr("content", trackSeoImageAlt.value)}${_scopeId}>`);
            } else {
              _push2(`<!---->`);
            }
            _push2(`<meta name="DC.title"${ssrRenderAttr("content", trackMetaTitle.value)}${_scopeId}>`);
            if (trackMetaDescription.value) {
              _push2(`<meta name="DC.description"${ssrRenderAttr("content", trackMetaDescription.value)}${_scopeId}>`);
            } else {
              _push2(`<!---->`);
            }
            if (trackMetaKeywords.value) {
              _push2(`<meta name="DC.subject"${ssrRenderAttr("content", trackMetaKeywords.value)}${_scopeId}>`);
            } else {
              _push2(`<!---->`);
            }
            _push2(`<meta name="DC.language"${ssrRenderAttr("content", trackLocale.value)}${_scopeId}>`);
            if (canonicalUrl.value) {
              _push2(`<meta name="DC.identifier"${ssrRenderAttr("content", canonicalUrl.value)}${_scopeId}>`);
            } else {
              _push2(`<!---->`);
            }
            _push2(`<meta name="DC.type" content="InteractiveResource"${_scopeId}><meta name="DC.format" content="text/html"${_scopeId}>`);
            ssrRenderVNode(_push2, createVNode(resolveDynamicComponent("script"), { type: "application/ld+json" }, null), _parent2, _scopeId);
            ssrRenderVNode(_push2, createVNode(resolveDynamicComponent("script"), { type: "application/ld+json" }, null), _parent2, _scopeId);
            if (childTracksJsonLd.value) {
              ssrRenderVNode(_push2, createVNode(resolveDynamicComponent("script"), { type: "application/ld+json" }, null), _parent2, _scopeId);
            } else {
              _push2(`<!---->`);
            }
          } else {
            return [
              createVNode("title", null, toDisplayString(trackMetaTitle.value), 1),
              createVNode("meta", {
                name: "title",
                content: trackMetaTitle.value
              }, null, 8, ["content"]),
              trackMetaDescription.value ? (openBlock(), createBlock("meta", {
                key: 0,
                name: "description",
                content: trackMetaDescription.value
              }, null, 8, ["content"])) : createCommentVNode("", true),
              trackMetaKeywords.value ? (openBlock(), createBlock("meta", {
                key: 1,
                name: "keywords",
                content: trackMetaKeywords.value
              }, null, 8, ["content"])) : createCommentVNode("", true),
              createVNode("meta", {
                name: "robots",
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
                content: trackMetaTitle.value
              }, null, 8, ["content"]),
              trackMetaDescription.value ? (openBlock(), createBlock("meta", {
                key: 3,
                property: "og:description",
                content: trackMetaDescription.value
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
              trackSeoImage.value ? (openBlock(), createBlock("meta", {
                key: 5,
                property: "og:image",
                content: trackSeoImage.value
              }, null, 8, ["content"])) : createCommentVNode("", true),
              trackSeoImage.value ? (openBlock(), createBlock("meta", {
                key: 6,
                property: "og:image:alt",
                content: trackSeoImageAlt.value
              }, null, 8, ["content"])) : createCommentVNode("", true),
              createVNode("meta", {
                name: "twitter:card",
                content: trackSeoImage.value ? "summary_large_image" : "summary"
              }, null, 8, ["content"]),
              createVNode("meta", {
                name: "twitter:title",
                content: trackMetaTitle.value
              }, null, 8, ["content"]),
              trackMetaDescription.value ? (openBlock(), createBlock("meta", {
                key: 7,
                name: "twitter:description",
                content: trackMetaDescription.value
              }, null, 8, ["content"])) : createCommentVNode("", true),
              trackSeoImage.value ? (openBlock(), createBlock("meta", {
                key: 8,
                name: "twitter:image",
                content: trackSeoImage.value
              }, null, 8, ["content"])) : createCommentVNode("", true),
              trackSeoImage.value ? (openBlock(), createBlock("meta", {
                key: 9,
                name: "twitter:image:alt",
                content: trackSeoImageAlt.value
              }, null, 8, ["content"])) : createCommentVNode("", true),
              createVNode("meta", {
                name: "DC.title",
                content: trackMetaTitle.value
              }, null, 8, ["content"]),
              trackMetaDescription.value ? (openBlock(), createBlock("meta", {
                key: 10,
                name: "DC.description",
                content: trackMetaDescription.value
              }, null, 8, ["content"])) : createCommentVNode("", true),
              trackMetaKeywords.value ? (openBlock(), createBlock("meta", {
                key: 11,
                name: "DC.subject",
                content: trackMetaKeywords.value
              }, null, 8, ["content"])) : createCommentVNode("", true),
              createVNode("meta", {
                name: "DC.language",
                content: trackLocale.value
              }, null, 8, ["content"]),
              canonicalUrl.value ? (openBlock(), createBlock("meta", {
                key: 12,
                name: "DC.identifier",
                content: canonicalUrl.value
              }, null, 8, ["content"])) : createCommentVNode("", true),
              createVNode("meta", {
                name: "DC.type",
                content: "InteractiveResource"
              }),
              createVNode("meta", {
                name: "DC.format",
                content: "text/html"
              }),
              (openBlock(), createBlock(resolveDynamicComponent("script"), {
                type: "application/ld+json",
                innerHTML: breadcrumbJsonLd.value
              }, null, 8, ["innerHTML"])),
              (openBlock(), createBlock(resolveDynamicComponent("script"), {
                type: "application/ld+json",
                innerHTML: trackJsonLd.value
              }, null, 8, ["innerHTML"])),
              childTracksJsonLd.value ? (openBlock(), createBlock(resolveDynamicComponent("script"), {
                key: 13,
                type: "application/ld+json",
                innerHTML: childTracksJsonLd.value
              }, null, 8, ["innerHTML"])) : createCommentVNode("", true)
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
          var _a2, _b2, _c, _d;
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
            _push2(`<article itemscope itemtype="https://schema.org/LearningResource"${ssrRenderAttr("itemid", canonicalUrl.value)} class="w-full pb-6 slate-1 min-w-0"${_scopeId}><meta itemprop="identifier"${ssrRenderAttr("content", String(track.value.id || ""))}${_scopeId}><meta itemprop="name"${ssrRenderAttr("content", trackName.value)}${_scopeId}>`);
            if (trackMetaDescription.value) {
              _push2(`<meta itemprop="description"${ssrRenderAttr("content", trackMetaDescription.value)}${_scopeId}>`);
            } else {
              _push2(`<!---->`);
            }
            if (trackMetaKeywords.value) {
              _push2(`<meta itemprop="keywords"${ssrRenderAttr("content", trackMetaKeywords.value)}${_scopeId}>`);
            } else {
              _push2(`<!---->`);
            }
            if (canonicalUrl.value) {
              _push2(`<meta itemprop="url"${ssrRenderAttr("content", canonicalUrl.value)}${_scopeId}>`);
            } else {
              _push2(`<!---->`);
            }
            _push2(`<meta itemprop="inLanguage"${ssrRenderAttr("content", trackLocale.value)}${_scopeId}>`);
            if (trackSeoImage.value) {
              _push2(`<meta itemprop="image"${ssrRenderAttr("content", trackSeoImage.value)}${_scopeId}>`);
            } else {
              _push2(`<!---->`);
            }
            _push2(`<div class="mx-auto max-w-6xl"${_scopeId}><nav class="mb-3 text-sm" aria-label="Breadcrumb" itemscope itemtype="https://schema.org/BreadcrumbList"${_scopeId}><ol class="flex flex-wrap items-center font-semibold"${_scopeId}><li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem" class="flex items-center"${_scopeId}>`);
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
              href: _ctx.route("public.schoolTracks.index"),
              class: "breadcrumb-link hover:underline"
            }, {
              default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                if (_push3) {
                  _push3(`<span itemprop="name"${_scopeId2}>${ssrInterpolate(unref(t)("tracks"))}</span>`);
                } else {
                  return [
                    createVNode("span", { itemprop: "name" }, toDisplayString(unref(t)("tracks")), 1)
                  ];
                }
              }),
              _: 1
            }, _parent2, _scopeId));
            _push2(`<meta itemprop="position" content="2"${_scopeId}></li>`);
            if ((_a2 = track.value) == null ? void 0 : _a2.parent) {
              _push2(`<li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem" class="flex items-center"${_scopeId}><span class="mx-2 breadcrumbs"${_scopeId}>/</span>`);
              _push2(ssrRenderComponent(unref(Link), {
                itemprop: "item",
                href: _ctx.route("public.schoolTracks.show", { slug: track.value.parent.slug }),
                class: "breadcrumb-link hover:underline"
              }, {
                default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                  var _a3, _b3, _c2, _d2;
                  if (_push3) {
                    _push3(`<span itemprop="name"${_scopeId2}>${ssrInterpolate(((_b3 = (_a3 = track.value.parent) == null ? void 0 : _a3.translation) == null ? void 0 : _b3.name) || "")}</span>`);
                  } else {
                    return [
                      createVNode("span", { itemprop: "name" }, toDisplayString(((_d2 = (_c2 = track.value.parent) == null ? void 0 : _c2.translation) == null ? void 0 : _d2.name) || ""), 1)
                    ];
                  }
                }),
                _: 1
              }, _parent2, _scopeId));
              _push2(`<meta itemprop="position" content="3"${_scopeId}></li>`);
            } else {
              _push2(`<!---->`);
            }
            _push2(`<li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem" class="flex items-center" aria-current="page"${_scopeId}><span class="mx-2 breadcrumbs"${_scopeId}>/</span><span itemprop="name" class="breadcrumbs"${_scopeId}>${ssrInterpolate(trackName.value)}</span>`);
            if (canonicalUrl.value) {
              _push2(`<meta itemprop="item"${ssrRenderAttr("content", canonicalUrl.value)}${_scopeId}>`);
            } else {
              _push2(`<!---->`);
            }
            _push2(`<meta itemprop="position"${ssrRenderAttr("content", ((_b2 = track.value) == null ? void 0 : _b2.parent) ? 4 : 3)}${_scopeId}></li></ol></nav>`);
            if (hasTrackImages.value) {
              _push2(`<div class="flex items-center justify-center"${_scopeId}><div class="w-full"${_scopeId}>`);
              _push2(ssrRenderComponent(ImageGalleryMain, {
                images: trackImages.value,
                alt: trackName.value,
                "rounded-class": "rounded-lg",
                "shadow-class": "shadow-lg shadow-gray-400 dark:shadow-gray-700",
                "img-class": "w-full h-full object-cover"
              }, null, _parent2, _scopeId));
              _push2(`</div></div>`);
            } else {
              _push2(`<!---->`);
            }
            _push2(`<div class="flex items-center justify-between gap-1"${_scopeId}><div${ssrRenderAttr("title", unref(t)("courses"))} class="flex items-center justify-center gap-1"${_scopeId}><svg class="h-5 w-5 text-slate-600/85 dark:text-slate-200/85" fill="currentColor" viewBox="0 0 24 24"${_scopeId}><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25"${_scopeId}></path></svg>`);
            if (hasCourses.value) {
              _push2(`<span class="text-center text-sm text-gray-500"${_scopeId}>${ssrInterpolate(__props.coursesCount)}</span>`);
            } else {
              _push2(`<!---->`);
            }
            _push2(`</div><div class="flex flex-wrap items-center justify-center gap-3 title my-3"${_scopeId}><h1 itemprop="headline" class="text-2xl font-bold"${_scopeId}>${ssrInterpolate(trackName.value)}</h1></div><div${ssrRenderAttr("title", unref(t)("views"))} class="flex items-center justify-center gap-1"${_scopeId}><svg class="h-4 w-4 text-slate-600/85 dark:text-slate-200/85" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512" fill="currentColor"${_scopeId}><path d="M569.354 231.631C512.97 135.949 407.81 72 288 72 168.14 72 63.004 135.994 6.646 231.631a47.999 47.999 0 0 0 0 48.739C63.031 376.051 168.19 440 288 440c119.86 0 224.996-63.994 281.354-159.631a47.997 47.997 0 0 0 0-48.738zM288 392c-102.556 0-192.091-54.701-240-136 44.157-74.933 123.677-127.27 216.162-135.007C273.958 131.078 280 144.83 280 160c0 30.928-25.072 56-56 56s-56-25.072-56-56l.001-.042C157.794 179.043 152 200.844 152 224c0 75.111 60.889 136 136 136s136-60.889 136-136c0-31.031-10.4-59.629-27.895-82.515C451.704 164.638 498.009 205.106 528 256c-47.908 81.299-137.444 136-240 136z"${_scopeId}></path></svg><span class="text-center text-sm text-gray-500"${_scopeId}>${ssrInterpolate(track.value.views || 0)}</span></div></div>`);
            if (trackShort.value) {
              _push2(`<div itemprop="abstract" class="mb-3 text-sm subtitle text-center"${_scopeId}>${ssrInterpolate(trackShort.value)}</div>`);
            } else {
              _push2(`<!---->`);
            }
            if (trackDescription.value) {
              _push2(`<div itemprop="text" class="my-3 text-sm subtitle text-center"${_scopeId}>${trackDescription.value ?? ""}</div>`);
            } else {
              _push2(`<!---->`);
            }
            _push2(`<div class="flex justify-center items-center"${_scopeId}>`);
            _push2(ssrRenderComponent(LikeButtonEntity, {
              "likes-count": track.value.likes_count || 0,
              "already-liked": track.value.already_liked || false,
              "route-name": "public.schoolTracks.like",
              "route-params": track.value.id,
              title: unref(t)("like"),
              "icon-class": "w-4 h-4"
            }, null, _parent2, _scopeId));
            _push2(`</div>`);
            if (hasCourses.value) {
              _push2(`<section aria-labelledby="track-courses-title"${_scopeId}><h2 id="track-courses-title" class="sr-only"${_scopeId}>${ssrInterpolate(unref(t)("courses"))} — ${ssrInterpolate(trackName.value)}</h2>`);
              _push2(ssrRenderComponent(_sfc_main$4, {
                modelValue: qCourses.value,
                "onUpdate:modelValue": ($event) => qCourses.value = $event,
                "view-mode": viewMode.value,
                "onUpdate:viewMode": ($event) => viewMode.value = $event,
                "sort-value": sortCourses.value,
                "onUpdate:sortValue": ($event) => sortCourses.value = $event,
                found: effectiveCoursesFound.value,
                "sort-options": courseSortOptions,
                "default-sort": DEFAULT_SORT,
                "found-label": unref(t)("courses"),
                "search-placeholder": unref(t)("searchByName"),
                onSubmit: applyCourseFilters,
                onReset: resetCourseFilters
              }, null, _parent2, _scopeId));
              if (displayedCourses.value.length === 0) {
                _push2(`<div class="mt-6 text-center text-slate-700 dark:text-slate-300"${_scopeId}>${ssrInterpolate(unref(t)("noData"))}</div>`);
              } else {
                _push2(`<!---->`);
              }
              if (displayedCourses.value.length) {
                _push2(`<div${_scopeId}>`);
                if (viewMode.value === "grid") {
                  _push2(ssrRenderComponent(_sfc_main$5, {
                    courses: displayedCourses.value,
                    cols: gridCols.value
                  }, null, _parent2, _scopeId));
                } else {
                  _push2(ssrRenderComponent(_sfc_main$6, { courses: displayedCourses.value }, null, _parent2, _scopeId));
                }
                _push2(`</div>`);
              } else {
                _push2(`<!---->`);
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
              _push2(`</section>`);
            } else {
              _push2(`<!---->`);
            }
            if (hasChildren.value) {
              _push2(`<section aria-labelledby="child-tracks-title"${_scopeId}><div class="mt-4 flex flex-wrap items-center justify-center gap-3 text-slate-700/85 dark:text-slate-300/85"${_scopeId}><svg class="h-8 w-8 opacity-70" fill="currentColor" viewBox="0 0 640 512" aria-hidden="true"${_scopeId}><path d="M622.34 153.2L343.4 67.5c-15.2-4.67-31.6-4.67-46.79 0L17.66 153.2c-23.54 7.23-23.54 38.36 0 45.59l48.63 14.94c-10.67 13.19-17.23 29.28-17.88 46.9C38.78 266.15 32 276.11 32 288c0 10.78 5.68 19.85 13.86 25.65L20.33 428.53C18.11 438.52 25.71 448 35.94 448h56.11c10.24 0 17.84-9.48 15.62-19.47L82.14 313.65C90.32 307.85 96 298.78 96 288c0-11.57-6.47-21.25-15.66-26.87.76-15.02 8.44-28.3 20.69-36.72L296.6 284.5c9.06 2.78 26.44 6.25 46.79 0l278.95-85.7c23.55-7.24 23.55-38.36 0-45.6zM352.79 315.09c-28.53 8.76-52.84 3.92-65.59 0l-145.02-44.55L128 384c0 35.35 85.96 64 192 64s192-28.65 192-64l-14.18-113.47-145.03 44.56z"${_scopeId}></path></svg><h2 id="child-tracks-title" class="text-xl font-semibold"${_scopeId}>${ssrInterpolate(unref(t)("subheadings"))}</h2></div><div class="mt-6"${_scopeId}>`);
              if (viewMode.value === "grid") {
                _push2(ssrRenderComponent(_sfc_main$9, {
                  tracks: childTracks.value,
                  cols: gridCols.value
                }, null, _parent2, _scopeId));
              } else {
                _push2(ssrRenderComponent(_sfc_main$a, { tracks: childTracks.value }, null, _parent2, _scopeId));
              }
              _push2(`</div></section>`);
            } else {
              _push2(`<!---->`);
            }
            _push2(ssrRenderComponent(_sfc_main$b, { videos: mainVideos.value }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(SectionBanners, { banners: mainBanners.value }, null, _parent2, _scopeId));
            _push2(`</div></article>`);
            if (showRight.value) {
              _push2(`<aside class="${ssrRenderClass([rightCollapsed.value ? "lg:w-6" : "lg:w-72", "shrink-0 pr-3 transition-all duration-300"])}"${_scopeId}>`);
              _push2(ssrRenderComponent(_sfc_main$c, {
                collapsed: rightCollapsed.value,
                onCollapsed: setRightCollapsed
              }, null, _parent2, _scopeId));
              _push2(`</aside>`);
            } else {
              _push2(`<!---->`);
            }
            _push2(`</div></div></main>`);
            _push2(ssrRenderComponent(_sfc_main$d, null, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(Progress, null, null, _parent2, _scopeId));
            if (isAdmin.value) {
              _push2(ssrRenderComponent(_sfc_main$e, {
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
                      itemtype: "https://schema.org/LearningResource",
                      itemid: canonicalUrl.value,
                      class: "w-full pb-6 slate-1 min-w-0"
                    }, [
                      createVNode("meta", {
                        itemprop: "identifier",
                        content: String(track.value.id || "")
                      }, null, 8, ["content"]),
                      createVNode("meta", {
                        itemprop: "name",
                        content: trackName.value
                      }, null, 8, ["content"]),
                      trackMetaDescription.value ? (openBlock(), createBlock("meta", {
                        key: 0,
                        itemprop: "description",
                        content: trackMetaDescription.value
                      }, null, 8, ["content"])) : createCommentVNode("", true),
                      trackMetaKeywords.value ? (openBlock(), createBlock("meta", {
                        key: 1,
                        itemprop: "keywords",
                        content: trackMetaKeywords.value
                      }, null, 8, ["content"])) : createCommentVNode("", true),
                      canonicalUrl.value ? (openBlock(), createBlock("meta", {
                        key: 2,
                        itemprop: "url",
                        content: canonicalUrl.value
                      }, null, 8, ["content"])) : createCommentVNode("", true),
                      createVNode("meta", {
                        itemprop: "inLanguage",
                        content: trackLocale.value
                      }, null, 8, ["content"]),
                      trackSeoImage.value ? (openBlock(), createBlock("meta", {
                        key: 3,
                        itemprop: "image",
                        content: trackSeoImage.value
                      }, null, 8, ["content"])) : createCommentVNode("", true),
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
                                href: _ctx.route("public.schoolTracks.index"),
                                class: "breadcrumb-link hover:underline"
                              }, {
                                default: withCtx(() => [
                                  createVNode("span", { itemprop: "name" }, toDisplayString(unref(t)("tracks")), 1)
                                ]),
                                _: 1
                              }, 8, ["href"]),
                              createVNode("meta", {
                                itemprop: "position",
                                content: "2"
                              })
                            ]),
                            ((_c = track.value) == null ? void 0 : _c.parent) ? (openBlock(), createBlock("li", {
                              key: 0,
                              itemprop: "itemListElement",
                              itemscope: "",
                              itemtype: "https://schema.org/ListItem",
                              class: "flex items-center"
                            }, [
                              createVNode("span", { class: "mx-2 breadcrumbs" }, "/"),
                              createVNode(unref(Link), {
                                itemprop: "item",
                                href: _ctx.route("public.schoolTracks.show", { slug: track.value.parent.slug }),
                                class: "breadcrumb-link hover:underline"
                              }, {
                                default: withCtx(() => {
                                  var _a3, _b3;
                                  return [
                                    createVNode("span", { itemprop: "name" }, toDisplayString(((_b3 = (_a3 = track.value.parent) == null ? void 0 : _a3.translation) == null ? void 0 : _b3.name) || ""), 1)
                                  ];
                                }),
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
                              createVNode("span", { class: "mx-2 breadcrumbs" }, "/"),
                              createVNode("span", {
                                itemprop: "name",
                                class: "breadcrumbs"
                              }, toDisplayString(trackName.value), 1),
                              canonicalUrl.value ? (openBlock(), createBlock("meta", {
                                key: 0,
                                itemprop: "item",
                                content: canonicalUrl.value
                              }, null, 8, ["content"])) : createCommentVNode("", true),
                              createVNode("meta", {
                                itemprop: "position",
                                content: ((_d = track.value) == null ? void 0 : _d.parent) ? 4 : 3
                              }, null, 8, ["content"])
                            ])
                          ])
                        ]),
                        hasTrackImages.value ? (openBlock(), createBlock("div", {
                          key: 0,
                          class: "flex items-center justify-center"
                        }, [
                          createVNode("div", { class: "w-full" }, [
                            createVNode(ImageGalleryMain, {
                              images: trackImages.value,
                              alt: trackName.value,
                              "rounded-class": "rounded-lg",
                              "shadow-class": "shadow-lg shadow-gray-400 dark:shadow-gray-700",
                              "img-class": "w-full h-full object-cover"
                            }, null, 8, ["images", "alt"])
                          ])
                        ])) : createCommentVNode("", true),
                        createVNode("div", { class: "flex items-center justify-between gap-1" }, [
                          createVNode("div", {
                            title: unref(t)("courses"),
                            class: "flex items-center justify-center gap-1"
                          }, [
                            (openBlock(), createBlock("svg", {
                              class: "h-5 w-5 text-slate-600/85 dark:text-slate-200/85",
                              fill: "currentColor",
                              viewBox: "0 0 24 24"
                            }, [
                              createVNode("path", {
                                "stroke-linecap": "round",
                                "stroke-linejoin": "round",
                                d: "M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25"
                              })
                            ])),
                            hasCourses.value ? (openBlock(), createBlock("span", {
                              key: 0,
                              class: "text-center text-sm text-gray-500"
                            }, toDisplayString(__props.coursesCount), 1)) : createCommentVNode("", true)
                          ], 8, ["title"]),
                          createVNode("div", { class: "flex flex-wrap items-center justify-center gap-3 title my-3" }, [
                            createVNode("h1", {
                              itemprop: "headline",
                              class: "text-2xl font-bold"
                            }, toDisplayString(trackName.value), 1)
                          ]),
                          createVNode("div", {
                            title: unref(t)("views"),
                            class: "flex items-center justify-center gap-1"
                          }, [
                            (openBlock(), createBlock("svg", {
                              class: "h-4 w-4 text-slate-600/85 dark:text-slate-200/85",
                              xmlns: "http://www.w3.org/2000/svg",
                              viewBox: "0 0 576 512",
                              fill: "currentColor"
                            }, [
                              createVNode("path", { d: "M569.354 231.631C512.97 135.949 407.81 72 288 72 168.14 72 63.004 135.994 6.646 231.631a47.999 47.999 0 0 0 0 48.739C63.031 376.051 168.19 440 288 440c119.86 0 224.996-63.994 281.354-159.631a47.997 47.997 0 0 0 0-48.738zM288 392c-102.556 0-192.091-54.701-240-136 44.157-74.933 123.677-127.27 216.162-135.007C273.958 131.078 280 144.83 280 160c0 30.928-25.072 56-56 56s-56-25.072-56-56l.001-.042C157.794 179.043 152 200.844 152 224c0 75.111 60.889 136 136 136s136-60.889 136-136c0-31.031-10.4-59.629-27.895-82.515C451.704 164.638 498.009 205.106 528 256c-47.908 81.299-137.444 136-240 136z" })
                            ])),
                            createVNode("span", { class: "text-center text-sm text-gray-500" }, toDisplayString(track.value.views || 0), 1)
                          ], 8, ["title"])
                        ]),
                        trackShort.value ? (openBlock(), createBlock("div", {
                          key: 1,
                          itemprop: "abstract",
                          class: "mb-3 text-sm subtitle text-center"
                        }, toDisplayString(trackShort.value), 1)) : createCommentVNode("", true),
                        trackDescription.value ? (openBlock(), createBlock("div", {
                          key: 2,
                          itemprop: "text",
                          class: "my-3 text-sm subtitle text-center",
                          innerHTML: trackDescription.value
                        }, null, 8, ["innerHTML"])) : createCommentVNode("", true),
                        createVNode("div", { class: "flex justify-center items-center" }, [
                          createVNode(LikeButtonEntity, {
                            "likes-count": track.value.likes_count || 0,
                            "already-liked": track.value.already_liked || false,
                            "route-name": "public.schoolTracks.like",
                            "route-params": track.value.id,
                            title: unref(t)("like"),
                            "icon-class": "w-4 h-4"
                          }, null, 8, ["likes-count", "already-liked", "route-params", "title"])
                        ]),
                        hasCourses.value ? (openBlock(), createBlock("section", {
                          key: 3,
                          "aria-labelledby": "track-courses-title"
                        }, [
                          createVNode("h2", {
                            id: "track-courses-title",
                            class: "sr-only"
                          }, toDisplayString(unref(t)("courses")) + " — " + toDisplayString(trackName.value), 1),
                          createVNode(_sfc_main$4, {
                            modelValue: qCourses.value,
                            "onUpdate:modelValue": ($event) => qCourses.value = $event,
                            "view-mode": viewMode.value,
                            "onUpdate:viewMode": ($event) => viewMode.value = $event,
                            "sort-value": sortCourses.value,
                            "onUpdate:sortValue": ($event) => sortCourses.value = $event,
                            found: effectiveCoursesFound.value,
                            "sort-options": courseSortOptions,
                            "default-sort": DEFAULT_SORT,
                            "found-label": unref(t)("courses"),
                            "search-placeholder": unref(t)("searchByName"),
                            onSubmit: applyCourseFilters,
                            onReset: resetCourseFilters
                          }, null, 8, ["modelValue", "onUpdate:modelValue", "view-mode", "onUpdate:viewMode", "sort-value", "onUpdate:sortValue", "found", "found-label", "search-placeholder"]),
                          displayedCourses.value.length === 0 ? (openBlock(), createBlock("div", {
                            key: 0,
                            class: "mt-6 text-center text-slate-700 dark:text-slate-300"
                          }, toDisplayString(unref(t)("noData")), 1)) : createCommentVNode("", true),
                          displayedCourses.value.length ? (openBlock(), createBlock("div", { key: 1 }, [
                            viewMode.value === "grid" ? (openBlock(), createBlock(_sfc_main$5, {
                              key: 0,
                              courses: displayedCourses.value,
                              cols: gridCols.value
                            }, null, 8, ["courses", "cols"])) : (openBlock(), createBlock(_sfc_main$6, {
                              key: 1,
                              courses: displayedCourses.value
                            }, null, 8, ["courses"]))
                          ])) : createCommentVNode("", true),
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
                          }, null, 8, ["currentPage", "onUpdate:currentPage", "items-per-page", "total-items"])) : createCommentVNode("", true)
                        ])) : createCommentVNode("", true),
                        hasChildren.value ? (openBlock(), createBlock("section", {
                          key: 4,
                          "aria-labelledby": "child-tracks-title"
                        }, [
                          createVNode("div", { class: "mt-4 flex flex-wrap items-center justify-center gap-3 text-slate-700/85 dark:text-slate-300/85" }, [
                            (openBlock(), createBlock("svg", {
                              class: "h-8 w-8 opacity-70",
                              fill: "currentColor",
                              viewBox: "0 0 640 512",
                              "aria-hidden": "true"
                            }, [
                              createVNode("path", { d: "M622.34 153.2L343.4 67.5c-15.2-4.67-31.6-4.67-46.79 0L17.66 153.2c-23.54 7.23-23.54 38.36 0 45.59l48.63 14.94c-10.67 13.19-17.23 29.28-17.88 46.9C38.78 266.15 32 276.11 32 288c0 10.78 5.68 19.85 13.86 25.65L20.33 428.53C18.11 438.52 25.71 448 35.94 448h56.11c10.24 0 17.84-9.48 15.62-19.47L82.14 313.65C90.32 307.85 96 298.78 96 288c0-11.57-6.47-21.25-15.66-26.87.76-15.02 8.44-28.3 20.69-36.72L296.6 284.5c9.06 2.78 26.44 6.25 46.79 0l278.95-85.7c23.55-7.24 23.55-38.36 0-45.6zM352.79 315.09c-28.53 8.76-52.84 3.92-65.59 0l-145.02-44.55L128 384c0 35.35 85.96 64 192 64s192-28.65 192-64l-14.18-113.47-145.03 44.56z" })
                            ])),
                            createVNode("h2", {
                              id: "child-tracks-title",
                              class: "text-xl font-semibold"
                            }, toDisplayString(unref(t)("subheadings")), 1)
                          ]),
                          createVNode("div", { class: "mt-6" }, [
                            viewMode.value === "grid" ? (openBlock(), createBlock(_sfc_main$9, {
                              key: 0,
                              tracks: childTracks.value,
                              cols: gridCols.value
                            }, null, 8, ["tracks", "cols"])) : (openBlock(), createBlock(_sfc_main$a, {
                              key: 1,
                              tracks: childTracks.value
                            }, null, 8, ["tracks"]))
                          ])
                        ])) : createCommentVNode("", true),
                        createVNode(_sfc_main$b, { videos: mainVideos.value }, null, 8, ["videos"]),
                        createVNode(SectionBanners, { banners: mainBanners.value }, null, 8, ["banners"])
                      ])
                    ], 8, ["itemid"]),
                    showRight.value ? (openBlock(), createBlock("aside", {
                      key: 1,
                      class: ["shrink-0 pr-3 transition-all duration-300", rightCollapsed.value ? "lg:w-6" : "lg:w-72"]
                    }, [
                      createVNode(_sfc_main$c, {
                        collapsed: rightCollapsed.value,
                        onCollapsed: setRightCollapsed
                      }, null, 8, ["collapsed"])
                    ], 2)) : createCommentVNode("", true)
                  ])
                ])
              ]),
              createVNode(_sfc_main$d),
              createVNode(Progress),
              isAdmin.value ? (openBlock(), createBlock(_sfc_main$e, {
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
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Pages/Public/Default/School/SchoolTracks/Show.vue");
  return _sfc_setup ? _sfc_setup(props, ctx) : void 0;
};
export {
  _sfc_main as default
};
