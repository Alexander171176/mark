import { mergeProps, unref, withCtx, createTextVNode, createBlock, openBlock, createVNode, useSSRContext, toDisplayString } from "vue";
import { ssrRenderAttrs, ssrRenderComponent, ssrInterpolate, ssrRenderAttr } from "vue/server-renderer";
import { Link, Head } from "@inertiajs/vue3";
import { useI18n } from "vue-i18n";
import { _ as _sfc_main$2 } from "./DefaultLayout-6zxGxPtK.js";
import { _ as _sfc_main$3, a as _sfc_main$4, P as Progress } from "./Progress-CyfOKcqP.js";
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
const _sfc_main$1 = {
  __name: "HomeHero",
  __ssrInlineRender: true,
  setup(__props) {
    const { t } = useI18n();
    return (_ctx, _push, _parent, _attrs) => {
      _push(`<section${ssrRenderAttrs(mergeProps({ class: "relative overflow-hidden" }, _attrs))}><div class="pointer-events-none absolute inset-0 overflow-hidden" aria-hidden="true"><div class="absolute -right-32 -top-32 h-96 w-96 rounded-full bg-sky-500/10 blur-3xl dark:bg-sky-400/10"></div><div class="absolute -bottom-40 left-1/4 h-96 w-96 rounded-full bg-emerald-500/10 blur-3xl dark:bg-emerald-400/10"></div></div><div class="relative mx-auto max-w-7xl px-4 pb-16 pt-28 sm:px-6 sm:pb-20 sm:pt-32 lg:px-8 lg:pb-24 lg:pt-44"><div class="grid items-center gap-10 lg:grid-cols-2 lg:gap-16"><div class="min-w-0"><div class="mb-5 inline-flex items-center gap-2 rounded-full border border-slate-200 bg-white/80 px-3 py-1.5 text-sm font-medium text-slate-700 shadow-sm backdrop-blur dark:border-slate-700 dark:bg-slate-900/70 dark:text-slate-200"><span class="h-2 w-2 rounded-full bg-emerald-500"></span> Инженерные решения для вашего объекта </div><h1 class="max-w-3xl text-4xl font-bold leading-tight tracking-tight text-slate-900 dark:text-white sm:text-5xl lg:text-6xl"> Вентиляционное и климатическое оборудование <span class="text-sky-600 dark:text-sky-400">для объектов любой сложности</span></h1><p class="mt-6 max-w-2xl text-base leading-7 text-slate-600 dark:text-slate-300 sm:text-lg sm:leading-8"> Подбор, поставка и техническое сопровождение оборудования для систем вентиляции, отопления и кондиционирования. </p><div class="mt-8 flex flex-col gap-3 sm:flex-row sm:flex-wrap">`);
      _push(ssrRenderComponent(unref(Link), {
        href: _ctx.route("public.marketProducts.index"),
        class: "inline-flex items-center justify-center gap-2 rounded-xl bg-sky-600 px-6 py-3.5 text-sm font-semibold text-white shadow-sm transition hover:bg-sky-700 focus:outline-none focus:ring-2 focus:ring-sky-500 focus:ring-offset-2 dark:focus:ring-offset-slate-950"
      }, {
        default: withCtx((_, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(` Каталог оборудования <svg class="h-4 w-4" viewBox="0 0 20 20" fill="none" aria-hidden="true"${_scopeId}><path d="M4 10h12M11 5l5 5-5 5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"${_scopeId}></path></svg>`);
          } else {
            return [
              createTextVNode(" Каталог оборудования "),
              (openBlock(), createBlock("svg", {
                class: "h-4 w-4",
                viewBox: "0 0 20 20",
                fill: "none",
                "aria-hidden": "true"
              }, [
                createVNode("path", {
                  d: "M4 10h12M11 5l5 5-5 5",
                  stroke: "currentColor",
                  "stroke-width": "1.8",
                  "stroke-linecap": "round",
                  "stroke-linejoin": "round"
                })
              ]))
            ];
          }
        }),
        _: 1
      }, _parent));
      _push(`<a href="#equipment-selection" class="inline-flex items-center justify-center rounded-xl border border-slate-300 bg-white/70 px-6 py-3.5 text-sm font-semibold text-slate-800 shadow-sm transition hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-sky-500 focus:ring-offset-2 dark:border-slate-700 dark:bg-slate-900/60 dark:text-slate-100 dark:hover:bg-slate-800 dark:focus:ring-offset-slate-950"> Подобрать оборудование </a></div><div class="mt-10 grid gap-4 border-t border-slate-200 pt-6 dark:border-slate-800 sm:grid-cols-3"><div class="flex items-start gap-3"><span class="mt-1 flex h-7 w-7 shrink-0 items-center justify-center rounded-lg bg-sky-100 text-sky-700 dark:bg-sky-500/10 dark:text-sky-400"><svg class="h-4 w-4" viewBox="0 0 20 20" fill="none" aria-hidden="true"><path d="m5 10 3 3 7-7" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"></path></svg></span><span class="text-sm leading-6 text-slate-600 dark:text-slate-300"> Инженерный подбор </span></div><div class="flex items-start gap-3"><span class="mt-1 flex h-7 w-7 shrink-0 items-center justify-center rounded-lg bg-sky-100 text-sky-700 dark:bg-sky-500/10 dark:text-sky-400"><svg class="h-4 w-4" viewBox="0 0 20 20" fill="none" aria-hidden="true"><path d="m5 10 3 3 7-7" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"></path></svg></span><span class="text-sm leading-6 text-slate-600 dark:text-slate-300"> Комплектация проектов </span></div><div class="flex items-start gap-3"><span class="mt-1 flex h-7 w-7 shrink-0 items-center justify-center rounded-lg bg-sky-100 text-sky-700 dark:bg-sky-500/10 dark:text-sky-400"><svg class="h-4 w-4" viewBox="0 0 20 20" fill="none" aria-hidden="true"><path d="m5 10 3 3 7-7" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"></path></svg></span><span class="text-sm leading-6 text-slate-600 dark:text-slate-300"> Поставка по Казахстану </span></div></div></div><div class="relative min-h-[380px] lg:min-h-[520px]"><div class="absolute inset-0 rounded-[2rem] border border-slate-200 bg-gradient-to-br from-slate-100 via-white to-sky-50 shadow-xl shadow-slate-900/5 dark:border-slate-700 dark:from-slate-900 dark:via-slate-900 dark:to-sky-950/40"><div class="flex h-full flex-col justify-between p-6 sm:p-8"><div class="flex items-center justify-between gap-4"><div><div class="text-xs font-semibold uppercase tracking-[0.2em] text-sky-600 dark:text-sky-400"> Агровент </div><div class="mt-1 text-sm text-slate-500 dark:text-slate-400"> Вентиляция · Климат · Отопление </div></div><div class="flex h-12 w-12 items-center justify-center rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-700 dark:bg-slate-800"><svg class="h-6 w-6 text-sky-600 dark:text-sky-400" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M12 3v18M3 12h18M5.64 5.64l12.72 12.72M18.36 5.64 5.64 18.36" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"></path></svg></div></div><div class="flex flex-1 items-center justify-center py-8"><div class="relative flex h-52 w-52 items-center justify-center rounded-full border border-sky-200 bg-sky-50/80 dark:border-sky-800 dark:bg-sky-950/40 sm:h-64 sm:w-64"><div class="absolute inset-5 rounded-full border border-dashed border-sky-300 dark:border-sky-700"></div><svg class="h-20 w-20 text-sky-500/80 dark:text-sky-400/80" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M12 4a3 3 0 0 1 3 3c0 2-1.5 3-3 5M12 20a3 3 0 0 1-3-3c0-2 1.5-3 3-5M4 12a3 3 0 0 1 3-3c2 0 3 1.5 5 3M20 12a3 3 0 0 1-3 3c-2 0-3-1.5-5-3" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"></path><circle cx="12" cy="12" r="2" stroke="currentColor" stroke-width="1.5"></circle></svg></div></div><div class="rounded-2xl border border-white/70 bg-white/80 p-4 shadow-sm backdrop-blur dark:border-slate-700 dark:bg-slate-800/80"><div class="text-sm font-semibold text-slate-900 dark:text-white"> Нужна помощь с подбором? </div><p class="mt-1 text-sm leading-6 text-slate-500 dark:text-slate-400"> Отправьте параметры объекта или спецификацию — подготовим решение под вашу задачу. </p></div></div></div></div></div></div></section>`);
    };
  }
};
const _sfc_setup$1 = _sfc_main$1.setup;
_sfc_main$1.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Components/Public/Default/Home/HomeHero.vue");
  return _sfc_setup$1 ? _sfc_setup$1(props, ctx) : void 0;
};
const _sfc_main = {
  __name: "Index",
  __ssrInlineRender: true,
  setup(__props) {
    const { t } = useI18n();
    return (_ctx, _push, _parent, _attrs) => {
      _push(`<!--[-->`);
      _push(ssrRenderComponent(unref(Head), null, {
        default: withCtx((_, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(`<title${_scopeId}>${ssrInterpolate(unref(t)("home"))}</title><meta name="title"${ssrRenderAttr("content", unref(t)("home"))}${_scopeId}><meta name="description" content=""${_scopeId}><meta property="og:title"${ssrRenderAttr("content", unref(t)("home"))}${_scopeId}><meta property="og:type" content="website"${_scopeId}><meta property="og:url"${ssrRenderAttr("content", _ctx.route("home"))}${_scopeId}><meta name="twitter:card" content="summary"${_scopeId}><meta name="twitter:title"${ssrRenderAttr("content", unref(t)("home"))}${_scopeId}><meta name="DC.title"${ssrRenderAttr("content", unref(t)("home"))}${_scopeId}><meta name="DC.identifier"${ssrRenderAttr("content", _ctx.route("home"))}${_scopeId}>`);
          } else {
            return [
              createVNode("title", null, toDisplayString(unref(t)("home")), 1),
              createVNode("meta", {
                name: "title",
                content: unref(t)("home")
              }, null, 8, ["content"]),
              createVNode("meta", {
                name: "description",
                content: ""
              }),
              createVNode("meta", {
                property: "og:title",
                content: unref(t)("home")
              }, null, 8, ["content"]),
              createVNode("meta", {
                property: "og:type",
                content: "website"
              }),
              createVNode("meta", {
                property: "og:url",
                content: _ctx.route("home")
              }, null, 8, ["content"]),
              createVNode("meta", {
                name: "twitter:card",
                content: "summary"
              }),
              createVNode("meta", {
                name: "twitter:title",
                content: unref(t)("home")
              }, null, 8, ["content"]),
              createVNode("meta", {
                name: "DC.title",
                content: unref(t)("home")
              }, null, 8, ["content"]),
              createVNode("meta", {
                name: "DC.identifier",
                content: _ctx.route("home")
              }, null, 8, ["content"])
            ];
          }
        }),
        _: 1
      }, _parent));
      _push(ssrRenderComponent(_sfc_main$2, null, {
        default: withCtx((_, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(ssrRenderComponent(_sfc_main$3, null, null, _parent2, _scopeId));
            _push2(`<main class="min-h-screen"${_scopeId}>`);
            _push2(ssrRenderComponent(_sfc_main$1, null, null, _parent2, _scopeId));
            _push2(`</main>`);
            _push2(ssrRenderComponent(_sfc_main$4, null, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(Progress, null, null, _parent2, _scopeId));
          } else {
            return [
              createVNode(_sfc_main$3),
              createVNode("main", { class: "min-h-screen" }, [
                createVNode(_sfc_main$1)
              ]),
              createVNode(_sfc_main$4),
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
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Pages/Public/Default/Index.vue");
  return _sfc_setup ? _sfc_setup(props, ctx) : void 0;
};
export {
  _sfc_main as default
};
