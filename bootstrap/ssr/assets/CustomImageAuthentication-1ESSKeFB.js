import { mergeProps, unref, useSSRContext } from "vue";
import { ssrRenderAttrs } from "vue/server-renderer";
import { _ as _export_sfc } from "./_plugin-vue_export-helper-1tPrXgE0.js";
const _sfc_main = {
  __name: "CustomImageAuthentication",
  __ssrInlineRender: true,
  setup(__props) {
    const bgImage = new URL("../../../../images/desktop.jpg", import.meta.url).href;
    return (_ctx, _push, _parent, _attrs) => {
      _push(`<div${ssrRenderAttrs(mergeProps({
        class: "fixed inset-0 z-0 bg-cover bg-center animate-auth-background",
        style: { backgroundImage: `url(${unref(bgImage)})` },
        "aria-hidden": "true"
      }, _attrs))} data-v-22d49c09></div>`);
    };
  }
};
const _sfc_setup = _sfc_main.setup;
_sfc_main.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Components/User/Image/CustomImageAuthentication.vue");
  return _sfc_setup ? _sfc_setup(props, ctx) : void 0;
};
const CustomImageAuthentication = /* @__PURE__ */ _export_sfc(_sfc_main, [["__scopeId", "data-v-22d49c09"]]);
export {
  CustomImageAuthentication as C
};
