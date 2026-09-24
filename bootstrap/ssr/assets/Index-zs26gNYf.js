import { computed, ref, watch, unref, withCtx, createVNode, createBlock, createCommentVNode, openBlock, toDisplayString, Fragment, renderList, useSSRContext } from "vue";
import { ssrRenderComponent, ssrRenderClass, ssrInterpolate, ssrRenderList } from "vue/server-renderer";
import { usePage, Head } from "@inertiajs/vue3";
import { _ as _sfc_main$1 } from "./DefaultLayout-6zxGxPtK.js";
import { _ as _sfc_main$2, a as _sfc_main$3, P as Progress } from "./Progress-CyfOKcqP.js";
import "axios";
import "@inertiajs/inertia";
import "vue-i18n";
import "./ThemeToggle-DA16u1ft.js";
import "@vueuse/core";
import "./LocaleSelectOption-BeLdazeX.js";
import "./Checkbox-CgE3PSwb.js";
import "./TextInput-CCxUFX3K.js";
import "./InputLabel-Ds0Eo91B.js";
import "./PrimaryButton-D7EZDGT_.js";
import "./_plugin-vue_export-helper-1tPrXgE0.js";
const LEFT_SIDEBAR_KEY = "public_left_sidebar_collapsed";
const RIGHT_SIDEBAR_KEY = "public_right_sidebar_collapsed";
const _sfc_main = {
  __name: "Index",
  __ssrInlineRender: true,
  props: {
    locale: { type: String, default: "ru" },
    title: { type: String, default: "" },
    canLogin: { type: Boolean, default: false },
    canRegister: { type: Boolean, default: false }
  },
  setup(__props) {
    var _a;
    const page = usePage();
    const siteSettings = ((_a = page.props) == null ? void 0 : _a.siteSettings) || {};
    const privacy = computed(() => {
      var _a2, _b, _c;
      return ((_c = (_b = (_a2 = page.props.laravelLang) == null ? void 0 : _a2.public) == null ? void 0 : _b.privacy) == null ? void 0 : _c.page) ?? {};
    });
    const sections = computed(() => privacy.value.sections ?? []);
    const showLeft = computed(() => {
      return !(siteSettings == null ? void 0 : siteSettings.ViewLeftColumn) || siteSettings.ViewLeftColumn === "true";
    });
    const showRight = computed(() => {
      return !(siteSettings == null ? void 0 : siteSettings.ViewRightColumn) || siteSettings.ViewRightColumn === "true";
    });
    const getStoredBoolean = (key, defaultValue = false) => {
      const value = localStorage.getItem(key);
      if (value === null) {
        return defaultValue;
      }
      return value === "true";
    };
    const leftCollapsed = ref(
      getStoredBoolean(LEFT_SIDEBAR_KEY, false)
    );
    const rightCollapsed = ref(
      getStoredBoolean(RIGHT_SIDEBAR_KEY, false)
    );
    watch(leftCollapsed, (value) => {
      localStorage.setItem(LEFT_SIDEBAR_KEY, String(value));
    });
    watch(rightCollapsed, (value) => {
      localStorage.setItem(RIGHT_SIDEBAR_KEY, String(value));
    });
    return (_ctx, _push, _parent, _attrs) => {
      _push(`<!--[-->`);
      _push(ssrRenderComponent(unref(Head), {
        title: privacy.value.meta_title ?? privacy.value.title
      }, null, _parent));
      _push(ssrRenderComponent(_sfc_main$1, {
        title: __props.title,
        "can-login": __props.canLogin,
        "can-register": __props.canRegister
      }, {
        default: withCtx((_, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(ssrRenderComponent(_sfc_main$2, null, null, _parent2, _scopeId));
            _push2(`<div class="min-h-screen px-3 max-w-full"${_scopeId}><main class="mx-auto flex flex-col lg:flex-row gap-4 tracking-wider"${_scopeId}>`);
            if (showLeft.value) {
              _push2(`<aside class="${ssrRenderClass([leftCollapsed.value ? "lg:w-10" : "lg:w-64", "shrink-0 mt-12 sm:mt-16 transition-all duration-300"])}"${_scopeId}></aside>`);
            } else {
              _push2(`<!---->`);
            }
            _push2(`<div class="w-full lg:mt-16 pb-6 slate-1"${_scopeId}><div class="mx-auto max-w-6xl"${_scopeId}><div class="rounded-lg border border-gray-200 bg-white p-6 shadow-md dark:border-gray-700 dark:bg-gray-900 sm:p-8"${_scopeId}><div class="mb-8"${_scopeId}><h1 class="text-2xl font-bold text-center text-gray-900 dark:text-white sm:text-3xl"${_scopeId}>${ssrInterpolate(privacy.value.title)}</h1><p class="mt-2 font-semibold text-xs text-center text-gray-500 dark:text-gray-400"${_scopeId}>${ssrInterpolate(privacy.value.version_label)}: ${ssrInterpolate(privacy.value.version)}</p><p class="mt-3 text-sm text-gray-600 dark:text-gray-300"${_scopeId}>${ssrInterpolate(privacy.value.description)}</p></div><div class="space-y-6"${_scopeId}><!--[-->`);
            ssrRenderList(sections.value, (section) => {
              var _a2;
              _push2(`<section class="rounded-md border border-gray-200 p-5 dark:border-gray-700"${_scopeId}><h2 class="mb-3 text-lg font-semibold text-gray-900 dark:text-white"${_scopeId}>${ssrInterpolate(section.title)}</h2>`);
              if (section.description) {
                _push2(`<p class="mb-4 text-sm leading-6 text-gray-700 dark:text-gray-300"${_scopeId}>${ssrInterpolate(section.description)}</p>`);
              } else {
                _push2(`<!---->`);
              }
              if ((_a2 = section.items) == null ? void 0 : _a2.length) {
                _push2(`<ul class="list-disc space-y-2 pl-5 text-sm leading-6 text-gray-700 dark:text-gray-300"${_scopeId}><!--[-->`);
                ssrRenderList(section.items, (item) => {
                  _push2(`<li${_scopeId}>${ssrInterpolate(item)}</li>`);
                });
                _push2(`<!--]--></ul>`);
              } else {
                _push2(`<!---->`);
              }
              _push2(`</section>`);
            });
            _push2(`<!--]--></div></div></div></div>`);
            if (showRight.value) {
              _push2(`<aside class="${ssrRenderClass([rightCollapsed.value ? "lg:w-10" : "lg:w-64", "shrink-0 lg:mt-16 transition-all duration-300"])}"${_scopeId}></aside>`);
            } else {
              _push2(`<!---->`);
            }
            _push2(`</main></div>`);
            _push2(ssrRenderComponent(_sfc_main$3, null, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(Progress, null, null, _parent2, _scopeId));
          } else {
            return [
              createVNode(_sfc_main$2),
              createVNode("div", { class: "min-h-screen px-3 max-w-full" }, [
                createVNode("main", { class: "mx-auto flex flex-col lg:flex-row gap-4 tracking-wider" }, [
                  showLeft.value ? (openBlock(), createBlock("aside", {
                    key: 0,
                    class: ["shrink-0 mt-12 sm:mt-16 transition-all duration-300", leftCollapsed.value ? "lg:w-10" : "lg:w-64"]
                  }, null, 2)) : createCommentVNode("", true),
                  createVNode("div", { class: "w-full lg:mt-16 pb-6 slate-1" }, [
                    createVNode("div", { class: "mx-auto max-w-6xl" }, [
                      createVNode("div", { class: "rounded-lg border border-gray-200 bg-white p-6 shadow-md dark:border-gray-700 dark:bg-gray-900 sm:p-8" }, [
                        createVNode("div", { class: "mb-8" }, [
                          createVNode("h1", { class: "text-2xl font-bold text-center text-gray-900 dark:text-white sm:text-3xl" }, toDisplayString(privacy.value.title), 1),
                          createVNode("p", { class: "mt-2 font-semibold text-xs text-center text-gray-500 dark:text-gray-400" }, toDisplayString(privacy.value.version_label) + ": " + toDisplayString(privacy.value.version), 1),
                          createVNode("p", { class: "mt-3 text-sm text-gray-600 dark:text-gray-300" }, toDisplayString(privacy.value.description), 1)
                        ]),
                        createVNode("div", { class: "space-y-6" }, [
                          (openBlock(true), createBlock(Fragment, null, renderList(sections.value, (section) => {
                            var _a2;
                            return openBlock(), createBlock("section", {
                              key: section.title,
                              class: "rounded-md border border-gray-200 p-5 dark:border-gray-700"
                            }, [
                              createVNode("h2", { class: "mb-3 text-lg font-semibold text-gray-900 dark:text-white" }, toDisplayString(section.title), 1),
                              section.description ? (openBlock(), createBlock("p", {
                                key: 0,
                                class: "mb-4 text-sm leading-6 text-gray-700 dark:text-gray-300"
                              }, toDisplayString(section.description), 1)) : createCommentVNode("", true),
                              ((_a2 = section.items) == null ? void 0 : _a2.length) ? (openBlock(), createBlock("ul", {
                                key: 1,
                                class: "list-disc space-y-2 pl-5 text-sm leading-6 text-gray-700 dark:text-gray-300"
                              }, [
                                (openBlock(true), createBlock(Fragment, null, renderList(section.items, (item) => {
                                  return openBlock(), createBlock("li", { key: item }, toDisplayString(item), 1);
                                }), 128))
                              ])) : createCommentVNode("", true)
                            ]);
                          }), 128))
                        ])
                      ])
                    ])
                  ]),
                  showRight.value ? (openBlock(), createBlock("aside", {
                    key: 1,
                    class: ["shrink-0 lg:mt-16 transition-all duration-300", rightCollapsed.value ? "lg:w-10" : "lg:w-64"]
                  }, null, 2)) : createCommentVNode("", true)
                ])
              ]),
              createVNode(_sfc_main$3),
              createVNode(Progress)
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
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Pages/Public/Privacy/Index.vue");
  return _sfc_setup ? _sfc_setup(props, ctx) : void 0;
};
export {
  _sfc_main as default
};
