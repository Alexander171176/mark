import { mergeProps, useSSRContext } from "vue";
import { ssrRenderAttrs, ssrRenderSlot } from "vue/server-renderer";
import { _ as _export_sfc } from "./_plugin-vue_export-helper-1tPrXgE0.js";
const _sfc_main = {
  name: "HeadingAuth"
};
function _sfc_ssrRender(_ctx, _push, _parent, _attrs, $props, $setup, $data, $options) {
  _push(`<h1${ssrRenderAttrs(mergeProps({ class: "text-center text-lg text-blue-400 font-semibold mb-1" }, _attrs))}>`);
  ssrRenderSlot(_ctx.$slots, "default", { type: "text" }, null, _push, _parent);
  _push(` ✨ </h1>`);
}
const _sfc_setup = _sfc_main.setup;
_sfc_main.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Components/User/Heading/HeadingAuth.vue");
  return _sfc_setup ? _sfc_setup(props, ctx) : void 0;
};
const HeadingAuth = /* @__PURE__ */ _export_sfc(_sfc_main, [["ssrRender", _sfc_ssrRender]]);
export {
  HeadingAuth as H
};
