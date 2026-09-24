import { ref, onMounted, mergeProps, useSSRContext } from "vue";
import { ssrRenderAttrs } from "vue/server-renderer";
const _sfc_main = {
  __name: "InputProgress",
  __ssrInlineRender: true,
  props: {
    modelValue: {
      type: [Number, String],
      required: true,
      default: 0
    }
  },
  emits: ["update:modelValue"],
  setup(__props, { emit: __emit }) {
    const input = ref(null);
    onMounted(() => {
      var _a;
      if ((_a = input.value) == null ? void 0 : _a.hasAttribute("autofocus")) {
        input.value.focus();
      }
    });
    return (_ctx, _push, _parent, _attrs) => {
      _push(`<input${ssrRenderAttrs(mergeProps({
        class: "w-20 py-0.5 border-slate-500 font-semibold text-sm focus:border-indigo-500 focus:ring-indigo-300 rounded-sm shadow-sm dark:bg-cyan-800 dark:text-slate-100",
        value: __props.modelValue,
        ref_key: "input",
        ref: input,
        type: "number",
        min: "0",
        max: "100",
        maxlength: "3"
      }, _attrs))}>`);
    };
  }
};
const _sfc_setup = _sfc_main.setup;
_sfc_main.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Components/Admin/UI/Input/InputProgress.vue");
  return _sfc_setup ? _sfc_setup(props, ctx) : void 0;
};
export {
  _sfc_main as _
};
