import { ref, watch, computed, mergeProps, unref, useSSRContext } from "vue";
import { ssrRenderAttrs, ssrRenderComponent, ssrRenderAttr, ssrInterpolate } from "vue/server-renderer";
import { useI18n } from "vue-i18n";
import { _ as _sfc_main$1 } from "./LabelInput-C61CQHdu.js";
import { _ as _sfc_main$2 } from "./InputError-CLVdJ1nk.js";
import { _ as _export_sfc } from "./_plugin-vue_export-helper-1tPrXgE0.js";
const _sfc_main = {
  __name: "SvgIconField",
  __ssrInlineRender: true,
  props: {
    modelValue: { type: [String, null], default: "" },
    label: { type: String, default: "" },
    error: { type: String, default: "" },
    rows: { type: Number, default: 7 }
  },
  emits: ["update:modelValue"],
  setup(__props, { emit: __emit }) {
    const { t } = useI18n();
    const props = __props;
    const emit = __emit;
    const localValue = ref("");
    watch(
      () => props.modelValue,
      (value) => {
        localValue.value = value || "";
      },
      { immediate: true }
    );
    watch(localValue, (value) => {
      emit("update:modelValue", value);
    });
    const previewHtml = computed(() => localValue.value.trim());
    const hasSvg = computed(() => {
      return previewHtml.value.includes("<svg") && previewHtml.value.includes("</svg>");
    });
    return (_ctx, _push, _parent, _attrs) => {
      _push(`<div${ssrRenderAttrs(mergeProps({ class: "mb-3 flex flex-col items-start w-full" }, _attrs))} data-v-a451f0a8>`);
      _push(ssrRenderComponent(_sfc_main$1, {
        for: "icon",
        value: __props.label || unref(t)("svg")
      }, null, _parent));
      _push(`<div class="grid grid-cols-1 lg:grid-cols-4 gap-3 w-full" data-v-a451f0a8><div class="lg:col-span-3" data-v-a451f0a8><textarea id="icon"${ssrRenderAttr("rows", __props.rows)} class="w-full px-3 py-2 bg-white dark:bg-slate-900 text-slate-700 dark:text-slate-100 border border-slate-400 dark:border-slate-600 rounded-sm shadow-sm font-mono text-xs" placeholder="&lt;svg ...&gt;...&lt;/svg&gt;" data-v-a451f0a8>${ssrInterpolate(localValue.value)}</textarea>`);
      _push(ssrRenderComponent(_sfc_main$2, {
        class: "mt-2",
        message: __props.error
      }, null, _parent));
      _push(`</div><div class="flex flex-col items-center justify-center min-h-36 border border-dashed border-slate-400 dark:border-slate-500 rounded-sm bg-white dark:bg-slate-900 p-3" data-v-a451f0a8><div class="text-xs font-semibold text-slate-600 dark:text-slate-300 mb-2" data-v-a451f0a8>${ssrInterpolate(unref(t)("view"))}</div>`);
      if (hasSvg.value) {
        _push(`<div class="svg-icon-preview flex items-center justify-center w-20 h-20 text-slate-700 dark:text-slate-100" data-v-a451f0a8>${previewHtml.value ?? ""}</div>`);
      } else {
        _push(`<div class="flex items-center justify-center w-20 h-20 rounded-sm border border-slate-300 dark:border-slate-600 text-xs text-slate-400 dark:text-slate-500" data-v-a451f0a8> SVG </div>`);
      }
      _push(`</div></div></div>`);
    };
  }
};
const _sfc_setup = _sfc_main.setup;
_sfc_main.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Components/Admin/UI/Icon/SvgIconField.vue");
  return _sfc_setup ? _sfc_setup(props, ctx) : void 0;
};
const SvgIconField = /* @__PURE__ */ _export_sfc(_sfc_main, [["__scopeId", "data-v-a451f0a8"]]);
export {
  SvgIconField as S
};
