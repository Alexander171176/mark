import { mergeProps, useSSRContext } from "vue";
import { ssrRenderAttrs, ssrRenderSlot, ssrInterpolate } from "vue/server-renderer";
const _sfc_main = {
  __name: "LabelInput",
  __ssrInlineRender: true,
  props: {
    value: {
      type: String
    }
  },
  setup(__props) {
    return (_ctx, _push, _parent, _attrs) => {
      _push(`<label${ssrRenderAttrs(mergeProps({ class: "mb-1 block font-medium text-sm text-indigo-600 dark:text-sky-500" }, _attrs))}>`);
      if (_ctx.$slots.default) {
        ssrRenderSlot(_ctx.$slots, "default", {}, null, _push, _parent);
      } else {
        _push(`<span>${ssrInterpolate(__props.value)}</span>`);
      }
      _push(`</label>`);
    };
  }
};
const _sfc_setup = _sfc_main.setup;
_sfc_main.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Components/Admin/UI/Input/LabelInput.vue");
  return _sfc_setup ? _sfc_setup(props, ctx) : void 0;
};
export {
  _sfc_main as _
};
