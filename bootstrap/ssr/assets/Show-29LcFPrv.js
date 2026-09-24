import { computed, ref, onMounted, watch, unref, withCtx, createVNode, createBlock, createCommentVNode, toDisplayString, openBlock, useSSRContext } from "vue";
import { ssrRenderComponent, ssrInterpolate, ssrRenderAttr, ssrRenderClass } from "vue/server-renderer";
import { usePage, Head, Link, router } from "@inertiajs/vue3";
import { useI18n } from "vue-i18n";
import { u as useSmoothScrollTo } from "./useSmoothScrollTo-g9G3kyDv.js";
import { _ as _sfc_main$1 } from "./DefaultLayout-6zxGxPtK.js";
import { _ as _sfc_main$2, a as _sfc_main$d, P as Progress } from "./Progress-CyfOKcqP.js";
import { _ as _sfc_main$3, a as _sfc_main$c } from "./RightSidebar-OMLtHB67.js";
import { _ as _sfc_main$4 } from "./EntityPageToolbar-DT32FtSd.js";
import { _ as _sfc_main$7, a as _sfc_main$8, b as _sfc_main$e } from "./PublicAdminBottomPanel-BhLEQMoJ.js";
import { _ as _sfc_main$5 } from "./ArticleGrid-BwxxSomM.js";
import { _ as _sfc_main$6 } from "./ArticleRows-DOH_NLgs.js";
import { _ as _sfc_main$9, a as _sfc_main$a } from "./RubricRows-CxJVX4pL.js";
import { _ as _sfc_main$b, S as SectionBanners } from "./SectionBanners-Cdgmr0Bw.js";
import { I as ImageGalleryMain } from "./ImageGalleryMain-mIfXDUWm.js";
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
import "./EntityStats-0c7h3PEr.js";
import "./LikeButtonEntity-ZC4HMEAO.js";
const DEFAULT_SORT = "sortAsc";
const VIEW_KEY = "public_blog_rubric_articles_view";
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
    rubric: {
      type: Object,
      default: () => ({})
    },
    publicBlogArticlesProcessingMode: {
      type: String,
      default: "server"
    },
    useServerProcessing: {
      type: Boolean,
      default: false
    },
    articles: {
      type: [Array, Object],
      default: () => []
    },
    articlesCount: {
      type: Number,
      default: 0
    },
    articlesFound: {
      type: Number,
      default: 0
    },
    filters: {
      type: Object,
      default: () => ({})
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
    const rubric = computed(
      () => props.rubric ?? {}
    );
    const rubricTranslation = computed(
      () => {
        var _a2;
        return ((_a2 = rubric.value) == null ? void 0 : _a2.translation) ?? {};
      }
    );
    const rubricTitle = computed(
      () => {
        var _a2;
        return ((_a2 = rubricTranslation.value) == null ? void 0 : _a2.title) || "";
      }
    );
    const rubricShort = computed(
      () => {
        var _a2;
        return ((_a2 = rubricTranslation.value) == null ? void 0 : _a2.short) || "";
      }
    );
    const rubricDescription = computed(
      () => {
        var _a2;
        return ((_a2 = rubricTranslation.value) == null ? void 0 : _a2.description) || rubricShort.value || "";
      }
    );
    const rubricLocale = computed(
      () => {
        var _a2;
        return ((_a2 = rubricTranslation.value) == null ? void 0 : _a2.locale) || props.locale || "ru";
      }
    );
    const childRubrics = computed(
      () => {
        var _a2;
        return normalizeList(
          (_a2 = rubric.value) == null ? void 0 : _a2.children
        );
      }
    );
    const rubricImages = computed(
      () => {
        var _a2;
        return normalizeList(
          (_a2 = rubric.value) == null ? void 0 : _a2.images
        );
      }
    );
    const hasRubricImages = computed(
      () => rubricImages.value.length > 0
    );
    const hasSvgIcon = computed(() => {
      var _a2;
      if (!((_a2 = rubric.value) == null ? void 0 : _a2.icon)) {
        return false;
      }
      return /^\s*<svg[\s\S]*<\/svg>\s*$/i.test(
        String(rubric.value.icon)
      );
    });
    const seoTitle = computed(
      () => {
        var _a2;
        return ((_a2 = rubricTranslation.value) == null ? void 0 : _a2.meta_title) || rubricTitle.value || t("rubrics");
      }
    );
    const seoKeywords = computed(
      () => {
        var _a2;
        return ((_a2 = rubricTranslation.value) == null ? void 0 : _a2.meta_keywords) || "";
      }
    );
    const seoDescription = computed(
      () => {
        var _a2;
        return ((_a2 = rubricTranslation.value) == null ? void 0 : _a2.meta_desc) || rubricShort.value || rubricDescription.value || "";
      }
    );
    const canonicalUrl = computed(() => {
      var _a2;
      if (!((_a2 = rubric.value) == null ? void 0 : _a2.url)) {
        return "";
      }
      return String(
        route("public.blogRubrics.show", {
          url: rubric.value.url
        })
      );
    });
    const ogLocale = computed(
      () => rubricLocale.value === "ru" ? "ru_RU" : rubricLocale.value
    );
    const dcSubject = computed(
      () => seoKeywords.value || rubricTitle.value
    );
    const getImageUrl = (image) => (image == null ? void 0 : image.webp_url) || (image == null ? void 0 : image.image_url) || (image == null ? void 0 : image.thumb_url) || (image == null ? void 0 : image.url) || "";
    const seoImage = computed(() => {
      for (const image of rubricImages.value) {
        const url = getImageUrl(image);
        if (url) return url;
      }
      return "";
    });
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
    const mainVideos = computed(
      () => normalizeList(props.mainVideos)
    );
    const mainBanners = computed(
      () => normalizeList(props.mainBanners)
    );
    const articlesData = computed(
      () => normalizeList(props.articles)
    );
    const articlesCount = computed(
      () => safeNumber(props.articlesCount)
    );
    const hasArticles = computed(
      () => articlesCount.value > 0
    );
    const getArticleTitle = (article) => {
      var _a2;
      return ((_a2 = article == null ? void 0 : article.translation) == null ? void 0 : _a2.title) || "";
    };
    const getArticleSubtitle = (article) => {
      var _a2;
      return ((_a2 = article == null ? void 0 : article.translation) == null ? void 0 : _a2.subtitle) || "";
    };
    const getArticleShort = (article) => {
      var _a2;
      return ((_a2 = article == null ? void 0 : article.translation) == null ? void 0 : _a2.short) || "";
    };
    const getArticleDescription = (article) => {
      var _a2;
      return ((_a2 = article == null ? void 0 : article.translation) == null ? void 0 : _a2.description) || "";
    };
    const getArticleUrl = (article) => (article == null ? void 0 : article.url) || "";
    const qArticles = ref(
      String(
        ((_a = props.filters) == null ? void 0 : _a.q_articles) ?? ""
      )
    );
    const sortArticles = ref(
      String(
        ((_b = props.filters) == null ? void 0 : _b.sort_articles) ?? DEFAULT_SORT
      )
    );
    const perPageArticles = computed(() => {
      var _a2;
      const value = Number(
        (_a2 = props.filters) == null ? void 0 : _a2.per_page_articles
      );
      return Number.isFinite(value) && value > 0 ? value : 12;
    });
    const articleSortOptions = [
      { value: "sortAsc", label: `${t("sortNumber")} 0→9` },
      { value: "sortDesc", label: `${t("sortNumber")} 9→0` },
      { value: "idDesc", label: t("idDesc") },
      { value: "idAsc", label: t("idAsc") },
      { value: "titleAsc", label: `${t("title")} A→Z` },
      { value: "titleDesc", label: `${t("title")} Z→A` },
      { value: "viewsDesc", label: `${t("views")} 9→0` },
      { value: "viewsAsc", label: `${t("views")} 0→9` },
      { value: "likesDesc", label: `${t("likes")} 9→0` },
      { value: "likesAsc", label: `${t("likes")} 0→9` },
      { value: "dateDesc", label: `${t("publishedAt")} ↓` },
      { value: "dateAsc", label: `${t("publishedAt")} ↑` }
    ];
    const viewMode = ref("grid");
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
    const filteredArticles = computed(() => {
      if (props.useServerProcessing) {
        return articlesData.value;
      }
      const query = normalizeText(qArticles.value);
      if (!query) {
        return articlesData.value;
      }
      return articlesData.value.filter(
        (article) => {
          var _a2;
          const ownerName = ((_a2 = article == null ? void 0 : article.owner) == null ? void 0 : _a2.name) || "";
          const rubricTitles = Array.isArray(article == null ? void 0 : article.rubrics) ? article.rubrics.map(
            (item) => {
              var _a3;
              return ((_a3 = item == null ? void 0 : item.translation) == null ? void 0 : _a3.title) || "";
            }
          ).join(" ") : "";
          return [
            article == null ? void 0 : article.id,
            getArticleTitle(article),
            getArticleSubtitle(article),
            getArticleShort(article),
            getArticleDescription(article),
            getArticleUrl(article),
            ownerName,
            rubricTitles
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
    const sortedArticles = computed(() => {
      if (props.useServerProcessing) {
        return filteredArticles.value;
      }
      const list = [
        ...filteredArticles.value
      ];
      switch (sortArticles.value) {
        case "sortAsc":
          list.sort(
            (a, b) => compareNumber(a == null ? void 0 : a.sort, b == null ? void 0 : b.sort) || compareNumber(a == null ? void 0 : a.id, b == null ? void 0 : b.id)
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
              getArticleTitle(a),
              getArticleTitle(b)
            )
          );
          break;
        case "titleDesc":
          list.sort(
            (a, b) => compareText(
              getArticleTitle(b),
              getArticleTitle(a)
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
        case "likesAsc":
          list.sort(
            (a, b) => compareNumber(
              a == null ? void 0 : a.likes_count,
              b == null ? void 0 : b.likes_count
            )
          );
          break;
        case "likesDesc":
          list.sort(
            (a, b) => compareNumber(
              b == null ? void 0 : b.likes_count,
              a == null ? void 0 : a.likes_count
            )
          );
          break;
        case "dateAsc":
          list.sort(
            (a, b) => safeDate(a == null ? void 0 : a.published_at) - safeDate(b == null ? void 0 : b.published_at)
          );
          break;
        case "dateDesc":
          list.sort(
            (a, b) => safeDate(b == null ? void 0 : b.published_at) - safeDate(a == null ? void 0 : a.published_at)
          );
          break;
      }
      return list;
    });
    const frontendCurrentPage = ref(1);
    const {
      targetRef: articlesScrollTarget,
      scrollToTarget: scrollToArticles
    } = useSmoothScrollTo({
      offset: 80,
      duration: 1200
    });
    watch(
      [
        qArticles,
        sortArticles
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
          scrollToArticles();
        }
      }
    );
    const effectiveArticlesFound = computed(
      () => props.useServerProcessing ? safeNumber(
        props.articlesFound
      ) : sortedArticles.value.length
    );
    const frontendPaginatedArticles = computed(() => {
      if (props.useServerProcessing) {
        return articlesData.value;
      }
      const start = (frontendCurrentPage.value - 1) * perPageArticles.value;
      return sortedArticles.value.slice(
        start,
        start + perPageArticles.value
      );
    });
    const displayedArticles = computed(
      () => props.useServerProcessing ? articlesData.value : frontendPaginatedArticles.value
    );
    const currentPage = computed(
      () => {
        var _a2, _b2, _c;
        return Number(
          ((_b2 = (_a2 = props.articles) == null ? void 0 : _a2.meta) == null ? void 0 : _b2.current_page) ?? ((_c = props.articles) == null ? void 0 : _c.current_page) ?? 1
        ) || 1;
      }
    );
    const lastPage = computed(
      () => {
        var _a2, _b2, _c;
        return Number(
          ((_b2 = (_a2 = props.articles) == null ? void 0 : _a2.meta) == null ? void 0 : _b2.last_page) ?? ((_c = props.articles) == null ? void 0 : _c.last_page) ?? 1
        ) || 1;
      }
    );
    const reloadArticles = (pageNumber = 1) => {
      if (!props.useServerProcessing || !canonicalUrl.value) {
        return;
      }
      router.get(
        canonicalUrl.value,
        {
          q_articles: qArticles.value || void 0,
          sort_articles: sortArticles.value || void 0,
          page_articles: pageNumber
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
      reloadArticles(target);
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
    const applyArticleFilters = () => {
      if (props.useServerProcessing) {
        reloadArticles(1);
      } else {
        frontendCurrentPage.value = 1;
      }
    };
    const resetArticleFilters = () => {
      if (props.useServerProcessing) {
        reloadArticles(1);
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
            _push2(`<meta name="robots" content="index, follow, max-image-preview:large"${_scopeId}>`);
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
            _push2(`<meta property="og:locale"${ssrRenderAttr("content", ogLocale.value)}${_scopeId}>`);
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
            _push2(`<meta name="DC.language"${ssrRenderAttr("content", rubricLocale.value)}${_scopeId}>`);
            if (canonicalUrl.value) {
              _push2(`<meta name="DC.identifier"${ssrRenderAttr("content", canonicalUrl.value)}${_scopeId}>`);
            } else {
              _push2(`<!---->`);
            }
            _push2(`<meta name="DC.type" content="Collection"${_scopeId}><meta name="DC.format" content="text/html"${_scopeId}>`);
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
              seoImage.value ? (openBlock(), createBlock("meta", {
                key: 5,
                property: "og:image",
                content: seoImage.value
              }, null, 8, ["content"])) : createCommentVNode("", true),
              seoImage.value ? (openBlock(), createBlock("meta", {
                key: 6,
                property: "og:image:alt",
                content: seoTitle.value
              }, null, 8, ["content"])) : createCommentVNode("", true),
              seoImage.value ? (openBlock(), createBlock("meta", {
                key: 7,
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
                key: 8,
                name: "twitter:description",
                content: seoDescription.value
              }, null, 8, ["content"])) : createCommentVNode("", true),
              seoImage.value ? (openBlock(), createBlock("meta", {
                key: 9,
                name: "twitter:image",
                content: seoImage.value
              }, null, 8, ["content"])) : createCommentVNode("", true),
              createVNode("meta", {
                name: "DC.title",
                content: seoTitle.value
              }, null, 8, ["content"]),
              seoDescription.value ? (openBlock(), createBlock("meta", {
                key: 10,
                name: "DC.description",
                content: seoDescription.value
              }, null, 8, ["content"])) : createCommentVNode("", true),
              dcSubject.value ? (openBlock(), createBlock("meta", {
                key: 11,
                name: "DC.subject",
                content: dcSubject.value
              }, null, 8, ["content"])) : createCommentVNode("", true),
              createVNode("meta", {
                name: "DC.language",
                content: rubricLocale.value
              }, null, 8, ["content"]),
              canonicalUrl.value ? (openBlock(), createBlock("meta", {
                key: 12,
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
            if (canonicalUrl.value) {
              _push2(`<meta itemprop="url"${ssrRenderAttr("content", canonicalUrl.value)}${_scopeId}>`);
            } else {
              _push2(`<!---->`);
            }
            _push2(`<meta itemprop="inLanguage"${ssrRenderAttr("content", rubricLocale.value)}${_scopeId}><div itemprop="interactionStatistic" itemscope itemtype="https://schema.org/InteractionCounter" class="contents"${_scopeId}><link itemprop="interactionType" href="https://schema.org/ViewAction"${_scopeId}><meta itemprop="userInteractionCount"${ssrRenderAttr("content", rubric.value.views || 0)}${_scopeId}></div><div class="mx-auto max-w-6xl"${_scopeId}><nav class="text-sm mb-3" aria-label="Breadcrumb" itemscope itemtype="https://schema.org/BreadcrumbList"${_scopeId}><ol class="flex flex-wrap items-center font-semibold"${_scopeId}><li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem" class="flex items-center"${_scopeId}>`);
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
              href: _ctx.route("public.blogRubrics.index"),
              class: "breadcrumb-link hover:underline"
            }, {
              default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                if (_push3) {
                  _push3(`<span itemprop="name"${_scopeId2}>${ssrInterpolate(unref(t)("rubrics"))}</span>`);
                } else {
                  return [
                    createVNode("span", { itemprop: "name" }, toDisplayString(unref(t)("rubrics")), 1)
                  ];
                }
              }),
              _: 1
            }, _parent2, _scopeId));
            _push2(`<meta itemprop="position" content="2"${_scopeId}></li><li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem" class="flex items-center" aria-current="page"${_scopeId}><span class="mx-2 breadcrumbs"${_scopeId}>/</span><span itemprop="name" class="breadcrumbs"${_scopeId}>${ssrInterpolate(rubricTitle.value)}</span>`);
            if (canonicalUrl.value) {
              _push2(`<meta itemprop="item"${ssrRenderAttr("content", canonicalUrl.value)}${_scopeId}>`);
            } else {
              _push2(`<!---->`);
            }
            _push2(`<meta itemprop="position" content="3"${_scopeId}></li></ol></nav>`);
            if (hasRubricImages.value) {
              _push2(`<div class="flex items-center justify-center"${_scopeId}><div class="w-full"${_scopeId}>`);
              _push2(ssrRenderComponent(ImageGalleryMain, {
                images: rubricImages.value,
                alt: rubricTitle.value,
                "rounded-class": "rounded-lg",
                "shadow-class": "shadow-lg shadow-gray-400 dark:shadow-gray-700",
                "img-class": "w-full h-full object-cover"
              }, null, _parent2, _scopeId));
              _push2(`</div></div>`);
            } else {
              _push2(`<!---->`);
            }
            _push2(`<div class="flex items-center justify-between gap-1"${_scopeId}><div${ssrRenderAttr("title", unref(t)("articles"))} class="flex items-center justify-center gap-1"${_scopeId}><svg class="h-4 w-4 text-slate-600/85 dark:text-slate-200/85" viewBox="0 0 384 512" fill="currentColor"${_scopeId}><path d="M288 248v28c0 6.6-5.4 12-12 12H108c-6.6 0-12-5.4-12-12v-28c0-6.6 5.4-12 12-12h168c6.6 0 12 5.4 12 12zm-12 72H108c-6.6 0-12 5.4-12 12v28c0 6.6 5.4 12 12 12h168c6.6 0 12-5.4 12-12v-28c0-6.6-5.4-12-12-12h168c6.6 0 12 5.4 12 12z"${_scopeId}></path></svg><span class="text-sm text-gray-500"${_scopeId}>${ssrInterpolate(articlesCount.value)}</span></div><div class="my-3 flex flex-wrap items-center justify-center gap-3 title"${_scopeId}>`);
            if (hasSvgIcon.value) {
              _push2(`<span class="flex"${_scopeId}>${rubric.value.icon ?? ""}</span>`);
            } else {
              _push2(`<!---->`);
            }
            _push2(`<h1 itemprop="headline" class="text-2xl font-bold"${_scopeId}>${ssrInterpolate(rubricTitle.value)}</h1></div><div${ssrRenderAttr("title", unref(t)("views"))} class="flex items-center justify-center gap-1"${_scopeId}><svg class="h-4 w-4 text-slate-600/85 dark:text-slate-200/85" viewBox="0 0 576 512" fill="currentColor"${_scopeId}><path d="M569.354 231.631C512.97 135.949 407.81 72 288 72 168.14 72 63.004 135.994 6.646 231.631a47.999 47.999 0 0 0 0 48.739C63.031 376.051 168.19 440 288 440c119.86 0 224.996-63.994 281.354-159.631a47.997 47.997 0 0 0 0-48.738z"${_scopeId}></path></svg><span class="text-sm text-gray-500"${_scopeId}>${ssrInterpolate(rubric.value.views || 0)}</span></div></div>`);
            if (rubricDescription.value) {
              _push2(`<div itemprop="abstract" class="mt-1 mb-3 text-sm subtitle text-center"${_scopeId}>${ssrInterpolate(rubricDescription.value)}</div>`);
            } else {
              _push2(`<!---->`);
            }
            if (hasArticles.value) {
              _push2(ssrRenderComponent(_sfc_main$4, {
                modelValue: qArticles.value,
                "onUpdate:modelValue": ($event) => qArticles.value = $event,
                "view-mode": viewMode.value,
                "onUpdate:viewMode": ($event) => viewMode.value = $event,
                "sort-value": sortArticles.value,
                "onUpdate:sortValue": ($event) => sortArticles.value = $event,
                found: effectiveArticlesFound.value,
                "sort-options": articleSortOptions,
                "default-sort": DEFAULT_SORT,
                "found-label": unref(t)("articles"),
                "search-placeholder": unref(t)("searchByName"),
                onSubmit: applyArticleFilters,
                onReset: resetArticleFilters
              }, null, _parent2, _scopeId));
            } else {
              _push2(`<!---->`);
            }
            _push2(`<div${_scopeId}></div>`);
            if (hasArticles.value && displayedArticles.value.length === 0) {
              _push2(`<div class="mt-6 text-center text-slate-700 dark:text-slate-300"${_scopeId}>${ssrInterpolate(unref(t)("noData"))}</div>`);
            } else {
              _push2(`<!---->`);
            }
            if (displayedArticles.value.length) {
              _push2(`<div${_scopeId}>`);
              if (viewMode.value === "grid") {
                _push2(ssrRenderComponent(_sfc_main$5, {
                  articles: displayedArticles.value,
                  cols: rubricGridCols.value
                }, null, _parent2, _scopeId));
              } else {
                _push2(ssrRenderComponent(_sfc_main$6, { articles: displayedArticles.value }, null, _parent2, _scopeId));
              }
              _push2(`</div>`);
            } else {
              _push2(`<!---->`);
            }
            if (__props.useServerProcessing && hasArticles.value && lastPage.value > 1) {
              _push2(ssrRenderComponent(_sfc_main$7, {
                "current-page": currentPage.value,
                "last-page": lastPage.value,
                found: __props.articlesFound,
                onPrev: goPrev,
                onNext: goNext,
                onGo: goToPage
              }, null, _parent2, _scopeId));
            } else {
              _push2(`<!---->`);
            }
            if (!__props.useServerProcessing && effectiveArticlesFound.value > perPageArticles.value) {
              _push2(ssrRenderComponent(_sfc_main$8, {
                currentPage: frontendCurrentPage.value,
                "onUpdate:currentPage": ($event) => frontendCurrentPage.value = $event,
                "items-per-page": perPageArticles.value,
                "total-items": effectiveArticlesFound.value
              }, null, _parent2, _scopeId));
            } else {
              _push2(`<!---->`);
            }
            if (childRubrics.value.length) {
              _push2(`<div${_scopeId}><div class="mt-4 flex flex-wrap items-center justify-center gap-3 text-slate-700/85 dark:text-slate-300/85"${_scopeId}><svg class="h-8 w-8 opacity-70" fill="currentColor" viewBox="0 0 640 512"${_scopeId}><path d="M622.34 153.2L343.4 67.5c-15.2-4.67-31.6-4.67-46.79 0L17.66 153.2c-23.54 7.23-23.54 38.36 0 45.59l278.95 85.7c20.35 6.25 37.73 2.78 46.79 0l278.95-85.7c23.55-7.24 23.55-38.36 0-45.6zM352.79 315.09c-28.53 8.76-52.84 3.92-65.59 0l-145.02-44.55L128 384c0 35.35 85.96 64 192 64s192-28.65 192-64l-14.18-113.47-145.03 44.56z"${_scopeId}></path></svg><h2 class="text-xl font-semibold"${_scopeId}>${ssrInterpolate(unref(t)("subheadings"))}</h2></div><div class="mt-6"${_scopeId}>`);
              if (viewMode.value === "grid") {
                _push2(ssrRenderComponent(_sfc_main$9, {
                  rubrics: childRubrics.value,
                  cols: rubricGridCols.value
                }, null, _parent2, _scopeId));
              } else {
                _push2(ssrRenderComponent(_sfc_main$a, { rubrics: childRubrics.value }, null, _parent2, _scopeId));
              }
              _push2(`</div></div>`);
            } else {
              _push2(`<!---->`);
            }
            _push2(ssrRenderComponent(_sfc_main$b, { videos: mainVideos.value }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(SectionBanners, { banners: mainBanners.value }, null, _parent2, _scopeId));
            _push2(`</div></article>`);
            if (showRight.value) {
              _push2(`<aside class="${ssrRenderClass([rightCollapsed.value ? "lg:w-6" : "lg:w-72", "shrink-0 transition-all duration-300 overflow-hidden"])}"${_scopeId}>`);
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
                "setting-key": "publicBlogArticlesProcessingMode",
                mode: __props.publicBlogArticlesProcessingMode,
                "use-server-processing": __props.useServerProcessing,
                total: articlesCount.value
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
                      canonicalUrl.value ? (openBlock(), createBlock("meta", {
                        key: 2,
                        itemprop: "url",
                        content: canonicalUrl.value
                      }, null, 8, ["content"])) : createCommentVNode("", true),
                      createVNode("meta", {
                        itemprop: "inLanguage",
                        content: rubricLocale.value
                      }, null, 8, ["content"]),
                      createVNode("div", {
                        itemprop: "interactionStatistic",
                        itemscope: "",
                        itemtype: "https://schema.org/InteractionCounter",
                        class: "contents"
                      }, [
                        createVNode("link", {
                          itemprop: "interactionType",
                          href: "https://schema.org/ViewAction"
                        }),
                        createVNode("meta", {
                          itemprop: "userInteractionCount",
                          content: rubric.value.views || 0
                        }, null, 8, ["content"])
                      ]),
                      createVNode("div", { class: "mx-auto max-w-6xl" }, [
                        createVNode("nav", {
                          class: "text-sm mb-3",
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
                                href: _ctx.route("public.blogRubrics.index"),
                                class: "breadcrumb-link hover:underline"
                              }, {
                                default: withCtx(() => [
                                  createVNode("span", { itemprop: "name" }, toDisplayString(unref(t)("rubrics")), 1)
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
                              }, toDisplayString(rubricTitle.value), 1),
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
                        hasRubricImages.value ? (openBlock(), createBlock("div", {
                          key: 0,
                          class: "flex items-center justify-center"
                        }, [
                          createVNode("div", { class: "w-full" }, [
                            createVNode(ImageGalleryMain, {
                              images: rubricImages.value,
                              alt: rubricTitle.value,
                              "rounded-class": "rounded-lg",
                              "shadow-class": "shadow-lg shadow-gray-400 dark:shadow-gray-700",
                              "img-class": "w-full h-full object-cover"
                            }, null, 8, ["images", "alt"])
                          ])
                        ])) : createCommentVNode("", true),
                        createVNode("div", { class: "flex items-center justify-between gap-1" }, [
                          createVNode("div", {
                            title: unref(t)("articles"),
                            class: "flex items-center justify-center gap-1"
                          }, [
                            (openBlock(), createBlock("svg", {
                              class: "h-4 w-4 text-slate-600/85 dark:text-slate-200/85",
                              viewBox: "0 0 384 512",
                              fill: "currentColor"
                            }, [
                              createVNode("path", { d: "M288 248v28c0 6.6-5.4 12-12 12H108c-6.6 0-12-5.4-12-12v-28c0-6.6 5.4-12 12-12h168c6.6 0 12 5.4 12 12zm-12 72H108c-6.6 0-12 5.4-12 12v28c0 6.6 5.4 12 12 12h168c6.6 0 12-5.4 12-12v-28c0-6.6-5.4-12-12-12h168c6.6 0 12 5.4 12 12z" })
                            ])),
                            createVNode("span", { class: "text-sm text-gray-500" }, toDisplayString(articlesCount.value), 1)
                          ], 8, ["title"]),
                          createVNode("div", { class: "my-3 flex flex-wrap items-center justify-center gap-3 title" }, [
                            hasSvgIcon.value ? (openBlock(), createBlock("span", {
                              key: 0,
                              class: "flex",
                              innerHTML: rubric.value.icon
                            }, null, 8, ["innerHTML"])) : createCommentVNode("", true),
                            createVNode("h1", {
                              itemprop: "headline",
                              class: "text-2xl font-bold"
                            }, toDisplayString(rubricTitle.value), 1)
                          ]),
                          createVNode("div", {
                            title: unref(t)("views"),
                            class: "flex items-center justify-center gap-1"
                          }, [
                            (openBlock(), createBlock("svg", {
                              class: "h-4 w-4 text-slate-600/85 dark:text-slate-200/85",
                              viewBox: "0 0 576 512",
                              fill: "currentColor"
                            }, [
                              createVNode("path", { d: "M569.354 231.631C512.97 135.949 407.81 72 288 72 168.14 72 63.004 135.994 6.646 231.631a47.999 47.999 0 0 0 0 48.739C63.031 376.051 168.19 440 288 440c119.86 0 224.996-63.994 281.354-159.631a47.997 47.997 0 0 0 0-48.738z" })
                            ])),
                            createVNode("span", { class: "text-sm text-gray-500" }, toDisplayString(rubric.value.views || 0), 1)
                          ], 8, ["title"])
                        ]),
                        rubricDescription.value ? (openBlock(), createBlock("div", {
                          key: 1,
                          itemprop: "abstract",
                          class: "mt-1 mb-3 text-sm subtitle text-center"
                        }, toDisplayString(rubricDescription.value), 1)) : createCommentVNode("", true),
                        hasArticles.value ? (openBlock(), createBlock(_sfc_main$4, {
                          key: 2,
                          modelValue: qArticles.value,
                          "onUpdate:modelValue": ($event) => qArticles.value = $event,
                          "view-mode": viewMode.value,
                          "onUpdate:viewMode": ($event) => viewMode.value = $event,
                          "sort-value": sortArticles.value,
                          "onUpdate:sortValue": ($event) => sortArticles.value = $event,
                          found: effectiveArticlesFound.value,
                          "sort-options": articleSortOptions,
                          "default-sort": DEFAULT_SORT,
                          "found-label": unref(t)("articles"),
                          "search-placeholder": unref(t)("searchByName"),
                          onSubmit: applyArticleFilters,
                          onReset: resetArticleFilters
                        }, null, 8, ["modelValue", "onUpdate:modelValue", "view-mode", "onUpdate:viewMode", "sort-value", "onUpdate:sortValue", "found", "found-label", "search-placeholder"])) : createCommentVNode("", true),
                        createVNode("div", {
                          ref_key: "articlesScrollTarget",
                          ref: articlesScrollTarget
                        }, null, 512),
                        hasArticles.value && displayedArticles.value.length === 0 ? (openBlock(), createBlock("div", {
                          key: 3,
                          class: "mt-6 text-center text-slate-700 dark:text-slate-300"
                        }, toDisplayString(unref(t)("noData")), 1)) : createCommentVNode("", true),
                        displayedArticles.value.length ? (openBlock(), createBlock("div", { key: 4 }, [
                          viewMode.value === "grid" ? (openBlock(), createBlock(_sfc_main$5, {
                            key: 0,
                            articles: displayedArticles.value,
                            cols: rubricGridCols.value
                          }, null, 8, ["articles", "cols"])) : (openBlock(), createBlock(_sfc_main$6, {
                            key: 1,
                            articles: displayedArticles.value
                          }, null, 8, ["articles"]))
                        ])) : createCommentVNode("", true),
                        __props.useServerProcessing && hasArticles.value && lastPage.value > 1 ? (openBlock(), createBlock(_sfc_main$7, {
                          key: 5,
                          "current-page": currentPage.value,
                          "last-page": lastPage.value,
                          found: __props.articlesFound,
                          onPrev: goPrev,
                          onNext: goNext,
                          onGo: goToPage
                        }, null, 8, ["current-page", "last-page", "found"])) : createCommentVNode("", true),
                        !__props.useServerProcessing && effectiveArticlesFound.value > perPageArticles.value ? (openBlock(), createBlock(_sfc_main$8, {
                          key: 6,
                          currentPage: frontendCurrentPage.value,
                          "onUpdate:currentPage": ($event) => frontendCurrentPage.value = $event,
                          "items-per-page": perPageArticles.value,
                          "total-items": effectiveArticlesFound.value
                        }, null, 8, ["currentPage", "onUpdate:currentPage", "items-per-page", "total-items"])) : createCommentVNode("", true),
                        childRubrics.value.length ? (openBlock(), createBlock("div", { key: 7 }, [
                          createVNode("div", { class: "mt-4 flex flex-wrap items-center justify-center gap-3 text-slate-700/85 dark:text-slate-300/85" }, [
                            (openBlock(), createBlock("svg", {
                              class: "h-8 w-8 opacity-70",
                              fill: "currentColor",
                              viewBox: "0 0 640 512"
                            }, [
                              createVNode("path", { d: "M622.34 153.2L343.4 67.5c-15.2-4.67-31.6-4.67-46.79 0L17.66 153.2c-23.54 7.23-23.54 38.36 0 45.59l278.95 85.7c20.35 6.25 37.73 2.78 46.79 0l278.95-85.7c23.55-7.24 23.55-38.36 0-45.6zM352.79 315.09c-28.53 8.76-52.84 3.92-65.59 0l-145.02-44.55L128 384c0 35.35 85.96 64 192 64s192-28.65 192-64l-14.18-113.47-145.03 44.56z" })
                            ])),
                            createVNode("h2", { class: "text-xl font-semibold" }, toDisplayString(unref(t)("subheadings")), 1)
                          ]),
                          createVNode("div", { class: "mt-6" }, [
                            viewMode.value === "grid" ? (openBlock(), createBlock(_sfc_main$9, {
                              key: 0,
                              rubrics: childRubrics.value,
                              cols: rubricGridCols.value
                            }, null, 8, ["rubrics", "cols"])) : (openBlock(), createBlock(_sfc_main$a, {
                              key: 1,
                              rubrics: childRubrics.value
                            }, null, 8, ["rubrics"]))
                          ])
                        ])) : createCommentVNode("", true),
                        createVNode(_sfc_main$b, { videos: mainVideos.value }, null, 8, ["videos"]),
                        createVNode(SectionBanners, { banners: mainBanners.value }, null, 8, ["banners"])
                      ])
                    ], 8, ["itemid"]),
                    showRight.value ? (openBlock(), createBlock("aside", {
                      key: 1,
                      class: ["shrink-0 transition-all duration-300 overflow-hidden", rightCollapsed.value ? "lg:w-6" : "lg:w-72"]
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
                "setting-key": "publicBlogArticlesProcessingMode",
                mode: __props.publicBlogArticlesProcessingMode,
                "use-server-processing": __props.useServerProcessing,
                total: articlesCount.value
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
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Pages/Public/Default/Blog/BlogRubrics/Show.vue");
  return _sfc_setup ? _sfc_setup(props, ctx) : void 0;
};
export {
  _sfc_main as default
};
