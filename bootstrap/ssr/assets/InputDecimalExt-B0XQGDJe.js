import { ref, computed, onMounted, mergeProps, useSSRContext } from "vue";
import { ssrRenderAttrs } from "vue/server-renderer";
const _sfc_main = {
  __name: "InputDecimalExt",
  __ssrInlineRender: true,
  props: {
    modelValue: {
      type: [Number, String, null],
      default: null
    },
    min: {
      type: Number,
      default: 0
    },
    max: {
      type: Number,
      default: 5
    },
    step: {
      type: Number,
      default: 0.01
    },
    fractionDigits: {
      type: Number,
      default: 2
    }
  },
  emits: ["update:modelValue"],
  setup(__props, { emit: __emit }) {
    const props = __props;
    const input = ref(null);
    const displayValue = computed(() => {
      if (props.modelValue === null || props.modelValue === void 0 || props.modelValue === "") {
        return "";
      }
      return props.modelValue.toString();
    });
    onMounted(() => {
      if (input.value && input.value.hasAttribute("autofocus")) {
        input.value.focus();
      }
    });
    return (_ctx, _push, _parent, _attrs) => {
      _push(`<input${ssrRenderAttrs(mergeProps({
        ref_key: "input",
        ref: input,
        type: "number",
        inputmode: "decimal",
        step: __props.step,
        min: __props.min,
        max: __props.max,
        value: displayValue.value
      }, _ctx.$attrs, { class: "w-20 py-0.5 border-slate-500 font-semibold text-sm focus:border-indigo-500 focus:ring-indigo-300 rounded-sm shadow-sm dark:bg-cyan-800 dark:text-slate-100" }, _attrs))}>`);
    };
  }
};
const _sfc_setup = _sfc_main.setup;
_sfc_main.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Components/Admin/UI/Input/InputDecimalExt.vue");
  return _sfc_setup ? _sfc_setup(props, ctx) : void 0;
};
export {
  _sfc_main as _
};
