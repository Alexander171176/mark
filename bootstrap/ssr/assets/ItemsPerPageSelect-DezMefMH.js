import { mergeProps, unref, useSSRContext } from "vue";
import { ssrRenderAttrs, ssrRenderAttr } from "vue/server-renderer";
import { useI18n } from "vue-i18n";
const _sfc_main = {
  __name: "ItemsPerPageSelect",
  __ssrInlineRender: true,
  props: {
    itemsPerPage: {
      type: Number,
      required: true
    }
  },
  emits: [
    "update:itemsPerPage"
  ],
  setup(__props, { emit: __emit }) {
    const { t } = useI18n();
    const props = __props;
    return (_ctx, _push, _parent, _attrs) => {
      _push(`<select${ssrRenderAttrs(mergeProps({
        title: unref(t)("titleItemsPerPage"),
        value: props.itemsPerPage,
        class: "w-20 px-3 py-0.5 form-select bg-white dark:bg-gray-200 text-gray-600 dark:text-gray-900 border border-slate-400 dark:border-slate-600 rounded-sm shadow-sm"
      }, _attrs))}><option${ssrRenderAttr("value", 10)}>10</option><option${ssrRenderAttr("value", 20)}>20</option><option${ssrRenderAttr("value", 30)}>30</option><option${ssrRenderAttr("value", 50)}>50</option><option${ssrRenderAttr("value", 100)}>100</option></select>`);
    };
  }
};
const _sfc_setup = _sfc_main.setup;
_sfc_main.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Components/Admin/Market/MarketRecentlyViewedProduct/Select/ItemsPerPageSelect.vue");
  return _sfc_setup ? _sfc_setup(props, ctx) : void 0;
};
export {
  _sfc_main as _
};
