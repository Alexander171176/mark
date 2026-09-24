import { ref, watch, mergeProps, unref, useSSRContext } from "vue";
import { ssrRenderAttrs, ssrRenderAttr, ssrInterpolate, ssrRenderList, ssrIncludeBooleanAttr, ssrLooseContain, ssrLooseEqual, ssrRenderComponent } from "vue/server-renderer";
import { useI18n } from "vue-i18n";
import { _ as _sfc_main$1 } from "./ViewModeToggle-DMCnQ0wo.js";
const _sfc_main = {
  __name: "FrontendEntityPageToolbar",
  __ssrInlineRender: true,
  props: {
    modelValue: { type: String, default: "" },
    found: { type: Number, default: 0 },
    viewMode: { type: String, default: "grid" },
    sortValue: { type: String, default: "date_desc" },
    sortOptions: { type: Array, default: () => [] },
    defaultSort: { type: String, default: "date_desc" },
    foundLabel: { type: String, default: "" },
    searchPlaceholder: { type: String, default: "" }
  },
  emits: [
    "update:modelValue",
    "reset",
    "update:viewMode",
    "update:sortValue"
  ],
  setup(__props, { emit: __emit }) {
    const { t } = useI18n();
    const props = __props;
    const emit = __emit;
    const qLocal = ref(String(props.modelValue ?? ""));
    const sortLocal = ref(String(props.sortValue ?? props.defaultSort ?? "date_desc"));
    watch(
      () => props.modelValue,
      (value) => {
        qLocal.value = String(value ?? "");
      }
    );
    watch(
      () => props.sortValue,
      (value) => {
        sortLocal.value = String(value ?? props.defaultSort ?? "date_desc");
      }
    );
    const setView = (mode) => {
      emit("update:viewMode", mode);
    };
    return (_ctx, _push, _parent, _attrs) => {
      _push(`<div${ssrRenderAttrs(mergeProps({ class: "my-3" }, _attrs))}><div class="flex flex-col gap-2 sm:flex-row sm:items-center"><div class="relative w-full"><input${ssrRenderAttr("value", qLocal.value)} type="text"${ssrRenderAttr("placeholder", __props.searchPlaceholder || unref(t)("searchByName"))} class="w-full px-10 py-1.5 rounded-md shadow-sm search outline-none text-md font-semibold border border-gray-400 dark:border-gray-500 focus:border-indigo-400 focus:ring-2 focus:ring-indigo-200 placeholder:text-slate-400 dark:placeholder:text-slate-500 dark:focus:border-indigo-500 dark:focus:ring-indigo-900/40 placeholder:font-semibold"><svg class="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" fill="currentColor"><path d="M505 442.7 405.3 343c28.4-34.9 45.7-79.4 45.7-128C451 96.5 354.5 0 231 0S11 96.5 11 215.9 107.5 431.8 231 431.8c48.6 0 93.1-17.3 128-45.7l99.7 99.7c4.5 4.5 10.6 7 17 7s12.5-2.5 17-7c9.4-9.4 9.4-24.6.3-33.8zM231 383.8c-92.9 0-168-75.1-168-168S138.1 47.8 231 47.8s168 75.1 168 168-75.1 168-168 168z"></path></svg>`);
      if (qLocal.value) {
        _push(`<button type="button" class="absolute right-2 top-1/2 -translate-y-1/2 rounded-md px-2 py-1.5 hover:bg-indigo-50 dark:hover:bg-indigo-950 border border-indigo-700 dark:border-indigo-300"><span class="flex items-center justify-center gap-2"${ssrRenderAttr("title", unref(t)("reset"))}><svg class="w-4 h-4 text-indigo-700 dark:text-indigo-300" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 512 512"><path d="M370.72 133.28C339.458 104.008 298.888 87.962 255.848 88c-77.458.068-144.328 53.178-162.791 126.85-1.344 5.363-6.122 9.15-11.651 9.15H24.103c-7.498 0-13.194-6.807-11.807-14.176C33.933 94.924 134.813 8 256 8c66.448 0 126.791 26.136 171.315 68.685L463.03 40.97C478.149 25.851 504 36.559 504 57.941V192c0 13.255-10.745 24-24 24H345.941c-21.382 0-32.09-25.851-16.971-40.971l41.75-41.749zM32 296h134.059c21.382 0 32.09 25.851 16.971 40.971l-41.75 41.75c31.262 29.273 71.835 45.319 114.876 45.28 77.418-.07 144.315-53.144 162.787-126.849 1.344-5.363 6.122-9.15 11.651-9.15h57.304c7.498 0 13.194 6.807 11.807 14.176C478.067 417.076 377.187 504 256 504c-66.448 0-126.791-26.136-171.315-68.685L48.97 471.03C33.851 486.149 8 475.441 8 454.059V320c0-13.255 10.745-24 24-24z"></path></svg></span></button>`);
      } else {
        _push(`<!---->`);
      }
      _push(`</div></div><div class="px-2 py-3 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between"><div class="my-3 text-start center text-xs text-slate-500 dark:text-slate-400">${ssrInterpolate(unref(t)("found"))} ${ssrInterpolate(__props.foundLabel)}: <span class="font-bold">${ssrInterpolate(__props.found)}</span></div><div class="flex items-center gap-2 justify-end"><div class="flex flex-row items-center gap-2"><label class="hidden xl:block text-xs font-semibold text-slate-500 dark:text-slate-400">${ssrInterpolate(unref(t)("sort"))}</label><select class="rounded-sm pl-2 pr-6 py-1.5 text-xs font-semibold border border-gray-400 dark:border-gray-500 bg-white dark:bg-gray-900 outline-none text-slate-700 dark:text-slate-300 focus:border-indigo-400 focus:ring-2 focus:ring-indigo-200 dark:focus:border-indigo-500 dark:focus:ring-indigo-900/40"><!--[-->`);
      ssrRenderList(__props.sortOptions, (option) => {
        _push(`<option${ssrRenderAttr("value", option.value)}${ssrIncludeBooleanAttr(Array.isArray(sortLocal.value) ? ssrLooseContain(sortLocal.value, option.value) : ssrLooseEqual(sortLocal.value, option.value)) ? " selected" : ""}>${ssrInterpolate(option.label)}</option>`);
      });
      _push(`<!--]--></select></div>`);
      _push(ssrRenderComponent(_sfc_main$1, {
        "model-value": __props.viewMode,
        "onUpdate:modelValue": setView
      }, null, _parent));
      _push(`</div></div></div>`);
    };
  }
};
const _sfc_setup = _sfc_main.setup;
_sfc_main.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Components/Public/Default/PageToolbar/FrontendEntityPageToolbar.vue");
  return _sfc_setup ? _sfc_setup(props, ctx) : void 0;
};
export {
  _sfc_main as _
};
