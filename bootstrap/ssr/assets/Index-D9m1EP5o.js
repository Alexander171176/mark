import { computed, ref, onMounted, watch, unref, withCtx, createVNode, createBlock, createCommentVNode, toDisplayString, openBlock, useSSRContext } from "vue";
import { ssrRenderComponent, ssrInterpolate, ssrRenderAttr, ssrRenderClass } from "vue/server-renderer";
import { usePage, Head, Link, router } from "@inertiajs/vue3";
import { useI18n } from "vue-i18n";
import { u as useSmoothScrollTo } from "./useSmoothScrollTo-g9G3kyDv.js";
import { _ as _sfc_main$1 } from "./DefaultLayout-6zxGxPtK.js";
import { _ as _sfc_main$2, a as _sfc_main$b, P as Progress } from "./Progress-CyfOKcqP.js";
import { _ as _sfc_main$3, a as _sfc_main$a } from "./RightSidebar-OMLtHB67.js";
import { _ as _sfc_main$4 } from "./EntityPageToolbar-DT32FtSd.js";
import { _ as _sfc_main$7, a as _sfc_main$8, b as _sfc_main$c } from "./PublicAdminBottomPanel-BhLEQMoJ.js";
import { _ as _sfc_main$5, a as _sfc_main$6 } from "./RubricRows-CxJVX4pL.js";
import { _ as _sfc_main$9, S as SectionBanners } from "./SectionBanners-Cdgmr0Bw.js";
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
const DEFAULT_SORT = "sortAsc";
const VIEW_KEY = "public_blog_rubrics_view";
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
    publicBlogRubricsProcessingMode: {
      type: String,
      default: "server"
    },
    useServerProcessing: {
      type: Boolean,
      default: false
    },
    title: { type: String, default: "" },
    canLogin: { type: Boolean, default: false },
    canRegister: { type: Boolean, default: false },
    rubricTree: {
      type: Array,
      default: () => []
    },
    rubrics: {
      type: [Array, Object],
      default: () => []
    },
    rubricsCount: {
      type: Number,
      default: 0
    },
    rubricsFound: {
      type: Number,
      default: 0
    },
    filters: {
      type: Object,
      default: () => ({})
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
    const rubricTree = computed(
      () => Array.isArray(props.rubricTree) ? props.rubricTree : []
    );
    const rubricsData = computed(
      () => normalizeList(props.rubrics)
    );
    const mainVideos = computed(
      () => normalizeList(props.mainVideos)
    );
    const mainBanners = computed(
      () => normalizeList(props.mainBanners)
    );
    const getRubricTitle = (rubric) => {
      var _a2;
      return ((_a2 = rubric == null ? void 0 : rubric.translation) == null ? void 0 : _a2.title) || "";
    };
    const getRubricShort = (rubric) => {
      var _a2;
      return ((_a2 = rubric == null ? void 0 : rubric.translation) == null ? void 0 : _a2.short) || "";
    };
    const seoTitle = computed(
      () => {
        var _a2;
        return ((_a2 = props.seo) == null ? void 0 : _a2.title) || t("rubrics");
      }
    );
    const seoDescription = computed(
      () => {
        var _a2;
        return ((_a2 = props.seo) == null ? void 0 : _a2.description) || "";
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
      () => String(
        route("public.blogRubrics.index")
      )
    );
    const dcSubject = computed(
      () => seoKeywords.value || seoTitle.value
    );
    const seoImage = computed(() => {
      for (const rubric of rubricsData.value) {
        const images = normalizeList(
          rubric == null ? void 0 : rubric.images
        );
        const image = images[0];
        const url = (image == null ? void 0 : image.webp_url) || (image == null ? void 0 : image.image_url) || (image == null ? void 0 : image.thumb_url) || (image == null ? void 0 : image.url) || "";
        if (url) return url;
      }
      return "";
    });
    const q = ref(
      String(
        ((_a = props.filters) == null ? void 0 : _a.q) ?? ""
      )
    );
    const sort = ref(
      String(
        ((_b = props.filters) == null ? void 0 : _b.sort) ?? DEFAULT_SORT
      )
    );
    const perPage = computed(() => {
      var _a2;
      const value = Number(
        (_a2 = props.filters) == null ? void 0 : _a2.per_page
      );
      return Number.isFinite(value) && value > 0 ? value : 12;
    });
    const rubricSortOptions = [
      { value: "sortAsc", label: `${t("sortNumber")} 0→9` },
      { value: "sortDesc", label: `${t("sortNumber")} 9→0` },
      { value: "idDesc", label: t("idDesc") },
      { value: "idAsc", label: t("idAsc") },
      { value: "titleAsc", label: `${t("title")} A→Z` },
      { value: "titleDesc", label: `${t("title")} Z→A` },
      { value: "viewsDesc", label: `${t("views")} 9→0` },
      { value: "viewsAsc", label: `${t("views")} 0→9` },
      { value: "articlesDesc", label: `${t("articles")} 9→0` },
      { value: "articlesAsc", label: `${t("articles")} 0→9` },
      { value: "dateDesc", label: `${t("createdAt")} ↓` },
      { value: "dateAsc", label: `${t("createdAt")} ↑` }
    ];
    const viewMode = ref(
      String(
        ((_c = props.filters) == null ? void 0 : _c.view) || "grid"
      )
    );
    onMounted(() => {
      try {
        const storedView = localStorage.getItem(VIEW_KEY);
        if (storedView === "grid" || storedView === "rows") {
          viewMode.value = storedView;
        }
      } catch {
      }
    });
    watch(
      viewMode,
      (value) => {
        try {
          localStorage.setItem(
            VIEW_KEY,
            value
          );
        } catch {
        }
      }
    );
    const filteredRubrics = computed(() => {
      if (props.useServerProcessing) {
        return rubricsData.value;
      }
      const query = normalizeText(q.value);
      if (!query) {
        return rubricsData.value;
      }
      return rubricsData.value.filter(
        (rubric) => {
          var _a2;
          return [
            rubric == null ? void 0 : rubric.id,
            getRubricTitle(rubric),
            getRubricShort(rubric),
            rubric == null ? void 0 : rubric.url,
            (_a2 = rubric == null ? void 0 : rubric.owner) == null ? void 0 : _a2.name
          ].some(
            (value) => normalizeText(value).includes(query)
          );
        }
      );
    });
    const compareText = (a, b) => String(a ?? "").localeCompare(
      String(b ?? ""),
      props.locale,
      { sensitivity: "base" }
    );
    const compareNumber = (a, b) => safeNumber(a) - safeNumber(b);
    const sortedRubrics = computed(() => {
      if (props.useServerProcessing) {
        return filteredRubrics.value;
      }
      const list = [
        ...filteredRubrics.value
      ];
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
        case "titleAsc":
          list.sort(
            (a, b) => compareText(
              getRubricTitle(a),
              getRubricTitle(b)
            )
          );
          break;
        case "titleDesc":
          list.sort(
            (a, b) => compareText(
              getRubricTitle(b),
              getRubricTitle(a)
            )
          );
          break;
        case "viewsAsc":
          list.sort(
            (a, b) => compareNumber(
              a == null ? void 0 : a.views,
              b == null ? void 0 : b.views
            )
          );
          break;
        case "viewsDesc":
          list.sort(
            (a, b) => compareNumber(
              b == null ? void 0 : b.views,
              a == null ? void 0 : a.views
            )
          );
          break;
        case "articlesAsc":
          list.sort(
            (a, b) => compareNumber(
              a == null ? void 0 : a.articles_count,
              b == null ? void 0 : b.articles_count
            )
          );
          break;
        case "articlesDesc":
          list.sort(
            (a, b) => compareNumber(
              b == null ? void 0 : b.articles_count,
              a == null ? void 0 : a.articles_count
            )
          );
          break;
        case "dateAsc":
          list.sort(
            (a, b) => safeDate(a == null ? void 0 : a.created_at) - safeDate(b == null ? void 0 : b.created_at)
          );
          break;
        case "dateDesc":
          list.sort(
            (a, b) => safeDate(b == null ? void 0 : b.created_at) - safeDate(a == null ? void 0 : a.created_at)
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
    watch(
      [q, sort],
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
          scrollToTarget();
        }
      }
    );
    const effectiveRubricsFound = computed(
      () => props.useServerProcessing ? safeNumber(
        props.rubricsFound
      ) : sortedRubrics.value.length
    );
    const frontendPaginatedRubrics = computed(() => {
      if (props.useServerProcessing) {
        return rubricsData.value;
      }
      const start = (frontendCurrentPage.value - 1) * perPage.value;
      return sortedRubrics.value.slice(
        start,
        start + perPage.value
      );
    });
    const displayedRubrics = computed(
      () => props.useServerProcessing ? rubricsData.value : frontendPaginatedRubrics.value
    );
    const currentPage = computed(
      () => {
        var _a2, _b2, _c2;
        return Number(
          ((_b2 = (_a2 = props.rubrics) == null ? void 0 : _a2.meta) == null ? void 0 : _b2.current_page) ?? ((_c2 = props.rubrics) == null ? void 0 : _c2.current_page) ?? 1
        ) || 1;
      }
    );
    const lastPage = computed(
      () => {
        var _a2, _b2, _c2;
        return Number(
          ((_b2 = (_a2 = props.rubrics) == null ? void 0 : _a2.meta) == null ? void 0 : _b2.last_page) ?? ((_c2 = props.rubrics) == null ? void 0 : _c2.last_page) ?? 1
        ) || 1;
      }
    );
    const reloadRubrics = (pageNumber = 1) => {
      if (!props.useServerProcessing) {
        return;
      }
      router.get(
        canonicalUrl.value,
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
      reloadRubrics(target);
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
        reloadRubrics(1);
      } else {
        frontendCurrentPage.value = 1;
      }
    };
    const resetFilters = () => {
      if (props.useServerProcessing) {
        reloadRubrics(1);
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
    const rubricGridCols = computed(() => {
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
            if (seoImage.value) {
              _push2(`<meta property="og:image:alt"${ssrRenderAttr("content", seoTitle.value)}${_scopeId}>`);
            } else {
              _push2(`<!---->`);
            }
            if (seoImage.value) {
              _push2(`<meta name="twitter:image:alt"${ssrRenderAttr("content", seoTitle.value)}${_scopeId}>`);
            } else {
              _push2(`<!---->`);
            }
            _push2(`<meta name="twitter:card"${ssrRenderAttr("content", seoImage.value ? "summary_large_image" : "summary")}${_scopeId}><meta name="twitter:title"${ssrRenderAttr("content", seoTitle.value)}${_scopeId}>`);
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
              seoImage.value ? (openBlock(), createBlock("meta", {
                key: 4,
                property: "og:image:alt",
                content: seoTitle.value
              }, null, 8, ["content"])) : createCommentVNode("", true),
              seoImage.value ? (openBlock(), createBlock("meta", {
                key: 5,
                name: "twitter:image:alt",
                content: seoTitle.value
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
            _push2(`<meta itemprop="position" content="1"${_scopeId}></li><li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem" class="flex items-center" aria-current="page"${_scopeId}><span class="mx-2 breadcrumbs"${_scopeId}>/</span><span itemprop="name" class="breadcrumbs"${_scopeId}>${ssrInterpolate(unref(t)("rubrics"))}</span><meta itemprop="item"${ssrRenderAttr("content", canonicalUrl.value)}${_scopeId}><meta itemprop="position" content="2"${_scopeId}></li></ol></nav><div class="my-3 flex flex-wrap items-center justify-center gap-3 title"${_scopeId}><svg class="h-5 w-5" viewBox="0 0 512 512" fill="currentColor"${_scopeId}><path d="M489.2 287.9h-27.4c-2.6 0-4.6 2-4.6 4.6v32h-36.6V146.2c0-2.6-2-4.6-4.6-4.6h-27.4c-2.6 0-4.6 2-4.6 4.6v32h-36.6v-32c0-2.6-2-4.6-4.6-4.6h-27.4c-2.6 0-4.6 2-4.6 4.6v32h-36.6v-32c0-6-8-4.6-11.7-4.6v-38c8.3-2 17.1-3.4 25.7-3.4 10.9 0 20.9 4.3 31.4 4.3 4.6 0 27.7-1.1 27.7-8v-60c0-2.6-2-4.6-4.6-4.6-5.1 0-15.1 4.3-24 4.3-9.7 0-20.9-4.3-32.6-4.3-8 0-16 1.1-23.7 2.9v-4.9c5.4-2.6 9.1-8.3 9.1-14.3 0-20.7-31.4-20.8-31.4 0 0 6 3.7 11.7 9.1 14.3v111.7c-3.7 0-11.7-1.4-11.7 4.6v32h-36.6v-32c0-2.6-2-4.6-4.6-4.6h-27.4c-2.6 0-4.6 2-4.6 4.6v32H128v-32c0-2.6-2-4.6-4.6-4.6H96c-2.6 0-4.6 2-4.6 4.6v178.3H54.8v-32c0-2.6-2-4.6-4.6-4.6H22.8c-2.6 0-4.6 2-4.6 4.6V512h182.9v-96c0-72.6 109.7-72.6 109.7 0v96h182.9V292.5c.1-2.6-1.9-4.6-4.5-4.6z"${_scopeId}></path></svg><h1 itemprop="headline" class="text-2xl font-bold"${_scopeId}>${ssrInterpolate(unref(t)("rubrics"))}</h1></div>`);
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
              found: effectiveRubricsFound.value,
              "sort-options": rubricSortOptions,
              "default-sort": DEFAULT_SORT,
              "found-label": unref(t)("rubrics"),
              "search-placeholder": unref(t)("searchByName"),
              onSubmit: applyFilters,
              onReset: resetFilters
            }, null, _parent2, _scopeId));
            _push2(`<div${_scopeId}></div>`);
            if (displayedRubrics.value.length === 0) {
              _push2(`<div class="mt-6 text-center text-slate-700 dark:text-slate-300"${_scopeId}>${ssrInterpolate(unref(t)("noData"))}</div>`);
            } else {
              _push2(`<div${_scopeId}>`);
              if (viewMode.value === "grid") {
                _push2(ssrRenderComponent(_sfc_main$5, {
                  rubrics: displayedRubrics.value,
                  cols: rubricGridCols.value
                }, null, _parent2, _scopeId));
              } else {
                _push2(ssrRenderComponent(_sfc_main$6, { rubrics: displayedRubrics.value }, null, _parent2, _scopeId));
              }
              _push2(`</div>`);
            }
            if (__props.useServerProcessing && lastPage.value > 1) {
              _push2(ssrRenderComponent(_sfc_main$7, {
                "current-page": currentPage.value,
                "last-page": lastPage.value,
                found: __props.rubricsFound,
                onPrev: goPrev,
                onNext: goNext,
                onGo: goToPage
              }, null, _parent2, _scopeId));
            } else {
              _push2(`<!---->`);
            }
            if (!__props.useServerProcessing && effectiveRubricsFound.value > perPage.value) {
              _push2(ssrRenderComponent(_sfc_main$8, {
                currentPage: frontendCurrentPage.value,
                "onUpdate:currentPage": ($event) => frontendCurrentPage.value = $event,
                "items-per-page": perPage.value,
                "total-items": effectiveRubricsFound.value
              }, null, _parent2, _scopeId));
            } else {
              _push2(`<!---->`);
            }
            _push2(ssrRenderComponent(_sfc_main$9, { videos: mainVideos.value }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(SectionBanners, { banners: mainBanners.value }, null, _parent2, _scopeId));
            _push2(`</div></article>`);
            if (showRight.value) {
              _push2(`<aside class="${ssrRenderClass([rightCollapsed.value ? "lg:w-6" : "lg:w-72", "shrink-0 transition-all duration-300 overflow-hidden"])}"${_scopeId}>`);
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
                "setting-key": "publicBlogRubricsProcessingMode",
                mode: __props.publicBlogRubricsProcessingMode,
                "use-server-processing": __props.useServerProcessing,
                total: __props.rubricsCount
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
                      class: ["shrink-0 transition-all duration-300 overflow-hidden", leftCollapsed.value ? "lg:w-6" : "lg:w-72"]
                    }, [
                      createVNode(_sfc_main$3, {
                        "rubric-tree": rubricTree.value,
                        collapsed: leftCollapsed.value,
                        onCollapsed: setLeftCollapsed
                      }, null, 8, ["rubric-tree", "collapsed"])
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
                              class: "flex items-center",
                              "aria-current": "page"
                            }, [
                              createVNode("span", { class: "mx-2 breadcrumbs" }, "/"),
                              createVNode("span", {
                                itemprop: "name",
                                class: "breadcrumbs"
                              }, toDisplayString(unref(t)("rubrics")), 1),
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
                            class: "h-5 w-5",
                            viewBox: "0 0 512 512",
                            fill: "currentColor"
                          }, [
                            createVNode("path", { d: "M489.2 287.9h-27.4c-2.6 0-4.6 2-4.6 4.6v32h-36.6V146.2c0-2.6-2-4.6-4.6-4.6h-27.4c-2.6 0-4.6 2-4.6 4.6v32h-36.6v-32c0-2.6-2-4.6-4.6-4.6h-27.4c-2.6 0-4.6 2-4.6 4.6v32h-36.6v-32c0-6-8-4.6-11.7-4.6v-38c8.3-2 17.1-3.4 25.7-3.4 10.9 0 20.9 4.3 31.4 4.3 4.6 0 27.7-1.1 27.7-8v-60c0-2.6-2-4.6-4.6-4.6-5.1 0-15.1 4.3-24 4.3-9.7 0-20.9-4.3-32.6-4.3-8 0-16 1.1-23.7 2.9v-4.9c5.4-2.6 9.1-8.3 9.1-14.3 0-20.7-31.4-20.8-31.4 0 0 6 3.7 11.7 9.1 14.3v111.7c-3.7 0-11.7-1.4-11.7 4.6v32h-36.6v-32c0-2.6-2-4.6-4.6-4.6h-27.4c-2.6 0-4.6 2-4.6 4.6v32H128v-32c0-2.6-2-4.6-4.6-4.6H96c-2.6 0-4.6 2-4.6 4.6v178.3H54.8v-32c0-2.6-2-4.6-4.6-4.6H22.8c-2.6 0-4.6 2-4.6 4.6V512h182.9v-96c0-72.6 109.7-72.6 109.7 0v96h182.9V292.5c.1-2.6-1.9-4.6-4.5-4.6z" })
                          ])),
                          createVNode("h1", {
                            itemprop: "headline",
                            class: "text-2xl font-bold"
                          }, toDisplayString(unref(t)("rubrics")), 1)
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
                          found: effectiveRubricsFound.value,
                          "sort-options": rubricSortOptions,
                          "default-sort": DEFAULT_SORT,
                          "found-label": unref(t)("rubrics"),
                          "search-placeholder": unref(t)("searchByName"),
                          onSubmit: applyFilters,
                          onReset: resetFilters
                        }, null, 8, ["modelValue", "onUpdate:modelValue", "view-mode", "onUpdate:viewMode", "sort-value", "onUpdate:sortValue", "found", "found-label", "search-placeholder"]),
                        createVNode("div", {
                          ref_key: "scrollTarget",
                          ref: scrollTarget
                        }, null, 512),
                        displayedRubrics.value.length === 0 ? (openBlock(), createBlock("div", {
                          key: 1,
                          class: "mt-6 text-center text-slate-700 dark:text-slate-300"
                        }, toDisplayString(unref(t)("noData")), 1)) : (openBlock(), createBlock("div", { key: 2 }, [
                          viewMode.value === "grid" ? (openBlock(), createBlock(_sfc_main$5, {
                            key: 0,
                            rubrics: displayedRubrics.value,
                            cols: rubricGridCols.value
                          }, null, 8, ["rubrics", "cols"])) : (openBlock(), createBlock(_sfc_main$6, {
                            key: 1,
                            rubrics: displayedRubrics.value
                          }, null, 8, ["rubrics"]))
                        ])),
                        __props.useServerProcessing && lastPage.value > 1 ? (openBlock(), createBlock(_sfc_main$7, {
                          key: 3,
                          "current-page": currentPage.value,
                          "last-page": lastPage.value,
                          found: __props.rubricsFound,
                          onPrev: goPrev,
                          onNext: goNext,
                          onGo: goToPage
                        }, null, 8, ["current-page", "last-page", "found"])) : createCommentVNode("", true),
                        !__props.useServerProcessing && effectiveRubricsFound.value > perPage.value ? (openBlock(), createBlock(_sfc_main$8, {
                          key: 4,
                          currentPage: frontendCurrentPage.value,
                          "onUpdate:currentPage": ($event) => frontendCurrentPage.value = $event,
                          "items-per-page": perPage.value,
                          "total-items": effectiveRubricsFound.value
                        }, null, 8, ["currentPage", "onUpdate:currentPage", "items-per-page", "total-items"])) : createCommentVNode("", true),
                        createVNode(_sfc_main$9, { videos: mainVideos.value }, null, 8, ["videos"]),
                        createVNode(SectionBanners, { banners: mainBanners.value }, null, 8, ["banners"])
                      ])
                    ], 8, ["itemid"]),
                    showRight.value ? (openBlock(), createBlock("aside", {
                      key: 1,
                      class: ["shrink-0 transition-all duration-300 overflow-hidden", rightCollapsed.value ? "lg:w-6" : "lg:w-72"]
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
                "setting-key": "publicBlogRubricsProcessingMode",
                mode: __props.publicBlogRubricsProcessingMode,
                "use-server-processing": __props.useServerProcessing,
                total: __props.rubricsCount
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
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Pages/Public/Default/Blog/BlogRubrics/Index.vue");
  return _sfc_setup ? _sfc_setup(props, ctx) : void 0;
};
export {
  _sfc_main as default
};
