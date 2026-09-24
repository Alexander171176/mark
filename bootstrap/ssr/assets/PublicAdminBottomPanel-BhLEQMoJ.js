import { computed, ref, watch, mergeProps, unref, useSSRContext, withCtx, createBlock, openBlock, createVNode } from "vue";
import { ssrRenderAttrs, ssrRenderAttr, ssrInterpolate, ssrIncludeBooleanAttr, ssrRenderClass, ssrRenderComponent, ssrRenderSlot } from "vue/server-renderer";
import { useI18n } from "vue-i18n";
import { Link } from "@inertiajs/vue3";
import { _ as _sfc_main$3 } from "./ProcessingModeSwitcher-BJvzFf6_.js";
const _sfc_main$2 = {
  __name: "Pagination",
  __ssrInlineRender: true,
  props: {
    currentPage: { type: Number, default: 1 },
    lastPage: { type: Number, default: 1 },
    found: { type: Number, default: 0 }
  },
  emits: ["prev", "next", "go"],
  setup(__props, { emit: __emit }) {
    const { t } = useI18n();
    const props = __props;
    const show = computed(() => props.lastPage > 1);
    const pageInput = ref(props.currentPage);
    watch(() => props.currentPage, (v) => {
      pageInput.value = v;
    });
    return (_ctx, _push, _parent, _attrs) => {
      if (show.value) {
        _push(`<div${ssrRenderAttrs(mergeProps({ class: "mt-8 flex flex-col items-center gap-3" }, _attrs))}><div class="flex flex-wrap items-center justify-center gap-2"><button type="button"${ssrIncludeBooleanAttr(__props.currentPage <= 1) ? " disabled" : ""}${ssrRenderAttr("title", unref(t)("back"))} class="inline-flex items-center gap-2 rounded-sm px-2 py-2 text-sm font-semibold transition bg-slate-100 dark:bg-slate-800 hover:bg-slate-50 dark:hover:bg-slate-700 border border-slate-400 dark:border-slate-700 text-indigo-500 dark:text-indigo-300 disabled:cursor-not-allowed disabled:opacity-50"><svg class="h-4 w-4" fill="currentColor" viewBox="0 0 448 512"><path d="M223.7 239l136-136c9.4-9.4 24.6-9.4 33.9 0l22.6 22.6c9.4 9.4 9.4 24.6 0 33.9L319.9 256l96.4 96.4c9.4 9.4 9.4 24.6 0 33.9L393.7 409c-9.4 9.4-24.6 9.4-33.9 0l-136-136c-9.5-9.4-9.5-24.6-.1-34zm-192 34l136 136c9.4 9.4 24.6 9.4 33.9 0l22.6-22.6c9.4-9.4 9.4-24.6 0-33.9L127.9 256l96.4-96.4c9.4-9.4 9.4-24.6 0-33.9L201.7 103c-9.4-9.4-24.6-9.4-33.9 0l-136 136c-9.5 9.4-9.5 24.6-.1 34z"></path></svg></button><div class="px-3 py-2 flex items-center gap-2 rounded-sm border border-slate-400 dark:border-slate-700 bg-slate-100 dark:bg-slate-800"><span class="text-sm font-semibold text-slate-600 dark:text-slate-300">${ssrInterpolate(unref(t)("page"))}</span><input${ssrRenderAttr("value", pageInput.value)} inputmode="numeric" class="w-16 px-2 py-1 rounded-sm text-center text-sm font-semibold text-slate-900 dark:text-slate-100 bg-white dark:bg-gray-950 border border-slate-400 dark:border-slate-700 dark:focus:border-blue-500 dark:focus:ring-blue-900/40 outline-none focus:border-blue-400 focus:ring-2 focus:ring-blue-200"><span class="text-sm font-semibold text-slate-700 dark:text-slate-300">${ssrInterpolate(unref(t)("of"))} ${ssrInterpolate(__props.lastPage)}</span></div><button type="button"${ssrIncludeBooleanAttr(__props.currentPage >= __props.lastPage) ? " disabled" : ""}${ssrRenderAttr("title", unref(t)("next"))} class="inline-flex items-center gap-2 rounded-sm px-2 py-2 text-sm font-semibold transition bg-slate-100 dark:bg-slate-800 hover:bg-slate-50 dark:hover:bg-slate-700 border border-slate-400 dark:border-slate-700 text-indigo-500 dark:text-indigo-300 disabled:cursor-not-allowed disabled:opacity-50"><svg class="h-4 w-4" fill="currentColor" viewBox="0 0 448 512"><path d="M224.3 273l-136 136c-9.4 9.4-24.6 9.4-33.9 0l-22.6-22.6c-9.4-9.4-9.4-24.6 0-33.9l96.4-96.4-96.4-96.4c-9.4-9.4-9.4-24.6 0-33.9L54.3 103c9.4-9.4 24.6-9.4 33.9 0l136 136c9.5 9.4 9.5 24.6.1 34zm192-34l-136-136c-9.4-9.4-24.6-9.4-33.9 0l-22.6 22.6c-9.4 9.4-9.4 24.6 0 33.9l96.4 96.4-96.4 96.4c-9.4 9.4-9.4 24.6 0 33.9l22.6 22.6c9.4 9.4 24.6 9.4 33.9 0l136-136c9.4-9.2 9.4-24.4 0-33.8z"></path></svg></button></div><div class="text-xs text-slate-600 dark:text-slate-400">${ssrInterpolate(unref(t)("page"))} <span class="font-semibold">${ssrInterpolate(__props.currentPage)}/${ssrInterpolate(__props.lastPage)}</span> · ${ssrInterpolate(unref(t)("found"))}: <span class="font-semibold">${ssrInterpolate(__props.found)}</span></div></div>`);
      } else {
        _push(`<!---->`);
      }
    };
  }
};
const _sfc_setup$2 = _sfc_main$2.setup;
_sfc_main$2.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Components/Public/Default/Pagination/Pagination.vue");
  return _sfc_setup$2 ? _sfc_setup$2(props, ctx) : void 0;
};
const _sfc_main$1 = {
  __name: "FrontendPagination",
  __ssrInlineRender: true,
  props: {
    currentPage: { type: Number, default: 1 },
    itemsPerPage: { type: Number, default: 6 },
    totalItems: { type: Number, default: 0 }
  },
  emits: ["update:currentPage"],
  setup(__props, { emit: __emit }) {
    const { t } = useI18n();
    const props = __props;
    const totalPages = computed(() => {
      return Math.max(1, Math.ceil(props.totalItems / props.itemsPerPage));
    });
    const show = computed(() => totalPages.value > 1);
    const pageInput = ref(props.currentPage);
    watch(
      () => props.currentPage,
      (value) => {
        pageInput.value = value;
      }
    );
    return (_ctx, _push, _parent, _attrs) => {
      if (show.value) {
        _push(`<div${ssrRenderAttrs(mergeProps({ class: "mt-8 flex flex-col items-center gap-3" }, _attrs))}><div class="flex flex-wrap items-center justify-center gap-2"><button type="button"${ssrIncludeBooleanAttr(__props.currentPage <= 1) ? " disabled" : ""}${ssrRenderAttr("title", unref(t)("back"))} class="inline-flex items-center gap-2 rounded-sm px-2 py-2 text-sm font-semibold transition bg-slate-100 dark:bg-slate-800 hover:bg-slate-50 dark:hover:bg-slate-700 border border-slate-400 dark:border-slate-700 text-indigo-500 dark:text-indigo-300 disabled:cursor-not-allowed disabled:opacity-50"><svg class="h-4 w-4" fill="currentColor" viewBox="0 0 448 512"><path d="M223.7 239l136-136c9.4-9.4 24.6-9.4 33.9 0l22.6 22.6c9.4 9.4 9.4 24.6 0 33.9L319.9 256l96.4 96.4c9.4 9.4 9.4 24.6 0 33.9L393.7 409c-9.4 9.4-24.6 9.4-33.9 0l-136-136c-9.5-9.4-9.5-24.6-.1-34zm-192 34l136 136c9.4 9.4 24.6 9.4 33.9 0l22.6-22.6c9.4-9.4 9.4-24.6 0-33.9L127.9 256l96.4-96.4c9.4-9.4 9.4-24.6 0-33.9L201.7 103c-9.4-9.4-24.6-9.4-33.9 0l-136 136c-9.5 9.4-9.5 24.6-.1 34z"></path></svg></button><div class="px-3 py-2 flex items-center gap-2 rounded-sm border border-slate-400 dark:border-slate-700 bg-slate-100 dark:bg-slate-800"><span class="text-sm font-semibold text-slate-600 dark:text-slate-300">${ssrInterpolate(unref(t)("page"))}</span><input${ssrRenderAttr("value", pageInput.value)} inputmode="numeric" class="w-16 px-2 py-1 rounded-sm text-center text-sm font-semibold text-slate-900 dark:text-slate-100 bg-white dark:bg-gray-950 border border-slate-400 dark:border-slate-700 dark:focus:border-blue-500 dark:focus:ring-blue-900/40 outline-none focus:border-blue-400 focus:ring-2 focus:ring-blue-200"><span class="text-sm font-semibold text-slate-700 dark:text-slate-300">${ssrInterpolate(unref(t)("of"))} ${ssrInterpolate(totalPages.value)}</span></div><button type="button"${ssrIncludeBooleanAttr(__props.currentPage >= totalPages.value) ? " disabled" : ""}${ssrRenderAttr("title", unref(t)("next"))} class="inline-flex items-center gap-2 rounded-sm px-2 py-2 text-sm font-semibold transition bg-slate-100 dark:bg-slate-800 hover:bg-slate-50 dark:hover:bg-slate-700 border border-slate-400 dark:border-slate-700 text-indigo-500 dark:text-indigo-300 disabled:cursor-not-allowed disabled:opacity-50"><svg class="h-4 w-4" fill="currentColor" viewBox="0 0 448 512"><path d="M224.3 273l-136 136c-9.4 9.4-24.6 9.4-33.9 0l-22.6-22.6c-9.4-9.4-9.4-24.6 0-33.9l96.4-96.4-96.4-96.4c-9.4-9.4-9.4-24.6 0-33.9L54.3 103c9.4-9.4 24.6-9.4 33.9 0l136 136c9.5 9.4 9.5 24.6.1 34zm192-34l-136-136c-9.4-9.4-24.6-9.4-33.9 0l-22.6 22.6c-9.4 9.4-9.4 24.6 0 33.9l96.4 96.4-96.4 96.4c-9.4 9.4-9.4 24.6 0 33.9l22.6 22.6c9.4 9.4 24.6 9.4 33.9 0l136-136c9.4-9.2 9.4-24.4 0-33.8z"></path></svg></button></div><div class="text-xs text-slate-600 dark:text-slate-400">${ssrInterpolate(unref(t)("page"))} <span class="font-semibold">${ssrInterpolate(__props.currentPage)}/${ssrInterpolate(totalPages.value)}</span> · ${ssrInterpolate(unref(t)("found"))}: <span class="font-semibold">${ssrInterpolate(__props.totalItems)}</span></div></div>`);
      } else {
        _push(`<!---->`);
      }
    };
  }
};
const _sfc_setup$1 = _sfc_main$1.setup;
_sfc_main$1.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Components/Public/Default/Pagination/FrontendPagination.vue");
  return _sfc_setup$1 ? _sfc_setup$1(props, ctx) : void 0;
};
const _sfc_main = {
  __name: "PublicAdminBottomPanel",
  __ssrInlineRender: true,
  props: {
    settingKey: { type: String, required: true },
    mode: { type: String, default: "server" },
    useServerProcessing: { type: Boolean, default: false },
    total: { type: Number, default: 0 },
    storageKey: { type: String, default: "public_admin_panel_collapsed" }
  },
  setup(__props) {
    const { t } = useI18n();
    const props = __props;
    const getStoredBoolean = (key, defaultValue = false) => {
      const value = localStorage.getItem(key);
      if (value === null) {
        return defaultValue;
      }
      return value === "true";
    };
    const collapsed = ref(getStoredBoolean(props.storageKey, false));
    watch(collapsed, (value) => {
      localStorage.setItem(props.storageKey, String(value));
    });
    return (_ctx, _push, _parent, _attrs) => {
      _push(`<div${ssrRenderAttrs(mergeProps({ class: "fixed bottom-0 left-0 right-0 z-[9999]" }, _attrs))}><button type="button" class="${ssrRenderClass([collapsed.value ? "bottom-0" : "bottom-12 sm:bottom-8", "absolute left-1/2 -translate-x-1/2 flex h-4 w-10 items-center justify-center rounded-t-full border border-b-0 border-slate-400/60 bg-slate-300/95 dark:bg-slate-700/95 text-slate-700 dark:text-slate-300 shadow-md backdrop-blur-md hover:text-indigo-600 dark:hover:text-indigo-300"])}"${ssrRenderAttr("title", collapsed.value ? unref(t)("show") : unref(t)("hide"))}><svg class="${ssrRenderClass([collapsed.value ? "rotate-180" : "", "h-3 w-3 transition-transform duration-300"])}" fill="currentColor" viewBox="0 0 320 512"><path d="M182.6 137.4c-12.5-12.5-32.8-12.5-45.3 0l-128 128c-9.2 9.2-11.9 22.9-6.9 34.9S19.1 320 32 320h256c12.9 0 24.6-7.8 29.6-19.8s2.2-25.7-6.9-34.9l-128-128z"></path></svg></button><div class="${ssrRenderClass([collapsed.value ? "translate-y-full" : "translate-y-0", "min-h-10 sm:min-h-8 flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-2 px-2 sm:px-3 py-2 sm:py-0 border-t border-slate-400/40 bg-slate-300/90 dark:bg-slate-700/90 backdrop-blur-md text-[11px] transition-transform duration-300"])}"><div class="flex items-center justify-center sm:justify-start gap-2">`);
      _push(ssrRenderComponent(unref(Link), {
        href: _ctx.route("admin.index"),
        title: unref(t)("adminPanel"),
        class: "w-fit"
      }, {
        default: withCtx((_, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(`<svg class="shrink-0 h-7 w-7 fill-current text-cyan-600 dark:text-cyan-400" viewBox="0 0 512 512"${_scopeId}><path d="M157.52 272h36.96L176 218.78 157.52 272zM352 256c-13.23 0-24 10.77-24 24s10.77 24 24 24 24-10.77 24-24-10.77-24-24-24zM464 64H48C21.5 64 0 85.5 0 112v288c0 26.5 21.5 48 48 48h416c26.5 0 48-21.5 48-48V112c0-26.5-21.5-48-48-48zM250.58 352h-16.94c-6.81 0-12.88-4.32-15.12-10.75L211.15 320h-70.29l-7.38 21.25A16 16 0 0 1 118.36 352h-16.94c-11.01 0-18.73-10.85-15.12-21.25L140 176.12A23.995 23.995 0 0 1 162.67 160h26.66A23.99 23.99 0 0 1 212 176.13l53.69 154.62c3.61 10.4-4.11 21.25-15.11 21.25zM424 336c0 8.84-7.16 16-16 16h-16c-4.85 0-9.04-2.27-11.98-5.68-8.62 3.66-18.09 5.68-28.02 5.68-39.7 0-72-32.3-72-72s32.3-72 72-72c8.46 0 16.46 1.73 24 4.42V176c0-8.84 7.16-16 16-16h16c8.84 0 16 7.16 16 16v160z"${_scopeId}></path></svg>`);
          } else {
            return [
              (openBlock(), createBlock("svg", {
                class: "shrink-0 h-7 w-7 fill-current text-cyan-600 dark:text-cyan-400",
                viewBox: "0 0 512 512"
              }, [
                createVNode("path", { d: "M157.52 272h36.96L176 218.78 157.52 272zM352 256c-13.23 0-24 10.77-24 24s10.77 24 24 24 24-10.77 24-24-10.77-24-24-24zM464 64H48C21.5 64 0 85.5 0 112v288c0 26.5 21.5 48 48 48h416c26.5 0 48-21.5 48-48V112c0-26.5-21.5-48-48-48zM250.58 352h-16.94c-6.81 0-12.88-4.32-15.12-10.75L211.15 320h-70.29l-7.38 21.25A16 16 0 0 1 118.36 352h-16.94c-11.01 0-18.73-10.85-15.12-21.25L140 176.12A23.995 23.995 0 0 1 162.67 160h26.66A23.99 23.99 0 0 1 212 176.13l53.69 154.62c3.61 10.4-4.11 21.25-15.11 21.25zM424 336c0 8.84-7.16 16-16 16h-16c-4.85 0-9.04-2.27-11.98-5.68-8.62 3.66-18.09 5.68-28.02 5.68-39.7 0-72-32.3-72-72s32.3-72 72-72c8.46 0 16.46 1.73 24 4.42V176c0-8.84 7.16-16 16-16h16c8.84 0 16 7.16 16 16v160z" })
              ]))
            ];
          }
        }),
        _: 1
      }, _parent));
      ssrRenderSlot(_ctx.$slots, "left", {}, null, _push, _parent);
      _push(`</div><div class="flex items-center justify-center sm:justify-end gap-2 overflow-x-auto">`);
      ssrRenderSlot(_ctx.$slots, "right-before", {}, null, _push, _parent);
      _push(ssrRenderComponent(_sfc_main$3, {
        "setting-key": __props.settingKey,
        mode: __props.mode,
        "use-server-processing": __props.useServerProcessing,
        total: __props.total
      }, null, _parent));
      ssrRenderSlot(_ctx.$slots, "right-after", {}, null, _push, _parent);
      _push(`</div></div></div>`);
    };
  }
};
const _sfc_setup = _sfc_main.setup;
_sfc_main.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Components/Admin/UI/PublicAdminPanel/PublicAdminBottomPanel.vue");
  return _sfc_setup ? _sfc_setup(props, ctx) : void 0;
};
export {
  _sfc_main$2 as _,
  _sfc_main$1 as a,
  _sfc_main as b
};
