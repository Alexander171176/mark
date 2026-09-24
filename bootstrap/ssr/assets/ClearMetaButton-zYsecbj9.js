import { ref, mergeProps, useSSRContext } from "vue";
import { ssrRenderAttrs, ssrRenderSlot } from "vue/server-renderer";
const _sfc_main = {
  __name: "ClearMetaButton",
  __ssrInlineRender: true,
  setup(__props) {
    const isPressed = ref(false);
    return (_ctx, _push, _parent, _attrs) => {
      _push(`<button${ssrRenderAttrs(mergeProps({
        type: "button",
        class: ["flex items-center btn px-2 py-0.5 bg-yellow-200 rounded-sm shadow-md text-slate-600 text-sm font-semibold transition-colors duration-300 ease-in-out hover:text-slate-700 focus:text-slate-700 hover:bg-yellow-300 focus:bg-yellow-300 focus:outline-none", { "ring-2 ring-yellow-300 ring-offset-2 ring-offset-white": isPressed.value }]
      }, _attrs))}><span>`);
      ssrRenderSlot(_ctx.$slots, "icon", {}, () => {
        _push(`<svg class="w-4 h-4 fill-current text-gray-500 shrink-0" viewBox="0 0 512 512"><path d="M256 8C119.034 8 8 119.033 8 256s111.034 248 248 248 248-111.034 248-248S392.967 8 256 8zm130.108 117.892c65.448 65.448 70 165.481 20.677 235.637L150.47 105.216c70.204-49.356 170.226-44.735 235.638 20.676zM125.892 386.108c-65.448-65.448-70-165.481-20.677-235.637L361.53 406.784c-70.203 49.356-170.226 44.736-235.638-20.676z"></path></svg>`);
      }, _push, _parent);
      _push(`</span><span class="hidden xs:block ml-1">`);
      ssrRenderSlot(_ctx.$slots, "default", {}, null, _push, _parent);
      _push(`</span></button>`);
    };
  }
};
const _sfc_setup = _sfc_main.setup;
_sfc_main.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Components/Admin/UI/Buttons/ClearMetaButton.vue");
  return _sfc_setup ? _sfc_setup(props, ctx) : void 0;
};
export {
  _sfc_main as _
};
