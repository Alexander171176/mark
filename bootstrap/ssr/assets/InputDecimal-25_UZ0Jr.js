import { computed, mergeProps, useSSRContext } from "vue";
import { ssrRenderAttrs, ssrGetDynamicModelProps } from "vue/server-renderer";
const _sfc_main = {
  __name: "InputDecimal",
  __ssrInlineRender: true,
  props: {
    modelValue: { type: [Number, String, null], default: null },
    min: { type: [Number, String], default: null },
    max: { type: [Number, String], default: null },
    step: { type: [Number, String], default: "0.01" },
    fractionDigits: { type: Number, default: 2 }
  },
  emits: ["update:modelValue"],
  setup(__props, { emit: __emit }) {
    const props = __props;
    const emit = __emit;
    const displayValue = computed({
      get() {
        if (props.modelValue === null || props.modelValue === "") return "";
        const n = Number(props.modelValue);
        return Number.isFinite(n) ? n.toFixed(props.fractionDigits) : String(props.modelValue);
      },
      set(v) {
        if (v === "" || v === null) {
          emit("update:modelValue", null);
          return;
        }
        const n = Number(v);
        emit("update:modelValue", Number.isFinite(n) ? n : v);
      }
    });
    return (_ctx, _push, _parent, _attrs) => {
      let _temp0;
      _push(`<input${ssrRenderAttrs((_temp0 = mergeProps({
        type: "number",
        class: "py-0.5 border-slate-500 font-semibold text-sm focus:border-indigo-500 focus:ring-indigo-300 rounded-sm shadow-sm dark:bg-cyan-800 dark:text-slate-100 w-full lg:w-28",
        value: displayValue.value,
        min: __props.min ?? void 0,
        max: __props.max ?? void 0,
        step: __props.step
      }, _attrs), mergeProps(_temp0, ssrGetDynamicModelProps(_temp0, displayValue.value))))}>`);
    };
  }
};
const _sfc_setup = _sfc_main.setup;
_sfc_main.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Components/Admin/UI/Input/InputDecimal.vue");
  return _sfc_setup ? _sfc_setup(props, ctx) : void 0;
};
export {
  _sfc_main as _
};
