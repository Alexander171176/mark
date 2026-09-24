import { mergeProps, useSSRContext, computed, ref, watch, unref } from "vue";
import { ssrRenderAttrs, ssrRenderClass, ssrInterpolate, ssrRenderAttr, ssrIncludeBooleanAttr } from "vue/server-renderer";
import { useI18n } from "vue-i18n";
const _sfc_main$2 = {
  __name: "ToggleViewButton",
  __ssrInlineRender: true,
  props: {
    /**
     * Режим отображения:
     *  - 'table' — таблица
     *  - 'cards' — карточки (grid)
     */
    viewMode: {
      type: String,
      default: "table"
    }
  },
  emits: ["update:viewMode"],
  setup(__props, { emit: __emit }) {
    return (_ctx, _push, _parent, _attrs) => {
      _push(`<div${ssrRenderAttrs(mergeProps({ class: "flex justify-end items-center space-x-2" }, _attrs))}><button type="button" class="${ssrRenderClass([
        "p-1 border transition-colors duration-200 rounded",
        __props.viewMode === "cards" ? "border-slate-400 dark:border-slate-200 text-red-400 dark:text-red-200" : "border-slate-300 dark:border-slate-400 text-slate-600 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-600 hover:border-slate-500 dark:hover:border-slate-500"
      ])}"><svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path></svg></button><button type="button" class="${ssrRenderClass([
        "p-1 border transition-colors duration-200 rounded",
        __props.viewMode === "table" ? "border-slate-400 dark:border-slate-200 text-red-400 dark:text-red-200" : "border-slate-300 dark:border-slate-400 text-slate-600 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-600 hover:border-slate-500 dark:hover:border-slate-500"
      ])}"><svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"></path></svg></button></div>`);
    };
  }
};
const _sfc_setup$2 = _sfc_main$2.setup;
_sfc_main$2.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Components/Admin/UI/Buttons/ToggleViewButton.vue");
  return _sfc_setup$2 ? _sfc_setup$2(props, ctx) : void 0;
};
const _sfc_main$1 = {
  __name: "Pagination",
  __ssrInlineRender: true,
  props: {
    currentPage: {
      type: Number,
      required: true
    },
    itemsPerPage: {
      type: Number,
      required: true
    },
    totalItems: {
      type: Number,
      required: true
    }
  },
  emits: [
    "update:currentPage"
  ],
  setup(__props, { emit: __emit }) {
    const { t } = useI18n();
    const props = __props;
    const totalPages = computed(() => {
      if (props.totalItems <= 0 || props.itemsPerPage <= 0) {
        return 1;
      }
      return Math.max(
        1,
        Math.ceil(
          props.totalItems / props.itemsPerPage
        )
      );
    });
    const pageInput = ref(
      props.currentPage
    );
    watch(
      () => props.currentPage,
      (value) => {
        pageInput.value = value;
      }
    );
    const fromItem = computed(() => {
      if (props.totalItems <= 0) {
        return 0;
      }
      return (props.currentPage - 1) * props.itemsPerPage + 1;
    });
    const toItem = computed(() => {
      if (props.totalItems <= 0) {
        return 0;
      }
      return Math.min(
        props.currentPage * props.itemsPerPage,
        props.totalItems
      );
    });
    return (_ctx, _push, _parent, _attrs) => {
      if (__props.totalItems > 0) {
        _push(`<div${ssrRenderAttrs(mergeProps({ class: "w-full flex flex-col xl:flex-row items-center justify-between gap-2 mt-3 mb-1" }, _attrs))}><div class="text-sm font-semibold text-gray-600 dark:text-gray-300">${ssrInterpolate(fromItem.value)} – ${ssrInterpolate(toItem.value)} из ${ssrInterpolate(__props.totalItems)}</div><div class="flex items-center justify-center flex-wrap gap-2"><button type="button"${ssrIncludeBooleanAttr(__props.currentPage <= 1) ? " disabled" : ""} class="${ssrRenderClass([
          __props.currentPage <= 1 ? "text-slate-400 dark:text-slate-500 cursor-not-allowed" : "bg-cyan-600 hover:bg-cyan-700 dark:bg-cyan-800 dark:hover:bg-cyan-900 text-white",
          "px-3 py-1 text-sm font-semibold border rounded"
        ])}"> ← ${ssrInterpolate(unref(t)("previous"))}</button><div class="flex items-center gap-2"><input${ssrRenderAttr("value", pageInput.value)} type="number" min="1"${ssrRenderAttr("max", totalPages.value)} class="w-20 px-2 py-1 text-center border border-slate-500 font-semibold text-sm rounded-sm focus:border-indigo-500 focus:ring-indigo-300 dark:bg-cyan-800 dark:text-slate-100"><span class="text-sm font-semibold text-gray-700 dark:text-gray-200">${ssrInterpolate(unref(t)("of"))} ${ssrInterpolate(totalPages.value)}</span></div><button type="button"${ssrIncludeBooleanAttr(__props.currentPage >= totalPages.value) ? " disabled" : ""} class="${ssrRenderClass([
          __props.currentPage >= totalPages.value ? "text-slate-400 dark:text-slate-500 cursor-not-allowed" : "bg-cyan-600 hover:bg-cyan-700 dark:bg-cyan-800 dark:hover:bg-cyan-900 text-white",
          "px-3 py-1 text-sm font-semibold border rounded"
        ])}">${ssrInterpolate(unref(t)("next"))} → </button></div><div class="text-sm font-semibold text-gray-600 dark:text-gray-300">${ssrInterpolate(__props.currentPage)} / ${ssrInterpolate(totalPages.value)}</div></div>`);
      } else {
        _push(`<!---->`);
      }
    };
  }
};
const _sfc_setup$1 = _sfc_main$1.setup;
_sfc_main$1.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Components/Admin/UI/Pagination/Pagination.vue");
  return _sfc_setup$1 ? _sfc_setup$1(props, ctx) : void 0;
};
const _sfc_main = {
  __name: "ItemsPerPageSelect",
  __ssrInlineRender: true,
  props: {
    itemsPerPage: {
      type: Number,
      required: true
    }
  },
  emits: ["update:itemsPerPage"],
  setup(__props, { emit: __emit }) {
    const { t } = useI18n();
    const props = __props;
    return (_ctx, _push, _parent, _attrs) => {
      _push(`<select${ssrRenderAttrs(mergeProps({
        title: unref(t)("titleItemsPerPage"),
        value: props.itemsPerPage,
        class: "w-20 px-3 py-0.5 form-select bg-white dark:bg-gray-200 text-gray-600 dark:text-gray-900 border border-slate-400 dark:border-slate-600 rounded-sm shadow-sm"
      }, _attrs))}><option value="4">4</option><option value="8">8</option><option value="16">16</option><option value="32">32</option><option value="64">64</option><option value="128">128</option><option value="256">256</option></select>`);
    };
  }
};
const _sfc_setup = _sfc_main.setup;
_sfc_main.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Components/Admin/UI/Select/ItemsPerPageSelect.vue");
  return _sfc_setup ? _sfc_setup(props, ctx) : void 0;
};
export {
  _sfc_main as _,
  _sfc_main$2 as a,
  _sfc_main$1 as b
};
