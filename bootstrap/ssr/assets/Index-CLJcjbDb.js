import { computed, ref, onMounted, watch, unref, withCtx, createVNode, createBlock, createCommentVNode, toDisplayString, openBlock, useSSRContext } from "vue";
import { ssrRenderComponent, ssrInterpolate, ssrRenderAttr, ssrRenderClass } from "vue/server-renderer";
import { usePage, Head, Link, router } from "@inertiajs/vue3";
import { useI18n } from "vue-i18n";
import { u as useSmoothScrollTo } from "./useSmoothScrollTo-g9G3kyDv.js";
import { _ as _sfc_main$1 } from "./DefaultLayout-6zxGxPtK.js";
import { _ as _sfc_main$2, a as _sfc_main$b, P as Progress } from "./Progress-CyfOKcqP.js";
import { _ as _sfc_main$3, a as _sfc_main$a } from "./RightSidebarSchool-DlAegojf.js";
import { _ as _sfc_main$4 } from "./EntityPageToolbar-DT32FtSd.js";
import { _ as _sfc_main$7, a as _sfc_main$8, b as _sfc_main$c } from "./PublicAdminBottomPanel-BhLEQMoJ.js";
import { _ as _sfc_main$9, S as SectionBanners } from "./SectionBanners-Cdgmr0Bw.js";
import { _ as _sfc_main$5, a as _sfc_main$6 } from "./TrackRows-BjGnlkhH.js";
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
const VIEW_KEY = "public_school_tracks_view";
const LEFT_SIDEBAR_KEY = "public_left_sidebar_collapsed";
const RIGHT_SIDEBAR_KEY = "public_right_sidebar_collapsed";
const _sfc_main = {
  __name: "Index",
  __ssrInlineRender: true,
  props: {
    locale: { type: String, default: "ru" },
    seo: {
      type: Object,
      default: () => ({
        title: "",
        keywords: "",
        description: ""
      })
    },
    publicSchoolTracksProcessingMode: {
      type: String,
      default: "server"
    },
    useServerProcessing: {
      type: Boolean,
      default: false
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
    tracks: {
      type: [Array, Object],
      default: () => []
    },
    tracksCount: {
      type: Number,
      default: 0
    },
    tracksFound: {
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
    var _a, _b, _c;
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
    const trackTree = computed(
      () => Array.isArray(props.trackTree) ? props.trackTree : []
    );
    const mainVideos = computed(
      () => normalizeList(props.mainVideos)
    );
    const mainBanners = computed(
      () => normalizeList(props.mainBanners)
    );
    const tracksData = computed(
      () => normalizeList(props.tracks)
    );
    const getTrackTitle = (track) => {
      var _a2;
      return ((_a2 = track == null ? void 0 : track.translation) == null ? void 0 : _a2.name) || "";
    };
    const getTrackShort = (track) => {
      var _a2;
      return ((_a2 = track == null ? void 0 : track.translation) == null ? void 0 : _a2.short) || "";
    };
    const getTrackSlug = (track) => (track == null ? void 0 : track.slug) || "";
    const seoTitle = computed(
      () => {
        var _a2;
        return ((_a2 = props.seo) == null ? void 0 : _a2.title) || t("learningCategories");
      }
    );
    const seoDescription = computed(
      () => {
        var _a2;
        return ((_a2 = props.seo) == null ? void 0 : _a2.description) || t("learningCategories");
      }
    );
    const seoKeywords = computed(
      () => {
        var _a2;
        return ((_a2 = props.seo) == null ? void 0 : _a2.keywords) || "";
      }
    );
    const contentLocale = computed(
      () => props.locale || "ru"
    );
    const ogLocale = computed(
      () => contentLocale.value === "ru" ? "ru_RU" : contentLocale.value
    );
    const canonicalUrl = computed(
      () => String(route("public.schoolTracks.index"))
    );
    const seoImage = computed(() => {
      for (const track of tracksData.value) {
        const images = Array.isArray(track == null ? void 0 : track.images) ? track.images : [];
        const image = images[0];
        const url = (image == null ? void 0 : image.webp_url) || (image == null ? void 0 : image.image_url) || (image == null ? void 0 : image.thumb_url) || (image == null ? void 0 : image.url) || "";
        if (url) return url;
      }
      return "";
    });
    const dcSubject = computed(
      () => seoKeywords.value || seoTitle.value
    );
    const q = ref(
      String(((_a = props.filters) == null ? void 0 : _a.q) ?? "")
    );
    const sort = ref(
      String(((_b = props.filters) == null ? void 0 : _b.sort) ?? DEFAULT_SORT)
    );
    const perPage = computed(() => {
      var _a2;
      const value = Number(
        (_a2 = props.filters) == null ? void 0 : _a2.per_page
      );
      return Number.isFinite(value) && value > 0 ? value : 12;
    });
    const trackSortOptions = [
      { value: "sortAsc", label: `${t("sortNumber")} 0→9` },
      { value: "sortDesc", label: `${t("sortNumber")} 9→0` },
      { value: "idDesc", label: t("idDesc") },
      { value: "idAsc", label: t("idAsc") },
      { value: "nameAsc", label: `${t("title")} A→Z` },
      { value: "nameDesc", label: `${t("title")} Z→A` },
      { value: "slugAsc", label: "URL A→Z" },
      { value: "slugDesc", label: "URL Z→A" },
      { value: "viewsDesc", label: `${t("views")} 9→0` },
      { value: "viewsAsc", label: `${t("views")} 0→9` },
      { value: "likesDesc", label: `${t("likes")} 9→0` },
      { value: "likesAsc", label: `${t("likes")} 0→9` },
      { value: "childrenDesc", label: `${t("subheadings")} 9→0` },
      { value: "childrenAsc", label: `${t("subheadings")} 0→9` },
      { value: "coursesDesc", label: `${t("courses")} 9→0` },
      { value: "coursesAsc", label: `${t("courses")} 0→9` },
      { value: "imagesDesc", label: `${t("images")} 9→0` },
      { value: "imagesAsc", label: `${t("images")} 0→9` },
      { value: "dateDesc", label: t("sortNewestFirst") },
      { value: "dateAsc", label: t("sortOldestFirst") }
    ];
    const viewMode = ref(
      String(((_c = props.filters) == null ? void 0 : _c.view) || "grid")
    );
    onMounted(() => {
      try {
        const storedView = localStorage.getItem(VIEW_KEY);
        if (["grid", "rows"].includes(storedView)) {
          viewMode.value = storedView;
        }
      } catch {
      }
    });
    watch(viewMode, (value) => {
      try {
        localStorage.setItem(VIEW_KEY, value);
      } catch {
      }
    });
    const filteredTracks = computed(() => {
      if (props.useServerProcessing) {
        return tracksData.value;
      }
      const query = normalizeText(q.value);
      if (!query) {
        return tracksData.value;
      }
      return tracksData.value.filter((track) => {
        return [
          track == null ? void 0 : track.id,
          getTrackTitle(track),
          getTrackShort(track),
          getTrackSlug(track)
        ].some(
          (value) => normalizeText(value).includes(query)
        );
      });
    });
    const compareText = (a, b) => String(a ?? "").localeCompare(
      String(b ?? ""),
      props.locale,
      { sensitivity: "base" }
    );
    const compareNumber = (a, b) => safeNumber(a) - safeNumber(b);
    const sortedTracks = computed(() => {
      if (props.useServerProcessing) {
        return filteredTracks.value;
      }
      const list = [...filteredTracks.value];
      switch (sort.value) {
        case "sortAsc":
          list.sort(
            (a, b) => compareNumber(a == null ? void 0 : a.sort, b == null ? void 0 : b.sort) || compareNumber(b == null ? void 0 : b.id, a == null ? void 0 : a.id)
          );
          break;
        case "sortDesc":
          list.sort(
            (a, b) => compareNumber(b == null ? void 0 : b.sort, a == null ? void 0 : a.sort) || compareNumber(b == null ? void 0 : b.id, a == null ? void 0 : a.id)
          );
          break;
        case "idAsc":
          list.sort(
            (a, b) => compareNumber(a == null ? void 0 : a.id, b == null ? void 0 : b.id)
          );
          break;
        case "idDesc":
          list.sort(
            (a, b) => compareNumber(b == null ? void 0 : b.id, a == null ? void 0 : a.id)
          );
          break;
        case "nameAsc":
        case "titleAsc":
          list.sort(
            (a, b) => compareText(
              getTrackTitle(a),
              getTrackTitle(b)
            ) || compareNumber(b == null ? void 0 : b.id, a == null ? void 0 : a.id)
          );
          break;
        case "nameDesc":
        case "titleDesc":
          list.sort(
            (a, b) => compareText(
              getTrackTitle(b),
              getTrackTitle(a)
            ) || compareNumber(b == null ? void 0 : b.id, a == null ? void 0 : a.id)
          );
          break;
        case "slugAsc":
          list.sort(
            (a, b) => compareText(
              getTrackSlug(a),
              getTrackSlug(b)
            ) || compareNumber(b == null ? void 0 : b.id, a == null ? void 0 : a.id)
          );
          break;
        case "slugDesc":
          list.sort(
            (a, b) => compareText(
              getTrackSlug(b),
              getTrackSlug(a)
            ) || compareNumber(b == null ? void 0 : b.id, a == null ? void 0 : a.id)
          );
          break;
        case "viewsAsc":
          list.sort(
            (a, b) => compareNumber(
              a == null ? void 0 : a.views,
              b == null ? void 0 : b.views
            ) || compareNumber(b == null ? void 0 : b.id, a == null ? void 0 : a.id)
          );
          break;
        case "viewsDesc":
          list.sort(
            (a, b) => compareNumber(
              b == null ? void 0 : b.views,
              a == null ? void 0 : a.views
            ) || compareNumber(b == null ? void 0 : b.id, a == null ? void 0 : a.id)
          );
          break;
        case "likesAsc":
          list.sort(
            (a, b) => compareNumber(
              a == null ? void 0 : a.likes_count,
              b == null ? void 0 : b.likes_count
            ) || compareNumber(b == null ? void 0 : b.id, a == null ? void 0 : a.id)
          );
          break;
        case "likesDesc":
          list.sort(
            (a, b) => compareNumber(
              b == null ? void 0 : b.likes_count,
              a == null ? void 0 : a.likes_count
            ) || compareNumber(b == null ? void 0 : b.id, a == null ? void 0 : a.id)
          );
          break;
        case "childrenAsc":
          list.sort(
            (a, b) => compareNumber(
              a == null ? void 0 : a.children_count,
              b == null ? void 0 : b.children_count
            ) || compareNumber(b == null ? void 0 : b.id, a == null ? void 0 : a.id)
          );
          break;
        case "childrenDesc":
          list.sort(
            (a, b) => compareNumber(
              b == null ? void 0 : b.children_count,
              a == null ? void 0 : a.children_count
            ) || compareNumber(b == null ? void 0 : b.id, a == null ? void 0 : a.id)
          );
          break;
        case "coursesAsc":
          list.sort(
            (a, b) => compareNumber(
              a == null ? void 0 : a.courses_count,
              b == null ? void 0 : b.courses_count
            ) || compareNumber(b == null ? void 0 : b.id, a == null ? void 0 : a.id)
          );
          break;
        case "coursesDesc":
          list.sort(
            (a, b) => compareNumber(
              b == null ? void 0 : b.courses_count,
              a == null ? void 0 : a.courses_count
            ) || compareNumber(b == null ? void 0 : b.id, a == null ? void 0 : a.id)
          );
          break;
        case "imagesAsc":
          list.sort(
            (a, b) => compareNumber(
              a == null ? void 0 : a.images_count,
              b == null ? void 0 : b.images_count
            ) || compareNumber(b == null ? void 0 : b.id, a == null ? void 0 : a.id)
          );
          break;
        case "imagesDesc":
          list.sort(
            (a, b) => compareNumber(
              b == null ? void 0 : b.images_count,
              a == null ? void 0 : a.images_count
            ) || compareNumber(b == null ? void 0 : b.id, a == null ? void 0 : a.id)
          );
          break;
        case "dateAsc":
          list.sort(
            (a, b) => safeDate(a == null ? void 0 : a.created_at) - safeDate(b == null ? void 0 : b.created_at) || compareNumber(b == null ? void 0 : b.id, a == null ? void 0 : a.id)
          );
          break;
        case "dateDesc":
          list.sort(
            (a, b) => safeDate(b == null ? void 0 : b.created_at) - safeDate(a == null ? void 0 : a.created_at) || compareNumber(b == null ? void 0 : b.id, a == null ? void 0 : a.id)
          );
          break;
      }
      return list;
    });
    const frontendCurrentPage = ref(1);
    const {
      targetRef: scrollTarget,
      scrollToTarget
    } = useSmoothScrollTo({
      offset: 80,
      duration: 1200
    });
    watch([q, sort], () => {
      if (!props.useServerProcessing) {
        frontendCurrentPage.value = 1;
      }
    });
    watch(frontendCurrentPage, () => {
      if (!props.useServerProcessing) {
        scrollToTarget();
      }
    });
    const effectiveTracksFound = computed(
      () => props.useServerProcessing ? Number(props.tracksFound ?? 0) : sortedTracks.value.length
    );
    const frontendPaginatedTracks = computed(() => {
      if (props.useServerProcessing) {
        return tracksData.value;
      }
      const start = (frontendCurrentPage.value - 1) * perPage.value;
      return sortedTracks.value.slice(
        start,
        start + perPage.value
      );
    });
    const displayedTracks = computed(
      () => props.useServerProcessing ? tracksData.value : frontendPaginatedTracks.value
    );
    const currentPage = computed(
      () => {
        var _a2, _b2, _c2;
        return Number(
          ((_b2 = (_a2 = props.tracks) == null ? void 0 : _a2.meta) == null ? void 0 : _b2.current_page) ?? ((_c2 = props.tracks) == null ? void 0 : _c2.current_page) ?? 1
        ) || 1;
      }
    );
    const lastPage = computed(
      () => {
        var _a2, _b2, _c2;
        return Number(
          ((_b2 = (_a2 = props.tracks) == null ? void 0 : _a2.meta) == null ? void 0 : _b2.last_page) ?? ((_c2 = props.tracks) == null ? void 0 : _c2.last_page) ?? 1
        ) || 1;
      }
    );
    const reloadTracks = (pageNumber = 1) => {
      if (!props.useServerProcessing) {
        return;
      }
      router.get(
        route("public.schoolTracks.index"),
        {
          q: q.value || void 0,
          sort: sort.value || void 0,
          page: pageNumber
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
      reloadTracks(target);
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
    const applyFilters = () => {
      if (props.useServerProcessing) {
        reloadTracks(1);
      } else {
        frontendCurrentPage.value = 1;
      }
    };
    const resetFilters = () => {
      if (props.useServerProcessing) {
        reloadTracks(1);
      } else {
        frontendCurrentPage.value = 1;
      }
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
    const trackGridCols = computed(() => {
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
            _push2(`<meta name="robots" content="index, follow, max-image-preview:large"${_scopeId}><link rel="canonical"${ssrRenderAttr("href", canonicalUrl.value)}${_scopeId}><meta property="og:type" content="website"${_scopeId}><meta property="og:title"${ssrRenderAttr("content", seoTitle.value)}${_scopeId}>`);
            if (seoDescription.value) {
              _push2(`<meta property="og:description"${ssrRenderAttr("content", seoDescription.value)}${_scopeId}>`);
            } else {
              _push2(`<!---->`);
            }
            _push2(`<meta property="og:url"${ssrRenderAttr("content", canonicalUrl.value)}${_scopeId}><meta property="og:locale"${ssrRenderAttr("content", ogLocale.value)}${_scopeId}>`);
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
            _push2(`<meta name="DC.language"${ssrRenderAttr("content", contentLocale.value)}${_scopeId}><meta name="DC.identifier"${ssrRenderAttr("content", canonicalUrl.value)}${_scopeId}><meta name="DC.type" content="Collection"${_scopeId}><meta name="DC.format" content="text/html"${_scopeId}>`);
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
              createVNode("link", {
                rel: "canonical",
                href: canonicalUrl.value
              }, null, 8, ["href"]),
              createVNode("meta", {
                property: "og:type",
                content: "website"
              }),
              createVNode("meta", {
                property: "og:title",
                content: seoTitle.value
              }, null, 8, ["content"]),
              seoDescription.value ? (openBlock(), createBlock("meta", {
                key: 2,
                property: "og:description",
                content: seoDescription.value
              }, null, 8, ["content"])) : createCommentVNode("", true),
              createVNode("meta", {
                property: "og:url",
                content: canonicalUrl.value
              }, null, 8, ["content"]),
              createVNode("meta", {
                property: "og:locale",
                content: ogLocale.value
              }, null, 8, ["content"]),
              seoImage.value ? (openBlock(), createBlock("meta", {
                key: 3,
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
                key: 4,
                name: "twitter:description",
                content: seoDescription.value
              }, null, 8, ["content"])) : createCommentVNode("", true),
              seoImage.value ? (openBlock(), createBlock("meta", {
                key: 5,
                name: "twitter:image",
                content: seoImage.value
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
              createVNode("meta", {
                name: "DC.identifier",
                content: canonicalUrl.value
              }, null, 8, ["content"]),
              createVNode("meta", {
                name: "DC.type",
                content: "Collection"
              }),
              createVNode("meta", {
                name: "DC.format",
                content: "text/html"
              })
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
                "shrink-0 transition-all duration-300"
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
            _push2(`<article itemscope itemtype="https://schema.org/CollectionPage"${ssrRenderAttr("itemid", canonicalUrl.value)} class="w-full pb-6 slate-1 min-w-0"${_scopeId}><meta itemprop="name"${ssrRenderAttr("content", seoTitle.value)}${_scopeId}>`);
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
            _push2(`<meta itemprop="url"${ssrRenderAttr("content", canonicalUrl.value)}${_scopeId}><meta itemprop="inLanguage"${ssrRenderAttr("content", contentLocale.value)}${_scopeId}><div class="mx-auto max-w-6xl"${_scopeId}><nav class="text-sm" aria-label="Breadcrumb" itemscope itemtype="https://schema.org/BreadcrumbList"${_scopeId}><ol class="flex flex-wrap items-center font-semibold"${_scopeId}><li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem" class="flex items-center"${_scopeId}>`);
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
            _push2(`<meta itemprop="position" content="2"${_scopeId}></li><li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem" class="flex items-center" aria-current="page"${_scopeId}><span class="mx-2 breadcrumbs"${_scopeId}> / </span><span itemprop="name" class="breadcrumbs"${_scopeId}>${ssrInterpolate(unref(t)("tracks"))}</span><meta itemprop="item"${ssrRenderAttr("content", canonicalUrl.value)}${_scopeId}><meta itemprop="position" content="3"${_scopeId}></li></ol></nav><div class="my-3 flex flex-wrap items-center justify-center gap-3 title"${_scopeId}><svg class="shrink-0 h-5 w-5 text-slate-600/85 dark:text-slate-200/85" fill="currentColor" viewBox="0 0 24 24"${_scopeId}><path d="M23.58.424A1,1,0,0,0,22.819.13C8.791.862,3.609,13.358,3.559,13.484a1,1,0,0,0,.22,1.08l5.657,5.657a1,1,0,0,0,1.085.218c.125-.051,12.554-5.291,13.348-19.253A1,1,0,0,0,23.58.424Zm-8.166,10.99a2,2,0,1,1,0-2.828A2,2,0,0,1,15.414,11.414Z"${_scopeId}></path><path d="M1.113,18.844a2.844,2.844,0,1,1,4.022,4.022C4.024,23.977,0,24,0,24S0,19.954,1.113,18.844Z"${_scopeId}></path><path d="M10.357,2.341A8.911,8.911,0,0,0,2.522,4.825a9.084,9.084,0,0,0-1.384,1.8,1,1,0,0,0,.155,1.215l1.989,1.99A26.623,26.623,0,0,1,10.357,2.341Z"${_scopeId}></path><path d="M21.659,13.643a8.911,8.911,0,0,1-2.484,7.835,9.084,9.084,0,0,1-1.8,1.384,1,1,0,0,1-1.215-.155l-1.99-1.989A26.623,26.623,0,0,0,21.659,13.643Z"${_scopeId}></path></svg><h1 itemprop="headline" class="text-2xl font-bold"${_scopeId}>${ssrInterpolate(unref(t)("tracks"))}</h1></div>`);
            if (seoDescription.value) {
              _push2(`<div itemprop="abstract" class="my-1 text-sm subtitle text-center"${_scopeId}>${ssrInterpolate(seoDescription.value)}</div>`);
            } else {
              _push2(`<!---->`);
            }
            _push2(ssrRenderComponent(_sfc_main$4, {
              modelValue: q.value,
              "onUpdate:modelValue": ($event) => q.value = $event,
              "view-mode": viewMode.value,
              "onUpdate:viewMode": ($event) => viewMode.value = $event,
              "sort-value": sort.value,
              "onUpdate:sortValue": ($event) => sort.value = $event,
              found: effectiveTracksFound.value,
              "sort-options": trackSortOptions,
              "default-sort": DEFAULT_SORT,
              "found-label": unref(t)("tracks"),
              "search-placeholder": unref(t)("searchByName"),
              onSubmit: applyFilters,
              onReset: resetFilters
            }, null, _parent2, _scopeId));
            _push2(`<div${_scopeId}></div>`);
            if (displayedTracks.value.length === 0) {
              _push2(`<div class="mt-6 text-center text-slate-700 dark:text-slate-300"${_scopeId}>${ssrInterpolate(unref(t)("noData"))}</div>`);
            } else {
              _push2(`<div${_scopeId}>`);
              if (viewMode.value === "grid") {
                _push2(ssrRenderComponent(_sfc_main$5, {
                  tracks: displayedTracks.value,
                  cols: trackGridCols.value
                }, null, _parent2, _scopeId));
              } else {
                _push2(ssrRenderComponent(_sfc_main$6, { tracks: displayedTracks.value }, null, _parent2, _scopeId));
              }
              _push2(`</div>`);
            }
            if (__props.useServerProcessing && lastPage.value > 1) {
              _push2(ssrRenderComponent(_sfc_main$7, {
                "current-page": currentPage.value,
                "last-page": lastPage.value,
                found: __props.tracksFound,
                onPrev: goPrev,
                onNext: goNext,
                onGo: goToPage
              }, null, _parent2, _scopeId));
            } else {
              _push2(`<!---->`);
            }
            if (!__props.useServerProcessing && effectiveTracksFound.value > perPage.value) {
              _push2(ssrRenderComponent(_sfc_main$8, {
                currentPage: frontendCurrentPage.value,
                "onUpdate:currentPage": ($event) => frontendCurrentPage.value = $event,
                "items-per-page": perPage.value,
                "total-items": effectiveTracksFound.value
              }, null, _parent2, _scopeId));
            } else {
              _push2(`<!---->`);
            }
            _push2(ssrRenderComponent(_sfc_main$9, { videos: mainVideos.value }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(SectionBanners, { banners: mainBanners.value }, null, _parent2, _scopeId));
            _push2(`</div></article>`);
            if (showRight.value) {
              _push2(`<aside class="${ssrRenderClass([
                rightCollapsed.value ? "lg:w-6" : "lg:w-72",
                "shrink-0 transition-all duration-300"
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
                "setting-key": "publicSchoolTracksProcessingMode",
                mode: __props.publicSchoolTracksProcessingMode,
                "use-server-processing": __props.useServerProcessing,
                total: __props.tracksCount
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
                        "shrink-0 transition-all duration-300",
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
                      itemtype: "https://schema.org/CollectionPage",
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
                      seoKeywords.value ? (openBlock(), createBlock("meta", {
                        key: 1,
                        itemprop: "keywords",
                        content: seoKeywords.value
                      }, null, 8, ["content"])) : createCommentVNode("", true),
                      createVNode("meta", {
                        itemprop: "url",
                        content: canonicalUrl.value
                      }, null, 8, ["content"]),
                      createVNode("meta", {
                        itemprop: "inLanguage",
                        content: contentLocale.value
                      }, null, 8, ["content"]),
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
                              }, toDisplayString(unref(t)("tracks")), 1),
                              createVNode("meta", {
                                itemprop: "item",
                                content: canonicalUrl.value
                              }, null, 8, ["content"]),
                              createVNode("meta", {
                                itemprop: "position",
                                content: "3"
                              })
                            ])
                          ])
                        ]),
                        createVNode("div", { class: "my-3 flex flex-wrap items-center justify-center gap-3 title" }, [
                          (openBlock(), createBlock("svg", {
                            class: "shrink-0 h-5 w-5 text-slate-600/85 dark:text-slate-200/85",
                            fill: "currentColor",
                            viewBox: "0 0 24 24"
                          }, [
                            createVNode("path", { d: "M23.58.424A1,1,0,0,0,22.819.13C8.791.862,3.609,13.358,3.559,13.484a1,1,0,0,0,.22,1.08l5.657,5.657a1,1,0,0,0,1.085.218c.125-.051,12.554-5.291,13.348-19.253A1,1,0,0,0,23.58.424Zm-8.166,10.99a2,2,0,1,1,0-2.828A2,2,0,0,1,15.414,11.414Z" }),
                            createVNode("path", { d: "M1.113,18.844a2.844,2.844,0,1,1,4.022,4.022C4.024,23.977,0,24,0,24S0,19.954,1.113,18.844Z" }),
                            createVNode("path", { d: "M10.357,2.341A8.911,8.911,0,0,0,2.522,4.825a9.084,9.084,0,0,0-1.384,1.8,1,1,0,0,0,.155,1.215l1.989,1.99A26.623,26.623,0,0,1,10.357,2.341Z" }),
                            createVNode("path", { d: "M21.659,13.643a8.911,8.911,0,0,1-2.484,7.835,9.084,9.084,0,0,1-1.8,1.384,1,1,0,0,1-1.215-.155l-1.99-1.989A26.623,26.623,0,0,0,21.659,13.643Z" })
                          ])),
                          createVNode("h1", {
                            itemprop: "headline",
                            class: "text-2xl font-bold"
                          }, toDisplayString(unref(t)("tracks")), 1)
                        ]),
                        seoDescription.value ? (openBlock(), createBlock("div", {
                          key: 0,
                          itemprop: "abstract",
                          class: "my-1 text-sm subtitle text-center"
                        }, toDisplayString(seoDescription.value), 1)) : createCommentVNode("", true),
                        createVNode(_sfc_main$4, {
                          modelValue: q.value,
                          "onUpdate:modelValue": ($event) => q.value = $event,
                          "view-mode": viewMode.value,
                          "onUpdate:viewMode": ($event) => viewMode.value = $event,
                          "sort-value": sort.value,
                          "onUpdate:sortValue": ($event) => sort.value = $event,
                          found: effectiveTracksFound.value,
                          "sort-options": trackSortOptions,
                          "default-sort": DEFAULT_SORT,
                          "found-label": unref(t)("tracks"),
                          "search-placeholder": unref(t)("searchByName"),
                          onSubmit: applyFilters,
                          onReset: resetFilters
                        }, null, 8, ["modelValue", "onUpdate:modelValue", "view-mode", "onUpdate:viewMode", "sort-value", "onUpdate:sortValue", "found", "found-label", "search-placeholder"]),
                        createVNode("div", {
                          ref_key: "scrollTarget",
                          ref: scrollTarget
                        }, null, 512),
                        displayedTracks.value.length === 0 ? (openBlock(), createBlock("div", {
                          key: 1,
                          class: "mt-6 text-center text-slate-700 dark:text-slate-300"
                        }, toDisplayString(unref(t)("noData")), 1)) : (openBlock(), createBlock("div", { key: 2 }, [
                          viewMode.value === "grid" ? (openBlock(), createBlock(_sfc_main$5, {
                            key: 0,
                            tracks: displayedTracks.value,
                            cols: trackGridCols.value
                          }, null, 8, ["tracks", "cols"])) : (openBlock(), createBlock(_sfc_main$6, {
                            key: 1,
                            tracks: displayedTracks.value
                          }, null, 8, ["tracks"]))
                        ])),
                        __props.useServerProcessing && lastPage.value > 1 ? (openBlock(), createBlock(_sfc_main$7, {
                          key: 3,
                          "current-page": currentPage.value,
                          "last-page": lastPage.value,
                          found: __props.tracksFound,
                          onPrev: goPrev,
                          onNext: goNext,
                          onGo: goToPage
                        }, null, 8, ["current-page", "last-page", "found"])) : createCommentVNode("", true),
                        !__props.useServerProcessing && effectiveTracksFound.value > perPage.value ? (openBlock(), createBlock(_sfc_main$8, {
                          key: 4,
                          currentPage: frontendCurrentPage.value,
                          "onUpdate:currentPage": ($event) => frontendCurrentPage.value = $event,
                          "items-per-page": perPage.value,
                          "total-items": effectiveTracksFound.value
                        }, null, 8, ["currentPage", "onUpdate:currentPage", "items-per-page", "total-items"])) : createCommentVNode("", true),
                        createVNode(_sfc_main$9, { videos: mainVideos.value }, null, 8, ["videos"]),
                        createVNode(SectionBanners, { banners: mainBanners.value }, null, 8, ["banners"])
                      ])
                    ], 8, ["itemid"]),
                    showRight.value ? (openBlock(), createBlock("aside", {
                      key: 1,
                      class: [
                        "shrink-0 transition-all duration-300",
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
                "setting-key": "publicSchoolTracksProcessingMode",
                mode: __props.publicSchoolTracksProcessingMode,
                "use-server-processing": __props.useServerProcessing,
                total: __props.tracksCount
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
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Pages/Public/Default/School/SchoolTracks/Index.vue");
  return _sfc_setup ? _sfc_setup(props, ctx) : void 0;
};
export {
  _sfc_main as default
};
