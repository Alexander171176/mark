import { computed, ref, watch, unref, withCtx, createVNode, createBlock, createCommentVNode, toDisplayString, openBlock, useSSRContext } from "vue";
import { ssrRenderComponent, ssrInterpolate, ssrRenderAttr, ssrRenderClass, ssrRenderStyle } from "vue/server-renderer";
import { usePage, Head, Link, router } from "@inertiajs/vue3";
import { useI18n } from "vue-i18n";
import { u as useSmoothScrollTo } from "./useSmoothScrollTo-g9G3kyDv.js";
import { _ as _sfc_main$1 } from "./DefaultLayout-6zxGxPtK.js";
import { _ as _sfc_main$2, a as _sfc_main$b, P as Progress } from "./Progress-CyfOKcqP.js";
import { _ as _sfc_main$3, a as _sfc_main$a } from "./RightSidebarMarket-DpLr4Pjm.js";
import { _ as _sfc_main$6, a as _sfc_main$7 } from "./MarketProductRows-D4G6aZpS.js";
import { _ as _sfc_main$8, a as _sfc_main$9, b as _sfc_main$c } from "./PublicAdminBottomPanel-BhLEQMoJ.js";
import { _ as _sfc_main$4 } from "./EntityPageToolbar-DT32FtSd.js";
import { _ as _sfc_main$5 } from "./FrontendEntityPageToolbar-Xr_r9znL.js";
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
import "./ViewModeToggle-DMCnQ0wo.js";
const LEFT_SIDEBAR_KEY = "public_left_sidebar_collapsed";
const RIGHT_SIDEBAR_KEY = "public_right_sidebar_collapsed";
const VIEW_KEY = "public_market_products_view";
const _sfc_main = {
  __name: "Show",
  __ssrInlineRender: true,
  props: {
    /** Активная локаль от backend */
    locale: {
      type: String,
      default: ""
    },
    /** Текущий Public-тег */
    tag: {
      type: Object,
      default: () => ({})
    },
    /** Связанные Public-товары */
    products: {
      type: [Array, Object],
      default: () => []
    },
    /** Общее количество Public-товаров тега */
    productsCount: {
      type: Number,
      default: 0
    },
    /** Количество найденных товаров */
    productsFound: {
      type: Number,
      default: 0
    },
    /** Режим обработки товаров */
    useServerProcessing: {
      type: Boolean,
      default: false
    },
    /** Настройка server / frontend / auto */
    publicMarketProductsProcessingMode: {
      type: String,
      default: "server"
    },
    /** Сортировка Public по умолчанию */
    defaultSort: {
      type: String,
      default: "sortAsc"
    },
    /** Текущие параметры списка */
    filters: {
      type: Object,
      default: () => ({})
    },
    /** Дерево категорий */
    categoryTree: {
      type: Array,
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
    const categoryTree = computed(() => {
      return Array.isArray(props.categoryTree) ? props.categoryTree : [];
    });
    const tag = computed(() => {
      return props.tag || {};
    });
    const tagTranslation = computed(() => {
      var _a2;
      return ((_a2 = tag.value) == null ? void 0 : _a2.translation) || {};
    });
    const tagTitle = computed(() => {
      var _a2;
      return ((_a2 = tagTranslation.value) == null ? void 0 : _a2.title) || "";
    });
    const tagSubtitle = computed(() => {
      var _a2;
      return ((_a2 = tagTranslation.value) == null ? void 0 : _a2.subtitle) || "";
    });
    const tagShort = computed(() => {
      var _a2;
      return ((_a2 = tagTranslation.value) == null ? void 0 : _a2.short) || "";
    });
    const tagDescription = computed(() => {
      var _a2;
      return ((_a2 = tagTranslation.value) == null ? void 0 : _a2.description) || tagShort.value || "";
    });
    const tagProductsCount = computed(() => {
      return Number(
        props.productsCount ?? 0
      );
    });
    const tagViews = computed(() => {
      var _a2;
      return Number(((_a2 = tag.value) == null ? void 0 : _a2.views) ?? 0);
    });
    const hasSvgIcon = computed(() => {
      var _a2;
      if (!((_a2 = tag.value) == null ? void 0 : _a2.icon)) {
        return false;
      }
      return /^\s*<svg[\s\S]*<\/svg>\s*$/i.test(
        String(tag.value.icon)
      );
    });
    const productsData = computed(() => {
      var _a2;
      if (Array.isArray(props.products)) {
        return props.products;
      }
      if (Array.isArray((_a2 = props.products) == null ? void 0 : _a2.data)) {
        return props.products.data;
      }
      return [];
    });
    const normalizeList = (value) => {
      if (Array.isArray(value)) {
        return value;
      }
      if (Array.isArray(value == null ? void 0 : value.data)) {
        return value.data;
      }
      return [];
    };
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
    watch(
      viewMode,
      (value) => {
        localStorage.setItem(
          VIEW_KEY,
          value
        );
      }
    );
    const perPage = computed(() => {
      var _a2;
      const value = Number(
        (_a2 = props.filters) == null ? void 0 : _a2.per_page
      );
      return Number.isFinite(value) && value > 0 ? value : 12;
    });
    const productSortOptions = [
      {
        value: "idDesc",
        label: t("idDesc")
      },
      {
        value: "idAsc",
        label: t("idAsc")
      },
      {
        value: "sortAsc",
        label: `${t("sortNumber")} 0→9`
      },
      {
        value: "sortDesc",
        label: `${t("sortNumber")} 9→0`
      },
      {
        value: "titleAsc",
        label: `${t("title")} A→Z`
      },
      {
        value: "titleDesc",
        label: `${t("title")} Z→A`
      },
      {
        value: "priceAsc",
        label: `${t("price")} 0→9`
      },
      {
        value: "priceDesc",
        label: `${t("price")} 9→0`
      },
      {
        value: "quantityAsc",
        label: `${t("quantity")} 0→9`
      },
      {
        value: "quantityDesc",
        label: `${t("quantity")} 9→0`
      },
      {
        value: "viewsDesc",
        label: `${t("views")} 9→0`
      },
      {
        value: "viewsAsc",
        label: `${t("views")} 0→9`
      },
      {
        value: "likesDesc",
        label: `${t("likes")} 9→0`
      },
      {
        value: "likesAsc",
        label: `${t("likes")} 0→9`
      },
      {
        value: "ratingDesc",
        label: `${t("rating")} 9→0`
      },
      {
        value: "ratingAsc",
        label: `${t("rating")} 0→9`
      },
      {
        value: "ratingCountDesc",
        label: `${t("ratingCount")} 9→0`
      },
      {
        value: "ratingCountAsc",
        label: `${t("ratingCount")} 0→9`
      },
      {
        value: "reviewsDesc",
        label: `${t("reviews")} 9→0`
      },
      {
        value: "reviewsAsc",
        label: `${t("reviews")} 0→9`
      },
      {
        value: "brandAsc",
        label: `${t("brand")} A→Z`
      },
      {
        value: "brandDesc",
        label: `${t("brand")} Z→A`
      },
      {
        value: "publishedAtDesc",
        label: `${t("publishedAt")} ↓`
      },
      {
        value: "publishedAtAsc",
        label: `${t("publishedAt")} ↑`
      },
      {
        value: "createdAtDesc",
        label: `${t("createdAt")} ↓`
      },
      {
        value: "createdAtAsc",
        label: `${t("createdAt")} ↑`
      }
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
    const getBrandTitle = (product) => {
      var _a2, _b2;
      return ((_b2 = (_a2 = product == null ? void 0 : product.brand) == null ? void 0 : _a2.translation) == null ? void 0 : _b2.title) || "";
    };
    const searchWords = computed(() => {
      return normalizeText(q.value).split(/\s+/u).filter(
        (word) => word.length >= 2
      );
    });
    const getProductSearchValues = (product) => {
      var _a2, _b2, _c2, _d2, _e;
      return [
        product == null ? void 0 : product.id,
        product == null ? void 0 : product.url,
        product == null ? void 0 : product.sku,
        product == null ? void 0 : product.vendor_code,
        product == null ? void 0 : product.barcode,
        (_a2 = product == null ? void 0 : product.translation) == null ? void 0 : _a2.title,
        (_b2 = product == null ? void 0 : product.translation) == null ? void 0 : _b2.subtitle,
        (_c2 = product == null ? void 0 : product.translation) == null ? void 0 : _c2.short,
        (_e = (_d2 = product == null ? void 0 : product.brand) == null ? void 0 : _d2.translation) == null ? void 0 : _e.title
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
          case "brandAsc": {
            const result = compareText(
              getBrandTitle(a),
              getBrandTitle(b),
              "asc"
            );
            return result || compareIdDesc(a, b);
          }
          case "brandDesc": {
            const result = compareText(
              getBrandTitle(a),
              getBrandTitle(b),
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
    const contentLocale = computed(() => {
      return String(
        props.locale || ""
      );
    });
    const seoTitle = computed(() => {
      var _a2;
      return ((_a2 = tagTranslation.value) == null ? void 0 : _a2.meta_title) || tagTitle.value;
    });
    const seoKeywords = computed(() => {
      var _a2;
      return ((_a2 = tagTranslation.value) == null ? void 0 : _a2.meta_keywords) || "";
    });
    const seoDescription = computed(() => {
      var _a2;
      return ((_a2 = tagTranslation.value) == null ? void 0 : _a2.meta_desc) || tagShort.value || tagDescription.value || "";
    });
    const ogLocale = computed(() => {
      return contentLocale.value;
    });
    const dcSubject = computed(() => {
      return seoKeywords.value || tagTitle.value;
    });
    const seoPreview = computed(() => {
      for (const product of productsData.value) {
        const images = normalizeList(
          product == null ? void 0 : product.images
        );
        const image = images[0];
        const url = (image == null ? void 0 : image.webp_url) || (image == null ? void 0 : image.image_url) || (image == null ? void 0 : image.thumb_url) || (image == null ? void 0 : image.url) || "";
        if (url) {
          return {
            url,
            alt: getProductTitle(product) || tagTitle.value
          };
        }
      }
      return {
        url: "",
        alt: ""
      };
    });
    const seoImage = computed(() => {
      return seoPreview.value.url;
    });
    const seoImageAlt = computed(() => {
      return seoPreview.value.alt;
    });
    const canonicalUrl = computed(() => {
      var _a2;
      const baseUrl = String(
        route(
          "public.marketTags.show",
          {
            url: (_a2 = tag.value) == null ? void 0 : _a2.url
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
        "public.marketTags.show",
        {
          url: (_a2 = tag.value) == null ? void 0 : _a2.url
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
    const productListStartPosition = computed(() => {
      const pageNumber = props.useServerProcessing ? currentPage.value : frontendCurrentPage.value;
      return Math.max(
        0,
        (pageNumber - 1) * perPage.value
      );
    });
    return (_ctx, _push, _parent, _attrs) => {
      _push(`<!--[-->`);
      _push(ssrRenderComponent(unref(Head), null, {
        default: withCtx((_, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(`<title${_scopeId}>
            ${ssrInterpolate(seoTitle.value)}
        </title><meta name="title"${ssrRenderAttr("content", seoTitle.value)}${_scopeId}>`);
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
              createVNode("title", null, "\n            " + toDisplayString(seoTitle.value) + "\n        ", 1),
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
                key: 2,
                property: "og:description",
                content: seoDescription.value
              }, null, 8, ["content"])) : createCommentVNode("", true),
              createVNode("meta", {
                property: "og:url",
                content: canonicalUrl.value
              }, null, 8, ["content"]),
              ogLocale.value ? (openBlock(), createBlock("meta", {
                key: 3,
                property: "og:locale",
                content: ogLocale.value
              }, null, 8, ["content"])) : createCommentVNode("", true),
              seoImage.value ? (openBlock(), createBlock("meta", {
                key: 4,
                property: "og:image",
                content: seoImage.value
              }, null, 8, ["content"])) : createCommentVNode("", true),
              seoImage.value && seoImageAlt.value ? (openBlock(), createBlock("meta", {
                key: 5,
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
                key: 6,
                name: "twitter:description",
                content: seoDescription.value
              }, null, 8, ["content"])) : createCommentVNode("", true),
              seoImage.value ? (openBlock(), createBlock("meta", {
                key: 7,
                name: "twitter:image",
                content: seoImage.value
              }, null, 8, ["content"])) : createCommentVNode("", true),
              seoImage.value && seoImageAlt.value ? (openBlock(), createBlock("meta", {
                key: 8,
                name: "twitter:image:alt",
                content: seoImageAlt.value
              }, null, 8, ["content"])) : createCommentVNode("", true),
              createVNode("meta", {
                name: "DC.title",
                content: seoTitle.value
              }, null, 8, ["content"]),
              seoDescription.value ? (openBlock(), createBlock("meta", {
                key: 9,
                name: "DC.description",
                content: seoDescription.value
              }, null, 8, ["content"])) : createCommentVNode("", true),
              dcSubject.value ? (openBlock(), createBlock("meta", {
                key: 10,
                name: "DC.subject",
                content: dcSubject.value
              }, null, 8, ["content"])) : createCommentVNode("", true),
              contentLocale.value ? (openBlock(), createBlock("meta", {
                key: 11,
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
            _push2(`<div class="min-w-0 flex-1 pb-6 slate-1"${_scopeId}><div class="w-full"${_scopeId}><article itemscope itemtype="https://schema.org/CollectionPage"${ssrRenderAttr("itemid", canonicalUrl.value)}${_scopeId}><meta itemprop="url"${ssrRenderAttr("content", canonicalUrl.value)}${_scopeId}><meta itemprop="name"${ssrRenderAttr("content", tagTitle.value)}${_scopeId}>`);
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
            if (contentLocale.value) {
              _push2(`<meta itemprop="inLanguage"${ssrRenderAttr("content", contentLocale.value)}${_scopeId}>`);
            } else {
              _push2(`<!---->`);
            }
            _push2(`<nav class="text-sm" aria-label="Breadcrumb" itemscope itemtype="https://schema.org/BreadcrumbList"${_scopeId}><ol class="flex flex-wrap items-center font-semibold"${_scopeId}><li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem" class="flex items-center"${_scopeId}>`);
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
            _push2(`<meta itemprop="position" content="2"${_scopeId}></li><li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem" class="flex items-center" aria-current="page"${_scopeId}><span class="mx-2 breadcrumbs"${_scopeId}> / </span><div class="flex items-center gap-1.5"${_scopeId}>`);
            if (hasSvgIcon.value) {
              _push2(`<span class="flex shrink-0"${_scopeId}>${tag.value.icon ?? ""}</span>`);
            } else {
              _push2(`<!---->`);
            }
            _push2(`<h1 itemprop="name" class="breadcrumbs text-sm font-semibold" style="${ssrRenderStyle(tag.value.color ? {
              color: tag.value.color
            } : void 0)}"${_scopeId}> #${ssrInterpolate(tagTitle.value)}</h1></div><meta itemprop="item"${ssrRenderAttr("content", canonicalUrl.value)}${_scopeId}><meta itemprop="position" content="3"${_scopeId}></li></ol></nav><section class="mt-4 mb-3" itemscope itemtype="https://schema.org/DefinedTerm"${_scopeId}><meta itemprop="url"${ssrRenderAttr("content", canonicalUrl.value)}${_scopeId}><meta itemprop="name"${ssrRenderAttr("content", tagTitle.value)}${_scopeId}><div class="flex flex-wrap items-center justify-center gap-x-5 gap-y-2"${_scopeId}><div${ssrRenderAttr("title", unref(t)("products"))} class="flex items-center justify-center gap-1"${_scopeId}><svg class="h-4 w-4 text-sky-600/85 dark:text-sky-200/85" viewBox="0 0 24 24" fill="currentColor"${_scopeId}><path d="M21 8.5 12 3 3 8.5V19l9 5 9-5V8.5ZM12 5.3l5.8 3.5-2.2 1.3L10 6.8 12 5.3Zm-3.8 2.6 5.8 3.5-2 1.2-5.8-3.5 2-1.2ZM5 10.6l6 3.6v7L5 17.8v-7.2Zm8 10.6v-7l6-3.6v7.2l-6 3.4Z"${_scopeId}></path></svg><span class="text-sm text-gray-500"${_scopeId}>${ssrInterpolate(tagProductsCount.value)}</span></div><div${ssrRenderAttr("title", unref(t)("views"))} class="flex items-center justify-center gap-1"${_scopeId}><svg class="h-4 w-4 text-slate-600/85 dark:text-slate-200/85" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512" fill="currentColor"${_scopeId}><path d="M569.354 231.631C512.97 135.949 407.81 72 288 72 168.14 72 63.004 135.994 6.646 231.631a47.999 47.999 0 0 0 0 48.739C63.031 376.051 168.19 440 288 440c119.86 0 224.996-63.994 281.354-159.631a47.997 47.997 0 0 0 0-48.738zM288 392c-102.556 0-192.091-54.701-240-136 44.157-74.933 123.677-127.27 216.162-135.007C273.958 131.078 280 144.83 280 160c0 30.928-25.072 56-56 56s-56-25.072-56-56l.001-.042C157.794 179.043 152 200.844 152 224c0 75.111 60.889 136 136 136s136-60.889 136-136c0-31.031-10.4-59.629-27.895-82.515C451.704 164.638 498.009 205.106 528 256c-47.908 81.299-137.444 136-240 136z"${_scopeId}></path></svg><span class="text-sm text-gray-500"${_scopeId}>${ssrInterpolate(tagViews.value)}</span></div></div>`);
            if (tagSubtitle.value) {
              _push2(`<div class="mt-2 text-sm subtitle text-center"${_scopeId}>${ssrInterpolate(tagSubtitle.value)}</div>`);
            } else {
              _push2(`<!---->`);
            }
            if (tagDescription.value) {
              _push2(`<div itemprop="description" class="mt-1 mb-3 text-sm subtitle text-center"${_scopeId}>${ssrInterpolate(tagDescription.value)}</div>`);
            } else {
              _push2(`<!---->`);
            }
            _push2(`</section>`);
            if (__props.useServerProcessing) {
              _push2(ssrRenderComponent(_sfc_main$4, {
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
              _push2(ssrRenderComponent(_sfc_main$5, {
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
                _push2(ssrRenderComponent(_sfc_main$6, {
                  products: displayedProducts.value,
                  cols: productGridCols.value,
                  "start-position": productListStartPosition.value
                }, null, _parent2, _scopeId));
              } else {
                _push2(ssrRenderComponent(_sfc_main$7, {
                  products: displayedProducts.value,
                  "start-position": productListStartPosition.value
                }, null, _parent2, _scopeId));
              }
              _push2(`</div>`);
            }
            if (__props.useServerProcessing) {
              _push2(ssrRenderComponent(_sfc_main$8, {
                "current-page": currentPage.value,
                "last-page": lastPage.value,
                found: __props.productsFound,
                onPrev: goPrev,
                onNext: goNext,
                onGo: goToPage
              }, null, _parent2, _scopeId));
            } else {
              _push2(ssrRenderComponent(_sfc_main$9, {
                currentPage: frontendCurrentPage.value,
                "onUpdate:currentPage": ($event) => frontendCurrentPage.value = $event,
                "items-per-page": perPage.value,
                "total-items": sortedProducts.value.length
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
                        createVNode("article", {
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
                            content: tagTitle.value
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
                          contentLocale.value ? (openBlock(), createBlock("meta", {
                            key: 2,
                            itemprop: "inLanguage",
                            content: contentLocale.value
                          }, null, 8, ["content"])) : createCommentVNode("", true),
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
                              createVNode("li", {
                                itemprop: "itemListElement",
                                itemscope: "",
                                itemtype: "https://schema.org/ListItem",
                                class: "flex items-center",
                                "aria-current": "page"
                              }, [
                                createVNode("span", { class: "mx-2 breadcrumbs" }, " / "),
                                createVNode("div", { class: "flex items-center gap-1.5" }, [
                                  hasSvgIcon.value ? (openBlock(), createBlock("span", {
                                    key: 0,
                                    class: "flex shrink-0",
                                    innerHTML: tag.value.icon
                                  }, null, 8, ["innerHTML"])) : createCommentVNode("", true),
                                  createVNode("h1", {
                                    itemprop: "name",
                                    class: "breadcrumbs text-sm font-semibold",
                                    style: tag.value.color ? {
                                      color: tag.value.color
                                    } : void 0
                                  }, " #" + toDisplayString(tagTitle.value), 5)
                                ]),
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
                          createVNode("section", {
                            class: "mt-4 mb-3",
                            itemscope: "",
                            itemtype: "https://schema.org/DefinedTerm"
                          }, [
                            createVNode("meta", {
                              itemprop: "url",
                              content: canonicalUrl.value
                            }, null, 8, ["content"]),
                            createVNode("meta", {
                              itemprop: "name",
                              content: tagTitle.value
                            }, null, 8, ["content"]),
                            createVNode("div", { class: "flex flex-wrap items-center justify-center gap-x-5 gap-y-2" }, [
                              createVNode("div", {
                                title: unref(t)("products"),
                                class: "flex items-center justify-center gap-1"
                              }, [
                                (openBlock(), createBlock("svg", {
                                  class: "h-4 w-4 text-sky-600/85 dark:text-sky-200/85",
                                  viewBox: "0 0 24 24",
                                  fill: "currentColor"
                                }, [
                                  createVNode("path", { d: "M21 8.5 12 3 3 8.5V19l9 5 9-5V8.5ZM12 5.3l5.8 3.5-2.2 1.3L10 6.8 12 5.3Zm-3.8 2.6 5.8 3.5-2 1.2-5.8-3.5 2-1.2ZM5 10.6l6 3.6v7L5 17.8v-7.2Zm8 10.6v-7l6-3.6v7.2l-6 3.4Z" })
                                ])),
                                createVNode("span", { class: "text-sm text-gray-500" }, toDisplayString(tagProductsCount.value), 1)
                              ], 8, ["title"]),
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
                                createVNode("span", { class: "text-sm text-gray-500" }, toDisplayString(tagViews.value), 1)
                              ], 8, ["title"])
                            ]),
                            tagSubtitle.value ? (openBlock(), createBlock("div", {
                              key: 0,
                              class: "mt-2 text-sm subtitle text-center"
                            }, toDisplayString(tagSubtitle.value), 1)) : createCommentVNode("", true),
                            tagDescription.value ? (openBlock(), createBlock("div", {
                              key: 1,
                              itemprop: "description",
                              class: "mt-1 mb-3 text-sm subtitle text-center"
                            }, toDisplayString(tagDescription.value), 1)) : createCommentVNode("", true)
                          ]),
                          __props.useServerProcessing ? (openBlock(), createBlock(_sfc_main$4, {
                            key: 3,
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
                          }, null, 8, ["modelValue", "onUpdate:modelValue", "found", "view-mode", "sort-value", "default-sort", "found-label", "search-placeholder"])) : (openBlock(), createBlock(_sfc_main$5, {
                            key: 4,
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
                            key: 5,
                            class: "mt-6 text-center text-slate-700 dark:text-slate-300"
                          }, toDisplayString(unref(t)("noData")), 1)) : (openBlock(), createBlock("div", { key: 6 }, [
                            viewMode.value === "grid" ? (openBlock(), createBlock(_sfc_main$6, {
                              key: 0,
                              products: displayedProducts.value,
                              cols: productGridCols.value,
                              "start-position": productListStartPosition.value
                            }, null, 8, ["products", "cols", "start-position"])) : (openBlock(), createBlock(_sfc_main$7, {
                              key: 1,
                              products: displayedProducts.value,
                              "start-position": productListStartPosition.value
                            }, null, 8, ["products", "start-position"]))
                          ])),
                          __props.useServerProcessing ? (openBlock(), createBlock(_sfc_main$8, {
                            key: 7,
                            "current-page": currentPage.value,
                            "last-page": lastPage.value,
                            found: __props.productsFound,
                            onPrev: goPrev,
                            onNext: goNext,
                            onGo: goToPage
                          }, null, 8, ["current-page", "last-page", "found"])) : (openBlock(), createBlock(_sfc_main$9, {
                            key: 8,
                            currentPage: frontendCurrentPage.value,
                            "onUpdate:currentPage": ($event) => frontendCurrentPage.value = $event,
                            "items-per-page": perPage.value,
                            "total-items": sortedProducts.value.length
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
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Pages/Public/Default/Market/MarketTags/Show.vue");
  return _sfc_setup ? _sfc_setup(props, ctx) : void 0;
};
export {
  _sfc_main as default
};
