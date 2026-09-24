import { ref, mergeProps, unref, useSSRContext } from "vue";
import { ssrRenderAttrs } from "vue/server-renderer";
import "@inertiajs/vue3";
import { useI18n } from "vue-i18n";
const _sfc_main = {
  __name: "ServerItemsPerPageSelect",
  __ssrInlineRender: true,
  props: {
    itemsPerPage: {
      type: Number,
      required: true
    },
    updateRoute: {
      type: String,
      required: true
    }
  },
  setup(__props) {
    const { t } = useI18n();
    const processing = ref(false);
    return (_ctx, _push, _parent, _attrs) => {
      _push(`<select${ssrRenderAttrs(mergeProps({
        title: unref(t)("titleItemsPerPage"),
        value: __props.itemsPerPage,
        disabled: processing.value,
        class: "w-20 px-3 py-0.5 form-select bg-white dark:bg-gray-200 text-gray-600 dark:text-gray-900 border border-slate-400 dark:border-slate-600 rounded-sm shadow-sm disabled:opacity-50"
      }, _attrs))}><option value="4">4</option><option value="8">8</option><option value="16">16</option><option value="32">32</option><option value="64">64</option><option value="128">128</option><option value="256">256</option></select>`);
    };
  }
};
const _sfc_setup = _sfc_main.setup;
_sfc_main.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Components/Admin/UI/Select/ServerItemsPerPageSelect.vue");
  return _sfc_setup ? _sfc_setup(props, ctx) : void 0;
};
export {
  _sfc_main as _
};
