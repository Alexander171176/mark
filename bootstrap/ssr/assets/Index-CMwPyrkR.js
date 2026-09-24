import { computed, ref, mergeProps, unref, withCtx, createTextVNode, toDisplayString, createBlock, openBlock, createVNode, createCommentVNode, Fragment, renderList, useSSRContext } from "vue";
import { ssrRenderComponent, ssrInterpolate, ssrRenderAttr, ssrRenderList } from "vue/server-renderer";
import { Link, router } from "@inertiajs/vue3";
import { useI18n } from "vue-i18n";
import { A as AdminLayout } from "./AdminLayout-03yd3nxC.js";
import { T as TitlePage } from "./TitlePage-CBZZ8YZB.js";
import { _ as _sfc_main$1, a as _sfc_main$4 } from "./ServerSearchInput-CxQUCmzV.js";
import { _ as _sfc_main$3 } from "./CountTable-p8tyXGUL.js";
import { _ as _sfc_main$2 } from "./ItemsPerPageSelect-DezMefMH.js";
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
const _sfc_main = {
  __name: "Index",
  __ssrInlineRender: true,
  props: {
    /**
     * Серверная пагинированная коллекция пользователей,
     * имеющих историю просмотренных товаров.
     */
    users: {
      type: [Array, Object],
      default: () => []
    },
    /**
     * Общее количество пользователей,
     * имеющих историю просмотров.
     */
    usersCount: {
      type: Number,
      default: 0
    },
    /**
     * Текущая строка server-side поиска.
     */
    search: {
      type: String,
      default: ""
    },
    /**
     * Остальные server-side фильтры.
     */
    filters: {
      type: Object,
      default: () => ({})
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
    const { t, locale } = useI18n();
    const props = __props;
    const usersList = computed(() => {
      var _a2;
      if (Array.isArray(props.users)) {
        return props.users;
      }
      if (Array.isArray((_a2 = props.users) == null ? void 0 : _a2.data)) {
        return props.users.data;
      }
      return [];
    });
    const usersFound = computed(() => {
      var _a2, _b2, _c;
      const metaTotal = Number(
        (_b2 = (_a2 = props.users) == null ? void 0 : _a2.meta) == null ? void 0 : _b2.total
      );
      if (Number.isFinite(metaTotal)) {
        return metaTotal;
      }
      const total = Number(
        (_c = props.users) == null ? void 0 : _c.total
      );
      if (Number.isFinite(total)) {
        return total;
      }
      return usersList.value.length;
    });
    const searchQuery = ref(
      props.search || ""
    );
    const itemsPerPage = ref(
      Number(((_a = props.filters) == null ? void 0 : _a.per_page) || 20)
    );
    const sortParam = ref(
      String(
        ((_b = props.filters) == null ? void 0 : _b.sort) || "lastViewedAtDesc"
      )
    );
    const reloadUsers = (overrides = {}) => {
      router.get(
        window.location.pathname,
        {
          search: searchQuery.value || void 0,
          per_page: itemsPerPage.value,
          sort: sortParam.value || void 0,
          /**
           * При изменении сортировки
           * или количества строк
           * возвращаемся на первую страницу.
           */
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
      reloadUsers({
        per_page: perPage
      });
    };
    const updateSort = (value) => {
      const nextSort = String(
        value || "lastViewedAtDesc"
      );
      sortParam.value = nextSort;
      reloadUsers({
        sort: nextSort
      });
    };
    const userInitials = (user) => {
      const name = String(
        (user == null ? void 0 : user.name) || ""
      ).trim();
      if (!name) {
        return "?";
      }
      return name.split(/\s+/).filter(Boolean).slice(0, 2).map((part) => part.charAt(0)).join("").toUpperCase();
    };
    const viewedCount = (user) => {
      return Number(
        (user == null ? void 0 : user.recently_viewed_market_products_count) ?? 0
      );
    };
    const showRoute = (user) => {
      return route(
        "admin.users.marketRecentlyViewedProducts.show",
        {
          user: user.id
        }
      );
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
        locale.value || "ru",
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
          if (_push2) {
            _push2(`<div class="px-4 py-8 w-full max-w-12xl mx-auto sm:px-6 lg:px-8"${_scopeId}><div class="p-4 bg-slate-50 dark:bg-slate-700 border border-blue-400 dark:border-blue-200 shadow-lg shadow-gray-500 dark:shadow-slate-400 bg-opacity-95 dark:bg-opacity-95"${_scopeId}>`);
            if (__props.usersCount) {
              _push2(ssrRenderComponent(_sfc_main$1, {
                modelValue: searchQuery.value,
                "onUpdate:modelValue": ($event) => searchQuery.value = $event,
                placeholder: unref(t)("search")
              }, null, _parent2, _scopeId));
            } else {
              _push2(`<!---->`);
            }
            if (__props.usersCount) {
              _push2(`<div class="my-3 flex flex-col items-center justify-between gap-3 md:flex-row"${_scopeId}>`);
              _push2(ssrRenderComponent(_sfc_main$2, {
                "items-per-page": itemsPerPage.value,
                "onUpdate:itemsPerPage": updateItemsPerPage
              }, null, _parent2, _scopeId));
              _push2(`<div class="flex items-center justify-center h-fit my-2"${_scopeId}><label for="recentlyViewedUsersSort" class="hidden lg:block sm:mr-2 tracking-wider text-sm font-semibold text-slate-600 dark:text-slate-100"${_scopeId}>${ssrInterpolate(unref(t)("sort"))}</label><select id="recentlyViewedUsersSort"${ssrRenderAttr("value", sortParam.value)} class="w-56 px-3 py-0.5 form-select bg-white dark:bg-gray-200 text-gray-600 dark:text-gray-900 border border-slate-400 dark:border-slate-600 rounded-sm shadow-sm"${_scopeId}><option value="lastViewedAtDesc"${_scopeId}>${ssrInterpolate(unref(t)("lastViewedAt"))} ↓ </option><option value="lastViewedAtAsc"${_scopeId}>${ssrInterpolate(unref(t)("lastViewedAt"))} ↑ </option><option disabled${_scopeId}> ───────────────── </option><option value="viewsCountDesc"${_scopeId}>${ssrInterpolate(unref(t)("recentlyViewedProducts"))} 9→0 </option><option value="viewsCountAsc"${_scopeId}>${ssrInterpolate(unref(t)("recentlyViewedProducts"))} 0→9 </option><option disabled${_scopeId}> ───────────────── </option><option value="nameAsc"${_scopeId}>${ssrInterpolate(unref(t)("user"))} A→Z </option><option value="nameDesc"${_scopeId}>${ssrInterpolate(unref(t)("user"))} Z→A </option><option value="emailAsc"${_scopeId}> Email A→Z </option><option value="emailDesc"${_scopeId}> Email Z→A </option><option disabled${_scopeId}> ───────────────── </option><option value="idDesc"${_scopeId}> ID ↓ </option><option value="idAsc"${_scopeId}> ID ↑ </option></select></div></div>`);
            } else {
              _push2(`<!---->`);
            }
            if (__props.usersCount) {
              _push2(`<div class="mb-3 flex flex-col items-center justify-between gap-2 lg:flex-row"${_scopeId}><div class="flex items-center gap-2"${_scopeId}><span class="text-xs font-semibold text-slate-500 dark:text-slate-300"${_scopeId}>${ssrInterpolate(unref(t)("total"))}: </span>`);
              _push2(ssrRenderComponent(_sfc_main$3, null, {
                default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                  if (_push3) {
                    _push3(`${ssrInterpolate(__props.usersCount)}`);
                  } else {
                    return [
                      createTextVNode(toDisplayString(__props.usersCount), 1)
                    ];
                  }
                }),
                _: 1
              }, _parent2, _scopeId));
              _push2(`</div>`);
              if (searchQuery.value) {
                _push2(`<div class="inline-flex items-center gap-2 rounded-sm border border-emerald-200 bg-emerald-50 px-3 py-1 text-xs font-semibold text-emerald-700 dark:border-emerald-800 dark:bg-emerald-950/40 dark:text-emerald-300"${_scopeId}><span${_scopeId}>${ssrInterpolate(unref(t)("found"))}: </span><span class="font-bold"${_scopeId}>${ssrInterpolate(usersFound.value)}</span></div>`);
              } else {
                _push2(`<!---->`);
              }
              _push2(`</div>`);
            } else {
              _push2(`<!---->`);
            }
            if (usersFound.value) {
              _push2(`<div class="mt-3 flex items-center justify-center"${_scopeId}>`);
              _push2(ssrRenderComponent(_sfc_main$4, { pagination: __props.users }, null, _parent2, _scopeId));
              _push2(`</div>`);
            } else {
              _push2(`<!---->`);
            }
            _push2(`<div class="mt-3 bg-white dark:bg-slate-700 shadow-lg rounded-sm border border-slate-200 dark:border-slate-600 relative"${_scopeId}><div class="overflow-x-auto"${_scopeId}>`);
            if (usersList.value.length) {
              _push2(`<table class="table-auto w-full text-slate-700 dark:text-slate-100"${_scopeId}><thead class="text-sm uppercase bg-slate-200 dark:bg-cyan-900 border border-solid border-gray-300 dark:border-gray-700"${_scopeId}><tr${_scopeId}><th class="px-2 first:pl-5 last:pr-5 py-3 whitespace-nowrap w-px"${_scopeId}><div class="font-medium text-center"${_scopeId}>${ssrInterpolate(unref(t)("id"))}</div></th><th class="px-2 first:pl-5 last:pr-5 py-3 whitespace-nowrap"${_scopeId}><div class="font-medium text-left"${_scopeId}>${ssrInterpolate(unref(t)("user"))}</div></th><th class="px-2 first:pl-5 last:pr-5 py-3 whitespace-nowrap"${_scopeId}><div class="font-medium text-left"${_scopeId}> Email </div></th><th class="px-2 first:pl-5 last:pr-5 py-3 whitespace-nowrap"${_scopeId}><div class="font-medium text-center"${_scopeId}>${ssrInterpolate(unref(t)("recentlyViewedProducts"))}</div></th><th class="px-2 first:pl-5 last:pr-5 py-3 whitespace-nowrap"${_scopeId}><div class="font-medium text-center"${_scopeId}>${ssrInterpolate(unref(t)("lastViewedAt"))}</div></th><th class="px-2 first:pl-5 last:pr-5 py-3 whitespace-nowrap w-px"${_scopeId}><div class="font-medium text-center"${_scopeId}>${ssrInterpolate(unref(t)("actions"))}</div></th></tr></thead><tbody${_scopeId}><!--[-->`);
              ssrRenderList(usersList.value, (user) => {
                _push2(`<tr class="text-sm font-semibold border-b-2 hover:bg-slate-100 dark:hover:bg-cyan-800"${_scopeId}><td class="px-2 first:pl-5 last:pr-5 py-2 whitespace-nowrap"${_scopeId}><div class="text-center"${_scopeId}>${ssrInterpolate(user.id)}</div></td><td class="px-2 first:pl-5 last:pr-5 py-2 min-w-52"${_scopeId}><div class="flex items-center gap-3"${_scopeId}>`);
                if (user.profile_photo_url) {
                  _push2(`<img${ssrRenderAttr("src", user.profile_photo_url)}${ssrRenderAttr("alt", user.name || "")} class="h-9 w-9 shrink-0 rounded-full object-cover ring-1 ring-slate-200 dark:ring-slate-600"${_scopeId}>`);
                } else {
                  _push2(`<div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-indigo-100 text-xs font-bold text-indigo-700 dark:bg-indigo-900/50 dark:text-indigo-300"${_scopeId}>${ssrInterpolate(userInitials(user))}</div>`);
                }
                _push2(`<div class="min-w-0"${_scopeId}>`);
                _push2(ssrRenderComponent(unref(Link), {
                  href: showRoute(user),
                  class: "block truncate font-semibold text-blue-700 hover:underline dark:text-blue-300"
                }, {
                  default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                    if (_push3) {
                      _push3(`${ssrInterpolate(user.name || "—")}`);
                    } else {
                      return [
                        createTextVNode(toDisplayString(user.name || "—"), 1)
                      ];
                    }
                  }),
                  _: 2
                }, _parent2, _scopeId));
                _push2(`<div class="mt-0.5 text-[10px] text-slate-500 dark:text-slate-300"${_scopeId}> ID: ${ssrInterpolate(user.id)}</div></div></div></td><td class="px-2 first:pl-5 last:pr-5 py-2"${_scopeId}><div class="text-xs font-semibold text-slate-600 dark:text-slate-200 break-all"${_scopeId}>${ssrInterpolate(user.email || "—")}</div></td><td class="px-2 first:pl-5 last:pr-5 py-2 whitespace-nowrap"${_scopeId}><div class="flex justify-center"${_scopeId}><span class="inline-flex items-center justify-center min-w-8 px-2 py-1 rounded-full border border-cyan-300 bg-cyan-50 text-xs font-bold text-cyan-700 dark:border-cyan-700 dark:bg-cyan-950/40 dark:text-cyan-300"${_scopeId}>${ssrInterpolate(viewedCount(user))}</span></div></td><td class="px-2 first:pl-5 last:pr-5 py-2 whitespace-nowrap"${_scopeId}><div class="text-center"${_scopeId}><span class="inline-flex rounded-sm border border-indigo-200 bg-indigo-50 px-2 py-1 text-xs font-semibold text-indigo-700 dark:border-indigo-800 dark:bg-indigo-950/40 dark:text-indigo-300"${_scopeId}>${ssrInterpolate(formatDate(
                  user.last_viewed_at
                ))}</span></div></td><td class="px-2 first:pl-5 last:pr-5 py-2 whitespace-nowrap"${_scopeId}><div class="flex justify-center"${_scopeId}>`);
                _push2(ssrRenderComponent(unref(Link), {
                  href: showRoute(user),
                  title: unref(t)("view"),
                  class: "flex items-center justify-center rounded-sm px-1 py-1 border border-blue-500 dark:border-blue-300 text-blue-600 dark:text-blue-300 transition hover:border-blue-700 hover:bg-blue-100 dark:hover:bg-cyan-900"
                }, {
                  default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                    if (_push3) {
                      _push3(`<svg class="w-4 h-4 shrink-0 fill-current text-blue-500 mx-1" viewBox="0 0 16 16"${_scopeId2}><path d="M5 9h11v2H5V9zM0 9h3v2H0V9zm5 4h6v2H5v-2zm-5 0h3v2H0v-2zm5-8h7v2H5V5zM0 5h3v2H0V5zm5-4h11v2H5V1zM0 1h3v2H0V1z"${_scopeId2}></path></svg>`);
                    } else {
                      return [
                        (openBlock(), createBlock("svg", {
                          class: "w-4 h-4 shrink-0 fill-current text-blue-500 mx-1",
                          viewBox: "0 0 16 16"
                        }, [
                          createVNode("path", { d: "M5 9h11v2H5V9zM0 9h3v2H0V9zm5 4h6v2H5v-2zm-5 0h3v2H0v-2zm5-8h7v2H5V5zM0 5h3v2H0V5zm5-4h11v2H5V1zM0 1h3v2H0V1z" })
                        ]))
                      ];
                    }
                  }),
                  _: 2
                }, _parent2, _scopeId));
                _push2(`</div></td></tr>`);
              });
              _push2(`<!--]--></tbody></table>`);
            } else {
              _push2(`<div class="p-5 text-center text-slate-700 dark:text-slate-100"${_scopeId}>${ssrInterpolate(unref(t)("noData"))}</div>`);
            }
            _push2(`</div></div>`);
            if (usersFound.value) {
              _push2(`<div class="mt-3 flex items-center justify-center"${_scopeId}>`);
              _push2(ssrRenderComponent(_sfc_main$4, { pagination: __props.users }, null, _parent2, _scopeId));
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
                  __props.usersCount ? (openBlock(), createBlock(_sfc_main$1, {
                    key: 0,
                    modelValue: searchQuery.value,
                    "onUpdate:modelValue": ($event) => searchQuery.value = $event,
                    placeholder: unref(t)("search")
                  }, null, 8, ["modelValue", "onUpdate:modelValue", "placeholder"])) : createCommentVNode("", true),
                  __props.usersCount ? (openBlock(), createBlock("div", {
                    key: 1,
                    class: "my-3 flex flex-col items-center justify-between gap-3 md:flex-row"
                  }, [
                    createVNode(_sfc_main$2, {
                      "items-per-page": itemsPerPage.value,
                      "onUpdate:itemsPerPage": updateItemsPerPage
                    }, null, 8, ["items-per-page"]),
                    createVNode("div", { class: "flex items-center justify-center h-fit my-2" }, [
                      createVNode("label", {
                        for: "recentlyViewedUsersSort",
                        class: "hidden lg:block sm:mr-2 tracking-wider text-sm font-semibold text-slate-600 dark:text-slate-100"
                      }, toDisplayString(unref(t)("sort")), 1),
                      createVNode("select", {
                        id: "recentlyViewedUsersSort",
                        value: sortParam.value,
                        onChange: ($event) => updateSort($event.target.value),
                        class: "w-56 px-3 py-0.5 form-select bg-white dark:bg-gray-200 text-gray-600 dark:text-gray-900 border border-slate-400 dark:border-slate-600 rounded-sm shadow-sm"
                      }, [
                        createVNode("option", { value: "lastViewedAtDesc" }, toDisplayString(unref(t)("lastViewedAt")) + " ↓ ", 1),
                        createVNode("option", { value: "lastViewedAtAsc" }, toDisplayString(unref(t)("lastViewedAt")) + " ↑ ", 1),
                        createVNode("option", { disabled: "" }, " ───────────────── "),
                        createVNode("option", { value: "viewsCountDesc" }, toDisplayString(unref(t)("recentlyViewedProducts")) + " 9→0 ", 1),
                        createVNode("option", { value: "viewsCountAsc" }, toDisplayString(unref(t)("recentlyViewedProducts")) + " 0→9 ", 1),
                        createVNode("option", { disabled: "" }, " ───────────────── "),
                        createVNode("option", { value: "nameAsc" }, toDisplayString(unref(t)("user")) + " A→Z ", 1),
                        createVNode("option", { value: "nameDesc" }, toDisplayString(unref(t)("user")) + " Z→A ", 1),
                        createVNode("option", { value: "emailAsc" }, " Email A→Z "),
                        createVNode("option", { value: "emailDesc" }, " Email Z→A "),
                        createVNode("option", { disabled: "" }, " ───────────────── "),
                        createVNode("option", { value: "idDesc" }, " ID ↓ "),
                        createVNode("option", { value: "idAsc" }, " ID ↑ ")
                      ], 40, ["value", "onChange"])
                    ])
                  ])) : createCommentVNode("", true),
                  __props.usersCount ? (openBlock(), createBlock("div", {
                    key: 2,
                    class: "mb-3 flex flex-col items-center justify-between gap-2 lg:flex-row"
                  }, [
                    createVNode("div", { class: "flex items-center gap-2" }, [
                      createVNode("span", { class: "text-xs font-semibold text-slate-500 dark:text-slate-300" }, toDisplayString(unref(t)("total")) + ": ", 1),
                      createVNode(_sfc_main$3, null, {
                        default: withCtx(() => [
                          createTextVNode(toDisplayString(__props.usersCount), 1)
                        ]),
                        _: 1
                      })
                    ]),
                    searchQuery.value ? (openBlock(), createBlock("div", {
                      key: 0,
                      class: "inline-flex items-center gap-2 rounded-sm border border-emerald-200 bg-emerald-50 px-3 py-1 text-xs font-semibold text-emerald-700 dark:border-emerald-800 dark:bg-emerald-950/40 dark:text-emerald-300"
                    }, [
                      createVNode("span", null, toDisplayString(unref(t)("found")) + ": ", 1),
                      createVNode("span", { class: "font-bold" }, toDisplayString(usersFound.value), 1)
                    ])) : createCommentVNode("", true)
                  ])) : createCommentVNode("", true),
                  usersFound.value ? (openBlock(), createBlock("div", {
                    key: 3,
                    class: "mt-3 flex items-center justify-center"
                  }, [
                    createVNode(_sfc_main$4, { pagination: __props.users }, null, 8, ["pagination"])
                  ])) : createCommentVNode("", true),
                  createVNode("div", { class: "mt-3 bg-white dark:bg-slate-700 shadow-lg rounded-sm border border-slate-200 dark:border-slate-600 relative" }, [
                    createVNode("div", { class: "overflow-x-auto" }, [
                      usersList.value.length ? (openBlock(), createBlock("table", {
                        key: 0,
                        class: "table-auto w-full text-slate-700 dark:text-slate-100"
                      }, [
                        createVNode("thead", { class: "text-sm uppercase bg-slate-200 dark:bg-cyan-900 border border-solid border-gray-300 dark:border-gray-700" }, [
                          createVNode("tr", null, [
                            createVNode("th", { class: "px-2 first:pl-5 last:pr-5 py-3 whitespace-nowrap w-px" }, [
                              createVNode("div", { class: "font-medium text-center" }, toDisplayString(unref(t)("id")), 1)
                            ]),
                            createVNode("th", { class: "px-2 first:pl-5 last:pr-5 py-3 whitespace-nowrap" }, [
                              createVNode("div", { class: "font-medium text-left" }, toDisplayString(unref(t)("user")), 1)
                            ]),
                            createVNode("th", { class: "px-2 first:pl-5 last:pr-5 py-3 whitespace-nowrap" }, [
                              createVNode("div", { class: "font-medium text-left" }, " Email ")
                            ]),
                            createVNode("th", { class: "px-2 first:pl-5 last:pr-5 py-3 whitespace-nowrap" }, [
                              createVNode("div", { class: "font-medium text-center" }, toDisplayString(unref(t)("recentlyViewedProducts")), 1)
                            ]),
                            createVNode("th", { class: "px-2 first:pl-5 last:pr-5 py-3 whitespace-nowrap" }, [
                              createVNode("div", { class: "font-medium text-center" }, toDisplayString(unref(t)("lastViewedAt")), 1)
                            ]),
                            createVNode("th", { class: "px-2 first:pl-5 last:pr-5 py-3 whitespace-nowrap w-px" }, [
                              createVNode("div", { class: "font-medium text-center" }, toDisplayString(unref(t)("actions")), 1)
                            ])
                          ])
                        ]),
                        createVNode("tbody", null, [
                          (openBlock(true), createBlock(Fragment, null, renderList(usersList.value, (user) => {
                            return openBlock(), createBlock("tr", {
                              key: user.id,
                              class: "text-sm font-semibold border-b-2 hover:bg-slate-100 dark:hover:bg-cyan-800"
                            }, [
                              createVNode("td", { class: "px-2 first:pl-5 last:pr-5 py-2 whitespace-nowrap" }, [
                                createVNode("div", { class: "text-center" }, toDisplayString(user.id), 1)
                              ]),
                              createVNode("td", { class: "px-2 first:pl-5 last:pr-5 py-2 min-w-52" }, [
                                createVNode("div", { class: "flex items-center gap-3" }, [
                                  user.profile_photo_url ? (openBlock(), createBlock("img", {
                                    key: 0,
                                    src: user.profile_photo_url,
                                    alt: user.name || "",
                                    class: "h-9 w-9 shrink-0 rounded-full object-cover ring-1 ring-slate-200 dark:ring-slate-600"
                                  }, null, 8, ["src", "alt"])) : (openBlock(), createBlock("div", {
                                    key: 1,
                                    class: "flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-indigo-100 text-xs font-bold text-indigo-700 dark:bg-indigo-900/50 dark:text-indigo-300"
                                  }, toDisplayString(userInitials(user)), 1)),
                                  createVNode("div", { class: "min-w-0" }, [
                                    createVNode(unref(Link), {
                                      href: showRoute(user),
                                      class: "block truncate font-semibold text-blue-700 hover:underline dark:text-blue-300"
                                    }, {
                                      default: withCtx(() => [
                                        createTextVNode(toDisplayString(user.name || "—"), 1)
                                      ]),
                                      _: 2
                                    }, 1032, ["href"]),
                                    createVNode("div", { class: "mt-0.5 text-[10px] text-slate-500 dark:text-slate-300" }, " ID: " + toDisplayString(user.id), 1)
                                  ])
                                ])
                              ]),
                              createVNode("td", { class: "px-2 first:pl-5 last:pr-5 py-2" }, [
                                createVNode("div", { class: "text-xs font-semibold text-slate-600 dark:text-slate-200 break-all" }, toDisplayString(user.email || "—"), 1)
                              ]),
                              createVNode("td", { class: "px-2 first:pl-5 last:pr-5 py-2 whitespace-nowrap" }, [
                                createVNode("div", { class: "flex justify-center" }, [
                                  createVNode("span", { class: "inline-flex items-center justify-center min-w-8 px-2 py-1 rounded-full border border-cyan-300 bg-cyan-50 text-xs font-bold text-cyan-700 dark:border-cyan-700 dark:bg-cyan-950/40 dark:text-cyan-300" }, toDisplayString(viewedCount(user)), 1)
                                ])
                              ]),
                              createVNode("td", { class: "px-2 first:pl-5 last:pr-5 py-2 whitespace-nowrap" }, [
                                createVNode("div", { class: "text-center" }, [
                                  createVNode("span", { class: "inline-flex rounded-sm border border-indigo-200 bg-indigo-50 px-2 py-1 text-xs font-semibold text-indigo-700 dark:border-indigo-800 dark:bg-indigo-950/40 dark:text-indigo-300" }, toDisplayString(formatDate(
                                    user.last_viewed_at
                                  )), 1)
                                ])
                              ]),
                              createVNode("td", { class: "px-2 first:pl-5 last:pr-5 py-2 whitespace-nowrap" }, [
                                createVNode("div", { class: "flex justify-center" }, [
                                  createVNode(unref(Link), {
                                    href: showRoute(user),
                                    title: unref(t)("view"),
                                    class: "flex items-center justify-center rounded-sm px-1 py-1 border border-blue-500 dark:border-blue-300 text-blue-600 dark:text-blue-300 transition hover:border-blue-700 hover:bg-blue-100 dark:hover:bg-cyan-900"
                                  }, {
                                    default: withCtx(() => [
                                      (openBlock(), createBlock("svg", {
                                        class: "w-4 h-4 shrink-0 fill-current text-blue-500 mx-1",
                                        viewBox: "0 0 16 16"
                                      }, [
                                        createVNode("path", { d: "M5 9h11v2H5V9zM0 9h3v2H0V9zm5 4h6v2H5v-2zm-5 0h3v2H0v-2zm5-8h7v2H5V5zM0 5h3v2H0V5zm5-4h11v2H5V1zM0 1h3v2H0V1z" })
                                      ]))
                                    ]),
                                    _: 2
                                  }, 1032, ["href", "title"])
                                ])
                              ])
                            ]);
                          }), 128))
                        ])
                      ])) : (openBlock(), createBlock("div", {
                        key: 1,
                        class: "p-5 text-center text-slate-700 dark:text-slate-100"
                      }, toDisplayString(unref(t)("noData")), 1))
                    ])
                  ]),
                  usersFound.value ? (openBlock(), createBlock("div", {
                    key: 4,
                    class: "mt-3 flex items-center justify-center"
                  }, [
                    createVNode(_sfc_main$4, { pagination: __props.users }, null, 8, ["pagination"])
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
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Pages/Admin/Market/MarketRecentlyViewedProducts/Index.vue");
  return _sfc_setup ? _sfc_setup(props, ctx) : void 0;
};
export {
  _sfc_main as default
};
