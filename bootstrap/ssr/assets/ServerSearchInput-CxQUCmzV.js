import { computed, ref, watch, mergeProps, unref, useSSRContext } from "vue";
import { ssrRenderAttrs, ssrInterpolate, ssrRenderClass, ssrRenderAttr, ssrIncludeBooleanAttr } from "vue/server-renderer";
import "@inertiajs/vue3";
import { useI18n } from "vue-i18n";
const _sfc_main$1 = {
  __name: "AdminServerPagination",
  __ssrInlineRender: true,
  props: {
    pagination: {
      type: Object,
      required: true
    }
  },
  setup(__props) {
    const { t } = useI18n();
    const props = __props;
    const meta = computed(() => {
      var _a;
      return ((_a = props.pagination) == null ? void 0 : _a.meta) || {};
    });
    const currentPage = computed(() => {
      return Number(meta.value.current_page || 1);
    });
    const lastPage = computed(() => {
      return Number(meta.value.last_page || 1);
    });
    const totalItems = computed(() => {
      return Number(meta.value.total || 0);
    });
    const fromItem = computed(() => {
      return Number(meta.value.from || 0);
    });
    const toItem = computed(() => {
      return Number(meta.value.to || 0);
    });
    const pageInput = ref(currentPage.value);
    watch(
      currentPage,
      (value) => {
        pageInput.value = value;
      }
    );
    return (_ctx, _push, _parent, _attrs) => {
      if (totalItems.value > 0) {
        _push(`<div${ssrRenderAttrs(mergeProps({ class: "w-full flex flex-col xl:flex-row items-center justify-between gap-2 mt-3 mb-1" }, _attrs))}><div class="text-sm font-semibold text-gray-600 dark:text-gray-300">${ssrInterpolate(fromItem.value)} – ${ssrInterpolate(toItem.value)} из ${ssrInterpolate(totalItems.value)}</div><div class="flex items-center justify-center flex-wrap gap-2"><button type="button"${ssrIncludeBooleanAttr(currentPage.value <= 1) ? " disabled" : ""} class="${ssrRenderClass([
          currentPage.value <= 1 ? "text-slate-400 dark:text-slate-500 cursor-not-allowed" : "bg-cyan-600 hover:bg-cyan-700 dark:bg-cyan-800 dark:hover:bg-cyan-900 text-white",
          "px-3 py-1 text-sm font-semibold border rounded"
        ])}"> ← ${ssrInterpolate(unref(t)("previous"))}</button><div class="flex items-center gap-2"><input${ssrRenderAttr("value", pageInput.value)} type="number" min="1"${ssrRenderAttr("max", lastPage.value)} class="w-20 px-2 py-1 text-center border border-slate-500 font-semibold text-sm rounded-sm focus:border-indigo-500 focus:ring-indigo-300 dark:bg-cyan-800 dark:text-slate-100"><span class="text-sm font-semibold text-gray-700 dark:text-gray-200">${ssrInterpolate(unref(t)("of"))} ${ssrInterpolate(lastPage.value)}</span></div><button type="button"${ssrIncludeBooleanAttr(currentPage.value >= lastPage.value) ? " disabled" : ""} class="${ssrRenderClass([
          currentPage.value >= lastPage.value ? "text-slate-400 dark:text-slate-500 cursor-not-allowed" : "bg-cyan-600 hover:bg-cyan-700 dark:bg-cyan-800 dark:hover:bg-cyan-900 text-white",
          "px-3 py-1 text-sm font-semibold border rounded"
        ])}">${ssrInterpolate(unref(t)("next"))} → </button></div><div class="text-sm font-semibold text-gray-600 dark:text-gray-300">${ssrInterpolate(currentPage.value)} / ${ssrInterpolate(lastPage.value)}</div></div>`);
      } else {
        _push(`<!---->`);
      }
    };
  }
};
const _sfc_setup$1 = _sfc_main$1.setup;
_sfc_main$1.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Components/Admin/UI/Pagination/AdminServerPagination.vue");
  return _sfc_setup$1 ? _sfc_setup$1(props, ctx) : void 0;
};
const _sfc_main = {
  __name: "ServerSearchInput",
  __ssrInlineRender: true,
  props: {
    modelValue: {
      type: String,
      default: ""
    },
    placeholder: {
      type: String,
      default: ""
    }
  },
  setup(__props) {
    const { t } = useI18n();
    const props = __props;
    const placeholderText = computed(() => {
      return props.placeholder || `${t("search")} ...`;
    });
    const searchQuery = ref(props.modelValue || "");
    watch(
      () => props.modelValue,
      (value) => {
        searchQuery.value = value || "";
      }
    );
    return (_ctx, _push, _parent, _attrs) => {
      _push(`<div${ssrRenderAttrs(mergeProps({ class: "px-3 py-3 mb-2 border border-gray-400 dark:border-gray-500" }, _attrs))}><div class="relative w-full"><input${ssrRenderAttr("value", searchQuery.value)} type="text"${ssrRenderAttr("placeholder", placeholderText.value)} class="w-full px-2 py-1 pr-16 border border-slate-300 rounded-xs bg-white dark:bg-gray-800 text-sm font-semibold text-gray-700 dark:text-gray-300">`);
      if (searchQuery.value) {
        _push(`<button type="button" class="absolute right-8 top-1/2 -translate-y-1/2 rounded-sm px-1.5 py-1 hover:bg-blue-50 dark:hover:bg-blue-950 border border-blue-700 dark:border-blue-300"${ssrRenderAttr("title", unref(t)("clear"))}><span class="flex items-center justify-center gap-2"><svg class="w-4 h-4 text-blue-700 dark:text-blue-300" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 512 512"><path d="M370.72 133.28C339.458 104.008 298.888 87.962 255.848 88c-77.458.068-144.328 53.178-162.791 126.85-1.344 5.363-6.122 9.15-11.651 9.15H24.103c-7.498 0-13.194-6.807-11.807-14.176C33.933 94.924 134.813 8 256 8c66.448 0 126.791 26.136 171.315 68.685L463.03 40.97C478.149 25.851 504 36.559 504 57.941V192c0 13.255-10.745 24-24 24H345.941c-21.382 0-32.09-25.851-16.971-40.971l41.75-41.749zM32 296h134.059c21.382 0 32.09 25.851 16.971 40.971l-41.75 41.75c31.262 29.273 71.835 45.319 114.876 45.28 77.418-.07 144.315-53.144 162.787-126.849 1.344-5.363 6.122-9.15 11.651-9.15h57.304c7.498 0 13.194 6.807 11.807 14.176C478.067 417.076 377.187 504 256 504c-66.448 0-126.791-26.136-171.315-68.685L48.97 471.03C33.851 486.149 8 475.441 8 454.059V320c0-13.255 10.745-24 24-24z"></path></svg></span></button>`);
      } else {
        _push(`<!---->`);
      }
      _push(`<svg class="absolute right-2 top-2 w-4 h-4 text-gray-400 dark:text-gray-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M12.9 14.32a8 8 0 111.42-1.42l4.58 4.58a1 1 0 01-1.42 1.42l-4.58-4.58zm-4.9 0a6 6 0 100-12 6 6 0 000 12z" clip-rule="evenodd"></path></svg></div></div>`);
    };
  }
};
const _sfc_setup = _sfc_main.setup;
_sfc_main.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Components/Admin/UI/Search/ServerSearchInput.vue");
  return _sfc_setup ? _sfc_setup(props, ctx) : void 0;
};
export {
  _sfc_main as _,
  _sfc_main$1 as a
};
