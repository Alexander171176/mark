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
import { _ as _sfc_main$5 } from "./ArticleGrid-BwxxSomM.js";
import { _ as _sfc_main$6 } from "./ArticleRows-DOH_NLgs.js";
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
import "./EntityStats-0c7h3PEr.js";
import "./LikeButtonEntity-ZC4HMEAO.js";
const DEFAULT_SORT = "sortAsc";
const VIEW_KEY = "public_blog_articles_view";
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
    tag: {
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
    const tag = computed(
      () => props.tag ?? {}
    );
    const tagTranslation = computed(
      () => {
        var _a2;
        return ((_a2 = tag.value) == null ? void 0 : _a2.translation) ?? {};
      }
    );
    const tagName = computed(
      () => {
        var _a2;
        return ((_a2 = tagTranslation.value) == null ? void 0 : _a2.name) || "";
      }
    );
    const tagSubtitle = computed(
      () => {
        var _a2;
        return ((_a2 = tagTranslation.value) == null ? void 0 : _a2.subtitle) || "";
      }
    );
    const tagShort = computed(
      () => {
        var _a2;
        return ((_a2 = tagTranslation.value) == null ? void 0 : _a2.short) || "";
      }
    );
    const tagDescription = computed(
      () => {
        var _a2;
        return ((_a2 = tagTranslation.value) == null ? void 0 : _a2.description) || tagShort.value || "";
      }
    );
    const tagLocale = computed(
      () => {
        var _a2;
        return ((_a2 = tagTranslation.value) == null ? void 0 : _a2.locale) || props.locale || "ru";
      }
    );
    const hasSvgIcon = computed(() => {
      var _a2;
      if (!((_a2 = tag.value) == null ? void 0 : _a2.icon)) {
        return false;
      }
      return /^\s*<svg[\s\S]*<\/svg>\s*$/i.test(
        String(tag.value.icon)
      );
    });
    const seoTitle = computed(
      () => {
        var _a2;
        return ((_a2 = tagTranslation.value) == null ? void 0 : _a2.meta_title) || (tagName.value ? `#${tagName.value}` : t("tag"));
      }
    );
    const seoDescription = computed(
      () => {
        var _a2;
        return ((_a2 = tagTranslation.value) == null ? void 0 : _a2.meta_desc) || tagShort.value || tagDescription.value || "";
      }
    );
    const seoKeywords = computed(
      () => {
        var _a2;
        return ((_a2 = tagTranslation.value) == null ? void 0 : _a2.meta_keywords) || "";
      }
    );
    const canonicalUrl = computed(() => {
      var _a2;
      if (!((_a2 = tag.value) == null ? void 0 : _a2.slug)) {
        return "";
      }
      return String(
        route("public.blogTags.show", {
          slug: tag.value.slug
        })
      );
    });
    const ogLocale = computed(
      () => tagLocale.value === "ru" ? "ru_RU" : tagLocale.value
    );
    const dcSubject = computed(
      () => seoKeywords.value || tagName.value
    );
    const seoImage = computed(() => "");
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
    const getArticleSlug = (article) => (article == null ? void 0 : article.url) || (article == null ? void 0 : article.slug) || "";
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
            (rubric) => {
              var _a3;
              return ((_a3 = rubric == null ? void 0 : rubric.translation) == null ? void 0 : _a3.title) || "";
            }
          ).join(" ") : "";
          return [
            article == null ? void 0 : article.id,
            getArticleTitle(article),
            getArticleSubtitle(article),
            getArticleShort(article),
            getArticleDescription(article),
            getArticleSlug(article),
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
            (a, b) => compareNumber(
              a == null ? void 0 : a.sort,
              b == null ? void 0 : b.sort
            ) || compareNumber(
              b == null ? void 0 : b.id,
              a == null ? void 0 : a.id
            )
          );
          break;
        case "sortDesc":
          list.sort(
            (a, b) => compareNumber(
              b == null ? void 0 : b.sort,
              a == null ? void 0 : a.sort
            ) || compareNumber(
              b == null ? void 0 : b.id,
              a == null ? void 0 : a.id
            )
          );
          break;
        case "idAsc":
          list.sort(
            (a, b) => compareNumber(
              a == null ? void 0 : a.id,
              b == null ? void 0 : b.id
            )
          );
          break;
        case "idDesc":
          list.sort(
            (a, b) => compareNumber(
              b == null ? void 0 : b.id,
              a == null ? void 0 : a.id
            )
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
        case "commentsAsc":
          list.sort(
            (a, b) => compareNumber(
              a == null ? void 0 : a.comments_count,
              b == null ? void 0 : b.comments_count
            )
          );
          break;
        case "commentsDesc":
          list.sort(
            (a, b) => compareNumber(
              b == null ? void 0 : b.comments_count,
              a == null ? void 0 : a.comments_count
            )
          );
          break;
        case "rubricsAsc":
          list.sort(
            (a, b) => compareNumber(
              a == null ? void 0 : a.rubrics_count,
              b == null ? void 0 : b.rubrics_count
            )
          );
          break;
        case "rubricsDesc":
          list.sort(
            (a, b) => compareNumber(
              b == null ? void 0 : b.rubrics_count,
              a == null ? void 0 : a.rubrics_count
            )
          );
          break;
        case "imagesAsc":
          list.sort(
            (a, b) => compareNumber(
              a == null ? void 0 : a.images_count,
              b == null ? void 0 : b.images_count
            )
          );
          break;
        case "imagesDesc":
          list.sort(
            (a, b) => compareNumber(
              b == null ? void 0 : b.images_count,
              a == null ? void 0 : a.images_count
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
        var _a2, _b2, _c2;
        return Number(
          ((_b2 = (_a2 = props.articles) == null ? void 0 : _a2.meta) == null ? void 0 : _b2.current_page) ?? ((_c2 = props.articles) == null ? void 0 : _c2.current_page) ?? 1
        ) || 1;
      }
    );
    const lastPage = computed(
      () => {
        var _a2, _b2, _c2;
        return Number(
          ((_b2 = (_a2 = props.articles) == null ? void 0 : _a2.meta) == null ? void 0 : _b2.last_page) ?? ((_c2 = props.articles) == null ? void 0 : _c2.last_page) ?? 1
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
    const articleGridCols = computed(() => {
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
            _push2(`<meta name="DC.language"${ssrRenderAttr("content", tagLocale.value)}${_scopeId}>`);
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
                content: tagLocale.value
              }, null, 8, ["content"]),
              canonicalUrl.value ? (openBlock(), createBlock("meta", {
                key: 10,
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
              _push2(`<aside class="${ssrRenderClass([
                leftCollapsed.value ? "lg:w-6" : "lg:w-72",
                "shrink-0 transition-all duration-300 overflow-hidden"
              ])}"${_scopeId}>`);
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
            _push2(`<meta itemprop="inLanguage"${ssrRenderAttr("content", tagLocale.value)}${_scopeId}><div class="mx-auto max-w-6xl"${_scopeId}><nav class="text-sm" aria-label="Breadcrumb" itemscope itemtype="https://schema.org/BreadcrumbList"${_scopeId}><ol class="flex flex-wrap items-center font-semibold"${_scopeId}><li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem" class="flex items-center"${_scopeId}>`);
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
              href: _ctx.route("public.blogArticles.index"),
              class: "breadcrumb-link hover:underline"
            }, {
              default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                if (_push3) {
                  _push3(`<span itemprop="name"${_scopeId2}>${ssrInterpolate(unref(t)("articles"))}</span>`);
                } else {
                  return [
                    createVNode("span", { itemprop: "name" }, toDisplayString(unref(t)("articles")), 1)
                  ];
                }
              }),
              _: 1
            }, _parent2, _scopeId));
            _push2(`<meta itemprop="position" content="2"${_scopeId}></li><li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem" class="flex items-center" aria-current="page"${_scopeId}><span class="mx-2 breadcrumbs"${_scopeId}> / </span><span itemprop="name" class="breadcrumbs"${_scopeId}>${ssrInterpolate(unref(t)("tag"))} #${ssrInterpolate(tagName.value)}</span>`);
            if (canonicalUrl.value) {
              _push2(`<meta itemprop="item"${ssrRenderAttr("content", canonicalUrl.value)}${_scopeId}>`);
            } else {
              _push2(`<!---->`);
            }
            _push2(`<meta itemprop="position" content="3"${_scopeId}></li></ol></nav><div class="flex items-center justify-between gap-1"${_scopeId}><div${ssrRenderAttr("title", unref(t)("articles"))} class="flex items-center justify-center gap-1"${_scopeId}><svg class="h-4 w-4 text-slate-600/85 dark:text-slate-200/85" viewBox="0 0 384 512" fill="currentColor"${_scopeId}><path d="M288 248v28c0 6.6-5.4 12-12 12H108c-6.6 0-12-5.4-12-12v-28c0-6.6 5.4-12 12-12h168c6.6 0 12 5.4 12 12zm-12 72H108c-6.6 0-12 5.4-12 12v28c0 6.6 5.4 12 12 12h168c6.6 0 12-5.4 12-12v-28c0-6.6-5.4-12-12-12zm108-188.1V464c0 26.5-21.5 48-48 48H48c-26.5 0-48-21.5-48-48V48C0 21.5 21.5 0 48 0h204.1C264.8 0 277 5.1 286 14.1L369.9 98c9 8.9 14.1 21.2 14.1 33.9zm-128-80V128h76.1L256 51.9zM336 464V176H232c-13.3 0-24-10.7-24-24V48H48v416h288z"${_scopeId}></path></svg><span class="text-center text-sm text-gray-500"${_scopeId}>${ssrInterpolate(articlesCount.value)}</span></div><div class="my-3 flex flex-wrap items-center justify-center gap-3 title"${_scopeId}>`);
            if (hasSvgIcon.value) {
              _push2(`<span class="flex"${_scopeId}>${tag.value.icon ?? ""}</span>`);
            } else {
              _push2(`<!---->`);
            }
            _push2(`<h1 itemprop="name" class="text-2xl font-bold"${_scopeId}> #${ssrInterpolate(tagName.value)}</h1></div><div${ssrRenderAttr("title", unref(t)("views"))} class="flex items-center justify-center gap-1"${_scopeId}><div itemprop="interactionStatistic" itemscope itemtype="https://schema.org/InteractionCounter" class="contents"${_scopeId}><link itemprop="interactionType" href="https://schema.org/ViewAction"${_scopeId}><meta itemprop="userInteractionCount"${ssrRenderAttr("content", tag.value.views || 0)}${_scopeId}></div><svg class="h-4 w-4 text-slate-600/85 dark:text-slate-200/85" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512" fill="currentColor"${_scopeId}><path d="M569.354 231.631C512.97 135.949 407.81 72 288 72 168.14 72 63.004 135.994 6.646 231.631a47.999 47.999 0 0 0 0 48.739C63.031 376.051 168.19 440 288 440c119.86 0 224.996-63.994 281.354-159.631a47.997 47.997 0 0 0 0-48.738zM288 392c-102.556 0-192.091-54.701-240-136 44.157-74.933 123.677-127.27 216.162-135.007C273.958 131.078 280 144.83 280 160c0 30.928-25.072 56-56 56s-56-25.072-56-56l.001-.042C157.794 179.043 152 200.844 152 224c0 75.111 60.889 136 136 136s136-60.889 136-136c0-31.031-10.4-59.629-27.895-82.515C451.704 164.638 498.009 205.106 528 256c-47.908 81.299-137.444 136-240 136z"${_scopeId}></path></svg><span class="text-center text-sm text-gray-500"${_scopeId}>${ssrInterpolate(tag.value.views || 0)}</span></div></div>`);
            if (tagSubtitle.value) {
              _push2(`<div class="mt-1 text-sm subtitle text-center"${_scopeId}>${ssrInterpolate(tagSubtitle.value)}</div>`);
            } else {
              _push2(`<!---->`);
            }
            if (tagDescription.value) {
              _push2(`<div itemprop="description" class="mt-1 mb-3 text-sm subtitle text-center"${_scopeId}>${ssrInterpolate(tagDescription.value)}</div>`);
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
                  cols: articleGridCols.value
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
            _push2(ssrRenderComponent(_sfc_main$9, { videos: mainVideos.value }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(SectionBanners, { banners: mainBanners.value }, null, _parent2, _scopeId));
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
                      class: [
                        "shrink-0 transition-all duration-300 overflow-hidden",
                        leftCollapsed.value ? "lg:w-6" : "lg:w-72"
                      ]
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
                        content: tagLocale.value
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
                                href: _ctx.route("public.blogArticles.index"),
                                class: "breadcrumb-link hover:underline"
                              }, {
                                default: withCtx(() => [
                                  createVNode("span", { itemprop: "name" }, toDisplayString(unref(t)("articles")), 1)
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
                              }, toDisplayString(unref(t)("tag")) + " #" + toDisplayString(tagName.value), 1),
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
                              createVNode("path", { d: "M288 248v28c0 6.6-5.4 12-12 12H108c-6.6 0-12-5.4-12-12v-28c0-6.6 5.4-12 12-12h168c6.6 0 12 5.4 12 12zm-12 72H108c-6.6 0-12 5.4-12 12v28c0 6.6 5.4 12 12 12h168c6.6 0 12-5.4 12-12v-28c0-6.6-5.4-12-12-12zm108-188.1V464c0 26.5-21.5 48-48 48H48c-26.5 0-48-21.5-48-48V48C0 21.5 21.5 0 48 0h204.1C264.8 0 277 5.1 286 14.1L369.9 98c9 8.9 14.1 21.2 14.1 33.9zm-128-80V128h76.1L256 51.9zM336 464V176H232c-13.3 0-24-10.7-24-24V48H48v416h288z" })
                            ])),
                            createVNode("span", { class: "text-center text-sm text-gray-500" }, toDisplayString(articlesCount.value), 1)
                          ], 8, ["title"]),
                          createVNode("div", { class: "my-3 flex flex-wrap items-center justify-center gap-3 title" }, [
                            hasSvgIcon.value ? (openBlock(), createBlock("span", {
                              key: 0,
                              class: "flex",
                              innerHTML: tag.value.icon
                            }, null, 8, ["innerHTML"])) : createCommentVNode("", true),
                            createVNode("h1", {
                              itemprop: "name",
                              class: "text-2xl font-bold"
                            }, " #" + toDisplayString(tagName.value), 1)
                          ]),
                          createVNode("div", {
                            title: unref(t)("views"),
                            class: "flex items-center justify-center gap-1"
                          }, [
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
                                content: tag.value.views || 0
                              }, null, 8, ["content"])
                            ]),
                            (openBlock(), createBlock("svg", {
                              class: "h-4 w-4 text-slate-600/85 dark:text-slate-200/85",
                              xmlns: "http://www.w3.org/2000/svg",
                              viewBox: "0 0 576 512",
                              fill: "currentColor"
                            }, [
                              createVNode("path", { d: "M569.354 231.631C512.97 135.949 407.81 72 288 72 168.14 72 63.004 135.994 6.646 231.631a47.999 47.999 0 0 0 0 48.739C63.031 376.051 168.19 440 288 440c119.86 0 224.996-63.994 281.354-159.631a47.997 47.997 0 0 0 0-48.738zM288 392c-102.556 0-192.091-54.701-240-136 44.157-74.933 123.677-127.27 216.162-135.007C273.958 131.078 280 144.83 280 160c0 30.928-25.072 56-56 56s-56-25.072-56-56l.001-.042C157.794 179.043 152 200.844 152 224c0 75.111 60.889 136 136 136s136-60.889 136-136c0-31.031-10.4-59.629-27.895-82.515C451.704 164.638 498.009 205.106 528 256c-47.908 81.299-137.444 136-240 136z" })
                            ])),
                            createVNode("span", { class: "text-center text-sm text-gray-500" }, toDisplayString(tag.value.views || 0), 1)
                          ], 8, ["title"])
                        ]),
                        tagSubtitle.value ? (openBlock(), createBlock("div", {
                          key: 0,
                          class: "mt-1 text-sm subtitle text-center"
                        }, toDisplayString(tagSubtitle.value), 1)) : createCommentVNode("", true),
                        tagDescription.value ? (openBlock(), createBlock("div", {
                          key: 1,
                          itemprop: "description",
                          class: "mt-1 mb-3 text-sm subtitle text-center"
                        }, toDisplayString(tagDescription.value), 1)) : createCommentVNode("", true),
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
                            cols: articleGridCols.value
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
                        createVNode(_sfc_main$9, { videos: mainVideos.value }, null, 8, ["videos"]),
                        createVNode(SectionBanners, { banners: mainBanners.value }, null, 8, ["banners"])
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
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Pages/Public/Default/Blog/BlogTags/Show.vue");
  return _sfc_setup ? _sfc_setup(props, ctx) : void 0;
};
export {
  _sfc_main as default
};
