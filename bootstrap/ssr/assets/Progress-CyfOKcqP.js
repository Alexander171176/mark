import { computed, ref, watch, mergeProps, unref, useSSRContext, withCtx, createTextVNode, toDisplayString, createBlock, openBlock, Fragment, createVNode, withModifiers, onMounted, onUnmounted, createCommentVNode, onBeforeUnmount } from "vue";
import { ssrRenderAttrs, ssrRenderAttr, ssrInterpolate, ssrRenderComponent, ssrRenderSlot, ssrRenderStyle, ssrRenderClass, ssrRenderList, ssrRenderTeleport } from "vue/server-renderer";
import { usePage, router, useForm, Link } from "@inertiajs/vue3";
import { Inertia } from "@inertiajs/inertia";
import { useI18n } from "vue-i18n";
import { _ as _sfc_main$h } from "./ThemeToggle-DA16u1ft.js";
import { _ as _sfc_main$a, a as _sfc_main$b, b as _sfc_main$i } from "./LocaleSelectOption-BeLdazeX.js";
import { _ as _sfc_main$f } from "./Checkbox-CgE3PSwb.js";
import { _ as _sfc_main$d, a as _sfc_main$e } from "./TextInput-CCxUFX3K.js";
import { _ as _sfc_main$c } from "./InputLabel-Ds0Eo91B.js";
import { _ as _sfc_main$g } from "./PrimaryButton-D7EZDGT_.js";
import { _ as _export_sfc } from "./_plugin-vue_export-helper-1tPrXgE0.js";
const _sfc_main$9 = {
  __name: "MarketProductSearch",
  __ssrInlineRender: true,
  props: {
    mobile: {
      type: Boolean,
      default: false
    }
  },
  emits: [
    "submitted"
  ],
  setup(__props, { emit: __emit }) {
    const { t } = useI18n();
    const page = usePage();
    const currentSearch = computed(() => {
      var _a, _b;
      return String(
        ((_b = (_a = page.props) == null ? void 0 : _a.filters) == null ? void 0 : _b.q) ?? ""
      );
    });
    const searchQuery = ref(
      currentSearch.value
    );
    watch(
      currentSearch,
      (value) => {
        searchQuery.value = value;
      }
    );
    const hasSearch = computed(() => {
      return searchQuery.value.trim() !== "";
    });
    return (_ctx, _push, _parent, _attrs) => {
      if (__props.mobile) {
        _push(`<form${ssrRenderAttrs(mergeProps({ class: "mb-3" }, _attrs))}><div class="flex overflow-hidden rounded-lg border-2 border-blue-600"><div class="relative min-w-0 flex-1"><input${ssrRenderAttr("value", searchQuery.value)} type="text" class="w-full border-0 bg-white dark:bg-gray-900 px-3 py-2 pr-10 text-sm text-slate-800 dark:text-slate-100 placeholder:text-slate-400 focus:ring-0"${ssrRenderAttr("placeholder", unref(t)("search"))}>`);
        if (hasSearch.value) {
          _push(`<button type="button" class="absolute right-2 top-1/2 z-20 -translate-y-1/2 flex h-7 w-7 items-center justify-center rounded-full text-slate-400 transition hover:bg-slate-100 hover:text-blue-600 dark:text-slate-500 dark:hover:bg-slate-800 dark:hover:text-blue-400"${ssrRenderAttr("title", unref(t)("reset"))}><svg class="h-3.5 w-3.5" viewBox="0 0 384 512" fill="currentColor" aria-hidden="true"><path d="M342.6 150.6c12.5-12.5 12.5-32.8 0-45.3s-32.8-12.5-45.3 0L192 210.7 86.6 105.4c-12.5-12.5-32.8-12.5-45.3 0s-12.5 32.8 0 45.3L146.7 256 41.4 361.4c-12.5 12.5-12.5 32.8 0 45.3s32.8 12.5 45.3 0L192 301.3l105.4 105.3c12.5 12.5 32.8 12.5 45.3 0s12.5-32.8 0-45.3L237.3 256l105.3-105.4z"></path></svg></button>`);
        } else {
          _push(`<!---->`);
        }
        _push(`</div><button type="submit" class="shrink-0 bg-blue-600 px-4 text-sm font-bold text-white transition hover:bg-blue-700">${ssrInterpolate(unref(t)("find"))}</button></div></form>`);
      } else {
        _push(`<form${ssrRenderAttrs(mergeProps({ class: "flex w-full min-w-0 justify-center" }, _attrs))}><div class="flex w-full max-w-[1200px] items-stretch"><div class="relative z-20 min-w-0 flex-1 -mr-4"><input${ssrRenderAttr("value", searchQuery.value)} type="text" class="relative z-10 block h-full w-full rounded-xl border-2 border-blue-600 bg-white dark:bg-gray-900 px-4 py-2 pr-12 text-sm text-slate-800 dark:text-slate-100 placeholder:text-slate-400 outline-none focus:border-blue-600 focus:ring-0"${ssrRenderAttr("placeholder", unref(t)("search"))}>`);
        if (hasSearch.value) {
          _push(`<button type="button" class="absolute right-4 top-1/2 z-20 -translate-y-1/2 flex h-7 w-7 items-center justify-center rounded-full text-slate-600 transition hover:bg-slate-100 hover:text-blue-600 dark:text-slate-400 dark:hover:bg-slate-800 dark:hover:text-blue-400"${ssrRenderAttr("title", unref(t)("reset"))}><svg class="h-3.5 w-3.5" viewBox="0 0 384 512" fill="currentColor" aria-hidden="true"><path d="M342.6 150.6c12.5-12.5 12.5-32.8 0-45.3s-32.8-12.5-45.3 0L192 210.7 86.6 105.4c-12.5-12.5-32.8-12.5-45.3 0s-12.5 32.8 0 45.3L146.7 256 41.4 361.4c-12.5 12.5-12.5 32.8 0 45.3s32.8 12.5 45.3 0L192 301.3l105.4 105.3c12.5 12.5 32.8 12.5 45.3 0s12.5-32.8 0-45.3L237.3 256l105.3-105.4z"></path></svg></button>`);
        } else {
          _push(`<!---->`);
        }
        _push(`</div><button type="submit" class="relative z-10 shrink-0 rounded-r-xl bg-blue-600 pl-9 pr-6 text-sm font-bold text-white transition hover:bg-blue-700"${ssrRenderAttr("title", unref(t)("search"))}><svg class="h-5 w-5 fill-current" viewBox="0 0 512 512" aria-hidden="true"><path d="M505 442.7L405.3 343c-4.5-4.5-10.6-7-17-7H372c27.6-35.3 44-79.7 44-128C416 93.1 322.9 0 208 0S0 93.1 0 208s93.1 208 208 208c48.3 0 92.7-16.4 128-44v16.3c0 6.4 2.5 12.5 7 17l99.7 99.7c9.4 9.4 24.6 9.4 33.9 0l28.3-28.3c9.4-9.4 9.4-24.6.1-34zM208 336c-70.7 0-128-57.2-128-128 0-70.7 57.2-128 128-128 70.7 0 128 57.2 128 128 0 70.7-57.2 128-128 128z"></path></svg></button></div></form>`);
      }
    };
  }
};
const _sfc_setup$9 = _sfc_main$9.setup;
_sfc_main$9.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Components/Public/Default/Market/MarketProduct/MarketProductSearch.vue");
  return _sfc_setup$9 ? _sfc_setup$9(props, ctx) : void 0;
};
const _sfc_main$8 = {
  __name: "HeaderAccountDropdown",
  __ssrInlineRender: true,
  setup(__props) {
    const { t } = useI18n();
    const page = usePage();
    const isAuth = computed(() => {
      var _a, _b;
      return !!((_b = (_a = page.props) == null ? void 0 : _a.auth) == null ? void 0 : _b.user);
    });
    const user = computed(() => {
      var _a, _b;
      return ((_b = (_a = page.props) == null ? void 0 : _a.auth) == null ? void 0 : _b.user) || null;
    });
    const managesProfilePhotos = computed(() => {
      var _a, _b;
      return !!((_b = (_a = page.props) == null ? void 0 : _a.jetstream) == null ? void 0 : _b.managesProfilePhotos);
    });
    const logout = () => {
      router.post(route("logout"));
    };
    return (_ctx, _push, _parent, _attrs) => {
      _push(ssrRenderComponent(_sfc_main$a, mergeProps({
        align: "right",
        width: "60",
        class: "relative"
      }, _attrs), {
        trigger: withCtx((_, _push2, _parent2, _scopeId) => {
          var _a, _b, _c, _d, _e, _f;
          if (_push2) {
            if (isAuth.value) {
              _push2(`<button type="button" class="inline-flex h-9 w-9 items-center justify-center overflow-hidden rounded-full border border-slate-200 bg-white text-slate-600 transition hover:border-blue-300 hover:bg-slate-100 hover:text-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-300 dark:border-slate-700 dark:bg-gray-900 dark:text-slate-300 dark:hover:border-blue-700 dark:hover:bg-gray-800 dark:hover:text-blue-300"${ssrRenderAttr("title", ((_a = user.value) == null ? void 0 : _a.name) || unref(t)("account"))}${ssrRenderAttr("aria-label", ((_b = user.value) == null ? void 0 : _b.name) || unref(t)("account"))}${_scopeId}>`);
              if (managesProfilePhotos.value && ((_c = user.value) == null ? void 0 : _c.profile_photo_url)) {
                _push2(`<img class="h-full w-full object-cover"${ssrRenderAttr("src", user.value.profile_photo_url)}${ssrRenderAttr("alt", user.value.name)}${_scopeId}>`);
              } else {
                _push2(`<svg class="h-4 w-4" fill="currentColor" viewBox="0 0 448 512" aria-hidden="true"${_scopeId}><path d="M224 256A128 128 0 10224 0a128 128 0 000 256zm89.6 32h-16.7a174.1 174.1 0 01-145.8 0h-16.7A134.4 134.4 0 000 422.4V464a48 48 0 0048 48h352a48 48 0 0048-48v-41.6A134.4 134.4 0 00313.6 288z"${_scopeId}></path></svg>`);
              }
              _push2(`</button>`);
            } else {
              _push2(`<button type="button" class="inline-flex h-9 w-9 items-center justify-center rounded-full border border-slate-200 bg-white text-slate-600 transition hover:border-blue-300 hover:bg-slate-100 hover:text-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-300 dark:border-slate-700 dark:bg-gray-900 dark:text-slate-300 dark:hover:border-blue-700 dark:hover:bg-gray-800 dark:hover:text-blue-300"${ssrRenderAttr("title", unref(t)("account"))}${ssrRenderAttr("aria-label", unref(t)("account"))}${_scopeId}><svg class="h-4 w-4" fill="currentColor" viewBox="0 0 448 512" aria-hidden="true"${_scopeId}><path d="M224 256A128 128 0 10224 0a128 128 0 000 256zm89.6 32h-16.7a174.1 174.1 0 01-145.8 0h-16.7A134.4 134.4 0 000 422.4V464a48 48 0 0048 48h352a48 48 0 0048-48v-41.6A134.4 134.4 0 00313.6 288z"${_scopeId}></path></svg></button>`);
            }
          } else {
            return [
              isAuth.value ? (openBlock(), createBlock("button", {
                key: 0,
                type: "button",
                class: "inline-flex h-9 w-9 items-center justify-center overflow-hidden rounded-full border border-slate-200 bg-white text-slate-600 transition hover:border-blue-300 hover:bg-slate-100 hover:text-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-300 dark:border-slate-700 dark:bg-gray-900 dark:text-slate-300 dark:hover:border-blue-700 dark:hover:bg-gray-800 dark:hover:text-blue-300",
                title: ((_d = user.value) == null ? void 0 : _d.name) || unref(t)("account"),
                "aria-label": ((_e = user.value) == null ? void 0 : _e.name) || unref(t)("account")
              }, [
                managesProfilePhotos.value && ((_f = user.value) == null ? void 0 : _f.profile_photo_url) ? (openBlock(), createBlock("img", {
                  key: 0,
                  class: "h-full w-full object-cover",
                  src: user.value.profile_photo_url,
                  alt: user.value.name
                }, null, 8, ["src", "alt"])) : (openBlock(), createBlock("svg", {
                  key: 1,
                  class: "h-4 w-4",
                  fill: "currentColor",
                  viewBox: "0 0 448 512",
                  "aria-hidden": "true"
                }, [
                  createVNode("path", { d: "M224 256A128 128 0 10224 0a128 128 0 000 256zm89.6 32h-16.7a174.1 174.1 0 01-145.8 0h-16.7A134.4 134.4 0 000 422.4V464a48 48 0 0048 48h352a48 48 0 0048-48v-41.6A134.4 134.4 0 00313.6 288z" })
                ]))
              ], 8, ["title", "aria-label"])) : (openBlock(), createBlock("button", {
                key: 1,
                type: "button",
                class: "inline-flex h-9 w-9 items-center justify-center rounded-full border border-slate-200 bg-white text-slate-600 transition hover:border-blue-300 hover:bg-slate-100 hover:text-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-300 dark:border-slate-700 dark:bg-gray-900 dark:text-slate-300 dark:hover:border-blue-700 dark:hover:bg-gray-800 dark:hover:text-blue-300",
                title: unref(t)("account"),
                "aria-label": unref(t)("account")
              }, [
                (openBlock(), createBlock("svg", {
                  class: "h-4 w-4",
                  fill: "currentColor",
                  viewBox: "0 0 448 512",
                  "aria-hidden": "true"
                }, [
                  createVNode("path", { d: "M224 256A128 128 0 10224 0a128 128 0 000 256zm89.6 32h-16.7a174.1 174.1 0 01-145.8 0h-16.7A134.4 134.4 0 000 422.4V464a48 48 0 0048 48h352a48 48 0 0048-48v-41.6A134.4 134.4 0 00313.6 288z" })
                ]))
              ], 8, ["title", "aria-label"]))
            ];
          }
        }),
        content: withCtx((_, _push2, _parent2, _scopeId) => {
          var _a, _b, _c, _d, _e, _f;
          if (_push2) {
            if (isAuth.value) {
              _push2(`<!--[--><div class="px-4 py-4 border-b border-gray-200 dark:border-gray-700"${_scopeId}><div class="flex items-center gap-3"${_scopeId}><div class="flex h-10 w-10 shrink-0 items-center justify-center overflow-hidden rounded-full bg-slate-100 text-slate-500 dark:bg-gray-800 dark:text-slate-300"${_scopeId}>`);
              if (managesProfilePhotos.value && ((_a = user.value) == null ? void 0 : _a.profile_photo_url)) {
                _push2(`<img class="h-full w-full object-cover"${ssrRenderAttr("src", user.value.profile_photo_url)}${ssrRenderAttr("alt", user.value.name)}${_scopeId}>`);
              } else {
                _push2(`<svg class="h-5 w-5" fill="currentColor" viewBox="0 0 448 512" aria-hidden="true"${_scopeId}><path d="M224 256A128 128 0 10224 0a128 128 0 000 256zm89.6 32h-16.7a174.1 174.1 0 01-145.8 0h-16.7A134.4 134.4 0 000 422.4V464a48 48 0 0048 48h352a48 48 0 0048-48v-41.6A134.4 134.4 0 00313.6 288z"${_scopeId}></path></svg>`);
              }
              _push2(`</div><div class="min-w-0"${_scopeId}><div class="truncate text-sm font-semibold text-slate-800 dark:text-slate-100"${_scopeId}>${ssrInterpolate((_b = user.value) == null ? void 0 : _b.name)}</div><div class="mt-0.5 truncate text-xs text-slate-500 dark:text-slate-400"${_scopeId}>${ssrInterpolate((_c = user.value) == null ? void 0 : _c.email)}</div></div></div></div><div class="px-4 py-2 text-xs font-semibold text-slate-400 dark:text-slate-500"${_scopeId}>${ssrInterpolate(unref(t)("accountManagement"))}</div>`);
              _push2(ssrRenderComponent(_sfc_main$b, {
                href: _ctx.route("profile.show")
              }, {
                default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                  if (_push3) {
                    _push3(`${ssrInterpolate(unref(t)("profile"))}`);
                  } else {
                    return [
                      createTextVNode(toDisplayString(unref(t)("profile")), 1)
                    ];
                  }
                }),
                _: 1
              }, _parent2, _scopeId));
              _push2(`<div class="border-t border-gray-200 dark:border-gray-700"${_scopeId}></div><form${_scopeId}>`);
              _push2(ssrRenderComponent(_sfc_main$b, { as: "button" }, {
                default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                  if (_push3) {
                    _push3(`${ssrInterpolate(unref(t)("logout"))}`);
                  } else {
                    return [
                      createTextVNode(toDisplayString(unref(t)("logout")), 1)
                    ];
                  }
                }),
                _: 1
              }, _parent2, _scopeId));
              _push2(`</form><!--]-->`);
            } else {
              _push2(`<!--[--><div class="px-4 py-4 border-b border-gray-200 dark:border-gray-700"${_scopeId}><div class="flex items-center gap-3"${_scopeId}><div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-slate-100 text-slate-500 dark:bg-gray-800 dark:text-slate-300"${_scopeId}><svg class="h-5 w-5" fill="currentColor" viewBox="0 0 448 512" aria-hidden="true"${_scopeId}><path d="M224 256A128 128 0 10224 0a128 128 0 000 256zm89.6 32h-16.7a174.1 174.1 0 01-145.8 0h-16.7A134.4 134.4 0 000 422.4V464a48 48 0 0048 48h352a48 48 0 0048-48v-41.6A134.4 134.4 0 00313.6 288z"${_scopeId}></path></svg></div><div${_scopeId}><div class="text-sm font-semibold text-slate-800 dark:text-slate-100"${_scopeId}>${ssrInterpolate(unref(t)("guest"))}</div><div class="mt-0.5 text-xs text-slate-500 dark:text-slate-400"${_scopeId}>${ssrInterpolate(unref(t)("account"))}</div></div></div></div>`);
              _push2(ssrRenderComponent(_sfc_main$b, {
                href: _ctx.route("login")
              }, {
                default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                  if (_push3) {
                    _push3(`${ssrInterpolate(unref(t)("login"))}`);
                  } else {
                    return [
                      createTextVNode(toDisplayString(unref(t)("login")), 1)
                    ];
                  }
                }),
                _: 1
              }, _parent2, _scopeId));
              _push2(ssrRenderComponent(_sfc_main$b, {
                href: _ctx.route("register")
              }, {
                default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                  if (_push3) {
                    _push3(`${ssrInterpolate(unref(t)("register"))}`);
                  } else {
                    return [
                      createTextVNode(toDisplayString(unref(t)("register")), 1)
                    ];
                  }
                }),
                _: 1
              }, _parent2, _scopeId));
              _push2(`<!--]-->`);
            }
          } else {
            return [
              isAuth.value ? (openBlock(), createBlock(Fragment, { key: 0 }, [
                createVNode("div", { class: "px-4 py-4 border-b border-gray-200 dark:border-gray-700" }, [
                  createVNode("div", { class: "flex items-center gap-3" }, [
                    createVNode("div", { class: "flex h-10 w-10 shrink-0 items-center justify-center overflow-hidden rounded-full bg-slate-100 text-slate-500 dark:bg-gray-800 dark:text-slate-300" }, [
                      managesProfilePhotos.value && ((_d = user.value) == null ? void 0 : _d.profile_photo_url) ? (openBlock(), createBlock("img", {
                        key: 0,
                        class: "h-full w-full object-cover",
                        src: user.value.profile_photo_url,
                        alt: user.value.name
                      }, null, 8, ["src", "alt"])) : (openBlock(), createBlock("svg", {
                        key: 1,
                        class: "h-5 w-5",
                        fill: "currentColor",
                        viewBox: "0 0 448 512",
                        "aria-hidden": "true"
                      }, [
                        createVNode("path", { d: "M224 256A128 128 0 10224 0a128 128 0 000 256zm89.6 32h-16.7a174.1 174.1 0 01-145.8 0h-16.7A134.4 134.4 0 000 422.4V464a48 48 0 0048 48h352a48 48 0 0048-48v-41.6A134.4 134.4 0 00313.6 288z" })
                      ]))
                    ]),
                    createVNode("div", { class: "min-w-0" }, [
                      createVNode("div", { class: "truncate text-sm font-semibold text-slate-800 dark:text-slate-100" }, toDisplayString((_e = user.value) == null ? void 0 : _e.name), 1),
                      createVNode("div", { class: "mt-0.5 truncate text-xs text-slate-500 dark:text-slate-400" }, toDisplayString((_f = user.value) == null ? void 0 : _f.email), 1)
                    ])
                  ])
                ]),
                createVNode("div", { class: "px-4 py-2 text-xs font-semibold text-slate-400 dark:text-slate-500" }, toDisplayString(unref(t)("accountManagement")), 1),
                createVNode(_sfc_main$b, {
                  href: _ctx.route("profile.show")
                }, {
                  default: withCtx(() => [
                    createTextVNode(toDisplayString(unref(t)("profile")), 1)
                  ]),
                  _: 1
                }, 8, ["href"]),
                createVNode("div", { class: "border-t border-gray-200 dark:border-gray-700" }),
                createVNode("form", {
                  onSubmit: withModifiers(logout, ["prevent"])
                }, [
                  createVNode(_sfc_main$b, { as: "button" }, {
                    default: withCtx(() => [
                      createTextVNode(toDisplayString(unref(t)("logout")), 1)
                    ]),
                    _: 1
                  })
                ], 32)
              ], 64)) : (openBlock(), createBlock(Fragment, { key: 1 }, [
                createVNode("div", { class: "px-4 py-4 border-b border-gray-200 dark:border-gray-700" }, [
                  createVNode("div", { class: "flex items-center gap-3" }, [
                    createVNode("div", { class: "flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-slate-100 text-slate-500 dark:bg-gray-800 dark:text-slate-300" }, [
                      (openBlock(), createBlock("svg", {
                        class: "h-5 w-5",
                        fill: "currentColor",
                        viewBox: "0 0 448 512",
                        "aria-hidden": "true"
                      }, [
                        createVNode("path", { d: "M224 256A128 128 0 10224 0a128 128 0 000 256zm89.6 32h-16.7a174.1 174.1 0 01-145.8 0h-16.7A134.4 134.4 0 000 422.4V464a48 48 0 0048 48h352a48 48 0 0048-48v-41.6A134.4 134.4 0 00313.6 288z" })
                      ]))
                    ]),
                    createVNode("div", null, [
                      createVNode("div", { class: "text-sm font-semibold text-slate-800 dark:text-slate-100" }, toDisplayString(unref(t)("guest")), 1),
                      createVNode("div", { class: "mt-0.5 text-xs text-slate-500 dark:text-slate-400" }, toDisplayString(unref(t)("account")), 1)
                    ])
                  ])
                ]),
                createVNode(_sfc_main$b, {
                  href: _ctx.route("login")
                }, {
                  default: withCtx(() => [
                    createTextVNode(toDisplayString(unref(t)("login")), 1)
                  ]),
                  _: 1
                }, 8, ["href"]),
                createVNode(_sfc_main$b, {
                  href: _ctx.route("register")
                }, {
                  default: withCtx(() => [
                    createTextVNode(toDisplayString(unref(t)("register")), 1)
                  ]),
                  _: 1
                }, 8, ["href"])
              ], 64))
            ];
          }
        }),
        _: 1
      }, _parent));
    };
  }
};
const _sfc_setup$8 = _sfc_main$8.setup;
_sfc_main$8.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Components/Public/Default/Header/Auth/HeaderAccountDropdown.vue");
  return _sfc_setup$8 ? _sfc_setup$8(props, ctx) : void 0;
};
const _sfc_main$7 = {
  __name: "DropdownExt",
  __ssrInlineRender: true,
  props: {
    align: {
      type: String,
      default: "right"
    },
    width: {
      type: String,
      default: "48"
    },
    contentClasses: {
      type: Array,
      default: () => [
        "py-0",
        "bg-white"
      ]
    },
    /**
     * Обычные Dropdown закрываются после клика
     * по содержимому.
     *
     * Для интерактивных Dropdown с формами
     * можно отключить это поведение.
     */
    closeOnContentClick: {
      type: Boolean,
      default: true
    }
  },
  setup(__props, { expose: __expose }) {
    const props = __props;
    const open = ref(false);
    const close = () => {
      open.value = false;
    };
    const toggle = () => {
      open.value = !open.value;
    };
    const closeOnEscape = (event) => {
      if (open.value && event.key === "Escape") {
        close();
      }
    };
    onMounted(() => {
      document.addEventListener(
        "keydown",
        closeOnEscape
      );
    });
    onUnmounted(() => {
      document.removeEventListener(
        "keydown",
        closeOnEscape
      );
    });
    const widthClass = computed(() => {
      return {
        48: "w-48",
        56: "w-56",
        60: "w-60",
        64: "w-64",
        72: "w-72",
        80: "w-80",
        96: "w-96"
      }[props.width.toString()] || "w-48";
    });
    const alignmentClasses = computed(() => {
      if (props.align === "left") {
        return "ltr:origin-top-left rtl:origin-top-right start-0";
      }
      if (props.align === "right") {
        return "ltr:origin-top-right rtl:origin-top-left end-0";
      }
      return "origin-top";
    });
    __expose({
      open,
      close,
      toggle
    });
    return (_ctx, _push, _parent, _attrs) => {
      _push(`<div${ssrRenderAttrs(mergeProps({ class: "relative" }, _attrs))}><div>`);
      ssrRenderSlot(_ctx.$slots, "trigger", {}, null, _push, _parent);
      _push(`</div><div style="${ssrRenderStyle(open.value ? null : { display: "none" })}" class="fixed inset-0 z-10"></div><div style="${ssrRenderStyle([
        open.value ? null : { display: "none" },
        { "display": "none" }
      ])}" class="${ssrRenderClass([[
        widthClass.value,
        alignmentClasses.value
      ], "absolute z-20 mt-2 rounded-md shadow-lg"])}"><div class="${ssrRenderClass([__props.contentClasses, "w-full rounded-md ring-1 ring-black ring-opacity-5 dark:bg-slate-900 dark:border dark:border-gray-100"])}">`);
      ssrRenderSlot(_ctx.$slots, "content", { close }, null, _push, _parent);
      _push(`</div></div></div>`);
    };
  }
};
const _sfc_setup$7 = _sfc_main$7.setup;
_sfc_main$7.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Components/Base/DropdownExt.vue");
  return _sfc_setup$7 ? _sfc_setup$7(props, ctx) : void 0;
};
const _sfc_main$6 = {
  __name: "HeaderLoginForm",
  __ssrInlineRender: true,
  props: {
    canResetPassword: {
      type: Boolean,
      default: false
    },
    status: {
      type: String,
      default: ""
    }
  },
  emits: [
    "forgot",
    "register"
  ],
  setup(__props, { emit: __emit }) {
    const { t } = useI18n();
    const form = useForm({
      email: "",
      password: "",
      remember: false
    });
    return (_ctx, _push, _parent, _attrs) => {
      _push(`<div${ssrRenderAttrs(_attrs)}>`);
      if (__props.status) {
        _push(`<div class="mb-4 rounded-lg bg-emerald-50 px-3 py-2 text-sm font-medium text-emerald-700 dark:bg-emerald-950/40 dark:text-emerald-300">${ssrInterpolate(__props.status)}</div>`);
      } else {
        _push(`<!---->`);
      }
      _push(`<form><div>`);
      _push(ssrRenderComponent(_sfc_main$c, {
        for: "header-login-email",
        value: unref(t)("email")
      }, null, _parent));
      _push(ssrRenderComponent(_sfc_main$d, {
        id: "header-login-email",
        modelValue: unref(form).email,
        "onUpdate:modelValue": ($event) => unref(form).email = $event,
        type: "email",
        class: "mt-1 block w-full",
        required: "",
        autocomplete: "username"
      }, null, _parent));
      _push(ssrRenderComponent(_sfc_main$e, {
        class: "mt-2",
        message: unref(form).errors.email
      }, null, _parent));
      _push(`</div><div class="mt-3">`);
      _push(ssrRenderComponent(_sfc_main$c, {
        for: "header-login-password",
        value: unref(t)("password")
      }, null, _parent));
      _push(ssrRenderComponent(_sfc_main$d, {
        id: "header-login-password",
        modelValue: unref(form).password,
        "onUpdate:modelValue": ($event) => unref(form).password = $event,
        type: "password",
        class: "mt-1 block w-full",
        required: "",
        autocomplete: "current-password"
      }, null, _parent));
      _push(ssrRenderComponent(_sfc_main$e, {
        class: "mt-2",
        message: unref(form).errors.password
      }, null, _parent));
      _push(`</div><div class="mt-3"><label class="flex cursor-pointer items-center">`);
      _push(ssrRenderComponent(_sfc_main$f, {
        checked: unref(form).remember,
        "onUpdate:checked": ($event) => unref(form).remember = $event,
        name: "remember"
      }, null, _parent));
      _push(`<span class="ml-2 text-sm text-slate-600 dark:text-slate-300">${ssrInterpolate(unref(t)("rememberMe"))}</span></label></div><div class="mt-4 flex items-center justify-between gap-3">`);
      if (__props.canResetPassword) {
        _push(`<button type="button" class="text-sm font-medium text-blue-600 hover:underline dark:text-blue-300">${ssrInterpolate(unref(t)("forgotPassword"))}</button>`);
      } else {
        _push(`<!---->`);
      }
      _push(ssrRenderComponent(_sfc_main$g, {
        class: ["ml-auto", {
          "opacity-25": unref(form).processing
        }],
        disabled: unref(form).processing
      }, {
        default: withCtx((_, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(`${ssrInterpolate(unref(t)("login"))}`);
          } else {
            return [
              createTextVNode(toDisplayString(unref(t)("login")), 1)
            ];
          }
        }),
        _: 1
      }, _parent));
      _push(`</div></form><div class="mt-4 border-t border-slate-200 pt-3 text-center dark:border-slate-700"><span class="text-sm text-slate-500 dark:text-slate-400">${ssrInterpolate(unref(t)("registerPrompt"))}</span><button type="button" class="ml-1 text-sm font-semibold text-blue-600 hover:underline dark:text-blue-300">${ssrInterpolate(unref(t)("register"))}</button></div></div>`);
    };
  }
};
const _sfc_setup$6 = _sfc_main$6.setup;
_sfc_main$6.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Components/Public/Default/Header/Auth/HeaderLoginForm.vue");
  return _sfc_setup$6 ? _sfc_setup$6(props, ctx) : void 0;
};
const _sfc_main$5 = {
  __name: "HeaderRegisterForm",
  __ssrInlineRender: true,
  emits: [
    "login"
  ],
  setup(__props, { emit: __emit }) {
    const { t } = useI18n();
    const page = usePage();
    const form = useForm({
      name: "",
      email: "",
      password: "",
      password_confirmation: "",
      terms: false
    });
    return (_ctx, _push, _parent, _attrs) => {
      var _a, _b;
      _push(`<div${ssrRenderAttrs(_attrs)}><form><div>`);
      _push(ssrRenderComponent(_sfc_main$c, {
        for: "header-register-name",
        value: unref(t)("name")
      }, null, _parent));
      _push(ssrRenderComponent(_sfc_main$d, {
        id: "header-register-name",
        modelValue: unref(form).name,
        "onUpdate:modelValue": ($event) => unref(form).name = $event,
        type: "text",
        class: "mt-1 block w-full",
        required: "",
        autocomplete: "name"
      }, null, _parent));
      _push(ssrRenderComponent(_sfc_main$e, {
        class: "mt-2",
        message: unref(form).errors.name
      }, null, _parent));
      _push(`</div><div class="mt-3">`);
      _push(ssrRenderComponent(_sfc_main$c, {
        for: "header-register-email",
        value: unref(t)("email")
      }, null, _parent));
      _push(ssrRenderComponent(_sfc_main$d, {
        id: "header-register-email",
        modelValue: unref(form).email,
        "onUpdate:modelValue": ($event) => unref(form).email = $event,
        type: "email",
        class: "mt-1 block w-full",
        required: "",
        autocomplete: "username"
      }, null, _parent));
      _push(ssrRenderComponent(_sfc_main$e, {
        class: "mt-2",
        message: unref(form).errors.email
      }, null, _parent));
      _push(`</div><div class="mt-3">`);
      _push(ssrRenderComponent(_sfc_main$c, {
        for: "header-register-password",
        value: unref(t)("password")
      }, null, _parent));
      _push(ssrRenderComponent(_sfc_main$d, {
        id: "header-register-password",
        modelValue: unref(form).password,
        "onUpdate:modelValue": ($event) => unref(form).password = $event,
        type: "password",
        class: "mt-1 block w-full",
        required: "",
        autocomplete: "new-password"
      }, null, _parent));
      _push(ssrRenderComponent(_sfc_main$e, {
        class: "mt-2",
        message: unref(form).errors.password
      }, null, _parent));
      _push(`</div><div class="mt-3">`);
      _push(ssrRenderComponent(_sfc_main$c, {
        for: "header-register-password-confirmation",
        value: unref(t)("confirmPassword")
      }, null, _parent));
      _push(ssrRenderComponent(_sfc_main$d, {
        id: "header-register-password-confirmation",
        modelValue: unref(form).password_confirmation,
        "onUpdate:modelValue": ($event) => unref(form).password_confirmation = $event,
        type: "password",
        class: "mt-1 block w-full",
        required: "",
        autocomplete: "new-password"
      }, null, _parent));
      _push(ssrRenderComponent(_sfc_main$e, {
        class: "mt-2",
        message: unref(form).errors.password_confirmation
      }, null, _parent));
      _push(`</div>`);
      if ((_b = (_a = unref(page).props) == null ? void 0 : _a.jetstream) == null ? void 0 : _b.hasTermsAndPrivacyPolicyFeature) {
        _push(`<div class="mt-2"><label class="flex items-start gap-2 text-sm text-slate-600 dark:text-slate-300">`);
        _push(ssrRenderComponent(_sfc_main$f, {
          id: "header-register-terms",
          checked: unref(form).terms,
          "onUpdate:checked": ($event) => unref(form).terms = $event,
          name: "terms",
          required: ""
        }, null, _parent));
        _push(`<span>${ssrInterpolate(unref(t)("agreeTerms1"))} <a${ssrRenderAttr("href", _ctx.route("terms.show"))} target="_blank" class="font-semibold text-blue-600 hover:underline dark:text-blue-300">${ssrInterpolate(unref(t)("termsOfService"))}</a> ${ssrInterpolate(unref(t)("agreeTerms2"))} <a${ssrRenderAttr("href", _ctx.route("policy.show"))} target="_blank" class="font-semibold text-blue-600 hover:underline dark:text-blue-300">${ssrInterpolate(unref(t)("privacyPolicy"))}</a></span></label>`);
        _push(ssrRenderComponent(_sfc_main$e, {
          class: "mt-2",
          message: unref(form).errors.terms
        }, null, _parent));
        _push(`</div>`);
      } else {
        _push(`<!---->`);
      }
      _push(`<div class="mt-2 flex items-center justify-end">`);
      _push(ssrRenderComponent(_sfc_main$g, {
        class: {
          "opacity-25": unref(form).processing
        },
        disabled: unref(form).processing
      }, {
        default: withCtx((_, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(`${ssrInterpolate(unref(t)("register"))}`);
          } else {
            return [
              createTextVNode(toDisplayString(unref(t)("register")), 1)
            ];
          }
        }),
        _: 1
      }, _parent));
      _push(`</div></form><div class="mt-2 border-t border-slate-200 pt-3 text-center dark:border-slate-700"><button type="button" class="text-sm font-semibold text-blue-600 hover:underline dark:text-blue-300">${ssrInterpolate(unref(t)("alreadyRegistered"))}</button></div></div>`);
    };
  }
};
const _sfc_setup$5 = _sfc_main$5.setup;
_sfc_main$5.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Components/Public/Default/Header/Auth/HeaderRegisterForm.vue");
  return _sfc_setup$5 ? _sfc_setup$5(props, ctx) : void 0;
};
const _sfc_main$4 = {
  __name: "HeaderForgotPasswordForm",
  __ssrInlineRender: true,
  props: {
    status: {
      type: String,
      default: ""
    }
  },
  emits: [
    "login"
  ],
  setup(__props, { emit: __emit }) {
    const props = __props;
    const { t } = useI18n();
    const form = useForm({
      email: ""
    });
    const successfulStatus = computed(() => {
      return props.status || "";
    });
    return (_ctx, _push, _parent, _attrs) => {
      _push(`<div${ssrRenderAttrs(_attrs)}><button type="button" class="mb-4 inline-flex items-center gap-1 text-sm font-semibold text-blue-600 hover:underline dark:text-blue-300"><span>←</span><span>${ssrInterpolate(unref(t)("login"))}</span></button><div class="mb-4 text-sm font-semibold text-center text-slate-600 dark:text-slate-300">${ssrInterpolate(unref(t)("forgotPassword"))}</div>`);
      if (successfulStatus.value) {
        _push(`<div class="mb-4 rounded-lg bg-emerald-50 px-3 py-2 text-sm font-medium text-emerald-700 dark:bg-emerald-950/40 dark:text-emerald-300">${ssrInterpolate(successfulStatus.value)}</div>`);
      } else {
        _push(`<!---->`);
      }
      _push(`<form><div>`);
      _push(ssrRenderComponent(_sfc_main$c, {
        for: "header-forgot-email",
        value: unref(t)("email")
      }, null, _parent));
      _push(ssrRenderComponent(_sfc_main$d, {
        id: "header-forgot-email",
        modelValue: unref(form).email,
        "onUpdate:modelValue": ($event) => unref(form).email = $event,
        type: "email",
        class: "mt-1 block w-full",
        required: "",
        autocomplete: "username"
      }, null, _parent));
      _push(ssrRenderComponent(_sfc_main$e, {
        class: "mt-2",
        message: unref(form).errors.email
      }, null, _parent));
      _push(`</div><div class="mt-4 flex items-center justify-center">`);
      _push(ssrRenderComponent(_sfc_main$g, {
        class: {
          "opacity-25": unref(form).processing
        },
        disabled: unref(form).processing
      }, {
        default: withCtx((_, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(`${ssrInterpolate(unref(t)("emailPasswordResetLink"))}`);
          } else {
            return [
              createTextVNode(toDisplayString(unref(t)("emailPasswordResetLink")), 1)
            ];
          }
        }),
        _: 1
      }, _parent));
      _push(`</div></form></div>`);
    };
  }
};
const _sfc_setup$4 = _sfc_main$4.setup;
_sfc_main$4.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Components/Public/Default/Header/Auth/HeaderForgotPasswordForm.vue");
  return _sfc_setup$4 ? _sfc_setup$4(props, ctx) : void 0;
};
const _sfc_main$3 = {
  __name: "HeaderAuthDropdown",
  __ssrInlineRender: true,
  setup(__props) {
    const { t } = useI18n();
    const page = usePage();
    const view = ref("login");
    const canResetPassword = computed(() => {
      var _a;
      return Boolean(
        ((_a = page.props) == null ? void 0 : _a.canResetPassword) ?? true
      );
    });
    const status = computed(() => {
      var _a;
      return ((_a = page.props) == null ? void 0 : _a.status) || "";
    });
    const setView = (value) => {
      view.value = value;
    };
    return (_ctx, _push, _parent, _attrs) => {
      _push(ssrRenderComponent(_sfc_main$7, mergeProps({
        align: "right",
        width: "96",
        "close-on-content-click": false,
        "content-classes": [
          "bg-white",
          "dark:bg-slate-900"
        ]
      }, _attrs), {
        trigger: withCtx((_, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(`<button type="button" class="inline-flex h-9 w-9 items-center justify-center rounded-full border border-slate-200 bg-white text-slate-600 transition hover:border-blue-300 hover:bg-slate-100 hover:text-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-300 dark:border-slate-700 dark:bg-gray-900 dark:text-slate-300 dark:hover:border-blue-700 dark:hover:bg-gray-800 dark:hover:text-blue-300"${ssrRenderAttr("title", unref(t)("account"))}${ssrRenderAttr("aria-label", unref(t)("account"))}${_scopeId}><svg class="h-4 w-4" fill="currentColor" viewBox="0 0 448 512" aria-hidden="true"${_scopeId}><path d="M224 256A128 128 0 10224 0a128 128 0 000 256zm89.6 32h-16.7a174.1 174.1 0 01-145.8 0h-16.7A134.4 134.4 0 000 422.4V464a48 48 0 0048 48h352a48 48 0 0048-48v-41.6A134.4 134.4 0 00313.6 288z"${_scopeId}></path></svg></button>`);
          } else {
            return [
              createVNode("button", {
                type: "button",
                class: "inline-flex h-9 w-9 items-center justify-center rounded-full border border-slate-200 bg-white text-slate-600 transition hover:border-blue-300 hover:bg-slate-100 hover:text-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-300 dark:border-slate-700 dark:bg-gray-900 dark:text-slate-300 dark:hover:border-blue-700 dark:hover:bg-gray-800 dark:hover:text-blue-300",
                title: unref(t)("account"),
                "aria-label": unref(t)("account")
              }, [
                (openBlock(), createBlock("svg", {
                  class: "h-4 w-4",
                  fill: "currentColor",
                  viewBox: "0 0 448 512",
                  "aria-hidden": "true"
                }, [
                  createVNode("path", { d: "M224 256A128 128 0 10224 0a128 128 0 000 256zm89.6 32h-16.7a174.1 174.1 0 01-145.8 0h-16.7A134.4 134.4 0 000 422.4V464a48 48 0 0048 48h352a48 48 0 0048-48v-41.6A134.4 134.4 0 00313.6 288z" })
                ]))
              ], 8, ["title", "aria-label"])
            ];
          }
        }),
        content: withCtx((_, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(`<div class="w-96 max-w-[calc(100vw-2rem)] overflow-hidden rounded-xl bg-white dark:bg-slate-900"${_scopeId}><div class="border-b border-slate-200 dark:border-slate-700"${_scopeId}>`);
            if (view.value !== "forgot-password") {
              _push2(`<div class="grid grid-cols-2"${_scopeId}><button type="button" class="${ssrRenderClass([
                view.value === "login" ? "text-blue-700 dark:text-blue-300" : "text-slate-500 hover:text-slate-800 dark:text-slate-400 dark:hover:text-slate-200",
                "relative px-4 py-3 text-sm font-semibold transition"
              ])}"${_scopeId}><span class="flex flex-row items-center justify-center gap-2"${_scopeId}><svg viewBox="0 0 512 512" class="h-4 w-4" fill="currentColor"${_scopeId}><path d="M416 448h-84c-6.6 0-12-5.4-12-12v-40c0-6.6 5.4-12 12-12h84c17.7 0 32-14.3 32-32V160c0-17.7-14.3-32-32-32h-84c-6.6 0-12-5.4-12-12V76c0-6.6 5.4-12 12-12h84c53 0 96 43 96 96v192c0 53-43 96-96 96zm-47-201L201 79c-15-15-41-4.5-41 17v96H24c-13.3 0-24 10.7-24 24v96c0 13.3 10.7 24 24 24h136v96c0 21.5 26 32 41 17l168-168c9.3-9.4 9.3-24.6 0-34z"${_scopeId}></path></svg> ${ssrInterpolate(unref(t)("login"))}</span>`);
              if (view.value === "login") {
                _push2(`<span class="absolute bottom-0 left-4 right-4 h-0.5 rounded-full bg-blue-600 dark:bg-blue-400"${_scopeId}></span>`);
              } else {
                _push2(`<!---->`);
              }
              _push2(`</button><button type="button" class="${ssrRenderClass([
                view.value === "register" ? "text-blue-700 dark:text-blue-300" : "text-slate-500 hover:text-slate-800 dark:text-slate-400 dark:hover:text-slate-200",
                "relative px-4 py-3 text-sm font-semibold transition"
              ])}"${_scopeId}><span class="flex flex-row items-center justify-center gap-2"${_scopeId}><svg viewBox="0 0 512 512" class="h-3 w-3" fill="currentColor"${_scopeId}><path d="M512 176.001C512 273.203 433.202 352 336 352c-11.22 0-22.19-1.062-32.827-3.069l-24.012 27.014A23.999 23.999 0 0 1 261.223 384H224v40c0 13.255-10.745 24-24 24h-40v40c0 13.255-10.745 24-24 24H24c-13.255 0-24-10.745-24-24v-78.059c0-6.365 2.529-12.47 7.029-16.971l161.802-161.802C163.108 213.814 160 195.271 160 176 160 78.798 238.797.001 335.999 0 433.488-.001 512 78.511 512 176.001zM336 128c0 26.51 21.49 48 48 48s48-21.49 48-48-21.49-48-48-48-48 21.49-48 48z"${_scopeId}></path></svg> ${ssrInterpolate(unref(t)("register"))}</span>`);
              if (view.value === "register") {
                _push2(`<span class="absolute bottom-0 left-4 right-4 h-0.5 rounded-full bg-blue-600 dark:bg-blue-400"${_scopeId}></span>`);
              } else {
                _push2(`<!---->`);
              }
              _push2(`</button></div>`);
            } else {
              _push2(`<div class="px-5 py-3"${_scopeId}><div class="text-sm font-semibold text-slate-800 dark:text-slate-100"${_scopeId}>${ssrInterpolate(unref(t)("forgotPassword"))}</div></div>`);
            }
            _push2(`</div><div class="max-h-[calc(100vh-8rem)] overflow-y-auto overscroll-contain px-5 py-4"${_scopeId}>`);
            if (view.value === "login") {
              _push2(ssrRenderComponent(_sfc_main$6, {
                "can-reset-password": canResetPassword.value,
                status: status.value,
                onForgot: ($event) => setView("forgot-password"),
                onRegister: ($event) => setView("register")
              }, null, _parent2, _scopeId));
            } else if (view.value === "register") {
              _push2(ssrRenderComponent(_sfc_main$5, {
                onLogin: ($event) => setView("login")
              }, null, _parent2, _scopeId));
            } else {
              _push2(ssrRenderComponent(_sfc_main$4, {
                status: status.value,
                onLogin: ($event) => setView("login")
              }, null, _parent2, _scopeId));
            }
            _push2(`</div></div>`);
          } else {
            return [
              createVNode("div", { class: "w-96 max-w-[calc(100vw-2rem)] overflow-hidden rounded-xl bg-white dark:bg-slate-900" }, [
                createVNode("div", { class: "border-b border-slate-200 dark:border-slate-700" }, [
                  view.value !== "forgot-password" ? (openBlock(), createBlock("div", {
                    key: 0,
                    class: "grid grid-cols-2"
                  }, [
                    createVNode("button", {
                      type: "button",
                      class: [
                        "relative px-4 py-3 text-sm font-semibold transition",
                        view.value === "login" ? "text-blue-700 dark:text-blue-300" : "text-slate-500 hover:text-slate-800 dark:text-slate-400 dark:hover:text-slate-200"
                      ],
                      onClick: ($event) => setView("login")
                    }, [
                      createVNode("span", { class: "flex flex-row items-center justify-center gap-2" }, [
                        (openBlock(), createBlock("svg", {
                          viewBox: "0 0 512 512",
                          class: "h-4 w-4",
                          fill: "currentColor"
                        }, [
                          createVNode("path", { d: "M416 448h-84c-6.6 0-12-5.4-12-12v-40c0-6.6 5.4-12 12-12h84c17.7 0 32-14.3 32-32V160c0-17.7-14.3-32-32-32h-84c-6.6 0-12-5.4-12-12V76c0-6.6 5.4-12 12-12h84c53 0 96 43 96 96v192c0 53-43 96-96 96zm-47-201L201 79c-15-15-41-4.5-41 17v96H24c-13.3 0-24 10.7-24 24v96c0 13.3 10.7 24 24 24h136v96c0 21.5 26 32 41 17l168-168c9.3-9.4 9.3-24.6 0-34z" })
                        ])),
                        createTextVNode(" " + toDisplayString(unref(t)("login")), 1)
                      ]),
                      view.value === "login" ? (openBlock(), createBlock("span", {
                        key: 0,
                        class: "absolute bottom-0 left-4 right-4 h-0.5 rounded-full bg-blue-600 dark:bg-blue-400"
                      })) : createCommentVNode("", true)
                    ], 10, ["onClick"]),
                    createVNode("button", {
                      type: "button",
                      class: [
                        "relative px-4 py-3 text-sm font-semibold transition",
                        view.value === "register" ? "text-blue-700 dark:text-blue-300" : "text-slate-500 hover:text-slate-800 dark:text-slate-400 dark:hover:text-slate-200"
                      ],
                      onClick: ($event) => setView("register")
                    }, [
                      createVNode("span", { class: "flex flex-row items-center justify-center gap-2" }, [
                        (openBlock(), createBlock("svg", {
                          viewBox: "0 0 512 512",
                          class: "h-3 w-3",
                          fill: "currentColor"
                        }, [
                          createVNode("path", { d: "M512 176.001C512 273.203 433.202 352 336 352c-11.22 0-22.19-1.062-32.827-3.069l-24.012 27.014A23.999 23.999 0 0 1 261.223 384H224v40c0 13.255-10.745 24-24 24h-40v40c0 13.255-10.745 24-24 24H24c-13.255 0-24-10.745-24-24v-78.059c0-6.365 2.529-12.47 7.029-16.971l161.802-161.802C163.108 213.814 160 195.271 160 176 160 78.798 238.797.001 335.999 0 433.488-.001 512 78.511 512 176.001zM336 128c0 26.51 21.49 48 48 48s48-21.49 48-48-21.49-48-48-48-48 21.49-48 48z" })
                        ])),
                        createTextVNode(" " + toDisplayString(unref(t)("register")), 1)
                      ]),
                      view.value === "register" ? (openBlock(), createBlock("span", {
                        key: 0,
                        class: "absolute bottom-0 left-4 right-4 h-0.5 rounded-full bg-blue-600 dark:bg-blue-400"
                      })) : createCommentVNode("", true)
                    ], 10, ["onClick"])
                  ])) : (openBlock(), createBlock("div", {
                    key: 1,
                    class: "px-5 py-3"
                  }, [
                    createVNode("div", { class: "text-sm font-semibold text-slate-800 dark:text-slate-100" }, toDisplayString(unref(t)("forgotPassword")), 1)
                  ]))
                ]),
                createVNode("div", { class: "max-h-[calc(100vh-8rem)] overflow-y-auto overscroll-contain px-5 py-4" }, [
                  view.value === "login" ? (openBlock(), createBlock(_sfc_main$6, {
                    key: 0,
                    "can-reset-password": canResetPassword.value,
                    status: status.value,
                    onForgot: ($event) => setView("forgot-password"),
                    onRegister: ($event) => setView("register")
                  }, null, 8, ["can-reset-password", "status", "onForgot", "onRegister"])) : view.value === "register" ? (openBlock(), createBlock(_sfc_main$5, {
                    key: 1,
                    onLogin: ($event) => setView("login")
                  }, null, 8, ["onLogin"])) : (openBlock(), createBlock(_sfc_main$4, {
                    key: 2,
                    status: status.value,
                    onLogin: ($event) => setView("login")
                  }, null, 8, ["status", "onLogin"]))
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
const _sfc_setup$3 = _sfc_main$3.setup;
_sfc_main$3.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Components/Public/Default/Header/Auth/HeaderAuthDropdown.vue");
  return _sfc_setup$3 ? _sfc_setup$3(props, ctx) : void 0;
};
const _sfc_main$2 = {
  __name: "Navbar",
  __ssrInlineRender: true,
  setup(__props) {
    const { t, locale } = useI18n();
    const page = usePage();
    const isAuth = computed(() => {
      var _a, _b;
      return !!((_b = (_a = page.props) == null ? void 0 : _a.auth) == null ? void 0 : _b.user);
    });
    const cmsMenu = computed(() => {
      var _a;
      return ((_a = page.props) == null ? void 0 : _a.cmsMenu) || [];
    });
    const marketCatalog = computed(() => {
      var _a;
      return ((_a = page.props) == null ? void 0 : _a.marketCatalog) || [];
    });
    const isCatalogOpen = ref(false);
    const showingNavigationDropdown = ref(false);
    const isSolid = ref(false);
    const availableLocales = computed(() => {
      var _a, _b;
      const locales = ((_a = page.props) == null ? void 0 : _a.availableLocales) || ((_b = page.props) == null ? void 0 : _b.locales);
      return Array.isArray(locales) && locales.length ? locales.map((item) => String(item).trim().toLowerCase()).filter(Boolean) : [String(locale.value || "ru").toLowerCase()];
    });
    const selectedLocale = ref(locale.value);
    const currentLocale = computed(() => {
      var _a;
      return ((_a = page.props) == null ? void 0 : _a.locale) || locale.value || "ru";
    });
    watch(() => locale.value, (newLocale) => {
      if (selectedLocale.value !== newLocale) {
        selectedLocale.value = newLocale;
      }
    });
    watch(selectedLocale, (newLocale) => {
      const targetLocale = String(newLocale || "").toLowerCase();
      const current = String(locale.value || "").toLowerCase();
      if (!targetLocale || targetLocale === current) return;
      if (!availableLocales.value.includes(targetLocale)) return;
      locale.value = targetLocale;
      const pathSegments = window.location.pathname.split("/").filter(Boolean);
      if (pathSegments.length > 0 && availableLocales.value.includes(pathSegments[0].toLowerCase())) {
        pathSegments[0] = targetLocale;
      } else {
        pathSegments.unshift(targetLocale);
      }
      Inertia.visit(`/${pathSegments.join("/")}${window.location.search}`, {
        preserveState: false,
        preserveScroll: true,
        replace: true
      });
    });
    const getTranslation = (item) => {
      var _a;
      if (!((_a = item == null ? void 0 : item.translations) == null ? void 0 : _a.length)) {
        return (item == null ? void 0 : item.translation) || null;
      }
      return item.translations.find((translation) => translation.locale === currentLocale.value) || item.translations.find((translation) => translation.locale === "ru") || item.translations[0] || null;
    };
    const getTitle = (item) => {
      var _a;
      return (item == null ? void 0 : item.title) || ((_a = getTranslation(item)) == null ? void 0 : _a.title) || `ID: ${item == null ? void 0 : item.id}`;
    };
    const getPageChildren = (item) => (item == null ? void 0 : item.public_menu_children) || [];
    const getCategoryChildren = (item) => (item == null ? void 0 : item.public_catalog_children) || [];
    const normalizePath = (url) => {
      if (!url) return "/";
      const cleanUrl = String(url).trim();
      if (cleanUrl.startsWith("http")) {
        return cleanUrl;
      }
      return cleanUrl.startsWith("/") ? cleanUrl : `/${cleanUrl}`;
    };
    const getHref = (item) => {
      const url = normalizePath(item == null ? void 0 : item.url);
      const pathSegments = window.location.pathname.split("/").filter(Boolean);
      const hasLocalePrefix = pathSegments.length > 0 && availableLocales.value.includes(pathSegments[0].toLowerCase());
      if (!hasLocalePrefix) return url;
      const urlSegments = url.split("/").filter(Boolean);
      if (urlSegments.length > 0 && availableLocales.value.includes(urlSegments[0].toLowerCase())) {
        return url;
      }
      return `/${currentLocale.value}${url === "/" ? "" : url}`;
    };
    const isActiveUrl = (url) => {
      const cleanUrl = normalizePath(url);
      const currentPath = window.location.pathname;
      return currentPath === cleanUrl || currentPath.endsWith(cleanUrl) || currentPath.includes(`${cleanUrl}/`);
    };
    const isPageActive = (item) => {
      if (isActiveUrl(item == null ? void 0 : item.url)) return true;
      return getPageChildren(item).some((child) => {
        if (isActiveUrl(child == null ? void 0 : child.url)) return true;
        return getPageChildren(child).some((subChild) => isActiveUrl(subChild == null ? void 0 : subChild.url));
      });
    };
    const isCategoryActive = (item) => {
      if (isActiveUrl(item == null ? void 0 : item.url)) return true;
      return getCategoryChildren(item).some((child) => {
        if (isActiveUrl(child == null ? void 0 : child.url)) return true;
        return getCategoryChildren(child).some((subChild) => isActiveUrl(subChild == null ? void 0 : subChild.url));
      });
    };
    const closeCatalog = () => {
      isCatalogOpen.value = false;
    };
    const closeMobileMenu = () => {
      showingNavigationDropdown.value = false;
    };
    const closeAllMenus = () => {
      closeCatalog();
      closeMobileMenu();
    };
    watch(showingNavigationDropdown, (isOpen) => {
      document.body.classList.toggle("overflow-hidden", isOpen);
    });
    const handleScroll = () => {
      isSolid.value = (window.scrollY || window.pageYOffset || 0) > 40;
    };
    onMounted(() => {
      window.addEventListener("scroll", handleScroll, { passive: true });
      handleScroll();
    });
    onBeforeUnmount(() => {
      window.removeEventListener("scroll", handleScroll);
      document.body.classList.remove("overflow-hidden");
    });
    watch(() => page.url, () => closeAllMenus());
    return (_ctx, _push, _parent, _attrs) => {
      _push(`<!--[--><nav class="${ssrRenderClass([[
        isSolid.value ? "bg-white/95 dark:bg-gray-900/95 backdrop-blur-md" : "bg-white/80 dark:bg-slate-800/80 backdrop-blur",
        isCatalogOpen.value || showingNavigationDropdown.value ? "bg-white/95 dark:bg-gray-900/95" : "",
        isCatalogOpen.value ? "" : "rounded-b-3xl"
      ], "fixed top-0 left-1 right-1 lg:left-6 lg:right-6 z-[100] border-2 border-slate-300 dark:border-slate-500 transition-[background-color,backdrop-filter] duration-300 ease-out"])}"><div><div class="relative z-50 px-1 lg:px-12"><div class="grid h-16 items-center gap-2 grid-cols-[1fr_auto] sm:grid-cols-[180px_1fr_auto] lg:grid-cols-[200px_1fr_200px] xl:grid-cols-[220px_1fr_220px] 2xl:grid-cols-[240px_1fr_240px] lg:gap-6"><div class="flex items-center lg:justify-between gap-2 min-w-0"><button type="button" class="inline-flex items-center justify-center lg:hidden rounded-md p-1 text-gray-600 hover:bg-gray-100 hover:text-gray-900 dark:text-gray-300 dark:hover:bg-gray-800 dark:hover:text-white focus:outline-none focus:ring-2 focus:ring-blue-300 transition"><svg class="h-5 w-5" stroke="currentColor" fill="none" viewBox="0 0 24 24">`);
      if (!showingNavigationDropdown.value) {
        _push(`<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>`);
      } else {
        _push(`<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>`);
      }
      _push(`</svg></button>`);
      _push(ssrRenderComponent(unref(Link), {
        href: _ctx.route("home"),
        class: "flex items-center justify-center gap-1 shrink-0 logo",
        onClick: closeAllMenus
      }, {
        default: withCtx((_, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(`<svg class="w-6 h-6 fill-current text-teal-500" viewBox="0 0 576 512"${_scopeId}><path d="M546.2 9.7c-5.6-12.5-21.6-13-28.3-1.2C486.9 62.4 431.4 96 368 96h-80C182 96 96 182 96 288c0 7 .8 13.7 1.5 20.5C161.3 262.8 253.4 224 384 224c8.8 0 16 7.2 16 16s-7.2 16-16 16C132.6 256 26 410.1 2.4 468c-6.6 16.3 1.2 34.9 17.5 41.6 16.4 6.8 35-1.1 41.8-17.3 1.5-3.6 20.9-47.9 71.9-90.6 32.4 43.9 94 85.8 174.9 77.2C465.5 467.5 576 326.7 576 154.3c0-50.2-10.8-102.2-29.8-144.6z"${_scopeId}></path></svg><span class="inline-flex font-bold text-xl sm:text-2xl text-blue-600 truncate"${_scopeId}> AGROVENT </span>`);
          } else {
            return [
              (openBlock(), createBlock("svg", {
                class: "w-6 h-6 fill-current text-teal-500",
                viewBox: "0 0 576 512"
              }, [
                createVNode("path", { d: "M546.2 9.7c-5.6-12.5-21.6-13-28.3-1.2C486.9 62.4 431.4 96 368 96h-80C182 96 96 182 96 288c0 7 .8 13.7 1.5 20.5C161.3 262.8 253.4 224 384 224c8.8 0 16 7.2 16 16s-7.2 16-16 16C132.6 256 26 410.1 2.4 468c-6.6 16.3 1.2 34.9 17.5 41.6 16.4 6.8 35-1.1 41.8-17.3 1.5-3.6 20.9-47.9 71.9-90.6 32.4 43.9 94 85.8 174.9 77.2C465.5 467.5 576 326.7 576 154.3c0-50.2-10.8-102.2-29.8-144.6z" })
              ])),
              createVNode("span", { class: "inline-flex font-bold text-xl sm:text-2xl text-blue-600 truncate" }, " AGROVENT ")
            ];
          }
        }),
        _: 1
      }, _parent));
      _push(`<button type="button" class="hidden lg:inline-flex items-center gap-2 rounded-lg bg-blue-600 px-2.5 py-2.5 text-sm font-bold text-white hover:bg-blue-700 active:bg-blue-800 transition"><svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">`);
      if (!isCatalogOpen.value) {
        _push(`<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>`);
      } else {
        _push(`<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>`);
      }
      _push(`</svg></button></div><div class="hidden lg:block w-full min-w-0">`);
      _push(ssrRenderComponent(_sfc_main$9, null, null, _parent));
      _push(`</div><div class="flex justify-end items-center gap-1 sm:gap-2 min-w-0">`);
      _push(ssrRenderComponent(_sfc_main$h, { class: "relative" }, null, _parent));
      _push(ssrRenderComponent(_sfc_main$i, {
        modelValue: selectedLocale.value,
        "onUpdate:modelValue": ($event) => selectedLocale.value = $event,
        locales: availableLocales.value,
        placement: "bottom-end"
      }, null, _parent));
      if (isAuth.value) {
        _push(ssrRenderComponent(_sfc_main$8, null, null, _parent));
      } else {
        _push(ssrRenderComponent(_sfc_main$3, null, null, _parent));
      }
      _push(`</div></div></div>`);
      if (cmsMenu.value.length) {
        _push(`<div class="relative z-10 hidden lg:flex h-10 px-6 items-center justify-center gap-6"><!--[-->`);
        ssrRenderList(cmsMenu.value, (rootPage) => {
          _push(`<div class="relative group h-full flex items-center">`);
          _push(ssrRenderComponent(unref(Link), {
            href: getHref(rootPage),
            class: ["flex items-center gap-2 text-sm font-semibold transition text-slate-700 dark:text-slate-200 hover:text-blue-700 dark:hover:text-blue-300", {
              "text-blue-700 dark:text-blue-300": isPageActive(rootPage)
            }]
          }, {
            default: withCtx((_, _push2, _parent2, _scopeId) => {
              if (_push2) {
                if (rootPage.icon) {
                  _push2(`<span class="w-4 h-4 flex items-center justify-center"${_scopeId}>${rootPage.icon ?? ""}</span>`);
                } else {
                  _push2(`<!---->`);
                }
                _push2(`<span${_scopeId}>${ssrInterpolate(getTitle(rootPage))}</span>`);
              } else {
                return [
                  rootPage.icon ? (openBlock(), createBlock("span", {
                    key: 0,
                    class: "w-4 h-4 flex items-center justify-center",
                    innerHTML: rootPage.icon
                  }, null, 8, ["innerHTML"])) : createCommentVNode("", true),
                  createVNode("span", null, toDisplayString(getTitle(rootPage)), 1)
                ];
              }
            }),
            _: 2
          }, _parent));
          if (getPageChildren(rootPage).length) {
            _push(`<div class="invisible opacity-0 translate-y-2 group-hover:visible group-hover:opacity-100 group-hover:translate-y-0 absolute left-0 top-full z-20 min-w-64 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-950 shadow-xl transition-all duration-200"><div class="p-3 space-y-1"><!--[-->`);
            ssrRenderList(getPageChildren(rootPage), (child) => {
              _push(`<div class="relative group/child">`);
              _push(ssrRenderComponent(unref(Link), {
                href: getHref(child),
                class: "flex items-center justify-between gap-3 rounded-lg px-3 py-2 text-sm font-semibold text-slate-700 dark:text-slate-200 hover:bg-blue-50 dark:hover:bg-gray-900 hover:text-blue-700 dark:hover:text-blue-300"
              }, {
                default: withCtx((_, _push2, _parent2, _scopeId) => {
                  if (_push2) {
                    _push2(`<div class="flex items-center justify-center gap-2"${_scopeId}>`);
                    if (child.icon) {
                      _push2(`<span class="w-4 h-4 flex items-center justify-center"${_scopeId}>${child.icon ?? ""}</span>`);
                    } else {
                      _push2(`<!---->`);
                    }
                    _push2(`<span${_scopeId}>${ssrInterpolate(getTitle(child))}</span></div>`);
                    if (getPageChildren(child).length) {
                      _push2(`<span${_scopeId}> › </span>`);
                    } else {
                      _push2(`<!---->`);
                    }
                  } else {
                    return [
                      createVNode("div", { class: "flex items-center justify-center gap-2" }, [
                        child.icon ? (openBlock(), createBlock("span", {
                          key: 0,
                          class: "w-4 h-4 flex items-center justify-center",
                          innerHTML: child.icon
                        }, null, 8, ["innerHTML"])) : createCommentVNode("", true),
                        createVNode("span", null, toDisplayString(getTitle(child)), 1)
                      ]),
                      getPageChildren(child).length ? (openBlock(), createBlock("span", { key: 0 }, " › ")) : createCommentVNode("", true)
                    ];
                  }
                }),
                _: 2
              }, _parent));
              if (getPageChildren(child).length) {
                _push(`<div class="invisible opacity-0 translate-x-2 group-hover/child:visible group-hover/child:opacity-100 group-hover/child:translate-x-0 absolute left-full top-0 z-30 min-w-60 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-950 shadow-xl transition-all duration-200"><div class="p-3 space-y-1"><!--[-->`);
                ssrRenderList(getPageChildren(child), (subChild) => {
                  _push(ssrRenderComponent(unref(Link), {
                    key: subChild.id,
                    href: getHref(subChild),
                    class: "flex items-center justify-start gap-2 rounded-lg px-3 py-2 text-sm font-semibold text-slate-600 dark:text-slate-300 hover:bg-blue-50 dark:hover:bg-gray-900 hover:text-blue-700 dark:hover:text-blue-300"
                  }, {
                    default: withCtx((_, _push2, _parent2, _scopeId) => {
                      if (_push2) {
                        if (subChild.icon) {
                          _push2(`<span class="w-3.5 h-3.5 flex items-center justify-center"${_scopeId}>${subChild.icon ?? ""}</span>`);
                        } else {
                          _push2(`<!---->`);
                        }
                        _push2(`<span${_scopeId}>${ssrInterpolate(getTitle(subChild))}</span>`);
                      } else {
                        return [
                          subChild.icon ? (openBlock(), createBlock("span", {
                            key: 0,
                            class: "w-3.5 h-3.5 flex items-center justify-center",
                            innerHTML: subChild.icon
                          }, null, 8, ["innerHTML"])) : createCommentVNode("", true),
                          createVNode("span", null, toDisplayString(getTitle(subChild)), 1)
                        ];
                      }
                    }),
                    _: 2
                  }, _parent));
                });
                _push(`<!--]--></div></div>`);
              } else {
                _push(`<!---->`);
              }
              _push(`</div>`);
            });
            _push(`<!--]--></div></div>`);
          } else {
            _push(`<!---->`);
          }
          _push(`</div>`);
        });
        _push(`<!--]--></div>`);
      } else {
        _push(`<!---->`);
      }
      _push(`<div class="${ssrRenderClass([
        isSolid.value ? "grid-rows-[0fr] opacity-0" : "grid-rows-[1fr] opacity-100",
        "relative z-0 hidden lg:grid transition-[grid-template-rows,opacity] duration-300 ease-out"
      ])}"><div class="${ssrRenderClass([isCatalogOpen.value ? "" : "rounded-b-3xl", "min-h-0 overflow-hidden"])}"><a href="#" class="block h-14 overflow-hidden"><img src="/storage/header/rectangle_large.webp" alt="" class="block h-full w-full object-cover"></a></div></div></div>`);
      if (isCatalogOpen.value) {
        _push(`<div class="hidden lg:block absolute left-0 right-0 top-full z-[40] rounded-b-3xl overflow-hidden border-t border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-950 shadow-2xl"><div class="px-4 py-5"><div class="grid grid-cols-4 gap-5"><!--[-->`);
        ssrRenderList(marketCatalog.value, (rootCategory) => {
          _push(`<div class="rounded-xl border border-gray-200 dark:border-gray-800 bg-gray-50 dark:bg-gray-900 p-4">`);
          _push(ssrRenderComponent(unref(Link), {
            href: getHref(rootCategory),
            class: ["flex items-center gap-2 font-bold text-slate-900 dark:text-white hover:text-blue-700 dark:hover:text-blue-300 transition", {
              "text-blue-700 dark:text-blue-300": isCategoryActive(rootCategory)
            }],
            onClick: closeCatalog
          }, {
            default: withCtx((_, _push2, _parent2, _scopeId) => {
              if (_push2) {
                if (rootCategory.icon) {
                  _push2(`<span class="w-5 h-5 flex items-center justify-center text-slate-500 dark:text-slate-300"${_scopeId}>${rootCategory.icon ?? ""}</span>`);
                } else {
                  _push2(`<!---->`);
                }
                _push2(`<span${_scopeId}>${ssrInterpolate(getTitle(rootCategory))}</span>`);
              } else {
                return [
                  rootCategory.icon ? (openBlock(), createBlock("span", {
                    key: 0,
                    class: "w-5 h-5 flex items-center justify-center text-slate-500 dark:text-slate-300",
                    innerHTML: rootCategory.icon
                  }, null, 8, ["innerHTML"])) : createCommentVNode("", true),
                  createVNode("span", null, toDisplayString(getTitle(rootCategory)), 1)
                ];
              }
            }),
            _: 2
          }, _parent));
          if (getCategoryChildren(rootCategory).length) {
            _push(`<ul class="mt-3 space-y-2"><!--[-->`);
            ssrRenderList(getCategoryChildren(rootCategory), (child) => {
              _push(`<li>`);
              _push(ssrRenderComponent(unref(Link), {
                href: getHref(child),
                class: ["flex items-center gap-2 text-sm font-semibold text-slate-700 dark:text-slate-300 hover:text-blue-700 dark:hover:text-blue-300 transition", {
                  "text-blue-700 dark:text-blue-300": isCategoryActive(child)
                }],
                onClick: closeCatalog
              }, {
                default: withCtx((_, _push2, _parent2, _scopeId) => {
                  if (_push2) {
                    if (child.icon) {
                      _push2(`<span class="w-4 h-4 flex items-center justify-center text-slate-400"${_scopeId}>${child.icon ?? ""}</span>`);
                    } else {
                      _push2(`<!---->`);
                    }
                    _push2(`<span${_scopeId}>${ssrInterpolate(getTitle(child))}</span>`);
                  } else {
                    return [
                      child.icon ? (openBlock(), createBlock("span", {
                        key: 0,
                        class: "w-4 h-4 flex items-center justify-center text-slate-400",
                        innerHTML: child.icon
                      }, null, 8, ["innerHTML"])) : createCommentVNode("", true),
                      createVNode("span", null, toDisplayString(getTitle(child)), 1)
                    ];
                  }
                }),
                _: 2
              }, _parent));
              if (getCategoryChildren(child).length) {
                _push(`<ul class="mt-1 ml-6 space-y-1"><!--[-->`);
                ssrRenderList(getCategoryChildren(child), (subChild) => {
                  _push(`<li>`);
                  _push(ssrRenderComponent(unref(Link), {
                    href: getHref(subChild),
                    class: ["flex items-center gap-2 text-xs text-slate-500 dark:text-slate-400 hover:text-blue-700 dark:hover:text-blue-300 transition", {
                      "text-blue-700 dark:text-blue-300": isCategoryActive(subChild)
                    }],
                    onClick: closeCatalog
                  }, {
                    default: withCtx((_, _push2, _parent2, _scopeId) => {
                      if (_push2) {
                        if (subChild.icon) {
                          _push2(`<span class="w-3.5 h-3.5 opacity-70"${_scopeId}>${subChild.icon ?? ""}</span>`);
                        } else {
                          _push2(`<!---->`);
                        }
                        _push2(`<span${_scopeId}>${ssrInterpolate(getTitle(subChild))}</span>`);
                      } else {
                        return [
                          subChild.icon ? (openBlock(), createBlock("span", {
                            key: 0,
                            class: "w-3.5 h-3.5 opacity-70",
                            innerHTML: subChild.icon
                          }, null, 8, ["innerHTML"])) : createCommentVNode("", true),
                          createVNode("span", null, toDisplayString(getTitle(subChild)), 1)
                        ];
                      }
                    }),
                    _: 2
                  }, _parent));
                  _push(`</li>`);
                });
                _push(`<!--]--></ul>`);
              } else {
                _push(`<!---->`);
              }
              _push(`</li>`);
            });
            _push(`<!--]--></ul>`);
          } else {
            _push(`<!---->`);
          }
          _push(`</div>`);
        });
        _push(`<!--]--></div></div></div>`);
      } else {
        _push(`<!---->`);
      }
      _push(`</nav>`);
      ssrRenderTeleport(_push, (_push2) => {
        if (showingNavigationDropdown.value) {
          _push2(`<div class="fixed inset-0 z-[9999] lg:hidden"><div class="absolute inset-0 bg-black/40"></div><div class="absolute left-3 right-3 top-20 bottom-3 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-950 shadow-xl overflow-hidden"><div class="h-full overflow-y-auto overflow-x-hidden overscroll-contain px-3 py-3">`);
          _push2(ssrRenderComponent(_sfc_main$9, {
            mobile: "",
            onSubmitted: closeMobileMenu
          }, null, _parent));
          _push2(`<div class="space-y-2 pb-4"><!--[-->`);
          ssrRenderList(cmsMenu.value, (rootPage) => {
            _push2(`<div class="rounded-lg border border-gray-200 dark:border-gray-800 bg-gray-50 dark:bg-gray-900 overflow-hidden">`);
            _push2(ssrRenderComponent(unref(Link), {
              href: getHref(rootPage),
              onClick: closeMobileMenu,
              class: [
                "flex items-center gap-3 px-3 py-2 text-sm font-bold transition",
                isPageActive(rootPage) ? "bg-blue-100 text-blue-800 dark:bg-blue-900/40 dark:text-blue-300" : "text-slate-700 hover:bg-slate-100 dark:text-slate-200 dark:hover:bg-slate-800"
              ]
            }, {
              default: withCtx((_, _push3, _parent2, _scopeId) => {
                if (_push3) {
                  if (rootPage.icon) {
                    _push3(`<span class="h-4 w-4 flex items-center justify-center"${_scopeId}>${rootPage.icon ?? ""}</span>`);
                  } else {
                    _push3(`<!---->`);
                  }
                  _push3(`<span${_scopeId}>${ssrInterpolate(getTitle(rootPage))}</span>`);
                } else {
                  return [
                    rootPage.icon ? (openBlock(), createBlock("span", {
                      key: 0,
                      class: "h-4 w-4 flex items-center justify-center",
                      innerHTML: rootPage.icon
                    }, null, 8, ["innerHTML"])) : createCommentVNode("", true),
                    createVNode("span", null, toDisplayString(getTitle(rootPage)), 1)
                  ];
                }
              }),
              _: 2
            }, _parent));
            if (getPageChildren(rootPage).length) {
              _push2(`<div class="px-3 pb-2 space-y-1"><!--[-->`);
              ssrRenderList(getPageChildren(rootPage), (child) => {
                _push2(`<div>`);
                _push2(ssrRenderComponent(unref(Link), {
                  href: getHref(child),
                  onClick: closeMobileMenu,
                  class: [
                    "flex items-center gap-2 rounded-md px-3 py-2 text-sm font-semibold transition",
                    isPageActive(child) ? "bg-blue-100 text-blue-800 dark:bg-blue-900/40 dark:text-blue-300" : "text-slate-600 hover:bg-slate-100 dark:text-slate-300 dark:hover:bg-slate-800"
                  ]
                }, {
                  default: withCtx((_, _push3, _parent2, _scopeId) => {
                    if (_push3) {
                      if (child.icon) {
                        _push3(`<span class="h-4 w-4 flex items-center justify-center"${_scopeId}>${child.icon ?? ""}</span>`);
                      } else {
                        _push3(`<!---->`);
                      }
                      _push3(`<span${_scopeId}>${ssrInterpolate(getTitle(child))}</span>`);
                    } else {
                      return [
                        child.icon ? (openBlock(), createBlock("span", {
                          key: 0,
                          class: "h-4 w-4 flex items-center justify-center",
                          innerHTML: child.icon
                        }, null, 8, ["innerHTML"])) : createCommentVNode("", true),
                        createVNode("span", null, toDisplayString(getTitle(child)), 1)
                      ];
                    }
                  }),
                  _: 2
                }, _parent));
                if (getPageChildren(child).length) {
                  _push2(`<div class="ml-5 mt-1 space-y-1"><!--[-->`);
                  ssrRenderList(getPageChildren(child), (subChild) => {
                    _push2(ssrRenderComponent(unref(Link), {
                      key: subChild.id,
                      href: getHref(subChild),
                      onClick: closeMobileMenu,
                      class: [
                        "flex items-center gap-2 rounded-md px-3 py-1.5 text-xs font-semibold transition",
                        isPageActive(subChild) ? "bg-blue-100 text-blue-800 dark:bg-blue-900/40 dark:text-blue-300" : "text-slate-500 hover:bg-slate-100 dark:text-slate-400 dark:hover:bg-slate-800"
                      ]
                    }, {
                      default: withCtx((_, _push3, _parent2, _scopeId) => {
                        if (_push3) {
                          if (subChild.icon) {
                            _push3(`<span class="h-3.5 w-3.5 flex items-center justify-center"${_scopeId}>${subChild.icon ?? ""}</span>`);
                          } else {
                            _push3(`<!---->`);
                          }
                          _push3(`<span${_scopeId}>${ssrInterpolate(getTitle(subChild))}</span>`);
                        } else {
                          return [
                            subChild.icon ? (openBlock(), createBlock("span", {
                              key: 0,
                              class: "h-3.5 w-3.5 flex items-center justify-center",
                              innerHTML: subChild.icon
                            }, null, 8, ["innerHTML"])) : createCommentVNode("", true),
                            createVNode("span", null, toDisplayString(getTitle(subChild)), 1)
                          ];
                        }
                      }),
                      _: 2
                    }, _parent));
                  });
                  _push2(`<!--]--></div>`);
                } else {
                  _push2(`<!---->`);
                }
                _push2(`</div>`);
              });
              _push2(`<!--]--></div>`);
            } else {
              _push2(`<!---->`);
            }
            _push2(`</div>`);
          });
          _push2(`<!--]--><div class="pt-2 border-t border-gray-200 dark:border-gray-700">`);
          if (isAuth.value) {
            _push2(`<!--[-->`);
            _push2(ssrRenderComponent(unref(Link), {
              href: _ctx.route("profile.show"),
              onClick: closeMobileMenu,
              class: "block rounded-md px-3 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-100 dark:text-slate-200 dark:hover:bg-slate-800"
            }, {
              default: withCtx((_, _push3, _parent2, _scopeId) => {
                if (_push3) {
                  _push3(`${ssrInterpolate(unref(t)("profile"))}`);
                } else {
                  return [
                    createTextVNode(toDisplayString(unref(t)("profile")), 1)
                  ];
                }
              }),
              _: 1
            }, _parent));
            _push2(`<button type="button" class="block w-full text-left rounded-md px-3 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-100 dark:text-slate-200 dark:hover:bg-slate-800">${ssrInterpolate(unref(t)("logout"))}</button><!--]-->`);
          } else {
            _push2(`<!--[-->`);
            _push2(ssrRenderComponent(unref(Link), {
              href: _ctx.route("login"),
              onClick: closeMobileMenu,
              class: "block rounded-md px-3 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-100 dark:text-slate-200 dark:hover:bg-slate-800"
            }, {
              default: withCtx((_, _push3, _parent2, _scopeId) => {
                if (_push3) {
                  _push3(`${ssrInterpolate(unref(t)("login"))}`);
                } else {
                  return [
                    createTextVNode(toDisplayString(unref(t)("login")), 1)
                  ];
                }
              }),
              _: 1
            }, _parent));
            _push2(ssrRenderComponent(unref(Link), {
              href: _ctx.route("register"),
              onClick: closeMobileMenu,
              class: "block rounded-md px-3 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-100 dark:text-slate-200 dark:hover:bg-slate-800"
            }, {
              default: withCtx((_, _push3, _parent2, _scopeId) => {
                if (_push3) {
                  _push3(`${ssrInterpolate(unref(t)("register"))}`);
                } else {
                  return [
                    createTextVNode(toDisplayString(unref(t)("register")), 1)
                  ];
                }
              }),
              _: 1
            }, _parent));
            _push2(`<!--]-->`);
          }
          _push2(`</div></div></div></div></div>`);
        } else {
          _push2(`<!---->`);
        }
      }, "body", false, _parent);
      _push(`<!--]-->`);
    };
  }
};
const _sfc_setup$2 = _sfc_main$2.setup;
_sfc_main$2.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Partials/Default/Navbar.vue");
  return _sfc_setup$2 ? _sfc_setup$2(props, ctx) : void 0;
};
const fallbackLocale = "ru";
const _sfc_main$1 = {
  __name: "FooterBlog",
  __ssrInlineRender: true,
  setup(__props) {
    const { t, locale } = useI18n();
    const inertiaPage = usePage();
    const cmsFooter = computed(() => inertiaPage.props.cmsFooter || []);
    const currentLocale = computed(() => {
      return inertiaPage.props.locale || locale.value || "ru";
    });
    const getTranslation = (item) => {
      var _a;
      if (!((_a = item == null ? void 0 : item.translations) == null ? void 0 : _a.length)) {
        return (item == null ? void 0 : item.translation) || null;
      }
      return item.translations.find((translation) => translation.locale === currentLocale.value) || item.translations.find((translation) => translation.locale === fallbackLocale) || item.translations[0] || null;
    };
    const getTitle = (item) => {
      var _a;
      return (item == null ? void 0 : item.title) || ((_a = getTranslation(item)) == null ? void 0 : _a.title) || `ID: ${item == null ? void 0 : item.id}`;
    };
    const getFooterChildren = (item) => {
      return (item == null ? void 0 : item.public_footer_children) || [];
    };
    return (_ctx, _push, _parent, _attrs) => {
      _push(`<footer${ssrRenderAttrs(mergeProps({ class: "mx-1 lg:mx-6 my-3 lg:my-5 relative bg-gray-100 dark:bg-gray-900 border-2 border-slate-300 dark:border-slate-500 rounded-t-3xl" }, _attrs))}><div class="mx-auto px-3 py-3"><div class="grid grid-cols-1 md:grid-cols-4 gap-12"><div class="space-y-6"><div class="flex items-center gap-3"><div>`);
      _push(ssrRenderComponent(unref(Link), {
        href: _ctx.route("home"),
        class: "flex items-center justify-center gap-3 logo"
      }, {
        default: withCtx((_, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(`<svg class="h-10 w-10" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 640 512"${_scopeId}><path d="M622.34 153.2L343.4 67.5c-15.2-4.67-31.6-4.67-46.79 0L17.66 153.2c-23.54 7.23-23.54 38.36 0 45.59l48.63 14.94c-10.67 13.19-17.23 29.28-17.88 46.9C38.78 266.15 32 276.11 32 288c0 10.78 5.68 19.85 13.86 25.65L20.33 428.53C18.11 438.52 25.71 448 35.94 448h56.11c10.24 0 17.84-9.48 15.62-19.47L82.14 313.65C90.32 307.85 96 298.78 96 288c0-11.57-6.47-21.25-15.66-26.87.76-15.02 8.44-28.3 20.69-36.72L296.6 284.5c9.06 2.78 26.44 6.25 46.79 0l278.95-85.7c23.55-7.24 23.55-38.36 0-45.6zM352.79 315.09c-28.53 8.76-52.84 3.92-65.59 0l-145.02-44.55L128 384c0 35.35 85.96 64 192 64s192-28.65 192-64l-14.18-113.47-145.03 44.56z"${_scopeId}></path></svg><span class="font-semibold text-xs md:text-lg"${_scopeId}> SW Community </span>`);
          } else {
            return [
              (openBlock(), createBlock("svg", {
                class: "h-10 w-10",
                xmlns: "http://www.w3.org/2000/svg",
                fill: "currentColor",
                viewBox: "0 0 640 512"
              }, [
                createVNode("path", { d: "M622.34 153.2L343.4 67.5c-15.2-4.67-31.6-4.67-46.79 0L17.66 153.2c-23.54 7.23-23.54 38.36 0 45.59l48.63 14.94c-10.67 13.19-17.23 29.28-17.88 46.9C38.78 266.15 32 276.11 32 288c0 10.78 5.68 19.85 13.86 25.65L20.33 428.53C18.11 438.52 25.71 448 35.94 448h56.11c10.24 0 17.84-9.48 15.62-19.47L82.14 313.65C90.32 307.85 96 298.78 96 288c0-11.57-6.47-21.25-15.66-26.87.76-15.02 8.44-28.3 20.69-36.72L296.6 284.5c9.06 2.78 26.44 6.25 46.79 0l278.95-85.7c23.55-7.24 23.55-38.36 0-45.6zM352.79 315.09c-28.53 8.76-52.84 3.92-65.59 0l-145.02-44.55L128 384c0 35.35 85.96 64 192 64s192-28.65 192-64l-14.18-113.47-145.03 44.56z" })
              ])),
              createVNode("span", { class: "font-semibold text-xs md:text-lg" }, " SW Community ")
            ];
          }
        }),
        _: 1
      }, _parent));
      _push(`<h3 class="text-center subtitle"><span class="font-semibold text-gradient">${ssrInterpolate(unref(t)("ourCommunityBlog"))}</span></h3></div></div><p class="text-sm text-gray-600 dark:text-gray-400 leading-relaxed"> Изучайте статьи и руководства от экспертов сообщества. </p><div class="flex items-center gap-4"><a href="#" title="Facebook" class="text-gray-500 hover:text-sky-600 dark:hover:text-sky-400 transition"><svg xmlns="http://www.w3.org/2000/svg" width="0.54em" height="1em" viewBox="0 0 896 1664"><path fill="currentColor" d="M895 12v264H738q-86 0-116 36t-30 108v189h293l-39 296H592v759H286V905H31V609h255V391q0-186 104-288.5T667 0q147 0 228 12"></path></svg></a><a href="#" title="Twitter" class="text-gray-500 hover:text-sky-600 dark:hover:text-sky-400 transition"><svg xmlns="http://www.w3.org/2000/svg" width="1.25em" height="1em" viewBox="0 0 1600 1280"><path fill="currentColor" d="M1588 152q-67 98-162 167q1 14 1 42q0 130-38 259.5T1273.5 869T1089 1079.5t-258 146t-323 54.5q-271 0-496-145q35 4 78 4q225 0 401-138q-105-2-188-64.5T189 777q33 5 61 5q43 0 85-11q-112-23-185.5-111.5T76 454v-4q68 38 146 41q-66-44-105-115T78 222q0-88 44-163q121 149 294.5 238.5T788 397q-8-38-8-74q0-134 94.5-228.5T1103 0q140 0 236 102q109-21 205-78q-37 115-142 178q93-10 186-50"></path></svg></a><a href="#" title="Linkedin" class="text-gray-500 hover:text-sky-600 dark:hover:text-sky-400 transition"><svg xmlns="http://www.w3.org/2000/svg" width="1.03em" height="1em" viewBox="0 0 1536 1504"><path fill="currentColor" d="M349 497v991H19V497zm21-306q1 73-50.5 122T184 362h-2q-82 0-132-49T0 191q0-74 51.5-122.5T186 20t133 48.5T370 191m1166 729v568h-329V958q0-105-40.5-164.5T1040 734q-63 0-105.5 34.5T871 854q-11 30-11 81v553H531q2-399 2-647t-1-296l-1-48h329v144h-2q20-32 41-56t56.5-52t87-43.5T1157 474q171 0 275 113.5T1536 920"></path></svg></a><a href="#" title="GitHub" class="text-gray-500 hover:text-sky-600 dark:hover:text-sky-400 transition"><svg xmlns="http://www.w3.org/2000/svg" width="1.03em" height="1em" viewBox="0 0 1536 1504"><path fill="currentColor" d="M768 0q209 0 385.5 103T1433 382.5T1536 768q0 251-146.5 451.5T1011 1497q-27 5-40-7t-13-30q0-3 .5-76.5t.5-134.5q0-97-52-142q57-6 102.5-18t94-39t81-66.5t53-105T1258 728q0-119-79-206q37-91-8-204q-28-9-81 11t-92 44l-38 24q-93-26-192-26t-192 26q-16-11-42.5-27T450 331.5T365 318q-45 113-8 204q-79 87-79 206q0 85 20.5 150T351 983t80.5 67t94 39t102.5 18q-39 36-49 103q-21 10-45 15t-57 5t-65.5-21.5T356 1146q-19-32-48.5-52t-49.5-24l-20-3q-21 0-29 4.5t-5 11.5t9 14t13 12l7 5q22 10 43.5 38t31.5 51l10 23q13 38 44 61.5t67 30t69.5 7t55.5-3.5l23-4q0 38 .5 88.5t.5 54.5q0 18-13 30t-40 7q-232-77-378.5-277.5T0 768q0-209 103-385.5T382.5 103T768 0M291 1103q3-7-7-12q-10-3-13 2q-3 7 7 12q9 6 13-2m31 34q7-5-2-16q-10-9-16-3q-7 5 2 16q10 10 16 3m30 45q9-7 0-19q-8-13-17-6q-9 5 0 18t17 7m42 42q8-8-4-19q-12-12-20-3q-9 8 4 19q12 12 20 3m57 25q3-11-13-16q-15-4-19 7t13 15q15 6 19-6m63 5q0-13-17-11q-16 0-16 11q0 13 17 11q16 0 16-11m58-10q-2-11-18-9q-16 3-14 15t18 8t14-14"></path></svg></a></div></div><div class="md:col-span-3"><div class="grid grid-cols-1 gap-10 sm:grid-cols-2 md:grid-cols-4"><!--[-->`);
      ssrRenderList(cmsFooter.value, (rootPage) => {
        _push(`<div><h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-4">`);
        _push(ssrRenderComponent(unref(Link), {
          href: rootPage.url,
          class: "hover:text-sky-600 dark:hover:text-sky-400 transition"
        }, {
          default: withCtx((_, _push2, _parent2, _scopeId) => {
            if (_push2) {
              _push2(`${ssrInterpolate(getTitle(rootPage))}`);
            } else {
              return [
                createTextVNode(toDisplayString(getTitle(rootPage)), 1)
              ];
            }
          }),
          _: 2
        }, _parent));
        _push(`</h3><ul class="space-y-3 text-sm"><!--[-->`);
        ssrRenderList(getFooterChildren(rootPage), (child) => {
          _push(`<li>`);
          _push(ssrRenderComponent(unref(Link), {
            href: child.url,
            class: "text-gray-600 dark:text-gray-400 hover:text-sky-600 dark:hover:text-sky-400 transition"
          }, {
            default: withCtx((_, _push2, _parent2, _scopeId) => {
              if (_push2) {
                _push2(`${ssrInterpolate(getTitle(child))}`);
              } else {
                return [
                  createTextVNode(toDisplayString(getTitle(child)), 1)
                ];
              }
            }),
            _: 2
          }, _parent));
          if (getFooterChildren(child).length) {
            _push(`<ul class="mt-2 space-y-2"><!--[-->`);
            ssrRenderList(getFooterChildren(child), (subChild) => {
              _push(`<li>`);
              _push(ssrRenderComponent(unref(Link), {
                href: subChild.url,
                class: "text-xs text-gray-500 dark:text-gray-500 hover:text-sky-600 dark:hover:text-sky-400 transition"
              }, {
                default: withCtx((_, _push2, _parent2, _scopeId) => {
                  if (_push2) {
                    _push2(`${ssrInterpolate(getTitle(subChild))}`);
                  } else {
                    return [
                      createTextVNode(toDisplayString(getTitle(subChild)), 1)
                    ];
                  }
                }),
                _: 2
              }, _parent));
              _push(`</li>`);
            });
            _push(`<!--]--></ul>`);
          } else {
            _push(`<!---->`);
          }
          _push(`</li>`);
        });
        _push(`<!--]--></ul></div>`);
      });
      _push(`<!--]--></div></div></div></div><div class="absolute bottom-0 left-0 right-0 flex justify-center w-full pointer-events-none opacity-40 dark:opacity-40"><img src="/assets/illustrations/footer/footer-cityscape.png" class="max-w-full h-auto" alt=""></div><div class="border-t border-gray-200 dark:border-gray-800"><div class="mx-auto max-w-6xl px-6 py-6 font-semibold text-center text-sm text-gray-700 dark:text-gray-300">${ssrInterpolate((/* @__PURE__ */ new Date()).getFullYear())} © Community. All rights reserved. </div></div></footer>`);
    };
  }
};
const _sfc_setup$1 = _sfc_main$1.setup;
_sfc_main$1.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Partials/Default/FooterBlog.vue");
  return _sfc_setup$1 ? _sfc_setup$1(props, ctx) : void 0;
};
const _sfc_main = {
  __name: "Progress",
  __ssrInlineRender: true,
  setup(__props) {
    const CIRCUMFERENCE = 2 * Math.PI * 49;
    const dashArray = `${CIRCUMFERENCE.toFixed(3)}, ${CIRCUMFERENCE.toFixed(3)}`;
    const dashOffset = ref(CIRCUMFERENCE);
    const isActive = ref(false);
    let ticking = false;
    function updateProgress() {
      const doc = document.documentElement;
      const scrollTop = window.pageYOffset || doc.scrollTop || 0;
      const max = Math.max(1, doc.scrollHeight - window.innerHeight);
      const progress = Math.min(1, Math.max(0, scrollTop / max));
      dashOffset.value = CIRCUMFERENCE * (1 - progress);
      isActive.value = scrollTop > 120;
      ticking = false;
    }
    function onScroll() {
      if (!ticking) {
        ticking = true;
        requestAnimationFrame(updateProgress);
      }
    }
    const onResize = onScroll;
    onMounted(() => {
      updateProgress();
      window.addEventListener("scroll", onScroll, { passive: true });
      window.addEventListener("resize", onResize, { passive: true });
    });
    onBeforeUnmount(() => {
      window.removeEventListener("scroll", onScroll);
      window.removeEventListener("resize", onResize);
    });
    return (_ctx, _push, _parent, _attrs) => {
      _push(`<div${ssrRenderAttrs(mergeProps({
        class: ["progress-wrap", { "active-progress": isActive.value }],
        role: "button",
        tabindex: "0",
        "aria-label": "Прокрутить вверх"
      }, _attrs))} data-v-dd933e22><svg class="progress-circle" width="100%" height="100%" viewBox="-1 -1 102 102" aria-hidden="true" data-v-dd933e22><path class="track" d="M50,1 a49,49 0 0,1 0,98 a49,49 0 0,1 0,-98" data-v-dd933e22></path><path class="bar" d="M50,1 a49,49 0 0,1 0,98 a49,49 0 0,1 0,-98" style="${ssrRenderStyle({ strokeDasharray: dashArray, strokeDashoffset: dashOffset.value })}" data-v-dd933e22></path></svg><svg class="arrow-icon" viewBox="0 0 24 24" width="24" height="24" aria-hidden="true" data-v-dd933e22><path d="M22,18a1,1,0,0,1-.707-.293L12,8.414,2.707,17.707a1,1,0,0,1-1.414-1.414l10-10a1,1,0,0,1,1.414,0l10,10A1,1,0,0,1,22,18Z" data-v-dd933e22></path></svg></div>`);
    };
  }
};
const _sfc_setup = _sfc_main.setup;
_sfc_main.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Components/Public/Default/Progress/Progress.vue");
  return _sfc_setup ? _sfc_setup(props, ctx) : void 0;
};
const Progress = /* @__PURE__ */ _export_sfc(_sfc_main, [["__scopeId", "data-v-dd933e22"]]);
export {
  Progress as P,
  _sfc_main$2 as _,
  _sfc_main$1 as a
};
