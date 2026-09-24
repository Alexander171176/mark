import { computed, mergeProps, useSSRContext } from "vue";
import { ssrRenderAttrs, ssrRenderAttr, ssrInterpolate, ssrRenderList } from "vue/server-renderer";
import { useI18n } from "vue-i18n";
const _sfc_main$1 = {
  __name: "ThousandsSeparatorSelect",
  __ssrInlineRender: true,
  props: {
    modelValue: { type: String, default: "space" },
    // токен
    forbidden: { type: String, default: "" },
    // токен
    id: { type: String, default: "thousands_sep" },
    label: { type: String, default: "" }
  },
  emits: ["update:modelValue"],
  setup(__props, { emit: __emit }) {
    const { t } = useI18n();
    const props = __props;
    const OPTIONS = computed(() => [
      { label: t("currencySpace"), value: "space" },
      { label: t("currencyNbsp"), value: "nbsp" },
      { label: t("currencyThinSpace"), value: "currency" },
      { label: t("currencyComma"), value: "comma" },
      { label: t("currencyDot"), value: "dot" },
      { label: t("currencyApostrophe"), value: "apostrophe" }
    ]);
    const options = computed(
      () => OPTIONS.value.filter((o) => o.value !== props.forbidden)
    );
    const labelText = computed(
      () => props.label || t("thousandsSeparator")
    );
    return (_ctx, _push, _parent, _attrs) => {
      _push(`<div${ssrRenderAttrs(mergeProps({ class: "flex flex-col items-start w-full" }, _attrs))}><label${ssrRenderAttr("for", __props.id)} class="mb-1 block font-medium text-sm text-indigo-600 dark:text-sky-500">${ssrInterpolate(labelText.value)}</label><select${ssrRenderAttr("id", __props.id)}${ssrRenderAttr("value", __props.modelValue)} class="w-full px-3 py-0.5 form-select rounded-sm shadow-sm bg-white dark:bg-cyan-800 dark:text-slate-100 font-semibold border border-slate-500"><!--[-->`);
      ssrRenderList(options.value, (o) => {
        _push(`<option${ssrRenderAttr("value", o.value)}>${ssrInterpolate(o.label)}</option>`);
      });
      _push(`<!--]--></select></div>`);
    };
  }
};
const _sfc_setup$1 = _sfc_main$1.setup;
_sfc_main$1.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Components/Admin/Finance/Currency/Select/ThousandsSeparatorSelect.vue");
  return _sfc_setup$1 ? _sfc_setup$1(props, ctx) : void 0;
};
const _sfc_main = {
  __name: "DecimalSeparatorSelect",
  __ssrInlineRender: true,
  props: {
    modelValue: { type: String, default: "dot" },
    // Токен
    forbidden: { type: String, default: "" },
    // Токен
    id: { type: String, default: "decimal_sep" },
    label: { type: String, default: "" }
  },
  emits: ["update:modelValue"],
  setup(__props, { emit: __emit }) {
    const { t } = useI18n();
    const props = __props;
    const OPTIONS = computed(() => [
      { label: t("currencyComma"), value: "comma" },
      { label: t("currencyDot"), value: "dot" }
    ]);
    const options = computed(
      () => OPTIONS.value.filter((o) => o.value !== props.forbidden)
    );
    const labelText = computed(
      () => props.label || t("currencyDecimalSeparator")
    );
    return (_ctx, _push, _parent, _attrs) => {
      _push(`<div${ssrRenderAttrs(mergeProps({ class: "flex flex-col items-start w-full" }, _attrs))}><label${ssrRenderAttr("for", __props.id)} class="mb-1 block font-medium text-sm text-indigo-600 dark:text-sky-500">${ssrInterpolate(labelText.value)}</label><select${ssrRenderAttr("id", __props.id)}${ssrRenderAttr("value", __props.modelValue)} class="w-full px-3 py-0.5 form-select rounded-sm shadow-sm bg-white dark:bg-cyan-800 dark:text-slate-100 font-semibold border border-slate-500"><!--[-->`);
      ssrRenderList(options.value, (o) => {
        _push(`<option${ssrRenderAttr("value", o.value)}>${ssrInterpolate(o.label)}</option>`);
      });
      _push(`<!--]--></select></div>`);
    };
  }
};
const _sfc_setup = _sfc_main.setup;
_sfc_main.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Components/Admin/Finance/Currency/Select/DecimalSeparatorSelect.vue");
  return _sfc_setup ? _sfc_setup(props, ctx) : void 0;
};
export {
  _sfc_main$1 as _,
  _sfc_main as a
};
