import { useAttrs, ref, computed, watch, mergeProps, unref, useSSRContext } from "vue";
import { ssrRenderAttrs, ssrInterpolate } from "vue/server-renderer";
const _sfc_main = /* @__PURE__ */ Object.assign({
  inheritAttrs: false
}, {
  __name: "MarketProductPriceInput",
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
    currency: {
      type: Object,
      default: null
    },
    placeholder: {
      type: String,
      default: "0.00"
    },
    disabled: {
      type: Boolean,
      default: false
    },
    required: {
      type: Boolean,
      default: false
    }
  },
  emits: ["update:modelValue", "blur", "focus"],
  setup(__props, { emit: __emit }) {
    const attrs = useAttrs();
    const props = __props;
    const inputValue = ref("");
    computed(() => {
      var _a;
      return ((_a = props.currency) == null ? void 0 : _a.decimal_sep) || ".";
    });
    const currencySymbol = computed(() => {
      var _a, _b;
      return ((_a = props.currency) == null ? void 0 : _a.symbol) || ((_b = props.currency) == null ? void 0 : _b.code) || "";
    });
    const symbolFirst = computed(() => {
      var _a;
      return Boolean((_a = props.currency) == null ? void 0 : _a.symbol_first);
    });
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
      _push(`<div${ssrRenderAttrs(mergeProps({ class: "relative inline-flex w-full items-center" }, _attrs))}>`);
      if (currencySymbol.value && symbolFirst.value) {
        _push(`<span class="pointer-events-none absolute left-3 text-sm text-slate-500 dark:text-slate-300">${ssrInterpolate(currencySymbol.value)}</span>`);
      } else {
        _push(`<!---->`);
      }
      _push(`<input${ssrRenderAttrs(mergeProps(unref(attrs), {
        type: "text",
        inputmode: "decimal",
        value: inputValue.value,
        placeholder: __props.placeholder,
        disabled: __props.disabled,
        required: __props.required,
        class: [
          "w-full px-3 py-0.5 rounded-sm bg-white dark:bg-cyan-800 border border-slate-500 dark:border-slate-400 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 text-slate-700 dark:text-slate-100 font-semibold disabled:cursor-not-allowed disabled:opacity-60 shadow-sm outline-none transition",
          currencySymbol.value && symbolFirst.value ? "pl-9" : "",
          currencySymbol.value && !symbolFirst.value ? "pr-12" : ""
        ]
      }))}>`);
      if (currencySymbol.value && !symbolFirst.value) {
        _push(`<span class="pointer-events-none absolute right-3 font-semibold text-sm text-slate-500 dark:text-slate-300">${ssrInterpolate(currencySymbol.value)}</span>`);
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
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Components/Admin/UI/Input/MarketProductPriceInput.vue");
  return _sfc_setup ? _sfc_setup(props, ctx) : void 0;
};
export {
  _sfc_main as _
};
