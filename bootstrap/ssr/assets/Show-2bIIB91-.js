import { computed, ref, watch, unref, withCtx, createVNode, createBlock, createCommentVNode, toDisplayString, openBlock, useSSRContext } from "vue";
import { ssrRenderComponent, ssrInterpolate, ssrRenderAttr, ssrRenderClass } from "vue/server-renderer";
import { usePage, Head, Link, router } from "@inertiajs/vue3";
import { useI18n } from "vue-i18n";
import { u as useSmoothScrollTo } from "./useSmoothScrollTo-g9G3kyDv.js";
import { _ as _sfc_main$1 } from "./DefaultLayout-6zxGxPtK.js";
import { _ as _sfc_main$2, a as _sfc_main$e, P as Progress } from "./Progress-CyfOKcqP.js";
import { _ as _sfc_main$3, a as _sfc_main$d } from "./RightSidebarMarket-DpLr4Pjm.js";
import { _ as _sfc_main$7 } from "./EntityPageToolbar-DT32FtSd.js";
import { _ as _sfc_main$8 } from "./FrontendEntityPageToolbar-Xr_r9znL.js";
import { _ as _sfc_main$9, a as _sfc_main$a } from "./MarketProductRows-D4G6aZpS.js";
import { _ as _sfc_main$5, a as _sfc_main$6 } from "./MarketCategoryRows-D84VOzRP.js";
import { _ as _sfc_main$b, a as _sfc_main$c, b as _sfc_main$f } from "./PublicAdminBottomPanel-BhLEQMoJ.js";
import { I as ImageGalleryMain } from "./ImageGalleryMain-mIfXDUWm.js";
import { _ as _sfc_main$4 } from "./ViewModeToggle-DMCnQ0wo.js";
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
import "./UniversalImageSlider-Cu2Xndcn.js";
import "./LikeButtonEntity-ZC4HMEAO.js";
import "./ProcessingModeSwitcher-BJvzFf6_.js";
const CATEGORY_VIEW_KEY = "public_market_category_children_view";
const LEFT_SIDEBAR_KEY = "public_left_sidebar_collapsed";
const RIGHT_SIDEBAR_KEY = "public_right_sidebar_collapsed";
const VIEW_KEY = "public_market_products_view";
const _sfc_main = {
  __name: "Show",
  __ssrInlineRender: true,
  props: {
    /** Активная локаль от backend */
    locale: { type: String, default: "" },
    /** Текущая Public-категория */
    category: { type: Object, default: () => ({}) },
    /** Связанные Public-товары */
    products: {
      type: [Array, Object],
      default: () => []
    },
    /** Общее количество Public-товаров категории */
    productsCount: { type: Number, default: 0 },
    /** Количество найденных товаров */
    productsFound: { type: Number, default: 0 },
    /** Режим обработки товаров */
    useServerProcessing: { type: Boolean, default: false },
    /** Настройка server / frontend / auto */
    publicMarketProductsProcessingMode: { type: String, default: "server" },
    /** Сортировка Public по умолчанию */
    defaultSort: { type: String, default: "sortAsc" },
    /** Текущие параметры списка */
    filters: { type: Object, default: () => ({}) },
    /** Дерево категорий */
    categoryTree: { type: Array, default: () => [] }
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
    const categoryTree = computed(() => Array.isArray(props.categoryTree) ? props.categoryTree : []);
    const category = computed(() => props.category || {});
    const categoryTranslation = computed(() => {
      var _a2;
      return ((_a2 = category.value) == null ? void 0 : _a2.translation) || {};
    });
    const categoryTitle = computed(() => {
      var _a2;
      return ((_a2 = categoryTranslation.value) == null ? void 0 : _a2.title) || "";
    });
    const categorySubtitle = computed(() => {
      var _a2;
      return ((_a2 = categoryTranslation.value) == null ? void 0 : _a2.subtitle) || "";
    });
    const categoryShort = computed(() => {
      var _a2;
      return ((_a2 = categoryTranslation.value) == null ? void 0 : _a2.short) || "";
    });
    const categoryDescription = computed(() => {
      var _a2;
      return ((_a2 = categoryTranslation.value) == null ? void 0 : _a2.description) || categoryShort.value || "";
    });
    const categoryProductsCount = computed(() => {
      var _a2;
      return Number(
        props.productsCount ?? ((_a2 = category.value) == null ? void 0 : _a2.products_count) ?? 0
      );
    });
    const categoryViews = computed(() => {
      var _a2;
      return Number(((_a2 = category.value) == null ? void 0 : _a2.views) ?? 0);
    });
    const childCategories = computed(() => {
      var _a2;
      const value = (_a2 = category.value) == null ? void 0 : _a2.children;
      if (Array.isArray(value)) {
        return value;
      }
      if (Array.isArray(value == null ? void 0 : value.data)) {
        return value.data;
      }
      return [];
    });
    const childrenCount = computed(() => {
      var _a2;
      return Number(
        ((_a2 = category.value) == null ? void 0 : _a2.children_count) ?? childCategories.value.length ?? 0
      );
    });
    const parentCategory = computed(() => {
      var _a2;
      return ((_a2 = category.value) == null ? void 0 : _a2.parent) || null;
    });
    const parentCategoryTitle = computed(() => {
      var _a2, _b2;
      return ((_b2 = (_a2 = parentCategory.value) == null ? void 0 : _a2.translation) == null ? void 0 : _b2.title) || "";
    });
    const categoryImages = computed(() => {
      var _a2;
      const value = (_a2 = category.value) == null ? void 0 : _a2.images;
      if (Array.isArray(value)) {
        return value;
      }
      if (Array.isArray(value == null ? void 0 : value.data)) {
        return value.data;
      }
      return [];
    });
    const hasCategoryImages = computed(() => categoryImages.value.length > 0);
    const normalizeStorageUrl = (value) => {
      if (!value) {
        return "";
      }
      const url = String(value).trim();
      if (!url) {
        return "";
      }
      if (url.startsWith("http://") || url.startsWith("https://") || url.startsWith("/storage/")) {
        return url;
      }
      return `/storage/${url}`;
    };
    const categoryPrimaryImage = computed(() => {
      const image = categoryImages.value[0];
      return normalizeStorageUrl(
        (image == null ? void 0 : image.webp_url) || (image == null ? void 0 : image.image_url) || (image == null ? void 0 : image.thumb_url) || (image == null ? void 0 : image.url) || ""
      );
    });
    const categoryPrimaryImageAlt = computed(() => {
      const image = categoryImages.value[0];
      return (image == null ? void 0 : image.alt) || categoryTitle.value || "";
    });
    const hasSvgIcon = computed(() => {
      var _a2;
      if (!((_a2 = category.value) == null ? void 0 : _a2.icon)) {
        return false;
      }
      return /^\s*<svg[\s\S]*<\/svg>\s*$/i.test(
        String(category.value.icon)
      );
    });
    const categoryViewMode = ref(localStorage.getItem(CATEGORY_VIEW_KEY) || "grid");
    watch(categoryViewMode, (value) => localStorage.setItem(CATEGORY_VIEW_KEY, value));
    const productsData = computed(() => {
      var _a2;
      if (Array.isArray(props.products)) {
        return props.products;
      }
      if (Array.isArray(
        (_a2 = props.products) == null ? void 0 : _a2.data
      )) {
        return props.products.data;
      }
      return [];
    });
    const showLeft = computed(() => !(siteSettings == null ? void 0 : siteSettings.ViewLeftColumn) || siteSettings.ViewLeftColumn === "true");
    const showRight = computed(() => !(siteSettings == null ? void 0 : siteSettings.ViewRightColumn) || siteSettings.ViewRightColumn === "true");
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
    const productGridCols = computed(() => {
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
      [
        leftCollapsed,
        rightCollapsed
      ],
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
        ((_c = props.filters) == null ? void 0 : _c.sort) ?? props.defaultSort
      )
    );
    const viewMode = ref(
      String(
        ((_d = props.filters) == null ? void 0 : _d.view) || localStorage.getItem(VIEW_KEY) || "grid"
      )
    );
    watch(viewMode, (value) => localStorage.setItem(VIEW_KEY, value));
    const perPage = computed(() => {
      var _a2;
      const value = Number(
        (_a2 = props.filters) == null ? void 0 : _a2.per_page
      );
      return Number.isFinite(value) && value > 0 ? value : 12;
    });
    const productSortOptions = [
      { value: "idDesc", label: t("idDesc") },
      { value: "idAsc", label: t("idAsc") },
      { value: "sortAsc", label: `${t("sortNumber")} 0→9` },
      { value: "sortDesc", label: `${t("sortNumber")} 9→0` },
      { value: "titleAsc", label: `${t("title")} A→Z` },
      { value: "titleDesc", label: `${t("title")} Z→A` },
      { value: "priceAsc", label: `${t("price")} 0→9` },
      { value: "priceDesc", label: `${t("price")} 9→0` },
      { value: "quantityAsc", label: `${t("quantity")} 0→9` },
      { value: "quantityDesc", label: `${t("quantity")} 9→0` },
      { value: "viewsDesc", label: `${t("views")} 9→0` },
      { value: "viewsAsc", label: `${t("views")} 0→9` },
      { value: "likesDesc", label: `${t("likes")} 9→0` },
      { value: "likesAsc", label: `${t("likes")} 0→9` },
      { value: "ratingDesc", label: `${t("rating")} 9→0` },
      { value: "ratingAsc", label: `${t("rating")} 0→9` },
      { value: "ratingCountDesc", label: `${t("ratingCount")} 9→0` },
      { value: "ratingCountAsc", label: `${t("ratingCount")} 0→9` },
      { value: "reviewsDesc", label: `${t("reviews")} 9→0` },
      { value: "reviewsAsc", label: `${t("reviews")} 0→9` },
      { value: "categoryAsc", label: `${t("category")} A→Z` },
      { value: "categoryDesc", label: `${t("category")} Z→A` },
      { value: "publishedAtDesc", label: `${t("publishedAt")} ↓` },
      { value: "publishedAtAsc", label: `${t("publishedAt")} ↑` },
      { value: "createdAtDesc", label: `${t("createdAt")} ↓` },
      { value: "createdAtAsc", label: `${t("createdAt")} ↑` }
    ];
    const frontendCurrentPage = ref(1);
    const { targetRef: scrollTarget, scrollToTarget } = useSmoothScrollTo(
      { offset: 80, duration: 1200 }
    );
    const normalizeText = (value) => {
      return String(value ?? "").toLocaleLowerCase().trim();
    };
    const normalizeNumber = (value) => {
      const number = Number(value);
      return Number.isFinite(number) ? number : 0;
    };
    const normalizeDate = (value) => {
      if (!value) {
        return 0;
      }
      const timestamp = new Date(value).getTime();
      return Number.isFinite(timestamp) ? timestamp : 0;
    };
    const getProductTitle = (product) => {
      var _a2;
      return ((_a2 = product == null ? void 0 : product.translation) == null ? void 0 : _a2.title) || "";
    };
    const getProductCategoryTitle = (product) => {
      var _a2, _b2;
      return ((_b2 = (_a2 = product == null ? void 0 : product.category) == null ? void 0 : _a2.translation) == null ? void 0 : _b2.title) || "";
    };
    const searchWords = computed(() => {
      return normalizeText(q.value).split(/\s+/u).filter(
        (word) => word.length >= 2
      );
    });
    const getProductSearchValues = (product) => {
      var _a2, _b2, _c2, _d2, _e;
      return [
        product == null ? void 0 : product.url,
        product == null ? void 0 : product.sku,
        product == null ? void 0 : product.vendor_code,
        product == null ? void 0 : product.barcode,
        (_a2 = product == null ? void 0 : product.translation) == null ? void 0 : _a2.title,
        (_b2 = product == null ? void 0 : product.translation) == null ? void 0 : _b2.subtitle,
        (_c2 = product == null ? void 0 : product.translation) == null ? void 0 : _c2.short,
        (_e = (_d2 = product == null ? void 0 : product.category) == null ? void 0 : _d2.translation) == null ? void 0 : _e.title
      ].map(normalizeText);
    };
    const filteredProducts = computed(() => {
      const words = searchWords.value;
      if (!words.length) {
        return productsData.value;
      }
      return productsData.value.filter(
        (product) => {
          const values = getProductSearchValues(
            product
          );
          return words.every(
            (word) => values.some(
              (value) => value.includes(word)
            )
          );
        }
      );
    });
    const compareIdDesc = (a, b) => {
      return normalizeNumber(b == null ? void 0 : b.id) - normalizeNumber(a == null ? void 0 : a.id);
    };
    const compareNumber = (a, b, field, direction = "asc") => {
      const first = normalizeNumber(a == null ? void 0 : a[field]);
      const second = normalizeNumber(b == null ? void 0 : b[field]);
      const result = direction === "desc" ? second - first : first - second;
      return result || compareIdDesc(a, b);
    };
    const compareText = (first, second, direction = "asc") => {
      const a = normalizeText(first);
      const b = normalizeText(second);
      return direction === "desc" ? b.localeCompare(a) : a.localeCompare(b);
    };
    const compareDate = (a, b, field, direction = "asc") => {
      const first = normalizeDate(a == null ? void 0 : a[field]);
      const second = normalizeDate(b == null ? void 0 : b[field]);
      const result = direction === "desc" ? second - first : first - second;
      return result || compareIdDesc(a, b);
    };
    const sortedProducts = computed(() => {
      const list = [
        ...filteredProducts.value
      ];
      return list.sort((a, b) => {
        switch (sort.value) {
          case "idAsc":
            return normalizeNumber(a.id) - normalizeNumber(b.id);
          case "idDesc":
            return normalizeNumber(b.id) - normalizeNumber(a.id);
          case "sortAsc":
            return compareNumber(
              a,
              b,
              "sort",
              "asc"
            );
          case "sortDesc":
            return compareNumber(
              a,
              b,
              "sort",
              "desc"
            );
          case "titleAsc": {
            const result = compareText(
              getProductTitle(a),
              getProductTitle(b),
              "asc"
            );
            return result || compareIdDesc(a, b);
          }
          case "titleDesc": {
            const result = compareText(
              getProductTitle(a),
              getProductTitle(b),
              "desc"
            );
            return result || compareIdDesc(a, b);
          }
          case "priceAsc":
            return compareNumber(
              a,
              b,
              "price",
              "asc"
            );
          case "priceDesc":
            return compareNumber(
              a,
              b,
              "price",
              "desc"
            );
          case "quantityAsc":
            return compareNumber(
              a,
              b,
              "quantity",
              "asc"
            );
          case "quantityDesc":
            return compareNumber(
              a,
              b,
              "quantity",
              "desc"
            );
          case "viewsAsc":
            return compareNumber(
              a,
              b,
              "views",
              "asc"
            );
          case "viewsDesc":
            return compareNumber(
              a,
              b,
              "views",
              "desc"
            );
          case "likesAsc":
            return compareNumber(
              a,
              b,
              "likes_count",
              "asc"
            );
          case "likesDesc":
            return compareNumber(
              a,
              b,
              "likes_count",
              "desc"
            );
          case "ratingAsc":
            return compareNumber(
              a,
              b,
              "rating_avg",
              "asc"
            );
          case "ratingDesc":
            return compareNumber(
              a,
              b,
              "rating_avg",
              "desc"
            );
          case "ratingCountAsc":
            return compareNumber(
              a,
              b,
              "rating_count",
              "asc"
            );
          case "ratingCountDesc":
            return compareNumber(
              a,
              b,
              "rating_count",
              "desc"
            );
          case "reviewsAsc":
            return compareNumber(
              a,
              b,
              "reviews_count",
              "asc"
            );
          case "reviewsDesc":
            return compareNumber(
              a,
              b,
              "reviews_count",
              "desc"
            );
          case "categoryAsc": {
            const result = compareText(
              getProductCategoryTitle(a),
              getProductCategoryTitle(b),
              "asc"
            );
            return result || compareIdDesc(a, b);
          }
          case "categoryDesc": {
            const result = compareText(
              getProductCategoryTitle(a),
              getProductCategoryTitle(b),
              "desc"
            );
            return result || compareIdDesc(a, b);
          }
          case "publishedAtAsc":
            return compareDate(
              a,
              b,
              "published_at",
              "asc"
            );
          case "publishedAtDesc":
            return compareDate(
              a,
              b,
              "published_at",
              "desc"
            );
          case "createdAtAsc":
            return compareDate(
              a,
              b,
              "created_at",
              "asc"
            );
          case "createdAtDesc":
            return compareDate(
              a,
              b,
              "created_at",
              "desc"
            );
          default:
            return compareNumber(
              a,
              b,
              "sort",
              "asc"
            );
        }
      });
    });
    const frontendPaginatedProducts = computed(() => {
      const start = (frontendCurrentPage.value - 1) * perPage.value;
      return sortedProducts.value.slice(
        start,
        start + perPage.value
      );
    });
    watch(
      [
        q,
        sort,
        viewMode
      ],
      () => {
        frontendCurrentPage.value = 1;
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
    const currentPage = computed(() => {
      var _a2, _b2, _c2;
      return Number(
        ((_b2 = (_a2 = props.products) == null ? void 0 : _a2.meta) == null ? void 0 : _b2.current_page) ?? ((_c2 = props.products) == null ? void 0 : _c2.current_page) ?? 1
      ) || 1;
    });
    const lastPage = computed(() => {
      var _a2, _b2, _c2;
      return Number(
        ((_b2 = (_a2 = props.products) == null ? void 0 : _a2.meta) == null ? void 0 : _b2.last_page) ?? ((_c2 = props.products) == null ? void 0 : _c2.last_page) ?? 1
      ) || 1;
    });
    const contentLocale = computed(() => String(props.locale || ""));
    const seoTitle = computed(() => {
      var _a2;
      return ((_a2 = categoryTranslation.value) == null ? void 0 : _a2.meta_title) || categoryTitle.value;
    });
    const seoKeywords = computed(() => {
      var _a2;
      return ((_a2 = categoryTranslation.value) == null ? void 0 : _a2.meta_keywords) || "";
    });
    const seoDescription = computed(() => {
      var _a2;
      return ((_a2 = categoryTranslation.value) == null ? void 0 : _a2.meta_description) || categoryShort.value || categoryDescription.value || "";
    });
    const ogLocale = computed(() => contentLocale.value);
    const dcSubject = computed(() => {
      return seoKeywords.value || categoryTitle.value;
    });
    const seoImage = computed(() => {
      return categoryPrimaryImage.value || "";
    });
    const seoImageAlt = computed(() => {
      return categoryPrimaryImageAlt.value || categoryTitle.value || "";
    });
    const canonicalUrl = computed(() => {
      var _a2;
      const baseUrl = String(
        route(
          "public.marketCategories.show",
          {
            url: (_a2 = category.value) == null ? void 0 : _a2.url
          }
        )
      );
      if (props.useServerProcessing && currentPage.value > 1) {
        return `${baseUrl}?page=${currentPage.value}`;
      }
      return baseUrl;
    });
    const robotsContent = computed(() => {
      const hasSearch = normalizeText(q.value).length > 0;
      const hasAlternativeSort = String(
        sort.value || props.defaultSort
      ) !== String(
        props.defaultSort
      );
      if (props.useServerProcessing && (hasSearch || hasAlternativeSort)) {
        return "noindex, follow, max-image-preview:large";
      }
      return "index, follow, max-image-preview:large";
    });
    const showRoute = () => {
      var _a2;
      return route(
        "public.marketCategories.show",
        {
          url: (_a2 = category.value) == null ? void 0 : _a2.url
        }
      );
    };
    const reloadProducts = (pageNumber = 1) => {
      router.get(
        showRoute(),
        {
          q: q.value || void 0,
          sort: sort.value || props.defaultSort || void 0,
          view: viewMode.value || void 0,
          page: pageNumber
        },
        {
          preserveState: true,
          replace: true,
          preserveScroll: true
        }
      );
    };
    const submitSearch = () => {
      frontendCurrentPage.value = 1;
      if (props.useServerProcessing) {
        reloadProducts(1);
      }
    };
    const resetSearch = () => {
      q.value = "";
      sort.value = props.defaultSort || "";
      frontendCurrentPage.value = 1;
      if (props.useServerProcessing) {
        reloadProducts(1);
      }
    };
    const updateSort = (value) => {
      sort.value = value || props.defaultSort;
      frontendCurrentPage.value = 1;
      if (props.useServerProcessing) {
        reloadProducts(1);
      }
    };
    const updateViewMode = (value) => {
      viewMode.value = value || "grid";
      frontendCurrentPage.value = 1;
      if (props.useServerProcessing) {
        reloadProducts(1);
      }
    };
    const goToPage = (pageNumber) => {
      const value = Number(
        pageNumber
      );
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
      reloadProducts(
        safePage
      );
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
    const displayedProducts = computed(() => {
      return props.useServerProcessing ? productsData.value : frontendPaginatedProducts.value;
    });
    const productListTotalItems = computed(() => {
      return props.useServerProcessing ? Number(props.productsFound ?? 0) : sortedProducts.value.length;
    });
    const productListStartPosition = computed(() => {
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
            if (seoImage.value) {
              _push2(`<meta property="og:image"${ssrRenderAttr("content", seoImage.value)}${_scopeId}>`);
            } else {
              _push2(`<!---->`);
            }
            if (seoImage.value && seoImageAlt.value) {
              _push2(`<meta property="og:image:alt"${ssrRenderAttr("content", seoImageAlt.value)}${_scopeId}>`);
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
            if (seoImage.value && seoImageAlt.value) {
              _push2(`<meta name="twitter:image:alt"${ssrRenderAttr("content", seoImageAlt.value)}${_scopeId}>`);
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
              seoImage.value ? (openBlock(), createBlock("meta", {
                key: 5,
                property: "og:image",
                content: seoImage.value
              }, null, 8, ["content"])) : createCommentVNode("", true),
              seoImage.value && seoImageAlt.value ? (openBlock(), createBlock("meta", {
                key: 6,
                property: "og:image:alt",
                content: seoImageAlt.value
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
                key: 7,
                name: "twitter:description",
                content: seoDescription.value
              }, null, 8, ["content"])) : createCommentVNode("", true),
              seoImage.value ? (openBlock(), createBlock("meta", {
                key: 8,
                name: "twitter:image",
                content: seoImage.value
              }, null, 8, ["content"])) : createCommentVNode("", true),
              seoImage.value && seoImageAlt.value ? (openBlock(), createBlock("meta", {
                key: 9,
                name: "twitter:image:alt",
                content: seoImageAlt.value
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
              contentLocale.value ? (openBlock(), createBlock("meta", {
                key: 12,
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
      _push(ssrRenderComponent(_sfc_main$1, null, {
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
            _push2(`<div class="min-w-0 flex-1 pb-6 slate-1"${_scopeId}><div class="w-full"${_scopeId}><nav class="text-sm" aria-label="Breadcrumb" itemscope itemtype="https://schema.org/BreadcrumbList"${_scopeId}><ol class="flex flex-wrap items-center font-semibold"${_scopeId}><li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem" class="flex items-center"${_scopeId}>`);
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
              href: _ctx.route("public.marketCategories.index"),
              class: "breadcrumb-link hover:underline"
            }, {
              default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                if (_push3) {
                  _push3(`<span itemprop="name"${_scopeId2}>${ssrInterpolate(unref(t)("categories"))}</span>`);
                } else {
                  return [
                    createVNode("span", { itemprop: "name" }, toDisplayString(unref(t)("categories")), 1)
                  ];
                }
              }),
              _: 1
            }, _parent2, _scopeId));
            _push2(`<meta itemprop="position" content="2"${_scopeId}></li>`);
            if (parentCategory.value) {
              _push2(`<li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem" class="flex items-center"${_scopeId}><span class="mx-2 breadcrumbs"${_scopeId}>/</span>`);
              _push2(ssrRenderComponent(unref(Link), {
                itemprop: "item",
                href: _ctx.route(
                  "public.marketCategories.show",
                  { url: parentCategory.value.url }
                ),
                class: "breadcrumb-link hover:underline"
              }, {
                default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                  if (_push3) {
                    _push3(`<span itemprop="name"${_scopeId2}>${ssrInterpolate(parentCategoryTitle.value)}</span>`);
                  } else {
                    return [
                      createVNode("span", { itemprop: "name" }, toDisplayString(parentCategoryTitle.value), 1)
                    ];
                  }
                }),
                _: 1
              }, _parent2, _scopeId));
              _push2(`<meta itemprop="position" content="3"${_scopeId}></li>`);
            } else {
              _push2(`<!---->`);
            }
            _push2(`<li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem" class="flex items-center" aria-current="page"${_scopeId}><span class="mx-2 breadcrumbs"${_scopeId}>/</span><h1 itemprop="name" class="breadcrumbs text-sm font-semibold"${_scopeId}>${ssrInterpolate(categoryTitle.value)}</h1><meta itemprop="item"${ssrRenderAttr("content", canonicalUrl.value)}${_scopeId}><meta itemprop="position"${ssrRenderAttr("content", parentCategory.value ? 4 : 3)}${_scopeId}></li></ol></nav><article class="mt-4" itemscope itemtype="https://schema.org/CollectionPage"${ssrRenderAttr("itemid", canonicalUrl.value)}${_scopeId}><meta itemprop="url"${ssrRenderAttr("content", canonicalUrl.value)}${_scopeId}><meta itemprop="name"${ssrRenderAttr("content", categoryTitle.value)}${_scopeId}>`);
            if (contentLocale.value) {
              _push2(`<meta itemprop="inLanguage"${ssrRenderAttr("content", contentLocale.value)}${_scopeId}>`);
            } else {
              _push2(`<!---->`);
            }
            _push2(`<section class="overflow-hidden rounded-2xl border border-slate-200 bg-white dark:border-slate-700 dark:bg-slate-900"${_scopeId}>`);
            if (hasCategoryImages.value) {
              _push2(`<div class="flex items-center justify-center"${_scopeId}><div class="w-full"${_scopeId}>`);
              _push2(ssrRenderComponent(ImageGalleryMain, {
                images: categoryImages.value,
                alt: categoryTitle.value,
                "rounded-class": "rounded-lg",
                "shadow-class": "shadow-lg shadow-gray-400\n                                                              dark:shadow-gray-700",
                "img-class": "w-full h-full object-cover",
                itemprop: "image",
                loading: "eager",
                fetchpriority: "high"
              }, null, _parent2, _scopeId));
              _push2(`</div></div>`);
            } else {
              _push2(`<!---->`);
            }
            _push2(`<div class="px-4 py-5 sm:px-6"${_scopeId}><div class="flex flex-wrap items-center justify-center gap-3 text-center"${_scopeId}>`);
            if (hasSvgIcon.value) {
              _push2(`<span class="flex shrink-0"${_scopeId}>${category.value.icon ?? ""}</span>`);
            } else {
              _push2(`<!---->`);
            }
            _push2(`<h2 class="text-xl font-semibold text-slate-900 dark:text-slate-100"${_scopeId}>${ssrInterpolate(categoryTitle.value)}</h2></div>`);
            if (categorySubtitle.value) {
              _push2(`<div class="mt-1 text-center text-sm subtitle"${_scopeId}>${ssrInterpolate(categorySubtitle.value)}</div>`);
            } else {
              _push2(`<!---->`);
            }
            _push2(`<div class="mt-3 flex flex-wrap items-center justify-center gap-x-5 gap-y-2 text-sm text-slate-500 dark:text-slate-400"${_scopeId}><span${_scopeId}>${ssrInterpolate(unref(t)("products"))}: ${ssrInterpolate(categoryProductsCount.value)}</span><span${_scopeId}>${ssrInterpolate(unref(t)("subheadings"))}: ${ssrInterpolate(childrenCount.value)}</span><span${_scopeId}>${ssrInterpolate(unref(t)("views"))}: ${ssrInterpolate(categoryViews.value)}</span></div>`);
            if (categoryShort.value) {
              _push2(`<div class="mt-4 text-center text-sm leading-6 text-slate-600 dark:text-slate-300"${_scopeId}>${ssrInterpolate(categoryShort.value)}</div>`);
            } else {
              _push2(`<!---->`);
            }
            _push2(`</div></section>`);
            if (categoryDescription.value) {
              _push2(`<section class="mt-5 rounded-sm border border-slate-200 bg-white p-4 dark:border-slate-700 dark:bg-slate-900"${_scopeId}><div itemprop="description" class="text-sm leading-7 text-slate-700 dark:text-slate-300 whitespace-pre-line"${_scopeId}>${ssrInterpolate(categoryDescription.value)}</div></section>`);
            } else {
              _push2(`<!---->`);
            }
            if (childCategories.value.length) {
              _push2(`<section class="mt-6" aria-labelledby="child-categories-title"${_scopeId}><div class="flex flex-wrap items-center justify-between gap-3"${_scopeId}><h2 id="child-categories-title" class="text-lg font-semibold text-slate-900 dark:text-slate-100"${_scopeId}>${ssrInterpolate(unref(t)("subheadings"))}</h2>`);
              _push2(ssrRenderComponent(_sfc_main$4, {
                modelValue: categoryViewMode.value,
                "onUpdate:modelValue": ($event) => categoryViewMode.value = $event,
                "grid-value": "grid",
                "row-value": "rows"
              }, null, _parent2, _scopeId));
              _push2(`</div><div class="mt-4"${_scopeId}>`);
              if (categoryViewMode.value === "grid") {
                _push2(ssrRenderComponent(_sfc_main$5, {
                  categories: childCategories.value,
                  cols: categoryGridCols.value
                }, null, _parent2, _scopeId));
              } else {
                _push2(ssrRenderComponent(_sfc_main$6, { categories: childCategories.value }, null, _parent2, _scopeId));
              }
              _push2(`</div></section>`);
            } else {
              _push2(`<!---->`);
            }
            _push2(`<section class="mt-6" aria-labelledby="category-products-title"${_scopeId}><h2 id="category-products-title" class="mb-3 text-lg font-semibold text-slate-900 dark:text-slate-100"${_scopeId}>${ssrInterpolate(unref(t)("products"))} — ${ssrInterpolate(categoryTitle.value)}</h2>`);
            if (__props.useServerProcessing) {
              _push2(ssrRenderComponent(_sfc_main$7, {
                modelValue: q.value,
                "onUpdate:modelValue": ($event) => q.value = $event,
                found: __props.productsFound,
                "view-mode": viewMode.value,
                "sort-value": sort.value,
                "sort-options": productSortOptions,
                "default-sort": __props.defaultSort,
                "found-label": unref(t)("products"),
                "search-placeholder": unref(t)("searchByName"),
                onSubmit: submitSearch,
                onReset: resetSearch,
                "onUpdate:viewMode": updateViewMode,
                "onUpdate:sortValue": updateSort
              }, null, _parent2, _scopeId));
            } else {
              _push2(ssrRenderComponent(_sfc_main$8, {
                modelValue: q.value,
                "onUpdate:modelValue": ($event) => q.value = $event,
                found: sortedProducts.value.length,
                "view-mode": viewMode.value,
                "sort-value": sort.value,
                "sort-options": productSortOptions,
                "default-sort": __props.defaultSort,
                "found-label": unref(t)("products"),
                "search-placeholder": unref(t)("searchByName"),
                onReset: resetSearch,
                "onUpdate:viewMode": updateViewMode,
                "onUpdate:sortValue": updateSort
              }, null, _parent2, _scopeId));
            }
            _push2(`<div${_scopeId}></div>`);
            if (displayedProducts.value.length === 0) {
              _push2(`<div class="mt-6 text-center text-slate-700 dark:text-slate-300"${_scopeId}>${ssrInterpolate(unref(t)("noData"))}</div>`);
            } else {
              _push2(`<div${_scopeId}>`);
              if (viewMode.value === "grid") {
                _push2(ssrRenderComponent(_sfc_main$9, {
                  products: displayedProducts.value,
                  cols: productGridCols.value,
                  "start-position": productListStartPosition.value,
                  "total-items": productListTotalItems.value,
                  "schema-property": "mainEntity"
                }, null, _parent2, _scopeId));
              } else {
                _push2(ssrRenderComponent(_sfc_main$a, {
                  products: displayedProducts.value,
                  "start-position": productListStartPosition.value,
                  "total-items": productListTotalItems.value,
                  "schema-property": "mainEntity"
                }, null, _parent2, _scopeId));
              }
              _push2(`</div>`);
            }
            if (__props.useServerProcessing) {
              _push2(ssrRenderComponent(_sfc_main$b, {
                "current-page": currentPage.value,
                "last-page": lastPage.value,
                found: __props.productsFound,
                onPrev: goPrev,
                onNext: goNext,
                onGo: goToPage
              }, null, _parent2, _scopeId));
            } else {
              _push2(ssrRenderComponent(_sfc_main$c, {
                currentPage: frontendCurrentPage.value,
                "onUpdate:currentPage": ($event) => frontendCurrentPage.value = $event,
                "items-per-page": perPage.value,
                "total-items": sortedProducts.value.length
              }, null, _parent2, _scopeId));
            }
            _push2(`</section></article></div></div>`);
            if (showRight.value) {
              _push2(`<aside class="${ssrRenderClass([rightCollapsed.value ? "lg:w-6" : "lg:w-72", "shrink-0 transition-all duration-300"])}"${_scopeId}>`);
              _push2(ssrRenderComponent(_sfc_main$d, {
                collapsed: rightCollapsed.value,
                onCollapsed: ($event) => rightCollapsed.value = $event
              }, null, _parent2, _scopeId));
              _push2(`</aside>`);
            } else {
              _push2(`<!---->`);
            }
            _push2(`</div></div></main>`);
            _push2(ssrRenderComponent(_sfc_main$e, null, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(Progress, null, null, _parent2, _scopeId));
            if (isAdmin.value) {
              _push2(ssrRenderComponent(_sfc_main$f, {
                "setting-key": "publicMarketProductsProcessingMode",
                mode: __props.publicMarketProductsProcessingMode,
                "use-server-processing": __props.useServerProcessing,
                total: __props.productsCount
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
                    createVNode("div", { class: "min-w-0 flex-1 pb-6 slate-1" }, [
                      createVNode("div", { class: "w-full" }, [
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
                              createVNode("span", { class: "mx-2 breadcrumbs" }, "/"),
                              createVNode(unref(Link), {
                                itemprop: "item",
                                href: _ctx.route("public.marketCategories.index"),
                                class: "breadcrumb-link hover:underline"
                              }, {
                                default: withCtx(() => [
                                  createVNode("span", { itemprop: "name" }, toDisplayString(unref(t)("categories")), 1)
                                ]),
                                _: 1
                              }, 8, ["href"]),
                              createVNode("meta", {
                                itemprop: "position",
                                content: "2"
                              })
                            ]),
                            parentCategory.value ? (openBlock(), createBlock("li", {
                              key: 0,
                              itemprop: "itemListElement",
                              itemscope: "",
                              itemtype: "https://schema.org/ListItem",
                              class: "flex items-center"
                            }, [
                              createVNode("span", { class: "mx-2 breadcrumbs" }, "/"),
                              createVNode(unref(Link), {
                                itemprop: "item",
                                href: _ctx.route(
                                  "public.marketCategories.show",
                                  { url: parentCategory.value.url }
                                ),
                                class: "breadcrumb-link hover:underline"
                              }, {
                                default: withCtx(() => [
                                  createVNode("span", { itemprop: "name" }, toDisplayString(parentCategoryTitle.value), 1)
                                ]),
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
                              createVNode("h1", {
                                itemprop: "name",
                                class: "breadcrumbs text-sm font-semibold"
                              }, toDisplayString(categoryTitle.value), 1),
                              createVNode("meta", {
                                itemprop: "item",
                                content: canonicalUrl.value
                              }, null, 8, ["content"]),
                              createVNode("meta", {
                                itemprop: "position",
                                content: parentCategory.value ? 4 : 3
                              }, null, 8, ["content"])
                            ])
                          ])
                        ]),
                        createVNode("article", {
                          class: "mt-4",
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
                            content: categoryTitle.value
                          }, null, 8, ["content"]),
                          contentLocale.value ? (openBlock(), createBlock("meta", {
                            key: 0,
                            itemprop: "inLanguage",
                            content: contentLocale.value
                          }, null, 8, ["content"])) : createCommentVNode("", true),
                          createVNode("section", { class: "overflow-hidden rounded-2xl border border-slate-200 bg-white dark:border-slate-700 dark:bg-slate-900" }, [
                            hasCategoryImages.value ? (openBlock(), createBlock("div", {
                              key: 0,
                              class: "flex items-center justify-center"
                            }, [
                              createVNode("div", { class: "w-full" }, [
                                createVNode(ImageGalleryMain, {
                                  images: categoryImages.value,
                                  alt: categoryTitle.value,
                                  "rounded-class": "rounded-lg",
                                  "shadow-class": "shadow-lg shadow-gray-400\n                                                              dark:shadow-gray-700",
                                  "img-class": "w-full h-full object-cover",
                                  itemprop: "image",
                                  loading: "eager",
                                  fetchpriority: "high"
                                }, null, 8, ["images", "alt"])
                              ])
                            ])) : createCommentVNode("", true),
                            createVNode("div", { class: "px-4 py-5 sm:px-6" }, [
                              createVNode("div", { class: "flex flex-wrap items-center justify-center gap-3 text-center" }, [
                                hasSvgIcon.value ? (openBlock(), createBlock("span", {
                                  key: 0,
                                  class: "flex shrink-0",
                                  innerHTML: category.value.icon
                                }, null, 8, ["innerHTML"])) : createCommentVNode("", true),
                                createVNode("h2", { class: "text-xl font-semibold text-slate-900 dark:text-slate-100" }, toDisplayString(categoryTitle.value), 1)
                              ]),
                              categorySubtitle.value ? (openBlock(), createBlock("div", {
                                key: 0,
                                class: "mt-1 text-center text-sm subtitle"
                              }, toDisplayString(categorySubtitle.value), 1)) : createCommentVNode("", true),
                              createVNode("div", { class: "mt-3 flex flex-wrap items-center justify-center gap-x-5 gap-y-2 text-sm text-slate-500 dark:text-slate-400" }, [
                                createVNode("span", null, toDisplayString(unref(t)("products")) + ": " + toDisplayString(categoryProductsCount.value), 1),
                                createVNode("span", null, toDisplayString(unref(t)("subheadings")) + ": " + toDisplayString(childrenCount.value), 1),
                                createVNode("span", null, toDisplayString(unref(t)("views")) + ": " + toDisplayString(categoryViews.value), 1)
                              ]),
                              categoryShort.value ? (openBlock(), createBlock("div", {
                                key: 1,
                                class: "mt-4 text-center text-sm leading-6 text-slate-600 dark:text-slate-300"
                              }, toDisplayString(categoryShort.value), 1)) : createCommentVNode("", true)
                            ])
                          ]),
                          categoryDescription.value ? (openBlock(), createBlock("section", {
                            key: 1,
                            class: "mt-5 rounded-sm border border-slate-200 bg-white p-4 dark:border-slate-700 dark:bg-slate-900"
                          }, [
                            createVNode("div", {
                              itemprop: "description",
                              class: "text-sm leading-7 text-slate-700 dark:text-slate-300 whitespace-pre-line"
                            }, toDisplayString(categoryDescription.value), 1)
                          ])) : createCommentVNode("", true),
                          childCategories.value.length ? (openBlock(), createBlock("section", {
                            key: 2,
                            class: "mt-6",
                            "aria-labelledby": "child-categories-title"
                          }, [
                            createVNode("div", { class: "flex flex-wrap items-center justify-between gap-3" }, [
                              createVNode("h2", {
                                id: "child-categories-title",
                                class: "text-lg font-semibold text-slate-900 dark:text-slate-100"
                              }, toDisplayString(unref(t)("subheadings")), 1),
                              createVNode(_sfc_main$4, {
                                modelValue: categoryViewMode.value,
                                "onUpdate:modelValue": ($event) => categoryViewMode.value = $event,
                                "grid-value": "grid",
                                "row-value": "rows"
                              }, null, 8, ["modelValue", "onUpdate:modelValue"])
                            ]),
                            createVNode("div", { class: "mt-4" }, [
                              categoryViewMode.value === "grid" ? (openBlock(), createBlock(_sfc_main$5, {
                                key: 0,
                                categories: childCategories.value,
                                cols: categoryGridCols.value
                              }, null, 8, ["categories", "cols"])) : (openBlock(), createBlock(_sfc_main$6, {
                                key: 1,
                                categories: childCategories.value
                              }, null, 8, ["categories"]))
                            ])
                          ])) : createCommentVNode("", true),
                          createVNode("section", {
                            class: "mt-6",
                            "aria-labelledby": "category-products-title"
                          }, [
                            createVNode("h2", {
                              id: "category-products-title",
                              class: "mb-3 text-lg font-semibold text-slate-900 dark:text-slate-100"
                            }, toDisplayString(unref(t)("products")) + " — " + toDisplayString(categoryTitle.value), 1),
                            __props.useServerProcessing ? (openBlock(), createBlock(_sfc_main$7, {
                              key: 0,
                              modelValue: q.value,
                              "onUpdate:modelValue": ($event) => q.value = $event,
                              found: __props.productsFound,
                              "view-mode": viewMode.value,
                              "sort-value": sort.value,
                              "sort-options": productSortOptions,
                              "default-sort": __props.defaultSort,
                              "found-label": unref(t)("products"),
                              "search-placeholder": unref(t)("searchByName"),
                              onSubmit: submitSearch,
                              onReset: resetSearch,
                              "onUpdate:viewMode": updateViewMode,
                              "onUpdate:sortValue": updateSort
                            }, null, 8, ["modelValue", "onUpdate:modelValue", "found", "view-mode", "sort-value", "default-sort", "found-label", "search-placeholder"])) : (openBlock(), createBlock(_sfc_main$8, {
                              key: 1,
                              modelValue: q.value,
                              "onUpdate:modelValue": ($event) => q.value = $event,
                              found: sortedProducts.value.length,
                              "view-mode": viewMode.value,
                              "sort-value": sort.value,
                              "sort-options": productSortOptions,
                              "default-sort": __props.defaultSort,
                              "found-label": unref(t)("products"),
                              "search-placeholder": unref(t)("searchByName"),
                              onReset: resetSearch,
                              "onUpdate:viewMode": updateViewMode,
                              "onUpdate:sortValue": updateSort
                            }, null, 8, ["modelValue", "onUpdate:modelValue", "found", "view-mode", "sort-value", "default-sort", "found-label", "search-placeholder"])),
                            createVNode("div", {
                              ref_key: "scrollTarget",
                              ref: scrollTarget
                            }, null, 512),
                            displayedProducts.value.length === 0 ? (openBlock(), createBlock("div", {
                              key: 2,
                              class: "mt-6 text-center text-slate-700 dark:text-slate-300"
                            }, toDisplayString(unref(t)("noData")), 1)) : (openBlock(), createBlock("div", { key: 3 }, [
                              viewMode.value === "grid" ? (openBlock(), createBlock(_sfc_main$9, {
                                key: 0,
                                products: displayedProducts.value,
                                cols: productGridCols.value,
                                "start-position": productListStartPosition.value,
                                "total-items": productListTotalItems.value,
                                "schema-property": "mainEntity"
                              }, null, 8, ["products", "cols", "start-position", "total-items"])) : (openBlock(), createBlock(_sfc_main$a, {
                                key: 1,
                                products: displayedProducts.value,
                                "start-position": productListStartPosition.value,
                                "total-items": productListTotalItems.value,
                                "schema-property": "mainEntity"
                              }, null, 8, ["products", "start-position", "total-items"]))
                            ])),
                            __props.useServerProcessing ? (openBlock(), createBlock(_sfc_main$b, {
                              key: 4,
                              "current-page": currentPage.value,
                              "last-page": lastPage.value,
                              found: __props.productsFound,
                              onPrev: goPrev,
                              onNext: goNext,
                              onGo: goToPage
                            }, null, 8, ["current-page", "last-page", "found"])) : (openBlock(), createBlock(_sfc_main$c, {
                              key: 5,
                              currentPage: frontendCurrentPage.value,
                              "onUpdate:currentPage": ($event) => frontendCurrentPage.value = $event,
                              "items-per-page": perPage.value,
                              "total-items": sortedProducts.value.length
                            }, null, 8, ["currentPage", "onUpdate:currentPage", "items-per-page", "total-items"]))
                          ])
                        ], 8, ["itemid"])
                      ])
                    ]),
                    showRight.value ? (openBlock(), createBlock("aside", {
                      key: 1,
                      class: ["shrink-0 transition-all duration-300", rightCollapsed.value ? "lg:w-6" : "lg:w-72"]
                    }, [
                      createVNode(_sfc_main$d, {
                        collapsed: rightCollapsed.value,
                        onCollapsed: ($event) => rightCollapsed.value = $event
                      }, null, 8, ["collapsed", "onCollapsed"])
                    ], 2)) : createCommentVNode("", true)
                  ])
                ])
              ]),
              createVNode(_sfc_main$e),
              createVNode(Progress),
              isAdmin.value ? (openBlock(), createBlock(_sfc_main$f, {
                key: 0,
                "setting-key": "publicMarketProductsProcessingMode",
                mode: __props.publicMarketProductsProcessingMode,
                "use-server-processing": __props.useServerProcessing,
                total: __props.productsCount
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
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Pages/Public/Default/Market/MarketCategories/Show.vue");
  return _sfc_setup ? _sfc_setup(props, ctx) : void 0;
};
export {
  _sfc_main as default
};
