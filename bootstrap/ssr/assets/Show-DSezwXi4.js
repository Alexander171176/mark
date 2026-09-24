import { mergeProps, unref, useSSRContext, computed, ref, withCtx, createTextVNode, toDisplayString, createBlock, openBlock, createVNode, createCommentVNode } from "vue";
import { ssrRenderAttrs, ssrInterpolate, ssrRenderAttr, ssrRenderList, ssrRenderComponent } from "vue/server-renderer";
import { router } from "@inertiajs/vue3";
import { useI18n } from "vue-i18n";
import { A as AdminLayout } from "./AdminLayout-03yd3nxC.js";
import { T as TitlePage } from "./TitlePage-CBZZ8YZB.js";
import { _ as _sfc_main$4, a as _sfc_main$7 } from "./ServerSearchInput-CxQUCmzV.js";
import { _ as _sfc_main$6 } from "./CountTable-p8tyXGUL.js";
import { _ as _sfc_main$3 } from "./DefaultButton-DMDyGacf.js";
import { _ as _sfc_main$5 } from "./ItemsPerPageSelect-DezMefMH.js";
import "vue-toastification";
import "./LocaleSelectOption-BeLdazeX.js";
import "./ResponsiveNavLink-gtte0z5g.js";
import "./_plugin-vue_export-helper-1tPrXgE0.js";
import "./ThemeToggle-DA16u1ft.js";
import "@vueuse/core";
import "vuedraggable";
import "./ScrollButtons-2xyFJfJ4.js";
import "@fortawesome/vue-fontawesome";
import "@fortawesome/fontawesome-svg-core";
import "@fortawesome/free-solid-svg-icons";
import "@inertiajs/inertia";
import "vue-smooth-dnd";
const _sfc_main$2 = {
  __name: "SortSelect",
  __ssrInlineRender: true,
  props: {
    sortParam: {
      type: String,
      default: "viewedAtDesc"
    }
  },
  emits: [
    "update:sortParam"
  ],
  setup(__props, { emit: __emit }) {
    const { t } = useI18n();
    return (_ctx, _push, _parent, _attrs) => {
      _push(`<div${ssrRenderAttrs(mergeProps({ class: "flex justify-center items-center h-fit my-2" }, _attrs))}><label for="sortParam" class="hidden lg:block sm:mr-2 tracking-wider text-sm font-semibold text-slate-600 dark:text-slate-100">${ssrInterpolate(unref(t)("sort"))}</label><select id="sortParam"${ssrRenderAttr("value", __props.sortParam)} class="w-56 px-3 py-0.5 form-select bg-white dark:bg-gray-200 text-gray-600 dark:text-gray-900 border border-slate-400 dark:border-slate-600 rounded-sm shadow-sm"><option value="viewedAtDesc">${ssrInterpolate(unref(t)("viewedAt"))} ↓</option><option value="viewedAtAsc">${ssrInterpolate(unref(t)("viewedAt"))} ↑</option><option disabled>─────────────────</option><option value="idDesc">ID ↓</option><option value="idAsc">ID ↑</option><option disabled>─────────────────</option><option value="productIdDesc">${ssrInterpolate(unref(t)("marketProduct"))} ID ↓</option><option value="productIdAsc">${ssrInterpolate(unref(t)("marketProduct"))} ID ↑</option><option disabled>─────────────────</option><option value="productTitleAsc">${ssrInterpolate(unref(t)("marketProduct"))} A→Z</option><option value="productTitleDesc">${ssrInterpolate(unref(t)("marketProduct"))} Z→A</option><option disabled>─────────────────</option></select></div>`);
    };
  }
};
const _sfc_setup$2 = _sfc_main$2.setup;
_sfc_main$2.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Components/Admin/Market/MarketRecentlyViewedProduct/Sort/SortSelect.vue");
  return _sfc_setup$2 ? _sfc_setup$2(props, ctx) : void 0;
};
const _sfc_main$1 = {
  __name: "MarketRecentlyViewedProductTable",
  __ssrInlineRender: true,
  props: {
    history: {
      type: Array,
      default: () => []
    }
  },
  setup(__props) {
    const { t, locale } = useI18n();
    const props = __props;
    const productTranslation = (product) => {
      if (!product) {
        return null;
      }
      if (product.translation) {
        return product.translation;
      }
      const translations = Array.isArray(product.translations) ? product.translations : [];
      return translations.find(
        (translation) => translation.locale === locale.value
      ) || translations[0] || null;
    };
    const productTitle = (product) => {
      var _a;
      return ((_a = productTranslation(product)) == null ? void 0 : _a.title) || (product == null ? void 0 : product.title) || "—";
    };
    const brandTitle = (product) => {
      var _a, _b, _c;
      if (!(product == null ? void 0 : product.brand)) {
        return "—";
      }
      if ((_a = product.brand.translation) == null ? void 0 : _a.title) {
        return product.brand.translation.title;
      }
      const translations = Array.isArray(product.brand.translations) ? product.brand.translations : [];
      return ((_b = translations.find(
        (translation) => translation.locale === locale.value
      )) == null ? void 0 : _b.title) || ((_c = translations[0]) == null ? void 0 : _c.title) || product.brand.title || "—";
    };
    const formatDate = (dateString) => {
      if (!dateString) {
        return "—";
      }
      const date = new Date(dateString);
      if (Number.isNaN(date.getTime())) {
        return "—";
      }
      return new Intl.DateTimeFormat(
        locale.value || "ru-RU",
        {
          day: "2-digit",
          month: "2-digit",
          year: "numeric",
          hour: "2-digit",
          minute: "2-digit"
        }
      ).format(date);
    };
    const productPrice = (product) => {
      if (!product) {
        return "—";
      }
      const value = product.price ?? product.current_price ?? product.base_price;
      if (value === null || value === void 0 || value === "") {
        return "—";
      }
      const number = Number(value);
      if (!Number.isFinite(number)) {
        return value;
      }
      return new Intl.NumberFormat(
        locale.value || "ru-RU",
        {
          minimumFractionDigits: 0,
          maximumFractionDigits: 2
        }
      ).format(number);
    };
    const currency = (product) => {
      var _a, _b;
      return ((_a = product == null ? void 0 : product.currency) == null ? void 0 : _a.symbol) || ((_b = product == null ? void 0 : product.currency) == null ? void 0 : _b.code) || "";
    };
    const hasHistory = computed(() => {
      return props.history.length > 0;
    });
    return (_ctx, _push, _parent, _attrs) => {
      _push(`<div${ssrRenderAttrs(mergeProps({ class: "bg-white dark:bg-slate-700 shadow-lg rounded-sm border border-slate-200 dark:border-slate-600 relative" }, _attrs))}><div class="overflow-x-auto">`);
      if (hasHistory.value) {
        _push(`<table class="table-auto w-full text-slate-700 dark:text-slate-100"><thead class="text-sm uppercase bg-slate-200 dark:bg-cyan-900 border border-solid border-gray-300 dark:border-gray-700"><tr><th class="px-2 first:pl-5 last:pr-5 py-3 whitespace-nowrap w-px"><div class="font-medium text-center">${ssrInterpolate(unref(t)("id"))}</div></th><th class="px-2 first:pl-5 last:pr-5 py-3 whitespace-nowrap"><div class="font-medium text-left">${ssrInterpolate(unref(t)("marketProduct"))}</div></th><th class="px-2 first:pl-5 last:pr-5 py-3 whitespace-nowrap"><div class="font-medium text-center"> SKU </div></th><th class="px-2 first:pl-5 last:pr-5 py-3 whitespace-nowrap"><div class="font-medium text-center">${ssrInterpolate(unref(t)("brand"))}</div></th><th class="px-2 first:pl-5 last:pr-5 py-3 whitespace-nowrap"><div class="font-medium text-end">${ssrInterpolate(unref(t)("price"))}</div></th><th class="px-2 first:pl-5 last:pr-5 py-3 whitespace-nowrap"><div class="font-medium text-center">${ssrInterpolate(unref(t)("viewedAt"))}</div></th></tr></thead><tbody><!--[-->`);
        ssrRenderList(__props.history, (item) => {
          var _a;
          _push(`<tr class="text-sm font-semibold border-b-2 hover:bg-slate-100 dark:hover:bg-cyan-800"><td class="px-2 first:pl-5 last:pr-5 py-2 whitespace-nowrap"><div class="text-center">${ssrInterpolate(item.id)}</div></td><td class="px-2 first:pl-5 last:pr-5 py-2 min-w-64"><div class="flex flex-col"><div class="mt-0.5 text-xs text-slate-500 dark:text-slate-300"> ID: ${ssrInterpolate(item.market_product_id)}</div><div class="font-semibold text-amber-700 dark:text-amber-300">${ssrInterpolate(productTitle(item.product))}</div></div></td><td class="px-2 first:pl-5 last:pr-5 py-2 whitespace-nowrap"><div class="text-center text-xs">${ssrInterpolate(((_a = item.product) == null ? void 0 : _a.sku) || "—")}</div></td><td class="px-2 first:pl-5 last:pr-5 py-2"><div class="text-center text-blue-700 dark:text-blue-300">${ssrInterpolate(brandTitle(item.product))}</div></td><td class="px-2 first:pl-5 last:pr-5 py-2 whitespace-nowrap"><div class="text-end text-teal-700 dark:text-teal-300">${ssrInterpolate(productPrice(item.product))} `);
          if (currency(item.product)) {
            _push(`<span class="ml-1 text-xs">${ssrInterpolate(currency(item.product))}</span>`);
          } else {
            _push(`<!---->`);
          }
          _push(`</div></td><td class="px-2 first:pl-5 last:pr-5 py-2 whitespace-nowrap"><div class="text-center"><span class="inline-flex px-2 py-1 rounded-sm border border-indigo-300 bg-indigo-50 text-xs font-semibold text-indigo-800 dark:border-indigo-700 dark:bg-indigo-900/40 dark:text-indigo-200">${ssrInterpolate(formatDate(item.viewed_at))}</span></div></td></tr>`);
        });
        _push(`<!--]--></tbody></table>`);
      } else {
        _push(`<div class="p-5 text-center text-slate-700 dark:text-slate-100">${ssrInterpolate(unref(t)("noData"))}</div>`);
      }
      _push(`</div></div>`);
    };
  }
};
const _sfc_setup$1 = _sfc_main$1.setup;
_sfc_main$1.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Components/Admin/Market/MarketRecentlyViewedProduct/Table/MarketRecentlyViewedProductTable.vue");
  return _sfc_setup$1 ? _sfc_setup$1(props, ctx) : void 0;
};
const _sfc_main = {
  __name: "Show",
  __ssrInlineRender: true,
  props: {
    /**
     * Пользователь,
     * историю которого просматриваем.
     */
    user: {
      type: Object,
      default: () => ({})
    },
    /**
     * Серверная пагинированная история
     * просмотренных товаров.
     */
    history: {
      type: [Array, Object],
      default: () => []
    },
    /**
     * Общее количество уникальных товаров
     * в истории пользователя.
     */
    historyCount: {
      type: Number,
      default: 0
    },
    /**
     * Дата последнего просмотра товара.
     */
    lastViewedAt: {
      type: String,
      default: null
    },
    search: {
      type: String,
      default: ""
    },
    /**
     * Текущие server-side фильтры.
     */
    filters: {
      type: Object,
      default: () => ({})
    },
    /**
     * Текущая локаль Laravel.
     */
    locale: {
      type: String,
      default: "ru"
    },
    error: {
      type: String,
      default: ""
    },
    errors: {
      type: Object,
      default: () => ({})
    }
  },
  setup(__props) {
    var _a, _b;
    const { t } = useI18n();
    const props = __props;
    const historyList = computed(() => {
      var _a2;
      if (Array.isArray(props.history)) {
        return props.history;
      }
      if (Array.isArray((_a2 = props.history) == null ? void 0 : _a2.data)) {
        return props.history.data;
      }
      return [];
    });
    const historyFound = computed(() => {
      var _a2, _b2, _c;
      const metaTotal = Number(
        (_b2 = (_a2 = props.history) == null ? void 0 : _a2.meta) == null ? void 0 : _b2.total
      );
      if (Number.isFinite(metaTotal)) {
        return metaTotal;
      }
      const total = Number(
        (_c = props.history) == null ? void 0 : _c.total
      );
      if (Number.isFinite(total)) {
        return total;
      }
      return historyList.value.length;
    });
    const searchQuery = ref(
      props.search || ""
    );
    const itemsPerPage = ref(
      Number(((_a = props.filters) == null ? void 0 : _a.per_page) || 20)
    );
    const sortParam = ref(
      String(((_b = props.filters) == null ? void 0 : _b.sort) || "viewedAtDesc")
    );
    const reloadHistory = (overrides = {}) => {
      router.get(
        window.location.pathname,
        {
          search: searchQuery.value || void 0,
          per_page: itemsPerPage.value,
          sort: sortParam.value || void 0,
          page: void 0,
          ...overrides
        },
        {
          preserveState: true,
          preserveScroll: true,
          replace: true
        }
      );
    };
    const updateItemsPerPage = (value) => {
      const perPage = Number(value);
      if (!Number.isFinite(perPage)) {
        return;
      }
      itemsPerPage.value = perPage;
      reloadHistory({
        per_page: perPage
      });
    };
    const updateSort = (value) => {
      const nextSort = String(
        value || "viewedAtDesc"
      );
      sortParam.value = nextSort;
      reloadHistory({
        sort: nextSort
      });
    };
    const userName = computed(() => {
      var _a2;
      return ((_a2 = props.user) == null ? void 0 : _a2.name) || "—";
    });
    const userEmail = computed(() => {
      var _a2;
      return ((_a2 = props.user) == null ? void 0 : _a2.email) || "—";
    });
    const userInitials = computed(() => {
      var _a2;
      const name = String(
        ((_a2 = props.user) == null ? void 0 : _a2.name) || ""
      ).trim();
      if (!name) {
        return "?";
      }
      return name.split(/\s+/).filter(Boolean).slice(0, 2).map((part) => part.charAt(0)).join("").toUpperCase();
    });
    const formatDate = (dateString) => {
      if (!dateString) {
        return "—";
      }
      const date = new Date(dateString);
      if (Number.isNaN(date.getTime())) {
        return "—";
      }
      return new Intl.DateTimeFormat(
        props.locale || "ru",
        {
          day: "2-digit",
          month: "2-digit",
          year: "numeric",
          hour: "2-digit",
          minute: "2-digit"
        }
      ).format(date);
    };
    return (_ctx, _push, _parent, _attrs) => {
      _push(ssrRenderComponent(AdminLayout, mergeProps({
        title: unref(t)("recentlyViewedProducts")
      }, _attrs), {
        header: withCtx((_, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(ssrRenderComponent(TitlePage, null, {
              default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                if (_push3) {
                  _push3(`${ssrInterpolate(unref(t)("recentlyViewedProducts"))}`);
                } else {
                  return [
                    createTextVNode(toDisplayString(unref(t)("recentlyViewedProducts")), 1)
                  ];
                }
              }),
              _: 1
            }, _parent2, _scopeId));
          } else {
            return [
              createVNode(TitlePage, null, {
                default: withCtx(() => [
                  createTextVNode(toDisplayString(unref(t)("recentlyViewedProducts")), 1)
                ]),
                _: 1
              })
            ];
          }
        }),
        default: withCtx((_, _push2, _parent2, _scopeId) => {
          var _a2, _b2, _c, _d;
          if (_push2) {
            _push2(`<div class="px-4 py-8 w-full max-w-12xl mx-auto sm:px-6 lg:px-8"${_scopeId}><div class="p-4 bg-slate-50 dark:bg-slate-700 border border-blue-400 dark:border-blue-200 shadow-lg shadow-gray-500 dark:shadow-slate-400 bg-opacity-95 dark:bg-opacity-95"${_scopeId}><div class="mb-2 sm:flex sm:items-center sm:justify-between"${_scopeId}>`);
            _push2(ssrRenderComponent(_sfc_main$3, {
              href: _ctx.route(
                "admin.marketRecentlyViewedProducts.index"
              )
            }, {
              icon: withCtx((_2, _push3, _parent3, _scopeId2) => {
                if (_push3) {
                  _push3(`<svg class="w-4 h-4 mr-2 fill-current text-slate-100 shrink-0" viewBox="0 0 16 16"${_scopeId2}><path d="M4.3 4.5c1.9-1.9 5.1-1.9 7 0
                                       .7.7 1.2 1.7 1.4 2.7l2-.3
                                       c-.2-1.5-.9-2.8-1.9-3.8
                                       C10.1.4 5.7.4 2.9 3.1L.7.9
                                       0 7.3l6.4-.7-2.1-2.1z
                                       M15.6 8.7l-6.4.7 2.1 2.1
                                       c-1.9 1.9-5.1 1.9-7 0
                                       -.7-.7-1.2-1.7-1.4-2.7l-2 .3
                                       c.2 1.5.9 2.8 1.9 3.8
                                       1.4 1.4 3.1 2 4.9 2
                                       1.8 0 3.6-.7 4.9-2l2.2 2.2
                                       .8-6.4z"${_scopeId2}></path></svg>`);
                } else {
                  return [
                    (openBlock(), createBlock("svg", {
                      class: "w-4 h-4 mr-2 fill-current text-slate-100 shrink-0",
                      viewBox: "0 0 16 16"
                    }, [
                      createVNode("path", { d: "M4.3 4.5c1.9-1.9 5.1-1.9 7 0\n                                       .7.7 1.2 1.7 1.4 2.7l2-.3\n                                       c-.2-1.5-.9-2.8-1.9-3.8\n                                       C10.1.4 5.7.4 2.9 3.1L.7.9\n                                       0 7.3l6.4-.7-2.1-2.1z\n                                       M15.6 8.7l-6.4.7 2.1 2.1\n                                       c-1.9 1.9-5.1 1.9-7 0\n                                       -.7-.7-1.2-1.7-1.4-2.7l-2 .3\n                                       c.2 1.5.9 2.8 1.9 3.8\n                                       1.4 1.4 3.1 2 4.9 2\n                                       1.8 0 3.6-.7 4.9-2l2.2 2.2\n                                       .8-6.4z" })
                    ]))
                  ];
                }
              }),
              default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                if (_push3) {
                  _push3(` ${ssrInterpolate(unref(t)("back"))}`);
                } else {
                  return [
                    createTextVNode(" " + toDisplayString(unref(t)("back")), 1)
                  ];
                }
              }),
              _: 1
            }, _parent2, _scopeId));
            _push2(`</div><div class="mb-4 flex flex-col gap-4 rounded-sm border border-slate-300 dark:border-slate-500 bg-white dark:bg-slate-800 p-4 lg:flex-row lg:items-center lg:justify-between"${_scopeId}><div class="flex min-w-0 items-center gap-3"${_scopeId}>`);
            if ((_a2 = __props.user) == null ? void 0 : _a2.profile_photo_url) {
              _push2(`<img${ssrRenderAttr("src", __props.user.profile_photo_url)}${ssrRenderAttr("alt", userName.value)} class="h-12 w-12 shrink-0 rounded-full object-cover ring-2 ring-slate-200 dark:ring-slate-600"${_scopeId}>`);
            } else {
              _push2(`<div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-full bg-indigo-100 text-sm font-bold text-indigo-700 ring-2 ring-indigo-200 dark:bg-indigo-900/50 dark:text-indigo-300 dark:ring-indigo-700"${_scopeId}>${ssrInterpolate(userInitials.value)}</div>`);
            }
            _push2(`<div class="min-w-0"${_scopeId}><div class="truncate text-base font-semibold text-slate-800 dark:text-slate-100"${_scopeId}>${ssrInterpolate(userName.value)}</div><div class="truncate text-xs font-semibold text-slate-500 dark:text-slate-300"${_scopeId}>${ssrInterpolate(userEmail.value)}</div><div class="mt-0.5 text-[11px] font-semibold text-slate-400 dark:text-slate-400"${_scopeId}>${ssrInterpolate(unref(t)("id"))}: ${ssrInterpolate(((_b2 = __props.user) == null ? void 0 : _b2.id) ?? "—")}</div></div></div><div class="grid grid-cols-1 gap-2 sm:grid-cols-2"${_scopeId}><div class="min-w-44 px-4 py-2 rounded-sm border border-cyan-400 dark:border-cyan-200 bg-cyan-200 dark:bg-cyan-800 text-center"${_scopeId}><div class="text-[10px] font-semibold uppercase tracking-wide text-slate-800 dark:text-slate-200"${_scopeId}>${ssrInterpolate(unref(t)("recentlyViewedProducts"))}</div><div class="mt-1 text-lg font-bold text-gray-700 dark:text-gray-300"${_scopeId}>${ssrInterpolate(__props.historyCount)}</div></div><div class="min-w-44 px-4 py-2 rounded-sm border border-indigo-400 dark:border-indigo-200 bg-indigo-50 dark:bg-indigo-900 text-center"${_scopeId}><div class="text-[10px] font-semibold uppercase tracking-wide text-slate-800 dark:text-slate-200"${_scopeId}>${ssrInterpolate(unref(t)("lastViewedAt"))}</div><div class="mt-1 text-xs font-bold text-indigo-700 dark:text-indigo-300"${_scopeId}>${ssrInterpolate(formatDate(__props.lastViewedAt))}</div></div></div></div>`);
            if (__props.historyCount) {
              _push2(ssrRenderComponent(_sfc_main$4, {
                modelValue: searchQuery.value,
                "onUpdate:modelValue": ($event) => searchQuery.value = $event,
                placeholder: unref(t)("search")
              }, null, _parent2, _scopeId));
            } else {
              _push2(`<!---->`);
            }
            if (__props.historyCount) {
              _push2(`<div class="my-3 flex flex-col items-center justify-between gap-3 md:flex-row"${_scopeId}>`);
              _push2(ssrRenderComponent(_sfc_main$5, {
                "items-per-page": itemsPerPage.value,
                "onUpdate:itemsPerPage": updateItemsPerPage
              }, null, _parent2, _scopeId));
              _push2(ssrRenderComponent(_sfc_main$2, {
                "sort-param": sortParam.value,
                "onUpdate:sortParam": updateSort
              }, null, _parent2, _scopeId));
              _push2(`</div>`);
            } else {
              _push2(`<!---->`);
            }
            if (__props.historyCount) {
              _push2(`<div class="mb-3 flex flex-col items-center justify-between gap-2 lg:flex-row"${_scopeId}><div class="flex items-center gap-2"${_scopeId}><span class="text-xs font-semibold text-slate-500 dark:text-slate-300"${_scopeId}>${ssrInterpolate(unref(t)("total"))}: </span>`);
              _push2(ssrRenderComponent(_sfc_main$6, null, {
                default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                  if (_push3) {
                    _push3(`${ssrInterpolate(__props.historyCount)}`);
                  } else {
                    return [
                      createTextVNode(toDisplayString(__props.historyCount), 1)
                    ];
                  }
                }),
                _: 1
              }, _parent2, _scopeId));
              _push2(`</div>`);
              if (searchQuery.value) {
                _push2(`<div class="inline-flex items-center gap-2 rounded-sm border border-emerald-200 bg-emerald-50 px-3 py-1 text-xs font-semibold text-emerald-700 dark:border-emerald-800 dark:bg-emerald-950/40 dark:text-emerald-300"${_scopeId}><span${_scopeId}>${ssrInterpolate(unref(t)("found"))}: </span><span class="font-bold"${_scopeId}>${ssrInterpolate(historyFound.value)}</span></div>`);
              } else {
                _push2(`<!---->`);
              }
              _push2(`</div>`);
            } else {
              _push2(`<!---->`);
            }
            if (historyFound.value) {
              _push2(`<div class="mt-3 flex items-center justify-center"${_scopeId}>`);
              _push2(ssrRenderComponent(_sfc_main$7, { pagination: __props.history }, null, _parent2, _scopeId));
              _push2(`</div>`);
            } else {
              _push2(`<!---->`);
            }
            _push2(`<div class="mt-3"${_scopeId}>`);
            _push2(ssrRenderComponent(_sfc_main$1, { history: historyList.value }, null, _parent2, _scopeId));
            _push2(`</div>`);
            if (historyFound.value) {
              _push2(`<div class="mt-3 flex items-center justify-center"${_scopeId}>`);
              _push2(ssrRenderComponent(_sfc_main$7, { pagination: __props.history }, null, _parent2, _scopeId));
              _push2(`</div>`);
            } else {
              _push2(`<!---->`);
            }
            if (props.error) {
              _push2(`<div class="mt-3 text-sm font-semibold text-rose-700 dark:text-rose-300"${_scopeId}>${ssrInterpolate(props.error)}</div>`);
            } else {
              _push2(`<!---->`);
            }
            _push2(`</div></div>`);
          } else {
            return [
              createVNode("div", { class: "px-4 py-8 w-full max-w-12xl mx-auto sm:px-6 lg:px-8" }, [
                createVNode("div", { class: "p-4 bg-slate-50 dark:bg-slate-700 border border-blue-400 dark:border-blue-200 shadow-lg shadow-gray-500 dark:shadow-slate-400 bg-opacity-95 dark:bg-opacity-95" }, [
                  createVNode("div", { class: "mb-2 sm:flex sm:items-center sm:justify-between" }, [
                    createVNode(_sfc_main$3, {
                      href: _ctx.route(
                        "admin.marketRecentlyViewedProducts.index"
                      )
                    }, {
                      icon: withCtx(() => [
                        (openBlock(), createBlock("svg", {
                          class: "w-4 h-4 mr-2 fill-current text-slate-100 shrink-0",
                          viewBox: "0 0 16 16"
                        }, [
                          createVNode("path", { d: "M4.3 4.5c1.9-1.9 5.1-1.9 7 0\n                                       .7.7 1.2 1.7 1.4 2.7l2-.3\n                                       c-.2-1.5-.9-2.8-1.9-3.8\n                                       C10.1.4 5.7.4 2.9 3.1L.7.9\n                                       0 7.3l6.4-.7-2.1-2.1z\n                                       M15.6 8.7l-6.4.7 2.1 2.1\n                                       c-1.9 1.9-5.1 1.9-7 0\n                                       -.7-.7-1.2-1.7-1.4-2.7l-2 .3\n                                       c.2 1.5.9 2.8 1.9 3.8\n                                       1.4 1.4 3.1 2 4.9 2\n                                       1.8 0 3.6-.7 4.9-2l2.2 2.2\n                                       .8-6.4z" })
                        ]))
                      ]),
                      default: withCtx(() => [
                        createTextVNode(" " + toDisplayString(unref(t)("back")), 1)
                      ]),
                      _: 1
                    }, 8, ["href"])
                  ]),
                  createVNode("div", { class: "mb-4 flex flex-col gap-4 rounded-sm border border-slate-300 dark:border-slate-500 bg-white dark:bg-slate-800 p-4 lg:flex-row lg:items-center lg:justify-between" }, [
                    createVNode("div", { class: "flex min-w-0 items-center gap-3" }, [
                      ((_c = __props.user) == null ? void 0 : _c.profile_photo_url) ? (openBlock(), createBlock("img", {
                        key: 0,
                        src: __props.user.profile_photo_url,
                        alt: userName.value,
                        class: "h-12 w-12 shrink-0 rounded-full object-cover ring-2 ring-slate-200 dark:ring-slate-600"
                      }, null, 8, ["src", "alt"])) : (openBlock(), createBlock("div", {
                        key: 1,
                        class: "flex h-12 w-12 shrink-0 items-center justify-center rounded-full bg-indigo-100 text-sm font-bold text-indigo-700 ring-2 ring-indigo-200 dark:bg-indigo-900/50 dark:text-indigo-300 dark:ring-indigo-700"
                      }, toDisplayString(userInitials.value), 1)),
                      createVNode("div", { class: "min-w-0" }, [
                        createVNode("div", { class: "truncate text-base font-semibold text-slate-800 dark:text-slate-100" }, toDisplayString(userName.value), 1),
                        createVNode("div", { class: "truncate text-xs font-semibold text-slate-500 dark:text-slate-300" }, toDisplayString(userEmail.value), 1),
                        createVNode("div", { class: "mt-0.5 text-[11px] font-semibold text-slate-400 dark:text-slate-400" }, toDisplayString(unref(t)("id")) + ": " + toDisplayString(((_d = __props.user) == null ? void 0 : _d.id) ?? "—"), 1)
                      ])
                    ]),
                    createVNode("div", { class: "grid grid-cols-1 gap-2 sm:grid-cols-2" }, [
                      createVNode("div", { class: "min-w-44 px-4 py-2 rounded-sm border border-cyan-400 dark:border-cyan-200 bg-cyan-200 dark:bg-cyan-800 text-center" }, [
                        createVNode("div", { class: "text-[10px] font-semibold uppercase tracking-wide text-slate-800 dark:text-slate-200" }, toDisplayString(unref(t)("recentlyViewedProducts")), 1),
                        createVNode("div", { class: "mt-1 text-lg font-bold text-gray-700 dark:text-gray-300" }, toDisplayString(__props.historyCount), 1)
                      ]),
                      createVNode("div", { class: "min-w-44 px-4 py-2 rounded-sm border border-indigo-400 dark:border-indigo-200 bg-indigo-50 dark:bg-indigo-900 text-center" }, [
                        createVNode("div", { class: "text-[10px] font-semibold uppercase tracking-wide text-slate-800 dark:text-slate-200" }, toDisplayString(unref(t)("lastViewedAt")), 1),
                        createVNode("div", { class: "mt-1 text-xs font-bold text-indigo-700 dark:text-indigo-300" }, toDisplayString(formatDate(__props.lastViewedAt)), 1)
                      ])
                    ])
                  ]),
                  __props.historyCount ? (openBlock(), createBlock(_sfc_main$4, {
                    key: 0,
                    modelValue: searchQuery.value,
                    "onUpdate:modelValue": ($event) => searchQuery.value = $event,
                    placeholder: unref(t)("search")
                  }, null, 8, ["modelValue", "onUpdate:modelValue", "placeholder"])) : createCommentVNode("", true),
                  __props.historyCount ? (openBlock(), createBlock("div", {
                    key: 1,
                    class: "my-3 flex flex-col items-center justify-between gap-3 md:flex-row"
                  }, [
                    createVNode(_sfc_main$5, {
                      "items-per-page": itemsPerPage.value,
                      "onUpdate:itemsPerPage": updateItemsPerPage
                    }, null, 8, ["items-per-page"]),
                    createVNode(_sfc_main$2, {
                      "sort-param": sortParam.value,
                      "onUpdate:sortParam": updateSort
                    }, null, 8, ["sort-param"])
                  ])) : createCommentVNode("", true),
                  __props.historyCount ? (openBlock(), createBlock("div", {
                    key: 2,
                    class: "mb-3 flex flex-col items-center justify-between gap-2 lg:flex-row"
                  }, [
                    createVNode("div", { class: "flex items-center gap-2" }, [
                      createVNode("span", { class: "text-xs font-semibold text-slate-500 dark:text-slate-300" }, toDisplayString(unref(t)("total")) + ": ", 1),
                      createVNode(_sfc_main$6, null, {
                        default: withCtx(() => [
                          createTextVNode(toDisplayString(__props.historyCount), 1)
                        ]),
                        _: 1
                      })
                    ]),
                    searchQuery.value ? (openBlock(), createBlock("div", {
                      key: 0,
                      class: "inline-flex items-center gap-2 rounded-sm border border-emerald-200 bg-emerald-50 px-3 py-1 text-xs font-semibold text-emerald-700 dark:border-emerald-800 dark:bg-emerald-950/40 dark:text-emerald-300"
                    }, [
                      createVNode("span", null, toDisplayString(unref(t)("found")) + ": ", 1),
                      createVNode("span", { class: "font-bold" }, toDisplayString(historyFound.value), 1)
                    ])) : createCommentVNode("", true)
                  ])) : createCommentVNode("", true),
                  historyFound.value ? (openBlock(), createBlock("div", {
                    key: 3,
                    class: "mt-3 flex items-center justify-center"
                  }, [
                    createVNode(_sfc_main$7, { pagination: __props.history }, null, 8, ["pagination"])
                  ])) : createCommentVNode("", true),
                  createVNode("div", { class: "mt-3" }, [
                    createVNode(_sfc_main$1, { history: historyList.value }, null, 8, ["history"])
                  ]),
                  historyFound.value ? (openBlock(), createBlock("div", {
                    key: 4,
                    class: "mt-3 flex items-center justify-center"
                  }, [
                    createVNode(_sfc_main$7, { pagination: __props.history }, null, 8, ["pagination"])
                  ])) : createCommentVNode("", true),
                  props.error ? (openBlock(), createBlock("div", {
                    key: 5,
                    class: "mt-3 text-sm font-semibold text-rose-700 dark:text-rose-300"
                  }, toDisplayString(props.error), 1)) : createCommentVNode("", true)
                ])
              ])
            ];
          }
        }),
        _: 1
      }, _parent));
    };
  }
};
const _sfc_setup = _sfc_main.setup;
_sfc_main.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Pages/Admin/Market/MarketRecentlyViewedProducts/Show.vue");
  return _sfc_setup ? _sfc_setup(props, ctx) : void 0;
};
export {
  _sfc_main as default
};
