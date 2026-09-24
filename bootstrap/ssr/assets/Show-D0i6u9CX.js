import { computed, ref, watch, onMounted, unref, withCtx, createVNode, createBlock, createCommentVNode, toDisplayString, openBlock, Fragment, renderList, createTextVNode, useSSRContext } from "vue";
import { ssrRenderComponent, ssrInterpolate, ssrRenderAttr, ssrRenderClass, ssrRenderList } from "vue/server-renderer";
import { usePage, Head, Link, router } from "@inertiajs/vue3";
import { useI18n } from "vue-i18n";
import { u as useSmoothScrollTo } from "./useSmoothScrollTo-g9G3kyDv.js";
import { _ as _sfc_main$1 } from "./DefaultLayout-6zxGxPtK.js";
import { _ as _sfc_main$2, a as _sfc_main$b, P as Progress } from "./Progress-CyfOKcqP.js";
import { _ as _sfc_main$3, a as _sfc_main$a } from "./RightSidebarSchool-DlAegojf.js";
import { _ as _sfc_main$4 } from "./EntityPageToolbar-DT32FtSd.js";
import { _ as _sfc_main$7, a as _sfc_main$8, b as _sfc_main$c } from "./PublicAdminBottomPanel-BhLEQMoJ.js";
import { _ as _sfc_main$9, S as SectionBanners } from "./SectionBanners-Cdgmr0Bw.js";
import { I as ImageGalleryMain } from "./ImageGalleryMain-mIfXDUWm.js";
import { _ as _sfc_main$5 } from "./CourseGrid-DoYn9a32.js";
import { _ as _sfc_main$6 } from "./CourseRows-Q6HeVebv.js";
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
const DEFAULT_SORT = "sortAsc";
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
    locale: {
      type: String,
      default: "ru"
    },
    instructorProfile: {
      type: Object,
      default: () => ({})
    },
    publicSchoolCoursesProcessingMode: {
      type: String,
      default: "server"
    },
    useServerProcessing: {
      type: Boolean,
      default: false
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
    const instructor = computed(
      () => props.instructorProfile ?? {}
    );
    const translation = computed(
      () => {
        var _a2;
        return ((_a2 = instructor.value) == null ? void 0 : _a2.translation) ?? {};
      }
    );
    const instructorName = computed(
      () => {
        var _a2, _b2, _c;
        return ((_a2 = translation.value) == null ? void 0 : _a2.title) || ((_c = (_b2 = instructor.value) == null ? void 0 : _b2.user) == null ? void 0 : _c.name) || t("instructor");
      }
    );
    const instructorShort = computed(
      () => {
        var _a2;
        return ((_a2 = translation.value) == null ? void 0 : _a2.short) || "";
      }
    );
    const instructorBio = computed(
      () => {
        var _a2;
        return ((_a2 = translation.value) == null ? void 0 : _a2.bio) || "";
      }
    );
    const instructorLocale = computed(
      () => {
        var _a2;
        return ((_a2 = translation.value) == null ? void 0 : _a2.locale) || props.locale || "ru";
      }
    );
    const ratingAvg = computed(
      () => {
        var _a2, _b2;
        return ((_b2 = (_a2 = instructor.value) == null ? void 0 : _a2.rating) == null ? void 0 : _b2.avg) ?? null;
      }
    );
    const ratingCount = computed(
      () => {
        var _a2, _b2;
        return safeNumber(
          (_b2 = (_a2 = instructor.value) == null ? void 0 : _a2.rating) == null ? void 0 : _b2.count
        );
      }
    );
    const formattedRating = computed(() => {
      if (ratingAvg.value === null || ratingAvg.value === void 0) {
        return "—";
      }
      return Number(
        ratingAvg.value
      ).toFixed(1);
    });
    const instructorImages = computed(
      () => {
        var _a2;
        return Array.isArray((_a2 = instructor.value) == null ? void 0 : _a2.images) ? instructor.value.images : [];
      }
    );
    const hasInstructorImages = computed(
      () => instructorImages.value.length > 0
    );
    const primaryImage = computed(
      () => instructorImages.value[0] ?? null
    );
    const getImageUrl = (image) => (image == null ? void 0 : image.webp_url) || (image == null ? void 0 : image.image_url) || (image == null ? void 0 : image.thumb_url) || (image == null ? void 0 : image.url) || (image == null ? void 0 : image.preview) || "";
    const seoImage = computed(
      () => getImageUrl(primaryImage.value)
    );
    const socialLinks = computed(
      () => {
        var _a2;
        return ((_a2 = instructor.value) == null ? void 0 : _a2.social_links) ?? {};
      }
    );
    const normalizedSocialLinks = computed(() => {
      if (Array.isArray(socialLinks.value)) {
        return socialLinks.value.filter((item) => item == null ? void 0 : item.url).map((item) => ({
          label: item.label || item.name || item.platform || t("link"),
          url: item.url
        }));
      }
      return Object.entries(socialLinks.value || {}).filter(([, value]) => Boolean(value)).map(([key, value]) => ({
        label: key,
        url: value
      }));
    });
    const detectSocialType = (item) => {
      const label = String(
        (item == null ? void 0 : item.label) || ""
      ).toLowerCase();
      const url = String(
        (item == null ? void 0 : item.url) || ""
      ).toLowerCase();
      if (label.includes("instagram") || url.includes("instagram.com")) return "instagram";
      if (label.includes("facebook") || url.includes("facebook.com")) return "facebook";
      if (label.includes("telegram") || url.includes("t.me") || url.includes("telegram.me")) return "telegram";
      if (label.includes("youtube") || url.includes("youtube.com") || url.includes("youtu.be")) return "youtube";
      if (label.includes("tiktok") || url.includes("tiktok.com")) return "tiktok";
      if (label.includes("linkedin") || url.includes("linkedin.com")) return "linkedin";
      if (label === "x" || label.includes("twitter") || url.includes("twitter.com") || url.includes("x.com")) return "x";
      if (label.includes("whatsapp") || url.includes("wa.me") || url.includes("whatsapp.com")) return "whatsapp";
      if (label === "vk" || label.includes("vkontakte") || url.includes("vk.com")) return "vk";
      if (label.includes("site") || label.includes("website")) return "website";
      return "link";
    };
    const getSocialLabel = (item) => {
      switch (detectSocialType(item)) {
        case "instagram":
          return "Instagram";
        case "facebook":
          return "Facebook";
        case "telegram":
          return "Telegram";
        case "youtube":
          return "YouTube";
        case "tiktok":
          return "TikTok";
        case "linkedin":
          return "LinkedIn";
        case "x":
          return "X";
        case "whatsapp":
          return "WhatsApp";
        case "vk":
          return "VK";
        case "website":
          return t("website");
        default:
          return (item == null ? void 0 : item.label) || t("link");
      }
    };
    const seoTitle = computed(
      () => {
        var _a2;
        return ((_a2 = translation.value) == null ? void 0 : _a2.meta_title) || instructorName.value;
      }
    );
    const seoDescription = computed(
      () => {
        var _a2;
        return ((_a2 = translation.value) == null ? void 0 : _a2.meta_desc) || instructorShort.value || "";
      }
    );
    const seoKeywords = computed(
      () => {
        var _a2;
        return ((_a2 = translation.value) == null ? void 0 : _a2.meta_keywords) || "";
      }
    );
    const ogLocale = computed(
      () => instructorLocale.value === "ru" ? "ru_RU" : instructorLocale.value
    );
    const canonicalUrl = computed(() => {
      var _a2;
      if (!((_a2 = instructor.value) == null ? void 0 : _a2.slug)) {
        return "";
      }
      return String(
        route("public.schoolInstructors.show", {
          slug: instructor.value.slug
        })
      );
    });
    const dcSubject = computed(
      () => seoKeywords.value || instructorName.value
    );
    const seoCreatedAt = computed(
      () => {
        var _a2;
        return ((_a2 = instructor.value) == null ? void 0 : _a2.created_at) || "";
      }
    );
    const coursesData = computed(
      () => normalizeList(props.courses)
    );
    const coursesCount = computed(
      () => safeNumber(props.coursesCount)
    );
    const hasCourses = computed(
      () => coursesCount.value > 0
    );
    const qCourses = ref(
      String(
        ((_a = props.filters) == null ? void 0 : _a.q_courses) ?? ""
      )
    );
    const sortCourses = ref(
      String(
        ((_b = props.filters) == null ? void 0 : _b.sort_courses) ?? DEFAULT_SORT
      )
    );
    const perPageCourses = computed(() => {
      var _a2;
      const value = Number(
        (_a2 = props.filters) == null ? void 0 : _a2.per_page_courses
      );
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
    const getCourseInstructorName = (course) => {
      var _a2, _b2, _c, _d;
      return ((_b2 = (_a2 = course == null ? void 0 : course.instructorProfile) == null ? void 0 : _a2.translation) == null ? void 0 : _b2.title) || ((_d = (_c = course == null ? void 0 : course.instructorProfile) == null ? void 0 : _c.user) == null ? void 0 : _d.name) || "";
    };
    const frontendFilteredCourses = computed(() => {
      if (props.useServerProcessing) {
        return coursesData.value;
      }
      const query = normalizeText(
        qCourses.value
      );
      if (!query) {
        return coursesData.value;
      }
      return coursesData.value.filter((course) => [
        course == null ? void 0 : course.id,
        course == null ? void 0 : course.slug,
        getCourseTitle(course),
        getCourseShort(course),
        getCourseInstructorName(course),
        course == null ? void 0 : course.level,
        course == null ? void 0 : course.availability
      ].some(
        (value) => normalizeText(value).includes(query)
      ));
    });
    const compareText = (a, b) => String(a ?? "").localeCompare(
      String(b ?? ""),
      props.locale,
      { sensitivity: "base" }
    );
    const compareNumber = (a, b) => safeNumber(a) - safeNumber(b);
    const frontendSortedCourses = computed(() => {
      if (props.useServerProcessing) {
        return frontendFilteredCourses.value;
      }
      const list = [
        ...frontendFilteredCourses.value
      ];
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
          (a, b) => desc ? compareNumber(
            b == null ? void 0 : b[field],
            a == null ? void 0 : a[field]
          ) : compareNumber(
            a == null ? void 0 : a[field],
            b == null ? void 0 : b[field]
          )
        );
        return list;
      }
      switch (sortCourses.value) {
        case "titleAsc":
          list.sort(
            (a, b) => compareText(
              getCourseTitle(a),
              getCourseTitle(b)
            )
          );
          break;
        case "titleDesc":
          list.sort(
            (a, b) => compareText(
              getCourseTitle(b),
              getCourseTitle(a)
            )
          );
          break;
        case "levelAsc":
          list.sort(
            (a, b) => compareText(
              a == null ? void 0 : a.level,
              b == null ? void 0 : b.level
            )
          );
          break;
        case "levelDesc":
          list.sort(
            (a, b) => compareText(
              b == null ? void 0 : b.level,
              a == null ? void 0 : a.level
            )
          );
          break;
        case "availabilityAsc":
          list.sort(
            (a, b) => compareText(
              a == null ? void 0 : a.availability,
              b == null ? void 0 : b.availability
            )
          );
          break;
        case "availabilityDesc":
          list.sort(
            (a, b) => compareText(
              b == null ? void 0 : b.availability,
              a == null ? void 0 : a.availability
            )
          );
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
    const {
      targetRef: coursesScrollTarget,
      scrollToTarget: scrollToCourses
    } = useSmoothScrollTo({
      offset: 80,
      duration: 1200
    });
    watch(
      [
        qCourses,
        sortCourses
      ],
      () => {
        if (!props.useServerProcessing) {
          frontendCurrentPage.value = 1;
        }
      }
    );
    watch(
      frontendCurrentPage,
      () => {
        if (!props.useServerProcessing) {
          scrollToCourses();
        }
      }
    );
    const effectiveCoursesFound = computed(
      () => props.useServerProcessing ? safeNumber(props.coursesFound) : frontendSortedCourses.value.length
    );
    const frontendPaginatedCourses = computed(() => {
      if (props.useServerProcessing) {
        return coursesData.value;
      }
      const start = (frontendCurrentPage.value - 1) * perPageCourses.value;
      return frontendSortedCourses.value.slice(
        start,
        start + perPageCourses.value
      );
    });
    const displayedCourses = computed(
      () => props.useServerProcessing ? coursesData.value : frontendPaginatedCourses.value
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
    const loadServerCourses = (pageNumber = 1) => {
      if (!props.useServerProcessing || !canonicalUrl.value) {
        return;
      }
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
        Math.max(
          1,
          Number(pageNumber) || 1
        ),
        lastPage.value
      );
      loadServerCourses(target);
    };
    const goPrev = () => {
      if (currentPage.value > 1) {
        goToPage(
          currentPage.value - 1
        );
      }
    };
    const goNext = () => {
      if (currentPage.value < lastPage.value) {
        goToPage(
          currentPage.value + 1
        );
      }
    };
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
    const getStoredView = () => {
      if (typeof window === "undefined") {
        return "grid";
      }
      const value = localStorage.getItem(VIEW_KEY);
      return ["grid", "rows"].includes(value) ? value : "grid";
    };
    const viewMode = ref(
      getStoredView()
    );
    watch(
      viewMode,
      (value) => {
        if (typeof window !== "undefined") {
          localStorage.setItem(
            VIEW_KEY,
            value
          );
        }
      }
    );
    const siteSettings = computed(
      () => {
        var _a2;
        return ((_a2 = page.props) == null ? void 0 : _a2.siteSettings) ?? {};
      }
    );
    const isAdmin = computed(
      () => {
        var _a2;
        return ((_a2 = page.props) == null ? void 0 : _a2.isAdmin) === true;
      }
    );
    const settingEnabled = (value, defaultValue = true) => {
      if (value === void 0 || value === null || value === "") {
        return defaultValue;
      }
      if (typeof value === "boolean") {
        return value;
      }
      return String(value) === "true";
    };
    const showLeft = computed(
      () => {
        var _a2;
        return settingEnabled(
          (_a2 = siteSettings.value) == null ? void 0 : _a2.ViewLeftColumn,
          true
        );
      }
    );
    const showRight = computed(
      () => {
        var _a2;
        return settingEnabled(
          (_a2 = siteSettings.value) == null ? void 0 : _a2.ViewRightColumn,
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
    const trackTree = computed(
      () => Array.isArray(props.trackTree) ? props.trackTree : []
    );
    const mainVideosList = computed(
      () => normalizeList(props.mainVideos)
    );
    const mainBannersList = computed(
      () => normalizeList(props.mainBanners)
    );
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
            _push2(`<meta name="robots" content="index, follow, max-image-preview:large"${_scopeId}>`);
            if (canonicalUrl.value) {
              _push2(`<link rel="canonical"${ssrRenderAttr("href", canonicalUrl.value)}${_scopeId}>`);
            } else {
              _push2(`<!---->`);
            }
            _push2(`<meta property="og:type" content="profile"${_scopeId}><meta property="og:title"${ssrRenderAttr("content", seoTitle.value)}${_scopeId}>`);
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
            if (seoImage.value) {
              _push2(`<meta property="og:image"${ssrRenderAttr("content", seoImage.value)}${_scopeId}>`);
            } else {
              _push2(`<!---->`);
            }
            _push2(`<meta name="twitter:card"${ssrRenderAttr(
              "content",
              seoImage.value ? "summary_large_image" : "summary"
            )}${_scopeId}><meta name="twitter:title"${ssrRenderAttr("content", seoTitle.value)}${_scopeId}>`);
            if (seoDescription.value) {
              _push2(`<meta name="twitter:description"${ssrRenderAttr("content", seoDescription.value)}${_scopeId}>`);
            } else {
              _push2(`<!---->`);
            }
            if (seoImage.value) {
              _push2(`<meta name="twitter:image"${ssrRenderAttr("content", seoImage.value)}${_scopeId}>`);
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
            _push2(`<meta name="DC.language"${ssrRenderAttr("content", instructorLocale.value)}${_scopeId}>`);
            if (canonicalUrl.value) {
              _push2(`<meta name="DC.identifier"${ssrRenderAttr("content", canonicalUrl.value)}${_scopeId}>`);
            } else {
              _push2(`<!---->`);
            }
            _push2(`<meta name="DC.type" content="Person"${_scopeId}><meta name="DC.format" content="text/html"${_scopeId}>`);
            if (seoCreatedAt.value) {
              _push2(`<meta name="DC.date"${ssrRenderAttr("content", seoCreatedAt.value)}${_scopeId}>`);
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
              createVNode("meta", {
                name: "robots",
                content: "index, follow, max-image-preview:large"
              }),
              canonicalUrl.value ? (openBlock(), createBlock("link", {
                key: 2,
                rel: "canonical",
                href: canonicalUrl.value
              }, null, 8, ["href"])) : createCommentVNode("", true),
              createVNode("meta", {
                property: "og:type",
                content: "profile"
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
              seoImage.value ? (openBlock(), createBlock("meta", {
                key: 5,
                property: "og:image",
                content: seoImage.value
              }, null, 8, ["content"])) : createCommentVNode("", true),
              createVNode("meta", {
                name: "twitter:card",
                content: seoImage.value ? "summary_large_image" : "summary"
              }, null, 8, ["content"]),
              createVNode("meta", {
                name: "twitter:title",
                content: seoTitle.value
              }, null, 8, ["content"]),
              seoDescription.value ? (openBlock(), createBlock("meta", {
                key: 6,
                name: "twitter:description",
                content: seoDescription.value
              }, null, 8, ["content"])) : createCommentVNode("", true),
              seoImage.value ? (openBlock(), createBlock("meta", {
                key: 7,
                name: "twitter:image",
                content: seoImage.value
              }, null, 8, ["content"])) : createCommentVNode("", true),
              createVNode("meta", {
                name: "DC.title",
                content: seoTitle.value
              }, null, 8, ["content"]),
              seoDescription.value ? (openBlock(), createBlock("meta", {
                key: 8,
                name: "DC.description",
                content: seoDescription.value
              }, null, 8, ["content"])) : createCommentVNode("", true),
              dcSubject.value ? (openBlock(), createBlock("meta", {
                key: 9,
                name: "DC.subject",
                content: dcSubject.value
              }, null, 8, ["content"])) : createCommentVNode("", true),
              createVNode("meta", {
                name: "DC.language",
                content: instructorLocale.value
              }, null, 8, ["content"]),
              canonicalUrl.value ? (openBlock(), createBlock("meta", {
                key: 10,
                name: "DC.identifier",
                content: canonicalUrl.value
              }, null, 8, ["content"])) : createCommentVNode("", true),
              createVNode("meta", {
                name: "DC.type",
                content: "Person"
              }),
              createVNode("meta", {
                name: "DC.format",
                content: "text/html"
              }),
              seoCreatedAt.value ? (openBlock(), createBlock("meta", {
                key: 11,
                name: "DC.date",
                content: seoCreatedAt.value
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
          if (_push2) {
            _push2(ssrRenderComponent(_sfc_main$2, null, null, _parent2, _scopeId));
            _push2(`<main class="min-h-screen px-1 lg:px-6 max-w-full"${_scopeId}><div class="mx-auto tracking-wider pt-20 lg:pt-44"${_scopeId}><div class="ext-color w-full min-w-0 py-3 px-1 flex flex-col lg:flex-row gap-4 rounded-3xl border-2 border-slate-300 dark:border-slate-500"${_scopeId}>`);
            if (showLeft.value) {
              _push2(`<aside class="${ssrRenderClass([
                leftCollapsed.value ? "lg:w-6" : "lg:w-72",
                "shrink-0 transition-all duration-300 overflow-hidden"
              ])}"${_scopeId}>`);
              _push2(ssrRenderComponent(_sfc_main$3, {
                "track-tree": trackTree.value,
                collapsed: leftCollapsed.value,
                onCollapsed: setLeftCollapsed
              }, null, _parent2, _scopeId));
              _push2(`</aside>`);
            } else {
              _push2(`<!---->`);
            }
            _push2(`<article itemscope itemtype="https://schema.org/ProfilePage"${ssrRenderAttr("itemid", canonicalUrl.value)} class="w-full pb-6 slate-1 min-w-0"${_scopeId}><meta itemprop="name"${ssrRenderAttr("content", seoTitle.value)}${_scopeId}>`);
            if (seoDescription.value) {
              _push2(`<meta itemprop="description"${ssrRenderAttr("content", seoDescription.value)}${_scopeId}>`);
            } else {
              _push2(`<!---->`);
            }
            _push2(`<meta itemprop="inLanguage"${ssrRenderAttr("content", instructorLocale.value)}${_scopeId}>`);
            if (canonicalUrl.value) {
              _push2(`<meta itemprop="url"${ssrRenderAttr("content", canonicalUrl.value)}${_scopeId}>`);
            } else {
              _push2(`<!---->`);
            }
            if (seoCreatedAt.value) {
              _push2(`<meta itemprop="dateCreated"${ssrRenderAttr("content", seoCreatedAt.value)}${_scopeId}>`);
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
            _push2(`<meta itemprop="position" content="1"${_scopeId}></li><li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem" class="flex items-center"${_scopeId}><span class="mx-2 breadcrumbs"${_scopeId}> / </span>`);
            _push2(ssrRenderComponent(unref(Link), {
              itemprop: "item",
              href: _ctx.route("public.schoolInstructors.index"),
              class: "breadcrumb-link hover:underline"
            }, {
              default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                if (_push3) {
                  _push3(`<span itemprop="name"${_scopeId2}>${ssrInterpolate(unref(t)("instructors"))}</span>`);
                } else {
                  return [
                    createVNode("span", { itemprop: "name" }, toDisplayString(unref(t)("instructors")), 1)
                  ];
                }
              }),
              _: 1
            }, _parent2, _scopeId));
            _push2(`<meta itemprop="position" content="2"${_scopeId}></li><li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem" class="flex items-center" aria-current="page"${_scopeId}><span class="mx-2 breadcrumbs"${_scopeId}> / </span><span itemprop="name" class="breadcrumbs"${_scopeId}>${ssrInterpolate(instructorName.value)}</span>`);
            if (canonicalUrl.value) {
              _push2(`<meta itemprop="item"${ssrRenderAttr("content", canonicalUrl.value)}${_scopeId}>`);
            } else {
              _push2(`<!---->`);
            }
            _push2(`<meta itemprop="position" content="3"${_scopeId}></li></ol></nav><div itemprop="mainEntity" itemscope itemtype="https://schema.org/Person"${_scopeId}><meta itemprop="name"${ssrRenderAttr("content", instructorName.value)}${_scopeId}>`);
            if (instructorShort.value) {
              _push2(`<meta itemprop="description"${ssrRenderAttr("content", instructorShort.value)}${_scopeId}>`);
            } else {
              _push2(`<!---->`);
            }
            if (canonicalUrl.value) {
              _push2(`<meta itemprop="url"${ssrRenderAttr("content", canonicalUrl.value)}${_scopeId}>`);
            } else {
              _push2(`<!---->`);
            }
            if (seoImage.value) {
              _push2(`<meta itemprop="image"${ssrRenderAttr("content", seoImage.value)}${_scopeId}>`);
            } else {
              _push2(`<!---->`);
            }
            _push2(`<!--[-->`);
            ssrRenderList(normalizedSocialLinks.value, (item, index) => {
              _push2(`<link itemprop="sameAs"${ssrRenderAttr("href", item.url)}${_scopeId}>`);
            });
            _push2(`<!--]-->`);
            if (hasInstructorImages.value) {
              _push2(`<div class="flex items-center justify-center"${_scopeId}><div class="w-full"${_scopeId}>`);
              _push2(ssrRenderComponent(ImageGalleryMain, {
                images: instructorImages.value,
                alt: instructorName.value,
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
              _push2(`<span class="text-center text-sm text-gray-500"${_scopeId}>${ssrInterpolate(coursesCount.value)}</span>`);
            } else {
              _push2(`<!---->`);
            }
            _push2(`</div><div class="my-3 flex flex-wrap items-center justify-center gap-3 title"${_scopeId}><h1 itemprop="headline" class="text-2xl font-bold"${_scopeId}>${ssrInterpolate(instructorName.value)}</h1></div><div${ssrRenderAttr("title", unref(t)("views"))} class="flex items-center justify-center gap-1"${_scopeId}><svg class="h-4 w-4 text-slate-600/85 dark:text-slate-200/85" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512" fill="currentColor"${_scopeId}><path d="M569.354 231.631C512.97 135.949 407.81 72 288 72 168.14 72 63.004 135.994 6.646 231.631a47.999 47.999 0 0 0 0 48.739C63.031 376.051 168.19 440 288 440c119.86 0 224.996-63.994 281.354-159.631a47.997 47.997 0 0 0 0-48.738zM288 392c-102.556 0-192.091-54.701-240-136 44.157-74.933 123.677-127.27 216.162-135.007C273.958 131.078 280 144.83 280 160c0 30.928-25.072 56-56 56s-56-25.072-56-56l.001-.042C157.794 179.043 152 200.844 152 224c0 75.111 60.889 136 136 136s136-60.889 136-136c0-31.031-10.4-59.629-27.895-82.515C451.704 164.638 498.009 205.106 528 256c-47.908 81.299-137.444 136-240 136z"${_scopeId}></path></svg><span class="text-center text-sm text-gray-500"${_scopeId}>${ssrInterpolate(instructor.value.views ?? 0)}</span></div></div>`);
            if (instructorShort.value) {
              _push2(`<div itemprop="abstract" class="mb-4 text-center text-sm text-slate-600 dark:text-slate-300"${_scopeId}>${ssrInterpolate(instructorShort.value)}</div>`);
            } else {
              _push2(`<!---->`);
            }
            _push2(`<div class="mb-4 flex flex-wrap items-center justify-center gap-3 text-sm text-slate-600 dark:text-slate-300"${_scopeId}><div class="rounded-sm border border-slate-400 px-3 py-1 flex items-center justify-center gap-1"${_scopeId}>${ssrInterpolate(unref(t)("rating"))}: <svg viewBox="0 0 24 24" class="shrink-0 h-4 w-4"${_scopeId}><path class="fill-current text-red-400 dark:text-red-300" d="M12.746,1.464l3.11,6.3L22.81,8.776a.831.831,0,0,1,.461,1.418l-5.033,4.9,1.188,6.926a.832.832,0,0,1-1.207.877L12,19.632,5.78,22.9a.833.833,0,0,1-1.207-.878L5.761,15.1l-5.033-4.9a.831.831,0,0,1,.461-1.418L8.143,7.765l3.11-6.3A.833.833,0,0,1,12.746,1.464Z"${_scopeId}></path></svg> ${ssrInterpolate(formattedRating.value)} (${ssrInterpolate(ratingCount.value)}) </div>`);
            if (safeNumber(instructor.value.experience_years) > 0) {
              _push2(`<div class="relative inline-flex text-center px-3 py-1 rounded bg-emerald-500"${_scopeId}><div class="absolute w-3 h-3 rounded-full bg-white left-0 -translate-x-1/2 top-1/2 -translate-y-1/2" aria-hidden="true"${_scopeId}></div><div class="absolute w-3 h-3 rounded-full bg-white right-0 translate-x-1/2 top-1/2 -translate-y-1/2" aria-hidden="true"${_scopeId}></div><span class="text-sm text-emerald-50 font-medium"${_scopeId}>${ssrInterpolate(unref(t)("experienceYears"))}: ${ssrInterpolate(instructor.value.experience_years)}</span></div>`);
            } else {
              _push2(`<!---->`);
            }
            if (instructor.value.hourly_rate) {
              _push2(`<div class="rounded-sm border border-slate-400 px-3 py-1"${_scopeId}>${ssrInterpolate(unref(t)("hourlyRate"))}: ${ssrInterpolate(instructor.value.hourly_rate)}</div>`);
            } else {
              _push2(`<!---->`);
            }
            _push2(`</div>`);
            if (instructorBio.value) {
              _push2(`<div itemprop="description" class="mb-6 rounded-md border border-gray-200 bg-white p-4 text-sm leading-7 text-slate-700 shadow-sm dark:border-gray-700 dark:bg-gray-900 dark:text-slate-300"${_scopeId}>${instructorBio.value ?? ""}</div>`);
            } else {
              _push2(`<!---->`);
            }
            if (normalizedSocialLinks.value.length) {
              _push2(`<div class="mb-6 flex flex-wrap items-center justify-center gap-3"${_scopeId}><!--[-->`);
              ssrRenderList(normalizedSocialLinks.value, (item, index) => {
                _push2(`<a${ssrRenderAttr("href", item.url)} target="_blank" rel="noopener noreferrer" class="rounded-md px-3 py-1 btn-default text-sm font-semibold inline-flex items-center gap-2"${_scopeId}><svg class="h-4 w-4" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"${_scopeId}><path d="M14 3h7v7h-2V6.41l-8.29 8.3-1.42-1.42L17.59 5H14V3ZM5 5h6v2H5v12h12v-6h2v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V7a2 2 0 0 1 2-2Z"${_scopeId}></path></svg><span${_scopeId}>${ssrInterpolate(getSocialLabel(item))}</span></a>`);
              });
              _push2(`<!--]--></div>`);
            } else {
              _push2(`<!---->`);
            }
            _push2(`</div>`);
            if (hasCourses.value) {
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
            } else {
              _push2(`<!---->`);
            }
            _push2(`<div${_scopeId}></div>`);
            if (hasCourses.value && displayedCourses.value.length === 0) {
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
            if (__props.useServerProcessing && hasCourses.value && lastPage.value > 1) {
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
            _push2(ssrRenderComponent(_sfc_main$9, { videos: mainVideosList.value }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(SectionBanners, { banners: mainBannersList.value }, null, _parent2, _scopeId));
            _push2(`</div></article>`);
            if (showRight.value) {
              _push2(`<aside class="${ssrRenderClass([
                rightCollapsed.value ? "lg:w-6" : "lg:w-72",
                "shrink-0 transition-all duration-300 overflow-hidden"
              ])}"${_scopeId}>`);
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
                total: coursesCount.value
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
                      class: [
                        "shrink-0 transition-all duration-300 overflow-hidden",
                        leftCollapsed.value ? "lg:w-6" : "lg:w-72"
                      ]
                    }, [
                      createVNode(_sfc_main$3, {
                        "track-tree": trackTree.value,
                        collapsed: leftCollapsed.value,
                        onCollapsed: setLeftCollapsed
                      }, null, 8, ["track-tree", "collapsed"])
                    ], 2)) : createCommentVNode("", true),
                    createVNode("article", {
                      itemscope: "",
                      itemtype: "https://schema.org/ProfilePage",
                      itemid: canonicalUrl.value,
                      class: "w-full pb-6 slate-1 min-w-0"
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
                      createVNode("meta", {
                        itemprop: "inLanguage",
                        content: instructorLocale.value
                      }, null, 8, ["content"]),
                      canonicalUrl.value ? (openBlock(), createBlock("meta", {
                        key: 1,
                        itemprop: "url",
                        content: canonicalUrl.value
                      }, null, 8, ["content"])) : createCommentVNode("", true),
                      seoCreatedAt.value ? (openBlock(), createBlock("meta", {
                        key: 2,
                        itemprop: "dateCreated",
                        content: seoCreatedAt.value
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
                              createVNode("span", { class: "mx-2 breadcrumbs" }, " / "),
                              createVNode(unref(Link), {
                                itemprop: "item",
                                href: _ctx.route("public.schoolInstructors.index"),
                                class: "breadcrumb-link hover:underline"
                              }, {
                                default: withCtx(() => [
                                  createVNode("span", { itemprop: "name" }, toDisplayString(unref(t)("instructors")), 1)
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
                              }, toDisplayString(instructorName.value), 1),
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
                        createVNode("div", {
                          itemprop: "mainEntity",
                          itemscope: "",
                          itemtype: "https://schema.org/Person"
                        }, [
                          createVNode("meta", {
                            itemprop: "name",
                            content: instructorName.value
                          }, null, 8, ["content"]),
                          instructorShort.value ? (openBlock(), createBlock("meta", {
                            key: 0,
                            itemprop: "description",
                            content: instructorShort.value
                          }, null, 8, ["content"])) : createCommentVNode("", true),
                          canonicalUrl.value ? (openBlock(), createBlock("meta", {
                            key: 1,
                            itemprop: "url",
                            content: canonicalUrl.value
                          }, null, 8, ["content"])) : createCommentVNode("", true),
                          seoImage.value ? (openBlock(), createBlock("meta", {
                            key: 2,
                            itemprop: "image",
                            content: seoImage.value
                          }, null, 8, ["content"])) : createCommentVNode("", true),
                          (openBlock(true), createBlock(Fragment, null, renderList(normalizedSocialLinks.value, (item, index) => {
                            return openBlock(), createBlock("link", {
                              key: `same-as-${index}`,
                              itemprop: "sameAs",
                              href: item.url
                            }, null, 8, ["href"]);
                          }), 128)),
                          hasInstructorImages.value ? (openBlock(), createBlock("div", {
                            key: 3,
                            class: "flex items-center justify-center"
                          }, [
                            createVNode("div", { class: "w-full" }, [
                              createVNode(ImageGalleryMain, {
                                images: instructorImages.value,
                                alt: instructorName.value,
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
                              }, toDisplayString(coursesCount.value), 1)) : createCommentVNode("", true)
                            ], 8, ["title"]),
                            createVNode("div", { class: "my-3 flex flex-wrap items-center justify-center gap-3 title" }, [
                              createVNode("h1", {
                                itemprop: "headline",
                                class: "text-2xl font-bold"
                              }, toDisplayString(instructorName.value), 1)
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
                              createVNode("span", { class: "text-center text-sm text-gray-500" }, toDisplayString(instructor.value.views ?? 0), 1)
                            ], 8, ["title"])
                          ]),
                          instructorShort.value ? (openBlock(), createBlock("div", {
                            key: 4,
                            itemprop: "abstract",
                            class: "mb-4 text-center text-sm text-slate-600 dark:text-slate-300"
                          }, toDisplayString(instructorShort.value), 1)) : createCommentVNode("", true),
                          createVNode("div", { class: "mb-4 flex flex-wrap items-center justify-center gap-3 text-sm text-slate-600 dark:text-slate-300" }, [
                            createVNode("div", { class: "rounded-sm border border-slate-400 px-3 py-1 flex items-center justify-center gap-1" }, [
                              createTextVNode(toDisplayString(unref(t)("rating")) + ": ", 1),
                              (openBlock(), createBlock("svg", {
                                viewBox: "0 0 24 24",
                                class: "shrink-0 h-4 w-4"
                              }, [
                                createVNode("path", {
                                  class: "fill-current text-red-400 dark:text-red-300",
                                  d: "M12.746,1.464l3.11,6.3L22.81,8.776a.831.831,0,0,1,.461,1.418l-5.033,4.9,1.188,6.926a.832.832,0,0,1-1.207.877L12,19.632,5.78,22.9a.833.833,0,0,1-1.207-.878L5.761,15.1l-5.033-4.9a.831.831,0,0,1,.461-1.418L8.143,7.765l3.11-6.3A.833.833,0,0,1,12.746,1.464Z"
                                })
                              ])),
                              createTextVNode(" " + toDisplayString(formattedRating.value) + " (" + toDisplayString(ratingCount.value) + ") ", 1)
                            ]),
                            safeNumber(instructor.value.experience_years) > 0 ? (openBlock(), createBlock("div", {
                              key: 0,
                              class: "relative inline-flex text-center px-3 py-1 rounded bg-emerald-500"
                            }, [
                              createVNode("div", {
                                class: "absolute w-3 h-3 rounded-full bg-white left-0 -translate-x-1/2 top-1/2 -translate-y-1/2",
                                "aria-hidden": "true"
                              }),
                              createVNode("div", {
                                class: "absolute w-3 h-3 rounded-full bg-white right-0 translate-x-1/2 top-1/2 -translate-y-1/2",
                                "aria-hidden": "true"
                              }),
                              createVNode("span", { class: "text-sm text-emerald-50 font-medium" }, toDisplayString(unref(t)("experienceYears")) + ": " + toDisplayString(instructor.value.experience_years), 1)
                            ])) : createCommentVNode("", true),
                            instructor.value.hourly_rate ? (openBlock(), createBlock("div", {
                              key: 1,
                              class: "rounded-sm border border-slate-400 px-3 py-1"
                            }, toDisplayString(unref(t)("hourlyRate")) + ": " + toDisplayString(instructor.value.hourly_rate), 1)) : createCommentVNode("", true)
                          ]),
                          instructorBio.value ? (openBlock(), createBlock("div", {
                            key: 5,
                            itemprop: "description",
                            class: "mb-6 rounded-md border border-gray-200 bg-white p-4 text-sm leading-7 text-slate-700 shadow-sm dark:border-gray-700 dark:bg-gray-900 dark:text-slate-300",
                            innerHTML: instructorBio.value
                          }, null, 8, ["innerHTML"])) : createCommentVNode("", true),
                          normalizedSocialLinks.value.length ? (openBlock(), createBlock("div", {
                            key: 6,
                            class: "mb-6 flex flex-wrap items-center justify-center gap-3"
                          }, [
                            (openBlock(true), createBlock(Fragment, null, renderList(normalizedSocialLinks.value, (item, index) => {
                              return openBlock(), createBlock("a", {
                                key: index,
                                href: item.url,
                                target: "_blank",
                                rel: "noopener noreferrer",
                                class: "rounded-md px-3 py-1 btn-default text-sm font-semibold inline-flex items-center gap-2"
                              }, [
                                (openBlock(), createBlock("svg", {
                                  class: "h-4 w-4",
                                  viewBox: "0 0 24 24",
                                  fill: "currentColor",
                                  "aria-hidden": "true"
                                }, [
                                  createVNode("path", { d: "M14 3h7v7h-2V6.41l-8.29 8.3-1.42-1.42L17.59 5H14V3ZM5 5h6v2H5v12h12v-6h2v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V7a2 2 0 0 1 2-2Z" })
                                ])),
                                createVNode("span", null, toDisplayString(getSocialLabel(item)), 1)
                              ], 8, ["href"]);
                            }), 128))
                          ])) : createCommentVNode("", true)
                        ]),
                        hasCourses.value ? (openBlock(), createBlock(_sfc_main$4, {
                          key: 0,
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
                        }, null, 8, ["modelValue", "onUpdate:modelValue", "view-mode", "onUpdate:viewMode", "sort-value", "onUpdate:sortValue", "found", "found-label", "search-placeholder"])) : createCommentVNode("", true),
                        createVNode("div", {
                          ref_key: "coursesScrollTarget",
                          ref: coursesScrollTarget
                        }, null, 512),
                        hasCourses.value && displayedCourses.value.length === 0 ? (openBlock(), createBlock("div", {
                          key: 1,
                          class: "mt-6 text-center text-slate-700 dark:text-slate-300"
                        }, toDisplayString(unref(t)("noData")), 1)) : createCommentVNode("", true),
                        displayedCourses.value.length ? (openBlock(), createBlock("div", { key: 2 }, [
                          viewMode.value === "grid" ? (openBlock(), createBlock(_sfc_main$5, {
                            key: 0,
                            courses: displayedCourses.value,
                            cols: gridCols.value
                          }, null, 8, ["courses", "cols"])) : (openBlock(), createBlock(_sfc_main$6, {
                            key: 1,
                            courses: displayedCourses.value
                          }, null, 8, ["courses"]))
                        ])) : createCommentVNode("", true),
                        __props.useServerProcessing && hasCourses.value && lastPage.value > 1 ? (openBlock(), createBlock(_sfc_main$7, {
                          key: 3,
                          "current-page": currentPage.value,
                          "last-page": lastPage.value,
                          found: __props.coursesFound,
                          onPrev: goPrev,
                          onNext: goNext,
                          onGo: goToPage
                        }, null, 8, ["current-page", "last-page", "found"])) : createCommentVNode("", true),
                        !__props.useServerProcessing && effectiveCoursesFound.value > perPageCourses.value ? (openBlock(), createBlock(_sfc_main$8, {
                          key: 4,
                          currentPage: frontendCurrentPage.value,
                          "onUpdate:currentPage": ($event) => frontendCurrentPage.value = $event,
                          "items-per-page": perPageCourses.value,
                          "total-items": effectiveCoursesFound.value
                        }, null, 8, ["currentPage", "onUpdate:currentPage", "items-per-page", "total-items"])) : createCommentVNode("", true),
                        createVNode(_sfc_main$9, { videos: mainVideosList.value }, null, 8, ["videos"]),
                        createVNode(SectionBanners, { banners: mainBannersList.value }, null, 8, ["banners"])
                      ])
                    ], 8, ["itemid"]),
                    showRight.value ? (openBlock(), createBlock("aside", {
                      key: 1,
                      class: [
                        "shrink-0 transition-all duration-300 overflow-hidden",
                        rightCollapsed.value ? "lg:w-6" : "lg:w-72"
                      ]
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
                total: coursesCount.value
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
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Pages/Public/Default/School/SchoolInstructors/Show.vue");
  return _sfc_setup ? _sfc_setup(props, ctx) : void 0;
};
export {
  _sfc_main as default
};
