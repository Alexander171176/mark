import { useAttrs, ref, watch, mergeProps, unref, useSSRContext } from "vue";
import { ssrRenderAttrs, ssrInterpolate } from "vue/server-renderer";
const _sfc_main = /* @__PURE__ */ Object.assign({
  inheritAttrs: false
}, {
  __name: "MarketProductMeasureInput",
  __ssrInlineRender: true,
  props: {
    modelValue: {
      type: [Number, String, null],
      default: ""
    },
    min: {
      type: [Number, String],
      default: 0
    },
    max: {
      type: [Number, String, null],
      default: null
    },
    fractionDigits: {
      type: Number,
      default: 2
    },
    unit: {
      type: String,
      default: ""
    },
    placeholder: {
      type: String,
      default: "0"
    },
    disabled: {
      type: Boolean,
      default: false
    }
  },
  emits: ["update:modelValue", "blur", "focus"],
  setup(__props, { emit: __emit }) {
    const attrs = useAttrs();
    const props = __props;
    const inputValue = ref("");
    const normalizeIncomingValue = (value) => {
      if (value === null || value === void 0 || value === "") return "";
      return String(value).replace(",", ".");
    };
    watch(() => props.modelValue, (value) => {
      const normalized = normalizeIncomingValue(value);
      if (normalized !== inputValue.value.replace(",", ".")) {
        inputValue.value = normalized;
      }
    }, { immediate: true });
    return (_ctx, _push, _parent, _attrs) => {
      _push(`<div${ssrRenderAttrs(mergeProps({ class: "relative inline-flex w-full items-center" }, _attrs))}><input${ssrRenderAttrs(mergeProps(unref(attrs), {
        type: "text",
        inputmode: "decimal",
        value: inputValue.value,
        placeholder: __props.placeholder,
        disabled: __props.disabled,
        class: [
          "w-full px-3 py-0.5 rounded-sm bg-white dark:bg-cyan-800 border border-slate-500 dark:border-slate-400 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 text-slate-700 dark:text-slate-100 font-semibold disabled:cursor-not-allowed disabled:opacity-60 shadow-sm outline-none transition",
          __props.unit ? "pr-12" : ""
        ]
      }))}>`);
      if (__props.unit) {
        _push(`<span class="pointer-events-none absolute right-3 font-semibold text-sm text-slate-500 dark:text-slate-300">${ssrInterpolate(__props.unit)}</span>`);
      } else {
        _push(`<!---->`);
      }
      _push(`</div>`);
    };
  }
});
const _sfc_setup = _sfc_main.setup;
_sfc_main.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Components/Admin/UI/Input/MarketProductMeasureInput.vue");
  return _sfc_setup ? _sfc_setup(props, ctx) : void 0;
};
export {
  _sfc_main as _
};
