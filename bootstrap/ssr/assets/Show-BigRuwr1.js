import { unref, withCtx, createVNode, toDisplayString, createTextVNode, createBlock, createCommentVNode, openBlock, useSSRContext } from "vue";
import { ssrRenderComponent, ssrInterpolate, ssrRenderAttr } from "vue/server-renderer";
import { Head, Link } from "@inertiajs/vue3";
import { useI18n } from "vue-i18n";
import { _ as _sfc_main$1 } from "./DefaultLayout-6zxGxPtK.js";
import { _ as _sfc_main$2, a as _sfc_main$3, P as Progress } from "./Progress-CyfOKcqP.js";
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
const _sfc_main = {
  __name: "Show",
  __ssrInlineRender: true,
  props: {
    cmsPage: {
      type: Object,
      required: true
    }
  },
  setup(__props) {
    const { t } = useI18n();
    return (_ctx, _push, _parent, _attrs) => {
      _push(`<!--[-->`);
      _push(ssrRenderComponent(unref(Head), null, {
        default: withCtx((_, _push2, _parent2, _scopeId) => {
          var _a, _b, _c, _d, _e, _f, _g, _h, _i, _j, _k, _l, _m, _n, _o, _p;
          if (_push2) {
            _push2(`<title${_scopeId}>${ssrInterpolate(((_a = __props.cmsPage.seo) == null ? void 0 : _a.title) || __props.cmsPage.title)}</title><meta name="title"${ssrRenderAttr("content", ((_b = __props.cmsPage.seo) == null ? void 0 : _b.title) || __props.cmsPage.title || "")}${_scopeId}><meta name="keywords"${ssrRenderAttr("content", ((_c = __props.cmsPage.seo) == null ? void 0 : _c.keywords) || "")}${_scopeId}><meta name="description"${ssrRenderAttr("content", ((_d = __props.cmsPage.seo) == null ? void 0 : _d.description) || __props.cmsPage.short || "")}${_scopeId}><meta property="og:title"${ssrRenderAttr("content", ((_e = __props.cmsPage.seo) == null ? void 0 : _e.title) || __props.cmsPage.title || "")}${_scopeId}><meta property="og:description"${ssrRenderAttr("content", ((_f = __props.cmsPage.seo) == null ? void 0 : _f.description) || __props.cmsPage.short || "")}${_scopeId}><meta property="og:type" content="website"${_scopeId}><meta property="og:url"${ssrRenderAttr("content", __props.cmsPage.url || "/")}${_scopeId}><meta name="twitter:card" content="summary_large_image"${_scopeId}><meta name="twitter:title"${ssrRenderAttr("content", ((_g = __props.cmsPage.seo) == null ? void 0 : _g.title) || __props.cmsPage.title || "")}${_scopeId}><meta name="twitter:description"${ssrRenderAttr("content", ((_h = __props.cmsPage.seo) == null ? void 0 : _h.description) || __props.cmsPage.short || "")}${_scopeId}>`);
          } else {
            return [
              createVNode("title", null, toDisplayString(((_i = __props.cmsPage.seo) == null ? void 0 : _i.title) || __props.cmsPage.title), 1),
              createVNode("meta", {
                name: "title",
                content: ((_j = __props.cmsPage.seo) == null ? void 0 : _j.title) || __props.cmsPage.title || ""
              }, null, 8, ["content"]),
              createVNode("meta", {
                name: "keywords",
                content: ((_k = __props.cmsPage.seo) == null ? void 0 : _k.keywords) || ""
              }, null, 8, ["content"]),
              createVNode("meta", {
                name: "description",
                content: ((_l = __props.cmsPage.seo) == null ? void 0 : _l.description) || __props.cmsPage.short || ""
              }, null, 8, ["content"]),
              createVNode("meta", {
                property: "og:title",
                content: ((_m = __props.cmsPage.seo) == null ? void 0 : _m.title) || __props.cmsPage.title || ""
              }, null, 8, ["content"]),
              createVNode("meta", {
                property: "og:description",
                content: ((_n = __props.cmsPage.seo) == null ? void 0 : _n.description) || __props.cmsPage.short || ""
              }, null, 8, ["content"]),
              createVNode("meta", {
                property: "og:type",
                content: "website"
              }),
              createVNode("meta", {
                property: "og:url",
                content: __props.cmsPage.url || "/"
              }, null, 8, ["content"]),
              createVNode("meta", {
                name: "twitter:card",
                content: "summary_large_image"
              }),
              createVNode("meta", {
                name: "twitter:title",
                content: ((_o = __props.cmsPage.seo) == null ? void 0 : _o.title) || __props.cmsPage.title || ""
              }, null, 8, ["content"]),
              createVNode("meta", {
                name: "twitter:description",
                content: ((_p = __props.cmsPage.seo) == null ? void 0 : _p.description) || __props.cmsPage.short || ""
              }, null, 8, ["content"])
            ];
          }
        }),
        _: 1
      }, _parent));
      _push(ssrRenderComponent(_sfc_main$1, {
        title: __props.cmsPage.title
      }, {
        default: withCtx((_, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(ssrRenderComponent(_sfc_main$2, null, null, _parent2, _scopeId));
            _push2(`<main class="min-h-screen px-1 lg:px-6 max-w-full"${_scopeId}><div class="mx-auto tracking-wider pt-20 lg:pt-44"${_scopeId}><div class="ext-color w-full min-w-0 py-3 px-1 flex flex-col gap-4 rounded-3xl border-2 border-slate-300 dark:border-slate-500"${_scopeId}><nav class="text-sm mb-4" aria-label="Breadcrumb"${_scopeId}><ol class="flex items-center font-semibold"${_scopeId}><li${_scopeId}>`);
            _push2(ssrRenderComponent(unref(Link), {
              href: _ctx.route("home"),
              class: "breadcrumb-link hover:underline"
            }, {
              default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                if (_push3) {
                  _push3(`${ssrInterpolate(unref(t)("home"))}`);
                } else {
                  return [
                    createTextVNode(toDisplayString(unref(t)("home")), 1)
                  ];
                }
              }),
              _: 1
            }, _parent2, _scopeId));
            _push2(`</li><li${_scopeId}><span class="mx-2 breadcrumbs"${_scopeId}>/</span></li><li class="breadcrumbs"${_scopeId}>${ssrInterpolate(__props.cmsPage.title)}</li></ol></nav><section class="rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 shadow-sm px-4 py-6 sm:px-6 lg:px-8"${_scopeId}><header class="mb-6 text-center"${_scopeId}><h1 class="text-2xl sm:text-3xl font-bold title"${_scopeId}>${ssrInterpolate(__props.cmsPage.title)}</h1>`);
            if (__props.cmsPage.subtitle) {
              _push2(`<p class="mt-2 text-sm sm:text-base subtitle"${_scopeId}>${ssrInterpolate(__props.cmsPage.subtitle)}</p>`);
            } else {
              _push2(`<!---->`);
            }
            if (__props.cmsPage.short) {
              _push2(`<p class="mt-3 text-sm text-slate-600 dark:text-slate-300"${_scopeId}>${ssrInterpolate(__props.cmsPage.short)}</p>`);
            } else {
              _push2(`<!---->`);
            }
            _push2(`</header>`);
            if (__props.cmsPage.show_content && __props.cmsPage.description) {
              _push2(`<article class="prose prose-slate dark:prose-invert max-w-none"${_scopeId}>${__props.cmsPage.description ?? ""}</article>`);
            } else {
              _push2(`<div class="text-center text-slate-500 dark:text-slate-300"${_scopeId}>${ssrInterpolate(unref(t)("noData"))}</div>`);
            }
            _push2(`</section></div></div></main>`);
            _push2(ssrRenderComponent(_sfc_main$3, null, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(Progress, null, null, _parent2, _scopeId));
          } else {
            return [
              createVNode(_sfc_main$2),
              createVNode("main", { class: "min-h-screen px-1 lg:px-6 max-w-full" }, [
                createVNode("div", { class: "mx-auto tracking-wider pt-20 lg:pt-44" }, [
                  createVNode("div", { class: "ext-color w-full min-w-0 py-3 px-1 flex flex-col gap-4 rounded-3xl border-2 border-slate-300 dark:border-slate-500" }, [
                    createVNode("nav", {
                      class: "text-sm mb-4",
                      "aria-label": "Breadcrumb"
                    }, [
                      createVNode("ol", { class: "flex items-center font-semibold" }, [
                        createVNode("li", null, [
                          createVNode(unref(Link), {
                            href: _ctx.route("home"),
                            class: "breadcrumb-link hover:underline"
                          }, {
                            default: withCtx(() => [
                              createTextVNode(toDisplayString(unref(t)("home")), 1)
                            ]),
                            _: 1
                          }, 8, ["href"])
                        ]),
                        createVNode("li", null, [
                          createVNode("span", { class: "mx-2 breadcrumbs" }, "/")
                        ]),
                        createVNode("li", { class: "breadcrumbs" }, toDisplayString(__props.cmsPage.title), 1)
                      ])
                    ]),
                    createVNode("section", { class: "rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 shadow-sm px-4 py-6 sm:px-6 lg:px-8" }, [
                      createVNode("header", { class: "mb-6 text-center" }, [
                        createVNode("h1", { class: "text-2xl sm:text-3xl font-bold title" }, toDisplayString(__props.cmsPage.title), 1),
                        __props.cmsPage.subtitle ? (openBlock(), createBlock("p", {
                          key: 0,
                          class: "mt-2 text-sm sm:text-base subtitle"
                        }, toDisplayString(__props.cmsPage.subtitle), 1)) : createCommentVNode("", true),
                        __props.cmsPage.short ? (openBlock(), createBlock("p", {
                          key: 1,
                          class: "mt-3 text-sm text-slate-600 dark:text-slate-300"
                        }, toDisplayString(__props.cmsPage.short), 1)) : createCommentVNode("", true)
                      ]),
                      __props.cmsPage.show_content && __props.cmsPage.description ? (openBlock(), createBlock("article", {
                        key: 0,
                        class: "prose prose-slate dark:prose-invert max-w-none",
                        innerHTML: __props.cmsPage.description
                      }, null, 8, ["innerHTML"])) : (openBlock(), createBlock("div", {
                        key: 1,
                        class: "text-center text-slate-500 dark:text-slate-300"
                      }, toDisplayString(unref(t)("noData")), 1))
                    ])
                  ])
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
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Pages/Public/Default/Cms/Show.vue");
  return _sfc_setup ? _sfc_setup(props, ctx) : void 0;
};
export {
  _sfc_main as default
};
