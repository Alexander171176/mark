import { computed, ref, watch, unref, withCtx, createVNode, createBlock, createCommentVNode, toDisplayString, openBlock, useSSRContext } from "vue";
import { ssrRenderComponent, ssrInterpolate, ssrRenderAttr, ssrRenderClass } from "vue/server-renderer";
import { usePage, Head, Link, router } from "@inertiajs/vue3";
import { useI18n } from "vue-i18n";
import { u as useSmoothScrollTo } from "./useSmoothScrollTo-g9G3kyDv.js";
import { _ as _sfc_main$1 } from "./DefaultLayout-6zxGxPtK.js";
import { _ as _sfc_main$2, a as _sfc_main$b, P as Progress } from "./Progress-CyfOKcqP.js";
import { _ as _sfc_main$3, a as _sfc_main$a } from "./RightSidebarMarket-DpLr4Pjm.js";
import { _ as _sfc_main$4 } from "./EntityPageToolbar-DT32FtSd.js";
import { _ as _sfc_main$5 } from "./FrontendEntityPageToolbar-Xr_r9znL.js";
import { _ as _sfc_main$6, a as _sfc_main$7 } from "./MarketCategoryRows-D84VOzRP.js";
import { _ as _sfc_main$8, a as _sfc_main$9, b as _sfc_main$c } from "./PublicAdminBottomPanel-BhLEQMoJ.js";
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
import "./UniversalImageSlider-Cu2Xndcn.js";
import "./ProcessingModeSwitcher-BJvzFf6_.js";
const LEFT_SIDEBAR_KEY = "public_left_sidebar_collapsed";
const RIGHT_SIDEBAR_KEY = "public_right_sidebar_collapsed";
const VIEW_KEY = "public_market_categories_view";
const _sfc_main = {
  __name: "Index",
  __ssrInlineRender: true,
  props: {
    locale: { type: String, default: "" },
    seo: {
      type: Object,
      default: () => ({
        title: "",
        keywords: "",
        description: ""
      })
    },
    useServerProcessing: { type: Boolean, default: false },
    publicMarketCategoriesProcessingMode: { type: String, default: "server" },
    title: { type: String, default: "" },
    canLogin: { type: Boolean, default: false },
    canRegister: { type: Boolean, default: false },
    categoryTree: { type: Array, default: () => [] },
    categories: { type: [Array, Object], default: () => [] },
    categoriesCount: { type: Number, default: 0 },
    categoriesFound: { type: Number, default: 0 },
    filters: { type: Object, default: () => ({}) },
    /**
     * Источник значения —
     * PublicSettingsService на backend.
     */
    defaultSort: { type: String, default: "" }
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
    const categoryTree = computed(() => {
      return Array.isArray(props.categoryTree) ? props.categoryTree : [];
    });
    const categoriesData = computed(() => {
      var _a2;
      if (Array.isArray(props.categories)) {
        return props.categories;
      }
      if (Array.isArray((_a2 = props.categories) == null ? void 0 : _a2.data)) {
        return props.categories.data;
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
      getStoredBoolean(
        LEFT_SIDEBAR_KEY,
        true
      )
    );
    const rightCollapsed = ref(
      getStoredBoolean(
        RIGHT_SIDEBAR_KEY,
        true
      )
    );
    const categoryGridCols = computed(() => {
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
    watch(
      [leftCollapsed, rightCollapsed],
      () => {
        localStorage.setItem(
          LEFT_SIDEBAR_KEY,
          String(leftCollapsed.value)
        );
        localStorage.setItem(
          RIGHT_SIDEBAR_KEY,
          String(rightCollapsed.value)
        );
      }
    );
    const q = ref(
      String(
        ((_b = props.filters) == null ? void 0 : _b.q) ?? ""
      )
    );
    const sort = ref(
      String(
        ((_c = props.filters) == null ? void 0 : _c.sort) || props.defaultSort || ""
      )
    );
    const viewMode = ref(
      String(
        ((_d = props.filters) == null ? void 0 : _d.view) || localStorage.getItem(VIEW_KEY) || "grid"
      )
    );
    watch(viewMode, (value) => {
      localStorage.setItem(
        VIEW_KEY,
        value
      );
    });
    const perPage = computed(() => {
      var _a2;
      const value = Number(
        (_a2 = props.filters) == null ? void 0 : _a2.per_page
      );
      return Number.isFinite(value) && value > 0 ? value : 1;
    });
    const categorySortOptions = [
      { value: "idDesc", label: "ID 9→0" },
      { value: "idAsc", label: "ID 0→9" },
      { value: "sortAsc", label: `${t("sortNumber")} 0→9` },
      { value: "sortDesc", label: `${t("sortNumber")} 9→0` },
      { value: "titleAsc", label: `${t("title")} A→Z` },
      { value: "titleDesc", label: `${t("title")} Z→A` },
      { value: "levelAsc", label: `${t("level")} 0→9` },
      { value: "levelDesc", label: `${t("level")} 9→0` },
      { value: "parentAsc", label: `Parent ID 0→9` },
      { value: "parentDesc", label: `Parent ID 9→0` },
      { value: "childrenDesc", label: `${t("subheadings")} 9→0` },
      { value: "childrenAsc", label: `${t("subheadings")} 0→9` },
      { value: "productsDesc", label: `${t("products")} 9→0` },
      { value: "productsAsc", label: `${t("products")} 0→9` },
      { value: "imagesDesc", label: `${t("images")} 9→0` },
      { value: "imagesAsc", label: `${t("images")} 0→9` },
      { value: "viewsDesc", label: `${t("views")} 9→0` },
      { value: "viewsAsc", label: `${t("views")} 0→9` },
      { value: "urlAsc", label: "URL A→Z" },
      { value: "urlDesc", label: "URL Z→A" },
      { value: "publishedAtDesc", label: `${t("publishedAt")} 9→0` },
      { value: "publishedAtAsc", label: `${t("publishedAt")} 0→9` }
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
      return String(
        value ?? ""
      ).toLowerCase();
    };
    const getCategoryTranslation = (category) => {
      return (category == null ? void 0 : category.translation) || null;
    };
    const getCategoryTitle = (category) => {
      var _a2;
      return ((_a2 = getCategoryTranslation(
        category
      )) == null ? void 0 : _a2.title) || "";
    };
    const getCategorySubtitle = (category) => {
      var _a2;
      return ((_a2 = getCategoryTranslation(
        category
      )) == null ? void 0 : _a2.subtitle) || "";
    };
    const getCategoryShort = (category) => {
      var _a2;
      return ((_a2 = getCategoryTranslation(
        category
      )) == null ? void 0 : _a2.short) || "";
    };
    const getParentTitle = (category) => {
      var _a2, _b2;
      return ((_b2 = (_a2 = category == null ? void 0 : category.parent) == null ? void 0 : _a2.translation) == null ? void 0 : _b2.title) || "";
    };
    const searchWords = computed(() => {
      return normalizeText(q.value).trim().split(/\s+/u).filter(
        (word) => word.length >= 2
      );
    });
    const getCategorySearchValues = (category) => {
      return [
        category == null ? void 0 : category.id,
        category == null ? void 0 : category.parent_id,
        category == null ? void 0 : category.level,
        category == null ? void 0 : category.url,
        category == null ? void 0 : category.icon,
        category == null ? void 0 : category.views,
        getCategoryTitle(category),
        getCategorySubtitle(category),
        getCategoryShort(category),
        getParentTitle(category)
      ].map(normalizeText);
    };
    const filteredCategories = computed(() => {
      const words = searchWords.value;
      if (!words.length) {
        return categoriesData.value;
      }
      return categoriesData.value.filter(
        (category) => {
          const values = getCategorySearchValues(
            category
          );
          return words.every(
            (word) => values.some(
              (value) => value.includes(word)
            )
          );
        }
      );
    });
    const compareNumber = (a, b, direction = "asc") => {
      const first = Number(a ?? 0);
      const second = Number(b ?? 0);
      return direction === "desc" ? second - first : first - second;
    };
    const compareText = (a, b, direction = "asc") => {
      const first = normalizeText(a);
      const second = normalizeText(b);
      const result = first.localeCompare(second);
      return direction === "desc" ? -result : result;
    };
    const compareDate = (a, b, direction = "asc") => {
      const first = a ? new Date(a).getTime() : 0;
      const second = b ? new Date(b).getTime() : 0;
      return direction === "desc" ? second - first : first - second;
    };
    const compareIdDesc = (a, b) => {
      return Number((b == null ? void 0 : b.id) ?? 0) - Number((a == null ? void 0 : a.id) ?? 0);
    };
    const sortedCategories = computed(() => {
      const list = [
        ...filteredCategories.value
      ];
      return list.sort((a, b) => {
        let result = 0;
        switch (sort.value) {
          case "idAsc":
            return compareNumber(
              a.id,
              b.id
            );
          case "idDesc":
            return compareNumber(
              a.id,
              b.id,
              "desc"
            );
          case "sortAsc":
            result = compareNumber(
              a.sort,
              b.sort
            );
            break;
          case "sortDesc":
            result = compareNumber(
              a.sort,
              b.sort,
              "desc"
            );
            break;
          case "titleAsc":
            result = compareText(
              getCategoryTitle(a),
              getCategoryTitle(b)
            );
            break;
          case "titleDesc":
            result = compareText(
              getCategoryTitle(a),
              getCategoryTitle(b),
              "desc"
            );
            break;
          case "levelAsc":
            result = compareNumber(
              a.level,
              b.level
            );
            break;
          case "levelDesc":
            result = compareNumber(
              a.level,
              b.level,
              "desc"
            );
            break;
          case "parentAsc":
            result = compareNumber(
              a.parent_id,
              b.parent_id
            );
            break;
          case "parentDesc":
            result = compareNumber(
              a.parent_id,
              b.parent_id,
              "desc"
            );
            break;
          case "childrenAsc":
            result = compareNumber(
              a.children_count,
              b.children_count
            );
            break;
          case "childrenDesc":
            result = compareNumber(
              a.children_count,
              b.children_count,
              "desc"
            );
            break;
          case "productsAsc":
            result = compareNumber(
              a.products_count,
              b.products_count
            );
            break;
          case "productsDesc":
            result = compareNumber(
              a.products_count,
              b.products_count,
              "desc"
            );
            break;
          case "imagesAsc":
            result = compareNumber(
              a.images_count,
              b.images_count
            );
            break;
          case "imagesDesc":
            result = compareNumber(
              a.images_count,
              b.images_count,
              "desc"
            );
            break;
          case "viewsAsc":
            result = compareNumber(
              a.views,
              b.views
            );
            break;
          case "viewsDesc":
            result = compareNumber(
              a.views,
              b.views,
              "desc"
            );
            break;
          case "urlAsc":
            result = compareText(
              a.url,
              b.url
            );
            break;
          case "urlDesc":
            result = compareText(
              a.url,
              b.url,
              "desc"
            );
            break;
          case "publishedAtAsc":
            result = compareDate(
              a.published_at,
              b.published_at
            );
            break;
          case "publishedAtDesc":
            result = compareDate(
              a.published_at,
              b.published_at,
              "desc"
            );
            break;
          default:
            result = compareNumber(
              a.sort,
              b.sort
            );
            break;
        }
        return result !== 0 ? result : compareIdDesc(a, b);
      });
    });
    const frontendPaginatedCategories = computed(() => {
      const start = (frontendCurrentPage.value - 1) * perPage.value;
      return sortedCategories.value.slice(
        start,
        start + perPage.value
      );
    });
    watch(
      [q, sort, viewMode],
      () => {
        frontendCurrentPage.value = 1;
      }
    );
    watch(frontendCurrentPage, () => {
      if (!props.useServerProcessing) {
        scrollToTarget();
      }
    });
    const currentPage = computed(() => {
      var _a2, _b2, _c2;
      return Number(
        ((_b2 = (_a2 = props.categories) == null ? void 0 : _a2.meta) == null ? void 0 : _b2.current_page) ?? ((_c2 = props.categories) == null ? void 0 : _c2.current_page) ?? 1
      ) || 1;
    });
    const lastPage = computed(() => {
      var _a2, _b2, _c2;
      return Number(
        ((_b2 = (_a2 = props.categories) == null ? void 0 : _a2.meta) == null ? void 0 : _b2.last_page) ?? ((_c2 = props.categories) == null ? void 0 : _c2.last_page) ?? 1
      ) || 1;
    });
    const contentLocale = computed(() => String(props.locale || ""));
    const seoTitle = computed(() => {
      var _a2;
      return ((_a2 = props.seo) == null ? void 0 : _a2.title) || t("categories");
    });
    const seoKeywords = computed(() => {
      var _a2;
      return ((_a2 = props.seo) == null ? void 0 : _a2.keywords) || "";
    });
    const seoDescription = computed(() => {
      var _a2;
      return ((_a2 = props.seo) == null ? void 0 : _a2.description) || "";
    });
    const ogLocale = computed(() => contentLocale.value);
    const dcSubject = computed(() => seoKeywords.value || seoTitle.value);
    const canonicalUrl = computed(() => {
      const baseUrl = String(route("public.marketCategories.index"));
      if (props.useServerProcessing && currentPage.value > 1) {
        return `${baseUrl}?page=${currentPage.value}`;
      }
      return baseUrl;
    });
    const robotsContent = computed(() => {
      const hasSearch = normalizeText(q.value).trim().length > 0;
      const hasAlternativeSort = String(sort.value || props.defaultSort) !== String(props.defaultSort);
      if (props.useServerProcessing && (hasSearch || hasAlternativeSort)) {
        return "noindex, follow, max-image-preview:large";
      }
      return "index, follow, max-image-preview:large";
    });
    const indexRoute = () => {
      return route(
        "public.marketCategories.index"
      );
    };
    const reloadCategories = (page2 = 1) => {
      router.get(
        indexRoute(),
        {
          q: q.value || void 0,
          sort: sort.value || props.defaultSort || void 0,
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
      reloadCategories(1);
    };
    const resetSearch = () => {
      q.value = "";
      sort.value = props.defaultSort || "";
      frontendCurrentPage.value = 1;
      if (props.useServerProcessing) {
        reloadCategories(1);
      }
    };
    const updateSort = (value) => {
      sort.value = value || props.defaultSort || "";
      if (props.useServerProcessing) {
        reloadCategories(1);
      }
    };
    const updateViewMode = (value) => {
      viewMode.value = value || "grid";
      frontendCurrentPage.value = 1;
      if (props.useServerProcessing) {
        reloadCategories(1);
      }
    };
    const goToPage = (page2) => {
      const value = Number(page2);
      if (!Number.isFinite(value)) {
        return;
      }
      const safePage = Math.max(
        1,
        Math.min(
          value,
          lastPage.value
        )
      );
      reloadCategories(safePage);
    };
    const goPrev = () => {
      if (currentPage.value <= 1) {
        return;
      }
      goToPage(
        currentPage.value - 1
      );
    };
    const goNext = () => {
      if (currentPage.value >= lastPage.value) {
        return;
      }
      goToPage(
        currentPage.value + 1
      );
    };
    const displayedCategories = computed(() => {
      return props.useServerProcessing ? categoriesData.value : frontendPaginatedCategories.value;
    });
    const categoryListTotalItems = computed(() => {
      return props.useServerProcessing ? Number(props.categoriesFound ?? 0) : sortedCategories.value.length;
    });
    const categoryListStartPosition = computed(() => {
      const pageNumber = props.useServerProcessing ? currentPage.value : frontendCurrentPage.value;
      return Math.max(0, (pageNumber - 1) * perPage.value);
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
            if (contentLocale.value) {
              _push2(`<meta http-equiv="content-language"${ssrRenderAttr("content", contentLocale.value)}${_scopeId}>`);
            } else {
              _push2(`<!---->`);
            }
            _push2(`<meta name="robots"${ssrRenderAttr("content", robotsContent.value)}${_scopeId}><link rel="canonical"${ssrRenderAttr("href", canonicalUrl.value)}${_scopeId}><meta property="og:type" content="website"${_scopeId}><meta property="og:title"${ssrRenderAttr("content", seoTitle.value)}${_scopeId}>`);
            if (seoDescription.value) {
              _push2(`<meta property="og:description"${ssrRenderAttr("content", seoDescription.value)}${_scopeId}>`);
            } else {
              _push2(`<!---->`);
            }
            _push2(`<meta property="og:url"${ssrRenderAttr("content", canonicalUrl.value)}${_scopeId}>`);
            if (ogLocale.value) {
              _push2(`<meta property="og:locale"${ssrRenderAttr("content", ogLocale.value)}${_scopeId}>`);
            } else {
              _push2(`<!---->`);
            }
            _push2(`<meta name="twitter:card" content="summary"${_scopeId}><meta name="twitter:title"${ssrRenderAttr("content", seoTitle.value)}${_scopeId}>`);
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
            if (contentLocale.value) {
              _push2(`<meta name="DC.language"${ssrRenderAttr("content", contentLocale.value)}${_scopeId}>`);
            } else {
              _push2(`<!---->`);
            }
            _push2(`<meta name="DC.identifier"${ssrRenderAttr("content", canonicalUrl.value)}${_scopeId}><meta name="DC.type" content="Collection"${_scopeId}><meta name="DC.format" content="text/html"${_scopeId}>`);
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
              contentLocale.value ? (openBlock(), createBlock("meta", {
                key: 2,
                "http-equiv": "content-language",
                content: contentLocale.value
              }, null, 8, ["content"])) : createCommentVNode("", true),
              createVNode("meta", {
                name: "robots",
                content: robotsContent.value
              }, null, 8, ["content"]),
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
                key: 3,
                property: "og:description",
                content: seoDescription.value
              }, null, 8, ["content"])) : createCommentVNode("", true),
              createVNode("meta", {
                property: "og:url",
                content: canonicalUrl.value
              }, null, 8, ["content"]),
              ogLocale.value ? (openBlock(), createBlock("meta", {
                key: 4,
                property: "og:locale",
                content: ogLocale.value
              }, null, 8, ["content"])) : createCommentVNode("", true),
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
              contentLocale.value ? (openBlock(), createBlock("meta", {
                key: 8,
                name: "DC.language",
                content: contentLocale.value
              }, null, 8, ["content"])) : createCommentVNode("", true),
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
              _push2(`<aside class="${ssrRenderClass([leftCollapsed.value ? "lg:w-6" : "lg:w-72", "shrink-0 transition-all duration-300"])}"${_scopeId}>`);
              _push2(ssrRenderComponent(_sfc_main$3, {
                "category-tree": categoryTree.value,
                collapsed: leftCollapsed.value,
                onCollapsed: ($event) => leftCollapsed.value = $event
              }, null, _parent2, _scopeId));
              _push2(`</aside>`);
            } else {
              _push2(`<!---->`);
            }
            _push2(`<div class="w-full pb-6 slate-1"${_scopeId}><div class="mx-auto max-w-6xl"${_scopeId}><nav class="text-sm" aria-label="Breadcrumb" itemscope itemtype="https://schema.org/BreadcrumbList"${_scopeId}><ol class="flex flex-wrap items-center font-semibold"${_scopeId}><li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem" class="flex items-center"${_scopeId}>`);
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
            _push2(`<meta itemprop="position" content="1"${_scopeId}></li><li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem" class="flex items-center" aria-current="page"${_scopeId}><span class="mx-2 breadcrumbs"${_scopeId}>/</span><h1 itemprop="name" class="breadcrumbs text-sm font-semibold"${_scopeId}>${ssrInterpolate(unref(t)("categories"))}</h1><meta itemprop="item"${ssrRenderAttr("content", canonicalUrl.value)}${_scopeId}><meta itemprop="position" content="2"${_scopeId}></li></ol></nav><article class="mt-3" itemscope itemtype="https://schema.org/CollectionPage"${ssrRenderAttr("itemid", canonicalUrl.value)}${_scopeId}><meta itemprop="url"${ssrRenderAttr("content", canonicalUrl.value)}${_scopeId}><meta itemprop="name"${ssrRenderAttr("content", seoTitle.value)}${_scopeId}>`);
            if (seoDescription.value) {
              _push2(`<meta itemprop="description"${ssrRenderAttr("content", seoDescription.value)}${_scopeId}>`);
            } else {
              _push2(`<!---->`);
            }
            if (contentLocale.value) {
              _push2(`<meta itemprop="inLanguage"${ssrRenderAttr("content", contentLocale.value)}${_scopeId}>`);
            } else {
              _push2(`<!---->`);
            }
            _push2(`<div class="my-3 flex flex-wrap items-center justify-center gap-3 title"${_scopeId}><svg class="h-5 w-5 text-slate-600/85 dark:text-slate-200/85" viewBox="0 0 24 24" fill="currentColor"${_scopeId}><path d="M4 4h6v6H4V4Zm10 0h6v6h-6V4ZM4 14h6v6H4v-6Zm10 0h6v6h-6v-6Z"${_scopeId}></path></svg><h2 class="text-2xl font-bold"${_scopeId}>${ssrInterpolate(unref(t)("categories"))}</h2></div>`);
            if (__props.useServerProcessing) {
              _push2(ssrRenderComponent(_sfc_main$4, {
                modelValue: q.value,
                "onUpdate:modelValue": ($event) => q.value = $event,
                found: __props.categoriesFound,
                "view-mode": viewMode.value,
                "sort-value": sort.value,
                "sort-options": categorySortOptions,
                "default-sort": __props.defaultSort,
                "found-label": unref(t)("categories"),
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
                found: sortedCategories.value.length,
                "view-mode": viewMode.value,
                "sort-value": sort.value,
                "sort-options": categorySortOptions,
                "default-sort": __props.defaultSort,
                "found-label": unref(t)("categories"),
                "search-placeholder": unref(t)("searchByName"),
                onReset: resetSearch,
                "onUpdate:viewMode": updateViewMode,
                "onUpdate:sortValue": updateSort
              }, null, _parent2, _scopeId));
            }
            _push2(`<div${_scopeId}></div>`);
            if (displayedCategories.value.length === 0) {
              _push2(`<div class="mt-6 text-center text-slate-700 dark:text-slate-300"${_scopeId}>${ssrInterpolate(unref(t)("noData"))}</div>`);
            } else {
              _push2(`<div${_scopeId}>`);
              if (viewMode.value === "grid") {
                _push2(ssrRenderComponent(_sfc_main$6, {
                  categories: displayedCategories.value,
                  cols: categoryGridCols.value,
                  "start-position": categoryListStartPosition.value,
                  "total-items": categoryListTotalItems.value,
                  "schema-property": "mainEntity"
                }, null, _parent2, _scopeId));
              } else {
                _push2(ssrRenderComponent(_sfc_main$7, {
                  categories: displayedCategories.value,
                  "start-position": categoryListStartPosition.value,
                  "total-items": categoryListTotalItems.value,
                  "schema-property": "mainEntity"
                }, null, _parent2, _scopeId));
              }
              _push2(`</div>`);
            }
            if (__props.useServerProcessing) {
              _push2(ssrRenderComponent(_sfc_main$8, {
                "current-page": currentPage.value,
                "last-page": lastPage.value,
                found: __props.categoriesFound,
                onPrev: goPrev,
                onNext: goNext,
                onGo: goToPage
              }, null, _parent2, _scopeId));
            } else {
              _push2(ssrRenderComponent(_sfc_main$9, {
                currentPage: frontendCurrentPage.value,
                "onUpdate:currentPage": ($event) => frontendCurrentPage.value = $event,
                "items-per-page": perPage.value,
                "total-items": sortedCategories.value.length
              }, null, _parent2, _scopeId));
            }
            _push2(`</article></div></div>`);
            if (showRight.value) {
              _push2(`<aside class="${ssrRenderClass([rightCollapsed.value ? "lg:w-6" : "lg:w-72", "shrink-0 transition-all duration-300"])}"${_scopeId}>`);
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
            if (isAdmin.value) {
              _push2(ssrRenderComponent(_sfc_main$c, {
                "setting-key": "publicMarketCategoriesProcessingMode",
                mode: __props.publicMarketCategoriesProcessingMode,
                "use-server-processing": __props.useServerProcessing,
                total: __props.categoriesCount
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
                        "category-tree": categoryTree.value,
                        collapsed: leftCollapsed.value,
                        onCollapsed: ($event) => leftCollapsed.value = $event
                      }, null, 8, ["category-tree", "collapsed", "onCollapsed"])
                    ], 2)) : createCommentVNode("", true),
                    createVNode("div", { class: "w-full pb-6 slate-1" }, [
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
                              createVNode("h1", {
                                itemprop: "name",
                                class: "breadcrumbs text-sm font-semibold"
                              }, toDisplayString(unref(t)("categories")), 1),
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
                        createVNode("article", {
                          class: "mt-3",
                          itemscope: "",
                          itemtype: "https://schema.org/CollectionPage",
                          itemid: canonicalUrl.value
                        }, [
                          createVNode("meta", {
                            itemprop: "url",
                            content: canonicalUrl.value
                          }, null, 8, ["content"]),
                          createVNode("meta", {
                            itemprop: "name",
                            content: seoTitle.value
                          }, null, 8, ["content"]),
                          seoDescription.value ? (openBlock(), createBlock("meta", {
                            key: 0,
                            itemprop: "description",
                            content: seoDescription.value
                          }, null, 8, ["content"])) : createCommentVNode("", true),
                          contentLocale.value ? (openBlock(), createBlock("meta", {
                            key: 1,
                            itemprop: "inLanguage",
                            content: contentLocale.value
                          }, null, 8, ["content"])) : createCommentVNode("", true),
                          createVNode("div", { class: "my-3 flex flex-wrap items-center justify-center gap-3 title" }, [
                            (openBlock(), createBlock("svg", {
                              class: "h-5 w-5 text-slate-600/85 dark:text-slate-200/85",
                              viewBox: "0 0 24 24",
                              fill: "currentColor"
                            }, [
                              createVNode("path", { d: "M4 4h6v6H4V4Zm10 0h6v6h-6V4ZM4 14h6v6H4v-6Zm10 0h6v6h-6v-6Z" })
                            ])),
                            createVNode("h2", { class: "text-2xl font-bold" }, toDisplayString(unref(t)("categories")), 1)
                          ]),
                          __props.useServerProcessing ? (openBlock(), createBlock(_sfc_main$4, {
                            key: 2,
                            modelValue: q.value,
                            "onUpdate:modelValue": ($event) => q.value = $event,
                            found: __props.categoriesFound,
                            "view-mode": viewMode.value,
                            "sort-value": sort.value,
                            "sort-options": categorySortOptions,
                            "default-sort": __props.defaultSort,
                            "found-label": unref(t)("categories"),
                            "search-placeholder": unref(t)("searchByName"),
                            onSubmit: submitSearch,
                            onReset: resetSearch,
                            "onUpdate:viewMode": updateViewMode,
                            "onUpdate:sortValue": updateSort
                          }, null, 8, ["modelValue", "onUpdate:modelValue", "found", "view-mode", "sort-value", "default-sort", "found-label", "search-placeholder"])) : (openBlock(), createBlock(_sfc_main$5, {
                            key: 3,
                            modelValue: q.value,
                            "onUpdate:modelValue": ($event) => q.value = $event,
                            found: sortedCategories.value.length,
                            "view-mode": viewMode.value,
                            "sort-value": sort.value,
                            "sort-options": categorySortOptions,
                            "default-sort": __props.defaultSort,
                            "found-label": unref(t)("categories"),
                            "search-placeholder": unref(t)("searchByName"),
                            onReset: resetSearch,
                            "onUpdate:viewMode": updateViewMode,
                            "onUpdate:sortValue": updateSort
                          }, null, 8, ["modelValue", "onUpdate:modelValue", "found", "view-mode", "sort-value", "default-sort", "found-label", "search-placeholder"])),
                          createVNode("div", {
                            ref_key: "scrollTarget",
                            ref: scrollTarget
                          }, null, 512),
                          displayedCategories.value.length === 0 ? (openBlock(), createBlock("div", {
                            key: 4,
                            class: "mt-6 text-center text-slate-700 dark:text-slate-300"
                          }, toDisplayString(unref(t)("noData")), 1)) : (openBlock(), createBlock("div", { key: 5 }, [
                            viewMode.value === "grid" ? (openBlock(), createBlock(_sfc_main$6, {
                              key: 0,
                              categories: displayedCategories.value,
                              cols: categoryGridCols.value,
                              "start-position": categoryListStartPosition.value,
                              "total-items": categoryListTotalItems.value,
                              "schema-property": "mainEntity"
                            }, null, 8, ["categories", "cols", "start-position", "total-items"])) : (openBlock(), createBlock(_sfc_main$7, {
                              key: 1,
                              categories: displayedCategories.value,
                              "start-position": categoryListStartPosition.value,
                              "total-items": categoryListTotalItems.value,
                              "schema-property": "mainEntity"
                            }, null, 8, ["categories", "start-position", "total-items"]))
                          ])),
                          __props.useServerProcessing ? (openBlock(), createBlock(_sfc_main$8, {
                            key: 6,
                            "current-page": currentPage.value,
                            "last-page": lastPage.value,
                            found: __props.categoriesFound,
                            onPrev: goPrev,
                            onNext: goNext,
                            onGo: goToPage
                          }, null, 8, ["current-page", "last-page", "found"])) : (openBlock(), createBlock(_sfc_main$9, {
                            key: 7,
                            currentPage: frontendCurrentPage.value,
                            "onUpdate:currentPage": ($event) => frontendCurrentPage.value = $event,
                            "items-per-page": perPage.value,
                            "total-items": sortedCategories.value.length
                          }, null, 8, ["currentPage", "onUpdate:currentPage", "items-per-page", "total-items"]))
                        ], 8, ["itemid"])
                      ])
                    ]),
                    showRight.value ? (openBlock(), createBlock("aside", {
                      key: 1,
                      class: ["shrink-0 transition-all duration-300", rightCollapsed.value ? "lg:w-6" : "lg:w-72"]
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
              createVNode(Progress),
              isAdmin.value ? (openBlock(), createBlock(_sfc_main$c, {
                key: 0,
                "setting-key": "publicMarketCategoriesProcessingMode",
                mode: __props.publicMarketCategoriesProcessingMode,
                "use-server-processing": __props.useServerProcessing,
                total: __props.categoriesCount
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
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Pages/Public/Default/Market/MarketCategories/Index.vue");
  return _sfc_setup ? _sfc_setup(props, ctx) : void 0;
};
export {
  _sfc_main as default
};
